@extends('layouts.admin.app')

@section('page_title', 'Dashboard Hasil Ujian')

@section('content')
    <style>
        .result-page {
            max-width: 1300px;
            margin: 0 auto;
        }

        .result-html img,
        .question-content img,
        .answer-box img {
            max-width: 100% !important;
            height: auto !important;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 18px rgba(15, 23, 42, .08);
            margin: 10px 0;
        }

        .result-html table,
        .question-content table,
        .answer-box table {
            width: 100% !important;
            display: block;
            overflow-x: auto;
        }

        .summary-card {
            border: 0;
            border-radius: 18px;
            box-shadow: 0 12px 26px rgba(15, 23, 42, .08);
            overflow: hidden;
        }

        .question-item {
            background: #fff;
            border: 1px solid #eef2f7;
            border-radius: 18px;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 8px 18px rgba(15, 23, 42, .045);
        }

        .question-number-circle {
            width: 34px;
            height: 34px;
            min-width: 34px;
            border-radius: 50%;
            background: #4e73df;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            margin-right: .8rem;
        }

        .reading-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .answer-box {
            border-radius: 14px;
            padding: 1rem;
            min-height: 100%;
        }

        .answer-correct {
            background: rgba(40, 167, 69, .04);
            border: 1px solid rgba(40, 167, 69, .35);
            border-left: 5px solid #28a745;
        }

        .answer-wrong {
            background: rgba(220, 53, 69, .04);
            border: 1px solid rgba(220, 53, 69, .35);
            border-left: 5px solid #dc3545;
        }

        .answer-neutral {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 5px solid #f59e0b;
        }

        .bg-light-gray {
            background-color: #f8f9fc;
        }

        .text-xs {
            font-size: 0.75rem;
        }

        .bg-primary-soft { background-color: rgba(78, 115, 223, 0.1); }
        .bg-info-soft { background-color: rgba(54, 185, 204, 0.1); }
        .bg-warning-soft { background-color: rgba(246, 194, 62, 0.1); }
        .bg-secondary-soft { background-color: rgba(133, 135, 150, 0.1); }
        .bg-success-soft { background-color: rgba(40, 167, 69, 0.1); }
        .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }

        .option-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 0.75rem;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s;
        }
        
        .option-label-circle {
            width: 24px;
            height: 24px;
            min-width: 24px;
            border-radius: 50%;
            background: #f1f5f9;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            margin-right: 0.75rem;
            border: 1px solid #e2e8f0;
        }

        .option-label-circle.active {
            background: #4e73df;
            color: #fff;
            border-color: #4e73df;
        }

        .pagination .page-link {
            border-radius: 5px;
            margin: 0 2px;
        }
    </style>

    <div class="result-page">
        <div class="row mb-4">
            <div class="col-12">
                    <a href="{{ route($baseRoute . '.item_analysis', ['exam_session_id' => $attempt->exam_session_id]) }}" 
                       class="btn btn-light border-gray-300 shadow-sm rounded-lg px-4 hover:bg-gray-50 transition-all font-weight-bold text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
            </div>
        </div>

        {{-- Statistic Dashboard --}}
        <div class="row mb-4">
            <div class="col-md-3 mb-3 mb-md-0">
                <div class="card summary-card h-100">
                    <div class="card-body p-4 text-center text-white"
                        style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                        <p class="text-uppercase font-weight-bold text-xs mb-2" style="opacity:.8;">Skor Akhir</p>
                        @if($attempt->examSession->show_score)
                            <h2 class="font-weight-bold mb-0" style="font-size: 2.5rem;">{{ number_format($attempt->score, 1) }}
                            </h2>
                        @else
                            <h2 class="font-weight-bold mb-0"><i class="fas fa-eye-slash"></i></h2>
                            <p class="text-xs mt-2 mb-0">Disembunyikan</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="row h-100">
                    <div class="col-sm-3 mb-3 mb-sm-0">
                        <div class="card border-0 shadow-sm rounded-lg h-100"
                            style="border-left: 5px solid #28a745 !important;">
                            <div class="card-body p-3 d-flex align-items-center">
                                <div class="rounded-circle p-3 mr-3" style="background: rgba(40,167,69,0.1);">
                                    <i class="fas fa-check text-success fa-lg"></i>
                                </div>
                                <div>
                                    <p class="text-muted text-xs font-weight-bold text-uppercase mb-1">Benar</p>
                                    <h4 class="font-weight-bold mb-0 text-dark">{{ $stats['correct'] ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3 mb-3 mb-sm-0">
                        <div class="card border-0 shadow-sm rounded-lg h-100"
                            style="border-left: 5px solid #dc3545 !important;">
                            <div class="card-body p-3 d-flex align-items-center">
                                <div class="rounded-circle p-3 mr-3" style="background: rgba(220,53,69,0.1);">
                                    <i class="fas fa-times text-danger fa-lg"></i>
                                </div>
                                <div>
                                    <p class="text-muted text-xs font-weight-bold text-uppercase mb-1">Salah</p>
                                    <h4 class="font-weight-bold mb-0 text-dark">{{ $stats['wrong'] ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3 mb-3 mb-sm-0">
                        <div class="card border-0 shadow-sm rounded-lg h-100"
                            style="border-left: 5px solid #ffc107 !important;">
                            <div class="card-body p-3 d-flex align-items-center">
                                <div class="rounded-circle p-3 mr-3" style="background: rgba(255,193,7,0.1);">
                                    <i class="fas fa-pen-fancy text-warning fa-lg"></i>
                                </div>
                                <div>
                                    <p class="text-muted text-xs font-weight-bold text-uppercase mb-1">Esai</p>
                                    <h4 class="font-weight-bold mb-0 text-dark">{{ $stats['essay'] ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="card border-0 shadow-sm rounded-lg h-100"
                            style="border-left: 5px solid #6c757d !important;">
                            <div class="card-body p-3 d-flex align-items-center">
                                <div class="rounded-circle p-3 mr-3" style="background: rgba(108,117,125,0.1);">
                                    <i class="fas fa-minus text-secondary fa-lg"></i>
                                </div>
                                <div>
                                    <p class="text-muted text-xs font-weight-bold text-uppercase mb-1">Kosong</p>
                                    <h4 class="font-weight-bold mb-0 text-dark">{{ $stats['empty'] ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Detail Sesi --}}
            <div class="col-lg-4 mb-4">
                <div class="card summary-card">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-info-circle mr-2 text-primary"></i> Detail Sesi
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="p-4 bg-light-gray">
                            <h5 class="font-weight-bold text-primary mb-1">
                                {{ $attempt->examSession->subject->name ?? 'Mata Pelajaran' }}
                            </h5>
                            <p class="text-xs text-muted mb-0 font-weight-bold">
                                {{ $attempt->examSession->examPackage->title ?? 'Paket Campuran / Semua Soal' }}
                            </p>
                        </div>
                        <div class="table-responsive">
                            <table class="table mb-0 text-sm">
                                <tr>
                                    <td class="text-muted px-4 py-3">Mulai</td>
                                    <td class="font-weight-bold text-right px-4 py-3">
                                        {{ $attempt->start_time ? $attempt->start_time->format('H:i') : '-' }}
                                        <span class="text-xs font-weight-normal text-muted">
                                            (@tgl($attempt->start_time))
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted px-4 py-3">Selesai</td>
                                    <td class="font-weight-bold text-right px-4 py-3">
                                        {{ $attempt->end_time ? $attempt->end_time->format('H:i') : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted px-4 py-3">Durasi Pakai</td>
                                    <td class="font-weight-bold text-right px-4 py-3">
                                        @if($attempt->start_time && $attempt->end_time)
                                            {{ round($attempt->start_time->diffInMinutes($attempt->end_time)) }} Menit
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted px-4 py-3 border-bottom-0">Total Soal</td>
                                    <td class="font-weight-bold text-right px-4 py-3 border-bottom-0">
                                        {{ $stats['total'] ?? 0 }} Butir
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card summary-card mt-4 d-none d-lg-block">
                    <div class="card-body p-4 text-center">
                        @php
                            $total = $stats['total'] ?? 0;
                            $correct = $stats['correct'] ?? 0;
                            $accuracy = $total > 0 ? ($correct / $total) * 100 : 0;
                        @endphp
                        <div class="mx-auto mb-3"
                            style="width: 100px; height: 100px; border-radius: 50%; border: 8px solid #f8f9fa; border-top-color: #4e73df; display: flex; align-items: center; justify-content: center;">
                            <h4 class="mb-0 font-weight-bold">{{ round($accuracy) }}%</h4>
                        </div>
                        <p class="text-xs font-weight-bold text-uppercase text-muted mb-0">Tingkat Akurasi</p>
                    </div>
                </div>
            </div>

            {{-- Rincian Jawaban --}}
            <div class="col-lg-8">
                <div class="card summary-card">
                    <div
                        class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center">
                        <h6 class="font-weight-bold text-dark mb-0 mr-3">
                            <i class="fas fa-list-ol mr-2 text-primary"></i> Rincian Jawaban
                        </h6>

                        <form action="{{ request()->fullUrl() }}" method="GET" class="form-inline mt-2 mt-md-0">
                            <small class="text-muted font-weight-bold mr-2 text-xs text-uppercase">Tampilkan:</small>
                            <select name="per_page" class="form-control form-control-sm rounded-pill px-3"
                                onchange="this.form.submit()">
                                <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ ($perPage ?? 10) == 20 ? 'selected' : '' }}>20</option>
                                <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </form>
                    </div>

                    <div class="card-body p-4">
                        @if(!$attempt->examSession->show_score)
                            <div class="text-center py-5">
                                <div class="bg-light rounded-circle p-4 d-inline-block mb-3">
                                    <i class="fas fa-lock text-muted fa-3x"></i>
                                </div>
                                <h5 class="font-weight-bold text-dark">Detail Jawaban Dikunci</h5>
                                <p class="text-muted text-sm mb-0">Administrator tidak mengaktifkan fitur tinjauan jawaban untuk
                                    sesi ujian ini.</p>
                            </div>
                        @else
                            @forelse($answers as $index => $answer)
                                    @php
                                        $question = $answer->question;
                                        $questionNumber = ($answers->currentPage() - 1) * $answers->perPage() + $index + 1;
                                        $isObjective = in_array($question->type ?? '', ['multiple_choice', 'multiple_choice_complex', 'boolean_grid']);
                                        $isCorrect = (bool) $answer->is_correct || ($answer->option && $answer->option->is_correct);
                                        $answerText = $answer->answer_text;

                                        $typeConfig = match($question->type ?? '') {
                                            'multiple_choice'         => ['icon'=>'fa-list-ol',     'color'=>'text-primary',  'bg' => 'bg-primary-soft', 'label'=>'PG Tunggal'],
                                            'multiple_choice_complex' => ['icon'=>'fa-check-double','color'=>'text-info',     'bg' => 'bg-info-soft',    'label'=>'PG Kompleks'],
                                            'boolean_grid'            => ['icon'=>'fa-th',          'color'=>'text-warning',  'bg' => 'bg-warning-soft', 'label'=>'Benar/Salah'],
                                            'essay'                   => ['icon'=>'fa-pen-nib',     'color'=>'text-secondary','bg' => 'bg-secondary-soft','label'=>'Esai'],
                                            default                   => ['icon'=>'fa-question',    'color'=>'text-muted',    'bg' => 'bg-light',        'label'=>$question->type ?? 'Unknown'],
                                        };
                                    @endphp

                                <div class="question-item">
                                    <div class="d-flex align-items-start">
                                        <span class="question-number-circle">{{ $questionNumber }}</span>

                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="badge {{ $typeConfig['bg'] }} {{ $typeConfig['color'] }} px-2 py-1 rounded text-xs font-weight-bold">
                                                    <i class="fas {{ $typeConfig['icon'] }} mr-1"></i> {{ $typeConfig['label'] }}
                                                </span>
                                                @if($isObjective)
                                                    @if($isCorrect)
                                                        <span class="text-success text-xs font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Skor Penuh</span>
                                                    @else
                                                        <span class="text-danger text-xs font-weight-bold"><i class="fas fa-times-circle mr-1"></i> Tidak Tepat</span>
                                                    @endif
                                                @endif
                                            </div>

                                            @if($question && $question->readingText)
                                                <div class="reading-box">
                                                    <div class="font-weight-bold text-primary text-xs text-uppercase mb-2">
                                                        <i class="fas fa-book-open mr-1"></i> Stimulus / Bacaan
                                                    </div>
                                                    <div class="result-html">
                                                        {!! $question->readingText->content !!}
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="question-content text-dark mb-3 result-html" style="line-height: 1.7;">
                                                {!! $question->content ?? '<em>Soal tidak ditemukan</em>' !!}
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3 mb-md-0">
                                                    <div
                                                        class="answer-box {{ $isObjective ? ($isCorrect ? 'answer-correct' : 'answer-wrong') : 'answer-neutral' }}">
                                                        <small
                                                            class="text-uppercase text-xs font-weight-bold mb-2 d-block {{ $isObjective ? ($isCorrect ? 'text-success' : 'text-danger') : 'text-warning' }}">
                                                            Jawaban Anda
                                                        </small>

                                                        @if($answer->question_option_id && $answer->option)
                                                            <div class="options-list">
                                                                @foreach($question->options as $optIndex => $option)
                                                                    @php
                                                                        $isSelected = $answer->question_option_id == $option->id;
                                                                    @endphp
                                                                    <div class="option-item {{ $isSelected ? ($isCorrect ? 'bg-success-soft' : 'bg-danger-soft') : '' }}">
                                                                        <div class="option-label-circle {{ $isSelected ? 'active' : '' }}">
                                                                            {{ chr(65 + $optIndex) }}
                                                                        </div>
                                                                        <div class="result-html flex-grow-1 {{ $isSelected ? 'font-weight-bold' : '' }}">
                                                                            {!! $option->content !!}
                                                                        </div>
                                                                        @if($isSelected)
                                                                            <i class="fas {{ $isCorrect ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} ml-2 mt-1"></i>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @elseif($answerText)
                                                            @if(($question->type ?? '') === 'multiple_choice_complex')
                                                                @php
                                                                    $selectedIds = json_decode($answerText, true);
                                                                    if (!is_array($selectedIds))
                                                                        $selectedIds = [];
                                                                @endphp
                                                                 @if(count($selectedIds))
                                                                    <div class="options-list">
                                                                        @foreach($question->options as $optIndex => $option)
                                                                            @php
                                                                                $isSelected = in_array((string) $option->id, array_map('strval', $selectedIds));
                                                                            @endphp
                                                                            <div class="option-item {{ $isSelected ? 'bg-light' : '' }}">
                                                                                <div class="option-label-circle {{ $isSelected ? 'active' : '' }}">
                                                                                    {{ chr(65 + $optIndex) }}
                                                                                </div>
                                                                                <div class="result-html flex-grow-1">
                                                                                    {!! $option->content !!}
                                                                                </div>
                                                                                @if($isSelected)
                                                                                    <i class="fas fa-check-circle text-primary ml-2 mt-1"></i>
                                                                                @endif
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted font-italic">Tidak Dijawab</span>
                                                                @endif
                                                            @elseif(($question->type ?? '') === 'boolean_grid')
                                                                @php
                                                                    $gridAnswers = json_decode($answerText, true);
                                                                    if (!is_array($gridAnswers))
                                                                        $gridAnswers = [];
                                                                @endphp
                                                                @if(count($gridAnswers))
                                                                    <div class="table-responsive">
                                                                        <table class="table table-sm table-bordered bg-white mb-0">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Pernyataan</th>
                                                                                    <th class="text-center" width="90">Jawaban</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach($question->options as $gridOption)
                                                                                    @php
                                                                                        $gridValue = $gridAnswers[$gridOption->id] ?? null;
                                                                                    @endphp
                                                                                    <tr>
                                                                                        <td class="result-html">{!! $gridOption->content !!}</td>
                                                                                        <td class="text-center">
                                                                                            @if($gridValue === '1' || $gridValue === 1)
                                                                                                <span class="badge badge-success">Benar</span>
                                                                                            @elseif($gridValue === '0' || $gridValue === 0)
                                                                                                <span class="badge badge-danger">Salah</span>
                                                                                            @else
                                                                                                <span class="badge badge-secondary">Kosong</span>
                                                                                            @endif
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted font-italic">Tidak Dijawab</span>
                                                                @endif
                                                            @else
                                                                <div class="p-3 border rounded bg-white text-gray-700 shadow-sm" style="border-left: 4px solid #f6c23e !important;">
                                                                    {!! nl2br(e($answerText)) !!}
                                                                </div>
                                                                @if($answer->score > 0)
                                                                    <div class="mt-2 d-flex align-items-center">
                                                                        <span class="badge badge-success px-2 py-1 text-xs">Sudah Dinilai</span>
                                                                        <span class="ml-2 font-weight-bold text-success text-sm">Skor: {{ $answer->score }}</span>
                                                                    </div>
                                                                @else
                                                                    <span class="badge badge-warning mt-2 px-2 py-1 text-white text-xs">Menunggu Koreksi Guru</span>
                                                                @endif
                                                            @endif
                                                        @else
                                                            <span class="text-muted font-italic">Tidak Dijawab</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="answer-box answer-correct">
                                                        <small
                                                            class="text-uppercase text-xs font-weight-bold mb-2 d-block text-success">
                                                            Kunci Jawaban
                                                        </small>

                                                        @if($question && $question->type === 'boolean_grid')
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-bordered bg-white mb-0">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Pernyataan</th>
                                                                            <th class="text-center" width="90">Kunci</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($question->options as $gridOption)
                                                                            <tr>
                                                                                <td class="result-html">{!! $gridOption->content !!}</td>
                                                                                <td class="text-center">
                                                                                    @if($gridOption->is_correct)
                                                                                        <span class="badge badge-success">Benar</span>
                                                                                    @else
                                                                                        <span class="badge badge-danger">Salah</span>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @elseif($question && in_array($question->type, ['multiple_choice', 'multiple_choice_complex']))
                                                            <div class="options-list">
                                                                @foreach($question->options as $optIndex => $opt)
                                                                    @if($opt->is_correct)
                                                                        <div class="option-item bg-success-soft">
                                                                            <div class="option-label-circle bg-success text-white border-success">
                                                                                {{ chr(65 + $optIndex) }}
                                                                            </div>
                                                                            <div class="result-html flex-grow-1 text-dark">
                                                                                {!! $opt->content !!}
                                                                            </div>
                                                                            <i class="fas fa-key text-success ml-2 mt-1"></i>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <span class="text-muted">Esai menunggu koreksi guru.</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5 class="font-weight-bold text-dark">Belum ada jawaban tersimpan</h5>
                                    <p class="text-muted mb-0">Data jawaban belum tersedia.</p>
                                </div>
                            @endforelse

                            <div class="d-flex justify-content-center mt-4">
                                {{ $answers->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection