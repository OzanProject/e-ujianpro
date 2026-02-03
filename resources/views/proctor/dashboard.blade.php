@extends('layouts.admin.app')

@section('title', 'Dashboard Pengawas')
@section('page_title', 'Dashboard Pengawas')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="alert alert-info border-0 shadow-sm rounded-lg mb-4">
            <h5 class="alert-heading font-weight-bold"><i class="icon fas fa-info-circle"></i> Selamat Datang, {{ Auth::user()->name }}!</h5>
             Anda login sebagai <b>Pengawas Ujian</b>. Di bawah ini adalah daftar ujian yang aktif hari ini. Silakan pilih sesi untuk melakukan monitoring.
        </div>
    </div>
</div>

<div class="row">
    @forelse($activeSessions as $session)
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-lg border-0 rounded-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="card-header bg-gradient-to-r from-blue-600 to-indigo-600 p-4 border-0">
                <h5 class="card-title text-white font-weight-bold mb-0">
                    <i class="fas fa-file-alt mr-2"></i> {{ $session->title }}
                </h5>
            </div>
            <div class="card-body bg-light">
                <div class="mb-3">
                    <span class="d-block text-gray-600 font-weight-bold text-sm text-uppercase tracking-wide mb-1">Mata Pelajaran</span>
                    <span class="h6 font-weight-bold text-dark">{{ $session->subject->name }}</span>
                </div>
                
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <span class="d-block text-gray-600 font-weight-bold text-xs text-uppercase tracking-wide mb-1">Mulai</span>
                        <span class="text-dark font-weight-bold">{{ $session->start_time->format('H:i') }}</span>
                    </div>
                    <div class="text-right">
                        <span class="d-block text-gray-600 font-weight-bold text-xs text-uppercase tracking-wide mb-1">Selesai</span>
                        <span class="text-dark font-weight-bold">{{ $session->end_time->format('H:i') }}</span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <span class="badge badge-success bg-green-100 text-green-800 px-3 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-clock mr-1"></i> {{ $session->duration }} Menit
                    </span>
                    <span class="badge badge-info bg-blue-100 text-blue-800 px-3 py-2 rounded-pill shadow-sm">
                         Token: {{ $session->token }}
                    </span>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0 p-3">
                <a href="{{ route('proctor.monitor.show', ['subdomain' => request()->route('subdomain'), 'session' => $session->id]) }}" class="btn btn-primary btn-block rounded-pill font-weight-bold shadow-md bg-gradient-to-r from-blue-500 to-indigo-500 border-0 hover:from-blue-600 hover:to-indigo-600 text-white py-2">
                    <i class="fas fa-desktop mr-2"></i> Masuk Monitoring
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card bg-white shadow-sm rounded-lg text-center p-5 border-0">
            <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" alt="Empty" width="200" class="mx-auto mb-4 opacity-75">
            <h4 class="font-weight-bold text-gray-700">Tidak ada jadwal ujian aktif saat ini.</h4>
            <p class="text-muted">Jadwal ujian akan muncul di sini jika ada sesi yang berlangsung hari ini.</p>
        </div>
    </div>
    @endforelse
</div>
@endsection
