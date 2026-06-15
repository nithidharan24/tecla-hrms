@php
$permissions = App\Helpers\PermissionHelper::getPermissions('Recruitment Management');
@endphp
@extends('layouts.index')

@section('content')
<div class="content container-fluid mt-3">

<!-- RECRUITMENT MANAGEMENT TABS -->
<ul class="nav recruitment-tabs mb-0" role="tablist">
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#overview-tab">
            <i class="fas fa-chart-line me-2"></i>Overview
        </a>
    </li>
 
   

    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#job-requests-tab">
            <i class="fas fa-clipboard-list me-2"></i>Job Vacancy Requests
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#job-listings-tab">
            <i class="fas fa-briefcase me-2"></i>Job Listings
        </a>
    </li>
   
   
  
   

  
   
    {{-- <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#shortlist-tab">
            <i class="fas fa-list-check me-2"></i>Shortlisted
        </a>
    </li> --}}
  

  
    {{-- <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#onboarding-tab">
            <i class="fas fa-user-check me-2"></i>Onboarding
        </a>
    </li>
    



 
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#manage-resumes-tab">
            <i class="fas fa-file-alt me-2"></i>Manage Resumes
        </a>
    </li> --}}
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#add-resume-tab">
            <i class="fas fa-users me-2"></i>Candidates
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#offer-letter-tab">
            <i class="fas fa-envelope-open-text me-2"></i>Send Offer Letter
        </a>
    </li>
  
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#questions-experience-tab">
            <i class="fas fa-question-circle me-2"></i>Questions & Experience Management
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#activity-log-tab">
            <i class="fas fa-history me-2"></i>Activity Log
        </a>
    </li>

