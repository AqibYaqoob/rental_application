<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Support\Facades\DB;
use View;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check())
        {
            if(Auth::user()->Roles == 2)
            {
                // get tenant all currencies
                // check if count is greater then 0, that means he added some currencies
                // check for base currency is set or not
                // if set then pass request to next
                // if not then redirect him to set base currency
                $allowedURI = ['admin/company/currency/show', 'admin/company/currency/add', 'admin/company/currency/list', 'admin/company/settings', 'admin/company/settings/update/currency', 'admin/company/logout', 'admin/company/dashboard'];
                if (array_search($request->route()->uri(), $allowedURI) === false)
                {
                    $tenantCurrency = DB::table('currencies')->select('isBaseCurrency')->where("Tenant_Id", Auth::user()->TenantId)->get();
                    if ($tenantCurrency->count() > 0)
                    {
                        $baseCurrency = $tenantCurrency->whereIn('isBaseCurrency', 1);
                        if($baseCurrency->count() !== 1)
                        {
                            return redirect()->route('profile.setting')->with('error', 'You can not access other pages until you set your base currency');
                        }
                    }
                    else
                    {
                        // redirect user to add currency page
                        return redirect()->route('currency.form')->with('error', 'You can not access other pages until you add your currency');
                    }
                }
                return $next($request);
            }
            else
            {
                return redirect('/admin/login');
            }
        }
        else
        {
            return redirect('/admin/login');
        }
    }
}
