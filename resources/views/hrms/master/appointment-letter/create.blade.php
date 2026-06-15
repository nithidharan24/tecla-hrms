@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Add Appointment Letter Template</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('appointment-letter.index') }}">Appointment Letters</a></li>
                    <li class="breadcrumb-item active">Add</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('appointment-letter.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Template Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}"
                                   placeholder="Enter template name" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="content">Template Content (HTML/Blade)
                                <span class="text-danger">*</span></label>
                            @include('hrms.master.letters.partials.load-default-template', ['templateFile' => 'appointment-letter.blade.php', 'buttonText' => 'Load Default'])
                            <textarea class="form-control @error('content') is-invalid @enderror"
                                      id="content" name="content" rows="25" required>{{ old('content') }}</textarea>
                            @error('content')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">
                                Available variables:
                                <code>@{{ $employee->firstname }}</code>,
                                <code>@{{ $employee->lastname }}</code>,
                                <code>@{{ $appointment->joining_date }}</code>,
                                <code>@{{ $appointment->designation }}</code>,
                                <code>@{{ $appointment->annual_ctc }}</code>,
                                <code>@{{ $companyName }}</code>,
                                <code>@{{ $gm_name }}</code>,
                                <code>@{{ $logo }}</code>, etc.
                            </small>
                        </div>

                        <div class="submit-section">
                            <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                            <a href="{{ route('appointment-letter.index') }}" class="btn btn-secondary submit-btn">Cancel</a>
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
    <title>Letter of Appointment</title>
    <style>
        @page { margin: 30px; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 30px;
            color: #000;
            font-size: 11px;
            line-height: 1.6;
        }
        .header-section { text-align: center; margin-bottom: 30px; }
        .company-logo { margin-bottom: 20px; }
        .company-logo img { max-width: 150px; height: auto; }
        .header-divider { border-bottom: 3px solid #FF5722; margin: 15px 0 20px 0; }
        .date-section { text-align: right; font-size: 11px; margin-bottom: 20px; font-weight: bold; }
        .letter-title { text-align: center; font-weight: bold; font-size: 14px; margin: 20px 0; text-decoration: underline; color: #000; }
        .content-section { margin: 15px 0; text-align: justify; }
        .section-heading { font-weight: bold; margin-top: 15px; margin-bottom: 5px; }
        .acceptance-section { margin-top: 40px; border-top: 1px solid #000; padding-top: 15px; }
        .signature-line { margin-top: 50px; }
        .page-break { page-break-before: always; }
        .annexure-title { text-align: center; font-weight: bold; font-size: 13px; margin: 20px 0; border: 1px solid #000; padding: 8px; }
        .salary-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .salary-table th, .salary-table td { border: 1px solid #000; padding: 6px 8px; font-size: 10px; }
        .salary-table th { background-color: #FF5722; color: white; font-weight: bold; text-align: left; }
        .salary-table .text-right { text-align: right; }
        .salary-table .total-row { font-weight: bold; background-color: #FF5722; color: white; }
        .note { margin-top: 15px; font-size: 10px; text-align: center; font-style: italic; }
    </style>
</head>
<body>
    <div class="header-section">
        @if($logo)
            @php
                $logoPath = public_path($logo);
                $logoExists = file_exists($logoPath);
            @endphp
            @if($logoExists)
                <div class="company-logo">
                    <img src="data:image/png;base64,@{{ base64_encode(file_get_contents($logoPath)) }}" alt="Company Logo">
                </div>
            @endif
        @endif
        <div class="header-divider"></div>
    </div>

    <div class="date-section">
        Date: @{{ date('d/m/Y') }}
    </div>

    <div class="letter-title">LETTER OF APPOINTMENT</div>

    <div class="content-section">
        <p>Dear @{{ $employee->firstname }} @{{ $employee->lastname }},</p>
        <p>We are glad to appoint you as "@{{ $appointment->designation }}" in our company @{{ $companyName }}.</p>

        <p class="section-heading">Remuneration</p>
        <p>Your total remuneration package per annum will consist CTC Rs.@{{ $appointment->annual_ctc }}/- per annum (Rupees @{{ $appointment->ctc_words }}). The breakup of your compensation package shall be as detailed in Annexure A.</p>

        <p class="section-heading">Commencement</p>
        <p>Your employment with the company @{{ $companyName }} will be effective from @{{ $appointment->joining_date }}. You shall initially be placed at @{{ $appointment->work_location }}.</p>

        <p class="section-heading">Reporting</p>
        <p>You will report to "@{{ $appointment->reporting_to }}".</p>

        <p>We welcome you to our team and look forward to your contribution.</p>

        <p>You will be under probation for a period of @{{ $appointment->probation_period }}.</p>
    </div>

    <div class="acceptance-section">
        <p>I, @{{ $employee->firstname }} @{{ $employee->lastname }}, accept the appointment at @{{ $companyName }}.</p>
        <div class="signature-line">
            <p>Sign: _____________________________ &nbsp;&nbsp;&nbsp;&nbsp; Date: _____________________</p>
        </div>
    </div>

    <div style="margin-top: 60px; border-top: 1px solid #000; padding-top: 20px;">
        <div class="company-footer">For @{{ $companyName }},</div>
        <div class="gm-name">@{{ $gm_name }}</div>
        <div class="gm-title">@{{ $gm_title }}</div>
    </div>

    <div class="page-break"></div>

    <div class="annexure-title">ANNEXURE A</div>
    <div class="annexure-title" style="font-size: 12px; margin-top: 5px;">SALARY STRUCTURE</div>

    <table class="salary-table">
        <thead>
            <tr>
                <th>Salary</th>
                <th class="text-right">Per Month</th>
                <th class="text-right">Per Annum</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Basic</td><td class="text-right">@{{ $appointment->basic_monthly }}</td><td class="text-right">@{{ $appointment->basic_annual }}</td></tr>
            <tr><td>HRA</td><td class="text-right">@{{ $appointment->hra_monthly }}</td><td class="text-right">@{{ $appointment->hra_annual }}</td></tr>
            <tr><td>City Compensatory Allowance</td><td class="text-right">@{{ $appointment->cca_monthly }}</td><td class="text-right">@{{ $appointment->cca_annual }}</td></tr>
            <tr><td>Statutory Bonus</td><td class="text-right">@{{ $appointment->statutory_bonus_monthly }}</td><td class="text-right">@{{ $appointment->statutory_bonus_annual }}</td></tr>
            <tr><td>Training Allowance</td><td class="text-right">@{{ $appointment->training_allowance_monthly }}</td><td class="text-right">@{{ $appointment->training_allowance_annual }}</td></tr>
            <tr><td>Special Allowance</td><td class="text-right">@{{ $appointment->special_allowance_monthly }}</td><td class="text-right">@{{ $appointment->special_allowance_annual }}</td></tr>
            <tr class="total-row"><td><strong>Gross</strong></td><td class="text-right"><strong>@{{ $appointment->gross_monthly }}</strong></td><td class="text-right"><strong>@{{ $appointment->gross_annual }}</strong></td></tr>
            <tr><td>PF (Employer Contribution)</td><td class="text-right">@{{ $appointment->pf_employer_monthly }}</td><td class="text-right">@{{ $appointment->pf_employer_annual }}</td></tr>
            <tr><td>ESI (Employer Contribution)</td><td class="text-right">@{{ $appointment->esi_employer_monthly }}</td><td class="text-right">@{{ $appointment->esi_employer_annual }}</td></tr>
            <tr><td>PF (Employee Contribution)</td><td class="text-right">@{{ $appointment->pf_employee_monthly }}</td><td class="text-right">@{{ $appointment->pf_employee_annual }}</td></tr>
            <tr><td>ESI (Employee Contribution)</td><td class="text-right">@{{ $appointment->esi_employee_monthly }}</td><td class="text-right">@{{ $appointment->esi_employee_annual }}</td></tr>
            <tr><td>Staff Welfare Fund</td><td class="text-right">@{{ $appointment->staff_welfare_monthly }}</td><td class="text-right">@{{ $appointment->staff_welfare_annual }}</td></tr>
            <tr><td>Prof. Tax</td><td class="text-right">@{{ $appointment->prof_tax_monthly }}</td><td class="text-right">@{{ $appointment->prof_tax_annual }}</td></tr>
            <tr class="total-row"><td><strong>Net Income</strong></td><td class="text-right"><strong>@{{ $appointment->net_income_monthly }}</strong></td><td class="text-right"><strong>@{{ $appointment->net_income_annual }}</strong></td></tr>
            <tr class="total-row"><td><strong>Cost to The Company</strong></td><td class="text-right"><strong>@{{ $appointment->ctc_monthly }}</strong></td><td class="text-right"><strong>@{{ $appointment->ctc_annual }}</strong></td></tr>
        </tbody>
    </table>

    <p class="note">Income Tax as applicable will be deducted</p>
</body>
</html>`;

    document.getElementById('content').value = defaultTemplate;
    alert('Default template loaded successfully!');
}
</script>
@endsection
