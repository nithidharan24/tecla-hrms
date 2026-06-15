@extends('layouts.index')

@section('content')
<div class="container mt-5">
    

    <!-- Plan Type Selector -->
    <div class="d-flex justify-content-center my-5">
        <div class="plan-toggle btn-group shadow-sm" role="group">
            <button type="button" class="btn btn-plan active" id="monthly-plan-btn">
                <i class="fas fa-calendar-alt me-2"></i>Monthly Billing
            </button>
            <button type="button" class="btn btn-plan" id="annual-plan-btn">
                <i class="fas fa-calendar-check me-2"></i>Annual Billing
              
            </button>
        </div>
    </div>

    <!-- Monthly Plans Section -->
    <div id="monthly-plans" class="plans-section">
        <div class="row g-4">
            @foreach($plans->where('plan_type', 'monthly') as $plan)
            @php
    $isActive = false;
    $daysLeft = null;

    if(isset($activeSubscription) && $activeSubscription->plan_id == $plan->id){
        $isActive = true;
        $daysLeft = \Carbon\Carbon::now()->diffInDays(
            \Carbon\Carbon::parse($activeSubscription->end_date),
            false
        );
    }
@endphp

            <div class="col-xl-4 col-md-6">
                <div class="card plan-card h-100">
                    <div class="card-body">
                        @if($isActive)
    <div class="active-badge">
        Active Plan
    </div>
@endif


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
                        @if($isActive)
                        <div class="renew-info">
                            {{ $daysLeft > 0 ? $daysLeft . ' days left to renew' : 'Expired' }}
                        </div>
                    @endif
                        <div class="plan-features">
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

                        <button class="btn btn-upgrade w-100 mt-4" 
                                data-bs-toggle="modal" 
                                data-bs-target="#upgradePlanModal" 
                                data-plan-id="{{ $plan->id }}" 
                                data-plan-name="{{ $plan->plan_name }}" 
                                data-total-users="{{ $plan->total_users }}" 
                                data-amount="{{ $plan->plan_amount }}"
                                data-plan-type="monthly">
                            <i class="fas fa-arrow-up me-2"></i>Upgrade to {{ $plan->plan_name }}
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Annual Plans Section -->
    <div id="annual-plans" class="plans-section" style="display: none;">
        <div class="row g-4">
            @foreach($plans->where('plan_type', 'annual') as $plan)
            @php
            $isActive = false;
            $daysLeft = null;
        
            if(isset($activeSubscription) && $activeSubscription->plan_id == $plan->id){
                $isActive = true;
                $daysLeft = \Carbon\Carbon::now()->diffInDays(
                    \Carbon\Carbon::parse($activeSubscription->end_date),
                    false
                );
            }
        @endphp
            <div class="col-xl-4 col-md-6">
                <div class="card plan-card h-100">
                    @if($isActive)
                    <div class="active-badge">
                        Active Plan
                    </div>
                @endif
                    <div class="card-body">
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
                       
                            <p class="plan-description text-muted">{{ $plan->description }}</p>
                        </div>
                        @if($isActive)
                        <div class="renew-info">
                            {{ $daysLeft > 0 ? $daysLeft . ' days left to renew' : 'Expired' }}
                        </div>
                    @endif

                        <div class="plan-features">
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

                        <button class="btn btn-upgrade w-100 mt-4" 
                                data-bs-toggle="modal" 
                                data-bs-target="#upgradePlanModal" 
                                data-plan-id="{{ $plan->id }}" 
                                data-plan-name="{{ $plan->plan_name }}" 
                                data-total-users="{{ $plan->total_users }}" 
                                data-amount="{{ $plan->plan_amount }}"
                                data-plan-type="annual">
                            <i class="fas fa-arrow-up me-2"></i>Upgrade to {{ $plan->plan_name }}
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Upgrade Plan Modal -->
    <div class="modal fade" id="upgradePlanModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Confirm Plan Upgrade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-2">
                    <div class="selected-plan-summary p-3 bg-light rounded-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Selected Plan:</span>
                            <span class="fw-bold" id="modal-plan-name"></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Team Members:</span>
                            <span class="fw-bold" id="modal-total-users"></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Amount:</span>
                            <span class="fw-bold text-orange" id="modal-amount"></span>
                        </div>
                    </div>

                    <form action="{{ route('subscribecompany.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_id" id="plan_id">
                        <input type="hidden" name="plan_name" id="plan_name">
                        <input type="hidden" name="total_users" id="total_users">
                        <input type="hidden" name="amount" id="amount">

                        <button type="button" class="btn btn-primary w-100 py-3" id="fakePaymentBtn">
                            <i class="fas fa-lock me-2"></i>Proceed to Secure Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="fakePaymentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Complete Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="payment-icon mb-4">
                        <i class="fas fa-credit-card fa-3x text-orange"></i>
                    </div>
                    
                    <h4 class="mb-3">Secure Payment</h4>
                    <p class="text-muted mb-4" id="paymentAmountText"></p>

                    <div class="payment-methods mb-4">
                        <span class="badge bg-light text-dark p-2 me-2"><i class="fab fa-cc-visa me-1"></i>Visa</span>
                        <span class="badge bg-light text-dark p-2 me-2"><i class="fab fa-cc-mastercard me-1"></i>Mastercard</span>
                        <span class="badge bg-light text-dark p-2"><i class="fab fa-cc-amex me-1"></i>Amex</span>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-success py-3" id="confirmPayment">
                            <i class="fas fa-check-circle me-2"></i>Pay Now
                        </button>
                        <button class="btn btn-outline-secondary py-2" data-bs-dismiss="modal">
                            Cancel
                        </button>
                    </div>

                    <p class="text-muted small mt-3">
                        <i class="fas fa-shield-alt me-1"></i>Your payment information is secure and encrypted
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
:root {
    --orange-primary: #f97316;
    --orange-dark: #ea580c;
    --orange-light: #fb923c;
    --orange-soft: #fff7ed;
    --secondary-color: #64748b;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --dark-bg: #1e293b;
    --light-bg: #f8fafc;
}

.text-orange {
    color: var(--orange-primary) !important;
}

.bg-orange {
    background-color: var(--orange-primary) !important;
}

.bg-orange-soft {
    background-color: var(--orange-soft) !important;
}

/* Plan Toggle Styling */
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

/* Plan Cards */
.plan-card {
    border: none;
    border-radius: 20px;
    background: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.plan-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(249, 115, 22, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
    margin-bottom: 1rem;
}

.plan-price {
    margin-bottom: 0.5rem;
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
    font-weight: 400;
}

.plan-description {
    font-size: 0.95rem;
    margin-top: 0.5rem;
}

.savings-badge {
    background: #fff7ed;
    color: var(--orange-dark);
    padding: 0.25rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    display: inline-block;
    margin-top: 0.5rem;
}

/* Features List */
.plan-features {
    padding: 1.5rem 1rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    font-size: 0.95rem;
    color: #475569;
}

.feature-item i {
    width: 20px;
    font-size: 1rem;
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
    letter-spacing: 0.5px;
}

/* Buttons */
.btn-upgrade {
    background: linear-gradient(135deg, var(--orange-primary), var(--orange-dark));
    color: white;
    font-weight: 600;
    padding: 1rem;
    border: none;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.btn-upgrade:hover {
    transform: scale(1.02);
    box-shadow: 0 10px 20px -10px var(--orange-primary));
    color: white;
}

.btn-primary {
    background: var(--orange-primary) !important;
    border-color: var(--orange-primary) !important;
}

.btn-primary:hover {
    background: var(--orange-dark) !important;
    border-color: var(--orange-dark) !important;
}

/* Modal Styling */
.modal-content {
    border: none;
    border-radius: 20px;
}

.selected-plan-summary {
    background: linear-gradient(135deg, #fff7ed, #ffedd5);
    border: 1px solid #fed7aa;
}

.text-orange {
    color: var(--orange-primary) !important;
}

.payment-methods .badge {
    font-size: 0.9rem;
    border: 1px solid #e2e8f0;
}

.payment-icon {
    animation: float 3s ease-in-out infinite;
}
.active-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: #10b981;
    color: white;
    padding: 5px 12px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 20px;
}

.renew-info {
    margin-top: 10px;
    font-size: 14px;
    font-weight: 600;
    color: #ef4444;
}


@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .plan-toggle .btn-plan {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
    
    .plan-price .amount {
        font-size: 2.5rem;
    }
    
    .popular-badge {
        right: -40px;
        padding: 6px 35px;
        font-size: 0.75rem;
    }
}
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Plan toggle functionality
    const monthlyBtn = document.getElementById('monthly-plan-btn');
    const annualBtn = document.getElementById('annual-plan-btn');
    const monthlyPlans = document.getElementById('monthly-plans');
    const annualPlans = document.getElementById('annual-plans');

    monthlyBtn.addEventListener('click', function() {
        this.classList.add('active');
        annualBtn.classList.remove('active');
        monthlyPlans.style.display = 'block';
        annualPlans.style.display = 'none';
    });

    annualBtn.addEventListener('click', function() {
        this.classList.add('active');
        monthlyBtn.classList.remove('active');
        monthlyPlans.style.display = 'none';
        annualPlans.style.display = 'block';
    });

    // Modal data population
    const upgradeButtons = document.querySelectorAll('[data-bs-target="#upgradePlanModal"]');
    let selectedAmount = 0;

    upgradeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const planId = this.getAttribute('data-plan-id');
            const planName = this.getAttribute('data-plan-name');
            const totalUsers = this.getAttribute('data-total-users');
            const amount = this.getAttribute('data-amount');
            const planType = this.getAttribute('data-plan-type');

            selectedAmount = amount;

            document.getElementById('plan_id').value = planId;
            document.getElementById('plan_name').value = planName;
            document.getElementById('total_users').value = totalUsers;
            document.getElementById('amount').value = amount;

            document.getElementById('modal-plan-name').textContent = planName;
            document.getElementById('modal-total-users').textContent = totalUsers + ' members';
            document.getElementById('modal-amount').textContent = '₹' + amount + '/' + (planType === 'monthly' ? 'mo' : 'yr');
        });
    });

    // Payment flow
    document.getElementById('fakePaymentBtn').addEventListener('click', function() {
        const paymentModal = new bootstrap.Modal(document.getElementById('fakePaymentModal'));
        document.getElementById('paymentAmountText').innerHTML = 
            `Amount to Pay: <strong class="text-orange">₹${selectedAmount}</strong>`;
        paymentModal.show();
    });

    document.getElementById('confirmPayment').addEventListener('click', function() {
        Swal.fire({
            icon: 'success',
            title: 'Payment Successful!',
            text: 'Your plan has been upgraded successfully.',
            showConfirmButton: true,
            confirmButtonText: 'Continue',
            confirmButtonColor: '#f97316',
            timer: 3000,
            timerProgressBar: true
        }).then(() => {
            document.querySelector('#upgradePlanModal form').submit();
        });
    });
});
</script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection