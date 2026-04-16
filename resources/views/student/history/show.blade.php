@extends('layouts.student.app')

@section('page_title', 'Dashboard Hasil Ujian')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <a href="{{ request()->route('subdomain') ? route('institution.student.history.index', request()->route('subdomain')) : route('student.history.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Riwayat
        </a>
    </div>
</div>

{{-- Statistic Dashboard --}}
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100">
            <div class="card-body p-4 text-center bg-gradient-to-br from-blue-600 to-indigo-700 text-white" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                <p class="text-uppercase font-weight-bold text-xs mb-2 opacity-75">Skor Akhir</p>
                @if($attempt->examSession->show_score)
                    <h2 class="font-weight-bold mb-0" style="font-size: 2.5rem;">{{ number_format($attempt->score, 1) }}</h2>
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
                <div class="card border-0 shadow-sm rounded-lg h-100 border-left-success" style="border-left: 5px solid #28a745 !important;">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="bg-success-light rounded-circle p-3 mr-3" style="background: rgba(40,167,69,0.1);">
                            <i class="fas fa-check text-success fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-muted text-xs font-weight-bold text-uppercase mb-1">Benar</p>
                            <h4 class="font-weight-bold mb-0 text-dark">{{ $stats['correct'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 mb-3 mb-sm-0">
                <div class="card border-0 shadow-sm rounded-lg h-100 border-left-danger" style="border-left: 5px solid #dc3545 !important;">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="bg-danger-light rounded-circle p-3 mr-3" style="background: rgba(220,53,69,0.1);">
                            <i class="fas fa-times text-danger fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-muted text-xs font-weight-bold text-uppercase mb-1">Salah</p>
                            <h4 class="font-weight-bold mb-0 text-dark">{{ $stats['wrong'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 mb-3 mb-sm-0">
                <div class="card border-0 shadow-sm rounded-lg h-100 border-left-warning" style="border-left: 5px solid #ffc107 !important;">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="bg-warning-light rounded-circle p-3 mr-3" style="background: rgba(255,193,7,0.1);">
                            <i class="fas fa-pen-fancy text-warning fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-muted text-xs font-weight-bold text-uppercase mb-1">Esai</p>
                            <h4 class="font-weight-bold mb-0 text-dark">{{ $stats['essay'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card border-0 shadow-sm rounded-lg h-100 border-left-secondary" style="border-left: 5px solid #6c757d !important;">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="bg-secondary-light rounded-circle p-3 mr-3" style="background: rgba(108,117,125,0.1);">
                            <i class="fas fa-minus text-secondary fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-muted text-xs font-weight-bold text-uppercase mb-1">Kosong</p>
                            <h4 class="font-weight-bold mb-0 text-dark">{{ $stats['empty'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="font-weight-bold text-dark mb-0"><i class="fas fa-info-circle mr-2 text-primary"></i> Detail Sesi</h6>
            </div>
            <div class="card-body p-0">
                <div class="p-4 bg-light-gray">
                    <h5 class="font-weight-bold text-primary mb-1">{{ $attempt->examSession->subject->name }}</h5>
                    <p class="text-xs text-muted mb-0 font-weight-bold">{{ $attempt->examSession->examPackage->title ?? 'Paket Campuran' }}</p>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0 text-sm">
                        <tr>
                            <td class="text-muted px-4 py-3">Mulai</td>
                            <td class="font-weight-bold text-right px-4 py-3">{{ $attempt->start_time->format('H:i') }} <span class="text-xs font-weight-normal text-muted">({{ $attempt->start_time->format('d/m/y') }})</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted px-4 py-3">Selesai</td>
                            <td class="font-weight-bold text-right px-4 py-3">{{ $attempt->end_time ? $attempt->end_time->format('H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted px-4 py-3">Durasi Pakai</td>
                            <td class="font-weight-bold text-right px-4 py-3">
                                @if($attempt->end_time)
                                    {{ round($attempt->start_time->diffInMinutes($attempt->end_time)) }} Menit
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted px-4 py-3 border-bottom-0">Total Soal</td>
                            <td class="font-weight-bold text-right px-4 py-3 border-bottom-0">{{ $stats['total'] }} Butir</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-lg mt-4 d-none d-lg-block">
            <div class="card-body p-4 text-center">
                <div class="accuracy-circle mx-auto mb-3" style="width: 100px; height: 100px; border-radius: 50%; border: 8px solid #f8f9fa; border-top-color: #4e73df; display: flex; align-items: center; justify-content: center;">
                    <div class="text-center">
                        @php
                            $accuracy = $stats['total'] > 0 ? ($stats['correct'] / $stats['total']) * 100 : 0;
                        @endphp
                        <h4 class="mb-0 font-weight-bold">{{ round($accuracy) }}%</h4>
                    </div>
                </div>
                <p class="text-xs font-weight-bold text-uppercase text-muted mb-0">Tingkat Akurasi</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center">
                <h6 class="font-weight-bold text-dark mb-0 mr-3"><i class="fas fa-list-ol mr-2 text-primary"></i> Rincian Jawaban</h6>
                
                <div class="d-flex align-items-center mt-2 mt-md-0">
                    <form action="{{ request()->fullUrl() }}" method="GET" class="form-inline mr-3">
                        <small class="text-muted font-weight-bold mr-2 text-xs text-uppercase">Tampilkan:</small>
                        <select name="per_page" class="form-control form-control-sm rounded-pill px-3" onchange="this.form.submit()">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                    <div class="text-xs font-weight-bold text-muted border-left pl-3 ml-1">Halaman {{ $answers->currentPage() }} dari {{ $answers->lastPage() }}</div>
                </div>
            </div>
            <div class="card-body p-4">
                @if(!$attempt->examSession->show_score)
                    <div class="text-center py-5">
                        <div class="bg-light rounded-circle p-4 d-inline-block mb-3">
                            <i class="fas fa-lock text-muted fa-3x"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark">Detail Jawaban Dikunci</h5>
                        <p class="text-muted max-w-xs mx-auto text-sm">Maaf, administrator tidak mengaktifkan fitur tinjauan jawaban untuk sesi ujian ini.</p>
                    </div>
                @else
                    @foreach($answers as $index => $answer)
                        <div class="question-item mb-5 pb-4 border-bottom last-border-0">
                            <div class="d-flex align-items-start mb-3">
                                <div class="badge badge-primary rounded-circle mr-3 mt-1 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.9rem;">
                                    {{ ($answers->currentPage() - 1) * $answers->perPage() + $index + 1 }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="question-content text-dark mb-3" style="line-height: 1.6;">
                                        {!! $answer->question->content !!}
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <div class="p-3 rounded-lg border {{ ($answer->is_correct || ($answer->option && $answer->option->is_correct)) ? 'bg-success-fade border-success' : 'bg-danger-fade border-danger' }}" style="background: {{ ($answer->is_correct || ($answer->option && $answer->option->is_correct)) ? 'rgba(40,167,69,0.03)' : 'rgba(220,53,69,0.03)' }};">
                                                <small class="text-uppercase text-xs font-weight-bold mb-2 d-block {{ ($answer->is_correct || ($answer->option && $answer->option->is_correct)) ? 'text-success' : 'text-danger' }}">Jawaban Anda</small>
                                                <div class="text-sm font-weight-bold">
                                                    @if($answer->question_option_id)
                                                        {!! $answer->option->content !!}
                                                        <div class="mt-2">
                                                            @if($answer->is_correct || ($answer->option && $answer->option->is_correct))
                                                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Benar</span>
                                                            @else
                                                                <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> Salah</span>
                                                            @endif
                                                        </div>
                                                    @elseif($answer->answer_text)
                                                        <div class="p-2 border rounded bg-white text-gray-700">
                                                            {!! nl2br(e($answer->answer_text)) !!}
                                                        </div>
                                                        <span class="badge badge-warning mt-2 px-2 py-1 text-white text-xs">Menunggu Koreksi</span>
                                                    @else
                                                        <span class="text-muted font-italic">Tidak Dijawab</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if(!($answer->is_correct || ($answer->option && $answer->option->is_correct)) && $answer->question_option_id)
                                            <div class="col-md-6">
                                                <div class="p-3 rounded-lg border border-success bg-white h-100" style="border-style: dashed !important;">
                                                    <small class="text-uppercase text-xs font-weight-bold mb-2 d-block text-success">Kunci Jawaban</small>
                                                    <div class="text-sm font-weight-bold text-success">
                                                        @foreach($answer->question->options as $opt)
                                                            @if($opt->is_correct)
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-key text-xs mr-2 opacity-50"></i>
                                                                    <div>{!! $opt->content !!}</div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-center mt-4">
                        {{ $answers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light-gray { background-color: #f8f9fc; }
    .last-border-0:last-child { border-bottom: none !important; margin-bottom: 0 !important; }
    .pagination .page-link { border-radius: 5px; margin: 0 2px; }
    .accuracy-circle { border-width: 8px !important; }
    .text-xs { font-size: 0.75rem; }
    .bg-success-fade { border-left: 4px solid #28a745 !important; }
    .bg-danger-fade { border-left: 4px solid #dc3545 !important; }
</style>
@endsection
