@extends('layouts.index')

@section('content')


<div class="page-wrapper" style="margin-left: 5px; padding-top: 10px;">
    <div class="content container-fluid" style="max-width: 100%; padding-top: 10px;">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Employee task Report</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Timesheet Report</li>
                    </ul>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div id="successMessage" class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div id="errorMessage" class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row filter-row">
            <div class="col-sm-6 col-md-3">
                <div class="input-block mb-3 form-focus select-focus">
                    <form method="GET" action="{{ route('timesheet-reports.index') }}">
                        <select name="employee_id" class="select floating">
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->firstname }} {{ $emp->lastname }}
                                </option>
                            @endforeach
                        </select>
                        <label class="focus-label">Employee</label>
                </div>
            </div>

            <div class="col-sm-6 col-md-3">
                <div class="input-block mb-3 form-focus select-focus">
                    <select name="project_id" class="select floating">
                        <option value="">Select Project</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->projectid }}" {{ request('project_id') == $p->projectid ? 'selected' : '' }}>
                                {{ $p->projectname }}
                            </option>
                        @endforeach
                    </select>
                    <label class="focus-label">Project</label>
                </div>
            </div>

            <div class="col-sm-6 col-md-3">
                <div class="input-block mb-3 form-focus">
                    <div class="cal-icon">
                        <input name="from" value="{{ request('from') }}" class="form-control floating datetimepicker" type="text" placeholder="DD-MM-YYYY">
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-3">
                <div class="input-block mb-3 form-focus">
                    <div class="cal-icon">
                        <input name="to" value="{{ request('to') }}" class="form-control floating datetimepicker" type="text" placeholder="DD-MM-YYYY">
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-9">
                <div class="input-block mb-3 form-focus">
                    <input name="keyword" value="{{ request('keyword') }}" class="form-control floating" type="text" placeholder="Search description, employee, project">
                </div>
            </div>

            <div class="col-sm-6 col-md-3">
                <button type="submit" class="btn btn-success w-100">Search</button>
                </form>
            </div>
        </div>
         
        <div class="row mb-3">
            <div class="col-md-12 text-end">
                <a href="{{ route('timesheet-reports.export.csv', request()->query()) }}" class="btn btn-primary">
                    <i class="fa fa-file-excel"></i> Export to CSV
                </a>
                <a href="{{ route('timesheet-reports.export.pdf', request()->query()) }}" class="btn btn-danger">
                    <i class="fa fa-file-pdf"></i> Export to PDF
                </a>
            </div>
        </div>
         

        <div class="row">
            <div class="col-md-12">
    <table class="table  custom-table mb-0 datatable" id="timesheetTable">
        <thead>
            <tr>
               
                <th>Employee</th>
                <th>Designation</th>
                <th>Project</th>
                <th>Assigned Date</th>
                <th>Assigned Hours</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @forelse($timesheets as $t)
                <tr>
                  
                    <td data-label="Employee Name">
                        <span class="high">{{ ($t->firstname ?? '') . ' ' . ($t->lastname ?? '') }}</span>
                    </td>
                    
                    <td data-label="Designation">
                        {{ $t->designation ?? 'N/A' }}
                    </td>
                    
                    <td data-label="Project Name">
                        <span class="od-chip-highlight">{{ $t->projectname ?? 'N/A' }}</span>
                    </td>
                    
                    <td data-label="Assigned Date">
                        {{ $t->assigned_date ? \Carbon\Carbon::parse($t->assigned_date)->format('d-m-Y') : '' }}
                    </td>
                    
                    <td data-label="Assigned Hours">
                        {{ $t->assigned_hours }}
                    </td>
                    
                    <td data-label="Description">
                        {{ \Illuminate\Support\Str::limit($t->description, 80) }}
                    </td>
                    
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No timesheet entries found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    // Select all / row highlight
    $('#checkAllTimesheet').on('change', function() {
        var checked = $(this).is(':checked');
        $('.row-check').prop('checked', checked).trigger('change');
    });

    $('.row-check').on('change', function() {
        $(this).closest('tr').toggleClass('od-selected', $(this).is(':checked'));
    });
});
</script>

                @if($timesheets->isNotEmpty())
                    <div class="mt-2 small text-muted">Total rows: {{ $timesheets->count() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Fade out flash messages
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            var successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.style.transition = 'opacity 1s ease';
                successMessage.style.opacity = '0';
                setTimeout(function() { successMessage.remove(); }, 1000);
            }
            var errorMessage = document.getElementById('errorMessage');
            if (errorMessage) {
                errorMessage.style.transition = 'opacity 1s ease';
                errorMessage.style.opacity = '0';
                setTimeout(function() { errorMessage.remove(); }, 1000);
            }
        }, 2000);
    });
</script>
@endsection