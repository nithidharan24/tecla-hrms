@extends('layouts.index')

@section('content')

<style>

/* =================== TOP BANNER =================== */
.project-cover {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 240px;
    background: url('{{ asset('admin/assets/bg.jpg') }}') center/cover no-repeat;
    border-radius: 14px;
    z-index: 0;
}

.project-cover::after {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.35);
    border-radius: 14px;
}

/* WRAPPER */
.project-wrapper {
    padding: 20px;
    margin-top: 140px; /* pushes content below banner */
    position: relative;
}

/* =================== LEFT CARD =================== */

.project-info-card {
    background: #fff;
    padding: 22px;
    border-radius: 16px;
    width: 260px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.10);
    position: relative;
    z-index: 5;
    margin-left: -65px;
    margin-top: -50px;
}
/* Avatar block */
.project-avatar {
    width: 85px;
    height: 85px;
    background: #FF7A44;
    border-radius: 18px;
    font-size: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-bottom: 15px;
}

/* Title + Status */
.project-title {
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 4px;
}

.project-status {
    font-size: 14px;
    font-weight: 600;
    color: #ff7a44;
    margin-bottom: 15px;
}

.info-section {
    margin-bottom: 10px;
}

.info-row-label {
    font-size: 11px;
    color: #777;
    margin-bottom: 2px;
}

.info-row-value {
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

/* Divider */
.card-divider {
    border-top: 1px solid #eee;
    margin: 15px 0;
}

/* Description text */
.description-text {
    font-size: 13px;
    color: #555;
}


/* =================== TABS WRAPPER =================== */

.tabs-bar {
    background: #fff;
    padding: 15px 30px;
    border-radius: 14px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 20px;
    margin-left: -50px;
    width: 950PX;
    margin-top: -50px;
}



.tabs-bar ul {
    display: flex;
    gap: 50px;
    list-style: none;
}

.tab-btn {
    font-size: 15px;
    font-weight: 600;
    padding-bottom: 8px;
    cursor: pointer;
    transition: .3s;
}

.tab-btn.active {
    color: #FF7A44;
    border-bottom: 3px solid #FF7A44;
}

.tab-content {
    padding: 20px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
    width: 950px;
    margin-left: -50px;
}
/* Feed box */
.feed-box {
    background: #fff;
    padding: 15px;
    border-radius: 14px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
}

.feed-message-box {
    width: 100%;
    height: 90px;
    padding: 10px;
    border-radius: 10px;
    border: 1px solid #dcdcdc;
}

/* No feed message */
.empty-state {
    text-align: center;
    margin-top: 35px;
    color: #777;
}
.performance-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 10px;
}

.chart-card {
    padding: 20px;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    min-height: 220px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* Fix donut height */
.chart-card canvas {
    max-height: 150px !important;
}

/* Scroll inside Performance tab */
#performance {
    max-height: 750px;
    overflow-y: auto;
    padding-right: 10px;
}
.activity-timeline {
    position: relative;
    padding-left: 30px;
}

.activity-timeline::before {
    content: "";
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #eee;
}

.activity-item {
    position: relative;
    margin-bottom: 25px;
}

.activity-dot {
    position: absolute;
    left: -1px;
    top: 5px;
    width: 12px;
    height: 12px;
    background: #ff7a44;
    border-radius: 50%;
}

.activity-content {
    background: #fff;
    padding: 15px 18px;
    border-radius: 14px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
}




</style>

<!-- Background Banner -->
<div class="project-cover"></div>

<div class="container project-wrapper">

    <div class="row">

        <!-- =============== LEFT PROJECT CARD =============== -->
        <div class="col-md-3">
            <div class="project-info-card">

                <div class="project-avatar">
                    {{ strtoupper(substr($project->projectname,0,1)) }}
                </div>

                <div class="project-title">{{ $project->projectname }}</div>
                <div class="project-status">{{ $project->status }}</div>

                <div class="info-section">
                    <div class="info-row-label">Project Head</div>
                    <div class="info-row-value">{{ $project->leader_name }}</div>
                </div>

                <div class="info-section">
                    <div class="info-row-label">Client</div>
                    <div class="info-row-value">{{ $project->client_name }}</div>
                </div>

                <div class="info-section">
                    <div class="info-row-label">Project Users</div>
                    <div class="info-row-value">{{ $project->project_users_count }}</div>
                </div>

                <div class="card-divider"></div>

                <div class="project-block-title">Description</div>
                <p class="description-text">
                    {!! $project->description ?: "A detailed description helps give a clear picture about the project." !!}
                </p>

            </div>
        </div>


        <!-- =============== RIGHT PANEL (TABS + CONTENT) =============== -->
        <div class="col-md-9">

            <!-- TABS -->
            <div class="tabs-bar">
                <ul>
                    <li class="tab-btn active" data-tab="feeds">Feeds</li>
                    <li class="tab-btn" data-tab="performance">Project Performance</li>
                    <li class="tab-btn" data-tab="jobs">Task</li>
                    <li class="tab-btn" data-tab="users">Users</li>
                    <li class="tab-btn" data-tab="attachments">Attachments</li>
                    <li class="tab-btn" data-tab="activity">Activity Log</li>

                </ul>
            </div>

            <!-- TAB CONTENT AREA -->
            <div id="tabContentContainer">

                <!-- Feeds -->
                <div class="tab-content" id="feeds" style="display:block;">
    
                    <!-- MESSAGE INPUT -->
                    <div class="feed-box">
                        <textarea id="feedMessage" class="feed-message-box" placeholder="Type message..."></textarea>
                        <button class="btn btn-sm btn-primary mt-2" onclick="sendFeed()">Send</button>
                    </div>
                
                    <!-- DISPLAY FEEDS -->
                    <div id="feedList" class="mt-4"></div>
                
                </div>
                

                <div class="tab-content" id="performance" style="display:none;">
                   
                    <!-- TOP PERFORMANCE SUMMARY CARDS -->
                    <div class="row mb-4">
                
                        <div class="col-md-3">
                            <div class="card shadow-sm p-3 text-center" style="border-radius:16px;">
                                <h6>Total Tasks</h6>
                                <h2 class="fw-bold">{{ $totalTasks }}</h2>
                            </div>
                        </div>
                
                        <div class="col-md-3">
                            <div class="card shadow-sm p-3 text-center" style="border-radius:16px;">
                                <h6>Completed</h6>
                                <h2 class="fw-bold text-success">{{ $completedTasks }}</h2>
                            </div>
                        </div>
                
                        <div class="col-md-3">
                            <div class="card shadow-sm p-3 text-center" style="border-radius:16px;">
                                <h6>In Progress</h6>
                                <h2 class="fw-bold text-primary">{{ $inProgressTasks }}</h2>
                            </div>
                        </div>
                
                        <div class="col-md-3">
                            <div class="card shadow-sm p-3 text-center" style="border-radius:16px;">
                                <h6>Overdue</h6>
                                <h2 class="fw-bold text-danger">{{ $overdueTasks }}</h2>
                            </div>
                        </div>
                
                    </div>
                
                    <!-- ==================== ROW 2: DONUT CHARTS ==================== -->
                    <div class="row">
                
                        <!-- Task Status Donut -->
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm p-3" style="border-radius:16px;">
                                <h6 class="mb-3">Task Status Overview</h6>
                                <canvas id="taskStatusChart"></canvas>
                            </div>
                        </div>
                
                        <!-- Hours Logged vs Break Time -->
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm p-3" style="border-radius:16px;">
                                <h6 class="mb-3">Hours Logged vs Break Time</h6>
                                <canvas id="hoursBreakChart"></canvas>
                            </div>
                        </div>
                
                        <!-- On-time vs Delayed Tasks -->
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm p-3" style="border-radius:16px;">
                                <h6 class="mb-3">On-Time vs Delayed Completion</h6>
                                <canvas id="delayChart"></canvas>
                            </div>
                        </div>
                
                    </div>
                
                    <!-- ==================== ROW 3: BAR CHARTS ==================== -->
                    <div class="row">
                
                        <!-- User Task Load -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm p-3" style="border-radius:16px;">
                                <h6 class="mb-3">Tasks Assigned Per User</h6>
                                <canvas id="userTaskChart"></canvas>
                            </div>
                        </div>
                
                        <!-- User Logged Hours -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm p-3" style="border-radius:16px;">
                                <h6 class="mb-3">Logged Hours Per User</h6>
                                <canvas id="userHoursChart"></canvas>
                            </div>
                        </div>
                
                    </div>
                
                    <!-- ==================== WEEKLY REPORT ==================== -->
                    <div class="card shadow-sm p-4 mt-4" style="border-radius:16px;">
                        <h6>Weekly Time Log Summary</h6>
                        <canvas id="weeklyChart"></canvas>
                    </div>
                    <div class="card shadow-sm p-4 mt-4" style="border-radius:16px;">

                        <h5 class="fw-bold mb-3">Employee Performance Summary</h5>
                    
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="p-3 rounded bg-white shadow-sm">
                                    <h6 class="text">Top Performer</h6>
                                    <p class="mb-1 fw-bold text-warning">{{ $topPerformer['name'] }}</p>
                                    <small class="text-dark">Logged Hours: {{ $topPerformer['logged_hours'] }} hrs</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="p-3 rounded bg-white shadow-sm">
                                    <h6 class="text">Most Overdue Tasks</h6>
                                    <p class="mb-1 fw-bold text-warning">{{ $mostOverdue['name'] }}</p>
                                    <small class="text-dark">Overdue: {{ $mostOverdue['overdue'] }}</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="p-3 rounded bg-white shadow-sm">
                                    <h6 class="text">Best On-Time Completer</h6>
                                    <p class="mb-1 fw-bold text-warning">{{ $bestOnTime['name'] }}</p>
                                    <small class="text-dark">Completed: {{ $bestOnTime['completed'] }}</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="p-3 rounded bg-white shadow-sm">
                                    <h6 class="text">Most Break Time</h6>
                                    <p class="mb-1 fw-bold text-warning">{{ $mostBreaks['name'] }}</p>
                                    <small class="text-dark">Break Hours: {{ $mostBreaks['break_hours'] }} hrs</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="p-3 rounded bg-white shadow-sm">
                                    <h6 class="text">Least Active</h6>
                                    <p class="mb-1 fw-bold text-warning">{{ $leastActive['name'] }}</p>
                                    <small class="text-dark">Logged Hours: {{ $leastActive['logged_hours'] }} hrs</small>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    
            </div>
                
            <div class="tab-content" id="jobs" style="display:none;">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold">Task List</h5>
            
                    <!-- Filter Button -->
                    <button class="btn btn-outline-primary" onclick="openFilterPanel()">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                </div>
            
            
<div class="table-responsive">
    <table class="table custom-table datatable dataTable no-footer" id="DataTables_Table_0">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Task Name</th>
                                <th>Due Date</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Attempts</th>
                                <th>Break Time (min)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="taskBody">
                            <!-- Filled by AJAX -->
                        </tbody>
                    </table>
                </div>
            </div><!-- Right Slide Filter Panel -->
            <div id="filterPanel" style="
                position: fixed;
                top: 0;
                right: -380px;
                width: 350px;
                height: 100vh;
                background: white;
                box-shadow: -3px 0 10px rgba(0,0,0,0.15);
                padding: 20px;
                transition: 0.4s;
                z-index: 9999;
            ">
            
                <h5 class="fw-bold mb-3">Filter Tasks</h5>
            
                <label>Employee</label>
                <select id="filterEmployee" class="form-control mb-3">
                    <option value="">All</option>
                    @foreach($projectUsers as $u)
                        <option value="{{ $u->id }}">{{ $u->firstname }} {{ $u->lastname }}</option>
                    @endforeach
                </select>
            
                <label>Status</label>
                <select id="filterStatus" class="form-control mb-3">
                    <option value="">All</option>
                    <option value="completed">Completed</option>
                    <option value="in-progress">In Progress</option>
                    <option value="pending">Pending</option>
                </select>
            
                <label>Due Date</label>
                <input type="date" id="filterDueDate" class="form-control mb-3">
            
                <label>Task Name</label>
                <input type="text" id="filterTaskName" class="form-control mb-3">
            
                <button class="btn btn-primary w-100" onclick="applyFilters()">Apply Filters</button>
                <button class="btn btn-secondary w-100 mt-2" onclick="closeFilterPanel()">Close</button>
            </div>
                        
            <div class="tab-content" id="users" style="display:none;">

                <h5 class="fw-bold mb-4">Project Team Members</h5>
            
                <div class="row">
                    @forelse($projectTeam as $user)
            
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm p-3" style="border-radius:16px;">
                                
                                <div class="d-flex align-items-center">
                                    
                                    <img 
                                    src="{{ asset($user->profile_image) }}" 
                                    class="rounded-circle"
                                    width="45"
                                    height="45"
                                    style="object-fit:cover;"
                                >
                                
            
                                    <div class="ms-3">
                                        <h6 class="mb-0 fw-bold">
                                            {{ $user->firstname }} {{ $user->lastname }}
                                        </h6>
            
                                        <small class="text-muted">
                                            {{ $user->designation_name ?? '—' }}
                                        </small>
            
                                        @if($user->is_leader)
                                            <span class="badge bg-warning text-dark ms-2">
                                                Team Leader
                                            </span>
                                        @endif
                                    </div>
                                </div>
            
                            </div>
                        </div>
            
                    @empty
                        <div class="col-12 text-center text-muted">
                            No team members assigned to this project.
                        </div>
                    @endforelse
                </div>
            
            </div>
            

            <div class="tab-content" id="attachments" style="display:none;">

                <h5 class="fw-bold mb-4">Project Attachments</h5>
            
                @if(!empty($project->projectfile))
            
                    @php
                        $filePath = asset($project->projectfile);
                        $fileName = basename($project->projectfile);
                    @endphp
            
                    <div class="card shadow-sm p-4" style="border-radius:16px;">
                        <div class="d-flex align-items-center justify-content-between">
            
                            <div class="d-flex align-items-center">
                                <i class="fa fa-file-pdf-o text-danger fs-3 me-3"></i>
            
                                <div>
                                    <div class="fw-bold">{{ $fileName }}</div>
                                    <small class="text-muted">Project File</small>
                                </div>
                            </div>
                            <a href="{{ route('project.file.download', $project->projectid) }}"
                                class="btn btn-outline-danger">
                                 <i class="fa fa-download"></i> Download
                             </a>
                             
                        
            
                        </div>
                    </div>
            
                @else
                    <div class="card shadow-sm p-5 text-center text-muted" style="border-radius:16px;">
                        <img src="{{ asset('assets/no-attachment.png') }}" width="90" class="mb-3">
                        <p class="mb-0">No attachments uploaded for this project.</p>
                    </div>
                @endif
            
            </div>
            <div class="tab-content" id="activity" style="display:none;">

                <h5 class="fw-bold mb-4">Project Activity Log</h5>
            
                @if($activityLogs->count())
                    <div class="activity-timeline">
            
                        @foreach($activityLogs as $log)
                            <div class="activity-item">
                                <div class="activity-dot"></div>
            
                                <div class="activity-content">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $log->actor_name }}</strong>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, h:i A') }}
                                        </small>
                                    </div>
                                    <div class="text-warning fw-semibold mt-1">
                                        {{ ucfirst(str_replace('_',' ', $log->action)) }}
                                        @if(!empty($log->task_name))
                                            <span class="text-muted">
                                                ({{ $log->task_name }})
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="text-muted">
                                        {{ $log->description }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
            
                    </div>
                @else
                    <div class="card shadow-sm p-5 text-center text-muted" style="border-radius:16px;">
                        No activity logs found for this project.
                    </div>
                @endif
            
            </div>
            
            

            </div>

        </div>

    </div>
</div>


<script>
// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function () {

        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        document.querySelectorAll('.tab-content').forEach(tab => tab.style.display = "none");

        let selected = this.getAttribute('data-tab');
        document.getElementById(selected).style.display = "block";
    });
});
</script>
<script>
    let projectid = "{{ $project->projectid }}";
    
    function loadFeeds() {
    fetch(`/projectdetails/${projectid}/feeds`)
        .then(res => res.json())
        .then(data => {
            let html = "";

            data.forEach(feed => {
                html += `
                    <div style="padding:10px; margin-bottom:10px; background:#f8f8f8; border-radius:10px;">
                        <b>${feed.sender_name}</b><br>
                        ${feed.message}<br>
                        <small>${feed.created_at}</small>
                    </div>
                `;
            });

            document.getElementById("feedList").innerHTML = html || 
                "<div class='empty-state'><img src='/assets/no-feed.png' width='120'><br>No Feeds Yet</div>";
        });
}

    loadFeeds(); 
    setInterval(loadFeeds, 5000);
    
    function sendFeed() {
        let msg = document.getElementById("feedMessage").value;
        if (msg.trim() === "") return;
    
        fetch(`/projectdetails/feed/send`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                projectid: projectid,
                message: msg
            })
        })
        .then(res => res.json())
        .then(() => {
            document.getElementById("feedMessage").value = "";
            loadFeeds();
        });
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* ==================== TASK STATUS DONUT ==================== */
new Chart(document.getElementById('taskStatusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Completed', 'Progress', 'Pending', 'Overdue'],
        datasets: [{
            data: [
                {{ $completedTasks }},
                {{ $inProgressTasks }},
                {{ $pendingTasks }},
                {{ $overdueTasks }}
            ],
            backgroundColor: ['#28a745', '#007bff', '#ffc107', '#dc3545']
        }]
    },
    options: { responsive: true }
});

/* ==================== HOURS VS BREAK ==================== */
let totalBreak = {{ collect($userStats)->sum('break_hours') }};
let totalLogged = {{ collect($userStats)->sum('logged_hours') }};

new Chart(document.getElementById('hoursBreakChart'), {
    type: 'doughnut',
    data: {
        labels: ['Logged Hours', 'Break Hours'],
        datasets: [{
            data: [totalLogged, totalBreak],
            backgroundColor: ['#17a2b8', '#6c757d'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});


/* ==================== DELAY CHART ==================== */
new Chart(document.getElementById('delayChart'), {
    type: 'doughnut',
    data: {
        labels: ['On-Time', 'Delayed'],
        datasets: [{
            data: [
                {{ $completedTasks - ($overdueTasks) }},
                {{ $overdueTasks }}
            ],
            backgroundColor: ['#20c997', '#ff073a']
        }]
    }
});

/* ==================== USER TASK BAR ==================== */
new Chart(document.getElementById('userTaskChart'), {
    type: 'bar',
    data: {
        labels: @json(array_column($userStats, 'name')),
        datasets: [{
            label: 'Total Tasks',
            data: @json(array_column($userStats, 'total_tasks')),
            backgroundColor: '#007bff'
        }]
    }
});

/* ==================== USER HOURS BAR ==================== */
new Chart(document.getElementById('userHoursChart'), {
    type: 'bar',
    data: {
        labels: @json(array_column($userStats, 'name')),
        datasets: [{
            label: 'Hours Logged',
            data: @json(array_column($userStats, 'logged_hours')),
            backgroundColor: '#28a745'
        }]
    }
});

new Chart(document.getElementById('weeklyChart'), {
    type: 'line',
    data: {
        labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
        datasets: [{
            label: 'Logged Hours',
            data: [
                {{ $weeklyHours['Mon'] }},
                {{ $weeklyHours['Tue'] }},
                {{ $weeklyHours['Wed'] }},
                {{ $weeklyHours['Thu'] }},
                {{ $weeklyHours['Fri'] }},
                {{ $weeklyHours['Sat'] }},
                {{ $weeklyHours['Sun'] }}
            ],
            borderColor: '#007bff',
            fill: false,
            tension: 0.3
        }]
    }
});
function openFilterPanel() {
    document.getElementById("filterPanel").style.right = "0px";
}

function closeFilterPanel() {
    document.getElementById("filterPanel").style.right = "-380px";
}
function formatTime(time) {
    if (!time) return '-';

    // Check if time is in HH:MM:SS format
    if (typeof time === 'string' && time.includes(':')) {
        // Split the time
        let timeParts = time.split(':');
        let hours = parseInt(timeParts[0]);
        let minutes = timeParts[1];
        
        // Convert to 12-hour format
        let period = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // Convert 0 to 12 for midnight
        
        // Pad hours if needed (though usually not needed for 12-hour format)
        return `${hours}:${minutes} ${period}`;
    }
    
    return '-';
}
function loadTasks(filters = {}) {
    fetch(`{{ route('projectdetails.tasks.load', ['projectid' => $project->projectid]) }}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify(filters)
    })
    .then(res => res.json())
    .then(data => {
        let html = "";

        data.forEach(row => {
            html += `
                <tr>
                    <td>${row.employee}</td>
                    <td>${row.task_name}</td>
                    <td>${row.due_date}</td>
                    <td>${formatTime(row.start_time)}</td>
<td>${formatTime(row.end_time)}</td>

                    <td>${row.attempts}</td>
                    <td>${row.break_minutes}</td>
                    <td><span class="badge bg-${row.status_color}">${row.status}</span></td>
                </tr>
            `;
        });

        document.getElementById("taskBody").innerHTML = html;
    });
}

function applyFilters() {
    const filters = {
        employee_id: document.getElementById("filterEmployee").value,
        status: document.getElementById("filterStatus").value,
        due_date: document.getElementById("filterDueDate").value,
        task_name: document.getElementById("filterTaskName").value
    };

    loadTasks(filters);
    closeFilterPanel();
}

window.onload = function() {
    loadTasks(); // load all tasks by default
}


</script>

    

@endsection
