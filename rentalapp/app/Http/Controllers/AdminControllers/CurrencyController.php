<?php

namespace App\Http\Controllers\AdminControllers;

use App\Currency;
use App\CurrencyList;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class CurrencyController extends Controller
{
    public function addForm()
    {
        $currencies = CurrencyList::all()->sortBy('currency');
        return view('company_portal.currency.add_currency')->with('currencies', $currencies);
    }
    public function addCurrency(Request $request)
    {
        $this->validate($request,[
            "currency_name" => "required|integer",
            "rate" => "required|numeric"
        ],
        [
            "currency_name.required" => "Currency is required",
            "currency_name.integer"  => "Invalid currency type",
            "rate.required" => "Current currency rate is required",
            "rate.numeric" => "rate must be a number"
        ]);
        // check for same currency duplication for current user
        $user = Auth::user();
        if(is_null(Currency::where([['Tenant_Id', $user->TenantId], ['currency_id', $request->currency_name]])->first()))
        {
            $currency = new Currency();
            $currency->currency_id = $request->currency_name;
            $currency->Tenant_Id = Auth::user()->TenantId;
            // if user checked isBaseCurrency
            /*if(isset($request->isbase))
            {
                $currency->isBaseCurrency = $request->isbase;
            }*/
            $currency->CurrentRate = round($request->rate, 2);
            $currency->save();
            return redirect()->route('currency.show')->with('success', 'New Currency has been added');
        }
        else
        {
            return redirect()->route('currency.form')->with('error', 'You can not added same currency twice');
        }
    }
    public function showCurrencies()
    {
        $currencies = Currency::with('currencyList')->where('Tenant_Id', Auth::user()->TenantId)->get();
        return view('company_portal.currency.currency_list')->with('currencies', $currencies);
    }
    /*public function editCurrency($id)
    {
        $currency = Currency::find(Crypt::decryptString($id));
        $currencies = CurrencyList::all()->sortBy('currency');
        return view('company_portal.currency.edit_currency')->with(['editable' => $currency, 'currencies' => $currencies]);
    }*/
    /*public function updateCurrency(Request $request)
    {
        $this->validate($request,[
            "currency_name" => "required|integer",
            "rate" => "required|numeric"
        ],
            [
                "currency_name.required" => "Currency is required",
                "currency_name.integer"  => "Invalid currency type",
                "rate.required" => "Current currency rate is required",
                "rate.numeric" => "rate must be a number"
            ]);
        $currency = Currency::find(Crypt::decryptString($request->id));
        if(!is_null($currency))
        {
            $currency->CurrentRate = $request->rate;
            $currency->save();
            return redirect()->route('currency.show')->with('success', 'Currency has been updated');
        }
        else
        {
            return redirect()->back()->withInput()->with('error', 'Could not find currency');
        }
    }*/
    public function deleteCurrency(Request $request)
    {
        $currency = Currency::with("partnerAccountDetails", "sitesAccount", "tenantAccountDetails")->find(Crypt::decryptString($request->id));
        if(!is_null($currency))
        {
            try
            {
                if ($currency->sitesAccount->count() > 0 || $currency->partnerAccountDetails->count() > 0 || $currency->tenantAccountDetails->count() > 0)
                {
                    return redirect()->back()->with("error", "Currency cannot be delete because some of its information is still being used");
                }
                else
                {
                    $currency->delete();
                    return redirect()->route('currency.show')->with('success', 'Currency has been deleted');
                }
            }
            catch (\Exception $e)
            {
                Log::error("DELETE CURRENCY ".$e->getMessage()." LINE NO ".$e->getLine());
                return redirect()->back()->with('error', 'Can not delete currency');
            }
        }
        else
        {
            return redirect()->back()->with('error', 'Could not find currency');
        }
    }
}
