<?php

namespace App\Http\Controllers;

use App\Models\ActiveExamSession;
use App\Models\Student;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function showSessionClearPage()
    {
        $matricNumbers = Student::whereIn('id', ActiveExamSession::pluck('student_id'))->pluck('matric_no');
        return view('session.clear', compact('matricNumbers'));
    }

    public function clearStudentSessions(Request $request)
{
    $request->validate(['matric_number' => 'required']); // ✅ match the form field

    $student = Student::where('matric_no', $request->matric_number)->first();

    if (!$student) {
        return back()->with('error', 'Student not found.');
    }

    $deleted = ActiveExamSession::where('student_id', $student->id)->delete();

    return back()->with('success', "$deleted session(s) cleared for {$student->matric_no}.");
}
public function clearAllSessions()
{
    $count = ActiveExamSession::count();
    ActiveExamSession::truncate(); // Or ->delete() if you prefer soft deletes

    return back()->with('success', "✅ All $count active session(s) cleared successfully.");
}


}
