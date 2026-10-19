/**
 * Rimpy'ye özel ön yüz davranışları. Mimar'ın ortak betiği site.js'tedir;
 * burası yalnızca bu kurulumun widget'ları için.
 */

// ── Ürün galerisi ───────────────────────────────────────────────────────────
document.querySelectorAll('[data-pd-gallery]').forEach((gallery) => {
    const main = gallery.querySelector('[data-pd-main]');
    if (!main) return;

    gallery.addEventListener('click', (event) => {
        const thumb = event.target.closest('[data-pd-thumb]');
        if (!thumb) return;
        main.removeAttribute('srcset');
        main.src = thumb.dataset.pdThumb;
        gallery.querySelectorAll('[data-pd-thumb]').forEach((t) => t.classList.toggle('is-active', t === thumb));
    });
});

// ── Ürün sekmeleri ──────────────────────────────────────────────────────────
// Betik yoksa bölümler alt alta durur; varsa yalnızca seçili sekme görünür.
document.querySelectorAll('[data-pd-tabs]').forEach((root) => {
    const tabs = [...root.querySelectorAll('[role="tab"]')];
    const select = (tab, focus = false) => {
        tabs.forEach((t) => {
            const on = t === tab;
            t.classList.toggle('is-active', on);
            t.setAttribute('aria-selected', String(on));
            t.tabIndex = on ? 0 : -1;
            document.getElementById(t.getAttribute('aria-controls')).hidden = !on;
        });
        if (focus) tab.focus();
    };

    root.classList.add('is-ready');
    select(tabs[0]);
    tabs.forEach((tab, i) => {
        tab.addEventListener('click', () => select(tab));
        tab.addEventListener('keydown', (event) => {
            const step = { ArrowRight: 1, ArrowLeft: -1 }[event.key];
            if (step) select(tabs[(i + step + tabs.length) % tabs.length], true);
        });
    });
});

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// ── Açılış: tork göstergesi ─────────────────────────────────────────────────
// Açılışta ibre dinlenme değerine kadar dolar; sayfa kaydırıldıkça sona
// doğru "sıkılır", somun döner ve basınç okuması artar.
document.querySelectorAll('[data-hero-gauge]').forEach((hero) => {
    const svg = hero.querySelector('.hg-gauge svg');
    if (!svg) return;

    const C = 320, R = 270, START = 225, SWEEP = 270;
    const max = Number(hero.dataset.max) || 12000;
    const pressure = Number(hero.dataset.pressure) || 700;
    const rest = Math.min(1, Math.max(0.05, Number(hero.dataset.rest) || 0.62));
    const arc = svg.querySelector('[data-arc]');
    const nut = svg.querySelector('[data-nut]');
    const value = hero.querySelector('[data-value]');
    const bar = hero.querySelector('[data-bar]');
    const box = hero.querySelector('.hg-gauge');
    const number = new Intl.NumberFormat(document.documentElement.lang || 'tr');

    const point = (angle, radius = R) => {
        const t = (angle - 90) * Math.PI / 180;
        return [C + radius * Math.cos(t), C + radius * Math.sin(t)];
    };
    const path = (a0, a1) => {
        const [x0, y0] = point(a0), [x1, y1] = point(a1);
        return `M${x0} ${y0} A${R} ${R} 0 ${a1 - a0 > 180 ? 1 : 0} 1 ${x1} ${y1}`;
    };

    svg.querySelector('[data-track]').setAttribute('d', path(START, START + SWEEP));

    let ticks = '';
    for (let i = 0; i <= 60; i++) {
        const angle = START + SWEEP * i / 60, major = i % 10 === 0;
        const [x0, y0] = point(angle, R + 22), [x1, y1] = point(angle, R + (major ? 40 : 30));
        ticks += `<line x1="${x0}" y1="${y0}" x2="${x1}" y2="${y1}" stroke="rgba(255,255,255,${major ? .5 : .15})" stroke-width="${major ? 2 : 1}"/>`;
        if (major) {
            const [lx, ly] = point(angle, R + 58);
            const label = max >= 1000 ? `${Math.round(max * i / 60 / 100) / 10}k` : Math.round(max * i / 60);
            ticks += `<text x="${lx}" y="${ly}" fill="#6b7079" font-family="JetBrains Mono, monospace" font-size="12" text-anchor="middle" dominant-baseline="middle">${label}</text>`;
        }
    }
    svg.querySelector('[data-ticks]').innerHTML = ticks;

    const draw = (v) => {
        arc.setAttribute('d', path(START, START + Math.max(0.001, SWEEP * v)));
        value.textContent = number.format(Math.round(v * max));
        if (bar) bar.textContent = Math.round(v * pressure);
        nut.style.transform = `rotate(${v * 90}deg)`;
    };

    if (reduceMotion) { draw(rest); return; }

    let target = rest, current = 0, mx = 0, my = 0, running = false;

    const loop = () => {
        current += (target - current) * 0.08;
        draw(current);
        box.style.transform = `perspective(1200px) rotateY(${mx * 6}deg) rotateX(${-my * 6}deg)`;
        if (Math.abs(target - current) > 0.0005 || running) requestAnimationFrame(loop);
    };

    // Açılış dolumu
    let t0 = null;
    const intro = (time) => {
        t0 ??= time;
        const p = Math.min(1, (time - t0) / 1800);
        current = (1 - Math.pow(1 - p, 4)) * rest;
        draw(current);
        if (p < 1) requestAnimationFrame(intro);
        else { running = true; loop(); }
    };
    requestAnimationFrame(intro);

    // Görünmüyorken döngü durur; pil ve işlemci boşa harcanmaz.
    new IntersectionObserver(([entry]) => {
        const was = running;
        running = entry.isIntersecting && current >= rest * 0.99;
        if (running && !was) loop();
    }).observe(hero);

    addEventListener('scroll', () => {
        const r = box.getBoundingClientRect();
        target = Math.min(1, Math.max(rest, rest + (-r.top + 60) / (r.height * 0.9) * (1 - rest)));
    }, { passive: true });

    addEventListener('pointermove', (event) => {
        mx = event.clientX / innerWidth - 0.5;
        my = event.clientY / innerHeight - 0.5;
    }, { passive: true });
});

