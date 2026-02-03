@extends('layouts.admin.app')

@section('title', 'Profile')
@section('page_title', 'Edit Profile')

@section('content')
<div class="row">
    <!-- Left Column: Profile Information -->
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Informasi Akun</h3>
            </div>
            
            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('patch')
                
                <div class="card-body">
                    {{-- Success Message for Profile Update --}}
                    @if(session('status') === 'profile-updated')
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Profile berhasil diperbarui.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="form-group text-center mb-4">
                        <label class="d-block">Foto Profil</label>
                        <div class="mb-3">
                            <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('dist/img/user2-160x160.jpg') }}" 
                                 class="img-circle elevation-2" 
                                 style="width: 100px; height: 100px; object-fit: cover;" 
                                 id="photoPreview">
                        </div>
                        <div class="custom-file" style="max-width: 300px; margin: 0 auto;">
                            <input type="file" class="custom-file-input @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*" onchange="previewImage(this)">
                            <label class="custom-file-label text-left" for="photo">Pilih Foto</label>
                        </div>
                        @error('photo')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="alert alert-warning">
                            Your email address is unverified.
                            <button form="send-verification" class="btn btn-link p-0 m-0 align-baseline">Click here to re-send the verification email.</button>
                        </div>
                    @endif
                </div>

                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
        
        {{-- Hidden form for verification resend --}}
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>
    </div>

    <!-- Right Column: Update Password -->
    <div class="col-md-6">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Ganti Password</h3>
            </div>
            
            <form method="post" action="{{ route('password.update') }}">
                @csrf
                @method('put')
                
                <div class="card-body">
                    {{-- Success Message for Password Update --}}
                    @if(session('status') === 'password-updated')
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Password berhasil diubah.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                     @endif

                    <div class="form-group">
                        <label for="current_password">Password Saat Ini</label>
                        <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" id="current_password" name="current_password" autocomplete="current-password">
                        @error('current_password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password Baru</label>
                        <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" id="password" name="password" autocomplete="new-password">
                        @error('password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                        @error('password_confirmation', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-warning">Ganti Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card-danger collapsed-card">
            <div class="card-header">
                <h3 class="card-title">Hapus Akun</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <p>Setelah akun dihapus, semua data akan hilang permanen. Harap unduh data penting sebelum menghapus.</p>
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmUserDeletionModal">
                    Hapus Akun
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete Account -->
<div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="{{ route('profile.destroy') }}" class="modal-content">
            @csrf
            @method('delete')
            <div class="modal-header">
                <h5 class="modal-title" id="confirmUserDeletionModalLabel">Konfirmasi Hapus Akun</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus akun ini? Masukkan password untuk konfirmasi.</p>
                <div class="form-group">
                    <label for="password_delete">Password</label>
                    <input type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" id="password_delete" name="password" placeholder="Password Anda">
                    @error('password', 'userDeletion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Ya, Hapus Akun</button>
            </div>
        </form>
    </div>
</div>

@if($errors->userDeletion->isNotEmpty())
    <script>
        $(function() {
            $('#confirmUserDeletionModal').modal('show');
        });
    </script>
@endif

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#photoPreview').attr('src', e.target.result);
                // Also update label
                var fileName = input.files[0].name;
                $(input).next('.custom-file-label').html(fileName);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
