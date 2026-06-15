@extends('layouts.index') <!-- Extend your layout file -->

@section('content')
<div class="page-wrapper" style="margin-left: 15px; padding-top: 10px;">
    <div class="content container-fluid">
        <div class="page-header">
                <div class="row mb-4">
                    <div class="col">
                        <h3 class="page-title">Edit Trainer</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li> <!-- Link to dashboard -->
                            <li class="breadcrumb-item"><a href="{{ route('trainers.index') }}">Trainers</a></li> <!-- Link to trainers list -->
                            <li class="breadcrumb-item active">Edit Trainer</li> <!-- Current page -->
                        </ul>
                    </div>
                </div>

                <form action="{{ route('trainers.update', $trainer->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label">First Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="first_name" value="{{ $trainer->first_name }}" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label">Last Name</label>
                                <input class="form-control" type="text" name="last_name" value="{{ $trainer->last_name }}">
                            </div>
                        </div>
                        <div class="form-group">
    <label for="designation_id">Designation</label>
    <select name="designation_id" id="designation_id" class="form-control" required>
        <option value="">-- Select Designation --</option>
        @foreach($designations as $designation)
            <option value="{{ $designation->id }}" 
                {{ $trainer->designation_id == $designation->id ? 'selected' : '' }}>
                {{ $designation->designation }}
            </option>
        @endforeach
    </select>
</div>

                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label">Email <span class="text-danger">*</span></label>
                                <input class="form-control" type="email" name="email" value="{{ $trainer->email }}" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label">Phone</label>
                                <input class="form-control" type="text" name="phone" value="{{ $trainer->phone }}">
                            </div>
                        </div>
                       
                        <div class="col-sm-12">
                            <div class="input-block mb-3">
                                <label class="col-form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="description" rows="4" required>{{ $trainer->description }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Update</button>
                    </div>
                </form>
            
        </div>
    </div>
</div>
@endsection
