@extends('layouts.index')

@section('content')
<div class="container-fluid mt-5">
    <div class="content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Employee Check-in</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                        <li class="breadcrumb-item active">Check-in</li>
                    </ul>
                </div>
                <!-- Added history button in page header -->
                <div class="col-auto">
                    <a href="{{ route('test-checkin.history') }}" class="btn btn-outline-primary">
                        <i class="fas fa-history"></i> View History
                    </a>
                </div>
            </div>
        </div>

        <!-- Check-in Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Employee Check-in with Camera & Location</h4>
                        <!-- Added secondary history button in card header -->
                        <a href="{{ route('test-checkin.history') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-clock"></i> My History
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Auto-populate employee name from session and make it read-only -->
                        <div class="employee-name mb-3">
                            <label for="employeeName" class="form-label">Employee Name:</label>
                            @if(session('first_name') && session('last_name'))
                                <input type="text" id="employeeName" class="form-control" 
                                       value="{{ session('first_name') }} {{ session('last_name') }}" 
                                       readonly style="background-color: #f8f9fa; cursor: not-allowed;" />
                                <small class="text-muted">Logged in as: {{ session('first_name') }} {{ session('last_name') }} (ID: {{ session('employee_id') }})</small>
                            @else
                                <input type="text" id="employeeName" class="form-control" placeholder="Session expired - please login again" readonly />
                                <div class="alert alert-warning mt-2">
                                    <strong>Warning:</strong> Employee session not found. Please <a href="{{ route('login') }}">login again</a>.
                                </div>
                            @endif
                        </div>

                        <div class="camera-container text-center mb-4">
                            <video id="video" autoplay class="img-fluid border rounded" style="max-width: 400px;"></video>
                            <canvas id="canvas" style="display: none;"></canvas>
                            <div id="capturedImageContainer"></div>
                        </div>

                        <!-- Fixed button alignment for mobile and desktop -->
                        <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-3 mb-4">
                            <button id="startCamera" class="btn btn-primary btn-lg px-4">Start Camera</button>
                            <button id="capturePhoto" class="btn btn-success btn-lg px-4" disabled>Capture Photo</button>
                            <button id="checkinBtn" class="btn btn-warning btn-lg px-4" disabled>Check In</button>
                        </div>

                        <div id="status"></div>
                        <div id="locationInfo" style="display: none;"></div>
                        <!-- Added accuracy display -->
                        <div id="accuracyInfo" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.captured-image {
    max-width: 200px;
    border: 2px solid #28a745;
    border-radius: 8px;
    margin: 10px 0;
}

.status {
    margin: 20px 0;
    padding: 15px;
    border-radius: 8px;
    font-weight: bold;
}

.status.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.status.warning {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.location-info {
    background-color: #e8f5e8;
    padding: 15px;
    border-radius: 8px;
    margin: 15px 0;
    border-left: 4px solid #28a745;
}

.location-info h4 {
    margin: 0 0 10px 0;
    color: #2e7d32;
}

.address-text {
    font-size: 16px;
    color: #1b5e20;
    line-height: 1.4;
    font-weight: bold;
}

/* Added accuracy info styling */
.accuracy-info {
    background-color: #e3f2fd;
    padding: 15px;
    border-radius: 8px;
    margin: 15px 0;
    border-left: 4px solid #2196f3;
}

.accuracy-good {
    border-left-color: #4caf50 !important;
    background-color: #e8f5e8 !important;
}

.accuracy-poor {
    border-left-color: #ff9800 !important;
    background-color: #fff3e0 !important;
}

.accuracy-bad {
    border-left-color: #f44336 !important;
    background-color: #ffebee !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let video = document.getElementById('video');
    let canvas = document.getElementById('canvas');
    let ctx = canvas.getContext('2d');
    let capturedImage = null;
    let currentLocation = null;

    @if(!session('first_name') || !session('last_name'))
        updateStatus('❌ Please login as an employee to perform check-in.', 'error');
        document.getElementById('startCamera').disabled = true;
        return;
    @endif

    document.getElementById('startCamera').addEventListener('click', startCamera);
    document.getElementById('capturePhoto').addEventListener('click', capturePhoto);
    document.getElementById('checkinBtn').addEventListener('click', performCheckin);

    async function startCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    width: { ideal: 640 },
                    height: { ideal: 480 },
                    facingMode: 'user'
                } 
            });
            video.srcObject = stream;
            document.getElementById('startCamera').disabled = true;
            document.getElementById('capturePhoto').disabled = false;
            updateStatus('Camera started. Position yourself and click "Capture Photo"', 'success');
        } catch (err) {
            updateStatus('Error accessing camera: ' + err.message, 'error');
        }
    }

    function capturePhoto() {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0);
        
        capturedImage = canvas.toDataURL('image/jpeg', 0.8);
        
        const imgContainer = document.getElementById('capturedImageContainer');
        imgContainer.innerHTML = '<img src="' + capturedImage + '" class="captured-image" alt="Captured photo">';
        
        const stream = video.srcObject;
        const tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
        video.style.display = 'none';
        
        document.getElementById('capturePhoto').disabled = true;
        document.getElementById('checkinBtn').disabled = false;
        updateStatus('Photo captured! Now getting your precise location...', 'success');
        
        getLocation();
    }

    function getLocation() {
        if (navigator.geolocation) {
            updateStatus('🔍 Getting high-accuracy location... Please wait and stay still.', 'success');
            
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    currentLocation = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: position.coords.accuracy
                    };
                    
                    displayAccuracyInfo(position.coords.accuracy);
                    
                    if (position.coords.accuracy > 100) {
                        updateStatus('⚠️ Location accuracy is low (' + Math.round(position.coords.accuracy) + 'm). Trying to get better location...', 'warning');
                        // Try again with different settings
                        getLocationWithFallback();
                    } else {
                        updateStatus('✅ High-accuracy location obtained! Ready to check in.', 'success');
                    }
                },
                (error) => {
                    updateStatus('❌ Error getting location: ' + getLocationErrorMessage(error), 'error');
                    // Try with less strict settings
                    getLocationWithFallback();
                },
                {
                    enableHighAccuracy: true,
                    timeout: 30000, // Increased timeout for better accuracy
                    maximumAge: 0   // Always get fresh location, no cached data
                }
            );
        } else {
            updateStatus('❌ Geolocation is not supported by this browser.', 'error');
        }
    }

    function getLocationWithFallback() {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                currentLocation = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy
                };
                
                displayAccuracyInfo(position.coords.accuracy);
                
                if (position.coords.accuracy > 200) {
                    updateStatus('⚠️ Location accuracy is still poor (' + Math.round(position.coords.accuracy) + 'm). You may proceed but location may not be precise.', 'warning');
                } else {
                    updateStatus('✅ Location obtained with ' + Math.round(position.coords.accuracy) + 'm accuracy. Ready to check in.', 'success');
                }
            },
            (error) => {
                updateStatus('❌ Unable to get location: ' + getLocationErrorMessage(error), 'error');
            },
            {
                enableHighAccuracy: false, // Less strict for fallback
                timeout: 15000,
                maximumAge: 30000 // Allow slightly cached data as fallback
            }
        );
    }

    function displayAccuracyInfo(accuracy) {
        const accuracyDiv = document.getElementById('accuracyInfo');
        let accuracyClass = 'accuracy-info';
        let accuracyText = '';
        let accuracyIcon = '';
        
        if (accuracy <= 10) {
            accuracyClass += ' accuracy-good';
            accuracyText = 'Excellent';
            accuracyIcon = '🎯';
        } else if (accuracy <= 50) {
            accuracyClass += ' accuracy-good';
            accuracyText = 'Good';
            accuracyIcon = '✅';
        } else if (accuracy <= 100) {
            accuracyClass += ' accuracy-poor';
            accuracyText = 'Fair';
            accuracyIcon = '⚠️';
        } else {
            accuracyClass += ' accuracy-bad';
            accuracyText = 'Poor';
            accuracyIcon = '❌';
        }
        
        accuracyDiv.innerHTML = `
            <div class="${accuracyClass}">
                <h5>${accuracyIcon} Location Accuracy: ${accuracyText}</h5>
                <p>Accuracy: ±${Math.round(accuracy)} meters</p>
                <small class="text-muted">Coordinates: ${currentLocation.latitude.toFixed(6)}, ${currentLocation.longitude.toFixed(6)}</small>
            </div>
        `;
        accuracyDiv.style.display = 'block';
    }

    async function performCheckin() {
        if (!capturedImage) {
            updateStatus('❌ Please capture a photo first!', 'error');
            return;
        }
        
        if (!currentLocation) {
            updateStatus('❌ Location not available. Please try again.', 'error');
            return;
        }

        const employeeName = document.getElementById('employeeName').value.trim();
        
        if (!employeeName || employeeName.includes('Session expired')) {
            updateStatus('❌ Employee session expired. Please login again.', 'error');
            setTimeout(() => {
                window.location.href = "{{ route('login') }}";
            }, 2000);
            return;
        }
        
        updateStatus('🔄 Processing check-in and getting address...', 'success');
        document.getElementById('checkinBtn').disabled = true;

        try {
            const response = await fetch("{{ route('test-checkin.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    latitude: currentLocation.latitude,
                    longitude: currentLocation.longitude,
                    accuracy: currentLocation.accuracy,
                    employee_image: capturedImage,
                    employee_name: employeeName
                })
            });

            const data = await response.json();
            
            if (data.success) {
                updateStatus('✅ Check-in successful! Image and location saved. ID: ' + data.checkin_id, 'success');
                
                const locationInfo = document.getElementById('locationInfo');
                locationInfo.innerHTML = `
                    <div class="location-info">
                        <h4>📍 Check-in Location:</h4>
                        <div class="address-text">${data.address}</div>
                        <small class="text-muted">Accuracy: ${data.accuracy}</small>
                    </div>
                `;
                locationInfo.style.display = 'block';
                
                setTimeout(() => {
                    location.reload();
                }, 5000);
            } else {
                updateStatus('❌ Error: ' + data.message, 'error');
                document.getElementById('checkinBtn').disabled = false;
            }
        } catch (err) {
            updateStatus('❌ Network error: ' + err.message, 'error');
            document.getElementById('checkinBtn').disabled = false;
        }
    }

    function updateStatus(message, type = '') {
        const statusDiv = document.getElementById('status');
        statusDiv.innerHTML = message;
        statusDiv.className = 'status ' + type;
    }

    function getLocationErrorMessage(error) {
        switch(error.code) {
            case error.PERMISSION_DENIED:
                return "Location access denied by user. Please enable location permissions.";
            case error.POSITION_UNAVAILABLE:
                return "Location information is unavailable. Please check your GPS settings.";
            case error.TIMEOUT:
                return "Location request timed out. Please try again.";
            default:
                return "An unknown error occurred while getting location.";
        }
    }
});
</script>
@endsection
