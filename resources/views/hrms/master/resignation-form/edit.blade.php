@extends('layouts.index')

@section('content')
@php
  $rec = $resignation;
  $reasonVal = old('reason_for_resignation', $rec->reason_for_resignation ?? '');
  $isOthers = \Illuminate\Support\Str::startsWith($reasonVal, 'Others');
  $otherExtract = $isOthers ? trim(preg_replace('/^Others:\s*/', '', $reasonVal)) : old('other_reason');
@endphp
<style>
    /* General Layout & Spacing (matching onboarding style) */
    .container { max-width: 1200px; margin-left: auto; margin-right: auto; padding: 1.5rem; }
    .shadow-md { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
    .rounded-lg { border-radius: 0.5rem; }
    .p-6 { padding: 1.5rem; } .p-8 { padding: 2rem; }
    .mb-6 { margin-bottom: 1.5rem; }
    .flex { display: flex; } .items-center { align-items: center; } .justify-between { justify-content: space-between; }
    .gap-8 { gap: 2rem; }
    .grid { display: grid; }
    .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
    @media (min-width: 768px) {
        .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .md\:col-span-2 { grid-column: span 2 / span 2; }
        .md\:flex-row { flex-direction: row; }
        .md\:w-1\/4 { width: 25%; } .md\:w-3\/4 { width: 75%; }
    }

    /* Typography */
    .text-2xl { font-size: 1.5rem; } .text-xl { font-size: 1.25rem; }
    .font-bold { font-weight: 700; } .font-semibold { font-weight: 600; }
    .text-sm { font-size: 0.875rem; } .text-xs { font-size: 0.75rem; }
    .text-gray-800 { color: #1f2937; } .text-gray-600 { color: #4b5563; } .text-gray-700 { color: #374151; } .text-gray-900 { color: #111827; }
    .text-red-500 { color: #ef4444; } .text-blue-600 { color: #2563eb; } .text-white { color: #fff; }
    .hover\:underline:hover { text-decoration-line: underline; }

    /* Colors & Backgrounds */
    .bg-white { background-color: #fff; }
    .bg-gray-50 { background-color: #f9fafb; }
    .bg-green-500 { background-color: #22c55e; } .bg-green-500:hover { background-color: #16a34a; }
    .bg-blue-600 { background-color: #2563eb; } .hover\:bg-blue-600:hover { background-color: #1d4ed8; }

    /* Borders & Shadows */
    .border { border-width: 1px; } .border-gray-300 { border-color: #d1d5db; }
    .border-red-400 { border-color: #f87171; } .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0,0,0,.06); }

    /* Form Elements (matching onboarding) */
    input[type="text"], input[type="email"], input[type="number"], input[type="date"], select, textarea {
        display: block; width: 100%; padding: 0.75rem 1rem; margin-top: 0.5rem;
        border: 1px solid #e5e7eb; border-radius: 0.5rem; background-color: #f9fafb;
        box-shadow: inset 0 1px 2px rgba(0,0,0,.05); font-size: 0.9375rem; color: #374151;
        transition: border-color .2s, box-shadow .2s, background-color .2s;
    }
    input[type="text"]:focus, input[type="email"]:focus, input[type="number"]:focus, input[type="date"]:focus, select:focus, textarea:focus {
        border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,.25); background-color: #fff;
    }
    label { display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.25rem; }
    .border-red-500 { border-color: #ef4444 !important; }

    /* File Inputs (matching onboarding) */
    .file\:mr-4 { margin-right: 1rem; } .file\:py-2 { padding-top: .5rem; padding-bottom: .5rem; }
    .file\:px-4 { padding-left: 1rem; padding-right: 1rem; } .file\:rounded-full { border-radius: 9999px; }
    .file\:border-0 { border-width: 0; } .file\:text-sm { font-size: .875rem; } .file\:font-semibold { font-weight: 600; }
    .file\:bg-blue-50 { background-color: #eff6ff; } .file\:text-blue-700 { color: #1d4ed8; }
    .hover\:file\:bg-blue-100:hover { background-color: #dbeafe; }

    /* Tabs (matching onboarding) */
    .tab-link {
        display: block; padding: 0.75rem 1rem; border-radius: 0.375rem; font-weight: 500; color: #4b5563; text-decoration: none;
        transition: background-color .2s, color .2s;
    }
    .tab-link:hover { background-color: #e0f2fe; color: #1e40af; }
    .tab-link[data-active="true"] { background-color: #3b82f6; color: #fff; font-weight: 600; box-shadow: 0 2px 5px rgba(0,0,0,.1); }
    .tab-content {
        display: none; padding: 1.5rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background-color: #fff;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -1px rgba(0,0,0,.06);
    }
    .tab-content.active { display: block; }

    /* Buttons (matching onboarding) */
    button, .btn {
        display: inline-flex; align-items: center; justify-content: center; padding: 0.625rem 1.25rem;
        border-radius: 0.5rem; font-weight: 600; font-size: 0.9375rem; cursor: pointer;
        transition: background-color .2s, transform .1s; box-shadow: 0 2px 4px rgba(0,0,0,.1); border: none;
    }
    .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,.15); }
    .btn-primary { background-color: #22c55e; color: #fff; }
    .btn-primary:hover { background-color: #16a34a; }

    /* Utility */
    .hidden { display: none; }
    .h-4 { height: 1rem; } .w-4 { width: 1rem; } .ml-2 { margin-left: 0.5rem; }

    /* Spacing utilities */
    .gap-6 { gap: 1.5rem; }
    .space-y-2 > * + * { margin-top: 0.5rem; }
    .mt-1 { margin-top: 0.25rem; }
    .mt-4 { margin-top: 1rem; }
    .mb-4 { margin-bottom: 1rem; }
    .list-disc { list-style-type: disc; }
    .list-inside { list-style-position: inside; }

    /* Align submit row to the right */
    .justify-content-end { justify-content: flex-end; }
</style>

<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Resignation</h1>
        <a href="{{ route('resignation.index') }}" class="btn bg-blue-600 hover:bg-blue-600 text-white">Back to Records</a>
    </div>

    @if ($errors->any())
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
        <strong class="font-bold">Whoops!</strong>
        <span class="block sm:inline">There were some problems with your input.</span>
        <ul class="mt-3 list-disc list-inside text-sm">
          @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
      </div>
    @endif

  
    <div id="recommendedAlert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4 hidden" role="alert" aria-live="polite">
      <strong class="font-bold">Recommended fields missing.</strong>
      <span class="block sm:inline">Please complete the following recommended fields before submitting:</span>
      <ul id="recommendedList" class="mt-3 list-disc list-inside text-sm"></ul>
    </div>

    <form id="resignationForm" action="{{ route('resignation.update', $rec->id) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-8 flex flex-col md:flex-row gap-6">
        @csrf
        @method('PUT')

        <div class="w-full md:w-1/4 bg-gray-50 p-4 rounded-lg shadow-inner">
            <nav class="space-y-2">
                <a href="#employee-details" class="tab-link" data-tab="step-1">Employee Details</a>
                <a href="#resignation-details" class="tab-link" data-tab="step-2">Resignation Details</a>
                <a href="#reason" class="tab-link" data-tab="step-3">Reason</a>
                <a href="#exit-checklist" class="tab-link" data-tab="step-4">Exit Checklist</a>
                <a href="#documents" class="tab-link" data-tab="step-5">Documents</a>
                <a href="#declaration" class="tab-link" data-tab="step-6">Declaration</a>
                <a href="#hr-fields" class="tab-link" data-tab="step-7">HR/Manager</a>
            </nav>
        </div>

        <div class="w-full md:w-3/4">
            <div id="step-1" class="tab-content active">
                <h2 class="text-xl font-semibold mb-4">1. Employee Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="employee_name">Employee Name <span class="text-red-500">*</span></label>
                        <input type="text" id="employee_name" name="employee_name" value="{{ old('employee_name', $rec->employee_name) }}" >
                    </div>
                    <div>
                        <label for="employee_id">Employee ID <span class="text-red-500">*</span></label>
                        <input type="text" id="employee_id" name="employee_id" value="{{ old('employee_id', $rec->employee_id) }}" >
                    </div>
                    <div>
                        <label for="department">Department</label>
                        <input type="text" id="department" name="department" value="{{ old('department', $rec->department) }}">
                    </div>
                    <div>
                        <label for="designation">Designation</label>
                        <input type="text" id="designation" name="designation" value="{{ old('designation', $rec->designation) }}">
                    </div>
                    <div>
                        <label for="reporting_manager">Reporting Manager</label>
                        <input type="text" id="reporting_manager" name="reporting_manager" value="{{ old('reporting_manager', $rec->reporting_manager) }}">
                    </div>
                    <div>
                        <label for="official_email">Official Email</label>
                        <input type="email" id="official_email" name="official_email" value="{{ old('official_email', $rec->official_email) }}">
                    </div>
                    <div>
                        <label for="contact_number">Contact Number</label>
                        <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number', $rec->contact_number) }}">
                    </div>
                </div>
            </div>

            <div id="step-2" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-4">2. Resignation Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="date_of_resignation">Date of Resignation <span class="text-red-500">*</span></label>
                        <input type="date" id="date_of_resignation" name="date_of_resignation" value="{{ old('date_of_resignation', $rec->date_of_resignation ? $rec->date_of_resignation->format('Y-m-d') : '') }}" >
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <label for="last_working_day">Last Working Day <span class="text-red-500">*</span></label>
                            <label class="flex items-center text-sm text-gray-600">
                                 For edit, default auto-calc OFF so we don't overwrite existing value 
                                <input type="checkbox" id="auto_calc_lwd" class="h-4 w-4">
                                <span class="ml-2">Auto-calc</span>
                            </label>
                        </div>
                        <input type="date" id="last_working_day" name="last_working_day" value="{{ old('last_working_day', $rec->last_working_day ? $rec->last_working_day->format('Y-m-d') : '') }}" >
                        <p class="text-xs text-gray-600 mt-1">When enabled, LWD = DoR + Notice duration (days).</p>
                    </div>
                    <div>
                        <label for="notice_period_duration">Notice Period Duration (days)</label>
                        <input type="number" min="0" id="notice_period_duration" name="notice_period_duration" value="{{ old('notice_period_duration', $rec->notice_period_duration) }}" placeholder="e.g., 30">
                    </div>
                    <div>
                        <label for="mode_of_resignation">Mode of Resignation <span class="text-red-500">*</span></label>
                        @php $modeVal = old('mode_of_resignation', $rec->mode_of_resignation) @endphp
                        <select id="mode_of_resignation" name="mode_of_resignation" >
                            <option value="">Select</option>
                            <option value="Voluntary" {{ $modeVal==='Voluntary' ? 'selected' : '' }}>Voluntary</option>
                            <option value="Mutual" {{ $modeVal==='Mutual' ? 'selected' : '' }}>Mutual</option>
                            <option value="Termination by Company" {{ $modeVal==='Termination by Company' ? 'selected' : '' }}>Termination by Company</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="step-3" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-4">3. Reason for Resignation</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="reason_for_resignation">Reason <span class="text-red-500">*</span></label>
                        <select id="reason_for_resignation" name="reason_for_resignation" >
                            <option value="">Select reason</option>
                            <option {{ $reasonVal==='Personal Reasons' ? 'selected' : '' }}>Personal Reasons</option>
                            <option {{ $reasonVal==='Health Issues' ? 'selected' : '' }}>Health Issues</option>
                            <option {{ $reasonVal==='Higher Education' ? 'selected' : '' }}>Higher Education</option>
                            <option {{ $reasonVal==='Career Growth' ? 'selected' : '' }}>Career Growth</option>
                            <option {{ $reasonVal==='Salary Concerns' ? 'selected' : '' }}>Salary Concerns</option>
                            <option {{ $reasonVal==='Relocation' ? 'selected' : '' }}>Relocation</option>
                            <option {{ $reasonVal==='Work Environment' ? 'selected' : '' }}>Work Environment</option>
                            <option value="Others" {{ $isOthers ? 'selected' : '' }}>Others (Specify)</option>
                        </select>
                    </div>
                    <div id="other_reason_wrap" class="{{ $isOthers ? '' : 'hidden' }}">
                        <label for="other_reason">Specify Other Reason</label>
                        <input type="text" id="other_reason" name="other_reason" value="{{ $otherExtract }}">
                    </div>
                    <div class="md:col-span-2">
                        <label for="detailed_explanation">Detailed Explanation</label>
                        <textarea id="detailed_explanation" name="detailed_explanation" rows="3">{{ old('detailed_explanation', $rec->detailed_explanation) }}</textarea>
                    </div>
                </div>
            </div>

            <div id="step-4" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-4">4. Exit Process Checklist</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-center">
                        <input type="checkbox" id="responsibilities_handed_over" name="responsibilities_handed_over" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded" {{ old('responsibilities_handed_over', $rec->responsibilities_handed_over) ? 'checked' : '' }}>
                        <label for="responsibilities_handed_over" class="ml-2 text-sm text-gray-900">Have you handed over all responsibilities?</label>
                    </div>
                    <div>
                        <label for="person_handover_to">Name of Person Handover Done To</label>
                        <input type="text" id="person_handover_to" name="person_handover_to" value="{{ old('person_handover_to', $rec->person_handover_to) }}">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="company_assets_returned" name="company_assets_returned" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded" {{ old('company_assets_returned', $rec->company_assets_returned) ? 'checked' : '' }}>
                        <label for="company_assets_returned" class="ml-2 text-sm text-gray-900">Have you returned company assets?</label>
                    </div>
                    <div>
                        <label for="list_of_returned_items">List of Returned Items</label>
                        <input type="text" id="list_of_returned_items" name="list_of_returned_items" value="{{ old('list_of_returned_items', $rec->list_of_returned_items) }}" placeholder="Laptop, ID card, etc.">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="serve_full_notice_period" name="serve_full_notice_period" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded" {{ old('serve_full_notice_period', $rec->serve_full_notice_period) ? 'checked' : '' }}>
                        <label for="serve_full_notice_period" class="ml-2 text-sm text-gray-900">Willing to serve full notice period?</label>
                    </div>
                    <div>
                        <label for="leave_planned_during_notice">Any Leave Planned During Notice Period? (Yes/No + Details)</label>
                        <input type="text" id="leave_planned_during_notice" name="leave_planned_during_notice" value="{{ old('leave_planned_during_notice', $rec->leave_planned_during_notice) }}" placeholder="e.g., Yes - 2 days for family event">
                    </div>
                </div>
            </div>

            <div id="step-5" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-4">5. Document Upload (Optional)</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="resignation_letter">Resignation Letter (PDF/DOC/DOCX)</label>
                        <input type="file" id="resignation_letter" name="resignation_letter" accept=".pdf,.doc,.docx" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @if($rec->resignation_letter_path)
                          <p class="text-xs text-gray-500 mt-1">Current: <a href="{{ asset($rec->resignation_letter_path) }}" target="_blank" class="text-blue-600 hover:underline">View File</a></p>
                        @endif
                    </div>
                    <div>
                        <label for="medical_certificate">Medical Certificate (if applicable)</label>
                        <input type="file" id="medical_certificate" name="medical_certificate" accept=".pdf,.doc,.docx" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @if($rec->medical_certificate_path)
                          <p class="text-xs text-gray-500 mt-1">Current: <a href="{{ asset($rec->medical_certificate_path) }}" target="_blank" class="text-blue-600 hover:underline">View File</a></p>
                        @endif
                    </div>
                </div>
            </div>

            <div id="step-6" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-4">6. Declaration & Signature</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-start">
                        <input type="checkbox" id="declaration_agreed" name="declaration_agreed" value="1" class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded" {{ old('declaration_agreed', $rec->declaration_agreed) ? 'checked' : '' }} >
                        <label for="declaration_agreed" class="ml-2 text-sm text-gray-900">
                            I hereby declare that the above information is true and I am submitting this resignation voluntarily. I agree to follow the notice period and exit formalities. <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <div>
                        <label for="declaration_place">Place</label>
                        <input type="text" id="declaration_place" name="declaration_place" value="{{ old('declaration_place', $rec->declaration_place) }}">
                    </div>
                    <div>
                        <label for="declaration_date">Date</label>
                        <input type="date" id="declaration_date" name="declaration_date" value="{{ old('declaration_date', $rec->declaration_date ? $rec->declaration_date->format('Y-m-d') : '') }}">
                    </div>
                    <div>
                        <label for="employee_signature">Employee Signature (Image)</label>
                        <input type="file" id="employee_signature" name="employee_signature" accept=".jpg,.jpeg,.png" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @if($rec->employee_signature_path)
                          <p class="text-xs text-gray-500 mt-1">Current: <a href="{{ asset($rec->employee_signature_path) }}" target="_blank" class="text-blue-600 hover:underline">View</a></p>
                        @endif
                    </div>
                </div>
            </div>

            <div id="step-7" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-4">7. HR/Manager Fields (Internal)</h2>
                <p class="text-sm text-gray-600 mb-4">These fields are typically updated after submission by HR.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="approval_status">Approval Status</label>
                        @php $appr = old('approval_status', $rec->approval_status) @endphp
                        <select id="approval_status" name="approval_status">
                            <option value="">Select</option>
                            <option {{ $appr==='Approved' ? 'selected' : '' }}>Approved</option>
                            <option {{ $appr==='Rejected' ? 'selected' : '' }}>Rejected</option>
                            <option {{ $appr==='On Hold' ? 'selected' : '' }}>On Hold</option>
                        </select>
                    </div>
                    <div>
                        <label for="resignation_acceptance_date">Resignation Acceptance Date</label>
                        <input type="date" id="resignation_acceptance_date" name="resignation_acceptance_date" value="{{ old('resignation_acceptance_date', $rec->resignation_acceptance_date ? $rec->resignation_acceptance_date->format('Y-m-d') : '') }}">
                    </div>
                    <div>
                        <label for="exit_interview_scheduled_date">Exit Interview Scheduled Date</label>
                        <input type="date" id="exit_interview_scheduled_date" name="exit_interview_scheduled_date" value="{{ old('exit_interview_scheduled_date', $rec->exit_interview_scheduled_date ? $rec->exit_interview_scheduled_date->format('Y-m-d') : '') }}">
                    </div>
                    <div>
                        <label for="clearance_status">Clearance Status</label>
                        <input type="text" id="clearance_status" name="clearance_status" value="{{ old('clearance_status', $rec->clearance_status) }}" placeholder="Finance, IT, Admin, etc.">
                    </div>
                    <div>
                        <label for="final_settlement_status">Final Settlement Status</label>
                        <input type="text" id="final_settlement_status" name="final_settlement_status" value="{{ old('final_settlement_status', $rec->final_settlement_status) }}">
                    </div>
                    <div class="md:col-span-2">
                        <label for="feedback_notes">Feedback / Notes</label>
                        <textarea id="feedback_notes" name="feedback_notes" rows="3">{{ old('feedback_notes', $rec->feedback_notes) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">Update Resignation</button>
            </div>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(function() {
        const tabLinks = $('.tab-link');
        const tabContents = $('.tab-content');

        function showTab(tabId) {
            tabContents.removeClass('active').addClass('hidden');
            tabLinks.attr('data-active', 'false').removeClass('bg-blue-600 text-white').addClass('text-gray-700 hover:bg-blue-100 hover:text-blue-700');
            $('#' + tabId).removeClass('hidden').addClass('active');
            $('[data-tab="' + tabId + '"]').attr('data-active', 'true').removeClass('text-gray-700 hover:bg-blue-100 hover:text-blue-700').addClass('bg-blue-600 text-white');
        }

        tabLinks.on('click', function(e) {
            e.preventDefault();
            const tabId = $(this).data('tab');
            showTab(tabId);
        });

        // Initialize first tab
        showTab('step-1');

        // Auto-calc Last Working Day
        function calcLWD() {
            const auto = $('#auto_calc_lwd').is(':checked');
            if (!auto) return;
            const dor = $('#date_of_resignation').val();
            const npd = parseInt($('#notice_period_duration').val() || '0', 10);
            if (!dor || Number.isNaN(npd)) return;
            const d = new Date(dor);
            d.setDate(d.getDate() + npd);
            const iso = d.toISOString().slice(0,10);
            $('#last_working_day').val(iso);
        }
        $('#date_of_resignation, #notice_period_duration').on('input', calcLWD);
        $('#auto_calc_lwd').on('change', function() {
            $('#last_working_day').prop('readonly', $(this).is(':checked'));
            if ($(this).is(':checked')) calcLWD();
        });
        // For edit: auto-calc is off initially, so keep field editable
        $('#last_working_day').prop('readonly', $('#auto_calc_lwd').is(':checked'));

        // Reason Others toggle
        function syncOtherReason(){
            const val = $('#reason_for_resignation').val();
            if (val === 'Others') {
                $('#other_reason_wrap').removeClass('hidden');
            } else {
                $('#other_reason_wrap').addClass('hidden');
            }
        }
        $('#reason_for_resignation').on('change', syncOtherReason);
        syncOtherReason();

        // Helper to highlight fields
        function markField(id, on) {
            const el = $('#' + id);
            if (on) el.addClass('border-red-500');
            else el.removeClass('border-red-500');
        }

        // Client-side validation: required + recommended
        $('#resignationForm').on('submit', function(e) {
            let hasRequiredError = false;

            // Required fields (hard-stop)
            const requiredIds = [
                'employee_name','employee_id','date_of_resignation','last_working_day','mode_of_resignation','reason_for_resignation'
            ];

            requiredIds.forEach(function(id) {
                const el = $('#' + id);
                if (!el.val()) { hasRequiredError = true; markField(id, true); }
                else { markField(id, false); }
            });

            // Email check if provided
            const emailEl = $('#official_email');
            const emailVal = emailEl.val();
            if (emailVal && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
                hasRequiredError = true; markField('official_email', true);
            } else {
                markField('official_email', false);
            }

            // Declaration must be checked
            if (!$('#declaration_agreed').is(':checked')) {
                hasRequiredError = true;
            }

            if (hasRequiredError) {
                e.preventDefault();
                alert('Please fill in all required fields and correct any errors.');
                const firstInvalid = $('.border-red-500').first();
                if (firstInvalid.length) {
                    const parentTab = firstInvalid.closest('.tab-content');
                    if (parentTab.length) showTab(parentTab.attr('id'));
                }
                $('#recommendedAlert').addClass('hidden');
                $('#recommendedList').empty();
                return;
            }

            // Recommended fields (block submit with message)
            const recommended = [
                { id: 'department', label: 'Department', tab: 'step-1' },
                { id: 'designation', label: 'Designation', tab: 'step-1' },
                { id: 'reporting_manager', label: 'Reporting Manager', tab: 'step-1' },
                { id: 'contact_number', label: 'Contact Number', tab: 'step-1' },
                { id: 'notice_period_duration', label: 'Notice Period Duration', tab: 'step-2' },
                { id: 'detailed_explanation', label: 'Detailed Explanation', tab: 'step-3' },
                { id: 'person_handover_to', label: 'Person Handover To', tab: 'step-4' },
                { id: 'list_of_returned_items', label: 'List of Returned Items', tab: 'step-4' },
                { id: 'leave_planned_during_notice', label: 'Leave Planned During Notice', tab: 'step-4' },
                { id: 'declaration_place', label: 'Declaration Place', tab: 'step-6' },
                { id: 'declaration_date', label: 'Declaration Date', tab: 'step-6' }
            ];

            // Clear prior highlights
            recommended.forEach(f => markField(f.id, false));

            const missing = recommended.filter(f => !$('#' + f.id).val());

            if (missing.length > 0) {
                e.preventDefault();

                // Highlight and show message
                missing.forEach(f => markField(f.id, true));
                const list = $('#recommendedList').empty();
                missing.forEach(f => list.append(`<li>${f.label}</li>`));
                $('#recommendedAlert').removeClass('hidden');

                // Navigate to first missing recommended field
                showTab(missing[0].tab);
                setTimeout(() => { $('#' + missing[0].id).trigger('focus'); }, 0);
            } else {
                $('#recommendedAlert').addClass('hidden');
                $('#recommendedList').empty();
            }
        });

        // Clear red border while typing
        $('input, select, textarea').on('input change', function() {
            $(this).removeClass('border-red-500');
        });
    });
</script>
@endsection
