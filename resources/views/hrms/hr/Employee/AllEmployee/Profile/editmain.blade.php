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
                                    <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Employee List</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Edit Employee Profile</li>
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
                                    <!-- First Name -->
                                    <div class="col-md-6 mb-3">
                                        <label class="col-form-label">First Name</label>
                                        <input type="text" name="first_name" class="form-control" value="{{ $employee->firstname }}">
                                    </div>

                                    <!-- Last Name -->
                                    <div class="col-md-6 mb-3">
                                        <label class="col-form-label">Last Name</label>
                                        <input type="text" name="last_name" class="form-control" value="{{ $employee->lastname }}">
                                    </div>

                                    <!-- Birth Date -->
                                    <div class="col-md-6 mb-3">
                                        <label class="col-form-label">Birth Date</label>
                                        <input class="form-control datetimepicker" type="text" name="birth_date" value="{{ $employeeProfile->birthday }}">
                                    </div>

                                    <!-- Gender -->
                                    <div class="col-md-6 mb-3">
                                        <label class="col-form-label">Gender</label>
                                        <select class="select form-control" name="gender">
                                            <option value="male" {{ $employeeProfile->gender == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ $employeeProfile->gender == 'female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Address -->
                                    <div class="col-md-12 mb-3">
                                        <label class="col-form-label">Address</label>
                                        <input type="text" name="address" class="form-control" value="{{ $employeeProfile->address }}">
                                    </div>

                                    <!-- State -->
                                    <div class="col-md-6 mb-3">
                                        <label class="col-form-label">State</label>
                                        <input type="text" name="state" class="form-control" value="{{ $employeeProfile->state }}">
                                    </div>

                                    <!-- Country -->
                                    <div class="col-md-6 mb-3">
                                        <label class="col-form-label">Country</label>
                                        <input type="text" name="country" class="form-control" value="{{ $employeeProfile->country }}">
                                    </div>

                                    <!-- Pin Code -->
                                    <div class="col-md-6 mb-3">
                                        <label class="col-form-label">Pin Code</label>
                                        <input type="text" name="pin_code" class="form-control" value="{{ $employeeProfile->pin_code }}">
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="col-md-6 mb-3">
                                        <label class="col-form-label">Phone Number</label>
                                        <input type="text" name="phone_number" class="form-control" value="{{ $employee->phone }}">
                                    </div>

                                    <!-- Department -->
                                    <div class="col-md-6 mb-3">
                                        <label class="col-form-label">Department <span class="text-danger">*</span></label>
                                        <select class="select form-control" name="department_id">
                                            <option>Select Department</option>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->id }}" {{ $employee->department == $department->id ? 'selected' : '' }}>{{ $department->department }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Designation -->
                                    <div class="col-md-6 mb-3">
                                        <label class="col-form-label">Designation <span class="text-danger">*</span></label>
                                        <select class="select form-control" name="designation_id">
                                            <option>Select Designation</option>
                                            @foreach($designations as $designation)
                                                <option value="{{ $designation->id }}" {{ $employee->designation == $designation->id ? 'selected' : '' }}>{{ $designation->designation }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="submit-section">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
