@php
    use App\Models\Setting;
    $settings = \App\Models\Setting::first();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exam | {{ $exam->course->name }}</title>
            @if($settings && $settings->logo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $settings->logo) }}">
    @endif
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-icons.css') }}" rel="stylesheet">
<style>
    .question-nav .btn.active {
        background-color: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }

    .question-card {
        transition: all 0.3s ease-in-out;
    }
</style>

    <style>
        body {
            background-color: #f8f9fa;
        }
        .card-body {
            padding: 1.5rem;
        }
        .card img {
            border: 3px solid #007f3e;
        }
        .question-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    padding: 25px;
}

.timer {
    font-weight: bold;
    font-size: 1.3rem;
    padding: 10px 15px;
    background: linear-gradient(135deg, #ff6b6b, #feca57);
    color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 8px rgba(0,0,0,0.15);
}


        .topbar {
            background-color: #321168;
            color: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .question-card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 20px;
        }
        .question-nav .btn {
            width: 40px;
            margin: 3px;
        }
        .answered {
            background-color: #390c8b !important;
            color: white !important;
        }
        .current {
            border: 2px solid #007bff !important;
        }
        .student-box {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            text-align: center;
        }
        .btn-nav {
            width: 100px;
            font-weight: bold;
        }
        .attempted-count {
            font-weight: bold;
            margin: 15px 0 10px;
        }
        .timer {
            font-weight: bold;
            font-size: 1.25rem;
            padding: 15px;
            background: linear-gradient(135deg, #ff6b6b, #feca57);
            color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        @media (max-width: 767px) {
            .topbar {
                flex-direction: column;
                text-align: center;
            }
        }
        .bg-navy {
    background-color: navy !important;
}

    </style>
</head>
<body>
<div class="topbar flex-wrap text-center text-md-start">
    <div class="w-100 fw-bold fs-5"><center>{{ $settings->platform_name ?? 'Platform Name' }} -
        <small>Computer Based Test</small></center></div>
</div>
<div class="container-fluid mt-3">
    <div class="row gy-4">
<div class="col-md-3 col-sm-12">
    <div class="card shadow-sm border-0 text-center h-100">
        <div class="card-body">
            <img src="{{ $passport ? asset('downloaded_images/' . $passport) : asset('default.png') }}"
                alt="Passport"
                class="rounded-circle mb-3 shadow"
                width="120"
                height="120"
                style="object-fit: cover; border: 3px solid #007f3e;">

            <h5 class="fw-bold mb-1">
                {{ $student->surname ?? '' }} {{ $student->other_names ?? '' }}
            </h5>
            <p class="text-muted mb-3" style="font-size: 1rem;">{{ $matric }}</p>

            <div class="text-start" style="font-size: 0.9rem;">
                <p><strong>Level:</strong> {{ $student->level->name ?? '-' }}</p>
                <p><strong>Semester:</strong> {{ $exam->course->semester->name ?? '-' }}</p>
                <p><strong>Department:</strong> {{ $student->department->name ?? '-' }}</p>
                <p><strong>Exam Duration:</strong> {{ $exam->duration }} minutes</p>
            </div>

            <hr class="my-3">

            <div class="mb-3">
                
                <h6 class="text-uppercase text-muted fw-bold mb-1">Time Left</h6>
                <div class="timer" id="timerBox"></div>
            </div>

<button type="button" class="btn btn-danger w-100 mt-2" id="submitNowBtn" onclick="confirmFinalSubmit()" style="display: none;">
    Submit Now
</button>

        </div>
    </div>
</div>

<div class="col-md-8">
    <div class="d-flex justify-content-between align-items-center bg-navy text-white px-3 py-2 rounded shadow-sm mb-3 flex-wrap">
        <h5 class="mb-0 fw-bold">
            Course: {{ $exam->course->name }}
        </h5>
        <button class="btn btn-light btn-sm mt-2 mt-md-0" data-bs-toggle="modal" data-bs-target="#calcModal">
             Calculator
        </button>
    </div>

    <form id="examForm" method="POST" action="{{ route('exam.submit') }}">
        @csrf
        <input type="hidden" name="exam_id" value="{{ $exam->id }}">

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
{{-- QUESTIONS --}}
@foreach($questions as $index => $question)
    @php
        $options = is_array($question->options) ? $question->options : json_decode($question->options, true);
        $labels = range('A', 'Z');
        $inputType = $question->option_type === 'multiple' ? 'checkbox' : 'radio';
        $inputName = $question->option_type === 'multiple'
            ? "answers[{$question->id}][]"
            : "answers[{$question->id}]";

        $rawSaved = $progress[$question->id] ?? null;
        $savedAnswers = is_array($rawSaved) ? $rawSaved : json_decode($rawSaved, true) ?? [];
    @endphp

    <div class="question-card mb-4 p-3 border rounded shadow-sm"
         id="question-{{ $index }}"
         style="display: {{ $index === 0 ? 'block' : 'none' }};">
        <h5 class="d-flex justify-content-between align-items-center text-primary fw-bold mb-3">
            <span>Question {{ $index + 1 }} <small class="text-muted">/ {{ count($questions) }}</small></span>
            <span class="badge bg-secondary">Course: {{ $exam->course->course_code ?? '---' }}</span>
        </h5>

        <div class="mb-3">{!! $question->question !!}</div>
        <hr>

        @foreach($options as $key => $option)
            <div class="form-check mb-2">
                <input class="form-check-input"
                       type="{{ $inputType }}"
                       name="{{ $inputName }}"
                       value="{{ $key }}"
                       id="q{{ $index }}_{{ $key }}"
                       onchange="saveAnswer({{ $question->id }}, '{{ $question->option_type === "multiple" ? "multiple" : "single" }}', {{ $index }})"
                       {{ is_array($savedAnswers) && in_array((string)$key, $savedAnswers) ? 'checked' : '' }}>
                <label class="form-check-label" for="q{{ $index }}_{{ $key }}">
                    <strong>{{ $labels[$key] }}.</strong> {{ $option }}
                </label>
            </div>
        @endforeach
    </div>
@endforeach
{{-- NAVIGATION BUTTONS --}}
<div class="d-flex justify-content-center flex-wrap gap-2 mb-3">
    <button type="button" class="btn btn-outline-secondary" onclick="prevQuestion()">← Previous</button>
    <button type="button" class="btn btn-outline-primary" id="nextBtn" onclick="nextQuestion()">Next →</button>
</div>


        {{-- ATTEMPT COUNT --}}
<div class="text-center mb-2">
    Attempted: <span id="attemptedCount">{{ $attemptedCount }}</span> / {{ count($questions) }}
</div>


{{-- QUESTION NAVIGATION --}}
<div class="question-nav d-flex flex-wrap justify-content-center gap-2 mb-4">
    @foreach($questions as $q)
        <button type="button"
                class="btn btn-sm btn-outline-secondary"
                onclick="showQuestion({{ $loop->index }})"
                id="btn-q{{ $loop->index }}">
            {{ $loop->iteration }}
        </button>
    @endforeach
</div>

        </div>
    </form>
</div>
        
    </div>
</div>

<!-- CALCULATOR MODAL -->
<div class="modal fade" id="calcModal" tabindex="-1" aria-labelledby="calcModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="calcModalLabel">Scientific Calculator</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="calcDisplay" class="form-control mb-2 text-end">
                <div class="d-grid gap-1">
                    <div class="d-flex gap-1">
                        <button class="btn btn-light flex-fill" onclick="calcInput('Math.sin(')">sin</button>
                        <button class="btn btn-light flex-fill" onclick="calcInput('Math.cos(')">cos</button>
                        <button class="btn btn-light flex-fill" onclick="calcInput('Math.tan(')">tan</button>
                        <button class="btn btn-secondary flex-fill" onclick="backspaceCalc()">⌫</button>
                        <button class="btn btn-danger" onclick="clearCalc()">C</button>
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-light flex-fill" onclick="calcInput('7')">7</button>
                        <button class="btn btn-light flex-fill" onclick="calcInput('8')">8</button>
                        <button class="btn btn-light flex-fill" onclick="calcInput('9')">9</button>
                        <button class="btn btn-light flex-fill" onclick="calcInput('/')">/</button>
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-light flex-fill" onclick="calcInput('4')">4</button>
                        <button class="btn btn-light flex-fill" onclick="calcInput('5')">5</button>
                        <button class="btn btn-light flex-fill" onclick="calcInput('6')">6</button>
                        <button class="btn btn-light flex-fill" onclick="calcInput('*')">*</button>
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-light flex-fill" onclick="calcInput('1')">1</button>
                        <button class="btn btn-light flex-fill" onclick="calcInput('2')">2</button>
                        <button class="btn btn-light flex-fill" onclick="calcInput('3')">3</button>
                        <button class="btn btn-light flex-fill" onclick="calcInput('-')">-</button>
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-light flex-fill" onclick="calcInput('0')">0</button>
                        <button class="btn btn-light flex-fill" onclick="calcInput('.')">.</button>
                        <button class="btn btn-success flex-fill" onclick="calculate()">=</button>
                        <button class="btn btn-light flex-fill" onclick="calcInput('+')">+</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
let answersToSave = {}; // { [questionId]: { question_id, exam_id, type, answers: [], dirty: true } }
let autoSaveTimer = null;

document.addEventListener("DOMContentLoaded", () => {
    // Start periodic saver (every 2s)
    if (!autoSaveTimer) {
        autoSaveTimer = setInterval(autoSaveAnswers, 2000);
    }

    // Initialize attempt status for currently visible questions
    document.querySelectorAll('.question-card').forEach((qDiv, idx) => updateAttemptStatus(idx));
});

function saveAnswer(questionId, type, index) {
    const examId = document.querySelector('input[name="exam_id"]').value;
    const questionDiv = document.getElementById(`question-${index}`);
    if (!questionDiv) return;

    let selected = [];

    if (type === 'single') {
        const checked = questionDiv.querySelector('input[type="radio"]:checked');
        selected = checked ? [checked.value] : [];
    } else {
        selected = Array.from(questionDiv.querySelectorAll('input[type="checkbox"]:checked')).map(cb => cb.value);
    }

    const prevJson = answersToSave[questionId] ? JSON.stringify(answersToSave[questionId].answers) : null;
    const newJson  = JSON.stringify(selected);

    answersToSave[questionId] = {
        question_id: questionId,
        exam_id: examId,
        type: type,
        answers: selected,
        dirty: prevJson !== newJson // only mark dirty when changed
    };

    updateAttemptStatus(index);
}

function autoSaveAnswers() {
    let hasDirty = false;

    Object.entries(answersToSave).forEach(([qId, data]) => {
        if (!data.dirty) return;
        hasDirty = true;

        const payload = {
            exam_id: data.exam_id,
            question_id: data.question_id,
            type: data.type,
            answers: data.answers
        };

        fetch("{{ route('exam.save.progress') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(response => {
            if (response.status === 'ok') {
                data.dirty = false;
            } else {
                console.warn('Save failed for q' + qId, response);
            }
        })
        .catch(err => {
            console.error('Auto-save error', err);
        });
    });

    // 🔹 Only ping keep-alive every 5 minutes (300,000 ms)
    const now = Date.now();
    if (!window.lastKeepAlive || now - window.lastKeepAlive >= 300000) {
        fetch('{{ route("keep-alive") }}')
            .then(r => r.json())
            .then(data => {
                // Update meta tag & all _token hidden fields
                document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.token);
                document.querySelectorAll('input[name="_token"]').forEach(el => {
                    el.value = data.token;
                });
                window.lastKeepAlive = now;
                console.log('✅ Session kept alive & CSRF refreshed');
            })
            .catch(err => console.error('Keep-alive failed', err));
    }
}

function updateAttemptStatus(index) {
    const qDiv = document.getElementById('question-' + index);
    if (!qDiv) return;

    const inputs = qDiv.querySelectorAll('input[type=radio], input[type=checkbox]');
    const answered = Array.from(inputs).some(i => i.checked);

    const navBtn = document.getElementById('btn-q' + index);
    if (navBtn) {
        if (answered) {
            navBtn.classList.remove('btn-outline-secondary');
            navBtn.classList.add('btn-success');
        } else {
            navBtn.classList.remove('btn-success');
            navBtn.classList.add('btn-outline-secondary');
        }
    }

    const attemptedCount = document.getElementById('attemptedCount');
const allQuestions = document.querySelectorAll('.question-card');
let answeredCount = 0;

allQuestions.forEach(q => {
    const inputs = q.querySelectorAll('input[type=radio], input[type=checkbox]');
    if (Array.from(inputs).some(i => i.checked)) {
        answeredCount++;
    }
});

if (attemptedCount) attemptedCount.innerText = answeredCount;

}
</script>



<script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
<script>
    let currentQuestion = 0;
    const totalQuestions = {{ count($exam->questionBanks) }};
    const examId = {{ $exam->id }};
    const nextBtn = document.getElementById('nextBtn');
    const localStorageKey = `exam_timer_${examId}`;

    function showQuestion(index) {
        document.getElementById(`question-${currentQuestion}`).style.display = 'none';
        document.getElementById(`question-${index}`).style.display = 'block';
        document.getElementById(`btn-q${currentQuestion}`).classList.remove('current');
        document.getElementById(`btn-q${index}`).classList.add('current');
        currentQuestion = index;
        toggleNextButton();
    }

    function nextQuestion() {
        if (currentQuestion < totalQuestions - 1) {
            showQuestion(currentQuestion + 1);
        } else {
            Swal.fire({
                title: "Submit Exam?",
                text: "Are you sure you want to submit now? You cannot come back.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Submit",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    autoSubmitExam();
                }
            });
        }
    }

    function prevQuestion() {
        if (currentQuestion > 0) showQuestion(currentQuestion - 1);
    }

    function toggleNextButton() {
        nextBtn.innerText = (currentQuestion === totalQuestions - 1) ? 'Submit' : 'Next';
    }

    function markAnswered(index) {
        const btn = document.querySelector(`#btn-q${index}`);
        if (!btn.classList.contains('answered')) {
            btn.classList.add('answered');
            const count = document.getElementById('attemptedCount');
            count.textContent = parseInt(count.textContent) + 1;
        }
    }

   function autoSubmitExam() {
    clearInterval(countdown);
    localStorage.removeItem(localStorageKey);

    const form = document.getElementById('examForm');
    const formData = new FormData(form);

    Swal.fire({
        title: 'Submitting...',
        text: 'Please wait while we save your exam.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // 🔹 Step 1: Keep-alive + Refresh CSRF before submission
    fetch('{{ route("keep-alive") }}')
        .then(r => r.json())
        .then(data => {
            document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.token);
            document.querySelectorAll('input[name="_token"]').forEach(el => {
                el.value = data.token;
            });

            // 🔹 Step 2: Actually submit with fresh token
            return fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': data.token
                },
                credentials: 'same-origin'
            });
        })
        .then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Submitted!',
                text: 'Your exam was submitted successfully.',
                showConfirmButton: false,
                timer: 2000
            });

            setTimeout(() => {
                window.location.href = "{{ route('exam.login') }}";
            }, 2200);
        })
        .catch((error) => {
            console.error('Submission error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Submission Failed',
                text: 'Something went wrong while submitting. Please contact admin.',
                showConfirmButton: true
            }).then(() => {
                window.location.href = "{{ route('exam.login') }}";
            });
        });
}



    // Timer Logic
    const timerDisplay = document.getElementById('timer');
    const timerBox = document.getElementById('timerBox');
    // If no resume flag, clear saved timer
