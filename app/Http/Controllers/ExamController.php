<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Exam;
use Illuminate\Http\Request;
use App\Models\QuestionBank;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\QuestionImport;

class ExamController extends Controller
{
    /**
     * Display the CBT creation form with existing CBT list
     */
public function index()
{
    $courses = Course::all();
    
    // load exams with course + active student count
    $exams = Exam::with('course')
        ->withCount('activeSessions as active_students_count')
        ->latest()
        ->get();

    return view('exams.index', compact('courses', 'exams'));
}

public function create()
{
    $courses = Course::all();
    $exams = Exam::with('course')->latest()->get();

    // Fixing question count collection
    $questionCounts = QuestionBank::selectRaw('exam_id, course_id, COUNT(*) as total')
        ->groupBy('exam_id', 'course_id')
        ->get()
        ->mapWithKeys(function ($row) {
            return [$row->exam_id . '-' . $row->course_id => $row->total];
        });

    return view('admin.exams.create', compact('courses', 'exams', 'questionCounts'));
}


public function edit($id)
{
    $exam = Exam::findOrFail($id);
    $courses = Course::all();

    return view('admin.exams.edit', compact('exam', 'courses'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'type'           => 'required|in:test,exam,pre-national',
        'duration'       => 'required|integer|min:1',
        'question_limit' => 'nullable|integer|min:1',
    ]);

    $exam = Exam::findOrFail($id);
    $exam->type = $request->type;
    $exam->duration = $request->duration;
    $exam->question_limit = $request->question_limit;
    $exam->save();

    return redirect()->route('admin.exams.edit', $exam->id)->with('success', 'CBT updated successfully.');
}

public function destroy($id)
{
    $exam = Exam::findOrFail($id);

    // Delete all questions associated with this exam
    QuestionBank::where('exam_id', $exam->id)->delete();

    // Delete the exam itself
    $exam->delete();

    return back()->with('success', 'Exam and all its related questions deleted successfully.');
}
public function getExamsByCourse($courseId)
{
    $exams = Exam::where('course_id', $courseId)->get(['id', 'type']);
    return response()->json($exams);
}

    /**
     * Store a new CBT (Exam/Test)
     */
    public function store(Request $request)
{
    $request->validate([
        'course_id' => 'required|exists:courses,id',
        'type'      => 'required|in:test,exam,pre-national',
        'duration'  => 'required|integer|min:1',
        'question_limit' => 'nullable|integer|min:1',
    ]);

    Exam::create([
        'course_id'      => $request->course_id,
        'type'           => $request->type,
        'duration'       => $request->duration,
        'is_active'      => true,
        'question_limit' => $request->question_limit,
    ]);

    return redirect()->back()->with('success', 'Exam created successfully.');
}

    /**
     * Toggle CBT (Exam/Test) active status
     */
     public function toggleStatus(Exam $exam)
    {
        $exam->is_active = !$exam->is_active;
        $exam->save();

        return back()->with('success', 'Exam status updated.');
    }


    /**
     * Show a list of courses that have active CBTs
     */
    public function selectCourse()
    {
        $courses = Exam::with('course')
            ->where('is_active', true)
            ->get()
            ->pluck('course')
            ->unique('id');

        return view('admin.exams.select_course', compact('courses'));
    }
     public function createQuestion()
{
    // Get courses from active exams
    $courses = Exam::where('is_active', true)
        ->with('course')
        ->get()
        ->pluck('course')
        ->unique('id');

    return view('admin.exams.create_question', compact('courses'));
}

   public function storeQuestionBank(Request $request)
{
    $request->validate([
        'course_id' => 'required|exists:courses,id',
        'questions' => 'required|array|min:1',
    ]);

    // Get the active exam for the selected course
    $exam = Exam::where('course_id', $request->course_id)
                ->where('is_active', true)
                ->first();

    if (!$exam) {
        return back()->with('error', 'No active exam found for the selected course.');
    }

    foreach ($request->questions as $q) {
        QuestionBank::create([
            'course_id'   => $request->course_id,
            'exam_id'     => $exam->id, // ✅ Add exam_id here
            'question'    => $q['question'],
            'mark'        => $q['mark'],
            'option_type' => $q['option_type'],
            'options'     => json_encode($q['options'] ?? []),
            'answers'     => json_encode($q['answers'] ?? []),
            'is_active'   => true,
        ]);
    }

    return back()->with('success', 'Questions saved successfully.');
}
public function uploadFromExcel(Request $request)
{
    $request->validate([
        'course_id' => 'required|exists:courses,id',
        'question_file' => 'required|file|mimes:xlsx,xls',
    ]);

    $exam = Exam::where('course_id', $request->course_id)->where('is_active', true)->first();

    if (!$exam) {
        return back()->with('error', 'No active exam found for the selected course.');
    }

    try {
        Excel::import(new QuestionImport($request->course_id, $exam->id), $request->file('question_file'));
        return back()->with('success', 'Questions imported successfully.');
    } catch (\Exception $e) {
        return back()->with('error', 'Import failed: ' . $e->getMessage());
    }
}

public function deleteQuestions(Request $request)
{
    $examId = $request->exam_id;
    $courseId = $request->course_id;

    $deleted = QuestionBank::where('exam_id', $examId)
                ->where('course_id', $courseId)
                ->delete();

    return back()->with('success', "All questions for this exam have been deleted.");
}


}
