<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Option;

class QuestionBankController extends Controller
{
    /**
     * Show the form to create questions.
     */
    public function createQuestion()
    {
        $courses = Course::with('exams')->get();

        // Count of questions by course
        $questionCounts = Question::selectRaw('course_id, COUNT(*) as count')
            ->groupBy('course_id')
            ->pluck('count', 'course_id');

        return view('admin.exams.create_question', compact('courses', 'questionCounts'));
    }

    /**
     * Store uploaded questions.
     */
    public function store(Request $request)
{
    $request->validate([
        'exam_id'   => 'required|exists:exams,id',
        'questions' => 'required|array|min:1',
        'questions.*.question'    => 'required|string',
        'questions.*.mark'        => 'required|numeric',
        'questions.*.option_type' => 'required|string',
        'questions.*.options'     => 'required|array|min:1',
        'questions.*.answers'     => 'nullable|array',
    ]);

    // Fetch the exam and its course_id
    $exam = \App\Models\Exam::findOrFail($request->exam_id);
    $course_id = $exam->course_id;

    foreach ($request->questions as $qData) {
        $question = Question::create([
            'course_id'    => $course_id,
            'exam_id'      => $exam->id,
            'question'     => $qData['question'],
            'mark'         => $qData['mark'],
            'type'         => $qData['type'] ?? 'mcq',
            'option_type'  => $qData['option_type'],
            'answers'      => isset($qData['answers']) ? json_encode($qData['answers']) : null,
            'time'         => $qData['time'] ?? 0,
            'is_active'    => true,
        ]);

        // Save options
        if (!empty($qData['options']) && is_array($qData['options'])) {
            foreach ($qData['options'] as $key => $opt) {
                Option::create([
                    'question_id' => $question->id,
                    'option_text' => $opt,
                    'is_correct'  => isset($qData['answers']) && in_array($key, $qData['answers']),
                ]);
            }
        }
    }

    return back()->with('success', 'Questions uploaded successfully.');
}

}
