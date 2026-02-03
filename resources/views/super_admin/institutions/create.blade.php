@extends('layouts.admin.app')
@section('title', 'Tambah Sekolah Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Pendaftaran Sekolah Baru</h6>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.super.institutions.store') }}" method="POST">
                    @csrf
                    
                    <h6 class="heading-small text-muted mb-4">Informasi Akun Admin</h6>
                    <div class="pl-lg-4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label">Nama Admin</label>
                                    <input type="text" name="name" class="form-control" placeholder="Nama Lengkap Admin" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label">Email Login</label>
                                    <input type="email" name="email" class="form-control" placeholder="email@sekolah.com" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label">Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label">No. WhatsApp</label>
                                    <input type="text" name="whatsapp" class="form-control" placeholder="08xxxxxxxxxx">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" />

                    <h6 class="heading-small text-muted mb-4">Profil Sekolah</h6>
                    <div class="pl-lg-4">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-control-label">Nama Sekolah</label>
                                    <input type="text" name="institution_name" class="form-control" placeholder="Contoh: SMA Negeri 1 Jakarta" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label">Subdomain</label>
                                    <div class="input-group mb-3">
                                        <input type="text" name="subdomain" class="form-control" placeholder="sman1jkt" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">.e-ujian.id</span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Akan menjadi alamat akses sekolah.</small>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label">Kota / Kab</label>
                                    <input type="text" name="city" class="form-control" placeholder="Nama Kota" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label">Jenjang</label>
                                    <select name="type" class="form-control custom-select">
                                        <option value="SMA">SMA / SMK / MA</option>
                                        <option value="SMP">SMP / MTs</option>
                                        <option value="SD">SD / MI</option>
                                        <option value="UNIVERSITAS">Universitas</option>
                                        <option value="UMUM">Bimbel / Umum</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label">Kode Afiliasi (Opsional)</label>
                                    <input type="text" name="affiliate_code" class="form-control" placeholder="Kode Sales / Referal">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 text-right mt-4">
                        <a href="{{ route('admin.super.dashboard') }}" class="btn btn-secondary mr-2">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan & Aktifkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
