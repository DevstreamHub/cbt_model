<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamProgress;
use App\Models\ExamResult;
use App\Models\Student;
use Illuminate\Http\Request;

class ExamMonitorController extends Controller
{
    public function index(Exam $exam)
    {
        $attemptedCount = ExamResult::where('exam_id', $exam->id)->count();
        $inProgressCount = ExamProgress::where('exam_id', $exam->id)
            ->distinct('student_id')
            ->count('student_id');

        return view('exams.monitor', compact('exam', 'attemptedCount', 'inProgressCount'));
    }

public function attempted(Exam $exam)
{
    $students = ExamResult::with('student')
        ->where('exam_id', $exam->id)
        ->get()
        ->map(function ($result) {
            // if you saved "total_questions" in exam_results, use it
            $result->attempts = $result->total_questions;  
            return $result;
        });

    return response()->json($students);
}


    public function inProgress(Exam $exam)
    {
        $students = ExamProgress::with('student')
            ->where('exam_id', $exam->id)
            ->selectRaw('student_id, COUNT(*) as attempts')
            ->groupBy('student_id')
            ->get();

        return response()->json($students);
    }


    public function forceSubmit(Exam $exam, Student $student)
{
    $progress = \App\Models\ExamProgress::where('exam_id', $exam->id)
        ->where('student_id', $student->id)
        ->with('question')
        ->get();

    $correctAnswers = 0;
    $totalMark = 0;
    $totalQuestions = $progress->count();

    foreach ($progress as $p) {
        if ($p->question) {
            // ✅ Decode correct answers from question_bank
            $correctAnswerIndices = json_decode($p->question->answers, true);
            if (!is_array($correctAnswerIndices)) {
                $correctAnswerIndices = [$correctAnswerIndices];
            }
            $correctAnswerIndices = array_map('strval', $correctAnswerIndices);
            sort($correctAnswerIndices);

            // ✅ Decode selected option(s) from exam_progress
            $userSelected = json_decode($p->selected_option, true);
            $userSelected = \Illuminate\Support\Arr::wrap($userSelected);
            $userSelected = array_map('strval', $userSelected);
            sort($userSelected);

            // ✅ Compare arrays
            if ($userSelected === $correctAnswerIndices) {
                $correctAnswers++;
                $totalMark += $p->question->mark;
            }
        }
    }

    // Save result
    \App\Models\ExamResult::updateOrCreate(
        ['exam_id' => $exam->id, 'student_id' => $student->id],
        [
            'course_id'        => $exam->course_id,
            'score'            => $totalMark,
            'correct_answers'  => $correctAnswers,
            'total_questions'  => $totalQuestions,
        ]
    );

    // Clear progress
    \App\Models\ExamProgress::where('exam_id', $exam->id)
        ->where('student_id', $student->id)
        ->delete();

    // Kill active session
    \App\Models\ActiveExamSession::where('exam_id', $exam->id)
        ->where('student_id', $student->id)
        ->delete();

    // Optional: log out Laravel session
    \Illuminate\Support\Facades\DB::table('sessions')
        ->where('user_id', $student->id)
        ->delete();

    return response()->json([
        'message' => "Exam for {$student->name} was forcefully submitted, scored, and session terminated."
    ]);
}
}
