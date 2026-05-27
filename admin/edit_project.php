<?php
require_once '../config.php';
requireAdmin();
$pdo = db();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php?tab=projects'); exit; }

$p = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$p->execute([$id]);
$p = $p->fetch();
if (!$p) { header('Location: index.php?tab=projects'); exit; }

$techArr    = json_decode($p['tech_stack'] ?? '[]', true) ?: [];
$techString = implode(', ', $techArr);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Modifier le projet</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Mono:wght@300;400&display=swap" rel="stylesheet">
<style>
:root { --bg:#0a0a0a; --bg2:#111; --bg3:#181818; --border:rgba(255,255,255,0.07); --text:#f0ede6; --text2:#888; --accent:#c8f135; }
* { box-sizing:border-box; }
body { background:var(--bg); color:var(--text); font-family:'DM Mono',monospace; font-weight:300; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:2rem; }
.card { background:var(--bg2); border:1px solid var(--border); padding:2rem; width:100%; max-width:640px; }
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
      <div style="font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:800">✏️ Modifier le projet</div>
      <div style="font-size:0.65rem;letter-spacing:0.1em;color:var(--text2);margin-top:2px"><?= h($p['title']) ?></div>
    </div>
    <a href="index.php?tab=projects" style="font-size:0.65rem;color:var(--text2)">← Retour</a>
  </div>

  <!-- Aperçu image actuelle -->
  <?php if($p['image'] && file_exists('../uploads/projects/'.$p['image'])): ?>
  <div style="margin-bottom:20px">
    <label>IMAGE ACTUELLE</label>
    <img src="../uploads/projects/<?= h($p['image']) ?>" alt="" style="width:100%;max-height:180px;object-fit:cover;border:1px solid var(--border);border-radius:2px">
  </div>
  <?php endif; ?>

  <form method="POST" action="save_project.php" enctype="multipart/form-data">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id" value="<?= $p['id'] ?>">

    <div class="form-group">
      <label>TITRE *</label>
      <input name="title" class="field" required value="<?= h($p['title']) ?>">
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div class="form-group">
        <label>CATÉGORIE</label>
        <select name="category" class="field">
          <?php foreach(['web'=>'Web','design'=>'Design','software'=>'Logiciel','other'=>'Autre'] as $v=>$l): ?>
          <option value="<?= $v ?>" <?= $p['category']===$v?'selected':'' ?>><?= $l ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>ORDRE D'AFFICHAGE</label>
        <input name="sort_order" type="number" class="field" value="<?= $p['sort_order'] ?>">
      </div>
    </div>
    <div class="form-group">
      <label>DESCRIPTION *</label>
      <textarea name="description" class="field" rows="5" required><?= h($p['description']) ?></textarea>
    </div>
    <div class="form-group">
      <label>TECHNOLOGIES (séparées par virgule)</label>
      <input name="tech_raw" class="field" value="<?= h($techString) ?>">
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div class="form-group">
        <label>LIEN LIVE</label>
        <input name="link_live" class="field" value="<?= h($p['link_live']) ?>" placeholder="https://...">
      </div>
      <div class="form-group">
        <label>LIEN CODE</label>
        <input name="link_code" class="field" value="<?= h($p['link_code']) ?>" placeholder="https://github.com/...">
      </div>
    </div>
    <div class="form-group">
      <label>NOUVELLE IMAGE (laissez vide pour conserver l'actuelle)</label>
      <input name="image" type="file" class="field" accept="image/*" style="padding:8px">
    </div>
    <div class="form-group" style="display:flex;align-items:center;gap:10px;margin-bottom:24px">
      <input type="checkbox" name="is_featured" id="feat" value="1" <?= $p['is_featured']?'checked':'' ?> style="accent-color:var(--accent);width:16px;height:16px">
      <label for="feat" style="margin-bottom:0;cursor:pointer">Mettre en avant (★ Featured)</label>
    </div>
    <button type="submit" class="btn-a">💾 ENREGISTRER LES MODIFICATIONS</button>
  </form>
</div>
</body>
</html>
