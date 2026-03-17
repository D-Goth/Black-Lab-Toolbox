<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Weather Dashboard — Black-Lab Toolbox</title>
<style>
/* ── Layout ── */
.w-grid{display:grid;grid-template-columns:280px 1fr;gap:1rem;align-items:stretch}
@media(max-width:720px){.w-grid{grid-template-columns:1fr}}

/* ── Température principale ── */
.big-temp{font-size:3.5rem;font-weight:800;background:var(--gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent;line-height:1}
.weather-icon{font-size:3rem;line-height:1;font-family:"Apple Color Emoji","Segoe UI Emoji","Noto Color Emoji",sans-serif}

/* ── Lignes de stat ── */
.stat-row{display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--border);font-size:.83rem}
.stat-row:last-child{border:none}
.stat-row span:first-child{color:var(--text-muted)}
.stat-row span:last-child{color:var(--text);font-weight:500}

/* ── Prévisions 7j ── */
.forecast-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:.5rem}
@media(max-width:600px){.forecast-grid{grid-template-columns:repeat(4,1fr)}}
.forecast-day{background:var(--bg-2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:.5rem;text-align:center}
.forecast-day .fd-day{font-size:.7rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em}
.forecast-day .fd-icon{font-size:1.3rem;margin:.2rem 0;font-family:"Apple Color Emoji","Segoe UI Emoji","Noto Color Emoji",sans-serif}
.forecast-day .fd-hi{font-size:.85rem;font-weight:600;color:var(--text)}
.forecast-day .fd-lo{font-size:.75rem;color:var(--text-muted)}

/* ── Horaires ── */
.hourly-list{display:flex;gap:.5rem;overflow-x:auto;padding-bottom:.5rem}
.hourly-item{flex-shrink:0;text-align:center;background:var(--bg-2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:.5rem .6rem}
.hourly-item .hh{font-size:.72rem;color:var(--text-muted)}
.hourly-item .hi{font-size:1.1rem;margin:.15rem 0;font-family:"Apple Color Emoji","Segoe UI Emoji","Noto Color Emoji",sans-serif}
.hourly-item .ht{font-size:.82rem;font-weight:600}
.hourly-item .hp{font-size:.65rem;color:var(--text-muted)}

/* ── Search ── */
.search-row{display:flex;gap:.5rem;margin-bottom:.8rem}

/* ── Clock ── */
.clock{font-size:2rem;font-weight:700;font-variant-numeric:tabular-nums;color:var(--text)}
.clock-date{font-size:.82rem;color:var(--text-muted)}

/* ══════════════════════════════════════════
   SECTION ENVIRONNEMENTALE (données Pro)
══════════════════════════════════════════ */
.env-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
  gap:.65rem;
  margin-bottom:1rem;
}
.env-card{
  background:var(--bg-2);
  border:1px solid var(--border);
  border-radius:var(--radius-sm);
  padding:.75rem .9rem;
  display:flex;
  align-items:center;
  gap:.65rem;
  position:relative;
  overflow:hidden;
  transition:border-color .18s;
}
.env-card::before{
  content:'';
  position:absolute;
  top:0;left:0;right:0;
  height:2px;
  background:var(--gradient);
  opacity:.6;
}
.env-card:hover{border-color:rgba(255,22,84,.35)}
.env-icon{font-size:1.6rem;line-height:1;flex-shrink:0}
.env-label{font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-bottom:.15rem}
.env-value{font-size:.9rem;font-weight:700;color:var(--text)}
.env-sub{font-size:.72rem;color:var(--text-muted)}

/* Badge de niveau (couleur dynamique via style inline) */
.level-badge{
  display:inline-block;
  padding:.1rem .45rem;
  border-radius:10px;
  font-size:.65rem;
  font-weight:700;
  margin-left:.3rem;
}

/* Carte vigilance (alerte) */
.env-card.alert{
  border-color:rgba(255,95,86,.5);
}
.env-card.alert::before{
  background:linear-gradient(90deg,#ff5f56,#ff0000);
}

/* Barre AQI colorée */
.aqi-bar{
  height:4px;
  border-radius:4px;
  margin-top:.3rem;
  transition:width .4s;
}

/* Loader inline pour les données env */
.env-loading{
  font-size:.72rem;
  color:var(--text-dim);
  font-style:italic;
}
</style>
<style>
html, body { height: 100%; overflow-y: auto; }
body { display: flex; flex-direction: column; }
.page-wrap { position: relative; z-index: 1; flex: 1; display: flex; flex-direction: column; }
.page-body {
  flex: 1;
  padding: 1rem 1.4rem 1.5rem;
  display: flex;
  flex-direction: column;
  gap: .85rem;
  max-width: 1300px;
  width: 100%;
  margin: 0 auto;
}
/* weather-content occupe tout */
#weather-content { display: flex !important; flex-direction: column; gap: .85rem; }
/* Supprimer les margins inline via surcharge */
#weather-content > .card,
#weather-content > .w-grid { margin-bottom: 0 !important; }
/* Card météo principale : flex pour aligner icône + temp */
.card-main-weather { display: flex; flex-direction: column; justify-content: space-between; }
/* Forecast grid responsive renforcé */
@media(min-width:900px){
  .forecast-grid{ grid-template-columns:repeat(7,1fr) !important; }
}
@media(max-width:600px){
  .w-grid{ grid-template-columns:1fr !important; }
  .env-grid{ grid-template-columns:1fr 1fr !important; }
}
</style>
</head>
<body>

<!-- Ambient circles -->
<div class="ambient">
  <div class="ambient__circle"></div>
  <div class="ambient__circle"></div>
</div>

<div class="page-wrap">
<header class="hdr">
  <span class="hdr__icon">🌤️</span>
  <span class="hdr__title">WEATHER DASHBOARD</span>
  <span class="hdr__sep"></span>
  <span class="hdr__meta">Météo · Qualité de l'air · Pollens · UV · Open-Meteo · Sans clé API</span>
</header>
<div class="page-body">

  <div class="search-row">
    <input type="text" id="city-input" placeholder="Ville ou coordonnées…" style="flex:1">
    <button class="btn btn-ghost btn-sm" onclick="useGPS()">📡 Ma position</button>
    <button class="btn btn-primary" onclick="searchCity()">🔍 Rechercher</button>
  </div>

  <div id="loading" style="display:none;text-align:center;color:var(--text-muted);padding:2rem">⏳ Chargement météo…</div>
  <div id="error-box" class="card" style="display:none;color:var(--red);font-size:.85rem"></div>

  <div id="weather-content" style="display:none">

    <!-- ── Grille principale : carte météo + détails ── -->
    <div class="w-grid">
      <div class="card">
        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem">
          <div class="weather-icon" id="w-icon">☀️</div>
          <div>
            <div class="big-temp" id="w-temp">—</div>
            <div style="font-size:.82rem;color:var(--text-muted)" id="w-feels">Ressenti —</div>
          </div>
        </div>
        <div style="font-weight:600;margin-bottom:.3rem" id="w-city">—</div>
        <div style="font-size:.8rem;color:var(--text-muted);margin-bottom:.8rem" id="w-desc">—</div>
        <div class="clock" id="clock">—</div>
        <div class="clock-date" id="clock-date">—</div>
      </div>
      <div class="card">
        <div class="card-title">Détails météo</div>
        <div id="w-details"></div>
      </div>
    </div>

    <!-- ══ DONNÉES ENVIRONNEMENTALES (Pro) ══ -->
    <div class="card">
      <div class="card-title">🌍 Données environnementales</div>
      <div class="env-grid" id="env-grid">
        <!-- UV -->
        <div class="env-card" id="env-uv">
          <div class="env-icon">☀️</div>
          <div>
            <div class="env-label">Indice UV</div>
            <div class="env-value" id="env-uv-val"><span class="env-loading">⏳</span></div>
            <div class="env-sub" id="env-uv-sub">—</div>
          </div>
        </div>
        <!-- Qualité de l'air -->
        <div class="env-card" id="env-aqi">
          <div class="env-icon">💨</div>
          <div style="flex:1">
            <div class="env-label">Qualité de l'air</div>
            <div class="env-value" id="env-aqi-val"><span class="env-loading">⏳</span></div>
            <div class="env-sub" id="env-aqi-sub">—</div>
            <div class="aqi-bar" id="env-aqi-bar" style="width:0%;background:#00e400"></div>
          </div>
        </div>
        <!-- Pollens -->
        <div class="env-card" id="env-pollen">
          <div class="env-icon">🌳</div>
          <div>
            <div class="env-label">Pollens</div>
            <div class="env-value" id="env-pollen-val"><span class="env-loading">⏳</span></div>
            <div class="env-sub" id="env-pollen-sub">—</div>
          </div>
        </div>
        <!-- Rafales -->
        <div class="env-card" id="env-gusts">
          <div class="env-icon">🌬️</div>
          <div>
            <div class="env-label">Rafales max</div>
            <div class="env-value" id="env-gusts-val"><span class="env-loading">⏳</span></div>
            <div class="env-sub" id="env-gusts-sub">—</div>
          </div>
        </div>
        <!-- Vigilance (France) — affichée uniquement si alerte -->
        <div class="env-card alert" id="env-vigilance" style="display:none">
          <div class="env-icon">⚠️</div>
          <div>
            <div class="env-label">Vigilance Météo</div>
            <div class="env-value" id="env-vig-val">—</div>
            <div class="env-sub" id="env-vig-sub">—</div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Prévisions 7j ── -->
    <div class="card">
      <div class="card-title">Prévisions 7 jours</div>
      <div class="forecast-grid" id="forecast-grid"></div>
    </div>

    <!-- ── Prévisions horaires ── -->
    <div class="card">
      <div class="card-title">Prévisions horaires (24h)</div>
      <div class="hourly-list" id="hourly-list"></div>
    </div>

  </div><!-- /#weather-content -->
</div><!-- /page-body -->
</div><!-- /page-wrap -->
<div class="toast-area" id="ta"></div>

<script>
/* ══════════════════════════════════════════
   CLOCK
══════════════════════════════════════════ */
let clockInterval = null;
function startClock() {
  clearInterval(clockInterval);
  clockInterval = setInterval(() => {
    const n = new Date();
    document.getElementById('clock').textContent      = n.toLocaleTimeString('fr-FR',{hour:'2-digit',minute:'2-digit'});
    document.getElementById('clock-date').textContent = n.toLocaleDateString('fr-FR',{weekday:'long',day:'numeric',month:'long'});
  }, 1000);
}
startClock();

/* ══════════════════════════════════════════
   CODES WMO
══════════════════════════════════════════ */
const WMO = {0:'☀️',1:'🌤️',2:'⛅',3:'☁️',45:'🌫️',48:'🌫️',51:'🌦️',53:'🌦️',55:'🌧️',61:'🌧️',63:'🌧️',65:'🌧️',71:'🌨️',73:'🌨️',75:'❄️',80:'🌦️',81:'🌧️',82:'⛈️',95:'⛈️',96:'⛈️',99:'⛈️'};
const WMO_DESC = {0:'Ciel dégagé',1:'Peu nuageux',2:'Partiellement nuageux',3:'Couvert',45:'Brouillard',48:'Brouillard givrant',51:'Bruine légère',53:'Bruine modérée',55:'Bruine dense',61:'Pluie légère',63:'Pluie modérée',65:'Pluie forte',71:'Neige légère',73:'Neige modérée',75:'Neige forte',80:'Averses',81:'Averses fortes',82:'Averses violentes',95:'Orage',96:'Orage grêle',99:'Orage grêle fort'};

/* ══════════════════════════════════════════
   DIRECTION DU VENT (16 points)
══════════════════════════════════════════ */
function windDir16(deg) {
  const dirs = ['N','NNE','NE','ENE','E','ESE','SE','SSE','S','SSO','SO','OSO','O','ONO','NO','NNO'];
  return dirs[Math.round(deg / 22.5) % 16];
}

/* ══════════════════════════════════════════
   UV — niveau de risque
══════════════════════════════════════════ */
function uvLevel(uv) {
  if (uv <= 2)  return { label:'Faible',    color:'#27c93f' };
  if (uv <= 5)  return { label:'Modéré',    color:'#ffbd2e' };
  if (uv <= 7)  return { label:'Élevé',     color:'#ff7e00' };
  if (uv <= 10) return { label:'Très élevé',color:'#ff3333' };
  return              { label:'Extrême',    color:'#8f3f97' };
}

/* ══════════════════════════════════════════
   POLLENS — niveaux et noms
══════════════════════════════════════════ */
const POLLEN_NAMES = {
  alder_pollen:'Aulne', birch_pollen:'Bouleau', grass_pollen:'Herbes',
  mugwort_pollen:'Armoise', olive_pollen:'Olivier', ragweed_pollen:'Ambroisie'
};
function pollenRisk(total) {
  if (total <= 10)  return { level:'Aucun',      color:'#7a7a90' };
  if (total <= 30)  return { level:'Faible',      color:'#27c93f' };
  if (total <= 50)  return { level:'Modéré',      color:'#ffbd2e' };
  if (total <= 100) return { level:'Élevé',       color:'#ff7e00' };
  return                   { level:'Très élevé',  color:'#ff3333' };
}

/* ══════════════════════════════════════════
   AQI — couleur et niveau
══════════════════════════════════════════ */
function aqiInfo(aqi) {
  if (aqi <= 50)  return { level:'Bon',                       color:'#00e400' };
  if (aqi <= 100) return { level:'Modéré',                    color:'#ffff00' };
  if (aqi <= 150) return { level:'Mauvais (groupes sensibles)',color:'#ff7e00' };
  if (aqi <= 200) return { level:'Mauvais',                   color:'#ff0000' };
  if (aqi <= 300) return { level:'Très mauvais',              color:'#8f3f97' };
  return                 { level:'Dangereux',                  color:'#7e0023' };
}

/* ══════════════════════════════════════════
   RECHERCHE / GPS
══════════════════════════════════════════ */
async function searchCity() {
  const city = document.getElementById('city-input').value.trim();
  if (!city) return;
  show('loading');
  try {
    const geo = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(city)}&format=json&limit=1&accept-language=fr`).then(r => r.json());
    if (!geo.length) throw new Error('Ville non trouvée');
    await fetchAll(+geo[0].lat, +geo[0].lon, geo[0].display_name.split(',')[0]);
  } catch(e) { showError(e.message); }
}

async function useGPS() {
  if (!navigator.geolocation) { toast('Géolocalisation non disponible','err'); return; }
  show('loading');
  navigator.geolocation.getCurrentPosition(async pos => {
    try {
      const { latitude:lat, longitude:lon } = pos.coords;
      const geo = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json&accept-language=fr`).then(r => r.json());
      const city = geo.address?.city || geo.address?.town || geo.address?.village || 'Position GPS';
      await fetchAll(lat, lon, city);
    } catch(e) { showError(e.message); }
  }, () => showError('Accès GPS refusé'));
}

