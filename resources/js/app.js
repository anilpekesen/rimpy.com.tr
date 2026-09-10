/**
 * Panelin tarayıcı tarafı.
 *
 * Livewire sunucuyla konuşmayı üstlendiği için burada yalnızca sürükle-bırak
 * ve tuval etkileşimi gibi gerçekten tarayıcıda yaşaması gereken şeyler var.
 */

import Sortable from 'sortablejs';

window.Sortable = Sortable;

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
