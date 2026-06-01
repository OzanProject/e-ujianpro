@extends('layouts.admin.app')

@section('title', 'Cetak Berita Acara')
@section('page_title', 'Berita Acara Pelaksanaan Ujian')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-white">
                <h5 class="mb-0 font-weight-bold">Filter Cetak</h5>
            </div>
            <div class="card-body">
                <form action="{{ route($baseRoute . '.berita_acara.print') }}" method="GET" target="_blank">
                    <div class="form-group">
                        <label class="font-weight-bold">Tanggal Ujian (Cetak Massal Opsional)</label>
                        <input type="date" name="exam_date" class="form-control">
                        <small class="text-muted">Isi tanggal ini jika ingin mencetak untuk <strong>semua sesi</strong> pada tanggal tersebut sekaligus.</small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">ATAU Pilih 1 Jadwal Ujian (Sesi)</label>
                        <select name="exam_session_id" class="form-control">
                            <option value="">-- Pilih Jadwal --</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->id }}">{{ \Carbon\Carbon::parse($session->start_time)->format('d/m/Y H:i') }} - {{ $session->subject->name ?? 'Mata Pelajaran' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Ruang Ujian</label>
                        <select name="exam_room_id" class="form-control" required>
                            <option value="all">-- Semua Ruangan --</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-pill font-weight-bold shadow-sm px-4 btn-print-with-date">
                        <i class="fas fa-print mr-1"></i> Cetak Berita Acara
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
         <div class="alert alert-info shadow-sm border-0 rounded-lg">
             <h5 class="font-weight-bold"><i class="fas fa-info-circle mr-2"></i> Petunjuk</h5>
             <p class="mb-0 text-sm">
                 Berita Acara Pelaksanaan Ujian digunakan untuk mencatat jalannya ujian, meliputi jumlah peserta yang hadir, tidak hadir, serta catatan kejadian penting selama ujian berlangsung. Dokumen ini wajib ditandatangani oleh pengawas ruang dan kepala sekolah.
             </p>
         </div>
    </div>
</div>
@endsection
