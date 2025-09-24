@extends('layouts.app')

@section('title', 'Ongoing Exams Monitoring')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Ongoing Exams</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Ongoing Exams</li>
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
          <h3 class="card-title">List of Ongoing Exams</h3>
        </div>
        <div class="card-body table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Course</th>
                <th>Type</th>
                <th>Duration (mins)</th>
                <th>Students Active</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($exams as $exam)
                <tr>
                  <td>{{ $exam->course->name }}</td>
                  <td>{{ ucfirst($exam->type) }}</td>
                  <td>{{ $exam->duration }}</td>
                  <td>{{ $exam->active_students_count }}</td>
                  <td>
                    <a href="{{ route('exams.monitor', $exam->id) }}" 
                       class="btn btn-primary btn-sm">
                       Monitor
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center">No ongoing exams</td>
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

<!-- SweetAlert2 -->
<script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.4.5.2.js') }}"></script>
@endsection
