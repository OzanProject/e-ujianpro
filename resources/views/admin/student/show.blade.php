@extends('layouts.admin.app')

@section('title', 'Detail Peserta')
@section('page_title', 'Detail Peserta')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    @if($student->photo && file_exists(public_path('storage/' . $student->photo)))
                        <img class="profile-user-img img-fluid img-circle"
                             src="{{ asset('storage/' . $student->photo) }}"
                             alt="User profile picture"
                             style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <img class="profile-user-img img-fluid img-circle"
                             src="{{ asset('dist/img/user2-160x160.jpg') }}"
                             alt="User profile picture">
                    @endif
                </div>

                <h3 class="profile-username text-center">{{ $student->name }}</h3>
                <p class="text-muted text-center">{{ $student->nis }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Kelompok</b> <a class="float-right">{{ $student->group->name ?? '-' }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Kelas</b> <a class="float-right">{{ $student->kelas ?? '-' }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Jurusan</b> <a class="float-right">{{ $student->jurusan ?? '-' }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Ruangan</b> <a class="float-right">{{ $student->examRoom->name ?? 'Belum Ada' }}</a>
                    </li>
                </ul>
                
                <a href="{{ route('admin.student.edit', $student->id) }}" class="btn btn-warning btn-block"><b>Edit Profil</b></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header p-2">
                <h3 class="card-title">Informasi Lengkap</h3>
            </div>
            <div class="card-body">
                <form class="form-horizontal">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Nama Lengkap</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">{{ $student->name }}</p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">NIS</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">{{ $student->nis }}</p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Kelompok</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">{{ $student->group->name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Ruangan</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">{{ $student->examRoom->name ?? 'Belum Ada' }}</p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Kelas</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">{{ $student->kelas ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Jurusan</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">{{ $student->jurusan ?? '-' }}</p>
                        </div>
                    </div>
                     <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Bergabung Sejak</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">{{ $student->created_at->format('d F Y H:i') }}</p>
                        </div>
                    </div>
                </form>
                
                <hr>
                
                <a href="{{ route('admin.student.index') }}" class="btn btn-default">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection
