<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$filter = $_GET['filter'] ?? 'semua';
if (!in_array($filter, ['semua', 'belum', 'selesai'], true)) {
    $filter = 'semua';
}

$tasks = getTasks($pdo, $filter);
$stat  = countTasks($pdo);
$kembali = 'index.php' . ($filter !== 'semua' ? '?filter=' . $filter : '');
?>
<?php
$judulHalaman = 'Tugas';
$users  = getUsers($pdo);
$matkul = getMatkul($pdo);
include 'includes/header.php';
?>

    <!-- Ringkasan -->
    <div class="summary-row">
        <div class="summary-box"><div class="num"><?= $stat['total'] ?></div><div class="lbl">Total Tugas</div></div>
        <div class="summary-box"><div class="num"><?= $stat['belum'] ?></div><div class="lbl">Belum Selesai</div></div>
        <div class="summary-box"><div class="num"><?= $stat['selesai'] ?></div><div class="lbl">Selesai</div></div>
        <div class="summary-box"><div class="num" style="color:#b91c1c"><?= $stat['lewat'] ?></div><div class="lbl">Lewat Deadline</div></div>
    </div>

    <!-- Form tambah tugas -->
    <div class="card">
        <h2>➕ Tambah Tugas Baru</h2>
        <form action="tambah.php" method="post">
            <div class="form-row">
                <div class="form-group">
                    <label for="matkul_id">Mata Kuliah</label>
                    <select id="matkul_id" name="matkul_id" required>
                        <option value="">-- pilih --</option>
                        <?php foreach ($matkul as $m): ?>
                            <option value="<?= (int)$m['id'] ?>"><?= e($m['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="user_id">Pengguna</label>
                    <select id="user_id" name="user_id" required>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= (int)$u['id'] ?>"><?= e($u['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="deadline">Deadline</label>
                    <input type="date" id="deadline" name="deadline" required>
                </div>
            </div>
            <div class="form-group">
                <label for="judul">Judul Tugas</label>
                <input type="text" id="judul" name="judul" placeholder="Contoh: Makalah ekosistem startup" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi (opsional)</label>
                <textarea id="deskripsi" name="deskripsi" placeholder="Catatan tambahan..."></textarea>
            </div>
            <button type="submit" class="btn-submit">Simpan Tugas</button>
        </form>
    </div>

    <!-- Daftar tugas -->
    <div class="tabs">
        <a class="tab <?= $filter === 'semua' ? 'active' : '' ?>" href="index.php">Semua</a>
        <a class="tab <?= $filter === 'belum' ? 'active' : '' ?>" href="index.php?filter=belum">Belum Selesai</a>
        <a class="tab <?= $filter === 'selesai' ? 'active' : '' ?>" href="index.php?filter=selesai">Selesai</a>
    </div>

    <div class="card">
        <h2>📋 Daftar Tugas</h2>

        <?php if (count($tasks) === 0): ?>
            <div class="empty-state">Belum ada tugas. Tambahkan tugas pertamamu di atas! 🎉<?php if (count($matkul) === 0): ?> Tambahkan dulu <a href="matkul.php">mata kuliah</a>.<?php endif; ?></div>
        <?php endif; ?>

        <?php foreach ($tasks as $t):
            $urg = getUrgencyInfo($t['deadline'], $t['status']);
        ?>
            <div class="task-item <?= $t['status'] === 'selesai' ? 'selesai' : '' ?>">
                <div class="task-main">
                    <div class="matkul"><?= e($t['mata_kuliah']) ?> · <?= e($t['pengguna']) ?></div>
                    <div class="judul"><?= e($t['judul']) ?></div>
                    <?php if (!empty($t['deskripsi'])): ?>
                        <div class="deskripsi"><?= e($t['deskripsi']) ?></div>
                    <?php endif; ?>
                    <div class="deadline">
                        ⏰ <?= e(formatTanggalIndo($t['deadline'])) ?>
                        <span class="badge <?= $urg['class'] ?>"><?= e($urg['label']) ?></span>
                    </div>
                </div>
                <div class="task-actions">
                    <form action="status.php" method="post">
                        <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                        <input type="hidden" name="kembali" value="<?= e($kembali) ?>">
                        <?php if ($t['status'] === 'selesai'): ?>
                            <input type="hidden" name="aksi" value="batal">
                            <button class="btn-small btn-undo" type="submit">↩ Batalkan</button>
                        <?php else: ?>
                            <input type="hidden" name="aksi" value="selesai">
                            <button class="btn-small btn-done" type="submit">✔ Selesai</button>
                        <?php endif; ?>
                    </form>
                    <form action="hapus.php" method="post" onsubmit="return confirm('Hapus tugas ini?');">
                        <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                        <input type="hidden" name="kembali" value="<?= e($kembali) ?>">
                        <button class="btn-small btn-delete" type="submit">🗑 Hapus</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php include 'includes/footer.php'; ?>
