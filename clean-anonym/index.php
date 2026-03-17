<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$anonymized = ''; $error = ''; $script = ''; $modCount = 0; $cleanshell = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Requête non autorisée.";
    } elseif (mb_strlen($_POST['script'] ?? '') > 50000) {
        $error = "Script trop volumineux (limite : 50 000 caractères).";
    } elseif (!empty($_POST['script'])) {
        $script = $_POST['script'];
        $cleanshell = isset($_POST['cleanshell']) && $_POST['cleanshell'] === '1';
        $modCount = 0;
        $script = str_replace("\r\n", "\n", str_replace("\r", "\n", $script));
        $script = urldecode($script);
        $script = preg_replace_callback('/\\\\x([0-9A-Fa-f]{2})/', function($m) {
            return mb_convert_encoding(chr(hexdec($m[1])), 'UTF-8', 'ISO-8859-1');
        }, $script);
        if (function_exists('normalizer_normalize')) {
            $script = normalizer_normalize($script, Normalizer::FORM_C) ?: $script;
        }
        if ($cleanshell) {
            $lines = explode("\n", $script); $cleaned = [];
            $cleanRegex = '/[^\p{L}\p{N}\p{S}\-\_\|&<>\/\s\$#\*\=\+;:\'\"\[\],\(\)\{\}%!]/u';
            foreach (array_chunk($lines, 500) as $chunk) {
                foreach ($chunk as $line) {
                    $cl = preg_replace($cleanRegex, '', $line);
                    if ($cl !== $line) $modCount++;
                    $cleaned[] = $cl;
                }
            }
            $anonymized = implode("\n", $cleaned);
        } else {
            $anonymized = $script;
            $anonymized = preg_replace('/\b\d{1,3}(\.\d{1,3}){3}\b/', 'xxx.xxx.xxx.xxx', $anonymized);
            $anonymized = preg_replace('#/(home|root|etc|var|mnt|media|srv)/[^\s]+#', '/chemin/anonyme', $anonymized);
            $anonymized = preg_replace('/[A-Fa-f0-9]{32,}/', 'TOKEN_REMPLACÉ', $anonymized);
            $anonymized = preg_replace('/[A-Za-z0-9+\/=]{40,}/', 'CLÉ_BASE64', $anonymized);
            $anonymized = preg_replace('/https?:\/\/[^\s\'"]+/i', 'URL_ANONYMISÉE', $anonymized);
            $anonymized = preg_replace('/(?<=\buser=)[^\s&]+/i', 'utilisateur', $anonymized);
            $anonymized = preg_replace('/(?<=\busername=)[^\s&]+/i', 'utilisateur', $anonymized);
            $anonymized = preg_replace('/(?<=\bpassword=)[^\s&]+/i', 'motdepasse', $anonymized);
            $anonymized = preg_replace('/(?<=\bpasswd=)[^\s&]+/i', 'motdepasse', $anonymized);
            $anonymized = preg_replace('/(?<=\bhostname=)[^\s&]+/i', 'serveur.local', $anonymized);
            $anonymized = preg_replace('/(?<=token=)[^&\s]+/i', 'TOKEN_ANONYMISÉ', $anonymized);
            $anonymized = preg_replace('/(?<=apikey=)[^&\s]+/i', 'CLÉ_API', $anonymized);
            $anonymized = preg_replace('/(?<=auth=)[^&\s]+/i', 'AUTH_ANONYMISÉ', $anonymized);
            $anonymized = preg_replace('/[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}/', 'email@anonyme', $anonymized);
            $origLines = explode("\n", $script); $anonLines = explode("\n", $anonymized);
            $max = max(count($origLines), count($anonLines));
            for ($i = 0; $i < $max; $i++) {
                if (trim($origLines[$i] ?? '') !== trim($anonLines[$i] ?? '')) $modCount++;
            }
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

$scriptJs  = json_encode($script);
$anonJs    = json_encode($anonymized);
$hasResult = !empty($anonymized);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Clean Shell — Black-Lab Toolbox</title>
<!-- highlight.js — thème One Dark -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/bash.min.js"></script>
<style>
/* ── Reset & base hérité du toolbox ── */
html, body {
  height: 100%;
  overflow: hidden; /* on gère le scroll nous-mêmes */
}
body {
  display: flex;
  flex-direction: column;
}

/* ── Wrapping page ── */
.page-wrap {
  position: relative; z-index: 1;
  flex: 1;
  display: flex;
  flex-direction: column;
  min-height: 0; /* crucial pour flex shrink */
}

/* ── Header ── */
.hdr {
  flex-shrink: 0;
}

/* ── Body scrollable ── */
.page-body {
  flex: 1;
  min-height: 0;
  display: flex;
  flex-direction: column;
  gap: .8rem;
  padding: 1rem 1.4rem 1rem;
  overflow-y: auto;
}

/* ── Barre mode + actions ── */
.toolbar {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  gap: .6rem;
  flex-wrap: wrap;
}
.mode-btn {
  flex: 1; min-width: 180px;
  padding: .42rem .8rem;
  background: var(--surface-h); border: 1px solid var(--border);
  border-radius: var(--radius-sm); color: var(--text-muted);
  cursor: pointer; font-size: .8rem; font-family: inherit;
  transition: all var(--transition);
  white-space: nowrap;
}
.mode-btn.active {
  background: var(--accent-soft); border-color: var(--accent); color: var(--accent);
}

/* ── Panels côte à côte, prennent tout l'espace restant ── */
.panels {
  flex: 1;
  min-height: 0;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  align-items: stretch;
}
@media(max-width:700px) {
  .panels { grid-template-columns: 1fr; }
}

/* ── Chaque panel card ── */
.editor-card {
  display: flex;
  flex-direction: column;
  min-height: 0;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
}
.editor-card-head {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: .5rem .9rem;
  border-bottom: 1px solid var(--border);
  font-size: .78rem;
  font-family: var(--font-mono);
  letter-spacing: .5px;
  color: var(--text-muted);
  background: var(--surface-h);
}
.editor-card-head strong {
  color: var(--accent);
  text-transform: uppercase;
  letter-spacing: 1px;
  font-size: .72rem;
}

/* ── Zone d'édition : textarea + pre superposés ── */
.editor-wrap {
  flex: 1;
  min-height: 0;
  position: relative;
}

/* Textarea invisible par-dessus le highlight */
.editor-wrap textarea,
.editor-wrap pre {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  margin: 0;
  padding: .85rem 1rem;
  font-family: var(--font-mono);
  font-size: .8rem;
  line-height: 1.55;
  tab-size: 4;
  white-space: pre;
  overflow: auto;
  border: none;
  border-radius: 0;
  background: transparent;
  box-sizing: border-box;
}

/* Le pre est en dessous, affiché */
.editor-wrap pre {
  z-index: 1;
  background: #1e2127; /* atom-one-dark bg */
  color: #abb2bf;
  pointer-events: none;
  overflow: hidden; /* scroll géré par textarea */
}
.editor-wrap pre code {
  background: transparent;
  padding: 0;
  font-size: inherit;
  line-height: inherit;
}

/* Le textarea est au-dessus, transparent */
.editor-wrap textarea {
  z-index: 2;
  background: transparent;
  color: transparent;
  caret-color: #abb2bf;
  resize: none;
  outline: none;
  spellcheck: false;
  -webkit-text-fill-color: transparent;
}
.editor-wrap textarea::selection {
  background: rgba(97,175,254,.25);
  -webkit-text-fill-color: transparent;
}

/* Résultat (readonly) : on garde juste le pre, pas de textarea par-dessus */
.editor-wrap.readonly textarea {
  display: none;
}
.editor-wrap.readonly pre {
  pointer-events: auto;
  overflow: auto;
  user-select: text;
}

/* ── Stat badge ── */
.stat-badge {
  display: inline-flex; align-items: center; gap: .3rem;
  padding: .2rem .55rem; border-radius: 20px;
  font-size: .72rem; font-weight: 600;
}
.stat-ok   { background: rgba(0,200,120,.12); color: var(--green); border: 1px solid rgba(0,200,120,.25); }
.stat-warn { background: var(--accent-soft); color: var(--accent); border: 1px solid rgba(255,22,84,.3); }

/* ── Actions bas ── */
.actions {
  flex-shrink: 0;
  display: flex;
  gap: .7rem;
  align-items: center;
  padding-bottom: .2rem;
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
  <span class="hdr__icon">🧹</span>
  <span class="hdr__title">CLEAN SHELL</span>
  <span class="hdr__sep"></span>
  <span class="hdr__meta">Anonymisation et nettoyage de scripts Bash — IPs, tokens, chemins, credentials</span>
</header>

<div class="page-body">
  <form method="POST" id="cs-form" style="flex:1;min-height:0;display:flex;flex-direction:column;gap:.8rem">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <input type="hidden" name="cleanshell" id="mode-input" value="<?= $cleanshell ? '1' : '' ?>">

    <!-- Toolbar -->
    <div class="toolbar">
      <button type="button" class="mode-btn <?= !$cleanshell ? 'active' : '' ?>" onclick="setMode(false)">
        🎭 Anonymisation — remplace IPs, tokens, URLs, credentials
      </button>
      <button type="button" class="mode-btn <?= $cleanshell ? 'active' : '' ?>" onclick="setMode(true)">
        🧼 CleanShell — supprime les caractères non-standard
      </button>
    </div>

    <!-- Panels éditeur -->
    <div class="panels">

      <!-- SOURCE -->
      <div class="editor-card">
        <div class="editor-card-head">
          <strong>Source</strong>
          <span id="src-lines" style="opacity:.5">0 lignes</span>
        </div>
        <div class="editor-wrap" id="src-wrap">
          <pre><code class="language-bash" id="src-hl"><?= htmlspecialchars($script) ?></code></pre>
          <textarea
            name="script"
            id="src-ta"
            placeholder="Collez votre script Bash ici…"
            spellcheck="false"
            autocomplete="off"
          ><?= htmlspecialchars($script) ?></textarea>
        </div>
      </div>

      <!-- RÉSULTAT -->
      <div class="editor-card">
        <div class="editor-card-head">
          <strong>Résultat</strong>
          <div style="display:flex;align-items:center;gap:.5rem">
            <?php if ($hasResult): ?>
              <span class="stat-badge <?= $modCount > 0 ? 'stat-warn' : 'stat-ok' ?>">
                <?= $modCount > 0 ? "✎ {$modCount} modif." : '✓ Aucune modif.' ?>
              </span>
            <?php endif; ?>
            <?php if ($error): ?>
              <span class="stat-badge stat-warn">⚠ <?= htmlspecialchars($error) ?></span>
            <?php endif; ?>
          </div>
        </div>
        <div class="editor-wrap readonly" id="res-wrap">
          <pre><code class="language-bash" id="res-hl"><?= htmlspecialchars($anonymized) ?></code></pre>
        </div>
      </div>

    </div><!-- /panels -->

    <!-- Actions -->
    <div class="actions">
      <button type="submit" class="btn btn-primary">⚡ Traiter</button>
      <button type="button" class="btn btn-ghost" onclick="clearAll()">✕ Effacer</button>
      <?php if ($hasResult): ?>
      <button type="button" class="btn btn-ghost" onclick="copyResult()">📋 Copier le résultat</button>
      <?php endif; ?>
    </div>

  </form>
</div><!-- /page-body -->
</div><!-- /page-wrap -->

<div class="toast-area" id="ta"></div>

<script>
// ── Données PHP → JS ──────────────────────────────────────
const INIT_SCRIPT = <?= $scriptJs ?>;
const INIT_RESULT = <?= $anonJs ?>;

// ── Highlight.js init ─────────────────────────────────────
hljs.configure({ ignoreUnescapedHTML: true });

function highlight(code, elId) {
  const el = document.getElementById(elId);
  if (!el) return;
  el.textContent = code;
  hljs.highlightElement(el);
}

// Init au chargement
highlight(INIT_SCRIPT, 'src-hl');
highlight(INIT_RESULT, 'res-hl');
updateLineCount(INIT_SCRIPT);

// ── Sync textarea → highlight en temps réel ───────────────
const srcTa = document.getElementById('src-ta');
const srcPre = document.querySelector('#src-wrap pre');

// Sync le scroll entre textarea et pre
srcTa.addEventListener('scroll', () => {
  srcPre.scrollTop  = srcTa.scrollTop;
  srcPre.scrollLeft = srcTa.scrollLeft;
});

srcTa.addEventListener('input', () => {
  const val = srcTa.value;
  highlight(val, 'src-hl');
  updateLineCount(val);
  // Resync scroll après highlight (hljs peut changer la hauteur)
  srcPre.scrollTop  = srcTa.scrollTop;
  srcPre.scrollLeft = srcTa.scrollLeft;
});

// Tab → 4 espaces
srcTa.addEventListener('keydown', e => {
  if (e.key === 'Tab') {
    e.preventDefault();
    const s = srcTa.selectionStart, end = srcTa.selectionEnd;
    srcTa.value = srcTa.value.slice(0, s) + '    ' + srcTa.value.slice(end);
    srcTa.selectionStart = srcTa.selectionEnd = s + 4;
    srcTa.dispatchEvent(new Event('input'));
  }
});

// ── Compteur de lignes ────────────────────────────────────
function updateLineCount(text) {
  const n = text ? text.split('\n').length : 0;
  document.getElementById('src-lines').textContent = n + ' ligne' + (n > 1 ? 's' : '');
}

// ── Mode switch ───────────────────────────────────────────
function setMode(cs) {
  document.getElementById('mode-input').value = cs ? '1' : '';
  document.querySelectorAll('.mode-btn').forEach((b, i) =>
    b.classList.toggle('active', i === (cs ? 1 : 0))
  );
}

// ── Copier résultat ───────────────────────────────────────
function copyResult() {
  const code = document.getElementById('res-hl').textContent;
  navigator.clipboard.writeText(code).then(() => toast('Copié !'));
}

// ── Effacer ───────────────────────────────────────────────
function clearAll() {
  srcTa.value = '';
  srcTa.dispatchEvent(new Event('input'));
  highlight('', 'res-hl');
}

// ── Toast ─────────────────────────────────────────────────
function toast(m, t = 'ok') {
  const a = document.getElementById('ta'), e = document.createElement('div');
  e.className = `toast toast--${t}`; e.textContent = m;
  a.appendChild(e); setTimeout(() => e.remove(), 2000);
}
</script>
</body>
</html>