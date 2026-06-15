@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Addition</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}" class="breadcrumb-link">Addition List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Addition</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Edit Addition</h4>
                    <div class="card-body">
                        <form action="{{ route('payroll.update', $addition->id) }}" method="POST" id="edit-addition-form">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Name <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="name" value="{{ $addition->name }}" required id="edit-name">
                                    <small class="text-danger" id="edit-name-error" style="display:none;"></small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Category <span class="text-danger">*</span></label>
                                    <select class="select form-control" name="category" required id="edit-category">
                                        <option value="">Select a category</option>
                                        <option value="Monthly remuneration" {{ $addition->category == 'Monthly remuneration' ? 'selected' : '' }}>Monthly remuneration</option>
                                        <option value="Additional remuneration" {{ $addition->category == 'Additional remuneration' ? 'selected' : '' }}>Additional remuneration</option>
                                    </select>
                                    <small class="text-danger" id="edit-category-error" style="display:none;"></small>
                                </div>

                                <input type="hidden" name="unit_calculation" value="1">

                                <div class="col-md-6 mb-3" id="edit-unit-amount-div">
                                    <label class="col-form-label">Unit Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="text" class="form-control" name="unit_amount" id="edit-unit-amount" value="{{ $addition->unit_amount }}">
                                        <span class="input-group-text">.00</span>
                                    </div>
                                    <small class="text-danger" id="edit-unit-amount-error" style="display:none;"></small>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="d-block col-form-label">Assign to Employee</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="employee_assignment" id="edit_addition_no_emp" value="no_assignee" {{ $addition->employee_id ? '' : 'checked' }}>
                                        <label class="form-check-label" for="edit_addition_no_emp">No assignee</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="employee_assignment" id="edit_addition_all_emp" value="all_employees" {{ $addition->employee_id ? '' : 'checked' }}>
                                        <label class="form-check-label" for="edit_addition_all_emp">All employees</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="employee_assignment" id="edit_addition_single_emp" value="single_employee" {{ $addition->employee_id ? 'checked' : '' }}>
                                        <label class="form-check-label" for="edit_addition_single_emp">Select Employee</label>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <select class="select form-control" name="employee_id" id="edit-employee-select">
                                        <option value="">Select Employee</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ $addition->employee_id == $employee->id ? 'selected' : '' }}>
                                                {{$employee->firstname}} {{ $employee->lastname }} (ID: {{ $employee->employeeid }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger" id="edit-employee-id-error" style="display:none;"></small>
                                </div>

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

<script>
    // Real-time validation
    document.getElementById('edit-name').addEventListener('input', function() {
        const error = document.getElementById('edit-name-error');
        if (this.value.trim() === '') {
            error.textContent = 'Name is required.';
            error.style.display = 'block';
        } else {
            error.style.display = 'none';
        }
    });

    document.getElementById('edit-category').addEventListener('change', function() {
        const error = document.getElementById('edit-category-error');
        if (this.value === '') {
            error.textContent = 'Category is required.';
            error.style.display = 'block';
        } else {
            error.style.display = 'none';
        }
    });

    document.getElementById('edit-unit-amount').addEventListener('input', function() {
        const error = document.getElementById('edit-unit-amount-error');
        if (this.value.trim() !== '' && isNaN(this.value)) {
            error.textContent = 'Please enter a valid number.';
            error.style.display = 'block';
        } else {
            error.style.display = 'none';
        }
    });

    // Keep the employee select always visible and enabled. Only validate if single employee chosen.
    function validateEmployeeIfNeeded() {
        const singleSelected = document.getElementById('edit_addition_single_emp').checked;
        const select = document.getElementById('edit-employee-select');
        const error = document.getElementById('edit-employee-id-error');

        if (singleSelected && select.value === '') {
            error.textContent = 'Employee selection is required when "Select Employee" is chosen.';
            error.style.display = 'block';
        } else {
            error.style.display = 'none';
        }
    }

    document.getElementById('edit_addition_no_emp').addEventListener('change', validateEmployeeIfNeeded);
    document.getElementById('edit_addition_all_emp').addEventListener('change', validateEmployeeIfNeeded);
    document.getElementById('edit_addition_single_emp').addEventListener('change', validateEmployeeIfNeeded);
    document.getElementById('edit-employee-select').addEventListener('change', validateEmployeeIfNeeded);
</script>
@endsection