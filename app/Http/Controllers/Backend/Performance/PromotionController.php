<?php

namespace App\Http\Controllers\Backend\Performance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PromotionMail;
use App\Models\PromotionLetterTemplate; // Import the PromotionLetterTemplate model
use Illuminate\Support\Facades\Blade; // Import Blade facade for rendering
use PDF; // Import the PDF facade
use Illuminate\Support\Facades\Session;
class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promotions = DB::table('promotions')
            ->join('allemployees', 'promotions.employee_id', '=', 'allemployees.employeeid')
            ->join('department', 'promotions.department_id', '=', 'department.id')
            ->join('designation as from_designation', 'promotions.promotion_from', '=', 'from_designation.id')
            ->join('designation as to_designation', 'promotions.promotion_to', '=', 'to_designation.id')
            ->select(
                'promotions.id',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.employeeid',
                'department.department as department_name',
                'from_designation.designation as promotion_from',
                'to_designation.designation as promotion_to',
                'promotions.promotion_date',
                'promotions.created_at',
                'promotions.updated_at'
            )
            ->where('allemployees.deleted_at', 0)
            ->where('allemployees.status', 'active')
            ->orderBy('promotions.created_at', 'desc')
            ->get();
        return view('hrms.Promotions.index', compact('promotions'));
    }

    public function create(Request $request)
    {
        $employees = DB::table('allemployees')
            ->join('designation', 'allemployees.designation', '=', 'designation.id')
            ->join('department', 'allemployees.department', '=', 'department.id')
            ->where('allemployees.deleted_at', 0)
            ->where('allemployees.status', 'active')
            ->select(
                'allemployees.*',
                'designation.designation as designation_name',
                'department.department as department_name'
            )
            ->get();

        $selectedEmployee = null;
        if ($request->has('employee_id')) {
            $selectedEmployee = DB::table('allemployees')
                ->where('employeeid', $request->employee_id)
                ->first();
        }

        return view('hrms.Promotions.create', compact('employees', 'selectedEmployee'));
    }

    public function getEmployeeDetails(Request $request)
    {
        $employee = DB::table('allemployees')
            ->join('designation', 'allemployees.designation', '=', 'designation.id')
            ->join('department', 'allemployees.department', '=', 'department.id')
            ->where('allemployees.employeeid', $request->employee_id)
            ->select(
                'designation.id as current_designation_id',
                'designation.designation as current_designation',
                'department.department as department_name',
                'department.id as department_id'
            )
            ->first();
        if ($employee) {
            return response()->json([
                'success' => true,
                'current_designation_id' => $employee->current_designation_id,
                'current_designation' => $employee->current_designation,
                'department_name' => $employee->department_name,
                'department_id' => $employee->department_id
            ]);
        }

        return response()->json(['success' => false]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:allemployees,employeeid',
            'department_id' => 'required|exists:department,id',
            'current_designation_id' => 'required|exists:designation,id',
            'promotion_to' => 'required|exists:designation,id|different:current_designation_id',
            'promotion_date' => 'required|date',
        ]);

        // Get employee details
        $employee = DB::table('allemployees')
            ->where('employeeid', $validatedData['employee_id'])
            ->first();

        // Check for duplicate promotion
        $existingPromotion = DB::table('promotions')
            ->where('employee_id', $validatedData['employee_id'])
            ->where('promotion_from', $validatedData['current_designation_id'])
            ->where('promotion_to', $validatedData['promotion_to'])
            ->first();

        if ($existingPromotion) {
            return redirect()->back()
                ->with('error', 'This promotion already exists for the selected employee.')
                ->withInput();
        }

        // Create the promotion record
        $promotionId = DB::table('promotions')->insertGetId([
            'employee_id' => $validatedData['employee_id'],
            'department_id' => $validatedData['department_id'],
            'promotion_from' => $validatedData['current_designation_id'],
            'promotion_to' => $validatedData['promotion_to'],
            'promotion_date' => $validatedData['promotion_date'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Get the newly created promotion record
        $promotion = DB::table('promotions')->where('id', $promotionId)->first();

        // Get old and new designation names for the email and letter
        $oldDesignation = DB::table('designation')->where('id', $validatedData['current_designation_id'])->first();
        $newDesignation = DB::table('designation')->where('id', $validatedData['promotion_to'])->first();

        // Update employee's designation
        DB::table('allemployees')
            ->where('employeeid', $validatedData['employee_id'])
            ->update([
                'designation' => $validatedData['promotion_to'],
                'updated_at' => now()
            ]);

        $promotionLetterPdfContent = null;
        // Generate Promotion Letter PDF
        $promotionLetterTemplate = PromotionLetterTemplate::latest()->first(); // Get the latest template

        if ($promotionLetterTemplate) {
            // Data to pass to the Blade template
            $data = [
                'employee' => $employee,
                'promotion' => $promotion,
                'oldDesignationName' => $oldDesignation->designation ?? 'N/A',
                'newDesignationName' => $newDesignation->designation ?? 'N/A',
            ];

            // Render the Blade content from the database
            $html = Blade::render($promotionLetterTemplate->content, $data);

            // Generate PDF
            $pdf = PDF::loadHTML($html);
            $promotionLetterPdfContent = $pdf->output();
        }

        // Send promotion email with PDF attachment
        if ($employee && $employee->email) {
            Mail::to($employee->email)->send(new PromotionMail(
                $employee,
                $promotion,
                $newDesignation->designation ?? 'N/A',
                $oldDesignation->designation ?? 'N/A',
                $promotionLetterPdfContent // Pass the PDF content
            ));
        }

        return redirect()->route('employee.index')
            ->with('success', 'Promotion added successfully and employee designation updated. Promotion email and letter sent.');
    }

    public function getDesignationsByDepartment($department_id)
    {
        $designations = DB::table('designation')
            ->where('department_id', $department_id)
            ->select('id', 'designation')
            ->orderBy('designation')
            ->get();

        return response()->json($designations);
    }

    /**
     * Show the form for editing the specified resource.
     */
   public function edit($id)
{
    $promotion = DB::table('promotions')
        ->join('allemployees', 'promotions.employee_id', '=', 'allemployees.employeeid')
        ->where('promotions.id', $id)
        ->select(
            'promotions.*',
            'allemployees.firstname',
            'allemployees.lastname',
            'allemployees.employeeid'
        )
        ->first();
        
    $employees = DB::table('allemployees')
        ->where('deleted_at', 0)
        ->where('status', 'active')
        ->get();
        
    $departments = DB::table('department')->get();
    $designations = DB::table('designation')->get();
    
    return view('hrms.Promotions.edit', compact('promotion', 'employees', 'departments', 'designations'));
}

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, $id)
{
    $validatedData = $request->validate([
        'employee_id' => 'required|exists:allemployees,employeeid',
        'department_id' => 'required|exists:department,id',
        'promotion_from' => 'required|exists:designation,id',
        'promotion_to' => 'required|exists:designation,id|different:promotion_from',
        'promotion_date' => 'required|date',
    ]);

    DB::table('promotions')
        ->where('id', $id)
        ->update([
            'employee_id' => $validatedData['employee_id'],
            'department_id' => $validatedData['department_id'],
            'promotion_from' => $validatedData['promotion_from'],
            'promotion_to' => $validatedData['promotion_to'],
            'promotion_date' => $validatedData['promotion_date'],
            'updated_at' => now(),
        ]);

    return redirect()->route('promotion.index')->with('success', 'Promotion updated successfully.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::table('promotions')->where('id', $id)->delete();
        return redirect()->route('promotion.index')->with('success', 'Promotion deleted successfully.');
    }

    /**
     * Get employee's current designation
     */
    public function getDesignationByEmployeeId(Request $request)
    {
        $employee = DB::table('allemployees')
            ->join('designation', 'allemployees.designation', '=', 'designation.id')
            ->where('allemployees.employeeid', $request->employee_id)
            ->select('designation.designation', 'allemployees.department')
            ->first();
        if ($employee) {
            return response()->json([
                'designation' => $employee->designation,
                'department_id' => $employee->department
            ]);
        }

        return response()->json(['designation' => 'No designation found'], 404);
    }
}