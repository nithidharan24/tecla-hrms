@extends('layouts.index')

@section('content')
<div class="content container-fluid mt-3">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Offboarding Request</h5>
            <a href="{{ route('offboarding.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('offboarding.store') }}" method="POST" enctype="multipart/form-data" id="offboardingForm">
                @csrf
                
                <!-- Display validation errors if any -->
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Offboarding Type Selection -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <label class="form-label">Offboarding Type *</label>
                        <select class="form-select" name="offboarding_type" id="offboardingType" required 
                                onchange="toggleDeceasedDate()">
                            <option value="">Select Type</option>
                            <option value="resignation" {{ old('offboarding_type') == 'resignation' ? 'selected' : '' }}>Resignation</option>
                            <option value="termination" {{ old('offboarding_type') == 'termination' ? 'selected' : '' }}>Termination</option>
                            <option value="deceased" {{ old('offboarding_type') == 'deceased' ? 'selected' : '' }}>Deceased</option>
                        </select>
                    </div>
                </div>

                <!-- Employee Selection -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <label class="form-label">Employee *</label>
                        <select class="form-select" name="employee_id" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->employeeid }} - {{ $employee->firstname }} {{ $employee->lastname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Deceased Date (Only for deceased type) -->
                <div class="row mb-3" id="deceasedDateField" style="display: none;">
                    <div class="col-md-6">
                        <label class="form-label">Deceased Date *</label>
                        <input type="date" class="form-control" name="deceased_date" value="{{ old('deceased_date') }}">
                    </div>
                </div>

                <!-- Common Fields for all types -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Last Working Date *</label>
                        <input type="date" class="form-control" name="last_working_date" 
                               value="{{ old('last_working_date') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Login Disable Date *</label>
                        <input type="date" class="form-control" name="login_disable_date" 
                               value="{{ old('login_disable_date') }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Reason *</label>
                        <input type="text" class="form-control" name="reason" 
                               value="{{ old('reason') }}" required placeholder="Enter reason">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Replacement Required *</label>
                        <select class="form-select" name="replacement_required" required>
                            <option value="">Select</option>
                            <option value="Yes" {{ old('replacement_required') == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ old('replacement_required') == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Explanation</label>
                        <textarea class="form-control" name="explanation" rows="3" 
                                  placeholder="Optional explanation">{{ old('explanation') }}</textarea>
                    </div>
                </div>

                <!-- Hidden fields for status and exit type -->
                <input type="hidden" name="employee_status" id="employeeStatus" value="Active">
                <input type="hidden" name="exit_type" id="exitType" value="">

                <!-- Attachment -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Attachment</label>
                        <input type="file" class="form-control" name="attachment">
                        <small class="text-muted">Max. size is 5 MB (PDF, DOC, DOCX, JPG, PNG)</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Submit
                        </button>
                        <a href="{{ route('offboarding.index') }}" class="btn btn-secondary">
                            <i class="fa fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set hidden fields based on selected type
    const typeSelect = document.getElementById('offboardingType');
    
    typeSelect.addEventListener('change', function() {
        toggleDeceasedDate();
        updateHiddenFields();
    });
    
    // Initialize on page load
    toggleDeceasedDate();
    updateHiddenFields();
});

function toggleDeceasedDate() {
    const typeSelect = document.getElementById('offboardingType');
    const deceasedDateField = document.getElementById('deceasedDateField');
    
    if (typeSelect.value === 'deceased') {
        deceasedDateField.style.display = 'block';
    } else {
        deceasedDateField.style.display = 'none';
    }
}

function updateHiddenFields() {
    const typeSelect = document.getElementById('offboardingType');
    const employeeStatus = document.getElementById('employeeStatus');
    const exitType = document.getElementById('exitType');
    
    switch(typeSelect.value) {
        case 'resignation':
            employeeStatus.value = 'Resigned';
            exitType.value = 'Resignation';
            break;
        case 'termination':
            employeeStatus.value = 'Terminated';
            exitType.value = 'Termination';
            break;
        case 'deceased':
            employeeStatus.value = 'Deceased';
            exitType.value = 'Deceased';
            break;
        default:
            employeeStatus.value = 'Active';
            exitType.value = '';
    }
}

// Basic form validation
document.getElementById('offboardingForm').addEventListener('submit', function(e) {
    const typeSelect = document.getElementById('offboardingType');
    
    if (!typeSelect.value) {
        e.preventDefault();
        alert('Please select offboarding type');
        typeSelect.focus();
        return false;
    }
    
    // Additional validation for deceased type
    if (typeSelect.value === 'deceased') {
        const deceasedDate = document.querySelector('input[name="deceased_date"]');
        if (!deceasedDate.value) {
            e.preventDefault();
            alert('Please enter deceased date');
            deceasedDate.focus();
            return false;
        }
    }
    
    return true;
});
</script>
@endsection