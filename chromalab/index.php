<?php
/**
 * ChromaLab v3.1 — Le Lab'O Noir (Black-Lab Hub v4)
 * Générateur de palettes OKLCH avec harmonies & dégradés
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chromalab — Black-Lab Toolbox</title>
<style>
/* ── Variables Hub ── */
:root {
    --bg:          #0d0d0f;
    --bg-2:        #141418;
    --surface:     rgba(255,255,255,0.04);
    --surface-h:   rgba(255,255,255,0.08);
    --border:      rgba(255,255,255,0.08);
    --accent:      #ff1654;
    --accent-soft: rgba(255,22,84,0.18);
    --gradient:    linear-gradient(135deg,#ff1654,#5e006c);
    --text:        #e8e8f0;
    --text-muted:  #7a7a90;
    --text-dim:    #3a3a50;
    --radius:      12px;
    --radius-sm:   8px;
    --transition:  0.18s ease;
    --success:     #27c93f;
    --warning:     #ffbd2e;
    --cyan:        #22d3ee;
}

*,*::before,*::after { box-sizing:border-box; margin:0; padding:0; }

html, body {
    height: 100%;
    font-family: 'Inter', system-ui, sans-serif;
    font-size: 14px;
    background: var(--bg);
    color: var(--text);
    -webkit-font-smoothing: antialiased;
    overflow: hidden;
}

/* ── Fond ambiant ── */
/* ambient : .ambient__circle dans tools-shared.css */

/* ── Layout principal ── */
.app {
    display: grid;
    grid-template-rows: auto 1fr;
    height: 100vh;
    overflow: hidden;
    position: relative;
    z-index: 1;
}

/* .hdr : défini dans tools-shared.css */

/* ── Corps scrollable ── */
.body {
    overflow-y: auto;
    padding: 1.1rem 1.3rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    min-height: 0;
}

/* ── Panneau générique ── */
.panel {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1rem 1.1rem;
}
.panel__title {
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--accent);
    padding-bottom: 0.45rem;
    border-bottom: 1px solid var(--border);
    margin-bottom: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.panel__title svg { width: 13px; height: 13px; }

/* ══════════════════════════
   PANNEAU PARAMÈTRES
   2 colonnes
══════════════════════════ */
.params-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.2rem;
}

.control-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.control-label {
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
}

/* ── Sélecteur de couleur ── */
.color-input-row {
    display: flex;
    gap: 0.6rem;
    align-items: center;
}
.color-circle {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid var(--border);
    flex-shrink: 0;
    position: relative;
    transition: transform var(--transition), border-color var(--transition);
}
.color-circle:hover {
    transform: scale(1.08);
    border-color: var(--accent);
}
.color-circle::after {
    content: '🎨';
    position: absolute;
    bottom: -3px; right: -3px;
    font-size: 14px;
    background: var(--bg-2);
    border-radius: 50%;
    width: 20px; height: 20px;
    display: flex; align-items: center; justify-content: center;
    border: 1px solid var(--accent);
    opacity: 0.85;
}
.color-circle.pulse { animation: pulse 0.55s ease; }
@keyframes pulse {
    0%,100% { transform: scale(1); }
    50% { transform: scale(1.15); box-shadow: 0 0 20px rgba(255,22,84,0.55); }
}

.color-inputs-col {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}
.color-input {
    width: 100%;
    padding: 0.42rem 0.75rem;
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text);
    font-family: 'Fira Code', 'Cascadia Code', monospace;
    font-size: 0.8rem;
    outline: none;
    transition: border-color var(--transition), box-shadow var(--transition);
}
.color-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px var(--accent-soft);
}
.color-input::placeholder { color: var(--text-dim); }

/* ── Harmony grid ── */
.harmony-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.4rem;
}
.harmony-btn {
    padding: 0.38rem 0.5rem;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text-muted);
    font-size: 0.75rem;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: all var(--transition);
    text-align: center;
}
.harmony-btn:hover { color: var(--text); border-color: var(--accent); background: var(--surface-h); }
.harmony-btn.active { background: var(--accent); border-color: var(--accent); color: #fff; }

/* ── Select et range ── */
.form-select {
    width: 100%;
    padding: 0.45rem 0.75rem;
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text);
    font-size: 0.82rem;
    font-family: inherit;
    outline: none;
    cursor: pointer;
    transition: border-color var(--transition);
}
.form-select:focus { border-color: var(--accent); }
.form-select option { background: var(--bg-2); color: var(--text); }

.range-wrapper {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}
.range-val {
    font-family: 'Fira Code', monospace;
    font-size: 0.8rem;
    color: var(--accent);
    font-weight: 700;
    min-width: 24px;
    text-align: center;
    flex-shrink: 0;
}

input[type="range"] {
    -webkit-appearance: none;
    appearance: none;
    width: 100%;
    height: 5px;
    border-radius: 5px;
    background: var(--surface-h);
    outline: none;
    cursor: pointer;
    border: none;
    padding: 0;
}
input[type="range"]::-webkit-slider-runnable-track {
    height: 5px;
    border-radius: 5px;
    background: linear-gradient(135deg, var(--accent), #5e006c);
    border: none;
}
input[type="range"]::-moz-range-track {
    height: 5px;
    border-radius: 5px;
    background: linear-gradient(135deg, var(--accent), #5e006c);
    border: none;
}
input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 16px; height: 16px;
    background: var(--accent);
    border-radius: 50%;
    cursor: pointer;
    margin-top: -5.5px;
    box-shadow: 0 0 5px rgba(255,22,84,0.45);
    transition: all var(--transition);
}
input[type="range"]::-webkit-slider-thumb:hover {
    transform: scale(1.25);
    box-shadow: 0 0 12px rgba(255,22,84,0.8);
}
input[type="range"]::-moz-range-thumb {
    width: 16px; height: 16px;
    background: var(--accent);
    border-radius: 50%;
    cursor: pointer;
    border: none;
    box-shadow: 0 0 5px rgba(255,22,84,0.45);
    transition: all var(--transition);
}

/* ── Actions ── */
.actions-row {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-top: auto;
    padding-top: 0.2rem;
}
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.45rem 0.9rem;
    border-radius: var(--radius-sm);
    font-size: 0.78rem;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: all var(--transition);
    border: 1px solid var(--border);
    background: var(--surface);
    color: var(--text-muted);
    white-space: nowrap;
    flex: 1;
    justify-content: center;
}
.btn:hover { color: var(--text); border-color: var(--accent); background: var(--surface-h); }
.btn--primary {
    background: var(--gradient);
    border-color: transparent;
    color: #fff;
}
.btn--primary:hover { opacity: 0.88; border-color: transparent; }

/* ══════════════════════════
   FORMAT SWITCHER
══════════════════════════ */
.format-switcher {
    display: flex;
    gap: 0.35rem;
    flex-wrap: wrap;
    margin-bottom: 0.75rem;
}
.format-btn {
    padding: 0.28rem 0.7rem;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 20px;
    color: var(--text-muted);
    font-size: 0.72rem;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: all var(--transition);
    letter-spacing: 0.04em;
}
.format-btn:hover { color: var(--text); border-color: var(--accent); }
.format-btn.active { background: var(--accent-soft); border-color: var(--accent); color: var(--accent); }

/* ══════════════════════════
   TABS PREVIEW
══════════════════════════ */
.tabs-nav {
    display: flex;
    gap: 0;
    border-bottom: 1px solid var(--border);
    margin-bottom: 0.85rem;
    overflow-x: auto;
}
.tab {
    padding: 0.5rem 1rem;
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    color: var(--text-muted);
    font-size: 0.8rem;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: all var(--transition);
    white-space: nowrap;
    margin-bottom: -1px;
}
.tab:hover { color: var(--text); }
.tab.active { color: var(--accent); border-bottom-color: var(--accent); }

.tab-panel { display: none; }
.tab-panel.active { display: block; }

/* ── Preview container ── */
.preview-container {
    min-height: 280px;
    padding: 1rem;
    background: rgba(10,10,14,0.5);
    border-radius: var(--radius-sm);
    border: 1px solid var(--border);
}

/* Cards */
.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.75rem;
}
.preview-card {
    background: rgba(20,20,24,0.8);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 1rem;
    transition: transform var(--transition), box-shadow var(--transition);
}
.preview-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.4); }
.preview-card h4 { font-size: 0.95rem; margin-bottom: 0.4rem; }
.preview-card p { color: var(--text-muted); font-size: 0.78rem; line-height: 1.5; margin-bottom: 0.7rem; }
.preview-card-btn {
    padding: 0.38rem 0.85rem;
    border-radius: var(--radius-sm);
    border: none;
    font-weight: 600;
    font-size: 0.75rem;
    cursor: pointer;
    transition: opacity var(--transition);
}
.preview-card-btn:hover { opacity: 0.82; }

/* Buttons */
.buttons-showcase {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
    align-items: center;
    padding: 0.5rem 0;
}
.showcase-btn {
    padding: 0.45rem 1rem;
    border-radius: var(--radius-sm);
    border: 2px solid transparent;
    font-weight: 600;
    font-size: 0.8rem;
    cursor: pointer;
    transition: opacity var(--transition);
    font-family: inherit;
}
.showcase-btn:hover { opacity: 0.82; }

/* Forms */
.form-preview { max-width: 400px; margin: 0 auto; display: flex; flex-direction: column; gap: 0.7rem; }
.form-preview-label { display: block; font-size: 0.78rem; font-weight: 600; margin-bottom: 0.3rem; color: var(--text); }
.form-preview-input {
    width: 100%;
    padding: 0.48rem 0.85rem;
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text);
    font-size: 0.82rem;
    font-family: inherit;
    outline: none;
    transition: border-color var(--transition), box-shadow var(--transition);
}

/* Charts */
.chart-container { display: flex; gap: 0.75rem; }
.bar-chart {
    display: flex;
    align-items: flex-end;
    gap: 6px;
    height: 160px;
    padding: 0.75rem;
    background: rgba(20,20,24,0.7);
    border-radius: var(--radius-sm);
    border: 1px solid var(--border);
    flex: 1;
}
.bar {
    flex: 1;
    border-radius: 4px 4px 0 0;
    transition: opacity var(--transition);
    cursor: pointer;
}
.bar:hover { opacity: 0.75; }

/* Gradients */
.gradients-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 0.65rem;
}
.gradient-item {
    border-radius: var(--radius-sm);
    height: 90px;
    border: 1px solid var(--border);
    cursor: pointer;
    transition: transform var(--transition), box-shadow var(--transition);
    position: relative;
    overflow: hidden;
}
.gradient-item:hover {
    transform: scale(1.02);
    box-shadow: 0 6px 20px rgba(0,0,0,0.4);
}
.gradient-label {
    position: absolute;
    bottom: 6px; left: 8px;
    background: rgba(0,0,0,0.65);
    backdrop-filter: blur(4px);
    padding: 0.2rem 0.6rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
    color: #fff;
}

/* ══════════════════════════
   PALETTE — SHADES GRID
══════════════════════════ */
.shades-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(65px, 1fr));
    gap: 0.6rem;
}
.shade-item {
    text-align: center;
    cursor: pointer;
    transition: transform var(--transition);
    border-radius: var(--radius-sm);
    padding: 0.3rem;
}
.shade-item:hover { transform: scale(1.06); }
.shade-swatch {
    width: 100%;
    height: 55px;
    border-radius: var(--radius-sm);
    margin-bottom: 0.35rem;
    border: 1px solid rgba(255,255,255,0.08);
    transition: box-shadow var(--transition);
}
.shade-item:hover .shade-swatch { box-shadow: 0 4px 14px rgba(0,0,0,0.5); }
.shade-label {
    font-size: 0.65rem;
    font-weight: 700;
    color: var(--text-muted);
}
.shade-value {
    font-size: 0.6rem;
    color: var(--text-dim);
    font-family: 'Fira Code', monospace;
    margin-top: 0.15rem;
    word-break: break-all;
}

/* ── Harmony colors strip ── */
.harmony-strip {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex-wrap: wrap;
    margin-top: 0.5rem;
}
.harmony-swatch {
    width: 28px; height: 28px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.15);
    cursor: pointer;
    transition: transform var(--transition), border-color var(--transition);
    flex-shrink: 0;
}
.harmony-swatch:hover { transform: scale(1.18); border-color: rgba(255,255,255,0.5); }
.harmony-label {
    font-size: 0.7rem;
    color: var(--text-dim);
    font-style: italic;
}

/* ── Toast ── */
.toast {
    position: fixed;
    bottom: 1.2rem; right: 1.2rem;
    background: #111116;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 0.6rem 1rem;
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--text);
    z-index: 9999;
    opacity: 0;
    transform: translateY(8px);
    transition: all 0.25s ease;
    pointer-events: none;
    box-shadow: 0 8px 24px rgba(0,0,0,0.5);
    max-width: 280px;
}
.toast.show { opacity: 1; transform: translateY(0); }
.toast.success { border-color: var(--success); }
.toast.error   { border-color: var(--accent); }
.toast.info    { border-color: rgba(34,211,238,0.5); }

/* ── Modale Export ── */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.8);
    backdrop-filter: blur(8px);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.22s;
}
.modal-overlay.open { opacity: 1; pointer-events: all; }
.modal-box {
    background: #111116;
    border: 1px solid var(--border);
    border-top: 2px solid var(--accent);
    border-radius: var(--radius);
    width: 100%;
    max-width: 600px;
    max-height: 82vh;
    overflow-y: auto;
    box-shadow: 0 30px 60px rgba(0,0,0,0.6), 0 0 40px rgba(255,22,84,0.08);
    transform: translateY(12px);
    transition: transform 0.22s;
}
.modal-overlay.open .modal-box { transform: translateY(0); }
.modal-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.3rem;
    border-bottom: 1px solid var(--border);
    position: sticky;
    top: 0;
    background: #111116;
    z-index: 1;
}
.modal-head h2 {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--accent);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin: 0;
}
.modal-close-btn {
    background: var(--surface);
    border: 1px solid var(--border);
    color: var(--text-muted);
    border-radius: var(--radius-sm);
    width: 28px; height: 28px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    font-size: 1rem;
    transition: all var(--transition);
    font-family: inherit;
}
.modal-close-btn:hover { color: var(--accent); border-color: var(--accent); }
.modal-body { padding: 1rem 1.3rem; }

/* Export tabs */
.export-tabs {
    display: flex;
    gap: 0;
    border-bottom: 1px solid var(--border);
    margin-bottom: 0.85rem;
}
.export-tab {
    padding: 0.5rem 1rem;
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    color: var(--text-muted);
    font-size: 0.78rem;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: all var(--transition);
    margin-bottom: -1px;
}
.export-tab:hover { color: var(--text); }
.export-tab.active { color: var(--accent); border-bottom-color: var(--accent); }
.export-panel { display: none; }
.export-panel.active { display: block; }
.export-code {
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.9rem 1rem;
    font-family: 'Fira Code', 'Cascadia Code', monospace;
    font-size: 0.75rem;
    color: var(--text);
    white-space: pre;
    overflow-x: auto;
    max-height: 360px;
    overflow-y: auto;
    margin-bottom: 0.75rem;
    line-height: 1.55;
}
.btn-copy-export {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.45rem 1rem;
    background: var(--gradient);
    border: none;
    border-radius: var(--radius-sm);
    color: #fff;
    font-size: 0.78rem;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: opacity var(--transition);
}
.btn-copy-export:hover { opacity: 0.88; }

/* ── Scrollbar ── */

/* ── Responsive ── */
@media (max-width: 720px) {
    .params-grid { grid-template-columns: 1fr; }
    .harmony-grid { grid-template-columns: repeat(3,1fr); }
}
</style>
</head>
<body>

<!-- Ambient circles -->
<div class="ambient">
  <div class="ambient__circle"></div>
  <div class="ambient__circle"></div>
</div>

