{{-- resources/views/hrms/Employee/Schedule/create.blade.php --}}
@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Scheduling Details</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('scheduling.index') }}">Scheduling</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('scheduling.create') }}">Add</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card shadow-sm">
                <h4 class="card-header">Add Schedule</h4>
                <div class="card-body">
                    <form id="addSchedule" method="POST" action="{{ route('scheduling.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="department">Department <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="department" id="department" required>
                                    <option value="" disabled selected>Select a Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->department }}</option>
                                    @endforeach
                                </select>
                                <span id="departmentError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="employees">Employee Names <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="employees[]" id="employees" multiple="multiple" disabled>
                                    <option value="" disabled>Select employees first by choosing a department</option>
                                </select>
                                <span id="employeesError" class="text-danger"></span>
                                <small class="form-text text-muted">You can select multiple employees</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="schedule_start_date">Schedule Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="schedule_start_date" id="schedule_start_date" 
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                <span id="scheduleStartDateError" class="text-danger"></span>
                                <small class="form-text text-muted">When should this schedule begin? (Cannot be today)</small>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="schedule_end_date">Schedule End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="schedule_end_date" id="schedule_end_date" 
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                <span id="scheduleEndDateError" class="text-danger"></span>
                                <small class="form-text text-muted">When should this schedule end? (Cannot be today)</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="shift">Shifts <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="shift" id="shift" required>
                                    <option value="" disabled selected>Select a Shift</option>
                                    @foreach ($shifts as $shift)
                                        <option value="{{ $shift->id }}"
                                            data-start="{{ $shift->start_time }}"
                                            data-end="{{ $shift->end_time }}"
                                            data-break="{{ $shift->break_time }}"
                                            data-days="{{ $shift->days_of_week }}">
                                            {{ $shift->shift_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span id="shiftError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="repeat_every_week">Repeat Every Week <span class="text-danger">*</span></label>
                                <select class="form-control" name="repeat_every_week" id="repeat_every_week" required>
                                    <option value="" disabled selected>Should this schedule repeat weekly?</option>
                                    <option value="1">Yes - repeat every week</option>
                                    <option value="0">No - one-time schedule</option>
                                </select>
                                <span id="repeatEveryWeekError" class="text-danger"></span>
                                <small class="form-text text-muted">Select “Yes” to auto-assign this shift every week.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-4">
                                <label for="start_time">Start Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="start_time" id="start_time" readonly>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-4">
                                <label for="end_time">End Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="end_time" id="end_time" readonly>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-4">
                                <label for="break_time">Break Time (minutes) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="break_time" id="break_time" readonly>
                            </div>
                        </div>

                        <div class="row" id="daysOfWeekSection">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
                                <label>Days of the Week:</label>
                                <div>
                                    @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input days-of-week" type="checkbox" name="days_of_week[]" value="{{ $day }}" id="{{ strtolower($day) }}" disabled>
                                            <label class="form-check-label" for="{{ strtolower($day) }}">{{ $day }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="row" id="scheduleSummary" style="display: none;">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Schedule Summary:</h6>
                                    <div id="summaryContent"></div>
                                </div>
                            </div>
                        </div>

                        <small class="form-text text-warning">
                            ⚠️ Select dates that cover all days of the chosen shift (e.g., if shift is Mon–Fri, your dates must include Mon to Fri).
                        </small>

                        <div class="row mt-4 text-center">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <button class="btn btn-primary" type="submit" id="submitBtn" disabled>Submit</button>
                                <div id="loadingSpinner" class="spinner-border text-primary d-none" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const employeeByDepartmentRoute = "{{ route('scheduling.employees-by-department', ['departmentId' => ':id']) }}";

    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select an option",
            allowClear: true
        });

        $('#employees').select2({
            placeholder: "Select employees",
            allowClear: true,
            multiple: true
        });

        $('#schedule_start_date').on('change', function() {
            const startDate = $(this).val();
            const endDateField = $('#schedule_end_date');

            if (startDate) {
                endDateField.attr('min', startDate);
                if (endDateField.val() && endDateField.val() < startDate) {
                    endDateField.val('');
                }
            }

            updateScheduleSummary();
            checkFormValidity();
        });

        $('#schedule_end_date').on('change', function() {
            const endDate = $(this).val();
            const startDate = $('#schedule_start_date').val();

            if (startDate && endDate && endDate < startDate) {
                alert('End date cannot be before start date');
                $(this).val('');
                return;
            }

            updateScheduleSummary();
            checkFormValidity();
        });

        $('#department').on('change', function() {
            const departmentId = $(this).val();
            const employeesSelect = $('#employees');

            if (departmentId) {
                employeesSelect.prop('disabled', true);
                employeesSelect.empty().append('<option value="">Loading employees...</option>');

                const ajaxUrl = employeeByDepartmentRoute.replace(':id', departmentId);

                $.ajax({
                    url: ajaxUrl,
                    type: 'GET',
                    success: function(employees) {
                        employeesSelect.empty();

                        if (employees.length > 0) {
                            employeesSelect.append('<option value="" disabled>Select employees</option>');
                            employees.forEach(function(employee) {
                                employeesSelect.append(
                                    `<option value="${employee.id}">${employee.firstname} ${employee.lastname}</option>`
                                );
                            });
                            employeesSelect.prop('disabled', false);
                        } else {
                            employeesSelect.append('<option value="" disabled>No employees found in this department</option>');
                        }

                        employeesSelect.select2({
                            placeholder: "Select employees",
                            allowClear: true,
                            multiple: true
                        });

                        checkFormValidity();
                    },
                    error: function() {
                        employeesSelect.empty().append('<option value="" disabled>Error loading employees</option>');
                        employeesSelect.prop('disabled', false);
                    }
                });
            } else {
                employeesSelect.empty().append('<option value="" disabled>Select a department first</option>');
                employeesSelect.prop('disabled', true);
            }

            checkFormValidity();
        });

        function checkFormValidity() {
            const isValid = $('#department').val() &&
                $('#employees').val() && $('#employees').val().length > 0 &&
                $('#shift').val() &&
                $('#repeat_every_week').val() !== null &&
                $('#repeat_every_week').val() !== "" &&
                $('#schedule_start_date').val() &&
                $('#schedule_end_date').val();

            $('#submitBtn').prop('disabled', !isValid);
        }

        $('#department, #employees, #shift, #repeat_every_week, #schedule_start_date, #schedule_end_date').on('change input', function() {
            checkFormValidity();
            updateScheduleSummary();
        });

        $('#shift').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const startTime = selectedOption.data('start');
            const endTime = selectedOption.data('end');
            const breakTime = selectedOption.data('break');
            const daysOfWeek = selectedOption.data('days');

            $('#start_time').val(startTime);
            $('#end_time').val(endTime);
            $('#break_time').val(breakTime);

            if (daysOfWeek) {
                const daysArray = daysOfWeek.split(',');
                $('.days-of-week').prop('checked', false);
                daysArray.forEach(day => {
                    $(`#${day.toLowerCase()}`).prop('checked', true);
                });
            }

            updateScheduleSummary();
            checkFormValidity();
        });

        function updateScheduleSummary() {
            const startDate = $('#schedule_start_date').val();
            const endDate = $('#schedule_end_date').val();
            const repeatWeekly = $('#repeat_every_week').val();
            const shift = $('#shift option:selected').text();
            const employees = $('#employees option:selected').length;

            if (startDate && endDate && repeatWeekly !== "" && repeatWeekly !== null && shift && employees > 0) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                const totalWeeks = Math.ceil(diffDays / 7);

                let summaryHtml = `
                    <strong>Duration:</strong> ${diffDays} days (${totalWeeks} week${totalWeeks > 1 ? 's' : ''})<br>
                    <strong>Employees:</strong> ${employees} selected<br>
                    <strong>Shift:</strong> ${shift}<br>
                    <strong>Repeat Weekly:</strong> ${repeatWeekly === "1" ? 'Yes' : 'No'}<br>
                    <strong>Period:</strong> ${formatDate(startDate)} to ${formatDate(endDate)}
                `;

                $('#summaryContent').html(summaryHtml);
                $('#scheduleSummary').show();
            } else {
                $('#scheduleSummary').hide();
            }
        }

        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }

        $("#addSchedule").validate({
            rules: {
                department: "required",
                "employees[]": "required",
                schedule_start_date: "required",
                schedule_end_date: {
                    required: true,
                    greaterThan: "#schedule_start_date"
                },
                repeat_every_week: "required",
                shift: "required"
            },
            messages: {
                department: "Please select a department",
                "employees[]": "Please select at least one employee",
                schedule_start_date: "Please select a start date",
                schedule_end_date: {
                    required: "Please select an end date",
                    greaterThan: "End date must be after start date"
                },
                repeat_every_week: "Please choose if the schedule should repeat weekly",
                shift: "Please select a shift"
            },
            errorElement: "span",
            errorPlacement: function(error, element) {
                error.addClass("text-danger");
                if (element.attr("name") === "employees[]") {
                    error.insertAfter("#employees").next('.select2-container');
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                $('#submitBtn').prop('disabled', true);
                $('#loadingSpinner').removeClass('d-none');
                form.submit();
            }
        });

        $.validator.addMethod("greaterThan", function(value, element, param) {
            return this.optional(element) || new Date(value) > new Date($(param).val());
        });
    });
</script>
@endsection