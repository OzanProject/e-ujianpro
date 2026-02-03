@extends('layouts.admin.app')

@section('title', 'Materi Belajar (Modul)')
@section('page_title', 'Bank Materi & Modul')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Upload Materi Baru</h3>
            </div>
            <div class="card-body">
                 @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 pl-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.learning_material.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Mata Pelajaran</label>
                        <select name="subject_id" class="form-control" required>
                            <option value="">-- Pilih Mapel --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Judul Materi</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Modul Bab 1" required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi (Opsional)</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="form-group">
                        <label>File (PDF, PPT, DOC)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="file" id="fileUpload" required>
                            <label class="custom-file-label" for="fileUpload">Pilih File...</label>
                        </div>
                        <small class="text-muted">Max: 10MB</small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-upload"></i> Upload Materi</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Materi Tersedia</h3>
                <div class="card-tools">
                    <form action="{{ route('admin.learning_material.index') }}" method="GET" class="input-group input-group-sm" style="width: 200px;">
                        <select name="subject_id" class="form-control float-right" onchange="this.form.submit()">
                            <option value="">Semua Mapel</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                @if(session('success'))
                    <div class="alert alert-success m-2">{{ session('success') }}</div>
                @endif

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Judul</th>
                            <th>Mapel</th>
                            <th>Tipe</th>
                            <th style="width: 100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materials as $material)
                            <tr>
                                <td>{{ $loop->iteration + $materials->firstItem() - 1 }}</td>
                                <td>
                                    <strong>{{ $material->title }}</strong><br>
                                    <small class="text-muted">{{ Str::limit(strip_tags($material->description), 50) }}</small>
                                </td>
                                <td>{{ $material->subject->name }}</td>
                                <td>
                                    <span class="badge badge-secondary">{{ strtoupper($material->file_type) }}</span>
                                </td>
                                <td>
                                    <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="btn btn-xs btn-info" title="Download/Lihat">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <form action="{{ route('admin.learning_material.destroy', $material->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus materi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada materi yang diupload.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3 px-3">
                    {{ $materials->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>
@endpush
