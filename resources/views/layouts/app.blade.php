@php
    use App\Models\Setting;
    $settings = Setting::first();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title>CBT | {{ $settings->platform_name ?? 'Platform Name' }}</title>
   @if($settings && $settings->logo)
    <link rel="icon" type="image/png" href="{{ asset('storage/' . $settings->logo) }}">
    @endif
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">

<!-- overlayScrollbars -->
<link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

<!-- Theme style -->
<link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">

<!-- Google Font: Source Sans Pro -->
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

<!-- Summernote -->
<link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.css') }}">

<!-- Bootstrap 4 -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

  <!-- Summernote CSS -->
  <link href="{{ asset('css/summernote.min.css') }}" rel="stylesheet">

</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Test Mode</a>
      </li>
    </ul>

  </nav>
  <!-- /.navbar -->
            {{-- Navigation --}}
            @include('partials.sidebar')
           

            {{-- Page Content --}}
            <main>
                @yield('content')
            </main>

        
    @yield('scripts')
</body>
</html>


