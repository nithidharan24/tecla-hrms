@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <div class="container-fluid mt-4">
        <!-- Breadcrumb -->
        <div class="row mb-3">
            <div class="col">
                <h4 class="page-title">Edit Overtime</h4>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('overtime.index') }}">Overtime</a></li>
                    <li class="breadcrumb-item active">Edit Overtime</li>
                </ul>
            </div>
        </div>

        <!-- Add margin for spacing -->
        <div class="mb-4"></div> <!-- Adds bottom space -->

        <!-- Overtime Form -->
        <div class="row">
            <div class="col-md-12">
                <form method="POST" action="{{ route('overtime.update', $overtime->id) }}" id="overtimeForm">
                    @csrf
                    @method('PUT')

                    <!-- Row 1 -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="employee" class="form-label">Select Employee</label>
                            <select class="form-control" id="employee" name="employee_name" required>
                                <option value="">Choose Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ $overtime->employee_name == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="ot-date" class="form-label">Overtime Date</label>
                            <input type="date" class="form-control" id="ot-date" name="overtime_date" 
                                   value="{{ $overtime->overtime_date }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="ot-hours" class="form-label">Overtime Hours</label>
                            <input type="number" class="form-control" id="ot-hours" name="overtime_hours" 
                                   value="{{ $overtime->overtime_hours }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="overtime_type" class="form-label">Overtime Type</label>
                            <select class="form-control" id="overtime_type" name="overtime_type" required>
                                <option value="">Select Type</option>
                                @foreach ($overtimeTypes as $type)
                                    <option value="{{ $type->name }}" {{ $overtime->overtime_type == $type->name ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="approved_by" class="form-label">Approved By</label>
                            <select class="form-control" id="approved_by" name="approved_by" required>
                                <option value="">Choose Approver</option>
                                <option {{ $overtime->approved_by == 'Manager A' ? 'selected' : '' }}>Manager A</option>
                                <option {{ $overtime->approved_by == 'Manager B' ? 'selected' : '' }}>Manager B</option>
                                <option {{ $overtime->approved_by == 'HR Team' ? 'selected' : '' }}>HR Team</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required>{{ $overtime->description }}</textarea>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary" id="submitBtn">Update Overtime</button>
                        <a href="{{ route('overtime.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('overtimeForm');
    const submitBtn = document.getElementById('submitBtn');
    
    form.addEventListener('submit', function() {
        // Disable the submit button
        submitBtn.disabled = true;
        // Change button text to indicate processing
        submitBtn.innerText = 'Updating...';
        // You can also add a spinner here if you want
    });
    
    // Optional: Re-enable the button if form submission fails
    // This would require additional handling with AJAX
});
</script>
@endsection