<?php
require_once '../config.php';
requireAdmin();
$pdo = db();

$action = $_POST['action'] ?? 'add';
$id     = (int)($_POST['id'] ?? 0);

$title       = trim($_POST['title']       ?? '');
$institution = trim($_POST['institution'] ?? '');
$year_start  = (int)($_POST['year_start'] ?? date('Y'));
$year_end    = !empty($_POST['year_end']) ? (int)$_POST['year_end'] : null;
$description = trim($_POST['description'] ?? '');
$badge_icon  = trim($_POST['badge_icon']  ?? '🎓');
$sort_order  = (int)($_POST['sort_order'] ?? 0);

if (!$title || !$institution) {
    header('Location: index.php?tab=diplomas&msg=error'); exit;
}

if ($action === 'add') {
    $pdo->prepare(
        "INSERT INTO diplomas (title, institution, year_start, year_end, description, badge_icon, sort_order)
         VALUES (?,?,?,?,?,?,?)"
    )->execute([$title, $institution, $year_start, $year_end, $description, $badge_icon, $sort_order]);
} else {
    $pdo->prepare(
        "UPDATE diplomas SET title=?, institution=?, year_start=?, year_end=?, description=?, badge_icon=?, sort_order=?
         WHERE id=?"
    )->execute([$title, $institution, $year_start, $year_end, $description, $badge_icon, $sort_order, $id]);
}

header('Location: index.php?tab=diplomas&msg=saved');
exit;
