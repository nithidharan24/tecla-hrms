<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Memo Letter - {{ $companyName ?? 'Company Name' }}</title>
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

<div class="page" style="position:relative; padding-bottom: 60px;">
    <div class="corner-tl"></div>
    <div class="corner-br"></div>
    <div class="watermark">MEMO</div>

    <div class="header">
        <div class="logo-area">
            @if(!empty($logoPath) && file_exists($logoPath))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="{{ $companyName ?? 'Company' }} Logo">
            @else
                <div class="logo-placeholder">{{ $companyName ?? 'tecla' }}<span>.</span></div>
                <div class="logo-tagline">HRMS</div>
            @endif
        </div>
        <div class="header-meta">
            <div class="date-label">Date</div>
            <div class="date-value">{{ date('d / m / Y') }}</div>
        </div>
    </div>

    <div class="accent-bar"></div>

    <div class="letter-body">
        <div class="doc-title">Letter of Memo</div>

        <p>
            To,<br>
            <strong>{{ $employee->firstname ?? '' }} {{ $employee->lastname ?? '' }}</strong><br>
            {{ $employee->designation_name ?? '' }}<br>
            {{ $employee->department_name ?? '' }}<br>
            {{ $employee->branch_name ?? '' }}<br>
            <strong>{{ $employee->company ?? $companyName ?? '' }}</strong>
        </p>

        <p class="subject-line">Subject: {{ $memo->subject ?? 'Official Memo Regarding Work Conduct' }}</p>

        <p class="salutation">Dear {{ $employee->firstname ?? 'Employee' }},</p>

        <p>This is to inform you that certain issues have been observed regarding your recent performance and behavior at work. You are advised to take this memo seriously and improve your conduct and performance with immediate effect.</p>

        <p>Please note that continued negligence or violation of company rules may result in further disciplinary action.</p>

        <p>We hope you will treat this memo as a chance to correct your approach and align with company expectations.</p>

        <div class="signature-area">
            <div class="signature-block">
                <p style="margin:0; color:#555;">Regards,</p>
                <p style="margin:0; color:#555;">For <strong>{{ $employee->company ?? $companyName ?? 'Company Name' }}</strong>,</p>
                
                @if(!empty($offerSignatureDataUri))
                    <div class="authorized-signature">
                        <img src="{{ $offerSignatureDataUri }}" alt="Authorized Signature">
                    </div>
                @endif

                <div class="sig-name">{{ $gm_name ?? 'HR Department' }}</div>
                <div class="sig-title">{{ $gm_title ?? 'Human Resources' }}</div>
            </div>
        </div>
    </div>

    <div class="page-footer">
        <div class="footer-company">{{ $companyName ?? 'Company Name' }}</div>
        <div class="footer-accent"></div>
        <div class="footer-page">Confidential &nbsp;&middot;&nbsp; Memo Letter</div>
    </div>
</div>

</body>
</html>