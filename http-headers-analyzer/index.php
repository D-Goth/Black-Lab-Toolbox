<?php
/* ══════════════════════════════════════════════════════════
   API endpoint
══════════════════════════════════════════════════════════ */
if (isset($_GET['check'])) {
    header('Content-Type: application/json');

    $raw = trim($_GET['url'] ?? '');
    if (!preg_match('#^https?://#i', $raw)) $raw = 'https://' . $raw;
    if (!filter_var($raw, FILTER_VALIDATE_URL)) {
        echo json_encode(['error' => 'URL invalide']); exit;
    }

    $parsed = parse_url($raw);
    $host   = $parsed['host'] ?? '';

    /* ── Récupération des headers ── */
    $ctx  = stream_context_create(['http' => [
        'method'          => 'GET',
        'follow_location' => 1,
        'max_redirects'   => 5,
        'timeout'         => 10,
        'user_agent'      => 'Black-Lab HeaderAnalyzer/1.0',
        'ignore_errors'   => true,
    ], 'ssl' => [
        'verify_peer'      => true,
        'verify_peer_name' => true,
    ]]);

    $body = @file_get_contents($raw, false, $ctx);
    if ($body === false || empty($http_response_header)) {
        echo json_encode(['error' => "Impossible de joindre $host — vérifiez l'URL"]); exit;
    }

    /* ── Parse les headers HTTP ── */
    $rawHeaders = $http_response_header;
    $statusLine = array_shift($rawHeaders);
    preg_match('#HTTP/[\d.]+ (\d+)#', $statusLine, $sm);
    $statusCode = (int)($sm[1] ?? 0);

    $headers = [];
    foreach ($rawHeaders as $line) {
        if (strpos($line, ':') === false) continue;
        [$k, $v] = explode(':', $line, 2);
        $headers[strtolower(trim($k))] = trim($v);
    }

    /* ══════════════════════════════════════════
       Analyse des headers de sécurité
    ══════════════════════════════════════════ */
    $checks = [];

    /* ── Strict-Transport-Security ── */
    $hsts = $headers['strict-transport-security'] ?? null;
    $checks[] = [
        'key'    => 'Strict-Transport-Security',
        'short'  => 'HSTS',
        'value'  => $hsts,
        'status' => $hsts
            ? (str_contains($hsts, 'includeSubDomains') && str_contains($hsts, 'preload') ? 'ok' : 'warn')
            : 'bad',
        'info'   => $hsts
            ? (str_contains($hsts, 'includeSubDomains') && str_contains($hsts, 'preload')
                ? 'Parfait — includeSubDomains + preload présents'
                : 'Présent mais incomplet — ajoutez includeSubDomains et preload')
            : 'Absent — le site ne force pas HTTPS',
    ];

    /* ── Content-Security-Policy ── */
    $csp = $headers['content-security-policy'] ?? ($headers['content-security-policy-report-only'] ?? null);
    $cspRO = isset($headers['content-security-policy-report-only']) && !isset($headers['content-security-policy']);
    $checks[] = [
        'key'    => 'Content-Security-Policy',
        'short'  => 'CSP',
        'value'  => $csp,
        'status' => $csp ? ($cspRO ? 'warn' : 'ok') : 'bad',
        'info'   => $csp
            ? ($cspRO ? 'En mode Report-Only — ne bloque rien encore' : 'CSP active')
            : 'Absent — aucune politique de contenu définie',
    ];

    /* ── X-Frame-Options ── */
    $xfo = $headers['x-frame-options'] ?? null;
    $checks[] = [
        'key'    => 'X-Frame-Options',
        'short'  => 'X-Frame',
        'value'  => $xfo,
        'status' => $xfo
            ? (in_array(strtoupper($xfo), ['DENY','SAMEORIGIN']) ? 'ok' : 'warn')
            : 'bad',
        'info'   => $xfo
            ? 'Protection contre le clickjacking activée'
            : 'Absent — risque de clickjacking (préférez frame-ancestors dans le CSP)',
    ];

    /* ── X-Content-Type-Options ── */
    $xcto = $headers['x-content-type-options'] ?? null;
    $checks[] = [
        'key'    => 'X-Content-Type-Options',
        'short'  => 'XCTO',
        'value'  => $xcto,
        'status' => strtolower($xcto ?? '') === 'nosniff' ? 'ok' : ($xcto ? 'warn' : 'bad'),
        'info'   => strtolower($xcto ?? '') === 'nosniff'
            ? 'Correct — nosniff activé'
            : ($xcto ? 'Valeur incorrecte — doit être "nosniff"' : 'Absent — risque de MIME sniffing'),
    ];

    /* ── Referrer-Policy ── */
    $rp   = $headers['referrer-policy'] ?? null;
    $rpOk = in_array(strtolower($rp ?? ''), ['no-referrer','strict-origin','strict-origin-when-cross-origin','same-origin']);
    $checks[] = [
        'key'    => 'Referrer-Policy',
        'short'  => 'Referrer',
        'value'  => $rp,
        'status' => $rp ? ($rpOk ? 'ok' : 'warn') : 'bad',
        'info'   => $rp
            ? ($rpOk ? 'Politique de référent sécurisée' : 'Présente mais potentiellement trop permissive')
            : 'Absent — le référent complet peut être transmis',
    ];

    /* ── Permissions-Policy ── */
    $pp = $headers['permissions-policy'] ?? null;
    $checks[] = [
        'key'    => 'Permissions-Policy',
        'short'  => 'Permissions',
        'value'  => $pp,
        'status' => $pp ? 'ok' : 'warn',
        'info'   => $pp
            ? 'Restrictions de fonctionnalités browser définies'
            : 'Absent — accès aux fonctionnalités browser non restreint',
    ];

    /* ── X-XSS-Protection ── */
    $xxss = $headers['x-xss-protection'] ?? null;
    $checks[] = [
        'key'    => 'X-XSS-Protection',
        'short'  => 'XSS-Prot.',
        'value'  => $xxss,
        'status' => $xxss ? 'ok' : 'warn',
        'info'   => $xxss
            ? 'Présent (legacy — remplacé par CSP sur les navigateurs modernes)'
            : 'Absent (obsolète mais recommandé pour les vieux navigateurs)',
    ];

    /* ── Server / X-Powered-By — infos à cacher ── */
    $server     = $headers['server']       ?? null;
    $poweredBy  = $headers['x-powered-by'] ?? null;
    $checks[] = [
        'key'    => 'Server',
        'short'  => 'Server',
        'value'  => $server,
        'status' => $server ? 'warn' : 'ok',
        'info'   => $server
            ? "Visible — masquez ce header pour réduire la surface d'attaque"
            : 'Masqué — bonne pratique',
    ];
    $checks[] = [
        'key'    => 'X-Powered-By',
        'short'  => 'Powered-By',
        'value'  => $poweredBy,
        'status' => $poweredBy ? 'bad' : 'ok',
        'info'   => $poweredBy
            ? "Expose la techno serveur — à supprimer absolument"
            : 'Masqué — bonne pratique',
    ];

    /* ── Score global ── */
    $pts = 0; $max = 0;
    foreach ($checks as $c) {
        $max += 2;
        $pts += match($c['status']) { 'ok' => 2, 'warn' => 1, default => 0 };
    }
    $score = $max > 0 ? round($pts / $max * 100) : 0;
    $grade = $score >= 90 ? 'A+' : ($score >= 75 ? 'A' : ($score >= 55 ? 'B' : ($score >= 35 ? 'C' : 'F')));

    /* ── Tous les headers bruts ── */
    echo json_encode([
        'url'         => $raw,
        'host'        => $host,
        'status_code' => $statusCode,
        'checks'      => $checks,
        'score'       => $score,
        'grade'       => $grade,
        'all_headers' => $headers,
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>HTTP Headers Analyzer — Black-Lab Toolbox</title>
<style>
html, body { height: 100%; overflow-y: auto; }
body { display: flex; flex-direction: column; }
.page-wrap { position: relative; z-index: 1; flex: 1; display: flex; flex-direction: column; }
.page-body {
    flex: 1; padding: 1rem 1.4rem 1.6rem;
    display: flex; flex-direction: column; gap: 1rem;
    max-width: 1200px; width: 100%; margin: 0 auto;
}

/* ── Layout résultat ── */
.result-grid {
    display: grid;
    grid-template-columns: 140px 1fr;
    gap: 1rem;
    align-items: start;
}
@media(max-width:640px) { .result-grid { grid-template-columns: 1fr; } }

/* ── Grade ── */
.grade-wrap { display: flex; flex-direction: column; align-items: center; gap: .4rem; }
.grade-badge {
    width: 80px; height: 80px; border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 2.1rem; font-weight: 900; border: 2px solid;
}
.grade-badge.Ap { background: rgba(39,201,63,.15);  color: #27c93f; border-color: #27c93f; }
.grade-badge.A  { background: rgba(39,201,63,.1);   color: #27c93f; border-color: rgba(39,201,63,.5); }
.grade-badge.B  { background: rgba(255,189,46,.12); color: #ffbd2e; border-color: #ffbd2e; }
.grade-badge.C  { background: rgba(255,126,0,.12);  color: #ff7e00; border-color: #ff7e00; }
.grade-badge.F  { background: rgba(255,22,84,.15);  color: #ff1654; border-color: #ff1654; }
.grade-label { font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--text-muted); }

/* ── Score bar ── */
.score-bar-bg {
    height: 6px; border-radius: 6px;
    background: var(--bg-2); border: 1px solid var(--border);
    overflow: hidden; margin-top: .35rem;
}
.score-bar-fill { height: 100%; border-radius: 6px; transition: width .7s ease; }

/* ── Checks list ── */
.check-item {
    display: grid;
    grid-template-columns: 90px 1fr auto;
    gap: .5rem 1rem;
    align-items: start;
    padding: .65rem 0;
    border-bottom: 1px solid var(--border);
}
.check-item:last-child { border: none; }
.check-key {
    font-size: .7rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .06em; color: var(--text-muted); padding-top: .1rem;
}
.check-body { display: flex; flex-direction: column; gap: .2rem; }
.check-val {
    font-size: .75rem; font-family: 'Fira Code', monospace;
    color: var(--text); word-break: break-all; line-height: 1.5;
    background: var(--bg-2); border: 1px solid var(--border);
    border-radius: 4px; padding: .25rem .5rem;
}
.check-val.absent { color: var(--text-muted); font-style: italic; }
.check-info { font-size: .75rem; color: var(--text-muted); }

/* ── Status dot ── */
.dot {
    width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;
    margin-top: .3rem; justify-self: end;
}
.dot.ok   { background: #27c93f; box-shadow: 0 0 6px rgba(39,201,63,.6); }
.dot.warn { background: #ffbd2e; box-shadow: 0 0 6px rgba(255,189,46,.6); }
.dot.bad  { background: #ff1654; box-shadow: 0 0 6px rgba(255,22,84,.6); }

/* ── Légende ── */
.legend { display: flex; gap: 1rem; font-size: .72rem; color: var(--text-muted); align-items: center; flex-wrap: wrap; }
.legend-item { display: flex; align-items: center; gap: .35rem; }

/* ── Tous les headers (raw) ── */
.raw-table { width: 100%; border-collapse: collapse; font-size: .76rem; }
.raw-table tr { border-bottom: 1px solid var(--border); }
.raw-table tr:last-child { border: none; }
.raw-table td { padding: .45rem .5rem; vertical-align: top; }
.raw-table td:first-child {
    font-family: 'Fira Code', monospace; color: var(--accent);
    white-space: nowrap; font-size: .72rem; width: 220px;
}
.raw-table td:last-child { color: var(--text); word-break: break-all; font-family: 'Fira Code', monospace; }

/* ── Collapse raw ── */
.raw-toggle {
    cursor: pointer; font-size: .75rem; color: var(--text-muted);
    display: flex; align-items: center; gap: .4rem; user-select: none;
}
.raw-toggle:hover { color: var(--text); }

/* ── Placeholder ── */
.placeholder {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; padding: 3rem; gap: .8rem;
    color: var(--text-muted); text-align: center;
}
.placeholder-icon { font-size: 3rem; opacity: .15; }

/* ── HTTP status badge ── */
.http-badge {
    display: inline-block; padding: .15rem .55rem; border-radius: 6px;
    font-size: .72rem; font-weight: 700; font-family: 'Fira Code', monospace;
}
.http-badge.s2 { background: rgba(39,201,63,.12);  color: #27c93f; border: 1px solid rgba(39,201,63,.4); }
.http-badge.s3 { background: rgba(255,189,46,.12); color: #ffbd2e; border: 1px solid rgba(255,189,46,.4); }
.http-badge.s4 { background: rgba(255,126,0,.12);  color: #ff7e00; border: 1px solid rgba(255,126,0,.4); }
.http-badge.s5 { background: rgba(255,22,84,.12);  color: #ff1654; border: 1px solid rgba(255,22,84,.4); }
</style>
</head>
<body>

<div class="ambient">
  <div class="ambient__circle"></div>
  <div class="ambient__circle"></div>
</div>

<div class="page-wrap">
<header class="hdr">
  <span class="hdr__icon">🛡️</span>
  <span class="hdr__title">HTTP HEADERS ANALYZER</span>
  <span class="hdr__sep"></span>
  <span class="hdr__meta">CSP · HSTS · X-Frame · Referrer · Score de sécurité</span>
</header>
<div class="page-body">

  <!-- Search -->
  <div style="display:flex;gap:.6rem">
    <input type="text" id="url-input" placeholder="Saisissez l'adresse web que vous souhaitez analyser" style="flex:1">
    <button class="btn btn-primary" onclick="runCheck()">🔍 Analyser</button>
  </div>

  <div id="loading" style="display:none;text-align:center;color:var(--text-muted);padding:2rem">⏳ Analyse des headers…</div>
  <div id="error-box" class="card" style="display:none;font-size:.85rem"></div>

  <div id="result" style="display:none;flex-direction:column;gap:1rem">

    <!-- Grade + score -->
    <div class="card">
      <div class="result-grid">
        <div class="grade-wrap">
          <div class="grade-badge" id="grade-badge">—</div>
          <div class="grade-label" id="grade-label">—</div>
        </div>
        <div style="display:flex;flex-direction:column;gap:.7rem">
          <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap">
            <span style="font-size:1rem;font-weight:700" id="res-host"></span>
            <span class="http-badge" id="http-badge"></span>
          </div>
          <div>
            <div style="display:flex;justify-content:space-between;font-size:.72rem;margin-bottom:.3rem">
              <span style="color:var(--text-muted)">Score de sécurité</span>
              <span style="font-weight:700" id="score-label"></span>
            </div>
            <div class="score-bar-bg">
              <div class="score-bar-fill" id="score-bar" style="width:0%"></div>
            </div>
          </div>
          <div class="legend">
            <div class="legend-item"><div class="dot ok"></div> OK</div>
            <div class="legend-item"><div class="dot warn"></div> Attention</div>
            <div class="legend-item"><div class="dot bad"></div> Problème</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Checks sécurité -->
    <div class="card">
      <div class="card-title">Analyse des headers de sécurité</div>
      <div id="checks-list"></div>
    </div>

    <!-- Tous les headers (collapse) -->
    <div class="card">
      <div class="raw-toggle" onclick="toggleRaw()">
        <span id="raw-arrow">▶</span>
        <span>Tous les headers HTTP bruts</span>
        <span style="font-size:.68rem;opacity:.5" id="raw-count"></span>
      </div>
      <div id="raw-headers" style="display:none;margin-top:.8rem;overflow-x:auto">
        <table class="raw-table" id="raw-table"></table>
      </div>
    </div>

  </div>

  <div class="placeholder card" id="placeholder">
    <div class="placeholder-icon">🛡️</div>
    <div>Entrez une URL pour analyser ses headers de sécurité</div>
    <div style="font-size:.78rem;opacity:.5">HSTS · CSP · X-Frame-Options · Referrer-Policy · Permissions-Policy…</div>
  </div>

</div>
</div>
<div class="toast-area" id="ta"></div>

<script>
const $ = id => document.getElementById(id);
const GRADE_LABELS = { 'A+':'Excellent', A:'Bon', B:'Correct', C:'Faible', F:'Insuffisant' };
let rawVisible = false;

async function runCheck() {
  const url = $('url-input').value.trim();
  if (!url) return;

  $('placeholder').style.display = 'none';
  $('result').style.display      = 'none';
  $('error-box').style.display   = 'none';
  $('loading').style.display     = 'block';

  try {
    const res  = await fetch(`?check=1&url=${encodeURIComponent(url)}`);
    const data = await res.json();
    $('loading').style.display = 'none';

    if (data.error) {
      $('error-box').style.display   = 'block';
      $('error-box').textContent     = '❌ ' + data.error;
      return;
    }
    render(data);
  } catch(e) {
    $('loading').style.display   = 'none';
    $('error-box').style.display = 'block';
    $('error-box').textContent   = '❌ Erreur réseau : ' + e.message;
  }
}

function render(d) {
  /* Grade */
  const gc = d.grade.replace('+','p');
  $('grade-badge').textContent = d.grade;
  $('grade-badge').className   = `grade-badge ${gc}`;
  $('grade-label').textContent = GRADE_LABELS[d.grade] || d.grade;

  /* Host + HTTP status */
  $('res-host').textContent = d.host;
  const sc  = d.status_code;
  const scl = sc >= 500 ? 's5' : sc >= 400 ? 's4' : sc >= 300 ? 's3' : 's2';
  $('http-badge').textContent = 'HTTP ' + sc;
  $('http-badge').className   = 'http-badge ' + scl;

  /* Score bar */
  const color = d.score >= 75 ? '#27c93f' : d.score >= 50 ? '#ffbd2e' : '#ff1654';
  $('score-bar').style.cssText = `width:${d.score}%;background:${color};height:100%;border-radius:6px;transition:width .7s ease`;
  $('score-label').style.color = color;
  $('score-label').textContent = d.score + ' / 100';

  /* Checks */
  $('checks-list').innerHTML = d.checks.map(c => `
    <div class="check-item">
      <div class="check-key">${c.short}</div>
      <div class="check-body">
        <div class="check-val ${c.value ? '' : 'absent'}">${c.value || 'Non défini'}</div>
        <div class="check-info">${c.info}</div>
      </div>
      <div class="dot ${c.status}"></div>
    </div>`).join('');

  /* Raw headers */
  const entries = Object.entries(d.all_headers);
  $('raw-count').textContent = `(${entries.length} headers)`;
  $('raw-table').innerHTML   = entries.map(([k, v]) =>
    `<tr><td>${k}</td><td>${escHtml(v)}</td></tr>`
  ).join('');

  $('result').style.display = 'flex';
}

function toggleRaw() {
  rawVisible = !rawVisible;
  $('raw-headers').style.display = rawVisible ? 'block' : 'none';
  $('raw-arrow').textContent     = rawVisible ? '▼' : '▶';
}

function escHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

$('url-input').addEventListener('keydown', e => e.key === 'Enter' && runCheck());

const urlParam = new URLSearchParams(location.search).get('url');
if (urlParam) { $('url-input').value = urlParam; runCheck(); }
</script>
</body>
</html>
