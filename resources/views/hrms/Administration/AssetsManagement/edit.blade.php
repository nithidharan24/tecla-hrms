@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Assets Management</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('assets.index') }}">Assets Management</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('assets.edit', $asset->id) }}">Edit Asset</a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Form Start -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card shadow-sm">
                <h4 class="card-header">Edit Asset</h4>
                <div class="card-body">
                    <form id="editAssetForm" method="POST" action="{{ route('assets.update', $asset->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="asset_name">Asset Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="asset_name" id="asset_name" value="{{ $asset->asset_name }}" placeholder="Enter Asset Name">
                                <span id="assetNameError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="quantity" value="{{ $asset->quantity }}" placeholder="Enter Quantity" min="1">
                                <span id="quantityError" class="text-danger"></span>
                                <small class="text-muted">Currently assigned: {{ $asset->quantity - $asset->available_quantity }}</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="purchase_date">Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="purchase_date" id="purchase_date" value="{{ $asset->purchase_date }}">
                                <span id="purchaseDateError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="purchase_from">Purchase From <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="purchase_from" id="purchase_from" value="{{ $asset->purchase_from }}" placeholder="Enter Vendor Name">
                                <span id="purchaseFromError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="manufacturer">Manufacturer <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="manufacturer" id="manufacturer" value="{{ $asset->manufacturer }}" placeholder="Enter Manufacturer">
                                <span id="manufacturerError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="model">Model <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="model" id="model" value="{{ $asset->model }}" placeholder="Enter Model">
                                <span id="modelError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="serial_number">Serial Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="serial_number" id="serial_number" value="{{ $asset->serial_number }}" placeholder="Enter Serial Number">
                                <span id="serialNumberError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="supplier">Supplier <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="supplier" id="supplier" value="{{ $asset->supplier }}" placeholder="Enter Supplier">
                                <span id="supplierError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="condition">Condition <span class="text-danger">*</span></label>
                                <select class="form-control" name="condition" id="condition">
                                    <option value="">Select Condition</option>
                                    <option value="New" {{ $asset->condition == 'New' ? 'selected' : '' }}>New</option>
                                    <option value="Excellent" {{ $asset->condition == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                    <option value="Good" {{ $asset->condition == 'Good' ? 'selected' : '' }}>Good</option>
                                    <option value="Fair" {{ $asset->condition == 'Fair' ? 'selected' : '' }}>Fair</option>
                                    <option value="Poor" {{ $asset->condition == 'Poor' ? 'selected' : '' }}>Poor</option>
                                </select>
                                <span id="conditionError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="warranty">Warranty (in months) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="warranty" id="warranty" value="{{ $asset->warranty }}" placeholder="Enter Warranty Period" min="0">
                                <span id="warrantyError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="location">Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="location" id="location" value="{{ $asset->location }}" placeholder="Enter Location">
                                <span id="locationError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="asset_image">Asset Image</label>
                                @if($asset->asset_image)
                                    <div class="mb-2">
                                        <img src="{{ asset($asset->asset_image) }}" alt="Asset Image" style="max-width: 100px; max-height: 100px;" class="img-thumbnail">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="remove_asset_image" id="remove_asset_image" value="1">
                                            <label class="form-check-label" for="remove_asset_image">
                                                Remove current image
                                            </label>
                                        </div>
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="asset_image" id="asset_image" accept="image/*">
                                <span id="assetImageError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
                                <label for="description">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="description" id="description" rows="4" placeholder="Enter Description" style="resize: none">{{ $asset->description }}</textarea>
                                <span id="descriptionError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row mt-4 text-center">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <button class="btn btn-primary" type="submit">Update</button>
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
            // Validate fields on input change
            $('#asset_name, #quantity, #purchase_date, #purchase_from, #manufacturer, #model, #serial_number, #supplier, #condition, #warranty, #description, #location').on('input change', function() {
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

                // Validate Quantity
                const assignedCount = {{ $asset->quantity - $asset->available_quantity }};
                const newQuantity = parseInt($('#quantity').val());
                if ($('#quantity').val() === '' || newQuantity < 1) {
                    $('#quantityError').text('Quantity must be at least 1.');
                    isValid = false;
                } else if (newQuantity < assignedCount) {
                    $('#quantityError').text('Quantity cannot be less than currently assigned items (' + assignedCount + ').');
                    isValid = false;
                } else {
                    $('#quantityError').text('');
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

                return isValid;
            }

            // Form Submission
            $('#editAssetForm').on('submit', function(e) {
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