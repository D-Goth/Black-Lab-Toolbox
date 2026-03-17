<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Scriptorium — Black-Lab Toolbox</title>
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
    --danger:      #ff5f56;
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

/* ambient : .ambient__circle dans tools-shared.css */

/* ── Layout global ── */
.app {
    display: grid;
    grid-template-rows: auto 1fr;
    height: 100vh;
    overflow: hidden;
    position: relative;
    z-index: 1;
}

/* .hdr : défini dans tools-shared.css */
.hdr__badge {
    font-size: 0.6rem;
    font-weight: 700;
    padding: 0.18rem 0.55rem;
    border-radius: 20px;
    border: 1px solid rgba(39,201,63,0.4);
    color: var(--success);
    background: rgba(39,201,63,0.08);
    letter-spacing: 0.06em;
    text-transform: uppercase;
    white-space: nowrap;
}

/* ── Corps : 2 colonnes ── */
.body {
    display: grid;
    grid-template-columns: 300px 1fr;
    overflow: hidden;
    min-height: 0;
}

/* ══════════════════════════
   COLONNE GAUCHE — fichiers
══════════════════════════ */
.col-left {
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: var(--bg);
}

.col-left__head {
    padding: 0.7rem 0.9rem;
    border-bottom: 1px solid var(--border);
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--accent);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.file-count {
    font-size: 0.6rem;
    background: var(--accent-soft);
    border: 1px solid rgba(255,22,84,0.3);
    color: var(--accent);
    border-radius: 10px;
    padding: 0.1rem 0.45rem;
    font-weight: 700;
}

/* Drop zone */
.drop-zone {
    margin: 0.7rem;
    border: 2px dashed rgba(255,22,84,0.3);
    border-radius: var(--radius);
    padding: 1.2rem 1rem;
    text-align: center;
    cursor: pointer;
    transition: all var(--transition);
    background: rgba(255,22,84,0.02);
    flex-shrink: 0;
}
.drop-zone:hover, .drop-zone.dragover {
    border-color: var(--accent);
    background: rgba(255,22,84,0.06);
    box-shadow: 0 0 0 3px rgba(255,22,84,0.12);
}
.drop-zone svg { width: 28px; height: 28px; color: var(--accent); margin-bottom: 0.4rem; opacity: 0.7; }
.drop-zone p { font-size: 0.75rem; color: var(--text-muted); line-height: 1.45; }
.drop-zone span { font-size: 0.65rem; color: var(--text-dim); display: block; margin-top: 0.25rem; }

/* File list */
.file-list {
    flex: 1;
    overflow-y: auto;
    padding: 0 0.7rem 0.7rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.file-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.6rem 0.75rem;
    display: grid;
    grid-template-columns: 42px 1fr auto;
    gap: 0.6rem;
    align-items: center;
    transition: border-color var(--transition), background var(--transition);
    cursor: default;
}
.file-card:hover { border-color: rgba(255,22,84,0.35); background: var(--surface-h); }

.file-thumb {
    width: 42px; height: 52px;
    background: var(--bg-2);
    border-radius: 4px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.2rem;
    border: 1px solid var(--border);
}
.file-thumb img { width: 100%; height: 100%; object-fit: cover; }

.file-info { min-width: 0; }
.file-name {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 0.15rem;
}
.file-meta { font-size: 0.65rem; color: var(--text-muted); }

.file-remove {
    background: rgba(255,87,86,0.1);
    border: 1px solid rgba(255,87,86,0.3);
    border-radius: 50%;
    width: 22px; height: 22px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    color: var(--danger);
    font-size: 0.65rem;
    flex-shrink: 0;
    transition: all var(--transition);
    font-family: inherit;
}
.file-remove:hover { background: rgba(255,87,86,0.25); transform: scale(1.1); }

/* Empty state */
.files-empty {
    padding: 1.5rem 0.5rem;
    text-align: center;
    color: var(--text-dim);
    font-size: 0.75rem;
    display: none;
}
.files-empty svg { width: 28px; height: 28px; opacity: 0.2; margin-bottom: 0.5rem; }

/* ══════════════════════════
   COLONNE DROITE — outils
══════════════════════════ */
.col-right {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    min-height: 0;
}

/* Tabs navigation */
.tabs-nav {
    display: flex;
    border-bottom: 1px solid var(--border);
    background: var(--bg-2);
    overflow-x: auto;
    flex-shrink: 0;
}
.tabs-nav::-webkit-scrollbar { height: 2px; }

.tab-btn {
    padding: 0.65rem 1rem;
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    color: var(--text-muted);
    font-size: 0.78rem;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: all var(--transition);
    white-space: nowrap;
    margin-bottom: -1px;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}
.tab-btn:hover { color: var(--text); }
.tab-btn.active { color: var(--accent); border-bottom-color: var(--accent); }

