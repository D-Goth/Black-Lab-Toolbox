<?php
$score=0; $level='🟢 Faible'; $color_key='green';
$dangerous=$network=$execution=$obscure=$total=0;
$script_raw=''; $analysis_done=false; $truncated_notice='';

if (!empty($_POST['script'])) {
    $analysis_done = true;
    $script_raw = strip_tags($_POST['script']);
    if (strlen($script_raw) > 4*1024*1024) {
        $script_raw = substr($script_raw, 0, 4*1024*1024);
        $truncated_notice = '⚠️ Script tronqué à 4 Mo.';
    }
    function interpret_line($line) {
        $line = trim($line);
        if ($line === '') return '⸺ Ligne vide';
        if (preg_match('/^\s*[A-Z0-9_]+\s*=/', $line)) return '🧠 Affectation de variable';
        if (preg_match('/^\s*[a-zA-Z0-9_]+\s*\(\)\s*\{?$/', $line)) return '🧩 Définition de fonction shell';
        if (preg_match('/^\s*case\b/', $line)) return '🔀 Bloc conditionnel case';
        if (preg_match('/^\s*esac\b/', $line)) return '🔀 Fin de bloc case';
        if (preg_match('/^\s*\{/', $line)) return '🔲 Début de bloc';
        if (preg_match('/^\s*\}/', $line)) return '🔲 Fin de bloc';
        if (preg_match('/^\s*fi\b/', $line)) return '🔲 Fin de bloc if';
        if (preg_match('/^\s*then\b/', $line)) return '🔲 Bloc then';
        if (preg_match('/^\s*else\b/', $line)) return '🔲 Bloc else';
        if (preg_match('/\bmkdir\b/', $line)) return '📂 Création de répertoire';
        if (preg_match('/\bcd\b/', $line)) return '📂 Changement de répertoire';
        if (preg_match('/\btail\b/', $line)) return '📄 Affiche les dernières lignes d\'un fichier';
        if (preg_match('/\bcommand\s+-v\b/', $line)) return '🔍 Vérifie la présence d\'une commande';
        $patterns = [
            '/rm\s+-rf/' => '⚠️ Suppression récursive — potentiellement destructif',
            '/curl\s+.*\|\s*(sh|bash)/' => '⚠️ Téléchargement et exécution d\'un script distant — risque élevé',
            '/wget\s+.*\|\s*(sh|bash)/' => '⚠️ Téléchargement et exécution d\'un script distant — risque élevé',
            '/chmod\s+\+x/' => '🔧 Rend un fichier exécutable',
            '/chown\s+/' => '🔧 Change le propriétaire d\'un fichier',
            '/mv\s+/' => '📦 Déplacement ou renommage de fichier',
            '/cp\s+/' => '📄 Copie de fichier',
            '/echo\s+/' => '💬 Affiche un message ou une variable',
            '/grep\s+/' => '🔍 Recherche d\'un motif dans un texte',
            '/find\s+/' => '🔎 Recherche de fichiers dans un répertoire',
            '/tar\s+/' => '📦 Archive ou extraction de fichiers',
            '/apt\s+install/' => '📦 Installation de paquets système',
            '/systemctl\s+/' => '⚙️ Interaction avec un service système',
            '/for\s+/' => '🔁 Boucle sur une liste',
            '/while\s+/' => '🔁 Boucle conditionnelle',
            '/exit/' => '🚪 Fin du script',
            '/sleep\s+/' => '⏱️ Pause temporaire',
            '/base64\s+(--decode|-d)/' => '⚠️ Décodage base64 — peut cacher du code',
            '/eval\s+/' => '⚠️ Exécution dynamique — peut exécuter du code caché',
            '/exec\s+/' => '⚠️ Exécution directe',
            '/dd\s+/' => '⚠️ Manipulation bas niveau de disque',
            '/mkfs/' => '💣 Formatage de disque — dangereux',
            '/nc\s+/' => '📡 Netcat — réseau / backdoor potentiel',
            '/telnet\s+/' => '📡 Telnet — protocole non sécurisé',
            '/ftp\s+/' => '📡 FTP — non chiffré',
            '/openssl\s+enc/' => '🔐 Chiffrement / déchiffrement',
            '/iptables\s+/' => '🧱 Modification pare-feu',
            '/kill\s+/' => '☠️ Terminaison processus',
            '/pkill\s+/' => '☠️ Terminaison ciblée',
            '/nohup\s+/' => '🧩 Exécution persistante',
            '/su\s+/' => '🔑 Changement d\'utilisateur',
            '/passwd\s+/' => '🔑 Modification mot de passe',
            '/history\s+-c/' => '🕳️ Suppression historique',
        ];
        foreach ($patterns as $re => $desc) {
            if (preg_match($re, $line)) return $desc;
        }
        return '❓ Ligne non reconnue';
    }
    $lines = explode("\n", $script_raw);
    foreach ($lines as $line) {
        $total++;
        if (preg_match('/\b(rm\s+-rf|dd\s+|mkfs|chmod\s+\+x|chown|kill|pkill|history\s+-c)\b/', $line)) $dangerous++;
        if (preg_match('/\b(curl|wget|ftp|telnet|nc|scp|ssh)\b/', $line)) $network++;
        if (preg_match('/\b(eval|exec|bash|sh|\.\/|source|\. )\b/', $line)) $execution++;
        if (preg_match('/\b(base64\s+-d|openssl\s+enc|trap|disown|alias|nohup)\b/', $line)) $obscure++;
    }
    $score = $dangerous*3 + $execution*2 + $network + $obscure;
    if ($score >= 10) { $level='🔴 Élevé'; $color_key='red'; }
    elseif ($score >= 5) { $level='🟡 Modéré'; $color_key='yellow'; }
}
$score_color = ['green'=>'var(--green)','yellow'=>'var(--yellow)','red'=>'var(--red)'][$color_key];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Script Whisperer — Black-Lab Toolbox</title>
<style>
.sw-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.2rem;flex:1;min-height:0}
@media(max-width:720px){.sw-grid{grid-template-columns:1fr;overflow-y:auto}}
textarea{flex:1;min-height:0;font-family:'Fira Code',monospace;font-size:.8rem;line-height:1.6;resize:none}
.line-item{display:flex;gap:.75rem;padding:.38rem 0;border-bottom:1px solid var(--border);font-size:.79rem;align-items:flex-start}
.line-num{min-width:28px;color:var(--text-dim);font-family:'Fira Code',monospace;flex-shrink:0;text-align:right}
.line-code{color:var(--text-muted);font-family:'Fira Code',monospace;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex-shrink:0}
.line-interp{color:var(--text);flex:1}
.stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:.5rem;margin-bottom:1rem}
.stat-box{background:var(--bg-2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:.6rem;text-align:center}
.stat-n{font-size:1.4rem;font-weight:700}
.stat-l{font-size:.7rem;color:var(--text-muted);margin-top:.1rem}
.score-bar-wrap{height:8px;background:var(--border);border-radius:4px;overflow:hidden;margin:.4rem 0}
.score-bar-fill{height:100%;border-radius:4px;transition:width .5s}
</style>
<style>
html, body { height: 100%; overflow-y: auto; }
body { display: flex; flex-direction: column; }
.page-wrap { position: relative; z-index: 1; flex: 1; display: flex; flex-direction: column; }
.page-body { flex: 1; padding: 1.2rem 1.4rem; display: flex; flex-direction: column; gap: 1rem; max-width: 1100px; width: 100%; margin: 0 auto; }
/* Card gauche : textarea s'étire */
.card-left { display: flex; flex-direction: column; }
.card-left textarea { flex: 1; min-height: 300px; }
/* Colonne droite */
.col-right { display: flex; flex-direction: column; gap: 1rem; }
/* Card lignes : scroll interne avec hauteur max */
.card-lines { max-height: calc(100vh - 280px); overflow-y: auto; }
@media(max-width:720px){
  .card-left textarea { min-height: 240px; }
  .card-lines { max-height: none; }
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
  <span class="hdr__icon">🔍</span>
  <span class="hdr__title">SCRIPT WHISPERER</span>
  <span class="hdr__sep"></span>
  <span class="hdr__meta">Analyse de scripts Bash — explication ligne par ligne · scoring de dangerosité</span>
</header>
<div class="page-body">

  <?php if($truncated_notice): ?>
  <div class="card" style="color:var(--yellow);font-size:.82rem"><?= $truncated_notice ?></div>
  <?php endif; ?>

  <form method="POST">
  <div class="sw-grid">
    <div class="card card-left">
      <div class="card-title">Script Bash</div>
      <textarea name="script" placeholder="#!/bin/bash&#10;# Collez votre script ici…" spellcheck="false"><?= htmlspecialchars($script_raw) ?></textarea>
      <button type="submit" class="btn btn-primary" style="margin-top:.8rem;width:100%">🔬 Analyser</button>
    </div>

    <div class="col-right">
      <?php if($analysis_done): ?>
      <div class="card">
        <div class="card-title">Score de dangerosité</div>
        <div style="display:flex;align-items:center;gap:.8rem;margin-bottom:.8rem">
          <span style="font-size:1.6rem;font-weight:800;color:<?= $score_color ?>"><?= $score ?></span>
          <div>
            <div style="font-weight:600;color:<?= $score_color ?>"><?= $level ?></div>
            <div style="font-size:.75rem;color:var(--text-muted)"><?= $total ?> lignes analysées</div>
          </div>
        </div>
        <div class="score-bar-wrap">
          <div class="score-bar-fill" style="width:<?= min(100,round($score/20*100)) ?>%;background:<?= $score_color ?>"></div>
        </div>
        <div class="stat-grid" style="margin-top:.8rem">
          <div class="stat-box"><div class="stat-n" style="color:var(--red)"><?= $dangerous ?></div><div class="stat-l">Destructif</div></div>
          <div class="stat-box"><div class="stat-n" style="color:var(--yellow)"><?= $execution ?></div><div class="stat-l">Exécution</div></div>
          <div class="stat-box"><div class="stat-n" style="color:var(--accent)"><?= $network ?></div><div class="stat-l">Réseau</div></div>
          <div class="stat-box"><div class="stat-n" style="color:var(--text-muted)"><?= $obscure ?></div><div class="stat-l">Obscur</div></div>
        </div>
      </div>
      <div class="card card-lines">
        <div class="card-title">Interprétation ligne par ligne</div>
        <?php foreach(explode("\n",$script_raw) as $i=>$line): ?>
        <div class="line-item">
          <span class="line-num"><?= $i+1 ?></span>
          <span class="line-code" title="<?= htmlspecialchars(trim($line)) ?>"><?= htmlspecialchars(trim($line))?:' ' ?></span>
          <span class="line-interp"><?= interpret_line($line) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div class="card" style="text-align:center;padding:2rem;color:var(--text-muted)">
        <div style="font-size:2.5rem;margin-bottom:.5rem">🔬</div>
        Collez un script Bash et cliquez sur Analyser.
      </div>
      <?php endif; ?>
    </div>
  </div>
  </form>
</div><!-- /page-body -->
</div><!-- /page-wrap -->
</body>
</html>