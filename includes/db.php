<?php
/**
 * Koneksi Database - TugasKu
 * SQLite, file database dibuat otomatis di: data/tugasku.db
 * Tabel: users, mata_kuliah, tasks, reminders
 */

$dbDir = __DIR__ . '/../data';
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0777, true);
}

try {
    $pdo = new PDO('sqlite:' . $dbDir . '/tugasku.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA foreign_keys = ON');
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . $e->getMessage());
}

// Migrasi dari versi 1 (tabel tasks lama memakai kolom teks "mata_kuliah")
$kolomLama = false;
foreach ($pdo->query("PRAGMA table_info(tasks)")->fetchAll(PDO::FETCH_ASSOC) as $k) {
    if ($k['name'] === 'mata_kuliah') $kolomLama = true;
}
if ($kolomLama) {
    $pdo->exec("ALTER TABLE tasks RENAME TO tasks_lama");
}

// Tabel 1: users (Anggota 1)
$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nama TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )
");

// Tabel 2: mata_kuliah (Anggota 2)
$pdo->exec("
    CREATE TABLE IF NOT EXISTS mata_kuliah (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nama TEXT NOT NULL UNIQUE,
        dosen TEXT,
        semester INTEGER
    )
");

// Tabel 3: tasks (Anggota 4)
$pdo->exec("
    CREATE TABLE IF NOT EXISTS tasks (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        matkul_id INTEGER NOT NULL,
        judul TEXT NOT NULL,
        deskripsi TEXT,
        deadline TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT 'belum',
        created_at TEXT DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (matkul_id) REFERENCES mata_kuliah(id)
    )
");

// Tabel 4: reminders (Anggota 3)
$pdo->exec("
    CREATE TABLE IF NOT EXISTS reminders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        task_id INTEGER NOT NULL,
        jenis TEXT NOT NULL,
        tanggal_kirim TEXT NOT NULL,
        sudah_dikirim INTEGER NOT NULL DEFAULT 0,
        FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
    )
");

// Pengguna bawaan agar aplikasi langsung bisa dipakai
if ((int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() === 0) {
    $pdo->exec("INSERT INTO users (nama, email) VALUES ('Mahasiswa', 'mahasiswa@example.com')");
}

// Lanjutan migrasi: pindahkan data lama ke struktur baru
if ($kolomLama) {
    $pdo->exec("INSERT OR IGNORE INTO mata_kuliah (nama) SELECT DISTINCT mata_kuliah FROM tasks_lama");
    $pdo->exec("
        INSERT INTO tasks (user_id, matkul_id, judul, deskripsi, deadline, status, created_at)
        SELECT (SELECT MIN(id) FROM users),
               (SELECT m.id FROM mata_kuliah m WHERE m.nama = o.mata_kuliah),
               o.judul, o.deskripsi, o.deadline, o.status, o.created_at
        FROM tasks_lama o
    ");
    $pdo->exec("DROP TABLE tasks_lama");
}
