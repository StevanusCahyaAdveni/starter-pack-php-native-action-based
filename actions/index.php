<?php
session_start();
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    exit;
}
include '../config.php';
include '../functions/secure_query.php';
include '../functions/sanitasi.php';
include '../functions/generate_uuid.php';

$hal = 'dashboard';
$textTitle = 'Dashboard';
if (isset($_GET['hal'])) {
    $getHal = sani($_GET['hal']);
    $hal = str_replace('_', '/', $getHal);
    $lastUnderscore = strrpos($getHal, '_');
    $titlePart = ($lastUnderscore !== false) ? substr($getHal, $lastUnderscore + 1) : $getHal;
    $textTitle = ucwords(str_replace('-', ' ', $titlePart));
}

include 'pages/' . $hal . '.php';
?>