@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Emergency Contact</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Employee List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Emergency Contact</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Edit Emergency Contact</h4>
                    <div class="card-body">
                        <form action="{{ route('employee.emergency_contact.update', $employee->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <!-- Primary Contact -->
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Primary Contact Name</label>
                                    <input type="text" name="primary_name" class="form-control" value="{{ $emergencyContact->primary_name }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Relationship</label>
                                    <input type="text" name="relationship" class="form-control" value="{{ $emergencyContact->relationship }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" value="{{ $emergencyContact->phone }}">
                                </div>

                                <!-- Secondary Contact -->
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Secondary Contact Name</label>
                                    <input type="text" name="secondary_name" class="form-control" value="{{ $emergencyContact->secondary_name }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Secondary Relationship</label>
                                    <input type="text" name="secondary_relationship" class="form-control" value="{{ $emergencyContact->secondary_relationship }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label">Secondary Phone</label>
                                    <input type="text" name="secondary_phone" class="form-control" value="{{ $emergencyContact->secondary_phone }}">
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
