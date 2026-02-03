@extends('layouts.admin.app')

@section('title', 'Data Peserta')
@section('page_title', 'Data Peserta')

@section('content')

@push('styles')
    <style>
        .card-amount {
            font-size: 40px;
            font-weight: bold;
        }
        .card-title {
            font-size: 16px;
            font-weight: normal;
            color: #555;
        }
        .card.alert-warning {
            background-color: #fcf8e3;
            border-color: #faebcc;
            color: #8a6d3b;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            box-shadow: 0 1px 1px rgba(0,0,0,.05);
        }
        .alert-info {
            color: #31708f;
            background-color: #d9edf7;
            border-color: #bce8f1;
        }
    </style>
@endpush

    <!-- Actions / Toolbar (Replaces the Page Header buttons) -->
    <div class="row mb-3">
        <div class="col-12 text-right">
                <button type="button" class="btn btn-default btn-outline-secondary" data-toggle="modal" data-target="#filterModal"><i class="fa fa-filter"></i> Filter</button> 
                <a href="{{ route('admin.student.create') }}" class="btn btn-success">Tambah Baru</a> 
                
                <!-- Import Button -->
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#importExcelModal"><i class="fas fa-file-excel"></i> Import Excel</button>

                <a href="{{ route('admin.student.export') }}" class="btn btn-default btn-outline-secondary">Export Data</a> 
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Broadcast <span class="caret"></span>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#broadcastEmailModal">Email</a>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#broadcastWhatsappModal">Whatsapp</a>
                    </div>
                </div> 
                
                <a href="#" onclick="if(confirm('Apakah Anda yakin akan menghapus SEMUA data peserta? Peringatan: Data yang dihapus tidak dapat dikembalikan!')) { document.getElementById('delete-all-form').submit(); }" class="btn btn-danger">Hapus Semua Peserta</a> 
                <form id="delete-all-form" action="{{ route('admin.student.delete_all') }}" method="POST" style="display: none;">
                    @csrf
                </form>

                <a href="{{ route('admin.student.cards') }}" target="_blank" class="btn btn-default btn-outline-secondary">Kartu Peserta</a> 
                <a href="{{ route('admin.student.upload_photo') }}" class="btn btn-default btn-outline-secondary">Upload Foto</a>							
        </div>
    </div>

    <!-- Info Alert -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info alert-dismissible" style="margin-bottom: 20px;">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h4><i class="icon fa fa-info-circle"></i> Informasi</h4>
                <span class="">Anda dapat mengunduh daftar peserta dengan menekan tombol Export pada halaman ini. Jika ingin mengunduh data spesifik (misal kelompok tertentu), lakukan filtering data terlebih dahulu dengan tombol Filter.</span>
            </div>
        </div>
    </div>

    <!-- Stats Info Boxes -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Peserta</span>
                    <span class="info-box-number">
                        {{ $students->total() }}
                        <small>Siswa</small>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box shadow-sm mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-chalkboard-teacher"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Guru / Pengawas</span>
                    <span class="info-box-number">
                        {{ \App\Models\User::where('created_by', auth()->id())->whereRole('proctor')->count() }}
                        <small>Akun</small>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box shadow-sm mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-door-open"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Ruangan Ujian</span>
                    <span class="info-box-number">
                        {{ $rooms->count() }}
                        <small>Ruangan</small>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
             <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th style="width: 60px">Foto</th>
                                    <th>Nama Lengkap</th>
                                    <th>NIS</th>
                                    <th>Ruangan</th>
                                    <th>Kelompok</th>
                                    <th>Kelas</th>
                                    <th style="width: 120px" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr>
                                        <td>{{ $loop->iteration + $students->firstItem() - 1 }}</td>
                                        <td class="text-center">
                                            @if($student->photo)
                                                <img src="{{ asset('storage/' . $student->photo) }}" class="img-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle" style="width: 40px; height: 40px; object-fit: cover; opacity: 0.5;">
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="font-weight-bold">{{ $student->name }}</span>
                                                <small class="text-muted">{{ $student->email }}</small>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-light border">{{ $student->nis ?? '-' }}</span></td>
                                        <td>
                                            @if($student->examRoom)
                                                <span class="badge badge-primary px-2 py-1"><i class="fas fa-door-open mr-1"></i> {{ $student->examRoom->name }}</span>
                                            @else
                                                <span class="badge badge-warning text-white px-2 py-1"><i class="fas fa-exclamation-circle mr-1"></i> Belum Ada</span>
                                            @endif
                                        </td>
                                        <td>{{ $student->group->name ?? '-' }}</td>
                                        <td>{{ $student->kelas ?? '-' }} {{ $student->jurusan ? '/ '.$student->jurusan : '' }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('admin.student.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Hapus peserta ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <a href="{{ route('admin.student.show', $student->id) }}" class="btn btn-info btn-xs" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.student.edit', $student->id) }}" class="btn btn-warning btn-xs" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="submit" class="btn btn-danger btn-xs" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="alert alert-warning" style="margin: 10px;">
                                                <h4><i class="icon fa fa-warning"></i> Perhatian!</h4>
                                                Tidak ada data yang sesuai dengan kriteria pencarian Anda.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="pull-right">
                        {{ $students->appends(request()->input())->links() }}
                    </div>
                </div>
             </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="filterModalLabel">Filter Data Peserta</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="{{ route('admin.student.index') }}" method="GET">
              <div class="modal-body">
                <div class="form-group">
                    <label>Pencarian (Nama/NIS)</label>
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Cari Nama atau NIS...">
                </div>
                <div class="form-group">
                    <label>Kelompok Peserta (Rombel)</label>
                    <select name="student_group_id" class="form-control select2">
                        <option value="">-- Semua Kelompok --</option>
                        @foreach($groups as $grp)
                            <option value="{{ $grp->id }}" {{ request('student_group_id') == $grp->id ? 'selected' : '' }}>{{ $grp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Ruangan Ujian</label>
                    <select name="exam_room_id" class="form-control select2">
                        <option value="">-- Semua Status --</option>
                        <option value="null" {{ request('exam_room_id') == 'null' ? 'selected' : '' }}>Belum Punya Ruangan</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ request('exam_room_id') == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Kelas</label>
                    <input type="text" name="kelas" class="form-control" value="{{ request('kelas') }}" placeholder="contoh: X-IPA, Kelas 1, dll">
                </div>
              </div>
              <div class="modal-footer">
                <a href="{{ route('admin.student.index') }}" class="btn btn-secondary">Reset</a>
                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
              </div>
          </form>
        </div>
        </div>
      </div>

    <!-- Broadcast Email Modal -->
    <div class="modal fade" id="broadcastEmailModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Broadcast Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.student.broadcast.email') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Subjek Email</label>
                            <input type="text" name="subject" class="form-control" placeholder="Contoh: Informasi Ujian" required>
                        </div>
                        <div class="form-group">
                            <label>Pesan</label>
                            <textarea name="message" class="form-control" rows="5" placeholder="Tulis pesan Anda disini..." required></textarea>
                        </div>
                        <div class="alert alert-info">
                            <small>Sistem akan mengirim email ke semua peserta yang memiliki alamat email.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Kirim Email</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Broadcast Whatsapp Modal -->
    <div class="modal fade" id="broadcastWhatsappModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Broadcast Whatsapp</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.student.broadcast.whatsapp') }}" method="POST" target="_blank">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pesan Whatsapp</label>
                            <textarea name="message" class="form-control" rows="5" placeholder="Halo {name}, username ujian kamu adalah {nis}." required></textarea>
                            <small class="text-muted">Gunakan <code>{name}</code> untuk nama siswa dan <code>{nis}</code> untuk NIS.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Lanjutkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Excel Modal -->
    <div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data Peserta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.student.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pilih Kelompok / Kelas (Opsional)</label>
                            <select name="student_group_id" id="import_student_group_id" class="form-control">
                                <option value="">-- Semua / Umum --</option>
                                @foreach($groups as $grp)
                                    <option value="{{ $grp->id }}">{{ $grp->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Jika dipilih, semua siswa yang diimport akan masuk ke kelompok ini.</small>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> Gunakan template yang disediakan.
                            <br>
                            <a href="{{ route('admin.student.template') }}" id="btnDownloadTemplate" class="btn btn-sm btn-info mt-2">
                                <i class="fa fa-download"></i> Download Template Excel
                            </a>
                        </div>
                        
                        <div class="form-group">
                            <label>File Excel (.xlsx)</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> Import Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#import_student_group_id').change(function() {
                var groupId = $(this).val();
                var baseUrl = "{{ route('admin.student.template') }}";
                
                if(groupId) {
                    $('#btnDownloadTemplate').attr('href', baseUrl + '?student_group_id=' + groupId);
                } else {
                    $('#btnDownloadTemplate').attr('href', baseUrl);
                }
            });
        });
    </script>
    @endpush
@endsection
