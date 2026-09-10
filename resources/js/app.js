/**
 * Panelin tarayıcı tarafı.
 *
 * Livewire sunucuyla konuşmayı üstlendiği için burada yalnızca sürükle-bırak
 * ve tuval etkileşimi gibi gerçekten tarayıcıda yaşaması gereken şeyler var.
 */

import Sortable from 'sortablejs';

window.Sortable = Sortable;

function collectMenuPositions(root) {
    const positions = [];

    root.querySelectorAll('[data-menu-sortable]').forEach((list) => {
        const parentId = list.dataset.parentId ? Number(list.dataset.parentId) : null;
        const directItems = Array.from(list.children).filter((child) => child.matches('[data-menu-item]'));

        directItems.forEach((item, sort) => positions.push({
            id: Number(item.dataset.menuItem),
            parent_id: parentId,
            sort,
        }));
    });

    return positions;
}

window.initMenuSortables = (scope = document) => {
    scope.querySelectorAll('[data-menu-sortable]').forEach((list) => {
        if (list._mimarSortable) return;

        const root = list.closest('[data-menu-builder]');
        if (!root) return;

        list._mimarSortable = Sortable.create(list, {
            group: 'mimar-menu-tree',
            animation: 180,
            easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
            handle: '[data-menu-handle]',
            draggable: ':scope > [data-menu-item]',
            fallbackOnBody: true,
            swapThreshold: 0.65,
            ghostClass: 'menu-item-ghost',
            chosenClass: 'menu-item-chosen',
            dragClass: 'menu-item-dragging',
            onEnd() {
                const componentRoot = root.closest('[wire\\:id]');
                const component = componentRoot && window.Livewire?.find(componentRoot.getAttribute('wire:id'));
                component?.call('reorderItems', collectMenuPositions(root));
            },
        });
    });
};

document.addEventListener('DOMContentLoaded', () => window.initMenuSortables());
document.addEventListener('livewire:navigated', () => window.initMenuSortables());
document.addEventListener('livewire:init', () => {
    window.Livewire.hook('morph.updated', ({ el }) => window.initMenuSortables(el));
});

// Ortak medya seçici ayrı bir Livewire bileşenidir. Seçim tamamlandığında
// sonucu, seçiciyi açan gerçek denetime geri yollarız; böylece her ekran
// kendi alanını nasıl güncelleyeceğine kendisi karar verir.
let mediaPickerTrigger = null;

window.addEventListener('open-media-picker', (event) => {
    mediaPickerTrigger = event.target instanceof Element ? event.target : null;
});

window.addEventListener('media-selected', (event) => {
    if (!mediaPickerTrigger?.isConnected) return;

    mediaPickerTrigger.dispatchEvent(new CustomEvent('media-applied', {
        detail: event.detail,
        bubbles: true,
    }));

    mediaPickerTrigger = null;
});

// Kısayollar: Cmd/Ctrl+S kaydeder, Escape açık paneli kapatır.
document.addEventListener('keydown', (event) => {
    const meta = event.metaKey || event.ctrlKey;

    if (meta && event.key === 's') {
        event.preventDefault();
        window.Livewire?.dispatch('shortcut-save');
    }

    if (meta && event.key === 'z' && !event.shiftKey) {
        if (document.querySelector('[data-builder-canvas]')) {
            event.preventDefault();
            window.Livewire?.dispatch('shortcut-undo');
        }
    }

    if (meta && (event.key === 'y' || (event.key === 'z' && event.shiftKey))) {
        if (document.querySelector('[data-builder-canvas]')) {
            event.preventDefault();
            window.Livewire?.dispatch('shortcut-redo');
        }
    }
});

// Kaydedilmemiş değişiklik varken sekmeyi kapatmaya çalışan kullanıcıyı uyar.
let dirty = false;

window.addEventListener('builder-dirty', () => { dirty = true; });
window.addEventListener('builder-clean', () => { dirty = false; });
window.addEventListener('beforeunload', (event) => {
    if (dirty) {
        event.preventDefault();
        event.returnValue = '';
    }
});
