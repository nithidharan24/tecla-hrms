@extends('layouts.index')

@section('title', 'Automated Payslips')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-file-invoice-dollar"></i>
                        Automated Monthly Payslips
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#generatePayslipsModal">
                            <i class="fas fa-cogs"></i> Generate Payslips
                        </button>
                    </div>
                </div>

                <div class="card-body">
<!-- Summary Statistics (Pure Bootstrap) -->
<div class="row g-3 mb-4">

    <div class="col-lg-3 col-md-6">
      <div class="d-flex align-items-center justify-content-between border-start border-4 border-info bg-white shadow-sm p-3 rounded">
        <div>
          <h3 class="fw-bold mb-1">{{ $summaryStats['total_payslips'] }}</h3>
          <p class="mb-0 text-muted">Total Payslips</p>
        </div>
        <i class="fas fa-file-alt fs-2 text-info"></i>
      </div>
    </div>
  
    <div class="col-lg-3 col-md-6">
      <div class="d-flex align-items-center justify-content-between border-start border-4 border-success bg-white shadow-sm p-3 rounded">
        <div>
          <h3 class="fw-bold mb-1">{{ $summaryStats['emails_sent'] }}</h3>
          <p class="mb-0 text-muted">Emails Sent</p>
        </div>
        <i class="fas fa-envelope fs-2 text-success"></i>
      </div>
    </div>
  
    <div class="col-lg-3 col-md-6">
      <div class="d-flex align-items-center justify-content-between border-start border-4 border-warning bg-white shadow-sm p-3 rounded">
        <div>
          <h3 class="fw-bold mb-1">₹{{ number_format($summaryStats['total_payout'], 0) }}</h3>
          <p class="mb-0 text-muted">Total Payout</p>
        </div>
        <i class="fas fa-money-bill-wave fs-2 text-warning"></i>
      </div>
    </div>
  
    <div class="col-lg-3 col-md-6">
      <div class="d-flex align-items-center justify-content-between border-start border-4 border-danger bg-white shadow-sm p-3 rounded">
        <div>
          <h3 class="fw-bold mb-1">{{ $summaryStats['failed_count'] }}</h3>
          <p class="mb-0 text-muted">Failed</p>
        </div>
        <i class="fas fa-exclamation-triangle fs-2 text-danger"></i>
      </div>
    </div>
  
  </div>
  


                    <!-- Filters -->
                    <form method="GET" action="{{ route('automated-payslips.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="month">Month:</label>
                                <select name="month" id="month" class="form-control">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="year">Year:</label>
                                <select name="year" id="year" class="form-control">
                                    @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="employee_id">Employee:</label>
                                <select name="employee_id" id="employee_id" class="form-control">
                                    <option value="">All Employees</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->employeeid }} - {{ $employee->firstname }} {{ $employee->lastname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary form-control">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Payslips Table -->
                    <div class="table-responsive">
                        <table class="table custom-table datatable">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Employee ID</th>
                                    <th>Designation</th>
                                    <th>Month</th>
                                    <th>Net Salary</th>
                                    <th>Working Days</th>
                                    <th>Status</th>
                                    <th>Email Sent</th>
                                    <th>Generated At</th>
                                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payslips as $payslip)
                                <tr>
                                    <td data-label="Employee">
                                        <strong>{{ $payslip->firstname }} {{ $payslip->lastname }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $payslip->email }}</small>
                                    </td>
                                    
                                    <td data-label="Employee ID">{{ $payslip->employeeid }}</td>
                                    
                                    <td data-label="Designation">{{ $payslip->designation_name ?? 'N/A' }}</td>
                                    
                                    <td data-label="Payroll Month">{{ $payslip->payroll_month_formatted }}</td>
                                    
                                    <td data-label="Net Salary">
                                        <strong class="text-success">₹{{ number_format($payslip->net_salary, 2) }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            Gross: ₹{{ number_format($payslip->total_earnings, 2) }}
                                        </small>
                                    </td>
                                    
                                    <td data-label="Attendance">
                                        {{ $payslip->actual_working_days }}/{{ $payslip->total_working_days }}
                                        @if($payslip->overtime_hours > 0)
                                            <br><small class="text-info">OT: {{ $payslip->overtime_hours }}h</small>
                                        @endif
                                    </td>
                                    
                                    <td data-label="Status">
                                        @if($payslip->status == 'sent')
                                            <span class="badge badge-success">Sent</span>
                                        @elseif($payslip->status == 'generated')
                                            <span class="badge badge-warning">Generated</span>
                                        @else
                                            <span class="badge badge-danger">Failed</span>
                                        @endif
                                    </td>
                                    
                                    <td data-label="Email Sent">
                                        @if($payslip->email_sent)
                                            <span class="badge badge-success">
                                                <i class="fas fa-check"></i> Yes
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($payslip->email_sent_at)->format('d M, h:i A') }}
                                            </small>
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="fas fa-times"></i> No
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <td data-label="Generated At">
                                        {{ \Carbon\Carbon::parse($payslip->generated_at)->format('d M Y, h:i A') }}
                                    </td>
                                    
                                    <td data-label="Actions" class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('automated-payslips.show', $payslip->id) }}" 
                                               class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($payslip->pdf_path)
                                                <a href="{{ route('automated-payslips.download', $payslip->id) }}" 
                                                   class="btn btn-sm btn-success" title="Download PDF">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                            @if($payslip->email)
                                                <form method="POST" action="{{ route('automated-payslips.resend-email', $payslip->id) }}" 
                                                      style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning" 
                                                            title="Resend Email" 
                                                            onclick="return confirm('Resend payslip email to {{ $payslip->email }}?')">
                                                        <i class="fas fa-envelope"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                    
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">
                                        <div class="py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No payslips found</h5>
                                            <p class="text-muted">Generate payslips for the selected month to see them here.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Payslips Modal -->
<div class="modal fade" id="generatePayslipsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('automated-payslips.generate') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Generate Monthly Payslips</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        This will generate payslips for all employees who have salary records for the selected month.
                    </div>
                    
                    <div class="form-group">
                        <label for="generate_month">Month:</label>
                        <select name="month" id="generate_month" class="form-control" required>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="generate_year">Year:</label>
                        <select name="year" id="generate_year" class="form-control" required>
                            @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                                <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Note:</strong> This process will:
                        <ul class="mb-0 mt-2">
                            <li>Calculate salaries based on attendance, leave, and overtime data</li>
                            <li>Generate PDF payslips for all employees</li>
                            <li>Send email notifications with payslip attachments</li>
                            <li>Skip employees who already have payslips for this month</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-cogs"></i> Generate Payslips
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-refresh page every 30 seconds when generation is in progress
    @if(session('success') && strpos(session('success'), 'Generated') !== false)
        setTimeout(function() {
            location.reload();
        }, 30000);
    @endif
});
</script>
@endsection