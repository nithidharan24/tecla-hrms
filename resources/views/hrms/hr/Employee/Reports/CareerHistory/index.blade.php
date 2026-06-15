@extends('layouts.index')

@section('content')



<style>
/* ===== BASE & TYPOGRAPHY ===== */
body * {
    font-family: 'Inter', sans-serif;
    -webkit-font-smoothing: antialiased;
}

/* ===== PAGE CONTAINER ===== */
.career-page-container {
    background: linear-gradient(135deg, #fff8f6 0%, #fff0ed 100%);
    min-height: 100vh;
    padding: 40px 20px;
}

.career-wrapper {
    display: flex;
    gap: 40px;
    max-width: 1400px;
    margin: 0 auto;
    animation: career-fadeUp 0.8s ease-out;
}

@keyframes career-fadeUp {
    from { 
        opacity: 0; 
        transform: translateY(30px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

/* ===== PROFILE CARD - LEFT ===== */
.career-profile-card {
    width: 320px;
    flex-shrink: 0;
    background: white;
    border-radius: 24px;
    padding: 35px 30px;
    box-shadow: 
        0 10px 40px rgba(255, 55, 0, 0.08),
        0 1px 2px rgba(255, 55, 0, 0.03);
    position: sticky;
    top: 40px;
    align-self: flex-start;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
    border: 1px solid rgba(255, 255, 255, 0.9);
}

.career-profile-card:hover {
    transform: translateY(-5px) scale(1.005);
    box-shadow: 
        0 20px 60px rgba(255, 55, 0, 0.12),
        0 2px 4px rgba(255, 55, 0, 0.05);
}

/* Profile Image */
.career-profile-image-wrapper {
    position: relative;
    width: 140px;
    height: 140px;
    margin: 0 auto 25px;
}

.career-profile-image-wrapper::before {
    content: '';
    position: absolute;
    inset: -6px;
    background: linear-gradient(135deg, rgba(255, 55, 0, 0.8), rgba(255, 100, 0, 0.8));
    border-radius: 18px;
    opacity: 0.2;
    z-index: 0;
}

.career-profile-card img {
    width: 100%;
    height: 100%;
    border-radius: 16px;
    object-fit: cover;
    position: relative;
    z-index: 1;
    box-shadow: 0 8px 25px rgba(255, 55, 0, 0.15);
    border: 4px solid white;
}

/* Profile Name */
.career-profile-name {
    font-size: 24px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 6px;
    background: linear-gradient(90deg, rgba(255, 55, 0, 0.8), rgba(255, 100, 0, 0.8));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Profile Details */
.career-profile-details {
    background: #fffaf8;
    border-radius: 16px;
    padding: 20px;
    margin-top: 25px;
    border: 1px solid #ffeae5;
}

.career-profile-detail-item {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #ffeae5;
    transition: all 0.2s;
}

.career-profile-detail-item:hover {
    background: rgba(255, 55, 0, 0.02);
    padding-left: 8px;
    padding-right: 8px;
    margin: 0 -8px;
    border-radius: 8px;
}

.career-profile-detail-item:last-child {
    border-bottom: none;
}

.career-detail-label {
    font-size: 14px;
    color: #7c6b65;
    font-weight: 500;
}

.career-detail-value {
    font-size: 14px;
    color: #2c1f1a;
    font-weight: 600;
    text-align: right;
}

/* ===== TIMELINE CONTAINER - RIGHT ===== */
.career-timeline-container {
    flex: 1;
    background: white;
    padding: 45px 40px;
    border-radius: 24px;
    box-shadow: 
        0 10px 40px rgba(255, 55, 0, 0.06),
        0 1px 2px rgba(255, 55, 0, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.9);
}

/* Year Header */
.career-year-header {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 35px;
    padding-bottom: 15px;
    border-bottom: 2px solid #fff4f0;
}

.career-year-badge {
    font-size: 28px;
    font-weight: 800;
    color: white;
    background: linear-gradient(135deg, rgba(255, 55, 0, 0.8), rgba(255, 100, 0, 0.8));
    padding: 10px 24px;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(255, 55, 0, 0.25);
    min-width: 120px;
    text-align: center;
}

.career-year-count {
    font-size: 16px;
    color: #7c6b65;
    font-weight: 500;
}

.career-year-count strong {
    color: rgba(255, 55, 0, 0.8);
}

/* Timeline */
.career-timeline {
    position: relative;
    padding-left: 60px;
}

.career-timeline::before {
    content: '';
    position: absolute;
    left: 26px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, 
        rgba(255, 55, 0, 0.1) 0%,
        rgba(255, 55, 0, 0.8) 15%,
        rgba(255, 55, 0, 0.8) 85%,
        rgba(255, 55, 0, 0.1) 100%);
    border-radius: 3px;
}

/* Timeline Item */
.career-timeline-item {
    position: relative;
    margin-bottom: 45px;
    opacity: 0;
    animation: career-slideInRight 0.6s ease-out forwards;
}

.career-timeline-item:nth-child(1) { animation-delay: 0.1s; }
.career-timeline-item:nth-child(2) { animation-delay: 0.2s; }
.career-timeline-item:nth-child(3) { animation-delay: 0.3s; }
.career-timeline-item:nth-child(4) { animation-delay: 0.4s; }
.career-timeline-item:nth-child(5) { animation-delay: 0.5s; }

@keyframes career-slideInRight {
    from { 
        opacity: 0; 
        transform: translateX(-30px); 
    }
    to { 
        opacity: 1; 
        transform: translateX(0); 
    }
}

/* Timeline Dot */
.career-timeline-dot {
    position: absolute;
    left: -54px;
    top: 0;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 22px;
    box-shadow: 
        0 8px 25px rgba(0, 0, 0, 0.15),
        inset 0 2px 4px rgba(255, 255, 255, 0.3);
    z-index: 2;
    border: 4px solid white;
}

.career-dot-join {
    background: linear-gradient(135deg, rgba(255, 100, 0, 0.8), rgba(255, 55, 0, 0.8));
}

.career-dot-promotion {
    background: linear-gradient(135deg, rgba(255, 75, 0, 0.8), rgba(255, 55, 0, 0.8));
}

.career-dot-update {
    background: linear-gradient(135deg, rgba(255, 125, 0, 0.8), rgba(255, 80, 0, 0.8));
}

/* Event Card */
.career-event-card {
    background: white;
    padding: 28px 32px;
    border-radius: 18px;
    border: 1px solid #ffeae5;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.1);
    position: relative;
    overflow: hidden;
}

.career-event-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, rgba(255, 55, 0, 0.8), rgba(255, 100, 0, 0.8));
    border-radius: 4px 0 0 4px;
    opacity: 0;
    transition: opacity 0.3s;
}

.career-event-card:hover {
    transform: translateY(-5px);
    border-color: #ffddd0;
    box-shadow: 
        0 15px 40px rgba(0, 0, 0, 0.1),
        0 5px 15px rgba(255, 55, 0, 0.08);
}

.career-event-card:hover::before {
    opacity: 1;
}

/* Card Content */
.career-event-date {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #7c6b65;
    font-weight: 500;
    margin-bottom: 12px;
    padding: 6px 14px;
    background: #fffaf8;
    border-radius: 20px;
    border: 1px solid #ffeae5;
}

.career-event-date i {
    font-size: 14px;
    color: #b8a398;
}

.career-event-title {
    font-size: 20px;
    font-weight: 700;
    color: #2c1f1a;
    margin-bottom: 14px;
    line-height: 1.4;
}

.career-event-desc {
    font-size: 15px;
    color: #5a4a42;
    line-height: 1.7;
    margin: 0;
}

.career-event-desc p {
    margin: 0;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .career-wrapper {
        flex-direction: column;
        gap: 30px;
    }
    
    .career-profile-card {
        width: 100%;
        position: static;
    }
    
    .career-timeline-container {
        padding: 35px 30px;
    }
}

@media (max-width: 768px) {
    .career-page-container {
        padding: 20px 15px;
    }
    
    .career-wrapper {
        gap: 25px;
    }
    
    .career-timeline {
        padding-left: 40px;
    }
    
    .career-timeline-dot {
        width: 48px;
        height: 48px;
        left: -34px;
        font-size: 20px;
    }
    
    .career-timeline::before {
        left: 18px;
    }
    
    .career-event-card {
        padding: 22px 24px;
    }
    
    .career-year-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
}

/* Loading Animation */
@keyframes career-pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.career-loading {
    animation: career-pulse 1.5s ease-in-out infinite;
}

/* Empty State */
.career-empty-state {
    text-align: center;
    padding: 60px 40px;
    color: #7c6b65;
}

.career-empty-state i {
    font-size: 48px;
    margin-bottom: 20px;
    opacity: 0.3;
}
</style>

<div class="career-page-container">
    <div class="career-wrapper">

        <!-- LEFT PROFILE CARD -->
        <div class="career-profile-card">
            <div class="career-profile-image-wrapper">
                <img src="{{ asset($employee->profile_image) }}" alt="{{ $employee->firstname }} {{ $employee->lastname }}">
            </div>
            
            <h2 class="career-profile-name">{{ $employee->firstname }} {{ $employee->lastname }}</h2>
            <p style="color: #7c6b65; font-size: 15px; margin-bottom: 10px;">Employee ID: {{ $employee->employeeid }}</p>
            
            <div class="career-profile-details">
                <div class="career-profile-detail-item">
                    <span class="career-detail-label">Designation</span>
                    <span class="career-detail-value">{{ $employee->designation_name }}</span>
                </div>
                <div class="career-profile-detail-item">
                    <span class="career-detail-label">Department</span>
                    <span class="career-detail-value">{{ $employee->department_name }}</span>
                </div>
                <div class="career-profile-detail-item">
                    <span class="career-detail-label">Location</span>
                    <span class="career-detail-value">{{ $employee->branch_name }}</span>
                </div>
                <div class="career-profile-detail-item">
                    <span class="career-detail-label">Joined On</span>
                    <span class="career-detail-value">{{ date('d M Y', strtotime($employee->joiningdate)) }}</span>
                </div>
                <div class="career-profile-detail-item">
                    <span class="career-detail-label">Experience</span>
                    <span class="career-detail-value">
                        @php
                            $joinDate = new DateTime($employee->joiningdate);
                            $now = new DateTime();
                            $interval = $now->diff($joinDate);
                            echo $interval->y . ' years ' . $interval->m . ' months';
                        @endphp
                    </span>
                </div>
            </div>
        </div>

        <!-- RIGHT TIMELINE -->
        <div class="career-timeline-container">
            @php
                $grouped = collect($events)->groupBy(function($e){
                    return date('Y', strtotime($e['date']));
                });
            @endphp

            @if(count($grouped) > 0)
                @foreach($grouped as $year => $yearEvents)
                    <div class="career-year-header">
                        <div class="career-year-badge">{{ $year }}</div>
                        <div class="career-year-count">
                            <strong>{{ count($yearEvents) }}</strong> event{{ count($yearEvents) > 1 ? 's' : '' }} this year
                        </div>
                    </div>

                    <div class="career-timeline">
                        @foreach($yearEvents as $ev)
                            <div class="career-timeline-item">
                                <!-- Dot Icon -->
                                <div class="career-timeline-dot 
                                    @if($ev['type']=='join') career-dot-join
                                    @elseif($ev['type']=='promotion') career-dot-promotion
                                    @else career-dot-update
                                    @endif">
                                    
                                    @if($ev['type']=='join')
                                        <i class="ri-user-add-fill"></i>
                                    @elseif($ev['type']=='promotion')
                                        <i class="ri-trophy-fill"></i>
                                    @else
                                        <i class="ri-refresh-fill"></i>
                                    @endif
                                </div>

                                <!-- Event Card -->
                                <div class="career-event-card">
                                    <div class="career-event-date">
                                        <i class="ri-calendar-line"></i>
                                        {{ date('d F, Y', strtotime($ev['date'])) }}
                                    </div>
                                    <h3 class="career-event-title">{{ $ev['title'] }}</h3>
                                    <div class="career-event-desc">{!! $ev['description'] !!}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @else
                <div class="career-empty-state">
                    <i class="ri-history-line"></i>
                    <h3>No career events found</h3>
                    <p>Career timeline will appear here once events are added.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection