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
            <div class="card-body p-0">
                @if(session('success'))
                    <div class="alert alert-success m-3">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-nowrap mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 10px" class="text-center">#</th>
                                <th>Nama Paket</th>
                                <th>Kode</th>
                                <th>Mata Pelajaran</th>
                                <th class="text-center">Jumlah Soal</th>
                                <th class="text-center" style="width: 200px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($packages as $package)
                                <tr>
                                    <td class="text-center align-middle">{{ $loop->iteration + $packages->firstItem() - 1 }}</td>
                                    <td class="align-middle font-weight-bold">{{ $package->name }}</td>
                                    <td class="align-middle">{{ $package->code ?? '-' }}</td>
                                    <td class="align-middle">{{ $package->subject->name }}</td>
                                    <td class="align-middle text-center">
                                        <span class="badge badge-info px-2 py-1" style="font-size: 0.85rem;">
                                            {{ $package->questions_count }} / {{ $package->available_questions_count }} Soal
                                        </span>
                                        @if($package->questions_count < $package->available_questions_count)
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill" data-toggle="modal" data-target="#syncModal{{ $package->id }}" title="Sinkronkan soal dari Bank Soal">
                                                    <i class="fas fa-sync-alt mr-1"></i> Tarik Soal
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
                                                                <div class="modal-body text-left text-wrap">
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
                                    <td class="align-middle text-center">
                                        <form action="{{ route($baseRoute . '.destroy', $package->id) }}" method="POST" onsubmit="return confirm('Hapus paket ini?');" class="mb-0">
                                            @csrf
                                            @method('DELETE')
                                            <div class="btn-group shadow-sm">
                                                <a href="{{ route($baseRoute . '.preview', $package->id) }}" class="btn btn-default btn-sm" title="Preview Soal">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route($baseRoute . '.show', $package->id) }}" class="btn btn-info btn-sm" title="Kelola Soal">
                                                    <i class="fas fa-list mr-1"></i> Kelola
                                                </a>
                                                <a href="{{ route($baseRoute . '.edit', $package->id) }}" class="btn btn-warning btn-sm" title="Edit Info">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-folder-open fa-3x mb-3 text-gray-300"></i>
                                        <br>Belum ada paket soal.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white">
                    {{ $packages->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
