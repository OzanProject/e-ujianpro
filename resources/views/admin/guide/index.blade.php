@extends('layouts.admin.app')

@section('title', 'Panduan Sistem Lengkap')
@section('page_title', 'Panduan Penggunaan Sistem')

@section('content')
    <style>
        /* Custom Timeline CSS */
        .timeline {
            position: relative;
            padding: 20px 0;
            list-style: none;
        }

        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 40px;
            width: 3px;
            background: #e9ecef;
            margin-left: -1.5px;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 60px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-badge {
            position: absolute;
            left: 0;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            text-align: center;
            z-index: 100;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .timeline-content {
            margin-left: 100px;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }

        .timeline-content:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .timeline-content:before {
            content: '';
            position: absolute;
            width: 0;
            height: 0;
            border-top: 10px solid transparent;
            border-bottom: 10px solid transparent;
            border-right: 15px solid white;
            top: 30px;
            left: -15px;
        }

        /* Dynamic Colors */
        .border-l-500-indigo {
            border-left: 5px solid #6366f1;
        }

        .border-l-500-blue {
            border-left: 5px solid #3b82f6;
        }

        .border-l-500-cyan {
            border-left: 5px solid #06b6d4;
        }

        .border-l-500-green {
            border-left: 5px solid #22c55e;
        }

        .border-l-500-yellow {
            border-left: 5px solid #eab308;
        }

        .border-l-500-red {
            border-left: 5px solid #ef4444;
        }

        .text-indigo-600 {
            color: #6366f1;
        }

        .text-blue-600 {
            color: #3b82f6;
        }

        .text-cyan-600 {
            color: #06b6d4;
        }

        .text-green-600 {
            color: #22c55e;
        }

        .text-yellow-600 {
            color: #ca8a04;
        }

        /* Darker yellow for text */
        .text-red-600 {
            color: #ef4444;
        }

        .bg-indigo-50 {
            background-color: #eef2ff;
        }

        .bg-blue-50 {
            background-color: #eff6ff;
        }

        .bg-cyan-50 {
            background-color: #ecfeff;
        }

        .bg-green-50 {
            background-color: #f0fdf4;
        }

        .bg-yellow-50 {
            background-color: #fefce8;
        }

        .bg-red-50 {
            background-color: #fef2f2;
        }

        .badge-step {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 20px;
            margin-bottom: 10px;
            display: inline-block;
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-lg-10 col-12">
            <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
                <div class="card-header bg-gradient-to-r from-blue-700 to-indigo-700 p-5 border-0 text-center">
                    <h3 class="card-title text-white font-weight-bold mb-2">
                        <i class="fas fa-book-reader mr-2"></i> Panduan Lengkap Administrator
                    </h3>
                    <p class="text-white opacity-90 mb-0 font-weight-light">Alur kerja sistematis untuk pengelolaan ujian
                        yang lancar.</p>
                </div>

                <div class="card-body bg-light p-4 p-md-5">
                    <div class="timeline">

                        {{-- Step 1: Data Master --}}
                        <div class="timeline-item">
                            <div class="timeline-badge bg-white text-indigo-600 border border-indigo-100">
                                <i class="fas fa-database text-lg"></i>
                            </div>
                            <div class="timeline-content border-l-500-indigo">
                                <span
                                    class="badge badge-light text-indigo-600 font-weight-bold badge-step border border-indigo-100">Tahap
                                    1</span>
                                <h5 class="font-weight-bold text-gray-800 mb-2">Persiapan Data Master</h5>
                                <p class="text-gray-600 mb-3 text-sm">Langkah awal adalah mengisi data-data dasar yang
                                    diperlukan sistem.</p>

                                <button
                                    class="btn btn-sm btn-light text-indigo-600 font-weight-bold mb-3 rounded-pill px-3 shadow-sm"
                                    type="button" data-toggle="collapse" data-target="#step1">
                                    <i class="fas fa-chevron-down mr-1"></i> Lihat Detail
                                </button>

                                <div class="collapse mb-3" id="step1">
                                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                                        <h6 class="font-weight-bold text-indigo-600 mb-3">Apa yang perlu disiapkan?</h6>
                                        <ul class="text-sm text-gray-700 space-y-2 mb-0 pl-3">
                                            <li>
                                                <strong>1. Data Kelas/Rombel:</strong> Kelola grup siswa.
                                                <br><small class="text-muted">Contoh: X IPA 1, XII IPS 2.</small>
                                            </li>
                                            <li>
                                                <strong>2. Mata Pelajaran (Mapel):</strong> Buat daftar pelajaran yang akan
                                                diujikan.
                                                <br><small class="text-muted">Kode mapel sebaiknya unik (Misal: MAT-X,
                                                    BIG-XII).</small>
                                            </li>
                                            <li>
                                                <strong>3. Data Ruangan:</strong> Untuk mengelompokkan siswa saat ujian &
                                                mencetak kartu.
                                            </li>
                                            <li>
                                                <strong>4. Data Siswa:</strong> Input manual atau **Import Excel**
                                                (Disarankan).
                                                <br><small class="text-muted">Pastikan Username/NIS & Password unik untuk
                                                    setiap siswa.</small>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="border-t pt-3 mt-2 d-flex flex-wrap gap-2">
                                    <a href="{{ route('admin.subject.index') }}"
                                        class="btn btn-sm btn-outline-indigo rounded-pill">Kelola Mapel</a>
                                    <a href="{{ route('admin.student.index') }}"
                                        class="btn btn-sm btn-outline-indigo rounded-pill">Kelola Siswa</a>
                                    <a href="{{ route('admin.exam_room.index') }}"
                                        class="btn btn-sm btn-outline-indigo rounded-pill">Kelola Ruangan</a>
                                </div>
                            </div>
                        </div>

                        {{-- Step 2: Bank Soal --}}
                        <div class="timeline-item">
                            <div class="timeline-badge bg-white text-blue-600 border border-blue-100">
                                <i class="fas fa-archive text-lg"></i>
                            </div>
                            <div class="timeline-content border-l-500-blue">
                                <span
                                    class="badge badge-light text-blue-600 font-weight-bold badge-step border border-blue-100">Tahap
                                    2</span>
                                <h5 class="font-weight-bold text-gray-800 mb-2">Manajemen Bank Soal & Paket</h5>
                                <p class="text-gray-600 mb-3 text-sm">Membuat soal dan mengelompokkannya ke dalam paket
                                    ujian.</p>

                                <button
                                    class="btn btn-sm btn-light text-blue-600 font-weight-bold mb-3 rounded-pill px-3 shadow-sm"
                                    type="button" data-toggle="collapse" data-target="#step2">
                                    <i class="fas fa-chevron-down mr-1"></i> Lihat Detail
                                </button>

                                <div class="collapse mb-3" id="step2">
                                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                                        <ul class="text-sm text-gray-700 space-y-2 mb-0 pl-3">
                                            <li>
                                                <i class="fas fa-file-word text-blue-500 mr-2"></i><strong>Import Soal
                                                    (Word/Excel):</strong>
                                                <p class="mb-1">Cara tercepat. Download template yang disediakan, isi soal
                                                    (termasuk gambar), lalu upload kembali.</p>
                                            </li>
                                            <li>
                                                <i class="fas fa-edit text-blue-500 mr-2"></i><strong>Input Manual:</strong>
                                                <p class="mb-1">Gunakan editor WYSIWYG untuk soal yang membutuhkan format
                                                    khusus atau rumus matematika (MathJax).</p>
                                            </li>
                                            <li>
                                                <i class="fas fa-box-open text-blue-500 mr-2"></i><strong>Paket
                                                    Soal:</strong>
                                                <p class="mb-0">Soal-soal bisa dikelompokkan menjadi Paket (Misal: Paket A,
                                                    Paket B) untuk variasi ujian.</p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="border-t pt-3 mt-2">
                                    <a href="{{ route('admin.question.index') }}"
                                        class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm">
                                        <i class="fas fa-plus mr-1"></i> Kelola Bank Soal
                                    </a>
                                    <a href="{{ route('admin.exam_package.index') }}"
                                        class="btn btn-sm btn-outline-primary rounded-pill px-4 shadow-sm ml-2">
                                        Paket Soal
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Step 3: Pengaturan Jadwal --}}
                        <div class="timeline-item">
                            <div class="timeline-badge bg-white text-cyan-600 border border-cyan-100">
                                <i class="fas fa-calendar-alt text-lg"></i>
                            </div>
                            <div class="timeline-content border-l-500-cyan">
                                <span
                                    class="badge badge-light text-cyan-600 font-weight-bold badge-step border border-cyan-100">Tahap
                                    3</span>
                                <h5 class="font-weight-bold text-gray-800 mb-2">Penjadwalan Ujian (Sesi)</h5>
                                <p class="text-gray-600 mb-3 text-sm">Menentukan kapan ujian dilaksanakan dan siapa
                                    pesertanya.</p>

                                <button
                                    class="btn btn-sm btn-light text-cyan-600 font-weight-bold mb-3 rounded-pill px-3 shadow-sm"
                                    type="button" data-toggle="collapse" data-target="#step3">
                                    <i class="fas fa-chevron-down mr-1"></i> Lihat Detail
                                </button>

                                <div class="collapse mb-3" id="step3">
                                    <div class="bg-cyan-50 p-4 rounded-lg border border-cyan-100">
                                        <h6 class="font-weight-bold text-cyan-600 mb-2">Poin Penting:</h6>
                                        <ul class="text-sm text-gray-700 space-y-2 mb-0 pl-3">
                                            <li><strong>Jenis Ujian:</strong> Tentukan apakah ini UTS, UAS, Quiz, atau
                                                Tryout.</li>
                                            <li><strong>Waktu & Durasi:</strong> Set tanggal mulai, tanggal selesai, dan
                                                durasi pengerjaan (menit).</li>
                                            <li><strong>Token:</strong> Jika aktif, token akan digenerate otomatis. Siswa
                                                butuh token ini untuk masuk.</li>
                                            <li><strong>Tampilkan Nilai:</strong> Atur apakah siswa bisa melihat nilai
                                                langsung setelah selesai atau tidak.</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="border-t pt-3 mt-2">
                                    <a href="{{ route('admin.exam_session.create') }}"
                                        class="btn btn-sm btn-info text-white rounded-pill px-4 shadow-sm">
                                        <i class="fas fa-calendar-plus mr-1"></i> Buat Jadwal Baru
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Step 4: Pelaksanaan & Monitoring --}}
                        <div class="timeline-item">
                            <div class="timeline-badge bg-white text-yellow-600 border border-yellow-100">
                                <i class="fas fa-desktop text-lg"></i>
                            </div>
                            <div class="timeline-content border-l-500-yellow">
                                <span
                                    class="badge badge-light text-yellow-600 font-weight-bold badge-step border border-yellow-100">Tahap
                                    4</span>
                                <h5 class="font-weight-bold text-gray-800 mb-2">Pelaksanaan & Monitoring Ujian</h5>
                                <p class="text-gray-600 mb-3 text-sm">Memantau aktivitas siswa secara realtime saat ujian
                                    berlangsung.</p>

                                <button
                                    class="btn btn-sm btn-light text-yellow-600 font-weight-bold mb-3 rounded-pill px-3 shadow-sm"
                                    type="button" data-toggle="collapse" data-target="#step4">
                                    <i class="fas fa-chevron-down mr-1"></i> Lihat Detail
                                </button>

                                <div class="collapse mb-3" id="step4">
                                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                                        <ul class="text-sm text-gray-700 space-y-2 mb-0 pl-3">
                                            <li><i class="fas fa-eye text-yellow-600 mr-2"></i><strong>Live
                                                    Monitoring:</strong> Lihat siapa yang sedang mengerjakan, selesai, atau
                                                belum login.</li>
                                            <li><i class="fas fa-undo text-yellow-600 mr-2"></i><strong>Reset
                                                    Login:</strong> Jika siswa terputus atau ganti perangkat, admin bisa
                                                mereset status login mereka.</li>
                                            <li><i class="fas fa-ban text-yellow-600 mr-2"></i><strong>Hentikan
                                                    Paksa:</strong> Admin dapat menghentikan ujian siswa jika terjadi
                                                pelanggaran.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 5: Koreksi & Laporan --}}
                        <div class="timeline-item">
                            <div class="timeline-badge bg-white text-green-600 border border-green-100">
                                <i class="fas fa-check-double text-lg"></i>
                            </div>
                            <div class="timeline-content border-l-500-green">
                                <span
                                    class="badge badge-light text-green-600 font-weight-bold badge-step border border-green-100">Tahap
                                    5</span>
                                <h5 class="font-weight-bold text-gray-800 mb-2">Koreksi & Laporan Hasil</h5>
                                <p class="text-gray-600 mb-3 text-sm">Tahap akhir untuk mengolah nilai dan mencetak laporan.
                                </p>

                                <button
                                    class="btn btn-sm btn-light text-green-600 font-weight-bold mb-3 rounded-pill px-3 shadow-sm"
                                    type="button" data-toggle="collapse" data-target="#step5">
                                    <i class="fas fa-chevron-down mr-1"></i> Lihat Detail
                                </button>

                                <div class="collapse mb-3" id="step5">
                                    <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                                        <ul class="text-sm text-gray-700 space-y-2 mb-0 pl-3">
                                            <li>
                                                <strong>Koreksi Esai:</strong> Soal Pilihan Ganda dinilai otomatis. Soal
                                                Esai harus diperiksa dan diberi nilai manual oleh guru/admin di menu
                                                <em>Koreksi</em>.
                                            </li>
                                            <li>
                                                <strong>Rekap Nilai:</strong> Download hasil ujian seluruh siswa dalam
                                                format Excel/PDF.
                                            </li>
                                            <li>
                                                <strong>Cetak Hasil:</strong> Cetak lembar hasil individu atau daftar hadir
                                                ujian.
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="border-t pt-3 mt-2 d-flex flex-wrap gap-2">
                                    <a href="{{ route('admin.correction.index') }}"
                                        class="btn btn-sm btn-outline-success rounded-pill px-3">Koreksi Esai</a>
                                    <a href="{{ route('admin.recap.exam_result') }}"
                                        class="btn btn-sm btn-outline-success rounded-pill px-3">Rekap Nilai</a>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="mt-5 text-center">
                        <p class="text-gray-500 text-sm">Butuh bantuan lebih lanjut? Hubungi Super Admin atau cek
                            dokumentasi teknis.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection