@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h2>Active Course Summary</h2>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Course Name</th>
            <th>Course Code</th>
            <th>Total Questions</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($courses as $course)
            <tr>
              <td>{{ $course->name }}</td>
              <td>{{ $course->code ?? 'N/A' }}</td>
              <td>{{ $course->questions->count() }}</td>
              <td>
                @if ($course->questions->count() > 0)
                  <span class="badge badge-success">Completed</span>
                @else
                  <span class="badge badge-warning">Pending</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4">No active courses found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
</div>
@endsection
