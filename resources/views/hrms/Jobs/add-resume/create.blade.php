@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Add New Candidate</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('add-resume.index') }}">Candidates</a></li>
                    <li class="breadcrumb-item active">Add Candidate</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Candidate Information</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('add-resume.store') }}" method="POST" enctype="multipart/form-data" id="candidateForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                           name="first_name" value="{{ old('first_name') }}" required>
                                    @error('first_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                           name="last_name" value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           name="phone" 
                                           id="phone"
                                           value="{{ old('phone') }}" 
                                           required
                                           onkeypress="return isNumberKey(event)"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Enter only numbers (10-15 digits)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              name="address" rows="3">{{ old('address') }}</textarea>
                                    @error('address')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Position Applied <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('position_applied') is-invalid @enderror" 
                                            name="position_applied" required>
                                        <option value="">-- Select Job Title --</option>
                                        @foreach($jobs as $job)
                                            <option value="{{ $job->job_title }}" {{ old('position_applied') == $job->job_title ? 'selected' : '' }}>
                                                {{ $job->job_title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('position_applied')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Candidate Source</label>
                                    <select class="form-control @error('source') is-invalid @enderror" name="source">
                                        <option value="">Select Source</option>
                                        @foreach(['Website', 'Referral', 'LinkedIn', 'Naukri', 'Walk-in', 'Consultancy', 'Other'] as $source)
                                            <option value="{{ $source }}" {{ old('source') == $source ? 'selected' : '' }}>{{ $source }}</option>
                                        @endforeach
                                    </select>
                                    @error('source')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Experience (Years) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('experience_years') is-invalid @enderror" 
                                           name="experience_years" value="{{ old('experience_years') }}" required>
                                    @error('experience_years')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Expected Salary</label>
                                    <input type="number" class="form-control @error('expected_salary') is-invalid @enderror" 
                                           name="expected_salary" value="{{ old('expected_salary') }}" step="0.01">
                                    @error('expected_salary')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Resume <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('resume') is-invalid @enderror" 
                                           name="resume" accept=".pdf,.doc,.docx" required>
                                    @error('resume')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Accepted formats: PDF, DOC, DOCX (Max: 2MB)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Hiring Manager</label>
                                    <select class="form-control select2 @error('hiring_manager_id') is-invalid @enderror" name="hiring_manager_id">
                                        <option value="">Select Hiring Manager</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('hiring_manager_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->firstname }} {{ $employee->lastname }} {{ $employee->employeeid ? '(' . $employee->employeeid . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('hiring_manager_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Team Lead</label>
                                    <select class="form-control select2 @error('team_lead_id') is-invalid @enderror" name="team_lead_id">
                                        <option value="">Select Team Lead</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('team_lead_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->firstname }} {{ $employee->lastname }} {{ $employee->employeeid ? '(' . $employee->employeeid . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('team_lead_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              name="notes" rows="4">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="submit-section">
                            <button class="btn btn-primary submit-btn" type="submit">Add Candidate</button>
                            <a href="{{ route('add-resume.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Function to allow only numbers
function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

// Alternative: jQuery approach (if you have jQuery loaded)
$(document).ready(function() {
    // Prevent non-numeric input on keydown
    $('#phone').on('keydown', function(e) {
        // Allow: backspace, delete, tab, escape, enter, decimal point
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            (e.keyCode === 65 && e.ctrlKey === true) || 
            (e.keyCode === 67 && e.ctrlKey === true) || 
            (e.keyCode === 86 && e.ctrlKey === true) || 
            (e.keyCode === 88 && e.ctrlKey === true) ||
            // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        
        // Ensure that it is a number and stop the keypress if not
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    
    // Clean input on paste
    $('#phone').on('paste', function(e) {
        var pastedData = e.originalEvent.clipboardData.getData('text');
        if (!/^\d+$/.test(pastedData)) {
            e.preventDefault();
        }
    });
    
    // Real-time validation
    $('#phone').on('input', function() {
        var value = $(this).val();
        // Remove any non-numeric characters
        $(this).val(value.replace(/[^0-9]/g, ''));
    });
    
    // Form validation
    $('#candidateForm').on('submit', function(e) {
        var phone = $('#phone').val();
        var phoneRegex = /^[0-9]+$/;
        
        if (!phoneRegex.test(phone)) {
            e.preventDefault();
            alert('Phone number must contain only numbers.');
            $('#phone').focus();
            return false;
        }
        
        if (phone.length < 10 || phone.length > 15) {
            e.preventDefault();
            alert('Phone number must be between 10 and 15 digits.');
            $('#phone').focus();
            return false;
        }
    });
});
</script>
@endsection
