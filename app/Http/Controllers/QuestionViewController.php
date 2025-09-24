<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Exam;

class QuestionViewController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        return view('admin.questions.view', compact('courses'));
    }

    public function fetch(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'exam_type' => 'required|in:test,exam,pre-national',
        ]);

        $course = Course::findOrFail($request->course_id);
        $exam = $course->exams()->where('type', $request->exam_type)->first();

        if (!$exam) {
            return response()->json(['error' => 'No exam found for this course and type.'], 404);
        }

        $questions = $exam->questionBanks;

        return response()->json([
            'questions' => $questions,
        ]);
    }
}
