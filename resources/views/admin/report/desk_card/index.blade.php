@extends('layouts.admin.app')

@section('title', 'Cetak Kartu Meja')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Cetak Kartu Meja</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Laporan</li>
                    <li class="breadcrumb-item active">Cetak Kartu Meja</li>
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
                    <form action="{{ route('admin.report.desk_card.print') }}" method="GET" target="_blank">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Pilih Ruangan Ujian</label>
                                <select name="exam_room_id" class="form-control select2" required>
                                    <option value="all">Semua Ruangan</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                                    @endforeach
                                    <option value="null">-- Belum Ada Ruangan --</option>
                                </select>
                            </div>
                            <div class="form-group mt-3">
                                <label>Opsi Cetak Foto</label>
                                <select name="show_photo" class="form-control">
                                    <option value="1">Tampilkan Foto Siswa</option>
                                    <option value="0">Sembunyikan Foto Siswa</option>
                                </select>
                            </div>
                            <div class="alert alert-info mt-3">
                                <i class="icon fas fa-info"></i> Kartu Meja akan dicetak dengan ukuran A4 (2 Kartu per halaman atau 4 Kartu per halaman tergantung setting printer).
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-print-with-date"><i class="fas fa-print"></i> Cetak Kartu Meja</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
