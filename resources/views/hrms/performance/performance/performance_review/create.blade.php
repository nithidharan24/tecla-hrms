@extends('layouts.index')
@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Performance Review</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('performance-review.index')}}">Performance</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('performance-review.create')}}">Add</a></li>
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
                    <form action="{{ route('performance-review.store') }}" method="POST" id="performanceReviewForm">
                        @csrf
                        <!-- Section 1: Employee Basic Information -->
                        <section class="review-section information" id="section1">
                            <div class="review-header text-center">
                                <h3 class="review-title">Employee Basic Information</h3>
                                <p class="text-muted">Please fill in the employee's basic information</p>
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
                                                                    <option value="" disabled selected>Select Employee</option>
                                                                    @foreach($employees as $employee)
                                                                    <option value="{{ $employee->id }}" {{ old('employee') == $employee->id ? 'selected' : '' }}>{{ $employee->firstname }} {{ $employee->lastname }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="input-block mb-3">
                                                                <label for="emp_id">Emp ID:</label>
                                                                <input type="text" class="form-control" id="emp_id" name="emp_id" readonly>
                                                            </div>
                                                            <div class="input-block mb-3">
                                                                <label for="department">Department:</label>
                                                                <input type="text" class="form-control" id="department" name="department" readonly>
                                                            </div>
                                                            <div class="input-block mb-3">
                                                                <label for="designation">Designation:</label>
                                                                <input type="text" class="form-control" id="designation" name="designation" readonly>
                                                            </div>
                                                            <div class="input-block mb-3">
                                                                <label for="doj">Date of Join:</label>
                                                                <input type="text" class="form-control" id="doj" name="doj" readonly>
                                                            </div>                                                            
                                                    </td>
                            
                                                    <td>
                                                            <div class="input-block mb-3">
                                                                <label for="ro_name">RO's Name:<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="ro_name" name="ro_name" required>
                                                            </div>
                                                            <div class="input-block mb-3">
                                                                <label for="ro_designation">RO Designation:<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="ro_designation" name="ro_designation" required>
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
                        <section class="review-section professional-excellence" id="section2" style="display: none;">
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
                                                <tr>
                                                    <td rowspan="2">1</td>
                                                    <td rowspan="2">Production</td>
                                                    <td>Quality</td>
                                                    <td><input type="text" class="form-control weightage" readonly value="30"></td>
                                                    <td><input type="text" class="form-control percentage-self" name="percentage_self[0]" data-row="1"></td>
                                                    <td><input type="text" class="form-control points-self" name="points_self[0]" readonly></td>
                                                    <td><input type="text" class="form-control percentage-ro" name="percentage_ro[0]" data-row="1"></td>
                                                    <td><input type="text" class="form-control points-ro" name="points_ro[0]" readonly></td>
                                                </tr>
                                                <tr>
                                                    <td>TAT (turn around time)</td>
                                                    <td><input type="text" class="form-control weightage" readonly value="30"></td>
                                                    <td><input type="text" class="form-control percentage-self" name="percentage_self[1]" data-row="2"></td>
                                                    <td><input type="text" class="form-control points-self" name="points_self[1]" readonly></td>
                                                    <td><input type="text" class="form-control percentage-ro" name="percentage_ro[1]" data-row="2"></td>
                                                    <td><input type="text" class="form-control points-ro" name="points_ro[1]" readonly></td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>Process Improvement</td>
                                                    <td>PMS, New Ideas</td>
                                                    <td><input type="text" class="form-control weightage" readonly value="10"></td>
                                                    <td><input type="text" class="form-control percentage-self" name="percentage_self[2]" data-row="3"></td>
                                                    <td><input type="text" class="form-control points-self" name="points_self[2]" readonly></td>
                                                    <td><input type="text" class="form-control percentage-ro" name="percentage_ro[2]" data-row="3"></td>
                                                    <td><input type="text" class="form-control points-ro" name="points_ro[2]" readonly></td>
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <td>Team Management</td>
                                                    <td>Team Productivity, Dynamics, Attendance, Attrition</td>
                                                    <td><input type="text" class="form-control weightage" readonly value="5"></td>
                                                    <td><input type="text" class="form-control percentage-self" name="percentage_self[3]" data-row="4"></td>
                                                    <td><input type="text" class="form-control points-self" name="points_self[3]" readonly></td>
                                                    <td><input type="text" class="form-control percentage-ro" name="percentage_ro[3]" data-row="4"></td>
                                                    <td><input type="text" class="form-control points-ro" name="points_ro[3]" readonly></td>
                                                </tr>
                                                <tr>
                                                    <td>4</td>
                                                    <td>Knowledge Sharing</td>
                                                    <td>Sharing the Knowledge for Team Productivity</td>
                                                    <td><input type="text" class="form-control weightage" readonly value="5"></td>
                                                    <td><input type="text" class="form-control percentage-self" name="percentage_self[4]" data-row="5"></td>
                                                    <td><input type="text" class="form-control points-self" name="points_self[4]" readonly></td>
                                                    <td><input type="text" class="form-control percentage-ro" name="percentage_ro[4]" data-row="5"></td>
                                                    <td><input type="text" class="form-control points-ro" name="points_ro[4]" readonly></td>
                                                </tr>
                                                <tr>
                                                    <td>5</td>
                                                    <td>Reporting and Communication</td>
                                                    <td>Emails, Calls, Reports, and Other Communication</td>
                                                    <td><input type="text" class="form-control weightage" readonly value="5"></td>
                                                    <td><input type="text" class="form-control percentage-self" name="percentage_self[5]" data-row="6"></td>
                                                    <td><input type="text" class="form-control points-self" name="points_self[5]" readonly></td>
                                                    <td><input type="text" class="form-control percentage-ro" name="percentage_ro[5]" data-row="6"></td>
                                                    <td><input type="text" class="form-control points-ro" name="points_ro[5]" readonly></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="text-center">Total</td>
                                                    <td><input type="text" class="form-control" readonly value="85"></td>
                                                    <td><input type="text" class="form-control total-percentage-self" name="total_percentage_self" readonly></td>
                                                    <td><input type="text" class="form-control total-points-self" name="total_points_self" readonly></td>
                                                    <td><input type="text" class="form-control total-percentage-ro" name="total_percentage_ro" readonly></td>
                                                    <td><input type="text" class="form-control total-points-ro" name="total_points_ro" readonly></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>


                        <!-- Section 3: Personal Excellence -->
                        <section class="review-section personal-excellence" id="section3" style="display: none;">
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
                                                <!-- Row 1 -->
                                                <tr>
                                                    <td rowspan="2">1</td>
                                                    <td rowspan="2">Attendance</td>
                                                    <td>Planned or Unplanned Leaves</td>
                                                    <td><input type="text" class="form-control weightage" readonly value="2"></td>
                                                    <td><input type="text" class="form-control percentage-self" name="personal_percentage_self[0]" data-row="1"></td>
                                                    <td><input type="text" class="form-control points-self" name="personal_points_self[0]" readonly></td>
                                                    <td><input type="text" class="form-control percentage-ro" name="personal_percentage_ro[0]" data-row="1"></td>
                                                    <td><input type="text" class="form-control points-ro" name="personal_points_ro[0]" readonly></td>
                                                </tr>
                                                <tr>
                                                    <td>Time Consciousness</td>
                                                    <td><input type="text" class="form-control weightage" readonly value="2"></td>
                                                    <td><input type="text" class="form-control percentage-self" name="personal_percentage_self[1]" data-row="2"></td>
                                                    <td><input type="text" class="form-control points-self" name="personal_points_self[1]" readonly></td>
                                                    <td><input type="text" class="form-control percentage-ro" name="personal_percentage_ro[1]" data-row="2"></td>
                                                    <td><input type="text" class="form-control points-ro" name="personal_points_ro[1]" readonly></td>
                                                </tr>
                                                <!-- Row 2 -->
                                                <tr>
                                                    <td rowspan="2">2</td>
                                                    <td rowspan="2">Attitude & Behavior</td>
                                                    <td>Team Collaboration</td>
                                                    <td><input type="text" class="form-control weightage" readonly value="2"></td>
                                                    <td><input type="text" class="form-control percentage-self" name="personal_percentage_self[2]" data-row="3"></td>
                                                    <td><input type="text" class="form-control points-self" name="personal_points_self[2]" readonly></td>
                                                    <td><input type="text" class="form-control percentage-ro" name="personal_percentage_ro[2]" data-row="3"></td>
                                                    <td><input type="text" class="form-control points-ro" name="personal_points_ro[2]" readonly></td>
                                                </tr>
                                                <tr>
                                                    <td>Professionalism & Responsiveness</td>
                                                    <td><input type="text" class="form-control weightage" readonly value="2"></td>
                                                    <td><input type="text" class="form-control percentage-self" name="personal_percentage_self[3]" data-row="4"></td>
                                                    <td><input type="text" class="form-control points-self" name="personal_points_self[3]" readonly></td>
                                                    <td><input type="text" class="form-control percentage-ro" name="personal_percentage_ro[3]" data-row="4"></td>
                                                    <td><input type="text" class="form-control points-ro" name="personal_points_ro[3]" readonly></td>
                                                </tr>
                                                <!-- Row 3 -->
                                                <tr>
                                                    <td>3</td>
                                                    <td>Policy & Procedures</td>
                                                    <td>Adherence to policies and procedures</td>
                                                    <td><input type="text" class="form-control weightage" readonly value="2"></td>
                                                    <td><input type="text" class="form-control percentage-self" name="personal_percentage_self[4]" data-row="5"></td>
                                                    <td><input type="text" class="form-control points-self" name="personal_points_self[4]" readonly></td>
                                                    <td><input type="text" class="form-control percentage-ro" name="personal_percentage_ro[4]" data-row="5"></td>
                                                    <td><input type="text" class="form-control points-ro" name="personal_points_ro[4]" readonly></td>
                                                </tr>
                                                <!-- Row 4 -->
                                                <tr>
                                                    <td>4</td>
                                                    <td>Initiatives</td>
                                                    <td>Special Efforts, Suggestions, Ideas, etc.</td>
                                                    <td><input type="text" class="form-control weightage" readonly value="2"></td>
                                                    <td><input type="text" class="form-control percentage-self" name="personal_percentage_self[5]" data-row="6"></td>
                                                    <td><input type="text" class="form-control points-self" name="personal_points_self[5]" readonly></td>
                                                    <td><input type="text" class="form-control percentage-ro" name="personal_percentage_ro[5]" data-row="6"></td>
                                                    <td><input type="text" class="form-control points-ro" name="personal_points_ro[5]" readonly></td>
                                                </tr>
                                                <!-- Row 5 -->
                                                <tr>
                                                    <td>5</td>
                                                    <td>Continuous Skill Improvement</td>
                                                    <td>Preparedness to move to next level & Training utilization</td>
                                                    <td><input type="text" class="form-control weightage" readonly value="3"></td>
                                                    <td><input type="text" class="form-control percentage-self" name="personal_percentage_self[6]" data-row="7"></td>
                                                    <td><input type="text" class="form-control points-self" name="personal_points_self[6]" readonly></td>
                                                    <td><input type="text" class="form-control percentage-ro" name="personal_percentage_ro[6]" data-row="7"></td>
                                                    <td><input type="text" class="form-control points-ro" name="personal_points_ro[6]" readonly></td>
                                                </tr>
                                                <!-- Total Row -->
                                                <tr>
                                                    <td colspan="3" class="text-center">
                                                        <input type="hidden" name="personal_total_label" value="Total">
                                                        Total
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" id="personalTotalWeightage" name="personal_total_weightage" readonly value="15">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" id="personalTotalPercentageSelf" name="personal_total_percentage_self" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" id="personalTotalPointsSelf" name="personal_total_points_self" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" id="personalTotalPercentageRO" name="personal_total_percentage_ro" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" id="personalTotalPointsRO" name="personal_total_points_ro" readonly>
                                                    </td>
                                                </tr>
                                                <!-- Grade Row -->
                                                <tr>
                                                    <td colspan="3" class="text-center">
                                                        <input type="hidden" name="personal_total_percentage_label" value="Total Percentage(%)">
                                                        <b>Total Percentage(%)</b>
                                                    </td>
                                                    <td colspan="5" class="text-center">
                                                        <input type="text" class="form-control" id="personalTotalPercentage" name="personal_total_percentage" readonly>
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
                        <section class="review-section" id="section4" style="display: none;">
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
                                                <tr>
                                                    <td>1</td>
                                                    <td><input type="text" class="form-control" name="achievement_self[]"></td>
                                                    <td><input type="text" class="form-control" name="achievement_ro[]"></td>
                                                    <td><input type="text" class="form-control" name="achievement_hod[]"></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Section 5: Comments on the role -->
                        <section class="review-section" id="section5" style="display: none;">
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
                                                <tr>
                                                    <td>1</td>
                                                    <td><input type="text" class="form-control" name="alteration_self[]"></td>
                                                    <td><input type="text" class="form-control" name="alteration_ro[]"></td>
                                                    <td><input type="text" class="form-control" name="alteration_hod[]"></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Section 6: Strengths and Areas for Improvement -->
                        <section class="review-section"   id="section6" style="display: none;">
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
                                                @for ($i = 1; $i <= 5; $i++)
                                                <tr>
                                                    <td>{{ $i }}</td>
                                                    <td><input type="text" class="form-control" name="strength[]"></td>
                                                    <td><input type="text" class="form-control" name="improvement[]"></td>
                                                </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Section 7: Appraisee's Strengths and Areas for Improvement -->
                        <section class="review-section" id="section7" style="display: none;">
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
                                                @for ($i = 1; $i <= 3; $i++)
                                                <tr>
                                                    <td>{{ $i }}</td>
                                                    <td><input type="text" class="form-control" name="hod_strength[]"></td>
                                                    <td><input type="text" class="form-control" name="hod_improvement[]"></td>
                                                </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Section 8: Personal Goals -->
                        <section class="review-section" id="section8" style="display: none;">
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
                                                @for ($i = 1; $i <= 3; $i++)
                                                <tr>
                                                    <td>{{ $i }}</td>
                                                    <td><input type="text" class="form-control" name="last_year_goal[]"></td>
                                                    <td><input type="text" class="form-control" name="current_year_goal[]"></td>
                                                </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- New Section 9: Personal Updates -->
                        <section class="review-section" id="section9" style="display: none;">
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
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="married_last_year_details"></td>
                                                    <td>Marriage Plans</td>
                                                    <td>
                                                        <select class="form-control select" name="marriage_plans">
                                                            <option value="">Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="marriage_plans_details"></td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>Higher Studies/Certifications?</td>
                                                    <td>
                                                        <select class="form-control select" name="studies_last_year">
                                                            <option value="">Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="studies_last_year_details"></td>
                                                    <td>Plans For Higher Study</td>
                                                    <td>
                                                        <select class="form-control select" name="study_plans">
                                                            <option value="">Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="study_plans_details"></td>
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <td>Health Issues?</td>
                                                    <td>
                                                        <select class="form-control select" name="health_issues_last_year">
                                                            <option value="">Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="health_issues_last_year_details"></td>
                                                    <td>Certification Plans</td>
                                                    <td>
                                                        <select class="form-control select" name="certification_plans">
                                                            <option value="">Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="certification_plans_details"></td>
                                                </tr>
                                                <tr>
                                                    <td>4</td>
                                                    <td>Others</td>
                                                    <td>
                                                        <select class="form-control select" name="others_last_year">
                                                            <option value="">Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="others_last_year_details"></td>
                                                    <td>Others</td>
                                                    <td>
                                                        <select class="form-control select" name="others_current_year">
                                                            <option value="">Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="others_current_year_details"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- New Section 10: Professional Goals Achieved for last year -->
                        <section class="review-section" id="section10" style="display: none;">
                            <div class="review-header text-center">
                                <h3 class="review-title">Professional Goals Achieved for last year</h3>
                                <p class="text-muted">List your professional achievements from the past year</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-review review-table mb-0" id="table_goals">
                                            <thead>
                                                <tr>
                                                    <th class="width-pixel">#</th>
                                                    <th>By Self</th>
                                                    <th>RO's Comment</th>
                                                    <th>HOD's Comment</th>
                                                    <th class="width-64"><button type="button" class="btn btn-primary btn-add-row"></button></th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_goals_tbody">
                                                @for ($i = 1; $i <= 5; $i++)
                                                <tr>
                                                    <td>{{ $i }}</td>
                                                    <td><input type="text" class="form-control" name="goal_self[]"></td>
                                                    <td><input type="text" class="form-control" name="goal_ro[]"></td>
                                                    <td><input type="text" class="form-control" name="goal_hod[]"></td>
                                                    <td></td>
                                                </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- New Section 11: Professional Goals for the forthcoming year -->
                        <section class="review-section" id="section11" style="display: none;">
                            <div class="review-header text-center">
                                <h3 class="review-title">Professional Goals for the forthcoming year</h3>
                                <p class="text-muted">Set your professional goals for the upcoming year</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-review review-table mb-0" id="table_forthcoming">
                                            <thead>
                                                <tr>
                                                    <th class="width-pixel">#</th>
                                                    <th>By Self</th>
                                                    <th>RO's Comment</th>
                                                    <th>HOD's Comment</th>
                                                    <th class="width-64"><button type="button" class="btn btn-primary btn-add-row"></button></th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_forthcoming_tbody">
                                                @for ($i = 1; $i <= 5; $i++)
                                                <tr>
                                                    <td>{{ $i }}</td>
                                                    <td><input type="text" class="form-control" name="forthcoming_goal_self[]"></td>
                                                    <td><input type="text" class="form-control" name="forthcoming_goal_ro[]"></td>
                                                    <td><input type="text" class="form-control" name="forthcoming_goal_hod[]"></td>
                                                    <td></td>
                                                </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- New Section 12: Training Requirements -->
                        <section class="review-section" id="section12" style="display: none;">
                            <div class="review-header text-center">
                                <h3 class="review-title">Training Requirements</h3>
                                <p class="text-muted">Identify training needs to achieve Performance Standard Targets</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-review review-table mb-0" id="table_targets">
                                            <thead>
                                                <tr>
                                                <th class="width-pixel">#</th>
                                                <th>By Self</th>
                                                <th>RO's Comment</th>
                                                <th>HOD's Comment</th>
                                                <th class="width-64"><button type="button" class="btn btn-primary btn-add-row"></button></th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_targets_tbody">
                                                @for ($i = 1; $i <= 5; $i++)
                                                <tr>
                                                    <td>{{ $i }}</td>
                                                    <td><input type="text" class="form-control" name="training_self[]"></td>
                                                    <td><input type="text" class="form-control" name="training_ro[]"></td>
                                                    <td><input type="text" class="form-control" name="training_hod[]"></td>
                                                    <td></td>
                                                </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- New Section 13: Any other general comments -->
                        <section class="review-section" id="section13" style="display: none;">
                            <div class="review-header  text-center">
                                <h3 class="review-title">Any other general comments, observations, suggestions etc.</h3>
                                <p class="text-muted">Provide any additional feedback or suggestions</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-review review-table mb-0" id="general_comments">
                                            <thead>
                                                <tr>
                                                <th class="width-pixel">#</th>
                                                <th>Self</th>
                                                <th>RO</th>
                                                <th>HOD</th>
                                                <th class="width-64"><button type="button" class="btn btn-primary btn-add-row"></button></th>
                                                </tr>
                                            </thead>
                                            <tbody id="general_comments_tbody">
                                                @for ($i = 1; $i <= 5; $i++)
                                                <tr>
                                                    <td>{{ $i }}</td>
                                                    <td><input type="text" class="form-control" name="comment_self[]"></td>
                                                    <td><input type="text" class="form-control" name="comment_ro[]"></td>
                                                    <td><input type="text" class="form-control" name="comment_hod[]"></td>
                                                    <td></td>
                                                </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- New Section 14: For RO's Use Only -->
                        <section class="review-section" id="section14" style="display: none;">
                            <div class="review-header text-center">
                                <h3 class="review-title">For RO's Use Only</h3>
                                <p class="text-muted">Reporting Officer's assessment of the employee</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered review-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Yes/No</th>
                                                    <th>If Yes - Details</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>The Team member has Work related Issues</td>
                                                    <td>
                                                        <select class="form-control select" name="work_issues">
                                                            <option value="">Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="work_issues_details"></td>
                                                </tr>
                                                <tr>
                                                    <td>The Team member has Leave Issues</td>
                                                    <td>
                                                        <select class="form-control select" name="leave_issues">
                                                            <option value="">Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="leave_issues_details"></td>
                                                </tr>
                                                <tr>
                                                    <td>The team member has Stability Issues</td>
                                                    <td>
                                                        <select class="form-control select" name="stability_issues">
                                                            <option value="">Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="stability_issues_details"></td>
                                                </tr>
                                                <tr>
                                                    <td>The Team member exhibits non-supportive attitude</td>
                                                    <td>
                                                        <select class="form-control select" name="attitude_issues">
                                                            <option value="">Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="attitude_issues_details"></td>
                                                </tr>
                                                <tr>
                                                    <td>Any other points in specific to note about the team member</td>
                                                    <td>
                                                        <select class="form-control select" name="other_issues">
                                                            <option value="">Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="other_issues_details"></td>
                                                </tr>
                                                <tr>
                                                    <td>Overall Comment /Performance of the team member</td>
                                                    <td>
                                                        <select class="form-control select" name="overall_performance">
                                                            <option value="">Select</option>
                                                            <option value="Excellent">Excellent</option>
                                                            <option value="Good">Good</option>
                                                            <option value="Average">Average</option>
                                                            <option value="Poor">Poor</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="overall_performance_details"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- New Section 15: For HRD's Use Only -->
                        <section class="review-section" id="section15" style="display: none;">
                            <div class="review-header text-center">
                                <h3 class="review-title">For HRD's Use Only</h3>
                                <p class="text-muted">Human Resources Department's assessment</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered review-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Overall Parameters</th>
                                                    <th>Available Points</th>
                                                    <th>Points Scored</th>
                                                    <th>RO's Comment</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>KRAs Target Achievement Points</td>
                                                    <td><input type="number" class="form-control" name="kra_points_available"></td>
                                                    <td><input type="number" class="form-control" name="kra_points_scored"></td>
                                                    <td><input type="text" class="form-control" name="kra_comment"></td>
                                                </tr>
                                                <tr>
                                                    <td>Professional Skills Scores</td>
                                                    <td><input type="number" class="form-control" name="professional_points_available"></td>
                                                    <td><input type="number" class="form-control" name="professional_points_scored"></td>
                                                    <td><input type="text" class="form-control" name="professional_comment"></td>
                                                </tr>
                                                <tr>
                                                    <td>Personal Skills Scores</td>
                                                    <td><input type="number" class="form-control" name="personal_points_available"></td>
                                                    <td><input type="number" class="form-control" name="personal_points_scored"></td>
                                                    <td><input type="text" class="form-control" name="personal_comment"></td>
                                                </tr>
                                                <tr>
                                                    <td>Special Achievements Score</td>
                                                    <td><input type="number" class="form-control" name="achievement_points_available"></td>
                                                    <td><input type="number" class="form-control" name="achievement_points_scored"></td>
                                                    <td><input type="text" class="form-control" name="achievement_comment"></td>
                                                </tr>
                                                <tr>
                                                    <td>Overall Total Score</td>
                                                    <td><input type="number" class="form-control" name="total_points_available" ></td>
                                                    <td><input type="number" class="form-control" name="total_points_scored" ></td>
                                                    <td><input type="number" class="form-control" name="total_comment"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- New Section 16: Signature Section -->
                        <section class="review-section" id="section16" style="display: none;">
                            <div class="review-header text-center">
                                <h3 class="review-title">Signatures</h3>
                                <p class="text-muted">Approval and acknowledgment signatures</p>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered review-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Name</th>
                                                    <th>Signature</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Employee</td>
                                                    <td><input type="text" class="form-control" name="employee_name"></td>
                                                    <td><input type="text" class="form-control" name="employee_signature"></td>
                                                    <td><input type="date" class="form-control" name="employee_date"></td>
                                                </tr>
                                                <tr>
                                                    <td>Reporting Officer</td>
                                                    <td><input type="text" class="form-control" name="ro_name2"></td>
                                                    <td><input type="text" class="form-control" name="ro_signature"></td>
                                                    <td><input type="date" class="form-control" name="ro_date"></td>
                                                </tr>
                                                <tr>
                                                    <td>HOD</td>
                                                    <td><input type="text" class="form-control" name="hod_name"></td>
                                                    <td><input type="text" class="form-control" name="hod_signature"></td>
                                                    <td><input type="date" class="form-control" name="hod_date"></td>
                                                </tr>
                                                <tr>
                                                    <td>HRD</td>
                                                    <td><input type="text" class="form-control" name="hrd_name"></td>
                                                    <td><input type="text" class="form-control" name="hrd_signature"></td>
                                                    <td><input type="date" class="form-control" name="hrd_date"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Previous/Next Buttons -->
                        <div class="row mt-4 text-end">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <button class="btn btn-secondary" id="prevBtn" type="button" style="display: none;">Previous</button>
                                <button class="btn btn-primary" id="nextBtn" type="button">Next</button>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="form-group mt-3 mb-3">
                            <label for="status">Status<span class="text-danger">*</span></label>
                            <select name="status" class="form-control" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="row mt-4 text-center">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <button class="btn btn-primary" type="submit" id="submitBtn" style="display: none;">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let currentSection = 1;
        const totalSections = 16; // Updated to include new sections

        function showSection(sectionNumber) {
            $(`#section${currentSection}`).hide();
            $(`#section${sectionNumber}`).show();
            currentSection = sectionNumber;

            // Update button visibility
            if (currentSection > 1) {
                $('#prevBtn').show();
            } else {
                $('#prevBtn').hide();
            }

            if (currentSection < totalSections) {
                $('#nextBtn').show();
                $('#submitBtn').hide();
            } else {
                $('#nextBtn').hide();
                $('#submitBtn').show();
            }
        }

        $('#nextBtn').click(function() {
            if (currentSection < totalSections) {
                showSection(currentSection + 1);
            }
        });

        $('#prevBtn').click(function() {
            if (currentSection > 1) {
                showSection(currentSection - 1);
            }
        });

        // Existing AJAX call for employee details
        $('#employee').change(function() {
            var employeeId = $(this).val();
            if(employeeId) {
                $.ajax({
                    url: '/get-employee-details/' + employeeId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#emp_id').val(data.employeeid);
                        $('#department').val(data.department);
                        $('#designation').val(data.designation);
                        $('#doj').val(data.joiningdate);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching employee details:', error);
                    }
                });
            }
        });

        // Add row functionality for dynamic tables
        $('.btn-add-row').click(function() {
            var table = $(this).closest('table');
            var tbody = table.find('tbody');
            var lastRow = tbody.find('tr:last');
            var newRow = lastRow.clone();
            var rowNumber = parseInt(lastRow.find('td:first').text()) + 1;
            newRow.find('td:first').text(rowNumber);
            newRow.find('input').val('');
            tbody.append(newRow);
        });

        $(document).ready(function() {
    // Function to calculate points based on percentage and weightage
    function calculatePoints(percentage, weightage) {
        return (percentage * weightage) / 100;
    }

    // Function to calculate the total of Points Scored columns (self and RO)
    function calculateTotal() {
        let totalSelf = 0;
        let totalRO = 0;
        let totalPercentageSelf = 0;
        let totalPercentageRO = 0;
        let totalWeightage = 0;

        // Loop through each row to calculate the total points for self and RO
        $('#performanceTable tbody tr:not(:last-child)').each(function() {
            // Get the points scored values for self and RO in each row
            const pointsSelf = parseFloat($(this).find('.points-self').val()) || 0;
            const pointsRO = parseFloat($(this).find('.points-ro').val()) || 0;
            const percentageSelf = parseFloat($(this).find('.percentage-self').val()) || 0;
            const percentageRO = parseFloat($(this).find('.percentage-ro').val()) || 0;
            const weightage = parseFloat($(this).find('.weightage').val()) || 0;

            // Add to total points scored for each column
            totalSelf += pointsSelf;
            totalRO += pointsRO;
            totalPercentageSelf += percentageSelf * weightage;
            totalPercentageRO += percentageRO * weightage;
            totalWeightage += weightage;
        });

        // Calculate average percentages
        const avgPercentageSelf = totalWeightage > 0 ? totalPercentageSelf / totalWeightage : 0;
        const avgPercentageRO = totalWeightage > 0 ? totalPercentageRO / totalWeightage : 0;

        // Update the total fields in the last row for both self and RO
        $('.total-points-self').val(totalSelf.toFixed(2));
        $('.total-points-ro').val(totalRO.toFixed(2));
        $('.total-percentage-self').val(avgPercentageSelf.toFixed(2));
        $('.total-percentage-ro').val(avgPercentageRO.toFixed(2));
    }

    // Event listener for input fields in the Percentage columns (self and RO)
    $('#performanceTable').on('input', '.percentage-self, .percentage-ro', function() {
        const percentage = parseFloat($(this).val()) || 0;
        const weightage = parseFloat($(this).closest('tr').find('.weightage').val());

        // Calculate points based on the input and update points scored columns
        if ($(this).hasClass('percentage-self')) {
            const pointsSelf = calculatePoints(percentage, weightage);
            $(this).closest('tr').find('.points-self').val(pointsSelf.toFixed(2));
        } else {
            const pointsRO = calculatePoints(percentage, weightage);
            $(this).closest('tr').find('.points-ro').val(pointsRO.toFixed(2));
        }

        // Recalculate totals whenever an input changes
        calculateTotal();
    });
});


        // Form submission
        $('#performanceReviewForm').submit(function(e) {
            e.preventDefault();
            // Here you can add any final validation before submitting
            // If everything is valid, you can submit the form
            this.submit();
        });
    });

    

</script>

<script>
$(document).ready(function() {
 // Function to calculate points based on percentage and weightage
 function calculatePoints(percentage, weightage) {
        return (percentage * weightage) / 100;
    }

    // Function to calculate the total of Points Scored columns (self and RO) for Personal Excellence
    function calculatePersonalExcellenceTotal() {
        let totalWeightage = 0;
        let totalPercentageSelf = 0;
        let totalPointsSelf = 0;
        let totalPercentageRO = 0;
        let totalPointsRO = 0;

        // Loop through each row to calculate the total points for self and RO
        $('#personalExcellenceTable tbody tr:not(:last-child):not(:nth-last-child(2)):not(:nth-last-child(3))').each(function() {
            const weightage = parseFloat($(this).find('.weightage').val()) || 0;
            const percentageSelf = parseFloat($(this).find('.percentage-self').val()) || 0;
            const percentageRO = parseFloat($(this).find('.percentage-ro').val()) || 0;
            const pointsSelf = parseFloat($(this).find('.points-self').val()) || 0;
            const pointsRO = parseFloat($(this).find('.points-ro').val()) || 0;

            totalWeightage += weightage;
            totalPercentageSelf += percentageSelf;
            totalPointsSelf += pointsSelf;
            totalPercentageRO += percentageRO;
            totalPointsRO += pointsRO;
        });

        // Update the total fields
        $('#personalTotalWeightage').val(totalWeightage.toFixed(2));
        $('#personalTotalPercentageSelf').val(totalPercentageSelf.toFixed(2));
        $('#personalTotalPointsSelf').val(totalPointsSelf.toFixed(2));
        $('#personalTotalPercentageRO').val(totalPercentageRO.toFixed(2));
        $('#personalTotalPointsRO').val(totalPointsRO.toFixed(2));

        // Calculate and update total percentage
        const totalPercentage = (totalPointsSelf / totalWeightage) * 100;
        $('#personalTotalPercentage').val(totalPercentage.toFixed(2));

        // Update grade
        updatePersonalExcellenceGrade(totalPercentage);

        // Update hidden fields for form submission
        $('input[name="personal_total_weightage"]').val(totalWeightage.toFixed(2));
        $('input[name="personal_total_percentage_self"]').val(totalPercentageSelf.toFixed(2));
        $('input[name="personal_total_points_self"]').val(totalPointsSelf.toFixed(2));
        $('input[name="personal_total_percentage_ro"]').val(totalPercentageRO.toFixed(2));
        $('input[name="personal_total_points_ro"]').val(totalPointsRO.toFixed(2));
        $('input[name="personal_total_percentage"]').val(totalPercentage.toFixed(2));
    }

    // Function to update the grade based on total percentage
    function updatePersonalExcellenceGrade(percentage) {
        let grade, range, badgeClass;
        if (percentage < 65) {
            grade = 'Poor';
            range = 'Below 65';
            badgeClass = 'bg-danger';
        } else if (percentage < 75) {
            grade = 'Average';
            range = '65-74';
            badgeClass = 'bg-warning';
        } else if (percentage < 85) {
            grade = 'Satisfactory';
            range = '75-84';
            badgeClass = 'bg-info';
        } else if (percentage < 93) {
            grade = 'Good';
            range = '85-92';
            badgeClass = 'bg-primary';
        } else {
            grade = 'Excellent';
            range = 'Above 92';
            badgeClass = 'bg-success';
        }

        $('#personalGrade').text(`${grade} (${range})`).removeClass().addClass(`badge ${badgeClass}`);
        $('input[name="personal_grade"]').val(`${grade} (${range})`);
    }

    // Function to validate percentage input
    function validatePercentageInput(input) {
        let value = parseFloat(input.val());
        if (isNaN(value) || value < 0) {
            value = 0;
        } else if (value > 100) {
            value = 100;
        }
        input.val(value);
        return value;
    }

    // Event listener for input fields in the Percentage columns (self and RO)
    $('#personalExcellenceTable').on('input', '.percentage-self, .percentage-ro', function() {
        const row = $(this).closest('tr');
        const weightage = parseFloat(row.find('.weightage').val()) || 0;
        const percentageSelf = validatePercentageInput(row.find('.percentage-self'));
        const percentageRO = validatePercentageInput(row.find('.percentage-ro'));

        // Calculate points based on the input and update points scored columns
        const pointsSelf = calculatePoints(percentageSelf, weightage);
        const pointsRO = calculatePoints(percentageRO, weightage);

        row.find('.points-self').val(pointsSelf.toFixed(2));
        row.find('.points-ro').val(pointsRO.toFixed(2));

        // Recalculate totals
        calculatePersonalExcellenceTotal();
    });

    // Function to initialize the table
    function initializePersonalExcellenceTable() {
        // Set initial weightage values
        $('#personalExcellenceTable .weightage').each(function(index) {
            const weightageValues = [2, 2, 2, 2, 2, 2, 3];
            $(this).val(weightageValues[index % weightageValues.length]);
        });

        // Trigger initial calculation
        calculatePersonalExcellenceTotal();
    }

    // Initialize the table when the document is ready
    initializePersonalExcellenceTable();
});
    </script>
@endsection