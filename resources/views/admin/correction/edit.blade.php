@extends('layouts.admin.app')

@section('title', 'Koreksi Jawaban')
@section('page_title', 'Koreksi Jawaban: ' . $attempt->user->name)

@section('content')
<div class="row">
    <div class="col-md-12">
        <form action="{{ route('admin.correction.update', $attempt->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Detail Ujian</h3>
                    <div class="card-tools">Total Skor Saat Ini: <strong>{{ number_format($attempt->total_score, 2) }}</strong></div>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Mapel</dt>
                        <dd class="col-sm-9">{{ $attempt->examSession->subject->name }}</dd>
                        <dt class="col-sm-3">Judul Ujian</dt>
                        <dd class="col-sm-9">{{ $attempt->examSession->title }}</dd>
                    </dl>
                </div>
            </div>

            @foreach($attempt->answers as $index => $answer)
                <div class="card card-outline {{ $answer->question->type == 'essay' ? 'card-warning' : 'card-success' }}">
                    <div class="card-header">
                        <h3 class="card-title">
                            Soal #{{ $index + 1 }} 
                            <span class="badge {{ $answer->question->type == 'essay' ? 'badge-warning' : 'badge-info' }}">
                                {{ $answer->question->type == 'essay' ? 'Essay' : 'Pilgan' }}
                            </span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="font-weight-bold">{!! nl2br(e($answer->question->content)) !!}</p>
                        
                        <hr>
                        <strong>Jawaban Siswa:</strong>
                        <div class="p-2 bg-light border rounded mt-2">
                             @if($answer->question->type == 'multiple_choice')
                                {{ $answer->option->content ?? '(Tidak Menjawab)' }}
                                @if(optional($answer->option)->is_correct)
                                    <i class="fas fa-check-circle text-success ml-2"></i> (Benar)
                                @else
                                    <i class="fas fa-times-circle text-danger ml-2"></i> (Salah)
                                    <br><small class="text-success">Jawaban Benar: {{ $answer->question->choices->where('is_correct', true)->first()->content ?? '-' }}</small>
                                @endif
                             @else
                                <p>{!! nl2br(e($answer->answer_text ?? '(Tidak Menjawab)')) !!}</p>
                             @endif
                        </div>
                        
                        <div class="form-group mt-3">
                            <label>Nilai / Skor</label>
                            <input type="number" step="0.1" name="scores[{{ $answer->id }}]" class="form-control col-md-2" value="{{ $answer->score }}">
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="card">
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Simpan Hasil Koreksi</button>
                    <a href="{{ route('admin.correction.show', $attempt->exam_session_id) }}" class="btn btn-default btn-block">Batal</a>
                </div>
            </div>
            
        </form>
    </div>
</div>
@endsection
