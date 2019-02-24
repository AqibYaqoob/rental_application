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

Route::post('register', 'UserController@register');
Route::post('register/validation', 'UserController@register_validation');
Route::post('login', 'UserController@authenticate');
Route::post('forget/password', 'UserController@forget_password');
Route::get('open', 'DataController@open');
Route::get('get/packages/detail', 'PackageController@list');
Route::get('get/cities/info', 'UserController@getCities');
Route::post('verify/user/code', 'UserController@verifyUser');
Route::post('resend/otp/code', 'UserController@resendOtpCode');
Route::get('get/skill/set/detail', 'SkillSetController@list');
Route::get('get/property/type/details', 'PropertyController@get_property_type_list');
// General Routes
Route::get('get/payment/options', 'GeneralController@payment_list');
Route::post('update/device/token', 'GeneralController@update_device_token');
// End of General Routes
Route::group(['middleware' => ['jwt.verify']], function () {
    // Authenticate User
    Route::get('user', 'UserController@getAuthenticatedUser');
    Route::get('closed', 'DataController@closed');
    Route::get('get/all/non_rented/properties', 'PropertyController@get_all_non_rented_properties');
    Route::post('update/profile', 'UserController@profileEdit');
    Route::post('change/password', 'UserController@change_password');
    // Route::get('get/cities/info', 'UserController@getCities');
    /*=================================================
    =            Owner Manager Api Details            =
    =================================================*/
    Route::group(['prefix' => 'owner'], function () {
        /*----------  LandLoard Owner Properties Detail  ----------*/
        // 1) Add New Property Detail
        Route::post('add/property', 'PropertyController@add_property');
        // 2) Get Landloard Properties Detail
        Route::post('get/properties/all', 'PropertyController@get_landloard_properties');
        // 3) Get Specific Property Details
        Route::post('get/specific/property/details', 'PropertyController@get_specific_property');
        // 4)Remove Property Scheduling
        Route::post('remove/booking/for/showing', 'PropertyController@removePropertyShowing');
        // 5) Booking Details
        Route::post('property/booking/details', 'PropertyController@booking_details');
        // 6) Add Showing Booking Details
        Route::post('property/booking/add', 'PropertyController@add_booking_for_specific_property');
        // 7) Add Showing Booking Details
        Route::post('property/booking/reschedule', 'PropertyController@reScheduleProperty');
        // 8) Show Non Rented Properties for the Land Loard
        Route::post('non/rented/properties', 'PropertyController@show_rented_property_for_owner');
        // 9) Pending Bookings Details
        Route::post('pending/booking/details', 'PropertyController@pending_booking_details');
        // 10) Confirmed booking Details
        Route::post('confirmed/booking/details', 'PropertyController@confirmed_booking_details');
        // 11) Transaction Details for the Account
        Route::post('transaction/details', 'UserController@transaction_details');
        // 12) To check if no property is added than app should prompt to add Property.
        Route::post('check/any/property/added', 'PropertyController@check_available_property');
    });
    /*=====  End of Owner Manager Api Details  ======*/

    /*===============================================
    =            Contractors Api Details            =
    ===============================================*/

    Route::group(['prefix' => 'contractor'], function () {
        Route::post('get/all', 'ContractorController@list');
    });

    /*=====  End of Contractors Api Details  ======*/

    /*=============================================
    =            Applicant Api Details            =
    =============================================*/
    Route::group(['prefix' => 'applicant'], function () {
        Route::post('add/scheduling/properties', 'PropertyController@addPropertyScheduling');
        Route::post('get/specific/property/details', 'PropertyController@get_specific_property');
        Route::post('get/property/searchings', 'PropertyController@property_searching');
        Route::post('add/property/favourite', 'PropertyController@add_property_to_favourite');
        Route::post('get/property/favourite', 'PropertyController@show_favourite_property');
        Route::post('remove/property/favourite', 'PropertyController@remove_favourite_property');
        // 1) Apply for the Property
        Route::post('apply/for/properties', 'PropertyController@apply_for_property');
    });
    /*=====  End of Applicant Api Details  ======*/

    /*=====================================================================
    =            Chat Module for (Owner, Applicant, Contractor)            =
    ======================================================================*/
    Route::post('get/applicants/list', 'ChatController@get_applicants_list');
    Route::post('get/owners/list', 'ChatController@get_owners_list');
    Route::group(['prefix' => 'chat'], function () {
        Route::post('message', 'ChatController@chat');
        Route::post('get/messages', 'ChatController@get_chat_messages');

    });
    /*=====  End of Chat Module for (Owner, Applicant, Contractor)  ======*/
});

/*====================================================
=            Update User (online/offline)            =
=====================================================*/
Route::post('update/user/status', 'UserController@update_user_status');
/*=====  End of Update User (online/offline)  ======*/

/*=====================================================================
=            Transaction of the Brain Tree Payment Gateway            =
=====================================================================*/
Route::group(['prefix' => 'transaction'], function () {
    Route::get('client/generate/token', 'TransactionController@generate_client_token');
    Route::post('payment/process', 'TransactionController@payment_process');
});
/*=====  End of Transaction of the Brain Tree Payment Gateway  ======*/

// Testing Routes
Route::get('get/test/push/notification', 'GeneralController@push_notification_test');
