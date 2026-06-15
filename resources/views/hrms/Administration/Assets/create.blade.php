@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Assets</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('assets.index') }}">Assets</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('assets.create') }}">Add</a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Form Start -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card shadow-sm">
                <h4 class="card-header">Add Asset</h4>
                <div class="card-body">
                    <form id="addAssetForm" method="POST" action="{{ route('assets.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="asset_name">Asset Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="asset_name" id="asset_name" placeholder="Enter Asset Name" value="{{ old('asset_name') }}">
                                <span id="assetNameError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="asset_id">Asset ID</label>
                                <input type="text" class="form-control" name="asset_id" id="asset_id" placeholder="Auto-generated" readonly>
                                <span id="assetIdError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="purchase_date">Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="purchase_date" id="purchase_date" value="{{ old('purchase_date') }}">
                                <span id="purchaseDateError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="purchase_from">Purchase From <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="purchase_from" id="purchase_from" placeholder="Enter Vendor Name" value="{{ old('purchase_from') }}">
                                <span id="purchaseFromError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="manufacturer">Manufacturer <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="manufacturer" id="manufacturer" placeholder="Enter Manufacturer" value="{{ old('manufacturer') }}">
                                <span id="manufacturerError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="model">Model <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="model" id="model" placeholder="Enter Model" value="{{ old('model') }}">
                                <span id="modelError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="serial_number">Serial Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="serial_number" id="serial_number" placeholder="Enter Serial Number" value="{{ old('serial_number') }}">
                                <span id="serialNumberError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="supplier">Supplier <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="supplier" id="supplier" placeholder="Enter Supplier" value="{{ old('supplier') }}">
                                <span id="supplierError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="condition">Condition <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="condition" id="condition" placeholder="Enter Condition" value="{{ old('condition') }}">
                                <span id="conditionError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="warranty">Warranty (in months) <span class="text-danger">*</span></label>
                               <input type="number" class="form-control" name="warranty" id="warranty" placeholder="Enter Warranty Period" min="0" value="{{ old('warranty') }}">
                                <span id="warrantyError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="asset_user">Asset User <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="asset_user" id="asset_user" required>
                                    <option value="" disabled selected>Select a User</option>
                                    @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('asset_user') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->firstname }} {{ $employee->lastname }}
                                    </option>
                                    @endforeach
                                </select>
                                <span id="assetUserError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="location">Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="location" id="location" placeholder="Enter Location" value="{{ old('location') }}">
                                <span id="locationError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="assigned_date">Assigned Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="assigned_date" id="assigned_date" value="{{ old('assigned_date') }}">
                                <span id="assignedDateError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="return_date">Return Date (Optional)</label>
                                <input type="date" class="form-control" name="return_date" id="return_date" value="{{ old('return_date') }}">
                                <span id="returnDateError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="asset_image">Asset Image</label>
                                <input type="file" class="form-control" name="asset_image" id="asset_image" accept="image/*">
                                <span id="assetImageError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control" name="remarks" id="remarks" rows="3" placeholder="Enter Remarks" style="resize: none">{{ old('remarks') }}</textarea>
                                <span id="remarksError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
                                <label for="description">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="description" id="description" rows="4" placeholder="Enter Description" style="resize: none">{{ old('description') }}</textarea>
                                <span id="descriptionError" class="text-danger"></span>
                            </div>
                        </div>

                       <div class="row mt-4 text-center">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <button class="btn btn-primary" type="submit" id="submitButton">Submit</button>
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
        // Initialize select2 if applicable for asset user
        $('#asset_user').select2({
            placeholder: "Select a User",
            allowClear: true
        });

        // Function to generate Asset Id
        function generateAssetId() {
            let prefix = 'AST';
            let suffix = Math.floor(Math.random() * 10000);
            return `${prefix}-${String(suffix).padStart(4, '0')}`;
        }

        // Generate asset ID on page load
        $('#asset_id').val(generateAssetId());

        // Validate fields on input change
        $('#asset_name, #purchase_date, #purchase_from, #manufacturer, #model, #serial_number, #supplier, #condition, #warranty, #description, #asset_user, #location, #assigned_date').on('input change', function() {
            validateFields();
        });

        // Validation Function
        function validateFields() {
            var isValid = true;

            // Validate Asset Name
            if ($('#asset_name').val() === '') {
                $('#assetNameError').text('Asset Name is required.');
                isValid = false;
            } else {
                $('#assetNameError').text('');
            }

            // Validate Purchase Date
            if ($('#purchase_date').val() === '') {
                $('#purchaseDateError').text('Purchase Date is required.');
                isValid = false;
            } else {
                $('#purchaseDateError').text('');
            }

            // Validate Purchase From
            if ($('#purchase_from').val() === '') {
                $('#purchaseFromError').text('Purchase From is required.');
                isValid = false;
            } else {
                $('#purchaseFromError').text('');
            }

            // Validate Manufacturer
            if ($('#manufacturer').val() === '') {
                $('#manufacturerError').text('Manufacturer is required.');
                isValid = false;
            } else {
                $('#manufacturerError').text('');
            }

            // Validate Model
            if ($('#model').val() === '') {
                $('#modelError').text('Model is required.');
                isValid = false;
            } else {
                $('#modelError').text('');
            }

            // Validate Serial Number
            if ($('#serial_number').val() === '') {
                $('#serialNumberError').text('Serial Number is required.');
                isValid = false;
            } else {
                $('#serialNumberError').text('');
            }

            // Validate Supplier
            if ($('#supplier').val() === '') {
                $('#supplierError').text('Supplier is required.');
                isValid = false;
            } else {
                $('#supplierError').text('');
            }

            // Validate Condition
            if ($('#condition').val() === '') {
                $('#conditionError').text('Condition is required.');
                isValid = false;
            } else {
                $('#conditionError').text('');
            }

            // Validate Warranty
            if ($('#warranty').val() === '' || $('#warranty').val() < 0) {
                $('#warrantyError').text('Warranty must be a positive number.');
                isValid = false;
            } else {
                $('#warrantyError').text('');
            }

            // Validate Location
            if ($('#location').val() === '') {
                $('#locationError').text('Location is required.');
                isValid = false;
            } else {
                $('#locationError').text('');
            }

            // Validate Assigned Date
            if ($('#assigned_date').val() === '') {
                $('#assignedDateError').text('Assigned Date is required.');
                isValid = false;
            } else {
                $('#assignedDateError').text('');
            }

            // Validate Description
            var description = $('#description').val();
            if (description.length < 10 || description.length > 255 || /^\s/.test(description)) {
                if (description.length < 10) {
                    $('#descriptionError').text('Description must be at least 10 characters.');
                } else if (description.length > 255) {
                    $('#descriptionError').text('Description must not exceed 255 characters.');
                } else if (/^\s/.test(description)) {
                    $('#descriptionError').text('Description should not start with a space.');
                }
                isValid = false;
            } else {
                $('#descriptionError').text('');
            }

            // Validate Asset User
            if ($('#asset_user').val() === null || $('#asset_user').val() === '') {
                $('#assetUserError').text('Asset User is required.');
                isValid = false;
            } else {
                $('#assetUserError').text('');
            }

            return isValid;
        }

        // Form Submission - REMOVED preventDefault to allow normal form submission
        $('#addAssetForm').on('submit', function(e) {
            if (!validateFields()) {
                e.preventDefault();
                alert('Please fix the validation errors before submitting.');
                return false;
            }
            
            // If validation passes, allow normal form submission
            const submitButton = $(this).find('button[type="submit"]');
            submitButton.prop('disabled', true);
            submitButton.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            
            // Form will submit normally
        });
    });
</script>

</div>
@endsection