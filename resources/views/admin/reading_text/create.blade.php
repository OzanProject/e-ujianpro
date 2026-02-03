@extends('layouts.admin.app')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Tambah Bacaan Baru</h3>
            </div>
            <form action="{{ route('admin.reading_text.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Mata Pelajaran</label>
                        <select name="subject_id" class="form-control" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Judul Bacaan</label>
                        <input type="text" name="title" class="form-control" required placeholder="Contoh: Sejarah Kemerdekaan">
                    </div>
                    <div class="form-group">
                        <label>Isi Bacaan</label>
                        <textarea name="content" class="form-control" rows="10" required></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('admin.reading_text.index') }}" class="btn btn-default">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
