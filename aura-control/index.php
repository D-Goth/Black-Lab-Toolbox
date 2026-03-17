<?php
/**
 * Template Name: Aura Control
 * Description:  Panneau de contrôle OBS + Chat multi-services + Soundboard OBS
 * Author:       D-Goth | Black-Lab.fr
 * Version:      2.0 — OBS WebSocket 5.x | Twitch | YouTube | Sécurisé
 * https://black-lab.fr/
 * © 2025 Black-Lab.fr | Licence CC BY-NC 4.0
 */


if (session_status() === PHP_SESSION_NONE) session_start();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Aura Control — Black-Lab</title>
<style>
/* ══════════════════════════════════════════════════
   VARIABLES
══════════════════════════════════════════════════ */
:root {
  --glass-bg:     rgba(42,42,42,0.95);
  --glass-border: rgba(255,255,255,0.5);
  --glass-blur:   16px;
  --text-main:    #ffffff;
  --text-muted:   #a0a0a0;
  --brand:        #ff1654;
  --brand-hover:  #e91e63;
  --input-bg:     #0f0f12;
  --input-border: #333;
  --radius:       16px;
  --success:      #27c93f;
  --warning:      #ffbd2e;
  --danger:       #ff5f56;
  --cyan:         #22d3ee;
  --led-on:       #00FF00;
  --led-off:      #333;
}

*,*::before,*::after { box-sizing:border-box; }



/* ── PROTOTYPE BANNER ── */
.proto-banner {
  background: rgba(255,189,46,0.08);
  border: 1px solid rgba(255,189,46,0.3);
  border-radius: 10px;
  padding: 12px 20px;
  margin-bottom: 24px;
  font-size: 13px;
  color: var(--warning);
  text-align: center;
}

/* ── STATUS BAR ── */
.status-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 24px;
  flex-wrap: wrap;
}
.status-badge {
  padding: 10px 20px;
  font-weight: 700;
  font-size: 13px;
  border-radius: 10px;
  border: 2px solid var(--brand);
  color: var(--brand);
  background: rgba(255,22,84,0.1);
  transition: all 0.3s;
  flex: 1;
  text-align: center;
  min-width: 180px;
}
.status-badge.connected {
  border-color: var(--led-on);
  color: var(--led-on);
  background: rgba(0,255,0,0.08);
  box-shadow: 0 0 12px rgba(0,255,0,0.15);
}
.status-badge.connecting {
  border-color: var(--warning);
  color: var(--warning);
  background: rgba(255,189,46,0.08);
}
.timer-badge {
  padding: 10px 20px;
  font-weight: 700;
  font-size: 13px;
  border-radius: 10px;
  border: 1px solid var(--input-border);
  color: var(--text-muted);
  background: rgba(15,15,18,0.6);
  font-family: 'Courier New', monospace;
  min-width: 120px;
  text-align: center;
}
.timer-badge.running { border-color: var(--brand); color: var(--brand); }
.timer-badge.recording { border-color: var(--danger); color: var(--danger); }

/* ── GLASS PANEL ── */
.glass-panel {
  background: rgba(26,26,26,0.5);
  border: 1px solid var(--glass-border);
  border-radius: var(--radius);
  padding: 24px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.3);
  margin-bottom: 20px;
}

.section-title {
  color: var(--brand);
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 2px;
  margin: 16px 0 10px 0;
  border-left: 2px solid var(--brand);
  padding-left: 10px;
}
.section-title:first-child { margin-top: 0; }

/* ── LAYOUT ── */
.aura-layout {
  display: grid;
  grid-template-columns: 1fr 360px 300px;
  gap: 20px;
  align-items: start;
}
@media (max-width: 1300px) { .aura-layout { grid-template-columns: 1fr 300px; } }
@media (max-width: 900px)  { .aura-layout { grid-template-columns: 1fr; } }

/* ── CONNECT PANEL ── */
.connect-grid {
  display: grid;
  grid-template-columns: 1fr 90px 1fr auto;
  gap: 8px;
  align-items: center;
  margin-bottom: 4px;
}
@media (max-width: 700px) { .connect-grid { grid-template-columns: 1fr 1fr; } }

.obs-input {
  padding: 10px 14px;
  background: rgba(15,15,18,0.8) !important;
  border: 1px solid var(--input-border);
  border-radius: 8px;
  color: #fff !important;
  font-size: 14px;
  outline: none;
  transition: all 0.2s;
  font-family: inherit;
  -webkit-text-fill-color: #fff !important;
}
.obs-input:focus {
  border-color: var(--brand);
  box-shadow: 0 0 0 3px rgba(255,22,84,0.2);
}
.obs-input::placeholder { color: rgba(160,160,160,0.5); }

/* ── BUTTONS ── */
.btn {
  padding: 10px 18px;
  border: 1px solid var(--input-border);
  border-radius: 8px;
  cursor: pointer;
  font-weight: 700;
  font-size: 13px;
  color: #fff;
  background: rgba(42,42,42,0.75);
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-family: inherit;
}
.btn:disabled { opacity: 0.4; cursor: not-allowed; }
.btn:hover:not(:disabled) {
  border-color: var(--brand);
  box-shadow: 0 0 0 3px rgba(255,22,84,0.2), 0 0 12px rgba(255,22,84,0.15);
}
.btn-connect {
  background: var(--brand);
  border-color: var(--brand);
  padding: 10px 20px;
  white-space: nowrap;
}
.btn-connect:hover:not(:disabled) {
  background: var(--brand-hover);
  box-shadow: 0 0 0 3px rgba(255,22,84,0.3), 0 0 16px rgba(255,22,84,0.25);
}

/* ── HARDWARE BUTTONS (contrôles OBS) ── */
.control-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
}

.hw-btn {
  background: rgba(42,42,42,0.75);
  border: 1px solid var(--input-border);
  border-radius: 12px;
  padding: 16px 8px;
  cursor: pointer;
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 5px;
  transition: all 0.2s;
  min-height: 85px;
  font-family: inherit;
  color: var(--text-muted);
  user-select: none;
}
.hw-btn span.icon { font-size: 1.4rem; }
.hw-btn small { font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }

.hw-btn:hover:not(:disabled) {
  border-color: var(--brand);
  transform: translateY(-2px);
  box-shadow: 0 0 0 2px rgba(255,22,84,0.2), 0 0 12px rgba(255,22,84,0.15);
  color: var(--text-main);
}
.hw-btn:disabled { opacity: 0.35; cursor: not-allowed; }

/* Glow actif selon état */
.hw-btn.active-stream {
  border-color: var(--led-on);
  box-shadow: 0 0 0 2px rgba(0,255,0,0.3), 0 0 16px rgba(0,255,0,0.2);
  color: var(--led-on);
}
.hw-btn.active-record {
  border-color: var(--danger);
  box-shadow: 0 0 0 2px rgba(255,95,86,0.3), 0 0 16px rgba(255,95,86,0.2);
  color: var(--danger);
}
.hw-btn.active-mute {
  border-color: var(--warning);
  box-shadow: 0 0 0 2px rgba(255,189,46,0.3), 0 0 16px rgba(255,189,46,0.2);
  color: var(--warning);
}
.hw-btn.active-scene {
  border-color: var(--cyan);
  box-shadow: 0 0 0 2px rgba(34,211,238,0.3), 0 0 14px rgba(34,211,238,0.2);
  color: var(--cyan);
}
.hw-btn.playing {
  border-color: #a855f7;
  box-shadow: 0 0 0 2px rgba(168,85,247,0.3), 0 0 14px rgba(168,85,247,0.2);
  color: #a855f7;
}

/* LED indicator */
.led {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 7px; height: 7px;
  border-radius: 50%;
  background: var(--led-off);
  transition: all 0.3s;
}
.led.on { background: var(--led-on); box-shadow: 0 0 6px var(--led-on); }
.led.on-red { background: var(--danger); box-shadow: 0 0 6px var(--danger); }
.led.on-yellow { background: var(--warning); box-shadow: 0 0 6px var(--warning); }

