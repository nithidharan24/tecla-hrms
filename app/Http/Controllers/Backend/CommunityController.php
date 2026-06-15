<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CommunityController extends Controller
{
    /**
     * Get current authenticated user based on role
     */



    /**
     * Get current user ID for wishes
     */

    /**
     * Display community dashboard with separated birthday and anniversary sections
     */
 

    /**
     * Get departments - Fixed to handle missing deleted_at column
     */
   

    /**
     * Get employees with birthdays today
     */
  

    /**
     * Get employees with work anniversaries today
     */
 
    /**
     * Get upcoming birthdays in next 30 days
     */
  

    /**
     * Get recent wishes with sender and receiver details
     */
  
    /**
     * Get wish statistics for HR analytics
     */
  

    /**
     * Get recent achievements and milestones
     */
    
    /**
     * Get user achievements including personal achievements
     */
    private function getAchievements()
    {
       $achievements = collect();
    
    // Personal Achievements (user-submitted)
    $personalAchievements = DB::table('personal_achievements')
        ->leftJoin('allemployees', 'personal_achievements.employee_id', '=', 'allemployees.id')
        ->leftJoin('admin_access', 'personal_achievements.admin_id', '=', 'admin_access.id')
        ->leftJoin('department', 'allemployees.department', '=', 'department.id')
        ->where('personal_achievements.status', 'approved')
        ->where('personal_achievements.created_at', '>=', Carbon::now()->subDays(30))
        ->select(
            'personal_achievements.*',
            'allemployees.id as employee_id',
            'allemployees.firstname',
            'allemployees.lastname',
            'allemployees.profile_image',
            'admin_access.id as admin_id',
            'admin_access.name as admin_name',
            'department.department as department_name'
        )
        ->orderBy('personal_achievements.created_at', 'desc')
        ->get()
        ->map(function($achievement) {
            // Determine if this is an admin achievement
            $isAdminAchievement = !empty($achievement->admin_id);
            
            return (object)[
                'id' => $achievement->id,
                'type' => $isAdminAchievement ? 'admin' : 'personal',
                'title' => "🎓 " . $achievement->title,
                'description' => $achievement->description,
                'employee_name' => $isAdminAchievement 
                    ? $achievement->admin_name 
                    : "{$achievement->firstname} {$achievement->lastname}",
                'employee_image' => $isAdminAchievement 
                    ? asset('assets/img/admin-avatar.png') // Default admin image
                    : $achievement->profile_image,
                'department' => $isAdminAchievement ? 'Administration' : $achievement->department_name,
                'date' => $achievement->created_at,
                'icon' => $achievement->icon ?: '🎓',
                'color' => 'success',
                'achievement_image' => $achievement->achievement_image,
                'category' => $achievement->category,
                'employee_id' => $isAdminAchievement ? null : $achievement->employee_id,
                'admin_id' => $isAdminAchievement ? $achievement->admin_id : null
            ];
        });
        
    $achievements = $achievements->merge($personalAchievements);
        // Work Anniversary Achievements (5, 10, 15, 20+ years)
        $anniversaryAchievements = DB::table('allemployees')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->where('allemployees.deleted_at', 0)
            ->whereNotNull('allemployees.joiningdate')
            ->select(
                'allemployees.id',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.employeeid',
                'allemployees.profile_image',
                'allemployees.joiningdate',
                'department.department as department_name',
                'designation.designation as designation_name',
                DB::raw('FLOOR(DATEDIFF(CURDATE(), allemployees.joiningdate) / 365) as years_completed')
            )
            ->havingRaw('years_completed IN (5, 10, 15, 20, 25, 30)')
            ->whereRaw('DATE_FORMAT(allemployees.joiningdate, "%m-%d") >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 30 DAY), "%m-%d")')
            ->whereRaw('DATE_FORMAT(allemployees.joiningdate, "%m-%d") <= DATE_FORMAT(CURDATE(), "%m-%d")')
            ->orderBy('allemployees.joiningdate', 'desc')
            ->get()
            ->map(function($employee) {
                return (object)[
                    'id' => $employee->id,
                    'type' => 'milestone',
                    'title' => "🏆 {$employee->years_completed} Years of Excellence",
                    'description' => "{$employee->firstname} {$employee->lastname} completed {$employee->years_completed} years with us!",
                    'employee_name' => "{$employee->firstname} {$employee->lastname}",
                    'employee_image' => $employee->profile_image,
                    'department' => $employee->department_name,
                    'designation' => $employee->designation_name,
                    'date' => $employee->joiningdate,
                    'icon' => '🏆',
                    'color' => 'warning',
                    'achievement_image' => null,
                    'category' => 'work_milestone'
                ];
            });

        $achievements = $achievements->merge($anniversaryAchievements);

        // New Joiners (within last 30 days)
        $newJoiners = DB::table('allemployees')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->where('allemployees.deleted_at', 0)
            ->where('allemployees.joiningdate', '>=', Carbon::now()->subDays(30))
            ->select(
                'allemployees.id',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.profile_image',
                'allemployees.joiningdate',
                'department.department as department_name',
                'designation.designation as designation_name'
            )
            ->orderBy('allemployees.joiningdate', 'desc')
            ->get()
            ->map(function($employee) {
                return (object)[
                    'id' => $employee->id,
                    'type' => 'new_joiner',
                    'title' => "👋 Welcome to the Team",
                    'description' => "Welcome {$employee->firstname} {$employee->lastname} who joined us as {$employee->designation_name}",
                    'employee_name' => "{$employee->firstname} {$employee->lastname}",
                    'employee_image' => $employee->profile_image,
                    'department' => $employee->department_name,
                    'designation' => $employee->designation_name,
                    'date' => $employee->joiningdate,
                    'icon' => '👋',
                    'color' => 'info',
                    'achievement_image' => null,
                    'category' => 'new_joiner'
                ];
            });

        $achievements = $achievements->merge($newJoiners);

        return $achievements->sortByDesc('date')->take(15);
    }

    /**
     * Add personal achievement
     */
  public function addAchievement(Request $request)
{
    try {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'category' => 'required|string|in:education,certification,skill,project,award,other',
            'achievement_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'icon' => 'nullable|string|max:10'
        ]);

        $currentUser = $this->getCurrentUser();
        $currentUserId = $this->getCurrentUserId();
        $role = Session::get('role');
        
        if (!$currentUserId) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to add achievements'
            ], 401);
        }

        $imagePath = null;
        if ($request->hasFile('achievement_image')) {
            $image = $request->file('achievement_image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('achievements', $imageName, 'public');
        }

        // Set default icon based on category
        $defaultIcons = [
            'education' => '🎓',
            'certification' => '📜',
            'skill' => '💪',
            'project' => '🚀',
            'award' => '🏆',
            'other' => '⭐'
        ];

        $icon = $request->icon ?: $defaultIcons[$request->category];

        // Prepare achievement data
        $achievementData = [
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'icon' => $icon,
            'achievement_image' => $imagePath,
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now()
        ];

        // Store the appropriate ID based on role
        if ($role === 'admin') {
            $achievementData['admin_id'] = $currentUserId;
            $achievementData['employee_id'] = null; // Explicitly set to null
        } else {
            $achievementData['employee_id'] = $currentUserId;
            $achievementData['admin_id'] = null; // Explicitly set to null
        }

        $achievementId = DB::table('personal_achievements')->insertGetId($achievementData);

        return response()->json([
            'success' => true,
            'message' => 'Achievement added successfully!',
            'achievement_id' => $achievementId
        ]);

    } catch (\Exception $e) {
        Log::error('Error adding achievement: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error adding achievement. Please try again.'
        ], 500);
    }
}
    /**
     * Get user's personal achievements
     */
    public function getUserAchievements($userId = null)
    {
        try {
            $userId = $userId ?: $this->getCurrentUserId();
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $achievements = DB::table('personal_achievements')
                ->where('employee_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'achievements' => $achievements
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching user achievements: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading achievements'
            ]);
        }
    }

    /**
     * Congratulate on achievement
     */
    public function congratulateAchievement(Request $request)
    {
        try {
            $request->validate([
                'achievement_id' => 'required|integer',
                'message' => 'nullable|string|max:500'
            ]);

            $currentUserId = $this->getCurrentUserId();
            
            if (!$currentUserId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to congratulate'
                ], 401);
            }

            $achievementId = $request->achievement_id;
            $message = $request->message ?: 'Congratulations on your achievement!';

            // Check if achievement exists
            $achievement = DB::table('personal_achievements')->where('id', $achievementId)->first();
            if (!$achievement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Achievement not found'
                ], 404);
            }

            // Check if user already congratulated
            $existingCongrats = DB::table('achievement_congratulations')
                ->where('achievement_id', $achievementId)
                ->where('sender_id', $currentUserId)
                ->first();

            if ($existingCongrats) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already congratulated on this achievement!'
                ], 400);
            }

            // Insert congratulation
            DB::table('achievement_congratulations')->insert([
                'achievement_id' => $achievementId,
                'sender_id' => $currentUserId,
                'receiver_id' => $achievement->employee_id,
                'message' => $message,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Congratulations sent successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending congratulations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error sending congratulations. Please try again.'
            ], 500);
        }
    }

    /**
     * Send birthday or anniversary wish
     */
  

    /**
     * Get wishes for an employee
     */
   

    /**
     * Debug session data
     */
    public function debugSession()
    {
        $sessionData = [
            'role' => Session::get('role'),
            'user_id' => Session::get('user_id'),
            'admin_id' => Session::get('admin_id'),
            'email' => Session::get('email'),
            'first_name' => Session::get('first_name'),
            'last_name' => Session::get('last_name'),
            'employee_id' => Session::get('employee_id'),
            'all_session' => Session::all()
        ];
        
        $dbCheck = null;
        if (Session::get('role') === 'employee') {
            $email = Session::get('email');
            $userId = Session::get('user_id');
            $employeeId = Session::get('employee_id');
            
            $dbCheck = [
                'by_user_id' => $userId ? DB::table('allemployees')->where('id', $userId)->where('deleted_at', 0)->first() : null,
                'by_email' => $email ? DB::table('allemployees')->where('email', $email)->where('deleted_at', 0)->first() : null,
                'by_employee_id' => $employeeId ? DB::table('allemployees')->where('employeeid', $employeeId)->where('deleted_at', 0)->first() : null,
            ];
        }
        
        return response()->json([
            'session_data' => $sessionData,
            'database_check' => $dbCheck,
            'current_user' => $this->getCurrentUser(),
            'current_user_id' => $this->getCurrentUserId()
        ]);
    }

   private function getCurrentUser()
    {
        $role = Session::get('role');
        
        if ($role === 'admin') {
            $adminId = Session::get('admin_id');
            return $adminId ? DB::table('admin_access')->where('id', $adminId)->first() : null;
        }
        
        if ($role === 'employee') {
            $userId = Session::get('user_id');
            $email = Session::get('email');
            $employeeId = Session::get('employee_id');
            
            if ($userId) {
                $user = DB::table('allemployees')->where('id', $userId)->where('deleted_at', 0)->first();
                if ($user) return $user;
            }
            
            if ($email) {
                $user = DB::table('allemployees')->where('email', $email)->where('deleted_at', 0)->first();
                if ($user) {
                    Session::put('user_id', $user->id);
                    return $user;
                }
            }
            
            if ($employeeId) {
                $user = DB::table('allemployees')->where('employeeid', $employeeId)->where('deleted_at', 0)->first();
                if ($user) {
                    Session::put('user_id', $user->id);
                    return $user;
                }
            }
        }
        
        return null;
    }

    private function getCurrentUserId()
    {
        $role = Session::get('role');
        
        if ($role === 'admin') {
            return Session::get('admin_id');
        }
        
        if ($role === 'employee') {
            $user = $this->getCurrentUser();
            return $user ? $user->id : null;
        }
        
        return null;
    }

    public function index()
    {
        try {
            $currentUser = $this->getCurrentUser();
            $currentUserId = $this->getCurrentUserId();
        
            if (!$currentUser || !$currentUserId) {
                return view('hrms.community.index', [
                    'birthdayEmployees' => collect(),
                    'anniversaryEmployees' => collect(),
                    'upcomingBirthdays' => collect(),
                    'recentWishes' => collect(),
                    'currentUser' => null,
                    'currentUserId' => null,
                    'departments' => collect(),
                    'wishStats' => [],
                    'communityMessages' => collect()
                ]);
            }

            // Get UNIQUE employees with birthdays today
            $birthdayEmployees = $this->getBirthdayEmployees($currentUserId);
            
            // Get UNIQUE employees with anniversaries today
            $anniversaryEmployees = $this->getAnniversaryEmployees($currentUserId);
            
            $upcomingBirthdays = $this->getUpcomingBirthdays();
            $recentWishes = $this->getRecentWishes(50);
            $departments = $this->getDepartments();
            $wishStats = $this->getWishStatistics();
            $communityMessages = $this->getCommunityMessages();
        
            return view('hrms.community.index', compact(
                'birthdayEmployees',
                'anniversaryEmployees',
                'upcomingBirthdays',
                'recentWishes',
                'currentUser',
                'currentUserId',
                'departments',
                'wishStats',
                'communityMessages'
            ));
        
        } catch (\Exception $e) {
            Log::error('Error loading community dashboard: ' . $e->getMessage());
            return view('hrms.community.index', [
                'birthdayEmployees' => collect(),
                'anniversaryEmployees' => collect(),
                'upcomingBirthdays' => collect(),
                'recentWishes' => collect(),
                'currentUser' => null,
                'currentUserId' => null,
                'departments' => collect(),
                'wishStats' => [],
                'communityMessages' => collect()
            ]);
        }
    }

    /**
     * FIXED: Get UNIQUE employees with birthdays today - No duplicates
     */
    private function getBirthdayEmployees($currentUserId)
    {
        $today = Carbon::now();
        
        // Get employees with birthdays today - ENSURE UNIQUENESS
        $employees = DB::table('allemployees')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->leftJoin('employee_profile_main', 'allemployees.id', '=', 'employee_profile_main.employee_id')
            ->where('allemployees.deleted_at', 0)
            ->whereNotNull('employee_profile_main.birthday')
            ->whereRaw('DATE_FORMAT(employee_profile_main.birthday, "%m-%d") = ?', [$today->format('m-d')])
            ->select(
                'allemployees.id',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.employeeid',
                'allemployees.profile_image',
                'allemployees.joiningdate',
                'department.department as department_name',
                'designation.designation as designation_name',
                'employee_profile_main.birthday'
            )
            ->distinct() // Ensure uniqueness
            ->orderBy('allemployees.firstname')
            ->get()
            ->unique('id'); // Double ensure uniqueness by ID

        // Add wish counts and status for each unique employee
        return $employees->map(function($employee) use ($currentUserId) {
            // Count total wishes received today
            $employee->birthday_wish_count = DB::table('birthday_wishes')
                ->where('employee_id', $employee->id)
                ->where('wish_type', 'birthday')
                ->whereDate('created_at', Carbon::today())
                ->count();
            
            // Check if current user already wished this person today
            $employee->already_wished = DB::table('birthday_wishes')
                ->where('employee_id', $employee->id)
                ->where('sender_id', $currentUserId)
                ->where('wish_type', 'birthday')
                ->whereDate('created_at', Carbon::today())
                ->exists();
                
            return $employee;
        })->values(); // Reset array keys
    }

    /**
     * FIXED: Get UNIQUE employees with work anniversaries today - No duplicates
     */
    private function getAnniversaryEmployees($currentUserId)
    {
        $today = Carbon::now();
        $oneYearAgo = $today->copy()->subYear();
        
        // Get employees with anniversaries today - ENSURE UNIQUENESS
        $employees = DB::table('allemployees')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->where('allemployees.deleted_at', 0)
            ->whereNotNull('allemployees.joiningdate')
            ->where('allemployees.joiningdate', '<=', $oneYearAgo)
            ->whereRaw('DATE_FORMAT(allemployees.joiningdate, "%m-%d") = ?', [$today->format('m-d')])
            ->select(
                'allemployees.id',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.employeeid',
                'allemployees.profile_image',
                'allemployees.joiningdate',
                'department.department as department_name',
                'designation.designation as designation_name',
                DB::raw('FLOOR(DATEDIFF(CURDATE(), allemployees.joiningdate) / 365) as years_completed')
            )
            ->distinct() // Ensure uniqueness
            ->orderBy('allemployees.firstname')
            ->get()
            ->unique('id'); // Double ensure uniqueness by ID

        return $employees->map(function($employee) use ($currentUserId) {
            // Count total wishes received today
            $employee->anniversary_wish_count = DB::table('birthday_wishes')
                ->where('employee_id', $employee->id)
                ->where('wish_type', 'anniversary')
                ->whereDate('created_at', Carbon::today())
                ->count();
            
            // Check if current user already wished this person today
            $employee->already_wished = DB::table('birthday_wishes')
                ->where('employee_id', $employee->id)
                ->where('sender_id', $currentUserId)
                ->where('wish_type', 'anniversary')
                ->whereDate('created_at', Carbon::today())
                ->exists();
                
            return $employee;
        })->values(); // Reset array keys
    }

    /**
     * Send birthday or anniversary wish - FIXED
     */
    public function sendWish(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required|integer',
                'message' => 'required|string|max:500',
                'wish_type' => 'required|in:birthday,anniversary'
            ]);

            $currentUserId = $this->getCurrentUserId();
            
            if (!$currentUserId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to send wishes'
                ], 401);
            }
            
            $employeeId = $request->employee_id;
            $wishType = $request->wish_type;
            $message = $request->message;
            
            // Check if target employee exists
            $targetEmployee = DB::table('allemployees')
                ->where('id', $employeeId)
                ->where('deleted_at', 0)
                ->first();
                
            if (!$targetEmployee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ], 404);
            }
            
            // Check if user already sent wish today for this type
            $existingWish = DB::table('birthday_wishes')
                ->where('employee_id', $employeeId)
                ->where('sender_id', $currentUserId)
                ->where('wish_type', $wishType)
                ->whereDate('created_at', Carbon::today())
                ->first();
                
            if ($existingWish) {
                $typeText = $wishType === 'birthday' ? 'birthday wish' : 'anniversary wish';
                return response()->json([
                    'success' => false,
                    'message' => "You have already sent a {$typeText} to this person today!"
                ], 400);
            }

            // Insert wish
            $wishId = DB::table('birthday_wishes')->insertGetId([
                'employee_id' => $employeeId,
                'sender_id' => $currentUserId,
                'message' => $message,
                'wish_type' => $wishType,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => ucfirst($wishType) . ' wish sent successfully!',
                'wish_id' => $wishId
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending wish: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error sending wish. Please try again.'
            ], 500);
        }
    }

    /**
     * FIXED: Get wishes for an employee - Show wishes to recipient only
     */
    public function getWishes($employeeId, $wishType = 'birthday')
    {
        try {
            $currentUserId = $this->getCurrentUserId();
            
            // Convert string to integer for comparison
            $employeeId = (int) $employeeId;
            $currentUserId = (int) $currentUserId;
            
            Log::info("Getting wishes for employee {$employeeId}, current user: {$currentUserId}, wish type: {$wishType}");
            
            // Only allow the recipient to view their own wishes
            if ($currentUserId !== $employeeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only view your own wishes'
                ], 403);
            }

            $wishes = DB::table('birthday_wishes')
                ->join('allemployees', 'birthday_wishes.sender_id', '=', 'allemployees.id')
                ->leftJoin('department', 'allemployees.department', '=', 'department.id')
                ->where('birthday_wishes.employee_id', $employeeId)
                ->where('birthday_wishes.wish_type', $wishType)
                ->whereDate('birthday_wishes.created_at', Carbon::today())
                ->select(
                    'birthday_wishes.message',
                    'birthday_wishes.created_at',
                    'allemployees.firstname',
                    'allemployees.lastname',
                    'allemployees.profile_image',
                    'department.department as department_name'
                )
                ->orderBy('birthday_wishes.created_at', 'desc')
                ->get();

            Log::info("Found " . $wishes->count() . " wishes for employee {$employeeId}");

            return response()->json([
                'success' => true,
                'wishes' => $wishes,
                'count' => $wishes->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching wishes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading wishes'
            ], 500);
        }
    }

    /**
     * Add community message
     */
    public function addCommunityMessage(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
                'category' => 'required|string|in:announcement,celebration,milestone,news,other',
                'achievement_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'icon' => 'nullable|string|max:10'
            ]);

            $currentUser = $this->getCurrentUser();
            $currentUserId = $this->getCurrentUserId();
            $role = Session::get('role');
            
            if (!$currentUserId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to add community messages'
                ], 401);
            }

            $imagePath = null;
            if ($request->hasFile('achievement_image')) {
                $image = $request->file('achievement_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('community_messages', $imageName, 'public');
            }

            // Set default icon based on category
            $defaultIcons = [
                'announcement' => '📢',
                'celebration' => '🎉',
                'milestone' => '🏆',
                'news' => '📰',
                'other' => '💬'
            ];

            $icon = $request->icon ?: $defaultIcons[$request->category];

            // Prepare message data
            $messageData = [
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'icon' => $icon,
                'achievement_image' => $imagePath,
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Store the appropriate ID based on role
            if ($role === 'admin') {
                $messageData['admin_id'] = $currentUserId;
                $messageData['employee_id'] = null;
            } else {
                $messageData['employee_id'] = $currentUserId;
                $messageData['admin_id'] = null;
            }

            $messageId = DB::table('personal_achievements')->insertGetId($messageData);

            return response()->json([
                'success' => true,
                'message' => 'Community message added successfully!',
                'message_id' => $messageId
            ]);

        } catch (\Exception $e) {
            Log::error('Error adding community message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error adding community message. Please try again.'
            ], 500);
        }
    }

    // Keep other existing methods...
    private function getCommunityMessages()
    {
        $messages = collect();
    
        // Personal Messages (user-submitted)
        $personalMessages = DB::table('personal_achievements')
            ->leftJoin('allemployees', 'personal_achievements.employee_id', '=', 'allemployees.id')
            ->leftJoin('admin_access', 'personal_achievements.admin_id', '=', 'admin_access.id')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->where('personal_achievements.status', 'approved')
            ->where('personal_achievements.created_at', '>=', Carbon::now()->subDays(30))
            ->select(
                'personal_achievements.*',
                'allemployees.id as employee_id',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.profile_image',
                'admin_access.id as admin_id',
                'admin_access.name as admin_name',
                'department.department as department_name'
            )
            ->orderBy('personal_achievements.created_at', 'desc')
            ->get()
            ->map(function($message) {
                $isAdminMessage = !empty($message->admin_id);
                
                return (object)[
                    'id' => $message->id,
                    'type' => $isAdminMessage ? 'admin' : 'personal',
                    'title' => $message->title,
                    'description' => $message->description,
                    'employee_name' => $isAdminMessage 
                        ? $message->admin_name 
                        : "{$message->firstname} {$message->lastname}",
                    'employee_image' => $isAdminMessage 
                        ? asset('assets/img/admin-avatar.png')
                        : $message->profile_image,
                    'department' => $isAdminMessage ? 'Administration' : $message->department_name,
                    'date' => $message->created_at,
                    'icon' => $message->icon ?: '💬',
                    'color' => 'success',
                    'achievement_image' => $message->achievement_image,
                    'category' => $message->category,
                    'employee_id' => $isAdminMessage ? null : $message->employee_id,
                    'admin_id' => $isAdminMessage ? $message->admin_id : null
                ];
            });
            
        $messages = $messages->merge($personalMessages);

        return $messages->sortByDesc('date')->take(15);
    }

    private function getDepartments()
    {
        $columns = DB::getSchemaBuilder()->getColumnListing('department');
        
        if (in_array('deleted_at', $columns)) {
            return DB::table('department')->where('deleted_at', 0)->get();
        } else {
            return DB::table('department')->get();
        }
    }

    private function getUpcomingBirthdays()
    {
        $today = Carbon::now();
        $thirtyDaysLater = Carbon::now()->addDays(30);
        
        return DB::table('allemployees')
            ->leftJoin('employee_profile_main', 'allemployees.id', '=', 'employee_profile_main.employee_id')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->where('allemployees.deleted_at', 0)
            ->whereNotNull('employee_profile_main.birthday')
            ->select(
                'allemployees.id',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.profile_image',
                'employee_profile_main.birthday',
                'department.department as department_name'
            )
            ->distinct()
            ->get()
            ->unique('id')
            ->filter(function($employee) use ($today, $thirtyDaysLater) {
                if (!$employee->birthday) return false;
                
                $birthday = Carbon::parse($employee->birthday);
                $thisYearBirthday = Carbon::createFromFormat('Y-m-d', $today->year . '-' . $birthday->format('m-d'));
                
                if ($thisYearBirthday->isPast()) {
                    $thisYearBirthday->addYear();
                }
                
                return $thisYearBirthday->between($today->addDay(), $thirtyDaysLater);
            })
            ->map(function($employee) {
                $birthday = Carbon::parse($employee->birthday);
                $thisYearBirthday = Carbon::createFromFormat('Y-m-d', Carbon::now()->year . '-' . $birthday->format('m-d'));
                
                if ($thisYearBirthday->isPast()) {
                    $thisYearBirthday->addYear();
                }
                
                $employee->days_until = Carbon::now()->diffInDays($thisYearBirthday);
                $employee->birthday_date = $thisYearBirthday->format('M d');
                
                return $employee;
            })
            ->sortBy('days_until')
            ->values();
    }

    private function getRecentWishes($limit = 50)
    {
        $currentUserId = $this->getCurrentUserId();
        
        return DB::table('birthday_wishes')
            ->join('allemployees as sender', 'birthday_wishes.sender_id', '=', 'sender.id')
            ->join('allemployees as receiver', 'birthday_wishes.employee_id', '=', 'receiver.id')
            ->leftJoin('department as sender_dept', 'sender.department', '=', 'sender_dept.id')
            ->leftJoin('department as receiver_dept', 'receiver.department', '=', 'receiver_dept.id')
            ->select(
                'birthday_wishes.id',
                'birthday_wishes.message',
                'birthday_wishes.created_at',
                'birthday_wishes.wish_type',
                'sender.id as sender_id',
                'sender.firstname as sender_firstname',
                'sender.lastname as sender_lastname',
                'sender.profile_image as sender_image',
                'sender_dept.department as sender_department',
                'receiver.id as receiver_id',
                'receiver.firstname as receiver_firstname',
                'receiver.lastname as receiver_lastname',
                'receiver.profile_image as receiver_image',
                'receiver_dept.department as receiver_department'
            )
            ->where('birthday_wishes.created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('birthday_wishes.created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($wish) use ($currentUserId) {
                $wish->is_my_wish = $wish->sender_id == $currentUserId;
                $wish->is_for_me = $wish->receiver_id == $currentUserId;
                $wish->time_ago = Carbon::parse($wish->created_at)->diffForHumans();
                return $wish;
            });
    }

    private function getWishStatistics()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        return [
            'today_wishes' => DB::table('birthday_wishes')->whereDate('created_at', $today)->count(),
            'this_month_wishes' => DB::table('birthday_wishes')->where('created_at', '>=', $thisMonth)->count(),
            'total_wishes' => DB::table('birthday_wishes')->count(),
            'active_wishers' => DB::table('birthday_wishes')
                ->where('created_at', '>=', $thisMonth)
                ->distinct('sender_id')
                ->count()
        ];
    }
}
