@extends('layouts.admin.app')
@section('title', 'Edit Jadwal Ujian')
@section('page_title', 'Edit Jadwal Ujian')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-lg">
                <div class="card-header bg-white py-4 px-5 border-bottom-0">
                    <h5 class="card-title font-weight-bold text-dark mb-0">
                        <i class="fas fa-edit mr-2 text-warning"></i> Edit Jadwal Ujian
                    </h5>
                    <p class="text-muted text-sm mb-0 mt-1">Perbarui informasi jadwal ujian dan pengaturan waktu.</p>
                </div>

                <form action="{{ route('admin.exam_session.update', $examSession->id) }}" method="POST">
                    @csrf
                    @method('PUT')
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
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="text-secondary font-weight-bold text-uppercase text-xs mb-0"
                                        style="letter-spacing: 1px;">Informasi Dasar</h6>
                                    <div class="custom-control custom-control-right custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="isActiveSwitch"
                                            name="is_active" value="1" {{ $examSession->is_active ? 'checked' : '' }}>
                                        <label class="custom-control-label font-weight-bold text-dark text-sm"
                                            for="isActiveSwitch">Status Aktif</label>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark text-sm">Jenis Ujian <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i
                                                    class="fas fa-tag text-secondary"></i></span>
                                        </div>
                                        <select name="exam_type_id" class="form-control border-left-0 pl-2 custom-select"
                                            required>
                                            <option value="">-- Pilih Jenis Ujian --</option>
                                            @foreach($examTypes as $type)
                                                <option value="{{ $type->id }}" {{ old('exam_type_id', $examSession->exam_type_id) == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label class="font-weight-bold text-dark text-sm">Mata Pelajaran</label>
                                            <select name="subject_id"
                                                class="form-control custom-select bg-gray-100 text-muted" disabled>
                                                @foreach($subjects as $subject)
                                                    <option value="{{ $subject->id }}" {{ old('subject_id', $examSession->subject_id) == $subject->id ? 'selected' : '' }}>
                                                        {{ $subject->name }} ({{ $subject->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="subject_id" value="{{ $examSession->subject_id }}">
                                            <small class="text-muted"><i class="fas fa-lock mr-1"></i> Mapel tidak dapat
                                                diubah</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label class="font-weight-bold text-dark text-sm">Paket Soal</label>
                                            <select name="exam_package_id" class="form-control custom-select">
                                                <option value="">-- Acak / Semua --</option>
                                                @foreach($packages as $package)
                                                    <option value="{{ $package->id }}" {{ old('exam_package_id', $examSession->exam_package_id) == $package->id ? 'selected' : '' }}>
                                                        {{ $package->title ?? $package->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold text-dark text-sm">Deskripsi / Instruksi</label>
                                    <textarea name="description" class="form-control"
                                        rows="4">{{ old('description', $examSession->description) }}</textarea>
                                </div>
                            </div>

                            <!-- Right Column: Settings -->
                            <div class="col-md-5 pl-md-5">
                                <h6 class="text-secondary font-weight-bold text-uppercase text-xs mb-4"
                                    style="letter-spacing: 1px;">Pengaturan Waktu</h6>

                                <div class="bg-light rounded-lg p-4 border border-light">
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-dark text-sm">Waktu Mulai <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0"><i
                                                        class="far fa-calendar-alt text-success"></i></span>
                                            </div>
                                            <input type="datetime-local" name="start_time"
                                                class="form-control border-left-0"
                                                value="{{ old('start_time', $examSession->start_time->format('Y-m-d\TH:i')) }}"
                                                required>
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-dark text-sm">Waktu Selesai <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0"><i
                                                        class="far fa-calendar-times text-danger"></i></span>
                                            </div>
                                            <input type="datetime-local" name="end_time" class="form-control border-left-0"
                                                value="{{ old('end_time', $examSession->end_time->format('Y-m-d\TH:i')) }}"
                                                required>
                                        </div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold text-dark text-sm">Durasi (Menit) <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0"><i
                                                        class="fas fa-hourglass-half text-warning"></i></span>
                                            </div>
                                            <input type="number" name="duration" class="form-control border-left-0"
                                                value="{{ old('duration', $examSession->duration) }}" min="1" required>
                                        </div>
                                    </div>

                                    <div class="form-group mb-0 mt-4 border-top pt-3">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="showScoreSwitch"
                                                name="show_score" value="1" {{ $examSession->show_score ? 'checked' : '' }}>
                                            <label class="custom-control-label font-weight-bold text-dark text-sm"
                                                for="showScoreSwitch">Tampilkan Nilai ke Siswa</label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Jika dinonaktifkan, siswa tidak akan melihat
                                            nilai setelah ujian selesai.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer bg-white py-4 px-5 border-top d-flex justify-content-end">
                        <a href="{{ route('admin.exam_session.index') }}"
                            class="btn btn-light border mr-3 font-weight-bold text-secondary rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary shadow-sm px-5 font-weight-bold rounded-pill">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection