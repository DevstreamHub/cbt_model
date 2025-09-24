<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamResult;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;

class ExamSubmissionController extends Controller
{
    public function submit(Request $request)
{
    $student_id = session('student_id');

    if (!$student_id) {
        return redirect()->route('exam.login')->with('error', 'Session expired. Please log in again.');
    }

    try {
        $exam = Exam::with('questionBanks')->findOrFail($request->exam_id);
        $course_id = $exam->course_id;

        // ✅ Check if result already exists
        $existingResult = ExamResult::where('student_id', $student_id)
            ->where('exam_id', $exam->id)
            ->first();

        if ($existingResult) {
            return redirect()->route('cbt.exam.login.dropdown')->with('error', 'You have already submitted this exam.');
        }

        $correct = 0;
        $totalMark = 0;

        // ✅ First, try using ExamProgress (fastest & most reliable)
        $progress = \App\Models\ExamProgress::where('exam_id', $exam->id)
            ->where('student_id', $student_id)
            ->with('question')
            ->get();

        if ($progress->isNotEmpty()) {
            $totalQuestions = $progress->count();

            foreach ($progress as $p) {
                if ($p->question) {
                    // Decode correct answers
                    $correctAnswerIndices = json_decode($p->question->answers, true);
                    if (!is_array($correctAnswerIndices)) {
                        $correctAnswerIndices = [$correctAnswerIndices];
                    }
                    $correctAnswerIndices = array_map('strval', $correctAnswerIndices);
                    sort($correctAnswerIndices);

                    // Decode user selected
                    $userSelected = json_decode($p->selected_option, true);
                    $userSelected = \Illuminate\Support\Arr::wrap($userSelected);
                    $userSelected = array_map('strval', $userSelected);
                    sort($userSelected);

                    if ($userSelected === $correctAnswerIndices) {
                        $correct++;
                        $totalMark += $p->question->mark;
                    }
                }
            }
        } else {
            // ✅ Fallback: Use request->answers
            $answers = $request->input('answers', []);
            $totalQuestions = $exam->questionBanks->count();

            foreach ($exam->questionBanks as $question) {
                $questionId = $question->id;

                // Correct answers
                $correctAnswerIndices = json_decode($question->answers, true);
                if (!is_array($correctAnswerIndices)) {
                    $correctAnswerIndices = [$correctAnswerIndices];
                }
                $correctAnswerIndices = array_map('strval', $correctAnswerIndices);
                sort($correctAnswerIndices);

                // User selected
                $userSelected = \Illuminate\Support\Arr::wrap($answers[$questionId] ?? []);
                $userSelected = array_map('strval', $userSelected);
                sort($userSelected);

                if ($userSelected === $correctAnswerIndices) {
                    $correct++;
                    $totalMark += $question->mark;
                }
            }
        }

        // ✅ Save result
        ExamResult::create([
            'student_id'       => $student_id,
            'exam_id'          => $exam->id,
            'course_id'        => $course_id,
            'score'            => $totalMark,
            'correct_answers'  => $correct,
            'total_questions'  => $totalQuestions,
        ]);

        // ✅ Clear exam progress since exam is finalized
        \App\Models\ExamProgress::where('exam_id', $exam->id)
            ->where('student_id', $student_id)
            ->delete();

        return redirect()->route('cbt.exam.login.dropdown')->with('success', 'Your exam was submitted successfully.');
    } catch (ModelNotFoundException $e) {
        return redirect()->back()->with('error', 'Exam not found. Please try again.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'An error occurred during submission: ' . $e->getMessage());
    }
}

}
