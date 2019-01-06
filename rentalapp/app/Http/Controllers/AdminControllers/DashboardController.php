<?php

namespace App\Http\Controllers\AdminControllers;

use App\Currency;
use App\Http\Controllers\Controller;
use App\SiteAccountShareholder;
use App\SitesAccount;
use App\SitesAccountTransaction;
use App\TenantAccountDetails;
use App\TenantProfitLoss;
use App\User;
use Illuminate\Http\Request;
use View;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();
        $siteAccounts = SitesAccount::where('TenantId', $user->TenantId)->get();
        $totalPartners = User::where([['TenantId', $user->TenantId], ['Roles', 3], ['AccountStatus', 1]])->count();
        if($siteAccounts->count() == 1)
            $totalSharedAccounts = SiteAccountShareholder::where('SiteAccountId', $siteAccounts->pluck('Id')->toArray()[0])->count();
        elseif($siteAccounts->count() != 0 && $siteAccounts->count() > 1)
            $totalSharedAccounts = SiteAccountShareholder::whereBetween('SiteAccountId', $siteAccounts->pluck('Id')->toArray())->count();
        //$baseCurrency = Currency::with('currencyList')->where([['Tenant_Id', $user->TenantId], ['isBaseCurrency', 1]])->first();
        if($siteAccounts->count() == 1)
            $recentTransaction = SitesAccountTransaction::where('SiteAccountId', $siteAccounts->pluck('Id')->toArray()[0])->with('site_account')->orderBy('TransactionDate', 'desc')->take(5)->get();
        elseif($siteAccounts->count() != 0 && $siteAccounts->count() > 1)
            $recentTransaction = SitesAccountTransaction::whereBetween('SiteAccountId', $siteAccounts->pluck('Id')->toArray())->with('site_account')->orderBy('TransactionDate', 'desc')->take(5)->get();
        /*$tenantAccountDetails = TenantAccountDetails::where('UserId', $user->TenantId)->get();//->sum('TotalProfitLoss');
        $balance = 0;
        foreach($tenantAccountDetails as $tenantAccountDetail)
        {
            $balance += $tenantAccountDetail->Amount/$tenantAccountDetail->Current_Rate;
        }*/
        $data['title'] = 'dashboard';
        return view('company_portal.layouts.dashboard.dashboard', $data)->with(['record' => $recentTransaction ?? [], 'totalSiteAccount' => $siteAccounts->count() ?? 0, 'totalPartners' => $totalPartners ?? 0, 'totalSharedAccounts' => $totalSharedAccounts ?? 0]);
    }
}
