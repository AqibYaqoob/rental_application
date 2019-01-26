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
Route::post('login', 'UserController@authenticate');
Route::post('forget/password', 'UserController@forget_password');
Route::get('open', 'DataController@open');
Route::get('get/packages/detail', 'PackageController@list');
Route::get('get/cities/info', 'UserController@getCities');
Route::post('verify/user/code', 'UserController@verifyUser');
Route::post('resend/otp/code', 'UserController@resendOtpCode');
Route::get('get/skill/set/detail', 'SkillSetController@list');
// General Routes
Route::get('get/payment/options', 'GeneralController@payment_list');
// End of General Routes
Route::group(['middleware' => ['jwt.verify']], function () {
    // Authenticate User
    Route::get('user', 'UserController@getAuthenticatedUser');
    Route::get('closed', 'DataController@closed');
    Route::get('get/all/non_rented/properties', 'PropertyController@get_all_non_rented_properties');
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
    });

    /*=====  End of Applicant Api Details  ======*/

});
