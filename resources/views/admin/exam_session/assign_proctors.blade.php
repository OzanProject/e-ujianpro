@extends('layouts.admin.app')

@section('title', 'Atur Pengawas Ujian')
@section('page_title', 'Atur Pengawas Ujian')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Atur Pengawas: {{ $examSession->title }}</h1>
        <a href="{{ route($baseRoute . '.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Penugasan Pengawas / Guru</h6>
        </div>
        <div class="card-body">
            <form action="{{ route($baseRoute . '.proctors.store', $examSession->id) }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered" id="proctorTable">
                        <thead>
                            <tr>
                                <th>Guru (Pengawas)</th>
                                <th>Ruangan Ujian</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="proctorBody">
                            @if(count($assignments) > 0)
                                @foreach($assignments as $index => $assignment)
                                    <tr>
                                        <td>
                                            <select name="assignments[{{$index}}][user_id]" class="form-control" required>
                                                <option value="">-- Pilih Guru --</option>
                                                @foreach($teachers as $teacher)
                                                    <option value="{{ $teacher->id }}" {{ $assignment->id == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="assignments[{{$index}}][exam_room_id]" class="form-control" required>
                                                <option value="">-- Pilih Ruangan --</option>
                                                @foreach($rooms as $room)
                                                    <option value="{{ $room->id }}" {{ $assignment->pivot->exam_room_id == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <!-- Baris kosong awal -->
                                <tr>
                                    <td>
                                        <select name="assignments[0][user_id]" class="form-control" required>
                                            <option value="">-- Pilih Guru --</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="assignments[0][exam_room_id]" class="form-control" required>
                                            <option value="">-- Pilih Ruangan --</option>
                                            @foreach($rooms as $room)
                                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
                <button type="button" class="btn btn-success btn-sm mb-3" id="addRow">
                    <i class="fas fa-plus"></i> Tambah Pengawas
                </button>

                <hr>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary">Simpan Penugasan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let rowCount = {{ count($assignments) > 0 ? count($assignments) : 1 }};
        const tbody = document.getElementById('proctorBody');
        const addBtn = document.getElementById('addRow');

        addBtn.addEventListener('click', function () {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <select name="assignments[${rowCount}][user_id]" class="form-control" required>
                        <option value="">-- Pilih Guru --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="assignments[${rowCount}][exam_room_id]" class="form-control" required>
                        <option value="">-- Pilih Ruangan --</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}">{{ $room->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
            rowCount++;
        });

        tbody.addEventListener('click', function (e) {
            if (e.target.closest('.remove-row')) {
                e.target.closest('tr').remove();
            }
        });
    });
</script>
@endsection
