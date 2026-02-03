@extends('layouts.admin.app')

@section('title', 'Data Lembaga')
@section('page_title', 'Profil Lembaga')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    @if($institution->logo)
                        <img class="profile-user-img img-fluid img-circle"
                             src="{{ asset('storage/' . $institution->logo) }}"
                             alt="Logo Lembaga" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <img class="profile-user-img img-fluid img-circle"
                             src="{{ asset('dist/img/AdminLTELogo.png') }}"
                             alt="Logo Default">
                    @endif
                </div>

                <h3 class="profile-username text-center mt-3">{{ $institution->name }}</h3>
                <p class="text-muted text-center">{{ $institution->email ?? '-' }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>URL Lembaga</b> <a href="{{ url($institution->subdomain ?? '#') }}" target="_blank" class="float-right">{{ $institution->subdomain ? url($institution->subdomain) : '-' }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Kepala Sekolah</b> <a class="float-right">{{ $institution->head_master ?? '-' }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Telepon</b> <a class="float-right">{{ $institution->phone ?? '-' }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-header p-2">
                <h3 class="card-title">Edit Informasi Lembaga</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.institution.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Nama Lembaga</label>
                        <div class="col-sm-9">
                            <input type="text" name="name" class="form-control" value="{{ old('name', $institution->name) }}" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">URL / Subdomain</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{ url('/') }}/</span>
                                </div>
                                <input type="text" name="subdomain" class="form-control" value="{{ old('subdomain', $institution->subdomain) }}" placeholder="hanya-huruf-angka" {{ $institution->subdomain ? 'readonly' : '' }}>
                            </div>
                            @if(!$institution->subdomain)
                                <small class="text-muted">Masukkan username unik untuk URL akses sekolah Anda. (Tidak dapat diubah setelah disimpan)</small>
                            @else
                                <small class="text-muted">URL tidak dapat diubah. Hubungi admin untuk perubahan.</small>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Kepala Sekolah</label>
                        <div class="col-sm-9">
                            <input type="text" name="head_master" class="form-control" value="{{ old('head_master', $institution->head_master) }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">NIP Kepala Sekolah</label>
                        <div class="col-sm-9">
                            <input type="text" name="nip_head_master" class="form-control" value="{{ old('nip_head_master', $institution->nip_head_master) }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Email</label>
                        <div class="col-sm-9">
                            <input type="email" name="email" class="form-control" value="{{ old('email', $institution->email) }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Telepon</label>
                        <div class="col-sm-9">
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $institution->phone) }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Website</label>
                        <div class="col-sm-9">
                            <input type="text" name="website" class="form-control" value="{{ old('website', $institution->website) }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Alamat</label>
                        <div class="col-sm-9">
                            <textarea name="address" class="form-control" rows="3">{{ old('address', $institution->address) }}</textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Ganti Logo</label>
                        <div class="col-sm-9">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="logo" id="logoUpload">
                                <label class="custom-file-label" for="logoUpload">{{ $institution->logo ? basename($institution->logo) : 'Pilih file logo utama...' }}</label>
                            </div>
                            <small class="text-muted">Format: JPG, PNG. Maks: 2MB. Logo ini muncul di sidebar dan favicon.</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Logo Kiri (Dinas/Pemda)</label>
                        <div class="col-sm-9">
                            @if($institution->logo_kiri)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $institution->logo_kiri) }}" height="50">
                                </div>
                            @endif
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="logo_kiri" id="logoKiriUpload">
                                <label class="custom-file-label" for="logoKiriUpload">{{ $institution->logo_kiri ? basename($institution->logo_kiri) : 'Pilih logo kiri di kop surat...' }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Logo Kanan (Sekolah)</label>
                        <div class="col-sm-9">
                            @if($institution->logo_kanan)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $institution->logo_kanan) }}" height="50">
                                </div>
                            @endif
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="logo_kanan" id="logoKananUpload">
                                <label class="custom-file-label" for="logoKananUpload">{{ $institution->logo_kanan ? basename($institution->logo_kanan) : 'Pilih logo kanan di kop surat...' }}</label>
                            </div>
                        </div>
                    </div>
                
                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-9">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Custom file input label update
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>
@endpush
