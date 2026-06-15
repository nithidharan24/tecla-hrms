<?php

namespace App\Http\Controllers\Backend\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Mail\HikeMail; // Import the Mailable
use Carbon\Carbon; // Import Carbon for date parsing

class EmployeeProfileController extends Controller
{
    // Show the edit profile form with existing data
    public function edit($id)
    {
        // Fetch employee details from allemployees
        $employee = DB::table('allemployees')->where('id', $id)->first();
        // Check if employee exists
        if (!$employee) {
            return redirect()->back()->withErrors('Employee not found.');
        }

        // Fetch employee profile details from employee_profile_main
        $employeeProfile = DB::table('employee_profile_main')->where('employee_id', $id)->first();
        // If no profile found, create a default empty object with relevant keys
        if (!$employeeProfile) {
            $employeeProfile = (object) [
                'birth_date' => '',
                'gender' => '',
                'address' => '',
                'state' => '',
                'country' => '',
                'pin_code' => '',
            ];
        }

        // Fetch departments and designations for the select options
        $departments = DB::table('department')->get();
        $designations = DB::table('designation')->get();

        // Fetch employee bank statutory information for the 'Bank & Statutory' tab
        $bankStatutory = DB::table('employee_bank_statutory')->where('employee_id', $id)->first();
        if (!$bankStatutory) {
            $bankStatutory = (object) [
                'salary_basis' => 'Monthly',
                'salary_amount' => '0',
                'payment_type' => 'Bank transfer',
                'pf_contribution' => 'No',
                'pf_no' => '',
                'employee_pf_rate' => '0%',
                'additional_rate' => '0%',
                'total_rate' => '0%',
                'esi_contribution' => 'No',
                'esi_no' => '',
                'employee_esi_rate' => '0%',
                'esi_additional_rate' => '0%',
                'total_esi_rate' => '0%',
            ];
        }

        // Fetch data for other tabs in the main profile view (placeholders)
        // You need to adjust these queries to match your actual table names and relationships
 
        $familyMembers = DB::table('employee_family_informations')->where('employee_id', $id)->get();
        $educationInfos = DB::table('employee_education_informations')->where('employee_id', $id)->get();
        $experienceInfos = DB::table('employee_experience_informations')->where('employee_id', $id)->get();


        // Return view with compacted variables
        return view('hrms.Employee.AllEmployee.Profile.editmain', compact(
            'employee',
            'employeeProfile',
            'departments',
            'designations',
            'bankStatutory', // Pass bankStatutory data
           
            'familyMembers',
            'educationInfos',
            'experienceInfos',
           
        ));
    }

