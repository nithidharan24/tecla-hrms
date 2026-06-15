<?php

namespace App\Http\Controllers\Backend\Employee;

use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use ZipArchive;
use App\Export\EmployeeTemplateExport;
use App\Imports\EmployeeImport;
use App\Mail\EmployeeCredentialsMail;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;





class AllEmployeeController extends Controller
{


    public function index(Request $request)
    {
        $designations = DB::table('designation')->get();
        $departments = DB::table('department')->get(); // Added for department filter

        $departmentFilter = getEmployeeDepartmentFilter();
        $branchFilter = getAdminBranchFilter();
        $managerFilter = getManagerTeamFilter();

        $query = DB::table('allemployees')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->leftJoin('hierarchies', 'allemployees.hierarchy_id', '=', 'hierarchies.id')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
            ->leftJoin('allemployees as manager', 'allemployees.manager_id', '=', 'manager.id')
            ->leftJoin('trainers', 'allemployees.trainer_id', '=', 'trainers.id')
            ->where('allemployees.deleted_at', 0)
            ->select(
                'allemployees.*',
                'designation.designation as designation_name',
                'hierarchies.hierarchy_level',
                'department.department as department_name',
                'branches.name as branch_name',
                'branches.address as branch_address',
                DB::raw('CONCAT(manager.firstname, " ", manager.lastname) as manager_name'),
                DB::raw('CONCAT(trainers.first_name, " ", trainers.last_name) as trainer_name')
            );

        // Apply filters
        if ($branchFilter) {
            $query->where('allemployees.branch_id', $branchFilter);
        } elseif ($departmentFilter) {
            $query->where('allemployees.department', $departmentFilter);
        }

        if ($managerFilter) {
            $query->where('allemployees.manager_id', $managerFilter);
        }

        // Search by Employee ID
        if ($request->filled('employee_id')) {
            $query->where('allemployees.employeeid', 'LIKE', '%' . $request->input('employee_id') . '%');
        }

        // Search by Employee Name
        if ($request->filled('employee_name')) {
            $employeeName = $request->input('employee_name');
            $query->where(function ($q) use ($employeeName) {
                $q->where('allemployees.firstname', 'LIKE', '%' . $employeeName . '%')
                  ->orWhere('allemployees.lastname', 'LIKE', '%' . $employeeName . '%');
            });
        }

        // Search by Designation
        if ($request->filled('designation') && $request->input('designation') !== 'Select Designation') {
            $query->where('allemployees.designation', $request->input('designation'));
        }

        // **Search by Department (new filter)**
        if ($request->filled('department') && $request->input('department') !== 'Select Department') {
            $query->where('allemployees.department', $request->input('department'));
        }

        // Filter by Branch (if no branch restriction)
        if ($request->filled('branch_id') && !$branchFilter) {
            $query->where('allemployees.branch_id', $request->input('branch_id'));
        }
        // Join terminations table for notice period check
        $query->leftJoin('terminations', 'allemployees.id', '=', 'terminations.employee_id');

        // Apply Notice Period Filter
        if ($request->filled('notice_period') && $request->input('notice_period') == '1') {
            $query->whereNotNull('terminations.notice_date');
        }

        $query->orderBy('allemployees.id', 'desc');
        $employees = $query->get();

        // Add module count and document info
        foreach ($employees as $employee) {
            $employee->module_count = DB::table('employee_module_access')
                ->where('employee_id', $employee->id)
                ->count();

            $employee->has_documents = !empty($employee->document_path);
        }

        $branches = collect();

        // Pass departments to view without removing existing data
        return view('hrms.Employee.AllEmployee.index', compact('employees', 'designations', 'branches', 'departments'));
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // 1. Create Data Sheet
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Employee Data');

        // Set header columns
        $headers = [
            'Employee ID (Optional)', 'First Name *', 'Last Name *', 'Username *',
            'Email *', 'Phone *', 'Joining Date (YYYY-MM-DD) *', 'Company *',
            'Department (Dropdown)', 'Designation (Dropdown)', 'Branch (Dropdown)',
            'Date of Birth (YYYY-MM-DD)', 'Gender (Male/Female)', 'Address',
            'Passport No', 'Passport Exp Date (YYYY-MM-DD)', 'Nationality', 'Religion', 'Marital Status',
            'Emergency Primary Name', 'Emergency Primary Relationship', 'Emergency Primary Phone',
            'Bank Name', 'Bank Account No', 'IFSC Code', 'PAN No', 'Bank Branch Name', 'Account Type',
            'Basic Salary',
            'Education Institution', 'Education Subject', 'Education Start Date', 'Education End Date', 'Education Degree', 'Education Grade',
            'Experience Company', 'Experience Location', 'Experience Position', 'Experience Period From', 'Experience Period To',
            'Family Member Name', 'Family Relationship', 'Family Phone',
            'Reporting Manager (Dropdown)', 'Team Lead (Dropdown)', 'Training Needed (Dropdown)', 
            'Document reminder (days)', 'Hierarchy Level (Dropdown)', 'Employee Type (Dropdown)'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $col++;
        }

        // Add Reference Data Sheet for Dropdowns
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('ReferenceData');
        
        $deps = DB::table('department')->pluck('department')->toArray();
        $desigs = DB::table('designation')->pluck('designation')->toArray();
        $branches = DB::table('branches')->pluck('name')->toArray();
        
        $employeesList = DB::table('allemployees')
                            ->select('firstname', 'lastname', 'employeeid')
                            ->where('status', 'active')
                            ->get()
                            ->map(function ($emp) {
                                return trim($emp->firstname . ' ' . $emp->lastname) . ' (' . $emp->employeeid . ')';
                            })->toArray();
        $hierarchiesList = DB::table('hierarchies')->pluck('hierarchy_level')->toArray();
        $trainingOpts = ['Yes', 'No'];
        $empTypes = ['Full Time', 'Part Time'];

        foreach ($deps as $index => $dep) { $refSheet->setCellValue('A' . ($index + 1), $dep); }
        foreach ($desigs as $index => $des) { $refSheet->setCellValue('B' . ($index + 1), $des); }
        foreach ($branches as $index => $br) { $refSheet->setCellValue('C' . ($index + 1), $br); }
        foreach ($employeesList as $index => $emp) { $refSheet->setCellValue('D' . ($index + 1), $emp); }
        foreach ($hierarchiesList as $index => $hl) { $refSheet->setCellValue('E' . ($index + 1), $hl); }
        foreach ($trainingOpts as $index => $opt) { $refSheet->setCellValue('F' . ($index + 1), $opt); }
        foreach ($empTypes as $index => $et) { $refSheet->setCellValue('G' . ($index + 1), $et); }

        $refSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
        
        // Ensure we apply validation to the 'Employee Data' sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Apply Data Validation to rows 2 to 100
        for ($i = 2; $i <= 100; $i++) {
            $depValidation = $sheet->getCell('I' . $i)->getDataValidation();
            $depValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $depValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $depValidation->setAllowBlank(true);
            $depValidation->setShowDropDown(true);
            $depValidation->setFormula1('\'ReferenceData\'!$A$1:$A$' . count($deps));

            $desValidation = $sheet->getCell('J' . $i)->getDataValidation();
            $desValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $desValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $desValidation->setAllowBlank(true);
            $desValidation->setShowDropDown(true);
            $desValidation->setFormula1('\'ReferenceData\'!$B$1:$B$' . count($desigs));

            $brValidation = $sheet->getCell('K' . $i)->getDataValidation();
            $brValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $brValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $brValidation->setAllowBlank(true);
            $brValidation->setShowDropDown(true);
            $brValidation->setFormula1('\'ReferenceData\'!$C$1:$C$' . count($branches));

            // Reporting Manager (AR)
            if (count($employeesList) > 0) {
                $mgrValidation = $sheet->getCell('AR' . $i)->getDataValidation();
                $mgrValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $mgrValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                $mgrValidation->setAllowBlank(true);
                $mgrValidation->setShowDropDown(true);
                $mgrValidation->setFormula1('\'ReferenceData\'!$D$1:$D$' . count($employeesList));
                
                // Team Lead (AS)
                $tlValidation = $sheet->getCell('AS' . $i)->getDataValidation();
                $tlValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $tlValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                $tlValidation->setAllowBlank(true);
                $tlValidation->setShowDropDown(true);
                $tlValidation->setFormula1('\'ReferenceData\'!$D$1:$D$' . count($employeesList));
            }

            // Training Needed (AT)
            $trnValidation = $sheet->getCell('AT' . $i)->getDataValidation();
            $trnValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $trnValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $trnValidation->setAllowBlank(true);
            $trnValidation->setShowDropDown(true);
            $trnValidation->setFormula1('\'ReferenceData\'!$F$1:$F$2');

            // Hierarchy Level (AV)
            if (count($hierarchiesList) > 0) {
                $hlValidation = $sheet->getCell('AV' . $i)->getDataValidation();
                $hlValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $hlValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                $hlValidation->setAllowBlank(true);
                $hlValidation->setShowDropDown(true);
                $hlValidation->setFormula1('\'ReferenceData\'!$E$1:$E$' . count($hierarchiesList));
            }

            // Employee Type (AW)
            $etValidation = $sheet->getCell('AW' . $i)->getDataValidation();
            $etValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $etValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $etValidation->setAllowBlank(true);
            $etValidation->setShowDropDown(true);
            $etValidation->setFormula1('\'ReferenceData\'!$G$1:$G$2');
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Employee_Import_Template.xlsx';

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }
public function importEmployees(Request $request)
{
    Log::info('importEmployees called', [
        'user_id' => optional(auth()->user())->id,
        'has_file' => $request->hasFile('file')
    ]);

    // Basic validation
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:51200' // 50MB
    ]);

    if (!$request->hasFile('file')) {
        Log::warning('importEmployees: no file present');
        if ($request->ajax()) return response()->json(['status' => 'error', 'message' => 'No file uploaded.'], 400);
        return redirect()->back()->with('error', 'No file uploaded.');
    }

    $file = $request->file('file');
    $originalName = $file->getClientOriginalName();
    $storedPath = $file->storeAs('temp', time() . '_' . preg_replace('/\s+/', '_', $originalName));
    $fullPath = storage_path('app/' . $storedPath);

    Log::info('importEmployees: uploaded file stored', ['stored_path' => $fullPath]);

    // Load spreadsheet
    try {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fullPath);
        Log::info('Spreadsheet loaded successfully', ['file' => $fullPath]);
    } catch (\Throwable $e) {
        Log::error('Spreadsheet load failed', ['exception' => $e->getMessage()]);
        Storage::delete($storedPath);
        if ($request->ajax()) return response()->json(['status' => 'error', 'message' => 'Failed to read Excel file: ' . $e->getMessage()], 400);
        return redirect()->back()->with('error', 'Failed to read Excel file: ' . $e->getMessage());
    }

    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    if (!isset($rows[0])) {
        Log::warning('importEmployees: spreadsheet empty or missing header', ['file' => $fullPath]);
        Storage::delete($storedPath);
        if ($request->ajax()) return response()->json(['status' => 'error', 'message' => 'Excel file seems empty or malformed.'], 400);
        return redirect()->back()->with('error', 'Excel file seems empty or malformed.');
    }

    $dataRows = array_slice($rows, 1); // skip header
    $inserted = 0;
    $skipped = 0;
    $validCount = 0;
    $errors = [];
    $rowNumber = 2;
    $forceImport = $request->input('force_import') == '1';

    foreach ($dataRows as $row) {
        $row = array_map(function ($v) {
            return is_string($v) ? trim($v) : $v;
        }, $row);

        $employeeId  = $row[0] ?? null;
        $firstName   = $row[1] ?? null;
        $lastName    = $row[2] ?? null;
        $username    = $row[3] ?? null;
        $email       = $row[4] ?? null;
        $phone       = $row[5] ?? null;
        $joiningDate = $row[6] ?? null;
        $company     = $row[7] ?? null;
        $department  = $row[8] ?? null;
        $designation = $row[9] ?? null;
        $branch      = $row[10] ?? null;
        $dob         = $row[11] ?? null;
        $gender      = $row[12] ?? null;
        $address     = $row[13] ?? null;
        $passportNo  = $row[14] ?? null;
        $passportExp = $row[15] ?? null;
        $nationality = $row[16] ?? null;
        $religion    = $row[17] ?? null;
        $marital     = $row[18] ?? null;
        $emergName   = $row[19] ?? null;
        $emergRel    = $row[20] ?? null;
        $emergPhone  = $row[21] ?? null;
        $bankName    = $row[22] ?? null;
        $bankAcc     = $row[23] ?? null;
        $ifsc        = $row[24] ?? null;
        $pan         = $row[25] ?? null;
        $bBranchName = trim($row[26] ?? '');
        $bAccountType = trim($row[27] ?? '');
        $basicSalary = trim($row[28] ?? '');

        // New nested fields
        $eduInstitution = trim($row[29] ?? '');
        $eduSubject = trim($row[30] ?? '');
        $eduStart = trim($row[31] ?? '');
        $eduEnd = trim($row[32] ?? '');
        $eduDegree = trim($row[33] ?? '');
        $eduGrade = trim($row[34] ?? '');

        $expCompany = trim($row[35] ?? '');
        $expLocation = trim($row[36] ?? '');
        $expPosition = trim($row[37] ?? '');
        $expFrom = trim($row[38] ?? '');
        $expTo = trim($row[39] ?? '');

        $famName = trim($row[40] ?? '');
        $famRel = trim($row[41] ?? '');
        $famPhone = trim($row[42] ?? '');

        // New fields 43-48
        $reportingManager = trim($row[43] ?? '');
        $teamLead = trim($row[44] ?? '');
        $trainingNeeded = trim($row[45] ?? '');
        $documentReminder = trim($row[46] ?? '');
        $hierarchyLevel = trim($row[47] ?? '');
        $employeeType = trim($row[48] ?? '');

        if (empty($employeeId) && empty($firstName) && empty($lastName) && empty($username) && empty($email) && empty($phone) && empty($joiningDate) && empty($company)) {
            Log::info('Skipping blank row', ['row' => $rowNumber]);
            $rowNumber++;
            continue;
        }

        // Row validation
        $rowErrors = [];
        if (empty($firstName)) $rowErrors[] = 'Missing First Name (Column B)';
        if (empty($lastName)) $rowErrors[] = 'Missing Last Name (Column C)';
        if (empty($username)) $rowErrors[] = 'Missing Username (Column D)';
        if (empty($email)) $rowErrors[] = 'Missing Email (Column E)';
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) $rowErrors[] = 'Invalid Email format (Column E)';
        if (empty($phone)) $rowErrors[] = 'Missing Phone (Column F)';
        if (empty($joiningDate)) $rowErrors[] = 'Missing Joining Date (Column G)';
        if (!empty($joiningDate)) {
            // If Excel date number
            if (is_numeric($joiningDate)) {
                $joiningDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($joiningDate)->format('Y-m-d');
            } else {
                // Convert MM/DD/YYYY to YYYY-MM-DD
                $d = \DateTime::createFromFormat('m/d/Y', $joiningDate);
                if ($d) {
                    $joiningDate = $d->format('Y-m-d');
                } else {
                    // Validate YYYY-MM-DD
                    $d = \DateTime::createFromFormat('Y-m-d', $joiningDate);
                    if (!$d || $d->format('Y-m-d') !== $joiningDate) {
                        $rowErrors[] = 'Joining Date must be YYYY-MM-DD format (Column G)';
                    }
                }
            }
        }
        
        if (empty($company)) $rowErrors[] = 'Missing Company (Column H)';
        if (!empty($email) && DB::table('allemployees')->where('email', $email)->exists()) $rowErrors[] = 'Email already exists in Database (Column E)';
        if (!empty($username) && DB::table('allemployees')->where('username', $username)->exists()) $rowErrors[] = 'Username already exists in Database (Column D)';

        // Validate Dropdowns and get IDs
        $departmentId = null;
        if ($department) {
            $departmentId = DB::table('department')->where('department', $department)->value('id');
            if (!$departmentId) $rowErrors[] = "Invalid Department: '{$department}' (Column I)";
        }

        $designationId = null;
        if ($designation) {
            $designationId = DB::table('designation')->where('designation', $designation)->value('id');
            if (!$designationId) $rowErrors[] = "Invalid Designation: '{$designation}' (Column J)";
        }

        $branchId = null;
        if ($branch) {
            $branchId = DB::table('branches')->where('name', $branch)->value('id');
            if (!$branchId) $rowErrors[] = "Invalid Branch: '{$branch}' (Column K)";
        }

        // Parse new dropdown values to IDs
        $managerId = null;
        if (!empty($reportingManager)) {
            preg_match('/\((EMP-[^)]+)\)$/', $reportingManager, $matches);
            if (isset($matches[1])) {
                $managerId = DB::table('allemployees')->where('employeeid', $matches[1])->value('id');
            }
        }
        
        $teamLeadId = null;
        if (!empty($teamLead)) {
            preg_match('/\((EMP-[^)]+)\)$/', $teamLead, $matches);
            if (isset($matches[1])) {
                $teamLeadId = DB::table('allemployees')->where('employeeid', $matches[1])->value('id');
            }
        }
        
        $hierarchyId = null;
        if (!empty($hierarchyLevel)) {
            $hierarchyId = DB::table('hierarchies')->where('hierarchy_level', $hierarchyLevel)->value('id');
            if (!$hierarchyId) $rowErrors[] = "Invalid Hierarchy Level: '{$hierarchyLevel}' (Column AV)";
        }

        if (count($rowErrors) > 0) {
            $skipped++;
            $employeeName = trim($firstName . ' ' . $lastName);
            $errorPrefix = $employeeName ? "Row {$rowNumber} (Employee '{$employeeName}')" : "Row {$rowNumber}";
            $errors[] = "{$errorPrefix} has errors: " . implode('; ', $rowErrors);
            Log::warning('Row validation skipped', ['row' => $rowNumber, 'errors' => $rowErrors, 'data' => $row]);
            $rowNumber++;
            continue;
        }

        if (!$forceImport) {
            $validCount++;
            $rowNumber++;
            continue;
        }

        // Generate random password
        $randomPassword = Str::random(8);
        $hashedPassword = Hash::make($randomPassword);

        // Insert row
        DB::beginTransaction();
        try {
            if (empty($employeeId)) {
                $lastEmp = DB::table('allemployees')->orderBy('id', 'desc')->first();
                $employeeId = $lastEmp ? 'EMP-' . str_pad($lastEmp->id + 1, 4, '0', STR_PAD_LEFT) : 'EMP-0001';
            }

            // Date parsing helpers
            $parseDate = function($dateStr) {
                if (empty($dateStr)) return null;
                if (is_numeric($dateStr)) {
                    return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateStr)->format('Y-m-d');
                }
                
                // Try various formats
                $formats = ['m/d/Y', 'd/m/Y', 'n/j/Y', 'j/n/Y', 'Y-m-d'];
                foreach ($formats as $format) {
                    $d = \DateTime::createFromFormat($format, $dateStr);
                    if ($d && $d->format($format) === $dateStr) {
                        return $d->format('Y-m-d');
                    }
                }
                
                // Fallback attempt
                try {
                    $d = new \DateTime($dateStr);
                    return $d->format('Y-m-d');
                } catch (\Exception $e) {
                    return null; // Return null if totally unparseable to avoid SQL errors
                }
            };

            $dobParsed = $parseDate($dob);
            $passportExpParsed = $parseDate($passportExp);
            $eduStartParsed = $parseDate($eduStart);
            $eduEndParsed = $parseDate($eduEnd);
            $expFromParsed = $parseDate($expFrom);
            $expToParsed = $parseDate($expTo);

            $empDbId = DB::table('allemployees')->insertGetId([
                'employeeid'  => $employeeId,
                'firstname'   => $firstName,
                'lastname'    => $lastName,
                'username'    => $username,
                'email'       => $email,
                'phone'       => $phone,
                'joiningdate' => $joiningDate,
                'company'     => $company,
                'department'  => $departmentId,
                'designation' => $designationId,
                'branch_id'   => $branchId,
                'manager_id'  => $managerId,
                'team_lead_id'=> $teamLeadId,
                'training_needed' => $trainingNeeded,
                'hierarchy_id' => $hierarchyId,
                'employee_type'=> $employeeType,
                'password'    => $hashedPassword,
                'created_at'  => now(),
                'updated_at'  => now()
            ]);

            DB::table('employee_profile_main')->insert([
                'id' => $empDbId,
                'employee_id' => $empDbId,
                'email' => $email,
                'date_of_joining' => $joiningDate,
                'birthday' => $dobParsed,
                'gender' => $gender,
                'address' => $address,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('employee_personal_informations')->insert([
                'id' => $empDbId,
                'employee_id' => $empDbId,
                'passport_no' => $passportNo,
                'passport_exp_date' => $passportExpParsed,
                'nationality' => $nationality,
                'religion' => $religion,
                'marital_status' => $marital,
                'is_edited' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('employee_emergency_contact')->insert([
                'id' => $empDbId,
                'employee_id' => $empDbId,
                'primary_name' => $emergName,
                'relationship' => $emergRel,
                'phone' => $emergPhone,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if ($bankName || $bankAcc || $ifsc || $pan || $bBranchName || $bAccountType) {
                DB::table('employee_bank_informations')->insert([
                    'id' => $empDbId,
                    'employee_id' => $empDbId,
                    'bank_name' => $bankName,
                    'bank_account_no' => $bankAcc,
                    'ifsc_code' => $ifsc,
                    'pan_no' => $pan,
                    'branch_name' => $bBranchName,
                    'account_type' => $bAccountType,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            if ($basicSalary && is_numeric($basicSalary) && $basicSalary > 0) {
                DB::table('employee_salaries')->insert([
                    'id' => $empDbId,
                    'employee_id' => $empDbId,
                    'basic' => $basicSalary,
                    'da' => 0, 'hra' => 0, 'conveyance' => 0, 'allowance' => 0,
                    'medical' => 0, 'pf' => 0, 'esi' => 0, 'tds' => 0, 'tax' => 0,
                    'welfare' => 0, 'net_salary' => $basicSalary,
                    'total_earnings' => $basicSalary, 'total_deductions' => 0,
                    'employee_leave' => 0
                ]);
            }

            if ($eduInstitution) {
                DB::table('employee_education_informations')->insert([
                    'employee_id' => $empDbId,
                    'institution' => $eduInstitution,
                    'subject' => $eduSubject,
                    'start_date' => $eduStartParsed,
                    'end_date' => $eduEndParsed,
                    'degree' => $eduDegree,
                    'grade' => $eduGrade,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            if ($expCompany) {
                DB::table('employee_experience_informations')->insert([
                    'employee_id' => $empDbId,
                    'company_name' => $expCompany,
                    'location' => $expLocation,
                    'position' => $expPosition,
                    'period_from' => $expFromParsed,
                    'period_to' => $expToParsed,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            if ($famName) {
                DB::table('employee_family_informations')->insert([
                    'employee_id' => $empDbId,
                    'name' => $famName,
                    'relationship' => $famRel,
                    'phone' => $famPhone,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            if (!empty($documentReminder) && is_numeric($documentReminder)) {
                DB::table('employee_document_reminders')->insert([
                    'employee_id' => $empDbId,
                    'reminder_days' => $documentReminder,
                    'remind_at' => now()->addDays($documentReminder),
                    'is_sent' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            // Send credentials email
            $this->sendEmployeeCredentials($email, $firstName, $randomPassword);

            $inserted++;
            Log::info('Inserted employee from import', ['row' => $rowNumber, 'email' => $email, 'username' => $username]);
        } catch (\Throwable $e) {
            DB::rollBack();
            $skipped++;
            $employeeName = trim($firstName . ' ' . $lastName);
            $errorPrefix = $employeeName ? "Employee '{$employeeName}'" : "Row {$rowNumber}";
            $errors[] = "{$errorPrefix} DB insert failed: " . $e->getMessage();
            Log::error('Database insert error on import', ['row' => $rowNumber, 'exception' => $e->getMessage(), 'data' => $row]);
        }

        $rowNumber++;
    }

    Storage::delete($storedPath);

    if (!$forceImport) {
        return response()->json([
            'status' => 'confirm',
            'errors' => $errors,
            'valid_count' => $validCount
        ]);
    }

    Log::error('Import completed', ['inserted' => $inserted, 'skipped' => $skipped, 'errors' => $errors]);

    $flash = "Import finished: {$inserted} imported, {$skipped} skipped.";
    
    if ($request->ajax()) {
        return response()->json([
            'status' => 'success',
            'message' => $flash,
            'errors' => $errors
        ]);
    }

    if (!empty($errors)) {
        return redirect()->route('employee.index')->with('success', $flash)->with('import_errors', $errors);
    }

    return redirect()->route('employee.index')->with('success', $flash);
}

/**
 * Send employee credentials email
 */

public function create()
{
    $departmentFilter = getEmployeeDepartmentFilter();
    $branchFilter = getAdminBranchFilter();

    // Fetch departments, designations, hierarchies
    $departments = DB::table('department')->get();
    $designations = DB::table('designation')->get();
    $hierarchies = DB::table('hierarchies')->orderBy('hierarchy_level')->get();

    // Fetch branches based on admin access
    if ($branchFilter) {
        $branches = DB::table('branches')
            ->where('status', 1)
            ->where('id', $branchFilter)
            ->get();
    } else {
        $branches = DB::table('branches')
            ->where('status', 1)
            ->get();
    }

    // ✅ Fetch ALL employees in same department (filtered by branch if needed)
    $managers = DB::table('allemployees')
        ->when($branchFilter, function ($query) use ($branchFilter) {
            $query->where('branch_id', $branchFilter);
        })
        ->when($departmentFilter, function ($query) use ($departmentFilter) {
            $query->where('department', $departmentFilter);
        })
        ->select('id', 'firstname', 'lastname', 'employeeid', 'designation')
        ->get();

    // Fetch active trainers
    $trainers = DB::table('trainers')
        ->where('status', 'Active')
        ->get();
        // ⬅️ Fetch ACTIVE PF & ESI settings
    $statutory = DB::table('statutory_rates')->where('is_active', 1)->first();



    // Return data to view
    return view('hrms.Employee.AllEmployee.add', compact(
        'departments',
        'designations',
        'hierarchies',
        'branches',
        'managers',
        'trainers',
        'statutory' 
    ));
}


    public function getDesignations($department_id)
    {
        $designations = DB::table('designation')
                          ->where('department_id', $department_id)
                          ->get();
        return response()->json($designations);
    }

    /**
     * Get list of managers for dropdown
     */
    public function getManagers()
    {
        try {
            $branchFilter = getAdminBranchFilter();
            $managers = $this->getManagersList($branchFilter);
            
            return response()->json([
                'success' => true,
                'managers' => $managers
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching managers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading managers'
            ], 500);
        }
    }

    /**
     * Private method to get managers list with branch filter
     */
    private function getManagersList($branchFilter = null)
    {
        $query = DB::table('allemployees')
            ->join('designation', 'allemployees.designation', '=', 'designation.id')
            ->where('designation.designation', 'LIKE', '%Manager%')
            ->where('allemployees.deleted_at', 0)
            ->select(
                'allemployees.id',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.employeeid',
                'allemployees.branch_id',
                'designation.designation as designation_name'
            )
            ->orderBy('allemployees.firstname');

        // Apply branch filter if specified
        if ($branchFilter) {
            $query->where('allemployees.branch_id', $branchFilter);
        }

        return $query->get();
    }

  public function store(Request $request)
{
    $branchFilter = getAdminBranchFilter();

    // 🔹 Check subcompany plan restriction
    $subcompany = DB::table('subcompany')->first();
    if ($subcompany && $subcompany->users > 0) {
        $branchId = $branchFilter ?? $request->branch_id;
        $existingUsersCount = DB::table('allemployees')
            ->where('branch_id', $branchId)
            ->count();

        if ($existingUsersCount >= $subcompany->users) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Your plan allows only {$subcompany->users} user(s) per branch. Please upgrade your plan to add more employees.");
        }
    }

    // Validation rules
    $validationRules = [
        'employeeid' => 'nullable|string|max:50|unique:allemployees,employeeid',
        'firstname' => 'required|string|max:255',
        'lastname' => 'required|string|max:255',
       
        'email' => 'required|email|unique:allemployees',
        'joiningdate' => 'required|date',
        'phone' => 'required|string|max:15',
     
        'department' => 'required|integer|exists:department,id',
        'designation' => 'required|integer|exists:designation,id',
        'manager_id' => 'nullable|integer|exists:allemployees,id',
        'training_needed' => 'required|in:Yes,No',
        'trainer_id' => 'nullable|integer|exists:trainers,id',
        'profile_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        'document_files.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,txt|max:5120',
        'document_reminder_days' => 'nullable|integer|min:0|max:3650',
        'modules' => 'nullable|array',
        'modules.*.enabled' => 'nullable|boolean',
        'modules.*.can_view' => 'nullable|boolean',
        'modules.*.can_create' => 'nullable|boolean',
        'modules.*.can_edit' => 'nullable|boolean',
        'modules.*.can_delete' => 'nullable|boolean',
        'modules.*.can_approve' => 'nullable|boolean', 
        'modules.*.can_download' => 'nullable|boolean', 
        'modules.*.can_export' => 'nullable|boolean',   
    ];

    if ($branchFilter) {
        $validationRules['branch_id'] = 'required|integer|in:' . $branchFilter;
    } else {
        $validationRules['branch_id'] = 'required|integer|exists:branches,id';
    }

    $request->validate($validationRules);

    if ($request->training_needed === 'Yes' && empty($request->trainer_id)) {
        return redirect()->back()
            ->withInput()
            ->withErrors(['trainer_id' => 'Trainer is required when training is needed.']);
    }

    if ($branchFilter) {
        $request->merge(['branch_id' => $branchFilter]);
    }

    DB::beginTransaction();

    try {
        // ✅ Decide employee ID: manual or auto
        $employeeIdValue = $request->employeeid ?: $this->generateEmployeeId($request->department);

        $randomPassword = Str::random(8);
        $hashedPassword = Hash::make($randomPassword);

        // Handle profile image
        $profileImage = $request->file('profile_image');
        $imageName = time() . '.' . $profileImage->getClientOriginalExtension();
        $profileImage->move(public_path('admin/uploads/images/employee'), $imageName);
        $imagePath = 'admin/uploads/images/employee/' . $imageName;

        // Handle document files
        $zipPath = null;
        $documentFilesCount = 0;
        if ($request->hasFile('document_files')) {
            $documentFilesCount = count($request->file('document_files'));
            $documentBasePath = public_path('admin/uploads/documents/employee');
            if (!file_exists($documentBasePath)) mkdir($documentBasePath, 0755, true);

            $tempDir = storage_path('app/temp/' . Str::random(16));
            mkdir($tempDir, 0755, true);

            $zipFileName = 'docs_' . $employeeIdValue . '_' . time() . '.zip';
            $zip = new ZipArchive();
            if ($zip->open($documentBasePath . '/' . $zipFileName, ZipArchive::CREATE) === TRUE) {
                foreach ($request->file('document_files') as $file) {
                    $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                    $file->move($tempDir, $safeName);
                    $zip->addFile($tempDir . '/' . $safeName, 'employee_documents/' . $safeName);
                }
                $zip->close();
                $zipPath = 'admin/uploads/documents/employee/' . $zipFileName;
                array_map('unlink', glob("$tempDir/*"));
                rmdir($tempDir);
            }
        }

        // Get hierarchy modules
        $hierarchyModules = [];
        $hierarchy = DB::table('hierarchies')->where('id', $request->hierarchy_id)->first();
        if ($hierarchy && $hierarchy->modules) {
            $decodedModules = json_decode($hierarchy->modules, true);
            if (is_array($decodedModules)) {
                $hierarchyModules = array_keys($decodedModules);
            }
        }
        // Insert employee
        $employeeId = DB::table('allemployees')->insertGetId([
            'employeeid' => $employeeIdValue,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
         
            'email' => $request->email,
            'joiningdate' => $request->joiningdate,
            'phone' => $request->phone,
           
            'password' => $hashedPassword,
            'department' => $request->department,
            'designation' => $request->designation,
            'hierarchy_id' => $request->hierarchy_id,
            'branch_id' => $request->branch_id,
            'manager_id' => $request->manager_id,
            'training_needed' => $request->training_needed,
            'trainer_id' => $request->training_needed === 'Yes' ? $request->trainer_id : null,
            'profile_image' => $imagePath,
            'document_path' => $zipPath,
            'employee_type' => $request->employee_type,
            'team_lead_id' => $request->team_lead_id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // The rest of your existing code remains unchanged...
        // Some installations have no AUTO_INCREMENT on this table's PK.
        // Ensure we provide an explicit id matching the employee record to avoid SQL errors.
        DB::table('employee_profile_main')->insert([
            'employee_id' => $employeeId,
            'email' => $request->email,
            'date_of_joining' => $request->joiningdate,
            'birthday'       => $request->birthday,
            'gender'         => $request->gender,
            'address'        => $request->address,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('employee_personal_informations')->insert([
            'id' => $employeeId,
            'employee_id' => $employeeId,
            'passport_no' => $request->passport_no,
            'passport_exp_date' => $request->passport_exp_date ?? null,
            'tel' => $request->phone ?? null,
            'nationality' => $request->nationality ?? null,
            'religion' => $request->religion ?? null,
            'marital_status' => $request->marital_status ?? null,
            'is_edited'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('employee_emergency_contact')->insert([
            'id'                     => $employeeId,
            'employee_id'            => $employeeId,
            'primary_name'           => $request->primary_name,
            'relationship'           => $request->relationship,
            'phone'                  => $request->primary_phone ?? $request->phone ?? null,
            'secondary_name'         => $request->secondary_name,
            'secondary_relationship' => $request->secondary_relationship,
            'secondary_phone'        => $request->secondary_phone,
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);
        
    DB::table('employee_bank_informations')->insert([
        'id'               => $employeeId,
        'employee_id'      => $employeeId,
        'bank_name'        => $request->bank_name ?? null,
        'bank_account_no'  => $request->bank_account_no ?? null,
        'ifsc_code'        => $request->ifsc_code ?? null,
        'pan_no'           => $request->pan_no ?? null,
        'branch_name'      => $request->branch_name ?? null,
        'account_type'     => $request->account_type ?? null,
        'created_at'       => now(),
        'updated_at'       => now(),
    ]);
    // Save document reminder if admin provided days (guarded in try/catch if table not created yet)
    try {
        $reminderDays = intval($request->document_reminder_days ?? 0);
        if ($reminderDays > 0) {
            $remindAt = \Carbon\Carbon::now()->addDays($reminderDays);
            DB::table('employee_document_reminders')->insert([
                'employee_id' => $employeeId,
                'reminder_days' => $reminderDays,
                'remind_at' => $remindAt,
                'is_sent' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    } catch (\Throwable $e) {
        Log::warning('Could not save document reminder: ' . $e->getMessage());
    }
    foreach ($request->family_members as $member) {
        DB::table('employee_family_informations')->insert([
            'employee_id'   =>  $employeeId,
            'name'          => $member['name'],
            'relationship'  => $member['relationship'],
           
           
            'phone'         => $member['phone'] ?? null,
          
        ]);
    }
    foreach ($request->education as $edu) {
        DB::table('employee_education_informations')->insert([
            'employee_id' => $employeeId,
            'institution' => $edu['institution'],
            'subject' => $edu['subject'],
            'start_date' => $edu['start_date'] ?? null,
            'end_date' => $edu['end_date'] ?? null,
            'degree' => $edu['degree'] ?? null,
            'grade' => $edu['grade'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    foreach ($request->experience as $exp) {
        DB::table('employee_experience_informations')->insert([
            'employee_id' => $employeeId,
            'company_name' => $exp['company_name'],
            'location' => $exp['location'],
            'position' => $exp['job_position'],
            'period_from' => $exp['period_from'],
            'period_to' => $exp['period_to'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    


        
// In your store method, add this after employee creation and before DB::commit();

// Handle Salary Creation if basic salary is provided
if ($request->basic && $request->basic > 0) {
    $basicSalary = floatval($request->basic);
    $daPercentage = floatval($request->da) ?? 0;
    $hraPercentage = floatval($request->hra) ?? 0;
    $pfPercentage = floatval($request->pf) ?? 0;
    $esiPercentage = floatval($request->esi) ?? 0;
    
    // Calculate amounts
    $daAmount = ($daPercentage / 100) * $basicSalary;
    $hraAmount = ($hraPercentage / 100) * $basicSalary;
    $pfAmount = ($pfPercentage / 100) * $basicSalary;
    $esiAmount = ($esiPercentage / 100) * $basicSalary;
        
    // Calculate totals
    $totalEarnings = $basicSalary + $daAmount + $hraAmount + 
                    floatval($request->conveyance ?? 0) + 
                    floatval($request->allowance ?? 0) + 
                    floatval($request->medical ?? 0);
    
    $totalDeductions = $pfAmount + $esiAmount + 
                      floatval($request->tds ?? 0) + 
                      floatval($request->tax ?? 0) + 
                      floatval($request->welfare ?? 0);
    
    
    DB::table('employee_salaries')->insert([
        'employee_id' => $employeeId,
        'basic' => $basicSalary,
        'da' => $daAmount,
        'hra' => $hraAmount,
        'conveyance' => floatval($request->conveyance) ?? 0,
        'allowance' => floatval($request->allowance) ?? 0,
        'medical' => floatval($request->medical) ?? 0,
        'pf' => $pfAmount,
        'esi' => $esiAmount,
        'tds' => floatval($request->tds) ?? 0,
        'tax' => floatval($request->tax) ?? 0,
        'welfare' => floatval($request->welfare) ?? 0,
        'net_salary' => floatval($request->net_salary) ?? 0,
        'total_earnings' => $totalEarnings, // ✅ Store total earnings
        'total_deductions' => $totalDeductions, // ✅ Store total deductions
        'gross_salary' => floatval($request->gross_salary ?? 0),
        'employee_leave' => 0
    ]);
    
    Log::info('Salary record created for employee ID: ' . $employeeId);
}
        // Modules
// In your store method, update module data handling
$moduleData = [];
$totalModulesAssigned = 0;
if ($request->has('modules') && is_array($request->modules)) {
    foreach ($request->modules as $moduleName => $permissions) {
        if (is_string($moduleName) && is_array($permissions) && !empty($permissions['enabled'])) {
            
            $cleanHierarchyModules = array_filter($hierarchyModules, function($module) {
                return is_string($module) && !empty(trim($module));
            });
            
            $isFromHierarchy = in_array($moduleName, $cleanHierarchyModules);
            
            $moduleData[] = [
                'employee_id' => $employeeId,
                'hierarchy_id' => $request->hierarchy_id,
                'module_name' => $moduleName,
                'source' => $isFromHierarchy ? 'hierarchy' : 'manual',
                'can_view' => !empty($permissions['can_view']),
                'can_create' => !empty($permissions['can_create']),
                'can_edit' => !empty($permissions['can_edit']),
                'can_delete' => !empty($permissions['can_delete']),
                'can_approve' => !empty($permissions['can_approve']),
                'can_download' => !empty($permissions['can_download']),
                'can_export' => !empty($permissions['can_export']),
                'created_at' => now(),
                'updated_at' => now()
            ];
            $totalModulesAssigned++;
        }
    }
}
        if (!empty($moduleData)) {
            DB::table('employee_module_access')->insert($moduleData);
            Log::info('Inserted ' . count($moduleData) . ' modules for employee ID: ' . $employeeId);
        }

        $this->sendEmployeeCredentials($request->email, $request->firstname, $randomPassword);

        DB::commit();

        // Success message (same as your existing code)
        $managerInfo = '';
        if ($request->manager_id) {
            $manager = DB::table('allemployees')->find($request->manager_id);
            $managerInfo = " Manager assigned: {$manager->firstname} {$manager->lastname}.";
        }
        $trainerInfo = '';
        if ($request->training_needed === 'Yes' && $request->trainer_id) {
            $trainer = DB::table('trainers')->find($request->trainer_id);
            $trainerInfo = " Trainer assigned: {$trainer->first_name} {$trainer->last_name}.";
        }

        $permissionCounts = [
            'view' => collect($moduleData)->where('can_view', true)->count(),
            'create' => collect($moduleData)->where('can_create', true)->count(),
            'edit' => collect($moduleData)->where('can_edit', true)->count(),
            'delete' => collect($moduleData)->where('can_delete', true)->count(),
        ];

        $permissionInfo = " Permissions: {$permissionCounts['view']} view, {$permissionCounts['create']} create, {$permissionCounts['edit']} edit, {$permissionCounts['delete']} delete.";

    // Get global leave rules
    $annualLeave = DB::table('annual_leaves')->first();
    $medicalLeave = DB::table('medical_leaves')->first();

    // Insert into employee_leave_information table
    DB::table('employee_leave_information')->insert([
        'employee_id' => $employeeId,
        'casual_leaves' => $annualLeave->days,
        'sick_leaves' => $medicalLeave->sick,
        'hospitalization_leaves' => $medicalLeave->hospitalisation,
        'maternity_leaves' => $medicalLeave->maternity,
        'paternity_leaves' => $medicalLeave->paternity,
        'annual_leaves' => $annualLeave->days,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    Log::info('Leave information created for employee ID: ' . $employeeId);


        return redirect()->route('employee.index')
            ->with('success', "Employee created successfully. " .
                   ($documentFilesCount > 0 ? "{$documentFilesCount} documents archived. " : "") .
                   "$totalModulesAssigned modules assigned." . $permissionInfo . $managerInfo . $trainerInfo);

    } catch (\Exception $e) {
        DB::rollback();

        if (isset($imagePath) && file_exists(public_path($imagePath))) unlink(public_path($imagePath));
        if (isset($zipPath) && file_exists(public_path($zipPath))) unlink(public_path($zipPath));
        if (isset($tempDir) && is_dir($tempDir)) {
            array_map('unlink', glob("$tempDir/*"));
            rmdir($tempDir);
        }

        Log::error('Employee creation failed: ' . $e->getMessage());
        return redirect()->back()
            ->withInput()
            ->with('error', 'Employee creation failed: ' . $e->getMessage());
    }
}

       public function edit($id)
    {
        $departmentFilter = getEmployeeDepartmentFilter();
        $branchFilter = getAdminBranchFilter();

        $employee = DB::table('allemployees')->find($id);

        if (!$employee) {
            return redirect()->route('employee.index')->withErrors(['message' => 'Employee not found.']);
        }

        if ($branchFilter && $employee->branch_id != $branchFilter) {
            return redirect()->route('employee.index')->withErrors(['message' => 'Access denied. You can only edit employees from your branch.']);
        }

        $salary = DB::table('employee_salaries')->where('employee_id', $id)->first();
        $personal = DB::table('employee_personal_informations')->where('employee_id', $id)->first();
        $profile = DB::table('employee_profile_main')->where('employee_id', $id)->first();
        $emergency = DB::table('employee_emergency_contact')
            ->where('employee_id', $id)
            ->orderBy('id', 'desc')
            ->first();

        $experiences = DB::table('employee_experience_informations')
            ->where('employee_id', $id)
            ->get();

        $bankInfo = DB::table('employee_bank_informations')
            ->where('employee_id', $id)
            ->first();

        $familyMembers = DB::table('employee_family_informations')
            ->where('employee_id', $id)
            ->get();

        $educationInfo = DB::table('employee_education_informations')
            ->where('employee_id', $id)
            ->get();

        $departments = DB::table('department')->get();
        $designations = DB::table('designation')->get();
        $hierarchies = DB::table('hierarchies')->orderBy('hierarchy_level')->get();

        if ($branchFilter) {
            $branches = DB::table('branches')
                ->where('status', 1)
                ->where('id', $branchFilter)
                ->get();
        } else {
            $branches = DB::table('branches')
                ->where('status', 1)
                ->get();
        }

        // 1. Fetch Managers (EXCLUDING the current employee being edited)
        $managersQuery = DB::table('allemployees')
            ->join('designation', 'allemployees.designation', '=', 'designation.id')
            ->where('designation.designation', 'LIKE', '%Manager%')
            ->where('allemployees.deleted_at', 0)
            ->where('allemployees.id', '!=', $id) // Exclude self
            ->select(
                'allemployees.id',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.employeeid',
                'designation.designation as designation_name'
            )
            ->orderBy('allemployees.firstname');

        if ($branchFilter) {
            $managersQuery->where('allemployees.branch_id', $branchFilter);
        }
        $managers = $managersQuery->get();

        // 2. Fetch Team Leads (EXCLUDING the current employee being edited)
        $teamLeads = DB::table('allemployees')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->where('id', '!=', $id) // Exclude self
            ->select('id', 'firstname', 'lastname', 'employeeid')
            ->orderBy('firstname')
            ->get();

        $trainers = DB::table('trainers')->where('status', 'Active')->get();

        $employeeModules = DB::table('employee_module_access')
            ->where('employee_id', $id)
            ->pluck('module_name')
            ->toArray();

        $permissionsCollection = DB::table('employee_module_access')
            ->where('employee_id', $id)
            ->select(
                'module_name',
                'can_view',
                'can_create',
                'can_edit',
                'can_delete',
                'can_approve',
                'can_download',
                'can_export',
                'source'
            )
            ->get();

        $employeePermissions = [];
        foreach ($permissionsCollection as $perm) {
            $employeePermissions[$perm->module_name] = $perm;
        }

        $statutory = DB::table('statutory_rates')->where('is_active', 1)->first();

        $documentReminder = DB::table('employee_document_reminders')
            ->where('employee_id', $id)
            ->orderByDesc('id')
            ->first();

        return view('hrms.Employee.AllEmployee.edit', compact(
            'employee',
            'departments',
            'designations',
            'hierarchies',
            'branches',
            'managers',
            'teamLeads', // Excludes self
            'trainers',
            'employeeModules',
            'salary',
            'employeePermissions',
            'personal',
            'profile',
            'bankInfo',
            'familyMembers',
            'educationInfo',
            'emergency',
            'experiences',
            'statutory',
            'documentReminder'
        ));
    }
   public function update(Request $request, $id)
{
    $branchFilter = getAdminBranchFilter();
    DB::beginTransaction();

    try {
        $employee = DB::table('allemployees')->find($id);
        if (!$employee) {
            return redirect()->route('employee.index')->withErrors(['message' => 'Employee not found.']);
        }

        if ($branchFilter && $employee->branch_id != $branchFilter) {
            return redirect()->route('employee.index')->withErrors(['message' => 'Access denied.']);
        }

        // 🔹 Check subcompany plan restriction
        $subcompany = DB::table('subcompany')->first();
        if ($subcompany && $subcompany->users > 0) {
            $newBranchId = $branchFilter ?? $request->branch_id;

            if ($newBranchId != $employee->branch_id) {
                $existingUsersCount = DB::table('allemployees')
                    ->where('branch_id', $newBranchId)
                    ->count();

                if ($existingUsersCount >= $subcompany->users) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Your plan allows only {$subcompany->users} user(s) per branch. Please upgrade your plan.");
                }
            }
        }

        // Updated validation rules (matching add side)
        $validationRules = [
            'employeeid' => 'nullable|string|max:50|unique:allemployees,employeeid,' . $id,
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:allemployees,email,' . $id,
            'joiningdate' => 'required|date',
            'phone' => 'required|string|max:15',
            'department' => 'required|integer|exists:department,id',
            'designation' => 'required|integer|exists:designation,id',
            'manager_id' => 'nullable|integer|exists:allemployees,id',
            'training_needed' => 'required|in:Yes,No',
            'trainer_id' => 'nullable|integer|exists:trainers,id',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'document_files.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,txt|max:5120',
            'document_reminder_days' => 'nullable|integer|min:0|max:3650',
            'modules' => 'nullable|array',
            'modules.*.enabled' => 'nullable|boolean',
            'modules.*.can_view' => 'nullable|boolean',
            'modules.*.can_create' => 'nullable|boolean',
            'modules.*.can_edit' => 'nullable|boolean',
            'modules.*.can_delete' => 'nullable|boolean',
            'modules.*.can_approve' => 'nullable|boolean',
            'modules.*.can_download' => 'nullable|boolean',
            'modules.*.can_export' => 'nullable|boolean',
        ];

        if ($branchFilter) {
            $request->merge(['branch_id' => $branchFilter]);
            $validationRules['branch_id'] = 'required|integer|in:' . $branchFilter;
        } else {
            $validationRules['branch_id'] = 'required|integer|exists:branches,id';
        }

        $request->validate($validationRules);

        if ($request->training_needed === 'Yes' && empty($request->trainer_id)) {
            return redirect()->back()->withInput()->withErrors(['trainer_id' => 'Trainer is required when training is needed.']);
        }

        // Handle profile image upload
        $imagePath = $employee->profile_image;
        if ($request->hasFile('profile_image')) {
            if ($employee->profile_image && file_exists(public_path($employee->profile_image))) {
                unlink(public_path($employee->profile_image));
            }
            $profileImage = $request->file('profile_image');
            $imageName = time() . '.' . $profileImage->getClientOriginalExtension();
            $profileImage->move(public_path('admin/uploads/images/employee'), $imageName);
            $imagePath = 'admin/uploads/images/employee/' . $imageName;
        }

        // Handle document uploads
        $zipPath = $employee->document_path;
        $documentFilesCount = 0;
        $documentBasePath = public_path('admin/uploads/documents/employee');
        $tempDir = storage_path('app/temp/' . Str::random(16));

        if ($request->hasFile('document_files')) {
            $documentFilesCount = count($request->file('document_files'));
            if (!file_exists($documentBasePath)) {
                mkdir($documentBasePath, 0755, true);
            }
            mkdir($tempDir, 0755, true);

            $zipFileName = $employee->document_path ? basename($employee->document_path) : 'docs_' . $employee->employeeid . '_' . time() . '.zip';
            $zipFullPath = $documentBasePath . '/' . $zipFileName;
            $zip = new ZipArchive();

            if ($zip->open($zipFullPath, ZipArchive::CREATE) === TRUE) {
                foreach ($request->file('document_files') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $safeName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                    $tempPath = $tempDir . '/' . $safeName;
                    $file->move($tempDir, $safeName);
                    if ($zip->locateName('employee_documents/' . $safeName) === false) {
                        $zip->addFile($tempPath, 'employee_documents/' . $safeName);
                    }
                }
                $zip->close();
                $zipPath = 'admin/uploads/documents/employee/' . $zipFileName;
                array_map('unlink', glob("$tempDir/*"));
                rmdir($tempDir);
            }
        }

        // 🔹 Manual Employee ID takes priority; auto-generate only if blank
        $newEmployeeId = $request->employeeid
            ? $request->employeeid
            : ($employee->department != $request->department ? $this->generateEmployeeId($request->department) : $employee->employeeid);

        // Handle hierarchy modules
        $hierarchyModules = [];
        if ($request->hierarchy_id) {
            $hierarchy = DB::table('hierarchies')->where('id', $request->hierarchy_id)->first();
            if ($hierarchy && $hierarchy->modules) {
                $decodedModules = json_decode($hierarchy->modules, true);
                if (is_array($decodedModules)) {
                    $hierarchyModules = array_keys($decodedModules);
                }
            }
        }

        // Process module access with hierarchy_id
        $moduleData = [];
        $totalModulesAssigned = 0;
        if ($request->has('modules') && is_array($request->modules)) {
            foreach ($request->modules as $moduleName => $permissions) {
                if (is_string($moduleName) && !empty(trim($moduleName)) && isset($permissions['enabled']) && $permissions['enabled']) {
                    $trimmedModuleName = trim($moduleName);
                    $isFromHierarchy = in_array($trimmedModuleName, $hierarchyModules);

                    $moduleData[] = [
                        'employee_id' => $id,
                        'hierarchy_id' => $request->hierarchy_id,
                        'module_name' => $trimmedModuleName,
                        'source' => $isFromHierarchy ? 'hierarchy' : 'manual',
                        'can_view' => isset($permissions['can_view']) ? (bool)$permissions['can_view'] : false,
                        'can_create' => isset($permissions['can_create']) ? (bool)$permissions['can_create'] : false,
                        'can_edit' => isset($permissions['can_edit']) ? (bool)$permissions['can_edit'] : false,
                        'can_delete' => isset($permissions['can_delete']) ? (bool)$permissions['can_delete'] : false,
                        'can_approve' => isset($permissions['can_approve']) ? (bool)$permissions['can_approve'] : false,
                        'can_download' => isset($permissions['can_download']) ? (bool)$permissions['can_download'] : false,
                        'can_export' => isset($permissions['can_export']) ? (bool)$permissions['can_export'] : false,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    $totalModulesAssigned++;
                }
            }
        }

        // Prepare update data
        $updateData = $request->only([
            'firstname', 'lastname', 'email',
            'joiningdate', 'phone', 'designation'
        ]);
        $updateData['employeeid'] = $newEmployeeId;
        $updateData['department'] = $request->department;
        $updateData['hierarchy_id'] = $request->hierarchy_id;
        $updateData['branch_id'] = $request->branch_id;
        $updateData['manager_id'] = $request->manager_id;
        $updateData['team_lead_id'] = $request->team_lead_id;
        $updateData['training_needed'] = $request->training_needed;
        $updateData['employee_type'] = $request->employee_type;
        $updateData['trainer_id'] = $request->training_needed === 'Yes' ? $request->trainer_id : null;
        $updateData['profile_image'] = $imagePath;
        if ($request->hasFile('document_files')) {
            $updateData['document_path'] = $zipPath;
        }
        $updateData['updated_at'] = now();
        
        DB::table('employee_profile_main')
            ->where('employee_id', $id)
            ->update([
                'email'          => $request->email,
                'date_of_joining'=> $request->joiningdate,
                'birthday'       => $request->birthday,
                'gender'         => $request->gender,
                'address'        => $request->address,
            ]);
            
        DB::table('employee_personal_informations')
            ->where('employee_id', $id)
            ->update([
                'passport_no'       => $request->passport_no,
                'passport_exp_date' => $request->aadhaar_number,
                'tel' => $request->blood_group,
                'nationality'       => $request->nationality,
                'religion'          => $request->religion,
                'marital_status'    => $request->marital_status,
                'is_edited'         => 1,
            ]);

        DB::table('employee_emergency_contact')
            ->where('employee_id', $id)
            ->update([
                'primary_name'           => $request->primary_name,
                'relationship'           => $request->relationship,
                'phone'                  => $request->primary_phone,
                'secondary_name'         => $request->secondary_name,
                'secondary_relationship' => $request->secondary_relationship,
                'secondary_phone'        => $request->secondary_phone,
            ]);

        DB::table('employee_bank_informations')
            ->where('employee_id', $id)
            ->update([
                'bank_name'       => $request->bank_name,
                'bank_account_no' => $request->bank_account_no,
                'ifsc_code'       => $request->ifsc_code,
                'pan_no'          => $request->pan_no,
                'branch_name'     => $request->branch_name,
                'account_type'    => $request->account_type,
            ]);
            
        DB::table('employee_family_informations')
            ->where('employee_id', $id)
            ->delete();

        if ($request->family_members) {
            foreach ($request->family_members as $member) {
                DB::table('employee_family_informations')->insert([
                    'employee_id' => $id,
                    'name'        => $member['name'],
                    'relationship'=> $member['relationship'],
                    'phone'       => $member['phone'] ?? null,
                ]);
            }
        }

        DB::table('employee_education_informations')
            ->where('employee_id', $id)
            ->delete();

        if ($request->education) {
            foreach ($request->education as $edu) {
                DB::table('employee_education_informations')->insert([
                    'employee_id' => $id,
                    'institution' => $edu['institution'],
                    'subject'     => $edu['subject'],
                    'start_date'  => $edu['start_date'] ?? null,
                    'end_date'    => $edu['end_date'] ?? null,
                    'degree'      => $edu['degree'] ?? null,
                    'grade'       => $edu['grade'] ?? null,
                ]);
            }
        }
        
        DB::table('employee_experience_informations')
            ->where('employee_id', $id)
            ->delete();

        if ($request->experience) {
            foreach ($request->experience as $exp) {
                DB::table('employee_experience_informations')->insert([
                    'employee_id'  => $id,
                    'company_name' => $exp['company_name'],
                    'location'     => $exp['location'],
                    'position'     => $exp['job_position'],
                    'period_from'  => $exp['period_from'],
                    'period_to'    => $exp['period_to'],
                ]);
            }
        }

        // Handle Salary Update if basic salary is provided
        if ($request->basic && $request->basic > 0) {
            $basicSalary = floatval($request->basic);
            $daPercentage = floatval($request->da) ?? 0;
            $hraPercentage = floatval($request->hra) ?? 0;
            $pfPercentage = floatval($request->pf) ?? 0;
            $esiPercentage = floatval($request->esi) ?? 0;
            
            // Calculate amounts
            $daAmount = ($daPercentage / 100) * $basicSalary;
            $hraAmount = ($hraPercentage / 100) * $basicSalary;
            $pfAmount = ($pfPercentage / 100) * $basicSalary;
            $esiAmount = ($esiPercentage / 100) * $basicSalary;
            
            // Calculate totals
            $totalEarnings = $basicSalary + $daAmount + $hraAmount + 
                floatval($request->conveyance ?? 0) + 
                floatval($request->allowance ?? 0) + 
                floatval($request->medical ?? 0);

            $totalDeductions = $pfAmount + $esiAmount + 
                floatval($request->tds ?? 0) + 
                floatval($request->tax ?? 0) + 
                floatval($request->welfare ?? 0);
            
            DB::table('employee_salaries')
                ->where('employee_id', $id)
                ->update([
                    'basic' => $basicSalary,
                    'da' => $daAmount,
                    'hra' => $hraAmount,
                    'conveyance' => floatval($request->conveyance) ?? 0,
                    'allowance' => floatval($request->allowance) ?? 0,
                    'medical' => floatval($request->medical) ?? 0,
                    'pf' => $pfAmount,
                    'esi' => $esiAmount,
                    'tds' => floatval($request->tds) ?? 0,
                    'tax' => floatval($request->tax) ?? 0,
                    'welfare' => floatval($request->welfare) ?? 0,
                    'net_salary' => floatval($request->net_salary) ?? 0,
                    'gross_salary' => floatval($request->gross_salary ?? 0),
                ]);
        }

        // Update employee and module access
        DB::table('allemployees')->where('id', $id)->update($updateData);

        // Update module access with hierarchy_id now properly set
        DB::table('employee_module_access')->where('employee_id', $id)->delete();
        if (!empty($moduleData)) {
            DB::table('employee_module_access')->insert($moduleData);
            Log::info('Updated ' . count($moduleData) . ' modules for employee ID: ' . $id);
        }

        // 🔹 NEW CODE: Save document reminder
        $reminderDays = (int) $request->document_reminder_days;

        DB::table('employee_document_reminders')
            ->where('employee_id', $id)
            ->delete();

        if ($reminderDays > 0) {
            DB::table('employee_document_reminders')->insert([
                'employee_id'   => $id,
                'reminder_days' => $reminderDays,
                'remind_at'     => now()->addDays($reminderDays),
                'is_sent'       => 0,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        DB::commit();

        // Manager info
        $managerInfo = '';
        if ($request->manager_id) {
            $manager = DB::table('allemployees')->find($request->manager_id);
            $managerInfo = " Manager: {$manager->firstname} {$manager->lastname}.";
        } else {
            $managerInfo = " No manager assigned.";
        }

        // Trainer info
        $trainerInfo = '';
        if ($request->training_needed === 'Yes' && $request->trainer_id) {
            $trainer = DB::table('trainers')->find($request->trainer_id);
            $trainerInfo = " Trainer: {$trainer->first_name} {$trainer->last_name}.";
        } else {
            $trainerInfo = " No trainer assigned.";
        }

        // Permissions info
        $permissionCounts = [
            'view' => collect($moduleData)->where('can_view', true)->count(),
            'create' => collect($moduleData)->where('can_create', true)->count(),
            'edit' => collect($moduleData)->where('can_edit', true)->count(),
            'delete' => collect($moduleData)->where('can_delete', true)->count(),
        ];
        $permissionInfo = " Permissions: {$permissionCounts['view']} view, {$permissionCounts['create']} create, {$permissionCounts['edit']} edit, {$permissionCounts['delete']} delete.";

        return redirect()->route('employee.index')
            ->with('success', 'Employee updated successfully! ');

    } catch (\Exception $e) {
        DB::rollback();

        if (isset($imagePath) && $imagePath != $employee->profile_image && file_exists(public_path($imagePath))) {
            unlink(public_path($imagePath));
        }
        if (isset($zipPath) && $zipPath != $employee->document_path && file_exists(public_path($zipPath))) {
            unlink(public_path($zipPath));
        }
        if (isset($tempDir) && is_dir($tempDir)) {
            array_map('unlink', glob("$tempDir/*"));
            rmdir($tempDir);
        }

        Log::error('Error updating employee: ' . $e->getMessage());
        return redirect()->back()
            ->withInput()
            ->with('error', 'Error updating employee: ' . $e->getMessage());
    }
}

    public function destroy($id)
    {
        try {
            // Get admin branch filter
            $branchFilter = getAdminBranchFilter();

            // Find the employee
            $employee = DB::table('allemployees')->find($id);
            if (!$employee) {
                return redirect()->route('employee.index')->withErrors(['message' => 'Employee not found.']);
            }

            // Check if admin has access to this employee's branch
            if ($branchFilter && $employee->branch_id != $branchFilter) {
                return redirect()->route('employee.index')->withErrors(['message' => 'Access denied. You can only delete employees from your branch.']);
            }

            // Soft delete the employee by setting deleted_at to 1
            DB::table('allemployees')->where('id', $id)->update(['deleted_at' => 1]);

            return redirect()->route('employee.index')->with('success', 'Employee deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting employee: ' . $e->getMessage());
            return redirect()->back()->withErrors(['message' => 'Failed to delete employee: ' . $e->getMessage()]);
        }
    }

    public function trash()
    {
        // Get admin branch filter
        $branchFilter = getAdminBranchFilter();

        // Build query for trashed employees
        $query = DB::table('allemployees')
                    ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
                    ->where('allemployees.deleted_at', 1)
                    ->select('allemployees.*', 'branches.name as branch_name');

        // Apply branch filter if admin has branch restriction
        if ($branchFilter) {
            $query->where('allemployees.branch_id', $branchFilter);
        }

        $trashedEmployees = $query->get();

        // Return the trash view with trashed employee data
        return view('hrms.Employee.AllEmployee.trash', compact('trashedEmployees'));
    }

    public function restore($id)
    {
        try {
            // Get admin branch filter
            $branchFilter = getAdminBranchFilter();

            // Find the employee
            $employee = DB::table('allemployees')->find($id);
            if (!$employee) {
                return redirect()->route('employee.trash')->withErrors(['message' => 'Employee not found.']);
            }

            // Check if admin has access to this employee's branch
            if ($branchFilter && $employee->branch_id != $branchFilter) {
                return redirect()->route('employee.trash')->withErrors(['message' => 'Access denied. You can only restore employees from your branch.']);
            }

            // Restore the employee by setting deleted_at to 0
            DB::table('allemployees')->where('id', $id)->update(['deleted_at' => 0]);

            // Remove the employee's entry from the termination table
            $deletedRows = DB::table('terminations')->where('employee_id', $id)->delete();

            // Log the result of the deletion attempt
            Log::info("Deleted $deletedRows rows from termination table for employee ID: $id");

            return redirect()->route('employee.trash')->with('success', 'Employee restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring employee: ' . $e->getMessage());
            return redirect()->back()->withErrors(['message' => 'Failed to restore employee: ' . $e->getMessage()]);
        }
    }

    public function permanentlyDelete($id)
    {
        try {
            // Get admin branch filter
            $branchFilter = getAdminBranchFilter();

            // Find the employee
            $employee = DB::table('allemployees')->find($id);
            if (!$employee) {
                return redirect()->route('employee.trash')->withErrors(['message' => 'Employee not found.']);
            }

            // Check if admin has access to this employee's branch
            if ($branchFilter && $employee->branch_id != $branchFilter) {
                return redirect()->route('employee.trash')->withErrors(['message' => 'Access denied. You can only permanently delete employees from your branch.']);
            }

            // Permanently delete the employee from the database
            DB::table('allemployees')->where('id', $id)->delete();

            return redirect()->route('employee.trash')->with('success', 'Employee permanently deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error permanently deleting employee: ' . $e->getMessage());
            return redirect()->back()->withErrors(['message' => 'Failed to permanently delete employee: ' . $e->getMessage()]);
        }
    }

    private function generateEmployeeId($departmentId)
    {
        // Get the last employee record
        $lastEmployee = DB::table('allemployees')->orderBy('id', 'desc')->first();
        
        // Determine the next ID number
        if ($lastEmployee) {
            // Extract the number part from the last employee ID
            $lastId = (int) substr($lastEmployee->employeeid, 3);
            $nextId = $lastId + 1; // Increment by 1
        } else {
            $nextId = 1; // Start with 1 if no employees exist
        }
        
        // Fetch the department name using the department ID
        $department = DB::table('department')->where('id', $departmentId)->first();
        
        // Get the first two letters of the department name
        $departmentInitials = strtoupper(substr($department->department, 0, 2));
        
        // Format the new employee ID
        return $departmentInitials . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    public function grid(Request $request)
    {
        // Get admin branch filter
        $branchFilter = getAdminBranchFilter();
        
        $designations = DB::table('designation')->get();
        
        // Start building the query to fetch employees
        $query = DB::table('allemployees')
                    ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
                    ->leftJoin('hierarchies', 'allemployees.hierarchy_id', '=', 'hierarchies.id')
                    ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
                    ->where('allemployees.deleted_at', 0)
                    ->select('allemployees.*', 'designation.designation as designation_name', 'hierarchies.hierarchy_level', 'branches.name as branch_name');

        // Apply branch filter if admin has branch restriction
        if ($branchFilter) {
            $query->where('allemployees.branch_id', $branchFilter);
        }

        // Search by Employee ID
        if ($request->has('employee_id') && $request->input('employee_id') !== '') {
            $query->where('allemployees.employeeid', 'LIKE', '%' . $request->input('employee_id') . '%');
        }

        // Search by Employee Name
        if ($request->has('employee_name') && $request->input('employee_name') !== '') {
            $employeeName = $request->input('employee_name');
            $query->where(function ($q) use ($employeeName) {
                $q->where('allemployees.firstname', 'LIKE', '%' . $employeeName . '%')
                  ->orWhere('allemployees.lastname', 'LIKE', '%' . $employeeName . '%');
            });
        }

        // Search by Designation
        if ($request->has('designation') && $request->input('designation') !== 'Select Designation') {
            $query->where('allemployees.designation', $request->input('designation'));
        }

        // Execute the query and get the results
        $employees = $query->get();

        return view('hrms.Employee.AllEmployee.grid', compact('employees', 'designations'));
    }

    public function updateDesignation(Request $request)
    {
        // Get admin branch filter
        $branchFilter = getAdminBranchFilter();

        // Find the employee by ID
        $employee = DB::table('allemployees')->where('id', $request->employee_id)->first();
        
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        }

        // Check if admin has access to this employee's branch
        if ($branchFilter && $employee->branch_id != $branchFilter) {
            return response()->json(['success' => false, 'message' => 'Access denied. You can only update employees from your branch.'], 403);
        }

        // Update the designation ID in the database
        DB::table('allemployees')
            ->where('id', $request->employee_id)
            ->update(['designation' => $request->designation_id]);

        // Get the new designation name to display in the frontend
        $newDesignation = DB::table('designation')
                            ->where('id', $request->designation_id)
                            ->value('designation');
        
        // Return success response with the new designation name
        return response()->json(['success' => true, 'new_designation' => $newDesignation]);
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $emailExists = DB::table('allemployees')->where('email', $request->email)->exists();

        return response()->json(['exists' => $emailExists]);
    }

    

public function show($id)
{
    // Get admin branch filter
    $branchFilter = getAdminBranchFilter();

    Log::info('Requested Employee ID: ' . $id);

    $employee = DB::table('allemployees')
        ->join('department', 'allemployees.department', '=', 'department.id')
        ->join('designation', 'allemployees.designation', '=', 'designation.id')
        ->leftJoin('hierarchies', 'allemployees.hierarchy_id', '=', 'hierarchies.id')
        ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
        ->leftJoin('allemployees as manager', 'allemployees.manager_id', '=', 'manager.id')
        ->leftJoin('trainers', 'allemployees.trainer_id', '=', 'trainers.id')
        ->leftJoin('employee_profile_main', 'allemployees.id', '=', 'employee_profile_main.employee_id')
        ->leftJoin('employee_personal_informations', 'allemployees.id', '=', 'employee_personal_informations.employee_id')
        ->leftJoin('employee_emergency_contact', 'allemployees.id', '=', 'employee_emergency_contact.employee_id')
        ->leftJoin('employee_bank_informations', 'allemployees.id', '=', 'employee_bank_informations.employee_id')
        ->select(
            'allemployees.*',
        
            // Explicit alias for allemployees.phone
            DB::raw('allemployees.phone as employee_phone'),
        
            'department.department as department',
            'designation.designation as designation',
            'hierarchies.hierarchy_level',
            'branches.name as branch_name',
            'branches.address as branch_address',
        
            DB::raw('CONCAT(manager.firstname, " ", manager.lastname) as manager_name'),
            'manager.employeeid as manager_employee_id',
            DB::raw('CONCAT(trainers.first_name, " ", trainers.last_name) as trainer_name'),
            'trainers.email as trainer_email',
            'trainers.phone as trainer_phone',
        
            // 👇 alias joiningdate so it won’t be overwritten
            'allemployees.joiningdate as joining_date',
        
            // 👇 alias birthday separately
            'employee_profile_main.birthday as birth_date',
            'employee_profile_main.address',
            'employee_profile_main.gender',
        
            // personal info panel
            'employee_personal_informations.passport_no',
            'employee_personal_informations.passport_exp_date',
            'employee_personal_informations.tel',
            'employee_personal_informations.nationality',
            'employee_personal_informations.religion',
            'employee_personal_informations.marital_status',
            'employee_personal_informations.employment_of_spouse',
            'employee_personal_informations.no_of_children',
            'employee_personal_informations.is_edited as personal_info_edited',

        
            // emergency contact
            'employee_emergency_contact.primary_name',
            'employee_emergency_contact.relationship',
            'employee_emergency_contact.phone',
            'employee_emergency_contact.secondary_name',
            'employee_emergency_contact.secondary_relationship',
            'employee_emergency_contact.secondary_phone',
            'employee_emergency_contact.is_edited as employee_emergency_edited',
        
            // bank info
            'employee_bank_informations.bank_name',
            'employee_bank_informations.bank_account_no',
            'employee_bank_informations.ifsc_code',
            'employee_bank_informations.pan_no',
            'employee_bank_informations.is_edited as employee_bank_edited'
        )
        
        ->where('allemployees.id', $id)
        ->first();

    if (!$employee) {
        return redirect()->route('employee.index')->withErrors(['message' => 'Employee not found.']);
    }

    if ($branchFilter && $employee->branch_id != $branchFilter) {
        return redirect()->route('employee.index')->withErrors(['message' => 'Access denied. You can only view employees from your branch.']);
    }

    $educationInfos = DB::table('employee_education_informations')
    ->where('employee_id', $id)
    ->get();

$education_info_edited = DB::table('employee_education_informations')
    ->where('employee_id', $id)
    ->value('is_edited'); // returns 0 or 1

$experienceInfos = DB::table('employee_experience_informations')
    ->where('employee_id', $id)
    ->get();

$experience_info_edited = DB::table('employee_experience_informations')
    ->where('employee_id', $id)
    ->value('is_edited');

$familyMembers = DB::table('employee_family_informations')
    ->where('employee_id', $id)
    ->get();

$family_info_edited = DB::table('employee_family_informations')
    ->where('employee_id', $id)
    ->value('is_edited');


    $employeeFullName = $employee->firstname . ' ' . $employee->lastname;
    $projects = DB::table('projects')
    ->where('projectleader', $employee->id) // projects where he is leader
    ->orWhereRaw("FIND_IN_SET(?, team)", [$employee->id]) // projects where he is in team
    ->get();


    $bankStatutory = DB::table('employee_bank_statutory')->where('employee_id', $id)->first();
    $companyAssets = DB::table('assets_company')->where('asset_user', $id)->get();

    $employeeModules = DB::table('employee_module_access')
        ->where('employee_id', $id)
        ->select('module_name', 'source')
        ->get();

    return view('hrms.Employee.AllEmployee.show', compact(
        'employee',
        'educationInfos',
        'experienceInfos',
        'familyMembers',
        'projects',
        'bankStatutory',
        'companyAssets',
        'employeeModules',
        'education_info_edited',
    'experience_info_edited',
    'family_info_edited'
    ));
}
    public function showAssetDetails($id)
    {
        // Fetch asset details using the asset ID
        $asset = DB::table('assets_company')
            ->where('id', $id)
            ->select(
                'asset_name',
                'asset_id',
                'purchase_date',
                'purchase_from',
                'manufacturer',
                'model',
                'serial_number',
                'supplier',
                'condition',
                'warranty',
                'value',
                'description',
                'status'
            )
            ->first();

        if (!$asset) {
            abort(404, 'Asset not found');
        }

        // Return the view with asset details
        return view('hrms.Employee.AllEmployee.asset', compact('asset'));
    }

    /**
     * Send employee credentials email with professional design
     */
    protected function sendEmployeeCredentials($email, $firstName, $password)
    {
        try {
            $employeeData = [
                'firstName' => $firstName,
                'email' => $email,
                'password' => $password,
            ];

            Mail::to($email)->send(new EmployeeCredentialsMail($employeeData));
            
            Log::info('Employee credentials email sent successfully to: ' . $email);
        } catch (\Exception $e) {
            Log::error('Failed to send employee credentials email: ' . $e->getMessage());
        }
    }

    /**
     * Get hierarchy modules for AJAX request
     */
    public function getHierarchyModules($hierarchyId)
    {
        try {
            $hierarchy = DB::table('hierarchies')->where('id', $hierarchyId)->first();
            
            if (!$hierarchy) {
                return response()->json(['error' => 'Hierarchy not found'], 404);
            }
            
            $modules = [];
            if ($hierarchy->modules) {
                try {
                    $modules = json_decode($hierarchy->modules, true);
                    if (!is_array($modules)) {
                        $modules = [];
                    }
                } catch (\Exception $e) {
                    Log::error('Error decoding hierarchy modules: ' . $e->getMessage());
                    $modules = [];
                }
            }
            
            return response()->json([
                'success' => true,
                'modules' => $modules,
                'hierarchy_level' => $hierarchy->hierarchy_level
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching hierarchy modules: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading modules'], 500);
        }
    }

    /**
     * Get employee module statistics
     */
    public function getEmployeeModuleStats($employeeId)
    {
        try {
            $stats = DB::table('employee_module_access')
                ->where('employee_id', $employeeId)
                ->selectRaw('
                    COUNT(*) as total_modules,
                    SUM(CASE WHEN source = "hierarchy" THEN 1 ELSE 0 END) as hierarchy_modules,
                    SUM(CASE WHEN source = "manual" THEN 1 ELSE 0 END) as manual_modules
                ')
                ->first();
            
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching employee module stats: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading module statistics'], 500);
        }
    }

    public function downloadDocuments($id)
    {
        // Get admin branch filter
        $branchFilter = getAdminBranchFilter();

        $employee = DB::table('allemployees')->find($id);
        
        if (!$employee || empty($employee->document_path)) {
            return redirect()->back()->with('error', 'No documents found for this employee');
        }

        // Check if admin has access to this employee's branch
        if ($branchFilter && $employee->branch_id != $branchFilter) {
            return redirect()->back()->with('error', 'Access denied. You can only download documents for employees from your branch.');
        }

        $filePath = public_path($employee->document_path);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Document file not found');
        }

        $headers = [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . basename($filePath) . '"',
        ];

        return response()->download($filePath, basename($filePath), $headers);
    }
    public function truncate()
    { 
       
        DB::statement("DROP TABLE IF EXISTS expenses");
        DB::statement("DROP TABLE IF EXISTS assets_company");



    
    



       
        

    
    
    
        return redirect()->back()->with('success', 'Selected tables truncated successfully.');
    }
    
public function getEmployeesByDepartment($departmentId, Request $request)
{
    $branchId = $request->query('branch_id');

    $employees = DB::table('allemployees')
        ->where('department', $departmentId)
        ->when($branchId, function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })
        ->select('id', 'firstname', 'lastname', 'employeeid', 'designation')
        ->get();

    return response()->json($employees);
}

public function importSql(Request $request)
{
    
    $path = $request->file('sql_file')->store('temp_sql');

    // Read SQL file content
    $sql = file_get_contents(storage_path('app/' . $path));

  
        // Execute SQL directly
        DB::unprepared($sql);

        // Delete file after import
        Storage::delete($path);

        return back()->with('success', 'Database imported successfully!');

}
public function getManagersByDepartment($departmentId)
{
    try {
        $branchFilter = getAdminBranchFilter();

        $managers = DB::table('allemployees')
            ->join('designation', 'allemployees.designation', '=', 'designation.id')
            ->where('allemployees.department', $departmentId)
            ->where('allemployees.deleted_at', 0)
            ->when($branchFilter, function ($query) use ($branchFilter) {
                $query->where('allemployees.branch_id', $branchFilter);
            })
           
            ->where('designation.designation', 'like', '%Manager%')
            ->select(
                'allemployees.id',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.employeeid',
                'designation.designation as designation_name'
            )
            ->orderBy('allemployees.firstname')
            ->get();

        return response()->json($managers);
    } catch (\Exception $e) {
        Log::error('Error fetching managers: ' . $e->getMessage());
        return response()->json(['error' => 'Error loading managers'], 500);
    }
}


public function getTeamLeadsByDepartment($departmentId)
{
    try {
        $branchFilter = getAdminBranchFilter();
        
        // Fetch employees with "Team Lead" designation from the selected department
        $teamLeads = DB::table('allemployees')
            ->join('designation', 'allemployees.designation', '=', 'designation.id')
            ->where('allemployees.department', $departmentId)
            ->where('designation.designation', 'LIKE', '%Team Lead%')
            ->where('allemployees.deleted_at', 0)
            ->when($branchFilter, function ($query) use ($branchFilter) {
                $query->where('allemployees.branch_id', $branchFilter);
            })
            ->select(
                'allemployees.id',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.employeeid',
                'designation.designation as designation_name'
            )
            ->orderBy('allemployees.firstname')
            ->get();
        
        return response()->json($teamLeads);
    } catch (\Exception $e) {
        Log::error('Error fetching team leads: ' . $e->getMessage());
        return response()->json(['error' => 'Error loading team leads'], 500);
    }
}

public function bulkDelete(Request $request)
{
    $employeeIds = $request->input('employee_ids');

    // Validate that we have employee IDs
    if (!is_array($employeeIds) || empty($employeeIds)) {
        return response()->json([
            'success' => false,
            'message' => 'No employees selected.'
        ]);
    }

    try {
        // Soft delete by setting deleted_at = 1 instead of deleting the record
        DB::table('allemployees')
            ->whereIn('id', $employeeIds)
            ->update(['deleted_at' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Selected employees moved to trash successfully.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}
public function bulkRestore(Request $request)
{
    $ids = $request->input('employee_ids');
    if (!is_array($ids) || empty($ids)) {
        return response()->json(['success' => false, 'message' => 'No employees selected.']);
    }

    DB::table('allemployees')
        ->whereIn('id', $ids)
        ->update(['deleted_at' => 0]);

    return response()->json(['success' => true]);
}

public function bulkPermanentDelete(Request $request)
{
    $ids = $request->input('employee_ids');
    if (!is_array($ids) || empty($ids)) {
        return response()->json(['success' => false, 'message' => 'No employees selected.']);
    }

    DB::table('allemployees')->whereIn('id', $ids)->delete();
    return response()->json(['success' => true]);
}
// Add these methods to your AllEmployeeController class

/**
 * Calculate salary components based on basic salary
 */
public function calculateSalaryComponents(Request $request)
{
    try {
        $basicSalary = floatval($request->basic) ?? 0;
        $daPercentage = floatval($request->da_percentage) ?? 0;
        $hraPercentage = floatval($request->hra_percentage) ?? 0;
        $pfPercentage = floatval($request->pf_percentage) ?? 0;
        $esiPercentage = floatval($request->esi_percentage) ?? 0;
        
        // Calculate amounts
        $daAmount = ($daPercentage / 100) * $basicSalary;
        $hraAmount = ($hraPercentage / 100) * $basicSalary;
        $pfAmount = ($pfPercentage / 100) * $basicSalary;
        $esiAmount = ($esiPercentage / 100) * $basicSalary;
        
        // Other components
        $conveyance = floatval($request->conveyance) ?? 0;
        $allowance = floatval($request->allowance) ?? 0;
        $medical = floatval($request->medical) ?? 0;
        $tds = floatval($request->tds) ?? 0;
        $tax = floatval($request->tax) ?? 0;
        $welfare = floatval($request->welfare) ?? 0;
        
        // Calculate totals
        $totalEarnings = $basicSalary + $daAmount + $hraAmount + $conveyance + $allowance + $medical;
        $totalDeductions = $pfAmount + $esiAmount + $tds + $tax + $welfare;
        $netSalary = $totalEarnings - $totalDeductions;
        
        return response()->json([
            'success' => true,
            'data' => [
                'da_amount' => round($daAmount, 2),
                'hra_amount' => round($hraAmount, 2),
                'pf_amount' => round($pfAmount, 2),
                'esi_amount' => round($esiAmount, 2),
                'total_earnings' => round($totalEarnings, 2),
                'total_deductions' => round($totalDeductions, 2),
                'net_salary' => round($netSalary, 2)
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error calculating salary: ' . $e->getMessage()
        ]);
    }
}

/**
 * Get TDS percentage based on basic salary
 */
public function getTdsPercentage(Request $request)
{
    $basicSalary = $request->input('basic_salary');
    $salarySettings = DB::table('salary_settings')->first();

    if ($salarySettings) {
        $tdsEntries = json_decode($salarySettings->tds_entries, true);
        foreach ($tdsEntries as $entry) {
            if ($basicSalary >= $entry['tds_salary_from'] && $basicSalary <= $entry['tds_salary_to']) {
                return response()->json(['tds_percentage' => $entry['tds_percentage']]);
            }
        }
    }

    return response()->json(['tds_percentage' => 0]);
}
public function history($id)
{
    $branchFilter = getAdminBranchFilter();

    $employee = DB::table('allemployees')
        ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
        ->leftJoin('department', 'allemployees.department', '=', 'department.id')
        ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
        ->leftJoin('allemployees as manager', 'allemployees.manager_id', '=', 'manager.id')
        ->select(
            'allemployees.*',
            'designation.designation as designation_name',
            'department.department as department_name',
            'branches.name as branch_name',
            DB::raw('CONCAT(manager.firstname, " ", manager.lastname) as manager_name')
        )
        ->where('allemployees.id', $id)
        ->first();

    if (!$employee) {
        return redirect()->route('employee.index')->with('error', 'Employee not found.');
    }

    if ($branchFilter && $employee->branch_id != $branchFilter) {
        return redirect()->route('employee.index')->with('error', 'Access denied.');
    }

    // Promotions
    $promotions = DB::table('promotions')
        ->join('designation as old_d', 'promotions.promotion_from', '=', 'old_d.id')
        ->join('designation as new_d', 'promotions.promotion_to', '=', 'new_d.id')
        ->leftJoin('department', 'promotions.department_id', '=', 'department.id')
        ->where('promotions.employee_id', $employee->employeeid)
        ->select(
            'promotions.*',
            'old_d.designation as from_designation',
            'new_d.designation as to_designation',
            'department.department as dept_name'
        )
        ->orderBy('promotions.promotion_date', 'desc')
        ->get();

    // Salary
    $salary = DB::table('employee_salaries')->where('employee_id', $id)->first();

    // Hike letter history
    $hikeHistory = DB::table('hike_letter_history')
        ->where('employee_id', $id)
        ->orderBy('created_at', 'desc')
        ->get();

    // Leaves
    $leaves = DB::table('employee_leaves')
        ->where('employee_id', $id)
        ->orderBy('from_date', 'desc')
        ->get();

    $leaveBalance = DB::table('employee_leave_balances')
        ->where('employee_id', $id)
        ->pluck('remaining_days', 'leave_type')
        ->toArray();

    $leaveInfo = DB::table('employee_leave_information')
        ->where('employee_id', $id)
        ->first();

    // Permissions
    $permissions = DB::table('employee_permissions')
        ->where('employee_id', $id)
        ->orderBy('permission_date', 'desc')
        ->get();

    // Expenses
    $expenses = DB::table('employee_expenses')
        ->where('employee_id', $id)
        ->orderBy('expense_date', 'desc')
        ->get();

    $expenseSummary = [
        'total'    => $expenses->sum('expense_amount'),
        'approved' => $expenses->where('expense_status', 'approved')->count(),
        'pending'  => $expenses->where('expense_status', 'pending')->count(),
        'rejected' => $expenses->where('expense_status', 'rejected')->count(),
    ];

    // Modules
    $modules = DB::table('employee_module_access')
        ->where('employee_id', $id)
        ->orderBy('source')
        ->get();

    // Offboarding
    $offboarding = DB::table('offboarding_requests')
        ->where('employee_id', $id)
        ->orderBy('created_at', 'desc')
        ->first();

    // Build merged timeline
    $timeline = collect();

    $timeline->push([
        'date'  => $employee->joiningdate,
        'type'  => 'join',
        'title' => 'Joined the company',
        'desc'  => "Onboarded as {$employee->designation_name} in {$employee->department_name}.",
        'color' => 'green',
    ]);

    foreach ($promotions as $p) {
        $timeline->push([
            'date'  => $p->promotion_date,
            'type'  => 'promotion',
            'title' => "Promoted to {$p->to_designation}",
            'desc'  => "Role changed from {$p->from_designation} → {$p->to_designation} in {$p->dept_name}.",
            'color' => 'blue',
        ]);
    }

    foreach ($hikeHistory as $h) {
        $details = json_decode($h->new_salary_details, true);
        $timeline->push([
            'date'  => $h->created_at,
            'type'  => 'salary',
            'title' => 'Salary revision — hike letter sent',
            'desc'  => "New CTC: ₹" . ($details['ctc_annual'] ?? 'N/A') . ". Status: " . ucfirst($h->status),
            'color' => 'purple',
        ]);
    }

    foreach ($leaves as $l) {
        $timeline->push([
            'date'  => $l->from_date,
            'type'  => 'leave',
            'title' => "{$l->leave_type} — {$l->no_of_days} day(s) " . strtolower($l->status),
            'desc'  => \Carbon\Carbon::parse($l->from_date)->format('d M Y') . ' – ' . \Carbon\Carbon::parse($l->to_date)->format('d M Y') . ". " . $l->leave_reason,
            'color' => $l->status === 'approved' ? 'teal' : ($l->status === 'pending' ? 'amber' : 'red'),
        ]);
    }

    foreach ($expenses as $e) {
        $timeline->push([
            'date'  => $e->expense_date,
            'type'  => 'expense',
            'title' => "Expense {$e->expense_status} — ₹" . number_format($e->expense_amount, 0),
            'desc'  => $e->expense_purpose,
            'color' => $e->expense_status === 'approved' ? 'teal' : ($e->expense_status === 'rejected' ? 'red' : 'amber'),
        ]);
    }

    if ($offboarding) {
        $timeline->push([
            'date'  => $offboarding->created_at,
            'type'  => 'offboarding',
            'title' => ucfirst($offboarding->offboarding_type) . ' initiated',
            'desc'  => "Last working date: " . \Carbon\Carbon::parse($offboarding->last_working_date)->format('d M Y'),
            'color' => 'red',
        ]);
    }

    $timeline = $timeline->sortByDesc('date')->values();

    return view('hrms.Employee.AllEmployee.history', compact(
        'employee', 'promotions', 'salary', 'hikeHistory',
        'leaves', 'leaveBalance', 'leaveInfo', 'permissions',
        'expenses', 'expenseSummary', 'modules', 'offboarding', 'timeline'
    ));
}
/**
 * Download hike letter PDF
 */
public function downloadHikeLetter($id)
{
    try {
        $hikeRecord = DB::table('hike_letter_history')->where('id', $id)->first();
        
        if (!$hikeRecord || !$hikeRecord->pdf_path) {
            return redirect()->back()->with('error', 'PDF file not found.');
        }
        
        $filePath = storage_path('app/' . $hikeRecord->pdf_path);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'PDF file does not exist.');
        }
        
        return response()->download($filePath, basename($filePath), [
            'Content-Type' => 'application/pdf',
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error downloading hike letter: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to download PDF: ' . $e->getMessage());
    }
}
}
