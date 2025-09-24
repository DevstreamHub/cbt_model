@extends('layouts.app')

@section('title', 'Upload Student Index')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Student Index Management</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Upload Index Numbers</li>
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
          <h3 class="card-title">Upload Matric & Index Numbers via Excel</h3>
        </div>

        <div class="card-body">
          <a href="{{ route('student-index.template') }}" class="btn btn-success mb-3">Download Excel Template</a>

          <form action="{{ route('student-index.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
              <label for="excel_file">Excel File (.xlsx or .xls)</label>
              <input type="file" name="excel_file" id="excel_file" class="form-control" required>
              <small class="text-muted">Ensure the file contains both Matric and Index columns.</small>
            </div>

            <button type="submit" class="btn btn-primary">Upload</button>
          </form>

          <form id="auto-register-form" action="{{ route('student-index.auto-register') }}" method="POST">
              @csrf
              <button type="button" class="btn btn-primary mt-3" id="confirm-register-btn">
                  📘 Auto Register Uploaded Students
              </button>
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

<!-- SweetAlert2 -->
<script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>

<script>
    $(document).ready(function () {
        // Feedback after action
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                html: `{!! session('success') !!}`
            });
        @elseif(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                html: `{!! session('warning') !!}`
            });
        @elseif(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: `{!! session('error') !!}`
            });
        @endif

        // Confirmation before auto-register
        $('#confirm-register-btn').click(function (e) {
            Swal.fire({
                title: 'Are you sure?',
                html: "This will auto-register all indexed students for <br><strong>Pre-National Exam Paper I, II, and III</strong>.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#auto-register-form').submit();
                }
            });
        });
    });
</script>
</div>
@endsection