/* ══════════════════════════════════════════
   FETCH PRINCIPAL : météo + données env en parallèle
══════════════════════════════════════════ */
async function fetchAll(lat, lon, cityName) {
  try {
    show('loading');

    // Lancer toutes les requêtes en parallèle
    const [weatherData, airData] = await Promise.all([
      fetchWeatherAPI(lat, lon),
      fetchAirQualityAPI(lat, lon)
    ]);

    renderWeather(weatherData, cityName);
    renderEnvData(weatherData, airData);
    show('weather');

  } catch(e) { showError('Erreur API : ' + e.message); }
}

async function fetchWeatherAPI(lat, lon) {
  const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}`
    + `&current=temperature_2m,relative_humidity_2m,apparent_temperature,weather_code`
    + `,wind_speed_10m,wind_direction_10m,wind_gusts_10m,surface_pressure,uv_index`
    + `&hourly=temperature_2m,weather_code,precipitation_probability`
    + `&daily=weather_code,temperature_2m_max,temperature_2m_min,precipitation_probability_max,wind_speed_10m_max,wind_gusts_10m_max`
    + `&wind_speed_unit=kmh&timezone=auto&forecast_days=7`;
  return fetch(url).then(r => r.json());
}

async function fetchAirQualityAPI(lat, lon) {
  // Open-Meteo Air Quality — inclut AQI européen + pollens
  const url = `https://air-quality-api.open-meteo.com/v1/air-quality?latitude=${lat}&longitude=${lon}`
    + `&current=european_aqi,european_aqi_pm2_5,european_aqi_no2,european_aqi_o3`
    + `&hourly=alder_pollen,birch_pollen,grass_pollen,mugwort_pollen,olive_pollen,ragweed_pollen`
    + `&timezone=auto`;
  try {
    return await fetch(url).then(r => r.json());
  } catch { return null; }
}

/* ══════════════════════════════════════════
   RENDU MÉTÉO
══════════════════════════════════════════ */
function renderWeather(d, city) {
  const c    = d.current;
  const code = c.weather_code;

  document.getElementById('w-icon').textContent  = WMO[code] || '🌡️';
  document.getElementById('w-temp').textContent  = Math.round(c.temperature_2m) + '°';
  document.getElementById('w-feels').textContent = 'Ressenti ' + Math.round(c.apparent_temperature) + '°C';
  document.getElementById('w-city').textContent  = city;
  document.getElementById('w-desc').textContent  = WMO_DESC[code] || '';

  // Détails météo enrichis (16 directions + rafales)
  const dir = windDir16(c.wind_direction_10m);
  document.getElementById('w-details').innerHTML = [
    ['Humidité',   c.relative_humidity_2m + '%'],
    ['Vent',       Math.round(c.wind_speed_10m) + ' km/h ' + dir],
    ['Rafales',    Math.round(c.wind_gusts_10m) + ' km/h'],
    ['Pression',   Math.round(c.surface_pressure) + ' hPa'],
    ['Indice UV',  c.uv_index],
  ].map(([k,v]) => `<div class="stat-row"><span>${k}</span><span>${v}</span></div>`).join('');

  // Prévisions 7j
  const fg   = document.getElementById('forecast-grid');
  fg.innerHTML = '';
  const days = ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'];
  d.daily.time.forEach((t, i) => {
    const day = new Date(t);
    const precip = d.daily.precipitation_probability_max[i];
    fg.innerHTML += `<div class="forecast-day">
      <div class="fd-day">${i===0?'Auj.':days[day.getDay()]}</div>
      <div class="fd-icon">${WMO[d.daily.weather_code[i]]||'🌡️'}</div>
      <div class="fd-hi">${Math.round(d.daily.temperature_2m_max[i])}°</div>
      <div class="fd-lo">${Math.round(d.daily.temperature_2m_min[i])}°</div>
      ${precip > 0 ? `<div style="font-size:.62rem;color:var(--text-muted);margin-top:.1rem">💧${precip}%</div>` : ''}
    </div>`;
  });

  // Prévisions horaires (24h)
  const hl  = document.getElementById('hourly-list');
  hl.innerHTML = '';
  const now = new Date();
  d.hourly.time.slice(0, 48).forEach((t, i) => {
    const h = new Date(t);
    if (h < now && i > 0) return;
    if (h > new Date(now.getTime() + 24 * 3600 * 1000)) return;
    const pp = d.hourly.precipitation_probability[i];
    hl.innerHTML += `<div class="hourly-item">
      <div class="hh">${h.getHours()}h</div>
      <div class="hi">${WMO[d.hourly.weather_code[i]]||'🌡️'}</div>
      <div class="ht">${Math.round(d.hourly.temperature_2m[i])}°</div>
      ${pp > 0 ? `<div class="hp">💧${pp}%</div>` : ''}
    </div>`;
  });
}

/* ══════════════════════════════════════════
   RENDU DONNÉES ENVIRONNEMENTALES (Pro)
══════════════════════════════════════════ */
function renderEnvData(weatherData, airData) {

  /* ── UV (depuis Open-Meteo météo, déjà dans current) ── */
  const uv   = weatherData.current.uv_index;
  const uvLv = uvLevel(uv);
  document.getElementById('env-uv-val').innerHTML =
    `${uv} <span class="level-badge" style="background:${uvLv.color}22;color:${uvLv.color};border:1px solid ${uvLv.color}55">${uvLv.label}</span>`;
  document.getElementById('env-uv-sub').textContent =
    uv <= 2 ? 'Protection non nécessaire' :
    uv <= 5 ? 'Protection recommandée' :
    uv <= 7 ? 'Protection indispensable' : '⚠️ Évitez l\'exposition';

  /* ── Rafales max du jour ── */
  const gustsMax = Math.round(weatherData.daily.wind_gusts_10m_max?.[0] || weatherData.current.wind_gusts_10m || 0);
  document.getElementById('env-gusts-val').textContent = gustsMax + ' km/h';
  document.getElementById('env-gusts-sub').textContent =
    gustsMax < 50 ? 'Calme' : gustsMax < 80 ? 'Modérées' : gustsMax < 100 ? 'Fortes' : '⚠️ Très fortes';

  /* ── Qualité de l'air + Pollens (depuis Open-Meteo Air Quality) ── */
  if (airData && airData.current) {
    const aqi = airData.current.european_aqi;
    if (aqi != null) {
      const ai = aqiInfo(aqi);
      document.getElementById('env-aqi-val').innerHTML =
        `${aqi} <span class="level-badge" style="background:${ai.color}22;color:${ai.color};border:1px solid ${ai.color}55">${ai.level}</span>`;
      // Polluant dominant
      const dominants = {
        pm25: airData.current.european_aqi_pm2_5,
        no2:  airData.current.european_aqi_no2,
        o3:   airData.current.european_aqi_o3,
      };
      const dom = Object.entries(dominants).sort((a,b) => (b[1]||0)-(a[1]||0))[0];
      document.getElementById('env-aqi-sub').textContent = `Polluant principal : ${dom?.[0]?.toUpperCase() || '—'}`;
      // Barre colorée (max AQI ~300)
      const pct = Math.min(100, Math.round((aqi / 300) * 100));
      document.getElementById('env-aqi-bar').style.cssText = `width:${pct}%;background:${ai.color};height:4px;border-radius:4px;margin-top:.3rem;transition:width .6s`;
    }
  } else {
    document.getElementById('env-aqi-val').textContent = 'Indisponible';
    document.getElementById('env-aqi-sub').textContent = '(hors zone couverte)';
  }

  /* ── Pollens ── */
  if (airData && airData.hourly) {
    const pollens = {};
    let total = 0;
    Object.keys(POLLEN_NAMES).forEach(k => {
      const val = airData.hourly[k]?.[0];
      if (val != null) { pollens[k] = val; total += val; }
    });
    const risk = pollenRisk(total);
    document.getElementById('env-pollen-val').innerHTML =
      `<span class="level-badge" style="background:${risk.color}22;color:${risk.color};border:1px solid ${risk.color}55">${risk.level}</span>`;
    // Polluant dominant
    if (Object.keys(pollens).length) {
      const dom = Object.entries(pollens).sort((a,b) => b[1]-a[1])[0];
      document.getElementById('env-pollen-sub').textContent = 'Dominant : ' + (POLLEN_NAMES[dom[0]] || dom[0]);
    } else {
      document.getElementById('env-pollen-sub').textContent = 'Données indisponibles';
    }
  } else {
    document.getElementById('env-pollen-val').textContent = 'Indisponible';
    document.getElementById('env-pollen-sub').textContent = '(hors zone couverte)';
  }

  /* ── Vigilance Météo-France (France uniquement, silencieux si hors zone) ── */
  // On tente l'API Météo-France publique sur le département 75 (Paris) uniquement si la ville
  // semble française — on vérifie via le code pays dans nominatim (non dispo ici),
  // donc on tente et on ignore silencieusement si ça échoue.
  fetchVigilance();
}

