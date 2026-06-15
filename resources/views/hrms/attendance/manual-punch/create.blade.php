@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">New Manual Punch Request</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="">Attendance</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('manual-punch.index') }}">Manual Punch Requests</a></li>
                        <li class="breadcrumb-item active" aria-current="page">New Request</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <a href="{{ route('manual-punch.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">Request Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('manual-punch.store') }}" method="POST" id="requestForm">
                        @csrf

                        <!-- Employee Info -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Employee Name</label>
                                    <input type="text" class="form-control" value="{{ $employee->firstname }} {{ $employee->lastname }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Employee ID</label>
                                    <input type="text" class="form-control" value="{{ $employee->employeeid }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Request Type -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Request Type <span class="text-danger">*</span></label>
                                    <select class="form-select" name="request_type" id="requestType" required>
                                        <option value="">Select Type</option>
                                        <option value="punch_in">Punch In Request</option>
                                        <option value="punch_out">Punch Out Request</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Request Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="request_date" id="requestDate" 
                                           value="{{ $today }}" max="{{ $today }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Request Time -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Request Time <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" name="request_time" id="requestTime" required>
                                    <small class="text-muted">Format: HH:MM (24-hour format)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Suggested Time</label>
                                    <div id="suggestedTime" class="p-3 bg-light rounded">
                                        @if($currentSchedule)
                                            <small>Shift: {{ $currentSchedule->shift_name }}</small><br>
                                            <small>Start: {{ \Carbon\Carbon::parse($currentSchedule->start_time)->format('h:i A') }}</small><br>
                                            <small>End: {{ \Carbon\Carbon::parse($currentSchedule->end_time)->format('h:i A') }}</small>
                                        @else
                                            <small class="text-muted">No schedule found for today</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Validation Message -->
                        <div id="validationMessage" class="alert d-none mb-3"></div>

                        <!-- Existing Records -->
                        <div id="existingRecords" class="mb-4"></div>

                        <!-- Reason -->
                        <div class="mb-4">
                            <div class="form-group">
                                <label class="form-label">Reason <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="reason" rows="4" 
                                          placeholder="Please provide a detailed reason for this manual punch request..." 
                                          minlength="10" maxlength="500" required></textarea>
                                <small class="text-muted">Minimum 10 characters, maximum 500 characters</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('manual-punch.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Instructions -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-0">
                    <h6 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Instructions</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Manual punch requests are subject to admin approval</li>
                        <li>You can only have one pending request per date and type</li>
                        <li>Requests can only be made for past or current dates</li>
                        <li>Provide a clear and honest reason for your request</li>
                        <li>You can edit or delete pending requests within 1 hour of creation</li>
                        <li>Once approved, your attendance record will be updated automatically</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #suggestedTime { min-height: 72px; }
    #existingRecords .alert { 
        margin-bottom: 0;
        border-left: 4px solid;
        border-radius: 4px;
    }
    #existingRecords .alert-warning {
        border-left-color: #ffc107;
        background-color: #fff3cd;
        color: #856404;
    }
    #existingRecords .alert-danger {
        border-left-color: #dc3545;
        background-color: #f8d7da;
        color: #721c24;
    }
    #existingRecords .alert-info {
        border-left-color: #17a2b8;
        background-color: #d1ecf1;
        color: #0c5460;
    }
    #existingRecords .alert-success {
        border-left-color: #28a745;
        background-color: #d4edda;
        color: #155724;
    }
    .existing-record-details {
        margin-top: 5px;
        padding: 8px;
        background: rgba(255,255,255,0.3);
        border-radius: 3px;
        font-size: 0.9em;
    }
</style>

