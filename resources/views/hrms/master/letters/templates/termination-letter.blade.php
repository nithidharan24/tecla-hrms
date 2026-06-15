<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Termination Letter - {{ $companyName }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        @page { size: A4; margin: 0; }

        body {
            font-family: 'Lato', Arial, sans-serif;
            font-size: 11pt;
            color: #1a1a1a;
            background: #fff;
            line-height: 1.7;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            position: relative;
            overflow: hidden;
        }

        /* ── CORNER ACCENTS ── */
        .corner-tl { position: absolute; top: 0; left: 0; width: 5px; height: 72px; background: #FF5722; }
        .corner-br { position: absolute; bottom: 0; right: 0; width: 5px; height: 72px; background: #FF5722; }

        /* ── WATERMARK ── */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-family: 'Playfair Display', serif;
            font-size: 80pt;
            font-weight: 700;
            color: rgba(255, 87, 34, 0.035);
            pointer-events: none;
            white-space: nowrap;
            z-index: 0;
        }

        /* ── HEADER ── */
        .header {
            padding: 30px 44px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-area img { max-height: 50px; display: block; }

        .logo-placeholder {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
            color: #111;
        }

        .logo-placeholder span { color: #FF5722; }

        .logo-tagline {
            font-size: 9px;
            text-transform: uppercase;
            color: #bbb;
            margin-top: 1px;
            font-weight: 400;
        }

        .header-meta { text-align: right; }

        .header-meta .date-label {
            font-size: 8.5pt;
            color: #aaa;
            font-weight: 400;
            text-transform: uppercase;
        }

        .header-meta .date-value {
            font-size: 11pt;
            font-weight: 700;
            color: #222;
            margin-top: 1px;
        }

        /* ── ACCENT BAR ── */
        .accent-bar {
            margin: 16px 44px 0;
            height: 2px;
            background: linear-gradient(90deg, #FF5722 0%, #FF8A65 55%, transparent 100%);
            border-radius: 2px;
        }

        /* ── BODY ── */
        .letter-body {
            padding: 26px 44px 80px;
            position: relative;
            z-index: 1;
        }

        /* ── TITLE BLOCK ── */
        .title-block {
            text-align: center;
            margin-bottom: 26px;
            padding-bottom: 20px;
            border-bottom: 0.5px solid #eee;
        }

        .notice-tag {
            display: inline-block;
            background: #FF5722;
            color: #fff;
            font-size: 8pt;
            text-transform: uppercase;
            font-weight: 700;
            padding: 4px 18px;
            border-radius: 3px;
            margin-bottom: 10px;
        }

        .doc-title {
            font-family: 'Playfair Display', serif;
            font-size: 18pt;
            font-weight: 700;
            color: #111;
            text-transform: uppercase;
            line-height: 1.15;
        }

        .doc-company {
            font-size: 9.5pt;
            color: #999;
            text-transform: uppercase;
            margin-top: 5px;
            font-weight: 400;
        }

        .title-rule {
            width: 48px;
            height: 2.5px;
            background: #FF5722;
            border-radius: 2px;
            margin: 10px auto 0;
        }

        /* ── EMPLOYEE CARD ── */
        .emp-card {
            background: #FFF8F6;
            border-left: 4px solid #FF5722;
            border-top: 0.5px solid #FFD5C8;
            border-right: 0.5px solid #FFD5C8;
            border-bottom: 0.5px solid #FFD5C8;
            border-radius: 0 4px 4px 0;
            padding: 14px 18px;
            margin-bottom: 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .emp-name {
            font-family: 'Playfair Display', serif;
            font-size: 13.5pt;
            font-weight: 700;
            color: #111;
            margin-bottom: 2px;
        }

        .emp-role {
            font-size: 9pt;
            color: #FF5722;
            font-weight: 700;
            text-transform: uppercase;
        }

        .emp-meta {
            text-align: right;
            font-size: 9pt;
            color: #555;
            line-height: 2;
        }

        .emp-meta .meta-label {
            color: #bbb;
            font-size: 8pt;
            text-transform: uppercase;
            display: block;
        }

        .emp-meta .meta-value {
            display: block;
            font-weight: 600;
            color: #333;
        }

        /* ── SECTION HEADING ── */
        .section-heading {
            font-size: 8pt;
            text-transform: uppercase;
            color: #FF5722;
            font-weight: 700;
            margin: 18px 0 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-heading::after {
            content: '';
            flex: 1;
            height: 0.5px;
            background: #FFD5C8;
        }

        /* ── TERMINATION DETAILS BOX ── */
        .termination-box {
            border: 1px solid #FFD5C8;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 6px;
        }

        .term-grid {
            display: flex;
            align-items: stretch;
        }

        .term-cell {
            flex: 1;
            padding: 13px 18px;
            border-right: 0.5px solid #FFD5C8;
        }

        .term-cell:last-child { border-right: none; }

        .term-cell-label {
            font-size: 7.5pt;
            text-transform: uppercase;
            color: #aaa;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .term-cell-value {
            font-family: 'Playfair Display', serif;
            font-size: 11pt;
            font-weight: 700;
            color: #222;
        }

        .term-cell-value.highlight { color: #FF5722; }

        .term-footer-bar {
            background: #FF5722;
            color: #fff;
            text-align: center;
            padding: 7px 16px;
            font-size: 9pt;
            font-weight: 600;
        }

        /* ── BODY TEXT ── */
        p {
            margin-bottom: 12px;
            text-align: justify;
            color: #333;
            font-size: 11pt;
            line-height: 1.7;
        }

        /* ── SIGNATURE ── */
        .signature-area { margin-top: 30px; }

        .sig-closing {
            font-size: 11pt;
            color: #555;
            margin-bottom: 0;
            text-align: left;
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

        .sig-space { margin-top: 38px; }

        .sig-name {
            font-family: 'Playfair Display', serif;
            font-size: 12.5pt;
            font-weight: 700;
            color: #111;
        }

        .sig-title {
            font-size: 9pt;
            text-transform: uppercase;
            color: #FF5722;
            font-weight: 700;
            margin-top: 1px;
        }

        .sig-company {
            font-size: 9.5pt;
            color: #888;
            margin-top: 2px;
        }

        /* ── FOOTER ── */
        .page-footer {
            position: absolute;
            bottom: 18px;
            left: 44px;
            right: 44px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 0.5px solid #eee;
            padding-top: 8px;
        }

        .footer-left {
            font-size: 8.5pt;
            color: #ccc;
            text-transform: uppercase;
            font-weight: 400;
        }

        .footer-dot {
            width: 20px;
            height: 2px;
            background: #FF5722;
            border-radius: 1px;
        }

        .footer-right {
            font-size: 8.5pt;
            color: #ccc;
        }
    </style>
</head>
<body>

<div class="page">
    <div class="corner-tl"></div>
    <div class="corner-br"></div>
    <div class="watermark">TERMINATION</div>

    {{-- HEADER --}}
    <div class="header">
        <div class="logo-area">
            @if(!empty($logoPath) && file_exists($logoPath))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="{{ $companyName }} Logo">
            @else
                <div>
                    <div class="logo-placeholder">{{ $companyName }}<span>.</span></div>
                    <div class="logo-tagline">HRMS</div>
                </div>
            @endif
        </div>
        <div class="header-meta">
            <div class="date-label">Date</div>
            <div class="date-value">{{ \Carbon\Carbon::now()->format('d M Y') }}</div>
        </div>
    </div>

    <div class="accent-bar"></div>

    <div class="letter-body">

        {{-- TITLE --}}
        <div class="title-block">
        
            <div class="doc-title">Letter of Termination</div>
          
            <div class="title-rule"></div>
        </div>

    

        {{-- SALUTATION --}}
        <p>Dear <strong>{{ $employee->firstname }} {{ $employee->lastname }}</strong>,</p>

        {{-- TERMINATION DETAILS --}}
        <div class="section-heading">Termination Details</div>

        <div class="termination-box">
            <div class="term-grid">
                <div class="term-cell">
                    <div class="term-cell-label">Termination Type</div>
                    <div class="term-cell-value highlight">{{ $termination->termination_type }}</div>
                </div>
                <div class="term-cell">
                    <div class="term-cell-label">Notice Date</div>
                    <div class="term-cell-value">{{ \Carbon\Carbon::parse($termination->notice_date)->format('d M Y') }}</div>
                </div>
                <div class="term-cell">
                    <div class="term-cell-label">Last Working Day</div>
                    <div class="term-cell-value">{{ \Carbon\Carbon::parse($termination->termination_date)->format('d M Y') }}</div>
                </div>
            </div>
            <div class="term-footer-bar">
                Reason for Termination &mdash; {{ $termination->reason }}
            </div>
        </div>

        {{-- MESSAGE --}}
        <div class="section-heading" style="margin-top:20px;">Message</div>

        <p>This letter is to formally inform you of the termination of your employment with <strong>{{ $companyName }}</strong>, effective <strong>{{ \Carbon\Carbon::parse($termination->termination_date)->format('F j, Y') }}</strong>.</p>

        <p>Your last day of employment will be <strong>{{ \Carbon\Carbon::parse($termination->termination_date)->format('F j, Y') }}</strong>. Your notice period commenced on <strong>{{ \Carbon\Carbon::parse($termination->notice_date)->format('F j, Y') }}</strong>.</p>

        <p>During your employment, you served as <strong>{{ $employee->designation_name ?? 'Employee' }}</strong> in the <strong>{{ $employee->department_name ?? 'N/A' }}</strong> department, joining us on <strong>{{ \Carbon\Carbon::parse($employee->joiningdate)->format('F j, Y') }}</strong>. We appreciate the contributions you have made during your tenure.</p>

        <p>Further details regarding your final pay, benefits, and the return of any company property will be communicated to you separately by the HR department.</p>

        <p>We wish you the very best in your future endeavors.</p>

        {{-- SIGNATURE --}}
        <div class="signature-area">
            <p class="sig-closing">Sincerely,</p>
            @if(!empty($offerSignatureDataUri))
                <div class="authorized-signature">
                    <img src="{{ $offerSignatureDataUri }}" alt="Authorized Signature">
                </div>
            @endif
            <div class="sig-space"></div>
            <div class="sig-name">{{ $gm_name }}</div>
            <div class="sig-title">{{ $gm_title }}</div>
            <div class="sig-company">{{ $companyName }}</div>
        </div>

    </div>

    {{-- FOOTER --}}
    <div class="page-footer">
        <div class="footer-left">{{ $companyName }}</div>
        <div class="footer-dot"></div>
        <div class="footer-right">Confidential &nbsp;&middot;&nbsp; Termination Letter</div>
    </div>
</div>

</body>
</html>