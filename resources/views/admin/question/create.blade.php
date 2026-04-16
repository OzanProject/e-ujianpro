@extends('layouts.admin.app')
@php
    $user = auth()->user();
    $baseRoute = $user->role === 'pengajar' ? 'pengajar.question' : 'admin.question';
@endphp
@section('title', 'Tambah Soal Premium')
@section('page_title', 'Buat Soal Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-11">
        <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="card-header bg-gradient-to-r from-blue-700 to-indigo-800 p-4 border-0">
                <div class="d-flex justify-content-between align-items-center text-white">
                    <div>
                        <h4 class="font-weight-bold mb-0"><i class="fas fa-plus-circle mr-2"></i> Form Input Soal</h4>
                        <p class="text-xs mb-0 opacity-75">Gunakan editor di bawah untuk memasukkan soal dengan gambar dan format teks kaya.</p>
                    </div>
                    <a href="{{ route($baseRoute . '.index') }}" class="btn btn-outline-light btn-sm rounded-pill px-4">
                        <i class="fas fa-list mr-2"></i> Lihat Bank Soal
                    </a>
                </div>
            </div>
            
            <form action="{{ route($baseRoute . '.store') }}" method="POST" id="questionForm">
                @csrf
                <input type="hidden" name="submit_type" id="submit_type" value="save">

                <div class="card-body bg-light-gray p-4">
                    @if(session('success'))
                        <div class="alert alert-success border-0 shadow-sm rounded-lg mb-4">
                            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger border-0 shadow-sm rounded-lg mb-4">
                            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <div class="row">
                        {{-- Sidebar Konfigurasi --}}
                        <div class="col-lg-4">
                            <div class="bg-white p-4 rounded-lg shadow-sm mb-4 border-left-primary h-100">
                                <h6 class="font-weight-bold text-dark mb-4 border-bottom pb-2">Konfigurasi Soal</h6>
                                
                                <div class="form-group mb-4">
                                    <label class="text-xs font-weight-bold text-uppercase text-muted">Mata Pelajaran <span class="text-danger">*</span></label>
                                    <select name="subject_id" class="form-control form-control-lg border-0 bg-light-gray shadow-sm" required style="border-radius: 10px;">
                                        <option value="">-- Pilih Mapel --</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ (old('subject_id') ?? request('subject_id')) == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-xs font-weight-bold text-uppercase text-muted">Tipe Soal <span class="text-danger">*</span></label>
                                    <select name="type" id="type-select" class="form-control form-control-lg border-0 bg-light-gray shadow-sm" style="border-radius: 10px;">
                                        <option value="multiple_choice" {{ old('type') == 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                                        <option value="essay" {{ old('type') == 'essay' ? 'selected' : '' }}>Essay / Uraian</option>
                                    </select>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-xs font-weight-bold text-uppercase text-muted">Tingkat Kesulitan <span class="text-danger">*</span></label>
                                    <div class="row mt-2 px-2">
                                        <div class="col-4 px-1">
                                            <input type="radio" id="diff-easy" name="difficulty" value="easy" class="d-none radio-diff" {{ (old('difficulty') ?? request('difficulty') ?? 'easy') == 'easy' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-success btn-block rounded-lg shadow-sm font-weight-bold p-2 text-xs mb-0 label-easy" for="diff-easy">MUDAH</label>
                                        </div>
                                        <div class="col-4 px-1">
                                            <input type="radio" id="diff-medium" name="difficulty" value="medium" class="d-none radio-diff" {{ (old('difficulty') ?? request('difficulty')) == 'medium' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-warning btn-block rounded-lg shadow-sm font-weight-bold p-2 text-xs mb-0 label-warning" for="diff-medium">SEDANG</label>
                                        </div>
                                        <div class="col-4 px-1">
                                            <input type="radio" id="diff-hard" name="difficulty" value="hard" class="d-none radio-diff" {{ (old('difficulty') ?? request('difficulty')) == 'hard' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger btn-block rounded-lg shadow-sm font-weight-bold p-2 text-xs mb-0 label-danger" for="diff-hard">SULIT</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-0">
                                    <label class="text-xs font-weight-bold text-uppercase text-muted">Tagging (Pisahkan koma)</label>
                                    <textarea name="tags" class="form-control border-0 bg-light-gray shadow-sm" rows="2" placeholder="HOTS, UN, Olimpiade..." style="border-radius: 10px;">{{ old('tags') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Konten Utama --}}
                        <div class="col-lg-8">
                            <div class="bg-white p-4 rounded-lg shadow-sm border-left-info h-100">
                                <h6 class="font-weight-bold text-dark mb-4 border-bottom pb-2">Konten Pertanyaan</h6>
                                
                                <div class="form-group mb-4">
                                    <label class="text-xs font-weight-bold text-uppercase text-info"><i class="fas fa-info-circle mr-1"></i> Petunjuk / Bacaan (Opsional)</label>
                                    <textarea name="reading_text_content" class="form-control tinymce-editor-small" rows="3" placeholder="Masukkan teks atau sertakan gambar petunjuk di sini...">{{ old('reading_text_content') }}</textarea>
                                    <small class="text-muted mt-1 d-block">Akan ditampilkan di atas soal sebagai instruksi atau bacaan teks/gambar bersama.</small>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="text-xs font-weight-bold text-uppercase text-muted">Soal / Pertanyaan Inti <span class="text-danger">*</span></label>
                                    <textarea name="content" class="form-control tinymce-editor" rows="10">{{ old('content') }}</textarea>
                                </div>
                                
                                <div id="options-container">
                                    <h6 class="font-weight-bold text-dark mb-3">Pilihan Jawaban</h6>
                                    @foreach(['A', 'B', 'C', 'D', 'E'] as $key => $label)
                                        <div class="option-row mb-3 p-3 rounded-lg border bg-light-gray position-relative shadow-xs transition-all" id="row-opt-{{ $key }}">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="custom-control custom-radio mr-3">
                                                    <input type="radio" name="correct_option" id="opt-{{ $key }}" value="{{ $key }}" class="custom-control-input radio-correct" {{ $key == 0 ? 'checked' : '' }}>
                                                    <label class="custom-control-label font-weight-bold text-primary" for="opt-{{ $key }}">JADIKAN KUNCI JAWABAN {{ $label }}</label>
                                                </div>
                                            </div>
                                            <textarea name="options[{{ $key }}][content]" class="form-control tinymce-editor-small" rows="2">{{ old("options.$key.content") }}</textarea>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white border-top-0 d-flex flex-wrap justify-content-between p-4 align-items-center">
                    <div class="mb-3 mb-md-0 d-none d-lg-block">
                        <small class="text-muted"><i class="fas fa-lightbulb text-warning mr-1"></i> Tips: Anda bisa paste gambar langsung dari Word atau internet ke editor soal.</small>
                    </div>
                    <div class="d-flex flex-wrap">
                        <button type="button" onclick="submitForm('save_and_next')" class="btn btn-outline-primary font-weight-bold rounded-pill px-4 mr-2 mb-2 shadow-sm transition hover:scale-105">
                            <i class="fas fa-plus-circle mr-2"></i> Simpan & Tambah Lagi
                        </button>
                        <button type="button" onclick="submitForm('save')" class="btn btn-primary font-weight-bold rounded-pill px-5 mb-2 shadow-lg transform active:scale-95 transition-all">
                            <i class="fas fa-save mr-2"></i> Simpan Selesai
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .bg-light-gray { background-color: #f8f9fc; }
    .shadow-xs { box-shadow: 0 .125rem .25rem rgba(0,0,0,.04); }
    .cursor-pointer { cursor: pointer; }
    .transition-all { transition: all 0.2s ease; }
    
    /* Config Radio Buttons Style */
    .radio-diff:checked + .label-easy { background-color: #28a745 !important; border-color: #28a745 !important; color: #fff !important; }
    .radio-diff:checked + .label-warning { background-color: #ffc107 !important; border-color: #ffc107 !important; color: #111 !important; }
    .radio-diff:checked + .label-danger { background-color: #dc3545 !important; border-color: #dc3545 !important; color: #fff !important; }
    
    /* Correct Option Row Highlight */
    .option-row { border-width: 2px !important; }
    .option-row.is-correct { border-color: #28a745 !important; background-color: #f0fdf4 !important; }
    .option-row.is-correct .custom-control-label { color: #28a745 !important; }
</style>
@endpush
@endsection

@push('scripts')
@php
    $tinyApiKey = \App\Models\Setting::getValue('tinymce_api_key', 'no-api-key');
    if(empty(trim($tinyApiKey))) $tinyApiKey = 'no-api-key';
@endphp
<script src="https://cdn.tiny.cloud/1/{{ $tinyApiKey }}/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    function initTinyMCE(selector, height = 300) {
        tinymce.init({
            selector: selector,
            height: height,
            menubar: false,
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
            toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | image table | removeformat help',
            image_advtab: true,
            images_upload_url: "{{ route('admin.question.upload_image') }}",
            automatic_uploads: true,
            images_upload_credentials: true,
            relative_urls: false,
            remove_script_host: false,
            convert_urls: true,
            content_style: 'body { font-family:Inter,Arial,sans-serif; font-size:14px }'
        });
    }

    $(function() {
        initTinyMCE('.tinymce-editor', 400);
        initTinyMCE('.tinymce-editor-small', 180);

        $('#type-select').change(function() {
            if($(this).val() == 'essay') {
                $('#options-container').slideUp();
            } else {
                $('#options-container').slideDown();
            }
        });

        if($('#type-select').val() == 'essay') {
            $('#options-container').hide();
        }

        // Correct Option Highlight Logic
        function updateCorrectOptionHighlight() {
            $('.option-row').removeClass('is-correct');
            $('.radio-correct:checked').closest('.option-row').addClass('is-correct');
        }
        
        $('.radio-correct').on('change', updateCorrectOptionHighlight);
        updateCorrectOptionHighlight(); // Initialize setup
    });

    function submitForm(type) {
        // Trigger TinyMCE save to update textareas
        tinymce.triggerSave();
        $('#submit_type').val(type);
        $('#questionForm').submit();
    }
</script>
@endpush
