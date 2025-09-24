@extends('layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">CBT Instructions</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Instructions</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
        <div class="card-header">
          <h3 class="card-title">How to Use the CBT Model</h3>
        </div>
        <div class="card-body">
  <ol>
    <li><strong>Sync Student Records:</strong> Start by navigating to the <em>Database Sync</em> section and click on <strong>"Sync All Records"</strong>. This updates your CBT system with all student records (matric number, level, department, etc.) from the college database.</li>

    <li><strong>Download Student Passport Photos:</strong> After syncing student data, click on <strong>"Download Images"</strong> to fetch student passport photos. These will be used on student profiles and for printing exam cards and during exam.</li>

    <li><strong>Create an Exam:</strong> Go to the <strong>Exams</strong> menu and create a new exam. Choose a course, define the exam type (Test/Exam), set duration (in minutes), and optionally assign a CBT background image.</li>

    <li><strong>Add Questions:</strong> Go to the <strong>Question Bank</strong>. You can:
      <ul>
        <li>Add questions manually with rich text editor (supports both single and multiple-choice).</li>
        <li>Upload questions in bulk using <strong>Word (.docx)</strong> or <strong>Excel (.xlsx)</strong> templates.</li>
        <li><strong>Note:</strong> Any question that contains an image must be entered manually.</li>
      </ul>
    </li>

    <li><strong>Clear Mistaken Questions:</strong> If errors are found in uploaded questions, delete the question set completely using the <strong>"Clear Questions"</strong> button found in create exam table actions, then upload the corrected set again.</li>

    <li><strong>Activate Questions:</strong> After uploading or entering questions, click the <strong>"Activate"</strong> button to make them visible to students. Inactive sets will not appear during exams.</li>

    <li><strong>Upload Student Index Numbers:</strong> Navigate to <strong>Upload Index</strong> and import the file containing students' <strong>Exam Index Numbers and Matric Numbers</strong> required for CBT verification.</li>

    <li><strong>Customize CBT Background:</strong> Go to <strong>CBT Settings</strong> and upload a custom background image to be displayed behind each exam screen for branding or official appearance.</li>

    <li><strong>Student Login & Exam Start:</strong> Students access their portal using their matric or index number. Once they click "Start Exam", the timer begins and cannot be paused. Each student sees questions in randomized order.</li>

    <li><strong>Restore Interrupted Exams:</strong> If a student’s session is interrupted (e.g., browser closed or power failure), use the <strong>"Clear Session"</strong> button to reset their session so they can start again properly.</li>

    <li><strong>Admin can edit/update his Profile:</strong> From <strong>profile update</strong>, admins can update profile details like <strong>Name, Email, and Password</strong>.</li>

    <li><strong>Restrict Student Access:</strong> To prevent a student from participating in CBT, go to the <strong>Student List</strong> and toggle their <strong>activation status</strong> to <em>Inactive</em>. They will be blocked from taking exams.</li>

    <li><strong>Monitor & Export Results:</strong> After students complete exams, results are stored automatically. Navigate to the <strong>Results</strong> section to view and export results as <strong>Excel (.xlsx)</strong> for upload to portals or analysis.</li>

    <li><strong>Download Exam Cards:</strong> Go to <strong>Exam Cards</strong>, select session and semester, and generate a PDF exam card for each student. Cards include passport, personal details, and all registered courses.</li>
  </ol>

  <p class="mt-3"><strong>Important Note:</strong> Do <span class="text-danger">NOT</span> use the red <strong>"CLEAR CBT TABLES"</strong> button unless you intend to wipe all synchronized student records and CBT data. This action is irreversible and should be used with caution. It can be used done copying all results for new records for new test/exam</p>
</div>

      </div>
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>

  @include('partials.footer')
@endsection
