@extends('layouts.index')

@section('content')
<div class="container py-4">
    <div class="text-center mb-5">
        <h3 class="fw-bold text-dark">All Reports</h3>
        <p class="text-muted">Access and manage various reports in one place</p>
    </div>

    <div class="row g-4">
        @php
            $reportCards = [
                ['name' => 'Expense Report', 'route' => 'expenses-reports.index'],
                ['name' => 'Invoice Report', 'route' => 'invoice-reports.index'],
                ['name' => 'Payments Report', 'route' => 'payments-report.index'],
                ['name' => 'Account Reports', 'route' => 'accounts-reports.index'],
                ['name' => 'Project Report', 'route' => 'project-reports.index'],
                ['name' => 'New Project Reports', 'route' => 'newproject.reports.index'],
                ['name' => 'Task Report', 'route' => 'task-reports.index'],
                ['name' => 'Timesheet Reports', 'route' => 'timesheet-reports.index'],
                ['name' => 'Policy Report', 'route' => 'policies-report.index'],
                ['name' => 'Ticket Report', 'route' => 'ticket.reports.index'],
                ['name' => 'Test Ticket Report', 'route' => 'testing.reports.index'],
                ['name' => 'Asset Report', 'route' => 'asset-reports.index'],
                ['name' => 'Client Report', 'route' => 'client-reports.index'],
                ['name' => 'Daily Report', 'route' => 'daily-report.index'],
                ['name' => 'Interview Process Reports', 'route' => 'interview-process.index'],
            ];
        @endphp

        @foreach($reportCards as $report)
            @if(empty($modules) || in_array($report['name'], $modules))
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="report-card shadow-sm p-4 text-center h-100">
                        <div class="report-icon mb-3">
                            <i class="la la-file-alt fs-1 text-primary"></i>
                        </div>
                        <h6 class="fw-semibold text-dark mb-3">{{ $report['name'] }}</h6>
                        <a href="{{ route($report['route']) }}" class="btn btn-outline-primary btn-sm px-4">
                            View Report
                        </a>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>

<style>
.report-card {
    border-radius: 12px;
    background: #fff;
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}
.report-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08);
    border-color: #007bff20;
}
.report-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto;
    border-radius: 50%;
    background: #f5f7ff;
    display: flex;
    align-items: center;
    justify-content: center;
}
.report-icon i {
    font-size: 28px;
}
.btn-outline-primary {
    border-radius: 30px;
    font-weight: 500;
}
</style>
@endsection