// ── Hizmet şeridi ───────────────────────────────────────────────────────────
// Bölüm ekrana sabitlenir; dikey kaydırma şeridi yatayda ilerletir.
document.querySelectorAll('[data-service-rail]').forEach((rail) => {
    const track = rail.querySelector('[data-track]');
    const progress = rail.querySelector('[data-progress]');
    const wide = window.matchMedia('(min-width: 641px)');

    const update = () => {
        if (!wide.matches) { track.style.transform = ''; return; }
        const r = rail.getBoundingClientRect();
        const total = rail.offsetHeight - innerHeight;
        const p = Math.min(1, Math.max(0, -r.top / Math.max(1, total)));
        const distance = Math.max(0, track.scrollWidth - innerWidth);
        track.style.transform = `translate3d(${-p * distance}px,0,0)`;
        if (progress) progress.style.transform = `scaleX(${p})`;
    };

    addEventListener('scroll', update, { passive: true });
    addEventListener('resize', update);
    update();
});

// ── Sektör sekmeleri ────────────────────────────────────────────────────────
document.querySelectorAll('[data-industry-tabs]').forEach((widget) => {
    const tabs = [...widget.querySelectorAll('[role="tab"]')];
    const hover = window.matchMedia('(hover: hover)').matches;

    const select = (tab, focus = false) => {
        tabs.forEach((t) => {
            const on = t === tab;
            t.setAttribute('aria-selected', on);
            t.tabIndex = on ? 0 : -1;
            document.getElementById(t.getAttribute('aria-controls')).hidden = !on;
        });
        if (focus) tab.focus();
    };

    tabs.forEach((tab, i) => {
        tab.addEventListener('click', () => select(tab));
        if (hover) tab.addEventListener('mouseenter', () => select(tab));
        tab.addEventListener('keydown', (event) => {
            const step = { ArrowDown: 1, ArrowRight: 1, ArrowUp: -1, ArrowLeft: -1 }[event.key];
            if (step) { event.preventDefault(); select(tabs[(i + step + tabs.length) % tabs.length], true); }
        });
    });
});

// ── Ürün kartları süzgeci ───────────────────────────────────────────────────
document.querySelectorAll('[data-product-grid]').forEach((grid) => {
    const chips = grid.querySelectorAll('[data-filter]');
    chips.forEach((chip) => chip.addEventListener('click', () => {
        chips.forEach((c) => c.classList.toggle('is-active', c === chip));
        grid.querySelectorAll('.pc[data-terms]').forEach((card) => {
            const terms = card.dataset.terms.split(',');
            card.classList.toggle('is-hidden', Boolean(chip.dataset.filter) && !terms.includes(chip.dataset.filter));
        });
    }));
});

// ── Ekrana girince ──────────────────────────────────────────────────────────
(() => {
    const targets = document.querySelectorAll('[data-reveal]');
    if (!targets.length) return;
    if (reduceMotion) { targets.forEach((el) => el.classList.add('is-in')); return; }
    const io = new IntersectionObserver((entries) => entries.forEach((entry) => {
        if (entry.isIntersecting) { entry.target.classList.add('is-in'); io.unobserve(entry.target); }
    }), { threshold: 0.25 });
    targets.forEach((el) => io.observe(el));
})();

// ── Mühendislik araçları: ortak ─────────────────────────────────────────────
const toolData = (root) => JSON.parse(root.querySelector('[data-tool-data]').textContent);
const fmt = (value, digits = 0) => new Intl.NumberFormat(document.documentElement.lang || 'tr', { maximumFractionDigits: digits }).format(value);
const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

/** Sepete tek ürün ekleyen küçük form; gönderimi site.js'teki sepet modülü yakalar. */
const addForm = (data, item, label) => `
    <form method="POST" action="${escapeHtml(data.addUrl)}" data-quote-add data-quote-title="${escapeHtml(item.title)}" class="tool-addone">
        <input type="hidden" name="_token" value="${escapeHtml(csrf())}">
        <input type="hidden" name="item" value="${item.id}">
        <input type="hidden" name="locale" value="${escapeHtml(data.locale)}">
        <button type="submit">${escapeHtml(label)}</button>
    </form>`;

const productRow = (item, meta, extra = '') => `
    <li class="tool-item">
        <a class="tool-thumb" href="${escapeHtml(item.url)}">${item.image ? `<img src="${escapeHtml(item.image)}" alt="" loading="lazy">` : ''}</a>
        <div class="tool-info">
            <a class="tool-name" href="${escapeHtml(item.url)}">${escapeHtml(item.title)}</a>
            <span class="tool-code">${escapeHtml(item.code || '')}</span>
            <span class="tool-meta">${meta}</span>
        </div>
        ${extra}
    </li>`;