async function fetchVigilance() {
  try {
    // Departement par défaut 75 — l'API est publique sans token
    const url = 'https://public-api.meteofrance.fr/public/vigilance/1.0/cartevigilance/encours';
    const d = await fetch(url, { headers:{ 'accept':'application/json' } }).then(r => r.json());
    // On cherche un niveau >= 3 (orange ou rouge)
    if (d && d.product && d.product.phenomenon_items) {
      const alerts = d.product.phenomenon_items.filter(p => (p.phenomenon_max_color_id || p.color_id) >= 3);
      if (alerts.length) {
        const top = alerts.sort((a,b) => (b.phenomenon_max_color_id||0) - (a.phenomenon_max_color_id||0))[0];
        const colorId = top.phenomenon_max_color_id || top.color_id;
        const colorLabel = colorId >= 4 ? '🔴 Rouge' : '🟠 Orange';
        const name = top.phenomenon_max_color || top.phenomenon_name || 'Alerte météo';
        document.getElementById('env-vig-val').textContent = colorLabel;
        document.getElementById('env-vig-sub').textContent = name;
        document.getElementById('env-vigilance').style.display = 'flex';
      }
    }
  } catch { /* silencieux hors France */ }
}

/* ══════════════════════════════════════════
   SHOW / ERROR / TOAST
══════════════════════════════════════════ */
function show(what) {
  document.getElementById('loading').style.display       = what === 'loading' ? 'block' : 'none';
  document.getElementById('error-box').style.display     = what === 'error'   ? 'block' : 'none';
  document.getElementById('weather-content').style.display = what === 'weather' ? 'block' : 'none';
}
function showError(msg) {
  show('error');
  document.getElementById('error-box').textContent = '❌ ' + msg;
}
function toast(m, t='ok') {
  const a = document.getElementById('ta');
  const e = document.createElement('div');
  e.className = `toast toast--${t}`;
  e.textContent = m;
  a.appendChild(e);
  setTimeout(() => e.remove(), 2500);
}

/* ══════════════════════════════════════════
   INIT
══════════════════════════════════════════ */
document.getElementById('city-input').addEventListener('keydown', e => e.key === 'Enter' && searchCity());
fetchAll(48.8566, 2.3522, 'Paris');
document.getElementById('city-input').value = 'Paris';
</script>
</body>
</html>