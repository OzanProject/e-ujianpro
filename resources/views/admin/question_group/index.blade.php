@extends('layouts.admin.app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Grup Soal</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.question_group.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Grup
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
                            <th>Mata Pelajaran</th>
                            <th>Nama Grup</th>
                            <th>Jumlah Soal</th>
                            <th style="width: 150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questionGroups as $group)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $group->subject->name }}</td>
                                <td>{{ $group->name }}</td>
                                <td>{{ $group->questions_count }}</td>
                                <td>
                                    <form action="{{ route('admin.question_group.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Hapus grup ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <a href="{{ route('admin.question_group.edit', $group->id) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i> Edit</a>
                                        <button type="submit" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i> Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">Belum ada grup soal.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $questionGroups->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
