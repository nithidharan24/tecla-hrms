<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Review</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        h1, h2 {
            color: #2c3e50;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .section {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <h1>Performance Review</h1>

    <div class="section">
        <h2>Basic Information</h2>
        <table>
            <tr>
                <th>Employee Name</th>
                <td>{{ $review->employee_name }}</td>
            </tr>
            <tr>
                <th>Employee ID</th>
                <td>{{ $review->employee_id }}</td>
            </tr>
            <tr>
                <th>Designation</th>
                <td>{{ $review->designation_id }}</td>
            </tr>
            <tr>
                <th>Department</th>
                <td>{{ $review->department_id }}</td>
            </tr>
            <tr>
                <th>Date of Join</th>
                <td>{{ $review->date_of_join }}</td>
            </tr>
            <tr>
                <th>RO Name</th>
                <td>{{ $review->ro_name }}</td>
            </tr>
            <tr>
                <th>RO Designation</th>
                <td>{{ $review->ro_designation }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Professional Excellence</h2>
        <table>
            <tr>
                <th>Key Result Area</th>
                <th>Key Performance Indicator</th>
                <th>Weightage</th>
                <th>Percentage Self</th>
                <th>Points Self</th>
                <th>Percentage RO</th>
                <th>Points RO</th>
            </tr>
            @foreach($review->professional_excellence as $pe)
            <tr>
                <td>{{ $pe->key_result_area }}</td>
                <td>{{ $pe->key_performance_indicator }}</td>
                <td>{{ $pe->weightage }}</td>
                <td>{{ $pe->percentage_self }}</td>
                <td>{{ $pe->points_self }}</td>
                <td>{{ $pe->percentage_ro }}</td>
                <td>{{ $pe->points_ro }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="section">
        <h2>Personal Excellence</h2>
        <table>
            <tr>
                <th>Personal Attribute</th>
                <th>Key Indicator</th>
                <th>Weightage</th>
                <th>Percentage Self</th>
                <th>Points Self</th>
                <th>Percentage RO</th>
                <th>Points RO</th>
            </tr>
            @foreach($review->personal_excellence as $pe)
            <tr>
                <td>{{ $pe->personal_attribute }}</td>
                <td>{{ $pe->key_indicator }}</td>
                <td>{{ $pe->weightage }}</td>
                <td>{{ $pe->percentage_self }}</td>
                <td>{{ $pe->points_self }}</td>
                <td>{{ $pe->percentage_ro }}</td>
                <td>{{ $pe->points_ro }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="section">
        <h2>Special Initiatives, Achievements, Contributions</h2>
        <table>
            <tr>
                <th>By Self</th>
                <th>RO's Comment</th>
                <th>HOD's Comment</th>
            </tr>
            @foreach($review->special_initiatives as $si)
            <tr>
                <td>{{ $si->achievement_self }}</td>
                <td>{{ $si->achievement_ro }}</td>
                <td>{{ $si->achievement_hod }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="section">
        <h2>Comments on the Role</h2>
        <table>
            <tr>
                <th>By Self</th>
                <th>RO's Comment</th>
                <th>HOD's Comment</th>
            </tr>
            @foreach($review->role_comments as $rc)
            <tr>
                <td>{{ $rc->alteration_self }}</td>
                <td>{{ $rc->alteration_ro }}</td>
                <td>{{ $rc->alteration_hod }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="section">
        <h2>Strengths and Areas for Improvement</h2>
        <table>
            <tr>
                <th>Strengths</th>
                <th>Areas for Improvement</th>
            </tr>
            @foreach($review->strengths_improvements as $si)
            <tr>
                <td>{{ $si->strength }}</td>
                <td>{{ $si->improvement }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="section">
        <h2>Personal Goals</h2>
        <table>
            <tr>
                <th>Goal Achieved during last year</th>
                <th>Goal set for current year</th>
            </tr>
            @foreach($review->personal_goals as $pg)
            <tr>
                <td>{{ $pg->last_year_goal }}</td>
                <td>{{ $pg->current_year_goal }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="section">
        <h2>Training Requirements</h2>
        <table>
            <tr>
                <th>By Self</th>
                <th>RO's Comment</th>
                <th>HOD's Comment</th>
            </tr>
            @foreach($review->training_requirements as $tr)
            <tr>
                <td>{{ $tr->training_self }}</td>
                <td>{{ $tr->training_ro }}</td>
                <td>{{ $tr->training_hod }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="section">
        <h2>RO's Assessment</h2>
        <table>
            <tr>
                <th>Parameter</th>
                <th>Assessment</th>
                <th>Details</th>
            </tr>
            <tr>
                <td>Work Issues</td>
                <td>{{ $review->ro_assessment->work_issues }}</td>
                <td>{{ $review->ro_assessment->work_issues_details }}</td>
            </tr>
            <tr>
                <td>Leave Issues</td>
                <td>{{ $review->ro_assessment->leave_issues }}</td>
                <td>{{ $review->ro_assessment->leave_issues_details }}</td>
            </tr>
            <tr>
                <td>Overall Performance</td>
                <td>{{ $review->ro_assessment->overall_performance }}</td>
                <td>{{ $review->ro_assessment->overall_performance_details }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>HRD's Assessment</h2>
        <table>
            <tr>
                <th>Parameter</th>
                <th>Available Points</th>
                <th>Points Scored</th>
                <th>Comments</th>
            </tr>
            <tr>
                <td>KRAs Target Achievement</td>
                <td>{{ $review->hrd_assessment->kra_points_available }}</td>
                <td>{{ $review->hrd_assessment->kra_points_scored }}</td>
                <td>{{ $review->hrd_assessment->kra_comment }}</td>
            </tr>
            <tr>
                <td>Professional Skills</td>
                <td>{{ $review->hrd_assessment->professional_points_available }}</td>
                <td>{{ $review->hrd_assessment->professional_points_scored }}</td>
                <td>{{ $review->hrd_assessment->professional_comment }}</td>
            </tr>
            <tr>
                <td>Personal Skills</td>
                <td>{{ $review->hrd_assessment->personal_points_available }}</td>
                <td>{{ $review->hrd_assessment->personal_points_scored }}</td>
                <td>{{ $review->hrd_assessment->personal_comment }}</td>
            </tr>
            <tr>
                <td>Overall Total</td>
                <td>{{ $review->hrd_assessment->total_points_available }}</td>
                <td>{{ $review->hrd_assessment->total_points_scored }}</td>
                <td>{{ $review->hrd_assessment->total_comment }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Signatures</h2>
        <table>
            <tr>
                <th>Role</th>
                <th>Name</th>
                <th>Signature</th>
                <th>Date</th>
            </tr>
            <tr>
                <td>Employee</td>
                <td>{{ $review->signatures->employee_name }}</td>
                <td>{{ $review->signatures->employee_signature }}</td>
                <td>{{ $review->signatures->employee_date }}</td>
            </tr>
            <tr>
                <td>Reporting Officer</td>
                <td>{{ $review->signatures->ro_name }}</td>
                <td>{{ $review->signatures->ro_signature }}</td>
                <td>{{ $review->signatures->ro_date }}</td>
            </tr>
            <tr>
                <td>HOD</td>
                <td>{{ $review->signatures->hod_name }}</td>
                <td>{{ $review->signatures->hod_signature }}</td>
                <td>{{ $review->signatures->hod_date }}</td>
            </tr>
            <tr>
                <td>HRD</td>
                <td>{{ $review->signatures->hrd_name }}</td>
                <td>{{ $review->signatures->hrd_signature }}</td>
                <td>{{ $review->signatures->hrd_date }}</td>
            </tr>
        </table>
    </div>
</body>
</html>