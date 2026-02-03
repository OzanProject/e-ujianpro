@extends('layouts.admin.app')

@section('title', 'Cetak Daftar Hadir')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Cetak Daftar Hadir</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Laporan</li>
                    <li class="breadcrumb-item active">Cetak Daftar Hadir</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Filter Cetak</h3>
                    </div>
                    <form action="{{ route('admin.report.attendance.print') }}" method="GET" target="_blank">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Pilih Ruangan Ujian <span class="text-danger">*</span></label>
                                <select name="exam_room_id" class="form-control select2" required>
                                    <option value="" disabled selected>-- Pilih Ruangan --</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                                    @endforeach
                                    <option value="null">-- Belum Ada Ruangan --</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Pilih Jadwal Ujian (Opsional)</label>
                                <select name="exam_session_id" class="form-control select2">
                                    <option value="">-- Kosongkan (Isi Manual Nanti) --</option>
                                    @foreach($sessions as $session)
                                        <option value="{{ $session->id }}">
                                            {{ $session->subject->name }} - {{ $session->start_time->format('d M Y H:i') }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih jadwal untuk otomatis mengisi Kop Surat (Mata Pelajaran, Waktu).</small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-print"></i> Cetak Daftar Hadir</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
