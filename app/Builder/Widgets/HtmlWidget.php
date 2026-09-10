<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Widget;

/**
 * Dışarıdan alınan gömme kodları (rezervasyon aracı, canlı destek, harita)
 * için. Yalnızca yönetici seviyesindeki kullanıcılara açıktır; bir editörün
 * siteye rastgele betik eklemesi engellenir.
 */
class HtmlWidget extends Widget
{
    public function key(): string { return 'html'; }
    public function label(): string { return 'Gömme kod'; }
    public function icon(): string { return 'code'; }
    public function category(): string { return 'basic'; }

    public function minimumRole(): string
    {
        return 'admin';
    }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'Kod', 'controls' => [
                'code' => Controls::textarea('HTML kodu', [
                    'rows' => 10,
                    'mono' => true,
                    'hint' => 'Yalnızca güvendiğiniz kaynaklardan aldığınız kodu yapıştırın. Buraya yapıştırılan kod siteyi ziyaret eden herkesin tarayıcısında çalışır.',
                ]),
            ]],
        ];
    }
}
