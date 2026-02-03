@extends('layouts.student.app')

@section('page_title', 'Konfirmasi Ujian')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-lg border-0 overflow-hidden">
            <div class="card-header bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 border-0">
                <h3 class="card-title font-weight-bold text-lg mb-0">
                    <i class="fas fa-file-alt mr-2"></i> Konfirmasi Ujian
                </h3>
            </div>
            <div class="card-body p-5">
                <div class="row">
                    <div class="col-md-5 text-center mb-4 mb-md-0">
                         <div class="bg-blue-50 rounded-full p-5 d-inline-block mb-3">
                             <img src="https://cdn-icons-png.flaticon.com/512/2995/2995620.png" alt="Exam" width="120">
                         </div>
                         <h4 class="font-weight-bold text-gray-800">{{ $session->subject->name }}</h4>
                         <p class="text-gray-500">{{ $session->examPackage->title ?? 'Paket Soal Acak / Semua Soal' }}</p>
                    </div>
                    <div class="col-md-7">
                        <table class="table table-borderless">
                            <tr>
                                <th class="text-gray-500 w-40 pl-0">Waktu</th>
                                <td class="font-weight-bold text-gray-800">{{ $session->start_time->format('d M Y, H:i') }}</td>
                            </tr>
                            <tr>
                                <th class="text-gray-500 pl-0">Durasi</th>
                                <td class="font-weight-bold text-gray-800">{{ $session->duration }} Menit</td>
                            </tr>
                            <tr>
                                <th class="text-gray-500 pl-0">Status</th>
                                <td>
                                    <span class="badge badge-success px-3 py-1 rounded-pill">Aktif</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-gray-500 pl-0">Ruangan</th>
                                <td class="font-weight-bold text-gray-800">
                                    @if(Auth::guard('student')->user()->examRoom)
                                        {{ Auth::guard('student')->user()->examRoom->name }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @if($session->description)
                            <tr>
                                <th class="text-gray-500 pl-0">Info</th>
                                <td class="text-gray-700">{{ $session->description }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <div class="alert bg-yellow-50 border-yellow-200 text-yellow-800 rounded-lg p-4 mt-4">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-yellow-600 mt-1"></i>
                        </div>
                        <div class="ml-3">
                            <h5 class="font-weight-bold text-sm mb-2">Perhatian Sebelum Memulai!</h5>
                            <ul class="text-sm list-disc pl-4 space-y-1">
                                <li>Pastikan koneksi internet Anda stabil.</li>
                                <li>Waktu akan berjalan mundur otomatis setelah Anda klik tombol mulai.</li>
                                <li>Jangan menutup browser selama ujian berlangsung.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                @if(!($attempt && $attempt->status == 'in_progress'))
                <div class="mt-4">
                    <div class="form-group text-center">
                         <label for="token" class="font-weight-bold text-gray-700">Masukkan Token Ujian</label>
                         <input type="text" id="token" name="token" form="start-form" class="form-control text-center text-uppercase font-weight-bold mx-auto border-2 border-primary" style="max-width: 200px; font-size: 1.5rem; letter-spacing: 5px;" placeholder="_____" maxlength="5" required autocomplete="off">
                         <small class="text-muted d-block mt-2">Minta token kepada pengawas ujian.</small>
                    </div>
                </div>
                @endif

                <div class="mt-5 text-center">
                    <form id="start-form" action="{{ request()->route('subdomain') ? route('institution.student.exam.start', ['subdomain' => request()->route('subdomain'), 'id' => $session->id]) : route('student.exam.start', $session->id) }}" method="POST">
                        @csrf
                        <a href="{{ request()->route('subdomain') ? route('institution.student.dashboard', request()->route('subdomain')) : route('student.dashboard') }}" 
                           class="btn btn-default mr-3 py-3 px-6 rounded-xl font-weight-bold text-gray-600 hover:bg-gray-100 transition">
                            Batal
                        </a>
                        
                        @if($attempt && $attempt->status == 'in_progress')
                            <button type="submit" class="btn btn-warning py-3 px-8 rounded-xl font-weight-bold text-white shadow-lg transform hover:scale-105 transition">
                                <i class="fas fa-play mr-2"></i> Lanjutkan Ujian
                            </button>
                        @else
                            <button type="submit" class="btn btn-primary py-3 px-8 rounded-xl font-weight-bold shadow-lg transform hover:scale-105 transition" style="background: linear-gradient(to right, #2563eb, #4f46e5); border: none;">
                                <i class="fas fa-rocket mr-2"></i> Mulai Kerjakan
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
