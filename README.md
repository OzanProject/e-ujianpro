# E-Ujian PRO 🎓🚀

**E-Ujian PRO** adalah platform *Computer Based Test* (CBT) dan Sistem Manajemen Sekolah modern berbasis **Laravel 12** dan **Vite + Tailwind CSS 4**. Aplikasi ini dirancang dengan arsitektur *Multi-Tenancy* yang memungkinkan satu sistem digunakan oleh banyak sekolah/lembaga secara terisolasi, aman, dan profesional.

![Status Proyek](https://img.shields.io/badge/status-active-success.svg)
![Laravel Version](https://img.shields.io/badge/laravel-v12.0-red.svg)
![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue.svg)

---

## 🔥 Fitur Unggulan Terbaru

### 1. 🏢 Multi-Tenancy & Data Isolation
Setiap sekolah (Institution) memiliki ekosistem yang sepenuhnya terisolasi.
-   **Deep Delete System**: Penghapusan data sekolah akan membersihkan seluruh aset terkait (Siswa, Guru, Soal, Ujian) tanpa sisa sampah data.
-   **Custom Branding**: Logo dan Kop Surat sekolah yang dapat dikustomisasi.

### 2. 🎨 "Clean Enterprise" UI/UX
Desain antarmuka telah dirombak total menjadi lebih modern, bersih, dan profesional.
-   **Glassmorphism & Minimalist**: Tampilan dashboard dan form yang elegan menggunakan Tailwind CSS 4 & Bootstrap 5 (Hybrid optimized).
-   **Responsive Mobile**: Optimal untuk penggunaan di HP (Siswa & Guru).
-   **Professional Admin Panel**: Sidebar yang rapi, tabel interaktif, dan navigasi intuitif.

### 3. ✅ Manajemen Ujian Lengkap
-   **Master Jenis Ujian**: Kelola kategori ujian (UTS, UAS, Quiz) agar judul ujian lebih konsisten.
-   **Jadwal Ujian**: Pembuatan sesi ujian dengan pemilihan "Jenis Ujian" dan pengaturan waktu yang fleksibel.
-   **Token Ujian**: Generator token otomatis (5 karakter unik) untuk keamanan masuk ujian.
-   **Bank Soal & Paket**: Manajemen soal pilihan ganda & esai yang terstruktur, mendukung impor/ekspor.

### 4. 👥 Manajemen Pengguna & Peran
Sistem memiliki tingkatan akses yang detail:
-   **Super Admin**: Platform owner, verifikasi sekolah & poin.
-   **Admin Lembaga**: Kepala Sekolah/Operator Utama.
-   **Guru (Pengajar)**: Kelola Mapel, Bank Soal, dan Monitoring Ujian.
-   **Wali Kelas**: Monitoring spesifik untuk kelas binaannya (Rekap Absen & Laporan).
-   **Pengawas (Proctor)**: Akses khusus monitoring ruang ujian & absen peserta.
-   **Siswa**: Dashboard modern untuk mengerjakan ujian & melihat pengumuman.

### 5. 💰 Sistem Poin & Dompet (Wallet)
-   Integrasi sistem pembayaran berbasis poin untuk layanan sekolah.
-   Top-up dan riwayat transaksi poin transparan.

### 6. 📊 Laporan & Cetak Dokumen
Sistem pelaporan lengkap siap cetak:
-   **Kartu Meja & Daftar Hadir**: Cetak otomatis per ruang ujian.
-   **Berita Acara & Absen Pengawas**: Dokumen administrasi ujian lengkap.
-   **Rekap Nilai**: Ekspor hasil ujian siswa secara mendetail.
-   **Rekap Absensi Bulanan**: Laporan kehadiran siswa otomatis (Wali Kelas).

### 7. 📱 Fitur Scan Absensi (QR Code)
-   **Optimasi Mobile**: Halaman scan QR Code yang ringan dan cepat.
-   **Live Attendance**: Perekaman kehadiran siswa secara real-time.

---

## 🛠️ Teknologi yang Digunakan

-   **Backend**: Laravel Framework 12.x
-   **Frontend**: Blade Templates, Tailwind CSS v4, Bootstrap 5 (AdminLTE Customized)
-   **Interactivity**: Alpine.js, jQuery (Legacy Support)
-   **Build Tool**: Vite 7.x
-   **Database**: MySQL / MariaDB
-   **Authentication**: Laravel Breeze + Spatie Permission (Role Based)

---

## ⚙️ Persyaratan Sistem

Pastikan server Anda memenuhi spesifikasi berikut:
-   **PHP**: Versi 8.2 atau lebih baru (Disarankan 8.3).
-   **Database**: MySQL 5.7+ atau MariaDB 10.3+.
-   **Composer**: Dependency Manager v2+.
-   **Node.js & NPM**: Node 18+ (Disarankan 20+).

---

## 🚀 Panduan Instalasi Lokal

### 1. Clone & Install
```bash
git clone https://github.com/username/e-ujian-pro.git
cd e-ujian-pro
composer install
npm install
```

### 2. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```
Atur `.env` (DB_DATABASE, DB_USERNAME, dll).

### 3. Migrasi Database
```bash
php artisan migrate:fresh --seed
```

### 4. Menjalankan Aplikasi
```bash
npm run dev   # Terminal 1 (Frontend)
php artisan serve # Terminal 2 (Backend)
```
Akses: `http://localhost:8000`

---

## 🔑 Akun Default (Demo)

| Role | Email | Password |
| :--- | :--- | :--- |
| **Super Admin** | `ardiansyahdzan@gmail.com` | `password` |
| **Admin Sekolah** | (Dibuat sbg user pertama) | `password` |
| **Guru/Siswa** | (Hasil seeder / input manual) | `password` |

> ⚠️ **PENTING**: Segera ubah password default saat production!

---

## 🛡️ Lisensi & Kredit

Aplikasi ini dikembangkan dengan standar keamanan tinggi (CSRF, XSS Protection, Role Middleware).

---

*Happy Coding & Happy Testing! 🚀*
