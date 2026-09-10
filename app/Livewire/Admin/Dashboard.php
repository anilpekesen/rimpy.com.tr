<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\ContentItem;
use App\Models\ContentType;
use App\Models\FormSubmission;
use App\Models\Media;
use App\Models\Page;
use App\Services\LoginGuard;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'stats' => $this->stats(),
            'recentSubmissions' => FormSubmission::with('form')
                ->where('is_spam', false)->latest()->limit(5)->get(),
            'recentActivity' => ActivityLog::with('user')->latest('created_at')->limit(8)->get(),
            'drafts' => Page::withTranslations()->where('status', 'draft')->latest('updated_at')->limit(5)->get(),
            'warnings' => $this->warnings(),
        ])->title('Panel');
    }

    /** Panelin üstündeki özet kutuları. */
    private function stats(): array
    {
        $cards = [
            [
                'label' => 'Yayındaki sayfa',
                'value' => Page::live()->count(),
                'icon' => 'file-text',
                'href' => route('admin.pages.index'),
            ],
        ];

        foreach (ContentType::where('is_active', true)->orderBy('sort')->limit(2)->get() as $type) {
            $cards[] = [
                'label' => $type->pluralLabel('tr'),
                'value' => ContentItem::where('content_type_id', $type->id)->live()->count(),
                'icon' => $type->icon,
                'href' => route('admin.content.index', $type->key),
            ];
        }

        $cards[] = [
            'label' => 'Okunmamış mesaj',
            'value' => FormSubmission::where('is_read', false)->where('is_spam', false)->count(),
            'icon' => 'inbox',
            'href' => route('admin.submissions.index'),
        ];

        $cards[] = [
            'label' => 'Medya dosyası',
            'value' => Media::count(),
            'icon' => 'image',
            'href' => route('admin.media.index'),
        ];

        return $cards;
    }

    /**
     * Müşteriyi teknik ayrıntıya boğmadan, gerçekten harekete geçmesi
     * gereken durumları gösterir.
     */
    private function warnings(): array
    {
        $warnings = [];

        if (! Page::where('is_home', true)->exists()) {
            $warnings[] = [
                'text' => 'Ana sayfa henüz belirlenmemiş. Ziyaretçiler siteye girdiğinde boş bir sayfa görür.',
                'action' => ['label' => 'Sayfalara git', 'href' => route('admin.pages.index')],
            ];
        }

        if (blank(\App\Models\Setting::get('general.site_name'))) {
            $warnings[] = [
                'text' => 'Site adı girilmemiş. Arama sonuçlarında ve tarayıcı sekmesinde bu ad görünür.',
                'action' => ['label' => 'Genel ayarlar', 'href' => route('admin.settings.general')],
            ];
        }

        if (! auth()->user()->hasTwoFactor()) {
            $warnings[] = [
                'text' => 'Hesabınızda iki adımlı doğrulama kapalı. Şifreniz ele geçse bile hesabınızı korur.',
                'action' => ['label' => 'Şimdi aç', 'href' => route('admin.account')],
            ];
        }

        if (($failures = LoginGuard::recentFailures(24)) >= 10) {
            $warnings[] = [
                'text' => "Son 24 saatte {$failures} başarısız giriş denemesi yapıldı. Bu genellikle otomatik bir tarama demektir; sisteminiz denemeleri engelliyor.",
                'action' => ['label' => 'Güvenlik kayıtları', 'href' => route('admin.settings.security')],
                'level' => 'info',
            ];
        }

        return $warnings;
    }
}
