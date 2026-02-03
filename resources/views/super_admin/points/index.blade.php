@extends('layouts.admin.app')

@section('title', 'Verifikasi Poin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Verifikasi Topup Poin</h1>
                <p class="text-muted small">Kelola permintaan topup poin dari lembaga.</p>
            </div>
            <div class="col-sm-6">
                <div class="float-sm-right">
                    <div class="card shadow-sm border-0 mb-0" style="min-width: 200px;">
                        <div class="card-body p-2 d-flex align-items-center">
                            <div class="bg-warning text-white rounded p-2 mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-clock fa-lg"></i>
                            </div>
                            <div>
                                <small class="text-secondary font-weight-bold display-block">Pending Request</small>
                                <h5 class="mb-0 font-weight-bold text-dark">{{ $transactions->count() }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list text-primary mr-2"></i> Daftar Pending Request
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 pl-4" style="font-size: 0.75rem;">No</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7" style="font-size: 0.75rem;">Lembaga / User</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7" style="font-size: 0.75rem;">Jumlah Poin</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7" style="font-size: 0.75rem;">Total Bayar</th>
                                <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7" style="font-size: 0.75rem;">Bukti</th>
                                <th class="text-right text-uppercase text-secondary text-xs font-weight-bolder opacity-7 pr-4" style="font-size: 0.75rem;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $tx)
                            <tr>
                                <td class="pl-4 align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle">
                                    <div class="d-flex flex-column">
                                        <span class="font-weight-bold text-dark mb-0">
                                            {{ $tx->user->institution ? $tx->user->institution->name : 'Belum set lembaga' }}
                                        </span>
                                        <small class="text-muted">{{ $tx->user->name }}</small>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-light text-primary border border-primary px-3 py-2 rounded-pill">
                                        {{ number_format($tx->amount) }} Poin
                                    </span>
                                </td>
                                <td class="align-middle font-weight-bold">
                                    Rp {{ number_format($tx->amount * \App\Models\Setting::getValue('point_price', 675)) }}
                                </td>
                                <td class="align-middle text-center">
                                    @if($tx->reference_id && (filter_var($tx->reference_id, FILTER_VALIDATE_URL) || \Illuminate\Support\Facades\Storage::disk('public')->exists($tx->reference_id)))
                                        @php
                                            $proofUrl = filter_var($tx->reference_id, FILTER_VALIDATE_URL) ? $tx->reference_id : \Illuminate\Support\Facades\Storage::url($tx->reference_id);
                                        @endphp
                                        <button type="button" 
                                                onclick="showProof('{{ $proofUrl }}')"
                                                class="btn btn-outline-info btn-sm rounded-pill px-3">
                                            <i class="fas fa-eye mr-1"></i> Lihat Bukti
                                        </button>
                                    @else
                                        <span class="text-muted small font-italic">Tidak ada bukti ({{ $tx->reference_id ?? 'NULL' }})</span>
                                    @endif
                                </td>
                                <td class="align-middle text-right pr-4">
                                    <form action="{{ route('admin.super.points.approve', $tx->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Yakin setujui topup ini?');">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm btn-icon-split shadow-sm">
                                            <span class="icon text-white-50">
                                                <i class="fas fa-check"></i>
                                            </span>
                                            <span class="text">Approve</span>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.super.points.reject', $tx->id) }}" method="POST" class="d-inline-block ml-1" onsubmit="return confirm('Tolak topup ini? Pengguna akan diberitahu.');">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm btn-icon-split shadow-sm">
                                            <span class="icon text-white-50">
                                                <i class="fas fa-times"></i>
                                            </span>
                                            <span class="text">Reject</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-check-circle fa-2x text-muted"></i>
                                        </div>
                                        <h6 class="font-weight-bold text-dark mb-1">Tidak ada request pending</h6>
                                        <p class="text-muted small mb-0">Semua permintaan topup telah diproses.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($transactions->hasPages())
                <div class="card-footer bg-white py-3">
                    {{ $transactions->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
            <div class="modal-header border-0 bg-light pb-0">
                <h5 class="modal-title font-weight-bold pl-2" id="exampleModalLabel">Bukti Transfer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-0 bg-light">
                <div class="d-flex justify-content-center align-items-center" style="min-height: 200px; background-color: #f8f9fa;">
                    <img id="modalImage" src="" class="img-fluid" style="max-height: 80vh;" alt="Bukti Transfer">
                </div>
            </div>
            <div class="modal-footer border-0 bg-white justify-content-center">
                <button type="button" class="btn btn-secondary px-5 rounded-pill" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showProof(url) {
        $('#modalImage').attr('src', url);
        $('#imageModal').modal('show');
    }
</script>
@endpush
@endsection