/* Contenu scrollable */
.col-right__body {
    flex: 1;
    overflow-y: auto;
    padding: 1rem 1.1rem;
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
    min-height: 0;
}

/* Tab panes */
.tab-pane { display: none; }
.tab-pane.active { display: flex; flex-direction: column; gap: 0.85rem; }

/* Panel */
.panel {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
}
.panel__head {
    padding: 0.6rem 0.9rem;
    border-bottom: 1px solid var(--border);
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--accent);
    background: rgba(10,10,14,0.4);
}
.panel__body { padding: 0.9rem; display: flex; flex-direction: column; gap: 0.65rem; }

/* Intro text */
.intro-text {
    font-size: 0.78rem;
    color: var(--text-muted);
    line-height: 1.5;
}

/* Tool options */
.tool-options {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
}

/* Inputs */
.form-select, .form-input {
    padding: 0.42rem 0.75rem;
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text);
    font-size: 0.8rem;
    font-family: inherit;
    outline: none;
    transition: border-color var(--transition), box-shadow var(--transition);
    cursor: pointer;
}
.form-select:focus, .form-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px var(--accent-soft);
}
.form-select option { background: var(--bg-2); color: var(--text); }
.form-input::placeholder { color: var(--text-dim); }
.form-input[type="number"] { width: 75px; }

/* Page checkboxes */
.page-selector {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
    margin-top: 0.2rem;
}
.page-checkbox {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    background: var(--bg-2);
    border: 1px solid var(--border);
    padding: 0.28rem 0.65rem;
    border-radius: var(--radius-sm);
    cursor: pointer;
    color: var(--text-muted);
    font-size: 0.72rem;
    font-family: inherit;
    transition: all var(--transition);
    user-select: none;
}
.page-checkbox:hover { border-color: var(--accent); color: var(--text); }
.page-checkbox input[type="checkbox"] { accent-color: var(--accent); cursor: pointer; width: 12px; height: 12px; }
.page-checkbox input:checked + span { color: var(--text); }

/* Boutons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.42rem 0.85rem;
    border-radius: var(--radius-sm);
    font-size: 0.78rem;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: all var(--transition);
    border: 1px solid var(--border);
    background: var(--surface);
    color: var(--text-muted);
    white-space: nowrap;
}
.btn:hover:not(:disabled) { color: var(--text); border-color: var(--accent); background: var(--surface-h); }
.btn:disabled { opacity: 0.35; cursor: not-allowed; }

.btn--primary { background: var(--gradient); border-color: transparent; color: #fff; }
.btn--primary:hover:not(:disabled) { opacity: 0.88; border-color: transparent; }
.btn--success { border-color: rgba(39,201,63,0.4); color: var(--success); }
.btn--success:hover:not(:disabled) { background: rgba(39,201,63,0.1); border-color: var(--success); }
.btn--danger { border-color: rgba(255,87,86,0.4); color: var(--danger); }
.btn--danger:hover:not(:disabled) { background: rgba(255,87,86,0.1); border-color: var(--danger); }
.btn--warning { border-color: rgba(255,189,46,0.4); color: var(--warning); }
.btn--warning:hover:not(:disabled) { background: rgba(255,189,46,0.1); border-color: var(--warning); }

/* Progress bar */
.progress-bar {
    width: 100%;
    height: 3px;
    background: var(--surface-h);
    border-radius: 3px;
    overflow: hidden;
    display: none;
}
.progress-bar.show { display: block; }
.progress-fill {
    height: 100%;
    background: var(--gradient);
    width: 0%;
    transition: width 0.3s ease;
}

/* OCR status */
.ocr-status {
    padding: 0.55rem 0.9rem;
    background: rgba(255,22,84,0.06);
    border-left: 3px solid var(--accent);
    border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
    font-family: 'Fira Code', monospace;
    font-size: 0.75rem;
    color: var(--text-muted);
    display: none;
    animation: ocrPulse 1.5s ease-in-out infinite;
}
@keyframes ocrPulse {
    0%,100% { opacity: 1; }
    50%      { opacity: 0.55; }
}

/* Console résultat */
.console-frame {
    background: #0a0a0e;
    border: 1px solid var(--accent);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0,0,0,0.4);
    display: none;
}
.console-frame.show { display: block; }
.console-header {
    background: #111116;
    padding: 0.5rem 0.85rem;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    display: flex;
    align-items: center;
    gap: 0.6rem;
}
.dots { display: flex; gap: 6px; }
.dot { width: 10px; height: 10px; border-radius: 50%; }
.d-r { background: #ff5f56; } .d-y { background: #ffbd2e; } .d-g { background: #27c93f; }
.console-title {
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--accent);
}
.console-body {
    padding: 1rem;
    max-height: 380px;
    overflow: auto;
    color: var(--text);
    font-family: 'Fira Code', monospace;
    font-size: 0.78rem;
    background: #0a0a0e;
}
.console-body pre {
    background: transparent;
    font-family: inherit;
    font-size: inherit;
    white-space: pre-wrap;
    word-break: break-word;
    margin: 0; padding: 0;
    border: none; box-shadow: none;
}
.console-body img { max-width: 100%; border-radius: var(--radius-sm); }

