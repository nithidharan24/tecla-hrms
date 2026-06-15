@php
    $resourceType = $resource['resource_type'] ?? 'module';
    $isCompleted = isset($resource['is_completed']) && $resource['is_completed'];
    $isMandatory = isset($resource['is_mandatory']) && $resource['is_mandatory'];
@endphp

<div class="card border {{ $isCompleted ? 'border-success' : '' }} resource-card" data-resource-id="{{ $resource['id'] }}">
    <div class="card-body">
        <div class="d-flex align-items-start">
            <div class="flex-shrink-0 me-3">
                @if($resourceType == 'video')
                    <div class="avatar-sm">
                        <span class="avatar-title rounded-circle bg-danger text-white">
                            <i class="fas fa-play"></i>
                        </span>
                    </div>
                @elseif($resourceType == 'link')
                    <div class="avatar-sm">
                        <span class="avatar-title rounded-circle bg-info text-white">
                            <i class="fas fa-external-link-alt"></i>
                        </span>
                    </div>
                @elseif($resourceType == 'document')
                    <div class="avatar-sm">
                        <span class="avatar-title rounded-circle bg-success text-white">
                            <i class="fas fa-file-download"></i>
                        </span>
                    </div>
                @else
                    <div class="avatar-sm">
                        <span class="avatar-title rounded-circle bg-primary text-white">
                            <i class="fas fa-book-open"></i>
                        </span>
                    </div>
                @endif
            </div>
            
            <div class="flex-grow-1">
                <h5 class="font-size-14 mb-1 resource-title">
                    {{ $resource['title'] ?? 'Untitled Resource' }}
                    @if($isMandatory)
                        <span class="badge badge-soft-warning ms-1">Required</span>
                    @endif
                    @if($isCompleted)
                        <span class="badge badge-soft-success ms-1 completed-badge">
                            <i class="fas fa-check me-1"></i>Completed
                        </span>
                    @endif
                </h5>
                
                @if(isset($resource['description']) && $resource['description'])
                    <p class="text-muted mb-2">{{ $resource['description'] }}</p>
                @endif
                
                @if(isset($resource['completed_at']) && $resource['completed_at'])
                    <p class="text-muted mb-2 completion-info">
                        <small><i class="fas fa-clock me-1"></i>Completed on {{ date('M d, Y H:i', strtotime($resource['completed_at'])) }}</small>
                    </p>
                @endif
                
                <div class="d-flex gap-2 flex-wrap">
                    @if($resourceType == 'video' && isset($resource['external_url']) && $resource['external_url'])
                        <a href="{{ $resource['external_url'] }}" target="_blank" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-play me-1"></i>Watch Video
                        </a>
                    @elseif($resourceType == 'link' && isset($resource['external_url']) && $resource['external_url'])
                        <a href="{{ $resource['external_url'] }}" target="_blank" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-external-link-alt me-1"></i>Open Link
                        </a>
                    @endif
                    
                    @if(isset($resource['file_path']) && $resource['file_path'])
                        <a href="{{ url('/employee/training/resource/' . $resource['id'] . '/download') }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-download me-1"></i>Download
                        </a>
                    @endif
                    
                    @if(!$isCompleted)
                        <button type="button" class="btn btn-sm btn-outline-success mark-complete-btn"
                                data-resource-id="{{ $resource['id'] }}"
                                onclick="markResourceCompleted('{{ $resource['id'] }}', this)">
                            <i class="fas fa-check me-1"></i>Mark Complete
                        </button>
                    @else
                        <button type="button" class="btn btn-sm btn-success" disabled>
                            <i class="fas fa-check me-1"></i>Completed
                        </button>
                    @endif
                </div>
                
                <!-- Resource Type Badge -->
                <div class="mt-2">
                    <span class="badge badge-soft-secondary">
                        @if($resourceType == 'video')
                            <i class="fas fa-video me-1"></i>Video
                        @elseif($resourceType == 'link')
                            <i class="fas fa-link me-1"></i>External Link
                        @elseif($resourceType == 'document')
                            <i class="fas fa-file me-1"></i>Document
                        @else
                            <i class="fas fa-book me-1"></i>Module
                        @endif
                    </span>
                    
                    @if(isset($resource['order_sequence']))
                        <span class="badge badge-soft-info ms-1">
                            Order: {{ $resource['order_sequence'] }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    height: 2rem;
    width: 2rem;
}

.avatar-title {
    align-items: center;
    background-color: #556ee6;
    color: #fff;
    display: flex;
    font-weight: 500;
    height: 100%;
    justify-content: center;
    width: 100%;
}

.badge-soft-secondary {
    color: #6c757d;
    background-color: rgba(108, 117, 125, 0.1);
}

.resource-card {
    transition: all 0.3s ease;
}

.resource-card.completed {
    background-color: #f8fff8;
    border-color: #28a745 !important;
}

.mark-complete-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>