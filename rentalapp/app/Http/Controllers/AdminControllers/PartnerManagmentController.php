<?php

namespace App\Http\Controllers\AdminControllers;

use App\Currency;
use App\DailyInterest;
use App\Http\Controllers\Controller;
use App\PartnerAccountDetails;
use App\PartnerAssignment;
use App\PartnerProfitLoss;
use App\PartnerSetting;
use App\SiteAccountShareholder;
use App\Sites;
use App\SitesAccount;
use App\StaffMember;
use App\Tenants;
use App\User;
use Auth;
use Carbon\Carbon;
use GeneralFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use Validator;

class PartnerManagmentController extends Controller
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
     * Partner Screen Display Function
     *
     */

    public function partner_screen(Request $request)
    {
        $data['title']  = 'Add Partner';
        $data['record'] = [];
        $data['partner_base_currency'] = null;
        if ($request && $request->id != '') {
            // Decrypt Url Parameter
            $requestId = Crypt::decryptString($request->id);
            $getRecord = User::with('staff_members')->where('id', $requestId)->first();
            if ($getRecord) {
                $data['record'] = $getRecord->toArray();
            }
            $data['partner_base_currency'] = PartnerSetting::where('partner_id', $requestId)->where('settingName', 'base_currency')->first();
            $data['partner_base_currency'] = $data['partner_base_currency']->value;
        }
        $data['currency_list'] = Currency::with('currencyList')->where('Tenant_Id', Auth::user()->TenantId)->get();
        return view('company_portal.layouts.partners.add_partners', $data);
    }

    /**
     *
     * Partner Validation Function
     *
     */

    public function partner_validation(Request $request)
    {
        $validationArray = [
            'name'                 => 'required|max:255',
            'phone_number'         => 'required',
            'email'                => 'required',
            'assign_base_currency' => 'required'
        ];
        $validator = Validator::make($request->all(), $validationArray);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => 'error', 'msg_data' => $errors]);
        }
        // GeneralFunctions::ajax_debug();
        return response()->json(['status' => 'success', 'data' => $request->all()]);
    }

    /**
     *
     * Partner Record Save Display Function
     *
     */

    public function partner_insert(Request $request)
    {

        // Adding Temporary Password for the staff
        $uniqueId = substr(uniqid(rand(), true), 2, 2);
        $password = $request->input('email') . $uniqueId;

        if ($request && $request->id != '') {
            // Decrypt Url Parameter
            $requestId    = Crypt::decryptString($request->id);
            $userInstance = User::find($requestId);

            // Attaching Roles and User
            $updateUserData  = User::where('id', $requestId)->update(['EmailAddress' => $request->email, 'Username' => $request->email, 'interest_rate' => $request->rate]);
            $updateStaffData = StaffMember::where('user_id', $requestId)->update(['mobile_number' => $request->phone_number, 'home_number' => $request->home_number, 'staff_name' => $request->name]);
            // Currency Update
            PartnerSetting::where('partner_id', $requestId)->where('settingName', 'base_currency')->update(['value' => $request->assign_base_currency]);
            return back()->with('status', 'Record has been saved successfully');
        }

        // Adding User Data
        // Check if User Already Exist
        $userCheck = User::where('EmailAddress', $request->email)->where('TenantId', Auth::user()->TenantId)->get();
        if (count($userCheck) > 0) {
            return back()->withErrors(['User Already Exist in Company'])->withInput();
        }
        $userData = [
            'Username'      => $request->input('email'),
            'password'      => Hash::make($password),
            'Roles'         => 3,
            'TenantId'      => Auth::user()->TenantId,
            'CreatedBy'     => Auth::user()->Username,
            'AccountStatus' => 1,
            'EmailAddress'  => $request->input('email'),
            'interest_rate' => $request->rate,
        ];

        $userDetails = $userSave = User::create($userData);

        // Save record in Staff Detail Screen
        $staffDetail = [
            'staff_name'    => $request->input('name'),
            'mobile_number' => $request->input('phone_number'),
            'home_number'   => $request->input('home_number'),
            'role_id'       => 0,
            'user_id'       => $userSave->id,
        ];

        // Assign Base Currency to Partner
        $partnerSettings = [
            'settingName' => 'base_currency',
            'value'       => $request->assign_base_currency,
            'partner_id'  => $userDetails->id,
        ];

        $getCompanyDetails = Tenants::where('id', Auth::user()->TenantId)->first();
        $company           = 'Betting Form Administration Team';
        if ($getCompanyDetails) {
            $company = $getCompanyDetails->TenantName;
        }

        StaffMember::create($staffDetail);
        PartnerSetting::create($partnerSettings);
        $userInstance = User::find($userDetails->id);
        $data = [
            'subject'         => 'New Partners Account',
            'heading_details' => 'Welcome New Partner of  ' . $request->input('name'),
            'sub_heading'     => 'Account has Been Created by the Company Owner',
            'heading'         => 'Betting Form',
            'title'           => 'New Partner',
            'content'         => 'Congratulationss! for becoming part of <u>' . $company . '</u> u have been a new member. You can login from portal to strat the tasks. <br><b>User Name : ' . $request->input('email') . '</b><br> <b>Password : </b>' . $password,
            'email'           => $request->input('email'),
        ];
        GeneralFunctions::sendEmail($data);
        return back()->with('status', 'Record has been saved successfully');
    }

    /**
     *
     * Partner List Display Function
     *
     */

    public function partner_list()
    {
        $data             = [];
        $data['title']    = "Partners List";
        $data['partners'] = [];
        $data['partners'] = User::with('staff_members', 'partner_settings.currency.currencyList')->where('Roles', 3)->where('TenantId', Auth::user()->TenantId)->where('IsAdmin', 0)->get();
        if (isset($data['partners'])) {
            $data['partners'] = $data['partners']->toArray();
        }
        return view('company_portal.layouts.partners.partners_list', $data);
    }
    /*
     * Partner dashboard after login
     */
    public function home()
    {
        // 1) Total No. of site account shareholders
        $siteAccountShareholderCount = SiteAccountShareholder::where('PartnerId', Auth::user()->id)->get()->toArray();
        // 2) Get Partner Base Currency
        $data['base_currency'] = PartnerSetting::with('currency.currencyList')->where('partner_id', Auth::user()->id)->where('settingName', 'base_currency')->first();
        if ($data['base_currency']) {
            $data['base_currency'] = $data['base_currency']->currency->currencyList->code;
        } else {
            $data['base_currency'] = null;
        }
        $data['site_account_shareholder_count'] = $siteAccountShareholderCount;
        return view('partner_portal.dashboard', $data);
    }
    public function siteAccountForm()
    {
        $sites     = Sites::all();
        $currenies = Currency::all();
        return view('partner_portal.site_account')->with(['sites' => $sites, 'currencies' => $currenies]);
    }
    public function addSiteAccount(Request $request)
    {
        $this->validate($request, [
            "site"        => "required|integer",
            "currency"    => "required|integer",
            "siteCode"    => "required|numeric",
            "labelColor"  => "required|string|max:7",
            "turnover"    => "required|numeric",
            "maxBet"      => "required|numeric",
            "remarkLabel" => "required|string|max:7",
            "remarks"     => "required|string",
        ]);
        $user = Auth::user();
        // check already exist
        $account = SitesAccount::where([['SiteId', $request->site], ['TenantId', $user->TenantId], ['CurrencyId', $request->currency], ['user_id', $user->id]])->first();
        if (!is_null($account)) {
            return redirect()->back()->with('error', 'Record already exist');
        }
        $site                        = new SitesAccount();
        $site->SiteId                = $request->site;
        $site->TenantId              = $user->TenantId;
        $site->user_id               = $user->id;
        $site->CurrencyId            = $request->currency;
        $site->IsActive              = 1;
        $site->SiteAccountCode       = $request->siteCode;
        $site->SiteAccountLabelColor = $request->labelColor;
        $site->TotalTurnoverPercent  = round($request->turnover / 100, 2);
        $site->MaxSingleBet          = $request->maxBet;
        $site->Remarks               = $request->remarks;
        $site->RemarksLabelColor     = $request->remarkLabel;
        $site->created_at            = Carbon::now();
        $site->CreatedBy             = $user->Username;
        $site->save();
        return redirect()->route('partner.siteAccount.list')->with('success', 'Site account has been added');
    }
    public function listSiteAccount()
    {
        $accounts = SitesAccount::where('user_id', \Illuminate\Support\Facades\Auth::user()->id)->get();
        return view('partner_portal.account_list')->with('accounts', $accounts);
    }
    public function editSiteAccount($id)
    {
        $siteAccount                       = SitesAccount::find(Crypt::decryptString($id));
        $siteAccount->TotalTurnoverPercent = ($siteAccount->TotalTurnoverPercent * 100);
        $sites                             = Sites::all();
        $currenies                         = Currency::all();
        return (!is_null($siteAccount)) ? view('partner_portal.edit_account')->withSiteAccount($siteAccount)->with(['sites' => $sites, 'currencies' => $currenies]) : redirect()->back()->with('error', 'Could not find site account');
    }
    public function updateSiteAccount(Request $request)
    {
        $this->validate($request, [
            "site"                  => "required|integer",
            "currency"              => "required|integer",
            "SiteAccountCode"       => "required|numeric",
            "SiteAccountLabelColor" => "required|string|max:7",
            "TotalTurnoverPercent"  => "required|numeric",
            "MaxSingleBet"          => "required|numeric",
            "RemarksLabelColor"     => "required|string|max:7",
            "Remarks"               => "required|string",
        ]);

        $id   = Crypt::decryptString($request->id);
        $site = SitesAccount::find($id);
        if (!is_null($site)) {
            $site->SiteId                = $request->site;
            $site->CurrencyId            = $request->currency;
            $site->SiteAccountCode       = $request->SiteAccountCode;
            $site->SiteAccountLabelColor = $request->SiteAccountLabelColor;
            $site->TotalTurnoverPercent  = round($request->TotalTurnoverPercent / 100, 2);
            $site->MaxSingleBet          = $request->MaxSingleBet;
            $site->Remarks               = $request->Remarks;
            $site->RemarksLabelColor     = $request->RemarksLabelColor;
            $site->save();
            return redirect()->route('partner.siteAccount.list')->with('success', 'Site account has been updated');
        } else {
            return redirect()->back()->with('error', 'Could not find site account');
        }
    }
    public function deleteSiteAccount(Request $request)
    {
        $account = SitesAccount::find(Crypt::decryptString($request->id));
        if (!is_null($account)) {
            try
            {
                $account->delete();
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'can not delete site account');
            }
            return redirect()->back()->with('success', 'site account has been deleted');
        } else {
            return redirect()->back()->with('error', 'Could not find site account');
        }
    }
    /*public function showCurrencies()
    {
    $user = Auth::user();
    $setting = PartnerSetting::where('partner_id', $user->id)->first();
    $currencies = Currency::where('Tenant_Id', $user->TenantId)->get();
    return view('partner_portal.show_currencies')->with(['currencies' => $currencies, 'setting' => $setting]);
    }
    public function setBaseCurrency(Request $request)
    {
    if(isset($request->id))
    {
    // update partner settings
    $partnerSetting = PartnerSetting::find(Crypt::decryptString($request->id));
    if(!is_null($partnerSetting))
    {
    $partnerSetting->base_currency = $request->currency;
    $partnerSetting->save();
    return redirect()->back()->with(['success' => 'Base currency has been updated', 'setting' => $partnerSetting]);
    }
    else
    {
    return redirect()->back()->with('error', 'Could not find settings');
    }
    }
    else
    {
    $this->validate($request,[
    "currency" => "required|integer"
    ]);
    // create new settings
    $setting = new PartnerSetting();
    $setting->partner_id = Auth::user()->id;
    $setting->base_currency = $request->currency;
    $setting->created_at = Carbon::now();
    $setting->save();
    return redirect()->back()->with(['success' => 'base currency has been added', 'setting' => $setting]);
    }
    }*/
    public function listAssignedAccount()
    {
        $accounts = PartnerAssignment::where('PartnerId', Auth::user()->id)->get();
        return view('partner_portal.assigned_account')->with('accounts', $accounts);
    }
    public function assignBaseCurrency()
    {
        $user       = Auth::user();
        $currencies = Currency::where('Tenant_Id', $user->TenantId)->get();
        $parnters   = User::where([['TenantId', $user->TenantId], ['Roles', 3]])->get();
        return view('company_portal.currency.base_currency')->with(['currencies' => $currencies, 'partners' => $parnters]);
    }
    public function deletePartner(Request $request)
    {
        // check if user exist in database
        $user = User::with("siteAccountShareholder", "partnerAssignment")->find(Crypt::decryptString($request->id));
        if (!is_null($user))
        {
            try
            {
                if ($user->siteAccountShareholder->count() > 0 || $user->partnerAssignment->count() > 0)
                {
                    return redirect()->back()->with('error', 'Partner cannot be delete because some of its information is still being used');
                }
                else
                {
                    $user->delete();
                    // sending email to partner
                    $data = [
                        'subject'     => 'Account Deletion',
                        'sub_heading' => 'Your account has been deleted by the company owner',
                        'heading'     => 'Betting Form',
                        'email'       => $user->EmailAddress,
                    ];
                    GeneralFunctions::sendEmail($data);
                    return redirect()->back()->with('success', 'Partner has been deleted');
                }
            }
            catch (\Exception $e)
            {
                Log::error("DELETE PARTNER:". $e->getMessage()." ON LINE ".$e->getLine());
                return redirect()->back()->with('error', 'An error occurred');
            }
        }
        else
        {
            return redirect()->back()->with('error', 'Partner could not found');
        }
    }
    public function individualReport(Request $request, $id)
    {
        $user                    = User::find(Crypt::decryptString($id));
        $partnerBaseCurrencyRate = PartnerSetting::where([['settingName', 'base_currency'], ['partner_id', $user->id]])->first();
        if (!is_null($partnerBaseCurrencyRate)) {
            $rate = Currency::find($partnerBaseCurrencyRate->value)->CurrentRate;
        }
        if (!is_null($user)) {
            $data                  = PartnerProfitLoss::with('sitesAccount')->where('partnerId', $user->id)->get();
            $partnerAccountDetails = PartnerAccountDetails::with('currency')->where('UserId', $user->id)->get();
            $totalProfitLoss       = $data->sum('TotalProfitLoss');
        } else {
            return redirect()->back()->with('error', 'No partner found with given id');
        }
        return view('company_portal.reports.individual_report')->with(['profitLoss' => $data, 'totalProfitLoss' => $totalProfitLoss, 'details' => $partnerAccountDetails, 'rate' => $rate ?? 0.00, 'id' => $id]);
    }
    public function interestReportScreen()
    {
        return view('company_portal.reports.partner_interest');
    }
}