if (!sessionStorage.getItem('resumed_' + {{ $exam->id }})) {
    localStorage.removeItem(localStorageKey);
    sessionStorage.setItem('resumed_' + {{ $exam->id }}, true);
}

    let totalTime = localStorage.getItem(localStorageKey) || ({{ $exam->duration ?? 30 }} * 60);
    totalTime = parseInt(totalTime);

    const countdown = setInterval(() => {
        if (totalTime <= 0) {
            clearInterval(countdown);
            localStorage.removeItem(localStorageKey);
            Swal.fire({
                icon: 'info',
                title: '⏰ Time Up!',
                text: 'Submitting your exam...',
                showConfirmButton: false,
                timer: 1500
            });
            autoSubmitExam();
        } else {
            const min = Math.floor(totalTime / 60);
            const sec = totalTime % 60;
            const timeStr = `${min}m ${sec < 10 ? '0' + sec : sec}s`;
            if (timerDisplay) timerDisplay.textContent = timeStr;
            if (timerBox) timerBox.textContent = timeStr;
            totalTime--;
            localStorage.setItem(localStorageKey, totalTime);
        }
    }, 1000);

    document.addEventListener('DOMContentLoaded', () => {
        showQuestion(0);
        toggleNextButton();
    });

    // Keyboard navigation
    document.addEventListener('keydown', function (e) {
        const key = e.key.toUpperCase();
        const currentQ = document.getElementById(`question-${currentQuestion}`);
        if (!currentQ) return;

        const labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        const index = labels.indexOf(key);
        if (index !== -1) {
            const input = currentQ.querySelector(`#q${currentQuestion}_${index}`);
            if (input) input.click();
        }

        if (e.key === 'ArrowRight') nextQuestion();
        else if (e.key === 'ArrowLeft') prevQuestion();
    });
