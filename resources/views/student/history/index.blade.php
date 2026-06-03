@extends('layouts.student.app')

@section('page_title', 'Riwayat Ujian')

@section('content')
    <style>
        .history-page {
            max-width: 1200px;
            margin: 0 auto;
        }

        .history-hero {
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            border-radius: 22px;
            color: #fff;
            padding: 1.75rem;
            box-shadow: 0 12px 28px rgba(37, 99, 235, .22);
            margin-bottom: 1.5rem;
        }

        .history-card {
            border: 0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 12px 26px rgba(15, 23, 42, .08);
        }

        .history-table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: .72rem;
            letter-spacing: .05em;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        .history-table tbody td {
            vertical-align: middle;
            color: #334155;
            border-color: #eef2f7;
        }

        .exam-title {
            font-weight: 800;
            color: #0f172a;
            margin-bottom: .15rem;
        }

        .exam-subtitle {
            color: #64748b;
            font-size: .82rem;
        }

        .score-badge {
            font-size: .95rem;
            border-radius: 999px;
            padding: .45rem .8rem;
        }

        .btn-detail {
            border-radius: 999px;
            font-weight: 700;
            padding: .45rem .9rem;
            box-shadow: 0 6px 14px rgba(14, 165, 233, .18);
        }

        .empty-state {
            padding: 4rem 1rem;
            text-align: center;
            color: #64748b;
        }

        .empty-state-icon {
            width: 78px;
            height: 78px;
            border-radius: 999px;
            background: #eff6ff;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 767px) {
            .history-hero {
                padding: 1.25rem;
                border-radius: 18px;
            }

            .history-table {
                min-width: 760px;
            }
        }
    </style>

    <div class="history-page">
        <div class="history-hero">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <div class="text-uppercase font-weight-bold mb-1" style="font-size:.75rem; opacity:.78;">
                        <i class="fas fa-history mr-1"></i> Riwayat Ujian
                    </div>
                    <h3 class="font-weight-bold mb-1">Daftar Ujian Selesai</h3>
                    <p class="mb-0" style="opacity:.86;">Lihat hasil, skor, dan detail jawaban ujian yang sudah dikerjakan.
                    </p>
                </div>
                <div class="mt-3 mt-md-0">
                    <span class="badge badge-light text-primary px-3 py-2 rounded-pill">
                        Total: {{ $attempts->total() ?? $attempts->count() }} Ujian
                    </span>
                </div>
            </div>
        </div>

        <div class="card history-card">
            <div class="card-header bg-white border-0 px-4 pt-4 pb-2">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h5 class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-clipboard-check text-primary mr-2"></i>
                            Riwayat Pengerjaan
                        </h5>
                        <p class="text-muted text-sm mb-0">Data ujian diurutkan dari yang terbaru.</p>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                @if($attempts->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-file-circle-question"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark">Belum ada ujian yang diselesaikan</h5>
                        <p class="mb-0">Riwayat ujian akan muncul setelah Anda menyelesaikan ujian.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover history-table mb-0">
                            <thead>
                                <tr>
                                    <th class="pl-4" width="70">No</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Paket Soal</th>
                                    <th>Tanggal Ujian</th>
                                    <th>Durasi</th>
                                    <th>Nilai</th>
                                    <th class="text-center pr-4" width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attempts as $index => $attempt)
                                    @php
                                        $session = $attempt->examSession;
                                        $subject = optional($session)->subject;
                                        $package = optional($session)->examPackage;
                                        $durationUsed = ($attempt->start_time && $attempt->end_time)
                                            ? round($attempt->start_time->diffInMinutes($attempt->end_time))
                                            : null;
                                    @endphp
                                    <tr>
                                        <td class="pl-4 font-weight-bold text-muted">
                                            {{ $attempts->firstItem() + $index }}
                                        </td>
                                        <td>
                                            <div class="exam-title">
                                                {{ $subject->name ?? 'Mata Pelajaran Tidak Ditemukan' }}
                                            </div>
                                            <div class="exam-subtitle">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                @tgl_jam(optional($session)->start_time)
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-light border px-3 py-2 text-secondary">
                                                {{ $package->title ?? 'Paket Soal Acak / Semua Soal' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold">
                                                @tgl_jam($attempt->start_time)
                                            </div>
                                            <div class="exam-subtitle">
                                                Selesai: {{ $attempt->end_time ? $attempt->end_time->format('H:i') : '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($durationUsed !== null)
                                                <span class="badge badge-info px-3 py-2 rounded-pill">
                                                    {{ $durationUsed }} Menit
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($session && $session->show_score)
                                                @php
                                                    $score = (float) ($attempt->score ?? 0);
                                                    $kkm = $attempt->examSession->subject->kkm ?? 75;
                                                    $scoreClass = $score >= $kkm ? 'badge-success' : ($score >= ($kkm - 15) ? 'badge-warning text-white' : 'badge-danger');
                                                @endphp
                                                <span class="badge {{ $scoreClass }} score-badge">
                                                    {{ number_format($score, 2) }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary score-badge">
                                                    <i class="fas fa-eye-slash mr-1"></i> Disembunyikan
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center pr-4">
                                            <a href="{{ request()->route('subdomain') ? route('institution.student.history.show', ['subdomain' => request()->route('subdomain'), 'exam_session' => $attempt->id]) : route('student.history.show', $attempt->id) }}"
                                                class="btn btn-info btn-sm btn-detail">
                                                <i class="fas fa-eye mr-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 border-top bg-white">
                        {{ $attempts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection