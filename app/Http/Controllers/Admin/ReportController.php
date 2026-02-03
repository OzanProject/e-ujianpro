<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{

    public function index()
    {
        return view('admin.report.index');
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
             // If Admin Lembaga, view own subjects. If Operator, view parent's subjects.
             $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
             $subjectIds = \App\Models\Subject::where('created_by', $creatorId)->pluck('id');
             $query->whereIn('subject_id', $subjectIds);
        }

        $sessions = $query->get();

        // Stats
        $stats = [
            'total' => $sessions->count(),
            'active' => $sessions->where('is_active', 1)->count(),
            'upcoming' => $sessions->filter(fn($s) => $s->start_time > now())->count(),
            'finished' => $sessions->filter(fn($s) => $s->end_time < now())->count(),
        ];

        return view('admin.report.exam_schedule', compact('sessions', 'startDate', 'endDate', 'stats'));
    }

    public function printExamSchedule(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $user = Auth::user();
        
        $query = ExamSession::with(['subject', 'examPackage'])
            ->whereBetween('start_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('start_time');
            
        // Scoping (Same as main view)
        if ($user->role === 'pengajar') {
            $query->whereIn('subject_id', $user->subjects->pluck('id'));
        } else {
             $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
             $subjectIds = \App\Models\Subject::where('created_by', $creatorId)->pluck('id');
             $query->whereIn('subject_id', $subjectIds);
        }

        $sessions = $query->get();
        $sessions = $query->get();
        // Fix Institution: Get creator's institution
        $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
        $institution = \App\Models\Institution::where('user_id', $creatorId)->first();

        // Fallback for Teachers: Get institution via assigned subject creator or their own user_id if they belong to one
        if (!$institution && $user->role === 'pengajar') {
             // Try to find institution via first subject's creator
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
        $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
        $rooms = \App\Models\ExamRoom::where('created_by', $creatorId)->get();
        
        return view('admin.report.desk_card.index', compact('rooms'));
    }

    public function printDeskCard(Request $request)
    {
        $user = Auth::user();
        $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
        
        $query = \App\Models\Student::with('examRoom', 'group')
                    ->where('created_by', $creatorId);
        
        if ($request->has('exam_room_id') && $request->exam_room_id != 'all') {
            if ($request->exam_room_id == 'null') {
                $query->whereNull('exam_room_id');
            } else {
                $query->where('exam_room_id', $request->exam_room_id);
            }
        }
        
        // Logical Sort
        $students = \App\Models\StudentGroup::sortCollection($query->get()); // Re-use group sort logic? No, sort logic is for groups.
        // For students, we probably want to sort by Name or NIS? 
        // Let's sort by Group Name then Name.
        $students = $query->get()->sortBy(function($student) {
             return sprintf('%s-%s', $student->group->name ?? 'ZZZ', $student->name);
        });

        // Fix Institution
        $institution = \App\Models\Institution::where('user_id', $creatorId)->first();
        
        $roomName = $request->exam_room_id && $request->exam_room_id != 'all' && $request->exam_room_id != 'null' 
                    ? \App\Models\ExamRoom::find($request->exam_room_id)->name 
                    : 'Semua Ruangan';

        return view('admin.report.desk_card.print', compact('students', 'institution', 'roomName'));
    }

    public function attendanceIndex()
    {
        $user = Auth::user();
        $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
        $rooms = \App\Models\ExamRoom::where('created_by', $creatorId)->get();
        // Fetch upcoming/recent sessions for selection
        // Scope by Subjects owned/assigned
        $subjectIds = $user->role === 'pengajar' 
                        ? $user->subjects->pluck('id')
                        : \App\Models\Subject::where('created_by', $creatorId)->pluck('id');

        $sessions = ExamSession::with('subject')
            ->whereIn('subject_id', $subjectIds)
            ->where('start_time', '>=', now()->subDays(7)) // Show sessions from last week onwards
            ->orderBy('start_time', 'asc')
            ->get();
            
        return view('admin.report.attendance.index', compact('rooms', 'sessions'));
    }

    public function printAttendance(Request $request)
    {
        $user = Auth::user();
        $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
        
        // Check if printing Proctor List
        if ($request->has('type') && $request->type == 'proctor') {
             return $this->printAttendanceProctor($request);
        }

        $request->validate([
             'exam_room_id' => 'required',
        ]);

        $room = null;
        if ($request->exam_room_id != 'all' && $request->exam_room_id != 'null') {
             $room = \App\Models\ExamRoom::find($request->exam_room_id);
        }

        $session = null;
        if ($request->has('exam_session_id') && $request->exam_session_id) {
             $session = ExamSession::with('subject')->find($request->exam_session_id);
        }

        $query = \App\Models\Student::with('examRoom', 'group')
                    ->where('created_by', $creatorId);
        
        if ($request->exam_room_id != 'all') {
            if ($request->exam_room_id == 'null') {
                $query->whereNull('exam_room_id');
            } else {
                $query->where('exam_room_id', $request->exam_room_id);
            }
        }
        
        // Logical Sort
        $students = $query->get()->sortBy(function($student) {
             return sprintf('%s-%s', $student->group->name ?? 'ZZZ', $student->name);
        });

        $institution = \App\Models\Institution::where('user_id', $creatorId)->first();

        return view('admin.report.attendance.print', compact('students', 'institution', 'room', 'session'));
    }

    public function attendanceProctorIndex()
    {
        $user = Auth::user();
        $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
        
        $subjectIds = $user->role === 'pengajar' 
                        ? $user->subjects->pluck('id')
                        : \App\Models\Subject::where('created_by', $creatorId)->pluck('id');

        $sessions = ExamSession::with('subject')
            ->whereIn('subject_id', $subjectIds)
            ->where('start_time', '>=', now()->subDays(7))
            ->orderBy('start_time', 'asc')
            ->get();
            
        return view('admin.report.attendance_proctor.index', compact('sessions'));
    }

    public function printAttendanceProctor(Request $request)
    {
        $user = Auth::user();
        $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
        
        // Get Proctors created by this admin
        $proctors = \App\Models\User::where('role', 'proctor')
                    ->where('created_by', $creatorId)
                    ->with('examRoom')
                    ->get();
                    
        $session = null;
        if ($request->has('exam_session_id') && $request->exam_session_id) {
             $session = ExamSession::with('subject')->find($request->exam_session_id);
        }
        
        $institution = \App\Models\Institution::where('user_id', $creatorId)->first();

        return view('admin.report.attendance.print_proctor', compact('proctors', 'institution', 'session'));
    }
}
