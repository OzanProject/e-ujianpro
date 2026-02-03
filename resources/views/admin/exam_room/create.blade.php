@extends('layouts.admin.app')

@section('title', 'Tambah Ruangan Ujian')
@section('page_title', 'Tambah Ruangan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-white">
                <h5 class="mb-0 font-weight-bold">Form Tambah Ruangan</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.exam_room.store') }}" method="POST">
                    @csrf
                    <div class="form-group mb-4">
                        <label for="name" class="font-weight-bold text-gray-700">Nama Ruangan <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control rounded-lg @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Lab Komputer 1" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.exam_room.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 font-weight-bold">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