<div class="app">

    <!-- ══ HEADER ══════════════════════════════════════════════ -->
    <header class="hdr">
        <span class="hdr__icon">🎨</span>
        <span class="hdr__title">CHROMALAB</span>
        <span class="hdr__meta">Générateur de palettes OKLCH — harmonies, nuances &amp; dégradés</span>
        <div class="hdr__right">
            <button class="btn" id="generateBtn" style="flex:0">🎲 Aléatoire</button>
            <button class="btn" id="copyPaletteBtn" style="flex:0;border-color:rgba(34,211,238,0.4);color:var(--cyan)">📐 Copier palette</button>
            <button class="btn btn--primary" id="exportBtn" style="flex:0">📋 Exporter</button>
        </div>
    </header>

    <!-- ══ CORPS ══════════════════════════════════════════════ -->
    <div class="body">

        <!-- ── Paramètres ── -->
        <div class="panel">
            <div class="panel__title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/></svg>
                Paramètres
            </div>

            <div class="params-grid">

                <!-- Colonne gauche : couleur + harmonie -->
                <div style="display:flex;flex-direction:column;gap:0.85rem;">

                    <div class="control-group">
                        <div class="control-label">Couleur de base</div>
                        <div class="color-input-row">
                            <div class="color-circle" id="colorPreview" style="background:#ff1654;"></div>
                            <input type="color" id="nativeColorPicker" style="opacity:0;position:absolute;pointer-events:none;">
                            <div class="color-inputs-col">
                                <input type="text" class="color-input" id="colorInputHex" value="#ff1654" placeholder="#RRGGBB">
                                <input type="text" class="color-input" id="colorInputRgba" value="255, 22, 84" placeholder="R, G, B">
                            </div>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Harmonie</div>
                        <div class="harmony-grid">
                            <button class="harmony-btn active" data-harmony="none">Aucune</button>
                            <button class="harmony-btn" data-harmony="analogous">Analogues</button>
                            <button class="harmony-btn" data-harmony="complementary">Complém.</button>
                            <button class="harmony-btn" data-harmony="triadic">Triadique</button>
                            <button class="harmony-btn" data-harmony="tetradic">Tétradique</button>
                            <button class="harmony-btn" data-harmony="split">Split</button>
                        </div>
                        <div class="harmony-strip" id="harmonyStrip">
                            <span class="harmony-label">Aucune harmonie active</span>
                        </div>
                    </div>

                </div>

                <!-- Colonne droite : algo + nuances + naming -->
                <div style="display:flex;flex-direction:column;gap:0.85rem;">

                    <div class="control-group">
                        <div class="control-label">Algorithme</div>
                        <select class="form-select" id="algorithmSelect">
                            <option value="oklch">OKLCH (perceptuel)</option>
                            <option value="hsl">HSL (classique)</option>
                        </select>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Nuances</div>
                        <div class="range-wrapper">
                            <input type="range" id="shadesRange" min="5" max="21" step="2" value="11">
                            <span class="range-val" id="shadesCountLabel">11</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Nommage</div>
                        <select class="form-select" id="namingSelect">
                            <option value="50">50–950 (Tailwind)</option>
                            <option value="100">100–900</option>
                        </select>
                    </div>

                </div>
            </div>
        </div>

        <!-- ── Preview ── -->
        <div class="panel">
            <div class="panel__title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                Aperçu
            </div>

            <div class="format-switcher">
                <button class="format-btn active" data-format="hex">HEX</button>
                <button class="format-btn" data-format="rgb">RGB</button>
                <button class="format-btn" data-format="hsl">HSL</button>
                <button class="format-btn" data-format="oklch">OKLCH</button>
            </div>

            <div class="tabs-nav">
                <button class="tab active" data-tab="cards">Cards</button>
                <button class="tab" data-tab="buttons">Buttons</button>
                <button class="tab" data-tab="forms">Forms</button>
                <button class="tab" data-tab="charts">Charts</button>
                <button class="tab" data-tab="gradients">Dégradés</button>
            </div>

            <div class="preview-container">
                <div class="tab-panel active" data-panel="cards">
                    <div class="cards-grid" id="cardsGrid"></div>
                </div>
                <div class="tab-panel" data-panel="buttons">
                    <div class="buttons-showcase" id="buttonsShowcase"></div>
                </div>
                <div class="tab-panel" data-panel="forms">
                    <div class="form-preview">
                        <div>
                            <label class="form-preview-label">Email address</label>
                            <input type="email" class="form-preview-input" placeholder="you@example.com" id="formInput1">
                        </div>
                        <div>
                            <label class="form-preview-label">Password</label>
                            <input type="password" class="form-preview-input" placeholder="••••••••" id="formInput2">
                        </div>
                        <button class="preview-card-btn" id="formSubmitBtn" style="align-self:flex-start">Sign in</button>
                    </div>
                </div>
                <div class="tab-panel" data-panel="charts">
                    <div class="chart-container">
                        <div class="bar-chart" id="barChart"></div>
                    </div>
                </div>
                <div class="tab-panel" data-panel="gradients">
                    <div class="gradients-grid" id="gradientsGrid"></div>
                </div>
            </div>
        </div>

        <!-- ── Palette générée ── -->
        <div class="panel">
            <div class="panel__title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8.56 2.75c4.37 6.03 6.02 9.42 8.03 17.72m2.54-15.38c-3.72 4.35-8.94 5.66-16.88 5.85m19.5 1.9c-3.5-.93-6.63-.82-8.94 0-2.58.92-5.01 2.86-7.44 6.32"/></svg>
                Palette générée
                <span style="font-size:0.6rem;color:var(--text-dim);font-weight:400;text-transform:none;margin-left:auto">Cliquez sur une nuance pour copier</span>
            </div>
            <div class="shades-grid" id="shadesGrid"></div>
        </div>

    </div><!-- /.body -->
