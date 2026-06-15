<?php
namespace App\Http\Controllers\Backend\Settings;

use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SalarySettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
{
    // Fetch existing settings
    $salarySettings = DB::table('salary_settings')->first();

    // Decode TDS entries to array if it exists
    if ($salarySettings && isset($salarySettings->tds_entries)) {
        $salarySettings->tds_entries = json_decode($salarySettings->tds_entries, true) ?? [];
    } else {
        $salarySettings->tds_entries = []; // Ensure it’s an empty array if none exists
    }

    return view('hrms.Settings.SalarySettings.create', compact('salarySettings'));
}

public function update(Request $request)
{
    // Validate inputs
    $request->validate([
        'da_percentage' => 'required|numeric',
        'hra_percentage' => 'required|numeric',
        'pf_employee_share' => 'required|numeric',
        'pf_organization_share' => 'required|numeric',
        'esi_employee_share' => 'required|numeric',
        'esi_organization_share' => 'required|numeric',
        'tds_salary_from.*' => 'required|numeric',
        'tds_salary_to.*' => 'required|numeric',
        'tds_percentage.*' => 'required|numeric',
    ]);

    // Prepare TDS entries as an array of arrays
    $tdsEntries = [];
    foreach ($request->tds_salary_from as $index => $from) {
        $tdsEntries[] = [
            'tds_salary_from' => $from,
            'tds_salary_to' => $request->tds_salary_to[$index],
            'tds_percentage' => $request->tds_percentage[$index],
        ];
    }

    // Update the salary settings with JSON-encoded TDS entries
    DB::table('salary_settings')->update([
        'da_percentage' => $request->da_percentage,
        'hra_percentage' => $request->hra_percentage,
        'pf_employee_share' => $request->pf_employee_share,
        'pf_organization_share' => $request->pf_organization_share,
        'esi_employee_share' => $request->esi_employee_share,
        'esi_organization_share' => $request->esi_organization_share,
        'tds_entries' => json_encode($tdsEntries), // Store as JSON
    ]);

    return redirect()->route('salary-settings.edit')->with('success', 'Salary settings updated successfully.');
}

public function deleteTdsEntry($index)
{
    // Fetch current salary settings
    $salarySettings = DB::table('salary_settings')->first();

    // Decode the TDS entries
    $tdsEntries = json_decode($salarySettings->tds_entries, true);

    // Remove the entry by its index
    if (isset($tdsEntries[$index])) {
        unset($tdsEntries[$index]);
    }

    // Update the salary settings with the new TDS entries
    DB::table('salary_settings')->update([
        'tds_entries' => json_encode(array_values($tdsEntries)), // Store as JSON
    ]);

    return response()->json(['success' => true]);
}


}
