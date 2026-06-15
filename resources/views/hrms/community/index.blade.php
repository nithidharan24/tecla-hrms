@extends('layouts.index')

@section('content')
<style>
.community-dashboard {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 2rem 0;
}

.community-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    margin-bottom: 2rem;
}

.section-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 12px 12px 0 0;
    margin: 0;
}

.birthday-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.anniversary-header {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.community-messages-header {
    background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
}

.login-required {
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.employee-card {
    padding: 1.5rem;
    border-bottom: 1px solid #e9ecef;
    transition: background-color 0.3s ease;
}

.employee-card:hover {
    background-color: #f8f9fa;
}

.employee-card:last-child {
    border-bottom: none;
}

.employee-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #fff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.wish-button {
    background: #007bff;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.wish-button:hover:not(:disabled) {
    background: #0056b3;
    color: white;
    transform: translateY(-1px);
}

.wish-button:disabled {
    background: #28a745;
    color: white;
    cursor: not-allowed;
}

.anniversary-wish-button {
    background: #ffc107;
    color: #212529;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.anniversary-wish-button:hover:not(:disabled) {
    background: #e0a800;
    color: #212529;
    transform: translateY(-1px);
}

.anniversary-wish-button:disabled {
    background: #28a745;
    color: white;
    cursor: not-allowed;
}

.wish-count {
    background: #e9ecef;
    color: #495057;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
}

.already-wished-badge {
    background: #28a745;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.stats-row {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.stat-item {
    text-align: center;
    padding: 1rem;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #007bff;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
    margin-top: 0.5rem;
}

.upcoming-birthday-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 0.5rem;
}

.upcoming-birthday-item:hover {
    background: #e9ecef;
}

.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.modal-header {
    background: #007bff;
    color: white;
    border-radius: 12px 12px 0 0;
    border: none;
}

.btn-close-white {
    filter: brightness(0) invert(1);
}

.list-group-item {
    border-left: none;
    border-right: none;
    transition: background-color 0.2s ease;
}

.list-group-item:hover {
    background-color: #f5f5f5;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}

#communityMessagesContainer {
    max-height: 500px;
    -webkit-overflow-scrolling: touch;
}

#communityMessagesContainer::-webkit-scrollbar {
    width: 6px;
}

#communityMessagesContainer::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

#communityMessagesContainer::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

#communityMessagesContainer::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

@media (max-width: 768px) {
    .community-dashboard {
        padding: 1rem 0;
    }
    
    .employee-card {
        padding: 1rem;
    }
    
    .employee-avatar {
        width: 50px;
        height: 50px;
    }
}
</style>

