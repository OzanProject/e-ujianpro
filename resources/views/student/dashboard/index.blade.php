@extends('layouts.student.app')

@section('page_title', 'Dashboard Peserta')

@section('content')
    <div class="row">
        <!-- Hero / Profile Section -->
        <div class="col-lg-12 mb-4">
            <div class="card border-0 shadow position-relative overflow-hidden" style="background: linear-gradient(135deg, #3b82f6 0%, #4f46e5 100%); border-radius: 1rem;">
                 <!-- Decoration Circles -->
                <div style="position: absolute; top: -20px; right: -20px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -40px; left: -20px; width: 100px; height: 100px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>

                <div class="card-body p-4 p-lg-5 d-flex align-items-center flex-column flex-md-row text-white">
                    <div class="mr-md-4 mb-3 mb-md-0 text-center">
                        <div class="bg-white p-1 rounded-circle shadow-sm" style="display: inline-block;">
                            <img src="{{ Auth::guard('student')->user()->photo ? asset('storage/' . Auth::guard('student')->user()->photo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::guard('student')->user()->name).'&background=random&size=128' }}" 
                                 alt="User" class="img-circle" style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                    </div>
                    <div class="text-center text-md-left">
                        <h2 class="font-weight-bold mb-1" style="font-size: 1.75rem;">Halo, {{ Auth::guard('student')->user()->name }}! 👋</h2>
                        <p class="mb-0 opacity-8" style="font-size: 1rem;">
                             {{ Auth::guard('student')->user()->nis }} | Kelas: {{ Auth::guard('student')->user()->kelas ?? '-' }} | Jurusan: {{ Auth::guard('student')->user()->jurusan ?? '-' }}
                        </p>
                        <div class="mt-2">
                             @if(Auth::guard('student')->user()->examRoom)
                                <span class="badge badge-light px-3 py-1 rounded-pill text-primary font-weight-bold shadow-sm" style="font-size: 0.9rem;">
                                    <i class="fas fa-door-open mr-1"></i> Ruangan: {{ Auth::guard('student')->user()->examRoom->name }}
                                </span>
                            @else
                                <span class="badge badge-warning px-3 py-1 rounded-pill text-white font-weight-bold shadow-sm" style="font-size: 0.9rem; background-color: rgba(255,255,255,0.2);">
                                     <i class="fas fa-exclamation-circle mr-1"></i> Belum ada ruangan
                                </span>
                            @endif
                        </div>
                         <div class="mt-3">
                            <span class="badge badge-light px-3 py-2 rounded-pill font-weight-normal text-primary">
                                <i class="fas fa-school mr-1"></i> {{ $globalInstitution->name ?? 'E-Ujian' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Exams Section -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold text-gray-800"><i class="far fa-calendar-alt text-primary mr-2"></i>Jadwal Ujian Akan Datang</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    @if(isset($upcomingSessions) && $upcomingSessions->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless text-gray-700 mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="rounded-left py-3">Hari & Tanggal</th>
                                        <th class="py-3">Jam</th>
                                        <th class="py-3">Mata Pelajaran</th>
                                        <th class="rounded-right py-3">Durasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        \Carbon\Carbon::setLocale('id'); // Ensure Indonesian Locale
                                    @endphp
                                    @foreach($upcomingSessions as $session)
                                    <tr class="border-bottom">
                                        <td class="font-weight-bold">
                                            <div class="d-flex flex-column">
                                                <span class="text-primary text-lg">{{ $session->start_time->translatedFormat('l') }}</span>
                                                <span class="text-xs text-muted">{{ $session->start_time->translatedFormat('d F Y') }}</span>
                                            </div>
                                        </td>
                                        <td class="align-middle font-weight-bold">
                                            {{ $session->start_time->format('H:i') }}
                                        </td>
                                        <td class="align-middle">
                                            <span class="font-weight-bold text-dark">{{ $session->subject->name ?? '-' }}</span>
                                            <div class="text-xs text-muted">{{ $session->examPackage->title ?? '' }}</div>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge badge-light border">{{ $session->duration }} Menit</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 bg-light rounded">
                            <i class="far fa-calendar-check fa-3x mb-3 text-gray-300"></i>
                            <h6 class="text-muted font-weight-bold">Tidak ada jadwal ujian mendatang.</h6>
                            <p class="text-muted text-sm mb-0">Anda bisa bersantai sejenak! ☕</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-0 d-flex justify-content-between align-items-center bg-white pt-4 pb-0 px-4">
                    <h5 class="card-title mb-0" style="font-size: 1.25rem;">📝 Daftar Ujian Aktif</h5>
                    <!-- Optional Filter/Search could go here -->
                </div>
                <div class="card-body px-4 py-4">
                    @if($examSessions->isEmpty())
                        <div class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486831.png" alt="No Data" style="width: 120px; opacity: 0.6;" class="mb-3">
                            <h6 class="text-muted">Tidak ada jadwal ujian yang aktif saat ini.</h6>
                            <p class="text-small text-gray-400">Silakan cek kembali nanti atau hubungi pengajar Anda.</p>
                        </div>
                    @else
                        <div class="row">
                            @foreach($examSessions as $session)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border shadow-none" style="background: #ffffff; border: 1px solid #e2e8f0 !important; transition: all 0.3s ease;">
                                        <div class="card-body p-4 d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div class="bg-blue-50 text-blue-600 rounded p-2">
                                                    <i class="fas fa-book-open fa-lg" style="color: #3b82f6;"></i>
                                                </div>
                                                @if($session->attempt_status == 'completed')
                                                    <span class="badge badge-success px-3 py-2 rounded-pill">Selesai</span>
                                                @elseif($session->attempt_status == 'in_progress')
                                                    <span class="badge badge-warning px-3 py-2 rounded-pill text-white">Sedang Dikerjakan</span>
                                                @else
                                                    <span class="badge badge-secondary px-3 py-2 rounded-pill bg-gray-200 text-gray-600">Belum Mulai</span>
                                                @endif
                                            </div>
                                            
                                            <h5 class="font-weight-bold mb-1" style="color: #1e293b;">{{ $session->subject->name ?? 'Mata Pelajaran' }}</h5>
                                            <p class="text-muted text-sm mb-3">{{ $session->examPackage->title ?? 'Paket Soal' }}</p>
                                            
                                            <div class="mt-auto">
                                                <div class="d-flex align-items-center text-sm text-gray-600 mb-2">
                                                    <i class="far fa-calendar-alt mr-2 text-gray-400" style="width:20px"></i>
                                                    {{ $session->start_time->format('d M, H:i') }} - {{ $session->end_time->format('H:i') }}
                                                </div>
                                                <div class="d-flex align-items-center text-sm text-gray-600 mb-4">
                                                    <i class="far fa-clock mr-2 text-gray-400" style="width:20px"></i>
                                                    {{ $session->duration }} Menit
                                                </div>
                                                
                                                @if($session->attempt_status == 'completed')
                                                    <button class="btn btn-light btn-block text-muted" disabled>
                                                        <i class="fas fa-check-circle mr-1"></i> Sudah Selesai
                                                    </button>
                                                @elseif($session->attempt_status == 'in_progress')
                                                    <a href="{{ request()->route('subdomain') ? route('institution.student.exam.confirmation', ['subdomain' => request()->route('subdomain'), 'id' => $session->id]) : route('student.exam.confirmation', $session->id) }}" 
                                                       class="btn btn-warning btn-block text-white shadow-sm font-weight-bold">
                                                        <i class="fas fa-play mr-1"></i> Lanjutkan
                                                    </a>
                                                @else
                                                     <a href="{{ request()->route('subdomain') ? route('institution.student.exam.confirmation', ['subdomain' => request()->route('subdomain'), 'id' => $session->id]) : route('student.exam.confirmation', $session->id) }}" 
                                                        class="btn btn-primary btn-block shadow-sm font-weight-bold" style="background: #3b82f6; border: none;">
                                                        <i class="fas fa-pencil-alt mr-1"></i> Mulai Ujian
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
