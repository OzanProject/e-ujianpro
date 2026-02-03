@extends('layouts.admin.app')

@section('title', 'Tambah Soal Manual')
@section('page_title', 'Tambah Soal')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
             <div class="card-header bg-gradient-to-r from-blue-600 to-indigo-700 p-4 border-0">
                <h3 class="card-title text-white font-weight-bold mb-0">
                    <i class="fas fa-edit mr-2"></i> Form Buat Soal Manual
                </h3>
            </div>
            
            <form action="{{ route('admin.question.store') }}" method="POST">
                @csrf
                <div class="card-body bg-light p-4">
                    
                    @if(session('error'))
                         <div class="alert alert-danger border-0 shadow-sm rounded-lg mb-4">
                            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    {{-- Section 1: Konfigurasi Soal --}}
                    <div class="bg-white p-4 rounded-lg shadow-sm mb-4 border-left-primary">
                        <h6 class="font-weight-bold text-gray-700 mb-3 border-bottom pb-2">Konfigurasi Dasar</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-600">Mata Pelajaran <span class="text-danger">*</span></label>
                                    <select name="subject_id" class="form-control form-control-lg border-0 bg-light shadow-sm" required style="border-radius: 10px;">
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }} ({{ $subject->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-600">Tipe Soal <span class="text-danger">*</span></label>
                                    <select name="type" id="type-select" class="form-control form-control-lg border-0 bg-light shadow-sm" style="border-radius: 10px;">
                                        <option value="multiple_choice">Pilihan Ganda</option>
                                        <option value="essay">Essay / Uraian</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-600">Bacaan / Wacana (Opsional)</label>
                                    <select name="reading_text_id" class="form-control border-0 bg-light shadow-sm" style="border-radius: 8px;">
                                        <option value="">-- Tidak Ada --</option>
                                        @foreach($readingTexts as $text)
                                            <option value="{{ $text->id }}" {{ old('reading_text_id') == $text->id ? 'selected' : '' }}>
                                                {{ Str::limit($text->title, 40) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                     <label class="font-weight-bold text-gray-600">Kategori / Grup (Opsional)</label>
                                    <select name="question_group_id" class="form-control border-0 bg-light shadow-sm" style="border-radius: 8px;">
                                        <option value="">-- Tidak Ada --</option>
                                        @foreach($questionGroups as $group)
                                            <option value="{{ $group->id }}" {{ old('question_group_id') == $group->id ? 'selected' : '' }}>
                                                {{ $group->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Konten Soal --}}
                    <div class="bg-white p-4 rounded-lg shadow-sm mb-4 border-left-info">
                         <h6 class="font-weight-bold text-gray-700 mb-3 border-bottom pb-2">Konten Pertanyaan</h6>
                        <div class="form-group">
                            <textarea name="content" class="form-control" rows="5" required placeholder="Tulis pertanyaan di sini..." style="border-radius: 10px; border: 2px dashed #d1d3e2;">{{ old('content') }}</textarea>
                            <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle mr-1"></i> Anda bisa memasukkan teks pertanyaan.</small>
                        </div>
                    </div>

                    {{-- Section 3: Jawaban --}}
                    <div id="options-container" class="bg-white p-4 rounded-lg shadow-sm border-left-success">
                         <h6 class="font-weight-bold text-gray-700 mb-3 border-bottom pb-2">Pilihan Jawaban (Multiple Choice)</h6>
                        
                        <div class="alert alert-light border-left-warning shadow-sm">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i> Klik tombol <strong>Radio (O)</strong> di sebelah kiri untuk menentukan kunci jawaban yang benar.
                        </div>

                        <div id="options-list">
                            @foreach(['A', 'B', 'C', 'D', 'E'] as $key => $label)
                                <div class="input-group mb-3 option-item shadow-sm rounded-lg overflow-hidden">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-gray-100 border-0">
                                            <input type="radio" name="correct_option" value="{{ $key }}" {{ $key == 0 ? 'checked' : '' }} class="transform scale-150 cursor-pointer text-blue-600">
                                            <span class="ml-3 font-weight-bold text-gray-700">{{ $label }}</span>
                                        </div>
                                    </div>
                                    <input type="text" name="options[{{ $key }}][content]" class="form-control border-0 bg-white" placeholder="Tulis Opsi {{ $label }}..." style="height: 50px;" required>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
                <div class="card-footer bg-white border-top-0 d-flex justify-content-between p-4">
                     <a href="{{ route('admin.question.index') }}" class="btn btn-light text-gray-600 font-weight-bold rounded-pill px-4 shadow-sm">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary font-weight-bold rounded-pill px-5 shadow-lg transform hover:scale-105 transition-all duration-200">
                        <i class="fas fa-save mr-2"></i> Simpan Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $('#type-select').change(function() {
        if($(this).val() == 'essay') {
            $('#options-container').slideUp();
            $('#options-container input').prop('required', false);
        } else {
            $('#options-container').slideDown();
            $('#options-container input[type="text"]').prop('required', true);
        }
    });
</script>
@endpush
@endsection
