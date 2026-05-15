@extends('layouts.admin.app')

@section('title', 'Cetak Denah Ruang Ujian')
@section('page_title', 'Denah Tempat Duduk Peserta')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-white">
                <h5 class="mb-0 font-weight-bold">Pilih Ruangan</h5>
            </div>
            <div class="card-body">
                <form action="{{ route($baseRoute . '.denah_ruang.print') }}" method="GET" target="_blank">
                    <div class="form-group">
                        <label class="font-weight-bold">Ruang Ujian</label>
                        <select name="exam_room_id" class="form-control" required>
                            <option value="">-- Pilih Ruangan --</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Format Baris</label>
                        <select name="columns" class="form-control" required>
                            <option value="4">4 Kolom (Standar)</option>
                            <option value="5">5 Kolom</option>
                            <option value="3">3 Kolom</option>
                        </select>
                        <small class="text-muted">Pilih jumlah meja per baris mendatar</small>
                    </div>

                    <button type="submit" class="btn btn-success rounded-pill font-weight-bold shadow-sm px-4 mt-2">
                        <i class="fas fa-th mr-1"></i> Cetak Denah Ruang
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
         <div class="alert alert-info shadow-sm border-0 rounded-lg">
             <h5 class="font-weight-bold"><i class="fas fa-info-circle mr-2"></i> Petunjuk</h5>
             <p class="mb-0 text-sm">
                 Denah ruang ujian akan menampilkan tata letak tempat duduk peserta sesuai dengan ruangan yang telah dialokasikan pada pengaturan peserta. 
                 Anda dapat mengatur jumlah kolom (meja berderet ke samping) untuk menyesuaikan dengan kondisi fisik kelas sebenarnya.
             </p>
         </div>
    </div>
</div>
@endsection
