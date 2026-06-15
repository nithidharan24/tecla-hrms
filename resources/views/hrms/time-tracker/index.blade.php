
@php
$userRole = Session::get('role');
$userId = Session::get('user_id');
$adminId = Session::get('admin_id');

$modules = [];

if ($userRole === 'employee' && $userId) {
    $modules = DB::table('employee_module_access')
        ->where('employee_id', $userId)
        ->pluck('module_name')
        ->toArray();

} elseif ($userRole === 'admin' && $adminId) {
    $modules = DB::table('admin_module_access')
        ->where('admin_id', $adminId)
        ->pluck('module_name')
        ->toArray();
}
@endphp
@extends('layouts.index')

@section('content')

<div class="content container-fluid mt-3">

    <!-- TIME TRACKER TABS -->
    <ul class="nav leave-tabs mb-0" role="tablist">
        @if(in_array('Clients', $modules))
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#clients-tab">
                Clients
            </a>
        </li>
        @endif
        @if(in_array('Projects', $modules))
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#projects-tab">
                Projects
            </a>
        </li>
        @endif
        @if(in_array('Project Tasks', $modules))
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tasks-tab">
                Project Tasks Overview
            </a>
        </li>
        @endif
            <!-- ⭐ NEW TAB -->
            @if(in_array('My Tasks', $modules))
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#mytasks-tab">
            My Tasks
        </a>
    </li>
    @endif

    </ul>

    <div class="tabs-underline"></div>

    <!-- TAB CONTENT -->
    <div class="tab-content pt-4">
        @if(in_array('Clients', $modules))
        <!-- CLIENTS TAB -->
        <div class="tab-pane fade show active" id="clients-tab">
            @include('hrms.admin.client.index')
        </div>
        @endif
        @if(in_array('Projects', $modules))
        <div class="tab-pane fade" id="projects-tab">
            @include('hrms.Employee.Project.index')
        </div>
        @endif
        
        @if(in_array('Project Tasks', $modules))
        <div class="tab-pane fade" id="tasks-tab">
            @include('hrms.Employee.Task.index')
        </div>
        @endif
        
        <!-- MY TASKS TAB -->
        @if(in_array('My Tasks', $modules))
<div class="tab-pane fade" id="mytasks-tab">
    @include('hrms.Employee.Task.my-tasks', $myTasks)

</div>
@endif

        

    </div>
</div>

<style>
.leave-tabs .nav-link {
    font-size: 15px;
    font-weight: 500;
    color: #333;
    border-bottom: 3px solid transparent;
}

.leave-tabs .nav-link.active {
    color: #f97316;
    border-bottom: 3px solid #f97316;
}

.tabs-underline {
    width: 100%;
    height: 2px;
    background: #e5eaf2;
    margin-top: -4px;
    margin-bottom: 12px;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let tab = localStorage.getItem("tracker_tab") || "#clients-tab";
    let trigger = document.querySelector(`a[href="${tab}"]`);
    if (trigger) new bootstrap.Tab(trigger).show();

    document.querySelectorAll('.leave-tabs .nav-link').forEach(link => {
        link.addEventListener('click', function () {
            localStorage.setItem("tracker_tab", this.getAttribute("href"));
        });
    });
});
</script>

@endsection
