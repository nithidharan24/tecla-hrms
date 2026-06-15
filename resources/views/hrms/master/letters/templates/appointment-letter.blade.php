<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Appointment Letter - {{ $companyName }}</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap');

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        @page{
            size:A4;
            margin:0;
        }

        body{
            font-family:'Lato', Arial, sans-serif;
            font-size:10.5pt;
            color:#1a1a1a;
            background:#fff;
            line-height:1.7;
        }

        .page{
            width:210mm;
            min-height:297mm;
            position:relative;
            overflow:hidden;
            background:#fff;
            margin:0 auto;
            padding-bottom:60px;
        }

        /* ───────────────────────────── */
        /* CORNERS */
        /* ───────────────────────────── */

        .corner-tl{
            position:absolute;
            top:0;
            left:0;
            width:6px;
            height:80px;
            background:#FF5722;
        }

        .corner-br{
            position:absolute;
            bottom:0;
            right:0;
            width:6px;
            height:80px;
            background:#FF5722;
        }

        /* ───────────────────────────── */
        /* WATERMARK */
        /* ───────────────────────────── */

        .watermark{
            position:absolute;
            top:50%;
            left:50%;
            transform:translate(-50%, -50%) rotate(-30deg);
            font-family:'Playfair Display', serif;
            font-size:74pt;
            font-weight:700;
            color:rgba(255,87,34,0.04);
            white-space:nowrap;
            pointer-events:none;
            z-index:0;
        }

        /* ───────────────────────────── */
        /* HEADER */
        /* ───────────────────────────── */

        .header{
            padding:28px 40px 0;
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
        }

        .logo-area img{
            max-height:52px;
            display:block;
        }

        .logo-placeholder{
            font-family:'Playfair Display', serif;
            font-size:26px;
            font-weight:700;
            color:#111;
        }

        .logo-placeholder span{
            color:#FF5722;
        }

        .logo-tagline{
            font-size:8px;
            color:#999;
            margin-top:2px;
        }

        .header-meta{
            text-align:right;
            padding-top:4px;
        }

        .header-meta .date-label{
            font-size:8px;
            text-transform:uppercase;
            color:#999;
        }

        .header-meta .date-value{
            font-size:11pt;
            font-weight:700;
            color:#111;
            margin-top:2px;
        }

        /* ───────────────────────────── */
        /* ACCENT BAR */
        /* ───────────────────────────── */

        .accent-bar{
            margin:18px 40px 0;
            height:3px;
            border-radius:2px;
            background:linear-gradient(
                90deg,
                #FF5722 0%,
                #FF8A65 60%,
                transparent 100%
            );
        }

        /* ───────────────────────────── */
        /* BODY */
        /* ───────────────────────────── */

        .letter-body{
            position:relative;
            z-index:1;
            padding:28px 40px 40px;
        }

        /* ───────────────────────────── */
        /* TITLE */
        /* ───────────────────────────── */

        .title-block{
            text-align:center;
            margin-bottom:28px;
        }

        .notice-badge{
            display:inline-block;
            background:#FF5722;
            color:#fff;
            padding:5px 18px;
            border-radius:20px;
            font-size:8px;
            font-weight:700;
            margin-bottom:10px;
        }

        .doc-title{
            font-family:'Playfair Display', serif;
            font-size:18pt;
            font-weight:700;
            color:#111;
            line-height:1.2;
        }

        .doc-subtitle{
            font-size:9pt;
            color:#999;
            margin-top:5px;
        }

        .title-rule{
            width:60px;
            height:3px;
            background:#FF5722;
            border-radius:2px;
            margin:12px auto 0;
        }

        /* ───────────────────────────── */
        /* EMPLOYEE CARD */
        /* ───────────────────────────── */

        .emp-card{
            background:#FFF8F6;
            border:0.5px solid #FFD5C8;
            border-left:4px solid #FF5722;
            border-radius:0 6px 6px 0;
            padding:14px 18px;
            margin-bottom:24px;
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:16px;
        }

        .emp-name{
            font-family:'Playfair Display', serif;
            font-size:13pt;
            font-weight:700;
            color:#111;
            margin-bottom:2px;
        }

        .emp-role{
            font-size:9pt;
            color:#FF5722;
            font-weight:700;
        }

        .emp-meta{
            text-align:right;
            font-size:9pt;
            color:#666;
            line-height:1.8;
        }

        .emp-meta .label{
            font-size:8pt;
            color:#aaa;
            text-transform:uppercase;
        }

        /* ───────────────────────────── */
        /* SECTION LABEL */
        /* ───────────────────────────── */

        .section-label{
            font-size:8px;
            color:#FF5722;
            font-weight:700;
            margin:20px 0 8px;
            display:flex;
            align-items:center;
            gap:8px;
        }

        .section-label::after{
            content:'';
            flex:1;
            height:0.5px;
            background:#FF5722;
            opacity:0.3;
        }

        /* ───────────────────────────── */
        /* OFFER BOX */
        /* ───────────────────────────── */

        .offer-box{
            background:#fff;
            border:1px solid #FFD5C8;
            border-radius:6px;
            overflow:hidden;
            margin-bottom:22px;
        }

        .offer-row{
            display:flex;
            align-items:center;
        }

        .offer-cell{
            flex:1;
            padding:18px 20px;
            text-align:center;
        }

        .offer-cell.left{
            background:#fff;
        }

        .offer-cell.right{
            background:#FFF8F6;
        }

        .offer-label{
            font-size:8px;
            color:#999;
            margin-bottom:6px;
            text-transform:uppercase;
        }

        .offer-value{
            font-family:'Playfair Display', serif;
            font-size:14pt;
            font-weight:700;
            color:#111;
        }

        .offer-value.highlight{
            color:#FF5722;
        }

        .offer-divider{
            padding:0 10px;
            display:flex;
            align-items:center;
            justify-content:center;
            border-left:0.5px solid #FFD5C8;
            border-right:0.5px solid #FFD5C8;
        }

        .offer-arrow{
            font-size:22px;
            color:#FF5722;
        }

        .joining-strip{
            background:#FF5722;
            color:#fff;
            text-align:center;
            padding:8px 16px;
            font-size:9pt;
            font-weight:600;
        }

        /* ───────────────────────────── */
        /* TEXT */
        /* ───────────────────────────── */

        p{
            font-size:10.5pt;
            color:#333;
            margin-bottom:12px;
            text-align:justify;
        }

        /* ───────────────────────────── */
        /* SIGNATURE */
        /* ───────────────────────────── */

        .signature-area{
            margin-top:34px;
        }

        .sig-closing{
            color:#555;
            margin-bottom:0;
        }

        .authorized-signature {
            height: 46px;
            margin: 8px 0 0;
            display: flex;
            align-items: flex-end;
        }

        .authorized-signature img {
            max-height: 44px;
            max-width: 170px;
            object-fit: contain;
            display: block;
        }

        .authorized-signature + .sig-space,
        .authorized-signature + .sig-name {
            margin-top: 4px;
        }

        .sig-space{
            margin-top:40px;
        }

        .sig-name{
            font-family:'Playfair Display', serif;
            font-size:12pt;
            font-weight:700;
            color:#111;
        }

        .sig-title{
            font-size:9pt;
            color:#FF5722;
            font-weight:700;
            margin-top:2px;
        }

        .sig-company{
            font-size:9.5pt;
            color:#777;
            margin-top:2px;
        }

        /* ───────────────────────────── */
        /* ACCEPTANCE */
        /* ───────────────────────────── */

        .acceptance-block{
            margin-top:28px;
            padding-top:18px;
            border-top:0.5px solid #ddd;
        }

        .sign-row{
            display:flex;
            gap:40px;
            margin-top:20px;
        }

        .sign-box{
            flex:1;
        }

        .sign-line{
            height:1px;
            background:#bbb;
            margin-bottom:6px;
        }

        .sign-label{
            font-size:8.5pt;
            color:#888;
            text-transform:uppercase;
        }

        /* ───────────────────────────── */
        /* FOOTER */
        /* ───────────────────────────── */

        .page-footer{
            position:absolute;
            bottom:18px;
            left:40px;
            right:40px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            border-top:0.5px solid #eee;
            padding-top:8px;
        }

        .footer-company{
            font-size:8pt;
            color:#bbb;
            text-transform:uppercase;
        }

        .footer-accent{
            width:24px;
            height:2px;
            background:#FF5722;
            border-radius:2px;
        }

        .footer-page{
            font-size:8pt;
            color:#bbb;
        }

        /* ───────────────────────────── */
        /* PAGE BREAK */
        /* ───────────────────────────── */

        .page-break{
            page-break-before:always;
        }

        /* ───────────────────────────── */
        /* ANNEXURE */
        /* ───────────────────────────── */

        .annexure-title-block{
            text-align:center;
            margin-bottom:20px;
        }

        .annex-tag{
            display:inline-block;
            background:#FF5722;
            color:#fff;
            padding:4px 16px;
            border-radius:20px;
            font-size:8px;
            font-weight:700;
            margin-bottom:8px;
        }

        .annexure-title{
            font-family:'Playfair Display', serif;
            font-size:16pt;
            font-weight:700;
            color:#111;
        }

        /* ───────────────────────────── */
        /* INFO TABLE */
        /* ───────────────────────────── */

        .info-table{
            width:100%;
            border-collapse:collapse;
            margin-bottom:18px;
        }

        .info-table td{
            border:0.5px solid #e5e5e5;
            padding:10px 12px;
            font-size:10pt;
        }

        .info-label{
            background:#fafafa;
            font-size:8.5pt;
            color:#777;
            font-weight:700;
            text-transform:uppercase;
            width:25%;
        }

        .info-value{
            font-weight:700;
            color:#111;
        }

        /* ───────────────────────────── */
        /* SALARY TABLE */
        /* ───────────────────────────── */

        .salary-table{
            width:100%;
            border-collapse:collapse;
            font-size:10pt;
        }

        .salary-table thead th{
            background:#FF5722;
            color:#fff;
            padding:10px 12px;
            text-align:left;
            font-size:9pt;
            border:1px solid #e85d1a;
        }

        .salary-table thead th.text-right{
            text-align:right;
        }

        .salary-table tbody td{
            border:0.5px solid #e8e8e8;
            padding:8px 12px;
            color:#333;
        }

        .salary-table tbody tr:nth-child(even) td{
            background:#fafafa;
        }

        .salary-table tbody td.text-right{
            text-align:right;
        }

        .subtotal-row td{
            background:#FFF3EE !important;
            border-top:1.5px solid #FF5722;
            border-bottom:1.5px solid #FF5722;
            color:#c94a0f;
            font-weight:700;
        }

        .total-row td{
            background:#FF5722 !important;
            color:#fff !important;
            font-weight:700;
            border:1px solid #e85d1a;
        }

        .table-footnote{
            margin-top:12px;
            text-align:center;
            font-size:9pt;
            color:#888;
            font-style:italic;
        }

    </style>
