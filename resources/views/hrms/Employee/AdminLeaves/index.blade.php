@php
    $permissions           = App\Helpers\PermissionHelper::getPermissions('Leaves (Admin)');
    $permissionPermissions = App\Helpers\PermissionHelper::getPermissions('Permissions (Admin)');
    $activeTab = request('request_type') === 'permission' ? 'permissions' : 'leaves';

    $totalLeaves      = $leaves->count();
    $pendingLeaves    = $leaves->where('status','pending')->count();
    $approvedLeaves   = $leaves->where('status','approved')->count();
    $declinedLeaves   = $leaves->whereIn('status',['declined'])->count();
    $totalPerms       = $employeePermissions->count();
    $pendingPerms     = $employeePermissions->where('status','pending')->count();
@endphp
@extends('layouts.index')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="al-wrap">

{{-- ── FLASH ────────────────────────────────────────────────────────────────── --}}
@if(session('success'))
<div class="al-alert al-alert-success"><i class="fa fa-check-circle me-2"></i>{{ session('success') }}<button onclick="this.parentElement.remove()" class="al-alert-close">&times;</button></div>
@endif
@if(session('error'))
<div class="al-alert al-alert-danger"><i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}<button onclick="this.parentElement.remove()" class="al-alert-close">&times;</button></div>
@endif

{{-- ── TOP BAR ─────────────────────────────────────────────────────────────── --}}
<div class="al-topbar">
    <div>
        <h1 class="al-page-title">Leave & Permission Management</h1>
        <p class="al-page-sub">Admin · All Employees · {{ now()->format('d M Y') }}</p>
    </div>
</div>

{{-- ── KPI CARDS ───────────────────────────────────────────────────────────── --}}
<div class="al-kpi-grid">
    <div class="al-kpi" style="--ac:#6366F1">
        <div class="al-kpi-icon" style="background:#EEF2FF;color:#6366F1"><i class="fa fa-calendar-days"></i></div>
        <div><div class="al-kpi-val">{{ $totalLeaves }}</div><div class="al-kpi-lbl">Total Leaves</div><div class="al-kpi-sub">All requests</div></div>
    </div>
    <div class="al-kpi" style="--ac:#F59E0B">
        <div class="al-kpi-icon" style="background:#FFFBEB;color:#F59E0B"><i class="fa fa-hourglass-half"></i></div>
        <div><div class="al-kpi-val">{{ $pendingLeaves }}</div><div class="al-kpi-lbl">Pending Leaves</div><div class="al-kpi-sub">Awaiting action</div></div>
    </div>
    <div class="al-kpi" style="--ac:#10B981">
        <div class="al-kpi-icon" style="background:#ECFDF5;color:#10B981"><i class="fa fa-thumbs-up"></i></div>
        <div><div class="al-kpi-val">{{ $approvedLeaves }}</div><div class="al-kpi-lbl">Approved Leaves</div><div class="al-kpi-sub">Processed</div></div>
    </div>
    <div class="al-kpi" style="--ac:#EF4444">
        <div class="al-kpi-icon" style="background:#FEF2F2;color:#EF4444"><i class="fa fa-thumbs-down"></i></div>
        <div><div class="al-kpi-val">{{ $declinedLeaves }}</div><div class="al-kpi-lbl">Declined Leaves</div><div class="al-kpi-sub">Rejected</div></div>
    </div>
    <div class="al-kpi" style="--ac:#F97316">
        <div class="al-kpi-icon" style="background:#FFF7ED;color:#F97316"><i class="fa fa-clock"></i></div>
        <div><div class="al-kpi-val">{{ $totalPerms }}</div><div class="al-kpi-lbl">Permissions</div><div class="al-kpi-sub">{{ $pendingPerms }} pending</div></div>
    </div>
    <div class="al-kpi" style="--ac:#8B5CF6">
        <div class="al-kpi-icon" style="background:#F5F3FF;color:#8B5CF6"><i class="fa fa-calendar-xmark"></i></div>
        <div><div class="al-kpi-val">{{ $plannedLeaves }}</div><div class="al-kpi-lbl">Planned Leaves</div><div class="al-kpi-sub">Future dates</div></div>
    </div>
