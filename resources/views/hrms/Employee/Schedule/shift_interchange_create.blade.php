@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Request Shift Interchange</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('scheduling.index') }}">Scheduling</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('scheduling.shift-interchange') }}">Shift Interchange</a></li>
                    <li class="breadcrumb-item active">Create Request</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0">Create Shift Interchange Request</h4>
                    <p class="text-muted mb-0">Request to swap your shift with another employee for specific dates</p>
                </div>
                <div class="card-body">
                    <form id="interchangeForm" method="POST" action="{{ route('scheduling.shift-interchange.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="requester_schedule_id" class="form-label">Your Schedule <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="requester_schedule_id" id="requester_schedule_id" required>
                                    <option value="">Select your schedule</option>
                                    @foreach($mySchedules as $schedule)
                                        <option value="{{ $schedule->id }}" 
                                                data-shift="{{ $schedule->shift_name }}"
                                                data-start="{{ $schedule->start_time }}"
                                                data-end="{{ $schedule->end_time }}"
                                                data-department="{{ $schedule->department }}">
                                            {{ $schedule->shift_name }} ({{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}) - {{ $schedule->department }}
                                            <br>{{ \Carbon\Carbon::parse($schedule->schedule_start_date)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($schedule->schedule_end_date)->format('M d, Y') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="target_schedule_id" class="form-label">Target Employee's Schedule <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="target_schedule_id" id="target_schedule_id" required disabled>
                                    <option value="">Select your schedule first</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label class="form-label">Select Interchange Dates <span class="text-danger">*</span></label>
                                <div id="dateSelection" class="border rounded p-3 bg-light">
                                    <p class="text-muted mb-0">Please select your schedule first to see available dates</p>
                                </div>
                                <small class="form-text text-muted">Select the specific dates you want to interchange shifts</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="reason" class="form-label">Reason for Interchange <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="reason" id="reason" rows="4" required 
                                          placeholder="Please provide a detailed reason for requesting this shift interchange..."></textarea>
                                <small class="form-text text-muted">Maximum 500 characters</small>
                            </div>
                        </div>

                        <!-- Schedule Comparison -->
                        <div class="row" id="scheduleComparison" style="display: none;">
                            <div class="col-md-12 mb-4">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Schedule Comparison</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="text-primary">Your Current Schedule</h6>
                                                <div id="yourScheduleDetails"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-success">Target Employee's Schedule</h6>
                                                <div id="targetScheduleDetails"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('scheduling.shift-interchange') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                <i class="fas fa-paper-plane me-1"></i>Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Select an option",
        allowClear: true
    });

    $('#requester_schedule_id').on('change', function() {
        const scheduleId = $(this).val();
        
        if (scheduleId) {
            // Load available employees for interchange
            loadAvailableEmployees(scheduleId);
            // Generate date selection
            generateDateSelection();
        } else {
            $('#target_schedule_id').prop('disabled', true).empty().append('<option value="">Select your schedule first</option>');
            $('#dateSelection').html('<p class="text-muted mb-0">Please select your schedule first to see available dates</p>');
            $('#scheduleComparison').hide();
        }
        
        checkFormValidity();
    });

    $('#target_schedule_id').on('change', function() {
        updateScheduleComparison();
        checkFormValidity();
    });

    function loadAvailableEmployees(scheduleId) {
        $('#target_schedule_id').prop('disabled', true).empty().append('<option value="">Loading...</option>');
        
        $.ajax({
            url: '{{ route("scheduling.shift-interchange.available-employees") }}',
            method: 'GET',
            data: { schedule_id: scheduleId },
            success: function(employees) {
                $('#target_schedule_id').empty().append('<option value="">Select target employee</option>');
                
                if (employees.length > 0) {
                    employees.forEach(function(employee) {
                        const startTime = new Date('1970-01-01T' + employee.start_time).toLocaleTimeString('en-US', {
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                        });
                        const endTime = new Date('1970-01-01T' + employee.end_time).toLocaleTimeString('en-US', {
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                        });
                        
                        $('#target_schedule_id').append(
                            `<option value="${employee.schedule_id}" 
                                     data-employee="${employee.firstname} ${employee.lastname}"
                                     data-shift="${employee.shift_name}"
                                     data-start="${employee.start_time}"
                                     data-end="${employee.end_time}"
                                     data-department="${employee.department}">
                                ${employee.firstname} ${employee.lastname} - ${employee.shift_name} (${startTime} - ${endTime}) - ${employee.department}
                            </option>`
                        );
                    });
                    $('#target_schedule_id').prop('disabled', false);
                } else {
                    $('#target_schedule_id').append('<option value="">No available employees found</option>');
                }
            },
            error: function() {
                $('#target_schedule_id').empty().append('<option value="">Error loading employees</option>');
            }
        });
    }

    function generateDateSelection() {
        const selectedSchedule = $('#requester_schedule_id option:selected');
        
        if (!selectedSchedule.val()) return;
        
        // Generate next 30 days for selection
        let dateHtml = '<div class="row">';
        const today = new Date();
        
        for (let i = 0; i < 30; i++) {
            const date = new Date(today);
            date.setDate(today.getDate() + i);
            
            const dateStr = date.toISOString().split('T')[0];
            const displayDate = date.toLocaleDateString('en-US', { 
                weekday: 'short', 
                month: 'short', 
                day: 'numeric' 
            });
            
            dateHtml += `
                <div class="col-md-3 col-sm-4 col-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input interchange-date" type="checkbox" 
                               name="interchange_dates[]" value="${dateStr}" id="date_${dateStr}">
                        <label class="form-check-label" for="date_${dateStr}">
                            ${displayDate}
                        </label>
                    </div>
                </div>
            `;
        }
        dateHtml += '</div>';
        
        $('#dateSelection').html(dateHtml);
        
        // Add event listener for date selection
        $('.interchange-date').on('change', checkFormValidity);
    }

    function updateScheduleComparison() {
        const yourSchedule = $('#requester_schedule_id option:selected');
        const targetSchedule = $('#target_schedule_id option:selected');
        
        if (yourSchedule.val() && targetSchedule.val()) {
            const yourStartTime = new Date('1970-01-01T' + yourSchedule.data('start')).toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
            const yourEndTime = new Date('1970-01-01T' + yourSchedule.data('end')).toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
            
            const targetStartTime = new Date('1970-01-01T' + targetSchedule.data('start')).toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
            const targetEndTime = new Date('1970-01-01T' + targetSchedule.data('end')).toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
            
            $('#yourScheduleDetails').html(`
                <p><strong>Shift:</strong> ${yourSchedule.data('shift')}</p>
                <p><strong>Time:</strong> ${yourStartTime} - ${yourEndTime}</p>
                <p><strong>Department:</strong> ${yourSchedule.data('department')}</p>
            `);
            
            $('#targetScheduleDetails').html(`
                <p><strong>Employee:</strong> ${targetSchedule.data('employee')}</p>
                <p><strong>Shift:</strong> ${targetSchedule.data('shift')}</p>
                <p><strong>Time:</strong> ${targetStartTime} - ${targetEndTime}</p>
                <p><strong>Department:</strong> ${targetSchedule.data('department')}</p>
            `);
            
            $('#scheduleComparison').show();
        } else {
            $('#scheduleComparison').hide();
        }
    }

    function checkFormValidity() {
        const requesterSchedule = $('#requester_schedule_id').val();
        const targetSchedule = $('#target_schedule_id').val();
        const selectedDates = $('.interchange-date:checked').length;
        const reason = $('#reason').val().trim();
        
        const isValid = requesterSchedule && targetSchedule && selectedDates > 0 && reason.length > 0;
        $('#submitBtn').prop('disabled', !isValid);
    }

    $('#reason').on('input', function() {
        const maxLength = 500;
        const currentLength = $(this).val().length;
        
        if (currentLength > maxLength) {
            $(this).val($(this).val().substring(0, maxLength));
        }
        
        checkFormValidity();
    });

    // Form validation
    $('#interchangeForm').on('submit', function(e) {
        const selectedDates = $('.interchange-date:checked').length;
        
        if (selectedDates === 0) {
            e.preventDefault();
            alert('Please select at least one date for the interchange.');
            return false;
        }
        
        if ($('#reason').val().trim().length === 0) {
            e.preventDefault();
            alert('Please provide a reason for the interchange request.');
            return false;
        }
        
        // Disable submit button to prevent double submission
        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Submitting...');
    });
});
</script>
@endsection