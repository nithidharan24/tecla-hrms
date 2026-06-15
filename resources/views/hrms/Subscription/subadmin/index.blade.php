@extends('layouts.index')

@section('content')
<div class="container mt-5">
    <!-- Page Header -->       
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title fw-bold">Subscription Plans</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Plans</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="{{ route('subscribe.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Plan
                </a>
                <a href="{{ route('sub.index') }}" class="btn btn-outline-secondary ms-2">
                    <i class="fas fa-building me-2"></i>View Subscribed Companies
                </a>
            </div>
        </div>
    </div>

    <!-- Plan Type Selector -->
    <div class="d-flex justify-content-center my-5">
        <div class="plan-toggle btn-group shadow-sm" role="group">
            <button type="button" class="btn btn-plan active" id="monthly-plan-btn">
                <i class="fas fa-calendar-alt me-2"></i>Monthly Plans
            </button>
            <button type="button" class="btn btn-plan" id="annual-plan-btn">
                <i class="fas fa-calendar-check me-2"></i>Annual Plans
              
            </button>
        </div>
    </div>

    <!-- Cards View Section -->
    <div class="view-toggle d-flex justify-content-end mb-3">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-orange active" id="cards-view-btn">
                <i class="fas fa-th-large me-2"></i>Cards
            </button>
            <button type="button" class="btn btn-outline-orange" id="table-view-btn">
                <i class="fas fa-table me-2"></i>Table
            </button>
        </div>
    </div>

    <!-- Cards View -->
    <div id="cards-view">
        <!-- Monthly Plans Cards -->
        <div id="monthly-plans" class="plans-section">
            <div class="row g-4">
                @foreach($plans->where('plan_type', 'monthly') as $plan)
                <div class="col-xl-4 col-md-6">
                    <div class="card plan-card h-100">
                        <div class="card-body d-flex flex-column">

                            @if($loop->iteration == 2)
                            <div class="popular-badge">Most Popular</div>
                            @endif
                            
                            <div class="plan-header text-center mb-4">
                                <h4 class="plan-name">{{ $plan->plan_name }}</h4>
                                <div class="plan-price">
                                    <span class="currency">₹</span>
                                    <span class="amount">{{ number_format($plan->plan_amount, 0) }}</span>
                                    <span class="period">/mo</span>
                                </div>
                                <p class="plan-description text-muted">{{ $plan->description }}</p>
                            </div>

                            <div class="plan-features flex-grow-1">

                                <div class="feature-item">
                                    <i class="fas fa-users text-orange"></i>
                                    <span><strong>{{ $plan->total_users }}</strong> Team Members</span>
                                </div>
                                
                                @if(isset($plan->total_projects))
                                <div class="feature-item">
                                    <i class="fas fa-project-diagram text-orange"></i>
                                    <span><strong>{{ $plan->total_projects }}</strong> Projects</span>
                                </div>
                                @endif
                                
                                @if(isset($plan->total_storage))
                                <div class="feature-item">
                                    <i class="fas fa-database text-orange"></i>
                                    <span><strong>{{ $plan->total_storage }}GB</strong> Storage</span>
                                </div>
                                @endif

                                <div class="feature-divider">
                                    <span class="divider-text">Included Modules</span>
                                </div>

                                @foreach(explode(',', $plan->modules) as $module)
                                <div class="feature-item">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span>{{ ucfirst(trim($module)) }}</span>
                                </div>
                                @endforeach
                            </div>

                            <div class="card-actions mt-4">
                                <a href="{{ route('subscribe.edit', $plan->id) }}" class="btn btn-edit w-100 mb-2">
                                    <i class="fas fa-edit me-2"></i>Edit Plan
                                </a>
                                <form action="{{ route('subscribe.destroy', $plan->id) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this plan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-delete w-100">
                                        <i class="fas fa-trash-alt me-2"></i>Delete Plan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Annual Plans Cards -->
        <div id="annual-plans" class="plans-section" style="display: none;">
            <div class="row g-4">
                @foreach($plans->where('plan_type', 'annual') as $plan)
                <div class="col-xl-4 col-md-6">
                    <div class="card plan-card h-100">
                        <div class="card-body d-flex flex-column">

                            @if($loop->iteration == 2)
                            <div class="popular-badge">Best Value</div>
                            @endif
                            
                            <div class="plan-header text-center mb-4">
                                <h4 class="plan-name">{{ $plan->plan_name }}</h4>
                                <div class="plan-price">
                                    <span class="currency">₹</span>
                                    <span class="amount">{{ number_format($plan->plan_amount, 0) }}</span>
                                    <span class="period">/yr</span>
                                </div>
                                <div class="savings-badge">
                                    Save ${{ number_format($plan->plan_amount * 0.2, 0) }} yearly
                                </div>
                                <p class="plan-description text-muted">{{ $plan->description }}</p>
                            </div>

                            <div class="plan-features flex-grow-1">

                                <div class="feature-item">
                                    <i class="fas fa-users text-orange"></i>
                                    <span><strong>{{ $plan->total_users }}</strong> Team Members</span>
                                </div>
                                
                                @if(isset($plan->total_projects))
                                <div class="feature-item">
                                    <i class="fas fa-project-diagram text-orange"></i>
                                    <span><strong>{{ $plan->total_projects }}</strong> Projects</span>
                                </div>
                                @endif
                                
                                @if(isset($plan->total_storage))
                                <div class="feature-item">
                                    <i class="fas fa-database text-orange"></i>
                                    <span><strong>{{ $plan->total_storage }}GB</strong> Storage</span>
                                </div>
                                @endif

                                <div class="feature-divider">
                                    <span class="divider-text">Included Modules</span>
                                </div>

                                @foreach(explode(',', $plan->modules) as $module)
                                <div class="feature-item">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span>{{ ucfirst(trim($module)) }}</span>
                                </div>
                                @endforeach
                            </div>

                            <div class="card-actions mt-4">
                                <a href="{{ route('subscribe.edit', $plan->id) }}" class="btn btn-edit w-100 mb-2">
                                    <i class="fas fa-edit me-2"></i>Edit Plan
                                </a>
                                <form action="{{ route('subscribe.destroy', $plan->id) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this plan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-delete w-100">
                                        <i class="fas fa-trash-alt me-2"></i>Delete Plan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Table View (Initially Hidden) -->
    <div id="table-view" style="display: none;">
        <!-- Monthly Plans Table -->
        <div id="monthly-plans-table" class="table-responsive">
            <div class="table-header d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0">Monthly Plans</h4>
                <span class="badge bg-orange-soft text-orange">{{ $plans->where('plan_type', 'monthly')->count() }} Plans</span>
            </div>
            <table class="table modern-table">
                <thead>
                    <tr>
                      
                        <th>Plan Name</th>
                        <th>Amount</th>
                        <th>Users</th>
           
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plans->where('plan_type', 'monthly') as $plan)
                    <tr>
                       
                        <td>
                            <div class="plan-info">
                                <span class="plan-name-cell">{{ $plan->plan_name }}</span>
                                <small class="text-muted d-block">{{ Str::limit($plan->description, 30) }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="amount-cell">₹{{ number_format($plan->plan_amount, 2) }}</span>
                            <small class="text-muted d-block">/month</small>
                        </td>
                        <td>
                            <span class="badge bg-orange-soft text-orange">{{ $plan->total_users }}</span>
                        </td>
                  
                        <td class="text-end">
                            <div class="action-buttons">
                                <a href="{{ route('subscribe.edit', $plan->id) }}" class="action-btn edit-btn" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('subscribe.destroy', $plan->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete-btn" title="Delete" 
                                            onclick="return confirm('Are you sure you want to delete this plan?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Annual Plans Table -->
        <div id="annual-plans-table" class="table-responsive mt-5" style="display: none;">
            <div class="table-header d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0">Annual Plans</h4>
                <span class="badge bg-orange-soft text-orange">{{ $plans->where('plan_type', 'annual')->count() }} Plans</span>
            </div>
            <table class="table modern-table">
                <thead>
                    <tr>
                      
                        <th>Plan Name</th>
                        <th>Amount</th>
                        <th>Users</th>
                        
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plans->where('plan_type', 'annual') as $plan)
                    <tr>
                      
                        <td>
                            <div class="plan-info">
                                <span class="plan-name-cell">{{ $plan->plan_name }}</span>
                                <small class="text-muted d-block">{{ Str::limit($plan->description, 30) }}</small>
                            </div>
                        </td>
                        <td>
                                    <span class="currency">₹</span>
                                    <span class="amount-cell">{{ number_format($plan->plan_amount, 2) }}</span>
                            <small class="text-muted d-block">/year</small>
                        </td>
                        <td>
                            <span class="badge bg-orange-soft text-orange">{{ $plan->total_users }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary-soft">{{ $plan->total_projects ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary-soft">{{ $plan->total_storage ?? 'N/A' }} GB</span>
                        </td>
                        <td class="text-end">
                            <div class="action-buttons">
                                <a href="{{ route('subscribe.edit', $plan->id) }}" class="action-btn edit-btn" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('subscribe.destroy', $plan->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete-btn" title="Delete"
                                            onclick="return confirm('Are you sure you want to delete this plan?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Include Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
:root {
    --orange-primary: #f97316;
    --orange-dark: #ea580c;
    --orange-light: #fb923c;
    --orange-soft: #fff7ed;
    --secondary-color: #64748b;
    --secondary-soft: #f1f5f9;
    --success-color: #10b981;
    --danger-color: #ef4444;
    --danger-soft: #fee2e2;
    --dark-bg: #1e293b;
    --light-bg: #f8fafc;
}

.text-orange {
    color: var(--orange-primary) !important;
}

.bg-orange-soft {
    background-color: var(--orange-soft) !important;
}

.bg-secondary-soft {
    background-color: var(--secondary-soft) !important;
}

/* Button Styles */
.btn-primary {
    background: var(--orange-primary) !important;
    border-color: var(--orange-primary) !important;
    color: white !important;
    padding: 0.5rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: var(--orange-dark) !important;
    border-color: var(--orange-dark) !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
}

.btn-outline-secondary {
    border: 1px solid #e2e8f0;
    color: var(--secondary-color);
    padding: 0.5rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    background: var(--secondary-soft);
    border-color: #cbd5e1;
    color: var(--dark-bg);
}

/* Plan Toggle */
.plan-toggle {
    background: var(--light-bg);
    padding: 0.5rem;
    border-radius: 50px;
    border: 1px solid #e2e8f0;
}

.plan-toggle .btn-plan {
    padding: 0.75rem 2rem;
    font-weight: 600;
    border-radius: 50px;
    border: none;
    transition: all 0.3s ease;
}
.plan-card .card-body {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.plan-toggle .btn-plan.active {
    background: var(--orange-primary);
    color: white;
    box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
}

.plan-toggle .btn-plan:not(.active) {
    background: transparent;
    color: var(--secondary-color);
}

.plan-toggle .btn-plan:not(.active):hover {
    background: rgba(249, 115, 22, 0.1);
    color: var(--orange-primary);
}

/* View Toggle */
.btn-outline-orange {
    border: 1px solid var(--orange-primary);
    color: var(--orange-primary);
    padding: 0.5rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-outline-orange.active {
    background: var(--orange-primary);
    color: white;
}

.btn-outline-orange:hover:not(.active) {
    background: var(--orange-soft);
    color: var(--orange-dark);
}

/* Plan Cards */
.plan-card {
    border: none;
    border-radius: 20px;
    background: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.plan-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(249, 115, 22, 0.1);
}
.card-actions {
    margin-top: auto;   /* THIS pushes it to bottom */
}

.popular-badge {
    position: absolute;
    top: 20px;
    right: -35px;
    background: var(--orange-primary);
    color: white;
    padding: 8px 40px;
    font-size: 0.875rem;
    font-weight: 600;
    transform: rotate(45deg);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.plan-header {
    padding: 1.5rem 1rem;
    border-bottom: 2px solid #f1f5f9;
}

.plan-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark-bg);
}

.plan-price .currency {
    font-size: 1.5rem;
    font-weight: 600;
    vertical-align: top;
    color: var(--orange-primary);
}

.plan-price .amount {
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1;
    color: var(--dark-bg);
}

.plan-price .period {
    font-size: 1rem;
    color: var(--secondary-color);
}

.savings-badge {
    background: var(--orange-soft);
    color: var(--orange-dark);
    padding: 0.25rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    display: inline-block;
}

.plan-features {
    padding: 1.5rem 1rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    color: #475569;
}

.feature-divider {
    position: relative;
    text-align: center;
    margin: 1.5rem 0;
}

.feature-divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #e2e8f0;
}

.divider-text {
    position: relative;
    background: white;
    padding: 0 1rem;
    color: var(--secondary-color);
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
}

/* Card Action Buttons */
.btn-edit {
    background: var(--orange-soft);
    color: var(--orange-primary);
    border: none;
    padding: 0.75rem;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-edit:hover {
    background: var(--orange-primary);
    color: white;
}

.btn-delete {
    background: var(--danger-soft);
    color: var(--danger-color);
    border: none;
    padding: 0.75rem;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-delete:hover {
    background: var(--danger-color);
    color: white;
}

/* Modern Table */
.modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 8px;
}

.modern-table thead th {
    background: var(--light-bg);
    color: var(--secondary-color);
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 1rem;
    border: none;
}

.modern-table tbody tr {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    transition: all 0.3s ease;
}

.modern-table tbody tr:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transform: translateY(-2px);
}

.modern-table tbody td {
    padding: 1rem;
    border: none;
    vertical-align: middle;
}

/* Custom Checkbox */
.custom-control {
    position: relative;
    display: inline-block;
}

.custom-control-input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

.custom-control-label {
    position: relative;
    padding-left: 25px;
    cursor: pointer;
}

.custom-control-label:before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 18px;
    height: 18px;
    border: 2px solid #cbd5e1;
    border-radius: 4px;
    background: white;
    transition: all 0.2s ease;
}

.custom-control-input:checked ~ .custom-control-label:before {
    background: var(--orange-primary);
    border-color: var(--orange-primary);
}

.custom-control-input:checked ~ .custom-control-label:after {
    content: '\f00c';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    position: absolute;
    left: 4px;
    top: 50%;
    transform: translateY(-50%);
    color: white;
    font-size: 10px;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}

.action-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.edit-btn {
    background: var(--orange-soft);
    color: var(--orange-primary);
}

.edit-btn:hover {
    background: var(--orange-primary);
    color: white;
}

.delete-btn {
    background: var(--danger-soft);
    color: var(--danger-color);
    border: none;
}

.delete-btn:hover {
    background: var(--danger-color);
    color: white;
}

/* Badges */
.badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.875rem;
}

/* Responsive */
@media (max-width: 768px) {
    .plan-toggle .btn-plan {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
    
    .plan-price .amount {
        font-size: 2.5rem;
    }
    
    .modern-table {
        display: block;
        overflow-x: auto;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Plan toggle functionality
    const monthlyBtn = document.getElementById('monthly-plan-btn');
    const annualBtn = document.getElementById('annual-plan-btn');
    const monthlyCards = document.getElementById('monthly-plans');
    const annualCards = document.getElementById('annual-plans');
    const monthlyTable = document.getElementById('monthly-plans-table');
    const annualTable = document.getElementById('annual-plans-table');

    monthlyBtn.addEventListener('click', function() {
        this.classList.add('active');
        annualBtn.classList.remove('active');
        
        if (document.getElementById('cards-view').style.display !== 'none') {
            monthlyCards.style.display = 'block';
            annualCards.style.display = 'none';
        } else {
            monthlyTable.style.display = 'block';
            annualTable.style.display = 'none';
        }
    });

    annualBtn.addEventListener('click', function() {
        this.classList.add('active');
        monthlyBtn.classList.remove('active');
        
        if (document.getElementById('cards-view').style.display !== 'none') {
            monthlyCards.style.display = 'none';
            annualCards.style.display = 'block';
        } else {
            monthlyTable.style.display = 'none';
            annualTable.style.display = 'block';
        }
    });

    // View toggle functionality
    const cardsViewBtn = document.getElementById('cards-view-btn');
    const tableViewBtn = document.getElementById('table-view-btn');
    const cardsView = document.getElementById('cards-view');
    const tableView = document.getElementById('table-view');

    cardsViewBtn.addEventListener('click', function() {
        this.classList.add('active');
        tableViewBtn.classList.remove('active');
        cardsView.style.display = 'block';
        tableView.style.display = 'none';
        
        // Show appropriate cards based on active plan type
        if (monthlyBtn.classList.contains('active')) {
            monthlyCards.style.display = 'block';
            annualCards.style.display = 'none';
        } else {
            monthlyCards.style.display = 'none';
            annualCards.style.display = 'block';
        }
    });

    tableViewBtn.addEventListener('click', function() {
        this.classList.add('active');
        cardsViewBtn.classList.remove('active');
        cardsView.style.display = 'none';
        tableView.style.display = 'block';
        
        // Show appropriate table based on active plan type
        if (monthlyBtn.classList.contains('active')) {
            monthlyTable.style.display = 'block';
            annualTable.style.display = 'none';
        } else {
            monthlyTable.style.display = 'none';
            annualTable.style.display = 'block';
        }
    });

    // Checkbox functionality
    function setupCheckboxHandlers(allSelector, rowClass) {
        const checkAll = document.getElementById(allSelector);
        const rowChecks = document.querySelectorAll('.' + rowClass);

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                rowChecks.forEach(cb => {
                    cb.checked = this.checked;
                    const row = cb.closest('tr');
                    if (row) {
                        row.classList.toggle('selected-row', this.checked);
                    }
                });
            });
        }

        rowChecks.forEach(cb => {
            cb.addEventListener('change', function() {
                const row = this.closest('tr');
                if (row) {
                    row.classList.toggle('selected-row', this.checked);
                }
                
                // Update "check all" state
                if (checkAll) {
                    const allChecked = Array.from(rowChecks).every(c => c.checked);
                    const anyChecked = Array.from(rowChecks).some(c => c.checked);
                    checkAll.checked = allChecked;
                    checkAll.indeterminate = !allChecked && anyChecked;
                }
            });
        });
    }

    setupCheckboxHandlers('checkAllMonthly', 'row-check-monthly');
    setupCheckboxHandlers('checkAllAnnual', 'row-check-annual');
});
</script>
@endsection