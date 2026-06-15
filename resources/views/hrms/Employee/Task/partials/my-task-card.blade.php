@php
    $assignedName = trim(($task->emp_first ?? '') . ' ' . ($task->emp_last ?? ''));
    $due = $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M, Y') : '-';

    $taskTimesheets = $timesheetsByTask[$task->id] ?? [];
    $activeTs = null;
    $completedTimesheets = [];

    foreach ($taskTimesheets as $ts) {
        if ($ts->end_time === null) {
            $activeTs = $ts;
        } else {
            $completedTimesheets[] = $ts;
        }
    }

    $totalWorkSeconds = 0;
    $totalBreakSeconds = 0;
    $totalAttempts = count($completedTimesheets);

    foreach ($completedTimesheets as $ts) {
        $tsBreaks = $breaks[$ts->id] ?? [];
        $tsBreakSeconds = collect($tsBreaks)->sum('break_duration_seconds');
        $totalBreakSeconds += $tsBreakSeconds;

        $start = safeParseDateTime($ts->start_date, $ts->start_time);
        $end = safeParseDateTime($ts->end_date, $ts->end_time);

        if ($start && $end) {
            $workSeconds = $end->diffInSeconds($start) - $tsBreakSeconds;
            $totalWorkSeconds += max(0, $workSeconds);
        }
    }

    $totalWorkTime = formatSeconds($totalWorkSeconds);
    $totalBreakTime = formatSeconds($totalBreakSeconds);
    $latestTs = !empty($completedTimesheets) ? end($completedTimesheets) : null;

    $displayStartDate = $activeTs ? cleanDateValue($activeTs->start_date) : ($latestTs ? cleanDateValue($latestTs->start_date) : null);
    $displayStartTime = $activeTs ? cleanTimeValue($activeTs->start_time) : ($latestTs ? cleanTimeValue($latestTs->start_time) : null);
    $displayEndDate = $activeTs ? null : ($latestTs ? cleanDateValue($latestTs->end_date) : null);
    $displayEndTime = $activeTs ? null : ($latestTs ? cleanTimeValue($latestTs->end_time) : null);

    $latestWorkTime = '';
    $latestBreakSeconds = 0;

    if ($latestTs) {
        $latestBreaks = $breaks[$latestTs->id] ?? [];
        $latestBreakSeconds = collect($latestBreaks)->sum('break_duration_seconds');
        $latestStart = safeParseDateTime($latestTs->start_date, $latestTs->start_time);
        $latestEnd = safeParseDateTime($latestTs->end_date, $latestTs->end_time);
        $latestWorkTime = ($latestStart && $latestEnd)
            ? formatSeconds(max(0, $latestEnd->diffInSeconds($latestStart) - $latestBreakSeconds))
            : 'Invalid date/time';
    }

    $formatDisplayTime = function ($time) {
        if (!$time) {
            return '-';
        }

        $timeParts = explode(':', $time);
        if (count($timeParts) < 2) {
            return $time;
        }

        $hour = (int) $timeParts[0];
        $minute = (int) $timeParts[1];
        $period = $hour >= 12 ? 'PM' : 'AM';
        $hour12 = $hour % 12;
        $hour12 = $hour12 ?: 12;

        return sprintf('%02d:%02d %s', $hour12, $minute, $period);
    };

    $plainDescription = trim(strip_tags($task->description ?? ''));
    $descriptionPreview = $plainDescription !== ''
        ? \Illuminate\Support\Str::limit($plainDescription, 120, '...')
        : 'No description added.';
@endphp

<div class="col-12 col-md-6 col-xl-4">
    <div class="card h-100 my-task-card {{ $activeTs ? 'is-active' : '' }}">
        <div class="card-body d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                <span class="priority-pill priority-{{ $task->priority ?? 'pending' }}">{{ ucfirst($task->priority ?? 'Pending') }}</span>
                <span class="status-dot status-{{ $task->status ?? 'pending' }}">{{ ucfirst(str_replace('_', ' ', $task->status ?? 'pending')) }}</span>
            </div>

            <h5 class="card-title fw-semibold mb-3">{{ $task->task }}</h5>

            <div class="task-meta-grid">
                <div>
                    <span>Assigned to</span>
                    <strong>{{ $assignedName ?: '-' }}</strong>
                </div>
                <div>
                    <span>Due date</span>
                    <strong>{{ $due }}</strong>
                </div>
            </div>

            @if($totalAttempts > 0)
                <div class="session-summary">
                    <p class="fw-semibold mb-1">Total working time</p>
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <span class="time-pill">{{ $totalWorkTime }}</span>
                        @if($totalBreakSeconds > 0)
                            <span class="text-muted small">Breaks: {{ $totalBreakTime }}</span>
                        @endif
                    </div>
                    <small class="text-muted d-block mt-2">Attempts: {{ $totalAttempts }}</small>
                </div>
            @endif

            @if($activeTs || $latestTs)
                <div class="session-panel">
                    <h6 class="fw-semibold mb-2">{{ $activeTs ? 'Current Session' : 'Latest Session' }}</h6>

                    @if($displayStartDate)
                        <div class="session-grid">
                            <div>
                                <span>Start date</span>
                                <strong>
                                    @php
                                        try {
                                            echo \Carbon\Carbon::parse($displayStartDate)->format('d M, Y');
                                        } catch (\Exception $e) {
                                            echo e($displayStartDate);
                                        }
                                    @endphp
                                </strong>
                            </div>
                            <div>
                                <span>Start time</span>
                                <strong>{{ $formatDisplayTime($displayStartTime) }}</strong>
                            </div>
                        </div>
                    @endif

                    @if($displayEndDate && $displayEndTime)
                        <div class="session-grid mt-2">
                            <div>
                                <span>Completed date</span>
                                <strong>
                                    @php
                                        try {
                                            echo \Carbon\Carbon::parse($displayEndDate)->format('d M, Y');
                                        } catch (\Exception $e) {
                                            echo e($displayEndDate);
                                        }
                                    @endphp
                                </strong>
                            </div>
                            <div>
                                <span>Completed time</span>
                                <strong>{{ $formatDisplayTime($displayEndTime) }}</strong>
                            </div>
                        </div>

                        @if($latestWorkTime)
                            <div class="latest-session-line">
                                <span>This session</span>
                                <strong>{{ $latestWorkTime }}</strong>
                            </div>
                            @if($latestBreakSeconds > 0)
                                <small class="text-muted">Breaks in this session: {{ formatSeconds($latestBreakSeconds) }}</small>
                            @endif
                        @endif
                    @endif
                </div>
            @endif

            <button type="button"
                    class="task-description-preview"
                    data-title="{{ e($task->task) }}"
                    data-project="{{ e($task->projectname ?? '-') }}"
                    data-assigned="{{ e($assignedName ?: '-') }}"
                    data-due="{{ e($due) }}"
                    data-priority="{{ e(ucfirst($task->priority ?? 'Pending')) }}"
                    data-status="{{ e(ucfirst(str_replace('_', ' ', $task->status ?? 'pending'))) }}">
                <span>Description</span>
                <strong>{{ $descriptionPreview }}</strong>
                <small>View full details</small>
            </button>
            <div class="task-full-description d-none">{{ $plainDescription !== '' ? $plainDescription : 'No description added.' }}</div>

            <div class="mt-auto d-flex justify-content-end gap-2 task-actions">
                @if(!$activeTs)
                    <form action="{{ route('timesheets.start') }}" method="POST">
                        @csrf
                        <input type="hidden" name="task_id" value="{{ $task->id }}">
                        <button type="submit" class="btn start-task-btn btn-sm">
                            {{ $totalAttempts > 0 ? 'Resume Task' : 'Start Now' }}
                        </button>
                    </form>
                @else
                    <button type="button"
                            class="btn break-start-btn btn-sm break-btn"
                            data-timesheet-id="{{ $activeTs->id }}"
                            data-action="start">
                        Start Break
                    </button>

                    <button type="button"
                            class="btn break-end-btn btn-sm break-btn d-none"
                            data-timesheet-id="{{ $activeTs->id }}"
                            data-action="end">
                        End Break
                    </button>

                    <form action="{{ route('timesheets.complete') }}" method="POST">
                        @csrf
                        <input type="hidden" name="timesheet_id" value="{{ $activeTs->id }}">
                        <button type="submit" class="btn complete-task-btn btn-sm">Complete Now</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
