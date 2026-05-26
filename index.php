<?php
// ── Database config ──────────────────────────────────────────────────────────
$host   = 'localhost';
$dbname = 'myapp';
$user   = 'myapp_user';
$pass   = 'MyApp123!';

// ── Logic ────────────────────────────────────────────────────────────────────
$db_ok   = false;
$db_msg  = '';
$count   = 0;
$elapsed = null;

$t0 = microtime(true);
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $elapsed = round((microtime(true) - $t0) * 1000, 1);

    $pdo->exec("CREATE TABLE IF NOT EXISTS visites (
        id   INT AUTO_INCREMENT PRIMARY KEY,
        date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    $pdo->exec("INSERT INTO visites () VALUES ()");

    $count  = (int) $pdo->query("SELECT COUNT(*) FROM visites")->fetchColumn();
    $db_ok  = true;
    $db_msg = "Connexion établie en {$elapsed} ms";
} catch (PDOException $e) {
    $db_msg = htmlspecialchars($e->getMessage());
}

$now = date('d/m/Y à H:i:s');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Plateforme DevOps · Ansible</title>
<style>
/* ── Reset & tokens ─────────────────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --bg:          #060b14;
  --bg2:         #0d1526;
  --surface:     #111827;
  --surface2:    #1a2236;
  --border:      #1e2d45;
  --border-hi:   #2a4070;
  --blue:        #3b82f6;
  --blue-hi:     #60a5fa;
  --blue-glow:   rgba(59,130,246,.25);
  --green:       #10b981;
  --green-glow:  rgba(16,185,129,.2);
  --red:         #ef4444;
  --red-glow:    rgba(239,68,68,.2);
  --text:        #f0f6ff;
  --text-muted:  #64748b;
  --text-sub:    #94a3b8;
  --radius-sm:   8px;
  --radius:      14px;
  --radius-lg:   20px;
}

/* ── Body & background ──────────────────────────────────────────────────── */
body {
  font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
  background: var(--bg);
  color: var(--text);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 2.5rem 1rem 3rem;
  position: relative;
  overflow-x: hidden;
}

/* Radial glow behind header */
body::before {
  content: '';
  position: fixed;
  top: -120px; left: 50%;
  transform: translateX(-50%);
  width: 700px; height: 500px;
  background: radial-gradient(ellipse at center, rgba(59,130,246,.12) 0%, transparent 70%);
  pointer-events: none;
  z-index: 0;
}

/* ── Layout wrapper ─────────────────────────────────────────────────────── */
.wrapper {
  position: relative;
  z-index: 1;
  width: 100%;
  max-width: 900px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2rem;
}

/* ── Top bar ────────────────────────────────────────────────────────────── */
.topbar {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

.topbar-logo {
  display: flex;
  align-items: center;
  gap: .6rem;
  font-size: .8rem;
  font-weight: 700;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--text-muted);
}

.topbar-logo .dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: var(--blue);
  box-shadow: 0 0 8px var(--blue);
  animation: blink 2s infinite;
}

@keyframes blink {
  0%,100% { opacity: 1; }
  50%      { opacity: .3; }
}

.topbar-meta {
  font-size: .78rem;
  color: var(--text-muted);
}

/* ── Hero header ────────────────────────────────────────────────────────── */
.hero {
  text-align: center;
  padding: .5rem 0;
}

.hero-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  background: linear-gradient(135deg, rgba(59,130,246,.15), rgba(59,130,246,.05));
  border: 1px solid rgba(59,130,246,.3);
  border-radius: 999px;
  padding: .35rem 1rem;
  font-size: .72rem;
  font-weight: 700;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--blue-hi);
  margin-bottom: 1.2rem;
}

