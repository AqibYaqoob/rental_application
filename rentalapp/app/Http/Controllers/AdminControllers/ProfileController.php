<?php

namespace App\Http\Controllers\AdminControllers;

use App\Audit;
use App\Currency;
use App\Http\Controllers\Controller;
use App\PartnerSetting;
use App\RejectedTenantAccounts;
use App\StaffMember;
use App\Tenants;
use App\TenantSettings;
use App\User;
use Auth;
use Carbon\Carbon;
use GeneralFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;
use View;

class ProfileController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /*=============================================================
    =            Section For Profile Screen Functions             =
    =============================================================*/
    /**
     *
     * Profile Screen Display Function
     *
     */

    public function profile_screen(Request $req)
    {
        $data['title']           = 'Profile Settings';
        $getUserDetails          = User::with('staff_members')->where('id', Auth::user()->id)->first();
        $data['profile_details'] = [];
        if ($getUserDetails) {
            $data['profile_details'] = $getUserDetails->toArray();
        }
        return view('company_portal.layouts.profile.profile_screen', $data);
    }

    /**
     *
     * Profile Update Data Function
     *
     */

    public function profile_update(Request $req)
    {
        // 1) Validation
        $validationArray = [
            'p-full_name'     => 'required|max:255',
            'p-mobile_number' => 'required|numeric',
            'p-home_number'   => 'required',
            'p-user_name'     => 'required|max:255',
            'p-email'         => 'required|email',
        ];
        $rules = [
            'p-full_name.required'     => 'Full Name is required',
            'p-full_name.max'          => 'Full Name should not be more than 255 characters',
            'p-mobile_number.required' => 'Mobile Number is required',
            'p-mobile_number.numeric'  => 'Mobile Number is Numeric Only',
            'p-home_number.required'   => 'Home Number is Required',
            'p-user_name.required'     => 'Username is requi
            if ($validator->fails()) {
            return back()->withErrors($validator);
        }red',
            'p-user_name.max'          => 'Username should not be more than 255 characters',
            'p-email.required'         => 'Email address is required',
            'p-email.email'            => 'Email format is not correct',
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => 'error', 'msg_data' => $errors]);
        }

        // 2) Check if Contact Info Exist or Create Account
        $contactRecord = [
            'mobile_number' => $req->input('p-mobile_number'),
            'home_number'   => $req->input('p-home_number'),
            'staff_name'    => $req->input('p-full_name'),
            'user_id'       => Auth::user()->id,
            'role_id'       => 0,
        ];
        $staffMember = StaffMember::where('user_id', Auth::user()->id);
        $result      = $staffMember->get();
        if (count($result->toArray()) > 0) {
            // 3) Update Record
            $staffMember->update($contactRecord);
        } else {
            // 3) Create Record
            StaffMember::create($contactRecord);
        }
        // 4) Update Authentication Data
        User::where('id', Auth::user()->id)->update(['Username' => $req->input('p-user_name'), 'email' => $req->input('p-email')]);
        return response()->json(['status' => 'success']);
    }

    /*=====  End of Section Internal User Login Functions  ======*/

    /*==============================================================
    =            Section for Settings Screens Functions            =
    ==============================================================*/
    /**
     *
     * Settings Screens Function
     *
     */

    public function setting_screen(Request $req)
    {
        $user                           = Auth::user();
        $data['title']                  = 'Settings';
        $getTenantSettings              = TenantSettings::where('UserId', $user->id)->first();
        $data['tenant_setting_details'] = [];
        if ($getTenantSettings) {
            $data['tenant_setting_details'] = $getTenantSettings->toArray();
        }
        $currencies = Currency::with('currencyList')->where('Tenant_Id', $user->TenantId)->get();
        $parnters   = User::where([['TenantId', $user->TenantId], ['Roles', 3]])->get();
        return view('layouts.settings.setting_screen', $data)->with(['currencies' => $currencies, 'partners' => $parnters]);
    }

    /**
     *
     * Settings Update Function
     *
     */

    public function setting_update(Request $req)
    {
        // 1) Validation
        $validationArray = [
            'system_time_zone' => 'required',
        ];
        $rules = [
            'system_time_zone.required' => 'Time Zone is required',
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // 2) Check Existance
        $settingsRecord = [
            'SettingName' => 'time_zone',
            'ValueData'   => $req->input('system_time_zone'),
            'TenantId'    => Auth::user()->TenantId,
            'UserId'      => Auth::user()->id,
        ];
        $record = TenantSettings::where('UserId', Auth::user()->id);
        $result = $record->get();
        if (count($result->toArray()) > 0) {
            // 3) Update Record
            $record->update($settingsRecord);
        } else {
            // 3) Create Record
            TenantSettings::create($settingsRecord);
        }
        return redirect()->back()->with('status', 'Succesfuly updated record');
    }

    /*=====  End of Section for Settings Screens Functions  ======*/
    public function updateCurrencies(Request $request)
    {
        $this->validate($request, ['currency' => 'required|array'], ['currency.required' => 'Currency is required. If you not added currency yet then please add it first']);
        $baseFlag = false;
        if (count($request->currency) == 1) {
            $object = Currency::find(key($request->currency));
        } else {
            $object = Currency::whereBetween('id', array_keys($request->currency))->get();
        }
        if ($object->count() == 1) {
            $object->CurrentRate = $request->currency[$object->Id];
            // if base currency is not set
            if ($object->isBaseCurrency != 1) {
                $object->isBaseCurrency = ($object->Id == $request->baseCurrency) ? 1 : 0;
            }
            $object->save();
            return redirect()->route('profile.setting')->with('success', 'Currencies has been updated');
        } elseif ($object->count() > 1) {
            foreach ($object as $currency) {
                if ($currency->isBaseCurrency == 1) {
                    $baseFlag = true;
                }
                if (array_key_exists($currency->Id, $request->currency)) {
                    $currency->CurrentRate = $request->currency[$currency->Id];
                } else {
                    continue;
                }
                // check if already base currency is set
                if (!$baseFlag) {
                    $currency->isBaseCurrency = ($currency->Id == $request->baseCurrency) ? 1 : 0;
                }

                $currency->save();
            }
        }

        /*foreach ($request->currency as $key => $currency) {
        $object = Currency::find($key);
        dd($object);
        if (!is_null($object))
        {
        //update
        $object->CurrentRate    = $currency;
        $object->isBaseCurrency = ($key == $request->baseCurrency) ? 1 : 0;
        $object->save();
        }
        else
        {
        continue;
        }
        }*/
        return redirect()->route('profile.setting')->with('success', 'Currencies has been updated');
    }
    public function partnerCurrency(Request $request)
    {
        $validationArray = [
            "currency" => "required|integer",
            "partner"  => "required|integer",
        ];
        $rules = [
            "currency.required" => "Please select currency",
            "partner.required"  => "Please select partner",
        ];

        $validator = Validator::make($request->all(), $validationArray, $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }
        // check if already currency assigned to a partner
        $setting = PartnerSetting::where([['settingName', 'base_currency'], ['partner_id', $request->partner]])->first();
        if (!is_null($setting)) {
            // update settings
            $setting->value = $request->currency;
            $setting->save();
            return redirect()->route('partner.assigned.currency.list')->with('success', 'Partner setting has been updated');
        } else {
            // assign currency
            $partnerSetting              = new PartnerSetting();
            $partnerSetting->settingName = 'base_currency';
            $partnerSetting->value       = $request->currency;
            $partnerSetting->partner_id  = $request->partner;
            $partnerSetting->created_at  = Carbon::now();
            $partnerSetting->save();
            return redirect()->route('partner.assigned.currency.list')->with('success', 'New setting has been created for partner');
        }
    }
    public function partnerAssignedCurrencies()
    {
        $settings = User::with('partner_settings')->where('TenantId', Auth::user()->TenantId)->where('Roles', 3)->get();
        return view('company_portal.layouts.partners.list_partner_currencies')->with('settings', $settings);
    }
    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|string|min:6|confirmed',
        ],
            [
                'password.required'  => 'Please provide new password',
                'password.string'    => 'Password must be a string',
                'password.min'       => 'Minimum password should be 6 characters long',
                'password.confirmed' => 'Password and Confirm password does not match',
            ]
        );
        // check if user is loggedIn
        if (Auth::check()) {
            $user = Auth::user();
            // check if user provided password is same as stored password
            if (Hash::check($request->old_password, $user->password)) {
                $user->password = bcrypt($request->password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                return redirect()->back()->with('success', 'Password has been changed');
            } else {
                return redirect()->back()->with('error', 'Credentials does not match');
            }
        }
    }

    /**
     *
     * Section for Audit Trail Functionality
     *
     */
    public function audit_trail(Request $req)
    {
        $getAuditDetails       = Audit::with('user')->get()->toArray();
        $data['audit_details'] = $getAuditDetails;
        $data['title']         = 'Audit Trail';
        return view('layouts.audit_trail.list', $data);
    }

    /**
     *
     * Section for Changing Approval status
     *
     */
    public function change_approval_status(Request $req)
    {
        // dd($req->all());
        // 1) Validation
        if ($req->state == 1) {
            $validationArray = [
                'account_type' => 'required',
            ];
        } else {
            $validationArray = [
                'reason' => 'required',
            ];
        }
        $rules = [
            'account_type.required' => 'Please select the Account Type (Free or Paid Account)',
            'reason.required'       => 'Please provide reason for disapproving account',
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }
        $user = User::find($req->id);
        // Add Free or Paid Account
        if ($req->state == 1) {
            $accountStatus = 1;
            $content       = 'Thanks, for Registring to our System. You can now enjoy the Sevices of our Application.';
            if ($req->account_type == 2) {
                $content       = 'Administration has approved your account, You need to Pay first to use the system please click on a link below to pay amount. Thanks <br> <a href="' . url("/admin/payment_for_account/" . $req->id) . '">Click Here</a>';
                $accountStatus = 0;
            }
            $updateRecord = $user->update(['account_type' => $req->account_type, 'AccountStatus' => $accountStatus]);
            $msg          = 'Successfully Upated account status';
            // 1) Send Account Activation Confirmation to User to Login
            $data = [
                'subject'         => 'Account Confirmation',
                'heading_details' => 'Account Details',
                'sub_heading'     => 'Congratulations, your account is approved by administration',
                'heading'         => 'Account Confirmation',
                'title'           => 'Company <u><b>' . $user->company_name->TenantName . '</b></u> Registration',
                'content'         => $content,
                'email'           => $user->EmailAddress,
            ];
            GeneralFunctions::sendEmail($data);
        } else {
            // 1) Send Account Activation Confirmation to User to Login
            $data = [
                'subject'         => 'Account Confirmation',
                'heading_details' => 'Account Details',
                'sub_heading'     => 'Sorry, your account is disapproved by administration',
                'heading'         => 'Account Confirmation',
                'title'           => 'Company <u><b>' . $user->company_name->TenantName . '</b></u> Registration',
                'content'         => $req->reason,
                'email'           => $user->EmailAddress,
            ];
            // Delete Record from Database First
            $deleteRecord = User::find($req->id);
            RejectedTenantAccounts::create(['tenant_name' => $deleteRecord->tenants->TenantName, 'username' => $deleteRecord->Username, 'email_address' => $deleteRecord->EmailAddress, 'disapproval_date' => GeneralFunctions::convertToDateTime(now())]);
            $tenant_id            = $deleteRecord->TenantId;
            $tenantSettingsRecord = $deleteRecord->tenant_settings()->delete();
            $deleteRecord->delete();
            Tenants::find($tenant_id)->delete();
            GeneralFunctions::sendEmail($data);
            $msg = 'Successfully, disapproved Account with given reason';
        }

        return redirect()->back()->with('success', $msg);
    }

}
