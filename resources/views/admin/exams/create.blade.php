@extends('layouts.app')
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">CBT Management</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Create CBT</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-default">
        <div class="card-header">
          <h3 class="card-title">Create CBT (Test/Exam)</h3>
        </div>

        <div class="card-body">
          @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
          @endif

          <form action="{{ route('admin.exams.store') }}" method="POST">
            @csrf

            <div class="form-group">
              <label for="course_id">Select Course</label>
              <select class="form-control" name="course_id" id="course_id" required>
                <option value="">-- Choose Course --</option>
                @foreach($courses as $course)
                  <option value="{{ $course->id }}">{{ $course->name }} - {{ $course->course_code }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="type">CBT Type</label>
              <select name="type" id="type" class="form-control" required>
                <option value="test">Test</option>
                <option value="exam">Exam</option>
                <option value="pre-national">Pre National</option>
              </select>
            </div>

            <div class="form-group">
              <label for="duration">Duration (minutes)</label>
              <input type="number" name="duration" id="duration" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="question_limit">Number of Questions per Student</label>
                <input type="number" name="question_limit" class="form-control" value="{{ old('question_limit', $exam->question_limit ?? '') }}" min="1" placeholder="e.g. 20">
            </div>


            <button type="submit" class="btn btn-primary">Create CBT</button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
  <div class="container-fluid">
    <div class="card card-default">
      <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Created CBT List</h4>
      </div>

      <div class="card-body">
        @if($exams->isEmpty())
  <div class="alert alert-info">No CBT created yet.</div>
@else
  <div class="table-responsive">
<table class="table table-bordered table-striped table-hover">
  <thead class="thead-dark">
  <tr>
    <th>#</th>
    <th>Course</th>
    <th>Type</th>
    <th>Duration (mins)</th>
    <th>Questions Count</th>
    <th>Questions Per Student</th> {{-- ✅ New Column --}}
    <th>Status</th>
    <th>Toggle</th>
    <th>Actions</th>
    <th>Created At</th>
  </tr>
</thead>
  <tbody>
    @foreach($exams as $index => $exam)
      @php
        $key = $exam->id . '-' . $exam->course_id;
      @endphp
      <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $exam->course->name ?? 'N/A' }} ({{ $exam->course->course_code ?? 'N/A' }})</td>
        <td>{{ ucfirst($exam->type) }}</td>
        <td>{{ $exam->duration }}</td>
        <td>{{ $questionCounts[$key] ?? 0 }}</td>
        <td>{{ $exam->question_limit ?? 'Not Set' }}</td>

        <td>
          <span class="badge {{ $exam->is_active ? 'badge-success' : 'badge-secondary' }}">
            {{ $exam->is_active ? 'Active' : 'Inactive' }}
          </span>
        </td>
        <td>
          <form action="{{ route('admin.exams.toggle', $exam->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm {{ $exam->is_active ? 'btn-danger' : 'btn-success' }}">
              {{ $exam->is_active ? 'Deactivate' : 'Activate' }}
            </button>
          </form>
        </td>
        
       <td>
  <div class="dropdown">
    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="actionDropdown{{ $exam->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Actions
    </button>
    <div class="dropdown-menu" aria-labelledby="actionDropdown{{ $exam->id }}">
      {{-- Edit --}}
      <a class="dropdown-item" href="{{ route('admin.exams.edit', $exam->id) }}">✏️ Edit</a>

      {{-- Delete Exam --}}
      <a href="#" class="dropdown-item text-danger" onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this exam and all its questions?')) document.getElementById('delete-exam-{{ $exam->id }}').submit();">🗑️ Delete Exam</a>
      <form id="delete-exam-{{ $exam->id }}" action="{{ route('admin.exams.destroy', $exam->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
      </form>

      {{-- Delete Questions --}}
      <a href="#" class="dropdown-item text-dark" onclick="event.preventDefault(); if(confirm('Delete all questions for this exam?')) document.getElementById('delete-questions-{{ $exam->id }}').submit();">🧹 Delete Questions</a>
      <form id="delete-questions-{{ $exam->id }}" action="{{ route('admin.exams.deleteQuestions') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="exam_id" value="{{ $exam->id }}">
        <input type="hidden" name="course_id" value="{{ $exam->course_id }}">
      </form>
    </div>
  </div>
</td>


        <td>{{ $exam->created_at->format('d M Y') }}</td>
      </tr>
    @endforeach
  </tbody>
</table>

  </div>
@endif

      </div>
    </div>
  </div>
</section>


  @include('partials.footer')
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>



  <!-- Select2 JS -->
  <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>

  <script>
    $(function () {
      $('.select2').select2();
    });
  </script>
</div>
@endsection
