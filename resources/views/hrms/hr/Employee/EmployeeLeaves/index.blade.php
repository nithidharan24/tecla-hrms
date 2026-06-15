@php
$userRole = Session::get('role');
$userId = Session::get('user_id');
$adminId = Session::get('admin_id');

$modules = [];

if ($userRole === 'employee' && $userId) {
    $modules = DB::table('employee_module_access')
        ->where('employee_id', $userId)
        ->pluck('module_name')
        ->toArray();

} elseif ($userRole === 'admin' && $adminId) {
    $modules = DB::table('admin_module_access')
        ->where('admin_id', $adminId)
        ->pluck('module_name')
        ->toArray();
}
@endphp
@extends('layouts.index')

@section('content')

@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Employee Leaves');
    $permissionsHolidays = App\Helpers\PermissionHelper::getPermissions('Settings');
  
    $year = request('year', date('Y'));
@endphp

<div class="content container-fluid mt-3">

    <!-- ==============================================
         MAIN LEAVE TABS (Zoho Style)
    ================================================= -->
    <ul class="nav leave-tabs mb-0" role="tablist">
        <li class="nav-item">
            <a class="nav-link " data-bs-toggle="tab" href="#leave-summary">Leave Summary</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#leave-balance">Leave Balance</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#leave-requests">Leave Requests</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#shift">Shift</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#holidays">Holidays</a>
        </li>
       
    </ul>

    <div class="tabs-underline"></div>
   
    <!-- ==============================================
         TAB CONTENT
    ================================================= -->
    <div class="tab-content pt-4">

        <!-- ===================== LEAVE SUMMARY ===================== -->
        <div class="tab-pane fade show active" id="leave-summary">
            <div class="card p-3">
                <h5>Leave Summary</h5>
                <div class="tab-pane fade show active" id="leave-summary">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <span>Leave booked this year : </span>
                            <strong>{{ $leaves->sum('no_of_days') }} day(s)</strong>
                            <span class="ms-3"> | </span>
                            <span class="ms-2">Absent : <strong>0</strong></span>
                        </div>
                    
                        <div class="zs-date-filter d-flex align-items-center gap-2">
                            <button class="btn btn-outline-secondary" id="prevYearBtn">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>
                        
                            <div class="zs-date-box" id="yearRangeBox">
                                01-Jan-{{ $year }} - 31-Dec-{{ $year }}
                            </div>
                        
                            <button class="btn btn-outline-secondary" id="nextYearBtn">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        
                            <button class="btn btn-outline-primary" id="yearPickerBtn">
                                <i class="fa-regular fa-calendar"></i>
                            </button>
                        
                        
                            <input type="hidden" id="summaryYear" value="{{ $year }}">
                            @if(in_array('Team Leaves', $modules) || in_array('Team Leaves', $modules))
                            <!-- BUTTON ON RIGHT -->
    <a class="btn btn-primary ms-3" href="{{ route('team-leaves.index') }}">
        Team
    </a>
    @endif
                        </div>
                        
                    </div>
                    
                    <div class="row g-4 mt-3 leave-summary-row">
                        @foreach($leaveBalance as $type => $data)
                        <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                            <div class="zs-card">
                                <div class="zs-icon">
                                    @if($type == "Casual Leave") <i class="fa-solid fa-sun"></i>
                                    @elseif($type == "Sick Leave") <i class="fa-solid fa-stethoscope"></i>
                                    @elseif($type == "Hospitalisation") <i class="fa-solid fa-hospital"></i>
                                    @elseif($type == "Maternity Leave") <i class="fa-solid fa-baby-carriage"></i>
                                    @elseif($type == "Paternity Leave") <i class="fa-solid fa-baby"></i>
                                    @else <i class="fa-solid fa-calendar"></i>
                                    @endif
                                </div>
                    
                                <div class="zs-title">{{ $type }}</div>
                    
                                <div class="zs-stats">

                                    <div class="zs-line">
                                        <span>Paid</span>
                                        <strong>{{ $data['paid'] }}</strong>
                                    </div>
                                
                                    <div class="zs-line">
                                        <span>Unpaid (LOP)</span>
                                        <strong>{{ $data['lop'] }}</strong>
                                    </div>
                                
                                    <div class="zs-line">
                                        <span>Remaining</span>
                                        <strong>{{ $data['remaining'] }}</strong>
                                    </div>
                                    
                                </div>
                                
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    {{-- UPCOMING LEAVE & HOLIDAYS --}}
                    <div class="zs-section mt-4">
                        <button class="zs-dropdown-btn">
                            Upcoming Leave & Holidays
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                
                        <div class="zs-list">
                            @php $count = 0; @endphp
                
                            {{-- UPCOMING LEAVES --}}
                            @foreach($upcoming_leaves as $leave)
                                @php $count++; @endphp
                                <div class="zs-list-item">
                                    <strong>{{ \Carbon\Carbon::parse($leave->from_date)->format('d-M-Y, l') }}</strong>
                                    <span class="zs-tag">{{ $leave->leave_type }}</span>
                                    <span class="text-muted">• {{ $leave->no_of_days }} day</span>
                                </div>
                            @endforeach
                
                            {{-- UPCOMING HOLIDAYS --}}
                            @foreach($upcoming_holidays as $holiday)
                                @php $count++; @endphp
                                <div class="zs-list-item">
                                    <strong>{{ \Carbon\Carbon::parse($holiday->holidaydate)->format('d-M-Y, l') }}</strong>
                                    <span class="zs-holiday-tag">{{ $holiday->title }}</span>
                                </div>
                            @endforeach
                
                            @if($count == 0)
                            <div class="zs-empty">
                                <img src="/images/no-data.svg" width="120">
                                <p>No Data Found</p>
                            </div>
                            @endif
                        </div>
                    </div>
                
                    {{-- PAST LEAVE & HOLIDAYS --}}
                    <div class="zs-section mt-4">
                        <button class="zs-dropdown-btn">
                            Past Leave & Holidays
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                
                        <div class="zs-list">
                            {{-- PAST LEAVES --}}
                            @foreach($past_leaves as $leave)
                                <div class="zs-list-item">
                                    <strong>{{ \Carbon\Carbon::parse($leave->from_date)->format('d-M-Y, l') }}</strong>
                                    <span class="zs-tag">{{ $leave->leave_type }}</span>
                                    <span class="text-muted">• {{ $leave->no_of_days }} day</span>
                                </div>
                            @endforeach
                
                            {{-- PAST HOLIDAYS --}}
                            @foreach($past_holidays as $holiday)
                                <div class="zs-list-item">
                                    <strong>{{ \Carbon\Carbon::parse($holiday->holidaydate)->format('d-M-Y, l') }}</strong>
                                    <span class="zs-holiday-tag">{{ $holiday->title }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== LEAVE BALANCE ===================== -->
        <div class="tab-pane fade" id="leave-balance">
            @foreach($leaveBalance as $type => $data)
            <div class="lb-card">
                <div class="lb-left">
                    <div class="lb-icon" style="
                        background: {{ $loop->iteration % 6 == 1 ? '#e6f0ff' : ($loop->iteration % 6 == 2 ? '#eef9e6' : ($loop->iteration % 6 == 3 ? '#ffecec' : ($loop->iteration % 6 == 4 ? '#fff4e6' : ($loop->iteration % 6 == 5 ? '#f7e6ff' : '#e8ffe6')))) }};
                    ">
                        @if($type == 'Casual Leave')
                            <i class="fa-solid fa-sun"></i>
                        @elseif($type == 'Earned Leave')
                            <i class="fa-solid fa-stopwatch"></i>
                        @elseif($type == 'Leave Without Pay')
                            <i class="fa-solid fa-umbrella-beach"></i>
                        @elseif($type == 'Paternity Leave')
                            <i class="fa-solid fa-baby"></i>
                        @elseif($type == 'Sabbatical Leave')
                            <i class="fa-solid fa-rotate"></i>
                        @elseif($type == 'Sick Leave')
                            <i class="fa-solid fa-stethoscope"></i>
                        @else
                            <i class="fa-solid fa-calendar"></i>
                        @endif
                    </div>
        
                    <div class="lb-title">{{ $type }}</div>
                </div>
                <div class="lb-right">

                    <div class="lb-item">
                        <span class="lb-label">Paid</span>
                        <span class="lb-green">{{ $data['paid'] }} days</span>
                    </div>
                
                    <div class="lb-item">
                        <span class="lb-label">Unpaid (LOP)</span>
                        <span class="lb-green">{{ $data['lop'] }} days</span>
                    </div>
                
                    <div class="lb-item">
                        <span class="lb-label">Remaining</span>
                        <span class="lb-green">{{ $data['remaining'] }} days</span>
                    </div>
                
                </div>
                
            </div>
            @endforeach
        </div>
        
        <!-- ===================== LEAVE REQUESTS ===================== -->
        <div class="tab-pane fade" id="leave-requests">
            <!-- TOP BAR -->
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <!-- LEFT: Leave / Permission Switch -->
                <select id="requestTypeSelect" class="form-select request-type-select">
                    <option value="leave" selected>Leave</option>
                    <option value="permission">Permission</option>
                </select>

                <!-- RIGHT: Filter + Add Request -->
                <div class="d-flex align-items-center gap-2">
                    <!-- FILTER BUTTON -->
                    <button type="button" id="filterToggle" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-filter"></i>
                    </button>
                    @if(isset($permissions) && $permissions->can_create)
                    <!-- Change this line in your HTML -->
<button type="button"
        class="btn btn-primary btn-sm"
        id="addRequestBtn"
        data-leave-url="{{ isset($permissions) && $permissions->can_create ? route('employee-leaves.create') : '' }}"
        data-permission-url="{{ isset($permissions) && $permissions->can_create ? route('employee-permissions.create') : '' }}">
    Apply Leave
</button>
                    @endif
                </div>
            </div>

            <!-- ===================== TABLE AREA ===================== -->
            <div class="card-body p-0">
                <!-- ===== LEAVE TABLE ===== -->
                <div id="leaveRequestsWrapper" class="p-3">
                    <div class="table-responsive">
                        <table class="table custom-table datatable">   
                            <thead>   
                                <tr>
                                    <th style="width: 40px"><input type="checkbox"></th>
                                    <th>Leave Type</th>
                                    <th>Type</th>
                                    <th>Leave Period</th>
                                    <th>Days/Hrs Taken</th>
                                    <th>Date of Request</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaves as $leave)
                                    <tr>
                                        <td><input type="checkbox"></td>
                                        <td>{{ $leave->leave_type }}</td>
                                        <td>{{ ucfirst($leave->type ?? 'Paid') }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($leave->from_date)->format('d-M-Y') }}
                                            -
                                            {{ \Carbon\Carbon::parse($leave->to_date)->format('d-M-Y') }}
                                        </td>
                                        <td>{{ $leave->no_of_days }} Day(s)</td>
                                        <td>{{ \Carbon\Carbon::parse($leave->created_at)->format('d-M-Y') }}</td>
                                        <td>{{ $leave->status }}</td>
                                        <td data-label="Actions" class="text-end">
                                            <div class="od-inline-actions">
                                                @if(isset($permissions) && $permissions->can_edit)
                                                <a href="{{ route('employee-leaves.edit', $leave->id) }}" 
                                                   class="od-icon-btn text-primary" 
                                                   title="Edit">
                                                    <i class="fa-solid fa-pencil"></i>
                                                </a>
                                                @endif
                                        
                                                @if(isset($permissions) && $permissions->can_delete)
                                                <button class="od-icon-btn danger" title="Delete" 
                                                        onclick="confirmDelete({{ $leave->id }}, 'leave')">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            No Leave Requests Found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ===== PERMISSION TABLE ===== -->
                <div id="permissionRequestsWrapper" class="p-3 d-none">
                    <div class="table-responsive">
                        <table class="table custom-table datatable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox"></th>
                                    <th>Date</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Duration</th>
                                    <th>Date of Request</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employeePermissions as $permission)
                                    <tr>
                                        <td><input type="checkbox"></td>
                                        <td>{{ \Carbon\Carbon::parse($permission->permission_date)->format('d-M-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($permission->start_time)->format('h:i A') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($permission->end_time)->format('h:i A') }}</td>
                                        <td>{{ $permission->duration }} hrs</td>
                                        <td>{{ \Carbon\Carbon::parse($permission->created_at)->format('d-M-Y') }}</td>
                                        <td data-label="Actions" class="text-end">
                                            <div class="od-inline-actions">
                                                @if(isset($permissions) && $permissions->can_edit)
                                                <a href="{{ route('employee-permissions.edit', $permission->id) }}" 
                                                   class="od-icon-btn text-primary" 
                                                   title="Edit">
                                                    <i class="fa-solid fa-pencil"></i>
                                                </a>
                                                @endif
                                                @if(isset($permissions) && $permissions->can_delete)
                                                <button class="od-icon-btn danger" title="Delete" 
                                                        onclick="confirmDelete({{ $permission->id }}, 'permission')">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No Permission Requests Found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== SHIFT ===================== -->
        <div class="tab-pane fade" id="shift">
            <div class="card p-3">
                <h5>Shift</h5>
                <div class="card p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <h5><strong>{{ $weekStart->format('d M Y') }} - {{ $weekEnd->format('d M Y') }}</strong></h5>
                
                        <div>
                            <button id="weeklyBtn" class="btn btn-primary btn-sm">Weekly</button>
                            <button id="monthlyBtn" class="btn btn-outline-primary btn-sm">Monthly</button>
                            <button class="btn btn-danger btn-sm">Assign Shift</button>
                        </div>
                    </div>
                    
                    <div id="weeklyView">
                        <table class="table table-bordered shift-table">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    @for ($hour = 8; $hour <= 19; $hour++)
                                        <th>{{ date('h A', strtotime("$hour:00")) }}</th>
                                    @endfor
                                </tr>
                            </thead>
                    
                            <tbody>
                                @for ($d = 0; $d < 7; $d++)
                                    @php
                                        $dateObj = $weekStart->copy()->addDays($d);
                                        $dateStr = $dateObj->format('Y-m-d');
                                        $dayShort = $dateObj->format('D');
                    
                                        $shiftToday = collect($calendarShifts)->firstWhere('date', $dateStr);
                                        $leaveToday = $leaveDays[$dateStr] ?? null;
                                        $isWeekend = !$shiftToday && !$leaveToday;
                                    @endphp
                    
                                    <tr>
                                        <td>
                                            <strong>{{ $dayShort }}</strong><br>
                                            {{ $dateObj->format('d M') }}
                                        </td>
                    
                                        @for ($hour = 8; $hour <= 19; $hour++)
                                            <td class="position-relative">
                                                {{-- WEEKEND BLOCK --}}
                                                @if ($isWeekend && $hour == 8)
                                                    <div class="shift-block weekend">
                                                        Weekend
                                                    </div>
                                                @endif
                    
                                                {{-- LEAVE BLOCK --}}
                                                @if ($leaveToday && $hour == 8)
                                                    <div class="shift-block leave-block">
                                                        Leave: {{ $leaveToday }}
                                                    </div>
                                                @endif
                                                
                                                {{-- HOLIDAY BLOCK --}}
                                                @if (isset($holidayDays[$dateStr]) && $hour == 8)
                                                    <div class="shift-block holiday-block">
                                                        Holiday: {{ $holidayDays[$dateStr] }}
                                                    </div>
                                                @endif
                    
                                                @if ($shiftToday)
                                                    @php
                                                        $start = \Carbon\Carbon::parse($shiftToday['start_time']);
                                                        $end   = \Carbon\Carbon::parse($shiftToday['end_time']);
                                                
                                                        $durationHours = ceil($start->diffInMinutes($end) / 60);
                                                        $minuteOffset = ($start->minute / 60) * 100;
                                                    @endphp
                                                
                                                    @if ($hour == $start->hour)
                                                        <div class="shift-block shift-theme"
                                                             style="
                                                                margin-left: {{ $minuteOffset }}%;
                                                                width: calc({{ $durationHours }} * 100%);
                                                             ">
                                                            <strong>{{ $shiftToday['shift_name'] }}</strong><br>
                                                            {{ $start->format('g:i A') }} - {{ $end->format('g:i A') }}
                                                        </div>
                                                    @endif
                                                @endif
                                            </td>
                                        @endfor
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>

                    <div id="monthlyView" class="d-none">
                        @php
                            $monthStart = now()->startOfMonth();
                            $monthEnd   = now()->endOfMonth();
                            $startGrid  = $monthStart->copy()->startOfWeek();
                            $endGrid    = $monthEnd->copy()->endOfWeek();
                            $currentMonth = now()->format('F Y');
                        @endphp
                    
                        <h5 class="mb-3"><strong>{{ $currentMonth }}</strong></h5>
                    
                        <table class="table table-bordered monthly-table">
                            <thead>
                                <tr>
                                    <th>Mon</th>
                                    <th>Tue</th>
                                    <th>Wed</th>
                                    <th>Thu</th>
                                    <th>Fri</th>
                                    <th class="weekend-col">Sat</th>
                                    <th class="weekend-col">Sun</th>
                                </tr>
                            </thead>
                    
                            <tbody>
                                @php $date = $startGrid->copy(); @endphp
                    
                                @while ($date <= $endGrid)
                                    <tr>
                                        @for ($i = 0; $i < 7; $i++)
                                            @php
                                                $dateStr = $date->format('Y-m-d');
                                                $shiftToday = collect($calendarShifts)->firstWhere('date', $dateStr);
                                                $leaveToday = $leaveDays[$dateStr] ?? null;
                                            @endphp
                    
                                            <td class="{{ $date->month != now()->month ? 'bg-light' : '' }} 
                                                       {{ $date->isWeekend() ? 'weekend-cell' : '' }}">
                    
                                                <div class="month-date">{{ $date->format('j') }}</div>
                    
                                                {{-- SHIFT --}}
                                                @if ($shiftToday)
                                                    <div class="month-shift">
                                                        <strong>{{ $shiftToday['shift_name'] }}</strong><br>
                                                        {{ date("g:i A", strtotime($shiftToday['start_time'])) }} -
                                                        {{ date("g:i A", strtotime($shiftToday['end_time'])) }}
                                                    </div>
                                                @endif
                    
                                                {{-- LEAVE --}}
                                                @if ($leaveToday)
                                                    <div class="month-leave">
                                                        Leave: {{ $leaveToday }}
                                                    </div>
                                                @endif
                                                
                                                {{-- HOLIDAY --}}
                                                @if (isset($holidayDays[$dateStr]))
                                                    <div class="month-holiday">
                                                        {{ $holidayDays[$dateStr] }}
                                                    </div>
                                                @endif
                                            </td>
                    
                                            @php $date->addDay(); @endphp
                                        @endfor
                                    </tr>
                                @endwhile
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== HOLIDAYS ===================== -->
        <div class="tab-pane fade" id="holidays">
            <div class="d-flex align-items-center gap-2 mb-3">
               <!-- Add Holiday Button -->
               @if(isset($permissionsHolidays) && $permissionsHolidays->can_create)
               <a href="#" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addHolidayModal">
                    Add Holiday
               </a>
               @endif
            </div>
           
            <div class="card p-3">
                <h5>Holiday Calendar</h5>
                
                <div class="table-responsive">
                    <table class="table custom-table datatable">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Holiday Title</th>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                
                            @forelse($holidays as $holiday)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $holiday->title }}</td>
                                    <td>{{ \Carbon\Carbon::parse($holiday->holidaydate)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($holiday->holidaydate)->format('l') }}</td>
                                    <td data-label="Actions" class="text-center">
                                        <div class="od-inline-actions">
                                            @if(isset($permissionsHolidays) && $permissionsHolidays->can_edit)
                                            <button class="od-icon-btn" title="Edit" onclick="editHoliday({{ $holiday->id }})">
                                                <i class="fa-solid fa-pencil"></i>
                                            </button>
                                            @endif
                                    
                                            @if(isset($permissionsHolidays) && $permissionsHolidays->can_delete)
                                            <button class="od-icon-btn danger" title="Delete" onclick="confirmDeleteHoliday({{ $holiday->id }})">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No Holidays Found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     DELETE CONFIRMATION MODAL
=============================================================== -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0">
                <h5 class="modal-title w-100 text-center" id="confirmationModalLabel" style="font-weight: bold;">Delete Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                Are you sure you want to delete this record?
            </div>
            <div class="modal-footer d-flex justify-content-around border-0">
                <button type="button" class="btn btn-outline-warning btn-lg" data-bs-dismiss="modal" style="border-radius: 50px; width: 150px;">Cancel</button>
                <button type="button" class="btn btn-danger btn-lg" id="confirmDeleteBtn" style="border-radius: 50px; width: 150px;">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     ADD/EDIT HOLIDAY MODAL
=============================================================== -->
<div class="modal fade" id="addHolidayModal" tabindex="-1" role="dialog" aria-labelledby="addHolidayModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addHolidayModalLabel">Add Holiday</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    style="background-color: orange; border-radius: 50%; width: 30px; height: 30px; color: white; font-size: 16px; line-height: 30px; text-align: center;">
                    &times;
                </button>
            </div>
            <div class="modal-body">
                <form id="holidayForm" action="{{ route('holidays.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="holiday-id" name="holiday_id">
                    <div class="form-group mb-3">
                        <label for="holiday-title">Holiday Title *</label>
                        <input type="text" class="form-control" id="holiday-title" name="holiday_title" required>
                        <div id="title-warning" class="alert alert-warning mt-2" style="display: none;">Holiday title already exists!</div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="holiday-date">Holiday Date *</label>
                        <input type="date" class="form-control" id="holiday-date" name="holiday_date" required>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     FILTER DRAWER (Zoho Style)
=============================================================== -->
<div id="filterOverlay" class="filter-overlay d-none"></div>

<div id="filterPanel" class="filter-drawer">
    <div class="filter-header d-flex justify-content-between align-items-center">
        <h5>Filter</h5>
        <button id="filterClose" class="btn-close"></button>
    </div>

    <div class="filter-body">
        <label class="form-label">Period</label>
        <select class="form-select mb-3" name="period" id="filterPeriod">
            <option value="this_year">This Year</option>
            <option value="last_year">Last Year</option>
            <option value="custom">Custom</option>
        </select>
        
        <input type="date" class="form-control mb-3" id="filterFrom" name="from_date">
        <input type="date" class="form-control mb-3" id="filterTo" name="to_date">
        
        <select class="form-select mb-3" id="filterPaidType" name="paid_type">
            <option value="all">All</option>
            <option value="paid">Paid</option>
            <option value="unpaid">Unpaid</option>
        </select>
        
        <select class="form-select" id="filterLeaveType" name="leave_type">
            <option value="all">All Leave Types</option>
            <option value="Casual Leave">Casual Leave</option>
            <option value="Sick">Sick</option>
            <option value="Hospitalisation">Hospitalisation</option>
            <option value="Maternity Leave">Maternity Leave</option>
            <option value="Paternity Leave">Paternity Leave</option>
            <option value="LOP">LOP</option>
        </select>
        
    </div>

    <div class="filter-footer d-flex justify-content-between">
        <button class="btn btn-outline-secondary btn-sm">Reset</button>
        <button class="btn btn-primary btn-sm">Apply</button>
    </div>
</div>

<!-- ============================================================
     CSS
=============================================================== -->
<style>

    .tab-content {
        visibility: hidden;
    }


.leave-tabs .nav-link {
    font-size: 15px;
    font-weight: 500;
    color: #333;
    margin-top: -25px;
    border-bottom: 3px solid transparent;
}

.leave-tabs .nav-link.active {
    color: #f97316;
    border-bottom: 3px solid #f97316;
}

.tabs-underline {
    width: 100%;
    height: 2px;
    background: #e5eaf2;
    margin-top: -4px;
    margin-bottom: 12px;
}

.request-type-select {
    width: 170px;
    border-radius: 6px;
}

.custom-leave-table thead {
    background: #f5f7fb;
}

.custom-leave-table th {
    font-size: 13px;
    font-weight: 600;
}

.custom-leave-table td {
    font-size: 13px;
    vertical-align: middle;
}

.filter-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.3);
    z-index: 1040;
}

