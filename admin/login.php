<?php
require_once '../config.php';
if (isAdmin()) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    $admin = db()->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
    $admin->execute([$u]);
    $admin = $admin->fetch();
    if ($admin && password_verify($p, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: index.php'); exit;
    }
    sleep(1);
    $error = 'Identifiants incorrects.';
}
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — Connexion</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Mono:wght@300;400&display=swap" rel="stylesheet">
<style>
:root { --bg:#0a0a0a; --bg2:#111; --bg3:#181818; --border:rgba(255,255,255,0.07); --text:#f0ede6; --text2:#888; --accent:#c8f135; }
body { background:var(--bg); color:var(--text); font-family:'DM Mono',monospace; min-height:100vh; display:flex; align-items:center; justify-content:center; }
.field { background:var(--bg3); border:1px solid var(--border); padding:12px 16px; width:100%; font-family:'DM Mono',monospace; font-size:0.85rem; color:var(--text); outline:none; transition:border-color 0.2s; border-radius:2px; }
.field:focus { border-color:var(--accent); }
.field::placeholder { color:var(--text2); }
</style>
</head>
<body>
<div style="width:100%;max-width:400px;padding:2rem">
  <!-- Logo -->
  <div class="text-center mb-10">
    <div style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;margin-bottom:4px">
      <span style="color:var(--accent)">MD</span> Admin
    </div>
    <div style="font-size:0.65rem;letter-spacing:0.2em;color:var(--text2)">ESPACE ADMINISTRATION</div>
  </div>

  <?php if($error): ?>
  <div style="background:rgba(255,100,100,0.1);border:1px solid #ff6464;color:#ff6464;padding:10px 14px;margin-bottom:16px;font-size:0.78rem">✕ <?= h($error) ?></div>
  <?php endif; ?>

  <form method="POST" style="space-y:12px">
    <div style="margin-bottom:16px">
      <label style="font-size:0.65rem;letter-spacing:0.15em;color:var(--text2);display:block;margin-bottom:8px">IDENTIFIANT</label>
      <input name="username" type="text" class="field" placeholder="admin" autofocus required value="<?= h($_POST['username']??'') ?>">
    </div>
    <div style="margin-bottom:24px">
      <label style="font-size:0.65rem;letter-spacing:0.15em;color:var(--text2);display:block;margin-bottom:8px">MOT DE PASSE</label>
      <input name="password" type="password" class="field" placeholder="••••••••" required>
    </div>
    <button type="submit" style="background:var(--accent);color:#0a0a0a;font-family:'Syne',sans-serif;font-weight:700;font-size:0.8rem;letter-spacing:0.1em;text-transform:uppercase;padding:14px 32px;border:none;cursor:pointer;width:100%">
      SE CONNECTER →
    </button>
  </form>

  <div class="text-center mt-6">
    <a href="../index.php" style="font-size:0.65rem;letter-spacing:0.1em;color:var(--text2)">← Retour au portfolio</a>
  </div>
</div>
</body>
</html>
