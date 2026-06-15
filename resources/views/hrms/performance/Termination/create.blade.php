@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Add Termination</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('terminations.index') }}" class="breadcrumb-link">Termination List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Termination</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($selectedEmployee))
        <div class="alert alert-info mb-4">
            You're initiating termination for: <strong>{{ $selectedEmployee->firstname }} {{ $selectedEmployee->lastname }}</strong> (ID: {{ $selectedEmployee->employeeid }})
        </div>
        @endif

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Add Termination</h4>
                    <div class="card-body">
                        <form id="terminationForm" method="POST" action="{{ route('terminations.store') }}" class="needs-validation">
                            @csrf
                            <div class="row">
                                <!-- Employee Selection -->
                                <div class="col-md-6 mb-3">
                                    <label for="employee_id">Select Employee <span class="text-danger">*</span></label>
                                    <select name="employee_id" id="employee_id" class="form-control select2" required>
                                        <option value="">Select Employee</option>
                                        @foreach($activeEmployees as $employee)
                                            <option value="{{ $employee->employeeid }}"
                                                data-firstname="{{ $employee->firstname }}"
                                                data-lastname="{{ $employee->lastname }}"
                                                data-department="{{ $employee->department }}"
                                                @if(isset($selectedEmployee) && $selectedEmployee->employeeid == $employee->employeeid) selected @endif
                                            >
                                                {{ $employee->employeeid }} - {{ $employee->firstname }} {{ $employee->lastname }} ({{ $employee->department }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="error-message text-danger" style="display: none;">Employee selection is required.</div>
                                </div>

                                <!-- Termination Type -->
                                <div class="col-md-6 mb-3">
                                    <label for="termination_type">Termination Type <span class="text-danger">*</span></label>
                                    <select name="termination_type" id="termination_type" class="form-control" required>
                                        <option value="">Select Termination Type</option>
                                        <option value="Misconduct">Misconduct</option>
                                        <option value="Poor Performance">Poor Performance</option>
                                        <option value="Redundancy">Redundancy</option>
                                        <option value="Resignation">Resignation</option>
                                        <option value="Retirement">Retirement</option>
                                        <option value="Others">Others</option>
                                    </select>
                                    <div class="error-message text-danger" style="display: none;">Termination type is required.</div>
                                </div>

                                <!-- Termination Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="termination_date">Termination Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="termination_date" id="termination_date" required>
                                    <div class="error-message text-danger" style="display: none;">Termination date is required.</div>
                                </div>

                                <!-- Notice Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="notice_date">Notice Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="notice_date" id="notice_date" required>
                                    <div class="error-message text-danger" style="display: none;">Notice date is required.</div>
                                </div>

                                <!-- Termination Reason -->
                                <div class="col-md-12 mb-3">
                                    <label for="termination_reason">Termination Reason <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="termination_reason" id="termination_reason" rows="3" placeholder="Enter detailed termination reason" maxlength="255" required></textarea>
                                    <small class="form-text text-muted">Maximum 255 characters</small>
                                    <div class="error-message text-danger" style="display: none;">Termination reason is required.</div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-md-12 mt-4">
                                    <button class="btn btn-primary btn-lg btn-block" type="submit">Save Termination</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Select2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Initialize Select2 for employee dropdown
    $('.select2').select2({
        placeholder: "Select an employee",
        allowClear: true
    });

    // If an employee is pre-selected, trigger the change event
    @if(isset($selectedEmployee))
        $('#employee_id').val('{{ $selectedEmployee->employeeid }}').trigger('change');
    @endif

    const form = document.getElementById('terminationForm');

    function validateField(field) {
        const errorMessage = field.nextElementSibling;
        const trimmedValue = field.value.trim();

        if (!trimmedValue) {
            // Check if labels[0] exists before accessing innerText
            const labelText = field.labels && field.labels[0] ? field.labels[0].innerText.replace('*', '').trim() : field.name;
            errorMessage.innerText = `${labelText} is required.`;
            errorMessage.style.display = 'block';
            return false;
        }

        errorMessage.style.display = 'none';
        return true;
    }

    // Removed the validateDateOrder() function entirely.

    form.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('input', () => {
            validateField(field);
            // Removed calls to validateDateOrder()
        });

        field.addEventListener('change', () => {
            validateField(field);
            // Removed calls to validateDateOrder()
        });
    });

    form.addEventListener('submit', function(event) {
        let allValid = true;

        form.querySelectorAll('input, select, textarea').forEach(field => {
            if (!validateField(field)) {
                allValid = false;
            }
        });

        // Removed call to validateDateOrder()
        // if (!validateDateOrder()) {
        //     allValid = false;
        // }

        if (!allValid) {
            event.preventDefault();
            const firstError = form.querySelector('.error-message[style="display: block;"]');
            if (firstError) {
                firstError.parentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
});
</script>
@endsection
