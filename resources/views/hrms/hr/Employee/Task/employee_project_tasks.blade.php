@extends('layouts.index')

@section('content')
<div class="content container-fluid">
 
        <div class="page-header mt-5">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Employee Tasks</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item active">Employee Tasks</li>
                    </ul>
                </div>
            </div>
        </div>
    
        <!-- 🔍 Filter Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('employee.project.tasks') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Employee</label>
                            <select name="employee_id" class="form-select">
                                <option value="">All Employees</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->firstname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
    
                        <div class="col-md-3">
                            <label class="form-label">Project</label>
                            <select name="project_id" class="form-select">
                                <option value="">All Projects</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->projectname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
    
                        <div class="col-md-2">
                            <label class="form-label">From Date</label>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
    
                        <div class="col-md-2">
                            <label class="form-label">To Date</label>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
    
                        <div class="col-md-2 text-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa fa-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    

    <div class="row">
        <div class="col-md-12">

            <div class="table-responsive">
                <table class="table custom-table datatable mb-0 align-middle" id="employeeTaskTable">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Employee Name</th>
                            <th>Project Name</th>
                            <th>Task Name</th>
                            <th>Description</th>
                            <th>Due Date</th>
                            <th>Start Date</th>
                            <th>Start Time</th>
                            <th>End Date</th>
                            <th>End Time</th>
                            <th>Status</th>
                            <th>Breaks Taken</th>
                            <th>Total Break Time</th>
                            <th>Total Worked Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($taskReports as $index => $report)
                            <tr>
                                <td data-label="S.No">{{ $index + 1 }}</td>
                                <td data-label="Employee Name">
                                    <span class="od-chip-highlight">{{ $report->employee_name }}</span>
                                </td>
                                <td data-label="Project Name">{{ $report->projectname }}</td>
                                <td data-label="Task Name">{{ $report->task_name }}</td>
                                <td data-label="Description">{{ $report->task_description }}</td>

                                <td data-label="Due Date">
                                    {{ \Carbon\Carbon::parse($report->due_date)->format('d-m-Y') }}
                                </td>

                                <td class="start-date" data-label="Start Date">
                                    {{ $report->start_date ? \Carbon\Carbon::parse($report->start_date)->format('Y-m-d') : '' }}
                                </td>
                                <td class="start-time" data-label="Start Time">
                                    {{ $report->start_time ? \Carbon\Carbon::parse($report->start_time)->format('H:i') : '' }}
                                </td>

                                <td class="end-date" data-label="End Date">
                                    {{ $report->end_date ? \Carbon\Carbon::parse($report->end_date)->format('Y-m-d') : '' }}
                                </td>
                                <td class="end-time" data-label="End Time">
                                    {{ $report->end_time ? \Carbon\Carbon::parse($report->end_time)->format('H:i') : '' }}
                                </td>

                                <td data-label="Status">
                                    <span class="badge 
                                        @if($report->status == 'Completed') bg-success 
                                        @elseif($report->status == 'In Progress') bg-warning 
                                        @else bg-secondary @endif">
                                        {{ $report->status }}
                                    </span>
                                </td>

                                <td data-label="Breaks Taken">
    @if($report->break_details && $report->break_details !== 'No Breaks')
        @php
            // Convert break details to readable format with Indian time
            $breakDetails = explode(', ', $report->break_details);
            $formattedBreaks = [];
            
            foreach ($breakDetails as $break) {
                if (preg_match('/Start: (.*) - End: (.*)/', $break, $matches)) {
                    $startTime = \Carbon\Carbon::parse($matches[1])->format('h:i A');
                    $endTime = \Carbon\Carbon::parse($matches[2])->format('h:i A');
                    $formattedBreaks[] = "{$startTime} - {$endTime}";
                }
            }
            
            $breakCount = count($formattedBreaks);
        @endphp
        
        <div class="break-container">
            <span class="break-count badge bg-info">{{ $breakCount }} break{{ $breakCount > 1 ? 's' : '' }}</span>
            
            @if($breakCount > 0)
                <div class="break-details mt-1">
                    @foreach($formattedBreaks as $index => $formattedBreak)
                        <small class="d-block text-muted">
                            <i class="fas fa-coffee me-1"></i>
                            Break {{ $index + 1 }}: {{ $formattedBreak }}
                        </small>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <span class="text-muted">No breaks taken</span>
    @endif
</td>
                                <td class="break-time" data-label="Total Break Time">
                                    {{ $report->total_break_time ?: '-' }}
                                </td>
                                <td class="total-work-time" data-label="Total Worked Hours">Calculating...</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-center text-muted">No tasks found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Auto-hide alerts -->
<script>
    setTimeout(() => {
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');
        if (successMessage) successMessage.style.display = 'none';
        if (errorMessage) errorMessage.style.display = 'none';
    }, 5000);

    // ✅ Calculate Total Worked Hours (subtracting break time)
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('#employeeTaskTable tbody tr');

        rows.forEach(row => {
            const startDate = row.querySelector('.start-date')?.innerText.trim();
            const startTime = row.querySelector('.start-time')?.innerText.trim();
            const endDate = row.querySelector('.end-date')?.innerText.trim();
            const endTime = row.querySelector('.end-time')?.innerText.trim();
            const breakText = row.querySelector('.break-time')?.innerText.trim();
            const totalCell = row.querySelector('.total-work-time');

            if (startDate && startTime && endDate && endTime) {
                const start = new Date(`${startDate}T${startTime}`);
                const end = new Date(`${endDate}T${endTime}`);

                if (!isNaN(start) && !isNaN(end) && end > start) {
                    let diffMs = end - start; // Total time in milliseconds

                    // ⏱ Parse break time (supports "10 mins", "1 hr 15 mins", "00:10:00" formats)
                    let breakMinutes = 0;
                    if (breakText && breakText !== '-' && breakText.toLowerCase() !== 'no breaks') {
                        const regex = /(\d+)\s*hr/i;
                        const regexMin = /(\d+)\s*min/i;
                        const timeFormat = /^(\d{1,2}):(\d{2})(?::(\d{2}))?$/;

                        if (timeFormat.test(breakText)) {
                            const [, h, m, s] = breakText.match(timeFormat);
                            breakMinutes = (parseInt(h || 0) * 60) + parseInt(m || 0) + ((parseInt(s || 0)) / 60);
                        } else {
                            const hrs = regex.exec(breakText);
                            const mins = regexMin.exec(breakText);
                            if (hrs) breakMinutes += parseInt(hrs[1]) * 60;
                            if (mins) breakMinutes += parseInt(mins[1]);
                        }
                    }

                    // Subtract break time
                    diffMs -= breakMinutes * 60 * 1000;

                    // Convert to hours and minutes
                    if (diffMs > 0) {
                        const diffHrs = Math.floor(diffMs / (1000 * 60 * 60));
                        const diffMins = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
                        totalCell.textContent = `${diffHrs} hrs ${diffMins} mins`;
                    } else {
                        totalCell.textContent = '0 mins';
                    }
                } else {
                    totalCell.textContent = '-';
                }
            } else {
                totalCell.textContent = '-';
            }
        });
    });
</script>
@endsection
