@extends('layouts.admin.app')

@section('title', 'Super Admin Dashboard')

@section('content')
{{-- Hero Section --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #198754 0%, #20c997 100%); border-radius: 1rem;">
            <div class="card-body p-4 p-md-5 position-relative">
                <div class="row align-items-center">
                    <div class="col-md-8 text-white z-index-1">
                        <h2 class="font-weight-bold mb-2">Selamat Datang, Super Administrator! 👋</h2>
                        <p class="mb-0 opacity-8" style="font-size: 1.1rem; line-height: 1.6;">
                            Pantau performa seluruh sekolah dan aktivitas platform dalam satu tampilan terpadu.
                        </p>
                    </div>
                    <div class="col-md-4 d-none d-md-block text-right z-index-1">
                         <i class="fas fa-chart-line fa-8x text-white" style="opacity: 0.2; transform: rotate(-10deg);"></i>
                    </div>
                </div>
                {{-- Decorative shapes --}}
                <div class="position-absolute" style="top: -20px; right: 10%; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div class="position-absolute" style="bottom: -50px; left: 5%; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Metric Cards --}}
<div class="row mb-4">
    {{-- Total Sekolah --}}
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 py-2" style="border-radius: 1rem;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Sekolah</div>
                        <div class="h1 mb-0 font-weight-bold text-gray-800">{{ $totalInstitutions }}</div>
                        <p class="text-muted text-xs mb-0 mt-2">Terdaftar di platform</p>
                    </div>
                    <div class="col-auto">
                        <div class="icon-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-university fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Guru --}}
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 py-2" style="border-radius: 1rem;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Guru</div>
                        <div class="h1 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div> {{-- As per screenshot likely count of users --}}
                        <p class="text-muted text-xs mb-0 mt-2">Aktif mengajar</p>
                    </div>
                    <div class="col-auto">
                         <div class="icon-circle bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-chalkboard-teacher fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Siswa --}}
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 py-2" style="border-radius: 1rem;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Siswa</div>
                        <div class="h1 mb-0 font-weight-bold text-gray-800">{{ $totalStudents }}</div>
                        <p class="text-muted text-xs mb-0 mt-2">Data peserta ujian</p>
                    </div>
                    <div class="col-auto">
                        <div class="icon-circle bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-user-graduate fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    {{-- Registration Chart --}}
    <div class="col-xl-8 col-lg-7">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
            <div class="card-header bg-white py-3 border-bottom-0 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-dark">Grafik Registrasi Sekolah (6 Bulan Terakhir)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area" style="height: 300px;">
                    <canvas id="registrationChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Active Exams --}}
    <div class="col-xl-4 col-lg-5">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h6 class="m-0 font-weight-bold text-dark">Ujian Sedang Berlangsung <span class="badge badge-success badge-pill">{{ $activeExamsCount }}</span></h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($activeExamSessions as $exam)
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-4 py-3">
                            <div>
                                <h6 class="mb-0 font-weight-bold">{{ $exam->title ?? 'Sesi Ujian' }}</h6>
                                <small class="text-muted">{{ $exam->subject->name ?? '-' }} ({{ $exam->examType->name ?? 'Ujian' }})</small>
                            </div>
                            <span class="badge badge-success badge-pill"><i class="fas fa-circle text-xs fa-fade"></i> Aktif</span>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-4 border-0">
                            Tidak ada ujian yang sedang berlangsung saat ini.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Recent Institutions Table (Preserved but styled simpler) --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-dark">Sekolah Terbaru Mendaftar</h6>
                <a href="{{ route('admin.super.institutions.index') }}" class="btn btn-sm btn-light text-primary font-weight-bold">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>Sekolah</th>
                            <th>Email Admin</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentInstitutions as $institution)
                        <tr>
                            <td>
                                <span class="font-weight-bold text-dark">{{ $institution->name }}</span>
                                <br>
                                <small class="text-muted">{{ $institution->city ?? 'Kota -' }}</small>
                            </td>
                            <td>{{ $institution->user->email ?? '-' }}</td>
                            <td>{{ $institution->created_at->format('d M Y') }}</td>
                            <td>
                                <span class="badge badge-success badge-pill">Aktif</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada sekolah yang mendaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById("registrationChart");
        if (ctx) {
            var myLineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: "Pendaftaran Baru",
                        lineTension: 0.3,
                        backgroundColor: "rgba(32, 201, 151, 0.05)",
                        borderColor: "rgba(32, 201, 151, 1)",
                        pointRadius: 4,
                        pointBackgroundColor: "rgba(32, 201, 151, 1)",
                        pointBorderColor: "rgba(255,255,255,1)",
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: "rgba(32, 201, 151, 1)",
                        pointHoverBorderColor: "rgba(255,255,255,1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: {!! json_encode($chartData) !!},
                        fill: true,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                        },
                        y: {
                            ticks: {
                                precision: 0,
                                beginAtZero: true
                            },
                            grid: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
