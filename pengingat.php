<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) markReminderSent($pdo, $id);
    header('Location: pengingat.php');
    exit;
}
$jatuhTempo = getReminders($pdo, true);
$semua      = getReminders($pdo, false);
$judulHalaman = 'Pengingat';
include 'includes/header.php';
?>
    <div class="card">
        <h2>🔔 Pengingat Hari Ini (<?= count($jatuhTempo) ?>)</h2>
        <?php if (count($jatuhTempo) === 0): ?>
            <div class="empty-state">Tidak ada pengingat yang jatuh tempo. 🎉</div>
        <?php endif; ?>
        <?php foreach ($jatuhTempo as $r): ?>
            <div class="task-item">
                <div class="task-main">
                    <div class="matkul"><?= e($r['mata_kuliah']) ?> · <?= e($r['pengguna']) ?></div>
                    <div class="judul"><?= e($r['judul']) ?></div>
                    <div class="deadline">⏰ Deadline <?= e(formatTanggalIndo($r['deadline'])) ?>
                        <span class="badge badge-dekat"><?= e($r['jenis']) ?></span></div>
                </div>
                <div class="task-actions">
                    <form method="post">
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <button class="btn-small btn-done" type="submit">✔ Tandai Terkirim</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="card">
        <h2>Jadwal Semua Pengingat</h2>
        <table class="simple">
            <tr><th>Tanggal</th><th>Jenis</th><th>Tugas</th><th>Mata Kuliah</th><th>Status</th></tr>
            <?php foreach ($semua as $r): ?>
            <tr>
                <td><?= e(formatTanggalIndo($r['tanggal_kirim'])) ?></td><td><?= e($r['jenis']) ?></td>
                <td><?= e($r['judul']) ?></td><td><?= e($r['mata_kuliah']) ?></td>
                <td><?= $r['sudah_dikirim'] ? 'Terkirim' : 'Menunggu' ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php include 'includes/footer.php'; ?>
