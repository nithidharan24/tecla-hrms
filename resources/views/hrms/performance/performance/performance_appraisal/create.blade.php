@extends('layouts.index')
@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Performance Appraisal</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('performance-appraisal.index')}}">Performance</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('performance-appraisal.create')}}">Add</a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->


    <!-- Error Message Section -->
    <div id="error-message" style="display: none;"></div>

    @if($errors->has('error'))
        <div class="alert alert-danger">
            {{ $errors->first('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <!-- /Error Message Section -->

    <!-- Form Content -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card shadow-sm">
                <h4 class="card-header">Give Performance Appraisal</h4>
                <div class="card-body">
                    <form action="{{ route('performance-appraisal.store') }}" method="POST">
                        @csrf
        
                        <div class="row">
                            <!-- Employee Field -->
                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <label for="employee">Employee<span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('employee') is-invalid @enderror" id="employee" name="employee" required>
                                        <option value="" disabled selected>Select Employee</option>
                                        @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee') == $employee->id ? 'selected' : '' }}>{{ $employee->firstname }} {{ $employee->lastname }}</option>
                                        @endforeach
                                    </select>
                                    @error('employee')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
    
                                <!-- Designation Field -->
                                <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <label for="designation">Designation<span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('designation') is-invalid @enderror" name="designation" id="designation" required>
                                        <option value="" disabled selected>Select a Designation</option>
                                        @foreach ($designations as $designation)
                                            <option value="{{ $designation->id }}" {{ old('designation') == $designation->id ? 'selected' : '' }}>{{ $designation->designation }}</option>
                                        @endforeach
                                    </select>
                                    @error('designation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
    
                            <!-- Appraisal Date Field -->
                            <div class="form-group mb-3">
                                <label for="appraisal_date">Select Date<span class="text-danger">*</span></label>
                                <input type="date" id="appraisal_date" name="appraisal_date" class="form-control @error('appraisal_date') is-invalid @enderror" value="{{ old('appraisal_date', date('Y-m-d')) }}" required>
                                @error('appraisal_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                        <!-- Tabs for Technical and Organizational -->
                        <ul class="nav nav-tabs" id="appraisalTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="technical-tab" data-toggle="tab" href="#technical" role="tab" aria-controls="technical" aria-selected="true">Technical</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="organizational-tab" data-toggle="tab" href="#organizational" role="tab" aria-controls="organizational" aria-selected="false">Organizational</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="appraisalTabsContent">
                            <!-- Technical Competencies Table -->
                            <div class="tab-pane fade show active" id="technical" role="tabpanel" aria-labelledby="technical-tab">
                                <div class="card">
                                    <div class="card-body">
                                        <h4>Technical Competencies<span class="text-danger">*</span></h4>
                                        <table class="table table-bordered" id="technicalTable">
                                            <thead>
                                                <tr>
                                                    <th>Indicator</th>
                                                    <th>Expected Value</th>
                                                    <th>Set Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($technicalCompetencies as $indicator => $expectedValue)
                                                    <tr>
                                                        <td>{{ formatString($indicator) }}</td>
                                                        <td>{{ capitalizeFirstLetter($expectedValue) }}</td>
                                                        <td>
                                                            <select class="form-control" name="technical[{{ $indicator }}]"> <!-- Updated name -->
                                                                <option value="none">None</option>
                                                                <option value="beginner">Beginner</option>
                                                                <option value="intermediate">Intermediate</option>
                                                                <option value="advanced">Advanced</option>
                                                                <option value="expert">Expert / Leader</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>                                            
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Organizational Competencies Table -->
                            <div class="tab-pane fade" id="organizational" role="tabpanel" aria-labelledby="organizational-tab">
                                <div class="card">
                                    <div class="card-body">
                                        <h4>Organizational Competencies<span class="text-danger">*</span></h4>
                                        <table class="table table-bordered" id="organizationalTable">
                                            <thead>
                                                <tr>
                                                    <th>Indicator</th>
                                                    <th>Expected Value</th>
                                                    <th>Set Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($organizationalCompetencies as $indicator => $expectedValue)
                                                    <tr>
                                                        <td>{{ formatString($indicator) }}</td>
                                                        <td>{{ capitalizeFirstLetter($expectedValue) }}</td>
                                                        <td>
                                                            <select class="form-control" name="organizational[{{ $indicator }}]"> <!-- Updated name -->
                                                                <option value="none">None</option>
                                                                <option value="beginner">Beginner</option>
                                                                <option value="intermediate">Intermediate</option>
                                                                <option value="advanced">Advanced</option>
                                                                <option value="expert">Expert / Leader</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Previous/Next Buttons -->
                        <div class="row mt-4 text-end">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <button class="btn btn-secondary" id="prevBtn" type="button" disabled>Previous</button>
                                <button class="btn btn-primary" id="nextBtn" type="button">Next</button>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="form-group mt-3 mb-3">
                            <label for="status">Status<span class="text-danger">*</span></label>
                            <select name="status" class="form-control">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="row mt-4 text-center">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS for active tab -->
<style>
    .nav-tabs .nav-link.active {
        background-color: orange !important;
        color: white !important;
    }
</style>

<!-- JavaScript to handle previous/next buttons and tab switching -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const technicalTab = document.querySelector('#technical-tab');
        const organizationalTab = document.querySelector('#organizational-tab');
        const prevBtn = document.querySelector('#prevBtn');
        const nextBtn = document.querySelector('#nextBtn');
        const technicalPane = document.querySelector('#technical');
        const organizationalPane = document.querySelector('#organizational');

        // Show Organizational Tab when Next is clicked
        nextBtn.addEventListener('click', function() {
            if (technicalPane.classList.contains('show')) {
                technicalPane.classList.remove('show', 'active');
                organizationalPane.classList.add('show', 'active');
                technicalTab.classList.remove('active');
                organizationalTab.classList.add('active');
                prevBtn.disabled = false;
                nextBtn.disabled = true;
            }
        });

        // Show Technical Tab when Previous is clicked
        prevBtn.addEventListener('click', function() {
            if (organizationalPane.classList.contains('show')) {
                organizationalPane.classList.remove('show', 'active');
                technicalPane.classList.add('show', 'active');
                organizationalTab.classList.remove('active');
                technicalTab.classList.add('active');
                prevBtn.disabled = true;
                nextBtn.disabled = false;
            }
        });
    });

    $(document).ready(function() {
            // Initialize select2 with placeholder
            $('#employee').select2({
                placeholder: "Select an Employee",
                allowClear: true
            });
            $('#designation').select2({
                placeholder: "Select an Designation",
                allowClear: true
            });
        });

        $(document).ready(function() {
            $('#designation').change(function() {
                var designationId = $(this).val();

                if (designationId) {
                    $.ajax({
                        url: '{{ route("getPerformanceIndicators") }}', // Route to fetch data
                        type: 'GET',
                        data: { designation_id: designationId },
                        success: function(response) {
                            if (response.success) {
                                // Hide any previous error message
                                $('#error-message').hide();

                                // Format Technical Competency Table
                                var technicalHtml = '';
                                $.each(response.data.technical, function(indicator, value) {
                                    technicalHtml += '<tr>' +
                                        '<td>' + formatString(indicator) + '</td>' + // Format indicator
                                        '<td>' + capitalizeFirstLetter(value) + '</td>' + // Format expected value
                                        '<td>' +
                                        '<select class="form-control" name="technical[' + indicator + ']">' +
                                        '<option value="none">None</option>' +
                                        '<option value="beginner">Beginner</option>' +
                                        '<option value="intermediate">Intermediate</option>' +
                                        '<option value="advanced">Advanced</option>' +
                                        '<option value="expert">Expert / Leader</option>' +
                                        '</select>' +
                                        '</td>' +
                                        '</tr>';
                                });
                                $('#technicalTable tbody').html(technicalHtml);

                                // Format Organizational Competency Table
                                var organizationalHtml = '';
                                $.each(response.data.organizational, function(indicator, value) {
                                    organizationalHtml += '<tr>' +
                                        '<td>' + formatString(indicator) + '</td>' + // Format indicator
                                        '<td>' + capitalizeFirstLetter(value) + '</td>' + // Format expected value
                                        '<td>' +
                                        '<select class="form-control" name="organizational[' + indicator + ']">' +
                                        '<option value="none">None</option>' +
                                        '<option value="beginner">Beginner</option>' +
                                        '<option value="intermediate">Intermediate</option>' +
                                        '<option value="advanced">Advanced</option>' +
                                        '<option value="expert">Expert / Leader</option>' +
                                        '</select>' +
                                        '</td>' +
                                        '</tr>';
                                });
                                $('#organizationalTable tbody').html(organizationalHtml);
                            } else {
                                // Show error message in red at the top of the form
                                $('#error-message').text('No performance indicators found for the selected designation.')
                                                    .css('color', 'red')
                                                    .show();

                                // Clear the Technical and Organizational Competency Tables
                                $('#technicalTable tbody').html('');
                                $('#organizationalTable tbody').html('');
                            }
                        },
                        error: function() {
                            alert('Error fetching data. Please try again later.');
                        }
                    });
                } else {
                    // Clear tables if no designation is selected
                    $('#technicalTable tbody').html('');
                    $('#organizationalTable tbody').html('');
                }
            });

        // Function to format string: replace underscores with spaces and capitalize first letter of each word
        function formatString(str) {
            return str.split('_').map(function(word) {
                return word.charAt(0).toUpperCase() + word.slice(1);
            }).join(' ');
        }

        // Function to capitalize the first letter of the expected value
        function capitalizeFirstLetter(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    });
</script>
@endsection
