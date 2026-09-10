<?php

namespace App\Livewire\Admin\Settings;

use App\Models\ActivityLog;
use App\Models\LoginAttempt;
use App\Models\User;
use App\Services\LoginGuard;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Güvenlik durumu ve kayıtlar.
 *
 * Amaç müşteriyi teknik ayrıntıyla korkutmak değil, sitenin korunduğunu
 * görünür kılmak ve gerçekten yapılması gereken bir şey varsa onu
 * sade bir cümleyle söylemek.
 */
#[Layout('layouts.admin')]
class Security extends Component
{
    use WithPagination;

    #[Url(as: 'sekme')]
    public string $tab = 'overview';

    public function render()
    {
        return view('livewire.admin.settings.security', [
            'checks' => $this->checks(),
            'failedAttempts' => LoginAttempt::where('successful', false)->latest('created_at')->paginate(25),
            'activity' => ActivityLog::with('user')->latest('created_at')->paginate(30),
            'recentFailures' => LoginGuard::recentFailures(24),
        ])->title('Güvenlik');
    }

    /** @return array<int, array{title: string, ok: bool, text: string, action?: array}> */
    private function checks(): array
    {
        $withoutTwoFactor = User::whereNull('two_factor_confirmed_at')->where('is_active', true)->count();
        $adminPath = config('cms.admin_path');

        return [
            [
                'title' => 'Güvenli bağlantı (HTTPS)',
                'ok' => request()->secure() || app()->environment('local'),
                'text' => request()->secure() || app()->environment('local')
                    ? 'Site şifreli bağlantı üzerinden çalışıyor.'
                    : 'Site şifresiz bağlantı kullanıyor. Hosting panelinizden ücretsiz SSL sertifikasını etkinleştirin — girilen şifreler aksi hâlde ağ üzerinde açık gider.',
            ],
            [
                'title' => 'İki adımlı doğrulama',
                'ok' => $withoutTwoFactor === 0,
                'text' => $withoutTwoFactor === 0
                    ? 'Tüm etkin hesaplarda iki adımlı doğrulama açık.'
                    : "{$withoutTwoFactor} hesapta iki adımlı doğrulama kapalı. Şifresi ele geçen bir hesap panele girebilir.",
                'action' => ['label' => 'Kullanıcılar', 'href' => route('admin.users.index')],
            ],
            [
                'title' => 'Panel adresi',
                'ok' => $adminPath !== 'panel' && $adminPath !== 'admin',
                'text' => $adminPath !== 'panel' && $adminPath !== 'admin'
                    ? 'Panel tahmin edilmesi güç bir adreste. Otomatik tarayan botlar buraya ulaşamaz.'
                    : 'Panel varsayılan adreste (/'.$adminPath.'). Sunucudaki .env dosyasında CMS_ADMIN_PATH değerini değiştirmek, saldırıların büyük kısmını kapıya bile getirmez.',
            ],
            [
                'title' => 'Kaba kuvvet koruması',
                'ok' => true,
                'text' => 'Art arda '.config('cms.security.max_login_attempts').' hatalı denemeden sonra hesap ve IP '
                    .config('cms.security.lockout_minutes').' dakika kilitleniyor.',
            ],
            [
                'title' => 'Dosya yükleme denetimi',
                'ok' => true,
                'text' => 'Yüklenen her dosyanın uzantısı, MIME tipi ve gerçek içeriği ayrı ayrı doğrulanıyor; '
                    .'görseller yeniden kodlanıyor. Sunucuda çalıştırılabilir dosya oluşamaz.',
            ],
            [
                'title' => 'Eklenti kurulumu',
                'ok' => true,
                'text' => 'Panelden çalışma anında kod yüklenemez. WordPress sitelerinin büyük çoğunluğu '
                    .'güncellenmemiş bir eklenti üzerinden ele geçirilir; bu sistemde böyle bir yüzey yok.',
            ],
        ];
    }
}
