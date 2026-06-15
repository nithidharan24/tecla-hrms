<?php

namespace App\Http\Controllers\Backend\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Mail\Follow\SendMail;
use Illuminate\Support\Str;
use App\Mail\Auth\OtpMail;


class UserController extends Controller
{
    // Display a listing of users
    public function index(Request $request)
    {
        // Initial query to select user fields
        $query = DB::table('users')
        ->where('users.role', '!=', 'master_admin')
        ->select(
            'users.email',
            'users.user_id',
            DB::raw("DATE_FORMAT(users.created_at, '%d:%m:%y') as created_date"),
            'users.role',
            DB::raw("CONCAT(users.first_name, ' ', users.last_name) as user_name")
        );
    


        if ($request->filled('name')) {
            $query->where(DB::raw("CONCAT(users.first_name, ' ', users.last_name)"), 'like', '%' . $request->name . '%');
        }


        if ($request->filled('role')) {
            $query->where('users.role', $request->role);
        }

        $users = $query->get();

        $roles = DB::table('roles')
            ->where('role_name', '!=', 'master_admin')
            ->distinct()
            ->pluck('role_name');

    
        // Pass the search parameters back to the view to retain them in the form
        return view('hrms.Administration.Users.index', compact('users', 'roles'));
    }
    

    // Show the form for creating a new user
    public function create()
    {

        $permissions = explode(',', env('PERMISSIONS'));
        $roles = DB::table('roles')
        ->where('role_name', '!=', 'master_admin')
        ->distinct()
        ->pluck('role_name');


        return view('hrms.Administration.Users.create',compact('permissions','roles'));
    }

    // Store a newly created user in storage
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'fname' => 'required|min:1|max:255',
            'lname' => 'required|min:1|max:255',
            'creator' => 'required|string|in:master_admin',
            'phone' => 'required|numeric',
            'role' => 'required',
            'password' => 'required|min:6|same:confirm_password',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'array|min:1',     
        ], [
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'This email is already registered.',
            'fname.required' => 'The first name field is required.',
            'fname.min' => 'The first name must be at least 1 character.',
            'fname.max' => 'The first name may not be greater than 255 characters.',
            'lname.required' => 'The last name field is required.',
            'lname.min' => 'The last name must be at least 1 character.',
            'lname.max' => 'The last name may not be greater than 255 characters.',
            'phone.required' => 'The phone field is required.',
            'phone.numeric' => 'The phone field must be a valid number.',
            'phone.digits' => 'The phone number must be exactly 11 digits.',
            'creator.required' => 'Invalid Access',
            'creator.in' => 'Invalid Access',
            'company.unique' => 'This company name is already taken.',
            'role.required' => 'The role field is required.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 6 characters.',
            'password.same' => 'The password confirmation does not match.',
            'permissions.required' => 'At least one permission must be selected.',
            'permissions.*.min' => 'At least one permission must be selected.',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        if($request->input('creator')=='master_admin'){
            $otp = rand(100000, 999999);
            $expiresAt = now()->addMinutes(3);

            session(['otp' => $otp, 'otp_expires_at' => $expiresAt]);

            Mail::to($request->input('email'))->send(new OtpMail($otp));

            return response()->json([
                'message' => 'OTP has been sent successfully.',
                'otp' => $otp,
                'status' => 200,
            ]);
        }
    }

    public function submitData(Request $request){
        $request->validate([
            'email' => 'required|email',
            'fname' => 'required|string',
            'lname' => 'required|string',
            'phone' => 'required|string',
            'creator' => 'required|string',
            'role' => 'required|string',
            'password' => 'required|min:6',
            'otp' => 'required|string|size:6',
            'permissions' => 'required|array',
        ]);

        $inputOtp = $request->input('otp');
        $sessionOtp = session('otp');
        $expiresAt = session('otp_expires_at');

        try {
            if ($inputOtp == $sessionOtp && now()->lessThanOrEqualTo($expiresAt)) {

                if ($request->input('creator') == 'master_admin' ) {
                    $permissionsArray = $request->input('permissions', []);
                    $permissionsString = json_encode($permissionsArray);
                    
                    $userId = $this->generateUserId('USR');

                    DB::table('users')->insert([
                        'user_id' => $userId,
                        'first_name' => $request->input('fname'),
                        'last_name' => $request->input('lname'),
                        'email' => $request->input('email'),
                        'phone' => $request->input('phone'),
                        'role' => $request->input('role'),
                        'permissions' => $permissionsString, 
                        'password' => Hash::make($request->input('password')),
                    ]);

                    $subject = "Welcome to Our Service!";
                    $body = "Thank you for signing up, " . $request->input('email') . ". We are glad to have you!\nYour Login Password is: " . $request->input('password');

                    Mail::to($request->input('email'))->send(new SendMail($subject, $body));

                    session()->forget(['otp', 'otp_expires_at']);
                    
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed.'
                    ], 500);
                }          
                
            }  else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'OTP is invalid or expired.'
                ], 400);
            }

            return response()->json([
                'message' => 'User data submitted successfully!',
                'data' => $request->all()
            ], 200);
        

        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    
    }

    // Show the form for editing the specified user
    public function edit($id)
{
        $permissions = explode(',', env('PERMISSIONS'));

        $roles = DB::table('roles')
        ->where('role_name', '!=', 'master_admin')
        ->distinct()
        ->pluck('role_name');

        $userDetails = DB::table('users')
            ->where('users.user_id', $id)
            ->where('users.role', '!=', 'master_admin')
            ->select(
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone',
                'users.role',
                'users.permissions',
                'users.user_id',
            )
            ->first();

        $userPermissions = json_decode($userDetails->permissions, true);

        $subject = "Security Alert!";
        $body = "It appears that you are attempting to edit the details.";        

        Mail::to($userDetails->email)->send(new SendMail($subject, $body));
        
        return view('hrms.Administration.Users.edit',compact('permissions','roles','userDetails','userPermissions'));

}