.filter-drawer {
    position: fixed;
    right: 0;
    top: 0;
    width: 330px;
    height: 100%;
    background: #fff;
    transform: translateX(100%);
    transition: 0.35s;
    z-index: 1050;
    display: flex;
    flex-direction: column;
}

.filter-drawer.open {
    transform: translateX(0);
}

.filter-header, .filter-footer {
    padding: 15px;
    background: #fafbff;
}

.filter-body {
    padding: 15px;
    flex: 1;
    overflow-y: auto;
}

/* COMPACT Leave Balance Card */
.lb-card {
    width: 100%;
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e6e8ec;
    padding: 10px 12px;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: 0.25s;
    min-height: 65px;
}

.lb-card:hover {
    border-color: #d4e2ff;
}

/* LEFT Section */
.lb-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.lb-icon {
    background: #ff7f2a !important;
    color: #ffffff !important;
    width: 38px;
    height: 38px;
    border-radius: 8px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 18px;
}

.lb-icon i {
    color: #ffffff !important;
}

.lb-title {
    font-size: 15px;
    font-weight: 600;
}

/* RIGHT Section */
.lb-right {
    display: flex;
    align-items: center;
    gap: 25px;
}

.lb-item {
    text-align: right;
}

.lb-label {
    display: block;
    font-size: 11px;
    color: #666;
}

.lb-green {
    color: #0a8e40;
    font-weight: 600;
    font-size: 13px;
}

.lb-dark {
    color: #333;
    font-weight: 600;
    font-size: 13px;
}

/* Apply Button */
.lb-btn {
    background: #fff;
    border: 1px solid #d6d6d6;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    cursor: pointer;
}

.lb-btn:hover {
    background: #f3f6ff;
    border-color: #c5d3ff;
}

.leave-summary-row {
    justify-content: flex-start !important;
    margin-left: 150px;
}

/* CARD */
.zs-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e6e8ec;
    padding: 18px 10px;
    text-align: center;
    transition: 0.2s;
    height: 170px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.zs-card:hover {
    border-color: #ff7f2a;
}

/* ICON BOX */
.zs-icon {
    background: #ff7f2a;
    color: #fff;
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 22px;
    margin-bottom: 10px;
}

.zs-icon i {
    color: #ffffff !important;
}

/* TITLE */
.zs-title {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 8px;
}

/* STATS */
.zs-stats {
    width: 100%;
}

.zs-line {
    display: flex;
    justify-content: space-between;
    padding: 0 10px;
    font-size: 13px;
    margin-bottom: 4px;
}

.zs-line strong {
    color: #0a8e40;
}

.zs-date-box {
    padding: 8px 14px;
    background: #fff;
    border-radius: 6px;
    border: 1px solid #dce1e8;
    font-size: 14px;
}