h1 {
  font-size: clamp(1.7rem, 5vw, 2.8rem);
  font-weight: 800;
  line-height: 1.15;
  letter-spacing: -.02em;
  margin-bottom: .75rem;
  background: linear-gradient(160deg, #fff 0%, var(--blue-hi) 60%, var(--blue) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero-sub {
  font-size: 1rem;
  color: var(--text-sub);
  max-width: 500px;
  margin: 0 auto;
  line-height: 1.6;
}

/* ── Status banner ──────────────────────────────────────────────────────── */
.status-banner {
  width: 100%;
  border-radius: var(--radius);
  padding: 1rem 1.4rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  font-size: .9rem;
  border: 1px solid;
  backdrop-filter: blur(4px);
}

.status-banner.ok {
  background: linear-gradient(135deg, rgba(16,185,129,.08), rgba(16,185,129,.03));
  border-color: rgba(16,185,129,.3);
}

.status-banner.fail {
  background: linear-gradient(135deg, rgba(239,68,68,.08), rgba(239,68,68,.03));
  border-color: rgba(239,68,68,.3);
}

.status-icon {
  font-size: 1.4rem;
  flex-shrink: 0;
}

.status-text strong {
  display: block;
  font-size: .85rem;
  font-weight: 700;
  margin-bottom: .15rem;
}

.status-banner.ok  .status-text strong { color: var(--green); }
.status-banner.fail .status-text strong { color: var(--red); }

.status-text span { color: var(--text-muted); font-size: .8rem; }

.status-ping {
  margin-left: auto;
  font-size: .72rem;
  font-weight: 600;
  letter-spacing: .06em;
  padding: .3rem .7rem;
  border-radius: 999px;
  flex-shrink: 0;
}

.status-banner.ok   .status-ping { background: var(--green-glow); color: var(--green); }
.status-banner.fail .status-ping { background: var(--red-glow);   color: var(--red); }

/* ── Cards grid ─────────────────────────────────────────────────────────── */
.grid {
  width: 100%;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
  gap: 1rem;
}

.card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 1.4rem 1.5rem;
  display: flex;
  flex-direction: column;
  gap: .4rem;
  position: relative;
  overflow: hidden;
  transition: border-color .25s, transform .25s, box-shadow .25s;
}

.card::after {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 2px;
  border-radius: var(--radius) var(--radius) 0 0;
  background: linear-gradient(90deg, var(--blue), transparent);
  opacity: 0;
  transition: opacity .25s;
}

.card:hover {
  border-color: var(--border-hi);
  transform: translateY(-3px);
  box-shadow: 0 12px 32px rgba(0,0,0,.4), 0 0 0 1px var(--border-hi);
}

.card:hover::after { opacity: 1; }

.card-label {
  font-size: .7rem;
  font-weight: 700;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--text-muted);
}

.card-value {
  font-size: 2rem;
  font-weight: 800;
  color: var(--text);
  letter-spacing: -.03em;
  line-height: 1;
}

.card-value.blue  { color: var(--blue-hi); }
.card-value.green { color: var(--green); }
.card-value.sm    { font-size: 1rem; font-weight: 600; letter-spacing: -.01em; }

.card-desc {
  font-size: .78rem;
  color: var(--text-muted);
  margin-top: .1rem;
}

.card-icon {
  font-size: 1.5rem;
  margin-bottom: .2rem;
}

/* Visits card highlight */
.card.highlight {
  border-color: rgba(59,130,246,.3);
  background: linear-gradient(145deg, #111827, #0f1d35);
}

.card.highlight::after { opacity: 1; }

/* ── Stack bar ──────────────────────────────────────────────────────────── */
.stack-bar {
  width: 100%;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 1.1rem 1.5rem;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: .5rem .7rem;
}

.stack-title {
  font-size: .7rem;
  font-weight: 700;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--text-muted);
  margin-right: .4rem;
  flex-shrink: 0;
}

.tag {
  display: inline-flex;
  align-items: center;
  gap: .35rem;
  background: rgba(255,255,255,.04);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  padding: .3rem .75rem;
  font-size: .78rem;
  font-weight: 500;
  color: var(--text-sub);
  transition: border-color .2s, color .2s;
}

.tag:hover { border-color: var(--blue); color: var(--blue-hi); }

.tag .label { color: var(--text-muted); font-size: .65rem; margin-right: .1rem; }

/* ── Divider ────────────────────────────────────────────────────────────── */
.divider {
  width: 100%;
  height: 1px;
  background: linear-gradient(90deg, transparent, var(--border), transparent);
}

/* ── Footer ─────────────────────────────────────────────────────────────── */
footer {
  text-align: center;
  color: var(--text-muted);
  font-size: .78rem;
  line-height: 2;
}

footer .footer-main {
  color: var(--text-sub);
  font-weight: 500;
  margin-bottom: .2rem;
}

