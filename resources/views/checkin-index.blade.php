@extends('layouts.index')

@section('content')
<style>
    .location-name {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: 600;
        display: inline-block;
        font-size: 0.9rem;
    }
    
    .checkin-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .checkin-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    
    .employee-image:hover {
        transform: scale(1.05);
    }
</style>

<div class="container-fluid mt-5">
    <div class="content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Employee Check-ins</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                        <li class="breadcrumb-item active">Check-ins</li>
                    </ul>
                </div>
                <div class="col-auto float-end ms-auto">
                    <a href="{{ route('test-checkin.index') }}" class="btn add-btn">
                        New Check-in
                    </a>
                </div>
            </div>
        </div>

        <!-- Check-ins Grid -->
        <div class="row">
            @forelse($checkins as $checkin)
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card h-100 checkin-card">
                        <div class="card-body">
                            <div class="employee-info d-flex align-items-center mb-3">
                                @php
                                    $imagePath = $checkin->employee_image;
                                    if ($imagePath && !str_starts_with($imagePath, 'uploads/checkins/')) {
                                        $imagePath = 'uploads/checkins/' . basename($imagePath);
                                    }
                                    $fullPath = public_path($imagePath);
                                    $imageExists = $imagePath && file_exists($fullPath);
                                @endphp
                                
                                @if($imageExists)
                                    <img src="{{ asset($imagePath) }}" 
                                         alt="{{ $checkin->employee_name }}" 
                                         class="employee-image clickable-image rounded-circle me-3" 
                                         style="width: 60px; height: 60px; object-fit: cover; cursor: pointer; border: 3px solid #4facfe; transition: transform 0.2s ease;"
                                         data-employee-name="{{ $checkin->employee_name }}"
                                         data-checkin-time="{{ $checkin->checkin_time->format('M d, Y - h:i A') }}">
                                @else
                                    <div class="employee-image rounded-circle me-3 d-flex align-items-center justify-content-center text-white fw-bold" 
                                         style="width: 60px; height: 60px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); font-size: 1.2rem; transition: transform 0.2s ease;"
                                         title="Image path: {{ $imagePath ?? 'No image' }}">
                                        {{ strtoupper(substr($checkin->employee_name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="employee-name fw-bold">{{ $checkin->employee_name }}</div>
                            </div>
                            
                            <div class="checkin-details">
                                <div class="detail-row d-flex align-items-center mb-2">
                                    <i class="fa fa-calendar me-2" style="color: #4facfe;"></i>
                                    <span class="detail-label fw-semibold me-2" style="color: #555; min-width: 80px;">Time:</span>
                                    <span class="detail-value" style="color: #333;">
                                        {{ optional($checkin->checkin_time)->format('M d, Y - h:i A') }}
                                    </span>
                                </div>
                                
                                <!-- <div class="detail-row d-flex align-items-center mb-2">
                                    <i class="fa fa-mobile me-2" style="color: #4facfe;"></i>
                                    <span class="detail-label fw-semibold me-2" style="color: #555; min-width: 80px;">Device:</span>
                                    <span class="detail-value" style="color: #333;">{{ $checkin->device }}</span>
                                </div> -->
                                
                                @if($checkin->location)
                                    <div class="detail-row d-flex align-items-center">
                                        <i class="fa fa-map-marker-alt me-2" style="color: #4facfe;"></i>
                                        <span class="detail-label fw-semibold me-2" style="color: #555; min-width: 80px;">Location:</span>
                                        <span class="detail-value">
                                            <!-- Updated to use exact styling from original code -->
                                            <span class="location-name">{{ $checkin->location_name ?? 'Loading location...' }}</span>
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fa fa-clock fa-3x text-muted mb-3"></i>
                            <h3 class="text-muted">No Check-ins Found</h3>
                            <p class="text-muted">No employee check-in records available yet.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Employee Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" class="img-fluid rounded" src="/placeholder.svg" alt="">
                <div id="modalInfo" class="mt-3 p-2 bg-light rounded"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    const modalImage = document.getElementById('modalImage');
    const modalInfo = document.getElementById('modalInfo');
    const clickableImages = document.querySelectorAll('.clickable-image');
    
    // Open modal when image is clicked
    clickableImages.forEach(image => {
        image.addEventListener('click', function() {
            modalImage.src = this.src;
            modalImage.alt = this.alt;
            
            const employeeName = this.getAttribute('data-employee-name');
            const checkinTime = this.getAttribute('data-checkin-time');
            modalInfo.innerHTML = `<strong>${employeeName}</strong><br><small class="text-muted">${checkinTime}</small>`;
            
            imageModal.show();
        });
    });
});
</script>
@endsection
