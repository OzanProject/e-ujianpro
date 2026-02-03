@extends('layouts.admin.app')

@section('title', 'Edit Paket Soal')
@section('page_title', 'Edit Paket Soal')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Edit Info Paket Soal</h3>
            </div>
            <form action="{{ route('admin.exam_package.update', $examPackage->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="form-group">
                        <label>Nama Paket</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $examPackage->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label>Kode Paket</label>
                        <input type="text" name="code" class="form-control" value="{{ old('code', $examPackage->code) }}">
                    </div>

                    <div class="form-group">
                        <label>Mata Pelajaran</label>
                        <select name="subject_id" class="form-control" required>
                             @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $examPackage->subject_id) == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }} ({{ $subject->code }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-danger">* Mengubah mata pelajaran akan menghapus relasi soal yang ada.</small>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">Update Info Paket</button>
                    <a href="{{ route('admin.exam_package.index') }}" class="btn btn-default float-right">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
