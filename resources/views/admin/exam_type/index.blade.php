@extends('layouts.admin.app')
@section('title', 'Jenis Ujian')
@section('page_title', 'Master Data Jenis Ujian')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-header bg-white py-4 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="font-weight-bold text-dark mb-1">Daftar Jenis Ujian</h5>
                    <p class="text-muted text-sm mb-0">Kelola master data nama-nama ujian (misal: UTS, RAS, Quiz).</p>
                </div>
                <div>
                    <a href="{{ route('admin.exam_type.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 font-weight-bold">
                        <i class="fas fa-plus mr-2"></i> Tambah Jenis
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
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 px-4 py-3">Nama Ujian</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 px-4 py-3">Deskripsi</th>
                                <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7 px-4 py-3">Status</th>
                                <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7 px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($examTypes as $type)
                                <tr class="border-bottom">
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-blue-100 text-blue-600 rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-tag"></i>
                                            </div>
                                            <h6 class="mb-0 text-sm font-weight-bold text-dark">{{ $type->name }}</h6>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-muted">
                                        {{ $type->description ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($type->is_active)
                                            <span class="badge badge-sm bg-gradient-success text-white px-3 py-2 rounded-pill shadow-sm">Aktif</span>
                                        @else
                                            <span class="badge badge-sm bg-secondary text-white px-3 py-2 rounded-pill">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ route('admin.exam_type.edit', $type->id) }}" class="btn btn-sm btn-outline-info rounded-circle mr-2" title="Edit" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-pencil-alt text-xs"></i>
                                            </a>
                                            <form action="{{ route('admin.exam_type.destroy', $type->id) }}" method="POST" onsubmit="return confirm('Hapus jenis ujian ini?');">
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
                                                <i class="fas fa-tags text-gray-400 fa-3x"></i>
                                            </div>
                                            <h6 class="text-muted font-weight-bold">Belum ada Jenis Ujian</h6>
                                            <p class="text-sm text-gray-500">Silakan tambahkan jenis ujian baru master data.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer bg-white py-3 border-top-0 d-flex justify-content-end">
                    {{ $examTypes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
