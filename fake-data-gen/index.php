<?php
/* ══════════════════════════════════════════════════════════
   DONNÉES SOURCES
══════════════════════════════════════════════════════════ */
$DATA = [
    'fr' => [
        'prenoms_m' => ['Alexandre','Antoine','Baptiste','Benjamin','Clément','Damien','Étienne','François','Gabriel','Guillaume','Hugo','Julien','Kevin','Laurent','Mathieu','Nicolas','Olivier','Pierre','Quentin','Romain','Samuel','Thomas','Valentin','Xavier','Yann'],
        'prenoms_f' => ['Alice','Amélie','Anaïs','Aurélie','Camille','Charlotte','Clara','Delphine','Émilie','Eva','Inès','Julie','Laura','Léa','Lucie','Manon','Marie','Mathilde','Nathalie','Pauline','Sarah','Sophie','Valentine','Victoria','Zoé'],
        'noms'      => ['Martin','Bernard','Dubois','Thomas','Robert','Richard','Petit','Durand','Leroy','Moreau','Simon','Laurent','Lefebvre','Michel','Garcia','David','Bertrand','Roux','Vincent','Fournier','Morin','Girard','André','Lefevre','Mercier'],
        'villes'    => [['Paris','75001'],['Lyon','69001'],['Marseille','13001'],['Bordeaux','33000'],['Toulouse','31000'],['Nantes','44000'],['Nice','06000'],['Strasbourg','67000'],['Montpellier','34000'],['Rennes','35000'],['Grenoble','38000'],['Lille','59000']],
        'rues'      => ['rue de la Paix','avenue des Champs-Élysées','boulevard Haussmann','rue du Faubourg Saint-Antoine','allée des Roses','impasse du Moulin','chemin des Vignes','place de la République','avenue Victor Hugo','rue Nationale'],
        'pays'      => 'France',
        'domaines'  => ['gmail.com','yahoo.fr','outlook.fr','free.fr','orange.fr','laposte.net','sfr.fr'],
        'entreprises'=> ['Société Générale','BNP Paribas','Air France','Renault','LVMH','Total Energies','Carrefour','Engie','Orange','Danone','Michelin','Peugeot','Bouygues','Capgemini','Veolia'],
        'postes'    => ['Développeur Full-Stack','Chef de Projet','Responsable Marketing','Data Analyst','UX Designer','Architecte Logiciel','DevOps Engineer','Product Manager','Scrum Master','CTO','Directeur Commercial','Comptable','Juriste','RH Manager','Consultant'],
        'secteurs'  => ['Technologie','Finance','Santé','Éducation','Commerce','Industrie','Transport','Énergie','Immobilier','Médias'],
        'tel_prefix'=> '0',
        'tel_fmt'   => fn() => '0' . rand(1,9) . implode('', array_map(fn() => rand(0,9), range(1,8))),
    ],
    'en' => [
        'prenoms_m' => ['James','John','Robert','Michael','William','David','Richard','Joseph','Thomas','Charles','Christopher','Daniel','Matthew','Anthony','Mark','Donald','Steven','Paul','Andrew','Joshua','Kenneth','Kevin','Brian','George','Timothy'],
        'prenoms_f' => ['Mary','Patricia','Jennifer','Linda','Barbara','Elizabeth','Susan','Jessica','Sarah','Karen','Lisa','Nancy','Betty','Sandra','Dorothy','Ashley','Kimberly','Donna','Emily','Michelle','Carol','Amanda','Melissa','Deborah','Stephanie'],
        'noms'      => ['Smith','Johnson','Williams','Brown','Jones','Garcia','Miller','Davis','Rodriguez','Martinez','Hernandez','Lopez','Gonzalez','Wilson','Anderson','Thomas','Taylor','Moore','Jackson','Martin','Lee','Perez','Thompson','White','Harris'],
        'villes'    => [['New York','10001'],['Los Angeles','90001'],['Chicago','60601'],['Houston','77001'],['Phoenix','85001'],['Philadelphia','19101'],['San Antonio','78201'],['San Diego','92101'],['Dallas','75201'],['San Jose','95101']],
        'rues'      => ['Main Street','Oak Avenue','Maple Drive','Cedar Lane','Pine Road','Elm Street','Washington Blvd','Park Avenue','Lake Drive','Hill Road'],
        'pays'      => 'United States',
        'domaines'  => ['gmail.com','yahoo.com','outlook.com','hotmail.com','icloud.com','protonmail.com'],
        'entreprises'=> ['Google','Microsoft','Apple','Amazon','Meta','Netflix','Tesla','Spotify','Airbnb','Uber','Twitter','LinkedIn','Adobe','Salesforce','Oracle'],
        'postes'    => ['Software Engineer','Product Manager','Data Scientist','UX Designer','DevOps Engineer','CTO','Marketing Manager','Sales Director','HR Manager','Business Analyst','Cloud Architect','Frontend Developer','Backend Developer','Scrum Master','QA Engineer'],
        'secteurs'  => ['Technology','Finance','Healthcare','Education','Retail','Manufacturing','Transportation','Energy','Real Estate','Media'],
        'tel_fmt'   => fn() => '+1 (' . rand(200,999) . ') ' . rand(200,999) . '-' . rand(1000,9999),
    ],
];

/* ══════════════════════════════════════════════════════════
   HELPERS
══════════════════════════════════════════════════════════ */
function pick(array $arr) { return $arr[array_rand($arr)]; }

function generateOne(array $d, string $locale): array {
    $genre   = rand(0,1) ? 'M' : 'F';
    $prenom  = pick($genre === 'M' ? $d['prenoms_m'] : $d['prenoms_f']);
    $nom     = pick($d['noms']);
    $ville   = pick($d['villes']); // [ville, cp]
    $rue     = rand(1,150) . ' ' . pick($d['rues']);
    $domaine = pick($d['domaines']);
    $slug    = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', iconv('UTF-8','ASCII//TRANSLIT',$prenom))) . '.' .
               strtolower(preg_replace('/[^a-zA-Z0-9]/', '', iconv('UTF-8','ASCII//TRANSLIT',$nom)));
    $email   = $slug . rand(1,999) . '@' . $domaine;
    $birthY  = rand(1960, 2002);
    $birthM  = str_pad(rand(1,12), 2, '0', STR_PAD_LEFT);
    $birthD  = str_pad(rand(1,28), 2, '0', STR_PAD_LEFT);
    $age     = date('Y') - $birthY;
    $salaire = rand(28, 95) * 1000;
    $tel     = ($d['tel_fmt'])();
    $ip      = rand(1,254).'.'.rand(0,254).'.'.rand(0,254).'.'.rand(1,254);
    $uuid    = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        rand(0,0xffff), rand(0,0xffff), rand(0,0xffff),
        rand(0,0x0fff)|0x4000, rand(0,0x3fff)|0x8000,
        rand(0,0xffff), rand(0,0xffff), rand(0,0xffff));
    $entreprise = pick($d['entreprises']);
    $poste      = pick($d['postes']);
    $secteur    = pick($d['secteurs']);
    $username   = $slug . rand(10,99);
    $pwd        = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%'), 0, 14);

    // IBAN fake FR
    $iban = 'FR' . rand(10,99) . ' ' . rand(1000,9999) . ' ' . rand(1000,9999) . ' ' . rand(1000,9999) . ' ' . rand(1000,9999) . ' ' . rand(100,999);

    return [
        'genre'      => $genre === 'M' ? ($locale === 'fr' ? 'Homme' : 'Male') : ($locale === 'fr' ? 'Femme' : 'Female'),
        'prenom'     => $prenom,
        'nom'        => $nom,
        'naissance'  => "$birthD/$birthM/$birthY",
        'age'        => $age . ($locale === 'fr' ? ' ans' : ' y.o.'),
        'email'      => $email,
        'telephone'  => $tel,
        'username'   => $username,
        'adresse'    => $rue,
        'ville'      => $ville[0],
        'cp'         => $ville[1],
        'pays'       => $d['pays'],
        'entreprise' => $entreprise,
        'poste'      => $poste,
        'secteur'    => $secteur,
        'salaire'    => number_format($salaire, 0, ',', ' ') . ($locale === 'fr' ? ' €/an' : ' $/yr'),
        'ip'         => $ip,
        'uuid'       => $uuid,
        'password'   => $pwd,
        'iban'       => $iban,
    ];
}

/* ══════════════════════════════════════════════════════════
   API endpoint
══════════════════════════════════════════════════════════ */
if (isset($_POST['action']) && $_POST['action'] === 'generate') {
    header('Content-Type: application/json');
    $locale = $_POST['locale'] === 'en' ? 'en' : 'fr';
    $count  = max(1, min(50, (int)($_POST['count'] ?? 5)));
    $fields = $_POST['fields'] ?? [];
    if (!is_array($fields) || empty($fields)) {
        $fields = array_keys(generateOne($DATA[$locale], $locale));
    }
    $d = $DATA[$locale];
    $rows = [];
    for ($i = 0; $i < $count; $i++) {
        $all  = generateOne($d, $locale);
        $row  = [];
        foreach ($fields as $f) { if (isset($all[$f])) $row[$f] = $all[$f]; }
        $rows[] = $row;
    }
    echo json_encode(['rows' => $rows, 'fields' => array_keys($rows[0] ?? [])], JSON_UNESCAPED_UNICODE);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Fake Data Generator — Black-Lab Toolbox</title>
<style>
html, body { height: 100%; overflow-y: auto; }
body { display: flex; flex-direction: column; }
.page-wrap { position: relative; z-index: 1; flex: 1; display: flex; flex-direction: column; }
.page-body {
    flex: 1; padding: 1rem 1.4rem 1.6rem;
    display: flex; flex-direction: column; gap: .85rem;
    max-width: 1400px; width: 100%; margin: 0 auto;
}

/* ── Config bar ── */
.cfg-bar {
    display: flex; gap: .6rem; align-items: center; flex-wrap: wrap;
}
.cfg-bar label { font-size: .72rem; color: var(--text-muted); white-space: nowrap; }
.cfg-bar select, .cfg-bar input[type=number] { width: auto; font-size: .82rem; padding: .35rem .6rem; }
.cfg-bar input[type=number] { width: 70px; }

/* ── Fields selector ── */
.fields-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: .4rem;
}
.field-chip {
    display: flex; align-items: center; gap: .5rem;
    padding: .35rem .65rem; border-radius: 6px; font-size: .75rem;
    border: 1px solid var(--border); color: var(--text-muted);
    cursor: pointer; transition: all .15s; user-select: none;
    background: var(--bg-2);
}
.field-chip input[type=checkbox] { accent-color: var(--accent,#ff1654); flex-shrink: 0; }
.field-chip:hover { border-color: var(--accent,#ff1654); color: var(--text); }
.field-chip.checked { border-color: var(--accent,#ff1654); color: var(--text); background: rgba(255,22,84,.06); }

/* ── Export tabs ── */
.view-tabs { display: flex; gap: .3rem; }
.vtab {
    padding: .22rem .65rem; border-radius: 6px; font-size: .72rem; font-weight: 600;
    cursor: pointer; border: 1px solid var(--border);
    background: transparent; color: var(--text-muted); transition: all .15s;
}
.vtab.active { background: rgba(255,22,84,.12); border-color: var(--accent,#ff1654); color: var(--accent,#ff1654); }

/* ── Result table ── */
.result-table-wrap { overflow-x: auto; }
.result-table { width: 100%; border-collapse: collapse; font-size: .75rem; }
.result-table th {
    font-family: 'Fira Code', monospace; font-size: .65rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .08em; color: var(--accent,#ff1654);
    padding: .5rem .7rem; border-bottom: 1px solid var(--border);
    text-align: left; background: rgba(0,0,0,.2); white-space: nowrap;
}
.result-table td {
    padding: .45rem .7rem; border-bottom: 1px solid var(--border);
    color: var(--text); font-family: 'Fira Code', monospace; font-size: .74rem;
    white-space: nowrap;
}
.result-table tr:hover td { background: rgba(255,255,255,.03); }
.result-table tr:last-child td { border: none; }

/* ── Code output ── */
.code-output {
    width: 100%; min-height: 260px; resize: vertical;
    font-family: 'Fira Code', monospace; font-size: .78rem; line-height: 1.7;
    background: rgba(0,0,0,.3); border: 1px solid var(--border);
    border-radius: var(--radius-sm); color: #a5d6a7;
    padding: .85rem 1rem; outline: none; tab-size: 2;
}

/* ── Category labels ── */
.cat-label {
    font-size: .62rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .1em; color: var(--text-muted);
    padding: .4rem 0 .2rem; border-bottom: 1px solid var(--border);
    margin-bottom: .4rem; grid-column: 1 / -1;
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
  <span class="hdr__icon">🎲</span>
  <span class="hdr__title">FAKE DATA GENERATOR</span>
  <span class="hdr__sep"></span>
  <span class="hdr__meta">Noms · Adresses · Emails · Pro · Web · UUID · Export JSON/CSV/SQL</span>
</header>
<div class="page-body">

  <!-- Config -->
  <div class="card">
    <div class="card-title">Configuration</div>
    <div class="cfg-bar" style="margin-bottom:.75rem">
      <label>Langue</label>
      <select id="cfg-locale">
        <option value="fr">🇫🇷 Français</option>
        <option value="en">🇬🇧 English</option>
      </select>
      <label>Nombre</label>
      <input type="number" id="cfg-count" value="5" min="1" max="50">
      <label>Table SQL</label>
      <input type="text" id="cfg-table" value="users" style="width:120px">
      <div style="flex:1"></div>
      <button class="btn btn-ghost btn-sm" onclick="selectAll()">Tout cocher</button>
      <button class="btn btn-ghost btn-sm" onclick="selectNone()">Tout décocher</button>
      <button class="btn btn-primary" onclick="generate()">🎲 Générer</button>
    </div>

    <!-- Champs à générer -->
    <div class="fields-grid" id="fields-grid">
      <div class="cat-label">👤 Identité</div>
      <label class="field-chip checked"><input type="checkbox" value="genre" checked> Genre</label>
      <label class="field-chip checked"><input type="checkbox" value="prenom" checked> Prénom</label>
      <label class="field-chip checked"><input type="checkbox" value="nom" checked> Nom</label>
      <label class="field-chip checked"><input type="checkbox" value="naissance" checked> Naissance</label>
      <label class="field-chip checked"><input type="checkbox" value="age" checked> Âge</label>

      <div class="cat-label">📧 Contact</div>
      <label class="field-chip checked"><input type="checkbox" value="email" checked> Email</label>
      <label class="field-chip checked"><input type="checkbox" value="telephone" checked> Téléphone</label>
      <label class="field-chip checked"><input type="checkbox" value="username" checked> Username</label>

      <div class="cat-label">📍 Adresse</div>
      <label class="field-chip checked"><input type="checkbox" value="adresse" checked> Rue</label>
      <label class="field-chip checked"><input type="checkbox" value="ville" checked> Ville</label>
      <label class="field-chip checked"><input type="checkbox" value="cp" checked> Code postal</label>
      <label class="field-chip"><input type="checkbox" value="pays"> Pays</label>

      <div class="cat-label">💼 Professionnel</div>
      <label class="field-chip checked"><input type="checkbox" value="entreprise" checked> Entreprise</label>
      <label class="field-chip checked"><input type="checkbox" value="poste" checked> Poste</label>
      <label class="field-chip"><input type="checkbox" value="secteur"> Secteur</label>
      <label class="field-chip"><input type="checkbox" value="salaire"> Salaire</label>

      <div class="cat-label">🌐 Technique</div>
      <label class="field-chip"><input type="checkbox" value="ip"> Adresse IP</label>
      <label class="field-chip"><input type="checkbox" value="uuid"> UUID</label>
      <label class="field-chip"><input type="checkbox" value="password"> Mot de passe</label>
      <label class="field-chip"><input type="checkbox" value="iban"> IBAN (fake)</label>
    </div>
  </div>

  <!-- Résultats -->
  <div class="card" id="results-card" style="display:none">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;flex-wrap:wrap;gap:.5rem">
      <div class="card-title" style="margin:0" id="results-title">Résultats</div>
      <div style="display:flex;gap:.4rem;align-items:center;flex-wrap:wrap">
        <div class="view-tabs">
          <button class="vtab active" id="vt-table"  onclick="switchView('table')">Tableau</button>
          <button class="vtab"        id="vt-json"   onclick="switchView('json')">JSON</button>
          <button class="vtab"        id="vt-csv"    onclick="switchView('csv')">CSV</button>
          <button class="vtab"        id="vt-sql"    onclick="switchView('sql')">SQL</button>
        </div>
        <button class="btn btn-ghost btn-sm" onclick="copyOutput()">📋 Copier</button>
        <button class="btn btn-ghost btn-sm" onclick="downloadOutput()">⬇ Télécharger</button>
        <button class="btn btn-ghost btn-sm" onclick="generate()">🔄 Regénérer</button>
      </div>
    </div>

    <!-- Table view -->
    <div id="view-table">
      <div class="result-table-wrap">
        <table class="result-table" id="data-table"></table>
      </div>
    </div>

    <!-- Code views -->
    <div id="view-code" style="display:none">
      <textarea class="code-output" id="code-output" readonly></textarea>
    </div>
  </div>

</div>
</div>
<div class="toast-area" id="ta"></div>

<script>
const $ = id => document.getElementById(id);
let lastRows   = [];
let lastFields = [];
let curView    = 'table';

/* ── Chips sync ── */
document.querySelectorAll('.field-chip').forEach(chip => {
    const cb = chip.querySelector('input');
    cb.addEventListener('change', () => {
        chip.classList.toggle('checked', cb.checked);
    });
});

function selectAll()  { document.querySelectorAll('.field-chip input').forEach(cb => { cb.checked = true;  cb.closest('.field-chip').classList.add('checked'); }); }
function selectNone() { document.querySelectorAll('.field-chip input').forEach(cb => { cb.checked = false; cb.closest('.field-chip').classList.remove('checked'); }); }

/* ══════════════════════════════════════════
   GÉNÉRATION
══════════════════════════════════════════ */
async function generate() {
    const fields = [...document.querySelectorAll('.field-chip input:checked')].map(cb => cb.value);
    if (!fields.length) { toast('Cochez au moins un champ', 'err'); return; }

    const fd = new FormData();
    fd.append('action', 'generate');
    fd.append('locale', $('cfg-locale').value);
    fd.append('count',  $('cfg-count').value);
    fields.forEach(f => fd.append('fields[]', f));

    try {
        const res  = await fetch('', { method: 'POST', body: fd });
        const data = await res.json();
        if (!data.rows) { toast('Erreur serveur', 'err'); return; }
        lastRows   = data.rows;
        lastFields = data.fields;
        $('results-title').textContent = `Résultats — ${lastRows.length} entrée${lastRows.length > 1 ? 's' : ''}`;
        $('results-card').style.display = 'block';
        renderView();
        $('results-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
    } catch(e) { toast('Erreur réseau', 'err'); }
}

/* ══════════════════════════════════════════
   VUES
══════════════════════════════════════════ */
function switchView(v) {
    curView = v;
    ['table','json','csv','sql'].forEach(x => $('vt-' + x).classList.toggle('active', x === v));
    renderView();
}

function renderView() {
    const isTable = curView === 'table';
    $('view-table').style.display = isTable ? 'block' : 'none';
    $('view-code').style.display  = isTable ? 'none'  : 'block';

    if (curView === 'table') {
        renderTable();
    } else if (curView === 'json') {
        $('code-output').value = JSON.stringify(lastRows, null, 2);
    } else if (curView === 'csv') {
        $('code-output').value = toCSV();
    } else if (curView === 'sql') {
        $('code-output').value = toSQL();
    }
}

function renderTable() {
    if (!lastRows.length) return;
    const thead = '<thead><tr>' + lastFields.map(f => `<th>${f}</th>`).join('') + '</tr></thead>';
    const tbody = '<tbody>' + lastRows.map(row =>
        '<tr>' + lastFields.map(f => `<td>${escHtml(row[f] ?? '—')}</td>`).join('') + '</tr>'
    ).join('') + '</tbody>';
    $('data-table').innerHTML = thead + tbody;
}

function toCSV() {
    const header = lastFields.join(',');
    const rows   = lastRows.map(row =>
        lastFields.map(f => '"' + String(row[f] ?? '').replace(/"/g, '""') + '"').join(',')
    );
    return [header, ...rows].join('\n');
}

function toSQL() {
    const table  = $('cfg-table').value.trim() || 'users';
    const cols   = lastFields.map(f => '`' + f + '`').join(', ');
    const rows   = lastRows.map(row => {
        const vals = lastFields.map(f => "'" + String(row[f] ?? '').replace(/'/g, "\\'") + "'").join(', ');
        return `  (${vals})`;
    });
    return `INSERT INTO \`${table}\` (${cols})\nVALUES\n${rows.join(',\n')};`;
}

/* ══════════════════════════════════════════
   COPY / DOWNLOAD
══════════════════════════════════════════ */
function copyOutput() {
    let text = '';
    if (curView === 'table') text = toCSV();
    else text = $('code-output').value;
    if (!text) return;
    navigator.clipboard.writeText(text).then(() => toast('Copié !')).catch(() => toast('Échec', 'err'));
}

function downloadOutput() {
    if (!lastRows.length) return;
    let content, ext, mime;
    if (curView === 'json') { content = JSON.stringify(lastRows, null, 2); ext = 'json'; mime = 'application/json'; }
    else if (curView === 'csv' || curView === 'table') { content = toCSV(); ext = 'csv'; mime = 'text/csv'; }
    else if (curView === 'sql') { content = toSQL(); ext = 'sql'; mime = 'text/plain'; }
    else return;
    const a = document.createElement('a');
    a.href = 'data:' + mime + ';charset=utf-8,' + encodeURIComponent(content);
    a.download = 'fake-data-blacklab.' + ext;
    a.click();
    toast('Téléchargement lancé !');
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function toast(m, t = 'ok') {
    const a = $('ta');
    const e = document.createElement('div');
    e.className = `toast toast--${t}`;
    e.textContent = m;
    a.appendChild(e);
    setTimeout(() => e.remove(), 2500);
}
</script>
</body>
</html>
