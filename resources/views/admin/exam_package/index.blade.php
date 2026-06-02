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
                    <a href="{{ route($baseRoute . '.create') }}" class="btn btn-primary btn-sm">
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
                                <td>
                                    <strong>{{ $package->questions_count }}</strong> / {{ $package->available_questions_count }} Soal
                                    @if($package->questions_count < $package->available_questions_count)
                                        <div class="mt-1">
                                            <button type="button" class="btn btn-xs btn-outline-success" data-toggle="modal" data-target="#syncModal{{ $package->id }}" title="Sinkronkan soal dari Bank Soal">
                                                <i class="fas fa-sync-alt"></i> Tarik Soal
                                            </button>

                                            <!-- Modal Sync By Tag -->
                                            <div class="modal fade" id="syncModal{{ $package->id }}" tabindex="-1" role="dialog" aria-labelledby="syncModalLabel{{ $package->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <form action="{{ route('admin.exam_package.sync_all', $package->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header bg-success text-white">
                                                                <h5 class="modal-title" id="syncModalLabel{{ $package->id }}">
                                                                    <i class="fas fa-cloud-download-alt mr-2"></i> Tarik Soal ke Paket
                                                                </h5>
                                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body text-left">
                                                                <p>Tarik soal dari Bank Soal untuk mata pelajaran <strong>{{ $package->subject->name }}</strong>.</p>
                                                                <div class="form-group">
                                                                    <label for="tag{{ $package->id }}" class="font-weight-bold">Berdasarkan Tag / Label (Opsional)</label>
                                                                    <input type="text" name="tag" id="tag{{ $package->id }}" class="form-control" placeholder="Contoh: Kelas 7">
                                                                    <small class="text-muted mt-1 d-block">
                                                                        Ketikan nama tag jika hanya ingin menarik soal tertentu.<br>
                                                                        <strong>Biarkan KOSONG</strong> jika ingin menarik <strong>SEMUA</strong> soal ke paket ini.
                                                                    </small>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-success rounded-pill px-4"><i class="fas fa-sync-alt mr-1"></i> Mulai Tarik Soal</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route($baseRoute . '.destroy', $package->id) }}" method="POST" onsubmit="return confirm('Hapus paket ini?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <a href="{{ route($baseRoute . '.preview', $package->id) }}" class="btn btn-default btn-xs" title="Preview Soal">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route($baseRoute . '.show', $package->id) }}" class="btn btn-info btn-xs" title="Kelola Soal">
                                            <i class="fas fa-list"></i> Kelola
                                        </a>
                                        <a href="{{ route($baseRoute . '.edit', $package->id) }}" class="btn btn-warning btn-xs" title="Edit Info">
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
