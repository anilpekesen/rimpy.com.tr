<!DOCTYPE html>
<html lang="tr">
<head><meta charset="utf-8"><title>Yeni form mesajı</title></head>
<body style="margin:0;background:#f5f6f8;font-family:-apple-system,'Segoe UI',Roboto,Arial,sans-serif;color:#1f2937">
    <div style="max-width:600px;margin:24px auto;background:#fff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9">
            <h1 style="margin:0;font-size:16px;font-weight:600">{{ $form->name }}</h1>
            <p style="margin:4px 0 0;font-size:13px;color:#6b7280">
                {{ now()->timezone(config('app.timezone'))->format('d.m.Y H:i') }} tarihinde gönderildi
            </p>
        </div>

        <table style="width:100%;border-collapse:collapse">
            @foreach ($data as $key => $value)
                @php
                    $label = collect($form->fields)->firstWhere('key', $key)['label']['tr'] ?? $key;
                @endphp
                @continue (in_array($key, ['locale', 'page_url'], true))
                <tr>
                    <td style="padding:10px 24px;font-size:13px;color:#6b7280;vertical-align:top;width:35%;border-bottom:1px solid #f1f5f9">
                        {{ $label }}
                    </td>
                    <td style="padding:10px 24px;font-size:14px;color:#111827;border-bottom:1px solid #f1f5f9;white-space:pre-line">
                        {{ is_bool($value) ? ($value ? 'Evet' : 'Hayır') : $value }}
                    </td>
                </tr>
            @endforeach
        </table>

        <div style="padding:16px 24px;background:#f9fafb;font-size:12px;color:#6b7280">
            Bu mesaj {{ \App\Models\Setting::get('general.site_name', config('app.name')) }} sitesindeki formdan gönderildi.
            @if ($submission)
                <br>Panelden görüntülemek için: <a href="{{ route('admin.submissions.index') }}" style="color:#2563eb">Gelen mesajlar</a>
            @endif
        </div>
    </div>
</body>
</html>
