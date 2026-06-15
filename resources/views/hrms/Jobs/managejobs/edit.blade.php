@extends('layouts.index')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h3>Edit Job</h3>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('managejobs.update', $job->id) }}" method="POST" id="jobForm">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <!-- <CHANGE> Reorganized to show Department first for dynamic job title suggestions -->
                            <div class="col-md-6 mb-3">
                                <label for="department" class="form-label">Department</label>
                                <select class="form-control select2" name="department" id="department" required>
                                    <option value="" disabled>Select a Department</option>
                                    @foreach ($department as $dep)
                                        <option value="{{ $dep->department }}" 
                                            {{ old('department', $job->department) == $dep->department ? 'selected' : '' }}>
                                            {{ $dep->department }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- <CHANGE> Updated Job Title field with datalist for suggestions + manual entry -->
                            <div class="col-md-6 mb-3">
                                <label for="job_title" class="form-label">Job Title</label>
                                <input type="text" class="form-control" id="job_title" name="job_title" 
                                       list="job_titles_list"
                                       value="{{ old('job_title', $job->job_title) }}" 
                                       placeholder="Select or enter job title" required>
                                <datalist id="job_titles_list"></datalist>
                                <small class="text-muted">Select from suggestions or enter a custom title</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="job_location" class="form-label">Job Location</label>
                                <input type="text" class="form-control" id="job_location" name="job_location" 
                                       value="{{ old('job_location', $job->job_location) }}" placeholder="Enter location" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="vacancies" class="form-label">No of Vacancies</label>
                                <input type="number" class="form-control" id="vacancies" name="vacancies" 
                                       value="{{ old('vacancies', $job->vacancies) }}" placeholder="Enter number" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="experience" class="form-label">Experience</label>
                                <select class="form-control select2" name="experience" id="experience" required>
                                    <option value="" disabled>Select Experience Level</option>
                                    @foreach ($experiences as $exp)
                                        <option value="{{ $exp->experience }}" @if($exp->experience == $job->experience) selected @endif>
                                            {{ $exp->experience }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- <CHANGE> Updated Skills field with auto-population based on job title -->
                            <div class="col-md-6 mb-3">
                                <label for="skills" class="form-label">Skills (Comma separated)</label>
                                <input type="text" class="form-control" id="skills" name="skills" 
                                       value="{{ old('skills', $job->skills) }}" 
                                       placeholder="e.g. Laravel, React, MySQL">
                                <small class="text-muted">Auto-populated based on job title, but you can edit</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="salary_from" class="form-label">Salary From</label>
                                <input type="number" class="form-control" id="salary_from" name="salary_from" 
                                       value="{{ old('salary_from', $job->salary_from) }}" placeholder="Enter minimum salary" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="salary_to" class="form-label">Salary To</label>
                                <input type="number" class="form-control" id="salary_to" name="salary_to" 
                                       value="{{ old('salary_to', $job->salary_to) }}" placeholder="Enter maximum salary" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="job_type" class="form-label">Job Type</label>
                                <select class="form-select" id="job_type" name="job_type" required>
                                    <option value="" disabled>Select Job Type</option>
                                    <option value="Full Time" {{ old('job_type', $job->job_type) == 'Full Time' ? 'selected' : '' }}>Full Time</option>
                                    <option value="Part Time" {{ old('job_type', $job->job_type) == 'Part Time' ? 'selected' : '' }}>Part Time</option>
                                    <option value="Contract" {{ old('job_type', $job->job_type) == 'Contract' ? 'selected' : '' }}>Contract</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="" disabled>Select Status</option>
                                    <option value="Open" {{ old('status', $job->status) == 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="Closed" {{ old('status', $job->status) == 'Closed' ? 'selected' : '' }}>Closed</option>
                                    <option value="Cancelled" {{ old('status', $job->status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ old('start_date', $job->start_date) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="expired_date" class="form-label">Expired Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ old('end_date', $job->end_date) }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" 
                                          placeholder="Enter job description" required>{{ old('description', $job->description) }}</textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="screening_questions" class="form-label">Screening Questions</label>
                                <textarea class="form-control" id="screening_questions" name="screening_questions" rows="3"
                                          placeholder="Enter screening questions for candidates">{{ old('screening_questions', $job->screening_questions ?? '') }}</textarea>
                            </div>
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary px-5" id="submitBtn">
                                    <span id="submitText">Update</span>
                                    <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                                </button>
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('jobForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitSpinner = document.getElementById('submitSpinner');
    const departmentSelect = document.getElementById('department');
    const jobTitleInput = document.getElementById('job_title');
    const skillsInput = document.getElementById('skills');
    const jobTitlesList = document.getElementById('job_titles_list');

    // <CHANGE> Load job titles when department changes
    departmentSelect.addEventListener('change', function() {
        const department = this.value;
        if (department) {
            fetch(`{{ route('managejobs.getJobTitlesByDepartment') }}?department=${encodeURIComponent(department)}`)
                .then(response => response.json())
                .then(data => {
                    jobTitlesList.innerHTML = '';
                    data.forEach(title => {
                        const option = document.createElement('option');
                        option.value = title;
                        jobTitlesList.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching job titles:', error));
        }
    });

    // <CHANGE> Load skills when job title changes
    jobTitleInput.addEventListener('change', function() {
        const jobTitle = this.value;
        if (jobTitle) {
            fetch(`{{ route('managejobs.getSkillsByJobTitle') }}?job_title=${encodeURIComponent(jobTitle)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.skills) {
                        skillsInput.value = data.skills;
                    }
                })
                .catch(error => console.error('Error fetching skills:', error));
        }
    });

    // Trigger job titles load on page load if department is already selected
    if (departmentSelect.value) {
        departmentSelect.dispatchEvent(new Event('change'));
    }

    // Form submission handler
    form.addEventListener('submit', function(e) {
        if (submitBtn.classList.contains('btn-disabled')) {
            e.preventDefault();
            return;
        }
        
        submitBtn.classList.add('btn-disabled');
        submitText.textContent = 'Updating...';
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
        submitText.textContent = 'Update';
        submitSpinner.classList.add('d-none');
        submitBtn.disabled = false;
    }
});
</script>
@endsection
