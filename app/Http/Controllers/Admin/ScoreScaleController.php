<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\QuestionGroup;
use App\Models\ScoreScale;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScoreScaleController extends Controller
{
    public function index(Request $request)
    {
        $subjects = Subject::all(); // Or filter by institution if subjects are scoped
        // For now get all subjects as admin_lembaga usually sees all mapel?
        // Actually, verify scope. Assuming general access for now or scope later.
        
        $selectedSubjectId = $request->get('subject_id');
        $selectedGroupId = $request->get('question_group_id');
        
        $questionGroups = [];
        if ($selectedSubjectId) {
            $questionGroups = QuestionGroup::where('subject_id', $selectedSubjectId)->get();
        }

        $scales = [];
        $maxQuestions = 0;

        if ($selectedGroupId) {
            $group = QuestionGroup::withCount('questions')->find($selectedGroupId);
            if ($group) {
                $maxQuestions = $group->questions_count;
                // Get existing scales
                $institutionId = Institution::where('user_id', auth()->id())->value('id');
                
                $scales = ScoreScale::where('institution_id', $institutionId)
                            ->where('question_group_id', $selectedGroupId)
                            ->pluck('scaled_score', 'correct_count')
                            ->toArray();
            }
        }

        return view('admin.score_scale.index', compact('subjects', 'questionGroups', 'selectedSubjectId', 'selectedGroupId', 'scales', 'maxQuestions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question_group_id' => 'required|exists:question_groups,id',
            'scales' => 'required|array',
            'scales.*' => 'nullable|numeric|min:0',
        ]);

        $institutionId = Institution::where('user_id', auth()->id())->value('id');
        if (!$institutionId) {
            return back()->with('error', 'Data lembaga tidak ditemukan.');
        }

        // Use transaction for bulk update
        DB::beginTransaction();
        try {
            // Remove old scales for this group/institution to avoid duplicates/conflicts easily
            // Or use updateOrCreate per count.
            
            foreach ($request->scales as $correctCount => $scaledScore) {
                if ($scaledScore !== null) {
                    ScoreScale::updateOrCreate(
                        [
                            'institution_id' => $institutionId,
                            'question_group_id' => $request->question_group_id,
                            'correct_count' => $correctCount
                        ],
                        [
                            'scaled_score' => $scaledScore
                        ]
                    );
                } else {
                    // Remove existing scale if input is cleared (revert to standard)
                    ScoreScale::where('institution_id', $institutionId)
                        ->where('question_group_id', $request->question_group_id)
                        ->where('correct_count', $correctCount)
                        ->delete();
                }
            }
            
            DB::commit();
            return back()->with('success', 'Konversi skor berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}
