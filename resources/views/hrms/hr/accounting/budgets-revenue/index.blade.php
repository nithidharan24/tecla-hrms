@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Budget Revenues');
@endphp

@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Budgets Revenues</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                    <li class="breadcrumb-item active">Accounts</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                    <i class="fa fa-plus"></i> Add New Revenues
                </a>
                @endif
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Success/Error Messages -->
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- ZOHO STYLE TABS -->
    <ul class="nav leave-tabs mb-0" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#summary-tab">
                <i class="fa fa-chart-bar me-1"></i> Revenues Summary
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#list-tab">
                <i class="fa fa-list me-1"></i> List
                <span class="badge bg-primary ms-1">{{ $expenses->count() }}</span>
            </a>
        </li>
    </ul>

    <!-- TAB CONTENT AREA -->
    <div class="tab-content pt-4">

        <!-- TAB 1 : SUMMARY -->
        <div class="tab-pane fade show active" id="summary-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="card-group m-b-30">
                        <!-- Total Revenues Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-money-bill-alt text-primary"></i> Total Revenues
                                    </span>
                                    <span class="text-primary">{{ $expenses->count() }}</span>
                                </div>
                                <h3 class="mb-3 text-primary">{{ $expenses->count() }}</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-primary" style="width: 100%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">All revenue records</small>
                            </div>
                        </div>

                        <!-- Total Revenue Amount -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-chart-line text-success"></i> Total Revenue
                                    </span>
                                    <span class="text-success">All Revenues</span>
                                </div>
                                @php
                                    $totalAmount = $expenses->sum('amount');
                                @endphp
                                <h3 class="mb-3 text-success">₹{{ number_format($totalAmount, 2) }}</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-success" style="width: 100%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">Total revenue amount</small>
                            </div>
                        </div>

                        <!-- Average Revenue Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-calculator text-warning"></i> Average Revenue
                                    </span>
                                    <span class="text-warning">Per Record</span>
                                </div>
                                @php
                                    $averageAmount = $expenses->count() > 0 ? round($expenses->avg('amount'), 2) : 0;
                                @endphp
                                <h3 class="mb-3 text-warning">₹{{ number_format($averageAmount, 2) }}</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-warning" style="width: 100%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">Average revenue per record</small>
                            </div>
                        </div>

                        <!-- This Month Revenue -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="d-block">
                                        <i class="fa fa-calendar-check text-info"></i> This Month
                                    </span>
                                    <span class="text-info">{{ \Carbon\Carbon::now()->format('M Y') }}</span>
                                </div>
                                @php
                                    $currentMonth = \Carbon\Carbon::now()->month;
                                    $currentYear = \Carbon\Carbon::now()->year;
                                    $monthRevenues = $expenses->filter(function($expense) use ($currentMonth, $currentYear) {
                                        $revenueDate = \Carbon\Carbon::parse($expense->{'revenue-date'});
                                        return $revenueDate->month == $currentMonth && $revenueDate->year == $currentYear;
                                    });
                                    $monthTotal = $monthRevenues->sum('amount');
                                @endphp
                                <h3 class="mb-3 text-info">₹{{ number_format($monthTotal, 2) }}</h3>
                                <div class="progress height-five mb-2">
                                    <div class="progress-bar bg-info" style="width: 100%" role="progressbar"></div>
                                </div>
                                <small class="text-muted">Revenue this month</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END TAB 1 -->

        <!-- TAB 2 : LIST WITH FILTER -->
        <div class="tab-pane fade" id="list-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Top Bar with Filter Button -->
                            <div class="d-flex justify-content-between align-items-center mb-3" style="margin-top:-10px;">
                                <h4 class="fw-bold mb-0">All Revenues</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <button id="openRevenueFilterBtn" class="filter-square-btn">
                                        <i class="fa-solid fa-filter"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Filter Panel (right slide) -->
                            <div id="revenueFilterPanel" class="filter-slide-panel">
                                <div class="filter-header d-flex justify-content-between align-items-center p-3 border-bottom">
                                    <h5 class="mb-0">Filter Revenues</h5>
                                    <button id="closeRevenueFilterBtn" class="btn btn-sm btn-light">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>

                                <form class="p-3" method="GET" action="{{ route('budgetrevenue.index') }}" id="filterForm">
                                    @if(request()->has('search') || request()->has('category') || request()->has('date_range'))
                                    <div class="mb-3">
                                        <div class="alert alert-info py-2">
                                            <small><i class="fa fa-info-circle me-1"></i> Active filters applied</small>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Search Notes</label>
                                        <input type="text" name="search" value="{{ request('search') }}" 
                                               class="form-control" placeholder="Search by notes...">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Category</label>
                                        <select name="category" class="form-select">
                                            <option value="">All Categories</option>
                                            @foreach($categories as $categoryId => $categoryGroup)
                                                @php $category = $categoryGroup->first(); @endphp
                                                <option value="{{ $category->category_id }}" {{ request('category') == $category->category_id ? 'selected' : '' }}>
                                                    {{ $category->category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Amount Range</label>
                                        <select name="amount_range" class="form-select">
                                            <option value="">All Amounts</option>
                                            <option value="0-1000" {{ request('amount_range') == '0-1000' ? 'selected' : '' }}>₹0 - ₹1,000</option>
                                            <option value="1001-5000" {{ request('amount_range') == '1001-5000' ? 'selected' : '' }}>₹1,001 - ₹5,000</option>
                                            <option value="5001-10000" {{ request('amount_range') == '5001-10000' ? 'selected' : '' }}>₹5,001 - ₹10,000</option>
                                            <option value="10001-50000" {{ request('amount_range') == '10001-50000' ? 'selected' : '' }}>₹10,001 - ₹50,000</option>
                                            <option value="50001-100000" {{ request('amount_range') == '50001-100000' ? 'selected' : '' }}>₹50,001 - ₹100,000</option>
                                            <option value="100001+" {{ request('amount_range') == '100001+' ? 'selected' : '' }}>₹100,001+</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Date Range</label>
                                        <select name="date_range" class="form-select" id="dateRangeSelect">
                                            <option value="">All Time</option>
                                            <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                                            <option value="this_week" {{ request('date_range') == 'this_week' ? 'selected' : '' }}>This Week</option>
                                            <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                                            <option value="this_year" {{ request('date_range') == 'this_year' ? 'selected' : '' }}>This Year</option>
                                            <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                                        </select>
                                    </div>

                                    <div id="customDateRange" class="mb-3" style="display: none;">
                                        <div class="row">
                                            <div class="col-6">
                                                <label class="form-label small">Start Date</label>
                                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small">End Date</label>
                                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2 mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-search me-1"></i> Apply Filters
                                        </button>
                                        <button type="button" class="btn btn-light border" onclick="resetFilters()">
                                            <i class="fa fa-refresh me-1"></i> Reset Filters
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Active Filters Display -->
                            @if(request()->has('search') || request()->has('category') || request()->has('amount_range') || request()->has('date_range'))
                            <div class="alert alert-light border mb-3 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted me-2">Active Filters:</small>
                                        @if(request('search'))
                                        <span class="badge bg-info me-2">
                                            Search: "{{ request('search') }}"
                                            <a href="?{{ http_build_query(request()->except('search')) }}" class="text-white ms-1">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </span>
                                        @endif
                                        @if(request('category'))
                                        @php
                                            $selectedCategory = $categories->firstWhere('*.category_id', request('category'));
                                            $categoryName = $selectedCategory ? $selectedCategory->first()->category_name : '';
                                        @endphp
                                        <span class="badge bg-info me-2">
                                            Category: {{ $categoryName }}
                                            <a href="?{{ http_build_query(request()->except('category')) }}" class="text-white ms-1">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </span>
                                        @endif
                                        @if(request('amount_range'))
                                        <span class="badge bg-info me-2">
                                            Amount: {{ request('amount_range') }}
                                            <a href="?{{ http_build_query(request()->except('amount_range')) }}" class="text-white ms-1">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </span>
                                        @endif
                                        @if(request('date_range'))
                                        <span class="badge bg-info me-2">
                                            Date: {{ ucfirst(str_replace('_', ' ', request('date_range'))) }}
                                            <a href="?{{ http_build_query(request()->except('date_range', 'start_date', 'end_date')) }}" class="text-white ms-1">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </span>
                                        @endif
                                    </div>
                                    <a href="{{ route('budgetrevenue.index') }}" class="btn btn-sm btn-outline-secondary">
                                        Clear All
                                    </a>
                                </div>
                            </div>
                            @endif

                            <!-- Revenues Table -->
                            <div class="table-responsive">
                                @if($expenses->count())
                                    <table id="budget-revenue-table" class="table custom-table datatable">
                                        <thead>
                                            <tr>
                                                <th style="width:36px;">
                                                    <input type="checkbox" class="od-check" id="checkAllRevenue" aria-label="Select all">
                                                </th>
                                                <th>ID</th>
                                                <th>Notes</th>
                                                <th>Category</th>
                                                <th>Subcategory</th>
                                                <th>Amount</th>
                                                <th>Revenue Date</th>
                                                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                    <th class="text-end">Actions</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($expenses as $expense)
                                            <tr id="revenue-row-{{ $expense->id }}">
                                                <td>
                                                    <input type="checkbox" class="od-check row-check-revenue" aria-label="Select row">
                                                </td>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><span class="od-chip-highlight">{{ $expense->Notes }}</span></td>
                                                <td>{{ $expense->category_name }}</td>
                                                <td>{{ $expense->subcategory_name }}</td>
                                                <td><span class="od-chip-highlight">₹{{ number_format($expense->amount, 2) }}</span></td>
                                                <td>{{ \Carbon\Carbon::parse($expense->{'revenue-date'})->format('M d, Y') }}</td>
                                                <td class="text-end">
                                                    <div class="od-inline-actions">
                                                        @if(isset($permissions) && $permissions->can_edit)
                                                        <a class="od-icon-btn" data-bs-toggle="modal" data-bs-target="#edit_budget_expense_{{ $expense->id }}" title="Edit">
                                                            <i class="fa-solid fa-pencil"></i>
                                                        </a>
                                                        @endif
                                                        @if(isset($permissions) && $permissions->can_delete)
                                                        <a class="od-icon-btn danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal_{{ $expense->id }}" title="Delete">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="alert alert-light text-center py-4">
                                        <i class="fa fa-money-bill-alt fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No Revenues Found</h5>
                                        <p class="text-muted">
                                            @if(request()->has('search') || request()->has('category') || request()->has('amount_range') || request()->has('date_range'))
                                            No revenues found matching your filter criteria.
                                            @else
                                            No revenues have been recorded yet.
                                            @endif
                                        </p>
                                        @if(isset($permissions) && $permissions->can_create)
                                        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                                            <i class="fa fa-plus me-2"></i> Add Revenue
                                        </a>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Results Count -->
                            <div class="text-muted mt-2 small">
                                Showing {{ $expenses->count() }} revenues • Total: ₹{{ number_format($expenses->sum('amount'), 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END TAB 2 -->

    </div>
    <!-- END tab-content -->
</div>

<!-- ALL MODALS ARE PLACED HERE AT THE BOTTOM -->

<!-- Add Revenue Modal -->
<div id="addExpenseModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Budget Revenue</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('budgetrevenue.store') }}" method="POST" enctype="multipart/form-data" id="addRevenueForm">
                    @csrf

                    <div class="form-group m-4">
                        <label for="amount">Amount<span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control amount-input" id="amountInput" required>
                        <div class="invalid-feedback" id="amountError">
                            Please enter a positive amount.
                        </div>
                    </div>

                    <div class="form-group m-4">
                        <label for="notes">Notes<span class="text-danger">*</span></label>
                        <textarea name="notes" class="form-control" rows="3" required></textarea>
                    </div>

                    <!-- Category Dropdown -->
                    <div class="form-group m-4">
                        <label for="category">Category<span class="text-danger">*</span></label>
                        <select name="category" class="form-select category-select" id="categorySelect" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $categoryId => $categoryGroup)
                                @php
                                    $category = $categoryGroup->first();
                                @endphp
                                <option value="{{ $category->category_id }}">
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Subcategory Dropdown -->
                    <div class="form-group m-4">
                        <label for="sub_category">Subcategory<span class="text-danger">*</span></label>
                        <select name="sub_category" class="form-select subcategory-select" id="subcategorySelect" required disabled>
                            <option value="">Select Subcategory</option>
                            @foreach($categories as $categoryGroup)
                                @foreach($categoryGroup as $subcategory)
                                    @if($subcategory->subcategory_id)
                                        <option value="{{ $subcategory->subcategory_id }}" 
                                            data-category="{{ $subcategory->category_id }}">
                                            {{ $subcategory->subcategory_name }}
                                        </option>
                                    @endif
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group m-4">
                        <label for="expense_date">Revenue Date <span class="text-danger">*</span></label>
                        <input type="date" name="expense_date" class="form-control" required>
                    </div>

                    <div class="form-group m-4">
                        <label for="img">Attach File<span class="text-danger">*</span></label>
                        <input type="file" name="img" class="form-control" required>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="submitBtn">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@foreach($expenses as $expense)
<!-- Edit Modal for {{ $expense->id }} -->
<div id="edit_budget_expense_{{ $expense->id }}" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Budget Revenue</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('budgetrevenue.update', $expense->id) }}" method="POST" enctype="multipart/form-data" class="edit-revenue-form">
                    @csrf
                    @method('PUT')

                    <div class="form-group m-4">
                        <label>Amount<span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control amount-input" value="{{ $expense->amount }}" required>
                        <div class="invalid-feedback amount-error">Please enter a positive amount.</div>
                    </div>

                    <div class="form-group m-4">
                        <label>Notes<span class="text-danger">*</span></label>
                        <textarea name="notes" class="form-control" rows="3" required>{{ $expense->Notes }}</textarea>
                    </div>

                    <div class="form-group m-4">
                        <label>Category<span class="text-danger">*</span></label>
                        <select name="category" class="form-select category-select" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $categoryId => $categoryGroup)
                                @php $category = $categoryGroup->first(); @endphp
                                <option value="{{ $category->category_id }}" {{ $category->category_id == $expense->categories ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group m-4">
                        <label>Subcategory<span class="text-danger">*</span></label>
                        <select name="sub_category" class="form-select subcategory-select" required>
                            <option value="">Select Subcategory</option>
                            @foreach($categories as $categoryGroup)
                                @foreach($categoryGroup as $subcategory)
                                    @if($subcategory->subcategory_id)
                                        <option value="{{ $subcategory->subcategory_id }}" 
                                            data-category="{{ $subcategory->category_id }}"
                                            {{ $subcategory->subcategory_id == $expense->{'sub-categories'} ? 'selected' : '' }}>
                                            {{ $subcategory->subcategory_name }}
                                        </option>
                                    @endif
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group m-4">
                        <label>Revenue Date <span class="text-danger">*</span></label>
                        <input type="date" name="expense_date" class="form-control" value="{{ \Carbon\Carbon::parse($expense->{'revenue-date'})->format('Y-m-d') }}" required>
                    </div>

                    <div class="form-group m-4">
                        <label>Attach File</label>
                        <input type="file" name="img" class="form-control">
                        @if ($expense->img)
                            <div class="mt-2">
                                <p>Current File: <span class="text-muted">{{ basename($expense->img) }}</span></p>
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary update-btn">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal for {{ $expense->id }} -->
<div class="modal fade" id="confirmDeleteModal_{{ $expense->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete "{{ $expense->Notes }}" revenue?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm_{{ $expense->id }}" action="{{ route('budgetrevenue.destroy', $expense->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
    /* Zoho Style Tabs */
    .leave-tabs .nav-link {
        font-size: 15px;
        font-weight: 500;
        color: #333;
        margin-top: -25px;
        border-bottom: 3px solid transparent;
        padding: 10px 18px;
    }

    .leave-tabs .nav-link.active {
        color: #f97316;
        border-bottom: 3px solid #f97316;
    }

    /* Filter Slide Panel Styles */
    .filter-slide-panel {
        position: fixed;
        top: 0;
        right: -400px;
        width: 380px;
        height: 100vh;
        background: #fff;
        box-shadow: -2px 0 10px rgba(0,0,0,0.1);
        z-index: 1050;
        transition: right 0.3s ease;
        overflow-y: auto;
    }

    .filter-slide-panel.active {
        right: 0;
    }

    .filter-square-btn {
        width: 42px;
        height: 42px;
        background: #fff;
        border: 1px solid #d6d6d6;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #595959;
        cursor: pointer;
        transition: 0.2s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }

    .filter-square-btn:hover {
        background: #f5f5f5;
    }

    /* Active Filters Badges */
    .badge.bg-info a {
        text-decoration: none;
        opacity: 0.8;
    }
    
    .badge.bg-info a:hover {
        opacity: 1;
    }

    /* Tab Content Area */
    .tab-content {
        margin-top: 20px;
    }

    /* Card Group Styling */
    .card-group.m-b-30 .card {
        border: 1px solid #e5eaf2;
        border-radius: 8px;
        margin-right: 15px;
    }

    .card-group.m-b-30 .card:last-child {
        margin-right: 0;
    }

    /* Progress Bar */
    .height-five {
        height: 5px;
    }

    /* Alert Styling */
    .alert-dismissible .btn-close {
        position: absolute;
        top: 0;
        right: 0;
        padding: 1.25rem 1rem;
    }

    /* Form Validation */
    .is-invalid {
        border-color: #dc3545 !important;
    }
    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875em;
        width: 100%;
        margin-top: 0.25rem;
        display: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                alert.style.transition = 'all 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 500);
            });
        }, 5000);

        // Filter Panel Functionality
        const openFilterBtn = document.getElementById('openRevenueFilterBtn');
        const closeFilterBtn = document.getElementById('closeRevenueFilterBtn');
        const filterPanel = document.getElementById('revenueFilterPanel');
        const dateRangeSelect = document.getElementById('dateRangeSelect');
        const customDateRange = document.getElementById('customDateRange');

        if (openFilterBtn) {
            openFilterBtn.onclick = () => {
                filterPanel.classList.add('active');
            };
        }

        if (closeFilterBtn) {
            closeFilterBtn.onclick = () => {
                filterPanel.classList.remove('active');
            };
        }

        // Close filter when clicking outside
        document.addEventListener('click', (e) => {
            if (filterPanel.classList.contains('active') && 
                !filterPanel.contains(e.target) && 
                e.target !== openFilterBtn) {
                filterPanel.classList.remove('active');
            }
        });

        // Show/hide custom date range
        if (dateRangeSelect && customDateRange) {
            // Check on page load
            if (dateRangeSelect.value === 'custom') {
                customDateRange.style.display = 'block';
            }

            dateRangeSelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customDateRange.style.display = 'block';
                } else {
                    customDateRange.style.display = 'none';
                }
            });
        }

        // Handle individual filter badge removal
        document.querySelectorAll('.badge.bg-info a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                window.location.href = url;
            });
        });

        // Check All functionality
        const checkAllRevenue = document.getElementById('checkAllRevenue');
        const rowsRevenue = document.querySelectorAll('.row-check-revenue');

        if (checkAllRevenue) {
            checkAllRevenue.addEventListener('change', function() {
                rowsRevenue.forEach(r => {
                    r.checked = this.checked;
                    r.closest('tr').classList.toggle('od-selected', this.checked);
                });
            });
        }

        rowsRevenue.forEach(r => {
            r.addEventListener('change', function() {
                this.closest('tr').classList.toggle('od-selected', this.checked);
            });
        });

        // Form validation for Add Modal
        const addForm = document.getElementById('addRevenueForm');
        const amountInput = document.getElementById('amountInput');
        const amountError = document.getElementById('amountError');
        const submitBtn = document.getElementById('submitBtn');
        
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                if (parseFloat(amountInput.value) <= 0 || isNaN(parseFloat(amountInput.value))) {
                    e.preventDefault();
                    amountInput.classList.add('is-invalid');
                    amountError.style.display = 'block';
                }
            });
            
            amountInput.addEventListener('input', function() {
                if (parseFloat(this.value) > 0) {
                    this.classList.remove('is-invalid');
                    amountError.style.display = 'none';
                }
            });
        }

        // Form validation for Edit Modals
        document.querySelectorAll('.edit-revenue-form').forEach(form => {
            const amountInput = form.querySelector('.amount-input');
            const amountError = form.querySelector('.amount-error');
            const updateBtn = form.querySelector('.update-btn');
            
            form.addEventListener('submit', function(e) {
                if (parseFloat(amountInput.value) <= 0 || isNaN(parseFloat(amountInput.value))) {
                    e.preventDefault();
                    amountInput.classList.add('is-invalid');
                    amountError.style.display = 'block';
                }
            });
            
            amountInput.addEventListener('input', function() {
                if (parseFloat(this.value) > 0) {
                    this.classList.remove('is-invalid');
                    amountError.style.display = 'none';
                }
            });
        });

        // Category-Subcategory relationship for Add Modal
        const categorySelect = document.getElementById('categorySelect');
        const subcategorySelect = document.getElementById('subcategorySelect');
        const subcategoryOptions = subcategorySelect.querySelectorAll('option');
        
        if (categorySelect) {
            categorySelect.addEventListener('change', function() {
                const selectedCategoryId = this.value;
                
                // Enable/disable subcategory select
                subcategorySelect.disabled = !selectedCategoryId;
                
                // Show/hide relevant subcategories
                subcategoryOptions.forEach(option => {
                    if (option.value === "" || option.dataset.category === selectedCategoryId) {
                        option.style.display = 'block';
                    } else {
                        option.style.display = 'none';
                    }
                });
                
                // Reset subcategory selection
                subcategorySelect.value = '';
            });
        }

        // Category-Subcategory relationship for Edit Modals
        document.querySelectorAll('.edit-revenue-form').forEach(form => {
            const categorySelect = form.querySelector('.category-select');
            const subcategorySelect = form.querySelector('.subcategory-select');
            const subcategoryOptions = subcategorySelect.querySelectorAll('option');
            
            if (categorySelect) {
                categorySelect.addEventListener('change', function() {
                    const selectedCategoryId = this.value;
                    
                    // Show/hide relevant subcategories
                    subcategoryOptions.forEach(option => {
                        if (option.value === "" || option.dataset.category === selectedCategoryId) {
                            option.style.display = 'block';
                        } else {
                            option.style.display = 'none';
                        }
                    });
                    
                    // Reset selection if current selection doesn't belong to the category
                    if (subcategorySelect.value && 
                        subcategorySelect.querySelector(`option[value="${subcategorySelect.value}"]`).dataset.category !== selectedCategoryId) {
                        subcategorySelect.value = '';
                    }
                });
                
                // Initialize on load
                categorySelect.dispatchEvent(new Event('change'));
            }
        });

        // Submit button loading state for Add Modal
        if (submitBtn) {
            submitBtn.addEventListener('click', function(e) {
                if (parseFloat(amountInput.value) <= 0 || isNaN(parseFloat(amountInput.value))) {
                    e.preventDefault();
                    amountInput.classList.add('is-invalid');
                    amountError.style.display = 'block';
                    return;
                }
                
                // Disable the button and show loading state
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';
                this.form.submit();
            });
        }
    });

    // Reset filters function
    function resetFilters() {
        // Navigate to base URL (without filters)
        window.location.href = "{{ route('budgetrevenue.index') }}";
    }
</script>
@endsection