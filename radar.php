
  <style>
    :root {
    --radar-bg: #070808;
    --radar-bg-2: #0c0c0d;
    --radar-bg-3: #16191c;
    --radar-line: #232326;
    --radar-line-2: #2d2d2d;
    --radar-text: #f4f4f5;
    --radar-text-2: #d4d4d8;
    --radar-text-3: #a1a1aa;
    --radar-text-4: #71717a;
    --radar-text-5: #52525b;
    --radar-yellow: #F6F128;
    --radar-yellow-2: #E5DF20;
    --radar-blue: #2B83F4;
    --radar-green: #22c55e;
    --radar-amber: #f59e0b;
    --radar-red: #ef4444;
    --radar-red-deep: #7f1d1d;
    --radar-track: -0.015em;
    --radar-track-tight: -0.035em;
    --radar-track-wide: 0.18em;
}

.radar-a { color: var(--radar-blue); text-decoration: none; }
.radar-a:hover { text-decoration: underline; }
.radar-code, .radar-mono { font-family: "JetBrains Mono", ui-monospace, SFMono-Regular, Menlo, monospace; }
.radar-button { font-family: inherit; letter-spacing: inherit; cursor: pointer; }
.radar-visually-hidden { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); }

/* ---- top nav ---- */
.radar-topnav {
    position: sticky; top: 0; z-index: 10;
    background: var(--radar-bg);
    border-bottom: 1px dashed var(--radar-line-2);
}
.radar-topnav__inner {
    max-width: 88rem; margin: 0 auto;
    padding: 1.1rem 2rem;
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem;
}
.radar-cw-logo {
    display: inline-flex; align-items: center; gap: 0.6rem;
    font-weight: 400; color: var(--radar-text);
    font-size: 1.05rem; letter-spacing: 0;
    text-transform: lowercase;
}
.radar-cw-logo:hover { text-decoration: none; }
.radar-cw-logo__img { height: 32px; width: auto; display: block; }
.radar-cw-logo__divider { color: var(--radar-text-4); margin: 0 0.6rem; font-weight: 300; }
.radar-cw-logo__lang {
    display: inline-flex; align-items: center; gap: 0.55rem;
    font-size: 0.85rem; font-weight: 400;
    color: var(--radar-text-3); text-transform: uppercase;
    letter-spacing: 0.08em;
}
.radar-cw-logo__lang b { color: var(--radar-text); font-weight: 400; }
.radar-cw-logo__lang span { color: var(--radar-text-4); }
.radar-topnav__links {
    display: flex; gap: 1.6rem; align-items: center;
    font-size: 0.875rem; color: var(--radar-text-3);
}
.radar-topnav__links a { color: var(--radar-text-3); }
.radar-topnav__links a:hover { color: var(--radar-text); text-decoration: none; }
.radar-topnav__cta {
    display: inline-flex; align-items: center; gap: 0.35rem;
    color: var(--radar-text) !important;
    border: 1px solid var(--radar-line-2);
    padding: 0.45rem 0.9rem;
    border-radius: 9999px;
    font-weight: 600;
}
.radar-topnav__cta:hover { border-color: var(--radar-yellow); color: var(--radar-yellow) !important; }
@media (max-width: 720px) {
    .radar-topnav__links .radar-hide-sm { display: none; }
}

/* ---- page ---- */
.radar-page { max-width: 76rem; margin: 0 auto; padding: 2.5rem 2rem 5rem; }

#input-screen, #results-screen { scroll-margin-top: 5rem; }

.radar-blogbar {
    max-width: 88rem; margin: 0 auto;
    padding: 0.8rem 2rem;
    border-bottom: 1px dashed var(--radar-line-2);
    display: flex; justify-content: flex-end; gap: 0.6rem;
    align-items: baseline; flex-wrap: wrap;
    font-size: 0.82rem;
}
.radar-blogbar__tag {
    color: var(--radar-yellow); font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.12em;
    font-size: 0.72rem;
}
.radar-blogbar__title { color: var(--radar-text-2); }
.radar-blogbar__title a { color: var(--radar-text-2); text-decoration: none; }
.radar-blogbar__title a:hover { color: var(--radar-yellow); }

/* ---- hero ---- */
.radar-eyebrow {
    display: inline-flex; align-items: center; gap: 0.5rem;
    font-size: 0.7rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: var(--radar-track-wide);
    color: var(--radar-text-3);
}
.radar-eyebrow__dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: var(--radar-yellow);
    box-shadow: 0 0 0 4px rgba(246,241,40,0.12);
    animation: pulse 2.4s ease-in-out infinite;
}
@keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 4px rgba(246,241,40,0.12); }
    50% { box-shadow: 0 0 0 7px rgba(246,241,40,0.05); }
}

.radar-hero { display: flex; flex-direction: column; gap: 1.25rem; margin-bottom: 2.75rem; }
.radar-hero__banner {
    margin-top: 1rem;
    border-radius: 0.8rem;
    overflow: hidden;
    border: 1px solid var(--radar-line);
    position: relative;
    aspect-ratio: 1920 / 640;
    background: #C55A3C;
}
.radar-hero__banner img {
    width: 100%; height: 100%; display: block; object-fit: cover;
    filter: saturate(0.95) contrast(1.02);
}
.radar-hero__banner-overlay {
    position: absolute; inset: auto 0 0 0;
    padding: 1rem 1.25rem;
    display: flex; justify-content: space-between; align-items: flex-end;
    gap: 1rem;
    background: linear-gradient(to top, rgba(0,0,0,0.55), transparent 80%);
    color: #fff;
}
.radar-hero__banner-caption {
    font-size: 0.75rem; font-weight: 600;
    letter-spacing: var(--radar-track-wide); text-transform: uppercase;
    color: rgba(255,255,255,0.92);
}
.radar-hero__banner-note {
    font-family: "JetBrains Mono", monospace;
    font-size: 0.7rem; color: rgba(255,255,255,0.75);
}
@media (max-width: 640px) {
    .radar-hero__banner-overlay { padding: 0.6rem 0.8rem; }
    .radar-hero__banner-caption { font-size: 0.65rem; }
}
.radar-hero__row {
    display: flex; align-items: flex-end; justify-content: space-between;
    gap: 2rem; flex-wrap: wrap;
}
.radar-hero__title {
    margin: 0;
    font-size: clamp(2.2rem, 5.5vw, 3.75rem);
    font-weight: 700;
    line-height: 1.02;
    letter-spacing: var(--radar-track-tight);
    text-wrap: balance;
    max-width: 28ch;
}
.radar-hero__title em {
    font-style: normal;
    color: var(--radar-yellow);
    position: relative;
    white-space: nowrap;
}
.radar-hero__snapshot {
    flex-shrink: 0;
    background: transparent;
    border: 1px solid var(--radar-line);
    padding: 0.6rem 0.9rem;
    border-radius: 0.5rem;
    min-width: 14rem;
}
.radar-hero__snapshot-row { display: flex; justify-content: space-between; gap: 1rem; }
.radar-hero__snapshot-label {
    font-size: 0.625rem; text-transform: uppercase;
    letter-spacing: var(--radar-track-wide); color: var(--radar-text-4);
}
.radar-hero__snapshot-date { font-size: 0.75rem; color: var(--radar-text-2); }
.radar-hero__snapshot-stats {
    margin-top: 0.5rem; padding-top: 0.5rem;
    border-top: 1px dashed var(--radar-line-2);
    display: flex; gap: 1rem;
    font-family: "JetBrains Mono", monospace;
    font-size: 0.7rem; color: var(--radar-text-3);
}
.radar-hero__snapshot-stats b { color: var(--radar-text); font-weight: 700; }

.radar-hero__tagline {
    margin: 0; font-size: 1.2rem; color: var(--radar-text-3);
    text-wrap: pretty;
}

.radar-hero__jump {
    margin-top: 0.5rem;
}
.radar-hero__jump-btn {
    text-decoration: none;
}
.radar-hero__jump-btn:hover { text-decoration: none; }

/* ---- tabs ---- */
.radar-tabs {
    display: inline-flex; padding: 0.3rem; gap: 0.25rem;
    background: var(--radar-bg-2);
    border: 1px solid var(--radar-line);
    border-radius: 9999px;
    margin-bottom: 1.25rem;
}
.radar-tab {
    appearance: none; background: transparent; border: 0;
    padding: 0.5rem 1.1rem; border-radius: 9999px;
    color: var(--radar-text-3); font-size: 0.82rem; font-weight: 600;
    display: inline-flex; align-items: center; gap: 0.4rem;
}
.radar-tab:hover { color: var(--radar-text); }
.radar-tab[aria-selected="true"] { background: var(--radar-yellow); color: #000; }
.radar-tab[aria-selected="true"]:hover { color: #000; }

/* ---- card ---- */
.radar-card {
    background: var(--radar-bg-3);
    border: 1px solid var(--radar-line-2);
    border-radius: 1.1rem;
    padding: 2rem;
}
@media (min-width: 768px) { .radar-card { padding: 2.5rem; } }

.radar-card__label {
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem; margin-bottom: 0.8rem; flex-wrap: wrap;
}
.radar-card__label-text {
    font-size: 0.72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: var(--radar-track-wide);
    color: var(--radar-text-4);
}
.radar-presets { display: flex; gap: 0.35rem; flex-wrap: wrap; }
.radar-preset {
    appearance: none; background: transparent;
    border: 1px solid var(--radar-line-2);
    color: var(--radar-text-3);
    padding: 0.3rem 0.7rem; border-radius: 9999px;
    font-size: 0.72rem; font-weight: 600;
    font-family: "JetBrains Mono", monospace;
    transition: all .15s;
}
.radar-preset:hover { color: var(--radar-text); border-color: var(--radar-text-4); }
.radar-preset.radar-is-active { background: var(--radar-text); color: #000; border-color: var(--radar-text); }

.radar-textarea {
    width: 100%; height: 16rem;
    background: #000; border: 1px solid #3f3f46;
    border-radius: 0.6rem; padding: 1rem 1.1rem;
    font-family: "JetBrains Mono", monospace;
    font-size: 0.82rem; color: var(--radar-text-2);
    outline: none; resize: vertical;
    line-height: 1.55; tab-size: 2;
    transition: border-color .15s, box-shadow .15s;
}
.radar-textarea::placeholder { color: #3f3f46; }
.radar-textarea:focus {
    border-color: var(--radar-blue);
    box-shadow: 0 0 0 2px rgba(43,131,244,0.3);
}

.radar-actions {
    display: flex; flex-direction: column; gap: 1.25rem;
    margin-top: 1.5rem;
}
@media (min-width: 720px) {
    .radar-actions { flex-direction: row; align-items: flex-end; justify-content: space-between; }
}
.radar-single { flex: 1; }
.radar-single__label {
    display: block; font-size: 0.625rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: var(--radar-track-wide);
    color: var(--radar-text-4); margin-bottom: 0.35rem;
}
.radar-single__input {
    width: 100%; background: var(--radar-bg-2);
    border: 1px solid #3f3f46; border-radius: 9999px;
    padding: 0.6rem 1rem; font-size: 0.88rem;
    color: var(--radar-text); outline: none;
    font-family: "JetBrains Mono", monospace;
    transition: border-color .15s;
}
.radar-single__input:focus { border-color: var(--radar-yellow); }

.radar-privacy-note {
    color: var(--radar-text-5); font-size: 0.72rem; margin: 0;
    max-width: 20rem; text-align: right;
}
.radar-try-examples {
    color: var(--radar-text-5); font-size: 0.72rem; margin: 0;
}

/* ---- buttons ---- */
.radar-btn {
    display: inline-flex; align-items: center; justify-content: center;
    gap: 0.4rem;
    font-weight: 700; font-size: 0.95rem;
    border-radius: 9999px;
    padding: 0.75rem 2rem;
    border: 2px solid transparent;
    transition: background-color .15s, color .15s, transform .15s;
}
.radar-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.radar-btn--primary { background: var(--radar-yellow); color: #000; }
.radar-btn--primary:hover:not(:disabled) { background: var(--radar-yellow-2); }
.radar-btn--primary:active { transform: translateY(1px); }
.radar-btn--secondary {
    background: transparent; color: var(--radar-yellow);
    border-color: var(--radar-yellow);
}
.radar-btn--secondary:hover:not(:disabled) { background: var(--radar-yellow); color: #000; }
.radar-btn--ghost {
    background: transparent; color: var(--radar-text-3); border: 0;
    padding: 0.5rem 0.75rem;
}
.radar-btn--ghost:hover:not(:disabled) { color: var(--radar-text); }

/* ---- parse error ---- */
.radar-error {
    margin-top: 1rem; padding: 0.8rem 1rem;
    background: rgba(220,38,38,0.1);
    border: 1px solid rgba(220,38,38,0.35);
    border-left: 3px solid var(--radar-red);
    border-radius: 0.5rem; color: #fca5a5;
    font-family: "JetBrains Mono", monospace; font-size: 0.8rem;
}

/* ---- CTA box ---- */
.radar-cta-box {
    margin-top: 2.5rem;
    background: var(--radar-yellow);
    color: #000;
    border-radius: 1.1rem;
    padding: 1.75rem 2rem;
    display: flex; align-items: center; justify-content: space-between;
    gap: 1.5rem; flex-wrap: wrap;
}
.radar-cta-box__body { flex: 1 1 22rem; min-width: 16rem; }
.radar-cta-box__title {
    margin: 0 0 0.4rem 0;
    font-size: 1.4rem; font-weight: 700;
    letter-spacing: var(--radar-track-tight);
    color: #000;
}
.radar-cta-box__text {
    margin: 0; font-size: 0.98rem;
    color: #1a1a1a; text-wrap: pretty;
    max-width: 52ch;
}
.radar-cta-box__btn {
    display: inline-flex; align-items: center; justify-content: center;
    background: #000; color: var(--radar-yellow) !important;
    font-weight: 700; font-size: 0.95rem;
    padding: 0.85rem 1.6rem;
    border-radius: 9999px;
    text-decoration: none;
    transition: background-color .15s, color .15s, transform .15s;
    white-space: nowrap;
    flex-shrink: 0;
}
.radar-cta-box__btn:hover {
    background: #1a1a1a;
    text-decoration: none;
    transform: translateY(-1px);
}

/* ---- footer ---- */
.radar-pagefoot {
    margin-top: 3rem; padding-top: 1.8rem;
    border-top: 1px solid var(--radar-line);
    display: flex; justify-content: space-between; gap: 1rem;
    flex-wrap: wrap; color: var(--radar-text-4); font-size: 0.85rem;
}
.radar-pagefoot a { color: var(--radar-text-2); text-decoration: underline; text-underline-offset: 4px; }
.radar-pagefoot a:hover { color: var(--radar-yellow); }
.radar-pagefoot__heart { color: var(--radar-yellow); }
.radar-pagefoot__domain { color: var(--radar-text-5); font-size: 0.75rem; }

/* ---- results ---- */
.radar-res-header {
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem; margin-bottom: 1.75rem; flex-wrap: wrap;
}
.radar-res-actions { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
.radar-res-count { display: inline-flex; align-items: baseline; gap: 0.7rem; }
.radar-res-count__num {
    background: var(--radar-yellow); color: #000;
    font-weight: 700; padding: 0.2rem 0.7rem;
    border-radius: 0.35rem;
    font-family: "JetBrains Mono", monospace;
    font-size: 1.05rem;
}
.radar-res-count__label { font-size: 1.2rem; font-weight: 700; letter-spacing: var(--radar-track-tight); }

/* summary strip */
.radar-summary {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.5rem;
    margin-bottom: 1.75rem;
}
@media (max-width: 720px) { .radar-summary { grid-template-columns: repeat(2, 1fr); } }
.radar-summary__cell {
    background: var(--radar-bg-2); border: 1px solid var(--radar-line);
    border-radius: 0.6rem; padding: 0.9rem 1rem;
    display: flex; flex-direction: column; gap: 0.25rem;
    position: relative; overflow: hidden;
}
.radar-summary__cell::before {
    content: ""; position: absolute; left: 0; top: 0; bottom: 0;
    width: 3px; background: var(--radar-c, var(--radar-text-5));
}
.radar-summary__cell[data-tone="ready"] { --radar-c: var(--radar-green); }
.radar-summary__cell[data-tone="progress"] { --radar-c: #60a5fa; }
.radar-summary__cell[data-tone="notready"] { --radar-c: var(--radar-red); }
.radar-summary__cell[data-tone="unknown"] { --radar-c: var(--radar-amber); }
.radar-summary__num {
    font-family: "JetBrains Mono", monospace;
    font-size: 1.5rem; font-weight: 700;
    color: var(--radar-text); letter-spacing: var(--radar-track-tight);
}
.radar-summary__lbl {
    font-size: 0.65rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: var(--radar-track-wide);
    color: var(--radar-text-3);
}

.radar-detected {
    display: flex; flex-wrap: wrap; align-items: baseline; gap: 0.5rem;
    padding: 0.8rem 1rem;
    border: 1px solid #3f3f46;
    border-left: 3px solid var(--radar-yellow);
    background: rgba(246,241,40,0.04);
    border-radius: 0.5rem;
    margin-bottom: 1.75rem;
}
.radar-detected__lbl {
    text-transform: uppercase; font-size: 0.625rem;
    letter-spacing: var(--radar-track-wide); color: var(--radar-text-3);
    font-weight: 700;
}
.radar-detected__name { font-family: "JetBrains Mono", monospace; color: var(--radar-text-2); }
.radar-detected__ver { font-family: "JetBrains Mono", monospace; color: var(--radar-yellow); font-weight: 700; }
.radar-detected__suffix { color: var(--radar-text-3); font-size: 0.88rem; }

.radar-group { margin-bottom: 2rem; }
.radar-group__title {
    display: flex; align-items: center; gap: 0.55rem;
    margin: 0 0 0.9rem 0; color: var(--radar-text-2);
    font-size: 0.72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: var(--radar-track-wide);
}
.radar-group__title .radar-count {
    color: var(--radar-text-4); font-weight: 500;
    letter-spacing: 0; text-transform: none;
    font-family: "JetBrains Mono", monospace;
    font-size: 0.72rem;
}
.radar-dot { display: inline-block; width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.radar-dot--green { background: var(--radar-green); box-shadow: 0 0 0 3px rgba(34,197,94,0.15); }
.radar-dot--blue { background: #60a5fa; box-shadow: 0 0 0 3px rgba(96,165,250,0.15); }
.radar-dot--red { background: var(--radar-red); box-shadow: 0 0 0 3px rgba(239,68,68,0.15); }
.radar-dot--amber { background: var(--radar-amber); box-shadow: 0 0 0 3px rgba(245,158,11,0.15); }

.radar-rows { display: grid; gap: 0.5rem; }
.radar-row {
    background: var(--radar-bg-2); border: 1px solid var(--radar-line);
    border-radius: 0.8rem;
    padding: 1rem 1.1rem;
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem; flex-wrap: wrap;
    transition: border-color .15s, transform .15s;
}
.radar-row:hover { border-color: var(--radar-line-2); }
.radar-row--danger { border-left: 3px solid var(--radar-red-deep); }
.radar-row--muted { opacity: 0.92; }
.radar-row--unknown {
    background: rgba(39,39,42,0.3);
    border-style: dashed;
}
.radar-row__name {
    font-weight: 700; font-size: 1.02rem;
    display: block; letter-spacing: var(--radar-track-tight);
}
.radar-row__meta {
    margin: 0.3rem 0 0; color: var(--radar-text-4);
    font-size: 0.86rem;
}
.radar-row__meta code { color: var(--radar-text-2); }
.radar-row__meta code.radar-is-stale { color: #fca5a5; }
.radar-row__meta code.radar-is-fallback { color: var(--radar-text-3); }
.radar-row__note { color: #93c5fd; font-style: italic; font-weight: 500; }
.radar-row__link { font-size: 0.82rem; white-space: nowrap; color: var(--radar-text-3); }
.radar-row__link:hover { color: var(--radar-yellow); text-decoration: none; }
.radar-row--unknown .radar-row__name {
    font-size: 0.88rem;
    font-family: "JetBrains Mono", monospace;
    font-weight: 500;
}
.radar-row__tag { font-size: 0.7rem; color: var(--radar-text-4); font-style: italic; }
.radar-row__downloads {
    font-family: "JetBrains Mono", monospace;
    font-size: 0.7rem; color: var(--radar-text-5);
    margin-top: 0.15rem;
}

.radar-row__migrate {
    margin: 0.4rem 0 0;
    padding: 0.45rem 0.7rem;
    background: rgba(43, 131, 244, 0.06);
    border: 1px solid rgba(43, 131, 244, 0.22);
    border-left: 2px solid var(--radar-blue);
    border-radius: 0.4rem;
    font-size: 0.82rem;
    color: var(--radar-text-2);
    display: flex; flex-wrap: wrap; align-items: baseline;
    gap: 0.35rem;
}
.radar-row__migrate-arrow {
    color: var(--radar-blue);
    font-family: "JetBrains Mono", monospace;
    font-weight: 700;
}
.radar-row__migrate-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: var(--radar-track-wide);
    color: var(--radar-text-3);
    font-weight: 700;
}
.radar-row__migrate-target {
    font-family: "JetBrains Mono", monospace;
    color: var(--radar-text);
    font-weight: 700;
    text-decoration: none;
    display: inline-flex; align-items: baseline; gap: 0.25rem;
    border-bottom: 1px solid transparent;
    transition: border-color .15s, color .15s;
}
.radar-row__migrate-target:hover,
.radar-row__migrate-target:focus-visible {
    color: var(--radar-yellow);
    border-bottom-color: var(--radar-yellow);
    text-decoration: none;
}
.radar-row__migrate-target code {
    color: inherit;
    font-family: inherit;
    font-weight: inherit;
}
.radar-row__migrate-target-arrow {
    font-size: 0.72rem;
    color: var(--radar-blue);
    transition: color .15s;
}
.radar-row__migrate-target:hover .radar-row__migrate-target-arrow,
.radar-row__migrate-target:focus-visible .radar-row__migrate-target-arrow {
    color: var(--radar-yellow);
}
.radar-row__migrate-tags {
    font-family: "JetBrains Mono", monospace;
    font-size: 0.72rem;
    color: var(--radar-text-4);
}
.radar-row__migrate-reason {
    display: block;
    flex-basis: 100%;
    color: var(--radar-text-3);
    font-size: 0.8rem;
    margin-top: 0.2rem;
}
.radar-row__migrate--alternative {
    background: rgba(148, 163, 184, 0.05);
    border-color: rgba(148, 163, 184, 0.2);
    border-left-color: var(--radar-text-4);
}

.radar-row__progress {
    margin: 0.4rem 0 0;
    padding: 0.45rem 0.7rem;
    background: rgba(96, 165, 250, 0.06);
    border: 1px solid rgba(96, 165, 250, 0.22);
    border-left: 2px solid #60a5fa;
    border-radius: 0.4rem;
    font-size: 0.82rem;
    color: var(--radar-text-2);
    display: flex; flex-wrap: wrap; align-items: baseline;
    gap: 0.35rem;
}
.radar-row__prerelease {
    margin: 0.4rem 0 0;
    padding: 0.4rem 0.7rem;
    background: rgba(251, 191, 36, 0.06);
    border: 1px solid rgba(251, 191, 36, 0.22);
    border-left: 2px solid #fbbf24;
    border-radius: 0.4rem;
    font-size: 0.78rem;
    color: var(--radar-text-2);
}
.radar-row__prerelease code { color: #fbbf24; }
.radar-row__progress-arrow {
    color: #60a5fa;
    font-family: "JetBrains Mono", monospace;
    font-weight: 700;
}
.radar-row__progress-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: var(--radar-track-wide);
    color: var(--radar-text-3);
    font-weight: 700;
}
.radar-row__progress-link {
    font-family: "JetBrains Mono", monospace;
    color: var(--radar-text);
    font-weight: 700;
    text-decoration: none;
    display: inline-flex; align-items: baseline; gap: 0.25rem;
    border-bottom: 1px solid transparent;
    transition: border-color .15s, color .15s;
}
.radar-row__progress-link:hover,
.radar-row__progress-link:focus-visible {
    color: var(--radar-yellow);
    border-bottom-color: var(--radar-yellow);
    text-decoration: none;
}
.radar-row__progress-link code { color: inherit; font-family: inherit; font-weight: inherit; }
.radar-row__progress-link-arrow {
    font-size: 0.72rem;
    color: #60a5fa;
    transition: color .15s;
}
.radar-row__progress-link:hover .radar-row__progress-link-arrow,
.radar-row__progress-link:focus-visible .radar-row__progress-link-arrow { color: var(--radar-yellow); }
.radar-row__progress-updated {
    font-family: "JetBrains Mono", monospace;
    font-size: 0.72rem;
    color: var(--radar-text-4);
}
.radar-row__progress-stale {
    font-family: "JetBrains Mono", monospace;
    font-size: 0.72rem;
    color: var(--radar-amber);
}

/* collapsible "other" */
.radar-other {
    border-top: 1px solid var(--radar-line);
    padding-top: 1.5rem; margin-top: 1.5rem;
}
.radar-other__summary {
    cursor: pointer; list-style: none;
    display: flex; align-items: center; justify-content: space-between;
    color: var(--radar-text-4); font-weight: 700; font-size: 0.72rem;
    text-transform: uppercase; letter-spacing: var(--radar-track-wide);
}
.radar-other__summary::-webkit-details-marker { display: none; }
.radar-other__arrow { transition: transform .2s; }
.radar-other[open] .radar-other__arrow { transform: rotate(180deg); }
.radar-other__grid {
    margin-top: 1rem;
    display: grid; grid-template-columns: repeat(auto-fill, minmax(14rem, 1fr));
    gap: 0.4rem;
    font-family: "JetBrains Mono", monospace;
    font-size: 0.72rem; color: var(--radar-text-4);
}
.radar-other__grid > div {
    background: var(--radar-bg); border: 1px solid var(--radar-line);
    border-radius: 0.35rem; padding: 0.5rem 0.7rem;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}

.radar-center { display: flex; justify-content: center; padding-top: 1.5rem; }
.radar-is-hidden { display: none !important; }

/* loader row */
.radar-loader__dots { display: inline-flex; gap: 3px; }
.radar-loader__dots span {
    width: 5px; height: 5px; border-radius: 50%;
    background: var(--radar-yellow);
    animation: jump 1s infinite ease-in-out;
}
.radar-loader__dots span:nth-child(2) { animation-delay: .15s; }
.radar-loader__dots span:nth-child(3) { animation-delay: .3s; }
@keyframes jump {
    0%, 80%, 100% { transform: translateY(0); opacity: 0.35; }
    40% { transform: translateY(-4px); opacity: 1; }
}

/* print-only report header; hidden on screen, revealed in @media print */
.radar-print-header { display: none; }

/* ---- print: results report only ---- */
@page { margin: 1.5cm; }
@media print {
    /* Show only the results report; hide the rest of the page chrome. */
    body * { visibility: hidden; }
    #results-screen, #results-screen * { visibility: visible; }
    #results-screen {
        position: absolute;
        top: 0; left: 0; width: 100%;
    }

    /* Drop interactive-only controls and the marketing CTA — neither
       belongs on a printed report. */
    .radar-res-actions,
    .radar-center,
    .radar-cta-box { display: none !important; }

    /* Reveal the print-only report header. */
    .radar-print-header {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--radar-text);
    }
    .radar-print-header__title {
        font-size: 1.15rem; font-weight: 700;
        letter-spacing: var(--radar-track-tight);
    }
    .radar-print-header__sub {
        font-family: "JetBrains Mono", monospace;
        font-size: 0.75rem; color: var(--radar-text-4);
    }

    /* Repaint the dark theme as an ink-friendly light report so the
       light-on-dark text stays readable once browsers drop backgrounds. */
    :root {
        --radar-bg: #ffffff;
        --radar-bg-2: #ffffff;
        --radar-bg-3: #ffffff;
        --radar-line: #d4d4d8;
        --radar-line-2: #a1a1aa;
        --radar-text: #18181b;
        --radar-text-2: #27272a;
        --radar-text-3: #3f3f46;
        --radar-text-4: #52525b;
        --radar-text-5: #71717a;
    }
    .radar-row__note { color: #1d4ed8; }

    /* Keep the colour-coded accents (count badge, status dots, summary
       stripes) — they carry the at-a-glance verdict. */
    .radar-res-count__num,
    .radar-dot,
    .radar-summary__cell::before {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* The collapsible lists are forced open via beforeprint, so drop the
       now-pointless disclosure arrow. */
    .radar-other__arrow { display: none; }
}

  </style>
  
  <main class="radar-page">

  <header class="radar-hero">

    <div class="radar-hero__row">
      <aside class="radar-hero__snapshot" aria-label="Database snapshot">
        <div class="radar-hero__snapshot-row">
          <span class="radar-hero__snapshot-label">Radar snapshot</span>
          <span class="radar-hero__snapshot-date radar-mono" data-snapshot-date>—</span>
        </div>
        <div class="radar-hero__snapshot-stats">
          <span><b data-stat-total>—</b> plugins tracked</span>
          <span><b data-stat-ready>—</b> on 2.x</span>
        </div>
      </aside>
    </div>

    <p class="radar-hero__tagline">
      Paste your <code class="radar-code">composer.json</code>. We parse every dependency against a snapshot of the Sylius plugin ecosystem, so you stop reading constraint strings one by one to scope a migration.
    </p>
  </header>

  <section id="input-screen" aria-labelledby="input-title">
    <h2 id="input-title" class="radar-visually-hidden">Input</h2>

    <div class="radar-tabs" role="tablist">
      <button class="radar-button radar-tab" role="tab" aria-selected="true" data-tab="paste">📋 Paste composer.json</button>
      <button class="radar-button radar-tab" role="tab" aria-selected="false" data-tab="single">🔍 Check single package</button>
    </div>

    <div class="radar-card">

      <div data-panel="paste">
        <div class="radar-card__label">
          <span class="radar-card__label-text">Your composer.json</span>
          <div class="radar-presets" role="group" aria-label="Demo presets">
            <button class="radar-button radar-preset" data-preset="empty">empty</button>
            <button class="radar-button radar-preset radar-is-active" data-preset="b2c">B2C shop</button>
            <button class="radar-button radar-preset" data-preset="b2b">B2B platform</button>
            <button class="radar-button radar-preset" data-preset="legacy">legacy 1.13</button>
          </div>
        </div>
        <textarea id="composer-input" class="radar-textarea" spellcheck="false" autocomplete="off"
                  placeholder='{ "require": { "sylius/sylius": "^1.14", "bitbag/cms-plugin": "^1.13" } }'></textarea>
        <div id="parse-error" class="radar-error radar-is-hidden" role="alert"></div>
        <div class="radar-actions">
          <button id="check-btn" type="button" class="radar-button radar-btn radar-btn--primary">
            Check compatibility →
          </button>
          <p class="radar-mono radar-privacy-note">
            Nothing leaves your browser.<br>Parsing happens client-side.
          </p>
        </div>
      </div>

      <div data-panel="single" class="radar-is-hidden">
        <div class="radar-single">
          <label for="single-input" class="radar-single__label">Package name</label>
          <input id="single-input" type="text" class="radar-single__input"
                 placeholder="vendor/package-name" autocomplete="off" spellcheck="false">
        </div>
        <div class="radar-actions">
          <button id="single-btn" type="button" class="radar-button radar-btn radar-btn--primary">Look it up →</button>
          <p class="radar-mono radar-try-examples">
            Try: <a class="radar-a" href="#" data-try="bitbag/cms-plugin">bitbag/cms-plugin</a> ·
            <a class="radar-a" href="#" data-try="sylius/refund-plugin">sylius/refund-plugin</a>
          </p>
        </div>
      </div>

    </div>

    <aside class="radar-cta-box" aria-labelledby="cta-title">
      <div class="radar-cta-box__body">
        <h3 id="cta-title" class="radar-cta-box__title">Planning a Sylius 2 migration?</h3>
        <p class="radar-cta-box__text">
          The radar gives you a snapshot. We can give you a plan — scope, sequencing, and the real cost of every red row above.
        </p>
      </div>
      <a class="radar-a radar-cta-box__btn" href="https://commerceweavers.com/contact">
        Schedule a free consultation ↗
      </a>
    </aside>
  </section>

  <section id="results-screen" class="radar-is-hidden" aria-labelledby="results-title">
    <h2 id="results-title" class="radar-visually-hidden">Results</h2>

    <div class="radar-print-header" aria-hidden="true">
      <span class="radar-print-header__title">Sylius 2.x Compatibility Report</span>
      <span class="radar-print-header__sub">Commerce Weavers · Compatibility Radar</span>
    </div>

    <div class="radar-res-header">
      <div class="radar-res-count">
        <span id="plugin-count" class="radar-res-count__num">0</span>
        <span id="plugin-count-label" class="radar-res-count__label">Sylius plugins identified</span>
      </div>
      <div class="radar-res-actions">
        <button type="button" class="radar-button radar-btn radar-btn--ghost" data-radar-print>🖨 Print report</button>
        <button type="button" class="radar-button radar-btn radar-btn--ghost" data-radar-back>← Check another</button>
      </div>
    </div>

    <div id="summary-strip" class="radar-summary radar-is-hidden"></div>

    <div id="results-groups"></div>

    <div class="radar-center">
      <button type="button" class="radar-button radar-btn radar-btn--secondary" data-radar-back>Check another composer.json</button>
    </div>

    <aside class="radar-cta-box" aria-labelledby="cta-results-title">
      <div class="radar-cta-box__body">
        <h3 id="cta-results-title" class="radar-cta-box__title">Want a human read on these results?</h3>
        <p class="radar-cta-box__text">
          We'll walk through the red rows with you — what's risky, what's a swap, what's a rebuild — and turn the radar into a migration plan.
        </p>
      </div>
      <a class="radar-a radar-cta-box__btn" href="https://commerceweavers.com/contact">
        Schedule a free consultation ↗
      </a>
    </aside>
  </section>

</main>

<script>
  (() => {
    'use strict';

    const DATA_URL = <?= json_encode(($radarBase ?? '.') . '/plugins.json', JSON_UNESCAPED_SLASHES) ?>;
    const TRACKER_STATE_URL = <?= json_encode(($radarBase ?? '.') . '/tracker-state.json', JSON_UNESCAPED_SLASHES) ?>;

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
    "sylius/sylius": "^1.14",
    "sylius/refund-plugin": "^1.6",
    "sylius/paypal-plugin": "^1.7",
    "sylius/invoicing-plugin": "^1.1",
    "bitbag/cms-plugin": "^4.0",
    "bitbag/wishlist-plugin": "^4.0",
    "stefandoorn/sitemap-plugin": "^2.0",
    "monsieurbiz/sylius-rich-editor-plugin": "^2.0",
    "monsieurbiz/sylius-search-plugin": "^3.0",
    "acme/sylius-custom-checkout-plugin": "^1.0",
    "symfony/framework-bundle": "^6.4",
    "doctrine/orm": "^2.15",
    "sylius-labs/coding-standard": "^4.3",
    "twig/twig": "^3.8"
  }
}`,
        b2b: `{
  "require": {
    "sylius/sylius": "^1.14",
    "sylius/plus": "^1.13",
    "sylius/refund-plugin": "^1.6",
    "sylius/invoicing-plugin": "^1.1",
    "bitbag/elasticsearch-plugin": "^4.0",
    "friendsofsylius/sylius-import-export-plugin": "~0.27",
    "synolia/sylius-scheduler-command-plugin": "^3.8",
    "commerce-weavers/sylius-tpay-plugin": "^1.0",
    "api-platform/core": "^3.2",
    "symfony/messenger": "^6.4"
  }
}`,
        legacy: `{
  "require": {
    "sylius/sylius": "^1.13",
    "bitbag/cms-plugin": "^1.13",
    "bitbag/sylius-adyen-plugin": "^1.13",
    "setono/sylius-feed-plugin": "^0.6",
    "setono/sylius-redirect-plugin": "^1.0",
    "stefandoorn/sitemap-plugin": "^1.8",
    "symfony/framework-bundle": "^5.4"
  }
}`,
    };

    // Known package handovers / takeovers by the Sylius core team.
    // When a user's composer.json pins one of these older names, surface the
    // Sylius-maintained successor so the upgrade path is visible at a glance.
    //
    //  urgency:
    //    'replace'     — community package handed over; migrate to Sylius.
    //    'alternative' — Sylius also publishes one; old one still supported.
    //
    // Verified against Packagist on 2026-04-21. Stripe takeover intentionally
    // omitted until sylius/stripe-plugin lands on Packagist; flux-se users
    // should keep their current plugin per CW guidance.
    // Sylius-adjacent packages that carry no Sylius runtime dependency —
    // coding standards, Rector configs, Behat extensions, and the like.
    // By default they classify as "Not yet covered" (no parseable
    // sylius/sylius constraint), which misleads viewers: these work
    // with any Sylius version, so the upgrade doesn't touch them.
    const VERSION_AGNOSTIC = new Set([
        'sylius-labs/coding-standard',
        'sylius-labs/suite-tags-extension',
        'sylius/sylius-rector',
        'setono/code-quality-pack',
    ]);

    // Plugins with visible Sylius 2.x work in flight. Each entry cites a
    // specific PR or branch the viewer can audit. Validated daily by
    // bin/verify-trackers.php — `lastUpdate` and `stale` are overridden at
    // runtime from tracker-state.json (the sidecar generated by that
    // script), so the values below are seed defaults; edit only when adding
    // or removing entries.
    const IN_PROGRESS = {
        'flux-se/sylius-payum-stripe-plugin': {
            summary: 'Open 2.x PR on the (now Sylius-maintained) repo: "Sylius 2.0"',
            tracker: { type: 'pr', url: 'https://github.com/Sylius/PayumStripePlugin/pull/69', label: 'PR #69' },
            lastUpdate: '2026-02-06',
        },
        'loevgaard/sylius-brand-plugin': {
            summary: 'Sylius 2.x work ships as v3.0.0-alpha series on the 3.x branch',
            tracker: { type: 'branch', url: 'https://github.com/loevgaard/SyliusBrandPlugin/tree/3.x', label: '3.x branch' },
            lastUpdate: '2026-05-12',
        },
        'monsieurbiz/sylius-advanced-option-plugin': {
            summary: 'Open 2.x PR: "Sylius 2.0 Available"',
            tracker: { type: 'pr', url: 'https://github.com/monsieurbiz/SyliusAdvancedOptionPlugin/pull/21', label: 'PR #21' },
            lastUpdate: '2026-01-21',
        },
        'setono/sylius-feed-plugin': {
            summary: 'Active 2.x PR in review',
            tracker: { type: 'pr', url: 'https://github.com/Setono/SyliusFeedPlugin/pull/109', label: 'PR #109' },
            lastUpdate: '2026-02-25',
        },
        'sherlockode/sylius-mondial-relay-plugin': {
            summary: 'Dedicated feature/sylius-2-compatibility branch',
            tracker: { type: 'branch', url: 'https://github.com/sherlockode/SyliusMondialRelayPlugin/tree/feature/sylius-2-compatibility', label: 'feature/sylius-2-compatibility' },
            lastUpdate: '2025-12-11',
        },
    };

    // Evidence for `directDescendant: true` is GitHub contributor-count
    // matching between the two repos — identical counts imply imported git
    // history, i.e. same underlying commits. `null` means partial overlap
    // only, so drop-in replacement is not guaranteed.
    const MIGRATIONS = {
        'bitbag/cms-plugin': {
            target: 'sylius/cms-plugin',
            urgency: 'replace',
            directDescendant: true,
            reason: "BitBag's repo declares 1.x-only and points to Sylius for 2.x. Shared git-history contributors confirm the codebase carried over.",
        },
        'bitbag/wishlist-plugin': {
            target: 'sylius/wishlist-plugin',
            urgency: 'replace',
            directDescendant: true,
            reason: "BitBag's repo declares 1.x-only and points to Sylius for 2.x. Shared git-history contributors confirm the codebase carried over.",
        },
        'bitbag/sylius-adyen-plugin': {
            target: 'sylius/adyen-plugin',
            urgency: 'replace',
            directDescendant: null, // partial contributor overlap only
            reason: 'Sylius publishes an official Adyen plugin. Contributor overlap with BitBag is partial — expect some migration work, not a drop-in swap.',
        },
        'mollie/sylius-plugin': {
            target: 'sylius/mollie-plugin',
            urgency: 'replace',
            directDescendant: true,
            reason: 'Mollie archived their repo and redirects to Sylius. Shared git-history contributors confirm the codebase carried over, with further rework by the Sylius team.',
        },
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
        // 2.x-era monorepo packages. Kept in sync with bin/sylius_core_packages.php
        // via `php bin/smoke.php --core-drift`.
        'sylius/twig-hooks',
        'sylius/twig-extra',
        'sylius/admin-ui',
        'sylius/bootstrap-admin-ui',
        'sylius/ui-translations',
        'sylius/calendar',
        'sylius/flow-bundle',
        'sylius/money-bundle',
        'sylius/sylius-rector',
        'sylius/storage',
        'sylius/translation',
        'sylius/translation-bundle',
        'sylius/pdf-generation-bundle',
        'sylius/import-export-bundle',
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
        // Fetch plugins + tracker state in parallel. The sidecar is optional —
        // when missing (e.g. file:// preview, or workflow hadn't run yet),
        // the hand-curated IN_PROGRESS values stay authoritative.
        const [dbResult, trackerResult] = await Promise.allSettled([
            fetch(DATA_URL, { cache: 'no-store' }).then((r) => {
                if (!r.ok) throw new Error('http ' + r.status);
                return r.json();
            }),
            fetch(TRACKER_STATE_URL, { cache: 'no-cache' }).then((r) => r.ok ? r.json() : null),
        ]);
        if (trackerResult.status === 'fulfilled' && trackerResult.value) {
            mergeTrackerState(trackerResult.value);
        } else if (trackerResult.status === 'rejected') {
            console.warn('tracker-state.json fetch failed:', trackerResult.reason);
        }
        if (dbResult.status === 'fulfilled') {
            useDb(dbResult.value);
        } else {
            useDb(EMBEDDED_DB);
        }
        applyPreset('b2c');
    })();

    function mergeTrackerState(payload) {
        // Sidecar shape: { generatedAt, packages: { "vendor/pkg": { lastUpdate?, stale? } } }
        // Only override the dynamic fields; tracker identity (type/url/label),
        // summary, and curated content all stay sourced from IN_PROGRESS.
        const packages = (payload && payload.packages) || {};
        for (const [pkg, override] of Object.entries(packages)) {
            const editorial = IN_PROGRESS[pkg];
            if (!editorial) continue;
            if (override.lastUpdate !== undefined) editorial.lastUpdate = override.lastUpdate;
            if (override.stale !== undefined) editorial.stale = override.stale;
        }
        state.trackerStateAt = payload && payload.generatedAt || null;
    }

    // ---------- wiring ----------

    $$('[data-tab]').forEach((t) => t.addEventListener('click', () => switchTab(t.dataset.tab)));
    $$('[data-preset]').forEach((b) => b.addEventListener('click', () => applyPreset(b.dataset.preset)));
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
    $$('[data-radar-print]').forEach((b) => b.addEventListener('click', () => window.print()));

    // Force the collapsible "core" / "other" lists open while printing so the
    // full dependency inventory lands on paper, then restore the on-screen
    // state. Only the ones we opened are re-collapsed, so a panel the user
    // expanded by hand stays open.
    window.addEventListener('beforeprint', () => {
        $$('.radar-other').forEach((d) => {
            if (!d.open) { d.dataset.printForced = '1'; d.open = true; }
        });
    });
    window.addEventListener('afterprint', () => {
        $$('.radar-other').forEach((d) => {
            if (d.dataset.printForced) { d.open = false; delete d.dataset.printForced; }
        });
    });

    function switchTab(which) {
        $$('[data-tab]').forEach((t) => t.setAttribute('aria-selected', t.dataset.tab === which ? 'true' : 'false'));
        $$('[data-panel]').forEach((p) => p.classList.toggle('radar-is-hidden', p.dataset.panel !== which));
    }

    function applyPreset(name) {
        dom.textarea.value = PRESETS[name] ?? '';
        $$('[data-preset]').forEach((b) => b.classList.toggle('radar-is-active', b.dataset.preset === name));
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
        dom.checkBtn.innerHTML = 'Scanning… <span class="radar-loader__dots"><span></span><span></span><span></span></span>';
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
            if (VERSION_AGNOSTIC.has(pkg.name)) {
                ready.push({
                    packageName: pkg.name,
                    userConstraint: pkg.constraint,
                    notes: 'Development-time tool with no Sylius runtime dependency — carries no upgrade risk.',
                });
                continue;
            }
            const migration = MIGRATIONS[pkg.name] ? resolveMigration(pkg.name) : null;
            const progress = IN_PROGRESS[pkg.name] ?? null;
            const entry = state.db.get(pkg.name);
            if (entry) {
                const row = { ...entry, userConstraint: pkg.constraint, migration, progress };
                const notesInProgress = entry.notes && /\b(in\s*progress|alpha|beta|rc|v2\s*branch|work\s*in\s*progress)\b/i.test(entry.notes);
                if (entry.prereleaseOnly) {
                    // Plugin has only prerelease tags (alpha/beta/rc). The
                    // resolver carries the prerelease's constraint for display
                    // but never inherits supports2x from it, so we route by
                    // editorial signal alone — In Progress if curated,
                    // otherwise Not yet ready (we know there's no stable to
                    // recommend).
                    if (progress || notesInProgress) {
                        inProgress.push(row);
                    } else {
                        notReady.push(row);
                    }
                } else if ((progress || notesInProgress) && !entry.supports2x) {
                    inProgress.push(row);
                } else if (entry.supports2x) {
                    ready.push(row);
                } else if (entry.prereleaseTargets2x) {
                    // Stable still pins Sylius 1.x but a newer prerelease ships
                    // Sylius 2.x. The maintainer is in flight; surface it as
                    // In Progress so the customer can see the alpha/RC and
                    // decide whether to ride it. No editorial entry required —
                    // the Packagist snapshot is the source of truth.
                    inProgress.push(row);
                } else if (entry.supports1x) {
                    notReady.push(row);
                } else {
                    unknownSylius.push({
                        packageName: pkg.name,
                        note: 'Radar has no parseable Sylius constraint for this plugin',
                        migration,
                    });
                }
            } else if (progress) {
                // IN_PROGRESS entry with no DB row — the package is probably
                // unpublished or not yet on Packagist, but its tracker link is
                // still load-bearing. Surface it rather than dropping it.
                inProgress.push({
                    packageName: pkg.name,
                    userConstraint: pkg.constraint,
                    migration,
                    progress,
                });
            } else if (looksLikeSylius(pkg.name)) {
                unknownSylius.push({ packageName: pkg.name, note: null, migration });
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

    function resolveMigration(oldName) {
        const m = MIGRATIONS[oldName];
        if (!m) return null;
        const successor = state.db ? state.db.get(m.target) : null;
        return {
            ...m,
            successorSupports2x: successor ? !!successor.supports2x : null,
            successorTag: successor ? successor.latestTag : null,
        };
    }

    // ---------- rendering ----------

    function renderResults(buckets) {
        const total = buckets.ready.length + buckets.inProgress.length + buckets.notReady.length + buckets.unknownSylius.length;
        dom.countNumber.textContent = String(total);
        dom.countLabel.textContent = total === 1 ? 'Sylius plugin identified' : 'Sylius plugins identified';
        dom.groups.innerHTML = '';
        dom.summaryStrip.innerHTML = '';

        if (total > 0) {
            dom.summaryStrip.classList.remove('radar-is-hidden');
            dom.summaryStrip.append(
                summaryCell('ready', buckets.ready.length, 'Ready for 2.x'),
                summaryCell('progress', buckets.inProgress.length, 'In progress'),
                summaryCell('notready', buckets.notReady.length, 'Not yet ready'),
                summaryCell('unknown', buckets.unknownSylius.length, 'Uncovered'),
            );
        } else {
            dom.summaryStrip.classList.add('radar-is-hidden');
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
        const d = el('div', 'radar-summary__cell');
        d.dataset.tone = tone;
        d.append(withText(el('span', 'radar-summary__num'), String(num)), withText(el('span', 'radar-summary__lbl'), label));
        return d;
    }

    function renderGroup({ title, dot, count, rows }) {
        const section = el('section', 'radar-group');
        const h = el('h3', 'radar-group__title');
        h.appendChild(el('span', `radar-dot radar-dot--${dot}`));
        h.appendChild(document.createTextNode(' ' + title + ' '));
        h.appendChild(withText(el('span', 'radar-count'), `(${count})`));
        section.appendChild(h);
        const grid = el('div', 'radar-rows');
        rows.forEach((r) => grid.appendChild(r));
        section.appendChild(grid);
        return section;
    }

    function renderReadyRow(e) {
        const row = el('div', 'radar-row');
        const info = el('div');
        info.appendChild(withText(el('span', 'radar-row__name'), e.packageName));
        const meta = el('p', 'radar-row__meta');

        if (e.syliusConstraint && e.latestTag) {
            meta.append(
                document.createTextNode('Latest '),
                withText(el('code', 'radar-code'), e.latestTag),
                document.createTextNode(' requires '),
                withText(el('code', e.constraintFrom && e.constraintFrom !== 'sylius/sylius' ? 'radar-code radar-is-fallback' : 'radar-code'),
                    `${e.constraintFrom || 'sylius/sylius'}: ${e.syliusConstraint}`),
            );
        } else if (e.notes) {
            // Curated entry with no Packagist resolution (e.g. sylius/plus).
            // The note is load-bearing — surface it verbatim.
            meta.appendChild(withText(el('span', 'radar-row__note'), e.notes));
        } else {
            meta.appendChild(document.createTextNode('Radar flags this package as ready for 2.x.'));
        }

        info.appendChild(meta);
        if (e.downloads) {
            info.appendChild(withText(el('span', 'radar-row__downloads'), `${formatDownloads(e.downloads)} downloads on Packagist`));
        }
        row.append(info, packagistLink(e.packageName));
        return row;
    }

    function renderInProgressRow(e) {
        const row = el('div', 'radar-row radar-row--muted');
        const info = el('div');
        info.appendChild(withText(el('span', 'radar-row__name'), e.packageName));

        const meta = el('p', 'radar-row__meta');
        const summary = e.progress?.summary || e.notes || 'in progress';
        meta.append(
            document.createTextNode('Status: '),
            withText(el('span', 'radar-row__note'), summary),
        );
        info.appendChild(meta);

        if ((e.prereleaseOnly && e.latestTag) || e.prereleaseTargets2x) {
            info.appendChild(renderPrereleasePill(e));
        }

        if (e.progress?.tracker) {
            info.appendChild(renderProgressTracker(e.progress));
        }

        if (e.downloads) {
            info.appendChild(withText(el('span', 'radar-row__downloads'), `${formatDownloads(e.downloads)} downloads`));
        }
        row.append(info, packagistLink(e.packageName));
        return row;
    }

    function renderPrereleasePill(e) {
        // Carries the prerelease tag + Sylius constraint. Two sources:
        //   - prereleaseOnly: no stable exists; latestTag IS the prerelease.
        //   - prereleaseTargets2x: stable exists (and pins 1.x); the newer
        //     prerelease pinned 2.x lives in entry.prereleaseTag.
        const tag = e.prereleaseTag || e.latestTag;
        const constraint = e.prereleaseSyliusConstraint || e.syliusConstraint;
        const constraintFrom = e.prereleaseConstraintFrom || e.constraintFrom;
        const p = el('p', 'radar-row__prerelease');
        p.append(
            document.createTextNode('Prerelease '),
            withText(el('code', 'radar-code'), tag || '?'),
        );
        if (constraint) {
            p.append(
                document.createTextNode(' targets '),
                withText(el('code', 'radar-code'), `${constraintFrom || 'sylius/sylius'}: ${constraint}`),
            );
        }
        if (e.prereleaseOnly) {
            p.append(document.createTextNode(' · no stable release yet'));
        } else if (e.prereleaseTargets2x) {
            p.append(document.createTextNode(` · stable ${e.latestTag || '?'} still pins 1.x`));
        }
        return p;
    }

    function renderProgressTracker(progress) {
        const p = el('p', 'radar-row__progress');
        p.appendChild(withText(el('span', 'radar-row__progress-arrow'), '↻'));
        p.appendChild(withText(el('span', 'radar-row__progress-label'), progress.tracker.type === 'pr' ? 'Tracking' : 'Branch'));

        const link = document.createElement('a');
        link.href = progress.tracker.url;
        link.target = '_blank';
        link.rel = 'noopener';
        link.className = 'radar-a radar-row__progress-link';
        link.appendChild(withText(el('code', 'radar-code'), progress.tracker.label));
        link.appendChild(withText(el('span', 'radar-row__progress-link-arrow'), '↗'));
        p.appendChild(link);

        if (progress.lastUpdate) {
            p.appendChild(withText(el('span', 'radar-row__progress-updated'), `· updated ${progress.lastUpdate}`));
        }
        if (progress.stale) {
            p.appendChild(withText(el('span', 'radar-row__progress-stale'), '· looks stale'));
        }
        return p;
    }

    function renderNotReadyRow(e) {
        const row = el('div', 'radar-row radar-row--danger');
        const info = el('div');
        info.appendChild(withText(el('span', 'radar-row__name'), e.packageName));
        const meta = el('p', 'radar-row__meta');
        if (e.prereleaseOnly) {
            meta.append(
                document.createTextNode('No stable release yet — newest is '),
                withText(el('code', 'radar-code'), e.latestTag || '?'),
            );
        } else {
            meta.append(
                document.createTextNode('Latest '),
                withText(el('code', 'radar-code'), e.latestTag || '?'),
                document.createTextNode(' still pins '),
                withText(el('code', 'radar-code radar-is-stale'), `${e.constraintFrom || 'sylius/sylius'}: ${e.syliusConstraint || '?'}`),
            );
        }
        info.appendChild(meta);
        if (e.prereleaseOnly) info.appendChild(renderPrereleasePill(e));
        if (e.migration) info.appendChild(renderMigrationHint(e.migration));
        if (e.downloads) {
            const suffix = e.downloads > 250000 ? ' · high-impact block' : '';
            info.appendChild(withText(el('span', 'radar-row__downloads'), `${formatDownloads(e.downloads)} downloads${suffix}`));
        }
        row.append(info, packagistLink(e.packageName));
        return row;
    }

    function renderUnknownRow(e) {
        const row = el('div', 'radar-row radar-row--unknown');
        const info = el('div');
        info.append(
            withText(el('span', 'radar-row__name'), e.packageName),
            withText(el('span', 'radar-row__tag'), e.note || 'Not yet covered by the radar'),
        );
        if (e.migration) info.appendChild(renderMigrationHint(e.migration));
        row.appendChild(info);
        return row;
    }

    function renderMigrationHint(m) {
        const p = el('p', `radar-row__migrate radar-row__migrate--${m.urgency}`);
        p.appendChild(withText(el('span', 'radar-row__migrate-arrow'), '→'));
        const label = m.urgency === 'replace' ? 'Migrate to' : 'Also available as';
        p.appendChild(withText(el('span', 'radar-row__migrate-label'), label));

        const link = document.createElement('a');
        link.href = `https://packagist.org/packages/${m.target}`;
        link.target = '_blank';
        link.rel = 'noopener';
        link.className = 'radar-a radar-row__migrate-target';
        link.appendChild(withText(el('code', 'radar-code'), m.target));
        link.appendChild(withText(el('span', 'radar-row__migrate-target-arrow'), '↗'));
        p.appendChild(link);

        const tags = [];
        if (m.directDescendant === true) tags.push('drop-in successor');
        else if (m.directDescendant === null) tags.push('verify migration path');
        if (m.successorSupports2x === true && m.successorTag) tags.push(`${m.successorTag} is on 2.x`);
        if (tags.length) {
            p.appendChild(withText(el('span', 'radar-row__migrate-tags'), `· ${tags.join(' · ')}`));
        }

        p.appendChild(withText(el('span', 'radar-row__migrate-reason'), m.reason));
        return p;
    }

    function renderDetected(constraint) {
        const w = el('div', 'radar-detected');
        w.append(
            withText(el('span', 'radar-detected__lbl'), 'Detected'),
            withText(el('code', 'radar-code radar-detected__name'), 'sylius/sylius'),
            withText(el('code', 'radar-code radar-detected__ver'), constraint || '—'),
            withText(el('span', 'radar-detected__suffix'), 'Checking plugins against Sylius 2.x.'),
        );
        return w;
    }

    function renderCore(pkgs) {
        const d = el('details', 'radar-other');
        const s = el('summary', 'radar-other__summary');
        s.append(
            document.createTextNode(`Sylius core components (${pkgs.length}) — follow sylius/sylius`),
            withText(el('span', 'radar-other__arrow'), '↓'),
        );
        d.appendChild(s);
        const grid = el('div', 'radar-other__grid');
        pkgs.sort((a, b) => a.name.localeCompare(b.name)).forEach((p) => {
            grid.appendChild(withText(el('div'), p.constraint ? `${p.name}: ${p.constraint}` : p.name));
        });
        d.appendChild(grid);
        return d;
    }

    function renderOther(names) {
        const d = el('details', 'radar-other');
        const s = el('summary', 'radar-other__summary');
        s.append(
            document.createTextNode(`Other PHP dependencies (${names.length})`),
            withText(el('span', 'radar-other__arrow'), '↓'),
        );
        d.appendChild(s);
        const grid = el('div', 'radar-other__grid');
        names.sort().forEach((n) => grid.appendChild(withText(el('div'), n)));
        d.appendChild(grid);
        return d;
    }

    function packagistLink(name) {
        const a = document.createElement('a');
        a.href = `https://packagist.org/packages/${name}`;
        a.target = '_blank';
        a.rel = 'noopener';
        a.className = 'radar-a radar-row__link';
        a.textContent = 'Packagist ↗';
        return a;
    }

    // ---------- ui helpers ----------

    function showScreen(which) {
        dom.inputScreen.classList.toggle('radar-is-hidden', which !== 'input');
        dom.resultsScreen.classList.toggle('radar-is-hidden', which !== 'results');
        if (which === 'results') {
            dom.resultsScreen.scrollIntoView({ block: 'start', behavior: 'smooth' });
        } else {
            window.scrollTo({ top: 0, behavior: 'auto' });
        }
    }

    function showParseError(msg) {
        dom.parseError.textContent = msg;
        dom.parseError.classList.remove('radar-is-hidden');
    }

    function clearError() {
        dom.parseError.textContent = '';
        dom.parseError.classList.add('radar-is-hidden');
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

</script>
