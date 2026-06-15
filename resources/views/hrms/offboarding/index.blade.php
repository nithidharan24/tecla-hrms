@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Offboarding Approval');
@endphp
@extends('layouts.index')

@section('content')

@php
    $departments = DB::table('department')->get();
@endphp

<div class="content container-fluid mt-3">

    <!-- ==============================================
         MAIN OFFBOARDING TABS
    ================================================= -->
    <ul class="nav leave-tabs mb-0" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $status == 'inprogress' ? 'active' : '' }}" 
               href="{{ route('offboarding.index', ['status' => 'inprogress']) }}">In Progress</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status == 'completed' ? 'active' : '' }}" 
               href="{{ route('offboarding.index', ['status' => 'completed']) }}">Completed</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status == 'rejected' ? 'active' : '' }}" 
               href="{{ route('offboarding.index', ['status' => 'rejected']) }}">Rejected</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status == 'cancelled' ? 'active' : '' }}" 
               href="{{ route('offboarding.index', ['status' => 'cancelled']) }}">Cancelled</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status == 'all' ? 'active' : '' }}" 
               href="{{ route('offboarding.index', ['status' => 'all']) }}">All Requests</a>
        </li>
    </ul>

    <div class="tabs-underline"></div>

    <!-- ==============================================
         TAB CONTENT
    ================================================= -->
    <div class="tab-content pt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ ucfirst($status) }} Offboarding</h5>
                <div class="d-flex align-items-center gap-2">
                    <!-- FILTER BUTTON -->
                    <button type="button" id="filterToggle" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                    @if(isset($permissions) && $permissions->can_create)
                    <!-- ADD REQUEST BUTTON -->
                    <a href="{{ route('offboarding.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> Add Request
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
<table id="offboardingTable" class="table custom-table">
                            <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Department</th>
                                <th>Exit Type</th>
                                <th>Last Working Date</th>
                                <th>Date of Request</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($offboardings as $offboarding)
                                <tr>
                                    <td>{{ $offboarding->employeeid }}</td>
                                    <td>{{ $offboarding->firstname }} {{ $offboarding->lastname }}</td>
                                    <td>{{ $offboarding->department_name }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($offboarding->offboarding_type == 'resignation') bg-warning
                                            @elseif($offboarding->offboarding_type == 'termination') bg-danger
                                            @else bg-secondary @endif">
                                            {{ ucfirst($offboarding->offboarding_type) }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($offboarding->last_working_date)->format('d-M-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($offboarding->created_at)->format('d-M-Y') }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($offboarding->status == 'inprogress') bg-info
                                            @elseif($offboarding->status == 'completed') bg-success
                                            @elseif($offboarding->status == 'rejected') bg-danger
                                            @elseif($offboarding->status == 'cancelled') bg-secondary
                                            @else bg-warning @endif">
                                            {{ ucfirst($offboarding->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(isset($permissions) && $permissions->can_approve)
                                        <a href="{{ route('offboarding.show', $offboarding->id) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @endif
                                        @if(isset($permissions) && $permissions->can_edit)
                                        <a href="{{ route('offboarding.edit', $offboarding->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        @endif
                                        @if(isset($permissions) && $permissions->can_delete)
                                        <form action="{{ route('offboarding.destroy', $offboarding->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        No offboarding requests found.
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

<!-- ============================================================
     FILTER DRAWER (Zoho Style)
=============================================================== -->
<div id="filterOverlay" class="filter-overlay d-none"></div>

<div id="filterPanel" class="filter-drawer">
    <div class="filter-header d-flex justify-content-between align-items-center">
        <h5>Filter</h5>
        <button id="filterClose" class="btn-close"></button>
    </div>

    <form action="{{ route('offboarding.index') }}" method="GET" id="filterForm">
        <input type="hidden" name="status" value="{{ $status }}">
        
        <div class="filter-body">
            <!-- Employee Filter -->
            <div class="mb-3">
                <label class="form-label fw-bold">Employee</label>
                <select class="form-select" name="employee_filter">
                    <option value="all" {{ request('employee_filter') == 'all' ? 'selected' : '' }}>All Employees</option>
                    <option value="specific" {{ request('employee_id') ? 'selected' : '' }}>Specific Employee</option>
                </select>
            </div>

            <!-- Specific Employee (shown when specific is selected) -->
            <div class="mb-3" id="specificEmployeeField" style="{{ request('employee_id') ? '' : 'display: none;' }}">
                <label class="form-label">Select Employee</label>
                <select class="form-select" name="employee_id">
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->employeeid }} - {{ $emp->firstname }} {{ $emp->lastname }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Exit Type Filter -->
            <div class="mb-3">
                <label class="form-label fw-bold">Exit type</label>
                <select class="form-select" name="exit_type">
                    <option value="all" {{ request('exit_type') == 'all' ? 'selected' : '' }}>All Types</option>
                    <option value="resignation" {{ request('exit_type') == 'resignation' ? 'selected' : '' }}>Resignation</option>
                    <option value="termination" {{ request('exit_type') == 'termination' ? 'selected' : '' }}>Termination</option>
                    <option value="deceased" {{ request('exit_type') == 'deceased' ? 'selected' : '' }}>Deceased</option>
                </select>
            </div>

            <!-- Last Working Date Filter -->
            <div class="mb-3">
                <label class="form-label fw-bold">Last working date</label>
                <select class="form-select mb-2" name="date_period" id="datePeriod">
                    <option value="">Select Period</option>
                    <option value="current_year" {{ request('date_period') == 'current_year' ? 'selected' : '' }}>Current Year</option>
                    <option value="last_year" {{ request('date_period') == 'last_year' ? 'selected' : '' }}>Last Year</option>
                    <option value="custom" {{ request('from_date') ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>

            <!-- Custom Date Range (shown when custom is selected) -->
            <div class="mb-3" id="customDateRange" style="{{ request('from_date') ? '' : 'display: none;' }}">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">From</label>
                        <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">To</label>
                        <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">
                    </div>
                </div>
            </div>

            <!-- Department Filter -->
            <div class="mb-3">
                <label class="form-label fw-bold">Department</label>
                <select class="form-select" name="department">
                    <option value="all" {{ request('department') == 'all' ? 'selected' : '' }}>All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->department }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="filter-footer d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="resetFilter">Reset</button>
            <button type="submit" class="btn btn-primary btn-sm">Apply</button>
        </div>
    </form>
</div>

<!-- ============================================================
     CSS
=============================================================== -->
<style>
.leave-tabs .nav-link {
    font-size: 15px;
    font-weight: 500;
    color: #333;
    padding: 10px 18px;
    border-bottom: 3px solid transparent;
}

.leave-tabs .nav-link.active {
    color: rgb(249 115 22);
    border-bottom: 3px solid rgb(249 115 22);
}

.tabs-underline {
    width: 100%;
    height: 2px;
    background: #e5eaf2;
    margin-top: -4px;
    margin-bottom: 12px;
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
    transition: transform 0.35s;
    z-index: 1050;
    display: flex;
    flex-direction: column;
    box-shadow: -5px 0 15px rgba(0,0,0,0.1);
}

.filter-drawer.open {
    transform: translateX(0);
}

.filter-header {
    padding: 15px;
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.filter-body {
    padding: 15px;
    flex: 1;
    overflow-y: auto;
}

.filter-footer {
    padding: 15px;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

.form-label.fw-bold {
    color: #333;
    margin-bottom: 8px;
}

.badge {
    font-size: 12px;
    padding: 4px 8px;
}
</style>

<!-- ============================================================
     JS
=============================================================== -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Filter drawer elements
    const filterToggle = document.getElementById("filterToggle");
    const filterPanel = document.getElementById("filterPanel");
    const filterOverlay = document.getElementById("filterOverlay");
    const filterClose = document.getElementById("filterClose");
    const resetFilter = document.getElementById("resetFilter");
    const filterForm = document.getElementById("filterForm");
    const employeeFilter = document.querySelector('select[name="employee_filter"]');
    const datePeriod = document.getElementById("datePeriod");
    const specificEmployeeField = document.getElementById("specificEmployeeField");
    const customDateRange = document.getElementById("customDateRange");

    // Open filter drawer
    function openFilter() {
        filterPanel.classList.add("open");
        filterOverlay.classList.remove("d-none");
        document.body.style.overflow = "hidden";
    }

    // Close filter drawer
    function closeFilter() {
        filterPanel.classList.remove("open");
        filterOverlay.classList.add("d-none");
        document.body.style.overflow = "auto";
    }

    // Toggle specific employee field
    function toggleEmployeeField() {
        if (employeeFilter.value === 'specific') {
            specificEmployeeField.style.display = 'block';
        } else {
            specificEmployeeField.style.display = 'none';
            specificEmployeeField.querySelector('select').value = '';
        }
    }

    // Toggle custom date range
    function toggleDateRange() {
        if (datePeriod.value === 'custom') {
            customDateRange.style.display = 'block';
        } else {
            customDateRange.style.display = 'none';
            customDateRange.querySelector('input[name="from_date"]').value = '';
            customDateRange.querySelector('input[name="to_date"]').value = '';
        }
    }

    // Reset filter form
    function resetFilterForm() {
        filterForm.reset();
        specificEmployeeField.style.display = 'none';
        customDateRange.style.display = 'none';
        
        // Reset hidden status field
        const statusInput = filterForm.querySelector('input[name="status"]');
        if (statusInput) {
            statusInput.value = '{{ $status }}';
        }
        
        // Submit form to reset filters
        filterForm.submit();
    }

    // Event Listeners
    filterToggle.addEventListener("click", openFilter);
    filterClose.addEventListener("click", closeFilter);
    filterOverlay.addEventListener("click", closeFilter);
    
    employeeFilter.addEventListener("change", toggleEmployeeField);
    datePeriod.addEventListener("change", toggleDateRange);
    resetFilter.addEventListener("click", resetFilterForm);

    // Initialize fields on page load
    toggleEmployeeField();
    toggleDateRange();

    // Close filter when Escape key is pressed
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeFilter();
        }
    });

    // Initialize DataTable if you're using it
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('.datatable').DataTable({
            pageLength: 10,
            responsive: true,
            order: [[5, 'desc']] // Sort by date of request descending
        });
    }
});
</script>

@endsection