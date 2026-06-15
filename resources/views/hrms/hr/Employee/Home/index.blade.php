@extends('layouts.index')

@section('content')

<style>
/* ================= BANNER ================= */
.employee-banner {
    position: relative;
    height: 200px;
    background: url('{{ asset("admin/assets/bg.jpg") }}') center/cover no-repeat;
    margin: -15px -15px 0 -15px; /* FULL WIDTH */
}

.employee-banner::after {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.35);
}

/* ================= MAIN WRAPPER ================= */
.employee-wrapper {
    position: relative;
    padding: 0 20px;
}

/* ================= LEFT PROFILE CARD ================= */
.profile-card {
    background: #fff;
    border-radius: 18px;
    padding: 20px;
    margin-top: -90px;
    box-shadow: 0 6px 24px rgba(0,0,0,0.12);
}

.avatar {
    width: 78px;
    height: 78px;
    border-radius: 14px;
    background: #ff7f2a;
    color: #fff;
    font-size: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ================= TABS BAR ================= */
.tabs-wrapper {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 5px 18px rgba(0,0,0,0.10);
    margin-top: -65px;
    padding: 16px 28px;
}

.tabs-wrapper ul {
    list-style: none;
    display: flex;
    gap: 32px;
    padding: 0;
    margin: 0;
}

.tabs-wrapper li {
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    padding-bottom: 6px;
    color: #444;
}

.tabs-wrapper li.active {
    color: #ff7f2a;
    border-bottom: 3px solid #ff7f2a;
}

/* ================= CONTENT ================= */
.tab-content-box {
    background: #fff;
    border-radius: 18px;
    padding: 40px;
    margin-top: 18px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.06);
}

/* Custom card styles */
.card {
    border-radius: 12px;
    border: none;
    overflow: hidden;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,0.05);
    background: #fff;
}
/* ================= TIMESHEET CARD COMPACT ================= */

.card .punch-status {
    padding: 14px !important;
}

.card .card-body {
    padding: 16px;
}

.card h4 {
    font-size: 20px;
}

.card .btn-lg {
    padding: 10px;
    font-size: 15px;
}


.btn-lg {
    padding: 12px 24px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 10px;
}

.alert {
    border-radius: 10px;
    border: none;
}

.progress {
    border-radius: 6px;
}

.badge {
    border-radius: 20px;
    padding: 6px 12px;
    font-weight: 500;
}
.tab-pane {
    display: none;
}

.tab-pane.active {
    display: block;
}
/* ================= PROFILE CARDS FIX ================= */

/* Make profile grid cards equal height */
.tab-pane .row.g-3 > div {
    display: flex;
}

.tab-pane .card {
    width: 100%;
    height: 100%;
}

/* Compact card body */
.tab-pane .card-body {
    padding: 16px 18px;
}

/* Orange title style */
.card-header {
    font-weight: 700;
    font-size: 15px;
    color: #ff7f2a;
    background: #fff;
    padding: 14px 18px;
}

/* Remove excessive spacing in profile text */
.tab-pane p {
    margin-bottom: 6px;
    font-size: 14px;
}

/* Address card text */
.tab-pane .card-body {
    font-size: 14px;
    color: #333;
}
/* ================= LEAVE SUMMARY DESIGN ================= */

