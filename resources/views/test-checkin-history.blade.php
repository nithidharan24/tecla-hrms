@extends('layouts.index')

@section('content')
<div class="container-fluid mt-5">
    <div class="content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">My Check-in History</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('test-checkin.index') }}">Check-in</a></li>
                        <li class="breadcrumb-item active">History</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('test-checkin.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Check-in
                    </a>
                </div>
            </div>
        </div>

        <!-- Employee Info -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user"></i> {{ $employeeName }}
                        </h5>
                        <small class="text-muted">Employee ID: {{ session('employee_id') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Check-in History -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Previous Check-ins</h4>
                    </div>
                    <div class="card-body">
                        @if($checkins->count() > 0)
                            <!-- Mobile Card Layout (visible on small screens) -->
                            <div class="d-block d-md-none">
                                @foreach($checkins as $checkin)
                                <div class="card mb-3 shadow-sm">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-4">
                                                @if($checkin->employee_image)
                                                    <img src="{{ asset($checkin->employee_image) }}" 
                                                         alt="Check-in photo" 
                                                         class="img-fluid rounded" 
                                                         data-bs-toggle="modal" 
                                                         data-bs-target="#imageModal{{ $checkin->id }}"
                                                         style="cursor: pointer; max-height: 80px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                                        <i class="fas fa-camera text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-8">
                                                <h6 class="card-title mb-1">{{ $checkin->checkin_time->format('M d, Y') }}</h6>
                                                <small class="text-muted d-block mb-2">{{ $checkin->checkin_time->format('h:i A') }}</small>
                                                
                                                <!-- Location with proper wrapping -->
                                                <div class="location-mobile mb-2">
                                                    <strong class="d-block" style="font-size: 0.9em; line-height: 1.3; word-break: break-word;">
                                                        {{ $checkin->location_name }}
                                                    </strong>
                                                </div>
                                                
                                                <!-- Accuracy and Map button in row -->
                                                <div class="d-flex justify-content-between align-items-center">
                                                    @if($checkin->location && $checkin->location->accuracy)
                                                        @php
                                                            $accuracy = $checkin->location->accuracy;
                                                            $accuracyClass = $accuracy <= 10 ? 'success' : ($accuracy <= 50 ? 'warning' : 'danger');
                                                            $accuracyText = $accuracy <= 10 ? 'Excellent' : ($accuracy <= 50 ? 'Good' : 'Fair');
                                                        @endphp
                                                        <span class="badge bg-{{ $accuracyClass }}" style="font-size: 0.7em;">
                                                            {{ $accuracyText }} (±{{ round($accuracy) }}m)
                                                        </span>
                                                    @endif
                                                    
                                                    @if($checkin->location)
                                                        <a href="https://maps.google.com/?q={{ $checkin->location->latitude }},{{ $checkin->location->longitude }}" 
                                                           target="_blank" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-map-marker-alt"></i> View Map
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Desktop Table Layout (hidden on small screens) -->
                            <div class="d-none d-md-block">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date & Time</th>
                                                <th>Photo</th>
                                                <th>Location</th>
                                                <th>Accuracy</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($checkins as $checkin)
                                            <tr>
                                                <td>
                                                    <strong>{{ $checkin->checkin_time->format('M d, Y') }}</strong><br>
                                                    <small class="text-muted">{{ $checkin->checkin_time->format('h:i A') }}</small>
                                                </td>
                                                <td>
                                                    @if($checkin->employee_image)
                                                        <img src="{{ asset($checkin->employee_image) }}" 
                                                             alt="Check-in photo" 
                                                             class="img-thumbnail" 
                                                             style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                                             data-bs-toggle="modal" 
                                                             data-bs-target="#imageModal{{ $checkin->id }}">
                                                    @else
                                                        <span class="text-muted">No photo</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="location-info">
                                                        <strong>{{ $checkin->location_name }}</strong>
                                                        @if($checkin->location)
                                                            <br><small class="text-muted">
                                                                {{ round($checkin->location->latitude, 6) }}, 
                                                                {{ round($checkin->location->longitude, 6) }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($checkin->location && $checkin->location->accuracy)
                                                        @php
                                                            $accuracy = $checkin->location->accuracy;
                                                            $accuracyClass = $accuracy <= 10 ? 'success' : ($accuracy <= 50 ? 'warning' : 'danger');
                                                            $accuracyText = $accuracy <= 10 ? 'Excellent' : ($accuracy <= 50 ? 'Good' : 'Fair');
                                                        @endphp
                                                        <span class="badge bg-{{ $accuracyClass }}">
                                                            {{ $accuracyText }} (±{{ round($accuracy) }}m)
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Unknown</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($checkin->location)
                                                        <a href="https://maps.google.com/?q={{ $checkin->location->latitude }},{{ $checkin->location->longitude }}" 
                                                           target="_blank" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-map-marker-alt"></i> View Map
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-center">
                                    {{ $checkins->links() }}
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                                <h5>No Check-ins Yet</h5>
                                <p class="text-muted">You haven't made any check-ins yet. Start by creating your first check-in!</p>
                                <a href="{{ route('test-checkin.index') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Make First Check-in
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modals -->
@foreach($checkins as $checkin)
    @if($checkin->employee_image)
    <div class="modal fade" id="imageModal{{ $checkin->id }}" tabindex="-1" aria-labelledby="imageModalLabel{{ $checkin->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel{{ $checkin->id }}">
                        Check-in Photo - {{ $checkin->checkin_time->format('M d, Y h:i A') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-2">
                    <img src="{{ asset($checkin->employee_image) }}" 
                         alt="Check-in photo" 
                         class="img-fluid rounded"
                         style="max-height: 80vh; width: auto;">
                    <div class="mt-3">
                        <small class="text-muted">
                            <strong>Location:</strong> {{ $checkin->location_name }}<br>
                            <strong>Time:</strong> {{ $checkin->checkin_time->format('M d, Y h:i A') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach

<style>
.location-info {
    max-width: 250px;
}

.location-mobile {
    max-width: 100%;
    overflow-wrap: break-word;
    word-wrap: break-word;
    hyphens: auto;
}

.img-thumbnail {
    cursor: pointer;
    transition: transform 0.2s;
}

.img-thumbnail:hover {
    transform: scale(1.1);
}

.badge {
    font-size: 0.75em;
    white-space: nowrap;
}

/* Mobile specific improvements */
@media (max-width: 767.98px) {
    .card-body {
        padding: 1rem 0.75rem;
    }
    
    .location-mobile strong {
        font-size: 0.85em !important;
        line-height: 1.2 !important;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    /* Mobile modal improvements */
    .modal-dialog {
        margin: 0.5rem;
    }
    
    .modal-body {
        padding: 1rem 0.5rem;
    }
    
    .modal-body img {
        max-height: 70vh !important;
    }
}

/* Ensure table doesn't break on smaller desktop screens */
@media (min-width: 768px) and (max-width: 991.98px) {
    .location-info {
        max-width: 200px;
    }
    
    .location-info strong {
        font-size: 0.9em;
        line-height: 1.3;
    }
}
</style>
@endsection
