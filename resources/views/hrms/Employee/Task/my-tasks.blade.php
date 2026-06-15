@php
    use Carbon\Carbon;

    if (!function_exists('cleanDateValue')) {
        function cleanDateValue($dateValue) {
            if (!$dateValue) {
                return null;
            }

            if ($dateValue instanceof Carbon) {
                return $dateValue->toDateString();
            }

            if (strpos($dateValue, ' ') !== false) {
                $dateValue = explode(' ', $dateValue)[0];
            }

            if (strpos($dateValue, '.') !== false) {
                $dateValue = explode('.', $dateValue)[0];
            }

            return $dateValue;
        }
    }

    if (!function_exists('cleanTimeValue')) {
        function cleanTimeValue($timeValue) {
            if (!$timeValue) {
                return null;
            }

            if ($timeValue instanceof Carbon) {
                return $timeValue->format('H:i:s');
            }

            if (strpos($timeValue, '.') !== false) {
                $timeValue = explode('.', $timeValue)[0];
            }

            if (strlen($timeValue) > 8) {
                $timeValue = substr($timeValue, 0, 8);
            }

            return $timeValue;
        }
    }

    if (!function_exists('formatSeconds')) {
        function formatSeconds($seconds) {
            if ($seconds <= 0) {
                return '0s';
            }

            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            $secs = $seconds % 60;
            $result = '';

            if ($hours > 0) {
                $result .= $hours . 'h ';
            }

            if ($minutes > 0) {
                $result .= $minutes . 'm ';
            }

            if ($secs > 0 || ($hours === 0 && $minutes === 0)) {
                $result .= $secs . 's';
            }

            return trim($result);
        }
    }

    if (!function_exists('safeParseDateTime')) {
        function safeParseDateTime($date, $time) {
            if (!$date || !$time) {
                return null;
            }

            try {
                $cleanDate = cleanDateValue($date);
                $cleanTime = cleanTimeValue($time);

                if (!$cleanTime || !preg_match('/^\d{1,2}:\d{1,2}(:\d{1,2})?$/', $cleanTime)) {
                    $cleanTime = '00:00:00';
                }

                return Carbon::parse($cleanDate . ' ' . $cleanTime);
            } catch (\Exception $e) {
                try {
                    if (strpos($date, ' ') !== false) {
                        return Carbon::parse($date);
                    }

                    return null;
                } catch (\Exception $e2) {
                    return null;
                }
            }
        }
    }

    $taskCollection = collect($tasks);
    $activeTasks = $taskCollection->filter(function ($task) use ($timesheetsByTask) {
        foreach (($timesheetsByTask[$task->id] ?? []) as $ts) {
            if ($ts->end_time === null) {
                return true;
            }
        }

        return false;
    })->values();

    $activeTaskIds = $activeTasks->pluck('id')->all();
    $queuedTasks = $taskCollection->reject(function ($task) use ($activeTaskIds) {
        return in_array($task->id, $activeTaskIds);
    })->values();

    $projectOptions = $taskCollection
        ->filter(function ($task) {
            return !empty($task->project_db_id);
        })
        ->unique('projects')
        ->values();

    $statusLabels = [
        '' => 'Pending + active',
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
    ];
    $currentStatusLabel = $statusLabels[request('status', '')] ?? ucfirst(str_replace('_', ' ', request('status')));
@endphp

