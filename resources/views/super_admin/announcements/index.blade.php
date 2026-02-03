@extends('layouts.admin.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kelola Pengumuman Sistem</h1>
        <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#createModal">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Pengumuman
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengumuman</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Judul</th>
                            <th width="40%">Isi Konten</th>
                            <th width="10%">Tipe</th>
                            <th width="10%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcements as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $item->title }}</td>
                            <td>{{ Str::limit(strip_tags($item->content), 80) }}</td>
                            <td class="text-center">
                                @if($item->type == 'info') <span class="badge badge-info">Info</span>
                                @elseif($item->type == 'warning') <span class="badge badge-warning">Warning</span>
                                @elseif($item->type == 'danger') <span class="badge badge-danger">Danger</span>
                                @elseif($item->type == 'success') <span class="badge badge-success">Success</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.super.announcements.toggle', $item->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $item->is_active ? 'btn-success' : 'btn-secondary' }}">
                                        {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#editModal{{ $item->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.super.announcements.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengumuman ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Pengumuman</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.super.announcements.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body text-left">
                                            <div class="form-group">
                                                <label>Judul</label>
                                                <input type="text" name="title" class="form-control" value="{{ $item->title }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Tipe Alert</label>
                                                <select name="type" class="form-control">
                                                    <option value="info" {{ $item->type == 'info' ? 'selected' : '' }}>Info (Biru)</option>
                                                    <option value="success" {{ $item->type == 'success' ? 'selected' : '' }}>Success (Hijau)</option>
                                                    <option value="warning" {{ $item->type == 'warning' ? 'selected' : '' }}>Warning (Kuning)</option>
                                                    <option value="danger" {{ $item->type == 'danger' ? 'selected' : '' }}>Danger (Merah)</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Konten</label>
                                                <textarea name="content" class="form-control summernote" rows="5" required>{{ $item->content }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada pengumuman.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $announcements->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Pengumuman Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.super.announcements.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Judul</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Pemeliharaan Sistem" required>
                    </div>
                    <div class="form-group">
                        <label>Tipe Alert</label>
                        <select name="type" class="form-control">
                            <option value="info">Info (Biru)</option>
                            <option value="success">Success (Hijau)</option>
                            <option value="warning">Warning (Kuning)</option>
                            <option value="danger">Danger (Merah)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Konten</label>
                        <textarea name="content" class="form-control summernote" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
    $(document).ready(function() {
        $('.summernote').summernote({
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });
    });
</script>
@endpush
