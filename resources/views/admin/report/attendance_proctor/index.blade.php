@extends('layouts.admin.app')

@section('title', 'Cetak Absen Pengawas')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Cetak Absen Pengawas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Laporan</li>
                    <li class="breadcrumb-item active">Cetak Absen Pengawas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Filter Cetak</h3>
                    </div>
                    <form action="{{ route('admin.report.attendance.print') }}" method="GET" target="_blank">
                        <!-- Type Proctor triggers the specific print method -->
                        <input type="hidden" name="type" value="proctor">
                        
                        <div class="card-body">
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
                                <small class="text-muted">Pilih jadwal untuk otomatis mengisi Kop Surat (Info Mata Pelajaran & Waktu).</small>
                            </div>
                            
                            <div class="alert alert-light border mt-3">
                                <i class="fas fa-info-circle mr-1"></i> Informasi
                                <p class="mb-0 mt-1 small">
                                    Sistem akan mencetak daftar hadir untuk <b>seluruh pengawas</b> yang terdaftar pada akun Admin ini.
                                </p>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-info"><i class="fas fa-print mr-1"></i> Cetak Daftar Hadir</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