.zs-section {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e7ebf1;
    padding: 15px;
}

.zs-dropdown-btn {
    background: #f4f6fa;
    padding: 10px 14px;
    border-radius: 8px;
    width: 100%;
    text-align: left;
    font-size: 14px;
    font-weight: 600;
    border: none;
}

.zs-list {
    padding: 12px 10px;
}

.zs-list-item {
    padding: 12px 10px;
    border-bottom: 1px solid #f1f3f7;
    display: flex;
    align-items: center;
    gap: 15px;
}

.zs-tag {
    background: #dfe7ff;
    padding: 3px 7px;
    border-radius: 6px;
    font-size: 12px;
    color: #004aad;
}

.zs-holiday-tag {
    background: #ffe0b3;
    padding: 3px 7px;
    border-radius: 6px;
    font-size: 12px;
    color: #9a5e00;
}

.zs-empty {
    text-align: center;
    padding: 30px;
    color: #777;
}

.shift-table td, .shift-table th {
    padding: 0 !important;
    height: 65px;
    font-size: 13px;
}

.shift-block {
    position: absolute;
    top:0;
    left:0;
    height: 64px;
    padding: 8px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    display: flex;
    align-items: center;
    flex-direction: column;
    justify-content: center;
}

.shift-theme {
    background: #ffddaa;
    border-left: 4px solid #ff7b00;
    position: absolute;
    top: 0;
    height: 64px;
    padding: 6px;
    border-radius: 8px;
    box-sizing: border-box;
    z-index: 10;
}

/* Leave Color */
.leave-block {
    background: #ffd1d1;
    border-left: 4px solid #d9534f;
}

/* Weekend Color */
.weekend {
    background: #e2e8f0;
    border-left: 4px solid #64748b;
    color: #333;
}

.monthly-table td {
    height: 110px;
    vertical-align: top;
    padding: 4px !important;
    position: relative;
    font-size: 12px;
}

.month-date {
    font-weight: 700;
    font-size: 13px;
    margin-bottom: 3px;
}

.month-shift {
    background: #ffddaa;
    border-left: 4px solid #ff7b00;
    padding: 3px 5px;
    border-radius: 6px;
    font-size: 11px;
    margin-bottom: 3px;
}

.month-leave {
    background: #ffd1d1;
    border-left: 4px solid #d9534f;
    padding: 3px 5px;
    border-radius: 6px;
    font-size: 11px;
}

.weekend-cell {
    background: #f1f5f9 !important;
}

.weekend-col {
    background: #f8fafc !important;
}

.holiday-block {
    background: #c5e8ff;
    border-left: 4px solid #008cd6;
    color: #004a77;
}

.month-holiday {
    background: #c5e8ff;
    border-left: 4px solid #0077c8;
    padding: 3px 5px;
    border-radius: 6px;
    font-size: 11px;
    margin-top: 3px;
}

