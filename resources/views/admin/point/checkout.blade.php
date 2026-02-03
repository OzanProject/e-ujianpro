@extends('layouts.admin.app')

@section('title', 'Pilih Paket Poin')

@section('content')
<div class="container-fluid">
    {{-- Page Heading --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pembelian Paket Poin</h1>
        <a href="{{ route('admin.point.topup') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Batalkan
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle"></i> Informasi
                        <br>
                        Anda akan membeli paket poin <strong>{{ $packageName }}</strong>. Klik <strong>Checkout</strong> apabila sudah yakin.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.point.checkout.store') }}" class="form-horizontal">
                        @csrf
                        <input type="hidden" name="amount" value="{{ $amount }}">
                        <input type="hidden" name="package_name" value="{{ $packageName }}">

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Paket Poin yang Dipilih</label>
                            <div class="col-sm-9">
                                <input type="text" readonly class="form-control-plaintext" value="{{ $packageName }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Poin yang Didapatkan</label>
                            <div class="col-sm-9">
                                <input type="text" readonly class="form-control-plaintext" value="{{ number_format($amount) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Harga Paket</label>
                            <div class="col-sm-9">
                                <span class="form-control-plaintext text-primary font-weight-bold">Rp. {{ number_format($totalPrice, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary btn-lg px-4">
                                    <i class="fas fa-shopping-cart"></i> Checkout
                                </button>
                                <a href="{{ route('admin.point.topup') }}" class="btn btn-default btn-lg">Batalkan</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
