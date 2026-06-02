@extends('layouts.admin.app')

@section('title', 'Kelola Soal Paket')
@section('page_title', 'Kelola Soal: ' . $examPackage->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header bg-gradient-to-r from-blue-600 to-indigo-700 py-4 px-4 border-bottom-0 d-flex justify-content-between align-items-center text-white rounded-t-lg" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                <div>
                    <h5 class="font-weight-bold mb-1"><i class="fas fa-tasks mr-2 text-warning"></i> Kelola Soal Paket</h5>
                    <p class="text-xs mb-0 opacity-75">Pilih atau ubah soal yang akan dimasukkan ke dalam paket: <strong>{{ $examPackage->name }}</strong></p>
                </div>
                <div>
                    <a href="{{ route('admin.exam_package.index') }}" class="btn btn-light btn-sm rounded-pill font-weight-bold text-dark px-3 shadow-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="card-body bg-light p-4">
                @if(session('success'))
                    <div class="alert alert-success rounded shadow-sm border-0">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger rounded shadow-sm border-0">{{ session('error') }}</div>
                @endif

                <div class="row mb-4">
                    <div class="col-md-7 mb-3 mb-md-0">
                        <div class="card shadow-sm border-0 h-100 rounded-lg">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-random mr-2"></i> Generate Soal Otomatis</h6>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('admin.exam_package.random', $examPackage->id) }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Jumlah Diambil</label>
                                            <input type="number" name="count" class="form-control form-control-sm rounded bg-light border-0" placeholder="Contoh: 50" min="1" required>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Tipe Soal</label>
                                            <select name="type" class="form-control form-control-sm rounded bg-light border-0">
                                                <option value="all">Semua Tipe (Campur)</option>
                                                <option value="multiple_choice">PG Tunggal</option>
                                                <option value="multiple_choice_complex">PG Kompleks</option>
                                                <option value="boolean_grid">Benar / Salah</option>
                                                <option value="essay">Esai / Uraian</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm btn-block rounded-pill shadow-sm" onclick="return confirm('Peringatan: Ini akan menimpa/mengganti semua soal yang sudah ada di paket ini. Lanjutkan?')">
                                        <i class="fas fa-magic mr-1"></i> Generate Sekarang
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                         <div class="card shadow-sm border-0 bg-success text-white h-100 rounded-lg" style="background: linear-gradient(45deg, #1cc88a 0%, #13855c 100%);">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                                <i class="fas fa-check-circle fa-3x mb-3 opacity-75"></i>
                                <h4 class="font-weight-bold mb-1">{{ $examPackage->questions->count() }}</h4>
                                <span class="text-sm text-uppercase tracking-wider">Total Soal Terpilih</span>
                                <div class="mt-3 text-xs bg-white text-success px-3 py-1 rounded-pill font-weight-bold">
                                    Mapel: {{ $examPackage->subject->name }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-header bg-white border-bottom-0 p-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-list-ul mr-2 text-primary"></i> Daftar Soal Tersedia</h6>
                            <form action="{{ route('admin.exam_package.show', $examPackage->id) }}" method="GET" class="mt-2 mt-md-0">
                                <div class="input-group input-group-sm rounded-pill shadow-sm overflow-hidden" style="width: 250px;">
                                    <input type="text" name="q" class="form-control border-0 bg-light" placeholder="Cari isi soal..." value="{{ request('q') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary border-0 px-3" type="submit"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <form action="{{ route('admin.exam_package.assign', $examPackage->id) }}" method="POST">
                        @csrf
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                                <table class="table table-hover table-striped text-nowrap mb-0 align-middle">
                                    <thead class="bg-gray-100 text-secondary text-xs font-weight-bold text-uppercase" style="position: sticky; top: 0; z-index: 1;">
                                        <tr>
                                            <th style="width: 50px" class="text-center px-4 py-3">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="check-all">
                                                    <label class="custom-control-label" for="check-all"></label>
                                                </div>
                                            </th>
                                            <th class="px-4 py-3">Konten Soal</th>
                                            <th class="px-4 py-3 text-center">Tipe</th>
                                            <th class="px-4 py-3 text-center">Kunci / Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($questions as $question)
                                            <tr class="border-bottom">
                                                <td class="text-center px-4 py-3 align-middle">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input question-check" id="q_{{ $question->id }}" name="questions[]" value="{{ $question->id }}" {{ $examPackage->questions->contains($question->id) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="q_{{ $question->id }}"></label>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 align-middle" style="min-width: 300px; max-width: 500px; white-space: normal;">
                                                    <div class="text-sm text-gray-800" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                        {!! strip_tags($question->content) !!}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-center align-middle">
                                                    @php
                                                        $typeLabels = [
                                                            'multiple_choice' => ['label' => 'PG Tunggal', 'class' => 'bg-blue-100 text-blue-800'],
                                                            'multiple_choice_complex' => ['label' => 'PG Kompleks', 'class' => 'bg-indigo-100 text-indigo-800'],
                                                            'boolean_grid' => ['label' => 'Benar/Salah', 'class' => 'bg-green-100 text-green-800'],
                                                            'essay' => ['label' => 'Esai', 'class' => 'bg-yellow-100 text-yellow-800'],
                                                        ];
                                                        $typeInfo = $typeLabels[$question->type] ?? ['label' => $question->type, 'class' => 'bg-gray-100 text-gray-800'];
                                                    @endphp
                                                    <span class="badge px-3 py-1 rounded-pill {{ $typeInfo['class'] }}">
                                                        {{ $typeInfo['label'] }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center align-middle">
                                                    @if($question->type == 'multiple_choice')
                                                        <span class="text-xs font-weight-bold text-success"><i class="fas fa-check-circle mr-1"></i> {!! strip_tags($question->options->where('is_correct', true)->first()->content ?? '-') !!}</span>
                                                    @elseif($question->type == 'multiple_choice_complex')
                                                        <span class="text-xs font-weight-bold text-primary"><i class="fas fa-list-ul mr-1"></i> {{ $question->options->where('is_correct', true)->count() }} Benar</span>
                                                    @elseif($question->type == 'boolean_grid')
                                                        <span class="text-xs font-weight-bold text-success"><i class="fas fa-th-list mr-1"></i> {{ $question->options->count() }} Baris</span>
                                                    @else
                                                        <span class="text-xs text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <i class="fas fa-box-open fa-3x text-gray-300 mb-3"></i>
                                                        <h6 class="text-muted font-weight-bold">Tidak ada soal tersedia</h6>
                                                        <p class="text-xs text-gray-400">Silakan buat soal di Bank Soal untuk mata pelajaran ini.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white py-4 d-flex justify-content-end border-top">
                            <button type="submit" class="btn btn-success rounded-pill px-5 shadow-sm font-weight-bold">
                                <i class="fas fa-save mr-2"></i> Simpan Perubahan Paket
                            </button>
                        </div>
                    </form>
                </div>
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