public function update(Request $request, $id)
{
    $validatedData = $request->validate([
        'fname' => 'required|string|max:255',
        'lname' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id . ',user_id',
        'phone' => 'required|string|max:15',
        'role' => 'required|string',
        'permissions' => 'required|array',
    ]);
    
    DB::beginTransaction();
    
    try {
        DB::table('users')->where('user_id', $id)->update([
            'first_name' => $validatedData['fname'],
            'last_name' => $validatedData['lname'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'role' => $validatedData['role'],
            'permissions' => json_encode($validatedData['permissions']),
            'updated_at' => now(),
        ]);
    
        DB::commit();

        $subject = "Your Details Updated!";
        $body = "Details Updated Successfully!";

        Mail::to($validatedData['email'])->send(new SendMail($subject, $body));
    
        Session::flash('messageType', 'success');
        Session::flash('message', 'Update Success!');
        return redirect()->route('users.index');
    
    } catch (\Exception $e) {
        DB::rollBack();
        Session::flash('messageType', 'error');
        Session::flash('message', 'An unexpected error occurred. Please try again later.');
        return redirect()->back();
    }
    
}


    // Remove the specified user from storage
    public function destroy($id)
    {
        $userExists = DB::table('users')->where('user_id', $id)->exists();

        if (!$userExists) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $deleted = DB::table('users')->where('user_id', $id)->delete();

        return $deleted
            ? response()->json(['message' => 'User deleted successfully'], 200)
            : response()->json(['message' => 'User not found'], 404);
        
    }

    public function sendDetailToEmail($id){

        $user = DB::table('users')->where('user_id', $id)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
    
        $temporaryPassword = Str::random(8);
        $hashedPassword = bcrypt($temporaryPassword);
    
        DB::table('users')->where('user_id', $id)->update(['password' => $hashedPassword]);
    
        $email = $user->email;
        $subject = "Temporary Password";
        $body = "Your temporary password is: $temporaryPassword";
    
        Mail::to($email)->send(new SendMail($subject, $body));
    
        return response()->json(['message' => 'Temporary password sent successfully!'], 200);

    }

    private function generateUserID($base)
{
    $baseID = $base;
    $lastID = DB::table('users')
        ->whereNotNull('user_id')
        ->where('user_id', 'like', $baseID . '%')
        ->orderBy('id', 'desc')
        ->value('user_id');

    $newIDNumber = $lastID ? (int) substr($lastID, 3) + 1 : 1;

    do {
        $newUserID = $baseID . str_pad($newIDNumber, 10, '0', STR_PAD_LEFT);
        $newIDNumber++;
    } while (DB::table('users')->where('user_id', $newUserID)->exists());

    return $newUserID;
}

    public function showRoles(){

        $roles = DB::table('roles')->where('role_name', '!=', 'master_admin')
        ->distinct()
        ->get();
        
        return view('hrms.Administration.Roles.index', compact('roles'));
    }

    public function addRoles(Request $request){
        $request->validate([
            'roleName' => 'required|string|max:255|unique:roles,role_name',
        ], [
            'roleName.required' => 'The role name is required.',
            'roleName.string' => 'The role name must be a valid string.',
            'roleName.max' => 'The role name may not exceed 255 characters.',
            'roleName.unique' => 'This role name already exists.',
        ]);

        $request->merge([
            'roleName' => strtolower(str_replace(' ', '_', $request->roleName)),
        ]);

        $res = DB::table('roles')->insert([
            'role_name' => $request->input('roleName')
        ]);

        Session::flash('messageType', $res ? 'success' : 'error');
        Session::flash('message', $res ? 'Created Successfully!' : 'An unexpected error occurred. Please try again later.');

        return redirect()->route('show-user-role');
    }

    public function getData($id){
        $role = DB::table('roles')->where('id', $id)->first()->role_name;

        return response()->json([
            'role' => ucwords(str_replace('_', ' ', $role))
        ]);

    }

    public function updateData(Request $request, $id){
        $request->validate([
            'edit_roleName' => 'required|string|max:255|unique:roles,role_name,' . $id,
        ]);

        $roleName = strtolower(str_replace(' ', '_', $request->input('edit_roleName')));
        
        DB::table('roles')->where('id', $id)->update(['role_name' => $roleName]);

        Session::flash('messageType', 'success');
        Session::flash('message', 'Updated successfully!');

        return redirect()->route('show-user-role');
    }

    public function updateStatus(Request $request){
        $id = $request->input('id');
        $status = $request->input('status');
        $result = DB::table('roles')->where('id', $id)->update(['status' => $status]);
        
        if ($result) {
            return response()->json(['status' => 1]);
        }

        return response()->json(['status' => 0, 'message' => 'Failed to change status.']);
    }

    public function deleteData($id){
        DB::table('roles')->where('id', $id)->delete();

        return response()->json(['id' => $id]);
        
    }
}