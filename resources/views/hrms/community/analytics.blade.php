@extends('layouts.index')

@section('content')
<style>
.analytics-dashboard {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.analytics-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    overflow: hidden;
}

.chart-container {
    position: relative;
    height: 400px;
    padding: 2rem;
}

.department-stat-item {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border-left: 4px solid #667eea;
    transition: all 0.3s ease;
}

.department-stat-item:hover {
    transform: translateX(5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}
</style>

<div class="analytics-dashboard">
    <div class="content container-fluid">
        <!-- Analytics Header -->
        <div class="analytics-card mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="mb-2" style="font-size: 2.5rem; font-weight: 800;">📊 Community Analytics</h1>
                        <p class="mb-0" style="font-size: 1.1rem; opacity: 0.9;">Detailed insights into community engagement and celebration trends</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('community.admin.index') }}" class="btn btn-light btn-lg">
                            ← Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($error))
            <div class="alert alert-danger rounded-4">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ $error }}
            </div>
        @else
            <div class="row">
                <!-- Monthly Trends Chart -->
                <div class="col-lg-8 mb-4">
                    <div class="analytics-card">
                        <div class="card-header bg-transparent border-0 p-4">
                            <h3 class="section-title">📈 Monthly Wish Trends (Last 12 Months)</h3>
                        </div>
                        <div class="chart-container">
                            <canvas id="monthlyTrendsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Department Statistics -->
                <div class="col-lg-4 mb-4">
                    <div class="analytics-card">
                        <div class="card-header bg-transparent border-0 p-4">
                            <h3 class="section-title">🏢 Department Statistics</h3>
                        </div>
                        <div class="card-body p-4">
                            @if($departmentStats->count() > 0)
                                <div style="max-height: 400px; overflow-y: auto;">
                                    @foreach($departmentStats as $dept)
                                        <div class="department-stat-item">
                                            <h6 class="fw-bold mb-2">{{ $dept->department_name }}</h6>
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <div class="text-primary fw-bold">{{ $dept->birthday_wishes }}</div>
                                                    <small class="text-muted">🎂 Birthday</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-success fw-bold">{{ $dept->anniversary_wishes }}</div>
                                                    <small class="text-muted">🏆 Anniversary</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-info fw-bold">{{ $dept->total_wishes }}</div>
                                                    <small class="text-muted">📊 Total</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No department data available</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Department Comparison Chart -->
            <div class="row">
                <div class="col-12">
                    <div class="analytics-card">
                        <div class="card-header bg-transparent border-0 p-4">
                            <h3 class="section-title">🏢 Department Comparison</h3>
                        </div>
                        <div class="chart-container">
                            <canvas id="departmentChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(!isset($error))
        // Monthly Trends Chart
        const monthlyData = @json($monthlyTrends);
        const monthlyCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
        
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: monthlyData.map(item => {
                    const date = new Date(item.month + '-01');
                    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                }),
                datasets: [
                    {
                        label: '🎂 Birthday Wishes',
                        data: monthlyData.map(item => item.birthday_wishes),
                        borderColor: '#ff6b6b',
                        backgroundColor: 'rgba(255, 107, 107, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: '🏆 Anniversary Wishes',
                        data: monthlyData.map(item => item.anniversary_wishes),
                        borderColor: '#00b894',
                        backgroundColor: 'rgba(0, 184, 148, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Monthly Wish Trends'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Department Chart
        const deptData = @json($departmentStats);
        const deptCtx = document.getElementById('departmentChart').getContext('2d');
        
        new Chart(deptCtx, {
            type: 'bar',
            data: {
                labels: deptData.map(item => item.department_name),
                datasets: [
                    {
                        label: '🎂 Birthday Wishes',
                        data: deptData.map(item => item.birthday_wishes),
                        backgroundColor: 'rgba(255, 107, 107, 0.8)',
                        borderColor: '#ff6b6b',
                        borderWidth: 2
                    },
                    {
                        label: '🏆 Anniversary Wishes',
                        data: deptData.map(item => item.anniversary_wishes),
                        backgroundColor: 'rgba(0, 184, 148, 0.8)',
                        borderColor: '#00b894',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Department-wise Wish Distribution'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    @endif
});
</script>

@endsection