.download-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.75rem;
}
.download-item {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.45rem 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 0.75rem;
    color: var(--text-muted);
}

/* ── Toast ── */
.toast {
    position: fixed;
    bottom: 1.2rem; right: 1.2rem;
    background: #111116;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 0.6rem 1rem;
    font-size: 0.78rem;
    font-weight: 500;
    color: var(--text);
    z-index: 9999;
    opacity: 0;
    transform: translateY(8px);
    transition: all 0.25s ease;
    pointer-events: none;
    box-shadow: 0 8px 24px rgba(0,0,0,0.5);
    max-width: 300px;
}
.toast.show { opacity: 1; transform: translateY(0); }
.toast.success { border-color: var(--success); }
.toast.error   { border-color: var(--danger); }
.toast.info    { border-color: var(--accent); }

/* ── Scrollbar ── */

@media (max-width: 760px) {
    .body { grid-template-columns: 1fr; grid-template-rows: auto 1fr; }
    .col-left { border-right: none; border-bottom: 1px solid var(--border); max-height: 40vh; }
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

    <!-- ══ HEADER ═══════════════════════════════════════════════ -->
    <header class="hdr">
        <span class="hdr__icon">📜</span>
        <span class="hdr__title">SCRIPTORIUM PDF</span>
        <span class="hdr__badge">100% client-side</span>
        <span class="hdr__meta">Fusion, split, rotation, suppression, extraction texte &amp; OCR, conversion image</span>
    </header>

    <!-- ══ CORPS ════════════════════════════════════════════════ -->
    <div class="body">

        <!-- ── COLONNE GAUCHE — Fichiers ── -->
        <div class="col-left">
            <div class="col-left__head">
                📁 Fichiers PDF
                <span class="file-count" id="fileCount">0</span>
            </div>

            <!-- Drop zone -->
            <div class="drop-zone" id="dropZone">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <p>Glissez-déposez vos PDF ici<br>ou cliquez pour sélectionner</p>
                <span>Plusieurs fichiers acceptés</span>
                <input type="file" id="fileInput" accept=".pdf" multiple style="display:none">
            </div>

            <!-- Liste fichiers -->
            <div class="file-list" id="fileList">
                <div class="files-empty" id="filesEmpty" style="display:flex;flex-direction:column;align-items:center">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <span>Aucun fichier chargé</span>
                </div>
            </div>
        </div><!-- /.col-left -->

        <!-- ── COLONNE DROITE — Outils ── -->
        <div class="col-right">

            <!-- Tabs -->
            <div class="tabs-nav">
                <button class="tab-btn active" data-tab="merge">🔗 Fusion</button>
                <button class="tab-btn" data-tab="split">✂️ Split</button>
                <button class="tab-btn" data-tab="rotate">🔄 Rotation</button>
                <button class="tab-btn" data-tab="delete">🗑️ Supprimer pages</button>
                <button class="tab-btn" data-tab="text">📝 Extraction texte</button>
                <button class="tab-btn" data-tab="image">🖼️ Conversion image</button>
            </div>

            <!-- Corps scrollable -->
            <div class="col-right__body">

                <!-- ── FUSION ── -->
                <div class="tab-pane active" id="tab-merge">
                    <div class="panel">
                        <div class="panel__head">🔗 Fusionner plusieurs PDF</div>
                        <div class="panel__body">
                            <p class="intro-text">Chargez au moins 2 fichiers PDF à gauche, puis cliquez sur Fusionner. L'ordre suit celui de la liste.</p>
                            <div class="tool-options">
                                <button class="btn btn--primary" id="mergeBtn" disabled>🔗 Fusionner</button>
                            </div>
                        </div>
                    </div>
                    <div class="progress-bar" id="mergeProg"><div class="progress-fill" id="mergeProgFill"></div></div>
                    <div class="console-frame" id="mergeResult">
                        <div class="console-header"><div class="dots"><div class="dot d-r"></div><div class="dot d-y"></div><div class="dot d-g"></div></div><span class="console-title">Résultat</span></div>
                        <div class="console-body"><div id="mergeResultContent"></div><div id="mergeDownloadList" class="download-list"></div></div>
                    </div>
                </div>

                <!-- ── SPLIT ── -->
                <div class="tab-pane" id="tab-split">
                    <div class="panel">
                        <div class="panel__head">✂️ Extraire une plage de pages</div>
                        <div class="panel__body">
                            <p class="intro-text">Choisissez un fichier et spécifiez les pages à extraire. Exemples : <code style="color:var(--accent);font-family:monospace">1-3</code>, <code style="color:var(--accent);font-family:monospace">1,3,5</code>, <code style="color:var(--accent);font-family:monospace">2-4,7</code></p>
                            <div class="tool-options">
                                <select id="splitFileSelect" class="form-select" style="min-width:180px"></select>
                                <input type="text" id="splitRange" class="form-input" placeholder="ex: 1-3,5" value="1" style="width:120px">
                                <button class="btn btn--primary" id="splitBtn" disabled>✂️ Split</button>
                            </div>
                        </div>
                    </div>
                    <div class="progress-bar" id="splitProg"><div class="progress-fill" id="splitProgFill"></div></div>
                    <div class="console-frame" id="splitResult">
                        <div class="console-header"><div class="dots"><div class="dot d-r"></div><div class="dot d-y"></div><div class="dot d-g"></div></div><span class="console-title">Résultat</span></div>
                        <div class="console-body"><div id="splitResultContent"></div><div id="splitDownloadList" class="download-list"></div></div>
                    </div>
                </div>

                <!-- ── ROTATION ── -->
                <div class="tab-pane" id="tab-rotate">
                    <div class="panel">
                        <div class="panel__head">🔄 Rotation de pages</div>
                        <div class="panel__body">
                            <p class="intro-text">Sélectionnez un fichier, l'angle de rotation, puis cochez les pages à pivoter.</p>
                            <div class="tool-options">
                                <select id="rotateFileSelect" class="form-select" style="min-width:180px"></select>
                                <select id="rotateAngle" class="form-select">
                                    <option value="90">90°</option>
                                    <option value="180">180°</option>
                                    <option value="270">270°</option>
                                </select>
                                <button class="btn btn--primary" id="rotateBtn" disabled>🔄 Appliquer</button>
                            </div>
                            <div id="rotatePageSelector" class="page-selector"></div>
                        </div>
                    </div>
                    <div class="progress-bar" id="rotateProg"><div class="progress-fill" id="rotateProgFill"></div></div>
                    <div class="console-frame" id="rotateResult">
                        <div class="console-header"><div class="dots"><div class="dot d-r"></div><div class="dot d-y"></div><div class="dot d-g"></div></div><span class="console-title">Résultat</span></div>
                        <div class="console-body"><div id="rotateResultContent"></div><div id="rotateDownloadList" class="download-list"></div></div>
                    </div>
                </div>

                <!-- ── SUPPRESSION ── -->
                <div class="tab-pane" id="tab-delete">
                    <div class="panel">
                        <div class="panel__head">🗑️ Supprimer des pages</div>
                        <div class="panel__body">
                            <p class="intro-text">Sélectionnez un fichier et cochez les pages à supprimer définitivement.</p>
                            <div class="tool-options">
                                <select id="deleteFileSelect" class="form-select" style="min-width:180px"></select>
                                <button class="btn btn--danger" id="deleteBtn" disabled>🗑️ Supprimer</button>
                            </div>
                            <div id="deletePageSelector" class="page-selector"></div>
                        </div>
                    </div>
                    <div class="progress-bar" id="deleteProg"><div class="progress-fill" id="deleteProgFill"></div></div>
                    <div class="console-frame" id="deleteResult">
                        <div class="console-header"><div class="dots"><div class="dot d-r"></div><div class="dot d-y"></div><div class="dot d-g"></div></div><span class="console-title">Résultat</span></div>
                        <div class="console-body"><div id="deleteResultContent"></div><div id="deleteDownloadList" class="download-list"></div></div>
                    </div>
                </div>

                <!-- ── EXTRACTION TEXTE ── -->
                <div class="tab-pane" id="tab-text">
                    <div class="panel">
                        <div class="panel__head">📝 Extraction de texte</div>
                        <div class="panel__body">
                            <p class="intro-text">Extraction automatique du texte natif. Si le PDF est scanné (sans couche texte), l'OCR Tesseract.js prendra automatiquement le relais.</p>
                            <div class="tool-options">
                                <select id="textFileSelect" class="form-select" style="min-width:180px"></select>
                                <select id="ocrLang" class="form-select">
                                    <option value="fra">Français</option>
                                    <option value="eng">English</option>
                                    <option value="fra+eng">Français + English</option>
                                    <option value="deu">Deutsch</option>
                                    <option value="spa">Español</option>
                                </select>
                                <button class="btn btn--primary" id="extractTextBtn" disabled>📝 Extraire</button>
                            </div>
                            <div class="ocr-status" id="ocrStatus"></div>
                        </div>
                    </div>
                    <div class="progress-bar" id="textProg"><div class="progress-fill" id="textProgFill"></div></div>
                    <div class="console-frame" id="textResult">
                        <div class="console-header"><div class="dots"><div class="dot d-r"></div><div class="dot d-y"></div><div class="dot d-g"></div></div><span class="console-title">Texte extrait</span></div>
                        <div class="console-body"><div id="textResultContent"></div><div id="textDownloadList" class="download-list"></div></div>
                    </div>
                </div>

                <!-- ── CONVERSION IMAGE ── -->
                <div class="tab-pane" id="tab-image">
                    <div class="panel">
                        <div class="panel__head">🖼️ Convertir une page en image</div>
                        <div class="panel__body">
                            <p class="intro-text">Sélectionnez un fichier, un numéro de page et le format de sortie souhaité.</p>
                            <div class="tool-options">
                                <select id="imageFileSelect" class="form-select" style="min-width:180px"></select>
                                <input type="number" id="imagePage" min="1" value="1" class="form-input">
                                <select id="imageFormat" class="form-select">
                                    <option value="jpg">JPG</option>
                                    <option value="png">PNG</option>
                                </select>
                                <button class="btn btn--primary" id="convertImageBtn" disabled>🖼️ Convertir</button>
                            </div>
                        </div>
                    </div>
                    <div class="progress-bar" id="imageProg"><div class="progress-fill" id="imageProgFill"></div></div>
                    <div class="console-frame" id="imageResult">
                        <div class="console-header"><div class="dots"><div class="dot d-r"></div><div class="dot d-y"></div><div class="dot d-g"></div></div><span class="console-title">Image générée</span></div>
                        <div class="console-body"><div id="imageResultContent"></div><div id="imageDownloadList" class="download-list"></div></div>
                    </div>
                </div>

            </div><!-- /.col-right__body -->
        </div><!-- /.col-right -->

    </div><!-- /.body -->
</div><!-- /.app -->

<div class="toast" id="toast"></div>

<!-- ── Bibliothèques ── -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>

<script>
(function () {
'use strict';

/* ══ PDF.JS WORKER ══ */
pdfjsLib.GlobalWorkerOptions.workerSrc =
    'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

/* ══ STATE ══ */
let files  = [];
let nextId = 1;

/* ══ DOM ══ */
const dropZone    = document.getElementById('dropZone');
const fileInput   = document.getElementById('fileInput');
const fileList    = document.getElementById('fileList');
const filesEmpty  = document.getElementById('filesEmpty');
const fileCountEl = document.getElementById('fileCount');

const splitFileSelect  = document.getElementById('splitFileSelect');
const rotateFileSelect = document.getElementById('rotateFileSelect');
const deleteFileSelect = document.getElementById('deleteFileSelect');
const textFileSelect   = document.getElementById('textFileSelect');
const imageFileSelect  = document.getElementById('imageFileSelect');

const mergeBtn       = document.getElementById('mergeBtn');
const splitBtn       = document.getElementById('splitBtn');
const rotateBtn      = document.getElementById('rotateBtn');
const deleteBtn      = document.getElementById('deleteBtn');
const extractTextBtn = document.getElementById('extractTextBtn');
const convertImageBtn= document.getElementById('convertImageBtn');

/* ══ TOAST ══ */
let toastTimer;
function toast(msg, type) {
    const t = document.getElementById('toast');
    t.textContent = msg; t.className = `toast show ${type||'info'}`;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), 3200);
}

/* ══ PROGRESS ══ */
function showProg(id, pct) {
    const bar = document.getElementById(id);
    const fill= document.getElementById(id+'Fill');
    if (!bar||!fill) return;
    bar.classList.add('show');
    fill.style.width = pct + '%';
}
function hideProg(id) {
    const bar = document.getElementById(id);
    if (bar) bar.classList.remove('show');
}

/* ══ RESULT ══ */
function displayResult(frameId, contentId, listId, html, downloads=[]) {
    const frame = document.getElementById(frameId);
    const cont  = document.getElementById(contentId);
    const list  = document.getElementById(listId);
    frame.classList.add('show');
    if (typeof html === 'string') cont.innerHTML = html;
    list.innerHTML = '';
    downloads.forEach(item => {
        const div = document.createElement('div');
        div.className = 'download-item';
        const btn = document.createElement('button');
        btn.className = 'btn btn--success';
        btn.textContent = '💾 ' + item.name;
        btn.onclick = () => { const a=document.createElement('a'); a.href=item.data; a.download=item.name; a.click(); };
        div.innerHTML = `<span style="font-size:0.72rem;color:var(--text-muted)">${item.name}</span>`;
        div.appendChild(btn);
        list.appendChild(div);
    });
}

/* ══ TABS ══ */
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
    });
});

