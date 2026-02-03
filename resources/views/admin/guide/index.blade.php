@extends('layouts.admin.app')

@section('title', 'Panduan Sistem')
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
        margin-bottom: 50px;
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
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
    }
    .timeline-content {
        margin-left: 100px;
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
    }
    .timeline-content:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
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
    /* Colors */
    .border-l-500-purple { border-left: 5px solid #8b5cf6; }
    .border-l-500-blue { border-left: 5px solid #3b82f6; }
    .border-l-500-green { border-left: 5px solid #22c55e; }
    .border-l-500-orange { border-left: 5px solid #f97316; }
    
    .text-purple-600 { color: #8b5cf6; }
    .text-blue-600 { color: #3b82f6; }
    .text-green-600 { color: #22c55e; }
    .text-orange-600 { color: #f97316; }

    .bg-purple-100 { background-color: #f3e8ff; }
    .bg-blue-100 { background-color: #dbeafe; }
    .bg-green-100 { background-color: #dcfce7; }
    .bg-orange-100 { background-color: #ffedd5; }
</style>

<div class="row justify-content-center">
    <div class="col-lg-10 col-12">
        <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="card-header bg-gradient-to-r from-purple-600 to-indigo-600 p-5 border-0 text-center">
                <h3 class="card-title text-white font-weight-bold mb-2">
                    <i class="fas fa-rocket mr-2"></i> Panduan Sukses Sistem Ujian
                </h3>
                <p class="text-white opacity-90 mb-0 font-weight-light">4 Langkah Mudah Menjalankan Ujian Berbasis Komputer</p>
            </div>
            
            <div class="card-body bg-light p-4 p-md-5">
                <div class="timeline">
                    
                    {{-- Step 1 --}}
                    <div class="timeline-item">
                        <div class="timeline-badge bg-white text-purple-600 border border-purple-100">
                            <span class="text-2xl">01</span>
                        </div>
                        <div class="timeline-content border-l-500-purple">
                            <h5 class="font-weight-bold text-gray-800 mb-2">
                                <i class="fas fa-database text-purple-600 mr-2"></i> Persiapan Data Master
                            </h5>
                            <p class="text-gray-600 mb-3 text-sm">Fondasi utama sistem. Pastikan data mata pelajaran, ruangan, dan siswa sudah lengkap dan valid.</p>
                            
                            <button class="btn btn-sm btn-light text-purple-600 font-weight-bold mb-3 rounded-pill px-3 shadow-sm hover:bg-purple-100 transition" type="button" data-toggle="collapse" data-target="#detailStep1">
                                <i class="fas fa-chevron-down mr-1"></i> Detail & Tips
                            </button>

                            <div class="collapse mb-3" id="detailStep1">
                                <div class="bg-purple-50 p-3 rounded-lg border border-purple-100">
                                    <ul class="pl-3 text-sm text-gray-700 mb-0 space-y-1">
                                        <li><i class="fas fa-check-circle text-purple-600 text-xs mr-1"></i> <strong>Mapel:</strong> Wajib ada sebagai pengelompokan soal.</li>
                                        <li><i class="fas fa-check-circle text-purple-600 text-xs mr-1"></i> <strong>Siswa:</strong> Gunakan fitur <strong>Import Excel</strong> untuk mass-upload. Pastikan NIS/Username unik.</li>
                                        <li><i class="fas fa-check-circle text-purple-600 text-xs mr-1"></i> <strong>Ruangan:</strong> Membantu saat mencetak kartu peserta/absen per kelas.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="border-t pt-3 mt-2 d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.subject.index') }}" class="btn btn-xs btn-outline-dark rounded-pill px-3">Mapel</a>
                                <a href="{{ route('admin.exam_room.index') }}" class="btn btn-xs btn-outline-dark rounded-pill px-3">Ruangan</a>
                                <a href="{{ route('admin.student.index') }}" class="btn btn-xs btn-outline-primary rounded-pill px-3 font-weight-bold">Data Siswa</a>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="timeline-item">
                        <div class="timeline-badge bg-white text-blue-600 border border-blue-100">
                            <span class="text-2xl">02</span>
                        </div>
                        <div class="timeline-content border-l-500-blue">
                            <h5 class="font-weight-bold text-gray-800 mb-2">
                                <i class="fas fa-archive text-blue-600 mr-2"></i> Manajemen Bank Soal
                            </h5>
                            <p class="text-gray-600 mb-3 text-sm">Inti dari ujian. Input soal pilihan ganda atau essay dengan mudah.</p>
                            
                            <button class="btn btn-sm btn-light text-blue-600 font-weight-bold mb-3 rounded-pill px-3 shadow-sm hover:bg-blue-100 transition" type="button" data-toggle="collapse" data-target="#detailStep2">
                                <i class="fas fa-chevron-down mr-1"></i> Detail & Tips
                            </button>

                            <div class="collapse mb-3" id="detailStep2">
                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                                    <ul class="pl-3 text-sm text-gray-700 mb-0 space-y-1">
                                        <li><i class="fas fa-info-circle text-blue-600 text-xs mr-1"></i> <strong>Import Word (.docx):</strong> Cara termudah! Download template, isi tabel, upload.</li>
                                        <li><i class="fas fa-info-circle text-blue-600 text-xs mr-1"></i> <strong>Manual:</strong> Gunakan editor untuk soal matematika (MathJax) atau gambar kompleks.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="border-t pt-3 mt-2">
                                <a href="{{ route('admin.question.index') }}" class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm">
                                    <i class="fas fa-plus-circle mr-1"></i> Kelola Bank Soal
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="timeline-item">
                        <div class="timeline-badge bg-white text-green-600 border border-green-100">
                            <span class="text-2xl">03</span>
                        </div>
                        <div class="timeline-content border-l-500-green">
                            <h5 class="font-weight-bold text-gray-800 mb-2">
                                <i class="fas fa-clock text-green-600 mr-2"></i> Jadwal & Setup Ujian
                            </h5>
                            <p class="text-gray-600 mb-3 text-sm">Menentukan "Kapan", "Siapa", dan "Apa" yang diujikan.</p>
                            
                            <button class="btn btn-sm btn-light text-green-600 font-weight-bold mb-3 rounded-pill px-3 shadow-sm hover:bg-green-100 transition" type="button" data-toggle="collapse" data-target="#detailStep3">
                                <i class="fas fa-chevron-down mr-1"></i> Detail & Tips
                            </button>

                            <div class="collapse mb-3" id="detailStep3">
                                <div class="bg-green-50 p-3 rounded-lg border border-green-100">
                                    <ul class="pl-3 text-sm text-gray-700 mb-0 space-y-1">
                                        <li><i class="fas fa-magic text-green-600 text-xs mr-1"></i> <strong>Sesi Ujian:</strong> Hubungkan Mapel, Bank Soal, dan Kelas Peserta di sini.</li>
                                        <li><i class="fas fa-key text-green-600 text-xs mr-1"></i> <strong>Token:</strong> Jika diaktifkan, token dinamis wajib dimasukkan siswa saat mulai.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="border-t pt-3 mt-2">
                                <a href="{{ route('admin.exam_session.index') }}" class="btn btn-sm btn-success rounded-pill px-4 shadow-sm">
                                    <i class="fas fa-calendar-check mr-1"></i> Buat Sesi Baru
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Step 4 --}}
                    <div class="timeline-item">
                        <div class="timeline-badge bg-white text-orange-600 border border-orange-100">
                            <span class="text-2xl">04</span>
                        </div>
                        <div class="timeline-content border-l-500-orange">
                            <h5 class="font-weight-bold text-gray-800 mb-2">
                                <i class="fas fa-flag-checkered text-orange-600 mr-2"></i> Pelaksanaan & Pelaporan
                            </h5>
                            <p class="text-gray-600 mb-3 text-sm">Tahap akhir: Siswa mengerjakan, Admin memantau dan cetak hasil.</p>
                            
                            <button class="btn btn-sm btn-light text-orange-600 font-weight-bold mb-3 rounded-pill px-3 shadow-sm hover:bg-orange-100 transition" type="button" data-toggle="collapse" data-target="#detailStep4">
                                <i class="fas fa-chevron-down mr-1"></i> Detail & Tips
                            </button>

                            <div class="collapse mb-3" id="detailStep4">
                                <div class="bg-orange-50 p-3 rounded-lg border border-orange-100">
                                    <ul class="pl-3 text-sm text-gray-700 mb-0 space-y-1">
                                        <li><i class="fas fa-print text-orange-600 text-xs mr-1"></i> <strong>Kartu Ujian:</strong> Print lalu bagikan agar siswa tahu username/pass mereka.</li>
                                        <li><i class="fas fa-poll text-orange-600 text-xs mr-1"></i> <strong>Hasil:</strong> Realtime. Untuk soal Essay, wajib 'Koreksi Manual' dulu agar nilai muncul.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="border-t pt-3 mt-2 d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.student.cards') }}" class="btn btn-xs btn-outline-warning rounded-pill px-3 font-weight-bold text-dark">Cetak Kartu</a>
                                <a href="{{ route('admin.recap.exam_result') }}" class="btn btn-xs btn-outline-danger rounded-pill px-3">Lihat Hasil</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
