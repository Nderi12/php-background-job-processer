@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Background Jobs</h1>

    <!-- Table to display jobs -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Job Class</th>
                <th>Parameters</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jobs as $job)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ class_basename($job->class) }}</td>
                    <td>{{ $job->parameters }}</td>
                    <td>
                        <!-- Status Badge -->
                        @if($job->status == 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif($job->status == 'complete')
                            <span class="badge bg-success">Complete</span>
                        @elseif($job->status == 'failed')
                            <span class="badge bg-danger">Failed</span>
                        @else
                            <span class="badge bg-secondary">Unknown</span>
                        @endif
                    </td>
                    <td>{{ $job->priority }}</td>
                    <td>{{ $job->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
