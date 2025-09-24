@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <!-- Content Header -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Question Bank</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Add Questions</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Upload via Word Form -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-secondary mt-4">
        <div class="card-header">
          <h3 class="card-title">Upload Questions via Word (.docx)</h3>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.questionbank.upload_word') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
              <label>Select Course</label>
              <select name="course_id" id="word_course_id" class="form-control" required>
                <option value="">-- Choose Course --</option>
                @foreach($courses as $course)
                  <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                    {{ $course->name }} - {{ $course->course_code }}
                  </option>
                @endforeach
              </select>

              <select name="exam_id" id="word_exam_id" class="form-control mt-2" required>
                <option value="">-- Choose Exam --</option>
              </select>
            </div>
            <p>
              <a href="{{ asset('exam.docx') }}" class="btn btn-sm btn-outline-primary">
                Download Word Template
              </a>
            </p>
            <div class="form-group">
              <label>Upload .docx File</label>
              <input type="file" name="docx_file" class="form-control" accept=".docx" required>
            </div>

            <button type="submit" class="btn btn-success">Upload Word Questions</button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- Excel Upload Form -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
        <div class="card-header">
          <h3 class="card-title">Upload Questions via Excel</h3>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.questionbank.upload_excel') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
              <label>Select Course</label>
              <select name="course_id" id="excel_course_id" class="form-control" required>
                <option value="">-- Choose Course --</option>
                @foreach($courses as $course)
                  <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                    {{ $course->name }} - {{ $course->course_code }}
                  </option>
                @endforeach
              </select>

              <select name="exam_id" id="excel_exam_id" class="form-control mt-2" required>
                <option value="">-- Choose Exam --</option>
              </select>
            </div>

            <p>
              <a href="{{ asset('exam_questions_template.xlsx') }}" class="btn btn-sm btn-outline-primary">
                Download Excel Template
              </a>
            </p>

            <div class="form-group">
              <label>Upload Completed Excel File</label>
              <input type="file" name="question_file" class="form-control" accept=".xlsx" required>
            </div>

            <button type="submit" class="btn btn-success">Upload Questions</button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- Manual Entry Form -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-success">
        <div class="card-header">
          <h3 class="card-title">Add Questions Manually</h3>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.questionbank.store') }}">
            @csrf

            <div class="form-group">
              <label>Select Course</label>
              <select name="course_id" id="manual_course_id" class="form-control" required>
                <option value="">-- Choose Course --</option>
                @foreach($courses as $course)
                  <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                    {{ $course->name }} - {{ $course->course_code }}
                  </option>
                @endforeach
              </select>

              <select name="exam_id" id="manual_exam_id" class="form-control mt-2" required>
                <option value="">-- Choose Exam --</option>
              </select>
            </div>

            <div id="questions-container"></div>
            <button type="button" class="btn btn-info mt-2" onclick="addQuestion()">+ Add Question</button>
            <br><br>
            <button type="submit" class="btn btn-success">Save All Questions</button>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>



@include('partials.footer')

<!-- JavaScript remains unchanged except removing the entry_mode logic -->
<script>
  let count = 0;

  function addQuestion() {
    let html = `
      <div class="card p-3 mb-3">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
          <span class="font-weight-bold">New Question</span>
          <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.card').remove()">Remove</button>
        </div>
        <div class="card-body">
          <div class="form-group">
            <label>Question</label>
            <textarea name="questions[${count}][question]" class="form-control summernote" required></textarea>
          </div>
          <div class="form-group">
            <label>Mark</label>
            <input type="number" name="questions[${count}][mark]" class="form-control" value="1" min="1" required>
          </div>
          <div class="form-group">
            <label>Option Type</label>
            <select name="questions[${count}][option_type]" class="form-control" onchange="toggleAnswerMode(this, ${count})" required>
              <option value="">-- Select Option Type --</option>
              <option value="single">Single Choice</option>
              <option value="multiple">Multiple Choice</option>
            </select>
          </div>
          <div class="form-group">
            <label>Options</label>
            <div id="options-${count}"></div>
            <button type="button" class="btn btn-sm btn-primary mt-2" onclick="addOption(${count})">+ Add Option</button>
          </div>
        </div>
      </div>
    `;
    $('#questions-container').append(html);
    setTimeout(() => {
      $('.summernote').last().summernote({ height: 200 });
    }, 100);
    count++;
  }

  function addOption(qIndex) {
    const container = document.getElementById(`options-${qIndex}`);
    const optionCount = container.querySelectorAll('.option-input').length;
    const html = `
      <div class="input-group mb-2" id="option-${qIndex}-${optionCount}">
        <input type="text" name="questions[${qIndex}][options][]" class="form-control option-input" placeholder="Option ${optionCount + 1}" required>
        <div class="input-group-append">
          <span class="input-group-text">
            <input type="checkbox" class="answer-${qIndex}" name="questions[${qIndex}][answers][]" value="${optionCount}">
          </span>
          <button type="button" class="btn btn-danger btn-sm" onclick="removeOption('option-${qIndex}-${optionCount}')">&times;</button>
        </div>
      </div>`;
    container.insertAdjacentHTML('beforeend', html);
  }

  function removeOption(id) {
    document.getElementById(id).remove();
  }

  function toggleAnswerMode(select, index) {
    const type = select.value;
    const options = document.querySelectorAll(`.answer-${index}`);
    options.forEach(opt => {
      opt.type = type === 'single' ? 'radio' : 'checkbox';
      opt.name = `questions[${index}][answers][]`;
    });
  }
