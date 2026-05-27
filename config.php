<?php
// config.php — Configuration & helpers portfolio

define('DB_HOST',    'localhost');
define('DB_NAME',    'portfolio_md');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

define('SITE_EMAIL', 'mouhamed.diallo@email.com');
define('SITE_NAME',  'Mouhamed Diallo');
define('SITE_PHONE', '+221 77 093 06 00');
define('SITE_LOCATION', 'Dakar, Sénégal');

if (session_status() === PHP_SESSION_NONE) session_start();

function db() {
    static $pdo = null;
    if ($pdo) return $pdo;
    try {
        $pdo = new PDO(
            'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET,
            DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    } catch (PDOException $e) {
        die('<p style="color:red;font-family:monospace">DB Error: '.$e->getMessage().'</p>');
    }
    return $pdo;
}

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function isAdmin() { return !empty($_SESSION['admin_id']); }
function requireAdmin() {
    if (!isAdmin()) { header('Location: login.php'); exit; }
}
function redirect($url) { header('Location: '.$url); exit; }

function tagsArray($json) {
    $arr = json_decode($json ?? '[]', true);
    return is_array($arr) ? $arr : [];
}
