<?php

namespace Tests\Unit;

use App\Builder\StyleCompiler;
use App\Builder\Tree;
use Tests\TestCase;

/**
 * Stil derleyicisi kurucunun kalbidir: kullanıcının seçtiği her ayar
 * buradan geçerek CSS'e dönüşür. Bozulursa sitenin görünümü bozulur.
 */
class StyleCompilerTest extends TestCase
{
    private function compile(array $settings, string $type = 'section', ?string $widget = null, ?string $device = null): string
    {
        $tree = Tree::fromArray(['elements' => [[
            'id' => 'x1',
            'type' => $type,
            'widget' => $widget,
            'settings' => $settings,
            'elements' => [],
        ]]]);

        return app(StyleCompiler::class)->compile($tree, $device);
    }

    public function test_kenar_bosluklari_dogru_ceviriliyor(): void
    {
        $css = $this->compile([
            '_padding' => ['d' => ['t' => 80, 'b' => 40, 'unit' => 'px']],
        ]);

        $this->assertStringContainsString('.e-x1{padding-top:80px;padding-bottom:40px;}', $css);
        // Boş bırakılan kenarlar hiç yazılmamalı.
        $this->assertStringNotContainsString('padding-left', $css);
    }

    public function test_tema_rengi_css_degiskenine_donusuyor(): void
    {
        $css = $this->compile([
            '_background' => ['type' => 'color', 'color' => '@primary'],
        ]);

        $this->assertStringContainsString('background-color:var(--c-primary)', $css);
    }

    public function test_ozel_renk_oldugu_gibi_yaziliyor(): void
    {
        $css = $this->compile([
            '_background' => ['type' => 'color', 'color' => '#ff6600'],
        ]);

        $this->assertStringContainsString('background-color:#ff6600', $css);
    }

    public function test_cihaz_ayarlari_medya_sorgusuna_giriyor(): void
    {
        $css = $this->compile([
            '_padding' => [
                'd' => ['t' => 80, 'unit' => 'px'],
                'm' => ['t' => 24, 'unit' => 'px'],
            ],
        ]);

        $this->assertStringContainsString('.e-x1{padding-top:80px;}', $css);
        $this->assertMatchesRegularExpression('/@media\(max-width:\d+px\)\{\.e-x1\{padding-top:24px;\}\}/', $css);
    }

    public function test_onizleme_kipinde_cihaz_kurallari_duz_yaziliyor(): void
    {
        $css = $this->compile([
            '_padding' => [
                'd' => ['t' => 80, 'unit' => 'px'],
                'm' => ['t' => 24, 'unit' => 'px'],
            ],
        ], device: 'm');

        // Tuval gerçek bir telefon kadar dar olmadığı için medya sorgusu
        // kullanılamaz; kural doğrudan yazılmalı.
        $this->assertStringNotContainsString('@media', $css);
        $this->assertStringContainsString('padding-top:24px', $css);
    }

    public function test_tipografi_bilesik_deger_uretiyor(): void
    {
        $css = $this->compile([
            'typography' => ['d' => ['family' => 'heading', 'size' => 42, 'weight' => 700, 'lh' => 1.15, 'ls' => -0.02]],
        ], type: 'widget', widget: 'heading');

        $this->assertStringContainsString('font-family:var(--font-heading)', $css);
        $this->assertStringContainsString('font-size:42px', $css);
        $this->assertStringContainsString('font-weight:700', $css);
        $this->assertStringContainsString('line-height:1.15', $css);
        $this->assertStringContainsString('letter-spacing:-0.02em', $css);
    }

    public function test_kenarlik_ve_kose_yuvarlakligi(): void
    {
        $css = $this->compile([
            '_border' => [
                'style' => 'solid',
                'color' => '@border',
                'width' => ['t' => 1, 'r' => 1, 'b' => 1, 'l' => 1, 'unit' => 'px'],
                'radius' => ['t' => 12, 'r' => 12, 'b' => 12, 'l' => 12, 'unit' => 'px'],
            ],
        ]);

        $this->assertStringContainsString('border-top-width:1px', $css);
        $this->assertStringContainsString('border-style:solid', $css);
        $this->assertStringContainsString('border-color:var(--c-border)', $css);
        $this->assertStringContainsString('border-top-left-radius:12px', $css);
    }

    public function test_bos_ayarlar_css_uretmiyor(): void
    {
        $this->assertSame('', $this->compile([]));
        $this->assertSame('', $this->compile(['_padding' => null, '_background' => []]));
    }

    public function test_her_eleman_kendi_kapsaminda(): void
    {
        $tree = Tree::fromArray(['elements' => [
            ['id' => 'a', 'type' => 'section', 'settings' => ['_padding' => ['d' => ['t' => 10, 'unit' => 'px']]], 'elements' => []],
            ['id' => 'b', 'type' => 'section', 'settings' => ['_padding' => ['d' => ['t' => 20, 'unit' => 'px']]], 'elements' => []],
        ]]);

        $css = app(StyleCompiler::class)->compile($tree);

        // İki bölüm birbirinin stilini ezmemeli.
        $this->assertStringContainsString('.e-a{padding-top:10px;}', $css);
        $this->assertStringContainsString('.e-b{padding-top:20px;}', $css);
    }
}
