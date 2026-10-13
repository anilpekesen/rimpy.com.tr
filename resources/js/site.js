/**
 * Ziyaretçi tarafındaki davranışlar.
 *
 * Hiçbir kütüphane yüklenmez; toplam boyut birkaç kilobayttır. Her parça
 * kendi işaretlemesini sayfada arar, bulamazsa hiçbir şey yapmaz — bir
 * widget kullanılmadığında maliyeti sıfırdır.
 */

// ── Giriş animasyonları ─────────────────────────────────────────────────────
// Ekrana giren öğeler yumuşakça belirir. Hareketi azaltma tercihi olan
// ziyaretçilerde animasyon hiç başlatılmaz.
(() => {
    const targets = document.querySelectorAll('.anim');
    if (!targets.length) return;

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        targets.forEach((el) => el.classList.add('is-in'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-in');
                observer.unobserve(entry.target);
            }
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.05 });

    targets.forEach((el) => observer.observe(el));
})();

// ── Sayaçlar ────────────────────────────────────────────────────────────────
(() => {
    const counters = document.querySelectorAll('[data-counter]');
    if (!counters.length) return;

    const format = (value, separated) =>
        separated ? Math.round(value).toLocaleString('tr-TR') : String(Math.round(value));

    const run = (el) => {
        const target = Number(el.dataset.counter) || 0;
        const duration = Number(el.dataset.counterDuration) || 2000;
        const separated = el.dataset.counterSeparator === '1';
        const output = el.querySelector('[data-counter-number]');
        if (!output) return;

        const start = performance.now();

        const step = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            // Sona doğru yavaşlayan bir eğri; sayı "yerine oturmuş" hissi verir.
            const eased = 1 - Math.pow(1 - progress, 3);
            output.textContent = format(target * eased, separated);
            if (progress < 1) requestAnimationFrame(step);
        };

        requestAnimationFrame(step);
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                run(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.4 });

    counters.forEach((el) => observer.observe(el));
})();

// ── Sekmeler ────────────────────────────────────────────────────────────────
// Klavyeyle de gezilebilir: ok tuşları sekmeler arasında dolaşır.
document.querySelectorAll('[data-tabs]').forEach((container) => {
    const buttons = [...container.querySelectorAll('[role="tab"]')];
    const panels = [...container.querySelectorAll('[role="tabpanel"]')];

    const activate = (index) => {
        buttons.forEach((button, i) => {
            const selected = i === index;
            button.setAttribute('aria-selected', selected ? 'true' : 'false');
            button.tabIndex = selected ? 0 : -1;
            panels[i]?.toggleAttribute('hidden', !selected);
        });
        buttons[index]?.focus();
    };

    buttons.forEach((button, index) => {
        button.addEventListener('click', () => activate(index));
        button.addEventListener('keydown', (event) => {
            const step = { ArrowRight: 1, ArrowDown: 1, ArrowLeft: -1, ArrowUp: -1 }[event.key];
            if (!step) return;
            event.preventDefault();
            activate((index + step + buttons.length) % buttons.length);
        });
    });
});

// ── Slider ──────────────────────────────────────────────────────────────────
document.querySelectorAll('[data-slider]').forEach((slider) => {
    const slides = [...slider.querySelectorAll('.slide')];
    if (slides.length < 2) return;

    const dots = [...slider.querySelectorAll('[data-slider-dot]')];
    const interval = Number(slider.dataset.interval) || 6000;
    let index = 0;
    let timer = null;

    const show = (next) => {
        index = (next + slides.length) % slides.length;

        slides.forEach((slide, i) => {
            const active = i === index;
            slide.classList.toggle('is-active', active);
            slide.toggleAttribute('aria-hidden', !active);
        });

        dots.forEach((dot, i) => dot.setAttribute('aria-selected', i === index ? 'true' : 'false'));
    };

    const start = () => {
        if (slider.dataset.autoplay !== '1') return;
        stop();
        timer = setInterval(() => show(index + 1), interval);
    };

    const stop = () => timer && clearInterval(timer);

    slider.querySelector('[data-slider-next]')?.addEventListener('click', () => { show(index + 1); start(); });
    slider.querySelector('[data-slider-prev]')?.addEventListener('click', () => { show(index - 1); start(); });
    dots.forEach((dot, i) => dot.addEventListener('click', () => { show(i); start(); }));

    // Fareyle üzerindeyken ya da sekme arka plandayken dönmesin.
    slider.addEventListener('mouseenter', stop);
    slider.addEventListener('mouseleave', start);
    slider.addEventListener('focusin', stop);
    document.addEventListener('visibilitychange', () => (document.hidden ? stop() : start()));

    start();
});

// ── Mobil menü ──────────────────────────────────────────────────────────────
document.querySelectorAll('[data-menu]').forEach((nav) => {
    const toggle = nav.querySelector('[data-menu-toggle]');
    const list = nav.querySelector('[data-menu-list]');
    if (!toggle || !list) return;

    const close = () => {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    };

    toggle.addEventListener('click', () => {
        const open = !nav.classList.contains('is-open');
        nav.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', String(open));
        // Panel açıkken arkadaki sayfanın kaymasını engelle.
        document.body.style.overflow = open ? 'hidden' : '';
    });

    list.addEventListener('click', (event) => event.target.closest('a') && close());
    document.addEventListener('keydown', (event) => event.key === 'Escape' && close());
});

