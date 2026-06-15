<!DOCTYPE html>
<html>
<head>
    <title>Policies Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            padding: 20px;
        }
        .container {
            position: relative;
            max-width: 100%;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }
        .watermark-image {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            opacity: 0.10;
            z-index: 0;
            pointer-events: none;
        }
        .watermark-image img {
            max-width: 500px;
            height: auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        .logo img {
            max-width: 150px;
            height: auto;
        }
        .report-details {
            text-align: right;
        }
        .report-details h1 {
            font-size: 20px;
            color: #333;
            margin-bottom: 5px;
        }
        .report-details p {
            color: #666;
            font-size: 13px;
        }
        .filter-info {
            margin: 15px 0;
            font-size: 14px;
            color: #555;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
            position: relative;
            z-index: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            position: relative;
            z-index: 1;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #999;
            text-align: center;
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Watermark background image -->
        @if(isset($logoSetting) && $logoSetting->logo)
        <div class="watermark-image">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($logoSetting->logo))) }}" alt="Watermark Logo">
        </div>
        @endif

        <!-- Header section with logo and report details -->
        <div class="header">
            @if(isset($logoSetting) && $logoSetting->logo)
            <div class="logo">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($logoSetting->logo))) }}" alt="Company Logo">
            </div>
            @endif
            <div class="report-details">
                <h1>Policies Report</h1>
                <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
                @if(request('from') || request('to'))
                <p>
                    @if(request('from')) From: {{ request('from') }} @endif
                    @if(request('to')) To: {{ request('to') }} @endif
                </p>
                @endif
            </div>
        </div>

        <!-- Filter information -->
        @if(request('from') || request('to') || request('department_id'))
        <div class="filter-info">
            <strong>Filters Applied:</strong>
            @if(request('from')) From: {{ request('from') }} @endif
            @if(request('to')) To: {{ request('to') }} @endif
            @if(request('department_id'))
                | Department: 
                @php
                    $dept = DB::table('department')->where('id', request('department_id'))->first();
                    echo $dept ? $dept->department : 'N/A';
                @endphp
            @endif
        </div>
        @endif

        <!-- Policies Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Policy Name</th>
                    <th>Description</th>
                    <th>Department</th>
                    <th>Created Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($policies as $policy)
                <tr>
                    <td>{{ $policy->id }}</td>
                    <td>{{ $policy->policy_name }}</td>
                    <td>{{ $policy->description }}</td>
                    <td>{{ $policy->department_name ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($policy->created_at)->format('Y-m-d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            This is a computer generated report. No signature required.
        </div>
    </div>
</body>
</html>