</div>

{{-- ── FILTER BAR ──────────────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('admin-leaves.index') }}" id="filterForm">
<div class="al-filter-bar">
    <div class="al-filter-grid">
        <input type="text" name="employee_name" class="al-input" placeholder="🔍 Employee name…" value="{{ request('employee_name') }}">
        <select name="request_type" id="requestTypeFilter" class="al-select">
            <option value="">All Types</option>
            <option value="leave"      {{ request('request_type')=='leave'?'selected':'' }}>Leaves</option>
            <option value="permission" {{ request('request_type')=='permission'?'selected':'' }}>Permissions</option>
        </select>
        <select name="leave_status" class="al-select">
            <option value="">All Status</option>
            <option value="pending"  {{ request('leave_status')=='pending'?'selected':'' }}>Pending</option>
            <option value="approved" {{ request('leave_status')=='approved'?'selected':'' }}>Approved</option>
            <option value="declined" {{ request('leave_status')=='declined'?'selected':'' }}>Declined</option>
        </select>
        <input type="date" name="from_date" class="al-input" value="{{ request('from_date') }}" placeholder="From">
        <input type="date" name="to_date"   class="al-input" value="{{ request('to_date') }}"   placeholder="To">
        <div class="d-flex gap-2">
            <button type="submit" class="al-btn al-btn-primary"><i class="fa fa-search me-1"></i> Search</button>
            <a href="{{ route('admin-leaves.index') }}" class="al-btn al-btn-outline"><i class="fa fa-rotate-left me-1"></i> Reset</a>
        </div>
    </div>
</div>
</form>

{{-- ── TABS ─────────────────────────────────────────────────────────────────── --}}
<div class="al-tabs">
    <button class="al-tab {{ $activeTab=='leaves'?'active':'' }}" data-tab="leaves">
        <i class="fa fa-calendar-day me-1"></i> Leave Requests
        <span class="al-tab-count {{ $pendingLeaves>0?'al-count-warn':'' }}">{{ $totalLeaves }}</span>
    </button>
    <button class="al-tab {{ $activeTab=='permissions'?'active':'' }}" data-tab="permissions">
        <i class="fa fa-clock me-1"></i> Permission Requests
        <span class="al-tab-count {{ $pendingPerms>0?'al-count-warn':'' }}">{{ $totalPerms }}</span>
    </button>
</div>

