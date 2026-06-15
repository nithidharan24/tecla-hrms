@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Assets Assignment</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('assets-assignment.index') }}">Assets Assignment</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('assets-assignment.edit', $assignment->id) }}">Edit Assignment</a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Form Start -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card shadow-sm">
                <h4 class="card-header">Edit Asset Assignment</h4>
                <div class="card-body">
                    <form id="editAssignmentForm" method="POST" action="{{ route('assets-assignment.update', $assignment->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="employee_id">Select Employee <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="employee_id" id="employee_id" required>
                                    <option value="" disabled>Select an Employee</option>
                                    @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ $employee->id == $assignment->employee_id ? 'selected' : '' }}>
                                        {{ $employee->firstname }} {{ $employee->lastname }}
                                    </option>
                                    @endforeach
                                </select>
                                <span id="employeeIdError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="assigned_date">Assigned Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="assigned_date" id="assigned_date" value="{{ $assignment->assigned_date }}">
                                <span id="assignedDateError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="return_date">Expected Return Date</label>
                                <input type="date" class="form-control" name="return_date" id="return_date" value="{{ $assignment->return_date }}">
                                <span id="returnDateError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="condition">Condition <span class="text-danger">*</span></label>
                                <select class="form-control" name="condition" id="condition">
                                    <option value="">Select Condition</option>
                                    <option value="New" {{ $assignment->condition == 'New' ? 'selected' : '' }}>New</option>
                                    <option value="Excellent" {{ $assignment->condition == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                    <option value="Good" {{ $assignment->condition == 'Good' ? 'selected' : '' }}>Good</option>
                                    <option value="Fair" {{ $assignment->condition == 'Fair' ? 'selected' : '' }}>Fair</option>
                                    <option value="Poor" {{ $assignment->condition == 'Poor' ? 'selected' : '' }}>Poor</option>
                                </select>
                                <span id="conditionError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control" name="remarks" id="remarks" rows="3" placeholder="Enter any remarks or notes" style="resize: none">{{ $assignment->remarks }}</textarea>
                                <span id="remarksError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row mt-4 text-center">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <button class="btn btn-primary" type="submit">Update Assignment</button>
                                <a href="{{ route('assets-assignment.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                                @if($assignment->status == 'assigned')
                                <a href="{{ route('assets-assignment.return', $assignment->id) }}" class="btn btn-success ms-2" 
                                   onclick="return confirm('Are you sure you want to mark this asset as returned?')">
                                    <i class="fa-solid fa-rotate-left me-1"></i> Mark as Returned
                                </a>
                                @endif
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Form End -->

    <script>
        $(document).ready(function() {
            // Initialize select2
            $('#employee_id').select2({
                placeholder: "Select an employee",
                allowClear: true
            });

            // Validate fields on input change
            $('#employee_id, #assigned_date, #condition').on('input change', function() {
                validateFields();
            });

            // Validation Function
            function validateFields() {
                var isValid = true;

                // Validate Employee
                if ($('#employee_id').val() === null || $('#employee_id').val() === '') {
                    $('#employeeIdError').text('Please select an employee.');
                    isValid = false;
                } else {
                    $('#employeeIdError').text('');
                }

                // Validate Assigned Date
                if ($('#assigned_date').val() === '') {
                    $('#assignedDateError').text('Assigned Date is required.');
                    isValid = false;
                } else {
                    $('#assignedDateError').text('');
                }

                // Validate Condition
                if ($('#condition').val() === '') {
                    $('#conditionError').text('Condition is required.');
                    isValid = false;
                } else {
                    $('#conditionError').text('');
                }

                // Validate Return Date
                const assignedDate = new Date($('#assigned_date').val());
                const returnDate = new Date($('#return_date').val());
                if ($('#return_date').val() && returnDate <= assignedDate) {
                    $('#returnDateError').text('Return date must be after assigned date.');
                    isValid = false;
                } else {
                    $('#returnDateError').text('');
                }

                return isValid;
            }

            // Form Submission
            $('#editAssignmentForm').on('submit', function(e) {
                if (!validateFields()) {
                    e.preventDefault();
                    alert('Please fix the validation errors before submitting.');
                    return false;
                }
                
                // If validation passes, allow normal form submission
                const submitButton = $(this).find('button[type="submit"]');
                submitButton.prop('disabled', true);
                submitButton.html('<i class="fas fa-spinner fa-spin"></i> Updating...');
                
                // Form will submit normally
            });
        });
    </script>

</div>
@endsection