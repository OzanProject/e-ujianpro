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
                <form action="{{ route($baseRoute . '.index') }}" method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <input type="text" name="search" class="form-control" placeholder="Cari mata pelajaran..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <select name="subject_id" class="form-control">
                                <option value="">-- Semua Mata Pelajaran --</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <select name="sort" class="form-control">
                                <option value="az" {{ request('sort', 'az') == 'az' ? 'selected' : '' }}>A-Z (Mapel)</option>
                                <option value="za" {{ request('sort') == 'za' ? 'selected' : '' }}>Z-A (Mapel)</option>
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <select name="limit" class="form-control">
                                <option value="10" {{ request('limit', 10) == 10 ? 'selected' : '' }}>10 Data</option>
                                <option value="20" {{ request('limit') == 20 ? 'selected' : '' }}>20 Data</option>
                                <option value="50" {{ request('limit') == 50 ? 'selected' : '' }}>50 Data</option>
                                <option value="100" {{ request('limit') == 100 ? 'selected' : '' }}>100 Data</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Filter</button>
                        </div>
                    </div>
                </form>

                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Judul Ujian</th>
                            <th>Mata Pelajaran</th>
                            <th>Waktu Selesai</th>
                            <th>Jumlah Peserta (Berlangsung & Selesai)</th>
                            <th style="width: 100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                            <tr>
                                <td>{{ $loop->iteration + $sessions->firstItem() - 1 }}</td>
                                <td>{{ $session->title }}</td>
                                <td>
                                    {{ $session->subject->name }}
                                    <br><small class="text-muted">Kelas: {{ $session->target_kelas ?? 'Semua' }}</small>
                                </td>
                                <td>{{ $session->end_time->format('d M Y H:i') }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $session->attempts_count }} Peserta</span>
                                </td>
                                <td>
                                    <a href="{{ route($baseRoute . '.show', $session->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada sesi ujian yang memiliki peserta.</td>
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
