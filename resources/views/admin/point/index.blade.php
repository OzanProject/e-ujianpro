@extends('layouts.admin.app')

@section('title', 'Dompet Poin')

@section('content')
<div class="container-fluid">
    @php
        /** @var \App\Models\User $user */
        $user = auth()->user();
    @endphp

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Dompet Sekolah</h1>
            <p class="mb-0 text-muted">Kelola saldo poin untuk menambah kuota siswa.</p>
        </div>
        <a href="{{ route('admin.point.topup') }}" class="btn btn-primary btn-icon-split shadow-sm">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Topup Poin</span>
        </a>
    </div>

    <div class="row">
        <!-- Balance Card (Digital Wallet Style) -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-lg text-white" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border-radius: 1rem; overflow: hidden;">
                <div class="card-body p-4 position-relative">
                    <!-- Decorative Circles -->
                    <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                    <div style="position: absolute; bottom: -40px; left: -10px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                    
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <div class="text-xs text-uppercase text-white-50 font-weight-bold mb-1">Saldo Tersedia</div>
                            <div class="h2 mb-0 font-weight-bold">{{ number_format($user->points_balance) }} POIN</div>
                        </div>
                        <i class="fas fa-wallet fa-2x text-white-50"></i>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between mt-4">
                        <small class="text-white-50">{{ $user->institution->name ?? 'Sekolah' }}</small>
                        <span class="badge badge-light text-primary px-3 py-1" style="border-radius: 20px;">Active</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="col-xl-8 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 1rem;">
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    <h5 class="font-weight-bold text-gray-800 mb-3">Apa itu Poin?</h5>
                    <p class="text-muted mb-0">
                        Poin digunakan untuk menambah kuota siswa ("Create Siswa" atau "Import Excel"). 
                        <br>
                        <strong>1 Poin = 1 Kuota Siswa</strong>.
                        <br>
                        Topup poin sekarang untuk menambah kapasitas ujian di sekolah Anda.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="card border-0 shadow mb-4" style="border-radius: 1rem;">
        <div class="card-header py-3 bg-white border-bottom-0 d-flex justify-content-between align-items-center" style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Transaksi</h6>
            <i class="fas fa-history text-gray-400"></i>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-gray-600">
                        <tr>
                            <th class="py-3 pl-4 border-0" style="width: 5%;">#</th>
                            <th class="py-3 border-0" style="width: 20%;">Tanggal</th>
                            <th class="py-3 border-0">Keterangan</th>
                            <th class="py-3 border-0 text-center">Tipe</th>
                            <th class="py-3 border-0 text-right">Nominal</th>
                            <th class="py-3 pr-4 border-0 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                        <tr>
                            <td class="pl-4 py-3">{{ $loop->iteration }}</td>
                            <td class="py-3">
                                <span class="d-block font-weight-bold text-gray-800">{{ $tx->created_at->format('d M Y') }}</span>
                                <small class="text-muted">{{ $tx->created_at->format('H:i') }} WIB</small>
                            </td>
                            <td class="py-3">
                                <span class="d-block text-gray-800">{{ $tx->description }}</span>
                                <small class="text-muted text-xs">Ref: {{ $tx->reference_id }}</small>
                            </td>
                            <td class="text-center py-3">
                                @if($tx->type == 'in')
                                    <span class="badge badge-soft-success text-success px-2 py-1">Masuk</span>
                                @else
                                    <span class="badge badge-soft-danger text-danger px-2 py-1">Keluar</span>
                                @endif
                            </td>
                            <td class="text-right py-3 font-weight-bold {{ $tx->type == 'in' ? 'text-success' : 'text-danger' }}">
                                {{ $tx->type == 'in' ? '+' : '-' }} {{ number_format($tx->amount) }}
                            </td>
                            <td class="pr-4 text-center py-3">
                                @if($tx->status == 'approved' || $tx->status == 'success')
                                    <span class="badge badge-success" style="border-radius: 20px;"><i class="fas fa-check mr-1"></i> Berhasil</span>
                                @elseif($tx->status == 'pending')
                                    <span class="badge badge-warning" style="border-radius: 20px;"><i class="fas fa-clock mr-1"></i> Menunggu</span>
                                @else
                                    <span class="badge badge-danger" style="border-radius: 20px;"><i class="fas fa-times mr-1"></i> Gagal</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" alt="Empty" style="width: 150px; opacity: 0.5;">
                                <p class="mt-3 mb-0">Belum ada riwayat transaksi.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-top">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>

</div>

<style>
    .badge-soft-success {
        background-color: #d1e7dd;
        color: #0f5132;
    }
    .badge-soft-danger {
        background-color: #f8d7da;
        color: #842029;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fc;
    }
</style>
@endsection
