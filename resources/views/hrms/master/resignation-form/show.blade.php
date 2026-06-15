@extends('layouts.index')

@section('content')
<style>
  .container { max-width: 1200px; margin: 0 auto; padding: 1.5rem; }
  .shadow-md{ box-shadow: 0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06); }
  .rounded-lg{ border-radius: .5rem; }
  .p-8{ padding:2rem; }
  .mb-6{ margin-bottom:1.5rem; }
  .text-2xl{ font-size:1.5rem; }
  .text-xl{ font-size:1.25rem; }
  .font-bold{ font-weight:700; }
  .font-semibold{ font-weight:600; }
  .text-gray-800{ color:#1f2937; }
  .text-blue-600{ color:#2563eb; }
  .bg-white{ background:#fff; }
  .btn{ display:inline-flex; align-items:center; gap:.5rem; padding:.5rem .875rem; border-radius:.5rem; font-weight:600; font-size:.875rem; cursor:pointer; transition:all .15s; }
  .bg-blue-500{ background:#3b82f6; color:#fff; }
  .bg-blue-500:hover{ background:#2563eb; }
  .grid{ display:grid; gap:1rem; }
  @media(min-width:768px){ .grid-2{ grid-template-columns:repeat(2,minmax(0,1fr)); } }
  .section{ margin-bottom:2rem; }
  .section h2{ font-size:1.25rem; font-weight:600; color:#1f2937; padding-bottom:.5rem; border-bottom:2px solid #e5e7eb; margin-bottom:1rem; }
  .detail-item{ margin-bottom:1rem; }
  .label{ display:block; font-weight:600; color:#4b5563; margin-bottom:.25rem; }
  .value{ color:#374151; }
  .image-preview{ max-width:200px; height:auto; border:1px solid #e5e7eb; border-radius:.25rem; padding:.5rem; margin-top:.5rem; box-shadow:0 1px 3px rgba(0,0,0,.05); }
</style>

<div class="container">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Resignation Details: {{ $resignation->employee_name }}</h1>
    <a href="{{ route('resignation.index') }}" class="btn bg-blue-500">
     
      Back to Records
    </a>
  </div>

  <div class="bg-white shadow-md rounded-lg p-8">
    <div class="section">
      <h2>1. Employee Details</h2>
      <div class="grid grid-2">
        <div class="detail-item"><span class="label">Employee Name:</span><span class="value">{{ $resignation->employee_name }}</span></div>
        <div class="detail-item"><span class="label">Employee ID:</span><span class="value">{{ $resignation->employee_id }}</span></div>
        <div class="detail-item"><span class="label">Department:</span><span class="value">{{ $resignation->department ?? 'N/A' }}</span></div>
        <div class="detail-item"><span class="label">Designation:</span><span class="value">{{ $resignation->designation ?? 'N/A' }}</span></div>
        <div class="detail-item"><span class="label">Reporting Manager:</span><span class="value">{{ $resignation->reporting_manager ?? 'N/A' }}</span></div>
        <div class="detail-item"><span class="label">Official Email:</span><span class="value">{{ $resignation->official_email ?? 'N/A' }}</span></div>
        <div class="detail-item"><span class="label">Contact Number:</span><span class="value">{{ $resignation->contact_number ?? 'N/A' }}</span></div>
      </div>
    </div>

    <div class="section">
      <h2>2. Resignation Details</h2>
      <div class="grid grid-2">
        <div class="detail-item"><span class="label">Date of Resignation:</span><span class="value">{{ $resignation->date_of_resignation ? $resignation->date_of_resignation->format('Y-m-d') : 'N/A' }}</span></div>
        <div class="detail-item"><span class="label">Last Working Day:</span><span class="value">{{ $resignation->last_working_day ? $resignation->last_working_day->format('Y-m-d') : 'N/A' }}</span></div>
        <div class="detail-item"><span class="label">Notice Period Duration:</span><span class="value">{{ $resignation->notice_period_duration ?? 'N/A' }}</span></div>
        <div class="detail-item"><span class="label">Mode of Resignation:</span><span class="value">{{ $resignation->mode_of_resignation ?? 'N/A' }}</span></div>
      </div>
    </div>

    <div class="section">
      <h2>3. Reason for Resignation</h2>
      <div class="grid">
        <div class="detail-item"><span class="label">Reason:</span><span class="value">{{ $resignation->reason_for_resignation ?? 'N/A' }}</span></div>
        <div class="detail-item"><span class="label">Detailed Explanation:</span><span class="value">{{ $resignation->detailed_explanation ?? 'N/A' }}</span></div>
      </div>
    </div>

    <div class="section">
      <h2>4. Exit Process Checklist</h2>
      <div class="grid grid-2">
        <div class="detail-item"><span class="label">Responsibilities Handed Over:</span><span class="value">{{ $resignation->responsibilities_handed_over ? 'Yes' : 'No' }}</span></div>
        <div class="detail-item"><span class="label">Person Handover To:</span><span class="value">{{ $resignation->person_handover_to ?? 'N/A' }}</span></div>
        <div class="detail-item"><span class="label">Company Assets Returned:</span><span class="value">{{ $resignation->company_assets_returned ? 'Yes' : 'No' }}</span></div>
        <div class="detail-item"><span class="label">Returned Items:</span><span class="value">{{ $resignation->list_of_returned_items ?? 'N/A' }}</span></div>
        <div class="detail-item"><span class="label">Serve Full Notice Period:</span><span class="value">{{ $resignation->serve_full_notice_period ? 'Yes' : 'No' }}</span></div>
        <div class="detail-item"><span class="label">Leave Planned During Notice:</span><span class="value">{{ $resignation->leave_planned_during_notice ?? 'N/A' }}</span></div>
      </div>
    </div>

    <div class="section">
      <h2>5. Document Upload</h2>
      <div class="grid grid-2">
        <div class="detail-item">
          <span class="label">Resignation Letter:</span>
          <span class="value">
            @if($resignation->resignation_letter_path)
              <a class="text-blue-600" href="{{ asset($resignation->resignation_letter_path) }}" target="_blank">View File</a>
            @else
              N/A
            @endif
          </span>
        </div>
        <div class="detail-item">
          <span class="label">Medical Certificate:</span>
          <span class="value">
            @if($resignation->medical_certificate_path)
              <a class="text-blue-600" href="{{ asset($resignation->medical_certificate_path) }}" target="_blank">View File</a>
            @else
              N/A
            @endif
          </span>
        </div>
      </div>
    </div>

    <div class="section">
      <h2>6. Declaration & Signature</h2>
      <div class="grid grid-2">
        <div class="detail-item"><span class="label">Declaration Agreed:</span><span class="value">{{ $resignation->declaration_agreed ? 'Yes' : 'No' }}</span></div>
        <div class="detail-item"><span class="label">Place:</span><span class="value">{{ $resignation->declaration_place ?? 'N/A' }}</span></div>
        <div class="detail-item"><span class="label">Date:</span><span class="value">{{ $resignation->declaration_date ? $resignation->declaration_date->format('Y-m-d') : 'N/A' }}</span></div>
        <div class="detail-item">
          <span class="label">Signature:</span>
          <span class="value">
            @if($resignation->employee_signature_path)
              <img src="{{ asset($resignation->employee_signature_path) }}" alt="Signature" class="image-preview">
            @else
              N/A
            @endif
          </span>
        </div>
      </div>
    </div>

    <div class="section">
      <h2>7. HR/Manager Fields (Internal)</h2>
      <div class="grid grid-2">
        <div class="detail-item"><span class="label">Approval Status:</span><span class="value">{{ $resignation->approval_status ?? 'N/A' }}</span></div>
        <div class="detail-item"><span class="label">Resignation Acceptance Date:</span><span class="value">{{ $resignation->resignation_acceptance_date ? $resignation->resignation_acceptance_date->format('Y-m-d') : 'N/A' }}</span></div>
        <div class="detail-item"><span class="label">Exit Interview Date:</span><span class="value">{{ $resignation->exit_interview_scheduled_date ? $resignation->exit_interview_scheduled_date->format('Y-m-d') : 'N/A' }}</span></div>
        <div class="detail-item"><span class="label">Clearance Status:</span><span class="value">{{ $resignation->clearance_status ?? 'N/A' }}</span></div>
        <div class="detail-item"><span class="label">Final Settlement Status:</span><span class="value">{{ $resignation->final_settlement_status ?? 'N/A' }}</span></div>
        <div class="detail-item md:col-span-2"><span class="label">Feedback / Notes:</span><span class="value">{{ $resignation->feedback_notes ?? 'N/A' }}</span></div>
      </div>
    </div>
  </div>
</div>
@endsection
