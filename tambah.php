<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId    = (int)($_POST['user_id'] ?? 0);
    $matkulId  = (int)($_POST['matkul_id'] ?? 0);
    $judul     = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $deadline  = trim($_POST['deadline'] ?? '');

    if ($userId > 0 && $matkulId > 0 && $judul !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline)) {
        try {
            addTask($pdo, $userId, $matkulId, $judul, $deskripsi, $deadline);
        } catch (PDOException $e) {
            // user / mata kuliah tidak valid: abaikan, kembali ke beranda
        }
    }
}
header('Location: index.php');
exit;
