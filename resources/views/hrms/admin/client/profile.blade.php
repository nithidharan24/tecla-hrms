@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Profile</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
    
    <div class="card mb-0">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="profile-view">
                        <div class="profile-img-wrap">
                            <div class="profile-img">
                                <a href="">
                                    <img src="{{asset('admin/assets/img/user.jpg')}}" alt="User Image">
                                </a>
                            </div>
                        </div>
                        <div class="profile-basic">
                            <div class="row p-3">
                                <div class="col-md-5">
                                    <div class="profile-info-left">
                                        <h3 class="user-name m-t-0">{{ucFirst($client->company_name)}}</h3>
                                        <h5 class="company-role m-t-0 mb-0">{{ucFirst($client->first_name.' '.$client->last_name)}}</h5>
                                        <div class="staff-id">Client ID : {{$client->client_id}}</div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <ul class="personal-info">
                                        <li>
                                            <span class="title">Phone:</span>
                                            <span class="text"><a href="tel:{{$client->phone}}">{{$client->phone}}</a></span>
                                        </li>
                                        <li>
                                            <span class="title">Email:</span>
                                            <span class="text"><a href="mailto:{{$client->email}}"><span class="__cf_email__" data-cfemail="0d6f6c7f7f746e78696c4d68756c607d6168236e6260">[email&#160;protected]</span></a></span>
                                        </li>
                                        <li>
                                            <span class="title">Address:</span>
                                            <span class="text">{{$client->address}}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card tab-box">
        <div class="row user-tabs">
            <div class="col-lg-12 col-md-12 col-sm-12 line-tabs">
                <ul class="nav nav-tabs nav-tabs-bottom">
                    <li class="nav-item col-sm-3"><a class="nav-link active" data-bs-toggle="tab" href="#myprojects">Projects</a></li>
                    <li class="nav-item col-sm-3"><a class="nav-link" data-bs-toggle="tab" href="#tasks">Tasks</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12"> 
            <div class="tab-content profile-tab-content">
                
                <!-- Projects Tab -->
                <div id="myprojects" class="tab-pane fade show active">
                    <div class="row">
                        @if (count($projects) > 0)
                            @foreach ($projects as $item)
                            <div class="col-lg-4 col-sm-6 col-md-4 col-xl-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h4 class="project-title"><a href="#">
                                    @if (strlen($item->projectname)>=20){{
                                        substr($item->projectname, 0, 19).'...'}}
                                    @else
                                        {{$item->projectname}}
                                    @endif 
                                    </a></h4>

                                    <small class="block text-ellipsis m-b-15">
                                        <span class="text-xs">
                                        @php
                                            $pending;
                                        @endphp
                                        @php
                                            $i = 0;
                                        @endphp
                                            @foreach ($tasks as $pen)
                                                @if ($item->projectid == $pen->projects && $pen->status=='pending')
                                                    @php $i++;
                                                    @endphp
                                                @endif
                                            @endforeach
                                            @php
                                            $pending=$i;
                                            @endphp
                                        {{$pending}}
                                        </span> <span class="text-muted">open tasks, </span>
                                        <span class="text-xs">
                                        @php
                                            $completed;
                                        @endphp
                                        @php
                                            $j = 0;
                                        @endphp
                                            @foreach ($tasks as $pen)
                                                @if ($item->projectid == $pen->projects && $pen->status=='completed')
                                                    @php $j++;
                                                    @endphp
                                                @endif
                                            @endforeach
                                            @php
                                            $completed=$j;
                                            @endphp
                                        {{$completed}}
                                        </span> <span class="text-muted">tasks completed</span>
                                    </small>
                                    <p class="text-muted" style="height: 90px; overflow: hidden;">
                                        @if (strlen($item->description)>=76)
                                            {{substr($item->description, 0, 76).'...'}}
                                        @else
                                            {{$item->description}}
                                        @endif
                                    </p>
                                    <div class="pro-deadline m-b-15">
                                        <div class="sub-title">
                                            Deadline:
                                        </div>
                                        <div class="text-muted">
                                            {{$item->enddate}}
                                        </div>
                                    </div>
                                    <div class="project-members m-b-15">
                                        <div>Project Leader :</div>
                                        <ul class="team-members">
                                            <li>
                                                {{ucFirst($item->projectleader)}}
                                            </li>
                                        </ul>
                                    </div>
                                    @php
                                        $total = $completed+$pending;
                                        $cpercentage =  $total > 0 ? ($completed / $total) * 100 : 0;
                                    @endphp
                                    <p class="m-b-5">Progress <span class="text-success float-end">{{$cpercentage}}%</span></p>
                                    <div class="progress progress-xs mb-0">
                                        <div class="progress-bar bg-success w-{{$cpercentage}}" role="progressbar" data-bs-toggle="tooltip" title="{{$cpercentage}}%"></div>
                                    </div>
                                </div>
                            </div>
                            </div> 
                        @endforeach  
                        @else
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card text-center p-3">
                                    <strong>Projects Not Found</strong>
                                </div>
                            </div>
                        @endif                
                    </div>
                </div>
                <!-- /Projects Tab -->
                
                <!-- Task Tab -->
                <div id="tasks" class="tab-pane fade">
                    <div class="project-task">
                        <ul class="nav nav-tabs nav-tabs-top nav-justified mb-0">
                            <li class="nav-item"><a class="nav-link active" href="#all_tasks" data-bs-toggle="tab" aria-expanded="true">All Tasks</a></li>
                            <li class="nav-item"><a class="nav-link" href="#pending_tasks" data-bs-toggle="tab" aria-expanded="false">Pending Tasks</a></li>
                            <li class="nav-item"><a class="nav-link" href="#completed_tasks" data-bs-toggle="tab" aria-expanded="false">Completed Tasks</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane show active" id="all_tasks">
                                <div class="task-wrapper">
                                    <div class="task-list-container">
                                        <div class="task-list-body">
                                            <ul id="task-list">
                                                @if (count($tasks) > 0)
                                                @foreach ($tasks as $tsk)
                                                    <li class="{{$tsk->status=='completed' ? 'completed ' : ''}} task">
                                                        <div class="task-container">
                                                            <span class="task-action-btn task-check">
                                                                <span class="action-circle large complete-btn" title="Mark Complete">
                                                                    <i class="material-icons">check</i>
                                                                </span>
                                                            </span>
                                                            <span class="task-label">{{$tsk->task}}</span>
                                                        </div>
                                                    </li>
                                                    @endforeach
                                                @else
                                                    <li class="text-center">No Tasks Found</li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="pending_tasks">
                                <div class="task-wrapper">
                                    <div class="task-list-container">
                                        <div class="task-list-body">
                                            <ul id="task-list">
                                                @if (count($tasks) > 0)
                                                    @foreach ($tasks as $tsk)
                                                        @if ($tsk->status == 'pending')
                                                            <li class="task">
                                                                <div class="task-container">
                                                                    <span class="task-action-btn task-check">
                                                                        <span class="action-circle large complete-btn" title="Mark Complete">
                                                                            <i class="material-icons">check</i>
                                                                        </span>
                                                                    </span>
                                                                    <span class="task-label">{{$tsk->task}}</span>
                                                                </div>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <li class="text-center">No Pending Tasks Found</li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="completed_tasks">
                                <div class="task-wrapper">
                                    <div class="task-list-container">
                                        <div class="task-list-body">
                                            <ul id="task-list">
                                                @if (count($tasks) > 0)
                                                    @foreach ($tasks as $tsk)
                                                        @if ($tsk->status == 'completed')
                                                            <li class="task">
                                                                <div class="task-container">
                                                                    <span class="task-action-btn task-check">
                                                                        <span class="action-circle large complete-btn {{$tsk->status=='completed' ? 'bg-success text-white' : ''}}" title="Mark Complete">
                                                                            <i class="material-icons">check</i>
                                                                        </span>
                                                                    </span>
                                                                    <span class="task-label">{{$tsk->task}}</span>
                                                                </div>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <li class="text-center">No Completed Tasks Found</li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Task Tab -->
                
            </div>
        </div>
    </div>

</div>
<!-- /Page Content -->
<script>
        function deleteproject(id) {
                Swal.fire({
                    title: "Are you sure you want to delete this item?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    allowOutsideClick: false,
                }).then(function(result) {
                    if (result.isConfirmed) {
                        var url = "{{ route('client.destroy', ':id') }}"; // Here to Change the Route To Project Delete
                        url = url.replace(':id', id);

                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (data) {
                                Swal.fire({
                                    title: "Deleted!",
                                    text: "project has been deleted.",
                                    icon: "success"
                                }).then(() => {
                                    // Remove the table row
                                    // $('#client-row-' + id).remove();
                                    window.location.reload();
                                });
                            },
                            error: function (error) {
                                console.error("Error deleting project:", error);
                                Swal.fire({
                                    title: "Error",
                                    text: "Failed to delete project.",
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            }
</script>

@if (session('message'))
    <script>
        Swal.fire({
            icon: '{{ session("messageType") }}', // 'success' or other message types
            title: '{{ session("messageType") }}',
            text: '{{ session("message") }}', // The message text
            timer: 2500
        });
    </script>
@endif

@endsection
