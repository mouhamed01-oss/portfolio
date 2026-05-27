<?php
require_once '../config.php';
requireAdmin();
$pdo = db();

$action      = $_POST['action']     ?? 'add';
$id          = (int)($_POST['id']   ?? 0);
$title       = trim($_POST['title'] ?? '');
$category    = $_POST['category']   ?? 'web';
$description = trim($_POST['description'] ?? '');
$tech_raw    = trim($_POST['tech_raw'] ?? '');
$link_live   = trim($_POST['link_live'] ?? '');
$link_code   = trim($_POST['link_code'] ?? '');
$is_featured = isset($_POST['is_featured']) ? 1 : 0;
$sort_order  = (int)($_POST['sort_order'] ?? 0);

if (!$title || !$description) {
    header('Location: index.php?tab=projects&msg=error'); exit;
}

// Convertir tech en JSON
$techArr  = array_values(array_filter(array_map('trim', explode(',', $tech_raw))));
$techJson = json_encode($techArr, JSON_UNESCAPED_UNICODE);

// Gérer l'image uploadée
$imageName = null;
if (!empty($_FILES['image']['name'])) {
    $allowed   = ['image/jpeg','image/png','image/webp','image/gif'];
    $uploadDir = __DIR__ . '/../uploads/projects/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $file = $_FILES['image'];
    if ($file['error'] === UPLOAD_ERR_OK && in_array($file['type'], $allowed) && $file['size'] <= 5*1024*1024) {
        $ext       = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $imageName = 'proj_' . uniqid() . '.' . $ext;
        move_uploaded_file($file['tmp_name'], $uploadDir . $imageName);
    }
}

if ($action === 'add') {
    $pdo->prepare(
        "INSERT INTO projects (title, category, description, tech_stack, image, link_live, link_code, is_featured, sort_order)
         VALUES (?,?,?,?,?,?,?,?,?)"
    )->execute([$title, $category, $description, $techJson, $imageName, $link_live, $link_code, $is_featured, $sort_order]);
} else {
    // Si une nouvelle image est uploadée, supprimer l'ancienne
    if ($imageName) {
        $old = $pdo->prepare("SELECT image FROM projects WHERE id=?");
        $old->execute([$id]);
        $oldImg = $old->fetchColumn();
        if ($oldImg) {
            $oldPath = __DIR__ . '/../uploads/projects/' . $oldImg;
            if (file_exists($oldPath)) unlink($oldPath);
        }
        $pdo->prepare(
            "UPDATE projects SET title=?, category=?, description=?, tech_stack=?, image=?,
             link_live=?, link_code=?, is_featured=?, sort_order=? WHERE id=?"
        )->execute([$title, $category, $description, $techJson, $imageName, $link_live, $link_code, $is_featured, $sort_order, $id]);
    } else {
        $pdo->prepare(
            "UPDATE projects SET title=?, category=?, description=?, tech_stack=?,
             link_live=?, link_code=?, is_featured=?, sort_order=? WHERE id=?"
        )->execute([$title, $category, $description, $techJson, $link_live, $link_code, $is_featured, $sort_order, $id]);
    }
}

header('Location: index.php?tab=projects&msg=saved');
exit;
