<?php
    $context = new \App\Builder\RenderContext($locale, subject: $page);
    $tree = \App\Builder\Tree::fromArray($translation->builder_data);
?>

<?php if (isset($component)) { $__componentOriginald956570c5321d7185b887a45463f814f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald956570c5321d7185b887a45463f814f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.site-layout','data' => ['locale' => $locale,'subject' => $page,'seoTitle' => $translation->seo_title ?: $translation->title.' — '.\App\Models\Setting::get('general.site_name'),'seoDescription' => $translation->seo_description ?: $translation->excerpt,'noindex' => $translation->noindex,'ogImageId' => $translation->og_image_id,'canonical' => $translation->canonical_url ?: url()->current()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['locale' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($locale),'subject' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($page),'seo-title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($translation->seo_title ?: $translation->title.' — '.\App\Models\Setting::get('general.site_name')),'seo-description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($translation->seo_description ?: $translation->excerpt),'noindex' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($translation->noindex),'og-image-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($translation->og_image_id),'canonical' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($translation->canonical_url ?: url()->current())]); ?>

    <?php $__env->startPush('styles'); ?>
        <style><?php echo $translation->compiled_css; ?></style>
    <?php $__env->stopPush(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tree->isEmpty()): ?>
        <div style="max-width:var(--container);margin-inline:auto;padding:6rem max(16px,var(--gutter))">
            <h1><?php echo e($translation->title); ?></h1>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($translation->excerpt): ?>
                <p><?php echo e($translation->excerpt); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php else: ?>
        <?php echo app(\App\Builder\Renderer::class)->render($tree, $context); ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php
        // Yapısal veri PHP tarafında kurulur; Schema.org anahtarları "@" ile
        // başladığı için Blade içinde yazılamaz.
        $structured = array_filter([
            $page->parent_id
                ? \App\Support\Schema::breadcrumbs(\App\Support\Breadcrumbs::for($page, $locale))
                : [],
            $page->is_home ? \App\Support\Schema::organization($locale) : [],
        ]);
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $structured; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($json = \App\Support\Schema::encode($block)): ?>
            <script type="application/ld+json"><?php echo $json; ?></script>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald956570c5321d7185b887a45463f814f)): ?>
<?php $attributes = $__attributesOriginald956570c5321d7185b887a45463f814f; ?>
<?php unset($__attributesOriginald956570c5321d7185b887a45463f814f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald956570c5321d7185b887a45463f814f)): ?>
<?php $component = $__componentOriginald956570c5321d7185b887a45463f814f; ?>
<?php unset($__componentOriginald956570c5321d7185b887a45463f814f); ?>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/front/page.blade.php ENDPATH**/ ?>