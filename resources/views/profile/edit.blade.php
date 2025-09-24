@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Edit Profile</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Profile</li>
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

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Manage Your Profile</h3>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <div class="form-group">
              <label for="name">Name</label>
              <input id="name" type="text" name="name" class="form-control"
                     value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <input id="email" type="email" name="email" class="form-control"
                     value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="form-group">
              <label for="password">New Password <small>(leave blank if unchanged)</small></label>
              <input id="password" type="password" name="password" class="form-control">
            </div>

            <div class="form-group">
              <label for="password_confirmation">Confirm Password</label>
              <input id="password_confirmation" type="password" name="password_confirmation" class="form-control">
            </div>

            <button class="btn btn-primary" type="submit">Update Profile</button>
          </form>
        </div>
      </div>

    </div>
  </section>

  @include('partials.footer')
</div>

<!-- Optional scripts for future UI needs -->
<script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.4.5.2.js') }}"></script>
@endsection
