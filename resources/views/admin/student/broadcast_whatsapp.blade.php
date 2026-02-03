@extends('layouts.admin.app')

@section('title', 'Broadcast Whatsapp')
@section('page_title', 'Broadcast Whatsapp')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Kirim Pesan Whatsapp</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="icon fas fa-info"></i> Klik tombol <strong>"Kirim WA"</strong> di bawah ini untuk membuka aplikasi Whatsapp Web/Desktop dan mengirim pesan ke masing-masing peserta.
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nama Peserta</th>
                                <th>Nomor HP</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                @php
                                    // Format Message
                                    $msg = $request->message;
                                    $msg = str_replace('{name}', $student->name, $msg);
                                    $msg = str_replace('{nis}', $student->nis, $msg);
                                    $encodedMsg = urlencode($msg);
                                    
                                    // Format Phone (Ensure it starts with 62 or code)
                                    $phone = $student->phone_number;
                                    // Simple check/replace 0 with 62
                                    if(substr($phone, 0, 1) == '0') {
                                        $phone = '62' . substr($phone, 1);
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->phone_number }}</td>
                                    <td>
                                        <a href="https://wa.me/{{ $phone }}?text={{ $encodedMsg }}" target="_blank" class="btn btn-success btn-sm">
                                            <i class="fab fa-whatsapp"></i> Kirim WA
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <a href="{{ route('admin.student.index') }}" class="btn btn-default mt-3">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection
