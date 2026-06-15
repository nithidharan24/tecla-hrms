@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Termination</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('terminations.index') }}" class="breadcrumb-link">Termination List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Termination</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Edit Termination</h4>
                    <div class="card-body">
                        <form id="terminationForm" method="POST" action="{{ route('terminations.update', $termination->id) }}" class="needs-validation">
                            @csrf
                            @method('PUT')
                            <div class="row">
                               <!-- Employee ID -->
<div class="col-md-6 mb-3">
    <label for="employee_id">Employee ID <span class="text-danger">*</span></label>
    <input type="text" class="form-control" name="employee_id" id="employee_id" value="{{ $employee->employeeid ?? '' }}" placeholder="Enter Employee ID" required>

    <div class="error-message text-danger" style="display: none;">Employee ID is required.</div>
</div>


                                <!-- Termination Type -->
                                <div class="col-md-6 mb-3">
                                    <label for="termination_type">Termination Type <span class="text-danger">*</span></label>
                                    <select name="termination_type" id="termination_type" class="form-control" required>
                                        <option value="">Select Termination Type</option>
                                        <option value="Misconduct" {{ $termination->termination_type == 'Misconduct' ? 'selected' : '' }}>Misconduct</option>
                                        <option value="Others" {{ $termination->termination_type == 'Others' ? 'selected' : '' }}>Others</option>
                                    </select>
                                    <div class="error-message text-danger" style="display: none;">Termination type is required.</div>
                                </div>

                                <!-- Termination Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="termination_date">Termination Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="termination_date" id="termination_date" value="{{ $termination->termination_date }}" required>
                                    <div class="error-message text-danger" style="display: none;">Termination date is required.</div>
                                </div>

                                <!-- Notice Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="notice_date">Notice Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="notice_date" id="notice_date" value="{{ $termination->notice_date }}" required>
                                    <div class="error-message text-danger" style="display: none;">Notice date is required.</div>
                                </div>

                                <!-- Termination Reason -->
                                <div class="col-md-12 mb-3">
                                    <label for="termination_reason">Termination Reason <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="termination_reason" id="termination_reason" rows="3" placeholder="Enter Termination Reason" maxlength="255" required>{{ $termination->reason }}</textarea>
                                    <div class="error-message text-danger" style="display: none;">Termination reason is required and must be less than 255 characters.</div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-md-12 mt-4">
                                    <button class="btn btn-primary btn-lg btn-block" type="submit">Update Termination</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript validation code can be reused here as in create.blade.php -->

@endsection
