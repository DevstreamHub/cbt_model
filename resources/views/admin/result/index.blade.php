@extends('layouts.app')

@section('title', 'Generate & Export Results')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Generate & Export Student Results</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Generate Results</li>
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

      <div class="card mb-4">
        <div class="card-header">
          <h3 class="card-title">Generate Student Results Automatically</h3>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.result.copy') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Generate Results</button>
          </form>
        </div>
      </div>

      <!-- Table of courses with results -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Courses with Saved Results</h3>
        </div>
        <div class="card-body p-0">
          <table class="table table-bordered table-striped mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Course Code</th>
                <th>Course Title</th>
                <th>Export Type</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @php
                $courseIds = \App\Models\Result::pluck('course_registration_id')
                  ->map(function($regId) {
                      return \App\Models\CourseRegistration::find($regId)?->course_id;
                  })->filter()->unique();

                $courses = \App\Models\Course::whereIn('id', $courseIds)->get();
              @endphp

              @forelse($courses as $index => $course)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $course->course_code }}</td>
                <td>{{ $course->name }}</td>
                <td>
                  <form method="GET" action="{{ route('admin.result.export') }}" class="form-inline" id="exportForm-{{ $course->id }}">
                    <select name="type" class="form-control form-control-sm mr-2" required>
                      <option value="both">Both C.A and Exam</option>
                      <option value="ca">C.A Only</option>
                      <option value="exam">Exam Only</option>
                    </select>
                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                    <input type="hidden" name="session_id" value="{{ \App\Models\AcademicSession::where('is_active', true)->first()?->id }}">
                </td>
                <td>
                    <button type="submit" class="btn btn-sm btn-success">Export</button>
                  </form>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center">No results saved yet.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </section>

  @include('partials.footer')
</div>

<!-- Scripts -->
<script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.4.5.2.js') }}"></script>
@endsection
