@extends('layouts.admin.app')

@section('title', 'Edit Pengawas')
@section('page_title', 'Edit Pengawas')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="card-header bg-warning p-4 border-0">
                <h3 class="card-title text-white font-weight-bold mb-0">
                    <i class="fas fa-user-edit mr-2"></i> Edit Data Pengawas
                </h3>
            </div>
            <form action="{{ route('admin.proctor.update', $proctor->id) }}" method="POST">
                @csrf
                @method('PUT')
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

                    <div class="bg-white p-4 rounded-lg shadow-sm border-left-warning">
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control form-control-lg border-0 bg-light shadow-sm" value="{{ old('name', $proctor->name) }}" required>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-lg border-0 bg-light shadow-sm" value="{{ old('email', $proctor->email) }}" required>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700">Password (Opsional)</label>
                            <input type="password" name="password" class="form-control form-control-lg border-0 bg-light shadow-sm" placeholder="Kosongkan jika tidak ingin mengubah password">
                        </div>

                         <div class="form-group">
                            <label class="font-weight-bold text-gray-700">Status Akun</label>
                            <select name="status" class="form-control form-control-lg border-0 bg-light shadow-sm">
                                <option value="active" {{ $proctor->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="suspended" {{ $proctor->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700">Tugas Ruangan (Opsional)</label>
                            <select name="exam_room_id" class="form-control form-control-lg border-0 bg-light shadow-sm">
                                <option value="">-- Tanpa Ruangan (Bisa Monitoring Semua) --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ old('exam_room_id', $proctor->exam_room_id) == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
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
                    <button type="submit" class="btn btn-warning font-weight-bold text-white rounded-pill px-5 shadow-lg">
                        <i class="fas fa-save mr-2"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
