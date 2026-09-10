<?php

namespace App\Http\Controllers;

use App\Models\ContentItem;
use App\Models\ContentType;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\Page;
use App\Models\Redirect;
use App\Models\Term;
use App\Support\Locales;
use App\Support\Urls;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

/**
 * Ön yüzün tek giriş kapısı.
 *
 * Adresler rota dosyasında tek tek tanımlanmaz; müşteri panelden yeni bir
 * içerik türü açtığında ya da bir sayfanın adresini değiştirdiğinde kod
 * dokunulmadan çalışmaya devam eder. Sıra şudur: yönlendirme tablosu →
 * ana sayfa → sayfalar → içerik arşivleri → kategori arşivleri → içerik
 * detayları.
 */
class FrontController extends Controller
{
    public function resolve(Request $request, ?string $path = null)
    {
        $segments = array_values(array_filter(explode('/', trim((string) $path, '/')), 'strlen'));

        // Çok dilli sitede ilk parça dil koduysa ayrılır; değilse varsayılan
        // dile düşülür. Tek dilli sitede ön ek hiç aranmaz.
        $locale = Locales::default();

        if (Locales::isMultilingual() && isset($segments[0]) && Locales::exists($segments[0])) {
            $locale = array_shift($segments);
        } elseif (Locales::isMultilingual() && $segments !== []) {
            // Dil öneki olmayan adresler varsayılan dile kalıcı olarak yönlendirilir;
            // aynı içerik iki farklı adreste indekslenmez.
            return redirect('/'.$locale.'/'.implode('/', $segments), 301);
        }

        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);

        if ($response = $this->checkRedirect($request)) {
            return $response;
        }

        if ($segments === []) {
            return $this->home($locale);
        }

        if ($page = Urls::findPageByPath($segments, $locale)) {
            return $this->page($page, $locale);
        }

        if ($response = $this->contentRoute($segments, $locale)) {
            return $response;
        }

