<?php
/** Fungsi tabel users - Anggota 1 */

function getUsers(PDO $pdo): array {
    return $pdo->query("
        SELECT u.*, (SELECT COUNT(*) FROM tasks t WHERE t.user_id = u.id) AS jml_tugas
        FROM users u ORDER BY u.nama ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
}

/** Mengembalikan pesan error, atau null jika berhasil. */
function addUser(PDO $pdo, string $nama, string $email): ?string {
    if ($nama === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Nama wajib diisi dan email harus valid.';
    }
    try {
        $pdo->prepare("INSERT INTO users (nama, email) VALUES (?, ?)")->execute([$nama, $email]);
    } catch (PDOException $e) {
        return 'Email sudah terdaftar.';
    }
    return null;
}

/** Pengguna yang masih punya tugas tidak boleh dihapus. */
function deleteUser(PDO $pdo, int $id): ?string {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ?");
    $stmt->execute([$id]);
    if ((int)$stmt->fetchColumn() > 0) {
        return 'Pengguna masih memiliki tugas, hapus tugasnya terlebih dahulu.';
    }
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    return null;
}
