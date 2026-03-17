<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Badge Generator — Black-Lab Toolbox</title>
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



/* ── Layout app ── */
.app {
    display: grid;
    grid-template-rows: auto 1fr;
    height: 100vh;
    overflow: hidden;
    position: relative;
    z-index: 1;
}

/* ── Header ── */
.hdr {
    padding: 0.75rem 1.4rem;
    border-bottom: 1px solid var(--border);
    background: rgba(13,13,15,0.9);
    backdrop-filter: blur(12px);
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-shrink: 0;
}
.hdr__title {
    font-size: 1.05rem;
    font-weight: 700;
    background: var(--gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    white-space: nowrap;
}
.hdr__meta { font-size: 0.75rem; color: var(--text-muted); }
.hdr__right { margin-left: auto; }

/* ── Corps scrollable ── */
.body {
    overflow-y: auto;
    padding: 1.2rem 1.4rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    min-height: 0;
}

/* ── Ligne 1 : 2 colonnes ── */
.row-top {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

/* ── Panneau générique ── */
.panel {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.1rem 1.2rem;
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
}

.panel__title {
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--accent);
    padding-bottom: 0.45rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.panel__title svg { width: 13px; height: 13px; }

/* ── Groupes de formulaire ── */
.input-group {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}
.input-group label {
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
}
.form-control {
    width: 100%;
    padding: 0.48rem 0.85rem;
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text);
    font-size: 0.85rem;
    font-family: inherit;
    outline: none;
    transition: border-color var(--transition), box-shadow var(--transition);
}
.form-control:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px var(--accent-soft);
}
.form-control::placeholder { color: var(--text-dim); }

/* ── Color picker ── */
.color-row {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.25rem 0.6rem;
    transition: border-color var(--transition);
}
.color-row:focus-within { border-color: var(--accent); }

input[type="color"] {
    -webkit-appearance: none;
    border: none;
    width: 36px;
    height: 28px;
    background: none;
    cursor: pointer;
    border-radius: 6px;
    flex-shrink: 0;
    padding: 0;
}
input[type="color"]::-webkit-color-swatch-wrapper { padding: 0; }
input[type="color"]::-webkit-color-swatch { border: none; border-radius: 5px; }

.color-hex {
    font-family: 'Fira Code', 'Cascadia Code', monospace;
    font-size: 0.8rem;
    color: var(--text-muted);
    min-width: 60px;
    user-select: none;
}

/* ── Sélecteurs style / taille ── */
.style-selector, .size-selector {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
}

.style-btn, .size-btn {
    padding: 0.35rem 0.75rem;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text-muted);
    font-size: 0.78rem;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: all var(--transition);
}
.style-btn:hover, .size-btn:hover {
    color: var(--text);
    border-color: var(--accent);
    background: var(--surface-h);
}
.style-btn.active, .size-btn.active {
    background: var(--accent);
    border-color: var(--accent);
    color: #fff;
}

/* ── Ligne 2 : résultat pleine largeur ── */
.row-result {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

/* Preview zone */
.preview-box {
    background: repeating-linear-gradient(
        45deg,
        rgba(255,255,255,0.02) 0px,
        rgba(255,255,255,0.02) 10px,
        transparent 10px,
        transparent 20px
    );
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
}
.preview-box::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at center, rgba(255,22,84,0.04), transparent 70%);
    pointer-events: none;
}
#badge-img-preview {
    transition: transform 0.3s ease;
    position: relative;
    z-index: 1;
    max-width: 100%;
}
.preview-placeholder {
    color: var(--text-dim);
    font-size: 0.8rem;
    text-align: center;
}

/* Codes output */
.codes-col {
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
}

.code-block { display: flex; flex-direction: column; gap: 0.3rem; }
.code-block label {
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
}
.code-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}
.code-input {
    width: 100%;
    padding: 0.45rem 2.5rem 0.45rem 0.8rem;
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--accent);
    font-family: 'Fira Code', 'Cascadia Code', monospace;
    font-size: 0.75rem;
    outline: none;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    transition: border-color var(--transition);
    cursor: text;
}
.code-input:focus { border-color: var(--accent); }
.code-input.copied { border-color: var(--success); color: var(--success); }

