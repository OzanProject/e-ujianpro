@extends('layouts.admin.app')

@section('title', 'Konversi Skor')
@section('page_title', 'Konversi Skor per Grup Soal')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pilih Grup Soal</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.score-scales.index') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Mata Pelajaran</label>
                                <select name="subject_id" class="form-control select2" onchange="this.form.submit()">
                                    <option value="">-- Pilih Mapel --</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ $selectedSubjectId == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Grup Soal</label>
                                <select name="question_group_id" class="form-control select2" onchange="this.form.submit()">
                                    <option value="">-- Pilih Grup --</option>
                                    @foreach($questionGroups as $group)
                                        <option value="{{ $group->id }}" {{ $selectedGroupId == $group->id ? 'selected' : '' }}>
                                            {{ $group->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($selectedGroupId && $maxQuestions > 0)
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Tabel Konversi Skor: {{ $questionGroups->where('id', $selectedGroupId)->first()->name }}</h3>
            </div>
            <form method="POST" action="{{ route('admin.score-scales.store') }}">
                @csrf
                <input type="hidden" name="question_group_id" value="{{ $selectedGroupId }}">
                
                <div class="card-body table-responsive p-0" style="height: 500px;">
                    <table class="table table-head-fixed text-nowrap">
                        <thead>
                            <tr>
                                <th style="width: 150px">Jumlah Benar</th>
                                <th>Skor Konversi</th>
                                <th>Skor Standar (Ref)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 0; $i <= $maxQuestions; $i++)
                                @php
                                    $standardScore = ($i / $maxQuestions) * 100;
                                @endphp
                                <tr>
                                    <td class="align-middle text-center font-weight-bold">{{ $i }}</td>
                                    <td>
                                        <input type="number" 
                                               step="0.01" 
                                               name="scales[{{ $i }}]" 
                                               class="form-control" 
                                               value="{{ $scales[$i] ?? '' }}" 
                                               placeholder="{{ number_format($standardScore, 2) }}">
                                    </td>
                                    <td class="align-middle text-muted">
                                        {{ number_format($standardScore, 2) }}
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Konversi</button>
                    <small class="text-muted ml-3">* Kosongkan untuk menggunakan skor standar.</small>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-4">
        <div class="alert alert-info">
            <h5><i class="icon fas fa-info"></i> Panduan</h5>
            <p>Fitur ini memungkinkan Anda mengubah nilai akhir berdasarkan jumlah jawaban benar pada Grup Soal ini.</p>
            <p>Contoh: Untuk soal listening TOEFL, 30 benar mungkin bernilai 50, bukan 60.</p>
            <p>Jika kolom isian dikosongkan, sistem akan menggunakan perhitungan standar (Benar / Total * 100).</p>
        </div>
    </div>
</div>
@elseif($selectedGroupId && $maxQuestions == 0)
<div class="row">
    <div class="col-12">
        <div class="alert alert-warning">
            <i class="icon fas fa-exclamation-triangle"></i> Grup soal ini belum memiliki soal. Tambahkan soal terlebih dahulu.
        </div>
    </div>
</div>
@endif
@endsection