// ── Cıvata verisi: tork hesaplayıcı ve cıvata germe birlikte kullanır ───────
// Metrik: kaba diş adımı (ISO 261) ve ağır seri somun anahtar ağzı (ISO 4032 / 4033).
// İnç: [çap (inç), inçte diş]; 1" üstü 8UN. Ağır altıgen somun ağzı 1,5·D + 1/8".
const BOLTS = {
    M: {
        M12: [1.75, 18], M14: [2, 21], M16: [2, 24], M18: [2.5, 27], M20: [2.5, 30], M22: [2.5, 34],
        M24: [3, 36], M27: [3, 41], M30: [3.5, 46], M33: [3.5, 50], M36: [4, 55], M39: [4, 60],
        M42: [4.5, 65], M45: [4.5, 70], M48: [5, 75], M52: [5, 80], M56: [5.5, 85], M60: [5.5, 90],
        M64: [6, 95], M68: [6, 100], M72: [6, 105], M76: [6, 110], M80: [6, 115], M90: [6, 130], M100: [6, 145],
    },
    I: {
        '1/2"': [0.5, 13], '5/8"': [0.625, 11], '3/4"': [0.75, 10], '7/8"': [0.875, 9], '1"': [1, 8],
        '1-1/8"': [1.125, 8], '1-1/4"': [1.25, 8], '1-3/8"': [1.375, 8], '1-1/2"': [1.5, 8], '1-5/8"': [1.625, 8],
        '1-3/4"': [1.75, 8], '1-7/8"': [1.875, 8], '2"': [2, 8], '2-1/4"': [2.25, 8], '2-1/2"': [2.5, 8],
        '2-3/4"': [2.75, 8], '3"': [3, 8], '3-1/4"': [3.25, 8], '3-1/2"': [3.5, 8], '3-3/4"': [3.75, 8], '4"': [4, 8],
    },
};
const DEFAULT_SIZE = { M: 'M48', I: '1-7/8"' };

/**
 * Akma dayanımı (MPa), çapa (mm) göre. Metrik ISO 898-1; inç sınıfları
 * ASTM A193 / A320 ve SAE J429 değerleridir (çap büyüdükçe düşer).
 */
const GRADES = {
    M: { '4.6': () => 240, '5.6': () => 300, '8.8': (d) => (d <= 16 ? 640 : 660), '10.9': () => 940, '12.9': () => 1100 },
    I: {
        'ASTM A193 B7': (d) => (d <= 63.5 ? 724 : d <= 101.6 ? 655 : 517),
        'ASTM A193 B16': (d) => (d <= 63.5 ? 724 : d <= 101.6 ? 655 : 586),
        'ASTM A320 L7': () => 724,
        'SAE Grade 5': (d) => (d <= 25.4 ? 634 : 558),
        'SAE Grade 8': () => 896,
    },
};
const DEFAULT_GRADE = { M: '10.9', I: 'ASTM A193 B7' };

/** Diş geometrisi, mm. d2 bölüm dairesi, d3 diş dibi çapı (60° diş, ISO 724). */
const boltGeometry = (sys, size) => {
    const row = BOLTS[sys][size];
    if (!row) return null;
    const [d, pitch, af] = sys === 'M'
        ? [Number(size.slice(1)), row[0], row[1]]
        : [row[0] * 25.4, 25.4 / row[1], Math.round((1.5 * row[0] + 0.125) * 25.4)];
    const d2 = d - 0.649519 * pitch;
    const d3 = d - 1.226869 * pitch;
    const area = Math.PI / 4 * ((d2 + d3) / 2) ** 2;                  // gerilme kesiti As
    return { d, pitch, af, d2, d3, area };
};

/** Bir formdaki diş sistemi seçimine göre ölçü ve kalite listelerini doldurur. */
const fillBoltSelects = (form) => {
    const sys = form.elements.sys.value;
    const fill = (select, values, preferred) => {
        const keep = values.includes(select.value) ? select.value : preferred;
        select.innerHTML = values.map((v) => `<option value="${escapeHtml(v)}">${escapeHtml(v)}</option>`).join('');
        select.value = keep;
    };
    fill(form.elements.size, Object.keys(BOLTS[sys]), DEFAULT_SIZE[sys]);
    fill(form.elements.grade, Object.keys(GRADES[sys]), DEFAULT_GRADE[sys]);
};

