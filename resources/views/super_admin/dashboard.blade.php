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
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 py-2" style="border-radius: 1rem;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Sekolah</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $totalInstitutions }}</div>
                    </div>
                    <div class="col-auto">
                        <div class="icon-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-university fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Guru --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 py-2" style="border-radius: 1rem;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Guru</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $totalTeachers }}</div>
                    </div>
                    <div class="col-auto">
                         <div class="icon-circle bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-chalkboard-teacher fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Siswa --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 py-2" style="border-radius: 1rem;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Siswa</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $totalStudents }}</div>
                    </div>
                    <div class="col-auto">
                        <div class="icon-circle bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-user-graduate fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ujian Aktif --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 py-2" style="border-radius: 1rem;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Ujian Aktif Global</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $activeExamsCount }}</div>
                    </div>
                    <div class="col-auto">
                        <div class="icon-circle bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-laptop-code fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Chart Section --}}
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm rounded-lg h-100">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h6 class="font-weight-bold text-dark mb-0">Statistik Pendaftaran Sekolah (6 Bulan Terakhir)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area" style="height: 320px;">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm rounded-lg h-100">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h6 class="font-weight-bold text-dark mb-0">Menu Cepat (Quick Actions)</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-3">
                         <a href="{{ route('admin.super.institutions.create') }}" class="btn btn-light w-100 h-100 py-3 shadow-none border d-flex flex-column align-items-center justify-content-center hover-card">
                            <i class="fas fa-plus-circle fa-2x text-primary mb-2"></i>
                            <span class="small font-weight-bold">Tambah Sekolah</span>
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('admin.super.points.index') }}" class="btn btn-light w-100 h-100 py-3 shadow-none border d-flex flex-column align-items-center justify-content-center hover-card">
                            <i class="fas fa-wallet fa-2x text-success mb-2"></i>
                            <span class="small font-weight-bold">Verifikasi Poin</span>
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                         <a href="{{ route('admin.super.institutions.index') }}" class="btn btn-light w-100 h-100 py-3 shadow-none border d-flex flex-column align-items-center justify-content-center hover-card">
                            <i class="fas fa-school fa-2x text-info mb-2"></i>
                            <span class="small font-weight-bold">Kelola Sekolah</span>
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                         <a href="{{ route('admin.super.settings.index') }}" class="btn btn-light w-100 h-100 py-3 shadow-none border d-flex flex-column align-items-center justify-content-center hover-card">
                            <i class="fas fa-cog fa-2x text-secondary mb-2"></i>
                            <span class="small font-weight-bold">Pengaturan</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Recent Institutions Table --}}
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm rounded-lg h-100">
            <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-dark">Sekolah Baru Mendaftar</h6>
                <a href="{{ route('admin.super.institutions.index') }}" class="btn btn-sm btn-light text-primary font-weight-bold">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>Sekolah</th>
                            <th>Kota/Kab</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentInstitutions as $institution)
                        <tr>
                            <td>
                                <span class="font-weight-bold text-dark">{{ $institution->name }}</span>
                                <div class="small text-muted">{{ $institution->user->email ?? '-' }}</div>
                            </td>
                            <td>{{ $institution->city ?? '-' }}</td>
                            <td>{{ $institution->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Belum ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Active Exams Global --}}
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm rounded-lg h-100">
            <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-dark">Ujian Aktif (Global)</h6>
                 <span class="badge badge-warning">{{ $activeExamsCount }} Aktif</span>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>Jenis Ujian</th>
                            <th>Mapel</th>
                            <th>Sekolah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeExamSessions as $session)
                        <tr>
                            <td>
                                <span class="badge badge-primary">{{ $session->examType->name ?? 'Ujian' }}</span>
                            </td>
                            <td>
                                <span class="font-weight-bold text-dark">{{ $session->subject->name ?? '-' }}</span>
                            </td>
                            <td>
                                <div class="small">{{ $session->subject->createdBy->institution->name ?? 'Admin User' }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Tidak ada ujian aktif saat ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-card:hover {
        background-color: #f8f9fc !important;
        transform: translateY(-2px);
        transition: all 0.2s ease;
    }
</style>
@endsection

@push('scripts')
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
<script>
    // Set new default font family and font color to mimic Bootstrap's default styling
    Chart.defaults.global.defaultFontFamily = 'Inter', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    function number_format(number, decimals, dec_point, thousands_sep) {
      // *     example: number_format(1234.56, 2, ',', ' ');
      // *     return: '1 234,56'
      number = (number + '').replace(',', '').replace(' ', '');
      var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
          var k = Math.pow(10, prec);
          return '' + Math.round(n * k) / k;
        };
      s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }
      if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
      }
      return s.join(dec);
    }

    // Area Chart Example
    var ctx = document.getElementById("myAreaChart");
    var myLineChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
          label: "Sekolah Baru",
          lineTension: 0.3,
          backgroundColor: "rgba(25, 135, 84, 0.05)",
          borderColor: "rgba(25, 135, 84, 1)", // Bootstrap Success Green #198754
          pointRadius: 3,
          pointBackgroundColor: "rgba(25, 135, 84, 1)",
          pointBorderColor: "rgba(25, 135, 84, 1)",
          pointHoverRadius: 3,
          pointHoverBackgroundColor: "rgba(25, 135, 84, 1)",
          pointHoverBorderColor: "rgba(25, 135, 84, 1)",
          pointHitRadius: 10,
          pointBorderWidth: 2,
          data: {!! json_encode($chartData) !!},
        }],
      },
      options: {
        maintainAspectRatio: false,
        layout: {
          padding: {
            left: 10,
            right: 25,
            top: 25,
            bottom: 0
          }
        },
        scales: {
          xAxes: [{
            time: {
              unit: 'date'
            },
            gridLines: {
              display: false,
              drawBorder: false
            },
            ticks: {
              maxTicksLimit: 7
            }
          }],
          yAxes: [{
            ticks: {
              maxTicksLimit: 5,
              padding: 10,
              // Include a dollar sign in the ticks
              callback: function(value, index, values) {
                return number_format(value);
              },
              beginAtZero: true
            },
            gridLines: {
              color: "rgb(234, 236, 244)",
              zeroLineColor: "rgb(234, 236, 244)",
              drawBorder: false,
              borderDash: [2],
              zeroLineBorderDash: [2]
            }
          }],
        },
        legend: {
          display: false
        },
        tooltips: {
          backgroundColor: "rgb(255,255,255)",
          bodyFontColor: "#858796",
          titleMarginBottom: 10,
          titleFontColor: '#6e707e',
          titleFontSize: 14,
          borderColor: '#dddfeb',
          borderWidth: 1,
          xPadding: 15,
          yPadding: 15,
          displayColors: false,
          intersect: false,
          mode: 'index',
          caretPadding: 10,
          callbacks: {
            label: function(tooltipItem, chart) {
              var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
              return datasetLabel + ': ' + number_format(tooltipItem.yLabel);
            }
          }
        }
      }
    });
</script>
@endpush
