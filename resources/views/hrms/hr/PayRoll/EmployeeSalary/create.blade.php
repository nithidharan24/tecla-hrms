@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Add Staff Salary</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('salary.index') }}" class="breadcrumb-link">Salary List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Staff Salary</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Add Salary</h4>
                    <div class="card-body">
                        <form action="{{ route('salary.store') }}" method="POST" id="salaryForm">
                            @csrf
                            <div class="row">
                                <!-- Select Staff -->
                                <div class="col-sm-6 mb-3">
                                    <label class="col-form-label">Select Staff</label>
                                    <select class="select form-control" name="employee_id" id="employee_id">
                                        <option value="">Select Employee</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}">
                                                {{ $employee->employeeid }} - {{ $employee->firstname }} {{ $employee->lastname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Net Salary -->
                                <div class="col-sm-6 mb-3">
                                    <label class="col-form-label">Net Salary</label>
                                    <input class="form-control" type="text" name="net_salary" id="net_salary" readonly>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <h4 class="text-primary">Earnings</h4>

                                    <!-- Basic Salary -->
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">Basic</label>
                                        <input class="form-control" type="text" name="basic" id="basic" oninput="calculateNetSalary()">
                                    </div>

                                    <!-- DA -->
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">DA %</label>
                                        <input class="form-control" type="number" name="da" id="da" value="0" oninput="calculateNetSalary()">
                                    </div>

                                    <!-- HRA -->
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">HRA %</label>
                                        <input class="form-control" type="number" name="hra" id="hra" value="0" oninput="calculateNetSalary()">
                                    </div>

                                    <!-- Other Earnings -->
                                    @foreach(['conveyance' => 'Conveyance', 'allowance' => 'Allowance', 'medical' => 'Medical Allowance'] as $field => $label)
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">{{ $label }}</label>
                                            <input class="form-control" type="text" name="{{ $field }}" id="{{ $field }}" oninput="calculateNetSalary()" value="0">
                                        </div>
                                    @endforeach

                                    <div id="additions-section"></div>
                                </div>

                                <div class="col-sm-6">
                                    <h4 class="text-primary">Deductions</h4>

                                    <!-- PF -->
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">PF %</label>
                                        <input class="form-control" type="number" name="pf" id="pf" value="0" oninput="calculateNetSalary()">
                                        <input type="hidden" name="pf_amount" id="pf_amount">
                                    </div>

                                    <!-- ESI -->
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">ESI %</label>
                                        <input class="form-control" type="number" name="esi" id="esi" value="0" oninput="calculateNetSalary()">
                                        <input type="hidden" name="esi_amount" id="esi_amount">
                                    </div>
                                    <!-- Other Deductions -->
                                    @foreach(['tds' => 'TDS',  'tax' => 'Prof. Tax', 'welfare' => 'Welfare'] as $field => $label)
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">{{ $label }}</label>
                                            <input class="form-control" type="text" name="{{ $field }}" id="{{ $field }}" oninput="calculateNetSalary()" value="0">
                                        </div>
                                    @endforeach

                                    <div id="deductions-section"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12 mt-4">
                                    <button class="btn btn-primary btn-lg btn-block" type="submit" id="submitBtn">
                                        <span id="submitText">Submit</span>
                                        <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
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

<style>
    .btn-primary:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Employee change handler
        $('#employee_id').change(function() {
            fetchEmployeeDetails();
        });

        // Basic salary change handler
        $('#basic').on('input', function() {
            calculateNetSalary();
        });
    });

    function calculateNetSalary() {
    const basicSalary = parseFloat($('#basic').val()) || 0;

    // Get percentages from input fields
    const daPercentage = parseFloat($('#da').val()) || 0;
    const hraPercentage = parseFloat($('#hra').val()) || 0;
    const pfPercentage = parseFloat($('#pf').val()) || 0;
    const esiPercentage = parseFloat($('#esi').val()) || 0;

    // Earnings
    const daAmount = (daPercentage / 100) * basicSalary;
    const hraAmount = (hraPercentage / 100) * basicSalary;

    const conveyance = parseFloat($('#conveyance').val()) || 0;
    const allowance = parseFloat($('#allowance').val()) || 0;
    const medical = parseFloat($('#medical').val()) || 0;

    let additionalEarnings = 0;
    $('#additions-section input').each(function () {
        additionalEarnings += parseFloat($(this).val()) || 0;
    });

    const totalEarnings = basicSalary + daAmount + hraAmount + conveyance + allowance + medical + additionalEarnings;

    // Deductions
    const pfAmount = (pfPercentage / 100) * basicSalary;
    const esiAmount = (esiPercentage / 100) * basicSalary;
    $('#pf_amount').val(pfAmount.toFixed(2));
    $('#esi_amount').val(esiAmount.toFixed(2));

    const tds = parseFloat($('#tds').val()) || 0;
    const leave = parseFloat($('#employee_leave').val()) || 0;
    const tax = parseFloat($('#tax').val()) || 0;
    const welfare = parseFloat($('#welfare').val()) || 0;

    let additionalDeductions = 0;
    $('#deductions-section input').each(function () {
        additionalDeductions += parseFloat($(this).val()) || 0;
    });

    const totalDeductions = pfAmount + esiAmount + tds + leave + tax + welfare + additionalDeductions;

    // Net salary
    const netSalary = totalEarnings - totalDeductions;
    $('#net_salary').val(netSalary.toFixed(2));
}


    function fetchEmployeeDetails() {
        const employeeId = $('#employee_id').val();
        if (employeeId) {
            $.ajax({
                url: '/get-additions-deductions',
                method: 'GET',
                data: { employee_id: employeeId },
                success: function(response) {
                    $('#additions-section').empty();
                    $('#deductions-section').empty();

                    // Process additions
                    if (response.additions && response.additions.length > 0) {
                        response.additions.forEach(function(addition) {
                            $('#additions-section').append(`
                                <div class="input-block mb-3">
                                    <label>${addition.name}</label>
                                    <input type="number" name="additions[${addition.name}]" class="form-control" 
                                        value="${addition.unit_amount}" oninput="calculateNetSalary()">
                                </div>
                            `);
                        });
                    }

                    // Process deductions
                    if (response.deductions && response.deductions.length > 0) {
                        response.deductions.forEach(function(deduction) {
                            $('#deductions-section').append(`
                                <div class="input-block mb-3">
                                    <label>${deduction.name}</label>
                                    <input type="number" name="deductions[${deduction.name}]" class="form-control" 
                                        value="${deduction.unit_amount}" oninput="calculateNetSalary()">
                                </div>
                            `);
                        });
                    }

                    // Recalculate net salary
                    calculateNetSalary();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching employee details:', textStatus, errorThrown);
                }
            });
        } else {
            $('#additions-section').empty();
            $('#deductions-section').empty();
        }
    }
    
    $(document).ready(function() {
        $('#salaryForm').on('submit', function() {
            // Prevent double submission
            if ($(this).data('submitted') === true) {
                return false;
            }
            
            $(this).data('submitted', true);
            $('#submitBtn').prop('disabled', true);
            $('#submitText').text('Processing...');
            $('#submitSpinner').removeClass('d-none');
            
            return true; // Allow form to submit normally
        });
    });
</script>
@endsection