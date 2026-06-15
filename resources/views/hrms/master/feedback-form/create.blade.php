@extends('layouts.index')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .feedback-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #fafafa 0%, #dddddd 100%);
        padding: 2rem 1rem;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .feedback-form {
        max-width: 800px;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 3rem;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        transform: translateY(20px);
        opacity: 0;
        animation: slideUp 0.8s ease-out forwards;
    }

    @keyframes slideUp {
        to {
            transform: translateY(0);
            opacity: 1;
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
        background: linear-gradient(90deg, #3b82f6, #2563eb);
        border-radius: 2px;
        animation: expandLine 1s ease-out 0.5s both;
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
        color: #2d3748;
        margin-bottom: 0.5rem;
        font-weight: 700;
    }

    .form-subtitle {
        color: #718096;
        font-size: 1.1rem;
        font-weight: 400;
    }

    .employee-welcome {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.05));
        padding: 1.5rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        border: 2px solid rgba(59, 130, 246, 0.2);
        text-align: center;
    }

    .welcome-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #3b82f6;
        margin-bottom: 0.5rem;
    }

    .welcome-subtitle {
        color: #718096;
        font-weight: 500;
    }

    .form-group {
        margin-bottom: 2rem;
        opacity: 0;
        transform: translateX(-20px);
        animation: slideInLeft 0.6s ease-out forwards;
    }

    .form-group:nth-child(2) {
        animation-delay: 0.1s;
    }
    .form-group:nth-child(3) {
        animation-delay: 0.2s;
    }
    .form-group:nth-child(4) {
        animation-delay: 0.3s;
    }
    .form-group:nth-child(5) {
        animation-delay: 0.4s;
    }
    .form-group:nth-child(6) {
        animation-delay: 0.5s;
    }
    .form-group:nth-child(7) {
        animation-delay: 0.6s;
    }
    .form-group:nth-child(8) {
        animation-delay: 0.7s;
    }

    @keyframes slideInLeft {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .form-label {
        display: block;
        margin-bottom: 0.8rem;
        font-weight: 600;
        color: #2d3748;
        font-size: 1.1rem;
        position: relative;
    }

    .required {
        color: #e53e3e;
        margin-left: 4px;
    }

    .auto-fill-notice {
        color: #3b82f6;
        font-size: 0.9rem;
        margin-top: 0.5rem;
        display: block;
        font-weight: 500;
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
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
    }

    .rating-label:hover::before {
        left: 100%;
    }

    .rating-input:checked + .rating-label {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        transform: scale(1.1);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        border-color: #3b82f6;
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
        border-color: #3b82f6;
        background: white;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        transform: translateY(-2px);
    }

    .form-input[readonly] {
        background: #f1f5f9;
        color: #64748b;
        cursor: not-allowed;
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
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border-color: #3b82f6;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
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
        background: #3b82f6;
    }

    .submit-container {
        text-align: center;
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 1px solid #e2e8f0;
    }

    .submit-btn {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        padding: 1rem 3rem;
        border: none;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        min-width: 200px;
    }

    .submit-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(59, 130, 246, 0.4);
    }

    .submit-btn:hover::before {
        left: 100%;
    }

    .submit-btn:active {
        transform: translateY(-1px);
    }

    .progress-bar {
        position: fixed;
        top: 0;
        left: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #2563eb);
        transition: width 0.3s ease;
        z-index: 1000;
    }

    @media (max-width: 768px) {
        .feedback-form {
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
        background: rgba(59, 130, 246, 0.1);
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

<div class="feedback-container">
    <form class="feedback-form" action="{{ route('feedback.store') }}" method="POST" id="feedbackForm">
        @csrf

        <div class="form-header">
            <h1 class="form-title">Employee Feedback Form</h1>
            <p class="form-subtitle">Your feedback helps us create a better workplace for everyone</p>
        </div>

        <!-- Employee Welcome Section -->
        @if($employeeName)
        <div class="employee-welcome">
            <div class="welcome-title">👋 Welcome, {{ $employeeName }}</div>
            <div class="welcome-subtitle">
                @if(isset($employee) && $employee->employeeid)
                    Employee ID: {{ $employee->employeeid }}
                @endif
            </div>
        </div>
        @endif

        <!-- Employee Name Field -->
        <div class="form-group">
            <label class="form-label" for="employee_name">
                Employee Name <span class="required">*</span>
            </label>
            <input type="text" 
                   id="employee_name" 
                   name="employee_name" 
                   class="form-input" 
                   value="{{ old('employee_name', $employeeName) }}" 
                   {{ $employeeName ? 'readonly' : '' }} 
                   required
                   placeholder="Enter your full name">
            
            @if($employeeName)
            <span class="auto-fill-notice">
                🔒 Your name is automatically filled from your employee profile
            </span>
            @endif
            
            @error('employee_name')
                <div class="text-danger" style="color: #e53e3e; font-size: 0.9rem; margin-top: 0.5rem;">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Department Field -->
        <div class="form-group">
            <label class="form-label" for="department">
                Department <span class="required">*</span>
            </label>
            <select id="department" name="department" class="form-select" required>
                <option value="">Select Department</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" 
                        {{ (old('department') == $department->id || (isset($employeeDepartment) && $employeeDepartment == $department->id)) ? 'selected' : '' }}>
                        {{ $department->department }}
                    </option>
                @endforeach
            </select>
            
            @if(isset($employeeDepartment) && $employeeDepartment)
            <span class="auto-fill-notice">
                📁 Your department is pre-selected based on your profile
            </span>
            @endif
            
            @error('department')
                <div class="text-danger" style="color: #e53e3e; font-size: 0.9rem; margin-top: 0.5rem;">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Job Satisfaction -->
        <div class="form-group">
            <label class="form-label">
                Overall Job Satisfaction <span class="required">*</span>
            </label>
            <div class="rating-container">
                <div class="rating-group">
                    @for($i = 1; $i <= 5; $i++)
                        <input type="radio" 
                               id="job_satisfaction_{{ $i }}" 
                               name="job_satisfaction" 
                               value="{{ $i }}" 
                               class="rating-input" 
                               {{ old('job_satisfaction') == $i ? 'checked' : '' }}
                               required>
                        <label for="job_satisfaction_{{ $i }}" class="rating-label">{{ $i }}</label>
                    @endfor
                </div>
            </div>
            <div class="rating-scale">
                <span>Very Dissatisfied</span>
                <span>Very Satisfied</span>
            </div>
            @error('job_satisfaction')
                <div class="text-danger" style="color: #e53e3e; font-size: 0.9rem; margin-top: 0.5rem;">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Work Environment -->
        <div class="form-group">
            <label class="form-label">
                Work Environment <span class="required">*</span>
            </label>
            <div class="rating-container">
                <div class="rating-group">
                    @for($i = 1; $i <= 5; $i++)
                        <input type="radio" 
                               id="work_environment_{{ $i }}" 
                               name="work_environment" 
                               value="{{ $i }}" 
                               class="rating-input" 
                               {{ old('work_environment') == $i ? 'checked' : '' }}
                               required>
                        <label for="work_environment_{{ $i }}" class="rating-label">{{ $i }}</label>
                    @endfor
                </div>
            </div>
            <div class="rating-scale">
                <span>Poor</span>
                <span>Excellent</span>
            </div>
            @error('work_environment')
                <div class="text-danger" style="color: #e53e3e; font-size: 0.9rem; margin-top: 0.5rem;">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Manager Support -->
        <div class="form-group">
            <label class="form-label">
                Manager Support <span class="required">*</span>
            </label>
            <div class="rating-container">
                <div class="rating-group">
                    @for($i = 1; $i <= 5; $i++)
                        <input type="radio" 
                               id="manager_support_{{ $i }}" 
                               name="manager_support" 
                               value="{{ $i }}" 
                               class="rating-input" 
                               {{ old('manager_support') == $i ? 'checked' : '' }}
                               required>
                        <label for="manager_support_{{ $i }}" class="rating-label">{{ $i }}</label>
                    @endfor
                </div>
            </div>
            <div class="rating-scale">
                <span>Poor</span>
                <span>Excellent</span>
            </div>
            @error('manager_support')
                <div class="text-danger" style="color: #e53e3e; font-size: 0.9rem; margin-top: 0.5rem;">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Growth Opportunities -->
        <div class="form-group">
            <label class="form-label">
                Opportunities for Growth <span class="required">*</span>
            </label>
            <div class="rating-container">
                <div class="rating-group">
                    @for($i = 1; $i <= 5; $i++)
                        <input type="radio" 
                               id="growth_opportunities_{{ $i }}" 
                               name="growth_opportunities" 
                               value="{{ $i }}" 
                               class="rating-input" 
                               {{ old('growth_opportunities') == $i ? 'checked' : '' }}
                               required>
                        <label for="growth_opportunities_{{ $i }}" class="rating-label">{{ $i }}</label>
                    @endfor
                </div>
            </div>
            <div class="rating-scale">
                <span>Limited</span>
                <span>Abundant</span>
            </div>
            @error('growth_opportunities')
                <div class="text-danger" style="color: #e53e3e; font-size: 0.9rem; margin-top: 0.5rem;">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Suggestions -->
        <div class="form-group">
            <label class="form-label" for="suggestions">
                Suggestions for Improvement
            </label>
            <textarea id="suggestions" 
                      name="suggestions" 
                      class="form-textarea" 
                      placeholder="Please share your suggestions for improving our workplace...">{{ old('suggestions') }}</textarea>
            @error('suggestions')
                <div class="text-danger" style="color: #e53e3e; font-size: 0.9rem; margin-top: 0.5rem;">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Recommend Company -->
        <div class="form-group">
            <label class="form-label">
                Would you recommend this company? <span class="required">*</span>
            </label>
            <div class="radio-group">
                <input type="radio" 
                       id="recommend_yes" 
                       name="recommend_company" 
                       value="yes" 
                       class="radio-input" 
                       {{ old('recommend_company') == 'yes' ? 'checked' : '' }}
                       required>
                <label for="recommend_yes" class="radio-item">
                    <div class="radio-custom"></div>
                    <span>Yes</span>
                </label>

                <input type="radio" 
                       id="recommend_no" 
                       name="recommend_company" 
                       value="no" 
                       class="radio-input" 
                       {{ old('recommend_company') == 'no' ? 'checked' : '' }}
                       required>
                <label for="recommend_no" class="radio-item">
                    <div class="radio-custom"></div>
                    <span>No</span>
                </label>
            </div>
            @error('recommend_company')
                <div class="text-danger" style="color: #e53e3e; font-size: 0.9rem; margin-top: 0.5rem;">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Additional Comments -->
        <div class="form-group">
            <label class="form-label" for="additional_comments">
                Additional Comments
            </label>
            <textarea id="additional_comments" 
                      name="additional_comments" 
                      class="form-textarea" 
                      placeholder="Any additional feedback you'd like to share...">{{ old('additional_comments') }}</textarea>
            @error('additional_comments')
                <div class="text-danger" style="color: #e53e3e; font-size: 0.9rem; margin-top: 0.5rem;">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="submit-container">
            <button type="submit" class="submit-btn">
                Submit Feedback
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('feedbackForm');
        const progressBar = document.getElementById('progressBar');

        // Progress bar functionality
        function updateProgress() {
            const requiredFields = form.querySelectorAll('[required]');
            let filledFields = 0;

            requiredFields.forEach(field => {
                if (field.type === 'radio') {
                    const radioGroup = form.querySelectorAll(`input[name="${field.name}"]`);
                    let isChecked = false;
                    radioGroup.forEach(radio => {
                        if (radio.checked) isChecked = true;
                    });
                    if (isChecked) filledFields++;
                } else if (field.type === 'select-one') {
                    if (field.value !== '') filledFields++;
                } else if (field.value.trim() !== '') {
                    filledFields++;
                }
            });

            const progress = (filledFields / requiredFields.length) * 100;
            progressBar.style.width = progress + '%';
        }

        // Initialize progress
        updateProgress();

        // Add event listeners
        form.addEventListener('input', updateProgress);
        form.addEventListener('change', updateProgress);

        // Form submission animation
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('.submit-btn');
            submitBtn.innerHTML = '<span style="display: inline-block; animation: spin 1s linear infinite;">⟳</span> Submitting...';
            submitBtn.disabled = true;
        });

        // Smooth scroll to errors
        const firstError = form.querySelector('.text-danger');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    // Add spin animation
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