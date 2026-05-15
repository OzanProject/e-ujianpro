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
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header bg-white">
                <h3 class="card-title font-weight-bold">Tabel Konversi Skor: {{ $questionGroups->where('id', $selectedGroupId)->first()->name }}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-default btn-sm" onclick="autoFillStandard()">
                        <i class="fas fa-magic mr-1"></i> Auto Fill Skor Standar
                    </button>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.score-scales.store') }}" id="scaleForm">
                @csrf
                <input type="hidden" name="question_group_id" value="{{ $selectedGroupId }}">
                
                <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th class="text-center py-3" style="width: 150px">Jumlah Benar</th>
                                <th class="py-3">Skor Konversi (Custom)</th>
                                <th class="py-3 text-muted">Skor Standar (Referensi)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 0; $i <= $maxQuestions; $i++)
                                @php
                                    $standardScore = ($i / $maxQuestions) * 100;
                                @endphp
                                <tr>
                                    <td class="align-middle text-center font-weight-bold bg-light">{{ $i }}</td>
                                    <td class="py-2">
                                        <input type="number" 
                                               step="0.01" 
                                               name="scales[{{ $i }}]" 
                                               class="form-control form-control-sm scale-input" 
                                               value="{{ isset($scales[$i]) ? $scales[$i] : '' }}" 
                                               data-standard="{{ number_format($standardScore, 2, '.', '') }}"
                                               placeholder="Gunakan standar">
                                    </td>
                                    <td class="align-middle text-muted small">
                                        {{ number_format($standardScore, 2) }}
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save mr-1"></i> Simpan Konversi</button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="clearAll()">
                        <i class="fas fa-eraser mr-1"></i> Kosongkan Semua
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-info card-outline shadow-sm">
            <div class="card-header bg-white">
                <h3 class="card-title font-weight-bold">Informasi Konversi</h3>
            </div>
            <div class="card-body">
                <p class="text-sm">Fitur ini digunakan jika Anda ingin menerapkan sistem penilaian non-linear (seperti TOEFL atau IELTS).</p>
                <div class="callout callout-info">
                    <h6 class="font-weight-bold">Aturan:</h6>
                    <ul class="pl-3 mb-0 text-sm">
                        <li>Jika kolom **Skor Konversi** diisi, maka nilai itulah yang akan digunakan.</li>
                        <li>Jika dikosongkan, sistem akan otomatis menghitung menggunakan rumus standar: `(Benar / Total) * 100`.</li>
                        <li>Anda bisa menekan tombol **Auto Fill** untuk mengisi semua kolom dengan nilai standar lalu mengubah angka tertentu saja.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function autoFillStandard() {
    if(confirm('Isi semua kolom dengan skor standar?')) {
        $('.scale-input').each(function() {
            $(this).val($(this).data('standard'));
        });
    }
}

function clearAll() {
    if(confirm('Kosongkan semua isian konversi? (Akan kembali ke skor standar)')) {
        $('.scale-input').val('');
    }
}
</script>
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
