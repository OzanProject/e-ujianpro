@extends('layouts.admin.app')

@section('title', 'Pembayaran & Konfirmasi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pembayaran</h1>
    </div>

    <div class="row">
        {{-- Payment Instructions --}}
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Instruksi Pembayaran</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h4 class="text-gray-900 mb-2">Total Tagihan</h4>
                        <h1 class="display-4 font-weight-bold text-primary">Rp. {{ number_format($transaction->amount * $pointPrice, 0, ',', '.') }}</h1>
                        <p class="text-muted">Untuk pembelian {{ $transaction->amount }} Poin</p>
                    </div>

                    <div class="alert alert-warning" role="alert">
                        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Penting!</h5>
                        <p class="mb-0">Silakan transfer sesuai nominal di atas ke salah satu rekening di bawah ini.</p>
                    </div>

                    <ul class="list-group mb-3">
                        @forelse($bankAccounts as $bank)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="my-0 font-weight-bold">{{ $bank['bank'] }}</h6>
                                <small class="text-muted">a.n {{ $bank['name'] }}</small>
                            </div>
                            <span class="h5 font-weight-bold">{{ $bank['number'] }}</span>
                        </li>
                        @empty
                        <li class="list-group-item text-center">Belum ada rekening tersedia.</li>
                        @endforelse
                    </ul>

                    <p class="small text-muted text-center">Simpan bukti transfer Anda untuk diupload pada form konfirmasi di samping.</p>
                </div>
            </div>
        </div>

        {{-- Upload Proof --}}
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Konfirmasi Pembayaran</h6>
                </div>
                <div class="card-body">
                    @if($transaction->reference_id)
                         <div class="alert alert-info">
                            Bukti pembayaran sudah diupload. Menunggu verifikasi admin.
                        </div>
                        <div class="text-center mb-3">
                             <img src="{{ Storage::url($transaction->reference_id) }}" class="img-fluid img-thumbnail" style="max-height: 200px;">
                        </div>
                    @endif

                    <form action="{{ route('admin.point.payment.store', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Upload Bukti Transfer (Foto/Screenshot)</label>
                            <input type="file" name="proof" class="form-control-file @error('proof') is-invalid @enderror" required accept="image/*">
                            @error('proof')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Format: JPG, PNG. Maksimal 2MB.</small>
                        </div>

                        <button type="submit" class="btn btn-success btn-block btn-lg">
                            <i class="fas fa-upload"></i> Kirim Konfirmasi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