footer .footer-stack {
  color: var(--text-muted);
  font-size: .72rem;
}

footer .sep { margin: 0 .4rem; opacity: .4; }

/* ── Responsive ─────────────────────────────────────────────────────────── */
@media (max-width: 600px) {
  body { padding: 1.5rem .75rem 2.5rem; }
  .grid { grid-template-columns: 1fr 1fr; }
  .topbar-meta { display: none; }
  h1 { font-size: 1.6rem; }
}

@media (max-width: 400px) {
  .grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>
<div class="wrapper">

  <!-- Top bar -->
  <nav class="topbar">
    <div class="topbar-logo">
      <div class="dot">TP2</div>
      DevOps Platform
    </div>
    <div class="topbar-meta">Mise à jour : <?= $now ?></div>
  </nav>

  <!-- Hero -->
  <header class="hero">
    <div class="hero-eyebrow">
      &#9679; Master 2 &mdash; Génie Logiciel
    </div>
    <h1>🚀 Plateforme DevOps<br>Déployée avec Ansible</h1>
    <p class="hero-sub">
      Infrastructure as Code &mdash; déploiement automatisé,
      stack LEMP provisionnée via Ansible&nbsp;&amp;&nbsp;Git.
    </p>
  </header>

  <!-- DB status banner -->
  <?php if ($db_ok): ?>
  <div class="status-banner ok">
    <div class="status-icon">✅</div>
    <div class="status-text">
      <strong>Base de données connectée</strong>
      <span><?= $db_msg ?> &mdash; MariaDB &middot; <?= htmlspecialchars($dbname) ?>@<?= htmlspecialchars($host) ?></span>
    </div>
    <div class="status-ping">LIVE</div>
  </div>
  <?php else: ?>
  <div class="status-banner fail">
    <div class="status-icon">❌</div>
    <div class="status-text">
      <strong>Connexion échouée</strong>
      <span><?= $db_msg ?></span>
    </div>
    <div class="status-ping">DOWN</div>
  </div>
  <?php endif; ?>

  <!-- Metrics grid -->
  <div class="grid">

    <div class="card highlight">
      <div class="card-icon">👁️</div>
      <div class="card-label">Visites totales</div>
      <div class="card-value blue"><?= $db_ok ? number_format($count, 0, ',', '&nbsp;') : '—' ?></div>
      <div class="card-desc">Depuis le déploiement</div>
    </div>

    <div class="card">
      <div class="card-icon">⚡</div>
      <div class="card-label">Latence DB</div>
      <div class="card-value sm"><?= $db_ok ? $elapsed . ' ms' : 'N/A' ?></div>
      <div class="card-desc">Temps de connexion PDO</div>
    </div>

    <div class="card">
      <div class="card-icon">🗄️</div>
      <div class="card-label">Base de données</div>
      <div class="card-value sm green"><?= $db_ok ? 'MariaDB' : 'Hors ligne' ?></div>
      <div class="card-desc"><?= htmlspecialchars($dbname) ?>@<?= htmlspecialchars($host) ?></div>
    </div>

    <div class="card">
      <div class="card-icon">📦</div>
      <div class="card-label">Déploiement</div>
      <div class="card-value sm">Ansible</div>
      <div class="card-desc">Provisionné via Git</div>
    </div>

  </div>

  <!-- Stack tags -->
  <div class="stack-bar">
    <span class="stack-title">Stack</span>
    <span class="tag"><span class="label">OS</span> Linux</span>
    <span class="tag"><span class="label">Web</span> Nginx</span>
    <span class="tag"><span class="label">Lang</span> PHP 8</span>
    <span class="tag"><span class="label">DB</span> MariaDB</span>
    <span class="tag"><span class="label">IaC</span> Ansible</span>
    <span class="tag"><span class="label">VCS</span> Git</span>
  </div>

  <div class="divider"></div>

  <!-- Footer -->
  <footer>
    <div class="footer-main">
      Déployé automatiquement via Ansible + Git
    </div>
    <div class="footer-stack">
      Stack&nbsp;: Nginx + PHP + MariaDB
      <span class="sep">|</span>
      Master 2 Génie Logiciel
      <span class="sep">|</span>
      <?= date('Y') ?>
    </div>
  </footer>

</div><!-- /wrapper -->
</body>
</html>
