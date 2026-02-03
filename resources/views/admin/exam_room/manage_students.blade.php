@extends('layouts.admin.app')

@section('title', 'Atur Peserta Ruangan')
@section('page_title', 'Atur Peserta Ruangan')

@section('content')
<div class="row">
    <!-- Auto Assign Card -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0 font-weight-bold"><i class="fas fa-magic mr-2"></i> Isi Otomatis</h5>
            </div>
            <div class="card-body">
                <p class="text-sm text-gray-600 mb-3">
                    Isi ruangan ini dengan mengambil siswa secara acak yang <strong>belum memiliki ruangan</strong>.
                </p>
                
                <div class="alert alert-info border-0 shadow-sm rounded-lg mb-3">
                    <i class="fas fa-info-circle mr-1"></i> Tersedia: <strong>{{ $availableCount }}</strong> Siswa
                </div>

                <form action="{{ route('admin.exam_room.assign_random', $room->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="font-weight-bold">Jumlah Siswa</label>
                        <input type="number" name="count" class="form-control" min="1" max="{{ $availableCount > 0 ? $availableCount : 1 }}" value="20" required {{ $availableCount == 0 ? 'disabled' : '' }}>
                        <small class="text-muted">Maksimal: {{ $availableCount }}</small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block rounded-pill font-weight-bold shadow-sm" {{ $availableCount == 0 ? 'disabled' : '' }}>
                        <i class="fas fa-random mr-1"></i> Ambil Acak
                    </button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-lg mt-3">
             <div class="card-header bg-white">
                <h5 class="mb-0 font-weight-bold">Info Ruangan</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-gray-600">Nama Ruangan</td>
                        <td class="font-weight-bold text-right">{{ $room->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-gray-600">Total Peserta</td>
                        <td class="font-weight-bold text-right">{{ $students->total() }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <a href="{{ route('admin.exam_room.index') }}" class="btn btn-default btn-block mt-3 rounded-pill text-gray-600">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Data Ruangan
        </a>
    </div>

    <!-- Student List Card -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                 <div class="d-flex align-items-center">
                     <h5 class="mb-0 font-weight-bold text-gray-800 mr-3">Daftar Peserta</h5>
                     <span class="badge badge-primary px-3 py-1 rounded-pill mr-2">{{ $students->total() }} Total</span>
                     <button type="button" class="btn btn-danger btn-sm rounded-pill font-weight-bold shadow-sm" onclick="if(confirm('Yakin ingin mengeluarkan siswa terpilih?')) { document.getElementById('bulkDeleteForm').submit(); }">
                        <i class="fas fa-trash-alt mr-1"></i> Hapus Terpilih
                     </button>
                 </div>
                 <form method="GET" class="form-inline">
                     <select name="per_page" class="custom-select custom-select-sm border-gray-300 rounded-pill shadow-sm text-gray-700 font-weight-bold" onchange="this.form.submit()">
                         <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 baris</option>
                         <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 baris</option>
                         <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                         <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris</option>
                     </select>
                 </form>
            </div>
            <div class="card-body p-0">
                <form action="{{ route('admin.exam_room.bulk_remove', $room->id) }}" method="POST" id="bulkDeleteForm" onsubmit="return confirm('Yakin ingin mengeluarkan siswa terpilih?');">
                    @csrf
                    @method('DELETE')
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 border-0" style="width: 50px;">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="checkAll">
                                            <label class="custom-control-label" for="checkAll"></label>
                                        </div>
                                    </th>
                                    <th class="px-4 py-3 border-0">No</th>
                                    <th class="px-4 py-3 border-0">Nama Siswa</th>
                                    <th class="px-4 py-3 border-0">NIS / Username</th>
                                    <th class="px-4 py-3 border-0">Kelompok</th>
                                    <th class="px-4 py-3 border-0 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr>
                                        <td class="px-4 py-3 align-middle text-center">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input student-checkbox" name="student_ids[]" value="{{ $student->id }}" id="check{{ $student->id }}">
                                                <label class="custom-control-label" for="check{{ $student->id }}"></label>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 align-middle text-gray-500">{{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}</td>
                                        <td class="px-4 py-3 align-middle font-weight-bold text-dark">{{ $student->name }}</td>
                                        <td class="px-4 py-3 align-middle">{{ $student->nis }}</td>
                                        <td class="px-4 py-3 align-middle">
                                            @if($student->group)
                                                <span class="badge badge-info bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ $student->group->name }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 align-middle text-center">
                                                <button type="button" class="btn btn-danger btn-sm btn-icon rounded-circle shadow-sm" title="Keluarkan" onclick="if(confirm('Keluarkan siswa ini?')) { document.getElementById('delete-single-{{ $student->id }}').submit(); }">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486831.png" alt="Empty" width="60" class="opacity-50 mb-3">
                                                <h6 class="text-muted font-weight-bold">Ruangan ini masih kosong</h6>
                                                <p class="text-sm text-gray-500 mb-0">Gunakan fitur "Isi Otomatis" di samping.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
                
                @foreach($students as $student)
                    <form action="{{ route('admin.exam_room.remove_student', ['id' => $room->id, 'student_id' => $student->id]) }}" method="POST" id="delete-single-{{ $student->id }}" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach
                
                <script>
                    document.getElementById('checkAll').addEventListener('change', function() {
                        var checkboxes = document.querySelectorAll('.student-checkbox');
                        for (var i = 0; i < checkboxes.length; i++) {
                            checkboxes[i].checked = this.checked;
                        }
                    });
                </script>
            </div>
             <div class="card-footer bg-white border-top-0 d-flex justify-content-end">
                {{ $students->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
