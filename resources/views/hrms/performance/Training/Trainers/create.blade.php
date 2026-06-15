@extends('layouts.index') <!-- Ensure this extends the correct layout -->

@section('content')
<div class="page-wrapper" style="margin-left: 15px; padding-top: 10px;">
    <div class="content container-fluid">
        
            <div class="row mb-4">
                <div class="col">
                    <h3 class="page-title">Add New Trainer</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li> <!-- Link to dashboard -->
                        <li class="breadcrumb-item"><a href="{{ route('trainers.index') }}">Trainers</a></li> <!-- Link to trainers list -->
                        <li class="breadcrumb-item active">Add New Trainer</li> <!-- Current page -->
                    </ul>
                </div>
            </div>

            <!-- The rest of your form goes here -->
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('trainers.store') }}" method="POST">
                        @csrf
                        <!-- Form fields -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">First Name <span class="text-danger">*</span></label>
                                    <input class="form-control @error('first_name') is-invalid @enderror" type="text" name="first_name" value="{{ old('first_name') }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Last Name <span class="text-danger">*</span></label>
                                    <input class="form-control @error('last_name') is-invalid @enderror" type="text" name="last_name" value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
    <div class="input-block mb-3">
        <label class="col-form-label">Designation <span class="text-danger">*</span></label>
        <select class="form-control @error('designation_id') is-invalid @enderror" name="designation_id" required>
            <option value="">-- Select Designation --</option>
            @foreach($designations as $designation)
                <option value="{{ $designation->id }}" {{ old('designation_id') == $designation->id ? 'selected' : '' }}>{{ $designation->designation }}</option>
            @endforeach
        </select>
        @error('designation_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Email <span class="text-danger">*</span></label>
                                    <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Phone<span class="text-danger">*</span></label>
                                    <input class="form-control @error('phone') is-invalid @enderror" type="number" name="phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="4" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="submit-section">
                            <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        
    </div>
</div>
@endsection
