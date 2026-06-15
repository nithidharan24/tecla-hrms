<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class SuperAdminController extends Controller
{// LOGIN PAGE

    // LOGIN PAGE

    public function login()
    {
        return view('super_admin.auth.login');
    }


    // LOGIN SUBMIT

    public function loginSubmit(Request $request)
    {
        $admin = DB::table('super_admins')
            ->where('email', $request->email)
            ->where('status', 'active')
            ->first();

        if ($admin && Hash::check($request->password, $admin->password)) {

            Session::put('super_admin_id', $admin->id);
            Session::put('super_admin_name', $admin->name);
            Session::put('super_admin_role', $admin->role);

            return redirect()->route('superadmin.dashboard');
        }

        return redirect()->back()->with('error', 'Invalid Credentials');
    }


    // DASHBOARD

    public function dashboard()
    {
        if (!Session::has('super_admin_id')) {
            return redirect()->route('superadmin.login');
        }

        $totalAdmins = DB::table('super_admins')->count();

        return view('super_admin.dashboard.dashboard', compact('totalAdmins'));
    }


    // LOGOUT

    public function logout()
    {
        Session::flush();

        return redirect()->route('superadmin.login');
    }
     // INDEX PAGE

    public function index()
    {
        if (!Session::has('super_admin_id')) {
            return redirect()->route('superadmin.login');
        }

        $admins = DB::table('super_admins')
            ->orderBy('id', 'desc')
            ->get();

        return view('super_admin.admins.index', compact('admins'));
    }



    public function create()
    {
        if (!Session::has('super_admin_id')) {
            return redirect()->route('superadmin.login');
        }

        return view('super_admin.admins.create');
    }


    // STORE

    public function store(Request $request)
    {
        DB::table('super_admins')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('superadmin.admins.index')
            ->with('success', 'Admin Created Successfully');
    }


    // EDIT PAGE

    public function edit($id)
    {
        $admin = DB::table('super_admins')
            ->where('id', $id)
            ->first();

        return view('super_admin.admins.edit', compact('admin'));
    }


    // UPDATE

    public function update(Request $request, $id)
    {
        DB::table('super_admins')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'status' => $request->status,
                'updated_at' => now(),
            ]);

        return redirect()->route('superadmin.admins.index')
            ->with('success', 'Admin Updated Successfully');
    }


    // DELETE

    public function delete($id)
    {
        DB::table('super_admins')
            ->where('id', $id)
            ->delete();

        return redirect()->back()
            ->with('success', 'Admin Deleted Successfully');
    }

}