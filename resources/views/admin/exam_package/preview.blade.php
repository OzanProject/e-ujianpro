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
                    <div class="mb-4 p-3 border rounded bg-white shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge badge-info">{{ strtoupper(str_replace('_', ' ', $question->type)) }}</span>
                            <span class="badge badge-secondary">{{ strtoupper($question->difficulty) }}</span>
                        </div>
                        
                        <div class="font-weight-bold mb-3" style="font-size: 1.1rem;">
                            {{ $index + 1 }}. {!! $question->content !!}
                        </div>
                        
                        {{-- PG Tunggal --}}
                        @if($question->type == 'multiple_choice')
                            <div class="ml-4">
                                @foreach($question->options as $key => $option)
                                    <div class="mb-2 d-flex align-items-center">
                                        <span class="badge {{ $option->is_correct ? 'badge-success' : 'badge-light border' }} mr-2" style="width: 28px; height: 28px; line-height: 20px; text-align: center; border-radius: 50%;">
                                            {{ chr(65 + $key) }}
                                        </span>
                                        <div class="flex-grow-1 {{ $option->is_correct ? 'text-success font-weight-bold' : '' }}">
                                            {!! $option->content !!}
                                        </div>
                                        @if($option->is_correct)
                                            <i class="fas fa-check-circle text-success ml-2"></i>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                        {{-- PG Kompleks --}}
                        @elseif($question->type == 'multiple_choice_complex')
                            <div class="ml-4">
                                <small class="text-muted d-block mb-2"><i>(Pilihan Ganda Kompleks - Bisa lebih dari satu jawaban benar)</i></small>
                                @foreach($question->options as $key => $option)
                                    <div class="mb-2 d-flex align-items-center p-2 rounded {{ $option->is_correct ? 'bg-success-light border-success' : 'bg-light border' }}" style="{{ $option->is_correct ? 'background-color: #e8f5e9; border: 1px solid #c8e6c9;' : '' }}">
                                        <div class="custom-control custom-checkbox mr-2">
                                            <input type="checkbox" class="custom-control-input" {{ $option->is_correct ? 'checked' : '' }} disabled>
                                            <label class="custom-control-label"></label>
                                        </div>
                                        <div class="flex-grow-1 {{ $option->is_correct ? 'text-success font-weight-bold' : '' }}">
                                            {!! $option->content !!}
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        {{-- Benar / Salah (Boolean Grid) --}}
                        @elseif($question->type == 'boolean_grid')
                            <div class="ml-4">
                                <table class="table table-sm table-bordered mt-2" style="max-width: 500px;">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Pernyataan</th>
                                            <th width="80" class="text-center">Jawaban</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($question->options as $option)
                                            <tr>
                                                <td>{!! $option->content !!}</td>
                                                <td class="text-center">
                                                    @if($option->is_correct)
                                                        <span class="badge badge-success px-3">BENAR</span>
                                                    @else
                                                        <span class="badge badge-danger px-3">SALAH</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        {{-- Esai / Uraian --}}
                        @else
                            <div class="ml-4">
                                <div class="p-3 border rounded bg-light" style="min-height: 100px; border-style: dashed !important;">
                                    <small class="text-muted">Area Jawaban Siswa (Uraian/Esai)</small>
                                </div>
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
