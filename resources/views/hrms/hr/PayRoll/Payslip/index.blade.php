@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Departments');
@endphp

@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Payslip</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                    <li class="breadcrumb-item active">Payslip</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('salary.download.csv', $salary->id) }}" class="btn btn-white">Download CSV</a>
                    <a href="{{ route('salary1.download.pdf', $salary->id) }}" class="btn btn-white">Download PDF</a>
                    <button class="btn btn-white" id="printPayslipBtn">
                        <i class="fa-solid fa-print fa-lg"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
                
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="payslip-title">Payslip for the month of {{ $salaryMonth }}</h4>
                    <div class="row">
                        <div class="col-sm-6 m-b-20">
                            <img src="{{asset('admin/assets/img/logo.png')}}" class="inv-logo" alt="Logo">
                            <ul class="list-unstyled mb-0">
                                <li>Dreamguy's Technologies</li>
                                <li>3864 Quiet Valley Lane,</li>
                                <li>Sherman Oaks, CA, 91403</li>
                            </ul>
                        </div>
                        <div class="col-sm-6 m-b-20">
                            <div class="invoice-details">
                                <h3 class="text-uppercase">Payslip </h3>
                                <ul class="list-unstyled">
                                    <li>Salary Month: <span>{{ $salaryMonth }}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 m-b-20">
                            <ul class="list-unstyled">
                                <li><h5 class="mb-0"><strong>{{ $employee->firstname }} {{ $employee->lastname }}</strong></h5></li>
                                <li><span>{{ $designation->designation }}</span></li>
                                <li>Employee ID: {{ $employee->employeeid }}</li>
                            <li>Joining Date: {{ \Carbon\Carbon::parse($employee->joiningdate)->format('d-m-y') }}</li>

                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div>
                                <h4 class="m-b-10"><strong>Earnings</strong></h4>
                                <table class="table table-bordered">
                                    <tbody>
                                        @foreach ($earnings as $earning)
                                            <tr>
                                                <td><strong>{{ $earning['label'] }}</strong> <span class="float-end">${{ number_format((float) $earning['amount'], 2) }}</span></td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td><strong>Total Earnings</strong> <span class="float-end"><strong>${{ number_format($totalEarnings, 2) }}</strong></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div>
                                <h4 class="m-b-10"><strong>Deductions</strong></h4>
                                <table class="table table-bordered">
                                    <tbody>
                                        @foreach ($deductions as $deduction)
                                            <tr>
                                                <td>
                                                    <strong>{{ $deduction['label'] }}</strong>
                                                    <span class="float-end">${{ number_format((float) $deduction['amount'], 2) }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td><strong>Total Deductions</strong> <span class="float-end"><strong>${{ number_format($totalDeductions, 2) }}</strong></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <p><strong>Net Salary: ${{ number_format($netSalary, 2) }}</strong> ({{ $netSalaryInWords }} only.)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .card, .card * {
            visibility: visible;
        }
        .card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none;
            box-shadow: none;
        }
        .page-header, .breadcrumb, .float-end.ms-auto {
            display: none !important;
        }
        .no-print {
            display: none !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('printPayslipBtn').addEventListener('click', function() {
            window.print();
        });
        
        // Optional: Add event listener for after print
        window.addEventListener('afterprint', function() {
            // You can add any post-print actions here if needed
        });
    });
</script>
@endsection