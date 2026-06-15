@extends('layouts.index')
@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-12">
            <div class="d-flex align-items-center">
                <h5 class="page-title">Dashboard</h5>
                
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fas fa-calendar-day mr-2"></i>Daily Attendance - {{ $formattedDate }}
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.attendance.daily') }}" method="GET">
                    <div class="row filter-row">
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group">
                                <label class="form-label">Date</label>
                                <div class="cal-icon">
                                    <input type="date" class="form-control datetimepicker" name="date" value="{{ $date }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group">
                                <label class="form-label">Employee</label>
                                <select class="form-control select2" name="employee_id">
                                    <option value="">All Employees</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ $selectedEmployee == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->firstname }} {{ $employee->lastname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary btn-block"> 
                                    <i class="fas fa-search mr-1"></i> Search 
                                </button>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group mt-4">
                                <a href="{{ route('admin.attendance.daily') }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-sync-alt mr-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-striped custom-table datatable">
                        <thead class="bg-light">
                            <tr>
                                <th style="min-width: 200px;">Employee</th>
                                <th style="min-width: 150px;">Date</th>
                                <th>Punch In</th>
                                <th>Punch Out</th>
                                <th>Duration</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendance as $record)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm mr-3">
                                                <span class="avatar-title rounded-circle ">
                                                    {{ substr($record->firstname, 0, 1) }}{{ substr($record->lastname, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $record->firstname }} {{ $record->lastname }}</h6>
                                             
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ date('M j, Y', strtotime($record->date)) }}</td>
                                    <td>
                                        @if($record->punch_in)
                                            <span class="text-success font-weight-bold">
                                                <i class="far fa-clock mr-1"></i>{{ date('h:i A', strtotime($record->punch_in)) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->punch_out)
                                            <span class="text-danger font-weight-bold">
                                                <i class="far fa-clock mr-1"></i>{{ date('h:i A', strtotime($record->punch_out)) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->punch_in && $record->punch_out)
                                            @php
                                                $start = \Carbon\Carbon::parse($record->punch_in);
                                                $end = \Carbon\Carbon::parse($record->punch_out);
                                                $duration = $start->diff($end);
                                            @endphp
                                            <span class="text-info font-weight-bold">
                                                {{ $duration->format('%h h %i m') }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$record->punch_in)
                                            <span class="badge badge-pill bg-danger-light">Absent</span>
                                        @elseif(!$record->punch_out)
                                            <span class="badge badge-pill bg-warning-light">Working</span>
                                        @else
                                            <span class="badge badge-pill bg-success-light">Present</span>
                                        @endif
                                    </td>
                                    
                                   
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="empty-table">
                                            <i class="far fa-calendar-times fa-3x text-muted mb-3"></i>
                                            <p class="mb-0">No attendance records found</p>
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





<style>
    .empty-table {
        padding: 20px 0;
        text-align: center;
    }
    .avatar-sm {
        width: 40px;
        height: 40px;
        font-size: 14px;
    }
    .table-avatar {
        display: flex;
        align-items: center;
    }
    .datatable th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 13px;
    }
    .badge-pill {
        padding: 5px 10px;
        border-radius: 50px;
    }
    .bg-success-light {
        background-color: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }
    .bg-danger-light {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }
</style>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Select Employee',
            allowClear: true
        });

        $('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD',
            icons: {
                up: "fas fa-chevron-up",
                down: "fas fa-chevron-down",
                next: 'fas fa-chevron-right',
                previous: 'fas fa-chevron-left'
            }
        });

        $('.timepicker').datetimepicker({
            format: 'LT',
            icons: {
                up: "fas fa-chevron-up",
                down: "fas fa-chevron-down",
                next: 'fas fa-chevron-right',
                previous: 'fas fa-chevron-left'
            }
        });
    });
</script>

@endsection