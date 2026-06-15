<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Jobs\InterviewFeedbackController;
use App\Http\Controllers\Backend\Jobs\OfferApprovalController;
use App\Http\Controllers\Backend\Jobs\RecruitmentController;

// Interview Feedback Routes
Route::prefix('interview-feedback')->name('interview-feedback.')->group(function () {
    Route::get('{interviewId}/create', [InterviewFeedbackController::class, 'create'])->name('create');
    Route::post('store', [InterviewFeedbackController::class, 'store'])->name('store');
    Route::get('{interviewId}/view', [InterviewFeedbackController::class, 'view'])->name('view');
    Route::get('candidate/{candidateId}/summary', [InterviewFeedbackController::class, 'candidateSummary'])->name('candidate-summary');
});

// Offer Approval Routes
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

// Recruitment Dashboard Stats
Route::get('recruitment/dashboard-stats', [RecruitmentController::class, 'getDashboardStats'])->name('recruitment.dashboard-stats');