/* ══ DROP ZONE ══ */
dropZone.addEventListener('click', () => fileInput.click());
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => { e.preventDefault(); dropZone.classList.remove('dragover'); handleFiles(e.dataTransfer.files); });
fileInput.addEventListener('change', () => handleFiles(fileInput.files));

/* ══ UTILS ══ */
async function getArrayBuffer(file) {
    return new Promise((res, rej) => { const r=new FileReader(); r.onload=()=>res(r.result); r.onerror=rej; r.readAsArrayBuffer(file); });
}
async function getPdfDocument(ab) {
    return await pdfjsLib.getDocument({ data: ab }).promise;
}
async function generateThumbnail(ab) {
    try {
        const pdf=await getPdfDocument(ab), page=await pdf.getPage(1);
        const vp=page.getViewport({scale:0.5});
        const c=document.createElement('canvas');
        c.width=vp.width; c.height=vp.height;
        await page.render({canvasContext:c.getContext('2d'),viewport:vp}).promise;
        return c.toDataURL('image/jpeg',0.6);
    } catch { return null; }
}
function parseRange(str) {
    const result=[];
    str.split(',').forEach(part => {
        const t=part.trim();
        if (t.includes('-')) { const [a,b]=t.split('-').map(Number); if(!isNaN(a)&&!isNaN(b)&&a<=b) for(let i=a;i<=b;i++) result.push(i); }
        else { const n=Number(t); if(!isNaN(n)&&n>0) result.push(n); }
    });
    return [...new Set(result)].sort((a,b)=>a-b);
}
function isTextEmpty(text, numPages) {
    const cleaned = text.replace(/---\s*Page\s*\d+\s*---/g,'').replace(/\s+/g,'').trim();
    return cleaned.length < numPages * 5;
}
async function renderPageToCanvas(pdfPage) {
    const vp=pdfPage.getViewport({scale:2.0});
    const c=document.createElement('canvas'); c.width=vp.width; c.height=vp.height;
    await pdfPage.render({canvasContext:c.getContext('2d'),viewport:vp}).promise;
    return c;
}

/* ══ HANDLE FILES ══ */
async function handleFiles(newFiles) {
    const pdfs = Array.from(newFiles).filter(f => f.type === 'application/pdf');
    if (!pdfs.length) { toast('⚠️ Sélectionnez uniquement des fichiers PDF','error'); return; }
    for (const file of pdfs) {
        try {
            const ab  = await getArrayBuffer(file);
            const pdf = await getPdfDocument(ab);
            const thumb = await generateThumbnail(ab);
            files.push({ id:nextId++, file, name:file.name, size:file.size, pages:pdf.numPages, thumbnail:thumb, arrayBuffer:ab });
        } catch(e) {
            toast(`❌ Impossible de charger ${file.name}`, 'error');
        }
    }
    updateUI();
}

/* ══ UPDATE UI ══ */
function updateUI() {
    fileCountEl.textContent = files.length;
    filesEmpty.style.display = files.length === 0 ? 'flex' : 'none';

    /* File list */
    const existingCards = fileList.querySelectorAll('.file-card');
    existingCards.forEach(c => c.remove());
    files.forEach(f => {
        const card = document.createElement('div');
        card.className = 'file-card';
        card.dataset.id = f.id;
        const thumb = f.thumbnail
            ? `<div class="file-thumb"><img src="${f.thumbnail}" alt=""></div>`
            : `<div class="file-thumb">📄</div>`;
        card.innerHTML = `${thumb}
            <div class="file-info">
                <div class="file-name" title="${f.name}">${f.name}</div>
                <div class="file-meta">${f.pages} page${f.pages>1?'s':''} · ${(f.size/1024).toFixed(1)} Ko</div>
            </div>
            <button class="file-remove" title="Retirer" onclick="removeFile(${f.id})">✕</button>`;
        fileList.insertBefore(card, filesEmpty);
    });

    /* Buttons */
    mergeBtn.disabled        = files.length < 2;
    splitBtn.disabled        = files.length === 0;
    rotateBtn.disabled       = files.length === 0;
    deleteBtn.disabled       = files.length === 0;
    extractTextBtn.disabled  = files.length === 0;
    convertImageBtn.disabled = files.length === 0;

    /* Selects */
    const opts = files.length
        ? files.map(f=>`<option value="${f.id}">${f.name} (${f.pages}p.)</option>`).join('')
        : '<option value="">Aucun fichier chargé</option>';
    [splitFileSelect,rotateFileSelect,deleteFileSelect,textFileSelect,imageFileSelect]
        .forEach(s => { s.innerHTML = opts; });

    updatePageSelectors();
}

