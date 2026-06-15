@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <!-- Page Header -->
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Configure Candidate Salary</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('recruitment.index') }}" class="breadcrumb-link">Recruitment</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Set Salary</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <!-- Candidate Info Card -->
            <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12">
                <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; border-radius: 12px; overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px; font-weight: bold; box-shadow: 0 4px 8px rgba(0,0,0,0.15);">
                                {{ strtoupper(substr($candidate->first_name, 0, 1)) }}{{ strtoupper(substr($candidate->last_name, 0, 1)) }}
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-0 text-white font-weight-bold">{{ $candidate->first_name }} {{ $candidate->last_name }}</h4>
                                <span class="badge bg-light text-primary mt-1">{{ $candidate->position_applied }}</span>
                            </div>
                        </div>
                        <hr style="border-top: 1px solid rgba(255,255,255,0.15);">
                        <div class="mt-3">
                            <p class="mb-2"><i class="fas fa-envelope me-2"></i> {{ $candidate->email }}</p>
                            <p class="mb-2"><i class="fas fa-phone me-2"></i> {{ $candidate->phone ?? 'N/A' }}</p>
                            <p class="mb-2"><i class="fas fa-history me-2"></i> Experience: {{ $candidate->experience_years }} Years</p>
                            <p class="mb-0"><i class="fas fa-money-bill-wave me-2"></i> Expected Salary: ₹{{ number_format($candidate->expected_salary) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Calculation Info Card -->
                <div class="card shadow-sm border-0" style="border-radius: 12px;">
                    <div class="card-header bg-light border-0">
                        <h5 class="mb-0 text-dark font-weight-bold">
                            <i class="fas fa-info-circle text-primary me-2"></i>Calculation Formulas
                        </h5>
                    </div>
                    <div class="card-body p-3" style="font-size: 13px;">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Basic Salary:</span>
                                <span class="fw-bold" id="formula-basic">Gross × <span id="lbl-basic-pct">50</span>%</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>DA (Dearness Allowance):</span>
                                <span class="fw-bold" id="formula-da">Basic × <span id="lbl-da-pct">10</span>%</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>HRA (House Rent Allowance):</span>
                                <span class="fw-bold" id="formula-hra">Basic × <span id="lbl-hra-pct">15</span>%</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>PF Deduction:</span>
                                <span class="fw-bold" id="formula-pf">Basic × <span id="lbl-pf-pct">12</span>%</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>ESI Deduction:</span>
                                <span class="fw-bold" id="formula-esi">Basic × <span id="lbl-esi-pct">0.75</span>%</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Net Take-home:</span>
                                <span class="fw-bold text-success">Gross - Total Deductions</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Salary Configuration Form -->
            <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12">
                <div class="card shadow-sm border-0" style="border-radius: 12px;">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 text-dark font-weight-bold">
                            <i class="fas fa-sliders-h text-primary me-2"></i>Salary Structures
                        </h4>
                        <a href="{{ route('recruitment.index', ['tab' => 'offer-letter']) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('recruitment.store-salary', $candidate->id) }}" id="salaryForm">
                            @csrf
                            
                            <!-- Gross Salary Input -->
                            <div class="p-3 mb-4 rounded" style="background-color: #f0f4f8; border-left: 5px solid #1e3c72;">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold text-dark mb-0" style="font-size: 16px;">
                                            <i class="fas fa-wallet text-primary me-2"></i>Enter Monthly Gross Salary
                                        </label>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-dark border-end-0 fw-bold">₹</span>
                                            <input type="number" 
                                                   class="form-control form-control-lg border-start-0 fw-bold text-primary" 
                                                   id="gross_salary" 
                                                   name="gross_salary" 
                                                   placeholder="e.g. 50000" 
                                                   value="{{ old('gross_salary', $salaryStructure->gross_salary ?? '') }}"
                                                   required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Salary Components Breakdown -->
                            <h5 class="text-secondary border-bottom pb-2 mb-3">
                                <i class="fas fa-plus-circle text-success me-2"></i>Earnings (Monthly)
                            </h5>
                            <div class="row">
                                <!-- Basic Salary -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Basic Salary</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01" class="form-control calc-earning" id="basic_salary" name="basic_salary" value="{{ old('basic_salary', $salaryStructure->basic_salary ?? '') }}" required>
                                    </div>
                                </div>

                                <!-- DA -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Dearness Allowance (DA)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01" class="form-control calc-earning" id="da_amount" name="da_amount" value="{{ old('da_amount', $salaryStructure->da_amount ?? '') }}">
                                    </div>
                                </div>

                                <!-- HRA -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">House Rent Allowance (HRA)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01" class="form-control calc-earning" id="hra_amount" name="hra_amount" value="{{ old('hra_amount', $salaryStructure->hra_amount ?? '') }}">
                                    </div>
                                </div>

                                <!-- Conveyance -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Conveyance Allowance</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01" class="form-control calc-earning" id="conveyance" name="conveyance" value="{{ old('conveyance', $salaryStructure->conveyance ?? '') }}">
                                    </div>
                                </div>

                                <!-- Special Allowance -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Special Allowance</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01" class="form-control calc-earning" id="special_allowance" name="special_allowance" value="{{ old('special_allowance', $salaryStructure->special_allowance ?? '') }}">
                                    </div>
                                </div>

                                <!-- Medical Allowance -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Medical Allowance</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01" class="form-control calc-earning" id="medical_allowance" name="medical_allowance" value="{{ old('medical_allowance', $salaryStructure->medical_allowance ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            <h5 class="text-secondary border-bottom pb-2 mb-3 mt-4">
                                <i class="fas fa-minus-circle text-danger me-2"></i>Deductions (Monthly)
                            </h5>
                            <div class="row">
                                <!-- PF -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Provident Fund (PF)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01" class="form-control calc-deduction" id="pf_amount" name="pf_amount" value="{{ old('pf_amount', $salaryStructure->pf_amount ?? '') }}">
                                    </div>
                                </div>

                                <!-- ESI -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Employee State Insurance (ESI)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01" class="form-control calc-deduction" id="esi_amount" name="esi_amount" value="{{ old('esi_amount', $salaryStructure->esi_amount ?? '') }}">
                                    </div>
                                </div>

                                <!-- Professional Tax -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Professional Tax (PT)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01" class="form-control calc-deduction" id="professional_tax" name="professional_tax" value="{{ old('professional_tax', $salaryStructure->professional_tax ?? '') }}">
                                    </div>
                                </div>

                                <!-- Welfare Fund -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Welfare Fund</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01" class="form-control calc-deduction" id="welfare_fund" name="welfare_fund" value="{{ old('welfare_fund', $salaryStructure->welfare_fund ?? '') }}">
                                    </div>
                                </div>

                                <!-- TDS -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">TDS (Tax Deducted at Source)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01" class="form-control calc-deduction" id="tds" name="tds" value="{{ old('tds', $salaryStructure->tds ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Net Salary -->
                            <div class="p-3 mb-4 mt-4 rounded" style="background-color: #e2f0d9; border-left: 5px solid #385723;">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold text-dark mb-0" style="font-size: 16px;">
                                            <i class="fas fa-hand-holding-usd text-success me-2"></i>Net Take-home Salary (Monthly)
                                        </label>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-dark border-end-0 fw-bold">₹</span>
                                            <input type="number" 
                                                   class="form-control form-control-lg border-start-0 fw-bold text-success" 
                                                   id="net_salary" 
                                                   name="net_salary" 
                                                   value="{{ old('net_salary', $salaryStructure->net_salary ?? '') }}"
                                                   readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('recruitment.index', ['tab' => 'offer-letter']) }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-1"></i> Save Salary Configuration
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Live Calculation Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let masterConfig = null;

        // Fetch master configurations on load
        fetch('{{ route("salary-master.get-config") }}')
            .then(response => response.json())
            .then(data => {
                masterConfig = data;
                
                // Update formula display labels
                document.getElementById('lbl-basic-pct').textContent = data.gross_to_basic_percentage;
                document.getElementById('lbl-da-pct').textContent = data.da_percentage;
                document.getElementById('lbl-hra-pct').textContent = data.hra_percentage;
                document.getElementById('lbl-pf-pct').textContent = data.pf_percentage;
                document.getElementById('lbl-esi-pct').textContent = data.esi_percentage;
                
                // If gross salary is already filled, calculate or recalculate
                const grossInput = document.getElementById('gross_salary');
                if (grossInput.value) {
                    // If components are empty, initialize them
                    if (!document.getElementById('basic_salary').value) {
                        calculateComponents(parseFloat(grossInput.value));
                    }
                }
            })
            .catch(error => {
                console.error('Error loading salary master configuration:', error);
            });

        const grossInput = document.getElementById('gross_salary');
        grossInput.addEventListener('input', function() {
            const grossVal = parseFloat(this.value);
            if (!isNaN(grossVal) && grossVal > 0) {
                calculateComponents(grossVal);
            } else {
                clearComponents();
            }
        });

        // Add event listeners on components to manually recalculate net salary
        const calcInputs = document.querySelectorAll('.calc-earning, .calc-deduction');
        calcInputs.forEach(input => {
            input.addEventListener('input', function() {
                recalculateNet();
            });
        });

        function calculateComponents(gross) {
            if (!masterConfig) return;

            // 1. Basic Salary
            const basicPct = parseFloat(masterConfig.gross_to_basic_percentage) || 50;
            const basic = (gross * basicPct) / 100;
            document.getElementById('basic_salary').value = basic.toFixed(2);

            // 2. Dearness Allowance (DA)
            const daPct = parseFloat(masterConfig.da_percentage) || 0;
            const da = (basic * daPct) / 100;
            document.getElementById('da_amount').value = da.toFixed(2);

            // 3. House Rent Allowance (HRA)
            const hraPct = parseFloat(masterConfig.hra_percentage) || 0;
            const hra = (basic * hraPct) / 100;
            document.getElementById('hra_amount').value = hra.toFixed(2);

            // 4. Fixed Allowances from Master Config
            document.getElementById('conveyance').value = (parseFloat(masterConfig.conveyance) || 0).toFixed(2);
            document.getElementById('special_allowance').value = (parseFloat(masterConfig.special_allowance) || 0).toFixed(2);
            document.getElementById('medical_allowance').value = (parseFloat(masterConfig.medical_allowance) || 0).toFixed(2);

            // 5. Deductions
            const pfPct = parseFloat(masterConfig.pf_percentage) || 0;
            const pf = (basic * pfPct) / 100;
            document.getElementById('pf_amount').value = pf.toFixed(2);

            const esiPct = parseFloat(masterConfig.esi_percentage) || 0;
            const esi = (basic * esiPct) / 100;
            document.getElementById('esi_amount').value = esi.toFixed(2);

            document.getElementById('professional_tax').value = (parseFloat(masterConfig.professional_tax) || 0).toFixed(2);
            document.getElementById('welfare_fund').value = (parseFloat(masterConfig.welfare_fund) || 0).toFixed(2);
            document.getElementById('tds').value = (parseFloat(masterConfig.tds) || 0).toFixed(2);

            // Recalculate Net
            recalculateNet();
        }

        function recalculateNet() {
            const grossVal = parseFloat(grossInput.value) || 0;
            
            const pf = parseFloat(document.getElementById('pf_amount').value) || 0;
            const esi = parseFloat(document.getElementById('esi_amount').value) || 0;
            const pt = parseFloat(document.getElementById('professional_tax').value) || 0;
            const welfare = parseFloat(document.getElementById('welfare_fund').value) || 0;
            const tds = parseFloat(document.getElementById('tds').value) || 0;

            const totalDeductions = pf + esi + pt + welfare + tds;
            const net = grossVal - totalDeductions;
            
            document.getElementById('net_salary').value = net.toFixed(2);
        }

        function clearComponents() {
            document.querySelectorAll('.calc-earning, .calc-deduction, #net_salary').forEach(el => {
                el.value = '';
            });
        }
    });
</script>
@endsection