// ── Tork hesaplayıcı ────────────────────────────────────────────────────────
// Basit yöntem:  F = oran · Rp0,2 · As,  T = K · F · d
// VDI 2230:      σM = oran · Rp0,2 / √(1 + 3·[2·d2/d0 · (P/(π·d2) + 1,155·μG)]²),  F = σM · As
//                T = F · (P/2π + 0,577·d2·μG + Dkm/2·μK),  Dkm = (anahtar ağzı + 1,05·d)/2
// İkinci yöntem, sıkarken dişte oluşan burulmayı da gerilmeye katar ve diş ile
// somun altı sürtünmesini ayrı ele alır (Norbar'ın hesaplayıcısıyla aynı yaklaşım).
document.querySelectorAll('[data-torque-calc]').forEach((root) => {
    const data = toolData(root);
    const form = root.querySelector('[data-tool-form]');
    const out = (key) => root.querySelector(`[data-out="${key}"]`);
    const headroom = Number(root.dataset.headroom) || 0.9;
    let method = 'k';

    form.querySelectorAll('[name="sys"]').forEach((r) => r.addEventListener('change', () => fillBoltSelects(form)));

    const calculate = () => {
        const f = new FormData(form);
        const sys = f.get('sys');
        const g = boltGeometry(sys, f.get('size'));
        if (!g) return;
        const { d, pitch, af, d2, d3, area } = g;
        const rp = GRADES[sys][f.get('grade')](d);

        // Yöntem değişince kaydırıcı o yöntemin olağan değerine döner.
        if (f.get('method') !== method) {
            method = f.get('method');
            form.elements.pct.value = method === 'vdi' ? 90 : 75;
            form.querySelectorAll('[data-method]').forEach((el) => { el.hidden = el.dataset.method !== method; });
            out('pctLabel').textContent = method === 'vdi' ? data.text.pctVdi : data.text.pctK;
        }
        const pct = Number(form.elements.pct.value) / 100;
        out('pct').textContent = Math.round(pct * 100);

        let force, torque;
        if (method === 'vdi') {
            const muG = Number(f.get('mu_thread'));
            const muK = Number(f.get('mu_head'));
            const d0 = (d2 + d3) / 2;
            const twist = 2 * d2 / d0 * (pitch / (Math.PI * d2) + 1.155 * muG);
            force = pct * rp / Math.sqrt(1 + 3 * twist ** 2) * area;                       // N
            torque = force * (pitch / (2 * Math.PI) + d2 * muG / (2 * Math.cos(Math.PI / 6)) + (af + 1.05 * d) / 4 * muK) / 1000;
            out('method').textContent = data.text.vdiNote.replace(':g', fmt(muG, 2)).replace(':k', fmt(muK, 2));
        } else {
            force = pct * rp * area;                                                       // N
            torque = Number(f.get('k')) * force * d / 1000;                                // N·m
            out('method').textContent = '';
        }

        out('torque').textContent = `${fmt(Math.round(torque / 10) * 10)} N·m`;
        out('ftlb').textContent = `≈ ${fmt(Math.round(torque * 0.737562 / 10) * 10)} ft·lbf`;
        out('force').textContent = `${fmt(force / 1000)} kN`;
        out('area').textContent = `${fmt(area)} mm²`;
        out('af').textContent = `${af} mm`;

        const socket = data.sockets.find((s) => s.hex && af >= s.hex[0] && af <= s.hex[1]);

        const matches = data.wrenches
            .filter((w) => torque <= w.tork_maks_nm * headroom && torque >= (w.tork_min_nm || w.tork_maks_nm * 0.1))
            .filter((w) => !w.hex || (af >= w.hex[0] && af <= w.hex[1]))
            .sort((a, b) => a.tork_maks_nm - b.tork_maks_nm);

        out('matches').innerHTML = matches.map((w) => {
            const usage = torque / w.tork_maks_nm;
            let meta;
            if (w.basinc_bar) {
                meta = `${data.text.pressure}: <b>${fmt(Math.ceil(usage * w.basinc_bar / 5) * 5)} bar</b> · ${fmt(usage * 100)}% ${data.text.usage}`;
            } else {
                meta = `${data.text.electric} · ${fmt(usage * 100)}% ${data.text.usage}`;
            }
            if (w.hex) meta += ` · ${data.text.cassette} (${w.hex[0]}–${w.hex[1]} mm)`;
            else if (socket) meta += ` · ${data.text.socket}: ${af} mm (${escapeHtml(socket.code)})`;
            return productRow(w, meta, addForm(data, w, data.text.add));
        }).join('');

        out('none').hidden = matches.length > 0;
    };

    form.addEventListener('input', calculate);
    calculate();
});

