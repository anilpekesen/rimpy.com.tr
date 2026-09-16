{{--
    Kurucunun önizleme çerçevesi (bkz. BuilderPreviewController).

    Sayfa ziyaretçinin gördüğü düzende çizilir. Buradaki betik yalnızca
    düzenleyiciyle konuşur: tıklanan öğeyi bildirir, seçili öğeyi işaretler,
    bağlantıların ve formların çalışmasını engeller.
--}}
<x-site-layout :locale="$locale" :subject="$subject" :noindex="true" :preview="true" :chrome="$chrome">

    @push('styles')
        <style>{!! $css !!}</style>
        <style>
            [data-el].pv-hover { outline: 2px dashed #2563eb; outline-offset: -2px; cursor: pointer; }
            [data-el].pv-selected { outline: 3px solid #2563eb; outline-offset: -3px; }
            .pv-tag {
                position: fixed; z-index: 2147483000; pointer-events: none;
                padding: 4px 8px; border-radius: 4px 4px 0 0;
                background: #2563eb; color: #fff;
                font: 700 12px/1 system-ui, sans-serif; white-space: nowrap;
                transform: translateY(-100%);
            }
            .pv-tag[hidden] { display: none; }
            .pv-tag--selected { background: #fff; color: #2563eb; box-shadow: inset 0 0 0 2px #2563eb; }
        </style>
    @endpush

    {!! $html !!}

    @push('scripts')
        <script>
        (() => {
            const origin = location.origin;
            const send = (message) => parent.postMessage({ source: 'builder-preview', ...message }, origin);

            const hoverTag = Object.assign(document.createElement('div'), { className: 'pv-tag', hidden: true });
            const selectedTag = Object.assign(document.createElement('div'), { className: 'pv-tag pv-tag--selected', hidden: true });
            document.body.append(hoverTag, selectedTag);

            let hovered = null;
            let selected = null;

            // Tek sütunlu bölümde sütun, bölümle aynı alanı kaplar; oraya
            // tıklayan kişi bölümü kastediyordur.
            const target = (node) => {
                let el = node instanceof Element ? node.closest('[data-el]') : null;

                if (el?.dataset.elType === 'column' && el.parentElement?.children.length === 1) {
                    el = el.closest('[data-el-type="section"]') ?? el;
                }

                return el;
            };

            const place = (tag, el) => {
                if (!el || !el.isConnected) { tag.hidden = true; return; }
                const box = el.getBoundingClientRect();
                tag.textContent = el.dataset.elLabel || '';
                tag.style.left = Math.max(0, box.left) + 'px';
                tag.style.top = Math.max(tag.offsetHeight || 20, box.top) + 'px';
                tag.hidden = box.bottom < 0 || box.top > innerHeight;
            };

            const refresh = () => {
                place(hoverTag, hovered !== selected ? hovered : null);
                place(selectedTag, selected);
            };

            document.addEventListener('mouseover', (event) => {
                const el = target(event.target);
                if (el === hovered) return;
                hovered?.classList.remove('pv-hover');
                hovered = el;
                hovered?.classList.add('pv-hover');
                refresh();
            });

            document.addEventListener('mouseleave', () => {
                hovered?.classList.remove('pv-hover');
                hovered = null;
                refresh();
            });

            // Önizlemede tıklama yalnızca seçer: bağlantı açılmaz, form gönderilmez.
            document.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                const el = target(event.target);
                if (el) send({ type: 'select', id: el.dataset.el });
            }, true);

            document.addEventListener('submit', (event) => event.preventDefault(), true);

            const mark = (id, scroll) => {
                selected?.classList.remove('pv-selected');
                selected = id ? document.querySelector(`[data-el="${CSS.escape(id)}"]`) : null;
                selected?.classList.add('pv-selected');

                if (selected && scroll) {
                    const box = selected.getBoundingClientRect();
                    if (box.top < 60 || box.top > innerHeight * 0.6) {
                        scrollTo({ top: scrollY + box.top - 120, behavior: 'smooth' });
                    }
                }

                refresh();
            };

            addEventListener('message', (event) => {
                if (event.origin !== origin || event.data?.source !== 'builder-editor') return;
                if (event.data.type === 'select') mark(event.data.id, event.data.scroll);
                if (event.data.type === 'scroll') scrollTo({ top: event.data.y, behavior: 'instant' });
            });

            let ticking = false;
            addEventListener('scroll', () => {
                if (ticking) return;
                ticking = true;
                requestAnimationFrame(() => { ticking = false; refresh(); send({ type: 'scroll', y: scrollY }); });
            }, { passive: true });
            addEventListener('resize', refresh);

            // Kaydet / geri al kısayolları çerçevenin içindeyken de çalışsın.
            addEventListener('keydown', (event) => {
                const meta = event.metaKey || event.ctrlKey;
                if (!meta || !['s', 'z', 'y'].includes(event.key.toLowerCase())) return;
                event.preventDefault();
                send({ type: 'key', key: event.key.toLowerCase(), shift: event.shiftKey });
            });
        })();
        </script>
    @endpush
</x-site-layout>
