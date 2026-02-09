@extends('layouts.student.app')

@section('page_title', 'Riwayat Ujian')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Daftar Ujian Selesai</h3>
                </div>
                <div class="card-body">
                    @if($attempts->isEmpty())
                        <div class="alert alert-info">
                            Belum ada ujian yang diselesaikan.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Paket Soal</th>
                                        <th>Tanggal Ujian</th>
                                        <th>Nilai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attempts as $index => $attempt)
                                        <tr>
                                            <td>{{ $attempts->firstItem() + $index }}</td>
                                            <td>{{ $attempt->examSession->subject->name }}</td>
                                            <td>{{ $attempt->examSession->examPackage->title ?? 'Paket Soal Acak / Semua Soal' }}
                                            </td>
                                            <td>{{ $attempt->start_time->format('d M Y H:i') }}</td>
                                            <td>
                                                @if($attempt->examSession->show_score)
                                                    <span class="badge badge-success"
                                                        style="font-size: 1em">{{ number_format($attempt->score, 2) }}</span>
                                                @else
                                                    <span class="badge badge-secondary" style="font-size: 1em"><i
                                                            class="fas fa-eye-slash mr-1"></i> Disembunyikan</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ request()->route('subdomain') ? route('institution.student.history.show', ['subdomain' => request()->route('subdomain'), 'exam_session' => $attempt->id]) : route('student.history.show', $attempt->id) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $attempts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection