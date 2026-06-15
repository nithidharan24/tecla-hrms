@extends('layouts.index')

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper" style="margin-left: 5px; padding-top: 10px;">
    <div class="content container-fluid" style="max-width: 100%; padding-top: 10px;">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">User Report</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                        <li class="breadcrumb-item active">User Reports</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

       <!-- Search Filter --> 
<div class="row filter-row">
    <form action="{{ route('user-reports.index') }}" method="GET" class="w-100 d-flex gap-3">
        <div class="col-sm-6 col-md-3">  
            <div class="input-block mb-3 form-focus">
                <select name="user_name" class="form-control floating select">
                    <option value="">All Users</option> <!-- Option to select all -->
                    @foreach ($allUsers as $user) <!-- Use allUsers to populate the dropdown -->
                        <option value="{{ $user->id }}" {{ request('user_name') == $user->id ? 'selected' : '' }}>
                            {{ $user->first_name }} {{ $user->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    
        <div class="col-sm-6 col-md-3">  
            <button type="submit" class="btn btn-success w-100"> Search </button>
        </div>     
    </form>
</div>
<!-- /Search Filter -->
<!-- Export Buttons -->
<div class="row mb-3">
    <div class="col-md-12 text-end">
        <a href="{{ route('user-reports.export.csv', request()->query()) }}" class="btn btn-primary">
            <i class="fa fa-file-excel"></i> Export to CSV
        </a>
        <a href="{{ route('user-reports.export.pdf', request()->query()) }}" class="btn btn-danger">
            <i class="fa fa-file-pdf"></i> Export to PDF
        </a>
    </div>
</div>
<!-- /Export Buttons -->


        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped custom-table mb-0 datatable">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Name</th>
                            
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <h2 class="table-avatar">
                                                                
                                        <a href="profile.html">{{ $user->first_name }} {{ $user->last_name }}</a>
                                    </h2>
                                </td>
                                
                                <td>{{ $user->email }}</td>

                               <td>
    @switch($user->role)
        @case('Admin')
            <span class="badge bg-inverse-danger">{{ $user->role }}</span>
            @break
        @case('Employee')
            <span class="badge bg-inverse-success">{{ $user->role }}</span>
            @break
        @case('Client')
            <span class="badge bg-inverse-info">{{ $user->role }}</span>
            @break
        @default
            <span class="badge bg-secondary">{{ $user->role }}</span>
    @endswitch
</td>

                                <td>
    <div class="dropdown action-label">
        <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#" data-bs-toggle="dropdown">
            <i class="fa-regular fa-circle-dot {{ $user->status == 'active' ? 'text-success' : 'text-danger' }}"></i>
            {{ ucfirst($user->status) }}
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <form action="{{ route('user-reports.changeStatus', $user->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="Active">
                <button type="submit" class="dropdown-item">
                    <i class="fa-regular fa-circle-dot text-success"></i> Active
                </button>
            </form>
            <form action="{{ route('user-reports.changeStatus', $user->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="Inactive">
                <button type="submit" class="dropdown-item">
                    <i class="fa-regular fa-circle-dot text-danger"></i> Inactive
                </button>
            </form>
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
    </div>
</div>
@endsection
