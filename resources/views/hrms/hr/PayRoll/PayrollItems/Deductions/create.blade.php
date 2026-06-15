@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Add Deduction</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}" class="breadcrumb-link">Deduction List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Deduction</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Add Deduction</h4>
                    <div class="card-body">
                        <form action="{{ route('deductions.store') }}" method="POST" id="deduction-form">
                            @csrf
                            <div class="row">
                                <!-- Name Input -->
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Name <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="name" required id="name" value="{{ old('name') }}">
                                    <small class="text-danger" id="name-error" style="display:none;"></small>
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Category (Dropdown) -->
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Category <span class="text-danger">*</span></label>
                                    <select class="select form-control" name="category" required id="category">
                                        <option value="">Select a category</option>
                                        <option value="Monthly remuneration" {{ old('category') == 'Monthly remuneration' ? 'selected' : '' }}>Monthly remuneration</option>
                                        <option value="Additional remuneration" {{ old('category') == 'Additional remuneration' ? 'selected' : '' }}>Additional remuneration</option>
                                    </select>
                                    <small class="text-danger" id="category-error" style="display:none;"></small>
                                    @error('category')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Unit Calculation -->
                                <div class="col-md-6 mb-3">
                                    <label class="d-block col-form-label">Unit calculation</label>
                                    <div class="status-toggle">
                                        <input type="checkbox" id="unit_calculation" name="unit_calculation" class="check" value="1" {{ old('unit_calculation') ? 'checked' : '' }}>
                                        <label for="unit_calculation" class="checktoggle">checkbox</label>
                                    </div>
                                </div>

                                <!-- Unit Amount -->
                                <div class="col-md-6 mb-3" id="unit_amount_div" style="display: {{ old('unit_calculation') ? 'block' : 'none' }};">
                                    <label class="col-form-label">Unit Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="text" class="form-control" name="unit_amount" id="unit_amount" value="{{ old('unit_amount') }}">
                                        <span class="input-group-text">.00</span>
                                    </div>
                                    <small class="text-danger" id="unit_amount-error" style="display:none;"></small>
                                    @error('unit_amount')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Assignee -->
                                <div class="col-md-12 mb-3">
                                    <label class="d-block col-form-label">Assignee</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="addition_assignee" id="addition_no_emp" value="no_assignee" {{ old('addition_assignee', 'no_assignee') == 'no_assignee' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="addition_no_emp">No assignee</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="addition_assignee" id="addition_all_emp" value="all_employees" {{ old('addition_assignee') == 'all_employees' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="addition_all_emp">All employees</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="addition_assignee" id="addition_single_emp" value="single_employee" {{ old('addition_assignee') == 'single_employee' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="addition_single_emp">Select Employee</label>
                                    </div>
                                    @error('addition_assignee')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Employee Select Dropdown -->
                                <div class="col-md-12 mb-3">
                                    <select class="select form-control" name="employee_id" id="employee_select" {{ old('addition_assignee') != 'single_employee' ? 'disabled' : '' }}>
                                        <option value="">Select Employee</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{$employee->firstname}} {{ $employee->lastname }} (ID: {{ $employee->employeeid }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger" id="employee_id-error" style="display:none;"></small>
                                    @error('employee_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <div class="col-md-12 mt-4">
                                    <button class="btn btn-primary btn-lg btn-block" type="submit" id="submit-btn">
                                        <i class="fas fa-save mr-2"></i> Save Deduction
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
    document.addEventListener('DOMContentLoaded', function() {
        // Unit Calculation Toggle
        const unitCalculation = document.getElementById('unit_calculation');
        const unitAmountDiv = document.getElementById('unit_amount_div');
        
        if (unitCalculation && unitAmountDiv) {
            unitCalculation.addEventListener('change', function() {
                if (this.checked) {
                    unitAmountDiv.style.display = 'block';
                } else {
                    unitAmountDiv.style.display = 'none';
                }
            });
        }

        // Employee Select Enable/Disable
        const additionSingleEmp = document.getElementById('addition_single_emp');
        const additionNoEmp = document.getElementById('addition_no_emp');
        const additionAllEmp = document.getElementById('addition_all_emp');
        const employeeSelect = document.getElementById('employee_select');

        if (additionSingleEmp && additionNoEmp && additionAllEmp && employeeSelect) {
            // Event listeners
            additionSingleEmp.addEventListener('click', function() {
                employeeSelect.disabled = false;
            });

            additionNoEmp.addEventListener('click', function() {
                employeeSelect.disabled = true;
                employeeSelect.value = '';
            });

            additionAllEmp.addEventListener('click', function() {
                employeeSelect.disabled = true;
                employeeSelect.value = '';
            });
        }

        // Form validation on submit
        const form = document.getElementById('deduction-form');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Clear previous errors
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                document.querySelectorAll('.text-danger[id$="-error"]').forEach(el => el.style.display = 'none');
                
                // Validate name
                const nameInput = document.getElementById('name');
                if (!nameInput.value.trim()) {
                    isValid = false;
                    nameInput.classList.add('is-invalid');
                    document.getElementById('name-error').textContent = 'Name is required.';
                    document.getElementById('name-error').style.display = 'block';
                }
                
                // Validate category
                const categorySelect = document.getElementById('category');
                if (!categorySelect.value) {
                    isValid = false;
                    categorySelect.classList.add('is-invalid');
                    document.getElementById('category-error').textContent = 'Category is required.';
                    document.getElementById('category-error').style.display = 'block';
                }
                
                // Validate employee selection if "single_employee" is selected
                if (additionSingleEmp && additionSingleEmp.checked) {
                    if (!employeeSelect.value) {
                        isValid = false;
                        employeeSelect.classList.add('is-invalid');
                        document.getElementById('employee_id-error').textContent = 'Employee selection is required.';
                        document.getElementById('employee_id-error').style.display = 'block';
                    }
                }
                
                // Validate unit amount if unit calculation is checked
                if (unitCalculation && unitCalculation.checked) {
                    const unitAmountInput = document.getElementById('unit_amount');
                    const unitAmountValue = unitAmountInput ? unitAmountInput.value.trim() : '';
                    if (!unitAmountValue || isNaN(unitAmountValue)) {
                        isValid = false;
                        if (unitAmountInput) {
                            unitAmountInput.classList.add('is-invalid');
                            document.getElementById('unit_amount-error').textContent = 'Unit amount is required and must be a valid number.';
                            document.getElementById('unit_amount-error').style.display = 'block';
                        }
                    }
                }
                
                if (!isValid) {
                    e.preventDefault();
                    return false;
                }
                
                // Disable submit button to prevent double submission
                const submitBtn = document.getElementById('submit-btn');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
                }
                
                return true;
            });
        }
    });
</script>

<style>
    .is-invalid {
        border-color: #dc3545 !important;
    }
    .btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
</style>

@endsection