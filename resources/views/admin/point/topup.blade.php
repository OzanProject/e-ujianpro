@extends('layouts.admin.app')

@section('title', 'Topup Poin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Paket Poin <small class="text-muted" style="font-size: 0.6em;">Ekonomis</small></h1>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    {{-- Info Alert --}}
    <div class="alert alert-info">
        <strong>Pemberitahuan.</strong> Silakan pilih paket poin yang tersedia di bawah ini. Poin digunakan untuk menyelenggarakan ujian.
    </div>

    {{-- Pricing Tables --}}
    <style>
        .pricing-table {
            border: 1px solid #eee;
            padding: 15px;
            min-height: 280px;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            text-align: center;
            position: relative;
            margin-bottom: 20px;
            transition: transform 0.2s;
        }
        .pricing-table:hover {
            transform: translateY(-5px);
        }
        .pt-title {
            border-bottom: 1px solid #cecece;
            padding-bottom: 10px;
            font-weight: bold;
            font-size: 1.1rem;
            color: #4e73df;
        }
        .pt-body {
            margin-top: 20px;
            font-size: 30px;
            font-weight: 800;
            color: #1cc88a;
        }
        .pt-poin-text {
            font-size: 0.5em;
            color: #858796;
            font-weight: 600;
        }
        .pt-price {
            margin-top: 10px;
            font-size: 18px;
            color: #5a5c69;
        }
        .btn-choose-poin {
            margin-top: 20px;
            width: 100%;
        }
    </style>

    <div class="row">
        @foreach($packages as $pkg)
        <div class="col-md-2 col-6">
            <div class="pricing-table">
                <div class="pt-title">{{ $pkg['name'] }}</div>
                <div class="pt-body">
                    {{ number_format($pkg['points'], 0, ',', '.') }} <span class="pt-poin-text">POIN</span>
                </div>
                <div class="pt-price">
                    Rp. <b>{{ number_format($pkg['price'], 0, ',', '.') }}</b>
                </div>
                <a href="{{ route('admin.point.checkout', ['amount' => $pkg['points']]) }}" class="btn btn-primary btn-choose-poin">
                    Pilih
                </a>
            </div>
        </div>
        @endforeach

        {{-- Custom Package --}}
        <div class="col-md-2 col-6">
            <div class="pricing-table">
                <div class="pt-title">Paket Custom</div>
                <div class="pt-body text-info" style="font-size: 1rem; margin-top: 35px;">
                    <input type="number" id="custom_poin" class="form-control text-center" placeholder="Jml Poin">
                </div>
                <div class="pt-price" style="font-size: 0.8rem; margin-top: 15px;">
                    <span id="custom_price_display">Min. 20 Poin</span>
                </div>
                <button type="button" class="btn btn-primary btn-choose-poin" onclick="chooseCustom()">
                    Pilih
                </button>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <h4 class="h5 text-gray-800">Riwayat Pembelian</h4>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Deskripsi</th>
                                <th>Jumlah Poin</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Fetch transactions for current user context
                                $txs =  Auth::user()->pointTransactions()->latest()->take(5)->get();
                            @endphp
                            @forelse($txs as $tx)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $tx->created_at->format('d M Y H:i') }}</td>
                                <td>{{ $tx->description }}</td>
                                <td>{{ number_format($tx->amount) }}</td>
                                <td>
                                    @if($tx->status == 'approved')
                                        <span class="badge badge-success">Berhasil</span>
                                    @elseif($tx->status == 'pending')
                                        <span class="badge badge-warning">Menunggu Verifikasi</span>
                                    @else
                                        <span class="badge badge-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    @if($tx->status == 'pending')
                                        <a href="{{ route('admin.point.payment', $tx->id) }}" class="btn btn-sm btn-info">Bayar / Upload</a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada riwayat transaksi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function chooseCustom() {
        var amount = document.getElementById('custom_poin').value;
        if(amount < 20) {
            alert('Minimal pembelian adalah 20 Poin.');
            return;
        }
        window.location.href = "{{ route('admin.point.checkout') }}?amount=" + amount;
    }
</script>
@endsection
