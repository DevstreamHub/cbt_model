@extends('layouts.app')

@section('title', 'Clear Student Exam Session')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Clear Student Exam Session</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Exam Session</li>
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
          <h3 class="card-title">Select Student to Clear Session</h3>
        </div>
        <div class="card-body">
          <form action="{{ route('admin.clear.session') }}" method="POST">
            @csrf
            <div class="form-group">
              <label for="matric_number">Matric Number</label>
              <select name="matric_number" class="form-control" required>
                <option value="">-- Select Matric Number --</option>
                @foreach($matricNumbers as $matric)
                  <option value="{{ $matric }}">{{ $matric }}</option>
                @endforeach
              </select>
            </div>

            <button class="btn btn-danger mt-3" type="submit">Clear Session</button>
          </form>
          <br>
<form id="clearAllSessionsForm" action="{{ route('admin.clear.all.sessions') }}" method="POST">
    @csrf
    <button class="btn btn-danger" type="button" onclick="confirmClearAll()">🧹 Clear All Sessions</button>
</form>

        </div>
      </div>
</div>

    </div>
  </section>

  @include('partials.footer')
</div>
<script>
    function confirmClearAll() {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will clear ALL active student exam sessions!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, clear all!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('clearAllSessionsForm').submit();
            }
        });
    }
</script>
<!-- SweetAlert2 CDN -->
<script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>

<!-- Optional scripts for consistency -->
<script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.4.5.2.js') }}"></script>
@endsection
