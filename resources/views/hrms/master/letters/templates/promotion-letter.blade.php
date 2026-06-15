<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Promotion Letter - {{ $companyName }}</title>
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

        /* ── CORNER ACCENTS ── */
        .corner-tl { position: absolute; top: 0; left: 0; width: 6px; height: 80px; background: #FF5722; }
        .corner-br { position: absolute; bottom: 0; right: 0; width: 6px; height: 80px; background: #FF5722; }

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

        /* ── HEADER ── */
        .header {
            padding: 28px 40px 0;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .logo-area img { max-height: 52px; display: block; }

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
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #999;
            margin-top: 2px;
        }

        .header-meta { text-align: right; padding-top: 4px; }

        .header-meta .date-label {
            font-size: 8px;
            letter-spacing: 1px;
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

        /* ── BODY ── */
        .letter-body { padding: 28px 40px 80px; position: relative; z-index: 1; }

        /* ── CELEBRATION BADGE ── */
        .celebration-badge {
            text-align: center;
            margin-bottom: 28px;
        }

        .badge-pill {
            display: inline-block;
            background: #FF5722;
            color: #fff;
            font-size: 8px;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 5px 20px;
            border-radius: 20px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .doc-title {
            font-family: 'Playfair Display', serif;
            font-size: 17pt;
            font-weight: 700;
            color: #111;
            letter-spacing: 1px;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .doc-subtitle {
            font-size: 9pt;
            color: #999;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .title-rule {
            width: 60px;
            height: 3px;
            background: #FF5722;
            border-radius: 2px;
            margin: 10px auto 0;
        }

        /* ── EMPLOYEE INFO CARD ── */
        .emp-card {
            background: #FFF8F6;
            border: 0.5px solid #FFD5C8;
            border-left: 4px solid #FF5722;
            border-radius: 0 6px 6px 0;
            padding: 14px 18px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
        }

        .emp-card-left .emp-name {
            font-family: 'Playfair Display', serif;
            font-size: 13pt;
            font-weight: 700;
            color: #111;
            margin-bottom: 2px;
        }

        .emp-card-left .emp-role {
            font-size: 9pt;
            color: #FF5722;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .emp-card-right {
            text-align: right;
            font-size: 9pt;
            color: #666;
            line-height: 1.8;
        }

        .emp-card-right span {
            display: block;
        }

        .emp-card-right .label {
            color: #aaa;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── SECTION LABEL ── */
        .section-label {
            font-size: 8px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #FF5722;
            font-weight: 700;
            margin: 20px 0 6px;
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

        /* ── PROMOTION HIGHLIGHT BOX ── */
        .promotion-box {
            background: #fff;
            border: 1px solid #FFD5C8;
            border-radius: 6px;
            padding: 16px 20px;
            margin: 8px 0 20px;
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .promo-from, .promo-to {
            flex: 1;
            text-align: center;
        }

        .promo-tag {
            font-size: 7.5px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #aaa;
            margin-bottom: 4px;
        }

        .promo-value {
            font-family: 'Playfair Display', serif;
            font-size: 11pt;
            font-weight: 700;
            color: #333;
        }

        .promo-arrow {
            font-size: 20pt;
            color: #FF5722;
            font-weight: 300;
            flex-shrink: 0;
        }

        .promo-date-row {
            text-align: center;
            margin-top: 10px;
            font-size: 9pt;
            color: #888;
            border-top: 0.5px solid #FFD5C8;
            padding-top: 8px;
        }

        .promo-date-row strong { color: #FF5722; }

        /* ── BODY PARAGRAPHS ── */
        p {
            margin-bottom: 12px;
            text-align: justify;
            color: #333;
            font-size: 10.5pt;
        }

        /* ── SIGNATURE ── */
        .signature-area { margin-top: 28px; }

        .sig-closing { font-size: 10.5pt; color: #555; margin-bottom: 0; }

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

        .sig-name {
            font-family: 'Playfair Display', serif;
            font-size: 12pt;
            font-weight: 700;
            color: #111;
            margin-top: 36px;
        }

        .sig-title {
            font-size: 9pt;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #FF5722;
            font-weight: 700;
        }

        .sig-company {
            font-size: 9.5pt;
            color: #555;
            margin-top: 2px;
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

        .page-footer .footer-company {
            font-size: 8pt;
            color: #bbb;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .footer-accent { width: 24px; height: 2px; background: #FF5722; border-radius: 1px; }

        .page-footer .footer-page { font-size: 8pt; color: #bbb; }
    </style>
</head>
<body>

<div class="page" style="position:relative;">
    <div class="corner-tl"></div>
    <div class="corner-br"></div>
    <div class="watermark">PROMOTION</div>

    {{-- ── HEADER ── --}}
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
            <div class="date-label">Date</div>
            <div class="date-value">{{ \Carbon\Carbon::parse($promotion->promotion_date)->format('d / m / Y') }}</div>
        </div>
    </div>

    <div class="accent-bar"></div>

    <div class="letter-body">

        {{-- ── TITLE BLOCK ── --}}
        <div class="celebration-badge">
          
            <div class="doc-title">Letter of Promotion</div>
         
            <div class="title-rule"></div>
        </div>



        {{-- ── BODY ── --}}
        <div class="section-label" style="margin-top:22px;">Message</div>

        <p>We are delighted to inform you of your promotion from <strong>{{ $oldDesignationName }}</strong> to <strong>{{ $newDesignationName }}</strong>, effective <strong>{{ \Carbon\Carbon::parse($promotion->promotion_date)->format('F jS, Y') }}</strong>.</p>

        <p>This promotion is a direct result of your outstanding performance, dedication, and valuable contributions to our team and the company. Your commitment to excellence and your consistent efforts have been truly commendable, and we recognize your growth and potential within our organization.</p>

        <p>In your new role as <strong>{{ $newDesignationName }}</strong>, we are confident that you will continue to excel and take on new challenges with the same enthusiasm and professionalism you have consistently demonstrated. We look forward to your continued success and the positive impact you will undoubtedly make.</p>

        <p>We wish you all the best in your new position and are excited to see your continued development and achievements.</p>



                {{-- ── EMPLOYEE CARD ── --}}
        <div class="emp-card">
            <div class="emp-card-left">
                <div class="emp-name">{{ $employee->firstname }} {{ $employee->lastname }}</div>
                <div class="emp-role">{{ $newDesignationName }}</div>
            </div>
            <div class="emp-card-right">
                <span class="label">Employee ID</span>
                <span>{{ $employee->employeeid }}</span>
                <span class="label" style="margin-top:6px;">Department</span>
                <span>{{ $employee->department_name ?? 'N/A' }}</span>
                <span class="label" style="margin-top:6px;">Email</span>
                <span>{{ $employee->email ?? 'N/A' }}</span>
            </div>
        </div>

  

        {{-- ── SIGNATURE ── --}}
        <div class="signature-area">
            <p class="sig-closing">Sincerely,</p>
            @if(!empty($offerSignatureDataUri))
                <div class="authorized-signature">
                    <img src="{{ $offerSignatureDataUri }}" alt="Authorized Signature">
                </div>
            @endif
            <div class="sig-name">{{ $gm_name }}</div>
            <div class="sig-title">{{ $gm_title }}</div>
            <div class="sig-company">{{ $companyName }}</div>
        </div>

    </div>

    {{-- ── FOOTER ── --}}
    <div class="page-footer">
        <div class="footer-company">{{ $companyName }}</div>
        <div class="footer-accent"></div>
        <div class="footer-page">Confidential &nbsp;&middot;&nbsp; Promotion Letter</div>
    </div>
</div>

</body>
</html>