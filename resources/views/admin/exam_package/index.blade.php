@extends('layouts.admin.app')

@section('title', 'Paket Soal')
@section('page_title', 'Paket Soal')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Paket Soal</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.exam_package.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Buat Paket Baru
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Nama Paket</th>
                            <th>Kode</th>
                            <th>Mata Pelajaran</th>
                            <th>Jumlah Soal</th>
                            <th style="width: 150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packages as $package)
                            <tr>
                                <td>{{ $loop->iteration + $packages->firstItem() - 1 }}</td>
                                <td>{{ $package->name }}</td>
                                <td>{{ $package->code ?? '-' }}</td>
                                <td>{{ $package->subject->name }}</td>
                                <td>{{ $package->questions->count() }} Soal</td>
                                <td>
                                    <form action="{{ route('admin.exam_package.destroy', $package->id) }}" method="POST" onsubmit="return confirm('Hapus paket ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <a href="{{ route('admin.exam_package.preview', $package->id) }}" class="btn btn-default btn-xs" title="Preview Soal">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.exam_package.show', $package->id) }}" class="btn btn-info btn-xs" title="Kelola Soal">
                                            <i class="fas fa-list"></i> Kelola
                                        </a>
                                        <a href="{{ route('admin.exam_package.edit', $package->id) }}" class="btn btn-warning btn-xs" title="Edit Info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="submit" class="btn btn-danger btn-xs" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada paket soal.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $packages->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
