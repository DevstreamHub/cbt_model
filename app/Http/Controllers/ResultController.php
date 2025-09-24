<?php

namespace App\Http\Controllers;

use App\Models\ExamResult;
use App\Models\Exam;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Result;
use Illuminate\Http\Request;
use App\Models\Student;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResultController extends Controller
{
    public function index()
    {
        return view('admin.result.index'); // Ensure the view exists
    }

    public function copy()
    {
        $examResults = ExamResult::with('exam')->get();

        foreach ($examResults as $examResult) {
            $exam = $examResult->exam;
            if (!$exam) continue;

            $course = Course::find($examResult->course_id);
            if (!$course) continue;

            $courseReg = CourseRegistration::where('student_id', $examResult->student_id)
                ->where('course_id', $examResult->course_id)
                ->first();

            if (!$courseReg) continue;

            $result = Result::firstOrNew([
                'course_registration_id' => $courseReg->id,
            ]);

            $result->session_id = $courseReg->academic_session_id;
            $result->department_id = $course->department_id;
            $result->semester_id = $course->semester_id;
            $result->level_id = $course->level_id;

            if (in_array($exam->type, ['test', 'pre-national'])) {
                $result->ca_score = $examResult->score;
            } elseif ($exam->type === 'exam') {
                $result->exam_score = $examResult->score;
            }


            $result->total_score = ($result->ca_score ?? 0) + ($result->exam_score ?? 0);
            $result->save();
        }

        return redirect()->back()->with('success', 'Results copied successfully.');
    }

    public function export(Request $request)
{
    $request->validate([
        'type' => 'required|in:ca,exam,both',
        'course_id' => 'required|exists:courses,id',
    ]);

    $course = Course::findOrFail($request->course_id);

    $results = Result::whereHas('courseRegistration', function ($query) use ($course) {
        $query->where('course_id', $course->id);
    })->with('courseRegistration.student')->get();

    $filename = str_replace(' ', '_', $course->title) . '_results.csv';

    return new StreamedResponse(function () use ($results, $request) {
        $handle = fopen('php://output', 'w');

        // Define headers
        $headers = ['Matric Number'];
        if ($request->type === 'ca' || $request->type === 'both') {
            $headers[] = 'C.A';
        }
        if ($request->type === 'exam' || $request->type === 'both') {
            $headers[] = 'Exam';
        }

        fputcsv($handle, $headers);

        // Write student rows
        foreach ($results as $result) {
            $student = $result->courseRegistration->student;
            $row = [$student->matric_no ?? ''];

            if ($request->type === 'ca' || $request->type === 'both') {
                $row[] = $result->ca_score ?? '';
            }
            if ($request->type === 'exam' || $request->type === 'both') {
                $row[] = $result->exam_score ?? '';
            }

            fputcsv($handle, $row);
        }

        fclose($handle);
    }, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ]);
}

    public function showResultClearPage()
    {
        // Get only students who have results
        $matricNumbers = Student::whereIn('id', ExamResult::pluck('student_id'))
            ->pluck('matric_no');

        return view('results.clear', compact('matricNumbers'));
    }

    public function clearStudentResults(Request $request)
    {
        $request->validate(['matric_number' => 'required']);

        $student = Student::where('matric_no', $request->matric_number)->first();

        if (!$student) {
            return back()->with('error', 'Student not found.');
        }

        $deleted = ExamResult::where('student_id', $student->id)->delete();

        return back()->with('success', "✅ $deleted result(s) cleared for {$student->matric_no}.");
    }

    public function clearAllResults()
    {
        $count = ExamResult::count();
        ExamResult::truncate();

        return back()->with('success', "✅ All $count exam result(s) cleared successfully.");
    }


}
