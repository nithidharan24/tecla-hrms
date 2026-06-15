@php
    $payrollItemsPermissions = App\Helpers\PermissionHelper::getPermissions('Payroll Items');
    $employeeSalaryPermissions = App\Helpers\PermissionHelper::getPermissions('Employee Salary');
    $automatedPayslipsPermissions = App\Helpers\PermissionHelper::getPermissions('Automated Payslips');
@endphp
@extends('layouts.index')

@section('content')
    <!-- Page Content -->
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                 
                </div>
            </div>
        </div>
        <!-- /Page Header -->
        
        <!-- Page Tab -->
        <div class="page-menu">
            <div class="row">
                <div class="col-sm-12">
                    <ul class="nav nav-tabs nav-tabs-bottom">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_payroll_items">Payroll Items</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_employee_salary">Employee Salary</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_salary_release">Salary Release</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_automated_payslips">Automated Payslips</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_activity_log">Activity Log</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Tab -->
        
        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Payroll Items Tab -->
            <div class="tab-pane show active" id="tab_payroll_items">
                <!-- Inner Tabs for Payroll Items -->
                <ul class="nav nav-pills nav-justified mb-4">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#subtab_additions">Additions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#subtab_overtime">Overtime</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#subtab_deductions">Deductions</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Additions Subt Tab -->
                    <div class="tab-pane show active" id="subtab_additions">
                        <!-- Add Addition Button -->
                        <div class="text-end mb-4 clearfix">
                            @if(isset($payrollItemsPermissions) && $payrollItemsPermissions->can_create)
                            <a href="{{ route('payroll.create')}}" class="btn btn-primary add-btn" type="button">Add Addition</a>
                            @endif
                        </div>
                        <!-- /Add Addition Button -->
                        
                        <!-- Payroll Additions Table -->
                        <div class="payroll-table card">
                            <div class="table-responsive">
                                <table class="table table-hover table-radius datatable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Default/Unit Amount</th>
                                            @if(isset($payrollItemsPermissions) && ($payrollItemsPermissions->can_edit || $payrollItemsPermissions->can_delete))
                                            <th class="text-end">Actions</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($additions as $addition)
                                            <tr>
                                                <td>{{ $addition->name }}</td>
                                                <td>{{ $addition->category }}</td>
                                                <td>₹{{ number_format($addition->unit_amount, 2) }}</td>
                                                <td class="text-end">
                                                    <div class="dropdown dropdown-action">
                                                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="material-icons">more_vert</i>
                                                        </a>
                                                        <!-- Dropdown Delete Button -->
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <!-- Edit Button -->
                                                            @if(isset($payrollItemsPermissions) && $payrollItemsPermissions->can_edit)
                                                            <a class="dropdown-item" href="{{ route('payroll.edit', $addition->id) }}">
                                                                <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                                            </a>
                                                            @endif
                                                            <!-- Delete Button -->
                                                            @if(isset($payrollItemsPermissions) && $payrollItemsPermissions->can_delete)
                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_addition" onclick="setDeleteAdditionId({{ $addition->id }})">
                                                                <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                                            </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach

                                        @if ($additions->isEmpty())
                                            <tr>
                                                <td colspan="4" class="text-center">No additions found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /Payroll Additions Table -->
                    </div>
                    <!-- /Additions Subt Tab -->
                    
                    <!-- Overtime Subt Tab -->
                    <div class="tab-pane" id="subtab_overtime">
                        <!-- Add Overtime Button -->
                        <div class="text-end mb-4 clearfix">
                            @if(isset($payrollItemsPermissions) && $payrollItemsPermissions->can_create)
                            <a href="{{ route('payovertime.create') }}" class="btn btn-primary add-btn" type="button">Add Overtime</a>
                            @endif
                        </div>
                        <!-- /Add Overtime Button -->

                        <!-- Payroll Overtime Table -->
                       <div class="payroll-table card">
                            <div class="table-responsive">
                                <table class="table table-hover table-radius datatable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Hierarchy</th>
                                            <th>Rate</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($overtimes as $overtime)
                                            @php
                                                $hierarchy = DB::table('hierarchies')->find($overtime->hierarchy_id);
                                                $hierarchyName = $hierarchy ? $hierarchy->hierarchy_level : 'N/A';
                                            @endphp
                                            <tr>
                                                <td>{{ $overtime->name }}</td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $hierarchyName }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-success">{{ $overtime->rate_type }}: {{ number_format($overtime->rate, 2) }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="dropdown dropdown-action">
                                                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="material-icons">more_vert</i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            @if(isset($payrollItemsPermissions) && $payrollItemsPermissions->can_edit)
                                                            <a class="dropdown-item" href="{{ route('payovertime.edit', $overtime->id) }}">
                                                                <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                                            </a>
                                                            @endif
                                                            @if(isset($payrollItemsPermissions) && $payrollItemsPermissions->can_delete)
                                                            <a class="dropdown-item" href="#" 
                                                               data-bs-toggle="modal" 
                                                               data-bs-target="#delete_overtime" 
                                                               onclick="setDeleteOvertimeId({{ $overtime->id }})">
                                                               <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                                            </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /Payroll Overtime Table -->
                    </div>
                    <!-- /Overtime Subt Tab -->
                    
                    <!-- Deductions Subt Tab -->
                    <div class="tab-pane" id="subtab_deductions">
                        <!-- Add Deductions Button -->
                        <div class="text-end mb-4 clearfix">
                            @if(isset($payrollItemsPermissions) && $payrollItemsPermissions->can_create)
                            <a href="{{ route('deductions.create') }}" class="btn btn-primary add-btn" type="button">Add Deduction</a>
                            @endif
                        </div>
                        <!-- /Add Deductions Button -->

                        <!-- Payroll Deduction Table -->
                        <div class="payroll-table card">
                            <div class="table-responsive">
                                <table class="table table-hover table-radius datatable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Default/Unit Amount</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($deductions as $deduction)
                                            <tr>
                                                <td>{{ $deduction->name }}</td>
                                                <td>₹{{ number_format($deduction->unit_amount, 2) }}</td>
                                                <td class="text-end">
                                                    <div class="dropdown dropdown-action">
                                                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="material-icons">more_vert</i>
                                                        </a>
                                                        <!-- Dropdown Delete Button -->
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <!-- Edit Button -->
                                                            @if(isset($payrollItemsPermissions) && $payrollItemsPermissions->can_edit)
                                                            <a class="dropdown-item" href="{{ route('deductions.edit', $deduction->id) }}">
                                                                <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                                            </a>
                                                            @endif
                                                            <!-- Delete Button -->
                                                            @if(isset($payrollItemsPermissions) && $payrollItemsPermissions->can_delete)
                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_deduction" onclick="setDeleteDeductionId({{ $deduction->id }})">
                                                                <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                                            </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach

                                        @if ($deductions->isEmpty())
                                            <tr>
                                                <td colspan="3" class="text-center">No deductions found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /Payroll Deduction Table -->
                    </div>
                    <!-- /Deductions Subt Tab -->
                </div>
            </div>
            <!-- /Payroll Items Tab -->
            
            <!-- Employee Salary Tab -->
            <div class="tab-pane" id="tab_employee_salary">
                <form method="POST" action="{{ route('salary.bulk-approval-status') }}" id="bulkApprovalForm">
                    @csrf
                    <input type="hidden" name="approval_status" id="bulkApprovalStatusInput">
                </form>

                <!-- Add Salary and Export Buttons -->
                <div class="d-flex justify-content-between align-items-center gap-2 mb-4 flex-wrap">
                    <div>
                        @if(isset($employeeSalaryPermissions) && $employeeSalaryPermissions->can_edit)
                        <div class="btn-group">
                            <button type="button" class="btn btn-success" onclick="submitBulkApproval('approved')">
                                <i class="fa-solid fa-check"></i> Approve Selected
                            </button>
                            <button type="button" class="btn btn-warning" onclick="submitBulkApproval('hold')">
                                <i class="fa-solid fa-pause"></i> Hold Selected
                            </button>
                        </div>
                        @endif
                    </div>
                    <div class="d-flex justify-content-end align-items-center gap-2 flex-wrap">
                        @if(isset($employeeSalaryPermissions) && $employeeSalaryPermissions->can_edit)
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#bulkUpdateSalaryModal">
                            <i class="fas fa-edit"></i> Bulk Update
                        </button>
                        @endif
                        @if(isset($employeeSalaryPermissions) && $employeeSalaryPermissions->can_create)
                        <a href="{{ route('salary.create') }}" class="btn btn-primary add-btn">Add Salary</a>
                        @endif
                        <a href="{{ route('payroll.employee-salary.export.csv', request()->query()) }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-file-csv"></i> Export CSV
                        </a>
                        <a href="{{ route('payroll.employee-salary.export.pdf', request()->query()) }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-file-pdf"></i> Export PDF
                        </a>
                    </div>
                </div>
                <!-- /Add Salary and Export Buttons -->
                
                <!-- Employee Salary Table -->
                <div class="table-responsive">
                    <!-- Success and Error Messages -->
                    @if (session('success'))
                        <div class="alert alert-success" id="success-message">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger" id="error-message">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <table class="table custom-table mb-0 datatable">
                        <thead>
                            <tr>
                                @if(isset($employeeSalaryPermissions) && $employeeSalaryPermissions->can_edit)
                                <th style="width: 40px;">
                                    <input type="checkbox" id="checkAll">
                                </th>
                                @endif
                                <th>Employee</th>
                                <th>Employee ID</th>
                                <th>Email</th>
                                <th>Join Date</th>
                                <th>Role</th>
                                <th>Salary</th>
                                @if(isset($employeeSalaryPermissions) && ($employeeSalaryPermissions->can_edit || $employeeSalaryPermissions->can_delete))
                                <th class="text-end">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salaries as $salary)
                            <tr class="row-check" id="salary-row-{{ $salary->id }}">
                                @if(isset($employeeSalaryPermissions) && $employeeSalaryPermissions->can_edit)
                                <td>
                                    <input type="checkbox" class="row-checkbox" name="salary_ids[]" value="{{ $salary->id }}" form="bulkApprovalForm">
                                </td>
                                @endif
                                <td data-label="Employee">
                                    <h2 class="table-avatar">
                                        <a href="">
                                            {{ $salary->firstname }} {{ $salary->lastname }} 
                                            <span class="od-chip-highlight">{{ $salary->designation_name }}</span>
                                        </a>
                                    </h2>
                                </td>
                                
                                <td class="high" data-label="Employee ID">{{ $salary->employeeid }}</td>
                                
                                <td class="text-muted" data-label="Email">
                                    <span class="high">{{ $salary->email }}</span>
                                </td>
                                
                                <td data-label="Joining Date">
                                    {{ \Carbon\Carbon::parse($salary->joiningdate)->format('d-m-y') }}
                                </td>
                                
                                <td data-label="Designation">{{ $salary->designation_name }}</td>
                                
                                <td data-label="Net Salary">{{ $salary->net_salary }}</td>

                                @php
                                    $salaryStatus = $salary->approval_status ?? 'pending';
                                    $salaryStatusClasses = [
                                        'pending' => 'salary-status-pending',
                                        'approved' => 'salary-status-approved',
                                        'hold' => 'salary-status-hold',
                                    ];
                                    $salaryStatusLabels = [
                                        'pending' => 'Pending',
                                        'approved' => 'Approved',
                                        'hold' => 'Hold',
                                    ];
                                @endphp
                                
                                <td class="text-end od-inline-actions" data-label="Actions">
                                    @if(isset($employeeSalaryPermissions) && $employeeSalaryPermissions->can_edit)
                                    <div class="dropdown d-inline-block">
                                        <button class="salary-status-action {{ $salaryStatusClasses[$salaryStatus] ?? 'salary-status-pending' }} dropdown-toggle"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                            title="Salary Status">
                                            {{ $salaryStatusLabels[$salaryStatus] ?? 'Pending' }}
                                        </button>
                                        <div class="dropdown-menu">
                                            @foreach($salaryStatusLabels as $statusValue => $statusLabel)
                                                <form method="POST" action="{{ route('salary.update-approval-status', $salary->id) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="approval_status" value="{{ $statusValue }}">
                                                    <button type="submit" class="dropdown-item {{ $salaryStatus === $statusValue ? 'active' : '' }}">
                                                        {{ $statusLabel }}
                                                    </button>
                                                </form>
                                            @endforeach
                                        </div>
                                    </div>
                                    @else
                                    <span class="salary-status-action {{ $salaryStatusClasses[$salaryStatus] ?? 'salary-status-pending' }}">
                                        {{ $salaryStatusLabels[$salaryStatus] ?? 'Pending' }}
                                    </span>
                                    @endif

                                    @if(isset($employeeSalaryPermissions) && $employeeSalaryPermissions->can_edit)
                                    <a href="{{ route('salary.edit', $salary->id) }}" class="od-icon-btn" title="Edit">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    @endif
                                
                                    @if(isset($employeeSalaryPermissions) && $employeeSalaryPermissions->can_delete)
                                    <a href="#" class="od-icon-btn" data-bs-toggle="modal" data-bs-target="#deleteSalaryModal_{{ $salary->id }}" title="Delete">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </a>
                                    @endif
                                
                                    <form method="POST" action="{{ route('salary.send-hike-letter', $salary->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="od-icon-btn" title="Send Hike Letter">
                                            <i class="fa-solid fa-envelope"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Delete Confirmation Modal -->
                            <div class="modal fade" id="deleteSalaryModal_{{ $salary->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteSalaryLabel_{{ $salary->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteSalaryLabel_{{ $salary->id }}">Confirm Delete</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete this salary record for {{ $salary->firstname }} {{ $salary->lastname }}?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <form method="POST" action="{{ route('salary.destroy', $salary->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /Employee Salary Table -->
            </div>
            <!-- /Employee Salary Tab -->

            <!-- Salary Release Tab -->
            <div class="tab-pane" id="tab_salary_release">
                @if (session('success'))
                    <div class="alert alert-success" id="release-success-message">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger" id="release-error-message">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('salary.bulk-release-status') }}" id="salaryReleaseForm">
                    @csrf
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                        <div>
                            <h4 class="mb-1">Approved Salary Employees</h4>
                            <p class="text-muted mb-0">Only released salary records will be used for automated payslip generation.</p>
                        </div>

                        @if(isset($employeeSalaryPermissions) && $employeeSalaryPermissions->can_edit)
                            <div class="btn-group">
                                <button type="submit" name="release_status" value="released" class="btn btn-success">
                                    <i class="fa-solid fa-check"></i> Release Salary
                                </button>
                                <button type="submit" name="release_status" value="hold" class="btn btn-warning">
                                    <i class="fa-solid fa-pause"></i> Hold Salary
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table custom-table mb-0 datatable">
                            <thead>
                                <tr>
                                    @if(isset($employeeSalaryPermissions) && $employeeSalaryPermissions->can_edit)
                                        <th style="width: 40px;">
                                            <input type="checkbox" id="releaseCheckAll">
                                        </th>
                                    @endif
                                    <th>Employee</th>
                                    <th>Employee ID</th>
                                    <th>Email</th>
                                    <th>Join Date</th>
                                    <th>Role</th>
                                    <th>Salary</th>
                                    <th>Release Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($approvedSalaries as $salary)
                                    @php
                                        $releaseStatus = $salary->release_status ?? 'hold';
                                        $releaseStatusClasses = [
                                            'released' => 'salary-status-approved',
                                            'hold' => 'salary-status-hold',
                                        ];
                                        $releaseStatusLabels = [
                                            'released' => 'Released',
                                            'hold' => 'Hold',
                                        ];
                                    @endphp
                                    <tr class="release-row-check">
                                        @if(isset($employeeSalaryPermissions) && $employeeSalaryPermissions->can_edit)
                                            <td>
                                                <input type="checkbox" class="release-row-checkbox" name="salary_ids[]" value="{{ $salary->id }}">
                                            </td>
                                        @endif
                                        <td data-label="Employee">
                                            <h2 class="table-avatar">
                                                <a href="">
                                                    {{ $salary->firstname }} {{ $salary->lastname }}
                                                    <span class="od-chip-highlight">{{ $salary->designation_name }}</span>
                                                </a>
                                            </h2>
                                        </td>
                                        <td class="high" data-label="Employee ID">{{ $salary->employeeid }}</td>
                                        <td class="text-muted" data-label="Email">
                                            <span class="high">{{ $salary->email }}</span>
                                        </td>
                                        <td data-label="Joining Date">
                                            {{ \Carbon\Carbon::parse($salary->joiningdate)->format('d-m-y') }}
                                        </td>
                                        <td data-label="Designation">{{ $salary->designation_name }}</td>
                                        <td data-label="Net Salary">{{ $salary->net_salary }}</td>
                                        <td data-label="Release Status">
                                            <span class="salary-status-action {{ $releaseStatusClasses[$releaseStatus] ?? 'salary-status-hold' }}">
                                                {{ $releaseStatusLabels[$releaseStatus] ?? 'Hold' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ (isset($employeeSalaryPermissions) && $employeeSalaryPermissions->can_edit) ? 8 : 7 }}" class="text-center">
                                            No approved salary employees found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <!-- /Salary Release Tab -->

            <!-- Automated Payslips Tab -->
            <div class="tab-pane" id="tab_automated_payslips">
                <!-- Generate Payslips Button -->
                <div class="text-end mb-4 clearfix">
                    @if(isset($automatedPayslipsPermissions) && $automatedPayslipsPermissions->can_create)
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generatePayslipsModal">
                        <i class="fas fa-cogs"></i> Generate Payslips
                    </button>
                    @endif
                </div>

                <!-- Summary Statistics -->
                <div class="row g-3 mb-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex align-items-center justify-content-between border-start border-4 border-info bg-white shadow-sm p-3 rounded">
                            <div>
                                <h3 class="fw-bold mb-1">{{ $summaryStats['total_payslips'] ?? 0 }}</h3>
                                <p class="mb-0 text-muted">Total Payslips</p>
                            </div>
                            <i class="fas fa-file-alt fs-2 text-info"></i>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex align-items-center justify-content-between border-start border-4 border-success bg-white shadow-sm p-3 rounded">
                            <div>
                                <h3 class="fw-bold mb-1">{{ $summaryStats['emails_sent'] ?? 0 }}</h3>
                                <p class="mb-0 text-muted">Emails Sent</p>
                            </div>
                            <i class="fas fa-envelope fs-2 text-success"></i>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex align-items-center justify-content-between border-start border-4 border-warning bg-white shadow-sm p-3 rounded">
                            <div>
                                <h3 class="fw-bold mb-1">₹{{ number_format($summaryStats['total_payout'] ?? 0, 0) }}</h3>
                                <p class="mb-0 text-muted">Total Payout</p>
                            </div>
                            <i class="fas fa-money-bill-wave fs-2 text-warning"></i>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex align-items-center justify-content-between border-start border-4 border-danger bg-white shadow-sm p-3 rounded">
                            <div>
                                <h3 class="fw-bold mb-1">{{ $summaryStats['failed_count'] ?? 0 }}</h3>
                                <p class="mb-0 text-muted">Failed</p>
                            </div>
                            <i class="fas fa-exclamation-triangle fs-2 text-danger"></i>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <form method="GET" action="{{ route('payroll.combined') }}" class="mb-4">
                    <input type="hidden" name="tab" value="automated_payslips">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="month">Month:</label>
                            <select name="month" id="month" class="form-control">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ ($month ?? date('m')) == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="year">Year:</label>
                            <select name="year" id="year" class="form-control">
                                @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                    <option value="{{ $i }}" {{ ($year ?? date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="employee_id">Employee:</label>
                            <select name="employee_id" id="employee_id" class="form-control">
                                <option value="">All Employees</option>
                                @foreach($payslipEmployees as $employee)
                                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->employeeid }} - {{ $employee->firstname }} {{ $employee->lastname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary form-control">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Payslips Table -->
                <div class="table-responsive">
                    <table class="table custom-table datatable">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Employee ID</th>
                                <th>Designation</th>
                                <th>Month</th>
                                <th>Net Salary</th>
                                <th>Working Days</th>
                                <th>Status</th>
                                <th>Email Sent</th>
                                <th>Generated At</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payslips as $payslip)
                            <tr>
                                <td data-label="Employee">
                                    <strong>{{ $payslip->firstname }} {{ $payslip->lastname }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $payslip->email }}</small>
                                </td>
                                
                                <td data-label="Employee ID">{{ $payslip->employeeid }}</td>
                                
                                <td data-label="Designation">{{ $payslip->designation_name ?? 'N/A' }}</td>
                                
                                <td data-label="Payroll Month">{{ $payslip->payroll_month_formatted }}</td>
                                
                                <td data-label="Net Salary">
                                    <strong class="text-success">₹{{ number_format($payslip->net_salary, 2) }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        Gross: ₹{{ number_format($payslip->total_earnings, 2) }}
                                    </small>
                                </td>
                                
                                <td data-label="Attendance">
                                    {{ $payslip->actual_working_days }}/{{ $payslip->total_working_days }}
                                    @if($payslip->overtime_hours > 0)
                                        <br><small class="text-info">OT: {{ $payslip->overtime_hours }}h</small>
                                    @endif
                                </td>
                                
                                <td data-label="Status">
                                    @if($payslip->status == 'sent')
                                        <span class="badge bg-success">Sent</span>
                                    @elseif($payslip->status == 'generated')
                                        <span class="badge bg-warning">Generated</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </td>
                                
                                <td data-label="Email Sent">
                                    @if($payslip->email_sent)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> Yes
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($payslip->email_sent_at)->format('d M, h:i A') }}
                                        </small>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-times"></i> No
                                        </span>
                                    @endif
                                </td>
                                
                                <td data-label="Generated At">
                                    {{ \Carbon\Carbon::parse($payslip->generated_at)->format('d M Y, h:i A') }}
                                </td>
                                
                                <td data-label="Actions" class="text-end">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="material-icons">more_vert</i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="{{ route('automated-payslips.show', $payslip->id) }}">
                                                <i class="fas fa-eye m-r-5"></i> View Details
                                            </a>
                                            @if($payslip->pdf_path)
                                            <a class="dropdown-item" href="{{ route('automated-payslips.download', $payslip->id) }}">
                                                <i class="fas fa-download m-r-5"></i> Download PDF
                                            </a>
                                            @endif
                                            @if($payslip->email && isset($automatedPayslipsPermissions) && $automatedPayslipsPermissions->can_edit)
                                            <form method="POST" action="{{ route('automated-payslips.resend-email', $payslip->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item" onclick="return confirm('Resend payslip email to {{ $payslip->email }}?')">
                                                    <i class="fas fa-envelope m-r-5"></i> Resend Email
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">
                                    <div class="py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No payslips found</h5>
                                        <p class="text-muted">Generate payslips for the selected month to see them here.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /Automated Payslips Tab -->

          <!-- Activity Log Tab -->
<div class="tab-pane" id="tab_activity_log">
    <!-- Filter Toggle and Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">
                <i class="fas fa-history me-2"></i> Activity Logs
                @if($activityLogs->count() > 0)
                    <small class="text-muted">({{ $activityLogs->total() }} records)</small>
                @endif
            </h5>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" type="button" 
                    data-bs-toggle="collapse" data-bs-target="#activityLogFiltersCollapse" 
                    aria-expanded="false" aria-controls="activityLogFiltersCollapse">
                <i class="fas fa-filter"></i> Filter
            </button>
            <div class="btn-group btn-group-sm" role="group">
                <input type="radio" class="btn-check" name="viewMode" id="listView" autocomplete="off" checked>
                <label class="btn btn-outline-primary" for="listView" onclick="switchPayrollViewMode('list')">
                    <i class="fas fa-list"></i>
                </label>
                <input type="radio" class="btn-check" name="viewMode" id="tableView" autocomplete="off">
                <label class="btn btn-outline-primary" for="tableView" onclick="switchPayrollViewMode('table')">
                    <i class="fas fa-table"></i> 
                </label>
            </div>
        </div>
    </div>

    <!-- Active Filters Display -->
    @if(request()->hasAny(['action_type', 'module', 'date']))
    <div class="mb-3">
        <div class="d-flex flex-wrap gap-2">
            <span class="badge bg-light text-dark border">Active Filters:</span>
            @if(request('action_type'))
                <span class="badge bg-info">
                    Action: {{ ucfirst(request('action_type')) }}
                    <button type="button" class="btn-close btn-close-white ms-1" style="font-size: 0.7rem;" 
                            onclick="removePayrollFilter('action_type')"></button>
                </span>
            @endif
            @if(request('module'))
                <span class="badge bg-info">
                    Module: {{ request('module') }}
                    <button type="button" class="btn-close btn-close-white ms-1" style="font-size: 0.7rem;" 
                            onclick="removePayrollFilter('module')"></button>
                </span>
            @endif
            @if(request('date'))
                <span class="badge bg-info">
                    Date: {{ request('date') }}
                    <button type="button" class="btn-close btn-close-white ms-1" style="font-size: 0.7rem;" 
                            onclick="removePayrollFilter('date')"></button>
                </span>
            @endif
        </div>
    </div>
    @endif

    <!-- Collapsible Filters -->
    <div class="collapse mb-3" id="activityLogFiltersCollapse">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('payroll.combined') }}" id="activityLogFilterForm">
                    <input type="hidden" name="tab" value="activity_log">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="activity_action" class="form-label">Action Type:</label>
                            <select name="action_type" id="activity_action" class="form-control">
                                <option value="">All Actions</option>
                                <option value="create" {{ request('action_type') == 'create' ? 'selected' : '' }}>Create</option>
                                <option value="update" {{ request('action_type') == 'update' ? 'selected' : '' }}>Update</option>
                                <option value="delete" {{ request('action_type') == 'delete' ? 'selected' : '' }}>Delete</option>
                                <option value="generate" {{ request('action_type') == 'generate' ? 'selected' : '' }}>Generate</option>
                                <option value="send_email" {{ request('action_type') == 'send_email' ? 'selected' : '' }}>Send Email</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="activity_module" class="form-label">Module:</label>
                            <select name="module" id="activity_module" class="form-control">
                                <option value="">All Modules</option>
                                <option value="Payroll Items" {{ request('module') == 'Payroll Items' ? 'selected' : '' }}>Payroll Items</option>
                                <option value="Employee Salary" {{ request('module') == 'Employee Salary' ? 'selected' : '' }}>Employee Salary</option>
                                <option value="Automated Payslips" {{ request('module') == 'Automated Payslips' ? 'selected' : '' }}>Automated Payslips</option>
                                <option value="Additions" {{ request('module') == 'Additions' ? 'selected' : '' }}>Additions</option>
                                <option value="Deductions" {{ request('module') == 'Deductions' ? 'selected' : '' }}>Deductions</option>
                                <option value="Overtime" {{ request('module') == 'Overtime' ? 'selected' : '' }}>Overtime</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="activity_date" class="form-label">Date:</label>
                            <input type="date" name="date" id="activity_date" class="form-control" 
                                   value="{{ request('date') }}">
                        </div>
                        <div class="col-md-12">
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-secondary" onclick="resetPayrollFilters()">
                                    <i class="fas fa-redo"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Apply Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($activityLogs->count() > 0)
        <!-- List View -->
        <div id="payrollListViewContent" class="view-content">
            <div class="activity-log-container">
                <div class="timeline">
                    @foreach($activityLogs as $log)
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                @php
                                    $actionIcons = [
                                        'create' => 'fa-plus-circle text-success',
                                        'update' => 'fa-edit text-warning',
                                        'delete' => 'fa-trash text-danger',
                                        'generate' => 'fa-cogs text-primary',
                                        'send_email' => 'fa-envelope text-info'
                                    ];
                                    $icon = $actionIcons[$log->action] ?? 'fa-history text-secondary';
                                @endphp
                                <i class="fas {{ $icon }}"></i>
                            </div>
                            
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <span class="badge bg-info">{{ $log->module }}</span>
                                                @if($log->employee_id)
                                                    <span class="ms-2">
                                                        <i class="fas fa-user me-1"></i>{{ $log->employeeid ?? 'N/A' }}
                                                    </span>
                                                @endif
                                            </h6>
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="badge bg-light text-dark border">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, h:i A') }}
                                                </span>
                                                <span class="badge bg-{{ $log->user_type == 'admin' ? 'primary' : ($log->user_type == 'employee' ? 'success' : 'secondary') }}">
                                                    {{ ucfirst($log->user_type) }}
                                                </span>
                                            </div>
                                        </div>
                                        <span class="badge bg-light text-dark">
                                            {{ ucfirst($log->action) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="timeline-body">
                                    <div class="d-flex align-items-start gap-3 mb-2">
                                        <div class="avatar-sm">
                                            @php
                                                $initials = '';
                                                if ($log->user_name) {
                                                    $nameParts = explode(' ', $log->user_name);
                                                    foreach ($nameParts as $part) {
                                                        $initials .= strtoupper(substr($part, 0, 1));
                                                    }
                                                    $initials = substr($initials, 0, 2);
                                                }
                                            @endphp
                                            <div class="avatar-title" style="background-color: {{ $log->user_type == 'admin' ? '#0d6efd' : ($log->user_type == 'employee' ? '#198754' : '#6c757d') }}; color: white;">
                                                {{ $initials ?: 'S' }}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-1">
                                                <strong>{{ $log->user_name ?? 'System' }}</strong>
                                                @if($log->email)
                                                    <small class="text-muted ms-2">{{ $log->email }}</small>
                                                @endif
                                            </p>
                                            <p class="mb-2">{{ $log->description }}</p>
                                            @if($log->ip_address)
                                                <small class="text-muted">
                                                    <i class="fas fa-globe me-1"></i> {{ $log->ip_address }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($log->old_data || $log->new_data)
                                        <div class="mt-3">
                                            <button class="btn btn-sm btn-outline-info" 
                                                    type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#changes-{{ $log->id }}">
                                                <i class="fas fa-eye me-1"></i> View Details
                                            </button>
                                            <div class="collapse mt-2" id="changes-{{ $log->id }}">
                                                <div class="card card-body bg-light">
                                                    <h6 class="mb-3">Changes Made:</h6>
                                                    @if($log->old_data && $log->new_data)
                                                        @php
                                                            $oldData = json_decode($log->old_data, true);
                                                            $newData = json_decode($log->new_data, true);
                                                        @endphp
                                                        @if(is_array($oldData) && is_array($newData))
                                                            <div class="row">
                                                                @foreach($oldData as $key => $oldValue)
                                                                    @if(isset($newData[$key]) && $oldValue != $newData[$key])
                                                                        <div class="col-md-6 mb-2">
                                                                            <div class="change-item p-2 border rounded">
                                                                                <small class="text-muted d-block mb-1">
                                                                                    {{ ucfirst(str_replace('_', ' ', $key)) }}
                                                                                </small>
                                                                                <div class="d-flex align-items-center">
                                                                                    <div class="text-danger me-2">
                                                                                        <small>Old:</small>
                                                                                        <div class="fw-semibold">
                                                                                            {{ is_array($oldValue) ? json_encode($oldValue) : $oldValue }}
                                                                                        </div>
                                                                                    </div>
                                                                                    <i class="fas fa-arrow-right text-muted mx-2"></i>
                                                                                    <div class="text-success">
                                                                                        <small>New:</small>
                                                                                        <div class="fw-semibold">
                                                                                            {{ is_array($newData[$key]) ? json_encode($newData[$key]) : $newData[$key] }}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="row">
                                                                <div class="col-md-6 mb-2">
                                                                    <div class="change-item p-2 border rounded">
                                                                        <small class="text-muted d-block mb-1">Old Data</small>
                                                                        <div class="fw-semibold">{{ $log->old_data }}</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <div class="change-item p-2 border rounded">
                                                                        <small class="text-muted d-block mb-1">New Data</small>
                                                                        <div class="fw-semibold">{{ $log->new_data }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @elseif($log->old_data)
                                                        <div class="alert alert-warning mb-0">
                                                            <small>Old Data: {{ $log->old_data }}</small>
                                                        </div>
                                                    @elseif($log->new_data)
                                                        <div class="alert alert-success mb-0">
                                                            <small>New Data: {{ $log->new_data }}</small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Table View -->
        <div id="payrollTableViewContent" class="view-content d-none">
            <div class="table-responsive">
                <table class="table custom-table" id="payrollActivityLogTable">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Employee ID</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activityLogs as $log)
                        <tr>
                            <td data-label="Date & Time">
                                {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, h:i A') }}
                            </td>
                            
                            <td data-label="User">
                                @if($log->user_type == 'admin')
                                    <strong>Admin: {{ $log->user_name ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $log->email ?? '' }}</small>
                                @elseif($log->user_type == 'employee')
                                    <strong>{{ $log->user_name ?? '' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $log->email ?? '' }}</small>
                                @else
                                    <span class="text-muted">System</span>
                                @endif
                            </td>
                            
                            <td data-label="Role">
                                <span class="badge 
                                    @if($log->user_type == 'admin') bg-primary
                                    @elseif($log->user_type == 'employee') bg-success
                                    @else bg-secondary @endif">
                                    {{ ucfirst($log->user_type) }}
                                </span>
                            </td>
                            
                            <td data-label="Module">
                                <span class="badge bg-info">{{ $log->module }}</span>
                            </td>
                            
                            <td data-label="Action">
                                @if($log->action == 'create')
                                    <span class="badge bg-success">
                                        <i class="fas fa-plus-circle"></i> Create
                                    </span>
                                @elseif($log->action == 'update')
                                    <span class="badge bg-warning">
                                        <i class="fas fa-edit"></i> Update
                                    </span>
                                @elseif($log->action == 'delete')
                                    <span class="badge bg-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </span>
                                @elseif($log->action == 'generate')
                                    <span class="badge bg-primary">
                                        <i class="fas fa-cogs"></i> Generate
                                    </span>
                                @elseif($log->action == 'send_email')
                                    <span class="badge bg-info">
                                        <i class="fas fa-envelope"></i> Send Email
                                    </span>
                                @else
                                    <span class="badge bg-secondary">{{ $log->action }}</span>
                                @endif
                            </td>
                            
                            <td data-label="Description">
                                {{ $log->description }}
                                @if($log->old_data || $log->new_data)
                                    <br>
                                    <small class="text-muted">
                                        <button type="button" class="btn btn-sm btn-outline-info mt-1" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#table-changes-{{ $log->id }}">
                                            View Details
                                        </button>
                                    </small>
                                    <div class="collapse mt-2" id="table-changes-{{ $log->id }}">
                                        <div class="card card-body">
                                            @if($log->old_data && $log->new_data)
                                                <h6>Changes:</h6>
                                                @php
                                                    $oldData = json_decode($log->old_data, true);
                                                    $newData = json_decode($log->new_data, true);
                                                @endphp
                                                @if(is_array($oldData) && is_array($newData))
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <th>Field</th>
                                                            <th>Old Value</th>
                                                            <th>New Value</th>
                                                        </tr>
                                                        @foreach($oldData as $key => $oldValue)
                                                            @if(isset($newData[$key]) && $oldValue != $newData[$key])
                                                            <tr>
                                                                <td>{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                                                                <td class="text-danger">{{ is_array($oldValue) ? json_encode($oldValue) : $oldValue }}</td>
                                                                <td class="text-success">{{ is_array($newData[$key]) ? json_encode($newData[$key]) : $newData[$key] }}</td>
                                                            </tr>
                                                            @endif
                                                        @endforeach
                                                    </table>
                                                @else
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <strong>Old Data:</strong>
                                                            <p>{{ $log->old_data }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>New Data:</strong>
                                                            <p>{{ $log->new_data }}</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            @elseif($log->old_data)
                                                <strong>Old Data:</strong>
                                                <p>{{ $log->old_data }}</p>
                                            @elseif($log->new_data)
                                                <strong>New Data:</strong>
                                                <p>{{ $log->new_data }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </td>
                            
                            <td data-label="Employee ID">
                                @if($log->employee_id)
                                    {{ $log->employeeid ?? 'N/A' }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            
                            <td data-label="IP Address">
                                <small class="text-muted">{{ $log->ip_address }}</small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($activityLogs->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $activityLogs->links() }}
        </div>
        @endif
    @else
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-history"></i></div>
            <div class="empty-state-title">No activity logs found</div>
            <div class="empty-state-text">
                @if(request()->hasAny(['action_type', 'module', 'date']))
                    No activity logs found with the current filters.
                    <div class="mt-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="resetPayrollFilters()">
                            Clear Filters
                        </button>
                    </div>
                @else
                    Activity will appear here as you perform actions.
                @endif
            </div>
        </div>
    @endif
</div>
<!-- /Activity Log Tab -->

<style>
    .salary-status-action {
        align-items: center;
        border: 1px solid;
        border-radius: 6px;
        display: inline-flex;
        font-size: 13px;
        font-weight: 600;
        height: 38px;
        justify-content: center;
        line-height: 1;
        min-width: 88px;
        padding: 0 10px;
        vertical-align: middle;
    }

    .salary-status-action.dropdown-toggle::after {
        margin-left: 6px;
    }

    .salary-status-pending {
        background: #fff7e6;
        border-color: #ffb020;
        color: #b76e00;
    }

    .salary-status-approved {
        background: #eaf8f0;
        border-color: #28a745;
        color: #16783a;
    }

    .salary-status-hold {
        background: #f1f3f5;
        border-color: #6c757d;
        color: #495057;
    }

    /* Activity Log Styles */
    .view-content {
        transition: opacity 0.3s ease;
    }
    
    .filter-pill {
        cursor: pointer;
    }
    
    .filter-pill .btn-close {
        padding: 0.3rem;
        font-size: 0.7rem;
    }
    
    /* Timeline Styles for List View */
    .activity-log-container {
        max-width: 100%;
        margin: 0 auto;
    }

    .timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 25px;
        padding-left: 50px;
    }

    .timeline-marker {
        position: absolute;
        left: 10px;
        top: 0;
        width: 24px;
        height: 24px;
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
    }

    .timeline-marker i {
        font-size: 12px;
    }

    .timeline-content {
        background: white;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 3px solid #0d6efd;
    }

    .timeline-header {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

    .timeline-body {
        font-size: 0.9rem;
    }

    .avatar-sm {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-title {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
    }

    .change-item {
        transition: all 0.3s ease;
    }

    .change-item:hover {
        background: #f8f9fa;
        transform: translateY(-2px);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state-icon {
        font-size: 64px;
        color: #d1d5db;
        margin-bottom: 20px;
    }

    .empty-state-title {
        font-size: 18px;
        color: #666;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .empty-state-text {
        color: #999;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .timeline::before {
            left: 15px;
        }

        .timeline-item {
            padding-left: 40px;
        }

        .timeline-marker {
            left: 5px;
            width: 20px;
            height: 20px;
        }

        .timeline-marker i {
            font-size: 10px;
        }
        
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    }
</style>

<script>
// Global variable for DataTable
let payrollActivityLogDataTable = null;

// View Mode Switching
function switchPayrollViewMode(mode) {
    if (mode === 'table') {
        document.getElementById('payrollTableViewContent').classList.remove('d-none');
        document.getElementById('payrollListViewContent').classList.add('d-none');
        
        // Initialize DataTable when switching to table view
        setTimeout(initializePayrollDataTable, 50);
    } else {
        document.getElementById('payrollTableViewContent').classList.add('d-none');
        document.getElementById('payrollListViewContent').classList.remove('d-none');
        
        // Destroy DataTable when switching away from table view
        if (payrollActivityLogDataTable !== null) {
            payrollActivityLogDataTable.destroy();
            payrollActivityLogDataTable = null;
        }
    }
}

// Initialize DataTable
function initializePayrollDataTable() {
    // Destroy existing DataTable if it exists
    if (payrollActivityLogDataTable !== null) {
        payrollActivityLogDataTable.destroy();
        payrollActivityLogDataTable = null;
    }
    
    // Check if the table element exists and has data
    const table = document.getElementById('payrollActivityLogTable');
    if (table && $('#payrollActivityLogTable tbody tr').length > 0) {
        payrollActivityLogDataTable = $('#payrollActivityLogTable').DataTable({
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            responsive: true,
            order: [[0, 'desc']], // Sort by Date column descending
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                zeroRecords: "No matching records found",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    }
}

// Reset Filters
function resetPayrollFilters() {
    const form = document.getElementById('activityLogFilterForm');
    const inputs = form.querySelectorAll('select, input[type="date"]');
    
    inputs.forEach(input => {
        if (input.type === 'date') {
            input.value = '';
        } else if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        }
    });
    
    form.submit();
}

// Remove individual filter
function removePayrollFilter(filterName) {
    const form = document.getElementById('activityLogFilterForm');
    const input = form.querySelector(`[name="${filterName}"]`);
    
    if (input) {
        if (input.type === 'date') {
            input.value = '';
        } else if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        }
    }
    
    form.submit();
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Set default view mode to List View
    switchPayrollViewMode('list');
    
    // Show filter section if there are active filters
    if (window.location.search.includes('action_type') || 
        window.location.search.includes('module') || 
        window.location.search.includes('date')) {
        const filterCollapse = new bootstrap.Collapse(document.getElementById('activityLogFiltersCollapse'), {
            toggle: true
        });
    }
    
    // Auto-submit form when date input changes
    const dateInput = document.getElementById('activity_date');
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('activityLogFilterForm').submit();
            }
        });
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    if (payrollActivityLogDataTable !== null) {
        payrollActivityLogDataTable.columns.adjust();
    }
});
</script>
        </div>
        <!-- /Tab Content -->
    </div>
    <!-- /Page Content -->
    
    <!-- Delete Modals (same as before) -->
    <!-- Delete Addition Modal -->
    <div class="modal fade" id="delete_addition" tabindex="-1" aria-labelledby="deleteAdditionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-0">
                    <h5 class="modal-title w-100 text-center" id="deleteAdditionModalLabel" style="font-weight: bold;">Delete Addition</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    Are you sure you want to delete this addition?
                </div>
                <div class="modal-footer d-flex justify-content-around border-0">
                    <button type="button" class="btn btn-outline-warning btn-lg" data-bs-dismiss="modal" style="border-radius: 50px; width: 150px;">Cancel</button>
                    <form id="delete-addition-form" method="POST" action="{{ route('payroll.destroy', '') }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="additionId" name="additionId" value="">
                        <button type="submit" class="btn btn-danger btn-lg" style="border-radius: 50px; width: 150px;">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Overtime Modal -->
    <div class="modal fade" id="delete_overtime" tabindex="-1" aria-labelledby="deleteOvertimeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-0">
                    <h5 class="modal-title w-100 text-center" id="deleteOvertimeModalLabel" style="font-weight: bold;">Delete Overtime</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    Are you sure you want to delete this overtime record?
                </div>
                <div class="modal-footer d-flex justify-content-around border-0">
                    <button type="button" class="btn btn-outline-warning btn-lg" data-bs-dismiss="modal" style="border-radius: 50px; width: 150px;">Cancel</button>
                    <form id="delete-overtime-form" method="POST" action="" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="overtimeId" name="overtimeId" value="">
                        <button type="submit" class="btn btn-danger btn-lg" style="border-radius: 50px; width: 150px;">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Deduction Modal -->
    <div class="modal fade" id="delete_deduction" tabindex="-1" aria-labelledby="deleteDeductionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-0">
                    <h5 class="modal-title w-100 text-center" id="deleteDeductionModalLabel" style="font-weight: bold;">Delete Deduction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    Are you sure you want to delete this deduction?
                </div>
                <div class="modal-footer d-flex justify-content-around border-0">
                    <button type="button" class="btn btn-outline-warning btn-lg" data-bs-dismiss="modal" style="border-radius: 50px; width: 150px;">Cancel</button>
                    <form id="delete-deduction-form" method="POST" action="{{ route('deductions.destroy', '') }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="deductionId" name="deductionId" value="">
                        <button type="submit" class="btn btn-danger btn-lg" style="border-radius: 50px; width: 150px;">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Generate Payslips Modal -->
    <div class="modal fade" id="generatePayslipsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('automated-payslips.generate') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Generate Monthly Payslips</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            This will generate payslips only for employees whose approved salary is released.
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="generate_month">Month:</label>
                            <select name="month" id="generate_month" class="form-control" required>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ ($month ?? date('m')) == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="generate_year">Year:</label>
                            <select name="year" id="generate_year" class="form-control" required>
                                @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                                    <option value="{{ $i }}" {{ ($year ?? date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Note:</strong> This process will:
                            <ul class="mb-0 mt-2">
                                <li>Calculate salaries based on attendance, leave, and overtime data</li>
                                <li>Generate PDF payslips only for released salary employees</li>
                                <li>Send email notifications with payslip attachments</li>
                                <li>Skip employees who already have payslips for this month</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-cogs"></i> Generate Payslips
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Update Salary Modal -->
    <div class="modal fade" id="bulkUpdateSalaryModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form method="POST" action="{{ route('salary.bulk-update-field') }}">
                    @csrf
                    <div class="modal-header bg-light border-bottom-0 pb-3 rounded-top-4">
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="fas fa-layer-group text-primary me-2"></i> Bulk Update Salary Component
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-primary bg-primary-subtle border-0 rounded-3 mb-4 d-flex align-items-center">
                            <i class="fas fa-info-circle fs-4 text-primary me-3"></i>
                            <div class="fs-6 text-dark">
                                Apply a new value across <strong>all existing employee salary records</strong>. The net salary will be automatically recalculated.
                            </div>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="update_field" class="form-label fw-semibold text-secondary">Select Component to Update</label>
                            <select name="field" id="update_field" class="form-select form-select-lg shadow-none border-secondary-subtle" required onchange="updateValueFormatHint()">
                                <option value="" disabled selected>-- Choose a Salary Component --</option>
                                <optgroup label="Earnings">
                                    <option value="basic" data-format="Amount (₹)">Basic Salary</option>
                                    <option value="da" data-format="Percentage (%)">DA (Dearness Allowance %)</option>
                                    <option value="hra" data-format="Percentage (%)">HRA (House Rent Allowance %)</option>
                                    <option value="conveyance" data-format="Amount (₹)">Conveyance</option>
                                    <option value="allowance" data-format="Amount (₹)">Other Allowance</option>
                                    <option value="medical" data-format="Amount (₹)">Medical Allowance</option>
                                </optgroup>
                                <optgroup label="Deductions">
                                    <option value="pf" data-format="Percentage (%)">PF (Provident Fund %)</option>
                                    <option value="esi" data-format="Percentage (%)">ESI (%)</option>
                                    <option value="tds" data-format="Amount (₹)">TDS</option>
                                    <option value="tax" data-format="Amount (₹)">Professional Tax</option>
                                    <option value="welfare" data-format="Amount (₹)">Staff Welfare</option>
                                </optgroup>
                            </select>
                        </div>
                        
                        <div class="form-group mb-2">
                            <label for="update_value" class="form-label fw-semibold text-secondary">New Value <span id="valueFormatHint" class="badge bg-secondary ms-1 fw-normal"></span></label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-secondary-subtle text-muted" id="valuePrefixIcon"><i class="fas fa-hashtag"></i></span>
                                <input type="number" step="0.01" min="0" name="value" id="update_value" class="form-control shadow-none border-secondary-subtle" placeholder="Enter value" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0 pt-3 rounded-bottom-4">
                        <button type="button" class="btn btn-outline-secondary px-4 fw-medium" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-medium" onclick="return confirm('Are you sure you want to apply this update to all salary records? This action cannot be undone.');">
                            <i class="fas fa-check-circle me-1"></i> Apply to All
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Old Data:</h6>
                            <pre id="oldData" class="bg-light p-3" style="max-height: 300px; overflow-y: auto;"></pre>
                        </div>
                        <div class="col-md-6">
                            <h6>New Data:</h6>
                            <pre id="newData" class="bg-light p-3" style="max-height: 300px; overflow-y: auto;"></pre>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to set the addition ID for deletion
        function setDeleteAdditionId(id) {
            document.getElementById('additionId').value = id;
            document.getElementById('delete-addition-form').action = '{{ route('payroll.destroy', '') }}' + '/' + id;
        }

        // Function to set the overtime ID for deletion
        function setDeleteOvertimeId(id) {
            document.getElementById('overtimeId').value = id;
            document.getElementById('delete-overtime-form').action = '{{ route("payovertime.destroy", ":id") }}'.replace(':id', id);
        }

        // Function to set the deduction ID for deletion
        function setDeleteDeductionId(id) {
            document.getElementById('deductionId').value = id;
            document.getElementById('delete-deduction-form').action = '{{ route('deductions.destroy', '') }}' + '/' + id;
        }

        // Row highlighting functionality
        const checkAll = document.getElementById('checkAll');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                rowCheckboxes.forEach(cb => {
                    cb.checked = this.checked;
                    cb.closest('tr').classList.toggle('od-selected', this.checked);
                });
            });
        }

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                this.closest('tr').classList.toggle('od-selected', this.checked);
            });
        });

        const releaseCheckAll = document.getElementById('releaseCheckAll');
        const releaseRowCheckboxes = document.querySelectorAll('.release-row-checkbox');

        if (releaseCheckAll) {
            releaseCheckAll.addEventListener('change', function() {
                releaseRowCheckboxes.forEach(cb => {
                    cb.checked = this.checked;
                    cb.closest('tr').classList.toggle('od-selected', this.checked);
                });
            });
        }

        releaseRowCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                this.closest('tr').classList.toggle('od-selected', this.checked);
            });
        });

        // Auto-refresh page every 30 seconds when generation is in progress
        @if(session('success') && strpos(session('success'), 'Generated') !== false)
            setTimeout(function() {
                location.reload();
            }, 30000);
        @endif

        // Tab handling on page load
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            
            if (tab) {
                const tabElement = document.querySelector(`[href="#tab_${tab}"]`);
                if (tabElement) {
                    tabElement.click();
                }
            }
            
            // Details modal functionality
            const viewDetailLinks = document.querySelectorAll('.view-details');
            const detailsModal = document.getElementById('detailsModal');
            
            viewDetailLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const oldData = JSON.parse(this.getAttribute('data-old-data') || '{}');
                    const newData = JSON.parse(this.getAttribute('data-new-data') || '{}');
                    
                    document.getElementById('oldData').textContent = 
                        JSON.stringify(oldData, null, 2);
                    document.getElementById('newData').textContent = 
                        JSON.stringify(newData, null, 2);
                    
                    // Initialize Bootstrap modal if not already
                    const modal = new bootstrap.Modal(detailsModal);
                    modal.show();
                });
            });
        });
    </script>

    <script>
        function updateValueFormatHint() {
            var select = document.getElementById('update_field');
            var selectedOption = select.options[select.selectedIndex];
            var format = selectedOption.getAttribute('data-format');
            
            var hintElement = document.getElementById('valueFormatHint');
            var iconElement = document.getElementById('valuePrefixIcon');
            
            if (format) {
                hintElement.textContent = format;
                if (format.includes('%')) {
                    iconElement.innerHTML = '<i class="fas fa-percent"></i>';
                } else if (format.includes('₹')) {
                    iconElement.innerHTML = '<i class="fas fa-rupee-sign"></i>';
                } else {
                    iconElement.innerHTML = '<i class="fas fa-hashtag"></i>';
                }
            } else {
                hintElement.textContent = '';
                iconElement.innerHTML = '<i class="fas fa-hashtag"></i>';
            }
        }

        function submitBulkApproval(status) {
            var checkboxes = document.querySelectorAll('.row-checkbox:checked');
            if(checkboxes.length === 0) {
                alert('Please select at least one salary record.');
                return;
            }
            if(confirm('Are you sure you want to mark the selected records as ' + status + '?')) {
                document.getElementById('bulkApprovalStatusInput').value = status;
                document.getElementById('bulkApprovalForm').submit();
            }
        }
    </script>
@endsection