// ── Görsel büyütme ──────────────────────────────────────────────────────────
(() => {
    const links = document.querySelectorAll('[data-lightbox]');
    if (!links.length) return;

    let overlay = null;

    const close = () => {
        overlay?.remove();
        overlay = null;
        document.body.style.overflow = '';
    };

    const open = (href, alt) => {
        overlay = document.createElement('div');
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.setAttribute('aria-label', alt || 'Büyütülmüş görsel');
        overlay.style.cssText =
            'position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;' +
            'background:rgba(6,8,15,.92);padding:2rem;cursor:zoom-out';

        const image = document.createElement('img');
        image.src = href;
        image.alt = alt || '';
        image.style.cssText = 'max-width:100%;max-height:100%;object-fit:contain;border-radius:6px';

        overlay.append(image);
        overlay.addEventListener('click', close);
        document.body.append(overlay);
        document.body.style.overflow = 'hidden';
        overlay.focus();
    };

    links.forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            open(link.getAttribute('href'), link.querySelector('img')?.alt);
        });
    });

    document.addEventListener('keydown', (event) => event.key === 'Escape' && close());
})();

// ── Form gönderimi ──────────────────────────────────────────────────────────
// Çift gönderimi engeller: buton bir kez basıldıktan sonra kilitlenir.
document.querySelectorAll('[data-form] form').forEach((form) => {
    form.addEventListener('submit', () => {
        const button = form.querySelector('[type="submit"]');
        if (!button) return;

        button.disabled = true;
        button.dataset.originalText = button.textContent;
        button.textContent = 'Gönderiliyor…';

        // Doğrulama hatasıyla geri dönülürse buton kilitli kalmasın.
        setTimeout(() => {
            button.disabled = false;
            button.textContent = button.dataset.originalText;
        }, 8000);
    });
});

