<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\CourseRegistration;
use App\Models\Exam;
use App\Models\AcademicSession;
use App\Models\QuestionBank;
use App\Models\ExamProgress;
use App\Models\StudentIndex;
use App\Models\ActiveExamSession;
use Illuminate\Support\Str;
use App\Models\ShuffledQuestion;

class CBTController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'passcode' => 'required|string',
            'exam_id' => 'required|exists:exams,id',
        ]);

        $identifier = trim($request->input('identifier'));
        $surname = strtolower(trim($request->input('passcode')));
        $exam = Exam::with('course', 'questionBanks')->findOrFail($request->exam_id);

        $student = null;

        // 🟨 PNE special case
        if ($exam->course && $exam->course->name === 'Pre-National Exam') {
            $index = StudentIndex::where('index_number', $identifier)->first();
            if (!$index) {
                return back()->withErrors(['identifier' => 'Invalid index number.']);
            }

            $student = Student::where('matric_no', $index->matric_no)->first();
            if (!$student) {
                return back()->withErrors(['identifier' => 'No student found for this index number.']);
            }
        } else {
            $student = Student::where('matric_no', $identifier)->first();
            if (!$student) {
                return back()->withErrors(['identifier' => 'matric number do not exist.']);
            }
        }

        // 🔒 Surname check
        if (strtolower($student->surname) !== $surname) {
            return back()->withErrors(['passcode' => 'Incorrect surname as passcode.']);
        }

        if (!$student->can_access_exam) {
            return back()->withErrors(['identifier' => 'You are not allowed to take the exam at this time. Communicate to the ICT.']);
        }

        $activeSession = AcademicSession::where('is_active', true)->first();
        if (!$activeSession) {
            return back()->withErrors(['exam_id' => 'No active academic session found.']);
        }

        if ($exam->course->name !== 'Pre-National Exam') {
            $isRegistered = CourseRegistration::where('student_id', $student->id)
                ->where('course_id', $exam->course_id)
                ->where('academic_session_id', $activeSession->id)
                ->exists();

            if (!$isRegistered) {
                return back()->withErrors(['exam_id' => 'You are not registered for this course in the current academic session.']);
            }
        }

        $alreadyAttempted = \App\Models\ExamResult::where('student_id', $student->id)
            ->where('exam_id', $exam->id)
            ->exists();

        if ($alreadyAttempted) {
            return back()->withErrors(['exam_id' => 'You have already taken this exam.']);
        }

        // 🔐 Enforce single session
        $existingSession = ActiveExamSession::where('student_id', $student->id)
            ->where('exam_id', $exam->id)
            ->first();

        if ($existingSession && $existingSession->session_token) {
            return back()->withErrors(['exam_id' => 'You are already logged into this exam from another browser or device.']);
        }

        // ✅ Generate session token and save
        $token = Str::uuid()->toString();

        ActiveExamSession::updateOrCreate(
            ['student_id' => $student->id, 'exam_id' => $exam->id],
            ['session_token' => $token]
        );

        session([
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'session_token' => $token,
        ]);

        // ✅ Shuffle only once per student-exam
        

      $existingShuffled = ShuffledQuestion::where([
    'student_id' => $student->id,
    'exam_id' => $exam->id,
])->first();

