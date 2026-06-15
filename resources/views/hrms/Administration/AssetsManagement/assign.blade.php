@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Assign Asset</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('assets.index') }}">Assets</a></li>
                    <li class="breadcrumb-item active">Assign Asset</li>
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
                    <!-- Asset Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Asset Information</h5>
                            <div class="asset-info bg-light p-3 rounded">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Asset Name:</strong> {{ $asset->asset_name }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Asset ID:</strong> {{ $asset->asset_id }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Available Quantity:</strong> 
                                        <span class="badge bg-success">{{ $asset->available_quantity }}</span>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-4">
                                        <strong>Model:</strong> {{ $asset->model }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Serial Number:</strong> {{ $asset->serial_number ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Condition:</strong> {{ $asset->condition }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form id="assignAssetForm" method="POST" action="{{ route('assets.assign', $asset->id) }}">
                        @csrf

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="asset_user">Assign to Employee <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="asset_user" id="asset_user" required>
                                    <option value="" disabled selected>Select an Employee</option>
                                    @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->firstname }} {{ $employee->lastname }}
                                    </option>
                                    @endforeach
                                </select>
                                <span id="assetUserError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="quantity">Quantity to Assign <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="quantity" 
                                       value="1" min="1" max="{{ $asset->available_quantity }}" 
                                       {{ $asset->asset_type == 'unique' ? 'readonly' : '' }}>
                                <small class="form-text text-muted">Max available: {{ $asset->available_quantity }}</small>
                                <span id="quantityError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="assigned_date">Assigned Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="assigned_date" id="assigned_date" value="{{ date('Y-m-d') }}">
                                <span id="assignedDateError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="return_date">Expected Return Date</label>
                                <input type="date" class="form-control" name="return_date" id="return_date">
                                <span id="returnDateError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
                                <label for="remarks">Assignment Remarks</label>
                                <textarea class="form-control" name="remarks" id="remarks" rows="3" placeholder="Enter assignment remarks" style="resize: none">{{ old('remarks') }}</textarea>
                                <span id="remarksError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row mt-4 text-center">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <button class="btn btn-primary" type="submit" id="submitButton">Assign Asset</button>
                                <a href="{{ route('assets.index') }}" class="btn btn-secondary ms-2">Cancel</a>
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
        // Initialize select2 for employee selection
        $('#asset_user').select2({
            placeholder: "Select an Employee",
            allowClear: true
        });

        // Set minimum date for return date to assigned date
        $('#assigned_date').change(function() {
            $('#return_date').attr('min', $(this).val());
        });

        // Validate quantity
        $('#quantity').change(function() {
            const maxQuantity = {{ $asset->available_quantity }};
            const enteredQuantity = parseInt($(this).val());
            
            if (enteredQuantity > maxQuantity) {
                $('#quantityError').text(`Cannot assign more than ${maxQuantity} items.`);
                $(this).val(maxQuantity);
            } else {
                $('#quantityError').text('');
            }
        });

        // Validation Function
        function validateFields() {
            var isValid = true;

            // Validate Employee
            if ($('#asset_user').val() === null || $('#asset_user').val() === '') {
                $('#assetUserError').text('Employee selection is required.');
                isValid = false;
            } else {
                $('#assetUserError').text('');
            }

            // Validate Quantity
            const quantity = parseInt($('#quantity').val());
            const maxQuantity = {{ $asset->available_quantity }};
            if (isNaN(quantity) || quantity < 1 || quantity > maxQuantity) {
                $('#quantityError').text(`Please enter a valid quantity between 1 and ${maxQuantity}.`);
                isValid = false;
            } else {
                $('#quantityError').text('');
            }

            // Validate Assigned Date
            if ($('#assigned_date').val() === '') {
                $('#assignedDateError').text('Assigned Date is required.');
                isValid = false;
            } else {
                $('#assignedDateError').text('');
            }

            // Validate Return Date if provided
            const returnDate = $('#return_date').val();
            const assignedDate = $('#assigned_date').val();
            if (returnDate && returnDate < assignedDate) {
                $('#returnDateError').text('Return date cannot be before assigned date.');
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
        });
    });
    </script>

</div>
@endsection