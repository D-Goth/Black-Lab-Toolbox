<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Qrcode Gen — Black-Lab Toolbox</title>
<script src="https://cdn.jsdelivr.net/npm/qr-code-styling@1.5.0/lib/qr-code-styling.js"></script>
<style>
/* ── Layout principal ── */
.qr-main {
  display: grid;
  grid-template-columns: 2fr 1fr;
  grid-template-rows: auto auto;
  gap: 1rem;
  align-items: start;
}
.qr-params   { grid-column: 1; grid-row: 1; display: flex; flex-direction: column; gap: .85rem; }
.qr-preview  { grid-column: 2; grid-row: 1; display: flex; flex-direction: column; gap: .85rem; position: sticky; top: 1rem; }
.qr-tips     { grid-column: 1 / -1; grid-row: 2; }

@media(max-width:860px){
  .qr-main { grid-template-columns: 1fr; }
  .qr-params  { grid-column: 1; grid-row: 1; }
  .qr-preview { grid-column: 1; grid-row: 2; position: static; }
  .qr-tips    { grid-column: 1; grid-row: 3; }
}

/* ── Color picker ── */
.color-pair { display: grid; grid-template-columns: 1fr 1fr; gap: .6rem; }
.cpick { display: flex; align-items: center; gap: .4rem; }
.cpick input[type=color] { width: 32px; height: 28px; border: 1px solid var(--border); border-radius: var(--radius-sm); background: none; cursor: pointer; padding: 2px; flex-shrink: 0; }
.cpick input[type=text]  { flex: 1; font-family: 'Fira Code', monospace; font-size: .78rem; }

/* ── Canvas QR ── */
.qr-canvas {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  min-height: 260px;
  background: var(--bg-2); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem;
}

/* ── Validation badges ── */
.vbadge { display: inline-flex; align-items: center; gap: .35rem; padding: .2rem .6rem; border-radius: 20px; font-size: .75rem; font-weight: 600; }
.vok   { background: rgba(0,200,120,.12); color: var(--green); border: 1px solid rgba(0,200,120,.3); }
.vwarn { background: rgba(245,166,35,.1);  color: var(--yellow); border: 1px solid rgba(245,166,35,.3); }
.verr  { background: rgba(255,22,84,.12);  color: var(--accent); border: 1px solid rgba(255,22,84,.3); }

/* ── Slider row ── */
.strow { display: flex; align-items: center; gap: .5rem; }
.strow span { font-size: .73rem; color: var(--text-muted); white-space: nowrap; }

/* ── Tips ── */
.tips-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: .65rem; margin-top: .5rem; }
.tip-item {
  background: var(--bg-2); border: 1px solid var(--border); border-radius: var(--radius-sm);
  padding: .75rem .9rem; position: relative; overflow: hidden;
}
.tip-item::before {
  content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
  background: var(--gradient); opacity: .5;
}
.tip-icon { font-size: 1.3rem; margin-bottom: .3rem; }
.tip-title { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--text-muted); margin-bottom: .25rem; }
.tip-body { font-size: .78rem; color: var(--text); line-height: 1.5; }
</style>
<style>
html, body { height: 100%; overflow-y: auto; }
body { display: flex; flex-direction: column; }
.page-wrap { position: relative; z-index: 1; flex: 1; display: flex; flex-direction: column; }
.page-body { flex: 1; padding: 1.2rem 1.4rem 1.6rem; display: flex; flex-direction: column; gap: 1rem; max-width: 1300px; width: 100%; margin: 0 auto; }
</style>
</head>
<body>

<div class="ambient">
  <div class="ambient__circle"></div>
  <div class="ambient__circle"></div>
</div>

<div class="page-wrap">
<header class="hdr">
  <span class="hdr__icon">◼️</span>
  <span class="hdr__title">QR CODE GENERATOR</span>
  <span class="hdr__sep"></span>
  <span class="hdr__meta">Gradients · Label personnalisé · Google Fonts · Safe Browsing</span>
