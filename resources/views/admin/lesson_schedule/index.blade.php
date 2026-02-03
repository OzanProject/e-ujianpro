@extends('layouts.admin.app')

@section('title', 'Jadwal Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Jadwal Pelajaran</h1>
        <a href="{{ route('admin.lesson_schedule.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Jadwal
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Content Row -->
    <div class="row">
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Jadwal</h6>
                </div>
                <div class="card-body">
                    <!-- Filter -->
                    <form method="GET" action="{{ route('admin.lesson_schedule.index') }}" class="form-inline mb-4">
                        <div class="form-group mr-2">
                            <select name="student_group_id" class="form-control">
                                <option value="">Semua Kelas</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ request('student_group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mr-2">
                            <select name="day" class="form-control">
                                <option value="">Semua Hari</option>
                                @foreach(['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'] as $day)
                                    <option value="{{ $day }}" {{ request('day') == $day ? 'selected' : '' }}>{{ ucfirst($day) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        @if(request()->has('student_group_id') || request()->has('day'))
                            <a href="{{ route('admin.lesson_schedule.index') }}" class="btn btn-secondary ml-2">Reset</a>
                        @endif
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Hari</th>
                                    <th>Jam</th>
                                    <th>Kelas</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedules as $schedule)
                                    <tr>
                                        <td>{{ ucfirst($schedule->day) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                                        <td>{{ $schedule->studentGroup->name }}</td>
                                        <td>{{ $schedule->subject->name }}</td>
                                        <td>
                                            @if($schedule->teacher)
                                                {{ $schedule->teacher->name }}
                                            @else
                                                <span class="badge badge-secondary">Tidak diset</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.lesson_schedule.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada jadwal pelajaran.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $schedules->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
