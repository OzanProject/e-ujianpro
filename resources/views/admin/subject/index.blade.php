@extends('layouts.admin.app')

@section('title', 'Data Mata Pelajaran')
@section('page_title', 'Data Mata Pelajaran')

@section('content')
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
                <h3 class="card-title">Daftar Mata Pelajaran</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.subject.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Mapel
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Kode</th>
                            <th>Nama Mata Pelajaran</th>
                            <th>Dibuat Pada</th>
                            <th style="width: 150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjects as $subject)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="badge bg-info">{{ $subject->code }}</span></td>
                                <td>{{ $subject->name }}</td>
                                <td>{{ $subject->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.subject.edit', $subject->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.subject.destroy', $subject->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus Mata Pelajaran ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data mata pelajaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
