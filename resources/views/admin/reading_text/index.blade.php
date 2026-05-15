@extends('layouts.admin.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Bacaan / Wacana Soal</h3>
                <div class="card-tools">
                    <a href="{{ route($baseRoute . '.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Bacaan
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route($baseRoute . '.index') }}" method="GET" class="mb-4">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label for="subject_id">Mata Pelajaran</label>
                            <select name="subject_id" id="subject_id" class="form-control select2">
                                <option value="">-- Semua Mapel --</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="per_page">Tampilkan</label>
                            <select name="per_page" id="per_page" class="form-control">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 data</option>
                                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20 data</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 data</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 data</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-info btn-block">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route($baseRoute . '.index') }}" class="btn btn-default btn-block">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
                <form id="bulkDeleteForm" action="{{ route('admin.reading_text.delete_all') }}" method="POST">
                    @csrf
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 10px"><input type="checkbox" id="check-all"></th>
                                <th style="width: 10px">#</th>
                                <th>Mata Pelajaran</th>
                                <th>Judul</th>
                                <th>Jumlah Soal</th>
                                <th>Cuplikan Isi</th>
                                <th style="width: 150px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($readingTexts as $text)
                                <tr>
                                    <td class="text-center"><input type="checkbox" name="ids[]" value="{{ $text->id }}" class="item-check"></td>
                                    <td>{{ $loop->iteration + $readingTexts->firstItem() - 1 }}</td>
                                    <td>{{ $text->subject->name }}</td>
                                    <td>{{ $text->title }}</td>
                                    <td class="text-center">
                                        @if($text->questions_count > 0)
                                            <span class="badge badge-info">{{ $text->questions_count }} Soal</span>
                                        @else
                                            <span class="badge badge-warning">Belum Digunakan</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit(strip_tags($text->content), 100) }}</td>
                                    <td>
                                        <a href="{{ route($baseRoute . '.edit', $text->id) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i> Edit</a>
                                        <button type="button" class="btn btn-danger btn-xs" onclick="deleteSingle({{ $text->id }})"><i class="fas fa-trash"></i> Hapus</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center">Belum ada data bacaan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-danger" id="btn-bulk-delete" disabled onclick="return confirm('Apakah Anda yakin ingin menghapus semua bacaan yang dipilih?')">
                            <i class="fas fa-trash-alt mr-1"></i> Hapus Terpilih
                        </button>
                        <div>
                            {{ $readingTexts->links() }}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Hidden form for single delete --}}
<form id="singleDeleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const $checkAll = $('#check-all');
        const $itemChecks = $('.item-check');
        const $btnBulkDelete = $('#btn-bulk-delete');

        $checkAll.on('change', function() {
            $itemChecks.prop('checked', this.checked);
            toggleBulkButton();
        });

        $itemChecks.on('change', function() {
            $checkAll.prop('checked', $itemChecks.length === $('.item-check:checked').length);
            toggleBulkButton();
        });

        function toggleBulkButton() {
            $btnBulkDelete.prop('disabled', $('.item-check:checked').length === 0);
        }
    });

    function deleteSingle(id) {
        if (confirm('Apakah Anda yakin ingin menghapus bacaan ini?')) {
            const form = document.getElementById('singleDeleteForm');
            form.action = "{{ url('admin/reading_text') }}/" + id;
            form.submit();
        }
    }
</script>
@endpush