</script>
<script>
function confirmFinalSubmit() {
    Swal.fire({
        title: "Submit Exam?",
        text: "Are you sure you want to submit now? This cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, Submit",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            autoSubmitExam();
        }
    });
}
</script>

<script>
    let calcDisplay = document.getElementById('calcDisplay');
    document.getElementById('calcModal').addEventListener('shown.bs.modal', function () {
        calcDisplay.focus();
    });
    function calcInput(value) {
        calcDisplay.value += value;
    }

    function clearCalc() {
        calcDisplay.value = '';
    }

    function backspaceCalc() {
        calcDisplay.value = calcDisplay.value.slice(0, -1);
    }

    function calculate() {
        try {
            let result = eval(calcDisplay.value);
            calcDisplay.value = result;
        } catch (e) {
            calcDisplay.value = 'Error';
        }
    }

    // Handle "Enter" key for calculation
   calcDisplay.addEventListener('keydown', function (e) {
    const allowed = ['0','1','2','3','4','5','6','7','8','9','+','-','*','/','.', '(', ')'];
    if (e.key === 'Enter' || e.key === '=') {
        e.preventDefault();
        calculate();
    } else if (
        !allowed.includes(e.key) &&
        !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight'].includes(e.key)
    ) {
        e.preventDefault();
    }
});
</script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const submitBtn = document.getElementById("submitNowBtn");
        const examDuration = {{ $exam->duration ?? 30 }} * 60; // total seconds
        const halfTime = Math.floor(examDuration / 2);

        let remainingTime = localStorage.getItem("exam_timer_{{ $exam->id }}");
        remainingTime = remainingTime ? parseInt(remainingTime) : examDuration;

        const showSubmitCheck = setInterval(() => {
            remainingTime = parseInt(localStorage.getItem("exam_timer_{{ $exam->id }}")) || 0;

            if (remainingTime <= halfTime && submitBtn.style.display === "none") {
                submitBtn.style.display = "block";
                clearInterval(showSubmitCheck); // Stop checking
            }
        }, 1000); // check every second
    });
</script>

<script src="{{ asset('js/bootstrap.bundle.min.5.3.0.js') }}"></script>
</body>
</html>