<script>
    $(document).ready(function() {
        const requestType = $('#requestType');
        const requestDate = $('#requestDate');
        const requestTime = $('#requestTime');
        const validationMessage = $('#validationMessage');
        const existingRecords = $('#existingRecords');
        const submitBtn = $('#submitBtn');

        // Check availability when type or date changes
        function checkAvailability() {
            const type = requestType.val();
            const date = requestDate.val();

            if (!type || !date) {
                validationMessage.addClass('d-none');
                existingRecords.empty();
                submitBtn.prop('disabled', false);
                return;
            }

            validationMessage.removeClass('d-none').addClass('alert-info')
                .html('<i class="fas fa-spinner fa-spin me-2"></i> Checking availability...');

            submitBtn.prop('disabled', true);

            $.ajax({
                url: '{{ route("manual-punch.check-availability") }}',
                method: 'GET',
                data: { 
                    type: type, 
                    date: date,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    validationMessage.removeClass('alert-info alert-danger alert-success alert-warning')
                        .addClass(response.can_request ? 'alert-success' : 'alert-danger')
                        .html('<i class="fas fa-' + (response.can_request ? 'check-circle' : 'times-circle') + ' me-2"></i>' + response.message);

                    existingRecords.empty();
                    
                    if (response.existing_record) {
                        let alertClass = 'alert-warning';
                        let icon = 'fa-exclamation-triangle';
                        let title = '';
                        let details = '';
                        
                        switch(response.existing_record.type) {
                            case 'existing_punch_in':
                                title = '<strong>Existing Punch In Found</strong>';
                                details = `You already have a punch in recorded at <strong>${response.existing_record.time}</strong> on <strong>${response.existing_record.date}</strong>.`;
                                alertClass = 'alert-danger';
                                icon = 'fa-ban';
                                break;
                                
                            case 'existing_punch_out':
                                title = '<strong>Existing Punch Out Found</strong>';
                                details = `You already have a punch out recorded at <strong>${response.existing_record.time}</strong> on <strong>${response.existing_record.date}</strong>.`;
                                alertClass = 'alert-danger';
                                icon = 'fa-ban';
                                break;
                                
                            case 'pending_request':
                                title = '<strong>Pending Request Found</strong>';
                                details = `You have a pending ${type.replace('_', ' ')} request for <strong>${response.existing_record.time}</strong> on <strong>${response.existing_record.date}</strong>.`;
                                alertClass = 'alert-info';
                                icon = 'fa-clock';
                                break;
                                
                            case 'missing_punch_in':
                                title = '<strong>Punch In Required</strong>';
                                details = `You need to have a punch in before submitting a punch out request for <strong>${response.existing_record.date}</strong>.`;
                                alertClass = 'alert-warning';
                                icon = 'fa-exclamation-circle';
                                break;
                        }
                        
                        existingRecords.html(`
                            <div class="alert ${alertClass}">
                                <i class="fas ${icon} me-2"></i>
                                ${title}
                                <div class="existing-record-details mt-2">
                                    ${details}
                                </div>
                            </div>
                        `);
                        
                        // If there's an existing punch, suggest a different time
                        if (response.existing_record.raw_time) {
                            const existingTime = response.existing_record.raw_time;
                            const formattedTime = existingTime.substring(0, 5); // Get HH:MM format
                            requestTime.val(formattedTime);
                        }
                    }

                    submitBtn.prop('disabled', !response.can_request);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    validationMessage.removeClass('alert-info').addClass('alert-danger')
                        .html('<i class="fas fa-times-circle me-2"></i> Error checking availability. Please try again.');
                    submitBtn.prop('disabled', true);
                }
            });
        }

        // Set default time based on schedule
        function setDefaultTime() {
            @if($currentSchedule)
                const type = requestType.val();
                if (type === 'punch_in') {
                    requestTime.val('{{ \Carbon\Carbon::parse($currentSchedule->start_time)->format("H:i") }}');
                } else if (type === 'punch_out') {
                    requestTime.val('{{ \Carbon\Carbon::parse($currentSchedule->end_time)->format("H:i") }}');
                }
            @endif
        }

        // Event listeners
        requestType.on('change', function() {
            checkAvailability();
            setDefaultTime();
        });

        requestDate.on('change', checkAvailability);
        requestTime.on('change', function() {
            // Validate time format
            const timeRegex = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/;
            if (!timeRegex.test($(this).val())) {
                validationMessage.removeClass('d-none').addClass('alert-danger')
                    .html('<i class="fas fa-times-circle me-2"></i> Invalid time format. Use HH:MM (24-hour format)');
                submitBtn.prop('disabled', true);
            } else {
                validationMessage.addClass('d-none');
                if (requestType.val() && requestDate.val()) {
                    submitBtn.prop('disabled', false);
                }
            }
        });

        // Check on page load if type is already selected
        if (requestType.val()) {
            checkAvailability();
        }

        // Form validation
        $('#requestForm').on('submit', function(e) {
            const reason = $('textarea[name="reason"]').val().trim();
            if (reason.length < 10) {
                e.preventDefault();
                validationMessage.removeClass('d-none').addClass('alert-danger')
                    .html('<i class="fas fa-times-circle me-2"></i> Reason must be at least 10 characters');
                $('textarea[name="reason"]').focus();
                return false;
            }
            
            // Double-check availability before submitting
            if (submitBtn.prop('disabled')) {
                e.preventDefault();
                validationMessage.removeClass('d-none').addClass('alert-danger')
                    .html('<i class="fas fa-times-circle me-2"></i> Cannot submit request. Please check the errors above.');
                return false;
            }
        });
    });
</script>
@endsection