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
                    <a href="{{ route($baseRoute . '.index') }}" class="btn btn-default btn-sm">Kembali</a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <form action="{{ route($baseRoute . '.show', $session->id) }}" method="GET" class="form-inline">
                            <label class="mr-2">Tampilkan:</label>
                            <select name="limit" class="form-control form-control-sm mr-3" onchange="this.form.submit()">
                                <option value="10" {{ request('limit') == 10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ request('limit', 20) == 20 ? 'selected' : '' }}>20</option>
                                <option value="50" {{ request('limit') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('limit') == 100 ? 'selected' : '' }}>100</option>
                            </select>

                            <label class="mr-2">Urutkan:</label>
                            <select name="sort" class="form-control form-control-sm mr-3" onchange="this.form.submit()">
                                <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="highest" {{ request('sort') == 'highest' ? 'selected' : '' }}>Nilai Terbesar</option>
                                <option value="lowest" {{ request('sort') == 'lowest' ? 'selected' : '' }}>Nilai Terkecil</option>
                            </select>
                        </form>
                    </div>
                </div>

                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Nama Peserta</th>
                            <th>Status Ujian</th>
                            <th>Nilai Saat Ini</th>
                            <th>Waktu Update Terakhir</th>
                            <th style="width: 100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attempts as $attempt)
                            <tr>
                                <td>{{ $loop->iteration + $attempts->firstItem() - 1 }}</td>
                                <td>{{ $attempt->student->name }}</td>
                                <td>
                                    @if($attempt->status == 'completed')
                                        <span class="badge badge-success">Selesai</span>
                                    @else
                                        <span class="badge badge-warning">Berlangsung</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-success" style="font-size: 14px">{{ number_format($attempt->score, 2) }}</span>
                                </td>
                                <td>{{ $attempt->updated_at->format('d M Y H:i') }}</td>
                                <td>
                                    <a href="{{ route($baseRoute . '.edit', $attempt->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Koreksi
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada peserta yang mengikuti ujian ini.</td>
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
