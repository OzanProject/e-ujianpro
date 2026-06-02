@extends('layouts.admin.app')
@php
    $user = auth()->user();
    $baseRoute = $user->role === 'pengajar' ? 'pengajar.question' : 'admin.question';
@endphp
@section('title', 'Bank Soal Premium')
@section('page_title', 'Manajemen Bank Soal')

@section('content')
{{-- Stats Overview --}}
<div class="row mb-4">
    <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100">
            <div class="card-body p-3 d-flex align-items-center" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                <div class="rounded-circle p-2 mr-3" style="background: rgba(255,255,255,0.2);">
                    <i class="fas fa-database fa-lg text-white"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0 text-white">{{ number_format($stats['total']) }}</h5>
                    <p class="text-uppercase text-xs mb-0" style="color:rgba(255,255,255,0.8)">Total Soal</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-2 mr-3" style="background: rgba(78,115,223,0.1);">
                    <i class="fas fa-list-ol fa-lg text-primary"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0 text-dark">{{ number_format($stats['mc']) }}</h5>
                    <p class="text-muted text-xs font-weight-bold text-uppercase mb-0">PG Tunggal</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-2 mr-3" style="background: rgba(54,185,204,0.1);">
                    <i class="fas fa-check-double fa-lg text-info"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0 text-dark">{{ number_format($stats['mc_complex']) }}</h5>
                    <p class="text-muted text-xs font-weight-bold text-uppercase mb-0">PG Kompleks</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-2 mr-3" style="background: rgba(246,194,62,0.15);">
                    <i class="fas fa-th fa-lg text-warning"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0 text-dark">{{ number_format($stats['boolean']) }}</h5>
                    <p class="text-muted text-xs font-weight-bold text-uppercase mb-0">Benar/Salah</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-2 mr-3" style="background: rgba(133,135,150,0.1);">
                    <i class="fas fa-pen-fancy fa-lg text-secondary"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0 text-dark">{{ number_format($stats['essay']) }}</h5>
                    <p class="text-muted text-xs font-weight-bold text-uppercase mb-0">Esai/Uraian</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-2 mr-3" style="background: rgba(40,167,69,0.1);">
                    <i class="fas fa-smile fa-lg text-success"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0 text-dark">{{ number_format($stats['easy']) }}</h5>
                    <p class="text-muted text-xs font-weight-bold text-uppercase mb-0">Tingkat Mudah</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-2 mr-3" style="background: rgba(253,126,20,0.1);">
                    <i class="fas fa-meh fa-lg text-orange" style="color: #fd7e14;"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0 text-dark">{{ number_format($stats['medium']) }}</h5>
                    <p class="text-muted text-xs font-weight-bold text-uppercase mb-0">Tingkat Sedang</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-2 mr-3" style="background: rgba(220,53,69,0.1);">
                    <i class="fas fa-frown fa-lg text-danger"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0 text-dark">{{ number_format($stats['hard']) }}</h5>
                    <p class="text-muted text-xs font-weight-bold text-uppercase mb-0">Tingkat Sulit</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <h6 class="font-weight-bold text-dark mb-0 mr-3"><i class="fas fa-filter text-primary mr-2"></i> Kontrol Bank Soal</h6>
                    <div id="bulkActionButtons" class="d-none">
                        <button type="button" class="btn btn-warning btn-sm rounded-pill px-3 shadow-sm mr-2" data-toggle="modal" data-target="#bulkTagModal">
                            <i class="fas fa-tags mr-1"></i> Tambah Tag (<span class="selectedCount">0</span>)
                        </button>
                        <button type="button" id="btnBulkDelete" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm">
                            <i class="fas fa-trash mr-1"></i> Hapus Terpilih (<span class="selectedCount">0</span>)
                        </button>
                    </div>
                </div>
                
                <div class="card-tools d-flex align-items-center">
                    <a href="{{ route($baseRoute . '.print_card', request()->all()) }}" target="_blank" class="btn btn-info btn-sm font-weight-bold shadow-sm rounded-pill px-4 mr-2">
                        <i class="fas fa-print mr-1"></i> Cetak Kartu Soal
                    </a>
                    <a href="{{ route($baseRoute . '.export.word', request()->all()) }}" class="btn btn-dark btn-sm font-weight-bold shadow-sm rounded-pill px-4 mr-2">
                        <i class="fas fa-file-word mr-1"></i> Export Word
                    </a>
                    <button type="button" class="btn btn-success btn-sm font-weight-bold shadow-sm rounded-pill px-4 mr-2" data-toggle="modal" data-target="#importModal">
                        <i class="fas fa-file-import mr-1"></i> Import Word/Excel
                    </button>
                    <a href="{{ route($baseRoute . '.create') }}" class="btn btn-primary btn-sm font-weight-bold shadow-sm rounded-pill px-4">
                        <i class="fas fa-plus mr-1"></i> Buat Soal Manual
                    </a>
                </div>
            </div>
            
            <div class="card-body bg-light-gray p-4">
                {{-- Advanced Search & Filter Bar --}}
                <form action="{{ route($baseRoute . '.index') }}" method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="input-group shadow-sm rounded-lg border-0 bg-white">
                                <div class="input-group-prepend border-0">
                                    <span class="input-group-text bg-white border-0 text-muted"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="search" name="search" value="{{ request('search') }}" class="form-control border-0 bg-white shadow-none" placeholder="Cari isi soal atau tag (cth: Kelas 7)..." aria-label="Search">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <select name="subject_id" class="form-control border-0 shadow-sm rounded-lg font-weight-bold text-dark" onchange="this.form.submit()">
                                <option value="">-- Semua Mapel --</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <select name="exam_package_id" class="form-control border-0 shadow-sm rounded-lg font-weight-bold text-dark" onchange="this.form.submit()">
                                <option value="">-- Semua Paket Soal --</option>
                                @foreach($packages as $package)
                                    <option value="{{ $package->id }}" {{ request('exam_package_id') == $package->id ? 'selected' : '' }}>
                                        {{ $package->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-3 mb-md-0">
                            <select name="type" class="form-control border-0 shadow-sm rounded-lg font-weight-bold text-dark" onchange="this.form.submit()">
                                <option value="">-- Tipe Soal --</option>
                                <option value="multiple_choice" {{ request('type') == 'multiple_choice' ? 'selected' : '' }}>PG Tunggal</option>
                                <option value="multiple_choice_complex" {{ request('type') == 'multiple_choice_complex' ? 'selected' : '' }}>PG Kompleks</option>
                                <option value="boolean_grid" {{ request('type') == 'boolean_grid' ? 'selected' : '' }}>Benar / Salah</option>
                                <option value="essay" {{ request('type') == 'essay' ? 'selected' : '' }}>Esai / Uraian</option>
                            </select>
                        </div>
                         <div class="col-md-2 mb-3 mb-md-0">
                            <select name="difficulty" class="form-control border-0 shadow-sm rounded-lg font-weight-bold text-dark" onchange="this.form.submit()">
                                <option value="">-- Sulit --</option>
                                <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>MUDAH</option>
                                <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>SEDANG</option>
                                <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>SULIT</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-dark btn-block rounded-lg shadow-sm font-weight-bold">
                                Terapkan
                            </button>
                        </div>
                    </div>
                </form>

                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-lg mb-4">
                        <i class="fas fa-check-circle mr-2 text-white"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error_list'))
                    <div class="alert alert-warning border-0 shadow-sm rounded-lg mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-exclamation-triangle mr-2 text-warning"></i>
                            <strong class="text-dark">Beberapa soal gagal di-import:</strong>
                        </div>
                        <ul class="mb-0 text-sm list-unstyled pl-4">
                            @foreach(session('error_list') as $err)
                                <li class="text-dark mb-1 small"><i class="fas fa-caret-right mr-1 opacity-50"></i> {{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="table-responsive bg-white rounded-lg shadow-sm">
                    <table class="table table-hover align-middle mb-0" id="questionTable">
                        <thead class="bg-blue-50">
                            <tr>
                                <th class="px-4 py-3 border-0 text-center" style="width: 40px;">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="checkAll">
                                        <label class="custom-control-label" for="checkAll"></label>
                                    </div>
                                </th>
                                <th class="text-secondary text-xs font-weight-bold text-uppercase px-4 py-3 border-0">No</th>
                                <th class="text-secondary text-xs font-weight-bold text-uppercase px-4 py-3 border-0" style="min-width: 150px;">Mapel & Tags</th>
                                <th class="text-secondary text-xs font-weight-bold text-uppercase px-4 py-3 border-0" style="min-width: 300px;">Isi Soal</th>
                                <th class="text-secondary text-xs font-weight-bold text-uppercase px-4 py-3 border-0 text-center">Tingkat</th>
                                <th class="text-secondary text-xs font-weight-bold text-uppercase px-4 py-3 border-0 text-center" style="width: 140px; white-space: nowrap;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($questions as $question)
                                <tr class="border-bottom transition-all hover:bg-light">
                                    <td class="px-4 py-3 text-center align-middle">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input q-checkbox" name="question_ids[]" value="{{ $question->id }}" id="check-{{ $question->id }}">
                                            <label class="custom-control-label" for="check-{{ $question->id }}"></label>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-middle text-muted font-weight-bold">
                                        {{ $loop->iteration + $questions->firstItem() - 1 }}
                                    </td>
                                    <td class="px-4 py-3 align-middle">
                                        <div class="d-flex flex-column">
                                            <span class="badge badge-light text-primary border-primary px-2 py-1 mb-1 text-xs" style="border: 1px solid rgba(0,123,255,0.2) !important;">
                                                <i class="fas fa-book mr-1"></i> {{ $question->subject->name }}
                                            </span>
                                            <div class="d-flex flex-wrap mt-1">
                                                @foreach($question->tags as $tag)
                                                    <span class="badge bg-gray-100 text-muted px-2 py-1 mr-1 mb-1 text-xs shadow-none border" style="font-size: 10px;">{{ $tag->name }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-middle">
                                        @if($question->readingText)
                                            <div class="mb-3 p-2 bg-blue-50 rounded border-left-info shadow-sm text-dark text-xs" style="max-height: 8em; overflow: hidden; line-height: 1.4; border-left-width: 3px !important; border-color: #36b9cc !important;">
                                                <span class="badge badge-info text-xs mb-1"><i class="fas fa-info-circle mr-1"></i> Ada Petunjuk Soal</span><br>
                                                <div class="reading-text-preview" style="max-height: 60px; overflow: hidden;">
                                                    {!! strip_tags($question->readingText->content, '<img><br><p><b><i><strong>') !!}
                                                </div>
                                            </div>
                                        @endif
                                        <div class="question-preview text-dark text-sm max-w-lg mb-2" style="max-height: 4.5em; overflow: hidden; line-height: 1.5;">
                                            {!! Str::limit(strip_tags($question->content, '<img><br><p><b><i><strong>'), 150) !!}
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center">
                                            @php
                                                $typeConfig = match($question->type) {
                                                    'multiple_choice'         => ['icon'=>'fa-list-ol',     'color'=>'text-primary',  'label'=>'PG Tunggal'],
                                                    'multiple_choice_complex' => ['icon'=>'fa-check-double','color'=>'text-info',     'label'=>'PG Kompleks'],
                                                    'boolean_grid'            => ['icon'=>'fa-th',          'color'=>'text-warning',  'label'=>'Benar/Salah'],
                                                    'essay'                   => ['icon'=>'fa-pen-nib',     'color'=>'text-secondary','label'=>'Esai'],
                                                    default                   => ['icon'=>'fa-question',    'color'=>'text-muted',    'label'=>$question->type],
                                                };
                                            @endphp
                                            <span class="badge bg-light {{ $typeConfig['color'] }} px-2 py-1 rounded text-xs mr-2">
                                                <i class="fas {{ $typeConfig['icon'] }} mr-1"></i> {{ $typeConfig['label'] }}
                                                @if($question->type !== 'essay') ({{ $question->options->count() }} Opsi) @endif
                                            </span>

                                            @if($question->type === 'multiple_choice')
                                                @php $correct = $question->options->where('is_correct', true)->first(); @endphp
                                                @if($correct)
                                                    <small class="text-success font-weight-bold text-xs">
                                                        <i class="fas fa-check-circle mr-1"></i> Kunci: {{ Str::limit(strip_tags($correct->content), 30) }}
                                                    </small>
                                                @endif
                                            @elseif($question->type === 'multiple_choice_complex')
                                                @php $corrects = $question->options->where('is_correct', true)->pluck('content'); @endphp
                                                @if($corrects->isNotEmpty())
                                                    <small class="text-info font-weight-bold text-xs">
                                                        <i class="fas fa-check-double mr-1"></i>
                                                        Kunci: {{ $corrects->map(fn($c) => Str::limit(strip_tags($c), 15))->implode(', ') }}
                                                    </small>
                                                @endif
                                            @elseif($question->type === 'boolean_grid')
                                                @php
                                                    $boolKeys = $question->options->map(fn($o) => $o->is_correct ? 'B' : 'S')->implode(', ');
                                                @endphp
                                                <small class="text-warning font-weight-bold text-xs">
                                                    <i class="fas fa-th mr-1"></i> Kunci: {{ $boolKeys }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-middle text-center">
                                        @if($question->difficulty == 'easy')
                                            <span class="badge badge-pill px-3 py-2 text-xs shadow-sm bg-soft-success text-success">MUDAH</span>
                                        @elseif($question->difficulty == 'medium')
                                            <span class="badge badge-pill px-3 py-2 text-xs shadow-sm bg-soft-warning text-warning">SEDANG</span>
                                        @else
                                            <span class="badge badge-pill px-3 py-2 text-xs shadow-sm bg-soft-danger text-danger">SULIT</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 align-middle text-center">
                                        <div class="btn-group rounded-lg overflow-hidden shadow-sm border">
                                            <button type="button" class="btn btn-white btn-sm px-3 text-primary border-right" 
                                                    onclick="previewQuestion({{ $question->id }})" data-toggle="tooltip" title="Preview Soal">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="{{ route($baseRoute . '.edit', $question->id) }}" class="btn btn-white btn-sm px-3 text-warning border-right" data-toggle="tooltip" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-white btn-sm px-3 text-danger" onclick="deleteQuestion({{ $question->id }})" data-toggle="tooltip" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                        <form id="delete-form-{{ $question->id }}" action="{{ route($baseRoute . '.destroy', $question->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center opacity-50">
                                            <div class="bg-light rounded-circle p-4 mb-3">
                                                <i class="fas fa-database fa-3x text-muted border-dashed p-3"></i>
                                            </div>
                                            <h5 class="font-weight-bold text-muted">Belum Ada Soal</h5>
                                            <p class="text-xs">Mulai isi bank soal Anda dengan menekan tombol Buat Soal atau Import.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <p class="text-xs text-muted font-weight-bold mb-0 mr-3">
                            Menampilkan <span class="text-dark">{{ $questions->count() }}</span> dari <span class="text-dark">{{ $questions->total() }}</span> total soal
                        </p>
                        <form method="GET" action="" class="d-flex align-items-center mb-0" id="perPageForm">
                            @foreach(request()->except('per_page', 'page') as $key => $val)
                                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                            @endforeach
                            <label class="text-xs text-muted mb-0 mr-1">Tampilkan:</label>
                            <select name="per_page" class="form-control form-control-sm" style="width:75px;"
                                onchange="document.getElementById('perPageForm').submit()">
                                @foreach([10, 20, 50, 100] as $opt)
                                    <option value="{{ $opt }}" {{ request('per_page', 10) == $opt ? 'selected' : '' }}>
                                        {{ $opt }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    {{ $questions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Preview Modal --}}
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg overflow-hidden">
            <div class="modal-header bg-gradient-to-r from-blue-700 to-indigo-800 text-white border-bottom-0 py-3">
                <h5 class="modal-title font-weight-bold" id="previewModalLabel">
                    <i class="fas fa-eye mr-2"></i> Preview Soal
                </h5>
                <button type="button" class="close text-white opacity-75 hover:opacity-100" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-5 bg-white" id="previewContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Bulk Tag Modal --}}
<div class="modal fade" id="bulkTagModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-gradient-to-r from-blue-700 to-indigo-800 text-white border-bottom-0 py-3">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-tags mr-2"></i> Tambah Tag Massal
                </h5>
                <button type="button" class="close text-white opacity-75 hover:opacity-100" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="form-group">
                    <label class="font-weight-bold text-dark">Nama Tag / Label</label>
                    <input type="text" id="bulkTagInput" class="form-control" placeholder="Contoh: Kelas 7, UTS, PAI" required>
                    <small class="text-muted mt-2 d-block">Gunakan koma (,) jika ingin menambahkan lebih dari satu tag sekaligus.</small>
                </div>
            </div>
            <div class="modal-footer bg-light border-top-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4 shadow-sm font-weight-bold" data-dismiss="modal">Batal</button>
                <button type="button" id="btnSubmitBulkTag" class="btn btn-primary rounded-pill px-4 shadow-sm font-weight-bold">
                    <i class="fas fa-save mr-1"></i> Simpan Tag
                </button>
            </div>
        </div>
    </div>
</div>

@include('admin.question._import_modal')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Check All Feature
        $('#checkAll').on('change', function() {
            $('.q-checkbox').prop('checked', $(this).prop('checked'));
            updateBulkActionBtns();
        });

        $('.q-checkbox').on('change', function() {
            updateBulkActionBtns();
        });

        function updateBulkActionBtns() {
            var selectedLength = $('.q-checkbox:checked').length;
            if (selectedLength > 0) {
                $('#bulkActionButtons').removeClass('d-none');
                $('.selectedCount').text(selectedLength);
            } else {
                $('#bulkActionButtons').addClass('d-none');
            }
        }

        // Bulk Delete Process
        $('#btnBulkDelete').on('click', function() {
            var selectedIds = [];
            $('.q-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            Swal.fire({
                title: 'Hapus Massal?',
                text: "Anda akan menghapus " + selectedIds.length + " soal sekaligus. Tindakan ini permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ $user->role === 'pengajar' ? route('pengajar.question.bulk_destroy') : route('admin.question.bulk_destroy') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            ids: selectedIds
                        },
                        success: function(response) {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        },
                        error: function(err) {
                            Swal.fire('Oops!', 'Gagal menghapus data massal.', 'error');
                        }
                    });
                }
            });
        });

        // Bulk Tag Process
        $('#btnSubmitBulkTag').on('click', function() {
            var selectedIds = [];
            $('.q-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });
            var tags = $('#bulkTagInput').val().trim();

            if (!tags) {
                Swal.fire('Error', 'Nama tag tidak boleh kosong.', 'error');
                return;
            }

            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

            $.ajax({
                url: "{{ $user->role === 'pengajar' ? route('pengajar.question.bulk_tag') : route('admin.question.bulk_tag') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: selectedIds,
                    tags: tags
                },
                success: function(response) {
                    Swal.fire('Berhasil!', response.message, 'success').then(() => {
                        location.reload();
                    });
                },
                error: function(err) {
                    btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Tag');
                    Swal.fire('Oops!', 'Gagal menambahkan tag massal.', 'error');
                }
            });
        });
    });

    function deleteQuestion(id) {
        Swal.fire({
            title: 'Hapus Soal?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    function previewQuestion(id) {
        $('#previewModal').modal('show');
        $('#previewContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');
        
        var previewUrl = "{{ url('/') }}/" + ( "{{ $user->role }}" === "pengajar" ? "pengajar" : "admin" ) + "/question/" + id + "/preview";
        $.get(previewUrl, function(html) {
            $('#previewContent').html(html);
        });
    }
</script>
@endpush

@push('styles')
<style>
    .bg-light-gray { background-color: #f8f9fc; }
    .bg-blue-50 { background-color: #f0f7ff; }
    .scale-75 { transform: scale(0.85); }
    .max-w-lg { max-width: 500px; }
    .transition-all { transition: all 0.2s ease; }
    .bg-white-20 { background: rgba(255,255,255,0.2); }
    .border-bottom-success { border-bottom: 4px solid #28a745 !important; }
    .border-bottom-warning { border-bottom: 4px solid #ffc107 !important; }
    .border-bottom-danger { border-bottom: 4px solid #dc3545 !important; }
    .text-indigo { color: #6610f2; }
    .text-orange { color: #fd7e14; }
    .bg-soft-success { background-color: rgba(40, 167, 69, 0.1) !important; }
    .bg-soft-warning { background-color: rgba(255, 193, 7, 0.1) !important; }
    .bg-soft-danger { background-color: rgba(220, 53, 69, 0.1) !important; }
    
    /* Table Thumbnail Control */
    .reading-text-preview img, .question-preview img {
        max-height: 40px !important;
        width: auto !important;
        max-width: 100% !important;
        object-fit: contain;
        border-radius: 4px;
        border: 1px solid #e3e6f0;
        margin: 2px 0;
        vertical-align: middle;
    }
</style>
@endpush
@endsection
