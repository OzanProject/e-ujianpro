@extends('layouts.admin.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Data Guru / Pengajar</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Data Guru</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Guru / Pengajar</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.teacher.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Guru
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Terdaftar</th>
                                    <th style="width: 150px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teachers as $teacher)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $teacher->name }}</td>
                                        <td>{{ $teacher->email }}</td>
                                        <td>
                                            @if($teacher->status == 'active')
                                                <span class="badge badge-success badge-pill">Active</span>
                                            @elseif($teacher->status == 'pending')
                                                <span class="badge badge-warning badge-pill">Pending</span>
                                            @else
                                                <span class="badge badge-danger badge-pill">{{ ucfirst($teacher->status) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $teacher->created_at->format('d M Y') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                @if($teacher->status == 'pending')
                                                    <form action="{{ route('admin.teacher.approve', $teacher->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @elseif($teacher->status == 'active')
                                                    <form action="{{ route('admin.teacher.suspend', $teacher->id) }}" method="POST" onsubmit="return confirm('Suspend akun guru ini? Guru tidak akan bisa login.');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-warning btn-sm" title="Suspend">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    </form>
                                                @elseif($teacher->status == 'suspended')
                                                    <form action="{{ route('admin.teacher.activate', $teacher->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm" title="Aktifkan Kembali">
                                                            <i class="fas fa-power-off"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <a href="{{ route('admin.teacher.edit', $teacher->id) }}" class="btn btn-info btn-sm ml-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.teacher.destroy', $teacher->id) }}" method="POST" class="ml-1 d-inline" onsubmit="return confirm('Yakin ingin menghapus guru ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada data guru.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
