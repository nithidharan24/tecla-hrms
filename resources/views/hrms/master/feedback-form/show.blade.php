@extends('layouts.index')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .show-container {
        min-height: 100vh;
       background: linear-gradient(135deg, #fafafa 0%, #dddddd 100%); /* Bluish-silver background */
        padding: 2rem 1rem;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .back-button {
        max-width: 1000px;
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

    .feedback-detail {
        max-width: 1000px;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 25px;
        overflow: hidden;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        opacity: 0;
        transform: translateY(30px);
        animation: slideUp 0.8s ease-out 0.2s forwards;
    }

    @keyframes slideUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInLeft {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .detail-header {
        background: linear-gradient(135deg, #3b82f6, #2563eb); /* Blue gradient */
        color: white;
        padding: 3rem 3rem 2rem;
        position: relative;
        overflow: hidden;
    }

    .detail-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }

    @keyframes rotate {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    .header-content {
        position: relative;
        z-index: 2;
    }

    .employee-name {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .employee-meta {
        display: flex;
        gap: 2rem;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .department-tag {
        background: rgba(59, 130, 246, 0.2); /* Light blue tint */
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(59, 130, 246, 0.3); /* Light blue border */
    }

    .submission-date {
        color: rgba(255,255,255,0.9); /* White text on dark blue header */
        font-size: 1rem;
    }

    .detail-body {
        padding: 3rem;
    }

    .ratings-section {
        margin-bottom: 3rem;
    }

    .section-title {
        font-size: 1.8rem;
        color: #2d3748; /* Dark text */
        margin-bottom: 2rem;
        font-weight: 700;
        position: relative;
        padding-bottom: 0.5rem;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, #3b82f6, #2563eb); /* Blue gradient */
        border-radius: 2px;
    }

    .ratings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .rating-card {
        background: #f8fafc;
        padding: 2rem;
        border-radius: 15px;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .rating-card::before {
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

    .rating-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border-color: #3b82f6; /* Blue border on hover */
    }

    .rating-card:hover::before {
        transform: scaleX(1);
    }

    .rating-label {
        font-size: 1rem;
        color: #4a5568;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .rating-display {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stars-container {
        display: flex;
        gap: 3px;
    }

    .star {
        font-size: 1.5rem;
        transition: all 0.2s ease;
    }

    .star.filled {
        color: #f59e0b; /* Keep orange for stars */
        text-shadow: 0 0 10px rgba(245, 158, 11, 0.5);
        animation: twinkle 2s ease-in-out infinite;
    }

    .star.empty {
        color: #d1d5db;
    }

    @keyframes twinkle {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
    }

    .rating-number {
        font-size: 2rem;
        font-weight: 700;
        color: #3b82f6; /* Blue text */
    }

    .recommendation-section {
        margin-bottom: 3rem;
    }

    .recommendation-card {
        padding: 2rem;
        border-radius: 15px;
        font-size: 1.2rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }

    .recommendation-card.yes {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.05)); /* Light green tint */
        color: #059669; /* Green text */
        border: 2px solid rgba(16, 185, 129, 0.2); /* Light green border */
    }

    .recommendation-card.no {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.05)); /* Light red tint */
        color: #dc2626; /* Red text */
        border: 2px solid rgba(239, 68, 68, 0.2); /* Light red border */
    }

    .recommendation-icon {
        font-size: 2rem;
        animation: bounce 2s ease-in-out infinite;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }

    .comments-section {
        margin-bottom: 3rem;
    }

    .comment-card {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        position: relative;
        transition: all 0.3s ease;
    }

    .comment-card:hover {
        border-color: #3b82f6; /* Blue border on hover */
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.1); /* Light blue shadow */
    }

    .comment-label {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 1rem;
        font-size: 1.1rem;
    }

    .comment-text {
        color: #2d3748;
        line-height: 1.6;
        font-size: 1rem;
    }

    .empty-comment {
        color: #9ca3af;
        font-style: italic;
    }

    .actions-section {
        border-top: 2px solid #e2e8f0;
        padding-top: 2rem;
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn {
        padding: 0.8rem 2rem;
        border: none;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
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
        background: linear-gradient(135deg, #3b82f6, #1d4ed8); /* Blue gradient */
        color: white;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3); /* Blue shadow */
    }

    .btn-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706); /* Orange gradient */
        color: white;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626); /* Red gradient */
        color: white;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }

    @media (max-width: 768px) {
        .detail-body {
            padding: 2rem 1.5rem;
        }

        .detail-header {
            padding: 2rem 1.5rem;
        }

        .employee-name {
            font-size: 2rem;
        }

        .employee-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .ratings-grid {
            grid-template-columns: 1fr;
        }

        .actions-section {
            flex-direction: column;
        }
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
        background: rgba(59, 130, 246, 0.05); /* Light blue tint for shapes */
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
</style>

<div class="floating-elements">
    <div class="floating-shape"></div>
    <div class="floating-shape"></div>
    <div class="floating-shape"></div>
</div>

<div class="show-container">
    <div class="back-button">
        <a href="{{ route('feedback.index') }}" class="btn-back">
            ← Back to Feedback List
        </a>
    </div>

    <div class="feedback-detail">
        <div class="detail-header">
            <div class="header-content">
                <h1 class="employee-name">{{ $feedback->employee_name }}</h1>
                <div class="employee-meta">
                    <span class="department-tag">{{ ucfirst($feedback->department) }}</span>
                    <span class="submission-date">
                        Submitted on {{ \Carbon\Carbon::parse($feedback->created_at)->format('F d, Y \a\t g:i A') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="detail-body">
            <div class="ratings-section">
                <h2 class="section-title">📊 Performance Ratings</h2>
                <div class="ratings-grid">
                    <div class="rating-card">
                        <div class="rating-label">Overall Job Satisfaction</div>
                        <div class="rating-display">
                            <div class="stars-container">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="star {{ $i <= $feedback->job_satisfaction ? 'filled' : 'empty' }}">⭐</span>
                                @endfor
                            </div>
                            <span class="rating-number">{{ $feedback->job_satisfaction }}/5</span>
                        </div>
                    </div>
                    <div class="rating-card">
                        <div class="rating-label">Work Environment</div>
                        <div class="rating-display">
                            <div class="stars-container">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="star {{ $i <= $feedback->work_environment ? 'filled' : 'empty' }}">⭐</span>
                                @endfor
                            </div>
                            <span class="rating-number">{{ $feedback->work_environment }}/5</span>
                        </div>
                    </div>
                    <div class="rating-card">
                        <div class="rating-label">Manager Support</div>
                        <div class="rating-display">
                            <div class="stars-container">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="star {{ $i <= $feedback->manager_support ? 'filled' : 'empty' }}">⭐</span>
                                @endfor
                            </div>
                            <span class="rating-number">{{ $feedback->manager_support }}/5</span>
                        </div>
                    </div>
                    <div class="rating-card">
                        <div class="rating-label">Growth Opportunities</div>
                        <div class="rating-display">
                            <div class="stars-container">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="star {{ $i <= $feedback->growth_opportunities ? 'filled' : 'empty' }}">⭐</span>
                                @endfor
                            </div>
                            <span class="rating-number">{{ $feedback->growth_opportunities }}/5</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="recommendation-section">
                <h2 class="section-title">💼 Company Recommendation</h2>
                <div class="recommendation-card {{ $feedback->recommend_company }}">
                    <span class="recommendation-icon">
                        @if($feedback->recommend_company === 'yes')
                            ✅
                        @else
                            ❌
                        @endif
                    </span>
                    <span>
                        @if($feedback->recommend_company === 'yes')
                            Would recommend this company to others
                        @else
                            Would not recommend this company to others
                        @endif
                    </span>
                </div>
            </div>

            <div class="comments-section">
                <h2 class="section-title">💬 Feedback Comments</h2>

                <div class="comment-card">
                    <div class="comment-label">Suggestions for Improvement</div>
                    <div class="comment-text">
                        @if($feedback->suggestions)
                            {{ $feedback->suggestions }}
                        @else
                            <span class="empty-comment">No suggestions provided</span>
                        @endif
                    </div>
                </div>

                <div class="comment-card">
                    <div class="comment-label">Additional Comments</div>
                    <div class="comment-text">
                        @if($feedback->additional_comments)
                            {{ $feedback->additional_comments }}
                        @else
                            <span class="empty-comment">No additional comments provided</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="actions-section">
                <a href="{{ route('feedback.edit', $feedback->id) }}" class="btn btn-warning">
                    ✏️ Edit Feedback
                </a>
                <a href="{{ route('feedback.index') }}" class="btn btn-primary">
                    📋 View All Feedback
                </a>
                <form method="POST" action="{{ route('feedback.destroy', $feedback->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this feedback? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        🗑️ Delete Feedback
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add staggered animation to rating cards
        const ratingCards = document.querySelectorAll('.rating-card');
        ratingCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.animation = `slideUp 0.6s ease-out ${index * 0.1}s forwards`;
        });

        // Add hover effect to stars
        const stars = document.querySelectorAll('.star.filled');
        stars.forEach(star => {
            star.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.2)';
            });

            star.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });

        // Add click animation to buttons
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.style.position = 'absolute';
                ripple.style.borderRadius = '50%';
                ripple.style.background = 'rgba(255,255,255,0.5)';
                ripple.style.transform = 'scale(0)';
                ripple.style.animation = 'ripple 0.6s linear';
                ripple.style.pointerEvents = 'none';

                this.appendChild(ripple);

                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    });

    // Add ripple animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
</script>
@endsection
