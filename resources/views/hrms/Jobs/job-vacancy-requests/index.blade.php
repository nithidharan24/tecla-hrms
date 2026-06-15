@extends('layouts.index')

@section('content')
<div class="content container-fluid mt-3">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-clipboard-list me-2"></i>Job Vacancy Requests
            </h5>
            <a href="{{ route('job-vacancy-requests.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Add Request
            </a>
        </div>
        <div class="card-body text-center py-5">
            <a href="{{ route('recruitment.index', ['tab' => 'job-requests']) }}" class="btn btn-primary">
                View Requests in Recruitment
            </a>
        </div>
    </div>
</div>
@endsection
