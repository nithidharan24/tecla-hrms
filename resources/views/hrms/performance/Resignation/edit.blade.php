@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Edit Resignation</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Edit Resignation</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Form to Edit Resignation -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <h4 class="card-header">Edit Resignation</h4>
                <div class="card-body">
                    <form id="editResignationForm" action="{{ route('resignation.update', $resignation->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Resigning Employee -->
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
                                <label for="employee">Resigning Employee <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="employee" id="employee" disabled>
                                    <option value="" disabled>Select an Employee</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" 
                                            {{ $employee->id == $resignation->resignation_employee_id ? 'selected' : '' }}>
                                            {{ $employee->firstname }} {{ $employee->lastname }}
                                        </option>
                                    @endforeach
                                </select>
                                <span id="employeeError" class="text-danger"></span>
                                 <!-- Hidden input to submit the selected employee -->
                                <input type="hidden" name="employee" value="{{ $resignation->resignation_employee_id }}">
                            </div>
                        </div>

                        <!-- Notice Date and Resignation Date -->
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="notice_date">Notice Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="notice_date" id="notice_date" value="{{ $resignation->notice_date }}">
                                <span id="noticeDateError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="resignation_date">Resignation Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="resignation_date" id="resignation_date" value="{{ $resignation->resignation_date }}">
                                <span id="resignationDateError" class="text-danger"></span>
                            </div>
                        </div>

                        <!-- Reason Field -->
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <label for="reason">Reason <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="reason" id="reason" rows="4">{{ $resignation->reason }}</textarea>
                                <span id="reasonError" class="text-danger"></span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row mt-4 text-center">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <button class="btn btn-primary" type="submit">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Page Content -->

<script>
    $(document).ready(function() {
        // Initialize select2 with placeholder
        $('#employee').select2({
            placeholder: "Select an Employee",
            allowClear: true
        });

        // Function to validate all fields
        function validateFields() {
            var isValid = true;

            // Validate employee selection
            if ($('#employee').val() === null) {
                $('#employeeError').text('Please select an employee.');
                isValid = false;
            } else {
                $('#employeeError').text('');
            }

            // Validate notice date
            if ($('#notice_date').val() === '') {
                $('#noticeDateError').text('Notice date is required.');
                isValid = false;
            } else {
                $('#noticeDateError').text('');
            }

            // Validate resignation date
            if ($('#resignation_date').val() === '') {
                $('#resignationDateError').text('Resignation date is required.');
                isValid = false;
            } else {
                $('#resignationDateError').text('');
            }

            // Validate reason field
            var reason = $('#reason').val();
            if (reason.length < 10 || reason.length > 255 || /^\s/.test(reason)) {
                if (reason.length < 10) {
                    $('#reasonError').text('Reason must be at least 10 characters.');
                } else if (reason.length > 255) {
                    $('#reasonError').text('Reason must not exceed 255 characters.');
                } else if (/^\s/.test(reason)) {
                    $('#reasonError').text('Reason should not start with a space.');
                }
                isValid = false;
            } else {
                $('#reasonError').text('');
            }

            return isValid;
        }

        // Validate fields on change or input events
        $('#employee, #notice_date, #resignation_date, #reason').on('change input', function() {
            validateFields();
        });

        // Form validation on submit
        $('#editResignationForm').on('submit', function(e) {
            if (!validateFields()) {
                e.preventDefault(); // Prevent form submission if validation fails
            }
        });
    });
</script>
@endsection
