@extends('layouts.admin.app')

@section('title', 'Upload Foto Peserta')
@section('page_title', 'Upload Foto Peserta')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Upload Foto Peserta Massal</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> Petunjuk Upload Foto</h5>
                    <ol>
                        <li>Siapkan file foto peserta dalam format <strong>.jpg, .jpeg, atau .png</strong>.</li>
                        <li>Beri nama file foto sesuai dengan <strong>NIS Peserta</strong> (Contoh: <code>10001.jpg</code>).</li>
                        <li>Sistem akan otomatis mencocokkan foto dengan data peserta berdasarkan NIS.</li>
                        <li>Jika NIS tidak ditemukan, foto akan diabaikan.</li>
                        <li>Maksimal ukuran per file adalah <strong>2MB</strong>.</li>
                    </ol>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                
                <form action="{{ route('admin.student.store_photo') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="photos">Pilih Foto (Bisa Pilih Banyak)</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="photos" name="photos[]" multiple accept="image/*" required>
                                <label class="custom-file-label" for="photos">Pilih File...</label>
                            </div>
                        </div>
                        <small class="text-muted">Tekan Ctrl (Windows) atau Command (Mac) untuk memilih banyak foto sekaligus.</small>
                        <div id="preview-container" class="mt-3 d-flex flex-wrap"></div>
                    </div>

                    <div class="form-group text-right">
                        <a href="{{ route('admin.student.index') }}" class="btn btn-default">Kembali</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Mulai Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Custom File Input Label
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        
        // Count files
        var files = $(this)[0].files;
        if (files.length > 1) {
            fileName = files.length + " file dipilih";
        }
        
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);

        // Preview Images
        $('#preview-container').html('');
        if (files) {
            [].forEach.call(files, function(file) {
                 // Only process image files
                if (file.type.match('image.*')) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                         // Extract NIS from filename for display
                         var name = file.name;
                        $('#preview-container').append(
                            '<div class="m-2 text-center" style="display:inline-block;">' +
                                '<img src="'+e.target.result+'" class="img-thumbnail" style="height: 100px; width: 100px; object-fit: cover;"><br>' +
                                '<small style="font-size: 10px;">'+name+'</small>' +
                            '</div>'
                        );
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
@endpush
@endsection