.copy-btn {
    position: absolute;
    right: 4px;
    width: 28px;
    height: calc(100% - 8px);
    background: var(--surface-h);
    border: 1px solid var(--border);
    color: var(--text-muted);
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    transition: all var(--transition);
    font-family: inherit;
}
.copy-btn:hover { background: var(--accent-soft); color: var(--accent); border-color: var(--accent); }
.copy-btn.done { background: rgba(39,201,63,0.12); color: var(--success); border-color: var(--success); }

/* Boutons download */
.dl-row {
    display: flex;
    gap: 0.5rem;
    margin-top: auto;
    padding-top: 0.4rem;
}
.btn-dl {
    flex: 1;
    padding: 0.5rem 0.6rem;
    border-radius: var(--radius-sm);
    font-size: 0.78rem;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: all var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    border: 1px solid var(--border);
}
.btn-dl svg { width: 13px; height: 13px; flex-shrink: 0; }
.btn-dl--svg {
    background: var(--gradient);
    border-color: transparent;
    color: #fff;
}
.btn-dl--svg:hover { opacity: 0.88; }
.btn-dl--png {
    background: var(--surface-h);
    color: var(--text-muted);
}
.btn-dl--png:hover { color: var(--text); border-color: var(--accent); }

/* ── Toast ── */
.toast {
    position: fixed;
    bottom: 1.2rem;
    right: 1.2rem;
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

/* ── Modale ── */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.75);
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
    max-width: 560px;
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
.modal-close {
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
.modal-close:hover { color: var(--accent); border-color: var(--accent); }
.modal-body { padding: 1.2rem 1.3rem; display: flex; flex-direction: column; gap: 1.1rem; }
.modal-section-title {
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--accent);
    border-bottom: 1px solid rgba(255,22,84,0.2);
    padding-bottom: 0.35rem;
    margin-bottom: 0.7rem;
}
.modal-row {
    display: flex;
    align-items: flex-start;
    gap: 0.8rem;
    margin-bottom: 0.65rem;
    font-size: 0.82rem;
}
.modal-row:last-child { margin-bottom: 0; }
.modal-row-icon {
    width: 28px; height: 28px;
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    background: var(--surface);
    border: 1px solid var(--border);
    font-size: 0.9rem;
}
.modal-row-text strong { display: block; color: var(--text); margin-bottom: 0.2rem; font-size: 0.82rem; }
.modal-row-text span { color: var(--text-muted); font-size: 0.78rem; line-height: 1.5; }
.modal-row-text a { color: var(--accent); }
.modal-row-text a:hover { text-decoration: underline; }

.logos-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
}
.logo-chip {
    font-family: 'Fira Code', 'Cascadia Code', monospace;
    font-size: 0.72rem;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 0.18rem 0.6rem;
    color: var(--text-muted);
    cursor: pointer;
    transition: all var(--transition);
    user-select: none;
}
.logo-chip:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-soft); }

.modal-tip {
    background: var(--accent-soft);
    border: 1px solid rgba(255,22,84,0.25);
    border-left: 3px solid var(--accent);
    border-radius: var(--radius-sm);
    padding: 0.7rem 0.9rem;
    font-size: 0.78rem;
    color: var(--text-muted);
    line-height: 1.55;
}
.modal-tip strong { color: var(--accent); }

