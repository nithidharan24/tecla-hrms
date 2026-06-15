@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Personal Information</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Employee List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Personal Information</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Edit Personal Information</h4>
                    <div class="card-body">
                        <form action="{{ route('employee.personal_info.update', $employee->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <!-- Passport No -->
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Passport No</label>
                                    <input type="text" name="passport_no" class="form-control" value="{{ $employeeProfile->passport_no ?? '' }}">
                                </div>

                                <!-- Passport Expiry Date -->
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Aadhaar Number</label>
                                    <div class="cal-icon">
                                        <input class="form-control datetimepicker" type="text" name="passport_expiry" value="{{ $employeeProfile->passport_exp_date ?? '' }}">
                                    </div>
                                </div>

                                <!-- Tel -->
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Blood group</label>
                                    <input class="form-control" type="text" name="tel" value="{{ $employeeProfile->tel ?? '' }}">
                                </div>

                                <!-- Nationality -->
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Nationality <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="nationality" value="{{ $employeeProfile->nationality ?? '' }}" required>
                                </div>

                                <!-- Religion -->
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Religion</label>
                                    <input class="form-control" type="text" name="religion" value="{{ $employeeProfile->religion ?? '' }}">
                                </div>

                                <!-- Marital status -->
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Marital status <span class="text-danger">*</span></label>
                                    <select class="select form-control" name="marital_status" required>
                                        <option value="">-</option>
                                        <option value="Single" {{ (isset($employeeProfile->marital_status) && $employeeProfile->marital_status == 'Single') ? 'selected' : '' }}>Single</option>
                                        <option value="Married" {{ (isset($employeeProfile->marital_status) && $employeeProfile->marital_status == 'Married') ? 'selected' : '' }}>Married</option>
                                    </select>
                                </div>

                               
                            </div>

                            <div class="submit-section">
                                <button type="submit" class="btn btn-primary btn-lg btn-block">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
