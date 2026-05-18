@extends('layouts.admin.app')

@section('title', 'Monitoring Ujian')
@section('page_title', 'Monitoring Ujian')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('proctor.dashboard', ['subdomain' => request()->route('subdomain')]) }}" class="btn btn-outline-secondary rounded-pill font-weight-bold">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

<div class="row">
    <!-- Session Info Card -->
    <div class="col-12">
         <div class="card shadow-md border-0 mb-4 overflow-hidden" style="border-radius: 15px; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white;">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <div class="d-flex align-items-center mb-2">
                             <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 45px; height: 45px; background: rgba(255,255,255,0.2) !important;">
                                <i class="fas fa-file-signature text-white"></i>
                             </div>
                             <div>
                                <h3 class="font-weight-bold mb-0" style="font-size: 1.5rem; letter-spacing: -0.5px;">{{ $session->title }}</h3>
                                <div class="badge badge-light px-3 py-1 mt-1" style="background: rgba(255,255,255,0.2); border: none; color: white;">
                                    <i class="fas fa-book-open mr-1"></i> {{ $session->subject->name }}
                                </div>
                             </div>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-right mt-3 mt-md-0">
                         <div class="bg-white rounded-lg p-3 shadow-sm d-inline-block text-left" style="min-width: 180px; border-left: 4px solid #f6c23e;">
                            <span class="d-block text-xs font-weight-bold text-uppercase tracking-wider text-muted mb-1">Token Ujian Aktif</span>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="h2 font-weight-bold mb-0 text-primary" style="font-family: 'Courier New', Courier, monospace;">{{ $session->token }}</span>
                                <i class="fas fa-key text-gray-300 ml-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header bg-white border-bottom-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold text-gray-800 mb-0">
                        <i class="fas fa-users mr-2 text-indigo-500"></i> Status Peserta
                    </h5>
                    <span class="badge badge-light border px-3 py-2 rounded-pill" id="lastUpdated">
                        <i class="fas fa-sync-alt fa-spin mr-1"></i> Syncing...
                    </span>
                </div>
                <div class="d-flex align-items-center mt-3 flex-wrap" style="gap: 10px;">
                    <div class="d-flex align-items-center">
                        <label class="mb-0 mr-2 text-sm font-weight-bold text-gray-600">Tampilkan:</label>
                        <select id="filterLimit" class="form-control form-control-sm" style="width: 80px;">
                            <option value="10">10</option>
                            <option value="20" selected>20</option>
                            <option value="50">50</option>
                            <option value="0">Semua</option>
                        </select>
                    </div>
                    <div class="d-flex align-items-center">
                        <label class="mb-0 mr-2 text-sm font-weight-bold text-gray-600">Urutkan:</label>
                        <select id="filterSort" class="form-control form-control-sm" style="width: 180px;">
                            <option value="latest" selected>Terbaru (Aktivitas)</option>
                            <option value="oldest">Terlama (Aktivitas)</option>
                            <option value="highest">Nilai Terbesar</option>
                            <option value="lowest">Nilai Terkecil</option>
                            <option value="name_asc">Nama A-Z</option>
                            <option value="name_desc">Nama Z-A</option>
                            <option value="start_asc">Waktu Mulai [▲]</option>
                            <option value="start_desc">Waktu Mulai [▼]</option>
                            <option value="cheat_desc">Pelanggaran [▲]</option>
                            <option value="cheat_asc">Pelanggaran [▼]</option>
                        </select>
                    </div>
                    <span id="totalCount" class="badge badge-secondary px-3 py-2 rounded-pill">
                        <i class="fas fa-user mr-1"></i> <span id="countText">0</span> peserta
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="bg-gray-100 text-gray-600 text-uppercase text-xs font-weight-bold" style="user-select: none;">
                            <tr>
                                <th class="px-4 py-3 border-0 sort-header" data-sort-asc="name_asc" data-sort-desc="name_desc" style="cursor: pointer;">
                                    Nama Peserta <i class="fas fa-sort ml-1 text-muted" id="sort-icon-name"></i>
                                </th>
                                <th class="px-4 py-3 border-0 sort-header" data-sort-asc="start_asc" data-sort-desc="start_desc" style="cursor: pointer;">
                                    Waktu Mulai <i class="fas fa-sort ml-1 text-muted" id="sort-icon-start"></i>
                                </th>
                                <th class="px-4 py-3 border-0">Status</th>
                                <th class="px-4 py-3 border-0 sort-header" data-sort-asc="lowest" data-sort-desc="highest" style="cursor: pointer;">
                                    Nilai Sementara <i class="fas fa-sort ml-1 text-muted" id="sort-icon-score"></i>
                                </th>
                                <th class="px-4 py-3 border-0 sort-header" data-sort-asc="oldest" data-sort-desc="latest" style="cursor: pointer;">
                                    Aktivitas Terakhir <i class="fas fa-sort ml-1 text-muted" id="sort-icon-activity"></i>
                                </th>
                                <th class="px-4 py-3 border-0 text-center sort-header" data-sort-asc="cheat_asc" data-sort-desc="cheat_desc" style="cursor: pointer;">
                                    Pelanggaran <i class="fas fa-sort ml-1 text-muted" id="sort-icon-cheat"></i>
                                </th>
                                <th class="px-4 py-3 border-0 text-center" style="width: 200px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="monitorTableBody">
                            <!-- Data loaded via AJAX -->
                            @foreach($attempts as $attempt)
                             <!-- Pre-fill for instant load, then replaced by JS -->
                             <tr id="row-{{ $attempt->id }}">
                                <td class="px-4 py-3 align-middle font-weight-bold">{{ $attempt->student->name }}</td>
                                <td class="px-4 py-3 align-middle">{{ $attempt->start_time ? $attempt->start_time->format('H:i:s') : '-' }}</td>
                                <td class="px-4 py-3 align-middle">
                                    @if($attempt->status == 'completed')
                                        <span class="badge badge-primary bg-blue-100 text-blue-800 px-3 py-1 rounded-pill">Selesai</span>
                                    @elseif($attempt->status == 'in_progress')
                                        <span class="badge badge-success bg-green-100 text-green-800 px-3 py-1 rounded-pill">Mengerjakan</span>
                                    @else
                                         <span class="badge badge-secondary px-3 py-1 rounded-pill">{{ $attempt->status }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-middle font-weight-bold">{{ is_numeric($attempt->score) ? number_format($attempt->score, 2) : ($attempt->score ?? '-') }}</td>
                                <td class="px-4 py-3 align-middle text-sm text-gray-500">{{ $attempt->updated_at->diffForHumans() }}</td>
                                <td class="px-4 py-3 align-middle text-center">
                                    <div class="btn-group shadow-sm rounded-lg" role="group">
                                         <button onclick="resetLogin({{ $attempt->id }})" class="btn btn-warning btn-sm hover:bg-yellow-500 hover:text-white transition" title="Reset Login"><i class="fas fa-undo"></i></button>
                                         <button onclick="stopExam({{ $attempt->id }})" class="btn btn-danger btn-sm hover:bg-red-700 transition" title="Hentikan Paksa"><i class="fas fa-stop-circle"></i></button>
                                    </div>
                                </td>
                             </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const sessionId = {{ $session->id }};
    const monitorUrl = "{{ route('proctor.monitor.data', ['subdomain' => request()->route('subdomain'), 'session' => $session->id]) }}";
    
    let allData = [];

    function applyFilterAndRender() {
        const limit = parseInt(document.getElementById('filterLimit').value);
        const sort = document.getElementById('filterSort').value;

        let sorted = [...allData];

        // Reset all sort icons
        document.querySelectorAll('.sort-header i').forEach(icon => {
            icon.className = 'fas fa-sort ml-1 text-muted';
        });

        // Set active sort icon
        if (sort === 'name_asc') {
            document.getElementById('sort-icon-name').className = 'fas fa-sort-up ml-1 text-primary';
        } else if (sort === 'name_desc') {
            document.getElementById('sort-icon-name').className = 'fas fa-sort-down ml-1 text-primary';
        } else if (sort === 'start_asc') {
            document.getElementById('sort-icon-start').className = 'fas fa-sort-up ml-1 text-primary';
        } else if (sort === 'start_desc') {
            document.getElementById('sort-icon-start').className = 'fas fa-sort-down ml-1 text-primary';
        } else if (sort === 'highest') {
            document.getElementById('sort-icon-score').className = 'fas fa-sort-down ml-1 text-primary';
        } else if (sort === 'lowest') {
            document.getElementById('sort-icon-score').className = 'fas fa-sort-up ml-1 text-primary';
        } else if (sort === 'latest') {
            document.getElementById('sort-icon-activity').className = 'fas fa-sort-down ml-1 text-primary';
        } else if (sort === 'oldest') {
            document.getElementById('sort-icon-activity').className = 'fas fa-sort-up ml-1 text-primary';
        } else if (sort === 'cheat_desc') {
            document.getElementById('sort-icon-cheat').className = 'fas fa-sort-down ml-1 text-primary';
        } else if (sort === 'cheat_asc') {
            document.getElementById('sort-icon-cheat').className = 'fas fa-sort-up ml-1 text-primary';
        }

        // Apply sorting
        if (sort === 'highest') {
            sorted.sort((a, b) => {
                const scoreA = parseFloat(a.score) || 0;
                const scoreB = parseFloat(b.score) || 0;
                return scoreB - scoreA;
            });
        } else if (sort === 'lowest') {
            sorted.sort((a, b) => {
                const scoreA = parseFloat(a.score) || 0;
                const scoreB = parseFloat(b.score) || 0;
                return scoreA - scoreB;
            });
        } else if (sort === 'name_asc') {
            sorted.sort((a, b) => a.student_name.localeCompare(b.student_name));
        } else if (sort === 'name_desc') {
            sorted.sort((a, b) => b.student_name.localeCompare(a.student_name));
        } else if (sort === 'start_asc') {
            sorted.sort((a, b) => (a.start_time || '').localeCompare(b.start_time || ''));
        } else if (sort === 'start_desc') {
            sorted.sort((a, b) => (b.start_time || '').localeCompare(a.start_time || ''));
        } else if (sort === 'latest') {
            sorted.sort((a, b) => (b.updated_at_ts || 0) - (a.updated_at_ts || 0));
        } else if (sort === 'oldest') {
            sorted.sort((a, b) => (a.updated_at_ts || 0) - (b.updated_at_ts || 0));
        } else if (sort === 'cheat_desc') {
            sorted.sort((a, b) => (parseInt(b.cheat_count) || 0) - (parseInt(a.cheat_count) || 0));
        } else if (sort === 'cheat_asc') {
            sorted.sort((a, b) => (parseInt(a.cheat_count) || 0) - (parseInt(b.cheat_count) || 0));
        }

        const sliced = limit === 0 ? sorted : sorted.slice(0, limit);

        document.getElementById('countText').textContent = allData.length;

        const tbody = document.getElementById('monitorTableBody');
        tbody.innerHTML = '';

        if (sliced.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted">Belum ada peserta yang memulai ujian.</td></tr>';
            return;
        }

        sliced.forEach(item => {
                    let statusBadge = '';
                    if (item.status === 'completed') {
                        statusBadge = '<span class="badge badge-primary bg-blue-100 text-blue-800 px-3 py-1 rounded-pill">Selesai</span>';
                    } else if (item.status === 'in_progress') {
                        if (item.is_online) {
                             statusBadge = '<span class="badge badge-success bg-green-100 text-green-800 px-3 py-1 rounded-pill">Mengerjakan (Online)</span>';
                        } else {
                             statusBadge = '<span class="badge badge-warning bg-yellow-100 text-yellow-800 px-3 py-1 rounded-pill">Mengerjakan (Offline?)</span>';
                        }
                    } else {
                        statusBadge = `<span class="badge badge-secondary px-3 py-1 rounded-pill">${item.status}</span>`;
                    }

                    let cheatBadge = '';
                    if (item.cheat_count == 0) {
                        cheatBadge = '<span class="badge badge-light border text-muted px-3 py-1 rounded-pill">0x</span>';
                    } else if (item.cheat_count == 1) {
                        cheatBadge = '<span class="badge badge-warning text-white px-3 py-1 rounded-pill">⚠️ 1x</span>';
                    } else if (item.cheat_count == 2) {
                        cheatBadge = '<span class="badge px-3 py-1 rounded-pill" style="background-color: #f6993f; color: white;">⚠️ 2x</span>';
                    } else {
                        cheatBadge = '<span class="badge badge-danger px-3 py-1 rounded-pill">⛔ Diskualifikasi ('+item.cheat_count+'x)</span>';
                    }

                    const row = `
                        <tr class="border-bottom hover:bg-gray-50 transition">
                            <td class="px-4 py-3 align-middle font-weight-bold text-gray-700">${item.student_name} <br> <small class="text-muted font-weight-normal">${item.student_number}</small></td>
                            <td class="px-4 py-3 align-middle">${item.start_time}</td>
                            <td class="px-4 py-3 align-middle">${statusBadge}</td>
                            <td class="px-4 py-3 align-middle font-weight-bold text-dark">${item.score}</td>
                            <td class="px-4 py-3 align-middle text-sm text-gray-500">${item.last_activity}</td>
                            <td class="px-4 py-3 align-middle text-center">${cheatBadge}</td>
                            <td class="px-4 py-3 align-middle text-center">
                                <div class="btn-group shadow-sm rounded-lg">
                                     <button onclick="resetLogin(${item.id})" class="btn btn-warning btn-sm text-white" title="Reset Login"><i class="fas fa-undo"></i></button>
                                     <button onclick="stopExam(${item.id})" class="btn btn-danger btn-sm" title="Hentikan Paksa"><i class="fas fa-stop-circle"></i></button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
    }

    function loadData() {
        fetch(monitorUrl)
            .then(response => response.json())
            .then(data => {
                allData = data;
                applyFilterAndRender();
                // Update Time
                document.getElementById('lastUpdated').innerHTML = '<i class="fas fa-check-circle text-success mr-1"></i> Updated ' + new Date().toLocaleTimeString();
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    // Re-render when filter/sort changes
    document.getElementById('filterLimit').addEventListener('change', applyFilterAndRender);
    document.getElementById('filterSort').addEventListener('change', applyFilterAndRender);

    // Handle click on column headers to sort
    document.querySelectorAll('.sort-header').forEach(header => {
        header.addEventListener('click', function() {
            const ascSort = this.getAttribute('data-sort-asc');
            const descSort = this.getAttribute('data-sort-desc');
            const currentSort = document.getElementById('filterSort').value;

            let newSort = ascSort;
            if (currentSort === ascSort) {
                newSort = descSort;
            }

            document.getElementById('filterSort').value = newSort;
            applyFilterAndRender();
        });
    });

    // Auto Refresh every 5 seconds
    setInterval(loadData, 5000);

    // Actions
    function resetLogin(id) {
        if (!confirm('Apakah Anda yakin ingin me-reset login siswa ini? Status akan dikembalikan ke "Mengerjakan".')) return;
        
        const url = "{{ route('proctor.monitor.reset', ['subdomain' => request()->route('subdomain'), 'attempt' => ':id']) }}".replace(':id', id);
        fetch(url, { 
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            loadData();
        });
    }

    function stopExam(id) {
        if (!confirm('PERINGATAN: Apakah Anda yakin ingin menghentikan paksa ujian siswa ini? Siswa akan dianggap SELESAI.')) return;

        const url = "{{ route('proctor.monitor.stop', ['subdomain' => request()->route('subdomain'), 'attempt' => ':id']) }}".replace(':id', id);
        fetch(url, { 
            method: 'POST',
             headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            loadData();
        });
    }
</script>
@endpush