        abort(404);
    }

    // ── Sayfalar ─────────────────────────────────────────────────────────

    private function home(string $locale)
    {
        $page = Page::withTranslations()->live()->where('is_home', true)->first();

        abort_if($page === null, 404);

        return $this->page($page, $locale);
    }

    private function page(Page $page, string $locale)
    {
        // Yayında olmayan sayfayı yalnızca panele giriş yapmış biri görebilir
        // (önizleme için).
        if ($page->status !== 'published' && ! auth()->check()) {
            abort(404);
        }

        $translation = $page->translation($locale);

        abort_if($translation === null, 404);

        $page->timestamps = false;
        $page->increment('view_count');

        return view('front.page', [
            'page' => $page,
            'translation' => $translation,
            'locale' => $locale,
            'subject' => $page,
        ]);
    }

    // ── İçerik ───────────────────────────────────────────────────────────

    /** Arşiv, kategori ve detay adreslerini dener. */
    private function contentRoute(array $segments, string $locale)
    {
        $type = ContentType::where('is_active', true)->get()
            ->first(fn (ContentType $t) => $t->prefix($locale) === $segments[0]);

        if (! $type) {
            return null;
        }

        // /urunler
        if (count($segments) === 1) {
            return $type->has_archive ? $this->archive($type, $locale) : null;
        }

        // /urunler/kategori/mermer
        if (count($segments) === 3) {
            $taxonomy = $type->taxonomies->first(
                fn ($t) => ($t->route_prefix[$locale] ?? $t->key) === $segments[1]
            );

            if ($taxonomy) {
                $term = Term::withTranslations()->with('taxonomy')
                    ->where('taxonomy_id', $taxonomy->id)
                    ->whereHas('translations', fn ($q) => $q->where('locale', $locale)->where('slug', $segments[2]))
                    ->first();

                if ($term) {
                    return $this->archive($type, $locale, $term);
                }
            }
        }

        // /urunler/bir-urun
        $item = ContentItem::withTranslations()
            ->with('type', 'featuredImage', 'terms.translations')
            ->where('content_type_id', $type->id)
            ->whereHas('translations', fn ($q) => $q->where('locale', $locale)->where('slug', end($segments)))
            ->first();

        if ($item && ($item->status === 'published' || auth()->check())) {
            $item->timestamps = false;
            $item->increment('view_count');

            return view('front.content', [
                'item' => $item,
                'translation' => $item->translation($locale),
                'type' => $type,
                'locale' => $locale,
                'subject' => $item,
            ]);
        }

        return null;
    }

    private function archive(ContentType $type, string $locale, ?Term $term = null)
    {
        $items = ContentItem::withTranslations()
            ->with('featuredImage', 'type', 'terms.translations')
            ->where('content_type_id', $type->id)
            ->live()
            ->when($term, fn ($q) => $q->whereHas('terms', fn ($t) => $t->where('terms.id', $term->id)))
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('front.archive', [
            'type' => $type,
            'term' => $term,
            'items' => $items,
            'locale' => $locale,
            'subject' => null,
        ]);
    }

    // ── Yönlendirmeler ───────────────────────────────────────────────────

    /** Eski adresleri kurtarır; arama motorlarındaki sıralama korunur. */
    private function checkRedirect(Request $request)
    {
        $from = '/'.trim($request->path(), '/');

        $redirect = Redirect::where('is_active', true)
            ->where(fn ($q) => $q->where('from', $from)->orWhere('from', trim($from, '/')))
            ->first();

        if (! $redirect) {
            return null;
        }

        $redirect->timestamps = false;
        $redirect->increment('hits');
        $redirect->update(['last_hit_at' => now()]);

        return redirect($redirect->to, $redirect->status_code);
    }

    // ── Form gönderimi ───────────────────────────────────────────────────

    public function submitForm(Request $request, Form $form)
    {
        $this->guardAgainstSpam($request, $form);

        [$rules, $labels] = $this->formRules($form, $request->input('locale', Locales::default()));

        $validator = validator($request->all(), $rules, [], $labels);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray())
                ->errorBag('form-'.$form->key);
        }

        $data = $validator->validated();

        // Yüklenen dosyalar medya kütüphanesine, doğrulamadan geçerek girer.
        foreach ($form->fields as $field) {
            if (($field['type'] ?? null) === 'file' && $request->hasFile($field['key'])) {
                try {
                    $media = app(\App\Services\MediaUploader::class)->store($request->file($field['key']));
                    $data[$field['key']] = $media->url();
                } catch (\Throwable) {
                    unset($data[$field['key']]);
                }
            }
        }

        $submission = null;

        if ($form->store_submissions) {
            $submission = FormSubmission::create([
                'form_id' => $form->id,
                'data' => $data,
                'page_url' => $request->input('page_url'),
                'ip_address' => $request->ip(),
                'user_agent' => str($request->userAgent())->limit(500)->toString(),
                'locale' => $request->input('locale'),
            ]);
        }

        $this->notify($form, $data, $submission);

        if ($form->redirect_url) {
            return redirect($form->redirect_url);
        }

        return back()
            ->with('form_success', $form->key)
            ->withFragment('form-'.$form->key);
    }

    /** @return array{0: array, 1: array} */
    private function formRules(Form $form, string $locale): array
    {
        $rules = ['locale' => 'nullable|string|max:8', 'page_url' => 'nullable|string|max:500'];
        $labels = [];

        foreach ($form->fields as $field) {
            $key = $field['key'];
            $required = ! empty($field['required']) ? 'required' : 'nullable';

            $rules[$key] = match ($field['type']) {
                'email' => "{$required}|email|max:190",
                'tel' => "{$required}|string|max:40",
                'number' => "{$required}|numeric",
                'checkbox' => ! empty($field['required']) ? 'accepted' : 'nullable',
                'textarea' => "{$required}|string|max:5000",
                'file' => "{$required}|file|max:".(config('cms.media.max_upload_mb') * 1024),
                default => "{$required}|string|max:255",
            };

            $labels[$key] = $field['label'][$locale] ?? $field['label'][Locales::default()] ?? $key;
        }

        return [$rules, $labels];
    }

    /**
     * Otomatik gönderimleri eler: gizli alan doldurulmuşsa ya da form
     * insanın dolduramayacağı kadar hızlı gönderilmişse istek reddedilir.
     */
    private function guardAgainstSpam(Request $request, Form $form): void
    {
        if (! $form->spam_protection) {
            return;
        }

        if (filled($request->input('website'))) {
            abort(422);
        }

        $started = (int) $request->input('_ts', 0);

        if ($started > 0 && (time() - $started) < 3) {
            abort(422);
        }
    }

    private function notify(Form $form, array $data, ?FormSubmission $submission): void
    {
        $recipients = $form->notifyList();

        if (! $recipients) {
            return;
        }

        // E-posta gönderilemezse kayıt zaten veritabanında durur; müşteri
        // mesajı panelden görür. Bu yüzden hata sayfayı çökertmez.
        try {
            Mail::send('mail.submission', [
                'form' => $form,
                'data' => $data,
                'submission' => $submission,
            ], function ($message) use ($recipients, $form, $data) {
                $message->to($recipients)
                    ->subject('Yeni form mesajı: '.$form->name);

                // Yanıtla düğmesi doğrudan gönderene gitsin.
                foreach ($data as $value) {
                    if (is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $message->replyTo($value);
                        break;
                    }
                }
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }

    // ── Arama motoru dosyaları ───────────────────────────────────────────

    public function sitemap()
    {
        if (\App\Models\Setting::get('seo.noindex_site')) {
            abort(404);
        }

        $urls = [];

        foreach (Page::withTranslations()->live()->where('show_in_search', true)->get() as $page) {
            foreach (Locales::codes() as $code) {
                if (! $page->hasTranslation($code) || $page->translation($code)->noindex) {
                    continue;
                }

                $urls[] = [
                    'loc' => url(Urls::prefix($code).rtrim($page->path($code), '/')),
                    'lastmod' => $page->updated_at?->toAtomString(),
                    'priority' => $page->is_home ? '1.0' : '0.8',
                ];
            }
        }

        foreach (ContentItem::withTranslations()->with('type')->live()->get() as $item) {
            foreach (Locales::codes() as $code) {
                if (! $item->hasTranslation($code)) {
                    continue;
                }

                $urls[] = [
                    'loc' => url(Urls::prefix($code).$item->path($code)),
                    'lastmod' => $item->updated_at?->toAtomString(),
                    'priority' => '0.6',
                ];
            }
        }

        return response()
            ->view('front.sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $lines = \App\Models\Setting::get('seo.noindex_site')
            ? ['User-agent: *', 'Disallow: /']
            : [
                'User-agent: *',
                'Disallow: /'.config('cms.admin_path'),
                'Disallow: /storage/media/*/tmp',
                '',
                'Sitemap: '.url('/sitemap.xml'),
            ];

        return response(implode("\n", $lines), 200)->header('Content-Type', 'text/plain');
    }
}
