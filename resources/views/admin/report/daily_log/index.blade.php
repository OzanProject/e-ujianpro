@extends('layouts.admin.app')

@section('title', 'Cetak Buku Catatan Harian')
@section('page_title', 'Buku Catatan Harian / Kejadian Penting')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-white">
                <h5 class="mb-0 font-weight-bold">Filter Cetak</h5>
            </div>
            <div class="card-body">
                <form action="{{ route($baseRoute . '.daily_log.print') }}" method="GET" target="_blank">
                    <div class="form-group">
                        <label class="font-weight-bold">Jadwal Ujian (Sesi)</label>
                        <select name="exam_session_id" class="form-control" required>
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

                    <button type="submit" class="btn btn-primary rounded-pill font-weight-bold shadow-sm px-4">
                        <i class="fas fa-print mr-1"></i> Cetak Format Buku Catatan
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
         <div class="alert alert-info shadow-sm border-0 rounded-lg">
             <h5 class="font-weight-bold"><i class="fas fa-info-circle mr-2"></i> Petunjuk</h5>
             <p class="mb-0 text-sm">
                 Buku Catatan Harian atau Kejadian Penting digunakan oleh Pengawas Ruang untuk mencatat setiap peristiwa atau kejadian khusus yang terjadi di dalam ruang ujian selama pelaksanaan berlangsung (misal: peserta sakit, pemadaman listrik, dsb).
             </p>
         </div>
    </div>
</div>
@endsection
