<?php

use App\Http\Controllers\Backend\Employee\AdminLeavesController;
use App\Http\Controllers\Backend\Employee\AllEmployeeController;
use App\Http\Controllers\Backend\Employee\CustomPolicyController;
use App\Http\Controllers\Backend\Employee\EmployeeExpenseController;
use App\Http\Controllers\Backend\Employee\EmployeeLeavesController;
use App\Http\Controllers\Backend\Employee\LeavesSettingsController;
use App\Http\Controllers\Backend\Hr\EmployeeSalaryController;
use App\Http\Controllers\Backend\Hr\PayrollItemsController;
use App\Http\Controllers\Backend\Hr\PolicyController;
use App\Http\Controllers\Backend\Pages\EmployeeProfileController;
use App\Http\Controllers\Backend\Performance\TerminationController;
use App\Http\Controllers\Backend\Tickets\TicketsController;
use Illuminate\Support\Facades\Route;
/** **/

use App\Http\Controllers\Backend\Performance\TrainerController;
use App\Http\Controllers\Backend\Performance\TrainingManagementController;
use App\Http\Controllers\Backend\Performance\TrainingTypeController;
use App\Http\Controllers\Backend\Administration\UserController;
use App\Http\Controllers\Backend\Attendance\OvertimeController;
use App\Http\Controllers\Backend\worklog\WorklogController;
use App\Http\Controllers\Backend\master\BranchController;
/** **/
use App\Http\Controllers\Backend\Employee\HolidaysController;
use App\Http\Controllers\Backend\Employee\TimeTrackerController;

/** **/
use App\Http\Controllers\Backend\Pages\PrivacypolicyController;
use App\Http\Controllers\Backend\Pages\TermsController;
use App\Http\Controllers\Backend\Performance\PromotionController;
use App\Http\Controllers\Backend\Project\ProjectController;
use App\Http\Controllers\Backend\Project\TaskboardController;
use App\Http\Controllers\Backend\Project\TaskController;
use App\Http\Controllers\Backend\Project\ProjectDetailController;

use App\Http\Controllers\Backend\Subscription\SuscribecompanyController;
use App\Http\Controllers\Backend\Subscription\SuscribeController;
use App\Http\Controllers\Backend\Subscription\SuscribetableController;
use App\Http\Controllers\Backend\Pages\FaqController;
use App\Http\Controllers\Backend\Pages\SearchController;
use App\Http\Controllers\Backend\Administration\KnowledgebaseController;
use App\Http\Controllers\Backend\Jobs\ManagejobsController;
use App\Http\Controllers\Backend\Jobs\ExperienceController;
use App\Http\Controllers\Backend\Jobs\CandidateController;
use App\Http\Controllers\Backend\Jobs\sheduleController;
use App\Http\Controllers\Backend\Jobs\ShortlistController;
use App\Http\Controllers\Backend\Jobs\AddresumeController;
use App\Http\Controllers\Backend\Jobs\QuestionController;
use App\Http\Controllers\Backend\Jobs\ManageresumeController;

/** **/
use App\Http\Controllers\Backend\Hr\EstimateController;
use App\Http\Controllers\Backend\Hr\ExpenseController;
use App\Http\Controllers\Backend\Hr\InvoiceController;
use App\Http\Controllers\Backend\Hr\PFTypeController;
use App\Http\Controllers\Backend\Hr\ProvidentFundController;
use App\Http\Controllers\Backend\Hr\TaxController;
use App\Http\Controllers\Backend\Performance\GoalController;
use App\Http\Controllers\Backend\Admin\ClientController;

/** Auth Controller **/
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Masters\MasterController;

/** Shift , Schedule , Resignation & Timesheet **/
use App\Http\Controllers\Backend\Administration\AssetsController;

use App\Http\Controllers\Backend\Employee\SchedulingController;
use App\Http\Controllers\Backend\Employee\ShiftController;
use App\Http\Controllers\Backend\Employee\TimesheetController;

/** Performance_Indicator , Performance_Appraisal & Performance_review **/
use App\Http\Controllers\Backend\Performance\Performance_IndicatorController;
use App\Http\Controllers\Backend\Performance\Performance_AppraisalController;
use App\Http\Controllers\Backend\Performance\Performance_review\ReviewController;

/** **/
use App\Http\Controllers\Backend\Employee\DepartmentController;
use App\Http\Controllers\Backend\Employee\DesignationController;
use App\Http\Controllers\Backend\Hr\BudgetexpensesController;
use App\Http\Controllers\Backend\Hr\BudgetrevenueController;
use App\Http\Controllers\Backend\Hr\PaymentController;
use App\Http\Controllers\Backend\Hr\BudgetsController;
use App\Http\Controllers\Backend\Hr\CategoriesController;
use App\Http\Controllers\Backend\Settings\SalarySettingsController;
use App\Http\Controllers\Backend\Reports\NewProjectController;
use App\Http\Controllers\Backend\Settings\LeaveTypeController;

/** **/
use App\Http\Controllers\Backend\Reports\ExpenseReportController;
use App\Http\Controllers\Backend\Reports\InterviewProcessController;
use App\Http\Controllers\Backend\Reports\PaymentReportController;
use App\Http\Controllers\Backend\Reports\ProjectReportController;
use App\Http\Controllers\Backend\Reports\TestingReportController;
use App\Http\Controllers\Backend\Reports\AssetReportController;
use App\Http\Controllers\Backend\Reports\PoliciesReportController;
use App\Http\Controllers\Backend\Reports\AccountsReportController;
use App\Http\Controllers\Backend\Reports\UserReportController;
use App\Http\Controllers\Backend\Reports\TicketReportsController;
use App\Http\Controllers\Backend\Reports\PayslipReportController;
use App\Http\Controllers\Backend\Reports\TaskReportController;
use App\Http\Controllers\Backend\Reports\AttendanceReportController;
use App\Http\Controllers\Backend\Reports\TimesheetEmployeeReportController;

use App\Http\Controllers\Backend\Reports\LeaveReportController;
use App\Http\Controllers\Backend\Reports\EmployeeReportController;
use App\Http\Controllers\Backend\Reports\InvoiceReportController;
use App\Http\Controllers\Backend\Reports\DailyReportController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\HrDashboardController;

use App\Http\Controllers\Backend\TeamLeaderController;
use App\Http\Controllers\Backend\ManagerController;
use App\Http\Controllers\Backend\ManagerDashboardController;


use App\Http\Controllers\Backend\EmployeeDashboardController;
use App\Http\Controllers\Backend\Attendance\AttendanceController;
use App\Http\Controllers\Backend\Attendance\AdminAttendanceController;
use App\Http\Controllers\Backend\HierarchyController;
use App\Http\Controllers\Backend\CareerController;
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\CommunityController;

 use App\Http\Controllers\Employee\EmployeeTrainingDashboardController;
 use App\Http\Controllers\Backend\master\ServiceController;
 use App\Http\Controllers\Backend\master\AdminaccessController;
 use App\Http\Controllers\Backend\master\OfferletterController;
 use App\Http\Controllers\Backend\master\PromotionLetterController;
 use App\Http\Controllers\Backend\master\TerminationLetterController;
 use App\Http\Controllers\Backend\master\OnboardingController;
 use App\Http\Controllers\Backend\master\ResignationController;
 use App\Http\Controllers\Backend\master\FeedbackformController;

use App\Http\Controllers\Backend\master\CustomizesiteController;
use App\Http\Controllers\Backend\Employee\TestingController;
use App\Http\Controllers\Backend\Employee\TeamLeaveController;

use App\Http\Controllers\Backend\Reports\ClientreportController;

use App\Http\Controllers\Backend\Hr\AutomatedPayslipController;

use App\Http\Controllers\Backend\Reports\EmployeeSalaryreportController;
use App\Http\Controllers\TestCheckinController;
use App\Http\Controllers\Backend\master\HikeLetterController;
use App\Http\Controllers\Backend\master\AppointmentLetterController;
use App\Http\Controllers\Backend\master\MemoController;
use App\Http\Controllers\InvoiceTemplateController;
use App\Http\Controllers\PayrollTemplateController;  
use App\Http\Controllers\Backend\Jobs\BackgroundVerificationController;
use App\Http\Controllers\Backend\Performance\ClearanceController;
use App\Http\Controllers\Backend\master\StatutoryController;

use App\Http\Controllers\Backend\master\StatutoryChallanController;
use App\Http\Controllers\Backend\master\TdsController;
use App\Http\Controllers\Backend\Offboarding\OffboardingController;
use App\Http\Controllers\Backend\MastersettingController;
use App\Http\Controllers\Backend\Employee\EmployeeHomeController;
use App\Http\Controllers\MyReportController;
use App\Http\Controllers\Backend\Attendance\AdminManualPunchController;
use App\Http\Controllers\Backend\Attendance\ManualPunchController;
use App\Http\Controllers\Backend\Jobs\RecruitmentController;
use App\Http\Controllers\Backend\QuestionTemplateController;
use App\Http\Controllers\Backend\Employee\TimesheetEmployeeController;
use App\Http\Controllers\LettersController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;

use App\Http\Controllers\Backend\Employee\SalaryMasterController;
use App\Http\Controllers\Backend\Jobs\InterviewFeedbackController;
use App\Http\Controllers\Backend\Jobs\OfferApprovalController;
use App\Http\Controllers\Backend\Jobs\CandidateStatusController;
use App\Http\Controllers\Backend\Jobs\JobVacancyRequestController;



use App\Http\Controllers\Backend\Hr\SalaryReleaseController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/   
Route::resource('career', CareerController::class);
Route::get('/career/{id}', [CareerController::class, 'show'])->name('career.show');
Route::post('/career/filter', [CareerController::class, 'filter'])->name('career.filter');
Route::post('/career/apply', [CareerController::class, 'apply'])->name('career.apply');

Route::get('/dashboard/ceo', [DashboardController::class, 'ceo'])->name('ceo.dashboard');
Route::get('/manager/dashboard', [ManagerDashboardController::class, 'index'])->name('manager.dashboard');
Route::post('/manager/process-approval', [ManagerDashboardController::class, 'processApproval'])->name('manager.process-approval');
Route::get('/teamlead/dashboard', [TeamLeaderController::class, 'index'])->name('teamlead.dashboard');
Route::post('/teamlead/process-approval', [TeamLeaderController::class, 'processApproval'])->name('teamlead.process-approval');
Route::get('/dashboard/tester', [DashboardController::class, 'tester'])->name('tester.dashboard');
Route::get('/hr/dashboard', [HrDashboardController::class, 'index'])->name('hr.dashboard');
Route::post('/dashboard/process-approval', [DashboardController::class, 'processApproval'])->name('dashboard.process-approval');

Route::get('/dashboard/employee', [DashboardController::class, 'employee'])->name('employee.dashboard');



// Add these routes to your existing web.php file
Route::resource('hierarchy', HierarchyController::class);
Route::get('/hierarchy/{id}/modules', [HierarchyController::class, 'getHierarchyModules'])->name('get-hierarchy-modules');

Route::get('/hierarchy/{id}/details', [HierarchyController::class, 'getHierarchyDetails'])->name('hierarchy.details');
Route::get('/hierarchy/export', [HierarchyController::class, 'export'])->name('hierarchy.export');


// Authentication Routes
Route::controller(AuthController::class)->group(function() {
    Route::get('/login', 'showLoginPage')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

// Employee Routes
    Route::get('/employee/dashboard', [EmployeeDashboardController::class, 'index'])->name('eemployee.dashboard');
    // Other employee routes...

    Route::put('/tickets/{id}/state', [App\Http\Controllers\Backend\Tickets\TicketsController::class, 'updateState'])
    ->name('tickets.updateState');

Route::put('/tickets/{id}/priority', [App\Http\Controllers\Backend\Tickets\TicketsController::class, 'updatePriority'])
    ->name('tickets.updatePriority');
    Route::put('/{id}/assignment', [TicketsController::class, 'updateAssignment'])->name('tickets.updateAssignment');// Authentication Route
Route::get('/', [AuthController::class, 'showLoginPage'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');



Route::post('/auth/change/{id}/confirmpassword',[AuthController::class,'confirmPassword'])->name('confirm-change-password');
Route::post('/password/send/reset-link', [AuthController::class, 'sendResetLink'])->name('send-password-reset.link');
Route::get('/auth/{id}/resetpassword/{token}',[AuthController::class,'handleResetPassword'])->name('resetpassword');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('employee.dashboard');
// Remove duplicate routes and keep only these
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordPage'])->name('forgot-password');
Route::post('/forgot-password', [AuthController::class, 'handleForgotPassword'])->name('forgot-password.submit');
Route::post('/forgot-password', [AuthController::class, 'handleForgotPassword'])->name('forgot-password.submit');
// Reset Password Routes
// Reset Password Routes
Route::get('/reset-password', [AuthController::class, 'showResetPasswordPage'])->name('reset-password');
Route::post('/password/reset/{id}/{token}', [AuthController::class, 'handleResetPassword'])->name('reset-password.submit');

// Change password for authenticated users
Route::post('/auth/change/{id}/confirmpassword', [AuthController::class, 'confirmPassword'])->name('confirm-change-password');
//
/** Roles && Users **/
Route::resource('users', UserController::class);
Route::post('/submit/new/user/data',[UserController::class,'submitData'])->name('submit-new-userdata');
Route::get('/show/user-role',[UserController::class,'showRoles'])->name('show-user-role');
Route::post('/add/user-role',[UserController::class,'addRoles'])->name('add-user-role');
Route::get('/fetch/user-role/{id}/data',[UserController::class,'getData'])->name('fetch-role-data');
Route::put('/update/user-role/{id}/data', [UserController::class, 'updateData'])->name('update-role-data');
Route::delete('/delete/user-role/{id}/data', [UserController::class, 'deleteData'])->name('delete-role-date');
Route::put('/update/rolestatus-change', [UserController::class, 'updateStatus'])->name('update-role-statuschange');
Route::post('/set/def/pass/{id}/user/send-email', [UserController::class, 'sendDetailToEmail'])->name('set-user-send.email');
Route::put('/lop/earned-leaves/update', [LeavesSettingsController::class, 'updateLOPEarnedLeaves'])->name('lop.earnedleaves.update');
//


// Settings Route
Route::get('/settings', function () {
    return view('hrms.Employee.LeaveSettings.create');
});

// Employee Routes
Route::get('/employees/grid', [AllEmployeeController::class, 'grid'])->name('employee.grid');
Route::post('/employee/update-designation', [AllEmployeeController::class, 'updateDesignation'])->name('employee.updateDesignation');
Route::get('/check-email', [AllEmployeeController::class, 'checkEmail'])->name('employee.checkEmail');
Route::get('assets/details/{id}', [AllEmployeeController::class, 'showAssetDetails'])->name('assets.details');
Route::get('employee/{id}/history', [AllEmployeeController::class, 'history'])->name('employee.history');
Route::resource('employee', AllEmployeeController::class);

Route::get('/employeeprofile/edit/{id}', [EmployeeProfileController::class, 'edit'])->name('employeeprofile.edit');
Route::get('/get-employees-by-department/{departmentId}', [AllEmployeeController::class, 'getEmployeesByDepartment'])
     ->name('employees.byDepartment');




// Route for updating the employee profile
Route::post('/employeeprofile/update/{id}', [EmployeeProfileController::class, 'update'])->name('employeeprofile.update');
Route::get('employee/{id}/edit-personal-info', [EmployeeProfileController::class, 'editPersonalInfo'])
    ->name('employee.personal_info.edit');

Route::put('employee/{id}/update-personal-info', [EmployeeProfileController::class, 'updatePersonalInfo'])
    ->name('employee.personal_info.update');
    Route::get('employee/{id}/edit-emergency-contact', [EmployeeProfileController::class, 'editEmergencyContact'])->name('employee.emergency_contact.edit');
Route::put('employee/{id}/update-emergency-contact', [EmployeeProfileController::class, 'updateEmergencyContact'])->name('employee.emergency_contact.update');
Route::get('/employee/{id}/bank-info/edit', [EmployeeProfileController::class, 'editBankInfo'])->name('employee.bank_info.edit');
Route::put('/employee/{id}/bank-info', [EmployeeProfileController::class, 'updateBankInfo'])->name('employee.bank_info.update');
// Family Information Routes
Route::get('employee/{id}/family/edit', [EmployeeProfileController::class, 'editFamilyInfo'])->name('family.edit');
Route::put('employee/{id}/family/update', [EmployeeProfileController::class, 'updateFamilyInfo'])->name('family.update');
Route::delete('/family/{id}', [EmployeeProfileController::class, 'deleteFamilyMember'])->name('family.delete');
Route::get('/employee/{id}/edit-education', [EmployeeProfileController::class, 'editEducation'])->name('education.edit');
Route::put('/employee/{id}/education', [EmployeeProfileController::class, 'updateEducation'])->name('education.update');
Route::delete('/education/{id}', [EmployeeProfileController::class, 'destroy'])->name('education.destroy');
Route::get('employee/{id}/experience/edit', [EmployeeProfileController::class, 'editExperience'])->name('employee.experience.edit');
Route::put('employee/{id}/experience/update', [EmployeeProfileController::class, 'updateExperience'])->name('employee.experience.update');
Route::delete('employee/experience/{id}', [EmployeeProfileController::class, 'deleteExperience'])->name('employee.experience.delete');
Route::put('/employee/{id}/bank-statutory', [EmployeeProfileController::class, 'updateBankStatutory'])->name('bank_statutory.update');

// Trash Functionality Routes
Route::get('employees/trash', [AllEmployeeController::class, 'trash'])->name('employee.trash'); 
Route::delete('/employees/trash/{id}', [AllEmployeeController::class, 'destroy'])->name('employees.trash');
Route::post('employees/restore/{id}', [AllEmployeeController::class, 'restore'])->name('employee.restore');
Route::delete('employees/permanently-delete/{id}', [AllEmployeeController::class, 'permanentlyDelete'])->name('employee.permanentlyDelete'); 
Route::get('/get-designations/{department_id}', [AllEmployeeController::class, 'getDesignations']);
Route::get('/employee/{id}/download-documents', [AllEmployeeController::class, 'downloadDocuments'])
    ->name('employee.downloadDocuments');

Route::get('/employees/{id}/download-documents', [AllEmployeeController::class, 'downloadDocuments'])
    ->name('employee.downloadDocuments');

Route::resource('holidays', HolidaysController::class);
Route::get('/holidays/check-title', [HolidaysController::class, 'checkTitle'])->name('holidays.check-title');
Route::post('/holidays/update/{id}', [HolidaysController::class, 'update'])->name('holidays.update');

// Leave Management Routes
Route::resource('admin-leaves', AdminLeavesController::class);
Route::put('admin-leaves/{id}/update-status', [AdminLeavesController::class, 'updateStatus'])->name('admin-leaves.update-status');
Route::resource('employee-leaves', EmployeeLeavesController::class);

Route::get('/employees/{employee_id}', [EmployeeLeavesController::class, 'getEmployeeByEmployeeId']);

// Leave Settings Routes
Route::get('/leave-settings', [LeavesSettingsController::class, 'index'])->name('leave-settings.index');
Route::post('/leave-settings/update-annual-days', [LeavesSettingsController::class, 'updateAnnualDays'])->name('update.annual.days');
Route::post('/leave-settings/update-carry-forward', [LeavesSettingsController::class, 'updateCarryForward'])->name('update.carry.forward');
Route::post('/leave-settings/update-earned-leaves', [LeavesSettingsController::class, 'updateEarnedLeaves'])->name('update.earned.leaves');
Route::post('/leave-settings/update-hospitalisation', [LeavesSettingsController::class, 'updateHospitalisation'])->name('leave-settings.update-hospitalisation');
Route::post('/leave-settings/update-maternity', [LeavesSettingsController::class, 'updateMaternity'])->name('leave-settings.update-maternity');
Route::post('/leave-settings/update-paternity', [LeavesSettingsController::class, 'updatePaternity'])->name('leave-settings.update-paternity');
Route::post('/leave-settings/update-lop-days', [LeavesSettingsController::class, 'updateLOPDays'])->name('update.lop.days');
Route::put('/lop-days/update', [LeavesSettingsController::class, 'updateLOPDays'])->name('lop.days.update');
Route::put('/lop-carryforward/update', [LeavesSettingsController::class, 'updateLOPCarryForward'])->name('lop.carryforward.update');
Route::put('/lop-earnedleaves/update', [LeavesSettingsController::class, 'updateLOPEarnedLeaves'])->name('lop.earnedleaves.update');
Route::post('/leave-settings/update-sick', [LeavesSettingsController::class, 'updateSick'])->name('leave-settings.update-sick');

//DEPARTMENT//
Route::post('/department/check', [DepartmentController::class, 'check'])->name('department.check');
Route::resource('department', DepartmentController::class);
//Designation//
Route::resource('designation', DesignationController::class);

// Timesheet
Route::resource('timesheet', TimesheetController::class);
Route::get('/api/getProjectDetails/{projectId}', [TimesheetController::class, 'getProjectDetails']);


// scheduling
Route::resource('scheduling', SchedulingController::class);
Route::get('/shifts/{id}', [SchedulingController::class, 'getShiftDetails'])->name('shifts.details');

// shift
Route::resource('shift', ShiftController::class);
Route::post('/shifts/{id}/update-status', [ShiftController::class, 'updateStatus']);



// Assets
Route::resource('assets',AssetsController::class);
Route::post('/assets/{id}/update-status', [AssetsController::class, 'updateStatus']);


// performance-indicator
Route::resource('performance-indicator',Performance_IndicatorController::class);
Route::post('/performance-indicator/{id}/update-status', [Performance_IndicatorController::class, 'updateStatus'])->name('performance-indicator.update-status');

// performance-appraisal
Route::resource('performance-appraisal',Performance_AppraisalController::class);
Route::post('/performance-appraisal/{id}/update-status', [Performance_AppraisalController::class, 'updateStatus']);
Route::get('/getPerformanceIndicators', [Performance_AppraisalController::class, 'getPerformanceIndicators'])->name('getPerformanceIndicators');

// performance-review
Route::resource('performance-review',ReviewController::class);
Route::post('/performance-review/{id}/update-status', [ReviewController::class, 'updateStatus'])->name('performance-review.update-status');
Route::get('/get-employee-details/{id}', [ReviewController::class, 'getEmployeeDetails']);
Route::get('/performance-review/export/excel/{id}', [PerformanceReviewExportController::class, 'exportExcel']);
Route::get('/performance-review/export/pdf/{id}', [PerformanceReviewExportController::class, 'exportPdf']);



/** Client Routes **/
Route::get('client/{id}/profile',[ClientController::class,'showProfile'])->name('clientprofile');
Route::get('client/clientlist',[ClientController::class,'clientList'])->name('clientlist');

Route::post('/client/change-status', [ClientController::class, 'changeStatus'])->name('client.changeStatus');
Route::put('/client/update-amc-reminder', [ClientController::class, 'updateAmcReminder'])->name('client.updateAmcReminder');

Route::resource('client',ClientController::class);

//project

Route::put('/projects/update-status', [ProjectController::class, 'updateStatus'])->name('projects.updateStatus');
Route::resource('projects', ProjectController::class);

// task
Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
Route::resource('tasks', TaskController::class);

//taskboard
Route::resource('taskboard', TaskboardController::class);

Route::resource('tickets', TicketsController::class);
Route::put('/tickets/{id}/status', [TicketsController::class, 'updateStatus'])->name('tickets.updateStatus');
Route::put('/tickets/{id}/priority', [TicketsController::class, 'updatePriority'])->name('tickets.updatePriority');
// Ticket routes

Route::put('/tickets/{id}/status', [\App\Http\Controllers\Backend\Tickets\TicketsController::class, 'updateStatus'])->name('tickets.updateStatus');
Route::put('/tickets/{id}/priority', [\App\Http\Controllers\Backend\Tickets\TicketsController::class, 'updatePriority'])->name('tickets.updatePriority');
/** Goal Routes  **/
Route::delete('goal/track/delete/{id}',[GoalController::class,'deleteTrack'])->name('remove-goaltrack');
Route::put('goal/track/statuschange',[GoalController::class,'changeTrackStatus'])->name('goaltrack-statuschange');
Route::put('goal/track/update/{id}',[GoalController::class,'updateData'])->name('update-goaltrack');
Route::post('goal/track/add',[GoalController::class,'storeGoalTrack'])->name('store-goaltrack');
Route::get('goal/track/show',[GoalController::class,'showgoalTrack'])->name('show-goaltrack');
Route::get('goal/type/edit/{id}',[GoalController::class,'getData'])->name('edit-goaltype');
Route::put('goal/type/statuschange',[GoalController::class,'changeStatus'])->name('goal-statuschange');
Route::resource('goal',GoalController::class);

/** Tax Routes  **/
// Specific routes should come BEFORE resource routes
Route::post('tax/change-status/{id}', [TaxController::class, 'changeTaxStatus'])->name('tax.changeStatus');
Route::resource('tax', TaxController::class);

/** Training **/
Route::resource('training-type', TrainingTypeController::class);
Route::get('training-type/{id}/change-status', [TrainingTypeController::class, 'changeStatus'])->name('training-type.change-status');
Route::delete('/training-type/{id}', [TrainingTypeController::class, 'destroy'])->name('training-type.destroy');
Route::resource('trainers', TrainerController::class);
Route::put('/trainers/{id}/status', [TrainerController::class, 'updateStatus'])->name('trainers.updateStatus');
Route::get('/trainers/{id}', [TrainerController::class, 'show']);
Route::delete('/trainers/{id}', [TrainerController::class, 'destroy'])->name('trainers.destroy');




// Training Management Routes
Route::prefix('trainings')->name('trainings.')->group(function () {
    Route::get('/', [TrainingManagementController::class, 'index'])->name('index');
    Route::get('/edit/{id}', [TrainingManagementController::class, 'editEmployee'])->name('editEmployee');
    Route::put('/update/{id}', [TrainingManagementController::class, 'updateEmployee'])->name('updateEmployee');
    Route::post('/employee/{employeeId}/resource/{resourceId}/complete', [TrainingManagementController::class, 'markResourceCompleted'])->name('markResourceCompleted');
    Route::delete('/employee/{employeeId}/resource/{resourceId}', [TrainingManagementController::class, 'deleteResource'])->name('deleteResource');
    Route::get('/employee/{employeeId}/progress', [TrainingManagementController::class, 'getTrainingProgress'])->name('getTrainingProgress');
});


// Employee Training Dashboard Routes
Route::prefix('employee/training')->name('employee.training.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Employee\EmployeeTrainingDashboardController::class, 'index'])->name('dashboard');
    Route::post('/resource/{resourceId}/complete', [App\Http\Controllers\Employee\EmployeeTrainingDashboardController::class, 'markResourceCompleted'])->name('markCompleted');
    Route::get('/resource/{resourceId}/download', [App\Http\Controllers\Employee\EmployeeTrainingDashboardController::class, 'downloadResource'])->name('download');
});


//Categorie//
Route::resource('categories', CategoriesController::class);
// Budget//
Route::resource('budgets', BudgetsController::class);
// Budgetexpenses//
Route::resource('budgetexpenses', BudgetexpensesController::class);
//Budgetrevenue//
Route::resource('budgetrevenue', BudgetrevenueController::class);

Route::resource('payroll', PayrollItemsController::class)->except(['show']);


Route::get('/payovertime/create', [PayrollItemsController::class, 'createOvertime'])->name('payovertime.create');
Route::post('/payovertime', [PayrollItemsController::class, 'storeOvertime'])->name('payovertime.store');
Route::get('/payovertime/edit/{id}', [PayrollItemsController::class, 'overtimeedit'])->name('payovertime.edit');
Route::put('/payovertime/{id}', [PayrollItemsController::class, 'overtimeupdate'])->name('payovertime.update');
Route::delete('/payovertime/{id}', [PayrollItemsController::class, 'overtimedestroy'])->name('payovertime.destroy');

Route::get('/deductions/create', [PayrollItemsController::class, 'deductioncreate'])->name('deductions.create');
Route::post('/deductions', [PayrollItemsController::class, 'deductionstore'])->name('deductions.store');
Route::get('/deductions/edit/{id}', [PayrollItemsController::class, 'deductionedit'])->name('deductions.edit');
Route::put('/deductions/{id}', [PayrollItemsController::class, 'deductionupdate'])->name('deductions.update');
Route::delete('/deductions/{id}', [PayrollItemsController::class, 'deductiondestroy'])->name('deductions.destroy');




Route::resource('salary', EmployeeSalaryController::class);
Route::patch('/salary/{id}/approval-status', [EmployeeSalaryController::class, 'updateApprovalStatus'])->name('salary.update-approval-status');
Route::post('/salary/bulk-approval-status', [EmployeeSalaryController::class, 'bulkUpdateApprovalStatus'])->name('salary.bulk-approval-status');
Route::post('/salary/bulk-release-status', [EmployeeSalaryController::class, 'bulkUpdateReleaseStatus'])->name('salary.bulk-release-status');
Route::post('/salary/bulk-update-field', [EmployeeSalaryController::class, 'bulkUpdateField'])->name('salary.bulk-update-field');
Route::get('/get-additions-deductions', [EmployeeSalaryController::class, 'getAdditionsAndDeductions'])->name('get.additions.deductions');



Route::get('/payslip/{id}', [EmployeeSalaryController::class, 'showPayslip'])->name('payslip.show');
Route::get('/salary/{id}/download-csv', [EmployeeSalaryController::class, 'downloadCSV'])->name('salary.download.csv');
Route::get('/salary/{id}/download-pdf', [EmployeeSalaryController::class, 'downloadPDF'])->name('salary1.download.pdf');


Route::get('estimate/search/data', [EstimateController::class, 'getData'])->name('estimate-searchdata');
Route::post('estimate/change-status', [EstimateController::class, 'changeStatus'])->name('estimate-changeStatus');
Route::get('estimate/{id}/send-email', [EstimateController::class, 'sendEstimateEmail'])->name('estimate.send.email');
Route::get('estimate/{id}/printpdf', [EstimateController::class, 'pdfPrint'])->name('estimate.print.pdf');
Route::get('estimate/{id}/pdf', [EstimateController::class, 'pdf'])->name('estimate.pdf');
// Route::get('estimate/csv', [EstimateController::class, 'csv'])->name('estimate.csv');
Route::get('estimate/{id}/bill',[EstimateController::class,'viewEstimate'])->name('estimate.bill');
Route::get('estimate/get-projects', [EstimateController::class, 'getProjects'])->name('estimate.getprojects');
Route::resource('estimate',EstimateController::class);

/** Invoice Routes  **/
Route::get('invoice/show-payment/data', [InvoiceController::class, 'showPaymentData'])->name('show-payment-data');
Route::get('invoice/search/data', [InvoiceController::class, 'getData'])->name('invoice-searchdata');
Route::post('invoice/change-status', [InvoiceController::class, 'changeStatus'])->name('invoice-changeStatus');
Route::get('invoice/{id}/send-email', [InvoiceController::class, 'sendInvoiceEmail'])->name('invoice.send.email');
Route::get('invoice/{id}/printpdf', [InvoiceController::class, 'pdfPrint'])->name('invoice.print.pdf');
Route::get('invoice/{id}/pdf', [InvoiceController::class, 'pdf'])->name('invoice.pdf');
Route::get('invoice/{id}/bill',[InvoiceController::class,'viewInvoice'])->name('invoice.bill');
Route::get('invoice/get-projects', [InvoiceController::class, 'getProjects'])->name('invoice.getprojects');
Route::resource('invoice',InvoiceController::class);

/** PF Type Controller  **/
Route::put('pfund/type/statuschange',[PFTypeController::class,'changeStatus'])->name('pfund-type-statuschange');
Route::get('pfund/type/edit/{id}',[PFTypeController::class,'getData'])->name('edit-pftype');
Route::resource('pftype',PFTypeController::class);

/** Provident Fund  **/
Route::post('providentfund/change-status/{id}', [ProvidentFundController::class, 'changeStatus'])->name('provifund.changeStatus');
Route::resource('providentfund',controller: ProvidentFundController::class);

/** Expense **/
Route::post('expense/change-status/{id}', [ExpenseController::class, 'changeStatus'])->name('expense-changeStatus');
Route::resource('expense',controller: ExpenseController::class);

// Custom Policies Routes
Route::resource('policies', PolicyController::class);
Route::get('/policies/download/{id}', [PolicyController::class, 'download'])->name('policies.download');
Route::resource('custom-policies', CustomPolicyController::class)->only(['index', 'update', 'destroy']);
Route::post('/custom-policies/{id}', [CustomPolicyController::class, 'updateCustomPolicy'])->name('customupdate');
Route::post('/custom-policies/store', [CustomPolicyController::class, 'storeCustomPolicy'])->name('storeCustomPolicy');
Route::get('/custom-policies/create', [CustomPolicyController::class, 'create'])->name('createCustomPolicy');
// Employee Search Route
Route::get('employee/search', [CustomPolicyController::class, 'searchEmployees'])->name('employee.search');

Route::resource('terminations', TerminationController::class);

//promotion 
Route::post('/get-designation', [PromotionController::class, 'getDesignationByEmployeeId'])->name('get.designation');
Route::get('/get-designations/{department_id}', [PromotionController::class, 'getDesignationsByDepartment'])->name('get-designations');
Route::post('promotion/get-employee-details', [PromotionController::class, 'getEmployeeDetails'])
    ->name('promotion.getEmployeeDetails');
Route::resource('promotion', PromotionController::class);


//subscription
Route::resource('subscribe', SuscribeController::class);

//subcompany

Route::resource('subscribecompany', SuscribecompanyController::class);

//subtable

Route::resource('subscribetable', SuscribetableController::class);

//privacypolicy
Route::resource('privacy-policy', PrivacypolicyController::class);

//terms
Route::resource('terms', TermsController::class);
//Faq
Route::resource('faq', FaqController::class);



//search
// Search
Route::get('/search', [SearchController::class, 'index'])->name('search.index'); // For showing the search form
Route::get('/search/results', [SearchController::class, 'search'])->name('search.results'); // For handling the search
Route::get('/search', [SearchController::class, 'search'])->name('search.route');


Route::resource('search', SearchController::class);


//knowledgebase

Route::resource('knowledgebase', KnowledgebaseController::class);

//managejobs

Route::resource('managejobs', ManagejobsController::class);

//experience

Route::resource('experience', ExperienceController::class);
Route::post('/experience/{id}/toggle-status', [ExperienceController::class, 'toggleStatus'])->name('experience.toggleStatus');

//candidate

Route::resource('candidate', CandidateController::class);

//schedule

Route::resource('schedule', sheduleController::class);


Route::resource('add-resume', AddresumeController::class);
Route::post('add-resume/{id}/update-status', [AddresumeController::class, 'updateStatus'])->name('add-resume.update-status');
Route::get('/add-resume/view-resume/{id}', [AddresumeController::class, 'viewResume'])->name('add-resume.view-resume');

Route::resource('shortlist', ShortlistController::class);
Route::get('shortlist/{id}/schedule-interview', [ShortlistController::class, 'scheduleInterview'])->name('shortlist.schedule-interview');
Route::post('shortlist/store-interview', [ShortlistController::class, 'storeInterview'])->name('shortlist.store-interview');
Route::post('shortlist/interview/{id}/update-status', [ShortlistController::class, 'updateInterviewStatus'])->name('shortlist.update-interview-status');
Route::get('shortlist/{id}/resume', [ShortlistController::class, 'viewResume'])->name('shortlist.view-resume');

//Question

Route::resource('Question', QuestionController::class);


//resume


Route::prefix('resume')->name('resume.')->group(function () {
    Route::get('/', [App\Http\Controllers\Backend\Jobs\ManageresumeController::class, 'index'])->name('index');
    Route::get('/email-template/{type}', [App\Http\Controllers\Backend\Jobs\ManageresumeController::class, 'getEmailTemplate'])->name('email-template');
    Route::get('/stats/data', [App\Http\Controllers\Backend\Jobs\ManageresumeController::class, 'getStats'])->name('stats');
    Route::get('/{id}', [App\Http\Controllers\Backend\Jobs\ManageresumeController::class, 'show'])->name('show');
    Route::patch('/{id}/update-status', [App\Http\Controllers\Backend\Jobs\ManageresumeController::class, 'updateStatus'])->name('update-status');
    Route::post('/{id}/send-email', [App\Http\Controllers\Backend\Jobs\ManageresumeController::class, 'sendEmail'])->name('send-email');
    Route::delete('/{id}', [App\Http\Controllers\Backend\Jobs\ManageresumeController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/view-resume', [App\Http\Controllers\Backend\Jobs\ManageresumeController::class, 'viewResume'])->name('view-resume');
    
});




Route::get('/salary-settings', [SalarySettingsController::class, 'edit'])->name('salary-settings.edit');
Route::post('/salary-settings/update', [SalarySettingsController::class, 'update'])->name('salary-settings.update');
Route::delete('/salary-settings/delete-tds/{id}', 'SalarySettingsController@deleteTdsEntry')->name('salary-settings.delete-tds');
Route::get('/get-tds-percentage', [EmployeeSalaryController::class, 'getTdsPercentage']);


Route::resource('leave-types', LeaveTypeController::class);
Route::get('/leave-types/update-status/{id}/{status}', [LeaveTypeController::class, 'updateStatus'])->name('leave-types.updateStatus');
        
  
//Reports Routes

Route::resource('expenses-reports', ExpenseReportController::class);
Route::post('/expenses-reports/{id}/change-status', [ExpenseReportController::class, 'changeStatus'])->name('expenses-reports.changeStatus');
Route::resource('payments-report', PaymentReportController::class);
Route::resource('project-reports', ProjectReportController::class);
Route::get('/user-reports', [UserReportController::class, 'index'])->name('user-reports.index');
Route::post('/users/{id}/status', [UserReportController::class, 'changeStatus'])->name('user-reports.changeStatus');
Route::resource('payslip-reports', PayslipReportController::class);
Route::resource('task-reports', TaskReportController::class);
Route::resource('attendance-reports', AttendanceReportController::class)->only(['index']);
Route::resource('leave-reports', LeaveReportController::class)->except(['show']);
Route::get('leave-reports/pdf', [LeaveReportController::class, 'pdf'])->name('leave-reports.pdf');
Route::resource('employee-reports', EmployeeReportController::class)->except(['show']);
Route::get('employee-reports/pdf', [EmployeeReportController::class, 'pdf'])->name('employee-reports.pdf');
Route::resource('invoice-reports', InvoiceReportController::class);
Route::get('invoice-reports/{invoice}/download', [InvoiceReportController::class, 'download'])->name('invoice-reports.download');
Route::resource('daily-report', DailyReportController::class)->except(['show']);
Route::get('daily-report/pdf', [DailyReportController::class, 'pdf'])->name('daily-report.pdf');

Route::prefix('interview-process')->name('interview-process.')->group(function () {
    Route::get('/', [App\Http\Controllers\Backend\Reports\InterviewProcessController::class, 'index'])->name('index');
    Route::post('/job-posting-report', [App\Http\Controllers\Backend\Reports\InterviewProcessController::class, 'getJobPostingReport'])->name('job-posting-report');
    Route::post('/export-job-posting', [App\Http\Controllers\Backend\Reports\InterviewProcessController::class, 'exportJobPosting'])->name('export-job-posting');
    Route::post('/interview-scheduling-report', [App\Http\Controllers\Backend\Reports\InterviewProcessController::class, 'getInterviewSchedulingReport'])->name('interview-scheduling-report');
    Route::post('/export-interview-scheduling', [App\Http\Controllers\Backend\Reports\InterviewProcessController::class, 'exportInterviewScheduling'])->name('export-interview-scheduling');
    Route::post('/resume-management-report', [App\Http\Controllers\Backend\Reports\InterviewProcessController::class, 'getResumeManagementReport'])->name('resume-management-report');
    Route::post('/export-resume-management', [App\Http\Controllers\Backend\Reports\InterviewProcessController::class, 'exportResumeManagement'])->name('export-resume-management');
    Route::post('/candidate-shortlisting-report', [App\Http\Controllers\Backend\Reports\InterviewProcessController::class, 'getCandidateShortlistingReport'])->name('candidate-shortlisting-report');
    Route::post('/export-candidate-shortlisting', [App\Http\Controllers\Backend\Reports\InterviewProcessController::class, 'exportCandidateShortlisting'])->name('export-candidate-shortlisting');
    Route::get('/view-resume/{id}', [App\Http\Controllers\Backend\Reports\InterviewProcessController::class, 'viewResume'])->name('view-resume');
});

Route::prefix('newproject-reports')->name('newproject.reports.')->group(function () {
    Route::get('/', [NewProjectController::class, 'index'])->name('index');

    // Existing
    Route::post('/project-summary', [NewProjectController::class, 'getProjectSummaryReport'])->name('project-summary');
    Route::get('/export/project-summary', [NewProjectController::class, 'exportProjectSummary'])->name('export-project-summary');
    Route::post('/task-summary', [NewProjectController::class, 'getTaskSummaryReport'])->name('task-summary');
    Route::get('/export/task-summary', [NewProjectController::class, 'exportTaskSummary'])->name('export-task-summary');

    // NEW: Task Board Report
    Route::post('/taskboard-report', [NewProjectController::class, 'getTaskBoardReport'])->name('taskboard-report');
    Route::get('/export/taskboard-report', [NewProjectController::class, 'exportTaskBoardReport'])->name('export-taskboard-report');
});

use App\Http\Controllers\Backend\Reports\TimesheetReportController;

// Timesheet Reports
Route::group(['prefix' => 'timesheet-reports'], function () {
    Route::get('/', [TimesheetReportController::class, 'index'])->name('timesheet-reports.index');
    Route::get('/export/csv', [TimesheetReportController::class, 'exportCsv'])->name('timesheet-reports.export.csv');
    Route::get('/export/pdf', [TimesheetReportController::class, 'exportPdf'])->name('timesheet-reports.export.pdf');
});
Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
Route::post('/punch-in', [AttendanceController::class, 'punchIn'])->name('punch-in');
Route::post('/punch-out', [AttendanceController::class, 'punchOut'])->name('punch-out');
Route::get('/attendance/search', [AttendanceController::class, 'search'])->name('attendance.search');
Route::prefix('admin')->group(function() {
    Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('admin.attendance.index');
    Route::get('/attendance/daily', [AdminAttendanceController::class, 'daily'])->name('admin.attendance.daily');
});

  
//Reports Routes

Route::group(['prefix' => 'expenses-reports'], function() {
    Route::get('/', [ExpenseReportController::class, 'index'])->name('expenses-reports.index');
    Route::get('/export/csv', [ExpenseReportController::class, 'exportCSV'])->name('expenses-reports.export.csv');
    Route::get('/export/pdf', [ExpenseReportController::class, 'exportPDF'])->name('expenses-reports.export.pdf');
    // Add other routes as needed...
});
Route::group(['prefix' => 'payments-report'], function() {
    Route::get('/', [PaymentReportController::class, 'index'])->name('payments-report.index');
    Route::get('/export/csv', [PaymentReportController::class, 'exportCSV'])->name('payments-report.export.csv');
    Route::get('/export/pdf', [PaymentReportController::class, 'exportPDF'])->name('payments-report.export.pdf');
});
Route::group(['prefix' => 'project-reports'], function() {
    Route::get('/', [ProjectReportController::class, 'index'])->name('project-reports.index');
    Route::get('/export/csv', [ProjectReportController::class, 'exportCSV'])->name('project-reports.export.csv');
    Route::get('/export/pdf', [ProjectReportController::class, 'exportPDF'])->name('project-reports.export.pdf');
});
Route::group(['prefix' => 'user-reports'], function() {
    Route::get('/', [UserReportController::class, 'index'])->name('user-reports.index');
    Route::get('/export/csv', [UserReportController::class, 'exportCSV'])->name('user-reports.export.csv');
    Route::get('/export/pdf', [UserReportController::class, 'exportPDF'])->name('user-reports.export.pdf');
    Route::post('/change-status/{id}', [UserReportController::class, 'changeStatus'])->name('user-reports.changeStatus');
});
Route::group(['prefix' => 'payslip-reports'], function() {
    Route::get('/', [PayslipReportController::class, 'index'])->name('payslip-reports.index');
    Route::get('/export/csv', [PayslipReportController::class, 'exportCSV'])->name('payslip-reports.export.csv');
    Route::get('/export/pdf', [PayslipReportController::class, 'exportPDF'])->name('payslip-reports.export.pdf');
    Route::get('/download/{id}', [PayslipReportController::class, 'downloadPdf'])->name('salary.download.pdf');
});
Route::group(['prefix' => 'task-reports'], function() {
    Route::get('/', [TaskReportController::class, 'index'])->name('task-reports.index');
    Route::get('/export/csv', [TaskReportController::class, 'exportCSV'])->name('task-reports.export.csv');
    Route::get('/export/pdf', [TaskReportController::class, 'exportPDF'])->name('task-reports.export.pdf');
});
Route::resource('attendance-reports', AttendanceReportController::class)->only(['index']);
Route::resource('leave-reports', LeaveReportController::class)->except(['show']);
Route::get('leave-reports/pdf', [LeaveReportController::class, 'pdf'])->name('leave-reports.pdf');

    Route::get('employees', [\App\Http\Controllers\Backend\Reports\EmployeeReportController::class, 'index'])
         ->name('employee.reports');
         
    Route::get('employees/export', [\App\Http\Controllers\Backend\Reports\EmployeeReportController::class, 'export'])
         ->name('employee.reports.export');
Route::group(['prefix' => 'invoice-reports'], function() {
    Route::get('/', [InvoiceReportController::class, 'index'])->name('invoice-reports.index');
    Route::get('/export/csv', [InvoiceReportController::class, 'exportCSV'])->name('invoice-reports.export.csv');
    Route::get('/export/pdf', [InvoiceReportController::class, 'exportPDF'])->name('invoice-reports.export.pdf');
    Route::delete('/{id}', [InvoiceReportController::class, 'destroy'])->name('invoice-reports.destroy');
});
Route::resource('daily-report', DailyReportController::class)->except(['show']);
Route::get('daily-report/pdf', [DailyReportController::class, 'pdf'])->name('daily-report.pdf');



Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
Route::post('/punch-in', [AttendanceController::class, 'punchIn'])->name('punch-in');
Route::post('/punch-out', [AttendanceController::class, 'punchOut'])->name('punch-out');
Route::get('/attendance/search', [AttendanceController::class, 'search'])->name('attendance.search');
Route::prefix('admin')->group(function() {
    Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('admin.attendance.index');
    Route::get('/attendance/daily', [AdminAttendanceController::class, 'daily'])->name('admin.attendance.daily');
});

Route::get('leave-reports/csv', [LeaveReportController::class, 'csv'])->name('leave-reports.csv');

// use App\Http\Controllers\Backend\Worklog\WorklogController;

    Route::get('/worklogs', [WorklogController::class, 'index'])->name('worklogs.index');
    Route::get('/worklogs/create', [WorklogController::class, 'create'])->name('worklogs.create');
    Route::post('/worklogs', [WorklogController::class, 'store'])->name('worklogs.store');
    Route::get('/worklogs/{worklog}', [WorklogController::class, 'show'])->name('worklogs.show');
    Route::get('/worklogs/{worklog}/edit', [WorklogController::class, 'edit'])->name('worklogs.edit');
    Route::put('/worklogs/{worklog}', [WorklogController::class, 'update'])->name('worklogs.update');
    Route::delete('/worklogs/{worklog}', [WorklogController::class, 'destroy'])->name('worklogs.destroy');
    Route::get('/worklogs-report', [WorklogController::class, 'report'])->name('worklogs.report');

    Route::get('/tickets/{id}/download', [TicketsController::class, 'download'])->name('tickets.download');
Route::prefix('services')->group(function() {
    Route::get('/', [ServiceController::class, 'index'])->name('services.index');
    Route::post('/', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/{id}/edit', [ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/{id}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/{id}', [ServiceController::class, 'destroy'])->name('services.destroy');
});
    
Route::resource('branches', BranchController::class);
Route::put('branches/{branch}/status', [BranchController::class, 'updateStatus'])->name('branches.updateStatus');


    // Add this route to your existing routes
Route::get('/get-managers', [AllEmployeeController::class, 'getManagers'])->name('get.managers');
    Route::get('/employee_expenses/index', [EmployeeExpenseController::class, 'index'])->name('employee_expenses.index');
    Route::get('/employee_expenses/create', [EmployeeExpenseController::class, 'create'])->name('employee_expenses.create');
    Route::post('/employee_expenses/index', [EmployeeExpenseController::class, 'store'])->name('employee_expenses.store');
    Route::get('/employee_expenses/{id}', [EmployeeExpenseController::class, 'show'])->name('employee_expenses.show');
    Route::delete('/employee_expenses/{id}', [EmployeeExpenseController::class, 'destroy'])->name('employee_expenses.destroy');
    Route::get('/employee-expenses/pending', [EmployeeExpenseController::class, 'pendingExpenses'])->name('employee_expenses.pending');
    Route::get('/employee-expenses/approve/{id}', [EmployeeExpenseController::class, 'approve'])->name('employee_expenses.approve');
    Route::get('/employee-expenses/reject/{id}', [EmployeeExpenseController::class, 'reject'])->name('employee_expenses.reject');
 // Status update route
Route::put('/employee_expenses/status/{id}', [EmployeeExpenseController::class, 'updateStatus'])
    ->name('employee_expenses.updateStatus');

    // Edit - show edit form
Route::get('employee_expenses/{id}/edit', [EmployeeExpenseController::class, 'edit'])->name('employee_expenses.edit');

// Update - update existing expense
Route::put('employee_expenses/{id}', [EmployeeExpenseController::class, 'update'])->name('employee_expenses.update');



Route::prefix('master')->group(function() {
    Route::resource('services', \App\Http\Controllers\Backend\master\ServiceController::class);
});



// Admin Access Routes
Route::prefix('admin')->group(function () {
    // Resource routes for CRUD operations
    Route::resource('adminaccess', AdminaccessController::class);
    
    // Additional custom routes
    Route::get('adminaccess/check-email', [AdminaccessController::class, 'checkEmail'])->name('adminaccess.checkEmail');
    Route::get('get-hierarchy-modules/{hierarchyId}', [AdminaccessController::class, 'getHierarchyModules'])->name('adminaccess.getHierarchyModules');
    Route::post('adminaccess/{id}/change-status', [AdminaccessController::class, 'changeStatus'])->name('adminaccess.changeStatus');
});







Route::get('/scheduling/get-employees/{department}', [SchedulingController::class, 'getEmployeesByDepartment']);

Route::prefix('scheduling')->group(function() {
    Route::get('/', [SchedulingController::class, 'index'])->name('scheduling.index');
    Route::get('/create', [SchedulingController::class, 'create'])->name('scheduling.create');
    Route::post('/', [SchedulingController::class, 'store'])->name('scheduling.store');
    Route::get('/employees', [SchedulingController::class, 'getEmployees'])->name('scheduling.getEmployees');
    // Add other routes as needed
});












Route::get('/scheduling/employees-by-department/{departmentId}', [SchedulingController::class, 'getEmployeesByDepartment'])->name('scheduling.employees-by-department');


// Employee Attendance Routes (No middleware)
Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
Route::post('/attendance/punch-in', [AttendanceController::class, 'punchIn'])->name('punch-in');
Route::post('/attendance/punch-out', [AttendanceController::class, 'punchOut'])->name('punch-out');
Route::get('/attendance/search', [AttendanceController::class, 'search'])->name('attendance.search');
Route::get('/attendance/analytics', [AttendanceController::class, 'getAttendanceAnalytics'])->name('attendance.analytics');
Route::post('/attendance/screen-status', [AttendanceController::class, 'updateScreenStatus'])->name('attendance.screen-status');

// Admin Attendance Routes (No middleware)
Route::prefix('admin')->group(function () {
    Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('admin.attendance.index');
    Route::get('/attendance/daily', [AdminAttendanceController::class, 'daily'])->name('admin.attendance.daily');
    Route::get('/attendance/summary/{employeeId}', [AdminAttendanceController::class, 'getEmployeeAttendanceSummary'])->name('admin.attendance.summary');
    Route::get('/attendance/export', [AdminAttendanceController::class, 'export'])->name('admin.attendance.export');
    Route::post('/attendance/bulk', [AdminAttendanceController::class, 'bulkOperations'])->name('admin.attendance.bulk');
    Route::get('/attendance/details/{employeeId}/{date}', [AdminAttendanceController::class, 'getAttendanceDetails'])->name('admin.attendance.details');
});

// Scheduling Routes (No middleware)
Route::prefix('admin')->group(function () {
    Route::resource('scheduling', SchedulingController::class);
    Route::get('/scheduling/employees/{departmentId}', [SchedulingController::class, 'getEmployeesByDepartment'])->name('scheduling.employees');
});

// Shift Management Routes (No middleware)
Route::prefix('admin')->group(function () {
    Route::resource('shift', ShiftController::class);
    Route::post('/shift/{id}/status', [ShiftController::class, 'updateStatus'])->name('shift.status');
});
// Add these routes to your existing schedule routes
Route::post('/scheduling/{id}/acknowledge', [SchedulingController::class, 'acknowledgeUpdate'])->name('scheduling.acknowledge');
Route::post('/scheduling/{id}/reset-flag', [SchedulingController::class, 'resetUpdateFlag'])->name('scheduling.reset-flag');
// Add this route to your web.php file
Route::delete('/scheduling/bulk-delete', [SchedulingController::class, 'bulkDelete'])->name('scheduling.bulk-delete');
Route::delete('/scheduling/bulk-delete', [SchedulingController::class, 'bulkDelete'])->name('scheduling.bulk-delete');

Route::prefix('community')->group(function () {
    Route::get('/', [CommunityController::class, 'index'])->name('community.index');
    Route::post('/send-wish', [CommunityController::class, 'sendWish'])->name('community.send-wish');
    Route::get('/wishes/{employeeId}/{wishType}', [CommunityController::class, 'getWishes'])->name('community.wishes');
    Route::post('/add-achievement', [CommunityController::class, 'addAchievement'])->name('community.add-achievement');
    Route::get('/user-achievements/{userId?}', [CommunityController::class, 'getUserAchievements'])->name('community.user-achievements');
    Route::post('/congratulate-achievement', [CommunityController::class, 'congratulateAchievement'])->name('community.congratulate-achievement');
    Route::get('/debug-session', [CommunityController::class, 'debugSession'])->name('community.debug');
});






    
    // Basic CRUD Routes
    Route::get('customize-site', [CustomizesiteController::class, 'index'])->name('customize-site.index');
    Route::get('customize-site/create', [CustomizesiteController::class, 'create'])->name('customize-site.create');
    Route::post('customize-site', [CustomizesiteController::class, 'store'])->name('customize-site.store');
    Route::get('customize-site/{id}', [CustomizesiteController::class, 'show'])->name('customize-site.show');
    Route::get('customize-site/{id}/edit', [CustomizesiteController::class, 'edit'])->name('customize-site.edit');
    Route::put('customize-site/{id}', [CustomizesiteController::class, 'update'])->name('customize-site.update');
    Route::delete('customize-site/{id}', [CustomizesiteController::class, 'destroy'])->name('customize-site.destroy');
    
    // Logo Management Routes
    Route::get('customize-site/logo/management', [CustomizesiteController::class, 'logoManagement'])->name('customize-site.logo.management');
    Route::post('customize-site/logo/main', [CustomizesiteController::class, 'updateMainLogo'])->name('customize-site.logo.main');
    Route::post('customize-site/logo/currency', [CustomizesiteController::class, 'updateCurrencyLogo'])->name('customize-site.logo.currency');
    Route::delete('customize-site/logo/currency/{currencyCode}', [CustomizesiteController::class, 'deleteCurrencyLogo'])->name('customize-site.logo.currency.delete');
    
    // Bulk Operations
    Route::post('customize-site/bulk/toggle-status', [CustomizesiteController::class, 'bulkToggleStatus'])->name('customize-site.bulk.toggle-status');
    Route::post('customize-site/bulk/delete', [CustomizesiteController::class, 'bulkDelete'])->name('customize-site.bulk.delete');


// Testing Tickets Routes
Route::prefix('testing')->name('testing.')->group(function () {
    Route::get('/', [TestingController::class, 'index'])->name('index');
    Route::get('/create', [TestingController::class, 'create'])->name('create');
    Route::post('/store', [TestingController::class, 'store'])->name('store');
    Route::get('/show/{id}', [TestingController::class, 'show'])->name('show');
    Route::get('/edit/{id}', [TestingController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [TestingController::class, 'update'])->name('update');
    Route::get('/project-team-members/{id}', [TestingController::class, 'getProjectTeamMembers'])->name('getProjectTeamMembers');
    Route::put('/update-status/{id}', [TestingController::class, 'updateStatus'])->name('updateStatus');
    Route::put('/update-priority/{id}', [TestingController::class, 'updatePriority'])->name('updatePriority');
    Route::put('/update-assignment/{id}', [TestingController::class, 'updateAssignment'])->name('updateAssignment');
    Route::delete('/destroy/{id}', [TestingController::class, 'destroy'])->name('destroy');
});




Route::post('/invoice/preview', [InvoiceController::class, 'preview'])->name('invoice.preview');
Route::post('/invoice/send-preview-email', [InvoiceController::class, 'sendPreviewEmail'])->name('invoice.sendPreviewEmail');
Route::post('/invoice/preview/create', [InvoiceController::class, 'createPreview'])->name('invoice.preview.create');
Route::get('invoice/pdf/{id}', [InvoiceController::class, 'pdf'])->name('invoice.pdf');
Route::get('invoice/{id}/send-email', [InvoiceController::class, 'sendInvoiceEmail'])->name('invoice.send-email');
Route::get('/expense/download/{id}', [ExpenseController::class, 'download'])->name('expense.download');

Route::prefix('payments')->name('payment.')->group(function() {
    Route::get('/create', [PaymentController::class, 'create'])->name('create');
    Route::post('/', [PaymentController::class, 'store'])->name('store');
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::get('/show-invoice', [PaymentController::class, 'showInvoice'])->name('show.invoice');
     Route::get('/{payment}/edit', [PaymentController::class, 'edit'])->name('edit');
    Route::put('/{payment}', [PaymentController::class, 'update'])->name('update');
});
Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payment.destroy');





Route::post('add-resume/{id}/update-status', [App\Http\Controllers\Backend\Jobs\AddresumeController::class, 'updateStatus'])->name('add-resume.update-status');
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('generale-setting-update', [SettingsController::class, 'generalSettingUpdate'])->name('generale-setting-update');
    Route::put('email-setting-update', [SettingsController::class, 'emailConfigSettingUpdate'])->name('email-setting-update');
    Route::post('logo-setting-update', [SettingsController::class, 'logoSettingUpdate'])->name('logo-setting-update');
    Route::put('pusher-setting-update', [SettingsController::class, 'pusherSettingUpdate'])->name('pusher-setting-update');

    // Add these routes to your existing web.php file

// Overtime Management Routes
Route::prefix('overtime')->group(function () {
    Route::get('/', [OvertimeController::class, 'index'])->name('overtime.index');
    Route::get('/details/{id}', [OvertimeController::class, 'getOvertimeDetails']);
    Route::post('/{id}/approve', [OvertimeController::class, 'approve']);
    Route::post('/{id}/reject', [OvertimeController::class, 'reject']);
    Route::post('/bulk-approve', [OvertimeController::class, 'bulkApprove']);
    Route::get('/export', [OvertimeController::class, 'export']);
});

 // Main attendance routes
    Route::post('/punch-in', [AttendanceController::class, 'punchIn'])->name('punch-in');
    Route::post('/punch-out', [AttendanceController::class, 'punchOut'])->name('punch-out');
    
    // Break management routes
    Route::post('/start-break', [AttendanceController::class, 'startBreak'])->name('start-break');
    Route::post('/end-break', [AttendanceController::class, 'endBreak'])->name('end-break');
    Route::get('/break-status', [AttendanceController::class, 'getBreakStatus'])->name('break-status');
    
    // Search and analytics
    Route::get('/attendance/search', [AttendanceController::class, 'search'])->name('attendance.search');
    Route::get('/attendance/analytics', [AttendanceController::class, 'getAttendanceAnalytics'])->name('attendance.analytics');
    Route::get('/attendance/debug-schedule', [AttendanceController::class, 'debugSchedule'])->name('attendance.debug-schedule');


Route::prefix('hrms')->group(function () {
    Route::resource('promotion-letter', PromotionLetterController::class);
    Route::get('promotion-letter/{promotionLetter}/preview', [PromotionLetterController::class, 'preview'])->name('promotion-letter.preview');
});
Route::get('hrms/promotion-letter/debug-logo', [PromotionLetterController::class, 'debugLogo']);
Route::get('/questions/download-pdf', [QuestionController::class, 'downloadPdf'])->name('Question.downloadPdf');

Route::resource('termination-letter-templates', TerminationLetterController::class);
Route::get('termination-letter-templates/{id}/preview', [TerminationLetterController::class, 'preview'])->name('termination-letter-templates.preview');
Route::post('terminations/{termination}/send-email', [TerminationController::class, 'sendEmail'])->name('terminations.sendEmail');










    
Route::get('/admin/attendance/export', [AdminAttendanceController::class, 'export'])->name('admin.attendance.export');
// Add these routes to your existing attendance routes
Route::prefix('admin/attendance')->name('admin.attendance.')->group(function () {
    Route::get('/', [AdminAttendanceController::class, 'index'])->name('index');
    Route::get('/daily', [AdminAttendanceController::class, 'daily'])->name('daily');
    Route::get('/export', [AdminAttendanceController::class, 'export'])->name('export');
    Route::get('/export-daily', [AdminAttendanceController::class, 'exportDaily'])->name('export.daily');
    Route::post('/add-leave', [AdminAttendanceController::class, 'addEmployeeLeave'])->name('add.leave');
    Route::delete('/remove-leave', [AdminAttendanceController::class, 'removeEmployeeLeave'])->name('remove.leave');
    Route::get('/leave-details/{employeeId}/{date}', [AdminAttendanceController::class, 'getLeaveDetails'])->name('leave.details');
    Route::get('/details/{employeeId}/{date}', [AdminAttendanceController::class, 'getAttendanceDetails'])->name('details');
});
Route::post('/shift/{id}/status', [ShiftController::class, 'updateStatus'])->name('shift.status');
Route::post('/scheduling/interchange', [SchedulingController::class, 'interchangeShifts'])->name('scheduling.interchange');
Route::post('/interchange-shifts', [SchedulingController::class, 'interchangeShifts']);
Route::get('/interchange-shifts', [SchedulingController::class, 'showInterchangeForm'])->name('interchange.form');
Route::post('/interchange-shifts', [SchedulingController::class, 'interchangeShifts'])->name('interchange.shifts');
// routes/web.php
Route::post('/scheduling/interchange', [SchedulingController::class, 'interchange'])->name('scheduling.interchange');
Route::prefix('categories')->group(function() {
    // Display categories
    
    // Category routes
    Route::post('/store-category', [CategoriesController::class, 'storeCategory'])->name('categories.store-category');
    Route::put('/update-category/{id}', [CategoriesController::class, 'updateCategory'])->name('categories.update-category');
    Route::delete('/destroy-category/{id}', [CategoriesController::class, 'destroyCategory'])->name('categories.destroy-category');
    
    // Subcategory routes
    Route::post('/store-subcategory', [CategoriesController::class, 'storeSubcategory'])->name('categories.store-subcategory');
    Route::put('/update-subcategory/{id}', [CategoriesController::class, 'updateSubcategory'])->name('categories.update-subcategory');
    Route::delete('/destroy-subcategory/{id}', [CategoriesController::class, 'destroySubcategory'])->name('categories.destroy-subcategory');
});
// In your routes file (web.php)
Route::get('/categories/{category}/subcategories', [CategoryController::class, 'getSubcategories']);

// In your CategoryController
 // Shift Interchange Routes (integrated with SchedulingController)
    Route::prefix('scheduling')->name('scheduling.')->group(function () {
        // Existing scheduling routes...
        Route::get('/', [SchedulingController::class, 'index'])->name('index');
        Route::get('/create', [SchedulingController::class, 'create'])->name('create');
        Route::post('/store', [SchedulingController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [SchedulingController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SchedulingController::class, 'update'])->name('update');
        Route::delete('/{id}', [SchedulingController::class, 'destroy'])->name('destroy');
        Route::get('/employees-by-department/{departmentId}', [SchedulingController::class, 'getEmployeesByDepartment'])->name('employees-by-department');
        Route::post('/{id}/acknowledge-update', [SchedulingController::class, 'acknowledgeUpdate'])->name('acknowledge-update');
        Route::post('/{id}/reset-update-flag', [SchedulingController::class, 'resetUpdateFlag'])->name('reset-update-flag');
    });
        
        // Shift Interchange Routes
        Route::get('/shift-interchange', [SchedulingController::class, 'shiftInterchangeIndex'])->name('scheduling.shift-interchange');
        Route::get('/shift-interchange/create', [SchedulingController::class, 'shiftInterchangeCreate'])->name('scheduling.shift-interchange.create');
        Route::post('/shift-interchange/store', [SchedulingController::class, 'shiftInterchangeStore'])->name('scheduling.shift-interchange.store');
        Route::post('/shift-interchange/{id}/approve', [SchedulingController::class, 'shiftInterchangeApprove'])->name('scheduling.shift-interchange.approve');
        Route::post('/shift-interchange/{id}/reject', [SchedulingController::class, 'shiftInterchangeReject'])->name('scheduling.shift-interchange.reject');
        Route::post('/shift-interchange/{id}/cancel', [SchedulingController::class, 'shiftInterchangeCancel'])->name('scheduling.shift-interchange.cancel');
        Route::get('/shift-interchange/available-employees', [SchedulingController::class, 'getAvailableEmployeesForInterchange'])->name('scheduling.shift-interchange.available-employees');

        // Shift Interchange Routes
Route::prefix('scheduling')->name('scheduling.')->group(function () {
    // Main scheduling routes
    Route::get('/', [SchedulingController::class, 'index'])->name('index');
    Route::get('/create', [SchedulingController::class, 'create'])->name('create');
    Route::post('/store', [SchedulingController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [SchedulingController::class, 'edit'])->name('edit');
    Route::put('/{id}', [SchedulingController::class, 'update'])->name('update');
    Route::delete('/{id}', [SchedulingController::class, 'destroy'])->name('destroy');
    Route::get('/employees-by-department/{departmentId}', [SchedulingController::class, 'getEmployeesByDepartment'])->name('employees-by-department');
    
    // Shift Interchange Routes
    Route::prefix('shift-interchange')->name('shift-interchange.')->group(function () {
        Route::get('/', [SchedulingController::class, 'shiftInterchangeIndex'])->name('index');
        Route::get('/create', [SchedulingController::class, 'shiftInterchangeCreate'])->name('create');
        Route::post('/store', [SchedulingController::class, 'shiftInterchangeStore'])->name('store');
        Route::post('/{id}/approve', [SchedulingController::class, 'shiftInterchangeApprove'])->name('approve');
        Route::post('/{id}/reject', [SchedulingController::class, 'shiftInterchangeReject'])->name('reject');
        Route::post('/{id}/cancel', [SchedulingController::class, 'shiftInterchangeCancel'])->name('cancel');
        Route::get('/available-employees', [SchedulingController::class, 'getAvailableEmployeesForInterchange'])->name('available-employees');
    });
});

// Automated Payslip routes
Route::prefix('hrms/payroll')->group(function () {
    // Main payslip management
    Route::get('/automated-payslips', [AutomatedPayslipController::class, 'index'])->name('automated-payslips.index');
    Route::post('/automated-payslips/generate', [AutomatedPayslipController::class, 'generatePayslips'])->name('automated-payslips.generate');
    
    // Individual payslip actions
    Route::get('/automated-payslips/{id}', [AutomatedPayslipController::class, 'show'])->name('automated-payslips.show');
    Route::get('/automated-payslips/{id}/download', [AutomatedPayslipController::class, 'downloadPayslip'])->name('automated-payslips.download');
    Route::post('/automated-payslips/{id}/resend-email', [AutomatedPayslipController::class, 'resendEmail'])->name('automated-payslips.resend-email');
});


// Asset Reports
Route::group(['prefix' => 'asset-reports', 'as' => 'asset-reports.'], function() {
    Route::get('/', [AssetReportController::class, 'index'])->name('index');
});

Route::prefix('ticket/reports')->name('ticket.reports.')->group(function () {
    // Main reports dashboard
    Route::get('/', [TicketReportsController::class, 'index'])->name('index');
    
    // Detailed reports
    Route::get('/detailed', [TicketReportsController::class, 'detailedReport'])->name('detailed');
    
    // Export functionality
    Route::get('/export/csv', [TicketReportsController::class, 'exportCsv'])->name('export.csv');
    
    // Employee performance reports
    Route::get('/employee/{employeeId}/performance', [TicketReportsController::class, 'employeePerformance'])->name('employee.performance');
    
    // AJAX endpoints for analytics
    Route::get('/analytics/data', [TicketReportsController::class, 'getAnalyticsData'])->name('analytics.data');
});
Route::get('/ticket/reports/export/pdf', [TicketReportsController::class, 'exportPdf'])
    ->name('ticket.reports.export.pdf');

    Route::group(['prefix' => 'policies-report', 'as' => 'policies-report.'], function() {
    Route::get('/', [PoliciesReportController::class, 'index'])->name('index');
    Route::get('/export/csv', [PoliciesReportController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export/pdf', [PoliciesReportController::class, 'exportPdf'])->name('export.pdf');
});

// Employee Reports
Route::prefix('reports/employee')->group(function () {
    Route::get('/', [\App\Http\Controllers\Backend\Reports\EmployeeReportController::class, 'index'])
        ->name('employee-reports.index');

    // Employee Directory
    Route::post('/employee-directory', [\App\Http\Controllers\Backend\Reports\EmployeeReportController::class, 'getEmployeeDirectoryReport'])
        ->name('employee-reports.employee-directory-report');
    Route::post('/employee-directory/export', [\App\Http\Controllers\Backend\Reports\EmployeeReportController::class, 'exportEmployeeDirectory'])
        ->name('employee-reports.export-employee-directory');
    Route::post('/employee-directory/export-pdf', [\App\Http\Controllers\Backend\Reports\EmployeeReportController::class, 'exportEmployeeDirectoryPdf'])
        ->name('employee-reports.export-employee-directory-pdf');

    // Attendance
    Route::post('/attendance', [\App\Http\Controllers\Backend\Reports\EmployeeReportController::class, 'getAttendanceReport'])
        ->name('employee-reports.attendance-report');
    Route::post('/attendance/export', [\App\Http\Controllers\Backend\Reports\EmployeeReportController::class, 'exportAttendance'])
        ->name('employee-reports.export-attendance');
    Route::post('/attendance/export-pdf', [\App\Http\Controllers\Backend\Reports\EmployeeReportController::class, 'exportAttendancePdf'])
        ->name('employee-reports.export-attendance-pdf');
});

Route::post('employee-reports/overtime-report', [EmployeeReportController::class, 'getOvertimeReport'])->name('employee-reports.overtime-report');
Route::post('employee-reports/export-overtime', [EmployeeReportController::class, 'exportOvertime'])->name('employee-reports.export-overtime');
Route::post('employee-reports/export-overtime-pdf', [EmployeeReportController::class, 'exportOvertimePdf'])->name('employee-reports.export-overtime-pdf');

Route::prefix('reports')->group(function () {
    // AJAX route for DataTables (returns JSON)
    Route::post('/schedule/data', [EmployeeReportController::class, 'getScheduleReport'])
        ->name('employee-reports.schedule-report');

    // Export Schedule Report as CSV
    Route::post('/schedule/export/csv', [EmployeeReportController::class, 'exportScheduleReport'])
        ->name('employee-reports.export-schedule-report');

    // Export Schedule Report as PDF
    Route::post('/schedule/export/pdf', [EmployeeReportController::class, 'exportScheduleReportPdf'])
        ->name('employee-reports.export-schedule-report-pdf');
});

Route::post('leave-report', [EmployeeReportController::class, 'getLeaveReport'])->name('employee-reports.leave-report');
    Route::post('export-leave-report', [EmployeeReportController::class, 'exportLeaveReport'])->name('employee-reports.export-leave-report');
    Route::post('export-leave-report-pdf', [EmployeeReportController::class, 'exportLeaveReportPdf'])->name('employee-reports.export-leave-report-pdf');

    // Testing Reports Routes
Route::prefix('testing/reports')->group(function() {
    Route::get('/', [TestingReportController::class, 'index'])->name('testing.reports.index');
    Route::post('/generate', [TestingReportController::class, 'generateReport'])->name('testing.reports.generate');
    Route::get('/metrics', [TestingReportController::class, 'getMetrics'])->name('testing.reports.metrics');
});
    

Route::get('testing/tickets/datatable', [TestingController::class, 'datatable'])->name('testing.tickets.datatable');
// Add this to your routes/web.php
Route::get('/testing/tickets/{id}', [TestingReportController::class, 'viewTicket'])
    ->name('testing.tickets.view');


// ... your other routes

Route::prefix('reports/accounts')->name('accounts-reports.')->group(function () {
    Route::get('/', [AccountsReportController::class, 'index'])->name('index');

    // Estimates
    Route::post('/estimates', [AccountsReportController::class, 'getEstimateReport'])->name('estimates');
    Route::post('/export/estimates', [AccountsReportController::class, 'exportEstimates'])->name('export-estimates');
    Route::post('/export/estimates/pdf', [AccountsReportController::class, 'exportEstimatesPdf'])->name('export-estimates-pdf');

    // Expenses
    Route::post('/expenses', [AccountsReportController::class, 'getExpenseReport'])->name('expenses');
    Route::post('/export/expenses', [AccountsReportController::class, 'exportExpenses'])->name('export-expenses');
    Route::post('/export/expenses/pdf', [AccountsReportController::class, 'exportExpensesPdf'])->name('export-expenses-pdf');

    // Invoices
    Route::post('/invoices', [AccountsReportController::class, 'getInvoiceReport'])->name('invoices');
    Route::post('/export/invoices', [AccountsReportController::class, 'exportInvoices'])->name('export-invoices');
    Route::post('/export/invoices/pdf', [AccountsReportController::class, 'exportInvoicesPdf'])->name('export-invoices-pdf');

    // Payments
    Route::post('/payments', [AccountsReportController::class, 'getPaymentReport'])->name('payments');
    Route::post('/export/payments', [AccountsReportController::class, 'exportPayments'])->name('export-payments');
    Route::post('/export/payments/pdf', [AccountsReportController::class, 'exportPaymentsPdf'])->name('export-payments-pdf');

     // Budget Expense routes
    Route::post('budget-expenses', [AccountsReportController::class, 'getBudgetExpenseReport'])->name('budget-expenses');
    Route::post('export-budget-expenses', [AccountsReportController::class, 'exportBudgetExpenses'])->name('export-budget-expenses');
    Route::post('export-budget-expenses-pdf', [AccountsReportController::class, 'exportBudgetExpensesPdf'])->name('export-budget-expenses-pdf');
});
// Budget Revenue Report
Route::post('reports/budget-revenue', [AccountsReportController::class, 'getBudgetRevenueReport'])->name('accounts-reports.budget-revenue');
Route::post('reports/export-budget-revenue', [AccountsReportController::class, 'exportBudgetRevenue'])->name('accounts-reports.export-budget-revenue');
Route::post('reports/export-budget-revenue-pdf', [AccountsReportController::class, 'exportBudgetRevenuePdf'])->name('accounts-reports.export-budget-revenue-pdf');

Route::prefix('onboarding')->group(function () {
    Route::get('/', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::get('/create', [OnboardingController::class, 'create'])->name('onboarding.create');
    Route::post('/', [OnboardingController::class, 'store'])->name('onboarding.store');
    Route::get('/{onboarding}/edit', [OnboardingController::class, 'edit'])->name('onboarding.edit');
    Route::put('/{onboarding}', [OnboardingController::class, 'update'])->name('onboarding.update');
    Route::delete('/{onboarding}', [OnboardingController::class, 'destroy'])->name('onboarding.destroy');
    Route::get('/{onboarding}/download-pdf', [OnboardingController::class, 'downloadPdf'])->name('onboarding.downloadPdf');
    Route::resource('onboarding', OnboardingController::class);
//
});

// Resignation Routes
Route::resource('resignation', ResignationController::class);
Route::get('resignation/{resignation}/pdf', [ResignationController::class, 'downloadPdf'])->name('resignation.downloadPdf');
Route::get('/resignations/{resignation}/download-pdf', [ResignationController::class, 'downloadPdf'])->name('resignation.download-pdf');

// Feedback Form Routes

    Route::resource('feedback', FeedbackformController::class);
    Route::get('feedback/success', [FeedbackformController::class, 'success'])->name('feedback.success'); // New route for success page
    Route::get('feedback-analytics', [FeedbackformController::class, 'analytics'])->name('feedback.analytics');




Route::get('/client-reports', [ClientreportController::class, 'index'])->name('client-reports.index');
Route::get('/client-reports/export/csv', [ClientreportController::class, 'exportCsv'])->name('client-reports.export.csv');
Route::get('/client-reports/export/pdf', [ClientreportController::class, 'exportPdf'])->name('client-reports.export.pdf');


Route::get('/employee-salary-reports', [EmployeeSalaryreportController::class, 'index'])->name('employee-salary-reports.index');
Route::get('/employee-salary-reports/export/csv', [EmployeeSalaryreportController::class, 'exportCsv'])->name('employee-salary-reports.export.csv');
Route::get('/employee-salary-reports/export/pdf', [EmployeeSalaryreportController::class, 'exportPdf'])->name('employee-salary-reports.export.pdf');
Route::post('/attendance/capture', [AttendanceController::class, 'captureAttendance'])->name('attendance.capture');
Route::get('projects/{id}/download', [ProjectController::class, 'download'])->name('projects.download');
Route::get('/test-checkin', [TestCheckinController::class, 'index'])->name('test-checkin.index');
Route::post('/test-checkin/store', [TestCheckinController::class, 'store'])->name('test-checkin.store');
Route::get('/test-checkin/history', [TestCheckinController::class, 'history'])->name('test-checkin.history');

Route::get('/checkins', [TestCheckinController::class, 'showCheckins'])->name('checkins.index');
Route::get('/get-designations-by-department/{department_id}', [PromotionController::class, 'getDesignationsByDepartment'])
    ->name('get-designations-by-department');
    Route::get('/api/getEmployeeProjects/{employeeId}', [TimesheetController::class, 'getEmployeeProjects']);
    Route::delete('/allemployees/truncate', [AllEmployeeController::class, 'truncate'])
    ->name('allemployees.truncate');
  // web.php
Route::get('subcompany', [SuscribeController::class, 'subscribedCompanies'])->name('sub.index');
Route::patch('subscribecompany/{id}/status', [SuscribeController::class, 'updateCompanyStatus'])->name('subscribecompany.updateStatus');




// Resignation Routes
    Route::get('/resignations', [ResignationController::class, 'index'])->name('resignation.index');
    Route::get('/resignations/create', [ResignationController::class, 'create'])->name('resignation.create');
    Route::post('/resignations', [ResignationController::class, 'store'])->name('resignation.store');
    Route::get('/resignations/{resignation}', [ResignationController::class, 'show'])->name('resignation.show');
    Route::get('/resignations/{resignation}/edit', [ResignationController::class, 'edit'])->name('resignation.edit');
    Route::put('/resignations/{resignation}', [ResignationController::class, 'update'])->name('resignation.update');
    Route::delete('/resignations/{resignation}', [ResignationController::class, 'destroy'])->name('resignation.destroy');
    Route::get('/resignations/{resignation}/download-pdf', [ResignationController::class, 'downloadPdf'])->name('resignation.download-pdf');
    
    // Status Management Routes
    Route::post('/resignations/{resignation}/update-status', [ResignationController::class, 'updateStatus'])->name('resignation.update-status');
    Route::post('/resignations/{resignation}/approve', [ResignationController::class, 'approve'])->name('resignation.approve');
    Route::post('/resignations/{resignation}/reject', [ResignationController::class, 'reject'])->name('resignation.reject');

    // Task routes
Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');
Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
Route::post('/tasks/{id}/start-work', [TaskController::class, 'startWork'])->name('tasks.start-work');
Route::post('/tasks/{id}/end-work', [TaskController::class, 'endWork'])->name('tasks.end-work');
Route::get('/tasks/{id}/work-history', [TaskController::class, 'getWorkHistory'])->name('tasks.work-history');
// Medical certificate routes
Route::get('/admin-leaves/{id}/download-medical-certificate', [AdminLeavesController::class, 'downloadMedicalCertificate'])
    ->name('admin-leaves.download-medical-certificate');

Route::get('/admin-leaves/{id}/view-medical-certificate', [AdminLeavesController::class, 'viewMedicalCertificate'])
    ->name('admin-leaves.view-medical-certificate');
    
  // routes/web.php
Route::get('/download-template', [AllEmployeeController::class, 'downloadTemplate'])->name('employee.downloadTemplate');
Route::post('/import', [AllEmployeeController::class, 'importEmployees'])->name('employee.importEmployees');

Route::get('/download-template-client', [ClientController::class, 'downloadTemplate'])->name('client.downloadTemplate');
Route::post('/client/import', [ClientController::class, 'importClients'])->name('client.importClients');
Route::get('/my-tasks', [TaskController::class, 'myTasks'])->name('tasks.my');

    // Start/Complete timesheet actions (employee-only)
    Route::post('/timesheets/start', [TimesheetController::class, 'start'])->name('timesheets.start');
    Route::post('/timesheets/complete', [TimesheetController::class, 'complete'])->name('timesheets.complete');
    // Send AMC renewal reminders
    Route::get('/clients/send-amc-reminders', [ClientController::class, 'sendAMCReminders'])->name('sendAMCReminders');



//hike letter routes
Route::resource('hike-letter', HikeLetterController::class);
Route::get('hike-letter/{id}/preview', [HikeLetterController::class, 'preview'])->name('hike-letter.preview');
Route::get('hike-letter/{id}/generate-pdf', [HikeLetterController::class, 'generatePDF'])->name('hike-letter.generate-pdf');
Route::post('/salary/{id}/send-hike-letter', [EmployeeSalaryController::class, 'sendHikeLetter'])->name('salary.send-hike-letter');



Route::get('/memo', [MemoController::class, 'index'])->name('memo.index');
Route::get('/memo/create', [MemoController::class, 'create'])->name('memo.create');
Route::post('/memo/store', [MemoController::class, 'store'])->name('memo.store');
Route::get('/memo/{id}/edit', [MemoController::class, 'edit'])->name('memo.edit');
Route::post('/memo/{id}/update', [MemoController::class, 'update'])->name('memo.update');
Route::delete('/memo/{id}', [MemoController::class, 'destroy'])->name('memo.destroy');
Route::get('/memo/{id}/preview', [MemoController::class, 'preview'])->name('memo.preview');
Route::post('/memo/preview-draft', [MemoController::class, 'previewDraft'])->name('memo.preview-draft');
Route::get('/employee/{id}/send-memo', [MemoController::class, 'sendMemo'])->name('employee.sendMemo');
// Send memo to an employee
Route::get('/employee/{id}/send-memo', [MemoController::class, 'sendMemo'])->name('employee.sendMemo');




use App\Http\Controllers\FontMasterController;

Route::get('/font-master', [FontMasterController::class, 'index'])->name('fontmaster.index');
Route::post('/font-master/update', [FontMasterController::class, 'update'])->name('fontmaster.update');
Route::post('/hrms/jobs/add-resume/{id}/send-appointment-letter', [AddresumeController::class, 'sendAppointmentLetter'])->name('add-resume.sendAppointmentLetter');
Route::get('/hrms/jobs/add-resume/{id}/debug-template', [AddresumeController::class, 'debugTemplate']);
Route::post('/database/import', [AllEmployeeController::class, 'importSql'])->name('database.import');
Route::post('/salary/{id}/send-hike-letter', [EmployeeSalaryController::class, 'sendHikeLetter'])
    ->name('salary.send-hike-letter');

    Route::put('/clients/update-amc-reminder', [ClientController::class, 'updateAmcReminder'])->name('client.updateAmcReminder');
// Break management routes
Route::post('/timesheets/break-start', [TimesheetController::class, 'startBreak'])->name('timesheets.break-start');
Route::post('/timesheets/break-end', [TimesheetController::class, 'endBreak'])->name('timesheets.break-end');
// ... existing code ...

// <CHANGE> Add these new routes for dynamic job title and skills fetching
Route::post('/managejobs/get-job-titles', [ManagejobsController::class, 'getJobTitlesByDepartment'])->name('managejobs.getJobTitlesByDepartment');
Route::post('/managejobs/get-skills', [ManagejobsController::class, 'getSkillsByJobTitle'])->name('managejobs.getSkillsByJobTitle');

// ... existing code ...
// <CHANGE> Add routes for fetching managers and team leads by department
Route::get('/get-managers-by-department/{departmentId}', [AllEmployeeController::class, 'getManagersByDepartment'])
    ->name('employee.getManagersByDepartment');

Route::get('/get-team-leads-by-department/{departmentId}', [AllEmployeeController::class, 'getTeamLeadsByDepartment'])
    ->name('employee.getTeamLeadsByDepartment');

Route::post('/employee/bulk-delete', [AllEmployeeController::class, 'bulkDelete'])->name('employee.bulkDelete');
Route::post('/employee/bulk-restore', [AllEmployeeController::class, 'bulkRestore'])->name('employee.bulkRestore');
Route::post('/employee/bulk-permanent-delete', [AllEmployeeController::class, 'bulkPermanentDelete'])->name('employee.bulkPermanentDelete');
// routes/web.php - Temporary debug version
Route::get('/auto-reminders', function() {
    \Log::info('DEBUG - All request data:', [
        'all_parameters' => request()->all(),
        'key_parameter' => request('key'),
        'query_string' => request()->getQueryString(),
        'full_url' => request()->fullUrl()
    ]);
    
    // Temporary: Allow without key for testing
    $result = Artisan::call('reminder:amc-renewal');
    $output = Artisan::output();
    
    \Log::info("AMC reminders executed. Result: $result", ['output' => $output]);
    
    return response()->json([
        'status' => 'success',
        'executed_at' => now()->toDateTimeString(),
        'result' => $result,
        'output' => $output,
        'debug' => [
            'key_received' => request('key'),
            'all_params' => request()->all()
        ]
    ]);
});

Route::get('/tasks/employee/list', [TaskController::class, 'employeeProjectTasks'])
    ->name('tasks.employeeProjectTasks');
    Route::get('/employee-project-tasks', [TaskController::class, 'employeeProjectTasks'])->name('employee.project.tasks');
    Route::post('/email-config-setting-update', [SettingsController::class, 'emailConfigSettingUpdate'])->name('email-config-setting-update');
    Route::post('/employee-permissions', [EmployeeLeavesController::class, 'storePermission'])->name('employee-permissions.store');
    // Permission routes
    // Add this route
Route::get('/employee-permissions/create', [EmployeeLeavesController::class, 'createPermission'])->name('employee-permissions.create');
Route::post('/employee-permissions', [EmployeeLeavesController::class, 'storePermission'])->name('employee-permissions.store');
Route::get('/employee-permissions/{id}/edit', [EmployeeLeavesController::class, 'editPermission'])->name('employee-permissions.edit');
Route::put('/employee-permissions/{id}', [EmployeeLeavesController::class, 'updatePermission'])->name('employee-permissions.update');
Route::delete('/employee-permissions/{id}', [EmployeeLeavesController::class, 'destroyPermission'])->name('employee-permissions.destroy');
// Admin Permission Routes
Route::put('/admin-permissions/{id}/update-status', [AdminLeavesController::class, 'updatePermissionStatus'])->name('admin-permissions.update-status');
Route::get('/admin-permissions/{id}', [AdminLeavesController::class, 'showPermission'])->name('admin-permissions.show');
Route::post('/leave-settings/update-permission-hours', [LeavesSettingsController::class, 'updatePermissionHours'])->name('leave-settings.update-permission-hours');
Route::post('/leave-settings/update-max-allowed', [LeavesSettingsController::class, 'updateMaxAllowed'])
    ->name('leave-settings.update-max-allowed');
// Employee Salary Routes within Employee
Route::post('/employee/calculate-salary', [AllEmployeeController::class, 'calculateSalaryComponents'])->name('employee.calculateSalary');
Route::get('/employee/get-tds-percentage', [AllEmployeeController::class, 'getTdsPercentage'])->name('employee.getTdsPercentage');
// Add this route to your existing routes
Route::get('/get-managers', [AllEmployeeController::class, 'getManagers'])->name('get.managers');
    Route::get('/employee_expenses/index', [EmployeeExpenseController::class, 'index'])->name('employee_expenses.index');
    Route::get('/employee_expenses/create', [EmployeeExpenseController::class, 'create'])->name('employee_expenses.create');
    Route::post('/employee_expenses/index', [EmployeeExpenseController::class, 'store'])->name('employee_expenses.store');
    Route::get('/employee_expenses/{id}', [EmployeeExpenseController::class, 'show'])->name('employee_expenses.show');
    Route::delete('/employee_expenses/{id}', [EmployeeExpenseController::class, 'destroy'])->name('employee_expenses.destroy');
    Route::get('/employee-expenses/pending', [EmployeeExpenseController::class, 'pendingExpenses'])->name('employee_expenses.pending');
    Route::get('/employee-expenses/approve/{id}', [EmployeeExpenseController::class, 'approve'])->name('employee_expenses.approve');
    Route::get('/employee-expenses/reject/{id}', [EmployeeExpenseController::class, 'reject'])->name('employee_expenses.reject');
 // Status update route
Route::put('/employee_expenses/status/{id}', [EmployeeExpenseController::class, 'updateStatus'])
    ->name('employee_expenses.updateStatus');

    // Edit - show edit form
Route::get('employee_expenses/{id}/edit', [EmployeeExpenseController::class, 'edit'])->name('employee_expenses.edit');

// Update - update existing expense
Route::put('employee_expenses/{id}', [EmployeeExpenseController::class, 'update'])->name('employee_expenses.update');
Route::resource('background-verification', BackgroundVerificationController::class);
Route::post('background-verification/{id}/update-status', [BackgroundVerificationController::class, 'updateStatus'])->name('background-verification.update-status');
Route::post('background-verification/{id}/send-appointment-letter', [BackgroundVerificationController::class, 'sendAppointmentLetter'])->name('background-verification.send-appointment-letter');
// Custom route for getting employees by department for goals
Route::get('/goals/get-employees-by-department', [GoalController::class, 'getEmployeesByDepartment'])
    ->name('goals.get-employees-by-department');
    Route::resource('goals', GoalController::class);
    Route::group(['prefix' => 'goals'], function () {
        // Main CRUD Routes
        Route::get('/', [GoalController::class, 'index'])->name('goals.index');
        Route::get('/create', [GoalController::class, 'create'])->name('goals.create');
        Route::post('/', [GoalController::class, 'store'])->name('goals.store');
        Route::get('/{id}', [GoalController::class, 'show'])->name('goals.show');
        Route::get('/{id}/edit', [GoalController::class, 'edit'])->name('goals.edit');
        Route::put('/{id}', [GoalController::class, 'update'])->name('goals.update');
        Route::delete('/{id}', [GoalController::class, 'destroy'])->name('goals.destroy');
        
        // Additional Functionality Routes
        Route::get('/get-employees-by-department', [GoalController::class, 'getEmployeesByDepartment'])->name('goals.get-employees-by-department');
        Route::post('/{id}/update-progress', [GoalController::class, 'updateProgress'])->name('goals.update-progress');
        Route::post('/{id}/update-ratings', [GoalController::class, 'updateRatings'])->name('goals.update-ratings');
        Route::post('/change-status', [GoalController::class, 'changeStatus'])->name('goals.change-status');
    });
    Route::post('/taskboard/assign-tester', [TaskboardController::class, 'assignTester'])->name('taskboard.assignTester');




Route::prefix('invoice-templates')->group(function () {
    Route::get('/', [InvoiceTemplateController::class, 'index'])->name('invoice-template.index');
    Route::get('/create', [InvoiceTemplateController::class, 'create'])->name('invoice-template.create');
    Route::post('/', [InvoiceTemplateController::class, 'store'])->name('invoice-template.store');
    Route::get('/{id}/edit', [InvoiceTemplateController::class, 'edit'])->name('invoice-template.edit');
    Route::put('/{id}', [InvoiceTemplateController::class, 'update'])->name('invoice-template.update');
    Route::delete('/{id}', [InvoiceTemplateController::class, 'destroy'])->name('invoice-template.destroy');
    Route::get('/{id}/preview', [InvoiceTemplateController::class, 'preview'])->name('invoice-template.preview');
    Route::get('/{id}/generate-pdf', [InvoiceTemplateController::class, 'generatePDF'])->name('invoice-template.generate-pdf');
});



    
Route::resource('payroll-template', PayrollTemplateController::class);
Route::get('payroll-template/{id}/preview', [PayrollTemplateController::class, 'preview'])->name('payroll-template.preview');





        Route::prefix('appointment-letter')->name('appointment-letter.')->group(function () {
            Route::get('/', [AppointmentLetterController::class, 'index'])->name('index');
            Route::get('/create', [AppointmentLetterController::class, 'create'])->name('create');
            Route::post('/store', [AppointmentLetterController::class, 'store'])->name('store');
        
            // Place dynamic {id} routes AFTER all static ones
            Route::get('/{id}/preview', [AppointmentLetterController::class, 'preview'])->name('preview');
            Route::get('/{id}/edit', [AppointmentLetterController::class, 'edit'])->name('edit');
            Route::get('/{id}', [AppointmentLetterController::class, 'show'])->name('show');
            Route::put('/{id}', [AppointmentLetterController::class, 'update'])->name('update');
            Route::delete('/{id}', [AppointmentLetterController::class, 'destroy'])->name('destroy');
        });
// offerletter
Route::post('offer-letter/{id}/set-active', [OfferletterController::class, 'setActive'])->name('offer-letter.set-active');
Route::post('offer-letter/{id}/set-inactive', [OfferletterController::class, 'setInactive'])->name('offer-letter.set-inactive');
Route::get('offer-letter/preview/{id}', [OfferletterController::class, 'preview'])->name('offer-letter.preview');

// Under hrms/master prefix
Route::prefix('hrms/master')->group(function () {
    Route::resource('offer-letter', App\Http\Controllers\Backend\master\OfferletterController::class);
});


// Testing Tickets Routes
Route::prefix('testing')->name('testing.')->group(function () {
    Route::get('/', [TestingController::class, 'index'])->name('index');
    Route::get('/create', [TestingController::class, 'create'])->name('create');
    Route::post('/store', [TestingController::class, 'store'])->name('store');
    Route::get('/show/{id}', [TestingController::class, 'show'])->name('show');
    Route::get('/edit/{id}', [TestingController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [TestingController::class, 'update'])->name('update');
    Route::get('/project-team-members/{id}', [TestingController::class, 'getProjectTeamMembers'])->name('getProjectTeamMembers');
    Route::put('/update-status/{id}', [TestingController::class, 'updateStatus'])->name('updateStatus');
    Route::put('/update-priority/{id}', [TestingController::class, 'updatePriority'])->name('updatePriority');
    Route::put('/update-assignment/{id}', [TestingController::class, 'updateAssignment'])->name('updateAssignment');
    Route::delete('/destroy/{id}', [TestingController::class, 'destroy'])->name('destroy');
    
});
Route::get('testing/tickets/datatable', [TestingController::class, 'datatable'])->name('testing.tickets.datatable');
// Add this to your routes/web.php
//Question

Route::resource('Question', QuestionController::class);
Route::get('/questions/download-pdf', [QuestionController::class, 'downloadPdf'])->name('Question.downloadPdf');
// Training Management Routes
Route::prefix('trainings')->name('trainings.')->group(function () {
    Route::get('/', [TrainingManagementController::class, 'index'])->name('index');
    Route::get('/edit/{id}', [TrainingManagementController::class, 'editEmployee'])->name('editEmployee');
    Route::put('/update/{id}', [TrainingManagementController::class, 'updateEmployee'])->name('updateEmployee');
    Route::patch('/status/{id}', [TrainingManagementController::class, 'updateEmployeeStatus'])->name('updateEmployeeStatus');
    Route::post('/employee/{employeeId}/resource/{resourceId}/complete', [TrainingManagementController::class, 'markResourceCompleted'])->name('markResourceCompleted');
    Route::delete('/employee/{employeeId}/resource/{resourceId}', [TrainingManagementController::class, 'deleteResource'])->name('deleteResource');
    Route::get('/employee/{employeeId}/progress', [TrainingManagementController::class, 'getTrainingProgress'])->name('getTrainingProgress');
    Route::post('/{employeeId}/feedback/{trainingId}', [TrainingManagementController::class, 'submitFeedback'])->name('trainings.submitFeedback');
Route::get('/{employeeId}/{trainingId}/feedback-form', [TrainingManagementController::class, 'showFeedbackForm'])->name('trainings.feedbackForm');


});
// Assets
Route::resource('assets',AssetsController::class);
Route::post('/assets/{id}/update-status', [AssetsController::class, 'updateStatus']);
Route::get('/assets/get-employee-department', [AssetsController::class, 'getEmployeeDepartment'])->name('assets.getEmployeeDepartment');
// AppointmentLetter
Route::prefix('appointment-letter')->name('appointment-letter.')->group(function () {
    Route::get('/', [AppointmentLetterController::class, 'index'])->name('index');
    Route::get('/create', [AppointmentLetterController::class, 'create'])->name('create');
    Route::post('/store', [AppointmentLetterController::class, 'store'])->name('store');
    Route::get('/{id}', [AppointmentLetterController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [AppointmentLetterController::class, 'edit'])->name('edit');
    Route::put('/{id}', [AppointmentLetterController::class, 'update'])->name('update');
    Route::delete('/{id}', [AppointmentLetterController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/preview', [AppointmentLetterController::class, 'preview'])->name('preview');
});

// Send appointment letter from jobs
Route::post('/hrms/jobs/add-resume/{id}/send-appointment-letter', [AddresumeController::class, 'sendAppointmentLetter'])->name('add-resume.sendAppointmentLetter');
Route::post('background-verification/{id}/send-appointment-letter', [BackgroundVerificationController::class, 'sendAppointmentLetter'])->name('background-verification.send-appointment-letter');
// routes/web.php
Route::prefix('clearance')->group(function () {
    Route::get('/', [ClearanceController::class, 'index'])->name('clearance.index');
    Route::get('/{id}', [ClearanceController::class, 'show'])->name('clearance.show');
    Route::post('/{id}/update-status', [ClearanceController::class, 'updateStatus'])->name('clearance.update-status');
    Route::post('/bulk-update', [ClearanceController::class, 'bulkUpdate'])->name('clearance.bulk-update');
    Route::get('/export/report', [ClearanceController::class, 'exportReport'])->name('clearance.export-report');
});
Route::post('/clearance/{id}/bulk-update-single', [ClearanceController::class, 'bulkUpdateSingle'])->name('clearance.bulk-update-single');

Route::get('/reports/all', function () {
    return view('hrms.hr.Reports.index');
})->name('reports.all');        

// Assets Management Routes
Route::resource('assets', \App\Http\Controllers\Backend\Administration\AssetsManagementController::class);
Route::get('assets/{id}/check-assignments', [\App\Http\Controllers\Backend\Administration\AssetsManagementController::class, 'checkAssignments'])->name('assets-management.check-assignments');

// Assets Assignment Routes
Route::resource('assets-assignment', \App\Http\Controllers\Backend\Administration\AssetsAssignmentController::class);
Route::post('assets-assignment/{id}/return', [\App\Http\Controllers\Backend\Administration\AssetsAssignmentController::class, 'markReturned'])->name('assets-assignment.return');

Route::get('/statutory', [StatutoryController::class, 'index'])->name('statutory.index');
Route::post('/statutory/update', [StatutoryController::class, 'update'])->name('statutory.update');
// PF & ESI Filing Module
Route::prefix('statutory-filing')->group(function () {
    
    Route::get('/', [App\Http\Controllers\Backend\master\StatutoryFilingController::class, 'index'])
        ->name('statutory.filing.index');

    Route::get('/pf-ecr', [App\Http\Controllers\Backend\master\StatutoryFilingController::class, 'generatePF'])
        ->name('statutory.filing.pf');

    Route::get('/esi-return', [App\Http\Controllers\Backend\master\StatutoryFilingController::class, 'generateESI'])
        ->name('statutory.filing.esi');
});
// PF Challan
Route::prefix('pf-challans')->group(function () {
    Route::get('/', [StatutoryChallanController::class, 'pfIndex'])->name('pf.challan.index');
    Route::get('/create', [StatutoryChallanController::class, 'pfCreate'])->name('pf.challan.create');
    Route::post('/store', [StatutoryChallanController::class, 'pfStore'])->name('pf.challan.store');
    Route::get('/edit/{id}', [StatutoryChallanController::class, 'pfEdit'])->name('pf.challan.edit');
    Route::post('/update/{id}', [StatutoryChallanController::class, 'pfUpdate'])->name('pf.challan.update');
    // PF Delete
Route::delete('/delete/{id}', [StatutoryChallanController::class, 'pfDelete'])
->name('pf.challan.delete');
Route::get('/download/{id}', [StatutoryChallanController::class, 'pfDownload'])
    ->name('pf.challan.download');

});

// ESI Challan
Route::prefix('esi-challans')->group(function () {
    Route::get('/', [StatutoryChallanController::class, 'esiIndex'])->name('esi.challan.index');
    Route::get('/create', [StatutoryChallanController::class, 'esiCreate'])->name('esi.challan.create');
    Route::post('/store', [StatutoryChallanController::class, 'esiStore'])->name('esi.challan.store');
    Route::get('/edit/{id}', [StatutoryChallanController::class, 'esiEdit'])->name('esi.challan.edit');
    Route::post('/update/{id}', [StatutoryChallanController::class, 'esiUpdate'])->name('esi.challan.update');
    // ESI Delete
Route::delete('/delete/{id}', [StatutoryChallanController::class, 'esiDelete'])
->name('esi.challan.delete');

Route::get('/download/{id}', [StatutoryChallanController::class, 'esiDownload'])
    ->name('esi.challan.download');

});
// Employee PF & ESI View (ESS)
Route::get('/statutory-information', [\App\Http\Controllers\Backend\master\EmployeeStatutoryController::class, 'index'])
    ->name('employee.statutory');
// Admin PF & ESI Reports
Route::get('admin/statutory-reports', [
    \App\Http\Controllers\Backend\Reports\AdminStatutoryReportController::class,
    'index'
])->name('admin.statutory.reports');

// Excel export
Route::get('admin/statutory-reports/export', [
    \App\Http\Controllers\Backend\Reports\AdminStatutoryReportController::class,
    'export'
])->name('admin.statutory.reports.export');
// TDS Listing Page
Route::get('/tds', [TdsController::class, 'index'])->name('tds.index');

// Edit TDS Page
Route::get('/tds/edit/{id}', [TdsController::class, 'edit'])->name('tds.edit');

// Update TDS
Route::post('/tds/update/{id}', [TdsController::class, 'update'])->name('tds.update');
Route::get('/tds/calculate-all/{financialYear}', [TdsController::class, 'calculateAllTds'])
    ->name('tds.calculateAll');
    Route::get('/operations', function () {
        return view('hrms.operations.index');
    })->name('operations.index');
    Route::post('/employee/shifts/ajax', [EmployeeLeavesController::class, 'getShiftData'])->name('employee.shifts.ajax');
    Route::get('/employee-leaves/filter', [EmployeeLeavesController::class, 'ajaxFilter'])
    ->name('employee-leaves.filter');
    Route::get('/team-leaves', [TeamLeaveController::class, 'index'])->name('team-leaves.index');
    Route::get('/team-leaves/{employee}', [TeamLeaveController::class, 'show'])->name('team-leaves.show');
    Route::post('/team-leaves/{employee}/apply-leave', [TeamLeaveController::class, 'applyLeaveForMember'])->name('team-leaves.apply-leave');
    Route::get('/team-leaves/{employee}/apply-leave-form', [TeamLeaveController::class, 'applyLeaveForm'])->name('team-leaves.apply-leave-form');

    Route::get('/team-leaves/departments', [TeamLeaveController::class, 'getDepartments'])
    ->name('team-leaves.departments');

    // Update status routes mapping directly to TeamLeaveController
Route::put('admin-leaves/{id}/update-status', [TeamLeaveController::class, 'updateLeaveStatus']);
Route::put('admin-permissions/{id}/update-status', [TeamLeaveController::class, 'updatePermissionStatus'])->name('admin-permissions.update-status');

    Route::post('/admin-leaves/bulk-update', [AdminLeavesController::class, 'bulkLeaveUpdate']);
Route::post('/admin-permissions/bulk-update', [AdminLeavesController::class, 'bulkPermissionUpdate']);
Route::get('/time-tracker', [TimeTrackerController::class, 'index'])
    ->name('time-tracker.index');
// To:
Route::delete('/projects-multiDelete', [ProjectController::class, 'multiDelete'])
    ->name('projects.multiDelete');
    Route::post('/client-bulkdelete', [ClientController::class, 'bulkDelete'])
     ->name('client.bulkDelete');
     Route::get('/projectdetails/{projectid}', 
     [ProjectDetailController::class, 'index']
 )->name('projects.projectdetails');
 Route::post('/projectdetails/feed/send', [ProjectDetailController::class, 'sendFeed']);
Route::get('/projectdetails/{projectid}/feeds', [ProjectDetailController::class, 'loadFeeds']);
Route::post('/projectdetails/{projectid}/tasks', [ProjectDetailController::class, 'loadTasks'])->name('projectdetails.tasks.load');// Offboarding Routes
Route::prefix('offboarding')->group(function () {
    Route::get('/', [OffboardingController::class, 'index'])->name('offboarding.index');
    Route::get('/create', [OffboardingController::class, 'create'])->name('offboarding.create');
    Route::post('/', [OffboardingController::class, 'store'])->name('offboarding.store');
    Route::get('/{id}', [OffboardingController::class, 'show'])->name('offboarding.show');
    Route::get('/{id}/edit', [OffboardingController::class, 'edit'])->name('offboarding.edit');
    Route::put('/{id}', [OffboardingController::class, 'update'])->name('offboarding.update');
    Route::delete('/{id}', [OffboardingController::class, 'destroy'])->name('offboarding.destroy');
    Route::post('/clearance/{id}', [OffboardingController::class, 'updateClearance'])->name('offboarding.clearance.update');
    Route::get('/api/employees', [OffboardingController::class, 'getEmployees'])->name('offboarding.employees');
    // Clearance routes
Route::post('/offboarding/{id}/clearance', [OffboardingController::class, 'storeClearance'])->name('offboarding.clearance.store');
Route::put('/offboarding/clearance/{id}', [OffboardingController::class, 'updateClearance'])->name('offboarding.clearance.update');
Route::delete('/offboarding/clearance/{id}', [OffboardingController::class, 'destroyClearance'])->name('offboarding.clearance.destroy');

// Document routes
Route::post('/offboarding/{id}/document', [OffboardingController::class, 'uploadDocument'])->name('offboarding.document.upload');




});


// Clearance Routes
Route::post('/offboarding/{id}/clearance/store', [OffboardingController::class, 'storeClearance'])->name('offboarding.clearance.store');
Route::put('/offboarding/clearance/{id}/update', [OffboardingController::class, 'updateClearance'])->name('offboarding.clearance.update');
Route::put('/offboarding/clearance/{id}/it', [OffboardingController::class, 'updateItClearance'])->name('offboarding.clearance.it');
Route::put('/offboarding/clearance/{id}/hr', [OffboardingController::class, 'updateHrClearance'])->name('offboarding.clearance.hr');
Route::put('/offboarding/clearance/{id}/admin', [OffboardingController::class, 'updateAdminClearance'])->name('offboarding.clearance.admin');
Route::delete('/offboarding/clearance/{id}', [OffboardingController::class, 'destroyClearance'])->name('offboarding.clearance.destroy');
Route::post('/offboarding/clearance/{id}/assign', [OffboardingController::class, 'assignClearance'])->name('offboarding.clearance.assign');

Route::get('/offboarding/career-history', [OffboardingController::class, 'careerHistory'])->name('reports.careerhistory');
// Letters Main Page
Route::get('/letters', [LettersController::class, 'index'])->name('letters.index');
Route::post('/letters/signature-master', [LettersController::class, 'storeSignature'])->name('letters.signature-master.store');

// Templates Resource
Route::resource('templates', QuestionTemplateController::class);

// Metrics
Route::post('templates/{template}/metrics', [QuestionTemplateController::class, 'addMetric'])
    ->name('templates.metrics.store');
Route::delete('metrics/{metric}', [QuestionTemplateController::class, 'deleteMetric'])
    ->name('metrics.destroy');

// Questions
Route::post('templates/{template}/questions', [QuestionTemplateController::class, 'addQuestion'])
    ->name('templates.questions.store');
Route::put('questions/{question}', [QuestionTemplateController::class, 'updateQuestion'])
    ->name('questions.update');
Route::delete('questions/{question}', [QuestionTemplateController::class, 'deleteQuestion'])
    ->name('questions.destroy');
Route::post('questions/{question}/answers/order', [QuestionTemplateController::class, 'updateAnswerOrder'])
    ->name('questions.answers.order');

// Answers
Route::post('questions/{question}/answers', [QuestionTemplateController::class, 'addAnswer'])
    ->name('questions.answers.store');
Route::delete('answers/{answer}', [QuestionTemplateController::class, 'deleteAnswer'])
    ->name('answers.destroy');
    Route::get('/master-settings', [MastersettingController::class, 'index'])->name('master.settings');
    Route::get(
        '/projectdetails/{projectid}/download',
        [App\Http\Controllers\Backend\Project\ProjectDetailController::class, 'downloadProjectFile']
    )->name('project.file.download');

        Route::get('/home', [EmployeeHomeController::class, 'index'])
            ->name('employee.home');
    
        Route::get('/profile', [EmployeeHomeController::class, 'profile'])
            ->name('employee.profile');
  
Route::prefix('recruitment')->group(function () {
    Route::get('/', [RecruitmentController::class, 'index'])->name('recruitment.index');
    
    // Offer Letter / Salary
    Route::get('/set-salary/{candidateId}', [RecruitmentController::class, 'setSalaryForm'])->name('recruitment.set-salary');
    Route::post('/store-salary/{candidateId}', [RecruitmentController::class, 'storeSalary'])->name('recruitment.store-salary');
    Route::get('/view-salary/{candidateId}', [RecruitmentController::class, 'viewSalary'])->name('recruitment.view-salary');
    Route::post('/send-offer-letter/{candidateId}', [RecruitmentController::class, 'sendOfferLetter'])->name('recruitment.send-offer-letter');
    
    // Candidate Selection
    Route::post('/select-candidate/{id}', [RecruitmentController::class, 'selectCandidate'])->name('recruitment.select-candidate');
    Route::post('/reject-candidate/{id}', [RecruitmentController::class, 'rejectCandidate'])->name('recruitment.reject-candidate');
});

Route::post('/employee/feed/wish', [EmployeeHomeController::class, 'sendWish'])
    ->name('feed.wish');
    Route::get('/my-reports', [MyReportController::class, 'reportsIndex'])->name('employeeReports');

    Route::get('/my-reports/career-history', [MyReportController::class, 'careerHistory'])->name('reports.careerhistory');
    Route::get('/my-reports/leave-balance', [MyReportController::class, 'leaveBalance'])
         ->name('reports.leavebalance');
    Route::get('/my-reports/leave-balance/history/{type}', 
        [MyReportController::class, 'leaveBalanceHistory']
    )->name('myreports.leave.history.type');
    Route::get('/myreports/attendance/early-late', [MyReportController::class, 'earlyLateReport'])
        ->name('myreports.attendance.earlylate');
    Route::get('/myreports/present-absent', [MyReportController::class, 'presentAbsentStatus'])
         ->name('myreports.presentAbsent');
    
    Route::get('/myreports/present-absent/download', [MyReportController::class, 'presentAbsentDownload'])
        ->name('myreports.presentAbsent.download');
        Route::get('/myreports/presence-hours', [MyReportController::class, 'presenceHours'])
         ->name('myreports.presenceHours');
    // Presence Hours Report Routes
    
    Route::get('/presence-hours/pdf', [MyReportController::class, 'presenceHoursPDF'])->name('presence.hours.pdf');
    Route::get('/presence-hours/csv', [MyReportController::class, 'presenceHoursDownload'])->name('presence.hours.csv');
    Route::get('/weekly-report', [MyReportController::class, 'weeklyReport'])->name('weekly.report');
    
    Route::get('/job-status-report', [MyReportController::class, 'jobStatusReport']);
    // Project Status Report (NEW)
    Route::get('/project-status-report', [MyReportController::class, 'projectStatusReport'])
        ->name('project.status.report');
    
        Route::get('/scheduled-vs-worked-report', [MyReportController::class, 'scheduledVsWorkedReport'])->name('scheduled.worked.report');
    Route::get('/my-goals-report', [MyReportController::class, 'myGoalReport'])->name('my.goals.report');
    
    
    
    use App\Http\Controllers\TeamReportController;
    
    // =============================
    // RESOURCE AVAILABILITY REPORT
    // =============================
    Route::get('/resource-availability', [TeamReportController::class, 'resourceAvailability'])
         ->name('resource.availability');
    Route::get('/team/resource-availability/csv', 
        [TeamReportController::class, 'resourceAvailabilityCSV']
    )->name('team.resourceAvailability.csv');
    
         // Team Leave Balance
    Route::get('/team-leave-balance', [TeamReportController::class, 'teamLeaveBalance'])
        ->name('team.leave.balance');
    
    Route::get('/team-leave-balance/history/{empId}/{leaveType}', 
        [TeamReportController::class, 'teamLeaveBalanceHistory']);
    
        /* Daily Leave Status */
    Route::get('/team/daily-leave-status', [TeamReportController::class, 'teamDailyLeaveStatus'])
        ->name('team.daily.leave.status');
    Route::get('/team-daily-leave-status/csv', 
        [App\Http\Controllers\TeamReportController::class, 'teamDailyLeaveCSV']
    )->name('team.dailyLeave.csv');
    
         Route::get('/team/present-absent', 
            [App\Http\Controllers\TeamReportController::class, 'teamPresentAbsent']
        )->name('team.presentabsent');
    
        // CSV download
        Route::get('/team/present-absent/download', 
            [App\Http\Controllers\TeamReportController::class, 'teamPresentAbsentDownload']
        )->name('team.presentabsent.download');
    
        Route::get('/team/presence-hours', [TeamReportController::class, 'teamPresenceHours'])
        ->name('team.presence.hours');
    
    Route::get('/team/early-late-report', [TeamReportController::class, 'teamEarlyLateReport'])
        ->name('team.earlyLate');
    
    Route::get('/team/early-late-report/csv', [TeamReportController::class, 'teamEarlyLateCSV'])
        ->name('team.earlyLate.csv');
    
    Route::get('/team/early-late-report/pdf', [TeamReportController::class, 'teamEarlyLatePDF'])
        ->name('team.earlyLate.pdf');
    // web.php (example routes)
    // Team weekly report pages
    Route::get('team/weekly-report', [App\Http\Controllers\TeamReportController::class, 'teamWeeklyReport'])->name('team.weekly.report');
    Route::get('team/weekly-csv', [App\Http\Controllers\TeamReportController::class, 'teamWeeklyCSV'])->name('team.weekly.csv');
    
    /* ============================================
       TEAM JOB STATUS REPORT
    ============================================ */
    
    Route::get('/team/job-status-report', 
        [App\Http\Controllers\TeamReportController::class, 'teamJobStatusReport']
    )->name('team.job.status.report');
    /* ============================
       TEAM PROJECT STATUS REPORT
    ============================ */
    
    Route::get('/team/project-status-report', 
        [App\Http\Controllers\TeamReportController::class, 'teamProjectStatusReport']
    )->name('team.project.status');
    /* ================================
       TEAM SCHEDULED vs WORKED REPORT
       ================================ */
    Route::get('/team-scheduled-worked', 
        [App\Http\Controllers\TeamReportController::class, 'teamScheduledWorkedReport']
    )->name('team.scheduled.worked');
    Route::get('/Team-reports', [TeamReportController::class, 'reportsIndex'])->name('TeamReports');
    
    Route::get('/organization-reports', [MyReportController::class, 'organizationReports'])->name('OrganizationReports');
    
    
    use App\Http\Controllers\OrganizationReportsController;
    
    Route::get('/organization-resource-availability', 
        [OrganizationReportsController::class, 'resourceAvailability']
    )->name('OrganizationReports.ResourceAvailability');
    Route::get('/reports/resource-availability/export', [OrganizationReportsController::class, 'exportCSV'])
        ->name('reports.resource-availability.export');
    
        Route::get('/organization/leave-balance', [OrganizationReportsController::class, 'organizationLeaveBalance'])
         ->name('Organization.LeaveBalance');
    
    Route::get('/organization/leave-balance/csv', [OrganizationReportsController::class, 'organizationLeaveBalanceCSV'])
         ->name('Organization.LeaveBalance.csv');
    // Main page (Table + Chart + Filters)
    Route::get('/organization/daily-leave-status', 
        [OrganizationReportsController::class, 'organizationDailyLeaveStatus']
    )->name('Organization.dailyLeaveStatus');
    
    // CSV export
    Route::get('/organization/daily-leave-status/csv', 
        [OrganizationReportsController::class, 'organizationDailyLeaveStatusCSV']
    )->name('Organization.dailyLeave.csv');
    
    
    Route::get('/organization/present-absent', [OrganizationReportsController::class, 'organizationPresentAbsent'])
        ->name('Organization.presentAbsent');
    
    Route::get('/organization/present-absent/csv', [OrganizationReportsController::class, 'organizationPresentAbsentCSV'])
        ->name('Organization.presentAbsent.csv');
    
        // Organization presence hours
    Route::get('/organization-presence-hours', [\App\Http\Controllers\OrganizationReportsController::class, 'organizationPresenceHours'])
        ->name('organization.presence');
    
    Route::get('/organization-presence-hours/csv', [\App\Http\Controllers\OrganizationReportsController::class, 'organizationPresenceHoursCSV'])
        ->name('organization.presence.csv');
    
        // Organization Early/Late
    Route::get('/organization/early-late', [OrganizationReportsController::class, 'organizationEarlyLate'])
        ->name('organization.earlylate');
    
    Route::get('/organization/early-late/csv', [OrganizationReportsController::class, 'organizationEarlyLateCSV'])
        ->name('organization.earlylate.csv');
    Route::get('/organization/weekly-report', [OrganizationReportsController::class,'organizationWeeklyReport'])->name('Organization.weekly');
    Route::get('/organization/weekly-csv', [OrganizationReportsController::class,'organizationWeeklyCSV'])->name('Organization.weekly.csv');
    
    /* -------------------------------
    |  ORGANIZATION JOB STATUS REPORT
    |------------------------------- */
    
    
    Route::get('/organization/job-status', 
        [OrganizationReportsController::class, 'organizationJobStatusReport']
    )->name('organization.jobstatus');
    
    Route::get('/organization/job-status/csv', 
        [OrganizationReportsController::class, 'organizationJobStatusCSV']
    )->name('organization.jobstatus.csv');
    
    // Organization Project Status Report
    Route::get('/organization/project-status', [OrganizationReportsController::class, 'organizationProjectStatusReport'])
        ->name('organization.projectstatus');
    
    // CSV Export
    Route::get('/organization/project-status/csv', [OrganizationReportsController::class, 'organizationProjectStatusCSV'])
        ->name('organization.projectstatus.csv');
        
    
        // Attendance Routes
    Route::post('/attendance/punch-in', [AttendanceController::class, 'punchIn'])->name('punch-in');
    Route::post('/attendance/punch-out', [AttendanceController::class, 'punchOut'])->name('punch-out');
    Route::post('/attendance/break/start', [AttendanceController::class, 'startBreak'])->name('start-break');
    Route::post('/attendance/break/end', [AttendanceController::class, 'endBreak'])->name('end-break');
    Route::get('/attendance/break-status', [AttendanceController::class, 'getBreakStatus'])->name('break-status');
    Route::get('/attendance/search', [AttendanceController::class, 'search'])->name('attendance.search');
    Route::get('/attendance/calendar-data', [AttendanceController::class, 'getCalendarData'])->name('attendance.calendar');
    Route::get('/attendance/filter', [AttendanceController::class, 'getFilteredRecords'])->name('attendance.filter');
    
    
    Route::get('/payroll/combined', [EmployeeSalaryController::class, 'combinedIndex'])->name('payroll.combined');
        Route::get('/payroll/employee-salaries/export/csv', [EmployeeSalaryController::class, 'exportSalariesCsv'])->name('payroll.employee-salary.export.csv');
    Route::get('/payroll/employee-salaries/export/pdf', [EmployeeSalaryController::class, 'exportSalariesPdf'])->name('payroll.employee-salary.export.pdf');
    
    
    // Employee routes (NO middleware)
    Route::prefix('attendance')->group(function () {
        Route::prefix('manual-punch')->group(function () {
            Route::get('/', [ManualPunchController::class, 'index'])->name('manual-punch.index');
            Route::get('/create', [ManualPunchController::class, 'create'])->name('manual-punch.create');
            Route::post('/', [ManualPunchController::class, 'store'])->name('manual-punch.store');
            Route::get('/{id}/edit', [ManualPunchController::class, 'edit'])->name('manual-punch.edit');
            Route::put('/{id}', [ManualPunchController::class, 'update'])->name('manual-punch.update');
            Route::delete('/{id}', [ManualPunchController::class, 'destroy'])->name('manual-punch.destroy');
            Route::get('/check-availability', [ManualPunchController::class, 'checkAvailability'])
                ->name('manual-punch.check-availability');
        });
    });
    Route::prefix('admin')->group(function () {
        Route::prefix('manual-punch')->group(function () {
            Route::get('/', [AdminManualPunchController::class, 'index'])->name('admin.manual-punch.index');
            Route::get('/{id}', [AdminManualPunchController::class, 'show'])->name('admin.manual-punch.show');
            Route::post('/{id}/approve', [AdminManualPunchController::class, 'approve'])->name('admin.manual-punch.approve');
            Route::post('/{id}/reject', [AdminManualPunchController::class, 'reject'])->name('admin.manual-punch.reject');
            Route::post('/bulk-process', [AdminManualPunchController::class, 'bulkProcess'])
                ->name('admin.manual-punch.bulk-process');
            Route::get('/export', [AdminManualPunchController::class, 'export'])->name('admin.manual-punch.export');
        });
    });
    
        Route::post('/admin/attendance/mark-manual', [AdminAttendanceController::class, 'markManualAttendance'])->name('admin.attendance.mark-manual');

// Accounts Services Page
Route::get('/accounts/services', function () {
    return view('hrms.accounts.services');
})->name('accounts.services');


Route::get('/debug-schedule/{employeeId}', function($employeeId) {
    $today = date('Y-m-d');
    $controller = new \App\Http\Controllers\Backend\Employee\TeamLeaveController();
    
    $result = $controller->isWorkingDay($employeeId, $today);
    
    return response()->json([
        'employee_id' => $employeeId,
        'today' => $today,
        'result' => $result
    ]);
});
// Timesheet routes
Route::get('/timesheet', [TimesheetEmployeeController::class, 'index'])->name('timesheet.index');
Route::get('/timesheet/create', [TimesheetEmployeeController::class, 'create'])->name('timesheet.create');
Route::post('/timesheet', [TimesheetEmployeeController::class, 'store'])->name('timesheet.store');
Route::get('/timesheet/{id}/edit', [TimesheetEmployeeController::class, 'edit'])->name('timesheet.edit');
Route::put('/timesheet/{id}', [TimesheetEmployeeController::class, 'update'])->name('timesheet.update');
Route::delete('/timesheet/{id}', [TimesheetEmployeeController::class, 'destroy'])->name('timesheet.destroy');

// Admin approval routes - make sure these are outside any auth middleware that might conflict
Route::post('/timesheet/{id}/approve', [TimesheetEmployeeController::class, 'approve'])->name('timesheet.approve');
Route::post('/timesheet/{id}/reject', [TimesheetEmployeeController::class, 'reject'])->name('timesheet.reject');
Route::post('/timesheet/bulk-approve', [TimesheetEmployeeController::class, 'bulkApprove'])->name('timesheet.bulk-approve');



// Timesheet Employee Reports
Route::group(['prefix' => 'timesheet-employee-reports'], function() {
    Route::get('/', [TimesheetEmployeeReportController::class, 'index'])->name('timesheet-employee-reports.index');
    Route::get('/export/csv', [TimesheetEmployeeReportController::class, 'exportCsv'])->name('timesheet-employee-reports.export.csv');
    Route::get('/export/pdf', [TimesheetEmployeeReportController::class, 'exportPdf'])->name('timesheet-employee-reports.export.pdf');
    Route::get('/{id}/details', [TimesheetEmployeeReportController::class, 'getDetails'])->name('timesheet-employee-reports.details');
});


Route::put('/testing/bugs/{id}/status', [TestingController::class, 'updateBugStatus'])->name('testing.updateBugStatus');

// Add this route for updating bug status
Route::put('/testing/bugs/{id}/status', [TestingController::class, 'updateBugStatus'])->name('testing.updateBugStatus');

// Bug edit routes
Route::get('/testing/bugs/{id}/edit', [TestingController::class, 'editBug'])->name('testing.bug.edit');
Route::put('/testing/bugs/{id}', [TestingController::class, 'updateBug'])->name('testing.bug.update');


Route::patch('/trainings/status/{id}', [TrainingManagementController::class, 'updateEmployeeStatus'])
    ->name('trainings.updateEmployeeStatus');

    Route::prefix('super-admin')->group(function () {

    Route::get('/login', [SuperAdminController::class, 'login'])->name('superadmin.login');
    Route::post('/login-submit', [SuperAdminController::class, 'loginSubmit'])->name('superadmin.login.submit');

    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');

    Route::get('/logout', [SuperAdminController::class, 'logout'])->name('superadmin.logout');



    // SUB ADMINS

    Route::get('/admins', [SuperAdminController::class, 'index'])->name('superadmin.admins.index');

    Route::get('/admins/create', [SuperAdminController::class, 'create'])->name('superadmin.admins.create');

    Route::post('/admins/store', [SuperAdminController::class, 'store'])->name('superadmin.admins.store');

    Route::get('/admins/edit/{id}', [SuperAdminController::class, 'edit'])->name('superadmin.admins.edit');

    Route::post('/admins/update/{id}', [SuperAdminController::class, 'update'])->name('superadmin.admins.update');

    Route::get('/admins/delete/{id}', [SuperAdminController::class, 'delete'])->name('superadmin.admins.delete');

});

// Memo routes
Route::resource('memo', App\Http\Controllers\Backend\master\MemoController::class);
Route::get('memo/send/{id}', [App\Http\Controllers\Backend\master\MemoController::class, 'sendMemo'])->name('memo.send');
Route::get('memo/history', [App\Http\Controllers\Backend\master\MemoController::class, 'sendHistory'])->name('memo.history');
Route::get('memo/send-details/{id}', [App\Http\Controllers\Backend\master\MemoController::class, 'viewSendDetails'])->name('memo.send-details');
Route::post('memo/resend/{id}', [App\Http\Controllers\Backend\master\MemoController::class, 'resendMemo'])->name('memo.resend');
Route::get('memo/delete-history/{id}', [App\Http\Controllers\Backend\master\MemoController::class, 'deleteSendHistory'])->name('memo.delete-history');
Route::get('memo/statistics', [App\Http\Controllers\Backend\master\MemoController::class, 'getStatistics'])->name('memo.statistics');




Route::patch('/salary/{id}/approval-status', [EmployeeSalaryController::class, 'updateApprovalStatus'])->name('salary.update-approval-status');

Route::get('/employee/hike-letter/download/{id}', [AllEmployeeController::class, 'downloadHikeLetter'])->name('employee.hike-letter.download');

Route::post('/admin/announcements', [DashboardController::class, 'storeAnnouncement'])->name('admin.announcements.store');


Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');



// Chat routes
use App\Http\Controllers\ChatController;

Route::middleware(['check.session'])->group(function () {
    Route::get('/chat/hr',                 [ChatController::class, 'hrIndex'])->name('chat.hr.index');
    Route::get('/chat/hr/conversations',   [ChatController::class, 'getConversations'])->name('chat.hr.conversations');
    Route::get('/chat/hr/{id}',            [ChatController::class, 'hrShow'])->name('chat.hr.show');
    Route::get('/chat/employee',      [ChatController::class, 'employeeChat'])->name('chat.employee');
    Route::get('/chat/employee/unread', [ChatController::class, 'employeeUnread'])->name('chat.employee.unread');
    Route::post('/chat/{id}/send',    [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/{id}/read',    [ChatController::class, 'markRead'])->name('chat.read');
    Route::get('/chat/{id}/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
});


// Announcements (dynamic dashboard bar)
use App\Http\Controllers\Backend\AnnouncementController;
Route::get('/admin/announcements',              [AnnouncementController::class, 'index'])->name('admin.announcements.index');
Route::post('/admin/announcements',             [AnnouncementController::class, 'store'])->name('admin.announcements.store');
Route::delete('/admin/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('admin.announcements.destroy');




Route::prefix('salary-master')->name('salary-master.')->group(function () {

    Route::get('/', [SalaryMasterController::class, 'index'])
        ->name('index');

    Route::put('/update', [SalaryMasterController::class, 'update'])
        ->name('update');

    Route::get('/get-config', [SalaryMasterController::class, 'getConfig'])
        ->name('get-config');

});

// Interview Feedback Routes
Route::prefix('interview-feedback')->name('interview-feedback.')->group(function () {
    Route::get('{interviewId}/create', [InterviewFeedbackController::class, 'create'])->name('create');
    Route::post('store', [InterviewFeedbackController::class, 'store'])->name('store');
    Route::get('{interviewId}/view', [InterviewFeedbackController::class, 'view'])->name('view');
    Route::get('candidate/{candidateId}/summary', [InterviewFeedbackController::class, 'candidateSummary'])->name('candidate-summary');
});

// Offer Approval Workflow Routes
Route::prefix('offer-approval')->name('offer-approval.')->group(function () {
    Route::post('{candidateId}/create-draft', [OfferApprovalController::class, 'createDraft'])->name('create-draft');
    Route::post('{candidateId}/submit-manager', [OfferApprovalController::class, 'submitForManagerApproval'])->name('submit-manager');
    Route::post('{candidateId}/manager-approve', [OfferApprovalController::class, 'managerApprove'])->name('manager-approve');
    Route::post('{candidateId}/manager-reject', [OfferApprovalController::class, 'managerReject'])->name('manager-reject');
    Route::post('{candidateId}/hr-approve', [OfferApprovalController::class, 'hrApprove'])->name('hr-approve');
    Route::post('{candidateId}/hr-reject', [OfferApprovalController::class, 'hrReject'])->name('hr-reject');
    Route::post('{candidateId}/send-offer', [OfferApprovalController::class, 'sendOffer'])->name('send-offer');
    Route::post('{candidateId}/update-response', [OfferApprovalController::class, 'updateOfferResponse'])->name('update-response');
});

// Candidate Status Management Routes
Route::prefix('candidate')->name('candidate.')->group(function () {
    Route::post('{id}/update-status', [CandidateStatusController::class, 'updateStatus'])->name('update-status');
    Route::post('{id}/update-round-status', [CandidateStatusController::class, 'updateRoundStatus'])->name('update-round-status');
});

// Recruitment Dashboard Statistics
Route::get('recruitment/dashboard-stats', [RecruitmentController::class, 'getDashboardStats'])->name('recruitment.dashboard-stats');
// Add these routes if not already present
Route::post('/get-job-titles-by-department', [App\Http\Controllers\Backend\Jobs\ManageJobsController::class, 'getJobTitlesByDepartment'])
    ->name('managejobs.getJobTitlesByDepartment');

Route::post('/get-skills-by-job-title', [App\Http\Controllers\Backend\Jobs\ManageJobsController::class, 'getSkillsByJobTitle'])
    ->name('managejobs.getSkillsByJobTitle');
    Route::resource('job-vacancy-requests', JobVacancyRequestController::class);
Route::post('job-vacancy-requests/{id}/approve', [JobVacancyRequestController::class, 'approve'])->name('job-vacancy-requests.approve');
Route::post('job-vacancy-requests/{id}/hr-approve', [JobVacancyRequestController::class, 'hrApprove'])->name('job-vacancy-requests.hr-approve');
Route::post('job-vacancy-requests/{id}/reject', [JobVacancyRequestController::class, 'reject'])->name('job-vacancy-requests.reject');
Route::post('job-vacancy-requests/get-job-titles', [JobVacancyRequestController::class, 'getJobTitlesByDepartment'])->name('job-vacancy-requests.getJobTitlesByDepartment');
Route::post('job-vacancy-requests/get-skills', [JobVacancyRequestController::class, 'getSkillsByJobTitle'])->name('job-vacancy-requests.getSkillsByJobTitle');
Route::post('add-resume/{id}/update-status', [AddresumeController::class, 'updateStatus'])->name('add-resume.update-status');
Route::post('add-resume/{id}/update-round-status', [AddresumeController::class, 'updateRoundStatus'])->name('add-resume.update-round-status');
Route::get('/add-resume/view-resume/{id}', [AddresumeController::class, 'viewResume'])->name('add-resume.view-resume');
Route::get('shortlist/{id}/schedule-interview', [ShortlistController::class, 'scheduleInterview'])->name('shortlist.schedule-interview');
Route::post('shortlist/get-interviewers-by-round', [ShortlistController::class, 'getInterviewersByRound'])->name('shortlist.get-interviewers');
Route::post('shortlist/interview/{id}/update', [ShortlistController::class, 'updateInterviewStatus'])->name('shortlist.interview.update');
// Recruitment Dashboard Statistics
Route::get('recruitment/dashboard-stats', [RecruitmentController::class, 'getDashboardStats'])->name('recruitment.dashboard-stats');
// Add these routes if not already present
Route::post('/get-job-titles-by-department', [App\Http\Controllers\Backend\Jobs\ManageJobsController::class, 'getJobTitlesByDepartment'])
    ->name('managejobs.getJobTitlesByDepartment');

Route::post('/get-skills-by-job-title', [App\Http\Controllers\Backend\Jobs\ManageJobsController::class, 'getSkillsByJobTitle'])
    ->name('managejobs.getSkillsByJobTitle');


// Process Salary Routes
Route::get('/payroll/get-employees-by-department', [EmployeeSalaryController::class, 'getEmployeesByDepartment'])->name('payroll.get-employees-by-department');
Route::post('/payroll/reprocess-held-salary', [EmployeeSalaryController::class, 'reprocessHeldSalary'])->name('payroll.reprocess-held-salary');


Route::post('/payroll/process-salary', [EmployeeSalaryController::class, 'processSalary'])->name('payroll.process-salary');


Route::prefix('salary-release')->name('salary-release.')->group(function () {
    Route::get('/', [SalaryReleaseController::class, 'index'])->name('index');
    Route::get('/department/{departmentId}', [SalaryReleaseController::class, 'show'])->name('show');
    Route::post('/release-department', [SalaryReleaseController::class, 'releaseDepartment'])->name('release-department');
    Route::post('/release-employee', [SalaryReleaseController::class, 'releaseEmployee'])->name('release-employee');
    Route::post('/release-selected', [SalaryReleaseController::class, 'releaseSelected'])
    ->name('release-selected');
});