    public function update(Request $request, $id)
    {
        // Validate input data
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'department_id' => 'required|integer|exists:department,id',
            'designation_id' => 'required|integer|exists:designation,id',
            'phone_number' => 'required|string|max:20',
            'birth_date' => 'required|date_format:d-m-Y', // Validate the format 'DD-MM-YYYY'
            'gender' => 'required|string|in:male,female',
            'address' => 'required|string|max:255',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'pin_code' => 'required|string|max:10',
        ]);
        // Convert the birth_date to MySQL format (YYYY-MM-DD)
        $birth_date = Carbon::createFromFormat('d-m-Y', $request->birth_date)->format('Y-m-d');
        // Start transaction
        DB::transaction(function () use ($request, $id, $birth_date) {
            // Update the allemployees table
            DB::table('allemployees')->where('id', $id)->update([
                'firstname' => $request->first_name,
                'lastname' => $request->last_name,
                'department' => $request->department_id,
                'designation' => $request->designation_id,
                'phone' => $request->phone_number,
            ]);
            // Update the employee_profile_main table
            DB::table('employee_profile_main')->where('employee_id', $id)->update([
                'birthday' => $birth_date, // Use the converted date
                'gender' => $request->gender,
                'address' => $request->address,
                'state' => $request->state,
                'country' => $request->country,
                'pin_code' => $request->pin_code,
                'is_edited'     => 1, // Mark as edited
            ]);
        });
        return redirect()->route('employee.show', $id)
        ->with('success', 'Updated successfully!');
    }

    // Show the edit personal information form with existing data
    public function editPersonalInfo($id)
    {
        // Fetch employee profile details from employee_personal_informations
        $employeeProfile = DB::table('employee_personal_informations')->where('employee_id', $id)->first();
        // Check if profile exists
        if (!$employeeProfile) {
            return redirect()->back()->withErrors('Employee profile not found.');
        }
        // Fetch employee details from allemployees
        $employee = DB::table('allemployees')->where('id', $id)->first();
        return view('hrms.Employee.AllEmployee.Profile.edit_personal_info', compact('employee', 'employeeProfile'));
    }

    // Update personal information
    public function updatePersonalInfo(Request $request, $id)
    {
        // Validate input data
        $request->validate([
            'passport_no' => 'nullable|string|max:255',
            'passport_expiry' => 'nullable|date_format:d-m-Y', // Validate the format 'DD-MM-YYYY'
            'tel' => 'nullable|string|max:20',
            'nationality' => 'required|string|max:100',
            'religion' => 'nullable|string|max:100',
            'marital_status' => 'required|string|in:Single,Married',
            'spouse_employment' => 'nullable|string|max:255',
            'number_of_children' => 'nullable|integer|min:0',
        ]);
        // Convert the passport expiry date to MySQL format (YYYY-MM-DD)
        $passport_expiry = !empty($request->passport_expiry) ? Carbon::createFromFormat('d-m-Y', $request->passport_expiry)->format('Y-m-d') : null;
        // Start transaction
        DB::transaction(function () use ($request, $id, $passport_expiry) {
            // Update the employee_profile_main table
            DB::table('employee_personal_informations')->where('employee_id', $id)->update([
                'passport_no' => $request->passport_no,
                'passport_exp_date' => $passport_expiry, // Use the converted date
                'tel' => $request->tel,
                'nationality' => $request->nationality,
                'religion' => $request->religion,
                'marital_status' => $request->marital_status,
                'employment_of_spouse' => $request->spouse_employment,
                'no_of_children' => $request->number_of_children,
                'is_edited'     => 1, // Mark as edited
            ]);
        });
        return redirect()->route('employee.show', $id)
        ->with('success', 'Updated successfully!');
    }

    // Show the edit emergency contact form with existing data
    public function editEmergencyContact($id)
    {
        // Fetch employee emergency contact details from employee_emergency_contact
        $emergencyContact = DB::table('employee_emergency_contact')->where('employee_id', $id)->first();
        // Check if emergency contact exists
        if (!$emergencyContact) {
            return redirect()->back()->withErrors('Emergency contact not found.');
        }
        // Fetch employee details from allemployees
        $employee = DB::table('allemployees')->where('id', $id)->first();
        return view('hrms.Employee.AllEmployee.Profile.edit_emergency_contact', compact('employee', 'emergencyContact'));
    }

    // Update emergency contact information
    public function updateEmergencyContact(Request $request, $id)
    {
        // Validate input data
        $request->validate([
            'primary_name' => 'required|string|max:255',
            'relationship' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'secondary_name' => 'nullable|string|max:255',
            'secondary_relationship' => 'nullable|string|max:100',
            'secondary_phone' => 'nullable|string|max:20',
        ]);
        // Start transaction
        DB::transaction(function () use ($request, $id) {
            // Update the employee_emergency_contact table
            DB::table('employee_emergency_contact')->where('employee_id', $id)->update([
                'primary_name' => $request->primary_name,
                'relationship' => $request->relationship,
                'phone' => $request->phone,
                'secondary_name' => $request->secondary_name,
                'secondary_relationship' => $request->secondary_relationship,
                'secondary_phone' => $request->secondary_phone,
                'is_edited'     => 1, // Mark as edited
            ]);
        });
        return redirect()->route('employee.show', $id)
        ->with('success', 'Updated successfully!');
    
    }

    // Show the edit bank information form with existing data
    // NOTE: This method seems to handle 'employee_bank_informations' (bank name, account no, etc.)
    // The 'Bank & Statutory' tab (salary, PF, ESI) is handled by the 'edit' method and updateBankStatutory.
    public function editBankInfo($id)
    {
        // Fetch employee bank information from employee_bank_informations
        $bankInfo = DB::table('employee_bank_informations')->where('employee_id', $id)->first();
        // Check if bank information exists
        if (!$bankInfo) {
            // If no bank info found, create a default empty object with relevant keys
            $bankInfo = (object) [
                'bank_name' => '',
                'bank_account_no' => '',
                'ifsc_code' => '',
                'pan_no' => '',
            ];
        }
        // Fetch employee details from allemployees
        $employee = DB::table('allemployees')->where('id', $id)->first();
        return view('hrms.Employee.AllEmployee.Profile.edit_bank_info', compact('employee', 'bankInfo'));
    }

    // Update bank information
    public function updateBankInfo(Request $request, $id)
    {
        // Validate input data
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'bank_account_no' => 'required|string|max:20',
            'ifsc_code' => 'required|string|max:15',
            'pan_no' => 'required|string|max:15',
        ]);
        // Start transaction
        DB::transaction(function () use ($request, $id) {
            // Update the employee_bank_information table
            DB::table('employee_bank_informations')->where('employee_id', $id)->update([
                'bank_name' => $request->bank_name,
                'bank_account_no' => $request->bank_account_no,
                'ifsc_code' => $request->ifsc_code,
                'pan_no' => $request->pan_no,
                'is_edited'     => 1, // Mark as edited
            ]);
        });
        return redirect()->route('employee.show', $id)
        ->with('success', 'Updated successfully!');
    }

    public function updateBankStatutory(Request $request, $id)
    {
        // Validate input data
        $request->validate([
            'salary_basis' => 'required|string|max:255',
            'salary_amount' => 'required|numeric|min:0', // Ensure it's numeric
            'payment_type' => 'required|string|max:255',
            'pf_contribution' => 'required|string|in:Yes,No',
            'pf_no' => 'nullable|string|max:255',
            'employee_pf_rate' => 'nullable|string|max:10',
            'additional_rate' => 'nullable|string|max:10',
            'total_rate_pf' => 'nullable|string|max:10',
            'esi_contribution' => 'required|string|in:Yes,No',
            'esi_no' => 'nullable|string|max:255',
            'employee_esi_rate' => 'nullable|string|max:10',
            'esi_additional_rate' => 'nullable|string|max:10',
            'esi_total_rate' => 'nullable|string|max:10',
        ]);

        // Fetch current bank statutory info to compare salary
        $bankStatutory = DB::table('employee_bank_statutory')->where('employee_id', $id)->first();
        $oldSalaryAmount = $bankStatutory ? (float) $bankStatutory->salary_amount : null;
        $newSalaryAmount = (float) $request->input('salary_amount');

        // Fetch employee details for the email
        $employee = DB::table('allemployees')->where('id', $id)->first();

        DB::transaction(function () use ($request, $id, $oldSalaryAmount, $newSalaryAmount, $employee, $bankStatutory) {
            // Update or insert the bank statutory information for the employee
            DB::table('employee_bank_statutory')->updateOrInsert(
                ['employee_id' => $id], // Attributes to find the record
                [
                    'salary_basis' => $request->input('salary_basis'),
                    'salary_amount' => $newSalaryAmount, // Use the float value
                    'payment_type' => $request->input('payment_type'),
                    'pf_contribution' => $request->input('pf_contribution'),
                    'pf_no' => $request->input('pf_no'),
                    'employee_pf_rate' => $request->input('employee_pf_rate'),
                    'additional_rate' => $request->input('additional_rate'),
                    'total_rate' => $request->input('total_rate_pf'),
                    'esi_contribution' => $request->input('esi_contribution'),
                    'esi_no' => $request->input('esi_no'),
                    'employee_esi_rate' => $request->input('employee_esi_rate'),
                    'esi_additional_rate' => $request->input('esi_additional_rate'),
                    'total_esi_rate' => $request->input('esi_total_rate'),
                    'is_edited'     => 1, // Mark as edited
                    'updated_at' => now(),
                    'created_at' => $bankStatutory ? $bankStatutory->created_at : now(), // Preserve original created_at or set new
                ]
            );

            // Check if salary was updated (not just initially set) and if employee email exists
            if ($oldSalaryAmount !== null && $newSalaryAmount !== $oldSalaryAmount && $employee && $employee->email) {
                // Fetch the email template from the database
                $template = DB::table('email_templates')->where('name', 'salary_hike_notification')->first();

                // Default subject and body if template is not found in DB
                $defaultSubject = 'Regarding Your Salary Update, {employee_name}';
                $defaultBody = "Dear {employee_name},\n\nThis email confirms that your salary information has been updated.\n\nPrevious Salary: {old_salary}\nNew Salary: {new_salary}\n\nIf you have any questions, please contact HR.\n\nBest regards,\n[Your Company Name]";

                if ($template) {
                    $subject = str_replace(
                        ['{employee_name}'],
                        [$employee->firstname . ' ' . $employee->lastname],
                        $template->subject
                    );

                    $body = str_replace(
                        ['{employee_name}', '{old_salary}', '{new_salary}'],
                        [$employee->firstname . ' ' . $employee->lastname, number_format($oldSalaryAmount, 2), number_format($newSalaryAmount, 2)],
                        $template->body
                    );
                } else {
                    // Use default template if not found in DB
                    $subject = str_replace(
                        ['{employee_name}'],
                        [$employee->firstname . ' ' . $employee->lastname],
                        $defaultSubject
                    );

                    $body = str_replace(
                        ['{employee_name}', '{old_salary}', '{new_salary}'],
                        [$employee->firstname . ' ' . $employee->lastname, number_format($oldSalaryAmount, 2), number_format($newSalaryAmount, 2)],
                        $defaultBody
                    );
                }

                // Send email using the Mailable
                \Mail::to($employee->email)->send(new HikeMail($subject, $body));

                // For the "hike letter", you might generate a PDF here.
                // Example using a hypothetical PDF library (e.g., dompdf/barryvdh/laravel-dompdf):
                // \PDF::loadView('emails.hike_letter_pdf', compact('employee', 'oldSalaryAmount', 'newSalaryAmount'))
                //     ->save(storage_path('app/public/hike_letters/hike_letter_' . $employee->id . '_' . now()->format('YmdHis') . '.pdf'));
            }
        });

        return redirect()->back()->with('success', 'Bank statutory information updated successfully!');
    }

    /// Show the edit family information form with existing data
    public function editFamilyInfo($id)
    {
        // Fetch employee family information from employee_family_informations
        $familyInfo = DB::table('employee_family_informations')->where('employee_id', $id)->get();
        // Fetch employee details from allemployees
        $employee = DB::table('allemployees')->where('id', $id)->first();
        return view('hrms.Employee.AllEmployee.Profile.edit_family_info', compact('employee', 'familyInfo'));
    }

    // Update family information
    public function updateFamilyInfo(Request $request, $id)
    {
        // Validate input data
        $request->validate([
            'family_members' => 'required|array',
            'family_members.*.name' => 'required|string|max:255',
            'family_members.*.relationship' => 'required|string|max:100',
            'family_members.*.dob' => 'nullable|date_format:d-m-Y', // Validate the format 'DD-MM-YYYY'
            'family_members.*.phone' => 'nullable|string|max:20',
        ]);
        // Start transaction
        DB::transaction(function () use ($request, $id) {
            // Loop through each family member and insert/update their information
            foreach ($request->family_members as $member) {
                // Convert the date of birth to MySQL format (YYYY-MM-DD)
                $dob = !empty($member['dob']) ? Carbon::createFromFormat('d-m-Y', $member['dob'])->format('Y-m-d') : null;
                // Check if family member already exists, if so, update, otherwise insert new
                if (isset($member['id'])) {
                    DB::table('employee_family_informations')->where('id', $member['id'])->update([
                        'name' => $member['name'],
                        'relationship' => $member['relationship'],
                        'date_of_birth' => $dob,
                        'phone' => $member['phone'],
                        'is_edited'     => 1, // Mark as edited
                    ]);
                } else {
                    DB::table('employee_family_informations')->insert([
                        'employee_id' => $id,
                        'name' => $member['name'],
                        'relationship' => $member['relationship'],
                        'date_of_birth' => $dob,
                        'phone' => $member['phone'],
                        'is_edited'     => 1, // Mark as edited
                    ]);
                }
            }
        });
        return redirect()->route('employee.show', $id)
        ->with('success', 'Updated successfully!');
    }

    public function deleteFamilyMember($id)
    {
        // Find the family member by ID and delete
        DB::table('employee_family_informations')->where('id', $id)->delete();
        // Redirect back with success message
        return redirect()->back()->with('success', 'Family member deleted successfully.');
    }

    // Show the edit education information form with existing data
    public function editEducation($id)
    {
        // Fetch employee details from allemployees
        $employee = DB::table('allemployees')->where('id', $id)->first();
        // Fetch education information for the employee
        $educationInfos = DB::table('employee_education_informations')->where('employee_id', $id)->get();
        // Check if employee exists
        if (!$employee) {
            return redirect()->back()->withErrors('Employee not found.');
        }
        return view('hrms.Employee.AllEmployee.Profile.edit_education_info', compact('employee', 'educationInfos'));
    }

    // Update education information
    public function updateEducation(Request $request, $id)
    {
        // Start transaction
        DB::transaction(function () use ($request, $id) {
            // Clear existing records before inserting updated ones
            DB::table('employee_education_informations')->where('employee_id', $id)->delete();
            // Insert updated education information
            foreach ($request->education as $edu) {
                DB::table('employee_education_informations')->insert([
                    'employee_id' => $id,
                    'institution' => $edu['institution'],
                    'subject' => $edu['subject'],
                    'start_date' => $edu['start_date'],
                    'end_date' => $edu['end_date'],
                    'degree' => $edu['degree'],
                    'grade' => $edu['grade'],
                    'is_edited'     => 1, // Mark as edited
                ]);
            }
        });
        return redirect()->route('employee.show', $id)
        ->with('success', 'Updated successfully!');
    }

    public function destroy($id)
    {
        // Find the education record and delete it
        $deleted = DB::table('employee_education_informations')->where('id', $id)->delete();
        if ($deleted) {
            return response()->json(['message' => 'Education entry deleted successfully.']);
        } else {
            return response()->json(['message' => 'Education entry not found.'], 404);
        }
    }

    public function editExperience($id)
    {
        // Fetch employee experience details
        $experienceInfo = DB::table('employee_experience_informations')->where('employee_id', $id)->get();
        // Fetch employee details from allemployees
        $employee = DB::table('allemployees')->where('id', $id)->first();
        return view('hrms.Employee.AllEmployee.Profile.edit_exp_info', compact('employee', 'experienceInfo'));
    }

    public function updateExperience(Request $request, $id)
    {
        $request->validate([
            'experience' => 'required|array',
            'experience.*.company_name' => 'required|string|max:255',
            'experience.*.location' => 'required|string|max:100',
            'experience.*.job_position' => 'required|string|max:100',
            'experience.*.period_from' => 'required|date',
            'experience.*.period_to'   => 'required|date|after_or_equal:experience.*.period_from',
        ]);
    
        DB::transaction(function () use ($request, $id) {
            $incomingIds = [];
    
            foreach ($request->experience as $exp) {
                $periodFrom = $exp['period_from'] ?: null;
                $periodTo   = $exp['period_to'] ?: null;
    
                if (!empty($exp['id'])) {
                    DB::table('employee_experience_informations')
                        ->where('id', $exp['id'])
                        ->where('employee_id', $id)
                        ->update([
                            'company_name' => $exp['company_name'],
                            'location'     => $exp['location'],
                            'position'     => $exp['job_position'],
                            'period_from'  => $periodFrom,
                            'period_to'    => $periodTo,
                            'is_edited'     => 1, // Mark as edited
                            'updated_at'   => now(),
                        ]);
                    $incomingIds[] = $exp['id'];
                } else {
                    $newId = DB::table('employee_experience_informations')->insertGetId([
                        'employee_id'  => $id,
                        'company_name' => $exp['company_name'],
                        'location'     => $exp['location'],
                        'position'     => $exp['job_position'],
                        'period_from'  => $periodFrom,
                        'period_to'    => $periodTo,
                        'is_edited'     => 1, // Mark as edited
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                    $incomingIds[] = $newId;
                }
            }
    
            DB::table('employee_experience_informations')
                ->where('employee_id', $id)
                ->whereNotIn('id', $incomingIds)
                ->delete();
        });
    
        return redirect()->route('employee.show', $id)
        ->with('success', 'Updated successfully!');
    }
    
    public function deleteExperience($id)
    {
        // Delete experience by ID
        DB::table('employee_experience_informations')->where('id', $id)->delete(); // Corrected table name
        return redirect()->back()->with('success', 'Experience deleted successfully!');
    }
}
