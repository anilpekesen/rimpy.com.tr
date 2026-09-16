<?php

use App\Http\Controllers\FrontController;
use Illuminate\Support\Facades\Route;

/*
|-------------------------------------------------------------------------------
| Yönetim paneli
|-------------------------------------------------------------------------------
| Panelin adresi .env içindeki CMS_ADMIN_PATH ile belirlenir. Her kurulumda
| farklı bir yol vermek, panel adresini tarayan otomatik botların kapıyı hiç
| bulamaması anlamına gelir.
*/

$admin = config('cms.admin_path');

Route::prefix($admin)->name('admin.')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::get('giris', App\Livewire\Auth\Login::class)->name('login');
        Route::get('sifremi-unuttum', App\Livewire\Auth\ForgotPassword::class)->name('password.request');
        Route::get('sifre-sifirla/{token}', App\Livewire\Auth\ResetPassword::class)->name('password.reset');
    });

    // İki adımlı doğrulama ekranı: kullanıcı doğrulandı ama oturum henüz tam açık değil.
    Route::get('dogrulama', App\Livewire\Auth\TwoFactorChallenge::class)
        ->middleware('guest')->name('two-factor');

    Route::post('cikis', [App\Http\Controllers\Admin\SessionController::class, 'destroy'])
        ->middleware('auth')->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', App\Livewire\Admin\Dashboard::class)->name('dashboard');

        // İçerik
        Route::get('sayfalar', App\Livewire\Admin\Pages\PageList::class)->name('pages.index');
        Route::get('sayfalar/{page}/ayarlar', App\Livewire\Admin\Pages\PageSettings::class)->name('pages.settings');
        Route::get('icerik/{type:key}', App\Livewire\Admin\Content\ItemList::class)->name('content.index');
        Route::get('icerik/{type:key}/{item}', App\Livewire\Admin\Content\ItemEdit::class)->name('content.edit');
        Route::get('icerik/{type:key}/{taxonomy:key}/terimler', App\Livewire\Admin\Content\TermList::class)->name('terms.index');

        // Kurucu — tam ekran düzenleme tuvali
        Route::get('kurucu/sayfa/{page}/{locale?}', App\Livewire\Builder\Editor::class)->name('builder.page');
        Route::get('kurucu/sablon/{template}', App\Livewire\Builder\Editor::class)->name('builder.template');
        Route::get('kurucu/icerik/{item}/{locale?}', App\Livewire\Builder\Editor::class)->name('builder.content');
        Route::get('kurucu/onizleme/{key}', App\Http\Controllers\Admin\BuilderPreviewController::class)
            ->where('key', '[a-f0-9]{40}')->name('builder.preview');

        // Tasarım
        Route::get('tasarim', App\Livewire\Admin\Design\ThemeSettings::class)->name('design.theme');
        Route::get('sablonlar', App\Livewire\Admin\Design\TemplateList::class)->name('templates.index');
        Route::get('menuler', App\Livewire\Admin\Design\MenuBuilder::class)->name('menus.index');
        Route::get('sliderlar', App\Livewire\Admin\Design\SliderManager::class)->name('sliders.index');

        // Medya
        Route::get('medya', App\Livewire\Admin\MediaLibrary::class)->name('media.index');

        // Formlar
        Route::get('formlar', App\Livewire\Admin\Forms\FormList::class)->name('forms.index');
        Route::get('formlar/{form}', App\Livewire\Admin\Forms\FormEdit::class)->name('forms.edit');
        Route::get('mesajlar', App\Livewire\Admin\Forms\Submissions::class)->name('submissions.index');

        // Ayarlar — yalnızca yönetici ve üzeri
        Route::middleware('admin:admin')->group(function () {
            Route::get('ayarlar', App\Livewire\Admin\Settings\General::class)->name('settings.general');
            Route::get('ayarlar/diller', App\Livewire\Admin\Settings\Languages::class)->name('settings.languages');
            Route::get('ayarlar/icerik-turleri', App\Livewire\Admin\Settings\ContentTypes::class)->name('settings.content-types');
            Route::get('ayarlar/seo', App\Livewire\Admin\Settings\Seo::class)->name('settings.seo');
            Route::get('ayarlar/yonlendirmeler', App\Livewire\Admin\Settings\Redirects::class)->name('settings.redirects');
            Route::get('kullanicilar', App\Livewire\Admin\Settings\Users::class)->name('users.index');
            Route::get('guvenlik', App\Livewire\Admin\Settings\Security::class)->name('settings.security');
        });

        Route::get('hesabim', App\Livewire\Admin\Account::class)->name('account');
    });
});

/*
|-------------------------------------------------------------------------------
| Ön yüz
|-------------------------------------------------------------------------------
| Tüm adresler tek bir çözümleyiciden geçer: sırasıyla yönlendirme tablosu,
| sayfalar, içerik arşivleri ve içerik detayları denenir. Böylece müşteri
| panelden yeni bir içerik türü açtığında rota dosyasına dokunmak gerekmez.
*/

Route::post('form/{form:key}', [FrontController::class, 'submitForm'])
    ->middleware('throttle:10,1')->name('form.submit');

Route::get('sitemap.xml', [FrontController::class, 'sitemap'])->name('sitemap');
Route::get('robots.txt', [FrontController::class, 'robots'])->name('robots');

// Dil öneki denetleyicinin içinde çözülür; böylece diller panelden
// eklenip çıkarıldığında rota dosyasına dokunmak gerekmez.
Route::get('{path?}', [FrontController::class, 'resolve'])
    ->where('path', '.*')
    ->name('front');
