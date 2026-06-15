@extends('layouts.index')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h3>Add Job</h3>
                </div>
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('managejobs.store') }}" method="POST" id="jobForm">
                        @csrf
                        <div class="row">
                            <!-- Department -->
                            <div class="col-md-6 mb-3">
                                <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                                <select class="form-control select2 @error('department') is-invalid @enderror" 
                                    name="department" id="department" required>
                                    <option value="" disabled selected>Select a Department</option>
                                    @foreach ($department as $dep)
                                        <option value="{{ $dep->department }}" {{ old('department') == $dep->department ? 'selected' : '' }}>
                                            {{ $dep->department }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Please select a department.</div>
                                @enderror
                            </div>

                            <!-- Job Title - <CHANGE> Now dynamically populated based on department -->
                            <div class="col-md-6 mb-3">
                                <label for="job_title" class="form-label">Job Title <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select class="form-control @error('job_title') is-invalid @enderror" 
                                        id="job_title_select" name="job_title_select">
                                        <option value="">Select from suggestions</option>
                                    </select>
                                    <span class="input-group-text">or</span>
                                </div>
                                <input type="text" class="form-control mt-2 @error('job_title') is-invalid @enderror" 
                                    id="job_title" name="job_title" value="{{ old('job_title') }}"
                                    placeholder="Enter custom job title" required minlength="3" maxlength="100">
                                @error('job_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Please enter a valid job title (3-100 characters).</div>
                                @enderror
                            </div>

                            <!-- Job Location -->
                            <div class="col-md-6 mb-3">
                                <label for="job_location" class="form-label">Job Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('job_location') is-invalid @enderror" 
                                    id="job_location" name="job_location" value="{{ old('job_location') }}"
                                    placeholder="Enter location" required minlength="2" maxlength="100">
                                @error('job_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Please enter a valid job location (2-100 characters).</div>
                                @enderror
                            </div>
 
                            <!-- Vacancies -->
                            <div class="col-md-6 mb-3">
                                <label for="vacancies" class="form-label">No of Vacancies <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('vacancies') is-invalid @enderror" 
                                    id="vacancies" name="vacancies" value="{{ old('vacancies') }}"
                                    placeholder="Enter number" required min="1" max="999">
                                @error('vacancies')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Please enter a valid number of vacancies (1-999).</div>
                                @enderror
                            </div>

                            <!-- Experience -->
                            <div class="col-md-6 mb-3">
                                <label for="experience" class="form-label">Experience <span class="text-danger">*</span></label>
                                <select class="form-control select2 @error('experience') is-invalid @enderror" 
                                    name="experience" id="experience" required>
                                    <option value="" disabled selected>Select Experience Level</option>
                                    @foreach ($experiences as $exp)
                                        <option value="{{ $exp->experience }}" {{ old('experience') == $exp->experience ? 'selected' : '' }}>
                                            {{ $exp->experience }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('experience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Please select an experience level.</div>
                                @enderror
                            </div>

                            <!-- Skills - <CHANGE> Now auto-populated based on job title -->
                            <div class="col-md-6 mb-3">
                                <label for="skills" class="form-label">Skills (Comma separated) <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('skills') is-invalid @enderror" 
                                    id="skills" name="skills" rows="2"
                                    placeholder="Skills will auto-populate based on job title" required maxlength="500">{{ old('skills') }}</textarea>
                                <small class="form-text text-muted">You can edit the auto-populated skills or add custom ones.</small>
                                @error('skills')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Please enter skills separated by commas (max 500 characters).</div>
                                @enderror
                            </div>

                            <!-- Salary From -->
                            <div class="col-md-6 mb-3">
                                <label for="salary_from" class="form-label">Salary From <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('salary_from') is-invalid @enderror" 
                                    id="salary_from" name="salary_from" value="{{ old('salary_from') }}"
                                    placeholder="Enter minimum salary" required min="1000" max="9999999">
                                @error('salary_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Please enter a valid minimum salary (minimum 1000).</div>
                                @enderror
                            </div>

                            <!-- Salary To -->
                            <div class="col-md-6 mb-3">
                                <label for="salary_to" class="form-label">Salary To <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('salary_to') is-invalid @enderror" 
                                    id="salary_to" name="salary_to" value="{{ old('salary_to') }}"
                                    placeholder="Enter maximum salary" required min="1000" max="9999999">
                                @error('salary_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Please enter a valid maximum salary (must be ≥ minimum salary).</div>
                                @enderror
                            </div>

                            <!-- Job Type -->
                            <div class="col-md-6 mb-3">
                                <label for="job_type" class="form-label">Job Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('job_type') is-invalid @enderror" 
                                    id="job_type" name="job_type" required>
                                    <option value="" disabled selected>Select Job Type</option>
                                    <option value="Full Time" {{ old('job_type') == 'Full Time' ? 'selected' : '' }}>Full Time</option>
                                    <option value="Part Time" {{ old('job_type') == 'Part Time' ? 'selected' : '' }}>Part Time</option>
                                    <option value="Contract" {{ old('job_type') == 'Contract' ? 'selected' : '' }}>Contract</option>
                                </select>
                                @error('job_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Please select a job type.</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                    <option value="" disabled selected>Select Status</option>
                                    <option value="Open" {{ old('status') == 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="Closed" {{ old('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                                    <option value="Cancelled" {{ old('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Please select a status.</div>
                                @enderror
                            </div>

                            <!-- Start Date -->
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                    id="start_date" name="start_date" value="{{ old('start_date') }}" required
                                    min="{{ date('Y-m-d') }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Please select a valid start date (today or later).</div>
                                @enderror
                            </div>

                            <!-- Expired Date -->
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">Expired Date</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                    id="end_date" name="end_date" value="{{ old('end_date') }}" 
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Please select a valid expired date (after start date).</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                    id="description" name="description" rows="3"
                                    placeholder="Enter job description" required minlength="10" maxlength="2000">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback">Please enter a description (10-2000 characters).</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="screening_questions" class="form-label">Screening Questions</label>
                                <textarea class="form-control @error('screening_questions') is-invalid @enderror"
                                    id="screening_questions" name="screening_questions" rows="3"
                                    placeholder="Enter screening questions for candidates">{{ old('screening_questions') }}</textarea>
                                @error('screening_questions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary px-5" id="submitBtn">
                                    <span id="submitText">Submit</span>
                                    <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                                </button>
                                <a href="{{ route('managejobs.index') }}" class="btn btn-secondary px-5 ms-2">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    .text-danger {
        color: #dc3545 !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('jobForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitSpinner = document.getElementById('submitSpinner');
    const departmentSelect = document.getElementById('department');
    const jobTitleSelect = document.getElementById('job_title_select');
    const jobTitleInput = document.getElementById('job_title');
    const skillsInput = document.getElementById('skills');

    // <CHANGE> Fetch job titles when department changes
    departmentSelect.addEventListener('change', function() {
        const department = this.value;
        
        if (!department) {
            jobTitleSelect.innerHTML = '<option value="">Select from suggestions</option>';
            return;
        }

        fetch('{{ route("managejobs.getJobTitlesByDepartment") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            body: JSON.stringify({ department: department })
        })
        .then(response => response.json())
        .then(data => {
            jobTitleSelect.innerHTML = '<option value="">Select from suggestions</option>';
            if (data.titles && data.titles.length > 0) {
                data.titles.forEach(title => {
                    const option = document.createElement('option');
                    option.value = title;
                    option.textContent = title;
                    jobTitleSelect.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Error fetching job titles:', error));
    });

    // <CHANGE> Populate job title input when selecting from dropdown
    jobTitleSelect.addEventListener('change', function() {
        if (this.value) {
            jobTitleInput.value = this.value;
            fetchSkillsForJobTitle(this.value);
        }
    });

    // <CHANGE> Fetch skills when job title input changes
    jobTitleInput.addEventListener('change', function() {
        if (this.value) {
            fetchSkillsForJobTitle(this.value);
        }
    });

    // <CHANGE> Function to fetch skills based on job title
    function fetchSkillsForJobTitle(jobTitle) {
        fetch('{{ route("managejobs.getSkillsByJobTitle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            body: JSON.stringify({ job_title: jobTitle })
        })
        .then(response => response.json())
        .then(data => {
            if (data.skills) {
                skillsInput.value = data.skills;
            }
        })
        .catch(error => console.error('Error fetching skills:', error));
    }

    // Form submission handler
    form.addEventListener('submit', function(e) {
        if (submitBtn.classList.contains('btn-disabled')) {
            e.preventDefault();
            return;
        }

        submitBtn.classList.add('btn-disabled');
        submitText.textContent = 'Processing...';
        submitSpinner.classList.remove('d-none');
        submitBtn.disabled = true;

        setTimeout(() => {
            if (!form.checkValidity()) {
                resetSubmitButton();
            }
        }, 0);
    });

    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            resetSubmitButton();
        }
    });

    function resetSubmitButton() {
        submitBtn.classList.remove('btn-disabled');
        submitText.textContent = 'Submit';
        submitSpinner.classList.add('d-none');
        submitBtn.disabled = false;
    }

    // Number input validation
    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('keydown', function(e) {
            if (e.key === '-' || e.key === 'e' || e.key === 'E' || e.key === '+') {
                e.preventDefault();
            }
        });

        input.addEventListener('input', function() {
            if (this.value < 0) {
                this.value = Math.abs(this.value);
            }
            
            if (this.id === 'vacancies' && (this.value < 1 || this.value > 999)) {
                this.setCustomValidity('Number of vacancies must be between 1 and 999');
            } else if ((this.id === 'salary_from' || this.id === 'salary_to') && this.value < 1000) {
                this.setCustomValidity('Salary must be at least 1000');
            } else {
                this.setCustomValidity('');
            }
        });
    });

    // Date validation
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    startDate.addEventListener('change', function() {
        endDate.min = new Date(new Date(this.value).getTime() + 86400000).toISOString().split('T')[0];
        if (new Date(endDate.value) < new Date(this.value)) {
            endDate.value = '';
        }
    });

    endDate.addEventListener('change', function() {
        if (!this.value) {
            this.setCustomValidity('');
            this.classList.remove('is-invalid', 'is-valid');
            return;
        }

        const startValue = new Date(startDate.value);
        const endValue = new Date(this.value);
        
        if (endValue <= startValue) {
            this.setCustomValidity('Expired date must be after start date');
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
        } else {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });

    // Salary validation
    const salaryFrom = document.getElementById('salary_from');
    const salaryTo = document.getElementById('salary_to');

    salaryFrom.addEventListener('input', validateSalary);
    salaryTo.addEventListener('input', validateSalary);

    function validateSalary() {
        const fromValue = parseFloat(salaryFrom.value) || 0;
        const toValue = parseFloat(salaryTo.value) || 0;
        
        if (toValue > 0 && fromValue > 0 && toValue < fromValue) {
            salaryTo.setCustomValidity('Maximum salary must be greater than or equal to minimum salary');
            salaryTo.classList.add('is-invalid');
            salaryTo.classList.remove('is-valid');
        } else {
            salaryTo.setCustomValidity('');
            salaryTo.classList.remove('is-invalid');
            if (toValue > 0) {
                salaryTo.classList.add('is-valid');
            }
        }
    }

    // Client-side validation
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (input.checkValidity()) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            } else {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
            }
        });
    });
});
</script>
@endsection
