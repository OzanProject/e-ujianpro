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
                                            <a href="{{ route('admin.exam_room.assignments', $room->id) }}" class="btn btn-default btn-sm border-gray-200 hover:bg-blue-50 hover:text-blue-600 transition" title="Atur Peserta">
                                                <i class="fas fa-users"></i>
                                            </a>
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
