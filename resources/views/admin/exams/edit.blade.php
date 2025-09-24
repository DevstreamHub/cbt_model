@extends('layouts.app')

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
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.exams.create') }}">Create CBT</a></li>
            <li class="breadcrumb-item active">Edit CBT</li>
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
          <h3 class="card-title">Edit CBT (Test/Exam)</h3>
        </div>

        <div class="card-body">
          @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
          @endif

          <form action="{{ route('admin.exams.update', $exam->id) }}" method="POST">
            @csrf

            <div class="form-group">
              <label>Course</label>
              <input type="text" class="form-control" value="{{ $exam->course->name }}" disabled>
            </div>

            <div class="form-group">
              <label for="type">CBT Type</label>
              <select name="type" id="type" class="form-control" required>
                <option value="test" {{ $exam->type == 'test' ? 'selected' : '' }}>Test</option>
                <option value="exam" {{ $exam->type == 'exam' ? 'selected' : '' }}>Exam</option>
              </select>
            </div>

            <div class="form-group">
              <label for="duration">Duration (minutes)</label>
              <input type="number" name="duration" id="duration" class="form-control" value="{{ $exam->duration }}" required>
            </div>
            
            <div class="form-group">
                <label for="question_limit">Number of Questions per Student</label>
                <input type="number" name="question_limit" class="form-control" value="{{ old('question_limit', $exam->question_limit ?? '') }}" min="1" placeholder="e.g. 20">
            </div>

            <div class="mt-3">
              <button type="submit" class="btn btn-primary">Update CBT</button>
              <a href="{{ route('admin.exams.create') }}" class="btn btn-secondary">Back</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

  @include('partials.footer')

  <!-- jQuery -->
  <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>

  <!-- Bootstrap JS -->
  <script src="{{ asset('js/bootstrap.bundle.min.4.5.2.js') }}"></script>
</div>
@endsection
