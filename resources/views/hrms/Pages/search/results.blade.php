@extends('layouts.index')

@section('content')
<div class="container mt-5">
    <div class="page-header">
        <h3 class="page-title">Search Result Found For: {{ $keyword }}</h3> <!-- Ensure this is correct -->
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
            <li class="breadcrumb-item">Search Results</li>
        </ul>
    </div>

    <div class="row">
        @foreach($projects as $project)
        <div class="col-lg-3 col-md-6 mb-4 d-flex">
            <div class="card w-100">
                <div class="card-body">
                    <h4 class="project-title">
                        <a href="#">{{ $project->projectname }}</a>
                    </h4>
                    <p class="text-muted">{{ Str::limit(strip_tags($project->description), 100, '...') }}</p>
                    <div class="pro-deadline m-b-15">
                        <div class="sub-title">Deadline:</div>
                        <div class="text-muted">{{ \Carbon\Carbon::parse($project->enddate)->format('d M, Y') }}</div>
                    </div>
                    <div class="project-members m-b-15">
                        <div>Assigned to:</div>
                        <ul class="team-members">
                            <li>{{ $project->projectleader }}</li>
                        </ul>
                    </div>
                    <div class="project-members m-b-15">
                        <div>Team Members:</div>
                        <ul class="team-members">
                            <li>{{ $project->team }}</li>
                        </ul>
                    </div>
                    <div class="project-members m-b-15">
                        <div>Client ID:</div>
                        <ul class="team-members">
                            <li>{{ $project->client }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
