@php
$userRole = Session::get('role');
$userId   = Session::get('user_id');
$adminId  = Session::get('admin_id');
$modules  = [];
if ($userRole === 'employee' && $userId) {
    $modules = DB::table('employee_module_access')->where('employee_id', $userId)->pluck('module_name')->toArray();
} elseif ($userRole === 'admin' && $adminId) {
    $modules = DB::table('admin_module_access')->where('admin_id', $adminId)->pluck('module_name')->toArray();
}
@endphp
@extends('layouts.index')
@section('content')
@php
    $permissions          = App\Helpers\PermissionHelper::getPermissions('Employee Leaves');
    $permissionsHolidays  = App\Helpers\PermissionHelper::getPermissions('Settings');
    $year                 = request('year', date('Y'));

    // KPI totals
    $totalAllocated = array_sum(array_column($leaveBalance, 'allocated'));
    $totalUsed      = array_sum(array_column($leaveBalance, 'paid')) + array_sum(array_column($leaveBalance, 'lop'));
    $totalRemaining = array_sum(array_column($leaveBalance, 'remaining'));
    $pendingCount   = $leaves->where('status','pending')->count();
    $approvedCount  = $leaves->where('status','approved')->count();
    $declinedCount  = $leaves->whereIn('status',['declined','rejected'])->count();
    $usedPct        = $totalAllocated > 0 ? round($totalUsed / $totalAllocated * 100) : 0;
@endphp

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="lv-wrap">

{{-- ── FLASH MESSAGES ─────────────────────────────────────────────────────── --}}
@if(session('success'))
<div class="lv-alert lv-alert-success">
    <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
    <button onclick="this.parentElement.remove()" class="lv-alert-close">&times;</button>
</div>
@endif
@if(session('error'))
<div class="lv-alert lv-alert-danger">
    <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
    <button onclick="this.parentElement.remove()" class="lv-alert-close">&times;</button>
</div>
@endif

{{-- ── TOP BAR ─────────────────────────────────────────────────────────────── --}}
<div class="lv-topbar">
    <div>
        <h1 class="lv-page-title">Leave Management</h1>
        <p class="lv-page-sub">{{ date('Y') }} · {{ $year == date('Y') ? 'Current Year' : 'Year '.$year }}</p>
    </div>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        @if(in_array('Team Leaves', $modules))
        <a href="{{ route('team-leaves.index') }}" class="lv-btn lv-btn-outline">
            <i class="fa fa-users me-1"></i> Team Leaves
        </a>
        @endif
        @if(isset($permissions) && $permissions->can_create)
        <a href="{{ route('employee-leaves.create') }}" class="lv-btn lv-btn-primary">
            <i class="fa fa-plus me-1"></i> Apply Leave
        </a>
        @endif
    </div>
</div>

{{-- ── KPI CARDS ───────────────────────────────────────────────────────────── --}}
<div class="lv-kpi-grid">
    <div class="lv-kpi-card" style="--accent:#6366F1">
        <div class="lv-kpi-icon" style="background:#EEF2FF;color:#6366F1"><i class="fa fa-calendar-days"></i></div>
        <div class="lv-kpi-body">
            <div class="lv-kpi-val">{{ $totalAllocated }}</div>
            <div class="lv-kpi-label">Total Allocated</div>
            <div class="lv-kpi-sub">Days for {{ $year }}</div>
        </div>
    </div>
    <div class="lv-kpi-card" style="--accent:#F97316">
        <div class="lv-kpi-icon" style="background:#FFF7ED;color:#F97316"><i class="fa fa-clock-rotate-left"></i></div>
        <div class="lv-kpi-body">
            <div class="lv-kpi-val">{{ $totalUsed }}</div>
            <div class="lv-kpi-label">Days Used</div>
            <div class="lv-kpi-sub">{{ $usedPct }}% of allocation</div>
        </div>
    </div>
    <div class="lv-kpi-card" style="--accent:#10B981">
        <div class="lv-kpi-icon" style="background:#ECFDF5;color:#10B981"><i class="fa fa-circle-check"></i></div>
        <div class="lv-kpi-body">
            <div class="lv-kpi-val">{{ $totalRemaining }}</div>
            <div class="lv-kpi-label">Remaining</div>
            <div class="lv-kpi-sub">Days available</div>
        </div>
    </div>
    <div class="lv-kpi-card" style="--accent:#F59E0B">
        <div class="lv-kpi-icon" style="background:#FFFBEB;color:#F59E0B"><i class="fa fa-hourglass-half"></i></div>
        <div class="lv-kpi-body">
            <div class="lv-kpi-val">{{ $pendingCount }}</div>
            <div class="lv-kpi-label">Pending</div>
            <div class="lv-kpi-sub">Awaiting approval</div>
        </div>
    </div>
    <div class="lv-kpi-card" style="--accent:#22C55E">
        <div class="lv-kpi-icon" style="background:#F0FDF4;color:#22C55E"><i class="fa fa-thumbs-up"></i></div>
        <div class="lv-kpi-body">
            <div class="lv-kpi-val">{{ $approvedCount }}</div>
            <div class="lv-kpi-label">Approved</div>
            <div class="lv-kpi-sub">This year</div>
        </div>
    </div>
    <div class="lv-kpi-card" style="--accent:#EF4444">
        <div class="lv-kpi-icon" style="background:#FEF2F2;color:#EF4444"><i class="fa fa-thumbs-down"></i></div>
        <div class="lv-kpi-body">
            <div class="lv-kpi-val">{{ $declinedCount }}</div>
            <div class="lv-kpi-label">Declined</div>
            <div class="lv-kpi-sub">This year</div>
        </div>
    </div>
</div>

{{-- ── TABS ─────────────────────────────────────────────────────────────────── --}}
<div class="lv-tabs">
    <button class="lv-tab active" data-tab="summary">
        <i class="fa fa-chart-pie me-1"></i> Leave Summary
    </button>
    <button class="lv-tab" data-tab="balance">
        <i class="fa fa-scale-balanced me-1"></i> Leave Balance
    </button>
    <button class="lv-tab" data-tab="requests">
        <i class="fa fa-list-check me-1"></i> Leave Requests
        @if($pendingCount > 0)<span class="lv-badge-pill">{{ $pendingCount }}</span>@endif
    </button>
    <button class="lv-tab" data-tab="shift">
        <i class="fa fa-calendar-week me-1"></i> Shift
    </button>
    <button class="lv-tab" data-tab="holidays">
        <i class="fa fa-umbrella-beach me-1"></i> Holidays
    </button>
</div>

<div class="lv-tab-content">

