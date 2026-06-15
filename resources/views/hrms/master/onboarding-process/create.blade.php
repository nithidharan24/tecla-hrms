@extends('layouts.index')

@section('content')
<style>
    /* General Layout & Spacing */
    .container {
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        padding: 1.5rem;
    }
    .shadow-md {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .rounded-lg {
        border-radius: 0.5rem;
    }
    .p-6 {
        padding: 1.5rem;
    }
    .mb-6 {
        margin-bottom: 1.5rem;
    }
    .flex {
        display: flex;
    }
    .items-center {
        align-items: center;
    }
    .justify-between {
        justify-content: space-between;
    }
    .gap-4 {
        gap: 1rem;
    }
    .grid {
        display: grid;
    }
    .grid-cols-1 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
    @media (min-width: 768px) { /* md breakpoint */
        .md\:grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .md\:col-span-2 {
            grid-column: span 2 / span 2;
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
    }

    /* Typography */
    .text-2xl {
        font-size: 1.5rem;
    }
    .text-xl {
        font-size: 1.25rem;
    }
    .font-bold {
        font-weight: 700;
    }
    .font-semibold {
        font-weight: 600;
    }
    .text-sm {
        font-size: 0.875rem;
    }
    .text-xs {
        font-size: 0.75rem;
    }
    .font-medium {
        font-weight: 500;
    }
    .text-gray-800 {
        color: #1f2937;
    }
    .text-gray-600 {
        color: #4b5563;
    }
    .text-gray-700 {
        color: #374151;
    }
    .text-gray-900 {
        color: #111827;
    }
    .text-red-500 {
        color: #ef4444;
    }
    .text-red-700 {
        color: #b91c1c;
    }
    .text-blue-600 {
        color: #2563eb;
    }
    .text-blue-700 {
        color: #1d4ed8;
    }
    .text-white {
        color: #fff;
    }
    .list-disc {
        list-style-type: disc;
    }
    .list-inside {
        list-style-position: inside;
    }

    /* Colors & Backgrounds */
    .bg-white {
        background-color: #fff;
    }
    .bg-gray-50 {
        background-color: #f9fafb;
    }
    .bg-green-500 {
        background-color: #22c55e;
    }
    .hover\:bg-green-700:hover {
        background-color: #15803d;
    }
    .bg-red-100 {
        background-color: #fee2e2;
    }
    .bg-blue-600 {
        background-color: #2563eb;
    }
    .hover\:bg-blue-100:hover {
        background-color: #dbeafe;
    }
    .file\:bg-blue-50 {
        background-color: #eff6ff;
    }
    .hover\:file\:bg-blue-100:hover {
        background-color: #dbeafe;
    }

    /* Borders & Shadows */
    .border {
        border-width: 1px;
    }
    .border-gray-300 {
        border-color: #d1d5db;
    }
    .border-red-400 {
        border-color: #f87171;
    }
    .shadow-inner {
        box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
    }

    /* Form Elements */
    input[type="text"],
    input[type="email"],
    input[type="number"],
    input[type="date"],
    select,
    textarea {
        display: block;
        width: 100%;
        padding: 0.75rem 1rem; /* Increased padding for a modern look */
        margin-top: 0.5rem; /* More space between label and input */
        border: 1px solid #e5e7eb; /* Lighter border */
        border-radius: 0.5rem; /* More rounded corners */
        background-color: #f9fafb; /* Light background for fields */
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05); /* Subtle inner shadow */
        font-size: 0.9375rem; /* Slightly larger font */
        color: #374151;
        transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out, background-color 0.2s ease-in-out;
    }
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="number"]:focus,
    input[type="date"]:focus,
    select:focus,
    textarea:focus {
        border-color: #3b82f6; /* Blue focus border */
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25); /* Blue focus ring */
        background-color: #fff; /* White background on focus */
    }
    label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600; /* Slightly bolder labels */
        color: #374151;
        margin-bottom: 0.25rem;
    }
    .grid.gap-6 > div {
        margin-bottom: 1rem; /* Add vertical spacing between grid items */
    }
    .grid.gap-6 {
        gap: 1.5rem; /* Increase overall grid gap */
    }
    .file\:mr-4 {
        margin-right: 1rem;
    }
    .file\:py-2 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }
    .file\:px-4 {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    .file\:rounded-full {
        border-radius: 9999px;
    }
    .file\:border-0 {
        border-width: 0;
    }
    .file\:text-sm {
        font-size: 0.875rem;
    }
    .file\:font-semibold {
        font-weight: 600;
    }
    .file\:bg-blue-50 {
        background-color: #eff6ff;
    }
    .file\:text-blue-700 {
        color: #1d4ed8;
    }
    .hover\:file\:bg-blue-100:hover {
        background-color: #dbeafe;
    }
    .h-4 {
        height: 1rem;
    }
    .w-4 {
        width: 1rem;
    }
    .ml-2 {
        margin-left: 0.5rem;
    }
    .mt-1 {
        margin-top: 0.25rem;
    }
    .border-red-500 {
        border-color: #ef4444;
    }

    /* Buttons */
    button, .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.625rem 1.25rem; /* Larger padding for buttons */
        border-radius: 0.5rem; /* More rounded buttons */
        font-weight: 600;
        font-size: 0.9375rem; /* Slightly larger font */
        cursor: pointer;
        transition: background-color 0.2s ease-in-out, transform 0.1s ease-out; /* Added transform for subtle animation */
        box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Subtle shadow for buttons */
    }
    .btn:hover {
        transform: translateY(-1px); /* Lift effect on hover */
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    .bg-green-500 {
        background-color: #22c55e;
        color: #fff;
        border: none;
    }
    .bg-green-500:hover {
        background-color: #16a34a;
    }

    /* Tab Navigation */
    .tab-link {
        display: block;
        padding: 0.75rem 1rem; /* Increased padding */
        border-radius: 0.375rem;
        font-weight: 500;
        color: #4b5563; /* Default text color */
        text-decoration: none;
        transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
    }
    .tab-link:hover {
        background-color: #e0f2fe; /* Light blue hover */
        color: #1e40af; /* Darker blue text on hover */
    }
    .tab-link[data-active="true"] {
        background-color: #3b82f6; /* Active tab background */
        color: #fff; /* Active tab text color */
        font-weight: 600; /* Bolder active tab */
        box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Subtle shadow for active tab */
    }
    .tab-content {
        display: none;
        padding: 1.5rem; /* Padding inside tab content */
        border: 1px solid #e5e7eb; /* Border around content */
        border-radius: 0.5rem;
        background-color: #fff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .tab-content.active {
        display: block;
    }
</style>

<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Create New Onboarding Record</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
            <strong class="font-bold">Whoops!</strong>
            <span class="block sm:inline">There were some problems with your input.</span>
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="onboardingForm" action="{{ route('onboarding.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-8 flex flex-col md:flex-row gap-8">
        @csrf
        <!-- Left Column: Navigation Tabs -->
        <div class="w-full md:w-1/4 bg-gray-50 p-4 rounded-lg shadow-inner">
            <nav class="space-y-2">
                <a href="#personal-details" class="tab-link" data-tab="step-1">Personal Details</a>
                <a href="#address-details" class="tab-link" data-tab="step-2">Address Details</a>
                <a href="#educational-details" class="tab-link" data-tab="step-3">Educational Details</a>
                <a href="#previous-employment" class="tab-link" data-tab="step-4">Previous Employment</a>
                <a href="#job-details" class="tab-link" data-tab="step-5">Job Details</a>
                <a href="#bank-details" class="tab-link" data-tab="step-6">Bank Details</a>
                <a href="#health-insurance" class="tab-link" data-tab="step-7">Health & Insurance</a>
                <a href="#document-uploads" class="tab-link" data-tab="step-8">Document Uploads</a>
                <a href="#declaration" class="tab-link" data-tab="step-9">Declaration</a>
            </nav>
        </div>

        <!-- Right Column: Form Content -->
        <div class="w-full md:w-3/4">
            <!-- Step 1: Personal Details -->
            <div id="step-1" class="tab-content active">
                <h2 class="text-xl font-semibold mb-4">1. Personal Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="full_name">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}">
                    </div>
                    <div>
                        <label for="date_of_birth">Date of Birth <span class="text-red-500">*</span></label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}">
                    </div>
                    <div>
                        <label for="gender">Gender <span class="text-red-500">*</span></label>
                        <select name="gender" id="gender">
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="blood_group">Blood Group</label>
                        <input type="text" name="blood_group" id="blood_group" value="{{ old('blood_group') }}">
                    </div>
                    <div>
                        <label for="nationality">Nationality</label>
                        <input type="text" name="nationality" id="nationality" value="{{ old('nationality') }}">
                    </div>
                    <div>
                        <label for="marital_status">Marital Status</label>
                        <select name="marital_status" id="marital_status">
                            <option value="">Select Status</option>
                            <option value="Single" {{ old('marital_status') == 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ old('marital_status') == 'Married' ? 'selected' : '' }}>Married</option>
                            <option value="Divorced" {{ old('marital_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                            <option value="Widowed" {{ old('marital_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                        </select>
                    </div>
                    <div>
                        <label for="mobile_number">Contact Number (Mobile) <span class="text-red-500">*</span></label>
                        <input type="text" name="mobile_number" id="mobile_number" value="{{ old('mobile_number') }}">
                    </div>
                    <div>
                        <label for="alternate_contact_number">Alternate Contact Number</label>
                        <input type="text" name="alternate_contact_number" id="alternate_contact_number" value="{{ old('alternate_contact_number') }}">
                    </div>
                    <div>
                        <label for="personal_email_id">Personal Email ID <span class="text-red-500">*</span></label>
                        <input type="email" name="personal_email_id" id="personal_email_id" value="{{ old('personal_email_id') }}">
                    </div>
                    <div>
                        <label for="aadhaar_number">Aadhaar Number</label>
                        <input type="text" name="aadhaar_number" id="aadhaar_number" value="{{ old('aadhaar_number') }}">
                    </div>
                    <div>
                        <label for="pan_number">PAN Number</label>
                        <input type="text" name="pan_number" id="pan_number" value="{{ old('pan_number') }}">
                    </div>
                    <div>
                        <label for="passport_number">Passport Number (if applicable)</label>
                        <input type="text" name="passport_number" id="passport_number" value="{{ old('passport_number') }}">
                    </div>
                    <div>
                        <label for="father_mother_name">Father's / Mother's Name</label>
                        <input type="text" name="father_mother_name" id="father_mother_name" value="{{ old('father_mother_name') }}">
                    </div>
                    <div>
                        <label for="emergency_contact_name">Emergency Contact Name <span class="text-red-500">*</span></label>
                        <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name') }}">
                    </div>
                    <div>
                        <label for="emergency_contact_number">Emergency Contact Number <span class="text-red-500">*</span></label>
                        <input type="text" name="emergency_contact_number" id="emergency_contact_number" value="{{ old('emergency_contact_number') }}">
                    </div>
                    <div>
                        <label for="emergency_contact_relationship">Relationship with Emergency Contact</label>
                        <input type="text" name="emergency_contact_relationship" id="emergency_contact_relationship" value="{{ old('emergency_contact_relationship') }}">
                    </div>
                </div>
            </div>

            <!-- Step 2: Address Details -->
            <div id="step-2" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-4">2. Address Details</h2>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="current_address">Current Address <span class="text-red-500">*</span></label>
                        <textarea name="current_address" id="current_address" rows="3">{{ old('current_address') }}</textarea>
                    </div>
                    <div>
                        <label for="permanent_address">Permanent Address <span class="text-red-500">*</span></label>
                        <textarea name="permanent_address" id="permanent_address" rows="3">{{ old('permanent_address') }}</textarea>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_current_permanent" id="is_current_permanent" value="1" {{ old('is_current_permanent') ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                        <label for="is_current_permanent" class="ml-2 block text-sm text-gray-900">Is Current Address same as Permanent Address?</label>
                    </div>
                </div>
            </div>

            <!-- Step 3: Educational Details -->
            <div id="step-3" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-4">3. Educational Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="highest_qualification">Highest Qualification</label>
                        <input type="text" name="highest_qualification" id="highest_qualification" value="{{ old('highest_qualification') }}">
                    </div>
                    <div>
                        <label for="stream_specialization">Stream / Specialization</label>
                        <input type="text" name="stream_specialization" id="stream_specialization" value="{{ old('stream_specialization') }}">
                    </div>
                    <div>
                        <label for="institution_name">Institution Name</label>
                        <input type="text" name="institution_name" id="institution_name" value="{{ old('institution_name') }}">
                    </div>
                    <div>
                        <label for="year_of_passing">Year of Passing</label>
                        <input type="number" name="year_of_passing" id="year_of_passing" value="{{ old('year_of_passing') }}">
                    </div>
                    <div>
                        <label for="cgpa_percentage">CGPA / Percentage</label>
                        <input type="text" name="cgpa_percentage" id="cgpa_percentage" value="{{ old('cgpa_percentage') }}">
                    </div>
                    <div>
                        <label for="tenth_twelfth_marks_boards_year">10th & 12th Marks / Boards / Year</label>
                        <input type="text" name="tenth_twelfth_marks_boards_year" id="tenth_twelfth_marks_boards_year" value="{{ old('tenth_twelfth_marks_boards_year') }}">
                    </div>
                    <div class="md:col-span-2">
                        <label for="certifications">Certifications (if any)</label>
                        <textarea name="certifications" id="certifications" rows="3">{{ old('certifications') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Step 4: Previous Employment Details -->
            <div id="step-4" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-4">4. Previous Employment Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="previous_company_name">Previous Company Name</label>
                        <input type="text" name="previous_company_name" id="previous_company_name" value="{{ old('previous_company_name') }}">
                    </div>
                    <div>
                        <label for="last_drawn_salary">Last Drawn Salary</label>
                        <input type="number" name="last_drawn_salary" id="last_drawn_salary" value="{{ old('last_drawn_salary') }}">
                    </div>
                    <div class="md:col-span-2">
                        <label for="reason_for_leaving">Reason for Leaving</label>
                        <textarea name="reason_for_leaving" id="reason_for_leaving" rows="3">{{ old('reason_for_leaving') }}</textarea>
                    </div>
                    <div>
                        <label for="experience_letter">Experience Letter (upload)</label>
                        <input type="file" name="experience_letter" id="experience_letter" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div>
                        <label for="relieving_letter">Relieving Letter (upload)</label>
                        <input type="file" name="relieving_letter" id="relieving_letter" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>
            </div>

            <!-- Step 5: Job/Employment Details (For Current Role) -->
            <div id="step-5" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-4">5. Job/Employment Details (For Current Role)</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="date_of_joining">Date of Joining <span class="text-red-500">*</span></label>
                        <input type="date" name="date_of_joining" id="date_of_joining" value="{{ old('date_of_joining') }}">
                    </div>
                   
                    <div>
                        <label for="department">Department</label>
                        <input type="text" name="department" id="department" value="{{ old('department') }}">
                    </div>
                    <div>
                        <label for="designation">Designation</label>
                        <input type="text" name="designation" id="designation" value="{{ old('designation') }}">
                    </div>
                    <div>
                        <label for="reporting_manager">Reporting Manager</label>
                        <input type="text" name="reporting_manager" id="reporting_manager" value="{{ old('reporting_manager') }}">
                    </div>
                    <div>
                        <label for="work_location">Work Location</label>
                        <input type="text" name="work_location" id="work_location" value="{{ old('work_location') }}">
                    </div>
                    <div>
                        <label for="shift_timing">Shift Timing</label>
                        <input type="text" name="shift_timing" id="shift_timing" value="{{ old('shift_timing') }}">
                    </div>
                    <div>
                        <label for="employment_type">Type of Employment</label>
                        <select name="employment_type" id="employment_type">
                            <option value="">Select Type</option>
                            <option value="Full-time" {{ old('employment_type') == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                            <option value="Intern" {{ old('employment_type') == 'Intern' ? 'selected' : '' }}>Intern</option>
                            <option value="Contract" {{ old('employment_type') == 'Contract' ? 'selected' : '' }}>Contract</option>
                        </select>
                    </div>
                    <div>
                        <label for="probation_period_months">Probation Period (in months)</label>
                        <input type="number" name="probation_period_months" id="probation_period_months" value="{{ old('probation_period_months') }}">
                    </div>
                </div>
            </div>

            <!-- Step 6: Bank Details -->
            <div id="step-6" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-4">6. Bank Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="bank_name">Bank Name</label>
                        <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name') }}">
                    </div>
                    <div>
                        <label for="branch">Branch</label>
                        <input type="text" name="branch" id="branch" value="{{ old('branch') }}">
                    </div>
                    <div>
                        <label for="account_holder_name">Account Holder Name</label>
                        <input type="text" name="account_holder_name" id="account_holder_name" value="{{ old('account_holder_name') }}">
                    </div>
                    <div>
                        <label for="account_number">Account Number</label>
                        <input type="text" name="account_number" id="account_number" value="{{ old('account_number') }}">
                    </div>
                    <div>
                        <label for="ifsc_code">IFSC Code</label>
                        <input type="text" name="ifsc_code" id="ifsc_code" value="{{ old('ifsc_code') }}">
                    </div>
                    <div>
                        <label for="cancelled_cheque">Cancelled Cheque (upload)</label>
                        <input type="file" name="cancelled_cheque" id="cancelled_cheque" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>
            </div>

            <!-- Step 7: Health & Insurance Details -->
            <div id="step-7" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-4">7. Health & Insurance Details</h2>
                <div class="grid grid-cols-1 gap-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="has_medical_conditions" id="has_medical_conditions" value="1" {{ old('has_medical_conditions') ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                        <label for="has_medical_conditions" class="ml-2 block text-sm text-gray-900">Do you have any existing medical conditions?</label>
                    </div>
                    <div>
                        <label for="medical_history">Medical History (if yes)</label>
                        <textarea name="medical_history" id="medical_history" rows="3">{{ old('medical_history') }}</textarea>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_covered_by_insurance" id="is_covered_by_insurance" value="1" {{ old('is_covered_by_insurance') ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                        <label for="is_covered_by_insurance" class="ml-2 block text-sm text-gray-900">Are you covered under any insurance?</label>
                    </div>
                    <div>
                        <label for="nominee_name">Nominee Name</label>
                        <input type="text" name="nominee_name" id="nominee_name" value="{{ old('nominee_name') }}">
                    </div>
                    <div>
                        <label for="nominee_relationship">Nominee Relationship</label>
                        <input type="text" name="nominee_relationship" id="nominee_relationship" value="{{ old('nominee_relationship') }}">
                    </div>
                </div>
            </div>

            <!-- Step 8: Document Uploads -->
            <div id="step-8" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-4">8. Document Uploads</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="resume">Resume</label>
                        <input type="file" name="resume" id="resume" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div>
                        <label for="passport_photo">Passport-size Photo</label>
                        <input type="file" name="passport_photo" id="passport_photo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div>
                        <label for="aadhaar_card_front">Aadhaar Card (Front)</label>
                        <input type="file" name="aadhaar_card_front" id="aadhaar_card_front" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div>
                        <label for="aadhaar_card_back">Aadhaar Card (Back)</label>
                        <input type="file" name="aadhaar_card_back" id="aadhaar_card_back" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div>
                        <label for="pan_card">PAN Card</label>
                        <input type="file" name="pan_card" id="pan_card" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div>
                        <label for="educational_certificates">Educational Certificates</label>
                        <input type="file" name="educational_certificates" id="educational_certificates" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div>
                        <label for="experience_certificates">Experience Certificates</label>
                        <input type="file" name="experience_certificates" id="experience_certificates" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div>
                        <label for="address_proof">Address Proof (Electricity Bill / Rental Agreement)</label>
                        <input type="file" name="address_proof" id="address_proof" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div>
                        <label for="signed_offer_letter">Signed Offer Letter / Appointment Letter</label>
                        <input type="file" name="signed_offer_letter" id="signed_offer_letter" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div>
                        <label for="covid_vaccination_certificate">COVID Vaccination Certificate (if required)</label>
                        <input type="file" name="covid_vaccination_certificate" id="covid_vaccination_certificate" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>
            </div>

            <!-- Step 9: Declaration & Signature -->
            <div id="step-9" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-4">9. Declaration & Signature</h2>
                <div class="grid grid-cols-1 gap-6">
                    <div class="flex items-start">
                        <input type="checkbox" name="declaration_agreed" id="declaration_agreed" value="1" {{ old('declaration_agreed') ? 'checked' : '' }} class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        <label for="declaration_agreed" class="ml-2 block text-sm text-gray-900">I confirm all the above information is true and accurate to the best of my knowledge. <span class="text-red-500">*</span></label>
                    </div>
                    <div>
                        <label for="declaration_place">Place</label>
                        <input type="text" name="declaration_place" id="declaration_place" value="{{ old('declaration_place') }}">
                    </div>
                    <div>
                        <label for="declaration_date">Date</label>
                        <input type="date" name="declaration_date" id="declaration_date" value="{{ old('declaration_date') }}">
                    </div>
                    <div>
                        <label for="signature">Signature (Upload)</label>
                        <input type="file" name="signature" id="signature" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>
            </div>

            <div class="flex justify-content-end mt-4 mb-2">
                <button type="submit" class="btn bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Create Record</button>
            </div>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const tabLinks = $('.tab-link');
        const tabContents = $('.tab-content');

        function showTab(tabId) {
            tabContents.removeClass('active').addClass('hidden');
            tabLinks.attr('data-active', 'false').removeClass('bg-blue-600 text-white').addClass('text-gray-700 hover:bg-blue-100 hover:text-blue-700');

            $('#' + tabId).removeClass('hidden').addClass('active');
            $(`[data-tab="${tabId}"]`).attr('data-active', 'true').removeClass('text-gray-700 hover:bg-blue-100 hover:text-blue-700').addClass('bg-blue-600 text-white');
        }

        tabLinks.on('click', function(e) {
            e.preventDefault();
            const tabId = $(this).data('tab');
            showTab(tabId);
        });

        // Initialize form with the first tab
        showTab('step-1');

        // Handle "Is Current Address same as Permanent Address?" checkbox
        $('#is_current_permanent').on('change', function() {
            if ($(this).is(':checked')) {
                $('#permanent_address').val($('#current_address').val()).prop('readonly', true);
            } else {
                $('#permanent_address').val('').prop('readonly', false);
            }
        });

        // Update permanent address if current address changes while checkbox is checked
        $('#current_address').on('input', function() {
            if ($('#is_current_permanent').is(':checked')) {
                $('#permanent_address').val($(this).val());
            }
        });

        // Basic form validation before submission (client-side)
        $('#onboardingForm').on('submit', function(e) {
            let isValid = true;
            // Check all required fields across all tabs
            $(this).find('input[required], select[required], textarea[required], input[type="email"]').each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).addClass('border-red-500'); // Highlight empty required fields
                } else {
                    $(this).removeClass('border-red-500');
                }
            });

            // Specific validation for email format
            $(this).find('input[type="email"]').each(function() {
                if ($(this).val() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test($(this).val())) {
                    isValid = false;
                    $(this).addClass('border-red-500');
                    // Optionally, show a more specific error message next to the field
                } else {
                    $(this).removeClass('border-red-500');
                }
            });

            // Declaration checkbox validation for the last step
            if (!$('#declaration_agreed').is(':checked')) {
                isValid = false;
                // Optionally, highlight the checkbox or show an alert
            }

            if (!isValid) {
                e.preventDefault(); // Prevent form submission
                alert('Please fill in all required fields and correct any errors.');
                // Find the first invalid field and switch to its tab
                const firstInvalidField = $(this).find('.border-red-500').first();
                if (firstInvalidField.length) {
                    const parentTab = firstInvalidField.closest('.tab-content');
                    if (parentTab.length) {
                        showTab(parentTab.attr('id'));
                    }
                }
            }
        });
    });
</script>
@endsection
