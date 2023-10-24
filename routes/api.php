<?php

use App\Http\Controllers\Api\CommandController;
use App\Http\Controllers\Api\PassportAuthController;
use App\Http\Controllers\Api\ReferralController;
use App\Http\Controllers\Api\ReportingController;
use App\Http\Controllers\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::group(['middleware' => ['cors', 'json.response']], function () {

    //public route
    Route::post('register', [PassportAuthController::class, 'register']);
    Route::post('login', [PassportAuthController::class, 'login']);
    Route::get('getUserByNIP/{nip}', [PassportAuthController::class, 'getUserByNIP']);
    Route::get('list-division', [ReferralController::class, 'getListDivision']);
    Route::get('list-region', [ReferralController::class, 'getListRegion']);
    // Route::get('list-branch/{regionId}', [ReferralController::class, 'getListBranch']);
    Route::get('list-city/{regionId}', [ReferralController::class, 'getListCity']);

    //Forgot Password
    Route::post('forgot-password', [PassportAuthController::class, 'forgotPassword']);
    Route::get('/reset-password/{token}', [PassportAuthController::class, 'redirectForgotPassword'])
    ->middleware('guest')->name('password.reset');


    //email verify route
    Route::get('email/verify', function () {
        return view('auth.verify-email');
    })->middleware('auth')->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])
    ->middleware(['throttle:6,1'])
    ->name('verification.send');

    //Command
    Route::get('do-migrate', [CommandController::class, 'migrate']);
    Route::get('do-seed', [CommandController::class, 'seed']);
    Route::get('do-passport-install', [CommandController::class, 'passportInstall']);
    Route::get('do-key-generate', [CommandController::class, 'keyGenerate']);

    //authenticated route
    Route::group(['middleware' => ['auth:api', 'verified']], function () {
    // Route::middleware('auth:api', 'verified')->group(function () {
        Route::get('get-user', [PassportAuthController::class, 'userInfo']);
        Route::get('logout', [PassportAuthController::class, 'logout']);
        Route::post('update-password', [PassportAuthController::class, 'updatePassword']);
        Route::post('create-referral', [ReferralController::class, 'createReferral']);
        Route::post('update-referral-sts', [ReferralController::class, 'updateReferralStatus']);
        Route::get('list-product-type', [ReferralController::class, 'getListProductType']);
        Route::get('list-product-categories/{productTypeId}', [ReferralController::class, 'getListProductCategory']);
        Route::get('list-status-referral/{currentStatusId}', [ReferralController::class, 'getListStatusReferral']);
        Route::get('list-refer-name/{divisionId}/{regionId}/{branchLocationId}', [ReferralController::class, 'getListToReferName']);
        Route::get('list-my-referral', [ReferralController::class, 'getMyListReferral']);
        Route::get('get-tracking-detail/{referralId}', [ReferralController::class, 'getTrackingDetail']);
        Route::get('get-my-request', [ReferralController::class, 'getMyRequestReferral']);
        Route::get('get-referral-detail/{referralId}', [ReferralController::class, 'getReferalDetail']);
        Route::get('get-referral-doc/{referralId}', [ReferralController::class, 'getReferalDocuments']);
        Route::get('download-doc/{docName}', [ReferralController::class, 'downloadDocuments']);
        Route::get('get-new-req-count', [ReferralController::class, 'getNewRequestCount']);
        Route::get('get-form/{formtipe}', [ReferralController::class, 'getForm']);
        Route::get('get-ranking', [ReportingController::class, 'rankPerDivision']);
        Route::get('get-open-referral', [ReferralController::class, 'getListOpenReferral']);
        Route::post('take-referral', [ReferralController::class, 'takeReferral']);
        Route::get('get-leader-board', [ReportingController::class, 'referralLeaderBoard']);
        Route::post('list-user-to-take', [ReferralController::class, 'listUserToTakeReferral']);
    });
});
