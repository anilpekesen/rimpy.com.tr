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
