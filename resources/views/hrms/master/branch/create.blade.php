@extends('layouts.index')
@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Add New Branch</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('branches.index') }}">Branches</a></li>
                        <li class="breadcrumb-item active">Add New</li>
                    </ul>
                </div>
                <div class="col-auto float-end ms-auto">
                    <a href="{{ route('branches.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back to Branches
                    </a>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fa fa-plus-circle"></i> Branch Information
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('branches.store') }}" method="POST" id="createBranchForm">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Branch Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label class="form-label">Address <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('address') is-invalid @enderror"
                                                   name="address" rows="3" required>{{ old('address') }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                 <div class="form-group mb-3">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        name="phone"
                                        value="{{ old('phone') }}"
                                        pattern="[6-9][0-9]{9}"
                                        maxlength="10"
                                        title="Enter a valid 10-digit Indian phone number starting with 6-9"
                                        required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                </div>
                                
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Opening Time <span class="text-danger">*</span></label>
                                                <input type="time" class="form-control @error('opening_time') is-invalid @enderror"
                                                        name="opening_time" value="{{ old('opening_time', '09:00') }}" required>
                                                @error('opening_time')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Closing Time <span class="text-danger">*</span></label>
                                                <input type="time" class="form-control @error('closing_time') is-invalid @enderror"
                                                        name="closing_time" value="{{ old('closing_time', '17:00') }}" required>
                                                @error('closing_time')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="reset" class="btn btn-secondary me-2">
                                    <i class="fa fa-undo"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fa fa-save"></i> <span id="submitText">Save Branch</span>
                                    <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('createBranchForm').addEventListener('submit', function(e) {
            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            
            submitBtn.disabled = true;
            submitText.textContent = 'Processing...';
            submitSpinner.classList.remove('d-none');
            
            const openingTime = document.querySelector('input[name="opening_time"]').value;
            const closingTime = document.querySelector('input[name="closing_time"]').value;
            
            if (openingTime >= closingTime) {
                e.preventDefault();
                // Restore button state
                submitBtn.disabled = false;
                submitText.textContent = 'Save Branch';
                submitSpinner.classList.add('d-none');
                
                Swal.fire({
                    title: 'Invalid Time Range',
                    text: 'Closing time must be after opening time.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
            // If validation passes, the form will submit and button will stay disabled until page reload
        });

        // Show validation errors if any
        @if($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Validation Error',
                    html: '<ul style="text-align: left;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        @endif
    </script>
@endsection