<div class="community-dashboard">
    <div class="container-fluid">
        <!-- Header -->
        <div class="community-card">
            <div class="section-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="mb-2">🎉 HRMS Community Celebrations</h1>
                        <p class="mb-0">Celebrate birthdays and work anniversaries with your colleagues</p>
                    </div>
                    @if($currentUser)
                        <div class="col-md-4 text-end">
                            <div class="d-flex justify-content-end gap-3">
                                <div class="text-center">
                                    <div class="stat-number text-white">{{ $wishStats['today_wishes'] ?? 0 }}</div>
                                    <small>Today's Wishes</small>
                                </div>
                                <div class="text-center">
                                    <div class="stat-number text-white">{{ $wishStats['this_month_wishes'] ?? 0 }}</div>
                                    <small>This Month</small>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if(!$currentUser)
            <div class="login-required">
                <i class="fas fa-lock fa-4x text-muted mb-4"></i>
                <h3 class="text-muted mb-3">Authentication Required</h3>
                <p class="text-muted mb-4">Please log in to view community features and participate in celebrations.</p>
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>Login to Continue
                </a>
            </div>
        @else
            <!-- Stats Row -->
            <div class="stats-row">
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-number">{{ $birthdayEmployees->count() }}</div>
                            <div class="stat-label">Birthdays Today</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-number">{{ $anniversaryEmployees->count() }}</div>
                            <div class="stat-label">Anniversaries Today</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-number">{{ $upcomingBirthdays->count() }}</div>
                            <div class="stat-label">Upcoming Birthdays</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-number">{{ $wishStats['active_wishers'] ?? 0 }}</div>
                            <div class="stat-label">Active Wishers</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Birthday Section -->
                <div class="col-lg-4 mb-4">
                    <div class="community-card">
                        <div class="section-header birthday-header">
                            <h3 class="mb-0">🎂 Birthdays Today</h3>
                        </div>
                        <div class="card-body p-0">
                            @if($birthdayEmployees->count() > 0)
                                <div style="max-height: 500px; overflow-y: auto;">
                                    @foreach($birthdayEmployees as $employee)
                                        <div class="employee-card">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $employee->profile_image ? asset($employee->profile_image) : asset('admin/uploads/images/default-avatar.png') }}"
                                                     alt="{{ $employee->firstname }}"
                                                     class="employee-avatar me-3">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-bold">{{ $employee->firstname }} {{ $employee->lastname }}</h6>
                                                    <p class="mb-1 text-muted small">{{ $employee->designation_name }}</p>
                                                    <p class="mb-0 text-muted small">{{ $employee->department_name }}</p>
                                                    @if($employee->birthday_wish_count > 0)
                                                        <span class="wish-count mt-1">{{ $employee->birthday_wish_count }} wishes</span>
                                                    @endif
                                                </div>
                                                <div class="text-end">
                                                    @if($employee->id != $currentUserId)
                                                        @if($employee->already_wished)
                                                            <div class="already-wished-badge">
                                                                <i class="fas fa-check"></i> Already Wished
                                                            </div>
                                                        @else
                                                            <button class="btn wish-button btn-sm mb-1"
                                                                    onclick="openWishModal({{ $employee->id }}, '{{ $employee->firstname }} {{ $employee->lastname }}', 'birthday')">
                                                                Send Wish
                                                            </button>
                                                        @endif
                                                    @endif
                                                    
                                                    @if($employee->birthday_wish_count > 0 && $employee->id == $currentUserId)
                                                        <button class="btn btn-outline-primary btn-sm"
                                                                onclick="viewWishes({{ $employee->id }}, '{{ $employee->firstname }} {{ $employee->lastname }}', 'birthday')">
                                                            View Wishes
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-birthday-cake fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No birthdays today</h5>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Anniversary Section -->
                <div class="col-lg-4 mb-4">
                    <div class="community-card">
                        <div class="section-header anniversary-header">
                            <h3 class="mb-0">🏆 Work Anniversaries Today</h3>
                        </div>
                        <div class="card-body p-0">
                            @if($anniversaryEmployees->count() > 0)
                                <div style="max-height: 500px; overflow-y: auto;">
                                    @foreach($anniversaryEmployees as $employee)
                                        <div class="employee-card">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $employee->profile_image ? asset($employee->profile_image) : asset('admin/uploads/images/default-avatar.png') }}"
                                                     alt="{{ $employee->firstname }}"
                                                     class="employee-avatar me-3">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-bold">{{ $employee->firstname }} {{ $employee->lastname }}</h6>
                                                    <p class="mb-1 text-muted small">{{ $employee->designation_name }}</p>
                                                    <p class="mb-1 text-muted small">{{ $employee->department_name }}</p>
                                                    <small class="text-success">{{ $employee->years_completed }} Year{{ $employee->years_completed > 1 ? 's' : '' }}</small>
                                                    @if($employee->anniversary_wish_count > 0)
                                                        <br><span class="wish-count mt-1">{{ $employee->anniversary_wish_count }} wishes</span>
                                                    @endif
                                                </div>
                                                <div class="text-end">
                                                    @if($employee->id != $currentUserId)
                                                        @if($employee->already_wished)
                                                            <div class="already-wished-badge">
                                                                <i class="fas fa-check"></i> Already Wished
                                                            </div>
                                                        @else
                                                            <button class="btn anniversary-wish-button btn-sm mb-1"
                                                                    onclick="openWishModal({{ $employee->id }}, '{{ $employee->firstname }} {{ $employee->lastname }}', 'anniversary')">
                                                                Send Wish
                                                            </button>
                                                        @endif
                                                    @endif
                                                    
                                                    @if($employee->anniversary_wish_count > 0 && $employee->id == $currentUserId)
                                                        <button class="btn btn-outline-warning btn-sm"
                                                                onclick="viewWishes({{ $employee->id }}, '{{ $employee->firstname }} {{ $employee->lastname }}', 'anniversary')">
                                                            View Wishes
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No work anniversaries today</h5>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Community Messages Section -->
                <div class="col-lg-4 mb-4">
                    <div class="community-card h-100">
                        <div class="section-header community-messages-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="mb-0">💬 Community Messages</h3>
                                <button class="btn btn-light btn-sm" onclick="openAddCommunityMessageModal()">
                                    <i class="fas fa-plus me-1"></i>Add
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0 d-flex flex-column">
                            <!-- Search Bar -->
                            <div class="p-3 border-bottom">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-0 bg-light" 
                                           placeholder="Search messages..." id="communityMessageSearch">
                                </div>
                            </div>
                            
                            <!-- Community Messages List -->
                            <div id="communityMessagesContainer" class="flex-grow-1" style="overflow-y: auto;">
                                @if($communityMessages->count() > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($communityMessages as $message)
                                        <div class="list-group-item list-group-item-action p-3 community-message-item">
                                            <div class="d-flex align-items-start">
                                                <!-- Message Icon/Avatar -->
                                                <div class="position-relative me-3">
                                                    @if($message->employee_image)
                                                        <img src="{{ asset($message->employee_image) }}"
                                                             alt="{{ $message->employee_name }}"
                                                             class="rounded-circle" width="50" height="50">
                                                    @else
                                                        <div class="rounded-circle bg-{{ $message->color }} text-white d-flex align-items-center justify-content-center" 
                                                             style="width: 50px; height: 50px; font-size: 1.2rem;">
                                                            {{ strtoupper(substr($message->employee_name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <span class="position-absolute bottom-0 end-0 bg-white rounded-circle p-1 border">
                                                        {{ $message->icon ?? '💬' }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Message Content -->
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-1 fw-bold">{{ $message->title }}</h6>
                                                            <small class="text-muted">{{ $message->employee_name }}</small>
                                                        </div>
                                                        <small class="text-muted">
                                                            {{ \Carbon\Carbon::parse($message->date)->format('M d') }}
                                                        </small>
                                                    </div>
                                                    
                                                    <p class="mb-1 mt-2 small text-muted">
                                                        {{ Str::limit($message->description, 100) }}
                                                    </p>
                                                    
                                                    @if($message->achievement_image)
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/' . $message->achievement_image) }}" 
                                                             alt="Message Image" 
                                                             class="img-thumbnail rounded" 
                                                             style="max-width: 150px; max-height: 100px; cursor: pointer;"
                                                             onclick="viewMessageImage('{{ asset('storage/' . $message->achievement_image) }}', '{{ $message->title }}')">
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5 h-100 d-flex flex-column justify-content-center">
                                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No community messages yet</h5>
                                        <p class="text-muted">Be the first to share a message!</p>
                                        <button class="btn btn-primary align-self-center" onclick="openAddCommunityMessageModal()">
                                            <i class="fas fa-plus me-2"></i>Add Message
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Birthdays -->
            @if($upcomingBirthdays->count() > 0)
                <div class="community-card">
                    <div class="section-header">
                        <h3 class="mb-0">📅 Upcoming Birthdays (Next 30 Days)</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($upcomingBirthdays as $birthday)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="upcoming-birthday-item">
                                        <img src="{{ $birthday->profile_image ? asset($birthday->profile_image) : asset('admin/uploads/images/default-avatar.png') }}"
                                             alt="{{ $birthday->firstname }}"
                                             style="width: 45px; height: 45px; border-radius: 50%; margin-right: 1rem;">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 fw-bold">{{ $birthday->firstname }} {{ $birthday->lastname }}</h6>
                                            <small class="text-muted">{{ $birthday->department_name }}</small>
                                        </div>
                                        <div class="text-end">
                                            <strong class="text-primary">{{ $birthday->birthday_date }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $birthday->days_until }} day{{ $birthday->days_until > 1 ? 's' : '' }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

<!-- Send Wish Modal -->
<div class="modal fade" id="wishModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span id="wishModalIcon">🎉</span>
                    Send <span id="wishTypeText">Birthday</span> Wish
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="wishForm">
                    <input type="hidden" id="employeeId" name="employee_id">
                    <input type="hidden" id="wishType" name="wish_type">
                    
                    <div class="mb-3">
                        <div class="alert alert-info">
                            <strong id="employeeName"></strong> - <span id="wishTypeTextLower">birthday</span> wish
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="wishMessage" class="form-label">Your Message</label>
                        <textarea class="form-control" id="wishMessage" name="message" rows="4"
                                  placeholder="Write a heartfelt message..." required maxlength="500"></textarea>
                        <div class="form-text">Maximum 500 characters</div>
                    </div>

                    <!-- Quick Templates -->
                    <div class="mb-3">
                        <label class="form-label">Quick Templates:</label>
                        <div class="d-flex flex-wrap gap-2" id="quickTemplates">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="sendWishBtn" onclick="sendWish()">
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                    Send Wish
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Wishes Modal -->
<div class="modal fade" id="viewWishesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span id="viewWishesIcon">🎂</span>
                    <span id="viewWishesType">Birthday</span> Wishes for
                    <span id="viewWishesEmployeeName" class="text-warning"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="wishesContainer">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Community Message Modal -->
<div class="modal fade" id="addCommunityMessageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span>💬</span> Add Community Message
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="communityMessageForm" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="messageTitle" class="form-label">Message Title *</label>
                                <input type="text" class="form-control" id="messageTitle" name="title" 
                                       placeholder="e.g., Team Achievement, Company News" required maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="messageCategory" class="form-label">Category *</label>
                                <select class="form-select" id="messageCategory" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="announcement">📢 Announcement</option>
                                    <option value="celebration">🎉 Celebration</option>
                                    <option value="milestone">🏆 Milestone</option>
                                    <option value="news">📰 News</option>
                                    <option value="other">💬 Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="messageDescription" class="form-label">Message *</label>
                        <textarea class="form-control" id="messageDescription" name="description" rows="3"
                                  placeholder="Share your message with the community..." 
                                  required maxlength="1000"></textarea>
                        <div class="form-text">Maximum 1000 characters</div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="messageImage" class="form-label">Message Image</label>
                                <input type="file" class="form-control" id="messageImage" name="achievement_image" 
                                       accept="image/*" onchange="previewMessageImage(this)">
                                <div class="form-text">Upload related image (Max: 2MB)</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="messageIcon" class="form-label">Custom Icon (Optional)</label>
                                <input type="text" class="form-control" id="messageIcon" name="icon" 
                                       placeholder="💬" maxlength="10">
                                <div class="form-text">Use emoji or leave blank for default</div>
                            </div>
                        </div>
                    </div>

                    <div id="imagePreview" class="mb-3" style="display: none;">
                        <label class="form-label">Image Preview:</label>
                        <div>
                            <img id="previewImg" src="/placeholder.svg" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="submitMessageBtn" onclick="submitCommunityMessage()">
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                    <i class="fas fa-paper-plane me-2"></i>Post Message
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Message Image Modal -->
<div class="modal fade" id="viewImageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalTitle">Message Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="/placeholder.svg" alt="Message" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<script>
// Quick message templates
const messageTemplates = {
    birthday: [
        "🎉 Happy Birthday! Wishing you a fantastic year ahead!",
        "🎂 Hope your special day is filled with happiness and joy!",
        "🎈 Another year of awesome! Happy Birthday!",
        "🌟 May all your birthday wishes come true!"
    ],
    anniversary: [
        "🏆 Congratulations on your work anniversary! Thank you for your dedication!",
        "🎊 Happy work anniversary! Your contributions are truly valued!",
        "⭐ Celebrating another year of your amazing work!",
        "🙌 Thank you for being such an important part of our team!"
    ]
};

function openWishModal(employeeId, employeeName, wishType) {
    console.log('Opening wish modal:', { employeeId, employeeName, wishType });

    document.getElementById('employeeId').value = employeeId;
    document.getElementById('employeeName').textContent = employeeName;
    document.getElementById('wishType').value = wishType;
    document.getElementById('wishMessage').value = '';

    // Update modal content based on wish type
    if (wishType === 'birthday') {
        document.getElementById('wishModalIcon').textContent = '🎂';
        document.getElementById('wishTypeText').textContent = 'Birthday';
        document.getElementById('wishTypeTextLower').textContent = 'birthday';
        document.getElementById('sendWishBtn').className = 'btn btn-success';
    } else {
        document.getElementById('wishModalIcon').textContent = '🏆';
        document.getElementById('wishTypeText').textContent = 'Anniversary';
        document.getElementById('wishTypeTextLower').textContent = 'anniversary';
        document.getElementById('sendWishBtn').className = 'btn btn-warning';
    }

    // Populate quick templates
    const templatesContainer = document.getElementById('quickTemplates');
    templatesContainer.innerHTML = '';
    messageTemplates[wishType].forEach(template => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-outline-secondary btn-sm';
        btn.textContent = template;
        btn.onclick = () => {
            document.getElementById('wishMessage').value = template;
        };
        templatesContainer.appendChild(btn);
    });

    var modal = new bootstrap.Modal(document.getElementById('wishModal'));
    modal.show();
}

function sendWish() {
    const employeeId = document.getElementById('employeeId').value;
    const wishType = document.getElementById('wishType').value;
    const message = document.getElementById('wishMessage').value;

    console.log('Sending wish:', { employeeId, wishType, message });

    if (!employeeId || !wishType || !message.trim()) {
        alert('Please fill in all required fields.');
        return;
    }

    const sendButton = document.getElementById('sendWishBtn');
    const spinner = sendButton.querySelector('.spinner-border');
    const originalText = sendButton.innerHTML;

    sendButton.disabled = true;
    spinner.classList.remove('d-none');
    sendButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Sending...';

    const requestData = {
        employee_id: parseInt(employeeId),
        message: message.trim(),
        wish_type: wishType,
        _token: '{{ csrf_token() }}'
    };

    fetch('{{ route("community.send-wish") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                <strong>Success!</strong> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 3000);
            
            // Close modal and refresh page
            bootstrap.Modal.getInstance(document.getElementById('wishModal')).hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            alert(data.message || 'Error sending wish');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Something went wrong. Please try again.');
    })
    .finally(() => {
        sendButton.disabled = false;
        spinner.classList.add('d-none');
        sendButton.innerHTML = originalText;
    });
}

function viewWishes(employeeId, employeeName, wishType) {
    console.log('Viewing wishes for:', { employeeId, employeeName, wishType });
    
    document.getElementById('viewWishesEmployeeName').textContent = employeeName;

    if (wishType === 'birthday') {
        document.getElementById('viewWishesIcon').textContent = '🎂';
        document.getElementById('viewWishesType').textContent = 'Birthday';
    } else {
        document.getElementById('viewWishesIcon').textContent = '🏆';
        document.getElementById('viewWishesType').textContent = 'Anniversary';
    }

    const modal = new bootstrap.Modal(document.getElementById('viewWishesModal'));
    modal.show();

    // Reset container to loading state
    document.getElementById('wishesContainer').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

    fetch(`{{ url('community/wishes') }}/${employeeId}/${wishType}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Wishes data:', data);
        const container = document.getElementById('wishesContainer');
        
        if (data.success && data.wishes && data.wishes.length > 0) {
            let wishesHtml = '';
            data.wishes.forEach(wish => {
                const avatar = wish.profile_image ? 
                    `{{ asset('') }}${wish.profile_image}` : 
                    '{{ asset("admin/uploads/images/default-avatar.png") }}';
                
                wishesHtml += `
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <img src="${avatar}" alt="${wish.firstname}"
                                     style="width: 50px; height: 50px; border-radius: 50%; margin-right: 1rem; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">${wish.firstname} ${wish.lastname}</h6>
                                    <small class="text-muted">${wish.department_name || 'N/A'}</small>
                                    <p class="mb-2 mt-2">${wish.message}</p>
                                    <small class="text-muted">${new Date(wish.created_at).toLocaleString()}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = wishesHtml;
        } else {
            const wishTypeText = wishType === 'birthday' ? 'birthday' : 'anniversary';
            const icon = wishType === 'birthday' ? '🎂' : '🏆';
            container.innerHTML = `
                <div class="text-center py-5">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">${icon}</div>
                    <h5 class="text-muted">No ${wishTypeText} wishes yet</h5>
                    <p class="text-muted">Be the first to send a ${wishTypeText} wish!</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('wishesContainer').innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <p class="text-muted">Error loading wishes. Please try again.</p>
            </div>
        `;
    });
}

// Community Message Functions
function openAddCommunityMessageModal() {
    document.getElementById('communityMessageForm').reset();
    document.getElementById('imagePreview').style.display = 'none';
    var modal = new bootstrap.Modal(document.getElementById('addCommunityMessageModal'));
    modal.show();
}

function previewMessageImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        document.getElementById('imagePreview').style.display = 'none';
    }
}

function submitCommunityMessage() {
    const form = document.getElementById('communityMessageForm');
    const formData = new FormData(form);

    const title = document.getElementById('messageTitle').value.trim();
    const description = document.getElementById('messageDescription').value.trim();
    const category = document.getElementById('messageCategory').value;

    if (!title || !description || !category) {
        alert('Please fill in all required fields.');
        return;
    }

    const submitButton = document.getElementById('submitMessageBtn');
    const spinner = submitButton.querySelector('.spinner-border');
    const originalText = submitButton.innerHTML;

    submitButton.disabled = true;
    spinner.classList.remove('d-none');
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Posting...';

    formData.append('_token', '{{ csrf_token() }}');

    fetch('{{ route("community.add-achievement") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                <strong>Success!</strong> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 3000);
            
            // Close modal and refresh page
            bootstrap.Modal.getInstance(document.getElementById('addCommunityMessageModal')).hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            alert(data.message || 'Error adding community message');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Something went wrong. Please try again.');
    })
    .finally(() => {
        submitButton.disabled = false;
        spinner.classList.add('d-none');
        submitButton.innerHTML = originalText;
    });
}