</div><!-- /.app -->

<!-- ── Toast ── -->
<div class="toast" id="toast"></div>

<!-- ══ MODALE EXPORT ════════════════════════════════════════ -->
<div class="modal-overlay" id="exportModal" onclick="closeModalOutside(event)">
    <div class="modal-box" role="dialog" aria-modal="true">
        <div class="modal-head">
            <h2>📋 Exporter la palette</h2>
            <button class="modal-close-btn" onclick="closeModal()">✕</button>
        </div>
        <div class="modal-body">

            <div class="export-tabs">
                <button class="export-tab active" data-export="css">CSS</button>
                <button class="export-tab" data-export="tailwind">Tailwind</button>
                <button class="export-tab" data-export="json">JSON</button>
                <button class="export-tab" data-export="gradients">Dégradés CSS</button>
            </div>

            <div class="export-panel active" data-export-panel="css">
                <pre class="export-code" id="exportCSS"></pre>
                <button class="btn-copy-export" id="copyCSSBtn">📋 Copier CSS</button>
            </div>
            <div class="export-panel" data-export-panel="tailwind">
                <pre class="export-code" id="exportTailwind"></pre>
                <button class="btn-copy-export" id="copyTailwindBtn">📋 Copier Tailwind Config</button>
            </div>
            <div class="export-panel" data-export-panel="json">
                <pre class="export-code" id="exportJSON"></pre>
                <button class="btn-copy-export" id="copyJSONBtn">📋 Copier JSON</button>
            </div>
            <div class="export-panel" data-export-panel="gradients">
                <pre class="export-code" id="exportGradients"></pre>
                <button class="btn-copy-export" id="copyGradientsBtn">📋 Copier Dégradés</button>
            </div>

        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    /* ══ STATE ══ */
    let currentColor    = '#ff1654';
    let currentHarmony  = 'none';
    let currentAlgorithm = 'oklch';
    let shadesCount     = 11;
    let currentNaming   = '50';
    let currentFormat   = 'hex';
    let palette         = [];
    let harmonyColors   = [];

    /* ══ DOM ══ */
    const colorInputHex    = document.getElementById('colorInputHex');
    const colorInputRgba   = document.getElementById('colorInputRgba');
    const colorPreview     = document.getElementById('colorPreview');
    const nativePicker     = document.getElementById('nativeColorPicker');
    const harmonyBtns      = document.querySelectorAll('.harmony-btn');
    const algorithmSelect  = document.getElementById('algorithmSelect');
    const shadesRange      = document.getElementById('shadesRange');
    const shadesCountLabel = document.getElementById('shadesCountLabel');
    const namingSelect     = document.getElementById('namingSelect');
    const formatBtns       = document.querySelectorAll('.format-btn');
    const tabs             = document.querySelectorAll('.tab');
    const tabPanels        = document.querySelectorAll('.tab-panel');
    const shadesGrid       = document.getElementById('shadesGrid');
    const cardsGrid        = document.getElementById('cardsGrid');
    const buttonsShowcase  = document.getElementById('buttonsShowcase');
    const barChart         = document.getElementById('barChart');
    const gradientsGrid    = document.getElementById('gradientsGrid');
    const harmonyStrip     = document.getElementById('harmonyStrip');
    const exportModal      = document.getElementById('exportModal');
    const exportTabs       = document.querySelectorAll('.export-tab');
    const exportPanels     = document.querySelectorAll('.export-panel');

    /* ══ TOAST ══ */
    let toastTimer;
    function toast(msg, type) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.className = 'toast show ' + (type || 'success');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => t.classList.remove('show'), 2800);
    }

    /* ══ UTILS COULEUR ══ */
    function hexToRgb(hex) {
        const r = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return r ? { r: parseInt(r[1],16), g: parseInt(r[2],16), b: parseInt(r[3],16) } : null;
    }

    function rgbToHex(r, g, b) {
        return '#' + [r,g,b].map(x => {
            const h = Math.round(Math.max(0,Math.min(255,x))).toString(16);
            return h.length === 1 ? '0'+h : h;
        }).join('');
    }

    function parseRgba(s) {
        const cleaned = s.replace(/rgb\(|\)/g,'').trim();
        const parts = cleaned.split(',').map(p => parseInt(p.trim()));
        if (parts.length >= 3 && parts.every(p => !isNaN(p) && p >= 0 && p <= 255))
            return { r: parts[0], g: parts[1], b: parts[2] };
        return null;
    }

    function rgbToHsl(r,g,b) {
        r/=255; g/=255; b/=255;
        const max = Math.max(r,g,b), min = Math.min(r,g,b);
        let h, s, l = (max+min)/2;
        if (max===min) { h=s=0; } else {
            const d = max-min;
            s = l > 0.5 ? d/(2-max-min) : d/(max+min);
            switch(max) {
                case r: h=((g-b)/d+(g<b?6:0))/6; break;
                case g: h=((b-r)/d+2)/6; break;
                case b: h=((r-g)/d+4)/6; break;
            }
        }
        return { h: h*360, s: s*100, l: l*100 };
    }

    /* ══ OKLCH ══ */
    function rgb_to_oklch(r,g,b) {
        r/=255; g/=255; b/=255;
        const l=0.4122214708*r+0.5363325363*g+0.0514459929*b;
        const m=0.2119034982*r+0.6806995451*g+0.1073969566*b;
        const s=0.0883024619*r+0.2817188376*g+0.6299787005*b;
        const l_=Math.cbrt(l), m_=Math.cbrt(m), s_=Math.cbrt(s);
        const L=0.2104542553*l_+0.7936177850*m_-0.0040720468*s_;
        const a=1.9779984951*l_-2.4285922050*m_+0.4505937099*s_;
        const b_=0.0259040371*l_+0.7827717662*m_-0.8086757660*s_;
        const C=Math.sqrt(a*a+b_*b_);
        let h=Math.atan2(b_,a)*180/Math.PI;
        if (h<0) h+=360;
        return { L, C, h };
    }

    function oklch_to_rgb(L,C,h) {
        h=h*Math.PI/180;
        const a=C*Math.cos(h), b=C*Math.sin(h);
        const l_=L+0.3963377774*a+0.2158037573*b;
        const m_=L-0.1055613458*a-0.0638541728*b;
        const s_=L-0.0894841775*a-1.2914855480*b;
        const l=l_*l_*l_, m=m_*m_*m_, s=s_*s_*s_;
        let r=+4.0767416621*l-3.3077115913*m+0.2309699292*s;
        let g=-1.2684380046*l+2.6097574011*m-0.3413193965*s;
        let bv=-0.0041960863*l-0.7034186147*m+1.7076147010*s;
        return {
            r: Math.max(0,Math.min(1,r))*255,
            g: Math.max(0,Math.min(1,g))*255,
            b: Math.max(0,Math.min(1,bv))*255
        };
    }

    /* ══ HARMONY ══ */
    function generateHarmony(baseHex, harmony) {
        const rgb = hexToRgb(baseHex);
        const { h } = rgb_to_oklch(rgb.r, rgb.g, rgb.b);
        let hues = [h];
        switch(harmony) {
            case 'analogous':       hues = [h,(h+30)%360,(h-30+360)%360]; break;
            case 'complementary':   hues = [h,(h+180)%360]; break;
            case 'triadic':         hues = [h,(h+120)%360,(h+240)%360]; break;
            case 'tetradic':        hues = [h,(h+90)%360,(h+180)%360,(h+270)%360]; break;
            case 'split':           hues = [h,(h+150)%360,(h+210)%360]; break;
        }
        return hues.map(hue => {
            const rgb = oklch_to_rgb(0.65, 0.2, hue);
            return rgbToHex(rgb.r, rgb.g, rgb.b);
        });
    }

    /* ══ PALETTE ══ */
    function generatePalette(baseHex) {
        const rgb = hexToRgb(baseHex);
        const { L: baseL, C: baseC, h } = rgb_to_oklch(rgb.r, rgb.g, rgb.b);
        const steps = shadesCount;
        const names50  = [50,100,200,300,400,500,600,700,800,900,950];
        const names100 = [100,200,300,400,500,600,700,800,900];
        const names = currentNaming === '50' ? names50 : names100;
        const shades = [];
        for (let i=0; i<steps; i++) {
            const t = i/(steps-1);
            const L = 0.95 - t*0.85;
            const C = currentAlgorithm === 'oklch'
                ? baseC * (1 - Math.abs(t-0.5)*0.5)
                : baseC;
            const rgb = oklch_to_rgb(L, C, h);
            const hex = rgbToHex(rgb.r, rgb.g, rgb.b);
            shades.push({ name: names[i] || (i*100), hex, rgb, L, C, h });
        }
        return shades;
    }

    /* ══ FORMAT ══ */
    function formatColor(shade) {
        switch(currentFormat) {
            case 'hex':   return shade.hex;
            case 'rgb':   return `rgb(${Math.round(shade.rgb.r)}, ${Math.round(shade.rgb.g)}, ${Math.round(shade.rgb.b)})`;
            case 'hsl': {
                const hsl = rgbToHsl(shade.rgb.r, shade.rgb.g, shade.rgb.b);
                return `hsl(${Math.round(hsl.h)}, ${Math.round(hsl.s)}%, ${Math.round(hsl.l)}%)`;
            }
            case 'oklch': return `oklch(${(shade.L*100).toFixed(1)}% ${shade.C.toFixed(2)} ${Math.round(shade.h)})`;
            default:      return shade.hex;
        }
    }

    /* ══ GRADIENTS ══ */
    function generateGradients() {
        const mid = Math.floor(palette.length/2);
        const grads = [
            { name: 'Clair → Base',     css: `linear-gradient(135deg, ${palette[0].hex}, ${palette[mid].hex})` },
            { name: 'Base → Foncé',     css: `linear-gradient(135deg, ${palette[mid].hex}, ${palette[palette.length-1].hex})` },
            { name: 'Spectre complet',  css: `linear-gradient(135deg, ${palette.map(s=>s.hex).join(', ')})` },
        ];
        if (harmonyColors.length > 1) {
            grads.push({ name: 'Harmonie linéaire', css: `linear-gradient(135deg, ${harmonyColors.join(', ')})` });
            grads.push({ name: 'Harmonie radiale',  css: `radial-gradient(circle, ${harmonyColors.join(', ')})` });
        }
        return grads;
    }

    /* ══ EXPORT ══ */
    function generateCSSExport() {
        return `:root {\n${palette.map(s=>`  --color-${s.name}: ${s.hex};`).join('\n')}\n}`;
    }
    function generateTailwindExport() {
        const colors = {};
        palette.forEach(s => { colors[s.name] = s.hex; });
        return `module.exports = {\n  theme: {\n    extend: {\n      colors: {\n        'primary': ${JSON.stringify(colors,null,8)}\n      }\n    }\n  }\n}`;
    }
    function generateJSONExport() {
        return JSON.stringify({
            baseColor: currentColor,
            harmony: currentHarmony,
            algorithm: currentAlgorithm,
            shades: palette.map(s => ({
                name: s.name,
                hex: s.hex,
                rgb: `rgb(${Math.round(s.rgb.r)}, ${Math.round(s.rgb.g)}, ${Math.round(s.rgb.b)})`,
                oklch: `oklch(${(s.L*100).toFixed(1)}% ${s.C.toFixed(2)} ${Math.round(s.h)})`
            }))
        }, null, 2);
    }
    function generateGradientsExport() {
        return generateGradients().map(g=>`/* ${g.name} */\nbackground: ${g.css};`).join('\n\n');
    }

    /* ══ RENDER ══ */
    function render() {
        palette       = generatePalette(currentColor);
        harmonyColors = generateHarmony(currentColor, currentHarmony);

        /* Harmony strip */
        if (currentHarmony === 'none' || harmonyColors.length <= 1) {
            harmonyStrip.innerHTML = '<span class="harmony-label">Aucune harmonie active</span>';
        } else {
            harmonyStrip.innerHTML = harmonyColors.map(c =>
                `<div class="harmony-swatch" style="background:${c}" title="${c}" data-color="${c}"></div>`
            ).join('') + `<span class="harmony-label">${harmonyColors.length} couleurs — cliquez pour copier</span>`;
        }

        /* Shades grid */
        shadesGrid.innerHTML = palette.map(shade => `
            <div class="shade-item" data-color="${shade.hex}">
                <div class="shade-swatch" style="background:${shade.hex}"></div>
                <div class="shade-label">${shade.name}</div>
                <div class="shade-value">${formatColor(shade)}</div>
            </div>`).join('');

        /* Cards */
        cardsGrid.innerHTML = palette.slice(2,6).map((shade,i) => `
            <div class="preview-card" style="border-color:${shade.hex}">
                <h4 style="color:${shade.hex}">Feature ${i+1}</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                <button class="preview-card-btn" style="background:${shade.hex};color:#fff">Learn More</button>
            </div>`).join('');

        /* Buttons */
        const btnSlice = palette.slice(3,8);
        buttonsShowcase.innerHTML =
            btnSlice.map(s => `<button class="showcase-btn" style="background:${s.hex};color:#fff">Button</button>`).join('') +
            btnSlice.map(s => `<button class="showcase-btn" style="border-color:${s.hex};color:${s.hex};background:transparent">Outlined</button>`).join('');

        /* Forms */
        if (palette[5]) {
            document.getElementById('formInput1').style.borderColor = palette[5].hex;
            document.getElementById('formInput2').style.borderColor = palette[5].hex;
        }
        if (palette[6]) {
            const fBtn = document.getElementById('formSubmitBtn');
            fBtn.style.background = palette[6].hex;
            fBtn.style.color = '#fff';
        }

        /* Chart */
        barChart.innerHTML = palette.slice(2,9).map((shade,i) =>
            `<div class="bar" style="background:${shade.hex};height:${(i+1)*12}%"></div>`).join('');

        /* Gradients */
        gradientsGrid.innerHTML = generateGradients().map(g =>
            `<div class="gradient-item" style="background:${g.css}" data-gradient="${g.css}">
                <div class="gradient-label">${g.name}</div>
            </div>`).join('');

        /* Export panels */
        document.getElementById('exportCSS').textContent      = generateCSSExport();
        document.getElementById('exportTailwind').textContent = generateTailwindExport();
        document.getElementById('exportJSON').textContent     = generateJSONExport();
        document.getElementById('exportGradients').textContent = generateGradientsExport();
    }

    /* ══ LISTENERS — couleur ══ */
    function applyHex(hex) {
        if (/^#[0-9A-F]{6}$/i.test(hex)) {
            currentColor = hex;
            colorPreview.style.background = hex;
            const rgb = hexToRgb(hex);
            if (rgb) colorInputRgba.value = `${rgb.r}, ${rgb.g}, ${rgb.b}`;
            render();
            return true;
        }
        return false;
    }

    colorInputHex.addEventListener('input', e => applyHex(e.target.value.trim()));
    colorInputRgba.addEventListener('input', e => {
        const rgb = parseRgba(e.target.value.trim());
        if (rgb) {
            const hex = rgbToHex(rgb.r, rgb.g, rgb.b);
            currentColor = hex;
            colorInputHex.value = hex;
            colorPreview.style.background = hex;
            render();
        }
    });
    colorPreview.addEventListener('click', () => nativePicker.click());
    nativePicker.addEventListener('input', e => {
        currentColor = e.target.value;
        colorInputHex.value = currentColor;
        colorPreview.style.background = currentColor;
        const rgb = hexToRgb(currentColor);
        if (rgb) colorInputRgba.value = `${rgb.r}, ${rgb.g}, ${rgb.b}`;
        render();
    });

    /* ══ Harmonies ══ */
    harmonyBtns.forEach(btn => btn.addEventListener('click', () => {
        harmonyBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentHarmony = btn.dataset.harmony;
        render();
        if (currentHarmony !== 'none')
            toast(`Harmonie ${currentHarmony} — ${harmonyColors.length} couleurs`);
    }));

    /* ══ Algo / Nuances / Naming ══ */
    algorithmSelect.addEventListener('change', e => { currentAlgorithm = e.target.value; render(); });
    shadesRange.addEventListener('input', e => {
        shadesCount = parseInt(e.target.value);
        shadesCountLabel.textContent = shadesCount;
        render();
    });
    namingSelect.addEventListener('change', e => { currentNaming = e.target.value; render(); });

    /* ══ Format ══ */
    formatBtns.forEach(btn => btn.addEventListener('click', () => {
        formatBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentFormat = btn.dataset.format;
        render();
    }));

    /* ══ Tabs preview ══ */
    tabs.forEach(tab => tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        tabPanels.forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        document.querySelector(`[data-panel="${tab.dataset.tab}"]`).classList.add('active');
    }));

    /* ══ Aléatoire ══ */
    document.getElementById('generateBtn').addEventListener('click', () => {
        const hex = '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6,'0');
        currentColor = hex;
        colorInputHex.value = hex;
        colorPreview.style.background = hex;
        const rgb = hexToRgb(hex);
        if (rgb) colorInputRgba.value = `${rgb.r}, ${rgb.g}, ${rgb.b}`;
        colorPreview.classList.add('pulse');
        setTimeout(() => colorPreview.classList.remove('pulse'), 600);
        render();
        toast('🎲 Couleur aléatoire !', 'success');
    });

    /* ══ Copier palette ══ */
    document.getElementById('copyPaletteBtn').addEventListener('click', () => {
        const lines = palette.map(s => `${s.name}: ${s.hex}`).join('\n');
        navigator.clipboard.writeText(lines).then(() =>
            toast(`📐 ${palette.length} couleurs copiées (${currentFormat.toUpperCase()})`, 'info')
        );
    });

    /* ══ Export modal ══ */
    document.getElementById('exportBtn').addEventListener('click', () =>
        exportModal.classList.add('open'));
    window.closeModal = () => exportModal.classList.remove('open');
    window.closeModalOutside = e => { if (e.target === exportModal) closeModal(); };
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    exportTabs.forEach(tab => tab.addEventListener('click', () => {
        exportTabs.forEach(t => t.classList.remove('active'));
        exportPanels.forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        document.querySelector(`[data-export-panel="${tab.dataset.export}"]`).classList.add('active');
    }));

    document.getElementById('copyCSSBtn').addEventListener('click', () => {
        navigator.clipboard.writeText(generateCSSExport());
        toast('✅ CSS copié !');
    });
    document.getElementById('copyTailwindBtn').addEventListener('click', () => {
        navigator.clipboard.writeText(generateTailwindExport());
        toast('✅ Tailwind config copié !');
    });
    document.getElementById('copyJSONBtn').addEventListener('click', () => {
        navigator.clipboard.writeText(generateJSONExport());
        toast('✅ JSON copié !');
    });
    document.getElementById('copyGradientsBtn').addEventListener('click', () => {
        navigator.clipboard.writeText(generateGradientsExport());
        toast('✅ Dégradés CSS copiés !');
    });

    /* ══ Copie au clic — nuances ══ */
    shadesGrid.addEventListener('click', e => {
        const item = e.target.closest('.shade-item');
        if (item) {
            navigator.clipboard.writeText(item.dataset.color);
            toast(`${item.dataset.color} copié !`, 'success');
        }
    });

    /* ══ Copie au clic — dégradés ══ */
    gradientsGrid.addEventListener('click', e => {
        const item = e.target.closest('.gradient-item');
        if (item) {
            navigator.clipboard.writeText(`background: ${item.dataset.gradient};`);
            toast('✅ Dégradé CSS copié !', 'success');
        }
    });

    /* ══ Copie au clic — harmonie ══ */
    harmonyStrip.addEventListener('click', e => {
        const swatch = e.target.closest('.harmony-swatch');
        if (swatch) {
            navigator.clipboard.writeText(swatch.dataset.color);
            toast(`${swatch.dataset.color} copié !`, 'success');
        }
    });

    /* ══ URL params (chargement depuis partage externe) ══ */
    const p = new URLSearchParams(window.location.search);
    const cp = p.get('color');
    if (cp && /^[0-9A-F]{6}$/i.test(cp)) {
        currentColor = '#'+cp;
        colorInputHex.value = currentColor;
        colorPreview.style.background = currentColor;
        const rgb = hexToRgb(currentColor);
        if (rgb) colorInputRgba.value = `${rgb.r}, ${rgb.g}, ${rgb.b}`;
    }
    const hp = p.get('harmony');
    if (hp && ['none','analogous','complementary','triadic','tetradic','split'].includes(hp)) {
        currentHarmony = hp;
        harmonyBtns.forEach(btn => btn.classList.toggle('active', btn.dataset.harmony === hp));
    }
    const ap = p.get('algo');
    if (ap && ['oklch','hsl'].includes(ap)) { currentAlgorithm = ap; algorithmSelect.value = ap; }
    const sp = p.get('shades');
    if (sp && !isNaN(sp)) { shadesCount = parseInt(sp); shadesRange.value = shadesCount; shadesCountLabel.textContent = shadesCount; }
    const np = p.get('naming');
    if (np && ['50','100'].includes(np)) { currentNaming = np; namingSelect.value = np; }

    /* ══ Init ══ */
    render();
})();
</script>
</body>
</html>