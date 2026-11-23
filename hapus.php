<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) deleteTask($pdo, $id);
}
header('Location: ' . ($_POST['kembali'] ?? 'index.php'));
exit;
