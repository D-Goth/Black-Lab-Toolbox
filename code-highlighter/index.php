<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Code Highlighter — Black-Lab Toolbox</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css" id="highlightTheme">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/python.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/php.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/java.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/cpp.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/sql.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/bash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/json.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/typescript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/go.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/rust.min.js"></script>
<style>
html, body { height: 100%; overflow: hidden; }
body { display: flex; flex-direction: column; }

.page-wrap {
  position: relative; z-index: 1;
  flex: 1; min-height: 0;
  display: flex; flex-direction: column;
}
.page-body {
  flex: 1; min-height: 0;
  display: flex; flex-direction: column;
  gap: .6rem;
  padding: .8rem 1.2rem;
  overflow-y: auto;
}

/* ── Sélecteurs ─────────────────────────────────────────────── */
.selectors-bar {
  flex-shrink: 0;
  display: flex; gap: .8rem; flex-wrap: wrap;
}
.selector-group { display: flex; flex-direction: column; gap: .3rem; flex: 1; min-width: 200px; }
.selector-label {
  font-size: .68rem; text-transform: uppercase; letter-spacing: 1px;
  color: var(--text-muted); font-family: var(--font-mono);
}
.pills { display: flex; flex-wrap: wrap; gap: .25rem; }
.pill {
  padding: .2rem .55rem;
  background: var(--surface-h); border: 1px solid var(--border);
  border-radius: 12px; color: var(--text-muted);
  font-size: .7rem; font-weight: 600; font-family: var(--font-mono);
  cursor: pointer; transition: all var(--transition); line-height: 1.5;
}
.pill:hover { border-color: var(--accent); color: var(--text); }
.pill.active { background: var(--accent-soft); border-color: var(--accent); color: var(--accent); }

/* ── Snippet bar ────────────────────────────────────────────── */
.snippet-bar {
  flex-shrink: 0;
  display: flex; align-items: center; gap: .8rem;
  padding: .4rem .8rem;
  background: var(--surface-h); border: 1px solid var(--border);
  border-radius: var(--radius-sm);
}
.snippet-title-input {
  background: transparent; border: none; outline: none;
  color: var(--text-muted); font-family: var(--font-mono);
  font-size: .78rem; flex: 1; min-width: 0;
}
.snippet-title-input::placeholder { color: var(--text-dim); }
.snippet-title-input:focus { color: var(--text); }
.stat-pill { font-family: var(--font-mono); font-size: .7rem; color: var(--text-muted); white-space: nowrap; }
.stat-pill span { color: var(--accent); font-weight: 700; }

/* ── Panels ─────────────────────────────────────────────────── */
.panels {
  flex: 1; min-height: 0;
  display: grid; grid-template-columns: 1fr 1fr; gap: .8rem;
}
@media(max-width:860px) { .panels { grid-template-columns: 1fr; } }
.panel { display: flex; flex-direction: column; min-height: 0; gap: .5rem; }

