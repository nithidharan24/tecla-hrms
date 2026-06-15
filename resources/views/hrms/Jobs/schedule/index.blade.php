@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Schedule timing');
@endphp@extends('layouts.index')

@section('content')

<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Scheduled Timing</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Schedule</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
@if(isset($permissions) && $permissions->can_create)
                <a href="{{ route('schedule.create') }}" class="btn add-btn">
                     Schedule Timing
                </a>
@endif
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped datatable mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Candidate Name</th>
                            <th>Date 1</th>
                            <th>Time 1</th>
                            <th>Date 2</th>
                            <th>Time 2</th>
                            <th>Date 3</th>
                            <th>Time 3</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $schedule) <!-- Make sure to pass $schedules from your controller -->
                        <tr>
                            <td>{{ $schedule->id }}</td>
                            <td>
                                {{ optional($candidates->firstWhere('id', $schedule->name))->first_name . ' ' . optional($candidates->firstWhere('id', $schedule->name))->last_name }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($schedule->selectdate1)->format('d-m-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($schedule->selecttime1)->format('H:i') }}</td>
                            <td>{{ $schedule->selectdate2 ? \Carbon\Carbon::parse($schedule->selectdate2)->format('d-m-Y') : 'N/A' }}</td>
                            <td>{{ $schedule->selecttime2 ? \Carbon\Carbon::parse($schedule->selecttime2)->format('H:i') : 'N/A' }}</td>
                            <td>{{ $schedule->selectdate3 ? \Carbon\Carbon::parse($schedule->selectdate3)->format('d-m-Y') : 'N/A' }}</td>
                            <td>{{ $schedule->selecttime3 ? \Carbon\Carbon::parse($schedule->selecttime3)->format('H:i') : 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Automatically hide success and error messages after 1.5 seconds
    setTimeout(function() {
        let successMessage = document.getElementById('success-message');
        if (successMessage) {
            successMessage.style.display = 'none';
        }
        let errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            errorMessage.style.display = 'none';
        }
    }, 1500);
</script>

@endsection
