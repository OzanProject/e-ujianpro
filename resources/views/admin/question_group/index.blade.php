@extends('layouts.admin.app')

@section('title', 'Grup Soal')
@section('page_title', 'Kelola Grup Soal')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-header bg-white py-4 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="font-weight-bold text-dark mb-1">Daftar Grup Soal</h5>
                    <p class="text-muted text-sm mb-0">Kelompokkan soal untuk manajemen konversi skor yang lebih baik.</p>
                </div>
                <div>
                    <button type="button" class="btn btn-outline-primary shadow-sm rounded-pill px-4 font-weight-bold mr-2" data-toggle="modal" data-target="#modalGenerate">
                        <i class="fas fa-magic mr-2"></i> Generate Cerdas
                    </button>
                    <a href="{{ route($baseRoute . '.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 font-weight-bold">
                        <i class="fas fa-plus mr-2"></i> Tambah Grup
                    </a>
                </div>
            </div>
            
            <div class="card-body p-0">
                @if(session('success'))
                    <div class="alert alert-success m-4 border-0 shadow-sm d-flex align-items-center" role="alert" style="background-color: #d1fae5; color: #065f46;">
                        <i class="fas fa-check-circle mr-2 text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                {{-- Filter & Per Page --}}
                <div class="px-4 py-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                    <form action="{{ route($baseRoute . '.index') }}" method="GET" class="form-inline">
                        <div class="form-group mr-3">
                            <label class="text-xs font-weight-bold text-muted mr-2">FILTER MAPEL:</label>
                            <select name="subject_id" class="form-control form-control-sm rounded-pill border-gray-300" onchange="this.form.submit()">
                                <option value="">Semua Mapel</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="text-xs font-weight-bold text-muted mr-2">TAMPILKAN:</label>
                            <select name="per_page" class="form-control form-control-sm rounded-pill border-gray-300" onchange="this.form.submit()">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 data</option>
                                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20 data</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 data</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 data</option>
                            </select>
                        </div>
                    </form>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 font-weight-bold shadow-sm" id="btn-bulk-delete" disabled onclick="confirmBulkDelete()">
                            <i class="fas fa-trash-alt mr-1"></i> Hapus Terpilih
                        </button>
                    </div>
                </div>

                <form id="bulkDeleteForm" action="{{ route($baseRoute . '.bulk_destroy') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3" style="width: 40px;">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="check-all">
                                            <label class="custom-control-label" for="check-all"></label>
                                        </div>
                                    </th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 px-4 py-3">Mata Pelajaran</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 px-4 py-3">Nama Grup</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7 px-4 py-3">Jumlah Soal</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7 px-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($questionGroups as $group)
                                    <tr class="border-bottom">
                                        <td class="px-4 py-3">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="ids[]" value="{{ $group->id }}" class="custom-control-input item-check" id="check-{{ $group->id }}">
                                                <label class="custom-control-label" for="check-{{ $group->id }}"></label>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary-light rounded p-2 mr-3 text-primary" style="background-color: #ebf5ff;">
                                                    <i class="fas fa-book"></i>
                                                </div>
                                                <span class="font-weight-bold text-dark">{{ $group->subject->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-sm text-dark font-weight-600">{{ $group->name }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="badge badge-pill badge-light border px-3">{{ $group->questions_count }} Soal</span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="d-flex justify-content-center">
                                                <a href="{{ route($baseRoute . '.edit', $group->id) }}" class="btn btn-sm btn-outline-info rounded-circle mr-2" title="Edit" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-pencil-alt text-xs"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" title="Hapus" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;" onclick="deleteSingle({{ $group->id }})">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center justify-content-center">
                                                <div class="bg-gray-100 rounded-circle p-4 mb-3">
                                                    <i class="fas fa-layer-group text-gray-400 fa-3x"></i>
                                                </div>
                                                <h6 class="text-muted font-weight-bold">Belum ada Grup Soal</h6>
                                                <p class="text-sm text-gray-500">Gunakan Generate Cerdas untuk membuat grup otomatis.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            
            <div class="card-footer bg-white py-3 border-top-0 d-flex justify-content-end">
                {{ $questionGroups->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Hidden forms for single actions --}}
<form id="singleDeleteForm" method="POST" style="display:none;">@csrf @method('DELETE')</form>

@push('scripts')
<script>
    $(document).ready(function() {
        const $checkAll = $('#check-all');
        const $itemChecks = $('.item-check');
        const $btnBulkDelete = $('#btn-bulk-delete');

        $checkAll.on('change', function() {
            $itemChecks.prop('checked', this.checked);
            toggleBulkButton();
        });

        $itemChecks.on('change', function() {
            $checkAll.prop('checked', $itemChecks.length === $('.item-check:checked').length);
            toggleBulkButton();
        });

        function toggleBulkButton() {
            $btnBulkDelete.prop('disabled', $('.item-check:checked').length === 0);
        }
    });

    function confirmBulkDelete() {
        if (confirm('Apakah Anda yakin ingin menghapus grup soal yang dipilih?')) {
            document.getElementById('bulkDeleteForm').submit();
        }
    }

    function deleteSingle(id) {
        if (confirm('Apakah Anda yakin ingin menghapus grup soal ini?')) {
            const form = document.getElementById('singleDeleteForm');
            form.action = "{{ url('admin/question_group') }}/" + id;
            @if($baseRoute === 'pengajar')
                form.action = "{{ url('admin/pengajar/question_group') }}/" + id;
            @endif
            form.submit();
        }
    }
</script>
@endpush

<!-- Modal Generate Cerdas -->
<div class="modal fade" id="modalGenerate" tabindex="-1" role="dialog" aria-labelledby="modalGenerateLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-dark" id="modalGenerateLabel">Generate Grup Cerdas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route($baseRoute . '.generate') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <p class="text-sm text-muted mb-4">Sistem akan secara otomatis membuat grup dan memindahkan soal ke dalam grup tersebut berdasarkan kriteria yang Anda pilih.</p>
                    
                    <div class="form-group mb-4">
                        <label class="text-xs font-weight-bold text-uppercase text-muted mb-2">Pilih Mata Pelajaran</label>
                        <select name="subject_id" class="form-control rounded-pill border-gray-300" required>
                            <option value="">-- Pilih Mapel --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label class="text-xs font-weight-bold text-uppercase text-muted mb-2">Strategi Pengelompokan</label>
                        <div class="row">
                            <div class="col-6">
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="strategyType" name="strategy" value="type" class="custom-control-input" checked>
                                    <label class="custom-control-label font-weight-bold text-dark" for="strategyType">Berdasarkan Tipe</label>
                                    <p class="text-xs text-muted">PG, PG Kompleks, Esai, dll.</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="strategyDifficulty" name="strategy" value="difficulty" class="custom-control-input">
                                    <label class="custom-control-label font-weight-bold text-dark" for="strategyDifficulty">Berdasarkan Kesulitan</label>
                                    <p class="text-xs text-muted">Mudah, Sedang, Sulit.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 font-weight-bold">
                        Mulai Generate <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