window.removeFile = function(id) {
    files = files.filter(f => f.id !== id);
    updateUI();
};

/* ══ PAGE SELECTORS ══ */
async function updatePageSelectors() {
    ['rotate','delete'].forEach(type => {
        const sel    = document.getElementById(`${type}FileSelect`);
        const target = document.getElementById(`${type}PageSelector`);
        const file   = files.find(f => f.id == sel?.value);
        if (file) {
            target.innerHTML = Array.from({length:file.pages},(_,i)=>
                `<label class="page-checkbox"><input type="checkbox" class="${type}-page" value="${i+1}"><span>Page ${i+1}</span></label>`
            ).join('');
        } else { target.innerHTML = ''; }
    });
}
rotateFileSelect.addEventListener('change', updatePageSelectors);
deleteFileSelect.addEventListener('change', updatePageSelectors);

/* ══ FUSION ══ */
mergeBtn.addEventListener('click', async () => {
    if (files.length < 2) return;
    showProg('mergeProg', 30);
    try {
        const merged = await PDFLib.PDFDocument.create();
        for (const f of files) {
            const pdf    = await PDFLib.PDFDocument.load(f.arrayBuffer);
            const copied = await merged.copyPages(pdf, pdf.getPageIndices());
            copied.forEach(p => merged.addPage(p));
        }
        showProg('mergeProg', 80);
        const bytes = await merged.save();
        const url   = URL.createObjectURL(new Blob([bytes],{type:'application/pdf'}));
        displayResult('mergeResult','mergeResultContent','mergeDownloadList',
            `<span style="color:var(--success)">✅ Fusion terminée — ${files.length} fichiers · ${(bytes.length/1024).toFixed(1)} Ko</span>`,
            [{name:'fusion.pdf', data:url}]);
        toast('✅ Fusion réussie !','success');
    } catch(e) { toast('❌ Erreur fusion : '+e.message,'error'); }
    finally { hideProg('mergeProg'); }
});

