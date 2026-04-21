(() => {
    'use strict';

    const DATA_URL = 'plugins.json';

    // Tiny embedded fallback so the page still classifies something when
    // opened via file:// (fetch('plugins.json') blocked). Real data lives
    // in plugins.json; this is only a safety net for demos without a server.
    const EMBEDDED_DB = {
        generatedAt: null,
        plugins: [
            {packageName:'sylius/refund-plugin',latestTag:'v2.0.5',syliusConstraint:'^2.0',constraintFrom:'sylius/sylius',supports1x:false,supports2x:true,downloads:1688887},
            {packageName:'sylius/paypal-plugin',latestTag:'v2.0.8',syliusConstraint:'^2.0.1',constraintFrom:'sylius/sylius',supports1x:false,supports2x:true,downloads:1420226},
            {packageName:'bitbag/cms-plugin',latestTag:'v4.4',syliusConstraint:'^1.13',constraintFrom:'sylius/sylius',supports1x:true,supports2x:false,downloads:1145974},
            {packageName:'stefandoorn/sitemap-plugin',latestTag:'v3.0.0',syliusConstraint:'^2.0',constraintFrom:'sylius/sylius',supports1x:false,supports2x:true,downloads:1039431},
            {packageName:'sylius/invoicing-plugin',latestTag:'v2.1.1',syliusConstraint:'^2.0',constraintFrom:'sylius/sylius',supports1x:false,supports2x:true,downloads:1019681},
            {packageName:'bitbag/wishlist-plugin',latestTag:'v4.5.0',syliusConstraint:'^1.13 || ^1.14',constraintFrom:'sylius/sylius',supports1x:true,supports2x:false,downloads:600836,notes:'v2 branch in progress'},
            {packageName:'bitbag/elasticsearch-plugin',latestTag:'v5.3.0',syliusConstraint:'^2.0',constraintFrom:'sylius/sylius',supports1x:false,supports2x:true,downloads:491321},
            {packageName:'friendsofsylius/sylius-import-export-plugin',latestTag:'0.28.0',syliusConstraint:'~2.0',constraintFrom:'sylius/sylius',supports1x:false,supports2x:true,downloads:411108},
            {packageName:'monsieurbiz/sylius-rich-editor-plugin',latestTag:'v3.1.8',syliusConstraint:'~2.0',constraintFrom:'sylius/sylius',supports1x:false,supports2x:true,downloads:389497},
            {packageName:'synolia/sylius-scheduler-command-plugin',latestTag:'v4.0.2',syliusConstraint:'^2.0',constraintFrom:'sylius/sylius',supports1x:false,supports2x:true,downloads:368577},
            {packageName:'setono/sylius-feed-plugin',latestTag:'v0.6.25',syliusConstraint:'^1.0',constraintFrom:'sylius/core',supports1x:true,supports2x:false,downloads:463140},
            {packageName:'setono/sylius-redirect-plugin',latestTag:'v2.7.0',syliusConstraint:'^1.0',constraintFrom:'sylius/core-bundle',supports1x:true,supports2x:false,downloads:356476},
            {packageName:'monsieurbiz/sylius-search-plugin',latestTag:'v3.0.0',syliusConstraint:'^2.0',constraintFrom:'sylius/sylius',supports1x:false,supports2x:true,downloads:142889},
            {packageName:'commerce-weavers/sylius-tpay-plugin',latestTag:'v1.2.0',syliusConstraint:'^2.0',constraintFrom:'sylius/sylius',supports1x:false,supports2x:true,downloads:31420},
        ],
    };

    // Demo presets used by the preset-pill buttons.
    const PRESETS = {
        empty: '',
        b2c: `{
  "require": {
    "sylius/sylius": "^2.0",
    "sylius/refund-plugin": "^2.0",
    "sylius/paypal-plugin": "^2.0",
    "sylius/invoicing-plugin": "^2.0",
    "bitbag/cms-plugin": "^4.0",
    "bitbag/wishlist-plugin": "^4.0",
    "stefandoorn/sitemap-plugin": "^3.0",
    "monsieurbiz/sylius-rich-editor-plugin": "^3.0",
    "monsieurbiz/sylius-search-plugin": "^3.0",
    "symfony/framework-bundle": "^6.4",
    "doctrine/orm": "^2.15",
    "twig/twig": "^3.8"
  }
}`,
        b2b: `{
  "require": {
    "sylius/sylius": "^2.0",
    "sylius/refund-plugin": "^2.0",
    "sylius/invoicing-plugin": "^2.0",
    "bitbag/elasticsearch-plugin": "^5.0",
    "friendsofsylius/sylius-import-export-plugin": "~0.28",
    "synolia/sylius-scheduler-command-plugin": "^4.0",
    "commerce-weavers/sylius-tpay-plugin": "^1.0",
    "api-platform/core": "^3.2",
    "symfony/messenger": "^6.4"
  }
}`,
        legacy: `{
  "require": {
    "sylius/sylius": "^1.13",
    "bitbag/cms-plugin": "^1.13",
    "bitbag/wishlist-plugin": "^1.13",
    "setono/sylius-feed-plugin": "^0.6",
    "setono/sylius-redirect-plugin": "^1.0",
    "stefandoorn/sitemap-plugin": "^1.8",
    "symfony/framework-bundle": "^5.4"
  }
}`,
    };

    // Sylius monorepo + framework packages. These track sylius/sylius directly;
    // route them out of the plugin buckets so core isn't mislabeled as missing.
    const SYLIUS_CORE = new Set([
        'sylius/sylius',
        'sylius/core', 'sylius/core-bundle',
        'sylius/admin-bundle', 'sylius/admin-api-bundle',
        'sylius/api-bundle',
        'sylius/shop-bundle', 'sylius/shop-api-plugin',
        'sylius/resource', 'sylius/resource-bundle',
        'sylius/grid', 'sylius/grid-bundle',
        'sylius/mailer', 'sylius/mailer-bundle',
        'sylius/attribute', 'sylius/attribute-bundle',
        'sylius/channel', 'sylius/channel-bundle',
        'sylius/currency', 'sylius/currency-bundle',
        'sylius/customer', 'sylius/customer-bundle',
        'sylius/inventory', 'sylius/inventory-bundle',
        'sylius/locale', 'sylius/locale-bundle',
        'sylius/order', 'sylius/order-bundle',
        'sylius/payment', 'sylius/payment-bundle',
        'sylius/product', 'sylius/product-bundle',
        'sylius/promotion', 'sylius/promotion-bundle',
        'sylius/shipping', 'sylius/shipping-bundle',
        'sylius/taxation', 'sylius/taxation-bundle',
        'sylius/taxonomy', 'sylius/taxonomy-bundle',
        'sylius/theme-bundle',
        'sylius/review', 'sylius/review-bundle',
        'sylius/addressing', 'sylius/addressing-bundle',
        'sylius/user', 'sylius/user-bundle',
        'sylius/ui-bundle',
        'sylius/registry',
        'sylius/state-machine-abstraction',
        'sylius/fixtures-bundle',
    ]);

    const state = {
        db: null,
        generatedAt: null,
    };

    const $ = (sel, root = document) => root.querySelector(sel);
    const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];

    const dom = {
        inputScreen: $('#input-screen'),
        resultsScreen: $('#results-screen'),
        textarea: $('#composer-input'),
        singleInput: $('#single-input'),
        checkBtn: $('#check-btn'),
        singleBtn: $('#single-btn'),
        parseError: $('#parse-error'),
        countNumber: $('#plugin-count'),
        countLabel: $('#plugin-count-label'),
        groups: $('#results-groups'),
        summaryStrip: $('#summary-strip'),
        snapshotDate: $('[data-snapshot-date]'),
        statTotal: $('[data-stat-total]'),
        statReady: $('[data-stat-ready]'),
    };

    // ---------- bootstrap ----------

    function useDb(payload) {
        state.db = new Map(payload.plugins.map((p) => [p.packageName, p]));
        state.generatedAt = payload.generatedAt;
        dom.snapshotDate.textContent = formatDate(payload.generatedAt);
        dom.statTotal.textContent = String(payload.plugins.length);
        dom.statReady.textContent = String(payload.plugins.filter((p) => p.supports2x).length);
        dom.checkBtn.disabled = false;
        dom.singleBtn.disabled = false;
    }

    (async function boot() {
        dom.checkBtn.disabled = true;
        dom.singleBtn.disabled = true;
        try {
            const r = await fetch(DATA_URL, { cache: 'no-store' });
            if (!r.ok) throw new Error('http ' + r.status);
            useDb(await r.json());
        } catch (_) {
            useDb(EMBEDDED_DB);
        }
        applyPreset('b2c');
    })();

    // ---------- wiring ----------

    $$('.tab').forEach((t) => t.addEventListener('click', () => switchTab(t.dataset.tab)));
    $$('.preset').forEach((b) => b.addEventListener('click', () => applyPreset(b.dataset.preset)));
    $$('[data-try]').forEach((a) => a.addEventListener('click', (e) => {
        e.preventDefault();
        dom.singleInput.value = a.dataset.try;
        handleSingle();
    }));

    dom.checkBtn.addEventListener('click', handleCheck);
    dom.singleBtn.addEventListener('click', handleSingle);
    dom.singleInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); handleSingle(); }
    });
    $$('[data-radar-back]').forEach((b) => b.addEventListener('click', () => showScreen('input')));

    function switchTab(which) {
        $$('.tab').forEach((t) => t.setAttribute('aria-selected', t.dataset.tab === which ? 'true' : 'false'));
        $$('[data-panel]').forEach((p) => p.classList.toggle('is-hidden', p.dataset.panel !== which));
    }

    function applyPreset(name) {
        dom.textarea.value = PRESETS[name] ?? '';
        $$('.preset').forEach((b) => b.classList.toggle('is-active', b.dataset.preset === name));
        clearError();
    }

    // ---------- handlers ----------

    function handleCheck() {
        clearError();
        const raw = dom.textarea.value.trim();
        if (!raw) {
            showParseError('Paste a composer.json above, or try one of the demo presets.');
            return;
        }
        let parsed;
        try { parsed = JSON.parse(raw); }
        catch (e) { showParseError(humanizeJsonError(raw, e)); return; }

        const pkgs = extractPackages(parsed);
        if (!pkgs.length) {
            showParseError('No packages found in `require` or `require-dev`. Did you paste a composer.json?');
            return;
        }

        dom.checkBtn.disabled = true;
        const original = dom.checkBtn.innerHTML;
        dom.checkBtn.innerHTML = 'Scanning… <span class="loader__dots"><span></span><span></span><span></span></span>';
        setTimeout(() => {
            renderResults(classify(pkgs));
            dom.checkBtn.disabled = false;
            dom.checkBtn.innerHTML = original;
        }, 350);
    }

    function handleSingle() {
        clearError();
        const name = dom.singleInput.value.trim().toLowerCase();
        if (!name) return;
        renderResults(classify([{ name, constraint: null }]));
    }

    // ---------- classification ----------

    function extractPackages(composer) {
        const merged = {};
        for (const section of ['require', 'require-dev']) {
            const obj = composer[section];
            if (obj && typeof obj === 'object') {
                for (const [k, v] of Object.entries(obj)) {
                    if (typeof k === 'string' && k.includes('/')) {
                        merged[k.toLowerCase()] = typeof v === 'string' ? v : null;
                    }
                }
            }
        }
        return Object.entries(merged).map(([name, constraint]) => ({ name, constraint }));
    }

    function classify(pkgs) {
        const ready = [];
        const inProgress = [];
        const notReady = [];
        const unknownSylius = [];
        const other = [];
        const core = [];
        let detectedSylius = null;

        for (const pkg of pkgs) {
            if (pkg.name === 'sylius/sylius') {
                detectedSylius = pkg.constraint;
                core.push(pkg);
                continue;
            }
            if (SYLIUS_CORE.has(pkg.name)) {
                core.push(pkg);
                continue;
            }
            const entry = state.db.get(pkg.name);
            if (entry) {
                const row = { ...entry, userConstraint: pkg.constraint };
                if (entry.notes && /in\s*progress|alpha|beta|rc|v2\s*branch|work\s*in\s*progress/i.test(entry.notes)) {
                    inProgress.push(row);
                } else if (entry.supports2x) {
                    ready.push(row);
                } else if (entry.supports1x) {
                    notReady.push(row);
                } else {
                    unknownSylius.push({
                        packageName: pkg.name,
                        note: 'Radar has no parseable Sylius constraint for this plugin',
                    });
                }
            } else if (looksLikeSylius(pkg.name)) {
                unknownSylius.push({ packageName: pkg.name, note: null });
            } else {
                other.push(pkg.name);
            }
        }

        const byDownloads = (a, b) => (b.downloads || 0) - (a.downloads || 0);
        ready.sort(byDownloads);
        inProgress.sort(byDownloads);
        notReady.sort(byDownloads);

        return { ready, inProgress, notReady, unknownSylius, other, core, detectedSylius };
    }

    function looksLikeSylius(name) {
        return /sylius|bitbag|setono|monsieurbiz|webgriffe|synolia/.test(name);
    }

    // ---------- rendering ----------

    function renderResults(buckets) {
        const total = buckets.ready.length + buckets.inProgress.length + buckets.notReady.length + buckets.unknownSylius.length;
        dom.countNumber.textContent = String(total);
        dom.countLabel.textContent = total === 1 ? 'Sylius plugin identified' : 'Sylius plugins identified';
        dom.groups.innerHTML = '';

        if (total > 0) {
            dom.summaryStrip.classList.remove('is-hidden');
            dom.summaryStrip.innerHTML = '';
            dom.summaryStrip.append(
                summaryCell('ready', buckets.ready.length, 'Ready for 2.x'),
                summaryCell('progress', buckets.inProgress.length, 'In progress'),
                summaryCell('notready', buckets.notReady.length, 'Not yet ready'),
                summaryCell('unknown', buckets.unknownSylius.length, 'Uncovered'),
            );
        } else {
            dom.summaryStrip.classList.add('is-hidden');
        }

        if (buckets.detectedSylius) {
            dom.groups.appendChild(renderDetected(buckets.detectedSylius));
        }
        if (buckets.ready.length) {
            dom.groups.appendChild(renderGroup({ title: 'Ready for 2.x', dot: 'green', count: buckets.ready.length, rows: buckets.ready.map(renderReadyRow) }));
        }
        if (buckets.inProgress.length) {
            dom.groups.appendChild(renderGroup({ title: 'In progress', dot: 'blue', count: buckets.inProgress.length, rows: buckets.inProgress.map(renderInProgressRow) }));
        }
        if (buckets.notReady.length) {
            dom.groups.appendChild(renderGroup({ title: 'Not yet ready', dot: 'red', count: buckets.notReady.length, rows: buckets.notReady.map(renderNotReadyRow) }));
        }
        if (buckets.unknownSylius.length) {
            dom.groups.appendChild(renderGroup({ title: 'Not yet covered', dot: 'amber', count: buckets.unknownSylius.length, rows: buckets.unknownSylius.map(renderUnknownRow) }));
        }
        if (buckets.core.length) {
            dom.groups.appendChild(renderCore(buckets.core));
        }
        if (buckets.other.length) {
            dom.groups.appendChild(renderOther(buckets.other));
        }

        showScreen('results');
    }

    function summaryCell(tone, num, label) {
        const d = el('div', 'summary__cell');
        d.dataset.tone = tone;
        d.append(withText(el('span', 'summary__num'), String(num)), withText(el('span', 'summary__lbl'), label));
        return d;
    }

    function renderGroup({ title, dot, count, rows }) {
        const section = el('section', 'group');
        const h = el('h3', 'group__title');
        h.appendChild(el('span', `dot dot--${dot}`));
        h.appendChild(document.createTextNode(' ' + title + ' '));
        h.appendChild(withText(el('span', 'count'), `(${count})`));
        section.appendChild(h);
        const grid = el('div', 'rows');
        rows.forEach((r) => grid.appendChild(r));
        section.appendChild(grid);
        return section;
    }

    function renderReadyRow(e) {
        const row = el('div', 'row');
        const info = el('div');
        info.appendChild(withText(el('span', 'row__name'), e.packageName));
        const meta = el('p', 'row__meta');

        if (e.syliusConstraint && e.latestTag) {
            meta.append(
                document.createTextNode('Latest '),
                withText(el('code'), e.latestTag),
                document.createTextNode(' requires '),
                withText(el('code', e.constraintFrom && e.constraintFrom !== 'sylius/sylius' ? 'is-fallback' : ''),
                    `${e.constraintFrom || 'sylius/sylius'}: ${e.syliusConstraint}`),
            );
        } else if (e.notes) {
            // Curated entry with no Packagist resolution (e.g. sylius/plus).
            // The note is load-bearing — surface it verbatim.
            meta.appendChild(withText(el('span', 'row__note'), e.notes));
        } else {
            meta.appendChild(document.createTextNode('Radar flags this package as ready for 2.x.'));
        }

        info.appendChild(meta);
        if (e.downloads) {
            info.appendChild(withText(el('span', 'row__downloads'), `${formatDownloads(e.downloads)} downloads on Packagist`));
        }
        row.append(info, packagistLink(e.packageName));
        return row;
    }

    function renderInProgressRow(e) {
        const row = el('div', 'row row--muted');
        const info = el('div');
        info.appendChild(withText(el('span', 'row__name'), e.packageName));
        const meta = el('p', 'row__meta');
        meta.append(document.createTextNode('Notes: '), withText(el('span', 'row__note'), e.notes || 'in progress'));
        info.appendChild(meta);
        if (e.downloads) {
            info.appendChild(withText(el('span', 'row__downloads'), `${formatDownloads(e.downloads)} downloads`));
        }
        row.append(info, packagistLink(e.packageName));
        return row;
    }

    function renderNotReadyRow(e) {
        const row = el('div', 'row row--danger');
        const info = el('div');
        info.appendChild(withText(el('span', 'row__name'), e.packageName));
        const meta = el('p', 'row__meta');
        meta.append(
            document.createTextNode('Latest '),
            withText(el('code'), e.latestTag || '?'),
            document.createTextNode(' still pins '),
            withText(el('code', 'is-stale'), `${e.constraintFrom || 'sylius/sylius'}: ${e.syliusConstraint || '?'}`),
        );
        info.appendChild(meta);
        if (e.downloads) {
            const suffix = e.downloads > 250000 ? ' · high-impact block' : '';
            info.appendChild(withText(el('span', 'row__downloads'), `${formatDownloads(e.downloads)} downloads${suffix}`));
        }
        row.append(info, packagistLink(e.packageName));
        return row;
    }

    function renderUnknownRow(e) {
        const row = el('div', 'row row--unknown');
        row.append(
            withText(el('span', 'row__name'), e.packageName),
            withText(el('span', 'row__tag'), e.note || 'Not yet covered by the radar'),
        );
        return row;
    }

    function renderDetected(constraint) {
        const w = el('div', 'detected');
        w.append(
            withText(el('span', 'detected__lbl'), 'Detected'),
            withText(el('code', 'detected__name'), 'sylius/sylius'),
            withText(el('code', 'detected__ver'), constraint || '—'),
            withText(el('span', 'detected__suffix'), 'Checking plugins against Sylius 2.x.'),
        );
        return w;
    }

    function renderCore(pkgs) {
        const d = el('details', 'other');
        const s = el('summary', 'other__summary');
        s.append(
            document.createTextNode(`Sylius core components (${pkgs.length}) — follow sylius/sylius`),
            withText(el('span', 'other__arrow'), '↓'),
        );
        d.appendChild(s);
        const grid = el('div', 'other__grid');
        pkgs.sort((a, b) => a.name.localeCompare(b.name)).forEach((p) => {
            grid.appendChild(withText(el('div'), p.constraint ? `${p.name}: ${p.constraint}` : p.name));
        });
        d.appendChild(grid);
        return d;
    }

    function renderOther(names) {
        const d = el('details', 'other');
        const s = el('summary', 'other__summary');
        s.append(
            document.createTextNode(`Other PHP dependencies (${names.length})`),
            withText(el('span', 'other__arrow'), '↓'),
        );
        d.appendChild(s);
        const grid = el('div', 'other__grid');
        names.sort().forEach((n) => grid.appendChild(withText(el('div'), n)));
        d.appendChild(grid);
        return d;
    }

    function packagistLink(name) {
        const a = document.createElement('a');
        a.href = `https://packagist.org/packages/${name}`;
        a.target = '_blank';
        a.rel = 'noopener';
        a.className = 'row__link';
        a.textContent = 'Packagist ↗';
        return a;
    }

    // ---------- ui helpers ----------

    function showScreen(which) {
        dom.inputScreen.classList.toggle('is-hidden', which !== 'input');
        dom.resultsScreen.classList.toggle('is-hidden', which !== 'results');
        window.scrollTo({ top: 0, behavior: 'auto' });
    }

    function showParseError(msg) {
        dom.parseError.textContent = msg;
        dom.parseError.classList.remove('is-hidden');
    }

    function clearError() {
        dom.parseError.textContent = '';
        dom.parseError.classList.add('is-hidden');
    }

    function humanizeJsonError(raw, err) {
        const m = /position\s+(\d+)/i.exec(err.message || '');
        if (!m) return `Could not parse JSON: ${err.message}`;
        const pos = parseInt(m[1], 10);
        const up = raw.slice(0, pos);
        const line = up.split('\n').length;
        const col = pos - up.lastIndexOf('\n');
        return `Could not parse JSON at line ${line}, column ${col}: ${err.message}`;
    }

    function formatDownloads(n) {
        if (!n) return '';
        if (n >= 1e6) return (n / 1e6).toFixed(1).replace(/\.0$/, '') + 'M';
        if (n >= 1e3) return (n / 1e3).toFixed(0) + 'K';
        return String(n);
    }

    function formatDate(iso) {
        if (!iso) return '';
        const d = new Date(iso);
        if (isNaN(d)) return iso;
        const y = d.getUTCFullYear();
        const m = String(d.getUTCMonth() + 1).padStart(2, '0');
        const day = String(d.getUTCDate()).padStart(2, '0');
        return `${y}-${m}-${day}`;
    }

    function el(tag, cn) {
        const n = document.createElement(tag);
        if (cn) n.className = cn;
        return n;
    }

    function withText(n, t) {
        n.textContent = t;
        return n;
    }
})();
