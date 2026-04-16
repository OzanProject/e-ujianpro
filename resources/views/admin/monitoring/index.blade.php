@extends('layouts.admin.app')

@section('title', 'Daftar Monitoring Ujian')
@section('page_title', 'Daftar Monitoring Ujian')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-header bg-gradient-to-r from-blue-600 to-indigo-700 py-4 px-4 border-bottom-0 d-flex justify-content-between align-items-center text-white rounded-t-lg" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                <div>
                    <h5 class="font-weight-bold mb-1"><i class="fas fa-desktop mr-2 text-warning"></i> Monitoring Seluruh Ujian</h5>
                    <p class="text-xs mb-0 opacity-75">Pantau jalannya ujian dan kecurangan siswa secara real-time dari semua sesi.</p>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 px-4 py-3">Sesi Ujian</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 px-4 py-3">Mata Pelajaran</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 px-4 py-3">Waktu Pelaksanaan</th>
                                <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7 px-4 py-3">Status Sesi</th>
                                <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7 px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $session)
                                @php
                                    $now = now();
                                    $statusColor = 'secondary';
                                    $statusText = 'Belum Mulai';
                                    
                                    if ($now >= $session->start_time && $now <= $session->end_time) {
                                        $statusColor = 'success';
                                        $statusText = 'Sedang Berjalan';
                                    } elseif ($now > $session->end_time) {
                                        $statusColor = 'danger';
                                        $statusText = 'Selesai';
                                    }
                                @endphp
                                <tr class="border-bottom">
                                    <td class="px-4 py-3">
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-0 text-sm font-weight-bold text-dark">{{ $session->title }}</h6>
                                            <span class="text-xs text-muted mt-1 font-weight-bold">
                                                <i class="fas fa-key text-xs mr-1"></i> Token: {{ $session->token }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-blue-50 text-blue-600 rounded p-2 mr-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-book"></i>
                                            </div>
                                            <div>
                                                <span class="text-sm font-weight-bold d-block text-dark">{{ $session->subject->name }}</span>
                                                <span class="text-xs text-secondary">{{ $session->examPackage->title ?? 'Semua Paket' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="d-flex flex-column text-sm">
                                            <span class="text-dark font-weight-bold mb-1">
                                                {{ $session->start_time->format('d M Y') }}
                                            </span>
                                            <span class="text-muted text-xs">
                                                {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge badge-{{ $statusColor }} px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.7rem;">
                                            <i class="fas {{ $statusText == 'Sedang Berjalan' ? 'fa-pulse fa-spinner' : 'fa-circle' }} mr-1 text-xs"></i> {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('proctor.monitor.show', ['subdomain' => request()->route('subdomain') ?? ($globalInstitution->subdomain ?? 'default'), 'session' => $session->id]) }}" 
                                           class="btn btn-primary btn-sm px-4 rounded-pill font-weight-bold shadow-sm transition hover:scale-105">
                                            <i class="fas fa-desktop mr-2"></i> Monitor
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="bg-light rounded-circle p-4 mb-3">
                                                <i class="fas fa-calendar-times text-muted fa-3x"></i>
                                            </div>
                                            <h6 class="text-muted font-weight-bold">Belum ada Jadwal Ujian</h6>
                                            <p class="text-xs text-gray-400">Silakan buat jadwal ujian terlebih dahulu di menu Jadwal Ujian.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card-footer bg-white py-3 border-top-0 d-flex justify-content-end">
                {{ $sessions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
