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

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
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
                                            <form action="{{ route($baseRoute . '.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Hapus grup ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Hapus" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
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
            </div>
            
            <div class="card-footer bg-white py-3 border-top-0 d-flex justify-content-end">
                {{ $questionGroups->links() }}
            </div>
        </div>
    </div>
</div>

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
                            @php
                                $user = auth()->user();
                                if ($user->role === 'pengajar') {
                                    $subjects = $user->subjects;
                                } else {
                                    $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
                                    $subjects = \App\Models\Subject::where('created_by', $creatorId)->get();
                                }
                            @endphp
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

