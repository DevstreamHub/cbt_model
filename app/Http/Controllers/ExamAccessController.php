<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Level;
use App\Models\Department;

class ExamAccessController extends Controller
{
    public function index(Request $request)
    {
        $levels = Level::all();
        $departments = Department::all();

        $students = Student::query();

        if ($request->filled('level_id')) {
            $students->where('level_id', $request->level_id);
        }

        if ($request->filled('department_id')) {
            $students->where('department_id', $request->department_id);
        }

        $students = $students->with('level', 'department')->get();

        return view('admin.exam-access.index', compact('students', 'levels', 'departments'));
    }

    public function toggle(Student $student)
    {
        $student->can_access_exam = !$student->can_access_exam;
        $student->save();

        return response()->json(['success' => true, 'status' => $student->can_access_exam]);
    }
}
