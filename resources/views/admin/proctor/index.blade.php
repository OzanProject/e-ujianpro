@extends('layouts.admin.app')

@section('title', 'Data Pengawas')
@section('page_title', 'Data Pengawas')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary">
                <h3 class="card-title text-white">
                    <i class="fas fa-user-secret mr-2"></i> Data Pengawas
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.proctor.create') }}" class="btn btn-light text-primary font-weight-bold btn-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah Pengawas
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Nama Pengawas</th>
                                <th>Email / Username</th>
                                <th>Ruangan</th>
                                <th class="text-center">Status</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proctors as $proctor)
                                <tr>
                                    <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-40 mr-3">
                                                 @if($proctor->photo && file_exists(public_path('storage/' . $proctor->photo)))
                                                    <img src="{{ asset('storage/' . $proctor->photo) }}" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover;">
                                                @else
                                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <span class="font-weight-bold">{{ $proctor->name }}</span>
                                        </div>
                                    </td>
                                    <td class="align-middle">{{ $proctor->email }}</td>
                                    <td class="align-middle">
                                         @if($proctor->examRoom)
                                            <span class="badge badge-info">{{ $proctor->examRoom->name }}</span>
                                         @else
                                            <span class="badge badge-secondary">Semua Ruangan</span>
                                         @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($proctor->status == 'active')
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Suspended</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <form action="{{ route('admin.proctor.destroy', $proctor->id) }}" method="POST" onsubmit="return confirm('Hapus akun pengawas ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.proctor.edit', $proctor->id) }}" class="btn btn-warning text-white" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="submit" class="btn btn-danger" title="Hapus">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center p-4">
                                        <div class="text-muted">Belum ada data pengawas</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $proctors->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
