<?php
/**
 * Tek bir ayarın arayüzünü çizer.
 *
 * Denetimin türü tanımında yazdığı için burada yalnızca doğru girdi
 * seçilir. Yeni bir widget yazan kişinin ayrıca arayüz kodu yazması
 * gerekmez.
 */
$type = $control['type'] ?? 'text';
$responsive = ! empty($control['responsive']);
$value = $this->settingValue($key, $responsive, $control['default'] ?? null);
$id = 'ctrl-'.$key;

// Koşullu denetim: bağlı olduğu ayar beklenen değerde değilse hiç çizilmez.
if ($condition = $control['condition'] ?? null) {
    foreach ($condition as $dependsOn => $expected) {
        $actual = $this->settingValue($dependsOn, false);
        $matches = is_array($expected) ? in_array($actual, $expected, true) : $actual == $expected;

        if (! $matches) {
            return;
        }
    }
}

$devices = ['d' => 'Masaüstü', 't' => 'Tablet', 'm' => 'Telefon'];
?>

<div class="space-y-1.5">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type !== 'toggle'): ?>
        <div class="flex items-baseline justify-between gap-2">
            <label for="<?php echo e($id); ?>" class="label mb-0"><?php echo e($control['label']); ?></label>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($responsive): ?>
                <span class="text-[10px] font-medium text-ink-400" title="Bu ayar her cihaz için ayrı tutulur">
                    <?php echo e($devices[$device]); ?>

                </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php switch($type):

        
        case ('text'): ?>
            <input id="<?php echo e($id); ?>" type="text" class="field"
                   value="<?php echo e(is_scalar($value) ? $value : ''); ?>"
                   wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>"
                   x-on:input.debounce.400ms="$wire.setSetting('<?php echo e($key); ?>', $event.target.value, <?php echo e($responsive ? 'true' : 'false'); ?>)">
            <?php break; ?>

        <?php case ('textarea'): ?>
            <textarea id="<?php echo e($id); ?>" rows="<?php echo e($control['rows'] ?? 3); ?>"
                      class="field <?php echo e(! empty($control['mono']) ? 'font-mono text-xs' : ''); ?>"
                      wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>"
                      x-on:input.debounce.400ms="$wire.setSetting('<?php echo e($key); ?>', $event.target.value, <?php echo e($responsive ? 'true' : 'false'); ?>)"><?php echo e(is_scalar($value) ? $value : ''); ?></textarea>
            <?php break; ?>

        <?php case ('rich'): ?>
            <div wire:ignore wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>">
                <div class="overflow-hidden rounded-lg border border-ink-300">
                    <div class="flex flex-wrap gap-0.5 border-b border-ink-200 bg-ink-50 p-1"
                         x-data="{ cmd(c, v = null) { document.execCommand(c, false, v); $refs.editor.focus(); } }">
                        <button type="button" @click="cmd('bold')" class="btn-ghost btn-sm font-bold" title="Kalın">B</button>
                        <button type="button" @click="cmd('italic')" class="btn-ghost btn-sm italic" title="İtalik">I</button>
                        <button type="button" @click="cmd('formatBlock', '<h2>')" class="btn-ghost btn-sm" title="Başlık">H2</button>
                        <button type="button" @click="cmd('formatBlock', '<h3>')" class="btn-ghost btn-sm" title="Alt başlık">H3</button>
                        <button type="button" @click="cmd('formatBlock', '<p>')" class="btn-ghost btn-sm" title="Paragraf">P</button>
                        <button type="button" @click="cmd('insertUnorderedList')" class="btn-ghost btn-sm" title="Madde listesi">•</button>
                        <button type="button" @click="cmd('insertOrderedList')" class="btn-ghost btn-sm" title="Numaralı liste">1.</button>
                        <button type="button" @click="const u = prompt('Bağlantı adresi:'); if (u) cmd('createLink', u)" class="btn-ghost btn-sm" title="Bağlantı">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'link','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'link','class' => 'size-3.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                        </button>
                        <button type="button" @click="cmd('removeFormat')" class="btn-ghost btn-sm ml-auto" title="Biçimi temizle">⌫</button>
                    </div>
                    <div x-ref="editor" contenteditable="true"
                         class="prose prose-sm max-w-none px-3 py-2 text-sm focus:outline-none [&_h2]:text-base [&_h2]:font-semibold [&_h3]:text-sm [&_h3]:font-semibold [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5"
                         style="min-height: 120px"
                         x-on:input.debounce.500ms="$wire.setSetting('<?php echo e($key); ?>', $refs.editor.innerHTML, <?php echo e($responsive ? 'true' : 'false'); ?>)"
                    ><?php echo is_scalar($value) ? $value : ''; ?></div>
                </div>
            </div>
            <?php break; ?>

        <?php case ('number'): ?>
            <input id="<?php echo e($id); ?>" type="number" class="field"
                   value="<?php echo e(is_scalar($value) ? $value : ''); ?>"
                   wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>"
                   x-on:input.debounce.400ms="$wire.setSetting('<?php echo e($key); ?>', $event.target.value === '' ? null : Number($event.target.value), <?php echo e($responsive ? 'true' : 'false'); ?>)">
            <?php break; ?>

        
        <?php case ('slider'): ?>
            <?php
                $units = $control['units'] ?? [$control['unit'] ?? 'px'];
                $size = is_array($value) ? ($value['size'] ?? '') : ($value ?? '');
                $unit = is_array($value) ? ($value['unit'] ?? $units[0]) : $units[0];
            ?>
            
            <div class="flex items-center gap-2" wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>"
                 x-data="{
                    size: <?php echo \Illuminate\Support\Js::from($size)->toHtml() ?>, unit: <?php echo \Illuminate\Support\Js::from($unit)->toHtml() ?>,
                    push() { $wire.setSetting('<?php echo e($key); ?>', { size: this.size === '' ? null : Number(this.size), unit: this.unit }, <?php echo e($responsive ? 'true' : 'false'); ?>) }
                 }">
                <input type="range" class="flex-1 accent-brand-600"
                       min="<?php echo e($control['min'] ?? 0); ?>" max="<?php echo e($control['max'] ?? 200); ?>" step="<?php echo e($control['step'] ?? 1); ?>"
                       x-model.number="size" @change="push()">
                <input type="number" class="field w-16 px-1.5 py-1 text-center text-xs"
                       x-model.number="size" @input.debounce.400ms="push()" aria-label="<?php echo e($control['label']); ?> değeri">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($units) > 1): ?>
                    <select class="field w-14 px-1 py-1 text-xs" x-model="unit" @change="push()" aria-label="Birim">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($u); ?>"><?php echo e($u); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                <?php else: ?>
                    <span class="w-8 text-xs text-ink-400"><?php echo e($units[0]); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php break; ?>

        
        <?php case ('select'): ?>
            <select id="<?php echo e($id); ?>" class="field" wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>"
                    x-on:change="$wire.setSetting('<?php echo e($key); ?>', $event.target.value, <?php echo e($responsive ? 'true' : 'false'); ?>)">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $control['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionValue => $optionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($optionValue); ?>" <?php if($value == $optionValue): echo 'selected'; endif; ?>><?php echo e($optionLabel); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
            <?php break; ?>

        <?php case ('choose'): ?>
            <div class="flex rounded-lg border border-ink-200 p-0.5" role="group" aria-label="<?php echo e($control['label']); ?>"
                 wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $control['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionValue => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button"
                            x-on:click="$wire.setSetting('<?php echo e($key); ?>', '<?php echo e($optionValue); ?>', <?php echo e($responsive ? 'true' : 'false'); ?>)"
                            title="<?php echo e($option['label']); ?>"
                            class="flex-1 rounded-md px-2 py-1.5 text-xs font-medium transition
                                   <?php echo e($value === $optionValue ? 'bg-ink-900 text-white' : 'text-ink-500 hover:bg-ink-100'); ?>">
                        <?php echo e($option['label']); ?>

                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php break; ?>

        <?php case ('toggle'): ?>
            <label class="flex cursor-pointer items-start gap-2.5" wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>">
                <input type="checkbox" class="mt-0.5 size-4 shrink-0 rounded border-ink-300 text-brand-600 focus:ring-brand-500"
                       <?php if($value): echo 'checked'; endif; ?>
                       x-on:change="$wire.setSetting('<?php echo e($key); ?>', $event.target.checked, <?php echo e($responsive ? 'true' : 'false'); ?>)">
                <span class="text-sm text-ink-700"><?php echo e($control['label']); ?></span>
            </label>
            <?php break; ?>

        
        <?php case ('color'): ?>
            <?php $theme = app(\App\Support\Theme::class); ?>
            <div class="space-y-1.5" wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>">
                <div class="flex flex-wrap gap-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $theme->get('colors', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $token => $hex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" title="Tema rengi: <?php echo e($token); ?>"
                                x-on:click="$wire.setSetting('<?php echo e($key); ?>', <?php echo \Illuminate\Support\Js::from('@'.$token)->toHtml() ?>, <?php echo e($responsive ? 'true' : 'false'); ?>)"
                                class="size-6 rounded-md border-2 transition
                                       <?php echo e($value === '@'.$token ? 'border-brand-600' : 'border-ink-200 hover:border-ink-400'); ?>"
                                style="background: <?php echo e($hex); ?>">
                            <span class="sr-only"><?php echo e($token); ?></span>
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="flex items-center gap-2">
                    <input type="color" class="size-8 shrink-0 cursor-pointer rounded border border-ink-300 bg-white p-0.5"
                           value="<?php echo e(str_starts_with((string) $value, '#') ? $value : '#000000'); ?>"
                           x-on:input.debounce.300ms="$wire.setSetting('<?php echo e($key); ?>', $event.target.value, <?php echo e($responsive ? 'true' : 'false'); ?>)"
                           aria-label="Özel renk">
                    <input type="text" class="field flex-1 font-mono text-xs" placeholder="Tema rengi ya da #ffffff"
                           value="<?php echo e(is_scalar($value) ? $value : ''); ?>"
                           x-on:input.debounce.400ms="$wire.setSetting('<?php echo e($key); ?>', $event.target.value, <?php echo e($responsive ? 'true' : 'false'); ?>)">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($value): ?>
                        <button type="button" class="btn-ghost btn-sm" title="Temizle"
                                x-on:click="$wire.setSetting('<?php echo e($key); ?>', null, <?php echo e($responsive ? 'true' : 'false'); ?>)">×</button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(str_starts_with((string) $value, '@')): ?>
                    <p class="hint">Tema rengi seçildi — paleti değiştirdiğinizde burası da değişir.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php break; ?>

        
        <?php case ('dimensions'): ?>
            <?php
                $dims = is_array($value) ? $value : [];
                $dimUnits = $control['units'] ?? ['px'];
            ?>
            <div wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>"
                 x-data="{
                    v: <?php echo \Illuminate\Support\Js::from(['t' => $dims['t'] ?? '', 'r' => $dims['r'] ?? '', 'b' => $dims['b'] ?? '', 'l' => $dims['l'] ?? '', 'unit' => $dims['unit'] ?? $dimUnits[0]])->toHtml() ?>,
                    linked: <?php echo e((($dims['t'] ?? null) === ($dims['r'] ?? null) && ($dims['r'] ?? null) === ($dims['b'] ?? null) && ($dims['b'] ?? null) === ($dims['l'] ?? null)) ? 'true' : 'false'); ?>,
                    set(side, val) {
                        if (this.linked) { this.v.t = this.v.r = this.v.b = this.v.l = val; }
                        else { this.v[side] = val; }
                        this.push();
                    },
                    push() {
                        const out = { unit: this.v.unit };
                        for (const s of ['t','r','b','l']) out[s] = this.v[s] === '' ? null : Number(this.v[s]);
                        $wire.setSetting('<?php echo e($key); ?>', out, <?php echo e($responsive ? 'true' : 'false'); ?>);
                    }
                 }">
                <div class="flex items-center gap-1.5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['t' => 'Üst', 'r' => 'Sağ', 'b' => 'Alt', 'l' => 'Sol']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $side => $sideLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex-1">
                            <input type="number" class="field px-1 py-1 text-center text-xs" placeholder="—"
                                   x-model="v.<?php echo e($side); ?>" @input.debounce.400ms="set('<?php echo e($side); ?>', v.<?php echo e($side); ?>)"
                                   aria-label="<?php echo e($control['label']); ?> <?php echo e($sideLabel); ?>">
                            <span class="mt-0.5 block text-center text-[10px] text-ink-400"><?php echo e($sideLabel); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <button type="button" @click="linked = !linked" class="btn-ghost btn-sm shrink-0 self-start"
                            :title="linked ? 'Kenarları ayrı ayarla' : 'Tüm kenarları birlikte ayarla'"
                            :class="linked && 'text-brand-600'">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'link','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'link','class' => 'size-3.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                    </button>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($dimUnits) > 1): ?>
                        <select class="field w-13 shrink-0 self-start px-1 py-1 text-xs" x-model="v.unit" @change="push()" aria-label="Birim">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $dimUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($u); ?>"><?php echo e($u); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php break; ?>

        
        <?php case ('typography'): ?>
            <?php $typo = is_array($value) ? $value : []; ?>
            <div class="space-y-2 rounded-lg border border-ink-200 p-2.5" wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>"
                 x-data="{
                    v: <?php echo \Illuminate\Support\Js::from($typo + ['family' => '', 'size' => '', 'weight' => '', 'lh' => '', 'ls' => '', 'transform' => ''])->toHtml() ?>,
                    push() {
                        const out = {};
                        for (const [k, val] of Object.entries(this.v)) if (val !== '' && val !== null) out[k] = val;
                        $wire.setSetting('<?php echo e($key); ?>', out, <?php echo e($responsive ? 'true' : 'false'); ?>);
                    }
                 }">
                <div class="grid grid-cols-2 gap-2">
                    <label class="block">
                        <span class="mb-1 block text-[11px] text-ink-500">Yazı tipi</span>
                        <select class="field px-1.5 py-1 text-xs" x-model="v.family" @change="push()">
                            <option value="">Tema</option>
                            <option value="heading">Başlık yazı tipi</option>
                            <option value="body">Metin yazı tipi</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-[11px] text-ink-500">Boyut (px)</span>
                        <input type="number" class="field px-1.5 py-1 text-xs" x-model="v.size" @input.debounce.400ms="push()">
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-[11px] text-ink-500">Kalınlık</span>
                        <select class="field px-1.5 py-1 text-xs" x-model="v.weight" @change="push()">
                            <option value="">Varsayılan</option>
                            <option value="300">İnce</option>
                            <option value="400">Normal</option>
                            <option value="500">Orta</option>
                            <option value="600">Yarı kalın</option>
                            <option value="700">Kalın</option>
                            <option value="800">Çok kalın</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-[11px] text-ink-500">Satır yüksekliği</span>
                        <input type="number" step="0.05" class="field px-1.5 py-1 text-xs" x-model="v.lh" @input.debounce.400ms="push()" placeholder="1.4">
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-[11px] text-ink-500">Harf aralığı (em)</span>
                        <input type="number" step="0.01" class="field px-1.5 py-1 text-xs" x-model="v.ls" @input.debounce.400ms="push()" placeholder="0">
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-[11px] text-ink-500">Büyük/küçük</span>
                        <select class="field px-1.5 py-1 text-xs" x-model="v.transform" @change="push()">
                            <option value="">Yazıldığı gibi</option>
                            <option value="uppercase">BÜYÜK HARF</option>
                            <option value="lowercase">küçük harf</option>
                            <option value="capitalize">Baş Harfler</option>
                        </select>
                    </label>
                </div>
            </div>
            <?php break; ?>

        
        <?php case ('background'): ?>
            <?php echo $__env->make('livewire.builder.partials.control-background', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php break; ?>

        <?php case ('border'): ?>
            <?php echo $__env->make('livewire.builder.partials.control-border', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php break; ?>

        <?php case ('shadow'): ?>
            <?php echo $__env->make('livewire.builder.partials.control-shadow', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php break; ?>

        
        <?php case ('media'): ?>
            <?php echo $__env->make('livewire.builder.partials.control-media', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php break; ?>

        <?php case ('gallery'): ?>
            <?php echo $__env->make('livewire.builder.partials.control-gallery', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php break; ?>

        <?php case ('link'): ?>
            <?php echo $__env->make('livewire.builder.partials.control-link', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php break; ?>

        <?php case ('icon'): ?>
            <?php echo $__env->make('livewire.builder.partials.control-icon', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php break; ?>

        <?php case ('repeater'): ?>
            <?php echo $__env->make('livewire.builder.partials.control-repeater', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php break; ?>

        <?php case ('query'): ?>
            <?php echo $__env->make('livewire.builder.partials.control-query', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php break; ?>

        
        <?php case ('form_select'): ?>
        <?php case ('menu_select'): ?>
        <?php case ('template_select'): ?>
            <?php
                $options = match ($type) {
                    'form_select' => \App\Models\Form::orderBy('name')->pluck('name', 'id'),
                    'menu_select' => \App\Models\Menu::orderBy('name')->pluck('name', 'key'),
                    default => \App\Models\Template::where('type', $control['template_type'] ?? 'section')->orderBy('name')->pluck('name', 'id'),
                };
            ?>
            <select id="<?php echo e($id); ?>" class="field" wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>"
                    x-on:change="$wire.setSetting('<?php echo e($key); ?>', $event.target.value || null, false)">
                <option value="">— Seçin —</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionValue => $optionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($optionValue); ?>" <?php if($value == $optionValue): echo 'selected'; endif; ?>><?php echo e($optionLabel); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($options->isEmpty()): ?>
                <p class="hint text-amber-700">Henüz tanımlı bir kayıt yok. Önce ilgili bölümden oluşturun.</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php break; ?>

        
        <?php case ('device_visibility'): ?>
            <?php $hide = is_array($value) ? $value : []; ?>
            <div class="space-y-1.5" wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deviceKey => $deviceLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex items-center gap-2 text-sm text-ink-700">
                        <input type="checkbox" class="size-4 rounded border-ink-300 text-brand-600 focus:ring-brand-500"
                               <?php if($hide[$deviceKey] ?? false): echo 'checked'; endif; ?>
                               x-on:change="$wire.setSetting('<?php echo e($key); ?>', Object.assign(<?php echo \Illuminate\Support\Js::from($hide)->toHtml() ?>, { '<?php echo e($deviceKey); ?>': $event.target.checked }), false)">
                        <?php echo e($deviceLabel); ?> cihazlarda gizle
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php break; ?>

        
        <?php case ('column_width'): ?>
            <div class="flex items-center gap-2" wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>">
                <input type="range" min="10" max="100" step="1" class="flex-1 accent-brand-600"
                       value="<?php echo e(is_numeric($value) ? $value : 100); ?>"
                       x-on:change="$wire.setSetting('<?php echo e($key); ?>', Number($event.target.value), true)">
                <span class="w-12 text-right text-xs text-ink-500"><?php echo e(is_numeric($value) ? round($value) : 100); ?>%</span>
            </div>
            <p class="hint">Aynı bölümdeki sütunların toplamı %100'ü aşarsa alt satıra kayarlar.</p>
            <?php break; ?>

        <?php default: ?>
            <input id="<?php echo e($id); ?>" type="text" class="field" value="<?php echo e(is_scalar($value) ? $value : ''); ?>"
                   wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>"
                   x-on:input.debounce.400ms="$wire.setSetting('<?php echo e($key); ?>', $event.target.value, <?php echo e($responsive ? 'true' : 'false'); ?>)">
    <?php endswitch; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($control['hint']) && $type !== 'color'): ?>
        <p class="hint"><?php echo e($control['hint']); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/livewire/builder/partials/control.blade.php ENDPATH**/ ?>