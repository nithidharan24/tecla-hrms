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
    .p-8 {
        padding: 2rem;
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
    .text-blue-600 {
        color: #2563eb;
    }
    .hover\:underline:hover {
        text-decoration-line: underline;
    }
    /* Colors & Backgrounds */
    .bg-white {
        background-color: #fff;
    }
    .bg-blue-500 {
        background-color: #3b82f6;
    }
    .hover\:bg-blue-600:hover {
        background-color: #2563eb;
    }
    .text-white {
        color: #fff;
    }
    /* Borders & Shadows */
    .border {
        border-width: 1px;
    }
    .border-b {
        border-bottom-width: 1px;
    }
    .border-gray-300 {
        border-color: #d1d5db;
    }
    .border-gray-200 {
        border-color: #e5e7eb;
    }
    /* Specific for show page */
    .detail-section {
        margin-bottom: 2rem;
    }
    .detail-section-title {
        font-size: 1.25rem; /* text-xl */
        font-weight: 600; /* font-semibold */
        color: #1f2937; /* text-gray-800 */
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb; /* Lighter, more modern separator */
    }
    .detail-item {
        margin-bottom: 1rem; /* Space between items */
    }
    .detail-label {
        font-weight: 600; /* font-semibold */
        color: #4b5563; /* text-gray-600 */
        display: block;
        margin-bottom: 0.25rem;
        font-size: 0.9375rem; /* Slightly larger than text-sm */
    }
    .detail-value {
        color: #374151; /* text-gray-700 */
        padding-bottom: 0.5rem;
        font-size: 1rem; /* Base font size */
        line-height: 1.5;
        /* Removed border-bottom to match screenshot's clean look */
    }
    .image-preview {
        max-width: 200px;
        height: auto;
        border: 1px solid #e5e7eb;
        border-radius: 0.25rem;
        padding: 0.5rem;
        margin-top: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
</style>

<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Onboarding Details: {{ $onboarding->full_name }}</h1>
        <a href="{{ route('onboarding.index') }}" class="btn bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md flex items-center gap-2">
        
            
            Back to Records
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-8">
        <!-- 1. Personal Details -->
        <div class="detail-section">
            <h2 class="detail-section-title">1. Personal Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div class="detail-item">
                    <span class="detail-label">Full Name:</span>
                    <span class="detail-value">{{ $onboarding->full_name }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Date of Birth:</span>
                    <span class="detail-value">{{ $onboarding->date_of_birth ? $onboarding->date_of_birth->format('Y-m-d') : 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Gender:</span>
                    <span class="detail-value">{{ $onboarding->gender }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Blood Group:</span>
                    <span class="detail-value">{{ $onboarding->blood_group ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Nationality:</span>
                    <span class="detail-value">{{ $onboarding->nationality ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Marital Status:</span>
                    <span class="detail-value">{{ $onboarding->marital_status ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Mobile Number:</span>
                    <span class="detail-value">{{ $onboarding->mobile_number }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Alternate Contact Number:</span>
                    <span class="detail-value">{{ $onboarding->alternate_contact_number ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Personal Email ID:</span>
                    <span class="detail-value">{{ $onboarding->personal_email_id }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Aadhaar Number:</span>
                    <span class="detail-value">{{ $onboarding->aadhaar_number ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">PAN Number:</span>
                    <span class="detail-value">{{ $onboarding->pan_number ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Passport Number:</span>
                    <span class="detail-value">{{ $onboarding->passport_number ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Father's / Mother's Name:</span>
                    <span class="detail-value">{{ $onboarding->father_mother_name ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Emergency Contact Name:</span>
                    <span class="detail-value">{{ $onboarding->emergency_contact_name }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Emergency Contact Number:</span>
                    <span class="detail-value">{{ $onboarding->emergency_contact_number }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Emergency Contact Relationship:</span>
                    <span class="detail-value">{{ $onboarding->emergency_contact_relationship ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Address Details -->
        <div class="detail-section">
            <h2 class="detail-section-title">2. Address Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div class="detail-item md:col-span-2">
                    <span class="detail-label">Current Address:</span>
                    <span class="detail-value">{{ $onboarding->current_address }}</span>
                </div>
                <div class="detail-item md:col-span-2">
                    <span class="detail-label">Permanent Address:</span>
                    <span class="detail-value">{{ $onboarding->permanent_address }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Current same as Permanent:</span>
                    <span class="detail-value">{{ $onboarding->is_current_permanent ? 'Yes' : 'No' }}</span>
                </div>
            </div>
        </div>

        <!-- 3. Educational Details -->
        <div class="detail-section">
            <h2 class="detail-section-title">3. Educational Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div class="detail-item">
                    <span class="detail-label">Highest Qualification:</span>
                    <span class="detail-value">{{ $onboarding->highest_qualification ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Stream / Specialization:</span>
                    <span class="detail-value">{{ $onboarding->stream_specialization ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Institution Name:</span>
                    <span class="detail-value">{{ $onboarding->institution_name ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Year of Passing:</span>
                    <span class="detail-value">{{ $onboarding->year_of_passing ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">CGPA / Percentage:</span>
                    <span class="detail-value">{{ $onboarding->cgpa_percentage ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">10th & 12th Marks / Boards / Year:</span>
                    <span class="detail-value">{{ $onboarding->tenth_twelfth_marks_boards_year ?? 'N/A' }}</span>
                </div>
                <div class="detail-item md:col-span-2">
                    <span class="detail-label">Certifications:</span>
                    <span class="detail-value">{{ $onboarding->certifications ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- 4. Previous Employment Details -->
        <div class="detail-section">
            <h2 class="detail-section-title">4. Previous Employment Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div class="detail-item">
                    <span class="detail-label">Previous Company Name:</span>
                    <span class="detail-value">{{ $onboarding->previous_company_name ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Last Drawn Salary:</span>
                    <span class="detail-value">{{ $onboarding->last_drawn_salary ? '₹' . number_format($onboarding->last_drawn_salary, 2) : 'N/A' }}</span>
                </div>
                <div class="detail-item md:col-span-2">
                    <span class="detail-label">Reason for Leaving:</span>
                    <span class="detail-value">{{ $onboarding->reason_for_leaving ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Experience Letter:</span>
                    <span class="detail-value">
                        @if($onboarding->experience_letter_path)
                            <a href="{{ asset($onboarding->experience_letter_path) }}" target="_blank" class="text-blue-600 hover:underline">View File</a>
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Relieving Letter:</span>
                    <span class="detail-value">
                        @if($onboarding->relieving_letter_path)
                            <a href="{{ asset($onboarding->relieving_letter_path) }}" target="_blank" class="text-blue-600 hover:underline">View File</a>
                        @else
                            N/A
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- 5. Job/Employment Details (For Current Role) -->
        <div class="detail-section">
            <h2 class="detail-section-title">5. Job/Employment Details (For Current Role)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div class="detail-item">
                    <span class="detail-label">Date of Joining:</span>
                    <span class="detail-value">{{ $onboarding->date_of_joining ? $onboarding->date_of_joining->format('Y-m-d') : 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Employee Code:</span>
                    <span class="detail-value">{{ $onboarding->employee_code ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Department:</span>
                    <span class="detail-value">{{ $onboarding->department ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Designation:</span>
                    <span class="detail-value">{{ $onboarding->designation ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Reporting Manager:</span>
                    <span class="detail-value">{{ $onboarding->reporting_manager ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Work Location:</span>
                    <span class="detail-value">{{ $onboarding->work_location ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Shift Timing:</span>
                    <span class="detail-value">{{ $onboarding->shift_timing ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Employment Type:</span>
                    <span class="detail-value">{{ $onboarding->employment_type ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Probation Period (months):</span>
                    <span class="detail-value">{{ $onboarding->probation_period_months ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- 6. Bank Details -->
        <div class="detail-section">
            <h2 class="detail-section-title">6. Bank Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div class="detail-item">
                    <span class="detail-label">Bank Name:</span>
                    <span class="detail-value">{{ $onboarding->bank_name ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Branch:</span>
                    <span class="detail-value">{{ $onboarding->branch ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Account Holder Name:</span>
                    <span class="detail-value">{{ $onboarding->account_holder_name ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Account Number:</span>
                    <span class="detail-value">{{ $onboarding->account_number ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">IFSC Code:</span>
                    <span class="detail-value">{{ $onboarding->ifsc_code ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Cancelled Cheque:</span>
                    <span class="detail-value">
                        @if($onboarding->cancelled_cheque_path)
                            <a href="{{ asset($onboarding->cancelled_cheque_path) }}" target="_blank" class="text-blue-600 hover:underline">View File</a>
                        @else
                            N/A
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- 7. Health & Insurance Details -->
        <div class="detail-section">
            <h2 class="detail-section-title">7. Health & Insurance Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div class="detail-item">
                    <span class="detail-label">Has Medical Conditions:</span>
                    <span class="detail-value">{{ $onboarding->has_medical_conditions ? 'Yes' : 'No' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Medical History:</span>
                    <span class="detail-value">{{ $onboarding->medical_history ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Covered by Insurance:</span>
                    <span class="detail-value">{{ $onboarding->is_covered_by_insurance ? 'Yes' : 'No' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Nominee Name:</span>
                    <span class="detail-value">{{ $onboarding->nominee_name ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Nominee Relationship:</span>
                    <span class="detail-value">{{ $onboarding->nominee_relationship ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- 8. Document Uploads -->
        <div class="detail-section">
            <h2 class="detail-section-title">8. Document Uploads</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div class="detail-item">
                    <span class="detail-label">Resume:</span>
                    <span class="detail-value">
                        @if($onboarding->resume_path)
                            <a href="{{ asset($onboarding->resume_path) }}" target="_blank" class="text-blue-600 hover:underline">View File</a>
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Passport-size Photo:</span>
                    <span class="detail-value">
                        @if($onboarding->passport_photo_path)
                            <img src="{{ asset($onboarding->passport_photo_path) }}" alt="Passport Photo" class="image-preview">
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Aadhaar Card (Front):</span>
                    <span class="detail-value">
                        @if($onboarding->aadhaar_card_front_path)
                            <a href="{{ asset($onboarding->aadhaar_card_front_path) }}" target="_blank" class="text-blue-600 hover:underline">View File</a>
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Aadhaar Card (Back):</span>
                    <span class="detail-value">
                        @if($onboarding->aadhaar_card_back_path)
                            <a href="{{ asset($onboarding->aadhaar_card_back_path) }}" target="_blank" class="text-blue-600 hover:underline">View File</a>
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">PAN Card:</span>
                    <span class="detail-value">
                        @if($onboarding->pan_card_path)
                            <a href="{{ asset($onboarding->pan_card_path) }}" target="_blank" class="text-blue-600 hover:underline">View File</a>
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Educational Certificates:</span>
                    <span class="detail-value">
                        @if($onboarding->educational_certificates_path)
                            <a href="{{ asset($onboarding->educational_certificates_path) }}" target="_blank" class="text-blue-600 hover:underline">View File</a>
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Experience Certificates:</span>
                    <span class="detail-value">
                        @if($onboarding->experience_certificates_path)
                            <a href="{{ asset($onboarding->experience_certificates_path) }}" target="_blank" class="text-blue-600 hover:underline">View File</a>
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Address Proof (Electricity Bill / Rental Agreement):</span>
                    <span class="detail-value">
                        @if($onboarding->address_proof_path)
                            <a href="{{ asset($onboarding->address_proof_path) }}" target="_blank" class="text-blue-600 hover:underline">View File</a>
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Signed Offer Letter / Appointment Letter:</span>
                    <span class="detail-value">
                        @if($onboarding->signed_offer_letter_path)
                            <a href="{{ asset($onboarding->signed_offer_letter_path) }}" target="_blank" class="text-blue-600 hover:underline">View File</a>
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">COVID Vaccination Certificate (if required):</span>
                    <span class="detail-value">
                        @if($onboarding->covid_vaccination_certificate_path)
                            <a href="{{ asset($onboarding->covid_vaccination_certificate_path) }}" target="_blank" class="text-blue-600 hover:underline">View File</a>
                        @else
                            N/A
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- 9. Declaration & Signature -->
        <div class="detail-section">
            <h2 class="detail-section-title">9. Declaration & Signature</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div class="detail-item">
                    <span class="detail-label">Declaration Agreed:</span>
                    <span class="detail-value">{{ $onboarding->declaration_agreed ? 'Yes' : 'No' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Place:</span>
                    <span class="detail-value">{{ $onboarding->declaration_place ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value">{{ $onboarding->declaration_date ? $onboarding->declaration_date->format('Y-m-d') : 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Signature:</span>
                    <span class="detail-value">
                        @if($onboarding->signature_path)
                            <img src="{{ asset($onboarding->signature_path) }}" alt="Signature" class="image-preview">
                        @else
                            N/A
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
