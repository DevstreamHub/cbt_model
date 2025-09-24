@php
    use App\Models\BackgroundImage;
    $bg = BackgroundImage::active();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CBT | {{ $settings->platform_name ?? 'Platform Name' }}</title>
    @if($settings && $settings->logo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $settings->logo) }}">
    @endif
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5 -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background: #f2f2f2 url('{{ $bg ? asset('storage/' . $bg->file_path) : asset('vendors/img/bg9.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>

    <style>
        .login-card {
            max-width: 500px;
            margin: 6% auto;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(4px);
            padding: 2rem;
        }

        .exam-icon {
            font-size: 3rem;
            color: #4e73df;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #4e73df;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .exam-details {
            font-size: 0.9rem;
            margin-top: 10px;
            display: none;
        }

        .exam-details.active {
            display: block;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <i class="fas fa-edit exam-icon"></i>
         <center>
                    <div class="mb-2">
                        <a href="#" class="brand-link">
                        <img src="{{ asset('vendors/img/logo.jpg') }}" alt="Site Logo" width="50">
                        </a> 
                    </div>
                </center>
        <h4><b>{{ $settings->platform_name ?? 'Platform Name' }}</b></h4>
        <small class="text-muted">Computer Based Test (CBT)</small>
    </div>

    @if($errors->any())
        <div class="alert alert-danger text-center small">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('exam.login') }}" autocomplete="off">
        @csrf

        <!-- Dynamic exam details -->
        <div class="exam-details alert alert-info text-center small" id="exam_info">
            <strong id="exam_title"></strong><br>
            <span>You are about to start a <strong id="exam_type"></strong> for:<br> <strong id="exam_course"></strong> </span>
            <div class="mt-2">
                <p id="exam_desc" style="font-size: 0.85rem;"></p>
                <p><b>Number of Questions:</b> <span id="exam_questions"></span></p>
                <p><b>Duration:</b> <span id="exam_duration"></span> mins</p>
            </div>
        </div>

        <!-- Exam selection -->
        <div class="mb-3">
            <label for="exam_id" class="form-label"><b>Select Available Exam</b></label>
            <select name="exam_id" id="exam_id" class="form-select @error('exam_id') is-invalid @enderror" required onchange="showExamDetails(this)">
                <option value="">-- Choose Exam --</option>
                @foreach($exams as $exam)
                    <option 
                        value="{{ $exam->id }}"
                        data-type="{{ e(ucfirst($exam->type)) }}"
                        data-course="{{ e($exam->course->name ?? 'Course') }}"
                        data-course-code="{{ e($exam->course->course_code ?? '') }}"
                        data-desc="{{ e($exam->description ?? '') }}"
                        data-title="{{ e($exam->title) }}"
                        data-questions="{{ $exam->question_limit ?? $exam->questionBanks?->count() }}"
                        data-duration="{{ $exam->duration }}"
                    >
                        {{ $exam->title }} {{ $exam->course->name ?? 'Course' }} - {{ $exam->course->course_code ?? 'Course' }} ({{ ucfirst($exam->type) }})
                    </option>

                @endforeach
            </select>
            @error('exam_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Matric number -->
        <div class="mb-3">
            <label for="identifier" class="form-label"><b>Student ID</b></label>
            <input type="text" id="identifier" name="identifier" class="form-control @error('identifier') is-invalid @enderror" placeholder="Enter Your ID" required>
            @error('identifier')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Passcode -->
        <div class="mb-3">
            <label for="passcode" class="form-label"><b>Passcode</b></label>
            <input type="text" id="passcode" name="passcode" class="form-control @error('passcode') is-invalid @enderror" placeholder="Enter passcode" required>
            @error('passcode')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-success w-100" onclick="this.disabled=true; this.form.submit();">
            <i class="fas fa-sign-in-alt me-1"></i> Start Selected Exam
        </button>
    </form>
</div>

<script>
    function showExamDetails(select) {
        const option = select.options[select.selectedIndex];
        const infoBox = document.getElementById('exam_info');

        if (option.value !== "") {
            const title = option.dataset.title;
            const type = option.dataset.type;
            const course = option.dataset.course;
            const courseCode = option.dataset.courseCode;
            const desc = option.dataset.desc;
            const questions = option.dataset.questions;
            const duration = option.dataset.duration;

            infoBox.classList.add('active');
            document.getElementById('exam_title').innerText = title;
            document.getElementById('exam_type').innerText = type;
            document.getElementById('exam_course').innerText = `${course} (${courseCode})`;
            document.getElementById('exam_desc').innerText = desc;
            document.getElementById('exam_questions').innerText = questions;
            document.getElementById('exam_duration').innerText = duration;
        } else {
            infoBox.classList.remove('active');
        }
    }
</script>
<script src="{{ asset('js/bootstrap.bundle.min.5.3.0.js') }}"></script>

<!-- Bootstrap Bundle -->

</body>
</html>
