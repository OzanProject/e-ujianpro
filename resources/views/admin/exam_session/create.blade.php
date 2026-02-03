@extends('layouts.admin.app')

@section('title', 'Buat Jadwal Ujian')
@section('page_title', 'Buat Jadwal Ujian')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-header bg-white py-4 px-5 border-bottom-0">
                <h5 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-plus-circle mr-2 text-primary"></i> Buat Jadwal Ujian Baru
                </h5>
                <p class="text-muted text-sm mb-0 mt-1">Isi form di bawah untuk membuat sesi ujian baru.</p>
            </div>
            
            <form action="{{ route('admin.exam_session.store') }}" method="POST">
                @csrf
                <div class="card-body px-5 py-3">
                    @if(session('error'))
                        <div class="alert alert-danger rounded-lg shadow-sm border-0 mb-4">{{ session('error') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-lg shadow-sm border-0 mb-4">
                            <ul class="mb-0 pl-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Left Column: Details -->
                        <div class="col-md-7 pr-md-5 border-right border-light">
                             <h6 class="text-secondary font-weight-bold text-uppercase text-xs mb-4" style="letter-spacing: 1px;">Informasi Dasar</h6>
                             
                             <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark text-sm">Jenis Ujian <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0"><i class="fas fa-tag text-secondary"></i></span>
                                    </div>
                                    <select name="exam_type_id" class="form-control border-left-0 pl-2 custom-select" required>
                                        <option value="">-- Pilih Jenis Ujian --</option>
                                        @foreach($examTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('exam_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @if($examTypes->isEmpty())
                                    <small class="text-danger mt-1 d-block">
                                        Belum ada data Jenis Ujian. <a href="{{ route('admin.exam_type.create') }}" class="text-danger font-weight-bold" target="_blank">Buat sekarang</a>
                                    </small>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                     <div class="form-group mb-4">
                                        <label class="font-weight-bold text-dark text-sm">Mata Pelajaran <span class="text-danger">*</span></label>
                                        <select name="subject_id" id="subject_id" class="form-control custom-select" required>
                                            <option value="">-- Pilih Mapel --</option>
                                            @foreach($subjects as $subject)
                                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                                    {{ $subject->name }} ({{ $subject->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-dark text-sm">Paket Soal</label>
                                        <select name="exam_package_id" id="exam_package_id" class="form-control custom-select">
                                            <option value="">-- Acak / Semua --</option>
                                            @foreach($packages as $package)
                                                <option value="{{ $package->id }}" data-subject="{{ $package->subject_id }}" class="package-option">
                                                    {{ $package->title ?? $package->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="font-weight-bold text-dark text-sm">Deskripsi / Instruksi</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Tuliskan instruksi untuk siswa...">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <!-- Right Column: Settings -->
                        <div class="col-md-5 pl-md-5">
                            <h6 class="text-secondary font-weight-bold text-uppercase text-xs mb-4" style="letter-spacing: 1px;">Pengaturan Waktu</h6>
                            
                            <div class="bg-light rounded-lg p-4 border border-light">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark text-sm">Waktu Mulai <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                         <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="far fa-calendar-alt text-success"></i></span>
                                        </div>
                                        <input type="datetime-local" name="start_time" class="form-control border-left-0" value="{{ old('start_time') }}" required>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark text-sm">Waktu Selesai <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="far fa-calendar-times text-danger"></i></span>
                                        </div>
                                        <input type="datetime-local" name="end_time" class="form-control border-left-0" value="{{ old('end_time') }}" required>
                                    </div>
                                </div>

                                <div class="form-group mb-0">
                                    <label class="font-weight-bold text-dark text-sm">Durasi (Menit) <span class="text-danger">*</span></label>
                                     <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-hourglass-half text-warning"></i></span>
                                        </div>
                                        <input type="number" name="duration" class="form-control border-left-0" value="{{ old('duration', 60) }}" min="1" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer bg-white py-4 px-5 border-top d-flex justify-content-end">
                    <a href="{{ route('admin.exam_session.index') }}" class="btn btn-light border mr-3 font-weight-bold text-secondary rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-primary shadow-sm px-5 font-weight-bold rounded-pill">
                        <i class="fas fa-save mr-1"></i> Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#subject_id').change(function() {
            var subjectId = $(this).val();
            $('#exam_package_id').val(''); // Reset selection
            
            if(subjectId) {
                $('.package-option').hide();
                $('.package-option[data-subject="' + subjectId + '"]').show();
            } else {
                $('.package-option').hide();
            }
        });
        
        // Trigger on load if old value exists
        $('#subject_id').trigger('change');
    });
</script>
@endpush
