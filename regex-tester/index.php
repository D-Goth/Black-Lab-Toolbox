<?php
/* ══════════════════════════════════════════════════════════
   API — test regex côté PHP (PCRE natif)
══════════════════════════════════════════════════════════ */
if (isset($_POST['action']) && $_POST['action'] === 'test') {
    header('Content-Type: application/json');

    $pattern = $_POST['pattern'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $flags   = $_POST['flags']   ?? '';

    if ($pattern === '') {
        echo json_encode(['error' => 'Pattern vide']); exit;
    }

    // Construire le délimiteur sécurisé
    $delimiter = '#';
    $phpFlags  = '';
    $validFlags = ['i','m','s','u','x','U','A'];
    foreach (str_split($flags) as $f) {
        if (in_array($f, $validFlags)) $phpFlags .= $f;
    }

    $regex = $delimiter . str_replace($delimiter, '\\' . $delimiter, $pattern) . $delimiter . $phpFlags;

    // Vérif syntaxe
    $valid = @preg_match($regex, '') !== false;
    if (!$valid) {
        $err = error_get_last();
        echo json_encode(['error' => preg_replace('/^preg_match\(\):\s*/i', '', $err['message'] ?? 'Regex invalide')]);
        exit;
    }

    // Trouver tous les matches avec positions
    $matches  = [];
    $allMatches = [];

    preg_match_all($regex, $subject, $allMatches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

    $matchCount  = count($allMatches);
    $groupCount  = $matchCount > 0 ? count($allMatches[0]) - 1 : 0;

    // Construire la liste des matches pour le front
    $matchList = [];
    foreach ($allMatches as $idx => $matchSet) {
        $m = [
            'index'  => $idx,
            'value'  => $matchSet[0][0],
            'start'  => $matchSet[0][1],
            'end'    => $matchSet[0][1] + strlen($matchSet[0][0]),
            'groups' => [],
        ];
        for ($g = 1; $g < count($matchSet); $g++) {
            $m['groups'][] = [
                'value' => $matchSet[$g][0] ?? null,
                'start' => $matchSet[$g][1] ?? -1,
            ];
        }
        $matchList[] = $m;
    }

    // Résultat replace si demandé
    $replaced = null;
    $replaceWith = $_POST['replace'] ?? null;
    if ($replaceWith !== null && $replaceWith !== '') {
        $replaced = preg_replace($regex, $replaceWith, $subject);
    }

    echo json_encode([
        'valid'       => true,
        'match_count' => $matchCount,
        'group_count' => $groupCount,
        'matches'     => $matchList,
        'replaced'    => $replaced,
        'flags_used'  => $phpFlags,
        'regex_php'   => $regex,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Regex Tester — Black-Lab Toolbox</title>
<style>
html, body { height: 100%; overflow-y: auto; }
body { display: flex; flex-direction: column; }
.page-wrap { position: relative; z-index: 1; flex: 1; display: flex; flex-direction: column; }
.page-body {
    flex: 1; padding: 1rem 1.4rem 1.6rem;
    display: flex; flex-direction: column; gap: .85rem;
    max-width: 1400px; width: 100%; margin: 0 auto;
}

/* ── Layout principal ── */
.rx-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: .85rem;
    align-items: start;
}
@media(max-width:900px) { .rx-grid { grid-template-columns: 1fr; } }

/* ── Pattern bar ── */
.pattern-bar {
    display: flex; gap: 0; align-items: stretch;
    background: var(--bg-2); border: 1px solid var(--border);
    border-radius: var(--radius-sm); overflow: hidden;
    font-family: 'Fira Code', monospace;
    transition: border-color .15s;
}
.pattern-bar:focus-within { border-color: var(--accent, #ff1654); }
.pattern-delim {
    padding: 0 .7rem; display: flex; align-items: center;
    color: var(--accent, #ff1654); font-size: 1rem; font-weight: 700;
    background: rgba(255,22,84,.08); flex-shrink: 0; user-select: none;
    font-family: 'Fira Code', monospace;
}
.pattern-input {
    flex: 1; background: none; border: none; outline: none;
    color: var(--text); font-family: 'Fira Code', monospace;
    font-size: .88rem; padding: .55rem .4rem;
}
.pattern-input::placeholder { color: var(--text-dim); }
.flags-input {
    width: 72px; background: none; border: none; border-left: 1px solid var(--border);
    outline: none; color: var(--accent, #ff1654); font-family: 'Fira Code', monospace;
    font-size: .88rem; padding: .55rem .6rem; text-align: center;
}
.flags-input::placeholder { color: var(--text-dim); font-size: .75rem; }

/* ── Flags chips ── */
.flags-row { display: flex; gap: .35rem; flex-wrap: wrap; }
.flag-chip {
    padding: .18rem .55rem; border-radius: 6px; font-size: .72rem;
    font-family: 'Fira Code', monospace; letter-spacing: .05em;
    border: 1px solid var(--border); color: var(--text-muted);
    background: transparent; cursor: pointer; transition: all .15s;
    user-select: none;
}
.flag-chip.active {
    background: rgba(255,22,84,.12); border-color: var(--accent, #ff1654);
    color: var(--accent, #ff1654);
}
.flag-chip:hover { border-color: var(--accent, #ff1654); color: var(--accent, #ff1654); }

/* ── Subject avec highlight ── */
.subject-wrap { position: relative; }
.subject-ta {
    width: 100%; min-height: 160px; resize: vertical;
    font-family: 'Fira Code', monospace; font-size: .83rem; line-height: 1.7;
    background: transparent; border: none;
    color: var(--text);
    padding: .75rem 1rem; outline: none; tab-size: 2;
    position: relative; z-index: 2;
}

/* Overlay highlight (superposé sur le textarea) */
.highlight-overlay {
    position: absolute; top: 0; left: 0; right: 0; bottom: 0;
    pointer-events: none;
    font-family: 'Fira Code', monospace; font-size: .83rem; line-height: 1.7;
    padding: .75rem 1rem;
    white-space: pre-wrap; word-wrap: break-word;
    color: transparent; /* texte invisible — seuls les marks sont visibles */
    background: transparent; /* ne pas couvrir le textarea */
    border: 1px solid transparent;
    border-radius: var(--radius-sm);
    overflow: hidden;
    z-index: 1;
}
.subject-ta {
    position: relative; z-index: 2;
    background: transparent !important;
}
.subject-wrap {
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    position: relative;
    transition: border-color .15s;
}
.subject-wrap:focus-within { border-color: var(--accent, #ff1654); }
.hl-match {
    background: rgba(255,189,46,.25);
    border-radius: 2px;
    outline: 1px solid rgba(255,189,46,.5);
}
.hl-match.hl-active {
    background: rgba(255,22,84,.3);
    outline: 1px solid rgba(255,22,84,.6);
}

/* ── Replace ── */
.replace-bar {
    display: flex; gap: .5rem; align-items: center;
}
.replace-bar input { flex: 1; font-family: 'Fira Code', monospace; font-size: .83rem; }
.replace-result {
    background: var(--bg-2); border: 1px solid var(--border);
    border-radius: var(--radius-sm); padding: .65rem 1rem;
    font-family: 'Fira Code', monospace; font-size: .82rem;
    color: var(--text); white-space: pre-wrap; word-break: break-all;
    min-height: 40px; max-height: 160px; overflow-y: auto;
    line-height: 1.65;
}

/* ── Status bar ── */
.status-bar {
    display: flex; align-items: center; gap: .5rem; flex-wrap: wrap;
    min-height: 26px;
}
.status-badge {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .18rem .6rem; border-radius: 20px; font-size: .72rem; font-weight: 600;
    border: 1px solid;
}
.sb-ok   { background: rgba(39,201,63,.1);  color: #27c93f; border-color: rgba(39,201,63,.4); }
.sb-warn { background: rgba(255,189,46,.1); color: #ffbd2e; border-color: rgba(255,189,46,.4); }
.sb-err  { background: rgba(255,22,84,.1);  color: #ff1654; border-color: rgba(255,22,84,.4); }
.sb-neu  { background: var(--bg-2); color: var(--text-muted); border-color: var(--border); }

/* ── Matches list (panneau droit) ── */
.match-item {
    border: 1px solid var(--border); border-radius: var(--radius-sm);
    padding: .6rem .8rem; margin-bottom: .45rem; cursor: pointer;
    transition: border-color .13s; background: var(--bg-2);
}
.match-item:hover { border-color: rgba(255,189,46,.4); }
.match-item.active { border-color: rgba(255,22,84,.5); background: rgba(255,22,84,.05); }
.match-idx  { font-family: 'Fira Code', monospace; font-size: .68rem; color: var(--text-muted); margin-bottom: .2rem; }
.match-val  { font-family: 'Fira Code', monospace; font-size: .82rem; color: #ffbd2e; word-break: break-all; }
.match-pos  { font-family: 'Fira Code', monospace; font-size: .68rem; color: var(--text-muted); margin-top: .2rem; }
.match-groups { margin-top: .4rem; display: flex; flex-direction: column; gap: .2rem; }
.match-group  { font-size: .72rem; font-family: 'Fira Code', monospace; }
.mg-label { color: var(--text-muted); }
.mg-val   { color: #a5d6a7; }

/* ── PHP regex ── */
.php-regex {
    font-family: 'Fira Code', monospace; font-size: .75rem;
    color: var(--text-muted); word-break: break-all;
    background: var(--bg-2); padding: .5rem .8rem;
    border-radius: var(--radius-sm); border: 1px solid var(--border);
}

/* ── Cheat sheet ── */
.cs-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .3rem; }
.cs-row  { display: flex; gap: .4rem; align-items: baseline; font-size: .75rem; padding: .2rem 0; border-bottom: 1px solid var(--border); }
.cs-row:last-child { border: none; }
.cs-pat  { font-family: 'Fira Code', monospace; color: var(--accent, #ff1654); flex-shrink: 0; min-width: 52px; font-size: .73rem; }
.cs-desc { color: var(--text-muted); font-size: .72rem; }

/* ── Matches scroll ── */
.matches-scroll { max-height: 420px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: var(--border) transparent; }
.no-matches { text-align: center; padding: 2rem; color: var(--text-muted); font-size: .8rem; }
</style>
</head>
<body>

<div class="ambient">
  <div class="ambient__circle"></div>
  <div class="ambient__circle"></div>
</div>

<div class="page-wrap">
<header class="hdr">
  <span class="hdr__icon">⚡</span>
  <span class="hdr__title">REGEX TESTER</span>
  <span class="hdr__sep"></span>
  <span class="hdr__meta">PCRE · Flags · Groupes · Replace · Highlight temps réel</span>
</header>
<div class="page-body">

  <!-- ── Pattern + flags ── -->
  <div class="card">
    <div class="card-title">Expression régulière</div>
    <div class="pattern-bar" style="margin-bottom:.6rem">
      <span class="pattern-delim">/</span>
      <input type="text" class="pattern-input" id="rx-pattern" placeholder="([a-z]+)\d+" autocomplete="off" spellcheck="false">
      <span class="pattern-delim">/</span>
      <input type="text" class="flags-input" id="rx-flags" placeholder="flags" maxlength="8" autocomplete="off" spellcheck="false">
    </div>
    <div class="flags-row" id="flags-row">
      <span class="flag-chip" data-flag="i" title="Insensible à la casse">i — insensible casse</span>
      <span class="flag-chip" data-flag="m" title="Multiligne (^ et $ sur chaque ligne)">m — multiligne</span>
      <span class="flag-chip" data-flag="s" title="Le point matche aussi \n">s — dot-all</span>
      <span class="flag-chip" data-flag="u" title="Mode Unicode">u — unicode</span>
      <span class="flag-chip" data-flag="x" title="Ignorer espaces et commentaires">x — verbose</span>
      <span class="flag-chip" data-flag="g" title="Toutes les occurrences (PCRE : preg_match_all)">g — global</span>
    </div>
  </div>

  <div class="rx-grid">

    <!-- Colonne gauche -->
    <div style="display:flex;flex-direction:column;gap:.85rem">

      <!-- Sujet -->
      <div class="card">
        <div class="card-title">Texte de test</div>
        <div class="subject-wrap">
          <div class="highlight-overlay" id="hl-overlay"></div>
          <textarea class="subject-ta" id="rx-subject" spellcheck="false"
            placeholder="Collez votre texte ici…"></textarea>
        </div>
        <div class="status-bar" id="status-bar" style="margin-top:.6rem">
          <span class="status-badge sb-neu">En attente</span>
        </div>
      </div>

      <!-- Replace -->
      <div class="card">
        <div class="card-title">Remplacement <span style="font-size:.7rem;font-weight:400;color:var(--text-muted)">(optionnel)</span></div>
        <div class="replace-bar" style="margin-bottom:.6rem">
          <input type="text" id="rx-replace" placeholder="$1, ${groupe}, \\0 …"
            style="font-family:'Fira Code',monospace;font-size:.83rem" autocomplete="off" spellcheck="false">
          <button class="btn btn-ghost btn-sm" onclick="copyReplace()">📋 Copier</button>
        </div>
        <div class="replace-result" id="replace-result">
          <span style="color:var(--text-dim);font-style:italic">Le résultat apparaîtra ici…</span>
        </div>
      </div>

      <!-- PHP regex générée -->
      <div class="card">
        <div class="card-title">Regex PHP générée</div>
        <div class="php-regex" id="php-regex">—</div>
      </div>

    </div><!-- /col-left -->

    <!-- Colonne droite -->
    <div style="display:flex;flex-direction:column;gap:.85rem">

      <!-- Matches -->
      <div class="card">
        <div class="card-title">Matches <span style="font-size:.7rem;font-weight:400;color:var(--text-muted)" id="match-count-label"></span></div>
        <div class="matches-scroll" id="matches-list">
          <div class="no-matches">Lance une recherche pour voir les résultats</div>
        </div>
      </div>

      <!-- Cheat sheet -->
      <div class="card">
        <div class="card-title">Aide-mémoire</div>
        <div class="cs-grid">
          <div>
            <div class="cs-row"><span class="cs-pat">.</span><span class="cs-desc">N'importe quel caractère</span></div>
            <div class="cs-row"><span class="cs-pat">\d</span><span class="cs-desc">Chiffre [0-9]</span></div>
            <div class="cs-row"><span class="cs-pat">\w</span><span class="cs-desc">Mot [a-zA-Z0-9_]</span></div>
            <div class="cs-row"><span class="cs-pat">\s</span><span class="cs-desc">Espace / tab / newline</span></div>
            <div class="cs-row"><span class="cs-pat">\b</span><span class="cs-desc">Limite de mot</span></div>
            <div class="cs-row"><span class="cs-pat">^</span><span class="cs-desc">Début de chaîne</span></div>
            <div class="cs-row"><span class="cs-pat">$</span><span class="cs-desc">Fin de chaîne</span></div>
            <div class="cs-row"><span class="cs-pat">[abc]</span><span class="cs-desc">Classe de caractères</span></div>
            <div class="cs-row"><span class="cs-pat">[^abc]</span><span class="cs-desc">Négation de classe</span></div>
          </div>
          <div>
            <div class="cs-row"><span class="cs-pat">*</span><span class="cs-desc">0 ou plus (greedy)</span></div>
            <div class="cs-row"><span class="cs-pat">+</span><span class="cs-desc">1 ou plus (greedy)</span></div>
            <div class="cs-row"><span class="cs-pat">?</span><span class="cs-desc">0 ou 1 (optionnel)</span></div>
            <div class="cs-row"><span class="cs-pat">{n,m}</span><span class="cs-desc">Entre n et m fois</span></div>
            <div class="cs-row"><span class="cs-pat">*?</span><span class="cs-desc">Lazy (non-greedy)</span></div>
            <div class="cs-row"><span class="cs-pat">(abc)</span><span class="cs-desc">Groupe capturant</span></div>
            <div class="cs-row"><span class="cs-pat">(?:abc)</span><span class="cs-desc">Groupe non-capturant</span></div>
            <div class="cs-row"><span class="cs-pat">a|b</span><span class="cs-desc">Alternative (a ou b)</span></div>
            <div class="cs-row"><span class="cs-pat">(?=…)</span><span class="cs-desc">Lookahead</span></div>
          </div>
        </div>
      </div>

    </div><!-- /col-right -->

  </div><!-- /rx-grid -->

</div>
</div>
<div class="toast-area" id="ta"></div>

<script>
const $ = id => document.getElementById(id);
let debounce = null;
let lastData  = null;
let activeMatch = -1;

/* ── Flags chips ── */
$('flags-row').querySelectorAll('.flag-chip').forEach(chip => {
    chip.addEventListener('click', () => {
        const flag  = chip.dataset.flag;
        const input = $('rx-flags');
        // 'g' n'existe pas en PCRE — on l'ignore côté serveur, c'est toujours preg_match_all
        if (flag === 'g') { chip.classList.toggle('active'); triggerTest(); return; }
        if (input.value.includes(flag)) {
            input.value = input.value.replace(flag, '');
            chip.classList.remove('active');
        } else {
            input.value += flag;
            chip.classList.add('active');
        }
        triggerTest();
    });
});

$('rx-flags').addEventListener('input', () => {
    // Sync chips
    $('flags-row').querySelectorAll('.flag-chip').forEach(chip => {
        if (chip.dataset.flag === 'g') return;
        chip.classList.toggle('active', $('rx-flags').value.includes(chip.dataset.flag));
    });
    triggerTest();
});

/* ── Listeners ── */
$('rx-pattern').addEventListener('input', triggerTest);
$('rx-subject').addEventListener('input', () => { syncOverlay(); triggerTest(); });
$('rx-replace').addEventListener('input', triggerTest);
$('rx-subject').addEventListener('scroll', syncOverlayScroll);

function triggerTest() {
    clearTimeout(debounce);
    debounce = setTimeout(runTest, 200);
}

/* ── Test ── */
async function runTest() {
    const pattern = $('rx-pattern').value;
    const subject = $('rx-subject').value;
    const flags   = $('rx-flags').value.replace('g', ''); // retire g (pseudo-flag)
    const replace = $('rx-replace').value;

    if (!pattern) {
        resetUI(); return;
    }

    const fd = new FormData();
    fd.append('action',  'test');
    fd.append('pattern', pattern);
    fd.append('subject', subject);
    fd.append('flags',   flags);
    if (replace) fd.append('replace', replace);

    try {
        const res  = await fetch('', { method: 'POST', body: fd });
        const data = await res.json();
        lastData = data;
        if (data.error) {
            showError(data.error); return;
        }
        renderResult(data);
    } catch(e) {
        showError('Erreur serveur');
    }
}

/* ── Render ── */
function renderResult(d) {
    activeMatch = -1;

    /* Status bar */
    const sb = $('status-bar');
    if (d.match_count === 0) {
        sb.innerHTML = '<span class="status-badge sb-warn">✗ Aucun match</span>';
    } else {
        sb.innerHTML = `<span class="status-badge sb-ok">✓ ${d.match_count} match${d.match_count > 1 ? 's' : ''}</span>`
            + (d.group_count > 0 ? `<span class="status-badge sb-neu">${d.group_count} groupe${d.group_count > 1 ? 's' : ''}</span>` : '');
    }

    /* Match count label */
    $('match-count-label').textContent = d.match_count > 0 ? `(${d.match_count})` : '';

    /* PHP regex */
    $('php-regex').textContent = d.regex_php || '—';

    /* Replace result */
    if (d.replaced !== null && d.replaced !== undefined) {
        $('replace-result').textContent = d.replaced;
    } else {
        $('replace-result').innerHTML = '<span style="color:var(--text-dim);font-style:italic">Le résultat apparaîtra ici…</span>';
    }

    /* Matches list */
    renderMatches(d.matches);

    /* Highlight overlay */
    renderHighlight(d.matches, -1);
}

function renderMatches(matches) {
    const list = $('matches-list');
    if (!matches || !matches.length) {
        list.innerHTML = '<div class="no-matches">Aucun match trouvé</div>';
        return;
    }
    list.innerHTML = matches.map((m, i) => {
        const groups = m.groups && m.groups.length
            ? `<div class="match-groups">${m.groups.map((g, gi) =>
                `<div class="match-group"><span class="mg-label">$${gi + 1}: </span><span class="mg-val">${escHtml(g.value ?? '∅')}</span></div>`
              ).join('')}</div>` : '';
        return `<div class="match-item" id="mi-${i}" onclick="selectMatch(${i})">
            <div class="match-idx">Match #${i + 1}</div>
            <div class="match-val">${escHtml(m.value)}</div>
            <div class="match-pos">pos ${m.start}–${m.end} · len ${m.end - m.start}</div>
            ${groups}
        </div>`;
    }).join('');
}

function selectMatch(idx) {
    activeMatch = activeMatch === idx ? -1 : idx;
    document.querySelectorAll('.match-item').forEach((el, i) => {
        el.classList.toggle('active', i === activeMatch);
    });
    if (lastData) renderHighlight(lastData.matches, activeMatch);
}

/* ── Highlight overlay ── */
function renderHighlight(matches, active) {
    const text = $('rx-subject').value;
    if (!matches || !matches.length) {
        $('hl-overlay').innerHTML = escHtml(text); return;
    }

    let result = '';
    let cursor = 0;
    matches.forEach((m, i) => {
        if (m.start > cursor) result += escHtml(text.slice(cursor, m.start));
        const cls = i === active ? 'hl-match hl-active' : 'hl-match';
        result += `<mark class="${cls}">${escHtml(text.slice(m.start, m.end))}</mark>`;
        cursor = m.end;
    });
    if (cursor < text.length) result += escHtml(text.slice(cursor));
    $('hl-overlay').innerHTML = result;
    syncOverlayScroll();
}

function syncOverlay() {
    // L'overlay se synchronise via CSS — rien à faire ici sauf le scroll
    syncOverlayScroll();
}

function syncOverlayScroll() {
    $('hl-overlay').scrollTop = $('rx-subject').scrollTop;
}

/* ── Reset / Error ── */
function resetUI() {
    $('status-bar').innerHTML     = '<span class="status-badge sb-neu">En attente</span>';
    $('match-count-label').textContent = '';
    $('matches-list').innerHTML   = '<div class="no-matches">Lance une recherche pour voir les résultats</div>';
    $('php-regex').textContent    = '—';
    $('replace-result').innerHTML = '<span style="color:var(--text-dim);font-style:italic">Le résultat apparaîtra ici…</span>';
    $('hl-overlay').innerHTML     = '';
    lastData = null;
}

function showError(msg) {
    $('status-bar').innerHTML     = `<span class="status-badge sb-err">⚠ ${escHtml(msg)}</span>`;
    $('match-count-label').textContent = '';
    $('matches-list').innerHTML   = '<div class="no-matches">Regex invalide</div>';
    $('php-regex').textContent    = '—';
    $('hl-overlay').innerHTML     = '';
}

/* ── Copy replace ── */
function copyReplace() {
    const text = $('replace-result').textContent;
    if (!text || text.includes('apparaîtra')) return;
    navigator.clipboard.writeText(text).then(() => toast('Copié !')).catch(() => toast('Échec', 'err'));
}

/* ── Utils ── */
function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function toast(m, t = 'ok') {
    const a = $('ta');
    const e = document.createElement('div');
    e.className = `toast toast--${t}`;
    e.textContent = m;
    a.appendChild(e);
    setTimeout(() => e.remove(), 2500);
}

/* ── Sample ── */
$('rx-pattern').value = '(\\w+)@([\\w.-]+)\\.([a-z]{2,})';
$('rx-subject').value = 'Contactez-nous : hello@exemple-site.fr ou support@example.com\nAutre : info@sub.domain.org';
$('rx-flags').value   = 'i';
$('flags-row').querySelector('[data-flag="i"]').classList.add('active');
runTest();
</script>
</body>
</html>