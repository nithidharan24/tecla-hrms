@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Bank Information</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Employee List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Bank Information</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Bank Information</h4>
                    <div class="card-body">
                        <form action="{{ route('employee.bank_info.update', $employee->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Bank Name -->
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Bank Name</label>
                                    <input type="text" name="bank_name" id="bank_name" class="form-control" value="{{ old('bank_name', $bankInfo->bank_name) }}" required>
                                    @error('bank_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Bank Account No. -->
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Bank Account No.</label>
                                    <input type="text" name="bank_account_no" id="bank_account_no" class="form-control" value="{{ old('bank_account_no', $bankInfo->bank_account_no) }}" required>
                                    @error('bank_account_no')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- IFSC Code -->
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">IFSC Code</label>
                                    <input type="text" name="ifsc_code" id="ifsc_code" class="form-control" value="{{ old('ifsc_code', $bankInfo->ifsc_code) }}" required>
                                    @error('ifsc_code')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- PAN No. -->
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">PAN No.</label>
                                    <input type="text" name="pan_no" id="pan_no" class="form-control" value="{{ old('pan_no', $bankInfo->pan_no) }}" required>
                                    @error('pan_no')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                         
                                <button type="submit" class="btn btn-primary btn-lg btn-block">Update Bank Information</button>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
