@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Employee Attendance');
@endphp
@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Employee Attendance</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Attendance</li>
                    </ol>
                </nav>
            </div>
            @if($currentSchedule)
                <div class="col-auto">
                    <div class="badge bg-primary">
                        {{ $currentSchedule->shift_name }} 
                        ({{ \Carbon\Carbon::parse($currentSchedule->start_time)->format('h:i A') }} - 
                         {{ \Carbon\Carbon::parse($currentSchedule->end_time)->format('h:i A') }})
                        <br><small>Break: {{ $currentSchedule->break_time }} minutes</small>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Restriction Alert -->
    @if($isRestrictedDay)
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Attendance Restricted!</strong> {{ $restrictionReason }} - Punch in/out is disabled today.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Permission Restriction Alert -->
    @if(isset($hasActivePermission) && $hasActivePermission['has_permission'])
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Permission Active!</strong> {{ $hasActivePermission['message'] }}
            <br>
            <small>
                <strong>Reason:</strong> {{ $hasActivePermission['reason'] }}
            </small>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Dashboard Cards -->
    <div class="row mb-4">
        <!-- Timesheet Card -->
        <div class="col-md-4 mb-4 mb-md-0">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="card-title mb-0">Today's Timesheet</h5>
                    <p class="text-muted small mb-0">{{ now()->format('l, F j, Y') }}</p>
                    @if($currentSchedule)
                        <p class="text-primary small mb-0">
                            <i class="fas fa-clock me-1"></i>
                            {{ $currentSchedule->shift_name }} - {{ $currentSchedule->department }}
                        </p>
                    @endif
                </div>
                <div class="card-body">
                    @if($isRestrictedDay)
                        <!-- Restricted Day Notice -->
                        <div class="text-center py-4">
                            <div class="restriction-notice bg-light rounded p-4 mb-4">
                                <i class="fas fa-ban fa-2x text-warning mb-3"></i>
                                <p class="mb-0 text-muted">{{ $restrictionReason }}</p>
                            </div>
                        </div>
                    @elseif(isset($hasActivePermission) && $hasActivePermission['has_permission'])
                        <!-- Permission Active Notice -->
                        <div class="text-center py-4">
                            <div class="permission-notice bg-light rounded p-4 mb-4">
                                <i class="fas fa-user-clock fa-2x text-info mb-3"></i>
                                <h6 class="text-info mb-2">Permission Active</h6>
                                <p class="mb-1">{{ $hasActivePermission['start_time'] }} - {{ $hasActivePermission['end_time'] }}</p>
                                <p class="mb-2 small text-muted">{{ $hasActivePermission['duration'] }} hours</p>
                                <p class="mb-0 small">{{ $hasActivePermission['reason'] }}</p>
                            </div>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Punch in/out disabled during permission hours
                            </div>
                        </div>
                    @elseif($todayAttendance->isNotEmpty() && is_null($todayAttendance->last()->punch_out))
                        <!-- Already Punched In -->
                        <div class="text-center mb-4">
                            <div class="punch-status bg-light rounded p-3 mb-3">
                                <h6 class="text-muted mb-1">Punched In At</h6>
                                <h4 class="text-success mb-0">
                                    {{ \Carbon\Carbon::parse($todayAttendance->last()->punch_in)->format('h:i A') }}
                                </h4>
                                @if($currentSchedule)
                                    <small class="text-muted">
                                        Scheduled: {{ \Carbon\Carbon::parse($currentSchedule->start_time)->format('h:i A') }}
                                    </small>
                                @endif
                            </div>

                            <!-- Break Status -->
                            @if($breakStatus['allocated_break_time'] > 0)
                                <div class="break-status mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small">Break Time</span>
                                        <span class="small fw-bold">
                                            {{ $breakStatus['total_break_taken'] }}/{{ $breakStatus['allocated_break_time'] }} min
                                        </span>
                                    </div>
                                    <div class="progress mb-2" style="height: 6px;">
                                        @php
                                            $breakPercentage = $breakStatus['allocated_break_time'] > 0 ? 
                                                min(100, ($breakStatus['total_break_taken'] / $breakStatus['allocated_break_time']) * 100) : 0;
                                        @endphp
                                        <div class="progress-bar bg-info" style="width: {{ $breakPercentage }}%"></div>
                                    </div>
                                    
                                    @if($breakStatus['is_on_break'])
                                        <div class="alert alert-info py-2 mb-2">
                                            <i class="fas fa-coffee me-1"></i>
                                            Currently on break since {{ \Carbon\Carbon::parse($breakStatus['current_break_start'])->format('h:i A') }}
                                        </div>
                                        <form action="{{ route('end-break') }}" method="POST" class="mb-2">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm w-100">
                                                <i class="fas fa-play me-1"></i> End Break
                                            </button>
                                        </form>
                                    @elseif($breakStatus['can_take_break'])
                                        <form action="{{ route('start-break') }}" method="POST" class="mb-2">
                                            @csrf
                                            <button type="submit" class="btn btn-info btn-sm w-100">
                                                <i class="fas fa-coffee me-1"></i> Start Break ({{ $breakStatus['remaining_break_time'] }} min left)
                                            </button>
                                        </form>
                                    @else
                                        <small class="text-muted">No break time remaining</small>
                                    @endif
                                </div>
                            @endif

                            <div class="current-session mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Current Session</span>
                                    <span class="fw-bold">{{ number_format($todayStats['total_hours'], 2) }} hrs</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    @php
                                        $progressPercentage = $todayStats['scheduled_hours'] > 0 ? 
                                            min(100, ($todayStats['total_hours'] / $todayStats['scheduled_hours']) * 100) : 0;
                                    @endphp
                                    <div class="progress-bar bg-primary" style="width: {{ $progressPercentage }}%"></div>
                                </div>
                                @if($todayStats['scheduled_hours'] > 0)
                                    <small class="text-muted">
                                        Target: {{ number_format($todayStats['scheduled_hours'], 2) }} hrs
                                    </small>
                                @endif
                                
                                <!-- Overtime Indicator -->
                                @if($todayStats['total_hours'] > 8)
                                    <div class="mt-2">
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>
                                            Overtime: {{ number_format($todayStats['total_hours'] - 8, 2) }} hrs
                                        </span>
                                    </div>
                                @endif
                            </div>
                            
                            @if(!$breakStatus['is_on_break'])
                                <form action="{{ route('punch-out') }}" method="POST" class="mb-4">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-lg w-100">
                                        <i class="fas fa-sign-out-alt me-2"></i> Punch Out
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        <!-- Ready to Punch In -->
                        <div class="text-center py-4">
                            <div class="punch-status bg-light rounded p-4 mb-4">
                                <i class="fas fa-clock fa-2x text-muted mb-3"></i>
                                <p class="mb-0">Ready to start your workday</p>
                                @if($currentSchedule)
                                    <small class="text-muted">
                                        Scheduled start: {{ \Carbon\Carbon::parse($currentSchedule->start_time)->format('h:i A') }}
                                        <br>Break time: {{ $currentSchedule->break_time }} minutes
                                    </small>
                                @endif
                            </div>
                            <form action="{{ route('punch-in') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-sign-in-alt me-2"></i> Punch In
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ... rest of your existing blade content remains the same ... -->
  
        <!-- Statistics Card -->
        <div class="col-md-4 mb-4 mb-md-0">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">Monthly Statistics</h5>
                    <p class="text-muted small mb-0">{{ now()->format('F Y') }}</p>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stat-card text-center p-3 rounded bg-light">
                                <h4 class="text-primary mb-1">{{ $monthlyStats['working_days'] }}</h4>
                                <small class="text-muted">Working Days</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card text-center p-3 rounded bg-light">
                                <h4 class="text-success mb-1">{{ number_format($monthlyStats['total_hours'], 1) }}</h4>
                                <small class="text-muted">Total Hours</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card text-center p-3 rounded bg-light">
                                <h4 class="text-info mb-1">{{ $monthlyStats['on_time_days'] }}</h4>
                                <small class="text-muted">On Time</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card text-center p-3 rounded bg-light">
                                <h4 class="text-warning mb-1">{{ $monthlyStats['late_days'] }}</h4>
                                <small class="text-muted">Late Days</small>
                            </div>
                        </div>
                        @if(isset($monthlyStats['overtime_hours']) && $monthlyStats['overtime_hours'] > 0)
                        <div class="col-12">
                            <div class="stat-card text-center p-3 rounded bg-light">
                                <h4 class="text-purple mb-1">{{ number_format($monthlyStats['overtime_hours'], 1) }}</h4>
                                <small class="text-muted">Overtime Hours</small>
                            </div>
                        </div>
                        @endif
                        @if(isset($monthlyStats['attendance_percentage']))
                        <div class="col-12">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">Attendance Rate</span>
                                <span class="small fw-bold">{{ $monthlyStats['attendance_percentage'] }}%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" 
                                     style="width: {{ $monthlyStats['attendance_percentage'] }}%"></div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Today Activity Card -->
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">Today's Activity</h5>
                </div>
                <div class="card-body p-0">
                    <div class="activity-container" style="max-height: 400px; overflow-y: auto;">
                        <ul class="list-group list-group-flush">
                            @if($isRestrictedDay)
                                <li class="list-group-item border-0 py-4 text-center">
                                    <div class="empty-state">
                                        <i class="fas fa-calendar-times fa-2x text-muted mb-3"></i>
                                        <p class="mb-0">{{ $restrictionReason }}</p>
                                        <small class="text-muted">No activities allowed today</small>
                                    </div>
                                </li>
                            @else
                                @forelse($todayAttendance as $activity)
                                    <li class="list-group-item border-0 py-3 px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm bg-soft-primary rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-sign-in-alt text-primary"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0">Punched In</h6>
                                                <p class="text-muted small mb-0">
                                                    <i class="far fa-clock me-1"></i>
                                                    {{ \Carbon\Carbon::parse($activity->punch_in)->format('h:i A') }}
                                                    @if($activity->status)
                                                        <span class="badge badge-sm bg-{{ $activity->status == 'late' ? 'warning' : ($activity->status == 'early' ? 'info' : 'success') }} ms-2">
                                                            {{ ucfirst($activity->status) }}
                                                        </span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </li>

                                    <!-- Break Sessions -->
                                    @if($activity->break_sessions)
                                        @php
                                            $breakSessions = json_decode($activity->break_sessions, true) ?? [];
                                        @endphp
                                        @foreach($breakSessions as $breakSession)
                                            @if($breakSession['end_time'])
                                                <li class="list-group-item border-0 py-2 px-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar-sm bg-soft-info rounded-circle d-flex align-items-center justify-content-center">
                                                                <i class="fas fa-coffee text-info"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-0 small">Break</h6>
                                                            <p class="text-muted small mb-0">
                                                                {{ \Carbon\Carbon::parse($breakSession['start_time'])->format('h:i A') }} - 
                                                                {{ \Carbon\Carbon::parse($breakSession['end_time'])->format('h:i A') }}
                                                                <span class="badge bg-info ms-1">{{ $breakSession['duration'] ?? 0 }} min</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endif
                                        @endforeach
                                    @endif

                                    @if($activity->punch_out)
                                        <li class="list-group-item border-0 py-3 px-4">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar-sm bg-soft-danger rounded-circle d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-sign-out-alt text-danger"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-0">Punched Out</h6>
                                                    <p class="text-muted small mb-0">
                                                        <i class="far fa-clock me-1"></i>
                                                        {{ \Carbon\Carbon::parse($activity->punch_out)->format('h:i A') }}
                                                        @if($activity->working_hours)
                                                            <span class="text-success ms-2">
                                                                ({{ $controller->formatDuration($activity->working_hours) }})
                                                            </span>
                                                        @endif
                                                        @if($activity->overtime_hours > 0)
                                                            <span class="badge bg-warning ms-1">
                                                                OT: {{ $activity->overtime_hours }}h
                                                            </span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                @empty
                                    <li class="list-group-item border-0 py-4 text-center">
                                        <div class="empty-state">
                                            <i class="fas fa-history fa-2x text-muted mb-3"></i>
                                            <p class="mb-0">No activities recorded today</p>
                                        </div>
                                    </li>
                                @endforelse
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Filter -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('attendance.search') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
                </div>
                <div class="col-md-3">
                    <label for="month" class="form-label">Month</label>
                    <select class="form-select" id="month" name="month">
                        <option value="">All Months</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="year" class="form-label">Year</label>
                    <select class="form-select" id="year" name="year">
                        <option value="">All Years</option>
                        @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                            <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Records Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Attendance Records</h5>
               <div class="btn-group">
    <button class="btn btn-sm btn-outline-secondary active" onclick="toggleView('attendance')">
        <i class="fas fa-calendar-check me-1"></i> Attendance
    </button>

    <!-- <button class="btn btn-sm btn-outline-warning" onclick="toggleView('overtime')">
        <i class="fas fa-clock me-1"></i> Overtime
    </button> -->
   
    <a href="{{ route('manual-punch.index') }}" class="btn btn-sm btn-outline-primary">
        <i class="fas fa-hand-point-up me-1"></i> Missed Punch Request
    </a>
  
