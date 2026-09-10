<?php

namespace App\Services;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

/**
 * İki adımlı doğrulama.
 *
 * Şifreler sızabilir, tahmin edilebilir ya da müşteri onu başka bir yerde de
 * kullanmış olabilir. İkinci adım, şifreyi bilen birinin bile panele
 * girememesini sağlar — hacklenen bir WordPress ile hacklenmeyen bir site
 * arasındaki en büyük fark budur.
 */
class TwoFactor
{
    private Google2FA $engine;

    public function __construct()
    {
        $this->engine = new Google2FA;
    }

    public function generateSecret(): string
    {
        return $this->engine->generateSecretKey(32);
    }

    /** Doğrulama uygulamasının okuyacağı adres. */
    public function otpauthUrl(User $user, string $secret): string
    {
        $issuer = \App\Models\Setting::get('general.site_name', config('app.name'));

        return $this->engine->getQRCodeUrl($issuer, $user->email, $secret);
    }

    /** Kurulum ekranında gösterilecek kare kod. */
    public function qrCodeSvg(User $user, string $secret): string
    {
        $writer = new Writer(new ImageRenderer(
            new RendererStyle(200, 0),
            new SvgImageBackEnd
        ));

        return $writer->writeString($this->otpauthUrl($user, $secret));
    }

    /**
     * Girilen kodu doğrular. Saat farkını tolere etmek için bir önceki ve
     * bir sonraki zaman dilimi de kabul edilir.
     */
    public function verify(User $user, string $code): bool
    {
        $secret = $user->two_factor_secret;

        if (! $secret) {
            return false;
        }

        return (bool) $this->engine->verifyKey($secret, preg_replace('/\s+/', '', $code), 1);
    }

    /** Kurulum sırasında, gizli anahtar henüz kaydedilmemişken kullanılır. */
    public function verifySecret(string $secret, string $code): bool
    {
        return (bool) $this->engine->verifyKey($secret, preg_replace('/\s+/', '', $code), 1);
    }

    /** @return array<int, string> */
    public function generateRecoveryCodes(int $count = 8): array
    {
        return collect(range(1, $count))
            ->map(fn () => Str::lower(Str::random(5).'-'.Str::random(5)))
            ->all();
    }

    /** Kurtarma kodu tek kullanımlıktır; doğrulandığında listeden düşer. */
    public function useRecoveryCode(User $user, string $code): bool
    {
        $codes = $user->two_factor_recovery_codes ?? [];
        $code = Str::lower(trim($code));

        $index = array_search($code, array_map('strtolower', $codes), true);

        if ($index === false) {
            return false;
        }

        unset($codes[$index]);
        $user->forceFill(['two_factor_recovery_codes' => array_values($codes)])->save();

        ActivityLogger::log('two_factor.recovery_used', $user,
            'Kurtarma kodu kullanıldı. Kalan kod: '.count($codes));

        return true;
    }
}
