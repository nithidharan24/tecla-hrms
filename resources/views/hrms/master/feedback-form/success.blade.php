@extends('layouts.index')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .success-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #e0f2f7 0%, #cce7f0 100%); /* Bluish-silver background */
        padding: 2rem 1rem;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .success-card {
        max-width: 600px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 3rem;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        text-align: center;
        transform: translateY(20px);
        opacity: 0;
        animation: slideUp 0.8s ease-out forwards;
        position: relative;
        z-index: 1;
        overflow: hidden;
    }

    @keyframes slideUp {
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .success-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #2563eb); /* Blue gradient */
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .success-card:hover::before {
        transform: scaleX(1);
    }

    .success-icon {
        background: linear-gradient(135deg, #3b82f6, #2563eb); /* Blue gradient */
        color: white;
        border-radius: 50%;
        width: 100px;
        height: 100px;
        margin: 0 auto 2rem;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 3rem;
        box-shadow: 0 15px 30px rgba(59, 130, 246, 0.3); /* Blue shadow */
        position: relative;
        animation: popIn 0.5s ease-out forwards;
    }

    .success-icon::after {
        content: '';
        position: absolute;
        top: -5px;
        left: -5px;
        width: 110px;
        height: 110px;
        border: 3px solid rgba(59, 130, 246, 0.3); /* Light blue border */
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes popIn {
        from {
            transform: scale(0);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.7;
        }
    }

    .success-title {
        font-size: 2.5rem;
        color: #2d3748; /* Dark text */
        margin-bottom: 1rem;
        font-weight: 700;
        animation: fadeIn 1s ease-out 0.5s forwards;
    }

    .success-message {
        color: #718096; /* Muted dark text */
        font-size: 1.1rem;
        margin-bottom: 2rem;
        animation: fadeIn 1s ease-out 0.7s forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .feedback-summary {
        background: #e0e7ff; /* Light blue background */
        border: 2px solid #c3dafe; /* Light blue border */
        padding: 1.5rem;
        border-radius: 15px;
        margin-top: 2rem;
        margin-bottom: 2rem;
        text-align: left;
        animation: slideUp 0.8s ease-out 0.9s forwards;
    }

    .summary-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #3b82f6; /* Blue text */
        margin-bottom: 0.8rem;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px dashed #a7c5f2; /* Lighter blue dashed border */
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .summary-label {
        color: #4a5568;
        font-weight: 500;
    }

    .summary-value {
        color: #3b82f6; /* Blue text */
        font-weight: 600;
    }

    .btn-success {
        background: linear-gradient(135deg, #3b82f6, #2563eb); /* Blue gradient */
        color: white;
        padding: 1rem 2.5rem;
        border: none;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3); /* Blue shadow */
        animation: fadeIn 1s ease-out 1.1s forwards;
    }

    .btn-success:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(59, 130, 246, 0.4); /* Stronger blue shadow */
    }

    .floating-elements {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: -1;
    }

    .floating-shape {
        position: absolute;
        background: rgba(59, 130, 246, 0.1); /* Light blue tint for shapes */
        border-radius: 50%;
        animation: float 8s ease-in-out infinite;
    }

    .floating-shape:nth-child(1) {
        width: 100px;
        height: 100px;
        top: 10%;
        left: 5%;
        animation-delay: 0s;
    }

    .floating-shape:nth-child(2) {
        width: 150px;
        height: 150px;
        top: 70%;
        right: 5%;
        animation-delay: 3s;
    }

    .floating-shape:nth-child(3) {
        width: 80px;
        height: 80px;
        bottom: 10%;
        left: 15%;
        animation-delay: 6s;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px) rotate(0deg);
        }
        50% {
            transform: translateY(-30px) rotate(180deg);
        }
    }

    .confetti-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        overflow: hidden;
        z-index: 0;
    }

    .confetti {
        position: absolute;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        animation: fall 5s linear infinite;
        opacity: 0;
    }

    .confetti:nth-child(odd) {
        background: #3b82f6; /* Blue */
    }

    .confetti:nth-child(even) {
        background: #2563eb; /* Darker Blue */
    }

    .confetti:nth-child(3n) {
        background: #10b981; /* Green */
    }

    .confetti:nth-child(4n) {
        background: #f59e0b; /* Orange */
    }

    @keyframes fall {
        0% {
            transform: translateY(-100px) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) rotate(720deg);
            opacity: 0;
        }
    }
</style>

<div class="floating-elements">
    <div class="floating-shape"></div>
    <div class="floating-shape"></div>
    <div class="floating-shape"></div>
</div>

