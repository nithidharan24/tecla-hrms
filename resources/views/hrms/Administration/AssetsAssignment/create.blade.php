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
                    <li class="breadcrumb-item active"><a href="{{ route('assets-assignment.create') }}">Assign Asset</a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Form Start -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card shadow-sm">
                <h4 class="card-header">Assign Asset to Employee</h4>
                <div class="card-body">
                    <form id="assignAssetForm" method="POST" action="{{ route('assets-assignment.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="asset_id">Select Asset <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="asset_id" id="asset_id" required>
                                    <option value="" disabled selected>Select an Asset</option>
                                    @foreach ($assets as $asset)
                                    <option value="{{ $asset->id }}" 
                                        data-available="{{ $asset->available_quantity }}"
                                        data-model="{{ $asset->model }}"
                                        data-serial="{{ $asset->serial_number }}"
                                        {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                        {{ $asset->asset_name }} ({{ $asset->asset_id }}) - Available: {{ $asset->available_quantity }}
                                    </option>
                                    @endforeach
                                </select>
                                <span id="assetIdError" class="text-danger"></span>
                                <div id="asset-details" class="mt-2 p-2 bg-light rounded" style="display: none;">
                                    <small>
                                        <strong>Model:</strong> <span id="asset-model">-</span><br>
                                        <strong>Serial No:</strong> <span id="asset-serial">-</span><br>
                                        <strong>Available:</strong> <span id="asset-available">-</span>
                                    </small>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="employee_id">Select Employee <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="employee_id" id="employee_id" required>
                                    <option value="" disabled selected>Select an Employee</option>
                                    @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->firstname }} {{ $employee->lastname }}
                                    </option>
                                    @endforeach
                                </select>
                                <span id="employeeIdError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="assigned_date">Assigned Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="assigned_date" id="assigned_date" value="{{ old('assigned_date', date('Y-m-d')) }}">
                                <span id="assignedDateError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="return_date">Expected Return Date (Optional)</label>
                                <input type="date" class="form-control" name="return_date" id="return_date" value="{{ old('return_date') }}">
                                <span id="returnDateError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="condition">Condition <span class="text-danger">*</span></label>
                                <select class="form-control" name="condition" id="condition">
                                    <option value="">Select Condition</option>
                                    <option value="New" {{ old('condition') == 'New' ? 'selected' : '' }}>New</option>
                                    <option value="Excellent" {{ old('condition') == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                    <option value="Good" {{ old('condition') == 'Good' ? 'selected' : '' }}>Good</option>
                                    <option value="Fair" {{ old('condition') == 'Fair' ? 'selected' : '' }}>Fair</option>
                                    <option value="Poor" {{ old('condition') == 'Poor' ? 'selected' : '' }}>Poor</option>
                                </select>
                                <span id="conditionError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control" name="remarks" id="remarks" rows="3" placeholder="Enter any remarks or notes" style="resize: none">{{ old('remarks') }}</textarea>
                                <span id="remarksError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row mt-4 text-center">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <button class="btn btn-primary" type="submit" id="submitButton">Assign Asset</button>
                                <a href="{{ route('assets-assignment.index') }}" class="btn btn-secondary ms-2">Cancel</a>
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
            $('#asset_id, #employee_id').select2({
                placeholder: "Select an option",
                allowClear: true
            });

            // Show asset details when asset is selected
            $('#asset_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const available = selectedOption.data('available');
                const model = selectedOption.data('model');
                const serial = selectedOption.data('serial');

                if (selectedOption.val()) {
                    $('#asset-model').text(model || '-');
                    $('#asset-serial').text(serial || '-');
                    $('#asset-available').text(available);
                    $('#asset-details').show();

                    // Show warning if no available quantity
                    if (available <= 0) {
                        $('#assetIdError').text('This asset is not available for assignment.');
                    } else {
                        $('#assetIdError').text('');
                    }
                } else {
                    $('#asset-details').hide();
                    $('#assetIdError').text('');
                }
            });

            // Validate fields on input change
            $('#asset_id, #employee_id, #assigned_date, #condition').on('input change', function() {
                validateFields();
            });

            // Validation Function
            function validateFields() {
                var isValid = true;

                // Validate Asset
                if ($('#asset_id').val() === null || $('#asset_id').val() === '') {
                    $('#assetIdError').text('Please select an asset.');
                    isValid = false;
                } else {
                    const available = $('#asset_id').find('option:selected').data('available');
                    if (available <= 0) {
                        $('#assetIdError').text('This asset is not available for assignment.');
                        isValid = false;
                    } else {
                        $('#assetIdError').text('');
                    }
                }

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
            $('#assignAssetForm').on('submit', function(e) {
                if (!validateFields()) {
                    e.preventDefault();
                    alert('Please fix the validation errors before submitting.');
                    return false;
                }
                
                // If validation passes, allow normal form submission
                const submitButton = $(this).find('button[type="submit"]');
                submitButton.prop('disabled', true);
                submitButton.html('<i class="fas fa-spinner fa-spin"></i> Assigning...');
                
                // Form will submit normally
            });

            // Trigger change on asset_id to show details if there's a value
            @if(old('asset_id'))
                $('#asset_id').trigger('change');
            @endif
        });
    </script>

</div>
@endsection