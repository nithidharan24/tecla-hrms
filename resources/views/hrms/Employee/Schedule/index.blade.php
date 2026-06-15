@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Shift & Schedule');
@endphp
@extends('layouts.index')
@section('content')
@php use Carbon\Carbon; @endphp

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="lv-wrap">

@if(session('success'))
<div class="lv-alert lv-alert-success"><i class="fa fa-check-circle me-2"></i>{{ session('success') }}<button onclick="this.parentElement.remove()" class="lv-alert-close">&times;</button></div>
@endif
@if(session('error'))
<div class="lv-alert lv-alert-danger"><i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}<button onclick="this.parentElement.remove()" class="lv-alert-close">&times;</button></div>
@endif
@if(session('warning'))
<div class="lv-alert lv-alert-warning"><i class="fa fa-triangle-exclamation me-2"></i>{{ session('warning') }}<button onclick="this.parentElement.remove()" class="lv-alert-close">&times;</button></div>
@endif

@php
    $currentSchedules   = $schedules->filter(function($s) use ($today) {
        $start = Carbon::parse($s->schedule_start_date);
        if ($s->repeat_every_week) return $start->lte($today);
        return $start->lte($today) && Carbon::parse($s->schedule_end_date)->gte($today);
    });
    $upcomingSchedules  = $schedules->filter(fn($s) => Carbon::parse($s->schedule_start_date)->gt($today));
    $totalSchedules     = $schedules->count();
@endphp

{{-- TOP BAR --}}
<div class="lv-topbar">
    <div>
        <h1 class="lv-page-title">Employee Schedules</h1>
        <p class="lv-page-sub">{{ $today->format('d M Y') }} &middot; {{ $totalSchedules }} total schedule(s)</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if(isset($permissions) && $permissions->can_create)
        <a href="{{ route('shift.index') }}" class="lv-btn lv-btn-outline"><i class="fa fa-clock me-1"></i> Manage Shifts</a>
        <a href="{{ route('scheduling.shift-interchange.create') }}" class="lv-btn lv-btn-outline"><i class="fa fa-right-left me-1"></i> Interchange</a>
        <a href="{{ route('scheduling.create') }}" class="lv-btn lv-btn-primary"><i class="fa fa-plus me-1"></i> Add Schedule</a>
        @endif
    </div>
</div>

{{-- KPI CARDS --}}
<div class="lv-kpi-grid" style="grid-template-columns:repeat(4,minmax(0,1fr))">
    <div class="lv-kpi-card" style="--accent:#6366F1">
        <div class="lv-kpi-icon" style="background:#EEF2FF;color:#6366F1"><i class="fa fa-calendar-days"></i></div>
        <div class="lv-kpi-body"><div class="lv-kpi-val">{{ $totalSchedules }}</div><div class="lv-kpi-label">Total Schedules</div></div>
    </div>
    <div class="lv-kpi-card" style="--accent:#22C55E">
        <div class="lv-kpi-icon" style="background:#F0FDF4;color:#22C55E"><i class="fa fa-circle-play"></i></div>
        <div class="lv-kpi-body"><div class="lv-kpi-val">{{ $currentSchedules->count() }}</div><div class="lv-kpi-label">Current</div></div>
    </div>
    <div class="lv-kpi-card" style="--accent:#06B6D4">
        <div class="lv-kpi-icon" style="background:#ECFEFF;color:#06B6D4"><i class="fa fa-hourglass-half"></i></div>
        <div class="lv-kpi-body"><div class="lv-kpi-val">{{ $upcomingSchedules->count() }}</div><div class="lv-kpi-label">Upcoming</div></div>
    </div>
    <div class="lv-kpi-card" style="--accent:#F59E0B">
        <div class="lv-kpi-icon" style="background:#FFFBEB;color:#F59E0B"><i class="fa fa-triangle-exclamation"></i></div>
        <div class="lv-kpi-body"><div class="lv-kpi-val">{{ $endingSoonSchedules->count() }}</div><div class="lv-kpi-label">Ending Soon</div></div>
    </div>
