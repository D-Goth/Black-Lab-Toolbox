<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prompt Lab — Black-Lab Toolbox</title>
    <style>
    /* ── Variables Hub (hérite du contexte parent via iframe) ── */
    :root {
        --bg:           #0d0d0f;
        --bg-2:         #141418;
        --surface:      rgba(255, 255, 255, 0.04);
        --surface-h:    rgba(255, 255, 255, 0.08);
        --border:       rgba(255, 255, 255, 0.08);
        --accent:       #ff1654;
        --accent-soft:  rgba(255, 22, 84, 0.18);
        --violet:       #5e006c;
        --gradient:     linear-gradient(135deg, #ff1654, #5e006c);
        --text:         #e8e8f0;
        --text-muted:   #7a7a90;
        --text-dim:     #3a3a50;
        --radius:       12px;
        --radius-sm:    8px;
        --transition:   0.18s ease;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html, body {
        height: 100%;
        font-family: 'Inter', system-ui, sans-serif;
        font-size: 14px;
        background: var(--bg);
        color: var(--text);
        -webkit-font-smoothing: antialiased;
        overflow: hidden;
    }

    /* ── Layout ────────────────────────────────────────────── */
    /*
        GRID :
        [header          header        ]
        [briques  |  info + score      ]
        [composer bottom (full width)  ]
    */
    .pl-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        grid-template-rows: auto 1fr auto;
        height: 100vh;
        max-height: 100vh;
        overflow: hidden;
        gap: 0;
    }

    /* ── Header ────────────────────────────────────────────── */
    /* .hdr : défini dans tools-shared.css */
    .hdr { grid-column: 1 / -1; flex-wrap: wrap; }


    /* Phase tabs */
    .phase-tabs {
        display: flex;
        gap: 0.3rem;
        flex-wrap: wrap;
    }

    .phase-tab {
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        border: 1px solid var(--border);
        background: none;
        color: var(--text-muted);
        font-size: 0.78rem;
        font-weight: 500;
        cursor: pointer;
        transition: all var(--transition);
    }
    .phase-tab:hover { border-color: var(--accent); color: var(--text); }
    .phase-tab.active {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
    }

    /* Score legend button */
    .legend-btn {
        margin-left: auto;
        padding: 0.35rem 0.85rem;
        border-radius: var(--radius-sm);
        border: 1px solid var(--accent);
        background: var(--accent-soft);
        color: var(--accent);
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.3rem;
        transition: all var(--transition);
        white-space: nowrap;
    }
    .legend-btn:hover { background: var(--accent); color: #fff; }

    /* ── Left panel : briques ───────────────────────────────── */
    .pl-left {
        overflow-y: auto;
        padding: 0.8rem 1rem 0.8rem 1.2rem;
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        min-height: 0;
        grid-row: 2;
        grid-column: 1;
    }

    /* ── Right panel : info + score ─────────────────────────── */
    .pl-right {
        display: flex;
        flex-direction: column;
        overflow: hidden;
        min-height: 0;
        grid-row: 2;
        grid-column: 2;
        padding: 0.8rem 1rem;
        gap: 0.8rem;
        border-left: 1px solid var(--border);
    }

    /* ── Bottom composer ────────────────────────────────────── */
    .pl-bottom {
        grid-column: 1 / -1;
        grid-row: 3;
        border-top: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        background: var(--bg-2, #141418);
        max-height: 42vh;
        min-height: 0;
    }

    .pl-bottom__inner {
        display: flex;
        flex: 1;
        overflow: hidden;
        min-height: 0;
    }

    /* Category filter bar */
    .cat-filters {
        display: flex;
        gap: 0.4rem;
        flex-wrap: wrap;
        padding-bottom: 0.8rem;
        border-bottom: 1px solid var(--border);
        position: sticky;
        top: 0;
        background: var(--bg);
        z-index: 10;
        padding-top: 0.2rem;
    }

    .cat-btn {
        padding: 0.3rem 0.75rem;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--text-muted);
        font-size: 0.78rem;
        font-weight: 500;
        cursor: pointer;
        transition: all var(--transition);
    }
    .cat-btn:hover { border-color: var(--accent); color: var(--text); background: var(--surface-h); }
    .cat-btn.active { background: var(--accent-soft); border-color: var(--accent); color: var(--accent); }

    /* Briques grid */
    .briques-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 0.6rem;
    }

    .brique-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 0.85rem 1rem;
        cursor: pointer;
        transition: background var(--transition), border-color var(--transition), transform var(--transition);
    }
    .brique-card:hover {
        background: var(--surface-h);
        border-color: var(--accent);
        transform: translateY(-1px);
    }
    .brique-card.flash {
        animation: briquePulse 0.35s ease;
    }
    @keyframes briquePulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(0.97); background: var(--accent-soft); border-color: var(--accent); }
    }

    .brique-text {
        font-size: 0.82rem;
        line-height: 1.5;
        color: var(--text);
        margin-bottom: 0.6rem;
    }

    .brique-scores {
        display: flex;
        align-items: center;
        gap: 3px;
    }
    .score-bar {
        height: 6px;
        width: 18px;
        border-radius: 2px;
        flex-shrink: 0;
    }
    .s-0 { background: #2a2a2a; }
    .s-1 { background: #ff5f56; }
    .s-2 { background: #ffbd2e; }
    .s-3 { background: #ffeb3b; }
    .s-4 { background: #aeea00; }
    .s-5 { background: #27c93f; }
    .score-vals {
        font-size: 0.68rem;
        color: var(--text-dim);
        margin-left: 6px;
        font-family: 'Fira Code', monospace;
    }

    /* ── Right panel : info + score ─────────────────────────── */
    .pl-right {
        display: flex;
        flex-direction: column;
        overflow: hidden;
        min-height: 0;
        grid-row: 2;
        grid-column: 2;
        padding: 0.8rem 1rem;
        gap: 0.8rem;
        border-left: 1px solid var(--border);
    }
    .console-wrap {
        flex: 1;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        border-right: 1px solid var(--border);
        background: #0a0a0c;
        min-width: 0;
    }

    .console-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.55rem 0.9rem;
        background: #111116;
        border-bottom: 1px solid var(--border);
        flex-shrink: 0;
    }

    .console-dots { display: flex; gap: 6px; align-items: center; }
    .dot { width: 10px; height: 10px; border-radius: 50%; }
    .dot-r { background: #ff5f56; }
    .dot-y { background: #ffbd2e; }
    .dot-g { background: #27c93f; }

    .console-label {
        font-size: 0.65rem;
        color: var(--accent);
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        margin-left: 0.6rem;
    }

    .autosave-status {
        font-size: 0.68rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 4px;
        transition: color var(--transition);
    }
    .autosave-status.saved { color: #27c93f; }

    .builder-area {
        flex: 1;
        padding: 0.7rem 1rem;
        font-size: 0.88rem;
        font-family: 'Fira Code', 'Cascadia Code', monospace;
        line-height: 1.65;
        color: var(--text);
        outline: none;
        white-space: pre-wrap;
        overflow-y: auto;
        min-height: 80px;
        background: transparent;
        caret-color: var(--accent);
    }
    .builder-area:empty::before {
        content: attr(data-placeholder);
        color: var(--text-dim);
        pointer-events: none;
    }

    /* Variables détectées */
    .var-section {
        display: none;
        padding: 0.8rem 1rem;
        border-top: 1px solid var(--border);
        background: rgba(255,22,84,0.04);
        flex-shrink: 0;
    }
    .var-section.visible { display: block; }
    .var-section-title {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--accent);
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    .var-grid { display: flex; flex-direction: column; gap: 0.4rem; }
    .var-row { display: flex; align-items: center; gap: 0.5rem; }
    .var-label { font-size: 0.72rem; color: var(--text-muted); white-space: nowrap; min-width: 80px; font-family: monospace; }
    .var-input {
        flex: 1;
        padding: 0.35rem 0.6rem;
        background: var(--bg-2, #141418);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        color: var(--text);
        font-size: 0.8rem;
        outline: none;
        transition: border-color var(--transition);
    }
    .var-input:focus { border-color: var(--accent); }

    /* Global score */
    .global-score {
        padding: 0.7rem 1rem;
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 0.6rem;
        flex-shrink: 0;
        flex-wrap: wrap;
    }

    .global-score__label {
        font-size: 0.68rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        white-space: nowrap;
    }

    .global-score__bars { display: flex; gap: 3px; align-items: center; }

    .global-score__dim {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
    }

    .global-score__bar {
        height: 20px;
        width: 14px;
        border-radius: 2px;
        background: var(--surface);
        transition: background var(--transition);
    }

    .global-score__val {
        font-size: 0.6rem;
        color: var(--text-dim);
        font-family: monospace;
    }

    .global-score__warning {
        font-size: 0.7rem;
        color: #ffbd2e;
        flex: 1;
        min-width: 0;
    }

    .density-tag {
        font-size: 0.7rem;
        color: var(--text-muted);
        white-space: nowrap;
    }
    .density-tag span { color: var(--accent); font-weight: 700; }

    /* ── Actions bar ────────────────────────────────────────── */
    .actions-bar {
        display: flex;
        gap: 0.4rem;
        flex-wrap: wrap;
        padding: 0.5rem 0.8rem;
        flex-shrink: 0;
        border-top: 1px solid var(--border);
        background: var(--bg-2, #141418);
        align-items: center;
    }

    .btn {
        padding: 0.45rem 0.9rem;
        border-radius: var(--radius-sm);
        border: none;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all var(--transition);
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    .btn:hover { opacity: 0.85; }

    .btn--primary { background: var(--gradient); color: #fff; }
    .btn--ghost {
        background: var(--surface);
        border: 1px solid var(--border);
        color: var(--text-muted);
    }
    .btn--ghost:hover { color: var(--text); border-color: var(--accent); }
    .btn--danger {
        background: rgba(255,95,86,0.12);
        border: 1px solid rgba(255,95,86,0.3);
        color: #ff5f56;
    }
    .btn--danger:hover { background: rgba(255,95,86,0.22); }

    /* ── Info panel ─────────────────────────────────────────── */
    .info-panel {
        margin: 0 1.2rem 1rem 0;
        padding: 0.8rem 1rem;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        font-size: 0.8rem;
        flex-shrink: 0;
    }
    .info-panel__title {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--accent);
        font-weight: 600;
        margin-bottom: 0.4rem;
    }
    .info-panel__content { color: var(--text-muted); line-height: 1.5; }

    /* ── Modal ──────────────────────────────────────────────── */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.85);
        backdrop-filter: blur(12px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    .modal-overlay.open { display: flex; }

    .modal {
        background: #111116;
        border: 1px solid var(--accent);
        border-radius: var(--radius);
        padding: 1.5rem;
        max-width: 520px;
        width: 90%;
        max-height: 85vh;
        overflow-y: auto;
        position: relative;
        box-shadow: 0 0 40px rgba(255,22,84,0.15);
    }

    .modal__close {
        position: absolute;
        top: 0.8rem;
        right: 1rem;
        font-size: 1.2rem;
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        transition: color var(--transition);
    }
    .modal__close:hover { color: var(--accent); }

    .modal h3 {
        font-size: 0.9rem;
        color: var(--accent);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 1rem;
    }

    .scale-row {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .scale-item {
        flex: 1;
        text-align: center;
        font-size: 0.65rem;
        color: var(--text-muted);
    }
    .scale-box { height: 8px; border-radius: 2px; margin-bottom: 3px; }

    .dim-row {
        display: flex;
        align-items: flex-start;
        gap: 0.7rem;
        padding: 0.7rem 0;
        border-bottom: 1px solid var(--border);
    }
    .dim-row:last-child { border: none; }
    .dim-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
        margin-top: 3px;
    }
    .dim-row-text {}
    .dim-row-name {
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--text);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.2rem;
    }
    .dim-row-desc { font-size: 0.75rem; color: var(--text-muted); line-height: 1.45; }

    /* Draft restore modal */
    .draft-info {
        background: var(--accent-soft);
        border-left: 3px solid var(--accent);
        border-radius: var(--radius-sm);
        padding: 0.7rem 0.9rem;
        margin: 0.8rem 0;
    }
    .draft-ts { font-size: 0.72rem; color: var(--text-muted); margin-bottom: 0.4rem; }
    .draft-preview {
        font-size: 0.72rem;
        font-family: monospace;
        color: var(--text);
        background: rgba(0,0,0,0.3);
        padding: 0.5rem;
        border-radius: 4px;
        max-height: 80px;
        overflow-y: auto;
        white-space: pre-wrap;
        word-break: break-word;
    }
    .modal-actions { display: flex; gap: 0.5rem; margin-top: 1rem; }
    .modal-actions .btn { flex: 1; justify-content: center; }

    /* ── Toast ──────────────────────────────────────────────── */
    .toast {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        background: #111116;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 0.7rem 1rem;
        font-size: 0.8rem;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transform: translateY(120%);
        transition: transform 0.25s ease;
        z-index: 2000;
        box-shadow: 0 8px 24px rgba(0,0,0,0.5);
        max-width: 280px;
    }
    .toast.show { transform: translateY(0); }
    .toast.success { border-color: #27c93f; }
    .toast.error   { border-color: var(--accent); }

    /* ── Scrollbar ──────────────────────────────────────────── */
    ::-webkit-scrollbar { width: 4px; height: 4px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

    /* ── Responsive ─────────────────────────────────────────── */
    @media (max-width: 900px) {
        .pl-layout {
            grid-template-columns: 1fr;
            grid-template-rows: auto 1fr auto auto;
        }
        .pl-right {
            grid-column: 1;
            grid-row: 3;
            max-height: 160px;
            border-left: none;
            border-top: 1px solid var(--border);
        }
        .pl-bottom {
            grid-column: 1;
            grid-row: 4;
        }
    }
    </style>
</head>
<body>

<!-- Ambient circles -->
<div class="ambient">
  <div class="ambient__circle"></div>
  <div class="ambient__circle"></div>
</div>

<div class="pl-layout">

    <!-- ── HEADER ──────────────────────────────────────────── -->
    <header class="hdr">
        <span class="hdr__icon">🤖</span>
        <span class="hdr__title">PROMPT LAB</span>
        <span class="hdr__sep"></span>
        <span class="hdr__meta" id="header-meta">300 briques — 15 catégories</span>
        <div class="phase-tabs" id="phase-tabs"></div>
        <button class="legend-btn" id="legend-btn"><span>ⓘ</span> Légende scoring</button>
    </header>

    <!-- ── LEFT : LIBRARY ──────────────────────────────────── -->
    <section class="pl-left" id="pl-left">
        <div class="cat-filters" id="cat-filters">
            <!-- injecté dynamiquement -->
        </div>
        <div class="briques-grid" id="briques-grid">
            <!-- injecté dynamiquement -->
        </div>
    </section>

    <!-- ── RIGHT : INFO + SCORE ──────────────────────────────── -->
    <aside class="pl-right">

        <div class="info-panel" id="info-panel" style="flex:1;overflow-y:auto;">
            <div class="info-panel__title">Détail brique</div>
            <div class="info-panel__content" id="info-content">
                Cliquez sur une brique pour voir ses effets.
            </div>
        </div>

        <div class="global-score" id="global-score" style="border-top:1px solid var(--border);border-radius:0;margin:0;">
            <span class="global-score__label">Score global</span>
            <div class="global-score__bars" id="score-bars">
                <span style="font-size:0.72rem; color:var(--text-dim);">Ajoutez des briques…</span>
            </div>
            <span class="density-tag">Densité : <span id="density">0</span></span>
            <span class="global-score__warning" id="global-warning"></span>
        </div>

    </aside>

    <!-- ── BOTTOM : COMPOSER ─────────────────────────────────── -->
    <div class="pl-bottom">
        <div class="pl-bottom__inner">

            <div class="console-wrap">
                <div class="console-bar">
                    <div class="console-dots">
                        <span class="dot dot-r"></span>
                        <span class="dot dot-y"></span>
                        <span class="dot dot-g"></span>
                        <span class="console-label">Composer v2</span>
                    </div>
                    <div class="autosave-status" id="autosave-status">
                        💾 <span id="autosave-text">Auto-save actif</span>
                    </div>
                </div>

                <div
                    class="builder-area"
                    id="builder-area"
                    contenteditable="true"
                    spellcheck="false"
                    data-placeholder="Sélectionnez des briques pour composer votre prompt..."
                ></div>
            </div>

            <div class="var-section" id="var-section" style="width:260px;flex-shrink:0;border-left:1px solid var(--border);overflow-y:auto;">
                <div class="var-section-title">Variables détectées</div>
                <div class="var-grid" id="var-grid"></div>
            </div>

        </div>

        <div class="actions-bar">
            <button class="btn btn--primary" id="btn-copy">📋 Copier</button>
            <button class="btn btn--ghost" id="btn-export">⬇ .txt</button>
            <button class="btn btn--ghost" id="btn-raw">◐ Raw</button>
            <button class="btn btn--ghost" id="btn-clear">✕ Vider</button>
            <button class="btn btn--danger" id="btn-del-draft">🗑</button>
        </div>
    </div>

</div>

<!-- ── MODAL : Légende scoring ────────────────────────────── -->
<div class="modal-overlay" id="modal-legend">
    <div class="modal">
        <button class="modal__close" id="legend-close">×</button>
        <h3>Échelle de valeur (0–5)</h3>
        <div class="scale-row">
            <div class="scale-item"><div class="scale-box s-0"></div>0 – Nul</div>
            <div class="scale-item"><div class="scale-box s-1"></div>1 – Léger</div>
            <div class="scale-item"><div class="scale-box s-2"></div>2</div>
            <div class="scale-item"><div class="scale-box s-3"></div>3 – Notable</div>
            <div class="scale-item"><div class="scale-box s-4"></div>4</div>
            <div class="scale-item"><div class="scale-box s-5"></div>5 – Critique</div>
        </div>
        <h3>Les 5 Dimensions cognitives</h3>
        <div class="dim-row">
            <div class="dim-dot" style="background:#ff1654"></div>
            <div class="dim-row-text">
                <div class="dim-row-name">1 — Intensité</div>
                <div class="dim-row-desc">Poids comportemental. Définit si l'influence de la brique est subtile ou prioritaire dans le prompt final.</div>
            </div>
        </div>
        <div class="dim-row">
            <div class="dim-dot" style="background:#c71585"></div>
            <div class="dim-row-text">
                <div class="dim-row-name">2 — Stabilité</div>
                <div class="dim-row-desc">Cohérence interne. Persistance de l'effet dans le temps et capacité du modèle à maintenir l'instruction.</div>
            </div>
        </div>
        <div class="dim-row">
            <div class="dim-dot" style="background:#9b30ff"></div>
            <div class="dim-row-text">
                <div class="dim-row-name">3 — Sécurité</div>
                <div class="dim-row-desc">Risque d'hallucination. Un score bas (0–1) indique une brique "Safe" (catégorie Vérité).</div>
            </div>
        </div>
        <div class="dim-row">
            <div class="dim-dot" style="background:#6a5acd"></div>
            <div class="dim-row-text">
                <div class="dim-row-name">4 — Charge Cognitive</div>
                <div class="dim-row-desc">Complexité de traitement. Niveau d'effort logique et d'attention imposé à l'IA.</div>
            </div>
        </div>
        <div class="dim-row">
            <div class="dim-dot" style="background:#4169e1"></div>
            <div class="dim-row-text">
                <div class="dim-row-name">5 — Impact Relationnel</div>
                <div class="dim-row-desc">Ton et Interaction. Influence la chaleur, la diplomatie et le style de communication.</div>
            </div>
        </div>
    </div>
</div>

<!-- ── MODAL : Restaurer brouillon ───────────────────────── -->
<div class="modal-overlay" id="modal-draft">
    <div class="modal">
        <h3>📝 Brouillon détecté</h3>
        <p style="color:var(--text-muted);font-size:0.82rem;margin-bottom:0.5rem;">Un brouillon non sauvegardé a été trouvé. Voulez-vous le restaurer ?</p>
        <div class="draft-info">
            <div class="draft-ts" id="draft-ts"></div>
            <div class="draft-preview" id="draft-preview"></div>
        </div>
        <div class="modal-actions">
            <button class="btn btn--primary" id="btn-restore">✓ Restaurer</button>
            <button class="btn btn--ghost" id="btn-discard">✗ Recommencer</button>
        </div>
    </div>
</div>

<!-- ── TOAST ─────────────────────────────────────────────── -->
<div class="toast" id="toast">
    <span id="toast-icon">💾</span>
    <span id="toast-msg">Sauvegardé</span>
</div>

<!-- ── BRIQUES DATA ───────────────────────────────────────── -->
<script src="/tools/prompt-lab/briques.js"></script>

<script>
(() => {
// ── État ──────────────────────────────────────────────────
let activeBriques  = [];
let currentPhase   = 'all';
let currentCat     = null;
let isRaw          = false;
let autoSaveTimer  = null;
const AUTOSAVE_MS  = 1200;
const DRAFT_KEY    = 'hub_promptlab_draft';

// ── DOM ───────────────────────────────────────────────────
const phaseTabs   = document.getElementById('phase-tabs');
const catFilters  = document.getElementById('cat-filters');
const grid        = document.getElementById('briques-grid');
const area        = document.getElementById('builder-area');
const varSection  = document.getElementById('var-section');
const varGrid     = document.getElementById('var-grid');
const scoreBars   = document.getElementById('score-bars');
const density     = document.getElementById('density');
const warning     = document.getElementById('global-warning');
const infoContent = document.getElementById('info-content');
const autosaveEl  = document.getElementById('autosave-status');
const autosaveTxt = document.getElementById('autosave-text');

// ── Init ──────────────────────────────────────────────────
function init() {
    buildPhaseTabs();
    applyPhase('all');
    checkDraft();
    updateGlobalScore();
    area.addEventListener('input', onAreaInput);
    area.addEventListener('paste', onAreaInput);
    bindActions();
    bindModals();
}

// ── Phase tabs ────────────────────────────────────────────
function buildPhaseTabs() {
    const phases = ['all', ...new Set(window.briques.map(b => b.phase))].sort((a,b) => a === 'all' ? -1 : a.localeCompare(b));
    phaseTabs.innerHTML = '';
    phases.forEach(p => {
        const btn = document.createElement('button');
        btn.className = 'phase-tab' + (p === 'all' ? ' active' : '');
        btn.textContent = p === 'all' ? 'Toutes' : p;
        btn.dataset.phase = p;
        btn.addEventListener('click', () => {
            document.querySelectorAll('.phase-tab').forEach(t => t.classList.remove('active'));
            btn.classList.add('active');
            currentPhase = p;
            applyPhase(p);
        });
        phaseTabs.appendChild(btn);
    });
}

function applyPhase(phase) {
    const filtered = phase === 'all' ? window.briques : window.briques.filter(b => b.phase === phase);
    const cats = [...new Set(filtered.map(b => b.category))].sort();
    buildCatFilters(cats, filtered);
    if (cats.length) selectCat(cats[0], filtered);
}

// ── Category filters ──────────────────────────────────────
function buildCatFilters(cats, filtered) {
    catFilters.innerHTML = '';
    cats.forEach((cat, i) => {
        const btn = document.createElement('button');
        btn.className = 'cat-btn' + (i === 0 ? ' active' : '');
        btn.textContent = cat;
        btn.dataset.cat = cat;
        btn.addEventListener('click', () => {
            document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentCat = cat;
            renderBriques(filtered.filter(b => b.category === cat));
        });
        catFilters.appendChild(btn);
    });
}

function selectCat(cat, filtered) {
    currentCat = cat;
    renderBriques(filtered.filter(b => b.category === cat));
}

// ── Render briques ────────────────────────────────────────
function renderBriques(list) {
    grid.innerHTML = '';
    list.forEach(b => {
        const card = document.createElement('div');
        card.className = 'brique-card';
        const bars = b.scores.map(v => `<span class="score-bar s-${v}"></span>`).join('');
        card.innerHTML = `
            <div class="brique-text">${b.text}</div>
            <div class="brique-scores">${bars}<span class="score-vals">${b.scores.join('/')}</span></div>
        `;
        card.addEventListener('click', () => {
            addBrique(b, card);
            showInfo(b);
        });
        grid.appendChild(card);
    });
}

// ── Add brique to composer ────────────────────────────────
function addBrique(b, cardEl) {
    activeBriques.push(b);

    const txt = area.innerText.trim();
    const sep = txt === '' ? '' : '\n\n';
    area.innerText += sep + (b.template ?? b.text);

    updateDensity();
    updateGlobalScore();
    detectVars(area.innerText);
    scheduleAutoSave();

    cardEl.classList.add('flash');
    setTimeout(() => cardEl.classList.remove('flash'), 380);
}

function showInfo(b) {
    infoContent.innerHTML = `
        <strong style="color:var(--text)">${b.category}</strong>
        &nbsp;·&nbsp; ${b.phase}
        <br><br>
        <span style="color:var(--text)">${b.info}</span>
        <br><br>
        <span style="color:#27c93f;font-size:0.72rem;">✓ Brique ajoutée</span>
    `;
}

// ── Density & global score ────────────────────────────────
function updateDensity() {
    density.textContent = activeBriques.length;
}

function updateGlobalScore() {
    if (activeBriques.length === 0) {
        scoreBars.innerHTML = '<span style="font-size:0.72rem; color:var(--text-dim);">Ajoutez des briques…</span>';
        warning.textContent = '';
        return;
    }

    const n = activeBriques.length;
    const sums = [0,0,0,0,0];
    activeBriques.forEach(b => b.scores.forEach((s,i) => sums[i] += s));
    const avgs = sums.map(s => s / n);

    const colors = ['#ff1654','#c71585','#9b30ff','#6a5acd','#4169e1'];
    scoreBars.innerHTML = avgs.map((avg, i) => {
        const v = Math.round(avg);
        const heightPct = (v / 5) * 100;
        return `<div class="global-score__dim">
            <div class="global-score__bar" style="background:${colors[i]};opacity:${0.3 + v*0.14};height:${8 + v*4}px;"></div>
            <span class="global-score__val">${avg.toFixed(1)}</span>
        </div>`;
    }).join('');

    const warns = [];
    if (n > 8) warns.push('⚠️ Surcharge');
    const hasTruth = activeBriques.some(b => b.category === 'Vérité');
    const hasCreat = activeBriques.some(b => b.category === 'Créativité');
    if (hasTruth && hasCreat) warns.push('⚖️ Vérité ↔ Créativité');
    if (avgs[0] > 4) warns.push('🔥 Intensité max');
    if (avgs[3] > 4) warns.push('🧠 Surcharge cog.');
    warning.textContent = warns.join(' · ');
}

// ── Variable detection ────────────────────────────────────
function detectVars(text) {
    const matches = [...new Set((text.match(/\[([^\]]+)\]/g) || []).map(m => m.slice(1,-1)))];
    if (!matches.length) { varSection.classList.remove('visible'); return; }

    varGrid.innerHTML = '';
    matches.forEach(name => {
        const row = document.createElement('div');
        row.className = 'var-row';
        row.innerHTML = `
            <span class="var-label">[${name}]</span>
            <input type="text" class="var-input" data-var="${name}" placeholder="Valeur…">
        `;
        row.querySelector('.var-input').addEventListener('input', scheduleAutoSave);
        varGrid.appendChild(row);
    });
    varSection.classList.add('visible');
}

function getVarValues() {
    const v = {};
    document.querySelectorAll('.var-input').forEach(inp => {
        if (inp.value.trim()) v[inp.dataset.var] = inp.value.trim();
    });
    return v;
}

// ── Auto-save ─────────────────────────────────────────────
function onAreaInput() {
    scheduleAutoSave();
    detectVars(area.innerText);
    updateGlobalScore();
}

function scheduleAutoSave() {
    clearTimeout(autoSaveTimer);
    autosaveEl.classList.remove('saved');
    autosaveTxt.textContent = '…';
    autoSaveTimer = setTimeout(doSave, AUTOSAVE_MS);
}

function doSave() {
    const content = area.innerText;
    if (content.trim() === '' || content.includes('Sélectionnez des briques')) return;
    try {
        localStorage.setItem(DRAFT_KEY, JSON.stringify({
            content,
            briques: activeBriques,
            vars: getVarValues(),
            ts: Date.now()
        }));
        autosaveEl.classList.add('saved');
        autosaveTxt.textContent = 'Sauvegardé ' + new Date().toLocaleTimeString('fr-FR', {hour:'2-digit',minute:'2-digit'});
        showToast('💾 Brouillon sauvegardé', 'success');
    } catch(e) {
        autosaveTxt.textContent = 'Erreur';
    }
}

// ── Draft restore ─────────────────────────────────────────
function checkDraft() {
    try {
        const raw = localStorage.getItem(DRAFT_KEY);
        if (!raw) return;
        const data = JSON.parse(raw);
        document.getElementById('draft-ts').textContent =
            'Dernière modification : ' + new Date(data.ts).toLocaleString('fr-FR');
        document.getElementById('draft-preview').textContent =
            data.content.substring(0, 220) + (data.content.length > 220 ? '…' : '');
        document.getElementById('modal-draft').classList.add('open');
    } catch(e) { localStorage.removeItem(DRAFT_KEY); }
}

document.getElementById('btn-restore').addEventListener('click', () => {
    try {
        const data = JSON.parse(localStorage.getItem(DRAFT_KEY));
        area.innerText = data.content;
        activeBriques = data.briques || [];
        updateDensity();
        updateGlobalScore();
        detectVars(data.content);
        if (data.vars) {
            Object.entries(data.vars).forEach(([k,v]) => {
                const inp = document.querySelector(`[data-var="${k}"]`);
                if (inp) inp.value = v;
            });
        }
        document.getElementById('modal-draft').classList.remove('open');
        showToast('✓ Brouillon restauré', 'success');
    } catch(e) { showToast('Erreur de restauration', 'error'); }
});

document.getElementById('btn-discard').addEventListener('click', () => {
    localStorage.removeItem(DRAFT_KEY);
    document.getElementById('modal-draft').classList.remove('open');
});

// ── Actions ───────────────────────────────────────────────
function bindActions() {
    document.getElementById('btn-copy').addEventListener('click', () => {
        let text = area.innerText;
        document.querySelectorAll('.var-input').forEach(inp => {
            const val = inp.value.trim() || `[${inp.dataset.var}]`;
            text = text.replace(new RegExp(`\\[${inp.dataset.var}\\]`, 'g'), val);
        });
        navigator.clipboard.writeText(text).then(() => showToast('📋 Prompt copié !', 'success'));
    });

    document.getElementById('btn-export').addEventListener('click', () => {
        const blob = new Blob([area.innerText], {type:'text/plain'});
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'prompt_' + Date.now() + '.txt';
        a.click();
        showToast('⬇ Fichier exporté', 'success');
    });

    document.getElementById('btn-raw').addEventListener('click', () => {
        isRaw = !isRaw;
        area.style.background   = isRaw ? '#000' : 'transparent';
        area.style.color        = isRaw ? '#00ff41' : 'var(--text)';
        area.style.fontFamily   = isRaw ? 'monospace' : "'Fira Code', monospace";
    });

    document.getElementById('btn-clear').addEventListener('click', () => {
        area.innerText = '';
        activeBriques = [];
        varSection.classList.remove('visible');
        varGrid.innerHTML = '';
        updateDensity();
        updateGlobalScore();
        infoContent.textContent = 'Cliquez sur une brique pour voir ses effets.';
    });

    document.getElementById('btn-del-draft').addEventListener('click', () => {
        if (confirm('Supprimer le brouillon sauvegardé ?')) {
            localStorage.removeItem(DRAFT_KEY);
            document.getElementById('btn-clear').click();
            autosaveTxt.textContent = 'Auto-save actif';
            autosaveEl.classList.remove('saved');
            showToast('🗑 Brouillon effacé', 'success');
        }
    });
}

// ── Modals ────────────────────────────────────────────────
function bindModals() {
    document.getElementById('legend-btn').addEventListener('click', () => {
        document.getElementById('modal-legend').classList.add('open');
    });
    document.getElementById('legend-close').addEventListener('click', () => {
        document.getElementById('modal-legend').classList.remove('open');
    });
    document.getElementById('modal-legend').addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('open');
    });
}

// ── Toast ─────────────────────────────────────────────────
let toastTimer;
function showToast(msg, type = 'success') {
    const toast = document.getElementById('toast');
    document.getElementById('toast-msg').textContent = msg;
    toast.className = `toast ${type} show`;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('show'), 2800);
}

// ── Go ────────────────────────────────────────────────────
// Attend que briques.js soit disponible (chargement async possible)
if (typeof window.briques !== 'undefined' && window.briques.length > 0) {
    init();
} else {
    setTimeout(() => {
        if (typeof window.briques !== 'undefined' && window.briques.length > 0) {
            init();
        } else {
            document.getElementById('briques-grid').innerHTML =
                '<div style="color:var(--accent);padding:1rem;font-size:0.85rem;">' +
                '⚠️ Impossible de charger <code>briques.js</code>.<br><br>' +
                'Vérifie que le fichier est bien dans :<br>' +
                '<code>hub/public/tools/prompt-lab/briques.js</code>' +
                '</div>';
            document.getElementById('phase-tabs').innerHTML = '';
            document.getElementById('cat-filters').innerHTML = '';
        }
    }, 150);
}

})();
</script>
</body>
</html>