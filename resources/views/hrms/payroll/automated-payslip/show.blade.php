@extends('layouts.index')

@section('title', 'Automated Payslips')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Payslip Details</h4>
               
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4 class="card-title">Payslip for {{ $payslip->payroll_month_formatted }}</h4>
                            <p class="text-muted">Generated on: {{ \Carbon\Carbon::parse($payslip->generated_at)->format('M d, Y h:i A') }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="btn-group">
                                <!-- <a href="{{ route('automated-payslips.download', $payslip->id) }}" class="btn btn-primary">
                                    <i class="fas fa-download me-1"></i> Download PDF
                                </a> -->
                                
                                <a href="{{ route('payroll.combined') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Employee Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Employee Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Employee ID:</strong> {{ $payslip->employeeid }}</p>
                                            <p><strong>Name:</strong> {{ $payslip->firstname }} {{ $payslip->lastname }}</p>
                                            <p><strong>Designation:</strong> {{ $payslip->designation_name }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Joining Date:</strong> {{ \Carbon\Carbon::parse($payslip->joiningdate)->format('M d, Y') }}</p>
                                            <p><strong>Email:</strong> {{ $payslip->email }}</p>
                                            <p><strong>Phone:</strong> {{ $payslip->phone }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Summary -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Payment Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Payroll Month:</strong> {{ $payslip->payroll_month_formatted }}</p>
                                            <p><strong>Working Days:</strong> {{ $payslip->actual_working_days }} of {{ $payslip->total_working_days }}</p>
                                            <p><strong>Total Hours:</strong> {{ number_format($payslip->total_hours_worked, 2) }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Status:</strong> 
                                                <span class="badge bg-{{ $payslip->status == 'generated' ? 'success' : ($payslip->status == 'sent' ? 'info' : 'danger') }}">
                                                    {{ ucfirst($payslip->status) }}
                                                </span>
                                            </p>
                                            <p><strong>Email Sent:</strong> 
                                                {{ $payslip->email_sent ? 'Yes ('.\Carbon\Carbon::parse($payslip->email_sent_at)->format('M d, Y').')' : 'No' }}
                                            </p>
                                            <p><strong>Net Salary:</strong> ₹{{ number_format($payslip->net_salary, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Earnings -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title mb-0">Earnings</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Component</th>
                                                    <th class="text-end">Amount (₹)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Basic Salary</td>
                                                    <td class="text-end">{{ number_format($payslip->basic_salary, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>House Rent Allowance (HRA)</td>
                                                    <td class="text-end">{{ number_format($payslip->hra, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Dearness Allowance (DA)</td>
                                                    <td class="text-end">{{ number_format($payslip->da, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Conveyance Allowance</td>
                                                    <td class="text-end">{{ number_format($payslip->conveyance, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Special Allowance</td>
                                                    <td class="text-end">{{ number_format($payslip->allowance, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Medical Allowance</td>
                                                    <td class="text-end">{{ number_format($payslip->medical, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Welfare Allowance</td>
                                                    <td class="text-end">{{ number_format($payslip->welfare, 2) }}</td>
                                                </tr>

                                                {{-- ───── Additions (assigned via "Add Addition" + reimbursements) ───── --}}
                                                @php
                                                    $addData  = $payslip->additions_data ?? ['items' => []];
                                                    $addItems = $addData['items'] ?? [];
                                                @endphp
                                                @if(count($addItems))
                                                <tr>
                                                    <td colspan="2" class="text-uppercase small fw-bold text-muted pt-3">Additions</td>
                                                </tr>
                                                @foreach($addItems as $addition)
                                                <tr>
                                                    <td class="ps-4">{{ $addition['name'] ?? 'Addition' }}
                                                        @if(!empty($addition['category']))
                                                            <span class="badge bg-light text-muted ms-1">{{ $addition['category'] }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">{{ number_format($addition['amount'] ?? 0, 2) }}</td>
                                                </tr>
                                                @endforeach
                                                @endif

                                                <tr class="table-success">
                                                    <th>Total Earnings</th>
                                                    <th class="text-end">{{ number_format($payslip->total_earnings, 2) }}</th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Deductions -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="card-title mb-0">Deductions</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Component</th>
                                                    <th class="text-end">Amount (₹)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Provident Fund (PF)</td>
                                                    <td class="text-end">{{ number_format($payslip->pf, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Employee State Insurance (ESI)</td>
                                                    <td class="text-end">{{ number_format($payslip->esi, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Tax Deducted at Source (TDS)</td>
                                                    <td class="text-end">{{ number_format($payslip->tds, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Professional Tax</td>
                                                    <td class="text-end">{{ number_format($payslip->tax, 2) }}</td>
                                                </tr>
                                                @if($payslip->lop_deduction > 0)
                                                <tr>
                                                    <td>Loss of Pay (LOP) Deduction ({{ $payslip->lop_days }} days)</td>
                                                    <td class="text-end">{{ number_format($payslip->lop_deduction, 2) }}</td>
                                                </tr>
                                                @endif
                                                @if($payslip->dynamic_deductions > 0)
                                                <tr>
                                                    <td>Other Deductions</td>
                                                    <td class="text-end">{{ number_format($payslip->dynamic_deductions, 2) }}</td>
                                                </tr>
                                                @endif
                                                <tr class="table-danger">
                                                    <th>Total Deductions</th>
                                                    <th class="text-end">{{ number_format($payslip->total_deductions, 2) }}</th>
                                                </tr>
                                                <tr class="table-primary">
                                                    <th>Net Salary Payable</th>
                                                    <th class="text-end">₹{{ number_format($payslip->net_salary, 2) }}</th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Attendance Summary -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h5 class="card-title mb-0">Attendance Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Working Days:</span>
                                        <strong>{{ $payslip->actual_working_days }} / {{ $payslip->total_working_days }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Hours:</span>
                                        <strong>{{ number_format($payslip->total_hours_worked, 2) }} hrs</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Late Arrivals:</span>
                                        <strong>{{ $payslip->late_arrivals }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Early Departures:</span>
                                        <strong>{{ $payslip->early_departures }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Leave Summary -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="card-title mb-0">Leave Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Leave Days:</span>
                                        <strong>{{ $payslip->leave_days_taken }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Unpaid Leave Days:</span>
                                        <strong>{{ $payslip->unpaid_leave_days }}</strong>
                                    </div>
                                    @if(isset($payslip->leave_data['leave_types']) && count($payslip->leave_data['leave_types']) > 0)
                                    <hr>
                                    <h6 class="mb-2">Leave Types:</h6>
                                    @foreach($payslip->leave_data['leave_types'] as $leave)
                                    <div class="d-flex justify-content-between small">
                                        <span>{{ $leave['type'] }}:</span>
                                        <span>{{ $leave['days'] }} days</span>
                                    </div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- LOP Details -->
                    @if($payslip->lop_days > 0 && isset($payslip->lop_data['lop_record']))
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="card-title mb-0">Loss of Pay (LOP) Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong>LOP Days:</strong> {{ $payslip->lop_days }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Deduction Amount:</strong> ₹{{ number_format($payslip->lop_deduction, 2) }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Reason:</strong> {{ $payslip->lop_data['lop_record']['reason'] ?? 'Not specified' }}</p>
                                        </div>
                                    </div>
                                    @if(isset($payslip->lop_data['lop_record']['remarks']))
                                    <div class="row">
                                        <div class="col-12">
                                            <p><strong>Remarks:</strong> {{ $payslip->lop_data['lop_record']['remarks'] }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card-header {
        padding: 0.75rem 1.25rem;
    }
    .table th, .table td {
        padding: 0.75rem;
    }
    .badge {
        font-size: 0.75em;
    }
</style>
@endsection