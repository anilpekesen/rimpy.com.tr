<?php

namespace App\Support;

use App\Models\ContentItem;

/**
 * Hidrolik tork anahtarlarında çıkış torku pompa basıncıyla doğru orantılıdır:
 * T = Tmaks × P / Pmaks. Tablo bu oranla, 50 bar adımlarla üretilir.
 */
class TorqueTable
{
    private const PSI_PER_BAR = 14.5038;

    private const FTLB_PER_NM = 0.737562;

    /** @return array{max_torque: float, max_pressure: float, rows: array}|null */
    public static function for(ContentItem $item, int $step = 50): ?array
    {
        $torque = $item->custom('tork_maks_nm');
        $pressure = $item->custom('basinc_bar');

        if (! is_numeric($torque) || ! is_numeric($pressure) || $pressure <= 0) {
            return null;
        }

        $rows = [];

        for ($bar = $step; $bar <= $pressure; $bar += $step) {
            $nm = $torque * $bar / $pressure;
            $rows[] = [
                'bar' => $bar,
                'psi' => $bar * self::PSI_PER_BAR,
                'nm' => $nm,
                'ftlb' => $nm * self::FTLB_PER_NM,
            ];
        }

        return ['max_torque' => (float) $torque, 'max_pressure' => (float) $pressure, 'rows' => $rows];
    }
}
