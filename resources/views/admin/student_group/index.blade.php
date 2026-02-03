@extends('layouts.admin.app')

@section('title', 'Kelompok Peserta (Rombel)')
@section('page_title', 'Data Kelompok Peserta')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Tambah Kelompok Baru</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.student_group.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Nama Kelompok (Kelas)</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: XII IPA 1" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-plus"></i> Tambah</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Kelompok Peserta</h3>
            </div>
            <div class="card-body p-0">
                @if(session('success'))
                    <div class="alert alert-success m-2">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger m-2">{{ session('error') }}</div>
                @endif

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Nama Kelompok</th>
                            <th>Jumlah Peserta</th>
                            <th style="width: 150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groups as $group)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $group->name }}</td>
                                <td><span class="badge badge-info">{{ $group->students_count }} Siswa</span></td>
                                <td>
                                    <button class="btn btn-warning btn-xs" data-toggle="modal" data-target="#editModal{{ $group->id }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.student_group.destroy', $group->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kelompok ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-xs"><i class="fas fa-trash"></i> Hapus</button>
                                    </form>

                                    <!-- Modal Edit -->
                                    <div class="modal fade" id="editModal{{ $group->id }}">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Edit Kelompok</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="{{ route('admin.student_group.update', $group->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Nama Kelompok</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $group->name }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer justify-content-between">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Modal -->
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada kelompok peserta.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
