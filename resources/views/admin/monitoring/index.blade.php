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
                <!-- Filter Section -->
                <div class="bg-light p-4 border-bottom">
                    <form action="{{ route($baseRoute . '.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3 mb-3 mb-md-0">
                                <div class="input-group shadow-sm rounded">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                                    </div>
                                    <input type="text" name="search" class="form-control border-0" placeholder="Cari nama, token, kelas..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2 mb-3 mb-md-0">
                                <select name="subject_id" class="form-control shadow-sm border-0 font-weight-bold text-dark" onchange="this.form.submit()">
                                    <option value="">Semua Mapel</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3 mb-md-0">
                                <select name="status" class="form-control shadow-sm border-0 font-weight-bold text-dark" onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>🟢 Sedang Berjalan</option>
                                    <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>⏳ Belum Mulai</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>🔴 Selesai</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3 mb-md-0">
                                <select name="sort" class="form-control shadow-sm border-0 font-weight-bold text-dark" onchange="this.form.submit()">
                                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Terbaru Dibuat</option>
                                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Terlama Dibuat</option>
                                    <option value="start_desc" {{ request('sort') === 'start_desc' ? 'selected' : '' }}>Waktu Pelaksanaan (Baru ke Lama)</option>
                                    <option value="start_asc" {{ request('sort') === 'start_asc' ? 'selected' : '' }}>Waktu Pelaksanaan (Lama ke Baru)</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-dark btn-block shadow-sm font-weight-bold rounded">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
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
                                            <div class="d-flex align-items-center">
                                                <h6 class="mb-0 text-sm font-weight-bold text-dark">{{ $session->title }}</h6>
                                                @if($session->target_kelas)
                                                    <span class="badge badge-info bg-blue-100 text-blue-800 border border-blue-200 px-2 py-1 ml-2 text-xs rounded" style="font-size: 0.65rem;">
                                                        Kelas {{ $session->target_kelas }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary bg-gray-100 text-gray-700 border border-gray-200 px-2 py-1 ml-2 text-xs rounded" style="font-size: 0.65rem;">
                                                        Semua Kelas
                                                    </span>
                                                @endif
                                            </div>
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
