@extends('layouts.index')
@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Edit Performance Review</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('performance-review.index') }}">Performance</a></li>
                    <li class="breadcrumb-item active">Edit</li>
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
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- /Error Message Section -->

    <!-- Form Content -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card shadow-sm">
                <h4 class="card-header">Performance Review Form</h4>
                <div class="card-body">
                    <form action="{{ route('performance-review.update', $review->id) }}" method="POST" id="performanceReviewForm">
                        @csrf
                        @method('PUT')
                        <!-- Section 1: Employee Basic Information -->
                        <section class="review-section information" id="section1">
                            <div class="review-header text-center">
                                <h3 class="review-title">Employee Basic Information</h3>
                                <p class="text-muted">Please update the employee's basic information</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-nowrap review-table mb-0">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="input-block mb-3">
                                                            <label for="employee">Employee:<span class="text-danger">*</span></label>
                                                            <select class="form-control select2" id="employee" name="employee" required>
                                                                <option value="" disabled>Select Employee</option>
                                                                @foreach($employees as $employee)
                                                                <option value="{{ $employee->id }}" {{ $review->employee_name == $employee->id ? 'selected' : '' }}>
                                                                    {{ $employee->firstname }} {{ $employee->lastname }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="input-block mb-3">
                                                            <label for="emp_id">Emp ID:</label>
                                                            <input type="text" class="form-control" id="emp_id" name="emp_id" value="{{ $review->employee_id }}" readonly>
                                                        </div>
                                                        <div class="input-block mb-3">
                                                            <label for="department">Department:</label>
                                                            <input type="text" class="form-control" id="department" name="department" value="{{ $departments->firstWhere('id', $review->department_id)->department ?? '' }}" readonly>
                                                        </div>
                                                        <div class="input-block mb-3">
                                                            <label for="designation">Designation:</label>
                                                            <input type="text" class="form-control" id="designation" name="designation" value="{{ $designations->firstWhere('id', $review->designation_id)->designation ?? '' }}" readonly>
                                                        </div>
                                                        <div class="input-block mb-3">
                                                            <label for="doj">Date of Join:</label>
                                                            <input type="text" class="form-control" id="doj" name="doj" value="{{ $review->date_of_join }}" readonly>
                                                        </div>                                                            
                                                    </td>
                        
                                                    <td>
                                                        <div class="input-block mb-3">
                                                            <label for="ro_name">RO's Name:<span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="ro_name" name="ro_name" value="{{ $review->ro_name }}" required>
                                                        </div>
                                                        <div class="input-block mb-3">
                                                            <label for="ro_designation">RO Designation:<span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="ro_designation" name="ro_designation" value="{{ $review->ro_designation }}" required>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Section 2: Professional Excellence -->
                        <section class="review-section professional-excellence" id="section2">
                            <div class="review-header text-center">
                                <h3 class="review-title">Professional Excellence</h3>
                                <p class="text-muted">Evaluate the employee's professional performance</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered review-table mb-0" id="performanceTable">
                                            <thead>
                                                <tr>
                                                    <th class="width-pixel">#</th>
                                                    <th>Key Result Area</th>
                                                    <th>Key Performance Indicators</th>
                                                    <th>Weightage</th>
                                                    <th>Percentage achieved <br>(self Score)</th>
                                                    <th>Points Scored <br>(self)</th>
                                                    <th>Percentage achieved <br>(RO's Score)</th>
                                                    <th>Points Scored <br>(RO)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($professionalExcellence as $index => $pe)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $pe->key_result_area }}</td>
                                                    <td>{{ $pe->key_performance_indicator }}</td>
                                                    <td><input type="text" class="form-control weightage" readonly value="{{ $pe->weightage }}"></td>
                                                    <td><input type="text" class="form-control percentage-self" name="percentage_self[{{ $index }}]" value="{{ $pe->percentage_self }}" data-row="{{ $index + 1 }}"></td>
                                                    <td><input type="text" class="form-control points-self" name="points_self[{{ $index }}]" value="{{ $pe->points_self }}" readonly></td>
                                                    <td><input type="text" class="form-control percentage-ro" name="percentage_ro[{{ $index }}]" value="{{ $pe->percentage_ro }}" data-row="{{ $index + 1 }}"></td>
                                                    <td><input type="text" class="form-control points-ro" name="points_ro[{{ $index }}]" value="{{ $pe->points_ro }}" readonly></td>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="3" class="text-center">Total</td>
                                                    <td><input type="text" class="form-control" readonly value="85"></td>
                                                    <td><input type="text" class="form-control total-percentage-self" name="total_percentage_self" value="{{ $professionalExcellence->sum('percentage_self') }}" readonly></td>
                                                    <td><input type="text" class="form-control total-points-self" name="total_points_self" value="{{ $professionalExcellence->sum('points_self') }}" readonly></td>
                                                    <td><input type="text" class="form-control total-percentage-ro" name="total_percentage_ro" value="{{ $professionalExcellence->sum('percentage_ro') }}" readonly></td>
                                                    <td><input type="text" class="form-control total-points-ro" name="total_points_ro" value="{{ $professionalExcellence->sum('points_ro') }}" readonly></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Section 3: Personal Excellence -->
                        <section class="review-section personal-excellence" id="section3">
                            <div class="review-header text-center">
                                <h3 class="review-title">Personal Excellence</h3>
                                <p class="text-muted">Evaluate the employee's personal attributes</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered review-table mb-0" id="personalExcellenceTable">
                                            <thead>
                                                <tr>
                                                    <th class="width-pixel">#</th>
                                                    <th>Personal Attributes</th>
                                                    <th>Key Indicators</th>
                                                    <th>Weightage</th>
                                                    <th>Percentage achieved <br>(self Score)</th>
                                                    <th>Points Scored <br>(self)</th>
                                                    <th>Percentage achieved <br>(RO's Score)</th>
                                                    <th>Points Scored <br>(RO)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($personalExcellence as $index => $pe)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $pe->personal_attribute }}</td>
                                                    <td>{{ $pe->key_indicator }}</td>
                                                    <td><input type="text" class="form-control weightage" readonly value="{{ $pe->weightage }}"></td>
                                                    <td><input type="text" class="form-control percentage-self" name="personal_percentage_self[{{ $index }}]" value="{{ $pe->percentage_self }}" data-row="{{ $index + 1 }}"></td>
                                                    <td><input type="text" class="form-control points-self" name="personal_points_self[{{ $index }}]" value="{{ $pe->points_self }}" readonly></td>
                                                    <td><input type="text" class="form-control percentage-ro" name="personal_percentage_ro[{{ $index }}]" value="{{ $pe->percentage_ro }}" data-row="{{ $index + 1 }}"></td>
                                                    <td><input type="text" class="form-control points-ro" name="personal_points_ro[{{ $index }}]" value="{{ $pe->points_ro }}" readonly></td>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="3" class="text-center">Total</td>
                                                    <td>
                                                        <input type="text" class="form-control" id="personalTotalWeightage" name="personal_total_weightage" readonly value="{{ $personalExcellence->sum('weightage') }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" id="personalTotalPercentageSelf" name="personal_total_percentage_self" readonly value="{{ $personalExcellence->sum('percentage_self') }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" id="personalTotalPointsSelf" name="personal_total_points_self" readonly value="{{ $personalExcellence->sum('points_self') }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" id="personalTotalPercentageRO" name="personal_total_percentage_ro" readonly value="{{ $personalExcellence->sum('percentage_ro') }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" id="personalTotalPointsRO" name="personal_total_points_ro" readonly value="{{ $personalExcellence->sum('points_ro') }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="text-center">
                                                        <input type="hidden" name="personal_total_percentage_label" value="Total Percentage(%)">
                                                        <b>Total Percentage(%)</b>
                                                    </td>
                                                    <td colspan="5" class="text-center">
                                                        <input type="text" class="form-control" id="personalTotalPercentage" name="personal_total_percentage" readonly value="{{ $personalExcellence->sum('points_ro') / $personalExcellence->sum('weightage') * 100 }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="8" class="text-center">
                                                        <div class="grade-span">
                                                            <h4>
                                                                <input type="hidden" name="personal_grade_label" value="Grade">
                                                                Grade
                                                            </h4>
                                                            <span id="personalGrade" class="badge"></span>
                                                            <input type="hidden" id="personalGradeInput" name="personal_grade" value="">
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Section 4: Special Initiatives -->
                        <section class="review-section" id="section4">
                            <div class="review-header text-center">
                                
                                <h3 class="review-title">Special Initiatives, Achievements, contributions</h3>
                                <p class="text-muted">List any special initiatives or achievements</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-review review-table mb-0" id="table_achievements">
                                            <thead>
                                                <tr>
                                                    <th class="width-pixel">#</th>
                                                    <th>By Self</th>
                                                    <th>RO's Comment</th>
                                                    <th>HOD's Comment</th>
                                                    <th class="width-64"><button type="button" class="btn btn-primary btn-add-row"></button></th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_achievements_tbody">
                                                @foreach($specialInitiatives as $index => $initiative)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td><input type="text" class="form-control" name="achievement_self[]" value="{{ $initiative->achievement_self }}"></td>
                                                    <td><input type="text" class="form-control" name="achievement_ro[]" value="{{ $initiative->achievement_ro }}"></td>
                                                    <td><input type="text" class="form-control" name="achievement_hod[]" value="{{ $initiative->achievement_hod }}"></td>
                                                    <td></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Section 5: Comments on the role -->
                        <section class="review-section" id="section5">
                            <div class="review-header text-center">
                                <h3 class="review-title">Comments on the role</h3>
                                <p class="text-muted">Provide comments on any alterations required in responsibilities</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-review review-table mb-0" id="table_alterations">
                                            <thead>
                                                <tr>
                                                    <th class="width-pixel">#</th>
                                                    <th>By Self</th>
                                                    <th>RO's Comment</th>
                                                    <th>HOD's Comment</th>
                                                    <th class="width-64"><button type="button" class="btn btn-primary btn-add-row"></button></th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_alterations_tbody">
                                                @foreach($roleComments as $index => $comment)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td><input type="text" class="form-control" name="alteration_self[]" value="{{ $comment->alteration_self }}"></td>
                                                    <td><input type="text" class="form-control" name="alteration_ro[]" value="{{ $comment->alteration_ro }}"></td>
                                                    <td><input type="text" class="form-control" name="alteration_hod[]" value="{{ $comment->alteration_hod }}"></td>
                                                    <td></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Section 6: Strengths and Areas for Improvement -->
                        <section class="review-section" id="section6">
                            <div class="review-header text-center">
                                <h3 class="review-title">Appraisee's Strengths and Areas for Improvement perceived by the Reporting officer</h3>
                                <p class="text-muted">Identify strengths and areas for improvement</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered review-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="width-pixel">#</th>
                                                    <th>Strengths</th>
                                                    <th>Areas for Improvement</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($strengthsAndImprovements as $index => $si)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td><input type="text" class="form-control" name="strength[]" value="{{ $si->strength }}"></td>
                                                    <td><input type="text" class="form-control" name="improvement[]" value="{{ $si->improvement }}"></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Section 7: Appraisee's Strengths and Areas for Improvement -->
                        <section class="review-section" id="section7">
                            <div class="review-header text-center">
                                <h3 class="review-title">Appraisee's Strengths and Areas for Improvement (By HOD)</h3>
                                <p class="text-muted">HOD's perspective on employee's strengths and areas for improvement</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered review-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="width-pixel">#</th>
                                                    <th>Strengths</th>
                                                    <th>Areas for Improvement</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($hodStrengthsAndImprovements as $index => $hsi)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td><input type="text" class="form-control" name="hod_strength[]" value="{{ $hsi->strength }}"></td>
                                                    <td><input type="text" class="form-control" name="hod_improvement[]" value="{{ $hsi->improvement }}"></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Section 8: Personal Goals -->
                        <section class="review-section" id="section8">
                            <div class="review-header text-center">
                                <h3 class="review-title">Personal Goals</h3>
                                <p class="text-muted">Set personal goals for the upcoming year</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered review-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="width-pixel">#</th>
                                                    <th>Goal Achieved during last year</th>
                                                    <th>Goal set for current year</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($personalGoals as $index => $goal)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td><input type="text" class="form-control" name="last_year_goal[]" value="{{ $goal->last_year_goal }}"></td>
                                                    <td><input type="text" class="form-control" name="current_year_goal[]" value="{{ $goal->current_year_goal }}"></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Section 9: Personal Updates -->
                        <section class="review-section" id="section9">
                            <div class="review-header text-center">
                                <h3 class="review-title">Personal Updates</h3>
                                <p class="text-muted">Update personal information and future plans</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered review-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="width-pixel">#</th>
                                                    <th>Last Year</th>
                                                    <th>Yes/No</th>
                                                    <th>Details</th>
                                                    <th>Current Year</th>
                                                    <th>Yes/No</th>
                                                    <th>Details</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>Married/Engaged?</td>
                                                    <td>
                                                        <select class="form-control select" name="married_last_year">
                                                            <option value="">Select</option>
                                                            <option value="Yes" {{ $personalUpdates->married_last_year == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                            <option value="No" {{ $personalUpdates->married_last_year == 'No' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="married_last_year_details" value="{{ $personalUpdates->married_last_year_details }}"></td>
                                                    <td>Marriage Plans</td>
                                                    <td>
                                                        <select class="form-control select" name="marriage_plans">
                                                            <option value="">Select</option>
                                                            <option value="Yes" {{ $personalUpdates->marriage_plans == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                            <option value="No" {{ $personalUpdates->marriage_plans == 'No' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="marriage_plans_details" value="{{ $personalUpdates->marriage_plans_details }}"></td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>Higher Studies/Certifications?</td>
                                                    <td>
                                                        <select class="form-control select" name="studies_last_year">
                                                            <option value="">Select</option>
                                                            <option value="Yes" {{ $personalUpdates->studies_last_year == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                            <option value="No" {{ $personalUpdates->studies_last_year == 'No' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="studies_last_year_details" value="{{ $personalUpdates->studies_last_year_details }}"></td>
                                                    <td>Plans For Higher Study</td>
                                                    <td>
                                                        <select class="form-control select" name="study_plans">
                                                            <option value="">Select</option>
                                                            <option value="Yes" {{ $personalUpdates->study_plans == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                            <option value="No" {{ $personalUpdates->study_plans == 'No' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="study_plans_details" value="{{ $personalUpdates->study_plans_details }}"></td>
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <td>Health Issues?</td>
                                                    <td>
                                                        <select class="form-control select" name="health_issues_last_year">
                                                            <option value="">Select</option>
                                                            <option value="Yes" {{ $personalUpdates->health_issues_last_year == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                            <option value="No" {{ $personalUpdates->health_issues_last_year == 'No' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="health_issues_last_year_details" value="{{ $personalUpdates->health_issues_last_year_details }}"></td>
                                                    <td>Certification Plans</td>
                                                    <td>
                                                        <select class="form-control select" name="certification_plans">
                                                            <option value="">Select</option>
                                                            <option value="Yes" {{ $personalUpdates->certification_plans == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                            <option value="No" {{ $personalUpdates->certification_plans == 'No' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="certification_plans_details" value="{{ $personalUpdates->certification_plans_details }}"></td>
                                                </tr>
                                                <tr>
                                                    <td>4</td>
                                                    <td>Others</td>
                                                    <td>
                                                        <select class="form-control select" name="others_last_year">
                                                            <option value="">Select</option>
                                                            <option value="Yes" {{ $personalUpdates->others_last_year == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                            <option value="No" {{ $personalUpdates->others_last_year == 'No' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="others_last_year_details" value="{{ $personalUpdates->others_last_year_details }}"></td>
                                                    <td>Others</td>
                                                    <td>
                                                        <select class="form-control select" name="others_current_year">
                                                            <option value="">Select</option>
                                                            <option value="Yes" {{$personalUpdates->others_current_year == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                            <option value="No" {{ $personalUpdates->others_current_year == 'No' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="others_current_year_details" value="{{ $personalUpdates->others_current_year_details }}"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Section 10: Professional Goals -->
                        <section class="review-section" id="section10">
                            <div class="review-header text-center">
                                <h3 class="review-title">Professional Goals</h3>
                                <p class="text-muted">Set professional goals for the upcoming year</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered review-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="width-pixel">#</th>
                                                    <th>Goal Type</th>
                                                    <th>Goal Description</th>
                                                    <th>Target Achievement Date</th>
                                                    <th>Weightage</th>
                                                    <th>Percentage achieved <br>(self Score)</th>
                                                    <th>Percentage achieved <br>(RO's Score)</th>
                                                    <th>RO's Comment</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($professionalGoals as $index => $goal)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        <select class="form-control select" name="goal_type[]">
                                                            <option value="">Select Goal Type</option>
                                                            <option value="Professional" {{ $goal->goal_type == 'Professional' ? 'selected' : '' }}>Professional</option>
                                                            <option value="Personal" {{ $goal->goal_type == 'Personal' ? 'selected' : '' }}>Personal</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="goal_description[]" value="{{ $goal->goal_description }}"></td>
                                                    <td><input type="date" class="form-control" name="target_achievement_date[]" value="{{ $goal->target_achievement_date }}"></td>
                                                    <td><input type="number" class="form-control" name="weightage[]" value="{{ $goal->weightage }}"></td>
                                                    <td><input type="number" class="form-control" name="percentage_achieved_self[]" value="{{ $goal->percentage_achieved_self }}"></td>
                                                    <td><input type="number" class="form-control" name="percentage_achieved_ro[]" value="{{ $goal->percentage_achieved_ro }}"></td>
                                                    <td><input type="text" class="form-control" name="ro_comment[]" value="{{ $goal->ro_comment }}"></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Section 11: Training Requirements -->
                        <section class="review-section" id="section11">
                            <div class="review-header text-center">
                                <h3 class="review-title">Training Requirements</h3>
                                <p class="text-muted">Identify training needs for professional development</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered review-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="width-pixel">#</th>
                                                    <th>Training Requirements</th>
                                                    <th>Comments</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($trainingRequirements as $index => $training)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td><input type="text" class="form-control" name="training_requirement[]" value="{{ $training->training_requirement }}"></td>
                                                    <td><input type="text" class="form-control" name="training_comment[]" value="{{ $training->comment }}"></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Section 12: Any other general comments, observations, suggestions etc. -->
                        <section class="review-section" id="section12">
                            <div class="review-header text-center">
                                <h3 class="review-title">Any other general comments, observations, suggestions etc.</h3>
                                <p class="text-muted">Provide any additional comments or suggestions</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered review-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="width-pixel">#</th>
                                                    <th>Self</th>
                                                    <th>RO</th>
                                                    <th>HOD</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($generalComments as $index => $comment)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td><textarea class="form-control" name="self_comment[]">{{ $comment->self_comment }}</textarea></td>
                                                    <td><textarea class="form-control" name="ro_comment[]">{{ $comment->ro_comment }}</textarea></td>
                                                    <td><textarea class="form-control" name="hod_comment[]">{{ $comment->hod_comment }}</textarea></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Section 13: Performance Assessment -->
                        <section class="review-section" id="section13">
                            <div class="review-header text-center">
                                <h3 class="review-title">Performance Assessment</h3>
                                <p class="text-muted">Assess the overall performance</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered review-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="width-pixel">#</th>
                                                    <th>Assessment</th>
                                                    <th>Percentage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>Professional Skills</td>
                                                    <td><input type="number" class="form-control" name="professional_skills_percentage" value="{{ $roAssessment->professional_skills_percentage ?? '' }}"></td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>Personal Skills</td>
                                                    <td><input type="number" class="form-control" name="personal_skills_percentage" value="{{ $roAssessment->personal_skills_percentage ?? '' }}"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-center">Total Percentage</td>
                                                    <td><input type="number" class="form-control" name="total_percentage" value="{{ $roAssessment->total_percentage ?? '' }}" readonly></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">
                                                        <div class="grade-span">
                                                            <h4>Overall Performance (Grade)</h4>
                                                            <span class="badge bg-inverse-success" id="overallGrade"></span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Section 14: HRD Assessment -->
                        <section class="review-section" id="section14">
                            <div class="review-header text-center">
                                <h3 class="review-title">HRD's Assessment</h3>
                                <p class="text-muted">Human Resources Department's evaluation</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered review-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="width-pixel">#</th>
                                                    <th>Assessment</th>
                                                    <th>Percentage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>Professional Skills</td>
                                                    <td><input type="number" class="form-control" name="hrd_professional_skills_percentage" value="{{ $hrdAssessment->professional_skills_percentage ?? '' }}"></td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>Personal Skills</td>
                                                    <td><input type="number" class="form-control" name="hrd_personal_skills_percentage" value="{{ $hrdAssessment->personal_skills_percentage ?? '' }}"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-center">Total Percentage</td>
                                                    <td><input type="number" class="form-control" name="hrd_total_percentage" value="{{ $hrdAssessment->total_percentage ?? '' }}" readonly></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">
                                                        <div class="grade-span">
                                                            <h4>Overall Performance (Grade)</h4>
                                                            <span class="badge bg-inverse-success" id="hrdOverallGrade"></span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Section 15: Signatures -->
                        <section class="review-section" id="section15">
                            <div class="review-header text-center">
                                <h3 class="review-title">Signatures</h3>
                                <p class="text-muted">Signatures of the involved parties</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered review-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="width-pixel">#</th>
                                                    <th>Signature</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>Employee</td>
                                                    <td><input type="date" class="form-control" name="employee_signature_date" value="{{ $signatures->employee_signature_date ?? '' }}"></td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>Reporting Officer</td>
                                                    <td><input type="date" class="form-control" name="ro_signature_date" value="{{ $signatures->ro_signature_date ?? '' }}"></td>
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <td>HOD</td>
                                                    <td><input type="date" class="form-control" name="hod_signature_date" value="{{ $signatures->hod_signature_date ?? '' }}"></td>
                                                </tr>
                                                <tr>
                                                    <td>4</td>
                                                    <td>HRD</td>
                                                    <td><input type="date" class="form-control" name="hrd_signature_date" value="{{ $signatures->hrd_signature_date ?? '' }}"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Submit Button -->
                        <div class="row mt-4 text-center">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <button class="btn btn-primary" type="submit">Update Performance Review</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript for dynamic calculations and validations
$(document).ready(function() {
    // Function to calculate points
    function calculatePoints(row) {
        var weightage = parseFloat(row.find('.weightage').val()) || 0;
        var percentageSelf = parseFloat(row.find('.percentage-self').val()) || 0;
        var percentageRO = parseFloat(row.find('.percentage-ro').val()) || 0;

        var pointsSelf = (weightage * percentageSelf) / 100;
        var pointsRO = (weightage * percentageRO) / 100;

        row.find('.points-self').val(pointsSelf.toFixed(2));
        row.find('.points-ro').val(pointsRO.toFixed(2));
    }

    // Calculate points on input change
    $('#performanceTable, #personalExcellenceTable').on('input', '.percentage-self, .percentage-ro', function() {
        calculatePoints($(this).closest('tr'));
        updateTotals();
    });

    // Function to update totals
    function updateTotals() {
        var totalWeightage = 0;
        var totalPercentageSelf = 0;
        var totalPointsSelf = 0;
        var totalPercentageRO = 0;
        var totalPointsRO = 0;

        $('#performanceTable tbody tr:not(:last-child)').each(function() {
            totalWeightage += parseFloat($(this).find('.weightage').val()) || 0;
            total PercentageSelf += parseFloat($(this).find('.percentage-self').val()) || 0;
            totalPointsSelf += parseFloat($(this).find('.points-self').val()) || 0;
            totalPercentageRO += parseFloat($(this).find('.percentage-ro').val()) || 0;
            totalPointsRO += parseFloat($(this).find('.points-ro').val()) || 0;
        });

        $('#performanceTable tbody tr:last-child').find('td:eq(1) input').val(totalWeightage.toFixed(2));
        $('#performanceTable tbody tr:last-child').find('td:eq(2) input').val(totalPercentageSelf.toFixed(2));
        $('#performanceTable tbody tr:last-child').find('td:eq(3) input').val(totalPointsSelf.toFixed(2));
        $('#performanceTable tbody tr:last-child').find('td:eq(4) input').val(totalPercentageRO.toFixed(2));
        $('#performanceTable tbody tr:last-child').find('td:eq(5) input').val(totalPointsRO.toFixed(2));

        // Calculate and update the grade
        var grade = calculateGrade(totalPointsRO / totalWeightage * 100);
        $('#performanceTable tbody tr:last-child').find('.grade-span span').text(grade);
        $('#performanceTable tbody tr:last-child').find('.grade-span input').val(grade);
    }

    // Function to calculate grade
    function calculateGrade(percentage) {
        if (percentage >= 90) return 'A+';
        else if (percentage >= 80) return 'A';
        else if (percentage >= 70) return 'B+';
        else if (percentage >= 60) return 'B';
        else if (percentage >= 50) return 'C';
        else return 'D';
    }

    // Initial calculation
    $('#performanceTable tbody tr').each(function() {
        calculatePoints($(this));
    });
    updateTotals();

    // Add row functionality for Special Initiatives and Comments on the role
    $('.btn-add-row').click(function() {
        var table = $(this).closest('table');
        var newRow = table.find('tbody tr:first').clone();
        newRow.find('input').val('');
        table.find('tbody').append(newRow);
    });

    // Calculate overall performance grade
    $('input[name="professional_skills_percentage"], input[name="personal_skills_percentage"]').on('input', function() {
        var professionalSkills = parseFloat($('input[name="professional_skills_percentage"]').val()) || 0;
        var personalSkills = parseFloat($('input[name="personal_skills_percentage"]').val()) || 0;
        var totalPercentage = professionalSkills + personalSkills;
        $('input[name="total_percentage"]').val(totalPercentage.toFixed(2));
        $('#overallGrade').text(calculateGrade(totalPercentage));
    });

    // Calculate HRD overall performance grade
    $('input[name="hrd_professional_skills_percentage"], input[name="hrd_personal_skills_percentage"]').on('input', function() {
        var professionalSkills = parseFloat($('input[name="hrd_professional_skills_percentage"]').val()) || 0;
        var personalSkills = parseFloat($('input[name="hrd_personal_skills_percentage"]').val()) || 0;
        var totalPercentage = professionalSkills + personalSkills;
        $('input[name="hrd_total_percentage"]').val(totalPercentage.toFixed(2));
        $('#hrdOverallGrade').text(calculateGrade(totalPercentage));
    });
});
</script>
@endsection