/* ── Source textarea ────────────────────────────────────────── */
.source-card {
  flex: 1; min-height: 0;
  display: flex; flex-direction: column;
  border: 1px solid var(--border); border-radius: var(--radius);
  overflow: hidden; background: var(--surface-h);
}
.card-topbar {
  flex-shrink: 0;
  display: flex; align-items: center; justify-content: space-between;
  padding: .4rem .8rem;
  background: rgba(10,10,18,.6);
  border-bottom: 1px solid var(--border);
}
.card-topbar-label {
  font-size: .7rem; font-family: var(--font-mono);
  color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;
}
.source-ta {
  flex: 1; min-height: 0;
  width: 100%; padding: .85rem 1rem;
  background: #1a1a22;
  color: #e8e8f0;
  border: none; outline: none; resize: none;
  font-family: var(--font-mono); font-size: .8rem; line-height: 1.6;
  tab-size: 4; box-sizing: border-box;
  overflow: auto;
}
.source-ta::placeholder { color: #555; }

/* ── Résultat ───────────────────────────────────────────────── */
.output-card {
  flex: 1; min-height: 0;
  display: flex; flex-direction: column;
  border: 1px solid var(--border); border-radius: var(--radius);
  overflow: hidden;
}
.macos-bar {
  flex-shrink: 0;
  display: flex; align-items: center; gap: .5rem;
  padding: .5rem .9rem;
  background: rgba(10,10,18,.7);
  border-bottom: 1px solid var(--border);
}
.macos-dots { display: flex; gap: .35rem; }
.mac-dot { width: 11px; height: 11px; border-radius: 50%; }
.mac-dot.r { background: #ff5f56; }
.mac-dot.y { background: #ffbd2e; }
.mac-dot.g { background: #27c93f; }
.code-display {
  flex: 1; min-height: 0;
  overflow: auto; padding: .85rem 1rem;
  background: var(--surface-h);
}
.code-display pre { margin: 0; background: transparent !important; border: none; padding: 0; }
.code-display code.hljs { background: transparent !important; padding: 0 !important; font-size: .8rem; line-height: 1.6; }

/* ── Bouton inline ──────────────────────────────────────────── */
.btn-inline {
  padding: .18rem .55rem;
  background: var(--surface-h); border: 1px solid var(--border);
  border-radius: var(--radius-sm); color: var(--text-muted);
  font-size: .68rem; font-weight: 600; cursor: pointer;
  transition: all var(--transition); font-family: inherit; white-space: nowrap;
}
.btn-inline:hover { color: var(--text); border-color: var(--accent); }

/* ── Bottom row ─────────────────────────────────────────────── */
.bottom-row { flex-shrink: 0; display: grid; grid-template-columns: 1fr 1fr; gap: .4rem; }
.integration-btn {
  display: flex; align-items: center; gap: .5rem;
  padding: .4rem .7rem;
  background: var(--surface-h); border: 1px solid var(--border);
  border-radius: var(--radius-sm); cursor: pointer;
  transition: all var(--transition); text-align: left; width: 100%; font-family: inherit;
}
.integration-btn:hover { border-color: var(--accent); background: var(--accent-soft); }
.ib-title { font-size: .76rem; font-weight: 700; color: var(--text); display: block; }
.ib-desc  { font-size: .65rem; color: var(--text-muted); font-family: var(--font-mono); }

/* ── Actions ────────────────────────────────────────────────── */
.actions-bar { flex-shrink: 0; display: flex; gap: .5rem; flex-wrap: wrap; align-items: center; }
</style>
</head>
<body>

<div class="ambient">
  <div class="ambient__circle"></div>
  <div class="ambient__circle"></div>
</div>

<div class="page-wrap">
<header class="hdr">
  <span class="hdr__icon">🖌️</span>
  <span class="hdr__title">CODE HIGHLIGHTER</span>
  <span class="hdr__sep"></span>
  <span class="hdr__meta">Coloration syntaxique · 15 langages · 8 thèmes · export HTML / JSON</span>
</header>

<div class="page-body">

  <!-- Sélecteurs -->
  <div class="selectors-bar">
    <div class="selector-group">
      <div class="selector-label">Langage</div>
      <div class="pills" id="langSelector"></div>
    </div>
    <div class="selector-group">
      <div class="selector-label">Thème</div>
      <div class="pills" id="themeSelector"></div>
    </div>
  </div>

  <!-- Snippet bar -->
  <div class="snippet-bar">
    <span style="color:var(--text-dim);font-size:.8rem">🏷️</span>
    <input class="snippet-title-input" id="snippetTitle" placeholder="Titre du snippet…" maxlength="80" autocomplete="off">
    <div class="stat-pill">Lignes <span id="lineCount">0</span></div>
    <div class="stat-pill">Mots <span id="wordCount">0</span></div>
    <div class="stat-pill">Chars <span id="charCount">0</span></div>
  </div>

  <!-- Panels -->
  <div class="panels">

    <!-- SOURCE -->
    <div class="panel">
      <div class="source-card">
        <div class="card-topbar">
          <span class="card-topbar-label">Source</span>
          <button class="btn-inline" onclick="doFormat()">🔧 Formater</button>
        </div>
        <textarea class="source-ta" id="codeInput" spellcheck="false" autocomplete="off"
          placeholder="Collez votre code ici… (Ctrl+Enter pour coloriser)">function fibonacci(n) {
    if (n <= 1) return n;
    return fibonacci(n - 1) + fibonacci(n - 2);
}

console.log(fibonacci(10)); // 55

class Calculator {
    constructor() { this.result = 0; }
    add(v)      { this.result += v; return this; }
    multiply(v) { this.result *= v; return this; }
    getResult() { return this.result; }
}

const result = new Calculator().add(5).multiply(3).getResult();
console.log(result); // 15</textarea>
      </div>
      <div class="actions-bar">
        <button class="btn btn-primary" onclick="doHighlight()">🎨 Coloriser</button>
        <button class="btn btn-ghost"   onclick="doSave()">💾 Sauvegarder</button>
        <button class="btn btn-danger btn-sm" onclick="doClear()" style="margin-left:auto">🗑️ Effacer</button>
      </div>
    </div>

    <!-- RÉSULTAT -->
    <div class="panel">
      <div class="output-card">
        <div class="macos-bar">
          <div class="macos-dots">
            <div class="mac-dot r"></div>
            <div class="mac-dot y"></div>
            <div class="mac-dot g"></div>
          </div>
          <span id="previewTitle" style="flex:1;font-size:.75rem;color:var(--text-muted);font-family:var(--font-mono)">snippet.js</span>
          <button class="btn-inline" onclick="doCopyHTML()">📋 Copier HTML</button>
        </div>
        <div class="code-display" id="codeDisplay">
          <pre><code id="highlightedCode" class="language-javascript">// Collez du code à gauche puis cliquez sur Coloriser…</code></pre>
        </div>
      </div>

      <div class="bottom-row">
        <button class="integration-btn" onclick="doCopyRaw()">
          <span>📄</span><div><span class="ib-title">Code brut</span><span class="ib-desc">Texte pur</span></div>
        </button>
        <button class="integration-btn" onclick="doCopyEmbed()">
          <span>🔗</span><div><span class="ib-title">&lt;pre&gt;&lt;code&gt;</span><span class="ib-desc">Prêt HTML</span></div>
        </button>
        <button class="integration-btn" onclick="doExportJSON()">
          <span>💾</span><div><span class="ib-title">Export JSON</span><span class="ib-desc">Réimportable</span></div>
        </button>
        <button class="integration-btn" onclick="doExportHTML()">
          <span>📄</span><div><span class="ib-title">Export HTML</span><span class="ib-desc">Fichier autonome</span></div>
        </button>
      </div>
    </div>

  </div><!-- /panels -->

</div><!-- /page-body -->
</div><!-- /page-wrap -->
<div class="toast-area" id="toastArea"></div>

<script>
'use strict';

const languages = [
  { id:'javascript', name:'JavaScript', ext:'js'   },
  { id:'typescript', name:'TypeScript', ext:'ts'   },
  { id:'python',     name:'Python',     ext:'py'   },
  { id:'php',        name:'PHP',        ext:'php'  },
  { id:'html',       name:'HTML',       ext:'html' },
  { id:'css',        name:'CSS',        ext:'css'  },
  { id:'java',       name:'Java',       ext:'java' },
  { id:'cpp',        name:'C++',        ext:'cpp'  },
  { id:'sql',        name:'SQL',        ext:'sql'  },
  { id:'bash',       name:'Bash',       ext:'sh'   },
  { id:'json',       name:'JSON',       ext:'json' },
  { id:'xml',        name:'XML',        ext:'xml'  },
  { id:'go',         name:'Go',         ext:'go'   },
  { id:'rust',       name:'Rust',       ext:'rs'   },
  { id:'ruby',       name:'Ruby',       ext:'rb'   },
];
const themes = [
  { id:'github-dark',    name:'GitHub Dark'  },
  { id:'github',         name:'GitHub Light' },
  { id:'vs2015',         name:'VS Dark'      },
  { id:'vs',             name:'VS Light'     },
  { id:'atom-one-dark',  name:'Atom Dark'    },
  { id:'atom-one-light', name:'Atom Light'   },
  { id:'monokai',        name:'Monokai'      },
  { id:'dracula',        name:'Dracula'      },
];

let currentLanguage = 'javascript';
let currentTheme    = 'github-dark';

function toast(msg, type = 'ok') {
  const area = document.getElementById('toastArea');
  const el   = document.createElement('div');
  el.className   = `toast toast--${type}`;
  el.textContent = msg;
  area.appendChild(el);
  setTimeout(() => el.remove(), 2800);
}

function initSelectors() {
  // Langages
  const lc = document.getElementById('langSelector');
  languages.forEach(lang => {
    const btn = document.createElement('button');
    btn.className   = 'pill' + (lang.id === currentLanguage ? ' active' : '');
    btn.textContent = lang.name;
    btn.addEventListener('click', () => {
      document.querySelectorAll('#langSelector .pill').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentLanguage = lang.id;
      updatePreviewTitle();
      doHighlight();
    });
    lc.appendChild(btn);
  });
  // Thèmes
  const tc = document.getElementById('themeSelector');
  themes.forEach(theme => {
    const btn = document.createElement('button');
    btn.className   = 'pill' + (theme.id === currentTheme ? ' active' : '');
    btn.textContent = theme.name;
    btn.addEventListener('click', () => {
      document.querySelectorAll('#themeSelector .pill').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentTheme = theme.id;
      document.getElementById('highlightTheme').href =
        `https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/${theme.id}.min.css`;
      doHighlight();
    });
    tc.appendChild(btn);
  });
}

function updatePreviewTitle() {
  const lang  = languages.find(l => l.id === currentLanguage);
  const title = document.getElementById('snippetTitle').value.trim() || 'snippet';
  document.getElementById('previewTitle').textContent = `${title}.${lang.ext}`;
}

function updateStats() {
  const code = document.getElementById('codeInput').value;
  document.getElementById('lineCount').textContent = code ? code.split('\n').length : 0;
  document.getElementById('wordCount').textContent = code.split(/\s+/).filter(w => w.length).length;
  document.getElementById('charCount').textContent = code.length;
}

function doHighlight() {
  const code   = document.getElementById('codeInput').value;
  const hlLang = currentLanguage === 'html' ? 'xml' : currentLanguage;

  // Recréer l'élément proprement pour éviter les artefacts hljs
  const display = document.getElementById('codeDisplay');
  display.innerHTML = '<pre><code id="highlightedCode"></code></pre>';
  const codeEl = document.getElementById('highlightedCode');
  codeEl.className   = `language-${hlLang}`;
  codeEl.textContent = code;
  hljs.highlightElement(codeEl);

  updateStats();
  updatePreviewTitle();
}

function doFormat() {
  const ta = document.getElementById('codeInput');
  let code = ta.value;
  code = code.replace(/\t/g, '    ');
  code = code.replace(/[ \t]+$/gm, '');
  code = code.replace(/\r\n/g, '\n');
  ta.value = code;
  updateStats();
  toast('✨ Code formaté');
}

function doSave() {
  const data = {
    title: document.getElementById('snippetTitle').value,
    language: currentLanguage, theme: currentTheme,
    code: document.getElementById('codeInput').value,
    savedAt: new Date().toISOString(),
  };
  localStorage.setItem('bl_code_snippet', JSON.stringify(data));
  toast('💾 Snippet sauvegardé');
}

function loadSaved() {
  try {
    const s = JSON.parse(localStorage.getItem('bl_code_snippet') || '{}');
    if (!s.code) return;
    document.getElementById('snippetTitle').value  = s.title    || '';
    document.getElementById('codeInput').value     = s.code     || '';
    currentLanguage = s.language || 'javascript';
    currentTheme    = s.theme    || 'github-dark';
    document.querySelectorAll('#langSelector .pill').forEach(b => {
      const l = languages.find(l => l.name === b.textContent);
      b.classList.toggle('active', l?.id === currentLanguage);
    });
    document.querySelectorAll('#themeSelector .pill').forEach(b => {
      const t = themes.find(t => t.name === b.textContent);
      b.classList.toggle('active', t?.id === currentTheme);
    });
    document.getElementById('highlightTheme').href =
      `https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/${currentTheme}.min.css`;
  } catch(e) {}
}

function doClear() {
  if (!confirm('Effacer tout le code ?')) return;
  document.getElementById('codeInput').value    = '';
  document.getElementById('snippetTitle').value = '';
  document.getElementById('codeDisplay').innerHTML = '<pre><code id="highlightedCode" class="language-javascript">// Collez du code à gauche puis cliquez sur Coloriser…</code></pre>';
  updateStats();
  toast('🗑️ Code effacé');
}

function doCopyHTML() {
  const html = document.getElementById('codeDisplay').innerHTML;
  navigator.clipboard.writeText(html).then(() => toast('📋 HTML coloré copié'));
}
function doCopyRaw() {
  navigator.clipboard.writeText(document.getElementById('codeInput').value)
    .then(() => toast('📄 Code brut copié'));
}
function doCopyEmbed() {
  const lang = currentLanguage === 'html' ? 'xml' : currentLanguage;
  const code = document.getElementById('codeInput').value
    .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  navigator.clipboard.writeText(`<pre><code class="language-${lang}">${code}</code></pre>`)
    .then(() => toast('🔗 Bloc <pre><code> copié'));
}

function doExportJSON() {
  const title = document.getElementById('snippetTitle').value.trim() || 'snippet';
  const lang  = languages.find(l => l.id === currentLanguage);
  const data  = { title, language: currentLanguage, theme: currentTheme,
                  code: document.getElementById('codeInput').value,
                  exportDate: new Date().toISOString() };
  const a = document.createElement('a');
  a.href = URL.createObjectURL(new Blob([JSON.stringify(data,null,2)],{type:'application/json'}));
  a.download = `${title}.${lang.ext}.json`;
  a.click(); URL.revokeObjectURL(a.href);
  toast('💾 JSON exporté');
}

function doExportHTML() {
  const title   = document.getElementById('snippetTitle').value.trim() || 'Code Snippet';
  const lang    = languages.find(l => l.id === currentLanguage);
  const content = document.getElementById('codeDisplay').innerHTML;
  const fullHTML = `<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8">
<title>${title.replace(/</g,'&lt;')}</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/${currentTheme}.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"><\/script>
<style>
:root{--bg:#0d0d0f;--surface:#1a1a20;--border:rgba(255,255,255,.08);--accent:#ff1654;--text:#e8e8f0;--muted:#7a7a90}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--text);
display:flex;flex-direction:column;align-items:center;min-height:100vh;padding:2rem;gap:1rem}
.wrap{width:100%;max-width:960px;border-radius:14px;overflow:hidden;border:1px solid var(--border);
box-shadow:0 20px 50px rgba(0,0,0,.5)}
.hdr{background:var(--surface);padding:.9rem 1.2rem;border-bottom:1px solid var(--border);
display:flex;justify-content:space-between;align-items:center}
.dots{display:flex;gap:.3rem}.dot{width:11px;height:11px;border-radius:50%}
.dr{background:#ff5f56}.dy{background:#ffbd2e}.dg{background:#27c93f}
.code-area{padding:1.4rem;overflow-x:auto;background:var(--bg)}
pre{margin:0}.hljs{background:transparent!important;padding:0!important;font-size:.85rem;line-height:1.65}
footer{font-size:.73rem;color:var(--muted)}footer span{color:var(--accent);font-weight:700}
</style></head><body>
<div class="wrap">
<div class="hdr">
<div style="display:flex;align-items:center;gap:.6rem">
<div class="dots"><div class="dot dr"></div><div class="dot dy"></div><div class="dot dg"></div></div>
<span style="font-family:monospace;font-size:.82rem;color:var(--muted)">${title.replace(/</g,'&lt;')}.${lang.ext}</span>
</div>
<span style="font-size:.75rem;color:var(--muted)">${lang.name} · ${new Date().toLocaleDateString('fr-FR')}</span>
</div>
<div class="code-area">${content}</div>
</div>
<footer>Généré par <span>Black-Lab</span> — Code Highlighter</footer>
<script>hljs.highlightAll();<\/script>
</body></html>`;
  const a = document.createElement('a');
  a.href = URL.createObjectURL(new Blob([fullHTML],{type:'text/html'}));
  a.download = `${title.replace(/\s+/g,'-').toLowerCase()}.html`;
  a.click(); URL.revokeObjectURL(a.href);
  toast('📄 HTML exporté');
}

document.addEventListener('DOMContentLoaded', () => {
  hljs.configure({ ignoreUnescapedHTML: true });
  initSelectors();
  loadSaved();
  doHighlight(); // colorisation initiale

  const ta = document.getElementById('codeInput');
  ta.addEventListener('input', updateStats);
  ta.addEventListener('keydown', e => {
    if (e.key === 'Enter' && e.ctrlKey) { e.preventDefault(); doHighlight(); }
    if (e.key === 'Tab') {
      e.preventDefault();
      const s = ta.selectionStart, end = ta.selectionEnd;
      ta.value = ta.value.slice(0, s) + '    ' + ta.value.slice(end);
      ta.selectionStart = ta.selectionEnd = s + 4;
    }
  });
  document.getElementById('snippetTitle').addEventListener('input', updatePreviewTitle);
});
</script>
</body>
</html>