@extends('layouts.admin.app')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Edit Grup Soal</h3>
            </div>
            <form action="{{ route('admin.question_group.update', $questionGroup->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label>Mata Pelajaran</label>
                        <select name="subject_id" class="form-control" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ $questionGroup->subject_id == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Grup</label>
                        <input type="text" name="name" class="form-control" required value="{{ $questionGroup->name }}">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">Perbarui</button>
                    <a href="{{ route('admin.question_group.index') }}" class="btn btn-default">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
