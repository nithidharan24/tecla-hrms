@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Employee Profile</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Employee List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Edit Employee Profile</h4>
                    <div class="card-body">
                        <form action="{{ route('employeeprofile.update', $employee->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control" value="{{ $employee->firstname }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control" value="{{ $employee->lastname }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="birth_date">Birth Date</label>
                                    <div class="cal-icon">
                                        <input class="form-control datetimepicker" type="text" name="birth_date" value="{{ $employeeProfile->birthday }}">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" name="gender" required>
                                        <option value="male" {{ $employeeProfile->gender == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ $employeeProfile->gender == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="address">Address</label>
                                    <input type="text" name="address" class="form-control" value="{{ $employeeProfile->address }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="state">State</label>
                                    <input type="text" name="state" class="form-control" value="{{ $employeeProfile->state }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="country">Country</label>
                                    <input type="text" name="country" class="form-control" value="{{ $employeeProfile->country }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="pin_code">Pin Code</label>
                                    <input type="text" name="pin_code" class="form-control" value="{{ $employeeProfile->pin_code }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone_number">Phone Number</label>
                                    <input type="text" name="phone_number" class="form-control" value="{{ $employee->phone }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="department_id">Department <span class="text-danger">*</span></label>
                                    <select class="form-control" name="department_id" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ $employee->department_id == $department->id ? 'selected' : '' }}>{{ $department->department }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="designation_id">Designation <span class="text-danger">*</span></label>
                                    <select class="form-control" name="designation_id" required>
                                        <option value="">Select Designation</option>
                                        @foreach($designations as $designation)
                                            <option value="{{ $designation->id }}" {{ $employee->designation_id == $designation->id ? 'selected' : '' }}>{{ $designation->designation }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary btn-lg btn-block">Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
    