/* ══ SPLIT ══ */
splitBtn.addEventListener('click', async () => {
    const file = files.find(f => f.id == splitFileSelect.value);
    if (!file) return;
    const pages = parseRange(document.getElementById('splitRange').value);
    if (!pages.length) { toast('⚠️ Plage de pages invalide','error'); return; }
    showProg('splitProg', 20);
    try {
        const src  = await PDFLib.PDFDocument.load(file.arrayBuffer);
        const dest = await PDFLib.PDFDocument.create();
        const indices = pages.filter(p=>p>=1&&p<=src.getPageCount()).map(p=>p-1);
        const copied  = await dest.copyPages(src, indices);
        copied.forEach(p => dest.addPage(p));
        showProg('splitProg', 80);
        const bytes = await dest.save();
        const url   = URL.createObjectURL(new Blob([bytes],{type:'application/pdf'}));
        displayResult('splitResult','splitResultContent','splitDownloadList',
            `<span style="color:var(--success)">✅ Split terminé — ${indices.length} page(s) extraite(s)</span>`,
            [{name:`split_${file.name}`, data:url}]);
        toast('✅ Split réussi !','success');
    } catch(e) { toast('❌ Erreur split : '+e.message,'error'); }
    finally { hideProg('splitProg'); }
});

/* ══ ROTATION ══ */
rotateBtn.addEventListener('click', async () => {
    const file  = files.find(f => f.id == rotateFileSelect.value);
    if (!file) return;
    const angle = parseInt(document.getElementById('rotateAngle').value);
    const cbs   = document.querySelectorAll('.rotate-page:checked');
    const pages = Array.from(cbs).map(cb => parseInt(cb.value)-1);
    if (!pages.length) { toast('⚠️ Sélectionnez au moins une page','error'); return; }
    showProg('rotateProg', 30);
    try {
        const doc   = await PDFLib.PDFDocument.load(file.arrayBuffer);
        const docPages = doc.getPages();
        pages.forEach(i => { if(i<docPages.length) docPages[i].setRotation(PDFLib.degrees(angle)); });
        showProg('rotateProg', 80);
        const bytes = await doc.save();
        const url   = URL.createObjectURL(new Blob([bytes],{type:'application/pdf'}));
        displayResult('rotateResult','rotateResultContent','rotateDownloadList',
            `<span style="color:var(--success)">✅ Rotation ${angle}° appliquée sur ${pages.length} page(s)</span>`,
            [{name:`rotate_${file.name}`, data:url}]);
        toast('✅ Rotation appliquée !','success');
    } catch(e) { toast('❌ Erreur rotation : '+e.message,'error'); }
    finally { hideProg('rotateProg'); }
});

/* ══ SUPPRESSION ══ */
deleteBtn.addEventListener('click', async () => {
    const file = files.find(f => f.id == deleteFileSelect.value);
    if (!file) return;
    const cbs  = document.querySelectorAll('.delete-page:checked');
    const toDel= Array.from(cbs).map(cb => parseInt(cb.value)-1);
    if (!toDel.length) { toast('⚠️ Sélectionnez au moins une page','error'); return; }
    showProg('deleteProg', 30);
    try {
        const doc   = await PDFLib.PDFDocument.load(file.arrayBuffer);
        const total = doc.getPageCount();
        const keep  = Array.from({length:total},(_,i)=>i).filter(i=>!toDel.includes(i));
        if (!keep.length) { toast('⚠️ Le document résultant serait vide','error'); hideProg('deleteProg'); return; }
        const dest   = await PDFLib.PDFDocument.create();
        const copied = await dest.copyPages(doc, keep);
        copied.forEach(p => dest.addPage(p));
        showProg('deleteProg', 80);
        const bytes = await dest.save();
        const url   = URL.createObjectURL(new Blob([bytes],{type:'application/pdf'}));
        displayResult('deleteResult','deleteResultContent','deleteDownloadList',
            `<span style="color:var(--success)">✅ ${toDel.length} page(s) supprimée(s) — ${keep.length} page(s) conservée(s)</span>`,
            [{name:`sans_pages_${file.name}`, data:url}]);
        toast('✅ Pages supprimées !','success');
    } catch(e) { toast('❌ Erreur suppression : '+e.message,'error'); }
    finally { hideProg('deleteProg'); }
});

