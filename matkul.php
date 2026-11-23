<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$pesan = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['aksi'] ?? '') === 'tambah') {
        $smt = ($_POST['semester'] ?? '') !== '' ? (int)$_POST['semester'] : null;
        $pesan = addMatkul($pdo, trim($_POST['nama'] ?? ''), trim($_POST['dosen'] ?? ''), $smt);
    } elseif (($_POST['aksi'] ?? '') === 'hapus') {
        $pesan = deleteMatkul($pdo, (int)($_POST['id'] ?? 0));
    }
    if ($pesan === null) {
        header('Location: matkul.php');
        exit;
    }
}
$daftar = getMatkul($pdo);
$judulHalaman = 'Mata Kuliah';
include 'includes/header.php';
?>
    <div class="card">
        <h2>🎓 Tambah Mata Kuliah</h2>
        <?php if ($pesan): ?><div class="alert"><?= e($pesan) ?></div><?php endif; ?>
        <form method="post">
            <input type="hidden" name="aksi" value="tambah">
            <div class="form-row">
                <div class="form-group"><label for="nama">Nama Mata Kuliah</label><input type="text" id="nama" name="nama" required></div>
                <div class="form-group"><label for="dosen">Dosen</label><input type="text" id="dosen" name="dosen"></div>
                <div class="form-group"><label for="semester">Semester</label><input type="text" id="semester" name="semester" inputmode="numeric"></div>
            </div>
            <button type="submit" class="btn-submit">Simpan Mata Kuliah</button>
        </form>
    </div>
    <div class="card">
        <h2>Daftar Mata Kuliah</h2>
        <table class="simple">
            <tr><th>Mata Kuliah</th><th>Dosen</th><th>Smt</th><th>Jml Tugas</th><th></th></tr>
            <?php foreach ($daftar as $m): ?>
            <tr>
                <td><?= e($m['nama']) ?></td><td><?= e((string)$m['dosen']) ?></td>
                <td><?= e((string)$m['semester']) ?></td><td><?= (int)$m['jml_tugas'] ?></td>
                <td>
                    <form method="post" onsubmit="return confirm('Hapus mata kuliah ini?');">
                        <input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                        <button class="btn-small btn-delete" type="submit">🗑 Hapus</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php include 'includes/footer.php'; ?>
