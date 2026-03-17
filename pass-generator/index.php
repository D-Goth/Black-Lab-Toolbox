<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pass Generator — Black-Lab Toolbox</title>
    <style>
    :root {
        --bg:          #0d0d0f;
        --bg-2:        #111116;
        --surface:     rgba(255,255,255,0.04);
        --surface-h:   rgba(255,255,255,0.08);
        --border:      rgba(255,255,255,0.08);
        --accent:      #ff1654;
        --accent-soft: rgba(255,22,84,0.15);
        --violet:      #5e006c;
        --gradient:    linear-gradient(135deg,#ff1654,#5e006c);
        --text:        #e8e8f0;
        --text-muted:  #7a7a90;
        --text-dim:    #3a3a50;
        --radius:      14px;
        --radius-sm:   8px;
        --trans:       0.18s ease;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html, body {
        height: 100%;
        font-family: 'Segoe UI', system-ui, sans-serif;
        font-size: 14px;
        background: var(--bg);
        color: var(--text);
        -webkit-font-smoothing: antialiased;
        overflow-x: hidden;
    }

    /* ── Ambient circles ──────────────────────────────────── */
    /* .ambient : défini dans tools-shared.css */

    /* ── Layout ───────────────────────────────────────────── */
    .page {
        position: relative;
        z-index: 1;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 2rem 1.5rem 3rem;
        gap: 1.5rem;
    }

    /* .hdr : défini dans tools-shared.css */

    /* ── Main grid ────────────────────────────────────────── */
    .main-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.2rem;
        width: 100%;
        max-width: 860px;
    }

    /* ── Glass panel ──────────────────────────────────────── */
    .panel {
        background: rgba(20,20,26,0.85);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.5rem;
        backdrop-filter: blur(16px);
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .panel__title {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--accent);
    }

    /* ── Form elements ────────────────────────────────────── */
    .field { display: flex; flex-direction: column; gap: 0.4rem; }
    .field label {
        font-size: 0.78rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    input[type="number"] {
        width: 100%;
        padding: 0.6rem 0.9rem;
        background: var(--bg-2);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        color: var(--text);
        font-size: 0.9rem;
        outline: none;
        transition: border-color var(--trans), box-shadow var(--trans);
        -moz-appearance: textfield;
    }
    input[type="number"]::-webkit-inner-spin-button { opacity: 0.4; }
    input[type="number"]:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px var(--accent-soft);
    }

    /* Length slider */
    .length-row {
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }
    .length-row input[type="range"] {
        flex: 1;
        -webkit-appearance: none;
        height: 4px;
        background: var(--border);
        border-radius: 2px;
        outline: none;
        cursor: pointer;
    }
    .length-row input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 16px; height: 16px;
        border-radius: 50%;
        background: var(--accent);
        cursor: pointer;
        box-shadow: 0 0 6px var(--accent-soft);
    }
    .length-val {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--accent);
        min-width: 2.5rem;
        text-align: right;
        font-family: monospace;
    }

    /* Checkboxes grid */
    .options-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem 1rem;
    }
    .option {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        font-size: 0.82rem;
        color: var(--text-muted);
        transition: color var(--trans);
    }
    .option:hover { color: var(--text); }
    .option input[type="checkbox"] {
        accent-color: var(--accent);
        width: 14px; height: 14px;
        cursor: pointer;
    }

    /* Generate button */
    .btn-generate {
        width: 100%;
        padding: 0.75rem;
        background: var(--gradient);
        border: none;
        border-radius: var(--radius-sm);
        color: #fff;
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        letter-spacing: 0.04em;
        transition: opacity var(--trans), transform var(--trans), box-shadow var(--trans);
        box-shadow: 0 4px 20px rgba(255,22,84,0.25);
        margin-top: 0.2rem;
    }
    .btn-generate:hover {
        opacity: 0.88;
        transform: translateY(-1px);
        box-shadow: 0 6px 24px rgba(255,22,84,0.4);
    }
    .btn-generate:active { transform: translateY(0); }

    /* ── Password output ──────────────────────────────────── */
    .pwd-display {
        font-family: 'Cascadia Code', 'Fira Code', 'Consolas', monospace;
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--accent);
        word-break: break-all;
        padding: 0.6rem 0.9rem;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        min-height: 2.4rem;
        display: flex;
        align-items: center;
        line-height: 1.4;
        letter-spacing: 0.04em;
        transition: border-color var(--trans);
    }
    .pwd-display.flash {
        animation: pwdFlash 0.3s ease;
    }
    @keyframes pwdFlash {
        0%,100% { border-color: var(--border); }
        50% { border-color: var(--accent); box-shadow: 0 0 12px var(--accent-soft); }
    }

    /* Strength bar */
    .strength-wrap { display: flex; flex-direction: column; gap: 0.4rem; }
    .strength-track {
        height: 6px;
        background: rgba(255,255,255,0.06);
        border-radius: 3px;
        overflow: hidden;
    }
    .strength-fill {
        height: 100%;
        width: 0%;
        border-radius: 3px;
        transition: width 0.35s ease, background-color 0.35s ease;
    }
    .strength-label {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    /* Entropy badge */
    .entropy-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.7rem;
        color: var(--text-dim);
        font-family: monospace;
    }
    .entropy-badge span { color: var(--text-muted); }

    /* Action buttons row */
    .action-row {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .btn {
        padding: 0.5rem 1rem;
        border-radius: var(--radius-sm);
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all var(--trans);
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--text-muted);
        flex: 1;
        text-align: center;
    }
    .btn:hover { background: var(--surface-h); color: var(--text); border-color: var(--accent); }
    .btn.copied { background: rgba(46,204,113,0.15); border-color: #2ecc71; color: #2ecc71; }

    .ambiguous-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-top: 0.3rem;
    }

    /* ── Tips panel (full width) ─────────────────────────── */
    .tips-panel {
        width: 100%;
        max-width: 860px;
    }
    .tips-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.75rem;
        margin-top: 0.6rem;
    }
    .tip-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 0.9rem 1rem;
        font-size: 0.8rem;
        color: var(--text-muted);
        line-height: 1.6;
        display: flex;
        gap: 0.6rem;
    }
    .tip-card__icon { font-size: 1.1rem; flex-shrink: 0; margin-top:1px; }
    .tip-card strong { color: var(--text); }

    /* ── Tabs mode ──────────────────────────────────────── */
    .mode-tabs { display:flex; gap:0.4rem; margin-bottom:0.8rem; }
    .mode-tab {
        flex:1; padding:0.4rem 0.6rem; background:var(--surface); border:1px solid var(--border);
        border-radius:var(--radius-sm); color:var(--text-muted); cursor:pointer;
        font-size:0.78rem; font-weight:600; font-family:inherit; transition:all var(--trans); text-align:center;
    }
    .mode-tab.active { background:var(--accent-soft); border-color:var(--accent); color:var(--accent); }

    /* ── Batch ───────────────────────────────────────────── */
    .batch-list {
        display:flex; flex-direction:column; gap:0.3rem; margin-top:0.4rem;
    }
    .batch-item {
        display:flex; align-items:center; gap:0.5rem;
        background:var(--bg); border:1px solid var(--border); border-radius:var(--radius-sm);
        padding:0.4rem 0.7rem; font-family:monospace; font-size:0.82rem; color:var(--accent);
    }
    .batch-item span { flex:1; word-break:break-all; }
    .batch-copy { background:none; border:none; color:var(--text-dim); cursor:pointer; font-size:0.75rem; padding:0; transition:color var(--trans); flex-shrink:0; }
    .batch-copy:hover { color:var(--accent); }

    /* ── Crack time ──────────────────────────────────────── */
    .crack-row { display:flex; align-items:center; justify-content:space-between; font-size:0.72rem; color:var(--text-dim); margin-top:0.25rem; }
    .crack-val { color:var(--text-muted); font-family:monospace; }

    /* ── Footer ───────────────────────────────────────────── */
    .tool-footer {
        font-size: 0.7rem;
        color: var(--text-dim);
        text-align: center;
        padding-top: 0.5rem;
    }
    .tool-footer a { color: var(--text-dim); text-decoration: none; }
    .tool-footer a:hover { color: var(--accent); }

    /* ── Scrollbar ────────────────────────────────────────── */
    ::-webkit-scrollbar { width: 4px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

    /* ── Responsive ───────────────────────────────────────── */
    @media (max-width: 640px) {
        .main-grid { grid-template-columns: 1fr; }
    }
    </style>
</head>
<body>

<!-- Ambient background -->
<div class="ambient">
    <div class="ambient__circle"></div>
    <div class="ambient__circle"></div>
</div>

<header class="hdr">
  <span class="hdr__icon">🔑</span>
  <span class="hdr__title">PASSWORD GENERATOR</span>
  <span class="hdr__sep"></span>
  <span class="hdr__meta">Générateur cryptographiquement sécurisé</span>
</header>

<div class="page">

    <!-- Main grid -->
    <div class="main-grid">

        <!-- LEFT : Config -->
        <div class="panel">
            <div class="panel__title">Configuration</div>

            <div class="field">
                <label>Longueur du mot de passe</label>
                <div class="length-row">
                    <input type="range" id="pwd-slider" min="4" max="128" value="16">
                    <span class="length-val" id="length-display">16</span>
                </div>
            </div>

            <div class="field">
                <label>Jeux de caractères</label>
                <div class="options-grid">
                    <label class="option">
                        <input type="checkbox" id="include-lowercase" checked>
                        <span>Minuscules <small style="opacity:.5">a–z</small></span>
                    </label>
                    <label class="option">
                        <input type="checkbox" id="include-uppercase" checked>
                        <span>Majuscules <small style="opacity:.5">A–Z</small></span>
                    </label>
                    <label class="option">
                        <input type="checkbox" id="include-numbers" checked>
                        <span>Chiffres <small style="opacity:.5">0–9</small></span>
                    </label>
                    <label class="option">
                        <input type="checkbox" id="include-symbols" checked>
                        <span>Symboles <small style="opacity:.5">!@#…</small></span>
                    </label>
                </div>
            </div>

            <label class="option ambiguous-row">
                <input type="checkbox" id="exclude-ambiguous">
                <span style="font-size:0.8rem;color:var(--text-muted);">Exclure caractères ambigus <small style="opacity:.5">O0Il1</small></span>
            </label>

            <button class="btn-generate" id="btn-generate">⚡ Générer</button>
        </div>

        <!-- RIGHT : Output -->
        <div class="panel">
            <div class="panel__title">Résultat</div>

            <!-- Tabs -->
            <div class="mode-tabs">
                <button class="mode-tab active" id="tab-single" onclick="setTab('single')">🔑 Simple</button>
                <button class="mode-tab" id="tab-batch" onclick="setTab('batch')">📦 Lot (×5)</button>
                <button class="mode-tab" id="tab-phrase" onclick="setTab('phrase')">💬 Passphrase</button>
            </div>

            <!-- Single -->
            <div id="view-single">
                <div class="pwd-display" id="pwd-output"></div>
                <div class="crack-row">
                    <span>Temps de crack estimé :</span>
                    <span class="crack-val" id="crack-time">—</span>
                </div>
                <div class="strength-wrap" style="margin-top:0.5rem">
                    <div class="strength-track">
                        <div class="strength-fill" id="strength-fill"></div>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span class="strength-label" id="strength-label"></span>
                        <span class="entropy-badge">Entropie : <span id="entropy-val">—</span> bits</span>
                    </div>
                </div>
                <div class="action-row" style="margin-top:0.6rem">
                    <button class="btn" id="btn-copy">📋 Copier</button>
                    <button class="btn" id="btn-regen">🔄 Régénérer</button>
                </div>
            </div>

            <!-- Batch -->
            <div id="view-batch" style="display:none">
                <div class="batch-list" id="batch-list"></div>
                <button class="btn-generate" id="btn-batch" style="margin-top:0.6rem">⚡ Générer 5 mots de passe</button>
            </div>

            <!-- Passphrase -->
            <div id="view-phrase" style="display:none">
                <div class="pwd-display" id="phrase-output" style="font-size:0.88rem;letter-spacing:0"></div>
                <div style="font-size:0.72rem;color:var(--text-dim);margin-top:0.3rem">4 mots aléatoires séparés par <code style="background:var(--surface);padding:0 4px;border-radius:3px">-</code></div>
                <div class="action-row" style="margin-top:0.6rem">
                    <button class="btn" id="btn-copy-phrase">📋 Copier</button>
                    <button class="btn" id="btn-regen-phrase">🔄 Régénérer</button>
                </div>
            </div>
        </div>

    </div>

    <!-- Tips -->
    <div class="tips-panel">
        <div class="panel">
            <div class="panel__title">Conseils de sécurité</div>
            <div class="tips-grid">
                <div class="tip-card">
                    <span class="tip-card__icon">🔒</span>
                    <span>Minimum <strong>16 caractères</strong> pour un bon niveau. 24+ pour les comptes sensibles.</span>
                </div>
                <div class="tip-card">
                    <span class="tip-card__icon">🎲</span>
                    <span>Combinez les <strong>4 types</strong> de caractères pour maximiser l'entropie et la résistance.</span>
                </div>
                <div class="tip-card">
                    <span class="tip-card__icon">🚫</span>
                    <span>Évitez les <strong>mots du dictionnaire</strong>, dates de naissance et informations personnelles.</span>
                </div>
                <div class="tip-card">
                    <span class="tip-card__icon">🔁</span>
                    <span><strong>Un mot de passe unique</strong> par service. Utilisez Bitwarden, KeePass ou 1Password.</span>
                </div>
                <div class="tip-card">
                    <span class="tip-card__icon">💬</span>
                    <span>La <strong>passphrase</strong> (4 mots+) est souvent plus mémorisable et aussi sécurisée.</span>
                </div>
                <div class="tip-card">
                    <span class="tip-card__icon">⚡</span>
                    <span><strong>80 bits d'entropie</strong> = fort. 100+ bits = très fort contre les attaques GPU modernes.</span>
                </div>
                <div class="tip-card">
                    <span class="tip-card__icon">🛡️</span>
                    <span>Activez le <strong>2FA</strong> partout où c'est possible — même un bon MDP peut être volé.</span>
                </div>
                <div class="tip-card">
                    <span class="tip-card__icon">🔄</span>
                    <span>Changez vos mots de passe si un service est <strong>compromis</strong> — vérifiez HaveIBeenPwned.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="tool-footer">
        Le Lab'O Noir · Password Generator ·
        <a href="https://creativecommons.org/licenses/by-nc/4.0/deed.fr" target="_blank">CC BY-NC 4.0</a>
        · <a href="https://black-lab.fr" target="_blank">black-lab.fr</a>
    </div>

</div>


<script>
(function () {

    const slider       = document.getElementById('pwd-slider');
    const lengthDisp   = document.getElementById('length-display');
    const lowerEl      = document.getElementById('include-lowercase');
    const upperEl      = document.getElementById('include-uppercase');
    const numberEl     = document.getElementById('include-numbers');
    const symbolEl     = document.getElementById('include-symbols');
    const ambigEl      = document.getElementById('exclude-ambiguous');
    const output       = document.getElementById('pwd-output');
    const strengthFill = document.getElementById('strength-fill');
    const strengthLbl  = document.getElementById('strength-label');
    const entropyVal   = document.getElementById('entropy-val');
    const crackEl      = document.getElementById('crack-time');
    const btnGen       = document.getElementById('btn-generate');
    const btnCopy      = document.getElementById('btn-copy');
    const btnRegen     = document.getElementById('btn-regen');

    const CHARS = {
        lower:   'abcdefghijklmnopqrstuvwxyz',
        upper:   'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        numbers: '0123456789',
        symbols: '!@#$%^&*()_+[]{}|;:,.<>?'
    };
    const AMBIGUOUS = /[O0Il1]/g;

    function buildPool() {
        let pool = '';
        if (lowerEl.checked)  pool += CHARS.lower;
        if (upperEl.checked)  pool += CHARS.upper;
        if (numberEl.checked) pool += CHARS.numbers;
        if (symbolEl.checked) pool += CHARS.symbols;
        if (ambigEl.checked)  pool = pool.replace(AMBIGUOUS, '');
        return pool;
    }

    function cryptoRand(max) {
        const arr = new Uint32Array(1);
        crypto.getRandomValues(arr);
        return arr[0] % max;
    }

    function crackTime(entropy) {
        const seconds = Math.pow(2, entropy) / (2 * 1e12);
        if (seconds < 1)        return '< 1 seconde';
        if (seconds < 60)       return Math.round(seconds) + ' sec';
        if (seconds < 3600)     return Math.round(seconds / 60) + ' min';
        if (seconds < 86400)    return Math.round(seconds / 3600) + ' heures';
        if (seconds < 2592000)  return Math.round(seconds / 86400) + ' jours';
        if (seconds < 31536000) return Math.round(seconds / 2592000) + ' mois';
        const y = seconds / 31536000;
        if (y < 1e6)  return y.toExponential(1) + ' ans';
        if (y < 1e9)  return 'millions d annees';
        if (y < 1e12) return 'milliards d annees';
        return 'invulnerable';
    }

    function generate() {
        const len  = parseInt(slider.value, 10);
        const pool = buildPool();
        if (!pool.length) {
            output.textContent = 'Selectionnez au moins un jeu de caracteres.';
            strengthFill.style.width = '0%';
            strengthLbl.textContent = '';
            entropyVal.textContent = '-';
            if (crackEl) crackEl.textContent = '-';
            return;
        }
        const req = [];
        if (lowerEl.checked)  req.push(CHARS.lower[cryptoRand(CHARS.lower.length)]);
        if (upperEl.checked)  req.push(CHARS.upper[cryptoRand(CHARS.upper.length)]);
        if (numberEl.checked) req.push(CHARS.numbers[cryptoRand(CHARS.numbers.length)]);
        if (symbolEl.checked) req.push(CHARS.symbols[cryptoRand(CHARS.symbols.length)]);
        let pwd = req.join('');
        for (let i = pwd.length; i < len; i++) pwd += pool[cryptoRand(pool.length)];
        pwd = pwd.split('').sort(() => cryptoRand(3) - 1).join('');

        output.textContent = pwd;
        output.classList.remove('flash');
        void output.offsetWidth;
        output.classList.add('flash');

        const entropy = Math.round(len * Math.log2(pool.length));
        updateStrength(entropy);
        entropyVal.textContent = entropy;
        if (crackEl) crackEl.textContent = crackTime(entropy);
    }

    function updateStrength(entropy) {
        let pct, color, label;
        if (entropy < 40)       { pct = 20; color = '#ff4444'; label = 'Tres faible'; }
        else if (entropy < 60)  { pct = 40; color = '#ff7700'; label = 'Faible'; }
        else if (entropy < 80)  { pct = 60; color = '#ffbd2e'; label = 'Moyen'; }
        else if (entropy < 100) { pct = 80; color = '#aeea00'; label = 'Fort'; }
        else                    { pct = 100; color = '#27c93f'; label = 'Tres fort'; }
        strengthFill.style.width = pct + '%';
        strengthFill.style.backgroundColor = color;
        strengthLbl.textContent = label;
        strengthLbl.style.color = color;
    }

    slider.addEventListener('input', () => { lengthDisp.textContent = slider.value; generate(); });
    btnGen.addEventListener('click', generate);
    btnRegen.addEventListener('click', generate);
    [lowerEl, upperEl, numberEl, symbolEl, ambigEl].forEach(el => el.addEventListener('change', generate));
    btnCopy.addEventListener('click', () => {
        const txt = output.textContent.trim();
        if (!txt) return;
        navigator.clipboard.writeText(txt).then(() => {
            btnCopy.textContent = 'Copie !';
            btnCopy.classList.add('copied');
            setTimeout(() => { btnCopy.textContent = 'Copier'; btnCopy.classList.remove('copied'); }, 1500);
        });
    });

    // Tabs
    function setTab(tab) {
        ['single','batch','phrase'].forEach(t => {
            document.getElementById('view-' + t).style.display = t === tab ? '' : 'none';
            document.getElementById('tab-' + t).classList.toggle('active', t === tab);
        });
    }
    window.setTab = setTab;

    // Batch
    function generateBatch() {
        const list = document.getElementById('batch-list');
        list.innerHTML = '';
        const pool = buildPool();
        if (!pool.length) return;
        const len = parseInt(slider.value, 10);
        for (let i = 0; i < 5; i++) {
            const req = [];
            if (lowerEl.checked)  req.push(CHARS.lower[cryptoRand(CHARS.lower.length)]);
            if (upperEl.checked)  req.push(CHARS.upper[cryptoRand(CHARS.upper.length)]);
            if (numberEl.checked) req.push(CHARS.numbers[cryptoRand(CHARS.numbers.length)]);
            if (symbolEl.checked) req.push(CHARS.symbols[cryptoRand(CHARS.symbols.length)]);
            let pwd = req.join('');
            for (let j = pwd.length; j < len; j++) pwd += pool[cryptoRand(pool.length)];
            pwd = pwd.split('').sort(() => cryptoRand(3) - 1).join('');
            const row = document.createElement('div'); row.className = 'batch-item';
            const span = document.createElement('span'); span.textContent = pwd;
            const btn  = document.createElement('button'); btn.className = 'batch-copy'; btn.textContent = 'Copier';
            btn.addEventListener('click', () => {
                navigator.clipboard.writeText(pwd).then(() => { btn.textContent = 'OK'; setTimeout(() => btn.textContent = 'Copier', 1200); });
            });
            row.appendChild(span); row.appendChild(btn); list.appendChild(row);
        }
    }
    document.getElementById('btn-batch').addEventListener('click', generateBatch);

    // Passphrase
    const WORDS = ['soleil','nuage','rapide','arbre','force','cristal','ombre','fleuve',
        'montagne','vent','pierre','forge','lame','acier','nuit','aube','eclair','vague',
        'dragon','loup','aigle','serpent','renard','corbeau','tigre','lion','cerf','ours',
        'rouge','bleu','noir','blanc','vert','violet','dore','argent','ardent','froid',
        'chateau','foret','desert','ocean','vallee','sommet','caverne','plaine','glacier','tempete'];

    function generatePhrase() {
        const words = [];
        for (let i = 0; i < 4; i++) words.push(WORDS[cryptoRand(WORDS.length)]);
        document.getElementById('phrase-output').textContent = words.join('-');
    }
    document.getElementById('btn-regen-phrase').addEventListener('click', generatePhrase);
    document.getElementById('btn-copy-phrase').addEventListener('click', () => {
        const txt = document.getElementById('phrase-output').textContent;
        if (!txt) return;
        navigator.clipboard.writeText(txt).then(() => {
            const b = document.getElementById('btn-copy-phrase');
            b.textContent = 'Copie !'; b.classList.add('copied');
            setTimeout(() => { b.textContent = 'Copier'; b.classList.remove('copied'); }, 1500);
        });
    });

    // Init
    generate();
    generatePhrase();
    generateBatch();

})();
</script>

</body>
</html>