</script>

<!-- Scripts -->
<script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.4.5.2.js') }}"></script>
<script src="{{ asset('js/summernote.min.js') }}"></script>
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script>
  let count = 0;

  $(function () {
    $('.select2').select2();

    $('#course_id').on('change', function () {
  const selectedId = $(this).val();
  if (selectedId) {
    $('#selectedCourseId').val(selectedId);
    $('#excelCourseId').val(selectedId);
    $('#question-section').show();
    $('#questions-container').html('');
    $('#entry_mode').val('');
    $('#manualForm').hide();
    $('#excelForm').hide();
    count = 0;
  } else {
    $('#question-section').hide();
    $('#manualForm').hide();
    $('#excelForm').hide();
    $('#questions-container').html('');
  }
});

  });

  function addQuestion() {
    let questionId = `question-${count}`;
    let html = `
      <div class="card p-3 mb-3" id="${questionId}">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
          <span class="font-weight-bold">New Question</span>
          <button type="button" class="btn btn-sm btn-danger" onclick="removeQuestion('${questionId}')">Remove</button>
        </div>
        <div class="card-body">
          <div class="form-group">
            <label>Enter Question</label>
            <textarea name="questions[${count}][question]" class="form-control summernote" required></textarea>
          </div>
          <div class="form-group">
            <label>Mark</label>
            <input type="number" name="questions[${count}][mark]" class="form-control" value="1" min="1" required>
          </div>
          <div class="form-group">
            <label>Option Type</label>
            <select name="questions[${count}][option_type]" class="form-control mb-2" onchange="toggleAnswerMode(this, ${count})" required>
              <option value="">-- Select Option Type --</option>
              <option value="single">Single Choice</option>
              <option value="multiple">Multiple Choice</option>
            </select>
          </div>
          <div class="form-group">
            <label>Options</label>
            <div id="options-${count}"></div>
            <button type="button" class="btn btn-sm btn-primary mt-2" onclick="addOption(${count})">+ Add Option</button>
          </div>
        </div>
      </div>
    `;

    $('#questions-container').append(html);

    setTimeout(() => {
      $('.summernote').last().summernote({
        height: 200,
        placeholder: 'Write your question here...',
        tabsize: 2
      });
    }, 100);

    count++;
  }

  function removeQuestion(id) {
    document.getElementById(id).remove();
  }

  function addOption(qIndex) {
    const container = document.getElementById(`options-${qIndex}`);
    const optionCount = container.querySelectorAll('.option-input').length;

    const optionId = `option-${qIndex}-${optionCount}`;

    const html = `
      <div class="input-group mb-2" id="${optionId}">
        <input type="text" name="questions[${qIndex}][options][]" class="form-control option-input" placeholder="Option ${optionCount + 1}" required>
        <div class="input-group-append">
          <span class="input-group-text">
            <input type="checkbox" class="answer-${qIndex}" name="questions[${qIndex}][answers][]" value="${optionCount}">
          </span>
          <button type="button" class="btn btn-danger btn-sm" onclick="removeOption('${optionId}')">&times;</button>
        </div>
      </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
  }

  function removeOption(optionId) {
    document.getElementById(optionId).remove();
  }

  function toggleAnswerMode(select, index) {
    const type = select.value;
    const options = document.querySelectorAll(`.answer-${index}`);
    options.forEach(opt => {
      opt.type = type === 'single' ? 'radio' : 'checkbox';
      opt.name = `questions[${index}][answers][]`;
    });
  }
</script>
<!-- SweetAlert Script -->
<script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
<script>
  @if(session('success'))
    Swal.fire({
      title: 'Success!',
      text: "{{ session('success') }}",
      icon: 'success',
      confirmButtonText: 'OK'
    });
  @endif

  @if(session('error'))
    Swal.fire({
      title: 'Error!',
      text: "{{ session('error') }}",
      icon: 'error',
      confirmButtonText: 'OK'
    });
  @endif
</script>
<!-- AJAX Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const formConfigs = [
    { courseId: 'manual_course_id', examId: 'manual_exam_id' },
    { courseId: 'excel_course_id', examId: 'excel_exam_id' },
    { courseId: 'word_course_id', examId: 'word_exam_id' }
  ];

  const oldExamId = "{{ old('exam_id') }}";

  formConfigs.forEach(config => {
    const courseSelect = document.getElementById(config.courseId);
    const examSelect = document.getElementById(config.examId);

    if (!courseSelect || !examSelect) return;

    function loadExams(courseId) {
      examSelect.innerHTML = '<option value="">-- Choose Exam --</option>';

      if (courseId) {
        fetch(`/get-exams/${courseId}`)
          .then(response => response.json())
          .then(data => {
            data.forEach(exam => {
              const option = document.createElement('option');
              option.value = exam.id;
              option.textContent = exam.type.charAt(0).toUpperCase() + exam.type.slice(1);
              if (exam.id == oldExamId) option.selected = true;
              examSelect.appendChild(option);
            });
          });
      }
    }

    courseSelect.addEventListener('change', function () {
      loadExams(this.value);
    });

    if (courseSelect.value) {
      loadExams(courseSelect.value);
    }
  });
});
</script>


@endsection
