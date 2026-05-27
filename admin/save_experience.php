<?php
require_once '../config.php';
requireAdmin();
$pdo = db();

$action     = $_POST['action'] ?? 'add';
$id         = (int)($_POST['id'] ?? 0);

$job_title  = trim($_POST['job_title']  ?? '');
$company    = trim($_POST['company']    ?? '');
$location   = trim($_POST['location']   ?? '');
$date_start = trim($_POST['date_start'] ?? '');
$date_end   = !empty($_POST['date_end']) ? $_POST['date_end'] : null;
$description= trim($_POST['description']?? '');
$sort_order = (int)($_POST['sort_order']?? 0);

// Convertir les tags raw en JSON
$tags_raw   = trim($_POST['tags_raw'] ?? '');
$tagsArr    = array_values(array_filter(array_map('trim', explode(',', $tags_raw))));
$tags_json  = json_encode($tagsArr, JSON_UNESCAPED_UNICODE);

if (!$job_title || !$company || !$date_start) {
    header('Location: index.php?tab=experiences&msg=error'); exit;
}

if ($action === 'add') {
    $pdo->prepare(
        "INSERT INTO experiences (job_title, company, location, date_start, date_end, description, tags, sort_order)
         VALUES (?,?,?,?,?,?,?,?)"
    )->execute([$job_title, $company, $location, $date_start, $date_end, $description, $tags_json, $sort_order]);
} else {
    $pdo->prepare(
        "UPDATE experiences SET job_title=?, company=?, location=?, date_start=?, date_end=?,
         description=?, tags=?, sort_order=? WHERE id=?"
    )->execute([$job_title, $company, $location, $date_start, $date_end, $description, $tags_json, $sort_order, $id]);
}

header('Location: index.php?tab=experiences&msg=saved');
exit;
