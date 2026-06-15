@extends('layouts.index')

@section('content')
<div class="content container-fluid">

    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Travel Initiate</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('expense.index')}}">Expenses</a></li>
                    <li class="breadcrumb-item active">Initiate Travel</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Travel Initiate Details</h5>
                </div>
                <div class="card-body">
                    <form id="expense-form" method="POST" action="{{route('expense.store')}}" enctype="multipart/form-data" novalidate>
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label" for="employee_id">Employee ID <span class="text-danger">*</span></label>
                                    <select class="form-select select" id="employee_id" name="employee_id" required>
                                        <option value="">Select Employee</option>
                                        {{-- Populate employee data from controller --}}
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->employee_code }}" {{ old('employee_id') == $employee->employee_code ? 'selected' : '' }}>
                                                {{ $employee->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label" for="place_of_visit">Place of visit</label>
                                    <input class="form-control" id="place_of_visit" name="place_of_visit" type="text" value="{{ old('place_of_visit') }}">
                                </div>
                            </div>
                        </div>

                         <div class="row">
                            <div class="col-md-6">
                                <div class="input-block mb-3 d-flex">
                                    <div class="flex-grow-1">
                                        <label class="col-form-label" for="department_name">Employee Department</label>
                                        {{-- The department will be auto-filled by JS based on employee_id selection --}}
                                        <select class="form-select select" id="department_name" name="department_name">
                                            <option value="">Select</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept }}" {{ old('department_name') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                 
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label" for="arrival_date">Expected date of arrival</label>
                                    <input class="form-control datetimepicker" id="arrival_date" name="arrival_date" type="text" placeholder="dd-MMM-yyyy" value="{{ old('arrival_date') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label" for="departure_date">Expected date of departure <span class="text-danger">*</span></label>
                                    <input class="form-control datetimepicker" id="departure_date" name="departure_date" type="text" placeholder="dd-MMM-yyyy" value="{{ old('departure_date') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label" for="duration_days">Expected duration in days</label>
                                    <input class="form-control" id="duration_days" name="duration_days" type="number" min="1" value="{{ old('duration_days') }}">
                                </div>
                            </div>
                        </div>
                        
                         <div class="row">
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label" for="purpose_of_visit">Purpose of visit <span class="text-danger">*</span></label>
                                    <input class="form-control" id="purpose_of_visit" name="purpose_of_visit" type="text" value="{{ old('purpose_of_visit') }}" required>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label" for="customer_name">Customer name</label>
                                    <input class="form-control" id="customer_name" name="customer_name" type="text" value="{{ old('customer_name') }}">
                                </div>
                            </div>
                        </div>

                         <div class="row">
                            <div class="col-md-6">
                                <div class="input-block mb-3 d-flex">
                                     <div class="flex-grow-1">
                                        <label class="col-form-label" for="is_billable">Is billable to customer</label>
                                        <select class="form-select select" id="is_billable" name="is_billable" required>
                                            <option value="" {{ old('is_billable') === null ? 'selected' : '' }}>Select</option>
                                            <option value="1" {{ old('is_billable') == '1' ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ old('is_billable') == '0' ? 'selected' : '' }}>No</option>
                                        </select>
                                     </div>
                                   
                                </div>
                            </div>
                        </div>
                         
                         <hr>
                         <h5 class="card-title mb-3">Expense Details & Receipts</h5>

                         <div class="row">
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label" for="expense_amount">Total Estimated Amount <span class="text-danger">*</span></label>
                                    <input class="form-control" id="expense_amount" name="expense_amount" type="number" step="0.01" min="0" value="{{ old('expense_amount') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label" for="currency">Currency <span class="text-danger">*</span></label>
                                    <select class="form-select select" id="currency" name="currency" required>
                                        <option value="INR" {{ old('currency') == 'INR' ? 'selected' : '' }}>INR - Indian Rupee</option>
                                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                        <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-block mb-3">
                                    <label class="col-form-label" for="upload_receipt">Upload Initial Receipt/Document <span class="text-danger">*</span></label>
                                    <input class="form-control" id="upload_receipt" name="upload_receipt" type="file" accept=".png, .jpg, .jpeg, .gif, .pdf" required>
                                    <small class="text-muted">Supported formats: PNG, JPG, JPEG, GIF, PDF (Max: 2MB)</small>
                                    <div class="image-preview mt-2">
                                        <img id="imagePreview" src="#" alt="Receipt Preview" style="display:none; max-width: 300px; max-height: 200px;" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="submit-section">
                            <button type="submit" class="btn btn-primary submit-btn">Submit Travel Initiate</button>
                        </div>
                    </form>                       
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    $(document).ready(function() {
        // Initialize select2 buttons
        if($('.select').length > 0) {
            $('.select').select2({
                minimumResultsForSearch: -1,
                width: '100%'
            });
        }
        
        // Initialize Datetimepicker
        if($('.datetimepicker').length > 0) {
            $('.datetimepicker').datetimepicker({
                format: 'DD-MMM-YYYY',
                icons: {
                    up: "fa fa-angle-up",
                    down: "fa fa-angle-down",
                    next: 'fa fa-angle-right',
                    previous: 'fa fa-angle-left'
                }
            });
        }

        // --- START: Employee ID to Department Auto-fill Logic ---
        // Create a map from the PHP data for quick lookup
        const employeeData = @json($employees);
        const employeeMap = {};
        employeeData.forEach(emp => {
            employeeMap[emp.employee_code] = emp.department_name;
        });

        $('#employee_id').on('change', function() {
            const selectedEmployeeCode = $(this).val();
            
            // Lookup department name using the selected employee ID
            const departmentName = employeeMap[selectedEmployeeCode] || '';
            
            // Set the value of the department select box and trigger change for Select2
            $('#department_name').val(departmentName).trigger('change');
        }).trigger('change'); // Trigger on load if old data is present (for validation errors)

        // --- END: Employee ID to Department Auto-fill Logic ---


        // Validation and Image Preview Scripts (adjust as per your needs)
        
        // Add custom validation methods
        $.validator.addMethod("filesize", function(value, element, param) {
            if (element.files.length > 0) {
                const fileSize = element.files[0].size;
                return this.optional(element) || (fileSize <= param);
            }
            return true;
        }, "File size must be less than {0} bytes");

        $.validator.addMethod("extension", function(value, element, param) {
            if (this.optional(element)) {
                return true;
            }
            const fileExtension = value.split('.').pop().toLowerCase();
            return $.inArray(fileExtension, param.split('|')) !== -1;
        }, "Please select a file with a valid extension.");


        $("#expense-form").validate({
            rules: {
                employee_id: "required",
                departure_date: "required",
                purpose_of_visit: "required",
                expense_amount: { required: true, number: true, min: 0 },
                currency: "required",
                upload_receipt: { 
                    required: true, 
                    extension: "png|jpg|jpeg|gif|pdf", 
                    filesize: 2 * 1024 * 1024 // 2MB 
                },
                arrival_date: {
                    date: true,
                    // Additional check for after_or_equal:departure_date is handled in the controller
                }
            },
            messages: {
                employee_id: "Please select an employee",
                departure_date: "Please select expected date of departure",
                purpose_of_visit: "Please enter purpose of visit",
            },
            errorClass: "text-danger error",
            errorElement: "span",
            errorPlacement: function(error, element) {
                if (element.closest('.input-block').find('.select2-container').length) {
                     error.insertAfter(element.closest('.input-block').find('.select2-container'));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                $('.submit-btn').prop('disabled', true).html('Submitting...');
                form.submit();
            }
        });
        
        $('#upload_receipt').change(function() {
            const file = this.files[0];
            if (file) {
                const fileExtension = file.name.split('.').pop().toLowerCase();
                const allowedExtensions = ['png', 'jpg', 'jpeg', 'gif', 'pdf'];

                if ($.inArray(fileExtension, allowedExtensions) !== -1) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').attr('src', e.target.result).show();
                    }
                    reader.readAsDataURL(file);
                } else {
                    $('#imagePreview').hide();
                    Swal.fire({ title: "Invalid file format!", text: "Please upload an image in PNG, JPG, JPEG, PDF, or GIF format.", icon: "error" });
                }
            }
        });
    });

    @if ($errors->any())
        var errorMessage = '';
        @foreach ($errors->all() as $error)
            errorMessage += "{{ $error }}\n";
        @endforeach
        Swal.fire({ icon: 'error', title: 'Validation Errors', text: errorMessage, });
    @endif
</script>
@endsection