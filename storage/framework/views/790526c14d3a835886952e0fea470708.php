<?php
    // Kurucuda kullanılabilen ikonlar. Dışarıdan bir ikon paketi yüklenmediği
    // için liste bilinçli olarak dar ve kurumsal sitelerin ihtiyacına göre seçili.
    $icons = [
        '' => 'Yok',
        'check' => 'Onay', 'check-circle' => 'Onay (daire)', 'star' => 'Yıldız',
        'heart' => 'Kalp', 'shield' => 'Kalkan', 'award' => 'Ödül',
        'phone' => 'Telefon', 'mail' => 'Zarf', 'map-pin' => 'Konum',
        'clock' => 'Saat', 'truck' => 'Kamyon', 'package' => 'Paket',
        'factory' => 'Fabrika', 'leaf' => 'Yaprak', 'users' => 'Ekip',
        'trending-up' => 'Artış', 'target' => 'Hedef', 'settings' => 'Ayar',
        'arrow-right' => 'Sağ ok', 'download' => 'İndir', 'play' => 'Oynat',
    ];
?>

<select id="<?php echo e($id); ?>" class="field" wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>-icon"
        x-on:change="$wire.setSetting('<?php echo e($key); ?>', $event.target.value, false)">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $icons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iconKey => $iconLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($iconKey); ?>" <?php if($value === $iconKey): echo 'selected'; endif; ?>><?php echo e($iconLabel); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</select>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/livewire/builder/partials/control-icon.blade.php ENDPATH**/ ?>