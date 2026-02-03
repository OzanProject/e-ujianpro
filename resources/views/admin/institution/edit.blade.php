@extends('layouts.admin.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Kustomisasi Aplikasi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Data Lembaga</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-check"></i> Sukses!</h5>
                {{ session('success') }}
            </div>
        @endif

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Form Data Lembaga</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{ route('admin.institution.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nama Lembaga / Sekolah</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $institution->name) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="dinas_name">Nama Dinas / Yayasan (Kop Surat)</label>
                                <input type="text" class="form-control" id="dinas_name" name="dinas_name" value="{{ old('dinas_name', $institution->dinas_name) }}" placeholder="Contoh: PEMERINTAH KABUPATEN CIANJUR">
                            </div>
                            <div class="form-group">
                                <label for="npsn">NPSN</label>
                                <input type="text" class="form-control" id="npsn" name="npsn" value="{{ old('npsn', $institution->npsn) }}">
                            </div>
                            <div class="form-group">
                                <label for="subdomain">URL / Subdomain</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ url('/') }}/</span>
                                    </div>
                                    <input type="text" class="form-control" id="subdomain" name="subdomain" value="{{ old('subdomain', $institution->subdomain) }}" placeholder="hanya-huruf-angka" {{ $institution->subdomain ? 'readonly' : '' }}>
                                </div>
                                @if(!$institution->subdomain)
                                    <small class="form-text text-muted">Username unik untuk link akses sekolah (misal: smpn1). Tidak bisa diubah setelah disimpan.</small>
                                @else
                                    <small class="form-text text-muted">URL akses sekolah. Hubungi admin jika ingin mengubah.</small>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $institution->email) }}">
                            </div>
                            <div class="form-group">
                                <label for="phone">Telepon</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $institution->phone) }}">
                            </div>
                            <div class="form-group">
                                <label for="address">Alamat</label>
                                <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $institution->address) }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="city">Kota / Kabupaten (untuk Titimangsa)</label>
                                <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $institution->city) }}" placeholder="Contoh: Kab. Cianjur">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="head_master">Nama Kepala Sekolah</label>
                                <input type="text" class="form-control" id="head_master" name="head_master" value="{{ old('head_master', $institution->head_master) }}">
                            </div>
                            <div class="form-group">
                                <label for="nip_head_master">NIP Kepala Sekolah</label>
                                <input type="text" class="form-control" id="nip_head_master" name="nip_head_master" value="{{ old('nip_head_master', $institution->nip_head_master) }}">
                            </div>
                            <div class="form-group">
                                <label for="logo">Logo Lembaga</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="logo" name="logo" onchange="previewImage(this, 'inst_logo_preview')">
                                        <label class="custom-file-label" for="logo">Pilih file</label>
                                    </div>
                                </div>
                                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah logo.</small>
                            </div>
                            
                            <div class="mt-3 text-center p-3 bg-light border rounded">
                                <label class="small text-muted d-block mb-2">Preview Logo:</label>
                                <img id="inst_logo_preview" src="{{ $institution->logo ? asset('storage/' . $institution->logo) : asset('img/logo-placeholder.png') }}" alt="Preview Logo" style="max-height: 150px; max-width: 100%; object-fit: contain; {{ $institution->logo ? '' : 'display:none;' }}">
                            </div>

                            {{-- Divider --}}
                            <div class="my-4 border-top"></div>
                            <h5 class="text-primary mb-3">Kop Surat (Cetak)</h5>

                            {{-- Logo Kiri --}}
                            <div class="form-group">
                                <label for="logo_kiri">Logo Kiri (Dinas/Pemda)</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="logo_kiri" name="logo_kiri" onchange="previewImage(this, 'inst_logo_kiri_preview')">
                                        <label class="custom-file-label" for="logo_kiri">Pilih file logo kiri</label>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center p-2 mb-3 bg-light border rounded">
                                <img id="inst_logo_kiri_preview" src="{{ $institution->logo_kiri ? asset('storage/' . $institution->logo_kiri) : '' }}" alt="Preview Kiri" style="max-height: 100px; max-width: 100%; object-fit: contain; {{ $institution->logo_kiri ? '' : 'display:none;' }}">
                            </div>

                            {{-- Logo Kanan --}}
                            <div class="form-group">
                                <label for="logo_kanan">Logo Kanan (Sekolah/Institusi)</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="logo_kanan" name="logo_kanan" onchange="previewImage(this, 'inst_logo_kanan_preview')">
                                        <label class="custom-file-label" for="logo_kanan">Pilih file logo kanan</label>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center p-2 bg-light border rounded mb-3">
                                <img id="inst_logo_kanan_preview" src="{{ $institution->logo_kanan ? asset('storage/' . $institution->logo_kanan) : '' }}" alt="Preview Kanan" style="max-height: 100px; max-width: 100%; object-fit: contain; {{ $institution->logo_kanan ? '' : 'display:none;' }}">
                            </div>

                            {{-- Tanda Tangan dan Stempel --}}
                            <h5 class="text-primary mb-3">Tanda Tangan & Stempel</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="signature">Tanda Tangan Kepala Sekolah (PNG Transparan)</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="signature" name="signature" onchange="previewImage(this, 'inst_signature_preview')">
                                                <label class="custom-file-label" for="signature">Pilih File</label>
                                            </div>
                                        </div>
                                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah tanda tangan.</small>
                                    </div>
                                    <div class="text-center p-2 mb-3 bg-light border rounded">
                                        <img id="inst_signature_preview" src="{{ $institution->signature ? asset('storage/' . $institution->signature) : '' }}" alt="Preview TTD" style="max-height: 100px; max-width: 100%; object-fit: contain; {{ $institution->signature ? '' : 'display:none;' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="stamp">Stempel Lembaga (PNG Transparan)</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="stamp" name="stamp" onchange="previewImage(this, 'inst_stamp_preview')">
                                                <label class="custom-file-label" for="stamp">Pilih File</label>
                                            </div>
                                        </div>
                                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah stempel.</small>
                                    </div>
                                    <div class="text-center p-2 mb-3 bg-light border rounded">
                                        <img id="inst_stamp_preview" src="{{ $institution->stamp ? asset('storage/' . $institution->stamp) : '' }}" alt="Preview Stempel" style="max-height: 100px; max-width: 100%; object-fit: contain; {{ $institution->stamp ? '' : 'display:none;' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    function previewImage(input, targetId) {
        const preview = document.getElementById(targetId);
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
