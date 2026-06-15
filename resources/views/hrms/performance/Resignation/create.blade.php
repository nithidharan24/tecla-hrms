@extends('layouts.index')
@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Resignation</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('resignation.index')}}">Resignation</a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Form Start -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card shadow-sm">
                <h4 class="card-header">Add Resignation</h4>
                <div class="card-body">
                    <form id="addResignationForm" method="POST" action="{{ route('resignation.store') }}">
                        @csrf

                        <!-- Resigning Employee Field -->
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
                                <label for="employee">Resigning Employee</label>
                                <select class="form-control select2" name="employee" id="employee">
                                    <option value="" disabled selected>Select an Employee</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->firstname }} {{ $employee->lastname }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Notice Date and Resignation Date Fields -->
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="notice_date">Notice Date</label>
                                <input type="date" class="form-control" name="notice_date" id="notice_date">
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <label for="resignation_date">Resignation Date</label>
                                <input type="date" class="form-control" name="resignation_date" id="resignation_date">
                            </div>
                        </div>

                        <!-- Reason Field -->
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <label for="reason">Reason</label>
                                <textarea class="form-control" name="reason" id="reason" rows="4" placeholder="Enter Reason" style="resize: none"></textarea>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row mt-4 text-center">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <button class="btn btn-primary" type="submit" id="submitBtn">Submit</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Form End -->

    <!-- Script to show "Processing..." -->
    <script>
        $(document).ready(function() {
            $('#employee').select2({
                placeholder: "Select an Employee",
                allowClear: true
            });

            $('#addResignationForm').on('submit', function() {
                $('#submitBtn').prop('disabled', true).text('Processing...');
            });
        });
    </script>
</div>
<!-- /Page Content -->
@endsection
