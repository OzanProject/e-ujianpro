@extends('layouts.admin.app')

@section('title', 'Daftar Peserta Ujian')
@section('page_title', 'Koreksi: ' . $session->title)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Hasil Peserta</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.correction.index') }}" class="btn btn-default btn-sm">Kembali</a>
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
                            <th>Nama Peserta</th>
                            <th>Nilai Saat Ini</th>
                            <th>Waktu Selesai</th>
                            <th style="width: 100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attempts as $attempt)
                            <tr>
                                <td>{{ $loop->iteration + $attempts->firstItem() - 1 }}</td>
                                <td>{{ $attempt->student->name }}</td>
                                <td>
                                    <span class="badge badge-success" style="font-size: 14px">{{ number_format($attempt->total_score, 2) }}</span>
                                </td>
                                <td>{{ $attempt->updated_at->format('d M Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.correction.edit', $attempt->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Koreksi
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada peserta yang menyelesaikan ujian ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $attempts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
