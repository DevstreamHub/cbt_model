<?php

use App\Http\Controllers\ExamSubmissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SyncSettingsController;
use App\Http\Controllers\SyncPreviewController;
use App\Http\Controllers\QuestionBankController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\CBTController;
use App\Http\Controllers\ExcelQuestionController;
use App\Http\Controllers\StudentIndexController;
use App\Http\Controllers\Admin\ExamCardController;
use App\Http\Controllers\BackgroundImageController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\AdminForgotPasswordController;
use App\Http\Controllers\QuestionImportController;
use App\Http\Controllers\Admin\ImageDownloadController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\AdminExamController;
use App\Http\Controllers\ExamMonitorController;

Route::get('/exams/{exam}/monitor', [ExamMonitorController::class, 'index'])->name('exams.monitor');

// Ajax calls for modal data
Route::get('/exams/{exam}/attempted', [ExamMonitorController::class, 'attempted'])->name('exams.attempted');
Route::get('/exams/{exam}/in-progress', [ExamMonitorController::class, 'inProgress'])->name('exams.inProgress');
Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');

// Force submit
Route::post('/exams/{exam}/force-submit/{student}', [ExamMonitorController::class, 'forceSubmit'])->name('exams.forceSubmit');
// Results clearing
Route::get('/admin/results/clear', [ResultController::class, 'showResultClearPage'])->name('admin.results.clear');
Route::post('/admin/results/clear', [ResultController::class, 'clearStudentResults'])->name('admin.clear.results');
Route::post('/admin/results/clear-all', [ResultController::class, 'clearAllResults'])->name('admin.clear.all.results');


Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('exam-access', [App\Http\Controllers\ExamAccessController::class, 'index'])->name('exam-access.index');
    Route::post('exam-access/toggle/{student}', [App\Http\Controllers\ExamAccessController::class, 'toggle'])->name('exam-access.toggle');
});
Route::get('/keep-alive', function () {
    return response()->json([
        'token' => csrf_token(),
        'status' => 'alive'
    ]);
})->name('keep-alive');

Route::prefix('admin')->name('admin.')->group(function () {
    // Route without examId for main monitoring page
    Route::get('/exam-monitor', [AdminExamController::class, 'monitor'])
        ->name('exam.monitor');

    // Optional: route for monitoring a specific exam
    Route::get('/exam-monitor/{examId}', [AdminExamController::class, 'monitor'])
        ->name('exam.monitor.exam');
});


Route::post('/admin/questionbank/upload-excel', [ExcelQuestionController::class, 'uploadExcel'])
    ->name('admin.questionbank.upload_excel');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('results', [ResultController::class, 'index'])->name('result.index');
    Route::post('results/copy', [ResultController::class, 'copy'])->name('result.copy');
    Route::get('result/export', [ResultController::class, 'export'])->name('result.export');
});

Route::post('/admin/questionbank/upload-word', [QuestionImportController::class, 'importFromDocx'])->name('admin.questionbank.upload_word');
Route::get('/cbt/sync-images', [SyncSettingsController::class, 'syncImages'])->name('cbt.sync.images');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/cbt/sync/download-images', [ImageDownloadController::class, 'download'])->name('download.images.run');
});

Route::get('/get-exams/{courseId}', [App\Http\Controllers\ExamController::class, 'getExamsByCourse']);

Route::post('/admin/exams/delete-questions', [ExamController::class, 'deleteQuestions'])->name('admin.exams.deleteQuestions');

Route::get('/admin/clear-session', [SessionController::class, 'showSessionClearPage'])
    ->name('admin.clear.session.page');

Route::post('/admin/clear-session', [SessionController::class, 'clearStudentSessions'])
    ->name('admin.clear.session');
Route::post('/admin/clear-all-sessions', [SessionController::class, 'clearAllSessions'])->name('admin.clear.all.sessions');

Route::get('/admin/questions/view', [App\Http\Controllers\QuestionViewController::class, 'index'])->name('admin.questions.view');
Route::get('/admin/questions/fetch', [App\Http\Controllers\QuestionViewController::class, 'fetch'])->name('admin.questions.fetch');

// Public route
Route::get('/', function () {
    return view('welcome');
});

// Dashboard (requires auth & email verification)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update'); // ← change to PUT
});

Route::get('/admin/forgot-password', [AdminForgotPasswordController::class, 'showForm'])->name('admin.password.request');
Route::post('/admin/forgot-password', [AdminForgotPasswordController::class, 'resetPassword'])->name('admin.password.reset');


// CBT Sync routes (can also wrap this in middleware if needed)
// CBT Sync routes (only for logged-in users)
Route::middleware('auth')->prefix('cbt/sync')->group(function () {
    Route::get('/settings', [SyncSettingsController::class, 'index'])->name('sync.settings');
    Route::post('/settings/save', [SyncSettingsController::class, 'save'])->name('cbt.sync.save');
    Route::get('/sync-all', [SyncSettingsController::class, 'syncAll'])->name('cbt.sync.all');
    Route::post('/clear-tables', [SyncSettingsController::class, 'clearCbtTables'])->name('cbt.clear.tables');
});
// routes/web.php
Route::middleware('auth')->prefix('question-bank')->group(function () {
    Route::get('/', [QuestionBankController::class, 'index'])->name('questionbank.index');
    Route::get('/create', [QuestionBankController::class, 'selectCourse'])->name('questionbank.select.course');
    Route::get('/create/{course}', [QuestionBankController::class, 'create'])->name('questionbank.create');
    Route::post('/store', [QuestionBankController::class, 'store'])->name('questionbank.store');
    Route::get('/exams/{id}/edit', [ExamController::class, 'edit'])->name('admin.exams.edit');
    Route::post('/exams/{id}/update', [ExamController::class, 'update'])->name('admin.exams.update');
    Route::delete('/exams/{id}', [ExamController::class, 'destroy'])->name('admin.exams.destroy');


});
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/backgrounds', [BackgroundImageController::class, 'index'])->name('backgrounds.index');
    Route::post('/backgrounds', [BackgroundImageController::class, 'store'])->name('backgrounds.store');
    Route::get('/backgrounds/activate/{id}', [BackgroundImageController::class, 'activate'])->name('backgrounds.activate');
    Route::delete('/backgrounds/{id}', [BackgroundImageController::class, 'destroy'])->name('backgrounds.destroy');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/exam-cards', [ExamCardController::class, 'index'])->name('exam_cards.index');
    Route::post('/exam-cards/filter', [ExamCardController::class, 'filter'])->name('exam_cards.filter');
    Route::get('/exam-cards/{student}/download', [ExamCardController::class, 'download'])->name('exam_cards.download');
    Route::post('/exam-cards/print-all', [ExamCardController::class, 'printAll'])->name('exam-cards.printAll');
});

Route::middleware(['auth'])->prefix('question-bank')->group(function () {
    Route::get('/', [QuestionBankController::class, 'index'])->name('questionbank.index');
    Route::get('/edit/{course}', [QuestionBankController::class, 'edit'])->name('questionbank.edit');
    Route::patch('/toggle/{course}', [QuestionBankController::class, 'toggleStatus'])->name('questionbank.toggleStatus');
    Route::get('/question-bank/view/{course}', [QuestionBankController::class, 'view'])->name('questionbank.view');
    Route::get('/question-bank/{course}/edit', [QuestionBankController::class, 'edit'])->name('questionbank.edit');
    Route::put('/question-bank/{course}', [QuestionBankController::class, 'update'])->name('questionbank.update');

});
// ✅ Public (no 'auth' middleware here!)
Route::post('/exam/save-progress', [CBTController::class, 'saveProgress'])->name('exam.save.progress');

Route::get('/admin/upload-student-index', [StudentIndexController::class, 'uploadForm'])->name('student-index.form');
Route::post('/admin/upload-student-index', [StudentIndexController::class, 'upload'])->name('student-index.upload');
Route::post('/student-index/auto-register', [StudentIndexController::class, 'autoRegister'])->name('student-index.auto-register');


Route::get('/exams/completed', function () {
    return view('student.exams.completed');
})->name('exams.completed');
// CBT login dropdown form
Route::get('/student/exams/exam-login', [CBTController::class, 'showLoginDropdownForm'])->name('cbt.exam.login.dropdown');
// Auth scaffolding routes (login, register, etc.)
Route::get('/admin/student-index/template', [StudentIndexController::class, 'downloadTemplate'])->name('student-index.template');

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/exams/create', [ExamController::class, 'create'])->name('exams.create');
    Route::post('/exams/store', [ExamController::class, 'store'])->name('exams.store');
    Route::post('/exams/{exam}/toggle', [ExamController::class, 'toggleStatus'])->name('exams.toggle');

    Route::get('/question-bank/select-course', [QuestionBankController::class, 'selectCourse'])->name('questionbank.select.exam');
    Route::get('/question-bank/create-question', [QuestionBankController::class, 'createQuestion'])->name('questionbank.create-question');
    Route::post('/question-bank/store', [QuestionBankController::class, 'store'])->name('questionbank.store'); // only this
});


Route::prefix('admin/exams')->name('admin.')->group(function () {
    Route::get('/create', [ExamController::class, 'create'])->name('exams.create');
    Route::get('/create-question', [ExamController::class, 'createQuestion'])->name('questionbank.create');
    Route::post('/question-bank/store', [ExamController::class, 'storeQuestionBank'])->name('questionbank.store');
    Route::post('/admin/question-bank/store', [ExamController::class, 'storeQuestionBank'])->name('admin.questionbank.store');
});

// ✅ NOT PRESENT
// Use the dropdown-based login
Route::get('/cbt/login', [CBTController::class, 'showLoginDropdownForm'])->name('exam.login.form');

// Start exam
Route::get('/cbt/start/{id}', [CBTController::class, 'startExam'])->name('exam.start');

// Handle login POST
Route::post('/cbt/login', [CBTController::class, 'login'])->name('exam.login');

// Submit exam (AJAX or post)



Route::post('/exam/submit', [ExamSubmissionController::class, 'submit'])->name('exam.submit');



require __DIR__.'/auth.php';
