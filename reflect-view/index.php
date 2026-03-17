<?php
/* ══════════════════════════════════════════════════════════
   API endpoint — diff côté PHP (algo Myers simplifié)
══════════════════════════════════════════════════════════ */
if (isset($_POST['action']) && $_POST['action'] === 'diff') {
    header('Content-Type: application/json');

    $left  = $_POST['left']  ?? '';
    $right = $_POST['right'] ?? '';
    $mode  = $_POST['mode']  ?? 'line'; // line | word | char

    function diffArrays(array $a, array $b): array {
        $m = count($a); $n = count($b);
        $dp = [];
        for ($i = 0; $i <= $m; $i++) $dp[$i][0] = $i;
        for ($j = 0; $j <= $n; $j++) $dp[0][$j] = $j;
        for ($i = 1; $i <= $m; $i++)
            for ($j = 1; $j <= $n; $j++)
                $dp[$i][$j] = $a[$i-1] === $b[$j-1]
                    ? $dp[$i-1][$j-1]
                    : 1 + min($dp[$i-1][$j], $dp[$i][$j-1], $dp[$i-1][$j-1]);

        $result = []; $i = $m; $j = $n;
        while ($i > 0 || $j > 0) {
            if ($i > 0 && $j > 0 && $a[$i-1] === $b[$j-1]) {
                array_unshift($result, ['type'=>'eq', 'val'=>$a[$i-1]]); $i--; $j--;
            } elseif ($j > 0 && ($i === 0 || $dp[$i][$j-1] <= $dp[$i-1][$j])) {
                array_unshift($result, ['type'=>'add', 'val'=>$b[$j-1]]); $j--;
            } else {
                array_unshift($result, ['type'=>'del', 'val'=>$a[$i-1]]); $i--;
            }
        }
        return $result;
    }

    if ($mode === 'line') {
        $la = explode("\n", $left);
        $lb = explode("\n", $right);
        $diff = diffArrays($la, $lb);
    } elseif ($mode === 'word') {
        preg_match_all('/\S+|\s+/', $left,  $wa);
        preg_match_all('/\S+|\s+/', $right, $wb);
        $diff = diffArrays($wa[0], $wb[0]);
    } else {
        $diff = diffArrays(mb_str_split($left), mb_str_split($right));
    }

    /* Stats */
    $added = $deleted = $unchanged = 0;
    foreach ($diff as $d) {
        if ($d['type'] === 'add') $added++;
        elseif ($d['type'] === 'del') $deleted++;
        else $unchanged++;
    }

    echo json_encode([
        'diff'      => $diff,
        'added'     => $added,
        'deleted'   => $deleted,
        'unchanged' => $unchanged,
        'mode'      => $mode,
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Reflect View — Black-Lab Toolbox</title>
<style>
html, body { height: 100%; overflow-y: auto; }
body { display: flex; flex-direction: column; }
.page-wrap { position: relative; z-index: 1; flex: 1; display: flex; flex-direction: column; }
.page-body {
    flex: 1; padding: 1rem 1.4rem 1.6rem;
    display: flex; flex-direction: column; gap: .85rem;
    max-width: 1500px; width: 100%; margin: 0 auto;
}

/* ── Toolbar ── */
.toolbar { display: flex; gap: .5rem; align-items: center; flex-wrap: wrap; }
.toolbar-sep { flex: 1; }

/* ── Mode tabs ── */
.mode-tab {
    padding: .22rem .65rem; border-radius: 6px; font-size: .72rem; font-weight: 700;
    cursor: pointer; border: 1px solid var(--border);
    background: transparent; color: var(--text-muted); transition: all .15s;
    text-transform: uppercase; letter-spacing: .05em;
}
.mode-tab.active {
    background: rgba(255,22,84,.15); border-color: var(--accent,#ff1654);
    color: var(--accent,#ff1654);
}

/* ── Stats chips ── */
.stat-chip {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .18rem .55rem; border-radius: 20px; font-size: .72rem; font-weight: 600;
    border: 1px solid;
}
.stat-chip.add { background: rgba(39,201,63,.1);  color: #27c93f; border-color: rgba(39,201,63,.35); }
.stat-chip.del { background: rgba(255,22,84,.1);  color: #ff1654; border-color: rgba(255,22,84,.35); }
.stat-chip.eq  { background: var(--bg-2); color: var(--text-muted); border-color: var(--border); }

/* ── Editor grid ── */
.editor-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .85rem;
}
@media(max-width:760px) { .editor-grid { grid-template-columns: 1fr; } }

/* ── Pane ── */
.pane {
    display: flex; flex-direction: column;
    border: 1px solid var(--border); border-radius: var(--radius);
    overflow: hidden; min-height: 260px;
    background: var(--bg-2);
}
.pane-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: .4rem .8rem;
    background: rgba(10,10,18,.5);
    border-bottom: 1px solid var(--border); flex-shrink: 0;
}
.pane-title { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--text-muted); }
.pane-actions { display: flex; gap: .35rem; }

textarea.rv-ta {
    flex: 1; resize: none; border: none; outline: none;
    background: transparent; color: var(--text);
    font-family: 'Fira Code', 'Cascadia Code', monospace;
    font-size: .82rem; line-height: 1.65;
    padding: .75rem 1rem; tab-size: 2; min-height: 220px;
}
textarea.rv-ta::placeholder { color: var(--text-dim); }

/* ── Résultat diff ── */
.result-wrap {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .85rem;
}
@media(max-width:760px) { .result-wrap { grid-template-columns: 1fr; } }

.diff-pane {
    border: 1px solid var(--border); border-radius: var(--radius);
    overflow: hidden; background: var(--bg-2);
}
.diff-pane-header {
    padding: .4rem .8rem;
    background: rgba(10,10,18,.5);
    border-bottom: 1px solid var(--border);
    font-size: .68rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .08em; color: var(--text-muted);
    display: flex; align-items: center; justify-content: space-between;
}
.diff-body {
    font-family: 'Fira Code', 'Cascadia Code', monospace;
    font-size: .8rem; line-height: 1.7;
    overflow: auto; max-height: 480px;
}

/* ── Lignes diff ── */
.dl {
    display: flex; align-items: stretch; min-width: max-content;
}
.dl-num {
    flex-shrink: 0; width: 38px; text-align: right; padding: 0 .5rem;
    color: var(--text-dim); font-size: .72rem; user-select: none;
    border-right: 1px solid var(--border);
    display: flex; align-items: center; justify-content: flex-end;
    background: rgba(0,0,0,.2);
}
.dl-sign {
    flex-shrink: 0; width: 22px; text-align: center; padding: 0 .3rem;
    font-weight: 700; display: flex; align-items: center; justify-content: center;
}
.dl-text { padding: 0 .6rem; white-space: pre; flex: 1; }

.dl.eq  { color: var(--text-muted); }
.dl.add { background: rgba(39,201,63,.08);  color: #a5d6a7; border-left: 3px solid #27c93f; }
.dl.del { background: rgba(255,22,84,.08);  color: #ff9a9a; border-left: 3px solid #ff1654; }
.dl.add .dl-sign { color: #27c93f; }
.dl.del .dl-sign { color: #ff1654; }
.dl.eq  .dl-sign { color: var(--text-dim); }

/* Highlights inline pour mode word/char */
.hl-add { background: rgba(39,201,63,.25);  border-radius: 2px; }
.hl-del { background: rgba(255,22,84,.2);   border-radius: 2px; text-decoration: line-through; }

/* ── Unified view ── */
.unified-body {
    font-family: 'Fira Code', monospace; font-size: .8rem; line-height: 1.7;
    overflow: auto; max-height: 480px;
    padding: .5rem 0;
}

/* ── View toggle ── */
.view-toggle { display: flex; gap: .3rem; }
.vtab {
    padding: .18rem .5rem; border-radius: 5px; font-size: .68rem; font-weight: 600;
    cursor: pointer; border: 1px solid var(--border);
    background: transparent; color: var(--text-muted); transition: all .15s;
}
.vtab.active { background: rgba(255,22,84,.12); border-color: #ff1654; color: #ff1654; }

/* ── Placeholder ── */
.placeholder {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; padding: 2.5rem; gap: .7rem;
    color: var(--text-muted); text-align: center;
}
.placeholder-icon { font-size: 2.8rem; opacity: .15; }

/* ── Similarity bar ── */
.sim-bar-bg {
    height: 5px; border-radius: 5px;
    background: var(--bg-2); border: 1px solid var(--border);
    overflow: hidden; flex: 1;
}
.sim-bar-fill { height: 100%; border-radius: 5px; transition: width .6s ease; background: var(--accent,#ff1654); }
</style>
</head>
<body>

<div class="ambient">
  <div class="ambient__circle"></div>
  <div class="ambient__circle"></div>
</div>

<div class="page-wrap">
<header class="hdr">
  <span class="hdr__icon">⟺</span>
  <span class="hdr__title">REFLECT VIEW</span>
  <span class="hdr__sep"></span>
  <span class="hdr__meta">Comparaison de texte · Ligne · Mot · Caractère · Side-by-side</span>
</header>
<div class="page-body">

  <!-- Toolbar -->
  <div class="toolbar">
    <!-- Mode -->
    <span style="font-size:.72rem;color:var(--text-muted)">Mode&nbsp;:</span>
    <button class="mode-tab active" id="mode-line" onclick="setMode('line')">Ligne</button>
    <button class="mode-tab"        id="mode-word" onclick="setMode('word')">Mot</button>
    <button class="mode-tab"        id="mode-char" onclick="setMode('char')">Caractère</button>

    <div class="toolbar-sep"></div>

    <!-- Stats -->
    <div id="stats-bar" style="display:flex;gap:.4rem;align-items:center;flex-wrap:wrap"></div>

    <!-- View -->
    <div class="view-toggle">
      <button class="vtab active" id="vt-side"    onclick="setView('side')">Side-by-side</button>
      <button class="vtab"        id="vt-unified" onclick="setView('unified')">Unifié</button>
    </div>

    <button class="btn btn-ghost btn-sm" onclick="swapTexts()">⇄ Swap</button>
    <button class="btn btn-ghost btn-sm" onclick="clearAll()">✕ Effacer</button>
  </div>

  <!-- Inputs -->
  <div class="editor-grid">
    <div class="pane">
      <div class="pane-header">
        <span class="pane-title" id="label-left">Texte A (original)</span>
        <div class="pane-actions">
          <button class="btn btn-ghost btn-sm" onclick="loadSampleLeft()">Exemple</button>
          <button class="btn btn-ghost btn-sm" onclick="pasteLeft()">📋</button>
        </div>
      </div>
      <textarea class="rv-ta" id="text-left"
        placeholder="Collez ou tapez le texte original…"
        spellcheck="false"></textarea>
    </div>
    <div class="pane">
      <div class="pane-header">
        <span class="pane-title" id="label-right">Texte B (modifié)</span>
        <div class="pane-actions">
          <button class="btn btn-ghost btn-sm" onclick="loadSampleRight()">Exemple</button>
          <button class="btn btn-ghost btn-sm" onclick="pasteRight()">📋</button>
        </div>
      </div>
      <textarea class="rv-ta" id="text-right"
        placeholder="Collez ou tapez le texte modifié…"
        spellcheck="false"></textarea>
    </div>
  </div>

  <!-- Résultat -->
  <div id="result-area">
    <div class="placeholder card" id="placeholder">
      <div class="placeholder-icon">⟺</div>
      <div>Entrez deux textes pour visualiser les différences</div>
      <div style="font-size:.78rem;opacity:.5">La comparaison se lance automatiquement</div>
    </div>
  </div>

</div>
</div>
<div class="toast-area" id="ta"></div>

<script>
const $ = id => document.getElementById(id);
let curMode = 'line';
let curView = 'side';
let lastDiff = null;
let debounce = null;

/* ══════════════════════════════════════════
   DIFF REQUEST
══════════════════════════════════════════ */
async function runDiff() {
  const left  = $('text-left').value;
  const right = $('text-right').value;

  if (!left.trim() && !right.trim()) {
    $('result-area').innerHTML = `
      <div class="placeholder card" id="placeholder">
        <div class="placeholder-icon">⟺</div>
        <div>Entrez deux textes pour visualiser les différences</div>
        <div style="font-size:.78rem;opacity:.5">La comparaison se lance automatiquement</div>
      </div>`;
    $('stats-bar').innerHTML = '';
    return;
  }

  const fd = new FormData();
  fd.append('action', 'diff');
  fd.append('left',  left);
  fd.append('right', right);
  fd.append('mode',  curMode);

  try {
    const res  = await fetch('', { method: 'POST', body: fd });
    lastDiff   = await res.json();
    renderAll(lastDiff);
  } catch(e) { console.error(e); }
}

/* ══════════════════════════════════════════
   RENDER
══════════════════════════════════════════ */
function renderAll(d) {
  renderStats(d);
  if (curView === 'side') renderSide(d);
  else renderUnified(d);
}

/* ── Stats ── */
function renderStats(d) {
  const total = d.added + d.deleted + d.unchanged;
  const sim   = total > 0 ? Math.round(d.unchanged / total * 100) : 100;
  const simColor = sim >= 80 ? '#27c93f' : sim >= 50 ? '#ffbd2e' : '#ff1654';

  $('stats-bar').innerHTML = `
    <span class="stat-chip add">+${d.added} ajout${d.added > 1 ? 's' : ''}</span>
    <span class="stat-chip del">−${d.deleted} suppression${d.deleted > 1 ? 's' : ''}</span>
    <span class="stat-chip eq">${d.unchanged} identique${d.unchanged > 1 ? 's' : ''}</span>
    <span style="display:flex;align-items:center;gap:.4rem;font-size:.72rem;color:var(--text-muted)">
      <span>Similarité</span>
      <div class="sim-bar-bg" style="width:80px">
        <div class="sim-bar-fill" style="width:${sim}%;background:${simColor}"></div>
      </div>
      <span style="color:${simColor};font-weight:700">${sim}%</span>
    </span>`;
}

/* ── Side-by-side ── */
function renderSide(d) {
  const leftLines  = buildSideLines(d, 'del', 'eq');
  const rightLines = buildSideLines(d, 'add', 'eq');

  $('result-area').innerHTML = `
    <div class="result-wrap">
      <div class="diff-pane">
        <div class="diff-pane-header">
          <span>Texte A — original</span>
          <span class="stat-chip del" style="font-size:.65rem">−${d.deleted}</span>
        </div>
        <div class="diff-body">${leftLines}</div>
      </div>
      <div class="diff-pane">
        <div class="diff-pane-header">
          <span>Texte B — modifié</span>
          <span class="stat-chip add" style="font-size:.65rem">+${d.added}</span>
        </div>
        <div class="diff-body">${rightLines}</div>
      </div>
    </div>`;
}

function buildSideLines(d, showType, eqType) {
  let html = ''; let num = 0;
  const isLine = curMode === 'line';

  if (isLine) {
    d.diff.forEach(item => {
      if (item.type === 'add' && showType === 'del') {
        html += `<div class="dl eq"><div class="dl-num"></div><div class="dl-sign"> </div><div class="dl-text"> </div></div>`;
        return;
      }
      if (item.type === 'del' && showType === 'add') {
        html += `<div class="dl eq"><div class="dl-num"></div><div class="dl-sign"> </div><div class="dl-text"> </div></div>`;
        return;
      }
      if (item.type === showType || item.type === eqType) {
        num++;
        const cls  = item.type === eqType ? 'eq' : showType === 'del' ? 'del' : 'add';
        const sign = item.type === eqType ? ' ' : (showType === 'del' ? '−' : '+');
        html += `<div class="dl ${cls}"><div class="dl-num">${num}</div><div class="dl-sign">${sign}</div><div class="dl-text">${escHtml(item.val)}</div></div>`;
      }
    });
  } else {
    // Mode word/char : inline
    num = 1;
    let lineHtml = '';
    d.diff.forEach(item => {
      if (item.type === eqType) {
        lineHtml += escHtml(item.val);
      } else if (item.type === showType) {
        const cls = showType === 'del' ? 'hl-del' : 'hl-add';
        lineHtml += `<span class="${cls}">${escHtml(item.val)}</span>`;
      }
    });
    html = `<div class="dl ${showType === 'del' ? 'del' : 'add'}"><div class="dl-num">${num}</div><div class="dl-sign"> </div><div class="dl-text">${lineHtml}</div></div>`;
  }
  return html || `<div class="dl eq"><div class="dl-num">—</div><div class="dl-sign"> </div><div class="dl-text" style="color:var(--text-dim);font-style:italic">Vide</div></div>`;
}

/* ── Unified ── */
function renderUnified(d) {
  let html = ''; let numL = 0; let numR = 0;

  d.diff.forEach(item => {
    if (item.type === 'eq')  { numL++; numR++; }
    if (item.type === 'del') { numL++; }
    if (item.type === 'add') { numR++; }

    const cls  = item.type === 'eq' ? 'eq' : item.type === 'add' ? 'add' : 'del';
    const sign = item.type === 'eq' ? ' '  : item.type === 'add' ? '+'   : '−';
    const numDisplay = item.type === 'del' ? numL : item.type === 'add' ? numR : numL;

    html += `<div class="dl ${cls}">
      <div class="dl-num">${numDisplay}</div>
      <div class="dl-sign">${sign}</div>
      <div class="dl-text">${escHtml(item.val)}</div>
    </div>`;
  });

  $('result-area').innerHTML = `
    <div class="diff-pane">
      <div class="diff-pane-header">
        <span>Vue unifiée</span>
        <div style="display:flex;gap:.4rem">
          <span class="stat-chip add" style="font-size:.65rem">+${d.added}</span>
          <span class="stat-chip del" style="font-size:.65rem">−${d.deleted}</span>
        </div>
      </div>
      <div class="diff-body unified-body">${html}</div>
    </div>`;
}

/* ══════════════════════════════════════════
   CONTRÔLES
══════════════════════════════════════════ */
function setMode(m) {
  curMode = m;
  ['line','word','char'].forEach(x => $('mode-'+x).classList.toggle('active', x === m));
  triggerDiff();
}

function setView(v) {
  curView = v;
  $('vt-side').classList.toggle('active',    v === 'side');
  $('vt-unified').classList.toggle('active', v === 'unified');
  if (lastDiff) renderAll(lastDiff);
}

function swapTexts() {
  const l = $('text-left').value;
  $('text-left').value  = $('text-right').value;
  $('text-right').value = l;
  triggerDiff();
}

function clearAll() {
  $('text-left').value  = '';
  $('text-right').value = '';
  lastDiff = null;
  $('stats-bar').innerHTML = '';
  $('result-area').innerHTML = `
    <div class="placeholder card">
      <div class="placeholder-icon">⟺</div>
      <div>Entrez deux textes pour visualiser les différences</div>
      <div style="font-size:.78rem;opacity:.5">La comparaison se lance automatiquement</div>
    </div>`;
}

async function pasteLeft()  { try { $('text-left').value  = await navigator.clipboard.readText(); triggerDiff(); } catch { toast('Accès presse-papier refusé','err'); } }
async function pasteRight() { try { $('text-right').value = await navigator.clipboard.readText(); triggerDiff(); } catch { toast('Accès presse-papier refusé','err'); } }

function loadSampleLeft() {
  $('text-left').value = `<?php
function hello(string $name): string {
    return "Hello, " . $name . "!";
}

$result = hello("World");
echo $result;
?>`;
  triggerDiff();
}

function loadSampleRight() {
  $('text-right').value = `<?php
function greet(string $name, string $greeting = "Hello"): string {
    return $greeting . ", " . $name . "!";
}

$result = greet("Black-Lab", "Bienvenue");
echo $result;
?>`;
  triggerDiff();
}

function triggerDiff() {
  clearTimeout(debounce);
  debounce = setTimeout(runDiff, 300);
}

function escHtml(s) {
  return String(s)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function toast(m, t = 'ok') {
  const a = $('ta');
  const e = document.createElement('div');
  e.className = `toast toast--${t}`;
  e.textContent = m;
  a.appendChild(e);
  setTimeout(() => e.remove(), 2500);
}

/* ── Listeners ── */
[$('text-left'), $('text-right')].forEach(el =>
  el.addEventListener('input', triggerDiff)
);
</script>
</body>
</html>
