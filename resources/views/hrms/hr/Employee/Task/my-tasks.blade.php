@php
    use Carbon\Carbon;
    
    // Helper functions to clean date/time values
    function cleanDateValue($dateValue) {
        if (!$dateValue) {
            return null;
        }
        
        // If it's already a Carbon instance, format it
        if ($dateValue instanceof Carbon) {
            return $dateValue->toDateString();
        }
        
        // Remove everything after the space (including microseconds)
        if (strpos($dateValue, ' ') !== false) {
            $dateValue = explode(' ', $dateValue)[0];
        }
        
        // Remove microseconds if present in date string
        if (strpos($dateValue, '.') !== false) {
            $dateValue = explode('.', $dateValue)[0];
        }
        
        return $dateValue;
    }
    
    function cleanTimeValue($timeValue) {
        if (!$timeValue) {
            return null;
        }
        
        // If it's already a Carbon instance, format it
        if ($timeValue instanceof Carbon) {
            return $timeValue->format('H:i:s');
        }
        
        // If time contains microseconds, remove them
        if (strpos($timeValue, '.') !== false) {
            $timeValue = explode('.', $timeValue)[0];
        }
        
        // Extract just the time part (HH:MM:SS)
        if (strlen($timeValue) > 8) {
            $timeValue = substr($timeValue, 0, 8);
        }
        
        return $timeValue;
    }
    
    function formatSeconds($seconds) {
        if ($seconds <= 0) return '0s';
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        $result = '';
        if ($hours > 0) $result .= $hours . 'h ';
        if ($minutes > 0) $result .= $minutes . 'm ';
        if ($secs > 0 || ($hours === 0 && $minutes === 0)) $result .= $secs . 's';
        
        return trim($result);
    }
    
    function safeParseDateTime($date, $time) {
        if (!$date || !$time) {
            return null;
        }
        
        try {
            // Clean both values first
            $cleanDate = cleanDateValue($date);
            $cleanTime = cleanTimeValue($time);
            
            // If cleanTime is empty or invalid, use a default
            if (!$cleanTime || !preg_match('/^\d{1,2}:\d{1,2}(:\d{1,2})?$/', $cleanTime)) {
                $cleanTime = '00:00:00';
            }
            
            return Carbon::parse($cleanDate . ' ' . $cleanTime);
        } catch (\Exception $e) {
            // Try alternative parsing
            try {
                // Sometimes the date might contain time too
                if (strpos($date, ' ') !== false) {
                    return Carbon::parse($date);
                }
                
                // If all else fails, return null
                return null;
            } catch (\Exception $e2) {
                return null;
            }
        }
    }
@endphp

<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">My Tasks</h4>
        <button id="openMyTaskFilter" class="filter-square-btn">
            <i class="fa-solid fa-filter"></i>
        </button>
    </div>
    
    <!-- =============== RIGHT FILTER PANEL =============== -->
    <div id="myTaskFilterPanel" class="task-filter-panel">
        <div class="filter-header">
            <h5 class="mb-0">Filter Tasks</h5>
            <button id="closeMyTaskFilter" class="close-btn">&times;</button>
        </div>
    
        <form method="GET" class="p-3">
            <label class="fw-bold">Project</label>
            <select name="project" class="form-control mb-3">
                <option value="">All</option>
                @foreach($tasks as $t)
                    @if($t->project_db_id)
                    <option value="{{ $t->projects }}"
                        {{ request('project')==$t->projects?'selected':'' }}>
                        {{ $t->projectname }}
                    </option>
                    @endif
                @endforeach
            </select>
    
            <label class="fw-bold">Priority</label>
            <select name="priority" class="form-control mb-3">
                <option value="">All</option>
                <option value="low" {{ request('priority')=='low'?'selected':'' }}>Low</option>
                <option value="medium" {{ request('priority')=='medium'?'selected':'' }}>Medium</option>
                <option value="high" {{ request('priority')=='high'?'selected':'' }}>High</option>
                <option value="urgent" {{ request('priority')=='urgent'?'selected':'' }}>Urgent</option>
            </select>
    
            <label class="fw-bold">Status</label>
            <select name="status" class="form-control mb-3">
                <option value="">Pending (Default)</option>
                <option value="in_progress" {{ request('status')=='in_progress'?'selected':'' }}>In Progress</option>
                <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
            </select>
    
            <button class="btn btn-primary w-100 mt-2">Apply Filters</button>
        </form>
    </div>
    
    {{-- Card layout for tasks --}}
    <div class="row g-3">
        @foreach($tasks as $task)
            @php
                $assignedName = trim(($task->emp_first ?? '') . ' ' . ($task->emp_last ?? ''));
                $due = $task->due_date ? Carbon::parse($task->due_date)->format('d M, Y') : '-';
                
                // Get all timesheets for this task
                $taskTimesheets = $timesheetsByTask[$task->id] ?? [];
                
                // Separate active and completed timesheets
                $activeTs = null;
                $completedTimesheets = [];
                
                foreach ($taskTimesheets as $ts) {
                    if ($ts->end_time === null) {
                        $activeTs = $ts;
                    } else {
                        $completedTimesheets[] = $ts;
                    }
                }
                
                // Calculate total working time and breaks across all timesheets
                $totalWorkSeconds = 0;
                $totalBreakSeconds = 0;
                $totalAttempts = count($completedTimesheets);
                
                foreach ($completedTimesheets as $ts) {
                    // Get breaks for this timesheet
                    $tsBreaks = $breaks[$ts->id] ?? [];
                    $tsBreakSeconds = collect($tsBreaks)->sum('break_duration_seconds');
                    $totalBreakSeconds += $tsBreakSeconds;
                    
                    // Calculate work time for this timesheet using safe parsing
                    $start = safeParseDateTime($ts->start_date, $ts->start_time);
                    $end = safeParseDateTime($ts->end_date, $ts->end_time);
                    
                    if ($start && $end) {
                        $workSeconds = $end->diffInSeconds($start) - $tsBreakSeconds;
                        $totalWorkSeconds += max(0, $workSeconds);
                    }
                }
                
                // Format totals
                $totalWorkTime = formatSeconds($totalWorkSeconds);
                $totalBreakTime = formatSeconds($totalBreakSeconds);
                
                // Get latest completed timesheet for display
                $latestTs = !empty($completedTimesheets) ? end($completedTimesheets) : null;
                
                // Clean dates and times for display
                $displayStartDate = $activeTs ? cleanDateValue($activeTs->start_date) : ($latestTs ? cleanDateValue($latestTs->start_date) : null);
                $displayStartTime = $activeTs ? cleanTimeValue($activeTs->start_time) : ($latestTs ? cleanTimeValue($latestTs->start_time) : null);
                $displayEndDate = $activeTs ? null : ($latestTs ? cleanDateValue($latestTs->end_date) : null);
                $displayEndTime = $activeTs ? null : ($latestTs ? cleanTimeValue($latestTs->end_time) : null);
                
                // Calculate latest session time
                $latestWorkTime = '';
                $latestBreakSeconds = 0;
                
                if ($latestTs) {
                    $latestBreaks = $breaks[$latestTs->id] ?? [];
                    $latestBreakSeconds = collect($latestBreaks)->sum('break_duration_seconds');
                    
                    $latestStart = safeParseDateTime($latestTs->start_date, $latestTs->start_time);
                    $latestEnd = safeParseDateTime($latestTs->end_date, $latestTs->end_time);
                    
                    if ($latestStart && $latestEnd) {
                        $workSeconds = $latestEnd->diffInSeconds($latestStart) - $latestBreakSeconds;
                        $latestWorkTime = formatSeconds(max(0, $workSeconds));
                    } else {
                        $latestWorkTime = 'Invalid date/time';
                    }
                }
            @endphp

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            <span class="badge bg-secondary text-uppercase">{{ $task->priority ?? 'pending' }}</span>
                        </div>

                        <h5 class="card-title fw-semibold mb-2">{{ $task->task }}</h5>

                        <p class="text-muted mb-2"><strong>Assign To Employee:</strong> {{ $assignedName ?: '—' }}</p>
                        <p class="text-muted mb-2"><strong>Task Priority:</strong> {{ ucfirst($task->priority ?? '—') }}</p>
                        <p class="text-muted mb-2"><strong>Due Date:</strong> {{ $due }}</p>

                        {{-- Show total working time across all attempts --}}
                        @if($totalAttempts > 0)
                            <div class="mb-3 p-3 bg-light rounded">
                                <p class="fw-semibold mb-1">Total Working Time (All Attempts):</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-success">{{ $totalWorkTime }}</span>
                                    @if($totalBreakSeconds > 0)
                                        <span class="text-muted small">Breaks: {{ $totalBreakTime }}</span>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">Number of attempts: {{ $totalAttempts }}</small>
                                </div>
                            </div>
                        @endif

                        {{-- Latest/Current Session --}}
                        @if($activeTs || $latestTs)
                            <div class="mb-3">
                                <h6 class="fw-semibold mb-2">
                                    @if($activeTs)
                                        Current Session
                                    @else
                                        Latest Session
                                    @endif
                                </h6>
                                
                                @if($displayStartDate)
                                <div class="row mb-2">
                                    <div class="col-6">
                                        <p class="text-muted mb-1"><strong>Start Date:</strong></p>
                                        <div>
                                            @php
                                                try {
                                                    echo Carbon::parse($displayStartDate)->format('d M, Y');
                                                } catch (\Exception $e) {
                                                    echo $displayStartDate;
                                                }
                                            @endphp
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted mb-1"><strong>Start Time:</strong></p>
                                        <div>
                                            @if($displayStartTime)
                                                @php
                                                    try {
                                                        // Try to parse and format the time
                                                        $timeParts = explode(':', $displayStartTime);
                                                        if (count($timeParts) >= 2) {
                                                            $hour = (int)$timeParts[0];
                                                            $minute = (int)$timeParts[1];
                                                            $period = $hour >= 12 ? 'PM' : 'AM';
                                                            $hour12 = $hour % 12;
                                                            $hour12 = $hour12 ? $hour12 : 12;
                                                            echo sprintf('%02d:%02d %s', $hour12, $minute, $period);
                                                        } else {
                                                            echo $displayStartTime;
                                                        }
                                                    } catch (\Exception $e) {
                                                        echo $displayStartTime;
                                                    }
                                                @endphp
                                            @else
                                                —
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($displayEndDate && $displayEndTime)
                                    <div class="row mb-2">
                                        <div class="col-6">
                                            <p class="text-muted mb-1"><strong>Completed Date:</strong></p>
                                            <div>
                                                @php
                                                    try {
                                                        echo Carbon::parse($displayEndDate)->format('d M, Y');
                                                    } catch (\Exception $e) {
                                                        echo $displayEndDate;
                                                    }
                                                @endphp
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-muted mb-1"><strong>Completed Time:</strong></p>
                                            <div>
                                                @if($displayEndTime)
                                                    @php
                                                        try {
                                                            $timeParts = explode(':', $displayEndTime);
                                                            if (count($timeParts) >= 2) {
                                                                $hour = (int)$timeParts[0];
                                                                $minute = (int)$timeParts[1];
                                                                $period = $hour >= 12 ? 'PM' : 'AM';
                                                                $hour12 = $hour % 12;
                                                                $hour12 = $hour12 ? $hour12 : 12;
                                                                echo sprintf('%02d:%02d %s', $hour12, $minute, $period);
                                                            } else {
                                                                echo $displayEndTime;
                                                            }
                                                        } catch (\Exception $e) {
                                                            echo $displayEndTime;
                                                        }
                                                    @endphp
                                                @else
                                                    —
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($latestWorkTime)
                                        <p class="text-muted mb-1">
                                            <strong>This Session Time:</strong> {{ $latestWorkTime }}
                                        </p>
                                        
                                        @if($latestBreakSeconds > 0)
                                            <p class="text-muted mb-1 small">
                                                <strong>Breaks in this session:</strong> {{ formatSeconds($latestBreakSeconds) }}
                                            </p>
                                        @endif
                                    @endif
                                @endif
                            </div>
                        @endif

                        <p class="card-text mt-2" style="white-space: pre-line;">
                            {{ \Illuminate\Support\Str::limit(strip_tags($task->description ?? ''), 300, '...') }}
                        </p>

                        <div class="mt-auto d-flex justify-content-end gap-2">
                            @if(!$activeTs)
                                <form action="{{ route('timesheets.start') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="task_id" value="{{ $task->id }}">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        @if($totalAttempts > 0)
                                            Resume Task
                                        @else
                                            Start Now
                                        @endif
                                    </button>
                                </form>
                            @else
                                {{-- Break Management Buttons --}}
                                <button type="button" 
                                        class="btn btn-warning btn-sm break-btn" 
                                        data-timesheet-id="{{ $activeTs->id }}"
                                        data-action="start">
                                    Start Break
                                </button>
                                
                                <button type="button" 
                                        class="btn btn-info btn-sm break-btn d-none" 
                                        data-timesheet-id="{{ $activeTs->id }}"
                                        data-action="end">
                                    End Break
                                </button>
                                
                                <form action="{{ route('timesheets.complete') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="timesheet_id" value="{{ $activeTs->id }}">
                                    <button type="submit" class="btn btn-danger btn-sm">Complete Now</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @if(count($tasks) === 0)
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        No tasks assigned.
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .task-filter-panel {
        position: fixed;
        top: 0;
        right: -350px;
        width: 350px;
        height: 100%;
        background: #fff;
        box-shadow: -3px 0 10px rgba(0,0,0,0.3);
        transition: right .3s ease-in-out;
        z-index: 9999;
    }
    
    .task-filter-panel.active {
        right: 0;
    }
    
    .filter-square-btn {
        width: 40px;
        height: 40px;
        border: 1px solid #ddd;
        background: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    
    .filter-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .close-btn {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #666;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter panel toggle
    const openBtn = document.getElementById("openMyTaskFilter");
    const closeBtn = document.getElementById("closeMyTaskFilter");
    const panel = document.getElementById("myTaskFilterPanel");

    if (openBtn && closeBtn && panel) {
        openBtn.addEventListener("click", () => panel.classList.add("active"));
        closeBtn.addEventListener("click", () => panel.classList.remove("active"));
    }

    // Break management functionality
    document.querySelectorAll('.break-btn').forEach(button => {
        button.addEventListener('click', function() {
            const timesheetId = this.getAttribute('data-timesheet-id');
            const action = this.getAttribute('data-action');
            manageBreak(timesheetId, action);
        });
    });

    function manageBreak(timesheetId, action) {
        const url = action === 'start' 
            ? '{{ route("timesheets.break-start") }}' 
            : '{{ route("timesheets.break-end") }}';
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                timesheet_id: timesheetId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Toggle break buttons visibility
                const startBtn = document.querySelector(`.break-btn[data-timesheet-id="${timesheetId}"][data-action="start"]`);
                const endBtn = document.querySelector(`.break-btn[data-timesheet-id="${timesheetId}"][data-action="end"]`);
                
                if (action === 'start') {
                    startBtn.classList.add('d-none');
                    endBtn.classList.remove('d-none');
                } else {
                    startBtn.classList.remove('d-none');
                    endBtn.classList.add('d-none');
                }
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while managing break');
        });
    }
});
</script>