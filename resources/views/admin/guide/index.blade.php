@extends('layouts.admin.app')

@section('title', 'Panduan Sistem')
@section('page_title', 'Panduan Penggunaan Sistem')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="card-header bg-gradient-to-r from-purple-600 to-indigo-600 p-5 border-0">
                <h3 class="card-title text-white font-weight-bold mb-0">
                    <i class="fas fa-map-signs mr-2"></i> Alur Jalan Sistem Ujian
                </h3>
                <p class="text-white opacity-75 mb-0 mt-1">Ikuti langkah-langkah berikut untuk menjalankan ujian dengan lancar.</p>
            </div>
            <div class="card-body bg-light p-5">
                
                {{-- Step 1: Data Master --}}
                <div class="row align-items-start mb-5">
                    <div class="col-md-2 text-center">
                        <div class="bg-white rounded-circle shadow-lg d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                            <span class="text-3xl font-weight-bold text-purple-600">1</span>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="bg-white p-4 rounded-lg shadow-sm border-left-4 border-purple-500">
                            <h5 class="font-weight-bold text-gray-800 mb-2">Persiapan Data Master</h5>
                            <p class="text-gray-600 mb-2">Siapkan data dasar sebelum membuat ujian.</p>
                            
                            <button class="btn btn-link p-0 mb-3 text-purple-600 font-weight-bold" type="button" data-toggle="collapse" data-target="#detailStep1">
                                <i class="fas fa-info-circle mr-1"></i> Baca Detail Panduan
                            </button>

                            <div class="collapse mb-3" id="detailStep1">
                                <div class="card card-body bg-gray-50 border-0">
                                    <ul class="pl-3 text-sm text-gray-700">
                                        <li><strong>Mata Pelajaran:</strong> Wajib dibuat pertama kali. Contoh: Matematika, Bahasa Indo.</li>
                                        <li><strong>Ruangan & Sesi:</strong> Opsional tapi disarankan untuk kerapian.</li>
                                        <li><strong>Siswa:</strong> Bisa diinput manual atau <strong>Import Excel</strong>. 
                                            <br><em>Tips: Saat import, pastikan format sesuai template. Kolom NIS harus unik (dijadikan username login).</em>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.subject.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-book mr-1"></i> 1. Buat Mata Pelajaran
                                </a>
                                <a href="{{ route('admin.exam_room.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-door-open mr-1"></i> 2. Buat Ruangan
                                </a>
                                <a href="{{ route('admin.student.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-users mr-1"></i> 3. Import / Tambah Siswa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Arrow Connector --}}
                <div class="row mb-5">
                    <div class="col-md-2 text-center">
                        <i class="fas fa-arrow-down text-gray-300 text-3xl"></i>
                    </div>
                </div>

                {{-- Step 2: Bank Soal --}}
                <div class="row align-items-start mb-5">
                    <div class="col-md-2 text-center">
                        <div class="bg-white rounded-circle shadow-lg d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                            <span class="text-3xl font-weight-bold text-blue-600">2</span>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="bg-white p-4 rounded-lg shadow-sm border-left-4 border-blue-500">
                            <h5 class="font-weight-bold text-gray-800 mb-2">Siapkan Bank Soal</h5>
                            <p class="text-gray-600 mb-2">Input soal-soal yang akan diujikan. Bisa manual atau import Excel/Word.</p>
                            
                            <button class="btn btn-link p-0 mb-3 text-blue-600 font-weight-bold" type="button" data-toggle="collapse" data-target="#detailStep2">
                                <i class="fas fa-info-circle mr-1"></i> Baca Detail Panduan
                            </button>

                            <div class="collapse mb-3" id="detailStep2">
                                <div class="card card-body bg-gray-50 border-0">
                                    <ul class="pl-3 text-sm text-gray-700">
                                        <li><strong>Import Word (.docx):</strong> Fitur baru! Buat tabel di Word dengan kolom [No, Soal, Jenis, Opsi A-E, Jawaban]. 
                                            <br><em>Klik tombol 'Download Template Word' di menu Import.</em>
                                        </li>
                                        <li><strong>Import Excel:</strong> Lebih cepat untuk data banyak. Gunakan template yang disediakan.</li>
                                        <li><strong>Manual:</strong> Cocok untuk edit soal satu per satu atau menyisipkan gambar/audio.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.question.index') }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                    <i class="fas fa-database mr-1"></i> 1. Bank Soal
                                </a>
                                <span class="text-muted text-sm align-self-center mx-2">-> Import / Buat Baru</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Arrow Connector --}}
                <div class="row mb-5">
                    <div class="col-md-2 text-center">
                        <i class="fas fa-arrow-down text-gray-300 text-3xl"></i>
                    </div>
                </div>

                {{-- Step 3: Setting Ujian --}}
                <div class="row align-items-start mb-5">
                    <div class="col-md-2 text-center">
                        <div class="bg-white rounded-circle shadow-lg d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                            <span class="text-3xl font-weight-bold text-green-600">3</span>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="bg-white p-4 rounded-lg shadow-sm border-left-4 border-green-500">
                            <h5 class="font-weight-bold text-gray-800 mb-2">Buat Jadwal & Sesi Ujian</h5>
                            <p class="text-gray-600 mb-2">Atur kapan ujian dilaksanakan dan siapa pesertanya.</p>
                            
                            <button class="btn btn-link p-0 mb-3 text-green-600 font-weight-bold" type="button" data-toggle="collapse" data-target="#detailStep3">
                                <i class="fas fa-info-circle mr-1"></i> Baca Detail Panduan
                            </button>

                            <div class="collapse mb-3" id="detailStep3">
                                <div class="card card-body bg-gray-50 border-0">
                                    <ul class="pl-3 text-sm text-gray-700">
                                        <li><strong>Buat Sesi:</strong> Tentukan Nama Ujian, Waktu Mulai/Selesai, dan Durasi.</li>
                                        <li><strong>Pilih Soal:</strong> Kaitkan dengan bank soal yang sudah dibuat.</li>
                                        <li><strong>Token:</strong> Setelah sesi dibuat, klik detail untuk melihat/generate <strong>Token Masuk</strong> jika ujian diproteksi token.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.exam_session.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                    <i class="fas fa-calendar-alt mr-1"></i> 1. Buat Sesi Ujian
                                </a>
                                <span class="text-muted text-sm align-self-center mx-2">-> Generate Token Masuk</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Arrow Connector --}}
                <div class="row mb-5">
                    <div class="col-md-2 text-center">
                        <i class="fas fa-arrow-down text-gray-300 text-3xl"></i>
                    </div>
                </div>

                {{-- Step 4: Pelaksanaan & Laporan --}}
                <div class="row align-items-start">
                    <div class="col-md-2 text-center">
                        <div class="bg-white rounded-circle shadow-lg d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                            <span class="text-3xl font-weight-bold text-orange-600">4</span>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="bg-white p-4 rounded-lg shadow-sm border-left-4 border-orange-500">
                            <h5 class="font-weight-bold text-gray-800 mb-2">Pelaksanaan & Hasil</h5>
                            <p class="text-gray-600 mb-2">Siswa login dan mengerjakan. Setelah selesai, lihat hasilnya.</p>
                            
                            <button class="btn btn-link p-0 mb-3 text-orange-600 font-weight-bold" type="button" data-toggle="collapse" data-target="#detailStep4">
                                <i class="fas fa-info-circle mr-1"></i> Baca Detail Panduan
                            </button>

                            <div class="collapse mb-3" id="detailStep4">
                                <div class="card card-body bg-gray-50 border-0">
                                    <ul class="pl-3 text-sm text-gray-700">
                                        <li><strong>Kartu Peserta:</strong> Cetak dan bagikan username/password ke siswa.</li>
                                        <li><strong>Login Siswa:</strong> Siswa login, pilih ujian, dan masukkan token (jika ada).</li>
                                        <li><strong>Hasil:</strong> Nilai otomatis keluar untuk Pilihan Ganda. Essay perlu dikoreksi manual di menu <strong>Koreksi Ujian</strong>.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.student.cards') }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                    <i class="fas fa-id-card mr-1"></i> 1. Cetak Kartu Peserta
                                </a>
                                <a href="{{ route('admin.recap.exam_result') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                    <i class="fas fa-poll mr-1"></i> 2. Lihat Hasil Ujian
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
