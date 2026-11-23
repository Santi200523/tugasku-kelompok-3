<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$pesan = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['aksi'] ?? '') === 'tambah') {
        $pesan = addUser($pdo, trim($_POST['nama'] ?? ''), trim($_POST['email'] ?? ''));
    } elseif (($_POST['aksi'] ?? '') === 'hapus') {
        $pesan = deleteUser($pdo, (int)($_POST['id'] ?? 0));
    }
    if ($pesan === null) {
        header('Location: pengguna.php');
        exit;
    }
}
$users = getUsers($pdo);
$judulHalaman = 'Pengguna';
include 'includes/header.php';
?>
    <div class="card">
        <h2>👤 Tambah Pengguna</h2>
        <?php if ($pesan): ?><div class="alert"><?= e($pesan) ?></div><?php endif; ?>
        <form method="post">
            <input type="hidden" name="aksi" value="tambah">
            <div class="form-row">
                <div class="form-group"><label for="nama">Nama</label><input type="text" id="nama" name="nama" required></div>
                <div class="form-group"><label for="email">Email</label><input type="text" id="email" name="email" required></div>
            </div>
            <button type="submit" class="btn-submit">Simpan Pengguna</button>
        </form>
    </div>
    <div class="card">
        <h2>Daftar Pengguna</h2>
        <table class="simple">
            <tr><th>Nama</th><th>Email</th><th>Jml Tugas</th><th></th></tr>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= e($u['nama']) ?></td><td><?= e($u['email']) ?></td><td><?= (int)$u['jml_tugas'] ?></td>
                <td>
                    <form method="post" onsubmit="return confirm('Hapus pengguna ini?');">
                        <input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                        <button class="btn-small btn-delete" type="submit">🗑 Hapus</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php include 'includes/footer.php'; ?>
