<?php $link = is_array($value) ? $value : ['type' => 'url']; ?>

<div class="space-y-2 rounded-lg border border-ink-200 p-2.5" wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>-link"
     x-data="{
        v: <?php echo \Illuminate\Support\Js::from($link + ['type' => 'url', 'url' => '', 'id' => '', 'anchor' => '', 'email' => '', 'phone' => '', 'target' => '_self'])->toHtml() ?>,
        push() { $wire.setSetting('<?php echo e($key); ?>', this.v, false) }
     }">

    <select class="field px-1.5 py-1 text-xs" x-model="v.type" @change="push()" aria-label="Bağlantı türü">
        <option value="page">Sitedeki bir sayfa</option>
        <option value="content">Sitedeki bir içerik</option>
        <option value="url">Web adresi</option>
        <option value="anchor">Aynı sayfada bir bölüm</option>
        <option value="email">E-posta gönder</option>
        <option value="phone">Telefonla ara</option>
        <option value="file">İndirilecek dosya</option>
    </select>

    <template x-if="v.type === 'page'">
        <select class="field px-1.5 py-1 text-xs" x-model="v.id" @change="push()">
            <option value="">— Sayfa seçin —</option>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Models\Page::withTranslations()->orderBy('sort')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($p->id); ?>"><?php echo e($p->t('title') ?: 'Adsız'); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </select>
    </template>

    <template x-if="v.type === 'content'">
        <select class="field px-1.5 py-1 text-xs" x-model="v.id" @change="push()">
            <option value="">— İçerik seçin —</option>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Models\ContentItem::withTranslations()->with('type')->latest()->limit(200)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ci): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($ci->id); ?>"><?php echo e($ci->type?->label()); ?>: <?php echo e($ci->t('title') ?: 'Adsız'); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </select>
    </template>

    <template x-if="v.type === 'url'">
        <input type="url" class="field px-1.5 py-1 text-xs" x-model="v.url"
               @input.debounce.400ms="push()" placeholder="https://ornek.com">
    </template>

    <template x-if="v.type === 'anchor'">
        <input type="text" class="field px-1.5 py-1 text-xs" x-model="v.anchor"
               @input.debounce.400ms="push()" placeholder="iletisim">
    </template>

    <template x-if="v.type === 'email'">
        <input type="email" class="field px-1.5 py-1 text-xs" x-model="v.email"
               @input.debounce.400ms="push()" placeholder="info@ornek.com">
    </template>

    <template x-if="v.type === 'phone'">
        <input type="tel" class="field px-1.5 py-1 text-xs" x-model="v.phone"
               @input.debounce.400ms="push()" placeholder="+90 555 000 00 00">
    </template>

    <template x-if="v.type === 'file'">
        <button type="button" class="btn-secondary btn-sm w-full"
                @click="$dispatch('open-media-picker', { target: '<?php echo e($key); ?>.id', accept: 'document' })">
            Dosya seç
        </button>
    </template>

    <label class="flex items-center gap-2 text-xs text-ink-700">
        <input type="checkbox" class="size-3.5 rounded border-ink-300 text-brand-600"
               :checked="v.target === '_blank'"
               @change="v.target = $event.target.checked ? '_blank' : '_self'; push()">
        Yeni sekmede açılsın
    </label>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/livewire/builder/partials/control-link.blade.php ENDPATH**/ ?>