</div>

            </div>
        </div>
        <div class="card-body p-0">
            <!-- Attendance Records View -->
            <div id="attendanceView" class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">S.No</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Shift</th>
                            <th>Punch In</th>
                            <th>Punch Out</th>
                            <th>Break Time</th>
                            <th>Working Hours</th>
                            <th>Status</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($searchResults ?? $todayAttendance as $index => $record)
                            @php
                                $recordDate = \Carbon\Carbon::parse($record->date);
                                $isHoliday = DB::table('holidays')->whereDate('holidaydate', $record->date)->first();
                                $isSunday = $recordDate->dayOfWeek === \Carbon\Carbon::SUNDAY;
                                
                                // Get overtime for this date
                                $overtimeRecord = null;
                                if(isset($employeeOvertimeRecords)) {
                                    $overtimeRecord = $employeeOvertimeRecords->where('overtime_date', $record->date)->first();
                                }

                                // Parse break sessions
                                $breakSessions = json_decode($record->break_sessions ?? '[]', true);
                                $totalBreakTaken = $record->total_break_taken ?? 0;
                                $allocatedBreakTime = $record->allocated_break_time ?? 0;
                            @endphp
                            <tr>
                                <td class="ps-4">{{ $index + 1 }}</td>
                                <td>{{ $recordDate->format('d M Y') }}</td>
                                <td>
                                    <span class="badge {{ $isSunday ? 'bg-danger' : ($isHoliday ? 'bg-warning' : 'bg-primary') }}">
                                        {{ $recordDate->format('l') }}
                                    </span>
                                </td>
                                <td>
                                      
                                     
                                   
                                </td>
                                <td>
                                    @if($record->punch_in)
                                        <span class="badge bg-soft-primary text-primary">
                                            {{ \Carbon\Carbon::parse($record->punch_in)->format('h:i A') }}
                                        </span>
                                        <!-- @if($record->status == 'late')
                                            <br><small class="text-warning">Late</small>
                                        @elseif($record->status == 'early')
                                            <br><small class="text-info">Early</small>
                                        @endif -->
                                    @else
                                        <span class="badge bg-soft-secondary text-secondary">--</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->punch_out)
                                        <span class="badge bg-soft-danger text-danger">
                                            {{ \Carbon\Carbon::parse($record->punch_out)->format('h:i A') }}
                                        </span>
                                    @else
                                        <span class="badge bg-soft-secondary text-secondary">--</span>
                                    @endif
                                </td>
                                <td>
                                    @if($allocatedBreakTime > 0)
                                        <div class="break-info">
                                            <span class="badge bg-info">
                                                {{ $totalBreakTaken }}/{{ $allocatedBreakTime }} min
                                            </span>
                                            @if(count($breakSessions) > 0)
                                                <br><small class="text-muted">
                                                    {{ count($breakSessions) }} session(s)
                                                </small>
                                                <!-- Break sessions tooltip -->
                                                <div class="break-sessions-tooltip" style="display: none;">
                                                    @foreach($breakSessions as $session)
                                                        @if($session['end_time'])
                                                            <div class="small">
                                                                {{ \Carbon\Carbon::parse($session['start_time'])->format('h:i A') }} - 
                                                                {{ \Carbon\Carbon::parse($session['end_time'])->format('h:i A') }}
                                                                ({{ $session['duration'] ?? 0 }} min)
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="working-hours-info">
                                        <span class="fw-bold">{{ $controller->formatDuration($record->working_hours ?? 0) }}</span>
                                        @if($record->scheduled_hours)
                                           
                                        @endif
                                        @if($record->actual_working_minutes)
                                            <br><small class="text-info">
                                                Actual: {{ floor($record->actual_working_minutes / 60) }}h {{ $record->actual_working_minutes % 60 }}m
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($isSunday)
                                        <span class="badge bg-danger">Sunday</span>
                                    @elseif($isHoliday)
                                        <span class="badge bg-warning">{{ $isHoliday->title }}</span>
                                    @elseif($record->punch_out)
                                        <span class="badge bg-success">Complete</span>
                                        @if($record->undertime_minutes > 0)
                                            <br><span class="badge bg-warning">
                                                Undertime: {{ floor($record->undertime_minutes / 60) }}h {{ $record->undertime_minutes % 60 }}m
                                            </span>
                                        @endif
                                    @elseif($record->punch_in)
                                        <span class="badge bg-info">In Progress</span>
                                    @else
                                        <span class="badge bg-secondary">No Record</span>
                                    @endif
                                </td>
                               
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="fas fa-table fa-2x text-muted mb-3"></i>
                                        <p class="mb-0">No attendance records found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Overtime Records View -->
            <div id="overtimeView" class="table-responsive" style="display: none;">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Date</th>
                            <th>Overtime Hours</th>
                            <th>Status</th>
                            <th>Approved By</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($employeeOvertimeRecords))
                            @forelse($employeeOvertimeRecords as $index => $overtime)
                                <tr>
                                    <td class="ps-4">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="fw-bold">{{ date('d M Y', strtotime($overtime->overtime_date)) }}</span>
                                        <br><small class="text-muted">{{ date('l', strtotime($overtime->overtime_date)) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning-light text-warning">
                                            {{ number_format($overtime->overtime_hours, 0) }} hrs
                                        </span>
                                    </td>
                                   
                                    <td>
                                        @if($overtime->status == 'pending')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        @elseif($overtime->status == 'approved')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Approved
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times me-1"></i>Rejected
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($overtime->approved_by)
                                            <div>
                                                <small class="fw-bold">{{ $overtime->approver_firstname }} {{ $overtime->approver_lastname }}</small>
                                                <br><small class="text-muted">{{ date('d M Y', strtotime($overtime->approved_at)) }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($overtime->remarks)
                                            <span class="text-truncate" style="max-width: 150px;" 
                                                  data-bs-toggle="tooltip" title="{{ $overtime->remarks }}">
                                                {{ Str::limit($overtime->remarks, 30) }}
                                            </span>
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-clock fa-2x text-muted mb-3"></i>
                                            <p class="mb-0">No overtime records found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        @else
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="fas fa-clock fa-2x text-muted mb-3"></i>
                                        <p class="mb-0">No overtime records found</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-sm {
        width: 36px;
        height: 36px;
        font-size: 16px;
    }
    .activity-container::-webkit-scrollbar {
        width: 6px;
    }
    .activity-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .activity-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    .empty-state {
        opacity: 0.6;
    }
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        background-color: #f8f9fa;
        transform: translateY(-2px);
    }
    .restriction-notice {
        border: 2px dashed #dee2e6;
    }
    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.1);
    }
    .bg-soft-danger {
        background-color: rgba(220, 53, 69, 0.1);
    }
    .bg-soft-secondary {
        background-color: rgba(108, 117, 125, 0.1);
    }
    .bg-soft-info {
        background-color: rgba(13, 202, 240, 0.1);
    }
    .text-purple {
        color: #6f42c1 !important;
    }
    .bg-purple {
        background-color: #6f42c1 !important;
    }
    .badge-sm {
        font-size: 0.75em;
    }
    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }
    .bg-success-light {
        background-color: rgba(25, 135, 84, 0.1);
    }
    .bg-info-light {
        background-color: rgba(13, 202, 240, 0.1);
    }
    .bg-primary-light {
        background-color: rgba(13, 110, 253, 0.1);
    }
    .break-info:hover .break-sessions-tooltip {
        display: block !important;
        position: absolute;
        background: #333;
        color: white;
        padding: 8px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 1000;
        margin-top: 5px;
    }
    .working-hours-info {
        min-width: 120px;
    }
