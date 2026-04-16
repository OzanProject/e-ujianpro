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
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100">
            <div class="card-body p-4 d-flex align-items-center bg-gradient-to-r from-blue-600 to-indigo-700 text-white" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                <div class="bg-white-20 rounded-circle p-3 mr-3" style="background: rgba(255,255,255,0.2);">
                    <i class="fas fa-database fa-lg text-white"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0 text-white">{{ number_format($stats['total']) }}</h5>
                    <p class="text-uppercase text-xs mb-0 opacity-75">Total Soal</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100 border-bottom-success">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="bg-success-light rounded-circle p-3 mr-3" style="background: rgba(40,167,69,0.1);">
                    <i class="fas fa-smile fa-lg text-success"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0 text-dark">{{ number_format($stats['easy']) }}</h5>
                    <p class="text-muted text-xs font-weight-bold text-uppercase mb-0">Mudah</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100 border-bottom-warning">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="bg-warning-light rounded-circle p-3 mr-3" style="background: rgba(255,193,7,0.1);">
                    <i class="fas fa-meh fa-lg text-warning"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0 text-dark">{{ number_format($stats['medium']) }}</h5>
                    <p class="text-muted text-xs font-weight-bold text-uppercase mb-0">Sedang</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden h-100 border-bottom-danger">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="bg-danger-light rounded-circle p-3 mr-3" style="background: rgba(220,53,69,0.1);">
                    <i class="fas fa-frown fa-lg text-danger"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0 text-dark">{{ number_format($stats['hard']) }}</h5>
                    <p class="text-muted text-xs font-weight-bold text-uppercase mb-0">Sulit</p>
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
                    <button type="button" id="btnBulkDelete" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm d-none">
                        <i class="fas fa-trash mr-1"></i> Hapus Terpilih (<span id="selectedCount">0</span>)
                    </button>
                </div>
                
                <div class="card-tools d-flex align-items-center">
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
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="input-group shadow-sm rounded-lg border-0 bg-white">
                                <div class="input-group-prepend border-0">
                                    <span class="input-group-text bg-white border-0 text-muted"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="search" name="search" value="{{ request('search') }}" class="form-control border-0 bg-white shadow-none" placeholder="Cari isi soal..." aria-label="Search">
                            </div>
                        </div>
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
                        <div class="col-md-2 mb-3 mb-md-0">
                            <select name="type" class="form-control border-0 shadow-sm rounded-lg font-weight-bold text-dark" onchange="this.form.submit()">
                                <option value="">-- Tipe --</option>
                                <option value="multiple_choice" {{ request('type') == 'multiple_choice' ? 'selected' : '' }}>PILGAN</option>
                                <option value="essay" {{ request('type') == 'essay' ? 'selected' : '' }}>ESAI</option>
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
                                            @if($question->type == 'multiple_choice')
                                                <span class="badge bg-light text-indigo px-2 py-1 rounded text-xs mr-2">
                                                    <i class="fas fa-list-ol mr-1"></i> PILGAN ({{ $question->options->count() }} Opsi)
                                                </span>
                                            @else
                                                <span class="badge bg-light text-orange px-2 py-1 rounded text-xs mr-2">
                                                    <i class="fas fa-pen-nib mr-1"></i> ESAI
                                                </span>
                                            @endif
                                            
                                            @if($question->type == 'multiple_choice')
                                                @php $correct = $question->options->where('is_correct', true)->first(); @endphp
                                                @if($correct)
                                                    <small class="text-success font-weight-bold text-xs">
                                                        <i class="fas fa-check-circle mr-1"></i> Kunci: {{ Str::limit(strip_tags($correct->content), 30) }}
                                                    </small>
                                                @endif
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

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <p class="text-xs text-muted font-weight-bold mb-0">
                        Menampilkan <span class="text-dark">{{ $questions->count() }}</span> dari <span class="text-dark">{{ $questions->total() }}</span> total soal
                    </p>
                    {{ $questions->withQueryString()->links() }}
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
            updateBulkDeleteBtn();
        });

        $('.q-checkbox').on('change', function() {
            updateBulkDeleteBtn();
        });

        function updateBulkDeleteBtn() {
            var selectedLength = $('.q-checkbox:checked').length;
            if (selectedLength > 0) {
                $('#btnBulkDelete').removeClass('d-none');
                $('#selectedCount').text(selectedLength);
            } else {
                $('#btnBulkDelete').addClass('d-none');
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
                            question_ids: selectedIds
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
