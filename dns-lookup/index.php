<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>DNS Lookup — Black-Lab Toolbox</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
/* ── Full-height layout ─────────────────────────────────────── */
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
  overflow: hidden;
}

/* ── Search bar ─────────────────────────────────────────────── */
.search-bar {
  flex-shrink: 0;
  display: flex; gap: .6rem; align-items: center; flex-wrap: wrap;
}
.search-bar input { flex: 1; min-width: 200px; }

/* ── Type chips ─────────────────────────────────────────────── */
.type-chips {
  flex-shrink: 0;
  display: flex; flex-wrap: wrap; gap: .3rem;
}
.tc {
  padding: .2rem .6rem; border-radius: 20px; font-size: .73rem;
  cursor: pointer; background: var(--surface-h);
  border: 1px solid var(--border); color: var(--text-muted);
  font-family: var(--font-mono); transition: all var(--transition);
}
.tc.active { background: var(--accent-soft); border-color: var(--accent); color: var(--accent); }

/* ── Main content : gauche records | droite carte ───────────── */
.content {
  flex: 1; min-height: 0;
  display: grid;
  grid-template-columns: 340px 1fr;
  gap: .8rem;
  overflow: hidden;
}
@media(max-width:760px) {
  .content { grid-template-columns: 1fr; grid-template-rows: 1fr 280px; }
}

/* ── Panneau gauche : records ───────────────────────────────── */
.panel-left {
  min-height: 0;
  display: flex; flex-direction: column;
  border: 1px solid var(--border); border-radius: var(--radius);
  overflow: hidden; background: var(--surface-h);
}
.panel-title {
  flex-shrink: 0;
  padding: .45rem .9rem;
  font-size: .68rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: 1px;
  color: var(--text-muted);
  background: rgba(10,10,18,.5);
  border-bottom: 1px solid var(--border);
  display: flex; align-items: center; justify-content: space-between;
}
.records-scroll {
  flex: 1; min-height: 0;
  overflow-y: auto;
  padding: .4rem .6rem;
}
.dns-record {
  display: flex; gap: .6rem;
  padding: .5rem .3rem;
  border-bottom: 1px solid var(--border);
  font-size: .8rem; align-items: flex-start;
}
.dns-record:last-child { border: none; }
.dns-type {
  flex-shrink: 0;
  min-width: 46px; padding: .18rem .4rem;
  background: var(--accent-soft); color: var(--accent);
  border-radius: 4px; font-size: .68rem; font-weight: 700;
  text-align: center; font-family: var(--font-mono);
}
.dns-val {
  color: var(--text); flex: 1;
  word-break: break-all;
  font-family: var(--font-mono); font-size: .76rem; line-height: 1.5;
}
.dns-ttl {
  flex-shrink: 0;
  font-size: .68rem; color: var(--text-dim);
  font-family: var(--font-mono); white-space: nowrap;
  padding-top: .15rem;
}
.placeholder {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  height: 100%; gap: .6rem;
  color: var(--text-dim); font-size: .82rem; text-align: center; padding: 2rem;
}
.placeholder-icon { font-size: 2.2rem; opacity: .2; }

/* ── Panneau droit : carte + geo ────────────────────────────── */
.panel-right {
  min-height: 0;
  display: flex; flex-direction: column;
  gap: .6rem; overflow: hidden;
}
.map-card {
  flex: 1; min-height: 0;
  display: flex; flex-direction: column;
  border: 1px solid var(--border); border-radius: var(--radius);
  overflow: hidden;
}
.map-topbar {
  flex-shrink: 0;
  padding: .45rem .9rem;
  font-size: .68rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: 1px;
  color: var(--text-muted);
  background: rgba(10,10,18,.5);
  border-bottom: 1px solid var(--border);
  display: flex; align-items: center; gap: .5rem;
}
#map {
  flex: 1; min-height: 0;
  width: 100%;
}

