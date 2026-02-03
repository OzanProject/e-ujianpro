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
         <div class="card shadow-md border-0 rounded-lg mb-4 bg-gradient-to-r from-blue-700 to-indigo-800 text-white overflow-hidden">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="font-weight-bold mb-1">{{ $session->title }}</h3>
                        <p class="mb-0 opacity-75"><i class="fas fa-book mr-2"></i> {{ $session->subject->name }}</p>
                    </div>
                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                         <div class="bg-white text-blue-800 rounded-lg p-3 shadow-sm d-inline-block text-left">
                            <span class="d-block text-xs font-weight-bold text-uppercase tracking-wider text-gray-500">Token Ujian</span>
                            <span class="h3 font-weight-bold mb-0">{{ $session->token }}</span>
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
            <div class="card-header bg-white border-bottom-0 p-4 d-flex justify-content-between align-items-center">
                <h5 class="font-weight-bold text-gray-800 mb-0">
                    <i class="fas fa-users mr-2 text-indigo-500"></i> Status Peserta
                </h5>
                <span class="badge badge-light border px-3 py-2 rounded-pill" id="lastUpdated">
                    <i class="fas fa-sync-alt fa-spin mr-1"></i> Syncing...
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="bg-gray-100 text-gray-600 text-uppercase text-xs font-weight-bold">
                            <tr>
                                <th class="px-4 py-3 border-0">Nama Peserta</th>
                                <th class="px-4 py-3 border-0">Waktu Mulai</th>
                                <th class="px-4 py-3 border-0">Status</th>
                                <th class="px-4 py-3 border-0">Nilai Sementara</th>
                                <th class="px-4 py-3 border-0">Aktivitas Terakhir</th>
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
                                <td class="px-4 py-3 align-middle font-weight-bold">{{ $attempt->score ?? '-' }}</td>
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
    
    function loadData() {
        fetch(monitorUrl)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('monitorTableBody');
                tbody.innerHTML = ''; // Clear current
                
                if (data.length === 0) {
                     tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted">Belum ada peserta yang memulai ujian.</td></tr>';
                     return;
                }

                data.forEach(item => {
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

                    const row = `
                        <tr class="border-bottom hover:bg-gray-50 transition">
                            <td class="px-4 py-3 align-middle font-weight-bold text-gray-700">${item.student_name} <br> <small class="text-muted font-weight-normal">${item.student_number}</small></td>
                            <td class="px-4 py-3 align-middle">${item.start_time}</td>
                            <td class="px-4 py-3 align-middle">${statusBadge}</td>
                            <td class="px-4 py-3 align-middle font-weight-bold text-dark">${item.score}</td>
                            <td class="px-4 py-3 align-middle text-sm text-gray-500">${item.last_activity}</td>
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
                
                // Update Time
                document.getElementById('lastUpdated').innerHTML = '<i class="fas fa-check-circle text-success mr-1"></i> Updated ' + new Date().toLocaleTimeString();
            })
            .catch(error => console.error('Error fetching data:', error));
    }

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
