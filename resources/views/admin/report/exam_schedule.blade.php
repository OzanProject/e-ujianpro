@extends('layouts.admin.app')

@section('title', 'Rekap Jadwal Ujian')
@section('page_title', 'Rekap Jadwal Ujian')

@section('content')
<div class="row">
    <!-- Filter Section -->
    <div class="col-md-12 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4">
                <form action="{{ route('admin.report.exam_schedule') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="font-weight-bold text-muted mb-2">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate ?? date('Y-m-d') }}" required style="height: 48px; border-radius: 10px;">
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="font-weight-bold text-muted mb-2">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate ?? date('Y-m-d') }}" required style="height: 48px; border-radius: 10px;">
                        </div>
                        <div class="col-md-4">
                             <button type="submit" class="btn btn-primary btn-block" style="height: 48px; border-radius: 10px;">
                                <i class="fas fa-search mr-2"></i> Tampilkan Jadwal
                             </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Summary Widgets -->
    @if(isset($stats))
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="font-weight-bold m-0 text-dark">Ringkasan Rentang Waktu Ini</h4>
            @if($sessions->count() > 0)
            <a href="{{ route('admin.report.print_exam_schedule', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="btn btn-outline-success">
                <i class="fas fa-print mr-2"></i> Cetak Jadwal
            </a>
            @endif
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: white; border-left: 5px solid #6366f1;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape bg-indigo-soft text-indigo rounded-circle p-3 mr-3" style="background: #e0e7ff; color: #4338ca;">
                                <i class="fas fa-calendar-check fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Total Jadwal</h6>
                                <h2 class="font-weight-bold mb-0 text-dark">{{ $stats['total'] }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                 <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: white; border-left: 5px solid #10b981;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape bg-green-soft text-green rounded-circle p-3 mr-3" style="background: #d1fae5; color: #047857;">
                                <i class="fas fa-play-circle fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Aktif Sekarang</h6>
                                <h2 class="font-weight-bold mb-0 text-dark">{{ $stats['active'] }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: white; border-left: 5px solid #f59e0b;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape bg-yellow-soft text-yellow rounded-circle p-3 mr-3" style="background: #fef3c7; color: #b45309;">
                                <i class="fas fa-clock fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Akan Datang</h6>
                                <h2 class="font-weight-bold mb-0 text-dark">{{ $stats['upcoming'] }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                 <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: white; border-left: 5px solid #64748b;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                             <div class="icon-shape bg-gray-soft text-gray rounded-circle p-3 mr-3" style="background: #f1f5f9; color: #475569;">
                                <i class="fas fa-check-double fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Selesai</h6>
                                <h2 class="font-weight-bold mb-0 text-dark">{{ $stats['finished'] }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    @if(isset($sessions))
    <div class="col-12">
        <div class="card border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="font-weight-bold m-0 text-dark">Daftar Jadwal</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="scheduleTable" class="table table-hover table-striped mb-0">
                        <thead class="bg-light text-muted uppercase" style="font-size: 0.85rem; letter-spacing: 0.05em;">
                            <tr>
                                <th class="px-4 py-3 border-0" style="width: 5%;">No</th>
                                <th class="px-4 py-3 border-0">Mata Pelajaran</th>
                                <th class="px-4 py-3 border-0">Paket Soal</th>
                                <th class="px-4 py-3 border-0">Waktu Pelaksanaan</th>
                                <th class="px-4 py-3 border-0 text-center">Durasi</th>
                                <th class="px-4 py-3 border-0 text-center">Token</th>
                                <th class="px-4 py-3 border-0 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $session)
                                <tr>
                                    <td class="px-4 py-3 align-middle font-weight-bold text-muted">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 align-middle font-weight-bold text-dark">
                                        {{ $session->subject->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 align-middle">
                                        {{ $session->examPackage->title ?? 'Semua Soal' }}
                                    </td>
                                    <td class="px-4 py-3 align-middle">
                                        <div class="d-flex flex-column">
                                            <span class="text-success font-weight-bold"><i class="fas fa-play mr-1"></i> {{ \Carbon\Carbon::parse($session->start_time)->format('d M Y H:i') }}</span>
                                            <span class="text-danger small mt-1"><i class="fas fa-stop mr-1"></i> {{ \Carbon\Carbon::parse($session->end_time)->format('d M Y H:i') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-middle text-center">
                                        <span class="badge badge-light border px-3 py-2">{{ $session->duration }} Menit</span>
                                    </td>
                                    <td class="px-4 py-3 align-middle text-center">
                                        <span class="badge badge-warning text-dark px-3 py-2 font-weight-bold" style="letter-spacing: 1px;">{{ $session->token }}</span>
                                    </td>
                                    <td class="px-4 py-3 align-middle text-center">
                                        @php
                                            $now = now();
                                            $status = '';
                                            $badgeClass = '';
                                            if ($now < $session->start_time) {
                                                $status = 'Upcoming';
                                                $badgeClass = 'badge-soft-warning text-warning bg-yellow-soft';
                                            } elseif ($now > $session->end_time) {
                                                $status = 'Selesai';
                                                $badgeClass = 'badge-soft-secondary text-secondary bg-gray-soft';
                                            } else {
                                                $status = 'Berjalan';
                                                $badgeClass = 'badge-soft-success text-success bg-green-soft';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill">{{ $status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">Tidak ada jadwal ujian pada rentang tanggal ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        @if(isset($sessions) && $sessions->count() > 0)
        $('#scheduleTable').DataTable({
            "responsive": true, 
            "lengthChange": true, 
            "autoWidth": false,
            "pageLength": 10,
            "order": [[ 3, "asc" ]], // Order by Start Time
             "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            }
        });
        @endif
    });
</script>
<style>
    /* Soft Badges */
    .bg-green-soft { background-color: #d1fae5; }
    .bg-yellow-soft { background-color: #fef3c7; }
    .bg-gray-soft { background-color: #f1f5f9; }
</style>
@endpush

