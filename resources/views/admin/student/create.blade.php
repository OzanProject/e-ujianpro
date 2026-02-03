@extends('layouts.admin.app')

@section('title', 'Tambah Peserta')
@section('page_title', 'Tambah Peserta')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Form Tambah Peserta</h3>
            </div>
            <form action="{{ route('admin.student.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <h5 class="text-primary">Data Siswa</h5>
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Nama Lengkap Siswa">
                    </div>
                    
                    <div class="form-group">
                        <label>NIS (Nomor Induk Siswa)</label>
                        <input type="text" name="nis" class="form-control" value="{{ old('nis') }}" required placeholder="Contoh: 12345678">
                        <small class="text-muted">Akan digunakan sebagai <strong>Username</strong> login.</small>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="Password Login">
                    </div>

                    <hr>

                    <div class="row">
                         <div class="col-md-6">
                            <div class="form-group">
                                <label>Kelompok (Rombel)</label>
                                <select name="student_group_id" class="form-control select2">
                                    <option value="">-- Pilih Kelompok --</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Kelas <small>(Opsional jika sudah pakai Kelompok)</small></label>
                                <input type="text" name="kelas" class="form-control" value="{{ old('kelas') }}" placeholder="Contoh: XII IPA 1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jurusan <small>(Khusus SMK/SMA)</small></label>
                                <input type="text" name="jurusan" class="form-control" value="{{ old('jurusan') }}" placeholder="Contoh: IPA, TKJ (Boleh dikosongkan untuk SD/SMP)">
                            </div>
                            
                            <div class="form-group">
                                <label>Ruangan Ujian (Opsional)</label>
                                <select name="exam_room_id" class="form-control select2">
                                    <option value="">-- Tanpa Spesifik Ruangan --</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Foto Peserta</label>
                                <div class="mb-2 text-center">
                                    <img id="preview-photo" src="#" alt="Preview Foto" class="img-thumbnail" style="max-height: 150px; display: none;">
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="photo" id="photo" accept="image/*" onchange="previewImage(this)">
                                    <label class="custom-file-label" for="photo">Pilih Foto...</label>
                                </div>
                                <small class="text-muted">Format: JPG/PNG, Maks: 2MB</small>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan Peserta</button>
                    <a href="{{ route('admin.student.index') }}" class="btn btn-default float-right">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-photo').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection
