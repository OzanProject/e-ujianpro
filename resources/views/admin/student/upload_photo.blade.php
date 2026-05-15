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
                
                <form id="form-upload-photo" action="{{ route('admin.student.store_photo_ajax') }}" method="POST" enctype="multipart/form-data">
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
                        
                        <div class="progress mt-3 d-none" id="upload-progress-wrapper" style="height: 25px;">
                            <div id="upload-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                        </div>
                        <div id="upload-status" class="mt-2 text-sm font-weight-bold"></div>

                        <div id="preview-container" class="mt-3 d-flex flex-wrap"></div>
                    </div>

                    <div class="form-group text-right">
                        <a href="{{ route('admin.student.index') }}" class="btn btn-default">Kembali</a>
                        <button type="submit" id="btn-submit" class="btn btn-primary"><i class="fas fa-upload"></i> Mulai Upload</button>
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
    // AJAX Upload Logic
    $('#form-upload-photo').on('submit', async function(e) {
        e.preventDefault();
        
        var files = $('#photos')[0].files;
        if (files.length === 0) {
            Swal.fire('Error', 'Silakan pilih foto terlebih dahulu.', 'error');
            return;
        }

        var btnSubmit = $('#btn-submit');
        var progressWrapper = $('#upload-progress-wrapper');
        var progressBar = $('#upload-progress-bar');
        var statusText = $('#upload-status');
        
        btnSubmit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sedang Mengupload...');
        progressWrapper.removeClass('d-none');
        
        var totalFiles = files.length;
        var successCount = 0;
        var failCount = 0;
        var failedFiles = [];
        
        for (var i = 0; i < totalFiles; i++) {
            var file = files[i];
            var formData = new FormData();
            formData.append('photo', file);
            formData.append('_token', $('input[name="_token"]').val());
            
            statusText.text('Mengupload: ' + file.name + ' (' + (i+1) + ' dari ' + totalFiles + ')');
            
            try {
                var response = await $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false
                });
                
                if (response.success) {
                    successCount++;
                } else {
                    failCount++;
                    failedFiles.push(file.name + ' (NIS tidak ditemukan)');
                }
            } catch (error) {
                failCount++;
                var msg = error.responseJSON && error.responseJSON.message ? error.responseJSON.message : 'Error Server';
                failedFiles.push(file.name + ' (' + msg + ')');
            }
            
            var percentage = Math.round(((i + 1) / totalFiles) * 100);
            progressBar.css('width', percentage + '%').attr('aria-valuenow', percentage).text(percentage + '%');
        }
        
        btnSubmit.prop('disabled', false).html('<i class="fas fa-upload"></i> Mulai Upload');
        statusText.text('Selesai!');
        
        var finalMessage = `<b>${successCount} foto berhasil diupload.</b>`;
        if (failCount > 0) {
            finalMessage += `<br><br><span class="text-danger">${failCount} foto gagal diproses:</span><br><div style="max-height: 150px; overflow-y: auto; text-align: left; font-size: 12px; margin-top: 10px; border: 1px solid #eee; padding: 5px;">` + failedFiles.join('<br>') + `</div>`;
            Swal.fire({
                title: 'Upload Selesai!',
                html: finalMessage,
                icon: 'warning',
                confirmButtonText: 'Tutup'
            }).then(() => {
                window.location.href = "{{ route('admin.student.index') }}";
            });
        } else {
            Swal.fire({
                title: 'Berhasil!',
                html: finalMessage,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = "{{ route('admin.student.index') }}";
            });
        }
    });
</script>
@endpush
@endsection