// ── Cıvata germe hesabı ─────────────────────────────────────────────────────
// Nord-Lock / Boltight hesap föyü:
//   LTF = 1,01 + d / sıkma boyu  (1,10'dan küçükse 1,10)
//   Germe yükü = kalıcı yük × LTF
//   Basınç (bar) = 10 · germe yükü (N) / alet alanı (mm²)
//   %50 kapsamada ilk tur basıncı A = 1,25 · B
// Katalogdaki aletin alanı, maks. yükünden ve basıncından türetilir.
document.querySelectorAll('[data-tension-tool]').forEach((root) => {
    const data = toolData(root);
    const t = data.text;
    const form = root.querySelector('[data-tool-form]');
    const out = (key) => root.querySelector(`[data-out="${key}"]`);
    const toolArea = (tool) => tool.germe_yuk_kn * 1000 * 10 / tool.basinc_bar;     // mm²

    form.querySelectorAll('[name="sys"]').forEach((r) => r.addEventListener('change', () => fillBoltSelects(form)));
    fillBoltSelects(form);

    const calculate = () => {
        const f = new FormData(form);
        const sys = f.get('sys');
        const g = boltGeometry(sys, f.get('size'));
        if (!g) return;
        const rp = GRADES[sys][f.get('grade')](g.d);
        const pct = Number(f.get('pct')) / 100;
        const grip = Math.max(1, Number(f.get('grip')) || 1);
        const bolts = Math.max(1, Math.round(Number(f.get('bolts')) || 1));
        const half = f.get('coverage') === '50';
        const manualArea = Number(f.get('area')) || 0;
        out('pct').textContent = Math.round(pct * 100);

        const residual = pct * rp * g.area;                                  // N
        const rawLtf = 1.01 + g.d / grip;
        const ltf = Math.max(1.10, rawLtf);
        const applied = residual * ltf;                                      // N
        const ratio = applied / g.area / rp;
        const pressureFor = (area) => 10 * applied / area;                   // bar

        const tools = data.tensioners
            .filter((x) => !x.bolts || (Math.round(g.d) >= x.bolts[0] && Math.round(g.d) <= x.bolts[1]))
            .map((x) => {
                const b = pressureFor(toolArea(x));
                return { ...x, b, peak: half ? b * 1.25 : b };
            })
            .sort((a, b) => a.germe_yuk_kn - b.germe_yuk_kn);
        const fitting = tools.filter((x) => x.peak <= x.basinc_bar);

        // Büyük rakam: girilen alan varsa onunla, yoksa en küçük uygun aletle.
        const chosen = manualArea > 0 ? { b: pressureFor(manualArea), manual: true } : fitting[0] || null;
        out('bigLabel').textContent = half ? t.pressureB : t.pressure;
        if (chosen) {
            out('pressure').textContent = `${fmt(chosen.b, 1)} bar`;
            out('psi').textContent = `≈ ${fmt(chosen.b * 14.5038)} psi · ${chosen.manual ? t.manual.replace(':area', fmt(manualArea, 1)) : t.withTool.replace(':code', chosen.code || chosen.title)}`;
            out('passA').textContent = half ? t.passA.replace(':a', fmt(chosen.b * 1.25, 1)) : '';
        } else {
            out('pressure').textContent = '—';
            out('psi').textContent = '';
            out('passA').textContent = '';
        }

        out('residual').textContent = `${fmt(residual / 1000, 1)} kN`;
        out('ltf').textContent = fmt(ltf, 3);
        out('applied').textContent = `${fmt(applied / 1000, 1)} kN`;
        out('ratio').textContent = `%${fmt(ratio * 100)}`;
        out('stressArea').textContent = `${fmt(g.area)} mm²`;
        out('tools').textContent = t.tools.replace(':n', half ? Math.ceil(bolts / 2) : bolts);

        const warnings = [];
        if (ratio > 0.95) warnings.push(t.yield.replace(':r', fmt(ratio * 100)));
        if (rawLtf < 1.10) warnings.push(t.ltfMin);
        if (grip < 3 * g.d) warnings.push(t.short);
        tools.filter((x) => x.peak > x.basinc_bar).forEach((x) => warnings.push(
            t.over.replace(':code', x.code || x.title).replace(':p', fmt(x.peak)).replace(':max', fmt(x.basinc_bar))));
        out('warn').innerHTML = warnings.map(escapeHtml).join('<br>');
        out('warn').hidden = warnings.length === 0;

        out('matches').innerHTML = fitting.map((x) => {
            const meta = `${t.pressure}: <b>${fmt(x.b, 1)} bar</b>${half ? ` · A ${fmt(x.peak, 1)} bar` : ''} · ${fmt(x.peak / x.basinc_bar * 100)}% ${t.usage}`;
            return productRow(x, meta, addForm(data, x, t.add));
        }).join('');
        out('none').hidden = fitting.length > 0;
    };

    form.addEventListener('input', calculate);
    form.addEventListener('change', calculate);
    calculate();
});

// ── Silindir seçici ve sistem kurucu ────────────────────────────────────────
document.querySelectorAll('[data-cylinder-tool]').forEach((root) => {
    const data = toolData(root);
    const form = root.querySelector('[data-tool-form]');
    const out = (key) => root.querySelector(`[data-out="${key}"]`);
    let kit = {};

    const calculate = () => {
        const f = new FormData(form);
        const load = Math.max(0, Number(f.get('load')) || 0);
        const n = Number(f.get('points')) || 1;
        const sf = Number(f.get('sf')) || 1.25;
        const stroke = Math.max(0, Number(f.get('stroke')) || 0);
        const action = f.get('action');
        out('n').textContent = n;

        const need = load * sf / n;
        out('need').textContent = `${fmt(need, 1)} t`;
        out('formula').textContent = `${fmt(load)} t × ${String(sf).replace('.', ',')} ÷ ${n}`;

        const cylinder = data.cylinders
            .filter((c) => c.kapasite_ton >= need && c.strok_mm >= stroke && (!action || c.action === action))
            .sort((a, b) => a.kapasite_ton - b.kapasite_ton || a.strok_mm - b.strok_mm)[0];

        kit = {};
        const rows = [];
        const warnings = [];

        if (cylinder) {
            kit[cylinder.id] = n;
            rows.push(productRow(cylinder,
                `${n} × ${data.text.cylinder} · ${fmt(need / cylinder.kapasite_ton * 100)}% ${data.text.usage} · ${fmt(cylinder.kapasite_ton)} t / ${fmt(cylinder.strok_mm)} mm`,
                `<span class="tool-qty">× ${n}</span>`));

            // Yağ: silindirlerin toplamı + %10 hortum ve pay
            const oil = (cylinder.yag_hacmi_cm3 || 0) * n * 1.1;
            const pump = data.pumps
                .filter((p) => p.kullanilabilir_yag_cm3 >= oil)
                .sort((a, b) => a.kullanilabilir_yag_cm3 - b.kullanilabilir_yag_cm3)[0];

            if (pump) {
                kit[pump.id] = 1;
                rows.push(productRow(pump, `${data.text.pump} · ${fmt(oil)} cm³ ${data.text.oil} / ${fmt(pump.kullanilabilir_yag_cm3)} cm³`, '<span class="tool-qty">× 1</span>'));
            } else if (oil > 0) {
                warnings.push(data.text.noPump);
            }

            if (data.hose) { kit[data.hose.id] = n; rows.push(productRow(data.hose, data.text.hose, `<span class="tool-qty">× ${n}</span>`)); }
            if (data.gauge) { kit[data.gauge.id] = 1; rows.push(productRow(data.gauge, data.text.gauge, '<span class="tool-qty">× 1</span>')); }

            if (n > 1) warnings.push(`${n} ${data.text.manifold}`);
            if (cylinder.action === 'çift') warnings.push(data.text.double);
        }

        out('kit').innerHTML = rows.join('');
        out('none').hidden = Boolean(cylinder);
        out('warn').hidden = warnings.length === 0;
        out('warn').textContent = warnings.join(' ');
        out('add').hidden = !cylinder;
    };

    out('add').addEventListener('click', async () => {
        const button = out('add');
        const body = new FormData();
        Object.entries(kit).forEach(([id, qty]) => body.append(`items[${id}]`, qty));
        body.append('locale', data.locale);
        button.disabled = true;
        try {
            const response = await fetch(data.addManyUrl, {
                method: 'POST', body, credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf() },
            });
            if (!response.ok) throw response;
            const state = await response.json();
            document.dispatchEvent(new CustomEvent('quote:update', { detail: { state, message: data.text.added } }));
        } finally {
            button.disabled = false;
        }
    });

    form.addEventListener('input', calculate);
    calculate();
});

// ── Birim dönüştürücü ───────────────────────────────────────────────────────
// Her kutu, grubun ilk birimine göre çarpanını taşır: değer → taban → hedef.
// Sıcaklık doğrusal değildir; çarpan yerine ölçek adı (C, F, K) taşır.
document.querySelectorAll('[data-unit-converter]').forEach((root) => {
    const tabs = root.querySelectorAll('[data-uc-tab]');
    const decimal = (1.5).toLocaleString(document.documentElement.lang || 'tr').charAt(1);
    // Türkçede "1.355,8" binlik noktalıdır; ama "25.4" gibi tek noktalı bir
    // yazım da ondalık kabul edilir, çünkü kullanıcılar ikisini de yazar.
    const parse = (value) => {
        let s = String(value).trim().replace(/\s/g, '');
        if (decimal === ',') {
            const grouped = /^-?\d{1,3}(\.\d{3})+(,\d*)?$/.test(s);
            s = grouped || s.includes(',') ? s.replace(/\./g, '').replace(',', '.') : s;
        } else {
            s = s.replace(/,/g, '');
        }
        return Number(s);
    };
    const show = (value) => {
        const abs = Math.abs(value);
        const rounded = abs === 0 ? 0 : abs >= 1000 ? Math.round(value * 10) / 10 : Number(value.toPrecision(abs >= 1 ? 6 : 4));
        return fmt(rounded, 10);
    };
    const toC = (v, scale) => scale === 'C' ? v : scale === 'F' ? (v - 32) * 5 / 9 : v - 273.15;
    const fromC = (c, scale) => scale === 'C' ? c : scale === 'F' ? c * 9 / 5 + 32 : c + 273.15;

    const update = (panel, source) => {
        const value = parse(source.value);
        panel.querySelectorAll('.tool-field').forEach((field) => field.classList.toggle('is-source', field.contains(source)));
        if (source.value.trim() === '' || Number.isNaN(value)) return;

        const from = source.dataset.factor;
        panel.querySelectorAll('input[data-factor]').forEach((input) => {
            if (input === source) return;
            const to = input.dataset.factor;
            const out = Number.isNaN(Number(from)) ? fromC(toC(value, from), to) : value * Number(from) / Number(to);
            input.value = show(out);
        });
    };

    root.querySelectorAll('[data-uc-panel]').forEach((panel) => {
        panel.addEventListener('input', (event) => update(panel, event.target));
        update(panel, panel.querySelector('input[data-factor]'));
    });

    const select = (tab) => {
        tabs.forEach((t) => t.setAttribute('aria-selected', String(t === tab)));
        root.querySelectorAll('[data-uc-panel]').forEach((panel) => { panel.hidden = panel.dataset.ucPanel !== tab.dataset.ucTab; });
    };
    tabs.forEach((tab, i) => {
        tab.addEventListener('click', () => select(tab));
        tab.addEventListener('keydown', (event) => {
            const step = { ArrowRight: 1, ArrowLeft: -1 }[event.key];
            if (!step) return;
            const next = tabs[(i + step + tabs.length) % tabs.length];
            select(next);
            next.focus();
        });
    });
});

// ── Flanş sıkma sıralaması ──────────────────────────────────────────────────
// Çapraz sıra 4'lü gruplarla kurulur: her grup 0°, 180°, 90°, 270° konumları.
// Grupların sırası Van der Corput dizisiyle seçilir; böylece art arda gelen
// gruplar çevre üzerinde birbirinden olabildiğince uzak düşer
// (16 cıvatada 1-9-5-13, 3-11-7-15, 2-10-6-14, 4-12-8-16).
const groupOrder = (groups) => {
    const out = [];
    const seen = new Set();
    for (let i = 0; out.length < groups && i < groups * 64; i++) {
        let v = 0, d = 0.5, k = i;
        while (k) { if (k & 1) v += d; d /= 2; k >>= 1; }
        const g = Math.round(v * groups) % groups;
        if (!seen.has(g)) { seen.add(g); out.push(g); }
    }
    for (let g = 0; g < groups; g++) if (!seen.has(g)) out.push(g);
    return out;
};
const starSequence = (n) => groupOrder(n / 4).flatMap((o) => [o, o + n / 2, o + n / 4, o + 3 * n / 4]).map((i) => i + 1);

