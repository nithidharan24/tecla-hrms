@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Add Addition</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}" class="breadcrumb-link">Addition List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Addition</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Add Addition</h4>
                    <div class="card-body">
                        <form action="{{ route('payroll.store') }}" method="POST" id="addition-form">
                            @csrf
                            <div class="row">
                                
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Name <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="name" required id="name">
                                    <small class="text-danger" id="name-error" style="display:none;"></small>
                                </div>

                                  
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Category <span class="text-danger">*</span></label>
                                    <select class="select form-control" name="category" required id="category">
                                        <option value="">Select a category</option>
                                        <option value="Monthly remuneration">Monthly remuneration</option>
                                        <option value="Additional remuneration">Additional remuneration</option>
                                    </select>
                                    <small class="text-danger" id="category-error" style="display:none;"></small>
                                </div>

                                <input type="hidden" name="unit_calculation" value="1">

                                <div class="col-md-6 mb-3" id="unit_amount_div">
                                    <label class="col-form-label">Unit Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="text" class="form-control" name="unit_amount" id="unit_amount">
                                        <span class="input-group-text">.00</span>
                                    </div>
                                    <small class="text-danger" id="unit_amount-error" style="display:none;"></small>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="d-block col-form-label">Assignee</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="addition_assignee" id="addition_no_emp" value="no_assignee" checked>
                                        <label class="form-check-label" for="addition_no_emp">No assignee</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="addition_assignee" id="addition_all_emp" value="all_employees">
                                        <label class="form-check-label" for="addition_all_emp">All employees</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="addition_assignee" id="addition_single_emp" value="single_employee">
                                        <label class="form-check-label" for="addition_single_emp">Select Employee</label>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <select class="select form-control" name="employee_id" id="employee_select">
                                        <option value="">Select Employee</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}">{{$employee->firstname}} {{ $employee->lastname }} (ID: {{ $employee->employeeid }})</option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger" id="employee_id-error" style="display:none;"></small>
                                </div>

                                <div class="col-md-12 mt-4">
                                    <button class="btn btn-primary btn-lg btn-block" type="submit" id="saveAdditionBtn">
                                        <span id="saveAdditionText">Save Addition</span>
                                        <span id="saveAdditionSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    </button>
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
    // <CHANGE> Removed all JS that showed/hidden fields or disabled/enabled elements.

    // Real-time validation
    document.getElementById('name').addEventListener('input', function() {
        const error = document.getElementById('name-error');
        if (this.value.trim() === '') {
            error.textContent = 'Name is required.';
            error.style.display = 'block';
        } else {
            error.style.display = 'none';
        }
    });

    document.getElementById('category').addEventListener('change', function() {
        const error = document.getElementById('category-error');
        if (this.value === '') {
            error.textContent = 'Category is required.';
            error.style.display = 'block';
        } else {
            error.style.display = 'none';
        }
    });

    document.getElementById('unit_amount').addEventListener('input', function() {
        const error = document.getElementById('unit_amount-error');
        if (this.value.trim() !== '' && isNaN(this.value)) {
            error.textContent = 'Please enter a valid number.';
            error.style.display = 'block';
        } else {
            error.style.display = 'none';
        }
    });

    document.getElementById('employee_select').addEventListener('change', function() {
        const error = document.getElementById('employee_id-error');
        // Still only required when "Select Employee" is chosen
        if (this.value === '' && document.getElementById('addition_single_emp').checked) {
            error.textContent = 'Employee selection is required when "Select Employee" is chosen.';
            error.style.display = 'block';
        } else {
            error.style.display = 'none';
        }
    });

    // Form submission handler
    document.getElementById('addition-form').addEventListener('submit', function(e) {
        let isValid = true;

        // Name validation
        if (document.getElementById('name').value.trim() === '') {
            document.getElementById('name-error').textContent = 'Name is required.';
            document.getElementById('name-error').style.display = 'block';
            isValid = false;
        }

        // Category validation
        if (document.getElementById('category').value === '') {
            document.getElementById('category-error').textContent = 'Category is required.';
            document.getElementById('category-error').style.display = 'block';
            isValid = false;
        }

        // Employee validation if single employee is selected
        if (document.getElementById('addition_single_emp').checked &&
            document.getElementById('employee_select').value === '') {
            document.getElementById('employee_id-error').textContent = 'Employee selection is required when "Select Employee" is chosen.';
            document.getElementById('employee_id-error').style.display = 'block';
            isValid = false;
        }

        // <CHANGE> Validate unit amount only if provided (field always visible)
        const unitAmount = document.getElementById('unit_amount').value.trim();
        if (unitAmount !== '' && isNaN(unitAmount)) {
            document.getElementById('unit_amount-error').textContent = 'Please enter a valid number.';
            document.getElementById('unit_amount-error').style.display = 'block';
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            return false;
        }

        // Submit button state
        const submitBtn = document.getElementById('saveAdditionBtn');
        if (submitBtn.getAttribute('data-submitting') === 'true') {
            e.preventDefault();
            return false;
        }
        submitBtn.setAttribute('data-submitting', 'true');
        submitBtn.disabled = true;
        document.getElementById('saveAdditionText').textContent = 'Saving...';
        document.getElementById('saveAdditionSpinner').classList.remove('d-none');

        return true;
    });

    // Reset button state if form validation fails (for modern browsers)
    document.getElementById('addition-form').addEventListener('invalid', function(e) {
        const submitBtn = document.getElementById('saveAdditionBtn');
        submitBtn.removeAttribute('data-submitting');
        submitBtn.disabled = false;
        document.getElementById('saveAdditionText').textContent = 'Save Addition';
        document.getElementById('saveAdditionSpinner').classList.add('d-none');
    }, true);
</script>
@endsection