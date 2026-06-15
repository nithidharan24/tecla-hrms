@extends('layouts.index')

@section('title', 'Profile Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Profile Settings</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Profile Settings</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Profile Completion Card -->
        <div class="col-md-4">
            <div class="card profile-completion-card">
                <div class="card-header">
                    <h5 class="card-title">Profile Completion</h5>
                </div>
                <div class="card-body text-center">
                    <div class="completion-circle" id="completionCircle">
                        <span class="completion-percentage" id="completionPercentage">0%</span>
                    </div>
                    <p class="mt-3">Complete your profile to unlock all features</p>
                    <div class="completion-details" id="completionDetails">
                        <small class="text-muted">Loading...</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('employee.profile.edit', Session::get('user_id')) }}" class="btn btn-outline-primary btn-block">
                                <i class="fa-solid fa-user-edit"></i> Edit Basic Info
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('employee.profile.personal', Session::get('user_id')) }}" class="btn btn-outline-info btn-block">
                                <i class="fa-solid fa-id-card"></i> Personal Information
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('employee.profile.emergency', Session::get('user_id')) }}" class="btn btn-outline-warning btn-block">
                                <i class="fa-solid fa-phone"></i> Emergency Contacts
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('employee.profile.bank', Session::get('user_id')) }}" class="btn btn-outline-success btn-block">
                                <i class="fa-solid fa-university"></i> Bank Information
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Profile Activities</h5>
                </div>
                <div class="card-body">
                    <div class="activity-timeline" id="activityTimeline">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-completion-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.completion-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: conic-gradient(#28a745 0deg, #28a745 0deg, rgba(255,255,255,0.3) 0deg);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    position: relative;
}

.completion-circle::before {
    content: '';
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: white;
    position: absolute;
}

.completion-percentage {
    font-size: 18px;
    font-weight: bold;
    color: #333;
    z-index: 1;
}

.activity-timeline .activity-item {
    display: flex;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.activity-timeline .activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.activity-content {
    flex: 1;
}

.activity-date {
    font-size: 12px;
    color: #6c757d;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load profile completion
    loadProfileCompletion();
    
    // Load recent activities
    loadRecentActivities();
});

function loadProfileCompletion() {
    fetch('{{ route("header.profile.completion") }}')
        .then(response => response.json())
        .then(data => {
            const percentage = data.completion || 0;
            const completedFields = data.completed_fields || 0;
            const totalFields = data.total_fields || 0;
            
            // Update completion circle
            const circle = document.getElementById('completionCircle');
            const percentageElement = document.getElementById('completionPercentage');
            const detailsElement = document.getElementById('completionDetails');
            
            percentageElement.textContent = percentage + '%';
            
            // Update circle gradient
            const degrees = (percentage / 100) * 360;
            circle.style.background = `conic-gradient(#28a745 0deg, #28a745 ${degrees}deg, rgba(255,255,255,0.3) ${degrees}deg)`;
            
            // Update details
            detailsElement.innerHTML = `
                <small class="text-light">
                    ${completedFields} of ${totalFields} fields completed
                </small>
            `;
        })
        .catch(error => {
            console.error('Error loading profile completion:', error);
            document.getElementById('completionDetails').innerHTML = 
                '<small class="text-light">Unable to load completion data</small>';
        });
}

function loadRecentActivities() {
    fetch('{{ route("header.recent.activities") }}')
        .then(response => response.json())
        .then(data => {
            const timeline = document.getElementById('activityTimeline');
            const activities = data.activities || [];
            
            if (activities.length === 0) {
                timeline.innerHTML = '<p class="text-center text-muted">No recent activities found</p>';
                return;
            }
            
            let html = '';
            activities.forEach(activity => {
                const date = new Date(activity.date).toLocaleDateString();
                html += `
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fa-solid ${activity.icon} text-primary"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-message">${activity.message}</div>
                            <div class="activity-date">${date}</div>
                        </div>
                    </div>
                `;
            });
            
            timeline.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading recent activities:', error);
            document.getElementById('activityTimeline').innerHTML = 
                '<p class="text-center text-muted">Unable to load recent activities</p>';
        });
}
</script>
@endsection