<div class="container-fluid my-task-page mt-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="my-task-shell">
        <div class="my-task-hero">
            <div>
                <span class="my-task-kicker">Time tracker</span>
                <h4 class="fw-bold mb-1">My Tasks</h4>
                <p class="text-muted mb-0">Started tasks stay visible here, separate from the rest of your queue.</p>
            </div>
            <div class="my-task-hero-actions">
                <span class="filter-chip">{{ $currentStatusLabel }}</span>
                <button id="openMyTaskFilter" class="filter-square-btn" title="Filter tasks">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </div>
        </div>

        <div id="myTaskFilterPanel" class="task-filter-panel">
            <div class="filter-header">
                <div>
                    <span class="my-task-kicker">Refine</span>
                    <h5 class="mb-0">Filter Tasks</h5>
                </div>
                <button id="closeMyTaskFilter" class="close-btn" type="button">&times;</button>
            </div>

            <form method="GET" class="filter-form">
                <label class="fw-semibold">Project</label>
                <select name="project" class="form-control mb-3">
                    <option value="">All projects</option>
                    @foreach($projectOptions as $t)
                        <option value="{{ $t->projects }}" {{ request('project') == $t->projects ? 'selected' : '' }}>
                            {{ $t->projectname }}
                        </option>
                    @endforeach
                </select>

                <label class="fw-semibold">Priority</label>
                <select name="priority" class="form-control mb-3">
                    <option value="">All priorities</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>

                <label class="fw-semibold">Status</label>
                <select name="status" class="form-control mb-3">
                    <option value="" {{ request('status') === null || request('status') === '' ? 'selected' : '' }}>Pending + active</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>

                <button class="btn my-task-primary w-100 mt-2">Apply Filters</button>
            </form>
        </div>

        @if($activeTasks->count() > 0)
            <section class="task-section active-work-section">
                <div class="task-section-heading">
                    <div>
                        <span class="my-task-kicker">In progress now</span>
                        <h5 class="mb-0">Started Tasks</h5>
                    </div>
                    <span class="task-count-badge">{{ $activeTasks->count() }}</span>
                </div>

                <div class="row g-3">
                    @foreach($activeTasks as $task)
                        @include('hrms.Employee.Task.partials.my-task-card', ['task' => $task])
                    @endforeach
                </div>
            </section>
        @endif

        <section class="task-section">
            <div class="task-section-heading">
                <div>
                    <span class="my-task-kicker">Task queue</span>
                    <h5 class="mb-0">{{ $activeTasks->count() > 0 ? 'Remaining Tasks' : 'Assigned Tasks' }}</h5>
                </div>
                <span class="task-count-badge">{{ $queuedTasks->count() }}</span>
            </div>

            <div class="row g-3">
                @foreach($queuedTasks as $task)
                    @include('hrms.Employee.Task.partials.my-task-card', ['task' => $task])
                @endforeach

                @if($taskCollection->count() === 0)
                    <div class="col-12">
                        <div class="my-task-empty">
                            <i class="fa-regular fa-circle-check"></i>
                            <h6 class="mb-1">No tasks assigned.</h6>
                            <p class="text-muted mb-0">When a task is assigned to you, it will appear here.</p>
                        </div>
                    </div>
                @elseif($queuedTasks->count() === 0 && $activeTasks->count() > 0)
                    <div class="col-12">
                        <div class="my-task-empty compact">
                            <h6 class="mb-1">Everything else is clear.</h6>
                            <p class="text-muted mb-0">Your only visible task is already in progress above.</p>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>
</div>

<div id="taskDescriptionModal" class="task-description-modal" aria-hidden="true">
    <div class="task-description-backdrop" data-close-description></div>
    <div class="task-description-dialog" role="dialog" aria-modal="true" aria-labelledby="taskDescriptionTitle">
        <div class="task-description-header">
            <div>
                <span class="my-task-kicker">Task details</span>
                <h5 id="taskDescriptionTitle" class="mb-0"></h5>
            </div>
            <button type="button" class="task-description-close" data-close-description>&times;</button>
        </div>
        <div class="task-description-meta">
            <div>
                <span>Project</span>
                <strong data-description-project></strong>
            </div>
            <div>
                <span>Assigned to</span>
                <strong data-description-assigned></strong>
            </div>
            <div>
                <span>Due date</span>
                <strong data-description-due></strong>
            </div>
            <div>
                <span>Priority</span>
                <strong data-description-priority></strong>
            </div>
            <div>
                <span>Status</span>
                <strong data-description-status></strong>
            </div>
        </div>
        <div class="task-description-body">
            <span>Description</span>
            <p data-description-body></p>
        </div>
    </div>
</div>

<style>
    .my-task-page {
        color: #1f2937;
    }

    .my-task-shell {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 8px;
        padding: 18px;
    }

    .my-task-hero,
    .task-section {
        background: #fff;
        border: 1px solid #e9eef5;
        border-radius: 8px;
    }

    .my-task-hero {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 18px;
        border-left: 4px solid #f97316;
    }

    .my-task-hero-actions,
    .task-section-heading {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .my-task-hero-actions {
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .my-task-kicker {
        display: block;
        color: #f97316;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        margin-bottom: 3px;
    }

    .filter-chip,
    .task-count-badge {
        border: 1px solid #fed7aa;
        background: #fff7ed;
        color: #c2410c;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        padding: 7px 11px;
    }

    .filter-square-btn {
        width: 40px;
        height: 40px;
        border: 1px solid #fed7aa;
        background: #fff;
        color: #f97316;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all .2s ease;
    }

    .filter-square-btn:hover {
        background: #fff7ed;
        transform: translateY(-1px);
    }

    .task-section {
        margin-top: 16px;
        padding: 16px;
    }

    .active-work-section {
        background: linear-gradient(180deg, #fff7ed 0%, #fff 32%);
        border-color: #fed7aa;
    }

    .task-section-heading {
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .my-task-card {
        border: 1px solid #e5eaf2;
        border-radius: 8px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
        overflow: hidden;
    }

    .my-task-card.is-active {
        border-color: #fdba74;
        box-shadow: 0 14px 30px rgba(249, 115, 22, .14);
    }

    .priority-pill,
    .status-dot,
    .time-pill {
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .03em;
        text-transform: uppercase;
        padding: 6px 9px;
    }

    .priority-low { background: #ecfdf5; color: #047857; }
    .priority-medium { background: #eff6ff; color: #1d4ed8; }
    .priority-high { background: #fff7ed; color: #c2410c; }
    .priority-urgent { background: #fef2f2; color: #b91c1c; }
    .priority-pending { background: #f3f4f6; color: #4b5563; }

    .status-dot {
        background: #f3f4f6;
        color: #4b5563;
        text-transform: none;
        letter-spacing: 0;
    }

    .status-in_progress {
        background: #fff7ed;
        color: #c2410c;
    }

    .status-completed {
        background: #ecfdf5;
        color: #047857;
    }

    .task-meta-grid,
    .session-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .task-meta-grid {
        margin-bottom: 14px;
    }

    .task-meta-grid div,
    .session-grid div {
        background: #f8fafc;
        border: 1px solid #eef2f7;
        border-radius: 8px;
        padding: 10px;
        min-width: 0;
    }

    .task-meta-grid span,
    .session-grid span,
    .latest-session-line span {
        display: block;
        color: #64748b;
        font-size: 12px;
        margin-bottom: 2px;
    }

    .task-meta-grid strong,
    .session-grid strong {
        display: block;
        color: #111827;
        font-size: 13px;
        overflow-wrap: anywhere;
    }

    .session-summary,
    .session-panel {
        background: #fbfdff;
        border: 1px solid #eef2f7;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 14px;
    }

    .time-pill {
        background: #ecfdf5;
        color: #047857;
    }

    .latest-session-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
    }

    .task-description-preview {
        width: 100%;
        border: 1px solid #fed7aa;
        background: #fffaf5;
        border-radius: 8px;
        color: #334155;
        cursor: pointer;
        margin: 2px 0 16px;
        padding: 12px;
        text-align: left;
        transition: all .2s ease;
    }

    .task-description-preview:hover {
        border-color: #fb923c;
        box-shadow: 0 10px 22px rgba(249, 115, 22, .1);
        transform: translateY(-1px);
    }

    .task-description-preview span,
    .task-description-preview small {
        display: block;
        color: #c2410c;
        font-size: 12px;
        font-weight: 700;
    }

    .task-description-preview strong {
        display: -webkit-box;
        color: #1f2937;
        font-size: 13px;
        font-weight: 500;
        line-height: 1.5;
        margin: 5px 0;
        overflow: hidden;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 3;
        white-space: normal;
    }

    .task-description-preview small {
        color: #f97316;
        font-size: 11px;
        text-transform: uppercase;
    }

    .task-actions {
        flex-wrap: wrap;
    }

    .start-task-btn,
    .my-task-primary {
        background: #f97316;
        border-color: #f97316;
        color: #fff;
        font-weight: 700;
    }

    .start-task-btn:hover,
    .my-task-primary:hover {
        background: #ea580c;
        border-color: #ea580c;
        color: #fff;
    }

    .break-start-btn {
        background: #fff7ed;
        border-color: #fdba74;
        color: #c2410c;
        font-weight: 700;
    }

    .break-end-btn {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #1d4ed8;
        font-weight: 700;
    }

    .complete-task-btn {
        background: #fee2e2;
        border-color: #fecaca;
        color: #b91c1c;
        font-weight: 700;
    }

    .task-filter-panel {
        position: fixed;
        top: 0;
        right: -380px;
        width: 360px;
        max-width: calc(100vw - 24px);
        height: 100%;
        background: #fff;
        box-shadow: -18px 0 40px rgba(15, 23, 42, .18);
        transition: right .3s ease-in-out;
        z-index: 9999;
        border-left: 1px solid #fed7aa;
    }

    .task-filter-panel.active {
        right: 0;
    }

    .filter-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px;
        border-bottom: 1px solid #eef2f7;
    }

    .filter-form {
        padding: 18px;
    }

    .filter-form .form-control {
        border-radius: 8px;
        border-color: #e5eaf2;
        min-height: 42px;
    }

    .close-btn {
        width: 36px;
        height: 36px;
        border: 1px solid #e5eaf2;
        background: #fff;
        border-radius: 8px;
        font-size: 22px;
        line-height: 1;
        cursor: pointer;
        color: #64748b;
    }

    .my-task-empty {
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        background: #fff;
        text-align: center;
        padding: 34px 18px;
    }

    .my-task-empty.compact {
        padding: 22px 18px;
    }

    .my-task-empty i {
        color: #f97316;
        font-size: 28px;
        margin-bottom: 10px;
    }

    .task-description-modal {
        display: none;
        inset: 0;
        position: fixed;
        z-index: 10050;
    }

    .task-description-modal.active {
        display: block;
    }

    .task-description-backdrop {
        background: rgba(15, 23, 42, .42);
        inset: 0;
        position: absolute;
    }

    .task-description-dialog {
        background: #fff;
        border: 1px solid #fed7aa;
        border-radius: 8px;
        box-shadow: 0 24px 70px rgba(15, 23, 42, .28);
        left: 50%;
        max-height: min(76vh, 620px);
        max-width: 680px;
        overflow: hidden;
        position: absolute;
        top: 50%;
        transform: translate(-50%, -50%);
        width: calc(100% - 32px);
    }

    .task-description-header {
        align-items: flex-start;
        border-bottom: 1px solid #eef2f7;
        display: flex;
        gap: 16px;
        justify-content: space-between;
        padding: 18px;
    }

    .task-description-close {
        align-items: center;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-radius: 8px;
        color: #c2410c;
        cursor: pointer;
        display: inline-flex;
        font-size: 24px;
        height: 36px;
        justify-content: center;
        line-height: 1;
        width: 36px;
    }

    .task-description-meta {
        display: grid;
        gap: 10px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        padding: 16px 18px 0;
    }

    .task-description-meta div {
        background: #f8fafc;
        border: 1px solid #eef2f7;
        border-radius: 8px;
        min-width: 0;
        padding: 10px;
    }

    .task-description-meta span,
    .task-description-body span {
        color: #64748b;
        display: block;
        font-size: 12px;
        margin-bottom: 3px;
    }

    .task-description-meta strong {
        color: #111827;
        display: block;
        font-size: 13px;
        overflow-wrap: anywhere;
    }

    .task-description-body {
        padding: 16px 18px 18px;
    }

    .task-description-body p {
        background: #fffaf5;
        border: 1px solid #fed7aa;
        border-radius: 8px;
        color: #1f2937;
        line-height: 1.65;
        margin: 0;
        max-height: 300px;
        overflow: auto;
        padding: 14px;
        white-space: pre-line;
    }

    @media (max-width: 767.98px) {
        .my-task-shell {
            padding: 12px;
        }

        .my-task-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .my-task-hero-actions {
            justify-content: space-between;
            width: 100%;
        }

        .task-meta-grid,
        .session-grid {
            grid-template-columns: 1fr;
        }

        .task-description-meta {
            grid-template-columns: 1fr;
        }

        .task-description-dialog {
            max-height: 84vh;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const openBtn = document.getElementById("openMyTaskFilter");
    const closeBtn = document.getElementById("closeMyTaskFilter");
    const panel = document.getElementById("myTaskFilterPanel");

    if (openBtn && closeBtn && panel) {
        openBtn.addEventListener("click", () => panel.classList.add("active"));
        closeBtn.addEventListener("click", () => panel.classList.remove("active"));
    }

    document.querySelectorAll('.break-btn').forEach(button => {
        button.addEventListener('click', function() {
            const timesheetId = this.getAttribute('data-timesheet-id');
            const action = this.getAttribute('data-action');
            manageBreak(timesheetId, action);
        });
    });

    const descriptionModal = document.getElementById('taskDescriptionModal');
    const modalTitle = descriptionModal ? descriptionModal.querySelector('#taskDescriptionTitle') : null;
    const modalBody = descriptionModal ? descriptionModal.querySelector('[data-description-body]') : null;

    document.querySelectorAll('.task-description-preview').forEach(button => {
        button.addEventListener('click', function() {
            const card = this.closest('.my-task-card');
            const fullDescription = card ? card.querySelector('.task-full-description') : null;

            if (!descriptionModal || !modalTitle || !modalBody) {
                return;
            }

            modalTitle.textContent = this.dataset.title || 'Task description';
            modalBody.textContent = fullDescription ? fullDescription.textContent.trim() : 'No description added.';
            descriptionModal.querySelector('[data-description-project]').textContent = this.dataset.project || '-';
            descriptionModal.querySelector('[data-description-assigned]').textContent = this.dataset.assigned || '-';
            descriptionModal.querySelector('[data-description-due]').textContent = this.dataset.due || '-';
            descriptionModal.querySelector('[data-description-priority]').textContent = this.dataset.priority || '-';
            descriptionModal.querySelector('[data-description-status]').textContent = this.dataset.status || '-';
            descriptionModal.classList.add('active');
            descriptionModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-open');
        });
    });

    document.querySelectorAll('[data-close-description]').forEach(button => {
        button.addEventListener('click', closeDescriptionModal);
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDescriptionModal();
        }
    });

    function closeDescriptionModal() {
        if (!descriptionModal) {
            return;
        }

        descriptionModal.classList.remove('active');
        descriptionModal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    }

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
