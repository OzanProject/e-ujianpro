@extends('layouts.admin.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Bacaan / Wacana Soal</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.reading_text.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Bacaan
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
                            <th>Judul</th>
                            <th>Cuplikan Isi</th>
                            <th style="width: 150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($readingTexts as $text)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $text->subject->name }}</td>
                                <td>{{ $text->title }}</td>
                                <td>{{ Str::limit(strip_tags($text->content), 100) }}</td>
                                <td>
                                    <form action="{{ route('admin.reading_text.destroy', $text->id) }}" method="POST" onsubmit="return confirm('Hapus bacaan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <a href="{{ route('admin.reading_text.edit', $text->id) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i> Edit</a>
                                        <button type="submit" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i> Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">Belum ada data bacaan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $readingTexts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
