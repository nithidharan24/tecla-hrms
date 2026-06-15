@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Shift & Schedule');
@endphp
@extends('layouts.index')
@section('content')
@php
use Carbon\Carbon;
@endphp

<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header bg-white border-bottom rounded-lg mb-4 shadow-sm">
        <div class="row align-items-center p-4">
            <div class="col">
                <h3 class="page-title d-flex align-items-center mb-2 fw-bold text-dark">
                    <i class="fas fa-calendar-alt me-2 text-primary"></i>
                    Employee Scheduling Dashboard
                </h3>
                <ul class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/" class="text-muted hover-text-primary">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-muted hover-text-primary">Employees</a></li>
                    <li class="breadcrumb-item active text-dark">Shift Scheduling</li>
                </ul>
            </div>
            <div class="col-auto">
                <div class="btn-group" role="group">
                    @if(isset($permissions) && $permissions->can_create)
                    <a href="{{ route('shift.index') }}" class="btn btn-outline-primary btn-sm rounded shadow-sm">
                        <i class="fas fa-cogs me-1"></i>Manage Shifts
                    </a>
                    @endif
                    @if(isset($permissions) && $permissions->can_create)
                    <a href="{{ route('scheduling.create') }}" class="btn btn-primary btn-sm rounded shadow-sm ms-2">
                        <i class="fas fa-plus me-1"></i>Add Schedule
                    </a>    
                    @endif
                  
                    <a href="{{ route('scheduling.shift-interchange.create') }}" class="btn btn-warning btn-sm rounded shadow-sm ms-2">
                        <i class="fas fa-exchange-alt me-1"></i> Interchange
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Alert Messages -->
    @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" id="success-alert">
            <i class="fas fa-check-circle me-2"></i>{{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @elseif(Session::has('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" id="error-alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ Session::get('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @elseif(Session::has('warning'))
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm" id="warning-alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ Session::get('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
    // Filter schedules by status - with proper handling for weekly repeating schedules
    $currentSchedules = $schedules->filter(function($schedule) use ($today) {
        $startDate = Carbon::parse($schedule->schedule_start_date);
        
        // For weekly repeating schedules, only check if start date has passed
        if ($schedule->repeat_every_week) {
            return $startDate->lte($today);
        }
        
        // For regular schedules, check if current date is between start and end dates
        $endDate = Carbon::parse($schedule->schedule_end_date);
        return $startDate->lte($today) && $endDate->gte($today);
    });
    
    $upcomingSchedules = $schedules->filter(function($schedule) use ($today) {
        $startDate = Carbon::parse($schedule->schedule_start_date);
        return $startDate->gt($today);
    });
@endphp

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 col-12 mb-3">
            <div class="card bg-white border-0 shadow-sm hover-card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon bg-primary text-white rounded">
                            <i class="fas fa-play-circle"></i>
                        </span>
                        <div class="dash-count">
                            <h3 class="counter text-dark">{{ $currentSchedules->count() }}</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info mt-2">
                        <h6 class="text-muted mb-0">Current Schedules</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 mb-3">
            <div class="card bg-white border-0 shadow-sm hover-card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon bg-info text-white rounded">
                            <i class="fas fa-clock"></i>
                        </span>
                        <div class="dash-count">
                            <h3 class="counter text-dark">{{ $upcomingSchedules->count() }}</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info mt-2">
                        <h6 class="text-muted mb-0">Upcoming Schedules</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 mb-3">
            <div class="card bg-white border-0 shadow-sm hover-card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon bg-warning text-white rounded">
                            <i class="fas fa-exclamation-triangle"></i>
                        </span>
                        <div class="dash-count">
                            <h3 class="counter text-dark">{{ $endingSoonSchedules->count() }}</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info mt-2">
                        <h6 class="text-muted mb-0">Ending Soon</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 mb-3">
            <div class="card bg-white border-0 shadow-sm hover-card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon bg-secondary text-white rounded">
                            <i class="fas fa-list"></i>
                        </span>
                        <div class="dash-count">
                            <h3 class="counter text-dark">{{ $schedules->count() }}</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info mt-2">
                        <h6 class="text-muted mb-0">Total Schedules</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 text-dark"><i class="fas fa-bolt text-primary me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 mb-2">
                            <button class="btn btn-outline-dark btn-sm w-100 rounded filter-btn active" data-filter="all">
                                <i class="fas fa-list me-1"></i>All Schedules
                            </button>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button class="btn btn-outline-primary btn-sm w-100 rounded filter-btn" data-filter="current">
                                <i class="fas fa-play me-1"></i>Current
                            </button>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button class="btn btn-outline-info btn-sm w-100 rounded filter-btn" data-filter="upcoming">
                                <i class="fas fa-clock me-1"></i>Upcoming
                            </button>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button class="btn btn-outline-warning btn-sm w-100 rounded filter-btn" data-filter="ending_soon">
                                <i class="fas fa-exclamation-triangle me-1"></i>Ending Soon
                            </button>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button class="btn btn-outline-secondary btn-sm w-100 rounded filter-btn" data-filter="need_update">
                                <i class="fas fa-sync-alt me-1"></i>Need Update
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Schedules Section -->
    @if($upcomingSchedules->count() > 0)
    <div class="row mb-4 schedule-section" id="upcoming-section">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-dark">
                        <i class="fas fa-clock me-2 text-info"></i>
                        Upcoming Schedules
                        <span class="badge bg-info text-white ms-2">{{ $upcomingSchedules->count() }}</span>
                    </h4>
                    <button class="btn btn-sm btn-outline-secondary rounded" type="button" data-bs-toggle="collapse" data-bs-target="#upcomingCollapse">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse show" id="upcomingCollapse">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Employee</th>
                                        <th>Department</th>
                                        <th>Shift Details</th>
                                        <th>Schedule Period</th>
                                        <th class="text-center">Starts In</th>
                                        <th class="text-center">Status</th>
                                       @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingSchedules->sortBy('schedule_start_date') as $index => $schedule)
                                        @php
                                            $startDate = Carbon::parse($schedule->schedule_start_date);
                                            $daysUntilStart = $today->diffInDays($startDate, false);
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-2">
                                                        @if($schedule->profile_image)
                                                            <img src="{{ asset($schedule->profile_image) }}" alt="{{ $schedule->firstname }}" class="avatar-img rounded-circle">
                                                        @else
                                                            <span class="avatar-title rounded-circle bg-primary text-white">
                                                                {{ substr($schedule->firstname, 0, 1) }}{{ substr($schedule->lastname, 0, 1) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 text-dark">{{ $schedule->firstname }} {{ $schedule->lastname }}</h6>
                                                        <small class="text-muted">{{ $schedule->designation ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary text-white rounded">{{ $schedule->department }}</span>
                                            </td>
                                            <td>
                                                <strong class="text-dark">{{ $schedule->shift_name }}</strong><br>
                                                <small class="text-muted">
                                                    {{ Carbon::parse($schedule->start_time)->format('h:i A') }} -
                                                     {{ Carbon::parse($schedule->end_time)->format('h:i A') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong class="text-dark">{{ $schedule->formatted_start_date }}</strong> to<br>
                                                    <strong class="text-dark">{{ $schedule->formatted_end_date }}</strong>
                                                </div>
                                                <small class="text-muted">{{ $schedule->schedule_duration }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info text-white rounded">
                                                    {{ $daysUntilStart }} day(s)
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($schedule->publish)
                                                    <span class="badge bg-success text-white rounded">Published</span>
                                                @else
                                                    <span class="badge bg-secondary text-white rounded">Draft</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('scheduling.edit', $schedule->id) }}" class="btn btn-sm btn-primary rounded action-btn" title="Edit Schedule">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
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
    @endif

    <!-- Schedules Ending Soon Section -->
    @if($endingSoonSchedules->count() > 0)
    <div class="row mb-4 schedule-section" id="ending-soon-section">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-dark">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                        Current Schedules Ending Within 1 Week
                        <span class="badge bg-warning text-dark ms-2">{{ $endingSoonSchedules->count() }}</span>
                    </h4>
                    <button class="btn btn-sm btn-outline-secondary rounded" type="button" data-bs-toggle="collapse" data-bs-target="#endingSoonCollapse">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse show" id="endingSoonCollapse">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Employee</th>
                                        <th>Department</th>
                                        <th>Shift Details</th>
                                        <th>Schedule Period</th>
                                        <th class="text-center">Days Left</th>
                                        <th class="text-center">Status</th>
                                       @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($endingSoonSchedules as $index => $schedule)
                                        <tr class="@if($schedule->days_remaining <= 3) table-danger @elseif($schedule->days_remaining <= 7) table-warning @endif">
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-2">
                                                        @if($schedule->profile_image)
                                                            <img src="{{ asset($schedule->profile_image) }}" alt="{{ $schedule->firstname }}" class="avatar-img rounded-circle">
                                                        @else
                                                            <span class="avatar-title rounded-circle bg-primary text-white">
                                                                {{ substr($schedule->firstname, 0, 1) }}{{ substr($schedule->lastname, 0, 1) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 text-dark">{{ $schedule->firstname }} {{ $schedule->lastname }}</h6>
                                                        <small class="text-muted">{{ $schedule->designation ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary text-white rounded">{{ $schedule->department }}</span>
                                            </td>
                                            <td>
                                                <strong class="text-dark">{{ $schedule->shift_name }}</strong><br>
                                                <small class="text-muted">
                                                    {{ Carbon::parse($schedule->start_time)->format('h:i A') }} -
                                                     {{ Carbon::parse($schedule->end_time)->format('h:i A') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong class="text-dark">{{ $schedule->formatted_start_date }}</strong> to<br>
                                                    <strong class="text-dark">{{ $schedule->formatted_end_date }}</strong>
                                                </div>
                                                <small class="text-muted">{{ $schedule->schedule_duration }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge @if($schedule->days_remaining <= 3) bg-danger @else bg-warning text-dark @endif rounded">
                                                    {{ $schedule->days_remaining }} day(s)
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($schedule->publish)
                                                    <span class="badge bg-success text-white rounded">Published</span>
                                                @else
                                                    <span class="badge bg-secondary text-white rounded">Draft</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('scheduling.edit', $schedule->id) }}" class="btn btn-sm btn-primary rounded action-btn" title="Edit Schedule">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
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
    @endif

    <!-- Schedules Needing Update Section -->
    @if($schedulesNeedingUpdate->count() > 0)
    <div class="row mb-4 schedule-section" id="need-update-section">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-dark">
                        <i class="fas fa-bell me-2 text-info"></i>
                        Current Schedules Needing Update
                        <span class="badge bg-info text-white ms-2">{{ $schedulesNeedingUpdate->count() }}</span>
                    </h4>
                    <button class="btn btn-sm btn-outline-secondary rounded" type="button" data-bs-toggle="collapse" data-bs-target="#needUpdateCollapse">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse show" id="needUpdateCollapse">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Employee</th>
                                        <th>Department</th>
                                        <th>Current Shift</th>
                                        <th>Current Period</th>
                                        <th class="text-center">Next Cycle In</th>
                                       @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedulesNeedingUpdate as $index => $schedule)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-2">
                                                        @if($schedule->profile_image)
                                                            <img src="{{ asset($schedule->profile_image) }}" alt="{{ $schedule->firstname }}" class="avatar-img rounded-circle">
                                                        @else
                                                            <span class="avatar-title rounded-circle bg-info text-white">
                                                                {{ substr($schedule->firstname, 0, 1) }}{{ substr($schedule->lastname, 0, 1) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 text-dark">{{ $schedule->firstname }} {{ $schedule->lastname }}</h6>
                                                        <small class="text-muted">{{ $schedule->designation ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary text-white rounded">{{ $schedule->department }}</span>
                                            </td>
                                            <td>
                                                <strong class="text-dark">{{ $schedule->shift_name }}</strong><br>
                                                <small class="text-muted">
                                                    {{ Carbon::parse($schedule->start_time)->format('g:i A') }} -
                                                     {{ Carbon::parse($schedule->end_time)->format('g:i A') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $schedule->formatted_start_date }} to<br>
                                                    {{ $schedule->formatted_end_date }}
                                                </div>
                                                <small class="text-muted">{{ $schedule->schedule_duration }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info text-white rounded">
                                                    {{ $today->diffInDays(Carbon::parse($schedule->schedule_end_date)) + 1 }} day(s)
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('scheduling.edit', $schedule->id) }}" class="btn btn-sm btn-warning rounded action-btn-lg">
                                                    <i class="fas fa-sync-alt me-1"></i>Update Schedule
                                                </a>
                                            </td>
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
    @endif
<!-- Weekly Schedule View - Current Schedules Only -->
@php
    $daysOfWeek = [];
    for ($i = 0; $i < 7; $i++) {
        $day = $today->copy()->addDays($i);
        $daysOfWeek[] = [
            'display' => $day->format('D') . $day->format('d'),
            'date' => $day->format('Y-m-d'),
            'day_name' => $day->format('D'),
            'full_date' => $day->format('l, M d'),
            'carbon' => $day
        ];
    }
@endphp

<div class="row schedule-section" id="weekly-schedule-section">
    <div class="col-md-12">
        <div class="card border-0 shadow">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h4 class="mb-0 text-dark">
                    <i class="fas fa-calendar-week me-2 text-primary"></i>
                    Current Weekly Schedule
                    <small class="ms-2 text-muted">{{ $today->format('d M') }} - {{ $today->copy()->addDays(6)->format('d M, Y') }}</small>
                </h4>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-secondary rounded" onclick="toggleScheduleView('compact')">
                        <i class="fas fa-compress-alt"></i> Compact
                    </button>
                    <button class="btn btn-sm btn-outline-secondary rounded ms-1" onclick="toggleScheduleView('detailed')">
                        <i class="fas fa-expand-alt"></i> Detailed
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0" id="schedule-table">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 200px;">Employee</th>
                                @foreach ($daysOfWeek as $day)
                                    <th class="text-center" style="min-width: 150px;">
                                        <div class="text-dark">{{ $day['display'] }}</div>
                                        <small class="text-muted">{{ $day['full_date'] }}</small>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody id="schedule-tbody">
                            @forelse ($currentSchedules as $i => $schedule)
                            <tr class="schedule-row" 
                                data-schedule-type="current"
                                data-ending-soon="{{ $schedule->ending_soon ?? false ? 'true' : 'false' }}"
                                data-need-update="{{ $schedule->needs_update ?? false ? 'true' : 'false' }}"
                                data-published="{{ $schedule->publish ? 'true' : 'false' }}"
                                data-repeat-weekly="{{ $schedule->repeat_every_week ? 'true' : 'false' }}">
                                <td class="align-middle">
                                    <div class="d-flex align-items-center p-2">
                                        <div class="avatar avatar-sm me-3">
                                            @if($schedule->profile_image)
                                                <img src="{{ asset($schedule->profile_image) }}" alt="{{ $schedule->firstname }}" class="avatar-img rounded-circle">
                                            @else
                                                <span class="avatar-title rounded-circle bg-primary text-white">
                                                    {{ substr($schedule->firstname, 0, 1) }}{{ substr($schedule->lastname, 0, 1) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-1 text-dark">{{ $schedule->firstname }} {{ $schedule->lastname }}</h6>
                                            <div class="small text-muted">
                                                <i class="fas fa-building me-1"></i>{{ $schedule->department ?? 'N/A' }}
                                            </div>
                                            <div class="small">
                                                <span class="badge bg-primary text-white rounded">Current</span>
                                                @if($schedule->repeat_every_week)
                                                    <span class="badge bg-success text-white rounded">Repeats Weekly</span>
                                                @endif
                                                @if($schedule->ending_soon ?? false)
                                                    <span class="badge bg-warning text-dark rounded">Ending Soon</span>
                                                @endif
                                                @if($schedule->needs_update ?? false)
                                                    <span class="badge bg-info text-white rounded">Needs Update</span>
                                                @endif
                                                @if(!$schedule->publish)
                                                    <span class="badge bg-secondary text-white rounded">Draft</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @foreach ($daysOfWeek as $day)
                                    <td class="text-center align-middle p-2">
                                        @php
                                            $dayName = $day['day_name'];
                                            $shiftDays = explode(',', $schedule->days_of_week ?? '');
                                            $shouldDisplayShift = in_array(strtolower($dayName), array_map('strtolower', $shiftDays));
                                            
                                            // Check if schedule is active for this date
                                            $scheduleStart = Carbon::parse($schedule->schedule_start_date);
                                            $scheduleEnd = Carbon::parse($schedule->schedule_end_date);
                                            $currentDate = $day['carbon'];
                                            
                                            // For weekly repeating schedules, ignore end date and just check if current date is after start date
                                            if ($schedule->repeat_every_week) {
                                                $isInSchedulePeriod = $currentDate->gte($scheduleStart);
                                            } else {
                                                $isInSchedulePeriod = $currentDate->between($scheduleStart, $scheduleEnd);
                                            }
                                            
                                            // Additional check for repeat_every_week > 1
                                            if ($schedule->repeat_every_week > 1 && $shouldDisplayShift && $isInSchedulePeriod) {
                                                $weeksSinceStart = $scheduleStart->diffInWeeks($currentDate);
                                                $shouldDisplayShift = ($weeksSinceStart % $schedule->repeat_every_week == 0);
                                            }
                                        @endphp
                                        @if($shouldDisplayShift && $isInSchedulePeriod)
                                            <div class="schedule-cell bg-light border rounded p-2 position-relative">
                                                <div class="schedule-content">
                                                    <div class="fw-bold text-primary mb-1">
                                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }} -
                                                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
                                                    </div>
                                                    
                                                    <div class="small text-muted mb-1">
                                                        {{ $schedule->shift_name }}
                                                    </div>
                                                    <div class="small text-primary">
                                                        {{ $schedule->duration }}
                                                    </div>
                                                    @if($schedule->accept_extra_hours)
                                                        <div class="small">
                                                            <span class="badge bg-info text-white rounded">Extra Hours OK</span>
                                                        </div>
                                                    @endif
                                                    @if($schedule->repeat_every_week)
                                                        <div class="small">
                                                            <span class="badge bg-success text-white rounded">Weekly</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="schedule-cell-empty text-muted p-3">
                                                <i class="fas fa-minus opacity-50"></i>
                                                @if(!$isInSchedulePeriod && !$schedule->repeat_every_week)
                                                    <div class="small mt-1">Outside schedule period</div>
                                                @elseif(!$shouldDisplayShift)
                                                    <div class="small mt-1">No shift scheduled</div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ count($daysOfWeek) + 1 }}" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-calendar-times fa-3x mb-3 text-secondary"></i>
                                        <h5 class="text-dark">No Current Schedules Found</h5>
                                        <p>There are no current active schedules to display.</p>
                                        <a href="{{ route('scheduling.create') }}" class="btn btn-primary rounded mt-2">
                                            <i class="fas fa-plus me-1"></i>Create New Schedule
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Schedule Details Modal -->
<div class="modal fade" id="scheduleDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Schedule Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="scheduleDetailsContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading schedule details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Card hover effects */
.hover-card {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.hover-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
}

/* Schedule cell styling */
.schedule-cell {
    min-height: 80px;
    transition: all 0.3s ease;
    cursor: pointer;
    border-radius: 6px !important;
}

.schedule-cell:hover {
    transform: translateY(-2px);
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.schedule-cell-empty {
    min-height: 80px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: all 0.3s ease;
}

/* Action buttons */
.action-btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.action-btn:hover {
    transform: scale(1.05);
}

.action-btn-lg {
    transition: all 0.3s ease;
}

.action-btn-lg:hover {
    transform: translateY(-1px);
}

/* Avatar styling */
.avatar {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.avatar-sm {
    width: 32px;
    height: 32px;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
}

/* Dashboard widgets */
.dash-widget-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.dash-widget-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.dash-count h3 {
    font-size: 2rem;
    font-weight: bold;
    margin: 0;
}

/* Hover text effect */
.hover-text-primary:hover {
    color: #0d6efd !important;
}

/* Counter animation */
.counter {
    display: inline-block;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .schedule-cell {
        min-height: 60px;
        font-size: 0.8rem;
    }
    
    .dash-count h3 {
        font-size: 1.5rem;
    }
    
    .dash-widget-icon {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
    }
}

/* Badge styling */
.badge {
    font-size: 0.75em;
    font-weight: 500;
}

/* Table row hover effect */
.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
}

/* Button focus states */
.btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}
</style>

<script>
$(document).ready(function() {
    // Auto-hide alerts
    setTimeout(function() {
        $(".alert").fadeOut(500, function() {
            $(this).remove();
        });
    }, 5000);
    
    // Initialize tooltips
    $('[title]').tooltip();
    
    // Counter animation
    $('.counter').each(function() {
        var $this = $(this);
        var countTo = parseInt($this.text());
        
        $({ countNum: 0 }).animate({
            countNum: countTo
        }, {
            duration: 1000,
            easing: 'swing',
            step: function() {
                $this.text(Math.floor(this.countNum));
            },
            complete: function() {
                $this.text(this.countNum);
            }
        });
    });
    
    // Add active class to filter buttons
    $('.filter-btn').click(function() {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        // Call filter function
        filterSchedules($(this).data('filter'));
    });
});

// Filter schedules function
function filterSchedules(type) {
    const rows = document.querySelectorAll('.schedule-row');
    const sections = document.querySelectorAll('.schedule-section');
    
    // Hide all sections first
    sections.forEach(section => {
        if (section) {
            section.style.display = 'none';
        }
    });
    
    switch(type) {
        case 'current':
            // Show only weekly schedule section with current schedules
            document.getElementById('weekly-schedule-section').style.display = 'block';
            rows.forEach(row => {
                row.style.display = row.dataset.scheduleType === 'current' ? '' : 'none';
            });
            break;
            
        case 'upcoming':
            const upcomingSection = document.getElementById('upcoming-section');
            if (upcomingSection) {
                upcomingSection.style.display = 'block';
            }
            break;
            
        case 'ending_soon':
            rows.forEach(row => {
                row.style.display = row.dataset.endingSoon === 'true' ? '' : 'none';
            });
            const endingSoonSection = document.getElementById('ending-soon-section');
            if (endingSoonSection) {
                endingSoonSection.style.display = 'block';
            }
            document.getElementById('weekly-schedule-section').style.display = 'block';
            break;
            
        case 'need_update':
            rows.forEach(row => {
                row.style.display = row.dataset.needUpdate === 'true' ? '' : 'none';
            });
            const needUpdateSection = document.getElementById('need-update-section');
            if (needUpdateSection) {
                needUpdateSection.style.display = 'block';
            }
            document.getElementById('weekly-schedule-section').style.display = 'block';
            break;
            
        case 'all':
        default:
            rows.forEach(row => {
                row.style.display = '';
            });
            // Show all sections
            sections.forEach(section => {
                if (section) {
                    section.style.display = 'block';
                }
            });
            break;
    }
}

// Toggle schedule view
function toggleScheduleView(view) {
    const table = document.getElementById('schedule-table');
    const cells = document.querySelectorAll('.schedule-cell');
    
    if (view === 'compact') {
        table.classList.add('table-sm');
        cells.forEach(cell => {
            cell.style.minHeight = '50px';
            const content = cell.querySelector('.schedule-content');
            if (content) {
                content.style.fontSize = '0.75rem';
            }
        });
    } else {
        table.classList.remove('table-sm');
        cells.forEach(cell => {
            cell.style.minHeight = '80px';
            const content = cell.querySelector('.schedule-content');
            if (content) {
                content.style.fontSize = '1rem';
            }
        });
    }
}

// View schedule details
function viewScheduleDetails(scheduleId) {
    $('#scheduleDetailsModal').modal('show');
    
    // Simulate loading
    setTimeout(function() {
        $('#scheduleDetailsContent').html(`
            <div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Schedule details for ID: ${scheduleId}
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-dark">Employee Information</h5>
                        <p class="text-muted">This would show employee details from an AJAX call.</p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-dark">Schedule Information</h5>
                        <p class="text-muted">This would show schedule details from an AJAX call.</p>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <a href="/scheduling/${scheduleId}/edit" class="btn btn-primary rounded">
                        <i class="fas fa-edit me-1"></i>Edit Schedule
                    </a>
                </div>
            </div>
        `);
    }, 1000);
}

// Refresh page data
function refreshSchedules() {
    location.reload();
}

// Export schedule data
function exportSchedules() {
    window.open('/scheduling/export', '_blank');
}
</script>
@endsection