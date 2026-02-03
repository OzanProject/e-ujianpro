@extends('layouts.admin.app')

@section('title', 'Edit Peserta')
@section('page_title', 'Edit Peserta')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Form Edit Peserta</h3>
            </div>
            <form action="{{ route('admin.student.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                     @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <h5 class="text-primary">Data Siswa</h5>
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $student->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label>NIS (Nomor Induk Siswa)</label>
                        <input type="text" name="nis" class="form-control" value="{{ old('nis', $student->nis) }}" required>
                        <small class="text-muted">Username login.</small>
                    </div>

                    <div class="form-group">
                        <label>Password (Isi jika ingin mengganti)</label>
                        <input type="password" name="password" class="form-control" placeholder="Biarkan kosong jika tetap">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                             <div class="form-group">
                                <label>Kelompok (Rombel)</label>
                                <select name="student_group_id" class="form-control select2">
                                    <option value="">-- Pilih Kelompok --</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}" {{ (old('student_group_id', $student->student_group_id) == $group->id) ? 'selected' : '' }}>
                                            {{ $group->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Kelas</label>
                                <input type="text" name="kelas" class="form-control" value="{{ old('kelas', $student->kelas) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-group">
                                <label>Jurusan <small>(Boleh kosong untuk SD/SMP)</small></label>
                                <input type="text" name="jurusan" class="form-control" value="{{ old('jurusan', $student->jurusan) }}">
                            </div>

                            <div class="form-group">
                                <label>Ruangan Ujian (Opsional)</label>
                                <select name="exam_room_id" class="form-control select2">
                                    <option value="">-- Tanpa Spesifik Ruangan --</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}" {{ (old('exam_room_id', $student->exam_room_id) == $room->id) ? 'selected' : '' }}>
                                            {{ $room->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                            <div class="form-group">
                                <label>Foto Peserta</label>
                                <div class="mb-2 text-center">
                                    @if($student->photo)
                                        <img id="preview-photo" src="{{ asset('storage/' . $student->photo) }}" alt="Preview Foto" class="img-thumbnail" style="max-height: 150px;">
                                    @else
                                        <img id="preview-photo" src="#" alt="Preview Foto" class="img-thumbnail" style="max-height: 150px; display: none;">
                                    @endif
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="photo" id="photo" accept="image/*" onchange="previewImage(this)">
                                    <label class="custom-file-label" for="photo">Ganti Foto...</label>
                                </div>
                                <small class="text-muted">Format: JPG/PNG, Maks: 2MB. Biarkan kosong jika tidak ingin mengubah.</small>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">Update Peserta</button>
                    <a href="{{ route('admin.student.index') }}" class="btn btn-default float-right">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

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
