<!-- Import Modal -->
@php
    $user = auth()->user();
    $baseRoute = $user->role === 'pengajar' ? 'pengajar.question' : 'admin.question';
@endphp
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg overflow-hidden">
            <div class="modal-header bg-gradient-to-r from-blue-700 to-indigo-800 text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold" id="importModalLabel">
                    <i class="fas fa-file-import mr-2"></i> Import Soal Massal
                </h5>
                <button type="button" class="close text-white opacity-75 hover:opacity-100" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route($baseRoute . '.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 bg-light-gray">
                    <div class="alert bg-blue-50 border-0 shadow-sm rounded-lg d-flex align-items-start mb-4" style="border-left: 4px solid #4e73df !important;">
                        <i class="fas fa-info-circle text-lg mr-3 mt-1 text-primary"></i>
                        <span class="text-sm text-dark">Gunakan template yang disediakan. Sistem mendukung format <strong>.xlsx</strong> (Excel) dan <strong>.docx</strong> (Word Table Parser).</span>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark text-sm">Mata Pelajaran Target <span class="text-danger">*</span></label>
                        <select name="subject_id" id="importSubjectId" class="form-control form-control-lg border-0 shadow-sm rounded-lg text-sm" required onchange="updateTemplateLink()">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark text-sm">Upload File (.xlsx / .docx) <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="file" name="file" class="custom-file-input position-absolute" id="customFile" required accept=".xlsx, .xls, .docx" style="opacity:0; width:100%; height:100%; top:0; left:0; cursor: pointer; z-index: 10;">
                            <div class="border-dashed p-4 rounded-lg text-center bg-white shadow-sm transition-all" id="dropZone" style="border-color: #cbd5e1; border-width: 2px;">
                                <div class="bg-gray-100 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px; background-color: #f8f9fc;">
                                    <i class="fas fa-cloud-upload-alt text-primary fa-lg"></i>
                                </div>
                                <h6 class="font-weight-bold mb-1 text-dark" id="fileNameDisplay">Klik atau Drop File di Sini</h6>
                                <p class="text-xs text-muted mb-0">Format yang didukung: Excel / Word (Maks 10MB)</p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 border-light">
                    
                    <label class="font-weight-bold text-dark text-sm d-block mb-3 text-center">Belum punya formatnya? Download Template:</label>
                    <div class="row">
                        <div class="col-6 pr-2">
                            <a href="{{ route($baseRoute . '.template') }}" id="downloadTemplateBtn" class="btn btn-white btn-block rounded-lg font-weight-bold shadow-sm d-flex flex-column align-items-center py-3 transition-all hover:bg-light" target="_blank" style="border: 1px solid rgba(40,167,69,0.3);">
                                 <i class="fas fa-file-excel fa-2x text-success mb-2"></i> 
                                 <span class="text-success text-xs">Format Excel</span>
                            </a>
                        </div>
                        <div class="col-6 pl-2">
                            <a href="{{ route($baseRoute . '.template.word') }}" id="downloadTemplateWordBtn" class="btn btn-white btn-block rounded-lg font-weight-bold shadow-sm d-flex flex-column align-items-center py-3 transition-all hover:bg-light" target="_blank" style="border: 1px solid rgba(0,123,255,0.3);">
                                 <i class="fas fa-file-word fa-2x text-primary mb-2"></i> 
                                 <span class="text-primary text-xs">Format Word</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light text-gray-600 font-weight-bold rounded-pill px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold shadow-sm rounded-pill px-4">
                        <i class="fas fa-upload mr-1"></i> Mulai Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Custom Dropzone File Display Interaction
    $(document).ready(function() {
        $("#customFile").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            if(fileName) {
                $("#fileNameDisplay").html('<i class="fas fa-check-circle text-success mr-1"></i> ' + fileName).removeClass('text-dark').addClass('text-success');
                $("#dropZone").css("border-color", "#28a745").css("background-color", "#f0fdf4");
            } else {
                $("#fileNameDisplay").html('Klik atau Drop File di Sini').removeClass('text-success').addClass('text-dark');
                $("#dropZone").css("border-color", "#cbd5e1").css("background-color", "#ffffff");
            }
        });

        // Drag and Drop Visual Feedback
        $("#customFile").on("dragenter", function() {
            $("#dropZone").css("background-color", "#f8f9fc").css("border-color", "#4e73df");
        });
        $("#customFile").on("dragleave drop", function() {
            if(!$(this).val()) {
                $("#dropZone").css("background-color", "#ffffff").css("border-color", "#cbd5e1");
            }
        });
    });

    // Dynamic Template Link Update (For Excel Template, attaches subject ID)
    function updateTemplateLink() {
        var subjectId = document.getElementById('importSubjectId').value;
        var btn = document.getElementById('downloadTemplateBtn');
        var baseUrl = "{{ route($baseRoute . '.template') }}";
        
        if(subjectId) {
            btn.href = baseUrl + "?subject_id=" + subjectId;
        } else {
            btn.href = baseUrl;
        }
    }
</script>
@endpush