{{-- ────────────────────── SUMMARY TAB ──────────────────────────────────────── --}}
<div class="lv-pane active" id="tab-summary">
    {{-- Year navigator --}}
    <div class="lv-year-bar">
        <button class="lv-icon-btn" id="prevYear"><i class="fa fa-chevron-left"></i></button>
        <span class="lv-year-label" id="yearLabel">{{ date('d M', mktime(0,0,0,1,1,$year)) }} {{ $year }} — {{ date('d M', mktime(0,0,0,12,31,$year)) }} {{ $year }}</span>
        <button class="lv-icon-btn" id="nextYear"><i class="fa fa-chevron-right"></i></button>
        <input type="hidden" id="currentYear" value="{{ $year }}">
        <span class="lv-year-stat ms-3">
            <strong>{{ $leaves->sum('no_of_days') }}</strong> days booked this year
            &nbsp;·&nbsp; Paid: <strong>{{ array_sum(array_column($leaveBalance,'paid')) }}</strong>
            &nbsp;·&nbsp; LOP: <strong>{{ array_sum(array_column($leaveBalance,'lop')) }}</strong>
        </span>
    </div>

    {{-- Leave type cards with progress bar --}}
    <div class="lv-balance-cards">
        @foreach($leaveBalance as $type => $data)
        @php
            $usedDays = $data['paid'] + $data['lop'];
            $alloc    = max($data['allocated'], 1);
            $pct      = min(round($usedDays / $alloc * 100), 100);
            $icons    = ['Casual Leave'=>'fa-sun','Sick Leave'=>'fa-stethoscope','Hospitalisation'=>'fa-hospital','Maternity Leave'=>'fa-baby-carriage','Paternity Leave'=>'fa-baby'];
            $colors   = ['Casual Leave'=>'#6366F1','Sick Leave'=>'#F97316','Hospitalisation'=>'#EF4444','Maternity Leave'=>'#EC4899','Paternity Leave'=>'#3B82F6'];
            $icon     = $icons[$type]  ?? 'fa-calendar';
            $color    = $colors[$type] ?? '#8892B0';
        @endphp
        <div class="lv-bal-card">
            <div class="lv-bal-head">
                <div class="lv-bal-icon" style="background:{{ $color }}20;color:{{ $color }}">
                    <i class="fa {{ $icon }}"></i>
                </div>
                <div>
                    <div class="lv-bal-type">{{ $type }}</div>
                    <div class="lv-bal-meta">{{ $usedDays }} / {{ $data['allocated'] }} used</div>
                </div>
                <div class="lv-bal-remaining" style="color:{{ $data['remaining'] > 0 ? '#10B981' : '#EF4444' }}">
                    {{ $data['remaining'] }}<span>left</span>
                </div>
            </div>
            <div class="lv-progress">
                <div class="lv-progress-fill" style="width:{{ $pct }}%;background:{{ $color }}"></div>
            </div>
            <div class="lv-bal-stats">
                <span><em>Allocated</em> <strong>{{ $data['allocated'] }}</strong></span>
                <span><em>Paid Used</em> <strong>{{ $data['paid'] }}</strong></span>
                <span><em>LOP</em> <strong style="color:#EF4444">{{ $data['lop'] }}</strong></span>
                <span><em>Remaining</em> <strong style="color:#10B981">{{ $data['remaining'] }}</strong></span>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Upcoming --}}
    <div class="lv-section mt-4">
        <div class="lv-section-head" onclick="toggleSection(this)">
            <span><i class="fa fa-calendar-check me-2" style="color:#6366F1"></i> Upcoming Leaves & Holidays</span>
            <i class="fa fa-chevron-down lv-chevron"></i>
        </div>
        <div class="lv-section-body" style="display:none">
            @forelse($upcoming_leaves as $l)
            <div class="lv-item">
                <span class="lv-dot" style="background:#6366F1"></span>
                <strong>{{ \Carbon\Carbon::parse($l->from_date)->format('d M Y, l') }}</strong>
                <span class="lv-type-tag">{{ $l->leave_type }}</span>
                <span class="text-muted small">{{ $l->no_of_days }} day(s)</span>
            </div>
            @empty @endforelse
            @forelse($upcoming_holidays as $h)
            <div class="lv-item">
                <span class="lv-dot" style="background:#F59E0B"></span>
                <strong>{{ \Carbon\Carbon::parse($h->holidaydate)->format('d M Y, l') }}</strong>
                <span class="lv-holiday-tag">{{ $h->title }}</span>
            </div>
            @empty
            @if($upcoming_leaves->isEmpty())
            <div class="lv-empty">No upcoming leaves or holidays.</div>
            @endif
            @endforelse
        </div>
    </div>

    {{-- Past --}}
    <div class="lv-section mt-3">
        <div class="lv-section-head" onclick="toggleSection(this)">
            <span><i class="fa fa-clock-rotate-left me-2" style="color:#F97316"></i> Past Leaves & Holidays</span>
            <i class="fa fa-chevron-down lv-chevron"></i>
        </div>
        <div class="lv-section-body" style="display:none">
            @forelse($past_leaves as $l)
            <div class="lv-item">
                <span class="lv-dot" style="background:#8892B0"></span>
                <strong>{{ \Carbon\Carbon::parse($l->from_date)->format('d M Y') }}</strong>
                <span class="lv-type-tag">{{ $l->leave_type }}</span>
                <span class="text-muted small">{{ $l->no_of_days }} day(s)</span>
                <span class="lv-status-badge lv-badge-{{ $l->status }}">{{ ucfirst($l->status) }}</span>
            </div>
            @empty @endforelse
            @forelse($past_holidays as $h)
            <div class="lv-item">
                <span class="lv-dot" style="background:#F59E0B"></span>
                <strong>{{ \Carbon\Carbon::parse($h->holidaydate)->format('d M Y') }}</strong>
                <span class="lv-holiday-tag">{{ $h->title }}</span>
            </div>
            @empty
            @if($past_leaves->isEmpty())
            <div class="lv-empty">No past leaves or holidays.</div>
            @endif
            @endforelse
        </div>
    </div>
</div>

{{-- ─────────────────────── BALANCE TAB ──────────────────────────────────────── --}}
<div class="lv-pane" id="tab-balance">
    <div class="lv-panel">
        @foreach($leaveBalance as $type => $data)
        @php
            $usedDays = $data['paid'] + $data['lop'];
            $alloc    = max($data['allocated'], 1);
            $pct      = min(round($usedDays / $alloc * 100), 100);
            $icons    = ['Casual Leave'=>'fa-sun','Sick Leave'=>'fa-stethoscope','Hospitalisation'=>'fa-hospital','Maternity Leave'=>'fa-baby-carriage','Paternity Leave'=>'fa-baby'];
            $colors   = ['Casual Leave'=>'#6366F1','Sick Leave'=>'#F97316','Hospitalisation'=>'#EF4444','Maternity Leave'=>'#EC4899','Paternity Leave'=>'#3B82F6'];
            $icon     = $icons[$type]  ?? 'fa-calendar';
            $color    = $colors[$type] ?? '#8892B0';
        @endphp
        <div class="lv-lb-row">
            <div class="lv-lb-left">
                <div class="lv-lb-icon" style="background:{{ $color }}20;color:{{ $color }}">
                    <i class="fa {{ $icon }}"></i>
                </div>
                <div>
                    <div class="lv-lb-type">{{ $type }}</div>
                    <div class="lv-progress mt-1" style="width:120px">
                        <div class="lv-progress-fill" style="width:{{ $pct }}%;background:{{ $color }}"></div>
                    </div>
                    <div style="font-size:11px;color:#8892B0;margin-top:3px">{{ $pct }}% used</div>
                </div>
            </div>
            <div class="lv-lb-right">
                <div class="lv-lb-stat">
                    <span>Allocated</span><strong style="color:#6366F1">{{ $data['allocated'] }}</strong>
                </div>
                <div class="lv-lb-stat">
                    <span>Paid Used</span><strong>{{ $data['paid'] }}</strong>
                </div>
                <div class="lv-lb-stat">
                    <span>LOP</span><strong style="color:#EF4444">{{ $data['lop'] }}</strong>
                </div>
                <div class="lv-lb-stat">
                    <span>Remaining</span><strong style="color:#10B981">{{ $data['remaining'] }}</strong>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ─────────────────────── REQUESTS TAB ─────────────────────────────────────── --}}
