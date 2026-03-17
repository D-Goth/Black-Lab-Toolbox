<?php
/* ══════════════════════════════════════════════════════════
   HELPERS
══════════════════════════════════════════════════════════ */
function parseWhoisDate(string $raw): ?int {
    $raw = trim($raw);
    // Formats courants : 2025-12-31, 2025-12-31T00:00:00Z, 31/12/2025, 31-Dec-2025
    $formats = [
        'Y-m-d\TH:i:s\Z', 'Y-m-d\TH:i:sP', 'Y-m-d H:i:s',
        'Y-m-d', 'd/m/Y', 'd-M-Y', 'D M d H:i:s T Y',
    ];
    foreach ($formats as $fmt) {
        $dt = DateTime::createFromFormat($fmt, $raw);
        if ($dt) return $dt->getTimestamp();
    }
    $ts = strtotime($raw);
    return $ts !== false ? $ts : null;
}

/* ══════════════════════════════════════════════════════════
   API endpoint
══════════════════════════════════════════════════════════ */
if (isset($_GET['check'])) {
    header('Content-Type: application/json');

    $raw    = trim($_GET['domain'] ?? '');
    $domain = preg_replace('#^https?://#', '', $raw);
    $domain = strtok($domain, '/');
    $domain = strtok($domain, ':');

    if (empty($domain) || !preg_match('/^[a-z0-9.\-]+\.[a-z]{2,}$/i', $domain)) {
        echo json_encode(['error' => 'Domaine invalide']); exit;
    }

    $result = ['domain' => $domain, 'ssl' => null, 'whois' => null, 'error' => null];

    /* ══════════════════════
       1. SSL
    ══════════════════════ */
    $verify_failed = false;
    $ctx = stream_context_create(['ssl' => [
        'capture_peer_cert' => true, 'capture_peer_cert_chain' => true,
        'verify_peer' => true, 'verify_peer_name' => true,
        'SNI_enabled' => true, 'peer_name' => $domain,
    ]]);
    $client = @stream_socket_client("ssl://{$domain}:443", $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $ctx);
    if (!$client) {
        $ctx2 = stream_context_create(['ssl' => [
            'capture_peer_cert' => true, 'capture_peer_cert_chain' => true,
            'verify_peer' => false, 'verify_peer_name' => false,
            'SNI_enabled' => true, 'peer_name' => $domain,
        ]]);
        $client = @stream_socket_client("ssl://{$domain}:443", $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $ctx2);
        $verify_failed = true;
    }

    if ($client) {
        $params = stream_context_get_params($client);
        $cert   = $params['options']['ssl']['peer_certificate'];
        $chain  = $params['options']['ssl']['peer_certificate_chain'] ?? [];
        fclose($client);

        if ($cert) {
            $info      = openssl_x509_parse($cert);
            $now       = time();
            $validFrom = $info['validFrom_time_t'];
            $validTo   = $info['validTo_time_t'];
            $daysLeft  = (int) round(($validTo - $now) / 86400);
            $totalDays = (int) round(($validTo - $validFrom) / 86400);
            $subject   = $info['subject'] ?? [];
            $issuer    = $info['issuer']  ?? [];
            $selfSigned= ($subject['CN'] ?? 'a') === ($issuer['CN'] ?? 'b');
            $sans = [];
            if (!empty($info['extensions']['subjectAltName'])) {
                preg_match_all('/DNS:([^,\s]+)/', $info['extensions']['subjectAltName'], $m);
                $sans = $m[1] ?? [];
            }
            $keyType = '—'; $keyBits = null;
            $pubkey  = openssl_pkey_get_public($cert);
            if ($pubkey) {
                $kd = openssl_pkey_get_details($pubkey);
                $keyBits = $kd['bits'] ?? null;
                $keyType = match($kd['type'] ?? -1) {
                    OPENSSL_KEYTYPE_RSA => 'RSA', OPENSSL_KEYTYPE_EC => 'ECDSA',
                    OPENSSL_KEYTYPE_DSA => 'DSA', default => '—'
                };
            }
            $hsts = false;
            $hdrs = @get_headers("https://{$domain}", 1);
            if ($hdrs) foreach ($hdrs as $k => $v)
                if (strtolower((string)$k) === 'strict-transport-security') { $hsts = true; break; }

            $expired = $now > $validTo;
            $grade_ssl = match(true) {
                $verify_failed || $expired => 'F',
                $selfSigned || $daysLeft < 7 => 'C',
                $daysLeft < 30 => 'B',
                $daysLeft >= 90 && $hsts => 'A+',
                default => 'A'
            };

            $result['ssl'] = [
                'subject_cn'  => $subject['CN'] ?? $domain,
                'subject_o'   => $subject['O']  ?? '—',
                'issuer_cn'   => $issuer['CN']  ?? '—',
                'issuer_o'    => $issuer['O']   ?? '—',
                'valid_from'  => date('d/m/Y', $validFrom),
                'valid_to'    => date('d/m/Y', $validTo),
                'days_left'   => $daysLeft,
                'total_days'  => $totalDays,
                'expired'     => $expired,
                'self_signed' => $selfSigned,
                'sans'        => $sans,
                'chain_depth' => count($chain),
                'hsts'        => $hsts,
                'grade'       => $grade_ssl,
                'sig_alg'     => $info['signatureTypeSN'] ?? '—',
                'key_type'    => $keyType,
                'key_bits'    => $keyBits,
            ];
        }
    } else {
        $result['ssl'] = ['error' => "SSL inaccessible : $errstr"];
    }

    /* ══════════════════════
       2. WHOIS
    ══════════════════════ */
    $tld        = strtolower(substr(strrchr($domain, '.'), 1));
    $whoisHost  = null;
    $whoisMap   = [
        'fr'=>'whois.nic.fr','com'=>'whois.verisign-grs.com','net'=>'whois.verisign-grs.com',
        'org'=>'whois.pir.org','io'=>'whois.nic.io','dev'=>'whois.nic.google',
        'app'=>'whois.nic.google','info'=>'whois.afilias.net','eu'=>'whois.eu',
        'be'=>'whois.dns.be','de'=>'whois.denic.de','uk'=>'whois.nic.uk',
        'co'=>'whois.nic.co','me'=>'whois.nic.me','biz'=>'whois.biz',
        'shop'=>'whois.nic.shop','tech'=>'whois.nic.tech',
    ];
    $whoisHost = $whoisMap[$tld] ?? 'whois.iana.org';

    $whoisRaw = '';
    $sock = @fsockopen($whoisHost, 43, $errno, $errstr, 8);
    if ($sock) {
        fwrite($sock, $domain . "\r\n");
        while (!feof($sock)) $whoisRaw .= fgets($sock, 4096);
        fclose($sock);
    }

    // Si IANA redirige
    if (str_contains($whoisRaw, 'refer:')) {
        preg_match('/refer:\s*(\S+)/', $whoisRaw, $ref);
        if (!empty($ref[1])) {
            $sock2 = @fsockopen(trim($ref[1]), 43, $e2, $e2s, 8);
            if ($sock2) {
                fwrite($sock2, $domain . "\r\n");
                $whoisRaw = '';
                while (!feof($sock2)) $whoisRaw .= fgets($sock2, 4096);
                fclose($sock2);
            }
        }
    }

    $w = ['raw' => $whoisRaw, 'expiry' => null, 'created' => null, 'updated' => null,
          'registrar' => null, 'nameservers' => [], 'status' => [], 'days_left' => null,
          'grade' => null, 'error' => null];

    if (!$whoisRaw) {
        $w['error'] = 'WHOIS indisponible pour ce TLD';
    } else {
        $lines = explode("\n", $whoisRaw);
        $ns    = [];
        $st    = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (!$line || str_starts_with($line, '%') || str_starts_with($line, '#')) continue;
            if (!str_contains($line, ':')) continue;
            [$key, $val] = array_map('trim', explode(':', $line, 2));
            $kl = strtolower($key);
            if (in_array($kl, ['expiry date','expiration date','registry expiry date','paid-till','expires','expire date','expiration time','validity']))
                if (!$w['expiry'] && $val) $w['expiry'] = $val;
            if (in_array($kl, ['creation date','created','created date','registration time','registration date']))
                if (!$w['created'] && $val) $w['created'] = $val;
            if (in_array($kl, ['updated date','last-changed','last updated','modified','last update']))
                if (!$w['updated'] && $val) $w['updated'] = $val;
            if (in_array($kl, ['registrar','registrar name','registrar handle']))
                if (!$w['registrar'] && $val) $w['registrar'] = $val;
            if (str_contains($kl, 'name server') || $kl === 'nserver')
                if ($val) $ns[] = strtolower(explode(' ', $val)[0]);
            if ($kl === 'domain status' || $kl === 'status')
                if ($val) $st[] = trim(explode(' ', $val)[0]);
        }
        $w['nameservers'] = array_unique(array_filter($ns));
        $w['status']      = array_unique(array_filter($st));

        if ($w['expiry']) {
            $ts = parseWhoisDate($w['expiry']);
            if ($ts) {
                $w['days_left']  = (int) round(($ts - time()) / 86400);
                $w['expiry_fmt'] = date('d/m/Y', $ts);
            }
        }
        if ($w['created']) { $ts = parseWhoisDate($w['created']); if ($ts) $w['created_fmt'] = date('d/m/Y', $ts); }
        if ($w['updated']) { $ts = parseWhoisDate($w['updated']); if ($ts) $w['updated_fmt'] = date('d/m/Y', $ts); }

        $dl = $w['days_left'];
        $w['grade'] = match(true) {
            $dl === null      => 'N/A',
            $dl < 0           => 'F',
            $dl < 7           => 'F',
            $dl < 30          => 'C',
            $dl < 90          => 'B',
            default           => 'A',
        };
    }
    $result['whois'] = $w;

    /* ══════════════════════
       3. Score global
    ══════════════════════ */
    $gradeMap  = ['A+'=>5,'A'=>4,'B'=>3,'C'=>2,'F'=>1,'N/A'=>0];
    $sslScore  = $gradeMap[$result['ssl']['grade'] ?? 'F'] ?? 1;
    $whoisScore= $gradeMap[$result['whois']['grade'] ?? 'N/A'] ?? 0;
    $total     = $sslScore + $whoisScore;
    $result['global_grade'] = match(true) {
        $total >= 9  => 'A+',
        $total >= 7  => 'A',
        $total >= 5  => 'B',
        $total >= 3  => 'C',
        default      => 'F',
    };

    echo json_encode($result);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>SSL & Domain Checker — Black-Lab Toolbox</title>
