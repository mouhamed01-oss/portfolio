<?php
// fix_admin.php — SUPPRIMER APRÈS USAGE
require_once 'config.php';
$pdo = db();

// Générer un hash avec VOTRE PHP installé
$nouveauHash = password_hash('Admin2025', PASSWORD_BCRYPT);

// Mettre à jour
$pdo->prepare("UPDATE admins SET password = ? WHERE username = 'admin'")
    ->execute([$nouveauHash]);

echo "<pre style='font-family:monospace;background:#0a0a0a;color:#c8f135;padding:30px;'>";
echo "✅ Mot de passe mis à jour !\n\n";
echo "Identifiant : admin\n";
echo "Mot de passe : Admin2025\n\n";
echo "Hash généré par PHP " . phpversion() . " :\n";
echo $nouveauHash . "\n\n";

// Vérification immédiate
$row = $pdo->query("SELECT password FROM admins WHERE username='admin'")->fetch();
$ok  = password_verify('Admin2025', $row['password']);
echo "Vérification : " . ($ok ? "✅ SUCCÈS — vous pouvez vous connecter !" : "❌ ÉCHEC") . "\n\n";
echo "👉 Connectez-vous sur : <a href='admin/login.php' style='color:#c8f135'>admin/login.php</a>\n";
echo "🗑️  Supprimez ce fichier ensuite !\n";
echo "</pre>";
