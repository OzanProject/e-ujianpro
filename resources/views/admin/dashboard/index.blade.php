@extends('layouts.admin.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    @php
        /** @var \App\Models\User $user */
        $user = auth()->user();
        // Quota Logic
        $quotaPercent = 0;
        if(isset($maxStudents) && $maxStudents > 0) {
            $quotaPercent = ($pesertaCount / $maxStudents) * 100;
        }
    @endphp

    <!-- 1. Hero Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border-radius: 15px;">
                <div class="card-body p-4 text-white position-relative">
                    <div style="position: absolute; right: -20px; top: -20px; font-size: 150px; opacity: 0.1; transform: rotate(-15deg);">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <div class="row align-items-center position-relative z-1">
                        <div class="col-md-8">
                            <h2 class="font-weight-bold mb-1">Halo, {{ explode(' ', $user->name)[0] }}! 👋</h2>
                            <p class="mb-0 text-white-50">Selamat datang kembali di panel kontrol sekolah Anda.</p>
                        </div>
                        <div class="col-md-4 text-md-right mt-3 mt-md-0">
                            <div class="h1 font-weight-bold mb-0" id="clock">00:00</div>
                            <small class="text-white-50" id="date">Loading date...</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Premium Stats Cards -->
    <div class="row">
        <!-- Card 1: Siswa & Kuota -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 py-2" style="border-radius: 15px; border-left: 5px solid #4e73df;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Peserta</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pesertaCount }}</div>
                            <div class="mt-2">
                                @if(isset($maxStudents) && $maxStudents > 0)
                                    <div class="progress progress-sm mr-2 mb-1 bg-gray-200">
                                        <div class="progress-bar {{ $quotaPercent > 90 ? 'bg-danger' : 'bg-primary' }}" role="progressbar" style="width: {{ $quotaPercent }}%" aria-valuenow="{{ $quotaPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-muted">{{ $pesertaCount }} / {{ $maxStudents }} Kuota</small>
                                @else
                                    <span class="badge badge-success badge-pill"><i class="fas fa-check-circle mr-1"></i> Unlimited</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-primary text-white">
                                <i class="fas fa-user-graduate fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Guru -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 py-2" style="border-radius: 15px; border-left: 5px solid #1cc88a;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Guru</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $guruCount }}</div>
                            <small class="text-muted">Pengajar Aktif</small>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-success text-white">
                                <i class="fas fa-chalkboard-teacher fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Bank Soal -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 py-2" style="border-radius: 15px; border-left: 5px solid #36b9cc;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Bank Soal</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $paketSoalCount }}</div>
                            <small class="text-muted">Paket Ujian</small>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-info text-white">
                                <i class="fas fa-book-open fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Sesi Ujian -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 py-2" style="border-radius: 15px; border-left: 5px solid #f6c23e;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Sesi Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeExamSessionCount }}</div>
                            <small class="text-muted">Sedang Berlangsung</small>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-warning text-white">
                                <i class="fas fa-clock fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- 3. Main Chart & Quick Actions -->
        <div class="col-xl-8 col-lg-7">
            <!-- Chart -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                    <h6 class="m-0 font-weight-bold text-primary">Aktivitas Ujian (7 Hari Terakhir)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-lg-3 col-6 mb-3">
                    <a href="{{ route('admin.student.create') }}" class="btn btn-white shadow-sm w-100 h-100 py-4 d-flex flex-column align-items-center justify-content-center hover-card" style="border-radius: 15px;">
                        <div class="bg-primary-soft text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(78, 115, 223, 0.1);">
                            <i class="fas fa-user-plus fa-lg"></i>
                        </div>
                        <span class="font-weight-bold text-gray-800 small">Tambah Siswa</span>
                    </a>
                </div>
                <div class="col-lg-3 col-6 mb-3">
                    <a href="{{ route('admin.exam_session.create') }}" class="btn btn-white shadow-sm w-100 h-100 py-4 d-flex flex-column align-items-center justify-content-center hover-card" style="border-radius: 15px;">
                        <div class="bg-success-soft text-success rounded-circle mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(28, 200, 138, 0.1);">
                            <i class="fas fa-calendar-plus fa-lg"></i>
                        </div>
                        <span class="font-weight-bold text-gray-800 small">Buat Jadwal</span>
                    </a>
                </div>
                <div class="col-lg-3 col-6 mb-3">
                    <a href="{{ route('admin.report.desk_card.index') }}" class="btn btn-white shadow-sm w-100 h-100 py-4 d-flex flex-column align-items-center justify-content-center hover-card" style="border-radius: 15px;">
                        <div class="bg-info-soft text-info rounded-circle mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(54, 185, 204, 0.1);">
                            <i class="fas fa-id-card fa-lg"></i>
                        </div>
                        <span class="font-weight-bold text-gray-800 small">Cetak Kartu</span>
                    </a>
                </div>
                <div class="col-lg-3 col-6 mb-3">
                    <a href="{{ route('admin.recap.exam_result') }}" class="btn btn-white shadow-sm w-100 h-100 py-4 d-flex flex-column align-items-center justify-content-center hover-card" style="border-radius: 15px;">
                        <div class="bg-warning-soft text-warning rounded-circle mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(246, 194, 62, 0.1);">
                            <i class="fas fa-poll fa-lg"></i>
                        </div>
                        <span class="font-weight-bold text-gray-800 small">Lihat Hasil</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 4. Announcements & Info -->
        <div class="col-xl-4 col-lg-5">
            <!-- Announcements -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-header py-3 bg-white border-bottom-0" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                    <h6 class="m-0 font-weight-bold text-primary">Pengumuman Sistem</h6>
                </div>
                <div class="card-body">
                    @forelse($announcements as $announce)
                    <div class="mb-3 pb-3 border-bottom {{ $loop->last ? 'border-0 pb-0' : '' }}">
                        <div class="small text-muted mb-1">{{ $announce->created_at->format('d M Y') }}</div>
                        <h6 class="font-weight-bold mb-1">{{ $announce->title }}</h6>
                        <div class="small text-gray-600 mb-0">
                            {{ Str::limit(strip_tags($announce->content), 80) }}
                            <a href="#" class="text-primary font-weight-bold" data-toggle="modal" data-target="#announceModal{{ $announce->id }}">Baca</a>
                        </div>
                        
                        <!-- Modal -->
                        <div class="modal fade" id="announceModal{{ $announce->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header border-bottom-0">
                                        <h5 class="modal-title font-weight-bold text-primary">{{ $announce->title }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-dark">
                                        {!! $announce->content !!}
                                    </div>
                                    <div class="modal-footer border-top-0">
                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-bell-slash fa-3x text-gray-300 mb-3"></i>
                        <p class="text-gray-500 mb-0">Tidak ada pengumuman baru.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            
            <!-- System Info -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-body">
                    <h6 class="font-weight-bold text-gray-800 mb-3">Informasi Sistem</h6>
                    <ul class="list-unstyled mb-0 small text-gray-600">
                        <li class="mb-2 d-flex justify-content-between">
                            <span>Versi Aplikasi:</span>
                            <span class="font-weight-bold badge badge-light">v2.5.0 (Pro)</span>
                        </li>
                        <li class="mb-2 d-flex justify-content-between">
                            <span>Status Server:</span>
                            <span class="text-success font-weight-bold"><i class="fas fa-circle text-xs mr-1"></i> Online</span>
                        </li>
                        <li class="mb-2 d-flex justify-content-between">
                            <span>Waktu Server:</span>
                            <span class="font-weight-bold">{{ now()->timezone('Asia/Jakarta')->format('H:i') }} WIB</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    height: 3.5rem;
    width: 3.5rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.hover-card {
    transition: all 0.3s;
}
.hover-card:hover {
    transform: translateY(-5px);
    background-color: #f8f9fc !important;
}
</style>
@endsection

@push('scripts')
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
<script>
    // Realtime Clock
    function updateClock() {
        const now = new Date();
        const timeOptions = { hour: '2-digit', minute: '2-digit' };
        const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        
        document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID', timeOptions).replace('.', ':');
        document.getElementById('date').textContent = now.toLocaleDateString('id-ID', dateOptions);
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Chart.js Configuration
    Chart.defaults.global.defaultFontFamily = 'Inter', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    var ctx = document.getElementById("myAreaChart");
    var myLineChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
          label: "Siswa Mengerjakan",
          lineTension: 0.4,
          backgroundColor: "rgba(78, 115, 223, 0.05)",
          borderColor: "rgba(78, 115, 223, 1)",
          pointRadius: 3,
          pointBackgroundColor: "rgba(78, 115, 223, 1)",
          pointBorderColor: "rgba(78, 115, 223, 1)",
          pointHoverRadius: 3,
          pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
          pointHoverBorderColor: "rgba(78, 115, 223, 1)",
          pointHitRadius: 10,
          pointBorderWidth: 2,
          data: {!! json_encode($chartData) !!},
        }],
      },
      options: {
        maintainAspectRatio: false,
        layout: {
          padding: { left: 10, right: 25, top: 25, bottom: 0 }
        },
        scales: {
          xAxes: [{
            time: { unit: 'date' },
            gridLines: { display: false, drawBorder: false },
            ticks: { maxTicksLimit: 7 }
          }],
          yAxes: [{
            ticks: {
              maxTicksLimit: 5, padding: 10,
              callback: function(value, index, values) { return value; },
              beginAtZero: true
            },
            gridLines: {
              color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)",
              drawBorder: false, borderDash: [2], zeroLineBorderDash: [2]
            }
          }],
        },
        legend: { display: false },
        tooltips: {
          backgroundColor: "rgb(255,255,255)",
          bodyFontColor: "#858796",
          titleMarginBottom: 10,
          titleFontColor: '#6e707e',
          titleFontSize: 14,
          borderColor: '#dddfeb',
          borderWidth: 1,
          xPadding: 15, yPadding: 15,
          displayColors: false, intersect: false, mode: 'index', caretPadding: 10,
          callbacks: {
            label: function(tooltipItem, chart) {
              var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
              return datasetLabel + ': ' + tooltipItem.yLabel + ' Siswa';
            }
          }
        }
      }
    });
</script>
@endpush