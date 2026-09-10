<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\Locales;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

/**
 * Yeni bir siteyi baştan sona kurar.
 *
 * Aynı kod tabanı birden çok müşteride çalıştığı için kurulum tek komuta
 * indirgenmiştir: veritabanı hazırlanır, ilk yönetici oluşturulur ve panelin
 * adresi tahmin edilemez bir değere ayarlanır.
 */
class InstallCommand extends Command
{
    protected $signature = 'cms:install {--force : Var olan verinin üzerine yaz}';

    protected $description = 'Yeni bir site kurulumunu tamamlar';

    public function handle(): int
    {
        $this->components->info('Mimar CMS kurulumu');

        if (! $this->checkDatabase()) {
            return self::FAILURE;
        }

        $installed = DB::table('migrations')->exists() && DB::getSchemaBuilder()->hasTable('users');

        if ($installed && ! $this->option('force')) {
            if (! confirm('Bu veritabanında zaten bir kurulum var. Devam edilsin mi?', default: false)) {
                return self::SUCCESS;
            }
        }

        $this->components->task('Veritabanı tabloları oluşturuluyor', function () {
            Artisan::call('migrate', ['--force' => true]);

            return true;
        });

        $this->components->task('Başlangıç verileri yazılıyor', function () {
            Artisan::call('db:seed', ['--force' => true]);

            return true;
        });

        $this->components->task('Medya klasörü bağlanıyor', function () {
            Artisan::call('storage:link');

            return true;
        });

        $this->createOwner();
        $this->suggestAdminPath();

        $this->newLine();
        $this->components->info('Kurulum tamamlandı.');
        $this->components->bulletList([
            'Panel adresi: '.rtrim(config('app.url'), '/').'/'.config('cms.admin_path'),
            'Diller: '.implode(', ', Locales::codes()),
        ]);

        return self::SUCCESS;
    }

    private function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();

            return true;
        } catch (\Throwable $e) {
            $this->components->error('Veritabanına bağlanılamadı.');
            $this->line('  <fg=gray>'.$e->getMessage().'</>');
            $this->newLine();
            $this->line('  <fg=yellow>.env dosyasındaki DB_DATABASE, DB_USERNAME ve DB_PASSWORD değerlerini kontrol edin.</>');
            $this->line('  <fg=yellow>Veritabanının hosting panelinden önceden oluşturulmuş olması gerekir.</>');

            return false;
        }
    }

    private function createOwner(): void
    {
        $this->newLine();
        $this->components->info('Yönetici hesabı');

        $existing = User::where('role', 'owner')->first();

        if ($existing && ! confirm("Zaten bir yönetici var ({$existing->email}). Yenisi eklensin mi?", default: false)) {
            $this->components->warn('Varsayılan şifreyi ilk girişte mutlaka değiştirin.');

            return;
        }

        $name = text('Ad soyad', required: true, default: 'Site Sahibi');

        $email = text(
            label: 'E-posta adresi',
            required: true,
            validate: fn ($value) => match (true) {
                ! filter_var($value, FILTER_VALIDATE_EMAIL) => 'Geçerli bir e-posta adresi girin.',
                User::where('email', $value)->exists() => 'Bu e-posta zaten kayıtlı.',
                default => null,
            },
        );

        $min = (int) config('cms.security.min_password_length', 10);

        $secret = password(
            label: "Şifre (en az {$min} karakter, harf ve rakam içermeli)",
            required: true,
            validate: fn ($value) => match (true) {
                strlen($value) < $min => "Şifre en az {$min} karakter olmalı.",
                ! preg_match('/[a-zA-Z]/', $value) || ! preg_match('/\d/', $value) => 'Şifre hem harf hem rakam içermeli.',
                default => null,
            },
        );

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($secret),
            'role' => 'owner',
            'is_active' => true,
            'password_changed_at' => now(),
        ]);

        // Tohumlamanın oluşturduğu varsayılan hesap artık gereksiz.
        User::where('role', 'owner')
            ->where('id', '!=', User::where('email', $email)->value('id'))
            ->where('password_changed_at', null)
            ->delete();

        $this->components->info("Yönetici hesabı oluşturuldu: {$email}");
    }

    /**
     * Panel adresi hâlâ varsayılansa rastgele bir öneri sunar. Tahmin
     * edilemez bir adres, panel arayan botları tamamen dışarıda bırakır.
     */
    private function suggestAdminPath(): void
    {
        if (config('cms.admin_path') !== 'panel') {
            return;
        }

        $this->newLine();
        $suggestion = 'panel-'.Str::lower(Str::random(6));

        $this->components->warn('Panel adresi varsayılan değerde: /panel');
        $this->line("  <fg=gray>Güvenlik için .env dosyasına şunu ekleyin:</>");
        $this->line("  <fg=green>CMS_ADMIN_PATH={$suggestion}</>");
    }
}