.leave-card {
    background: #fff;
    border-radius: 14px;
    padding: 14px 18px;   /* was 18px 22px */
    margin-bottom: 10px; /* was 14px */
    box-shadow: 0 4px 14px rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.leave-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.leave-icon {
    width: 38px;      /* was 44px */
    height: 38px;
    font-size: 17px; /* was 20px */
    border-radius: 10px;   width: 44px;
    border-radius: 12px;
    background: #ff7f2a;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 20px;
}

.leave-name {
    font-size: 15px; /* was 16px */
    font-weight: 600;
}

.leave-right {
    display: flex;
    gap: 26px; /* was 36px */
    text-align: center;
}

.leave-col {
    min-width: 90px;
}

.leave-col span {
    display: block;
    font-size: 12px;
    color: #777;
}

.leave-col strong {
    font-size: 14px;
    color: #0a8f3c; /* green */
    font-weight: 600;
}
/* ===== Attendance Calendar ===== */

.attendance-calendar {
    border: 1px solid #e6e6e6;
    border-radius: 12px;
    overflow: hidden;
}

.calendar-head {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    background: #f8f9fa;
    font-weight: 600;
}

.day-head {
    padding: 10px;
    text-align: center;
    border-right: 1px solid #eee;
}

.calendar-body {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
}

.calendar-cell {
    min-height: 110px;
    border-right: 1px solid #eee;
    border-bottom: 1px solid #eee;
    padding: 6px;
    position: relative;
    background: #fff;
}

.calendar-cell.muted {
    background: #fafafa;
    color: #bbb;
}

.date-number {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 4px;
}

/* Events */
.event {
    font-size: 12px;
    padding: 4px 6px;
    border-radius: 6px;
    margin-top: 4px;
}

.event.shift {
    background: #ffe0b2;
    border-left: 4px solid #ff9800;
}

.event.leave {
    background: #ffd6d6;
    border-left: 4px solid #e53935;
}

.event.holiday {
    background: #e3f2fd;
    border-left: 4px solid #1e88e5;
}
/* ================= COMPACT CALENDAR ================= */

.calendar-wrapper {
    transform: scale(0.95);        /* overall shrink */
    transform-origin: top center;
}

/* Day header */
.calendar-table th {
    font-size: 10px;
    padding: 8px 4px;
}

/* Date number */
.calendar-day-number {
    font-size: 10px;
    font-weight: 500;
}

/* Calendar cells */
.calendar-table td {
    height: 100px;                 /* reduce cell height */
    padding: 4px;
}

/* Shift / Leave cards */
.calendar-event {
    font-size: 10px;
    padding: 4px 6px;
    border-radius: 6px;
    line-height: 1.4;
}

/* Reduce spacing between rows */
.calendar-table {
    border-spacing: 4px;
}

</style>

<!-- ================= BANNER ================= -->
<div class="employee-banner"></div>

<div class="container-fluid employee-wrapper">
    <div class="row">

        <!-- ================= LEFT ================= -->
        <div class="col-md-3">
         <!-- Profile Card -->
<div class="profile-card mb-4">
    <div class="d-flex align-items-center">
        <div class="avatar me-3">
            {{ strtoupper(substr(Session::get('first_name') ?? 'U', 0, 1)) }}
        </div>
        <div class="flex-grow-1">
            <h5 class="mb-1">{{ Session::get('first_name') ?? '' }} {{ Session::get('last_name') ?? '' }}</h5>
            <p class="text-muted mb-0">Employee</p>
            <small class="text-primary">ID: {{ Session::get('employee_id') ?? '' }}</small>
        </div>
    </div>
    
    <!-- Reporting Manager -->
    @if($manager)
    <div class="mt-3 pt-3 border-top">
        <small class="text-muted d-block mb-1">Reporting To</small>
        <div class="d-flex align-items-center">
            <div class="manager-avatar me-2" style="width: 30px; height: 30px; background: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                {{ strtoupper(substr($manager->firstname ?? 'M', 0, 1)) }}
            </div>
            <div class="fw-semibold">
                {{ $manager->firstname ?? '-' }} {{ $manager->lastname ?? '' }}
            </div>
        </div>
    </div>
    @endif
</div>

            <!-- Timesheet Card -->
            <div class="card h-100 border-0 shadow-sm mb-4">
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
                        <div class="text-center py-4">
                            <div class="restriction-notice bg-light rounded p-4 mb-4">
                                <i class="fas fa-ban fa-2x text-warning mb-3"></i>
                                <p class="mb-0 text-muted">{{ $restrictionReason }}</p>
                            </div>
                        </div>
                    @elseif(isset($hasActivePermission) && $hasActivePermission['has_permission'])
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
                        <div class="text-center mb-4">
                            <div class="punch-status bg-light rounded p-3 mb-3">
                                <h6 class="text-muted mb-1">Punched In At</h6>
                                <h4 class="text-success mb-0">
                                    {{ \Carbon\Carbon::parse($todayAttendance->last()->punch_in)->format('h:i A') }}
                                </h4>
                                <input type="hidden" id="punchInTime"
       value="{{ \Carbon\Carbon::parse($todayAttendance->last()->punch_in)->timestamp }}">

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
                                    <span class="fw-bold">
                                        <span id="liveWorkTimer">00:00:00</span>
                                    </span>
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

        <!-- ================= RIGHT ================= -->
        <div class="col-md-9">
            <!-- ================= TABS ================= -->
            <div class="tabs-wrapper">
                <ul id="employeeTabs">
                    <li data-tab="feeds" class="active">Feeds</li>
                    <li data-tab="profile">Profile</li>
                    <li data-tab="leave">Leave</li>
                    <li data-tab="attendance">Attendance</li>
                    <li data-tab="timesheets">Task</li>
                </ul>
            </div>
            
            <div class="tab-content-box">
                <div class="tab-pane active" id="feeds">
                    {{-- 🎂 Birthdays --}}
                    @if($feeds['birthdays']->count())
                        <h6 class="fw-bold mb-2">🎂 Birthdays Today</h6>
                        @foreach($feeds['birthdays'] as $b)
                            <div class="feed-card mb-2 p-3 bg-light rounded">
                                <strong>{{ $b->firstname }} {{ $b->lastname }}</strong>
                                <div class="text-muted small">Wish them a happy birthday 🎉</div>
                
                                @if($b->id != Session::get('user_id'))
                                <form method="POST" action="{{ route('feed.wish') }}" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="employee_id" value="{{ $b->id }}">
                                    <input type="text" name="message" class="form-control form-control-sm"
                                           placeholder="Write a birthday wish..." required>
                                    <button class="btn btn-primary btn-sm mt-1">Send Wish</button>
                                </form>
                                @endif
                            </div>
                        @endforeach
                    @endif
                
                    {{-- 💍 Work Anniversary --}}
                    @if($feeds['anniversaries']->count())
                        <h6 class="fw-bold mt-4 mb-2">💍 Work Anniversary</h6>
                        @foreach($feeds['anniversaries'] as $a)
                            <div class="feed-card mb-2 p-3 bg-light rounded">
                                <strong>{{ $a->firstname }} {{ $a->lastname }}</strong>
                                <div class="text-muted small">Celebrating work anniversary 🎊</div>
                            </div>
                        @endforeach
                    @endif
                
                    {{-- 💬 Wishes --}}
                    <h6 class="fw-bold mt-4 mb-2">💬 Wishes for You</h6>
                
                    @forelse($feeds['wishes'] as $wish)
                        <div class="feed-card mb-2 p-3 border rounded">
                            <strong>{{ $wish->sender_name ?? 'System' }}</strong>
                            <div class="small text-muted">{{ $wish->created_at->diffForHumans() }}</div>
                            <p class="mb-0">{{ $wish->message }}</p>
                        </div>
                    @empty
                        <p class="text-muted">No wishes yet.</p>
                    @endforelse
                </div>
                
                <div class="tab-pane" id="profile">
                    <div class="row">
                        <div class="col-md-6 d-flex">
                            <div class="card profile-box flex-fill">
                                <div class="card-body">
                                    <h3 class="card-title">Personal Information</h3>
                                    <ul class="personal-info">
                                        <li>
                                            <div class="title">Passport No.</div>
                                            <div class="text">{{ $employee->passport_no ?? 'N/A'}}</div>
                                        </li>
                                        <li>
                                            <div class="title">Aadhaar Number</div>
                                            <div class="text">{{ $employee->passport_exp_date ?? 'N/A' }}</div>
                                        </li>
                                        <li>
                                            <div class="title">Blood group</div>
                                            <div class="text">
                                                @if($employee && $employee->tel)
                                                    <a href="tel:{{ $employee->tel }}">{{ $employee->tel }}</a>
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </li>
                                        <li>
                                            <div class="title">Nationality</div>
                                            <div class="text">{{ $employee->nationality ?? 'N/A'}}</div>
                                        </li>
                                        <li>
                                            <div class="title">Religion</div>
                                            <div class="text">{{ $employee->religion ?? 'N/A'}}</div>
                                        </li>
                                        <li>
                                            <div class="title">Marital status</div>
                                            <div class="text">{{ $employee->marital_status ?? 'N/A'}}</div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex">
                            <div class="card profile-box flex-fill">
                                <div class="card-body">
                                    <h3 class="card-title">Emergency Contact</h3>
                                    <h5 class="section-title">Primary</h5>
                                    <ul class="personal-info">
                                        <li>
                                            <div class="title">Name</div>
                                            <div class="text">{{ $employee->primary_name ?? 'N/A' }}</div>
                                        </li>
                                        <li>
                                            <div class="title">Relationship</div>
                                            <div class="text">{{ $employee->relationship ?? 'N/A'}}</div>
                                        </li>
                                        <li>
                                            <div class="title">Phone</div>
                                            <div class="text">{{ $employee->phone ?? 'N/A' }}</div>
                                        </li>
                                    </ul>
                                    <hr>
                                    <h5 class="section-title">Secondary</h5>
                                    <ul class="personal-info">
                                        <li>
                                            <div class="title">Name</div>
                                            <div class="text">{{ $employee->secondary_name ?? 'N/A' }}</div>
                                        </li>
                                        <li>
                                            <div class="title">Relationship</div>
                                            <div class="text">{{ $employee->secondary_relationship ?? 'N/A' }}</div>
                                        </li>
                                        <li>
                                            <div class="title">Phone</div>
                                            <div class="text">{{ $employee->secondary_phone ?? 'N/A' }}</div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 d-flex">
                            <div class="card profile-box flex-fill">
                                <div class="card-body">
                                    <h3 class="card-title">Bank Information</h3>
                                    <ul class="personal-info">
                                        <li>
                                            <div class="title">Bank Name</div>
                                            <div class="text">{{ $employee->bank_name ?? 'N/A' }}</div>
                                        </li>
                                        <li>
                                            <div class="title">Bank Account No.</div>
                                            <div class="text">{{ $employee->bank_account_no ?? 'N/A' }}</div>
                                        </li>
                                        <li>
                                            <div class="title">IFSC Code</div>
                                            <div class="text">{{ $employee->ifsc_code ?? 'N/A' }}</div>
                                        </li>
                                        <li>
                                            <div class="title">PAN No</div>
                                            <div class="text">{{ $employee->pan_no ?? 'N/A' }}</div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex">
                            <div class="card profile-box flex-fill">
                                <div class="card-body">
                                    <h3 class="card-title d-flex justify-content-between align-items-center">
                                        Family Informations
                                    </h3>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover align-middle mb-0">
                                            <thead class="table-light text-center">
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Relationship</th>
                                                    <th>Phone</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($familyMembers as $family)
                                                <tr>
                                                    <td>{{ $family->name }}</td>
                                                    <td class="text-center">{{ $family->relationship }}</td>
                                                    <td class="text-center">{{ $family->phone }}</td>
                                                    <td class="text-center">
                                                        @if((Session::get('role') === 'employee' && $family_info_edited == 0) || Session::get('role') === 'admin')
                                                        <div class="dropdown dropdown-action">
                                                            <a class="action-icon dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="material-icons">more_vert</i></a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#deleteFamilyModal"
                                                                   onclick="setDeleteFormAction('{{ route('family.delete', $family->id) }}')">
                                                                    <i class="fa-regular fa-trash-can me-2"></i> Delete
                                                                </a>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No family members found.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Delete Confirmation Modal -->
                    <div class="modal fade" id="deleteFamilyModal" tabindex="-1" aria-labelledby="deleteFamilyModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="border-radius: 15px;">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title w-100 text-center" id="deleteFamilyModalLabel" style="font-weight: bold;">Delete Family Member</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    Are you sure you want to delete this family member?
                                </div>
                                <div class="modal-footer d-flex justify-content-around border-0">
                                    <button type="button" class="btn btn-outline-warning btn-lg" data-bs-dismiss="modal" style="border-radius: 50px; width: 150px;">Cancel</button>
                                    <form id="deleteFamilyForm" method="POST" action="" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-lg" style="border-radius: 50px; width: 150px;">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 d-flex">
                            <div class="card profile-box flex-fill">
                                <div class="card-body">
                                    <h3 class="card-title">Education Informations</h3>
                                    
                                    <div class="experience-box">
                                        <ul class="experience-list">
                                            @foreach($educationInfos as $education)
                                            <li>
                                                <div class="experience-user">
                                                    <div class="before-circle"></div>
                                                </div>
                                                <div class="experience-content">
                                                    <div class="timeline-content">
                                                        <a href="#/" class="name">{{ $education->institution }}</a>
                                                        <div>{{ $education->degree }}</div>
                                                        <span class="time">{{ $education->start_date }} - {{ $education->end_date }}</span>
                                                    </div>
                                                </div>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex">
                            <div class="card profile-box flex-fill">
                                <div class="card-body">
                                    <h3 class="card-title">Experience</h3>
                                    <div class="experience-box">
                                        <ul class="experience-list">
                                            @foreach($experienceInfos as $experience)
                                            <li>
                                                <div class="experience-user">
                                                    <div class="before-circle"></div>
                                                </div>
                                                <div class="experience-content">
                                                    <div class="timeline-content">
                                                        <a href="#/" class="name">
                                                            @if(!empty($experience->position) && !empty($experience->company_name))
                                                                {{ $experience->position }} at {{ $experience->company_name }}
                                                            @elseif(!empty($experience->position))
                                                                {{ $experience->position }}
                                                            @elseif(!empty($experience->company_name))
                                                                {{ $experience->company_name }}
                                                            @endif
                                                        </a>
                                                        
                                                        <span class="time">
                                                            @if(!empty($experience->period_from) && !empty($experience->period_to))
                                                                {{ \Carbon\Carbon::parse($experience->period_from)->format('M Y') }}
                                                                -
                                                                {{ \Carbon\Carbon::parse($experience->period_to)->format('M Y') }}
                                                            @elseif(!empty($experience->period_from) && empty($experience->period_to))
                                                                {{ \Carbon\Carbon::parse($experience->period_from)->format('M Y') }} - Present
                                                            @elseif(empty($experience->period_from) && !empty($experience->period_to))
                                                                {{ \Carbon\Carbon::parse($experience->period_to)->format('M Y') }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="tab-pane" id="leave">
                    <a href="{{ route('employee-leaves.index') }}" class="btn btn-primary btn-sm">
                        Apply Leave
                    </a>

                    @php
                        $leaveUI = [
                            'Casual Leave' => 'fa-gear',
                            'Sick' => 'fa-stethoscope',
                            'Hospitalisation' => 'fa-hospital',
                            'Maternity Leave' => 'fa-baby',
                            'Paternity Leave' => 'fa-children'
                        ];
                    @endphp
                
                    @foreach($leaveUI as $type => $icon)
                        @php
                            $balance = $leaveBalances[$type] ?? null;
                
                            $paid = $balance->used_days ?? 0;
                            $remaining = $balance->remaining_days ?? 0;
                
                            // If paid exceeds allocation → LOP
                            $lop = max(0, $paid - ($balance->allocated_days ?? 0));
                        @endphp
                
                        <div class="leave-card">
                            <!-- LEFT -->
                            <div class="leave-left">
                                <div class="leave-icon">
                                    <i class="fa-solid {{ $icon }}"></i>
                                </div>
                                <div class="leave-name">
                                    {{ $type }}
                                </div>
                            </div>
                
                            <!-- RIGHT -->
                            <div class="leave-right">
                                <div class="leave-col">
                                    <span>Paid</span>
                                    <strong>{{ $paid }} days</strong>
                                </div>
                                <div class="leave-col">
                                    <span>Unpaid (LOP)</span>
                                    <strong>{{ $lop }} days</strong>
                                </div>
                                <div class="leave-col">
                                    <span>Remaining</span>
                                    <strong>{{ $remaining }} days</strong>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="tab-pane" id="attendance">
                    @php
                        use Carbon\Carbon;
                    
                        $currentMonth = Carbon::parse($month ?? now());
                        $startOfMonth = $currentMonth->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
                        $endOfMonth   = $currentMonth->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);
                    @endphp
                    
                    <form method="GET" class="d-flex align-items-center gap-2 mb-3">
                        <label class="fw-semibold mb-0">Month:</label>
                    
                        <input type="month"
                               name="month"
                               value="{{ request('month', now()->format('Y-m')) }}"
                               class="form-control form-control-sm"
                               style="max-width: 180px;"
                               onchange="this.form.submit()">
                    </form>
                     
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold">
                            {{ $currentMonth->format('F Y') }}
                        </h5>
                    
                        <div>
                            <a href="?month={{ $currentMonth->copy()->subMonth()->format('Y-m') }}"
                               class="btn btn-outline-secondary btn-sm">‹</a>
                    
                            <a href="?month={{ now()->format('Y-m') }}"
                               class="btn btn-outline-primary btn-sm">Today</a>
                    
                            <a href="?month={{ $currentMonth->copy()->addMonth()->format('Y-m') }}"
                               class="btn btn-outline-secondary btn-sm">›</a>
                        </div>
                    </div>
                    
                    <div class="calendar-wrapper">
                        <div class="attendance-calendar">
                            <div class="calendar-head">
                                @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
                                    <div class="day-head">{{ $day }}</div>
                                @endforeach
                            </div>
                        
                            <div class="calendar-body">
                                @for($date = $startOfMonth->copy(); $date <= $endOfMonth; $date->addDay())
                                    @php
                                        $dateStr = $date->format('Y-m-d');
                                        $data = $calendarData[$dateStr] ?? null;
                                    @endphp
                        
                                    <div class="calendar-cell {{ $date->month != $currentMonth->month ? 'muted' : '' }}">
                                        <div class="date-number">{{ $date->day }}</div>
                        
                                        {{-- Holiday --}}
                                        @if($data && $data['type'] === 'holiday')
                                            <div class="event holiday">
                                                {{ $data['title'] }}
                                            </div>
                        
                                        {{-- Leave --}}
                                        @elseif($data && $data['type'] === 'leave')
                                            <div class="event leave">
                                                Leave: {{ $data['leave_type'] }}
                                            </div>
                        
                                        {{-- Shift --}}
                                        @elseif($data && $data['type'] === 'shift')
                                            <div class="event shift">
                                                <strong>{{ $data['shift_name'] }}</strong><br>
                                                {{ $data['start'] }} – {{ $data['end'] }}
                                            </div>
                                        @endif
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="tab-pane" id="timesheets">
                    <h6 class="fw-bold mb-3">My Tasks</h6>
                
                    @if($tasks->count())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Task</th>
                                        <th>Project</th>
                                        <th>Due Date</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr>
                                            <td class="fw-semibold">
                                                {{ $task->task }}
                                            </td>
                        
                                            <td>
                                                {{ $task->projects }}
                                            </td>
                        
                                            <td>
                                                {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}
                                            </td>
                        
                                            <td>
                                                <span class="badge 
                                                    @if($task->priority == 'high') bg-danger
                                                    @elseif($task->priority == 'medium') bg-warning
                                                    @else bg-secondary
                                                    @endif">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            </td>
                        
                                            <td>
                                                <span class="badge 
                                                    @if($task->status == 'completed') bg-success
                                                    @elseif($task->status == 'pending') bg-warning
                                                    @else bg-secondary
                                                    @endif">
                                                    {{ ucfirst($task->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No tasks assigned.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Add AJAX for break status updates
    function updateBreakStatus() {
        fetch('{{ route("break-status") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update break status elements
                    if (document.querySelector('.break-status')) {
                        console.log('Break status updated:', data);
                    }
                }
            })
            .catch(error => console.error('Error updating break status:', error));
    }

    // Update break status every 10 seconds
    setInterval(updateBreakStatus, 10000);
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('#employeeTabs li');
        const panes = document.querySelectorAll('.tab-pane');
    
        tabs.forEach(tab => {
            tab.addEventListener('click', function () {
                // Remove active from all tabs
                tabs.forEach(t => t.classList.remove('active'));
                // Hide all panes
                panes.forEach(p => p.classList.remove('active'));
                // Activate clicked tab
                this.classList.add('active');
                // Show matching pane
                const target = this.getAttribute('data-tab');
                const pane = document.getElementById(target);
                if (pane) {
                    pane.classList.add('active');
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const punchInInput = document.getElementById('punchInTime');
        const timerEl = document.getElementById('liveWorkTimer');

        if (!punchInInput || !timerEl) return;

        const punchInTimestamp = parseInt(punchInInput.value) * 1000;
        
        // Get break sessions from PHP and pass to JavaScript
        const breakSessions = @json($todayAttendance->last() ? json_decode($todayAttendance->last()->break_sessions ?? '[]', true) : []);
        
        function calculateWorkingTime() {
            const now = new Date().getTime();
            let totalElapsed = now - punchInTimestamp; // Total time since punch in
            
            // Subtract all completed break durations
            let totalBreakTime = 0;
            
            breakSessions.forEach(session => {
                if (session.end_time) {
                    // Completed break
                    const breakStart = new Date(session.start_time).getTime();
                    const breakEnd = new Date(session.end_time).getTime();
                    totalBreakTime += (breakEnd - breakStart);
                } else {
                    // Active break - subtract time until now
                    const breakStart = new Date(session.start_time).getTime();
                    totalBreakTime += (now - breakStart);
                }
            });
            
            // Working time = total elapsed - break time
            return Math.max(0, totalElapsed - totalBreakTime);
        }

        function updateTimer() {
            const workingTime = calculateWorkingTime();
            
            let hours = Math.floor(workingTime / (1000 * 60 * 60));
            let minutes = Math.floor((workingTime / (1000 * 60)) % 60);
            let seconds = Math.floor((workingTime / 1000) % 60);

            hours = hours.toString().padStart(2, '0');
            minutes = minutes.toString().padStart(2, '0');
            seconds = seconds.toString().padStart(2, '0');

            timerEl.innerText = `${hours}:${minutes}:${seconds}`;
        }

        updateTimer();               // run once immediately
        setInterval(updateTimer, 1000); // update every second
    });
</script>

@endsection