</head>

<body>

<!-- PAGE 1 -->

<div class="page">

    <div class="corner-tl"></div>
    <div class="corner-br"></div>

    <div class="watermark">APPOINTMENT</div>

    <!-- HEADER -->

    <div class="header">

        <div class="logo-area">

            @if(!empty($logoPath) && file_exists($logoPath))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}">
            @else

                <div class="logo-placeholder">
                    {{ $companyName }}<span>.</span>
                </div>

                <div class="logo-tagline">
                    HRMS
                </div>

            @endif

        </div>

        <div class="header-meta">

            <div class="date-label">
                Issued On
            </div>

            <div class="date-value">
                {{ date('d / m / Y') }}
            </div>

        </div>

    </div>

    <div class="accent-bar"></div>

    <!-- BODY -->

    <div class="letter-body">

        <!-- TITLE -->

        <div class="title-block">

            

            <div class="doc-title">
                Letter of Appointment
            </div>

          

            <div class="title-rule"></div>

        </div>

        <!-- EMPLOYEE CARD -->

   

        <!-- OFFER -->

      



        <!-- CONTENT -->

        <div class="section-label">
            Employment Terms
        </div>

        <p>
            We are pleased to appoint you as
            <strong>{{ $appointment->designation }}</strong>
            at <strong>{{ $companyName }}</strong>.
            Your appointment is based on your qualifications,
            performance during the selection process, and
            organizational requirements.
        </p>

        <p>
            You will be posted at
            <strong>{{ $appointment->work_location }}</strong>
            and will report to
            <strong>{{ $appointment->reporting_to }}</strong>.
            Your employment type will be
            <strong>{{ $appointment->employment_type }}</strong>.
        </p>

        <p>
            Your initial probation period shall be
            <strong>{{ $appointment->probation_period }}</strong>,
            during which your performance and conduct will be reviewed.
            Confirmation of employment will be subject to satisfactory performance.
        </p>

        <p>
            Your total annual Cost to Company (CTC) will be
            <strong>
                ₹{{ $appointment->annual_ctc }}/-
                ({{ $appointment->ctc_words }})
            </strong>.
            Detailed salary structure is enclosed in Annexure A.
        </p>

        <p>
            This appointment is subject to the terms and conditions
            of employment, company policies, confidentiality obligations,
            and successful completion of joining formalities.
        </p>

        <!-- SIGNATURE -->

        <div class="signature-area">

            <p class="sig-closing">
                Yours faithfully,
            </p>

            @if(!empty($offerSignatureDataUri))
                <div class="authorized-signature">
                    <img src="{{ $offerSignatureDataUri }}" alt="Authorized Signature">
                </div>
            @endif

            <div class="sig-space"></div>

            <div class="sig-name">
                {{ $gm_name }}
            </div>

            <div class="sig-title">
                {{ $gm_title }}
            </div>

            <div class="sig-company">
                {{ $companyName }}
            </div>

        </div>

        <!-- ACCEPTANCE -->

        <div class="acceptance-block">

            <p>
                I,
                <strong>
                    {{ $employee->firstname }}
                    {{ $employee->lastname }}
                </strong>,
                hereby accept the appointment and terms of employment.
            </p>

            <div class="sign-row">

                <div class="sign-box">

                    <div class="sign-line"></div>

                    <div class="sign-label">
                        Signature
                    </div>

                </div>

                <div class="sign-box">

                    <div class="sign-line"></div>

                    <div class="sign-label">
                        Date
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- FOOTER -->

    <div class="page-footer">

        <div class="footer-company">
            {{ $companyName }}
        </div>

        <div class="footer-accent"></div>

        <div class="footer-page">
            Page 1 of 2 · Confidential
        </div>

    </div>

