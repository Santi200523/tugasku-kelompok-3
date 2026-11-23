# 📚 TugasKu

Aplikasi web sederhana untuk **mencatat, mengingatkan, dan melacak tugas kuliah** mahasiswa.
Dibuat sebagai prototype **MVP startup digital bidang pendidikan (Edtech)** untuk mata kuliah
*Pengembangan Start-Up Digital* — Program Studi Informatika, Universitas Metamedia.

## Masalah yang Diselesaikan
Mahasiswa sering lupa deadline tugas karena informasi tersebar di banyak tempat
(grup WhatsApp, email dosen, Google Classroom, dll).

## Value Proposition
> Satu tempat untuk semua tugas kuliahmu — tidak ada lagi deadline yang terlewat.

## Fitur MVP
1. **Input tugas** — mata kuliah, judul, deskripsi, deadline
2. **Penanda urgensi** — H-3, H-1, Deadline Hari Ini, Lewat Deadline
3. **Dashboard status** — Belum Selesai / Selesai + ringkasan jumlah tugas

## Teknologi
- PHP 8+ (PDO)
- SQLite (database file, tanpa MySQL)
- Laragon (Apache + PHP) sebagai server lokal
- HTML + CSS

## Struktur Database (4 Tabel)
| Tabel | Fungsi | Penanggung jawab |
|---|---|---|
| `users` | Data pengguna (nama, email) | Anggota 1 |
| `mata_kuliah` | Data mata kuliah (nama, dosen, semester) | Anggota 2 |
| `reminders` | Pengingat H-3, H-1, Hari-H per tugas | Anggota 3 |
| `tasks` | Tugas kuliah (relasi ke `users` dan `mata_kuliah`) | Anggota 4 |

Relasi: `tasks.user_id` -> `users.id`, `tasks.matkul_id` -> `mata_kuliah.id`, `reminders.task_id` -> `tasks.id` (hapus tugas = pengingatnya ikut terhapus).

## Struktur Proyek
```
tugasku/
├── index.php            # Dashboard + form + daftar tugas          (Anggota 4)
├── tambah.php           # Proses tambah tugas                      (Anggota 4)
├── status.php           # Proses ubah status                       (Anggota 4)
├── hapus.php            # Proses hapus tugas                       (Anggota 4)
├── pengguna.php         # Kelola pengguna                          (Anggota 1)
├── matkul.php           # Kelola mata kuliah                       (Anggota 2)
├── pengingat.php        # Daftar & status pengingat                (Anggota 3)
├── includes/
│   ├── db.php           # Koneksi SQLite, 4 tabel, migrasi v1      (Anggota 4)
│   ├── functions.php    # Fungsi tasks & fungsi bantu              (Anggota 4)
│   ├── header.php       # Header + navigasi bersama                (Anggota 4)
│   ├── footer.php       # Penutup halaman                          (Anggota 4)
│   ├── users.php        # Fungsi tabel users                       (Anggota 1)
│   ├── matkul.php       # Fungsi tabel mata_kuliah                 (Anggota 2)
│   └── reminders.php    # Fungsi tabel reminders                   (Anggota 3)
├── assets/style.css     # Tampilan
└── data/                # Lokasi file database (dibuat otomatis)
```

## Cara Menjalankan (Laragon)
Aplikasi ini memakai **SQLite**, jadi **tidak perlu MySQL / membuat database**.

1. Ekstrak folder `tugasku` ke `C:\laragon\www\`
2. Buka **Laragon** lalu klik **Start All** (cukup Apache, MySQL boleh dibiarkan mati)
3. Buka browser:
   - `http://localhost/tugasku`, atau
   - `http://tugasku.test` (otomatis dibuat Laragon lewat Auto Virtual Hosts)
4. File database `data/tugasku.db` dibuat otomatis saat halaman pertama dibuka

Jika muncul error "could not find driver", aktifkan ekstensi `pdo_sqlite`:
Laragon > Menu > PHP > Extensions > centang **pdo_sqlite** dan **sqlite3**, lalu restart Apache.

### Alternatif tanpa Laragon
```bash
cd tugasku
php -S localhost:8000
```

## Rencana Pengembangan (Versi 2)
- Pengiriman reminder otomatis (email / WhatsApp); versi ini baru menjadwalkan dan menandai pengingat
- Login dengan password (versi ini baru data pengguna)
- Integrasi kalender dan SIAKAD kampus
- Kolaborasi tugas kelompok

## Model Bisnis
Freemium (fitur dasar gratis, fitur premium berbayar) dan B2B ke kampus.
