@extends('layouts.index')

@section('content')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .index-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #fafafa 0%, #dddddd 100%);
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
            color: #f66a3b;
            margin-bottom: 0.5rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .page-subtitle {
            color: #f66a3b;
            font-size: 1.1rem;
        }

        .user-role-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #f66a3b, #eb5a25);
            color: white;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-left: 1rem;
            vertical-align: middle;
        }

        .actions-bar {
            max-width: 1200px;
            margin: 0 auto 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            opacity: 0;
            transform: translateX(-20px);
            animation: slideInLeft 0.8s ease-out 0.2s forwards;
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
            background: linear-gradient(135deg, #f66d3b, #eb5a25);
            color: white;
            box-shadow: 0 4px 15px rgba(246, 109, 59, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(246, 131, 59, 0.4);
        }

        .btn-secondary {
            background: rgba(246, 106, 59, 0.1);
            color: #f66a3b;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(246, 106, 59, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(246, 106, 59, 0.15);
            transform: translateY(-2px);
        }

        .search-container {
            position: relative;
            max-width: 400px;
        }

        .search-input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 3rem;
            border: none;
            border-radius: 50px;
            background: rgba(246, 106, 59, 0.1);
            backdrop-filter: blur(10px);
            color: #2d3748;
            border: 1px solid rgba(246, 106, 59, 0.2);
        }

        .search-input::placeholder {
            color: #718096;
        }

        .search-input:focus {
            outline: none;
            background: rgba(246, 106, 59, 0.15);
            box-shadow: 0 0 0 3px rgba(246, 106, 59, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #718096;
        }

        .feedback-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }

        .feedback-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            width: 400px;
            min-height: 300px;
            padding: 1.25rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
            transform: translateY(20px);
            animation: slideUp 0.6s ease-out forwards;
            position: relative;
            overflow: hidden;
        }

        .feedback-card:nth-child(1) { animation-delay: 0.1s; }
        .feedback-card:nth-child(2) { animation-delay: 0.2s; }
        .feedback-card:nth-child(3) { animation-delay: 0.3s; }
        .feedback-card:nth-child(4) { animation-delay: 0.4s; }
        .feedback-card:nth-child(5) { animation-delay: 0.5s; }
        .feedback-card:nth-child(6) { animation-delay: 0.6s; }

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

        .feedback-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #f66a3b, #f66a3b);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .feedback-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .feedback-card:hover::before {
            transform: scaleX(1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .employee-info h3 {
            font-size: 1.3rem;
            color: #2d3748;
            margin-bottom: 0.3rem;
            font-weight: 700;
        }

        .department-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background: linear-gradient(135deg, #f6953b, #eb6a25);
            color: white;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .date-info {
            color: #718096;
            font-size: 0.9rem;
            text-align: right;
        }

        .ratings-summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .rating-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .rating-label {
            font-size: 0.9rem;
            color: #4a5568;
            font-weight: 500;
        }

        .rating-value {
            display: flex;
            gap: 2px;
        }

        .star {
            width: 16px;
            height: 16px;
            color: #fbbf24;
        }

        .star.filled {
            color: #f59e0b;
        }

        .recommendation {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            padding: 0.8rem;
            border-radius: 12px;
            font-weight: 600;
        }

        .recommendation.yes {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .recommendation.no {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .card-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            border-radius: 8px;
        }

        .btn-info {
            background: linear-gradient(135deg, #f66a3b, #f66a3b);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #718096;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .pagination-container {
            max-width: 1200px;
            margin: 3rem auto 0;
            display: flex;
            justify-content: center;
        }

        .pagination {
            display: flex;
            gap: 0.5rem;
            background: rgba(246, 106, 59, 0.1);
            backdrop-filter: blur(10px);
            padding: 1rem;
            border-radius: 15px;
            border: 1px solid rgba(246, 106, 59, 0.2);
        }

        .page-link {
            padding: 0.5rem 1rem;
            color: #f66a3b;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .page-link:hover, .page-link.active {
            background: rgba(246, 106, 59, 0.2);
            border-color: rgba(246, 106, 59, 0.3);
        }

        .stats-bar {
            max-width: 1200px;
            margin: 0 auto 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            opacity: 0;
            transform: translateY(20px);
            animation: slideUp 0.8s ease-out 0.4s forwards;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.911);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            border: 1px solid rgba(246, 140, 59, 0.2);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            background: rgba(246, 106, 59, 0.15);
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #f66a3b;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #f66a3b;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .employee-only-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border-radius: 15px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        @media (max-width: 768px) {
            .feedback-grid {
                grid-template-columns: 1fr;
                padding: 0 1rem;
            }

            .actions-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-container {
                max-width: 100%;
            }

            .ratings-summary {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 2rem;
            }

            .stats-bar {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .alert {
            max-width: 1200px;
            margin: 0 auto 2rem;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            font-weight: 500;
            opacity: 0;
            transform: translateY(-10px);
            animation: slideDown 0.5s ease-out forwards;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
    </style>

    <div class="index-container">
        <div class="page-header">
            <h1 class="page-title">
                Employee Feedback
                <span class="user-role-badge">
                    {{ ucfirst($userRole) }} View
                </span>
            </h1>
            <p class="page-subtitle">
                @if($userRole === 'admin')
                    Manage and review all employee feedback submissions
                @else
                    View and manage your feedback submissions
                @endif
            </p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="stats-bar">
            <div class="stat-card">
                <div class="stat-number">{{ $feedbacks->total() }}</div>
                <div class="stat-label">
                    @if($userRole === 'admin')
                        Total Responses
                    @else
                        My Responses
                    @endif
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ number_format($feedbacks->avg('job_satisfaction') ?? 0, 1) }}</div>
                <div class="stat-label">Avg Satisfaction</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $feedbacks->where('recommend_company', 'yes')->count() }}</div>
                <div class="stat-label">Recommendations</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $feedbacks->groupBy('department')->count() }}</div>
                <div class="stat-label">
                    @if($userRole === 'admin')
                        Departments
                    @else
                        My Department
                    @endif
                </div>
            </div>
        </div>

        <div class="actions-bar">
            <div class="search-container">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" placeholder="Search feedback..." id="searchInput">
            </div>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="{{ route('feedback.create') }}" class="btn btn-primary">
                    ➕ New Feedback
                </a>
                @if($userRole === 'admin')
                    <a href="{{ route('feedback.analytics') }}" class="btn btn-secondary">
                        📊 Analytics
                    </a>
                @endif
            </div>
        </div>

        @if($feedbacks->count() > 0)
            <div class="feedback-grid" id="feedbackGrid">
                @foreach($feedbacks as $feedback)
                    <div class="feedback-card" data-search="{{ strtolower($feedback->employee_name . ' ' . $feedback->department_name) }}">
                        <div class="card-header">
                            <div class="employee-info">
                                <h3>
                                    {{ $feedback->employee_name }}
                                    @if($userRole === 'employee')
                                        <span class="employee-only-badge">You</span>
                                    @endif
                                </h3>
                                <span class="department-badge">{{ ucfirst($feedback->department_name) }}</span>
                            </div>
                            <div class="date-info">
                                {{ \Carbon\Carbon::parse($feedback->created_at)->format('M d, Y') }}
                            </div>
                        </div>
                        <div class="ratings-summary">
                            <div class="rating-item">
                                <span class="rating-label">Job Satisfaction</span>
                                <div class="rating-value">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star {{ $i <= $feedback->job_satisfaction ? 'filled' : '' }}">⭐</span>
                                    @endfor
                                </div>
                            </div>
                            <div class="rating-item">
                                <span class="rating-label">Work Environment</span>
                                <div class="rating-value">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star {{ $i <= $feedback->work_environment ? 'filled' : '' }}">⭐</span>
                                    @endfor
                                </div>
                            </div>
                            <div class="rating-item">
                                <span class="rating-label">Manager Support</span>
                                <div class="rating-value">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star {{ $i <= $feedback->manager_support ? 'filled' : '' }}">⭐</span>
                                    @endfor
                                </div>
                            </div>
                            <div class="rating-item">
                                <span class="rating-label">Growth Opportunities</span>
                                <div class="rating-value">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star {{ $i <= $feedback->growth_opportunities ? 'filled' : '' }}">⭐</span>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <div class="recommendation {{ $feedback->recommend_company }}">
                            @if($feedback->recommend_company === 'yes')
                                ✅ Would recommend this company
                            @else
                                ❌ Would not recommend this company
                            @endif
                        </div>
                        <div class="card-actions">
                            <a href="{{ route('feedback.show', $feedback->id) }}" class="btn btn-info btn-sm">
                                👁️ View
                            </a>
                            @if($userRole === 'admin' || ($userRole === 'employee' && $feedback->employee_id == session('user_id')))
                                <a href="{{ route('feedback.edit', $feedback->id) }}" class="btn btn-warning btn-sm">
                                    ✏️ Edit
                                </a>
                                <form method="POST" action="{{ route('feedback.destroy', $feedback->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this feedback?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        🗑️ Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pagination-container">
                <div class="pagination">
                    {{ $feedbacks->links() }}
                </div>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">📝</div>
                <h3>
                    @if($userRole === 'admin')
                        No feedback submissions yet
                    @else
                        You haven't submitted any feedback yet
                    @endif
                </h3>
                <p>
                    @if($userRole === 'admin')
                        Start collecting valuable feedback from your employees
                    @else
                        Share your feedback to help us improve
                    @endif
                </p>
                <a href="{{ route('feedback.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    Create First Feedback
                </a>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const feedbackCards = document.querySelectorAll('.feedback-card');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();

                feedbackCards.forEach(card => {
                    const searchData = card.getAttribute('data-search');
                    if (searchData.includes(searchTerm)) {
                        card.style.display = 'block';
                        card.style.animation = 'slideUp 0.3s ease-out forwards';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });

            // Add hover effects to cards
            feedbackCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-10px) scale(1.02)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
@endsection