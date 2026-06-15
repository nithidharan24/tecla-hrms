@extends('layouts.index')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Promotion</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('promotion.index') }}">List</a></li>
                <li class="breadcrumb-item">Add</li>
            </ul>
        </div>
    </div>
</div>
<div class="container mt-4">
    <div class="card border-0" style="box-shadow: none;">
        <div class="card-header bg-transparent border-0">
            <h3 class="card-title mb-0">Add Promotion</h3>
        </div>
        <div class="card-body">
            <form id="promotionForm" action="{{ route('promotion.store') }}" method="POST">
                @csrf
                
                <!-- Employee Selection -->
                <div class="mb-3">
                    <label class="form-label">Employee <span class="text-danger">*</span></label>
                    <select class="form-select select2" id="employee_select" name="employee_id" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->employeeid }}" 
                                @if($selectedEmployee && $emp->employeeid == $selectedEmployee->employeeid) selected @endif
                                data-designation="{{ $emp->designation_name }}"
                                data-department="{{ $emp->department_name }}">
                                {{ $emp->firstname }} {{ $emp->lastname }} ({{ $emp->employeeid }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Auto-filled Details -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Current Department</label>
                        <input type="text" class="form-control" id="department_display" readonly>
                        <input type="hidden" name="department_id" id="department_id">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Current Designation</label>
                        <input type="text" class="form-control" id="current_designation" name="current_designation" readonly>
                        <input type="hidden" id="current_designation_id" name="current_designation_id">
                    </div>
                </div>

                <!-- Promotion Details -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Promotion To <span class="text-danger">*</span></label>
                        <select class="form-select" id="promotion_to" name="promotion_to" required>
                            <option value="">Select New Designation</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Promotion Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="promotion_date" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Submit Promotion</button>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize select2
    $('.select2').select2();
    
    // If employee is pre-selected, load their details
    @if($selectedEmployee)
        loadEmployeeDetails('{{ $selectedEmployee->employeeid }}');
    @endif
    
    // Handle employee selection change
    $('#employee_select').change(function() {
        const employeeId = $(this).val();
        if (employeeId) {
            loadEmployeeDetails(employeeId);
        } else {
            // Clear fields if no employee selected
            resetFormFields();
        }
    });
    
    function resetFormFields() {
        $('#current_designation').val('');
        $('#current_designation_id').val('');
        $('#department_display').val('');
        $('#department_id').val('');
        $('#promotion_to').html('<option value="">Select New Designation</option>');
    }
    
    function loadEmployeeDetails(employeeId) {
        $.ajax({
            url: "{{ route('promotion.getEmployeeDetails') }}",
            type: "POST",
            data: {
                employee_id: employeeId,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    // Set current details
                    $('#current_designation').val(response.current_designation);
                    $('#current_designation_id').val(response.current_designation_id);
                    $('#department_display').val(response.department_name);
                    $('#department_id').val(response.department_id);
                    
                    // Load available designations for promotion
                    loadDesignations(response.department_id, response.current_designation_id);
                } else {
                    resetFormFields();
                    showToast('error', 'Employee details not found');
                }
            },
            error: function() {
                resetFormFields();
                showToast('error', 'Error loading employee details');
            }
        });
    }
    
    function loadDesignations(departmentId, currentDesignationId) {
    $('#promotion_to').html('<option value="">Loading...</option>');

    $.ajax({
        url: "{{ route('get-designations-by-department', ':department_id') }}"
                .replace(':department_id', departmentId),
        type: 'GET',
        success: function(designations) {
            if (designations.length > 0) {
                let options = '<option value="">Select New Designation</option>';

                $.each(designations, function(index, designation) {
                    if (designation.id != currentDesignationId) {
                        // ✅ fixed string template
                        options += `<option value="${designation.id}">${designation.designation}</option>`;
                    }
                });

                $('#promotion_to').html(options);
            } else {
                $('#promotion_to').html('<option value="">No designations available</option>');
                showToast('warning', 'No other designations available in this department');
            }
        },
        error: function(xhr) {
            $('#promotion_to').html('<option value="">Error loading designations</option>');
            showToast('error', 'Error loading designations');
            console.error(xhr.responseText);
        }
    });
}


    
function showToast(type, message) {
    console.log(`${type}: ${message}`);
    // If you use toastr uncomment below:
    // toastr[type](message);
}

});
</script>
@endsection