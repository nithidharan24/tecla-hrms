<!DOCTYPE html>
<html>
<head>
  <title>Resignation Details - {{ $resignation->employee_name }}</title>
  <style>
    body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; color: #333; }
    .container { width: 100%; margin: 0 auto; border: 1px solid #eee; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    h1, h2 { color: #d97706; border-bottom: 2px solid #d97706; padding-bottom: 5px; margin-bottom: 15px; }
    h1 { text-align: center; font-size: 24px; margin-bottom: 20px; }
    h2 { font-size: 18px; margin-top: 25px; }
    .section-content { margin-bottom: 20px; }
    .field { margin-bottom: 10px; }
    .field label { font-weight: bold; display: inline-block; width: 220px; vertical-align: top; }
    .field span { display: inline-block; vertical-align: top; width: calc(100% - 230px); }
    .file-flag { color: #2563eb; }
    .signature-box { border: 1px solid #ccc; padding: 10px; min-height: 80px; text-align: center; margin-top: 10px; }
    .signature-img { max-width: 150px; max-height: 100px; display: block; margin: 0 auto; }
    .muted { color: #6b7280; font-size: 12px; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Employee Resignation Details</h1>

    <h2>1. Employee Details</h2>
    <div class="section-content">
      <div class="field"><label>Employee Name:</label><span>{{ $resignation->employee_name }}</span></div>
      <div class="field"><label>Employee ID:</label><span>{{ $resignation->employee_id }}</span></div>
      <div class="field"><label>Department:</label><span>{{ $resignation->department ?? 'N/A' }}</span></div>
      <div class="field"><label>Designation:</label><span>{{ $resignation->designation ?? 'N/A' }}</span></div>
      <div class="field"><label>Reporting Manager:</label><span>{{ $resignation->reporting_manager ?? 'N/A' }}</span></div>
      <div class="field"><label>Official Email:</label><span>{{ $resignation->official_email ?? 'N/A' }}</span></div>
      <div class="field"><label>Contact Number:</label><span>{{ $resignation->contact_number ?? 'N/A' }}</span></div>
    </div>

    <h2>2. Resignation Details</h2>
    <div class="section-content">
      <div class="field"><label>Date of Resignation:</label><span>{{ $resignation->date_of_resignation ? $resignation->date_of_resignation->format('Y-m-d') : 'N/A' }}</span></div>
      <div class="field"><label>Last Working Day:</label><span>{{ $resignation->last_working_day ? $resignation->last_working_day->format('Y-m-d') : 'N/A' }}</span></div>
      <div class="field"><label>Notice Period Duration:</label><span>{{ $resignation->notice_period_duration ?? 'N/A' }}</span></div>
      <div class="field"><label>Mode of Resignation:</label><span>{{ $resignation->mode_of_resignation ?? 'N/A' }}</span></div>
    </div>

    <h2>3. Reason for Resignation</h2>
    <div class="section-content">
      <div class="field"><label>Reason:</label><span>{{ $resignation->reason_for_resignation ?? 'N/A' }}</span></div>
      <div class="field"><label>Detailed Explanation:</label><span>{{ $resignation->detailed_explanation ?? 'N/A' }}</span></div>
    </div>

    <h2>4. Exit Process Checklist</h2>
    <div class="section-content">
      <div class="field"><label>Responsibilities Handed Over:</label><span>{{ $resignation->responsibilities_handed_over ? 'Yes' : 'No' }}</span></div>
      <div class="field"><label>Person Handover To:</label><span>{{ $resignation->person_handover_to ?? 'N/A' }}</span></div>
      <div class="field"><label>Company Assets Returned:</label><span>{{ $resignation->company_assets_returned ? 'Yes' : 'No' }}</span></div>
      <div class="field"><label>Returned Items:</label><span>{{ $resignation->list_of_returned_items ?? 'N/A' }}</span></div>
      <div class="field"><label>Serve Full Notice Period:</label><span>{{ $resignation->serve_full_notice_period ? 'Yes' : 'No' }}</span></div>
      <div class="field"><label>Leave Planned During Notice:</label><span>{{ $resignation->leave_planned_during_notice ?? 'N/A' }}</span></div>
    </div>

    <h2>5. Documents</h2>
    <div class="section-content">
      <div class="field"><label>Resignation Letter:</label><span class="file-flag">@if($resignation->resignation_letter_path)File Available @else N/A @endif</span></div>
      <div class="field"><label>Medical Certificate:</label><span class="file-flag">@if($resignation->medical_certificate_path)File Available @else N/A @endif</span></div>
    </div>

    <h2>6. Declaration & Signature</h2>
    <div class="section-content">
      <div class="field"><label>Declaration Agreed:</label><span>{{ $resignation->declaration_agreed ? 'Yes' : 'No' }}</span></div>
      <div class="field"><label>Place:</label><span>{{ $resignation->declaration_place ?? 'N/A' }}</span></div>
      <div class="field"><label>Date:</label><span>{{ $resignation->declaration_date ? $resignation->declaration_date->format('Y-m-d') : 'N/A' }}</span></div>
      <div class="field">
        <label>Employee Signature:</label>
        @if($signatureBase64)
          <div class="signature-box">
            <img src="{{ $signatureBase64 }}" alt="Signature" class="signature-img">
            <p class="muted">Digital Signature</p>
          </div>
        @else
          <span>N/A</span>
        @endif
      </div>
    </div>
  </div>
</body>
</html>