</div>

<!-- PAGE 2 -->

<div class="page page-break">

    <div class="corner-tl"></div>
    <div class="corner-br"></div>

    <div class="watermark">APPOINTMENT</div>

    <!-- HEADER -->

    <div class="header">

        <div class="logo-area">

            @if(!empty($logoPath) && file_exists($logoPath))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}">
            @else

                <div class="logo-placeholder">
                    {{ $companyName }}<span>.</span>
                </div>

                <div class="logo-tagline">
                    HRMS
                </div>

            @endif

        </div>

        <div class="header-meta">

            <div class="date-label">
                Issued On
            </div>

            <div class="date-value">
                {{ date('d / m / Y') }}
            </div>

        </div>

    </div>

    <div class="accent-bar"></div>

    <!-- BODY -->

    <div class="letter-body">

        <div class="annexure-title-block">

            <div class="annex-tag">
                Annexure A
            </div>

            <div class="annexure-title">
                Salary Structure
            </div>

        </div>

        <!-- EMPLOYEE INFO -->

        <table class="info-table">

            <tr>

                <td class="info-label">
                    Employee Name
                </td>

                <td class="info-value">
                    {{ $employee->firstname }} {{ $employee->lastname }}
                </td>

                <td class="info-label">
                    Designation
                </td>

                <td class="info-value">
                    {{ $appointment->designation }}
                </td>

            </tr>

        </table>

        <!-- SALARY TABLE -->

        <table class="salary-table">

            <thead>

                <tr>

                    <th style="width:52%;">
                        Salary Component
                    </th>

                    <th class="text-right" style="width:24%;">
                        Per Month (₹)
                    </th>

                    <th class="text-right" style="width:24%;">
                        Per Annum (₹)
                    </th>

                </tr>

            </thead>

            <tbody>

                <tr>
                    <td>Basic</td>
                    <td class="text-right">{{ $appointment->basic_monthly }}</td>
                    <td class="text-right">{{ $appointment->basic_annual }}</td>
                </tr>

                <tr>
                    <td>HRA</td>
                    <td class="text-right">{{ $appointment->hra_monthly }}</td>
                    <td class="text-right">{{ $appointment->hra_annual }}</td>
                </tr>

                <tr>
                    <td>City Compensatory Allowance</td>
                    <td class="text-right">{{ $appointment->cca_monthly }}</td>
                    <td class="text-right">{{ $appointment->cca_annual }}</td>
                </tr>

                <tr>
                    <td>Statutory Bonus</td>
                    <td class="text-right">{{ $appointment->statutory_bonus_monthly }}</td>
                    <td class="text-right">{{ $appointment->statutory_bonus_annual }}</td>
                </tr>

                <tr>
                    <td>Training Allowance</td>
                    <td class="text-right">{{ $appointment->training_allowance_monthly }}</td>
                    <td class="text-right">{{ $appointment->training_allowance_annual }}</td>
                </tr>

                <tr>
                    <td>Special Allowance</td>
                    <td class="text-right">{{ $appointment->special_allowance_monthly }}</td>
                    <td class="text-right">{{ $appointment->special_allowance_annual }}</td>
                </tr>

                <tr>
                    <td>VPP</td>
                    <td class="text-right">{{ $appointment->vpp_monthly }}</td>
                    <td class="text-right">{{ $appointment->vpp_annual }}</td>
                </tr>

                <tr class="subtotal-row">
                    <td>Gross Salary</td>
                    <td class="text-right">{{ $appointment->gross_monthly }}</td>
                    <td class="text-right">{{ $appointment->gross_annual }}</td>
                </tr>

                <tr>
                    <td>PF — Employer Contribution</td>
                    <td class="text-right">{{ $appointment->pf_employer_monthly }}</td>
                    <td class="text-right">{{ $appointment->pf_employer_annual }}</td>
                </tr>

                <tr>
                    <td>ESI — Employer Contribution</td>
                    <td class="text-right">{{ $appointment->esi_employer_monthly }}</td>
                    <td class="text-right">{{ $appointment->esi_employer_annual }}</td>
                </tr>

                <tr>
                    <td>PF — Employee Contribution</td>
                    <td class="text-right">{{ $appointment->pf_employee_monthly }}</td>
                    <td class="text-right">{{ $appointment->pf_employee_annual }}</td>
                </tr>

                <tr>
                    <td>ESI — Employee Contribution</td>
                    <td class="text-right">{{ $appointment->esi_employee_monthly }}</td>
                    <td class="text-right">{{ $appointment->esi_employee_annual }}</td>
                </tr>

                <tr>
                    <td>Staff Welfare Fund</td>
                    <td class="text-right">{{ $appointment->staff_welfare_monthly }}</td>
                    <td class="text-right">{{ $appointment->staff_welfare_annual }}</td>
                </tr>

                <tr>
                    <td>Professional Tax</td>
                    <td class="text-right">{{ $appointment->prof_tax_monthly }}</td>
                    <td class="text-right">{{ $appointment->prof_tax_annual }}</td>
                </tr>

                <tr class="subtotal-row">
                    <td>Net Income</td>
                    <td class="text-right">{{ $appointment->net_income_monthly }}</td>
                    <td class="text-right">{{ $appointment->net_income_annual }}</td>
                </tr>

                <tr class="total-row">
                    <td>Cost to Company (CTC)</td>
                    <td class="text-right">{{ $appointment->ctc_monthly }}</td>
                    <td class="text-right">{{ $appointment->ctc_annual }}</td>
                </tr>

            </tbody>

        </table>

        <p class="table-footnote">
            * Income Tax as applicable will be deducted from salary.
            All figures are in Indian Rupees (₹).
        </p>

    </div>

    <!-- FOOTER -->

    <div class="page-footer">

        <div class="footer-company">
            {{ $companyName }}
        </div>

        <div class="footer-accent"></div>

        <div class="footer-page">
            Page 2 of 2 · Confidential
        </div>

    </div>

</div>

</body>
</html>