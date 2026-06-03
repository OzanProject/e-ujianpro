@extends('layouts.admin.app')

@section('title', 'Analisis Butir Soal')
@section('page_title', 'Analisis Butir Soal')

@push('styles')
<style>
    .table-analysis th, .table-analysis td {
        vertical-align: middle !important;
        white-space: nowrap;
    }
    .col-fixed {
        position: sticky;
        left: 0;
        background-color: #fff;
        z-index: 1;
        box-shadow: 2px 0 5px rgba(0,0,0,0.05);
    }
    .col-fixed-2 {
        position: sticky;
        left: 50px;
        background-color: #fff;
        z-index: 1;
        box-shadow: 2px 0 5px rgba(0,0,0,0.05);
    }
    .table-analysis thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    .table-analysis thead th.col-fixed,
    .table-analysis thead th.col-fixed-2 {
        z-index: 2;
    }
</style>
@endpush

@section('content')
<div class="row">
    <!-- Filter Section -->
    <div class="col-md-12 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4">
                <form action="{{ route($baseRoute . '.item_analysis') }}" method="GET" id="filterForm">
                    <div class="row align-items-end">
                        <div class="col-md-7 mb-3 mb-md-0">
                            <label class="font-weight-bold text-muted mb-2">Pilih Sesi Ujian</label>
                            <select name="exam_session_id" class="form-control select2" required style="height: 50px;">
                                <option value="">-- Pilih Sesi Ujian --</option>
                                @foreach($examSessions as $session)
                                    <option value="{{ $session->id }}" {{ request('exam_session_id') == $session->id ? 'selected' : '' }}>
                                        {{ $session->subject->name ?? 'Unknown Subject' }} (Kelas: {{ $session->target_kelas ?? 'Semua' }}) - {{ $session->title }} 
                                        (@tgl_jam(\Carbon\Carbon::parse($session->start_time)))
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label class="font-weight-bold text-muted mb-2">Nilai KKM</label>
                            <input type="number" name="kkm" class="form-control" value="{{ $kkm }}" min="0" max="100" style="height: 45px; border-radius: 10px;">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100 px-4 py-2" style="border-radius: 10px; min-height: 45px;">
                                <i class="fas fa-filter mr-2"></i> Tampilkan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($selectedSession)
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="font-weight-bold m-0 text-dark">Matriks Analisis Butir Soal</h5>
            <a href="{{ route($baseRoute . '.print_item_analysis', ['exam_session_id' => $selectedSession->id, 'kkm' => $kkm]) }}" target="_blank" class="btn btn-success btn-print-with-date">
                <i class="fas fa-print mr-2"></i> Cetak Analisis
            </a>
        </div>
    </div>

    <!-- Detailed Table -->
    <div class="col-12">
        <div class="card border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 600px;">
                    <table class="table table-bordered table-hover table-analysis mb-0 text-center">
                        <thead>
                            <tr>
                                <th class="col-fixed" style="width: 50px;">No</th>
                                <th class="col-fixed-2 text-left" style="min-width: 250px;">Nama Peserta</th>
                                @foreach($questions as $index => $question)
                                    <th title="Soal No {{ $index + 1 }}">{{ $index + 1 }}</th>
                                @endforeach
                                <th style="min-width: 80px;">Benar</th>
                                <th style="min-width: 80px;">Salah</th>
                                <th style="min-width: 100px;">Nilai Akhir</th>
                                <th style="min-width: 100px;">Ketuntasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $questionStats = [];
                                foreach($questions as $q) {
                                    $questionStats[$q->id] = ['correct' => 0, 'total' => 0];
                                }
                            @endphp

                            @forelse($attempts as $attempt)
                                <tr>
                                    <td class="col-fixed bg-white">{{ $loop->iteration }}</td>
                                    <td class="col-fixed-2 bg-white text-left font-weight-bold text-dark">
                                        {{ $attempt->student->name }}
                                        <br><small class="text-muted font-weight-normal">{{ $attempt->student->group->name ?? '-' }}</small>
                                    </td>
                                    
                                    @php
                                        $correctCount = 0;
                                        $wrongCount = 0;
                                        $attemptAnswers = $attempt->answers->keyBy('question_id');
                                    @endphp

                                    @foreach($questions as $question)
                                        @php
                                            $ans = $attemptAnswers->get($question->id);
                                            $questionStats[$question->id]['total']++;
                                            
                                            $isCorrect = false;
                                            $score = 0;

                                            if ($ans) {
                                                if ($question->type == 'essay') {
                                                    $score = $ans->score;
                                                    // Jika nilai essay > 0 anggap benar sebagian/penuh
                                                    $isCorrect = $score > 0;
                                                } else {
                                                    $isCorrect = $ans->is_correct || ($ans->option && $ans->option->is_correct);
                                                }
                                            }

                                            if ($isCorrect && $question->type != 'essay') {
                                                $questionStats[$question->id]['correct']++;
                                                $correctCount++;
                                            } elseif ($question->type != 'essay') {
                                                $wrongCount++;
                                            }
                                        @endphp
                                        
                                        <td>
                                            @if($question->type == 'essay')
                                                <span class="font-weight-bold {{ $score > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $ans ? $score : 0 }}
                                                </span>
                                            @else
                                                @if($isCorrect)
                                                    <i class="fas fa-check text-success font-weight-bold"></i>
                                                @else
                                                    <i class="fas fa-times text-danger font-weight-bold"></i>
                                                @endif
                                            @endif
                                        </td>
                                    @endforeach
                                    
                                    <td class="text-success font-weight-bold">{{ $correctCount }}</td>
                                    <td class="text-danger font-weight-bold">{{ $wrongCount }}</td>
                                    <td>
                                        <span class="font-weight-bold text-dark" style="font-size: 1.1em;">{{ number_format($attempt->score, 2) }}</span>
                                    </td>
                                    <td>
                                        @if($attempt->score >= $kkm)
                                            <span class="badge badge-soft-success text-success px-3 py-1 rounded-pill" style="background: #ecfdf5;">Tuntas</span>
                                        @else
                                            <span class="badge badge-soft-danger text-danger px-3 py-1 rounded-pill" style="background: #fef2f2;">Belum</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($questions) + 6 }}" class="text-center py-5 text-muted">Belum ada data hasil ujian.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($attempts->count() > 0)
                        <tfoot>
                            <tr class="bg-light">
                                <td colspan="2" class="col-fixed font-weight-bold text-right" style="z-index: 3;">Persentase Menjawab Benar:</td>
                                @foreach($questions as $question)
                                    @php
                                        $stats = $questionStats[$question->id];
                                        $percentage = $stats['total'] > 0 ? round(($stats['correct'] / $stats['total']) * 100) : 0;
                                    @endphp
                                    <td class="font-weight-bold {{ $percentage < 50 && $question->type != 'essay' ? 'text-danger' : 'text-success' }}">
                                        @if($question->type == 'essay')
                                            -
                                        @else
                                            {{ $percentage }}%
                                        @endif
                                    </td>
                                @endforeach
                                <td colspan="4"></td>
                            </tr>
                        </tfoot>
                        @endif
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
    });
</script>
@endpush
