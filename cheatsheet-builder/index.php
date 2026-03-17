<?php
/**
 * CheatSheet Builder — Le Lab'O Noir (Black-Lab Hub v4)
 * Outil de création d'aide-mémoire techniques
 */

function blabcsb_get_categories(): array {
    return [
        'linux'      => ['label' => 'Linux',      'color' => '#a855f7', 'icon' => 'terminal'],
        'git'        => ['label' => 'Git',         'color' => '#f97316', 'icon' => 'git'],
        'docker'     => ['label' => 'Docker',      'color' => '#3b82f6', 'icon' => 'docker'],
        'php'        => ['label' => 'PHP',         'color' => '#818cf8', 'icon' => 'php'],
        'javascript' => ['label' => 'JavaScript',  'color' => '#eab308', 'icon' => 'code'],
        'python'     => ['label' => 'Python',      'color' => '#22d3ee', 'icon' => 'python'],
        'sql'        => ['label' => 'SQL',         'color' => '#ef4444', 'icon' => 'database'],
        'network'    => ['label' => 'Network',     'color' => '#27c93f', 'icon' => 'network'],
        'security'   => ['label' => 'Security',    'color' => '#ff1654', 'icon' => 'shield'],
        'autre'      => ['label' => 'Autre',       'color' => '#94a3b8', 'icon' => 'star'],
    ];
}

function blabcsb_icon(string $name): string {
    $icons = [
        'terminal' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="4 17 10 11 4 5"/><line x1="12" y1="19" x2="20" y2="19"/></svg>',
        'git'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><path d="M6 21V9a9 9 0 009 9"/></svg>',
        'docker'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="10" width="4" height="4"/><rect x="8" y="10" width="4" height="4"/><rect x="13" y="10" width="4" height="4"/><rect x="8" y="5" width="4" height="4"/><path d="M21 11.5a5 5 0 01-5 4.5H4a2 2 0 01-2-2v-1"/></svg>',
        'php'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="12" rx="10" ry="7"/><path d="M7 12h2a2 2 0 000-4H7v8m6-8h2a2 2 0 010 4h-2v-4m0 4h2"/></svg>',
        'code'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
        'python'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2C8 2 7 4 7 6v2h5v1H5C3 9 2 11 2 13s1 4 3 4h2v-2c0-2 1-3 5-3s5 1 5 3v2h2c2 0 3-2 3-4s-1-4-3-4h-5V8h5V6c0-2-1-4-7-4z"/></svg>',
        'database' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>',
        'network'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><circle cx="3" cy="6" r="2"/><circle cx="21" cy="6" r="2"/><circle cx="3" cy="18" r="2"/><circle cx="21" cy="18" r="2"/><path d="M5 6h5l2 6-2 6H5M19 6h-5l-2 6 2 6h5"/></svg>',
        'shield'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
        'star'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
        'copy'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>',
        'trash'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>',
        'edit'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>',
        'download' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>',
        'upload'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>',
        'print'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>',
        'plus'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>',
        'check'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>',
        'book'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>',
        'help'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
        'save'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>',
        'filter'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>',
        'search'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>',
    ];
    return $icons[$name] ?? $icons['star'];
}

$categories = blabcsb_get_categories();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cheatsheet Builder — Black-Lab Toolbox</title>
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

/* ambient : .ambient__circle dans tools-shared.css */

/* ── Layout principal : 3 rangées ── */
.app {
    display: grid;
    grid-template-rows: auto auto 1fr;
    height: 100vh;
    overflow: hidden;
    position: relative;
    z-index: 1;
}

/* .hdr : défini dans tools-shared.css */

/* ── Barre titre cheatsheet ── */
.titlebar {
    padding: 0.55rem 1.2rem;
    border-bottom: 1px solid var(--border);
    background: var(--bg-2);
    display: flex;
    align-items: center;
    gap: 0.6rem;
    flex-shrink: 0;
}
.titlebar__input {
    flex: 1;
    padding: 0.38rem 0.8rem;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text);
    font-size: 0.88rem;
    font-weight: 600;
    font-family: inherit;
    outline: none;
    transition: border-color var(--transition), box-shadow var(--transition);
}
.titlebar__input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px var(--accent-soft);
}
.titlebar__input::placeholder { color: var(--text-dim); font-weight: 400; }
.titlebar__label {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
    white-space: nowrap;
}
.cmd-count {
    font-size: 0.7rem;
    color: var(--text-muted);
    background: var(--surface);
    border: 1px solid var(--border);
    padding: 0.2rem 0.6rem;
    border-radius: 20px;
    white-space: nowrap;
}
.cmd-count span { color: var(--accent); font-weight: 700; }

/* ── Corps : 2 colonnes ── */
.body {
    display: grid;
    grid-template-columns: 320px 1fr;
    overflow: hidden;
    min-height: 0;
}

/* ══════════════════════════
   COLONNE GAUCHE — Éditeur
══════════════════════════ */
.col-left {
    display: flex;
    flex-direction: column;
    border-right: 1px solid var(--border);
    overflow: hidden;
    min-height: 0;
}

/* Formulaire d'ajout */
.form-zone {
    padding: 0.9rem 1rem;
    border-bottom: 1px solid var(--border);
    flex-shrink: 0;
    background: var(--bg);
}
.form-section-title {
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--accent);
    margin-bottom: 0.7rem;
    padding-bottom: 0.35rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.form-section-title svg { width: 12px; height: 12px; }

.form-group { margin-bottom: 0.6rem; }
.form-group label {
    display: block;
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
    margin-bottom: 0.3rem;
}
.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 0.45rem 0.75rem;
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text);
    font-size: 0.82rem;
    font-family: inherit;
    outline: none;
    transition: border-color var(--transition), box-shadow var(--transition);
}
.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px var(--accent-soft);
}
.form-group input::placeholder,
.form-group textarea::placeholder { color: var(--text-dim); }
#inp-command { font-family: 'Fira Code', 'Cascadia Code', monospace; font-size: 0.82rem; }
.form-group textarea { height: 60px; resize: none; }
.form-group select option { background: var(--bg-2); color: var(--text); }

/* Bouton ajouter */
.btn-add-main {
    width: 100%;
    padding: 0.5rem;
    background: var(--gradient);
    border: none;
    border-radius: var(--radius-sm);
    color: #fff;
    font-size: 0.82rem;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    transition: opacity var(--transition);
    margin-top: 0.2rem;
}
.btn-add-main:hover { opacity: 0.88; }
.btn-add-main svg { width: 14px; height: 14px; }
.btn-add-main.editing {
    background: linear-gradient(135deg, #ffbd2e, #f97316);
}

/* Liste des commandes */
.list-zone {
    flex: 1;
    overflow-y: auto;
    padding: 0.6rem 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    background: var(--bg);
    min-height: 0;
}
.list-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    gap: 0.5rem;
    color: var(--text-dim);
    font-size: 0.8rem;
    text-align: center;
}
.list-empty svg { width: 28px; height: 28px; opacity: 0.2; }

/* Filtre de recherche */
.list-search {
    position: relative;
    margin-bottom: 0.4rem;
    flex-shrink: 0;
}
.list-search input {
    width: 100%;
    padding: 0.38rem 0.7rem 0.38rem 2rem;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text);
    font-size: 0.78rem;
    font-family: inherit;
    outline: none;
    transition: border-color var(--transition);
}
.list-search input:focus { border-color: var(--accent); }
.list-search input::placeholder { color: var(--text-dim); }
.list-search svg {
    position: absolute;
    left: 0.55rem;
    top: 50%;
    transform: translateY(-50%);
    width: 12px; height: 12px;
    color: var(--text-dim);
    pointer-events: none;
}

.cmd-item {
    background: var(--surface);
    border: 1px solid var(--border);
    border-left: 2px solid rgba(255,22,84,0.4);
    border-radius: var(--radius-sm);
    padding: 0.5rem 0.65rem;
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    transition: border-color var(--transition), background var(--transition);
    cursor: default;
}
.cmd-item:hover { border-color: var(--accent); background: var(--surface-h); }
.cmd-item:hover .cmd-item-lb { border-left-color: var(--accent); }
.cmd-item-content { flex: 1; min-width: 0; }
.cmd-item-section {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--text-muted);
    margin-bottom: 0.2rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cmd-item-command {
    font-family: 'Fira Code', 'Cascadia Code', monospace;
    font-size: 0.78rem;
    color: var(--cyan);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cmd-item-actions { display: flex; gap: 3px; flex-shrink: 0; }

/* ══════════════════════════
   COLONNE DROITE — Preview
══════════════════════════ */
.col-right {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    min-height: 0;
}

/* Barre d'actions export */
.export-bar {
    padding: 0.55rem 1rem;
    border-bottom: 1px solid var(--border);
    background: var(--bg-2);
    display: flex;
    align-items: center;
    gap: 0.4rem;
    flex-wrap: wrap;
    flex-shrink: 0;
}
.export-bar-label {
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
    margin-right: 0.2rem;
}

/* Zone de prévisualisation */
.preview-zone {
    flex: 1;
    overflow-y: auto;
    padding: 1rem 1.2rem;
    min-height: 0;
}
.preview-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    gap: 0.6rem;
    color: var(--text-dim);
    font-size: 0.82rem;
    text-align: center;
}
.preview-empty svg { width: 32px; height: 32px; opacity: 0.18; }

.preview-section { margin-bottom: 1.4rem; }
.preview-section-title {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--accent);
    border-bottom: 1px solid rgba(255,22,84,0.2);
    padding-bottom: 0.35rem;
    margin-bottom: 0.6rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.preview-section-title .section-count {
    font-size: 0.6rem;
    background: var(--accent-soft);
    color: var(--accent);
    padding: 0.08rem 0.4rem;
    border-radius: 20px;
    margin-left: auto;
}

.preview-command {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.7rem 0.9rem;
    margin-bottom: 0.45rem;
    transition: border-color var(--transition), transform var(--transition);
}
.preview-command:hover {
    border-color: var(--accent);
    transform: translateX(2px);
}
.preview-command-top {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    flex-wrap: wrap;
}
.preview-command-code {
    font-family: 'Fira Code', 'Cascadia Code', monospace;
    font-size: 0.85rem;
    color: var(--cyan);
    font-weight: 600;
    flex: 1;
    word-break: break-all;
}
.preview-command-desc {
    font-size: 0.78rem;
    color: var(--text-muted);
    margin-top: 0.4rem;
    font-style: italic;
    line-height: 1.45;
}
.cat-badge {
    font-size: 0.62rem;
    font-weight: 700;
    padding: 0.15rem 0.55rem;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    flex-shrink: 0;
    white-space: nowrap;
}

/* ── Boutons génériques ── */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.38rem 0.8rem;
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
}
.btn svg { width: 13px; height: 13px; flex-shrink: 0; }
.btn:hover { color: var(--text); border-color: var(--accent); background: var(--surface-h); }

.btn--ghost-help {
    padding: 0.3rem 0.7rem;
    border: 1px solid rgba(255,22,84,0.3);
    background: var(--accent-soft);
    color: var(--accent);
}
.btn--ghost-help:hover { background: var(--accent); color: #fff; border-color: var(--accent); }

.btn--success { border-color: rgba(39,201,63,0.4); color: var(--success); }
.btn--success:hover { background: rgba(39,201,63,0.1); border-color: var(--success); color: var(--success); }
.btn--info    { border-color: rgba(34,211,238,0.4); color: var(--cyan); }
.btn--info:hover    { background: rgba(34,211,238,0.1); border-color: var(--cyan); color: var(--cyan); }
.btn--warning { border-color: rgba(255,189,46,0.4); color: var(--warning); }
.btn--warning:hover { background: rgba(255,189,46,0.1); border-color: var(--warning); color: var(--warning); }
.btn--danger  { border-color: rgba(255,95,86,0.4);  color: var(--danger); }
.btn--danger:hover  { background: rgba(255,95,86,0.1); border-color: var(--danger); color: var(--danger); }

/* Boutons icônes */
.btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px; height: 26px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border);
    background: var(--surface);
    color: var(--text-muted);
    cursor: pointer;
    transition: all var(--transition);
    padding: 0;
    font-family: inherit;
    flex-shrink: 0;
}
.btn-icon svg { width: 12px; height: 12px; }
.btn-icon.btn-edit:hover   { color: var(--warning); border-color: var(--warning); background: rgba(255,189,46,0.08); }
.btn-icon.btn-delete:hover { color: var(--danger);  border-color: var(--danger);  background: rgba(255,95,86,0.08);  }
.btn-icon.btn-copy-cmd:hover { color: var(--cyan); border-color: var(--cyan); background: rgba(34,211,238,0.08); }

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
.toast.error   { border-color: var(--accent);  }
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
.modal-close:hover { color: var(--danger); border-color: var(--danger); background: rgba(255,95,86,0.08); }

.modal-body { padding: 1.2rem 1.3rem; display: flex; flex-direction: column; gap: 1.2rem; }

.modal-section-title {
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--accent);
    border-bottom: 1px solid rgba(255,22,84,0.2);
    padding-bottom: 0.35rem;
    margin-bottom: 0.8rem;
}
.modal-row {
    display: flex;
    align-items: flex-start;
    gap: 0.8rem;
    margin-bottom: 0.7rem;
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
}
.modal-row-icon svg { width: 13px; height: 13px; }
.modal-row-text strong { display: block; color: var(--text); margin-bottom: 0.2rem; font-size: 0.82rem; }
.modal-row-text span  { color: var(--text-muted); font-size: 0.78rem; line-height: 1.5; }

.cat-grid {
    display: grid;
    grid-template-columns: repeat(2,1fr);
    gap: 0.4rem;
}
.cat-grid-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.45rem 0.75rem;
    font-size: 0.78rem;
    font-weight: 600;
}
.cat-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

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
.modal-shortcut-grid {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 0.3rem 0.8rem;
    font-size: 0.78rem;
}
.modal-shortcut-grid kbd {
    background: var(--surface-h);
    border: 1px solid var(--border);
    border-radius: 4px;
    padding: 0.1rem 0.45rem;
    font-family: 'Fira Code', monospace;
    font-size: 0.72rem;
    color: var(--accent);
    white-space: nowrap;
    display: inline-block;
}
.modal-shortcut-grid span { color: var(--text-muted); display: flex; align-items: center; }


/* ── Print ── */
@media print {
    body { background: #fff; color: #111; overflow: visible; height: auto; }
    .col-left, .hdr, .titlebar, .export-bar { display: none !important; }
    .app { display: block; height: auto; }
    .body { display: block; }
    .col-right { display: block; overflow: visible; }
    .preview-zone { overflow: visible; }
    .preview-command { border: 1px solid #ddd; border-left: 3px solid #333; background: #f9f9f9; box-shadow: none; transform: none !important; }
    .preview-command-code { color: #1a1a1a; }
    .preview-command-desc { color: #555; }
    .preview-section-title { color: #333; border-bottom-color: #ccc; }
    .btn-icon, .toast, .modal-overlay { display: none !important; }
    .cat-badge { border: 1px solid #ccc !important; color: #333 !important; background: #eee !important; }
}

/* ── Responsive ── */
@media (max-width: 800px) {
    .body { grid-template-columns: 1fr; grid-template-rows: 1fr 1fr; }
    .col-left { border-right: none; border-bottom: 1px solid var(--border); }
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
        <span class="hdr__icon">📋</span>
        <span class="hdr__title">CHEATSHEET BUILDER</span>
        <span class="hdr__meta">Créez, organisez et exportez vos aide-mémoire techniques</span>
        <div class="hdr__right">
            <button class="btn btn--ghost-help" onclick="openModal()">
                <?= blabcsb_icon('help') ?> Aide
            </button>
        </div>
    </header>

    <!-- ══ BARRE TITRE ════════════════════════════════════════ -->
    <div class="titlebar">
        <span class="titlebar__label">Titre :</span>
        <input type="text" class="titlebar__input" id="cs-title"
               placeholder="Ma cheat sheet…" maxlength="120" autocomplete="off">
        <div class="cmd-count">Total : <span id="cmd-total">0</span> cmd</div>
    </div>

    <!-- ══ CORPS ══════════════════════════════════════════════ -->
    <div class="body">

        <!-- ── COLONNE GAUCHE ── -->
        <div class="col-left">

            <!-- Formulaire -->
            <div class="form-zone">
                <div class="form-section-title">
                    <?= blabcsb_icon('plus') ?> Ajouter une commande
                </div>

                <div class="form-group">
                    <label>Section</label>
                    <input type="text" id="inp-section" placeholder="Ex : Commandes Git"
                           maxlength="80" autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Commande</label>
                    <input type="text" id="inp-command" placeholder="Ex : git status"
                           maxlength="300" autocomplete="off" spellcheck="false">
                </div>
                <div class="form-group">
                    <label>Description <span style="color:var(--text-dim);font-weight:400;text-transform:none">(optionnel)</span></label>
                    <textarea id="inp-desc" placeholder="Ex : Affiche l'état des fichiers dans le dépôt"
                              maxlength="400"></textarea>
                </div>
                <div class="form-group">
                    <label>Catégorie</label>
                    <select id="inp-category">
                        <?php foreach ($categories as $key => $cat): ?>
                        <option value="<?= htmlspecialchars($key, ENT_QUOTES) ?>">
                            <?= htmlspecialchars($cat['label'], ENT_QUOTES) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button class="btn-add-main" id="btn-add" onclick="handleAdd()">
                    <?= blabcsb_icon('plus') ?>
                    <span id="btn-add-label">Ajouter la commande</span>
                </button>
            </div>

            <!-- Liste des commandes -->
            <div class="list-zone">
                <div class="list-search">
                    <?= blabcsb_icon('search') ?>
                    <input type="text" id="search-input" placeholder="Filtrer les commandes…"
                           oninput="filterList()" autocomplete="off">
                </div>
                <div id="commands-list">
                    <div class="list-empty">
                        <?= blabcsb_icon('book') ?>
                        <span>Aucune commande<br>Remplissez le formulaire</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── COLONNE DROITE ── -->
        <div class="col-right">

            <!-- Barre export -->
            <div class="export-bar">
                <span class="export-bar-label">Export :</span>
                <button class="btn btn--success" onclick="exportJSON()">
                    <?= blabcsb_icon('download') ?> JSON
                </button>
                <button class="btn btn--info" onclick="exportHTML()">
                    <?= blabcsb_icon('download') ?> HTML
                </button>
                <button class="btn btn--warning" onclick="window.print()">
                    <?= blabcsb_icon('print') ?> Imprimer
                </button>
                <button class="btn" onclick="importJSON()">
                    <?= blabcsb_icon('upload') ?> Importer
                </button>
                <button class="btn btn--danger" onclick="clearAll()" style="margin-left:auto">
                    <?= blabcsb_icon('trash') ?> Effacer tout
                </button>
                <input type="file" id="file-import" accept=".json"
                       style="display:none" onchange="handleImport(event)">
            </div>

            <!-- Prévisualisation -->
            <div class="preview-zone" id="preview">
                <div class="preview-empty">
                    <?= blabcsb_icon('book') ?>
                    <span>Votre cheat sheet s'affichera ici</span>
                </div>
            </div>
        </div>

    </div><!-- /.body -->
</div><!-- /.app -->

<!-- ══ TOAST ═══════════════════════════════════════════════ -->
<div class="toast" id="toast"></div>

<!-- ══ MODALE AIDE ══════════════════════════════════════════ -->
<div class="modal-overlay" id="modal-help" onclick="closeModalOutside(event)">
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modal-title">

        <div class="modal-head">
            <h2 id="modal-title">📚 Aide &amp; Légende</h2>
            <button class="modal-close" onclick="closeModal()" title="Fermer">✕</button>
        </div>

        <div class="modal-body">

            <!-- Utilisation -->
            <div>
                <div class="modal-section-title">Comment ça marche ?</div>
                <div class="modal-row">
                    <div class="modal-row-icon" style="color:var(--accent)"><?= blabcsb_icon('plus') ?></div>
                    <div class="modal-row-text">
                        <strong>Ajouter une commande</strong>
                        <span>Remplissez la <em>Section</em>, la <em>Commande</em> et optionnellement une <em>Description</em>, puis cliquez sur "Ajouter". Les commandes sont groupées par section dans la prévisualisation.</span>
                    </div>
                </div>
                <div class="modal-row">
                    <div class="modal-row-icon" style="color:var(--warning)"><?= blabcsb_icon('edit') ?></div>
                    <div class="modal-row-text">
                        <strong>Éditer / Supprimer</strong>
                        <span>Dans la liste, ✏️ pour modifier (le formulaire se pré-remplit), 🗑️ pour supprimer. En mode édition, le bouton devient <em>Mettre à jour</em>.</span>
                    </div>
                </div>
                <div class="modal-row">
                    <div class="modal-row-icon" style="color:var(--cyan)"><?= blabcsb_icon('copy') ?></div>
                    <div class="modal-row-text">
                        <strong>Copie rapide</strong>
                        <span>Dans la prévisualisation, chaque commande a un bouton 📋 pour la copier dans le presse-papiers instantanément.</span>
                    </div>
                </div>
                <div class="modal-row">
                    <div class="modal-row-icon" style="color:var(--text-muted)"><?= blabcsb_icon('search') ?></div>
                    <div class="modal-row-text">
                        <strong>Filtrer la liste</strong>
                        <span>Le champ de recherche filtre en temps réel sur le nom de la commande et sa section.</span>
                    </div>
                </div>
            </div>

            <!-- Raccourcis clavier -->
            <div>
                <div class="modal-section-title">Raccourcis clavier</div>
                <div class="modal-shortcut-grid">
                    <kbd>Entrée</kbd><span>Ajouter / Mettre à jour la commande (quand un champ est actif)</span>
                    <kbd>Échap</kbd><span>Annuler l'édition en cours / Fermer la modale</span>
                    <kbd>Ctrl+Z</kbd><span>Annuler la dernière suppression (à venir)</span>
                </div>
            </div>

            <!-- Catégories -->
            <div>
                <div class="modal-section-title">Catégories disponibles</div>
                <div class="cat-grid">
                    <?php foreach ($categories as $key => $cat): ?>
                    <div class="cat-grid-item">
                        <div class="cat-dot" style="background:<?= htmlspecialchars($cat['color'], ENT_QUOTES) ?>"></div>
                        <span style="color:<?= htmlspecialchars($cat['color'], ENT_QUOTES) ?>">
                            <?= htmlspecialchars($cat['label'], ENT_QUOTES) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <p style="color:var(--text-dim);font-size:0.72rem;margin-top:0.6rem;">
                    Le badge de catégorie est visible dans la prévisualisation et les exports.
                </p>
            </div>

            <!-- Export / Import -->
            <div>
                <div class="modal-section-title">Export &amp; Import</div>
                <div class="modal-row">
                    <div class="modal-row-icon" style="color:var(--success)"><?= blabcsb_icon('download') ?></div>
                    <div class="modal-row-text">
                        <strong>Export JSON</strong>
                        <span>Sauvegarde complète et réimportable. Inclut le titre, la date et toutes les métadonnées.</span>
                    </div>
                </div>
                <div class="modal-row">
                    <div class="modal-row-icon" style="color:var(--cyan)"><?= blabcsb_icon('download') ?></div>
                    <div class="modal-row-text">
                        <strong>Export HTML</strong>
                        <span>Fichier autonome au style Lab'O Noir, consultable hors-ligne et compatible impression.</span>
                    </div>
                </div>
                <div class="modal-row">
                    <div class="modal-row-icon" style="color:var(--warning)"><?= blabcsb_icon('print') ?></div>
                    <div class="modal-row-text">
                        <strong>Impression</strong>
                        <span>Seule la prévisualisation est imprimée — le panneau éditeur est masqué automatiquement.</span>
                    </div>
                </div>
                <div class="modal-row">
                    <div class="modal-row-icon" style="color:var(--text-muted)"><?= blabcsb_icon('upload') ?></div>
                    <div class="modal-row-text">
                        <strong>Import JSON</strong>
                        <span>Les commandes s'ajoutent à celles déjà présentes. Taille max : 512 Ko. Le titre est restauré s'il était présent.</span>
                    </div>
                </div>
            </div>

            <div class="modal-tip">
                <strong>💡 Astuce :</strong> Donnez un nom précis à votre cheat sheet dans la barre de titre — il apparaîtra dans les exports JSON, HTML et à l'impression. Vous pouvez créer autant de cheat sheets que vous voulez en exportant/important des JSON différents.
            </div>

        </div>
    </div>
</div>

<script>
/* ══════════════════════════════════════════════════════════════
   CONFIG CATÉGORIES (miroir PHP → JS)
══════════════════════════════════════════════════════════════ */
const CATEGORIES = <?= json_encode($categories, JSON_UNESCAPED_UNICODE) ?>;

/* ══════════════════════════════════════════════════════════════
   ÉTAT
══════════════════════════════════════════════════════════════ */
let commands  = [];
let nextId    = 1;
let editingId = null;

/* ══════════════════════════════════════════════════════════════
   SANITISATION
══════════════════════════════════════════════════════════════ */
function sanitize(str) {
    if (typeof str !== 'string') return '';
    return str.trim().slice(0, 500);
}

/* ══════════════════════════════════════════════════════════════
   TOAST
══════════════════════════════════════════════════════════════ */
let toastTimer = null;
function toast(msg, type = 'success') {
    const el = document.getElementById('toast');
    el.className = 'toast show ' + type;
    el.textContent = msg;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => el.classList.remove('show'), 2800);
}

/* ══════════════════════════════════════════════════════════════
   FORMULAIRE
══════════════════════════════════════════════════════════════ */
function getFormValues() {
    return {
        section:  sanitize(document.getElementById('inp-section').value),
        command:  sanitize(document.getElementById('inp-command').value),
        desc:     sanitize(document.getElementById('inp-desc').value),
        category: document.getElementById('inp-category').value,
    };
}

function resetForm() {
    document.getElementById('inp-command').value = '';
    document.getElementById('inp-desc').value    = '';
    editingId = null;
    const btn = document.getElementById('btn-add');
    document.getElementById('btn-add-label').textContent = 'Ajouter la commande';
    btn.classList.remove('editing');
}

function handleAdd() {
    const v = getFormValues();
    if (!v.section || !v.command) {
        toast('⚠️ Section et commande sont obligatoires', 'error');
        return;
    }
    if (editingId !== null) {
        const idx = commands.findIndex(c => c.id === editingId);
        if (idx !== -1) {
            commands[idx] = { id: editingId, ...v };
            toast('✏️ Commande mise à jour', 'info');
        }
        resetForm();
    } else {
        commands.push({ id: nextId++, ...v });
        toast('✅ Commande ajoutée');
    }
    document.getElementById('inp-command').value = '';
    document.getElementById('inp-desc').value    = '';
    render();
}

function editCommand(id) {
    const cmd = commands.find(c => c.id === id);
    if (!cmd) return;
    document.getElementById('inp-section').value  = cmd.section;
    document.getElementById('inp-command').value  = cmd.command;
    document.getElementById('inp-desc').value     = cmd.desc;
    document.getElementById('inp-category').value = cmd.category;
    editingId = id;
    const btn = document.getElementById('btn-add');
    document.getElementById('btn-add-label').textContent = 'Mettre à jour';
    btn.classList.add('editing');
    document.getElementById('inp-command').focus();
}

function removeCommand(id) {
    commands = commands.filter(c => c.id !== id);
    if (editingId === id) resetForm();
    render();
    toast('🗑️ Commande supprimée');
}

function clearAll() {
    if (!commands.length) return;
    if (!confirm('Effacer toutes les commandes ?')) return;
    commands = []; nextId = 1; editingId = null;
    resetForm();
    render();
    toast('🗑️ Cheat sheet effacée');
}

/* ══════════════════════════════════════════════════════════════
   COPIER UNE COMMANDE
══════════════════════════════════════════════════════════════ */
function copyCommand(text) {
    navigator.clipboard.writeText(text).then(() => toast('📋 Copié !', 'info'));
}

/* ══════════════════════════════════════════════════════════════
   GROUPER PAR SECTION
══════════════════════════════════════════════════════════════ */
function groupBySection(cmds) {
    return cmds.reduce((acc, cmd) => {
        (acc[cmd.section] = acc[cmd.section] || []).push(cmd);
        return acc;
    }, {});
}

/* ══════════════════════════════════════════════════════════════
   FILTRE DE RECHERCHE
══════════════════════════════════════════════════════════════ */
function filterList() {
    const q = document.getElementById('search-input').value.toLowerCase().trim();
    renderList(q);
}

/* ══════════════════════════════════════════════════════════════
   RENDU — createElement / textContent, zéro innerHTML user
══════════════════════════════════════════════════════════════ */
function render() {
    document.getElementById('cmd-total').textContent = commands.length;
    renderList(document.getElementById('search-input').value.toLowerCase().trim());
    renderPreview();
}

function renderList(filter = '') {
    const list = document.getElementById('commands-list');
    list.innerHTML = '';

    const filtered = filter
        ? commands.filter(c => c.command.toLowerCase().includes(filter) || c.section.toLowerCase().includes(filter))
        : commands;

    if (!filtered.length) {
        const empty = document.createElement('div');
        empty.className = 'list-empty';
        empty.textContent = filter ? 'Aucun résultat' : 'Aucune commande ajoutée';
        list.appendChild(empty);
        return;
    }

    filtered.forEach(cmd => {
        const cat = CATEGORIES[cmd.category] || CATEGORIES['autre'];
        const item = document.createElement('div');
        item.className = 'cmd-item';

        const content = document.createElement('div');
        content.className = 'cmd-item-content';

        const sectionEl = document.createElement('div');
        sectionEl.className = 'cmd-item-section';
        sectionEl.textContent = cmd.section;

        const commandEl = document.createElement('div');
        commandEl.className = 'cmd-item-command';
        commandEl.style.color = cat.color;
        commandEl.textContent = cmd.command;

        content.appendChild(sectionEl);
        content.appendChild(commandEl);

        const actions = document.createElement('div');
        actions.className = 'cmd-item-actions';

        const btnEdit = document.createElement('button');
        btnEdit.className = 'btn-icon btn-edit';
        btnEdit.title = 'Éditer';
        btnEdit.innerHTML = <?= json_encode(blabcsb_icon('edit')) ?>;
        btnEdit.addEventListener('click', () => editCommand(cmd.id));

        const btnDel = document.createElement('button');
        btnDel.className = 'btn-icon btn-delete';
        btnDel.title = 'Supprimer';
        btnDel.innerHTML = <?= json_encode(blabcsb_icon('trash')) ?>;
        btnDel.addEventListener('click', () => removeCommand(cmd.id));

        actions.appendChild(btnEdit);
        actions.appendChild(btnDel);

        item.appendChild(content);
        item.appendChild(actions);
        list.appendChild(item);
    });
}

function renderPreview() {
    const preview = document.getElementById('preview');
    preview.innerHTML = '';

    if (!commands.length) {
        const empty = document.createElement('div');
        empty.className = 'preview-empty';
        empty.innerHTML = <?= json_encode(blabcsb_icon('book')) ?>;
        const txt = document.createElement('span');
        txt.textContent = 'Votre cheat sheet s\'affichera ici';
        empty.appendChild(txt);
        preview.appendChild(empty);
        return;
    }

    const grouped = groupBySection(commands);

    Object.keys(grouped).forEach(sectionName => {
        const sectionEl = document.createElement('div');
        sectionEl.className = 'preview-section';

        const title = document.createElement('div');
        title.className = 'preview-section-title';
        title.textContent = sectionName;

        const countBadge = document.createElement('span');
        countBadge.className = 'section-count';
        countBadge.textContent = grouped[sectionName].length + ' cmd';
        title.appendChild(countBadge);

        sectionEl.appendChild(title);

        grouped[sectionName].forEach(cmd => {
            const cat = CATEGORIES[cmd.category] || CATEGORIES['autre'];

            const block = document.createElement('div');
            block.className = 'preview-command';

            const top = document.createElement('div');
            top.className = 'preview-command-top';

            const codeEl = document.createElement('span');
            codeEl.className = 'preview-command-code';
            codeEl.textContent = cmd.command;

            const badge = document.createElement('span');
            badge.className = 'cat-badge';
            badge.style.background = cat.color + '22';
            badge.style.color      = cat.color;
            badge.style.border     = '1px solid ' + cat.color + '44';
            badge.textContent      = cat.label;

            const btnCopy = document.createElement('button');
            btnCopy.className = 'btn-icon btn-copy-cmd';
            btnCopy.title = 'Copier la commande';
            btnCopy.innerHTML = <?= json_encode(blabcsb_icon('copy')) ?>;
            btnCopy.addEventListener('click', () => copyCommand(cmd.command));

            top.appendChild(codeEl);
            top.appendChild(badge);
            top.appendChild(btnCopy);
            block.appendChild(top);

            if (cmd.desc) {
                const desc = document.createElement('div');
                desc.className = 'preview-command-desc';
                desc.textContent = cmd.desc;
                block.appendChild(desc);
            }

            sectionEl.appendChild(block);
        });

        preview.appendChild(sectionEl);
    });
}

/* ══════════════════════════════════════════════════════════════
   EXPORT JSON
══════════════════════════════════════════════════════════════ */
function exportJSON() {
    if (!commands.length) { toast('⚠️ Aucune commande à exporter', 'error'); return; }
    const title = document.getElementById('cs-title').value.trim() || 'CheatSheet';
    const data  = {
        title,
        version:    '2.0',
        exportDate: new Date().toISOString(),
        generator:  'Lab\'O Noir — CheatSheet Builder',
        commands,
    };
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'cheatsheet-' + new Date().toISOString().split('T')[0] + '.json';
    a.click();
    URL.revokeObjectURL(a.href);
    toast('💾 JSON exporté');
}

/* ══════════════════════════════════════════════════════════════
   IMPORT JSON
══════════════════════════════════════════════════════════════ */
function importJSON() { document.getElementById('file-import').click(); }

function handleImport(evt) {
    const file = evt.target.files[0];
    if (!file) return;
    if (file.size > 512 * 1024) { toast('⚠️ Fichier trop volumineux (max 512 Ko)', 'error'); return; }
    const reader = new FileReader();
    reader.onload = e => {
        try {
            const raw = JSON.parse(e.target.result);
            if (!Array.isArray(raw.commands)) throw new Error('Format invalide');
            const imported = raw.commands.map(c => ({
                id:       nextId++,
                section:  sanitize(String(c.section  || 'Importé')),
                command:  sanitize(String(c.command  || '')),
                desc:     sanitize(String(c.desc     || c.description || '')),
                category: CATEGORIES[c.category] ? c.category : 'autre',
            })).filter(c => c.command);
            if (!imported.length) throw new Error('Aucune commande valide');
            commands = [...commands, ...imported];
            if (raw.title) document.getElementById('cs-title').value = sanitize(raw.title);
            render();
            toast(`📥 ${imported.length} commande(s) importée(s)`);
        } catch (err) {
            toast('⚠️ Fichier JSON invalide', 'error');
        }
        evt.target.value = '';
    };
    reader.readAsText(file, 'UTF-8');
}

/* ══════════════════════════════════════════════════════════════
   EXPORT HTML — cheat sheet autonome, style Lab'O Noir
══════════════════════════════════════════════════════════════ */
function exportHTML() {
    if (!commands.length) { toast('⚠️ Aucune commande à exporter', 'error'); return; }
    const title   = document.getElementById('cs-title').value.trim() || 'CheatSheet';
    const grouped = groupBySection(commands);
    const esc = s => s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

    let sectionsHTML = '';
    Object.keys(grouped).forEach(sec => {
        let cmdsHTML = '';
        grouped[sec].forEach(cmd => {
            const cat = CATEGORIES[cmd.category] || CATEGORIES['autre'];
            cmdsHTML += `
        <div class="cmd">
          <div class="cmd-top">
            <code>${esc(cmd.command)}</code>
            <span class="badge" style="background:${cat.color}22;color:${cat.color};border:1px solid ${cat.color}44">${cat.label}</span>
          </div>
          ${cmd.desc ? `<div class="cmd-desc">${esc(cmd.desc)}</div>` : ''}
        </div>`;
        });
        sectionsHTML += `
    <div class="section">
      <h2>${esc(sec)}</h2>
      ${cmdsHTML}
    </div>`;
    });

    const html = `<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>${esc(title)}</title>
<style>
:root{--accent:#ff1654;--cyan:#22d3ee;--bg:#0d0d0f;--surface:rgba(255,255,255,.05);--border:rgba(255,255,255,.08);--text:#e8e8f0;--muted:#7a7a90}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',system-ui,sans-serif;background:var(--bg);color:var(--text);padding:2rem;min-height:100vh;-webkit-font-smoothing:antialiased}
header{margin-bottom:2rem;padding-bottom:1rem;border-bottom:1px solid var(--border)}
h1{font-size:1.6rem;font-weight:800;background:linear-gradient(135deg,#ff1654,#5e006c);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:.3rem}
.meta{color:var(--muted);font-size:.78rem}
.section{margin-bottom:1.8rem}
h2{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--accent);border-bottom:1px solid rgba(255,22,84,.2);padding-bottom:.35rem;margin-bottom:.6rem}
.cmd{background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:.65rem .9rem;margin-bottom:.4rem}
.cmd-top{display:flex;align-items:center;gap:.6rem;flex-wrap:wrap}
code{font-family:'Courier New',monospace;font-size:.88rem;color:var(--cyan);font-weight:600;flex:1;word-break:break-all}
.badge{font-size:.6rem;font-weight:700;padding:.12rem .5rem;border-radius:20px;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap}
.cmd-desc{font-size:.78rem;color:var(--muted);margin-top:.35rem;font-style:italic}
@media print{body{background:#fff;color:#111}code{color:#1a1a1a}.cmd{background:#f8f8f8;border-color:#ddd}.cmd-desc{color:#555}h2{color:#333}h1{-webkit-text-fill-color:#333}}
</style>
</head>
<body>
<header>
  <h1>${esc(title)}</h1>
  <div class="meta">Généré le ${new Date().toLocaleDateString('fr-FR')} · Lab'O Noir — CheatSheet Builder</div>
</header>
${sectionsHTML}
</body>
</html>`;

    const blob = new Blob([html], { type: 'text/html;charset=utf-8' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'cheatsheet-' + new Date().toISOString().split('T')[0] + '.html';
    a.click();
    URL.revokeObjectURL(a.href);
    toast('📄 HTML exporté');
}

/* ══════════════════════════════════════════════════════════════
   MODALE
══════════════════════════════════════════════════════════════ */
function openModal()  { document.getElementById('modal-help').classList.add('open'); }
function closeModal() { document.getElementById('modal-help').classList.remove('open'); }
function closeModalOutside(e) { if (e.target === document.getElementById('modal-help')) closeModal(); }

/* ══════════════════════════════════════════════════════════════
   RACCOURCIS CLAVIER
══════════════════════════════════════════════════════════════ */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeModal();
        if (editingId !== null) { resetForm(); toast('Édition annulée'); }
        return;
    }
    if (e.key === 'Enter' && ['inp-section','inp-command','inp-desc'].includes(document.activeElement.id)) {
        e.preventDefault();
        handleAdd();
    }
});

/* ══════════════════════════════════════════════════════════════
   INIT
══════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => render());
</script>
</body>
</html>