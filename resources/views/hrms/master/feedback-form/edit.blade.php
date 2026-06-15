@extends('layouts.index')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .edit-container {
        min-height: 100vh;
         background: linear-gradient(135deg, #fafafa 0%, #dddddd 100%); /* Bluish-silver background */
        padding: 2rem 1rem;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .back-button {
        max-width: 800px;
        margin: 0 auto 2rem;
        opacity: 0;
        transform: translateX(-20px);
        animation: slideInLeft 0.6s ease-out forwards;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.8rem 1.5rem;
        background: rgba(59, 130, 246, 0.1); /* Light blue tint */
        color: #3b82f6; /* Blue text */
        text-decoration: none;
        border-radius: 50px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(59, 130, 246, 0.2); /* Light blue border */
        transition: all 0.3s ease;
        font-weight: 600;
    }

    .btn-back:hover {
        background: rgba(59, 130, 246, 0.15); /* Slightly darker blue tint on hover */
        transform: translateX(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .edit-form {
        max-width: 800px;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 3rem;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        transform: translateY(20px);
        opacity: 0;
        animation: slideUp 0.8s ease-out 0.2s forwards;
    }

    @keyframes slideUp {
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes slideInLeft {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .form-header {
        text-align: center;
        margin-bottom: 3rem;
        position: relative;
    }

    .form-header::after {
        content: '';
        position: absolute;
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #2563eb); /* Blue gradient */
        border-radius: 2px;
        animation: expandLine 1s ease-out 0.8s both;
    }

    @keyframes expandLine {
        from {
            width: 0;
        }
        to {
            width: 80px;
        }
    }

    .form-title {
        font-size: 2.5rem;
        color: #2d3748; /* Dark text */
        margin-bottom: 0.5rem;
        font-weight: 700;
    }

    .form-subtitle {
        color: #718096; /* Muted dark text */
        font-size: 1.1rem;
        font-weight: 400;
    }

    .employee-info {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.05)); /* Light blue tint */
        padding: 1.5rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        border: 2px solid rgba(59, 130, 246, 0.2); /* Light blue border */
    }

    .employee-name {
        font-size: 1.3rem;
        font-weight: 700;
        color: #3b82f6; /* Blue text */
        margin-bottom: 0.5rem;
    }

    .employee-dept {
        color: #718096;
        font-weight: 500;
    }

    .form-group {
        margin-bottom: 2rem;
        opacity: 0;
        transform: translateX(-20px);
        animation: slideInLeft 0.6s ease-out forwards;
    }

    .form-group:nth-child(3) {
        animation-delay: 0.1s;
    }
    .form-group:nth-child(4) {
        animation-delay: 0.2s;
    }
    .form-group:nth-child(5) {
        animation-delay: 0.3s;
    }
    .form-group:nth-child(6) {
        animation-delay: 0.4s;
    }
    .form-group:nth-child(7) {
        animation-delay: 0.5s;
    }
    .form-group:nth-child(8) {
        animation-delay: 0.6s;
    }
    .form-group:nth-child(9) {
        animation-delay: 0.7s;
    }
    .form-group:nth-child(10) {
        animation-delay: 0.8s;
    }

    .form-label {
        display: block;
        margin-bottom: 0.8rem;
        font-weight: 600;
        color: #2d3748; /* Dark text */
        font-size: 1.1rem;
        position: relative;
    }

    .required {
        color: #e53e3e;
        margin-left: 4px;
    }

    .rating-container {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .rating-group {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .rating-input {
        display: none;
    }

    .rating-label {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 600;
        color: #64748b;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .rating-label::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        transition: left 0.5s;
    }

    .rating-label:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3); /* Blue shadow on hover */
    }

    .rating-label:hover::before {
        left: 100%;
    }

    .rating-input:checked + .rating-label {
        background: linear-gradient(135deg, #3b82f6, #2563eb); /* Blue gradient when checked */
        color: white;
        transform: scale(1.1);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4); /* Stronger blue shadow */
        border-color: #3b82f6; /* Blue border */
    }

    .rating-scale {
        display: flex;
        justify-content: space-between;
        width: 100%;
        margin-top: 0.5rem;
        font-size: 0.9rem;
        color: #718096;
    }

    .form-input, .form-textarea, .form-select {
        width: 100%;
        padding: 1rem 1.5rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f8fafc;
        color: #2d3748;
    }

    .form-input:focus, .form-textarea:focus, .form-select:focus {
        outline: none;
        border-color: #3b82f6; /* Blue border on focus */
        background: white;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); /* Light blue shadow on focus */
        transform: translateY(-2px);
    }

    .form-textarea {
        min-height: 120px;
        resize: vertical;
        font-family: inherit;
    }

    .radio-group {
        display: flex;
        gap: 2rem;
        margin-top: 0.5rem;
    }

    .radio-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        padding: 0.8rem 1.5rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        border: 2px solid #e2e8f0;
        background: #f8fafc;
    }

    .radio-item:hover {
        background: #e2e8f0;
        transform: translateY(-2px);
    }

    .radio-input {
        display: none;
    }

    .radio-custom {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #cbd5e0;
        position: relative;
        transition: all 0.3s ease;
    }

    .radio-input:checked + .radio-item {
        background: linear-gradient(135deg, #3b82f6, #2563eb); /* Blue gradient when checked */
        color: white;
        border-color: #3b82f6; /* Blue border */
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3); /* Blue shadow */
    }

    .radio-input:checked + .radio-item .radio-custom {
        border-color: white;
        background: white;
    }

    .radio-input:checked + .radio-item .radio-custom::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #3b82f6; /* Blue dot */
    }

    .submit-container {
        text-align: center;
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 1px solid #e2e8f0;
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn {
        padding: 1rem 3rem;
        border: none;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        min-width: 180px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn:hover::before {
        left: 100%;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); /* Blue gradient */
        color: white;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3); /* Blue shadow */
    }

    .btn-secondary {
        background: linear-gradient(135deg, #6b7280, #4b5563); /* Keep grey for secondary */
        color: white;
        box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
    }

    .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }

    .btn:active {
        transform: translateY(-1px);
    }

    .progress-bar {
        position: fixed;
        top: 0;
        left: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #2563eb); /* Blue gradient */
        transition: width 0.3s ease;
        z-index: 1000;
    }

    .alert {
        max-width: 800px;
        margin: 0 auto 2rem;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        font-weight: 500;
        opacity: 0;
        transform: translateY(-10px);
        animation: slideDown 0.5s ease-out forwards;
    }

    @keyframes slideDown {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .alert-success {
        background: rgba(16, 185, 129, 0.1); /* Light green tint */
        color: #059669; /* Green text */
        border: 1px solid rgba(16, 185, 129, 0.2); /* Light green border */
    }

    .alert-error {
        background: rgba(239, 68, 68, 0.1); /* Light red tint */
        color: #dc2626; /* Red text */
        border: 1px solid rgba(239, 68, 68, 0.2); /* Light red border */
    }

    .error-list {
        list-style: none;
        margin-top: 0.5rem;
    }

    .error-list li {
        margin-bottom: 0.3rem;
    }

    @media (max-width: 768px) {
        .edit-form {
            padding: 2rem 1.5rem;
            margin: 1rem;
        }

        .form-title {
            font-size: 2rem;
        }

        .rating-container {
            justify-content: center;
        }

        .radio-group {
            flex-direction: column;
            gap: 1rem;
        }

        .rating-label {
            width: 45px;
            height: 45px;
        }

        .submit-container {
            flex-direction: column;
        }
    }

    .floating-shapes {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: -1;
    }

    .shape {
        position: absolute;
        background: rgba(59, 130, 246, 0.1); /* Light blue tint for shapes */
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    .shape:nth-child(1) {
        width: 80px;
        height: 80px;
        top: 20%;
        left: 10%;
        animation-delay: 0s;
    }

    .shape:nth-child(2) {
        width: 120px;
        height: 120px;
        top: 60%;
        right: 10%;
        animation-delay: 2s;
    }

    .shape:nth-child(3) {
        width: 60px;
        height: 60px;
        bottom: 20%;
        left: 20%;
        animation-delay: 4s;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px) rotate(0deg);
        }
        50% {
            transform: translateY(-20px) rotate(180deg);
        }
    }
</style>

<div class="floating-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
</div>

<div class="progress-bar" id="progressBar"></div>

<div class="edit-container">
    <div class="back-button">
        <a href="{{ route('feedback.show', $feedback->id) }}" class="btn-back">
            ← Back to Feedback Details
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <strong>Please correct the following errors:</strong>
            <ul class="error-list">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="edit-form" action="{{ route('feedback.update', $feedback->id) }}" method="POST" id="editForm">
        @csrf
        @method('PUT')

        <div class="form-header">
            <h1 class="form-title">Edit Employee Feedback</h1>
            <p class="form-subtitle">Update the feedback information below</p>
        </div>

        <div class="employee-info">
            <div class="employee-name">{{ $feedback->employee_name }}</div>
            <div class="employee-dept">{{ ucfirst($feedback->department) }} Department</div>
        </div>

        <div class="form-group">
            <label class="form-label" for="employee_name">
                Employee Name <span class="required">*</span>
            </label>
            <input type="text" id="employee_name" name="employee_name" class="form-input" value="{{ old('employee_name', $feedback->employee_name) }}" required>
        </div>

        <div class="form-group">
            <label for="department">Department <span class="text-danger">*</span></label>
            <select id="department" name="department" class="form-control" required>
                <option value="">Select Department</option>
                @foreach($departments as $department)
                <option value="{{ $department->id }}" 
                    {{ old('department', $feedback->department) == $department->id ? 'selected' : '' }}>
                    {{ $department->department }}
                </option>
                
                @endforeach
            </select>
            <div class="invalid-feedback">Please select a department.</div>
            @error('department')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">
                Overall Job Satisfaction <span class="required">*</span>
            </label>
            <div class="rating-container">
                <div class="rating-group">
                    @for($i = 1; $i <= 5; $i++)
                        <input type="radio" id="job_satisfaction_{{ $i }}" name="job_satisfaction" value="{{ $i }}" class="rating-input" {{ old('job_satisfaction', $feedback->job_satisfaction) == $i ? 'checked' : '' }} required>
                        <label for="job_satisfaction_{{ $i }}" class="rating-label">{{ $i }}</label>
                    @endfor
                </div>
            </div>
            <div class="rating-scale">
                <span>Very Dissatisfied</span>
                <span>Very Satisfied</span>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">
                Work Environment <span class="required">*</span>
            </label>
            <div class="rating-container">
                <div class="rating-group">
                    @for($i = 1; $i <= 5; $i++)
                        <input type="radio" id="work_environment_{{ $i }}" name="work_environment" value="{{ $i }}" class="rating-input" {{ old('work_environment', $feedback->work_environment) == $i ? 'checked' : '' }} required>
                        <label for="work_environment_{{ $i }}" class="rating-label">{{ $i }}</label>
                    @endfor
                </div>
            </div>
            <div class="rating-scale">
                <span>Poor</span>
                <span>Excellent</span>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">
                Manager Support <span class="required">*</span>
            </label>
            <div class="rating-container">
                <div class="rating-group">
                    @for($i = 1; $i <= 5; $i++)
                        <input type="radio" id="manager_support_{{ $i }}" name="manager_support" value="{{ $i }}" class="rating-input" {{ old('manager_support', $feedback->manager_support) == $i ? 'checked' : '' }} required>
                        <label for="manager_support_{{ $i }}" class="rating-label">{{ $i }}</label>
                    @endfor
                </div>
            </div>
            <div class="rating-scale">
                <span>Poor</span>
                <span>Excellent</span>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">
                Opportunities for Growth <span class="required">*</span>
            </label>
            <div class="rating-container">
                <div class="rating-group">
                    @for($i = 1; $i <= 5; $i++)
                        <input type="radio" id="growth_opportunities_{{ $i }}" name="growth_opportunities" value="{{ $i }}" class="rating-input" {{ old('growth_opportunities', $feedback->growth_opportunities) == $i ? 'checked' : '' }} required>
                        <label for="growth_opportunities_{{ $i }}" class="rating-label">{{ $i }}</label>
                    @endfor
                </div>
            </div>
            <div class="rating-scale">
                <span>Limited</span>
                <span>Abundant</span>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="suggestions">
                Suggestions for Improvement
            </label>
            <textarea id="suggestions" name="suggestions" class="form-textarea" placeholder="Please share your suggestions for improving our workplace...">{{ old('suggestions', $feedback->suggestions) }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">
                Would you recommend this company? <span class="required">*</span>
            </label>
            <div class="radio-group">
                <input type="radio" id="recommend_yes" name="recommend_company" value="yes" class="radio-input" {{ old('recommend_company', $feedback->recommend_company) == 'yes' ? 'checked' : '' }} required>
                <label for="recommend_yes" class="radio-item">
                    <div class="radio-custom"></div>
                    <span>Yes</span>
                </label>

                <input type="radio" id="recommend_no" name="recommend_company" value="no" class="radio-input" {{ old('recommend_company', $feedback->recommend_company) == 'no' ? 'checked' : '' }} required>
                <label for="recommend_no" class="radio-item">
                    <div class="radio-custom"></div>
                    <span>No</span>
                </label>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="additional_comments">
                Additional Comments
            </label>
            <textarea id="additional_comments" name="additional_comments" class="form-textarea" placeholder="Any additional feedback you'd like to share...">{{ old('additional_comments', $feedback->additional_comments) }}</textarea>
        </div>

        <div class="submit-container">
            <button type="submit" class="btn btn-primary">
                💾 Update Feedback
            </button>
            <a href="{{ route('feedback.show', $feedback->id) }}" class="btn btn-secondary">
                ❌ Cancel
            </a>
        </div>
    </form>
</div>

<script>
     // Department and Designation AJAX
     $('#department').on('change', function() {
        var departmentId = $(this).val();
      
        $('#designation').html('<option value="">Loading...</option>');
        
        if (departmentId) {
            $.ajax({
                url: '{{ url("/get-designations") }}/' + departmentId,
                type: 'GET',
                success: function(res) {
                    $('#designation').empty().append('<option value="">Select Designation</option>');
                    $.each(res, function(key, designation) {
                        var selected = (designation.id == currentDesignation) ? 'selected' : '';
                        $('#designation').append('<option value="' + designation.id + '" ' + selected + '>' + designation.designation + '</option>');
                    });
                },
                error: function() {
                    $('#designation').html('<option value="">Error loading designations</option>');
                }
            });
        } else {
            $('#designation').html('<option value="">Select Designation</option>');
        }
    });

    // Trigger department change on page load to populate designations
    if ($('#department').val()) {
        $('#department').trigger('change');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editForm');
        const progressBar = document.getElementById('progressBar');
        const formGroups = document.querySelectorAll('.form-group');

        // Progress bar functionality
        function updateProgress() {
            const requiredFields = form.querySelectorAll('[required]');
            let filledFields = 0;

            requiredFields.forEach(field => {
                if (field.type === 'radio') {
                    if (form.querySelector(`input[name="${field.name}"]:checked`)) {
                        filledFields++;
                    }
                } else if (field.value.trim() !== '') {
                    filledFields++;
                }
            });

            const progress = (filledFields / requiredFields.length) * 100;
            progressBar.style.width = progress + '%';
        }

        // Initialize progress bar
        updateProgress();

        // Add event listeners to all form inputs
        form.addEventListener('input', updateProgress);
        form.addEventListener('change', updateProgress);

        // Form submission with animation
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('.btn-primary');
            submitBtn.innerHTML = '<span style="display: inline-block; animation: spin 1s linear infinite;">⟳</span> Updating...';
            submitBtn.disabled = true;
        });

        // Smooth scroll to first error on validation
        form.addEventListener('invalid', function(e) {
            e.target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, true);

        // Add click animation to rating labels
        const ratingLabels = document.querySelectorAll('.rating-label');
        ratingLabels.forEach(label => {
            label.addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });
    });

    // CSS animation for spinning
    const style = document.createElement('style');
    style.textContent = `
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
</script>
@endsection
