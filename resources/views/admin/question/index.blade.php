@extends('layouts.admin.app')

@section('title', 'Bank Soal')
@section('page_title', 'Bank Soal')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
             <div class="card-header bg-gradient-to-r from-blue-600 to-indigo-700 p-4 border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title text-white font-weight-bold mb-0">
                        <i class="fas fa-database mr-2"></i> Bank Soal
                    </h3>
                    <div class="card-tools">
                         <button type="button" class="btn btn-light text-green-700 font-weight-bold shadow-sm rounded-pill px-4 mr-2" data-toggle="modal" data-target="#importModal">
                            <i class="fas fa-file-excel mr-1"></i> Import Excel
                        </button>
                        <a href="{{ route('admin.question.create') }}" class="btn btn-light text-blue-700 font-weight-bold shadow-sm rounded-pill px-4">
                            <i class="fas fa-plus mr-1"></i> Buat Soal
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body bg-light">
                 {{-- Subject Filter --}}
                 <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-lg shadow-sm">
                    <form action="{{ route('admin.question.index') }}" method="GET" class="form-inline w-100">
                        <label class="mr-3 font-weight-bold text-gray-600"><i class="fas fa-filter mr-1"></i> Filter Mapel:</label>
                        <select name="subject_id" class="form-control form-control-sm mr-2 border-0 bg-light font-weight-bold text-dark w-50" style="border-radius: 8px;" onchange="this.form.submit()">
                            <option value="">-- Tampilkan Semua Mata Pelajaran --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        @if(request('subject_id'))
                             <a href="{{ route('admin.question.index') }}" class="btn btn-xs btn-outline-danger rounded-pill ml-2">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        @endif
                    </form>
                    <div class="text-muted text-sm font-weight-bold">
                        Total Soal: <span class="text-primary">{{ $questions->total() }}</span>
                    </div>
                 </div>

                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-lg mb-4">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger border-0 shadow-sm rounded-lg mb-4">
                        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                    </div>
                @endif

                <div class="table-responsive bg-white rounded-lg shadow-sm p-0">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-secondary text-xs font-weight-bold text-uppercase px-4 py-3 border-0">No</th>
                                <th class="text-secondary text-xs font-weight-bold text-uppercase px-4 py-3 border-0">Mata Pelajaran</th>
                                <th class="text-secondary text-xs font-weight-bold text-uppercase px-4 py-3 border-0">Konten Soal</th>
                                <th class="text-secondary text-xs font-weight-bold text-uppercase px-4 py-3 border-0">Tipe</th>
                                <th class="text-secondary text-xs font-weight-bold text-uppercase px-4 py-3 border-0">Kunci/Opsi</th>
                                <th class="text-secondary text-xs font-weight-bold text-uppercase px-4 py-3 border-0 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($questions as $question)
                                <tr class="border-bottom">
                                    <td class="px-4 py-3 align-middle text-gray-500 font-weight-bold">{{ $loop->iteration + $questions->firstItem() - 1 }}</td>
                                    <td class="px-4 py-3 align-middle">
                                        <span class="badge badge-light text-left border px-2 py-1" style="font-size: 0.85em;">
                                            <i class="fas fa-book mr-1 text-blue-500"></i> {{ $question->subject->name }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 align-middle text-sm text-gray-700">
                                         {{ Str::limit(strip_tags($question->content), 80) }}
                                    </td>
                                    <td class="px-4 py-3 align-middle">
                                        @if($question->type == 'multiple_choice')
                                            <span class="badge badge-primary bg-indigo-100 text-indigo-800 border border-indigo-200 px-2 py-1 rounded">Pilgan</span>
                                        @else
                                            <span class="badge badge-warning bg-orange-100 text-orange-800 border border-orange-200 px-2 py-1 rounded">Essay</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 align-middle">
                                        @if($question->type == 'multiple_choice')
                                            <div class="d-flex flex-column">
                                                <small class="text-muted">{{ $question->options->count() }} Pilihan</small>
                                                <span class="text-success font-weight-bold text-xs mt-1">
                                                    Kunci: {{ strip_tags($question->options->where('is_correct', true)->first()->content ?? '?') }}
                                                </span>
                                            </div>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 align-middle text-center">
                                        <form action="{{ route('admin.question.destroy', $question->id) }}" method="POST" onsubmit="return confirm('Hapus soal ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <div class="btn-group shadow-sm rounded-lg" role="group">
                                                <a href="{{ route('admin.question.edit', $question->id) }}" class="btn btn-default btn-sm border-gray-200 hover:bg-yellow-50 hover:text-yellow-600 transition" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="submit" class="btn btn-default btn-sm border-gray-200 hover:bg-red-50 hover:text-red-600 transition" title="Hapus">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486831.png" alt="Empty" width="80" class="opacity-50 mb-3">
                                            <h6 class="text-muted font-weight-bold">Belum ada soal</h6>
                                            <p class="text-gray-400 text-sm">Silakan pilih mata pelajaran atau buat soal baru.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 mt-3">
                    {{ $questions->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg overflow-hidden">
            <div class="modal-header bg-gradient-to-r from-green-600 to-teal-600 text-white border-0">
                <h5 class="modal-title font-weight-bold" id="importModalLabel">
                    <i class="fas fa-file-import mr-2"></i> Import Soal (Excel / Word)
                </h5>
                <button type="button" class="close text-white opacity-75 hover:opacity-100" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.question.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="alert alert-info border-0 shadow-sm rounded-lg d-flex align-items-start mb-4">
                        <i class="fas fa-info-circle text-lg mr-3 mt-1"></i>
                        <span class="text-sm">Gunakan template yang disediakan. Sistem mendukung format <strong>.xlsx</strong> (Excel) dan <strong>.docx</strong> (Word dengan Tabel).</span>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-gray-700">Mata Pelajaran Target</label>
                        <select name="subject_id" id="importSubjectId" class="form-control form-control-lg border-0 shadow-sm" required onchange="updateTemplateLink()">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                         <label class="font-weight-bold text-gray-700">File Excel (.xlsx) atau Word (.docx)</label>
                         <div class="custom-file">
                            <input type="file" name="file" class="custom-file-input" id="customFile" required accept=".xlsx, .xls, .docx">
                            <label class="custom-file-label border-0 shadow-sm" for="customFile">Pilih file...</label>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('admin.question.template') }}" id="downloadTemplateBtn" class="btn btn-outline-success btn-block rounded-pill font-weight-bold border-2 mb-2" target="_blank">
                             <i class="fas fa-file-excel mr-1"></i> Download Template Excel
                        </a>
                        <a href="{{ route('admin.question.template.word') }}" id="downloadTemplateWordBtn" class="btn btn-outline-primary btn-block rounded-pill font-weight-bold border-2" target="_blank">
                             <i class="fas fa-file-word mr-1"></i> Download Template Word
                        </a>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light text-gray-600 font-weight-bold rounded-pill px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success font-weight-bold shadow-sm rounded-pill px-4">
                        <i class="fas fa-upload mr-1"></i> Import Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Custom File Input Label
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });

    // Dynamic Template Link Update
    function updateTemplateLink() {
        var subjectId = document.getElementById('importSubjectId').value;
        var subjectName = document.getElementById('importSubjectId').options[document.getElementById('importSubjectId').selectedIndex].text;
        var baseUrl = "{{ route('admin.question.template') }}";
        var btn = document.getElementById('downloadTemplateBtn');
        
        if(subjectId) {
            btn.href = baseUrl + "?subject_id=" + subjectId;
            btn.innerHTML = '<i class="fas fa-download mr-1"></i> Download Template (Format Import Soal - ' + subjectName + '.xlsx)';
        } else {
            btn.href = baseUrl;
             btn.innerHTML = '<i class="fas fa-download mr-1"></i> Download Template (Format Import Soal - Umum.xlsx)';
        }
    }
</script>
@endpush
@endsection
