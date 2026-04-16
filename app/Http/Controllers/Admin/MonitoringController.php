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

    public function index()
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

        $sessions = ExamSession::whereIn('subject_id', $subjectIds)
            ->with(['subject', 'examPackage'])
            ->latest()
            ->paginate(10);

        $baseRoute = $this->getBaseRoute();
        return view('admin.monitoring.index', compact('sessions', 'baseRoute'));
    }
}
