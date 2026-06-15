@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Edit Performance Appraisal</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('performance-appraisal.index') }}">Performance</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ul>
            </div>
        </div>
    </div>

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

    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card shadow-sm">
                <h4 class="card-header">Edit Performance Appraisal</h4>
                <div class="card-body">
                    <form action="{{ route('performance-appraisal.update', $performanceAppraisal->id) }}" method="POST">
                        @csrf
                        @method('PUT')
        
                        <div class="row">
                            <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-3">
                                <label for="employee">Employee<span class="text-danger">*</span></label>
                                <select class="form-control select2" id="employee" name="employee" required disabled>
                                    <option value="" disabled>Select Employee</option>
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ $performanceAppraisal->employee_id == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->firstname }} {{ $employee->lastname }}
                                    </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="employee" value="{{ $performanceAppraisal->employee_id }}" />
                            </div>
                        
                            <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-3">
                                <label for="designation">Designation<span class="text-danger">*</span></label>
                                <select class="form-control select2" name="designation" id="designation" required disabled>
                                    <option value="" disabled>Select a Designation</option>
                                    @foreach ($designations as $designation)
                                        <option value="{{ $designation->id }}" {{ $performanceAppraisal->designation_id == $designation->id ? 'selected' : '' }}>
                                            {{ $designation->designation }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="designation" value="{{ $performanceAppraisal->designation_id }}" />
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="appraisal_date">Select Date<span class="text-danger">*</span></label>
                            <input type="date" id="appraisal_date" name="appraisal_date" class="form-control" value="{{ $performanceAppraisal->appraisal_date }}" required  readonly/>
                        </div>
                        
                        <ul class="nav nav-tabs" id="appraisalTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="technical-tab" data-toggle="tab" href="#technical" role="tab" aria-controls="technical" aria-selected="true">Technical</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="organizational-tab" data-toggle="tab" href="#organizational" role="tab" aria-controls="organizational" aria-selected="false">Organizational</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="appraisalTabsContent">
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
                                                <!-- Will be populated dynamically -->
                                            </tbody>                                            
                                        </table>
                                    </div>
                                </div>
                            </div>
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
                                                <!-- Will be populated dynamically -->
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

                        <div class="form-group mt-3 mb-3">
                            <label for="status">Status<span class="text-danger">*</span></label>
                            <select name="status" class="form-control">
                                <option value="active" {{ $performanceAppraisal->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $performanceAppraisal->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="row mt-4 text-center">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <button class="btn btn-primary" type="submit">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs .nav-link.active {
        background-color: orange !important;
        color: white !important;
    }
</style>

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

    $(document).ready(function() {
        $('#employee').select2({
            placeholder: "Select an Employee",
            allowClear: true
        });
        $('#designation').select2({
            placeholder: "Select a Designation",
            allowClear: true
        });

        // Function to populate competency tables
        function populateCompetencyTable(tableId, data, type) {
            let html = '';
            $.each(data, function(indicator, expectedValue) {
                let formattedIndicator = formatString(indicator);
                let capitalizedExpectedValue = capitalizeFirstLetter(expectedValue);
                let currentValue = '{{ json_encode($performanceAppraisal->technical) }}';
                currentValue = JSON.parse(currentValue.replace(/&quot;/g, '"'));
                let selectedValue = currentValue[indicator] || 'none';
                
                html += '<tr>' +
                    '<td>' + formattedIndicator + '</td>' +
                    '<td>' + capitalizedExpectedValue + '</td>' +
                    '<td>' +
                    '<select class="form-control" name="' + type + '[' + indicator + ']">' +
                    '<option value="none"' + (selectedValue === 'none' ? ' selected' : '') + '>None</option>' +
                    '<option value="beginner"' + (selectedValue === 'beginner' ? ' selected' : '') + '>Beginner</option>' +
                    '<option value="intermediate"' + (selectedValue === 'intermediate' ? ' selected' : '') + '>Intermediate</option>' +
                    '<option value="advanced"' + (selectedValue === 'advanced' ? ' selected' : '') + '>Advanced</option>' +
                    '<option value="expert"' + (selectedValue === 'expert' ? ' selected' : '') + '>Expert / Leader</option>' +
                    '</select>' +
                    '</td>' +
                    '</tr>';
            });
            $('#' + tableId + ' tbody').html(html);
        }

        // Function to fetch and populate performance indicators
        function fetchPerformanceIndicators(designationId) {
            $.ajax({
                url: '{{ route("getPerformanceIndicators") }}',
                type: 'GET',
                data: { designation_id: designationId },
                success: function(response) {
                    if (response.success) {
                        $('#error-message').hide();
                        populateCompetencyTable('technicalTable', response.data.technical, 'technical');
                        populateCompetencyTable('organizationalTable', response.data.organizational, 'organizational');
                    } else {
                        $('#error-message').text('No performance indicators found for the selected designation.')
                                            .css('color', 'red')
                                            .show();
                        $('#technicalTable tbody').html('');
                        $('#organizationalTable tbody').html('');
                    }
                },
                error: function() {
                    alert('Error fetching data. Please try again later.');
                }
            });
        }

        // Fetch performance indicators on page load
        fetchPerformanceIndicators($('#designation').val());

        // Fetch performance indicators when designation changes
        $('#designation').change(function() {
            fetchPerformanceIndicators($(this).val());
        });
    });

    function formatString(str) {
        return str.split('_').map(function(word) {
            return word.charAt(0).toUpperCase() + word.slice(1);
        }).join(' ');
    }

    function capitalizeFirstLetter(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
});
</script>
@endsection