@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Settings');
@endphp
@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Add Custom Policy</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('employee.create') }}" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('employee.create') }}" class="breadcrumb-link">Employee List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Custom Policy</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Add Custom Policy</h4>
                    <div class="card-body">
                        <form action="{{ route('storeCustomPolicy') }}" method="POST">
                            @csrf
                            <div class="input-block mb-3">
                                <label class="col-form-label">Policy Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="policy_name" required>
                            </div>
                            <div class="input-block mb-3">
                                <label class="col-form-label">Days <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="days" required>
                            </div>
                            <div class="input-block mb-3 leave-duallist">
                                <label class="col-form-label">Add Employee</label>
                                <div class="row">
                                    <div class="col-lg-5 col-sm-5">
                                        <select name="customleave_from[]" id="customleave_select" class="form-control form-select" size="5" multiple>
                                            @if(isset($employees) && $employees->count())
                                                @foreach($employees as $employee)
                                                    <option value="{{ $employee->employeeid }}">{{ $employee->firstname }} {{ $employee->lastname }}</option>
                                                @endforeach
                                            @else
                                                <option disabled>No employees available</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="multiselect-controls col-lg-2 col-sm-2 d-grid gap-2">
                                        <button type="button" id="customleave_select_rightSelected" class="btn w-100 btn-white"><i class="fa fa-chevron-right"></i></button>
                                        <button type="button" id="customleave_select_leftSelected" class="btn w-100 btn-white"><i class="fa fa-chevron-left"></i></button>
                                        <button type="button" id="customleave_select_rightAll" class="btn w-100 btn-white"><i class="fa fa-forward"></i></button>
                                        <button type="button" id="customleave_select_leftAll" class="btn w-100 btn-white"><i class="fa fa-backward"></i></button>
                                    </div>
                                    <div class="col-lg-5 col-sm-5">
                                        <select name="customleave_to[]" id="customleave_select_to" class="form-control form-select" size="8" multiple></select>
                                    </div>
                                </div>
                            </div>
                            <div class="submit-section">
                                <button class="btn btn-primary submit-btn">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectFrom = document.getElementById('customleave_select');
    const selectTo = document.getElementById('customleave_select_to');

    // Move selected options from one select to another
    function moveSelectedOptions(source, target) {
        const selectedOptions = Array.from(source.selectedOptions);
        selectedOptions.forEach(option => {
            target.appendChild(option);  // Append the option to the target
        });
    }

    // Move all options from one select to another
    function moveAllOptions(source, target) {
        const allOptions = Array.from(source.options);
        allOptions.forEach(option => {
            target.appendChild(option);  // Append all options to the target
        });
    }

    // Event listeners for button clicks
    document.getElementById('customleave_select_rightSelected').addEventListener('click', function() {
        moveSelectedOptions(selectFrom, selectTo);
    });

    document.getElementById('customleave_select_leftSelected').addEventListener('click', function() {
        moveSelectedOptions(selectTo, selectFrom);
    });

    document.getElementById('customleave_select_rightAll').addEventListener('click', function() {
        moveAllOptions(selectFrom, selectTo);
    });

    document.getElementById('customleave_select_leftAll').addEventListener('click', function() {
        moveAllOptions(selectTo, selectFrom);
    });
});
</script>
@endsection
@endsection
