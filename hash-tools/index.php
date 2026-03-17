<?php
/* ══════════════════════════════════════════════════════════
   API endpoint
══════════════════════════════════════════════════════════ */
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    $input  = $_POST['input']  ?? '';
    $action = $_POST['action'] ?? '';

    switch ($action) {

        // ── Base64 ────────────────────────────────────────────
        case 'b64_encode':
            echo json_encode(['result' => base64_encode($input), 'label' => 'Base64 encodé']);
            exit;
        case 'b64_decode':
            $dec = base64_decode($input, true);
            if ($dec === false) { echo json_encode(['error' => 'Données Base64 invalides']); exit; }
            echo json_encode(['result' => $dec, 'label' => 'Base64 décodé']);
            exit;
        case 'b64url_encode':
            $r = rtrim(strtr(base64_encode($input), '+/', '-_'), '=');
            echo json_encode(['result' => $r, 'label' => 'Base64 URL-safe encodé']);
            exit;
        case 'b64url_decode':
            $padded = str_pad(strtr($input, '-_', '+/'), strlen($input) + (4 - strlen($input) % 4) % 4, '=');
            $dec    = base64_decode($padded, true);
            if ($dec === false) { echo json_encode(['error' => 'Données Base64 URL invalides']); exit; }
            echo json_encode(['result' => $dec, 'label' => 'Base64 URL-safe décodé']);
            exit;

        // ── Hashes ───────────────────────────────────────────
        case 'hash_all':
            $algos = ['md5','sha1','sha224','sha256','sha384','sha512','sha3-256','sha3-512','ripemd160','crc32b','whirlpool'];
            $results = [];
            foreach ($algos as $algo) {
                if (in_array($algo, hash_algos())) {
                    $results[] = ['algo' => $algo, 'hash' => hash($algo, $input)];
                }
            }
            echo json_encode(['multi' => $results]);
            exit;

        case 'hmac':
            $key  = $_POST['key'] ?? '';
            $algo = $_POST['algo'] ?? 'sha256';
            if (!in_array($algo, hash_algos())) { echo json_encode(['error' => 'Algo non supporté']); exit; }
            $r = hash_hmac($algo, $input, $key);
            echo json_encode(['result' => $r, 'label' => "HMAC-{$algo}"]);
            exit;

        // ── URL encode/decode ─────────────────────────────────
        case 'url_encode':
            echo json_encode(['result' => urlencode($input), 'label' => 'URL encodé']);
            exit;
        case 'url_decode':
            echo json_encode(['result' => urldecode($input), 'label' => 'URL décodé']);
            exit;
        case 'rawurl_encode':
            echo json_encode(['result' => rawurlencode($input), 'label' => 'Raw URL encodé (RFC 3986)']);
            exit;

        // ── Hex ───────────────────────────────────────────────
        case 'to_hex':
            echo json_encode(['result' => bin2hex($input), 'label' => 'Hexadécimal']);
            exit;
        case 'from_hex':
            if (!ctype_xdigit(str_replace(' ', '', $input))) { echo json_encode(['error' => 'Hex invalide']); exit; }
            $r = hex2bin(str_replace(' ', '', $input));
            echo json_encode(['result' => $r, 'label' => 'Depuis Hex']);
            exit;

        // ── Counts ────────────────────────────────────────────
        case 'count':
            echo json_encode([
                'chars'  => mb_strlen($input),
                'bytes'  => strlen($input),
                'words'  => str_word_count($input),
                'lines'  => substr_count($input, "\n") + 1,
                'b64len' => strlen(base64_encode($input)),
            ]);
            exit;

        default:
            echo json_encode(['error' => 'Action inconnue']); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Base64 & Hash — Black-Lab Toolbox</title>
<style>
html, body { height: 100%; overflow-y: auto; }
body { display: flex; flex-direction: column; }
.page-wrap { position: relative; z-index: 1; flex: 1; display: flex; flex-direction: column; }
.page-body {
    flex: 1; padding: 1rem 1.4rem 1.6rem;
    display: flex; flex-direction: column; gap: .85rem;
    max-width: 1300px; width: 100%; margin: 0 auto;
}

/* ── Tabs ── */
.tool-tabs { display: flex; gap: .4rem; flex-wrap: wrap; }
.ttab {
    padding: .3rem .85rem; border-radius: 6px; font-size: .75rem; font-weight: 700;
    cursor: pointer; border: 1px solid var(--border);
    background: transparent; color: var(--text-muted); transition: all .15s;
    text-transform: uppercase; letter-spacing: .06em;
}
.ttab.active { background: rgba(255,22,84,.12); border-color: var(--accent,#ff1654); color: var(--accent,#ff1654); }
.ttab:hover  { border-color: var(--accent,#ff1654); color: var(--accent,#ff1654); }

/* ── Panneaux ── */
.tool-panel { display: none; flex-direction: column; gap: .85rem; }
.tool-panel.active { display: flex; }

/* ── IO grid ── */
.io-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .85rem; }
@media(max-width:720px) { .io-grid { grid-template-columns: 1fr; } }

/* ── Textareas ── */
.io-ta {
    width: 100%; min-height: 160px; resize: vertical;
    font-family: 'Fira Code', monospace; font-size: .82rem; line-height: 1.65;
    background: var(--bg-2); border: 1px solid var(--border);
    border-radius: var(--radius-sm); color: var(--text);
    padding: .75rem 1rem; outline: none; tab-size: 2; transition: border-color .15s;
}
.io-ta:focus { border-color: var(--accent,#ff1654); }
.io-ta::placeholder { color: var(--text-dim); }
.io-ta.output { color: #a5d6a7; background: rgba(0,0,0,.2); }
.io-ta.error  { color: #ff6b8a; }

/* ── Action buttons ── */
.action-row { display: flex; gap: .4rem; flex-wrap: wrap; align-items: center; }
.act-btn {
    padding: .35rem .85rem; border-radius: 6px; font-size: .72rem; font-weight: 700;
    cursor: pointer; border: 1px solid var(--border);
    background: var(--bg-2); color: var(--text-muted); transition: all .15s;
    text-transform: uppercase; letter-spacing: .06em; white-space: nowrap;
}
.act-btn:hover { border-color: var(--accent,#ff1654); color: var(--accent,#ff1654); background: rgba(255,22,84,.05); }
.act-btn.primary { background: var(--accent,#ff1654); border-color: var(--accent,#ff1654); color: #fff; }
.act-btn.primary:hover { opacity: .85; }

/* ── Hash table ── */
.hash-table { width: 100%; border-collapse: collapse; font-size: .78rem; }
.hash-table tr { border-bottom: 1px solid var(--border); }
.hash-table tr:last-child { border: none; }
.hash-table td { padding: .5rem .6rem; vertical-align: middle; }
.hash-table td:first-child {
    font-family: 'Fira Code', monospace; color: var(--accent,#ff1654);
    font-size: .72rem; width: 110px; white-space: nowrap;
}
.hash-table td:nth-child(2) {
    font-family: 'Fira Code', monospace; color: var(--text);
    word-break: break-all; font-size: .76rem;
}
.hash-table td:last-child { width: 60px; text-align: right; }
.hash-table tr:hover { background: var(--bg-2); }
.copy-cell { cursor: pointer; font-size: .7rem; color: var(--text-muted); transition: color .13s; }
.copy-cell:hover { color: var(--accent,#ff1654); }

/* ── Stats bar ── */
.stats-chips { display: flex; gap: .4rem; flex-wrap: wrap; }
.stat-chip {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .18rem .55rem; border-radius: 20px; font-size: .72rem;
    background: var(--bg-2); border: 1px solid var(--border); color: var(--text-muted);
}
.stat-chip strong { color: var(--text); }

/* ── HMAC ── */
.hmac-row { display: flex; gap: .5rem; align-items: center; flex-wrap: wrap; }
.hmac-row select { width: auto; font-size: .78rem; padding: .3rem .5rem; }
.hmac-row input  { flex: 1; min-width: 200px; font-family: 'Fira Code', monospace; font-size: .82rem; }

/* ── Result label ── */
.result-label {
    font-size: .62rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .08em; color: var(--text-muted); margin-bottom: .3rem;
}
</style>
</head>
<body>

<div class="ambient">
  <div class="ambient__circle"></div>
  <div class="ambient__circle"></div>
</div>

<div class="page-wrap">
<header class="hdr">
  <span class="hdr__icon">📟</span>
  <span class="hdr__title">BASE64 &amp; HASH TOOLS</span>
  <span class="hdr__sep"></span>
  <span class="hdr__meta">Base64 · URL · Hex · MD5 · SHA · HMAC · Zéro API</span>
</header>
<div class="page-body">

  <!-- Tabs -->
  <div class="tool-tabs">
    <button class="ttab active" onclick="switchTab('base64')">Base64</button>
    <button class="ttab"        onclick="switchTab('hash')">Hash</button>
    <button class="ttab"        onclick="switchTab('hmac')">HMAC</button>
    <button class="ttab"        onclick="switchTab('url')">URL Encode</button>
    <button class="ttab"        onclick="switchTab('hex')">Hex</button>
  </div>

  <!-- ══ BASE64 ══ -->
  <div class="tool-panel active" id="panel-base64">
    <div class="io-grid">
      <div class="card">
        <div class="card-title">Entrée</div>
        <textarea class="io-ta" id="b64-input" placeholder="Collez votre texte ici…" oninput="autoB64()"></textarea>
        <div class="action-row" style="margin-top:.6rem">
          <button class="act-btn primary" onclick="doB64('b64_encode')">→ Encoder</button>
          <button class="act-btn primary" onclick="doB64('b64_decode')">→ Décoder</button>
          <button class="act-btn"         onclick="doB64('b64url_encode')">→ URL-safe encoder</button>
          <button class="act-btn"         onclick="doB64('b64url_decode')">→ URL-safe décoder</button>
          <div style="flex:1"></div>
          <button class="act-btn" onclick="clearB64()">✕ Effacer</button>
        </div>
        <div class="stats-chips" id="b64-stats" style="margin-top:.5rem"></div>
      </div>
      <div class="card">
        <div class="result-label" id="b64-label">Résultat</div>
        <textarea class="io-ta output" id="b64-output" readonly placeholder="Le résultat apparaîtra ici…"></textarea>
        <div class="action-row" style="margin-top:.6rem">
          <button class="act-btn" onclick="copyB64()">📋 Copier</button>
          <button class="act-btn" onclick="swapB64()">⇄ Utiliser comme entrée</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ══ HASH ══ -->
  <div class="tool-panel" id="panel-hash">
    <div class="card">
      <div class="card-title">Texte à hasher</div>
      <textarea class="io-ta" id="hash-input" placeholder="Entrez le texte à hasher…" oninput="autoHash()" style="min-height:100px"></textarea>
      <div class="action-row" style="margin-top:.6rem">
        <button class="act-btn primary" onclick="doHash()">⚡ Calculer tous les hashes</button>
        <button class="act-btn" onclick="clearHash()">✕ Effacer</button>
      </div>
    </div>
    <div class="card" id="hash-results" style="display:none">
      <div class="card-title">Résultats</div>
      <table class="hash-table" id="hash-table"></table>
    </div>
  </div>

  <!-- ══ HMAC ══ -->
  <div class="tool-panel" id="panel-hmac">
    <div class="io-grid">
      <div class="card">
        <div class="card-title">Message</div>
        <textarea class="io-ta" id="hmac-input" placeholder="Message à signer…" style="min-height:120px"></textarea>
        <div style="margin-top:.6rem">
          <div class="result-label">Clé secrète &amp; algorithme</div>
          <div class="hmac-row">
            <input type="text" id="hmac-key" placeholder="Votre clé secrète…" autocomplete="off">
            <select id="hmac-algo">
              <option value="sha256" selected>SHA-256</option>
              <option value="sha512">SHA-512</option>
              <option value="sha1">SHA-1</option>
              <option value="md5">MD5</option>
              <option value="sha3-256">SHA3-256</option>
            </select>
            <button class="act-btn primary" onclick="doHmac()">⚡ Signer</button>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="result-label" id="hmac-label">Signature HMAC</div>
        <textarea class="io-ta output" id="hmac-output" readonly placeholder="Résultat ici…" style="min-height:120px"></textarea>
        <div class="action-row" style="margin-top:.6rem">
          <button class="act-btn" onclick="copyEl('hmac-output')">📋 Copier</button>
        </div>
        <div style="margin-top:.75rem;font-size:.75rem;color:var(--text-muted);line-height:1.6">
          💡 HMAC (Hash-based Message Authentication Code) utilise une clé secrète pour signer un message. Utilisé pour vérifier l'intégrité et l'authenticité des données (webhooks, API…).
        </div>
      </div>
    </div>
  </div>

  <!-- ══ URL ENCODE ══ -->
  <div class="tool-panel" id="panel-url">
    <div class="io-grid">
      <div class="card">
        <div class="card-title">Entrée</div>
        <textarea class="io-ta" id="url-input" placeholder="Texte ou URL à encoder…"></textarea>
        <div class="action-row" style="margin-top:.6rem">
          <button class="act-btn primary" onclick="doUrl('url_encode')">→ URL Encode</button>
          <button class="act-btn primary" onclick="doUrl('url_decode')">→ URL Decode</button>
          <button class="act-btn"         onclick="doUrl('rawurl_encode')">→ Raw URL Encode</button>
          <button class="act-btn"         onclick="clearEl('url-input','url-output','url-label')">✕ Effacer</button>
        </div>
      </div>
      <div class="card">
        <div class="result-label" id="url-label">Résultat</div>
        <textarea class="io-ta output" id="url-output" readonly placeholder="Le résultat apparaîtra ici…"></textarea>
        <div class="action-row" style="margin-top:.6rem">
          <button class="act-btn" onclick="copyEl('url-output')">📋 Copier</button>
          <button class="act-btn" onclick="swapEl('url-input','url-output')">⇄ Utiliser comme entrée</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ══ HEX ══ -->
  <div class="tool-panel" id="panel-hex">
    <div class="io-grid">
      <div class="card">
        <div class="card-title">Entrée</div>
        <textarea class="io-ta" id="hex-input" placeholder="Texte ou valeur hex…"></textarea>
        <div class="action-row" style="margin-top:.6rem">
          <button class="act-btn primary" onclick="doHex('to_hex')">→ Texte → Hex</button>
          <button class="act-btn primary" onclick="doHex('from_hex')">→ Hex → Texte</button>
          <button class="act-btn"         onclick="clearEl('hex-input','hex-output','hex-label')">✕ Effacer</button>
        </div>
      </div>
      <div class="card">
        <div class="result-label" id="hex-label">Résultat</div>
        <textarea class="io-ta output" id="hex-output" readonly placeholder="Le résultat apparaîtra ici…"></textarea>
        <div class="action-row" style="margin-top:.6rem">
          <button class="act-btn" onclick="copyEl('hex-output')">📋 Copier</button>
          <button class="act-btn" onclick="swapEl('hex-input','hex-output')">⇄ Utiliser comme entrée</button>
        </div>
      </div>
    </div>
  </div>

</div>
</div>
<div class="toast-area" id="ta"></div>

<script>
const $ = id => document.getElementById(id);

/* ── Tabs ── */
function switchTab(name) {
    document.querySelectorAll('.tool-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.ttab').forEach(t => t.classList.remove('active'));
    $('panel-' + name).classList.add('active');
    event.target.classList.add('active');
}

/* ══════════════════════════════════════════
   BASE64
══════════════════════════════════════════ */
let b64Debounce;
function autoB64() {
    clearTimeout(b64Debounce);
    b64Debounce = setTimeout(updateStats, 300);
}

async function doB64(action) {
    const input = $('b64-input').value;
    const data  = await post({ action, input });
    if (data.error) { showOutput('b64-output', data.error, 'error'); return; }
    showOutput('b64-output', data.result, 'ok');
    $('b64-label').textContent = data.label || 'Résultat';
    updateStats();
}

function updateStats() {
    const v = $('b64-input').value;
    if (!v) { $('b64-stats').innerHTML = ''; return; }
    post({ action: 'count', input: v }).then(d => {
        if (d.error) return;
        $('b64-stats').innerHTML = [
            ['Chars', d.chars],
            ['Octets', d.bytes],
            ['Mots', d.words],
            ['Lignes', d.lines],
            ['Taille B64', d.b64len + ' car.'],
        ].map(([k,v]) => `<span class="stat-chip"><strong>${v}</strong> ${k}</span>`).join('');
    });
}

function clearB64() {
    $('b64-input').value = ''; $('b64-output').value = '';
    $('b64-label').textContent = 'Résultat'; $('b64-stats').innerHTML = '';
    $('b64-output').className = 'io-ta output';
}

function copyB64() { copyEl('b64-output'); }
function swapB64() {
    const out = $('b64-output').value;
    if (!out) return;
    $('b64-input').value = out; $('b64-output').value = '';
    updateStats();
}

/* ══════════════════════════════════════════
   HASH
══════════════════════════════════════════ */
let hashDebounce;
function autoHash() {
    clearTimeout(hashDebounce);
    hashDebounce = setTimeout(doHash, 400);
}

async function doHash() {
    const input = $('hash-input').value;
    if (!input) { $('hash-results').style.display = 'none'; return; }
    const data = await post({ action: 'hash_all', input });
    if (data.error || !data.multi) return;
    const tbody = data.multi.map(row =>
        `<tr>
            <td>${row.algo}</td>
            <td>${row.hash}</td>
            <td class="copy-cell" onclick="copyText('${row.hash}')" title="Copier">📋</td>
        </tr>`
    ).join('');
    $('hash-table').innerHTML = tbody;
    $('hash-results').style.display = 'block';
}

function clearHash() {
    $('hash-input').value = '';
    $('hash-results').style.display = 'none';
}

/* ══════════════════════════════════════════
   HMAC
══════════════════════════════════════════ */
async function doHmac() {
    const input = $('hmac-input').value;
    const key   = $('hmac-key').value;
    const algo  = $('hmac-algo').value;
    if (!input) { toast('Entrez un message', 'err'); return; }
    if (!key)   { toast('Entrez une clé secrète', 'err'); return; }
    const data = await post({ action: 'hmac', input, key, algo });
    if (data.error) { showOutput('hmac-output', data.error, 'error'); return; }
    showOutput('hmac-output', data.result, 'ok');
    $('hmac-label').textContent = data.label || 'Signature HMAC';
}

/* ══════════════════════════════════════════
   URL + HEX
══════════════════════════════════════════ */
async function doUrl(action) {
    const input = $('url-input').value;
    const data  = await post({ action, input });
    if (data.error) { showOutput('url-output', data.error, 'error'); return; }
    showOutput('url-output', data.result, 'ok');
    $('url-label').textContent = data.label || 'Résultat';
}

async function doHex(action) {
    const input = $('hex-input').value;
    const data  = await post({ action, input });
    if (data.error) { showOutput('hex-output', data.error, 'error'); return; }
    showOutput('hex-output', data.result, 'ok');
    $('hex-label').textContent = data.label || 'Résultat';
}

/* ══════════════════════════════════════════
   UTILS
══════════════════════════════════════════ */
async function post(fields) {
    const fd = new FormData();
    for (const [k, v] of Object.entries(fields)) fd.append(k, v);
    try {
        const res = await fetch('', { method: 'POST', body: fd });
        return await res.json();
    } catch(e) { return { error: 'Erreur réseau : ' + e.message }; }
}

function showOutput(id, text, type) {
    const el = $(id);
    el.value = text;
    el.className = 'io-ta output' + (type === 'error' ? ' error' : '');
}

function clearEl(inputId, outputId, labelId) {
    $(inputId).value = ''; $(outputId).value = '';
    if (labelId) $(labelId).textContent = 'Résultat';
    $(outputId).className = 'io-ta output';
}

function swapEl(inputId, outputId) {
    const out = $(outputId).value; if (!out) return;
    $(inputId).value = out; $(outputId).value = '';
}

function copyEl(id) {
    const val = $(id).value; if (!val) return;
    copyText(val);
}

function copyText(text) {
    navigator.clipboard.writeText(text)
        .then(() => toast('Copié !'))
        .catch(() => toast('Échec', 'err'));
}

function toast(m, t = 'ok') {
    const a = $('ta');
    const e = document.createElement('div');
    e.className = `toast toast--${t}`;
    e.textContent = m;
    a.appendChild(e);
    setTimeout(() => e.remove(), 2500);
}
</script>
</body>
</html>
