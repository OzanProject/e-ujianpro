@extends('layouts.admin.app')

@section('title', 'Kelola Soal Paket')
@section('page_title', 'Kelola Soal: ' . $examPackage->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pilih Soal untuk Paket Ini</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.exam_package.index') }}" class="btn btn-default btn-sm">Kembali</a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card card-outline card-info collapsed-card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-random"></i> Generate Soal Otomatis</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.exam_package.random', $examPackage->id) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label>Jumlah Soal yang Diambil</label>
                                        <input type="number" name="count" class="form-control" placeholder="Contoh: 50" min="1" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Tipe Soal</label>
                                        <select name="type" class="form-control">
                                            <option value="all">Semua Tipe (Campur)</option>
                                            <option value="multiple_choice">Hanya Pilihan Ganda</option>
                                            <option value="essay">Hanya Essay</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-info btn-block" onclick="return confirm('Peringatan: Ini akan menimpa/mengganti semua soal yang sudah ada di paket ini. Lanjutkan?')">
                                        <i class="fas fa-magic"></i> Generate Sekarang
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                         <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Soal Terpilih</span>
                                <span class="info-box-number">{{ $examPackage->questions->count() }} Soal</span>
                            </div>
                        </div>
                    </div>
                </div>

                <p>Mata Pelajaran: <strong>{{ $examPackage->subject->name }}</strong></p>

                <form action="{{ route('admin.exam_package.show', $examPackage->id) }}" method="GET" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Cari isi soal..." value="{{ request('q') }}">
                        <div class="input-group-append">
                            <button class="btn btn-default" type="submit"><i class="fas fa-search"></i> Cari</button>
                        </div>
                    </div>
                </form>

                <form action="{{ route('admin.exam_package.assign', $examPackage->id) }}" method="POST">
                    @csrf
                    
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 50px" class="text-center">
                                        <input type="checkbox" id="check-all">
                                    </th>
                                    <th>Konten Soal</th>
                                    <th>Tipe</th>
                                    <th>Kunci / Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($questions as $question)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="questions[]" value="{{ $question->id }}" class="question-check"
                                                {{ $examPackage->questions->contains($question->id) ? 'checked' : '' }}>
                                        </td>
                                        <td>{!! Str::limit(strip_tags($question->content), 150) !!}</td>
                                        <td>
                                            <span class="badge {{ $question->type == 'multiple_choice' ? 'badge-info' : 'badge-warning' }}">
                                                {{ $question->type == 'multiple_choice' ? 'Pilgan' : 'Essay' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($question->type == 'multiple_choice')
                                            <small class="text-success">Kunci: {!! strip_tags($question->options->where('is_correct', true)->first()->content ?? '-') !!}</small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada soal tersedia untuk mata pelajaran ini. Silakan buat soal di Bank Soal.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary col-md-3">Simpan Perubahan Paket</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $('#check-all').click(function() {
        $('.question-check').prop('checked', this.checked);
    });
</script>
@endpush
@endsection
