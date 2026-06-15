@php
$userRole = Session::get('role');
$userId = Session::get('user_id');
$adminId = Session::get('admin_id');

$modules = [];

if ($userRole === 'employee' && $userId) {
    $modules = DB::table('employee_module_access')
        ->where('employee_id', $userId)
        ->pluck('module_name')
        ->toArray();

} elseif ($userRole === 'admin' && $adminId) {
    $modules = DB::table('admin_module_access')
        ->where('admin_id', $adminId)
        ->pluck('module_name')
        ->toArray();
}
@endphp
@extends('layouts.index')

@section('content')

<style>
/* ===== Page Header ===== */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 28px;
}

.page-header h2 {
    font-weight: 700;
}

/* ===== Search Box ===== */
.search-box {
    position: relative;
}

.search-box input {
    padding-left: 36px;
    border-radius: 30px;
    height: 40px;
    width: 260px;
}

.search-box i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
}

/* ===== Services Grid ===== */
.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 24px;
}

/* ===== Service Card ===== */
.service-card {
    background: #fff;
    border-radius: 16px;
    padding: 28px 20px;
    text-align: center;
    box-shadow: 0 6px 20px rgba(0,0,0,0.06);
    transition: all 0.25s ease;
    cursor: pointer;
}

.service-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 28px rgba(0,0,0,0.12);
}

/* ===== Icon ===== */
.service-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: #fff4ec;
    color: #ff7f2a;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin: 0 auto 14px;
}

/* ===== Title ===== */
.service-title {
    font-weight: 600;
    font-size: 15px;
    color: #222;
}
</style>

<div class="container-fluid">

    <!-- ===== Header ===== -->
    <div class="page-header">
        <h2>Accounts</h2>
    </div>

    <div class="services-grid">

        {{-- Estimates --}}
        @if(in_array('Estimates', $modules))
            <a href="{{ route('estimate.index') }}" class="service-card text-decoration-none">
                <div class="service-icon">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                <div class="service-title">Estimates</div>
            </a>
        @endif
    
        {{-- Invoices --}}
        @if(in_array('Invoices', $modules))
            <a href="{{ route('invoice.index') }}" class="service-card text-decoration-none">
                <div class="service-icon">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div class="service-title">Invoices</div>
            </a>
        @endif
    
        {{-- Payments --}}
        @if($userRole === 'admin' || in_array('Payments', $modules))
            <a href="{{ route('payment.index') }}" class="service-card text-decoration-none">
                <div class="service-icon">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
                <div class="service-title">Payments</div>
            </a>
        @endif
    
        {{-- Expenses --}}
        @if(in_array('Expenses', $modules))
            <a href="{{ route('expense.index') }}" class="service-card text-decoration-none">
                <div class="service-icon">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <div class="service-title">Expenses</div>
            </a>
        @endif
    
        {{-- Taxes --}}
        @if(in_array('Taxes', $modules))
            <a href="{{ route('tax.index') }}" class="service-card text-decoration-none">
                <div class="service-icon">
                    <i class="fa-solid fa-percent"></i>
                </div>
                <div class="service-title">Taxes</div>
            </a>
        @endif
    
        {{-- Categories --}}
        @if(in_array('Categories', $modules))
            <a href="{{ route('categories.index') }}" class="service-card text-decoration-none">
                <div class="service-icon">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div class="service-title">Categories</div>
            </a>
        @endif
    
        {{-- Budgets --}}
        @if(in_array('Budgets', $modules))
            <a href="{{ route('budgets.index') }}" class="service-card text-decoration-none">
                <div class="service-icon">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div class="service-title">Budgets</div>
            </a>
        @endif
    
        {{-- Budget Expenses --}}
        @if(in_array('Budget Expenses', $modules))
            <a href="{{ route('budgetexpenses.index') }}" class="service-card text-decoration-none">
                <div class="service-icon">
                    <i class="fa-solid fa-arrow-down"></i>
                </div>
                <div class="service-title">Budget Expenses</div>
            </a>
        @endif
    
        {{-- Budget Revenues --}}
        @if(in_array('Budget Revenues', $modules))
            <a href="{{ route('budgetrevenue.index') }}" class="service-card text-decoration-none">
                <div class="service-icon">
                    <i class="fa-solid fa-arrow-up"></i>
                </div>
                <div class="service-title">Budget Revenues</div>
            </a>
        @endif
    
    </div>
    
</div>

@endsection