</div>

{{-- TABS --}}
<div class="lv-tabs">
    <button class="lv-tab active" data-tab="weekly"><i class="fa fa-calendar-week me-1"></i> Weekly View</button>
    <button class="lv-tab" data-tab="all"><i class="fa fa-list me-1"></i> All Schedules</button>
    <button class="lv-tab" data-tab="upcoming"><i class="fa fa-clock me-1"></i> Upcoming <span class="lv-badge-pill">{{ $upcomingSchedules->count() }}</span></button>
    <button class="lv-tab" data-tab="ending"><i class="fa fa-triangle-exclamation me-1"></i> Ending Soon <span class="lv-badge-pill lv-pill-warn">{{ $endingSoonSchedules->count() }}</span></button>
  
    <button class="lv-tab" data-tab="update"><i class="fa fa-rotate me-1"></i> Need Update <span class="lv-badge-pill">{{ $schedulesNeedingUpdate->count() }}</span></button>
</div>

<div class="lv-tab-content">

{{-- WEEKLY VIEW --}}
<div class="lv-pane active" id="tab-weekly">
    <div class="lv-panel p-0">
        @php
            $daysOfWeek = [];
            for ($i = 0; $i < 7; $i++) {
                $day = $today->copy()->addDays($i);
                $daysOfWeek[] = ['display'=>$day->format('D d'), 'date'=>$day->format('Y-m-d'), 'day_name'=>$day->format('D'), 'full_date'=>$day->format('l, M d'), 'carbon'=>$day];
            }
        @endphp
        <div class="lv-table-wrap" style="border-radius:14px">
            <table class="lv-table" style="min-width:900px">
                <thead>
                    <tr>
                        <th style="min-width:180px">Employee</th>
                        @foreach($daysOfWeek as $day)
                        <th class="text-center {{ $day['carbon']->isToday() ? 'lv-today-col' : '' }}" style="min-width:120px">
                            <div>{{ $day['display'] }}</div>
                            <div style="font-size:10px;font-weight:500;color:#9CA3AF">{{ $day['full_date'] }}</div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                @forelse($currentSchedules as $schedule)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="lv-avatar">{{ substr($schedule->firstname,0,1) }}{{ substr($schedule->lastname,0,1) }}</div>
                            <div>
                                <div class="fw-600">{{ $schedule->firstname }} {{ $schedule->lastname }}</div>
                                <div style="font-size:11px;color:#8892B0">{{ $schedule->department ?? '' }}</div>
                                <div class="d-flex gap-1 mt-1">
                                    @if($schedule->repeat_every_week)<span class="lv-mini-badge lv-mini-green">Weekly</span>@endif
                                    @if($schedule->ending_soon ?? false)<span class="lv-mini-badge lv-mini-warn">Ending Soon</span>@endif
                                    @if(!$schedule->publish)<span class="lv-mini-badge lv-mini-grey">Draft</span>@endif
                                </div>
                            </div>
                        </div>
                    </td>
                    @foreach($daysOfWeek as $day)
                    @php
                        $dayName = $day['day_name'];
                        $shiftDays = array_map('trim', explode(',', $schedule->days_of_week ?? ''));
                        $hasShift = collect($shiftDays)->contains(fn($d) => strtolower($d) === strtolower($dayName));
                        $schedStart = Carbon::parse($schedule->schedule_start_date);
                        $schedEnd   = Carbon::parse($schedule->schedule_end_date);
                        $curDate    = $day['carbon'];
                        $inPeriod   = $schedule->repeat_every_week ? $curDate->gte($schedStart) : $curDate->between($schedStart, $schedEnd);
                    @endphp
                    <td class="text-center {{ $day['carbon']->isToday() ? 'lv-today-col' : '' }}" style="padding:8px">
                        @if($hasShift && $inPeriod)
                        <div class="lv-shift-cell">
                            <div style="font-size:11px;font-weight:700;color:#1D4ED8">
                                {{ Carbon::parse($schedule->start_time)->format('g:i A') }}&ndash;{{ Carbon::parse($schedule->end_time)->format('g:i A') }}
                            </div>
                            <div style="font-size:10px;color:#6B7280;margin-top:2px">{{ $schedule->shift_name }}</div>
                        </div>
                        @else
                        <div class="lv-shift-empty"><i class="fa fa-minus" style="opacity:.3"></i></div>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @empty
                <tr>
                    <td colspan="{{ count($daysOfWeek)+1 }}" class="text-center py-5 text-muted">
                        <i class="fa fa-calendar-xmark fa-3x mb-3 d-block" style="opacity:.3"></i>
                        No current schedules.
                        @if(isset($permissions) && $permissions->can_create)
                        <a href="{{ route('scheduling.create') }}" class="lv-btn lv-btn-primary lv-btn-sm d-inline-flex mt-2"><i class="fa fa-plus me-1"></i> Add Schedule</a>
                        @endif
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ALL SCHEDULES --}}
<div class="lv-pane" id="tab-all">
    <div class="lv-panel">
        <!-- Single Line Filter Bar -->
        <div class="single-line-filter-bar mb-3">
            <div class="d-flex align-items-center w-100 m-0 flex-wrap" style="gap: 8px;">
                
                <!-- Primary Search (Orange Pill) -->
                <div class="sl-search-wrapper">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" id="allSearch" placeholder="Search employee..." class="sl-search-input">
                </div>
                
                <div class="sl-divider"></div>

                <!-- Department Dropdown -->
                <div class="sl-select-wrapper ms-2 dropdown">
                    <i class="fa-solid fa-building icon-left"></i>
                    <input type="hidden" id="allDept" value="">
                    <button class="sl-dropdown-btn" id="allDeptBtn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        All Departments
                    </button>
                    <i class="fa-solid fa-chevron-down icon-right" data-bs-toggle="dropdown" style="cursor: pointer;"></i>
                    
                    <ul class="dropdown-menu sl-dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('allDept').value=''; document.getElementById('allDeptBtn').innerText='All Departments'; filterAll();">All Departments</a></li>
                        @foreach($schedules->pluck('department')->filter()->unique()->sort() as $dept)
                            <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('allDept').value='{{ $dept }}'; document.getElementById('allDeptBtn').innerText='{{ $dept }}'; filterAll();">{{ $dept }}</a></li>
                        @endforeach
                    </ul>
                </div>
                
                <!-- Shift Dropdown -->
                <div class="sl-select-wrapper ms-2 dropdown">
                    <i class="fa-solid fa-clock icon-left"></i>
                    <input type="hidden" id="allShift" value="">
                    <button class="sl-dropdown-btn" id="allShiftBtn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        All Shifts
                    </button>
                    <i class="fa-solid fa-chevron-down icon-right" data-bs-toggle="dropdown" style="cursor: pointer;"></i>
                    
                    <ul class="dropdown-menu sl-dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('allShift').value=''; document.getElementById('allShiftBtn').innerText='All Shifts'; filterAll();">All Shifts</a></li>
                        @foreach($schedules->pluck('shift_name')->filter()->unique()->sort() as $shift)
                            <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('allShift').value='{{ $shift }}'; document.getElementById('allShiftBtn').innerText='{{ $shift }}'; filterAll();">{{ $shift }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <!-- Status Dropdown -->
                <div class="sl-select-wrapper ms-2 dropdown">
                    <i class="fa-solid fa-info-circle icon-left"></i>
                    <input type="hidden" id="allStatus" value="">
                    <button class="sl-dropdown-btn" id="allStatusBtn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        All Status
                    </button>
                    <i class="fa-solid fa-chevron-down icon-right" data-bs-toggle="dropdown" style="cursor: pointer;"></i>
                    
                    <ul class="dropdown-menu sl-dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('allStatus').value=''; document.getElementById('allStatusBtn').innerText='All Status'; filterAll();">All Status</a></li>
                        <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('allStatus').value='current'; document.getElementById('allStatusBtn').innerText='Current'; filterAll();">Current</a></li>
                        <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('allStatus').value='upcoming'; document.getElementById('allStatusBtn').innerText='Upcoming'; filterAll();">Upcoming</a></li>
                    </ul>
                </div>

                <div class="sl-divider ms-auto"></div>

                <button type="button" class="sl-action-btn" onclick="document.getElementById('allSearch').value=''; document.getElementById('allDept').value=''; document.getElementById('allDeptBtn').innerText='All Departments'; document.getElementById('allShift').value=''; document.getElementById('allShiftBtn').innerText='All Shifts'; document.getElementById('allStatus').value=''; document.getElementById('allStatusBtn').innerText='All Status'; filterAll();">
                    <i class="fa-solid fa-rotate-right"></i> Reset
                </button>
            </div>
        </div>
        <div class="lv-table-wrap">
            <table class="lv-table" id="allTable">
                <thead>
                    <tr>
                        <th>#</th><th>Employee</th><th>Department</th><th>Shift</th><th>Period</th><th>Repeat</th><th>Published</th>
                        @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))<th>Actions</th>@endif
                    </tr>
                </thead>
                <tbody>
                @forelse($schedules as $s)
                @php
                    $sStart = Carbon::parse($s->schedule_start_date);
                    $sType  = $sStart->gt($today) ? 'upcoming' : 'current';
                @endphp
                <tr data-search="{{ strtolower($s->firstname.' '.$s->lastname) }}" data-status="{{ $sType }}" data-dept="{{ $s->department }}" data-shift="{{ $s->shift_name }}" {!! $loop->iteration > 10 ? 'style="display:none;"' : '' !!}>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="lv-avatar lv-avatar-sm">{{ substr($s->firstname,0,1) }}{{ substr($s->lastname,0,1) }}</div>
                            <div>
                                <div class="fw-600">{{ $s->firstname }} {{ $s->lastname }}</div>
                                <div style="font-size:11px;color:#8892B0">{{ $s->employeeid ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="lv-type-badge">{{ $s->department ?? '-' }}</span></td>
                    <td>
                        <strong>{{ $s->shift_name }}</strong><br>
                        <span style="font-size:11px;color:#8892B0">{{ Carbon::parse($s->start_time)->format('h:i A') }} &ndash; {{ Carbon::parse($s->end_time)->format('h:i A') }}</span>
                    </td>
                    <td style="font-size:12px">
                        {{ Carbon::parse($s->schedule_start_date)->format('d M Y') }}<br>
                        @if(!$s->repeat_every_week)<span style="color:#8892B0">to {{ Carbon::parse($s->schedule_end_date)->format('d M Y') }}</span>@endif
                    </td>
                    <td>
                        @if($s->repeat_every_week)
                        <span class="lv-mini-badge lv-mini-green">Every Week</span>
                        @else
                        <span class="lv-mini-badge lv-mini-grey">Fixed</span>
                        @endif
                    </td>
                    <td>
                        @if($s->publish)
                        <span class="lv-status-badge lv-badge-approved">Published</span>
                        @else
                        <span class="lv-status-badge lv-badge-pending">Draft</span>
                        @endif
                    </td>
                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                    <td>
                        <div class="d-flex gap-1">
                            @if(isset($permissions) && $permissions->can_edit)
                            <a href="{{ route('scheduling.edit', $s->id) }}" class="lv-act-btn lv-act-edit"><i class="fa fa-pencil"></i></a>
                            @endif
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-5 text-muted">No schedules found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination Container -->
        <div id="lv-pagination"></div>
    </div>
</div>

{{-- UPCOMING --}}
<div class="lv-pane" id="tab-upcoming">
    <div class="lv-panel">
        @if($upcomingSchedules->count())
        <div class="lv-table-wrap">
            <table class="lv-table">
                <thead><tr><th>#</th><th>Employee</th><th>Shift</th><th>Starts On</th><th>Starts In</th><th>Published</th>
                @if(isset($permissions) && $permissions->can_edit)<th>Actions</th>@endif</tr></thead>
                <tbody>
                @foreach($upcomingSchedules->sortBy('schedule_start_date') as $i => $s)
                @php $daysUntil = $today->diffInDays(Carbon::parse($s->schedule_start_date), false); @endphp
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="lv-avatar lv-avatar-sm">{{ substr($s->firstname,0,1) }}{{ substr($s->lastname,0,1) }}</div>
                            <div><div class="fw-600">{{ $s->firstname }} {{ $s->lastname }}</div><div style="font-size:11px;color:#8892B0">{{ $s->department ?? '' }}</div></div>
                        </div>
                    </td>
                    <td><strong>{{ $s->shift_name }}</strong><br><span style="font-size:11px;color:#8892B0">{{ Carbon::parse($s->start_time)->format('h:i A') }} &ndash; {{ Carbon::parse($s->end_time)->format('h:i A') }}</span></td>
                    <td>{{ Carbon::parse($s->schedule_start_date)->format('d M Y') }}</td>
                    <td><span class="lv-mini-badge lv-mini-blue">{{ $daysUntil }} day(s)</span></td>
                    <td>@if($s->publish)<span class="lv-status-badge lv-badge-approved">Published</span>@else<span class="lv-status-badge lv-badge-pending">Draft</span>@endif</td>
                    @if(isset($permissions) && $permissions->can_edit)
                    <td><a href="{{ route('scheduling.edit', $s->id) }}" class="lv-act-btn lv-act-edit"><i class="fa fa-pencil"></i></a></td>
                    @endif
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="lv-empty-state"><i class="fa fa-clock fa-3x mb-3"></i><h5>No Upcoming Schedules</h5></div>
        @endif
    </div>
</div>

{{-- ENDING SOON --}}
<div class="lv-pane" id="tab-ending">
    <div class="lv-panel">
        @if($endingSoonSchedules->count())
        <div class="lv-table-wrap">
            <table class="lv-table">
                <thead><tr><th>#</th><th>Employee</th><th>Shift</th><th>Ends On</th><th>Days Left</th>
                @if(isset($permissions) && $permissions->can_edit)<th>Actions</th>@endif</tr></thead>
                <tbody>
                @foreach($endingSoonSchedules as $i => $s)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="lv-avatar lv-avatar-sm">{{ substr($s->firstname,0,1) }}{{ substr($s->lastname,0,1) }}</div>
                            <div><div class="fw-600">{{ $s->firstname }} {{ $s->lastname }}</div><div style="font-size:11px;color:#8892B0">{{ $s->department ?? '' }}</div></div>
                        </div>
                    </td>
                    <td><strong>{{ $s->shift_name }}</strong></td>
                    <td>{{ Carbon::parse($s->schedule_end_date)->format('d M Y') }}</td>
                    <td><span class="lv-mini-badge {{ $s->days_remaining <= 3 ? 'lv-mini-red' : 'lv-mini-warn' }}">{{ $s->days_remaining }} day(s)</span></td>
                    @if(isset($permissions) && $permissions->can_edit)
                    <td><a href="{{ route('scheduling.edit', $s->id) }}" class="lv-act-btn lv-act-edit"><i class="fa fa-pencil"></i></a></td>
                    @endif
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="lv-empty-state"><i class="fa fa-triangle-exclamation fa-3x mb-3"></i><h5>No Schedules Ending Soon</h5></div>
        @endif
    </div>
</div>

{{-- NEED UPDATE --}}
<div class="lv-pane" id="tab-update">
    <div class="lv-panel">
        @if($schedulesNeedingUpdate->count())
        <div class="lv-table-wrap">
            <table class="lv-table">
                <thead><tr><th>#</th><th>Employee</th><th>Shift</th><th>Period</th><th>Next Cycle In</th><th>Action</th></tr></thead>
                <tbody>
                @foreach($schedulesNeedingUpdate as $i => $s)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="lv-avatar lv-avatar-sm">{{ substr($s->firstname,0,1) }}{{ substr($s->lastname,0,1) }}</div>
                            <div><div class="fw-600">{{ $s->firstname }} {{ $s->lastname }}</div><div style="font-size:11px;color:#8892B0">{{ $s->department ?? '' }}</div></div>
                        </div>
                    </td>
                    <td><strong>{{ $s->shift_name }}</strong><br><span style="font-size:11px;color:#8892B0">{{ Carbon::parse($s->start_time)->format('h:i A') }} &ndash; {{ Carbon::parse($s->end_time)->format('h:i A') }}</span></td>
                    <td style="font-size:12px">{{ $s->formatted_start_date }} &ndash; {{ $s->formatted_end_date }}</td>
                    <td><span class="lv-mini-badge lv-mini-blue">{{ $today->diffInDays(Carbon::parse($s->schedule_end_date))+1 }} day(s)</span></td>
                    <td><a href="{{ route('scheduling.edit', $s->id) }}" class="lv-btn lv-btn-primary lv-btn-sm"><i class="fa fa-rotate me-1"></i>Update</a></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="lv-empty-state"><i class="fa fa-rotate fa-3x mb-3"></i><h5>No Schedules Need Update</h5></div>
        @endif
    </div>
</div>

</div>{{-- end lv-tab-content --}}
</div>{{-- end lv-wrap --}}

<style>
:root{--lv-primary:#F97316;--lv-primary-dark:#EA580C}
.lv-wrap{background:#F5F6FA;min-height:100vh;padding:24px 32px;font-family:'DM Sans',sans-serif}
@media(max-width:767px){.lv-wrap{padding:12px 10px}}
.lv-alert{display:flex;align-items:center;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:14px;font-weight:500;position:relative}
.lv-alert-success{background:#ECFDF5;color:#047857;border:1px solid #6EE7B7}
.lv-alert-danger{background:#FEF2F2;color:#B91C1C;border:1px solid #FCA5A5}
.lv-alert-warning{background:#FFFBEB;color:#B45309;border:1px solid #FDE68A}
.lv-alert-close{position:absolute;right:12px;background:none;border:none;font-size:18px;cursor:pointer;color:inherit;font-weight:700}
.lv-topbar{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:24px}
.lv-page-title{font-size:22px;font-weight:700;color:#0B1437;margin:0}
.lv-page-sub{font-size:13px;color:#8892B0;margin:2px 0 0}
.lv-btn{display:inline-flex;align-items:center;padding:9px 18px;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:.15s}
.lv-btn-primary{background:var(--lv-primary);color:#fff}.lv-btn-primary:hover{background:var(--lv-primary-dark);color:#fff}
.lv-btn-outline{background:#fff;color:#374151;border:1px solid #E5E7EB}.lv-btn-outline:hover{background:#F9FAFB}
.lv-btn-sm{padding:6px 14px;font-size:12px}
.lv-kpi-grid{display:grid;gap:16px;margin-bottom:24px}
@media(max-width:1000px){.lv-kpi-grid{grid-template-columns:repeat(2,1fr)!important}}
@media(max-width:600px){.lv-kpi-grid{grid-template-columns:repeat(2,1fr)!important}}
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
.lv-pill-warn{background:#F59E0B}
.lv-pane{display:none}.lv-pane.active{display:block}
.lv-panel{background:#fff;border:1px solid #E5E7EB;border-radius:14px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,0.02)}

/* Single Line Filter Bar CSS */
.single-line-filter-bar { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 50px; padding: 6px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); display: flex; align-items: center; gap: 8px; width: 100%; flex-wrap: wrap; }
.single-line-filter-bar::-webkit-scrollbar { height: 4px; }
.single-line-filter-bar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; }
.sl-search-wrapper { display: flex; align-items: center; border: 1px solid #fb923c; border-radius: 50px; padding: 6px 16px; background: #fffaf5; min-width: 200px; }
.sl-search-wrapper i { color: #94a3b8; margin-right: 8px; font-size: 14px; }
.sl-search-input { border: none; background: transparent; outline: none; font-size: 13px; color: #334155; width: 100%; }
.sl-search-input::placeholder { color: #94a3b8; }
.sl-divider { width: 1px; height: 24px; background-color: #e2e8f0; margin: 0 4px; }
.sl-select-wrapper { position: relative; display: flex; align-items: center; border: 1px solid #e2e8f0; border-radius: 50px; padding: 6px 14px; background: #ffffff; }
.sl-select-wrapper i.icon-left { color: #94a3b8; margin-right: 6px; font-size: 13px; }
.sl-select-wrapper i.icon-right { color: #94a3b8; margin-left: 6px; font-size: 10px; }
.sl-dropdown-btn { display: flex; align-items: center; border: none; background: transparent; outline: none; font-size: 13px; color: #475569; padding: 0; margin-right: 4px; }
.sl-dropdown-btn::after { display: none; }
.sl-dropdown-menu { border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); padding: 8px 0; min-width: 200px; margin-top: 8px !important; z-index: 1060; }
.sl-dropdown-menu .dropdown-item { font-size: 13px; padding: 8px 16px; color: #334155; transition: all 0.2s ease; }
.sl-dropdown-menu .dropdown-item:hover, .sl-dropdown-menu .dropdown-item:focus { background-color: #f1f5f9; color: #0f172a; }
.sl-dropdown-menu .dropdown-item.active { background-color: #e0f2fe; color: #0284c7; font-weight: 500; }
.sl-action-btn { display: flex; align-items: center; gap: 6px; background: #f1f5f9; border: none; border-radius: 50px; padding: 8px 16px; font-size: 13px; font-weight: 500; color: #475569; cursor: pointer; transition: background 0.2s; margin-left: auto; }
.sl-action-btn:hover { background: #e2e8f0; }
@media (max-width: 768px) {
    .single-line-filter-bar { border-radius: 12px; padding: 12px; }
    .single-line-filter-bar > div { flex-direction: column; align-items: stretch !important; gap: 12px; }
    .sl-search-wrapper, .sl-select-wrapper { width: 100%; min-width: unset; margin-left: 0 !important; }
    .sl-divider { display: none; }
    .sl-action-btn { width: 100%; justify-content: center; margin-top: 8px; }
}

.lv-table-wrap{overflow-x:auto;border-radius:12px;border:1px solid #E5E7EB}
.lv-table{width:100%;border-collapse:collapse;font-size:13px;background:#fff}
.lv-table th{background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#6B7280;font-size:11px;font-weight:700;letter-spacing:.05em;padding:11px 14px;text-align:left;text-transform:uppercase;white-space:nowrap}
.lv-table td{border-bottom:1px solid #F3F4F6;color:#0B1437;padding:10px 14px;vertical-align:middle}
.lv-table tbody tr:last-child td{border-bottom:none}
.lv-table tbody tr:hover td{background:#FAFAFA}
.lv-today-col{background:#FFF7ED!important}
.lv-avatar{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#F97316,#EA580C);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0}
.lv-avatar-sm{width:28px;height:28px;font-size:11px}
.lv-shift-cell{background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;padding:6px 8px;min-height:56px;display:flex;flex-direction:column;justify-content:center}
.lv-shift-empty{min-height:56px;display:flex;align-items:center;justify-content:center;color:#D1D5DB}
.lv-mini-badge{border-radius:20px;font-size:10px;font-weight:700;padding:2px 7px;display:inline-flex;white-space:nowrap}
.lv-mini-green{background:#ECFDF5;color:#047857}
.lv-mini-warn{background:#FFFBEB;color:#B45309}
.lv-mini-red{background:#FEF2F2;color:#B91C1C}
.lv-mini-grey{background:#F3F4F6;color:#6B7280}
.lv-mini-blue{background:#EFF6FF;color:#1D4ED8}
.lv-type-badge{background:#EFF6FF;color:#1D4ED8;border-radius:6px;padding:3px 9px;font-size:11px;font-weight:700;white-space:nowrap}
.lv-status-badge{border-radius:20px;font-size:11px;font-weight:700;padding:4px 10px;display:inline-flex;white-space:nowrap}
.lv-badge-approved{background:#ECFDF5;color:#047857}
.lv-badge-pending{background:#FFFBEB;color:#B45309}
.lv-act-btn{width:28px;height:28px;border-radius:6px;border:1px solid #E5E7EB;background:#fff;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;font-size:12px;transition:.15s;text-decoration:none;color:#374151}
.lv-act-edit:hover{background:#EFF6FF;border-color:#BFDBFE;color:#1D4ED8}
.lv-empty-state{text-align:center;padding:48px 24px;color:#8892B0}
.lv-empty-state h5{color:#374151;font-weight:700;margin-bottom:8px}
.fw-600{font-weight:600}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.lv-tab').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.lv-tab').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.lv-pane').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('tab-' + this.dataset.tab).classList.add('active');
        });
    });

    var allSearch = document.getElementById('allSearch');
    var allStatus = document.getElementById('allStatus');
    var allDept = document.getElementById('allDept');
    var allShift = document.getElementById('allShift');
    var currentPage = 1;
    var rowsPerPage = 10;
    var filteredRows = [];

    function renderPagination() {
        var totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        var start = (currentPage - 1) * rowsPerPage;
        var end = start + rowsPerPage;
        
        filteredRows.forEach(function(r, index) {
            if (index >= start && index < end) {
                r.style.display = '';
            } else {
                r.style.display = 'none';
            }
        });
        
        var paginationDiv = document.getElementById('lv-pagination');
        if (!paginationDiv) return;
        
        var html = '<div class="d-flex align-items-center gap-2 mt-3 justify-content-end">';
        if (totalPages > 1) {
            html += '<button class="lv-btn lv-btn-outline lv-btn-sm" onclick="changePage(' + (currentPage - 1) + ')" ' + (currentPage === 1 ? 'disabled' : '') + '><i class="fa fa-chevron-left"></i></button>';
            html += '<span class="fw-600" style="font-size:13px;color:#4B5563;margin:0 8px">Page ' + currentPage + ' of ' + totalPages + '</span>';
            html += '<button class="lv-btn lv-btn-outline lv-btn-sm" onclick="changePage(' + (currentPage + 1) + ')" ' + (currentPage === totalPages ? 'disabled' : '') + '><i class="fa fa-chevron-right"></i></button>';
        }
        html += '</div>';
        paginationDiv.innerHTML = html;
    }

    window.changePage = function(page) {
        var totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        if (page >= 1 && page <= totalPages) {
            currentPage = page;
            renderPagination();
        }
    };

    function filterAll() {
        var sv = allSearch ? allSearch.value.toLowerCase() : '';
        var stv = allStatus ? allStatus.value : '';
        var deptv = allDept ? allDept.value : '';
        var shiftv = allShift ? allShift.value : '';
        filteredRows = [];

        document.querySelectorAll('#allTable tbody tr').forEach(function(r) {
            // Ignore the "No schedules found" row
            if (r.children.length === 1 && r.children[0].colSpan > 1) return;
            
            var ms = !sv || (r.dataset.search||'').includes(sv);
            var mst = !stv || r.dataset.status === stv;
            var mdept = !deptv || r.dataset.dept === deptv;
            var mshift = !shiftv || r.dataset.shift === shiftv;

            if (ms && mst && mdept && mshift) {
                filteredRows.push(r);
            } else {
                r.style.display = 'none';
            }
        });
        
        currentPage = 1;
        renderPagination();
    }
    
    if (allSearch) allSearch.addEventListener('input', filterAll);
    // Note: The dropdowns now use onclick events inside the HTML to call filterAll() directly.
    
    // Initial pagination render
    filterAll();
});
</script>
@endsection
