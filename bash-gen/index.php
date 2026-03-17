<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bash Gen — Black-Lab Toolbox</title>
    <style>
    :root {
        --bg:          #0d0d0f;
        --bg-2:        #111116;
        --bg-3:        #16161c;
        --surface:     rgba(255,255,255,0.04);
        --surface-h:   rgba(255,255,255,0.07);
        --border:      rgba(255,255,255,0.08);
        --border-h:    rgba(255,255,255,0.14);
        --accent:      #ff1654;
        --accent-soft: rgba(255,22,84,0.14);
        --green:       #27c93f;
        --green-soft:  rgba(39,201,63,0.12);
        --violet:      #5e006c;
        --gradient:    linear-gradient(135deg,#ff1654,#5e006c);
        --text:        #e8e8f0;
        --text-muted:  #7a7a90;
        --text-dim:    #3a3a50;
        --mono:        'Cascadia Code','Fira Code','Consolas',monospace;
        --radius:      12px;
        --radius-sm:   7px;
        --trans:       0.16s ease;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html, body {
        height: 100%;
        font-family: 'Segoe UI', system-ui, sans-serif;
        font-size: 14px;
        background: var(--bg);
        color: var(--text);
        -webkit-font-smoothing: antialiased;
    }

    /* ── Ambient ──────────────────────────────────────── */
    .ambient {
        position: fixed; inset: 0;
        pointer-events: none; overflow: hidden; z-index: 0;
    }
    /* ambient__circle défini dans tools-shared.css */

    /* ── Layout ───────────────────────────────────────── */
    .page {
        position: relative; z-index: 1;
        min-height: 100vh;
        display: grid;
        grid-template-rows: auto 1fr auto;
        gap: 0;
    }

    /* ── Header : .hdr défini dans tools-shared.css ─── */

    /* ── Main area ────────────────────────────────────── */
    .main {
        display: grid;
        grid-template-columns: 280px 1fr 300px;
        gap: 0;
        overflow: hidden;
        height: calc(100vh - 57px);
    }

    /* ── Sidebar : command picker ─────────────────────── */
    .cmd-sidebar {
        border-right: 1px solid var(--border);
        overflow-y: auto;
        padding: 1rem 0.8rem;
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }
    .cmd-sidebar__section {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-dim);
        padding: 0.6rem 0.4rem 0.2rem;
    }

    .cmd-btn {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.55rem 0.8rem;
        border-radius: var(--radius-sm);
        border: 1px solid transparent;
        background: none;
        color: var(--text-muted);
        font-size: 0.82rem;
        font-weight: 500;
        cursor: pointer;
        transition: all var(--trans);
        text-align: left;
        width: 100%;
    }
    .cmd-btn:hover { background: var(--surface); color: var(--text); }
    .cmd-btn.active {
        background: var(--accent-soft);
        border-color: var(--accent);
        color: var(--accent);
        font-weight: 600;
    }
    .cmd-btn__name {
        font-family: var(--mono);
        font-size: 0.85rem;
        font-weight: 700;
    }
    .cmd-btn__desc { font-size: 0.72rem; opacity: .7; }

    /* ── Center : options + output ────────────────────── */
    .center {
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .options-area {
        padding: 1.2rem 1.4rem;
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
        border-bottom: 1px solid var(--border);
    }

    .options-area__title {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--accent);
    }

    .opts-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.7rem;
    }
    .opts-grid.single { grid-template-columns: 1fr; }

    .field {
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }
    .field label {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    .field label .badge {
        font-size: 0.6rem;
        padding: 0.1rem 0.35rem;
        border-radius: 3px;
        background: var(--accent-soft);
        color: var(--accent);
        font-weight: 700;
        letter-spacing: 0.04em;
    }

    input[type="text"], input[type="number"], select, textarea {
        width: 100%;
        padding: 0.5rem 0.75rem;
        background: var(--bg-2);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        color: var(--text);
        font-size: 0.82rem;
        font-family: inherit;
        outline: none;
        transition: border-color var(--trans), box-shadow var(--trans);
    }
    input[type="text"]:focus, input[type="number"]:focus,
    select:focus, textarea:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px var(--accent-soft);
    }
    select option { background: #1a1a22; }
    textarea { resize: vertical; min-height: 70px; font-family: var(--mono); font-size: 0.8rem; }

    .checkbox-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .check-pill {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.65rem;
        border: 1px solid var(--border);
        border-radius: 20px;
        cursor: pointer;
        font-size: 0.78rem;
        color: var(--text-muted);
        transition: all var(--trans);
        user-select: none;
        font-family: var(--mono);
    }
    .check-pill:hover { border-color: var(--accent); color: var(--text); }
    .check-pill input { display: none; }
    .check-pill.checked {
        background: var(--accent-soft);
        border-color: var(--accent);
        color: var(--accent);
    }

    /* ── Terminal output ──────────────────────────────── */
    .terminal {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .terminal-bar {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 1rem;
        background: #0a0a0d;
        border-bottom: 1px solid var(--border);
        flex-shrink: 0;
    }
    .t-dots { display: flex; gap: 5px; }
    .t-dot { width: 9px; height: 9px; border-radius: 50%; }
    .t-dot-r { background: #ff5f56; }
    .t-dot-y { background: #ffbd2e; }
    .t-dot-g { background: #27c93f; }
    .t-label {
        font-size: 0.65rem; color: var(--accent);
        font-weight: 700; letter-spacing: 0.12em;
        text-transform: uppercase; margin-left: 0.4rem;
    }

    .terminal-body {
        flex: 1;
        padding: 1rem 1.2rem;
        font-family: var(--mono);
        font-size: 0.9rem;
        line-height: 1.7;
        color: var(--green);
        background: #070709;
        overflow-y: auto;
        white-space: pre-wrap;
        word-break: break-all;
    }
    .terminal-body .prompt {
        color: var(--accent);
        font-weight: 700;
        user-select: none;
    }
    .terminal-body .cmd-text {
        color: #e8e8f0;
    }
    .terminal-body .cursor {
        display: inline-block;
        width: 9px; height: 1.1em;
        background: var(--green);
        vertical-align: text-bottom;
        animation: blink 1s step-end infinite;
    }
    @keyframes blink { 50% { opacity: 0; } }

    .terminal-actions {
        display: flex;
        gap: 0.5rem;
        padding: 0.6rem 1rem;
        background: #0a0a0d;
        border-top: 1px solid var(--border);
        flex-shrink: 0;
    }
    .btn {
        padding: 0.4rem 0.9rem;
        border-radius: var(--radius-sm);
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        transition: all var(--trans);
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--text-muted);
    }
    .btn:hover { background: var(--surface-h); color: var(--text); border-color: var(--accent); }
    .btn--primary { background: var(--gradient); border-color: transparent; color: #fff; }
    .btn--primary:hover { opacity: 0.85; border-color: transparent; }
    .btn--green { background: var(--green-soft); border-color: var(--green); color: var(--green); }
    .btn--green:hover { background: rgba(39,201,63,0.2); }
    .btn.copied { background: var(--green-soft); border-color: var(--green); color: var(--green); }

    /* ── Right : explain + history ────────────────────── */
    .right-panel {
        border-left: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .explain-area {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }
    .explain-title {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--accent);
    }
    .explain-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 0.7rem 0.85rem;
        font-size: 0.78rem;
        line-height: 1.55;
        color: var(--text-muted);
    }
    .explain-card__token {
        font-family: var(--mono);
        color: var(--accent);
        font-weight: 700;
        font-size: 0.82rem;
        margin-bottom: 0.25rem;
    }

    .history-area {
        border-top: 1px solid var(--border);
        padding: 0.8rem 1rem;
        max-height: 200px;
        overflow-y: auto;
    }
    .history-title {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-dim);
        margin-bottom: 0.5rem;
    }
    .history-item {
        font-family: var(--mono);
        font-size: 0.72rem;
        color: var(--text-muted);
        padding: 0.35rem 0.5rem;
        border-radius: 4px;
        cursor: pointer;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: all var(--trans);
        border: 1px solid transparent;
    }
    .history-item:hover {
        background: var(--surface);
        color: var(--text);
        border-color: var(--border);
    }
    .history-empty {
        font-size: 0.72rem;
        color: var(--text-dim);
        font-style: italic;
    }


    /* ── Responsive ───────────────────────────────────── */
    @media (max-width: 1024px) {
        .main { grid-template-columns: 220px 1fr; }
        .right-panel { display: none; }
    }
    @media (max-width: 700px) {
        .main { grid-template-columns: 1fr; grid-template-rows: auto 1fr; }
        .cmd-sidebar { display: flex; flex-direction: row; overflow-x: auto; border-right: none; border-bottom: 1px solid var(--border); padding: 0.5rem; height: auto; }
        .cmd-sidebar__section { display: none; }
        .cmd-btn { flex-direction: column; align-items: center; min-width: 70px; gap: 0.2rem; }
        .cmd-btn__desc { display: none; }
    }
    </style>
</head>
<body>

<div class="ambient">
    <div class="ambient__circle"></div>
    <div class="ambient__circle"></div>
</div>

<div class="page">

    <!-- Header -->
    <header class="hdr">
        <span class="hdr__icon">⌨️</span>
        <span class="hdr__title">BASH GENERATOR</span>
        <span class="hdr__sep"></span>
        <span class="hdr__meta">Preview en temps réel · Explications · Historique</span>
    </header>

    <div class="main">

        <!-- Sidebar : command picker -->
        <nav class="cmd-sidebar" id="cmd-sidebar">
            <div class="cmd-sidebar__section">Recherche</div>
            <button class="cmd-btn active" data-cmd="find">
                <span class="cmd-btn__name">find</span>
                <span class="cmd-btn__desc">Chercher fichiers/dossiers</span>
            </button>
            <button class="cmd-btn" data-cmd="grep">
                <span class="cmd-btn__name">grep</span>
                <span class="cmd-btn__desc">Recherche de motifs</span>
            </button>
            <button class="cmd-btn" data-cmd="locate">
                <span class="cmd-btn__name">locate</span>
                <span class="cmd-btn__desc">Index rapide de fichiers</span>
            </button>

            <div class="cmd-sidebar__section">Traitement texte</div>
            <button class="cmd-btn" data-cmd="sed">
                <span class="cmd-btn__name">sed</span>
                <span class="cmd-btn__desc">Édition de flux</span>
            </button>
            <button class="cmd-btn" data-cmd="awk">
                <span class="cmd-btn__name">awk</span>
                <span class="cmd-btn__desc">Traitement de colonnes</span>
            </button>
            <button class="cmd-btn" data-cmd="cut">
                <span class="cmd-btn__name">cut</span>
                <span class="cmd-btn__desc">Extraire des champs</span>
            </button>
            <button class="cmd-btn" data-cmd="sort">
                <span class="cmd-btn__name">sort</span>
                <span class="cmd-btn__desc">Trier des lignes</span>
            </button>

            <div class="cmd-sidebar__section">Réseau</div>
            <button class="cmd-btn" data-cmd="curl">
                <span class="cmd-btn__name">curl</span>
                <span class="cmd-btn__desc">Requêtes HTTP</span>
            </button>
            <button class="cmd-btn" data-cmd="wget">
                <span class="cmd-btn__name">wget</span>
                <span class="cmd-btn__desc">Téléchargement</span>
            </button>
            <button class="cmd-btn" data-cmd="ssh">
                <span class="cmd-btn__name">ssh</span>
                <span class="cmd-btn__desc">Connexion distante</span>
            </button>
            <button class="cmd-btn" data-cmd="rsync">
                <span class="cmd-btn__name">rsync</span>
                <span class="cmd-btn__desc">Synchronisation</span>
            </button>

            <div class="cmd-sidebar__section">Système</div>
            <button class="cmd-btn" data-cmd="chmod">
                <span class="cmd-btn__name">chmod</span>
                <span class="cmd-btn__desc">Permissions</span>
            </button>
            <button class="cmd-btn" data-cmd="tar">
                <span class="cmd-btn__name">tar</span>
                <span class="cmd-btn__desc">Archives</span>
            </button>
            <button class="cmd-btn" data-cmd="ps">
                <span class="cmd-btn__name">ps</span>
                <span class="cmd-btn__desc">Processus</span>
            </button>
            <button class="cmd-btn" data-cmd="cron">
                <span class="cmd-btn__name">cron</span>
                <span class="cmd-btn__desc">Tâches planifiées</span>
            </button>
        </nav>

        <!-- Center -->
        <div class="center">
            <div class="options-area">
                <div class="options-area__title" id="opts-title">Options — find</div>
                <div id="opts-container"></div>
            </div>

            <!-- Terminal -->
            <div class="terminal">
                <div class="terminal-bar">
                    <div class="t-dots">
                        <span class="t-dot t-dot-r"></span>
                        <span class="t-dot t-dot-y"></span>
                        <span class="t-dot t-dot-g"></span>
                    </div>
                    <span class="t-label">lab'o noir — bash terminal</span>
                </div>
                <div class="terminal-body" id="terminal-body">
                    <span class="prompt">dgoth@labonoiр:~$ </span><span class="cmd-text" id="cmd-output"></span><span class="cursor"></span>
                </div>
                <div class="terminal-actions">
                    <button class="btn btn--primary" id="btn-generate">⚡ Générer</button>
                    <button class="btn" id="btn-copy">📋 Copier</button>
                    <button class="btn" id="btn-clear">✕ Effacer</button>
                </div>
            </div>
        </div>

        <!-- Right panel -->
        <aside class="right-panel">
            <div class="explain-area">
                <div class="explain-title">📖 Explication</div>
                <div id="explain-content">
                    <div class="explain-card">
                        <div class="explain-card__token">Astuce</div>
                        Génère ta commande et chaque partie sera expliquée ici.
                    </div>
                </div>
            </div>
            <div class="history-area">
                <div class="history-title">🕘 Historique</div>
                <div id="history-list">
                    <div class="history-empty">Aucune commande générée.</div>
                </div>
            </div>
        </aside>

    </div>
</div>

<script>
(() => {

// ── Commandes config ──────────────────────────────────────────
const COMMANDS = {

    find: {
        label: 'find — Recherche de fichiers',
        build: (v) => {
            let cmd = 'find ' + (v.dir || '.');
            if (v.name)  cmd += ` -name "${v.name}"`;
            if (v.type)  cmd += ` -type ${v.type}`;
            if (v.maxd)  cmd += ` -maxdepth ${v.maxd}`;
            if (v.size)  cmd += ` -size ${v.size}`;
            if (v.newer) cmd += ` -newer ${v.newer}`;
            if (v.exec)  cmd += ` -exec ${v.exec} {} \\;`;
            if (v.flags?.includes('-iname'))  cmd = cmd.replace('-name', '-iname');
            if (v.flags?.includes('-empty'))  cmd += ' -empty';
            if (v.flags?.includes('-delete')) cmd += ' -delete';
            return cmd;
        },
        explain: (v) => {
            const parts = [];
            parts.push({ token: 'find', desc: 'Parcourt l\'arborescence de fichiers récursivement.' });
            parts.push({ token: v.dir || '.', desc: 'Répertoire de départ. `.` = dossier courant.' });
            if (v.name)  parts.push({ token: `-name "${v.name}"`, desc: 'Filtre par nom (sensible à la casse). Supporte les wildcards : `*`, `?`.' });
            if (v.flags?.includes('-iname')) parts.push({ token: '-iname', desc: 'Remplacement de -name : insensible à la casse.' });
            if (v.type)  parts.push({ token: `-type ${v.type}`, desc: v.type === 'f' ? 'Fichiers réguliers uniquement.' : v.type === 'd' ? 'Dossiers uniquement.' : 'Liens symboliques uniquement.' });
            if (v.maxd)  parts.push({ token: `-maxdepth ${v.maxd}`, desc: `Limite la profondeur de recherche à ${v.maxd} niveau(x).` });
            if (v.size)  parts.push({ token: `-size ${v.size}`, desc: 'Filtre par taille. Ex: +10M = plus de 10 Mo, -1k = moins de 1 Ko.' });
            if (v.exec)  parts.push({ token: `-exec ... {} \\;`, desc: 'Exécute une commande sur chaque résultat. `{}` est remplacé par le fichier trouvé.' });
            if (v.flags?.includes('-empty'))  parts.push({ token: '-empty', desc: 'Filtre les fichiers/dossiers vides.' });
            if (v.flags?.includes('-delete')) parts.push({ token: '-delete', desc: '⚠️ Supprime les fichiers trouvés. Utiliser avec précaution !' });
            return parts;
        },
        render: () => `
            <div class="opts-grid">
                <div class="field">
                    <label>Répertoire de départ</label>
                    <input type="text" data-key="dir" placeholder="/var/log ou . (courant)">
                </div>
                <div class="field">
                    <label>Nom de fichier <span class="badge">wildcard</span></label>
                    <input type="text" data-key="name" placeholder="*.log ou mon-fichier.txt">
                </div>
                <div class="field">
                    <label>Type</label>
                    <select data-key="type">
                        <option value="">Tous</option>
                        <option value="f">Fichier (f)</option>
                        <option value="d">Dossier (d)</option>
                        <option value="l">Lien symbolique (l)</option>
                    </select>
                </div>
                <div class="field">
                    <label>Profondeur max</label>
                    <input type="number" data-key="maxd" placeholder="Ex: 2" min="1" max="99">
                </div>
                <div class="field">
                    <label>Taille</label>
                    <input type="text" data-key="size" placeholder="+10M, -1k, 512c">
                </div>
                <div class="field">
                    <label>Exécuter (exec)</label>
                    <input type="text" data-key="exec" placeholder="rm, cp /backup, chmod 644">
                </div>
            </div>
            <div class="field" style="margin-top:.6rem">
                <label>Options supplémentaires</label>
                <div class="checkbox-group" data-flags="flags">
                    <label class="check-pill"><input type="checkbox" value="-iname"> -iname</label>
                    <label class="check-pill"><input type="checkbox" value="-empty"> -empty</label>
                    <label class="check-pill"><input type="checkbox" value="-delete"> -delete ⚠️</label>
                </div>
            </div>`
    },

    grep: {
        label: 'grep — Recherche de motifs',
        build: (v) => {
            let flags = '';
            if (v.flags?.includes('-i')) flags += 'i';
            if (v.flags?.includes('-r')) flags += 'r';
            if (v.flags?.includes('-n')) flags += 'n';
            if (v.flags?.includes('-v')) flags += 'v';
            if (v.flags?.includes('-l')) flags += 'l';
            if (v.flags?.includes('-c')) flags += 'c';
            if (v.flags?.includes('-w')) flags += 'w';
            const f = flags ? ` -${flags}` : '';
            const E = v.regex ? ' -E' : '';
            let cmd = `grep${f}${E}`;
            if (v.ctx_before) cmd += ` -B ${v.ctx_before}`;
            if (v.ctx_after)  cmd += ` -A ${v.ctx_after}`;
            if (v.pattern) cmd += ` "${v.pattern}"`;
            if (v.file)    cmd += ` ${v.file}`;
            return cmd;
        },
        explain: (v) => {
            const parts = [];
            parts.push({ token: 'grep', desc: 'Recherche des lignes correspondant à un motif dans des fichiers ou l\'entrée standard.' });
            if (v.flags?.includes('-i')) parts.push({ token: '-i', desc: 'Insensible à la casse : "ERROR" matche "error", "Error"...' });
            if (v.flags?.includes('-r')) parts.push({ token: '-r', desc: 'Récursif : parcourt tous les fichiers des sous-dossiers.' });
            if (v.flags?.includes('-n')) parts.push({ token: '-n', desc: 'Affiche le numéro de ligne devant chaque résultat.' });
            if (v.flags?.includes('-v')) parts.push({ token: '-v', desc: 'Inverse la sélection : affiche les lignes qui NE correspondent PAS.' });
            if (v.flags?.includes('-l')) parts.push({ token: '-l', desc: 'Affiche uniquement les noms de fichiers contenant le motif.' });
            if (v.flags?.includes('-c')) parts.push({ token: '-c', desc: 'Compte le nombre de lignes correspondantes par fichier.' });
            if (v.flags?.includes('-w')) parts.push({ token: '-w', desc: 'Correspond uniquement à des mots entiers (pas de sous-chaînes).' });
            if (v.regex) parts.push({ token: '-E', desc: 'Active les expressions régulières étendues (ERE) : +, ?, |, {n,m}...' });
            if (v.pattern) parts.push({ token: `"${v.pattern}"`, desc: 'Le motif de recherche. Avec -E tu peux écrire des regex complexes.' });
            if (v.file)    parts.push({ token: v.file, desc: 'Fichier(s) cibles. Utilise `*` pour tous les fichiers du dossier.' });
            return parts;
        },
        render: () => `
            <div class="opts-grid">
                <div class="field">
                    <label>Motif de recherche <span class="badge">regex ok</span></label>
                    <input type="text" data-key="pattern" placeholder="error|warning ou ^DEBUG">
                </div>
                <div class="field">
                    <label>Fichier(s)</label>
                    <input type="text" data-key="file" placeholder="app.log ou *.log ou /var/log/">
                </div>
                <div class="field">
                    <label>Contexte avant (lignes)</label>
                    <input type="number" data-key="ctx_before" placeholder="0" min="0">
                </div>
                <div class="field">
                    <label>Contexte après (lignes)</label>
                    <input type="number" data-key="ctx_after" placeholder="0" min="0">
                </div>
            </div>
            <div class="field" style="margin-top:.6rem">
                <label>Flags</label>
                <div class="checkbox-group" data-flags="flags">
                    <label class="check-pill"><input type="checkbox" value="-i"> -i casse</label>
                    <label class="check-pill"><input type="checkbox" value="-r"> -r récursif</label>
                    <label class="check-pill"><input type="checkbox" value="-n"> -n num. ligne</label>
                    <label class="check-pill"><input type="checkbox" value="-v"> -v inverse</label>
                    <label class="check-pill"><input type="checkbox" value="-l"> -l fichiers</label>
                    <label class="check-pill"><input type="checkbox" value="-c"> -c compter</label>
                    <label class="check-pill"><input type="checkbox" value="-w"> -w mot entier</label>
                </div>
            </div>
            <div class="field" style="margin-top:.4rem">
                <label class="check-pill" style="display:inline-flex">
                    <input type="checkbox" data-key="regex"> Activer regex étendue (-E)
                </label>
            </div>`
    },

    sed: {
        label: 'sed — Éditeur de flux',
        build: (v) => {
            let cmd = 'sed';
            if (v.flags?.includes('-i'))    cmd += ' -i';
            if (v.flags?.includes('-i.bak')) cmd += '.bak';
            if (v.flags?.includes('-n'))    cmd += ' -n';
            if (v.script) cmd += ` '${v.script}'`;
            if (v.file)   cmd += ` ${v.file}`;
            return cmd;
        },
        explain: (v) => {
            const parts = [];
            parts.push({ token: 'sed', desc: 'Stream EDitor — lit ligne par ligne et applique des transformations.' });
            if (v.flags?.includes('-i')) parts.push({ token: '-i', desc: 'Modifie le fichier en place (in-place). Sans ça, affiche seulement le résultat.' });
            if (v.flags?.includes('-i.bak')) parts.push({ token: '.bak', desc: 'Crée une sauvegarde .bak avant de modifier.' });
            if (v.flags?.includes('-n')) parts.push({ token: '-n', desc: 'Supprime l\'affichage automatique. Utilise `p` dans le script pour afficher.' });
            if (v.script) parts.push({ token: `'${v.script}'`, desc: 's/motif/remplacement/ = substitution. g = toutes les occurrences. d = supprimer. p = afficher.' });
            if (v.file)   parts.push({ token: v.file, desc: 'Fichier à traiter. Sans fichier, sed lit stdin.' });
            return parts;
        },
        render: () => `
            <div class="opts-grid single">
                <div class="field">
                    <label>Script sed <span class="badge">exemples ci-dessous</span></label>
                    <input type="text" data-key="script" placeholder="s/ancien/nouveau/g">
                </div>
                <div class="field">
                    <label>Fichier</label>
                    <input type="text" data-key="file" placeholder="/etc/config.conf">
                </div>
            </div>
            <div class="field" style="margin-top:.6rem">
                <label>Options</label>
                <div class="checkbox-group" data-flags="flags">
                    <label class="check-pill"><input type="checkbox" value="-i"> -i in-place</label>
                    <label class="check-pill"><input type="checkbox" value="-i.bak"> backup .bak</label>
                    <label class="check-pill"><input type="checkbox" value="-n"> -n silencieux</label>
                </div>
            </div>
            <div style="margin-top:.8rem; font-size:.75rem; color:var(--text-muted); line-height:1.8; font-family:var(--mono);">
                <span style="color:var(--accent)">Exemples :</span><br>
                s/foo/bar/g &nbsp;&nbsp;&nbsp; → remplacer toutes les occurrences<br>
                /^#/d &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; → supprimer les commentaires<br>
                1,5d &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; → supprimer lignes 1 à 5<br>
                /motif/p &nbsp;&nbsp;&nbsp;&nbsp; → afficher les lignes matchées (avec -n)
            </div>`
    },

    awk: {
        label: 'awk — Traitement de colonnes',
        build: (v) => {
            let cmd = 'awk';
            if (v.sep) cmd += ` -F '${v.sep}'`;
            if (v.script) cmd += ` '${v.script}'`;
            if (v.file)   cmd += ` ${v.file}`;
            return cmd;
        },
        explain: (v) => {
            const parts = [];
            parts.push({ token: 'awk', desc: 'Langage de traitement de données orienté lignes/colonnes. Idéal pour les fichiers CSV ou logs structurés.' });
            if (v.sep)    parts.push({ token: `-F '${v.sep}'`, desc: `Définit le séparateur de champ. Ex: -F ',' pour CSV, -F ':' pour /etc/passwd.` });
            if (v.script) parts.push({ token: `'${v.script}'`, desc: '$1, $2... = champs. $0 = ligne entière. NR = numéro de ligne. NF = nombre de champs.' });
            if (v.file)   parts.push({ token: v.file, desc: 'Fichier source. Peut être remplacé par un pipe : `commande | awk ...`' });
            return parts;
        },
        render: () => `
            <div class="opts-grid single">
                <div class="field">
                    <label>Séparateur de champ</label>
                    <input type="text" data-key="sep" placeholder=": ou , ou \\t (tabulation)">
                </div>
                <div class="field">
                    <label>Script awk</label>
                    <input type="text" data-key="script" placeholder="{print $1, $3}">
                </div>
                <div class="field">
                    <label>Fichier</label>
                    <input type="text" data-key="file" placeholder="/etc/passwd ou data.csv">
                </div>
            </div>
            <div style="margin-top:.8rem; font-size:.75rem; color:var(--text-muted); line-height:1.8; font-family:var(--mono);">
                <span style="color:var(--accent)">Exemples :</span><br>
                {print $1} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; → 1ère colonne<br>
                NR>1 {print $2,$5} &nbsp;&nbsp;&nbsp; → col 2 et 5, skip header<br>
                {sum+=$3} END{print sum} → somme colonne 3<br>
                /error/ {print NR, $0} &nbsp; → lignes avec "error"
            </div>`
    },

    curl: {
        label: 'curl — Requêtes HTTP',
        build: (v) => {
            let cmd = 'curl';
            if (v.method && v.method !== 'GET') cmd += ` -X ${v.method}`;
            if (v.flags?.includes('-s'))   cmd += ' -s';
            if (v.flags?.includes('-v'))   cmd += ' -v';
            if (v.flags?.includes('-L'))   cmd += ' -L';
            if (v.flags?.includes('-k'))   cmd += ' -k';
            if (v.flags?.includes('-o'))   cmd += ' -o output.txt';
            if (v.header) cmd += ` -H "${v.header}"`;
            if (v.data)   cmd += ` -d '${v.data}'`;
            if (v.url)    cmd += ` "${v.url}"`;
            return cmd;
        },
        explain: (v) => {
            const parts = [];
            parts.push({ token: 'curl', desc: 'Client HTTP en ligne de commande. Supporte HTTP, HTTPS, FTP, et bien d\'autres protocoles.' });
            if (v.method && v.method !== 'GET') parts.push({ token: `-X ${v.method}`, desc: `Méthode HTTP : ${v.method}. GET est la méthode par défaut.` });
            if (v.flags?.includes('-s')) parts.push({ token: '-s', desc: 'Mode silencieux : supprime la barre de progression et les messages d\'erreur.' });
            if (v.flags?.includes('-v')) parts.push({ token: '-v', desc: 'Mode verbose : affiche les headers de requête et réponse. Utile pour débugger.' });
            if (v.flags?.includes('-L')) parts.push({ token: '-L', desc: 'Suit les redirections automatiquement (301, 302...).' });
            if (v.flags?.includes('-k')) parts.push({ token: '-k', desc: '⚠️ Ignore les erreurs de certificat SSL. À éviter en production.' });
            if (v.header) parts.push({ token: `-H "${v.header}"`, desc: 'Ajoute un header HTTP. Ex: Authorization: Bearer token, Content-Type: application/json' });
            if (v.data)   parts.push({ token: `-d '${v.data}'`, desc: 'Corps de la requête (body). Pour les POST/PUT avec données JSON ou form.' });
            if (v.url)    parts.push({ token: `"${v.url}"`, desc: 'URL cible. Les guillemets permettent les URLs avec des caractères spéciaux.' });
            return parts;
        },
        render: () => `
            <div class="opts-grid">
                <div class="field">
                    <label>URL</label>
                    <input type="text" data-key="url" placeholder="https://api.example.com/endpoint">
                </div>
                <div class="field">
                    <label>Méthode HTTP</label>
                    <select data-key="method">
                        <option value="GET">GET</option>
                        <option value="POST">POST</option>
                        <option value="PUT">PUT</option>
                        <option value="PATCH">PATCH</option>
                        <option value="DELETE">DELETE</option>
                    </select>
                </div>
                <div class="field">
                    <label>Header <span class="badge">-H</span></label>
                    <input type="text" data-key="header" placeholder="Authorization: Bearer TOKEN">
                </div>
                <div class="field">
                    <label>Data body <span class="badge">-d</span></label>
                    <input type="text" data-key="data" placeholder='{"key":"value"}'>
                </div>
            </div>
            <div class="field" style="margin-top:.6rem">
                <label>Options</label>
                <div class="checkbox-group" data-flags="flags">
                    <label class="check-pill"><input type="checkbox" value="-s"> -s silencieux</label>
                    <label class="check-pill"><input type="checkbox" value="-v"> -v verbose</label>
                    <label class="check-pill"><input type="checkbox" value="-L"> -L redirections</label>
                    <label class="check-pill"><input type="checkbox" value="-k"> -k no SSL ⚠️</label>
                    <label class="check-pill"><input type="checkbox" value="-o"> -o fichier</label>
                </div>
            </div>`
    },

    wget: {
        label: 'wget — Téléchargement',
        build: (v) => {
            let cmd = 'wget';
            if (v.output)  cmd += ` -O ${v.output}`;
            if (v.flags?.includes('-q'))  cmd += ' -q';
            if (v.flags?.includes('-c'))  cmd += ' -c';
            if (v.flags?.includes('-r'))  cmd += ' -r';
            if (v.flags?.includes('-np')) cmd += ' -np';
            if (v.tries)   cmd += ` --tries=${v.tries}`;
            if (v.url)     cmd += ` "${v.url}"`;
            return cmd;
        },
        explain: (v) => {
            const parts = [];
            parts.push({ token: 'wget', desc: 'Téléchargeur en ligne de commande. Supporte HTTP, HTTPS, FTP. Idéal pour les scripts.' });
            if (v.output)  parts.push({ token: `-O ${v.output}`, desc: 'Sauvegarde sous ce nom de fichier au lieu du nom par défaut.' });
            if (v.flags?.includes('-q'))  parts.push({ token: '-q', desc: 'Silencieux : n\'affiche que les erreurs.' });
            if (v.flags?.includes('-c'))  parts.push({ token: '-c', desc: 'Reprend un téléchargement interrompu.' });
            if (v.flags?.includes('-r'))  parts.push({ token: '-r', desc: 'Téléchargement récursif (spider). Utile pour mirorer un site.' });
            if (v.tries)   parts.push({ token: `--tries=${v.tries}`, desc: `Nombre de tentatives en cas d'échec.` });
            return parts;
        },
        render: () => `
            <div class="opts-grid">
                <div class="field">
                    <label>URL</label>
                    <input type="text" data-key="url" placeholder="https://example.com/file.zip">
                </div>
                <div class="field">
                    <label>Fichier de sortie (-O)</label>
                    <input type="text" data-key="output" placeholder="archive.zip">
                </div>
                <div class="field">
                    <label>Tentatives (--tries)</label>
                    <input type="number" data-key="tries" placeholder="3" min="1">
                </div>
            </div>
            <div class="field" style="margin-top:.6rem">
                <label>Options</label>
                <div class="checkbox-group" data-flags="flags">
                    <label class="check-pill"><input type="checkbox" value="-q"> -q silencieux</label>
                    <label class="check-pill"><input type="checkbox" value="-c"> -c reprendre</label>
                    <label class="check-pill"><input type="checkbox" value="-r"> -r récursif</label>
                    <label class="check-pill"><input type="checkbox" value="-np"> -np no-parent</label>
                </div>
            </div>`
    },

    ssh: {
        label: 'ssh — Connexion distante',
        build: (v) => {
            let cmd = 'ssh';
            if (v.port && v.port !== '22') cmd += ` -p ${v.port}`;
            if (v.key)    cmd += ` -i ${v.key}`;
            if (v.flags?.includes('-v'))  cmd += ' -v';
            if (v.flags?.includes('-N'))  cmd += ' -N';
            if (v.tunnel) cmd += ` -L ${v.tunnel}`;
            const host = v.user ? `${v.user}@${v.host||'host'}` : (v.host || 'host');
            cmd += ` ${host}`;
            if (v.remote_cmd) cmd += ` "${v.remote_cmd}"`;
            return cmd;
        },
        explain: (v) => {
            const parts = [];
            parts.push({ token: 'ssh', desc: 'Secure Shell — connexion chiffrée à un serveur distant. Protocole standard pour l\'administration.' });
            if (v.port && v.port !== '22') parts.push({ token: `-p ${v.port}`, desc: `Port SSH personnalisé. Le port par défaut est 22.` });
            if (v.key)    parts.push({ token: `-i ${v.key}`, desc: 'Clé privée à utiliser pour l\'authentification (identité).' });
            if (v.tunnel) parts.push({ token: `-L ${v.tunnel}`, desc: 'Tunnel local : redirige port_local:host_distant:port_distant.' });
            if (v.remote_cmd) parts.push({ token: `"${v.remote_cmd}"`, desc: 'Commande à exécuter sur le serveur distant (non-interactif).' });
            return parts;
        },
        render: () => `
            <div class="opts-grid">
                <div class="field">
                    <label>Utilisateur</label>
                    <input type="text" data-key="user" placeholder="root ou deploy">
                </div>
                <div class="field">
                    <label>Hôte / IP</label>
                    <input type="text" data-key="host" placeholder="192.168.1.1 ou server.com">
                </div>
                <div class="field">
                    <label>Port</label>
                    <input type="number" data-key="port" placeholder="22" min="1" max="65535">
                </div>
                <div class="field">
                    <label>Clé privée (-i)</label>
                    <input type="text" data-key="key" placeholder="~/.ssh/id_rsa">
                </div>
                <div class="field">
                    <label>Tunnel local (-L)</label>
                    <input type="text" data-key="tunnel" placeholder="8080:localhost:80">
                </div>
                <div class="field">
                    <label>Commande distante</label>
                    <input type="text" data-key="remote_cmd" placeholder="ls -la /var/www">
                </div>
            </div>
            <div class="field" style="margin-top:.6rem">
                <label>Options</label>
                <div class="checkbox-group" data-flags="flags">
                    <label class="check-pill"><input type="checkbox" value="-v"> -v verbose</label>
                    <label class="check-pill"><input type="checkbox" value="-N"> -N tunnel only</label>
                </div>
            </div>`
    },

    rsync: {
        label: 'rsync — Synchronisation',
        build: (v) => {
            let flags = '-a';
            if (v.flags?.includes('-v'))     flags += 'v';
            if (v.flags?.includes('-z'))     flags += 'z';
            if (v.flags?.includes('-h'))     flags += 'h';
            let cmd = `rsync ${flags}`;
            if (v.flags?.includes('--delete'))   cmd += ' --delete';
            if (v.flags?.includes('--dry-run'))  cmd += ' --dry-run';
            if (v.flags?.includes('--progress')) cmd += ' --progress';
            if (v.exclude) cmd += ` --exclude="${v.exclude}"`;
            if (v.bwlimit) cmd += ` --bwlimit=${v.bwlimit}`;
            if (v.src)  cmd += ` ${v.src}`;
            if (v.dst)  cmd += ` ${v.dst}`;
            return cmd;
        },
        explain: (v) => {
            const parts = [];
            parts.push({ token: 'rsync', desc: 'Synchronisation de fichiers locale ou distante. Ne transfère que les différences.' });
            parts.push({ token: '-a', desc: 'Mode archive : préserve permissions, timestamps, liens symboliques, propriétaire.' });
            if (v.flags?.includes('-v'))  parts.push({ token: '-v', desc: 'Verbose : liste les fichiers transférés.' });
            if (v.flags?.includes('-z'))  parts.push({ token: '-z', desc: 'Compresse les données pendant le transfert.' });
            if (v.flags?.includes('--delete')) parts.push({ token: '--delete', desc: '⚠️ Supprime dans la destination les fichiers absents de la source.' });
            if (v.flags?.includes('--dry-run')) parts.push({ token: '--dry-run', desc: 'Simulation : montre ce qui serait fait sans rien modifier.' });
            if (v.exclude) parts.push({ token: `--exclude="${v.exclude}"`, desc: 'Exclut les fichiers/dossiers correspondants.' });
            return parts;
        },
        render: () => `
            <div class="opts-grid">
                <div class="field">
                    <label>Source</label>
                    <input type="text" data-key="src" placeholder="/local/path/ ou user@host:/path/">
                </div>
                <div class="field">
                    <label>Destination</label>
                    <input type="text" data-key="dst" placeholder="/backup/ ou user@host:/backup/">
                </div>
                <div class="field">
                    <label>Exclure</label>
                    <input type="text" data-key="exclude" placeholder="*.log ou node_modules">
                </div>
                <div class="field">
                    <label>Limite bande passante (KB/s)</label>
                    <input type="number" data-key="bwlimit" placeholder="1000">
                </div>
            </div>
            <div class="field" style="margin-top:.6rem">
                <label>Options</label>
                <div class="checkbox-group" data-flags="flags">
                    <label class="check-pill"><input type="checkbox" value="-v"> -v verbose</label>
                    <label class="check-pill"><input type="checkbox" value="-z"> -z compress</label>
                    <label class="check-pill"><input type="checkbox" value="-h"> -h human</label>
                    <label class="check-pill"><input type="checkbox" value="--progress"> --progress</label>
                    <label class="check-pill"><input type="checkbox" value="--delete"> --delete ⚠️</label>
                    <label class="check-pill"><input type="checkbox" value="--dry-run"> --dry-run</label>
                </div>
            </div>`
    },

    chmod: {
        label: 'chmod — Permissions',
        build: (v) => {
            let cmd = 'chmod';
            if (v.flags?.includes('-R')) cmd += ' -R';
            if (v.flags?.includes('-v')) cmd += ' -v';
            const mode = v.mode_type === 'octal' ? v.octal : v.symbolic;
            if (mode) cmd += ` ${mode}`;
            if (v.file) cmd += ` ${v.file}`;
            return cmd;
        },
        explain: (v) => {
            const parts = [];
            parts.push({ token: 'chmod', desc: 'CHange MODe — modifie les permissions d\'accès aux fichiers et dossiers.' });
            if (v.flags?.includes('-R')) parts.push({ token: '-R', desc: 'Récursif : applique les permissions à tous les fichiers du dossier.' });
            if (v.mode_type === 'octal' && v.octal) {
                parts.push({ token: v.octal, desc: `Notation octale. Chaque chiffre = propriétaire/groupe/autres. 7=rwx, 6=rw-, 5=r-x, 4=r--, 0=---. ${v.octal}=${v.octal.split('').map(n=>['---','--x','-w-','-wx','r--','r-x','rw-','rwx'][n]).join('/')}` });
            }
            if (v.file) parts.push({ token: v.file, desc: 'Fichier ou dossier cible.' });
            return parts;
        },
        render: () => `
            <div class="opts-grid">
                <div class="field">
                    <label>Fichier / Dossier</label>
                    <input type="text" data-key="file" placeholder="/var/www/html ou script.sh">
                </div>
                <div class="field">
                    <label>Type de notation</label>
                    <select data-key="mode_type">
                        <option value="octal">Octale (755)</option>
                        <option value="symbolic">Symbolique (u+x)</option>
                    </select>
                </div>
                <div class="field">
                    <label>Mode octal</label>
                    <input type="text" data-key="octal" placeholder="755 ou 644 ou 600">
                </div>
                <div class="field">
                    <label>Mode symbolique</label>
                    <input type="text" data-key="symbolic" placeholder="u+x ou go-w ou a+r">
                </div>
            </div>
            <div class="field" style="margin-top:.6rem">
                <div class="checkbox-group" data-flags="flags">
                    <label class="check-pill"><input type="checkbox" value="-R"> -R récursif</label>
                    <label class="check-pill"><input type="checkbox" value="-v"> -v verbose</label>
                </div>
            </div>
            <div style="margin-top:.8rem; font-size:.75rem; color:var(--text-muted); line-height:1.8; font-family:var(--mono);">
                <span style="color:var(--accent)">Référence octale :</span><br>
                777 = rwxrwxrwx &nbsp; 755 = rwxr-xr-x<br>
                644 = rw-r--r-- &nbsp; 600 = rw-------<br>
                700 = rwx------ &nbsp; 400 = r--------
            </div>`
    },

    tar: {
        label: 'tar — Archives',
        build: (v) => {
            let flags = v.action || 'c';
            if (v.flags?.includes('-z')) flags += 'z';
            if (v.flags?.includes('-j')) flags += 'j';
            if (v.flags?.includes('-J')) flags += 'J';
            flags += 'v';
            flags += 'f';
            let cmd = `tar -${flags}`;
            if (v.archive) cmd += ` ${v.archive}`;
            if (v.target)  cmd += ` ${v.target}`;
            return cmd;
        },
        explain: (v) => {
            const parts = [];
            parts.push({ token: 'tar', desc: 'Tape ARchive — crée, extrait et liste des archives. Souvent combiné avec gzip/bzip2.' });
            const acts = { c:'Crée une nouvelle archive.', x:'Extrait le contenu de l\'archive.', t:'Liste le contenu sans extraire.' };
            if (v.action) parts.push({ token: `-${v.action}`, desc: acts[v.action] || '' });
            if (v.flags?.includes('-z')) parts.push({ token: '-z', desc: 'Compression gzip (.tar.gz / .tgz). Bon compromis vitesse/taille.' });
            if (v.flags?.includes('-j')) parts.push({ token: '-j', desc: 'Compression bzip2 (.tar.bz2). Plus lent mais meilleur ratio.' });
            if (v.flags?.includes('-J')) parts.push({ token: '-J', desc: 'Compression xz (.tar.xz). Meilleure compression, plus lent.' });
            parts.push({ token: '-v', desc: 'Verbose : liste les fichiers traités.' });
            parts.push({ token: '-f', desc: 'Spécifie le nom du fichier archive (toujours en dernier).' });
            return parts;
        },
        render: () => `
            <div class="opts-grid">
                <div class="field">
                    <label>Action</label>
                    <select data-key="action">
                        <option value="c">Créer (-c)</option>
                        <option value="x">Extraire (-x)</option>
                        <option value="t">Lister (-t)</option>
                    </select>
                </div>
                <div class="field">
                    <label>Fichier archive</label>
                    <input type="text" data-key="archive" placeholder="archive.tar.gz">
                </div>
                <div class="field">
                    <label>Source / Destination</label>
                    <input type="text" data-key="target" placeholder="/dossier/ ou fichier.txt">
                </div>
            </div>
            <div class="field" style="margin-top:.6rem">
                <label>Compression</label>
                <div class="checkbox-group" data-flags="flags">
                    <label class="check-pill"><input type="checkbox" value="-z"> -z gzip</label>
                    <label class="check-pill"><input type="checkbox" value="-j"> -j bzip2</label>
                    <label class="check-pill"><input type="checkbox" value="-J"> -J xz</label>
                </div>
            </div>`
    },

    ps: {
        label: 'ps — Processus',
        build: (v) => {
            let cmd = 'ps';
            let flags = '';
            if (v.flags?.includes('a'))  flags += 'a';
            if (v.flags?.includes('u'))  flags += 'u';
            if (v.flags?.includes('x'))  flags += 'x';
            if (v.flags?.includes('e'))  flags += 'e';
            if (flags) cmd += ` ${flags}`;
            if (v.pipe_grep) cmd += ` | grep "${v.pipe_grep}"`;
            if (v.pipe_sort) cmd += ` | sort -k ${v.pipe_sort} -rn | head -20`;
            return cmd;
        },
        explain: (v) => {
            const parts = [];
            parts.push({ token: 'ps', desc: 'Process Status — affiche les processus en cours d\'exécution.' });
            if (v.flags?.includes('a'))  parts.push({ token: 'a', desc: 'Affiche les processus de tous les utilisateurs.' });
            if (v.flags?.includes('u'))  parts.push({ token: 'u', desc: 'Format orienté utilisateur : CPU%, MEM%, commande.' });
            if (v.flags?.includes('x'))  parts.push({ token: 'x', desc: 'Inclut les processus sans terminal de contrôle (démons).' });
            if (v.pipe_grep) parts.push({ token: `| grep "${v.pipe_grep}"`, desc: 'Filtre les résultats pour ne garder que les lignes correspondantes.' });
            return parts;
        },
        render: () => `
            <div class="opts-grid">
                <div class="field">
                    <label>Filtrer (grep)</label>
                    <input type="text" data-key="pipe_grep" placeholder="nginx ou python ou port">
                </div>
                <div class="field">
                    <label>Trier par colonne (sort -k)</label>
                    <select data-key="pipe_sort">
                        <option value="">Pas de tri</option>
                        <option value="3">CPU% (col 3)</option>
                        <option value="4">MEM% (col 4)</option>
                    </select>
                </div>
            </div>
            <div class="field" style="margin-top:.6rem">
                <label>Flags</label>
                <div class="checkbox-group" data-flags="flags">
                    <label class="check-pill"><input type="checkbox" value="a" checked> a tous users</label>
                    <label class="check-pill"><input type="checkbox" value="u" checked> u détaillé</label>
                    <label class="check-pill"><input type="checkbox" value="x" checked> x démons</label>
                    <label class="check-pill"><input type="checkbox" value="e"> e tous procs</label>
                </div>
            </div>`
    },

    cron: {
        label: 'cron — Tâches planifiées',
        build: (v) => {
            const m  = v.min  || '*';
            const h  = v.hour || '*';
            const d  = v.dom  || '*';
            const mo = v.mon  || '*';
            const dw = v.dow  || '*';
            const cmd = v.cmd || '/path/to/script.sh';
            return `${m} ${h} ${d} ${mo} ${dw} ${cmd}`;
        },
        explain: (v) => {
            const parts = [];
            parts.push({ token: 'crontab', desc: 'Format : min heure jour-du-mois mois jour-de-la-semaine commande. Ajoute avec : crontab -e' });
            parts.push({ token: v.min  || '*', desc: 'Minutes (0-59). * = toutes les minutes.' });
            parts.push({ token: v.hour || '*', desc: 'Heures (0-23). * = toutes les heures.' });
            parts.push({ token: v.dom  || '*', desc: 'Jour du mois (1-31). * = tous les jours.' });
            parts.push({ token: v.mon  || '*', desc: 'Mois (1-12). * = tous les mois.' });
            parts.push({ token: v.dow  || '*', desc: 'Jour de semaine (0-7, 0=dimanche). * = tous les jours.' });
            return parts;
        },
        render: () => `
            <div class="opts-grid">
                <div class="field"><label>Minutes</label><input type="text" data-key="min" placeholder="* ou 0 ou */5"></div>
                <div class="field"><label>Heures</label><input type="text" data-key="hour" placeholder="* ou 3 ou */2"></div>
                <div class="field"><label>Jour du mois</label><input type="text" data-key="dom" placeholder="* ou 1 ou 15"></div>
                <div class="field"><label>Mois</label><input type="text" data-key="mon" placeholder="* ou 1-6"></div>
                <div class="field"><label>Jour de semaine</label><input type="text" data-key="dow" placeholder="* ou 1-5 (lun-ven)"></div>
                <div class="field"><label>Commande</label><input type="text" data-key="cmd" placeholder="/usr/bin/python3 /scripts/backup.py"></div>
            </div>
            <div style="margin-top:.8rem; font-size:.75rem; color:var(--text-muted); line-height:1.8; font-family:var(--mono);">
                <span style="color:var(--accent)">Exemples courants :</span><br>
                0 2 * * * &nbsp;&nbsp;&nbsp;&nbsp; → tous les jours à 2h00<br>
                */15 * * * * &nbsp; → toutes les 15 minutes<br>
                0 0 * * 1 &nbsp;&nbsp;&nbsp; → chaque lundi à minuit<br>
                0 9 1 * * &nbsp;&nbsp;&nbsp; → le 1er du mois à 9h
            </div>`
    },

    locate: {
        label: 'locate — Index rapide',
        build: (v) => {
            let cmd = 'locate';
            if (v.flags?.includes('-i'))  cmd += ' -i';
            if (v.flags?.includes('-l'))  cmd += ` -l ${v.limit||10}`;
            if (v.flags?.includes('-r'))  cmd += ' -r';
            if (v.flags?.includes('-c'))  cmd += ' -c';
            if (v.pattern) cmd += ` "${v.pattern}"`;
            return cmd;
        },
        explain: (v) => [
            { token: 'locate', desc: 'Cherche dans une base de données d\'index (mise à jour par updatedb). Beaucoup plus rapide que find.' },
            ...(v.flags?.includes('-i') ? [{ token: '-i', desc: 'Insensible à la casse.' }] : []),
            ...(v.flags?.includes('-l') ? [{ token: `-l ${v.limit||10}`, desc: `Limite à ${v.limit||10} résultats.` }] : []),
            ...(v.flags?.includes('-r') ? [{ token: '-r', desc: 'Interprète le motif comme une expression régulière.' }] : []),
            ...(v.pattern ? [{ token: `"${v.pattern}"`, desc: 'Motif de recherche dans le chemin complet des fichiers.' }] : []),
        ],
        render: () => `
            <div class="opts-grid">
                <div class="field"><label>Motif</label><input type="text" data-key="pattern" placeholder="*.conf ou nginx ou id_rsa"></div>
                <div class="field"><label>Limite résultats</label><input type="number" data-key="limit" placeholder="10" min="1"></div>
            </div>
            <div class="field" style="margin-top:.6rem">
                <div class="checkbox-group" data-flags="flags">
                    <label class="check-pill"><input type="checkbox" value="-i"> -i casse</label>
                    <label class="check-pill"><input type="checkbox" value="-l"> -l limiter</label>
                    <label class="check-pill"><input type="checkbox" value="-r"> -r regex</label>
                    <label class="check-pill"><input type="checkbox" value="-c"> -c compter</label>
                </div>
            </div>`
    },

    cut: {
        label: 'cut — Extraire des champs',
        build: (v) => {
            let cmd = 'cut';
            if (v.sep)    cmd += ` -d '${v.sep}'`;
            if (v.fields) cmd += ` -f ${v.fields}`;
            if (v.chars)  cmd += ` -c ${v.chars}`;
            if (v.file)   cmd += ` ${v.file}`;
            return cmd;
        },
        explain: (v) => [
            { token: 'cut', desc: 'Extrait des colonnes ou des plages de caractères depuis des lignes de texte.' },
            ...(v.sep    ? [{ token: `-d '${v.sep}'`, desc: `Délimiteur de champ. Ex: ':' pour /etc/passwd, ',' pour CSV.` }] : []),
            ...(v.fields ? [{ token: `-f ${v.fields}`, desc: `Champs à extraire. Ex: 1,3 ou 1-4 ou 2-.` }] : []),
            ...(v.chars  ? [{ token: `-c ${v.chars}`, desc: `Caractères à extraire (positions). Ex: 1-10.` }] : []),
        ],
        render: () => `
            <div class="opts-grid">
                <div class="field"><label>Délimiteur</label><input type="text" data-key="sep" placeholder=": ou , ou \t"></div>
                <div class="field"><label>Champs (-f)</label><input type="text" data-key="fields" placeholder="1,3 ou 1-4 ou 2-"></div>
                <div class="field"><label>Caractères (-c)</label><input type="text" data-key="chars" placeholder="1-10 ou 5,10,15"></div>
                <div class="field"><label>Fichier</label><input type="text" data-key="file" placeholder="/etc/passwd ou data.csv"></div>
            </div>`
    },

    sort: {
        label: 'sort — Trier des lignes',
        build: (v) => {
            let cmd = 'sort';
            if (v.flags?.includes('-r')) cmd += ' -r';
            if (v.flags?.includes('-n')) cmd += ' -n';
            if (v.flags?.includes('-u')) cmd += ' -u';
            if (v.flags?.includes('-h')) cmd += ' -h';
            if (v.key)  cmd += ` -k ${v.key}`;
            if (v.sep)  cmd += ` -t '${v.sep}'`;
            if (v.file) cmd += ` ${v.file}`;
            return cmd;
        },
        explain: (v) => [
            { token: 'sort', desc: 'Trie les lignes d\'un fichier texte. Alphabétique par défaut.' },
            ...(v.flags?.includes('-r') ? [{ token: '-r', desc: 'Ordre inverse (décroissant).' }] : []),
            ...(v.flags?.includes('-n') ? [{ token: '-n', desc: 'Tri numérique (pas alphabétique). 10 > 9 au lieu de 10 < 9.' }] : []),
            ...(v.flags?.includes('-u') ? [{ token: '-u', desc: 'Supprime les doublons (unique).' }] : []),
            ...(v.flags?.includes('-h') ? [{ token: '-h', desc: 'Tri human-readable : 1K, 2M, 3G... (comme du ls -lh).' }] : []),
            ...(v.key   ? [{ token: `-k ${v.key}`, desc: `Trie selon la colonne ${v.key}.` }] : []),
        ],
        render: () => `
            <div class="opts-grid">
                <div class="field"><label>Fichier</label><input type="text" data-key="file" placeholder="data.txt"></div>
                <div class="field"><label>Trier par colonne (-k)</label><input type="text" data-key="key" placeholder="2 ou 3,3"></div>
                <div class="field"><label>Séparateur (-t)</label><input type="text" data-key="sep" placeholder=", ou :"></div>
            </div>
            <div class="field" style="margin-top:.6rem">
                <div class="checkbox-group" data-flags="flags">
                    <label class="check-pill"><input type="checkbox" value="-r"> -r inverse</label>
                    <label class="check-pill"><input type="checkbox" value="-n"> -n numérique</label>
                    <label class="check-pill"><input type="checkbox" value="-u"> -u unique</label>
                    <label class="check-pill"><input type="checkbox" value="-h"> -h human</label>
                </div>
            </div>`
    }
};

// ── State ─────────────────────────────────────────────────────
let currentCmd  = 'find';
let lastCommand = '';
const history   = [];

// ── DOM ───────────────────────────────────────────────────────
const optsTitle   = document.getElementById('opts-title');
const optsContainer = document.getElementById('opts-container');
const cmdOutput   = document.getElementById('cmd-output');
const explainContent = document.getElementById('explain-content');
const historyList = document.getElementById('history-list');
const btnGenerate = document.getElementById('btn-generate');
const btnCopy     = document.getElementById('btn-copy');
const btnClear    = document.getElementById('btn-clear');

// ── Render options panel ──────────────────────────────────────
function renderOptions(cmd) {
    const def = COMMANDS[cmd];
    if (!def) return;
    optsTitle.textContent = `Options — ${cmd}`;
    optsContainer.innerHTML = def.render();

    // Check-pill toggle
    optsContainer.querySelectorAll('.check-pill').forEach(label => {
        const cb = label.querySelector('input[type="checkbox"]');
        if (cb.checked) label.classList.add('checked');
        cb.addEventListener('change', () => {
            label.classList.toggle('checked', cb.checked);
            livePreview();
        });
    });

    // All other inputs → live preview
    optsContainer.querySelectorAll('input, select').forEach(el => {
        el.addEventListener('input', livePreview);
        el.addEventListener('change', livePreview);
    });

    livePreview();
}

// ── Collect values ────────────────────────────────────────────
function collectValues() {
    const v = {};
    optsContainer.querySelectorAll('[data-key]').forEach(el => {
        const key = el.dataset.key;
        if (el.type === 'checkbox') {
            v[key] = el.checked;
        } else {
            v[key] = el.value.trim();
        }
    });
    optsContainer.querySelectorAll('[data-flags]').forEach(group => {
        const key = group.dataset.flags;
        v[key] = [...group.querySelectorAll('input:checked')].map(cb => cb.value);
    });
    return v;
}

// ── Live preview ──────────────────────────────────────────────
function livePreview() {
    const def = COMMANDS[currentCmd];
    if (!def) return;
    const v = collectValues();
    const cmd = def.build(v);
    cmdOutput.textContent = cmd;
    lastCommand = cmd;
}

// ── Generate + explain + history ─────────────────────────────
function generate() {
    const def = COMMANDS[currentCmd];
    if (!def) return;
    const v = collectValues();
    const cmd = def.build(v);
    cmdOutput.textContent = cmd;
    lastCommand = cmd;

    // Explain
    const parts = def.explain(v);
    explainContent.innerHTML = parts.map(p => `
        <div class="explain-card">
            <div class="explain-card__token">${escHtml(p.token)}</div>
            <div>${escHtml(p.desc)}</div>
        </div>`).join('');

    // History
    if (cmd && cmd !== currentCmd) {
        history.unshift({ cmd, label: currentCmd });
        if (history.length > 15) history.pop();
        renderHistory();
    }
}

function renderHistory() {
    if (!history.length) {
        historyList.innerHTML = '<div class="history-empty">Aucune commande générée.</div>';
        return;
    }
    historyList.innerHTML = history.map((h, i) => `
        <div class="history-item" data-index="${i}" title="${escHtml(h.cmd)}">
            <span style="color:var(--accent);font-size:.6rem;">${h.label}</span> ${escHtml(h.cmd)}
        </div>`).join('');
    historyList.querySelectorAll('.history-item').forEach(el => {
        el.addEventListener('click', () => {
            cmdOutput.textContent = history[el.dataset.index].cmd;
            lastCommand = history[el.dataset.index].cmd;
        });
    });
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// ── Sidebar clicks ────────────────────────────────────────────
document.querySelectorAll('.cmd-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.cmd-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentCmd = btn.dataset.cmd;
        renderOptions(currentCmd);
    });
});

// ── Actions ───────────────────────────────────────────────────
btnGenerate.addEventListener('click', generate);

btnCopy.addEventListener('click', () => {
    if (!lastCommand) return;
    navigator.clipboard.writeText(lastCommand).then(() => {
        btnCopy.textContent = '✓ Copié !';
        btnCopy.classList.add('copied');
        setTimeout(() => { btnCopy.textContent = '📋 Copier'; btnCopy.classList.remove('copied'); }, 1500);
    });
});

btnClear.addEventListener('click', () => {
    cmdOutput.textContent = '';
    lastCommand = '';
    explainContent.innerHTML = '<div class="explain-card"><div class="explain-card__token">Astuce</div>Génère une commande pour voir son explication ici.</div>';
});

// ── Init ──────────────────────────────────────────────────────
renderOptions('find');

})();
</script>

</body>
</html>