<style>
html, body { height: 100%; overflow-y: auto; }
body { display: flex; flex-direction: column; }
.page-wrap { position: relative; z-index: 1; flex: 1; display: flex; flex-direction: column; }
.page-body {
    flex: 1; padding: 1rem 1.4rem 1.6rem;
    display: flex; flex-direction: column; gap: 1rem;
    max-width: 1200px; width: 100%; margin: 0 auto;
}

/* ── Grade ── */
.grade-wrap { display: flex; flex-direction: column; align-items: center; gap: .35rem; }
.grade-badge {
    width: 72px; height: 72px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.9rem; font-weight: 900; border: 2px solid;
    flex-shrink: 0;
}
.grade-badge.Ap { background: rgba(39,201,63,.15);  color: #27c93f; border-color: #27c93f; }
.grade-badge.A  { background: rgba(39,201,63,.1);   color: #27c93f; border-color: rgba(39,201,63,.5); }
.grade-badge.B  { background: rgba(255,189,46,.12); color: #ffbd2e; border-color: #ffbd2e; }
.grade-badge.C  { background: rgba(255,126,0,.12);  color: #ff7e00; border-color: #ff7e00; }
.grade-badge.F  { background: rgba(255,22,84,.15);  color: #ff1654; border-color: #ff1654; }
.grade-badge.NA { background: var(--bg-2); color: var(--text-muted); border-color: var(--border); }
.grade-label { font-size: .62rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--text-muted); text-align: center; }

/* ── Expiry bar ── */
.expiry-bar-bg { height: 5px; border-radius: 5px; background: var(--bg-2); border: 1px solid var(--border); overflow: hidden; margin-top: .3rem; }
.expiry-bar-fill { height: 100%; border-radius: 5px; transition: width .7s ease; }

/* ── Status chips ── */
.schip { display: inline-flex; align-items: center; gap: .3rem; padding: .18rem .6rem; border-radius: 20px; font-size: .71rem; font-weight: 600; border: 1px solid; }
.schip.ok   { background: rgba(39,201,63,.1);  color: #27c93f; border-color: rgba(39,201,63,.4); }
.schip.warn { background: rgba(255,189,46,.1); color: #ffbd2e; border-color: rgba(255,189,46,.4); }
.schip.bad  { background: rgba(255,22,84,.1);  color: #ff1654; border-color: rgba(255,22,84,.4); }

/* ── Info grid ── */
.info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(185px,1fr)); gap: .6rem; }
.info-item { display: flex; flex-direction: column; gap: .1rem; }
.info-label { font-size: .6rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--text-muted); }
.info-val   { font-size: .82rem; font-weight: 600; color: var(--text); font-family: 'Fira Code', monospace; word-break: break-all; }

/* ── SANs ── */
.san-list { display: flex; flex-wrap: wrap; gap: .3rem; margin-top: .4rem; }
.san-chip { padding: .12rem .5rem; border-radius: 20px; font-size: .7rem; font-family: 'Fira Code', monospace; background: var(--bg-2); border: 1px solid var(--border); color: var(--text-muted); }
.san-chip.match { border-color: rgba(39,201,63,.5); color: #27c93f; background: rgba(39,201,63,.08); }

/* ── NS chips ── */
.ns-chip { padding: .12rem .5rem; border-radius: 6px; font-size: .72rem; font-family: 'Fira Code', monospace; background: var(--bg-2); border: 1px solid var(--border); color: var(--text-muted); }

/* ── Section header avec grade inline ── */
.section-head { display: flex; align-items: center; gap: .75rem; margin-bottom: .75rem; }
.section-head .card-title { margin: 0; }

/* ── Global grade card ── */
.global-card {
    display: grid; grid-template-columns: 90px 1fr; gap: 1rem; align-items: center;
}
@media(max-width:560px) { .global-card { grid-template-columns: 1fr; } }

/* ── Two cols ── */
.two-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
@media(max-width:760px) { .two-cols { grid-template-columns: 1fr; } }

/* ── Placeholder ── */
.placeholder { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem; gap: .8rem; color: var(--text-muted); text-align: center; }
.placeholder-icon { font-size: 3rem; opacity: .15; }

/* ── Alert banner ── */
.alert-banner { display: flex; align-items: center; gap: .6rem; padding: .65rem 1rem; border-radius: var(--radius-sm); font-size: .8rem; font-weight: 600; border: 1px solid; }
.alert-banner.red    { background: rgba(255,22,84,.1);  color: #ff1654; border-color: rgba(255,22,84,.35); }
.alert-banner.orange { background: rgba(255,126,0,.1);  color: #ff7e00; border-color: rgba(255,126,0,.35); }
.alert-banner.yellow { background: rgba(255,189,46,.1); color: #ffbd2e; border-color: rgba(255,189,46,.35); }
</style>
</head>
<body>

<div class="ambient">
  <div class="ambient__circle"></div>
  <div class="ambient__circle"></div>
</div>

<div class="page-wrap">
<header class="hdr">
  <span class="hdr__icon">🔒</span>
  <span class="hdr__title">SSL & DOMAIN CHECKER</span>
  <span class="hdr__sep"></span>
  <span class="hdr__meta">Certificat SSL · Expiration domaine · WHOIS · Grade global</span>
</header>
<div class="page-body">

  <div style="display:flex;gap:.6rem">
    <input type="text" id="domain-input" placeholder="black-lab.fr" style="flex:1">
    <button class="btn btn-primary" onclick="runCheck()">🔍 Analyser</button>
  </div>

  <div id="loading" style="display:none;text-align:center;color:var(--text-muted);padding:2rem">⏳ Analyse SSL + WHOIS en cours…</div>
  <div id="error-box" class="card" style="display:none;font-size:.85rem"></div>

  <div id="result" style="display:none;flex-direction:column;gap:1rem">

    <!-- Alertes urgentes -->
    <div id="alerts"></div>

    <!-- Grade global -->
    <div class="card">
      <div class="global-card">
        <div class="grade-wrap">
          <div class="grade-badge" id="global-grade-badge">—</div>
          <div class="grade-label">Score global</div>
        </div>
        <div style="display:flex;flex-direction:column;gap:.5rem">
          <div style="font-size:1.05rem;font-weight:700" id="res-domain"></div>
          <div style="display:flex;gap:.5rem;flex-wrap:wrap" id="global-chips"></div>
        </div>
      </div>
    </div>

    <!-- SSL + WHOIS côte à côte -->
    <div class="two-cols">

      <!-- SSL -->
      <div class="card">
        <div class="section-head">
          <div class="card-title">🔒 Certificat SSL</div>
          <div class="grade-wrap" style="flex-direction:row;gap:.4rem;align-items:center">
            <div class="grade-badge" id="ssl-grade-badge" style="width:36px;height:36px;font-size:1rem;border-radius:8px">—</div>
          </div>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:.35rem;margin-bottom:.75rem" id="ssl-chips"></div>
        <div style="margin-bottom:.75rem">
          <div style="display:flex;justify-content:space-between;font-size:.72rem;margin-bottom:.3rem">
            <span style="color:var(--text-muted)">Expiration SSL</span>
            <span style="font-weight:600" id="ssl-days-label"></span>
          </div>
          <div class="expiry-bar-bg"><div class="expiry-bar-fill" id="ssl-bar" style="width:0%"></div></div>
        </div>
        <div class="info-grid" id="ssl-details"></div>
        <div id="sans-wrap" style="margin-top:.75rem;display:none">
          <div style="font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-bottom:.4rem">Domaines couverts (SAN)</div>
          <div class="san-list" id="san-list"></div>
        </div>
      </div>

      <!-- WHOIS -->
      <div class="card">
        <div class="section-head">
          <div class="card-title">🌐 WHOIS &amp; Domaine</div>
          <div class="grade-wrap" style="flex-direction:row;gap:.4rem;align-items:center">
            <div class="grade-badge" id="whois-grade-badge" style="width:36px;height:36px;font-size:1rem;border-radius:8px">—</div>
          </div>
        </div>
        <div id="whois-error" style="display:none;font-size:.8rem;color:var(--text-muted);font-style:italic;margin-bottom:.75rem"></div>
        <div style="margin-bottom:.75rem">
          <div style="display:flex;justify-content:space-between;font-size:.72rem;margin-bottom:.3rem">
            <span style="color:var(--text-muted)">Expiration domaine</span>
            <span style="font-weight:600" id="whois-days-label"></span>
          </div>
          <div class="expiry-bar-bg"><div class="expiry-bar-fill" id="whois-bar" style="width:0%"></div></div>
        </div>
        <div class="info-grid" id="whois-details"></div>
        <div id="ns-wrap" style="margin-top:.75rem;display:none">
          <div style="font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-bottom:.4rem">Nameservers</div>
          <div style="display:flex;flex-wrap:wrap;gap:.3rem" id="ns-list"></div>
        </div>
        <div id="status-wrap" style="margin-top:.75rem;display:none">
          <div style="font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-bottom:.4rem">Statuts WHOIS</div>
          <div style="display:flex;flex-wrap:wrap;gap:.3rem" id="status-list"></div>
        </div>
      </div>

    </div><!-- /two-cols -->

  </div><!-- /#result -->

  <div class="placeholder card" id="placeholder">
    <div class="placeholder-icon">🔒</div>
    <div>Entrez un domaine pour analyser SSL &amp; WHOIS</div>
    <div style="font-size:.78rem;opacity:.5">Certificat · Expiration · Registrar · Nameservers · Grade global</div>
  </div>

</div>
</div>
<div class="toast-area" id="ta"></div>

<script>
const $ = id => document.getElementById(id);
const GRADE_LABELS = { 'A+':'Excellent', A:'Bon', B:'Attention', C:'Faible', F:'Échec', 'N/A':'Inconnu' };

async function runCheck() {
  const domain = $('domain-input').value.trim();
  if (!domain) return;

  $('placeholder').style.display = 'none';
  $('result').style.display      = 'none';
  $('error-box').style.display   = 'none';
  $('loading').style.display     = 'block';

  try {
    const res  = await fetch(`?check=1&domain=${encodeURIComponent(domain)}`);
    const data = await res.json();
    $('loading').style.display = 'none';
    if (data.error) { showErr(data.error); return; }
    render(data);
  } catch(e) {
    $('loading').style.display = 'none';
    showErr('Erreur réseau : ' + e.message);
  }
}

function showErr(msg) {
  $('error-box').style.display = 'block';
  $('error-box').textContent   = '❌ ' + msg;
}

/* ══════════════════════════════════════════
   RENDER
══════════════════════════════════════════ */
function render(d) {
  const ssl   = d.ssl   || {};
  const whois = d.whois || {};

  /* ── Alerts ── */
  const alerts = [];
  if (ssl.days_left !== undefined && ssl.days_left < 30 && !ssl.expired)
    alerts.push({ cls: ssl.days_left < 7 ? 'red' : 'orange', msg: `⚠️ Certificat SSL expire dans ${ssl.days_left} jour${ssl.days_left > 1 ? 's' : ''} !` });
  if (ssl.expired)
    alerts.push({ cls: 'red', msg: '🚨 Certificat SSL expiré !' });
  if (whois.days_left !== null && whois.days_left !== undefined && whois.days_left < 60 && whois.days_left >= 0)
    alerts.push({ cls: whois.days_left < 14 ? 'red' : 'yellow', msg: `⚠️ Domaine expire dans ${whois.days_left} jour${whois.days_left > 1 ? 's' : ''} — pensez à renouveler !` });
  if (whois.days_left < 0)
    alerts.push({ cls: 'red', msg: '🚨 Domaine expiré !' });

  $('alerts').innerHTML = alerts.map(a =>
    `<div class="alert-banner ${a.cls}">${a.msg}</div>`
  ).join('');

  /* ── Grade global ── */
  const gg = d.global_grade.replace('+','p');
  $('global-grade-badge').textContent = d.global_grade;
  $('global-grade-badge').className   = `grade-badge ${gg}`;
  $('res-domain').textContent         = d.domain;
  $('global-chips').innerHTML = [
    { label: '🔒 SSL : ' + (ssl.grade || '—'),   cls: gradeChipCls(ssl.grade) },
    { label: '🌐 Domaine : ' + (whois.grade || '—'), cls: gradeChipCls(whois.grade) },
  ].map(c => `<span class="schip ${c.cls}">${c.label}</span>`).join('');

  /* ── SSL ── */
  renderSSL(ssl, d.domain);

  /* ── WHOIS ── */
  renderWHOIS(whois);

  $('result').style.display = 'flex';
}

function gradeChipCls(g) {
  return g === 'A+' || g === 'A' ? 'ok' : g === 'B' ? 'warn' : 'bad';
}

function renderSSL(ssl, domain) {
  if (ssl.error) {
    $('ssl-grade-badge').textContent = 'F';
    $('ssl-grade-badge').className   = 'grade-badge F';
    $('ssl-details').innerHTML = `<div style="color:var(--text-muted);font-size:.8rem;font-style:italic">${ssl.error}</div>`;
    return;
  }
  const gc = (ssl.grade||'F').replace('+','p');
  $('ssl-grade-badge').textContent = ssl.grade || 'F';
  $('ssl-grade-badge').className   = `grade-badge ${gc}`;

  // Chips
  $('ssl-chips').innerHTML = [
    { ok: !ssl.expired && !ssl.self_signed, label: ssl.expired ? '✗ Expiré' : ssl.self_signed ? '⚠ Auto-signé' : '✓ Valide' },
    { ok: ssl.hsts, label: ssl.hsts ? '✓ HSTS' : '✗ HSTS absent' },
    { ok: ssl.chain_depth > 1, label: `⛓ Chaîne : ${ssl.chain_depth}` },
  ].map(c => `<span class="schip ${c.ok ? 'ok' : 'bad'}">${c.label}</span>`).join('');

  // Bar
  const pct   = Math.max(0, Math.min(100, Math.round(ssl.days_left / ssl.total_days * 100)));
  const color = ssl.days_left < 7 ? '#ff1654' : ssl.days_left < 30 ? '#ff7e00' : ssl.days_left < 90 ? '#ffbd2e' : '#27c93f';
  $('ssl-bar').style.cssText    = `width:${pct}%;background:${color};height:100%;border-radius:5px;transition:width .7s`;
  $('ssl-days-label').style.color = color;
  $('ssl-days-label').textContent = ssl.expired
    ? `⛔ Expiré le ${ssl.valid_to}`
    : `${ssl.days_left}j — expire le ${ssl.valid_to}`;

  // Details
  $('ssl-details').innerHTML = [
    ['Émetteur',   ssl.issuer_o],
    ['Valide depuis', ssl.valid_from],
    ['Expire le',  ssl.valid_to],
    ['Algorithme', ssl.sig_alg],
    ['Clé',        ssl.key_type + (ssl.key_bits ? ` ${ssl.key_bits}b` : '')],
  ].map(([k,v]) => `<div class="info-item"><div class="info-label">${k}</div><div class="info-val">${v||'—'}</div></div>`).join('');

  // SANs
  if (ssl.sans?.length) {
    const root = domain.split('.').slice(-2).join('.');
    $('san-list').innerHTML = ssl.sans.map(s => {
      const match = s === domain || s === `*.${root}`;
      return `<span class="san-chip ${match ? 'match' : ''}">${s}</span>`;
    }).join('');
    $('sans-wrap').style.display = 'block';
  }
}

function renderWHOIS(w) {
  const gc = (w.grade||'NA').replace('+','p').replace('/','');
  $('whois-grade-badge').textContent = w.grade || '—';
  $('whois-grade-badge').className   = `grade-badge ${gc === 'NA' ? 'NA' : gc}`;

  if (w.error) {
    $('whois-error').style.display  = 'block';
    $('whois-error').textContent    = '⚠ ' + w.error;
    $('whois-days-label').textContent = '—';
    return;
  }

  // Bar
  if (w.days_left !== null && w.days_left !== undefined) {
    const maxDays = 365;
    const pct     = Math.max(0, Math.min(100, Math.round(w.days_left / maxDays * 100)));
    const color   = w.days_left < 14 ? '#ff1654' : w.days_left < 60 ? '#ff7e00' : w.days_left < 90 ? '#ffbd2e' : '#27c93f';
    $('whois-bar').style.cssText        = `width:${pct}%;background:${color};height:100%;border-radius:5px;transition:width .7s`;
    $('whois-days-label').style.color   = color;
    $('whois-days-label').textContent   = w.days_left < 0
      ? `⛔ Expiré le ${w.expiry_fmt || '—'}`
      : `${w.days_left}j — expire le ${w.expiry_fmt || '—'}`;
  } else {
    $('whois-days-label').textContent = 'Date non trouvée';
    $('whois-days-label').style.color = 'var(--text-muted)';
  }

  // Details
  $('whois-details').innerHTML = [
    ['Registrar',   w.registrar],
    ['Créé le',     w.created_fmt],
    ['Mis à jour',  w.updated_fmt],
    ['Expire le',   w.expiry_fmt],
  ].map(([k,v]) => `<div class="info-item"><div class="info-label">${k}</div><div class="info-val">${v||'—'}</div></div>`).join('');

  // Nameservers
  if (w.nameservers?.length) {
    $('ns-list').innerHTML = w.nameservers.map(ns =>
      `<span class="ns-chip">${ns}</span>`).join('');
    $('ns-wrap').style.display = 'block';
  }

  // Statuts
  if (w.status?.length) {
    $('status-list').innerHTML = w.status.map(s =>
      `<span class="ns-chip">${s}</span>`).join('');
    $('status-wrap').style.display = 'block';
  }
}

$('domain-input').addEventListener('keydown', e => e.key === 'Enter' && runCheck());

const urlParam = new URLSearchParams(location.search).get('domain');
if (urlParam) { $('domain-input').value = urlParam; runCheck(); }
</script>
</body>
</html>