@extends('layouts.index')

@section('content')

<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Edit Candidate</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Candidates</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Candidate Information</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('candidate.update', $candidate->id) }}" method="POST">
                        @csrf
                        @method('PUT') <!-- Indicate that this is a PUT request for update -->
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $candidate->first_name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $candidate->last_name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $candidate->email) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone', $candidate->phone) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="created_at" class="form-label">Created Date</label>
                            <input type="date" class="form-control" id="created_at" name="created_at" value="{{ old('created_at', \Carbon\Carbon::parse($candidate->created_at)->format('Y-m-d')) }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Candidate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
