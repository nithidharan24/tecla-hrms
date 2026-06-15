@extends('layouts.index')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .analytics-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #e0f2f7 0%, #cce7f0 100%); /* Bluish-silver background */
        padding: 2rem 1rem;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .page-header {
        max-width: 1200px;
        margin: 0 auto 2rem;
        text-align: center;
        opacity: 0;
        transform: translateY(-20px);
        animation: slideDown 0.8s ease-out forwards;
    }

    @keyframes slideDown {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .page-title {
        font-size: 2.5rem;
        color: #2d3748; /* Dark text for contrast */
        margin-bottom: 0.5rem;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Subtle shadow */
    }

    .page-subtitle {
        color: #718096; /* Muted dark text */
        font-size: 1.1rem;
    }

    .back-button {
        max-width: 1200px;
        margin: 0 auto 2rem;
        opacity: 0;
        transform: translateX(-20px);
        animation: slideInLeft 0.6s ease-out 0.2s forwards;
    }

    @keyframes slideInLeft {
        to {
            opacity: 1;
            transform: translateX(0);
        }
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

    .analytics-grid {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }

    .analytics-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 0;
        transform: translateY(20px);
        animation: slideUp 0.6s ease-out forwards;
        position: relative;
        overflow: hidden;
    }

    .analytics-card:nth-child(1) {
        animation-delay: 0.1s;
    }
    .analytics-card:nth-child(2) {
        animation-delay: 0.2s;
    }
    .analytics-card:nth-child(3) {
        animation-delay: 0.3s;
    }
    .analytics-card:nth-child(4) {
        animation-delay: 0.4s;
    }
    .analytics-card:nth-child(5) {
        animation-delay: 0.5s;
    }
    .analytics-card:nth-child(6) {
        animation-delay: 0.6s;
    }

    @keyframes slideUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .analytics-card::before {
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

    .analytics-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }

    .analytics-card:hover::before {
        transform: scaleX(1);
    }

    .card-title {
        font-size: 1.5rem;
        color: #2d3748; /* Dark text */
        margin-bottom: 1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-value {
        font-size: 3rem;
        font-weight: 800;
        color: #3b82f6; /* Blue text */
        margin-bottom: 1.5rem;
        text-align: center;
        animation: fadeIn 1s ease-out 0.5s forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .card-description {
        color: #718096;
        font-size: 1rem;
        line-height: 1.5;
    }

    .rating-stars {
        display: flex;
        justify-content: center;
        gap: 5px;
        margin-top: 1rem;
    }

    .star {
        font-size: 1.8rem;
        color: #d1d5db;
    }

    .star.filled {
        color: #f59e0b; /* Keep orange for stars */
        text-shadow: 0 0 10px rgba(245, 158, 11, 0.5);
    }

    .progress-bar-container {
        width: 100%;
        background-color: #e2e8f0;
        border-radius: 10px;
        height: 20px;
        overflow: hidden;
        margin-top: 1rem;
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #3b82f6, #2563eb); /* Blue gradient */
        border-radius: 10px;
        transition: width 1s ease-out;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 0.5rem;
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .department-list {
        list-style: none;
        padding: 0;
        margin-top: 1rem;
    }

    .department-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.8rem 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .department-item:last-child {
        border-bottom: none;
    }

    .department-name {
        font-weight: 500;
        color: #4a5568;
    }

    .department-count {
        background: #e0e7ff; /* Light blue background */
        color: #3b82f6; /* Blue text */
        padding: 0.3rem 0.8rem;
        border-radius: 15px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .analytics-grid {
            grid-template-columns: 1fr;
            padding: 0 1rem;
        }

        .page-title {
            font-size: 2rem;
        }

        .analytics-card {
            padding: 2rem;
        }

        .card-value {
            font-size: 2.5rem;
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

<div class="analytics-container">
    <div class="page-header">
        <h1 class="page-title">Feedback Analytics</h1>
        <p class="page-subtitle">In-depth insights from employee feedback</p>
    </div>

    <div class="back-button">
        <a href="{{ route('feedback.index') }}" class="btn-back">
            ← Back to Feedback List
        </a>
    </div>

    <div class="analytics-grid">
        <div class="analytics-card">
            <h2 class="card-title">📊 Total Responses</h2>
            <div class="card-value">{{ $analytics['total_responses'] }}</div>
            <p class="card-description">Total number of feedback forms submitted.</p>
        </div>

        <div class="analytics-card">
            <h2 class="card-title">⭐ Avg. Job Satisfaction</h2>
            <div class="card-value">{{ number_format($analytics['avg_job_satisfaction'] ?? 0, 1) }}</div>
            <div class="rating-stars">
                @for($i = 1; $i <= 5; $i++)
                    <span class="star {{ $i <= round($analytics['avg_job_satisfaction'] ?? 0) ? 'filled' : '' }}">⭐</span>
                @endfor
            </div>
            <p class="card-description">Average rating for overall job satisfaction (1-5 scale).</p>
        </div>

        <div class="analytics-card">
            <h2 class="card-title">👍 Recommendation Rate</h2>
            <div class="card-value">{{ number_format($analytics['recommendation_rate'] ?? 0, 1) }}%</div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: {{ number_format($analytics['recommendation_rate'] ?? 0, 1) }}%;">
                    {{ number_format($analytics['recommendation_rate'] ?? 0, 1) }}%
                </div>
            </div>
            <p class="card-description">Percentage of employees who would recommend the company.</p>
        </div>

        <div class="analytics-card">
            <h2 class="card-title">🏢 Department Breakdown</h2>
            <ul class="department-list">
                @forelse($analytics['department_breakdown'] as $department)
                    <li class="department-item">
                        <span class="department-name">{{ ucfirst($department->department) }}</span>
                        <span class="department-count">{{ $department->count }}</span>
                    </li>
                @empty
                    <li class="department-item">No department data available.</li>
                @endforelse
            </ul>
            <p class="card-description">Distribution of feedback submissions across different departments.</p>
        </div>

        <div class="analytics-card">
            <h2 class="card-title">Environ. Rating</h2>
            <div class="card-value">{{ number_format($analytics['avg_work_environment'] ?? 0, 1) }}</div>
            <div class="rating-stars">
                @for($i = 1; $i <= 5; $i++)
                    <span class="star {{ $i <= round($analytics['avg_work_environment'] ?? 0) ? 'filled' : '' }}">⭐</span>
                @endfor
            </div>
            <p class="card-description">Average rating for the work environment.</p>
        </div>

        <div class="analytics-card">
            <h2 class="card-title">Manager Support</h2>
            <div class="card-value">{{ number_format($analytics['avg_manager_support'] ?? 0, 1) }}</div>
            <div class="rating-stars">
                @for($i = 1; $i <= 5; $i++)
                    <span class="star {{ $i <= round($analytics['avg_manager_support'] ?? 0) ? 'filled' : '' }}">⭐</span>
                @endfor
            </div>
            <p class="card-description">Average rating for manager support.</p>
        </div>

        <div class="analytics-card">
            <h2 class="card-title">Growth Opps.</h2>
            <div class="card-value">{{ number_format($analytics['avg_growth_opportunities'] ?? 0, 1) }}</div>
            <div class="rating-stars">
                @for($i = 1; $i <= 5; $i++)
                    <span class="star {{ $i <= round($analytics['avg_growth_opportunities'] ?? 0) ? 'filled' : '' }}">⭐</span>
                @endfor
            </div>
            <p class="card-description">Average rating for opportunities for growth.</p>
        </div>
    </div>
</div>
@endsection
