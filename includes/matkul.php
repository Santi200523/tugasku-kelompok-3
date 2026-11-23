<?php
/** Fungsi tabel mata_kuliah - Anggota 2 */

function getMatkul(PDO $pdo): array {
    return $pdo->query("
        SELECT m.*, (SELECT COUNT(*) FROM tasks t WHERE t.matkul_id = m.id) AS jml_tugas
        FROM mata_kuliah m ORDER BY m.nama ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
}

/** Mengembalikan pesan error, atau null jika berhasil. */
function addMatkul(PDO $pdo, string $nama, string $dosen, ?int $semester): ?string {
    if ($nama === '') {
        return 'Nama mata kuliah wajib diisi.';
    }
    try {
        $pdo->prepare("INSERT INTO mata_kuliah (nama, dosen, semester) VALUES (?, ?, ?)")
            ->execute([$nama, $dosen, $semester]);
    } catch (PDOException $e) {
        return 'Mata kuliah sudah ada.';
    }
    return null;
}

/** Mata kuliah yang masih punya tugas tidak boleh dihapus. */
function deleteMatkul(PDO $pdo, int $id): ?string {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE matkul_id = ?");
    $stmt->execute([$id]);
    if ((int)$stmt->fetchColumn() > 0) {
        return 'Mata kuliah masih dipakai oleh tugas, hapus tugasnya terlebih dahulu.';
    }
    $pdo->prepare("DELETE FROM mata_kuliah WHERE id = ?")->execute([$id]);
    return null;
}
