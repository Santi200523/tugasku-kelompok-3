<?php
/** Kerangka halaman bersama (header + navigasi) - Anggota 4 */
$judulHalaman = $judulHalaman ?? 'Tugas';
$jmlPengingat = countDueReminders($pdo);
$halamanAktif = basename($_SERVER['SCRIPT_NAME']);
$menu = ['index.php' => 'Tugas', 'matkul.php' => 'Mata Kuliah', 'pengguna.php' => 'Pengguna', 'pengingat.php' => 'Pengingat'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($judulHalaman) ?> - TugasKu</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header>
    <h1>📚 TugasKu</h1>
    <p>Satu tempat untuk semua tugas kuliahmu — tidak ada lagi deadline yang terlewat.</p>
</header>
<nav class="nav">
    <?php foreach ($menu as $file => $label): ?>
        <a href="<?= $file ?>" class="<?= $halamanAktif === $file ? 'active' : '' ?>"><?= $label ?><?= ($file === 'pengingat.php' && $jmlPengingat > 0) ? " ({$jmlPengingat})" : '' ?></a>
    <?php endforeach; ?>
</nav>
<div class="container">
