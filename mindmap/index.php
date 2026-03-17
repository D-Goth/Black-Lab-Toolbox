<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mindmap — Black-Lab Toolbox</title>
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
    /* KG */
    --kg:          #7c3aed;
    --kg-light:    #a855f7;
    --kg-soft:     rgba(124,58,237,0.18);
    --kg-border:   rgba(124,58,237,0.35);
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

/* ══════════════════════════
   APP — 3 rangées fixes
   header / body / toolbar
══════════════════════════ */
.app {
    display: grid;
    grid-template-rows: auto 1fr auto;
    height: 100vh;
    overflow: hidden;
    position: relative;
    z-index: 1;
}

/* .hdr : défini dans tools-shared.css */

/* Badge de mode */
.mode-badge {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.2rem 0.65rem;
    border-radius: 20px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    border: 1px solid var(--accent);
    color: var(--accent);
    background: var(--accent-soft);
    transition: all 0.3s;
}
.mode-badge.kg-active {
    border-color: var(--kg-light);
    color: var(--kg-light);
    background: var(--kg-soft);
}

/* ── Corps scrollable ── */
.body {
    overflow-y: auto;
    overflow-x: hidden;
    min-height: 0;
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
    padding: 0.9rem 1.1rem;
}

/* ── Barre d'actions sticky en bas ── */
.toolbar {
    border-top: 1px solid var(--border);
    background: rgba(13,13,15,0.92);
    backdrop-filter: blur(12px);
    padding: 0.55rem 1.1rem;
    display: flex;
    align-items: center;
    gap: 0.45rem;
    flex-wrap: wrap;
    flex-shrink: 0;
}
.toolbar-sep {
    width: 1px;
    height: 20px;
    background: var(--border);
    margin: 0 0.2rem;
    flex-shrink: 0;
}

/* ── Panneau générique ── */
.panel {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
}
.panel.panel--kg {
    border-color: var(--kg-border);
}
.panel__head {
    padding: 0.6rem 1rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(10,10,14,0.4);
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--accent);
}
.panel--kg .panel__head {
    color: var(--kg-light);
    border-bottom-color: var(--kg-border);
}
.panel__head svg { width: 13px; height: 13px; }

