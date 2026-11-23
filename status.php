<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = (int)($_POST['id'] ?? 0);
    $aksi = $_POST['aksi'] ?? '';
    if ($id > 0 && $aksi === 'selesai') updateStatus($pdo, $id, 'selesai');
    if ($id > 0 && $aksi === 'batal')   updateStatus($pdo, $id, 'belum');
}
header('Location: ' . ($_POST['kembali'] ?? 'index.php'));
exit;
