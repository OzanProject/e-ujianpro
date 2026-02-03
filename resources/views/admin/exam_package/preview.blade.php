@extends('layouts.admin.app')

@section('title', 'Preview Paket Soal')
@section('page_title', 'Preview Paket: ' . $examPackage->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tampilan Soal (Total: {{ $examPackage->questions->count() }})</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.exam_package.show', $examPackage->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Edit Soal
                    </a>
                    <a href="{{ route('admin.exam_package.index') }}" class="btn btn-default btn-sm">Kembali</a>
                </div>
            </div>
            <div class="card-body">
                @forelse($examPackage->questions as $index => $question)
                    <div class="mb-4 p-3 border rounded bg-light">
                        <p class="font-weight-bold">{{ $index + 1 }}. {!! $question->content !!}</p>
                        
                        @if($question->type == 'multiple_choice')
                            <ul style="list-style-type: none; padding-left: 0;">
                                @foreach($question->options as $key => $option)
                                    <li class="mb-1">
                                        <span class="badge {{ $option->is_correct ? 'badge-success' : 'badge-secondary' }}" style="width: 25px; text-align: center; display: inline-block;">
                                            {{ chr(65 + $key) }}
                                        </span>
                                        {{ $option->content }}
                                        @if($option->is_correct)
                                            <i class="fas fa-check text-success ml-2"></i>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="form-group">
                                <textarea class="form-control" rows="3" disabled>Area Jawaban Siswa (Essay)</textarea>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="alert alert-warning">Belum ada soal dalam paket ini.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
