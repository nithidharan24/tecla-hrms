@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Add Hike Letter Template</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('hike-letter.index') }}">Hike Letters</a></li>
                    <li class="breadcrumb-item active">Add</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('hike-letter.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Template Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Enter template name" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="content">Template Content (HTML/Blade) <span class="text-danger">*</span></label>
                            @include('hrms.master.letters.partials.load-default-template', ['templateFile' => 'hike-letter.blade.php', 'buttonText' => 'Load Default'])
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="25" required>{{ old('content') }}</textarea>
                            @error('content')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">
                                Available variables: <code>@{{ $employee->firstname }}</code>, <code>@{{ $employee->lastname }}</code>, <code>@{{ $hike->effective_date }}</code>, <code>@{{ $hike->new_ctc }}</code>, <code>@{{ $hike->designation }}</code>, etc.
                            </small>
                        </div>

                        <div class="submit-section">
                            <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                            <a href="{{ route('hike-letter.index') }}" class="btn btn-secondary submit-btn">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadDefaultTemplate() {
    const defaultTemplate = `<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Hike Letter</title>
    <style>
        @page { margin: 20px; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #FF5722;
            padding-bottom: 12px;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #FF5722;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        .contact-info {
            font-size: 9px;
            line-height: 1.5;
            color: #555;
        }
        .manager-info {
            text-align: right;
            margin: 20px 0;
            font-size: 11px;
        }
        .manager-name {
            font-weight: bold;
            margin-bottom: 2px;
        }
        .letter-content {
            margin: 25px 0;
            line-height: 1.8;
            text-align: justify;
        }
        .subject {
            font-weight: bold;
            margin: 15px 0;
            text-decoration: underline;
        }
        .signature {
            margin-top: 50px;
            text-align: right;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            width: 200px;
            display: inline-block;
        }
        .page-break {
            page-break-before: always;
        }
        .annexure {
            margin-top: 30px;
        }
        .annexure-title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
            text-decoration: underline;
            letter-spacing: 2px;
        }
        .section-title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin: 15px 0;
        }
        .employee-info {
            margin: 15px 0;
        }
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .salary-table th,
        .salary-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 11px;
        }
        .salary-table th {
            background-color: #FF5722;
            color: white;
            font-weight: bold;
            text-align: left;
        }
        .salary-table .text-right {
            text-align: right;
        }
        .salary-table .total-row {
            font-weight: bold;
            background-color: #FF5722;
            color: white;
        }
        .note {
            margin-top: 15px;
            font-style: italic;
            font-size: 10px;
            text-align: center;
        }
        .bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">TECLA MEDIA</div>
        <div class="contact-info">
            Cell: 81224 29047<br>
            Head Office: 67, Jamindar Pada Salai Street, Vadagarai, Periyakulam. Theni - 625601<br>
            Branch Operational Office: 08, Red Bricks Vasantha, Susila Nagar, Kovilambakkam, Chennai - 600117<br>
            www.tecla.in
        </div>
    </div>

    <div class="manager-info">
        <div class="manager-name">A.SURENDER</div>
        <div>GENERAL MANAGER</div>
    </div>

    <div class="letter-content">
        <p>Dear @{{ $employee->firstname }} @{{ $employee->lastname }},</p>

        <p class="subject">Sub: Appraisal cum Increment Letter.</p>

        <p>In continuation to the performance assessment that was carried out recently, we are glad to inform you that your compensation stands revised with effect from <span class="bold">@{{ $hike->effective_date }}</span>, the revised total CTC per annum will be <span class="bold">Rs.@{{ $hike->new_ctc }}/- (Rupees @{{ $hike->new_ctc_words }})</span>.</p>

        <p>The Details of computation are as per Annexure A.</p>

        <p>We congratulate you for your hard work, enthusiasm, dedication and continuous effort in meeting the organization objective. We expect you to keep up your performance in the years to come and grow with the organization.</p>

        <p>Your salary details are strictly private and confidential and details in this letter must not be disclosed and discussed to others. Please acknowledge your acceptance of the revised terms by signing a duplicate copy of this letter.</p>

        <p class="bold">Wishing you all the best for future endeavors!!!</p>
    </div>

    <div class="signature">
        <p>For TECLA MEDIA,</p>
        <div class="signature-line"></div>
        <p>Authorized Signatory</p>
    </div>

    <div class="page-break"></div>

    <div class="header">
        <div class="company-name">TECLA MEDIA</div>
        <div class="contact-info">
            Head Office: 67, Jamindar Pada Salai Street, Vadagarai, Periyakulam. Theni - 625601<br>
            Branch Operational Office: 08, Red Bricks Vasantha, Susila Nagar, Kovilambakkam, Chennai - 600117
        </div>
    </div>

    <div class="annexure">
        <div class="annexure-title">ANNEXURE A</div>
        <div class="section-title">SALARY STRUCTURE</div>

        <div class="employee-info">
            <p><span class="bold">Name:</span> @{{ strtoupper($employee->firstname) }} @{{ strtoupper($employee->lastname) }}</p>
            <p><span class="bold">Designation:</span> @{{ $hike->designation }}</p>
        </div>

        <table class="salary-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Salary</th>
                    <th class="text-right" style="width: 25%;">Per Month</th>
                    <th class="text-right" style="width: 25%;">Per Annum</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic</td>
                    <td class="text-right">@{{ $hike->basic_monthly }}</td>
                    <td class="text-right">@{{ $hike->basic_annual }}</td>
                </tr>
                <tr>
                    <td>HRA</td>
                    <td class="text-right">@{{ $hike->hra_monthly }}</td>
                    <td class="text-right">@{{ $hike->hra_annual }}</td>
                </tr>
                <tr>
                    <td>City Compensatory Allowance</td>
                    <td class="text-right">@{{ $hike->cca_monthly }}</td>
                    <td class="text-right">@{{ $hike->cca_annual }}</td>
                </tr>
                <tr>
                    <td>Statutory Bonus</td>
                    <td class="text-right">@{{ $hike->statutory_bonus_monthly }}</td>
                    <td class="text-right">@{{ $hike->statutory_bonus_annual }}</td>
                </tr>
                <tr>
                    <td>Training Allowance</td>
                    <td class="text-right">@{{ $hike->training_allowance_monthly }}</td>
                    <td class="text-right">@{{ $hike->training_allowance_annual }}</td>
                </tr>
                <tr>
                    <td>Special Allowance</td>
                    <td class="text-right">@{{ $hike->special_allowance_monthly }}</td>
                    <td class="text-right">@{{ $hike->special_allowance_annual }}</td>
                </tr>
                <tr>
                    <td>VPP</td>
                    <td class="text-right">@{{ $hike->vpp_monthly }}</td>
                    <td class="text-right">@{{ $hike->vpp_annual }}</td>
                </tr>
                <tr class="total-row">
                    <td><strong>Gross</strong></td>
                    <td class="text-right"><strong>@{{ $hike->gross_monthly }}</strong></td>
                    <td class="text-right"><strong>@{{ $hike->gross_annual }}</strong></td>
                </tr>
                <tr>
                    <td>PF (Employer Contribution)</td>
                    <td class="text-right">@{{ $hike->pf_employer_monthly }}</td>
                    <td class="text-right">@{{ $hike->pf_employer_annual }}</td>
                </tr>
                <tr>
                    <td>ESI (Employer Contribution)</td>
                    <td class="text-right">@{{ $hike->esi_employer_monthly }}</td>
                    <td class="text-right">@{{ $hike->esi_employer_annual }}</td>
                </tr>
                <tr>
                    <td>PF (Employee Contribution)</td>
                    <td class="text-right">@{{ $hike->pf_employee_monthly }}</td>
                    <td class="text-right">@{{ $hike->pf_employee_annual }}</td>
                </tr>
                <tr>
                    <td>ESI (Employee Contribution)</td>
                    <td class="text-right">@{{ $hike->esi_employee_monthly }}</td>
                    <td class="text-right">@{{ $hike->esi_employee_annual }}</td>
                </tr>
                <tr>
                    <td>Staff Welfare Fund</td>
                    <td class="text-right">@{{ $hike->staff_welfare_monthly }}</td>
                    <td class="text-right">@{{ $hike->staff_welfare_annual }}</td>
                </tr>
                <tr>
                    <td>Prof. Tax</td>
                    <td class="text-right">@{{ $hike->prof_tax_monthly }}</td>
                    <td class="text-right">@{{ $hike->prof_tax_annual }}</td>
                </tr>
                <tr class="total-row">
                    <td><strong>Net Income</strong></td>
                    <td class="text-right"><strong>@{{ $hike->net_income_monthly }}</strong></td>
                    <td class="text-right"><strong>@{{ $hike->net_income_annual }}</strong></td>
                </tr>
                <tr class="total-row">
                    <td><strong>Cost to The Company</strong></td>
                    <td class="text-right"><strong>@{{ $hike->ctc_monthly }}</strong></td>
                    <td class="text-right"><strong>@{{ $hike->ctc_annual }}</strong></td>
                </tr>
            </tbody>
        </table>

        <p class="note">Income Tax as applicable will be deducted</p>
    </div>
</body>
</html>`;

    document.getElementById('content').value = defaultTemplate;
    alert('Default template loaded successfully!');
}
</script>
@endsection
