@extends('layouts.admin.app')

@section('title', 'Tambah Pengawas')
@section('page_title', 'Tambah Pengawas')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="card-header bg-primary p-4 border-0">
                <h3 class="card-title text-white font-weight-bold mb-0">
                    <i class="fas fa-user-plus mr-2"></i> Form Data Pengawas
                </h3>
            </div>
            <form action="{{ route('admin.proctor.store') }}" method="POST">
                @csrf
                <div class="card-body bg-light p-4">
                    @if($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-lg mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="bg-white p-4 rounded-lg shadow-sm border-left-primary">
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control form-control-lg border-0 bg-light shadow-sm" placeholder="Nama Pengawas" value="{{ old('name') }}" required>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700">Email Address (Username)</label>
                            <input type="email" name="email" class="form-control form-control-lg border-0 bg-light shadow-sm" placeholder="email@sekolah.sch.id" value="{{ old('email') }}" required>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700">Password</label>
                            <input type="password" name="password" class="form-control form-control-lg border-0 bg-light shadow-sm" placeholder="Minimal 8 karakter" required>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700">Tugas Ruangan (Opsional)</label>
                            <select name="exam_room_id" class="form-control form-control-lg border-0 bg-light shadow-sm">
                                <option value="">-- Tanpa Ruangan (Bisa Monitoring Semua) --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ old('exam_room_id') == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Jika dipilih, Pengawas hanya bisa memantau siswa di ruangan ini.</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 d-flex justify-content-between p-4">
                    <a href="{{ route('admin.proctor.index') }}" class="btn btn-light text-gray-600 font-weight-bold rounded-pill px-4 shadow-sm">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary font-weight-bold rounded-pill px-5 shadow-lg">
                        <i class="fas fa-save mr-2"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
