<?php

namespace App\Http\Controllers\Backend\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SalaryMasterController extends Controller
{
    /**
     * Display salary master configuration page
     */
    public function index()
{
    $salaryConfig = DB::table('salary_master_config')->first();

    if (!$salaryConfig) {
        DB::table('salary_master_config')->insert([
            'gross_to_basic_percentage' => 50,
            'da_percentage'             => 10,
            'hra_percentage'            => 15,
            'conveyance'                => 0,
            'special_allowance'         => 0,
            'medical_allowance'         => 0,
            'pf_percentage'             => 12,
            'esi_percentage'            => 0.75,
            'professional_tax'          => 0,
            'welfare_fund'              => 0,
            'tds'                       => 0,
            'created_at'                => now(),
            'updated_at'                => now(),
        ]);
        $salaryConfig = DB::table('salary_master_config')->first();
    }

    return view('hrms.salarymaster.index', compact('salaryConfig'));
}

public function update(Request $request)
{
    $request->validate([
        'gross_to_basic_percentage' => 'required|numeric|min:1|max:100',
        'da_percentage'             => 'required|numeric|min:0|max:100',
        'hra_percentage'            => 'required|numeric|min:0|max:100',
        'conveyance'                => 'required|numeric|min:0',
        'special_allowance'         => 'required|numeric|min:0',
        'medical_allowance'         => 'required|numeric|min:0',
        'pf_percentage'             => 'required|numeric|min:0|max:100',
        'esi_percentage'            => 'required|numeric|min:0|max:100',
        'professional_tax'          => 'required|numeric|min:0',
        'welfare_fund'              => 'required|numeric|min:0',
        'tds'                       => 'required|numeric|min:0',
    ]);

    DB::table('salary_master_config')->update([
        'gross_to_basic_percentage' => $request->gross_to_basic_percentage,
        'da_percentage'             => $request->da_percentage,
        'hra_percentage'            => $request->hra_percentage,
        'conveyance'                => $request->conveyance,
        'special_allowance'         => $request->special_allowance,
        'medical_allowance'         => $request->medical_allowance,
        'pf_percentage'             => $request->pf_percentage,
        'esi_percentage'            => $request->esi_percentage,
        'professional_tax'          => $request->professional_tax,
        'welfare_fund'              => $request->welfare_fund,
        'tds'                       => $request->tds,
        'updated_at'                => now(),
    ]);

    return redirect()->back()->with('success', 'Salary Master Configuration updated successfully!');
}

public function getConfig()
{
    $config = DB::table('salary_master_config')->first();

    if (!$config) {
        return response()->json([
            'gross_to_basic_percentage' => 50,
            'da_percentage'             => 0,
            'hra_percentage'            => 0,
            'conveyance'                => 0,
            'special_allowance'         => 0,
            'medical_allowance'         => 0,
            'pf_percentage'             => 0,
            'esi_percentage'            => 0,
            'professional_tax'          => 0,
            'welfare_fund'              => 0,
            'tds'                       => 0,
        ]);
    }

    return response()->json($config);
}
}