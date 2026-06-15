@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Deduction</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}" class="breadcrumb-link">Deduction List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Deduction</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Edit Deduction</h4>
                    <div class="card-body">
                        <form action="{{ route('deductions.update', $deductions->id) }}" method="POST" id="edit-addition-form">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <!-- Name Input -->
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Name <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="name" value="{{ $deductions->name }}" required id="edit-name">
                                    <small class="text-danger" id="edit-name-error" style="display:none;"></small>
                                </div>

                                <!-- Category (Dropdown) -->
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Category <span class="text-danger">*</span></label>
                                    <select class="select form-control" name="category" required id="edit-category">
                                        <option value="">Select a category</option>
                                        <option value="Monthly remuneration" {{ $deductions->category == 'Monthly remuneration' ? 'selected' : '' }}>Monthly remuneration</option>
                                        <option value="Additional remuneration" {{ $deductions->category == 'Additional remuneration' ? 'selected' : '' }}>Additional remuneration</option>
                                    </select>
                                    <small class="text-danger" id="edit-category-error" style="display:none;"></small>
                                </div>

                                <!-- Unit Calculation -->
                                <div class="col-md-6 mb-3">
                                    <label class="d-block col-form-label">Unit calculation</label>
                                    <div class="status-toggle">
                                        <input type="checkbox" id="edit_unit_calculation" name="unit_calculation" class="check" {{ $deductions->unit_amount ? 'checked' : '' }}>
                                        <label for="edit_unit_calculation" class="checktoggle">checkbox</label>
                                    </div>
                                </div>

                                <!-- Unit Amount -->
                                <div class="col-md-6 mb-3" id="edit-unit-amount-div" style="{{ $deductions->unit_amount ? 'display: block;' : 'display: none;' }}">
                                    <label class="col-form-label">Unit Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" class="form-control" name="unit_amount" id="edit-unit-amount" value="{{ $deductions->unit_amount }}">
                                        <span class="input-group-text">.00</span>
                                    </div>
                                    <small class="text-danger" id="edit-unit-amount-error" style="display:none;"></small>
                                </div>

                                <!-- Employee Selection -->
                                <div class="col-md-12 mb-3">
                                    <label class="d-block col-form-label">Assign to Employee</label>
                                    <div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" name="employee_assignment" id="edit_addition_no_emp" value="no_assignee" {{ $deductions->employee_id ? '' : 'checked' }}>
    <label class="form-check-label" for="edit_addition_no_emp">No assignee</label>
</div>
<div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" name="employee_assignment" id="edit_addition_all_emp" value="all_employees" {{ $deductions->employee_id ? '' : 'checked' }}>
    <label class="form-check-label" for="edit_addition_all_emp">All employees</label>
</div>
<div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" name="employee_assignment" id="edit_addition_single_emp" value="single_employee" {{ $deductions->employee_id ? 'checked' : '' }}>
    <label class="form-check-label" for="edit_addition_single_emp">Select Employee</label>
</div>

                                </div>

                                <!-- Employee Select Dropdown -->
                                <div class="col-md-12 mb-3">
                                    <select class="select form-control" name="employee_id" id="edit-employee-select" {{ $deductions->employee_id ? '' : 'disabled' }}>
                                        <option value="">Select Employee</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ $deductions->employee_id == $employee->id ? 'selected' : '' }}>
                                                {{$employee->firstname}} {{ $employee->lastname }} (ID: {{ $employee->employeeid }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger" id="edit-employee-id-error" style="display:none;"></small>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-md-12 mt-4">
                                    <button class="btn btn-primary btn-lg btn-block" type="submit">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enable/Disable Employee Select and Unit Amount Script -->
<script>
    // Enable/Disable employee select based on radio button choice
    document.getElementById('edit_addition_single_emp').addEventListener('click', function() {
        document.getElementById('edit-employee-select').disabled = false;
    });

    document.getElementById('edit_addition_no_emp').addEventListener('click', function() {
        document.getElementById('edit-employee-select').disabled = true;
    });

    document.getElementById('edit_addition_all_emp').addEventListener('click', function() {
        document.getElementById('edit-employee-select').disabled = true;
    });

    // Show/Hide Unit Amount field based on Unit Calculation checkbox
    document.getElementById('edit_unit_calculation').addEventListener('change', function() {
        var unitAmountDiv = document.getElementById('edit-unit-amount-div');
        unitAmountDiv.style.display = this.checked ? 'block' : 'none'; // Simplified toggle logic
    });

    // Real-time validation
    document.getElementById('edit-name').addEventListener('input', function() {
        const error = document.getElementById('edit-name-error');
        error.style.display = this.value.trim() === '' ? 'block' : 'none';
        error.textContent = this.value.trim() === '' ? 'Name is required.' : '';
    });

    document.getElementById('edit-category').addEventListener('change', function() {
        const error = document.getElementById('edit-category-error');
        error.style.display = this.value === '' ? 'block' : 'none';
        error.textContent = this.value === '' ? 'Category is required.' : '';
    });

    document.getElementById('edit-unit-amount').addEventListener('input', function() {
        const error = document.getElementById('edit-unit-amount-error');
        error.style.display = (this.value.trim() !== '' && isNaN(this.value)) ? 'block' : 'none';
        error.textContent = (this.value.trim() !== '' && isNaN(this.value)) ? 'Please enter a valid number.' : '';
    });

    document.getElementById('edit-employee-select').addEventListener('change', function() {
        const error = document.getElementById('edit-employee-id-error');
        error.style.display = (this.value === '' && document.getElementById('edit_addition_single_emp').checked) ? 'block' : 'none';
        error.textContent = (this.value === '' && document.getElementById('edit_addition_single_emp').checked) ? 'Employee selection is required when "Select Employee" is chosen.' : '';
    });
    
</script>
@endsection
