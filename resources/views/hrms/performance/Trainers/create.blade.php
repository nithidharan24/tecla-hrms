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
                    <form action="{{ route('trainers.store') }}" method="POST">
                        @csrf
                        <!-- Form fields -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">First Name <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="first_name" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Last Name</label>
                                    <input class="form-control" type="text" name="last_name">
                                </div>
                            </div>
                            <div class="col-sm-6">
    <div class="input-block mb-3">
        <label class="col-form-label">Designation <span class="text-danger">*</span></label>
        <select class="form-control" name="designation_id" required>
            <option value="">-- Select Designation --</option>
            @foreach($designations as $designation)
                <option value="{{ $designation->id }}">{{ $designation->designation }}</option>
            @endforeach
        </select>
    </div>
</div>

                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Email <span class="text-danger">*</span></label>
                                    <input class="form-control" type="email" name="email" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Phone</label>
                                    <input class="form-control" type="number" name="phone">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="description" rows="4" required></textarea>
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