{{-- ── LEAVE TABLE ─────────────────────────────────────────────────────────── --}}
<div class="al-pane {{ $activeTab=='leaves'?'active':'' }}" id="pane-leaves">
    @if($leaves->count())
    {{-- Bulk action bar --}}
    <div class="al-bulk-bar" id="leaveBulkBar" style="display:none">
        <span id="leaveSelectedCount">0</span> selected &nbsp;·&nbsp;
        <button class="al-btn al-btn-sm al-btn-green"  onclick="bulkLeave('approved')"><i class="fa fa-check me-1"></i>Approve All</button>
        <button class="al-btn al-btn-sm al-btn-danger" onclick="bulkLeave('declined')"><i class="fa fa-times me-1"></i>Decline All</button>
    </div>
    <div class="al-table-wrap">
        <table class="al-table" id="leaveTable">
            <thead>
                <tr>
                    <th style="width:36px"><input type="checkbox" id="selectAllLeaves" class="al-check"></th>
                    <th>Employee</th>
                    <th>Leave Type</th>
                    <th>Period</th>
                    <th>Days</th>
                    <th>Reason</th>
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
                <tr>
                    <td><input type="checkbox" class="al-check leave-check" value="{{ $leave->id }}"></td>
                    <td>
                        <div class="al-emp-cell">
                            <div class="al-avatar">{{ strtoupper(substr($leave->employee_name,0,2)) }}</div>
                            <div>
                                <div class="al-emp-name">{{ $leave->employee_name }}</div>
                                <div class="al-emp-des">{{ $leave->designation_name }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="al-type-badge">{{ $leave->leave_type }}</span></td>
                    <td>
                        <div style="font-size:13px;font-weight:600">{{ \Carbon\Carbon::parse($leave->from_date)->format('d M Y') }}</div>
                        <div style="font-size:11px;color:#8892B0">to {{ \Carbon\Carbon::parse($leave->to_date)->format('d M Y') }}</div>
                    </td>
                    <td><strong>{{ $leave->no_of_days }}</strong></td>
                    <td style="max-width:140px;font-size:12px;color:#4B5563">{{ \Illuminate\Support\Str::limit($leave->leave_reason,35) }}</td>
                    <td>@include('hrms.Employee.AdminLeaves.partials.apill', ['val'=>$leave->tl_approved??null,'name'=>$tlName])</td>
                    <td>@include('hrms.Employee.AdminLeaves.partials.apill', ['val'=>$leave->hr_approved??null,'name'=>$hrName])</td>
                    <td>@include('hrms.Employee.AdminLeaves.partials.apill', ['val'=>$leave->manager_approved??null,'name'=>$mgrName])</td>
                    <td>
                        <span class="al-badge al-badge-{{ $leave->status }}">{{ ucfirst($leave->status) }}</span>
                        <div style="font-size:10px;color:#8892B0;margin-top:2px">
                            {{ ($leave->tl_approved=='approved'?1:0)+($leave->hr_approved=='approved'?1:0)+($leave->manager_approved=='approved'?1:0) }}/3
                        </div>
                    </td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            <button class="al-act-btn al-act-approve" title="Approve" onclick="quickLeaveStatus({{ $leave->id }},'approved')"><i class="fa fa-check"></i></button>
                            <button class="al-act-btn al-act-decline" title="Decline" onclick="quickLeaveStatus({{ $leave->id }},'declined')"><i class="fa fa-times"></i></button>
                            <a href="{{ route('admin-leaves.show',$leave->id) }}" class="al-act-btn al-act-view" title="View"><i class="fa fa-eye"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="al-empty">
        <i class="fa fa-calendar-xmark fa-3x mb-3" style="color:#E5E7EB"></i>
        <h5>No Leave Requests</h5>
        <p>No records match the current filters.</p>
        @if(request()->hasAny(['employee_name','request_type','leave_status','from_date','to_date']))
        <a href="{{ route('admin-leaves.index') }}" class="al-btn al-btn-outline mt-2">Clear Filters</a>
        @endif
    </div>
    @endif
</div>

{{-- ── PERMISSION TABLE ────────────────────────────────────────────────────── --}}
<div class="al-pane {{ $activeTab=='permissions'?'active':'' }}" id="pane-permissions">
    @if($employeePermissions->count())
    <div class="al-bulk-bar" id="permBulkBar" style="display:none">
        <span id="permSelectedCount">0</span> selected &nbsp;·&nbsp;
        <button class="al-btn al-btn-sm al-btn-green"  onclick="bulkPerm('approved')"><i class="fa fa-check me-1"></i>Approve All</button>
        <button class="al-btn al-btn-sm al-btn-danger" onclick="bulkPerm('declined')"><i class="fa fa-times me-1"></i>Decline All</button>
    </div>
    <div class="al-table-wrap">
        <table class="al-table" id="permTable">
            <thead>
                <tr>
                    <th style="width:36px"><input type="checkbox" id="selectAllPerms" class="al-check"></th>
                    <th>Employee</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Duration</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employeePermissions as $perm)
                <tr>
                    <td><input type="checkbox" class="al-check perm-check" value="{{ $perm->id }}"></td>
                    <td>
                        <div class="al-emp-cell">
                            <div class="al-avatar">{{ strtoupper(substr($perm->employee_name,0,2)) }}</div>
                            <div>
                                <div class="al-emp-name">{{ $perm->employee_name }}</div>
                                <div class="al-emp-des">{{ $perm->designation_name }}</div>
                            </div>
                        </div>
                    </td>
                    <td><strong>{{ \Carbon\Carbon::parse($perm->permission_date)->format('d M Y') }}</strong></td>
                    <td style="font-size:12px">
                        {{ \Carbon\Carbon::parse($perm->start_time)->format('h:i A') }} –
                        {{ \Carbon\Carbon::parse($perm->end_time)->format('h:i A') }}
                    </td>
                    <td><strong>{{ $perm->duration }}</strong> hr(s)</td>
                    <td style="max-width:140px;font-size:12px;color:#4B5563">{{ \Illuminate\Support\Str::limit($perm->permission_reason,35) }}</td>
                    <td><span class="al-badge al-badge-{{ $perm->status }}">{{ ucfirst($perm->status) }}</span></td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            <button class="al-act-btn al-act-approve" title="Approve" onclick="quickPermStatus({{ $perm->id }},'approved')"><i class="fa fa-check"></i></button>
                            <button class="al-act-btn al-act-decline" title="Decline"  onclick="quickPermStatus({{ $perm->id }},'declined')"><i class="fa fa-times"></i></button>
                            <a href="{{ route('admin-permissions.show',$perm->id) }}" class="al-act-btn al-act-view" title="View"><i class="fa fa-eye"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="al-empty">
        <i class="fa fa-clock fa-3x mb-3" style="color:#E5E7EB"></i>
        <h5>No Permission Requests</h5>
        <p>No records match the current filters.</p>
    </div>
    @endif
</div>

</div>{{-- end al-wrap --}}

{{-- ── CONFIRM MODAL ───────────────────────────────────────────────────────── --}}
<div id="confirmModal" style="display:none;position:fixed;inset:0;background:rgba(11,20,55,.45);z-index:9999;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:16px;padding:28px 32px;max-width:380px;width:90%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.15);font-family:'DM Sans',sans-serif">
        <div id="confirmIcon" style="width:54px;height:54px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:22px"></div>
        <h5 id="confirmTitle" style="font-weight:700;color:#0B1437;margin-bottom:8px"></h5>
        <p id="confirmMsg" style="color:#8892B0;font-size:13px;margin-bottom:20px"></p>
        <div style="display:flex;gap:12px;justify-content:center">
            <button onclick="document.getElementById('confirmModal').style.display='none'" style="padding:9px 22px;border:1px solid #E5E7EB;border-radius:8px;background:#fff;cursor:pointer;font-weight:600;font-size:13px">Cancel</button>
            <button id="confirmOk" style="padding:9px 22px;border:none;border-radius:8px;cursor:pointer;font-weight:700;font-size:13px;color:#fff"></button>
        </div>
    </div>
</div>

<style>
:root{--al-primary:#F97316;--al-primary-dark:#EA580C}
.al-wrap{background:#F5F6FA;min-height:100vh;padding:24px 32px;font-family:'DM Sans',sans-serif}
@media(max-width:767px){.al-wrap{padding:12px 10px}}

/* alerts */
.al-alert{display:flex;align-items:center;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:14px;font-weight:500;position:relative}
.al-alert-success{background:#ECFDF5;color:#047857;border:1px solid #6EE7B7}
.al-alert-danger{background:#FEF2F2;color:#B91C1C;border:1px solid #FCA5A5}
.al-alert-close{position:absolute;right:12px;background:none;border:none;font-size:18px;cursor:pointer;color:inherit;font-weight:700}

/* topbar */
.al-topbar{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:24px}
.al-page-title{font-size:22px;font-weight:700;color:#0B1437;margin:0}
.al-page-sub{font-size:13px;color:#8892B0;margin:2px 0 0}

/* buttons */
.al-btn{display:inline-flex;align-items:center;padding:9px 18px;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:.15s;white-space:nowrap}
.al-btn-primary{background:var(--al-primary);color:#fff}
.al-btn-primary:hover{background:var(--al-primary-dark);color:#fff}
.al-btn-outline{background:#fff;color:#374151;border:1px solid #E5E7EB}
.al-btn-outline:hover{background:#F9FAFB}
.al-btn-green{background:#ECFDF5;color:#047857;border:1px solid #6EE7B7}
.al-btn-danger{background:#FEF2F2;color:#B91C1C;border:1px solid #FCA5A5}
.al-btn-sm{padding:6px 14px;font-size:12px}

/* KPI */
.al-kpi-grid{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:16px;margin-bottom:24px}
@media(max-width:1200px){.al-kpi-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:600px){.al-kpi-grid{grid-template-columns:repeat(2,1fr)}}
.al-kpi{background:#fff;border:1px solid #E5E7EB;border-radius:14px;padding:18px 16px;display:flex;align-items:center;gap:14px;border-bottom:3px solid var(--ac);transition:.2s}
.al-kpi:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,.06)}
.al-kpi-icon{width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.al-kpi-val{font-size:26px;font-weight:700;color:#0B1437;line-height:1}
.al-kpi-lbl{font-size:12px;font-weight:600;color:#4B5563;margin-top:4px}
.al-kpi-sub{font-size:11px;color:#8892B0;margin-top:2px}

/* filter */
.al-filter-bar{background:#fff;border:1px solid #E5E7EB;border-radius:14px;padding:16px 20px;margin-bottom:20px}
.al-filter-grid{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.al-input,.al-select{height:38px;border:1px solid #E5E7EB;border-radius:8px;padding:0 12px;font-size:13px;color:#374151;background:#fff;min-width:140px;flex:1}
.al-input:focus,.al-select:focus{border-color:var(--al-primary);outline:none}

/* tabs */
.al-tabs{display:flex;gap:4px;background:#fff;border-radius:12px;padding:6px;border:1px solid #E5E7EB;margin-bottom:20px;flex-wrap:wrap}
.al-tab{background:none;border:none;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;color:#4B5563;cursor:pointer;transition:.15s;display:flex;align-items:center;gap:6px}
.al-tab:hover{background:#F9FAFB}
.al-tab.active{background:var(--al-primary);color:#fff}
.al-tab-count{background:rgba(0,0,0,.12);color:inherit;border-radius:20px;font-size:11px;font-weight:700;padding:1px 7px}
.al-tab.active .al-tab-count{background:rgba(255,255,255,.25);color:#fff}
.al-count-warn{background:#FEF2F2!important;color:#B91C1C!important}
.al-tab.active .al-count-warn{background:rgba(255,255,255,.2)!important;color:#fff!important}

/* pane */
.al-pane{display:none}
.al-pane.active{display:block}

/* bulk bar */
.al-bulk-bar{display:flex;align-items:center;gap:10px;background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;padding:10px 16px;margin-bottom:12px;font-size:13px;font-weight:600;color:#1D4ED8;flex-wrap:wrap}

/* table */
.al-table-wrap{overflow-x:auto;border-radius:12px;border:1px solid #E5E7EB}
.al-table{width:100%;border-collapse:collapse;font-size:13px;background:#fff}
.al-table th{background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#6B7280;font-size:11px;font-weight:700;letter-spacing:.05em;padding:11px 14px;text-align:left;text-transform:uppercase;white-space:nowrap}
.al-table td{border-bottom:1px solid #F3F4F6;color:#0B1437;padding:12px 14px;vertical-align:middle}
.al-table tbody tr:last-child td{border-bottom:none}
.al-table tbody tr:hover td{background:#FAFAFA}

/* employee cell */
.al-emp-cell{display:flex;align-items:center;gap:10px}
.al-avatar{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#F97316,#FBBF24);color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0}
.al-emp-name{font-size:13px;font-weight:600;color:#0B1437}
.al-emp-des{font-size:11px;color:#8892B0}

/* badges */
.al-type-badge{background:#EEF2FF;color:#4338CA;border-radius:6px;padding:3px 9px;font-size:11px;font-weight:700;white-space:nowrap}
.al-badge{border-radius:20px;font-size:11px;font-weight:700;padding:4px 10px;display:inline-flex;white-space:nowrap}
.al-badge-approved{background:#ECFDF5;color:#047857}
.al-badge-pending{background:#FFFBEB;color:#B45309}
.al-badge-declined{background:#FEF2F2;color:#B91C1C}

/* approval pill */
.al-apill{border-radius:20px;font-size:10px;font-weight:700;padding:3px 8px;display:inline-flex;flex-direction:column;align-items:center;gap:1px;white-space:nowrap}
.al-apill-approved{background:#ECFDF5;color:#047857}
.al-apill-pending{background:#FFFBEB;color:#B45309}
.al-apill-declined{background:#FEF2F2;color:#B91C1C}
.al-apill-null{background:#F3F4F6;color:#9CA3AF}
.al-apill-name{font-size:9px;font-weight:500;opacity:.8;max-width:70px;overflow:hidden;text-overflow:ellipsis}

/* action buttons */
.al-act-btn{width:28px;height:28px;border-radius:6px;border:1px solid #E5E7EB;background:#fff;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;font-size:12px;transition:.15s;text-decoration:none;color:#374151}
.al-act-approve:hover{background:#ECFDF5;border-color:#6EE7B7;color:#10B981}
.al-act-decline:hover{background:#FEF2F2;border-color:#FCA5A5;color:#EF4444}
.al-act-view:hover{background:#EFF6FF;border-color:#BFDBFE;color:#1D4ED8}

/* checkbox */
.al-check{width:15px;height:15px;cursor:pointer;accent-color:var(--al-primary)}

/* empty */
.al-empty{text-align:center;padding:56px 24px;color:#8892B0}
.al-empty h5{color:#374151;font-weight:700;margin-bottom:8px}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
var _csrf = '{{ csrf_token() }}';

// ── Tab switching ──
document.querySelectorAll('.al-tab').forEach(function(btn){
    btn.addEventListener('click', function(){
        document.querySelectorAll('.al-tab').forEach(b=>b.classList.remove('active'));
        document.querySelectorAll('.al-pane').forEach(p=>p.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('pane-'+this.dataset.tab).classList.add('active');
        localStorage.setItem('al_tab', this.dataset.tab);
    });
});

// ── Restore tab ──
(function(){
    var saved = localStorage.getItem('al_tab') || '{{ $activeTab }}';
    var btn = document.querySelector('.al-tab[data-tab="'+saved+'"]');
    if(btn){ btn.classList.remove('active'); btn.click(); }
})();

// ── Select all checkboxes ──
document.getElementById('selectAllLeaves') && document.getElementById('selectAllLeaves').addEventListener('change', function(){
    document.querySelectorAll('.leave-check').forEach(cb=>cb.checked=this.checked);
    updateBulkBar('leave');
});
document.getElementById('selectAllPerms') && document.getElementById('selectAllPerms').addEventListener('change', function(){
    document.querySelectorAll('.perm-check').forEach(cb=>cb.checked=this.checked);
    updateBulkBar('perm');
});
document.querySelectorAll('.leave-check').forEach(cb=>cb.addEventListener('change',()=>updateBulkBar('leave')));
document.querySelectorAll('.perm-check').forEach(cb=>cb.addEventListener('change',()=>updateBulkBar('perm')));

function updateBulkBar(type){
    var checks = document.querySelectorAll('.'+type+'-check:checked');
    var bar = document.getElementById(type==='leave'?'leaveBulkBar':'permBulkBar');
    var countEl = document.getElementById(type==='leave'?'leaveSelectedCount':'permSelectedCount');
    if(countEl) countEl.textContent = checks.length;
    if(bar) bar.style.display = checks.length > 0 ? 'flex' : 'none';
}

// ── Quick leave status ──
window.quickLeaveStatus = function(id, status){
    showConfirm(
        status==='approved' ? '✓' : '✗',
        status==='approved' ? '#ECFDF5' : '#FEF2F2',
        status==='approved' ? '#10B981' : '#EF4444',
        (status==='approved'?'Approve':'Decline') + ' Leave?',
        'This will update the leave status to ' + status + '.',
        status==='approved' ? '#10B981' : '#EF4444',
        function(){
            ajaxStatus('{{ url("admin-leaves") }}/'+id+'/update-status', status, 'PUT');
        }
    );
};

// ── Quick permission status ──
window.quickPermStatus = function(id, status){
    showConfirm(
        status==='approved' ? '✓' : '✗',
        status==='approved' ? '#ECFDF5' : '#FEF2F2',
        status==='approved' ? '#10B981' : '#EF4444',
        (status==='approved'?'Approve':'Decline') + ' Permission?',
        'This will update the permission status to ' + status + '.',
        status==='approved' ? '#10B981' : '#EF4444',
        function(){
            ajaxStatus('{{ url("admin-permissions") }}/'+id+'/update-status', status, 'PUT');
        }
    );
};

// ── Bulk leave ──
window.bulkLeave = function(status){
    var ids = Array.from(document.querySelectorAll('.leave-check:checked')).map(c=>c.value);
    if(!ids.length){ Swal.fire('Info','Select at least one record.','info'); return; }
    ajaxBulk('{{ url("admin-leaves/bulk-update") }}', ids, status);
};
window.bulkPerm = function(status){
    var ids = Array.from(document.querySelectorAll('.perm-check:checked')).map(c=>c.value);
    if(!ids.length){ Swal.fire('Info','Select at least one record.','info'); return; }
    ajaxBulk('{{ url("admin-permissions/bulk-update") }}', ids, status);
};

function ajaxBulk(url, ids, status){
    Swal.fire({title:'Processing…',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
    $.ajax({url:url,type:'POST',data:{_token:_csrf,ids:ids,status:status},
        success:function(r){ Swal.close(); if(r.success){ Swal.fire('Done!','Status updated.','success').then(()=>location.reload()); } else { Swal.fire('Error',r.message||'Failed','error'); } },
        error:function(){ Swal.close(); Swal.fire('Error','Request failed.','error'); }
    });
}

function ajaxStatus(url, status, method){
    Swal.fire({title:'Updating…',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
    $.ajax({url:url,type:'POST',data:{_token:_csrf,_method:method,status:status},
        success:function(r){ Swal.close(); Swal.fire('Updated!',r.message||'Status changed.','success').then(()=>location.reload()); },
        error:function(xhr){ Swal.close(); Swal.fire('Error',xhr.responseText||'Request failed.','error'); }
    });
}

function showConfirm(icon, iconBg, iconColor, title, msg, btnColor, onOk){
    var modal = document.getElementById('confirmModal');
    document.getElementById('confirmIcon').style.background = iconBg;
    document.getElementById('confirmIcon').style.color = iconColor;
    document.getElementById('confirmIcon').textContent = icon;
    document.getElementById('confirmTitle').textContent = title;
    document.getElementById('confirmMsg').textContent = msg;
    var okBtn = document.getElementById('confirmOk');
    okBtn.style.background = btnColor;
    okBtn.textContent = 'Confirm';
    okBtn.onclick = function(){ modal.style.display='none'; onOk(); };
    modal.style.display = 'flex';
}
document.getElementById('confirmModal').addEventListener('click',function(e){ if(e.target===this) this.style.display='none'; });
document.addEventListener('keydown',function(e){ if(e.key==='Escape') document.getElementById('confirmModal').style.display='none'; });
</script>

@endsection
