@php
    use Carbon\Carbon;
    $employeeId = Session::get('user_id');
    $role = Session::get('role');
    $permissions = App\Helpers\PermissionHelper::getPermissions('AssetsAssignment');
@endphp
@extends('layouts.index')

@section('content')
    <div class="content container-fluid">
      
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" id="alert-message">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="alert-message">
                <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="alert-message">
                <i class="fa fa-exclamation-circle"></i> 
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <script>
            setTimeout(function() {
                let alert = document.getElementById('alert-message');
                if (alert) {
                    let bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 3000);
        </script>

        {{-- Tabs --}}
        <div class="row">
            <div class="col-md-12">
                <ul class="custom-report-tabs" role="tablist">
                    {{-- Assets Management Tabs --}}
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-assets-tab" data-bs-toggle="tab"
                                data-bs-target="#all-assets" type="button" role="tab">
                            All Assets ({{ $assets->total() ?? 0 }})
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="available-assets-tab" data-bs-toggle="tab"
                                data-bs-target="#available-assets" type="button" role="tab">
                            Available ({{ $availableCount ?? 0 }})
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="out-of-stock-assets-tab" data-bs-toggle="tab"
                                data-bs-target="#out-of-stock-assets" type="button" role="tab">
                            Out of Stock ({{ $outOfStockCount ?? 0 }})
                        </button>
                    </li>

                    {{-- Assets Assignment Tabs --}}
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="all-assignments-tab" data-bs-toggle="tab"
                                data-bs-target="#all-assignments" type="button" role="tab">
                            All Assignments ({{ count($assignments) }})
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="assigned-tab" data-bs-toggle="tab"
                                data-bs-target="#assigned" type="button" role="tab">
                            Assigned ({{ $assignedCount ?? 0 }})
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="returned-tab" data-bs-toggle="tab"
                                data-bs-target="#returned" type="button" role="tab">
                            Returned ({{ $returnedCount ?? 0 }})
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Assets Management Page Header --}}
        <div class="page-heade2r assets-header">
            <div class="row align-items-center">
                <div class="col">
                   
                </div>
                <div class="col-auto float-end ms-auto d-flex">
                    <a href="{{ route('assets.create') }}" class="btn add-btn me-2">
                        <i class="fa fa-plus"></i> Add Asset
                    </a>

                    @if(isset($permissions) && $permissions->can_create)
                    <a href="{{ route('assets-assignment.create') }}" class="btn add-btn me-2">
                        <i class="fa-solid fa-handshake"></i> Assign Asset
                    </a>
                    @endif

                    <button class="btn btn-outline-secondary" type="button"
                            data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas"
                            aria-controls="filterOffcanvas">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                </div>
            </div>
        </div>

        {{-- Tab Content --}}
        <div class="row">
            <div class="col-md-12">
                <div class="tab-content">

                    {{-- All Assets Tab --}}
                    <div class="tab-pane fade show active" id="all-assets" role="tabpanel">
                        @if($assets->count() > 0)
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table custom-table datatable">
                                            <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Asset ID</th>
                                                    <th>Asset Name</th>
                                                    <th>Image</th>
                                                    <th>Quantity</th>
                                                    <th>Available</th>
                                                    <th>Location</th>
                                                    <th>Purchase Date</th>
                                                    <th>Condition</th>
                                                    <th>Warranty</th>
                                                    <th>Status</th>
                                                    <th class="text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($assets as $index => $asset)
                                                    <tr>
                                                        <td>{{ $loop->iteration + (($assets->currentPage() - 1) * $assets->perPage()) }}</td>
                                                        <td>
                                                            <a href="#" class="text-primary fw-bold">
                                                                {{ $asset->asset_id ?? 'N/A' }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <strong>{{ $asset->asset_name ?? 'N/A' }}</strong>
                                                            <small class="d-block text-muted">{{ $asset->model ?? '' }}</small>
                                                        </td>
                                                        <td>
                                                            @if($asset->asset_image && file_exists(public_path($asset->asset_image)))
                                                                <img src="{{ asset($asset->asset_image) }}" alt="{{ $asset->asset_name }}" 
                                                                     class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                                            @else
                                                                <div class="no-image-placeholder" style="width: 60px; height: 60px; background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                                                                    <i class="fa fa-image text-muted"></i>
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info">{{ $asset->quantity ?? 0 }}</span>
                                                        </td>
                                                        <td>
                                                            @php
                                                                $availablePercent = $asset->quantity > 0 ? round(($asset->available_quantity / $asset->quantity) * 100) : 0;
                                                                $availableClass = $availablePercent >= 50 ? 'bg-success' : ($availablePercent >= 20 ? 'bg-warning' : 'bg-danger');
                                                            @endphp
                                                            <div class="progress" style="height: 6px;">
                                                                <div class="progress-bar {{ $availableClass }}" role="progressbar" 
                                                                     style="width: {{ $availablePercent }}%;" 
                                                                     aria-valuenow="{{ $availablePercent }}" 
                                                                     aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                            <small class="text-muted">{{ $asset->available_quantity ?? 0 }} available</small>
                                                        </td>
                                                        <td>{{ $asset->location ?? 'N/A' }}</td>
                                                        <td>{{ Carbon::parse($asset->purchase_date ?? now())->format('d-m-Y') }}</td>
                                                        <td>
                                                            @php
                                                                $conditionColors = [
                                                                    'New' => 'success',
                                                                    'Good' => 'info',
                                                                    'Fair' => 'warning',
                                                                    'Poor' => 'danger',
                                                                    'Damaged' => 'secondary'
                                                                ];
                                                                $conditionColor = $conditionColors[$asset->condition] ?? 'secondary';
                                                            @endphp
                                                            <span class="badge bg-{{ $conditionColor }}">
                                                                {{ $asset->condition ?? 'N/A' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($asset->warranty > 0)
                                                                @php
                                                                    $purchaseDate = Carbon::parse($asset->purchase_date);
                                                                    $warrantyEndDate = $purchaseDate->addMonths($asset->warranty);
                                                                    $daysRemaining = now()->diffInDays($warrantyEndDate, false);
                                                                    $warrantyClass = $daysRemaining > 30 ? 'bg-success' : ($daysRemaining > 0 ? 'bg-warning' : 'bg-danger');
                                                                @endphp
                                                                <span class="badge {{ $warrantyClass }}">
                                                                    {{ $daysRemaining > 0 ? $daysRemaining . ' days left' : 'Expired' }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary">No Warranty</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @php
                                                                $status = $asset->available_quantity > 0 ? 'Available' : 'Out of Stock';
                                                                $statusColors = [
                                                                    'Available' => 'success',
                                                                    'Out of Stock' => 'danger',
                                                                    'Assigned' => 'info'
                                                                ];
                                                                $statusColor = $statusColors[$status] ?? 'secondary';
                                                            @endphp
                                                            <span class="badge bg-{{ $statusColor }}">
                                                                {{ $status }}
                                                            </span>
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="dropdown dropdown-action">
                                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
                                                                    <i class="material-icons">more_vert</i>
                                                                </a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a class="dropdown-item" href="{{ route('assets.edit', $asset->id) }}">
                                                                        <i class="fa fa-edit"></i> Edit
                                                                    </a>
                                                                    @if($asset->available_quantity > 0)
                                                                    <a class="dropdown-item" href="{{ route('assets-assignment.create') }}">
                                                                        <i class="fa fa-user-plus"></i> Assign
                                                                    </a>
                                                                    @endif
                                                                  
                                                                    <div class="dropdown-divider"></div>
                                                                    <a class="dropdown-item text-danger" href="#" 
                                                                       data-asset-id="{{ $asset->id }}" 
                                                                       data-asset-name="{{ $asset->asset_name ?? 'N/A' }}"
                                                                       onclick="setDeleteAsset(this)">
                                                                        <i class="fa fa-trash"></i> Delete
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        {{ $assets->links() }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="ticket-table">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-box"></i></div>
                                    <div class="empty-state-title">No Assets Found</div>
                                    <div class="empty-state-text">No assets found with current filters.</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Available Assets Tab --}}
                    <div class="tab-pane fade" id="available-assets" role="tabpanel">
                        @php
                            $availableAssets = collect($assets->items())->where('available_quantity', '>', 0)->values();
                        @endphp

                        @if($availableAssets->count() > 0)
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table custom-table datatable">
                                            <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Asset ID</th>
                                                    <th>Asset Name</th>
                                                    <th>Image</th>
                                                    <th>Quantity</th>
                                                    <th>Available</th>
                                                    <th>Location</th>
                                                    <th>Condition</th>
                                                    <th>Warranty</th>
                                                    <th class="text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($availableAssets as $index => $asset)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <a href="#" class="text-primary fw-bold">
                                                                {{ $asset->asset_id ?? 'N/A' }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <strong>{{ $asset->asset_name ?? 'N/A' }}</strong>
                                                            <small class="d-block text-muted">{{ $asset->model ?? '' }}</small>
                                                        </td>
                                                        <td>
                                                            @if($asset->asset_image && file_exists(public_path($asset->asset_image)))
                                                                <img src="{{ asset($asset->asset_image) }}" alt="{{ $asset->asset_name }}" 
                                                                     class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                                            @else
                                                                <div class="no-image-placeholder" style="width: 60px; height: 60px; background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                                                                    <i class="fa fa-image text-muted"></i>
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info">{{ $asset->quantity ?? 0 }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-success">{{ $asset->available_quantity ?? 0 }}</span>
                                                        </td>
                                                        <td>{{ $asset->location ?? 'N/A' }}</td>
                                                        <td>
                                                            @php
                                                                $conditionColors = [
                                                                    'New' => 'success',
                                                                    'Good' => 'info',
                                                                    'Fair' => 'warning',
                                                                    'Poor' => 'danger',
                                                                    'Damaged' => 'secondary'
                                                                ];
                                                                $conditionColor = $conditionColors[$asset->condition] ?? 'secondary';
                                                            @endphp
                                                            <span class="badge bg-{{ $conditionColor }}">
                                                                {{ $asset->condition ?? 'N/A' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($asset->warranty > 0)
                                                                @php
                                                                    $purchaseDate = Carbon::parse($asset->purchase_date);
                                                                    $warrantyEndDate = $purchaseDate->addMonths($asset->warranty);
                                                                    $daysRemaining = now()->diffInDays($warrantyEndDate, false);
                                                                    $warrantyClass = $daysRemaining > 30 ? 'bg-success' : ($daysRemaining > 0 ? 'bg-warning' : 'bg-danger');
                                                                @endphp
                                                                <span class="badge {{ $warrantyClass }}">
                                                                    {{ $daysRemaining > 0 ? $daysRemaining . ' days left' : 'Expired' }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary">No Warranty</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="dropdown dropdown-action">
                                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
                                                                    <i class="material-icons">more_vert</i>
                                                                </a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a class="dropdown-item" href="{{ route('assets.edit', $asset->id) }}">
                                                                        <i class="fa fa-edit"></i> Edit
                                                                    </a>
                                                                    <a class="dropdown-item" href="{{ route('assets-assignment.create') }}">
                                                                        <i class="fa fa-user-plus"></i> Assign
                                                                    </a>
                                                                    <a class="dropdown-item" href="#" onclick="showAssetHistory({{ $asset->id }})">
                                                                        <i class="fa fa-history"></i> View History
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="ticket-table">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-box"></i></div>
                                    <div class="empty-state-title">No Available Assets</div>
                                    <div class="empty-state-text">All assets are currently out of stock.</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Out of Stock Tab --}}
                    <div class="tab-pane fade" id="out-of-stock-assets" role="tabpanel">
                        @php
                            $outOfStockAssets = collect($assets->items())->where('available_quantity', '<=', 0)->values();
                        @endphp

                        @if($outOfStockAssets->count() > 0)
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table custom-table datatable">
                                            <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Asset ID</th>
                                                    <th>Asset Name</th>
                                                    <th>Total Qty</th>
                                                    <th>Assigned Qty</th>
                                                    <th>Location</th>
                                                    <th>Purchase Date</th>
                                                    <th>Condition</th>
                                                    <th class="text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($outOfStockAssets as $index => $asset)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <a href="#" class="text-primary fw-bold">
                                                                {{ $asset->asset_id ?? 'N/A' }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <strong>{{ $asset->asset_name ?? 'N/A' }}</strong>
                                                            <small class="d-block text-muted">{{ $asset->model ?? '' }}</small>
                                                        </td>
                                                        <td>{{ $asset->quantity ?? 0 }}</td>
                                                        <td>
                                                            <span class="badge bg-info">{{ $asset->quantity ?? 0 }}</span>
                                                        </td>
                                                        <td>{{ $asset->location ?? 'N/A' }}</td>
                                                        <td>{{ Carbon::parse($asset->purchase_date ?? now())->format('d-m-Y') }}</td>
                                                        <td>
                                                            @php
                                                                $conditionColors = [
                                                                    'New' => 'success',
                                                                    'Good' => 'info',
                                                                    'Fair' => 'warning',
                                                                    'Poor' => 'danger',
                                                                    'Damaged' => 'secondary'
                                                                ];
                                                                $conditionColor = $conditionColors[$asset->condition] ?? 'secondary';
                                                            @endphp
                                                            <span class="badge bg-{{ $conditionColor }}">
                                                                {{ $asset->condition ?? 'N/A' }}
                                                            </span>
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="dropdown dropdown-action">
                                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
                                                                    <i class="material-icons">more_vert</i>
                                                                </a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a class="dropdown-item" href="{{ route('assets.edit', $asset->id) }}">
                                                                        <i class="fa fa-edit"></i> Edit
                                                                    </a>
                                                                    <a class="dropdown-item" href="#" onclick="showAssetHistory({{ $asset->id }})">
                                                                        <i class="fa fa-history"></i> View Assignments
                                                                    </a>
                                                                    <div class="dropdown-divider"></div>
                                                                    <a class="dropdown-item text-danger" href="#" 
                                                                       data-asset-id="{{ $asset->id }}" 
                                                                       data-asset-name="{{ $asset->asset_name ?? 'N/A' }}"
                                                                       onclick="setDeleteAsset(this)">
                                                                        <i class="fa fa-trash"></i> Delete
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="ticket-table">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-box"></i></div>
                                    <div class="empty-state-title">All Assets Available</div>
                                    <div class="empty-state-text">Great! All assets are currently in stock.</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- All Assignments Tab --}}
                    <div class="tab-pane fade" id="all-assignments" role="tabpanel">
                        @if(count($assignments) > 0)
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table custom-table datatable">
                                            <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Employee Name</th>
                                                    <th>Asset Name</th>
                                                    <th>Asset ID</th>
                                                    <th>Assigned Date</th>
                                                    <th>Return Date</th>
                                                    <th>Condition</th>
                                                    <th>Status</th>
                                                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                        <th class="text-end">Actions</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($assignments as $index => $assignment)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <strong>{{ $assignment->firstname }} {{ $assignment->lastname }}</strong>
                                                        @if($assignment->designation)
                                                        <br><small class="text-muted">{{ $assignment->designation }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>{{ $assignment->asset_name }}</strong>
                                                        <br><small class="text-muted">{{ $assignment->model ?? 'N/A' }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary">{{ $assignment->company_asset_id }}</span>
                                                    </td>
                                                    <td>{{ Carbon::parse($assignment->assigned_date)->format('d-m-Y') }}</td>
                                                    <td>
                                                        @if($assignment->return_date)
                                                            {{ Carbon::parse($assignment->return_date)->format('d-m-Y') }}
                                                        @else
                                                            <span class="text-muted">Not Returned</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $conditionColors = [
                                                                'New' => 'success',
                                                                'Excellent' => 'info',
                                                                'Good' => 'primary',
                                                                'Fair' => 'warning',
                                                                'Poor' => 'danger'
                                                            ];
                                                            $conditionColor = $conditionColors[$assignment->condition] ?? 'secondary';
                                                        @endphp
                                                        <span class="badge bg-{{ $conditionColor }}">
                                                            {{ $assignment->condition }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $status = strtolower($assignment->status);
                                                            $statusColors = [
                                                                'assigned' => 'primary',
                                                                'returned' => 'success'
                                                            ];
                                                            $statusColor = $statusColors[$status] ?? 'secondary';
                                                        @endphp
                                                        <span class="badge bg-{{ $statusColor }}">
                                                            {{ ucfirst($assignment->status) }}
                                                        </span>
                                                    </td>
                                                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                    <td class="text-end">
                                                        <div class="dropdown dropdown-action">
                                                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
                                                                <i class="material-icons">more_vert</i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                @if(isset($permissions) && $permissions->can_edit)
                                                                <a class="dropdown-item" href="{{ route('assets-assignment.edit', $assignment->id) }}">
                                                                    <i class="fa fa-edit"></i> Edit
                                                                </a>
                                                                @endif
                                                                @if($assignment->status != 'returned')
                                                                <a class="dropdown-item" href="#" onclick="markAsReturned({{ $assignment->id }})">
                                                                    <i class="fa-solid fa-rotate-left"></i> Mark as Returned
                                                                </a>
                                                                @endif
                                                                <div class="dropdown-divider"></div>
                                                                @if(isset($permissions) && $permissions->can_delete)
                                                                <a class="dropdown-item text-danger" href="#" 
                                                                   data-assignment-id="{{ $assignment->id }}" 
                                                                   data-employee-name="{{ $assignment->firstname }} {{ $assignment->lastname }}"
                                                                   data-asset-name="{{ $assignment->asset_name }}"
                                                                   onclick="setDeleteAssignment(this)">
                                                                    <i class="fa fa-trash"></i> Delete
                                                                </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="ticket-table">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-handshake"></i></div>
                                    <div class="empty-state-title">No Assignments Found</div>
                                    <div class="empty-state-text">No asset assignments found.</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Assigned Assets Tab --}}
                    <div class="tab-pane fade" id="assigned" role="tabpanel">
                        @php
                            $assignedAssets = collect($assignments)->where('status', 'assigned')->values();
                        @endphp

                        @if($assignedAssets->count() > 0)
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table custom-table datatable">
                                            <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Employee Name</th>
                                                    <th>Asset Name</th>
                                                    <th>Asset ID</th>
                                                    <th>Assigned Date</th>
                                                    <th>Condition</th>
                                                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                        <th class="text-end">Actions</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($assignedAssets as $index => $assignment)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <strong>{{ $assignment->firstname }} {{ $assignment->lastname }}</strong>
                                                        @if($assignment->designation)
                                                        <br><small class="text-muted">{{ $assignment->designation }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>{{ $assignment->asset_name }}</strong>
                                                        <br><small class="text-muted">{{ $assignment->model ?? 'N/A' }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary">{{ $assignment->company_asset_id }}</span>
                                                    </td>
                                                    <td>{{ Carbon::parse($assignment->assigned_date)->format('d-m-Y') }}</td>
                                                    <td>
                                                        @php
                                                            $conditionColors = [
                                                                'New' => 'success',
                                                                'Excellent' => 'info',
                                                                'Good' => 'primary',
                                                                'Fair' => 'warning',
                                                                'Poor' => 'danger'
                                                            ];
                                                            $conditionColor = $conditionColors[$assignment->condition] ?? 'secondary';
                                                        @endphp
                                                        <span class="badge bg-{{ $conditionColor }}">
                                                            {{ $assignment->condition }}
                                                        </span>
                                                    </td>
                                                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                                    <td class="text-end">
                                                        <div class="dropdown dropdown-action">
                                                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
                                                                <i class="material-icons">more_vert</i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                @if(isset($permissions) && $permissions->can_edit)
                                                                <a class="dropdown-item" href="{{ route('assets-assignment.edit', $assignment->id) }}">
                                                                    <i class="fa fa-edit"></i> Edit
                                                                </a>
                                                                @endif
                                                                <a class="dropdown-item" href="#" onclick="markAsReturned({{ $assignment->id }})">
                                                                    <i class="fa-solid fa-rotate-left"></i> Mark as Returned
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="ticket-table">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-handshake"></i></div>
                                    <div class="empty-state-title">No Active Assignments</div>
                                    <div class="empty-state-text">All assets have been returned.</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Returned Assets Tab --}}
                    <div class="tab-pane fade" id="returned" role="tabpanel">
                        @php
                            $returnedAssets = collect($assignments)->where('status', 'returned')->values();
                        @endphp

                        @if($returnedAssets->count() > 0)
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table custom-table datatable">
                                            <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Employee Name</th>
                                                    <th>Asset Name</th>
                                                    <th>Asset ID</th>
                                                    <th>Assigned Date</th>
                                                    <th>Return Date</th>
                                                    <th>Condition</th>
                                                    @if(isset($permissions) && $permissions->can_delete)
                                                        <th class="text-end">Actions</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($returnedAssets as $index => $assignment)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <strong>{{ $assignment->firstname }} {{ $assignment->lastname }}</strong>
                                                        @if($assignment->designation)
                                                        <br><small class="text-muted">{{ $assignment->designation }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>{{ $assignment->asset_name }}</strong>
                                                        <br><small class="text-muted">{{ $assignment->model ?? 'N/A' }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary">{{ $assignment->company_asset_id }}</span>
                                                    </td>
                                                    <td>{{ Carbon::parse($assignment->assigned_date)->format('d-m-Y') }}</td>
                                                    <td>{{ Carbon::parse($assignment->return_date)->format('d-m-Y') }}</td>
                                                    <td>
                                                        @php
                                                            $conditionColors = [
                                                                'New' => 'success',
                                                                'Excellent' => 'info',
                                                                'Good' => 'primary',
                                                                'Fair' => 'warning',
                                                                'Poor' => 'danger'
                                                            ];
                                                            $conditionColor = $conditionColors[$assignment->condition] ?? 'secondary';
                                                        @endphp
                                                        <span class="badge bg-{{ $conditionColor }}">
                                                            {{ $assignment->condition }}
                                                        </span>
                                                    </td>
                                                    @if(isset($permissions) && $permissions->can_delete)
                                                    <td class="text-end">
                                                        <div class="dropdown dropdown-action">
                                                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
                                                                <i class="material-icons">more_vert</i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <a class="dropdown-item text-danger" href="#" 
                                                                   data-assignment-id="{{ $assignment->id }}" 
                                                                   data-employee-name="{{ $assignment->firstname }} {{ $assignment->lastname }}"
                                                                   data-asset-name="{{ $assignment->asset_name }}"
                                                                   onclick="setDeleteAssignment(this)">
                                                                    <i class="fa fa-trash"></i> Delete
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="ticket-table">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-handshake"></i></div>
                                    <div class="empty-state-title">No Returned Assets</div>
                                    <div class="empty-state-text">No assets have been returned yet.</div>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Filter Offcanvas (Right Side) --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="filterOffcanvasLabel">
                <i class="fa fa-filter"></i> Filter Assets
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="nav nav-tabs nav-justified mb-3" id="filter-tab" role="tablist">
                <button class="nav-link active" id="asset-filters-tab" data-bs-toggle="tab" data-bs-target="#asset-filters" type="button" role="tab">Asset Filters</button>
                <button class="nav-link" id="assignment-filters-tab" data-bs-toggle="tab" data-bs-target="#assignment-filters" type="button" role="tab">Assignment Filters</button>
            </div>
            
            <div class="tab-content" id="filter-tabContent">
                {{-- Asset Filters --}}
                <div class="tab-pane fade show active" id="asset-filters" role="tabpanel">
                    <form method="GET" action="{{ route('assets.index') }}" id="assetFilterForm">
                        <div class="mb-3">
                            <label for="asset_name" class="form-label">Search Asset Name</label>
                            <input type="text" class="form-control" id="asset_name" name="asset_name" 
                                   value="{{ request('asset_name') }}" placeholder="Search by asset name...">
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="condition" class="form-label">Condition</label>
                            <select class="form-select" id="condition" name="condition">
                                <option value="">All Conditions</option>
                                <option value="New" {{ request('condition') == 'New' ? 'selected' : '' }}>New</option>
                                <option value="Good" {{ request('condition') == 'Good' ? 'selected' : '' }}>Good</option>
                                <option value="Fair" {{ request('condition') == 'Fair' ? 'selected' : '' }}>Fair</option>
                                <option value="Poor" {{ request('condition') == 'Poor' ? 'selected' : '' }}>Poor</option>
                                <option value="Damaged" {{ request('condition') == 'Damaged' ? 'selected' : '' }}>Damaged</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" 
                                   value="{{ request('location') }}" placeholder="Search by location...">
                        </div>

                        <div class="mb-3">
                            <label for="from_date" class="form-label">Purchase From Date</label>
                            <input type="date" class="form-control" id="from_date" name="from_date" 
                                   value="{{ request('from_date') }}">
                        </div>

                        <div class="mb-3">
                            <label for="to_date" class="form-label">Purchase To Date</label>
                            <input type="date" class="form-control" id="to_date" name="to_date" 
                                   value="{{ request('to_date') }}">
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" onclick="resetAssetFilters()">
                                <i class="fa fa-refresh"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search"></i> Apply Filters
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Assignment Filters --}}
                <div class="tab-pane fade" id="assignment-filters" role="tabpanel">
                    <form method="GET" action="{{ route('assets-assignment.index') }}" id="assignmentFilterForm">
                        <div class="mb-3">
                            <label for="employee_name" class="form-label">Employee Name</label>
                            <input type="text" class="form-control" id="employee_name" name="employee_name" 
                                   value="{{ request('employee_name') }}" placeholder="Search by employee name...">
                        </div>

                        <div class="mb-3">
                            <label for="asset_name" class="form-label">Asset Name</label>
                            <input type="text" class="form-control" id="asset_name" name="asset_name" 
                                   value="{{ request('asset_name') }}" placeholder="Search by asset name...">
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="from_date" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="from_date" name="from_date" 
                                   value="{{ request('from_date') }}">
                        </div>

                        <div class="mb-3">
                            <label for="to_date" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="to_date" name="to_date" 
                                   value="{{ request('to_date') }}">
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" onclick="resetAssignmentFilters()">
                                <i class="fa fa-refresh"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search"></i> Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Return Confirmation Modal -->
    <div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="returnModalLabel">
                        <i class="fa-solid fa-rotate-left"></i> Return Asset
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="avatar avatar-sm mb-3">
                            <div class="avatar-title bg-light text-success rounded-circle">
                                <i class="fa-solid fa-rotate-left fa-lg"></i>
                            </div>
                        </div>
                        <p class="mb-0">Are you sure you want to mark this asset as returned?</p>
                        <p class="text-info mt-2">This will make the asset available for assignment again.</p>
                    </div>
                </div>
                <div class="modal-footer justify-content-center border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirm-return" class="btn btn-success">
                        <i class="fa-solid fa-rotate-left me-1"></i> Mark as Returned
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-report-tabs {
            display: flex;
            gap: 20px;
            border-bottom: 1px solid #e5e7eb;
            padding-left: 0;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .custom-report-tabs .nav-item {
            list-style: none;
        }

        .custom-report-tabs .nav-link {
            background: none !important;
            border: none !important;
            font-size: 14px;
            color: #555;
            padding: 8px 0;
            position: relative;
            border-radius: 0 !important;
            font-weight: 500;
            white-space: nowrap;
        }

        .custom-report-tabs .nav-link:hover {
            color: #222;
        }

        .custom-report-tabs .nav-link.active {
            color: #ff6b00 !important;
            font-weight: 600;
        }

        .custom-report-tabs .nav-link.active::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -1px;
            width: 30px;
            height: 3px;
            background: #ff6b00;
            border-radius: 10px;
        }

        .ticket-table .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .ticket-table .empty-state-icon {
            font-size: 64px;
            color: #d1d5db;
            margin-bottom: 20px;
        }

        .ticket-table .empty-state-title {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .ticket-table .empty-state-text {
            color: #999;
            font-size: 14px;
        }

        .progress {
            width: 80px;
            margin-bottom: 5px;
        }

        .no-image-placeholder {
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        #filterOffcanvas .nav-tabs .nav-link {
            font-size: 14px;
            padding: 8px 15px;
        }

        #filterOffcanvas .nav-tabs .nav-link.active {
            color: #ff6b00;
            border-bottom: 2px solid #ff6b00;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    title: 'Error!',
                    text: "{{ session('error') }}",
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            @endif

            // Initialize DataTable for each tab
            if ($.fn.dataTable.isDataTable('.datatable')) {
                $('.datatable').DataTable();
            }

            // Update filter form based on active tab
            updateFilterForm();
            
            // Listen for tab changes
            const tabLinks = document.querySelectorAll('.custom-report-tabs .nav-link');
            tabLinks.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function (event) {
                    updateFilterForm();
                });
            });
        });

        function updateFilterForm() {
            const activeTab = document.querySelector('.custom-report-tabs .nav-link.active');
            if (activeTab) {
                const tabId = activeTab.id;
                
                if (tabId.includes('assets')) {
                    // Show asset filters
                    document.querySelector('#asset-filters-tab').classList.add('active');
                    document.querySelector('#assignment-filters-tab').classList.remove('active');
                    document.querySelector('#asset-filters').classList.add('show', 'active');
                    document.querySelector('#assignment-filters').classList.remove('show', 'active');
                } else if (tabId.includes('assignments') || tabId === 'assigned-tab' || tabId === 'returned-tab') {
                    // Show assignment filters
                    document.querySelector('#assignment-filters-tab').classList.add('active');
                    document.querySelector('#asset-filters-tab').classList.remove('active');
                    document.querySelector('#assignment-filters').classList.add('show', 'active');
                    document.querySelector('#asset-filters').classList.remove('show', 'active');
                }
            }
        }

        function resetAssetFilters() {
            window.location.href = "{{ route('assets.index') }}";
        }

        function resetAssignmentFilters() {
            window.location.href = "{{ route('assets-assignment.index') }}";
        }

        function setDeleteAsset(element) {
            const assetId = element.getAttribute('data-asset-id');
            const assetName = element.getAttribute('data-asset-name');

            Swal.fire({
                title: 'Delete Asset',
                html: `Are you sure you want to delete the asset:<br><strong>${assetName}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Check if asset has assignments before deletion
                    $.ajax({
                        url: '{{ url("assets") }}/' + assetId + '/check-assignments',
                        type: 'GET',
                        success: function(response) {
                            if (response.has_assignments) {
                                Swal.fire({
                                    title: 'Cannot Delete!',
                                    text: 'This asset has active assignments. Please remove all assignments before deleting.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                deleteAsset(assetId);
                            }
                        }
                    });
                }
            });
        }

        function deleteAsset(assetId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("assets.destroy", ":id") }}'.replace(':id', assetId);
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            document.body.appendChild(form);
            form.submit();
        }

        let currentAssignmentId = null;

        function markAsReturned(assignmentId) {
            currentAssignmentId = assignmentId;
            $('#returnModal').modal('show');
        }

        $('#confirm-return').click(function() {
            if (!currentAssignmentId) return;
            
            $.ajax({
                url: "{{ url('assets-assignment') }}/" + currentAssignmentId + "/return",
                method: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function(response) {
                    $('#returnModal').modal('hide');
                    Swal.fire({
                        title: 'Success!',
                        text: 'Asset marked as returned successfully.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(response) {
                    $('#returnModal').modal('hide');
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to mark asset as returned.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        function setDeleteAssignment(element) {
            const assignmentId = element.getAttribute('data-assignment-id');
            const employeeName = element.getAttribute('data-employee-name');
            const assetName = element.getAttribute('data-asset-name');

            Swal.fire({
                title: 'Delete Assignment',
                html: `Are you sure you want to delete the assignment for:<br>
                      <strong>${employeeName}</strong><br>
                      Asset: <strong>${assetName}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ url("assets-assignment") }}/' + assignmentId;
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);
                    
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function showAssetHistory(assetId) {
            window.location.href = '{{ url("assets-assignment") }}/' + assetId + '/history';
        }
    </script>
@endsection