@extends('layouts.admin.app')

@section('title', 'Jadwal Ujian')
@section('page_title', 'Jadwal Ujian')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-header bg-white py-4 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="font-weight-bold text-dark mb-1">Daftar Jadwal Ujian</h5>
                    <p class="text-muted text-sm mb-0">Kelola sesi ujian, waktu, dan token akses siswa.</p>
                </div>
                <div class="d-flex align-items-center">
                    <form action="{{ route($baseRoute . '.index') }}" method="GET" class="mr-3 d-flex align-items-center">
                        
                        <label class="text-xs font-weight-bold text-muted mb-0 mr-2">KELAS:</label>
                        <select name="target_kelas" class="form-control form-control-sm rounded-pill border-gray-300 mr-3" onchange="this.form.submit()" style="width: 130px;">
                            <option value="">Semua Kelas</option>
                            <option value="1" {{ request('target_kelas') == '1' ? 'selected' : '' }}>Kelas 1 / I</option>
                            <option value="2" {{ request('target_kelas') == '2' ? 'selected' : '' }}>Kelas 2 / II</option>
                            <option value="3" {{ request('target_kelas') == '3' ? 'selected' : '' }}>Kelas 3 / III</option>
                            <option value="4" {{ request('target_kelas') == '4' ? 'selected' : '' }}>Kelas 4 / IV</option>
                            <option value="5" {{ request('target_kelas') == '5' ? 'selected' : '' }}>Kelas 5 / V</option>
                            <option value="6" {{ request('target_kelas') == '6' ? 'selected' : '' }}>Kelas 6 / VI</option>
                            <option value="7" {{ request('target_kelas') == '7' ? 'selected' : '' }}>Kelas 7 / VII</option>
                            <option value="8" {{ request('target_kelas') == '8' ? 'selected' : '' }}>Kelas 8 / VIII</option>
                            <option value="9" {{ request('target_kelas') == '9' ? 'selected' : '' }}>Kelas 9 / IX</option>
                            <option value="10" {{ request('target_kelas') == '10' ? 'selected' : '' }}>Kelas 10 / X</option>
                            <option value="11" {{ request('target_kelas') == '11' ? 'selected' : '' }}>Kelas 11 / XI</option>
                            <option value="12" {{ request('target_kelas') == '12' ? 'selected' : '' }}>Kelas 12 / XII</option>
                        </select>

                        <label class="text-xs font-weight-bold text-muted mb-0 mr-2">TAMPILKAN:</label>
                        <select name="per_page" class="form-control form-control-sm rounded-pill border-gray-300" onchange="this.form.submit()" style="width: 100px;">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 data</option>
                            <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20 data</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 data</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 data</option>
                        </select>
                    </form>
                    <a href="{{ route($baseRoute . '.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 font-weight-bold">
                        <i class="fas fa-plus mr-2"></i> Buat Jadwal Baru
                    </a>
                </div>
            </div>
            
            <div class="card-body p-0">
                @if(session('success'))
                    <div class="alert alert-success m-4 border-0 shadow-sm d-flex align-items-center" role="alert" style="background-color: #d1fae5; color: #065f46;">
                        <i class="fas fa-check-circle mr-2 text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <form id="bulkActionForm" action="{{ route($baseRoute . '.bulk_regenerate_token') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3" style="width: 40px;">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="check-all">
                                            <label class="custom-control-label" for="check-all"></label>
                                        </div>
                                    </th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 px-4 py-3">Judul & Deskripsi</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 px-4 py-3">Mapel & Paket</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 px-4 py-3">Waktu Pelaksanaan</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7 px-4 py-3">Token</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7 px-4 py-3">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7 px-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($examSessions as $session)
                                    <tr class="border-bottom">
                                        <td class="px-4 py-3">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="ids[]" value="{{ $session->id }}" class="custom-control-input item-check" id="check-{{ $session->id }}">
                                                <label class="custom-control-label" for="check-{{ $session->id }}"></label>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm font-weight-bold text-dark">{{ $session->title }}</h6>
                                                <p class="text-xs text-secondary mb-0 mt-1">
                                                    {{ Str::limit($session->description, 50) ?: 'Tidak ada deskripsi' }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex flex-column">
                                                <span class="text-xs font-weight-bold text-dark mb-1">
                                                    <i class="fas fa-book text-primary mr-1"></i> {{ $session->subject->name }}
                                                </span>
                                                <span class="text-xs text-secondary mb-1">
                                                    <i class="fas fa-layer-group text-info mr-1"></i> {{ $session->examPackage->title ?? 'Semua Paket' }}
                                                </span>
                                                <span class="text-xs text-secondary">
                                                    <i class="fas fa-users text-warning mr-1"></i> 
                                                    @if($session->target_kelas)
                                                        <span class="badge badge-warning text-dark border">Target: Kelas {{ $session->target_kelas }}</span>
                                                    @else
                                                        Target: Semua Kelas
                                                    @endif
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex flex-column text-sm">
                                                <span class="text-dark font-weight-bold mb-1">
                                                    @tgl($session->start_time)
                                                </span>
                                                <span class="text-muted text-xs">
                                                    {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}
                                                    <span class="ml-1 badge badge-light border">{{ $session->duration }} Menit</span>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="bg-light rounded py-1 px-2 border d-inline-flex align-items-center justify-content-center">
                                                <span class="font-weight-bolder text-dark text-sm mr-2" style="letter-spacing: 2px;">{{ $session->token ?? '---' }}</span>
                                                <button type="button" class="btn btn-link text-primary p-0 m-0" title="Refresh Token" onclick="regenerateSingle({{ $session->id }})">
                                                    <i class="fas fa-sync-alt fa-xs"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($session->is_active)
                                                <span class="badge badge-sm bg-gradient-success text-white px-3 py-2 rounded-pill shadow-sm">Aktif</span>
                                            @else
                                                <span class="badge badge-sm bg-secondary text-white px-3 py-2 rounded-pill">Non-Aktif</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="d-flex justify-content-center">
                                                <a href="{{ route('proctor.monitor.show', ['subdomain' => request()->route('subdomain') ?? ($globalInstitution->subdomain ?? 'portal'), 'session' => $session->id]) }}" class="btn btn-sm btn-outline-primary rounded-circle mr-2 shadow-sm" title="Monitor Ujian" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-desktop text-xs"></i>
                                                </a>
                                                <a href="{{ route($baseRoute . '.proctors', $session->id) }}" class="btn btn-sm btn-outline-secondary rounded-circle mr-2" title="Atur Pengawas" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-user-shield text-xs"></i>
                                                </a>
                                                <a href="{{ route($baseRoute . '.edit', $session->id) }}" class="btn btn-sm btn-outline-info rounded-circle mr-2" title="Edit Jadwal" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-pencil-alt text-xs"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-success rounded-circle mr-2" title="Copy / Duplikat Jadwal" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;" onclick="duplicateSingle({{ $session->id }})">
                                                    <i class="far fa-copy text-xs"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" title="Hapus" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;" onclick="deleteSingle({{ $session->id }})">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center justify-content-center">
                                                <div class="bg-gray-100 rounded-circle p-4 mb-3">
                                                    <i class="fas fa-calendar-times text-gray-400 fa-3x"></i>
                                                </div>
                                                <h6 class="text-muted font-weight-bold">Belum ada Jadwal Ujian</h6>
                                                <p class="text-sm text-gray-500">Mulai dengan membuat sesi ujian baru untuk peserta.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer bg-white py-4 border-top d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-outline-primary rounded-pill px-4 font-weight-bold shadow-sm" id="btn-bulk-refresh" disabled onclick="return confirm('Apakah Anda yakin ingin me-refresh token untuk jadwal yang dipilih?')">
                            <i class="fas fa-sync-alt mr-2"></i> Refresh Token Terpilih
                        </button>
                        <div>
                            {{ $examSessions->links() }}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Hidden forms for single actions --}}
<form id="singleRefreshForm" method="POST" style="display:none;">@csrf</form>
<form id="singleDeleteForm" method="POST" style="display:none;">@csrf @method('DELETE')</form>
<form id="singleDuplicateForm" method="POST" style="display:none;">@csrf</form>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const $checkAll = $('#check-all');
        const $itemChecks = $('.item-check');
        const $btnBulkRefresh = $('#btn-bulk-refresh');

        $checkAll.on('change', function() {
            $itemChecks.prop('checked', this.checked);
            toggleBulkButton();
        });

        $itemChecks.on('change', function() {
            $checkAll.prop('checked', $itemChecks.length === $('.item-check:checked').length);
            toggleBulkButton();
        });

        function toggleBulkButton() {
            $btnBulkRefresh.prop('disabled', $('.item-check:checked').length === 0);
        }
    });

    function regenerateSingle(id) {
        if (confirm('Apakah Anda yakin ingin me-refresh token untuk jadwal ini?')) {
            const form = document.getElementById('singleRefreshForm');
            form.action = "{{ url('admin/exam_session') }}/" + id + "/regenerate-token";
            @if($baseRoute === 'pengajar')
                form.action = "{{ url('admin/pengajar/exam_session') }}/" + id + "/regenerate-token";
            @endif
            form.submit();
        }
    }

    function duplicateSingle(id) {
        if (confirm('Apakah Anda yakin ingin menyalin/menduperlikat jadwal ujian ini?')) {
            const form = document.getElementById('singleDuplicateForm');
            form.action = "{{ url('admin/exam_session') }}/" + id + "/duplicate";
            @if($baseRoute === 'pengajar')
                form.action = "{{ url('admin/pengajar/exam_session') }}/" + id + "/duplicate";
            @endif
            form.submit();
        }
    }

    function deleteSingle(id) {
        if (confirm('Apakah Anda yakin ingin menghapus jadwal ujian ini?')) {
            const form = document.getElementById('singleDeleteForm');
            form.action = "{{ url('admin/exam_session') }}/" + id;
            @if($baseRoute === 'pengajar')
                form.action = "{{ url('admin/pengajar/exam_session') }}/" + id;
            @endif
            form.submit();
        }
    }
</script>
@endpush
