<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'sl','middleware' => 'cors'], function () {

    //Route::get('/verify-otp', 'Api\SecondLifeApiController@verifyOtp')->name('sl.verify_otp');
    //Route::get('/verify-otp-and-submit-user-info', 'Api\SecondLifeApiController@verifyOtpAndSubmitUserInfo')->name('sl.verify_otp_and_submit_user_info');
    //Route::get('/create-user', 'Api\SecondLifeApiController@createUser')->name('sl.create_user');

    Route::post('/verify-otp', 'Api\SecondLifeApiController@verifyOtp')->name('sl.verify_otp');
    Route::post('/verify-otp-and-submit-user-info', 'Api\SecondLifeApiController@verifyOtpAndSubmitUserInfo')->name('sl.verify_otp_and_submit_user_info');
    Route::post('/create-user', 'Api\SecondLifeApiController@createUser')->name('sl.create_user');
    Route::post('/reset-password', 'Api\SecondLifeApiController@resetPassword')->name('sl.reset_password');

    Route::post('/add-balance-to-user', 'Api\SecondLifeApiPaymentController@addBalanceToUser')->name('sl.add_balance_to_user');
    Route::post('/get-user-balance', 'Api\SecondLifeApiPaymentController@getUserBalance')->name('sl.get_user_balance');
    Route::post('/purchase-plan', 'Api\SecondLifeApiPaymentController@purchasePlan')->name('sl.purchase_plan');
    Route::post('/purchase-plan-feature', 'Api\SecondLifeApiPaymentController@purchasePlanFeature')->name('sl.purchase_plan_feature');    
    Route::post('/purchase-token', 'Api\SecondLifeApiPaymentController@purchaseToken')->name('sl.purchase_token');
    Route::post('/add-donation', 'Api\SecondLifeApiPaymentController@addDonation')->name('sl.add_donation');
    Route::post('/add-advertisement', 'Api\SecondLifeApiPaymentController@addAdvertisement')->name('sl.add_advertisement');
    Route::post('/add-parcel-info', 'Api\SecondLifeApiParcelController@addParcelInfo')->name('sl.add_parcel_info');
    //Route::post('/update-user-relation', 'Api\SecondLifeApiController@updateSecondLifeApiUserRelation')->name('sl.update_user_relation');

    Route::post('/get-notifications', 'Api\SecondLifeApiNotificationController@getNotifications')->name('sl.get_notifications');
    Route::post('/add-notifications', 'Api\SecondLifeApiPaymentController@addNotifications')->name('sl.add_notifications');
    Route::post('/add-sl-notifications', 'Api\SecondLifeApiNotificationController@addSLnotifications')->name('sl.add_sl_notifications');
});
