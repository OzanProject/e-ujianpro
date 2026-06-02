<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    protected function getBaseRoute()
    {
        return auth()->user()->role === 'pengajar' ? 'pengajar.report' : 'admin.report';
    }

    public function index()
    {
        $baseRoute = $this->getBaseRoute();
        return view('admin.report.index', compact('baseRoute'));
    }

    public function examSchedule(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        
        $user = Auth::user();
        $query = ExamSession::with(['subject', 'examPackage'])
                    ->whereBetween('start_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->orderBy('start_time');

        // Scoping
        if ($user->role === 'pengajar') {
            $query->whereIn('subject_id', $user->subjects->pluck('id'));
        } else {
             $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
             $subjectIds = \App\Models\Subject::where('created_by', $creatorId)->pluck('id');
             $query->whereIn('subject_id', $subjectIds);
        }

        $sessions = $query->get();
        $baseRoute = $this->getBaseRoute();

        // Stats
        $stats = [
            'total' => $sessions->count(),
            'active' => $sessions->where('is_active', 1)->count(),
            'upcoming' => $sessions->filter(fn($s) => $s->start_time > now())->count(),
            'finished' => $sessions->filter(fn($s) => $s->end_time < now())->count(),
        ];

        return view('admin.report.exam_schedule', compact('sessions', 'startDate', 'endDate', 'stats', 'baseRoute'));
    }

    public function printExamSchedule(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $user = Auth::user();
        
        $query = ExamSession::with(['subject', 'examPackage'])
            ->whereBetween('start_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('start_time');
            
        // Scoping
        if ($user->role === 'pengajar') {
            $query->whereIn('subject_id', $user->subjects->pluck('id'));
        } else {
             $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
             $subjectIds = \App\Models\Subject::where('created_by', $creatorId)->pluck('id');
             $query->whereIn('subject_id', $subjectIds);
        }

        $sessions = $query->get();
        $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
        $institution = \App\Models\Institution::where('user_id', $creatorId)->first();

        // Fallback for Teachers
        if (!$institution && $user->role === 'pengajar') {
             $firstSubject = $user->subjects->first();
             if ($firstSubject) {
                  $institution = \App\Models\Institution::where('user_id', $firstSubject->created_by)->first();
             }
        }

        return view('admin.report.print_exam_schedule', compact('sessions', 'startDate', 'endDate', 'institution'));
    }

    public function deskCardIndex()
    {
        $user = Auth::user();
        $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
        
        $allIds = array_merge([$creatorId], \App\Models\User::where('created_by', $creatorId)->pluck('id')->toArray());
        $rooms = \App\Models\ExamRoom::whereIn('created_by', $allIds)->get();
        
        $baseRoute = $this->getBaseRoute();
        return view('admin.report.desk_card.index', compact('rooms', 'baseRoute'));
    }

    public function printDeskCard(Request $request)
    {
        $user = Auth::user();
        $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
        
        $ownerIds = [$creatorId];
        $subUserIds = \App\Models\User::where('created_by', $creatorId)->pluck('id')->toArray();
        $allIds = array_merge($ownerIds, $subUserIds);

        $query = \App\Models\Student::with('examRoom', 'group')
                    ->whereIn('created_by', $allIds);
        
        if ($request->has('exam_room_id') && $request->exam_room_id != 'all') {
            if ($request->exam_room_id == 'null') {
                $query->whereNull('exam_room_id');
            } else {
                $query->where('exam_room_id', $request->exam_room_id);
            }
        }
        
        $students = $query->get()->sortBy(function($student) {
             return sprintf('%s-%s', $student->group->name ?? 'ZZZ', $student->name);
        });

        $institution = \App\Models\Institution::where('user_id', $creatorId)->first();
        
        $roomName = $request->exam_room_id && $request->exam_room_id != 'all' && $request->exam_room_id != 'null' 
                    ? \App\Models\ExamRoom::find($request->exam_room_id)->name 
                    : 'Semua Ruangan';

        return view('admin.report.desk_card.print', compact('students', 'institution', 'roomName'));
    }

    public function attendanceIndex()
    {
        $user = Auth::user();
        $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
        
        $allIds = array_merge([$creatorId], \App\Models\User::where('created_by', $creatorId)->pluck('id')->toArray());
        
        $rooms = \App\Models\ExamRoom::whereIn('created_by', $allIds)->get();

        $subjectIds = $user->role === 'pengajar' 
                        ? $user->subjects->pluck('id')
                        : \App\Models\Subject::whereIn('created_by', $allIds)->pluck('id');

        $sessions = ExamSession::with('subject')
            ->whereIn('subject_id', $subjectIds)
            ->where('start_time', '>=', now()->subDays(7)) 
            ->orderBy('start_time', 'asc')
            ->get();
            
        $baseRoute = $this->getBaseRoute();
        return view('admin.report.attendance.index', compact('rooms', 'sessions', 'baseRoute'));
    }

    public function printAttendance(Request $request)
    {
        $user = Auth::user();
        $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
        
        if ($request->has('type') && $request->type == 'proctor') {
             return $this->printAttendanceProctor($request);
        }

        $request->validate([
             'exam_room_id' => 'required',
        ]);

        $ownerIds = [$creatorId];
        $subUserIds = \App\Models\User::where('created_by', $creatorId)->pluck('id')->toArray();
        $allIds = array_merge($ownerIds, $subUserIds);

        $subjectIds = $user->role === 'pengajar' 
                        ? $user->subjects->pluck('id')
                        : \App\Models\Subject::whereIn('created_by', $allIds)->pluck('id');

        $targetSessions = collect();
        if ($request->filled('exam_session_id')) {
            $session = ExamSession::with('subject')->find($request->exam_session_id);
            if ($session) $targetSessions->push($session);
        } elseif ($request->filled('exam_date')) {
            $targetSessions = ExamSession::with('subject')
                ->whereIn('subject_id', $subjectIds)
                ->whereDate('start_time', $request->exam_date)
                ->orderBy('start_time')
                ->get();
        } else {
            $targetSessions->push(null);
        }

        $query = \App\Models\Student::with('examRoom', 'group')
                    ->whereIn('created_by', $allIds);
        
        if ($request->exam_room_id != 'all') {
            if ($request->exam_room_id == 'null') {
                $query->whereNull('exam_room_id');
            } else {
                $query->where('exam_room_id', $request->exam_room_id);
            }
        } else {
            // Jika pilih Semua Ruangan, JANGAN cetak siswa yang belum punya ruangan
            // agar tidak membuang kertas dengan ratusan nama tak beruang.
            $query->whereNotNull('exam_room_id');
        }
        
        $students = $query->get()->sortBy(function($student) {
             return sprintf('%s-%s', $student->group->name ?? 'ZZZ', $student->name);
        });

        if ($request->exam_room_id == 'all') {
            $studentsByRoom = $students->groupBy('exam_room_id');
        } else {
            $studentsByRoom = collect([ $request->exam_room_id == 'null' ? '' : $request->exam_room_id => $students ]);
        }

        $institution = \App\Models\Institution::where('user_id', $creatorId)->first();

        return view('admin.report.attendance.print', compact('targetSessions', 'studentsByRoom', 'institution'));
    }

    public function attendanceProctorIndex()
    {
        $user = Auth::user();
        $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
        
        $subjectIds = $user->role === 'pengajar' 
                        ? $user->subjects->pluck('id')
                        : \App\Models\Subject::where('created_by', $creatorId)->pluck('id');

        $sessions = ExamSession::with('subject')
            ->whereIn('subject_id', $subjectIds)
            ->where('start_time', '>=', now()->subDays(7))
            ->orderBy('start_time', 'asc')
            ->get();
            
        $rooms = \App\Models\ExamRoom::where('created_by', $creatorId)->get();
            
        $baseRoute = $this->getBaseRoute();
        return view('admin.report.attendance_proctor.index', compact('sessions', 'rooms', 'baseRoute'));
    }

    public function printAttendanceProctor(Request $request)
    {
        $user = Auth::user();
        $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
        
        $session = null;
        if ($request->has('exam_session_id') && $request->exam_session_id) {
             $session = ExamSession::with('subject', 'proctors')->find($request->exam_session_id);
        }

        $room = null;
        if ($request->has('exam_room_id') && $request->exam_room_id != 'all' && $request->exam_room_id != 'null') {
             $room = \App\Models\ExamRoom::find($request->exam_room_id);
        }

        if ($session) {
            $proctorsQuery = $session->proctors();
            if ($room) {
                $proctorsQuery->wherePivot('exam_room_id', $room->id);
            }
            $proctors = $proctorsQuery->withPivot('exam_room_id')->get();
        } else {
            $query = \App\Models\User::whereIn('role', ['pengajar', 'proctor'])->where('created_by', $creatorId)->with('examRoom');
            if ($room) {
                $query->where('exam_room_id', $room->id);
            }
            $proctors = $query->get();
        }
        
        $institution = \App\Models\Institution::where('user_id', $creatorId)->first();

        return view('admin.report.attendance.print_proctor', compact('proctors', 'institution', 'session', 'room'));
    }
    // --- BERITA ACARA PELAKSANAAN ---
    public function beritaAcaraIndex()
    {
        $user = Auth::user();
        $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
        $rooms = \App\Models\ExamRoom::where('created_by', $creatorId)->get();

        $subjectIds = $user->role === 'pengajar' 
                        ? $user->subjects->pluck('id')
                        : \App\Models\Subject::where('created_by', $creatorId)->pluck('id');

        $sessions = ExamSession::with('subject')
            ->whereIn('subject_id', $subjectIds)
            ->where('start_time', '>=', now()->subDays(7)) 
            ->orderBy('start_time', 'asc')
            ->get();
            
        $baseRoute = $this->getBaseRoute();
        return view('admin.report.berita_acara.index', compact('rooms', 'sessions', 'baseRoute'));
    }

    public function printBeritaAcara(Request $request)
    {
        $user = Auth::user();
        $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
        
        $request->validate([
             'exam_room_id' => 'required',
        ]);

        $subjectIds = $user->role === 'pengajar' 
                        ? $user->subjects->pluck('id')
                        : \App\Models\Subject::where('created_by', $creatorId)->pluck('id');

        $targetSessions = collect();
        if ($request->filled('exam_date')) {
            $targetSessions = ExamSession::with('subject', 'examType')
                ->whereIn('subject_id', $subjectIds)
                ->whereDate('start_time', $request->exam_date)
                ->orderBy('start_time')
                ->get();
        } elseif ($request->filled('exam_session_id')) {
            $session = ExamSession::with('subject', 'examType')
                ->whereIn('subject_id', $subjectIds)
                ->findOrFail($request->exam_session_id);
            $targetSessions->push($session);
        } else {
            return back()->with('error', 'Pilih Tanggal Ujian atau Jadwal Sesi.');
        }
        
        $targetRooms = collect();
        if ($request->exam_room_id == 'all') {
             $targetRooms = \App\Models\ExamRoom::where('created_by', $creatorId)->get();
        } else {
             $room = \App\Models\ExamRoom::where('created_by', $creatorId)->findOrFail($request->exam_room_id);
             $targetRooms->push($room);
        }
        
        $institution = \App\Models\Institution::where('user_id', $creatorId)->first();

        return view('admin.report.berita_acara.print', compact('targetSessions', 'targetRooms', 'institution'));
    }

    // --- TATA TERTIB ---
    public function tataTertibPeserta()
    {
        $user = Auth::user();
        $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
        $institution = \App\Models\Institution::where('user_id', $creatorId)->first();
        
        return view('admin.report.tata_tertib.peserta', compact('institution'));
    }

    public function tataTertibPengawas()
    {
        $user = Auth::user();
        $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
        $institution = \App\Models\Institution::where('user_id', $creatorId)->first();
        
        return view('admin.report.tata_tertib.pengawas', compact('institution'));
    }

    // --- DENAH RUANG / PENEMPATAN PESERTA ---
    public function denahRuangIndex()
    {
        $user = Auth::user();
        $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
        $rooms = \App\Models\ExamRoom::where('created_by', $creatorId)->get();
        
        $baseRoute = $this->getBaseRoute();
        return view('admin.report.denah_ruang.index', compact('rooms', 'baseRoute'));
    }

    public function printDenahRuang(Request $request)
    {
        $user = Auth::user();
        $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
        
        $request->validate([
             'exam_room_id' => 'required',
        ]);

        $room = \App\Models\ExamRoom::where('created_by', $creatorId)->findOrFail($request->exam_room_id);
        
        $students = \App\Models\Student::with('group')
                    ->where('created_by', $creatorId)
                    ->where('exam_room_id', $room->id)
                    ->get()
                    ->sortBy(function($student) {
                         return sprintf('%s-%s', $student->group->name ?? 'ZZZ', $student->name);
                    })->values();

        $institution = \App\Models\Institution::where('user_id', $creatorId)->first();

        return view('admin.report.denah_ruang.print', compact('room', 'students', 'institution'));
    }
    public function studentRoomIndex()
    {
        $user = Auth::user();
        $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
        
        $allIds = array_merge([$creatorId], \App\Models\User::where('created_by', $creatorId)->pluck('id')->toArray());
        $rooms = \App\Models\ExamRoom::whereIn('created_by', $allIds)->get();
        
        $baseRoute = $this->getBaseRoute();
        return view('admin.report.student_room.index', compact('rooms', 'baseRoute'));
    }

    public function studentRoomPrint(Request $request)
    {
        $request->validate([
            'exam_room_id' => 'required|exists:exam_rooms,id'
        ]);

        $user = Auth::user();
        $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
        
        $room = \App\Models\ExamRoom::with(['students' => function($q) {
            $q->orderBy('name', 'asc');
        }])->findOrFail($request->exam_room_id);

        $institution = \App\Models\Institution::where('user_id', $creatorId)->first();

        return view('admin.report.student_room.print', compact('room', 'institution'));
    }

    public function dailyLogIndex()
    {
        $user = Auth::user();
        $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
        
        $allIds = array_merge([$creatorId], \App\Models\User::where('created_by', $creatorId)->pluck('id')->toArray());
        
        $rooms = \App\Models\ExamRoom::whereIn('created_by', $allIds)->get();

        $subjectIds = $user->role === 'pengajar' 
                        ? $user->subjects->pluck('id')
                        : \App\Models\Subject::whereIn('created_by', $allIds)->pluck('id');

        $sessions = ExamSession::with('subject')
            ->whereIn('subject_id', $subjectIds)
            ->where('start_time', '>=', now()->subDays(7)) 
            ->orderBy('start_time', 'asc')
            ->get();
            
        $baseRoute = $this->getBaseRoute();
        return view('admin.report.daily_log.index', compact('rooms', 'sessions', 'baseRoute'));
    }

    public function printDailyLog(Request $request)
    {
        $user = Auth::user();
        $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
        
        $request->validate([
             'exam_room_id' => 'required',
        ]);

        $subjectIds = $user->role === 'pengajar' 
                        ? $user->subjects->pluck('id')
                        : \App\Models\Subject::where('created_by', $creatorId)->pluck('id');

        $targetSessions = collect();
        if ($request->filled('exam_date')) {
            $targetSessions = ExamSession::with('subject', 'examType')
                ->whereIn('subject_id', $subjectIds)
                ->whereDate('start_time', $request->exam_date)
                ->orderBy('start_time')
                ->get();
        } elseif ($request->filled('exam_session_id')) {
            $session = ExamSession::with('subject', 'examType')
                ->whereIn('subject_id', $subjectIds)
                ->findOrFail($request->exam_session_id);
            $targetSessions->push($session);
        } else {
            return back()->with('error', 'Pilih Tanggal Ujian atau Jadwal Sesi.');
        }
        
        $targetRooms = collect();
        if ($request->exam_room_id == 'all') {
             $targetRooms = \App\Models\ExamRoom::where('created_by', $creatorId)->get();
        } else {
             $room = \App\Models\ExamRoom::where('created_by', $creatorId)->findOrFail($request->exam_room_id);
             $targetRooms->push($room);
        }
        
        $institution = \App\Models\Institution::where('user_id', $creatorId)->first();

        return view('admin.report.daily_log.print', compact('targetSessions', 'targetRooms', 'institution'));
    }
}
