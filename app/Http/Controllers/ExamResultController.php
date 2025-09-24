<?php
use App\Models\ExamResult;

public function showExamResultsClearPage()
{
    // Get matric numbers of students who have results
    $matricNumbers = Student::whereIn('id', ExamResult::pluck('student_id'))
        ->pluck('matric_no');

    return view('results.clear', compact('matricNumbers'));
}

public function clearStudentExamResults(Request $request)
{
    $request->validate(['matric_number' => 'required']);

    $student = Student::where('matric_no', $request->matric_number)->first();

    if (!$student) {
        return back()->with('error', 'Student not found.');
    }

    $deleted = ExamResult::where('student_id', $student->id)->delete();

    return back()->with('success', "$deleted exam result(s) cleared for {$student->matric_no}.");
}

public function clearAllExamResults()
{
    $count = ExamResult::count();
    ExamResult::truncate(); // Completely clears the table

    return back()->with('success', "✅ All $count exam result(s) cleared successfully.");
}