/* ── Boutons génériques ── */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.38rem 0.8rem;
    border-radius: var(--radius-sm);
    font-size: 0.75rem;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: all var(--transition);
    border: 1px solid var(--border);
    background: var(--surface);
    color: var(--text-muted);
    white-space: nowrap;
}
.btn:hover { color: var(--text); border-color: var(--accent); background: var(--surface-h); }
.btn--success { border-color: rgba(39,201,63,0.4); color: var(--success); }
.btn--success:hover { background: rgba(39,201,63,0.1); border-color: var(--success); }
.btn--primary { background: var(--gradient); border-color: transparent; color: #fff; }
.btn--primary:hover { opacity: 0.88; border-color: transparent; }
.btn--kg {
    border-color: rgba(124,58,237,0.5);
    color: #c4b5fd;
    background: rgba(60,20,120,0.3);
}
.btn--kg:hover { background: rgba(90,40,160,0.55); border-color: var(--kg-light); color: #fff; }
.btn--kg.active {
    background: var(--kg);
    border-color: var(--kg-light);
    color: #fff;
    box-shadow: 0 0 14px rgba(124,58,237,0.45);
}
.btn--switch {
    border-color: rgba(124,58,237,0.5);
    color: #c4b5fd;
    background: rgba(60,20,120,0.3);
    font-weight: 700;
    padding: 0.38rem 1rem;
}
.btn--switch:hover { background: rgba(90,40,160,0.55); border-color: var(--kg-light); color: #fff; }
.btn--switch.active {
    background: var(--kg);
    border-color: var(--kg-light);
    color: #fff;
    box-shadow: 0 0 14px rgba(124,58,237,0.45);
}

/* ══════════════════════════
   MODE MERMAID
══════════════════════════ */
.mermaid-only { display: flex; flex-direction: column; gap: 0.85rem; }
.kg-mode .mermaid-only { display: none !important; }

/* Templates bar */
.templates-bar {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
    padding: 0.75rem 1rem;
}
.template-btn {
    padding: 0.35rem 0.75rem;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text-muted);
    font-size: 0.75rem;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: all var(--transition);
}
.template-btn:hover { color: var(--text); border-color: var(--accent); background: var(--surface-h); }

/* Console éditeur Mermaid */
.console-wrap {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.85rem;
    min-height: 0;
}
.console-frame {
    background: #0c0c10;
    border: 1px solid var(--accent);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0,0,0,0.4);
    display: flex;
    flex-direction: column;
}
.console-header {
    background: #111116;
    padding: 0.55rem 0.85rem;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    display: flex;
    align-items: center;
    gap: 0.6rem;
    flex-shrink: 0;
}
.dots { display: flex; gap: 6px; }
.dot { width: 10px; height: 10px; border-radius: 50%; }
.d-r { background: #ff5f56; } .d-y { background: #ffbd2e; } .d-g { background: #27c93f; }
.console-title {
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--accent);
    flex: 1;
}
.mermaid-editor {
    flex: 1;
    width: 100%;
    min-height: 340px;
    padding: 1rem;
    background: transparent;
    border: none;
    color: #e8e8f0;
    font-family: 'Fira Code', 'Cascadia Code', monospace;
    font-size: 0.82rem;
    line-height: 1.65;
    outline: none;
    resize: none;
}
.mermaid-editor::placeholder { color: var(--text-dim); }

/* Preview Mermaid */
.preview-panel {
    display: flex;
    flex-direction: column;
}
.preview-box {
    flex: 1;
    padding: 1.5rem;
    background: #080810;
    border-radius: var(--radius);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: auto;
    min-height: 340px;
}
#mermaid-preview { max-width: 100%; }

/* ══════════════════════════
   MODE KG
══════════════════════════ */
#kg-section { display: none; }
.kg-mode #kg-section { display: flex; flex-direction: column; gap: 0.85rem; }

/* Accordéon syntaxe */
.kg-syntax-ref {
    border: 1px solid var(--kg-border);
    border-radius: var(--radius);
    overflow: hidden;
    background: rgba(10,5,20,0.6);
}
.kg-syntax-ref summary {
    padding: 0.65rem 1rem;
    cursor: pointer;
    font-size: 0.65rem;
    font-weight: 700;
    color: var(--text-muted);
    letter-spacing: 0.08em;
    text-transform: uppercase;
    list-style: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    user-select: none;
    transition: color var(--transition);
}
.kg-syntax-ref summary:hover { color: #c4b5fd; }
.kg-syntax-ref[open] summary { color: #c4b5fd; border-bottom: 1px solid var(--kg-border); }
.kg-ref-body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    padding: 1rem;
}
@media (max-width: 760px) { .kg-ref-body { grid-template-columns: 1fr; } }
.kg-ref-col h4 {
    font-size: 0.6rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--kg);
    margin: 0 0 0.5rem;
}
.kg-ref-col pre {
    background: rgba(10,5,16,0.8);
    border: 1px solid var(--kg-border);
    border-radius: var(--radius-sm);
    padding: 0.75rem 0.9rem;
    font-family: 'Fira Code', monospace;
    font-size: 0.72rem;
    color: #c4b5fd;
    margin: 0;
    line-height: 1.65;
    white-space: pre-wrap;
}
.kg-cat-list { margin: 0; padding: 0; list-style: none; }
.kg-cat-list li {
    font-size: 0.72rem;
    font-family: 'Fira Code', monospace;
    color: #bbb;
    padding: 0.18rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.kg-dot-sm { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }

/* Onglets KG (texte / visuel) */
.kg-tabs {
    display: flex;
    border-bottom: 1px solid var(--kg-border);
    padding: 0 1rem;
}
.kg-tab {
    padding: 0.5rem 1rem;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.78rem;
    color: var(--text-muted);
    border: none;
    border-bottom: 2px solid transparent;
    background: transparent;
    font-family: inherit;
    transition: all var(--transition);
    margin-bottom: -1px;
}
.kg-tab:hover { color: #c4b5fd; }
.kg-tab.active { color: #c4b5fd; border-bottom-color: var(--kg-light); }

.kg-tab-content { display: none; }
.kg-tab-content.active { display: block; }

/* Éditeur texte KG */
.kg-text-frame {
    background: #070410;
    border-top: none;
}
.kg-text-toolbar {
    background: #0d0818;
    padding: 0.55rem 0.9rem;
    border-bottom: 1px solid var(--kg-border);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}
.kg-syntax-switch {
    display: flex;
    border: 1px solid var(--kg-border);
    border-radius: var(--radius-sm);
    overflow: hidden;
}
.kg-syntax-btn {
    padding: 0.3rem 0.75rem;
    background: transparent;
    border: none;
    cursor: pointer;
    font-size: 0.72rem;
    font-weight: 700;
    font-family: inherit;
    color: var(--text-muted);
    transition: all var(--transition);
}
.kg-syntax-btn.active { background: var(--kg); color: #fff; }
.kg-syntax-btn:hover:not(.active) { background: var(--kg-soft); color: #c4b5fd; }
.kg-hint { font-size: 0.65rem; color: var(--text-dim); margin-left: auto; font-family: 'Fira Code', monospace; }

.kg-text-editor {
    width: 100%;
    min-height: 200px;
    padding: 1rem;
    background: transparent;
    border: none;
    color: #d4c5ff;
    font-family: 'Fira Code', monospace;
    font-size: 0.8rem;
    line-height: 1.7;
    outline: none;
    resize: vertical;
}
.kg-text-editor::placeholder { color: rgba(160,160,160,0.28); }

.kg-text-actions {
    padding: 0.55rem 0.9rem;
    background: #0d0818;
    border-top: 1px solid var(--kg-border);
    display: flex;
    gap: 0.45rem;
    align-items: center;
    flex-wrap: wrap;
}
#kg-parse-status { font-size: 0.72rem; color: var(--text-muted); margin-left: 0.4rem; }

/* Éditeur visuel KG */
.kg-visual-panel {
    padding: 0.85rem 1rem;
    border-top: none;
    background: rgba(10,5,20,0.3);
}
.kg-toolbar-panel {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    align-items: center;
}
.kg-toolbar-panel input[type="text"] {
    flex: 1;
    min-width: 140px;
    padding: 0.42rem 0.75rem;
    background: var(--bg-2);
    border: 1px solid var(--kg-border);
    border-radius: var(--radius-sm);
    color: var(--text);
    font-size: 0.82rem;
    font-family: inherit;
    outline: none;
    transition: border-color var(--transition), box-shadow var(--transition);
}
.kg-toolbar-panel input[type="text"]:focus {
    border-color: var(--kg-light);
    box-shadow: 0 0 0 3px var(--kg-soft);
}
.kg-toolbar-panel select {
    padding: 0.42rem 0.7rem;
    background: var(--bg-2);
    border: 1px solid var(--kg-border);
    border-radius: var(--radius-sm);
    color: var(--text);
    font-size: 0.78rem;
    font-family: inherit;
    cursor: pointer;
    outline: none;
}
.kg-toolbar-panel select:focus { border-color: var(--kg-light); }
.kg-toolbar-panel select option { background: var(--bg-2); }

/* Canvas KG */
.kg-canvas-wrap {
    position: relative;
    border-radius: var(--radius);
    overflow: hidden;
    border: 1px solid var(--kg-border);
}
#kg-canvas {
    width: 100%;
    height: 520px;
    background: #070410;
}

/* Panneau info KG */
#kg-info {
    position: absolute;
    top: 12px; right: 12px;
    width: 175px;
    background: rgba(6,3,18,0.90);
    border: 1px solid rgba(46,16,96,0.8);
    border-radius: var(--radius);
    padding: 0.85rem;
    backdrop-filter: blur(12px);
    z-index: 10;
    pointer-events: none;
    box-shadow: 0 8px 24px rgba(0,0,0,0.7);
    font-size: 0.72rem;
}
.kp-lbl {
    font-size: 0.55rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--kg);
    font-weight: 700;
    margin-bottom: 0.3rem;
}
#kg-info-name { font-size: 0.82rem; font-weight: 700; color: #fff; margin-bottom: 0.15rem; min-height: 14px; }
#kg-info-cat  { font-size: 0.7rem; color: #aaa; margin-bottom: 0.5rem; }
#kg-info-conn { color: #ccc; line-height: 1.65; min-height: 14px; font-size: 0.7rem; }
#kg-legend { margin-top: 0.55rem; padding-top: 0.45rem; border-top: 1px solid rgba(26,8,64,0.8); }
.kg-leg-row { display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.2rem; color: #bbb; font-size: 0.68rem; }
.kg-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }

/* ── Toast ── */
.toast {
    position: fixed;
    bottom: 4.2rem; right: 1.2rem;
    background: #111116;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 0.55rem 0.9rem;
    font-size: 0.78rem;
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
.toast.info    { border-color: rgba(124,58,237,0.6); }

/* ── Scrollbar ── */

@media (max-width: 800px) {
    .console-wrap { grid-template-columns: 1fr; }
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
        <span class="hdr__icon">🧠</span>
        <span class="hdr__title" id="hdr-title">MINDMAP CREATOR</span>
        <span class="mode-badge" id="mode-badge">Mermaid</span>
        <span class="hdr__meta" id="hdr-meta">Diagrammes interactifs avec preview live et export</span>
        <div class="hdr__right">
            <button class="btn btn--switch" id="btn-toggle-kg" onclick="toggleKG()">🕸 Knowledge Graph</button>
        </div>
    </header>

    <!-- ══ CORPS ══════════════════════════════════════════════ -->
    <div class="body" id="app-body">

        <!-- ────────────────────────────
             MODE MERMAID
        ──────────────────────────── -->
        <div class="mermaid-only">

            <!-- Templates -->
            <div class="panel">
                <div class="panel__head">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                    Templates
                </div>
                <div class="templates-bar">
                    <button class="template-btn" data-template="flowchart">📊 Flowchart</button>
                    <button class="template-btn" data-template="sequence">🔄 Sequence</button>
                    <button class="template-btn" data-template="gantt">📅 Gantt</button>
                    <button class="template-btn" data-template="class">🏛️ Class Diagram</button>
                    <button class="template-btn" data-template="mindmap">🧠 Mindmap</button>
                    <button class="template-btn" data-template="timeline">⏱️ Timeline</button>
                    <button class="template-btn" data-template="journey">🗺️ User Journey</button>
                    <button class="template-btn" data-template="pie">🥧 Pie Chart</button>
                </div>
            </div>

            <!-- Éditeur + Preview côte à côte -->
            <div class="console-wrap">

                <!-- Éditeur code -->
                <div class="console-frame">
                    <div class="console-header">
                        <div class="dots">
                            <div class="dot d-r"></div>
                            <div class="dot d-y"></div>
                            <div class="dot d-g"></div>
                        </div>
                        <span class="console-title">Code Mermaid</span>
                        <button class="btn btn--success" onclick="copyCode()" style="padding:0.25rem 0.6rem;font-size:0.7rem">📋 Copier</button>
                    </div>
                    <textarea class="mermaid-editor" id="mermaid-code"
                              placeholder="Écrivez votre code Mermaid ici…"></textarea>
                </div>

                <!-- Preview live -->
                <div class="preview-panel">
                    <div class="preview-box" id="preview-box">
                        <div id="mermaid-preview"></div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ────────────────────────────
             MODE KNOWLEDGE GRAPH
        ──────────────────────────── -->
        <div id="kg-section">

            <!-- Accordéon syntaxe -->
            <details class="kg-syntax-ref">
                <summary>📖 Référence syntaxe KG</summary>
                <div class="kg-ref-body">
                    <div class="kg-ref-col">
                        <h4>Mode Compact (style Mermaid)</h4>
                        <pre>graph
  NomNœud[categorie]
  Autre[categorie]
  NomNœud --> Autre
  Autre --> Troisieme[tools]

# Les lignes commençant par #
# sont des commentaires.</pre>
                    </div>
                    <div class="kg-ref-col">
                        <h4>Mode Explicite (node / link)</h4>
                        <pre>node: Mon Nœud | identity
node: PHP | webdev
node: Raspberry Pi | iot
node: Projet Hive | project

link: Mon Nœud -> PHP
link: Mon Nœud -> Raspberry Pi
link: PHP -> Projet Hive</pre>
                    </div>
                    <div class="kg-ref-col">
                        <h4>Catégories disponibles</h4>
                        <ul class="kg-cat-list" id="ref-cat-list"></ul>
                    </div>
                    <div class="kg-ref-col">
                        <h4>Template générique</h4>
                        <pre>graph
  Centre[identity]
  Branche_A[project]
  Branche_B[webdev]
  Feuille_1[tools]
  Feuille_2[tools]

  Centre --> Branche_A
  Centre --> Branche_B
  Branche_A --> Feuille_1
  Branche_B --> Feuille_2</pre>
                    </div>
                </div>
            </details>

            <!-- Panneau éditeur KG -->
            <div class="panel panel--kg">
                <div class="panel__head">
                    🕸 Knowledge Graph — Éditeur
                </div>

                <!-- Onglets -->
                <div class="kg-tabs">
                    <button class="kg-tab active" onclick="switchKGTab('text', this)">📝 Éditeur texte</button>
                    <button class="kg-tab" onclick="switchKGTab('visual', this)">🖱️ Éditeur visuel</button>
                </div>

                <!-- Tab Texte -->
                <div class="kg-tab-content active" id="kg-tab-text">
                    <div class="kg-text-frame">
                        <div class="kg-text-toolbar">
                            <div class="kg-syntax-switch">
                                <button class="kg-syntax-btn active" id="syn-compact"  onclick="setSyntax('compact')">Compact</button>
                                <button class="kg-syntax-btn"        id="syn-explicit" onclick="setSyntax('explicit')">Explicite</button>
                            </div>
                            <span class="kg-hint" id="kg-syntax-hint">graph · NomNœud[cat] · A --> B</span>
                            <button class="btn btn--kg" style="margin-left:auto" onclick="loadKGTemplate()">📄 Template</button>
                        </div>
                        <textarea class="kg-text-editor" id="kg-text-input"
                                  placeholder="Écrivez votre graphe ici…"></textarea>
                        <div class="kg-text-actions">
                            <button class="btn btn--primary" onclick="kgParseAndRender()">🎨 Générer le graphe</button>
                            <button class="btn btn--success" onclick="kgCopyText()">📋 Copier</button>
                            <button class="btn" onclick="kgClearText()">🗑️ Clear</button>
                            <span id="kg-parse-status"></span>
                        </div>
                    </div>
                </div>

                <!-- Tab Visuel -->
                <div class="kg-tab-content" id="kg-tab-visual">
                    <div class="kg-visual-panel">
                        <div class="kg-toolbar-panel">
                            <input type="text" id="kg-label" placeholder="Nom du nœud…">
                            <select id="kg-cat">
                                <option value="default">⬜ Défaut</option>
                                <option value="identity">🔵 Identité</option>
                                <option value="project">🟣 Projet</option>
                                <option value="webdev">🔴 Web / Dev</option>
                                <option value="network">🟠 Réseau / Cyber</option>
                                <option value="iot">🩷 IoT / Hardware</option>
                                <option value="ai">🟡 IA / Prompt</option>
                                <option value="tools">🟢 Outils</option>
                            </select>
                            <button class="btn btn--kg" onclick="kgAddNode()">➕ Ajouter</button>
                            <button class="btn btn--kg" id="btn-link" onclick="kgStartLink()">🔗 Lier</button>
                            <button class="btn btn--kg" onclick="kgEdit()">✏️ Modifier</button>
                            <button class="btn btn--kg" onclick="kgDelete()">🗑️ Supprimer</button>
                            <button class="btn btn--kg" onclick="kgCenter()">🎯 Centrer</button>
                            <button class="btn btn--kg" id="btn-freeze" onclick="kgTogglePhysics()">⏸ Figer</button>
                            <button class="btn btn--kg" onclick="kgClear()">🔄 Tout effacer</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Canvas -->
            <div class="kg-canvas-wrap">
                <div id="kg-canvas"></div>
                <div id="kg-info">
                    <div class="kp-lbl">// Nœud sélectionné</div>
                    <div id="kg-info-name">—</div>
                    <div id="kg-info-cat"></div>
                    <div class="kp-lbl">Connexions</div>
                    <div id="kg-info-conn">Cliquez sur un nœud</div>
                    <div id="kg-legend"></div>
                </div>
            </div>

        </div><!-- /#kg-section -->

    </div><!-- /.body -->

    <!-- ══ TOOLBAR BAS ═════════════════════════════════════════ -->
    <div class="toolbar">
        <!-- Mermaid -->
        <button class="btn btn--primary mermaid-only" onclick="renderDiagram()">🎨 Render</button>
        <button class="btn btn--success mermaid-only" onclick="exportSVG()">💾 SVG</button>
        <button class="btn btn--success mermaid-only" onclick="exportPNG()">🖼️ PNG</button>
        <button class="btn mermaid-only" onclick="shareURL()">🔗 Partager</button>
        <button class="btn mermaid-only" onclick="clearEditor()">🗑️ Clear</button>
        <!-- Séparateur -->
        <div class="toolbar-sep" id="toolbar-sep" style="display:none"></div>
        <!-- KG -->
        <button class="btn btn--success" id="kg-export-png" style="display:none" onclick="kgExportPNG()">🖼️ PNG</button>
        <button class="btn btn--success" id="kg-export-svg" style="display:none" onclick="kgExportSVG()">💾 SVG</button>
        <button class="btn btn--success" id="kg-save-btn"   style="display:none" onclick="kgSave()">💾 Sauvegarder</button>
        <button class="btn"             id="kg-load-btn"   style="display:none" onclick="kgLoad()">📂 Charger</button>
    </div>

</div><!-- /.app -->

<div class="toast" id="toast"></div>

<!-- ── Librairies ── -->
<script src="https://cdn.jsdelivr.net/npm/mermaid@11/dist/mermaid.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>

<script>
(function () {
'use strict';

/* ══════════════════════════════════════════════════
   MERMAID INIT
══════════════════════════════════════════════════ */
mermaid.initialize({
  startOnLoad: false, theme: 'dark',
  themeVariables: {
    primaryColor:'#ff1654', primaryTextColor:'#fff', primaryBorderColor:'#ff1654',
    lineColor:'#ff1654', secondaryColor:'#5e006c', tertiaryColor:'#2b1842',
    background:'#0d0d0d', mainBkg:'#1a1a1a', secondBkg:'#0f0f12', border1:'#333', border2:'#555'
  },
  flowchart: { curve:'basis', useMaxWidth:true, htmlLabels:true }
});

const editor  = document.getElementById('mermaid-code');
const preview = document.getElementById('mermaid-preview');

/* ══════════════════════════════════════════════════
   TEMPLATES MERMAID
══════════════════════════════════════════════════ */
const templates = {
  flowchart:`graph TD
    A[Start] -->|Process| B(Decision)
    B -->|Yes| C[Action 1]
    B -->|No| D[Action 2]
    C --> E[End]
    D --> E`,
  sequence:`sequenceDiagram
    participant User
    participant API
    participant DB
    User->>API: Request Data
    API->>DB: Query
    DB-->>API: Results
    API-->>User: Response`,
  gantt:`gantt
    title Project Timeline
    dateFormat YYYY-MM-DD
    section Planning
    Research       :a1, 2025-01-01, 30d
    Design         :a2, after a1, 20d
    section Development
    Backend        :b1, after a2, 40d
    Frontend       :b2, after a2, 35d
    section Launch
    Testing        :c1, after b1, 15d
    Deployment     :c2, after c1, 5d`,
  class:`classDiagram
    class Animal {
        +String name
        +int age
        +makeSound()
    }
    class Dog {
        +String breed
        +bark()
    }
    class Cat {
        +String color
        +meow()
    }
    Animal <|-- Dog
    Animal <|-- Cat`,
  mindmap:`mindmap
  root((Black-Lab))
    Development
      PHP
      JavaScript
      Bash
    Hardware
      Raspberry Pi
      Arduino
      3D Printing
    Security
      Hardening
      Honeypot
      Audit
    AI
      Prompt Engineering
      Assistants`,
  timeline:`timeline
    title History of Computing
    1940s : ENIAC
          : First Electronic Computer
    1950s : UNIVAC
          : First Commercial Computer
    1970s : Microprocessor
          : Intel 4004
    1980s : Personal Computer
          : IBM PC
    1990s : World Wide Web
          : Tim Berners-Lee
    2000s : Smartphones
          : iPhone Launch`,
  journey:`journey
    title User Registration Flow
    section Sign Up
      Visit Homepage: 5: User
      Click Register: 4: User
      Fill Form: 3: User
    section Verification
      Submit Form: 3: User
      Receive Email: 5: User
      Click Link: 4: User
    section Completion
      Confirm Account: 5: User
      Setup Profile: 4: User`,
  pie:`pie title Project Time Distribution
    "Development" : 40
    "Testing" : 15
    "Documentation" : 10
    "Meetings" : 20
    "Bug Fixing" : 15`
};

/* ══════════════════════════════════════════════════
   TOAST
══════════════════════════════════════════════════ */
let toastTimer;
function showToast(message, type='success') {
  const t = document.getElementById('toast');
  t.textContent = message; t.className = `toast ${type}`;
  clearTimeout(toastTimer);
  setTimeout(() => t.classList.add('show'), 10);
  toastTimer = setTimeout(() => t.classList.remove('show'), 3000);
}

/* ══════════════════════════════════════════════════
   MERMAID FONCTIONS
══════════════════════════════════════════════════ */
document.querySelectorAll('.template-btn').forEach(btn => {
  btn.addEventListener('click', () => { editor.value = templates[btn.dataset.template]; renderDiagram(); });
});

let renderCounter = 0;
window.renderDiagram = async function() {
  const code = editor.value.trim();
  if (!code) { showToast('⚠️ Le code est vide','error'); return; }
  try {
    preview.innerHTML = '';
    const { svg } = await mermaid.render(`mermaid-${renderCounter++}`, code);
    preview.innerHTML = svg;
    showToast('✅ Diagramme rendu !','success');
  } catch(err) {
    showToast('❌ Erreur de syntaxe Mermaid','error');
    preview.innerHTML = `<pre style="color:#ff5f56;font-family:monospace;font-size:12px;white-space:pre-wrap">${err.message}</pre>`;
  }
};

let debounceTimer;
editor.addEventListener('input', () => { clearTimeout(debounceTimer); debounceTimer = setTimeout(renderDiagram, 800); });

window.copyCode    = () => navigator.clipboard.writeText(editor.value).then(() => showToast('📋 Code copié !','success'));
window.exportSVG   = function() {
  const svg = preview.querySelector('svg'); if (!svg) { showToast('⚠️ Rendez d\'abord le diagramme','error'); return; }
  const blob = new Blob([new XMLSerializer().serializeToString(svg)],{type:'image/svg+xml'});
  const a = Object.assign(document.createElement('a'),{href:URL.createObjectURL(blob),download:'diagram.svg'}); a.click(); URL.revokeObjectURL(a.href);
  showToast('💾 SVG exporté !','success');
};
window.exportPNG   = async function() {
  if (!preview.querySelector('svg')) { showToast('⚠️ Rendez d\'abord le diagramme','error'); return; }
  try {
    const c = await html2canvas(preview,{backgroundColor:'#080810',scale:2});
    c.toBlob(blob => { const a=Object.assign(document.createElement('a'),{href:URL.createObjectURL(blob),download:'diagram.png'}); a.click(); URL.revokeObjectURL(a.href); showToast('🖼️ PNG exporté !','success'); });
  } catch(e) { showToast('❌ Erreur export PNG','error'); }
};
window.shareURL    = function() {
  const code = editor.value.trim(); if (!code) { showToast('⚠️ Le code est vide','error'); return; }
  navigator.clipboard.writeText(`${location.origin}${location.pathname}?code=${encodeURIComponent(btoa(code))}`).then(() => showToast('🔗 Lien copié !','success'));
};
window.clearEditor = function() { if (confirm('Effacer tout le code ?')) { editor.value=''; preview.innerHTML=''; showToast('🗑️ Éditeur effacé','success'); } };

const params = new URLSearchParams(location.search);
if (params.has('code')) {
  try { editor.value = atob(decodeURIComponent(params.get('code'))); renderDiagram(); }
  catch(e) { showToast('❌ URL invalide','error'); }
} else { editor.value = templates.flowchart; renderDiagram(); }

/* ══════════════════════════════════════════════════
   KG — CONFIG CATÉGORIES
══════════════════════════════════════════════════ */
const KG_CAT = {
  default:  { label:'Défaut',         color:'#94a3b8', border:'#475569', size:20 },
  identity: { label:'Identité',       color:'#3b82f6', border:'#1d4ed8', size:36 },
  project:  { label:'Projet',         color:'#a855f7', border:'#7e22ce', size:30 },
  webdev:   { label:'Web / Dev',      color:'#ef4444', border:'#b91c1c', size:26 },
  network:  { label:'Réseau / Cyber', color:'#f97316', border:'#c2410c', size:26 },
  iot:      { label:'IoT / Hardware', color:'#ec4899', border:'#be185d', size:26 },
  ai:       { label:'IA / Prompt',    color:'#eab308', border:'#a16207', size:26 },
  tools:    { label:'Outils',         color:'#22c55e', border:'#15803d', size:26 },
};

const KG_TEMPLATE_COMPACT = `graph
  Centre[identity]
  Branche_A[project]
  Branche_B[webdev]
  Branche_C[network]
  Feuille_1[tools]
  Feuille_2[ai]
  Feuille_3[iot]

  Centre --> Branche_A
  Centre --> Branche_B
  Centre --> Branche_C
  Branche_A --> Feuille_1
  Branche_B --> Feuille_2
  Branche_C --> Feuille_3`;

const KG_TEMPLATE_EXPLICIT = `node: Centre | identity
node: Branche A | project
node: Branche B | webdev
node: Branche C | network
node: Feuille 1 | tools
node: Feuille 2 | ai
node: Feuille 3 | iot

link: Centre -> Branche A
link: Centre -> Branche B
link: Centre -> Branche C
link: Branche A -> Feuille 1
link: Branche B -> Feuille 2
link: Branche C -> Feuille 3`;

/* ══════════════════════════════════════════════════
   KG — STATE
══════════════════════════════════════════════════ */
let kgNodes=null, kgEdges=null, kgNetwork=null;
let kgSelected=null, kgPhysics=true;
let kgLinking=false, kgLinkSrc=null;
let kgIsOn=false;
let kgSyntax='compact';

/* ══════════════════════════════════════════════════
   KG — ONGLETS
══════════════════════════════════════════════════ */
window.switchKGTab = function(tab, btn) {
  document.querySelectorAll('.kg-tab').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.kg-tab-content').forEach(c => c.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById(`kg-tab-${tab}`).classList.add('active');
};

/* ══════════════════════════════════════════════════
   KG — SWITCH SYNTAXE
══════════════════════════════════════════════════ */
const HINTS = {
  compact:  'graph · NomNœud[cat] · A --> B',
  explicit: 'node: Nom | cat  ·  link: A -> B'
};
window.setSyntax = function(mode) {
  kgSyntax = mode;
  document.getElementById('syn-compact').classList.toggle('active',  mode==='compact');
  document.getElementById('syn-explicit').classList.toggle('active', mode==='explicit');
  document.getElementById('kg-syntax-hint').textContent = HINTS[mode];
};

/* ══════════════════════════════════════════════════
   KG — TEMPLATE LOADER
══════════════════════════════════════════════════ */
window.loadKGTemplate = function() {
  const ta = document.getElementById('kg-text-input');
  if (ta.value.trim() && !confirm('Remplacer le contenu actuel par le template ?')) return;
  ta.value = kgSyntax==='compact' ? KG_TEMPLATE_COMPACT : KG_TEMPLATE_EXPLICIT;
  document.getElementById('kg-parse-status').textContent = '';
};

/* ══════════════════════════════════════════════════
   KG — PARSERS
══════════════════════════════════════════════════ */
let _nodeIdCounter = 1;

function parseCompact(text) {
  const nodeMap={}, nodesOut=[], edgesOut=[];
  const lines = text.split('\n').map(l=>l.trim()).filter(l=>l&&!l.startsWith('#')&&l!=='graph');
  lines.forEach(line => {
    if (line.includes('-->')) return;
    const n = parseNodeToken(line);
    if (n.label && !nodeMap[n.label]) { const id=_nodeIdCounter++; nodeMap[n.label]=id; nodesOut.push(buildNode(id,n.label,n.cat)); }
  });
  lines.forEach(line => {
    const m = line.match(/^(.+?)\s*-->\s*(.+?)$/);
    if (!m) return;
    const a=parseNodeToken(m[1].trim()), b=parseNodeToken(m[2].trim());
    [a,b].forEach(n => { if (n.label&&!nodeMap[n.label]) { const id=_nodeIdCounter++; nodeMap[n.label]=id; nodesOut.push(buildNode(id,n.label,n.cat)); }});
    if (nodeMap[a.label]&&nodeMap[b.label]) edgesOut.push({from:nodeMap[a.label],to:nodeMap[b.label]});
  });
  return { nodes:nodesOut, edges:edgesOut };
}

function parseNodeToken(token) {
  let t=token.trim(), cat='default';
  const catMatch=t.match(/\[(\w+)\]\s*$/);
  if (catMatch) { cat=catMatch[1]; t=t.slice(0,t.lastIndexOf('['+catMatch[1]+']')).trim(); }
  if ((t.startsWith('"')&&t.endsWith('"'))||(t.startsWith("'")&&t.endsWith("'"))) t=t.slice(1,-1).trim();
  const label=t.replace(/_/g,' ').trim();
  return { label, cat };
}

function parseExplicit(text) {
  const nodeMap={}, nodesOut=[], edgesOut=[];
  text.split('\n').forEach(rawLine => {
    const line=rawLine.trim();
    if (!line||line.startsWith('#')) return;
    const nodeMatch=line.match(/^node:\s*(.+?)\s*\|\s*(\w+)\s*$/i);
    if (nodeMatch) {
      const label=nodeMatch[1].trim(), cat=nodeMatch[2].trim();
      if (!nodeMap[label]) { const id=_nodeIdCounter++; nodeMap[label]=id; nodesOut.push(buildNode(id,label,cat)); }
      return;
    }
    const linkMatch=line.match(/^link:\s*(.+?)\s*->\s*(.+?)\s*$/i);
    if (linkMatch) {
      const la=linkMatch[1].trim(), lb=linkMatch[2].trim();
      [la,lb].forEach(label => { if (!nodeMap[label]) { const id=_nodeIdCounter++; nodeMap[label]=id; nodesOut.push(buildNode(id,label,'default')); }});
      edgesOut.push({from:nodeMap[la],to:nodeMap[lb]});
    }
  });
  return { nodes:nodesOut, edges:edgesOut };
}

function buildNode(id, label, catKey) {
  const cat=KG_CAT[catKey]||KG_CAT.default;
  return {
    id, label, _cat:catKey, _baseSize:cat.size, size:cat.size,
    color:{background:cat.color,border:cat.border,highlight:{background:cat.color,border:'#fff'},hover:{background:cat.color,border:'#fff'}},
    font:{color:'#fff',size:13,bold:true},
    title:`[${cat.label}] ${label}`
  };
}

function kgApplyDegreeSize() {
  if (!kgNodes||!kgEdges) return;
  const degree={};
  kgEdges.get().forEach(e => { degree[e.from]=(degree[e.from]||0)+1; degree[e.to]=(degree[e.to]||0)+1; });
  const maxDeg=Math.max(1,...Object.values(degree));
  const updates=kgNodes.get().map(n => { const deg=degree[n.id]||0,base=n._baseSize||20; return {id:n.id,size:base+Math.round((deg/maxDeg)*24)}; });
  kgNodes.update(updates);
}

function kgApplyEdgeGradients() {
  if (!kgNodes||!kgEdges) return;
  const nodeById={};
  kgNodes.get().forEach(n => { nodeById[n.id]=n; });
  const updates=kgEdges.get().map(e => {
    const nA=nodeById[e.from],nB=nodeById[e.to]; if (!nA||!nB) return null;
    const catA=KG_CAT[nA._cat]||KG_CAT.default, catB=KG_CAT[nB._cat]||KG_CAT.default;
    if (nA._cat===nB._cat) return {id:e.id,color:{color:catA.color+'55',highlight:catA.color+'cc',hover:catA.color+'aa'}};
    return {id:e.id,color:{color:catA.color+'66',highlight:catB.color+'dd',hover:catB.color+'aa',inherit:false}};
  }).filter(Boolean);
  kgEdges.update(updates);
}

function kgHighlightNode(selectedId) {
  if (!kgNetwork) return;
  const neighbors=new Set(kgNetwork.getConnectedNodes(selectedId)); neighbors.add(selectedId);
  const connEdges=new Set(kgNetwork.getConnectedEdges(selectedId));
  kgNodes.get().forEach(n => {
    const isFocus=neighbors.has(n.id), cat=KG_CAT[n._cat]||KG_CAT.default;
    kgNodes.update({id:n.id,
      color:isFocus?{background:cat.color,border:cat.border,highlight:{background:cat.color,border:'#fff'}}:{background:'#2a2a2a',border:'#444',highlight:{background:'#2a2a2a',border:'#444'}},
      font:{color:isFocus?'#fff':'#555',size:13,bold:isFocus}, opacity:isFocus?1:0.2});
  });
  kgEdges.get().forEach(e => {
    const isConn=connEdges.has(e.id);
    kgEdges.update({id:e.id,
      color:isConn?{color:'rgba(200,150,255,0.8)',highlight:'rgba(255,200,255,1)'}:{color:'rgba(60,60,60,0.15)',highlight:'rgba(60,60,60,0.15)'},
      width:isConn?2.5:1});
  });
}

function kgResetHighlight() {
  if (!kgNodes||!kgEdges) return;
  kgNodes.get().forEach(n => {
    const cat=KG_CAT[n._cat]||KG_CAT.default;
    kgNodes.update({id:n.id,
      color:{background:cat.color,border:cat.border,highlight:{background:cat.color,border:'#fff'},hover:{background:cat.color,border:'#fff'}},
      font:{color:'#fff',size:13,bold:true}, opacity:1});
  });
  kgApplyEdgeGradients();
}

/* ══════════════════════════════════════════════════
   KG — PARSE & RENDER
══════════════════════════════════════════════════ */
window.kgParseAndRender = function() {
  const text=document.getElementById('kg-text-input').value.trim();
  if (!text) { showToast('⚠️ L\'éditeur est vide','error'); return; }
  let result;
  try { result=kgSyntax==='compact' ? parseCompact(text) : parseExplicit(text); }
  catch(e) { showToast('❌ Erreur de parsing','error'); document.getElementById('kg-parse-status').textContent='❌ '+e.message; return; }
  if (!result.nodes.length) { showToast('⚠️ Aucun nœud détecté — vérifiez la syntaxe','error'); return; }
  kgInit();
  kgNetwork.setOptions({physics:{enabled:true,stabilization:{enabled:true,iterations:1500,fit:false}}});
  kgNodes.clear(); kgEdges.clear();
  kgNodes.add(result.nodes); kgEdges.add(result.edges);
  setTimeout(function() { kgApplyDegreeSize(); kgApplyEdgeGradients(); }, 50);
  document.getElementById('kg-parse-status').textContent='⏳ '+result.nodes.length+' nœuds · '+result.edges.length+' connexions — calcul en cours…';
  showToast('⏳ Calcul du graphe…','info');
};

window.kgCopyText  = () => navigator.clipboard.writeText(document.getElementById('kg-text-input').value).then(() => showToast('📋 Copié !','success'));
window.kgClearText = function() { if (confirm('Vider l\'éditeur texte ?')) { document.getElementById('kg-text-input').value=''; document.getElementById('kg-parse-status').textContent=''; }};

/* ══════════════════════════════════════════════════
   KG — INIT VIS-NETWORK
══════════════════════════════════════════════════ */
function kgInit() {
  if (kgNetwork) return;
  kgNodes=new vis.DataSet([]); kgEdges=new vis.DataSet([]);
  kgNetwork=new vis.Network(
    document.getElementById('kg-canvas'),
    {nodes:kgNodes,edges:kgEdges},
    {
      layout:{hierarchical:{enabled:false}},
      nodes:{shape:'dot',font:{size:13,face:'Inter, Segoe UI',color:'#ffffff',bold:true},borderWidth:2,shadow:{enabled:true,color:'rgba(0,0,0,0.65)',size:14,x:2,y:3}},
      edges:{smooth:{type:'continuous',roundness:0.35},arrows:{to:{enabled:false}},color:{color:'rgba(140,120,200,0.3)',highlight:'rgba(200,150,255,0.85)',hover:'rgba(180,130,255,0.6)'},width:1.5,selectionWidth:3},
      physics:{enabled:true,stabilization:{enabled:true,iterations:1500,updateInterval:50,fit:false},forceAtlas2Based:{gravitationalConstant:-60,centralGravity:0.008,springConstant:0.04,springLength:160,damping:0.55},solver:'forceAtlas2Based',maxVelocity:80,minVelocity:0.5,timestep:0.4},
      interaction:{dragNodes:true,dragView:true,zoomView:true,selectable:true,hover:true,tooltipDelay:180}
    }
  );

  kgNetwork.on('stabilizationProgress', function(params) {
    const pct=Math.round(params.iterations/params.total*100);
    const s=document.getElementById('kg-parse-status'); if(s) s.textContent='⏳ Calcul… '+pct+'%';
  });
  kgNetwork.on('stabilizationIterationsDone', function() {
    const s=document.getElementById('kg-parse-status');
    kgNetwork.setOptions({physics:{stabilization:{enabled:false}}});
    kgApplyDegreeSize(); kgApplyEdgeGradients();
    kgNetwork.fit({animation:{duration:600,easingFunction:'easeInOutQuad'}});
    if (s) { const n=kgNodes.length,e=kgEdges.length; s.textContent='✅ '+n+' nœuds · '+e+' connexions'; }
    showToast('✅ Graphe stabilisé !','success');
  });

  kgNetwork.on('selectNode', params => {
    kgSelected=params.nodes[0];
    const n=kgNodes.get(kgSelected);
    document.getElementById('kg-label').value=n?.label||'';
    kgUpdatePanel(kgSelected);
    if (!kgLinking) kgHighlightNode(kgSelected);
    if (kgLinking) {
      if (kgLinkSrc===null) { kgLinkSrc=kgSelected; showToast('✅ Source OK — cliquez la cible','info'); }
      else if (kgLinkSrc!==kgSelected) {
        const exists=kgEdges.get().some(e=>(e.from===kgLinkSrc&&e.to===kgSelected)||(e.from===kgSelected&&e.to===kgLinkSrc));
        if (!exists) { kgEdges.add({from:kgLinkSrc,to:kgSelected}); showToast('🔗 Connexion créée !','info'); }
        else showToast('⚠️ Connexion déjà existante','error');
        kgLinking=false; kgLinkSrc=null;
        document.getElementById('btn-link').classList.remove('active');
        document.getElementById('btn-link').textContent='🔗 Lier';
        document.getElementById('kg-canvas').style.cursor='default';
      }
    }
  });
  kgNetwork.on('deselectNode', () => {
    kgSelected=null; document.getElementById('kg-label').value='';
    kgClearPanel(); kgResetHighlight();
  });
  buildLegend();
}

/* ══════════════════════════════════════════════════
   TOGGLE MODE KG ↔ MERMAID  (le fameux switch !)
══════════════════════════════════════════════════ */
window.toggleKG = function() {
  kgIsOn=!kgIsOn;
  const body    = document.getElementById('app-body');
  const btnKG   = document.getElementById('btn-toggle-kg');
  const title   = document.getElementById('hdr-title');
  const badge   = document.getElementById('mode-badge');
  const meta    = document.getElementById('hdr-meta');
  const sep     = document.getElementById('toolbar-sep');

  ['kg-export-png','kg-export-svg','kg-save-btn','kg-load-btn'].forEach(id => {
    document.getElementById(id).style.display=kgIsOn?'inline-flex':'none';
  });
  sep.style.display = kgIsOn ? 'block' : 'none';

  if (kgIsOn) {
    body.classList.add('kg-mode');
    btnKG.classList.add('active'); btnKG.textContent='📊 Mode Mermaid';
    title.textContent='🕸 Knowledge Graph'; title.classList.add('kg-active');
    badge.textContent='KG Interactif'; badge.classList.add('kg-active');
    meta.textContent='Écrivez votre graphe en texte ou ajoutez les nœuds visuellement';
    kgInit();
  } else {
    body.classList.remove('kg-mode');
    btnKG.classList.remove('active'); btnKG.textContent='🕸 Knowledge Graph';
    title.textContent='🧠 Mindmap Creator'; title.classList.remove('kg-active');
    badge.textContent='Mermaid'; badge.classList.remove('kg-active');
    meta.textContent='Diagrammes interactifs avec preview live et export';
    kgLinking=false; kgLinkSrc=null;
  }
};

/* ══════════════════════════════════════════════════
   KG — ÉDITEUR VISUEL
══════════════════════════════════════════════════ */
window.kgAddNode = function() {
  const label=document.getElementById('kg-label').value.trim();
  if (!label) { showToast('⚠️ Saisissez un nom','error'); return; }
  const catKey=document.getElementById('kg-cat').value||'default';
  kgNodes.add(buildNode(_nodeIdCounter++,label,catKey));
  document.getElementById('kg-label').value='';
  showToast(`Nœud "${label}" ajouté`,'info');
};

window.kgStartLink = function() {
  kgLinking=true; kgLinkSrc=null;
  const btn=document.getElementById('btn-link'); btn.classList.add('active'); btn.textContent='🎯 Cliquez source…';
  document.getElementById('kg-canvas').style.cursor='crosshair';
  showToast('Cliquez sur le nœud SOURCE','info');
};

window.kgEdit = function() {
  if (!kgSelected) { showToast('⚠️ Sélectionnez un nœud','error'); return; }
  const label=document.getElementById('kg-label').value.trim();
  if (!label) { showToast('⚠️ Saisissez un nom','error'); return; }
  kgNodes.update({id:kgSelected,label}); kgUpdatePanel(kgSelected);
  showToast('✏️ Nœud modifié','info');
};

window.kgDelete = function() {
  if (!kgSelected) { showToast('⚠️ Sélectionnez un nœud','error'); return; }
  kgEdges.get().forEach(e => { if(e.from===kgSelected||e.to===kgSelected) kgEdges.remove(e.id); });
  kgNodes.remove(kgSelected); kgSelected=null;
  document.getElementById('kg-label').value=''; kgClearPanel();
  showToast('🗑️ Nœud supprimé','info');
};

window.kgCenter = () => kgNetwork?.fit({animation:{duration:700,easingFunction:'easeInOutQuad'}});

window.kgTogglePhysics = function() {
  kgPhysics=!kgPhysics; kgNetwork?.setOptions({physics:{enabled:kgPhysics}});
  document.getElementById('btn-freeze').textContent=kgPhysics?'⏸ Figer':'▶ Animer';
};

window.kgClear = function() {
  if (!confirm('Effacer tout le graphe ?')) return;
  kgNodes?.clear(); kgEdges?.clear(); kgSelected=null;
  document.getElementById('kg-label').value=''; kgClearPanel();
  showToast('🔄 Graphe effacé','info');
};

/* ══════════════════════════════════════════════════
   KG — EXPORT PNG (canvas natif vis-network)
══════════════════════════════════════════════════ */
window.kgExportPNG = function() {
  if (!kgNetwork) { showToast('⚠️ Graphe vide','error'); return; }
  const srcCanvas=kgNetwork.canvas.frame.canvas;
  const W=srcCanvas.width, H=srcCanvas.height;
  const exportCanvas=document.createElement('canvas');
  exportCanvas.width=W*2; exportCanvas.height=H*2;
  const ctx=exportCanvas.getContext('2d');
  ctx.scale(2,2); ctx.fillStyle='#070410'; ctx.fillRect(0,0,W,H); ctx.drawImage(srcCanvas,0,0);
  exportCanvas.toBlob(blob => {
    if (!blob) { showToast('❌ Erreur export PNG','error'); return; }
    const a=Object.assign(document.createElement('a'),{href:URL.createObjectURL(blob),download:`knowledge-graph-${new Date().toISOString().split('T')[0]}.png`});
    a.click(); URL.revokeObjectURL(a.href); showToast('🖼️ PNG exporté !','success');
  },'image/png');
};

/* ══════════════════════════════════════════════════
   KG — EXPORT SVG (reconstruction vectorielle)
══════════════════════════════════════════════════ */
window.kgExportSVG = function() {
  if (!kgNetwork||!kgNodes.length) { showToast('⚠️ Graphe vide','error'); return; }
  const positions=kgNetwork.getPositions(), allNodes=kgNodes.get(), allEdges=kgEdges.get();
  const xs=allNodes.map(n=>positions[n.id]?.x||0), ys=allNodes.map(n=>positions[n.id]?.y||0);
  const sizes=allNodes.map(n=>n.size||20), maxSize=Math.max(...sizes);
  const minX=Math.min(...xs)-maxSize-60, minY=Math.min(...ys)-maxSize-60;
  const maxX=Math.max(...xs)+maxSize+60, maxY=Math.max(...ys)+maxSize+60;
  const W=maxX-minX, H=maxY-minY;
  const esc=s=>s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  let parts=[];
  parts.push(`<defs><filter id="shadow" x="-30%" y="-30%" width="160%" height="160%"><feDropShadow dx="2" dy="3" stdDeviation="5" flood-color="rgba(0,0,0,0.65)"/></filter></defs>`);
  parts.push(`<rect width="${W}" height="${H}" fill="#070410" rx="16"/>`);
  allEdges.forEach(edge => {
    const pF=positions[edge.from],pT=positions[edge.to]; if(!pF||!pT) return;
    const x1=pF.x-minX,y1=pF.y-minY,x2=pT.x-minX,y2=pT.y-minY;
    const mx=(x1+x2)/2,my=(y1+y2)/2,dx=x2-x1,dy=y2-y1;
    parts.push(`<path d="M${x1},${y1} Q${mx-dy*0.15},${my+dx*0.15} ${x2},${y2}" fill="none" stroke="rgba(140,120,200,0.35)" stroke-width="1.5" stroke-linecap="round"/>`);
  });
  allNodes.forEach(node => {
    const pos=positions[node.id]; if(!pos) return;
    const cx=pos.x-minX,cy=pos.y-minY,r=node.size||20;
    const col=node.color?.background||'#94a3b8',bord=node.color?.border||'#475569';
    parts.push(`<circle cx="${cx}" cy="${cy}" r="${r}" fill="${col}" stroke="${bord}" stroke-width="2" filter="url(#shadow)"/>`);
    const fs=Math.max(10,Math.min(13,r*0.55));
    parts.push(`<text x="${cx}" y="${cy+r+fs+3}" text-anchor="middle" font-family="Inter, Segoe UI, Arial, sans-serif" font-size="${fs}" font-weight="700" fill="#ffffff" paint-order="stroke" stroke="#070410" stroke-width="3" stroke-linejoin="round">${esc(node.label||'')}</text>`);
  });
  const xmlDecl='<'+'?xml version="1.0" encoding="UTF-8"?'+'>';
  const svg=xmlDecl+'\n<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '+W+' '+H+'" width="'+W+'" height="'+H+'">\n  '+parts.join('\n  ')+'\n</svg>';
  const blob=new Blob([svg],{type:'image/svg+xml;charset=utf-8'});
  const a=Object.assign(document.createElement('a'),{href:URL.createObjectURL(blob),download:`knowledge-graph-${new Date().toISOString().split('T')[0]}.svg`});
  a.click(); URL.revokeObjectURL(a.href); showToast('💾 SVG exporté !','success');
};

window.kgSave = function() {
  if (!kgNodes) return;
  const textContent=document.getElementById('kg-text-input').value;
  localStorage.setItem('kg_project',JSON.stringify({nodes:kgNodes.get(),edges:kgEdges.get(),text:textContent,syntax:kgSyntax}));
  showToast('💾 Projet sauvegardé !','success');
};

window.kgLoad = function() {
  const saved=localStorage.getItem('kg_project'); if (!saved) { showToast('⚠️ Aucun projet sauvegardé','error'); return; }
  const {nodes:n,edges:e,text,syntax}=JSON.parse(saved);
  kgInit(); kgNodes.clear(); kgEdges.clear(); kgNodes.add(n); kgEdges.add(e);
  if (text) document.getElementById('kg-text-input').value=text;
  if (syntax) { kgSyntax=syntax; setSyntax(syntax); }
  kgNetwork.fit(); showToast('📂 Projet chargé !','success');
};

/* ══════════════════════════════════════════════════
   KG — PANNEAU INFO
══════════════════════════════════════════════════ */
function kgUpdatePanel(id) {
  const n=kgNodes.get(id); if(!n) return;
  document.getElementById('kg-info-name').textContent=n.label;
  document.getElementById('kg-info-cat').textContent=KG_CAT[n._cat||'default']?.label||'';
  const connected=kgNetwork.getConnectedNodes(id).map(cid=>kgNodes.get(cid)?.label).filter(Boolean);
  document.getElementById('kg-info-conn').innerHTML=connected.length?connected.map(l=>`• ${l}`).join('<br>'):'Aucune connexion';
}
function kgClearPanel() {
  document.getElementById('kg-info-name').textContent='—';
  document.getElementById('kg-info-cat').textContent='';
  document.getElementById('kg-info-conn').textContent='Cliquez sur un nœud';
}

function buildLegend() {
  const el=document.getElementById('kg-legend');
  el.innerHTML='<div class="kp-lbl" style="margin-top:6px;padding-top:6px;border-top:1px solid rgba(26,8,64,0.8)">Légende</div>';
  Object.entries(KG_CAT).forEach(([,cat]) => {
    el.innerHTML+=`<div class="kg-leg-row"><div class="kg-dot" style="background:${cat.color}"></div>${cat.label}</div>`;
  });
  const refList=document.getElementById('ref-cat-list');
  if (refList) {
    Object.entries(KG_CAT).forEach(([key,cat]) => {
      refList.innerHTML+=`<li><div class="kg-dot-sm" style="background:${cat.color}"></div><code>${key}</code> — ${cat.label}</li>`;
    });
  }
}

buildLegend();

})();
</script>
</body>
</html>