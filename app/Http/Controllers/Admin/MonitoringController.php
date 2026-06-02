<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ExamSession;
use App\Models\Subject;

class MonitoringController extends Controller
{
    protected function getBaseRoute()
    {
        return auth()->user()->role === 'pengajar' ? 'pengajar.monitoring' : 'admin.monitoring';
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Scope by Subjects owned by this user (Admin Lembaga) or Assigned (Teacher)
        if ($user->role === 'pengajar') {
            $subjectIds = $user->subjects->pluck('id');
        } else {
            // Admin/Operator: Filter by their subjects
            $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
            $subjectIds = Subject::where('created_by', $creatorId)->pluck('id');
        }

        $query = ExamSession::whereIn('subject_id', $subjectIds)->with(['subject', 'examPackage']);

        // Filter Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('token', 'like', '%' . $request->search . '%')
                  ->orWhere('target_kelas', 'like', '%' . $request->search . '%');
            });
        }

        // Filter Subject
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Filter Status
        if ($request->filled('status')) {
            $now = now();
            if ($request->status === 'active') {
                $query->where('start_time', '<=', $now)->where('end_time', '>=', $now);
            } elseif ($request->status === 'upcoming') {
                $query->where('start_time', '>', $now);
            } elseif ($request->status === 'completed') {
                $query->where('end_time', '<', $now);
            }
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        if ($sort === 'latest') {
            $query->latest();
        } elseif ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'start_asc') {
            $query->orderBy('start_time', 'asc');
        } elseif ($sort === 'start_desc') {
            $query->orderBy('start_time', 'desc');
        } else {
            $query->latest();
        }

        $sessions = $query->paginate(10)->withQueryString();
        
        $subjects = Subject::whereIn('id', $subjectIds)->orderBy('name')->get();

        $baseRoute = $this->getBaseRoute();
        return view('admin.monitoring.index', compact('sessions', 'baseRoute', 'subjects'));
    }
}