/* ── VOLUME SECTION ── */
.volume-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 14px;
}
.volume-row select {
  background: rgba(15,15,18,0.8);
  color: #fff;
  border: 1px solid var(--input-border);
  padding: 8px 10px;
  border-radius: 8px;
  font-size: 13px;
  outline: none;
  flex: 1;
  font-family: inherit;
  transition: border-color 0.2s;
}
.volume-row select:focus { border-color: var(--brand); }
.volume-row select option { background: #1a1a1a; }

input[type="range"] {
  -webkit-appearance: none;
  appearance: none;
  height: 6px;
  border: none;
  border-radius: 6px;
  outline: none;
  flex: 2;
  background: linear-gradient(90deg, var(--brand) var(--pct, 100%), #333 var(--pct, 100%));
}
input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 16px; height: 16px;
  background: var(--brand);
  border-radius: 50%;
  cursor: pointer;
  box-shadow: 0 0 6px rgba(255,22,84,0.5);
}
input[type="range"]::-moz-range-thumb {
  width: 16px; height: 16px;
  background: var(--brand);
  border-radius: 50%;
  cursor: pointer;
  border: none;
  box-shadow: 0 0 6px rgba(255,22,84,0.5);
}

.vol-value {
  font-size: 12px;
  font-weight: 700;
  color: var(--brand);
  min-width: 36px;
  text-align: right;
  font-family: 'Courier New', monospace;
}

/* ── VU METER ── */
.vu-meter {
  display: flex;
  gap: 2px;
  align-items: flex-end;
  height: 20px;
  margin-top: 6px;
}
.vu-bar {
  width: 4px;
  border-radius: 2px;
  background: var(--input-border);
  transition: height 0.1s, background 0.1s;
  min-height: 2px;
}

/* ── CHAT PANEL ── */
.chat-service-select {
  display: flex;
  gap: 8px;
  margin-bottom: 12px;
  flex-wrap: wrap;
}
.chat-tab {
  padding: 6px 14px;
  border: 1px solid var(--input-border);
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  background: transparent;
  color: var(--text-muted);
  font-family: inherit;
}
.chat-tab:hover { border-color: var(--brand); color: var(--text-main); }
.chat-tab.active {
  background: rgba(255,22,84,0.12);
  border-color: var(--brand);
  color: var(--brand);
  box-shadow: 0 0 8px rgba(255,22,84,0.2);
}
.chat-tab.connected-tab {
  border-color: var(--led-on);
  color: var(--led-on);
  background: rgba(0,255,0,0.06);
  box-shadow: 0 0 8px rgba(0,255,0,0.15);
}

/* Auth zone */
.auth-zone {
  background: rgba(15,15,18,0.6);
  border: 1px solid var(--input-border);
  border-radius: 10px;
  padding: 14px;
  margin-bottom: 12px;
  font-size: 13px;
}
.auth-zone h4 {
  color: var(--brand);
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin: 0 0 10px 0;
}
.auth-step {
  color: var(--text-muted);
  font-size: 12px;
  margin-bottom: 8px;
  line-height: 1.5;
}
.auth-step a { color: var(--cyan); text-decoration: none; }
.auth-step a:hover { text-decoration: underline; }
.auth-input {
  width: 100%;
  padding: 8px 12px;
  background: rgba(15,15,18,0.8);
  border: 1px solid var(--input-border);
  border-radius: 6px;
  color: #fff;
  font-size: 13px;
  outline: none;
  margin-bottom: 6px;
  font-family: inherit;
  transition: border-color 0.2s;
}
.auth-input:focus { border-color: var(--brand); }
.btn-auth {
  background: var(--brand);
  border: none;
  color: #fff;
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 700;
  font-size: 13px;
  font-family: inherit;
  transition: all 0.2s;
  width: 100%;
  margin-top: 4px;
}
.btn-auth:hover {
  background: var(--brand-hover);
  box-shadow: 0 0 12px rgba(255,22,84,0.3);
}
.btn-oauth {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  border: none;
  font-family: inherit;
  transition: all 0.2s;
  margin-bottom: 6px;
}
.btn-oauth.twitch { background: #9147ff; color: #fff; }
.btn-oauth.twitch:hover { background: #772ce8; box-shadow: 0 0 12px rgba(145,71,255,0.4); }
.btn-oauth.youtube { background: #ff0000; color: #fff; }
.btn-oauth.youtube:hover { background: #cc0000; box-shadow: 0 0 12px rgba(255,0,0,0.4); }

/* Chat messages */
.chat-messages {
  height: 380px;
  overflow-y: auto;
  font-size: 13px;
  padding-right: 4px;
}
.chat-messages::-webkit-scrollbar { width: 3px; }
.chat-messages::-webkit-scrollbar-thumb { background: #333; border-radius: 3px; }

.chat-msg {
  padding: 5px 0;
  border-bottom: 1px solid rgba(255,255,255,0.04);
  line-height: 1.4;
  word-break: break-word;
}
.chat-msg:last-child { border-bottom: none; }
.chat-msg-author {
  font-weight: 700;
  font-size: 12px;
  margin-right: 6px;
}
.chat-msg-text { color: var(--text-muted); font-size: 13px; }
.chat-msg-time { font-size: 10px; color: #555; margin-left: 4px; }

.chat-empty {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #444;
  font-size: 13px;
  text-align: center;
  flex-direction: column;
  gap: 6px;
}
.chat-empty-icon { font-size: 2rem; opacity: 0.3; }

/* Chat service badge */
.service-badge {
  display: inline-block;
  padding: 1px 6px;
  border-radius: 4px;
  font-size: 9px;
  font-weight: 700;
  text-transform: uppercase;
  margin-right: 4px;
}
.service-badge.twitch { background: #9147ff; color: #fff; }
.service-badge.youtube { background: #ff0000; color: #fff; }

/* ── SHORTCUTS HINT ── */
.shortcuts-bar {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-top: 10px;
}
.shortcut-pill {
  background: rgba(255,255,255,0.04);
  border: 1px solid #333;
  border-radius: 6px;
  padding: 3px 8px;
  font-size: 10px;
  color: #555;
}
.shortcut-pill kbd {
  background: rgba(255,255,255,0.08);
  border-radius: 3px;
  padding: 1px 4px;
  font-family: monospace;
  color: var(--text-muted);
  font-size: 10px;
}

/* ── TOAST ── */
.toast {
  position: fixed;
  bottom: 30px; right: 30px;
  padding: 12px 20px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  color: #fff;
  z-index: 99999;
  opacity: 0;
  transform: translateY(10px);
  transition: all 0.3s;
  pointer-events: none;
  max-width: 300px;
}
.toast.show { opacity: 1; transform: translateY(0); }
.toast.success { background: rgba(39,201,63,0.9);  border:1px solid var(--success); box-shadow: 0 4px 20px rgba(39,201,63,0.3); }
.toast.error   { background: rgba(255,87,86,0.9);  border:1px solid var(--danger);  box-shadow: 0 4px 20px rgba(255,87,86,0.3); }
.toast.info    { background: rgba(255,22,84,0.9);  border:1px solid var(--brand);   box-shadow: 0 4px 20px rgba(255,22,84,0.3); }
.toast.warn    { background: rgba(255,189,46,0.9); border:1px solid var(--warning); box-shadow: 0 4px 20px rgba(255,189,46,0.3); color:#111; }
</style>

<!-- Ambient circles -->
<div class="ambient">
  <div class="ambient__circle"></div>
  <div class="ambient__circle"></div>
</div>

<header class="hdr">
  <span class="hdr__icon">⚡</span>
  <span class="hdr__title">AURA CONTROL</span>
  <span class="hdr__sep"></span>
  <span class="hdr__meta">OBS WebSocket · Soundboard · Chat multi-services</span>
</header>

<div style="position:relative;z-index:1;overflow-y:auto;height:calc(100vh - 48px)">
<div style="max-width:1600px;margin:0 auto;padding:1.4rem">

      <!-- PROTO BANNER -->
      <div class="proto-banner">
        ⚠️ Prototype — connexion locale uniquement (OBS WebSocket sur réseau local). Pas d'installation requise côté serveur.
      </div>

      <!-- STATUS BAR -->
      <div class="status-bar">
        <div class="status-badge" id="obs-status">🔴 DÉCONNECTÉ</div>
        <div class="timer-badge" id="stream-timer">▶ --:--:--</div>
        <div class="timer-badge" id="record-timer">⏺ --:--:--</div>
        <div class="shortcut-pill">
          <kbd>F1</kbd> Mute &nbsp;
          <kbd>F2</kbd> Stream &nbsp;
          <kbd>F3</kbd> Record &nbsp;
          <kbd>F4</kbd> Snap
        </div>
      </div>

      <!-- CONNECT PANEL -->
      <div class="glass-panel">
        <h2 class="section-title">Connexion OBS WebSocket</h2>
        <div class="connect-grid">
          <input class="obs-input" type="text" id="obsIp"
                 placeholder="IP (ex: 192.168.1.10 ou localhost)"
                 value="localhost" maxlength="50" autocomplete="off">
          <input class="obs-input" type="text" id="obsPort"
                 placeholder="Port" value="4455" maxlength="6" autocomplete="off">
          <input class="obs-input" type="password" id="obsPassword"
                 placeholder="Mot de passe OBS"
                 value="<?php echo htmlspecialchars($_SESSION['obs_pw_hint'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                 maxlength="128" autocomplete="off">
          <button class="btn btn-connect" id="btn-connect" onclick="obsConnect()">CONNECT</button>
        </div>
      </div>

      <!-- MAIN LAYOUT -->
      <div class="aura-layout">

        <!-- ═══ COLONNE 1 — CONTRÔLES OBS ═══════════════════════ -->
        <div>
          <!-- Flux & Capture -->
          <div class="glass-panel">
            <h2 class="section-title">Flux &amp; Capture</h2>
            <div class="control-grid">

              <button class="hw-btn" id="btn-stream" onclick="toggleStream()" disabled>
                <div class="led" id="led-stream"></div>
                <span class="icon">▶️</span>
                <small id="lbl-stream">STREAM</small>
              </button>

              <button class="hw-btn" id="btn-record" onclick="toggleRecord()" disabled>
                <div class="led" id="led-record"></div>
                <span class="icon">🔴</span>
                <small id="lbl-record">RECORD</small>
              </button>

              <button class="hw-btn" id="btn-mute" onclick="toggleMute()" disabled>
                <div class="led" id="led-mute"></div>
                <span class="icon">🔇</span>
                <small>MUTE</small>
              </button>

              <button class="hw-btn" id="btn-snap" onclick="takeScreenshot()" disabled>
                <span class="icon">📸</span>
                <small>SNAP</small>
              </button>

              <button class="hw-btn" id="btn-pause-rec" onclick="pauseRecord()" disabled>
                <span class="icon">⏸️</span>
                <small>PAUSE REC</small>
              </button>

              <button class="hw-btn" onclick="replayBuffer()" disabled id="btn-replay">
                <span class="icon">🔁</span>
                <small>REPLAY</small>
              </button>

            </div>

            <!-- Scènes -->
            <h2 class="section-title">Scènes OBS</h2>
            <div class="control-grid" id="scene-grid">
              <div style="color:var(--text-muted);font-size:12px;grid-column:1/-1;text-align:center;padding:10px;">
                Connectez-vous à OBS pour charger les scènes
              </div>
            </div>

            <!-- Volume -->
            <h2 class="section-title">Audio</h2>
            <div class="volume-row">
              <select id="audioSources" onchange="setAudioSource(this.value)">
                <option value="">— Source audio —</option>
              </select>
              <input type="range" id="volSlider" min="0" max="100" value="100"
                     oninput="handleVolume(this.value)" style="--pct:100%">
              <span class="vol-value" id="vol-value">100%</span>
            </div>
            <div class="vu-meter" id="vu-meter">
              <?php for($i=0;$i<20;$i++) echo '<div class="vu-bar" style="height:3px"></div>'; ?>
            </div>

          </div>

          <!-- Soundboard -->
          <div class="glass-panel">
            <h2 class="section-title">Soundboard OBS</h2>
            <p style="font-size:12px;color:var(--text-muted);margin-bottom:12px;">
              ℹ️ Chaque son est joué via une source Media OBS dédiée (<code>BL_Soundboard</code>).
              Créez cette source dans OBS en avance, ou laissez Aura la créer automatiquement.
            </p>
            <div class="control-grid" id="sound-grid">
              <?php
              $sounds = [
                ['id'=>'applause', 'icon'=>'🎉', 'label'=>'APPLAUSE'],
                ['id'=>'laugh',    'icon'=>'😂', 'label'=>'LAUGH'],
                ['id'=>'airhorn',  'icon'=>'📢', 'label'=>'HORN'],
                ['id'=>'alert',    'icon'=>'🚨', 'label'=>'ALERT'],
                ['id'=>'explosion','icon'=>'💥', 'label'=>'BOOM'],
                ['id'=>'guitar',   'icon'=>'🎸', 'label'=>'RIFF'],
                ['id'=>'drum',     'icon'=>'🥁', 'label'=>'DRUM'],
                ['id'=>'win',      'icon'=>'🏆', 'label'=>'WIN'],
                ['id'=>'fail',     'icon'=>'😬', 'label'=>'FAIL'],
              ];
              foreach($sounds as $s):
              ?>
              <button class="hw-btn sound-btn" data-sound="<?= htmlspecialchars($s['id'],ENT_QUOTES) ?>"
                      onclick="playObsSound('<?= htmlspecialchars($s['id'],ENT_QUOTES) ?>')" disabled>
                <span class="icon"><?= $s['icon'] ?></span>
                <small><?= htmlspecialchars($s['label'],ENT_QUOTES) ?></small>
              </button>
              <?php endforeach; ?>
            </div>
            <div style="margin-top:12px;font-size:12px;color:var(--text-muted);">
              Chemins des fichiers sons (relatifs au PC OBS) :
              <input class="obs-input" type="text" id="sound-path"
                     placeholder="/chemin/vers/sons/ (ex: C:/sounds/)"
                     style="margin-top:6px;font-size:12px;width:100%;" autocomplete="off">
            </div>
          </div>
        </div>

        <!-- ═══ COLONNE 2 — CHAT ══════════════════════════════════ -->
        <div>
          <div class="glass-panel" style="height:100%;">
            <h2 class="section-title">Chat Live</h2>

            <!-- Onglets services -->
            <div class="chat-service-select">
              <button class="chat-tab active" id="tab-twitch" onclick="selectChatService('twitch')">
                💜 Twitch
              </button>
              <button class="chat-tab" id="tab-youtube" onclick="selectChatService('youtube')">
                🔴 YouTube
              </button>
            </div>

            <!-- Zone auth Twitch -->
            <div class="auth-zone" id="auth-twitch">
              <h4>💜 Connexion Twitch IRC</h4>
              <div class="auth-step">
                1. Obtenez un token OAuth sur
                <a href="https://twitchapps.com/tmi/" target="_blank" rel="noopener">twitchapps.com/tmi</a>
                (connexion avec votre compte Twitch)
              </div>
              <input class="auth-input" type="text" id="twitch-channel"
                     placeholder="Votre pseudo Twitch (ex: monpseudo)" maxlength="64" autocomplete="off">
              <input class="auth-input" type="password" id="twitch-token"
                     placeholder="oauth:xxxxxxxxxxxxxxxxxxxxxxx" maxlength="200" autocomplete="off">
              <button class="btn-auth" onclick="connectTwitch()">💜 Connecter Twitch IRC</button>
            </div>

            <!-- Zone auth YouTube (masquée par défaut) -->
            <div class="auth-zone" id="auth-youtube" style="display:none;">
              <h4>🔴 Connexion YouTube Live Chat</h4>
              <div class="auth-step">
                1. Créez un projet sur
                <a href="https://console.cloud.google.com/" target="_blank" rel="noopener">Google Cloud Console</a>
                et activez l'API YouTube Data v3.
              </div>
              <div class="auth-step">
                2. Créez des identifiants OAuth 2.0 (type "Application Web"),
                ajoutez <code><?php echo htmlspecialchars(home_url(), ENT_QUOTES); ?></code> comme URI de redirection.
              </div>
              <input class="auth-input" type="text" id="yt-client-id"
                     placeholder="Client ID Google OAuth" maxlength="200" autocomplete="off">
              <input class="auth-input" type="text" id="yt-video-id"
                     placeholder="ID vidéo YouTube Live (ex: dQw4w9WgXcQ)" maxlength="20" autocomplete="off">
              <button class="btn-oauth youtube" onclick="startYouTubeOAuth()">
                🔴 Autoriser via Google OAuth
              </button>
              <div class="auth-step" id="yt-token-zone" style="display:none;margin-top:8px;">
                <input class="auth-input" type="text" id="yt-access-token"
                       placeholder="Collez ici le token retourné" maxlength="500" autocomplete="off">
                <button class="btn-auth" onclick="connectYouTube()">🔴 Connecter YouTube Live</button>
              </div>
            </div>

            <!-- Messages -->
            <div class="chat-messages" id="chat-messages">
              <div class="chat-empty">
                <div class="chat-empty-icon">💬</div>
                Connectez un service de chat pour voir les messages en direct
              </div>
            </div>

          </div>
        </div>

      </div><!-- /.aura-layout -->
    </div><!-- /inner -->
</div><!-- /scroll -->

<div class="toast" id="toast"></div>

<script>
/* ══════════════════════════════════════════════════════════════
   ÉTAT GLOBAL
══════════════════════════════════════════════════════════════ */
const STATE = {
  obs:            null,       // WebSocket OBS
  obsConnected:   false,
  obsRetries:     0,
  obsMaxRetries:  5,
  streaming:      false,
  recording:      false,
  recordPaused:   false,
  muted:          false,
  currentScene:   null,
  currentSource:  null,
  streamStart:    null,
  recordStart:    null,

  // Chat
  chatService:    'twitch',
  twitchWs:       null,
  twitchConnected:false,
  ytConnected:    false,
  ytInterval:     null,
  ytToken:        null,
  ytVideoId:      null,
  ytNextToken:    null,
  chatColors:     {},

  // Timers
  streamTimer:    null,
  recordTimer:    null,
  vuTimer:        null,
};

/* ══════════════════════════════════════════════════════════════
   TOAST
══════════════════════════════════════════════════════════════ */
let _toastTimer = null;
function toast(msg, type = 'info') {
  const el = document.getElementById('toast');
  el.className = 'toast show ' + type;
  el.textContent = msg;
  clearTimeout(_toastTimer);
  _toastTimer = setTimeout(() => el.classList.remove('show'), 3200);
}

/* ══════════════════════════════════════════════════════════════
   HELPERS
══════════════════════════════════════════════════════════════ */
function setDisabled(ids, disabled) {
  ids.forEach(id => {
    const el = document.getElementById(id);
    if (el) el.disabled = disabled;
  });
}

function fmtTime(secs) {
  const h = Math.floor(secs / 3600);
  const m = Math.floor((secs % 3600) / 60);
  const s = secs % 60;
  return [h,m,s].map(n => String(n).padStart(2,'0')).join(':');
}

function startTimer(type) {
  const key   = type === 'stream' ? 'streamTimer' : 'recordTimer';
  const start = type === 'stream' ? 'streamStart'  : 'recordStart';
  const elId  = type === 'stream' ? 'stream-timer' : 'record-timer';
  const icon  = type === 'stream' ? '▶' : '⏺';
  const cls   = type === 'stream' ? 'running' : 'recording';
  clearInterval(STATE[key]);
  STATE[start] = Date.now();
  STATE[key] = setInterval(() => {
    const el = document.getElementById(elId);
    const s  = Math.floor((Date.now() - STATE[start]) / 1000);
    el.textContent = icon + ' ' + fmtTime(s);
    el.className = 'timer-badge ' + cls;
  }, 1000);
}

function stopTimer(type) {
  const key   = type === 'stream' ? 'streamTimer' : 'recordTimer';
  const elId  = type === 'stream' ? 'stream-timer' : 'record-timer';
  const icon  = type === 'stream' ? '▶' : '⏺';
  clearInterval(STATE[key]);
  STATE[key] = null;
  const el = document.getElementById(elId);
  el.textContent = icon + ' --:--:--';
  el.className = 'timer-badge';
}

/* Couleur pseudo chat stable */
function chatColor(user) {
  if (!STATE.chatColors[user]) {
    const palette = ['#ff1654','#a855f7','#22d3ee','#27c93f','#ffbd2e','#f97316','#818cf8','#fb923c'];
    STATE.chatColors[user] = palette[Math.abs(user.split('').reduce((a,c) => a+c.charCodeAt(0),0)) % palette.length];
  }
  return STATE.chatColors[user];
}

/* ══════════════════════════════════════════════════════════════
   OBS WEBSOCKET 5.x — AUTH SHA256 CHALLENGE
══════════════════════════════════════════════════════════════ */
async function sha256base64(message) {
  const encoder = new TextEncoder();
  const data    = encoder.encode(message);
  const hash    = await crypto.subtle.digest('SHA-256', data);
  return btoa(String.fromCharCode(...new Uint8Array(hash)));
}

async function obsAuthenticate(auth) {
  const pw       = document.getElementById('obsPassword').value;
  const secret   = await sha256base64(pw + auth.salt);
  const response = await sha256base64(secret + auth.challenge);
  obsSend({ op: 1, d: { rpcVersion: 1, authentication: response, eventSubscriptions: 127 } });
}

function obsSend(obj) {
  if (STATE.obs && STATE.obs.readyState === WebSocket.OPEN) {
    STATE.obs.send(JSON.stringify(obj));
  }
}

function obsRequest(requestType, requestData = {}, requestId = null) {
  obsSend({
    op: 6,
    d: {
      requestType,
      requestId:   requestId || requestType + '_' + Date.now(),
      requestData: Object.keys(requestData).length ? requestData : undefined,
    }
  });
}

let _obsReconnectTimer = null;
function obsConnect() {
  const ip   = document.getElementById('obsIp').value.trim()   || 'localhost';
  const port = document.getElementById('obsPort').value.trim() || '4455';

  if (!ip || !/^\d+$/.test(port)) { toast('IP ou port invalide', 'error'); return; }

  document.getElementById('obs-status').className = 'status-badge connecting';
  document.getElementById('obs-status').textContent = '🟡 CONNEXION…';

  if (STATE.obs) STATE.obs.close();

  try {
    STATE.obs = new WebSocket(`ws://${ip}:${port}`);
  } catch(e) {
    toast('WebSocket invalide : ' + e.message, 'error');
    return;
  }

  STATE.obs.onopen = () => {
    STATE.obsRetries = 0;
  };

  STATE.obs.onmessage = async (evt) => {
    let msg;
    try { msg = JSON.parse(evt.data); } catch { return; }

    // op:0 = Hello (auth challenge)
    if (msg.op === 0) {
      if (msg.d.authentication) {
        await obsAuthenticate(msg.d.authentication);
      } else {
        obsSend({ op: 1, d: { rpcVersion: 1, eventSubscriptions: 127 } });
      }
    }

    // op:2 = Identified (auth OK)
    if (msg.op === 2) {
      STATE.obsConnected = true;
      document.getElementById('obs-status').className = 'status-badge connected';
      document.getElementById('obs-status').textContent = '🟢 CONNECTÉ';
      setDisabled(['btn-stream','btn-record','btn-mute','btn-snap','btn-pause-rec','btn-replay'], false);
      document.querySelectorAll('.sound-btn').forEach(b => b.disabled = false);
      toast('OBS connecté !', 'success');
      obsRequest('GetSceneList', {}, 'scene_list');
      obsRequest('GetInputList', {}, 'input_list');
      obsRequest('GetStreamStatus', {}, 'stream_status');
      obsRequest('GetRecordStatus', {}, 'record_status');
      ensureSoundboardSource();
    }

    // op:5 = Event
    if (msg.op === 5) {
      handleObsEvent(msg.d);
    }

    // op:7 = RequestResponse
    if (msg.op === 7) {
      handleObsResponse(msg.d);
    }
  };

  STATE.obs.onerror = () => {
    toast('Erreur WebSocket OBS', 'error');
  };

  STATE.obs.onclose = () => {
    STATE.obsConnected = false;
    document.getElementById('obs-status').className = 'status-badge';
    document.getElementById('obs-status').textContent = '🔴 DÉCONNECTÉ';
    setDisabled(['btn-stream','btn-record','btn-mute','btn-snap','btn-pause-rec','btn-replay'], true);
    document.querySelectorAll('.sound-btn').forEach(b => b.disabled = true);
    stopTimer('stream'); stopTimer('record');

    // Reconnexion automatique avec backoff
    if (STATE.obsRetries < STATE.obsMaxRetries) {
      const delay = Math.min(3000 * Math.pow(2, STATE.obsRetries), 30000);
      STATE.obsRetries++;
      toast(`Reconnexion dans ${Math.round(delay/1000)}s… (${STATE.obsRetries}/${STATE.obsMaxRetries})`, 'warn');
      clearTimeout(_obsReconnectTimer);
      _obsReconnectTimer = setTimeout(obsConnect, delay);
    } else {
      toast('Reconnexion impossible. Vérifiez OBS.', 'error');
    }
  };
}

/* ── GESTION ÉVÉNEMENTS OBS ── */
function handleObsEvent(d) {
  switch (d.eventType) {

    case 'StreamStateChanged':
      STATE.streaming = d.eventData.outputActive;
      const sBtn = document.getElementById('btn-stream');
      const sLed = document.getElementById('led-stream');
      sLed.className = 'led' + (STATE.streaming ? ' on' : '');
      sBtn.className = 'hw-btn' + (STATE.streaming ? ' active-stream' : '');
      document.getElementById('lbl-stream').textContent = STATE.streaming ? 'STOP STR' : 'STREAM';
      if (STATE.streaming) startTimer('stream'); else stopTimer('stream');
      break;

    case 'RecordStateChanged':
      STATE.recording = d.eventData.outputActive;
      const rBtn = document.getElementById('btn-record');
      const rLed = document.getElementById('led-record');
      rLed.className = 'led' + (STATE.recording ? ' on-red' : '');
      rBtn.className = 'hw-btn' + (STATE.recording ? ' active-record' : '');
      document.getElementById('lbl-record').textContent = STATE.recording ? 'STOP REC' : 'RECORD';
      if (STATE.recording) startTimer('record'); else stopTimer('record');
      break;

    case 'InputMuteStateChanged':
      if (d.eventData.inputName === STATE.currentSource) {
        STATE.muted = d.eventData.inputMuted;
        document.getElementById('led-mute').className = 'led' + (STATE.muted ? ' on-yellow' : '');
        document.getElementById('btn-mute').className = 'hw-btn' + (STATE.muted ? ' active-mute' : '');
      }
      break;

    case 'CurrentProgramSceneChanged':
      STATE.currentScene = d.eventData.sceneName;
      updateSceneHighlight();
      break;

    case 'SceneListChanged':
      obsRequest('GetSceneList', {}, 'scene_list');
      break;
  }
}

/* ── GESTION RÉPONSES OBS ── */
function handleObsResponse(d) {
  if (!d.requestStatus?.result) return;
  const rd = d.responseData;

  switch (d.requestId?.split('_')[0]) {

    case 'scene':
      if (rd?.scenes) buildSceneGrid(rd.scenes, rd.currentProgramSceneName);
      break;

    case 'input':
      if (rd?.inputs) buildAudioSources(rd.inputs);
      break;

    case 'stream':
      STATE.streaming = rd?.outputActive;
      if (STATE.streaming) startTimer('stream');
      break;

    case 'record':
      STATE.recording = rd?.outputActive;
      if (STATE.recording) startTimer('record');
      break;
  }
}

/* ── BUILD SCÈNES ── */
function buildSceneGrid(scenes, currentScene) {
  const grid = document.getElementById('scene-grid');
  grid.innerHTML = '';
  STATE.currentScene = currentScene;

  scenes.slice().reverse().forEach(s => {
    const btn = document.createElement('button');
    btn.className = 'hw-btn' + (s.sceneName === currentScene ? ' active-scene' : '');
    btn.dataset.scene = s.sceneName;

    const icon = document.createElement('span');
    icon.className = 'icon';
    icon.textContent = '🎬';

    const lbl = document.createElement('small');
    lbl.textContent = s.sceneName.substring(0, 12);

    btn.appendChild(icon);
    btn.appendChild(lbl);
    btn.addEventListener('click', () => obsRequest('SetCurrentProgramScene', { sceneName: s.sceneName }));
    grid.appendChild(btn);
  });
}

function updateSceneHighlight() {
  document.querySelectorAll('#scene-grid .hw-btn').forEach(b => {
    b.className = 'hw-btn' + (b.dataset.scene === STATE.currentScene ? ' active-scene' : '');
  });
}

/* ── BUILD SOURCES AUDIO ── */
function buildAudioSources(inputs) {
  const sel = document.getElementById('audioSources');
  const audioKinds = ['wasapi_input_capture','wasapi_output_capture','pulse_input_capture','pulse_output_capture','alsa_input_capture','coreaudio_input_capture','coreaudio_output_capture'];
  sel.innerHTML = '<option value="">— Source audio —</option>';
  inputs.filter(i => audioKinds.includes(i.inputKind)).forEach(i => {
    const opt = document.createElement('option');
    opt.value = i.inputName;
    opt.textContent = i.inputName;
    sel.appendChild(opt);
  });
}

/* ── CONTRÔLES OBS ── */
function toggleStream() {
  if (!STATE.obsConnected) return;
  obsRequest(STATE.streaming ? 'StopStream' : 'StartStream');
}

function toggleRecord() {
  if (!STATE.obsConnected) return;
  obsRequest(STATE.recording ? 'StopRecord' : 'StartRecord');
}

function pauseRecord() {
  if (!STATE.obsConnected) return;
  obsRequest(STATE.recordPaused ? 'ResumeRecord' : 'PauseRecord');
  STATE.recordPaused = !STATE.recordPaused;
  toast(STATE.recordPaused ? 'Enregistrement en pause' : 'Enregistrement repris', 'info');
}

function toggleMute() {
  if (!STATE.obsConnected || !STATE.currentSource) {
    toast('Sélectionnez une source audio d\'abord', 'warn');
    return;
  }
  obsRequest('ToggleInputMute', { inputName: STATE.currentSource });
}

function takeScreenshot() {
  if (!STATE.obsConnected) return;
  if (!STATE.currentScene) { toast('Aucune scène active', 'warn'); return; }
  obsRequest('SaveSourceScreenshot', {
    sourceName:    STATE.currentScene,
    imageFormat:   'png',
    imageFilePath: 'C:/OBS_Screenshots/snap_' + Date.now() + '.png',
  });
  toast('📸 Screenshot sauvegardé', 'success');
}

function replayBuffer() {
  if (!STATE.obsConnected) return;
  obsRequest('SaveReplayBuffer');
  toast('🔁 Replay buffer sauvegardé', 'success');
}

function setAudioSource(name) {
  STATE.currentSource = name || null;
  startVuMeter();
}

function handleVolume(v) {
  document.getElementById('vol-value').textContent = v + '%';
  document.getElementById('volSlider').style.setProperty('--pct', v + '%');
  if (STATE.obsConnected && STATE.currentSource) {
    obsRequest('SetInputVolume', { inputName: STATE.currentSource, inputVolumeMul: v / 100 });
  }
}

/* ── VU METER (simulé côté UI) ── */
function startVuMeter() {
  clearInterval(STATE.vuTimer);
  const bars = document.querySelectorAll('.vu-bar');
  if (!STATE.obsConnected || !STATE.currentSource) {
    bars.forEach(b => { b.style.height = '3px'; b.style.background = 'var(--input-border)'; });
    return;
  }
  STATE.vuTimer = setInterval(() => {
    const vol = parseInt(document.getElementById('volSlider').value);
    bars.forEach((b, i) => {
      const level = Math.random() * vol / 100;
      const h = Math.max(2, Math.floor(level * 20)) + 'px';
      const pct = i / bars.length;
      b.style.height = h;
      b.style.background = pct < 0.6 ? 'var(--success)' : pct < 0.85 ? 'var(--warning)' : 'var(--danger)';
    });
  }, 80);
}

/* ══════════════════════════════════════════════════════════════
   SOUNDBOARD VIA SOURCE MEDIA OBS
══════════════════════════════════════════════════════════════ */
const SOUND_SOURCE = 'BL_Soundboard';

const SOUND_FILES = {
  applause:  'applause.mp3',
  laugh:     'laugh.mp3',
  airhorn:   'airhorn.mp3',
  alert:     'alert.mp3',
  explosion: 'explosion.mp3',
  guitar:    'guitar.mp3',
  drum:      'drum.mp3',
  win:       'win.mp3',
  fail:      'fail.mp3',
};

/* Vérifie ou crée la source BL_Soundboard dans OBS */
function ensureSoundboardSource() {
  obsRequest('GetInputSettings', { inputName: SOUND_SOURCE }, 'soundboard_check');
  // Si la requête échoue (source inexistante), on la crée
  STATE.obs.addEventListener('message', function onSbCheck(evt) {
    const msg = JSON.parse(evt.data);
    if (msg.op === 7 && msg.d.requestId === 'soundboard_check') {
      STATE.obs.removeEventListener('message', onSbCheck);
      if (!msg.d.requestStatus?.result) {
        // Créer la source
        obsRequest('CreateInput', {
          sceneName:   STATE.currentScene || '',
          inputName:   SOUND_SOURCE,
          inputKind:   'ffmpeg_source',
          inputSettings: { local_file: '', is_local_file: true, looping: false },
          sceneItemEnabled: false,
        }, 'soundboard_create');
        toast('Source OBS BL_Soundboard créée', 'success');
      }
    }
  });
}

function playObsSound(soundId) {
  if (!STATE.obsConnected) { toast('Non connecté à OBS', 'error'); return; }

  const basePath = document.getElementById('sound-path').value.trim() || '/sounds/';
  const file     = basePath.replace(/\/+$/, '/') + SOUND_FILES[soundId];

  // 1. Mettre à jour le fichier de la source
  obsRequest('SetInputSettings', {
    inputName: SOUND_SOURCE,
    inputSettings: { local_file: file, is_local_file: true, looping: false },
  }, 'sound_set_' + soundId);

  // 2. Redémarrer la lecture
  setTimeout(() => {
    obsRequest('TriggerMediaInputAction', {
      inputName:   SOUND_SOURCE,
      mediaAction: 'OBS_WEBSOCKET_MEDIA_INPUT_ACTION_RESTART',
    }, 'sound_play_' + soundId);
  }, 80);

  // Feedback visuel glow
  const btn = document.querySelector(`.sound-btn[data-sound="${soundId}"]`);
  if (btn) {
    btn.classList.add('playing');
    setTimeout(() => btn.classList.remove('playing'), 1200);
  }

  toast('🔊 ' + soundId.toUpperCase(), 'info');
}

/* ══════════════════════════════════════════════════════════════
   CHAT — SÉLECTION SERVICE
══════════════════════════════════════════════════════════════ */
function selectChatService(service) {
  STATE.chatService = service;
  document.getElementById('auth-twitch').style.display  = service === 'twitch'  ? '' : 'none';
  document.getElementById('auth-youtube').style.display = service === 'youtube' ? '' : 'none';
  document.querySelectorAll('.chat-tab').forEach(t => t.classList.remove('active'));
  document.getElementById('tab-' + service).classList.add('active');
}

/* ══════════════════════════════════════════════════════════════
   TWITCH IRC WEBSOCKET
══════════════════════════════════════════════════════════════ */
function connectTwitch() {
  const channel = document.getElementById('twitch-channel').value.trim().toLowerCase();
  const token   = document.getElementById('twitch-token').value.trim();

  if (!channel) { toast('Entrez votre pseudo Twitch', 'error'); return; }
  if (!token.startsWith('oauth:')) { toast('Le token doit commencer par oauth:', 'error'); return; }

  if (STATE.twitchWs) STATE.twitchWs.close();

  STATE.twitchWs = new WebSocket('wss://irc-ws.chat.twitch.tv:443');

  STATE.twitchWs.onopen = () => {
    STATE.twitchWs.send('CAP REQ :twitch.tv/tags twitch.tv/commands');
    STATE.twitchWs.send(`PASS ${token}`);
    STATE.twitchWs.send(`NICK ${channel}`);
    STATE.twitchWs.send(`JOIN #${channel}`);
  };

  STATE.twitchWs.onmessage = (evt) => {
    const lines = evt.data.split('\r\n');
    lines.forEach(line => {
      if (line.startsWith('PING')) {
        STATE.twitchWs.send('PONG :tmi.twitch.tv');
        return;
      }

      // Parser les tags Twitch IRC
      const tagMatch = line.match(/^@([^\s]+)\s:([^!]+)![^\s]+\sPRIVMSG\s#\w+\s:(.+)$/);
      if (tagMatch) {
        const tags    = Object.fromEntries(tagMatch[1].split(';').map(t => t.split('=')));
        const author  = tags['display-name'] || tagMatch[2];
        const text    = tagMatch[3];
        const color   = tags['color'] || chatColor(author);
        addChatMessage('twitch', author, text, color);
        return;
      }

      // Connexion confirmée
      if (line.includes(':End of /NAMES list')) {
        STATE.twitchConnected = true;
        document.getElementById('tab-twitch').className = 'chat-tab active connected-tab';
        toast('💜 Twitch connecté — #' + channel, 'success');
      }
    });
  };

  STATE.twitchWs.onerror = () => toast('Erreur Twitch IRC', 'error');
  STATE.twitchWs.onclose = () => {
    STATE.twitchConnected = false;
    document.getElementById('tab-twitch').className = 'chat-tab active';
    toast('💜 Twitch déconnecté', 'warn');
  };
}

/* ══════════════════════════════════════════════════════════════
   YOUTUBE LIVE CHAT — OAuth 2.0 + Polling
══════════════════════════════════════════════════════════════ */
function startYouTubeOAuth() {
  const clientId  = document.getElementById('yt-client-id').value.trim();
  const videoId   = document.getElementById('yt-video-id').value.trim();

  if (!clientId) { toast('Entrez votre Client ID Google', 'error'); return; }
  if (!videoId)  { toast('Entrez l\'ID de votre vidéo YouTube Live', 'error'); return; }

  STATE.ytVideoId = videoId;

  const redirectUri = encodeURIComponent(window.location.origin + window.location.pathname);
  const scope       = encodeURIComponent('https://www.googleapis.com/auth/youtube.readonly');
  const oauthUrl    = `https://accounts.google.com/o/oauth2/v2/auth?client_id=${encodeURIComponent(clientId)}&redirect_uri=${redirectUri}&response_type=token&scope=${scope}&access_type=online`;

  // Ouvrir dans un popup
  const popup = window.open(oauthUrl, 'yt_oauth', 'width=500,height=600,scrollbars=yes');

  // Écouter le retour du token (fragment URL)
  const checkPopup = setInterval(() => {
    try {
      if (popup.closed) {
        clearInterval(checkPopup);
        return;
      }
      const hash = popup.location.hash;
      if (hash && hash.includes('access_token')) {
        const params = new URLSearchParams(hash.substring(1));
        const token  = params.get('access_token');
        if (token) {
          popup.close();
          clearInterval(checkPopup);
          STATE.ytToken = token;
          document.getElementById('yt-access-token').value = token;
          document.getElementById('yt-token-zone').style.display = '';
          toast('🔴 Token YouTube obtenu !', 'success');
        }
      }
    } catch(e) { /* Cross-origin, attendre */ }
  }, 500);
}

function connectYouTube() {
  const token   = document.getElementById('yt-access-token').value.trim() || STATE.ytToken;
  const videoId = document.getElementById('yt-video-id').value.trim()     || STATE.ytVideoId;

  if (!token || !videoId) { toast('Token et ID vidéo requis', 'error'); return; }

  STATE.ytToken   = token;
  STATE.ytVideoId = videoId;

  // Récupérer le liveChatId depuis la vidéo
  fetch(`https://www.googleapis.com/youtube/v3/videos?part=liveStreamingDetails&id=${encodeURIComponent(videoId)}&access_token=${encodeURIComponent(token)}`)
    .then(r => r.json())
    .then(data => {
      const chatId = data?.items?.[0]?.liveStreamingDetails?.activeLiveChatId;
      if (!chatId) { toast('Live Chat ID introuvable — êtes-vous en live ?', 'error'); return; }

      STATE.ytConnected = true;
      STATE.ytNextToken = null;
      document.getElementById('tab-youtube').className = 'chat-tab connected-tab';
      toast('🔴 YouTube Live connecté !', 'success');

      // Polling toutes les 5 secondes
      clearInterval(STATE.ytInterval);
      pollYouTubeChat(chatId);
      STATE.ytInterval = setInterval(() => pollYouTubeChat(chatId), 5000);
    })
    .catch(() => toast('Erreur API YouTube', 'error'));
}

function pollYouTubeChat(chatId) {
  let url = `https://www.googleapis.com/youtube/v3/liveChat/messages?liveChatId=${encodeURIComponent(chatId)}&part=snippet,authorDetails&access_token=${encodeURIComponent(STATE.ytToken)}&maxResults=50`;
  if (STATE.ytNextToken) url += `&pageToken=${encodeURIComponent(STATE.ytNextToken)}`;

  fetch(url)
    .then(r => r.json())
    .then(data => {
      if (data.error) { toast('Erreur YouTube API : ' + data.error.message, 'error'); return; }
      STATE.ytNextToken = data.nextPageToken;
      (data.items || []).forEach(item => {
        const author = item.authorDetails?.displayName || 'Inconnu';
        const text   = item.snippet?.displayMessage    || '';
        if (text) addChatMessage('youtube', author, text, chatColor(author));
      });
    })
    .catch(() => {});
}

/* ══════════════════════════════════════════════════════════════
   AFFICHAGE MESSAGES CHAT
══════════════════════════════════════════════════════════════ */
function addChatMessage(service, author, text, color) {
  const container = document.getElementById('chat-messages');

  // Retirer l'empty state si présent
  const empty = container.querySelector('.chat-empty');
  if (empty) empty.remove();

  const msg = document.createElement('div');
  msg.className = 'chat-msg';

  const badge = document.createElement('span');
  badge.className = 'service-badge ' + service;
  badge.textContent = service === 'twitch' ? 'TW' : 'YT';

  const authorEl = document.createElement('span');
  authorEl.className = 'chat-msg-author';
  authorEl.style.color = color;
  authorEl.textContent = author + ':';

  const textEl = document.createElement('span');
  textEl.className = 'chat-msg-text';
  textEl.textContent = ' ' + text; // textContent = protection XSS

  const timeEl = document.createElement('span');
  timeEl.className = 'chat-msg-time';
  timeEl.textContent = new Date().toLocaleTimeString('fr-FR', {hour:'2-digit',minute:'2-digit'});

  msg.appendChild(badge);
  msg.appendChild(authorEl);
  msg.appendChild(textEl);
  msg.appendChild(timeEl);
  container.appendChild(msg);

  // Limiter à 200 messages
  const msgs = container.querySelectorAll('.chat-msg');
  if (msgs.length > 200) msgs[0].remove();

  // Auto-scroll
  container.scrollTop = container.scrollHeight;
}

/* ══════════════════════════════════════════════════════════════
   RACCOURCIS CLAVIER
══════════════════════════════════════════════════════════════ */
document.addEventListener('keydown', (e) => {
  // Ne pas déclencher si l'utilisateur est dans un input
  if (['INPUT','TEXTAREA','SELECT'].includes(e.target.tagName)) return;

  switch (e.key) {
    case 'F1': e.preventDefault(); toggleMute();       break;
    case 'F2': e.preventDefault(); toggleStream();     break;
    case 'F3': e.preventDefault(); toggleRecord();     break;
    case 'F4': e.preventDefault(); takeScreenshot();   break;
    case 'F5': e.preventDefault(); pauseRecord();      break;
  }
});

/* ══════════════════════════════════════════════════════════════
   INIT
══════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  // Check OAuth YouTube callback dans l'URL
  if (window.location.hash.includes('access_token')) {
    const params = new URLSearchParams(window.location.hash.substring(1));
    const token  = params.get('access_token');
    if (token) {
      STATE.ytToken = token;
      document.getElementById('yt-access-token').value = token;
      document.getElementById('yt-token-zone').style.display = '';
      document.getElementById('auth-youtube').style.display = '';
      selectChatService('youtube');
      toast('🔴 Token YouTube récupéré', 'success');
      history.replaceState(null, '', window.location.pathname);
    }
  }
  startVuMeter();
});
</script>

