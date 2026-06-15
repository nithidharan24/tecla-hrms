@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Leave Settings');
@endphp
@extends('layouts.index')

@section('content')

            <!-- Page Content -->
            <div class="content container-fluid">
            
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="page-title">Leave Settings</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                                <li class="breadcrumb-item active">Leave Settings</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->
                
                <div class="row">
                    <div class="col-md-12">
                    


    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
   
    <!-- Annual Leave -->
    <div class="card leave-box" id="leave_annual">
            <div class="card-body">
                <div class="h3 card-title with-switch">
                    Annual 
                </div>
                <div class="leave-item">
                    
                    <!-- Annual Days Leave -->
                    <form action="{{ route('update.annual.days') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $settings->id }}" readonly>
                        <div class="leave-row">
                            <div class="leave-left">
                                <div class="input-box">
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">Days</label>
                                        <input type="number" class="form-control" name="annual_days" value="{{ $settings->days ?? '' }}" disabled>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="leave-right">
                         @if(isset($permissions) && $permissions->can_edit)
                         <button class="leave-edit-btn" type="submit" >
                             Edit
                         </button>
                            @endif
                    
             
         </div>
                        </div>
                    </form>
                    <!-- /Annual Days Leave -->

                   
                </div>



                
            </div>
        </div>
        <!-- /Annual Leave -->
<!-- Permission Hours -->
<div class="card leave-box" id="leave_permission">
    <div class="card-body">
        <div class="h3 card-title with-switch">
            Permission Hours
        </div>
        <form action="{{ route('leave-settings.update-permission-hours') }}" method="POST">
            @csrf
            <div class="leave-item">
                <div class="leave-row">
                    <div class="leave-left">
                        <div class="input-box">
                            <div class="input-block mb-3">
                                <label class="col-form-label">Monthly Hours</label>
                                <input type="number" name="permission_hours" class="form-control" value="{{ $permissionHours->permission_hours ?? '0' }}" disabled>
                                <small class="text-muted">Maximum permission hours allowed per month</small>
                            </div>
                        </div>
                    </div>
                    <div class="leave-right">
                        @if(isset($permissions) && $permissions->can_edit)
                        <button type="submit" class="leave-edit-btn">
                            Edit
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- /Permission Hours -->
<!-- Max Allowed Leaves -->
<div class="card leave-box" id="leave_max_allowed">
    <div class="card-body">
        <div class="h3 card-title with-switch">
            Maximum Leaves Allowed per Month
        </div>
        <form action="{{ route('leave-settings.update-max-allowed') }}" method="POST">
            @csrf
            <div class="leave-item">
                <div class="leave-row">
                    <div class="leave-left">
                        <div class="input-box">
                            <div class="input-block mb-3">
                                <label class="col-form-label">Max Allowed</label>
                                <input type="number" name="max_allowed" class="form-control"
                                    value="{{ $settings->max_allowed ?? '0' }}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="leave-right">
                        @if(isset($permissions) && $permissions->can_edit)
                        <button type="submit" class="leave-edit-btn">
                            Edit
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- /Max Allowed Leaves -->


                        <!-- Sick Leave -->
<div class="card leave-box" id="leave_sick">
    <div class="card-body">
        <div class="h3 card-title with-switch">
            Sick
        </div>
        <form action="{{ route('leave-settings.update-sick') }}" method="POST">

            @csrf
            <div class="leave-item">
                <div class="leave-row">
                    <div class="leave-left">
                        <div class="input-box">
                            <div class="input-block mb-3">
                                <label class="col-form-label">Days</label>
                                <input type="number" name="sick" class="form-control" value="{{ $sickLeave->sick ?? '0' }}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="leave-right">
                            @if(isset($permissions) && $permissions->can_edit)
                         <button class="leave-edit-btn" type="submit" >
                             Edit
                         </button>
                            @endif
                    
             
         </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- /Sick Leave -->
      <!-- Hospitalisation Leave -->
<div class="card leave-box" id="leave_hospitalisation">
    <div class="card-body">
        <div class="h3 card-title with-switch">
            Hospitalisation
        </div>
        <form action="{{ route('leave-settings.update-hospitalisation') }}" method="POST">
            @csrf
            <div class="leave-item">
                <div class="leave-row">
                    <div class="leave-left">
                        <div class="input-box">
                            <div class="input-block mb-3">
                                <label class="col-form-label">Days</label>
                                <input type="number" name="hospitalisation" class="form-control" value="{{ $hospitalisationLeave->hospitalisation ?? '0' }}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="leave-right">
                        @if(isset($permissions) && $permissions->can_edit)
                         <button class="leave-edit-btn" type="submit" >
                             Edit
                         </button>
                        @endif
                    
             
         </div>
                </div>
            </div>
        </form>
        
                
    </div>
    
</div>
<!-- /Hospitalisation Leave -->

                                
                            </div>
                        </div>
                        <!-- /Hospitalisation Leave -->
                        
                        <!-- Maternity Leave -->
<div class="card leave-box" id="leave_maternity">
    <div class="card-body">
        <div class="h3 card-title with-switch">
            Maternity(Assigned to female only)
        </div>
        <form action="{{ route('leave-settings.update-maternity') }}" method="POST">
            @csrf
            <div class="leave-item">
                <div class="leave-row">
                    <div class="leave-left">
                        <div class="input-box">
                            <div class="input-block mb-3">
                                <label class="col-form-label">Days</label>
                                <input type="number" name="maternity" class="form-control" value="{{ $maternityLeave->maternity ?? '0' }}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="leave-right">
                        @if(isset($permissions) && $permissions->can_edit)
                        <button type="submit" class="leave-edit-btn">
                            Edit
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- /Maternity Leave -->

                        <!-- Paternity Leave -->
<div class="card leave-box" id="leave_paternity">
    <div class="card-body">
        <div class="h3 card-title with-switch">
            Paternity(Assigned to male only)
        </div>
        <form action="{{ route('leave-settings.update-paternity') }}" method="POST">
            @csrf
            <div class="leave-item">
                <div class="leave-row">
                    <div class="leave-left">
                        <div class="input-box">
                            <div class="input-block mb-3">
                                <label class="col-form-label">Days</label>
                                <input type="number" name="paternity" class="form-control" value="{{ $paternityLeave->paternity ?? '0' }}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="leave-right">
                        @if(isset($permissions) && $permissions->can_edit)
                        <button type="submit" class="leave-edit-btn">
                            Edit
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- /Paternity Leave -->

             
                    </div>
                </div>


@endsection         
            