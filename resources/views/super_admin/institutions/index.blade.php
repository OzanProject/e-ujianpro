@extends('layouts.admin.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Sekolah / Lembaga</h1>
        <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#createModal">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Sekolah
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Sekolah</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th class="align-middle text-center" width="5%">No</th>
                            <th class="align-middle" width="30%">Institusi & Admin</th>
                            <th class="align-middle text-center" width="15%">Saldo Poin</th>
                            <th class="align-middle text-center" width="15%">Kuota Siswa</th>
                            <th class="align-middle text-center" width="10%">Status</th>
                            <th class="align-middle text-center" width="15%">Terdaftar</th>
                            <th class="align-middle text-center" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($institutions as $inst)
                        <tr>
                            <td class="align-middle text-center">{{ $loop->iteration + ($institutions->currentPage() - 1) * $institutions->perPage() }}</td>
                            <td class="align-middle">
                                <div class="font-weight-bold text-primary mb-1" style="font-size: 1.1em;">
                                    {{ $inst->institution ? $inst->institution->name : $inst->name }}
                                </div>
                                <div class="small text-muted">
                                    <i class="fas fa-user-shield fa-fw mr-1"></i> {{ $inst->name }}
                                </div>
                                <div class="small text-muted">
                                    <i class="fas fa-envelope fa-fw mr-1"></i> {{ $inst->email }}
                                </div>
                            </td>
                            <td class="align-middle text-center">
                                <span class="badge badge-success badge-pill px-3 py-2" style="font-size: 0.9em;">
                                    {{ number_format($inst->points_balance ?? 0) }}
                                </span>
                            </td>
                            <td class="align-middle text-center">
                                @if(is_null($inst->max_students))
                                    <span class="badge badge-info badge-pill">Unlimited</span>
                                @else
                                    <span class="badge badge-warning badge-pill">{{ $inst->max_students }} Siswa</span>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                @if($inst->status == 'active')
                                    <span class="badge badge-success badge-pill">Active</span>
                                @elseif($inst->status == 'pending')
                                    <span class="badge badge-warning badge-pill">Pending</span>
                                @else
                                    <span class="badge badge-danger badge-pill">{{ ucfirst($inst->status) }}</span>
                                @endif
                            </td>
                            <td class="align-middle text-center small">
                                {{ $inst->created_at->format('d M Y') }}<br>
                                <span class="text-muted">{{ $inst->created_at->format('H:i') }}</span>
                            </td>
                            <td class="align-middle text-center">
                                <div class="btn-group" role="group">
                                    @if($inst->status == 'pending')
                                        <form action="{{ route('admin.super.institutions.approve', $inst->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" title="Approve" data-toggle="tooltip">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @elseif($inst->status == 'active')
                                        <form action="{{ route('admin.super.institutions.suspend', $inst->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menonaktifkan sekolah ini? Admin tidak akan bisa login.');">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm" title="Suspend / Nonaktifkan" data-toggle="tooltip">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @elseif($inst->status == 'suspended')
                                        <form action="{{ route('admin.super.institutions.activate', $inst->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" title="Aktifkan Kembali" data-toggle="tooltip">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <button class="btn btn-info btn-sm ml-1" data-toggle="modal" data-target="#quotaModal{{ $inst->id }}" title="Edit Quota">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{ route('admin.super.institutions.destroy', $inst->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sekolah ini? Data yang dihapus tidak dapat dikembalikan.');" class="ml-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                
                                <!-- Modal Edit Quota (Moved inside loop but kept tidy) -->
                                <div class="modal fade text-left" id="quotaModal{{ $inst->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                  <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title">Edit Kuota - {{ $inst->name }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                          <span aria-hidden="true">&times;</span>
                                        </button>
                                      </div>
                                      <form action="{{ route('admin.super.institutions.update_quota', $inst->id) }}" method="POST">
                                          @csrf
                                          <div class="modal-body">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Saldo Poin</label>
                                                <input type="number" name="points_balance" class="form-control" value="{{ $inst->points_balance }}" min="0">
                                                <small class="form-text text-muted mb-3">Ubah saldo poin manual (Top Up).</small>
                                            </div>
                                            <div class="form-group">
                                                <label class="font-weight-bold">Maksimal Siswa (Kuota)</label>
                                                <small class="form-text text-muted mb-2">Kosongkan input untuk membuat kuota Unlimited.</small>
                                                <input type="number" name="max_students" class="form-control" value="{{ $inst->max_students }}" min="0" placeholder="Unlimited">
                                            </div>
                                          </div>
                                          <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                          </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $institutions->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Create School -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createModalLabel">Tambah Sekolah Baru</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('admin.super.institutions.store') }}" method="POST">
          @csrf
          <div class="modal-body">
            <h6 class="font-weight-bold text-primary mb-3">Informasi Sekolah</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Sekolah <span class="text-danger">*</span></label>
                        <input type="text" name="institution_name" class="form-control" required placeholder="Contoh: SMA Negeri 1 Jakarta">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Subdomain Aplikasi <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ url('/') }}/</span>
                            </div>
                            <input type="text" name="subdomain" class="form-control" required placeholder="namasekolah" pattern="[a-zA-Z0-9-]+" title="Hanya huruf, angka, dan strip (-)">
                        </div>
                        <small class="form-text text-muted">Link akses: {{ url('/') }}/namasekolah</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kota / Kabupaten <span class="text-danger">*</span></label>
                        <select name="city" class="form-control" required>
                            <option value="">Pilih Kota...</option>
                            <option value="Jakarta">Jakarta</option>
                            <option value="Bandung">Bandung</option>
                            <option value="Surabaya">Surabaya</option>
                            <option value="Yogyakarta">Yogyakarta</option>
                            <option value="Semarang">Semarang</option>
                            <option value="Medan">Medan</option>
                            <option value="Makassar">Makassar</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jenjang Sekolah <span class="text-danger">*</span></label>
                        <select name="type" class="form-control" required>
                            <option value="">Pilih Jenjang...</option>
                            <option value="SD">SD / MI</option>
                            <option value="SMP">SMP / MTs</option>
                            <option value="SMA">SMA / MA / SMK</option>
                            <option value="Universitas">Universitas</option>
                            <option value="Umum">Umum / Bimbel</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr>
            <h6 class="font-weight-bold text-primary mb-3">Informasi Akun Admin</h6>
            
            <div class="row">
                <div class="col-md-6">
                     <div class="form-group">
                        <label>Nama Admin (Lengkap) <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Nama Kepala Sekolah / Admin">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>No. WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" name="whatsapp" class="form-control" required placeholder="08123456789">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email Login <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="admin@sekolah.com">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required minlength="8" placeholder="Minimal 8 karakter">
                    </div>
                </div>
            </div>
            
            <div class="form-group mt-2">
                 <label>Kode Afiliasi / Referensi (Opsional)</label>
                 <input type="text" name="affiliate_code" class="form-control" placeholder="Jika ada">
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan & Aktifkan</button>
          </div>
      </form>
    </div>
  </div>
</div>
@endsection
