<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Sitenin tasarım jetonları: renkler, yazı tipleri, köşe yuvarlaklığı,
 * kapsayıcı genişliği. Kurucudaki her widget bu değerlere isimle referans
 * verir ("Ana renk"), sabit bir kod yazmaz. Müşteri panelden ana rengi
 * değiştirdiğinde bütün site aynı anda güncellenir.
 */
class Theme
{
    public const DEFAULTS = [
        'colors' => [
            'primary' => '#1a56db',
            'secondary' => '#0f172a',
            'accent' => '#f59e0b',
            'text' => '#1f2937',
            'muted' => '#6b7280',
            'heading' => '#0f172a',
            'background' => '#ffffff',
            'surface' => '#f8fafc',
            'border' => '#e5e7eb',
        ],
        'fonts' => [
            'heading' => ['family' => 'Inter', 'weights' => [600, 700]],
            'body' => ['family' => 'Inter', 'weights' => [400, 500]],
        ],
        'typography' => [
            'base_size' => 16,
            'scale' => 1.25,
            'line_height' => 1.6,
        ],
        'layout' => [
            'container' => 1200,
            'gutter' => 24,
            'radius' => 10,
            'section_padding' => 80,
        ],
        'breakpoints' => [
            'tablet' => 1024,
            'mobile' => 767,
        ],
    ];

    private ?array $values = null;

    public function all(): array
    {
        return $this->values ??= array_replace_recursive(
            self::DEFAULTS,
            Setting::get('theme', []) ?: []
        );
    }

    public function get(string $path, mixed $default = null): mixed
    {
        return data_get($this->all(), $path, $default);
    }

    /**
     * Bellekteki kopyayı atar. Ayarlar kaydedildiğinde çağrılır; aksi hâlde
     * aynı istek içinde eski palet okunmaya devam ederdi.
     */
    public function flush(): void
    {
        $this->values = null;
    }

    /** Renk paletini `var(--c-primary)` gibi kullanılabilir hale getirir. */
    public function cssVariables(): string
    {
        $lines = [];

        foreach ($this->get('colors', []) as $key => $value) {
            $lines[] = "--c-{$key}: {$value};";
            // Saydamlık türevleri için renk bileşenleri
            if ($rgb = $this->hexToRgb($value)) {
                $lines[] = "--c-{$key}-rgb: {$rgb};";
            }
        }

        $lines[] = '--font-heading: '.$this->fontStack('heading').';';
        $lines[] = '--font-body: '.$this->fontStack('body').';';
        $lines[] = '--radius: '.$this->get('layout.radius').'px;';
        $lines[] = '--container: '.$this->get('layout.container').'px;';
        $lines[] = '--gutter: '.$this->get('layout.gutter').'px;';
        $lines[] = '--section-y: '.$this->get('layout.section_padding').'px;';
        $lines[] = '--text-base: '.$this->get('typography.base_size').'px;';
        $lines[] = '--leading: '.$this->get('typography.line_height').';';

        return ':root{'.implode('', $lines).'}';
    }

    public function fontStack(string $role): string
    {
        $family = $this->get("fonts.{$role}.family", 'Inter');

        return "'{$family}', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
    }

    /** Google Fonts bağlantısı; hiç yazı tipi seçilmemişse boş döner. */
    public function fontUrl(): ?string
    {
        $families = [];

        foreach (['heading', 'body'] as $role) {
            $family = $this->get("fonts.{$role}.family");
            $weights = $this->get("fonts.{$role}.weights", [400]);

            if ($family && $family !== 'system') {
                $key = str_replace(' ', '+', $family);
                $families[$key] = ($families[$key] ?? []) + array_flip($weights);
            }
        }

        if (! $families) {
            return null;
        }

        $parts = [];

        foreach ($families as $family => $weights) {
            $list = implode(';', array_map('strval', array_keys($weights)));
            $parts[] = "family={$family}:wght@{$list}";
        }

        return 'https://fonts.googleapis.com/css2?'.implode('&', $parts).'&display=swap';
    }

    public function breakpoint(string $device): int
    {
        return (int) $this->get("breakpoints.{$device}", $device === 'tablet' ? 1024 : 767);
    }

    private function hexToRgb(string $hex): ?string
    {
        $hex = ltrim($hex, '#');

        if (! preg_match('/^[0-9a-f]{6}$/i', $hex)) {
            return null;
        }

        return implode(' ', array_map('hexdec', str_split($hex, 2)));
    }
}
