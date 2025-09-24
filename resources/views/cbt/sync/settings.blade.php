@extends('layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">CBT Sync Settings</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">CBT Sync Settings</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">

      <div class="card card-primary shadow">
        <div class="card-header">
          <h3 class="card-title">API Configuration</h3>
        </div>
        <div class="card-body">
          @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
          @endif

          <form method="POST" action="{{ route('cbt.sync.save') }}">
            @csrf
            <div class="form-group">
              <label for="api_key">API Key</label>
              <input type="text" name="api_key" class="form-control" value="{{ old('api_key', $setting->api_key ?? '') }}" required placeholder="Enter your API key">
            </div>

            <div class="form-group">
              <label for="base_url">Base URL</label>
              <input type="url" name="base_url" class="form-control" value="{{ old('base_url', $setting->base_url ?? '') }}" required placeholder="e.g. https://domain.com/api">
            </div>

            <button type="submit" class="btn btn-primary mt-2">💾 Save Settings</button>
          </form>

          @if($setting)
            <hr class="my-4">

            
          <div class="d-flex flex-wrap">
            <div class="mr-2 mb-2">
              <!-- Clear Tables Form -->
              <form id="clear-form" action="{{ route('cbt.clear.tables') }}" method="POST">
                @csrf
                <button type="button" class="btn btn-danger" onclick="confirmClear()" title="Remove all existing CBT student and registration data">
                  🗑️ Clear CBT Tables
                </button>
              </form>
            </div>

            <div class="mr-2 mb-2">
              <!-- Sync All Records Form -->
              <form id="sync-form" action="{{ route('cbt.sync.all') }}" method="GET">
                @csrf
                <button type="button" class="btn btn-success" onclick="confirmSync()" title="Sync fresh data from API">
                  🔄 Sync All Records
                </button>
              </form>
            </div>

            <div class="mr-2 mb-2">
              <!-- Download Passport -->
              <form id="download-form" method="GET" action="{{ route('admin.download.images.run') }}">
                @csrf
                <button type="submit" id="downloadBtn" class="btn btn-success">
                  📥 Download All Images
                </button>
              </form>
            </div>
          </div>

          @endif

        </div>
      </div>

    </div>
  </section>
</div>

<!-- SweetAlert2 -->
<script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
<!-- Include jQuery and SweetAlert2 -->
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $('#download-form').on('submit', function(e) {
        e.preventDefault();

        const btn = $('#downloadBtn');
        btn.prop('disabled', true).text('⏳ Downloading...');

        $.ajax({
            url: $(this).attr('action'),
            type: 'GET',
            success: function(response) {
                Swal.fire({
                    title: '✅ Success',
                    html: `
                        <strong>${response.message}</strong><br>
                        <hr>
                        📥 Downloaded: ${response.downloaded}<br>
                        ❌ Failed: ${response.failed.length}
                    `,
                    icon: 'success'
                });
            },
            error: function() {
                Swal.fire({
                    title: '❌ Error',
                    text: 'Something went wrong during the download.',
                    icon: 'error'
                });
            },
            complete: function() {
                btn.prop('disabled', false).text('📥 Download All Images');
            }
        });
    });
</script>

<script>
  function confirmClear() {
    Swal.fire({
      title: 'Are you sure?',
      text: "This will permanently delete old CBT data!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#aaa',
      confirmButtonText: 'Yes, clear it'
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('clear-form').submit();
      }
    });
  }

  function confirmSync() {
    Swal.fire({
      title: 'Sync All Records?',
      text: "This will pull new student and course data, and overwrite existing records.",
      icon: 'info',
      showCancelButton: true,
      confirmButtonColor: '#28a745',
      cancelButtonColor: '#aaa',
      confirmButtonText: 'Yes, sync now'
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('sync-form').submit();
      }
    });
  }
</script>

<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>

@include('partials.footer')
@endsection
