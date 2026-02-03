@extends('layouts.admin.app')

@section('title', 'Pengaturan Platform')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pengaturan Platform</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <form action="{{ route('admin.super.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            {{-- App Identity --}}
            <div class="col-md-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Identitas Aplikasi Platform</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Aplikasi Platform</label>
                                    <input type="text" name="app_name" class="form-control" value="{{ $appName }}" placeholder="Contoh: CBT PRO Platform">
                                    <small class="text-muted">Nama ini akan muncul di sidebar dan footer untuk Super Admin.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Logo Platform</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" name="app_logo" class="custom-file-input" id="inputLogo" onchange="previewImage(this, 'app_logo_preview')">
                                            <label class="custom-file-label" for="inputLogo">Pilih file logo...</label>
                                        </div>
                                    </div>
                                    <small class="text-muted">Format: JPG, PNG. Maks: 2MB.</small>
                                    
                                    <div class="mt-2 text-center p-2 bg-light rounded border">
                                        <img id="app_logo_preview" src="{{ $appLogo ? asset('storage/' . $appLogo) : asset('img/logo-placeholder.png') }}" alt="Logo Preview" style="height: 100px; max-width: 100%; object-fit: contain; {{ $appLogo ? '' : 'display:none;' }}">
                                        @if(!$appLogo) <p class="small text-muted mb-0 mt-1" id="preview_help_text">Preview akan muncul di sini</p> @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact & Legal --}}
            <div class="col-md-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Kontak & Legalitas (Landing Page)</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label>WhatsApp Admin / Sales</label>
                                    <input type="text" name="app_whatsapp" class="form-control" value="{{ $appWhatsapp }}" placeholder="628123456789">
                                    <small class="text-muted">Nomor WA untuk tombol 'Hubungi Sales' di halaman depan.</small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Konten Halaman Kontak (Hubungi Kami)</label>
                                    <textarea name="content_contact" class="form-control" rows="4" placeholder="Tulis alamat, email, atau info kontak lainnya...">{{ $contentContact }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Konten Kebijakan Privasi</label>
                                    <textarea name="content_privacy" class="form-control" rows="6" placeholder="Tulis kebijakan privasi di sini...">{{ $contentPrivacy }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Konten Syarat & Ketentuan</label>
                                    <textarea name="content_terms" class="form-control" rows="6" placeholder="Tulis syarat dan ketentuan di sini...">{{ $contentTerms }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Point Settings --}}
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Pengaturan Poin</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="point_price">Harga per Poin (Rp)</label>
                            <input type="number" class="form-control" id="point_price" name="point_price" value="{{ $pointPrice }}" required min="1">
                            <small class="form-text text-muted">Akan berpengaruh pada kalkulasi semua paket.</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bank Accounts --}}
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Rekening Pembayaran</h6>
                        <button type="button" class="btn btn-sm btn-success" onclick="addBankRow()"><i class="fas fa-plus"></i> Tambah Bank</button>
                    </div>
                    <div class="card-body">
                        <div id="bank-container">
                            @foreach($bankAccounts as $index => $bank)
                            <div class="row mb-2 bank-row">
                                <div class="col-md-3">
                                    <input type="text" name="bank_name[]" class="form-control" placeholder="Nama Bank" value="{{ $bank['bank'] }}" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="account_number[]" class="form-control" placeholder="No. Rekening" value="{{ $bank['number'] }}" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="account_name[]" class="form-control" placeholder="Atas Nama" value="{{ $bank['name'] }}" required>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger btn-circle btn-sm" onclick="removeBankRow(this)"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Universal Image Preview Function
    function previewImage(input, targetId) {
        const preview = document.getElementById(targetId);
        const file = input.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block'; // Make sure it is visible
            }
            reader.readAsDataURL(file);
        }
    }

    function addBankRow() {
        var html = `
            <div class="card border-0 shadow-sm mb-3 bank-row">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label class="small text-muted mb-0">Nama Bank</label>
                            <input type="text" name="bank_name[]" class="form-control font-weight-bold" placeholder="BCA / Mandiri" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted mb-0">No. Rekening</label>
                            <input type="text" name="account_number[]" class="form-control font-weight-bold text-dark" placeholder="1234xxxx" required>
                        </div>
                        <div class="col-md-4">
                                <label class="small text-muted mb-0">Atas Nama</label>
                            <input type="text" name="account_name[]" class="form-control" placeholder="Pemilik Rekening" required>
                        </div>
                        <div class="col-md-1 text-right">
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-circle mt-3" onclick="removeBankRow(this)" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.querySelector('#bank-container').insertAdjacentHTML('beforeend', html);
    }

    function removeBankRow(btn) {
        // Find the closest .bank-row (which is now a card)
        var row = btn.closest('.bank-row');
        // Add a fade out effect/animation before removing could be nice, but simple remove is enough
        row.remove();
    }
</script>
@endsection
