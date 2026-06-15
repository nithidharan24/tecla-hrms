@extends('layouts.index')
@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">{{ $project->projectname }}</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Project</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="{{ route('projects.edit', $project->id) }}" class="btn add-btn">
                    Edit Project
                </a>
                <div class="view-icons">
                    <a href="{{ route('time-tracker.index') }}" class="list-view btn btn-link active"><i class="fa-solid fa-bars"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Project Description Section with Shadow -->
        <div class="col-lg-8 mb-4"> <!-- mb-4 for bottom margin -->
            <div class="card shadow-sm"> <!-- Added shadow-sm class for shadow -->
                <div class="card-body">
                    <h3 class="card-title">{{ $project->projectname }}</h3>
                    <p class="card-text">{!! $project->description !!}</p> <!-- Display HTML content -->
                </div>
            </div>
        </div>
        <!-- Project Details Section with Shadow for List -->
        <div class="col-lg-4 mb-4"> <!-- mb-4 for bottom margin -->
            <div class="card shadow-sm"> <!-- Added shadow-sm class for shadow -->
                <div class="card-body">
                    <h5 class="card-title">Project Details</h5>
                    <!-- Applied shadow to each list item -->
                    <ul class="list-group list-group-flush">
                      
                        @if(isset($project->totalhours))
                        <li class="list-group-item shadow-sm"><strong>Total Hours:</strong> {{ $project->totalhours }} Hours</li>
                        @else
                        <li class="list-group-item shadow-sm"><strong>Total Hours:</strong> N/A</li>
                        @endif
                        <li class="list-group-item shadow-sm"><strong>Created:</strong> {{ \Carbon\Carbon::parse($project->created_at)->format('d M, Y') }}</li>
                        <li class="list-group-item shadow-sm"><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($project->enddate)->format('d M, Y') }}</li>
                        <li class="list-group-item shadow-sm"><strong>Priority:</strong> <span class="badge bg-danger">{{ $project->priority }}</span></li>
                        <li class="list-group-item shadow-sm"><strong>Client:</strong> {{ $project->client }}</li>
                        <li class="list-group-item shadow-sm"><strong>Status:</strong> {{ $project->status }}</li>
                        <!-- Progress bar for project progress -->
                        <li class="list-group-item shadow-sm">
                            <strong>Progress:</strong>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">40%</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Assigned Leader Section with Shadow -->
    <div class="row">
        <div class="col-lg-8 mb-4"> <!-- mb-4 for bottom margin -->
            <div class="card shadow-sm"> <!-- Added shadow-sm class for shadow -->
                <div class="card-body">
                    <h5 class="card-title">Assigned Leader</h5>
                    <p>{{ $project->leaderName }}</p> <!-- Display leader's name -->
                </div>
            </div>
        </div>
    </div>
    <!-- Team Members Section with Shadow List -->
    <div class="row">
        <div class="col-lg-8 mb-4"> <!-- mb-4 for bottom margin -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Team Members</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item shadow-sm">{!! $project->teamNames !!}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
