<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\QuestionBank;
use App\Models\Course;
use App\Models\Question;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelQuestionController extends Controller
{
    /**
     * Upload Excel file and insert questions into QuestionBank.
     */
    public function uploadExcel(Request $request)
    {
        $request->validate([
            'question_file' => 'required|file|mimes:xlsx,xls',
            'course_id'     => 'required|exists:courses,id',
            'exam_id'       => 'required|exists:exams,id',
        ]);

        // Ensure the exam belongs to the selected course
        $exam = Exam::where('id', $request->exam_id)
                    ->where('course_id', $request->course_id)
                    ->first();

        if (!$exam) {
            return back()->with('error', 'Selected exam does not match the selected course.');
        }

        try {
            $file = $request->file('question_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            unset($rows[0]); // Remove header

            foreach ($rows as $row) {
                if (empty($row[0])) continue; // Skip empty rows

                QuestionBank::create([
                    'exam_id'     => $exam->id,
                    'course_id'   => $request->course_id,
                    'question'    => $row[0], // Question
                    'mark'        => $row[1] ?? 1, // Mark
                    'option_type' => $row[2] ?? 'single', // Option Type: single/multiple
                    'options'     => json_encode(array_map('trim', explode(',', $row[3] ?? ''))), // Options
                    'answers'     => json_encode(array_map('trim', explode(',', $row[4] ?? ''))), // Answers
                    'is_active'   => true,
                ]);
            }

            return back()->with('success', 'Questions uploaded successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Return list of exams for the selected course (AJAX).
     */
    public function getExams($course_id)
    {
        $exams = Exam::where('course_id', $course_id)
                     ->select('id', 'type')
                     ->orderBy('type')
                     ->get();

        return response()->json($exams);
    }
}
