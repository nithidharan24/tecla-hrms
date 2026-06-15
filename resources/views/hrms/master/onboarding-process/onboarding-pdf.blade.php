<!DOCTYPE html>
<html>
<head>
    <title>Onboarding Details - {{ $onboarding->full_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            border: 1px solid #eee;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #0056b3;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 18px;
            margin-top: 25px;
        }
        .section-content {
            margin-bottom: 20px;
        }
        .field {
            margin-bottom: 10px;
        }
        .field label {
            font-weight: bold;
            display: inline-block;
            width: 180px; /* Adjust as needed for alignment */
            vertical-align: top;
        }
        .field span {
            display: inline-block;
            vertical-align: top;
            width: calc(100% - 190px); /* Adjust based on label width */
        }
        .file-link {
            color: #007bff;
            text-decoration: none;
        }
        .file-link:hover {
            text-decoration: underline;
        }
        .declaration {
            margin-top: 30px;
            border-top: 1px dashed #ccc;
            padding-top: 20px;
        }
        .signature-box {
            border: 1px solid #ccc;
            padding: 10px;
            min-height: 80px;
            text-align: center;
            margin-top: 10px;
        }
        .signature-img {
            max-width: 150px;
            max-height: 100px;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Employee Onboarding Details</h1>
        <h2>1. Personal Details</h2>
        <div class="section-content">
            <div class="field"><label>Full Name:</label><span>{{ $onboarding->full_name }}</span></div>
            <div class="field"><label>Date of Birth:</label><span>{{ $onboarding->date_of_birth->format('Y-m-d') }}</span></div>
            <div class="field"><label>Gender:</label><span>{{ $onboarding->gender }}</span></div>
            <div class="field"><label>Blood Group:</label><span>{{ $onboarding->blood_group ?? 'N/A' }}</span></div>
            <div class="field"><label>Nationality:</label><span>{{ $onboarding->nationality ?? 'N/A' }}</span></div>
            <div class="field"><label>Marital Status:</label><span>{{ $onboarding->marital_status ?? 'N/A' }}</span></div>
            <div class="field"><label>Mobile Number:</label><span>{{ $onboarding->mobile_number }}</span></div>
            <div class="field"><label>Alternate Contact:</label><span>{{ $onboarding->alternate_contact_number ?? 'N/A' }}</span></div>
            <div class="field"><label>Personal Email ID:</label><span>{{ $onboarding->personal_email_id }}</span></div>
            <div class="field"><label>Aadhaar Number:</label><span>{{ $onboarding->aadhaar_number ?? 'N/A' }}</span></div>
            <div class="field"><label>PAN Number:</label><span>{{ $onboarding->pan_number ?? 'N/A' }}</span></div>
            <div class="field"><label>Passport Number:</label><span>{{ $onboarding->passport_number ?? 'N/A' }}</span></div>
            <div class="field"><label>Father's / Mother's Name:</label><span>{{ $onboarding->father_mother_name ?? 'N/A' }}</span></div>
            <div class="field"><label>Emergency Contact Name:</label><span>{{ $onboarding->emergency_contact_name }}</span></div>
            <div class="field"><label>Emergency Contact Number:</label><span>{{ $onboarding->emergency_contact_number }}</span></div>
            <div class="field"><label>Emergency Contact Relationship:</label><span>{{ $onboarding->emergency_contact_relationship ?? 'N/A' }}</span></div>
        </div>
        <h2>2. Address Details</h2>
        <div class="section-content">
            <div class="field"><label>Current Address:</label><span>{{ $onboarding->current_address }}</span></div>
            <div class="field"><label>Permanent Address:</label><span>{{ $onboarding->permanent_address }}</span></div>
            <div class="field"><label>Current same as Permanent:</label><span>{{ $onboarding->is_current_permanent ? 'Yes' : 'No' }}</span></div>
        </div>
        <h2>3. Educational Details</h2>
        <div class="section-content">
            <div class="field"><label>Highest Qualification:</label><span>{{ $onboarding->highest_qualification ?? 'N/A' }}</span></div>
            <div class="field"><label>Stream / Specialization:</label><span>{{ $onboarding->stream_specialization ?? 'N/A' }}</span></div>
            <div class="field"><label>Institution Name:</label><span>{{ $onboarding->institution_name ?? 'N/A' }}</span></div>
            <div class="field"><label>Year of Passing:</label><span>{{ $onboarding->year_of_passing ?? 'N/A' }}</span></div>
            <div class="field"><label>CGPA / Percentage:</label><span>{{ $onboarding->cgpa_percentage ?? 'N/A' }}</span></div>
            <div class="field"><label>10th & 12th Marks:</label><span>{{ $onboarding->tenth_twelfth_marks_boards_year ?? 'N/A' }}</span></div>
            <div class="field"><label>Certifications:</label><span>{{ $onboarding->certifications ?? 'N/A' }}</span></div>
        </div>
        <h2>4. Previous Employment Details</h2>
        <div class="section-content">
            <div class="field"><label>Previous Company Name:</label><span>{{ $onboarding->previous_company_name ?? 'N/A' }}</span></div>
            <div class="field"><label>Last Drawn Salary:</label><span>{{ $onboarding->last_drawn_salary ? '₹' . number_format($onboarding->last_drawn_salary, 2) : 'N/A' }}</span></div>
            <div class="field"><label>Reason for Leaving:</label><span>{{ $onboarding->reason_for_leaving ?? 'N/A' }}</span></div>
            <div class="field"><label>Experience Letter:</label><span>@if($onboarding->experience_letter_path)File Available @else N/A @endif</span></div>
            <div class="field"><label>Relieving Letter:</label><span>@if($onboarding->relieving_letter_path)File Available @else N/A @endif</span></div>
        </div>
        <h2>5. Job/Employment Details (Current Role)</h2>
        <div class="section-content">
            <div class="field"><label>Date of Joining:</label><span>{{ $onboarding->date_of_joining->format('Y-m-d') }}</span></div>
            <div class="field"><label>Employee Code:</label><span>{{ $onboarding->employee_code ?? 'N/A' }}</span></div>
            <div class="field"><label>Department:</label><span>{{ $onboarding->department ?? 'N/A' }}</span></div>
            <div class="field"><label>Designation:</label><span>{{ $onboarding->designation ?? 'N/A' }}</span></div>
            <div class="field"><label>Reporting Manager:</label><span>{{ $onboarding->reporting_manager ?? 'N/A' }}</span></div>
            <div class="field"><label>Work Location:</label><span>{{ $onboarding->work_location ?? 'N/A' }}</span></div>
            <div class="field"><label>Shift Timing:</label><span>{{ $onboarding->shift_timing ?? 'N/A' }}</span></div>
            <div class="field"><label>Employment Type:</label><span>{{ $onboarding->employment_type ?? 'N/A' }}</span></div>
            <div class="field"><label>Probation Period (months):</label><span>{{ $onboarding->probation_period_months ?? 'N/A' }}</span></div>
        </div>
        <h2>6. Bank Details</h2>
        <div class="section-content">
            <div class="field"><label>Bank Name:</label><span>{{ $onboarding->bank_name ?? 'N/A' }}</span></div>
            <div class="field"><label>Branch:</label><span>{{ $onboarding->branch ?? 'N/A' }}</span></div>
            <div class="field"><label>Account Holder Name:</label><span>{{ $onboarding->account_holder_name ?? 'N/A' }}</span></div>
            <div class="field"><label>Account Number:</label><span>{{ $onboarding->account_number ?? 'N/A' }}</span></div>
            <div class="field"><label>IFSC Code:</label><span>{{ $onboarding->ifsc_code ?? 'N/A' }}</span></div>
            <div class="field"><label>Cancelled Cheque:</label><span>@if($onboarding->cancelled_cheque_path)File Available @else N/A @endif</span></div>
        </div>
        <h2>7. Health & Insurance Details</h2>
        <div class="section-content">
            <div class="field"><label>Has Medical Conditions:</label><span>{{ $onboarding->has_medical_conditions ? 'Yes' : 'No' }}</span></div>
            <div class="field"><label>Medical History:</label><span>{{ $onboarding->medical_history ?? 'N/A' }}</span></div>
            <div class="field"><label>Covered by Insurance:</label><span>{{ $onboarding->is_covered_by_insurance ? 'Yes' : 'No' }}</span></div>
            <div class="field"><label>Nominee Name:</label><span>{{ $onboarding->nominee_name ?? 'N/A' }}</span></div>
            <div class="field"><label>Nominee Relationship:</label><span>{{ $onboarding->nominee_relationship ?? 'N/A' }}</span></div>
        </div>
        <h2>8. Document Uploads</h2>
        <div class="section-content">
            <div class="field"><label>Resume:</label><span>@if($onboarding->resume_path)File Available @else N/A @endif</span></div>
            <div class="field"><label>Passport-size Photo:</label><span>@if($passportPhotoBase64)<img src="{{ $passportPhotoBase64 }}" alt="Passport Photo" class="signature-img">@else N/A @endif</span></div>
            <div class="field"><label>Aadhaar Card (Front):</label><span>@if($onboarding->aadhaar_card_front_path)File Available @else N/A @endif</span></div>
            <div class="field"><label>Aadhaar Card (Back):</label><span>@if($onboarding->aadhaar_card_back_path)File Available @else N/A @endif</span></div>
            <div class="field"><label>PAN Card:</label><span>@if($onboarding->pan_card_path)File Available @else N/A @endif</span></div>
            <div class="field"><label>Educational Certificates:</label><span>@if($onboarding->educational_certificates_path)File Available @else N/A @endif</span></div>
            <div class="field"><label>Experience Certificates:</label><span>@if($onboarding->experience_certificates_path)File Available @else N/A @endif</span></div>
            <div class="field"><label>Address Proof:</label><span>@if($onboarding->address_proof_path)File Available @else N/A @endif</span></div>
            <div class="field"><label>Signed Offer Letter:</label><span>@if($onboarding->signed_offer_letter_path)File Available @else N/A @endif</span></div>
            <div class="field"><label>COVID Vaccination Certificate:</label><span>@if($onboarding->covid_vaccination_certificate_path)File Available @else N/A @endif</span></div>
        </div>
        <h2>9. Declaration & Signature</h2>
        <div class="section-content declaration">
            <div class="field"><label>Declaration Agreed:</label><span>{{ $onboarding->declaration_agreed ? 'Yes' : 'No' }}</span></div>
            <div class="field"><label>Place:</label><span>{{ $onboarding->declaration_place ?? 'N/A' }}</span></div>
            <div class="field"><label>Date:</label><span>{{ $onboarding->declaration_date ? $onboarding->declaration_date->format('Y-m-d') : 'N/A' }}</span></div>
            <div class="field">
                <label>Signature:</label>
                @if($signatureBase64)
                    <div class="signature-box">
                        <img src="{{ $signatureBase64 }}" alt="Signature" class="signature-img">
                        <p class="text-xs text-gray-500 mt-1">Digital Signature</p>
                    </div>
                @else
                    <span>N/A</span>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
