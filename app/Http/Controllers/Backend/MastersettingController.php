<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

class MasterSettingController extends Controller
{
    public function index()
    {
    
        return view('hrms.master.master-settings.index');
    }
}