</header>
<div class="page-body">

  <div class="qr-main">

    <!-- ══ COLONNE GAUCHE : Paramètres (2/3) ══ -->
    <div class="qr-params">

      <div class="card">
        <div class="card-title">Contenu</div>
        <label>URL ou texte libre</label>
        <textarea id="qr-text" rows="3" placeholder="https://votre-site.com …">https://black-lab.fr</textarea>
        <div id="qr-valid" style="margin-top:.5rem"></div>
      </div>

      <div class="card">
        <div class="card-title">Couleurs</div>
        <div class="color-pair" style="margin-bottom:.65rem">
          <div>
            <label>Couleur 1</label>
            <div class="cpick"><input type="color" id="c1" value="#ff1654"><input type="text" id="c1h" value="#ff1654" maxlength="7"></div>
          </div>
          <div>
            <label>Dégradé → couleur 2</label>
            <div class="cpick"><input type="color" id="c2" value="#5e006c"><input type="text" id="c2h" value="#5e006c" maxlength="7"></div>
          </div>
        </div>
        <label>Fond</label>
        <div class="cpick" style="margin-bottom:.65rem">
          <input type="color" id="cb" value="#0d0d0f"><input type="text" id="cbh" value="#0d0d0f" maxlength="7">
        </div>
        <label>Angle dégradé : <strong id="ang-lbl">45</strong>°</label>
        <input type="range" id="qr-angle" min="0" max="360" value="45" style="margin-bottom:.5rem">
        <label>Ratio dégradé : <strong id="rat-lbl">50</strong>%</label>
        <input type="range" id="qr-ratio" min="0" max="100" value="50">
      </div>

      <div class="card">
        <div class="card-title">Forme &amp; Taille</div>
        <label>Style : <strong id="sty-lbl">Arrondi</strong></label>
        <div class="strow" style="margin-bottom:.65rem">
          <span>▪ Carré</span>
          <input type="range" id="qr-style" min="0" max="100" value="80" style="flex:1">
          <span>● Rond</span>
        </div>
        <label>Taille : <strong id="sz-lbl">300</strong>px</label>
        <input type="range" id="qr-size" min="150" max="600" value="300" step="10">
      </div>

      <div class="card">
        <div class="card-title">Label</div>
        <label>Texte sous le QR</label>
        <input type="text" id="qr-label" placeholder="Optionnel — ex: Scannez-moi !" style="margin-bottom:.6rem">
        <div class="grid-2">
          <div>
            <label>Police</label>
            <select id="qr-font">
              <option>Inter</option><option>Roboto</option><option>Montserrat</option>
              <option>Fira Code</option><option>Playfair Display</option>
              <option>Pacifico</option><option>Orbitron</option>
            </select>
          </div>
          <div><label>Taille (px)</label><input type="number" id="qr-fsize" value="14" min="8" max="48"></div>
        </div>
      </div>

    </div><!-- /qr-params -->

    <!-- ══ COLONNE DROITE : Aperçu + Export (1/3) ══ -->
    <div class="qr-preview">

      <div class="card">
        <div class="card-title">Aperçu</div>
        <div class="qr-canvas"><div id="qr-out"></div></div>
      </div>

      <div class="card">
        <div class="card-title">Export</div>
        <div class="btn-group">
          <button class="btn btn-primary" id="dl-png">⬇ PNG</button>
          <button class="btn btn-ghost"   id="dl-svg">⬇ SVG</button>
        </div>
      </div>

    </div><!-- /qr-preview -->

    <!-- ══ LIGNE DU BAS : Conseils (1/3 hauteur, pleine largeur) ══ -->
    <div class="qr-tips card">
      <div class="card-title">💡 Conseils & Bonnes pratiques</div>
      <div class="tips-grid">
        <div class="tip-item">
          <div class="tip-icon">📐</div>
          <div class="tip-title">Taille minimale</div>
          <div class="tip-body">Pour une impression physique, exportez en 300px minimum. Sur un flyer A5, prévoyez au moins 2cm × 2cm pour un scan fiable à distance normale.</div>
        </div>
        <div class="tip-item">
          <div class="tip-icon">🔗</div>
          <div class="tip-title">URLs courtes</div>
          <div class="tip-body">Plus l'URL est longue, plus le QR est dense et difficile à scanner. Utilisez un raccourcisseur (bit.ly, votre propre domaine) pour réduire la complexité.</div>
        </div>
        <div class="tip-item">
          <div class="tip-icon">🛡️</div>
          <div class="tip-title">Safe Browsing</div>
          <div class="tip-body">Chaque URL est vérifiée en temps réel via Google Safe Browsing. Un badge vert confirme que l'URL n'est pas signalée comme dangereuse.</div>
        </div>
        <div class="tip-item">
          <div class="tip-icon">🎨</div>
          <div class="tip-title">Dégradés</div>
          <div class="tip-body">Les dégradés sont esthétiques mais peuvent réduire la lisibilité. Testez toujours le QR final avec plusieurs appareils avant de diffuser.</div>
        </div>
        <div class="tip-item">
          <div class="tip-icon">📄</div>
          <div class="tip-title">Format SVG</div>
          <div class="tip-body">Privilégiez le SVG pour les impressions grand format — il est vectoriel et ne pixelise jamais. Le PNG est idéal pour le web et les réseaux sociaux.</div>
        </div>
      </div>
    </div><!-- /qr-tips -->

  </div><!-- /qr-main -->

</div><!-- /page-body -->
</div><!-- /page-wrap -->
<div class="toast-area" id="ta"></div>

