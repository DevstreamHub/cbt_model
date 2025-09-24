@extends('layouts.app')

@section('title', 'Manage Background Images')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Manage Background Images</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Background Images</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">

      <!-- Success Message -->
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <!-- Upload Form -->
      <div class="card mb-4">
        <div class="card-header">
          <h3 class="card-title">Upload New Background</h3>
        </div>
        <div class="card-body">
          <form action="{{ route('admin.backgrounds.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
              <label for="name">Name (optional)</label>
              <input type="text" name="name" id="name" class="form-control">
            </div>
            <div class="form-group">
              <label for="background">Upload Background Image</label>
              <input type="file" name="background" id="background" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
          </form>
        </div>
      </div>

      <!-- Display Existing Backgrounds -->
      <div class="row">
        @forelse($images as $image)
          <div class="col-md-3 mb-4">
            <div class="card h-100">
              <img src="{{ asset('storage/' . $image->file_path) }}" class="card-img-top" height="150" style="object-fit: cover;">
              <div class="card-body text-center">
                <strong>{{ $image->name ?? 'No name' }}</strong>
                <p class="text-{{ $image->is_active ? 'success' : 'muted' }}">
                  {{ $image->is_active ? 'Active' : 'Inactive' }}
                </p>
                <div class="d-flex justify-content-center gap-2">
                  @if(!$image->is_active)
                    <a href="{{ route('admin.backgrounds.activate', $image->id) }}" class="btn btn-sm btn-success mr-1">Activate</a>
                  @endif
                  <form action="{{ route('admin.backgrounds.destroy', $image->id) }}" method="POST" onsubmit="return confirm('Delete this background?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12">
            <p class="text-center">No background images uploaded yet.</p>
          </div>
        @endforelse
      </div>

    </div>
  </section>

  @include('partials.footer')
</div>
@endsection
