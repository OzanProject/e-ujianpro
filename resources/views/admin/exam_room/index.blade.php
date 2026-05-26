@extends('layouts.admin.app')

@section('title', 'Data Ruangan Ujian')
@section('page_title', 'Manajemen Ruangan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bold text-gray-800">Daftar Ruangan</h5>
                <a href="{{ route('admin.exam_room.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                    <i class="fas fa-plus mr-1"></i> Tambah Ruangan
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 border-0">Nama Ruangan</th>
                                <th class="px-4 py-3 border-0 text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rooms as $room)
                                <tr>
                                    <td class="px-4 py-3 align-middle font-weight-bold">{{ $room->name }}</td>
                                    <td class="px-4 py-3 align-middle text-center">
                                        <div class="btn-group shadow-sm rounded-lg" role="group">
                                            <a href="{{ route('admin.exam_room.assignments', $room->id) }}" class="btn btn-default btn-sm border-gray-200 hover:bg-blue-50 hover:text-blue-600 transition" title="Atur Peserta (Detail)">
                                                <i class="fas fa-users"></i>
                                            </a>
                                            <button type="button" class="btn btn-default btn-sm border-gray-200 hover:bg-green-50 hover:text-green-600 transition" data-toggle="modal" data-target="#generateModal{{ $room->id }}" title="Generate Peserta Otomatis (Seimbang L/P)">
                                                <i class="fas fa-magic"></i>
                                            </button>
                                            <a href="{{ route('admin.exam_room.edit', $room->id) }}" class="btn btn-default btn-sm border-gray-200 hover:bg-yellow-50 hover:text-yellow-600 transition" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.exam_room.destroy', $room->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus ruangan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-default btn-sm border-gray-200 hover:bg-red-50 hover:text-red-600 transition rounded-right" title="Hapus">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Modal Generate Seimbang -->
                                        <div class="modal fade" id="generateModal{{ $room->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title font-weight-bold">Generate Peserta - {{ $room->name }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('admin.exam_room.assign_balanced_gender', $room->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body text-left">
                                                            <div class="form-group">
                                                                <div class="alert alert-info py-2 px-3 mb-3 border-0 shadow-sm" style="background-color: #e3f2fd; color: #0c5460; border-radius: 8px;">
                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                        <div class="font-weight-bold">
                                                                            <i class="fas fa-users mr-2 text-primary"></i> Siswa Belum Masuk Ruangan
                                                                        </div>
                                                                        <div class="h5 mb-0 font-weight-bold text-primary">{{ $totalAvailable }}</div>
                                                                    </div>
                                                                    <div class="mt-2 text-sm d-flex justify-content-between">
                                                                        <span><i class="fas fa-male text-blue-500 mr-1"></i> L: <strong>{{ $maleAvailable }}</strong></span>
                                                                        <span><i class="fas fa-female text-pink-500 mr-1"></i> P: <strong>{{ $femaleAvailable }}</strong></span>
                                                                    </div>
                                                                </div>

                                                                <label class="font-weight-bold">Target Kapasitas Ruangan <span class="text-danger">*</span></label>
                                                                <input type="number" name="count" class="form-control form-control-lg" placeholder="Contoh: 20" required min="1" max="{{ $totalAvailable > 0 ? $totalAvailable : 1 }}">
                                                                <small class="form-text text-muted mt-2">
                                                                    Sistem akan memasukkan siswa secara acak namun memprioritaskan <strong>pembagian rata (50/50) Laki-laki & Perempuan</strong>.<br>
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-success"><i class="fas fa-magic mr-1"></i> Generate Sekarang</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-muted">Belum ada data ruangan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0 d-flex justify-content-end">
                {{ $rooms->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