/* Modal center */
.modal-dialog {
    display: flex;
    align-items: center;
    min-height: calc(100% - 1rem);
    margin: 1rem auto;
}
</style>

<!-- ============================================================
     JAVASCRIPT
=============================================================== -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Switch tables based on dropdown
    const typeSelect = document.getElementById("requestTypeSelect");
    const leaveWrapper = document.getElementById("leaveRequestsWrapper");
    const permWrapper = document.getElementById("permissionRequestsWrapper");

    if (typeSelect && leaveWrapper && permWrapper) {
        typeSelect.addEventListener("change", function () {
            if (this.value === "permission") {
                leaveWrapper.classList.add("d-none");
                permWrapper.classList.remove("d-none");
            } else {
                permWrapper.classList.add("d-none");
                leaveWrapper.classList.remove("d-none");
            }
        });
    }

    // Add Request button
    // Add Request button
const addBtn = document.getElementById("addRequestBtn");
if (addBtn) {
    addBtn.addEventListener("click", () => {
        const type = typeSelect.value;

        if (type === "leave") {
            const url = addBtn.dataset.leaveUrl;
            if (url) {
                window.location.href = url;
            } else {
                alert("No access to create leave");
            }
        } else {
            // Changed to use permission URL
            const url = addBtn.dataset.permissionUrl;
            if (url) {
                window.location.href = url;
            } else {
                alert("No access to create permission");
            }
        }
    });
}

    // Filter drawer
    const filterToggle = document.getElementById("filterToggle");
    const filterPanel = document.getElementById("filterPanel");
    const filterOverlay = document.getElementById("filterOverlay");
    const filterClose = document.getElementById("filterClose");

    function openFilter() {
        filterPanel.classList.add("open");
        filterOverlay.classList.remove("d-none");
    }

    function closeFilter() {
        filterPanel.classList.remove("open");
        filterOverlay.classList.add("d-none");
    }

    if (filterToggle) filterToggle.addEventListener("click", openFilter);
    if (filterClose) filterClose.addEventListener("click", closeFilter);
    if (filterOverlay) filterOverlay.addEventListener("click", closeFilter);

    // Year navigation for summary
    const yearInput = document.getElementById("summaryYear");
    const yearRangeBox = document.getElementById("yearRangeBox");

    if (yearInput && yearRangeBox) {
        function updateDisplay() {
            const y = yearInput.value;
            yearRangeBox.innerHTML = `01-Jan-${y} - 31-Dec-${y}`;
        }

        // Prev Year
        const prevYearBtn = document.getElementById("prevYearBtn");
        if (prevYearBtn) {
            prevYearBtn.addEventListener("click", function () {
                yearInput.value = parseInt(yearInput.value) - 1;
                updateDisplay();
                reloadSummary();
            });
        }

        // Next Year
        const nextYearBtn = document.getElementById("nextYearBtn");
        if (nextYearBtn) {
            nextYearBtn.addEventListener("click", function () {
                yearInput.value = parseInt(yearInput.value) + 1;
                updateDisplay();
                reloadSummary();
            });
        }

        // Calendar Picker
        const yearPickerBtn = document.getElementById("yearPickerBtn");
        if (yearPickerBtn) {
            yearPickerBtn.addEventListener("click", function () {
                let y = prompt("Enter year (YYYY):", yearInput.value);
                if (y && !isNaN(y)) {
                    yearInput.value = y;
                    updateDisplay();
                    reloadSummary();
                }
            });
        }

        // Reload summary UI with selected year
        function reloadSummary() {
            window.location.href = `?year=${yearInput.value}`;
        }
    }

    // Shift view toggle
    const weeklyBtn = document.getElementById("weeklyBtn");
    const monthlyBtn = document.getElementById("monthlyBtn");
    const weeklyView = document.getElementById("weeklyView");
    const monthlyView = document.getElementById("monthlyView");

    if (weeklyBtn && monthlyBtn && weeklyView && monthlyView) {
        weeklyBtn.addEventListener("click", function() {
            weeklyView.classList.remove("d-none");
            monthlyView.classList.add("d-none");
            weeklyBtn.classList.add("btn-primary");
            weeklyBtn.classList.remove("btn-outline-primary");
            monthlyBtn.classList.remove("btn-primary");
            monthlyBtn.classList.add("btn-outline-primary");
        });

        monthlyBtn.addEventListener("click", function() {
            weeklyView.classList.add("d-none");
            monthlyView.classList.remove("d-none");
            weeklyBtn.classList.remove("btn-primary");
            weeklyBtn.classList.add("btn-outline-primary");
            monthlyBtn.classList.add("btn-primary");
            monthlyBtn.classList.remove("btn-outline-primary");
        });
    }
});

// Global delete functionality
let currentDeleteRecord = { id: null, type: null };

function confirmDelete(recordId, type) {
    currentDeleteRecord.id = recordId;
    currentDeleteRecord.type = type;
    
    const recordTypeText = type === 'permission' ? 'Permission' : 'Leave';
    document.getElementById('confirmationModalLabel').textContent = `Delete ${recordTypeText}`;
    
    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    confirmationModal.show();
}

// Initialize delete confirmation button
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (!currentDeleteRecord.id || !currentDeleteRecord.type) return;
    
    const baseUrl = currentDeleteRecord.type === 'permission'
        ? "{{ route('employee-leaves.index') }}"
        : "{{ route('employee-leaves.index') }}";
    
    const deleteUrl = `${baseUrl}/${currentDeleteRecord.id}`;
    
    // Create and submit form for DELETE request
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = deleteUrl;
    form.style.display = 'none';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    form.appendChild(methodInput);
    
    document.body.appendChild(form);
    form.submit();
});

// Holiday Functions
function confirmDeleteHoliday(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This holiday will be deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("holidays.destroy", ":id") }}'.replace(':id', id),
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                }
            });
        }
    });
}

function editHoliday(id) {
    $.get('{{ route("holidays.edit", ":id") }}'.replace(':id', id), function(holiday) {
        $('#holidayForm').attr('action', '{{ route("holidays.update", "") }}/' + id);
        $('#holiday-id').val(holiday.id);
        $('#holiday-title').val(holiday.title);
        $('#holiday-date').val(holiday.holidaydate);
        $('#addHolidayModalLabel').text('Edit Holiday');
        $('#submitBtn').text('Update');
        $('#addHolidayModal').modal('show');
        $('#holiday-title').trigger('input');
    });
}

