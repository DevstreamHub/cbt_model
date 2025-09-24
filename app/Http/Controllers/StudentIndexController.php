<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\StudentIndexImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentIndexTemplateExport;
use App\Models\StudentIndex;
use App\Models\Student;
use App\Models\CourseRegistration;
use Illuminate\Support\Facades\Log;


class StudentIndexController extends Controller
{
    public function uploadForm()
    {
        return view('admin.upload-student-index');
    }

    public function upload(Request $request)
{
    $request->validate([
        'excel_file' => 'required|mimes:xlsx,xls',
    ]);

    try {
        Excel::import(new StudentIndexImport, $request->file('excel_file'));
        return back()->with('success', 'Student Index Numbers uploaded successfully!');
    } catch (\Illuminate\Database\QueryException $e) {
        // Check if it's a duplicate entry error
        if ($e->errorInfo[1] == 1062) {
            // Extract duplicate key value from error message (optional)
            preg_match("/Duplicate entry '(.+?)'/", $e->getMessage(), $matches);
            $duplicate = $matches[1] ?? 'a duplicate entry';

            return back()->with('error', "Duplicate entry found: {$duplicate}. Please fix and re-upload.");
        }

        // General database error
        return back()->with('error', 'An error occurred during upload. Please check your file.');
    } catch (\Throwable $e) {
        return back()->with('error', 'Upload failed: ' . $e->getMessage());
    }
}

    public function downloadTemplate()
    {
        return Excel::download(new StudentIndexTemplateExport, 'student_index_template.xlsx');
    }



public function autoRegister(Request $request)
{
    $sessionId = 1;
    $courseIds = [1, 2, 3];
    $successMessages = [];
    $errors = [];

    $indexes = \App\Models\StudentIndex::all();

    if ($indexes->isEmpty()) {
        return back()->with('error', 'No student index records found.');
    }

    foreach ($indexes as $index) {
        $student = \App\Models\Student::where('matric_no', $index->matric_no)->first();

        if (!$student) {
            $errors[] = "❌ Student not found for matric_no: {$index->matric_no}";
            continue;
        }

        foreach ($courseIds as $courseId) {
            // Check if already registered
            $alreadyRegistered = \App\Models\CourseRegistration::where([
                'student_id' => $student->id,
                'course_id' => $courseId,
                'academic_session_id' => $sessionId
            ])->exists();

            if ($alreadyRegistered) {
                $successMessages[] = "⚠️ Already registered: {$student->matric_no} for course {$courseId}";
                continue;
            }

            try {
                $registration = \App\Models\CourseRegistration::create([
                    'student_id' => $student->id,
                    'course_id' => $courseId,
                    'academic_session_id' => $sessionId,
                ]);

                if ($registration) {
                    $successMessages[] = "✅ Successfully registered student {$student->matric_no} for course {$courseId}.";
                } else {
                    $errors[] = "❌ Failed to register student {$student->matric_no} for course {$courseId}.";
                }
            } catch (\Throwable $e) {
                $errors[] = "❌ Exception for {$student->matric_no}, course {$courseId}: " . $e->getMessage();
            }
        }
    }

    $finalMessage = implode('<br>', $successMessages);

    if (count($errors)) {
        $finalMessage .= '<br><br><strong>Errors:</strong><br>' . implode('<br>', $errors);
        return back()->with('warning', $finalMessage);
    }

    return back()->with('success', $finalMessage);
}
}
