@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Payroll Items');
@endphp
@extends('layouts.index')

@section('content')
    <!-- Page Content -->
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Payroll Items</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                        <li class="breadcrumb-item active">Payroll Items</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->
        
        <!-- Page Tab -->
        <div class="page-menu">
            <div class="row">
                <div class="col-sm-12">
                    <ul class="nav nav-tabs nav-tabs-bottom">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_additions">Additions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_overtime">Overtime</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_deductions">Deductions</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Tab -->
        
        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Additions Tab -->
            <div class="tab-pane show active" id="tab_additions">
                <!-- Add Addition Button -->
                <div class="text-end mb-4 clearfix">
                    @if(isset($permissions) && $permissions->can_create)
                    <a href="{{ route('payroll.create')}}" class="btn btn-primary add-btn" type="button">Add Addition</a>
                    @endif
                </div>
                <!-- /Add Addition Button -->
                
                <!-- Payroll Additions Table -->
                <div class="payroll-table card">
                    <div class="table-responsive">
                        <table class="table table-hover table-radius datatable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Default/Unit Amount</th>
                                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($additions as $addition)
                                    <tr>
                                        <td>{{ $addition->name }}</td>
                                        <td>{{ $addition->category }}</td>
                                        <td>₹{{ number_format($addition->unit_amount, 2) }}</td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="material-icons">more_vert</i>
                                                </a>
                                                <!-- Dropdown Delete Button -->
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <!-- Edit Button -->
                                                    @if(isset($permissions) && $permissions->can_edit)
                                                    <a class="dropdown-item" href="{{ route('payroll.edit', $addition->id) }}">
                                                        <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                                    </a>
                                                    @endif
                                                    <!-- Delete Button -->
                                                    @if(isset($permissions) && $permissions->can_delete)
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_addition" onclick="setDeleteAdditionId({{ $addition->id }})">
                                                        <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                                    </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                                @if ($additions->isEmpty())
                                    <tr>
                                        <td colspan="4" class="text-center">No additions found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /Payroll Additions Table -->
            </div>
            <!-- Additions Tab -->
            
            <!-- Overtime Tab -->
            <div class="tab-pane" id="tab_overtime">
                <!-- Add Overtime Button -->
                <div class="text-end mb-4 clearfix">
                    @if(isset($permissions) && $permissions->can_create)
                    <a href="{{ route('payovertime.create') }}" class="btn btn-primary add-btn" type="button">Add Overtime</a>
                    @endif
                </div>
                <!-- /Add Overtime Button -->

                <!-- Payroll Overtime Table -->
               <div class="payroll-table card">
    <div class="table-responsive">
        <table class="table table-hover table-radius datatable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Hierarchy</th>
                    <th>Rate</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($overtimes as $overtime)
                    @php
                        $hierarchy = DB::table('hierarchies')->find($overtime->hierarchy_id);
                        $hierarchyName = $hierarchy ? $hierarchy->hierarchy_level : 'N/A';
                    @endphp
                    <tr>
                        <td>{{ $overtime->name }}</td>
                        <td>
                            <span class="badge bg-primary">{{ $hierarchyName }}</span>
                        </td>
                        <td>
                            <span class="text-success">{{ $overtime->rate_type }}: {{ number_format($overtime->rate, 2) }}</span>
                        </td>
                        <td class="text-end">
                            <div class="dropdown dropdown-action">
                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="material-icons">more_vert</i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    @if(isset($permissions) && $permissions->can_edit)
                                    <a class="dropdown-item" href="{{ route('payovertime.edit', $overtime->id) }}">
                                        <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                    </a>
                                    @endif
                                    @if(isset($permissions) && $permissions->can_delete)
                                    <a class="dropdown-item" href="#" 
                                       data-bs-toggle="modal" 
                                       data-bs-target="#delete_overtime" 
                                       onclick="setDeleteOvertimeId({{ $overtime->id }})">
                                       <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>




                <!-- /Payroll Overtime Table -->
            </div>
            <!-- /Overtime Tab -->
            
            <!-- Deductions Tab -->
            <div class="tab-pane" id="tab_deductions">
                <!-- Add Deductions Button -->
                <div class="text-end mb-4 clearfix">
                    @if(isset($permissions) && $permissions->can_create)
                    <a href="{{ route('deductions.create') }}" class="btn btn-primary add-btn" type="button">Add Deduction</a>
                    @endif
                </div>
                <!-- /Add Deductions Button -->

                <!-- Payroll Deduction Table -->
                <div class="payroll-table card">
                    <div class="table-responsive">
                        <table class="table table-hover table-radius datatable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Default/Unit Amount</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deductions as $deduction)
                                    <tr>
                                        <td>{{ $deduction->name }}</td>
                                        <td>${{ number_format($deduction->unit_amount, 2) }}</td>
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="material-icons">more_vert</i>
                                                </a>
                                                <!-- Dropdown Delete Button -->
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <!-- Edit Button -->
                                                    @if(isset($permissions) && $permissions->can_edit)
                                                    <a class="dropdown-item" href="{{ route('deductions.edit', $deduction->id) }}">
                                                        <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                                    </a>
                                                    @endif
                                                    <!-- Delete Button -->
                                                    @if(isset($permissions) && $permissions->can_delete)
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_deduction" onclick="setDeleteDeductionId({{ $deduction->id }})">
                                                        <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                                    </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                                @if ($deductions->isEmpty())
                                    <tr>
                                        <td colspan="3" class="text-center">No deductions found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /Payroll Deduction Table -->
            </div>
            <!-- /Deductions Tab -->
        </div>
        <!-- Tab Content -->
    </div>
    <!-- /Page Content -->
    
    <!-- Delete Addition Modal -->
    <div class="modal fade" id="delete_addition" tabindex="-1" aria-labelledby="deleteAdditionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> <!-- Centered modal -->
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-0">
                    <h5 class="modal-title w-100 text-center" id="deleteAdditionModalLabel" style="font-weight: bold;">Delete Addition</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    Are you sure you want to delete this addition?
                </div>
                <div class="modal-footer d-flex justify-content-around border-0">
                    <button type="button" class="btn btn-outline-warning btn-lg" data-bs-dismiss="modal" style="border-radius: 50px; width: 150px;">Cancel</button>
                    <form id="delete-addition-form" method="POST" action="{{ route('payroll.destroy', '') }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="additionId" name="additionId" value="">
                        <button type="submit" class="btn btn-danger btn-lg" style="border-radius: 50px; width: 150px;">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Delete Addition Modal -->
    
    <!-- Delete Overtime Modal -->
    <div class="modal fade" id="delete_overtime" tabindex="-1" aria-labelledby="deleteOvertimeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> <!-- Centered modal -->
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-0">
                    <h5 class="modal-title w-100 text-center" id="deleteOvertimeModalLabel" style="font-weight: bold;">Delete Overtime</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    Are you sure you want to delete this overtime record?
                </div>
                <div class="modal-footer d-flex justify-content-around border-0">
                    <button type="button" class="btn btn-outline-warning btn-lg" data-bs-dismiss="modal" style="border-radius: 50px; width: 150px;">Cancel</button>
                    <form id="delete-overtime-form" method="POST" action="" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="overtimeId" name="overtimeId" value="">
                        <button type="submit" class="btn btn-danger btn-lg" style="border-radius: 50px; width: 150px;">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Delete Overtime Modal -->
    
    <!-- Delete Deduction Modal -->
    <div class="modal fade" id="delete_deduction" tabindex="-1" aria-labelledby="deleteDeductionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-0">
                    <h5 class="modal-title w-100 text-center" id="deleteDeductionModalLabel" style="font-weight: bold;">Delete Deduction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    Are you sure you want to delete this deduction?
                </div>
                <div class="modal-footer d-flex justify-content-around border-0">
                    <button type="button" class="btn btn-outline-warning btn-lg" data-bs-dismiss="modal" style="border-radius: 50px; width: 150px;">Cancel</button>
                    <form id="delete-deduction-form" method="POST" action="{{ route('deductions.destroy', '') }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="deductionId" name="deductionId" value="">
                        <button type="submit" class="btn btn-danger btn-lg" style="border-radius: 50px; width: 150px;">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Delete Deduction Modal -->

    <script>
        // Function to set the addition ID for deletion
        function setDeleteAdditionId(id) {
            document.getElementById('additionId').value = id;
            document.getElementById('delete-addition-form').action = '{{ route('payroll.destroy', '') }}' + '/' + id;
        }

        // Function to set the overtime ID for deletion
function setDeleteOvertimeId(id) {
    document.getElementById('overtimeId').value = id;
    // Use the named route with the correct parameter
    document.getElementById('delete-overtime-form').action = '{{ route("payovertime.destroy", ":id") }}'.replace(':id', id);
}

        // Function to set the deduction ID for deletion
        function setDeleteDeductionId(id) {
            document.getElementById('deductionId').value = id;
            document.getElementById('delete-deduction-form').action = '{{ route('deductions.destroy', '') }}' + '/' + id;
        }
    </script>
@endsection