<?php

namespace App\Services;

use App\Models\ExamAttempt;
use App\Models\ExamSession;
use App\Models\ExamAnswer;
use App\Models\Question;
use App\Models\ScoreScale;
use App\Models\Institution;
use App\Models\QuestionOption;

class ExamService
{
  public function gradeAttempt(ExamAttempt $attempt)
  {
    $session = $attempt->examSession;

    // 1. Fetch Questions & Answers
    $questions = $this->getQuestionsForSession($session);
    $answers = ExamAnswer::where('exam_attempt_id', $attempt->id)->get();

    // 2. Calculate Stats per Group
    $groups = $this->calculateGroupStats($questions, $answers);

    // 3. Calculate Final Score (Linear or Scaled)
    $finalScore = $this->calculateFinalScore($groups, $questions->count(), $session, $attempt);

    $attempt->score = $finalScore;
    $attempt->save();

    return $finalScore;
  }

  private function getQuestionsForSession($session)
  {
    if ($session->exam_package_id) {
      return $session->examPackage->questions()
        ->with('questionGroup')
        ->get()
        ->keyBy('id');
    } else {
      return Question::where('subject_id', $session->subject_id)
        ->with('questionGroup')
        ->get()
        ->keyBy('id');
    }
  }

  private function calculateGroupStats($questions, $answers)
  {
    $groups = [];

    // Initialize groups
    foreach ($questions as $q) {
      $groupId = $q->question_group_id ?? 'default';
      if (!isset($groups[$groupId])) {
        $groups[$groupId] = ['total' => 0, 'correct' => 0];
      }
      $groups[$groupId]['total']++;
    }

    // Process Answers
    foreach ($answers as $ans) {
      $q = $questions[$ans->question_id] ?? null;
      if ($q) {
        $groupId = $q->question_group_id ?? 'default';

        $opt = QuestionOption::find($ans->question_option_id);
        $isCorrect = $opt && $opt->is_correct;

        // Sync correctness if changed (e.g. answer key changed)
        if ($ans->is_correct != $isCorrect) {
          $ans->is_correct = $isCorrect;
          $ans->save();
        }

        if ($isCorrect) {
          $groups[$groupId]['correct']++;
        }
      }
    }
    return $groups;
  }

  private function calculateFinalScore($groups, $totalQuestions, $session, $attempt)
  {
    $finalScore = 0;
    $activeScalesCount = 0;
    $totalCorrectGlobal = 0;

    // Determine Institution ID
    // Try from Student -> User -> Institution
    $studentUser = $attempt->student ? $attempt->student->user : null; // Assuming Student has User relation or created_by
    // Better: Use Subject Creator
    $institutionId = null;
    if ($session->subject && $session->subject->created_by) {
      $institutionId = Institution::where('user_id', $session->subject->created_by)->value('id');
    }

    foreach ($groups as $groupId => $stats) {
      $totalCorrectGlobal += $stats['correct'];

      if ($groupId === 'default') {
        continue; // Default group usually follows linear or is ignored in scaling if mixed? 
        // Actually, if using Scale, we usually ignore 'default' unless mapped.
        // Let's assume if Scale exists for 'default', use it.
      }

      $scale = null;
      if ($institutionId) {
        $scale = ScoreScale::where('institution_id', $institutionId)
          ->where('question_group_id', $groupId)
          ->where('correct_count', $stats['correct'])
          ->first();
      }

      if ($scale) {
        $finalScore += $scale->scaled_score;
        $activeScalesCount++;
      }
    }

    // Mixed Mode or Linear Fallback
    // If we have scales, we return the sum of scaled scores.
    // If NO scales matched (activeScalesCount == 0), we do linear global.

    if ($activeScalesCount > 0) {
      return $finalScore;
    } else {
      // Linear Mode
      return $totalQuestions > 0 ? ($totalCorrectGlobal / $totalQuestions) * 100 : 0;
    }
  }
}
