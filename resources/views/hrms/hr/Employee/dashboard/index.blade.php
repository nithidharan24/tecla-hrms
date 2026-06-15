@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="d-flex align-items-center">
                    <div class="welcome-icon me-3">
                        <i class="fas fa-sun text-warning"></i>
                    </div>
                    <div>
                        <h2 class="page-title mb-1">Good morning, {{ $employee->firstname }}!</h2>
                        <p class="text-muted mb-0">Here's what's happening with your work today</p>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="user-profile d-flex align-items-center">
                    <div class="avatar avatar-lg me-3">
                        <span class="avatar-title rounded-circle bg-gradient-primary text-white">
                            {{ substr($employee->firstname, 0, 1) }}{{ substr($employee->lastname, 0, 1) }}
                        </span>
                    </div>
                    <div class="d-none d-md-block">
                        <h6 class="mb-0">{{ $employee->firstname }} {{ $employee->lastname }}</h6>
                        <small class="text-muted">{{ $employee->position ?? 'Employee' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Today's Schedule Alert -->
    @if($todaysShift)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert-card {{ $todaysShift->status === 'upcoming' ? 'alert-info' : ($todaysShift->status === 'in_progress' ? 'alert-success' : 'alert-secondary') }}">
                <div class="alert-icon">
                    @if($todaysShift->status === 'upcoming')
                        <i class="fas fa-clock"></i>
                    @elseif($todaysShift->status === 'in_progress')
                        <i class="fas fa-play-circle"></i>
                    @else
                        <i class="fas fa-check-circle"></i>
                    @endif
                </div>
                <div class="alert-content">
                    <h5 class="alert-title">
                        @if($todaysShift->status === 'upcoming')
                            Your shift starts soon!
                        @elseif($todaysShift->status === 'in_progress')
                            You're currently on shift
                        @else
                            Today's shift completed
                        @endif
                    </h5>
                    <p class="alert-message">
                        <strong>{{ $todaysShift->shift_name }}</strong> - 
                        {{ $todaysShift->start_time_formatted }} to {{ $todaysShift->end_time_formatted }}
                        <br>
                        <small class="text-muted">
                            Duration: {{ $todaysShift->shift_duration }}
                            @if($todaysShift->break_duration && $todaysShift->break_duration != '0 minutes')
                                • Break: {{ $todaysShift->break_duration }}
                            @endif
                        </small>
                    </p>
                    <div class="alert-countdown">
                        <span class="badge bg-light text-dark">{{ $todaysShift->time_until }}</span>
                        @if($todaysShift->status === 'in_progress')
                            <span class="badge bg-success text-white ms-2">
                                <i class="fas fa-circle pulse"></i> Active Now
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert-card alert-light">
                <div class="alert-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <div class="alert-content">
                    <h5 class="alert-title">No shift today</h5>
                    <p class="alert-message">
                        You don't have a scheduled shift for today ({{ \Carbon\Carbon::now()->format('l, M d, Y') }}).
                        <br>
                        <small class="text-muted">Enjoy your day off!</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Next Schedule Alert -->
    @if($nextSchedule)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert-card {{ $nextSchedule->status === 'upcoming' ? 'alert-primary' : 'alert-warning' }}">
                <div class="alert-icon">
                    @if($nextSchedule->status === 'upcoming')
                        <i class="fas fa-calendar-plus"></i>
                    @else
                        <i class="fas fa-exchange-alt"></i>
                    @endif
                </div>
                <div class="alert-content">
                    <h5 class="alert-title">
                        @if($nextSchedule->status === 'upcoming')
                            Next Schedule Coming Up!
                        @else
                            Schedule Change Ahead
                        @endif
                    </h5>
                    <p class="alert-message">
                        <strong>{{ $nextSchedule->shift_name }}</strong> - 
                        {{ $nextSchedule->start_time_formatted }} to {{ $nextSchedule->end_time_formatted }}
                        <br>
                        <small class="text-muted">
                            {{ $nextSchedule->formatted_start_date }} to {{ $nextSchedule->formatted_end_date }}
                            @if($nextSchedule->days_formatted)
                                • Working Days: {{ $nextSchedule->days_formatted }}
                            @endif
                        </small>
                    </p>
                    <div class="alert-countdown">
                        <span class="badge bg-light text-dark">{{ $nextSchedule->status_text }}</span>
                        @if($nextSchedule->is_schedule_change)
                            <span class="badge bg-warning text-dark ms-2">
                                <i class="fas fa-exclamation-triangle"></i> Schedule Change
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Stats Row -->
    <div class="row mb-4 g-4">
        <div class="col-lg-3 col-md-6">
            <div class="stats-card shadow-sm hover-scale">
                <div class="stats-icon bg-gradient-primary">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stats-content">
                    <h3 class="gradient-text">{{ count($upcomingHolidays) }}</h3>
                    <p>Upcoming Holidays</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card shadow-sm hover-scale">
                <div class="stats-icon bg-gradient-success">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stats-content">
                    <h3 class="gradient-text">{{ $ticketStats['total'] }}</h3>
                    <p>Total Tickets</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card shadow-sm hover-scale">
                <div class="stats-icon bg-gradient-warning">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <div class="stats-content">
                    <h3 class="gradient-text">{{ $projectStats['active'] }}</h3>
                    <p>Active Projects</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-card shadow-sm hover-scale">
                <div class="stats-icon bg-gradient-info">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stats-content">
                    <h3 class="gradient-text">{{ $ticketStats['open'] }}</h3>
                    <p>Open Tasks</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-4">
        <!-- Current Work Schedule Card -->
        <div class="col-xl-4 col-lg-6 mb-4">
            
        </div>
{{-- Add these sections to your existing index.blade.php after the existing cards --}}

{{-- Birthday Wishes Section (only show if it's user's birthday) --}}
@if($isBirthdayToday && count($myBirthdayWishes) > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="alert-card alert-success">
            <div class="alert-icon">
                <i class="fas fa-birthday-cake"></i>
            </div>
            <div class="alert-content">
                <h5 class="alert-title">🎉 Happy Birthday, {{ $employee->firstname }}!</h5>
                <p class="alert-message">
                    You've received {{ count($myBirthdayWishes) }} birthday wish{{ count($myBirthdayWishes) > 1 ? 'es' : '' }} from your colleagues today!
                </p>
                <div class="birthday-wishes-preview">
                    @foreach($myBirthdayWishes->take(3) as $wish)
                        <div class="wish-item">
                            <div class="wish-sender">
                                <div class="sender-avatar">
                                    @if($wish->sender_image)
                                        <img src="{{ asset('storage/' . $wish->sender_image) }}" alt="{{ $wish->sender_name }}">
                                    @else
                                        <span>{{ substr($wish->sender_firstname, 0, 1) }}{{ substr($wish->sender_lastname, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div class="sender-info">
                                    <strong>{{ $wish->sender_name }}</strong>
                                    <small>{{ $wish->sender_department }}</small>
                                </div>
                            </div>
                            <div class="wish-message">
                                "{{ $wish->message }}"
                            </div>
                        </div>
                    @endforeach
                    @if(count($myBirthdayWishes) > 3)
                        <div class="text-center mt-2">
                            <button class="btn-link-modern" onclick="showAllBirthdayWishes()">
                                View all {{ count($myBirthdayWishes) }} wishes
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Anniversary Wishes Section (only show if it's user's anniversary) --}}
@if($isAnniversaryToday && count($myAnniversaryWishes) > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="alert-card alert-warning">
            <div class="alert-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="alert-content">
                <h5 class="alert-title">🏆 Happy Work Anniversary, {{ $employee->firstname }}!</h5>
                <p class="alert-message">
                    You've received {{ count($myAnniversaryWishes) }} anniversary wish{{ count($myAnniversaryWishes) > 1 ? 'es' : '' }} from your colleagues today!
                </p>
                <div class="anniversary-wishes-preview">
                    @foreach($myAnniversaryWishes->take(3) as $wish)
                        <div class="wish-item">
                            <div class="wish-sender">
                                <div class="sender-avatar">
                                    @if($wish->sender_image)
                                        <img src="{{ asset('storage/' . $wish->sender_image) }}" alt="{{ $wish->sender_name }}">
                                    @else
                                        <span>{{ substr($wish->sender_firstname, 0, 1) }}{{ substr($wish->sender_lastname, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div class="sender-info">
                                    <strong>{{ $wish->sender_name }}</strong>
                                    <small>{{ $wish->sender_department }}</small>
                                </div>
                            </div>
                            <div class="wish-message">
                                "{{ $wish->message }}"
                            </div>
                        </div>
                    @endforeach
                    @if(count($myAnniversaryWishes) > 3)
                        <div class="text-center mt-2">
                            <button class="btn-link-modern" onclick="showAllAnniversaryWishes()">
                                View all {{ count($myAnniversaryWishes) }} wishes
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Recent Congratulations Card --}}
<div class="col-xl-6 col-lg-6 mb-4">
    <div class="modern-card shadow-sm hover-scale">
        <div class="card-header-modern">
            <div class="d-flex justify-content-between align-items-center">
                <div class="card-title-group">
                    <h5 class="card-title-modern">Recent Congratulations</h5>
                    <p class="card-subtitle">Colleagues celebrating your success</p>
                </div>
                <div class="card-badge">
                    <span class="badge-modern bg-gradient-info">{{ count($achievementCongratulations) }}</span>
                </div>
            </div>
        </div>
        <div class="card-body-modern">
            @if(count($achievementCongratulations) > 0)
                <div class="congratulations-list">
                    @foreach($achievementCongratulations->take(5) as $congrats)
                        <div class="congratulation-item-modern">
                            <div class="congratulation-sender">
                                <div class="sender-avatar">
                                    @if($congrats->sender_image)
                                        <img src="{{ asset('storage/' . $congrats->sender_image) }}" alt="{{ $congrats->sender_name }}">
                                    @else
                                        <span>{{ substr($congrats->sender_firstname, 0, 1) }}{{ substr($congrats->sender_lastname, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div class="congratulation-content">
                                    <div class="congratulation-header">
                                        <strong>{{ $congrats->sender_name }}</strong>
                                        <span class="congratulation-achievement">
                                            {{ $congrats->achievement_icon ?? '🏆' }} {{ Str::limit($congrats->achievement_title, 30) }}
                                        </span>
                                    </div>
                                    <div class="congratulation-message">
                                        "{{ $congrats->message }}"
                                    </div>
                                    <div class="congratulation-meta">
                                        <small class="text-muted">{{ $congrats->time_ago }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if(count($achievementCongratulations) > 5)
                    <div class="text-center mt-3">
                        <a href="{{ route('community.index') }}" class="btn-link-modern">
                            View all congratulations
                        </a>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-hands-clapping"></i>
                    </div>
                    <h6>No congratulations yet</h6>
                    <p>Add some achievements to receive congratulations from colleagues!</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Community Stats Card --}}
<div class="col-xl-4 col-lg-6 mb-4">
    <!-- <div class="modern-card shadow-sm hover-scale">
        <div class="card-header-modern">
            <div class="card-title-group">
                <h5 class="card-title-modern">Community Activity</h5>
                <p class="card-subtitle">Your social engagement</p>
            </div>
        </div>
        <div class="card-body-modern">
            <div class="community-stats-grid">
                <div class="community-stat-item">
                    <div class="stat-icon bg-gradient-success">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-details">
                        <h3 class="gradient-text">{{ $communityStats['wishes_received_today'] }}</h3>
                        <p>Wishes Received Today</p>
                    </div>
                </div>
                <div class="community-stat-item">
                    <div class="stat-icon bg-gradient-primary">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div class="stat-details">
                        <h3 class="gradient-text">{{ $communityStats['wishes_sent_today'] }}</h3>
                        <p>Wishes Sent Today</p>
                    </div>
                </div>
                <div class="community-stat-item">
                    <div class="stat-icon bg-gradient-warning">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-details">
                        <h3 class="gradient-text">{{ $communityStats['total_achievements'] }}</h3>
                        <p>Total Achievements</p>
                    </div>
                </div>
                <div class="community-stat-item">
                    <div class="stat-icon bg-gradient-info">
                        <i class="fas fa-hands-clapping"></i>
                    </div>
                    <div class="stat-details">
                        <h3 class="gradient-text">{{ $communityStats['congratulations_received'] }}</h3>
                        <p>Congratulations This Month</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="{{ route('community.index') }}" class="btn-modern btn-outline">
                    <i class="fas fa-users me-1"></i>Visit Community
                </a>
            </div>
        </div>
    </div> -->
    <div class="modern-card shadow-sm hover-scale">
                <div class="card-header-modern">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="card-title-group">
                            <h5 class="card-title-modern">Current Schedule</h5>
                            <p class="card-subtitle">Your regular work pattern</p>
                        </div>
                        <a href="{{ route('scheduling.index') }}" class="btn-link-modern">View Details</a>
                    </div>
                </div>
                <div class="card-body-modern">
                    @if($employeeSchedule)
                        <div class="schedule-card">
                            <div class="schedule-header">
                                <h4 class="schedule-name">{{ $employeeSchedule->shift_name }}</h4>
                                <span class="schedule-status-badge 
                                    @if(\Carbon\Carbon::parse($employeeSchedule->schedule_end_date)->diffInDays(\Carbon\Carbon::now()) <= 7)
                                        ending-soon
                                    @else
                                        active
                                    @endif">
                                    @if(\Carbon\Carbon::parse($employeeSchedule->schedule_end_date)->diffInDays(\Carbon\Carbon::now()) <= 7)
                                        Ending Soon
                                    @else
                                        Active
                                    @endif
                                </span>
                            </div>
                            <div class="schedule-timings">
                                <div class="timing-item">
                                    <i class="fas fa-clock"></i>
                                    <div>
                                        <span class="timing-label">Working Hours</span>
                                        <span class="timing-value">{{ $employeeSchedule->start_time_formatted }} - {{ $employeeSchedule->end_time_formatted }}</span>
                                    </div>
                                </div>
                                <div class="timing-item">
                                    <i class="fas fa-coffee"></i>
                                    <div>
                                        <span class="timing-label">Break Time</span>
                                        <span class="timing-value">{{ $employeeSchedule->break_time }} minutes</span>
                                    </div>
                                </div>
                            </div>
                            <div class="schedule-days">
                                <h6>Working Days:</h6>
                                <div class="days-list">
                                    {{ $employeeSchedule->days_formatted ?? 'Not specified' }}
                                </div>
                            </div>
                            <div class="schedule-duration">
                                <span>Daily Duration:</span>
                                <strong>{{ $employeeSchedule->duration }}</strong>
                            </div>
                            <div class="schedule-period">
                                <div class="period-info">
                                    <span class="period-label">Schedule Period:</span>
                                    <div class="period-dates">
                                        <strong>{{ \Carbon\Carbon::parse($employeeSchedule->schedule_start_date)->format('M d, Y') }}</strong>
                                        to
                                        <strong>{{ \Carbon\Carbon::parse($employeeSchedule->schedule_end_date)->format('M d, Y') }}</strong>
                                    </div>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($employeeSchedule->schedule_end_date)->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h6>No schedule assigned</h6>
                            <p>You don't have a regular work schedule assigned</p>
                        </div>
                    @endif
                </div>
            </div>
</div>

<style>
/* Personal Dashboard Styles */
.birthday-wishes-preview,
.anniversary-wishes-preview {
    margin-top: 1rem;
}

.wish-item {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 8px;
    margin-bottom: 0.75rem;
    transition: var(--transition);
}

.wish-item:hover {
    background: rgba(255, 255, 255, 0.95);
    transform: translateX(4px);
}

.wish-sender {
    display: flex;
    align-items: center;
    margin-right: 1rem;
    min-width: 120px;
}

.sender-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
    margin-right: 0.75rem;
    overflow: hidden;
}

.sender-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.sender-info strong {
    display: block;
    font-size: 0.875rem;
    color: #2d3748;
}

.sender-info small {
    color: #718096;
    font-size: 0.75rem;
}

.wish-message {
    flex: 1;
    font-style: italic;
    color: #4a5568;
    font-size: 0.875rem;
    line-height: 1.4;
}

/* Achievements Styles */
.achievements-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.achievement-item-modern {
    display: flex;
    align-items: flex-start;
    padding: 1.25rem;
    background: #f7fafc;
    border-radius: 12px;
    transition: var(--transition);
}

.achievement-item-modern:hover {
    background: #edf2f7;
    transform: translateY(-2px);
}

.achievement-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #ffd89b 0%, #19547b 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-right: 1rem;
    flex-shrink: 0;
}

.achievement-content {
    flex: 1;
}

.achievement-title {
    font-size: 1rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
}

.congratulations-count {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
    color: #718096;
}

.achievement-description {
    color: #4a5568;
    font-size: 0.875rem;
    margin: 0.5rem 0;
    line-height: 1.4;
}

.achievement-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.75rem;
}

.category-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.category-education { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.category-certification { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.category-skill { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.category-project { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
.category-award { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
.category-other { background: rgba(107, 114, 128, 0.1); color: #6b7280; }

.achievement-time {
    font-size: 0.75rem;
    color: #a0aec0;
}

/* Congratulations Styles */
.congratulations-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.congratulation-item-modern {
    padding: 1rem;
    background: #f7fafc;
    border-radius: 8px;
    transition: var(--transition);
}

.congratulation-item-modern:hover {
    background: #edf2f7;
    transform: translateX(4px);
}

.congratulation-sender {
    display: flex;
    align-items: flex-start;
}

.congratulation-content {
    flex: 1;
    margin-left: 0.75rem;
}

.congratulation-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.congratulation-header strong {
    color: #2d3748;
    font-size: 0.875rem;
}

.congratulation-achievement {
    font-size: 0.75rem;
    color: #718096;
    background: rgba(102, 126, 234, 0.1);
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
}

.congratulation-message {
    font-style: italic;
    color: #4a5568;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

.congratulation-meta {
    text-align: right;
}

/* Community Stats Grid */
.community-stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.community-stat-item {
    text-align: center;
    padding: 1rem;
    background: #f7fafc;
    border-radius: 8px;
    transition: var(--transition);
}

.community-stat-item:hover {
    background: #edf2f7;
    transform: translateY(-2px);
}

.community-stat-item .stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
    color: white;
    font-size: 1rem;
}

.community-stat-item .stat-details h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
}

.community-stat-item .stat-details p {
    color: #718096;
    font-size: 0.75rem;
    margin: 0;
    line-height: 1.2;
}

/* Responsive Design */
@media (max-width: 768px) {
    .wish-item {
        flex-direction: column;
        text-align: center;
    }
    
    .wish-sender {
        margin-right: 0;
        margin-bottom: 0.75rem;
        min-width: auto;
    }
    
    .achievement-item-modern {
        flex-direction: column;
        text-align: center;
    }
    
    .achievement-icon {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .community-stats-grid {
        grid-template-columns: 1fr;
    }
    
    .congratulation-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>

<script>
// Functions for showing modals and handling interactions
function showAllBirthdayWishes() {
    // Implementation to show all birthday wishes in a modal
    console.log('Show all birthday wishes modal');
}

function showAllAnniversaryWishes() {
    // Implementation to show all anniversary wishes in a modal
    console.log('Show all anniversary wishes modal');
}

function showAddAchievementModal() {
    // Implementation to show add achievement modal
    console.log('Show add achievement modal');
}
</script>
        <!-- Enhanced Next Schedule Card -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="modern-card shadow-sm hover-scale">
                <div class="card-header-modern">
                    <div class="card-title-group">
                        <h5 class="card-title-modern">
                            <i class="fas fa-calendar-alt me-2"></i>Next Schedule
                        </h5>
                        <p class="card-subtitle">Your upcoming work schedule</p>
                    </div>
                </div>
                <div class="card-body-modern">
                    @if($nextSchedule)
                        <div class="next-schedule-card">
                            <div class="schedule-header">
                                <h4 class="schedule-name">{{ $nextSchedule->shift_name }}</h4>
                                <span class="schedule-status-badge {{ $nextSchedule->status === 'upcoming' ? 'upcoming' : 'changing' }}">
                                    @if($nextSchedule->status === 'upcoming')
                                        <i class="fas fa-clock me-1"></i>Upcoming
                                    @else
                                        <i class="fas fa-exchange-alt me-1"></i>Changing
                                    @endif
                                </span>
                            </div>
                            
                            <div class="schedule-details">
                                <div class="detail-item">
                                    <i class="fas fa-clock text-primary"></i>
                                    <div>
                                        <span class="detail-label">Working Hours</span>
                                        <span class="detail-value">{{ $nextSchedule->start_time_formatted }} - {{ $nextSchedule->end_time_formatted }}</span>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <i class="fas fa-calendar text-success"></i>
                                    <div>
                                        <span class="detail-label">Schedule Period</span>
                                        <span class="detail-value">{{ $nextSchedule->formatted_start_date }} to {{ $nextSchedule->formatted_end_date }}</span>
                                    </div>
                                </div>
                                
                                @if($nextSchedule->days_formatted)
                                <div class="detail-item">
                                    <i class="fas fa-calendar-week text-info"></i>
                                    <div>
                                        <span class="detail-label">Working Days</span>
                                        <span class="detail-value">{{ $nextSchedule->days_formatted }}</span>
                                    </div>
                                </div>
                                @endif
                                
                                <div class="detail-item">
                                    <i class="fas fa-hourglass-half text-warning"></i>
                                    <div>
                                        <span class="detail-label">Daily Duration</span>
                                        <span class="detail-value">{{ $nextSchedule->duration }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="schedule-countdown">
                                <div class="countdown-badge {{ $nextSchedule->days_until_start <= 3 ? 'urgent' : 'normal' }}">
                                    @if($nextSchedule->days_until_start > 0)
                                        <i class="fas fa-calendar-day me-1"></i>
                                        Starts in {{ $nextSchedule->days_until_start }} day{{ $nextSchedule->days_until_start > 1 ? 's' : '' }}
                                    @elseif($nextSchedule->days_until_start == 0)
                                        <i class="fas fa-play me-1"></i>
                                        Starts Today!
                                    @else
                                        <i class="fas fa-check me-1"></i>
                                        {{ $nextSchedule->status_text }}
                                    @endif
                                </div>
                            </div>
                            
                            @if($nextSchedule->is_schedule_change)
                            <div class="schedule-change-notice">
                                <div class="notice-badge">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Schedule Change
                                </div>
                                <small class="text-muted">This schedule is different from your current one. Please review the new timings and working days.</small>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-calendar-plus"></i>
                            </div>
                            <h6>No upcoming schedule</h6>
                            <p>You don't have any scheduled shifts coming up</p>
                            <small class="text-muted">Contact HR if you need a schedule assignment</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Weekly Schedule Preview -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="modern-card shadow-sm hover-scale">
                <div class="card-header-modern">
                    <div class="card-title-group">
                        <h5 class="card-title-modern">This Week</h5>
                        <p class="card-subtitle">{{ \Carbon\Carbon::now()->format('M d') }} - {{ \Carbon\Carbon::now()->endOfWeek()->format('M d, Y') }}</p>
                    </div>
                </div>
                <div class="card-body-modern">
                    @if($weeklySchedule && count($weeklySchedule) > 0)
                        <div class="weekly-schedule">
                            @foreach($weeklySchedule as $day)
                                <div class="day-schedule {{ $day['is_today'] ? 'today' : '' }} {{ $day['has_shift'] ? 'has-shift' : 'no-shift' }}">
                                    <div class="day-header">
                                        <span class="day-name">{{ $day['day_name'] }}</span>
                                        <span class="day-date">{{ $day['date'] }}</span>
                                    </div>
                                    @if($day['has_shift'])
                                        <div class="day-shift">
                                            <div class="shift-time">
                                                {{ $day['start_time'] }} - {{ $day['end_time'] }}
                                            </div>
                                            <div class="shift-duration">
                                                {{ $day['duration'] }}
                                            </div>
                                        </div>
                                    @else
                                        <div class="day-off">
                                            <i class="fas fa-moon"></i>
                                            <span>Day Off</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-calendar-week"></i>
                            </div>
                            <h6>No schedule this week</h6>
                            <p>You don't have any scheduled shifts this week</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Holidays Card -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="modern-card shadow-sm hover-scale">
                <div class="card-header-modern">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="card-title-group">
                            <h5 class="card-title-modern">Upcoming Holidays</h5>
                            <p class="card-subtitle">Next celebrations</p>
                        </div>
                        <div class="card-badge">
                            <span class="badge-modern bg-gradient-primary">{{ count($upcomingHolidays) }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body-modern">
                    @if(count($upcomingHolidays) > 0)
                        <div class="holiday-timeline">
                            @foreach($upcomingHolidays->take(3) as $holiday)
                                <div class="holiday-item-modern {{ $holiday->is_next ? 'next-holiday-modern' : '' }}">
                                    <div class="holiday-date-modern">
                                        <span class="date">{{ Carbon\Carbon::parse($holiday->holidaydate)->format('d') }}</span>
                                        <span class="month">{{ Carbon\Carbon::parse($holiday->holidaydate)->format('M') }}</span>
                                    </div>
                                    <div class="holiday-content">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="holiday-title">{{ $holiday->title }}</h6>
                                           
                                        </div>
                                        <p class="holiday-day">{{ $holiday->day }}</p>
                                        <p class="holiday-time">{{ Carbon\Carbon::parse($holiday->holidaydate)->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if(count($upcomingHolidays) > 3)
                            <div class="text-center mt-3">
                                <a href="#" class="btn-link-modern">View all holidays</a>
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-umbrella-beach"></i>
                            </div>
                            <h6>No upcoming holidays</h6>
                            <p>Check back later for updates</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Ticket Management Card -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="modern-card shadow-sm hover-scale">
                <div class="card-header-modern">
                    <div class="card-title-group">
                        <h5 class="card-title-modern">Ticket Overview</h5>
                        <p class="card-subtitle">Your support requests</p>
                    </div>
                </div>
                <div class="card-body-modern">
                    <div class="ticket-overview">
                        <div class="ticket-total">
                            <h2 class="total-number gradient-text">{{ $ticketStats['total'] }}</h2>
                            <p class="total-label">Total Tickets</p>
                        </div>
                        <div class="ticket-breakdown">
                            <div class="ticket-stat">
                                <div class="stat-indicator bg-gradient-primary"></div>
                                <div class="stat-content">
                                    <span class="stat-number gradient-text">{{ $ticketStats['open'] }}</span>
                                    <span class="stat-label">Open</span>
                                </div>
                            </div>
                            <div class="ticket-stat">
                                <div class="stat-indicator bg-gradient-warning"></div>
                                <div class="stat-content">
                                    <span class="stat-number gradient-text">{{ $ticketStats['in_progress'] ?? 0 }}</span>
                                    <span class="stat-label">In Progress</span>
                                </div>
                            </div>
                            <div class="ticket-stat">
                                <div class="stat-indicator bg-gradient-success"></div>
                                <div class="stat-content">
                                    <span class="stat-number gradient-text">{{ $ticketStats['closed'] }}</span>
                                    <span class="stat-label">Closed</span>
                                </div>
                            </div>
                        </div>
                        <div class="ticket-actions mt-3">
                            <a href="{{ route('tickets.index') }}" class="btn-modern btn-outline"><i class="fas fa-list me-1"></i>View All</a>
                            <a href="{{ route('tickets.create') }}" class="btn-modern btn-primary"><i class="fas fa-plus me-1"></i>New Ticket</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Tickets Card -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="modern-card shadow-sm hover-scale">
                <div class="card-header-modern">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="card-title-group">
                            <h5 class="card-title-modern">Recent Tickets</h5>
                            <p class="card-subtitle">Latest activity</p>
                        </div>
                        <a href="{{ route('tickets.index') }}" class="btn-link-modern">View All</a>
                    </div>
                </div>
                <div class="card-body-modern">
                    @if(count($recentTickets) > 0)
                        <div class="ticket-list">
                            @foreach($recentTickets->take(3) as $ticket)
                                <div class="ticket-item-modern">
                                    <div class="ticket-priority priority-{{ strtolower($ticket->priority) }}"></div>
                                   
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <h6>No recent tickets</h6>
                            <p>You haven't created or been assigned any tickets recently</p>
                            <a href="{{ route('tickets.create') }}" class="btn-modern btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Create Ticket</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Project Statistics Card -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="modern-card shadow-sm hover-scale">
                <div class="card-header-modern">
                    <div class="card-title-group">
                        <h5 class="card-title-modern">Project Overview</h5>
                        <p class="card-subtitle">Your active work</p>
                    </div>
                </div>
                <div class="card-body-modern">
                    <div class="project-overview">
                        <div class="project-stats-grid">
                            <div class="project-stat-item">
                                <div class="stat-icon bg-gradient-primary">
                                    <i class="fas fa-play"></i>
                                </div>
                                <div class="stat-details">
                                    <h3 class="gradient-text">{{ $projectStats['active'] }}</h3>
                                    <p>Active Projects</p>
                                </div>
                            </div>
                            <div class="project-stat-item">
                                <div class="stat-icon bg-gradient-success">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="stat-details">
                                    <h3 class="gradient-text">{{ $projectStats['completed'] }}</h3>
                                    <p>Completed</p>
                                </div>
                            </div>
                            <div class="project-stat-item">
                                <div class="stat-icon bg-gradient-info">
                                    <i class="fas fa-list"></i>
                                </div>
                                <div class="stat-details">
                                    <h3 class="gradient-text">{{ $projectStats['total'] }}</h3>
                                    <p>Total Projects</p>
                                </div>
                            </div>
                        </div>
                        <div class="progress-section mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="progress-label">Completion Rate</span>
                                <span class="progress-percentage gradient-text">{{ $projectStats['total'] > 0 ? round(($projectStats['completed']/$projectStats['total'])*100) : 0 }}%</span>
                            </div>
                            <div class="progress-modern">
                                <div class="progress-bar-modern"
                                     style="width: {{ $projectStats['total'] > 0 ? ($projectStats['completed']/$projectStats['total'])*100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Projects Card -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="modern-card shadow-sm hover-scale">
                <div class="card-header-modern">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="card-title-group">
                            <h5 class="card-title-modern">Active Projects</h5>
                            <p class="card-subtitle">Current assignments</p>
                        </div>
                        <a href="{{ route('projects.index') }}" class="btn-link-modern">View All</a>
                    </div>
                </div>
                <div class="card-body-modern">
                    @if(count($recentProjects) > 0)
                        <div class="project-list">
                            @foreach($recentProjects->take(3) as $project)
                                <div class="project-item-modern">
                                    <div class="project-header">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h6 class="project-name">{{ $project->projectname }}</h6>
                                            @if($project->isLeader)
                                                <span class="leader-badge">
                                                    <i class="fas fa-crown"></i> Leader
                                                </span>
                                            @endif
                                        </div>
                                        <div class="project-meta">
                                            <span class="due-date">
                                                <i class="far fa-calendar"></i>
                                                Due {{ \Carbon\Carbon::parse($project->enddate)->format('M d, Y') }}
                                            </span>
                                            <span class="time-remaining {{ \Carbon\Carbon::parse($project->enddate)->isPast() ? 'overdue' : '' }}">
                                                {{ \Carbon\Carbon::parse($project->enddate)->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="project-team mt-2">
                                        <div class="team-avatars">
                                            @foreach(array_slice($project->teamMembers, 0, 4) as $member)
                                                <div class="team-avatar" data-bs-toggle="tooltip" title="{{ $member }}">
                                                    <span>{{ substr(explode(' ', $member)[0], 0, 1) }}{{ isset(explode(' ', $member)[1]) ? substr(explode(' ', $member)[1], 0, 1) : '' }}</span>
                                                </div>
                                            @endforeach
                                            @if(count($project->teamMembers) > 4)
                                                <div class="team-avatar more">
                                                    <span>+{{ count($project->teamMembers) - 4 }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <h6>No active projects</h6>
                            <p>You're not currently assigned to any active projects</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --primary-color: #667eea;
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-color: #48bb78;
    --success-gradient: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    --warning-color: #ed8936;
    --warning-gradient: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
    --danger-color: #f56565;
    --danger-gradient: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
    --info-color: #4299e1;
    --info-gradient: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
    --card-shadow: 0 4px 16px 0 rgba(102, 126, 234, 0.08), 0 2px 4px 0 rgba(0,0,0,0.04);
    --card-shadow-hover: 0 12px 32px 0 rgba(102, 126, 234, 0.15), 0 4px 8px 0 rgba(0,0,0,0.06);
    --border-radius: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Utility Classes */
.hover-scale {
    transition: var(--transition);
}

.hover-scale:hover {
    transform: scale(1.025);
    box-shadow: var(--card-shadow-hover) !important;
    z-index: 2;
}

.gradient-text {
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    color: transparent;
}

/* Page Header */
.page-header {
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    border-radius: var(--border-radius);
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--card-shadow);
}

.welcome-icon {
    width: 56px;
    height: 56px;
    background: rgba(255, 193, 7, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin: 0;
}

/* Alert Cards */
.alert-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--card-shadow);
    display: flex;
    align-items: center;
    gap: 1rem;
    border-left: 4px solid;
    transition: var(--transition);
}

.alert-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--card-shadow-hover);
}

.alert-card.alert-info {
    border-left-color: var(--info-color);
    background: linear-gradient(135deg, #ebf8ff 0%, #bee3f8 100%);
}

.alert-card.alert-success {
    border-left-color: var(--success-color);
    background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
}

.alert-card.alert-secondary {
    border-left-color: #718096;
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
}

.alert-card.alert-light {
    border-left-color: #f8f9fa;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.alert-card.alert-primary {
    border-left-color: var(--primary-color);
    background: linear-gradient(135deg, #ebf4ff 0%, #dbeafe 100%);
}

.alert-card.alert-warning {
    border-left-color: var(--warning-color);
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
}

.alert-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.alert-info .alert-icon {
    background: var(--info-gradient);
}

.alert-success .alert-icon {
    background: var(--success-gradient);
}

.alert-secondary .alert-icon {
    background: linear-gradient(135deg, #718096 0%, #4a5568 100%);
}

.alert-light .alert-icon {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
}

.alert-primary .alert-icon {
    background: var(--primary-gradient);
}

.alert-warning .alert-icon {
    background: var(--warning-gradient);
}

.alert-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0 0 0.5rem 0;
    color: #2d3748;
}

.alert-message {
    margin: 0;
    color: #4a5568;
}

.alert-countdown {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

/* Stats Cards */
.stats-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.75rem;
    box-shadow: var(--card-shadow);
    display: flex;
    align-items: center;
    gap: 1.5rem;
    height: 100%;
    transition: var(--transition);
}

.stats-icon {
    width: 64px;
    height: 64px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.75rem;
}

.bg-gradient-primary { background: var(--primary-gradient); }
.bg-gradient-success { background: var(--success-gradient); }
.bg-gradient-warning { background: var(--warning-gradient); }
.bg-gradient-danger { background: var(--danger-gradient); }
.bg-gradient-info { background: var(--info-gradient); }

.stats-content h3 {
    font-size: 2.25rem;
    font-weight: 700;
    margin: 0;
}

.stats-content p {
    margin: 0;
    color: #718096;
    font-weight: 500;
    font-size: 1rem;
}

/* Modern Cards */
.modern-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    transition: var(--transition);
    overflow: hidden;
    height: 100%;
}

.card-header-modern {
    padding: 1.75rem 1.75rem 0;
    border-bottom: none;
}

.card-title-modern {
    font-size: 1.375rem;
    font-weight: 700;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin: 0;
}

.card-subtitle {
    color: #718096;
    font-size: 0.875rem;
    margin: 0.25rem 0 0;
}

.card-body-modern {
    padding: 1.75rem;
}

.card-badge .badge-modern {
    background: var(--primary-gradient);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.btn-link-modern {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
}

.btn-link-modern:hover {
    color: #5a67d8;
}

/* Next Schedule Specific Styles */
.next-schedule-card {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e2e8f0;
}

.schedule-status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.schedule-status-badge.upcoming {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.2);
}

.schedule-status-badge.changing {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.2);
}

.schedule-status-badge.active {
    background: rgba(72, 187, 120, 0.1);
    color: #48bb78;
}

.schedule-status-badge.ending-soon {
    background: rgba(237, 137, 54, 0.1);
    color: #ed8936;
}

.schedule-details {
    margin: 1.5rem 0;
}

.detail-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1rem;
    padding: 0.75rem;
    background: white;
    border-radius: 8px;
    transition: var(--transition);
}

.detail-item:hover {
    transform: translateX(4px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.detail-item i {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.1rem;
    background: rgba(255, 255, 255, 0.8);
}

.detail-item .detail-label {
    display: block;
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-item .detail-value {
    display: block;
    font-size: 0.875rem;
    color: #1e293b;
    font-weight: 600;
    margin-top: 0.25rem;
}

.schedule-countdown {
    margin: 1.5rem 0 1rem;
    text-align: center;
}

.countdown-badge {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: var(--transition);
}

.countdown-badge.normal {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.countdown-badge.urgent {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.schedule-change-notice {
    margin-top: 1rem;
    padding: 1rem;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-radius: 8px;
    border: 1px solid #f59e0b;
}

.notice-badge {
    display: inline-block;
    background: rgba(245, 158, 11, 0.2);
    color: #92400e;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

/* Schedule Card Styles */
.schedule-card {
    background: #f7fafc;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
}

.schedule-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.schedule-name {
    font-size: 1.25rem;
    font-weight: 600;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin: 0;
}

.schedule-timings {
    margin-bottom: 1rem;
}

.timing-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    padding: 0.75rem;
    background: white;
    border-radius: 8px;
    transition: var(--transition);
}

.timing-item:hover {
    transform: translateX(4px);
    background: #edf2f7;
}

.timing-item i {
    width: 40px;
    height: 40px;
    background: rgba(102, 126, 234, 0.1);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    color: #667eea;
    font-size: 1.25rem;
}

.timing-label {
    display: block;
    font-size: 0.75rem;
    color: #718096;
}

.timing-value {
    display: block;
    font-size: 1rem;
    font-weight: 600;
    color: #2d3748;
}

.schedule-days {
    margin: 1.5rem 0;
    padding: 1rem;
    background: #edf2f7;
    border-radius: 8px;
}

.schedule-days h6 {
    font-size: 0.875rem;
    color: #4a5568;
    margin: 0 0 0.5rem 0;
}

.days-list {
    font-size: 0.875rem;
    color: #2d3748;
}

.schedule-duration {
    font-size: 0.875rem;
    color: #4a5568;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.schedule-period {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
}

.period-label {
    font-size: 0.75rem;
    color: #718096;
    display: block;
    margin-bottom: 0.5rem;
}

.period-dates {
    font-size: 0.875rem;
    color: #2d3748;
    margin-bottom: 0.25rem;
}

/* Weekly Schedule Styles */
.weekly-schedule {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.day-schedule {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: #f7fafc;
    border-radius: 8px;
    transition: var(--transition);
}

.day-schedule.today {
    background: linear-gradient(135deg, #ebf8ff 0%, #bee3f8 100%);
    border: 1px solid var(--info-color);
}

.day-schedule.has-shift:hover {
    transform: translateX(4px);
    background: #edf2f7;
}

.day-schedule.today.has-shift:hover {
    background: linear-gradient(135deg, #dbeafe 0%, #93c5fd 100%);
}

.day-header {
    display: flex;
    flex-direction: column;
}

.day-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #2d3748;
}

.day-date {
    font-size: 0.75rem;
    color: #718096;
}

.day-shift {
    text-align: right;
}

.shift-time {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--primary-color);
}

.shift-duration {
    font-size: 0.75rem;
    color: #718096;
}

.day-off {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #a0aec0;
    font-size: 0.875rem;
}

/* Holiday Timeline */
.holiday-timeline {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.holiday-item-modern {
    display: flex;
    align-items: flex-start;
    padding: 1.25rem;
    background: #f7fafc;
    border-radius: 12px;
    transition: var(--transition);
}

.holiday-item-modern:hover {
    transform: translateX(4px);
    background: #edf2f7;
}

.holiday-date-modern {
    width: 64px;
    height: 64px;
    background: var(--primary-gradient);
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-right: 1.25rem;
}

.holiday-date-modern .date {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    line-height: 1;
}

.holiday-date-modern .month {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.9);
    text-transform: uppercase;
    font-weight: 600;
}

.holiday-content .holiday-title {
    font-size: 1rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0 0 0.25rem 0;
}

.holiday-content .holiday-day {
    color: #4a5568;
    font-size: 0.875rem;
    margin: 0 0 0.25rem 0;
}

.holiday-content .holiday-time {
    color: #718096;
    font-size: 0.75rem;
    margin: 0;
}

.next-holiday-modern {
    background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
    border-radius: 12px;
    padding: 1rem;
    margin: 0 -1rem;
}

/* Ticket Overview */
.ticket-overview {
    text-align: center;
}

.ticket-total .total-number {
    font-size: 3rem;
    font-weight: 700;
    margin: 0;
}

.ticket-total .total-label {
    color: #718096;
    margin: 0 0 2rem 0;
}

.ticket-breakdown {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
}

.ticket-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.stat-indicator {
    width: 4px;
    height: 24px;
    border-radius: 2px;
    margin-bottom: 0.5rem;
}

.stat-content .stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
}

.stat-content .stat-label {
    font-size: 0.75rem;
    color: #718096;
    text-transform: uppercase;
    font-weight: 600;
}

.ticket-actions {
    display: flex;
    gap: 0.75rem;
}

.btn-modern {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition);
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-modern.btn-primary {
    background: var(--primary-gradient);
    color: white;
}

.btn-modern.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 15px -3px rgba(102, 126, 234, 0.4);
}

.btn-modern.btn-outline {
    background: transparent;
    border: 2px solid #e2e8f0;
    color: #4a5568;
}

.btn-modern.btn-outline:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
}

/* Ticket List */
.ticket-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.ticket-item-modern {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    background: #f7fafc;
    border-radius: 8px;
    margin-bottom: 0.75rem;
    position: relative;
    transition: var(--transition);
}

.ticket-item-modern:hover {
    background: #edf2f7;
    transform: translateX(4px);
}

.ticket-priority {
    width: 4px;
    height: 100%;
    position: absolute;
    left: 0;
    top: 0;
    border-radius: 0 4px 4px 0;
}

.ticket-priority.priority-high { background: var(--danger-color); }
.ticket-priority.priority-medium { background: var(--warning-color); }
.ticket-priority.priority-low { background: var(--info-color); }

.ticket-content {
    flex: 1;
    margin-left: 1rem;
}

.ticket-subject {
    font-size: 0.875rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
}

.ticket-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.5rem;
}

.priority-label {
    font-size: 0.75rem;
    color: #718096;
}

.ticket-time {
    font-size: 0.75rem;
    color: #a0aec0;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-badge.status-open { background: rgba(66, 153, 225, 0.1); color: #4299e1; }
.status-badge.status-closed { background: rgba(72, 187, 120, 0.1); color: #48bb78; }
.status-badge.status-in-progress { background: rgba(237, 137, 54, 0.1); color: #ed8936; }

/* Project Overview */
.project-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

.project-stat-item {
    text-align: center;
}

.project-stat-item .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
    color: white;
    font-size: 1.25rem;
}

.project-stat-item .stat-details h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
}

.project-stat-item .stat-details p {
    color: #718096;
    font-size: 0.875rem;
    margin: 0;
}

.progress-section {
    margin-top: 1.5rem;
}

.progress-label {
    font-weight: 600;
    color: #4a5568;
}

.progress-percentage {
    font-weight: 700;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.progress-modern {
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar-modern {
    height: 100%;
    background: var(--primary-gradient);
    transition: width 0.6s ease;
}

/* Project List */
.project-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.project-item-modern {
    padding: 1.5rem;
    background: #f7fafc;
    border-radius: 12px;
    margin-bottom: 1rem;
    transition: var(--transition);
}

.project-item-modern:hover {
    background: #edf2f7;
    transform: translateY(-2px);
}

.project-name {
    font-size: 1rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0 0 0.75rem 0;
}

.leader-badge {
    background: linear-gradient(135deg, #ffd89b 0%, #19547b 100%);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.project-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.due-date {
    color: #4a5568;
    font-size: 0.875rem;
}

.time-remaining {
    color: #718096;
    font-size: 0.75rem;
}

.time-remaining.overdue {
    color: var(--danger-color);
    font-weight: 600;
}

.team-avatars {
    display: flex;
    gap: 0.5rem;
}

.team-avatar {
    width: 32px;
    height: 32px;
    background: var(--primary-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.75rem;
    font-weight: 600;
}

.team-avatar.more {
    background: #e2e8f0;
    color: #718096;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 2rem 1rem;
}

.empty-icon {
    width: 64px;
    height: 64px;
    background: #f7fafc;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: #a0aec0;
    font-size: 1.5rem;
}

.empty-state h6 {
    color: #4a5568;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #718096;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

/* Pulse Animation */
.pulse {
    animation: pulse-dot 2s infinite;
}

@keyframes pulse-dot {
    0% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.1); }
    100% { opacity: 1; transform: scale(1); }
}

/* Responsive Design */
@media (max-width: 991.98px) {
    .project-stats-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .page-header {
        padding: 1.5rem;
    }
    
    .stats-card {
        margin-bottom: 1rem;
    }
    
    .holiday-date-modern {
        width: 48px;
        height: 48px;
    }
    
    .holiday-date-modern .date {
        font-size: 1.25rem;
    }
    
    .holiday-date-modern .month {
        font-size: 0.75rem;
    }
    
    .project-list, .ticket-list {
        gap: 0.5rem;
    }
    
    .schedule-card {
        padding: 1rem;
    }
    
    .weekly-schedule {
        gap: 0.5rem;
    }
    
    .day-schedule {
        padding: 0.5rem;
    }
    
    .alert-card {
        flex-direction: column;
        text-align: center;
    }
    
    .alert-icon {
        margin-bottom: 1rem;
        margin-right: 0;
    }
    
    .detail-item {
        padding: 0.5rem;
    }
    
    .detail-item i {
        width: 32px;
        height: 32px;
        font-size: 1rem;
    }
    
    .next-schedule-card {
        padding: 1rem;
    }
}
</style>

<script>
// Update current time every second
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', {
        hour12: true,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    
    // Update any time display elements
    const timeElements = document.querySelectorAll('.current-time');
    timeElements.forEach(element => {
        element.textContent = timeString;
    });
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips if available
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Update time immediately and then every second
    updateTime();
    setInterval(updateTime, 1000);
});

// Add smooth scrolling to anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Add loading states to buttons
document.querySelectorAll('.btn-modern').forEach(button => {
    button.addEventListener('click', function() {
        if (this.type === 'submit' || this.href) {
            this.classList.add('loading');
            setTimeout(() => {
                this.classList.remove('loading');
            }, 2000);
        }
    });
});

// Auto-refresh data every 5 minutes
setInterval(function() {
    // Only refresh if the page is visible
    if (!document.hidden) {
        // You can add AJAX calls here to refresh specific sections
        console.log('Auto-refreshing dashboard data...');
    }
}, 300000); // 5 minutes

// Handle visibility change
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        console.log('Dashboard hidden');
    } else {
        console.log('Dashboard visible');
        updateTime(); // Update time when page becomes visible
    }
});
</script>
@endsection