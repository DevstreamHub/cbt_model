@extends('layouts.app')

@section('title', 'Monitor Exam - ' . $exam->course->name)

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Monitor Exam: {{ $exam->course->name }} ({{ ucfirst($exam->type) }})</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Exams</a></li>
            <li class="breadcrumb-item active">Monitor</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Monitoring {{ $exam->course->name }} Exam</h3>
        </div>
        <div class="card-body">

          <div class="row text-center mb-4">
            <div class="col-md-6 mb-2">
              <button class="btn btn-success w-100" id="btnAttempted">
                Submitted Students (<span id="attemptedCount">{{ $attemptedCount }}</span>)
              </button>
            </div>
            <div class="col-md-6 mb-2">
              <button class="btn btn-warning w-100" id="btnInProgress">
                Students Still Writing (<span id="inProgressCount">{{ $inProgressCount }}</span>)
              </button>
            </div>
          </div>

            <!-- Submitted Students Table -->
            <div id="attemptedSection" style="display:none;">
            <h5>Submitted Students</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Matric No</th>
                    <th>Attempts</th>   <!-- ✅ Add this back -->
                    </tr>
                </thead>
                <tbody id="attemptedTableBody"></tbody>
                </table>
            </div>
            </div>


          <!-- In-Progress Students Table -->
          <div id="inProgressSection" style="display:none;">
            <h5>Students Still Writing</h5>
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Matric No</th>
                    <th>Attempts</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="inProgressTableBody"></tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>

  @include('partials.footer')
</div>
@endsection

@section('scripts')
<!-- SweetAlert -->
<script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.4.5.2.js') }}"></script>

<script>
function loadAttempted() {
    fetch("{{ route('exams.attempted', $exam->id) }}")
        .then(res => res.json())
        .then(data => {
            document.getElementById('attemptedCount').textContent = data.length;
            let tbody = document.getElementById('attemptedTableBody');
            tbody.innerHTML = '';
            data.forEach((item, index) => {
                tbody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.student.surname} ${item.student.other_names}</td>
                        <td>${item.student.matric_no}</td>
                        <td>${item.attempts}</td> <!-- ✅ use same property as inProgress -->
                    </tr>`;
            });

            document.getElementById('attemptedSection').style.display = 'block';
            document.getElementById('inProgressSection').style.display = 'none';
        });
}


function loadInProgress() {
    fetch("{{ route('exams.inProgress', $exam->id) }}")
        .then(res => res.json())
        .then(data => {
            document.getElementById('inProgressCount').textContent = data.length;
            let tbody = document.getElementById('inProgressTableBody');
            tbody.innerHTML = '';
            data.forEach((item, index) => {
                tbody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.student.surname} ${item.student.other_names}</td>
                        <td>${item.student.matric_no}</td>
                        <td>${item.attempts}</td>
                        <td>
                            <button class="btn btn-sm btn-danger" onclick="confirmForceSubmit(${item.student_id})">
                                Force Submit
                            </button>
                        </td>
                    </tr>`;
            });

            document.getElementById('inProgressSection').style.display = 'block';
            document.getElementById('attemptedSection').style.display = 'none';
        });
}

document.getElementById('btnAttempted').addEventListener('click', function() {
    loadAttempted();
});

document.getElementById('btnInProgress').addEventListener('click', function() {
    loadInProgress();
});

function confirmForceSubmit(studentId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will force submit the student's exam!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, submit it!'
    }).then((result) => {
        if (result.isConfirmed) {
            forceSubmit(studentId);
        }
    });
}

function forceSubmit(studentId) {
    fetch(`/exams/{{ $exam->id }}/force-submit/${studentId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(resp => {
        Swal.fire('Done!', resp.message, 'success');
        loadInProgress();
        loadAttempted();
    });
}

// 🔄 Auto-refresh every 10 seconds
setInterval(() => {
    if (document.getElementById('attemptedSection').style.display === 'block') {
        loadAttempted();
    }
    if (document.getElementById('inProgressSection').style.display === 'block') {
        loadInProgress();
    }
}, 10000);
</script>
@endsection
