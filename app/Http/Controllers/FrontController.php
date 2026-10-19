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

        if (count($segments) === 1 && ($response = $this->flatRoute($segments[0], $locale))) {
            return $response;
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

    /**
     * Kısa adres: /tr/hidrolik-krikolar ya da /tr/rht-08-kare-... Önce
     * kategori, sonra kayıt denenir; yalnızca cms.flat_urls'teki türler.
     */
    private function flatRoute(string $slug, string $locale)
    {
        $types = ContentType::with('taxonomies')->where('is_active', true)
            ->whereIn('key', config('cms.flat_urls', []))->get();

        foreach ($types as $type) {
            $term = Term::withTranslations()->with('taxonomy')
                ->whereIn('taxonomy_id', $type->taxonomies->pluck('id'))
                ->whereHas('translations', fn ($q) => $q->where('locale', $locale)->where('slug', $slug))
                ->first();

            if ($term) {
                return $this->archive($type, $locale, $term);
            }

            if ($item = $this->findItem($type, $slug, $locale)) {
                return $this->showItem($item, $type, $locale);
            }
        }

        return null;
    }

    private function findItem(ContentType $type, string $slug, string $locale): ?ContentItem
    {
        return ContentItem::withTranslations()
            ->with('type.taxonomies', 'featuredImage', 'terms.translations', 'terms.taxonomy')
            ->where('content_type_id', $type->id)
            ->whereHas('translations', fn ($q) => $q->where('locale', $locale)->where('slug', $slug))
            ->first();
    }

    private function showItem(ContentItem $item, ContentType $type, string $locale)
    {
        if ($item->status !== 'published' && ! auth()->check()) {
            return null;
        }

        $item->timestamps = false;
        $item->increment('view_count');

        [$previous, $next] = $this->neighbours($item, $type);

        return view($this->templateFor('content', $type), [
            'item' => $item,
            'translation' => $item->translation($locale),
            'type' => $type,
            'locale' => $locale,
            'subject' => $item,
            'previous' => $previous,
            'next' => $next,
        ]);
    }

    /** Arşiv, kategori ve detay adreslerini dener. */
    private function contentRoute(array $segments, string $locale)
    {
        // Sınıflandırmalar birlikte yüklenir: kategori adresi çözülürken
        // ($segments sayısı 3 olduğunda) hemen aşağıda okunuyorlar.
        $type = ContentType::with('taxonomies')->where('is_active', true)->get()
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
                    // Kısa adresli türde eski uzun adres kalıcı olarak taşınır.
                    return $type->hasFlatUrls()
                        ? redirect(Urls::prefix($locale).$term->path($locale), 301)
                        : $this->archive($type, $locale, $term);
                }
            }
        }

        // /urunler/bir-urun
        $item = $this->findItem($type, end($segments), $locale);

        if ($item && $type->hasFlatUrls()) {
            return redirect(Urls::prefix($locale).$item->path($locale), 301);
        }

        return $item ? $this->showItem($item, $type, $locale) : null;
    }

    /**
     * Listede bu kaydın hemen öncesindeki ve sonrasındaki kayıtlar.
     *
     * Sıralama arşivinkiyle aynı (önce yayın tarihi, eşitse kimlik); böylece
     * oklarla gezinmek listedeki sırayı izler. Katalogda çoğu ürünün yayın
     * tarihi aynı olduğu için kimlik ikinci ölçüt olarak şart.
     *
     * @return array{0: ?ContentItem, 1: ?ContentItem}
     */
    private function neighbours(ContentItem $item, ContentType $type): array
    {
        $base = fn () => ContentItem::withTranslations()
            ->with('type')
            ->where('content_type_id', $type->id)
            ->whereKeyNot($item->getKey())
            ->live();

        // Listede bir üstteki: tarihi daha yeni, eşitse kimliği daha büyük.
        $previous = $base()
            ->where(fn ($q) => $q
                ->where('published_at', '>', $item->published_at)
                ->orWhere(fn ($w) => $w->where('published_at', $item->published_at)->where('id', '>', $item->id)))
            ->orderBy('published_at')->orderBy('id')
            ->first();

        $next = $base()
            ->where(fn ($q) => $q
                ->where('published_at', '<', $item->published_at)
                ->orWhere(fn ($w) => $w->where('published_at', $item->published_at)->where('id', '<', $item->id)))
            ->orderByDesc('published_at')->orderByDesc('id')
            ->first();

        return [$previous, $next];
    }

    private function archive(ContentType $type, string $locale, ?Term $term = null)
    {
        $search = trim((string) request('q', ''));

        $items = ContentItem::withTranslations()
            ->with('featuredImage', 'type', 'terms.translations')
            ->where('content_type_id', $type->id)
            ->live()
            ->when($term, fn ($q) => $q->whereHas('terms', fn ($t) => $t->where('terms.id', $term->id)))
            ->when($search !== '', fn ($q) => $this->applySearch($q, $search, $locale))
            // Kimlik ikinci ölçüt: aynı tarihli yüzlerce kayıt varken tek
            // ölçütlü sıralama sayfalar arasında kayıt tekrarına ya da
            // atlanmasına yol açar.
            ->orderByDesc('published_at')->orderByDesc('id')
            ->paginate(12)
            // Sayfa değiştirirken arama terimi kaybolmasın.
            ->withQueryString();

        return view($this->templateFor('archive', $type), [
            'type' => $type,
            'term' => $term,
            'items' => $items,
            'locale' => $locale,
            'search' => $search,
            'subject' => null,
        ]);
    }

    /**
     * Arşiv içinde arama.
     *
     * Başlık ve kısa açıklamanın yanında kullanıcı tanımlı alanlara da bakar;
     * kataloglarda aranan şey çoğu zaman başlıkta değil, o türe özel bir alanda
     * durur (stok kodu, model, parça numarası...).
     *
     * Numaralar farklı yazımlarla aranıyor (`3128 3025 46`, `3128-3025-46`,
     * `31283025`). Bu yüzden girilen terimin bir de yalnızca harf ve
     * rakamlardan oluşan hâli denenir; kayıtlarda her iki biçim de bulunabilir.
     */
    private function applySearch($query, string $search, string $locale)
    {
        $terms = array_unique(array_filter([
            $search,
            preg_replace('/[^\p{L}\p{N}]+/u', '', $search),
        ], fn (string $t) => mb_strlen($t) >= 2));

        return $query->whereHas('translations', function ($q) use ($terms, $locale) {
            $q->where('locale', $locale)->where(function ($group) use ($terms) {
                foreach ($terms as $term) {
                    // LIKE için joker karakterler kaçırılıyor; kullanıcının
                    // yazdığı "%" tüm kayıtları getirmemeli.
                    $like = '%'.addcslashes($term, '%_\\').'%';

                    $group->orWhere('title', 'like', $like)
                        ->orWhere('excerpt', 'like', $like)
                        ->orWhere('custom', 'like', $like);
                }
            });
        });
    }

    /**
     * Türe özel şablon varsa onu seçer: `front.content-urunler` gibi. Bir
     * sitenin ürün sayfası ötekilerden farklı görünebilsin diye; yoksa
     * ortak şablon kullanılır.
     */
    private function templateFor(string $view, ContentType $type): string
    {
        $specific = "front.{$view}-{$type->key}";

        return view()->exists($specific) ? $specific : "front.{$view}";
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

        // Teklif formuysa sepetteki ürünler mesaja eklenir. Sepet boşsa
        // form yine gönderilebilir: "ne alacağımı bilmiyorum" talebi de teklif.
        $cart = app(\App\Services\QuoteCart::class);
        $isQuote = $form->key === config('cms.quote.form_key');

        if ($isQuote && ! $cart->isEmpty()) {
            $data = ['urunler' => $cart->summary($request->input('locale'))] + $data;
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

        if ($isQuote) {
            $cart->clear();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'sent',
                'message' => $form->localized('success_message', $request->input('locale')),
            ]);
        }

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
