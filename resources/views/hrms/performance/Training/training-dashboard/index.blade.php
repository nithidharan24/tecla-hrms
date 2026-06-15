
@extends('layouts.index')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 text-gray-800">My Training Dashboard</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('eemployee.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Training</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Dashboard Cards -->
    <div class="row g-4 mb-4">
        <!-- Employee Profile Card -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="position-relative me-3">
                            @if($employee->profile_image)
                                <img src="{{ asset('storage/' . $employee->profile_image) }}" 
                                     alt="Profile" 
                                     class="rounded-circle border border-3 border-white shadow-sm" 
                                     style="width: 70px; height: 70px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-gradient-primary d-flex align-items-center justify-content-center text-white shadow-sm" 
                                     style="width: 70px; height: 70px; font-size: 24px; font-weight: 600;">
                                    {{ strtoupper(substr($employee->firstname, 0, 1)) }}{{ strtoupper(substr($employee->lastname, 0, 1)) }}
                                </div>
                            @endif
                            <span class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-2 border-white" 
                                  style="width: 20px; height: 20px;"></span>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1 fw-bold">{{ $employee->firstname }} {{ $employee->lastname }}</h5>
                            <p class="text-muted mb-1 small">Employee ID: {{ $employee->employeeid }}</p>
                            <p class="text-muted mb-1 small">{{ $employee->email }}</p>
                            @if($trainer)
                                <p class="text-muted mb-0 small">
                                    <i class="fas fa-user-tie me-1"></i>Trainer: {{ $trainer->first_name }} {{ $trainer->last_name }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Training Progress Card -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 text-center">
                    <h5 class="card-title mb-4 fw-bold">Training Progress</h5>
                    <div class="position-relative d-inline-block mb-3">
                        <svg width="120" height="120" class="progress-ring">
                            <circle cx="60" cy="60" r="50" fill="none" stroke="#e9ecef" stroke-width="8"/>
                            <circle cx="60" cy="60" r="50" fill="none" stroke="url(#gradient)" stroke-width="8"
                                    stroke-dasharray="{{ 2 * 3.14159 * 50 }}"
                                    stroke-dashoffset="{{ 2 * 3.14159 * 50 * (1 - $progressPercentage / 100) }}"
                                    stroke-linecap="round"
                                    class="progress-circle"
                                    id="progress-circle"/>
                            <defs>
                                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="position-absolute top-50 start-50 translate-middle">
                            <h3 class="mb-0 fw-bold text-primary" id="progress-percentage">{{ round($progressPercentage, 1) }}%</h3>
                        </div>
                    </div>
                    <p class="text-muted mb-0">
                        <span id="completed-count" class="fw-semibold">{{ $completedCount }}</span> of 
                        <span id="total-count" class="fw-semibold">{{ $totalCount }}</span> resources completed
                    </p>
                </div>
            </div>
        </div>

        <!-- Training Status Card -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4 fw-bold">Training Status</h5>
                    @if($trainingDetails)
                        <div class="mb-3">
                            @php
                                $statusClass = $trainingDetails->status == 'completed' ? 'success' : 
                                              ($trainingDetails->status == 'in_progress' ? 'warning' : 'info');
                                $statusIcon = $trainingDetails->status == 'completed' ? 'fa-check-circle' : 
                                             ($trainingDetails->status == 'in_progress' ? 'fa-clock' : 'fa-info-circle');
                            @endphp
                            <span class="badge bg-{{ $statusClass }} px-3 py-2 rounded-pill">
                                <i class="fas {{ $statusIcon }} me-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $trainingDetails->status)) }}
                            </span>
                        </div>
                        
                        <div class="training-info">
                            @if($trainingDetails->start_date)
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-calendar-alt text-success me-2"></i>
                                    <small class="text-muted">Start: {{ date('M d, Y', strtotime($trainingDetails->start_date)) }}</small>
                                </div>
                            @endif
                            @if($trainingDetails->end_date)
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-calendar-check text-danger me-2"></i>
                                    <small class="text-muted">End: {{ date('M d, Y', strtotime($trainingDetails->end_date)) }}</small>
                                </div>
                            @endif
                            @if($trainingDetails->duration_hours)
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-clock text-primary me-2"></i>
                                    <small class="text-muted">Duration: {{ $trainingDetails->duration_hours }} hours</small>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-exclamation-triangle text-warning fa-2x mb-2"></i>
                            <p class="text-muted mb-0">No training assigned yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($trainingDetails)
        <!-- Training Description -->
        @if($trainingDetails->description)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3 fw-bold">
                            <i class="fas fa-info-circle text-primary me-2"></i>Training Description
                        </h5>
                        <p class="text-muted mb-3">{{ $trainingDetails->description }}</p>
                        @if($trainingDetails->training_type)
                            <div class="d-inline-block">
                                <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                                    <i class="fas fa-tag me-1"></i>{{ $trainingDetails->training_type }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Training Resources -->
        @if(!empty($trainingResources))
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4 fw-bold">
                            <i class="fas fa-book-open text-primary me-2"></i>Training Resources
                        </h5>
                        
                        <!-- Premium Nav tabs -->
                        <ul class="nav nav-pills nav-fill mb-4" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active rounded-pill mx-1" data-bs-toggle="tab" href="#all-resources">
                                    <i class="fas fa-list me-2"></i>All Resources 
                                    <span class="badge bg-white text-primary ms-1">{{ count($trainingResources) }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link rounded-pill mx-1" data-bs-toggle="tab" href="#modules">
                                    <i class="fas fa-book me-2"></i>Modules 
                                    <span class="badge bg-white text-muted ms-1">{{ count($groupedResources['module']) }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link rounded-pill mx-1" data-bs-toggle="tab" href="#videos">
                                    <i class="fas fa-video me-2"></i>Videos 
                                    <span class="badge bg-white text-muted ms-1">{{ count($groupedResources['video']) }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link rounded-pill mx-1" data-bs-toggle="tab" href="#links">
                                    <i class="fas fa-link me-2"></i>Links 
                                    <span class="badge bg-white text-muted ms-1">{{ count($groupedResources['link']) }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link rounded-pill mx-1" data-bs-toggle="tab" href="#documents">
                                    <i class="fas fa-file me-2"></i>Documents 
                                    <span class="badge bg-white text-muted ms-1">{{ count($groupedResources['document']) }}</span>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            <!-- All Resources Tab -->
                            <div class="tab-pane fade show active" id="all-resources">
                                @if(!empty($trainingResources))
                                    <div class="row g-3">
                                        @foreach($trainingResources as $resource)
                                            <div class="col-lg-6">
                                                @include('hrms.performance.Training.training-dashboard.resource-card', ['resource' => $resource])
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    @include('hrms.performance.Training.training-dashboard.empty-state', ['type' => 'resources', 'icon' => 'fa-graduation-cap'])
                                @endif
                            </div>

                            <!-- Other tabs -->
                            @foreach(['module' => 'book', 'video' => 'video', 'link' => 'link', 'document' => 'file'] as $type => $icon)
                            <div class="tab-pane fade" id="{{ $type }}s">
                                @if(!empty($groupedResources[$type]))
                                    <div class="row g-3">
                                        @foreach($groupedResources[$type] as $resource)
                                            <div class="col-lg-6">
                                                @include('hrms.performance.Training.training-dashboard.resource-card', ['resource' => $resource])
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    @include('hrms.performance.Training.training-dashboard.empty-state', ['type' => $type, 'icon' => 'fa-'.$icon])
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @else
        <!-- No Training Assigned -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-graduation-cap text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="text-muted mb-3">No Training Assigned</h4>
                        <p class="text-muted mb-4">You don't have any training assigned at the moment.</p>
                        <button class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-envelope me-2"></i>Contact HR Department
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>


<script>
// Fixed Mark Complete functionality
function markResourceCompleted(resourceId, button) {
    if (confirm('Are you sure you want to mark this resource as completed?')) {
        // Show loading state
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
        button.disabled = true;

        fetch(`{{ url('/employee/training/resource') }}/${resourceId}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data); // Debug log
            
            if (data.success) {
                // Update button to completed state
                button.innerHTML = '<i class="fas fa-check me-1"></i>Completed';
                button.className = 'btn btn-sm btn-success';
                button.disabled = true;
                
                // Update progress display
                updateProgress(data.progress, data.completed, data.total);
                
                // Add completed badge to resource card
                addCompletedBadge(button);
                
                // Update resource card styling
                const resourceCard = button.closest('.resource-card');
                resourceCard.classList.add('completed');
                resourceCard.classList.add('border-success');
                
                // Add completion timestamp
                addCompletionTimestamp(button);
                
                // Show success notification
                showNotification('success', data.message || 'Resource marked as completed successfully!');
                
                // Add completion animation
                animateCompletion(resourceCard);
            } else {
                // Restore button state on error
                button.innerHTML = originalText;
                button.disabled = false;
                showNotification('error', data.message || 'Failed to update resource status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Restore button state on error
            button.innerHTML = originalText;
            button.disabled = false;
            showNotification('error', 'An error occurred while updating resource status');
        });
    }
}

function updateProgress(progress, completed, total) {
    console.log('Updating progress:', progress, completed, total); // Debug log
    
    // Update progress percentage
    const progressElement = document.getElementById('progress-percentage');
    const completedElement = document.getElementById('completed-count');
    const totalElement = document.getElementById('total-count');
    
    if (progressElement) {
        progressElement.textContent = progress + '%';
    }
    
    if (completedElement) {
        completedElement.textContent = completed;
    }
    
    if (totalElement) {
        totalElement.textContent = total;
    }
    
    // Update progress circle with animation
    const circle = document.getElementById('progress-circle');
    if (circle) {
        const circumference = 2 * Math.PI * 50;
        const offset = circumference * (1 - progress / 100);
        circle.style.strokeDashoffset = offset;
    }
}

function addCompletedBadge(button) {
    const resourceCard = button.closest('.resource-card');
    const titleElement = resourceCard.querySelector('.resource-title');
    
    if (!titleElement.querySelector('.completed-badge')) {
        const badge = document.createElement('span');
        badge.className = 'badge badge-soft-success ms-1 completed-badge';
        badge.innerHTML = '<i class="fas fa-check me-1"></i>Completed';
        titleElement.appendChild(badge);
    }
}

function addCompletionTimestamp(button) {
    const resourceCard = button.closest('.resource-card');
    const actionsDiv = button.parentElement;
    
    // Check if completion info already exists
    if (!resourceCard.querySelector('.completion-info')) {
        const now = new Date();
        const timestamp = now.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const completionInfo = document.createElement('p');
        completionInfo.className = 'text-muted mb-2 completion-info';
        completionInfo.innerHTML = `<small><i class="fas fa-clock me-1"></i>Completed on ${timestamp}</small>`;
        
        // Insert before the actions div
        actionsDiv.parentElement.insertBefore(completionInfo, actionsDiv);
    }
}

function animateCompletion(card) {
    card.style.transform = 'scale(1.02)';
    card.style.transition = 'all 0.3s ease';
    
    setTimeout(() => {
        card.style.transform = 'scale(1)';
    }, 300);
}

function showNotification(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas ${iconClass} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert:last-of-type');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}

// Initialize progress circle animation on page load
document.addEventListener('DOMContentLoaded', function() {
    const circle = document.getElementById('progress-circle');
    if (circle) {
        circle.style.transition = 'stroke-dashoffset 1s ease-in-out';
    }
    
    console.log('Page loaded, progress elements found:', {
        progressPercentage: !!document.getElementById('progress-percentage'),
        completedCount: !!document.getElementById('completed-count'),
        totalCount: !!document.getElementById('total-count'),
        progressCircle: !!document.getElementById('progress-circle')
    });
});
</script>


<style>
/* Premium Styles */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.progress-ring {
    transform: rotate(-90deg);
}

.progress-circle {
    transition: stroke-dashoffset 1s ease-in-out;
}

.nav-pills .nav-link {
    color: #6c757d;
    background: transparent;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.nav-pills .nav-link:hover {
    background: #f8f9fa;
    border-color: #dee2e6;
    transform: translateY(-1px);
}

.nav-pills .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: transparent;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
}

.card {
    transition: all 0.3s ease;
    border-radius: 12px;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

.resource-card {
    transition: all 0.3s ease;
}

.resource-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.btn {
    transition: all 0.3s ease;
    font-weight: 500;
}

.btn:hover {
    transform: translateY(-1px);
}

.training-info i {
    width: 16px;
    text-align: center;
}

.shadow-sm {
    box-shadow: 0 2px 10px rgba(0,0,0,0.08) !important;
}

.rounded-pill {
    border-radius: 50rem !important;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

@endsection