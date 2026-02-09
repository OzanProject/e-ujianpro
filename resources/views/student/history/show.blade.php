@extends('layouts.student.app')

@section('page_title', 'Detail Hasil Ujian')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Informasi Ujian</h3>
            </div>
            <div class="card-body box-profile">
                <h3 class="profile-username text-center">{{ $attempt->examSession->subject->name }}</h3>
                <p class="text-muted text-center">{{ $attempt->examSession->examPackage->title ?? 'Paket Soal Acak / Semua Soal' }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Tanggal</b> <a class="float-right">{{ $attempt->start_time->format('d M Y H:i') }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Waktu Selesai</b> <a class="float-right">{{ $attempt->end_time ? $attempt->end_time->format('d M Y H:i') : '-' }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Nilai Akhir</b> 
                        @if($attempt->examSession->show_score)
                            <a class="float-right badge badge-success" style="font-size: 1.2em">{{ number_format($attempt->score, 2) }}</a>
                        @else
                            <a class="float-right badge badge-secondary"><i class="fas fa-eye-slash"></i> Hidden</a>
                        @endif
                    </li>
                </ul>

                <a href="{{ request()->route('subdomain') ? route('institution.student.history.index', request()->route('subdomain')) : route('student.history.index') }}" class="btn btn-default btn-block"><b>Kembali ke Riwayat</b></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Rincian Jawaban</h3>
            </div>
            <div class="card-body">
                @if(!$attempt->examSession->show_score)
                    <div class="alert alert-warning">
                        <i class="fas fa-lock mr-2"></i> Rincian jawaban dan pembahasan untuk ujian ini tidak ditampilkan.
                    </div>
                @else
                    {{-- Only show details if allowed --}}
                    @foreach($attempt->answers as $index => $answer)
                        <div class="post">
                            <div class="user-block">
                                <span class="username" style="margin-left: 0">
                                    <a href="#">Soal No. {{ $index + 1 }}</a>
                                </span>
                            </div>
                            <p>
                                {!! $answer->question->content !!}
                            </p>
                            
                            <div class="callout {{ ($answer->is_correct || ($answer->option && $answer->option->is_correct)) ? 'callout-success' : 'callout-danger' }}">
                                <h5>Jawaban Anda:</h5>
                                <p>
                                    @if($answer->question_option_id)
                                        {!! $answer->option->content !!} 
                                        @php
                                            // Fallback check: Use ExamAnswer flag OR explicit check on Option
                                            $isActuallyCorrect = $answer->is_correct || ($answer->option && $answer->option->is_correct);
                                        @endphp
                                        @if($isActuallyCorrect)
                                            <i class="fas fa-check-circle text-success"></i> (Benar)
                                        @else
                                            <i class="fas fa-times-circle text-danger"></i> (Salah)
                                        @endif
                                    @elseif($answer->essay_answer)
                                        <div class="p-2 bg-light border rounded">
                                            {!! nl2br(e($answer->essay_answer)) !!}
                                        </div>
                                        <small class="text-muted"><i class="fas fa-pencil-alt ml-1"></i> Soal Esai (Menunggu Koreksi)</small>
                                    @else
                                        <span class="text-muted">Tidak Dijawab</span>
                                    @endif
                                </p>
                                
                                {{-- Show correct answer if wrong --}}
                                @if(!($answer->is_correct || ($answer->option && $answer->option->is_correct)) && $answer->question_option_id)
                                    <hr>
                                    <h6>Kunci Jawaban:</h6>
                                    @foreach($answer->question->options as $opt)
                                        @if($opt->is_correct)
                                            <p class="text-success font-weight-bold">{!! $opt->content !!}</p>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