// AJAX check for duplicate holiday title
$('#holiday-title').on('input', function() {
    let title = $(this).val();
    $('#title-warning').hide();
    $('#submitBtn').attr('disabled', false);

    if (title.length > 0) {
        const isEditMode = $('#holiday-id').val() !== '';
        $.ajax({
            url: '{{ route("holidays.check-title") }}',
            method: 'GET',
            data: { title: title, editMode: isEditMode, id: $('#holiday-id').val() },
            success: function(response) {
                if (response.exists && !isEditMode) {
                    $('#success-alert').hide();
                    $('#title-warning').text('Holiday title already exists!').show();
                    $('#submitBtn').attr('disabled', true);
                } else {
                    $('#title-warning').hide();
                    $('#submitBtn').attr('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    } else {
        $('#title-warning').hide();
        $('#submitBtn').attr('disabled', false);
    }
});

// Reset modal for adding new holiday
$('#addHolidayModal').on('hidden.bs.modal', function () {
    $('#holidayForm').attr('action', '{{ route('holidays.store') }}');
    $('#addHolidayModalLabel').text('Add Holiday');
    $('#submitBtn').text('Submit');
    $('#holidayForm')[0].reset();
    $('#holiday-id').val('');
    $('#title-warning').hide();
    $('#submitBtn').attr('disabled', false);
});

// Close modal with X button
$('.close').on('click', function () {
    $('#addHolidayModal').modal('hide');
});

// Hide alerts after 2 seconds
$("#success-alert").fadeTo(2000, 500).slideUp(500, function() {
    $("#success-alert").slideUp(500);
});

if ($("#holiday-date-alert").length) {
    $("#holiday-date-alert").fadeTo(2000, 500).slideUp(500, function() {
        $("#holiday-date-alert").slideUp(500);
    });
}
document.addEventListener("DOMContentLoaded", function () {

let lastTab = localStorage.getItem("employee_last_active_tab") || "#leave-summary";
let tabTrigger = document.querySelector(`a[href="${lastTab}"]`);

if (tabTrigger) {
    new bootstrap.Tab(tabTrigger).show();
}

// Save active tab
document.querySelectorAll('.leave-tabs .nav-link').forEach(function (tab) {
    tab.addEventListener('click', function () {
        localStorage.setItem("employee_last_active_tab", this.getAttribute("href"));
    });
});

// SHOW content only after correct tab is loaded
document.querySelector(".tab-content").style.visibility = "visible";
});
document.querySelector(".filter-footer .btn-primary").addEventListener("click", function () {
    let params = new URLSearchParams();

    params.append("period", document.getElementById("filterPeriod").value);
    params.append("from_date", document.getElementById("filterFrom").value);
    params.append("to_date", document.getElementById("filterTo").value);
    params.append("paid_type", document.getElementById("filterPaidType").value);
    params.append("leave_type", document.getElementById("filterLeaveType").value);

    // SAVE CURRENT TAB (Leave Requests)
    localStorage.setItem("employee_last_active_tab", "#leave-requests");

    window.location.href = "?" + params.toString();
});

// RESET FILTER
document.querySelector(".filter-footer .btn-outline-secondary").addEventListener("click", function () {
    window.location.href = "?reset=1";
});



</script>

@endsection