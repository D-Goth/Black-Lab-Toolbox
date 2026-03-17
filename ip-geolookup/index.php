<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ip Geolookup — Black-Lab Toolbox</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/UAParser.js/0.7.28/ua-parser.min.js"></script>
<style>
:root {
    --bg:          #0d0d0f;
    --bg-2:        #141418;
    --surface:     rgba(255,255,255,0.04);
    --surface-h:   rgba(255,255,255,0.08);
    --border:      rgba(255,255,255,0.08);
    --accent:      #ff1654;
    --accent-soft: rgba(255,22,84,0.18);
    --gradient:    linear-gradient(135deg,#ff1654,#5e006c);
    --text:        #e8e8f0;
    --text-muted:  #7a7a90;
    --text-dim:    #3a3a50;
    --radius:      12px;
    --radius-sm:   8px;
    --transition:  0.18s ease;
}
*,*::before,*::after { box-sizing:border-box; margin:0; padding:0; }
html, body {
    height: 100%;
    font-family: 'Inter', system-ui, sans-serif;
    font-size: 14px;
    background: var(--bg);
    color: var(--text);
    -webkit-font-smoothing: antialiased;
    overflow: hidden;
}

/* ── Layout ── */
.layout {
    display: grid;
    grid-template-rows: auto auto 1fr;
    height: 100vh;
    overflow: hidden;
}

/* .hdr : défini dans tools-shared.css */

/* ── Search bar ── */
.search-bar {
    padding: 0.6rem 1rem;
    border-bottom: 1px solid var(--border);
    background: var(--bg-2);
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex-shrink: 0;
}
.ip-input {
    flex: 1;
    padding: 0.48rem 0.85rem;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text);
    font-family: 'Fira Code', 'Cascadia Code', monospace;
    font-size: 0.85rem;
    outline: none;
    transition: border-color var(--transition), box-shadow var(--transition);
}
.ip-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px var(--accent-soft);
}
.ip-input::placeholder { color: var(--text-dim); }
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.45rem 0.9rem;
    border-radius: var(--radius-sm);
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    border: 1px solid var(--border);
    background: var(--surface-h);
    color: var(--text-muted);
    white-space: nowrap;
    transition: all var(--transition);
}
.btn:hover { color: var(--text); border-color: var(--accent); background: var(--surface-h); }
.btn--primary { background: var(--gradient); border-color: transparent; color: #fff; }
.btn--primary:hover { opacity: 0.88; border-color: transparent; }

/* ── Main content ── */
.content {
    display: grid;
    grid-template-columns: 290px 1fr;
    overflow: hidden;
    min-height: 0;
}

/* ── Left panel ── */
.panel-left {
    border-right: 1px solid var(--border);
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 0.55rem;
    padding: 0.75rem;
}
.card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 0.8rem 0.95rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.card__label {
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--accent);
    padding-bottom: 0.35rem;
    border-bottom: 1px solid var(--border);
}
.ip-display {
    font-family: 'Fira Code', monospace;
    font-size: 0.92rem;
    font-weight: 700;
    color: var(--accent);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}
.badge {
    font-size: 0.6rem;
    padding: 0.1rem 0.45rem;
    border-radius: 20px;
    font-weight: 700;
    border: 1px solid;
    white-space: nowrap;
}
.badge--ok   { background: rgba(39,201,63,.1);  border-color: #27c93f; color: #27c93f; }
.badge--warn { background: rgba(255,189,46,.1); border-color: #ffbd2e; color: #ffbd2e; }

.info-row {
    display: grid;
    grid-template-columns: 72px 1fr;
    gap: 0.2rem 0.5rem;
    font-size: 0.77rem;
}
.info-row dt { color: var(--text-muted); padding: 0.2rem 0; }
.info-row dd { color: var(--text); font-weight: 500; padding: 0.2rem 0; border-bottom: 1px solid rgba(255,255,255,0.04); }
.info-row dd:last-child { border: none; }

.placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    gap: 0.6rem;
    color: var(--text-dim);
    font-size: 0.82rem;
    text-align: center;
    padding: 2rem;
}
.placeholder__icon { font-size: 2.5rem; opacity: 0.2; }

/* ── Right : map ── */
.panel-right {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    min-height: 0;
}
.map-topbar {
    padding: 0.4rem 0.9rem;
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--text-muted);
    background: var(--bg-2);
    border-bottom: 1px solid var(--border);
    flex-shrink: 0;
}
#map { flex: 1; min-height: 0; width: 100%; }

/* Leaflet theme dark */
.leaflet-popup-content-wrapper {
    background: #111116;
    color: var(--text);
    border: 1px solid var(--accent);
    border-radius: var(--radius-sm);
    box-shadow: 0 4px 24px rgba(0,0,0,0.7);
}
.leaflet-popup-tip { background: #111116; }
.leaflet-popup-content { font-family: 'Inter', sans-serif; font-size: 12px; line-height: 1.55; }

/* ── Toast ── */
.toast {
    position: fixed;
    bottom: 1rem;
    right: 1rem;
    background: #111116;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 0.55rem 0.95rem;
    font-size: 0.8rem;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 0.4rem;
    transform: translateY(140%);
    transition: transform 0.25s ease;
    z-index: 9999;
    box-shadow: 0 8px 24px rgba(0,0,0,0.5);
    max-width: 260px;
}
.toast.show { transform: translateY(0); }
.toast.success { border-color: #27c93f; }
.toast.error   { border-color: var(--accent); }


@media (max-width: 680px) {
    .content { grid-template-columns: 1fr; grid-template-rows: auto 280px; }
    .panel-left { border-right: none; border-bottom: 1px solid var(--border); }
}
</style>
</head>
<body>

<!-- Ambient circles -->
<div class="ambient">
  <div class="ambient__circle"></div>
  <div class="ambient__circle"></div>
</div>

<div class="layout">

    <header class="hdr">
        <span class="hdr__icon">📍</span>
        <span class="hdr__title">IP TRACER</span>
        <span class="hdr__sep"></span>
        <span class="hdr__meta">Géolocalisation — ASN, organisation, User-Agent, cartographie</span>
    </header>

    <div class="search-bar">
        <input class="ip-input" id="ip-input" type="text"
               placeholder="8.8.8.8 ou 2001:4860:4860::8888"
               autocomplete="off" spellcheck="false">
        <button class="btn" id="btn-myip">&#x1F5A5; Mon IP</button>
        <button class="btn btn--primary" id="btn-analyze">&#x1F50D; Analyser</button>
    </div>

    <div class="content">
        <div class="panel-left" id="panel-left">
            <div class="placeholder" id="placeholder">
                <div class="placeholder__icon">&#x1F30D;</div>
                <div>Entrez une IP pour commencer</div>
            </div>
        </div>
        <div class="panel-right">
            <div class="map-topbar">Carte</div>
            <div id="map"></div>
        </div>
    </div>
</div>

<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
(function () {
    'use strict';

    var TOKEN = '9a5763c01630ec';
    var map = null;
    var toastTimer;

    /* ── Toast ── */
    function toast(msg, type) {
        var t = document.getElementById('toast');
        document.getElementById('toast-msg').textContent = msg;
        t.className = 'toast ' + (type || 'info') + ' show';
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () { t.classList.remove('show'); }, 3000);
    }

    /* ── Flag emoji ── */
    function flag(code) {
        if (!code || code.length !== 2) return '&#x1F310;';
        var a = code.toUpperCase().charCodeAt(0) - 65 + 0x1F1E6;
        var b = code.toUpperCase().charCodeAt(1) - 65 + 0x1F1E6;
        return String.fromCodePoint(a) + String.fromCodePoint(b);
    }

    /* ── Init Leaflet ── */
    function initMap() {
        if (map) { map.remove(); map = null; }
        map = L.map('map', { zoomControl: true }).setView([20, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);
        [150, 500, 1000].forEach(function (d) {
            setTimeout(function () { if (map) map.invalidateSize(); }, d);
        });
    }

    /* ── Marker ── */
    function placeMarker(lat, lon, data) {
        map.setView([lat, lon], 11);
        var icon = L.divIcon({
            className: '',
            html: '<div style="background:#ff1654;width:14px;height:14px;border-radius:50%;border:2px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.6);"></div>',
            iconSize: [14, 14],
            iconAnchor: [7, 7]
        });
        L.marker([lat, lon], { icon: icon })
            .addTo(map)
            .bindPopup(
                '<span style="color:#ff1654;font-weight:700">' + (data.city || '') + ', ' + (data.country || '') + '</span><br>' +
                data.ip + '<br>' +
                '<span style="color:#7a7a90">' + (data.org || '') + '</span>'
            )
            .openPopup();
        [150, 500, 1000].forEach(function (d) {
            setTimeout(function () { if (map) map.invalidateSize(); }, d);
        });
    }

    /* ── Render left panel ── */
    function renderLeft(data) {
        var parser = new UAParser();
        var ua = parser.getResult();
        var isPrivate = /^(10\.|192\.168\.|172\.(1[6-9]|2\d|3[01])\.|127\.|::1$)/.test(data.ip || '');
        var parts = (data.org || '').split(' ');
        var asn = parts[0] || 'N/A';
        var org = parts.slice(1).join(' ') || 'N/A';

        document.getElementById('panel-left').innerHTML =
            '<div class="card">' +
                '<div class="card__label">Adresse IP</div>' +
                '<div class="ip-display">' + flag(data.country) + '&nbsp;' + (data.ip || '—') +
                    '<span class="badge ' + (isPrivate ? 'badge--warn' : 'badge--ok') + '">' +
                        (isPrivate ? '&#x26A0; Priv&eacute;e' : '&#x2713; Clean') +
                    '</span>' +
                '</div>' +
                '<dl class="info-row">' +
                    '<dt>Pays</dt><dd>' + (data.country || 'N/A') + '</dd>' +
                    '<dt>R&eacute;gion</dt><dd>' + (data.region || 'N/A') + '</dd>' +
                    '<dt>Ville</dt><dd>' + (data.city || 'N/A') + '</dd>' +
                    '<dt>Coords</dt><dd>' + (data.loc || 'N/A') + '</dd>' +
                    '<dt>ASN</dt><dd>' + asn + '</dd>' +
                    '<dt>Org</dt><dd>' + org + '</dd>' +
                    '<dt>Fuseau</dt><dd>' + (data.timezone || 'N/A') + '</dd>' +
                    '<dt>Hostname</dt><dd>' + (data.hostname || 'N/A') + '</dd>' +
                '</dl>' +
            '</div>' +
            '<div class="card">' +
                '<div class="card__label">Votre navigateur</div>' +
                '<dl class="info-row">' +
                    '<dt>Navigateur</dt><dd>' + (ua.browser.name || 'N/A') + ' ' + (ua.browser.version || '') + '</dd>' +
                    '<dt>OS</dt><dd>' + (ua.os.name || 'N/A') + ' ' + (ua.os.version || '') + '</dd>' +
                    '<dt>Device</dt><dd>' + (ua.device.type || 'desktop') + '</dd>' +
                '</dl>' +
            '</div>';
    }

    /* ── Lookup ── */
    function lookup(ip) {
        if (!ip) { toast('Entrez une adresse IP', 'error'); return; }
        toast('Analyse en cours\u2026', 'info');
        fetch('https://ipinfo.io/' + encodeURIComponent(ip) + '/json?token=' + TOKEN)
            .then(function (r) { if (!r.ok) throw new Error('Erreur r\u00e9seau'); return r.json(); })
            .then(function (data) {
                if (data.error) { toast(data.error.message || 'IP invalide', 'error'); return; }
                renderLeft(data);
                if (data.loc) {
                    var coords = data.loc.split(',');
                    placeMarker(parseFloat(coords[0]), parseFloat(coords[1]), data);
                }
                toast('Analyse termin\u00e9e !', 'success');
            })
            .catch(function (e) { toast(e.message, 'error'); });
    }

    /* ── Events ── */
    document.getElementById('btn-analyze').addEventListener('click', function () {
        lookup(document.getElementById('ip-input').value.trim());
    });
    document.getElementById('ip-input').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') lookup(this.value.trim());
    });
    document.getElementById('btn-myip').addEventListener('click', function () {
        fetch('https://api.ipify.org?format=json')
            .then(function (r) { return r.json(); })
            .then(function (d) { document.getElementById('ip-input').value = d.ip; lookup(d.ip); })
            .catch(function () { toast('Impossible de r\u00e9cup\u00e9rer votre IP', 'error'); });
    });
    window.addEventListener('resize', function () { if (map) map.invalidateSize(); });

    /* ── Init ── */
    initMap();
    fetch('https://api.ipify.org?format=json')
        .then(function (r) { return r.json(); })
        .then(function (d) { document.getElementById('ip-input').value = d.ip; lookup(d.ip); })
        .catch(function () { toast('Entrez une IP pour commencer', 'info'); });
})();
</script>
</body>
</html>