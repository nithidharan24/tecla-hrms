<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Offer Letter - Tecla Media</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: 'Lato', Arial, sans-serif;
            font-size: 10.5pt;
            color: #1a1a1a;
            background: #fff;
            line-height: 1.65;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 0;
            background: #fff;
            position: relative;
            overflow: hidden;
        }

        /* ── HEADER ── */
        .header {
            padding: 28px 40px 0;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .logo-area img {
            max-height: 52px;
            display: block;
        }

        .logo-placeholder {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 700;
            color: #111;
            letter-spacing: -0.5px;
        }

        .logo-placeholder span { color: #FF5722; }

        .logo-tagline {
            font-size: 8px;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #999;
            margin-top: 2px;
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

.authorized-signature + .sig-name {
    margin-top: 4px;
}

        .header-meta { text-align: right; padding-top: 4px; }

        .header-meta .date-label {
            font-size: 8px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #999;
        }

        .header-meta .date-value {
            font-size: 11pt;
            font-weight: 700;
            color: #1a1a1a;
        }

        /* ── ACCENT BAR ── */
        .accent-bar {
            margin: 18px 40px 0;
            height: 3px;
            background: linear-gradient(90deg, #FF5722 0%, #FF8A65 60%, transparent 100%);
            border-radius: 2px;
        }

        /* ── LETTER BODY ── */
        .letter-body { padding: 26px 40px 30px; }

        .doc-title {
            text-align: center;
            font-family: 'Playfair Display', serif;
            font-size: 14pt;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #111;
            text-transform: uppercase;
            border-bottom: 1.5px solid #FF5722;
            padding-bottom: 8px;
            margin-bottom: 22px;
        }

        .salutation { font-size: 11pt; font-weight: 700; color: #111; margin-bottom: 4px; }

        .subject-line {
            font-size: 10pt;
            font-weight: 700;
            color: #555;
            margin-bottom: 14px;
            font-style: italic;
        }

        p {
            margin-bottom: 11px;
            text-align: justify;
            color: #333;
            font-size: 10.5pt;
        }

        .section-label {
            font-size: 8px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #FF5722;
            font-weight: 700;
            margin: 18px 0 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-label::after {
            content: '';
            flex: 1;
            height: 0.5px;
            background: #FF5722;
            opacity: 0.3;
        }

        .ctc-box {
            background: #FFF8F6;
            border-left: 3px solid #FF5722;
            border-radius: 0 4px 4px 0;
            padding: 12px 16px;
            margin: 6px 0 14px;
        }

        .ctc-box p { margin: 0; font-size: 10.5pt; color: #333; }

        /* ── SIGNATURE ── */
        .signature-area { margin-top: 24px; }

        .signature-block { font-size: 10.5pt; line-height: 1.8; color: #333; }

        .sig-name {
            font-family: 'Playfair Display', serif;
            font-size: 12pt;
            font-weight: 700;
            color: #111;
            margin-top: 32px;
        }

        .sig-title {
            font-size: 9pt;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #FF5722;
            font-weight: 700;
        }

        .acceptance-block {
            margin-top: 24px;
            padding-top: 14px;
            border-top: 0.5px solid #ddd;
        }

        .acceptance-block p { font-size: 10pt; color: #444; margin-bottom: 16px; }

        .sign-line-row { display: flex; gap: 48px; margin-top: 8px; }

        .sign-line { flex: 1; }

        .sign-line .line { height: 1px; background: #bbb; margin-bottom: 5px; }

        .sign-line .label {
            font-size: 9pt;
            color: #888;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* ── PAGE BREAK ── */
        .page-break { page-break-before: always; }

        /* ── ANNEXURE ── */
        .annexure-title-block { text-align: center; margin-bottom: 18px; }

        .annexure-title-block .annex-tag {
            display: inline-block;
            background: #FF5722;
            color: #fff;
            font-size: 8px;
            letter-spacing: 3px;
            text-transform: uppercase;
            padding: 4px 16px;
            border-radius: 20px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .annexure-title-block h2 {
            font-family: 'Playfair Display', serif;
            font-size: 15pt;
            font-weight: 700;
            color: #111;
            letter-spacing: 1px;
        }

        /* ── TABLES ── */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }

        .info-table td { padding: 9px 12px; font-size: 10pt; border: 0.5px solid #e0e0e0; }

        .info-table .info-label {
            background: #fafafa;
            color: #666;
            font-weight: 700;
            font-size: 8.5pt;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            width: 24%;
        }

        .info-table .info-value { color: #111; font-weight: 700; width: 26%; }

        .salary-table { width: 100%; border-collapse: collapse; font-size: 10pt; }

        .salary-table thead th {
            background: #FF5722;
            color: #fff;
            font-weight: 700;
            padding: 9px 12px;
            text-align: left;
            font-size: 9pt;
            letter-spacing: 0.5px;
            border: 1px solid #e85d1a;
        }

        .salary-table thead th.text-right { text-align: right; }

        .salary-table tbody tr:nth-child(even) td { background: #FAFAFA; }

        .salary-table tbody td { padding: 8px 12px; border: 0.5px solid #e8e8e8; color: #333; }

        .salary-table tbody td.text-right { text-align: right; font-variant-numeric: tabular-nums; }

        .salary-table .subtotal-row td {
            background: #FFF3EE;
            border-top: 1.5px solid #FF5722;
            border-bottom: 1.5px solid #FF5722;
            font-weight: 700;
            color: #c94a0f;
        }

        .salary-table .subtotal-row td.text-right { text-align: right; }

        .salary-table .total-row td {
            background: #FF5722;
            color: #fff;
            font-weight: 700;
            font-size: 10.5pt;
            border: 1px solid #e85d1a;
        }

        .salary-table .total-row td.text-right { text-align: right; }

        .table-footnote {
            text-align: center;
            font-size: 9pt;
            color: #888;
            margin-top: 12px;
            font-style: italic;
        }

        /* ── WATERMARK ── */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-family: 'Playfair Display', serif;
            font-size: 72pt;
            font-weight: 700;
            color: rgba(255, 87, 34, 0.04);
            pointer-events: none;
            white-space: nowrap;
            z-index: 0;
        }

        /* ── FOOTER ── */
        .page-footer {
            position: absolute;
            bottom: 18px;
            left: 40px;
            right: 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 0.5px solid #eee;
            padding-top: 8px;
        }


        .page-footer .footer-company { font-size: 8pt; color: #bbb; letter-spacing: 1px; text-transform: uppercase; }

        .page-footer .footer-page { font-size: 8pt; color: #bbb; }

        .footer-accent { width: 24px; height: 2px; background: #FF5722; border-radius: 1px; }

        /* ── CORNER DECORATION ── */
        .corner-tl { position: absolute; top: 0; left: 0; width: 6px; height: 80px; background: #FF5722; }

        .corner-br { position: absolute; bottom: 0; right: 0; width: 6px; height: 80px; background: #FF5722; }
    </style>
</head>
<body>

{{-- ═══════════════════════════════════════════ PAGE 1 ═══ --}}
<div class="page" style="position:relative; padding-bottom: 60px;">
    <div class="corner-tl"></div>
    <div class="corner-br"></div>
    <div class="watermark">CONFIDENTIAL</div>

    <div class="header">
        <div class="logo-area">
            @if(!empty($logoPath) && file_exists($logoPath))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="{{ $companyName }} Logo">
            @else
                <div class="logo-placeholder">tecla<span>.</span></div>
                <div class="logo-tagline">HRMS</div>
            @endif
        </div>
        <div class="header-meta">
            <div class="date-label">Issued on</div>
            <div class="date-value">{{ date('d / m / Y') }}</div>
        </div>
    </div>

    <div class="accent-bar"></div>

    <div class="letter-body">
        <div class="doc-title">Letter of Offer &amp; Terms of Employment</div>

        <p class="salutation">Dear {{ $employee->firstname }} {{ $employee->lastname }},</p>
        <p class="subject-line">Re: Letter of Offer and Terms of Employment</p>

        <p>Thank you for exploring career opportunities with <strong>{{ $companyName }}</strong>. You have successfully completed our initial selection process and we are pleased to extend to you an offer of employment.</p>

        <p>This offer is based on your profile, relevant work experience, and performance in the selection process. You have been selected for the position of <strong>"{{ $appointment->designation }}"</strong> at {{ $companyName }}, posted at <strong>{{ $appointment->work_location }}</strong>.</p>

        <div class="section-label">Compensation</div>

        <div class="ctc-box">
            <p>Your Cost to Company (CTC) including all benefits will be <strong>Rs.{{ $appointment->annual_ctc }}/- per annum</strong> (Rupees {{ $appointment->ctc_words }} Only). The detailed computation is set out in <strong>Annexure A</strong> attached hereto.</p>
        </div>

        <div class="section-label">Acceptance</div>

        <p>Please confirm your acceptance of this offer by proposing your date of joining and returning the duly signed second copy of this document. You may also convey your acceptance via email. If not accepted within <strong>7 days</strong> of receipt, this offer is liable to lapse at the discretion of {{ $companyName }}. Kindly hand over your acceptance letter to the HR Department within the said period.</p>

        <p>Upon joining and successful completion of joining formalities, you will be issued a formal Letter of Appointment by {{ $companyName }}.</p>

        <div class="section-label">Conditions</div>

        <p>Your appointment is subject to satisfactory reference checks and clearance from any secrecy or service agreements you may have executed that could have a bearing on your association with us.</p>

        <div class="signature-area">
            <div class="signature-block">
                <p style="margin:0; color:#555;">Yours faithfully,</p>
                <p style="margin:0; color:#555;">For <strong>{{ $companyName }}</strong>,</p>
              @if(!empty($offerSignatureDataUri))
    <div class="authorized-signature">
        <img src="{{ $offerSignatureDataUri }}" alt="Authorized Signature">
    </div>
@endif

<div class="sig-name">{{ $gm_name }}</div>
<div class="sig-title">{{ $gm_title }}</div>
               
            </div>

            <div class="acceptance-block">
                <p>I, <strong>{{ $employee->firstname }}</strong>, hereby declare that I have read and understood the above-mentioned terms and accept the offer of employment.</p>
                <div class="sign-line-row">
                    <div class="sign-line">
                        <div class="line"></div>
                        <div class="label">Signature</div>
                    </div>
                    <div class="sign-line">
                        <div class="line"></div>
                        <div class="label">Date</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-footer">
        <div class="footer-company">{{ $companyName }}</div>
        <div class="footer-accent"></div>
        <div class="footer-page">Page 1 of 2 &nbsp;&middot;&nbsp; Confidential</div>
    </div>
</div>

{{-- ═══════════════════════════════════════════ PAGE 2 ═══ --}}
<div class="page page-break" style="position:relative; padding-bottom: 60px;">
    <div class="corner-tl"></div>
    <div class="corner-br"></div>
    <div class="watermark">CONFIDENTIAL</div>

    <div class="header">
        <div class="logo-area">
            @if(!empty($logoPath) && file_exists($logoPath))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="{{ $companyName }} Logo">
            @else
                <div class="logo-placeholder">tecla<span>.</span></div>
                <div class="logo-tagline">HRMS</div>
            @endif
        </div>
        <div class="header-meta">
            <div class="date-label">Issued on</div>
            <div class="date-value">{{ date('d / m / Y') }}</div>
        </div>
    </div>

    <div class="accent-bar"></div>

    <div class="letter-body">
        <div class="annexure-title-block">
            <div class="annex-tag">Annexure A</div>
            <h2>Salary Structure</h2>
        </div>

        {{-- Employee Info Row --}}
        <table class="info-table">
            <tr>
                <td class="info-label">Employee Name</td>
                <td class="info-value">{{ $employee->firstname }} {{ $employee->lastname }}</td>
                <td class="info-label">Designation</td>
                <td class="info-value">{{ $appointment->designation }}</td>
            </tr>
        </table>

        {{-- Salary Breakdown --}}
        <table class="salary-table">
            <thead>
                <tr>
                    <th style="width:52%;">Salary Component</th>
                    <th class="text-right" style="width:24%;">Per Month (&#8377;)</th>
                    <th class="text-right" style="width:24%;">Per Annum (&#8377;)</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Basic</td><td class="text-right">{{ $appointment->basic_monthly }}</td><td class="text-right">{{ $appointment->basic_annual }}</td></tr>
                <tr><td>HRA</td><td class="text-right">{{ $appointment->hra_monthly }}</td><td class="text-right">{{ $appointment->hra_annual }}</td></tr>
                <tr><td>City Compensatory Allowance</td><td class="text-right">{{ $appointment->cca_monthly }}</td><td class="text-right">{{ $appointment->cca_annual }}</td></tr>
                <tr><td>Statutory Bonus</td><td class="text-right">{{ $appointment->statutory_bonus_monthly }}</td><td class="text-right">{{ $appointment->statutory_bonus_annual }}</td></tr>
                <tr><td>Training Allowance</td><td class="text-right">{{ $appointment->training_allowance_monthly }}</td><td class="text-right">{{ $appointment->training_allowance_annual }}</td></tr>
                <tr><td>Special Allowance</td><td class="text-right">{{ $appointment->special_allowance_monthly }}</td><td class="text-right">{{ $appointment->special_allowance_annual }}</td></tr>
                <tr><td>VPP</td><td class="text-right">{{ $appointment->vpp_monthly }}</td><td class="text-right">{{ $appointment->vpp_annual }}</td></tr>

                <tr class="subtotal-row">
                    <td>Gross Salary</td>
                    <td class="text-right">{{ $appointment->gross_monthly }}</td>
                    <td class="text-right">{{ $appointment->gross_annual }}</td>
                </tr>

                <tr><td>PF &mdash; Employer Contribution</td><td class="text-right">{{ $appointment->pf_employer_monthly }}</td><td class="text-right">{{ $appointment->pf_employer_annual }}</td></tr>
                <tr><td>ESI &mdash; Employer Contribution</td><td class="text-right">{{ $appointment->esi_employer_monthly }}</td><td class="text-right">{{ $appointment->esi_employer_annual }}</td></tr>
                <tr><td>PF &mdash; Employee Contribution</td><td class="text-right">{{ $appointment->pf_employee_monthly }}</td><td class="text-right">{{ $appointment->pf_employee_annual }}</td></tr>
                <tr><td>ESI &mdash; Employee Contribution</td><td class="text-right">{{ $appointment->esi_employee_monthly }}</td><td class="text-right">{{ $appointment->esi_employee_annual }}</td></tr>
                <tr><td>Staff Welfare Fund</td><td class="text-right">{{ $appointment->staff_welfare_monthly }}</td><td class="text-right">{{ $appointment->staff_welfare_annual }}</td></tr>
                <tr><td>Professional Tax</td><td class="text-right">{{ $appointment->prof_tax_monthly }}</td><td class="text-right">{{ $appointment->prof_tax_annual }}</td></tr>

                <tr class="subtotal-row">
                    <td>Net Income</td>
                    <td class="text-right">{{ $appointment->net_income_monthly }}</td>
                    <td class="text-right">{{ $appointment->net_income_annual }}</td>
                </tr>

                <tr class="total-row">
                    <td>Cost to the Company (CTC)</td>
                    <td class="text-right">{{ $appointment->ctc_monthly }}</td>
                    <td class="text-right">{{ $appointment->ctc_annual }}</td>
                </tr>
            </tbody>
        </table>

        <p class="table-footnote">* Income Tax as applicable will be deducted from salary. All figures are in Indian Rupees (&#8377;).</p>
    </div>

    <div class="page-footer">
        <div class="footer-company">{{ $companyName }}</div>
        <div class="footer-accent"></div>
        <div class="footer-page">Page 2 of 2 &nbsp;&middot;&nbsp; Confidential</div>
    </div>
</div>

</body>
</html>