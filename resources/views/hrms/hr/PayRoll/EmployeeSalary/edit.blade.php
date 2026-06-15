@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Staff Salary</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('payroll.combined', ['tab' => 'employee_salary']) }}" class="breadcrumb-link">Salary List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Staff Salary</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Edit Salary</h4>
                    <div class="card-body">
                        <form action="{{ route('salary.update', $salary->id) }}" method="POST" id="salaryForm">
                            @csrf
                            @method('PUT')
                            
                            <!-- Hidden fields for actual PF/ESI amounts -->
                            <input type="hidden" name="pf_amount" id="pf_amount" value="{{ $salary->pf }}">
                            <input type="hidden" name="esi_amount" id="esi_amount" value="{{ $salary->esi }}">
                            
                            <!-- Salary Master Config Values (loaded from backend) -->
                            <input type="hidden" id="gross_to_basic_percentage" value="{{ $salaryConfig->gross_to_basic_percentage ?? 50 }}">
                            <input type="hidden" id="master_da_percentage" value="{{ $salaryConfig->da_percentage ?? 0 }}">
                            <input type="hidden" id="master_hra_percentage" value="{{ $salaryConfig->hra_percentage ?? 0 }}">
                            <input type="hidden" id="master_conveyance" value="{{ $salaryConfig->conveyance ?? 0 }}">
                            <input type="hidden" id="master_special_allowance" value="{{ $salaryConfig->special_allowance ?? 0 }}">
                            <input type="hidden" id="master_medical_allowance" value="{{ $salaryConfig->medical_allowance ?? 0 }}">
                            <input type="hidden" id="master_pf_percentage" value="{{ $salaryConfig->pf_percentage ?? 0 }}">
                            <input type="hidden" id="master_esi_percentage" value="{{ $salaryConfig->esi_percentage ?? 0 }}">
                            <input type="hidden" id="master_professional_tax" value="{{ $salaryConfig->professional_tax ?? 0 }}">
                            <input type="hidden" id="master_welfare_fund" value="{{ $salaryConfig->welfare_fund ?? 0 }}">
                            <input type="hidden" id="master_tds" value="{{ $salaryConfig->tds ?? 0 }}">
                            
                            <div class="row">
                                <!-- Select Staff - READ ONLY -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Select Staff <span class="text-danger">*</span></label>
                                    <select class="form-control" name="employee_id_display" id="employee_id_display" readonly disabled style="background-color: #e9ecef; cursor: not-allowed;">
                                        <option value="">Select Employee</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ $employee->id == $salary->employee_id ? 'selected' : '' }}>
                                                {{ $employee->employeeid }} - {{ $employee->firstname }} {{ $employee->lastname }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="employee_id" value="{{ $salary->employee_id }}">
                                    <small class="text-muted">Employee cannot be changed after salary creation</small>
                                </div>

                                <!-- Current Net Salary Display -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Current Net Salary</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">₹</span>
                                        <input class="form-control bg-light" type="text" value="{{ number_format($salary->net_salary, 2) }}" readonly disabled>
                                    </div>
                                    <small class="text-muted">Will be updated after changes</small>
                                </div>
                            </div>

                            <!-- Gross Salary - EDITABLE (Fixed: removed readonly) -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Gross Salary Configuration</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold mb-0">Gross Salary <span class="text-danger">*</span></label>
                                                    <small class="text-muted d-block">Enter total gross salary</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <span class="input-group-text">₹</span>
                                                        <input class="form-control form-control-lg" type="number" name="gross_salary" id="gross_salary" 
                                                               value="{{ $salary->gross_salary ?? ($salary->basic + ($salary->da ?? 0) + ($salary->hra ?? 0) + ($salary->conveyance ?? 0) + ($salary->allowance ?? 0) + ($salary->medical ?? 0)) }}" 
                                                               step="0.01" oninput="calculateFromGrossSalary()">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="alert alert-info mb-0">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Basic Salary will be calculated as <strong>{{ $salaryConfig->gross_to_basic_percentage ?? 50 }}%</strong> of Gross Salary
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Earnings Section -->
                                <div class="col-md-6">
                                    <div class="card border-success mb-3">
                                        <div class="card-header bg-success text-white">
                                            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Earnings</h5>
                                        </div>
                                        <div class="card-body">
                                            <!-- Basic Salary - Auto calculated from Gross -->
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-md-5">
                                                    <label class="form-label fw-bold mb-0">Basic Salary <span class="text-danger">*</span></label>
                                                    <small class="text-muted d-block">Auto from Gross</small>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="input-group">
                                                        <span class="input-group-text">₹</span>
                                                        <input class="form-control" type="number" name="basic" id="basic" 
                                                               value="{{ $salary->basic }}" step="0.01" readonly style="background-color: #e9ecef;">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- DA (Percentage) - Editable -->
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-md-5">
                                                    <label class="form-label fw-bold mb-0">DA (Dearness Allowance)</label>
                                                    <small class="text-muted d-block">Default: {{ $salaryConfig->da_percentage ?? 0 }}%</small>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="input-group">
                                                        <input class="form-control" type="number" name="da" id="da" 
                                                               value="{{ $salary->basic > 0 ? round(($salary->da / $salary->basic * 100), 2) : ($salaryConfig->da_percentage ?? 0) }}" 
                                                               step="0.01" oninput="calculateFromPercentages()">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- HRA (Percentage) - Editable -->
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-md-5">
                                                    <label class="form-label fw-bold mb-0">HRA (House Rent Allowance)</label>
                                                    <small class="text-muted d-block">Default: {{ $salaryConfig->hra_percentage ?? 0 }}%</small>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="input-group">
                                                        <input class="form-control" type="number" name="hra" id="hra" 
                                                               value="{{ $salary->basic > 0 ? round(($salary->hra / $salary->basic * 100), 2) : ($salaryConfig->hra_percentage ?? 0) }}" 
                                                               step="0.01" oninput="calculateFromPercentages()">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Conveyance - Editable -->
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-md-5">
                                                    <label class="form-label fw-bold mb-0">Conveyance</label>
                                                    <small class="text-muted d-block">Default: ₹{{ number_format($salaryConfig->conveyance ?? 0, 2) }}</small>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="input-group">
                                                        <span class="input-group-text">₹</span>
                                                        <input class="form-control" type="number" name="conveyance" id="conveyance" 
                                                               value="{{ $salary->conveyance }}" step="0.01" oninput="calculateFromPercentages()">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Special Allowance - Editable -->
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-md-5">
                                                    <label class="form-label fw-bold mb-0">Special Allowance</label>
                                                    <small class="text-muted d-block">Default: ₹{{ number_format($salaryConfig->special_allowance ?? 0, 2) }}</small>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="input-group">
                                                        <span class="input-group-text">₹</span>
                                                        <input class="form-control" type="number" name="allowance" id="allowance" 
                                                               value="{{ $salary->allowance }}" step="0.01" oninput="calculateFromPercentages()">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Medical Allowance - Editable -->
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-md-5">
                                                    <label class="form-label fw-bold mb-0">Medical Allowance</label>
                                                    <small class="text-muted d-block">Default: ₹{{ number_format($salaryConfig->medical_allowance ?? 0, 2) }}</small>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="input-group">
                                                        <span class="input-group-text">₹</span>
                                                        <input class="form-control" type="number" name="medical" id="medical" 
                                                               value="{{ $salary->medical }}" step="0.01" oninput="calculateFromPercentages()">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Dynamic Additions Section -->
                                            <div id="additions-section"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Deductions Section -->
                                <div class="col-md-6">
                                    <div class="card border-danger mb-3">
                                        <div class="card-header bg-danger text-white">
                                            <h5 class="mb-0"><i class="fas fa-minus-circle me-2"></i>Deductions</h5>
                                        </div>
                                        <div class="card-body">
                                            <!-- PF (Percentage) - Editable -->
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-md-5">
                                                    <label class="form-label fw-bold mb-0">PF (Provident Fund)</label>
                                                    <small class="text-muted d-block">Default: {{ $salaryConfig->pf_percentage ?? 0 }}%</small>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="input-group">
                                                        <input class="form-control" type="number" name="pf" id="pf" 
                                                               value="{{ $salary->basic > 0 ? round(($salary->pf / $salary->basic * 100), 2) : ($salaryConfig->pf_percentage ?? 0) }}" 
                                                               step="0.01" oninput="calculateFromPercentages()">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                    <small class="text-muted">Calculated on Basic Salary</small>
                                                </div>
                                            </div>

                                            <!-- ESI (Percentage) - Editable -->
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-md-5">
                                                    <label class="form-label fw-bold mb-0">ESI (Employee State Insurance)</label>
                                                    <small class="text-muted d-block">Default: {{ $salaryConfig->esi_percentage ?? 0 }}%</small>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="input-group">
                                                        <input class="form-control" type="number" name="esi" id="esi" 
                                                               value="{{ $salary->basic > 0 ? round(($salary->esi / $salary->basic * 100), 2) : ($salaryConfig->esi_percentage ?? 0) }}" 
                                                               step="0.01" oninput="calculateFromPercentages()">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                    <small class="text-muted">Calculated on Basic Salary</small>
                                                </div>
                                            </div>

                                            <!-- TDS - Editable -->
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-md-5">
                                                    <label class="form-label fw-bold mb-0">TDS (Tax Deducted at Source)</label>
                                                    <small class="text-muted d-block">Default: ₹{{ number_format($salaryConfig->tds ?? 0, 2) }}</small>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="input-group">
                                                        <span class="input-group-text">₹</span>
                                                        <input class="form-control" type="number" name="tds" id="tds" 
                                                               value="{{ $salary->tds }}" step="0.01" oninput="calculateFromPercentages()">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Professional Tax - Editable -->
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-md-5">
                                                    <label class="form-label fw-bold mb-0">Professional Tax</label>
                                                    <small class="text-muted d-block">Default: ₹{{ number_format($salaryConfig->professional_tax ?? 0, 2) }}</small>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="input-group">
                                                        <span class="input-group-text">₹</span>
                                                        <input class="form-control" type="number" name="tax" id="tax" 
                                                               value="{{ $salary->tax }}" step="0.01" oninput="calculateFromPercentages()">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Welfare Fund - Editable -->
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-md-5">
                                                    <label class="form-label fw-bold mb-0">Welfare Fund</label>
                                                    <small class="text-muted d-block">Default: ₹{{ number_format($salaryConfig->welfare_fund ?? 0, 2) }}</small>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="input-group">
                                                        <span class="input-group-text">₹</span>
                                                        <input class="form-control" type="number" name="welfare" id="welfare" 
                                                               value="{{ $salary->welfare }}" step="0.01" oninput="calculateFromPercentages()">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Employee Leave Deduction - Editable -->
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-md-5">
                                                    <label class="form-label fw-bold mb-0">Leave Deduction</label>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="input-group">
                                                        <span class="input-group-text">₹</span>
                                                        <input class="form-control" type="number" name="employee_leave" id="employee_leave" 
                                                               value="{{ $salary->employee_leave ?? 0 }}" step="0.01" oninput="calculateFromPercentages()">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Dynamic Deductions Section -->
                                            <div id="deductions-section"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Salary Calculation Summary -->
                            <div class="row mt-4" id="salarySummary">
                                <div class="col-md-12">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Salary Breakdown</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4 text-center">
                                                    <div class="border-end">
                                                        <h6 class="text-success mb-2">Total Earnings</h6>
                                                        <h3 class="text-success">₹<span id="total_earnings_display">0.00</span></h3>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    <div class="border-end">
                                                        <h6 class="text-danger mb-2">Total Deductions</h6>
                                                        <h3 class="text-danger">₹<span id="total_deductions_display">0.00</span></h3>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    <h6 class="text-primary mb-2">Net Salary</h6>
                                                    <h3 class="text-primary">₹<span id="net_salary_display">0.00</span></h3>
                                                    <input type="hidden" name="net_salary" id="net_salary" value="{{ $salary->net_salary }}">
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="text-success">Earnings Breakdown</h6>
                                                    <table class="table table-sm table-bordered">
                                                        <tbody id="earningsBreakdownTable">
                                                            <tr><td>Basic Salary</td><td class="text-end">₹<span id="breakdown_basic">0.00</span></td></tr>
                                                            <tr><td>DA Amount</td><td class="text-end">₹<span id="breakdown_da">0.00</span></td></tr>
                                                            <tr><td>HRA Amount</td><td class="text-end">₹<span id="breakdown_hra">0.00</span></td></tr>
                                                            <tr><td>Conveyance</td><td class="text-end">₹<span id="breakdown_conveyance">0.00</span></td></tr>
                                                            <tr><td>Special Allowance</td><td class="text-end">₹<span id="breakdown_allowance">0.00</span></td></tr>
                                                            <tr><td>Medical Allowance</td><td class="text-end">₹<span id="breakdown_medical">0.00</span></td></tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-danger">Deductions Breakdown</h6>
                                                    <table class="table table-sm table-bordered">
                                                        <tbody id="deductionsBreakdownTable">
                                                            <tr><td>PF Amount</td><td class="text-end">₹<span id="breakdown_pf">0.00</span></td></tr>
                                                            <tr><td>ESI Amount</td><td class="text-end">₹<span id="breakdown_esi">0.00</span></td></tr>
                                                            <tr><td>TDS</td><td class="text-end">₹<span id="breakdown_tds">0.00</span></td></tr>
                                                            <tr><td>Professional Tax</td><td class="text-end">₹<span id="breakdown_tax">0.00</span></td></tr>
                                                            <tr><td>Welfare Fund</td><td class="text-end">₹<span id="breakdown_welfare">0.00</span></td></tr>
                                                            <tr><td>Leave Deduction</td><td class="text-end">₹<span id="breakdown_leave">0.00</span></td></tr>
                                                        </tbody>
                                                     </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row mt-4">
                                <div class="col-md-12 text-center">
                                    
                                    <button type="submit" class="btn btn-primary btn-lg me-2">
                                        <i class="fas fa-save me-2"></i>Update Salary
                                    </button>
                                    <a href="{{ route('payroll.combined', ['tab' => 'employee_salary']) }}" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let isUpdatingFromGross = false;
    let masterConfig = {};

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load master config values
    function loadMasterConfig() {
        masterConfig = {
            grossToBasicPercentage: parseFloat($('#gross_to_basic_percentage').val()) || 50,
            daPercentage: parseFloat($('#master_da_percentage').val()) || 0,
            hraPercentage: parseFloat($('#master_hra_percentage').val()) || 0,
            conveyance: parseFloat($('#master_conveyance').val()) || 0,
            specialAllowance: parseFloat($('#master_special_allowance').val()) || 0,
            medicalAllowance: parseFloat($('#master_medical_allowance').val()) || 0,
            pfPercentage: parseFloat($('#master_pf_percentage').val()) || 0,
            esiPercentage: parseFloat($('#master_esi_percentage').val()) || 0,
            professionalTax: parseFloat($('#master_professional_tax').val()) || 0,
            welfareFund: parseFloat($('#master_welfare_fund').val()) || 0,
            tds: parseFloat($('#master_tds').val()) || 0
        };
        
        // Set default values from master if fields are empty
        if (!$('#da').val() || $('#da').val() == 0) $('#da').val(masterConfig.daPercentage);
        if (!$('#hra').val() || $('#hra').val() == 0) $('#hra').val(masterConfig.hraPercentage);
        if (!$('#conveyance').val() || $('#conveyance').val() == 0) $('#conveyance').val(masterConfig.conveyance);
        if (!$('#allowance').val() || $('#allowance').val() == 0) $('#allowance').val(masterConfig.specialAllowance);
        if (!$('#medical').val() || $('#medical').val() == 0) $('#medical').val(masterConfig.medicalAllowance);
        if (!$('#pf').val() || $('#pf').val() == 0) $('#pf').val(masterConfig.pfPercentage);
        if (!$('#esi').val() || $('#esi').val() == 0) $('#esi').val(masterConfig.esiPercentage);
        if (!$('#tax').val() || $('#tax').val() == 0) $('#tax').val(masterConfig.professionalTax);
        if (!$('#welfare').val() || $('#welfare').val() == 0) $('#welfare').val(masterConfig.welfareFund);
        if (!$('#tds').val() || $('#tds').val() == 0) $('#tds').val(masterConfig.tds);
    }

    // Calculate from Gross Salary (when user types in Gross field)
    function calculateFromGrossSalary() {

    let grossSalary = parseFloat($('#gross_salary').val()) || 0;

    // Basic Salary from Gross Salary
    let basicSalary = (grossSalary * masterConfig.grossToBasicPercentage) / 100;

    $('#basic').val(basicSalary.toFixed(2));

    let daPercent = parseFloat($('#da').val()) || 0;
    let hraPercent = parseFloat($('#hra').val()) || 0;
    let pfPercent = parseFloat($('#pf').val()) || 0;
    let esiPercent = parseFloat($('#esi').val()) || 0;

    let daAmount = (daPercent / 100) * basicSalary;
    let hraAmount = (hraPercent / 100) * basicSalary;

    let pfAmount = (pfPercent / 100) * basicSalary;
    let esiAmount = (esiPercent / 100) * basicSalary;

    let conveyance = parseFloat($('#conveyance').val()) || 0;
    let allowance = parseFloat($('#allowance').val()) || 0;
    let medical = parseFloat($('#medical').val()) || 0;

    let tds = parseFloat($('#tds').val()) || 0;
    let tax = parseFloat($('#tax').val()) || 0;
    let welfare = parseFloat($('#welfare').val()) || 0;
    let leaveDeduction = parseFloat($('#employee_leave').val()) || 0;

    let totalEarnings =
        basicSalary +
        daAmount +
        hraAmount +
        conveyance +
        allowance +
        medical;

    $('.addition-input').each(function () {
        totalEarnings += parseFloat($(this).val()) || 0;
    });

    let totalDeductions =
        pfAmount +
        esiAmount +
        tds +
        tax +
        welfare +
        leaveDeduction;

    $('.deduction-input').each(function () {
        totalDeductions += parseFloat($(this).val()) || 0;
    });

    let netSalary = totalEarnings - totalDeductions;

    updateDisplay(
        basicSalary,
        daAmount,
        hraAmount,
        pfAmount,
        esiAmount,
        conveyance,
        allowance,
        medical,
        tds,
        tax,
        welfare,
        leaveDeduction,
        totalEarnings,
        totalDeductions,
        netSalary
    );
}

function calculateFromPercentages() {

    let grossSalary = parseFloat($('#gross_salary').val()) || 0;

    let basicSalary = (grossSalary * masterConfig.grossToBasicPercentage) / 100;

    $('#basic').val(basicSalary.toFixed(2));

    let daPercent = parseFloat($('#da').val()) || 0;
    let hraPercent = parseFloat($('#hra').val()) || 0;
    let pfPercent = parseFloat($('#pf').val()) || 0;
    let esiPercent = parseFloat($('#esi').val()) || 0;

    let daAmount = (daPercent / 100) * basicSalary;
    let hraAmount = (hraPercent / 100) * basicSalary;

    let pfAmount = (pfPercent / 100) * basicSalary;
    let esiAmount = (esiPercent / 100) * basicSalary;

    let conveyance = parseFloat($('#conveyance').val()) || 0;
    let allowance = parseFloat($('#allowance').val()) || 0;
    let medical = parseFloat($('#medical').val()) || 0;

    let tds = parseFloat($('#tds').val()) || 0;
    let tax = parseFloat($('#tax').val()) || 0;
    let welfare = parseFloat($('#welfare').val()) || 0;
    let leaveDeduction = parseFloat($('#employee_leave').val()) || 0;

    let totalEarnings =
        basicSalary +
        daAmount +
        hraAmount +
        conveyance +
        allowance +
        medical;

    $('.addition-input').each(function () {
        totalEarnings += parseFloat($(this).val()) || 0;
    });

    let totalDeductions =
        pfAmount +
        esiAmount +
        tds +
        tax +
        welfare +
        leaveDeduction;

    $('.deduction-input').each(function () {
        totalDeductions += parseFloat($(this).val()) || 0;
    });

    let netSalary = totalEarnings - totalDeductions;

    updateDisplay(
        basicSalary,
        daAmount,
        hraAmount,
        pfAmount,
        esiAmount,
        conveyance,
        allowance,
        medical,
        tds,
        tax,
        welfare,
        leaveDeduction,
        totalEarnings,
        totalDeductions,
        netSalary
    );
}
    
    function updateDisplay(basic, daAmount, hraAmount, pfAmount, esiAmount, conveyance, allowance, medical, tds, tax, welfare, leaveDeduction, totalEarnings, totalDeductions, netSalary) {
        // Update summary
        $('#total_earnings_display').text(totalEarnings.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#total_deductions_display').text(totalDeductions.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#net_salary_display').text(netSalary.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#net_salary').val(netSalary.toFixed(2));
        
        // Update hidden fields
        $('#pf_amount').val(pfAmount.toFixed(2));
        $('#esi_amount').val(esiAmount.toFixed(2));
        
        // Update breakdown table
        $('#breakdown_basic').text(basic.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#breakdown_da').text(daAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#breakdown_hra').text(hraAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#breakdown_conveyance').text(conveyance.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#breakdown_allowance').text(allowance.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#breakdown_medical').text(medical.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#breakdown_pf').text(pfAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#breakdown_esi').text(esiAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#breakdown_tds').text(tds.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#breakdown_tax').text(tax.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#breakdown_welfare').text(welfare.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#breakdown_leave').text(leaveDeduction.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }

    $(document).ready(function() {
        // Load master config first
        loadMasterConfig();
        
        // Initialize gross salary and calculate
        setTimeout(function() {
            let initialGross = parseFloat($('#gross_salary').val()) || 0;
            if (initialGross > 0) {
                calculateFromGrossSalary();
            } else {
                calculateFromPercentages();
            }
        }, 100);
        
        // Recalculate on any input change
        $('#da, #hra, #conveyance, #allowance, #medical, #pf, #esi, #tds, #tax, #welfare, #employee_leave').on('input', function() {
            clearTimeout(window.calcTimeout);
            window.calcTimeout = setTimeout(calculateFromPercentages, 300);
        });
        
        // Preload data for selected employee
        if ($('#employee_id_display').val()) {
            fetchEmployeeDetails();
        }
    });

    function fetchEmployeeDetails() {
        const employeeId = $('#employee_id_display').val();
        if (employeeId) {
            $.ajax({
                url: "{{ route('get.additions.deductions') }}",
                method: 'GET',
                data: { employee_id: employeeId },
                success: function(response) {
                    $('#additions-section').empty();
                    $('#deductions-section').empty();

                    // Display additions
                    if (response.additions && response.additions.length) {
                        response.additions.forEach(function(addition, index) {
                            $('#additions-section').append(`
                                <div class="row mb-3 align-items-center">
                                    <div class="col-md-5">
                                        <label class="form-label mb-0">${addition.name}</label>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" name="additions[${index}][amount]" 
                                                   class="form-control addition-input" 
                                                   data-name="${addition.name}"
                                                   value="${addition.unit_amount || 0}" 
                                                   step="0.01" 
                                                   oninput="calculateFromPercentages()">
                                        </div>
                                    </div>
                                </div>
                            `);
                        });
                    }

                    // Display deductions
                    if (response.deductions && response.deductions.length) {
                        response.deductions.forEach(function(deduction, index) {
                            $('#deductions-section').append(`
                                <div class="row mb-3 align-items-center">
                                    <div class="col-md-5">
                                        <label class="form-label mb-0">${deduction.name}</label>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" name="deductions[${index}][amount]" 
                                                   class="form-control deduction-input" 
                                                   data-name="${deduction.name}"
                                                   value="${deduction.unit_amount || 0}" 
                                                   step="0.01" 
                                                   oninput="calculateFromPercentages()">
                                        </div>
                                    </div>
                                </div>
                            `);
                        });
                    }

                    // Recalculate after loading all fields
                    calculateFromPercentages();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error:', textStatus, errorThrown);
                }
            });
        } else {
            $('#additions-section').empty();
            $('#deductions-section').empty();
            calculateFromPercentages();
        }
    }
</script>

<style>
    .card {
        border-radius: 10px;
        overflow: hidden;
    }
    .card-header {
        font-weight: 600;
    }
    .form-label {
        font-weight: 500;
    }
    .input-group-text {
        background-color: #f8f9fa;
    }
    table.table-bordered td {
        padding: 8px;
    }
    .btn {
        border-radius: 8px;
        padding: 10px 25px;
    }
    select:disabled, input:read-only {
        cursor: not-allowed;
    }
    .alert-info {
        background-color: #e1f5fe;
        border-color: #b3e5fc;
    }
    #gross_salary {
        background-color: #ffffff;
        font-weight: bold;
        font-size: 1.1em;
    }
</style>
@endsection