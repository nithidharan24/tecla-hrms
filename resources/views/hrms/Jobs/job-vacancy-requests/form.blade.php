@php
    $editing = isset($jobRequest) && $jobRequest;
    $isEmployee = Session::get('role') === 'employee' || Session::get('role') === 'hr';
    $employeeDepartment = $isEmployee ? Session::get('department_name') : null;
    $employeeDepartmentId = $isEmployee ? Session::get('department_id') : null;
    $userRole = Session::get('role');
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ $action }}" method="POST" id="jobRequestForm">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
            
            @if($isEmployee && $employeeDepartmentId)
                <input type="text" class="form-control" value="{{ $employeeDepartment }}" readonly disabled>
                <input type="hidden" name="department" id="department" value="{{ $employeeDepartmentId }}">
                <small class="text-muted">Department is based on your assigned department.</small>
            @else
                <select class="form-control select2 @error('department') is-invalid @enderror" name="department" id="department" required>
                    <option value="" disabled {{ old('department', $editing ? $jobRequest->department : '') ? '' : 'selected' }}>Select a Department</option>
                    @foreach ($department as $dep)
                        <option value="{{ $dep->id }}" {{ old('department', $editing ? $jobRequest->department : '') == $dep->id ? 'selected' : '' }}>
                            {{ $dep->department }}
                        </option>
                    @endforeach
                </select>
            @endif
            @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="job_title" class="form-label">Job Title <span class="text-danger">*</span></label>
            <div class="input-group">
                <select class="form-control" id="job_title_select" name="job_title_select">
                    <option value="">Select from suggestions</option>
                </select>
                <span class="input-group-text">or</span>
            </div>
            <input type="text" class="form-control mt-2 @error('job_title') is-invalid @enderror" id="job_title" name="job_title"
                value="{{ old('job_title', $editing ? $jobRequest->job_title : '') }}" placeholder="Enter custom job title" required minlength="3" maxlength="100">
            @error('job_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="job_location" class="form-label">Job Location </label>
            <input type="text" class="form-control @error('job_location') is-invalid @enderror" id="job_location" name="job_location"
                value="{{ old('job_location', $editing ? $jobRequest->job_location : '') }}" placeholder="Enter location"  minlength="2" maxlength="100">
            @error('job_location')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="vacancies" class="form-label">No of Vacancies <span class="text-danger">*</span></label>
            <input type="number" class="form-control @error('vacancies') is-invalid @enderror" id="vacancies" name="vacancies"
                value="{{ old('vacancies', $editing ? $jobRequest->vacancies : '') }}" placeholder="Enter number" required min="1" max="999">
            @error('vacancies')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="experience" class="form-label">Experience <span class="text-danger">*</span></label>
            <select class="form-control select2 @error('experience') is-invalid @enderror" name="experience" id="experience" required>
                <option value="" disabled {{ old('experience', $editing ? $jobRequest->experience : '') ? '' : 'selected' }}>Select Experience Level</option>
                @foreach ($experiences as $exp)
                    <option value="{{ $exp->experience }}" {{ old('experience', $editing ? $jobRequest->experience : '') == $exp->experience ? 'selected' : '' }}>
                        {{ $exp->experience }}
                    </option>
                @endforeach
            </select>
            @error('experience')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="skills" class="form-label">Skills (Comma separated) <span class="text-danger">*</span></label>
            <textarea class="form-control @error('skills') is-invalid @enderror" id="skills" name="skills" rows="2"
                placeholder="Skills will auto-populate based on job title" required maxlength="500">{{ old('skills', $editing ? $jobRequest->skills : '') }}</textarea>
            @error('skills')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="salary_from" class="form-label">Salary From </label>
            <input type="number" class="form-control @error('salary_from') is-invalid @enderror" id="salary_from" name="salary_from"
                value="{{ old('salary_from', $editing ? $jobRequest->salary_from : '') }}" placeholder="Enter minimum salary"  min="1000" max="9999999">
            @error('salary_from')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="salary_to" class="form-label">Salary To </label>
            <input type="number" class="form-control @error('salary_to') is-invalid @enderror" id="salary_to" name="salary_to"
                value="{{ old('salary_to', $editing ? $jobRequest->salary_to : '') }}" placeholder="Enter maximum salary" min="1000" max="9999999">
            @error('salary_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="job_type" class="form-label">Job Type </label>
            <select class="form-select @error('job_type') is-invalid @enderror" id="job_type" name="job_type" >
                <option value="" disabled {{ old('job_type', $editing ? $jobRequest->job_type : '') ? '' : 'selected' }}>Select Job Type</option>
                @foreach(['Full Time', 'Part Time', 'Contract'] as $type)
                    <option value="{{ $type }}" {{ old('job_type', $editing ? $jobRequest->job_type : '') == $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
            @error('job_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="status" class="form-label">Job Status </label>
            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" >
                <option value="" disabled {{ old('status', $editing ? $jobRequest->status : '') ? '' : 'selected' }}>Select Status</option>
                @foreach(['Open', 'Closed', 'Cancelled'] as $status)
                    <option value="{{ $status }}" {{ old('status', $editing ? $jobRequest->status : '') == $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="start_date" class="form-label">Start Date </label>
            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date"
                value="{{ old('start_date', $editing ? $jobRequest->start_date : '') }}">
            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="end_date" class="form-label">Expired Date</label>
            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date"
                value="{{ old('end_date', $editing ? $jobRequest->end_date : '') }}">
            @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-12 mb-3">
            <label for="description" class="form-label">Description </label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3"
                placeholder="Enter job description"  minlength="10" maxlength="2000">{{ old('description', $editing ? $jobRequest->description : '') }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-12 mb-3">
            <label for="screening_questions" class="form-label">Screening Questions</label>
            <textarea class="form-control @error('screening_questions') is-invalid @enderror" id="screening_questions" name="screening_questions" rows="3"
                placeholder="Enter screening questions for candidates">{{ old('screening_questions', $editing ? ($jobRequest->screening_questions ?? '') : '') }}</textarea>
            @error('screening_questions')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-primary px-5" id="submitBtn">
                <span id="submitText">{{ $buttonText }}</span>
                <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
            </button>
            <a href="{{ route('recruitment.index', ['tab' => 'job-requests']) }}" class="btn btn-secondary px-5 ms-2">Cancel</a>
        </div>
    </div>
</form>

<style>
    .btn-disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('jobRequestForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitSpinner = document.getElementById('submitSpinner');
    const departmentSelect = document.getElementById('department');
    const jobTitleSelect = document.getElementById('job_title_select');
    const jobTitleInput = document.getElementById('job_title');
    const skillsInput = document.getElementById('skills');
    const salaryFrom = document.getElementById('salary_from');
    const salaryTo = document.getElementById('salary_to');

    function loadJobTitles(department) {
        if (!department) {
            jobTitleSelect.innerHTML = '<option value="">Select from suggestions</option>';
            return;
        }

        fetch('{{ route("job-vacancy-requests.getJobTitlesByDepartment") }}', {
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
    }

    function fetchSkillsForJobTitle(jobTitle) {
        fetch('{{ route("job-vacancy-requests.getSkillsByJobTitle") }}', {
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

    const departmentElem = document.getElementById('department');
    if (departmentElem) {
        // For admin dropdown
        if (departmentElem.tagName === 'SELECT') {
            departmentElem.addEventListener('change', function() {
                loadJobTitles(this.value);
            });
            
            if (departmentElem.value) {
                loadJobTitles(departmentElem.value);
            }
        } 
        // For employee hidden input
        else if (departmentElem.tagName === 'INPUT' && departmentElem.type === 'hidden') {
            if (departmentElem.value) {
                loadJobTitles(departmentElem.value);
            }
        }
    }

    jobTitleSelect.addEventListener('change', function() {
        if (this.value) {
            jobTitleInput.value = this.value;
            fetchSkillsForJobTitle(this.value);
        }
    });

    jobTitleInput.addEventListener('change', function() {
        if (this.value) {
            fetchSkillsForJobTitle(this.value);
        }
    });

    salaryFrom.addEventListener('input', validateSalary);
    salaryTo.addEventListener('input', validateSalary);

    function validateSalary() {
        const fromValue = parseFloat(salaryFrom.value) || 0;
        const toValue = parseFloat(salaryTo.value) || 0;

        if (toValue > 0 && fromValue > 0 && toValue < fromValue) {
            salaryTo.setCustomValidity('Maximum salary must be greater than or equal to minimum salary');
        } else {
            salaryTo.setCustomValidity('');
        }
    }

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
                submitBtn.classList.remove('btn-disabled');
                submitText.textContent = '{{ $buttonText }}';
                submitSpinner.classList.add('d-none');
                submitBtn.disabled = false;
            }
        }, 0);
    });
});
</script> 