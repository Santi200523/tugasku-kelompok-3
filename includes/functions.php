<?php
/**
 * Fungsi bantu aplikasi TugasKu (tabel tasks) - Anggota 4
 */
require_once __DIR__ . '/users.php';
require_once __DIR__ . '/matkul.php';
require_once __DIR__ . '/reminders.php';

function getTasks(PDO $pdo, string $filter = 'semua'): array {
    $select = "SELECT t.*, m.nama AS mata_kuliah, u.nama AS pengguna
               FROM tasks t
               JOIN mata_kuliah m ON m.id = t.matkul_id
               JOIN users u ON u.id = t.user_id";
    if ($filter === 'belum' || $filter === 'selesai') {
        $stmt = $pdo->prepare("$select WHERE t.status = ? ORDER BY t.deadline ASC");
        $stmt->execute([$filter]);
    } else {
        $stmt = $pdo->query("$select ORDER BY t.status DESC, t.deadline ASC");
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function countTasks(PDO $pdo): array {
    $row = $pdo->query("
        SELECT
            COUNT(*) AS total,
            COALESCE(SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END), 0) AS selesai,
            COALESCE(SUM(CASE WHEN status = 'belum' THEN 1 ELSE 0 END), 0) AS belum,
            COALESCE(SUM(CASE WHEN status = 'belum' AND date(deadline) < date('now','localtime') THEN 1 ELSE 0 END), 0) AS lewat
        FROM tasks
    ")->fetch(PDO::FETCH_ASSOC);
    return array_map('intval', $row);
}

function addTask(PDO $pdo, int $userId, int $matkulId, string $judul, string $deskripsi, string $deadline): void {
    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, matkul_id, judul, deskripsi, deadline) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $matkulId, $judul, $deskripsi, $deadline]);
    createReminders($pdo, (int)$pdo->lastInsertId(), $deadline);
}

function updateStatus(PDO $pdo, int $id, string $status): void {
    $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
}

function deleteTask(PDO $pdo, int $id): void {
    // reminders ikut terhapus (ON DELETE CASCADE)
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
}

/** Label urgensi berdasarkan sisa hari menuju deadline (H-3, H-1, hari-H, lewat). */
function getUrgencyInfo(string $deadline, string $status): array {
    if ($status === 'selesai') {
        return ['label' => 'Selesai', 'class' => 'badge-selesai'];
    }
    $today = new DateTime('today');
    $due = new DateTime($deadline);
    $diff = (int)$today->diff($due)->format('%r%a');

    if ($diff < 0)   return ['label' => 'Lewat Deadline', 'class' => 'badge-lewat'];
    if ($diff === 0) return ['label' => 'Deadline Hari Ini', 'class' => 'badge-hariini'];
    if ($diff <= 3)  return ['label' => "H-{$diff}", 'class' => 'badge-dekat'];
    return ['label' => "{$diff} hari lagi", 'class' => 'badge-aman'];
}

function formatTanggalIndo(string $tanggal): string {
    $bulan = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
              7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
    $d = new DateTime($tanggal);
    return $d->format('d') . ' ' . $bulan[(int)$d->format('n')] . ' ' . $d->format('Y');
}

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
