@extends('layouts.admin.app')

@section('title', 'Panduan Sistem Lengkap')
@section('page_title', 'Buku Panduan Penggunaan Sistem')

@push('styles')
<style>
    /* Documentation Layout Styles */
    .doc-sidebar {
        position: sticky;
        top: 80px;
        height: calc(100vh - 100px);
        overflow-y: auto;
    }
    
    .doc-sidebar .list-group-item {
        border: none;
        padding: 0.75rem 1.25rem;
        border-radius: 8px;
        margin-bottom: 2px;
        color: #4b5563;
        transition: all 0.2s ease;
    }
    
    .doc-sidebar .list-group-item:hover {
        background-color: #f3f4f6;
        color: #2563eb;
    }
    
    .doc-sidebar .list-group-item.active {
        background-color: #eff6ff;
        color: #2563eb;
        font-weight: 700;
        border-left: 4px solid #2563eb;
    }
    
    .doc-content h2 {
        font-size: 1.8rem;
        font-weight: 800;
        color: #1e293b;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #f1f5f9;
    }

    .doc-content h2:first-child {
        margin-top: 0;
    }
    
    .doc-content h4 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #334155;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    
    .doc-content p, .doc-content li {
        font-size: 1rem;
        line-height: 1.7;
        color: #475569;
    }
    
    .doc-alert {
        border-left: 4px solid;
        border-radius: 8px;
    }
    
    .doc-alert-info {
        background-color: #eff6ff;
        border-color: #3b82f6;
        color: #1e3a8a;
    }
    
    .doc-alert-warning {
        background-color: #fffbeb;
        border-color: #f59e0b;
        color: #92400e;
    }

    .doc-step-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        transition: transform 0.2s ease;
    }
    
    .doc-step-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .step-number {
        background: #2563eb;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 10px;
    }
</style>
@endpush

