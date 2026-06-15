@extends('layouts.index')

@section('content')
<div class="container-fluid mt-5">
    <div class="content">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Hike Letter History</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('payroll.combined') }}">Payroll</a></li>
                        <li class="breadcrumb-item active">Hike Letter History</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Sent</h5>
                        <h2 class="mb-0">{{ $stats->total }}</h2>
                        <small>Total letters sent</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Successful</h5>
                        <h2 class="mb-0">{{ $stats->sent }}</h2>
                        <small>Successfully delivered</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">Failed</h5>
                        <h2 class="mb-0">{{ $stats->failed }}</h2>
                        <small>Delivery failed</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Unique Employees</h5>
                        <h2 class="mb-0">{{ $stats->unique_employees }}</h2>
                        <small>Employees received hike</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('salary.hike-history') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Employee Name</label>
                        <input type="text" name="employee_name" class="form-control" value="{{ request('employee_name') }}" placeholder="Search by name">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Employee ID</label>
                        <input type="text" name="employee_employeeid" class="form-control" value="{{ request('employee_employeeid') }}" placeholder="Employee ID">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- History Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Employee</th>
                                <th>Employee ID</th>
                                <th>Designation</th>
                                <th>Department</th>
                                <th>New CTC</th>
                                <th>Status</th>
                                <th>Sent At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $record)
                            <tr>
                                <td>{{ $record->id }}</td>
                                <td>{{ $record->employee_name }}</td>
                                <td>{{ $record->employee_employeeid }}</td>
                                <td>{{ $record->designation_name }}</td>
                                <td>{{ $record->department_name }}</td>
                                <td>
                                    @php
                                        $details = json_decode($record->new_salary_details, true);
                                        $ctc = $details['ctc_annual'] ?? 'N/A';
                                    @endphp
                                    ₹{{ number_format(floatval(str_replace(',', '', $ctc)), 2) }}
                                </td>
                                <td>
                                    @if($record->status == 'sent')
                                        <span class="badge bg-success">Sent</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </td>
                                <td>{{ $record->email_sent_at ? date('d-m-Y H:i', strtotime($record->email_sent_at)) : '-' }}</td>
                                <td>
                                    <a href="{{ route('salary.hike-letter-details', $record->id) }}" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    @if($record->pdf_path)
                                    <a href="{{ route('salary.download-hike-pdf', $record->id) }}" class="btn btn-sm btn-secondary" title="Download PDF">
                                        <i class="fa fa-download"></i>
                                    </a>
                                    @endif
                                    @if($record->status == 'failed')
                                    <button onclick="resendHikeLetter({{ $record->id }})" class="btn btn-sm btn-warning" title="Resend">
                                        <i class="fa fa-repeat"></i>
                                    </button>
                                    @endif
                                    <button onclick="deleteRecord({{ $record->id }})" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">No records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $history->links() }}
            </div>
        </div>
    </div>
</div>

<script>
function resendHikeLetter(id) {
    Swal.fire({
        title: 'Resend Hike Letter?',
        text: 'Are you sure you want to resend this hike letter?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, resend!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ url('salary/resend-hike-letter') }}/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            });
        }
    });
}

function deleteRecord(id) {
    Swal.fire({
        title: 'Delete Record?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ url('salary/delete-hike-history') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            });
        }
    });
}
</script>
@endsection