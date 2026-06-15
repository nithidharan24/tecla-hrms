@extends('layouts.index')
@section('content')
@php
    $permissions   = App\Helpers\PermissionHelper::getPermissions('Team Leaves');
    $year          = request('year', date('Y'));
    $today         = request('date', now()->format('Y-m-d'));
    $role          = session('role');
    $isAdmin       = $role === 'admin';
    $viewIsTL      = !$isAdmin && !empty($isTeamLead);
    $viewIsMgr     = !$isAdmin && !empty($isManager);
    $viewIsHR      = !$isAdmin && !empty($isHR);

    $totalMembers  = count($teamMembers);
    $onLeaveCount  = collect($teamMembers)->filter(fn($m) => $m->onLeaveToday)->count();
    $presentCount  = collect($teamMembers)->filter(fn($m) => !$m->onLeaveToday && ($m->attendanceToday ?? false))->count();
    $pendingLvCnt  = count($pendingLeaves  ?? []);
    $pendingPrmCnt = ($pendingPermissions ?? collect())->count();
@endphp

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--lv-primary:#F97316;--lv-primary-dark:#EA580C}
.lv-wrap{background:#F5F6FA;min-height:100vh;padding:24px 32px;font-family:'DM Sans',sans-serif}
@media(max-width:767px){.lv-wrap{padding:12px 10px}}
.lv-alert{display:flex;align-items:center;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:14px;font-weight:500;position:relative}
.lv-alert-success{background:#ECFDF5;color:#047857;border:1px solid #6EE7B7}
.lv-alert-danger{background:#FEF2F2;color:#B91C1C;border:1px solid #FCA5A5}
.lv-alert-close{position:absolute;right:12px;background:none;border:none;font-size:18px;cursor:pointer;color:inherit;font-weight:700}
.lv-topbar{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:24px}
.lv-page-title{font-size:22px;font-weight:700;color:#0B1437;margin:0}
.lv-page-sub{font-size:13px;color:#8892B0;margin:2px 0 0}
.lv-date-input{height:36px;border:1px solid #E5E7EB;border-radius:8px;padding:0 12px;font-size:13px;color:#374151;background:#fff}
.lv-kpi-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:16px;margin-bottom:24px}
@media(max-width:1100px){.lv-kpi-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:600px){.lv-kpi-grid{grid-template-columns:repeat(2,1fr)}}
.lv-kpi-card{background:#fff;border:1px solid #E5E7EB;border-radius:14px;padding:18px 16px;display:flex;align-items:center;gap:14px;border-bottom:3px solid var(--accent);transition:.2s}
.lv-kpi-card:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,.06)}
.lv-kpi-icon{width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.lv-kpi-val{font-size:26px;font-weight:700;color:#0B1437;line-height:1}
.lv-kpi-label{font-size:12px;font-weight:600;color:#4B5563;margin-top:4px}
.lv-tabs{display:flex;gap:4px;background:#fff;border-radius:12px;padding:6px;border:1px solid #E5E7EB;margin-bottom:20px;flex-wrap:wrap}
.lv-tab{background:none;border:none;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;color:#4B5563;cursor:pointer;transition:.15s;display:flex;align-items:center;gap:4px}
.lv-tab:hover{background:#F9FAFB}
.lv-tab.active{background:var(--lv-primary);color:#fff}
.lv-badge-pill{background:#EF4444;color:#fff;border-radius:20px;font-size:10px;font-weight:700;padding:1px 6px;margin-left:4px}
.lv-pane{display:none}
.lv-pane.active{display:block}
.lv-panel{background:#fff;border:1px solid #E5E7EB;border-radius:14px;padding:24px}
.lv-btn{display:inline-flex;align-items:center;padding:9px 18px;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:.15s}
.lv-btn-primary{background:var(--lv-primary);color:#fff}
.lv-btn-primary:hover{background:var(--lv-primary-dark);color:#fff}
.lv-btn-outline{background:#fff;color:#374151;border:1px solid #E5E7EB}
.lv-btn-outline:hover{background:#F9FAFB}
.lv-btn-sm{padding:6px 14px;font-size:12px}
.lv-input{height:36px;border:1px solid #E5E7EB;border-radius:8px;padding:0 12px;font-size:13px;color:#374151;background:#fff}
.lv-select{height:36px;border:1px solid #E5E7EB;border-radius:8px;padding:0 10px;font-size:13px;color:#374151;background:#fff}
.tm-card{background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:16px;height:100%;display:flex;flex-direction:column;transition:.2s}
.tm-card:hover{border-color:var(--lv-primary);box-shadow:0 4px 12px rgba(249,115,22,.12);transform:translateY(-2px)}
.tm-leave-card{border-color:#FED7AA}
.tm-card-head{display:flex;align-items:center;gap:10px;margin-bottom:8px}
.tm-avatar{width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#F97316,#EA580C);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0}
.tm-avatar-sm{width:30px;height:30px;font-size:11px}
.tm-info{flex:1;min-width:0}
.tm-name{font-size:13px;font-weight:700;color:#0B1437;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tm-id{font-size:11px;color:#8892B0}
.tm-role{font-size:11px;color:#6B7280;margin-bottom:10px}
.tm-footer{margin-top:auto;padding-top:10px}
.tm-leave-meta{font-size:12px;color:#4B5563}
.fw-600{font-weight:600}
.lv-table-wrap{overflow-x:auto;border-radius:12px;border:1px solid #E5E7EB}
.lv-table{width:100%;border-collapse:collapse;font-size:13px;background:#fff}
.lv-table th{background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#6B7280;font-size:11px;font-weight:700;letter-spacing:.05em;padding:11px 14px;text-align:left;text-transform:uppercase;white-space:nowrap}
.lv-table td{border-bottom:1px solid #F3F4F6;color:#0B1437;padding:12px 14px;vertical-align:middle}
.lv-table tbody tr:last-child td{border-bottom:none}
.lv-table tbody tr:hover td{background:#FAFAFA}
.lv-type-badge{background:#EFF6FF;color:#1D4ED8;border-radius:6px;padding:3px 9px;font-size:11px;font-weight:700;white-space:nowrap}
.lv-type-tag{background:#FFF7ED;color:#C2410C;border-radius:6px;padding:2px 8px;font-size:11px;font-weight:600;white-space:nowrap}
.lv-status-badge{border-radius:20px;font-size:11px;font-weight:700;padding:4px 10px;display:inline-flex;white-space:nowrap}
.lv-badge-approved{background:#ECFDF5;color:#047857}
.lv-badge-pending{background:#FFFBEB;color:#B45309}
.lv-badge-declined,.lv-badge-rejected{background:#FEF2F2;color:#B91C1C}
.lv-badge-info{background:#ECFEFF;color:#0E7490}
.lv-badge-off{background:#F1F5F9;color:#64748B}
.lv-badge-absent{background:#1F2937;color:#fff}
.lv-empty-state{text-align:center;padding:48px 24px;color:#8892B0}
.lv-empty-state h5{color:#374151;font-weight:700;margin-bottom:8px}
</style>

<div class="lv-wrap">

@if(session('success'))
<div class="lv-alert lv-alert-success"><i class="fa fa-check-circle me-2"></i>{{ session('success') }}<button onclick="this.parentElement.remove()" class="lv-alert-close">&times;</button></div>
@endif
@if(session('error'))
<div class="lv-alert lv-alert-danger"><i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}<button onclick="this.parentElement.remove()" class="lv-alert-close">&times;</button></div>
@endif

<div class="lv-topbar">
    <div>
        <h1 class="lv-page-title">Team Leaves</h1>
        <p class="lv-page-sub">{{ $year }} &middot; Managing {{ $totalMembers }} reportee(s)</p>
    </div>
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <input type="date" id="dateFilter" value="{{ $today }}" class="lv-date-input">
    </div>
</div>

<div class="lv-kpi-grid">
    <div class="lv-kpi-card" style="--accent:#6366F1">
        <div class="lv-kpi-icon" style="background:#EEF2FF;color:#6366F1"><i class="fa fa-users"></i></div>
        <div class="lv-kpi-body"><div class="lv-kpi-val">{{ $totalMembers }}</div><div class="lv-kpi-label">Team Members</div></div>
    </div>
    <div class="lv-kpi-card" style="--accent:#22C55E">
        <div class="lv-kpi-icon" style="background:#F0FDF4;color:#22C55E"><i class="fa fa-circle-check"></i></div>
        <div class="lv-kpi-body"><div class="lv-kpi-val">{{ $presentCount }}</div><div class="lv-kpi-label">Present Today</div></div>
    </div>
    <div class="lv-kpi-card" style="--accent:#EF4444">
        <div class="lv-kpi-icon" style="background:#FEF2F2;color:#EF4444"><i class="fa fa-calendar-xmark"></i></div>
        <div class="lv-kpi-body"><div class="lv-kpi-val">{{ $onLeaveCount }}</div><div class="lv-kpi-label">On Leave Today</div></div>
    </div>
    <div class="lv-kpi-card" style="--accent:#F59E0B">
        <div class="lv-kpi-icon" style="background:#FFFBEB;color:#F59E0B"><i class="fa fa-hourglass-half"></i></div>
        <div class="lv-kpi-body"><div class="lv-kpi-val">{{ $pendingLvCnt }}</div><div class="lv-kpi-label">Pending Leaves</div></div>
    </div>
    <div class="lv-kpi-card" style="--accent:#06B6D4">
        <div class="lv-kpi-icon" style="background:#ECFEFF;color:#06B6D4"><i class="fa fa-clock"></i></div>
        <div class="lv-kpi-body"><div class="lv-kpi-val">{{ $pendingPrmCnt }}</div><div class="lv-kpi-label">Pending Permissions</div></div>
    </div>
</div>

<div class="lv-tabs">
    <button class="lv-tab active" data-tab="members"><i class="fa fa-users me-1"></i> Reportees</button>
    <button class="lv-tab" data-tab="onleave"><i class="fa fa-calendar-xmark me-1"></i> On Leave @if($onLeaveCount)<span class="lv-badge-pill">{{ $onLeaveCount }}</span>@endif</button>
    <button class="lv-tab" data-tab="leavereq"><i class="fa fa-list-check me-1"></i> Leave Requests @if($pendingLvCnt)<span class="lv-badge-pill">{{ $pendingLvCnt }}</span>@endif</button>
    <button class="lv-tab" data-tab="permreq"><i class="fa fa-clock me-1"></i> Permissions @if($pendingPrmCnt)<span class="lv-badge-pill">{{ $pendingPrmCnt }}</span>@endif</button>
</div>

<div class="lv-tab-content">

{{-- REPORTEES --}}
<div class="lv-pane active" id="tab-members">
    <div class="lv-panel">
        <div class="d-flex gap-2 mb-3">
            <input type="text" id="memberSearch" class="lv-input" placeholder="Search name or ID..." style="max-width:260px">
        </div>
        @if($totalMembers)
        <div class="row g-3">
            @foreach($teamMembers as $member)
            @php
                $isOnLeave = $member->onLeaveToday ?? false;
                $hasAtt    = $member->attendanceToday ?? false;
                $isOff     = !($member->isWorkingDay ?? true);
                $isHoliday = $isOff && str_contains(strtolower($member->workingDayReason ?? ''), 'holiday');
                if ($isOnLeave)     { $badge = ['On Leave','lv-badge-declined']; }
                elseif ($hasAtt)    { $badge = $member->attendanceStatus === 'Half Day' ? ['Half Day','lv-badge-pending'] : ['Present','lv-badge-approved']; }
                elseif ($isHoliday) { $badge = ['Holiday','lv-badge-info']; }
                elseif ($isOff)     { $badge = ['Week Off','lv-badge-off']; }
                else                { $badge = ['Absent','lv-badge-absent']; }
            @endphp
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 member-item"
                 data-name="{{ strtolower($member->firstname.' '.$member->lastname) }}"
                 data-id="{{ strtolower($member->employeeid) }}">
                <div class="tm-card">
                    <div class="tm-card-head">
                        <div class="tm-avatar">{{ substr($member->firstname,0,1) }}{{ substr($member->lastname,0,1) }}</div>
                        <div class="tm-info">
                            <div class="tm-name">{{ $member->firstname }} {{ $member->lastname }}</div>
                            <div class="tm-id">{{ $member->employeeid }}</div>
                        </div>
                        <span class="lv-status-badge {{ $badge[1] }}">{{ $badge[0] }}</span>
                    </div>
                    @php $roleName = $member->designation_name ?? $member->designation; @endphp
                    @if(!empty($roleName))
                    <div class="tm-role"><i class="fa fa-briefcase me-1"></i>{{ is_numeric($roleName) ? '' : $roleName }}</div>
                    @endif
                    <div class="tm-footer">
                        <a href="{{ route('team-leaves.show', $member->id) }}" class="lv-btn lv-btn-outline lv-btn-sm w-100">
                            <i class="fa fa-eye me-1"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="lv-empty-state"><i class="fa fa-users-slash fa-3x mb-3"></i><h5>No Reportees Found</h5><p>No employees are assigned to you.</p></div>
        @endif
    </div>
</div>

{{-- ON LEAVE --}}
<div class="lv-pane" id="tab-onleave">
    <div class="lv-panel">
        @php $membersOnLeave = collect($teamMembers)->filter(fn($m) => $m->onLeaveToday); @endphp
        @if($membersOnLeave->count())
        <div class="row g-3">
            @foreach($membersOnLeave as $member)
            @php $lv = $member->onLeaveToday; @endphp
            <div class="col-xl-4 col-lg-6 col-md-6">
                <div class="tm-card tm-leave-card">
                    <div class="tm-card-head">
                        <div class="tm-avatar">{{ substr($member->firstname,0,1) }}{{ substr($member->lastname,0,1) }}</div>
                        <div class="tm-info">
                            <div class="tm-name">{{ $member->firstname }} {{ $member->lastname }}</div>
                            <div class="tm-id">{{ $member->employeeid }}</div>
                        </div>
                        <span class="lv-type-tag">{{ $lv->leave_type }}</span>
                    </div>
                    <div class="tm-leave-meta mt-2">
                        <span><i class="fa fa-calendar me-1 text-muted"></i>{{ \Carbon\Carbon::parse($lv->from_date)->format('d M') }} – {{ \Carbon\Carbon::parse($lv->to_date)->format('d M') }}</span>
                        <span class="ms-3"><i class="fa fa-sun me-1 text-muted"></i>{{ $lv->no_of_days }} day(s)</span>
                    </div>
                    <div class="tm-footer mt-2">
                        <a href="{{ route('team-leaves.show', $member->id) }}" class="lv-btn lv-btn-outline lv-btn-sm w-100"><i class="fa fa-eye me-1"></i> View Details</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="lv-empty-state"><i class="fa fa-calendar-check fa-3x mb-3" style="color:#22C55E"></i><h5>Nobody on Leave Today</h5><p>All team members are present.</p></div>
        @endif
    </div>
</div>

{{-- LEAVE REQUESTS --}}
<div class="lv-pane" id="tab-leavereq">
    <div class="lv-panel">
        <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
            <input type="text" id="lvSearch" class="lv-input" placeholder="Search name / ID..." style="max-width:220px">
            <select id="lvType" class="lv-select" style="width:160px">
                <option value="">All Types</option>
                <option value="casual leave">Casual Leave</option>
                <option value="sick">Sick</option>
                <option value="hospitalisation">Hospitalisation</option>
                <option value="maternity leave">Maternity Leave</option>
                <option value="paternity leave">Paternity Leave</option>
                <option value="lop">LOP</option>
            </select>
            <select id="lvStatus" class="lv-select" style="width:140px">
                <option value="pending" selected>Pending</option>
                <option value="">All Status</option>
                <option value="approved">Approved</option>
                <option value="declined">Declined</option>
            </select>
            <button id="lvReset" class="lv-btn lv-btn-outline lv-btn-sm"><i class="fa fa-rotate-left me-1"></i>Reset</button>
        </div>

        @if(count($allLeaveRequests ?? []))
        <div class="lv-table-wrap">
            <table class="lv-table" id="lvTable">
                <thead>
                    <tr>
                        <th>Employee</th><th>Leave Type</th><th>Period</th><th>Days</th><th>Paid</th><th>LOP</th><th>Reason</th>
                        @if($isAdmin)
                            <th>TL</th><th>HR</th><th>Manager</th>
                        @elseif($viewIsTL && $viewIsMgr)
                            <th>TL Approval</th><th>Manager Approval</th>
                        @elseif($viewIsTL)
                            <th>TL Approval</th>
                        @elseif($viewIsHR)
                            <th>HR Approval</th>
                        @elseif($viewIsMgr)
                            <th>Manager Approval</th>
                        @endif
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($allLeaveRequests as $item)
                @php $lvUrl = url('admin-leaves/'.$item['leave']->id.'/update-status'); @endphp
                <tr data-name="{{ strtolower($item['member']->firstname.' '.$item['member']->lastname) }}"
                    data-id="{{ strtolower($item['member']->employeeid) }}"
                    data-type="{{ strtolower($item['leave']->leave_type) }}"
                    data-status="{{ $item['overall_status'] }}">
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="tm-avatar tm-avatar-sm">{{ substr($item['member']->firstname,0,1) }}{{ substr($item['member']->lastname,0,1) }}</div>
                            <div><div class="fw-600">{{ $item['member']->firstname }} {{ $item['member']->lastname }}</div><div style="font-size:11px;color:#8892B0">{{ $item['member']->employeeid }}</div></div>
                        </div>
                    </td>
                    <td><span class="lv-type-badge">{{ $item['leave']->leave_type }}</span></td>
                    <td style="font-size:12px">{{ \Carbon\Carbon::parse($item['leave']->from_date)->format('d M Y') }}<br><span style="color:#8892B0">to {{ \Carbon\Carbon::parse($item['leave']->to_date)->format('d M Y') }}</span></td>
                    <td><strong>{{ $item['leave']->no_of_days }}</strong></td>
                    <td><strong style="color:#10B981">{{ $item['leave']->paid_days ?? '-' }}</strong></td>
                    <td><strong style="color:#EF4444">{{ $item['leave']->lop_days ?? '-' }}</strong></td>
                    <td style="font-size:12px;max-width:140px">{{ \Illuminate\Support\Str::limit($item['leave']->leave_reason, 35) }}</td>
                    @if($isAdmin)
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'tl','value'=>$item['leave']->tl_approved,'approverName'=>$item['tl_name']??null,'url'=>$lvUrl,'canChange'=>true])</td>
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'hr','value'=>$item['leave']->hr_approved,'approverName'=>$item['hr_name']??null,'url'=>$lvUrl,'canChange'=>true])</td>
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'manager','value'=>$item['leave']->manager_approved,'approverName'=>$item['manager_name']??null,'url'=>$lvUrl,'canChange'=>true])</td>
                    @elseif($viewIsTL && $viewIsMgr)
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'tl','value'=>$item['leave']->tl_approved,'approverName'=>$item['tl_name']??null,'url'=>$lvUrl,'canChange'=>true])</td>
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'manager','value'=>$item['leave']->manager_approved,'approverName'=>$item['manager_name']??null,'url'=>$lvUrl,'canChange'=>true])</td>
                    @elseif($viewIsTL)
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'tl','value'=>$item['leave']->tl_approved,'approverName'=>$item['tl_name']??null,'url'=>$lvUrl,'canChange'=>true])</td>
                    @elseif($viewIsHR)
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'hr','value'=>$item['leave']->hr_approved,'approverName'=>$item['hr_name']??null,'url'=>$lvUrl,'canChange'=>true])</td>
                    @elseif($viewIsMgr)
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'manager','value'=>$item['leave']->manager_approved,'approverName'=>$item['manager_name']??null,'url'=>$lvUrl,'canChange'=>true])</td>
                    @endif
                    <td>
                        <span class="lv-status-badge lv-badge-{{ $item['overall_status'] }}">{{ ucfirst($item['overall_status']) }}</span>
                        <div style="font-size:10px;color:#8892B0;margin-top:2px">
                            {{ ($item['leave']->tl_approved=='approved'?1:0)+($item['leave']->hr_approved=='approved'?1:0)+($item['leave']->manager_approved=='approved'?1:0) }}/3
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="lv-empty-state"><i class="fa fa-inbox fa-3x mb-3"></i><h5>No Leave Requests</h5></div>
        @endif
    </div>
</div>

{{-- PERMISSIONS --}}
<div class="lv-pane" id="tab-permreq">
    <div class="lv-panel">
        <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
            <input type="text" id="pmSearch" class="lv-input" placeholder="Search name / ID..." style="max-width:220px">
            <select id="pmStatus" class="lv-select" style="width:140px">
                <option value="pending" selected>Pending</option>
                <option value="">All Status</option>
                <option value="approved">Approved</option>
                <option value="declined">Declined</option>
            </select>
            <button id="pmReset" class="lv-btn lv-btn-outline lv-btn-sm"><i class="fa fa-rotate-left me-1"></i>Reset</button>
        </div>

        @php
            $pmList = $allPermissionsWithStatus ?? collect();
            // Check which approval columns have any non-null/non-pending value (for approver name display)
            // and pre-build approver name lookup
            $pmApproverCache = [];
            foreach ($pmList as $p) {
                // Only manager_approved_by exists in employee_permissions
                $vid = $p->manager_approved_by ?? null;
                if ($vid && !isset($pmApproverCache[$vid])) {
                    $apEmp = collect($teamMembers)->firstWhere('id', $vid);
                    if (!$apEmp) {
                        $apEmp = DB::table('allemployees')->where('id',$vid)->first();
                    }
                    $pmApproverCache[$vid] = $apEmp ? trim($apEmp->firstname.' '.($apEmp->lastname ?? '')) : null;
                }
            }
        @endphp
        @if($pmList->count())
        <div class="lv-table-wrap">
            <table class="lv-table" id="pmTable">
                <thead>
                    <tr>
                        <th>Employee</th><th>Date</th><th>Time</th><th>Duration</th><th>Reason</th>
                        @if($isAdmin)
                            <th>TL</th><th>HR</th><th>Manager</th>
                        @elseif($viewIsTL && $viewIsMgr)
                            <th>TL Approval</th><th>Manager Approval</th>
                        @elseif($viewIsTL)
                            <th>TL Approval</th>
                        @elseif($viewIsHR)
                            <th>HR Approval</th>
                        @elseif($viewIsMgr)
                            <th>Manager Approval</th>
                        @endif
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($pmList as $p)
                @php
                    $emp      = collect($teamMembers)->firstWhere('id', $p->employee_id);
                    $pmUrl    = route('admin-permissions.update-status', $p->id);
                    $tlName   = null; // tl_approved_by not in employee_permissions
                    $hrName   = null; // hr_approved_by not in employee_permissions
                    $mgrName  = isset($p->manager_approved_by) ? ($pmApproverCache[$p->manager_approved_by] ?? null) : null;
                @endphp
                <tr data-name="{{ strtolower($emp ? $emp->firstname.' '.$emp->lastname : '') }}"
                    data-id="{{ strtolower($emp->employeeid ?? '') }}"
                    data-status="{{ strtolower($p->overall_status) }}">
                    <td>
                        @if($emp)
                        <div class="d-flex align-items-center gap-2">
                            <div class="tm-avatar tm-avatar-sm">{{ substr($emp->firstname,0,1) }}{{ substr($emp->lastname,0,1) }}</div>
                            <div><div class="fw-600">{{ $emp->firstname }} {{ $emp->lastname }}</div><div style="font-size:11px;color:#8892B0">{{ $emp->employeeid }}</div></div>
                        </div>
                        @else <span class="text-muted">Unknown</span> @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($p->permission_date)->format('d M Y') }}</td>
                    <td style="font-size:12px">{{ \Carbon\Carbon::parse($p->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($p->end_time)->format('h:i A') }}</td>
                    <td>{{ $p->duration }} hr</td>
                    <td style="font-size:12px;max-width:140px">{{ \Illuminate\Support\Str::limit($p->permission_reason, 35) }}</td>
                    @if($isAdmin)
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'tl','value'=>$p->tl_approved,'approverName'=>$tlName,'url'=>$pmUrl,'canChange'=>true])</td>
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'hr','value'=>$p->hr_approved,'approverName'=>$hrName,'url'=>$pmUrl,'canChange'=>true])</td>
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'manager','value'=>$p->manager_approved,'approverName'=>$mgrName,'url'=>$pmUrl,'canChange'=>true])</td>
                    @elseif($viewIsTL && $viewIsMgr)
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'tl','value'=>$p->tl_approved,'approverName'=>$tlName,'url'=>$pmUrl,'canChange'=>true])</td>
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'manager','value'=>$p->manager_approved,'approverName'=>$mgrName,'url'=>$pmUrl,'canChange'=>true])</td>
                    @elseif($viewIsTL)
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'tl','value'=>$p->tl_approved,'approverName'=>$tlName,'url'=>$pmUrl,'canChange'=>true])</td>
                    @elseif($viewIsHR)
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'hr','value'=>$p->hr_approved,'approverName'=>$hrName,'url'=>$pmUrl,'canChange'=>true])</td>
                    @elseif($viewIsMgr)
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'manager','value'=>$p->manager_approved,'approverName'=>$mgrName,'url'=>$pmUrl,'canChange'=>true])</td>
                    @endif
                    <td>
                        <span class="lv-status-badge lv-badge-{{ $p->overall_status }}">{{ ucfirst($p->overall_status) }}</span>
                        <div style="font-size:10px;color:#8892B0;margin-top:2px">
                            {{ ($p->tl_approved=='approved'?1:0)+($p->hr_approved=='approved'?1:0)+($p->manager_approved=='approved'?1:0) }}/3
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="lv-empty-state"><i class="fa fa-inbox fa-3x mb-3"></i><h5>No Permission Requests</h5></div>
        @endif
    </div>
</div>

</div>{{-- end lv-tab-content --}}
</div>{{-- end lv-wrap --}}

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.lv-tab').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.lv-tab').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.lv-pane').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('tab-' + this.dataset.tab).classList.add('active');
            localStorage.setItem('tl_tab', this.dataset.tab);
        });
    });
    var saved = localStorage.getItem('tl_tab') || 'members';
    var tabBtn = document.querySelector('.lv-tab[data-tab="' + saved + '"]');
    if (tabBtn) tabBtn.click();

    var dateFilter = document.getElementById('dateFilter');
    if (dateFilter) {
        dateFilter.addEventListener('change', function() {
            var url = new URL(window.location.href);
            url.searchParams.set('date', this.value);
            window.location.href = url.toString();
        });
    }

    var memberSearch = document.getElementById('memberSearch');
    if (memberSearch) {
        memberSearch.addEventListener('input', function() {
            var v = this.value.toLowerCase();
            document.querySelectorAll('.member-item').forEach(function(el) {
                el.style.display = (!v || el.dataset.name.includes(v) || el.dataset.id.includes(v)) ? '' : 'none';
            });
        });
    }

    function filterTable(tableId, searchId, typeId, statusId) {
        var rows = document.querySelectorAll('#' + tableId + ' tbody tr');
        var sv  = (document.getElementById(searchId)  || {value:''}).value.toLowerCase();
        var tv  = typeId ? (document.getElementById(typeId) || {value:''}).value.toLowerCase() : '';
        var stv = (document.getElementById(statusId) || {value:''}).value.toLowerCase();
        rows.forEach(function(r) {
            var ms  = !sv  || (r.dataset.name||'').includes(sv) || (r.dataset.id||'').includes(sv);
            var mt  = !tv  || (r.dataset.type||'') === tv;
            var mst = !stv || (r.dataset.status||'') === stv;
            r.style.display = (ms && mt && mst) ? '' : 'none';
        });
    }

    ['input','change'].forEach(function(ev) {
        ['lvSearch','lvType','lvStatus'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.addEventListener(ev, function(){ filterTable('lvTable','lvSearch','lvType','lvStatus'); });
        });
        ['pmSearch','pmStatus'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.addEventListener(ev, function(){ filterTable('pmTable','pmSearch',null,'pmStatus'); });
        });
    });

    filterTable('lvTable','lvSearch','lvType','lvStatus');
    filterTable('pmTable','pmSearch',null,'pmStatus');

    var lvReset = document.getElementById('lvReset');
    if (lvReset) lvReset.addEventListener('click', function() {
        document.getElementById('lvSearch').value = '';
        document.getElementById('lvType').value   = '';
        document.getElementById('lvStatus').value = 'pending';
        filterTable('lvTable','lvSearch','lvType','lvStatus');
    });

    var pmReset = document.getElementById('pmReset');
    if (pmReset) pmReset.addEventListener('click', function() {
        document.getElementById('pmSearch').value  = '';
        document.getElementById('pmStatus').value  = 'pending';
        filterTable('pmTable','pmSearch',null,'pmStatus');
    });
});
</script>
@endsection