<script>
document.addEventListener('DOMContentLoaded',()=>{
  let qr;
  const $=id=>document.getElementById(id);
  const txt=$('qr-text'), size=$('qr-size'), sty=$('qr-style');
  const c1=$('c1'), c1h=$('c1h'), c2=$('c2'), c2h=$('c2h'), cb=$('cb'), cbh=$('cbh');
  const ang=$('qr-angle'), rat=$('qr-ratio');
  const lbl=$('qr-label'), fnt=$('qr-font'), fsz=$('qr-fsize');
  const out=$('qr-out'), valid=$('qr-valid');

  // Color sync
  function syncPair(pick,hex){
    pick.addEventListener('input',()=>{hex.value=pick.value;gen();});
    hex.addEventListener('input',()=>{if(/^#[0-9a-fA-F]{6}$/.test(hex.value)){pick.value=hex.value;gen();}});
  }
  syncPair(c1,c1h); syncPair(c2,c2h); syncPair(cb,cbh);

  // Slider labels
  size.addEventListener('input',()=>{$('sz-lbl').textContent=size.value;gen();});
  ang.addEventListener('input', ()=>{$('ang-lbl').textContent=ang.value;gen();});
  rat.addEventListener('input', ()=>{$('rat-lbl').textContent=rat.value;gen();});
  sty.addEventListener('input', ()=>{$('sty-lbl').textContent=+sty.value>50?'Arrondi':'Carré';gen();});

  // Google fonts
  function loadFont(f){
    if(!f)return; const id='gf-'+f.replace(/\s+/g,'-').toLowerCase();
    if(document.getElementById(id))return;
    const l=Object.assign(document.createElement('link'),{id,rel:'stylesheet',
      href:`https://fonts.googleapis.com/css2?family=${f.replace(/ /g,'+')}&display=swap`});
    document.head.appendChild(l);
  }

  // Validation badge
  function badge(msg,cls){valid.innerHTML=`<span class="vbadge ${cls}">${msg}</span>`;}
  function isURL(s){return /^https?:\/\/[^\s/$.?#].[^\s]*$/i.test(s);}

  // Safe Browsing
  function checkSafe(url){
    fetch('https://safebrowsing.googleapis.com/v4/threatMatches:find?key=CHANGE_WITH_YOUR_API_KEY',{
      method:'POST',headers:{'Content-Type':'application/json'},
      body:JSON.stringify({client:{clientId:'Lab-O-Noir',clientVersion:'1.0'},threatInfo:{
        threatTypes:['MALWARE','SOCIAL_ENGINEERING'],platformTypes:['ANY_PLATFORM'],
        threatEntryTypes:['URL'],threatEntries:[{url}]}})
    }).then(r=>r.json()).then(d=>{
      if(d.matches?.length) badge('⚠ URL dangereuse (Safe Browsing)','verr');
      else badge('✓ URL vérifiée — Safe Browsing OK','vok');
    }).catch(()=>badge('⚠ Vérification réseau impossible','vwarn'));
  }

  // Generate
  function gen(){
    const data=txt.value.trim(); if(!data){badge('Saisissez une URL ou du texte','verr');return;}
    const n=+size.value, rounded=+sty.value>50;
    const has2=c1.value!==c2.value;
    out.innerHTML=''; valid.innerHTML='';
    qr=new QRCodeStyling({
      width:Math.min(n,600), height:Math.min(n,600), data,
      imageOptions:{crossOrigin:'anonymous',margin:0},
      dotsOptions:{color:c1.value,
        gradient:has2?{type:'linear',colorStops:[
          {offset:0,color:c1.value},{offset:+rat.value/100,color:c1.value},{offset:1,color:c2.value}
        ],rotation:+ang.value*Math.PI/180}:undefined,
        type:rounded?'rounded':'square'},
      backgroundOptions:{color:cb.value},
      cornersSquareOptions:{color:c1.value,type:rounded?'extra-rounded':'square'},
      cornersDotOptions:{color:c1.value,type:rounded?'dot':'square'}
    });
    qr.append(out);
    // Label
    const lt=lbl.value.trim();
    if(lt){
      loadFont(fnt.value);
      const el=document.createElement('div');
      Object.assign(el.style,{textAlign:'center',marginTop:'10px',
        fontFamily:fnt.value,fontSize:Math.min(+fsz.value,n)+'px',color:'#e8e8f0'});
      el.textContent=lt; out.appendChild(el);
    }
    // Validation
    if(isURL(data)) checkSafe(data);
    else badge('Texte libre — pas une URL','vwarn');
  }

  $('dl-png').onclick=()=>qr?.download({name:'qrcode-labonoir',extension:'png'});
  $('dl-svg').onclick=()=>qr?.download({name:'qrcode-labonoir',extension:'svg'});

  [txt,lbl,fsz].forEach(el=>el.addEventListener('input',gen));
  fnt.addEventListener('change',gen);
  gen();
});
</script>
</body>
</html>