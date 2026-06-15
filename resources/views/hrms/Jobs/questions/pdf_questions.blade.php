<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Question Paper</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 13px;
            line-height: 1.5;
            color: #333;
            background-color: #fff;
        }

        @page {
            size: A4;
            margin: 15mm;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid #1a237e;
            padding-bottom: 15px;
            margin-bottom: 20px;
            page-break-after: avoid;
        }

        .logo {
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 43%;
            


        }

        .logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            margin-top: 50px;
        }

        .header-content {
            flex: 1;
            text-align: center;
            padding: 0 20px;
        }

        .organization-name {
            font-size: 18px;
            font-weight: bold;
            color: #1a237e;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .exam-title {
            font-size: 16px;
            font-weight: bold;
            color: #000;
            margin-bottom: 3px;
        }

        .exam-subtitle {
            font-size: 12px;
            color: #666;
            margin-bottom: 8px;
        }

        .meta-info {
            flex: 1;
            text-align: right;
            border-left: 1px solid #ccc;
            padding-left: 20px;
            margin-right: 20px;
        }

        .meta-info div {
            font-size: 11px;
            margin-bottom: 4px;
            color: #555;
        }

        .meta-info-label {
            font-weight: bold;
            color: #333;
        }

        .instructions {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 12px 15px;
            margin-bottom: 20px;
            page-break-after: avoid;
        }

        .instructions h3 {
            font-size: 12px;
            font-weight: bold;
            color: #1a237e;
            margin-bottom: 8px;
            text-decoration: underline;
        }

        .instructions ul {
            list-style: none;
            padding-left: 0;
        }

        .instructions li {
            font-size: 11px;
            margin-bottom: 5px;
            padding-left: 18px;
            position: relative;
        }

        .instructions li:before {
            content: "•";
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        .question-container {
            margin-bottom: 20px;
            page-break-inside: avoid;
            margin-left: 20px;
        }

        .question-number {
            font-weight: bold;
            font-size: 13px;
            color: #1a237e;
            margin-bottom: 8px;
            display: flex;
            align-items: flex-start;
        }

        .question-text {
            margin-left: 0;
            margin-bottom: 12px;
            line-height: 1.6;
            color: #333;
            padding: 10px;
            background-color: #fafafa;
            border-left: 3px solid #1a237e;
        }

        .options {
            margin-left: 25px;
            margin-bottom: 15px;
        }

        .option {
            display: flex;
            margin-bottom: 8px;
            line-height: 1.5;
        }

        .option-letter {
            font-weight: bold;
            color: #1a237e;
            min-width: 25px;
            margin-right: 10px;
        }

        .option-text {
            flex: 1;
            color: #555;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #999;
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            page-break-inside: avoid;
        }

        .total-questions {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 4px;
            page-break-inside: avoid;
        }

        /* Page number styling */
        .page-number {
            position: fixed;
            bottom: 10mm;
            right: 10mm;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <!-- Professional header with logo, organization name, and metadata from general_settings -->
    <div class="header">
      <div class="logo">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($logoSetting->logo))) }}" alt="Company Logo">
            </div>

        <div class="header-content">
            <div class="organization-name">{{ $siteTitle }}</div>
            <div class="exam-title">Interview Question Paper</div>
            <div class="exam-subtitle">Assessment & Evaluation</div>
        </div>

        <!-- Updated meta-info to include contact phone from general_settings -->
        <div class="meta-info">
            <div>
                <span class="meta-info-label">Date:</span>
                <span>{{ $createdDate }}</span>
            </div>
            <div>
                <span class="meta-info-label">Total Questions:</span>
                <span>{{ $totalQuestions }}</span>
            </div>
            <div>
                <span class="meta-info-label">Email:</span>
                <span>{{ $contactEmail }}</span>
            </div>
            @if($contactPhone)
            <div>
                <span class="meta-info-label">Phone:</span>
                <span>{{ $contactPhone }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Professional instructions section -->
    <div class="instructions">
        <h3>INSTRUCTIONS TO CANDIDATES:</h3>
        <ul>
            <li>This question paper contains {{ $totalQuestions }} questions</li>
            <li>All questions are compulsory</li>
            <li>Each question has four options (A, B, C, D)</li>
            <li>Select the most appropriate answer for each question</li>
            <li>Do not make any unauthorized marks on the question paper</li>
            <li>Use only black or blue ink pen</li>
        </ul>
    </div>

    <!-- Professional question layout with enhanced formatting -->
    @foreach ($questions as $question)
        <div class="question-container">
            <div class="question-number">
                <span>Q{{ $loop->iteration }}:</span>
                @if($question->category)
                    <span style="margin-left: 10px; font-size: 11px; color: #666; background: #f0f0f0; padding: 2px 6px; border-radius: 3px;">
                        {{ $question->category }}
                    </span>
                @endif
            </div>

            <div class="question-text">
                {!! nl2br(e($question->question)) !!}
                @if($question->question_image)
                    <div style="margin-top: 10px; text-align: center;">
                        <img src="{{ public_path($question->question_image) }}" 
                             alt="Question Image" 
                             style="max-width: 100%; max-height: 200px; border-radius: 4px;">
                    </div>
                @endif
            </div>

            <div class="options">
                <div class="option">
                    <span class="option-letter">A)</span>
                    <span class="option-text">{!! nl2br(e($question->option_a)) !!}</span>
                </div>
                <div class="option">
                    <span class="option-letter">B)</span>
                    <span class="option-text">{!! nl2br(e($question->option_b)) !!}</span>
                </div>
                <div class="option">
                    <span class="option-letter">C)</span>
                    <span class="option-text">{!! nl2br(e($question->option_c)) !!}</span>
                </div>
                <div class="option">
                    <span class="option-letter">D)</span>
                    <span class="option-text">{!! nl2br(e($question->option_d)) !!}</span>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Professional footer with summary -->
    <div class="total-questions">
        <strong>Total Questions: {{ $totalQuestions }} | Document Generated: {{ date('d-m-Y H:i:s') }}</strong>
    </div>

    <!-- Updated footer to include contact phone and email from general_settings -->
    <div class="footer">
        <p>This is an official examination document. Reproduction without permission is strictly prohibited.</p>
        <p>Contact: {{ $contactEmail }}
            @if($contactPhone)
            | Phone: {{ $contactPhone }}
            @endif
        </p>
    </div>
</body>
</html>