@section('content')
<div class="row">
    <!-- Sidebar Navigation -->
    <div class="col-lg-3 col-md-4 d-none d-md-block">
        <div class="card border-0 shadow-sm rounded-lg doc-sidebar">
            <div class="card-body p-3">
                <h6 class="font-weight-bold text-uppercase text-muted mb-3 px-3" style="font-size: 0.8rem; letter-spacing: 1px;">Daftar Isi</h6>
                <div class="list-group list-group-flush" id="guide-nav">
                    <a class="list-group-item list-group-item-action active" href="#pengenalan">
                        <i class="fas fa-home w-20px mr-2 text-center"></i> Pengenalan
                    </a>
                    <a class="list-group-item list-group-item-action" href="#data-master">
                        <i class="fas fa-database w-20px mr-2 text-center"></i> Data Master
                    </a>
                    <a class="list-group-item list-group-item-action" href="#bank-soal">
                        <i class="fas fa-archive w-20px mr-2 text-center"></i> Bank & Paket Soal
                    </a>
                    <a class="list-group-item list-group-item-action" href="#jadwal-ujian">
                        <i class="fas fa-calendar-alt w-20px mr-2 text-center"></i> Jadwal Ujian
                    </a>
                    <a class="list-group-item list-group-item-action" href="#pelaksanaan">
                        <i class="fas fa-desktop w-20px mr-2 text-center"></i> Pelaksanaan & Monitor
                    </a>
                    <a class="list-group-item list-group-item-action" href="#evaluasi">
                        <i class="fas fa-chart-line w-20px mr-2 text-center"></i> Evaluasi & Laporan
                    </a>
                    <a class="list-group-item list-group-item-action" href="#cetak">
                        <i class="fas fa-print w-20px mr-2 text-center"></i> Cetak Administrasi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-lg-9 col-md-8">
        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-body p-4 p-lg-5 doc-content" data-spy="scroll" data-target="#guide-nav" data-offset="100">
                
                <!-- 1. Pengenalan -->
                <section id="pengenalan">
                    <h2><i class="fas fa-home text-primary mr-2"></i> Pengenalan Sistem</h2>
                    <p class="lead text-dark font-weight-normal">Selamat datang di Buku Panduan <strong>E-Ujian Pro</strong>. Sistem ini dirancang untuk memudahkan sekolah dalam melaksanakan ujian berbasis komputer secara profesional, transparan, dan terstruktur.</p>
                    
                    <div class="alert doc-alert doc-alert-info p-4 d-flex align-items-start mt-4">
                        <i class="fas fa-lightbulb fa-2x mr-3 mt-1"></i>
                        <div>
                            <h5 class="font-weight-bold mb-1">Alur Kerja (Workflow) Cepat</h5>
                            <p class="mb-0">Secara garis besar, proses ujian terdiri dari 4 langkah: <strong>1. Siapkan Data Master</strong> &rarr; <strong>2. Buat Bank Soal & Paket</strong> &rarr; <strong>3. Buat Jadwal Sesi</strong> &rarr; <strong>4. Siswa Ujian & Guru Rekap Nilai</strong>.</p>
                        </div>
                    </div>
                </section>

                <!-- 2. Data Master -->
                <section id="data-master">
                    <h2><i class="fas fa-database text-primary mr-2"></i> 1. Persiapan Data Master</h2>
                    <p>Sebelum memulai ujian apapun, pastikan seluruh data dasar sudah diinput dengan benar. Data master adalah fondasi dari aplikasi ini.</p>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="doc-step-box h-100">
                                <h4><i class="fas fa-users text-indigo-500 mr-2"></i> Kelas/Rombel</h4>
                                <p>Kelola daftar kelas yang ada di sekolah (Misal: X IPA 1, XII IPS 2). Kelas digunakan untuk mengelompokkan siswa.</p>
                                <a href="{{ route('admin.student_group.index') }}" class="btn btn-sm btn-light border shadow-sm">Kelola Kelas <i class="fas fa-external-link-alt ml-1 text-xs"></i></a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="doc-step-box h-100">
                                <h4><i class="fas fa-book text-green-500 mr-2"></i> Mata Pelajaran & KKM</h4>
                                <p>Input nama mata pelajaran dan nilai <strong>KKM (Kriteria Ketuntasan Minimal)</strong>. Nilai KKM akan otomatis mewarnai nilai siswa (Merah jika di bawah KKM).</p>
                                <a href="{{ route('admin.subject.index') }}" class="btn btn-sm btn-light border shadow-sm">Kelola Mapel <i class="fas fa-external-link-alt ml-1 text-xs"></i></a>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <div class="doc-step-box h-100">
                                <h4><i class="fas fa-door-open text-orange-500 mr-2"></i> Ruangan Ujian</h4>
                                <p>Daftarkan ruangan ujian (Misal: Lab Komputer 1, Ruang 01). Ruangan sangat berguna untuk <strong>Cetak Kartu Ujian</strong> dan <strong>Daftar Hadir</strong> berdasarkan letak fisik siswa.</p>
                                <a href="{{ route('admin.exam_room.index') }}" class="btn btn-sm btn-light border shadow-sm">Kelola Ruang <i class="fas fa-external-link-alt ml-1 text-xs"></i></a>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <div class="doc-step-box h-100 border-primary shadow-sm" style="background-color: #f8fbff;">
                                <h4><i class="fas fa-user-graduate text-primary mr-2"></i> Data Siswa</h4>
                                <p>Pastikan NIS/Username unik. Gunakan fitur <strong>Import Excel</strong> untuk memasukkan ratusan data siswa sekaligus secara cepat. Jangan lupa petakan siswa ke Kelas dan Ruangan yang tepat.</p>
                                <a href="{{ route('admin.student.index') }}" class="btn btn-sm btn-primary shadow-sm">Kelola Siswa <i class="fas fa-external-link-alt ml-1 text-xs"></i></a>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 3. Bank Soal & Paket -->
                <section id="bank-soal">
                    <h2><i class="fas fa-archive text-primary mr-2"></i> 2. Bank Soal & Paket Soal</h2>
                    <p>Setelah Data Master siap, tahap selanjutnya adalah membuat butir-butir soal dan merakitnya menjadi Paket Soal (misal: Paket A, Paket B).</p>
                    
                    <div class="doc-step-box">
                        <h4 class="mt-0"><span class="step-number">A</span> Input Bank Soal</h4>
                        <p>Ada dua metode utama untuk memasukkan soal:</p>
                        <ul class="mb-3">
                            <li><strong>Metode Manual (Editor):</strong> Cocok untuk soal yang butuh ketelitian tinggi, rumus matematika yang kompleks, atau format tabel spesifik.</li>
                            <li><strong>Metode Import Word/Excel:</strong> <em>Sangat Disarankan!</em> Gunakan format template yang disediakan. Sistem bisa membaca soal, gambar, dan tabel langsung dari file Word `.docx` Anda secara otomatis.</li>
                        </ul>
                        <div class="alert doc-alert doc-alert-warning py-2 px-3 mb-0 text-sm">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Tipe Soal yang didukung: Pilihan Ganda (Tunggal & Kompleks), Benar/Salah, Menjodohkan, dan Uraian/Esai.
                        </div>
                    </div>

                    <div class="doc-step-box">
                        <h4 class="mt-0"><span class="step-number">B</span> Merakit Paket Soal</h4>
                        <p>Soal tidak bisa diujikan langsung. Anda harus membuat <strong>Paket Soal</strong> terlebih dahulu, lalu memasukkan butir-butir soal yang ada di Bank Soal ke dalam paket tersebut.</p>
                        <p class="mb-2"><strong>Tips:</strong> Anda bisa mengatur agar soal dalam paket tersebut ditampilkan secara <em>Acak (Shuffle)</em> untuk meminimalisir kecurangan siswa yang duduk bersebelahan.</p>
                        <a href="{{ route('admin.exam_package.index') }}" class="btn btn-sm btn-light border shadow-sm">Ke Manajemen Paket <i class="fas fa-arrow-right ml-1"></i></a>
                    </div>
                </section>

                <!-- 4. Jadwal Ujian -->
                <section id="jadwal-ujian">
                    <h2><i class="fas fa-calendar-alt text-primary mr-2"></i> 3. Penjadwalan Ujian (Sesi)</h2>
                    <p>Paket soal yang sudah siap, harus di-jadwalkan (dibuatkan Sesi Ujian) agar bisa muncul di dashboard siswa.</p>

                    <div class="p-4 bg-light rounded-lg border">
                        <h5 class="font-weight-bold mb-3">Parameter Penting Sesi Ujian:</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="font-weight-bold text-dark"><i class="far fa-clock text-primary mr-2"></i>Waktu & Durasi</h6>
                                <p class="text-sm text-muted">Tentukan Waktu Mulai dan Waktu Selesai. Siswa hanya bisa login di antara rentang waktu ini. Durasi (dalam menit) adalah batas waktu pengerjaan sejak siswa klik tombol "Mulai".</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="font-weight-bold text-dark"><i class="fas fa-key text-primary mr-2"></i>Sistem Token</h6>
                                <p class="text-sm text-muted">Jika diaktifkan, token acak akan di-generate. Pengawas perlu membagikan token ini di papan tulis kelas agar siswa bisa masuk ke ruang ujian.</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="font-weight-bold text-dark"><i class="fas fa-eye text-primary mr-2"></i>Tampilkan Nilai</h6>
                                <p class="text-sm text-muted">Atur apakah siswa dapat melihat hasil dan skor mereka langsung setelah mereka mengakhiri ujian, atau dirahasiakan.</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="font-weight-bold text-dark"><i class="fas fa-users text-primary mr-2"></i>Target Kelas</h6>
                                <p class="text-sm text-muted">Sesi ujian ini dapat dikhususkan hanya untuk Kelas tertentu, atau dibiarkan kosong agar berlaku untuk semua kelas.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 5. Pelaksanaan & Monitor -->
                <section id="pelaksanaan">
                    <h2><i class="fas fa-desktop text-primary mr-2"></i> 4. Pelaksanaan & Live Monitor</h2>
                    <p>Saat ujian berlangsung, Admin dan Pengawas dapat melakukan pengawasan secara <em>Real-time</em> melalui halaman <strong>Jadwal Ujian Aktif</strong>.</p>
                    
                    <div class="doc-step-box">
                        <h4 class="mt-0">Status Ujian Siswa</h4>
                        <ul class="mb-3">
                            <li><span class="badge badge-secondary px-2 py-1">Belum Mengerjakan</span> Siswa belum login atau belum memasukkan token.</li>
                            <li><span class="badge badge-warning text-white px-2 py-1">Sedang Mengerjakan</span> Siswa sedang berada di dalam halaman pengerjaan soal.</li>
                            <li><span class="badge badge-success px-2 py-1">Selesai</span> Siswa telah mengklik tombol "Selesai" atau waktu ujian habis.</li>
                        </ul>
                        
                        <h4 class="mt-4">Fitur Tindakan Darurat</h4>
                        <table class="table table-sm table-bordered bg-white">
                            <thead class="bg-light">
                                <tr>
                                    <th>Aksi</th>
                                    <th>Kapan digunakan?</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="font-weight-bold"><i class="fas fa-undo text-warning mr-1"></i> Reset Status</td>
                                    <td>Ketika HP/Laptop siswa mati mendadak, atau browser tertutup tanpa sengaja. Reset status agar mereka bisa login dan melanjutkan sisa waktunya.</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold"><i class="fas fa-ban text-danger mr-1"></i> Force Submit</td>
                                    <td>Memaksa ujian siswa selesai secara sepihak dari server (berguna jika siswa lupa klik Selesai lalu pulang, atau terindikasi curang).</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- 6. Evaluasi & Laporan -->
                <section id="evaluasi">
                    <h2><i class="fas fa-chart-line text-primary mr-2"></i> 5. Evaluasi & Laporan Nilai</h2>
                    <p>Bagian terpenting bagi Guru. Sistem menyediakan instrumen laporan yang sangat komprehensif setelah ujian selesai.</p>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-success shadow-sm">
                                <div class="card-body">
                                    <h5 class="font-weight-bold text-success"><i class="fas fa-check-double mr-1"></i> Koreksi Esai</h5>
                                    <p class="text-sm">Soal Objektif dinilai otomatis. Gunakan menu ini untuk membaca jawaban esai siswa dan memberikan skor manual dengan cepat.</p>
                                    <a href="{{ route('admin.correction.index') }}" class="btn btn-sm btn-outline-success btn-block mt-3">Mulai Koreksi</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-info shadow-sm">
                                <div class="card-body">
                                    <h5 class="font-weight-bold text-info"><i class="fas fa-list-ol mr-1"></i> Rekap Nilai</h5>
                                    <p class="text-sm">Menampilkan tabel nilai akhir keseluruhan siswa. Terdapat indikator warna (Tuntas/Belum Tuntas) berdasarkan <strong>KKM</strong>. Bisa di-export ke Excel/PDF.</p>
                                    <a href="{{ route('admin.recap.exam_result') }}" class="btn btn-sm btn-outline-info btn-block mt-3">Lihat Rekap</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-primary shadow-sm">
                                <div class="card-body">
                                    <h5 class="font-weight-bold text-primary"><i class="fas fa-chart-pie mr-1"></i> Analisis Butir Soal</h5>
                                    <p class="text-sm">Sangat berguna untuk evaluasi guru! Melihat persentase benar/salah. Klik nama siswa untuk melihat <strong>Detail Kertas Ujian</strong> mereka.</p>
                                    <a href="{{ route('admin.recap.item_analysis') }}" class="btn btn-sm btn-outline-primary btn-block mt-3">Buka Analisis</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 7. Cetak Administrasi -->
                <section id="cetak">
                    <h2><i class="fas fa-print text-primary mr-2"></i> Cetak Kelengkapan Administrasi</h2>
                    <p>Sistem E-Ujian Pro memudahkan panitia ujian dalam menyiapkan berkas fisik. Semua berkas di-generate dalam bentuk PDF siap cetak dengan ukuran kertas standar (A4/F4).</p>

                    <ul class="list-group shadow-sm border-0">
                        <li class="list-group-item border-left-0 border-right-0 border-top-0 py-3">
                            <h5 class="font-weight-bold text-dark mb-1"><i class="fas fa-id-card text-muted mr-2"></i> Kartu Peserta Ujian</h5>
                            <p class="mb-0 text-sm text-gray-600">Berisi pas foto siswa, NIS, Username, dan Password (Barcode). Dapat difilter berdasarkan Ruangan, Kelas, atau individual.</p>
                        </li>
                        <li class="list-group-item border-left-0 border-right-0 py-3">
                            <h5 class="font-weight-bold text-dark mb-1"><i class="fas fa-clipboard-list text-muted mr-2"></i> Daftar Hadir Peserta</h5>
                            <p class="mb-0 text-sm text-gray-600">Otomatis mencetak form daftar hadir per-Ruangan yang lengkap dengan kolom tanda tangan siswa. Tinggal bagikan ke pengawas ruang.</p>
                        </li>
                        <li class="list-group-item border-left-0 border-right-0 border-bottom-0 py-3">
                            <h5 class="font-weight-bold text-dark mb-1"><i class="fas fa-file-signature text-muted mr-2"></i> Berita Acara Ujian</h5>
                            <p class="mb-0 text-sm text-gray-600">Form resmi berita acara pelaksanaan per mata pelajaran untuk ditandatangani oleh Proktor/Pengawas.</p>
                        </li>
                    </ul>
                </section>
                
                <hr class="my-5">
                <div class="text-center text-muted text-sm pb-4">
                    <i class="fas fa-code mr-1"></i> E-Ujian Pro V.1.0 &copy; {{ date('Y') }} - Dikembangkan untuk Pendidikan Masa Depan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Simple scrollspy implementation for sidebar
        $(window).on('scroll', function() {
            var scrollPos = $(document).scrollTop();
            $('#guide-nav a').each(function() {
                var currLink = $(this);
                var refElement = $(currLink.attr("href"));
                if (refElement.position().top - 150 <= scrollPos && refElement.position().top + refElement.height() > scrollPos) {
                    $('#guide-nav a').removeClass("active");
                    currLink.addClass("active");
                }
            });
        });
        
        // Smooth scrolling
        $("#guide-nav a").on('click', function(event) {
            if (this.hash !== "") {
                event.preventDefault();
                var hash = this.hash;
                $('html, body').animate({
                    scrollTop: $(hash).offset().top - 80
                }, 600);
            }
        });
    });
</script>
@endpush