<?php
/* ══════════════════════════════════════════════════════════
   API endpoint — validation + formatage côté PHP
══════════════════════════════════════════════════════════ */
if (isset($_POST['action']) && $_POST['action'] === 'format') {
    header('Content-Type: application/json');

    $raw    = $_POST['json'] ?? '';
    $indent = (int)($_POST['indent'] ?? 2);
    $indent = max(0, min(8, $indent));

    if (trim($raw) === '') {
        echo json_encode(['error' => 'Entrée vide']); exit;
    }

    $decoded = json_decode($raw);

    if (json_last_error() !== JSON_ERROR_NONE) {
        // Tentative de localisation de l'erreur
        $errMsg = json_last_error_msg();
        // Cherche ligne/colonne approximatives
        $lines = explode("\n", $raw);
        $lineNum = null;
        foreach ($lines as $i => $line) {
            $partial = implode("\n", array_slice($lines, 0, $i + 1));
            if (json_decode($partial) === null && json_last_error() !== JSON_ERROR_NONE) {
                $lineNum = $i + 1; break;
            }
        }
        echo json_encode([
            'valid'    => false,
            'error'    => $errMsg,
            'line'     => $lineNum,
        ]);
        exit;
    }

    $flags  = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
    $pretty = json_encode($decoded, $flags);

    // Indentation personnalisée (PHP force 4 espaces, on adapte)
    if ($indent !== 4) {
        $pretty = preg_replace_callback('/^( {4})+/m', function($m) use ($indent) {
            $level = strlen($m[0]) / 4;
            return str_repeat($indent === 0 ? "\t" : str_repeat(' ', $indent), $level);
        }, $pretty);
    }

    // Stats
    $stats = [
        'keys'    => 0,
        'strings' => 0,
        'numbers' => 0,
        'bools'   => 0,
        'nulls'   => 0,
        'arrays'  => 0,
        'objects' => 0,
        'depth'   => 0,
    ];

    function walk($node, &$stats, $depth = 0) {
        $stats['depth'] = max($stats['depth'], $depth);
        if (is_object($node)) {
            $stats['objects']++;
            foreach ((array)$node as $k => $v) {
                $stats['keys']++;
                walk($v, $stats, $depth + 1);
            }
        } elseif (is_array($node)) {
            $stats['arrays']++;
            foreach ($node as $v) walk($v, $stats, $depth + 1);
        } elseif (is_string($node))  $stats['strings']++;
        elseif (is_int($node) || is_float($node)) $stats['numbers']++;
        elseif (is_bool($node))      $stats['bools']++;
        elseif (is_null($node))      $stats['nulls']++;
    }
    walk($decoded, $stats);

    echo json_encode([
        'valid'     => true,
        'formatted' => $pretty,
        'minified'  => json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'size_raw'  => strlen($raw),
        'size_fmt'  => strlen($pretty),
        'size_min'  => strlen(json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
        'stats'     => $stats,
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>JSON Formatter — Black-Lab Toolbox</title>
<style>
html, body { height: 100%; overflow-y: auto; }
body { display: flex; flex-direction: column; }
.page-wrap { position: relative; z-index: 1; flex: 1; display: flex; flex-direction: column; }
.page-body {
    flex: 1; padding: 1rem 1.4rem 1.6rem;
    display: flex; flex-direction: column; gap: .85rem;
    max-width: 1400px; width: 100%; margin: 0 auto;
}

/* ── Toolbar ── */
.toolbar {
    display: flex; gap: .5rem; align-items: center; flex-wrap: wrap;
}
.toolbar-sep { flex: 1; }

/* ── Editor grid ── */
.editor-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .85rem;
    flex: 1;
}
@media(max-width:760px) { .editor-grid { grid-template-columns: 1fr; } }

/* ── Editor pane ── */
.pane {
    display: flex; flex-direction: column;
    border: 1px solid var(--border); border-radius: var(--radius);
    overflow: hidden; min-height: 420px;
    background: var(--bg-2);
}
.pane-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: .4rem .8rem;
    background: rgba(10,10,18,.5);
    border-bottom: 1px solid var(--border);
    flex-shrink: 0;
}
.pane-title { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--text-muted); }
.pane-actions { display: flex; gap: .35rem; }

textarea.json-ta {
    flex: 1; resize: none; border: none; outline: none;
    background: transparent; color: var(--text);
    font-family: 'Fira Code', 'Cascadia Code', 'Consolas', monospace;
    font-size: .82rem; line-height: 1.65;
    padding: .75rem 1rem;
    tab-size: 2;
}
textarea.json-ta::placeholder { color: var(--text-dim); }
textarea.json-ta.error { color: #ff6b6b; }

/* ── Sortie colorisée ── */
.json-output {
    flex: 1; overflow: auto;
    font-family: 'Fira Code', 'Cascadia Code', 'Consolas', monospace;
    font-size: .82rem; line-height: 1.65;
    padding: .75rem 1rem;
    white-space: pre;
}
/* Syntax highlighting */
.jt-key    { color: #79d4f0; }
.jt-str    { color: #a5d6a7; }
.jt-num    { color: #ffcc80; }
.jt-bool   { color: #ce93d8; }
.jt-null   { color: #ef9a9a; }
.jt-punct  { color: var(--text-muted); }

/* ── Validation badge ── */
.valid-badge {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .18rem .6rem; border-radius: 20px; font-size: .72rem; font-weight: 600;
    border: 1px solid;
}
.valid-badge.ok  { background: rgba(39,201,63,.1);  color: #27c93f; border-color: rgba(39,201,63,.4); }
.valid-badge.bad { background: rgba(255,22,84,.1);  color: #ff1654; border-color: rgba(255,22,84,.4); }
.valid-badge.neu { background: var(--bg-2); color: var(--text-muted); border-color: var(--border); }

/* ── Error line ── */
.error-line {
    padding: .4rem .9rem;
    background: rgba(255,22,84,.08);
    border-top: 1px solid rgba(255,22,84,.2);
    font-size: .75rem; color: #ff6b6b;
    font-family: 'Fira Code', monospace;
    flex-shrink: 0;
}

/* ── Stats bar ── */
.stats-bar {
    display: flex; gap: .5rem; flex-wrap: wrap;
}
.stat-chip {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .18rem .55rem; border-radius: 20px; font-size: .7rem;
    background: var(--bg-2); border: 1px solid var(--border); color: var(--text-muted);
}
.stat-chip strong { color: var(--text); }

/* ── Size info ── */
.size-info { font-size: .7rem; color: var(--text-muted); }

/* ── Indent select ── */
.indent-row { display: flex; align-items: center; gap: .5rem; font-size: .78rem; color: var(--text-muted); }
.indent-row select { width: auto; padding: .25rem .5rem; font-size: .78rem; }

/* ── View tabs ── */
.view-tabs { display: flex; gap: .3rem; }
.vtab {
    padding: .18rem .55rem; border-radius: 6px; font-size: .7rem; font-weight: 600;
    cursor: pointer; border: 1px solid var(--border);
    background: transparent; color: var(--text-muted); transition: all .15s;
}
.vtab.active { background: var(--accent-soft,rgba(255,22,84,.15)); border-color: var(--accent,#ff1654); color: var(--accent,#ff1654); }
</style>
</head>
<body>

<div class="ambient">
  <div class="ambient__circle"></div>
  <div class="ambient__circle"></div>
</div>

<div class="page-wrap">
<header class="hdr">
  <span class="hdr__icon">{ }</span>
  <span class="hdr__title">JSON FORMATTER</span>
  <span class="hdr__sep"></span>
  <span class="hdr__meta">Validation · Formatage · Minification · Syntax highlighting · Stats</span>
</header>
<div class="page-body">

  <!-- Toolbar -->
  <div class="toolbar">
    <span id="valid-badge" class="valid-badge neu">En attente</span>
    <div class="indent-row">
      <span>Indent</span>
      <select id="indent-sel" onchange="process()">
        <option value="2" selected>2 espaces</option>
        <option value="4">4 espaces</option>
        <option value="0">Tab</option>
      </select>
    </div>
    <div class="toolbar-sep"></div>
    <div class="stats-bar" id="stats-bar"></div>
  </div>

  <!-- Editors -->
  <div class="editor-grid">

    <!-- Entrée -->
    <div class="pane" id="pane-in">
      <div class="pane-header">
        <span class="pane-title">Entrée JSON</span>
        <div class="pane-actions">
          <button class="btn btn-ghost btn-sm" onclick="loadSample()">Exemple</button>
          <button class="btn btn-ghost btn-sm" onclick="clearInput()">Effacer</button>
          <button class="btn btn-ghost btn-sm" onclick="pasteClipboard()">📋 Coller</button>
        </div>
      </div>
      <textarea class="json-ta" id="json-input"
        placeholder='Collez votre JSON ici…&#10;&#10;{ "exemple": true }'
        spellcheck="false" autocomplete="off"></textarea>
      <div class="size-info" style="padding:.3rem .8rem;border-top:1px solid var(--border);flex-shrink:0" id="size-in">—</div>
    </div>

    <!-- Sortie -->
    <div class="pane" id="pane-out">
      <div class="pane-header">
        <span class="pane-title">Sortie</span>
        <div class="pane-actions">
          <div class="view-tabs">
            <button class="vtab active" id="tab-pretty" onclick="switchView('pretty')">Formaté</button>
            <button class="vtab" id="tab-mini"   onclick="switchView('mini')">Minifié</button>
          </div>
          <button class="btn btn-ghost btn-sm" onclick="copyOutput()">📋 Copier</button>
        </div>
      </div>
      <div class="json-output" id="json-output">
        <span style="color:var(--text-dim);font-style:italic">Le résultat apparaîtra ici…</span>
      </div>
      <div id="error-line" class="error-line" style="display:none"></div>
      <div class="size-info" style="padding:.3rem .8rem;border-top:1px solid var(--border);flex-shrink:0" id="size-out">—</div>
    </div>

  </div>

</div>
</div>
<div class="toast-area" id="ta"></div>

<script>
const $ = id => document.getElementById(id);
let lastData  = null;
let curView   = 'pretty';
let debounce  = null;

/* ══════════════════════════════════════════
   TRAITEMENT PRINCIPAL
══════════════════════════════════════════ */
async function process() {
  const raw = $('json-input').value;
  $('size-in').textContent = raw.length ? formatSize(raw.length) + ' en entrée' : '—';

  if (!raw.trim()) {
    resetOutput();
    return;
  }

  const fd = new FormData();
  fd.append('action', 'format');
  fd.append('json', raw);
  fd.append('indent', $('indent-sel').value);

  try {
    const res  = await fetch('', { method: 'POST', body: fd });
    const data = await res.json();
    lastData   = data;

    if (!data.valid) {
      showError(data.error, data.line);
    } else {
      showResult(data);
    }
  } catch(e) {
    showError('Erreur serveur : ' + e.message, null);
  }
}

/* ══════════════════════════════════════════
   AFFICHAGE RÉSULTAT
══════════════════════════════════════════ */
function showResult(d) {
  $('valid-badge').textContent = '✓ JSON valide';
  $('valid-badge').className   = 'valid-badge ok';
  $('error-line').style.display = 'none';
  $('json-input').classList.remove('error');

  renderOutput(d);
  renderStats(d.stats, d);
}

function renderOutput(d) {
  if (!d) return;
  const text = curView === 'mini' ? d.minified : d.formatted;
  $('json-output').innerHTML = highlight(text);
  const size = curView === 'mini' ? d.size_min : d.size_fmt;
  $('size-out').textContent  = formatSize(size) + ' en sortie';
}

function showError(msg, line) {
  $('valid-badge').textContent  = '✗ JSON invalide';
  $('valid-badge').className    = 'valid-badge bad';
  $('json-input').classList.add('error');
  $('error-line').style.display = 'block';
  $('error-line').textContent   = '⚠ ' + msg + (line ? ` (ligne ~${line})` : '');
  $('json-output').innerHTML    = `<span style="color:var(--text-dim);font-style:italic">JSON invalide — corrigez l'entrée</span>`;
  $('size-out').textContent     = '—';
  $('stats-bar').innerHTML      = '';
}

function resetOutput() {
  $('valid-badge').textContent  = 'En attente';
  $('valid-badge').className    = 'valid-badge neu';
  $('error-line').style.display = 'none';
  $('json-input').classList.remove('error');
  $('json-output').innerHTML    = `<span style="color:var(--text-dim);font-style:italic">Le résultat apparaîtra ici…</span>`;
  $('size-in').textContent      = '—';
  $('size-out').textContent     = '—';
  $('stats-bar').innerHTML      = '';
  lastData = null;
}

/* ══════════════════════════════════════════
   STATS BAR
══════════════════════════════════════════ */
function renderStats(s, d) {
  const chips = [
    ['🔑', s.keys,    'clé' + (s.keys > 1 ? 's' : '')],
    ['📦', s.objects, 'objet' + (s.objects > 1 ? 's' : '')],
    ['📋', s.arrays,  'tableau' + (s.arrays > 1 ? 'x' : '')],
    ['📝', s.strings, 'chaîne' + (s.strings > 1 ? 's' : '')],
    ['🔢', s.numbers, 'nombre' + (s.numbers > 1 ? 's' : '')],
    ['🔵', s.bools,   'booléen' + (s.bools > 1 ? 's' : '')],
    ['⬜', s.nulls,   'null' + (s.nulls > 1 ? 's' : '')],
    ['📐', s.depth,   'niv. max'],
  ].filter(([,v]) => v > 0);

  $('stats-bar').innerHTML = chips.map(([ico, v, lbl]) =>
    `<span class="stat-chip">${ico} <strong>${v}</strong> ${lbl}</span>`
  ).join('');
}

/* ══════════════════════════════════════════
   SYNTAX HIGHLIGHTING (JS côté client)
══════════════════════════════════════════ */
function highlight(json) {
  if (!json) return '';
  return json
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    .replace(/("(\\u[a-fA-F0-9]{4}|\\[^u]|[^"\\])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, m => {
      if (/^"/.test(m)) {
        if (/:$/.test(m)) return `<span class="jt-key">${m}</span>`;
        return `<span class="jt-str">${m}</span>`;
      }
      if (/true|false/.test(m)) return `<span class="jt-bool">${m}</span>`;
      if (/null/.test(m))       return `<span class="jt-null">${m}</span>`;
      return `<span class="jt-num">${m}</span>`;
    })
    .replace(/([{}\[\],])/g, '<span class="jt-punct">$1</span>');
}

/* ══════════════════════════════════════════
   SWITCH VUE Formaté / Minifié
══════════════════════════════════════════ */
function switchView(v) {
  curView = v;
  $('tab-pretty').classList.toggle('active', v === 'pretty');
  $('tab-mini').classList.toggle('active',   v === 'mini');
  if (lastData?.valid) renderOutput(lastData);
}

/* ══════════════════════════════════════════
   UTILITAIRES
══════════════════════════════════════════ */
function formatSize(b) {
  if (b < 1024) return b + ' o';
  return (b / 1024).toFixed(1) + ' Ko';
}

async function pasteClipboard() {
  try {
    const text = await navigator.clipboard.readText();
    $('json-input').value = text;
    process();
  } catch { toast('Accès presse-papier refusé', 'err'); }
}

function clearInput() {
  $('json-input').value = '';
  resetOutput();
}

function copyOutput() {
  if (!lastData) return;
  const text = curView === 'mini' ? lastData.minified : lastData.formatted;
  navigator.clipboard.writeText(text).then(() => toast('Copié !', 'ok')).catch(() => toast('Échec copie', 'err'));
}

function loadSample() {
  $('json-input').value = JSON.stringify({
    "site": "black-lab.fr",
    "version": 2,
    "actif": true,
    "tags": ["php", "cms", "tools"],
    "config": {
      "debug": false,
      "timezone": "Europe/Paris",
      "max_upload": 10485760
    },
    "auteur": {
      "nom": "D-Goth",
      "roles": ["admin", "dev"]
    },
    "meta": null
  }, null, 2);
  process();
}

function toast(m, t = 'ok') {
  const a = $('ta');
  const e = document.createElement('div');
  e.className = `toast toast--${t}`;
  e.textContent = m;
  a.appendChild(e);
  setTimeout(() => e.remove(), 2500);
}

/* ── Listener avec debounce ── */
$('json-input').addEventListener('input', () => {
  clearTimeout(debounce);
  debounce = setTimeout(process, 350);
});

/* ── Tab key dans le textarea ── */
$('json-input').addEventListener('keydown', e => {
  if (e.key === 'Tab') {
    e.preventDefault();
    const s = e.target.selectionStart;
    const v = e.target.value;
    e.target.value = v.substring(0, s) + '  ' + v.substring(e.target.selectionEnd);
    e.target.selectionStart = e.target.selectionEnd = s + 2;
  }
});
</script>
</body>
</html>
