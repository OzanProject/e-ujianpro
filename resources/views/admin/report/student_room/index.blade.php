@extends('layouts.admin.app')

@section('title', 'Cetak Daftar Peserta per Ruangan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Pilih Ruangan</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.report.student_room.print') }}" method="GET" target="_blank">
                        <div class="form-group">
                            <label for="exam_room_id">Ruangan</label>
                            <select name="exam_room_id" id="exam_room_id" class="form-control select2" required>
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }} ({{ $room->students_count ?? $room->students()->count() }} Peserta)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-print mr-1"></i> Buka Mode Cetak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">Informasi</h3>
                </div>
                <div class="card-body">
                    <p>Fitur ini digunakan untuk mencetak daftar seluruh peserta yang terdaftar pada suatu ruangan ujian.</p>
                    <ul>
                        <li>Pastikan Anda sudah mengalokasikan peserta ke ruangan masing-masing di menu <strong>Data Peserta</strong>.</li>
                        <li>Laporan ini menampilkan: Nama Peserta, NIS/NISN, dan Kelas.</li>
                        <li>Gunakan kertas A4 untuk hasil cetak maksimal.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4'
        });
    });
</script>
@endpush
