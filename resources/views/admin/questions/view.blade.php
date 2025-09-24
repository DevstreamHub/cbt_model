@extends('layouts.app')

@section('title', 'View Questions')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">View Questions</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Questions</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">

      <div class="card mb-4">
        <div class="card-header">
          <h3 class="card-title">📘 Filter Questions by Course & Exam Type</h3>
        </div>
        <div class="card-body">
          <form id="questionFilter">
            @csrf
            <div class="row">
              <div class="col-md-5">
                <div class="form-group">
                  <label for="course_id"><strong>Course</strong></label>
                  <select name="course_id" class="form-control" required>
                    <option value="">-- Select Course --</option>
                    @foreach($courses as $course)
                      <option value="{{ $course->id }}">{{ $course->name }} - {{ $course->course_code }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-md-5">
                <div class="form-group">
                  <label for="exam_type"><strong>Exam Type</strong></label>
                  <select name="exam_type" class="form-control" required>
                    <option value="">-- Select Type --</option>
                    <option value="test">Test</option>
                    <option value="exam">Exam</option>
                    <option value="pre-national">Pre-national</option>
                  </select>
                </div>
              </div>

              <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Fetch Questions</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div id="questionResults" class="card d-none">
        <div class="card-header">
          <h3 class="card-title">📄 Questions</h3>
        </div>
        <div class="card-body">
          <div id="questionTable"></div>
        </div>
      </div>

    </div>
  </section>

  @include('partials.footer')
</div>

<script>
document.getElementById('questionFilter').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const data = new FormData(form);
    const url = "{{ route('admin.questions.fetch') }}";

    fetch(url + '?' + new URLSearchParams(data), {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        },
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            return;
        }

        let html = '';

        data.questions.forEach((q, i) => {
            let optionsHtml = '';
            if (Array.isArray(q.options)) {
                optionsHtml = '<ul>';
                q.options.forEach((opt, idx) => {
                    optionsHtml += `<li><strong>Option ${String.fromCharCode(65 + idx)}:</strong> ${opt}</li>`;
                });
                optionsHtml += '</ul>';
            }

            let answers = '';

try {
    let parsed = typeof q.answers === 'string' ? JSON.parse(q.answers) : q.answers;

    if (Array.isArray(parsed)) {
        answers = parsed.map(ans => String.fromCharCode(65 + parseInt(ans))).join(', ');
    } else {
        answers = parsed;
    }
} catch (e) {
    answers = q.answers; // fallback to raw
}


            html += `
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Q${i + 1}. ${q.question}</h5><br><br>
                        <div><strong>Options:</strong> ${optionsHtml}</div>
                        <p class="mt-2"><strong>Answer:</strong> ${answers}</p>
                        <p><strong>Mark:</strong> ${q.mark} <strong>Option Type:</strong> ${q.option_type}</p>
                    </div>
                </div>
            `;
        });

        document.getElementById('questionTable').innerHTML = html;
        document.getElementById('questionResults').classList.remove('d-none');
    })
    .catch(error => {
        console.error(error);
        alert('Failed to fetch questions.');
    });
});
</script>
<script>
function submitEditForm(e, id) {
    e.preventDefault();
    const form = document.getElementById(`editForm${id}`);
    const formData = new FormData(form);

    fetch(`/admin/questions/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Question updated!');
            $('#editModal' + id).modal('hide');
            document.getElementById('questionFilter').dispatchEvent(new Event('submit')); // reload
        } else {
            alert('Failed to update question.');
        }
    })
    .catch(err => console.error(err));
}

function deleteQuestion(id) {
    if (!confirm('Are you sure you want to delete this question?')) return;

    fetch(`/admin/questions/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Question deleted!');
            document.getElementById('questionFilter').dispatchEvent(new Event('submit')); // reload
        } else {
            alert('Failed to delete.');
        }
    })
    .catch(err => console.error(err));
}
</script>

@endsection
