<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Student, CourseRegistration, Level, Department, Semester, AcademicSession};
use Illuminate\Http\Request;
use PDF;

class ExamCardController extends Controller
{
    public function index()
    {
        return view('admin.exam_cards.index', [
            'levels' => Level::all(),
            'departments' => Department::all(),
            'semesters' => Semester::all(),
        ]);
    }

    public function filter(Request $request)
{
    $request->validate([
        'level_id' => 'required',
        'department_id' => 'required',
        'semester_id' => 'required',
    ]);

    $activeSession = AcademicSession::where('is_active', true)->first();

    $students = Student::where('level_id', $request->level_id)
        ->where('department_id', $request->department_id)
        ->whereHas('courseRegistrations', function ($q) use ($activeSession) {
            $q->where('academic_session_id', $activeSession->id);
        })
        ->whereHas('courseRegistrations.course', function ($q) use ($request) {
            $q->where('semester_id', $request->semester_id);
        })
        ->get();

    return view('admin.exam_cards.index', [
        'levels' => Level::all(),
        'departments' => Department::all(),
        'semesters' => Semester::all(),
        'students' => $students,
        'selected' => $request->only(['level_id', 'department_id', 'semester_id']),
    ]);
}

public function download(Student $student)
{
    $session = AcademicSession::where('is_active', true)->first();
    $semesterId = request('semester_id');

    $courses = CourseRegistration::where('student_id', $student->id)
        ->where('academic_session_id', $session->id)
        ->whereHas('course', fn ($q) => $q->where('semester_id', $semesterId))
        ->with('course')
        ->get()
        ->pluck('course');

    $safeMatric = preg_replace('/[\/\\\\]+/', '_', $student->matric_no);
    $fileName = "exam_card_{$safeMatric}.pdf";

    $pdf = PDF::loadView('admin.exam_cards.pdf', [
        'student' => $student->load('department'),
        'session' => $session,
        'semester' => Semester::find($semesterId),
        'courses' => $courses,
    ])->setPaper([0, 0, 270, 170], 'portrait');
    
    return $pdf->download($fileName);
}
public function printAll(Request $request)
{
    $request->validate([
        'level_id' => 'required',
        'department_id' => 'required',
        'semester_id' => 'required',
    ]);

    $session = AcademicSession::where('is_active', true)->first();

    $students = Student::where('level_id', $request->level_id)
        ->where('department_id', $request->department_id)
        ->whereHas('courseRegistrations', function ($q) use ($session) {
            $q->where('academic_session_id', $session->id);
        })
        ->whereHas('courseRegistrations.course', function ($q) use ($request) {
            $q->where('semester_id', $request->semester_id);
        })
        ->with(['department'])
        ->get();

    $semester = Semester::find($request->semester_id);

    $studentCards = [];

    foreach ($students as $student) {
        $courses = CourseRegistration::where('student_id', $student->id)
            ->where('academic_session_id', $session->id)
            ->whereHas('course', fn ($q) => $q->where('semester_id', $semester->id))
            ->with('course')
            ->get()
            ->pluck('course');

        $studentCards[] = [
            'student' => $student,
            'courses' => $courses,
        ];
    }

    $pdf = PDF::loadView('admin.exam_cards.bulk_pdf', [
        'studentCards' => $studentCards,
        'session' => $session,
        'semester' => $semester,
    ])->setPaper('A4', 'portrait');

    return $pdf->download('all_exam_cards.pdf');
}


}
