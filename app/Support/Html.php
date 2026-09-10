<?php

namespace App\Support;

/**
 * Yönetici tarafından girilen zengin metni ziyaretçiye basmadan önce temizler.
 *
 * Panele giriş yapabilen herkes güvenilir sayılmaz: bir editörün hesabı ele
 * geçebilir ya da içerik başka bir siteden kopyalanıp yapıştırılmış olabilir.
 * Bu yüzden metin içeriği korunurken çalıştırılabilir her şey atılır.
 */
class Html
{
    /** Metin içinde kalmasına izin verilen etiketler. */
    private const ALLOWED_TAGS = '<p><br><strong><b><em><i><u><s><a><ul><ol><li><h2><h3><h4><h5><h6>'
        .'<blockquote><figure><figcaption><img><table><thead><tbody><tfoot><tr><th><td>'
        .'<span><div><hr><sup><sub><small><code><pre>';

    public static function sanitize(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        // Betik, stil ve gömme çerçeveleri içerikleriyle birlikte silinir.
        $html = preg_replace('#<(script|style|iframe|object|embed|form|link|meta)\b[^>]*>.*?</\1>#is', '', $html);
        $html = preg_replace('#<(script|style|iframe|object|embed|form|link|meta)\b[^>]*/?>#is', '', $html);

        $html = strip_tags($html, self::ALLOWED_TAGS);

        // onclick, onerror gibi olay öznitelikleri
        $html = preg_replace('/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);

        // javascript: ve data: ile başlayan adresler
        $html = preg_replace('/(href|src)\s*=\s*(["\']?)\s*(javascript|vbscript|data)\s*:[^"\'>\s]*/i', '$1=$2#', $html);

        // style özniteliği içine gizlenen ifadeler
        $html = preg_replace('/\sstyle\s*=\s*("[^"]*expression[^"]*"|\'[^\']*expression[^\']*\')/i', '', $html);

        return trim($html);
    }

    /**
     * Yalnızca yöneticinin girebildiği gömme kodu için. Buradaki içerik
     * kasıtlı olarak temizlenmez; bu yüzden ilgili widget yalnızca yönetici
     * ve üzeri rollere açıktır.
     */
    public static function raw(?string $html): string
    {
        return (string) $html;
    }

    /** Metinden belirtilen kelime sayısında özet çıkarır. */
    public static function excerpt(?string $html, int $words = 20): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $html)));

        return \Illuminate\Support\Str::words($text, $words, '…');
    }
}
