@extends('layouts.index')
@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Admin Details</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('adminaccess.index') }}" class="breadcrumb-link">Admin List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">View Details</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Admin Information Card -->
            <div class="col-xl-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Admin Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="avatar-lg mx-auto">
                                <div class="avatar-title bg-primary rounded-circle" style="width: 80px; height: 80px; font-size: 2rem;">
                                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                                </div>
                            </div>
                            <h4 class="mt-3 mb-1">{{ $admin->name }}</h4>
                            <p class="text-muted">{{ $admin->email }}</p>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td><strong>Admin ID:</strong></td>
                                        <td>{{ $admin->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $admin->email }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Hierarchy Level:</strong></td>
                                        <td>
                                            @if($admin->hierarchy_level)
                                                <span class="badge bg-info">{{ $admin->hierarchy_level }}</span>
                                            @else
                                                <span class="badge bg-secondary">No Hierarchy</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            @if($admin->status == 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created Date:</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($admin->created_at)->format('d M Y, h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Last Updated:</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($admin->updated_at)->format('d M Y, h:i A') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('adminaccess.edit', $admin->id) }}" class="btn btn-warning btn-sm me-2">
                                <i class="fas fa-edit me-1"></i>Edit Admin
                            </a>
                            <a href="{{ route('adminaccess.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Module Access Card -->
            <div class="col-xl-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-key me-2"></i>Module Access Permissions</h5>
                        <div>
                            <span class="badge bg-light text-dark">{{ $adminModules->count() }} Total Modules</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($adminModules->count() > 0)
                            <!-- Module Statistics -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $adminModules->where('source', 'hierarchy')->count() }}</h3>
                                            <p class="mb-0">From Hierarchy</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $adminModules->where('source', 'manual')->count() }}</h3>
                                            <p class="mb-0">Manual Selection</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $adminModules->count() }}</h3>
                                            <p class="mb-0">Total Modules</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Search Box -->
                            <div class="mb-3">
                                <input type="text" id="moduleSearch" class="form-control" placeholder="Search modules...">
                            </div>

                            <!-- Module List -->
                            <div class="row">
                                @php
                                    $moduleCategories = [
                                        'Employee Management' => ['All Employees', 'Departments', 'Designations', 'Policies', 'Promotion', 'Resignation', 'Termination'],
                                        'Attendance & Leaves' => ['Holidays', 'Leaves (Admin)', 'Leaves (Employee)', 'Leave Settings', 'Attendance (Admin)', 'Attendance (Employee)', 'Shift & Schedule', 'Overtime'],
                                        'Projects & Tasks' => ['Clients', 'Projects', 'Tasks', 'Task Board', 'Timesheet'],
                                        'Finance' => ['Estimates', 'Invoices', 'Payments', 'Expenses', 'Employee Salary', 'Payroll Items', 'Provident Fund', 'Taxes'],
                                        'Reports' => ['Expense Report', 'Invoice Report', 'Payments Report', 'Project Report', 'Task Report', 'User Report', 'Employee Reports', 'Payslip Report', 'Leave Report', 'Daily Report'],
                                        'System' => ['Users', 'Roles', 'Subscriptions (Admin)', 'Subscriptions (Company)', 'Subscribed Companies', 'Knowledgebase', 'FAQ']
                                    ];
                                    $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                                @endphp

                                @foreach($moduleCategories as $category => $categoryModules)
                                    @php
                                        $categoryColor = $colors[$loop->index % count($colors)];
                                        $adminCategoryModules = $adminModules->whereIn('module_name', $categoryModules);
                                    @endphp
                                    
                                    @if($adminCategoryModules->count() > 0)
                                        <div class="col-md-6 mb-4">
                                            <div class="card border-{{ $categoryColor }}">
                                                <div class="card-header bg-{{ $categoryColor }} text-white">
                                                    <h6 class="mb-0">
                                                        <i class="fas fa-folder me-2"></i>{{ $category }}
                                                        <span class="badge bg-light text-dark ms-2">{{ $adminCategoryModules->count() }}</span>
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    @foreach($adminCategoryModules as $module)
                                                        <div class="module-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                                            <span>{{ $module->module_name }}</span>
                                                            <div>
                                                                @if($module->source == 'hierarchy')
                                                                    <span class="badge bg-primary" title="From Hierarchy">
                                                                        <i class="fas fa-sitemap me-1"></i>Hierarchy
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-success" title="Manual Selection">
                                                                        <i class="fas fa-hand-pointer me-1"></i>Manual
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <h5>No Module Access</h5>
                                <p class="text-muted">This admin has no module access permissions assigned.</p>
                                <a href="{{ route('adminaccess.edit', $admin->id) }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Assign Modules
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-lg {
    width: 80px;
    height: 80px;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.module-item:last-child {
    border-bottom: none !important;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Module search functionality
    document.getElementById('moduleSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const moduleItems = document.querySelectorAll('.module-item');
        
        moduleItems.forEach(function(item) {
            const moduleName = item.querySelector('span').textContent.toLowerCase();
            const card = item.closest('.card');
            
            if (moduleName.includes(searchTerm)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
        
        // Hide/show category cards if no modules are visible
        document.querySelectorAll('.card').forEach(function(card) {
            const visibleModules = card.querySelectorAll('.module-item[style*="flex"]');
            const allModules = card.querySelectorAll('.module-item');
            
            if (allModules.length > 0) {
                if (visibleModules.length === 0 && searchTerm !== '') {
                    card.style.display = 'none';
                } else {
                    card.style.display = 'block';
                }
            }
        });
    });
});
</script>
@endsection