if (!$existingShuffled) {
    // First time login, shuffle and apply question limit
    $questionIds = $exam->questionBanks->pluck('id')->shuffle();

    if ($exam->question_limit) {
        $questionIds = $questionIds->take($exam->question_limit);
    }

    $shuffled = ShuffledQuestion::create([
        'student_id' => $student->id,
        'exam_id' => $exam->id,
        'question_order' => $questionIds->values()->toJson(),
        ]);

        $shuffledIds = $questionIds->values()->all();
        } else {
            // Use existing question order, but still respect question limit
            $ids = json_decode($existingShuffled->question_order, true);

            $shuffledIds = $exam->question_limit
                ? array_slice($ids, 0, $exam->question_limit)
                : $ids;
        }

        // ✅ Save to session
        session(['shuffled_questions' => $shuffledIds]);


        return redirect()->route('exam.start', $exam->id);
    }

    public function startExam($id)
    {
        $exam = Exam::with('course', 'questionBanks')->findOrFail($id);
        $studentId = session('student_id');
        $sessionToken = session('session_token');

        if (!$studentId || !$sessionToken) {
            return redirect()->route('exam.login')->withErrors(['login' => 'Session expired. Please login again.']);
        }

        // 🔒 Validate session token
        $activeSession = ActiveExamSession::where('student_id', $studentId)
            ->where('exam_id', $exam->id)
            ->first();

        if (!$activeSession || $activeSession->session_token !== $sessionToken) {
            return redirect()->route('exam.login')->withErrors([
                'login' => 'You have been logged out due to another login from a different browser or device.',
            ]);
        }

        $student = Student::find($studentId);
        $matric = $student->matric_no ?? null;
        $passport = $student->passport ?? null;

        $shuffled = \App\Models\ShuffledQuestion::where('student_id', $studentId)
            ->where('exam_id', $exam->id)
            ->first();

        $shuffledIds = $shuffled ? json_decode($shuffled->question_order, true) : [];


        $questions = QuestionBank::whereIn('id', $shuffledIds)
            ->get()
            ->sortBy(function ($q) use ($shuffledIds) {
                return array_search($q->id, $shuffledIds);
            })
            ->values();

// Original $progress grouped by question_id, used in Blade
$progress = ExamProgress::where('student_id', $studentId)
    ->where('exam_id', $exam->id)
    ->get()
    ->groupBy('question_id')
    ->map(function ($items) {
        $decoded = [];
        foreach ($items as $item) {
            $val = json_decode($item->selected_option, true);
            if (is_array($val)) {
                $decoded = array_merge($decoded, $val);
            }
        }
        return $decoded;
    });

// Get shuffled questions for this student
$shuffledIds = $shuffled ? json_decode($shuffled->question_order, true) : [];

// Get all progress records for this student and exam, limited to only the shuffled questions
$rawProgress = ExamProgress::where('student_id', $studentId)
    ->where('exam_id', $exam->id)
    ->whereIn('question_id', $shuffledIds)
    ->get()
    ->groupBy('question_id') // ✅ group by question_id to avoid duplicate count
    ->filter(function ($grouped) {
        $first = $grouped->first(); // use only the first record per question
        $answers = json_decode($first->selected_option, true);
        return is_array($answers) && count($answers) > 0;
    });

// ✅ Count only one attempt per question
$attemptedCount = $rawProgress->count();

        return view('student.exams.exam', compact('exam', 'questions', 'student', 'matric', 'passport', 'progress',  'attemptedCount'));
    }
public function saveProgress(Request $request)
{
    // ✅ Validate incoming data
    $validated = $request->validate([
        'exam_id' => 'required|integer|exists:exams,id',
        'question_id' => 'required|integer|exists:question_banks,id',
        'type' => 'required|string|in:single,multiple',
        'answers' => 'required|array', // ✅ must be array
        'answers.*' => 'string' // ✅ each answer as string
    ]);

    $studentId = session('student_id');

    if (!$studentId) {
        return response()->json([
            'error' => 'Session expired. Please login again.'
        ], 401);
    }

    // ✅ Save one row per question
    ExamProgress::updateOrCreate(
        [
            'student_id' => $studentId,
            'exam_id' => $validated['exam_id'],
            'question_id' => $validated['question_id'],
        ],
        [
            'selected_option' => json_encode($validated['answers']),
        ]
    );

    return response()->json(['status' => 'ok']);
}

    public function availableExams()
    {
        $exams = Exam::where('is_active', true)->with('course')->get();

        $courses = $exams->map(function ($exam) {
            $course = $exam->course;
            $course->exam = $exam;
            return $course;
        })->unique('id');

        return view('student.exams.exam-grid', compact('courses'));
    }

    public function showLoginDropdownForm()
    {
        $exams = Exam::with('course', 'questionBanks')->where('is_active', true)->get();
        $settings = \App\Models\Setting::first();

        return view('student.exams.exam-login', compact('exams', 'settings'));
    }

    public function showLoginForm($id)
    {
        $exam = Exam::with('course')->findOrFail($id);
        return view('student.exams.exam-login', compact('exam'));
    }
}
