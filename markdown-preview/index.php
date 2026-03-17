<?php
/* ══════════════════════════════════════════════════════════
   Export HTML — génère un fichier HTML standalone téléchargeable
══════════════════════════════════════════════════════════ */
if (isset($_POST['action']) && $_POST['action'] === 'export') {
    $md    = $_POST['markdown'] ?? '';
    $title = htmlspecialchars($_POST['title'] ?? 'Document', ENT_QUOTES);
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: attachment; filename="document.html"');
    // On échappe la balise PHP pour éviter toute interprétation
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8">
<title>' . $title . '</title>
<style>
body{font-family:system-ui,sans-serif;max-width:800px;margin:2rem auto;padding:0 1rem;line-height:1.7;color:#1a1a2e}
h1,h2,h3{margin-top:2rem;border-bottom:1px solid #e0e0e0;padding-bottom:.4rem}
code{background:#f4f4f8;padding:.15rem .4rem;border-radius:4px;font-size:.88em}
pre{background:#f4f4f8;padding:1rem;border-radius:8px;overflow-x:auto}
pre code{background:none;padding:0}
blockquote{border-left:4px solid #ddd;margin:0;padding:.5rem 1rem;color:#555}
table{border-collapse:collapse;width:100%}td,th{border:1px solid #ddd;padding:.5rem .8rem}
th{background:#f4f4f8}img{max-width:100%}a{color:#0066cc}
</style></head><body>';
    echo '<div id="content"></div>
<script src="https://cdn.jsdelivr.net/npm/marked@9/marked.min.js"></scr' . 'ipt>
<script>
document.getElementById("content").innerHTML = marked.parse(' . json_encode($md) . ');
</scr' . 'ipt></body></html>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Markdown Preview — Black-Lab Toolbox</title>
<script src="https://cdn.jsdelivr.net/npm/marked@9/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/highlight.js@11/build/highlight.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/highlight.js@11/styles/atom-one-dark.min.css">
<style>
html, body { height: 100%; overflow: hidden; }
body { display: flex; flex-direction: column; }
.page-wrap { position: relative; z-index: 1; flex: 1; min-height: 0; display: flex; flex-direction: column; }
.page-body {
    flex: 1; min-height: 0;
    padding: .8rem 1.2rem 1rem;
    display: flex; flex-direction: column; gap: .7rem;
    max-width: 100%; width: 100%; margin: 0 auto;
    box-sizing: border-box;
}

/* ── Toolbar ── */
.toolbar {
    flex-shrink: 0;
    display: flex; gap: .45rem; align-items: center; flex-wrap: wrap;
}
.toolbar-sep { flex: 1; }

/* ── Stats ── */
.md-stat {
    font-size: .72rem; color: var(--text-muted);
    display: flex; align-items: center; gap: .3rem;
}
.md-stat strong { color: var(--text); }

/* ── Format buttons ── */
.fmt-btn {
    width: 28px; height: 28px; border-radius: 5px;
    display: flex; align-items: center; justify-content: center;
    font-size: .78rem; font-weight: 700; cursor: pointer;
    border: 1px solid var(--border); background: transparent;
    color: var(--text-muted); transition: all .15s; flex-shrink: 0;
    font-family: 'Fira Code', monospace;
}
.fmt-btn:hover { border-color: var(--accent,#ff1654); color: var(--accent,#ff1654); background: rgba(255,22,84,.08); }

/* ── View tabs ── */
.vtab {
    padding: .2rem .6rem; border-radius: 6px; font-size: .72rem; font-weight: 600;
    cursor: pointer; border: 1px solid var(--border);
    background: transparent; color: var(--text-muted); transition: all .15s;
}
.vtab.active { background: rgba(255,22,84,.12); border-color: var(--accent,#ff1654); color: var(--accent,#ff1654); }

/* ── Editor layout ── */
.editor-wrap {
    flex: 1; min-height: 0;
    display: grid;
    gap: .7rem;
}
.editor-wrap.split   { grid-template-columns: 1fr 1fr; }
.editor-wrap.editor  { grid-template-columns: 1fr; }
.editor-wrap.preview { grid-template-columns: 1fr; }

@media(max-width:760px) { .editor-wrap.split { grid-template-columns: 1fr; } }

/* ── Panes ── */
.pane {
    min-height: 0; display: flex; flex-direction: column;
    border: 1px solid var(--border); border-radius: var(--radius);
    overflow: hidden; background: var(--bg-2);
}
.pane-header {
    flex-shrink: 0; padding: .38rem .8rem;
    background: rgba(10,10,18,.5); border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
}
.pane-title { font-size: .67rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--text-muted); }

/* ── Textarea ── */
textarea#md-input {
    flex: 1; resize: none; border: none; outline: none;
    background: transparent; color: var(--text);
    font-family: 'Fira Code', 'Cascadia Code', monospace;
    font-size: .83rem; line-height: 1.7;
    padding: .9rem 1rem; tab-size: 2;
    min-height: 0;
}
textarea#md-input::placeholder { color: var(--text-dim); }

/* ── Preview ── */
.preview-body {
    flex: 1; overflow-y: auto; padding: 1.2rem 1.4rem;
    min-height: 0;
}

/* ── Markdown styles dans la preview ── */
.md-render { color: var(--text); line-height: 1.8; }
.md-render h1 { font-size: 1.6rem; font-weight: 800; margin: 0 0 1rem; padding-bottom: .5rem; border-bottom: 2px solid var(--border); color: var(--text); }
.md-render h2 { font-size: 1.25rem; font-weight: 700; margin: 1.5rem 0 .75rem; padding-bottom: .35rem; border-bottom: 1px solid var(--border); color: var(--text); }
.md-render h3 { font-size: 1.05rem; font-weight: 700; margin: 1.2rem 0 .5rem; color: var(--text); }
.md-render h4,
.md-render h5,
.md-render h6 { font-weight: 700; margin: 1rem 0 .4rem; color: var(--text-muted); }
.md-render p  { margin: 0 0 .9rem; }
.md-render a  { color: var(--accent,#ff1654); text-decoration: underline; }
.md-render a:hover { opacity: .8; }
.md-render strong { font-weight: 700; color: var(--text); }
.md-render em     { font-style: italic; color: var(--text-muted); }
.md-render code {
    font-family: 'Fira Code', monospace; font-size: .82em;
    background: rgba(255,255,255,.06); border: 1px solid var(--border);
    border-radius: 4px; padding: .1rem .4rem; color: #ce93d8;
}
.md-render pre {
    background: #1a1a24; border: 1px solid var(--border); border-radius: 8px;
    overflow-x: auto; margin: .8rem 0; padding: 0;
}
.md-render pre code {
    background: none; border: none; padding: 1rem; display: block;
    font-size: .8rem; color: var(--text); border-radius: 0;
}
.md-render blockquote {
    border-left: 3px solid var(--accent,#ff1654);
    margin: .8rem 0; padding: .5rem 1rem;
    background: rgba(255,22,84,.05); border-radius: 0 6px 6px 0;
    color: var(--text-muted); font-style: italic;
}
.md-render ul, .md-render ol { padding-left: 1.5rem; margin: .5rem 0 .9rem; }
.md-render li  { margin: .25rem 0; }
.md-render hr  { border: none; border-top: 1px solid var(--border); margin: 1.5rem 0; }
.md-render table { width: 100%; border-collapse: collapse; margin: .8rem 0; font-size: .85rem; }
.md-render th { background: rgba(255,255,255,.05); border: 1px solid var(--border); padding: .45rem .7rem; text-align: left; font-weight: 700; }
.md-render td { border: 1px solid var(--border); padding: .4rem .7rem; }
.md-render tr:hover td { background: rgba(255,255,255,.03); }
.md-render img { max-width: 100%; border-radius: 6px; margin: .5rem 0; }

/* ── Highlight.js override ── */
.md-render .hljs { background: transparent; padding: 0; }

/* ── Scroll bar ── */
.preview-body::-webkit-scrollbar { width: 5px; }
.preview-body::-webkit-scrollbar-track { background: transparent; }
.preview-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 5px; }

/* ── TOC ── */
.toc-panel {
    flex-shrink: 0; max-height: 160px; overflow-y: auto;
    padding: .5rem .8rem;
    border-top: 1px solid var(--border);
    background: rgba(0,0,0,.2);
}
.toc-item { font-size: .75rem; padding: .15rem 0; color: var(--text-muted); cursor: pointer; }
.toc-item:hover { color: var(--accent,#ff1654); }
.toc-item.h2 { padding-left: 1rem; }
.toc-item.h3 { padding-left: 2rem; }
</style>
</head>
<body>

<div class="ambient">
  <div class="ambient__circle"></div>
  <div class="ambient__circle"></div>
</div>

<div class="page-wrap">
<header class="hdr">
  <span class="hdr__icon">📝</span>
  <span class="hdr__title">MARKDOWN PREVIEW</span>
  <span class="hdr__sep"></span>
  <span class="hdr__meta">Éditeur split · Syntax highlighting · TOC · Export HTML</span>
</header>
<div class="page-body">

  <!-- Toolbar -->
  <div class="toolbar">
    <button class="fmt-btn" title="Gras"        onclick="wrap('**','**')"><b>B</b></button>
    <button class="fmt-btn" title="Italique"     onclick="wrap('*','*')"><i>I</i></button>
    <button class="fmt-btn" title="Code inline"  onclick="wrap('`','`')">&#96;</button>
    <button class="fmt-btn" title="Lien"         onclick="insertLink()">🔗</button>
    <button class="fmt-btn" title="Image"        onclick="insertImg()">🖼</button>
    <button class="fmt-btn" title="Tableau"      onclick="insertTable()">⊞</button>
    <button class="fmt-btn" title="Bloc de code" onclick="insertCode()">{ }</button>
    <button class="fmt-btn" title="Citation"     onclick="insertQuote()">"</button>
    <button class="fmt-btn" title="H1"           onclick="insertHeading(1)">H1</button>
    <button class="fmt-btn" title="H2"           onclick="insertHeading(2)">H2</button>
    <button class="fmt-btn" title="H3"           onclick="insertHeading(3)">H3</button>
    <button class="fmt-btn" title="Liste"        onclick="insertList()">☰</button>

    <div class="toolbar-sep"></div>

    <div class="md-stat">
      <strong id="stat-words">0</strong> mots &nbsp;·&nbsp;
      <strong id="stat-chars">0</strong> car. &nbsp;·&nbsp;
      <strong id="stat-lines">0</strong> lignes
    </div>

    <div class="toolbar-sep"></div>

    <button class="vtab active" id="vt-split"   onclick="setView('split')">⊞ Split</button>
    <button class="vtab"        id="vt-editor"  onclick="setView('editor')">✏ Éditeur</button>
    <button class="vtab"        id="vt-preview" onclick="setView('preview')">👁 Aperçu</button>

    <button class="btn btn-ghost btn-sm" onclick="loadSample()">Exemple</button>
    <button class="btn btn-ghost btn-sm" onclick="clearAll()">✕</button>
    <button class="btn btn-primary btn-sm" onclick="exportHTML()">⬇ HTML</button>
  </div>

  <!-- Editor -->
  <div class="editor-wrap split" id="editor-wrap">

    <div class="pane" id="pane-editor">
      <div class="pane-header">
        <span class="pane-title">✏ Markdown</span>
        <div style="display:flex;gap:.35rem">
          <button class="btn btn-ghost btn-sm" onclick="pasteClipboard()">📋 Coller</button>
          <button class="btn btn-ghost btn-sm" onclick="copyMD()">Copier MD</button>
        </div>
      </div>
      <textarea id="md-input" spellcheck="false"
        placeholder="# Titre&#10;&#10;Écrivez votre **Markdown** ici…"></textarea>
    </div>

    <div class="pane" id="pane-preview">
      <div class="pane-header">
        <span class="pane-title">👁 Aperçu</span>
        <div style="display:flex;gap:.35rem">
          <button class="btn btn-ghost btn-sm" onclick="toggleTOC()" id="toc-btn">TOC ▶</button>
          <button class="btn btn-ghost btn-sm" onclick="copyHTML()">Copier HTML</button>
        </div>
      </div>
      <div id="toc-panel" class="toc-panel" style="display:none"></div>
      <div class="preview-body">
        <div class="md-render" id="preview-out"></div>
      </div>
    </div>

  </div>

</div>
</div>
<div class="toast-area" id="ta"></div>

<script>
const $ = id => document.getElementById(id);
let curView = 'split';
let tocVisible = false;
let debounce = null;

/* ── Marked config ── */
marked.setOptions({
  highlight: function(code, lang) {
    if (lang && hljs.getLanguage(lang)) {
      try { return hljs.highlight(code, { language: lang }).value; } catch(e) {}
    }
    return hljs.highlightAuto(code).value;
  },
  breaks: true,
  gfm: true,
});

/* ── Render ── */
function render() {
  var md  = $('md-input').value;
  var out = $('preview-out');
  out.innerHTML = marked.parse(md);
  updateStats(md);
  updateTOC();
}

function updateStats(md) {
  var words = md.trim() ? md.trim().split(/\s+/).length : 0;
  $('stat-words').textContent = words;
  $('stat-chars').textContent = md.length;
  $('stat-lines').textContent = md.split('\n').length;
}

function updateTOC() {
  var headings = $('preview-out').querySelectorAll('h1,h2,h3');
  if (!headings.length) {
    $('toc-panel').innerHTML = '<div style="font-size:.75rem;color:var(--text-dim);font-style:italic">Aucun titre trouvé</div>';
    return;
  }
  $('toc-panel').innerHTML = Array.from(headings).map(function(h) {
    var cls = h.tagName.toLowerCase();
    var id  = h.textContent.toLowerCase().replace(/\s+/g,'-').replace(/[^a-z0-9-]/g,'');
    h.id = id;
    return '<div class="toc-item ' + cls + '" onclick="scrollToId(\'' + id + '\')">' + h.textContent + '</div>';
  }).join('');
}

function scrollToId(id) {
  var el = document.getElementById(id);
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/* ── Vue ── */
function setView(v) {
  curView = v;
  ['split','editor','preview'].forEach(function(x) {
    $('vt-'+x).classList.toggle('active', x === v);
  });
  $('editor-wrap').className = 'editor-wrap ' + v;
  $('pane-editor').style.display  = v === 'preview' ? 'none' : 'flex';
  $('pane-preview').style.display = v === 'editor'  ? 'none' : 'flex';
}

function toggleTOC() {
  tocVisible = !tocVisible;
  $('toc-panel').style.display = tocVisible ? 'block' : 'none';
  $('toc-btn').textContent     = tocVisible ? 'TOC ▼' : 'TOC ▶';
}

/* ── Formatage ── */
function wrap(before, after) {
  var ta  = $('md-input');
  var s   = ta.selectionStart, e = ta.selectionEnd;
  var sel = ta.value.substring(s, e) || 'texte';
  insertAt(before + sel + after, s, e, before.length, before.length + sel.length);
}

function insertAt(text, start, end, selStart, selEnd) {
  var ta = $('md-input');
  var v  = ta.value;
  ta.value = v.substring(0, start) + text + v.substring(end);
  ta.selectionStart = start + (selStart != null ? selStart : 0);
  ta.selectionEnd   = start + (selEnd   != null ? selEnd   : text.length);
  ta.focus();
  triggerRender();
}

function insertLink() {
  var ta  = $('md-input');
  var sel = ta.value.substring(ta.selectionStart, ta.selectionEnd) || 'texte';
  insertAt('[' + sel + '](https://url.com)', ta.selectionStart, ta.selectionEnd, 1, 1 + sel.length);
}

function insertImg() {
  var ta = $('md-input');
  insertAt('![alt](https://url.com/image.png)', ta.selectionStart, ta.selectionEnd, 2, 5);
}

function insertTable() {
  var t = '\n| Colonne 1 | Colonne 2 | Colonne 3 |\n|-----------|-----------|----------|\n| cellule   | cellule   | cellule   |\n';
  var ta = $('md-input');
  insertAt(t, ta.selectionStart, ta.selectionEnd);
}

function insertCode() {
  var ta  = $('md-input');
  var sel = ta.value.substring(ta.selectionStart, ta.selectionEnd) || 'code';
  insertAt('```js\n' + sel + '\n```', ta.selectionStart, ta.selectionEnd, 4, 4 + sel.length);
}

function insertQuote() {
  var ta  = $('md-input');
  var sel = ta.value.substring(ta.selectionStart, ta.selectionEnd) || 'Citation';
  insertAt('\n> ' + sel, ta.selectionStart, ta.selectionEnd, 3, 3 + sel.length);
}

function insertHeading(n) {
  var ta  = $('md-input');
  var sel = ta.value.substring(ta.selectionStart, ta.selectionEnd) || 'Titre';
  var pre = Array(n+1).join('#') + ' ';
  insertAt('\n' + pre + sel, ta.selectionStart, ta.selectionEnd, 1 + pre.length, 1 + pre.length + sel.length);
}

function insertList() {
  var ta = $('md-input');
  insertAt('\n- Item 1\n- Item 2\n- Item 3\n', ta.selectionStart, ta.selectionEnd, 3, 9);
}

/* ── Clipboard ── */
function pasteClipboard() {
  navigator.clipboard.readText().then(function(text) {
    $('md-input').value = text; triggerRender();
  }).catch(function() { toast('Accès presse-papier refusé', 'err'); });
}

function copyMD() {
  navigator.clipboard.writeText($('md-input').value)
    .then(function() { toast('Markdown copié !'); })
    .catch(function() { toast('Échec', 'err'); });
}

function copyHTML() {
  navigator.clipboard.writeText($('preview-out').innerHTML)
    .then(function() { toast('HTML copié !'); })
    .catch(function() { toast('Échec', 'err'); });
}

/* ── Export HTML ── */
function exportHTML() {
  var fd = new FormData();
  fd.append('action',   'export');
  fd.append('markdown', $('md-input').value);
  var firstH1 = $('md-input').value.match(/^#\s+(.+)/m);
  fd.append('title', firstH1 ? firstH1[1] : 'Document');
  fetch('', { method: 'POST', body: fd })
    .then(function(r) { return r.blob(); })
    .then(function(b) {
      var a = document.createElement('a');
      a.href = URL.createObjectURL(b);
      a.download = 'document.html';
      a.click();
      toast('Export HTML téléchargé !');
    });
}

/* ── Sample — NOTE : pas de balise PHP dans le sample ── */
function loadSample() {
  /* On construit la chaîne sans écrire la balise ouvrante PHP directement
     pour éviter que PHP l'interprète lors du parsing du fichier.        */
  var phpOpen = '<' + '?php';
  $('md-input').value = [
    '# Black-Lab Toolbox',
    '',
    'Bienvenue dans la **documentation** de la toolbox *Black-Lab*.',
    '',
    '## Fonctionnalités',
    '',
    '- 🔒 SSL & Domain Checker',
    '- 🛡️ HTTP Headers Analyzer',
    '- 🌐 DNS Lookup',
    '- { } JSON Formatter',
    '- ⟺ ReflectView — comparaison de texte',
    '- 📝 Markdown Preview',
    '',
    '## Exemple de code',
    '',
    '```php',
    phpOpen,
    'function renderTool(string $name): string {',
    '    return "<div class=\'tool\'>{$name}</div>";',
    '}',
    'echo renderTool("Markdown Preview");',
    '```',
    '',
    '## Tableau comparatif',
    '',
    '| Outil            | Type      | API externe |',
    '|------------------|-----------|-------------|',
    '| SSL Checker      | PHP natif | Non         |',
    '| DNS Lookup       | JS fetch  | dns.google  |',
    '| Markdown Preview | JS (marked)| CDN        |',
    '',
    '## Citation',
    '',
    '> Un bon outil, c\'est celui qu\'on n\'a pas besoin d\'expliquer.',
    '',
    '## Liens utiles',
    '',
    '- [Black-Lab](https://black-lab.fr)',
    '- [Open-Meteo](https://api.open-meteo.com)',
    '',
    '---',
    '',
    '*Généré avec ❤ par Black-Lab Toolbox*',
  ].join('\n');
  triggerRender();
}

function clearAll() {
  $('md-input').value = '';
  $('preview-out').innerHTML = '';
  updateStats('');
}

/* ── Debounce render ── */
function triggerRender() {
  clearTimeout(debounce);
  debounce = setTimeout(render, 150);
}

$('md-input').addEventListener('input', triggerRender);

/* ── Tab key ── */
$('md-input').addEventListener('keydown', function(e) {
  if (e.key === 'Tab') {
    e.preventDefault();
    var s = e.target.selectionStart;
    var v = e.target.value;
    e.target.value = v.substring(0, s) + '  ' + v.substring(e.target.selectionEnd);
    e.target.selectionStart = e.target.selectionEnd = s + 2;
  }
});

function toast(m, t) {
  t = t || 'ok';
  var a = $('ta');
  var e = document.createElement('div');
  e.className = 'toast toast--' + t;
  e.textContent = m;
  a.appendChild(e);
  setTimeout(function() { e.remove(); }, 2500);
}

/* ── Init ── */
loadSample();
</script>
</body>
</html>