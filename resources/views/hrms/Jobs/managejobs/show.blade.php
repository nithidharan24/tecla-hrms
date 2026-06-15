@extends('layouts.index')

@section('content')
<style>
    :root {
        --orange: #F97316;
        --orange-hover: #EA6C0A;
        --orange-light: #FFF4EC;
        --white: #FFFFFF;
        --gray-bg: #F9FAFB;
        --gray-border: #E5E7EB;
        --gray-text: #6B7280;
        --dark-text: #1F2937;
    }

    .job-detail-page {
        background: var(--white);
        min-height: 100vh;
        padding: 24px 32px;
    }

    .page-header {
        margin-bottom: 24px;
    }

    .page-header h3 {
        color: var(--orange);
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 8px;
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .breadcrumb-item {
        font-size: 13px;
        color: var(--gray-text);
    }

    .breadcrumb-item a {
        color: var(--orange);
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        text-decoration: underline;
    }

    .breadcrumb-item.active {
        color: var(--gray-text);
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: var(--gray-text);
        padding-right: 8px;
    }

    .detail-card {
        background: var(--white);
        border: 1px solid var(--gray-border);
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .detail-card-header {
        background: linear-gradient(135deg, var(--orange) 0%, #FF8A3C 100%);
        padding: 16px 24px;
        border-bottom: none;
    }

    .detail-card-header h5 {
        color: var(--white);
        font-size: 16px;
        font-weight: 700;
        margin: 0;
    }

    .detail-card-body {
        padding: 24px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .info-item {
        padding: 12px;
        background: var(--gray-bg);
        border-radius: 10px;
        border-left: 3px solid var(--orange);
    }

    .info-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--gray-text);
        margin-bottom: 8px;
    }

    .info-value {
        font-size: 15px;
        font-weight: 700;
        color: var(--dark-text);
        word-break: break-word;
    }

    .info-value.large {
        font-size: 14px;
        font-weight: 500;
        line-height: 1.5;
    }

    .full-width {
        grid-column: 1 / -1;
    }

    .candidates-card {
        background: var(--white);
        border: 1px solid var(--gray-border);
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .candidates-card-header {
        background: var(--white);
        padding: 18px 24px;
        border-bottom: 1px solid var(--gray-border);
    }

    .candidates-card-header h5 {
        color: var(--orange);
        font-size: 16px;
        font-weight: 700;
        margin: 0;
    }

    .candidates-card-body {
        padding: 0;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
    }

    .custom-table thead th {
        background: var(--gray-bg);
        color: var(--orange);
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 16px;
        border-bottom: 1px solid var(--gray-border);
    }

    .custom-table tbody td {
        padding: 14px 16px;
        font-size: 14px;
        color: var(--dark-text);
        border-bottom: 1px solid var(--gray-border);
        vertical-align: middle;
    }

    .custom-table tbody tr:hover {
        background: var(--orange-light);
    }

    .custom-table tbody tr:last-child td {
        border-bottom: none;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .status-applied { background: #EFF6FF; color: #2563EB; }
    .status-shortlisted { background: #FEF3C7; color: #D97706; }
    .status-selected { background: #F0FDF4; color: #16A34A; }
    .status-rejected { background: #FEF2F2; color: #DC2626; }
    .status-interview_scheduled { background: #FFF4EC; color: var(--orange); }
    .status-interview_completed { background: #E0E7FF; color: #4F46E5; }

    .pagination-wrapper {
        padding: 20px 24px;
        border-top: 1px solid var(--gray-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .entries-info {
        font-size: 13px;
        color: var(--gray-text);
    }

    .pagination {
        margin: 0;
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .pagination .page-link {
        border: 1px solid var(--gray-border);
        border-radius: 8px;
        color: var(--dark-text);
        font-size: 13px;
        font-weight: 500;
        padding: 6px 12px;
        background: var(--white);
        transition: all 0.2s;
    }

    .pagination .page-link:hover {
        background: var(--orange-light);
        border-color: var(--orange);
        color: var(--orange);
    }

    .pagination .active .page-link {
        background: var(--orange);
        border-color: var(--orange);
        color: var(--white);
    }

    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: var(--gray-text);
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        color: var(--orange);
        opacity: 0.5;
    }

    .empty-state p {
        font-size: 14px;
        margin: 0;
    }

    @media (max-width: 992px) {
        .job-detail-page { padding: 20px; }
        .info-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    }

    @media (max-width: 640px) {
        .job-detail-page { padding: 16px; }
        .info-grid { grid-template-columns: 1fr; }
        .detail-card-body { padding: 16px; }
        .pagination-wrapper { flex-direction: column; text-align: center; }
    }
</style>

<div class="job-detail-page">
    <div class="page-header">
        <h3>{{ $job->job_title }}</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('recruitment.index', ['tab' => 'job-listings']) }}">Recruitment</a></li>
            <li class="breadcrumb-item active">Job Details</li>
        </ul>
    </div>

    <div class="detail-card">
        <div class="detail-card-header">
            <h5><i class="fas fa-briefcase me-2"></i>Job Details</h5>
        </div>
        <div class="detail-card-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-building me-1"></i> Department</div>
                    <div class="info-value">{{ $job->department }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-map-marker-alt me-1"></i> Location</div>
                    <div class="info-value">{{ $job->job_location }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-users me-1"></i> Vacancies</div>
                    <div class="info-value">{{ $job->vacancies }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-clock me-1"></i> Job Type</div>
                    <div class="info-value">{{ $job->job_type }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-flag-checkered me-1"></i> Status</div>
                    <div class="info-value">
                        <span class="status-badge status-{{ strtolower($job->status) }}" style="background: {{ $job->status == 'Open' ? '#F0FDF4' : ($job->status == 'Closed' ? '#FEF2F2' : '#FEF3C7') }}; color: {{ $job->status == 'Open' ? '#16A34A' : ($job->status == 'Closed' ? '#DC2626' : '#D97706') }}">
                            {{ $job->status }}
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-calendar-alt me-1"></i> Posted Date</div>
                    <div class="info-value">{{ $job->created_at ? \Carbon\Carbon::parse($job->created_at)->format('d-m-Y') : '-' }}</div>
                </div>
                <div class="info-item full-width">
                    <div class="info-label"><i class="fas fa-align-left me-1"></i> Description</div>
                    <div class="info-value large">{{ $job->description }}</div>
                </div>
                @if($job->screening_questions)
                <div class="info-item full-width">
                    <div class="info-label"><i class="fas fa-question-circle me-1"></i> Screening Questions</div>
                    <div class="info-value large">{{ $job->screening_questions }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="candidates-card">
        <div class="candidates-card-header">
            <h5><i class="fas fa-users me-2"></i>Candidates ({{ $candidates->total() }})</h5>
        </div>
        <div class="candidates-card-body">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user me-1"></i> Name</th>
                            <th><i class="fas fa-envelope me-1"></i> Email</th>
                            <th><i class="fas fa-phone me-1"></i> Phone</th>
                            <th><i class="fas fa-globe me-1"></i> Source</th>
                            <th><i class="fas fa-calendar-plus me-1"></i> Added Date</th>
                            <th><i class="fas fa-chart-line me-1"></i> Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($candidates as $candidate)
                            <tr>
                                <td><strong>{{ $candidate->first_name }} {{ $candidate->last_name }}</strong></td>
                                <td>{{ $candidate->email }}</td>
                                <td>{{ $candidate->phone }}</td>
                                <td>{{ $candidate->source ?? '-' }}</td>
                                <td>{{ $candidate->created_at ? \Carbon\Carbon::parse($candidate->created_at)->format('d-m-Y') : '-' }}</td>
                                <td>
                                    <span class="status-badge status-{{ str_replace('_', '', $candidate->status ?? 'applied') }}">
                                        {{ ucfirst(str_replace('_', ' ', $candidate->status ?? 'applied')) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="fas fa-user-friends"></i>
                                        <p>No candidates found for this job yet.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($candidates->count() > 0)
                <div class="pagination-wrapper">
                    <div class="entries-info">
                        <i class="fas fa-list me-1"></i>
                        Showing {{ $candidates->firstItem() }} to {{ $candidates->lastItem() }} of {{ $candidates->total() }} entries
                    </div>
                    <div>
                        {{ $candidates->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection