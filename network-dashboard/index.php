<?php
/**
 * Network Dashboard — Black-Lab Toolbox
 * Scanner de services réseau — ports, HTTP, dépôts Git.
 * Protégé par mot de passe — démo publique disponible.
 */

session_start();

/* ══════════════════════════════════════════════════════════════
   AUTHENTIFICATION
══════════════════════════════════════════════════════════════ */

// Récupère le mot de passe depuis les settings BDD (ou fallback)
function get_nd_password(): string {
    if (function_exists('setting')) {
        $p = setting('network_dashboard_password');
        if ($p) return $p;
    }
    return 'blacklab2025'; // fallback si setting() indisponible
}

$nd_auth    = !empty($_SESSION['nd_auth']);
$nd_error   = '';
$show_login = isset($_GET['login']) || isset($_POST['nd_password']);

// Traitement soumission mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nd_password'])) {
    if ($_POST['nd_password'] === get_nd_password()) {
        $_SESSION['nd_auth'] = true;
        $nd_auth = true;
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
        exit;
    } else {
        $nd_error   = 'Mot de passe incorrect.';
        $show_login = true;
    }
}

// Déconnexion
if (isset($_GET['logout'])) {
    unset($_SESSION['nd_auth']);
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

/* ══════════════════════════════════════════════════════════════
   UTILITAIRES
══════════════════════════════════════════════════════════════ */

function sanitize_host($raw) {
    $host = trim(strip_tags($raw));
    $host = preg_replace('/[^a-zA-Z0-9.\-_:]/', '', $host);
    if (strlen($host) < 1 || strlen($host) > 253) return false;
    if (filter_var($host, FILTER_VALIDATE_IP)) return $host;
    if (filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) return $host;
    if (preg_match('/^[a-zA-Z0-9][a-zA-Z0-9.\-_]*$/', $host)) return $host;
    return false;
}

function get_ports_list() {
    return [
        22   => ['label' => 'SSH',        'cat' => 'system', 'icon' => 'terminal'],
        80   => ['label' => 'HTTP',        'cat' => 'web',    'icon' => 'globe'],
        443  => ['label' => 'HTTPS',       'cat' => 'web',    'icon' => 'lock'],
        3000 => ['label' => 'Dev :3000',   'cat' => 'dev',    'icon' => 'code'],
        3001 => ['label' => 'Dev :3001',   'cat' => 'dev',    'icon' => 'code'],
        3002 => ['label' => 'Dev :3002',   'cat' => 'dev',    'icon' => 'code'],
        5000 => ['label' => 'Dev :5000',   'cat' => 'dev',    'icon' => 'code'],
        5001 => ['label' => 'Dev :5001',   'cat' => 'dev',    'icon' => 'code'],
        8080 => ['label' => 'HTTP-Alt',    'cat' => 'web',    'icon' => 'globe'],
        8081 => ['label' => 'HTTP-Alt2',   'cat' => 'web',    'icon' => 'globe'],
        9000 => ['label' => 'PHP-FPM',     'cat' => 'system', 'icon' => 'php'],
        3306 => ['label' => 'MySQL',       'cat' => 'db',     'icon' => 'database'],
        5432 => ['label' => 'PostgreSQL',  'cat' => 'db',     'icon' => 'database'],
        6379 => ['label' => 'Redis',       'cat' => 'db',     'icon' => 'database'],
        27017=> ['label' => 'MongoDB',     'cat' => 'db',     'icon' => 'database'],
    ];
}

/* ══════════════════════════════════════════════════════════════
   FONCTIONS DE SCAN (uniquement si authentifié)
══════════════════════════════════════════════════════════════ */

function scan_port($host, $port, $timeout = 1) {
    $conn = @fsockopen($host, $port, $errno, $errstr, $timeout);
    if ($conn) { fclose($conn); return true; }
    return false;
}

function fetch_http_meta($url, $timeout = 2) {
    $ctx = stream_context_create([
        'http' => ['timeout' => $timeout, 'follow_location' => 1, 'max_redirects' => 3,
                   'user_agent' => 'BlackLab-Dashboard/1.0', 'ignore_errors' => true],
        'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false],
    ]);
    $body    = @file_get_contents($url, false, $ctx);
    $headers = $http_response_header ?? [];
    if ($body === false) return null;
    $http_code = 0; $server = ''; $mime = '';
    foreach ($headers as $h) {
        if (preg_match('#HTTP/\S+\s+(\d+)#', $h, $m)) $http_code = (int)$m[1];
        if (preg_match('/^Server:\s*(.+)$/i', $h, $m))         $server = trim($m[1]);
        if (preg_match('/^Content-Type:\s*([^;]+)/i', $h, $m)) $mime   = trim($m[1]);
    }
    $title = '';
    if (preg_match('/<title[^>]*>([^<]{1,200})<\/title>/is', $body, $m))
        $title = trim(html_entity_decode($m[1], ENT_QUOTES, 'UTF-8'));
    return ['url' => $url, 'code' => $http_code, 'title' => $title,
            'server' => $server, 'mime' => $mime, 'size' => strlen($body),
            'headers' => array_slice($headers, 0, 10)];
}

function scan_ports($host) {
    $open = [];
    foreach (get_ports_list() as $port => $info)
        if (scan_port($host, $port)) $open[$port] = $info;
    return $open;
}

function scan_http_services($host, $open_ports) {
    $http_ports  = [80, 8080, 8081, 3000, 3001, 3002, 5000, 5001];
    $https_ports = [443];
    $web_ports   = [];
    foreach ($open_ports as $port => $info) {
        if (in_array($port, $https_ports)) {
            $url  = $port === 443 ? "https://{$host}" : "https://{$host}:{$port}";
            $meta = fetch_http_meta($url);
            if ($meta) $web_ports[] = $meta;
        } elseif (in_array($port, $http_ports)) {
            $url  = $port === 80  ? "http://{$host}"  : "http://{$host}:{$port}";
            $meta = fetch_http_meta($url);
            if ($meta) $web_ports[] = $meta;
        }
    }
    return $web_ports;
}

function scan_git_repos($host, $open_ports) {
    $repos   = [];
    $schemes = [];
    if (isset($open_ports[443])) $schemes[] = 'https';
    if (isset($open_ports[80]))  $schemes[] = 'http';
    if (empty($schemes))         $schemes[] = 'http';
    $git_paths = ['', '/repo', '/git', '/project', '/src', '/code'];
    foreach ($schemes as $scheme) {
        $base = "{$scheme}://{$host}";
        foreach ($git_paths as $path) {
            $head_url = "{$base}{$path}/.git/HEAD";
            $meta = fetch_http_meta($head_url, 2);
            if (!$meta || $meta['code'] !== 200) continue;
            $ctx = stream_context_create(['http' => ['timeout' => 2, 'user_agent' => 'BlackLab-Dashboard/1.0', 'ignore_errors' => true], 'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
            $head_content = @file_get_contents($head_url, false, $ctx);
            $branch = 'inconnue';
            if ($head_content && preg_match('#ref: refs/heads/(.+)#', $head_content, $m)) $branch = trim($m[1]);
            $commit = '';
            $log = @file_get_contents("{$base}{$path}/.git/FETCH_HEAD", false, $ctx);
            if ($log && preg_match('/^([a-f0-9]{7,40})/m', $log, $m)) $commit = substr($m[1], 0, 7);
            $repos[] = ['url' => "{$base}{$path}", 'path' => $path ?: '/', 'branch' => $branch, 'commit' => $commit, 'scheme' => $scheme];
        }
    }
    return $repos;
}

function scan_git_servers($host, $open_ports, $http_services) {
    $servers = [];
    foreach ($http_services as $svc) {
        $title_lower  = strtolower($svc['title'] ?? '');
        $server_lower = strtolower($svc['server'] ?? '');
        $type = null;
        if (strpos($title_lower, 'gitea')  !== false || strpos($server_lower, 'gitea')  !== false) $type = 'Gitea';
        elseif (strpos($title_lower, 'gitlab') !== false || strpos($server_lower, 'gitlab') !== false) $type = 'GitLab';
        elseif (strpos($title_lower, 'gogs')   !== false) $type = 'Gogs';
        elseif (strpos($title_lower, 'git')    !== false) $type = 'Git (inconnu)';
        if ($type) $servers[] = ['url' => rtrim($svc['url'], '/'), 'type' => $type, 'title' => $svc['title']];
    }
    return $servers;
}

/* ══════════════════════════════════════════════════════════════
   EXÉCUTION DU SCAN (si authentifié)
══════════════════════════════════════════════════════════════ */

$scan_results = null; $scan_host = ''; $scan_error = '';

if ($nd_auth && $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['host'])) {
    $clean_host = sanitize_host($_POST['host'] ?? '');
    if (!$clean_host) {
        $scan_error = 'Adresse invalide. Saisissez une IP ou un nom de serveur valide.';
    } else {
        $scan_host     = $clean_host;
        $t_start       = microtime(true);
        $open_ports    = scan_ports($scan_host);
        $http_services = scan_http_services($scan_host, $open_ports);
        $git_repos     = scan_git_repos($scan_host, $open_ports);
        $git_servers   = scan_git_servers($scan_host, $open_ports, $http_services);
        $scan_results  = [
            'host' => $scan_host, 'open_ports' => $open_ports,
            'http' => $http_services, 'git_repos' => $git_repos,
            'git_servers' => $git_servers, 'time' => round(microtime(true) - $t_start, 2),
        ];
    }
}

/* ── Helpers affichage ── */
function http_badge($code) {
    if ($code >= 200 && $code < 300) return "<span class='b b-ok'>{$code}</span>";
    if ($code >= 300 && $code < 400) return "<span class='b b-warn'>{$code}</span>";
    return "<span class='b b-err'>{$code}</span>";
}
function svg_icon($n) {
    $i = [
        'globe'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
        'lock'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
        'terminal' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="4 17 10 11 4 5"/><line x1="12" y1="19" x2="20" y2="19"/></svg>',
        'code'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
        'database' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>',
        'git'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><path d="M6 21V9a9 9 0 0 0 9 9"/></svg>',
        'branch'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="6" y1="3" x2="6" y2="15"/><circle cx="18" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M18 9a9 9 0 0 1-9 9"/></svg>',
        'server'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>',
        'link'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>',
        'scan'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2"/><line x1="7" y1="12" x2="17" y2="12"/></svg>',
        'clock'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
        'chevron'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>',
        'key'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="7.5" cy="15.5" r="5.5"/><path d="M21 2l-9.6 9.6M15.5 7.5l3 3"/></svg>',
        'eye-off'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>',
        'php'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="12" rx="10" ry="7"/></svg>',
        'logout'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
    ];
    return $i[$n] ?? $i['globe'];
}

/* ══════════════════════════════════════════════════════════════
   DONNÉES DÉMO (statiques, fictives)
══════════════════════════════════════════════════════════════ */
$demo_data = [
    'host' => '192.168.1.50',
    'time' => '1.24',
    'open_ports' => [
        22   => ['label' => 'SSH',      'cat' => 'system', 'icon' => 'terminal'],
        80   => ['label' => 'HTTP',     'cat' => 'web',    'icon' => 'globe'],
        443  => ['label' => 'HTTPS',    'cat' => 'web',    'icon' => 'lock'],
        3306 => ['label' => 'MySQL',    'cat' => 'db',     'icon' => 'database'],
        6379 => ['label' => 'Redis',    'cat' => 'db',     'icon' => 'database'],
        8080 => ['label' => 'HTTP-Alt', 'cat' => 'web',    'icon' => 'globe'],
    ],
    'http' => [
        ['url' => 'https://192.168.1.50', 'code' => 200, 'title' => 'Mon Projet — Dashboard', 'server' => 'nginx/1.25.3', 'mime' => 'text/html', 'size' => 18432],
        ['url' => 'http://192.168.1.50:8080', 'code' => 302, 'title' => '', 'server' => 'Apache/2.4.57', 'mime' => 'text/html', 'size' => 512],
    ],
    'git_repos' => [
        ['url' => 'http://192.168.1.50/project', 'path' => '/project', 'branch' => 'main', 'commit' => 'a3f9c12', 'scheme' => 'http'],
    ],
    'git_servers' => [],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Network Dashboard — Black-Lab Toolbox</title>
<style>
/* ── Variables ── */
:root {
    --bg:#0d0d0f; --bg-2:#141418;
    --surface:rgba(255,255,255,.04); --surface-h:rgba(255,255,255,.08);
    --border:rgba(255,255,255,.08);
    --accent:#ff1654; --accent-soft:rgba(255,22,84,.18);
    --gradient:linear-gradient(135deg,#ff1654,#5e006c);
    --text:#e8e8f0; --text-muted:#7a7a90; --text-dim:#3a3a50;
    --green:#27c93f; --yellow:#ffbd2e; --red:#ff5f56;
    --radius:12px; --radius-sm:8px; --transition:.18s ease;
    --c-web:#ef4444; --c-dev:#3b82f6; --c-db:#eab308; --c-system:#a855f7; --c-git:#27c93f;
    --font-display:'Bebas Neue',system-ui,sans-serif;
    --font-mono:'DM Mono','Fira Code',monospace;
    --font-body:'DM Sans','Inter',system-ui,sans-serif;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{font-family:var(--font-body);font-size:14px;background:var(--bg);color:var(--text);-webkit-font-smoothing:antialiased;height:100%;overflow-y:auto}

/* ── Layout ── */
.page-wrap { position:relative; z-index:1; display:flex; flex-direction:column; min-height:100%; }
.page-body { flex:1; max-width:1080px; margin:0 auto; padding:1.4rem; width:100%; }

/* ── .hdr (défini aussi dans tools-shared.css) ── */
/* .hdr : défini dans tools-shared.css */

/* ── Sas mot de passe ── */
.gate-overlay {
    position:fixed; inset:0;
    background:rgba(0,0,0,.85);
    backdrop-filter:blur(16px);
    z-index:100;
    display:flex; align-items:center; justify-content:center;
    padding:1.5rem;
}
.gate-box {
    background:#111116;
    border:1px solid var(--border);
    border-top:2px solid var(--accent);
    border-radius:var(--radius);
    padding:2rem 2.2rem;
    width:100%; max-width:380px;
    box-shadow:0 30px 60px rgba(0,0,0,.6), 0 0 40px rgba(255,22,84,.08);
}
.gate-icon { font-size:2rem; text-align:center; margin-bottom:.5rem; }
.gate-title {
    font-family:var(--font-display);
    font-size:1.6rem; letter-spacing:.04em;
    text-align:center;
    background:var(--gradient);
    -webkit-background-clip:text; -webkit-text-fill-color:transparent;
    margin-bottom:.35rem;
}
.gate-sub { font-size:.78rem; color:var(--text-muted); text-align:center; margin-bottom:1.5rem; }
.gate-input {
    width:100%; padding:.6rem 1rem;
    background:var(--bg-2); border:1px solid var(--border);
    border-radius:var(--radius-sm); color:var(--text);
    font-family:var(--font-mono); font-size:.9rem;
    outline:none; transition:border-color var(--transition), box-shadow var(--transition);
    margin-bottom:.75rem; letter-spacing:.08em;
}
.gate-input:focus { border-color:var(--accent); box-shadow:0 0 0 3px var(--accent-soft); }
.gate-btn {
    width:100%; padding:.6rem;
    background:var(--gradient); border:none;
    border-radius:var(--radius-sm); color:#fff;
    font-family:var(--font-display); font-size:1.1rem;
    letter-spacing:.06em; cursor:pointer;
    transition:opacity var(--transition);
}
.gate-btn:hover { opacity:.88; }
.gate-error {
    background:rgba(255,22,84,.1); border:1px solid rgba(255,22,84,.3);
    border-radius:var(--radius-sm); padding:.5rem .8rem;
    color:#fca5a5; font-size:.78rem; margin-bottom:.75rem;
}
.gate-cancel {
    display:block; text-align:center; margin-top:.9rem;
    font-size:.75rem; color:var(--text-muted); cursor:pointer;
    transition:color var(--transition);
}
.gate-cancel:hover { color:var(--text); }

/* ── Bannière démo ── */
.demo-banner {
    background:rgba(255,189,46,.08);
    border:1px solid rgba(255,189,46,.25);
    border-left:3px solid var(--yellow);
    border-radius:var(--radius-sm);
    padding:.7rem 1rem;
    margin-bottom:1.1rem;
    display:flex; align-items:center; gap:.8rem; flex-wrap:wrap;
}
.demo-banner__text { flex:1; font-size:.8rem; color:var(--text-muted); }
.demo-banner__text strong { color:var(--yellow); }
.demo-banner__btn {
    display:inline-flex; align-items:center; gap:.4rem;
    padding:.42rem 1rem;
    background:var(--gradient); border:none; border-radius:var(--radius-sm);
    color:#fff; font-family:var(--font-display);
    font-size:.95rem; letter-spacing:.05em;
    cursor:pointer; white-space:nowrap;
    transition:opacity var(--transition);
}
.demo-banner__btn:hover { opacity:.88; }
.demo-banner__btn svg { width:14px; height:14px; }

/* ── Watermark démo ── */
.demo-watermark {
    position:fixed; bottom:1rem; right:1rem;
    font-size:.65rem; color:var(--text-dim);
    background:var(--surface); border:1px solid var(--border);
    border-radius:20px; padding:.25rem .7rem;
    pointer-events:none; z-index:50;
    font-family:var(--font-mono);
}

/* ── Erreur / Form ── */
.err-box{background:rgba(255,87,86,.1);border:1px solid rgba(255,87,86,.3);border-radius:var(--radius);padding:.85rem 1.1rem;color:#fca5a5;margin-bottom:1rem;font-size:.83rem}
.form-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1.1rem 1.3rem;margin-bottom:1.1rem}
.form-card__title{font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--accent);padding-bottom:.4rem;border-bottom:1px solid var(--border);margin-bottom:.75rem}
.form-row{display:flex;gap:.5rem;align-items:center}
.scan-input{flex:1;padding:.5rem .88rem;background:var(--bg-2);border:1px solid var(--border);border-radius:var(--radius-sm);color:var(--text);font-family:var(--font-mono);font-size:.88rem;outline:none;transition:border-color var(--transition),box-shadow var(--transition)}
.scan-input:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft)}
.scan-input::placeholder{color:var(--text-dim)}
.btn-scan{display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1.2rem;background:var(--gradient);border:none;border-radius:var(--radius-sm);color:#fff;font-size:.83rem;font-weight:700;cursor:pointer;white-space:nowrap;transition:opacity var(--transition)}
.btn-scan:hover{opacity:.88}
.btn-scan:disabled{opacity:.4;cursor:not-allowed}
.btn-scan svg{width:15px;height:15px}
.scan-hint{font-size:.72rem;color:var(--text-dim);margin-top:.55rem}
.scan-hint code{background:var(--surface-h);padding:.1rem .4rem;border-radius:4px;font-family:var(--font-mono);font-size:.7rem}

/* ── Loader ── */
.loader{display:none;text-align:center;padding:2rem;color:var(--text-muted)}
.dots{display:inline-flex;gap:7px}
.d{width:9px;height:9px;border-radius:50%;background:var(--accent);animation:dp 1.4s ease-in-out infinite}
.d:nth-child(2){animation-delay:.2s;background:#7c3aed}
.d:nth-child(3){animation-delay:.4s;background:#27c93f}
@keyframes dp{0%,80%,100%{transform:scale(.55);opacity:.35}40%{transform:scale(1);opacity:1}}

/* ── Summary ── */
.summary{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:.8rem 1.1rem;margin-bottom:.9rem;display:flex;flex-wrap:wrap;gap:.7rem;align-items:center}
.summary__host{font-family:var(--font-mono);font-size:.95rem;font-weight:700;display:flex;align-items:center;gap:.4rem}
.summary__host svg{width:16px;height:16px;color:var(--green)}
.chips{display:flex;flex-wrap:wrap;gap:.4rem;margin-left:auto}
.chip{display:inline-flex;align-items:center;gap:.3rem;padding:.22rem .7rem;border-radius:20px;font-size:.72rem;font-weight:600;border:1px solid}
.chip svg{width:12px;height:12px}
.chip-p{border-color:rgba(168,85,247,.4);color:#c4b5fd}
.chip-h{border-color:rgba(239,68,68,.4);color:#fca5a5}
.chip-g{border-color:rgba(39,201,63,.4);color:#86efac}
.chip-t{border-color:rgba(160,160,160,.2);color:var(--text-muted)}

/* ── Sections ── */
.sec{margin-bottom:.7rem;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
.sec-hdr{display:flex;align-items:center;gap:.6rem;padding:.7rem .95rem;cursor:pointer;background:var(--surface);user-select:none;transition:background var(--transition)}
.sec-hdr:hover{background:var(--surface-h)}
.sec-icon{width:17px;height:17px;flex-shrink:0}
.sec-title{font-size:.83rem;font-weight:600;flex:1}
.sec-count{background:var(--accent-soft);color:var(--accent);font-size:.68rem;font-weight:700;padding:.08rem .48rem;border-radius:20px}
.sec-chev{width:15px;height:15px;color:var(--text-muted);transition:transform var(--transition)}
.sec.collapsed .sec-chev{transform:rotate(-90deg)}
.sec-body{padding:.75rem .95rem;background:var(--bg);border-top:1px solid var(--border)}
.sec.collapsed .sec-body{display:none}
.empty{color:var(--text-dim);font-size:.8rem;padding:.3rem 0}

/* ── Card grid ── */
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:.55rem}
.port-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);padding:.6rem .85rem;display:flex;align-items:center;gap:.65rem;transition:border-color var(--transition)}
.port-card:hover{border-color:var(--accent)}
.port-icon{width:18px;height:18px;flex-shrink:0}
.port-name{font-size:.8rem;font-weight:600}
.port-num{font-size:.7rem;color:var(--text-muted);font-family:var(--font-mono)}
.http-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);padding:.75rem .9rem;display:flex;flex-direction:column;gap:.45rem;transition:border-color var(--transition)}
.http-card:hover{border-color:var(--accent)}
.http-card-top{display:flex;gap:.5rem;align-items:flex-start}
.http-card-top svg{width:16px;height:16px;flex-shrink:0;margin-top:2px}
.http-title{font-size:.8rem;font-weight:600}
.http-url{font-size:.7rem;color:var(--text-muted);font-family:var(--font-mono);word-break:break-all}
.http-meta{display:flex;flex-wrap:wrap;gap:.3rem;align-items:center}
.b{font-size:.66rem;padding:.1rem .45rem;border-radius:20px;font-weight:700}
.b-ok{background:rgba(39,201,63,.12);color:#27c93f;border:1px solid rgba(39,201,63,.3)}
.b-warn{background:rgba(255,189,46,.12);color:#ffbd2e;border:1px solid rgba(255,189,46,.3)}
.b-err{background:rgba(255,95,86,.12);color:#ff5f56;border:1px solid rgba(255,95,86,.3)}
.pill{font-size:.68rem;color:var(--text-muted);background:var(--surface);padding:.08rem .4rem;border-radius:4px;border:1px solid var(--border)}
.ext-link{font-size:.7rem;color:var(--accent);display:inline-flex;align-items:center;gap:.2rem;margin-top:.1rem}
.ext-link svg{width:11px;height:11px}
.git-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);padding:.75rem .9rem;display:flex;flex-direction:column;gap:.4rem;transition:border-color var(--transition)}
.git-card:hover{border-color:var(--green)}
.git-top{display:flex;align-items:center;gap:.45rem}
.git-top svg{width:15px;height:15px;color:var(--green)}
.git-path{font-family:var(--font-mono);font-size:.8rem;font-weight:600}
.git-meta{display:flex;align-items:center;gap:.55rem}
.git-branch{display:flex;align-items:center;gap:.25rem;font-size:.73rem;color:var(--text-muted)}
.git-branch svg{width:12px;height:12px}
.git-commit{font-family:var(--font-mono);font-size:.7rem;color:var(--text-dim);padding:.08rem .38rem;background:var(--surface);border-radius:4px;border:1px solid var(--border)}
.gsrv-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);padding:.75rem .9rem;display:flex;flex-direction:column;gap:.38rem;transition:border-color var(--transition)}
.gsrv-card:hover{border-color:rgba(168,85,247,.5)}
.gsrv-type{display:inline-block;font-size:.66rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#c4b5fd;padding:.08rem .45rem;background:rgba(168,85,247,.1);border:1px solid rgba(168,85,247,.3);border-radius:20px}
.gsrv-title{font-size:.8rem;color:var(--text)}

/* ── Auth banner (si connecté) ── */
.auth-badge {
    display:inline-flex; align-items:center; gap:.4rem;
    padding:.22rem .7rem; border-radius:20px;
    font-size:.7rem; color:var(--green);
    border:1px solid rgba(39,201,63,.3);
    background:rgba(39,201,63,.06);
    margin-left:auto;
}
.auth-badge svg { width:12px; height:12px; }
.btn-logout {
    display:inline-flex; align-items:center; gap:.3rem;
    padding:.22rem .65rem; border-radius:20px;
    font-size:.7rem; color:var(--text-muted);
    border:1px solid var(--border); background:transparent;
    cursor:pointer; transition:all var(--transition);
    font-family:inherit;
}
.btn-logout:hover { color:var(--red); border-color:rgba(255,95,86,.4); }
.btn-logout svg { width:12px; height:12px; }
</style>
</head>
<body>

<!-- Ambient circles -->
<div class="ambient">
  <div class="ambient__circle"></div>
  <div class="ambient__circle"></div>
</div>

<?php if ($show_login && !$nd_auth): ?>
<!-- ══ SAS MOT DE PASSE ══════════════════════════════════════ -->
<div class="gate-overlay" id="gate">
  <div class="gate-box">
    <div class="gate-icon">🔐</div>
    <div class="gate-title">ACCÈS RESTREINT</div>
    <div class="gate-sub">Outil réservé aux administrateurs Black-Lab</div>
    <?php if ($nd_error): ?>
    <div class="gate-error">⚠ <?= htmlspecialchars($nd_error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <input type="password" name="nd_password" class="gate-input"
             placeholder="Mot de passe…" autocomplete="current-password" autofocus>
      <button type="submit" class="gate-btn"><?= svg_icon('key') ?> DÉVERROUILLER</button>
    </form>
    <a class="gate-cancel" onclick="history.back()">← Retour à la démo</a>
  </div>
</div>
<?php endif; ?>

<div class="page-wrap">

<!-- ══ HEADER ════════════════════════════════════════════════ -->
<header class="hdr">
  <span class="hdr__icon">⚡</span>
  <span class="hdr__title">NETWORK DASHBOARD</span>
  <span class="hdr__sep"></span>
  <span class="hdr__meta">Scanner de services — ports, HTTP, dépôts Git</span>
  <?php if ($nd_auth): ?>
  <span class="auth-badge"><?= svg_icon('lock') ?> Accès admin</span>
  <a href="?logout=1" class="btn-logout"><?= svg_icon('logout') ?> Déconnexion</a>
  <?php endif; ?>
</header>

<div class="page-body">

<?php if (!$nd_auth): ?>
<!-- ══ MODE DÉMO ═════════════════════════════════════════════ -->

<div class="demo-banner">
  <div class="demo-banner__text">
    <strong>⚠ Mode démonstration</strong> — Les données ci-dessous sont fictives.
    L'outil de scan réel est protégé par mot de passe.
  </div>
  <button class="demo-banner__btn" onclick="window.location='?login=1'">
    <?= svg_icon('key') ?> Accéder à l'outil
  </button>
</div>

<!-- Résultats démo (statiques) -->
<div class="summary">
  <div class="summary__host"><?= svg_icon('server') ?> <?= $demo_data['host'] ?> <span style="font-size:.7rem;color:var(--text-muted);font-weight:400">(démo)</span></div>
  <div class="chips">
    <span class="chip chip-p"><?= svg_icon('terminal') ?> <?= count($demo_data['open_ports']) ?> ports</span>
    <span class="chip chip-h"><?= svg_icon('globe') ?> <?= count($demo_data['http']) ?> services</span>
    <span class="chip chip-g"><?= svg_icon('git') ?> <?= count($demo_data['git_repos']) ?> repos</span>
    <span class="chip chip-t"><?= svg_icon('clock') ?> <?= $demo_data['time'] ?>s</span>
  </div>
</div>

<!-- Ports démo -->
<div class="sec" id="sec-ports">
  <div class="sec-hdr" onclick="toggleSec('sec-ports')">
    <span class="sec-icon" style="color:var(--c-system)"><?= svg_icon('terminal') ?></span>
    <span class="sec-title">Ports ouverts</span>
    <span class="sec-count"><?= count($demo_data['open_ports']) ?></span>
    <span class="sec-chev"><?= svg_icon('chevron') ?></span>
  </div>
  <div class="sec-body">
    <div class="grid">
      <?php foreach ($demo_data['open_ports'] as $port => $info): ?>
      <div class="port-card">
        <span class="port-icon" style="color:var(--c-<?= $info['cat'] ?>)"><?= svg_icon($info['icon']) ?></span>
        <div>
          <div class="port-name"><?= $info['label'] ?></div>
          <div class="port-num">:<?= $port ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- HTTP démo -->
<div class="sec" id="sec-http">
  <div class="sec-hdr" onclick="toggleSec('sec-http')">
    <span class="sec-icon" style="color:var(--c-web)"><?= svg_icon('globe') ?></span>
    <span class="sec-title">Services HTTP / HTTPS</span>
    <span class="sec-count"><?= count($demo_data['http']) ?></span>
    <span class="sec-chev"><?= svg_icon('chevron') ?></span>
  </div>
  <div class="sec-body">
    <div class="grid">
      <?php foreach ($demo_data['http'] as $svc): ?>
      <div class="http-card">
        <div class="http-card-top">
          <span style="color:var(--c-web)"><?= svg_icon(strpos($svc['url'],'https')===0 ? 'lock' : 'globe') ?></span>
          <div>
            <div class="http-title"><?= htmlspecialchars($svc['title'] ?: 'Sans titre') ?></div>
            <div class="http-url"><?= htmlspecialchars($svc['url']) ?></div>
          </div>
        </div>
        <div class="http-meta">
          <?= http_badge($svc['code']) ?>
          <span class="pill"><?= htmlspecialchars($svc['server']) ?></span>
          <span class="pill"><?= number_format($svc['size']/1024,1) ?> Ko</span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Git démo -->
<div class="sec" id="sec-git">
  <div class="sec-hdr" onclick="toggleSec('sec-git')">
    <span class="sec-icon" style="color:var(--c-git)"><?= svg_icon('git') ?></span>
    <span class="sec-title">Dépôts Git détectés</span>
    <span class="sec-count"><?= count($demo_data['git_repos']) ?></span>
    <span class="sec-chev"><?= svg_icon('chevron') ?></span>
  </div>
  <div class="sec-body">
    <div class="grid">
      <?php foreach ($demo_data['git_repos'] as $repo): ?>
      <div class="git-card">
        <div class="git-top"><?= svg_icon('git') ?> <span class="git-path"><?= htmlspecialchars($repo['path']) ?></span></div>
        <div class="git-meta">
          <div class="git-branch"><?= svg_icon('branch') ?> <?= htmlspecialchars($repo['branch']) ?></div>
          <span class="git-commit"><?= htmlspecialchars($repo['commit']) ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<div class="demo-watermark">⚠ Données fictives — démo</div>

<?php else: ?>
<!-- ══ OUTIL RÉEL (authentifié) ═════════════════════════════ -->

<?php if ($scan_error): ?>
<div class="err-box"><?= htmlspecialchars($scan_error) ?></div>
<?php endif; ?>

<div class="form-card">
  <div class="form-card__title">Scanner un serveur</div>
  <form method="POST" id="scan-form">
    <div class="form-row">
      <input type="text" name="host" id="host-input" class="scan-input"
             placeholder="192.168.1.50 · myserver.local · 10.0.0.1"
             value="<?= htmlspecialchars($_POST['host'] ?? '') ?>"
             autocomplete="off" spellcheck="false">
      <button type="submit" class="btn-scan" id="btn-scan">
        <?= svg_icon('scan') ?> Scanner
      </button>
    </div>
    <div class="scan-hint">Ex : <code>localhost</code> · <code>192.168.1.1</code> · <code>mon-serveur.local</code></div>
  </form>
</div>

<div class="loader" id="loader">
  <div class="dots"><span class="d"></span><span class="d"></span><span class="d"></span></div>
  <p style="margin-top:.9rem;font-size:.8rem">Scan en cours…</p>
</div>

<?php if ($scan_results): ?>

<div class="summary">
  <div class="summary__host"><?= svg_icon('server') ?> <?= htmlspecialchars($scan_results['host']) ?></div>
  <div class="chips">
    <span class="chip chip-p"><?= svg_icon('terminal') ?> <?= count($scan_results['open_ports']) ?> ports</span>
    <span class="chip chip-h"><?= svg_icon('globe') ?> <?= count($scan_results['http']) ?> services</span>
    <span class="chip chip-g"><?= svg_icon('git') ?> <?= count($scan_results['git_repos']) ?> repos</span>
    <span class="chip chip-t"><?= svg_icon('clock') ?> <?= $scan_results['time'] ?>s</span>
  </div>
</div>

<div class="sec" id="sec-ports">
  <div class="sec-hdr" onclick="toggleSec('sec-ports')">
    <span class="sec-icon" style="color:var(--c-system)"><?= svg_icon('terminal') ?></span>
    <span class="sec-title">Ports ouverts</span>
    <span class="sec-count"><?= count($scan_results['open_ports']) ?></span>
    <span class="sec-chev"><?= svg_icon('chevron') ?></span>
  </div>
  <div class="sec-body">
    <?php if (empty($scan_results['open_ports'])): ?>
      <div class="empty">Aucun port ouvert détecté.</div>
    <?php else: ?>
    <div class="grid">
      <?php foreach ($scan_results['open_ports'] as $port => $info): ?>
      <div class="port-card">
        <span class="port-icon" style="color:var(--c-<?= $info['cat'] ?>)"><?= svg_icon($info['icon']) ?></span>
        <div>
          <div class="port-name"><?= htmlspecialchars($info['label']) ?></div>
          <div class="port-num">:<?= $port ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<div class="sec" id="sec-http">
  <div class="sec-hdr" onclick="toggleSec('sec-http')">
    <span class="sec-icon" style="color:var(--c-web)"><?= svg_icon('globe') ?></span>
    <span class="sec-title">Services HTTP / HTTPS</span>
    <span class="sec-count"><?= count($scan_results['http']) ?></span>
    <span class="sec-chev"><?= svg_icon('chevron') ?></span>
  </div>
  <div class="sec-body">
    <?php if (empty($scan_results['http'])): ?>
      <div class="empty">Aucun service HTTP détecté.</div>
    <?php else: ?>
    <div class="grid">
      <?php foreach ($scan_results['http'] as $svc): ?>
      <div class="http-card">
        <div class="http-card-top">
          <span style="color:var(--c-web)"><?= svg_icon(strpos($svc['url'],'https')===0 ? 'lock' : 'globe') ?></span>
          <div>
            <div class="http-title"><?= htmlspecialchars($svc['title'] ?: 'Sans titre') ?></div>
            <div class="http-url"><?= htmlspecialchars($svc['url']) ?></div>
          </div>
        </div>
        <div class="http-meta">
          <?= http_badge($svc['code']) ?>
          <?php if ($svc['server']): ?><span class="pill"><?= htmlspecialchars($svc['server']) ?></span><?php endif; ?>
          <?php if ($svc['mime']): ?><span class="pill"><?= htmlspecialchars($svc['mime']) ?></span><?php endif; ?>
          <?php if ($svc['size']): ?><span class="pill"><?= number_format($svc['size']/1024,1) ?> Ko</span><?php endif; ?>
        </div>
        <a href="<?= htmlspecialchars($svc['url']) ?>" target="_blank" rel="noopener" class="ext-link"><?= svg_icon('link') ?> Ouvrir</a>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php if (!empty($scan_results['git_servers'])): ?>
<div class="sec" id="sec-gitsrv">
  <div class="sec-hdr" onclick="toggleSec('sec-gitsrv')">
    <span class="sec-icon" style="color:#a855f7"><?= svg_icon('server') ?></span>
    <span class="sec-title">Serveurs Git (Gitea / GitLab / Gogs)</span>
    <span class="sec-count"><?= count($scan_results['git_servers']) ?></span>
    <span class="sec-chev"><?= svg_icon('chevron') ?></span>
  </div>
  <div class="sec-body">
    <div class="grid">
      <?php foreach ($scan_results['git_servers'] as $srv): ?>
      <div class="gsrv-card">
        <span class="gsrv-type"><?= htmlspecialchars($srv['type']) ?></span>
        <div class="gsrv-title"><?= htmlspecialchars($srv['title'] ?: $srv['url']) ?></div>
        <a href="<?= htmlspecialchars($srv['url']) ?>" target="_blank" rel="noopener" class="ext-link"><?= svg_icon('link') ?> <?= htmlspecialchars($srv['url']) ?></a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<div class="sec" id="sec-git">
  <div class="sec-hdr" onclick="toggleSec('sec-git')">
    <span class="sec-icon" style="color:var(--c-git)"><?= svg_icon('git') ?></span>
    <span class="sec-title">Dépôts Git détectés</span>
    <span class="sec-count"><?= count($scan_results['git_repos']) ?></span>
    <span class="sec-chev"><?= svg_icon('chevron') ?></span>
  </div>
  <div class="sec-body">
    <?php if (empty($scan_results['git_repos'])): ?>
      <div class="empty">Aucun dépôt Git exposé détecté.</div>
    <?php else: ?>
    <div class="grid">
      <?php foreach ($scan_results['git_repos'] as $repo): ?>
      <div class="git-card">
        <div class="git-top"><?= svg_icon('git') ?> <span class="git-path"><?= htmlspecialchars($repo['path']) ?></span></div>
        <div class="git-meta">
          <div class="git-branch"><?= svg_icon('branch') ?> <?= htmlspecialchars($repo['branch']) ?></div>
          <?php if ($repo['commit']): ?><span class="git-commit"><?= htmlspecialchars($repo['commit']) ?></span><?php endif; ?>
        </div>
        <a href="<?= htmlspecialchars($repo['url']) ?>" target="_blank" rel="noopener" class="ext-link"><?= svg_icon('link') ?> <?= htmlspecialchars($repo['url']) ?></a>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php endif; // scan_results ?>
<?php endif; // nd_auth ?>

</div><!-- /page-body -->
</div><!-- /page-wrap -->

<script>
(function () {
    function toggleSec(id) {
        var s = document.getElementById(id);
        if (s) s.classList.toggle('collapsed');
    }
    window.toggleSec = toggleSec;

    var form   = document.getElementById('scan-form');
    var loader = document.getElementById('loader');
    var btn    = document.getElementById('btn-scan');
    var input  = document.getElementById('host-input');

    if (form) {
        form.addEventListener('submit', function (e) {
            if (!input || !input.value.trim()) { e.preventDefault(); if (input) input.focus(); return; }
            if (loader) loader.style.display = 'block';
            if (btn) { btn.disabled = true; btn.textContent = 'Scan\u2026'; }
        });
    }
    if (input && !input.value) input.focus();

    /* Historique session (outil réel uniquement) */
    <?php if ($nd_auth): ?>
    var HK = 'nd_hist';
    function getH() { try { return JSON.parse(sessionStorage.getItem(HK) || '[]'); } catch(e) { return []; } }
    function addH(v) { if (!v) return; var a = getH().filter(function(x){return x!==v;}); a.unshift(v); if (a.length > 8) a = a.slice(0,8); try { sessionStorage.setItem(HK, JSON.stringify(a)); } catch(e) {} }
    function renderH() {
        var h = getH(); if (!h.length || !input) return;
        var w = document.createElement('div');
        w.style.cssText = 'display:flex;flex-wrap:wrap;gap:5px;margin-top:8px;';
        h.forEach(function (host) {
            var c = document.createElement('button');
            c.type = 'button'; c.textContent = host;
            c.style.cssText = 'padding:3px 10px;background:var(--surface);border:1px solid var(--border);border-radius:20px;color:var(--text-muted);font-size:11px;font-family:monospace;cursor:pointer;transition:all .15s;';
            c.onmouseover = function(){ this.style.borderColor='var(--accent)'; this.style.color='var(--text)'; };
            c.onmouseout  = function(){ this.style.borderColor='var(--border)'; this.style.color='var(--text-muted)'; };
            c.addEventListener('click', function () { input.value = host; form && form.requestSubmit(); });
            w.appendChild(c);
        });
        var hint = document.querySelector('.scan-hint');
        if (hint) hint.after(w);
    }
    var scanned = input && input.value.trim();
    if (scanned) addH(scanned);
    renderH();
    <?php endif; ?>
})();
</script>
</body>
</html>