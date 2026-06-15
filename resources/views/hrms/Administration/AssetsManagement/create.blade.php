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
                    <li class="breadcrumb-item active"><a href="{{ route('assets.create') }}">Add Asset</a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Form Start -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card shadow-sm">
                <h4 class="card-header">Add New Asset</h4>
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
                                <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="quantity" placeholder="Enter Quantity" min="1" value="{{ old('quantity', 1) }}">
                                <span id="quantityError" class="text-danger"></span>
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
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="warranty">Warranty (in months) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="warranty" id="warranty" placeholder="Enter Warranty Period" min="0" value="{{ old('warranty') }}">
                                <span id="warrantyError" class="text-danger"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="location">Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="location" id="location" placeholder="Enter Location" value="{{ old('location') }}">
                                <span id="locationError" class="text-danger"></span>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="asset_image">Asset Image</label>
                                <input type="file" class="form-control" name="asset_image" id="asset_image" accept="image/*">
                                <span id="assetImageError" class="text-danger"></span>
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
        $(document).ready(function () {

const fields = '#asset_name, #quantity, #purchase_date, #purchase_from, #manufacturer, #model, #serial_number, #supplier, #condition, #warranty, #description, #location';

// Validate individual field on input/change
$(fields).on('input change', function () {
    validateField($(this));
});


function validateField(field) {

    let id = field.attr('id');
    let value = field.val().trim();

    switch (id) {

        case 'asset_name':
            if (value === '') {
                $('#assetNameError').text('Asset Name is required.');
            } else {
                $('#assetNameError').text('');
            }
            break;


        case 'quantity':
            if (value === '' || parseInt(value) < 1) {
                $('#quantityError').text('Quantity must be at least 1.');
            } else {
                $('#quantityError').text('');
            }
            break;


        case 'purchase_date':
            if (value === '') {
                $('#purchaseDateError').text('Purchase Date is required.');
            } else {
                $('#purchaseDateError').text('');
            }
            break;


        case 'purchase_from':
            if (value === '') {
                $('#purchaseFromError').text('Purchase From is required.');
            } else {
                $('#purchaseFromError').text('');
            }
            break;


        case 'manufacturer':
            if (value === '') {
                $('#manufacturerError').text('Manufacturer is required.');
            } else {
                $('#manufacturerError').text('');
            }
            break;


        case 'model':
            if (value === '') {
                $('#modelError').text('Model is required.');
            } else {
                $('#modelError').text('');
            }
            break;


        case 'serial_number':
            if (value === '') {
                $('#serialNumberError').text('Serial Number is required.');
            } else {
                $('#serialNumberError').text('');
            }
            break;


        case 'supplier':
            if (value === '') {
                $('#supplierError').text('Supplier is required.');
            } else {
                $('#supplierError').text('');
            }
            break;


        case 'condition':
            if (value === '') {
                $('#conditionError').text('Condition is required.');
            } else {
                $('#conditionError').text('');
            }
            break;


        case 'warranty':
            if (value === '' || parseInt(value) < 0) {
                $('#warrantyError').text('Warranty must be a positive number.');
            } else {
                $('#warrantyError').text('');
            }
            break;


        case 'location':
            if (value === '') {
                $('#locationError').text('Location is required.');
            } else {
                $('#locationError').text('');
            }
            break;


        case 'description':
            if (value.length < 10) {
                $('#descriptionError').text('Description must be at least 10 characters.');
            }
            else if (value.length > 255) {
                $('#descriptionError').text('Description must not exceed 255 characters.');
            }
            else {
                $('#descriptionError').text('');
            }
            break;

    }

}



function validateFields() {

    let isValid = true;

    $(fields).each(function () {

        validateField($(this));

        if ($(this).next('span').text() !== '') {
            isValid = false;
        }

    });

    return isValid;
}



// Form submit validation
$('#addAssetForm').on('submit', function (e) {

    if (!validateFields()) {

        e.preventDefault();
        alert('Please fix the validation errors before submitting.');
        return false;

    }

    const submitButton = $(this).find('button[type="submit"]');

    submitButton.prop('disabled', true);
    submitButton.html('<i class="fas fa-spinner fa-spin"></i> Processing...');

});

});
    </script>

</div>
@endsection