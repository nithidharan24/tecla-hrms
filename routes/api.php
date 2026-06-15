<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiAttendanceController;
use App\Http\Controllers\Api\ApiLeaveController;
use App\Http\Controllers\Api\ApiHolidayController;
use App\Http\Controllers\Api\ApiProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
|*/

Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/forgot-password', [ApiAuthController::class, 'forgotPassword']);
Route::post('/change-password', [ApiAuthController::class, 'changePassword']);

Route::prefix('attendance')->group(function () {
    Route::post('/punch-in', [ApiAttendanceController::class, 'punchIn']);
    Route::post('/punch-out', [ApiAttendanceController::class, 'punchOut']);
    Route::post('/start-break', [ApiAttendanceController::class, 'startBreak']);
    Route::post('/end-break', [ApiAttendanceController::class, 'endBreak']);
    Route::get('/break-status', [ApiAttendanceController::class, 'getBreakStatus']);
    Route::get('/history', [ApiAttendanceController::class, 'history']);
});

Route::get('/leaves', [ApiLeaveController::class, 'index']);
Route::post('/leaves/apply', [ApiLeaveController::class, 'applyLeave']);
Route::post('/leaves/apply-permission', [ApiLeaveController::class, 'applyPermission']);

Route::get('/holidays', [ApiHolidayController::class, 'index']);

Route::get('/profile', [ApiProfileController::class, 'show']);
Route::get('/profile/dashboard', [ApiProfileController::class, 'dashboardDetails']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
