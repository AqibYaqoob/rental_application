<?php

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

Route::get('/', function () {
    return view('welcome');
});
// Change Password
Route::get('/password/change', 'UserController@showForgetPasswordPage');
Route::post('new/password/save/record', 'UserController@savePassword');
