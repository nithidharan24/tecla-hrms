@extends('layouts.index')

@section('content')
<div class="container-fluid mt-5">
    <div class="content">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Memo Send History</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('memo.index') }}">Memos</a></li>
                        <li class="breadcrumb-item active">Send History</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Sent</h5>
                        <h2 class="mb-0">{{ $stats->total }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Successful</h5>
                        <h2 class="mb-0">{{ $stats->sent }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">Failed</h5>
                        <h2 class="mb-0">{{ $stats->failed }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('memo.history') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Employee Name</label>
                        <input type="text" name="employee_name" class="form-control" value="{{ request('employee_name') }}" placeholder="Search by name">
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
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="{{ route('memo.history') }}" class="btn btn-secondary">Reset</a>
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
                                <th>Department</th>
                                <th>Memo Template</th>
                                <th>Status</th>
                                <th>Sent At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sendHistory as $record)
                            <tr>
                                <td>{{ $record->id }}</td>
                                <td>{{ $record->employee_name }}</td>
                                <td>{{ $record->employee_employeeid }}</td>
                                <td>{{ $record->department_name }}</td>
                                <td>{{ $record->memo_name }}</td>
                                <td>
                                    @if($record->status == 'sent')
                                        <span class="badge bg-success">Sent</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </td>
                                <td>{{ $record->sent_at ? date('d-m-Y H:i', strtotime($record->sent_at)) : '-' }}</td>
                                <td>
                                    <a href="{{ route('memo.send-details', $record->id) }}" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    @if($record->status == 'failed')
                                    <button onclick="resendMemo({{ $record->id }})" class="btn btn-sm btn-warning" title="Resend">
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
                                <td colspan="8" class="text-center">No records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $sendHistory->links() }}
            </div>
        </div>
    </div>
</div>

<script>
function resendMemo(id) {
    Swal.fire({
        title: 'Resend Memo?',
        text: 'Are you sure you want to resend this memo?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, resend!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/memo/resend/${id}`, {
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
            window.location.href = `/memo/delete-history/${id}`;
        }
    });
}
</script>
@endsection