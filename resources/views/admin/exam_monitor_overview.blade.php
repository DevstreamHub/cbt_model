@extends('layouts.app')

@section('title', 'Ongoing Exams Monitoring')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Ongoing Exams</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
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
                                    <td>{{ $exam->exam_progress_count }}</td>
                                    <td>
                                        <a href="{{ route('admin.exam.monitor', $exam->id) }}" 
                                           class="btn btn-primary btn-sm">
                                           View Progress
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
</div>
@endsection
