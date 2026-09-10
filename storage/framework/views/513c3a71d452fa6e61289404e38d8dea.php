<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($template): ?>
    <?php echo app(\App\Builder\Renderer::class)->template($template, $context); ?>

<?php elseif($context->editing): ?>
    <p class="text-sm text-ink-400">Kayıtlı bölüm seçilmedi.</p>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/builder/widgets/template.blade.php ENDPATH**/ ?>