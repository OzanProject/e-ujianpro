@extends('layouts.admin.app')
@section('title', 'Edit Jenis Ujian')
@section('page_title', 'Edit Jenis Ujian')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-header bg-white py-4 px-5 border-bottom-0">
                <h5 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-edit mr-2 text-warning"></i> Edit Jenis Ujian
                </h5>
            </div>
            
            <form action="{{ route('admin.exam_type.update', $examType->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body px-5 py-3">
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-lg shadow-sm border-0 mb-4">
                            <ul class="mb-0 pl-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark text-sm">Nama Jenis Ujian <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $examType->name) }}" required>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark text-sm">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $examType->description) }}</textarea>
                    </div>

                    <div class="custom-control custom-switch mb-4">
                        <input type="checkbox" class="custom-control-input" id="isActiveSwitch" name="is_active" value="1" {{ $examType->is_active ? 'checked' : '' }}>
                        <label class="custom-control-label font-weight-bold text-dark text-sm" for="isActiveSwitch">Status Aktif</label>
                    </div>

                </div>
                <div class="card-footer bg-white py-4 px-5 border-top d-flex justify-content-end">
                    <a href="{{ route('admin.exam_type.index') }}" class="btn btn-light border mr-3 font-weight-bold text-secondary rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-primary shadow-sm px-5 font-weight-bold rounded-pill">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
