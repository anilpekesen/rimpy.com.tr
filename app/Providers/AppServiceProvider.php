<?php

namespace App\Providers;

use App\Models\Setting;
use App\Support\Theme;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Canlıda tüm bağlantılar https üretilsin; karışık içerik uyarısı
        // ve çerez sızıntısı riski böylece ortadan kalkar.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Modeller üzerinde tanımsız özellik okunması sessizce null dönmesin;
        // geliştirme sırasında yazım hataları hemen görünür.
        Model::preventAccessingMissingAttributes(! $this->app->environment('production'));
        Model::preventLazyLoading(! $this->app->environment('production'));

        $this->app->singleton(Theme::class);

        // Şablonlarda ayarlara ve tema jetonlarına kısa yoldan erişim.
        Blade::directive('setting', fn ($expression) => "<?php echo e(\App\Models\Setting::get($expression)); ?>");
    }
}
