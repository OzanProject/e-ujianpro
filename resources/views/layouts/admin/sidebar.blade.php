<aside class="main-sidebar sidebar-dark-primary elevation-4">
    @php
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $baseRoute = $user->role === 'pengajar' ? 'pengajar' : 'admin';
        $baseRoutePrefix = $user->role === 'pengajar' ? 'pengajar' : 'admin';
    @endphp
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{ $globalInstitution && $globalInstitution->logo ? asset('storage/' . $globalInstitution->logo) : asset('dist/img/AdminLTELogo.png') }}"
            alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">{{ $globalInstitution->name ?? 'E-Ujian PRO' }}</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('dist/img/user2-160x160.jpg') }}"
                    class="img-circle elevation-2" alt="User Image"
                    style="object-fit: cover; width: 34px; height: 34px;">
            </div>
            <div class="info">
                <a href="{{ route('profile.edit') }}" class="d-block">{{ Str::upper(Str::limit($user->name, 20)) }}</a>
                <small class="text-white">{{ Str::title(str_replace('_', ' ', $user->role)) }}</small>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('admin.super.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.guide.index') }}"
                        class="nav-link {{ request()->routeIs('admin.guide.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-map-signs"></i>
                        <p>Panduan Sistem</p>
                    </a>
                </li>

                {{-- Group: Super Admin --}}
                @if($user->role === 'super_admin')
                    <li
                        class="nav-item {{ request()->routeIs('admin.super.institutions.*') || request()->routeIs('admin.super.points.*') || request()->routeIs('admin.super.announcements.*') || request()->routeIs('admin.super.settings.*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->routeIs('admin.super.institutions.*') || request()->routeIs('admin.super.points.*') || request()->routeIs('admin.super.announcements.*') || request()->routeIs('admin.super.settings.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-shield"></i>
                            <p>
                                Platform Admin
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.super.institutions.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.super.institutions.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data Sekolah</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.super.points.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.super.points.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Verifikasi Poin</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.super.announcements.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.super.announcements.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Pengumuman Sistem</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.super.settings.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.super.settings.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Pengaturan Aplikasi</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- Group: Manajemen Data (System) --}}

                {{-- Group: Manajemen Data (Master) --}}
                @if(in_array($user->role, ['admin_lembaga', 'operator']))
                    <li
                        class="nav-item {{ request()->routeIs('admin.institution.*') || request()->routeIs('admin.subject.*') || request()->routeIs('admin.exam_type.*') || request()->routeIs('admin.student_group.*') || request()->routeIs('admin.student.*') || request()->routeIs('admin.operator.*') || request()->routeIs('admin.point.*') || request()->routeIs('admin.score-scales.*') || request()->routeIs('admin.teacher.*') || request()->routeIs('admin.exam_room.*') || request()->routeIs('admin.proctor.*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->routeIs('admin.institution.*') || request()->routeIs('admin.subject.*') || request()->routeIs('admin.exam_type.*') || request()->routeIs('admin.student_group.*') || request()->routeIs('admin.student.*') || request()->routeIs('admin.operator.*') || request()->routeIs('admin.point.*') || request()->routeIs('admin.score-scales.*') || request()->routeIs('admin.teacher.*') || request()->routeIs('admin.exam_room.*') || request()->routeIs('admin.proctor.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-database"></i>
                            <p>
                                Manajemen Data
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @if($user->role === 'admin_lembaga')
                                <li class="nav-item">
                                    <a href="{{ route('admin.score-scales.index') }}"
                                        class="nav-link {{ request()->routeIs('admin.score-scales.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Konversi Skor</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.institution.index') }}"
                                        class="nav-link {{ request()->routeIs('admin.institution.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Data Lembaga</p>
                                    </a>
                                </li>
                            @endif

                            <li class="nav-item">
                                <a href="{{ route('admin.subject.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.subject.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Mata Pelajaran</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.exam_type.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.exam_type.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Jenis Ujian</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.student_group.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.student_group.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Kelompok Peserta</p>
                                </a>
                            </li>

                            @if($user->role === 'admin_lembaga')
                                <li class="nav-item">
                                    <a href="{{ route('admin.teacher.index') }}"
                                        class="nav-link {{ request()->routeIs('admin.teacher.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Data Guru</p>
                                    </a>
                                </li>
                            @endif

                            <li class="nav-item">
                                <a href="{{ route('admin.exam_room.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.exam_room.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data Ruangan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.proctor.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.proctor.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data Pengawas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.student.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.student.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data Peserta</p>
                                </a>
                            </li>

                            @if($user->role === 'admin_lembaga')
                                <li class="nav-item">
                                    <a href="{{ route('admin.operator.index') }}"
                                        class="nav-link {{ request()->routeIs('admin.operator.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Data Operator</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.point.index') }}"
                                        class="nav-link {{ request()->routeIs('admin.point.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Dompet Poin</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                {{-- Group: Bank Soal & Materi --}}
                @if(in_array($user->role, ['admin_lembaga', 'pengajar']))
                    <li
                        class="nav-item {{ request()->routeIs($baseRoute . '.question.*') || request()->routeIs($baseRoute . '.reading_text.*') || request()->routeIs($baseRoute . '.question_group.*') || request()->routeIs($baseRoute . '.learning_material.*') || request()->routeIs($baseRoute . '.exam_package.*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->routeIs($baseRoute . '.question.*') || request()->routeIs($baseRoute . '.reading_text.*') || request()->routeIs($baseRoute . '.question_group.*') || request()->routeIs($baseRoute . '.learning_material.*') || request()->routeIs($baseRoute . '.exam_package.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-book"></i>
                            <p>
                                Bank & Materi
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route($baseRoute . '.question.index') }}"
                                    class="nav-link {{ request()->routeIs($baseRoute . '.question.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Bank Soal</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route($baseRoute . '.reading_text.index') }}"
                                    class="nav-link {{ request()->routeIs($baseRoute . '.reading_text.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data Bacaan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route($baseRoute . '.question_group.index') }}"
                                    class="nav-link {{ request()->routeIs($baseRoute . '.question_group.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Grup Soal</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route($baseRoute . '.learning_material.index') }}"
                                    class="nav-link {{ request()->routeIs($baseRoute . '.learning_material.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Modul Materi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route($baseRoute . '.exam_package.index') }}"
                                    class="nav-link {{ request()->routeIs($baseRoute . '.exam_package.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Paket Soal</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- Group: Pelaksanaan Ujian --}}
                @if(in_array($user->role, ['admin_lembaga', 'pengajar', 'operator']))
                    <li
                        class="nav-item {{ request()->routeIs($baseRoute . '.exam_session.*') || request()->routeIs($baseRoute . '.correction.*') || request()->routeIs('proctor.*') || request()->routeIs($baseRoute . '.monitoring.index') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->routeIs($baseRoute . '.exam_session.*') || request()->routeIs($baseRoute . '.correction.*') || request()->routeIs('proctor.*') || request()->routeIs($baseRoute . '.monitoring.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-laptop-code"></i>
                            <p>
                                Pelaksanaan Ujian
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route($baseRoute . '.exam_session.index') }}"
                                    class="nav-link {{ request()->routeIs($baseRoute . '.exam_session.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Jadwal Ujian</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ in_array($user->role, ['admin_lembaga', 'operator', 'pengajar']) ? route($baseRoute . '.monitoring.index') : route('proctor.dashboard', ['subdomain' => request()->route('subdomain') ?? ($globalInstitution->subdomain ?? 'default')]) }}"
                                    class="nav-link {{ request()->routeIs('proctor.dashboard') || request()->routeIs($baseRoute . '.monitoring.index') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Monitoring Ujian</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route($baseRoute . '.correction.index') }}"
                                    class="nav-link {{ request()->routeIs($baseRoute . '.correction.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Koreksi Ujian</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- Group: Laporan & Hasil --}}
                @if(in_array($user->role, ['admin_lembaga', 'pengajar', 'operator']))
                    <li
                        class="nav-item {{ request()->routeIs($baseRoute . '.report.*') || request()->routeIs($baseRoute . '.recap.*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->routeIs($baseRoute . '.report.*') || request()->routeIs($baseRoute . '.recap.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-print"></i>
                            <p>
                                Laporan & Hasil
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route($baseRoute . '.report.exam_schedule') }}"
                                    class="nav-link {{ request()->routeIs($baseRoute . '.report.exam_schedule') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Cetak Jadwal Ujian</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route($baseRoute . '.recap.exam_result') }}"
                                    class="nav-link {{ request()->routeIs($baseRoute . '.recap.exam_result') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Hasil Ujian</p>
                                </a>
                            </li>

                            @if($user->role !== 'pengajar')
                                <li class="nav-item">
                                    <a href="{{ route($baseRoute . '.report.desk_card.index') }}"
                                        class="nav-link {{ request()->routeIs($baseRoute . '.report.desk_card.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Cetak Kartu Meja</p>
                                    </a>
                                </li>
                            @endif

                            <li class="nav-item">
                                <a href="{{ route('admin.report.attendance.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.report.attendance.index') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Cetak Daftar Hadir</p>
                                </a>
                            </li>

                            @if($user->role !== 'pengajar')
                                <li class="nav-item">
                                    <a href="{{ route('admin.report.attendance_proctor.index') }}"
                                        class="nav-link {{ request()->routeIs('admin.report.attendance_proctor.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Cetak Absen Pengawas</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</aside>