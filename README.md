# E-Ujian PRO 🎓🚀

**E-Ujian PRO** adalah platform *Computer Based Test* (CBT) dan Sistem Manajemen Sekolah Modern. Dirancang dengan teknologi terkini (**Laravel 12**, **Tailwind CSS 4**), aplikasi ini menawarkan solusi Ujian Online yang **stabil**, **aman**, dan **mudah digunakan** oleh institusi pendidikan manapun.

Sistem ini mendukung arsitektur *Multi-Tenancy*, yang memungkinkan satu instalasi digunakan oleh banyak sekolah dengan data yang terisolasi sempurna.

![Status Proyek](https://img.shields.io/badge/status-active-success.svg?style=for-the-badge)
![Laravel Version](https://img.shields.io/badge/laravel-v12.0-red.svg?style=for-the-badge&logo=laravel)
![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue.svg?style=for-the-badge&logo=php)

---

## 🌐 Live Demo & Akses

Cobalah sistem secara langsung melalui tautan berikut. Gunakan akun demo di bawah ini untuk masuk.

👉 **URL Akses**: [https://e-ujian.ozanproject.site/](https://e-ujian.ozanproject.site/)

### 🔑 Akun Demo (Credentials)

| Peran (Role) | Email Login | Password | Fungsi Utama |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `superadmin@e-ujian.id` | `password` | Mengelola seluruh sekolah & langganan sistem. |
| **Admin Lembaga** | `admin@smkn1.sch.id` | `password` | **(RECOMMENDED)** Mengelola data siswa, guru, jadwal, dan laporan sekolah. |
| **Guru (Pengajar)** | `guru@smkn1.sch.id` | `password` | Membuat soal, memonitor ujian aktif, dan koreksi nilai. |
| **Siswa** | `123456` (NIS) | `password` | Mengerjakan ujian & melihat hasil. |

> **Catatan:** Jangan mengubah password akun demo agar pengguna lain tetap bisa mencoba.

---

## 🔥 Fitur Utama & Keunggulan

### 1. 📚 Bank Soal Fleksibel (Import Word/Excel)
Tidak perlu repot input satu per satu!
-   **Import Word (.docx)**: Buat tabel soal di Microsoft Word dan upload langsung. Mendukung soal bergambar/rumus matematika.
-   **Import Excel (.xlsx)**: Solusi cepat untuk input ribuan soal sekaligus.
-   **Editor WYSIWYG**: Input manual dengan fitur lengkap untuk soal kompleks.

### 2. 🗺️ Panduan Sistem Interaktif
Bingung harus mulai dari mana?
-   Fitur **"Panduan Sistem"** baru di panel admin memandu Anda langkah-demi-langkah.
-   Mulai dari **Data Master** -> **Bank Soal** -> **Jadwal** -> **Pelaksanaan**. Dilengkapi visual timeline agar alur kerja jelas.

### 3. 🛡️ Keamanan Ujian (Secure Exam)
-   **Token Ujian**: Generate token unik 6 digit yang wajib dimasukkan siswa sebelum ujian dimulai.
-   **Timer Server-Side**: Waktu ujian dihitung dari server, mencegah kecurangan manipulasi jam di perangkat siswa.
-   **Acak Soal & Jawaban**: Tiap siswa mendapatkan urutan soal yang berbeda.

### 4. 🏢 Multi-Tenancy & Data Isolation
-   Setiap sekolah memiliki **Logo**, **Kop Surat**, dan **Data** sendiri.
-   Penghapusan data satu sekolah TIDAK akan mengganggu data sekolah lain.
-   Dasbor statistik terpisah untuk setiap lembaga.

### 5. 📊 Laporan & Administrasi Lengkap
Sistem ini bukan hanya untuk ujian, tapi juga administrasi:
-   **Cetak Kartu Peserta**: Kartu ujian siap cetak dengen QR Code login.
-   **Absensi & Berita Acara**: Dokumen pengawas siap unduh dalam format PDF.
-   **Analisis Nilai**: Rekap nilai otomatis (Excel/PDF).

---

## 🛠️ Stack Teknologi

Dibangun dengan fondasi yang kokoh untuk performa tinggi:

-   **Backend**: Laravel Framework 12.x (PHP 8.2+)
-   **Frontend**: Blade, Tailwind CSS v4, Bootstrap 5 AdminLTE (Customized)
-   **Database**: MySQL / MariaDB
-   **Server**: Nginx / Apache
-   **Modul**: Spatie Permission, Excel Maatwebsite, PHPWord, DomPDF.

---

## ⚙️ Cara Instalasi (Localhost)

Ingin mengembangkan fitur ini di komputer sendiri?

### 1. Clone Repository
```bash
git clone https://github.com/OzanProject/e-ujianpro.git
cd e-ujianpro
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Setup Environment
Copy file `.env.example` ke `.env` dan atur database Anda.
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Migrasi & Seeding
Ini akan mengisi database dengan data dummy untuk percobaan.
```bash
# Pastikan database sudah dibuat di MySQL
php artisan migrate:fresh --seed
```

### 5. Jalankan Aplikasi
```bash
# Terminal 1 (Jalankan Vite untuk aset frontend)
npm run dev

# Terminal 2 (Jalankan Server Laravel)
php artisan serve
```
Akses di browser: `http://localhost:8000`

---

## 👨‍💻 Kontribusi & Lisensi

Proyek ini dikembangkan oleh **OzanProject Team**.
Silakan buat *Issue* atau *Pull Request* jika Anda menemukan bug atau ingin menambahkan fitur baru.

---
*Dibuat dengan ❤️ untuk kemajuan pendidikan Indonesia.*
