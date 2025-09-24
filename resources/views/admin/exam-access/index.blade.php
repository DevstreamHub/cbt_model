@extends('layouts.app')

@section('title', 'Manage Exam Access')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Manage Exam Access</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Exam Access</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      
      <!-- Filter Form -->
      <div class="card card-primary">
        <div class="card-header">
          <h3 class="card-title">Filter Students</h3>
        </div>
        <div class="card-body">
          <form method="GET" class="row g-3">
            <div class="col-md-4">
              <label>Level</label>
              <select name="level_id" class="form-control">
                <option value="">All Levels</option>
                @foreach($levels as $level)
                  <option value="{{ $level->id }}" {{ request('level_id') == $level->id ? 'selected' : '' }}>
                    {{ $level->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label>Department</label>
              <select name="department_id" class="form-control">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                  <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                    {{ $dept->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4 align-self-end">
              <button class="btn btn-primary w-100">Filter</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Student Table -->
      <div class="card card-white">
        <div class="card-header bg-light">
          <h3 class="card-title text-dark">Student Exam Access List</h3>
        </div>
        <div class="card-body">
          <table class="table table-bordered table-striped mb-0">
            <thead>
              <tr>
                <th>Name</th>
                <th>Matric No</th>
                <th>Level</th>
                <th>Department</th>
                <th>Exam Access</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($students as $student)
                <tr id="student-{{ $student->id }}">
                  <td>{{ $student->surname }} {{ $student->other_names }}</td>
                  <td>{{ $student->matric_no }}</td>
                  <td>{{ $student->level->name ?? '-' }}</td>
                  <td>{{ $student->department->name ?? '-' }}</td>
                  <td>
                    <span class="badge bg-{{ $student->can_access_exam ? 'success' : 'danger' }}">
                      {{ $student->can_access_exam ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-{{ $student->can_access_exam ? 'danger' : 'success' }}"
                            onclick="toggleAccess({{ $student->id }})">
                      {{ $student->can_access_exam ? 'Deactivate' : 'Activate' }}
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center">No students found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->

</div>
@include('partials.footer')
@endsection

@section('scripts')
<script>
  function toggleAccess(studentId) {
    const url = `{{ url('admin/exam-access/toggle') }}/${studentId}`;
    
    fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      }
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        location.reload();
      } else {
        alert('Toggle failed.');
      }
    })
    .catch(() => alert('Something went wrong.'));
  }
</script>
@endsection