document.querySelectorAll('[data-flange-tool]').forEach((root) => {
    const data = toolData(root);
    const t = data.text;
    const form = root.querySelector('[data-tool-form]');
    const out = (key) => root.querySelector(`[data-out="${key}"]`);
    const act = (key) => root.querySelector(`[data-act="${key}"]`);
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const state = { n: 16, passes: [], pass: 0, step: 0, timer: null };

    const groupSize = (p) => (p.mode === 'quad' ? 4 : 1);
    const totalSteps = (p) => Math.ceil(p.seq.length / groupSize(p));
    const stop = () => { clearInterval(state.timer); state.timer = null; };

    // ASME B16.5 seçiliyse cıvata sayısı tablodan gelir ve elle değiştirilemez.
    const applyStandard = () => {
        const standard = form.elements.std.value === 'b165';
        root.querySelector('[data-std]').hidden = !standard;
        form.elements.n.readOnly = standard;
        if (!standard) { out('flange').hidden = true; return true; }

        const nps = form.elements.nps.value;
        const cls = form.elements.class.value;
        const row = data.flanges[cls]?.[nps];
        out('flange').hidden = false;
        if (!row) {
            out('flange').textContent = t.noFlange.replace(':nps', nps).replace(':class', cls);
            return false;
        }
        form.elements.n.value = row[0];
        out('flange').textContent = t.flange.replace(':nps', nps).replace(':class', cls).replace(':n', row[0]).replace(':d', row[1]);
        return true;
    };

    const build = () => {
        if (!applyStandard()) return;
        const f = new FormData(form);
        const n = Number(f.get('n'));
        const torque = Number(f.get('torque'));
        const error = !(n >= 4 && n <= 96 && n % 4 === 0) ? t.errBolts : !(torque > 0) ? t.errTorque : '';
        out('error').textContent = error;
        out('error').hidden = !error;
        if (error) return;

        const unit = f.get('unit');
        const mode = f.get('pattern');
        const seq = starSequence(n);
        state.n = n;
        state.passes = f.get('passes').split(',').map(Number).map((pct, i) => ({
            name: `${t.pass} ${i + 1}`, pct, torque: Math.round(torque * pct / 100), unit, seq, mode,
        }));
        if (f.get('check')) {
            state.passes.push({ name: t.check, pct: 100, torque: Math.round(torque), unit, seq: seq.map((_, i) => i + 1), mode: 'cw' });
        }
        state.pass = 0;
        state.step = 0;
        stop();
        renderTable();
        render();
    };

    const renderTable = () => {
        out('table').innerHTML = `<thead><tr>${t.cols.map((c) => `<th>${escapeHtml(c)}</th>`).join('')}</tr></thead><tbody>${state.passes.map((p) => `
            <tr><td>${escapeHtml(p.name)}</td><td>%${p.pct}</td><td>${fmt(p.torque)} ${escapeHtml(p.unit)}</td><td>${escapeHtml(t[p.mode])}</td></tr>`).join('')}</tbody>`;
    };

    const render = () => {
        const p = state.passes[state.pass];
        if (!p) return;
        const n = state.n;
        const g = groupSize(p);
        const steps = totalSteps(p);
        const doneCount = Math.min(state.step * g, n);
        const current = new Set(state.step < steps ? p.seq.slice(state.step * g, state.step * g + g) : []);
        const done = new Set(p.seq.slice(0, doneCount));
        const orderOf = {};
        p.seq.forEach((bolt, i) => { orderOf[bolt] = i + 1; });

        out('pass').textContent = `${p.name} · %${p.pct}`;
        out('torque').textContent = `${fmt(p.torque)} ${p.unit}`;
        out('step').textContent = state.step >= steps ? t.done : t.step.replace(':n', state.step + 1).replace(':total', steps);

        out('tabs').innerHTML = state.passes.map((x, i) => `<button type="button" aria-pressed="${i === state.pass}" data-pass="${i}">${escapeHtml(x.name)}</button>`).join('');

        const cx = 200, cy = 200, r = 150, rb = Math.max(9, Math.min(19, 560 / n));
        let svg = `<circle class="fl-ring" cx="${cx}" cy="${cy}" r="188"/>`
            + `<circle class="fl-bore" cx="${cx}" cy="${cy}" r="${r - rb - 14}"/>`
            + `<circle class="fl-pcd" cx="${cx}" cy="${cy}" r="${r}"/>`
            + `<text class="fl-count" x="${cx}" y="${cy - 6}" text-anchor="middle">${doneCount}/${n}</text>`
            + `<text class="fl-sub" x="${cx}" y="${cy + 18}" text-anchor="middle">${escapeHtml(t.tightened)}</text>`;
        for (let i = 0; i < n; i++) {
            const a = -Math.PI / 2 + i * 2 * Math.PI / n;
            const x = (cx + r * Math.cos(a)).toFixed(1);
            const y = (cy + r * Math.sin(a)).toFixed(1);
            const bolt = i + 1;
            const cls = current.has(bolt) ? 'is-cur' : done.has(bolt) ? 'is-done' : '';
            svg += `<g class="fl-bolt ${cls}"><circle cx="${x}" cy="${y}" r="${rb}"/>`
                + `<text x="${x}" y="${(Number(y) + rb * 0.36).toFixed(1)}" text-anchor="middle" font-size="${(rb * 0.95).toFixed(1)}">${bolt}</text></g>`;
            if (n <= 48) {
                const lx = (cx + (r + rb + 11) * Math.cos(a)).toFixed(1);
                const ly = (cy + (r + rb + 11) * Math.sin(a) + 4).toFixed(1);
                svg += `<text class="fl-order" x="${lx}" y="${ly}" text-anchor="middle">#${orderOf[bolt]}</text>`;
            }
        }
        out('svg').innerHTML = svg;

        out('seq').innerHTML = p.seq.map((bolt, i) => {
            const s = Math.floor(i / g);
            const cls = s < state.step ? 'is-done' : s === state.step ? 'is-cur' : '';
            const gap = g > 1 && i % g === 0 && i > 0 ? ' fl-gap' : '';
            return `<li class="${cls}${gap}">${bolt}</li>`;
        }).join('');

        act('play').textContent = state.timer ? t.pause : t.play;
    };

    const next = () => {
        const p = state.passes[state.pass];
        if (state.step < totalSteps(p)) state.step++;
        else if (state.pass < state.passes.length - 1) { state.pass++; state.step = 0; }
        else stop();
        render();
    };

    out('tabs').addEventListener('click', (event) => {
        const button = event.target.closest('[data-pass]');
        if (!button) return;
        stop();
        state.pass = Number(button.dataset.pass);
        state.step = 0;
        render();
    });
    act('next').addEventListener('click', () => { stop(); next(); });
    act('prev').addEventListener('click', () => { stop(); if (state.step > 0) state.step--; render(); });
    act('reset').addEventListener('click', () => { stop(); state.pass = 0; state.step = 0; render(); });
    act('play').addEventListener('click', () => {
        if (state.timer) { stop(); render(); return; }
        state.timer = setInterval(() => {
            const p = state.passes[state.pass];
            if (state.pass === state.passes.length - 1 && state.step >= totalSteps(p)) { stop(); render(); return; }
            next();
        }, reduceMotion ? 1400 : 700);
        render();
    });

    form.addEventListener('input', build);
    build();
});

// ── Teknik şartname hazırlayıcı ─────────────────────────────────────────────
// Maddeler sunucudan dile göre gelir; burada yalnızca {yer tutucu}lar dolar.
document.querySelectorAll('[data-spec-tool]').forEach((root) => {
    const data = toolData(root);
    const spec = data.spec;
    const form = root.querySelector('[data-tool-form]');
    const out = (key) => root.querySelector(`[data-out="${key}"]`);
    const act = (key) => root.querySelector(`[data-act="${key}"]`);
    let sections = [];
    let title = '';

    const onCategory = () => {
        const eq = spec.equipment[form.elements.cat.value];
        out('capLabel').textContent = eq.cap[0];
        out('extLabel').textContent = eq.ext[0];
        form.elements.cap.value = eq.cap[1];
        form.elements.ext.value = eq.ext[1];
        form.elements.drive.value = eq.drive;
    };

    const calculate = () => {
        const f = form.elements;
        const eq = spec.equipment[f.cat.value];
        const qty = Math.max(1, Math.round(Number(f.qty.value)) || 1);
        const values = {
            q: qty,
            cap: f.cap.value === '' ? '…' : fmt(Number(f.cap.value), 2),
            ext: f.ext.value.trim() || '…',
            drive: spec.drives[f.drive.value][1],
            war: f.war.value || '…',
            del: f.del.value || '…',
            items: eq.calibration,
        };
        const fill = (clause) => {
            const text = typeof clause === 'string' ? clause : clause[qty > 2 ? 'multi' : 'single'];
            return text.replace(/\{(\w+)\}/g, (_, key) => values[key] ?? '');
        };

        const docs = [...spec.documents[1]];
        if (f.atex.checked) docs.push(spec.atex);
        if (f.cal.checked) docs.push(spec.calibration);
        const service = [...spec.service[1]];
        if (f.training.checked) service.push(spec.training);
        if (f.case.checked) service.push(spec.case);

        sections = [...eq.sections, [spec.documents[0], docs], [spec.service[0], service]]
            .map(([heading, clauses]) => [heading, clauses.map(fill)]);
        title = `${eq.title} — ${spec.suffix}`;

        out('title').textContent = title;
        out('spec').innerHTML = sections.map(([heading, list], i) => `
            <section><h4>${i + 1}. ${escapeHtml(heading)}</h4>
            <ol>${list.map((x, j) => `<li><span>${i + 1}.${j + 1}</span> ${escapeHtml(x)}</li>`).join('')}</ol></section>`).join('');
    };

    const plain = () => `${title}\n\n${sections.map(([h, list], i) => `${i + 1}. ${h}\n${list.map((x, j) => `${i + 1}.${j + 1}. ${x}`).join('\n')}`).join('\n\n')}`;

    act('copy').addEventListener('click', async () => {
        const button = act('copy');
        const label = button.textContent;
        try {
            await navigator.clipboard.writeText(plain());
            button.textContent = data.text.copied;
        } catch {
            const range = document.createRange();
            range.selectNodeContents(out('spec'));
            const selection = getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
            button.textContent = data.text.selected;
        }
        setTimeout(() => { button.textContent = label; }, 2500);
    });

    // Yalnızca şartname kutusu yazdırılsın: geri kalan sayfa print stilinde gizlenir.
    act('print').addEventListener('click', () => {
        const target = root.querySelector('[data-print-target]');
        target.classList.add('is-printing');
        document.body.classList.add('print-one');
        const cleanup = () => {
            target.classList.remove('is-printing');
            document.body.classList.remove('print-one');
            window.removeEventListener('afterprint', cleanup);
        };
        window.addEventListener('afterprint', cleanup);
        window.print();
    });

    form.elements.cat.addEventListener('change', onCategory);
    form.addEventListener('input', calculate);
    form.addEventListener('change', calculate);
    calculate();
});
