<?php

namespace App\Support;

/**
 * Arama motorlarının okuduğu yapısal veri (Schema.org).
 *
 * Bu yapılar Blade şablonlarında değil burada kurulur. Schema.org
 * anahtarları "@context", "@type" gibi "@" ile başlar; Blade bunları kendi
 * yönergesi sanıp şablonu bozar. Ayrıca yapıyı tek yerde tutmak, aynı
 * biçimin sayfa, içerik ve widget'lar arasında tutarlı kalmasını sağlar.
 */
class Schema
{
    /** Sıkça sorulan sorular bloğu. */
    public static function faq(array $items): array
    {
        $questions = [];

        foreach ($items as $item) {
            $question = trim(strip_tags((string) ($item['title'] ?? '')));
            $answer = trim(strip_tags((string) ($item['body'] ?? '')));

            // Google, sorusu ya da cevabı boş olan girdileri kabul etmez.
            if ($question === '' || $answer === '') {
                continue;
            }

            $questions[] = [
                '@type' => 'Question',
                'name' => $question,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $answer],
            ];
        }

        return $questions === [] ? [] : [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $questions,
        ];
    }

    /** Yol izi (breadcrumb) bloğu. */
    public static function breadcrumbs(array $trail): array
    {
        $items = [];

        foreach (array_values($trail) as $index => $crumb) {
            $items[] = array_filter([
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb['label'] ?? null,
                'item' => ! empty($crumb['url']) ? url($crumb['url']) : null,
            ], fn ($value) => $value !== null);
        }

        return $items === [] ? [] : [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /** Kurumsal kimlik bloğu; ana sayfada basılır. */
    public static function organization(string $locale): array
    {
        $settings = \App\Models\Setting::all();
        $name = data_get($settings, 'general.site_name');

        if (blank($name)) {
            return [];
        }

        $social = array_values(array_filter(data_get($settings, 'social', []) ?: []));
        $logo = \App\Models\Media::find(data_get($settings, 'general.logo_id'));

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $name,
            'url' => url('/'),
            'logo' => $logo?->url('medium'),
            'email' => data_get($settings, 'contact.email') ?: null,
            'telephone' => data_get($settings, 'contact.phone') ?: null,
            'address' => ($address = data_get($settings, "contact.address.{$locale}"))
                ? ['@type' => 'PostalAddress', 'streetAddress' => $address]
                : null,
            'sameAs' => $social ?: null,
        ], fn ($value) => $value !== null && $value !== '');
    }

    /** Boş yapılar hiç basılmasın diye null döner. */
    public static function encode(array $data): ?string
    {
        if ($data === []) {
            return null;
        }

        return json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP
        ) ?: null;
    }
}