/* ══ EXTRACTION TEXTE + OCR ══ */
extractTextBtn.addEventListener('click', async () => {
    const file    = files.find(f => f.id == textFileSelect.value);
    if (!file) return;
    const ocrLang = document.getElementById('ocrLang').value;
    const ocrSt   = document.getElementById('ocrStatus');
    extractTextBtn.disabled = true;
    ocrSt.style.display = 'none';
    showProg('textProg', 5);
    try {
        /* Étape 1 : extraction native pdf.js */
        const pdf = await getPdfDocument(file.arrayBuffer);
        let fullText = '';
        for (let i=1; i<=pdf.numPages; i++) {
            const page = await pdf.getPage(i);
            const tc   = await page.getTextContent();
            fullText  += `--- Page ${i} ---\n${tc.items.map(it=>it.str).join(' ')}\n\n`;
            showProg('textProg', 5 + Math.round((i/pdf.numPages)*25));
        }
        /* Étape 2 : fallback OCR si PDF scanné */
        if (isTextEmpty(fullText, pdf.numPages)) {
            toast('🔍 PDF scanné — démarrage OCR Tesseract…','info');
            ocrSt.style.display = 'block';
            ocrSt.textContent   = '🔍 Initialisation de l\'OCR…';
            const worker = await Tesseract.createWorker(ocrLang, 1, { logger:()=>{} });
            fullText = '';
            for (let i=1; i<=pdf.numPages; i++) {
                showProg('textProg', 30 + Math.round((i/pdf.numPages)*65));
                ocrSt.textContent = `🔍 OCR — page ${i} / ${pdf.numPages}…`;
                const pdfPage = await pdf.getPage(i);
                const canvas  = await renderPageToCanvas(pdfPage);
                const {data}  = await worker.recognize(canvas);
                fullText += `--- Page ${i} ---\n${data.text}\n\n`;
            }
            await worker.terminate();
            ocrSt.textContent = '✅ OCR terminé.';
            toast('✅ OCR terminé !','success');
        } else {
            toast('✅ Texte extrait !','success');
        }
        /* Étape 3 : affichage */
        const blob    = new Blob([fullText],{type:'text/plain'});
        const url     = URL.createObjectURL(blob);
        const preview = fullText.length > 3000 ? fullText.substring(0,3000)+'\n[…texte tronqué dans l\'aperçu]' : fullText;
        displayResult('textResult','textResultContent','textDownloadList',
            `<pre>${preview}</pre>`,
            [{name:file.name.replace('.pdf','.txt'), data:url}]);
    } catch(e) {
        ocrSt.style.display = 'none';
        toast('❌ Erreur extraction : '+e.message,'error');
    } finally {
        hideProg('textProg');
        extractTextBtn.disabled = false;
    }
});

/* ══ CONVERSION IMAGE ══ */
convertImageBtn.addEventListener('click', async () => {
    const file   = files.find(f => f.id == imageFileSelect.value);
    if (!file) return;
    const pageNum= parseInt(document.getElementById('imagePage').value);
    const format = document.getElementById('imageFormat').value;
    if (pageNum<1||pageNum>file.pages) { toast(`⚠️ Page invalide (1–${file.pages})`,'error'); return; }
    showProg('imageProg', 50);
    try {
        const pdf    = await getPdfDocument(file.arrayBuffer);
        const page   = await pdf.getPage(pageNum);
        const scale  = 1.5;
        const vp     = page.getViewport({scale});
        const canvas = document.createElement('canvas');
        canvas.width=vp.width; canvas.height=vp.height;
        await page.render({canvasContext:canvas.getContext('2d'),viewport:vp}).promise;
        const dataUrl = format==='jpg'
            ? canvas.toDataURL('image/jpeg',0.9)
            : canvas.toDataURL('image/png');
        showProg('imageProg', 90);
        displayResult('imageResult','imageResultContent','imageDownloadList',
            `<img src="${dataUrl}" style="max-width:100%;border-radius:var(--radius-sm)">`,
            [{name:`page${pageNum}.${format}`, data:dataUrl}]);
        toast('✅ Conversion réussie !','success');
    } catch(e) { toast('❌ Erreur conversion : '+e.message,'error'); }
    finally { hideProg('imageProg'); }
});

/* ══ INIT ══ */
updateUI();

})();
</script>
</body>
</html>