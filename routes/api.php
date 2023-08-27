<?php

use App\Http\Controllers\API\PassportAuthController;
use App\Http\Controllers\API\ReferralController;
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

    //authenticated route
    Route::middleware('auth:api', 'verified')->group(function () {
        Route::get('get-user', [PassportAuthController::class, 'userInfo']);
        Route::get('logout', [PassportAuthController::class, 'logout']);
        Route::post('update-password', [PassportAuthController::class, 'updatePassword']);
        Route::post('create-referral', [ReferralController::class, 'createReferral']);
        Route::post('update-referral-sts', [ReferralController::class, 'updateReferralStatus']);
        Route::get('list-product-type', [ReferralController::class, 'getListProductType']);
        Route::get('list-product-categories/{productTypeId}', [ReferralController::class, 'getListProductCategory']);
        Route::get('list-status-referral/{currentStatusId}', [ReferralController::class, 'getListStatusReferral']);
        Route::get('list-divisoin', [ReferralController::class, 'getListDivision']);
        Route::get('list-region', [ReferralController::class, 'getListRegion']);
        Route::get('list-branch/{regionId}', [ReferralController::class, 'getListBranch']);
        Route::get('list-refer-name/{divisionId}/{regionId}/{branchLocationId}', [ReferralController::class, 'getListToReferName']);
        Route::get('list-my-referal', [ReferralController::class, 'getMyListReferal']);
        Route::get('get-tracking-detail/{referralId}', [ReferralController::class, 'getTrackingDetail']);

    });
});
