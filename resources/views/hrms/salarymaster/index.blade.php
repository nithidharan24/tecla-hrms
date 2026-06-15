@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Salary Master Configuration</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Salary Master</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

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

        <div class="row mt-4">
            <div class="col-xl-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-cog me-2"></i>Salary Components Configuration
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('salary-master.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Gross to Basic Percentage -->
                                <div class="col-md-6 mb-4">
                                    <div class="card border-info">
                                        <div class="card-body">
                                            <label class="form-label fw-bold text-info">
                                                <i class="fas fa-percent me-2"></i>Gross to Basic Salary Percentage
                                            </label>
                                            <input type="number" 
                                                   class="form-control form-control-lg"
                                                   name="gross_to_basic_percentage" 
                                                   value="{{ old('gross_to_basic_percentage', $salaryConfig->gross_to_basic_percentage) }}"
                                                   step="0.01"
                                                   min="1"
                                                   max="100"
                                                   required>
                                            <small class="text-muted d-block mt-2">
                                                <i class="fas fa-info-circle me-1"></i>
                                                If Gross Salary is entered, Basic Salary = Gross × (this percentage / 100)
                                            </small>
                                            <div class="mt-3 p-3 bg-light rounded">
                                                <strong>Example:</strong> If set to 50% and Gross is ₹100,000 → Basic = ₹50,000
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- DA Percentage -->
                                <div class="col-md-6 mb-4">
                                    <div class="card border-success">
                                        <div class="card-body">
                                            <label class="form-label fw-bold text-success">
                                                <i class="fas fa-percent me-2"></i>Dearness Allowance (DA) %
                                            </label>
                                            <input type="number" 
                                                   class="form-control form-control-lg"
                                                   name="da_percentage" 
                                                   value="{{ old('da_percentage', $salaryConfig->da_percentage) }}"
                                                   step="0.01"
                                                   min="0"
                                                   max="100"
                                                   required>
                                            <small class="text-muted d-block mt-2">
                                                Default percentage of Basic Salary
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- HRA Percentage -->
                                <div class="col-md-6 mb-4">
                                    <div class="card border-warning">
                                        <div class="card-body">
                                            <label class="form-label fw-bold text-warning">
                                                <i class="fas fa-percent me-2"></i>House Rent Allowance (HRA) %
                                            </label>
                                            <input type="number" 
                                                   class="form-control form-control-lg"
                                                   name="hra_percentage" 
                                                   value="{{ old('hra_percentage', $salaryConfig->hra_percentage) }}"
                                                   step="0.01"
                                                   min="0"
                                                   max="100"
                                                   required>
                                            <small class="text-muted d-block mt-2">
                                                Default percentage of Basic Salary
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Conveyance -->
                                <div class="col-md-6 mb-4">
                                    <div class="card border-danger">
                                        <div class="card-body">
                                            <label class="form-label fw-bold text-danger">
                                                <i class="fas fa-money-bill-wave me-2"></i>Conveyance Allowance (Fixed)
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">₹</span>
                                                <input type="number" 
                                                       class="form-control form-control-lg"
                                                       name="conveyance" 
                                                       value="{{ old('conveyance', $salaryConfig->conveyance) }}"
                                                       step="0.01"
                                                       min="0"
                                                       required>
                                            </div>
                                            <small class="text-muted d-block mt-2">
                                                Fixed amount (not percentage)
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Special Allowance -->
                                <div class="col-md-6 mb-4">
                                    <div class="card border-secondary">
                                        <div class="card-body">
                                            <label class="form-label fw-bold text-secondary">
                                                <i class="fas fa-money-bill-wave me-2"></i>Special Allowance (Fixed)
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">₹</span>
                                                <input type="number" 
                                                       class="form-control form-control-lg"
                                                       name="special_allowance" 
                                                       value="{{ old('special_allowance', $salaryConfig->special_allowance) }}"
                                                       step="0.01"
                                                       min="0"
                                                       required>
                                            </div>
                                            <small class="text-muted d-block mt-2">
                                                Fixed amount (not percentage)
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Medical Allowance -->
                                <div class="col-md-6 mb-4">
                                    <div class="card border-info">
                                        <div class="card-body">
                                            <label class="form-label fw-bold text-info">
                                                <i class="fas fa-money-bill-wave me-2"></i>Medical Allowance (Fixed)
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">₹</span>
                                                <input type="number" 
                                                       class="form-control form-control-lg"
                                                       name="medical_allowance" 
                                                       value="{{ old('medical_allowance', $salaryConfig->medical_allowance) }}"
                                                       step="0.01"
                                                       min="0"
                                                       required>
                                            </div>
                                            <small class="text-muted d-block mt-2">
                                                Fixed amount (not percentage)
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 class="text-danger mb-3">
                                <i class="fas fa-minus-circle me-2"></i>Deductions Configuration
                            </h5>

                            <div class="row">
                                <!-- PF Percentage -->
                                <div class="col-md-6 mb-4">
                                    <div class="card border-dark">
                                        <div class="card-body">
                                            <label class="form-label fw-bold text-dark">
                                                <i class="fas fa-percent me-2"></i>Provident Fund (PF) %
                                            </label>
                                            <input type="number"
                                                   class="form-control form-control-lg"
                                                   name="pf_percentage"
                                                   value="{{ old('pf_percentage', $salaryConfig->pf_percentage) }}"
                                                   step="0.01" min="0" max="100" required>
                                            <small class="text-muted d-block mt-2">Applied on Basic Salary</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- ESI Percentage -->
                                <div class="col-md-6 mb-4">
                                    <div class="card border-dark">
                                        <div class="card-body">
                                            <label class="form-label fw-bold text-dark">
                                                <i class="fas fa-percent me-2"></i>ESI %
                                            </label>
                                            <input type="number"
                                                   class="form-control form-control-lg"
                                                   name="esi_percentage"
                                                   value="{{ old('esi_percentage', $salaryConfig->esi_percentage) }}"
                                                   step="0.01" min="0" max="100" required>
                                            <small class="text-muted d-block mt-2">Applied on Basic Salary</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Professional Tax -->
                                <div class="col-md-6 mb-4">
                                    <div class="card border-secondary">
                                        <div class="card-body">
                                            <label class="form-label fw-bold text-secondary">
                                                <i class="fas fa-money-bill-wave me-2"></i>Professional Tax (Fixed)
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">₹</span>
                                                <input type="number"
                                                       class="form-control form-control-lg"
                                                       name="professional_tax"
                                                       value="{{ old('professional_tax', $salaryConfig->professional_tax) }}"
                                                       step="0.01" min="0" required>
                                            </div>
                                            <small class="text-muted d-block mt-2">Default amount (can be overridden per employee)</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Welfare Fund -->
                                <div class="col-md-6 mb-4">
                                    <div class="card border-secondary">
                                        <div class="card-body">
                                            <label class="form-label fw-bold text-secondary">
                                                <i class="fas fa-money-bill-wave me-2"></i>Welfare Fund (Fixed)
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">₹</span>
                                                <input type="number"
                                                       class="form-control form-control-lg"
                                                       name="welfare_fund"
                                                       value="{{ old('welfare_fund', $salaryConfig->welfare_fund) }}"
                                                       step="0.01" min="0" required>
                                            </div>
                                            <small class="text-muted d-block mt-2">Default amount (can be overridden per employee)</small>
                                        </div>
                                    </div>
                                </div>
                                <!-- TDS -->
                                <div class="col-md-6 mb-4">
                                    <div class="card border-warning">
                                        <div class="card-body">
                                            <label class="form-label fw-bold text-warning">
                                                <i class="fas fa-money-bill-wave me-2"></i>TDS (Fixed)
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">₹</span>
                                                <input type="number"
                                                       class="form-control form-control-lg"
                                                       name="tds"
                                                       value="{{ old('tds', $salaryConfig->tds) }}"
                                                       step="0.01" min="0" required>
                                            </div>
                                            <small class="text-muted d-block mt-2">Default Tax Deducted at Source (can be overridden per employee)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Save Configuration
                                    </button>
                                    <a href="#" class="btn btn-secondary btn-lg ms-2">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Info Panel -->
            <div class="col-xl-4">
                <div class="card shadow-sm bg-light">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Configuration Guide
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6 class="text-primary mb-3">How It Works:</h6>
                        
                        <div class="mb-3">
                            <strong>1. Gross to Basic Conversion</strong>
                            <p class="text-muted small">
                                When adding/editing an employee, if you enter a Gross Salary, the Basic Salary will automatically be calculated as: Basic = Gross × (percentage / 100)
                            </p>
                        </div>

                        <div class="mb-3">
                            <strong>2. Percentage Fields (DA, HRA)</strong>
                            <p class="text-muted small">
                                These are applied to the Basic Salary. The amounts are calculated as: Amount = Basic × (percentage / 100)
                            </p>
                        </div>

                        <div class="mb-3">
                            <strong>3. Fixed Allowances</strong>
                            <p class="text-muted small">
                                Conveyance, Special, and Medical allowances are fixed amounts added to all employees by default. However, these can be overridden per employee.
                            </p>
                        </div>

                        <hr>

                        <h6 class="text-success mb-2">
                            <i class="fas fa-check-circle me-2"></i>Example Calculation
                        </h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Gross Salary</strong></td>
                                <td>₹100,000</td>
                            </tr>
                            <tr>
                                <td><strong>Basic (50%)</strong></td>
                                <td>₹50,000</td>
                            </tr>
                            <tr class="table-success">
                                <td><strong>DA (10%)</strong></td>
                                <td>₹5,000</td>
                            </tr>
                            <tr class="table-success">
                                <td><strong>HRA (15%)</strong></td>
                                <td>₹7,500</td>
                            </tr>
                            <tr class="table-success">
                                <td><strong>Conveyance</strong></td>
                                <td>₹2,000</td>
                            </tr>
                            <tr class="table-info">
                                <td><strong>Total Earnings</strong></td>
                                <td><strong>₹64,500</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .form-control-lg {
        font-size: 1.1rem;
        padding: 0.75rem 1rem;
    }
</style>

@endsection