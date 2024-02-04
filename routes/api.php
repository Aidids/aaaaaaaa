<?php
namespace App\Api\Controller;
use App\Models\RedeemOffshoreLeave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginApi::class, 'login'])->name('login');

Route::group(['middleware' => ['auth:sanctum']], function () {
    // Holiday Api
    Route::get('/holidays', [HolidayApi::class, 'index']);
    Route::get('/department-leave', [DepartmentApi::class, 'calendar']);
    Route::get('/department-leave/{department}', [DepartmentApi::class, 'dashboard']);

    // Job Profile API
    Route::prefix('profile-settings/{id}')->group(function (){
        Route::get('/', [ProfileApi::class, 'index']);
        Route::post('/', [ProfileApi::class, 'update']);

        Route::get('/personal-information', [UserInformationApi::class, 'getPersonalInformation']);
        Route::post('/personal-information', [UserInformationApi::class, 'storePersonalInformation']);
        Route::post('/family-detail', [UserInformationApi::class, 'storeFamilyDetail']);

        Route::get('/permanent-address', [ProfileApi::class, 'getPermAddress']);
        Route::post('/permanent-address', [ProfileApi::class, 'updatePermAddress']);
        Route::delete('/permanent-address', [ProfileApi::class, 'deletePermAddress']);

        Route::get('/current-address', [ProfileApi::class, 'getCurrAddress']);
        Route::post('/current-address', [ProfileApi::class, 'updateCurrAddress']);
        Route::delete('/current-address', [ProfileApi::class, 'deleteCurrAddress']);

        Route::get('/emergency', [ProfileApi::class, 'getEmergency']);
        Route::post('/emergency', [ProfileApi::class, 'updateEmergency']);
        Route::delete('/emergency/{contactId}', [ProfileApi::class, 'deleteEmergency']);

        Route::get('/document/{query?}', [ProfileApi::class, 'getDocument']);
        Route::post('/document', [ProfileApi::class, 'uploadDocument']);
        Route::post('/edit-document', [ProfileApi::class, 'editDocument']);
        Route::delete('/document/{document}', [ProfileApi::class, 'deleteDocument']);
    });

    // Leave Approver Api
    Route::prefix('approver')->group(function (){
        Route::get('/', [ApproverApi::class, 'index']);
        Route::post('/assign', [ApproverApi::class, 'store']);
        Route::get('/all', [ApproverApi::class, 'getEveryone']);
        Route::get('/{userId}', [ApproverApi::class, 'getApproverLevel']);
    });

    // Leave Type Api
    Route::group(['prefix' => 'leave-type'], function () {
        Route::get('/', [LeaveTypeApi::class, 'index']);
        Route::post('/', [LeaveTypeApi::class, 'store']);
        Route::post('/{id}', [LeaveTypeApi::class, 'update']);
        Route::delete('/{id}', [LeaveTypeApi::class, 'delete']);
    });

    // Leave balance API
    Route::group(['prefix' => 'leave-balance'], function () {
        Route::get('/{userId}', [LeaveBalanceApi::class, 'index']);
    });

    // Administration API
    Route::group(['prefix' => 'administration', 'middleware' => 'can:viewAny, App\User'], function () {
        Route::get('/update-annual-leave', [LeaveAddOnApi::class, 'index']);
        Route::post('/update-annual-leave', [LeaveAddOnApi::class, 'addLeave']);
        Route::get('/deduct-leave', [LeaveDeductionHistoryApi::class, 'index']);
        Route::post('/deduct-leave/{userId}', [LeaveDeductionHistoryApi::class, 'deductLeave']);
        Route::post('/add-leave-note/{leaveId}', [LeaveRequestApi::class, 'addHrNote']);

        Route::get('/excel-leave-summary', [ExcelApi::class, 'downloadLeaveSummary']);
        Route::get('/excel-user-profile/{userId}', [ExcelApi::class, 'downloadUserProfile']);
        Route::get('/excel-leave-balance', [ExcelApi::class, 'downloadLeaveBalance']);

        // Fixed Approver API
        Route::post('/fixed-approvers', [FixedApproverApi::class, 'storeApprovers']);
    });
    Route::group(['prefix' => 'administration'], function () {
        Route::get('/fixed-approvers', [FixedApproverApi::class, 'getApprovers']);
        Route::get('/departments', [DepartmentApi::class, 'index']);
    });

    // Leave Request API
    Route::group(['prefix' => 'leave-request'], function () {
        Route::get('/{userId}', [LeaveRequestApi::class, 'getAllLeave']);
        Route::post('/{userId}', [LeaveRequestApi::class, 'applyLeave']);
        Route::get('/{userId}/all', [LeaveRequestApi::class, 'index']);
        Route::post('/{userId}/cancel', [LeaveRequestApi::class, 'cancelLeave']);
        Route::get('/{userId}/approve', [LeaveRequestApi::class, 'getPendingLeaveRequest']);
        Route::get('/{userId}/summary', [LeaveRequestApi::class, 'getLeaveSummary']);
        Route::post('/{userId}/approve', [LeaveRequestApi::class, 'approvePendingLeaveRequest']);
    });

    // Travel Authorization API
    Route::group(['prefix' => '/travel-authorization'], function () {
        Route::get('/', [TravelAuthorizationApi::class, 'index']);
        Route::post('/apply', [TravelAuthorizationApi::class, 'applyTravel']);
        Route::get('/approver', [TravelAuthorizationApi::class, 'approvalIndex']);
        Route::get('/summary', [TravelAuthorizationApi::class, 'getTravelSummary'])->middleware('project.admin');
        Route::get('/{travelId}', [TravelAuthorizationApi::class, 'show']);
        Route::post('/{travelId}/edit', [TravelAuthorizationApi::class, 'editTravel']);
        Route::post('/{travelId}/cancel', [TravelAuthorizationApi::class, 'cancelTravel']);
        Route::post('/{travelId}/review', [TravelAuthorizationApi::class, 'reviewTravel']);
        Route::post('{travelId}/hrReview', [TravelAuthorizationApi::class, 'hrReviewTravel'])->middleware('project.admin');

        //For Hr upload flight ticket & Accomodation Ticket
        Route::post('/{travelId}/upload', [TravelAuthorizationApi::class, 'hrUpload'])->middleware('project.admin');;
        Route::delete('/{travelId}/deleteAttachment', [TravelAuthorizationApi::class, 'deleteHrUpload'])->middleware('project.admin');;
    });

    // Redeem "Replacement" Leave API
    Route::group(['prefix' => 'redeem-replacement-leave'], function (){
        Route::get('/', [RedeemReplacementLeaveApi::class, 'index']);
        Route::post('/apply', [RedeemReplacementLeaveApi::class, 'applyReplacement']);
        Route::post('/edit', [RedeemReplacementLeaveApi::class, 'editReplacement']);
        Route::get('/approve', [RedeemReplacementLeaveApi::class, 'approveIndex']);
        Route::post('/approve', [RedeemReplacementLeaveApi::class, 'approveReplacement']);
        Route::get('/summary', [RedeemReplacementLeaveApi::class, 'getReplacementSummary']);
        Route::post('/summary', [RedeemReplacementLeaveApi::class, 'finalizeReplacement']);
        Route::get('/completed', [RedeemReplacementLeaveApi::class, 'getAllCompleted']);

        // Upload Replacement Attachment API
        Route::post('/attachment/upload', [RedeemReplacementLeaveApi::class, 'uploadAttachment']);
        // Delete Replacement Attachment API
        Route::delete('/attachment/delete', [RedeemReplacementLeaveApi::class, 'deleteAttachment']);
    });

    // Redeem "Offshore" Leave API
    Route::group(['prefix' => 'redeem-offshore-leave'], function () {
        Route::get('/', [RedeemOffshoreLeaveApi::class, 'index']);
        Route::post('/apply', [RedeemOffshoreLeaveApi::class, 'apply']);
        Route::post('/edit', [RedeemOffshoreLeaveApi::class, 'edit']);
        Route::post('/cancel', [RedeemOffshoreLeaveApi::class, 'cancel']);
        Route::get('/approve', [RedeemOffshoreLeaveApi::class, 'approveIndex']);
        Route::post('/approve', [RedeemOffshoreLeaveApi::class, 'approve']);
        Route::get('/summary', [RedeemOffshoreLeaveApi::class, 'summary']);
        Route::post('/summary', [RedeemOffshoreLeaveApi::class, 'finalizeOffshore']);

        // Upload Offshore Attachment API
        Route::post('/attachment/upload', [RedeemOffshoreLeaveApi::class, 'uploadAttachment']);
        // Delete Offshore Attachment API
        Route::delete('/attachment/delete', [RedeemOffshoreLeaveApi::class, 'deleteAttachment']);
    });

    // Compassionate Leave API
    Route::group(['prefix' => 'compassionate-leave'], function (){
        Route::post('/{userId}/', [CompassionateLeaveApi::class, 'applyCompassionate']);
    });

    // Notification API
    Route::group(['prefix' => 'notification'], function (){
        Route::get('/{userID}', [NotificationApi::class, 'index']);
    });

    // All employee API
    Route::get('/all-employees/{query?}', [ProfileApi::class, 'getAllEmployees']);

    // Travel Claim API
    Route::group(['prefix' => '/travel-claim'], function () {
        Route::get('/', [TravelClaimApi::class, 'index']);
        Route::post('/', [TravelClaimApi::class, 'store']);
        Route::get('/history', [TravelClaimApi::class, 'history']);
        Route::post('/cancel', [TravelClaimApi::class, 'cancel']);
        Route::get('/approve', [TravelClaimApi::class, 'approvalIndex']);
        Route::post('/approve', [TravelClaimApi::class, 'approve']);
        Route::get('/summary', [TravelClaimApi::class, 'summary'])->middleware('can:isAdmin, App\User');
        Route::delete('/reset', [TravelClaimApi::class, 'reset']);
        Route::post('/notes', [TravelClaimApi::class, 'notes']);

        Route::group(['prefix' => '/{travel_id}'], function () {
            Route::get('/download', [TravelClaimApi::class, 'download'])->name('download');
            Route::get('/edit', [TravelClaimApi::class, 'edit']);
            Route::get('/show', [TravelClaimApi::class, 'show']);

            // Allowance API
            Route::get('/allowance', [AllowanceApi::class, 'index']);
            Route::post('/allowance', [AllowanceApi::class, 'store']);
            Route::post('/allowance-add', [AllowanceApi::class, 'add']);
            Route::delete('/allowance-delete', [AllowanceApi::class, 'delete']);

            // Transport API
            Route::get('/transport', [TransportApi::class, 'index']);
            Route::post('/transport', [TransportApi::class, 'store']);
            Route::post('/transport-add', [TransportApi::class, 'add']);
            Route::delete('/transport-delete', [TransportApi::class, 'delete']);

            // Expense API
            Route::get('/expense', [ExpenseApi::class, 'index']);
            Route::post('/expense', [ExpenseApi::class, 'store']);
            Route::post('/expense-add', [ExpenseApi::class, 'add']);
            Route::delete('/expense-delete', [ExpenseApi::class, 'delete']);
        });
    });

    // Allowance Attachment API
    Route::group(['prefix' => '/allowance-attachment'], function () {
        Route::post('/{allowanceId}', [AllowanceApi::class, 'addAttachment']);
        Route::delete('/{allowanceId}', [AllowanceApi::class, 'deleteAttachment']);
    });
    // Transport Attachment API
    Route::group(['prefix' => '/transport-attachment'], function () {
        Route::post('/{transportId}', [TransportApi::class, 'addAttachment']);
        Route::delete('/{transportId}', [TransportApi::class, 'deleteAttachment']);
    });
    // Expense Attachment API
    Route::group(['prefix' => '/expense-attachment'], function () {
        Route::post('/{expenseId}', [ExpenseApi::class, 'addAttachment']);
        Route::delete('/{expenseId}', [ExpenseApi::class, 'deleteAttachment']);
    });
});
