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
            draggable: '[data-menu-item]',
            fallbackOnBody: true,
            fallbackTolerance: 3,
            emptyInsertThreshold: 28,
            swapThreshold: 0.65,
            ghostClass: 'menu-item-ghost',
            chosenClass: 'menu-item-chosen',
            dragClass: 'menu-item-dragging',
            onStart() {
                root.classList.add('is-menu-dragging');
            },
            onEnd() {
                root.classList.remove('is-menu-dragging');
                const componentRoot = root.closest('[wire\\:id]');
                const component = componentRoot && window.Livewire?.find(componentRoot.getAttribute('wire:id'));
                component?.call('reorderItems', collectMenuPositions(root));
            },
        });
    });
};

window.initSlideSortables = (scope = document) => {
    scope.querySelectorAll('[data-slide-sortable]').forEach((list) => {
        if (list._mimarSortable) return;

        const root = list.closest('[data-slider-manager]');
        if (!root) return;

        list._mimarSortable = Sortable.create(list, {
            animation: 180,
            easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
            handle: '[data-slide-handle]',
            draggable: '[data-slide-item]',
            ghostClass: 'menu-item-ghost',
            chosenClass: 'menu-item-chosen',
            onEnd() {
                const componentRoot = root.closest('[wire\\:id]');
                const component = componentRoot && window.Livewire?.find(componentRoot.getAttribute('wire:id'));
                const ids = Array.from(list.children)
                    .filter((child) => child.matches('[data-slide-item]'))
                    .map((child) => Number(child.dataset.slideItem));
                component?.call('reorderSlides', ids);
            },
        });
    });
};

function initPanelSortables(scope = document) {
    window.initMenuSortables(scope);
    window.initSlideSortables(scope);
}

document.addEventListener('DOMContentLoaded', () => initPanelSortables());
document.addEventListener('livewire:navigated', () => initPanelSortables());
document.addEventListener('livewire:init', () => {
    window.Livewire.hook('morph.updated', ({ el }) => {
        window.requestAnimationFrame(() => initPanelSortables(el));
    });
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

// ── Kurucu önizlemesi ───────────────────────────────────────────────────────
// Sayfa ayrı bir çerçevede sitenin kendisiyle çizilir (BuilderPreviewController).
// Ağaç değiştikçe sunucu yeni bir adres verir; yeni sürüm arkadaki çerçevede
// yüklenir ve hazır olunca öne alınır, kaydırma konumu korunur. Çerçeveyle
// konuşma postMessage üzerinden, yalnızca aynı kökenle yapılır.
document.addEventListener('livewire:init', () => {
    window.Alpine.data('builderPreview', () => ({
        url: null,
        selected: null,
        pending: null,
        loading: false,
        front: 'a',
        y: 0,

        init() {
            // Livewire her yanıtta bu işaretin niteliklerini günceller.
            const sync = () => {
                const probe = this.$root.querySelector('[x-ref="probe"]');
                const url = probe?.dataset.url;
                const selected = probe?.dataset.selected || null;

                if (url && url !== this.url && url !== this.pending) this.load(url);

                if (selected !== this.selected) {
                    this.selected = selected;
                    this.post({ type: 'select', id: selected, scroll: true });
                }
            };

            new MutationObserver(sync).observe(this.$root, { attributes: true, subtree: true, attributeFilter: ['data-url', 'data-selected'] });

            window.addEventListener('message', (event) => {
                if (event.origin !== location.origin || event.data?.source !== 'builder-preview') return;

                const fromFront = event.source === this.$refs[this.front].contentWindow;
                const { type } = event.data;

                if (type === 'select') this.$wire.selectElement(event.data.id);
                if (type === 'scroll' && fromFront) this.y = event.data.y;
                if (type === 'key') {
                    const name = event.data.key === 's' ? 'shortcut-save'
                        : (event.data.key === 'y' || event.data.shift) ? 'shortcut-redo' : 'shortcut-undo';
                    window.Livewire.dispatch(name);
                }
            });

            sync();
        },

        post(message) {
            this.$refs[this.front].contentWindow?.postMessage({ source: 'builder-editor', ...message }, location.origin);
        },

        load(url) {
            if (this.loading) {
                this.pending = url;
                return;
            }

            this.loading = true;
            this.url = url;

            const back = this.front === 'a' ? 'b' : 'a';
            const frame = this.$refs[back];

            frame.onload = () => {
                frame.onload = null;
                const win = frame.contentWindow;

                win.scrollTo({ top: this.y, behavior: 'instant' });
                win.postMessage({ source: 'builder-editor', type: 'select', id: this.selected, scroll: false }, location.origin);

                // Yazı tipleri ve görseller yerine otursun, sonra öne al.
                setTimeout(() => {
                    this.$refs[this.front].classList.add('invisible');
                    this.$refs[this.front].setAttribute('aria-hidden', 'true');
                    frame.classList.remove('invisible');
                    frame.removeAttribute('aria-hidden');
                    this.front = back;
                    this.loading = false;

                    if (this.pending && this.pending !== this.url) {
                        const next = this.pending;
                        this.pending = null;
                        this.load(next);
                    } else {
                        this.pending = null;
                    }
                }, 60);
            };

            frame.src = url;
        },
    }));
});