</ul>

    <div class="tabs-underline"></div>

    <!-- TAB CONTENT -->
    <div class="tab-content pt-4">
        
        <!-- OVERVIEW TAB -->
        <div class="tab-pane fade" id="overview-tab">
            <!-- Scoped styling for elite overview dashboard -->
            <style>
                .overview-dashboard .stat-card {
                    border: none;
                    border-radius: 16px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                }
                .overview-dashboard .stat-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
                }
                .overview-dashboard .chart-card {
                    border: none;
                    border-radius: 16px;
                    box-shadow: 0 8px 26px rgba(0, 0, 0, 0.02);
                    background: #ffffff;
                }
                .overview-dashboard .stage-badge {
                    font-size: 11px;
                    font-weight: 700;
                    padding: 6px 12px;
                    border-radius: 30px;
                }
                .overview-dashboard .pipeline-stage {
                    background: #f8fafc;
                    border: 1px solid #e2e8f0;
                    border-radius: 12px;
                    padding: 16px;
                    text-align: center;
                    position: relative;
                    flex: 1;
                    min-width: 120px;
                    margin: 5px;
                    transition: all 0.2s;
                }
                .overview-dashboard .pipeline-stage:hover {
                    background: #f1f5f9;
                    border-color: #cbd5e1;
                }
                .overview-dashboard .pipeline-container {
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: space-between;
                    margin: 0 -5px;
                }
            </style>

            <div class="overview-dashboard">
                <!-- Analytics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card h-100" style="background: #ffffff; border: 1px solid #e1e5ef; border-bottom: 4px solid #ff6b13;">
                            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-uppercase tracking-wider mb-1" style="font-size: 11px; font-weight: 700; color: #7b88aa;">Total Applicants</h6>
                                    <h2 class="mb-0 fw-bold" style="color: #ff6b13;">{{ $stats['total_candidates'] ?? 0 }}</h2>
                                </div>
                                <div class="rounded-3 p-3" style="background: #fff8f3;">
                                    <i class="fas fa-users fa-2x" style="color: #ff6b13;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card h-100" style="background: #ffffff; border: 1px solid #e1e5ef; border-bottom: 4px solid #ff6b13;">
                            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-uppercase tracking-wider mb-1" style="font-size: 11px; font-weight: 700; color: #7b88aa;">Selected Candidates</h6>
                                    <h2 class="mb-0 fw-bold" style="color: #ff6b13;">{{ $stats['selected'] ?? 0 }}</h2>
                                </div>
                                <div class="rounded-3 p-3" style="background: #fff8f3;">
                                    <i class="fas fa-user-check fa-2x" style="color: #ff6b13;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card h-100" style="background: #ffffff; border: 1px solid #e1e5ef; border-bottom: 4px solid #ff6b13;">
                            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-uppercase tracking-wider mb-1" style="font-size: 11px; font-weight: 700; color: #7b88aa;">Rejected Candidates</h6>
                                    <h2 class="mb-0 fw-bold" style="color: #ff6b13;">{{ $stats['rejected'] ?? 0 }}</h2>
                                </div>
                                <div class="rounded-3 p-3" style="background: #fff8f3;">
                                    <i class="fas fa-user-times fa-2x" style="color: #ff6b13;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card h-100" style="background: #ffffff; border: 1px solid #e1e5ef; border-bottom: 4px solid #ff6b13;">
                            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-uppercase tracking-wider mb-1" style="font-size: 11px; font-weight: 700; color: #7b88aa;">Active Jobs & Vacancies</h6>
                                    <h2 class="mb-0 fw-bold" style="color: #ff6b13;">{{ $stats['active_jobs'] ?? 0 }} <span style="font-size: 12px; font-weight: normal; color: #7b88aa;">({{ $stats['total_vacancies'] ?? 0 }} openings)</span></h2>
                                </div>
                                <div class="rounded-3 p-3" style="background: #fff8f3;">
                                    <i class="fas fa-briefcase fa-2x" style="color: #ff6b13;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recruitment Pipeline Levels of Rounds -->
                <div class="card chart-card mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 16px;">
                            <i class="fas fa-network-wired text-primary me-2"></i>Recruitment & Interview Round Stages
                        </h5>
                        <p class="text-muted mb-0" style="font-size: 12px;">Candidate volume tracking across active recruitment stages and validation workflows</p>
                    </div>
                    <div class="card-body px-4 pb-4 pt-3">
                        <div class="pipeline-container">
                            <div class="pipeline-stage">
                                <div class="text-primary mb-2"><i class="fas fa-file-signature fa-lg"></i></div>
                                <h4 class="fw-bold mb-1 text-dark">{{ $stats['total_candidates'] ?? 0 }}</h4>
                                <div class="text-muted" style="font-size: 12px; font-weight: 600;">Applied</div>
                            </div>
                            <div class="pipeline-stage">
                                <div class="text-warning mb-2"><i class="fas fa-user-circle fa-lg"></i></div>
                                <h4 class="fw-bold mb-1 text-dark">{{ $stats['hr_round'] ?? 0 }}</h4>
                                <div class="text-muted" style="font-size: 12px; font-weight: 600;">HR Round</div>
                            </div>
                            <div class="pipeline-stage">
                                <div class="text-info mb-2"><i class="fas fa-laptop-code fa-lg"></i></div>
                                <h4 class="fw-bold mb-1 text-dark">{{ $stats['technical_round'] ?? 0 }}</h4>
                                <div class="text-muted" style="font-size: 12px; font-weight: 600;">Technical Round</div>
                            </div>
                            <div class="pipeline-stage">
                                <div class="text-secondary mb-2"><i class="fas fa-users-cog fa-lg"></i></div>
                                <h4 class="fw-bold mb-1 text-dark">{{ $stats['manager_round'] ?? 0 }}</h4>
                                <div class="text-muted" style="font-size: 12px; font-weight: 600;">Manager Round</div>
                            </div>
                            <div class="pipeline-stage">
                                <div class="text-dark mb-2"><i class="fas fa-trophy fa-lg"></i></div>
                                <h4 class="fw-bold mb-1 text-dark">{{ $stats['final_round'] ?? 0 }}</h4>
                                <div class="text-muted" style="font-size: 12px; font-weight: 600;">Final Round</div>
                            </div>
                            <div class="pipeline-stage" style="border-left: 3px solid #10b981;">
                                <div class="text-success mb-2"><i class="fas fa-check-double fa-lg"></i></div>
                                <h4 class="fw-bold mb-1 text-dark">{{ $stats['selected'] ?? 0 }}</h4>
                                <div class="text-muted" style="font-size: 12px; font-weight: 600;">Selected</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Graphs Section -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card chart-card h-100">
                            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0 text-dark" style="font-size: 16px;">
                                    <i class="fas fa-chart-area text-primary me-2"></i>Candidate Applications Trend (Last 6 Months)
                                </h5>
                            </div>
                            <div class="card-body p-4" style="position: relative; height: 280px;">
                                <canvas id="applicationTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card chart-card h-100">
                            <div class="card-header bg-transparent border-0 pt-4 px-4">
                                <h5 class="fw-bold mb-0 text-dark" style="font-size: 16px;">
                                    <i class="fas fa-chart-pie text-primary me-2"></i>Rounds Distribution
                                </h5>
                            </div>
                            <div class="card-body p-4 d-flex align-items-center justify-content-center" style="position: relative; height: 280px;">
                                <canvas id="roundDistributionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Applicants -->
                <div class="card chart-card mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 16px;">
                            <i class="fas fa-clock text-primary me-2"></i>Recent Applicants
                        </h5>
                        <button class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="document.querySelector('a[href=\'#add-resume-tab\']').click();">
                            View All Candidates
                        </button>
                    </div>
                    <div class="card-body px-4 pb-4 pt-2">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr class="text-muted" style="font-size: 12px; border-bottom: 2px solid #f1f5f9;">
                                        <th>Candidate Name</th>
                                        <th>Email</th>
                                        <th>Position Applied</th>
                                        <th>Experience</th>
                                        <th>Status</th>
                                        <th class="text-end">Applied Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_candidates'] ?? [] as $candidate)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark" style="font-size: 14px;">{{ $candidate->first_name }} {{ $candidate->last_name }}</span>
                                            </td>
                                            <td class="text-muted" style="font-size: 13px;">{{ $candidate->email }}</td>
                                            <td>
                                                <span class="badge bg-primary-light">{{ $candidate->position_applied }}</span>
                                            </td>
                                            <td class="text-dark" style="font-size: 13px;">{{ $candidate->experience_years }} Years</td>
                                            <td>
                                                <span class="badge 
                                                    @if($candidate->status == 'selected') bg-success
                                                    @elseif($candidate->status == 'rejected') bg-danger
                                                    @elseif($candidate->status == 'shortlisted') bg-warning
                                                    @elseif($candidate->status == 'interview_scheduled' || $candidate->status == 'interview_completed') bg-info
                                                    @else bg-secondary @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $candidate->status)) }}
                                                </span>
                                            </td>
                                            <td class="text-end text-muted" style="font-size: 13px;">{{ $candidate->created_at ? $candidate->created_at->format('d M Y, h:i A') : 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">No recent candidates found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import Chart.js and script initiation -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    // Application Trend Area/Line Chart
                    const trendCtx = document.getElementById('applicationTrendChart').getContext('2d');
                    const trendLabels = {!! json_encode(array_keys($stats['monthly_trend'] ?? [])) !!};
                    const trendData = {!! json_encode(array_values($stats['monthly_trend'] ?? [])) !!};
                    
                    const gradient = trendCtx.createLinearGradient(0, 0, 0, 250);
                    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
                    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

                    new Chart(trendCtx, {
                        type: 'line',
                        data: {
                            labels: trendLabels,
                            datasets: [{
                                label: 'Applicants',
                                data: trendData,
                                borderColor: '#3b82f6',
                                backgroundColor: gradient,
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#3b82f6',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    grid: { color: '#f1f5f9' },
                                    ticks: { color: '#64748b', font: { family: "'DM Sans', sans-serif", size: 11 } }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { color: '#64748b', font: { family: "'DM Sans', sans-serif", size: 11 } }
                                }
                            }
                        }
                    });

                    // Doughnut Chart for Rounds Distribution
                    const donutCtx = document.getElementById('roundDistributionChart').getContext('2d');
                    new Chart(donutCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['HR Round', 'Technical Round', 'Manager Round', 'Final Round', 'Selected', 'Rejected'],
                            datasets: [{
                                data: [
                                    {{ $stats['hr_round'] ?? 0 }},
                                    {{ $stats['technical_round'] ?? 0 }},
                                    {{ $stats['manager_round'] ?? 0 }},
                                    {{ $stats['final_round'] ?? 0 }},
                                    {{ $stats['selected'] ?? 0 }},
                                    {{ $stats['rejected'] ?? 0 }}
                                ],
                                backgroundColor: ['#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899', '#10b981', '#ef4444'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 10,
                                        padding: 15,
                                        font: { family: "'DM Sans', sans-serif", size: 11 },
                                        color: '#64748b'
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
        </div>

        <!-- ADD RESUME TAB -->
        <div class="tab-pane fade" id="add-resume-tab">
            <!-- Include the add-resume content directly -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        @if(isset($permissions) && $permissions->can_create)
                        <i class="fas fa-users me-2"></i>Candidates
                        <a href="{{ route('add-resume.create') }}" class="btn btn-primary btn-sm float-end">
                            <i class="fas fa-plus me-1"></i>Add New Candidate
                        </a>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('recruitment.index') }}" class="mb-4">
                        <input type="hidden" name="tab" value="add-resume">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Search by name, email, position..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="position" class="form-control">
                                    <option value="">All Positions</option>
                                    @foreach($positions as $position)
                                        <option value="{{ $position->position_applied }}" {{ request('position') == $position->position_applied ? 'selected' : '' }}>
                                            {{ $position->position_applied }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="applied" {{ request('status') == 'applied' ? 'selected' : '' }}>Applied</option>
                                    <option value="shortlisted" {{ request('status') == 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                                    <option value="interview_scheduled" {{ request('status') == 'interview_scheduled' ? 'selected' : '' }}>Interview Scheduled</option>
                                    <option value="interview_completed" {{ request('status') == 'interview_completed' ? 'selected' : '' }}>Interview Completed</option>
                                    <option value="selected" {{ request('status') == 'selected' ? 'selected' : '' }}>Selected</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="experience" class="form-control" placeholder="Min Experience (years)" value="{{ request('experience') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('recruitment.index') }}?tab=add-resume" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <!-- Candidates Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Position</th>
                                
                                    <th>Experience</th>
                                  
                                       <th>Interview Status</th>
                                   
                                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                        <th class="text-end">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody> 
                                @forelse($candidates as $index => $candidate)
                                    <tr>
                                        <td>{{ $candidates->firstItem() + $index }}</td>
                                        <td>{{ $candidate->first_name }} {{ $candidate->last_name }}</td>
                                        <td>{{ $candidate->email }}</td>
                                        <td>{{ $candidate->phone }}</td>
                                        <td>{{ $candidate->position_applied }}</td>
                                     
                                        <td>{{ $candidate->experience_years }} years</td>
                                     
                                        <td>
                                            <span class="badge 
                                                @if($candidate->status == 'selected') bg-success
                                                @elseif($candidate->status == 'rejected') bg-danger
                                                @elseif($candidate->status == 'shortlisted') bg-warning
                                                @elseif($candidate->status == 'interview_scheduled' || $candidate->status == 'interview_completed') bg-info
                                                @else bg-secondary @endif">
                                                {{ ucfirst(str_replace('_', ' ', $candidate->status)) }}
                                            </span>
                                        </td>
                                        @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                   
                                                        <a href="{{ route('add-resume.view-resume', $candidate->id) }}" 
                                                           class="btn btn-sm btn-primary" 
                                                           target="_blank"
                                                           title="View Resume">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                   
                                                    
                                                    @if(isset($permissions) && $permissions->can_edit)
                                                        <a href="{{ route('add-resume.edit', $candidate->id) }}" 
                                                           class="btn btn-sm btn-warning"
                                                           title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="{{ route('shortlist.schedule-interview', $candidate->id) }}"
                                                           class="btn btn-sm btn-secondary"
                                                           title="Schedule Interview">
                                                            <i class="fas fa-calendar-alt"></i>
                                                        </a>
                                                        <a href="{{ route('interview-feedback.candidate-summary', $candidate->id) }}"
                                                           class="btn btn-sm btn-success"
                                                           title="View Evaluation">
                                                            <i class="fas fa-chart-line"></i>
                                                        </a>

                                                        @if($candidate->status == 'interview_completed')
                                                            <form action="{{ route('recruitment.select-candidate', $candidate->id) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-success text-white" style="background-color: #28a745 !important; border-color: #28a745 !important;" title="Select Candidate" onclick="return confirm('Are you sure you want to select this candidate?')">
                                                                    <i class="fas fa-check text-white"></i>
                                                                </button>
                                                            </form>
                                                            <button type="button" class="btn btn-sm btn-danger text-white btn-reject-candidate" style="background-color: #dc3545 !important; border-color: #dc3545 !important;" data-id="{{ $candidate->id }}" data-name="{{ $candidate->first_name }} {{ $candidate->last_name }}" title="Reject Candidate">
                                                                <i class="fas fa-times text-white"></i>
                                                            </button>
                                                        @endif
                                                    @endif
                                                                                                        
                                                    @if(isset($permissions) && $permissions->can_delete)
                                                        <form action="{{ route('add-resume.destroy', $candidate->id) }}" 
                                                              method="POST" 
                                                              style="display:inline;"
                                                              onsubmit="return confirm('Are you sure you want to delete this candidate?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" class="text-center">No candidates found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing {{ $candidates->firstItem() }} to {{ $candidates->lastItem() }} of {{ $candidates->total() }} entries
                        </div>
                        <div>
                            {{ $candidates->appends(['tab' => 'add-resume'])->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- SEND OFFER LETTER TAB -->
        <div class="tab-pane fade" id="offer-letter-tab">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-envelope-open-text me-2"></i>Send Offer Letter
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('recruitment.index') }}" class="mb-4">
                        <input type="hidden" name="tab" value="offer-letter">
                        <div class="row g-3">
                            <div class="col-md-9 col-12">
                                <input type="text" name="selected_search" class="form-control" style="width:100% !important; min-width: 100%;" placeholder="Search selected candidates by name, email, position..." value="{{ request('selected_search') }}">
                            </div>
                            <div class="col-md-3 col-12">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('recruitment.index') }}?tab=offer-letter" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Position</th>
                                    <th>Experience</th>
                                    <th>Salary Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($selectedCandidates as $index => $candidate)
                                    <tr>
                                        <td>{{ $selectedCandidates->firstItem() + $index }}</td>
                                        <td>{{ $candidate->first_name }} {{ $candidate->last_name }}</td>
                                        <td>{{ $candidate->email }}</td>
                                        <td>{{ $candidate->position_applied }}</td>
                                        <td>{{ $candidate->experience_years }} years</td>
                                        <td>
                                            @if($candidate->salaryStructure)
                                                <span class="badge bg-success">Set (₹{{ number_format($candidate->salaryStructure->gross_salary, 2) }}/mo)</span>
                                            @else
                                                <span class="badge bg-danger">Not Set</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group" role="group">
                                                @if(!$candidate->salaryStructure)
                                                    <a href="{{ route('recruitment.set-salary', $candidate->id) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center justify-content-center" style="width: auto; height: 36px; padding: 0 10px !important;" title="Set Salary">
                                                        <i class="fas fa-coins me-1"></i> Set Salary
                                                    </a>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-info btn-view-salary d-inline-flex align-items-center justify-content-center" style="width: auto; height: 36px; padding: 0 10px !important;" data-id="{{ $candidate->id }}" title="View Salary Breakdown">
                                                        <i class="fas fa-list me-1"></i> View Salary
                                                    </button>
                                                    <a href="{{ route('recruitment.set-salary', $candidate->id) }}" class="btn btn-sm btn-warning d-inline-flex align-items-center justify-content-center" style="width: auto; height: 36px; padding: 0 10px !important;" title="Edit Salary">
                                                        <i class="fas fa-edit me-1"></i> Edit Salary
                                                    </a>
                                                    
                                                    @if($candidate->salaryStructure->offer_letter_sent)
                                                        <span class="badge bg-success py-2 px-3" style="font-size: 13px;"><i class="fas fa-check-circle me-1"></i> Offer Sent</span>
                                                    @else
                                                        <form action="{{ route('recruitment.send-offer-letter', $candidate->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Generate and email offer letter to {{ $candidate->first_name }}?')">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success d-inline-flex align-items-center justify-content-center" style="width: auto; height: 36px; padding: 0 10px !important; background-color: #28a745 !important; border-color: #28a745 !important; color: white !important;" title="Send Offer Letter">
                                                                <i class="fas fa-paper-plane me-1 text-white"></i> Send Offer
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No selected candidates found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing {{ $selectedCandidates->firstItem() ?? 0 }} to {{ $selectedCandidates->lastItem() ?? 0 }} of {{ $selectedCandidates->total() ?? 0 }} entries
                        </div>
                        <div>
                            {{ $selectedCandidates->appends(['tab' => 'offer-letter', 'selected_search' => request('selected_search')])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MANAGE RESUMES TAB -->
        <div class="tab-pane fade" id="manage-resumes-tab">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt me-2"></i>Job Applications / Manage Resumes
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Redirect to the standalone manage resumes page -->
                    <div class="text-center py-5">
                        <i class="fas fa-file-alt fa-3x text-primary mb-3"></i>
                        <h4>Job Applications Management</h4>
                        <p class="text-muted">Manage all job applications and resumes in one place.</p>
                        <a href="{{ route('resume.index') }}" class="btn btn-primary">
                            <i class="fas fa-external-link-alt me-2"></i>Go to Job Applications
                        </a>
                    </div>
                </div>
            </div>
        </div>
 
        <!-- SHORTLIST TAB -->
        <div class="tab-pane fade" id="shortlist-tab">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list-check me-2"></i>Shortlisted Candidates
                        </h5>
                        @php
                            $shortlistPermissions = App\Helpers\PermissionHelper::getPermissions('Shortlist Candidates');
                        @endphp
                    </div>
                </div>
                <div class="card-body">
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Filters Card -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Filters</h5>
                            <form id="shortlist-filter-form" method="GET" action="{{ route('recruitment.index') }}">
                                <input type="hidden" name="tab" value="shortlist">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Search</label>
                                            <input type="text" class="form-control" name="shortlist_search" 
                                                   value="{{ request('shortlist_search') }}" 
                                                   placeholder="Name, Email, Position">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Position</label>
                                            <select class="form-control" name="shortlist_position">
                                                <option value="">All Positions</option>
                                                @isset($shortlistPositions)
                                                    @foreach($shortlistPositions as $position)
                                                        <option value="{{ $position->position_applied }}" {{ request('shortlist_position') == $position->position_applied ? 'selected' : '' }}>
                                                            {{ $position->position_applied }}
                                                        </option>
                                                    @endforeach
                                                @endisset
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Candidate Status</label>
                                            <select class="form-control" name="shortlist_status">
                                                <option value="">All Status</option>
                                                <option value="shortlisted" {{ request('shortlist_status') == 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                                                <option value="telephonic_scheduled" {{ request('shortlist_status') == 'telephonic_scheduled' ? 'selected' : '' }}>Telephonic Scheduled</option>
                                                <option value="telephonic_completed" {{ request('shortlist_status') == 'telephonic_completed' ? 'selected' : '' }}>Telephonic Completed</option>
                                                <option value="interview_scheduled" {{ request('shortlist_status') == 'interview_scheduled' ? 'selected' : '' }}>Interview Scheduled</option>
                                                <option value="interview_completed" {{ request('shortlist_status') == 'interview_completed' ? 'selected' : '' }}>Interview Completed</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Interview Status</label>
                                            <select class="form-control" name="shortlist_interview_status">
                                                <option value="">All Interview Status</option>
                                                <option value="scheduled" {{ request('shortlist_interview_status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                                <option value="completed" {{ request('shortlist_interview_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="cancelled" {{ request('shortlist_interview_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                <option value="rescheduled" {{ request('shortlist_interview_status') == 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                                                <option value="no_interview" {{ request('shortlist_interview_status') == 'no_interview' ? 'selected' : '' }}>No Interview</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>From Date</label>
                                                    <input type="date" class="form-control" name="shortlist_start_date" 
                                                           value="{{ request('shortlist_start_date') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>To Date</label>
                                                    <input type="date" class="form-control" name="shortlist_end_date" 
                                                           value="{{ request('shortlist_end_date') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-secondary" onclick="resetShortlistFilters()">
                                                <i class="fa-solid fa-rotate-right"></i> Reset
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa-solid fa-filter"></i> Apply Filters
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /Filters Card -->

                    <!-- Shortlisted Candidates Table -->
                    <div class="table-responsive">
                        <table id="shortlist-table" class="table table-striped custom-table">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Candidate Name</th>
                                    <th>Email</th>
                                    <th>Position</th>
                                    <th>Status</th>
                                    <th>Interview Status</th>
                                    <th>Resume</th>
                                    @if(isset($shortlistPermissions) && ($shortlistPermissions->can_edit || $shortlistPermissions->can_delete))
                                        <th class="text-end">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($shortlistedCandidates as $index => $candidate)
                                <tr>
                                    <td data-label="ID">{{ $shortlistedCandidates->firstItem() + $index }}</td>
                                    <td data-label="Full Name">
                                        <strong>{{ $candidate->first_name }} {{ $candidate->last_name }}</strong>
                                    </td>
                                    <td data-label="Email">{{ $candidate->email }}</td>
                                    <td data-label="Position Applied">{{ $candidate->position_applied }}</td>
                                    <td data-label="Status">
                                        <span class="badge
                                            @if($candidate->status == 'shortlisted') bg-info
                                            @elseif($candidate->status == 'telephonic_scheduled') bg-warning
                                            @elseif($candidate->status == 'telephonic_completed') bg-primary
                                            @elseif($candidate->status == 'interview_scheduled') bg-secondary
                                            @elseif($candidate->status == 'interview_completed') bg-success
                                            @endif">
                                            {{ ucwords(str_replace('_', ' ', $candidate->status)) }}
                                        </span>
                                    </td>
                                    <td data-label="Latest Interview">
                                        @if($candidate->interviews->count() > 0)
                                            @php $latestInterview = $candidate->interviews->last(); @endphp
                                            <span class="badge
                                                @if($latestInterview->status == 'scheduled') bg-warning
                                                @elseif($latestInterview->status == 'completed') bg-success
                                                @elseif($latestInterview->status == 'cancelled') bg-danger
                                                @elseif($latestInterview->status == 'rescheduled') bg-info
                                                @endif">
                                                {{ ucfirst($latestInterview->status) }}
                                            </span><br>
                                            <small>{{ $latestInterview->interview_datetime->format('M d, Y H:i') }}</small>
                                        @else
                                            <span class="text-muted">No Interview</span>
                                        @endif
                                    </td>
                                    <td data-label="Resume">
                                        @if($candidate->resume_path)
                                            <div class="od-inline-actions">
                                                <a href="{{ route('shortlist.view-resume', $candidate->id) }}" target="_blank" class="od-icon-btn" title="View Resume">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                <a href="{{ route('shortlist.view-resume', ['id' => $candidate->id, 'download' => 1]) }}" class="od-icon-btn" title="Download Resume">
                                                    <i class="fa-solid fa-download"></i>
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-muted">No Resume</span>
                                        @endif
                                    </td>
                                    @if(isset($shortlistPermissions) && ($shortlistPermissions->can_edit || $shortlistPermissions->can_delete))
                                    <td data-label="Actions" class="text-end">
                                        <div class="dropdown dropdown-action">
                                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="material-icons">more_vert</i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="{{ route('shortlist.schedule-interview', $candidate->id) }}">
                                                    <i class="fa-solid fa-calendar-days m-r-5"></i> Schedule Interview
                                                </a>
                                                @if($candidate->interviews->count() > 0)
                                                    <a class="dropdown-item" href="javascript:void(0)" 
                                                       onclick="updateInterviewStatus({{ $candidate->interviews->last()->id }})">
                                                        <i class="fa-solid fa-pen m-r-5"></i> Update Interview
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($shortlistedCandidates->count() > 0)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $shortlistedCandidates->firstItem() }} to {{ $shortlistedCandidates->lastItem() }} of {{ $shortlistedCandidates->total() }} entries
                                </div>
                                <div>
                                    {{ $shortlistedCandidates->appends(['tab' => 'shortlist'])->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>

        <!-- ONBOARDING TAB -->
        <div class="tab-pane fade" id="onboarding-tab">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-check me-2"></i>Onboarding Records
                        </h5>
                        @if(isset($permissions) && $permissions->can_create)
                        <a href="{{ route('onboarding.create') }}" class="btn add-btn">
                            <i class="fa-solid fa-plus"></i> Add New Onboarding
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Search and Filter Form -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Filters</h5>
                            <form method="GET" action="{{ route('recruitment.index') }}">
                                <input type="hidden" name="tab" value="onboarding">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Search</label>
                                            <input type="text" name="onboarding_search" class="form-control" placeholder="Search by name, email, mobile..." value="{{ request('onboarding_search') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Gender</label>
                                            <select name="onboarding_gender" class="form-control">
                                                <option value="">All Genders</option>
                                                <option value="Male" {{ request('onboarding_gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ request('onboarding_gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                                <option value="Other" {{ request('onboarding_gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Entries</label>
                                            <select name="onboarding_per_page" class="form-control" onchange="this.form.submit()">
                                                <option value="10" {{ request('onboarding_per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                                <option value="25" {{ request('onboarding_per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                                                <option value="50" {{ request('onboarding_per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                                                <option value="100" {{ request('onboarding_per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <div class="btn-group w-100">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa-solid fa-filter"></i> Apply Filters
                                            </button>
                                            <a href="{{ route('recruitment.index') }}?tab=onboarding" class="btn btn-secondary">
                                                <i class="fa-solid fa-rotate-right"></i> Reset
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Onboarding Records Table -->
                    <div class="table-responsive">
                        <table class="table table-striped custom-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Gender</th>
                                    <th>Emergency Contact</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Fetch onboarding data - you'll need to pass this from controller
                                    $onboardings = $onboardings ?? collect();
                                @endphp
                                
                                @forelse ($onboardings as $onboarding)
                                <tr>
                                    <td data-label="Full Name" class="high">{{ $onboarding->full_name }}</td>
                                    <td data-label="Gender">{{ $onboarding->gender }}</td>
                                    <td data-label="Emergency Contact" class="od-chip-highlight">
                                        <span>{{ $onboarding->emergency_contact_name ?? 'N/A' }}</span>
                                    </td>
                                    <td data-label="Email">{{ $onboarding->personal_email_id }}</td>
                                    <td data-label="Mobile">{{ $onboarding->mobile_number }}</td>
                                    <td data-label="Actions" class="text-end">
                                        <div class="dropdown dropdown-action">
                                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="material-icons">more_vert</i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="{{ route('onboarding.show', $onboarding->id) }}">
                                                    <i class="fa-solid fa-eye m-r-5"></i> View
                                                </a>
                                                @if(isset($permissions) && $permissions->can_edit)
                                                <a class="dropdown-item" href="{{ route('onboarding.edit', $onboarding->id) }}">
                                                    <i class="fa-solid fa-pen m-r-5"></i> Edit
                                                </a>
                                                @endif
                                               
                                                <a class="dropdown-item" href="{{ route('onboarding.downloadPdf', $onboarding->id) }}">
                                                    <i class="fa-solid fa-file-pdf m-r-5"></i> PDF
                                                </a>
                                                
                                                @if(isset($permissions) && $permissions->can_delete)
                                                <form action="{{ route('onboarding.destroy', $onboarding->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fa-solid fa-trash m-r-5"></i> Delete
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-user-check fa-2x mb-3"></i><br>
                                        No onboarding records found.
                                        <p class="mt-2">
                                            @if(isset($permissions) && $permissions->can_create)
                                            <a href="{{ route('onboarding.create') }}" class="btn btn-sm btn-primary">
                                                <i class="fa-solid fa-plus"></i> Add New Onboarding
                                            </a>
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($onboardings->count() > 0)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $onboardings->firstItem() ?? 0 }} to {{ $onboardings->lastItem() ?? 0 }} of {{ $onboardings->total() ?? 0 }} entries
                                </div>
                                <div>
                                    {{ $onboardings->appends(['tab' => 'onboarding'])->links() ?? '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>

        <!-- JOB LISTINGS TAB -->
        <div class="tab-pane fade" id="job-listings-tab">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-briefcase me-2"></i>Job Listings
                        </h5>
                        @if(isset($permissions) && $permissions->can_create)
                            <a href="{{ route('managejobs.create') }}" class="btn add-btn">
                                <i class="fa-solid fa-plus"></i> Add Job
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- Job Filters -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Filters</h5>
                            <form id="job-filter-form" method="GET" action="{{ route('recruitment.index') }}">
                                <input type="hidden" name="tab" value="job-listings">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Search</label>
                                            <input type="text" class="form-control" name="job_search" 
                                                   value="{{ request('job_search') }}" 
                                                   placeholder="Job Title, Department, Location">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Department</label>
                                            <select class="form-control" name="job_department">
                                                <option value="">All Departments</option>
                                                @foreach($departments as $dept)
                                                    <option value="{{ $dept->department }}" {{ request('job_department') == $dept->department ? 'selected' : '' }}>
                                                        {{ $dept->department }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Job Type</label>
                                            <select class="form-control" name="job_type">
                                                <option value="">All Types</option>
                                                <option value="Full Time" {{ request('job_type') == 'Full Time' ? 'selected' : '' }}>Full Time</option>
                                                <option value="Part Time" {{ request('job_type') == 'Part Time' ? 'selected' : '' }}>Part Time</option>
                                                <option value="Contract" {{ request('job_type') == 'Contract' ? 'selected' : '' }}>Contract</option>
                                                <option value="Remote" {{ request('job_type') == 'Remote' ? 'selected' : '' }}>Remote</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select class="form-control" name="job_status">
                                                <option value="">All Status</option>
                                                <option value="Open" {{ request('job_status') == 'Open' ? 'selected' : '' }}>Open</option>
                                                <option value="Closed" {{ request('job_status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                                                <option value="Cancelled" {{ request('job_status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>From Date</label>
                                                    <input type="date" class="form-control" name="job_start_date" 
                                                           value="{{ request('job_start_date') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>To Date</label>
                                                    <input type="date" class="form-control" name="job_end_date" 
                                                           value="{{ request('job_end_date') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-secondary" onclick="resetJobFilters()">
                                                <i class="fa-solid fa-rotate-right"></i> Reset
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa-solid fa-filter"></i> Apply Filters
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Jobs Table -->
                    <div class="table-responsive">
                        <table id="job-table" class="table table-striped custom-table">
                            <thead>
                                <tr>
                                    <th>Job Title</th>
                                    <th>Department</th>
                                    <th>Candidates Count</th>
                                    <th>New Today</th>
                                    <th>Posted Date</th>
                                    <th>Status</th>
                                    @if(isset($jobPermissions) && ($jobPermissions->can_edit || $jobPermissions->can_delete))
                                        <th class="text-end">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($manageJobs as $job)
                                <tr>
                                    <td>
                                        <a href="{{ route('managejobs.show', $job->id) }}">
                                            <strong>{{ $job->job_title }}</strong>
                                        </a>
                                    </td>
                                 <td>{{ $job->department_name ?? $job->department }}</td>
                                    <td>{{ $job->candidates_count ?? 0 }}</td>
                                    <td>{{ $job->new_candidates_today_count ?? 0 }}</td>
                                    <td>
                                        {{ $job->created_at ? \Carbon\Carbon::parse($job->created_at)->format('d-m-Y') : '-' }}
                                    </td>
                                    <td>
                                        <span class="badge badge-pill bg-{{ $job->status == 'Open' ? 'success' : ($job->status == 'Closed' ? 'danger' : 'secondary') }}-light">
                                            {{ $job->status }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="od-inline-actions">
                                            @if(isset($permissions) && $permissions->can_edit)
                                            <a class="od-icon-btn text-warning" href="{{ route('managejobs.edit', $job->id) }}" title="Edit">
                                                <i class="fa-solid fa-pencil"></i>
                                            </a>
                                            @endif
                                            @if(isset($permissions) && $permissions->can_delete)
                                            <a class="od-icon-btn text-danger delete-job-btn" href="#" data-id="{{ $job->id }}" title="Delete">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </a>
                                            @endif
                                        </div>
                                        
                                        <form id="delete-form-{{ $job->id }}" action="{{ route('managejobs.destroy', $job->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fa-solid fa-briefcase fa-2x mb-3"></i><br>
                                        No jobs found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Jobs Pagination -->
                    @if($manageJobs->count() > 0)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $manageJobs->firstItem() }} to {{ $manageJobs->lastItem() }} of {{ $manageJobs->total() }} entries
                                </div>
                                <div>
                                    {{ $manageJobs->appends(['tab' => 'job-listings'])->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
 
        <!-- JOB REQUESTS TAB -->
        <div class="tab-pane fade" id="job-requests-tab">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>Job Vacancy Requests
                        </h5>
                        @if(isset($permissions) && $permissions->can_create)
                            <a href="{{ route('job-vacancy-requests.create') }}" class="btn add-btn">
                                <i class="fa-solid fa-plus"></i> Add Request
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Filters</h5>
                            <form id="job-request-filter-form" method="GET" action="{{ route('recruitment.index') }}">
                                <input type="hidden" name="tab" value="job-requests">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Search</label>
                                            <input type="text" class="form-control" name="job_request_search"
                                                   value="{{ request('job_request_search') }}"
                                                   placeholder="Job Title, Department, Location">
                                        </div>
                                    </div> 
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Manager Approval</label>
                                            <select class="form-control" name="job_request_status">
                                                <option value="">All Status</option>
                                                <option value="pending" {{ request('job_request_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="approved" {{ request('job_request_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                                <option value="rejected" {{ request('job_request_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>HR Approval</label>
                                            <select class="form-control" name="job_request_hr_status">
                                                <option value="">All HR Status</option>
                                                <option value="pending" {{ request('job_request_hr_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="approved" {{ request('job_request_hr_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                                <option value="rejected" {{ request('job_request_hr_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end justify-content-end gap-2">
                                        <button type="button" class="btn btn-secondary" onclick="resetJobRequestFilters()">
                                            <i class="fa-solid fa-rotate-right"></i>
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa-solid fa-filter"></i> Filter
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="job-requests-table" class="table table-striped custom-table">
                            <thead>
                                <tr>
                                    <th>Job Title</th>
                                    <th>Department</th>
                                    <th>Location</th>
                                    <th>Vacancies</th>
                                    <th>Start Date</th>
                                    <th>Manager Approval</th>
                                    <th>HR Approval</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($jobVacancyRequests as $requestItem)
                                    <tr>
                                        <td><strong>{{ $requestItem->job_title }}</strong></td>
                                     <td>{{ $requestItem->department_name ?? $requestItem->department }}</td>
                                        <td>{{ $requestItem->job_location }}</td>
                                        <td>{{ $requestItem->vacancies }}</td>
                                        <td>{{ $requestItem->start_date ? \Carbon\Carbon::parse($requestItem->start_date)->format('d-m-Y') : '-' }}</td>
                                        <td>
                                            <span class="badge badge-pill bg-{{ $requestItem->approval_status == 'approved' ? 'success' : ($requestItem->approval_status == 'rejected' ? 'danger' : 'warning') }}-light">
                                                {{ ucfirst($requestItem->approval_status ?? 'pending') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-pill bg-{{ $requestItem->hr_approval_status == 'approved' ? 'success' : ($requestItem->hr_approval_status == 'rejected' ? 'danger' : 'warning') }}-light">
                                                {{ ucfirst($requestItem->hr_approval_status ?? 'pending') }}
                                            </span>
                                            @if($requestItem->converted_job_id)
                                                <div class="small text-muted mt-1">Job #{{ $requestItem->converted_job_id }}</div>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="od-inline-actions">
                                                @if(($requestItem->hr_approval_status ?? 'pending') == 'pending')
                                                    @if(isset($permissions) && $permissions->can_edit)
                                                        <a class="od-icon-btn text-warning" href="{{ route('job-vacancy-requests.edit', $requestItem->id) }}" title="Edit">
                                                            <i class="fa-solid fa-pencil"></i>
                                                        </a>
                                                    @endif
                                                    @if(($requestItem->approval_status ?? 'pending') == 'pending')
                                                        <form action="{{ route('job-vacancy-requests.approve', $requestItem->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="od-icon-btn text-success" title="Manager Approve" style="border: 1px solid #dfe4ee; background: #fff;">
                                                                <i class="fa-solid fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('job-vacancy-requests.reject', $requestItem->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="od-icon-btn text-danger" title="Manager Reject" style="border: 1px solid #dfe4ee; background: #fff;">
                                                                <i class="fa-solid fa-xmark"></i>
                                                            </button>
                                                        </form>
                                                    @elseif(($requestItem->approval_status ?? 'pending') == 'approved')
                                                        <form action="{{ route('job-vacancy-requests.hr-approve', $requestItem->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="od-icon-btn text-success" title="HR Approve & Convert" style="border: 1px solid #dfe4ee; background: #fff;">
                                                                <i class="fa-solid fa-check-double"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('job-vacancy-requests.reject', $requestItem->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="od-icon-btn text-danger" title="HR Reject" style="border: 1px solid #dfe4ee; background: #fff;">
                                                                <i class="fa-solid fa-xmark"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                                @if(isset($permissions) && $permissions->can_delete)
                                                    <a class="od-icon-btn text-danger delete-job-request-btn" href="#" data-id="{{ $requestItem->id }}" title="Delete">
                                                        <i class="fa-regular fa-trash-can"></i>
                                                    </a>
                                                @endif
                                            </div>

                                            <form id="delete-job-request-form-{{ $requestItem->id }}" action="{{ route('job-vacancy-requests.destroy', $requestItem->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fa-solid fa-clipboard-list fa-2x mb-3"></i><br>
                                            No job vacancy requests found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($jobVacancyRequests->count() > 0)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $jobVacancyRequests->firstItem() }} to {{ $jobVacancyRequests->lastItem() }} of {{ $jobVacancyRequests->total() }} entries
                                </div>
                                <div>
                                    {{ $jobVacancyRequests->appends(['tab' => 'job-requests'])->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- QUESTIONS & EXPERIENCE TAB -->
        <div class="tab-pane fade" id="questions-experience-tab">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-question-circle me-2"></i>Questions & Experience Management
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Experience Management Card -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-briefcase fa-3x text-primary mb-3"></i>
                                    <h5 class="card-title">Experience Management</h5>
                                    <p class="card-text text-muted">Manage experience levels for job positions and candidates.</p>
                                    <a href="{{ route('experience.index') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-external-link-alt me-2"></i>Go to Experience Management
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Questions Management Card -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-question-circle fa-3x text-success mb-3"></i>
                                    <h5 class="card-title">Questions Management</h5>
                                    <p class="card-text text-muted">Manage interview questions, categories, and assessments.</p>
                                    <a href="{{ route('Question.index') }}" class="btn btn-success mt-3">
                                        <i class="fas fa-external-link-alt me-2"></i>Go to Questions Management
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity Section -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-history me-2"></i>Recent Activity
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Recent Experience Levels</h6>
                                            <ul class="list-group list-group-flush">
                                                @php
                                                    // Fetch recent experiences - you'll need to pass this from controller
                                                    $recentExperiences = $recentExperiences ?? collect();
                                                @endphp
                                                @forelse($recentExperiences as $exp)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    {{ $exp->experience }}
                                                    <span class="badge bg-{{ $exp->status == 'active' ? 'success' : 'secondary' }}">
                                                        {{ $exp->status }}
                                                    </span>
                                                </li>
                                                @empty
                                                <li class="list-group-item text-muted">No recent experience levels</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-success">Recent Questions</h6>
                                            <ul class="list-group list-group-flush">
                                                @php
                                                    // Fetch recent questions - you'll need to pass this from controller
                                                    $recentQuestions = $recentQuestions ?? collect();
                                                @endphp
                                                @forelse($recentQuestions as $question)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    {{ Str::limit($question->question, 50) }}
                                                    <span class="badge bg-info">{{ $question->category }}</span>
                                                </li>
                                                @empty
                                                <li class="list-group-item text-muted">No recent questions</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Section -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-chart-bar me-2"></i>Quick Statistics
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 col-6 text-center">
                                            <div class="stat-box p-3">
                                                <i class="fas fa-briefcase fa-2x text-primary mb-2"></i>
                                                <h4 class="mb-0">{{ $experienceCount ?? 0 }}</h4>
                                                <small class="text-muted">Experience Levels</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6 text-center">
                                            <div class="stat-box p-3">
                                                <i class="fas fa-question-circle fa-2x text-success mb-2"></i>
                                                <h4 class="mb-0">{{ $questionCount ?? 0 }}</h4>
                                                <small class="text-muted">Total Questions</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6 text-center">
                                            <div class="stat-box p-3">
                                                <i class="fas fa-tags fa-2x text-warning mb-2"></i>
                                                <h4 class="mb-0">{{ $categoryCount ?? 0 }}</h4>
                                                <small class="text-muted">Question Categories</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6 text-center">
                                            <div class="stat-box p-3">
                                                <i class="fas fa-check-circle fa-2x text-info mb-2"></i>
                                                <h4 class="mb-0">{{ $activeExperienceCount ?? 0 }}</h4>
                                                <small class="text-muted">Active Experiences</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- REPORTS TAB -->
        <div class="tab-pane fade" id="reports-tab">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recruitment Reports</h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-5">
                        <i class="fas fa-chart-bar fa-3x text-warning mb-3"></i>
                        <h4>Analytics & Reports</h4>
                        <p class="text-muted">View recruitment metrics, time-to-hire, and success rates.</p>
                        <button class="btn btn-warning">Generate Reports</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- SETTINGS/MORE TAB -->
        <div class="tab-pane fade" id="settings-tab">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">More Options</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 text-center">
                                <div class="card-body">
                                    <i class="fas fa-envelope fa-2x text-primary mb-3"></i>
                                    <h6>Email Templates</h6>
                                    <p class="small text-muted">Manage email templates for candidates</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 text-center">
                                <div class="card-body">
                                    <i class="fas fa-file-contract fa-2x text-success mb-3"></i>
                                    <h6>Offer Letters</h6>
                                    <p class="small text-muted">Create and manage offer letters</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 text-center">
                                <div class="card-body">
                                    <i class="fas fa-sliders-h fa-2x text-info mb-3"></i>
                                    <h6>Settings</h6>
                                    <p class="small text-muted">Configure recruitment settings</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ACTIVITY LOG TAB -->
        <div class="tab-pane fade" id="activity-log-tab">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Recruitment Activity Log
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('recruitment.index') }}" class="mb-4">
                        <input type="hidden" name="tab" value="activity-log">
                        <div class="row g-3">
                            <div class="col-md-4 col-12">
                                <input type="text" name="activity_search" class="form-control" placeholder="Search by name, action, remarks..." value="{{ request('activity_search') }}">
                            </div>
                            <div class="col-md-3 col-6">
                                <input type="date" name="activity_start_date" class="form-control" value="{{ request('activity_start_date') }}">
                            </div>
                            <div class="col-md-3 col-6">
                                <input type="date" name="activity_end_date" class="form-control" value="{{ request('activity_end_date') }}">
                            </div>
                            <div class="col-md-2 col-12 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                                <a href="{{ route('recruitment.index') }}?tab=activity-log" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="text-muted" style="font-size: 12px; border-bottom: 2px solid #f1f5f9;">
                                    <th>Date & Time</th>
                                    <th>Candidate Name</th>
                                    <th>Action</th>
                                    <th>Remarks</th>
                                    <th class="text-end">Performed By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activityLogs as $log)
                                    <tr>
                                        <td style="font-size: 13px;">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, h:i A') }}</td>
                                        <td>
                                            @if($log->candidate_id)
                                                <span class="fw-bold text-dark">{{ $log->candidate_first_name }} {{ $log->candidate_last_name }}</span>
                                            @else
                                                <span class="text-muted">General</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-light">{{ $log->action }}</span>
                                        </td>
                                        <td class="text-dark" style="font-size: 13px;">{{ $log->remarks ?? '-' }}</td>
                                        <td class="text-end fw-bold text-dark" style="font-size: 13px;">{{ $log->performer_name ?? 'System' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No activity logs recorded.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($activityLogs->count() > 0)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $activityLogs->firstItem() }} to {{ $activityLogs->lastItem() }} of {{ $activityLogs->total() }} entries
                                </div>
                                <div>
                                    {{ $activityLogs->appends([
                                        'tab' => 'activity-log',
                                        'activity_search' => request('activity_search'),
                                        'activity_start_date' => request('activity_start_date'),
                                        'activity_end_date' => request('activity_end_date')
                                    ])->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Interview Status Modal -->
<div class="modal fade" id="interviewStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Interview Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="interviewStatusForm">
                    <input type="hidden" id="interviewId">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" id="interviewStatus" required>
                            <option value="scheduled">Scheduled</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="rescheduled">Rescheduled</option>
                        </select>
                    </div>
                    <div class="form-group" id="marksGroup" style="display: none;">
                        <label class="mt-3">Total Marks</label>
                        <input type="number" class="form-control" id="totalMarks" name="total_marks" step="0.01" placeholder="Enter Total Marks">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveInterviewStatus()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<style>
.content.container-fluid {
    background: #f5f6fb;
    min-height: calc(100vh - 60px);
    padding: 28px 24px;
}

.recruitment-tabs {
    align-items: center;
    background: #fff;
    border: 1px solid #e1e5ef;
    border-radius: 14px;
    box-shadow: 0 8px 24px rgba(8, 24, 74, 0.04);
    display: flex;
    gap: 10px;
    flex-wrap: nowrap;
    overflow-x: auto;
    padding: 8px;
    white-space: nowrap;
}

.recruitment-tabs .nav-link {
    align-items: center;
    border: 1px solid transparent;
    border-radius: 10px;
    color: #29344d;
    display: flex;
    font-size: 14px;
    font-weight: 800;
    justify-content: center;
    min-height: 44px;
    padding: 10px 18px;
}

.recruitment-tabs .nav-link:hover {
    background: #fff8f3;
    color: #ff6b13;
}

.recruitment-tabs .nav-link.active {
    background: #ff6b13;
    border-color: #ff6b13;
    color: #fff;
}

.recruitment-tabs .nav-link i {
    font-size: 15px;
}

.tabs-underline {
    display: none;
}

.tab-content {
    padding-top: 24px !important;
}

.tab-pane > .card {
    background: #fff;
    border: 1px solid #e1e5ef;
    border-radius: 14px;
    box-shadow: 0 8px 24px rgba(8, 24, 74, 0.04);
}

.tab-pane > .card > .card-header {
    background: #fbfcff;
    border-bottom: 1px solid #e1e5ef;
    border-radius: 14px 14px 0 0;
    padding: 18px 22px;
}

.tab-pane > .card > .card-body {
    padding: 22px;
}

.card-title {
    color: #020b36;
    font-size: 18px;
    font-weight: 800;
    margin-bottom: 0;
}

.card-title i {
    color: #ff6b13;
}

.tab-pane > .card > .card-body > form.mb-4,
.tab-pane > .card > .card-body > .card.mb-4 {
    background: #fff;
    border: 1px solid #e1e5ef;
    border-radius: 14px;
    box-shadow: none;
    margin-bottom: 22px !important;
    padding: 18px;
}

.tab-pane > .card > .card-body > .card.mb-4 > .card-body {
    padding: 0;
}

.tab-pane > .card > .card-body > .card.mb-4 .card-title {
    color: #020b36;
    font-size: 14px;
    font-weight: 800;
    margin-bottom: 14px;
    text-transform: uppercase;
}

.tab-pane > .card > .card-body > form.mb-4,
.tab-pane > .card > .card-body > .card.mb-4 {
    background: #fff;
    border: 0;
    border-radius: 0;
    box-shadow: none;
    margin-bottom: 20px !important;
    padding: 0 0 2px;
}

.tab-pane > .card > .card-body > .card.mb-4 .card-title {
    display: none;
}

.tab-pane > .card > .card-body > form.mb-4 > .row,
.tab-pane > .card > .card-body > .card.mb-4 form > .row {
    align-items: center;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin: 0;
}

.tab-pane > .card > .card-body > form.mb-4 > .row > [class*="col-"],
.tab-pane > .card > .card-body > .card.mb-4 form > .row > [class*="col-"] {
    flex: 0 0 auto;
    max-width: none;
    padding: 0;
    width: auto;
}

.tab-pane > .card > .card-body > .card.mb-4 form > .row.mt-3 {
    margin-top: 0 !important;
}

.tab-pane > .card > .card-body > .card.mb-4 form .row .row {
    display: flex;
    gap: 10px;
    margin: 0;
}

.tab-pane > .card > .card-body > .card.mb-4 form .row .row > [class*="col-"] {
    padding: 0;
    width: auto;
}

.tab-pane > .card > .card-body > form.mb-4 .form-group,
.tab-pane > .card > .card-body > .card.mb-4 .form-group {
    margin-bottom: 0;
}

.tab-pane > .card > .card-body > form.mb-4 label,
.tab-pane > .card > .card-body > .card.mb-4 label {
    display: none;
}

.tab-pane > .card > .card-body > form.mb-4 .form-control,
.tab-pane > .card > .card-body > form.mb-4 select.form-control,
.tab-pane > .card > .card-body > .card.mb-4 .form-control,
.tab-pane > .card > .card-body > .card.mb-4 select.form-control {
    background-color: #fff;
    border: 1px solid #dce3ec;
    border-radius: 8px;
    box-shadow: none;
    color: #29344d;
    font-size: 15px;
    font-weight: 500;
    height: 44px;
    min-height: 44px;
    padding: 9px 16px;
    width: 190px;
}

.tab-pane > .card > .card-body > form.mb-4 input[type="text"],
.tab-pane > .card > .card-body > .card.mb-4 input[type="text"] {
    border-color: #dce3ec;
    padding-left: 16px;
    width: 235px;
}

.tab-pane > .card > .card-body > form.mb-4 input[type="date"],
.tab-pane > .card > .card-body > .card.mb-4 input[type="date"],
.tab-pane > .card > .card-body > form.mb-4 input[type="number"],
.tab-pane > .card > .card-body > .card.mb-4 input[type="number"] {
    width: 180px;
}

.tab-pane > .card > .card-body > form.mb-4 .form-control::placeholder,
.tab-pane > .card > .card-body > .card.mb-4 .form-control::placeholder {
    color: #9aa7ba;
    font-weight: 600;
}

.tab-pane > .card > .card-body > form.mb-4 .form-control:focus,
.tab-pane > .card > .card-body > .card.mb-4 .form-control:focus {
    background-color: #fff;
    border-color: #ff6b13;
    box-shadow: 0 0 0 3px rgba(255, 107, 19, 0.10);
}

.tab-pane > .card > .card-body > form.mb-4 .btn,
.tab-pane > .card > .card-body > .card.mb-4 .btn {
    border-radius: 8px;
    height: 42px;
    min-height: 42px;
    padding: 8px 18px;
    white-space: nowrap;
}

.tab-pane > .card > .card-body > form.mb-4 .btn-secondary,
.tab-pane > .card > .card-body > .card.mb-4 .btn-secondary {
    background: #f8fafc;
    border-color: #dfe6f2;
    color: #4b5568;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label,
.form-label {
    color: #29344d;
    font-size: 14px;
    font-weight: 800;
    margin-bottom: 8px;
}

.form-control,
.form-select,
select.form-control,
textarea.form-control {
    background-color: #fff;
    border: 1px solid #dfe4ee;
    border-radius: 10px;
    color: #020b36;
    font-size: 14px;
    min-height: 44px;
    padding: 10px 14px;
}

.form-control:focus,
.form-select:focus,
select.form-control:focus,
textarea.form-control:focus {
    border-color: #ff6b13;
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 19, 0.12);
}

.btn {
    border-radius: 10px;
    font-size: 14px;
    font-weight: 800;
    min-height: 40px;
    padding: 9px 16px;
}

.btn-primary,
.add-btn {
    background: #ff6b13;
    border-color: #ff6b13;
    color: #fff;
}

.btn-primary:hover,
.btn-primary:focus,
.add-btn:hover {
    background: #e85f0f;
    border-color: #e85f0f;
    color: #fff;
}

.btn-secondary,
.btn-info,
.btn-warning,
.btn-success,
.btn-danger {
    background: #fff;
    border: 1px solid #dfe4ee;
    color: #020b36;
}

.btn-secondary:hover,
.btn-info:hover,
.btn-warning:hover,
.btn-success:hover,
.btn-danger:hover {
    background: #fff8f3;
    border-color: #ff6b13;
    color: #ff6b13;
}

.btn-sm {
    align-items: center;
    display: inline-flex;
    justify-content: center;
    min-height: 34px;
    padding: 7px 11px;
}

.btn-group {
    gap: 6px;
}

.table .btn-group,
.table .od-inline-actions,
.table .dropdown-action {
    align-items: center;
    display: inline-flex;
    gap: 8px;
    justify-content: flex-end;
}

.table .btn-group form {
    display: inline-flex !important;
    margin: 0;
}

.table .btn-sm,
.table .od-icon-btn,
.table .action-icon {
    align-items: center;
    background: #fff !important;
    border: 1px solid #ff5b13 !important;
    border-radius: 8px !important;
    box-shadow: none !important;
    color: #ff5b13 !important;
    display: inline-flex;
    font-size: 15px;
    height: 36px;
    justify-content: center;
    line-height: 1;
    min-height: 36px;
    padding: 0 !important;
    text-decoration: none;
    width: 38px;
}

.table .btn-sm i,
.table .od-icon-btn i,
.table .action-icon i,
.table .action-icon .material-icons {
    color: #ff5b13 !important;
    font-size: 16px;
    line-height: 1;
    margin: 0 !important;
}

.table .action-icon .material-icons {
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
}

.table .action-icon .material-icons::before {
    content: "\f141";
}

.table .action-icon .material-icons {
    font-size: 0;
}

.table .action-icon .material-icons::before {
    font-size: 16px;
}

.table .btn-sm:hover,
.table .btn-sm:focus,
.table .od-icon-btn:hover,
.table .od-icon-btn:focus,
.table .action-icon:hover,
.table .action-icon:focus {
    background: #ff5b13 !important;
    border-color: #ff5b13 !important;
    color: #fff !important;
}

.table .btn-sm:hover i,
.table .btn-sm:focus i,
.table .od-icon-btn:hover i,
.table .od-icon-btn:focus i,
.table .action-icon:hover i,
.table .action-icon:focus i,
.table .action-icon:hover .material-icons,
.table .action-icon:focus .material-icons,
.table .action-icon:hover .material-icons::before,
.table .action-icon:focus .material-icons::before {
    color: #fff !important;
}

.table .action-icon.dropdown-toggle::after {
    display: none;
}

.table td.text-center .btn-sm {
    border-radius: 8px !important;
    height: auto;
    min-height: 36px;
    padding: 7px 12px !important;
    width: auto;
}

.table-responsive {
    background: #fff;
    border: 1px solid #e0e5ed;
    border-radius: 12px;
    box-shadow: none;
    overflow: auto;
}

.table {
    border-collapse: separate;
    border-spacing: 0;
    margin-bottom: 0;
}

.table thead th {
    background: #f8fafc;
    border-bottom: 1px solid #e0e5ed;
    color: #5f6b7a;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0;
    padding: 16px 18px;
    text-transform: uppercase;
    vertical-align: middle;
    white-space: nowrap;
}

.table tbody td {
    background: #fff;
    border-top: 1px solid #edf0f4;
    color: #020b36;
    font-size: 15px;
    padding: 16px 18px;
    vertical-align: middle;
}

.table tbody tr:hover td {
    background: #fffaf5 !important;
}

.table strong,
.table b {
    color: #020b36;
    font-weight: 800;
}

.badge {
    border-radius: 999px;
    font-size: 12px;
    font-weight: 800;
    padding: 7px 12px;
}

.badge.bg-success,
.bg-success-light {
    background: #eafaf1 !important;
    color: #078c4a !important;
}

.badge.bg-warning,
.bg-warning-light {
    background: #fff4df !important;
    color: #cf7600 !important;
}

.badge.bg-info,
.bg-info-light {
    background: #e8fbff !important;
    color: #078ca0 !important;
}

.badge.bg-danger,
.bg-danger-light {
    background: #fff0f0 !important;
    color: #d43d3d !important;
}

.badge.bg-secondary,
.bg-secondary-light {
    background: #eef1f6 !important;
    color: #526071 !important;
}

.badge.bg-primary,
.bg-primary-light {
    background: #eef0ff !important;
    color: #5455d9 !important;
}

.dropdown-menu {
    border: 1px solid #e1e5ef;
    border-radius: 12px;
    box-shadow: 0 12px 28px rgba(8, 24, 74, 0.12);
    padding: 8px;
}

.dropdown-item {
    border-radius: 8px;
    color: #29344d;
    font-size: 14px;
    font-weight: 700;
    padding: 10px 12px;
}

.dropdown-item:hover {
    background: #fff8f3;
    color: #ff6b13;
}

.dropdown-item i {
    color: #ff6b13;
    width: 16px;
    text-align: center;
}

.text-muted {
    color: #7b88aa !important;
}

.pagination {
    margin-bottom: 0;
}

.page-link {
    border: 1px solid #dfe4ee;
    border-radius: 8px;
    color: #020b36;
    margin: 0 2px;
}

.page-link:hover,
.page-item.active .page-link {
    background: #ff6b13;
    border-color: #ff6b13;
    color: #fff;
}

.od-inline-actions {
    display: inline-flex;
    gap: 8px;
}

.od-icon-btn {
    align-items: center;
    background: #fff;
    border: 1px solid #dfe4ee;
    border-radius: 10px;
    color: #020b36;
    display: inline-flex;
    height: 36px;
    justify-content: center;
    text-decoration: none;
    width: 40px;
}

.od-icon-btn:hover {
    background: #fff8f3;
    border-color: #ff6b13;
    color: #ff6b13;
}

.stat-box {
    background: #fff;
    border: 1px solid #e1e5ef;
    border-bottom: 3px solid #ff6b13;
    border-radius: 14px;
    box-shadow: 0 8px 24px rgba(8, 24, 74, 0.04);
}

.stat-box:hover {
    box-shadow: 0 10px 26px rgba(8, 24, 74, 0.08);
}

.list-group-item {
    border: none;
    padding: 0.75rem 0;
}

.modal-content {
    border: 1px solid #e1e5ef;
    border-radius: 14px;
    box-shadow: 0 18px 45px rgba(8, 24, 74, 0.18);
}

.modal-header {
    background: #fbfcff;
    border-bottom: 1px solid #e1e5ef;
    border-radius: 14px 14px 0 0;
}

.modal-title {
    color: #020b36;
    font-weight: 800;
}

@media (max-width: 768px) {
    .content.container-fluid {
        padding: 20px 14px;
    }

    .recruitment-tabs .nav-link {
        padding: 10px 12px;
    }

    .recruitment-tabs .nav-link i {
        margin-right: 0 !important;
    }

    .tab-pane > .card > .card-body {
        padding: 16px;
    }

    .table thead th,
    .table tbody td {
        padding: 13px 14px;
        white-space: nowrap;
    }

    .stat-box {
        margin-bottom: 1rem;
    }
}

.recruitment-tabs::-webkit-scrollbar {
    height: 4px;
}

.recruitment-tabs::-webkit-scrollbar-track {
    background: transparent;
}

.recruitment-tabs::-webkit-scrollbar-thumb {
    background: #dfe4ee;
    border-radius: 99px;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Load last active tab from URL or localStorage
    const urlParams = new URLSearchParams(window.location.search);
    const urlTab = urlParams.get('tab');
    const firstTabLink = document.querySelector('.recruitment-tabs .nav-link');
    let lastTab = urlTab ? `#${urlTab}-tab` : (firstTabLink ? firstTabLink.getAttribute('href') : null);
    let trigger = document.querySelector(`a[href="${lastTab}"]`);
    
    if (trigger) {
        // Remove active class from all tabs
        document.querySelectorAll('.recruitment-tabs .nav-link').forEach(link => {
            link.classList.remove('active');
        });
        
        // Add active class to current tab
        trigger.classList.add('active');
        
        // Hide all tab panes
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });
        
        // Show target tab pane
        let targetPane = document.querySelector(lastTab);
        if (targetPane) {
            targetPane.classList.add('show', 'active');
        }
    }

    // Save active tab on click
    document.querySelectorAll('.recruitment-tabs .nav-link').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            
            // Get the target tab ID
            let targetTab = this.getAttribute('href');
            let tabName = targetTab.replace('#', '').replace('-tab', '');
            
            // Update URL with tab parameter
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.pushState({}, '', url);
            
            // Save to localStorage
            localStorage.setItem("recruitment_active_tab", targetTab);
            
            // Remove active class from all tabs
            document.querySelectorAll('.recruitment-tabs .nav-link').forEach(navLink => {
                navLink.classList.remove('active');
            });
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Hide all tab panes
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Show target tab pane
            let targetPane = document.querySelector(targetTab);
            if (targetPane) {
                targetPane.classList.add('show', 'active');
            }
            
            // Bootstrap's tab activation
            new bootstrap.Tab(this).show();
        });
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// ============ JOBS FUNCTIONALITY ============
$(document).ready(function() {
    // Delete job functionality
    $(document).on('click', '.delete-job-btn', function(e) {
        e.preventDefault();
        var jobId = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "This job will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete-form-' + jobId).submit();
            }
        });
    });

    // Auto-submit form on filter change
    $('select[name="job_department"], select[name="job_type"], select[name="job_status"]').on('change', function() {
        $('#job-filter-form').submit();
    });

    $(document).on('click', '.delete-job-request-btn', function(e) {
        e.preventDefault();
        var requestId = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "This job vacancy request will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete-job-request-form-' + requestId).submit();
            }
        });
    });

    $('select[name="job_request_status"], select[name="job_request_hr_status"]').on('change', function() {
        $('#job-request-filter-form').submit();
    });
});

function resetJobFilters() {
    window.location.href = '{{ route("recruitment.index") }}?tab=job-listings';
}

function resetJobRequestFilters() {
    window.location.href = '{{ route("recruitment.index") }}?tab=job-requests';
}

// ============ SHORTLIST FUNCTIONALITY ============
$(document).ready(function() {
    // Auto-submit form on filter change for shortlist
    $('select[name="shortlist_position"], select[name="shortlist_status"], select[name="shortlist_interview_status"]').on('change', function() {
        $('#shortlist-filter-form').submit();
    });

    // Enter key support for shortlist search
    $('input[name="shortlist_search"]').on('keypress', function(e) {
        if (e.which === 13) {
            $('#shortlist-filter-form').submit();
        }
    });
});

function resetShortlistFilters() {
    window.location.href = '{{ route("recruitment.index") }}?tab=shortlist';
}

function updateInterviewStatus(interviewId) {
    $('#interviewId').val(interviewId);
    $('#interviewStatus').val('scheduled');
    $('#marksGroup').hide();
    $('#totalMarks').val('');
    $('#interviewStatusModal').modal('show');
}

$('#interviewStatus').change(function() {
    if ($(this).val() === 'completed') {
        $('#marksGroup').show();
    } else {
        $('#marksGroup').hide();
        $('#totalMarks').val('');
    }
});

function saveInterviewStatus() {
    const interviewId = $('#interviewId').val();
    const status = $('#interviewStatus').val();
    const totalMarks = $('#totalMarks').val();

    $.ajax({
        url: "{{ route('shortlist.update-interview-status', ':id') }}".replace(':id', interviewId),
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            status: status,
            total_marks: totalMarks
        },
        success: function(response) {
            if (response.success) {
                $('#interviewStatusModal').modal('hide');
                Swal.fire('Success!', response.message, 'success').then(() => {
                    location.reload();
                });
            }
        },
        error: function() {
            Swal.fire('Error!', 'Something went wrong!', 'error');
        }
    });
}

// ============ CANDIDATE FUNCTIONALITY ============
function updateStatus(candidateId, status) {
    if (confirm('Are you sure you want to update the status?')) {
        // Use the named route with parameters
        const url = "{{ route('add-resume.update-status', ['id' => ':id']) }}".replace(':id', candidateId);
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: status,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Status updated successfully!');
                location.reload(); // Reload to show updated status
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating status.');
        });
    }
}

function updateRoundStatus(candidateId, round, status) {
    if (confirm('Are you sure you want to update this interview round status?')) {
        const url = "{{ route('add-resume.update-round-status', ['id' => ':id']) }}".replace(':id', candidateId);

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                round: round,
                status: status,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the round status.');
        });
    }
}
// Auto hide success messages
setTimeout(function() {
    let successMessage = document.getElementById('success-message');
    if (successMessage) {
        successMessage.style.display = 'none';
    }
    let successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.opacity = '0';
            setTimeout(() => successAlert.remove(), 500);
        }, 3000);
    }
}, 3000);

// Handle browser back/forward buttons
window.addEventListener('popstate', function(event) {
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam) {
        const tabId = `#${tabParam}-tab`;
        const tabElement = document.querySelector(`a[href="${tabId}"]`);
        if (tabElement) {
            // Remove active class from all tabs
            document.querySelectorAll('.recruitment-tabs .nav-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Add active class to current tab
            tabElement.classList.add('active');
            
            // Hide all tab panes
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Show target tab pane
            let targetPane = document.querySelector(tabId);
            if (targetPane) {
                targetPane.classList.add('show', 'active');
            }
        }
    }
});

// Candidate Selection / Rejection triggers
$(document).ready(function() {
    $(document).on('click', '.btn-reject-candidate', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        $('#reject-candidate-name').text(name);
        $('#rejectCandidateForm').attr('action', '{{ route("recruitment.reject-candidate", ":id") }}'.replace(':id', id));
        $('#rejectCandidateModal').modal('show');
    });

    $(document).on('click', '.btn-view-salary', function() {
        const id = $(this).data('id');
        
        fetch('{{ route("recruitment.view-salary", ":id") }}'.replace(':id', id))
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    const data = res.data;
                    $('#mv-basic').text(parseFloat(data.basic_salary).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    $('#mv-da').text(parseFloat(data.da_amount).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    $('#mv-hra').text(parseFloat(data.hra_amount).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    $('#mv-conveyance').text(parseFloat(data.conveyance).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    $('#mv-special').text(parseFloat(data.special_allowance).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    $('#mv-medical').text(parseFloat(data.medical_allowance).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    $('#mv-gross').text(parseFloat(data.gross_salary).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    
                    $('#mv-pf').text(parseFloat(data.pf_amount).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    $('#mv-esi').text(parseFloat(data.esi_amount).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    $('#mv-pt').text(parseFloat(data.professional_tax).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    $('#mv-welfare').text(parseFloat(data.welfare_fund).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    $('#mv-tds').text(parseFloat(data.tds).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    
                    const deductions = parseFloat(data.pf_amount) + parseFloat(data.esi_amount) + parseFloat(data.professional_tax) + parseFloat(data.welfare_fund) + parseFloat(data.tds);
                    $('#mv-deductions').text(deductions.toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    
                    $('#mv-net').text(parseFloat(data.net_salary).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    
                    $('#salaryBreakdownModal').modal('show');
                } else {
                    alert('Error: ' + res.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Failed to load salary breakdown.');
            });
    });
});
</script>

<!-- Rejection Remarks Modal -->
<div class="modal fade" id="rejectCandidateModal" tabindex="-1" aria-labelledby="rejectCandidateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectCandidateForm" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectCandidateModalLabel">Reject Candidate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-dark">Are you sure you want to reject <strong id="reject-candidate-name"></strong>?</p>
                    <div class="form-group mt-3">
                        <label for="rejection-remarks" class="form-label fw-bold">Rejection Remarks</label>
                        <textarea class="form-control" id="rejection-remarks" name="remarks" rows="3" placeholder="Enter reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-end pt-0">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger text-white" style="background-color: #dc3545 !important; border-color: #dc3545 !important;">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Salary Breakdown Modal -->
<div class="modal fade" id="salaryBreakdownModal" tabindex="-1" aria-labelledby="salaryBreakdownModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="salaryBreakdownModalLabel"><i class="fas fa-coins text-primary me-2"></i>Salary Breakdown</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 border-end">
                        <h6 class="text-success border-bottom pb-2 mb-3 fw-bold">Earnings (Monthly)</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-dark">Basic Salary:</td>
                                <td class="text-end fw-bold text-dark">₹<span id="mv-basic">0.00</span></td>
                            </tr>
                            <tr>
                                <td class="text-dark">Dearness Allowance (DA):</td>
                                <td class="text-end fw-bold text-dark">₹<span id="mv-da">0.00</span></td>
                            </tr>
                            <tr>
                                <td class="text-dark">House Rent Allowance (HRA):</td>
                                <td class="text-end fw-bold text-dark">₹<span id="mv-hra">0.00</span></td>
                            </tr>
                            <tr>
                                <td class="text-dark">Conveyance Allowance:</td>
                                <td class="text-end fw-bold text-dark">₹<span id="mv-conveyance">0.00</span></td>
                            </tr>
                            <tr>
                                <td class="text-dark">Special Allowance:</td>
                                <td class="text-end fw-bold text-dark">₹<span id="mv-special">0.00</span></td>
                            </tr>
                            <tr>
                                <td class="text-dark">Medical Allowance:</td>
                                <td class="text-end fw-bold text-dark">₹<span id="mv-medical">0.00</span></td>
                            </tr>
                            <tr class="table-success fw-bold">
                                <td class="text-dark">Gross Salary:</td>
                                <td class="text-end text-dark">₹<span id="mv-gross">0.00</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-danger border-bottom pb-2 mb-3 fw-bold">Deductions (Monthly)</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-dark">Provident Fund (PF):</td>
                                <td class="text-end fw-bold text-dark">₹<span id="mv-pf">0.00</span></td>
                            </tr>
                            <tr>
                                <td class="text-dark">Employee State Insurance (ESI):</td>
                                <td class="text-end fw-bold text-dark">₹<span id="mv-esi">0.00</span></td>
                            </tr>
                            <tr>
                                <td class="text-dark">Professional Tax:</td>
                                <td class="text-end fw-bold text-dark">₹<span id="mv-pt">0.00</span></td>
                            </tr>
                            <tr>
                                <td class="text-dark">Welfare Fund:</td>
                                <td class="text-end fw-bold text-dark">₹<span id="mv-welfare">0.00</span></td>
                            </tr>
                            <tr>
                                <td class="text-dark">TDS:</td>
                                <td class="text-end fw-bold text-dark">₹<span id="mv-tds">0.00</span></td>
                            </tr>
                            <tr class="table-danger fw-bold">
                                <td class="text-dark">Total Deductions:</td>
                                <td class="text-end text-dark">₹<span id="mv-deductions">0.00</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="p-3 mt-4 rounded d-flex justify-content-between align-items-center" style="background-color: #e2f0d9;">
                    <span class="fw-bold text-dark" style="font-size: 16px;">Net Take-home Salary:</span>
                    <span class="fw-bold text-success" style="font-size: 20px;">₹<span id="mv-net">0.00</span>/month</span>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