<div class="lv-pane" id="tab-requests">
    <div class="lv-req-topbar">
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <select id="reqTypeSelect" class="lv-select" style="width:150px">
                <option value="leave">Leave</option>
                <option value="permission">Permission</option>
            </select>
            <input type="text" id="reqSearch" class="lv-input" placeholder="Search…" style="width:200px">
            <select id="reqStatusFilter" class="lv-select" style="width:140px">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="declined">Declined</option>
            </select>
        </div>
        <div class="d-flex gap-2">
            @if(isset($permissions) && $permissions->can_create)
            <button id="addReqBtn"
                data-leave-url="{{ route('employee-leaves.create') }}"
                data-perm-url="{{ isset($permissions) && $permissions->can_create ? route('employee-permissions.create') : '' }}"
                class="lv-btn lv-btn-primary">
                <i class="fa fa-plus me-1"></i> <span id="addBtnLabel">Apply Leave</span>
            </button>
            @endif
        </div>
    </div>

    {{-- LEAVE TABLE --}}
    <div id="leaveTableWrap">
        @if($leaves->count())
        <div class="lv-table-wrap">
            <table class="lv-table" id="leaveTable">
                <thead>
                    <tr>
                        <th>Leave Type</th>
                        <th>Period</th>
                        <th>Days</th>
                        <th>Reason</th>
                        <th>Applied On</th>
                        <th>TL</th>
                        <th>HR</th>
                        <th>Manager</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaves as $leave)
                    @php
                        $tlName  = $leave->tl_approved_by  ? DB::table('allemployees')->where('id',$leave->tl_approved_by)->value(DB::raw("CONCAT(firstname,' ',lastname)")) : null;
                        $hrName  = $leave->hr_approved_by  ? DB::table('allemployees')->where('id',$leave->hr_approved_by)->value(DB::raw("CONCAT(firstname,' ',lastname)")) : null;
                        $mgrName = $leave->manager_approved_by ? DB::table('allemployees')->where('id',$leave->manager_approved_by)->value(DB::raw("CONCAT(firstname,' ',lastname)")) : null;
                    @endphp
                    <tr data-status="{{ $leave->status }}" data-search="{{ strtolower($leave->leave_type.' '.$leave->leave_reason) }}">
                        <td>
                            <span class="lv-type-badge">{{ $leave->leave_type }}</span>
                        </td>
                        <td>
                            <div style="font-size:13px;font-weight:600">{{ \Carbon\Carbon::parse($leave->from_date)->format('d M Y') }}</div>
                            <div style="font-size:11px;color:#8892B0">to {{ \Carbon\Carbon::parse($leave->to_date)->format('d M Y') }}</div>
                        </td>
                        <td><strong>{{ $leave->no_of_days }}</strong></td>
                        <td style="max-width:160px;font-size:12px;color:#4B5563">{{ \Illuminate\Support\Str::limit($leave->leave_reason, 40) }}</td>
                        <td style="font-size:12px;color:#8892B0">{{ \Carbon\Carbon::parse($leave->created_at)->format('d M Y') }}</td>
                        <td>@include('hrms.Employee.EmployeeLeaves.partials.approval_pill', ['val'=>$leave->tl_approved, 'name'=>$tlName])</td>
                        <td>@include('hrms.Employee.EmployeeLeaves.partials.approval_pill', ['val'=>$leave->hr_approved, 'name'=>$hrName])</td>
                        <td>@include('hrms.Employee.EmployeeLeaves.partials.approval_pill', ['val'=>$leave->manager_approved, 'name'=>$mgrName])</td>
                        <td>
                            <span class="lv-status-badge lv-badge-{{ $leave->status }}">
                                {{ ucfirst($leave->status) }}
                            </span>
                            <div style="font-size:10px;color:#8892B0;margin-top:2px">
                                {{ ($leave->tl_approved == 'approved' ? 1 : 0) + ($leave->hr_approved == 'approved' ? 1 : 0) + ($leave->manager_approved == 'approved' ? 1 : 0) }}/3
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                @if(isset($permissions) && $permissions->can_edit && !in_array($leave->status,['approved','declined']))
                                <a href="{{ route('employee-leaves.edit', $leave->id) }}" class="lv-act-btn lv-act-edit" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                @endif
                                {{-- Timeline view --}}
                                <button class="lv-act-btn lv-act-view" title="Timeline"
                                    onclick="openTimeline({{ json_encode(['id'=>$leave->id,'type'=>$leave->leave_type,'from'=>$leave->from_date,'to'=>$leave->to_date,'days'=>$leave->no_of_days,'status'=>$leave->status,'tl_approved'=>$leave->tl_approved,'hr_approved'=>$leave->hr_approved,'manager_approved'=>$leave->manager_approved,'tl_name'=>$tlName,'hr_name'=>$hrName,'mgr_name'=>$mgrName,'reason'=>$leave->leave_reason,'applied'=>$leave->created_at]) }})">
                                    <i class="fa fa-eye"></i>
                                </button>
                                @if(isset($permissions) && $permissions->can_delete && $leave->status === 'pending')
                                <button class="lv-act-btn lv-act-del" title="Delete"
                                    onclick="confirmDeleteLeave({{ $leave->id }})">
                                    <i class="fa fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="lv-empty-state">
            <i class="fa fa-inbox fa-3x mb-3" style="color:#E5E7EB"></i>
            <h5>No Leave Requests</h5>
            <p>You haven't applied for any leave yet.</p>
            @if(isset($permissions) && $permissions->can_create)
            <a href="{{ route('employee-leaves.create') }}" class="lv-btn lv-btn-primary mt-2">Apply Now</a>
            @endif
        </div>
        @endif
    </div>

    {{-- PERMISSION TABLE --}}
    <div id="permTableWrap" style="display:none">
        @if($employeePermissions->count())
        @php
            // Check if any permission has manager_approved set (not null/pending)
            $hasManagerAction = $employeePermissions->contains(fn($p) => !empty($p->manager_approved) && $p->manager_approved !== 'pending');
            // Pre-fetch approver names
            $permApproverNames = [];
            foreach ($employeePermissions as $p) {
                $bid = $p->manager_approved_by ?? null;
                if ($bid && !isset($permApproverNames[$bid])) {
                    $permApproverNames[$bid] = DB::table('allemployees')->where('id',$bid)->value(DB::raw("CONCAT(firstname,' ',lastname)"));
                }
            }
        @endphp
        <div class="lv-table-wrap">
            <table class="lv-table" id="permTable">
                <thead>
                    <tr>
                        <th>Date</th><th>Time</th><th>Duration</th><th>Reason</th>
                        @if($hasManagerAction)<th>Manager</th>@endif
                        <th>Status</th><th>Applied On</th>
                        @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))<th>Actions</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($employeePermissions as $perm)
                    @php
                        $mgrApproverName = isset($perm->manager_approved_by) ? ($permApproverNames[$perm->manager_approved_by] ?? null) : null;
                    @endphp
                    <tr data-status="{{ $perm->status }}" data-search="{{ strtolower($perm->permission_reason) }}">
                        <td><strong>{{ \Carbon\Carbon::parse($perm->permission_date)->format('d M Y') }}</strong></td>
                        <td style="font-size:12px">
                            {{ \Carbon\Carbon::parse($perm->start_time)->format('h:i A') }} –
                            {{ \Carbon\Carbon::parse($perm->end_time)->format('h:i A') }}
                        </td>
                        <td>{{ $perm->duration }} hr(s)</td>
                        <td style="font-size:12px;color:#4B5563">{{ \Illuminate\Support\Str::limit($perm->permission_reason, 40) }}</td>
                        @if($hasManagerAction)
                        <td>@include('hrms.Employee.EmployeeLeaves.partials.approval_pill', ['val'=>$perm->manager_approved, 'name'=>$mgrApproverName])</td>
                        @endif
                        <td><span class="lv-status-badge lv-badge-{{ $perm->status }}">{{ ucfirst($perm->status) }}</span></td>
                        <td style="font-size:12px;color:#8892B0">{{ \Carbon\Carbon::parse($perm->created_at)->format('d M Y') }}</td>
                        @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                        <td>
                            <div class="d-flex gap-1">
                                @if(isset($permissions) && $permissions->can_edit)
                                <a href="{{ route('employee-permissions.edit', $perm->id) }}" class="lv-act-btn lv-act-edit"><i class="fa fa-pencil"></i></a>
                                @endif
                                @if(isset($permissions) && $permissions->can_delete)
                                <button class="lv-act-btn lv-act-del" onclick="confirmDeletePerm({{ $perm->id }})"><i class="fa fa-trash"></i></button>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="lv-empty-state">
            <i class="fa fa-inbox fa-3x mb-3" style="color:#E5E7EB"></i>
            <h5>No Permission Requests</h5>
        </div>
        @endif
    </div>
</div>

{{-- ─────────────────────── SHIFT TAB ─────────────────────────────────────────── --}}
<div class="lv-pane" id="tab-shift">
    <div class="lv-panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><strong>{{ $weekStart->format('d M Y') }} — {{ $weekEnd->format('d M Y') }}</strong></h5>
            <div class="d-flex gap-2">
                <button id="weeklyBtn" class="lv-btn lv-btn-primary lv-btn-sm">Weekly</button>
                <button id="monthlyBtn" class="lv-btn lv-btn-outline lv-btn-sm">Monthly</button>
            </div>
        </div>

        <div id="weeklyView" class="table-responsive">
            <table class="table table-bordered shift-table">
                <thead>
                    <tr>
                        <th class="shift-day-header">Day</th>
                        @for($h=8;$h<=19;$h++)
                            <th class="shift-hour-header">{{ date('g A',strtotime("$h:00")) }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                @for($d=0;$d<7;$d++)
                @php
                    $dateObj  = $weekStart->copy()->addDays($d);
                    $dateStr  = $dateObj->format('Y-m-d');
                    $shiftToday = collect($calendarShifts)->firstWhere('date',$dateStr);
                    $leaveToday = $leaveDays[$dateStr] ?? null;
                    // Use employee's actual week-off days instead of hardcoded isWeekend()
                    $isWeekend = !$shiftToday && !isset($calendarShifts[0]) ? $dateObj->isWeekend() : !collect($calendarShifts)->contains('date', $dateStr) && ($dateObj->isWeekend());
                    // Check if this day is a week-off based on calendarShifts (no shift = off day)
                    $isOffDay = !$shiftToday;
                    $holidayToday = $holidayDays[$dateStr] ?? null;
                @endphp
                <tr class="{{ $isOffDay && !$shiftToday ? 'weekend-row' : '' }}">
                    <td class="shift-day-cell">
                        <div class="shift-day-name">{{ $dateObj->format('D') }}</div>
                        <div class="shift-day-date">{{ $dateObj->format('d M') }}</div>
                        @if($holidayToday)
                            <div class="shift-day-holiday">{{ $holidayToday }}</div>
                        @endif
                    </td>
                    @for($h=8;$h<=19;$h++)
                    <td class="position-relative shift-hour-cell">
                        @if($leaveToday && $h==8)
                            <div class="shift-block leave-block">
                                <i class="fa fa-calendar-xmark me-1"></i>Leave
                            </div>
                        @endif
                        @if($shiftToday && !$leaveToday)
                        @php
                            $s = \Carbon\Carbon::parse($shiftToday['start_time']);
                            $e = \Carbon\Carbon::parse($shiftToday['end_time']);
                        @endphp
                        @if($h==$s->hour)
                        <div class="shift-block shift-theme" style="margin-left:{{ ($s->minute/60)*100 }}%;width:calc({{ ceil($s->diffInMinutes($e)/60) }} * 100%)">
                            <div class="shift-block-name">{{ $shiftToday['shift_name'] }}</div>
                            <div class="shift-block-time">{{ $s->format('g:i A') }}–{{ $e->format('g:i A') }}</div>
                        </div>
                        @endif
                        @endif
                        @if(!$shiftToday && !$leaveToday && $h==8 && $isOffDay)
                            <div class="shift-block weekend">
                                <i class="fa fa-calendar-day me-1"></i>Off
                            </div>
                        @endif
                    </td>
                    @endfor
                </tr>
                @endfor
                </tbody>
            </table>
        </div>

        <div id="monthlyView" class="d-none table-responsive">
            @php
                $mStart    = now()->startOfMonth();
                $mEnd      = now()->endOfMonth();
                $gridStart = $mStart->copy()->startOfWeek();
                $gridEnd   = $mEnd->copy()->endOfWeek();
            @endphp
            <h5 class="mb-3"><strong>{{ now()->format('F Y') }}</strong></h5>
            <table class="table table-bordered monthly-table">
                <thead><tr><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th class="text-muted">Sat</th><th class="text-muted">Sun</th></tr></thead>
                <tbody>
                @php $cur = $gridStart->copy(); @endphp
                @while($cur <= $gridEnd)
                <tr>
                @for($i=0;$i<7;$i++)
                @php
                    $ds = $cur->format('Y-m-d');
                    $st = collect($calendarShifts)->firstWhere('date',$ds);
                    $lt = $leaveDays[$ds] ?? null;
                @endphp
                <td class="{{ $cur->month!=now()->month?'bg-light':'' }} {{ $cur->isWeekend()?'weekend-cell':'' }}">
                    <div class="month-date">{{ $cur->format('j') }}</div>
                    @if($st)<div class="month-shift"><strong>{{ $st['shift_name'] }}</strong></div>@endif
                    @if($lt)<div class="month-leave">Leave: {{ $lt }}</div>@endif
                    @if(isset($holidayDays[$ds]))<div class="month-holiday">{{ $holidayDays[$ds] }}</div>@endif
                </td>
                @php $cur->addDay(); @endphp
                @endfor
                </tr>
                @endwhile
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ─────────────────────── HOLIDAYS TAB ──────────────────────────────────────── --}}
<div class="lv-pane" id="tab-holidays">
    <div class="lv-panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Holiday Calendar</h5>
            @if(isset($permissionsHolidays) && $permissionsHolidays->can_create)
            <a href="#" class="lv-btn lv-btn-primary lv-btn-sm" data-toggle="modal" data-target="#addHolidayModal">
                <i class="fa fa-plus me-1"></i> Add Holiday
            </a>
            @endif
        </div>
        <div class="lv-table-wrap">
            <table class="lv-table">
                @php
                    $showHolidayActions = isset($permissionsHolidays) && ($permissionsHolidays->can_edit || $permissionsHolidays->can_delete);
                @endphp
                <thead><tr><th>#</th><th>Holiday</th><th>Date</th><th>Day</th>@if($showHolidayActions)<th>Actions</th>@endif</tr></thead>
                <tbody>
                @forelse($holidays as $i => $h)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td><strong>{{ $h->title }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($h->holidaydate)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($h->holidaydate)->format('l') }}</td>
                    @if($showHolidayActions)
                    <td>
                        @if($permissionsHolidays->can_edit)
                        <button class="lv-act-btn lv-act-edit" onclick="editHoliday({{ $h->id }})"><i class="fa fa-pencil"></i></button>
                        @endif
                        @if($permissionsHolidays->can_delete)
                        <button class="lv-act-btn lv-act-del" onclick="confirmDeleteHoliday({{ $h->id }})"><i class="fa fa-trash"></i></button>
                        @endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="{{ $showHolidayActions ? 5 : 4 }}" class="text-center text-muted py-4">No holidays found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>{{-- end tab-content --}}
</div>{{-- end lv-wrap --}}

{{-- ══ TIMELINE MODAL ════════════════════════════════════════════════════════ --}}
<div id="timelineModal" style="display:none;position:fixed;inset:0;background:rgba(11,20,55,.5);z-index:9999;overflow-y:auto;padding:20px">
    <div style="background:#fff;border-radius:18px;max-width:600px;margin:40px auto;padding:28px;box-shadow:0 24px 64px rgba(11,20,55,.18);font-family:'DM Sans',sans-serif;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px">
            <div>
                <p style="font-size:11px;font-weight:700;color:#8892B0;text-transform:uppercase;letter-spacing:.08em;margin:0 0 4px">Approval Timeline</p>
                <h2 id="tlTitle" style="font-size:18px;font-weight:700;color:#0B1437;margin:0"></h2>
            </div>
            <button onclick="document.getElementById('timelineModal').style.display='none'" style="background:#F3F4F6;border:none;border-radius:8px;padding:6px 12px;font-size:18px;cursor:pointer;font-weight:700;">&times;</button>
        </div>
        <div id="tlLeaveInfo" style="background:#F9FAFB;border-radius:10px;padding:14px;margin-bottom:20px;font-size:13px;display:grid;grid-template-columns:1fr 1fr;gap:8px"></div>
        <div id="tlTimeline"></div>
    </div>
</div>

{{-- ══ DELETE CONFIRM MODAL ════════════════════════════════════════════════════ --}}
<div id="delModal" style="display:none;position:fixed;inset:0;background:rgba(11,20,55,.5);z-index:9999;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:16px;padding:28px;max-width:400px;width:90%;box-shadow:0 16px 48px rgba(0,0,0,.15);font-family:'DM Sans',sans-serif;text-align:center">
        <div style="width:56px;height:56px;border-radius:50%;background:#FEF2F2;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px;color:#EF4444">
            <i class="fa fa-trash"></i>
        </div>
        <h5 style="font-weight:700;color:#0B1437;margin-bottom:8px">Delete <span id="delType">Leave</span>?</h5>
        <p style="color:#8892B0;font-size:13px;margin-bottom:20px">This action cannot be undone.</p>
        <div style="display:flex;gap:12px;justify-content:center">
            <button onclick="document.getElementById('delModal').style.display='none'" style="padding:10px 24px;border:1px solid #E5E7EB;border-radius:8px;background:#fff;cursor:pointer;font-weight:600">Cancel</button>
            <form id="delForm" method="POST" style="margin:0">
                @csrf @method('DELETE')
                <button type="submit" style="padding:10px 24px;background:#EF4444;color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:700">Delete</button>
            </form>
        </div>
    </div>
</div>

