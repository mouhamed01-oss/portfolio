<?php
// reset_admin.php — Réinitialisation du mot de passe admin
// ⚠️ SUPPRIMER CE FICHIER APRÈS USAGE !
require_once 'config.php';
$pdo = db();
$msg = '';
$ok  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u  = trim($_POST['username'] ?? '');
    $p  = trim($_POST['password'] ?? '');
    $p2 = trim($_POST['confirm']  ?? '');
    if (!$u || !$p) { $msg = 'Champs obligatoires.'; }
    elseif (strlen($p) < 6) { $msg = 'Mot de passe trop court (min 6 caractères).'; }
    elseif ($p !== $p2) { $msg = 'Les mots de passe ne correspondent pas.'; }
    else {
        $hash = password_hash($p, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE admins SET password=? WHERE username=?");
        $stmt->execute([$hash, $u]);
        if ($stmt->rowCount() > 0) { $ok = true; $msg = 'Mot de passe mis à jour avec succès !'; }
        else { $msg = "Utilisateur « $u » introuvable."; }
    }
}
$admins = $pdo->query("SELECT id, username, LENGTH(password) as pl, CASE WHEN password LIKE '\$2y\$%' THEN 1 ELSE 0 END as is_hash FROM admins")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Reset Admin Password</title>
<style>
body { font-family:monospace; background:#0a0a0a; color:#f0ede6; padding:40px; max-width:480px; margin:0 auto; }
h2 { color:#c8f135; margin-bottom:20px; }
.warn { background:rgba(255,165,0,0.15); border:1px solid orange; color:orange; padding:12px; margin-bottom:20px; font-size:0.82rem; }
.ok   { background:rgba(200,241,53,0.1); border:1px solid #c8f135; color:#c8f135; padding:12px; margin-bottom:16px; }
.err  { background:rgba(255,80,80,0.1); border:1px solid #ff6464; color:#ff6464; padding:12px; margin-bottom:16px; }
input { display:block; width:100%; background:#181818; border:1px solid rgba(255,255,255,0.1); color:#f0ede6; padding:10px; margin-bottom:14px; font-family:monospace; font-size:0.85rem; outline:none; }
input:focus { border-color:#c8f135; }
label { font-size:0.65rem; letter-spacing:0.12em; color:#888; display:block; margin-bottom:6px; }
button { background:#c8f135; color:#0a0a0a; font-weight:bold; border:none; padding:12px 24px; width:100%; cursor:pointer; font-size:0.82rem; letter-spacing:0.1em; }
table { width:100%; border-collapse:collapse; margin-bottom:24px; font-size:0.78rem; }
th,td { padding:8px; border-bottom:1px solid rgba(255,255,255,0.07); text-align:left; }
th { color:#888; font-size:0.65rem; letter-spacing:0.12em; }
a { color:#888; font-size:0.72rem; }
</style>
</head>
<body>
<h2>🔑 Reset Mot de Passe Admin</h2>
<div class="warn">⚠️ Supprimez ce fichier immédiatement après utilisation !</div>

<h3 style="font-size:0.8rem;color:#888;letter-spacing:0.1em;margin-bottom:10px">ADMINISTRATEURS EN BASE</h3>
<table>
  <tr><th>ID</th><th>USERNAME</th><th>HASH (longueur)</th><th>TYPE</th></tr>
  <?php foreach($admins as $a): ?>
  <tr>
    <td><?= $a['id'] ?></td>
    <td><b style="color:#f0ede6"><?= h($a['username']) ?></b></td>
    <td><?= $a['pl'] ?> car.</td>
    <td><?= $a['is_hash'] ? '<span style="color:#c8f135">bcrypt ✓</span>' : '<span style="color:#ff6464">texte clair ✗</span>' ?></td>
  </tr>
  <?php endforeach; ?>
</table>

<?php if($msg): ?>
<div class="<?= $ok?'ok':'err' ?>"><?= $ok?'✓':'✕' ?> <?= h($msg) ?></div>
<?php endif; ?>

<?php if(!$ok): ?>
<form method="POST">
  <div><label>USERNAME</label><input name="username" required placeholder="admin" value="<?= h($_POST['username']??'admin') ?>"></div>
  <div><label>NOUVEAU MOT DE PASSE</label><input name="password" type="password" required placeholder="Min. 6 caractères"></div>
  <div><label>CONFIRMER</label><input name="confirm" type="password" required placeholder="Répéter le mot de passe"></div>
  <button type="submit">METTRE À JOUR →</button>
</form>
<?php else: ?>
<a href="admin/login.php">→ Aller à la connexion admin</a>
<?php endif; ?>

<p style="margin-top:24px;font-size:0.7rem;color:#444">🗑️ N'oubliez pas de supprimer reset_admin.php !</p>
</body>
</html>
