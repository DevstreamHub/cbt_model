@extends('layouts.app')

@section('title', 'Exam Card Generator')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Exam Card Generator</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Exam Card Generator</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">

      <!-- Filter Students Form -->
      <div class="card mb-4">
        <div class="card-header">
          <h3 class="card-title">Filter Students</h3>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.exam_cards.filter') }}">
            @csrf
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="level_id">Level</label>
                  <select name="level_id" id="level_id" class="form-control" required>
                    <option value="">Select Level</option>
                    @foreach($levels as $level)
                      <option value="{{ $level->id }}" {{ (old('level_id', $selected['level_id'] ?? '') == $level->id) ? 'selected' : '' }}>{{ $level->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="department_id">Department</label>
                  <select name="department_id" id="department_id" class="form-control" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $department)
                      <option value="{{ $department->id }}" {{ (old('department_id', $selected['department_id'] ?? '') == $department->id) ? 'selected' : '' }}>{{ $department->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="semester_id">Semester</label>
                  <select name="semester_id" id="semester_id" class="form-control" required>
                    <option value="">Select Semester</option>
                    @foreach($semesters as $semester)
                      <option value="{{ $semester->id }}" {{ (old('semester_id', $selected['semester_id'] ?? '') == $semester->id) ? 'selected' : '' }}>{{ $semester->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <button type="submit" class="btn btn-primary">Filter Students</button>
          </form>
        </div>
      </div>
      <!-- Filtered Students Table -->
      @if(isset($students))

      <!-- Print All Exam Cards Button -->
      @if(count($students))
      <div class="mb-3">
          <form action="{{ route('admin.exam-cards.printAll') }}" method="POST" target="_blank">
              @csrf
              <input type="hidden" name="level_id" value="{{ $selected['level_id'] }}">
              <input type="hidden" name="department_id" value="{{ $selected['department_id'] }}">
              <input type="hidden" name="semester_id" value="{{ $selected['semester_id'] }}">

              <button type="submit" class="btn btn-outline-primary">
                  <i class="fas fa-print"></i> Print All Exam Cards
              </button>
          </form>
      </div>
      @endif

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Filtered Students</h3>
        </div>
        <div class="card-body p-0">
          <table class="table table-bordered table-striped mb-0">
            <thead>
              <tr>
                <th>Matric No</th>
                <th>Name</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($students as $student)
              <tr>
                <td>{{ $student->matric_no }}</td>
                <td>{{ $student->surname }} {{ $student->other_names }}</td>
                <td>
                  <a href="{{ route('admin.exam_cards.download', ['student' => $student->id, 'semester_id' => $selected['semester_id']]) }}"
                     class="btn btn-sm btn-success" target="_blank">Download Card</a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="3" class="text-center">No students found.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @endif

    </div>
  </section>

  @include('partials.footer')
</div>
@endsection
