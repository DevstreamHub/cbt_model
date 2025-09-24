<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use App\Models\QuestionBank;
use App\Models\Course;
use App\Models\Exam;

class QuestionImportController extends Controller
{
    public function importFromDocx(Request $request)
    {
        $request->validate([
            'docx_file' => 'required|file|mimes:docx',
            'course_id' => 'required|exists:courses,id',
            'exam_id'   => 'required|exists:exams,id',
        ]);

        $course = Course::find($request->course_id);
        $exam = Exam::where('id', $request->exam_id)
                    ->where('course_id', $request->course_id)
                    ->first();

        if (!$exam) {
            return back()->with('error', 'The selected exam does not belong to the selected course.');
        }

        try {
            $phpWord = IOFactory::load($request->file('docx_file')->getPathname());
        } catch (\Exception $e) {
            return back()->with('error', 'Could not read Word document: ' . $e->getMessage());
        }

        $text = '';
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                }
            }
        }

        // Split questions by number prefix (e.g., "1.")
        $blocks = preg_split('/\n(?=\d+\.\s)/', $text);
        $imported = 0;

        foreach ($blocks as $block) {
            $block = trim($block);
            if (empty($block)) continue;

            // Extract question
            if (!preg_match('/^\d+\.\s+(.*?)\n/i', $block, $qMatch)) continue;
            $question = trim($qMatch[1]);

            // Extract options
            preg_match_all('/[a-d]\)\s+(.*?)(?=\n|$)/i', $block, $optMatches);
            $options = array_map('trim', $optMatches[1] ?? []);
            if (count($options) < 2) continue;

            // Extract answers
            preg_match('/Answer:\s*([a-d](?:,[a-d])*)/i', $block, $ansMatch);
            if (!isset($ansMatch[1])) continue;

            $answerLetters = explode(',', strtolower(trim($ansMatch[1])));
            $optionMap = ['a' => 0, 'b' => 1, 'c' => 2, 'd' => 3];
            $answers = [];

            foreach ($answerLetters as $letter) {
                $index = $optionMap[$letter] ?? null;
                if ($index !== null) {
                    $answers[] = $index;
                }
            }

            // Determine option type
            $optionType = count($answers) > 1 ? 'multiple' : 'single';

            // Extract mark
            preg_match('/Mark:\s*(\d+)/i', $block, $markMatch);
            $mark = isset($markMatch[1]) ? (int)$markMatch[1] : 1;

            QuestionBank::create([
                'course_id'   => $course->id,
                'exam_id'     => $exam->id,
                'question'    => $question,
                'option_type' => $optionType,
                'mark'        => $mark,
                'options'     => $options,
                'answers'     => json_encode($answers),
                'is_active'   => true,
            ]);

            $imported++;
        }

        if ($imported === 0) {
            return back()->with('error', 'No valid questions were found in the Word document.');
        }

        return back()->with('success', "$imported question(s) uploaded successfully.");
    }
}
