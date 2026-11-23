<?php
/** Fungsi tabel reminders - Anggota 3 */

/** Buat pengingat H-3, H-1, dan hari-H untuk sebuah tugas (tanggal yang sudah lewat dilewati). */
function createReminders(PDO $pdo, int $taskId, string $deadline): void {
    $hariIni = date('Y-m-d');
    $stmt = $pdo->prepare("INSERT INTO reminders (task_id, jenis, tanggal_kirim) VALUES (?, ?, ?)");
    foreach (['H-3' => 3, 'H-1' => 1, 'Hari-H' => 0] as $jenis => $selisih) {
        $tgl = (new DateTime($deadline))->modify("-{$selisih} days")->format('Y-m-d');
        if ($tgl >= $hariIni) {
            $stmt->execute([$taskId, $jenis, $tgl]);
        }
    }
}

/** Pengingat untuk tugas yang belum selesai. $hanyaJatuhTempo=true: hanya yang tanggalnya sudah tiba. */
function getReminders(PDO $pdo, bool $hanyaJatuhTempo = false): array {
    $sql = "
        SELECT r.*, t.judul, t.deadline, m.nama AS mata_kuliah, u.nama AS pengguna
        FROM reminders r
        JOIN tasks t ON t.id = r.task_id
        JOIN mata_kuliah m ON m.id = t.matkul_id
        JOIN users u ON u.id = t.user_id
        WHERE t.status = 'belum'
    ";
    if ($hanyaJatuhTempo) {
        $sql .= " AND r.sudah_dikirim = 0 AND r.tanggal_kirim <= date('now','localtime')";
    }
    return $pdo->query($sql . " ORDER BY r.tanggal_kirim ASC, t.deadline ASC")->fetchAll(PDO::FETCH_ASSOC);
}

function countDueReminders(PDO $pdo): int {
    return count(getReminders($pdo, true));
}

function markReminderSent(PDO $pdo, int $id): void {
    $pdo->prepare("UPDATE reminders SET sudah_dikirim = 1 WHERE id = ?")->execute([$id]);
}
