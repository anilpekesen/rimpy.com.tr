<!DOCTYPE html>
<html lang="tr">
<head><meta charset="utf-8"><title>Yeni form mesajı</title></head>
<body style="margin:0;background:#f5f6f8;font-family:-apple-system,'Segoe UI',Roboto,Arial,sans-serif;color:#1f2937">
    <div style="max-width:600px;margin:24px auto;background:#fff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9">
            <h1 style="margin:0;font-size:16px;font-weight:600"><?php echo e($form->name); ?></h1>
            <p style="margin:4px 0 0;font-size:13px;color:#6b7280">
                <?php echo e(now()->timezone(config('app.timezone'))->format('d.m.Y H:i')); ?> tarihinde gönderildi
            </p>
        </div>

        <table style="width:100%;border-collapse:collapse">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $label = collect($form->fields)->firstWhere('key', $key)['label']['tr'] ?? $key;
                ?>
                <?php if(in_array($key, ['locale', 'page_url'], true)) continue; ?>
                <tr>
                    <td style="padding:10px 24px;font-size:13px;color:#6b7280;vertical-align:top;width:35%;border-bottom:1px solid #f1f5f9">
                        <?php echo e($label); ?>

                    </td>
                    <td style="padding:10px 24px;font-size:14px;color:#111827;border-bottom:1px solid #f1f5f9;white-space:pre-line">
                        <?php echo e(is_bool($value) ? ($value ? 'Evet' : 'Hayır') : $value); ?>

                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </table>

        <div style="padding:16px 24px;background:#f9fafb;font-size:12px;color:#6b7280">
            Bu mesaj <?php echo e(\App\Models\Setting::get('general.site_name', config('app.name'))); ?> sitesindeki formdan gönderildi.
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($submission): ?>
                <br>Panelden görüntülemek için: <a href="<?php echo e(route('admin.submissions.index')); ?>" style="color:#2563eb">Gelen mesajlar</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/mail/submission.blade.php ENDPATH**/ ?>