{{-- ══ HOLIDAY MODALS ════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="addHolidayModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="holidayModalTitle">Add Holiday</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form id="holidayForm" action="{{ route('holidays.store') }}" method="POST">
                @csrf
                <input type="hidden" id="holiday-id" name="holiday_id">
                <div class="mb-3">
                    <label>Holiday Title *</label>
                    <input type="text" class="form-control" id="holiday-title" name="holiday_title" required>
                    <div id="title-warning" class="text-danger small mt-1" style="display:none">Title already exists!</div>
                </div>
                <div class="mb-3">
                    <label>Holiday Date *</label>
                    <input type="date" class="form-control" id="holiday-date" name="holiday_date" required>
                </div>
                <button type="submit" class="lv-btn lv-btn-primary" id="submitHolidayBtn">Submit</button>
            </form>
        </div>
    </div></div>
</div>

<style>
:root { --lv-primary:#F97316; --lv-primary-dark:#EA580C; }
.lv-wrap { background:#F5F6FA; min-height:100vh; padding:24px 32px; font-family:'DM Sans',sans-serif; }
@media(max-width:767px){.lv-wrap{padding:12px 10px}}

/* ALERTS */
.lv-alert { display:flex;align-items:center;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:14px;font-weight:500;position:relative }
.lv-alert-success { background:#ECFDF5;color:#047857;border:1px solid #6EE7B7 }
.lv-alert-danger  { background:#FEF2F2;color:#B91C1C;border:1px solid #FCA5A5 }
.lv-alert-close   { position:absolute;right:12px;background:none;border:none;font-size:18px;cursor:pointer;color:inherit;font-weight:700 }

/* TOPBAR */
.lv-topbar { display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:24px }
.lv-page-title { font-size:22px;font-weight:700;color:#0B1437;margin:0 }
.lv-page-sub   { font-size:13px;color:#8892B0;margin:2px 0 0 }

/* BUTTONS */
.lv-btn { display:inline-flex;align-items:center;padding:9px 18px;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:.15s }
.lv-btn-primary { background:var(--lv-primary);color:#fff }
.lv-btn-primary:hover { background:var(--lv-primary-dark);color:#fff }
.lv-btn-outline { background:#fff;color:#374151;border:1px solid #E5E7EB }
.lv-btn-outline:hover { background:#F9FAFB }
.lv-btn-sm { padding:6px 14px;font-size:12px }

/* KPI GRID */
.lv-kpi-grid { display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:16px;margin-bottom:24px }
@media(max-width:1200px){.lv-kpi-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:600px){.lv-kpi-grid{grid-template-columns:repeat(2,1fr)}}
.lv-kpi-card { background:#fff;border:1px solid #E5E7EB;border-radius:14px;padding:18px 16px;display:flex;align-items:center;gap:14px;border-bottom:3px solid var(--accent);transition:.2s }
.lv-kpi-card:hover { transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,.06) }
.lv-kpi-icon { width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0 }
.lv-kpi-val  { font-size:26px;font-weight:700;color:#0B1437;line-height:1 }
.lv-kpi-label{ font-size:12px;font-weight:600;color:#4B5563;margin-top:4px }
.lv-kpi-sub  { font-size:11px;color:#8892B0;margin-top:2px }

/* TABS */
.lv-tabs { display:flex;gap:4px;background:#fff;border-radius:12px;padding:6px;border:1px solid #E5E7EB;margin-bottom:20px;flex-wrap:wrap }
.lv-tab  { background:none;border:none;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;color:#4B5563;cursor:pointer;transition:.15s;white-space:nowrap;display:flex;align-items:center;gap:4px }
.lv-tab:hover  { background:#F9FAFB }
.lv-tab.active { background:var(--lv-primary);color:#fff }
.lv-badge-pill { background:#EF4444;color:#fff;border-radius:20px;font-size:10px;font-weight:700;padding:1px 6px;margin-left:4px }

/* PANE */
.lv-pane { display:none }
.lv-pane.active { display:block }
.lv-panel { background:#fff;border:1px solid #E5E7EB;border-radius:14px;padding:24px }

/* YEAR BAR */
.lv-year-bar { display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:20px }
.lv-icon-btn { background:#F3F4F6;border:none;border-radius:8px;padding:6px 12px;cursor:pointer;color:#374151;font-size:13px }
.lv-icon-btn:hover { background:#E5E7EB }
.lv-year-label { font-size:14px;font-weight:600;color:#0B1437 }
.lv-year-stat  { font-size:13px;color:#4B5563 }

/* BALANCE CARDS */
.lv-balance-cards { display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;margin-bottom:4px }
.lv-bal-card { background:#fff;border:1px solid #E5E7EB;border-radius:14px;padding:18px;transition:.2s }
.lv-bal-card:hover { box-shadow:0 4px 12px rgba(0,0,0,.06);transform:translateY(-2px) }
.lv-bal-head { display:flex;align-items:center;gap:12px;margin-bottom:12px }
.lv-bal-icon { width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0 }
.lv-bal-type { font-size:14px;font-weight:700;color:#0B1437 }
.lv-bal-meta { font-size:11px;color:#8892B0;margin-top:2px }
.lv-bal-remaining { margin-left:auto;text-align:right }
.lv-bal-remaining { font-size:22px;font-weight:700;line-height:1 }
.lv-bal-remaining span { display:block;font-size:10px;text-transform:uppercase;letter-spacing:.05em;font-weight:700;margin-top:2px }
.lv-progress { height:5px;background:#F3F4F6;border-radius:4px;overflow:hidden;margin-bottom:12px }
.lv-progress-fill { height:100%;border-radius:4px;transition:width .6s ease }
.lv-bal-stats { display:flex;justify-content:space-between;font-size:11px }
.lv-bal-stats span { display:flex;flex-direction:column;gap:2px }
.lv-bal-stats em { color:#8892B0;font-style:normal }
.lv-bal-stats strong { font-size:13px;color:#0B1437 }

/* SECTIONS */
.lv-section { background:#fff;border:1px solid #E5E7EB;border-radius:12px;overflow:hidden }
.lv-section-head { display:flex;justify-content:space-between;align-items:center;padding:14px 18px;cursor:pointer;font-size:14px;font-weight:600;color:#0B1437;user-select:none }
.lv-section-head:hover { background:#F9FAFB }
.lv-chevron { transition:transform .2s;font-size:12px;color:#8892B0 }
.lv-section-body { padding:0 18px 14px }
.lv-item { display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid #F3F4F6;font-size:13px;flex-wrap:wrap }
.lv-item:last-child { border-bottom:none }
.lv-dot { width:8px;height:8px;border-radius:50%;flex-shrink:0 }
.lv-type-tag    { background:#EEF2FF;color:#4338CA;border-radius:6px;padding:2px 8px;font-size:11px;font-weight:600 }
.lv-holiday-tag { background:#FFF7ED;color:#C2410C;border-radius:6px;padding:2px 8px;font-size:11px;font-weight:600 }

/* LEAVE BALANCE ROW */
.lv-lb-row { display:flex;align-items:center;justify-content:space-between;padding:14px 0;border-bottom:1px solid #F3F4F6;flex-wrap:wrap;gap:12px }
.lv-lb-row:last-child { border-bottom:none }
.lv-lb-left { display:flex;align-items:center;gap:14px }
.lv-lb-icon { width:38px;height:38px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0 }
.lv-lb-type { font-size:14px;font-weight:700;color:#0B1437 }
.lv-lb-right { display:flex;gap:24px;flex-wrap:wrap }
.lv-lb-stat { text-align:center }
.lv-lb-stat span { display:block;font-size:10px;color:#8892B0;text-transform:uppercase;letter-spacing:.04em }
.lv-lb-stat strong { font-size:14px;font-weight:700;color:#0B1437 }

/* REQUESTS */
.lv-req-topbar { display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:16px }
.lv-select { height:36px;border:1px solid #E5E7EB;border-radius:8px;padding:0 10px;font-size:13px;color:#374151;background:#fff }
.lv-input  { height:36px;border:1px solid #E5E7EB;border-radius:8px;padding:0 12px;font-size:13px;color:#374151;background:#fff }
.lv-table-wrap { overflow-x:auto;border-radius:12px;border:1px solid #E5E7EB }
.lv-table { width:100%;border-collapse:collapse;font-size:13px;background:#fff }
.lv-table th { background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#6B7280;font-size:11px;font-weight:700;letter-spacing:.05em;padding:11px 14px;text-align:left;text-transform:uppercase;white-space:nowrap }
.lv-table td { border-bottom:1px solid #F3F4F6;color:#0B1437;padding:12px 14px;vertical-align:middle }
.lv-table tbody tr:last-child td { border-bottom:none }
.lv-table tbody tr:hover td { background:#FAFAFA }

/* TYPE BADGE */
.lv-type-badge { background:#EFF6FF;color:#1D4ED8;border-radius:6px;padding:3px 9px;font-size:11px;font-weight:700;white-space:nowrap }

/* STATUS BADGES */
.lv-status-badge { border-radius:20px;font-size:11px;font-weight:700;padding:4px 10px;display:inline-flex;white-space:nowrap }
.lv-badge-approved { background:#ECFDF5;color:#047857 }
.lv-badge-pending  { background:#FFFBEB;color:#B45309 }
.lv-badge-declined,.lv-badge-rejected { background:#FEF2F2;color:#B91C1C }

/* APPROVAL PILL */
.lv-apill { border-radius:20px;font-size:10px;font-weight:700;padding:3px 8px;display:inline-flex;flex-direction:column;align-items:center;gap:1px;white-space:nowrap }
.lv-apill-approved { background:#ECFDF5;color:#047857 }
.lv-apill-pending  { background:#FFFBEB;color:#B45309 }
.lv-apill-declined { background:#FEF2F2;color:#B91C1C }
.lv-apill-null     { background:#F3F4F6;color:#9CA3AF }
.lv-apill-name     { font-size:9px;font-weight:500;opacity:.8;max-width:70px;overflow:hidden;text-overflow:ellipsis }

/* ACTION BUTTONS */
.lv-act-btn { width:28px;height:28px;border-radius:6px;border:1px solid #E5E7EB;background:#fff;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;font-size:12px;transition:.15s;text-decoration:none;color:#374151 }
.lv-act-edit:hover { background:#EFF6FF;border-color:#BFDBFE;color:#1D4ED8 }
.lv-act-del:hover  { background:#FEF2F2;border-color:#FCA5A5;color:#EF4444 }
.lv-act-view:hover { background:#F0FDF4;border-color:#86EFAC;color:#15803D }

/* EMPTY STATE */
.lv-empty-state { text-align:center;padding:48px 24px;color:#8892B0 }
.lv-empty-state h5 { color:#374151;font-weight:700;margin-bottom:8px }
.lv-empty { text-align:center;padding:32px;color:#8892B0;font-size:13px }

/* TIMELINE */
.lv-timeline { display:flex;flex-direction:column;gap:0 }
.lv-tl-step { display:flex;gap:16px;position:relative }
.lv-tl-step:not(:last-child)::after { content:'';position:absolute;left:19px;top:36px;bottom:-4px;width:2px;background:#E5E7EB }
.lv-tl-dot { width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;border:2px solid transparent }
.lv-tl-dot-approved { background:#ECFDF5;color:#10B981;border-color:#6EE7B7 }
.lv-tl-dot-pending  { background:#FFFBEB;color:#F59E0B;border-color:#FDE68A }
.lv-tl-dot-declined { background:#FEF2F2;color:#EF4444;border-color:#FCA5A5 }
.lv-tl-dot-skipped  { background:#F3F4F6;color:#D1D5DB;border-color:#E5E7EB }
.lv-tl-body { padding:8px 0 20px }
.lv-tl-role { font-size:12px;font-weight:700;color:#0B1437 }
.lv-tl-name { font-size:11px;color:#8892B0 }

/* SHIFT TABLE */
.shift-table { min-width: 100%; table-layout: fixed; }
.shift-table th, .shift-table td { padding: 0 !important; height: 70px; font-size: 11px; vertical-align: middle; }
.shift-day-header { width: 120px !important; min-width: 120px; background: #F9FAFB !important; font-weight: 700 !important; }
.shift-hour-header { font-weight: 600; color: #6B7280; font-size: 10px; }
.shift-day-cell { background: #F9FAFB; padding: 8px !important; text-align: center; vertical-align: middle !important; }
.shift-day-name { font-weight: 700; color: #0B1437; font-size: 13px; }
.shift-day-date { font-size: 11px; color: #8892B0; margin-top: 2px; }
.shift-day-holiday { font-size: 10px; color: #0077c8; margin-top: 4px; font-weight: 600; }
.shift-hour-cell { position: relative; border-left: 1px solid #F3F4F6; }
.weekend-row { background: #FAFBFC; }
.weekend-row .shift-day-cell { background: #F3F4F6; }

/* SHIFT BLOCKS */
.shift-block { position: absolute; top: 4px; left: 2px; height: 62px; padding: 6px 8px; border-radius: 8px; font-size: 11px; font-weight: 600; display: flex; align-items: center; justify-content: center; text-align: center; overflow: hidden; z-index: 1; }
.shift-theme { background: linear-gradient(135deg, #ffddaa 0%, #ffe4c4 100%); border-left: 4px solid #ff7b00; color: #7c4d00; }
.shift-block-name { font-size: 11px; font-weight: 700; line-height: 1.2; }
.shift-block-time { font-size: 9px; font-weight: 500; color: #8B7355; margin-top: 1px; }
.leave-block { background: linear-gradient(135deg, #ffd1d1 0%, #ffe0e0 100%); border-left: 4px solid #d9534f; color: #8B0000; font-size: 10px; }
.weekend { background: #e8ecf0; border-left: 3px solid #94a3b8; color: #64748b; font-size: 10px; }
.holiday-block { background: linear-gradient(135deg, #c5e8ff 0%, #d6eeff 100%); border-left: 4px solid #008cd6; color: #004a77; }

/* MONTHLY VIEW */
.monthly-table td { height: 100px; vertical-align: top; padding: 4px !important; font-size: 11px; }
.month-date { font-weight: 700; font-size: 13px; margin-bottom: 3px; color: #0B1437; }
.month-shift { background: #ffddaa; border-left: 3px solid #ff7b00; padding: 2px 4px; border-radius: 4px; font-size: 10px; margin-bottom: 2px; font-weight: 600; }
.month-leave { background: #ffd1d1; border-left: 3px solid #d9534f; padding: 2px 4px; border-radius: 4px; font-size: 10px; margin-bottom: 2px; }
.month-holiday { background: #c5e8ff; border-left: 3px solid #0077c8; padding: 2px 4px; border-radius: 4px; font-size: 10px; }
.weekend-cell { background: #f1f5f9 !important; }
.weekend-cell .month-date { color: #94a3b8; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Tab switching ──
    document.querySelectorAll('.lv-tab').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.lv-tab').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.lv-pane').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('tab-' + this.dataset.tab).classList.add('active');
            localStorage.setItem('lv_tab', this.dataset.tab);
        });
    });

    // Restore last tab
    var lastTab = localStorage.getItem('lv_tab') || 'summary';
    var tabBtn = document.querySelector('.lv-tab[data-tab="' + lastTab + '"]');
    if (tabBtn) tabBtn.click();

    // ── Year navigation ──
    document.getElementById('prevYear').addEventListener('click', function () {
        var y = parseInt(document.getElementById('currentYear').value) - 1;
        window.location.href = '?year=' + y;
    });
    document.getElementById('nextYear').addEventListener('click', function () {
        var y = parseInt(document.getElementById('currentYear').value) + 1;
        window.location.href = '?year=' + y;
    });

    // ── Section toggles ──
    window.toggleSection = function (head) {
        var body    = head.nextElementSibling;
        var chevron = head.querySelector('.lv-chevron');
        var open    = body.style.display !== 'none';
        body.style.display    = open ? 'none' : 'block';
        chevron.style.transform = open ? '' : 'rotate(180deg)';
    };

    // ── Request type switch ──
    var reqTypeSelect = document.getElementById('reqTypeSelect');
    var leaveWrap     = document.getElementById('leaveTableWrap');
    var permWrap      = document.getElementById('permTableWrap');
    var addBtnLabel   = document.getElementById('addBtnLabel');
    var addReqBtn     = document.getElementById('addReqBtn');

    if (reqTypeSelect) {
        reqTypeSelect.addEventListener('change', function () {
            if (this.value === 'permission') {
                leaveWrap && (leaveWrap.style.display = 'none');
                permWrap  && (permWrap.style.display  = 'block');
                if (addBtnLabel) addBtnLabel.textContent = 'Apply Permission';
            } else {
                leaveWrap && (leaveWrap.style.display = 'block');
                permWrap  && (permWrap.style.display  = 'none');
                if (addBtnLabel) addBtnLabel.textContent = 'Apply Leave';
            }
        });
    }

    if (addReqBtn) {
        addReqBtn.addEventListener('click', function () {
            var type = reqTypeSelect ? reqTypeSelect.value : 'leave';
            var url  = type === 'permission' ? this.dataset.permUrl : this.dataset.leaveUrl;
            if (url) window.location.href = url;
        });
    }

    // ── Search & filter ──
    function filterRows(tableId, search, status) {
        var rows = document.querySelectorAll('#' + tableId + ' tbody tr');
        rows.forEach(function (row) {
            var matchSearch = !search || (row.dataset.search || '').includes(search.toLowerCase());
            var matchStatus = !status || (row.dataset.status || '') === status;
            row.style.display = (matchSearch && matchStatus) ? '' : 'none';
        });
    }

    var searchInput  = document.getElementById('reqSearch');
    var statusFilter = document.getElementById('reqStatusFilter');

    function applyFilters() {
        var s = searchInput ? searchInput.value : '';
        var f = statusFilter ? statusFilter.value : '';
        filterRows('leaveTable', s, f);
        filterRows('permTable',  s, f);
    }

    if (searchInput)  searchInput.addEventListener('input',  applyFilters);
    if (statusFilter) statusFilter.addEventListener('change', applyFilters);

    // ── Shift view toggle ──
    var weeklyBtn  = document.getElementById('weeklyBtn');
    var monthlyBtn = document.getElementById('monthlyBtn');
    var weeklyView = document.getElementById('weeklyView');
    var monthlyView = document.getElementById('monthlyView');

    if (weeklyBtn) {
        weeklyBtn.addEventListener('click', function () {
            weeklyView && weeklyView.classList.remove('d-none');
            monthlyView && monthlyView.classList.add('d-none');
            this.className = 'lv-btn lv-btn-primary lv-btn-sm';
            monthlyBtn.className = 'lv-btn lv-btn-outline lv-btn-sm';
        });
        monthlyBtn.addEventListener('click', function () {
            weeklyView && weeklyView.classList.add('d-none');
            monthlyView && monthlyView.classList.remove('d-none');
            this.className = 'lv-btn lv-btn-primary lv-btn-sm';
            weeklyBtn.className = 'lv-btn lv-btn-outline lv-btn-sm';
        });
    }
});

// ── Delete leave ──
function confirmDeleteLeave(id) {
    document.getElementById('delType').textContent = 'Leave';
    var f = document.getElementById('delForm');
    f.action = '{{ url("employee-leaves") }}/' + id;
    document.getElementById('delModal').style.display = 'flex';
}
function confirmDeletePerm(id) {
    document.getElementById('delType').textContent = 'Permission';
    var f = document.getElementById('delForm');
    f.action = '{{ url("employee-permissions") }}/' + id;
    document.getElementById('delModal').style.display = 'flex';
}
document.getElementById('delModal').addEventListener('click', function(e){
    if(e.target===this) this.style.display='none';
});

// ── Timeline ──
window.openTimeline = function (data) {
    document.getElementById('tlTitle').textContent = data.type + ' — ' + data.days + ' day(s)';
    document.getElementById('tlLeaveInfo').innerHTML =
        '<div><em style="color:#8892B0;font-size:11px">FROM</em><br><strong>' + data.from + '</strong></div>' +
        '<div><em style="color:#8892B0;font-size:11px">TO</em><br><strong>' + data.to + '</strong></div>' +
        '<div><em style="color:#8892B0;font-size:11px">STATUS</em><br><strong>' + data.status.toUpperCase() + '</strong></div>' +
        '<div><em style="color:#8892B0;font-size:11px">APPLIED</em><br><strong>' + (data.applied||'').substr(0,10) + '</strong></div>';

    var steps = [
        { role:'Employee Applied', status:'approved', name: 'Applied on ' + (data.applied||'').substr(0,10), icon:'fa-user' },
        { role:'Team Lead',       status: data.tl_approved  || 'null', name: data.tl_name  || 'Pending', icon:'fa-user-tie' },
        { role:'HR',              status: data.hr_approved  || 'null', name: data.hr_name  || 'Pending', icon:'fa-id-badge' },
        { role:'Manager',         status: data.manager_approved || 'null', name: data.mgr_name || 'Pending', icon:'fa-briefcase' },
    ];

    var icons = { approved:'fa-check', pending:'fa-hourglass-half', declined:'fa-times', null:'fa-minus' };
    var html = '<div class="lv-timeline">';
    steps.forEach(function(s) {
        var cls  = s.status === 'approved' ? 'lv-tl-dot-approved' : s.status === 'declined' ? 'lv-tl-dot-declined' : s.status === 'pending' ? 'lv-tl-dot-pending' : 'lv-tl-dot-skipped';
        var ic   = icons[s.status] || 'fa-minus';
        html += '<div class="lv-tl-step">' +
            '<div class="lv-tl-dot ' + cls + '"><i class="fa ' + ic + '"></i></div>' +
            '<div class="lv-tl-body"><div class="lv-tl-role">' + s.role + '</div><div class="lv-tl-name">' + (s.name||'') + '</div></div>' +
            '</div>';
    });
    html += '</div>';
    document.getElementById('tlTimeline').innerHTML = html;
    document.getElementById('timelineModal').style.display = 'block';
};
document.getElementById('timelineModal').addEventListener('click', function(e){
    if(e.target===this) this.style.display='none';
});
document.addEventListener('keydown', function(e){ if(e.key==='Escape'){ document.getElementById('timelineModal').style.display='none'; document.getElementById('delModal').style.display='none'; } });

// ── Holiday functions ──
function confirmDeleteHoliday(id) {
    if(confirm('Delete this holiday?')) {
        $.post('{{ route("holidays.destroy","_ID_") }}'.replace('_ID_',id), {_method:'DELETE',_token:'{{ csrf_token() }}'}, function(){ location.reload(); });
    }
}
function editHoliday(id) {
    $.get('{{ route("holidays.edit","_ID_") }}'.replace('_ID_',id), function(h){
        $('#holidayForm').attr('action','{{ route("holidays.update","") }}/'+id);
        $('#holiday-id').val(h.id); $('#holiday-title').val(h.title); $('#holiday-date').val(h.holidaydate);
        $('#holidayModalTitle').text('Edit Holiday');
        $('#submitHolidayBtn').text('Update');
        $('#addHolidayModal').modal('show');
    });
}
$('#addHolidayModal').on('hidden.bs.modal', function(){
    $('#holidayForm').attr('action','{{ route("holidays.store") }}');
    $('#holidayModalTitle').text('Add Holiday');
    $('#submitHolidayBtn').text('Submit');
    $('#holidayForm')[0].reset(); $('#holiday-id').val('');
});
</script>

{{-- Approval pill partial inline since it's simple --}}
@push('scripts')
@endpush
@endsection
