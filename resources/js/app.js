/**
 * Panelin tarayıcı tarafı.
 *
 * Livewire sunucuyla konuşmayı üstlendiği için burada yalnızca sürükle-bırak
 * ve tuval etkileşimi gibi gerçekten tarayıcıda yaşaması gereken şeyler var.
 */

import Sortable from 'sortablejs';

window.Sortable = Sortable;

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
