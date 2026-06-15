@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Leave</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin-leaves.index') }}" class="breadcrumb-link">Leave List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Leave</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Edit Leave</h4>
                    <div class="card-body">
                        <form id="leaveForm" method="POST" action="{{ route('admin-leaves.update', $leave->id) }}" class="needs-validation">
                            @csrf
                            @method('PUT')
                            <div class="row">
                             <!-- Employee ID Input -->
<div class="col-md-6 mb-3">
    <label for="employee_id">Employee ID <span class="text-danger">*</span></label>
    <input type="text" class="form-control" name="employee_id" id="employee_id" value="{{ old('employee_id', $employeeid) }}" placeholder="Enter Employee ID" required>
    <div class="error-message text-danger" style="display: none;">Employee ID is required.</div>
</div>


                                <!-- Leave Type (Dropdown) -->
                                <div class="col-md-6 mb-3">
                                    <label for="leave_type">Leave Type <span class="text-danger">*</span></label>
                                    <select name="leave_type" id="leave_type" class="form-control" required>
                                        <option value="">Select Leave Type</option>
                                        <option value="Medical Leave" {{ $leave->leave_type == 'Medical Leave' ? 'selected' : '' }}>Medical Leave</option>
                                        <option value="Hospitalisation" {{ $leave->leave_type == 'Hospitalisation' ? 'selected' : '' }}>Hospitalisation</option>
                                        <option value="Maternity Leave" {{ $leave->leave_type == 'Maternity Leave' ? 'selected' : '' }}>Maternity Leave</option>
                                        <option value="Casual Leave" {{ $leave->leave_type == 'Casual Leave' ? 'selected' : '' }}>Casual Leave</option>
                                        <option value="LOP" {{ $leave->leave_type == 'LOP' ? 'selected' : '' }}>LOP</option>
                                        <option value="Paternity Leave" {{ $leave->leave_type == 'Paternity Leave' ? 'selected' : '' }}>Paternity Leave</option>
                                        <option value="Sick" {{ $leave->leave_type == 'Sick' ? 'selected' : '' }}>Sick</option>
                                    </select>
                                    <div class="error-message text-danger" style="display: none;">Leave type is required.</div>
                                </div>

                                <!-- From Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="from_date">From <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="from_date" id="from_date" value="{{ old('from_date', $leave->from_date) }}" required>
                                    <div class="error-message text-danger" style="display: none;">Start date is required.</div>
                                </div>

                                <!-- To Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="to_date">To <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="to_date" id="to_date" value="{{ old('to_date', $leave->to_date) }}" required>
                                    <div class="error-message text-danger" style="display: none;">End date is required.</div>
                                </div>

                                <!-- Number of Days -->
                                <div class="col-md-6 mb-3">
                                    <label for="num_days">Number of Days <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="num_days" id="num_days" value="{{ old('num_days', $leave->no_of_days) }}" readonly>
                                    <div class="error-message text-danger" style="display: none;">Number of days is required.</div>
                                </div>

                                <!-- Remaining Leaves -->
                                <div class="col-md-6 mb-3">
                                    <label for="remaining_leaves">Remaining Leaves <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="remaining_leaves" id="remaining_leaves" value="{{ old('remaining_leaves', $leave->remaining_leaves) }}" readonly>
                                    <div class="error-message text-danger" style="display: none;">Remaining leaves are required.</div>
                                </div>

                                <!-- Leave Reason -->
                                <div class="col-md-12 mb-3">
                                    <label for="leave_reason">Leave Reason <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="leave_reason" id="leave_reason" rows="3" placeholder="Enter Leave Reason" maxlength="255" required>{{ old('leave_reason', $leave->leave_reason) }}</textarea>
                                    <div class="error-message text-danger" style="display: none;">Leave reason is required and must be less than 255 characters.</div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-md-12 mt-4">
                                    <button class="btn btn-primary btn-lg btn-block" type="submit">Update Leave</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Validation and Calculation Script -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('leaveForm');
    const errorMessages = form.querySelectorAll('.error-message');
    const remainingLeavesInput = document.getElementById('remaining_leaves');
    const originalRemainingLeaves = parseInt(remainingLeavesInput.value, 10);

    function validateField(field) {
        const errorMessage = field.nextElementSibling;
        const trimmedValue = field.value.trim();

        if (!trimmedValue) {
            errorMessage.style.display = 'block';
        } else if (field.name === 'employee_name') {
            if (trimmedValue.length < 3) {
                errorMessage.innerText = 'Employee name must be at least 3 characters.';
                errorMessage.style.display = 'block';
            } else {
                errorMessage.style.display = 'none';
            }
        } else if (field.name === 'leave_reason') {
            if (trimmedValue.length > 255) {
                errorMessage.innerText = `Leave reason exceeds the maximum limit of 255 characters. (${trimmedValue.length} characters used)`;
                errorMessage.style.display = 'block';
            } else {
                errorMessage.style.display = 'none';
            }
        } else {
            errorMessage.style.display = 'none';
        }
    }

    function calculateDays() {
        const fromDateInput = document.getElementById('from_date').value;
        const toDateInput = document.getElementById('to_date').value;
        const numDaysInput = document.getElementById('num_days');

        if (fromDateInput && toDateInput) {
            const fromDate = new Date(fromDateInput);
            const toDate = new Date(toDateInput);

            if (fromDate <= toDate) {
                const timeDiff = toDate.getTime() - fromDate.getTime();
                const daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24)) + 1;
                numDaysInput.value = daysDiff;
                updateRemainingLeaves(daysDiff);
            } else {
                numDaysInput.value = '';
                updateRemainingLeaves(0);
            }
        } else {
            numDaysInput.value = '';
            updateRemainingLeaves(0);
        }
    }

    function updateRemainingLeaves(takenDays) {
        const newRemainingLeaves = originalRemainingLeaves - takenDays;
        remainingLeavesInput.value = newRemainingLeaves < 0 ? 0 : newRemainingLeaves;
    }

    form.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('input', () => validateField(field));
    });

    document.getElementById('from_date').addEventListener('change', calculateDays);
    document.getElementById('to_date').addEventListener('change', calculateDays);

    form.addEventListener('submit', function(event) {
        let allValid = true;
        form.querySelectorAll('input, select, textarea').forEach(field => {
            const trimmedValue = field.value.trim();
            if (!trimmedValue) {
                allValid = false;
                validateField(field);
            } else if (field.name === 'leave_reason' && trimmedValue.length > 255) {
                allValid = false;
                validateField(field);
            }
        });

        if (!allValid) {
            event.preventDefault();
        }
    });
});
</script>
@endsection