<div class="confetti-container">
    @for ($i = 0; $i < 50; $i++)
        <div class="confetti" style="left: {{ rand(0, 100) }}%; animation-delay: {{ $i * 0.1 }}s; background: hsl({{ rand(0, 360) }}, 70%, 60%);"></div>
    @endfor
</div>

<div class="success-container">
    <div class="success-card">
        <div class="success-icon">
            ✅
        </div>
        <h1 class="success-title">Thank You!</h1>
        <p class="success-message">Your feedback has been successfully submitted.</p>

        <div class="feedback-summary">
            <div class="summary-title">Summary of Your Feedback</div>
            <div class="summary-item">
                <span class="summary-label">Employee Name:</span>
                <span class="summary-value">{{ session('feedback_data.employee_name', 'N/A') }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Department:</span>
                <span class="summary-value">{{ ucfirst(session('feedback_data.department', 'N/A')) }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Job Satisfaction:</span>
                <span class="summary-value">{{ session('feedback_data.job_satisfaction', 'N/A') }}/5</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Work Environment:</span>
                <span class="summary-value">{{ session('feedback_data.work_environment', 'N/A') }}/5</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Manager Support:</span>
                <span class="summary-value">{{ session('feedback_data.manager_support', 'N/A') }}/5</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Growth Opportunities:</span>
                <span class="summary-value">{{ session('feedback_data.growth_opportunities', 'N/A') }}/5</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Recommend Company:</span>
                <span class="summary-value">{{ ucfirst(session('feedback_data.recommend_company', 'N/A')) }}</span>
            </div>
            @if(session('feedback_data.suggestions'))
                <div class="summary-item">
                    <span class="summary-label">Suggestions:</span>
                    <span class="summary-value">{{ session('feedback_data.suggestions') }}</span>
                </div>
            @endif
            @if(session('feedback_data.additional_comments'))
                <div class="summary-item">
                    <span class="summary-label">Additional Comments:</span>
                    <span class="summary-value">{{ session('feedback_data.additional_comments') }}</span>
                </div>
            @endif
        </div>

        <a href="{{ route('feedback.create') }}" class="btn-success">
            ➕ Submit New Feedback
        </a>
        <a href="{{ route('feedback.index') }}" class="btn-success" style="margin-left: 1rem; background: linear-gradient(135deg, #6b7280, #4b5563);">
            📋 View All Feedback
        </a>
    </div>
</div>
@endsection
@extends('layouts.index')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .success-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #e0f2f7 0%, #cce7f0 100%); /* Bluish-silver background */
        padding: 2rem 1rem;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .success-card {
        max-width: 600px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 3rem;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        text-align: center;
        transform: translateY(20px);
        opacity: 0;
        animation: slideUp 0.8s ease-out forwards;
        position: relative;
        z-index: 1;
        overflow: hidden;
    }

    @keyframes slideUp {
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .success-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #2563eb); /* Blue gradient */
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .success-card:hover::before {
        transform: scaleX(1);
    }

    .success-icon {
        background: linear-gradient(135deg, #3b82f6, #2563eb); /* Blue gradient */
        color: white;
        border-radius: 50%;
        width: 100px;
        height: 100px;
        margin: 0 auto 2rem;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 3rem;
        box-shadow: 0 15px 30px rgba(59, 130, 246, 0.3); /* Blue shadow */
        position: relative;
        animation: popIn 0.5s ease-out forwards;
    }

    .success-icon::after {
        content: '';
        position: absolute;
        top: -5px;
        left: -5px;
        width: 110px;
        height: 110px;
        border: 3px solid rgba(59, 130, 246, 0.3); /* Light blue border */
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes popIn {
        from {
            transform: scale(0);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.7;
        }
    }

    .success-title {
        font-size: 2.5rem;
        color: #2d3748; /* Dark text */
        margin-bottom: 1rem;
        font-weight: 700;
        animation: fadeIn 1s ease-out 0.5s forwards;
    }

    .success-message {
        color: #718096; /* Muted dark text */
        font-size: 1.1rem;
        margin-bottom: 2rem;
        animation: fadeIn 1s ease-out 0.7s forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .feedback-summary {
        background: #e0e7ff; /* Light blue background */
        border: 2px solid #c3dafe; /* Light blue border */
        padding: 1.5rem;
        border-radius: 15px;
        margin-top: 2rem;
        margin-bottom: 2rem;
        text-align: left;
        animation: slideUp 0.8s ease-out 0.9s forwards;
    }

    .summary-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #3b82f6; /* Blue text */
        margin-bottom: 0.8rem;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px dashed #a7c5f2; /* Lighter blue dashed border */
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .summary-label {
        color: #4a5568;
        font-weight: 500;
    }

    .summary-value {
        color: #3b82f6; /* Blue text */
        font-weight: 600;
    }

    .btn-success {
        background: linear-gradient(135deg, #3b82f6, #2563eb); /* Blue gradient */
        color: white;
        padding: 1rem 2.5rem;
        border: none;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3); /* Blue shadow */
        animation: fadeIn 1s ease-out 1.1s forwards;
    }

    .btn-success:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(59, 130, 246, 0.4); /* Stronger blue shadow */
    }

    .floating-elements {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: -1;
    }

    .floating-shape {
        position: absolute;
        background: rgba(59, 130, 246, 0.1); /* Light blue tint for shapes */
        border-radius: 50%;
        animation: float 8s ease-in-out infinite;
    }

    .floating-shape:nth-child(1) {
        width: 100px;
        height: 100px;
        top: 10%;
        left: 5%;
        animation-delay: 0s;
    }

    .floating-shape:nth-child(2) {
        width: 150px;
        height: 150px;
        top: 70%;
        right: 5%;
        animation-delay: 3s;
    }

    .floating-shape:nth-child(3) {
        width: 80px;
        height: 80px;
        bottom: 10%;
        left: 15%;
        animation-delay: 6s;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px) rotate(0deg);
        }
        50% {
            transform: translateY(-30px) rotate(180deg);
        }
    }

    .confetti-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        overflow: hidden;
        z-index: 0;
    }

    .confetti {
        position: absolute;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        animation: fall 5s linear infinite;
        opacity: 0;
    }

    .confetti:nth-child(odd) {
        background: #3b82f6; /* Blue */
    }

    .confetti:nth-child(even) {
        background: #2563eb; /* Darker Blue */
    }

    .confetti:nth-child(3n) {
        background: #10b981; /* Green */
    }

    .confetti:nth-child(4n) {
        background: #f59e0b; /* Orange */
    }

    @keyframes fall {
        0% {
            transform: translateY(-100px) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) rotate(720deg);
            opacity: 0;
        }
    }
</style>

<div class="floating-elements">
    <div class="floating-shape"></div>
    <div class="floating-shape"></div>
    <div class="floating-shape"></div>
</div>

<div class="confetti-container">
    @for ($i = 0; $i < 50; $i++)
        <div class="confetti" style="left: {{ rand(0, 100) }}%; animation-delay: {{ $i * 0.1 }}s; background: hsl({{ rand(0, 360) }}, 70%, 60%);"></div>
    @endfor
</div>

<div class="success-container">
    <div class="success-card">
        <div class="success-icon">
            ✅
        </div>
        <h1 class="success-title">Thank You!</h1>
        <p class="success-message">Your feedback has been successfully submitted.</p>

        <div class="feedback-summary">
            <div class="summary-title">Summary of Your Feedback</div>
            <div class="summary-item">
                <span class="summary-label">Employee Name:</span>
                <span class="summary-value">{{ session('feedback_data.employee_name', 'N/A') }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Department:</span>
                <span class="summary-value">{{ ucfirst(session('feedback_data.department', 'N/A')) }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Job Satisfaction:</span>
                <span class="summary-value">{{ session('feedback_data.job_satisfaction', 'N/A') }}/5</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Work Environment:</span>
                <span class="summary-value">{{ session('feedback_data.work_environment', 'N/A') }}/5</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Manager Support:</span>
                <span class="summary-value">{{ session('feedback_data.manager_support', 'N/A') }}/5</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Growth Opportunities:</span>
                <span class="summary-value">{{ session('feedback_data.growth_opportunities', 'N/A') }}/5</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Recommend Company:</span>
                <span class="summary-value">{{ ucfirst(session('feedback_data.recommend_company', 'N/A')) }}</span>
            </div>
            @if(session('feedback_data.suggestions'))
                <div class="summary-item">
                    <span class="summary-label">Suggestions:</span>
                    <span class="summary-value">{{ session('feedback_data.suggestions') }}</span>
                </div>
            @endif
            @if(session('feedback_data.additional_comments'))
                <div class="summary-item">
                    <span class="summary-label">Additional Comments:</span>
                    <span class="summary-value">{{ session('feedback_data.additional_comments') }}</span>
                </div>
            @endif
        </div>

        <a href="{{ route('feedback.create') }}" class="btn-success">
            ➕ Submit New Feedback
        </a>
        <a href="{{ route('feedback.index') }}" class="btn-success" style="margin-left: 1rem; background: linear-gradient(135deg, #6b7280, #4b5563);">
            📋 View All Feedback
        </a>
    </div>
</div>
@endsection
