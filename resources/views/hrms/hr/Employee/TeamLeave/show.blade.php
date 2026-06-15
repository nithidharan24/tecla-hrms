@extends('layouts.index')

@section('content')
@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Team Leaves');
    $year = request('year', date('Y'));
@endphp

<div class="content container-fluid mt-3">

    <!-- ==============================================
         MAIN TABS
    ================================================= -->
    <ul class="nav leave-tabs mb-0" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#leave-summary">Leave Summary</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#leave-balance">Leave Balance</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#leave-requests">Leave Requests</a>
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
                <!-- Header with Employee Info -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="mb-1">{{ $member->firstname }} {{ $member->lastname }} - Leave Summary</h5>
                        <p class="text-muted mb-0">
                            <i class="fa-solid fa-id-card me-1"></i> {{ $member->employeeid }} |
                            <i class="fa-solid fa-briefcase me-1"></i> {{ $member->designation }} |
                            <i class="fa-solid fa-building me-1"></i> {{ $member->department }}
                        </p>
                    </div>
                    
                    <div>
                        <a href="{{ route('team-leaves.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa-solid fa-arrow-left"></i> Back to Team
                        </a>
                    </div>
                </div>

                <!-- Year Navigation -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <span>Leave booked this year : </span>
                        <strong>{{ $member->totalLeavesTaken }} day(s)</strong>
                        <span class="ms-3"> | </span>
                        <span class="ms-2">Paid: <strong>{{ $member->totalPaid }}</strong></span>
                        <span class="ms-2"> | Unpaid (LOP): <strong>{{ $member->totalLOP }}</strong></span>
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
                    
                        @if(isset($permissions) && $permissions->can_create)
                        <!-- <button class="btn btn-primary" onclick="applyLeaveForMember({{ $member->id }})">
                            Apply Leave
                        </button> -->
                        @endif
                    
                        <input type="hidden" id="summaryYear" value="{{ $year }}">
                    </div>
                </div>
                
                <!-- Leave Summary Cards -->
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
                
                <!-- Upcoming Leaves -->
                <div class="zs-section mt-4">
                    <button class="zs-dropdown-btn">
                        Upcoming Leaves
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
            
                    <div class="zs-list">
                        @php $upcomingCount = 0; @endphp
            
                        {{-- UPCOMING LEAVES --}}
                        @foreach($upcomingLeaves as $leave)
                            @php $upcomingCount++; @endphp
                            <div class="zs-list-item">
                                <strong>{{ \Carbon\Carbon::parse($leave->from_date)->format('d-M-Y, l') }}</strong>
                                <span class="zs-tag">{{ $leave->leave_type }}</span>
                              
                            </div>
                        @endforeach
            
                        @if($upcomingCount == 0)
                        <div class="zs-empty">
                            <img src="/images/no-data.svg" width="120">
                            <p>No Upcoming Leaves</p>
                        </div>
                        @endif
                    </div>
                </div>
            
                <!-- Past Leaves -->
                <div class="zs-section mt-4">
                    <button class="zs-dropdown-btn">
                        Past Leaves
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="zs-list">
                        {{-- PAST LEAVES --}}
                        @foreach($pastLeaves as $leave)
                            <div class="zs-list-item">
                                <strong>{{ \Carbon\Carbon::parse($leave->from_date)->format('d-M-Y, l') }}</strong>
                                <span class="zs-tag">{{ $leave->leave_type }}</span>
                                <span class="text-muted">
                                    • 
                                    @if($leave->paid_days > 0)
                                        {{ $leave->paid_days }} day{{ $leave->paid_days > 1 ? 's' : '' }} (Paid)
                                    @endif
                                    @if($leave->lop_days > 0)
                                        {{ $leave->lop_days }} day{{ $leave->lop_days > 1 ? 's' : '' }} (Unpaid)
                                    @endif
                                </span>
                            </div>
                        @endforeach
                        
                        @if($pastLeaves->count() == 0)
                        <div class="zs-empty">
                            <img src="/images/no-data.svg" width="120">
                            <p>No Past Leaves</p>
                        </div>
                        @endif
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
                        @if($type == "Casual Leave") <i class="fa-solid fa-sun"></i>
                        @elseif($type == "Sick Leave") <i class="fa-solid fa-stethoscope"></i>
                        @elseif($type == "Hospitalisation") <i class="fa-solid fa-hospital"></i>
                        @elseif($type == "Maternity Leave") <i class="fa-solid fa-baby-carriage"></i>
                        @elseif($type == "Paternity Leave") <i class="fa-solid fa-baby"></i>
                        @else <i class="fa-solid fa-calendar"></i>
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
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="mb-0">Leave Requests</h5>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table custom-table datatable">   
                            <thead>   
                                <tr>
                                 
                                    <th>Leave Type</th>
                                    <th>Type</th>
                                    <th>Leave Period</th>
                                    <th>Days/Hrs Taken</th>
                                    <th>Date of Request</th>
                                    <th>Status</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaves as $leave)
                                    <tr>
                                       
                                        <td>{{ $leave->leave_type }}</td>
                                        <td>{{ ucfirst($leave->type ?? 'Paid') }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($leave->from_date)->format('d-M-Y') }}
                                            -
                                            {{ \Carbon\Carbon::parse($leave->to_date)->format('d-M-Y') }}
                                        </td>
                                        <td>{{ $leave->no_of_days }} Day(s)</td>
                                        <td>{{ \Carbon\Carbon::parse($leave->created_at)->format('d-M-Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $leave->status == 'approved' ? 'success' : ($leave->status == 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($leave->status) }}
                                            </span>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <div class="zs-empty">
                                                <img src="/images/no-data.svg" width="120">
                                                <p>No Leave Requests Found</p>
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
</div>

<!-- ============================================================
     APPLY LEAVE MODAL (Updated for new structure)
=============================================================== -->
<div class="modal fade" id="applyLeaveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 10px;">
            <div class="modal-header">
                <h5 class="modal-title">Apply Leave for {{ $member->firstname }} {{ $member->lastname }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="teamMemberLeaveForm" action="{{ route('team-leaves.apply-leave', $member->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="leave_type" class="form-label">Leave Type *</label>
                            <select class="form-select" id="leave_type" name="leave_type" required>
                                <option value="">Select Leave Type</option>
                                @foreach($leaveBalance as $type => $data)
                                    <option value="{{ $type }}" data-remaining="{{ $data['remaining'] }}">
                                        {{ $type }} (Remaining: {{ $data['remaining'] }} days)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Leave Type (Paid/Unpaid) *</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="Paid">Paid Leave</option>
                                <option value="Unpaid">Unpaid (LOP)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="remaining_days" class="form-label">Remaining Days</label>
                            <input type="text" class="form-control" id="remaining_days" readonly style="background-color: #f8f9fa;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="from_date" class="form-label">From Date *</label>
                            <input type="date" class="form-control" id="from_date" name="from_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="to_date" class="form-label">To Date *</label>
                            <input type="date" class="form-control" id="to_date" name="to_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="num_days" class="form-label">Number of Days *</label>
                            <input type="number" class="form-control" id="num_days" name="num_days" min="1" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="leave_reason" class="form-label">Reason *</label>
                            <textarea class="form-control" id="leave_reason" name="leave_reason" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Apply Leave</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Same CSS as before -->
<style>
<style>
/* Main Tabs */
.leave-tabs .nav-link {
    font-size: 15px;
    font-weight: 500;
    color: #333;
    margin-top: -25px;
    border-bottom: 3px solid transparent;
    padding: 10px 15px;
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

/* Year Navigation */
.zs-date-box {
    padding: 8px 14px;
    background: #fff;
    border-radius: 6px;
    border: 1px solid #dce1e8;
    font-size: 14px;
    min-width: 220px;
    text-align: center;
}

/* Cards */
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
    box-shadow: 0 4px 12px rgba(255, 127, 42, 0.1);
}

/* Icon Box */
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

/* Title */
.zs-title {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 8px;
    color: #333;
}

/* Stats */
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

.zs-line span {
    color: #666;
}

/* Sections */
.zs-section {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e7ebf1;
    padding: 15px;
    margin-top: 20px;
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
    color: #333;
}

.zs-dropdown-btn:hover {
    background: #e9edf5;
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

.zs-list-item:last-child {
    border-bottom: none;
}

.zs-tag {
    background: #dfe7ff;
    padding: 3px 7px;
    border-radius: 6px;
    font-size: 12px;
    color: #004aad;
}

.zs-empty {
    text-align: center;
    padding: 30px;
    color: #777;
}

.zs-empty img {
    opacity: 0.6;
}

/* Leave Balance Cards */
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
    border-color: #ff7f2a;
}

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
    color: #333;
}

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

/* Leave Summary Row */
.leave-summary-row {
    justify-content: flex-start !important;
    margin-left: 150px;
}

/* Table */
.custom-table thead {
    background: #f5f7fb;
}

.custom-table th {
    font-size: 13px;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #e5eaf2;
}

.custom-table td {
    font-size: 13px;
    vertical-align: middle;
    color: #555;
}

/* Filter Drawer */
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

/* Badges */
.badge.bg-success {
    background-color: #0a8e40 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #333;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
}

/* Buttons */
.btn-primary {
    background-color: #ff7f2a;
    border-color: #ff7f2a;
}

.btn-primary:hover {
    background-color: #e66a1a;
    border-color: #e66a1a;
}

.btn-outline-primary {
    color: #ff7f2a;
    border-color: #ff7f2a;
}

.btn-outline-primary:hover {
    background-color: #ff7f2a;
    border-color: #ff7f2a;
}

.btn-outline-secondary {
    border-color: #dce1e8;
    color: #666;
}

.btn-outline-secondary:hover {
    background-color: #f8f9fa;
    border-color: #dce1e8;
}

/* Modal */
.modal-content {
    border: none;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.modal-header {
    border-bottom: 1px solid #e5eaf2;
    padding: 20px;
}

.modal-title {
    font-weight: 600;
    color: #333;
}

.modal-body {
    padding: 20px;
}

.form-label {
    font-weight: 500;
    color: #555;
    margin-bottom: 5px;
}

.form-control, .form-select {
    border: 1px solid #dce1e8;
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 14px;
}

.form-control:focus, .form-select:focus {
    border-color: #ff7f2a;
    box-shadow: 0 0 0 0.2rem rgba(255, 127, 42, 0.25);
}
.nav-link{
    color: rgb(10, 10, 10);
}
</style>

</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Tab functionality
    const tabLinks = document.querySelectorAll('.leave-tabs .nav-link');
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            tabLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            const target = this.getAttribute('href');
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            document.querySelector(target).classList.add('show', 'active');
        });
    });

    // Year navigation
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
                reloadPage();
            });
        }

        // Next Year
        const nextYearBtn = document.getElementById("nextYearBtn");
        if (nextYearBtn) {
            nextYearBtn.addEventListener("click", function () {
                yearInput.value = parseInt(yearInput.value) + 1;
                updateDisplay();
                reloadPage();
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
                    reloadPage();
                }
            });
        }

        function reloadPage() {
            const url = new URL(window.location.href);
            url.searchParams.set('year', yearInput.value);
            window.location.href = url.toString();
        }
    }

    // Apply Leave Modal - Update for new structure
    const leaveTypeSelect = document.getElementById('leave_type');
    const remainingDaysInput = document.getElementById('remaining_days');
    const typeSelect = document.getElementById('type');
    const fromDateInput = document.getElementById('from_date');
    const toDateInput = document.getElementById('to_date');
    const numDaysInput = document.getElementById('num_days');

    // Leave type change handler
    if (leaveTypeSelect && remainingDaysInput) {
        leaveTypeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const remainingDays = selectedOption.getAttribute('data-remaining');
            remainingDaysInput.value = remainingDays || '0';
        });
    }

    // Date change handler to calculate days
    if (fromDateInput && toDateInput && numDaysInput) {
        function calculateDays() {
            if (fromDateInput.value && toDateInput.value) {
                const from = new Date(fromDateInput.value);
                const to = new Date(toDateInput.value);
                const diffTime = Math.abs(to - from);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                numDaysInput.value = diffDays;
            }
        }

        fromDateInput.addEventListener('change', calculateDays);
        toDateInput.addEventListener('change', calculateDays);
    }

    // Dropdown sections
    const dropdownButtons = document.querySelectorAll('.zs-dropdown-btn');
    dropdownButtons.forEach(button => {
        button.addEventListener('click', function() {
            const list = this.nextElementSibling;
            const icon = this.querySelector('i');
            
            if (list.style.display === 'none' || list.classList.contains('d-none')) {
                list.style.display = 'block';
                list.classList.remove('d-none');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                list.style.display = 'none';
                list.classList.add('d-none');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        });
    });

    // Initialize all dropdowns as closed
    document.querySelectorAll('.zs-list').forEach(list => {
        list.style.display = 'none';
        list.classList.add('d-none');
    });
});

// Apply leave function
function applyLeaveForMember(employeeId) {
    const modal = new bootstrap.Modal(document.getElementById('applyLeaveModal'));
    modal.show();
}
</script>
@endsection