/* ── Bouton aide (header) ── */
.btn-help {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.3rem 0.75rem;
    border: 1px solid rgba(255,22,84,0.3);
    background: var(--accent-soft);
    color: var(--accent);
    border-radius: var(--radius-sm);
    font-size: 0.78rem;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: all var(--transition);
}
.btn-help:hover { background: var(--accent); color: #fff; border-color: var(--accent); }
.btn-help svg { width: 13px; height: 13px; }



/* ── Responsive ── */
@media (max-width: 760px) {
    .row-top { grid-template-columns: 1fr; }
    .row-result { grid-template-columns: 1fr; }
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
        <span class="hdr__icon">🏷️</span>
        <span class="hdr__title">BADGE GENERATOR</span>
        <span class="hdr__meta">Créez des badges shields.io personnalisés</span>
        <div class="hdr__right">
            <button class="btn-help" onclick="openModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Aide
            </button>
        </div>
    </header>

    <!-- ══ CORPS ══════════════════════════════════════════════ -->
    <div class="body">

        <!-- Ligne 1 : Contenu | Apparence -->
        <div class="row-top">

            <!-- Panneau Contenu -->
            <div class="panel">
                <div class="panel__title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Contenu
                </div>

                <div class="input-group">
                    <label>Texte Gauche (Label)</label>
                    <input type="text" id="label-input" class="form-control" value="Build" autocomplete="off">
                </div>
                <div class="input-group">
                    <label>Texte Droite (Message)</label>
                    <input type="text" id="message-input" class="form-control" value="Passing" autocomplete="off">
                </div>
                <div class="input-group">
                    <label>Logo <span style="color:var(--text-dim);text-transform:none;font-weight:400">(simpleicons.org — optionnel)</span></label>
                    <input type="text" id="logo-input" class="form-control"
                           placeholder="ex: github, docker, linux…" autocomplete="off" spellcheck="false">
                </div>
                <div class="input-group">
                    <label>Couleur du Logo</label>
                    <div class="color-row">
                        <input type="color" id="logo-color-input" value="#ffffff">
                        <span class="color-hex" id="hex-logo">#ffffff</span>
                    </div>
                </div>
            </div>

            <!-- Panneau Apparence -->
            <div class="panel">
                <div class="panel__title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/></svg>
                    Apparence
                </div>

                <div class="input-group">
                    <label>Style Visuel</label>
                    <div class="style-selector">
                        <button class="style-btn active" data-style="flat">Flat</button>
                        <button class="style-btn" data-style="flat-square">Square</button>
                        <button class="style-btn" data-style="plastic">Plastic</button>
                        <button class="style-btn" data-style="for-the-badge">Badge</button>
                        <button class="style-btn" data-style="social">Social</button>
                    </div>
                </div>

                <div class="input-group">
                    <label>Taille d'affichage</label>
                    <div class="size-selector">
                        <button class="size-btn active" data-size="1">S</button>
                        <button class="size-btn" data-size="1.5">M</button>
                        <button class="size-btn" data-size="2">L</button>
                    </div>
                </div>

                <div class="input-group">
                    <label>Couleur Gauche (Label)</label>
                    <div class="color-row">
                        <input type="color" id="label-color-input" value="#555555">
                        <span class="color-hex" id="hex-label">#555555</span>
                    </div>
                </div>

                <div class="input-group">
                    <label>Couleur Droite (Message)</label>
                    <div class="color-row">
                        <input type="color" id="color-input" value="#ff1654">
                        <span class="color-hex" id="hex-msg">#ff1654</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ligne 2 : Résultat pleine largeur -->
        <div class="panel">
            <div class="panel__title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                Résultat
            </div>

            <div class="row-result">

                <!-- Preview -->
                <div style="display:flex;flex-direction:column;gap:0.65rem;">
                    <div class="preview-box" id="preview-box">
                        <img id="badge-img-preview" src="" alt="Badge Preview">
                    </div>
                    <div class="dl-row">
                        <button class="btn-dl btn-dl--svg" onclick="downloadBadge('svg')">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Télécharger SVG
                        </button>
                        <button class="btn-dl btn-dl--png" onclick="downloadBadge('png')">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Télécharger PNG
                        </button>
                    </div>
                </div>

                <!-- Codes -->
                <div class="codes-col">
                    <div class="code-block">
                        <label>URL Image</label>
                        <div class="code-wrapper">
                            <input type="text" class="code-input" id="out-url" readonly>
                            <button class="copy-btn" onclick="copyToClip('out-url')" title="Copier">📋</button>
                        </div>
                    </div>
                    <div class="code-block">
                        <label>Markdown</label>
                        <div class="code-wrapper">
                            <input type="text" class="code-input" id="out-md" readonly>
                            <button class="copy-btn" onclick="copyToClip('out-md')" title="Copier">📋</button>
                        </div>
                    </div>
                    <div class="code-block">
                        <label>HTML</label>
                        <div class="code-wrapper">
                            <input type="text" class="code-input" id="out-html" readonly>
                            <button class="copy-btn" onclick="copyToClip('out-html')" title="Copier">📋</button>
                        </div>
                    </div>
                    <div class="code-block">
                        <label>Lien cliquable (Markdown)</label>
                        <div class="code-wrapper">
                            <input type="text" class="code-input" id="out-link-url" placeholder="URL de destination…"
                                   oninput="updateLinkUrl(this.value)" autocomplete="off">
                        </div>
                    </div>
                    <div class="code-block">
                        <label>Markdown avec lien</label>
                        <div class="code-wrapper">
                            <input type="text" class="code-input" id="out-md-link" readonly
                                   placeholder="Renseignez l'URL ci-dessus…">
                            <button class="copy-btn" onclick="copyToClip('out-md-link')" title="Copier">📋</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div><!-- /.body -->
</div><!-- /.app -->

<!-- ══ TOAST ═════════════════════════════════════════════════ -->
<div class="toast" id="toast"></div>

<!-- ══ MODALE AIDE ═══════════════════════════════════════════ -->
<div class="modal-overlay" id="modal-help" onclick="closeModalOutside(event)">
    <div class="modal-box" role="dialog" aria-modal="true">
        <div class="modal-head">
            <h2>🏷️ Aide &amp; Conseils</h2>
            <button class="modal-close" onclick="closeModal()">✕</button>
        </div>
        <div class="modal-body">

            <!-- Utilisation -->
            <div>
                <div class="modal-section-title">Comment utiliser</div>
                <div class="modal-row">
                    <div class="modal-row-icon">✏️</div>
                    <div class="modal-row-text">
                        <strong>Texte</strong>
                        <span>Renseignez le texte gauche (<em>Label</em>) et droite (<em>Message</em>). Le badge se met à jour en temps réel.</span>
                    </div>
                </div>
                <div class="modal-row">
                    <div class="modal-row-icon">🎨</div>
                    <div class="modal-row-text">
                        <strong>Couleurs</strong>
                        <span>Cliquez sur les carrés colorés pour ouvrir le sélecteur. La couleur gauche (label) est souvent laissée en gris <code style="color:var(--accent)">#555555</code>.</span>
                    </div>
                </div>
                <div class="modal-row">
                    <div class="modal-row-icon">🔠</div>
                    <div class="modal-row-text">
                        <strong>Styles</strong>
                        <span><em>Flat</em> (défaut, épuré), <em>Square</em> (sans arrondi), <em>Plastic</em> (brillant), <em>Badge</em> (texte majuscule large), <em>Social</em> (style réseau social).</span>
                    </div>
                </div>
                <div class="modal-row">
                    <div class="modal-row-icon">🔗</div>
                    <div class="modal-row-text">
                        <strong>Lien cliquable</strong>
                        <span>Renseignez une URL de destination dans le champ prévu — le Markdown avec lien est généré automatiquement.</span>
                    </div>
                </div>
            </div>

            <!-- Logos -->
            <div>
                <div class="modal-section-title">Logos (simpleicons.org)</div>
                <div class="modal-row">
                    <div class="modal-row-icon">🖼️</div>
                    <div class="modal-row-text">
                        <strong>Comment trouver un logo</strong>
                        <span>Rendez-vous sur <a href="https://simpleicons.org" target="_blank" rel="noopener">simpleicons.org</a>, cherchez votre icône et copiez son nom exact (en minuscules, avec espaces).<br>Cliquez sur un nom ci-dessous pour l'insérer directement :</span>
                    </div>
                </div>
                <div class="logos-grid" id="logos-grid">
                    <!-- injecté en JS -->
                </div>
            </div>

            <!-- Encodage -->
            <div>
                <div class="modal-section-title">Encodage automatique</div>
                <div class="modal-row">
                    <div class="modal-row-icon">⚙️</div>
                    <div class="modal-row-text">
                        <strong>Caractères spéciaux</strong>
                        <span>Les tirets <code style="color:var(--accent)">-</code> sont encodés en <code style="color:var(--accent)">--</code> et les underscores <code style="color:var(--accent)">_</code> en <code style="color:var(--accent)">__</code> selon la convention shields.io. Les espaces sont encodés en <code style="color:var(--accent)">%20</code>.</span>
                    </div>
                </div>
            </div>

            <!-- Download -->
            <div>
                <div class="modal-section-title">Téléchargement</div>
                <div class="modal-row">
                    <div class="modal-row-icon">📥</div>
                    <div class="modal-row-text">
                        <strong>SVG vs PNG</strong>
                        <span><em>SVG</em> — vectoriel, qualité parfaite à toute taille, recommandé pour le web et les README.<br><em>PNG</em> — rendu via canvas, adapté aux contextes qui n'acceptent pas le SVG. En cas d'erreur CORS, utilisez le SVG ou faites une capture d'écran.</span>
                    </div>
                </div>
            </div>

            <div class="modal-tip">
                <strong>💡 Astuce :</strong> Pour un badge de version, utilisez <code style="color:var(--accent)">v1.0.0</code> comme message et le nom de votre projet comme label. Pour un statut, combinez une couleur rouge/orange/verte avec les mots <em>failing</em>, <em>warning</em>, <em>passing</em>.
            </div>

        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    /* ══ État ══ */
    const state = {
        label:      'Build',
        message:    'Passing',
        style:      'flat',
        color:      '#ff1654',
        labelColor: '#555555',
        logo:       '',
        logoColor:  '#ffffff',
        scale:      1,
        linkUrl:    ''
    };

    /* ══ Refs DOM ══ */
    const els = {
        label:      document.getElementById('label-input'),
        message:    document.getElementById('message-input'),
        logo:       document.getElementById('logo-input'),
        logoColor:  document.getElementById('logo-color-input'),
        color:      document.getElementById('color-input'),
        labelColor: document.getElementById('label-color-input'),
        img:        document.getElementById('badge-img-preview'),
        outUrl:     document.getElementById('out-url'),
        outMd:      document.getElementById('out-md'),
        outHtml:    document.getElementById('out-html'),
        outMdLink:  document.getElementById('out-md-link'),
        hexLogo:    document.getElementById('hex-logo'),
        hexLabel:   document.getElementById('hex-label'),
        hexMsg:     document.getElementById('hex-msg'),
        styleBtns:  document.querySelectorAll('.style-btn'),
        sizeBtns:   document.querySelectorAll('.size-btn'),
    };

    /* ══ Helpers ══ */
    const clean      = str => encodeURIComponent(str.trim().replace(/-/g, '--').replace(/_/g, '__') || ' ');
    const cleanColor = hex => hex.replace('#', '');

    function buildUrl() {
        let url = `https://img.shields.io/badge/${clean(state.label)}-${clean(state.message)}-${cleanColor(state.color)}`;
        const params = [];
        if (state.style !== 'flat') params.push(`style=${state.style}`);
        if (state.labelColor) params.push(`labelColor=${cleanColor(state.labelColor)}`);
        if (state.logo) {
            params.push(`logo=${encodeURIComponent(state.logo)}`);
            params.push(`logoColor=${cleanColor(state.logoColor)}`);
        }
        if (params.length) url += '?' + params.join('&');
        return url;
    }

    /* ══ Mise à jour globale ══ */
    function update() {
        const base = buildUrl();
        const url  = state.scale === 1
            ? base
            : `${base}${base.includes('?') ? '&' : '?'}scale=${state.scale}`;

        els.img.src = url;
        els.img.style.transform = state.scale === 1 ? 'scale(1)' : `scale(${state.scale})`;

        els.outUrl.value  = url;
        els.outMd.value   = `![${state.label}](${url})`;
        els.outHtml.value = `<img src="${url}" alt="${state.label}">`;
        updateMdLink();
    }

    function updateMdLink() {
        if (state.linkUrl) {
            const base = buildUrl();
            const url  = state.scale === 1 ? base : `${base}${base.includes('?') ? '&' : '?'}scale=${state.scale}`;
            els.outMdLink.value = `[![${state.label}](${url})](${state.linkUrl})`;
        } else {
            els.outMdLink.value = '';
        }
    }

    window.updateLinkUrl = function (val) {
        state.linkUrl = val.trim();
        updateMdLink();
    };

    /* ══ Listeners texte ══ */
    ['label', 'message', 'logo'].forEach(k => {
        els[k].addEventListener('input', e => { state[k] = e.target.value; update(); });
    });

    /* ══ Listeners couleur ══ */
    const colorMap = [
        { el: els.color,      key: 'color',      hex: els.hexMsg   },
        { el: els.labelColor, key: 'labelColor',  hex: els.hexLabel },
        { el: els.logoColor,  key: 'logoColor',   hex: els.hexLogo  },
    ];
    colorMap.forEach(({ el, key, hex }) => {
        el.addEventListener('input', e => {
            state[key] = e.target.value;
            hex.textContent = e.target.value;
            update();
        });
    });

    /* ══ Styles ══ */
    els.styleBtns.forEach(b => b.addEventListener('click', () => {
        els.styleBtns.forEach(x => x.classList.remove('active'));
        b.classList.add('active');
        state.style = b.dataset.style;
        update();
    }));

    /* ══ Tailles ══ */
    els.sizeBtns.forEach(b => b.addEventListener('click', () => {
        els.sizeBtns.forEach(x => x.classList.remove('active'));
        b.classList.add('active');
        state.scale = parseFloat(b.dataset.size);
        update();
    }));

    /* ══ Copie ══ */
    let toastTimer;
    function toast(msg, type) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.className = 'toast show ' + (type || 'success');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => t.classList.remove('show'), 2600);
    }

    window.copyToClip = function (id) {
        const el = document.getElementById(id);
        if (!el || !el.value) return;
        navigator.clipboard.writeText(el.value).then(() => {
            const btn = el.nextElementSibling;
            if (btn) {
                btn.textContent = '✓';
                btn.classList.add('done');
                setTimeout(() => { btn.textContent = '📋'; btn.classList.remove('done'); }, 1500);
            }
            toast('📋 Copié !', 'success');
        });
    };

    /* ══ Téléchargement ══ */
    window.downloadBadge = function (fmt) {
        const base = buildUrl();
        const url  = state.scale === 1 ? base : `${base}${base.includes('?') ? '&' : '?'}scale=${state.scale}`;
        const name = `${(state.label.trim() || 'label')}-${(state.message.trim() || 'message')}`;

        if (fmt === 'svg') {
            const link = document.createElement('a');
            link.href = url;
            link.download = name + '.svg';
            link.click();
            toast('⬇ SVG en cours de téléchargement…', 'info');
        } else if (fmt === 'png') {
            toast('⏳ Génération PNG…', 'info');
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.src = url + (url.includes('?') ? '&' : '?') + '_t=' + Date.now();
            img.onload = function () {
                const canvas = document.createElement('canvas');
                const ctx    = canvas.getContext('2d');
                canvas.width  = img.width  * state.scale;
                canvas.height = img.height * state.scale;
                ctx.scale(state.scale, state.scale);
                ctx.drawImage(img, 0, 0);
                canvas.toBlob(blob => {
                    const dlUrl = URL.createObjectURL(blob);
                    const link  = document.createElement('a');
                    link.href   = dlUrl;
                    link.download = name + '.png';
                    link.click();
                    URL.revokeObjectURL(dlUrl);
                    toast('✅ PNG téléchargé !', 'success');
                }, 'image/png');
            };
            img.onerror = function () {
                toast('⚠️ CORS shields.io — utilisez le SVG ou une capture', 'error');
            };
        }
    };

    /* ══ Modale ══ */
    window.openModal  = () => document.getElementById('modal-help').classList.add('open');
    window.closeModal = () => document.getElementById('modal-help').classList.remove('open');
    window.closeModalOutside = e => { if (e.target === document.getElementById('modal-help')) closeModal(); };
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    /* ══ Logos chips — clic pour insérer ══ */
    const LOGOS = [
        'github','gitlab','docker','linux','debian','ubuntu','arch linux',
        'raspberry pi','nginx','apache','nodejs','php','python','javascript',
        'typescript','react','vue.js','wordpress','mysql','postgresql','redis',
        'mongodb','git','bash','vim','visual studio code','ansible','terraform',
        'kubernetes','prometheus','grafana','elasticsearch','jenkins','firefox',
        'chrome','android','apple','windows','adobe photoshop','figma','notion',
        'discord','slack','twitch','youtube','twitter','instagram','linkedin',
        'github sponsors','opensourcehardware','openstreetmap','openjsfoundation',
        'creativecommons','shields.io','netlify','vercel','cloudflare','aws',
        'google cloud','microsoft azure','digitalocean','heroku',
    ];
    const grid = document.getElementById('logos-grid');
    LOGOS.forEach(logo => {
        const chip = document.createElement('span');
        chip.className = 'logo-chip';
        chip.textContent = logo;
        chip.addEventListener('click', () => {
            els.logo.value = logo;
            state.logo = logo;
            update();
            closeModal();
            toast(`Logo "${logo}" appliqué`, 'success');
        });
        grid.appendChild(chip);
    });

    /* ══ Init ══ */
    update();
})();
</script>
</body>
</html>