<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActiveExamSession;
use App\Models\ExamProgress;
use App\Models\ExamResult;
use App\Models\Student;
use App\Models\Exam;

class AdminExamController extends Controller
{
    // View all active students for an exam
    public function monitor($examId = null)
{
    if ($examId) {
        // Monitor a specific exam
        $exam = Exam::with('course')->findOrFail($examId);
        $students = ExamProgress::where('exam_id', $examId)
                                ->with('student')
                                ->get();

        return view('admin.exam_monitor_single', compact('exam', 'students'));
    }

    // Monitor overview page showing all ongoing exams
    $exams = Exam::with(['course'])
                 ->withCount('examProgress')
                 ->where('is_active', 1) // Only active exams
                 ->get();

    return view('admin.exam_monitor_overview', compact('exams'));
}



    // Force submit exam
    public function forceSubmit(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'student_id' => 'required|exists:students,id',
        ]);

        $examId = $request->exam_id;
        $studentId = $request->student_id;

        // Get all answers from ExamProgress
        $progress = ExamProgress::where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->get();

        if ($progress->isEmpty()) {
            return back()->with('error', 'No saved answers for this student.');
        }

        // Create/Update ExamResult
        ExamResult::updateOrCreate(
            [
                'exam_id' => $examId,
                'student_id' => $studentId,
            ],
            [
                'answers' => $progress->pluck('answer')->toJson(),
                'submitted_at' => now(),
            ]
        );

        // Remove from active session
        ActiveExamSession::where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->delete();

        return back()->with('success', 'Exam submitted for student successfully.');
    }
}
