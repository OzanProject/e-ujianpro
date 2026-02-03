@extends('layouts.admin.app')

@section('title', 'Edit Soal')
@section('page_title', 'Edit Soal')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Edit Soal</h3>
            </div>
            <form action="{{ route('admin.question.update', $question->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="form-group">
                        <label>Mata Pelajaran</label>
                        <select name="subject_id" class="form-control" required>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ $question->subject_id == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }} ({{ $subject->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Bacaan / Wacana (Opsional)</label>
                        <select name="reading_text_id" class="form-control">
                            <option value="">-- Tanpa Bacaan --</option>
                            @foreach($readingTexts as $text)
                                <option value="{{ $text->id }}" {{ $question->reading_text_id == $text->id ? 'selected' : '' }}>
                                    {{ Str::limit($text->title, 50) }} ({{ $text->subject->name }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih wacana jika soal ini berhubungan dengan teks bacaan tertentu.</small>
                    </div>

                    <div class="form-group">
                        <label>Grup Soal (Opsional)</label>
                        <select name="question_group_id" class="form-control">
                            <option value="">-- Tanpa Grup --</option>
                            @foreach($questionGroups as $group)
                                <option value="{{ $group->id }}" {{ $question->question_group_id == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }} ({{ $group->subject->name }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Kategorikan soal ini ke dalam grup tertentu (e.g. Bab 1, Mudah, Sulit).</small>
                    </div>

                    <div class="form-group">
                        <label>Konten Soal (Pertanyaan)</label>
                        <textarea name="content" class="form-control" rows="3" required>{{ $question->content }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Tipe Soal</label>
                        <select name="type" id="type-select" class="form-control">
                            <option value="multiple_choice" {{ $question->type == 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                            <option value="essay" {{ $question->type == 'essay' ? 'selected' : '' }}>Essay</option>
                        </select>
                    </div>

                    <div id="options-container" style="{{ $question->type == 'essay' ? 'display:none;' : '' }}">
                        <label>Pilihan Jawaban</label>
                        <div id="options-list">
                            @foreach($question->options as $index => $option)
                            <div class="input-group mb-3 option-item">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <input type="radio" name="correct_option" value="{{ $index }}" {{ $option->is_correct ? 'checked' : '' }}>
                                        <span class="ml-2">{{ chr(65 + $index) }}</span>
                                    </div>
                                </div>
                                <input type="text" name="options[{{ $index }}][content]" class="form-control" value="{{ $option->content }}" required>
                            </div>
                            @endforeach
                            
                            {{-- Fallback if no options exist (e.g. switching from essay) --}}
                            @if($question->options->isEmpty())
                                 @for ($i = 0; $i < 4; $i++)
                                    <div class="input-group mb-3 option-item">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <input type="radio" name="correct_option" value="{{ $i }}" {{ $i==0 ? 'checked' : '' }}>
                                                <span class="ml-2">{{ chr(65 + $i) }}</span>
                                            </div>
                                        </div>
                                        <input type="text" name="options[{{ $i }}][content]" class="form-control" placeholder="Pilihan {{ chr(65 + $i) }}" {{ $question->type == 'multiple_choice' ? 'required' : '' }}>
                                    </div>
                                 @endfor
                            @endif
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">Update Soal</button>
                    <a href="{{ route('admin.question.index') }}" class="btn btn-default float-right">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $('#type-select').change(function() {
        if($(this).val() == 'essay') {
            $('#options-container').hide();
            $('#options-container input').prop('required', false);
        } else {
            $('#options-container').show();
            // Only require inputs if visible
            $('#options-container input[type="text"]').prop('required', true);
        }
    });
</script>
@endpush
@endsection
