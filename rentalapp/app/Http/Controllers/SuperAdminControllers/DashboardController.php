<?php

namespace App\Http\Controllers\SuperAdminControllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use View;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     *
     * Dashboard Screen Display Function
     *
     */

    public function dashboard_screen(Request $req)
    {
        $data['title']                   = 'Dashboard';
        $data['record']                  = User::with('company_name')->where('Roles', 2)->where('isAdmin', 1)->where('account_type', 0)->where('AccountStatus', 0)->get();
        $data['active_tenant_accounts']  = User::where([['AccountStatus', 1], ['Roles', 2], ['isAdmin', 1]])->get()->toArray();
        $data['active_staff_members']    = User::where([['AccountStatus', 1], ['Roles', 1], ['isAdmin', 0]])->get()->toArray();
        $data['pending_tenant_accounts'] = User::where([['AccountStatus', 0], ['Roles', 1], ['isAdmin', 0], ['account_type', 0]])->get()->toArray();
        $data['record']->toArray();

        return view('super_admin_portal.layouts.dashboard.dashboard', $data);
    }
}
