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
Route::group(['middleware' => ['localization']], function () {
    /*===============================================================================
    =            Section for Company Admin and Super Admin Portal                   =
    ================================================================================*/
    Route::get('/change/language/{locale}', function ($locale) {
        Session::put('locale', $locale);
        App::setLocale($locale);
        return back();
    });
    Route::group(['prefix' => 'admin'], function () {
        Route::get('/login', 'AdminControllers\InternalUserController@login_screen');
        Route::get('/register', 'AdminControllers\InternalUserController@register_screen');
        Route::post('register/user', 'AdminControllers\InternalUserController@register_user');
        Route::post('login/user', 'AdminControllers\InternalUserController@login_user');
        Route::get('/password/reset', 'AdminControllers\InternalUserController@reset_password');
        /*----------  Subsection for Account Payment Using Payment Gateway  ----------*/
        Route::get('payment/success', 'AdminControllers\InternalUserController@payment_success');
        Route::get('payment/refused', 'AdminControllers\InternalUserController@payment_refused');
        Route::get('payment_for_account/{id}', 'AdminControllers\InternalUserController@show_payment_screen');
        Route::post('payment/details', 'AdminControllers\InternalUserController@account_activation');
        /**
         *
         * Block for Super Admin Routes
         *
         */

        Route::group(['middleware' => ['super_admin']], function () {
            Route::group(['middlewareGroups' => ['web']], function () {
                Route::get('/dashboard', 'SuperAdminControllers\DashboardController@dashboard_screen');
                /*----------- Company Management -----------*/
                Route::get('/company_list', 'SuperAdminControllers\CompanyController@company_list')->name('company.list');
                Route::get('/company/edit/{id}', 'SuperAdminControllers\CompanyController@editCompany')->name('company.edit');
                Route::post('/company/update/', 'SuperAdminControllers\CompanyController@updateCompany')->name('company.update');
                Route::post('/company/state/', 'SuperAdminControllers\CompanyController@changeCompanyState')->name('company.change.state');
                Route::get('/company/details/{id}', 'SuperAdminControllers\CompanyController@companyDetails')->name('company.details');
                Route::get('company/transactions', 'SuperAdminControllers\CompanyController@companyTransactionDetails');
                /*----------  Subsection For the Site Details Routes  ----------*/

                Route::get('/site/list', 'SuperAdminControllers\SiteManagmentController@site_list');

                /*----------  Subsection For the Staff Routes  ----------*/
                Route::get('staff/form', 'StaffController@staff_form');
                Route::post('check_staff_validations', 'StaffController@staff_validation');
                Route::post('/staff/member/save', 'StaffController@staff_insert');
                Route::get('staff/member/list', 'StaffController@staff_list');
                Route::post('delete_staff', 'StaffController@delete_staff');
                /*----------  Subsection For the User Roles Routes  ----------*/
                Route::get('/roles_list', 'UserRolesController@show_roles_list');
                Route::get('/roles_form', 'UserRolesController@show_roles_form');
                Route::post('/add/role_permissions', 'UserRolesController@roles_permission_save');
                Route::post('/roles/permission_validation', 'UserRolesController@roles_permission_validation');
                Route::post('delete_role', 'UserRolesController@delete_role');
                /*----------  Subsection For Logout  ----------*/
                Route::get('/logout', 'AdminControllers\InternalUserController@logout');
                /*----------  Subsection For the Profile \ Settings Routes  ----------*/
                Route::get('profile', 'AdminControllers\ProfileController@profile_screen');
                Route::post('/profile/update', 'AdminControllers\ProfileController@profile_update');
                Route::get('settings', 'AdminControllers\ProfileController@setting_screen');
                Route::post('/settings/update', 'AdminControllers\ProfileController@setting_update');
                Route::post('update/password', 'AdminControllers\ProfileController@updatePassword')->name('admin.change.password');
                /*----------  Subsection for Auditing of the System  ----------*/
                Route::get('/audit_trail', 'AdminControllers\ProfileController@audit_trail');
                /*----------  Subsection for update Pending account status (approve / disapprove)  ----------*/
                Route::post('/update/account/status', 'AdminControllers\ProfileController@change_approval_status');
                /*----------  Subsection for reporting of (Paid Active Account, Free Active Accounts, Rejected Accounts)  ----------*/
                Route::get('/companies_report', 'SuperAdminControllers\CompanyController@companies_reporting');
                /*----------  Raw Items Category Section  ----------*/
                Route::get('item_category_form', 'SuperAdminControllers\RawItemCategoryController@screen_display');
                Route::post('/raw_category/record/validation', 'SuperAdminControllers\RawItemCategoryController@validation');
                Route::post('/raw_category/save/record', 'SuperAdminControllers\RawItemCategoryController@save');
                Route::get('/raw_category/list', 'SuperAdminControllers\RawItemCategoryController@list');
                Route::post('/raw_category/delete', 'SuperAdminControllers\RawItemCategoryController@delete');
                /*----------  Suppliers Section  ----------*/
                Route::get('supplier_form', 'SuperAdminControllers\SupplierController@screen_display');
                Route::post('/supplier/record/validation', 'SuperAdminControllers\SupplierController@validation');
                Route::post('/supplier/save/record', 'SuperAdminControllers\SupplierController@save');
                Route::get('/supplier/list', 'SuperAdminControllers\SupplierController@list');
                Route::post('/supplier/delete', 'SuperAdminControllers\SupplierController@delete');
                /*----------  Raw Items Section  ----------*/
                Route::get('raw_item_form', 'SuperAdminControllers\RawItemsController@screen_display');
                Route::post('/raw_item/record/validation', 'SuperAdminControllers\RawItemsController@validation');
                Route::post('/raw_item/save/record', 'SuperAdminControllers\RawItemsController@save');
                Route::get('/raw_item/list', 'SuperAdminControllers\RawItemsController@list');
                Route::post('/raw_item/delete', 'SuperAdminControllers\RawItemsController@delete');
                /*----------  User Packages Section  ----------*/
                Route::get('packages_form', 'SuperAdminControllers\UserPackagesController@screen_display');
                Route::post('/packages/record/validation', 'SuperAdminControllers\UserPackagesController@validation');
                Route::post('/packages/save/record', 'SuperAdminControllers\UserPackagesController@save');
                Route::get('/packages/list', 'SuperAdminControllers\UserPackagesController@list');
                Route::post('/packages/delete', 'SuperAdminControllers\UserPackagesController@delete');
                /*---------- Payment Options Section  ----------*/
                Route::get('payments_form', 'SuperAdminControllers\PaymentsController@screen_display');
                Route::post('/payments/record/validation', 'SuperAdminControllers\PaymentsController@validation');
                Route::post('/payments/save/record', 'SuperAdminControllers\PaymentsController@save');
                Route::get('/payments/list', 'SuperAdminControllers\PaymentsController@list');
                Route::post('/payments/delete', 'SuperAdminControllers\PaymentsController@delete');
                /*---------- Skill Set Section  ----------*/
                Route::get('skills_form', 'SuperAdminControllers\SkillController@screen_display');
                Route::post('/skills/record/validation', 'SuperAdminControllers\SkillController@validation');
                Route::post('/skills/save/record', 'SuperAdminControllers\SkillController@save');
                Route::get('/skills/list', 'SuperAdminControllers\SkillController@list');
                Route::post('/skills/delete', 'SuperAdminControllers\SkillController@delete');
                /*----------  User Details  ----------*/
                Route::get('user_details', 'UserController@user_detail_screen');
            });
        });
    });
    /*=====  End of Section for Company Portal  ======*/
});

Route::get('/', function () {
    return view('landing_page');
});
Auth::routes();
// Change Password
Route::get('/password/change', 'UserController@showForgetPasswordPage');
// Password Save Record
Route::post('new/password/save/record', 'UserController@savePassword');
Route::get('test', 'UserController@test_screen');
Route::get('payment/process', 'UserController@payment_process')->name('payment.process');
