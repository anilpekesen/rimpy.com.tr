<?php

namespace App\Http\Controllers;

use App\Models\ContentItem;
use App\Support\TorqueTable;

/**
 * Mühendislik araçlarının dosya çıktıları.
 */
class ToolsController extends Controller
{
    /**
     * Bir hidrolik tork anahtarının basınç–tork tablosu, Excel'in doğrudan
     * açtığı CSV olarak. Sahada pompa manometresinden okunan basıncın hangi
     * torka karşılık geldiğini gösterir.
     */
    public function torqueTable(ContentItem $item)
    {
        abort_unless($item->status === 'published', 404);

        $table = TorqueTable::for($item);

        abort_if($table === null, 404);

        $code = $item->custom('model_kodu') ?: $item->id;
        $filename = 'basinc-tork-tablosu-'.\Illuminate\Support\Str::slug($code).'.csv';

        return response()->streamDownload(function () use ($item, $table, $code) {
            $out = fopen('php://output', 'w');

            // Excel'in Türkçe karakterleri doğru okuması için bayt işareti.
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, [$code.' — '.$item->t('title')], ';');
            fputcsv($out, ['Maks. tork: '.number_format($table['max_torque'], 0, ',', '.').' N·m', 'Maks. basınç: '.$table['max_pressure'].' bar'], ';');
            fputcsv($out, [], ';');
            fputcsv($out, ['Basınç (bar)', 'Basınç (psi)', 'Tork (N·m)', 'Tork (ft·lbf)'], ';');

            foreach ($table['rows'] as $row) {
                fputcsv($out, [
                    $row['bar'],
                    number_format($row['psi'], 0, ',', ''),
                    number_format($row['nm'], 0, ',', ''),
                    number_format($row['ftlb'], 0, ',', ''),
                ], ';');
            }

            fputcsv($out, [], ';');
            fputcsv($out, ['Değerler doğrusal yaklaşımdır; kesin değer için anahtarın kalibrasyon sertifikasındaki tabloyu esas alın.'], ';');

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