// ── Teklif sepeti ───────────────────────────────────────────────────────────
// Sunucu tarafı `QuoteCart`; burası yalnızca arayüz. İstekler arka planda
// gider, sepet sayfa yenilenmeden güncellenir. Betik yüklenmezse aynı
// formlar normal gönderilir ve her şey yine çalışır.
(() => {
    const root = document.querySelector('[data-quote]');
    if (!root) return;

    const panel = root.querySelector('.qc-panel');
    const list = root.querySelector('[data-quote-lines]');
    const empty = root.querySelector('[data-quote-empty]');
    const toast = document.querySelector('[data-quote-toast]');
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    const text = root.dataset;
    let lastFocus = null;
    let toastTimer = null;

    const request = async (url, body) => {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            body,
            credentials: 'same-origin',
        });
        if (!response.ok) throw response;
        return response.json();
    };

    const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    const render = (state) => {
        document.querySelectorAll('[data-quote-count]').forEach((badge) => {
            badge.textContent = state.count;
            badge.classList.remove('is-bump');
            void badge.offsetWidth;
            badge.classList.add('is-bump');
        });

        list.innerHTML = state.lines.map((line) => `
            <li class="qc-line" data-id="${line.id}">
                <a class="qc-thumb" href="${esc(line.url)}">${line.image ? `<img src="${esc(line.image)}" alt="" loading="lazy">` : ''}</a>
                <div class="qc-info">
                    <a class="qc-name" href="${esc(line.url)}">${esc(line.title)}</a>
                    ${line.code ? `<span class="qc-code">${esc(line.code)}</span>` : ''}
                    <button type="button" class="qc-remove" data-quote-remove="${line.id}">${esc(text.textRemove)}</button>
                </div>
                <div class="qc-qty">
                    <button type="button" data-quote-step="-1" aria-label="${esc(text.textLess)}">−</button>
                    <span aria-live="polite">${line.quantity}</span>
                    <button type="button" data-quote-step="1" aria-label="${esc(text.textMore)}">+</button>
                </div>
            </li>`).join('');

        empty.hidden = state.lines.length > 0;

        // Ürün kartlarındaki düğmeler sepette olduklarını göstersin.
        const inCart = new Map(state.lines.map((line) => [String(line.id), line.quantity]));
        document.querySelectorAll('[data-quote-item]').forEach((button) => {
            const quantity = inCart.get(button.dataset.quoteItem);
            button.classList.toggle('is-in', Boolean(quantity));
            const label = button.querySelector('[data-quote-label]');
            if (!label) return;
            label.dataset.original ??= label.textContent;
            label.textContent = quantity ? `${text.textInCart} · ${quantity}` : label.dataset.original;
        });
    };

    const open = () => {
        lastFocus = document.activeElement;
        root.hidden = false;
        requestAnimationFrame(() => root.classList.add('is-open'));
        document.documentElement.classList.add('qc-lock');
        hideToast();
        setTimeout(() => panel.querySelector('input:not([type=hidden]), button')?.focus({ preventScroll: true }), 250);
    };

    const close = () => {
        root.classList.remove('is-open');
        document.documentElement.classList.remove('qc-lock');
        setTimeout(() => { root.hidden = true; }, 350);
        lastFocus?.focus?.({ preventScroll: true });
    };

    const showToast = (message) => {
        if (!toast) return;
        toast.querySelector('[data-quote-toast-text]').textContent = message;
        toast.hidden = false;
        requestAnimationFrame(() => toast.classList.add('is-on'));
        clearTimeout(toastTimer);
        toastTimer = setTimeout(hideToast, 3200);
    };

    function hideToast() {
        if (!toast) return;
        toast.classList.remove('is-on');
        setTimeout(() => { toast.hidden = true; }, 300);
    }

    const update = (id, quantity) => {
        const body = new FormData();
        body.append('item', id);
        body.append('quantity', quantity);
        body.append('locale', text.locale);
        return request(text.endpointUpdate, body).then(render);
    };

    document.addEventListener('click', (event) => {
        // Kurucudaki sıradan bir buton da sepeti açabilsin: bağlantısı "#teklif" olması yeter.
        if (event.target.closest('[data-quote-open], a[href="#teklif"]')) { event.preventDefault(); open(); return; }
        if (event.target.closest('[data-quote-close]')) { close(); return; }

        const remove = event.target.closest('[data-quote-remove]');
        if (remove) {
            const body = new FormData();
            body.append('item', remove.dataset.quoteRemove);
            body.append('locale', text.locale);
            request(text.endpointRemove, body).then(render);
            return;
        }

        const step = event.target.closest('[data-quote-step]');
        if (step) {
            const line = step.closest('.qc-line');
            const current = Number(line.querySelector('.qc-qty span').textContent) || 1;
            update(line.dataset.id, Math.max(0, current + Number(step.dataset.quoteStep)));
        }
    });

    document.addEventListener('keydown', (event) => {
        if (root.hidden) return;
        if (event.key === 'Escape') close();

        // Odak panelin içinde dolaşsın.
        if (event.key === 'Tab') {
            const focusable = [...panel.querySelectorAll('a[href], button:not([disabled]), input:not([type=hidden]), select, textarea')]
                .filter((el) => el.offsetParent !== null);
            if (!focusable.length) return;
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
            else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        }
    });

    // Başka betikler (ör. sistem kurucu) sepeti bu olayla günceller:
    // document.dispatchEvent(new CustomEvent('quote:update', { detail: { state, message } }))
    document.addEventListener('quote:update', (event) => {
        if (event.detail?.state) render(event.detail.state);
        if (event.detail?.message) showToast(event.detail.message);
    });

    // Sepete ekleme
    document.addEventListener('submit', (event) => {
        const form = event.target.closest('[data-quote-add]');
        if (!form) return;
        event.preventDefault();

        const button = form.querySelector('button');
        button.disabled = true;

        request(form.action, new FormData(form))
            .then((state) => {
                render(state);
                const title = form.closest('[data-quote-title]')?.dataset.quoteTitle;
                showToast(title ? `${title} ${text.textAdded}` : text.textAdded);
            })
            .catch(() => form.submit())
            .finally(() => { button.disabled = false; });
    });

    // Teklif formu panelin içinden, sayfadan ayrılmadan gönderilir.
    const quoteForm = root.querySelector('[data-quote-form] form');
    quoteForm?.addEventListener('submit', (event) => {
        event.preventDefault();

        const button = quoteForm.querySelector('[type="submit"]');
        quoteForm.querySelectorAll('.frm-error').forEach((el) => el.remove());

        request(quoteForm.action, new FormData(quoteForm))
            .then((result) => {
                quoteForm.reset();
                render({ count: 0, lines: [] });
                const wrap = root.querySelector('[data-quote-form]');
                wrap.querySelector('.frm-success')?.remove();
                wrap.insertAdjacentHTML('beforeend', `<p class="frm-success" role="status">${esc(result.message || '')}</p>`);
            })
            .catch(async (response) => {
                if (response?.status !== 422) { quoteForm.submit(); return; }
                const { errors = {} } = await response.json();
                Object.entries(errors).forEach(([name, messages]) => {
                    const field = quoteForm.querySelector(`[name="${CSS.escape(name)}"]`)?.closest('.frm-field');
                    field?.insertAdjacentHTML('beforeend', `<p class="frm-error" role="alert">${esc(messages[0])}</p>`);
                });
                quoteForm.querySelector('.frm-error')?.closest('.frm-field')?.querySelector('.frm-input')?.focus();
            })
            .finally(() => {
                if (!button) return;
                button.disabled = false;
                if (button.dataset.originalText) button.textContent = button.dataset.originalText;
            });
    });

    // Betiksiz gönderimden dönüldüyse ya da ?teklif adresiyle gelindiyse sepet açık başlasın.
    if (new URLSearchParams(location.search).has('teklif') || root.querySelector('.frm-error, .frm-success')) open();
})();

