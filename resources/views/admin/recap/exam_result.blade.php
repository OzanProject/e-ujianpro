@extends('layouts.admin.app')

@section('title', 'Rekap Hasil Ujian')
@section('page_title', 'Rekap Hasil Ujian')

@section('content')
<div class="row">
    <!-- Filter Section -->
    <div class="col-md-12 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4">
                <form action="{{ route('admin.recap.exam_result') }}" method="GET" id="filterForm">
                    <div class="d-flex flex-column flex-md-row gap-3 align-items-end">
                        <div class="flex-grow-1 w-100">
                            <label class="font-weight-bold text-muted mb-2">Pilih Sesi Ujian</label>
                            <select name="exam_session_id" class="form-control select2" required style="height: 50px;">
                                <option value="">-- Pilih Sesi Ujian --</option>
                                @foreach($examSessions as $session)
                                    <option value="{{ $session->id }}" {{ request('exam_session_id') == $session->id ? 'selected' : '' }}>
                                        {{ $session->subject->name ?? 'Unknown Subject' }} - {{ $session->title }} 
                                        ({{ \Carbon\Carbon::parse($session->start_time)->format('d M Y H:i') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-100 w-md-auto">
                            <button type="submit" class="btn btn-primary h-100 px-4 py-2" style="border-radius: 10px; min-height: 45px;">
                                <i class="fas fa-filter mr-2"></i> Tampilkan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($selectedSession)
    
    <!-- Summary Cards -->
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="font-weight-bold m-0 text-dark">Ringkasan Hasil</h4>
            <a href="{{ route('admin.recap.print_exam_result', ['exam_session_id' => $selectedSession->id]) }}" target="_blank" class="btn btn-outline-success">
                <i class="fas fa-print mr-2"></i> Cetak Laporan
            </a>
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
                    <div class="card-body">
                        <h6 class="text-uppercase mb-2" style="opacity: 0.8; font-size: 0.8rem;">Rata-Rata Nilai</h6>
                        <h2 class="font-weight-bold mb-0">{{ number_format($summary['avg_score'], 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                    <div class="card-body">
                        <h6 class="text-uppercase mb-2" style="opacity: 0.8; font-size: 0.8rem;">Nilai Tertinggi</h6>
                        <h2 class="font-weight-bold mb-0">{{ number_format($summary['max_score'], 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;">
                    <div class="card-body">
                        <h6 class="text-uppercase mb-2" style="opacity: 0.8; font-size: 0.8rem;">Nilai Terendah</h6>
                        <h2 class="font-weight-bold mb-0">{{ number_format($summary['min_score'], 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: white; border-left: 5px solid #3b82f6;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Total Peserta</h6>
                                <h3 class="font-weight-bold text-dark">{{ $summary['total_students'] }}</h3>
                            </div>
                            <div class="text-right">
                                <div class="text-success small"><i class="fas fa-check-circle"></i> {{ $summary['passed'] }} Lulus</div>
                                <div class="text-danger small"><i class="fas fa-times-circle"></i> {{ $summary['failed'] }} Remedial</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Table -->
    <div class="col-12">
        <div class="card border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="font-weight-bold m-0">Detail Peserta</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="recapTable" class="table table-hover table-striped mb-0">
                        <thead class="bg-light text-muted uppercase" style="font-size: 0.85rem; letter-spacing: 0.05em;">
                            <tr>
                                <th class="px-4 py-3 border-0">Rank</th>
                                <th class="px-4 py-3 border-0">Nama Peserta</th>
                                <th class="px-4 py-3 border-0">NIS</th>
                                <th class="px-4 py-3 border-0">Kelas</th>
                                <th class="px-4 py-3 border-0 text-center">Jawaban Benar</th>
                                <th class="px-4 py-3 border-0 text-center">Jawaban Salah</th>
                                <th class="px-4 py-3 border-0 text-center">Nilai Akhir</th>
                                <th class="px-4 py-3 border-0 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attempts as $attempt)
                                <tr>
                                    <td class="px-4 py-3 align-middle font-weight-bold text-muted">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle mr-3 bg-light text-primary d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; font-weight: bold;">
                                                {{ substr($attempt->student->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-weight-bold text-dark">{{ $attempt->student->name }}</div>
                                                <div class="small text-muted">Selesai: {{ \Carbon\Carbon::parse($attempt->end_time)->format('H:i') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-middle">{{ $attempt->student->nis ?? '-' }}</td>
                                    <td class="px-4 py-3 align-middle">
                                        <span class="badge badge-light border">{{ $attempt->student->group->name ?? '-' }}</span>
                                    </td>
                                    
                                    {{-- Robust Correctness Calculation --}}
                                    @php
                                        $correctCount = 0;
                                        $wrongCount = 0;
                                        foreach($attempt->answers as $ans) {
                                            // Fallback Logic: Check flag OR check option directly
                                            if ($ans->is_correct || ($ans->option && $ans->option->is_correct)) {
                                                $correctCount++;
                                            } else {
                                                $wrongCount++;
                                            }
                                        }
                                    @endphp

                                    <td class="px-4 py-3 align-middle text-center text-success font-weight-bold">
                                        {{ $correctCount }}
                                    </td>
                                    <td class="px-4 py-3 align-middle text-center text-danger font-weight-bold">
                                        {{ $wrongCount }}
                                    </td>
                                    <td class="px-4 py-3 align-middle text-center">
                                        <span class="font-weight-bold text-dark" style="font-size: 1.1em;">{{ number_format($attempt->score, 2) }}</span>
                                    </td>
                                    <td class="px-4 py-3 align-middle text-center">
                                        @if($attempt->score >= 75)
                                            <span class="badge badge-soft-success text-success px-3 py-2 rounded-pill" style="background: #ecfdf5; min-width: 80px;">Lulus</span>
                                        @else
                                            <span class="badge badge-soft-danger text-danger px-3 py-2 rounded-pill" style="background: #fef2f2; min-width: 80px;">Remedial</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">Belum ada data hasil ujian.</td>
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
        $('.select2').select2({
            theme: 'bootstrap4'
        });
        
        @if($selectedSession)
        $('#recapTable').DataTable({
            "responsive": true, 
            "lengthChange": true, 
            "autoWidth": false,
            "pageLength": 25,
            "order": [[ 6, "desc" ]], // Urutkan berdasarkan Nilai (Kolom ke-7, index 6) DESC
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            }
        });
        @endif
    });
</script>
@endpush
@section('title', 'Rekap Hasil Ujian')
@section('page_title', 'Rekap Hasil Ujian')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Filter Sesi Ujian</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.recap.exam_result') }}" method="GET">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Pilih Sesi Ujian</label>
                                <select name="exam_session_id" class="form-control select2" required>
                                    <option value="">-- Pilih Sesi --</option>
                                    @foreach($examSessions as $session)
                                        <option value="{{ $session->id }}" {{ request('exam_session_id') == $session->id ? 'selected' : '' }}>
                                            {{ $session->subject->name ?? 'Unknown Subject' }} - {{ $session->title }} ({{ \Carbon\Carbon::parse($session->start_time)->format('d M Y H:i') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Tampilkan Hasil</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($selectedSession)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Detail Hasil: <strong>{{ $selectedSession->subject->name ?? '-' }} - {{ $selectedSession->title }}</strong>
                </h3>
                <div class="card-tools">
                    @if($attempts->count() > 0)
                        <a href="{{ route('admin.recap.print_exam_result', ['exam_session_id' => $selectedSession->id]) }}" target="_blank" class="btn btn-tool text-success">
                            <i class="fas fa-print"></i> Cetak Rekap
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Nama Peserta</th>
                            <th>NIS</th>
                            <th>Kelompok/Kelas</th>
                            <th class="text-center">Benar</th>
                            <th class="text-center">Salah</th>
                            <th class="text-center">Nilai Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attempts as $attempt)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $attempt->student->name }}</td>
                                <td>{{ $attempt->student->nis ?? '-' }}</td>
                                <td>
                                    {{ $attempt->student->group->name ?? '-' }}
                                    @if($attempt->student->kelas)
                                        <small class="text-muted">({{ $attempt->student->kelas }})</small>
                                    @endif
                                </td>
                                <td class="text-center text-success">{{ $attempt->answers->where('is_correct', true)->count() }}</td>
                                <td class="text-center text-danger">{{ $attempt->answers->where('is_correct', false)->count() }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $attempt->score >= 75 ? 'badge-success' : 'badge-warning' }}" style="font-size: 1.1em">
                                        {{ $attempt->score }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada data peserta yang mengerjakan ujian ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