function viewMessageImage(imageSrc, title) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModalTitle').textContent = title;
    var modal = new bootstrap.Modal(document.getElementById('viewImageModal'));
    modal.show();
}

// Search functionality for community messages
document.getElementById('communityMessageSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const messageItems = document.querySelectorAll('.community-message-item');
    
    messageItems.forEach(item => {
        const title = item.querySelector('h6').textContent.toLowerCase();
        const description = item.querySelector('p').textContent.toLowerCase();
        const author = item.querySelector('small').textContent.toLowerCase();
        
        if (title.includes(searchTerm) || description.includes(searchTerm) || author.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});

// Fix modal close buttons
document.addEventListener('DOMContentLoaded', function() {
    // Ensure all modal close buttons work properly
    const modals = ['wishModal', 'viewWishesModal', 'addCommunityMessageModal', 'viewImageModal'];
    
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            const closeButtons = modal.querySelectorAll('[data-bs-dismiss="modal"]');
            closeButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                });
            });
        }
    });
});

// Auto-refresh every 5 minutes (reduced from 2 minutes to avoid interrupting user actions)
setInterval(() => {
    // Only refresh if no modals are open
    const openModals = document.querySelectorAll('.modal.show');
    if (openModals.length === 0) {
        location.reload();
    }
}, 300000); // 5 minutes
</script>
@endsection
