@extends('layouts.index')

@section('content')
<div class="container mt-5">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Search</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item">Search Results</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="row mt-4">
        <div class="col-12">
            <form action="" method="GET" class="d-flex">
                <input type="text" name="keyword" class="form-control me-2" placeholder="Search Projects or Clients" value="{{ old('keyword', $keyword) }}" required>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="row mt-4">
        <div class="col-12">
            <ul class="nav nav-tabs" id="searchTabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#" id="projects-tab" onclick="showSection('projects')">Projects</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="clients-tab" onclick="showSection('clients')">Clients</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Projects Section -->
    <div class="row mt-3" id="projects-section">
        @foreach($projects as $project)
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h4 class="card-title">
                        <a href="#" class="text-decoration-none">{{ $project->projectname }}</a>
                    </h4>
                    <p class="card-text text-muted flex-grow-1">
                        {{ Str::limit(strip_tags($project->description), 100, '...') }}
                    </p>
                    <div class="pro-deadline mb-2">
                        <small class="text-muted">Deadline: {{ \Carbon\Carbon::parse($project->enddate)->format('d M, Y') }}</small>
                    </div>
                    <div class="project-members mb-2">
                        <small>Assigned to: {{ $project->projectleader }}</small>
                    </div>
                    <div class="project-members mb-2">
                        <small>Team Members: {{ $project->team }}</small>
                    </div>
                    <div class="project-members mb-2">
                        <small>Client ID: {{ $project->client }}</small>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Clients Section -->
    <div class="row mt-3 d-none" id="clients-section">
        @foreach($clients as $client)
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body d-flex flex-column align-items-center">
                    <!-- Company Name -->
                    <h4 class="card-title">
                        {{ $client->company_name }}
                    </h4>

                    <!-- CEO/Contact Person -->
                    <p class="text-muted mb-1">
                        {{ $client->first_name }}
                    </p>
                    <p class="text-muted mb-3">
                        {{ $client->user_name }}
                    </p>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-center">
                        <a href="#" class="btn btn-outline-primary btn-sm me-2">Message</a>
                        <a href="#" class="btn btn-primary btn-sm">View Profile</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>

<!-- JavaScript to toggle between Projects and Clients -->
<script>
    function showSection(section) {
        // Hide both sections first
        document.getElementById('projects-section').classList.add('d-none');
        document.getElementById('clients-section').classList.add('d-none');
        
        // Show the selected section
        document.getElementById(section + '-section').classList.remove('d-none');
        
        // Remove 'active' class from all tabs
        document.getElementById('projects-tab').classList.remove('active');
        document.getElementById('clients-tab').classList.remove('active');
        
        // Add 'active' class to the selected tab
        document.getElementById(section + '-tab').classList.add('active');
    }
</script>

@endsection
