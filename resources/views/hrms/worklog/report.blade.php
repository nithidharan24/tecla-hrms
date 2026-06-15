@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Worklog Reports</span>
                    <a href="{{ route('worklogs.index') }}" class="btn btn-secondary btn-sm">Back to Worklogs</a>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    Current Month Summary
                                </div>
                                <div class="card-body">
                                    <h5>Total Hours: {{ floor($totalHours / 60) }}h {{ $totalHours % 60 }}m</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    Project Breakdown
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Project</th>
                                                <th>Hours</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($projectSummary as $project)
                                            <tr>
                                                <td>{{ $project->project }}</td>
                                                <td>{{ $project->formatted_time }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection