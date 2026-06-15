@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Tasks');
@endphp

<style>
.task-card {
    background: #fff;
    border-radius: 14px;
    padding: 18px 18px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.05);
    transition: .2s ease;
    text-align: center;
    min-height: 220px;
}

.task-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.08);
}

.task-header-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 4px;
}

.task-header-leader {
    font-size: 13px;
    color: #555;
}

.task-header-leader i {
    color: #FF7A44;
    margin-right: 4px;
}

/* ICONS */
.task-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #FF7A44 !important;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    margin: auto;
    margin-bottom: 6px;
}

/* NUMBERS */
.stat-number {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 2px;
}

.stat-label {
    font-size: 12px;
    color: #666;
}

/* ACTION BUTTONS */
.task-actions {
    margin-top: 12px;
    display: flex;
    justify-content: center;
    gap: 10px;
}

.task-btn {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: #fff;
    border: 1px solid #FF7A44;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #FF7A44;
    cursor: pointer;
    font-size: 15px;
    transition: .2s;
}

.task-btn:hover {
    background: #FF7A44;
    color: white;
}

.filter-square-btn {
    width: 42px;
    height: 42px;
    background: #fff;
    border: 1px solid #d6d6d6;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #595959;
    cursor: pointer;
    transition: 0.2s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.filter-square-btn:hover {
    background: #f5f5f5;
}

/* Slide Panel */
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

.filter-header {
    padding: 15px;
    background: #FF7A44;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.close-btn {
    background: none;
    border: none;
    font-size: 26px;
    color: #fff;
    cursor: pointer;
}


</style>


<div class="content container-fluid mt-4">

    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Project Tasks Overview</h4>
    
        <button id="openTaskFilterBtn" class="filter-square-btn">
            <i class="fa-solid fa-filter"></i>
        </button>
    </div>
    <div id="taskFilterPanel" class="task-filter-panel">

        <div class="filter-header">
            <h5 class="mb-0">Filter Tasks</h5>
            <button id="closeTaskFilterBtn" class="close-btn">&times;</button>
        </div>
    
        <form method="GET" class="p-3">
    
            <!-- Project -->
            <label class="fw-bold">Project</label>
            <select name="project" class="form-control mb-3">
                <option value="">All Projects</option>
                @foreach($tasks as $proj)
                    <option value="{{ $proj->projectid }}"
                        {{ request('project') == $proj->projectid ? 'selected' : '' }}>
                        {{ $proj->projectname }}
                    </option>
                @endforeach
            </select>
    
            <!-- Priority -->
            <label class="fw-bold">Priority</label>
            <select name="priority" class="form-control mb-3">
                <option value="">All</option>
                <option value="low" {{ request('priority')=='low'?'selected':'' }}>Low</option>
                <option value="medium" {{ request('priority')=='medium'?'selected':'' }}>Medium</option>
                <option value="high" {{ request('priority')=='high'?'selected':'' }}>High</option>
                <option value="urgent" {{ request('priority')=='urgent'?'selected':'' }}>Urgent</option>
            </select>
    
            <!-- Leader -->
            <label class="fw-bold">Team Leader</label>
            <select name="leader" class="form-control mb-3">
                <option value="">All Leaders</option>
                @foreach($tasks as $proj)
                    <option value="{{ $proj->projectleader }}"
                        {{ request('leader') == $proj->projectleader ? 'selected' : '' }}>
                        {{ $proj->leaderName }}
                    </option>
                @endforeach
            </select>
    
            <button class="btn btn-primary w-100 mt-2">Apply Filters</button>
        </form>
    
    </div>
    
    <div class="row g-4">

        @foreach($tasks as $p)
        <div class="col-xl-4 col-lg-4 col-md-6">

            <div class="task-card">

                <!-- HEADER -->
                <div class="task-header-title">{{ $p->projectname }}</div>

                <div class="task-header-leader">
                    <i class="fa fa-user"></i> {{ $p->leaderName }}
                </div>

                <hr>

                <!-- STATS -->
                <div class="row justify-content-center text-center">

                    <!-- TOTAL -->
                    <div class="col-4">
                        <div class="task-icon">
                            <i class="fa fa-list"></i>
                        </div>
                        <div class="stat-number">{{ $p->total_tasks }}</div>
                        <div class="stat-label">Total</div>
                    </div>

                    <!-- COMPLETED + PERCENTAGE -->
                    <div class="col-4">
                        <div class="task-icon">
                            <i class="fa fa-check"></i>
                        </div>

                        <div class="stat-number">{{ $p->completed_tasks }}</div>

                        @php
                            $percentage = ($p->total_tasks > 0)
                                ? round(($p->completed_tasks / $p->total_tasks) * 100)
                                : 0;
                        @endphp

                        <div class="stat-label">Completed ({{ $percentage }}%)</div>
                    </div>

                    <!-- PENDING -->
                    <div class="col-4">
                        <div class="task-icon">
                            <i class="fa fa-clock"></i>
                        </div>
                        <div class="stat-number">{{ $p->pending_tasks }}</div>
                        <div class="stat-label">Pending</div>
                    </div>

                </div>

                <!-- ACTION BUTTONS -->
                <div class="task-actions">
                    @if(isset($permissions) && $permissions->can_create)
                 
                    <a href="{{ route('tasks.create', ['projectid' => $p->id]) }}" class="task-btn">
                        <i class="fa fa-plus"></i>
                    </a>
                    @endif
                  

                    <a href="{{ route('projects.projectdetails', $p->projectid) }}" class="task-btn">

                        <i class="fa fa-eye"></i>
                    </a>
                </div>

            </div>

        </div>
        @endforeach

    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
    
        const openBtn = document.getElementById("openTaskFilterBtn");
        const closeBtn = document.getElementById("closeTaskFilterBtn");
        const panel = document.getElementById("taskFilterPanel");
    
        openBtn.addEventListener("click", () => {
            panel.classList.add("active");
        });
    
        closeBtn.addEventListener("click", () => {
            panel.classList.remove("active");
        });
    
    });
    </script>
    
