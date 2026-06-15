@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Timesheet</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('timesheet.index') }}">Timesheets</a></li>
                                <li class="breadcrumb-item active">Edit</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Week Selection (Read-only) -->
        <div class="row mb-4">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-inline">
                            <label class="mr-2">Select Week:</label>
                            <input type="text" class="form-control mr-2" 
                                   value="{{ Carbon\Carbon::parse($weekStart)->format('Y-\WW') }}" 
                                   readonly 
                                   style="background-color: #f5f5f5;">
                            <span class="ml-2">
                                {{ Carbon\Carbon::parse($weekStart)->format('M d, Y') }} - 
                                {{ Carbon\Carbon::parse($weekEnd)->format('M d, Y') }}
                            </span>
                        </div>
                        <small class="text-muted">Week cannot be changed when editing</small>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('timesheet.update', $timesheet->id) }}" id="timesheetForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="week_start" value="{{ $weekStart }}">
            
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Enter Hours</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Project</th>
                                            @foreach($weekDays as $day)
                                                <th class="text-center">
                                                    {{ $day['day'] }}<br>
                                                    <small>{{ Carbon\Carbon::parse($day['date'])->format('M d') }}</small>
                                                    @if($day['is_weekend'])
                                                        <br><span class="badge badge-warning">Weekend</span>
                                                    @endif
                                                    @if($day['is_holiday'])
                                                        <br><span class="badge badge-success">Holiday</span>
                                                    @endif
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($projects as $project)
                                        <tr>
                                            <td>
                                                <strong>{{ $project->projectname ?? $project->name }}</strong><br>
                                                <small>{{ $project->projectid ?? $project->code }}</small>
                                            </td>
                                            @foreach($weekDays as $day)
                                                <td>
                                                    @php
                                                        // Get the stored hours for this project on this day
                                                        $storedHours = 0;
                                                        if (isset($dailyEntries[$project->id][$day['date']])) {
                                                            $storedHours = $dailyEntries[$project->id][$day['date']];
                                                        }
                                                    @endphp
                                                    
                                                    @if($day['is_weekend'] || $day['is_holiday'])
                                                        <input type="number" 
                                                               name="entries[{{ $day['date'] }}][{{ $project->id }}]"
                                                               class="form-control"
                                                               min="0" max="24" step="0.5"
                                                               value="0"
                                                               readonly
                                                               disabled
                                                               style="background-color: #f5f5f5;">
                                                        <input type="hidden" 
                                                               name="entries[{{ $day['date'] }}][{{ $project->id }}]" 
                                                               value="0">
                                                    @else
                                                        <input type="number" 
                                                               name="entries[{{ $day['date'] }}][{{ $project->id }}]"
                                                               class="form-control"
                                                               min="0" max="24" step="0.5"
                                                               value="{{ $storedHours }}">
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Summary Note -->
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <strong>Note:</strong> Hours entered for each day will be summed up per project for the week.
                                    </div>
                                </div>
                            </div>

                            <!-- Comments -->
                            <div class="form-group mt-3">
                                <label>Comments:</label>
                                <textarea name="comments" class="form-control" rows="2" 
                                          placeholder="Add comments if any">{{ $timesheet->comments }}</textarea>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Timesheet
                                </button>
                                <a href="{{ route('timesheet.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>




@endsection