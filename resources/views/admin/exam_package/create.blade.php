@extends('layouts.admin.app')

@section('title', 'Buat Paket Soal')
@section('page_title', 'Buat Paket Soal')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Form Paket Soal Baru</h3>
            </div>
            <form action="{{ route('admin.exam_package.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="form-group">
                        <label>Nama Paket</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Paket A (Mudah)">
                    </div>

                    <div class="form-group">
                        <label>Kode Paket (Opsional)</label>
                        <input type="text" name="code" class="form-control" value="{{ old('code') }}" placeholder="Contoh: PKT-A">
                    </div>

                    <div class="form-group">
                        <label>Mata Pelajaran</label>
                        <select name="subject_id" class="form-control" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }} ({{ $subject->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan & Lanjut ke Pilih Soal</button>
                    <a href="{{ route('admin.exam_package.index') }}" class="btn btn-default float-right">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
