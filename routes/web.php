<?php

use App\Http\Controllers\EformController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\RedeemLeaveController;
use App\Http\Controllers\UserGuideController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\AdministrationController;

// Login
Route::get('/hq/login', [LoginController::class, 'show'])->name('show.login');
Route::post('/hq/login', [LoginController::class, 'login'])->name('login')->middleware('throttle:5,1');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function() {

    // Impersonation
    Route::get('/impersonate/{user_id}', [ImpersonateController::class, 'index'])
        ->name('impersonate');
    Route::get('/impersonate-leave', [ImpersonateController::class, 'impersonate_leave'])
        ->name('impersonate-leave');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/calendar', [DashboardController::class, 'calendar'])->name('dashboard.calendar');

    // Profile
    Route::get('/profile-settings/{user}', [ProfileController::class, 'index'])->name('profile.index');

    // Employee
    Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.index');

    // Leave Request
    Route::group(['prefix' => 'leave-request'], function () {
        Route::get('/{user}', [LeaveController::class, 'history'])->name('leave.history');
        Route::get('/{user}/request', [LeaveController::class, 'request'])->name('leave.request');
        Route::get('/{user}/approve', [LeaveController::class, 'approveLeave'])->name('leave.approve');
        Route::get('/{user}/summary', [LeaveController::class, 'summary'])->name('leave.summary');
    });

    // Leave Redemption
    Route::group(['prefix' => 'redeem-leave'], function () {
        Route::get('/apply', [RedeemLeaveController::class, 'redeemReplacementIndex'])->name('leave.redeem-index');

        Route::get('/history/offshore-leave', [RedeemLeaveController::class, 'redeemReplacementHistory'])->name('leave.redeem-history');
        Route::get('/history/replacement-leave', [RedeemLeaveController::class, 'redeemReplacementHistory'])->name('leave.redeem-history');

        Route::get('/approve/offshore-leave', [RedeemLeaveController::class, 'approveRedeemReplacement'])->name('leave.redeem-approve');
        Route::get('/approve/replacement-leave', [RedeemLeaveController::class, 'approveRedeemReplacement'])->name('leave.redeem-approve');

        Route::get('/summary/offshore-leave', [RedeemLeaveController::class, 'summaryRedeemReplacement'])->name('leave.redeem-summary');
        Route::get('/summary/replacement-leave', [RedeemLeaveController::class, 'summaryRedeemReplacement'])->name('leave.redeem-summary');
    });

    // Leave Type
    Route::get('/leave-type', [AdministrationController::class, 'leaveType'])->name('administration.leave-type');

    // Administration
    Route::group(['prefix' => 'administration'], function () {
        Route::get('/approvers', [AdministrationController::class, 'approvers'])->name('administration.approvers');
        Route::get('/update-annual-leave', [AdministrationController::class, 'updateAnnualLeave'])->name('administration.updateAnnualLeave');
        Route::get('/deduct-leave', [AdministrationController::class, 'deductLeave'])->name('administration.deductLeave');
    });

    //User Guide
    Route::get('/user-guide', [UserGuideController::class, 'index'])->name('user-guide');

    //EForm
    Route::group(['prefix' => 'e-form'], function () {
        Route::get('/apply', [EformController::class, 'apply'])->name('eform.apply');
        Route::get('/{user}/travel/travel-authorization', [EformController::class, 'index'])->name('eform.travel.index');
        Route::get('/{user}/travel/travel-claim', [EformController::class, 'index'])->name('eform.travel.index');
        Route::get('/approvers/travel-authorization', [EformController::class, 'approvers'])->name('eform.approvers');
        Route::get('/approvers/travel-claim', [EformController::class, 'approvers'])->name('eform.approvers');

        Route::get('/summary/travel-claim', [EformController::class, 'summary'])->name('eform.summary')->middleware('redirect.ta');
        Route::get('/summary/travel-authorization', [EformController::class, 'summary'])->name('eform.travel.authorization')->middleware('project.admin');
    });
    //View E-Form
    Route::get('/travel-authorization/{travel}', [EformController::class, 'travelAuthorizationEdit']);
    Route::get('/travel-claim/{travel}', [EformController::class, 'travelExpenseEdit']);

    Route::get('/travel-authorization/{travel}/show', [EformController::class, 'travelAuthorizationShow']);
    Route::get('/travel-claim/{travel}/show', [EformController::class, 'travelExpenseShow']);
});


