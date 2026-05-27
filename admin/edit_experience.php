<?php
require_once '../config.php';
requireAdmin();
$pdo = db();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php?tab=experiences'); exit; }

$e = $pdo->prepare("SELECT * FROM experiences WHERE id = ?");
$e->execute([$id]);
$e = $e->fetch();
if (!$e) { header('Location: index.php?tab=experiences'); exit; }

// Reconvertir JSON → liste virgule pour le textarea
$tagsArr    = json_decode($e['tags'] ?? '[]', true) ?: [];
$tagsString = implode(', ', $tagsArr);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Modifier l'expérience</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Mono:wght@300;400&display=swap" rel="stylesheet">
<style>
:root { --bg:#0a0a0a; --bg2:#111; --bg3:#181818; --border:rgba(255,255,255,0.07); --text:#f0ede6; --text2:#888; --accent:#c8f135; }
* { box-sizing:border-box; }
body { background:var(--bg); color:var(--text); font-family:'DM Mono',monospace; font-weight:300; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:2rem; }
.card { background:var(--bg2); border:1px solid var(--border); padding:2rem; width:100%; max-width:620px; }
.field { background:var(--bg3); border:1px solid var(--border); padding:10px 14px; width:100%; font-family:'DM Mono',monospace; font-size:0.82rem; color:var(--text); outline:none; transition:border-color 0.2s; border-radius:2px; }
.field:focus { border-color:var(--accent); }
.field::placeholder { color:var(--text2); }
label { font-size:0.65rem; letter-spacing:0.15em; color:var(--text2); display:block; margin-bottom:6px; }
.form-group { margin-bottom:16px; }
.btn-a { background:var(--accent); color:#0a0a0a; font-family:'Syne',sans-serif; font-weight:700; font-size:0.72rem; letter-spacing:0.1em; text-transform:uppercase; padding:12px 24px; border:none; cursor:pointer; width:100%; }
</style>
</head>
<body>
<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
    <div>
      <div style="font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:800">✏️ Modifier l'expérience</div>
      <div style="font-size:0.65rem;letter-spacing:0.1em;color:var(--text2);margin-top:2px"><?= h($e['job_title']) ?> — <?= h($e['company']) ?></div>
    </div>
    <a href="index.php?tab=experiences" style="font-size:0.65rem;color:var(--text2)">← Retour</a>
  </div>

  <form method="POST" action="save_experience.php">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id" value="<?= $e['id'] ?>">

    <div class="form-group">
      <label>TITRE DU POSTE *</label>
      <input name="job_title" class="field" required value="<?= h($e['job_title']) ?>">
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div class="form-group">
        <label>ENTREPRISE *</label>
        <input name="company" class="field" required value="<?= h($e['company']) ?>">
      </div>
      <div class="form-group">
        <label>LIEU</label>
        <input name="location" class="field" value="<?= h($e['location']) ?>" placeholder="Dakar, Sénégal">
      </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div class="form-group">
        <label>DATE DÉBUT *</label>
        <input name="date_start" type="date" class="field" required value="<?= $e['date_start'] ?>">
      </div>
      <div class="form-group">
        <label>DATE FIN (vide = poste actuel)</label>
        <input name="date_end" type="date" class="field" value="<?= $e['date_end'] ?>">
      </div>
    </div>
    <div class="form-group">
      <label>DESCRIPTION *</label>
      <textarea name="description" class="field" rows="5" required><?= h($e['description']) ?></textarea>
    </div>
    <div class="form-group">
      <label>TAGS / COMPÉTENCES (séparés par virgule)</label>
      <input name="tags_raw" class="field" value="<?= h($tagsString) ?>" placeholder="PHP, MySQL, WINDEV">
    </div>
    <div class="form-group">
      <label>ORDRE D'AFFICHAGE</label>
      <input name="sort_order" type="number" class="field" value="<?= $e['sort_order'] ?>">
    </div>
    <button type="submit" class="btn-a">💾 ENREGISTRER LES MODIFICATIONS</button>
  </form>
</div>
</body>
</html>
