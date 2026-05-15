<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamPackage;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;

class ExamPackageController extends Controller
{
    protected function getBaseRoute()
    {
        return auth()->user()->role === 'pengajar' ? 'pengajar.exam_package' : 'admin.exam_package';
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        if ($user->role === 'pengajar') {
            $subjects = $user->subjects;
        } else {
            $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
            $subjects = Subject::where('created_by', $creatorId)->get();
        }
        $baseRoute = $this->getBaseRoute();
        return view('admin.exam_package.create', compact('subjects', 'baseRoute'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        // Security Check: Ensure teacher owns the subject
        $user = auth()->user();
        if ($user->role === 'pengajar' && !$user->subjects->contains($request->subject_id)) {
            return back()->withInput()->with('error', 'Akses Ditolak. Anda tidak dapat membuat paket untuk mata pelajaran ini.');
        }

        $data = $request->all();
        $data['created_by'] = auth()->id();
        $package = ExamPackage::create($data);

        return redirect()->route($this->getBaseRoute() . '.show', $package->id)
            ->with('success', 'Paket soal berhasil dibuat. Silakan tambahkan soal.');
    }

    /**
     * Display the specified resource. Using this to Assign Questions.
     */
    public function show(Request $request, ExamPackage $examPackage)
    {
        // Security check for Pengajar
        $user = auth()->user();
        if ($user->role === 'pengajar' && !$user->subjects->contains($examPackage->subject_id)) {
            abort(403, 'Akses Ditolak.');
        }

        $examPackage->load(['subject', 'questions']);

        $query = Question::where('subject_id', $examPackage->subject_id);

        if ($request->has('q')) {
            $query->where('content', 'like', '%' . $request->q . '%');
        }

        // Limit to 200 to prevent crash, let user search specifically
        $questions = $query->latest()->limit(200)->get();
        $baseRoute = $this->getBaseRoute();

        return view('admin.exam_package.show', compact('examPackage', 'questions', 'baseRoute'));
    }

    /**
     * Sync questions to the package.
     */
    public function assignQuestions(Request $request, ExamPackage $examPackage)
    {
        $request->validate([
            'questions' => 'array',
            'questions.*' => 'exists:questions,id',
        ]);

        $examPackage->questions()->sync($request->input('questions', []));

        return redirect()->back()->with('success', 'Daftar soal dalam paket berhasil diperbarui.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExamPackage $examPackage)
    {
        $user = auth()->user();
        if ($user->role === 'pengajar') {
            if (!$user->subjects->contains($examPackage->subject_id)) {
                abort(403, 'Akses Ditolak. Anda tidak mengampu mata pelajaran paket ini.');
            }
            $subjects = $user->subjects;
        } else {
            $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;
            $subjects = Subject::where('created_by', $creatorId)->get();
        }
        $baseRoute = $this->getBaseRoute();
        return view('admin.exam_package.edit', compact('examPackage', 'subjects', 'baseRoute'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExamPackage $examPackage)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        // Security Check: Ensure teacher owns the NEW subject id if changed
        $user = auth()->user();
        if ($user->role === 'pengajar' && !$user->subjects->contains($request->subject_id)) {
            return back()->withInput()->with('error', 'Akses Ditolak. Anda tidak dapat mengubah paket ke mata pelajaran ini.');
        }

        $examPackage->update($request->all());

        return redirect()->route($this->getBaseRoute() . '.index')->with('success', 'Paket soal berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamPackage $examPackage)
    {
        $examPackage->delete();
        return redirect()->route($this->getBaseRoute() . '.index')->with('success', 'Paket soal berhasil dihapus.');
    }

    /**
     * Generate random questions for the package.
     */
    public function generateRandomQuestions(Request $request, ExamPackage $examPackage)
    {
        $request->validate([
            'count' => 'required|integer|min:1',
            'type' => 'nullable|string|in:all,multiple_choice,essay',
        ]);

        $query = Question::where('subject_id', $examPackage->subject_id);

        if ($request->type && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $availableCount = $query->count();
        $limit = min($request->count, $availableCount);

        if ($limit <= 0) {
            return redirect()->back()->with('error', 'Tidak ada soal tersedia untuk kriteria yang dipilih.');
        }

        $randomQuestions = $query->inRandomOrder()->limit($limit)->pluck('id');

        $examPackage->questions()->sync($randomQuestions);

        return redirect()->back()->with('success', "Berhasil menambahkan $limit soal acak ke paket.");
    }

    public function index()
    {
        $user = auth()->user();
        $creatorId = $user->role === 'operator' ? $user->created_by : $user->id;

        if ($user->role === 'pengajar') {
            $subjects = $user->subjects->pluck('id');
        } else {
            $subjects = Subject::where('created_by', $creatorId)->pluck('id');
        }

        $packages = ExamPackage::whereIn('subject_id', $subjects)
            ->with(['subject', 'questions'])
            ->withCount('questions')
            ->latest()
            ->paginate(10);

        // Add available count for each package's subject
        foreach ($packages as $package) {
            $package->available_questions_count = Question::where('subject_id', $package->subject_id)->count();
        }

        $baseRoute = $this->getBaseRoute();
        return view('admin.exam_package.index', compact('packages', 'baseRoute'));
    }

    /**
     * Sync all questions from the Bank Soal (subject) to this package.
     */
    public function syncAll(ExamPackage $examPackage)
    {
        $user = auth()->user();
        if ($user->role === 'pengajar' && !$user->subjects->contains($examPackage->subject_id)) {
            abort(403, 'Akses Ditolak.');
        }

        $allQuestions = Question::where('subject_id', $examPackage->subject_id)->pluck('id');
        
        if ($allQuestions->isEmpty()) {
            return back()->with('error', 'Bank Soal untuk mata pelajaran ini masih kosong.');
        }

        $examPackage->questions()->sync($allQuestions);

        return back()->with('success', 'Berhasil mensinkronkan semua soal dari Bank Soal ke paket ini.');
    }

    public function preview($id)
    {
        $examPackage = ExamPackage::with(['questions.options'])->findOrFail($id);
        return view('admin.exam_package.preview', compact('examPackage'));
    }
}
