(() => {
    'use strict';

    const DATA_URL = 'plugins.json';

    const state = {
        db: null,        // Map of packageName -> plugin entry
        generatedAt: null,
    };

    // Sylius monorepo + framework packages. These are not plugins; they track
    // sylius/sylius directly. Route them out of the "unknown plugin" bucket so
    // the radar doesn't mislabel core as missing.
    const SYLIUS_CORE = new Set([
        'sylius/sylius',
        'sylius/core',
        'sylius/core-bundle',
        'sylius/admin-bundle',
        'sylius/admin-api-bundle',
        'sylius/api-bundle',
        'sylius/shop-bundle',
        'sylius/shop-api-plugin', // legacy: technically a plugin but part of core story
        'sylius/resource',
        'sylius/resource-bundle',
        'sylius/grid',
        'sylius/grid-bundle',
        'sylius/mailer',
        'sylius/mailer-bundle',
        'sylius/attribute',
        'sylius/attribute-bundle',
        'sylius/channel',
        'sylius/channel-bundle',
        'sylius/currency',
        'sylius/currency-bundle',
        'sylius/customer',
        'sylius/customer-bundle',
        'sylius/inventory',
        'sylius/inventory-bundle',
        'sylius/locale',
        'sylius/locale-bundle',
        'sylius/order',
        'sylius/order-bundle',
        'sylius/payment',
        'sylius/payment-bundle',
        'sylius/product',
        'sylius/product-bundle',
        'sylius/promotion',
        'sylius/promotion-bundle',
        'sylius/shipping',
        'sylius/shipping-bundle',
        'sylius/taxation',
        'sylius/taxation-bundle',
        'sylius/taxonomy',
        'sylius/taxonomy-bundle',
        'sylius/theme-bundle',
        'sylius/review',
        'sylius/review-bundle',
        'sylius/addressing',
        'sylius/addressing-bundle',
        'sylius/user',
        'sylius/user-bundle',
        'sylius/ui-bundle',
        'sylius/registry',
        'sylius/state-machine-abstraction',
        'sylius/fixtures-bundle',
    ]);

    const dom = {
        inputScreen: document.getElementById('input-screen'),
        resultsScreen: document.getElementById('results-screen'),
        textarea: document.getElementById('composer-input'),
        singleInput: document.getElementById('single-input'),
        checkBtn: document.getElementById('check-btn'),
        parseError: document.getElementById('parse-error'),
        countNumber: document.getElementById('plugin-count'),
        countLabel: document.getElementById('plugin-count-label'),
        groups: document.getElementById('results-groups'),
        generatedAt: document.querySelector('[data-radar-generated]'),
    };

    // ---------- bootstrap ----------

    fetch(DATA_URL, { cache: 'no-store' })
        .then((r) => {
            if (!r.ok) throw new Error(`plugins.json ${r.status}`);
            return r.json();
        })
        .then((payload) => {
            state.db = new Map(payload.plugins.map((p) => [p.packageName, p]));
            state.generatedAt = payload.generatedAt;
            dom.generatedAt.textContent = formatDate(payload.generatedAt);
            dom.checkBtn.disabled = false;
        })
        .catch((err) => {
            dom.checkBtn.disabled = true;
            showParseError(`Could not load plugin database: ${err.message}. Open this page over http (run \`php -S localhost:8000\`) rather than file:// to bypass browser CORS rules.`);
        });

    dom.checkBtn.disabled = true;

    dom.checkBtn.addEventListener('click', handleCheck);
    dom.singleInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleSingle();
        }
    });
    document.querySelectorAll('[data-radar-back]').forEach((btn) => {
        btn.addEventListener('click', () => showScreen('input'));
    });

    // ---------- handlers ----------

    function handleCheck() {
        clearError();
        const single = dom.singleInput.value.trim();
        if (single) {
            handleSingle();
            return;
        }

        const raw = dom.textarea.value.trim();
        if (!raw) {
            showParseError('Paste a composer.json above, or type a single package name in the input on the right.');
            return;
        }

        let parsed;
        try {
            parsed = JSON.parse(raw);
        } catch (e) {
            showParseError(humanizeJsonError(raw, e));
            return;
        }

        const pkgs = extractPackages(parsed);
        if (pkgs.length === 0) {
            showParseError('No packages found in `require` or `require-dev`. Did you paste a composer.json?');
            return;
        }

        renderResults(classify(pkgs));
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
        const unknownSylius = []; // looks Sylius-adjacent but not in DB
        const other = [];         // non-Sylius composer deps
        const core = [];          // sylius/sylius + monorepo components
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
                if (entry.notes && /in\s*progress|alpha|beta|rc|v2\s*branch|v2\s*branch|work\s*in\s*progress/i.test(entry.notes)) {
                    inProgress.push(row);
                } else if (entry.supports2x) {
                    ready.push(row);
                } else if (entry.supports1x) {
                    notReady.push(row);
                } else {
                    // Has entry but neither flag resolved (missing constraint). Amber bucket.
                    unknownSylius.push({
                        packageName: pkg.name,
                        note: 'Radar has no parseable Sylius constraint for this plugin',
                        homepage: entry.homepage,
                    });
                }
            } else if (looksLikeSylius(pkg.name)) {
                unknownSylius.push({ packageName: pkg.name, note: null, homepage: null });
            } else {
                other.push(pkg.name);
            }
        }

        // Stable, human-friendly sort by downloads desc within each bucket.
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

        if (buckets.detectedSylius) {
            dom.groups.appendChild(renderDetected(buckets.detectedSylius));
        }

        if (buckets.ready.length) {
            dom.groups.appendChild(renderGroup({
                title: 'Ready for 2.x',
                dot: 'green',
                rows: buckets.ready.map(renderReadyRow),
            }));
        }
        if (buckets.inProgress.length) {
            dom.groups.appendChild(renderGroup({
                title: 'In progress',
                dot: 'blue',
                rows: buckets.inProgress.map(renderInProgressRow),
            }));
        }
        if (buckets.notReady.length) {
            dom.groups.appendChild(renderGroup({
                title: 'Not yet ready',
                dot: 'red',
                rows: buckets.notReady.map(renderNotReadyRow),
            }));
        }
        if (buckets.unknownSylius.length) {
            dom.groups.appendChild(renderGroup({
                title: 'Not yet covered by the radar',
                dot: 'amber',
                rows: buckets.unknownSylius.map(renderUnknownRow),
            }));
        }
        if (buckets.core.length) {
            dom.groups.appendChild(renderCore(buckets.core));
        }
        if (buckets.other.length) {
            dom.groups.appendChild(renderOther(buckets.other));
        }

        showScreen('results');
    }

    function renderGroup({ title, dot, rows }) {
        const section = el('section', 'radar__group');
        const h = el('h3', 'radar__group-title');
        h.appendChild(el('span', `radar__dot radar__dot--${dot}`));
        h.appendChild(document.createTextNode(` ${title}`));
        section.appendChild(h);
        const grid = el('div', 'radar__rows');
        rows.forEach((r) => grid.appendChild(r));
        section.appendChild(grid);
        return section;
    }

    function renderReadyRow(entry) {
        const row = el('div', 'radar__row');
        const info = el('div');
        info.appendChild(withText(el('span', 'radar__row-name'), entry.packageName));
        const meta = el('p', 'radar__row-meta');
        meta.appendChild(document.createTextNode('Latest '));
        meta.appendChild(withText(el('code'), entry.latestTag || '?'));
        meta.appendChild(document.createTextNode(' requires '));
        meta.appendChild(withText(el('code', entry.constraintFrom && entry.constraintFrom !== 'sylius/sylius' ? 'is-fallback' : ''), `${entry.constraintFrom || 'sylius/sylius'}: ${entry.syliusConstraint || '?'}`));
        info.appendChild(meta);
        row.appendChild(info);
        row.appendChild(packagistLink(entry.packageName));
        return row;
    }

    function renderInProgressRow(entry) {
        const row = el('div', 'radar__row radar__row--muted');
        const info = el('div');
        info.appendChild(withText(el('span', 'radar__row-name'), entry.packageName));
        const meta = el('p', 'radar__row-meta');
        meta.appendChild(document.createTextNode('Notes: '));
        meta.appendChild(withText(el('span', 'radar__row-note'), entry.notes || 'in progress'));
        info.appendChild(meta);
        row.appendChild(info);
        row.appendChild(packagistLink(entry.packageName));
        return row;
    }

    function renderNotReadyRow(entry) {
        const row = el('div', 'radar__row radar__row--danger');
        const info = el('div');
        info.appendChild(withText(el('span', 'radar__row-name'), entry.packageName));
        const meta = el('p', 'radar__row-meta');
        meta.appendChild(document.createTextNode('Latest '));
        meta.appendChild(withText(el('code'), entry.latestTag || '?'));
        meta.appendChild(document.createTextNode(' still pins '));
        meta.appendChild(withText(el('code', 'is-stale'), `${entry.constraintFrom || 'sylius/sylius'}: ${entry.syliusConstraint || '?'}`));
        info.appendChild(meta);
        row.appendChild(info);
        row.appendChild(packagistLink(entry.packageName));
        return row;
    }

    function renderUnknownRow(entry) {
        const row = el('div', 'radar__row radar__row--unknown');
        row.appendChild(withText(el('span', 'radar__row-name'), entry.packageName));
        row.appendChild(withText(el('span', 'radar__row-tag'), entry.note || 'Not yet covered by the radar'));
        return row;
    }

    function renderDetected(constraint) {
        const wrapper = el('div', 'radar__detected');
        const label = el('span', 'radar__detected-label');
        label.textContent = 'Detected';
        const name = el('code', 'radar__detected-name');
        name.textContent = 'sylius/sylius';
        const ver = el('code', 'radar__detected-version');
        ver.textContent = constraint || '—';
        const suffix = el('span', 'radar__detected-suffix');
        suffix.textContent = 'Checking plugins against Sylius 2.x.';
        wrapper.append(label, name, ver, suffix);
        return wrapper;
    }

    function renderCore(pkgs) {
        const details = el('details', 'radar__other');
        const summary = el('summary', 'radar__other-summary');
        summary.appendChild(document.createTextNode(`Sylius core components (${pkgs.length}) — follow sylius/sylius`));
        summary.appendChild(withText(el('span', 'radar__other-arrow'), '↓'));
        details.appendChild(summary);

        const grid = el('div', 'radar__other-grid');
        pkgs.sort((a, b) => a.name.localeCompare(b.name)).forEach((p) => {
            grid.appendChild(withText(el('div'), p.constraint ? `${p.name}: ${p.constraint}` : p.name));
        });
        details.appendChild(grid);
        return details;
    }

    function renderOther(names) {
        const details = el('details', 'radar__other');
        const summary = el('summary', 'radar__other-summary');
        summary.appendChild(document.createTextNode(`Other PHP dependencies (${names.length})`));
        summary.appendChild(withText(el('span', 'radar__other-arrow'), '↓'));
        details.appendChild(summary);

        const grid = el('div', 'radar__other-grid');
        names.sort().forEach((n) => grid.appendChild(withText(el('div'), n)));
        details.appendChild(grid);
        return details;
    }

    function packagistLink(name) {
        const a = document.createElement('a');
        a.href = `https://packagist.org/packages/${name}`;
        a.target = '_blank';
        a.rel = 'noopener';
        a.className = 'radar__row-link';
        a.textContent = 'View on Packagist ↗';
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
        // Try to locate "at position N" in the message for a rough line/col.
        const m = /position\s+(\d+)/i.exec(err.message || '');
        if (!m) return `Could not parse JSON: ${err.message}`;
        const pos = parseInt(m[1], 10);
        const upto = raw.slice(0, pos);
        const line = upto.split('\n').length;
        const col = pos - upto.lastIndexOf('\n');
        return `Could not parse JSON at line ${line}, column ${col}: ${err.message}`;
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

    function el(tag, className) {
        const node = document.createElement(tag);
        if (className) node.className = className;
        return node;
    }

    function withText(node, text) {
        node.textContent = text;
        return node;
    }
})();
