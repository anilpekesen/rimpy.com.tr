<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Element;
use App\Builder\RenderContext;
use App\Builder\Widget;

class VideoWidget extends Widget
{
    public function key(): string { return 'video'; }
    public function label(): string { return 'Video'; }
    public function icon(): string { return 'play-circle'; }
    public function category(): string { return 'media'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => [
                'source' => Controls::select('Kaynak', [
                    'youtube' => 'YouTube', 'vimeo' => 'Vimeo', 'file' => 'Yüklenmiş video',
                ], ['default' => 'youtube']),
                'url' => Controls::text('Video adresi', [
                    'hint' => 'Tarayıcının adres çubuğundaki bağlantıyı olduğu gibi yapıştırın.',
                    'condition' => ['source' => ['youtube', 'vimeo']],
                ]),
                'file' => Controls::media('Video dosyası', ['accept' => 'video', 'condition' => ['source' => 'file']]),
                'poster' => Controls::media('Kapak görseli', [
                    'hint' => 'Video oynatılmadan önce görünen kare.',
                ]),
                'ratio' => Controls::select('En-boy oranı', [
                    '16/9' => '16:9 (geniş)', '4/3' => '4:3', '1/1' => 'Kare', '9/16' => 'Dikey',
                ], ['default' => '16/9', 'selector' => '{{WRAPPER}} .video', 'property' => 'aspect-ratio']),
                'autoplay' => Controls::toggle('Kendiliğinden başlat', [
                    'hint' => 'Tarayıcılar sesli videoyu kendiliğinden başlatmaz; sesi kapatmanız gerekir.',
                ]),
                'muted' => Controls::toggle('Sesi kapalı başlat', ['default' => true]),
                'loop' => Controls::toggle('Bitince başa dön'),
                'controls' => Controls::toggle('Oynatma düğmelerini göster', ['default' => true]),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'radius' => Controls::dimensions('Köşe yuvarlaklığı', 'border-radius', ['selector' => '{{WRAPPER}} .video']),
            ]],
        ];
    }

    public function data(Element $element, RenderContext $context): array
    {
        $settings = $this->resolved($element, $context);

        return [
            'settings' => $settings,
            'embed' => $this->embedUrl($settings),
        ];
    }

    /**
     * Kullanıcının yapıştırdığı adresi gömme adresine çevirir. Adres
     * tanınmazsa null döner ve widget hiçbir şey basmaz — sayfada kırık bir
     * çerçeve görünmez.
     */
    private function embedUrl(array $settings): ?string
    {
        $url = (string) ($settings['url'] ?? '');

        if (blank($url)) {
            return null;
        }

        $params = array_filter([
            'autoplay' => ! empty($settings['autoplay']) ? 1 : null,
            'mute' => ! empty($settings['muted']) ? 1 : null,
            'muted' => ! empty($settings['muted']) ? 1 : null,
            'loop' => ! empty($settings['loop']) ? 1 : null,
            'controls' => empty($settings['controls']) ? 0 : null,
            'rel' => 0,
        ], fn ($v) => $v !== null);

        if ($settings['source'] === 'youtube') {
            preg_match('#(?:youtu\.be/|v=|/embed/|/shorts/)([A-Za-z0-9_-]{6,})#', $url, $m);

            if (empty($m[1])) {
                return null;
            }

            unset($params['muted']);

            if (! empty($settings['loop'])) {
                $params['playlist'] = $m[1];
            }

            return 'https://www.youtube-nocookie.com/embed/'.$m[1].'?'.http_build_query($params);
        }

        if ($settings['source'] === 'vimeo') {
            preg_match('#vimeo\.com/(?:video/)?(\d+)#', $url, $m);

            if (empty($m[1])) {
                return null;
            }

            unset($params['mute'], $params['rel']);

            return 'https://player.vimeo.com/video/'.$m[1].'?'.http_build_query($params);
        }

        return null;
    }
}
