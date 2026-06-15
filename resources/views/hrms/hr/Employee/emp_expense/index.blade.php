@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Employee Expenses');
@endphp
@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <div class="container-fluid mt-4">

        <!-- Breadcrumb and Buttons in the Same Row -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-6">
                <h4 class="page-title">Expense Claims</h4>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active">Expense Claims</li>
                </ul>
            </div>
            @if(Session::get('role') == 'employee')
            <div class="col-md-6 text-end">
                <a href="{{ route('employee_expenses.create') }}" class="btn" style="background-color: #ff9b44; border-radius: 50px; color: white;">
                    Submit New Expense
                </a>
               
            </div>
            @endif
        </div>

        <!-- Expense Summary Cards -->
        <div class="row mb-4">
            @foreach ([
                'total' => 'Total Expenses', 
                'approved' => 'Approved Expenses', 
                'pending' => 'Pending Expenses', 
                'rejected' => 'Rejected Expenses'
            ] as $key => $label)
                <div class="col-md-3">
                    <div class="card bg-white summary-card">
                        <div class="card-body text-center">
                            <h6>{{ $label }}</h6>
                            <h3 class="font-weight-bold">
                                @if($key == 'total')
                                    ₹{{ number_format($expenseSummary[$key], 2) }}
                                @else
                                    {{ $expenseSummary[$key] }}
                                @endif
                            </h3>
                            <p class="text-muted">this month</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Expense Table -->
      <!-- Expense Table -->
<div class="row">
    <div class="col-md-12 px-2">
        @if (count($expenses) > 0)
            <div class="table-responsive">
                <table class="table table-striped custom-table datatable">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            @if(Session::get('role') === 'admin')
                            <th>Employee</th>
                            @endif
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th>Receipt</th>
                            @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expenses as $index => $expense)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                @if(Session::get('role') === 'admin')
                                <td>
                                    {{ $expense->firstname }} {{ $expense->lastname }}
                                </td>
                                @endif
                                <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d-m-y') }}</td>
                                <td>₹{{ number_format($expense->expense_amount, 2) }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($expense->expense_purpose, 50) }}</td>
                                <td class="text-center">
                                            <div class="dropdown action-label">
                                                <a href="#" class="btn btn-white btn-sm btn-rounded dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                    @if ($expense->expense_status === 'approved')
                                                        <i class="fa-regular fa-circle-dot text-success"></i> Approved
                                                    @elseif ($expense->expense_status === 'rejected')
                                                        <i class="fa-regular fa-circle-dot text-danger"></i> Rejected
                                                    @else
                                                        <i class="fa-regular fa-circle-dot text-warning"></i> Pending
                                                    @endif
                                                </a>
                                                @if(Session::get('role') === 'admin')
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="#" onclick="updateStatus({{ $expense->id }}, 'approved')">
                                                        <i class="fa-regular fa-circle-dot text-success"></i> Approved
                                                    </a>
                                                    <a class="dropdown-item" href="#" onclick="updateStatus({{ $expense->id }}, 'rejected')">
                                                        <i class="fa-regular fa-circle-dot text-danger"></i> Rejected
                                                    </a>
                                                    <a class="dropdown-item" href="#" onclick="updateStatus({{ $expense->id }}, 'pending')">
                                                        <i class="fa-regular fa-circle-dot text-warning"></i> Pending
                                                    </a>
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                      <td>
    @if($expense->receipt_attachment)
        @if(Str::startsWith($expense->receipt_attachment, 'http'))
            <a href="{{ $expense->receipt_attachment }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
        @elseif(Storage::exists($expense->receipt_attachment))
            <a href="{{ Storage::url($expense->receipt_attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
        @elseif(file_exists(public_path($expense->receipt_attachment)))
            <a href="{{ asset($expense->receipt_attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
        @else
            <span class="text-muted">File not found</span>
        @endif
    @else
        <span class="text-muted">None</span>
    @endif
</td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v text-black"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="{{ route('employee_expenses.show', $expense->id) }}">View Details</a></li>
                                                    @if(Session::get('role') === 'employee' && $expense->expense_status === 'pending')
                                                    <li>
    <a class="dropdown-item" href="{{ route('employee_expenses.edit', $expense->id) }}">Edit</a>
</li>
                                                    <li>
                                                        <form id="delete-form-{{ $expense->id }}" action="{{ route('employee_expenses.destroy', $expense->id) }}" method="POST" style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                        <a class="dropdown-item" href="#" onclick="confirmDelete({{ $expense->id }}, '{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }} expense')">Delete</a>
                                                    </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>  <!-- Rest of your table row remains the same -->
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">No expense records found.</div>
        @endif
    </div>
</div>
    </div>
</div>

<!-- Include SweetAlert2 CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function updateStatus(id, status) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to change the status to ${status}.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('employee_expenses.updateStatus', '') }}/" + id,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status,
                    _method: 'PUT' // Laravel sometimes prefers this for updates
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Status updated successfully!',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errorMsg = 'Something went wrong';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    } else if (xhr.responseText) {
                        errorMsg = xhr.responseText;
                    }
                    Swal.fire({
                        title: 'Error!',
                        text: errorMsg,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}
    function confirmDelete(id, expenseInfo) {
        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete the expense from ${expenseInfo}. This action will move the record to trash.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we process your request.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    // Wait until the DOM is fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Check for success message from Laravel session
        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif

        // Check for error message from Laravel session
        @if(session('error'))
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        @endif
    });
</script>

<style>
    .summary-card {
        background-color: #fff !important;
        height: 120px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .breadcrumb {
        padding-top: 10px;
        margin-bottom: 0;
    }

    /* Custom SweetAlert styling */
    .swal2-popup {
        border-radius: 15px;
    }
    
    .swal2-confirm {
        border-radius: 25px;
    }
    
    .swal2-cancel {
        border-radius: 25px;
    }
    
    .badge {
        padding: 5px 10px;
        border-radius: 10px;
        font-size: 12px;
    }
</style>

@endsection