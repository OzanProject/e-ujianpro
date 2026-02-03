@extends('layouts.admin.app')

@section('title', 'Koreksi Ujian')
@section('page_title', 'Daftar Sesi Ujian (Untuk Koreksi)')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pilih Sesi Ujian</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Judul Ujian</th>
                            <th>Mata Pelajaran</th>
                            <th>Waktu Selesai</th>
                            <th>Jumlah Peserta Selesai</th>
                            <th style="width: 100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                            <tr>
                                <td>{{ $loop->iteration + $sessions->firstItem() - 1 }}</td>
                                <td>{{ $session->title }}</td>
                                <td>{{ $session->subject->name }}</td>
                                <td>{{ $session->end_time->format('d M Y H:i') }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $session->attempts_count }} Peserta</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.correction.show', $session->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada sesi ujian yang memiliki peserta selesai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $sessions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
