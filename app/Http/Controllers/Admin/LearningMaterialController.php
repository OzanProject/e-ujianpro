<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningMaterial;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LearningMaterialController extends Controller
{
    protected function getBaseRoute()
    {
        return auth()->user()->role === 'pengajar' ? 'pengajar.learning_material' : 'admin.learning_material';
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = LearningMaterial::with('subject');

        if ($user->role === 'pengajar') {
            $subjects = $user->subjects;
            $query->whereIn('subject_id', $subjects->pluck('id'));
        } else {
            $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
            $subjects = Subject::where('created_by', $creatorId)->orderBy('name')->get();
            $query->whereIn('subject_id', $subjects->pluck('id'));
        }
        
        if ($request->has('subject_id') && $request->subject_id != '') {
            $query->where('subject_id', $request->subject_id);
        }
        
        $materials = $query->latest()->paginate(10);
        $baseRoute = $this->getBaseRoute();
        
        return view('admin.learning_material.index', compact('materials', 'subjects', 'baseRoute'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:10240', // Max 10MB
        ]);

        // Check if teacher has access to this subject
        $user = auth()->user();
        if ($user->role === 'pengajar' && !$user->subjects->contains($request->subject_id)) {
            abort(403, 'Akses Ditolak. Anda tidak mengampu mata pelajaran ini.');
        }

        $path = $request->file('file')->store('materials', 'public');
        $extension = $request->file('file')->getClientOriginalExtension();

        LearningMaterial::create([
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'file_type' => $extension,
            'created_by' => $user->id,
        ]);

        return redirect()->route($this->getBaseRoute() . '.index')->with('success', 'Materi belajar berhasil diunggah.');
    }

    public function destroy(LearningMaterial $learningMaterial)
    {
        // Check if teacher has access to this subject
        $user = auth()->user();
        if ($user->role === 'pengajar' && !$user->subjects->contains($learningMaterial->subject_id)) {
            abort(403, 'Akses Ditolak. Anda tidak mengampu mata pelajaran ini.');
        }

        if ($learningMaterial->file_path) {
            Storage::disk('public')->delete($learningMaterial->file_path);
        }
        
        $learningMaterial->delete();
        
        return redirect()->route($this->getBaseRoute() . '.index')->with('success', 'Materi belajar berhasil dihapus.');
    }
}
