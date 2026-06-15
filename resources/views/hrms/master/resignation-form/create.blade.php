@extends('layouts.index')

@section('content')
<style>
    /* Mobile First Approach */
    .container {
        width: 100%;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        padding: 1rem;
    }

    /* Typography */
    .text-2xl { font-size: 1.5rem; line-height: 2rem; }
    .text-xl { font-size: 1.25rem; line-height: 1.75rem; }
    .text-lg { font-size: 1.125rem; line-height: 1.75rem; }
    .text-base { font-size: 1rem; line-height: 1.5rem; }
    .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
    .text-xs { font-size: 0.75rem; line-height: 1rem; }
    
    .font-bold { font-weight: 700; }
    .font-semibold { font-weight: 600; }
    .font-medium { font-weight: 500; }
    
    .text-gray-800 { color: #1f2937; }
    .text-gray-600 { color: #4b5563; }
    .text-gray-700 { color: #374151; }
    .text-gray-900 { color: #111827; }
    .text-red-500 { color: #ef4444; }
    .text-blue-600 { color: #2563eb; }
    .text-white { color: #fff; }

    /* Layout */
    .flex { display: flex; }
    .flex-col { flex-direction: column; }
    .flex-row { flex-direction: row; }
    .flex-wrap { flex-wrap: wrap; }
    .items-center { align-items: center; }
    .items-start { align-items: flex-start; }
    .justify-between { justify-content: space-between; }
    .justify-end { justify-content: flex-end; }
    .grid { display: grid; }
    .block { display: block; }
    .hidden { display: none; }
    
    /* Spacing */
    .space-y-2 > * + * { margin-top: 0.5rem; }
    .space-y-4 > * + * { margin-top: 1rem; }
    .gap-2 { gap: 0.5rem; }
    .gap-4 { gap: 1rem; }
    .gap-6 { gap: 1.5rem; }
    .p-2 { padding: 0.5rem; }
    .p-4 { padding: 1rem; }
    .p-6 { padding: 1.5rem; }
    .px-4 { padding-left: 1rem; padding-right: 1rem; }
    .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
    .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
    .m-0 { margin: 0; }
    .mt-1 { margin-top: 0.25rem; }
    .mt-2 { margin-top: 0.5rem; }
    .mt-3 { margin-top: 0.75rem; }
    .mt-4 { margin-top: 1rem; }
    .mt-6 { margin-top: 1.5rem; }
    .mb-2 { margin-bottom: 0.5rem; }
    .mb-4 { margin-bottom: 1rem; }
    .mb-6 { margin-bottom: 1.5rem; }
    .ml-2 { margin-left: 0.5rem; }
    .mr-2 { margin-right: 0.5rem; }

    /* Width */
    .w-full { width: 100%; }
    .w-auto { width: auto; }

    /* Borders & Shadows */
    .border { border-width: 1px; }
    .border-0 { border-width: 0; }
    .border-gray-300 { border-color: #d1d5db; }
    .border-red-400 { border-color: #f87171; }
    .border-red-500 { border-color: #ef4444 !important; }
    .rounded { border-radius: 0.25rem; }
    .rounded-lg { border-radius: 0.5rem; }
    .rounded-full { border-radius: 9999px; }
    .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
    .shadow-md { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0,0,0,.06); }

    /* Colors & Backgrounds */
    .bg-white { background-color: #fff; }
    .bg-gray-50 { background-color: #f9fafb; }
    .bg-gray-100 { background-color: #f3f4f6; }
    .bg-blue-50 { background-color: #eff6ff; }
    .bg-blue-600 { background-color: #2563eb; }
    .bg-green-500 { background-color: #22c55e; }
    .bg-red-100 { background-color: #fee2e2; }
    .hover\:bg-blue-600:hover { background-color: #1d4ed8; }
    .hover\:bg-blue-100:hover { background-color: #dbeafe; }

    /* Form Elements */
    input[type="text"],
    input[type="email"],
    input[type="number"],
    input[type="date"],
    input[type="file"],
    select,
    textarea {
        display: block;
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        background-color: #f9fafb;
        font-size: 1rem;
        color: #374151;
        box-sizing: border-box;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="number"]:focus,
    input[type="date"]:focus,
    select:focus,
    textarea:focus {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(59,130,246,.25);
        background-color: #fff;
    }

    /* File Input Styling */
    input[type="file"] {
        padding: 0.5rem;
        background-color: #fff;
    }

    /* Select Arrow */
    select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }

    /* Labels */
    label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.25rem;
    }

    /* Checkboxes */
    input[type="checkbox"] {
        width: 1.25rem;
        height: 1.25rem;
        border-radius: 0.25rem;
        border: 1px solid #d1d5db;
        background-color: #fff;
    }

    /* Tabs Navigation - Mobile Vertical, Desktop Horizontal */
    .tabs-container {
        display: flex;
        flex-direction: column;
        width: 100%;
        background: #f9fafb;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .tabs-scroll {
        display: flex;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .tabs-scroll::-webkit-scrollbar {
        display: none;
    }

    .tab-link {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        white-space: nowrap;
        font-weight: 500;
        color: #4b5563;
        text-decoration: none;
        border-bottom: 2px solid transparent;
        transition: all 0.2s;
        min-width: max-content;
    }

    .tab-link:hover {
        background-color: #e0f2fe;
        color: #1e40af;
    }

    .tab-link[data-active="true"] {
        background-color: #3b82f6;
        color: #fff;
        font-weight: 600;
        border-bottom-color: #1d4ed8;
    }

    .tab-content {
        display: none;
        padding: 1.5rem;
        background-color: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .tab-content.active {
        display: block;
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        border: none;
        width: 100%;
        box-sizing: border-box;
    }

    .btn-primary {
        background-color: #22c55e;
        color: #fff;
    }

    .btn-primary:hover {
        background-color: #16a34a;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background-color: #2563eb;
        color: #fff;
    }

    .btn-secondary:hover {
        background-color: #1d4ed8;
    }

    /* Grid System */
    .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
    .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }

    /* Lists */
    .list-disc { list-style-type: disc; }
    .list-inside { list-style-position: inside; }

    /* Alerts */
    .alert {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }

    .alert-error {
        background-color: #fee2e2;
        border: 1px solid #f87171;
        color: #b91c1c;
    }

    /* Mobile Header */
    .mobile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 1rem;
    }

    /* Desktop Styles */
    @media (min-width: 768px) {
        .container {
            padding: 1.5rem;
        }

        .md\:flex-row {
            flex-direction: row;
        }

        .md\:w-1\/4 {
            width: 25%;
        }

        .md\:w-3\/4 {
            width: 75%;
        }

        .md\:grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .md\:col-span-2 {
            grid-column: span 2 / span 2;
        }

        .tabs-container {
            flex-direction: row;
            height: auto;
        }

        .tabs-scroll {
            flex-direction: column;
            overflow-x: visible;
            width: 100%;
        }

        .tab-link {
            border-bottom: none;
            border-right: 2px solid transparent;
        }

        .tab-link[data-active="true"] {
            border-bottom-color: transparent;
            border-right-color: #1d4ed8;
        }

        .btn {
            width: auto;
        }
    }

    @media (min-width: 1024px) {
        .container {
            padding: 2rem;
        }
    }

    /* Small mobile adjustments */
    @media (max-width: 360px) {
        .container {
            padding: 0.5rem;
        }
        
        .text-2xl {
            font-size: 1.25rem;
        }
        
        .p-6 {
            padding: 1rem;
        }
        
        .tab-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
    }
</style>

<div class="container">
    <!-- Mobile Header -->
    <div class="mobile-header md:hidden">
        <h1 class="text-xl font-bold text-gray-800">Create Resignation</h1>
        <a href="{{ route('resignation.index') }}" class="btn btn-secondary text-sm py-2 px-3">
            Back
        </a>
    </div>

    <!-- Desktop Header -->
    <div class="hidden md:flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create Resignation</h1>
        <a href="{{ route('resignation.index') }}" class="btn btn-secondary">
            Back to Records
        </a>
    </div>

    @if ($errors->any())
      <div class="alert alert-error mb-4" role="alert">
        <strong class="font-bold">Whoops!</strong>
        <span class="block sm:inline">There were some problems with your input.</span>
        <ul class="mt-2 list-disc list-inside text-sm">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div id="recommendedAlert" class="alert alert-error hidden" role="alert" aria-live="polite">
      <strong class="font-bold">Recommended fields missing.</strong>
      <span class="block sm:inline">Please complete the following recommended fields before submitting:</span>
      <ul id="recommendedList" class="mt-2 list-disc list-inside text-sm"></ul>
    </div>

    <form id="resignationForm" action="{{ route('resignation.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-4 md:p-6">
        @csrf

        <div class="flex flex-col md:flex-row gap-4 md:gap-6">
            <!-- Tabs Navigation -->
            <div class="tabs-container">
                <div class="tabs-scroll">
                    <a href="#employee-details" class="tab-link" data-tab="step-1">
                        <span class="hidden md:inline">Employee Details</span>
                        <span class="md:hidden">Employee</span>
                    </a>
                    <a href="#resignation-details" class="tab-link" data-tab="step-2">
                        <span class="hidden md:inline">Resignation Details</span>
                        <span class="md:hidden">Resignation</span>
                    </a>
                    <a href="#reason" class="tab-link" data-tab="step-3">Reason</a>
                    <a href="#exit-checklist" class="tab-link" data-tab="step-4">
                        <span class="hidden md:inline">Exit Checklist</span>
                        <span class="md:hidden">Checklist</span>
                    </a>
                    <a href="#documents" class="tab-link" data-tab="step-5">
                        <span class="hidden md:inline">Documents</span>
                        <span class="md:hidden">Docs</span>
                    </a>
                    <a href="#declaration" class="tab-link" data-tab="step-6">
                        <span class="hidden md:inline">Declaration</span>
                        <span class="md:hidden">Declare</span>
                    </a>
                </div>
            </div>

            <!-- Form Content -->
            <div class="w-full">
                <!-- Step 1: Employee Details -->
                <div id="step-1" class="tab-content active">
                    <h2 class="text-lg md:text-xl font-semibold mb-4">1. Employee Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <div>
                            <label for="employee_name">Employee Name <span class="text-red-500">*</span></label>
                            <input type="text" id="employee_name" name="employee_name" value="{{ old('employee_name') }}">
                        </div>
                        <div>
                            <label for="employee_id">Employee ID <span class="text-red-500">*</span></label>
                            <input type="text" id="employee_id" name="employee_id" value="{{ old('employee_id') }}">
                        </div>
                        <div>
                            <label for="department">Department</label>
                            <input type="text" id="department" name="department" value="{{ old('department') }}">
                        </div>
                        <div>
                            <label for="designation">Designation</label>
                            <input type="text" id="designation" name="designation" value="{{ old('designation') }}">
                        </div>
                        <div>
                            <label for="reporting_manager">Reporting Manager</label>
                            <input type="text" id="reporting_manager" name="reporting_manager" value="{{ old('reporting_manager') }}">
                        </div>
                        <div>
                            <label for="official_email">Official Email</label>
                            <input type="email" id="official_email" name="official_email" value="{{ old('official_email') }}">
                        </div>
                        <div>
                            <label for="contact_number">Contact Number</label>
                            <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number') }}">
                        </div>
                    </div>
                </div>

                <!-- Step 2: Resignation Details -->
                <div id="step-2" class="tab-content hidden">
                    <h2 class="text-lg md:text-xl font-semibold mb-4">2. Resignation Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <div>
                            <label for="date_of_resignation">Date of Resignation <span class="text-red-500">*</span></label>
                            <input type="date" id="date_of_resignation" name="date_of_resignation" value="{{ old('date_of_resignation') }}">
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="last_working_day">Last Working Day <span class="text-red-500">*</span></label>
                                <label class="flex items-center text-sm text-gray-600">
                                    <input type="checkbox" id="auto_calc_lwd" class="mr-2" checked>
                                    Auto-calc
                                </label>
                            </div>
                            <input type="date" id="last_working_day" name="last_working_day" value="{{ old('last_working_day') }}">
                            <p class="text-xs text-gray-600 mt-1">When enabled, LWD = DoR + Notice duration (days).</p>
                        </div>
                        <div>
                            <label for="notice_period_duration">Notice Period Duration (days)</label>
                            <input type="number" min="0" id="notice_period_duration" name="notice_period_duration" value="{{ old('notice_period_duration') }}" placeholder="e.g., 30">
                        </div>
                        <div>
                            <label for="mode_of_resignation">Mode of Resignation <span class="text-red-500">*</span></label>
                            @php $modeVal = old('mode_of_resignation') @endphp
                            <select id="mode_of_resignation" name="mode_of_resignation">
                                <option value="">Select</option>
                                <option value="Voluntary" {{ $modeVal==='Voluntary' ? 'selected' : '' }}>Voluntary</option>
                                <option value="Mutual" {{ $modeVal==='Mutual' ? 'selected' : '' }}>Mutual</option>
                                <option value="Termination by Company" {{ $modeVal==='Termination by Company' ? 'selected' : '' }}>Termination by Company</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Reason for Resignation -->
                <div id="step-3" class="tab-content hidden">
                    <h2 class="text-lg md:text-xl font-semibold mb-4">3. Reason for Resignation</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <div>
                            <label for="reason_for_resignation">Reason <span class="text-red-500">*</span></label>
                            @php $reasonVal = old('reason_for_resignation') @endphp
                            <select id="reason_for_resignation" name="reason_for_resignation">
                                <option value="">Select reason</option>
                                <option {{ $reasonVal==='Personal Reasons' ? 'selected' : '' }}>Personal Reasons</option>
                                <option {{ $reasonVal==='Health Issues' ? 'selected' : '' }}>Health Issues</option>
                                <option {{ $reasonVal==='Higher Education' ? 'selected' : '' }}>Higher Education</option>
                                <option {{ $reasonVal==='Career Growth' ? 'selected' : '' }}>Career Growth</option>
                                <option {{ $reasonVal==='Salary Concerns' ? 'selected' : '' }}>Salary Concerns</option>
                                <option {{ $reasonVal==='Relocation' ? 'selected' : '' }}>Relocation</option>
                                <option {{ $reasonVal==='Work Environment' ? 'selected' : '' }}>Work Environment</option>
                                <option value="Others" {{ $reasonVal==='Others' ? 'selected' : '' }}>Others (Specify)</option>
                            </select>
                        </div>
                        <div id="other_reason_wrap" class="{{ old('reason_for_resignation')==='Others' ? '' : 'hidden' }}">
                            <label for="other_reason">Specify Other Reason</label>
                            <input type="text" id="other_reason" name="other_reason" value="{{ old('other_reason') }}">
                        </div>
                        <div class="md:col-span-2">
                            <label for="detailed_explanation">Detailed Explanation</label>
                            <textarea id="detailed_explanation" name="detailed_explanation" rows="3">{{ old('detailed_explanation') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Exit Checklist -->
                <div id="step-4" class="tab-content hidden">
                    <h2 class="text-lg md:text-xl font-semibold mb-4">4. Exit Process Checklist</h2>
                    <div class="grid grid-cols-1 gap-4 md:gap-6">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" id="responsibilities_handed_over" name="responsibilities_handed_over" value="1" class="mt-1" {{ old('responsibilities_handed_over') ? 'checked' : '' }}>
                            <label for="responsibilities_handed_over" class="text-sm text-gray-900">Have you handed over all responsibilities?</label>
                        </div>
                        <div>
                            <label for="person_handover_to">Name of Person Handover Done To</label>
                            <input type="text" id="person_handover_to" name="person_handover_to" value="{{ old('person_handover_to') }}">
                        </div>
                        <div class="flex items-start gap-3">
                            <input type="checkbox" id="company_assets_returned" name="company_assets_returned" value="1" class="mt-1" {{ old('company_assets_returned') ? 'checked' : '' }}>
                            <label for="company_assets_returned" class="text-sm text-gray-900">Have you returned company assets?</label>
                        </div>
                        <div>
                            <label for="list_of_returned_items">List of Returned Items</label>
                            <input type="text" id="list_of_returned_items" name="list_of_returned_items" value="{{ old('list_of_returned_items') }}" placeholder="Laptop, ID card, etc.">
                        </div>
                        <div class="flex items-start gap-3">
                            <input type="checkbox" id="serve_full_notice_period" name="serve_full_notice_period" value="1" class="mt-1" {{ old('serve_full_notice_period') ? 'checked' : '' }}>
                            <label for="serve_full_notice_period" class="text-sm text-gray-900">Willing to serve full notice period?</label>
                        </div>
                        <div>
                            <label for="leave_planned_during_notice">Any Leave Planned During Notice Period?</label>
                            <input type="text" id="leave_planned_during_notice" name="leave_planned_during_notice" value="{{ old('leave_planned_during_notice') }}" placeholder="e.g., Yes - 2 days for family event">
                        </div>
                    </div>
                </div>

                <!-- Step 5: Documents -->
                <div id="step-5" class="tab-content hidden">
                    <h2 class="text-lg md:text-xl font-semibold mb-4">5. Document Upload (Optional)</h2>
                    <div class="grid grid-cols-1 gap-4 md:gap-6">
                        <div>
                            <label for="resignation_letter">Resignation Letter (PDF/DOC/DOCX)</label>
                            <input type="file" id="resignation_letter" name="resignation_letter" accept=".pdf,.doc,.docx">
                        </div>
                        <div>
                            <label for="medical_certificate">Medical Certificate (if applicable)</label>
                            <input type="file" id="medical_certificate" name="medical_certificate" accept=".pdf,.doc,.docx">
                        </div>
                    </div>
                </div>

                <!-- Step 6: Declaration -->
                <div id="step-6" class="tab-content hidden">
                    <h2 class="text-lg md:text-xl font-semibold mb-4">6. Declaration & Signature</h2>
                    <div class="grid grid-cols-1 gap-4 md:gap-6">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" id="declaration_agreed" name="declaration_agreed" value="1" class="mt-1" {{ old('declaration_agreed') ? 'checked' : '' }}>
                            <label for="declaration_agreed" class="text-sm text-gray-900">
                                I hereby declare that the above information is true and I am submitting this resignation voluntarily. I agree to follow the notice period and exit formalities. <span class="text-red-500">*</span>
                            </label>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            <div>
                                <label for="declaration_place">Place</label>
                                <input type="text" id="declaration_place" name="declaration_place" value="{{ old('declaration_place') }}">
                            </div>
                            <div>
                                <label for="declaration_date">Date</label>
                                <input type="date" id="declaration_date" name="declaration_date" value="{{ old('declaration_date') }}">
                            </div>
                        </div>
                        <div>
                            <label for="employee_signature">Employee Signature (Image)</label>
                            <input type="file" id="employee_signature" name="employee_signature" accept=".jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end mt-6">
                    <button type="submit" class="btn btn-primary w-full md:w-auto">
                        Submit Resignation
                    </button>
                </div>
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
            
            // Scroll active tab into view on mobile
            if (window.innerWidth < 768) {
                const activeTab = $('[data-tab="' + tabId + '"]')[0];
                if (activeTab) {
                    activeTab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                }
            }
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
        $('#last_working_day').prop('readonly', $('#auto_calc_lwd').is(':checked'));
        calcLWD();

        // Reason Others toggle
        function syncOtherReason(){
            const sel = $('#reason_for_resignation').val();
            if (sel === 'Others') {
                $('#other_reason_wrap').removeClass('hidden');
            } else {
                $('#other_reason_wrap').addClass('hidden');
                $('#other_reason').val('');
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

            // Required fields list (hard-stop)
            const requiredIds = [
                'employee_name',
                'employee_id',
                'date_of_resignation',
                'last_working_day',
                'mode_of_resignation',
                'reason_for_resignation'
            ];

            requiredIds.forEach(function(id) {
                const el = $('#' + id);
                if (!el.val()) {
                    hasRequiredError = true;
                    markField(id, true);
                } else {
                    markField(id, false);
                }
            });

            // Email basic format check (if provided)
            const emailEl = $('#official_email');
            const emailVal = emailEl.val();
            if (emailVal && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
                hasRequiredError = true;
                markField('official_email', true);
            } else {
                markField('official_email', false);
            }

            // Declaration checkbox must be checked
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
                // Hide recommended alert if showing
                $('#recommendedAlert').addClass('hidden');
                $('#recommendedList').empty();
                return;
            }

            // Recommended fields (soft requirement: block submit and show message)
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

            const missing = recommended.filter(f => {
                const el = $('#' + f.id);
                return !el.val(); // empty strings treated as missing
            });

            // Clear previous highlights for recommended
            recommended.forEach(f => markField(f.id, false));

            if (missing.length > 0) {
                e.preventDefault();

                // Highlight missing recommended fields
                missing.forEach(f => markField(f.id, true));

                // Populate and show alert
                const list = $('#recommendedList').empty();
                missing.forEach(f => {
                    list.append(`<li>${f.label}</li>`);
                });
                $('#recommendedAlert').removeClass('hidden');

                // Jump to first missing recommended field's tab
                showTab(missing[0].tab);

                // Focus the field
                setTimeout(() => {
                    $('#' + missing[0].id).trigger('focus');
                }, 0);
            } else {
                // Hide alert if previously shown
                $('#recommendedAlert').addClass('hidden');
                $('#recommendedList').empty();
            }
        });

        // Clear red border live as user types/selects
        $('input, select, textarea').on('input change', function() {
            $(this).removeClass('border-red-500');
        });

        // Handle window resize for better mobile experience
        $(window).on('resize', function() {
            // Adjust layout if needed on resize
        });
    });
</script>
@endsection