</style>

<script>
    // Initialize tooltips and other components
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Auto refresh every 5 minutes (only if not restricted day)
        @if(!$isRestrictedDay)
        setTimeout(function(){
            window.location.reload();
        }, 300000);
        @endif

        // Update break status every 30 seconds if on break
        @if(isset($breakStatus) && $breakStatus['is_on_break'])
        setInterval(function() {
            updateBreakTimer();
        }, 30000);
        @endif
    });

    function toggleView(viewType) {
        if (viewType === 'attendance') {
            $('#attendanceView').show();
            $('#overtimeView').hide();
            $('button[onclick="toggleView(\'attendance\')"]').removeClass('btn-outline-secondary').addClass('btn-secondary');
            $('button[onclick="toggleView(\'overtime\')"]').removeClass('btn-warning').addClass('btn-outline-warning');
        } else {
            $('#attendanceView').hide();
            $('#overtimeView').show();
            $('button[onclick="toggleView(\'overtime\')"]').removeClass('btn-outline-warning').addClass('btn-warning');
            $('button[onclick="toggleView(\'attendance\')"]').removeClass('btn-secondary').addClass('btn-outline-secondary');
        }
    }

    function updateBreakTimer() {
        fetch('{{ route("break-status") }}')
            .then(response => response.json())
            .then(data => {
                if (data.is_on_break && data.current_break_start) {
                    const breakStart = new Date(data.current_break_start);
                    const now = new Date();
                    const duration = Math.floor((now - breakStart) / 1000 / 60);
                    
                    // Update break duration display if element exists
                    const breakAlert = document.querySelector('.alert-info');
                    if (breakAlert) {
                        breakAlert.innerHTML = `<i class="fas fa-coffee me-1"></i>Currently on break for ${duration} minutes`;
                    }
                }
            })
            .catch(error => console.error('Error updating break timer:', error));
    }

    // Prevent punch out while on break
    document.addEventListener('DOMContentLoaded', function() {
        const punchOutForm = document.querySelector('form[action="{{ route("punch-out") }}"]');
        if (punchOutForm) {
            punchOutForm.addEventListener('submit', function(e) {
                @if(isset($breakStatus) && $breakStatus['is_on_break'])
                e.preventDefault();
                alert('Please end your current break before punching out!');
                @endif
            });
        }
    });

    function exportOvertime() {
    const month = $('#month').val();
    const year = $('#year').val();
    const status = $('#status').val();
    const employeeId = $('#employee_id').val();
    
    // Build query parameters
    let params = {
        month: month,
        year: year,
        status: status
    };
    
    if (employeeId) {
        params.employee_id = employeeId;
    }
    
    // Convert to query string
    const queryString = new URLSearchParams(params).toString();
    
    // Trigger download
    window.location.href = `/overtime/export?${queryString}`;
}
// Prevent actions during permission hours
document.addEventListener('DOMContentLoaded', function() {
        @if(isset($hasActivePermission) && $hasActivePermission['has_permission'])
            // Disable all attendance-related buttons
            const punchInBtn = document.querySelector('button[type="submit"]');
            const punchOutBtn = document.querySelector('form[action="{{ route("punch-out") }}"] button');
            const startBreakBtn = document.querySelector('form[action="{{ route("start-break") }}"] button');
            const endBreakBtn = document.querySelector('form[action="{{ route("end-break") }}"] button');
            
            [punchInBtn, punchOutBtn, startBreakBtn, endBreakBtn].forEach(btn => {
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('disabled');
                }
            });
        @endif
    });
</script>
@endsection