/* Leaflet dark overrides */
.leaflet-tile-pane { filter: brightness(.75) saturate(.6); }
.leaflet-popup-content-wrapper {
  background: #111116; color: #e8e8f0;
  border: 1px solid rgba(255,22,84,.3);
  border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,.6);
}
.leaflet-popup-tip { background: #111116; }

/* ── Geo info bar ───────────────────────────────────────────── */
.geo-bar {
  flex-shrink: 0;
  display: grid; grid-template-columns: repeat(3, 1fr);
  gap: .4rem;
}
@media(max-width:500px) { .geo-bar { grid-template-columns: 1fr 1fr; } }
.geo-item {
  background: var(--surface-h); border: 1px solid var(--border);
  border-radius: var(--radius-sm); padding: .4rem .6rem;
}
.geo-label { font-size: .63rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; }
.geo-val   { font-size: .8rem; color: var(--text); font-weight: 600; margin-top: .1rem; font-family: var(--font-mono); }

/* ── Loading / error ────────────────────────────────────────── */
#loading { display:none; text-align:center; color:var(--text-muted); padding:2rem; }
#error-box { display:none; }
</style>
</head>
<body>

<div class="ambient">
  <div class="ambient__circle"></div>
  <div class="ambient__circle"></div>
</div>

<div class="page-wrap">
<header class="hdr">
  <span class="hdr__icon">🌐</span>
  <span class="hdr__title">DNS LOOKUP</span>
  <span class="hdr__sep"></span>
  <span class="hdr__meta">Résolution DNS complète avec géolocalisation IP interactive</span>
</header>

<div class="page-body">

  <!-- Search -->
  <div class="search-bar">
    <input type="text" id="domain" placeholder="domaine.com ou adresse IP" style="flex:1">
    <button class="btn btn-primary" onclick="doLookup()">🔍 Rechercher</button>
  </div>

  <!-- Type chips -->
  <div class="type-chips" id="type-chips">
    <?php foreach(['ALL','A','AAAA','MX','TXT','CNAME','NS','SOA'] as $t): ?>
    <button class="tc <?= $t==='ALL'?'active':'' ?>" onclick="toggleType(this,'<?= $t ?>')"><?= $t ?></button>
    <?php endforeach; ?>
  </div>

  <!-- Contenu principal -->
  <div id="loading">⏳ Résolution en cours…</div>
  <div id="error-box" class="card">
    <div style="color:var(--red);font-size:.85rem" id="error-msg"></div>
  </div>

  <div class="content" id="main-content">

    <!-- Gauche : records -->
    <div class="panel-left">
      <div class="panel-title">
        <span>Enregistrements DNS</span>
        <span id="record-count" style="font-weight:400;opacity:.6"></span>
      </div>
      <div class="records-scroll" id="records-list">
        <div class="placeholder">
          <div class="placeholder-icon">🌐</div>
          Entrez un domaine et lancez la recherche
        </div>
      </div>
    </div>

    <!-- Droite : carte + geo -->
    <div class="panel-right">
      <div class="map-card">
        <div class="map-topbar">
          <span>🗺️ Géolocalisation</span>
          <span id="map-ip" style="color:var(--accent);font-family:var(--font-mono);font-size:.75rem"></span>
        </div>
        <div id="map"></div>
      </div>
      <div class="geo-bar" id="geo-bar"></div>
    </div>

  </div>

</div><!-- /page-body -->
</div><!-- /page-wrap -->
<div class="toast-area" id="ta"></div>

<script>
let selectedTypes = new Set(['ALL']);
let map = null, marker = null;

// ── Type chips ───────────────────────────────────────────────
function toggleType(btn, type) {
  if (type === 'ALL') {
    selectedTypes = new Set(['ALL']);
    document.querySelectorAll('.tc').forEach(b => b.classList.toggle('active', b.textContent === 'ALL'));
  } else {
    selectedTypes.delete('ALL');
    document.querySelector('.tc').classList.remove('active');
    if (selectedTypes.has(type)) { selectedTypes.delete(type); btn.classList.remove('active'); }
    else { selectedTypes.add(type); btn.classList.add('active'); }
    if (selectedTypes.size === 0) { selectedTypes.add('ALL'); document.querySelector('.tc').classList.add('active'); }
  }
}
function getTypes() {
  return selectedTypes.has('ALL') ? ['A','AAAA','MX','TXT','CNAME','NS'] : [...selectedTypes];
}

// ── Lookup ───────────────────────────────────────────────────
async function doLookup() {
  const d = document.getElementById('domain').value.trim();
  if (!d) return;

  document.getElementById('error-box').style.display = 'none';
  document.getElementById('loading').style.display = 'block';
  document.getElementById('records-list').innerHTML = '<div class="placeholder"><div class="placeholder-icon">⏳</div>Résolution…</div>';
  document.getElementById('record-count').textContent = '';
  document.getElementById('geo-bar').innerHTML = '';
  document.getElementById('map-ip').textContent = '';

  try {
    const types = getTypes();
    const allRecords = [];

    await Promise.all(types.map(async t => {
      const r = await fetch(`https://dns.google/resolve?name=${encodeURIComponent(d)}&type=${t}`);
      const j = await r.json();
      if (j.Answer) allRecords.push(...j.Answer.map(a => ({ ...a, typeStr: t })));
    }));

    // Tri par type
    const typeOrder = { A:1, AAAA:2, MX:3, CNAME:4, NS:5, TXT:6, SOA:7 };
    allRecords.sort((a, b) => (typeOrder[a.typeStr] || 9) - (typeOrder[b.typeStr] || 9));

    showRecords(allRecords);

    // Géoloc sur la première IP A
    const aRec = allRecords.find(r => r.typeStr === 'A');
    if (aRec) await geolocate(aRec.data);

    document.getElementById('loading').style.display = 'none';

  } catch(e) {
    document.getElementById('loading').style.display = 'none';
    document.getElementById('error-box').style.display = 'block';
    document.getElementById('error-msg').textContent = 'Erreur : ' + e.message;
  }
}

// ── Records ──────────────────────────────────────────────────
function showRecords(records) {
  const list = document.getElementById('records-list');
  document.getElementById('record-count').textContent = records.length + ' entrée' + (records.length > 1 ? 's' : '');

  if (!records.length) {
    list.innerHTML = '<div class="placeholder"><div class="placeholder-icon">🔍</div>Aucun enregistrement trouvé.</div>';
    return;
  }
  list.innerHTML = records.map(r => `
    <div class="dns-record">
      <span class="dns-type">${r.typeStr}</span>
      <span class="dns-val">${escHtml(r.data)}</span>
      <span class="dns-ttl">TTL&nbsp;${r.TTL}s</span>
    </div>`).join('');
}

function escHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// ── Géolocalisation ──────────────────────────────────────────
async function geolocate(ip) {
  try {
    const r = await fetch(`https://ipapi.co/${ip}/json/`);
    const d = await r.json();
    const lat = d.latitude, lng = d.longitude;

    document.getElementById('map-ip').textContent = ip;

    // Init ou réutilisation de la carte
    if (!map) {
      map = L.map('map', { zoomControl: true });
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
      }).addTo(map);
    }

    // ⚠️ CRUCIAL : recalculer la taille après affichage
    setTimeout(() => {
      map.invalidateSize();
      if (lat && lng) {
        map.setView([lat, lng], 10);
        if (marker) marker.remove();
        marker = L.circleMarker([lat, lng], {
          radius: 10, color: '#ff1654',
          fillColor: '#ff1654', fillOpacity: .85,
          weight: 2
        }).addTo(map);
        marker.bindPopup(`<strong>${d.city || '—'}</strong><br>${d.country_name || '—'}`).openPopup();
      }
    }, 100);

    // Geo bar
    const fields = [
      ['Pays',    `${d.country_name || '—'} (${d.country_code || '—'})`],
      ['Région',  d.region || '—'],
      ['Ville',   d.city || '—'],
      ['ASN',     d.asn  || '—'],
      ['Org',     d.org  || '—'],
      ['Timezone',d.timezone || '—'],
    ];
    document.getElementById('geo-bar').innerHTML = fields.map(([k,v]) => `
      <div class="geo-item">
        <div class="geo-label">${k}</div>
        <div class="geo-val">${escHtml(v)}</div>
      </div>`).join('');

  } catch(e) {
    document.getElementById('map-ip').textContent = ip + ' (géoloc indisponible)';
  }
}

// ── Entrée clavier ───────────────────────────────────────────
document.getElementById('domain').addEventListener('keydown', e => e.key === 'Enter' && doLookup());

// ── Init carte vide ──────────────────────────────────────────
window.addEventListener('load', () => {
  map = L.map('map', { zoomControl: true });
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
  }).addTo(map);
  map.setView([20, 0], 2);
  setTimeout(() => map.invalidateSize(), 200);
});
</script>
</body>
</html>