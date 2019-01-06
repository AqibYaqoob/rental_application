<?php

namespace App\Http\Controllers\SuperAdminControllers;

use App\Currency;
use App\Http\Controllers\Controller;
use App\RejectedTenantAccounts;
use App\SitesAccount;
use App\TenantAccountDetails;
use App\Tenants;
use App\User;
use GeneralFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use View;

class CompanyController extends Controller
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

    public function company_list(Request $req)
    {
        $data['title']  = 'Company List';
        $data['record'] = User::with('company_name')->where('Roles', 2)->where('isAdmin', 1)->get();
        $data['record']->toArray();
        return view('super_admin_portal.layouts.dashboard.company.company_list', $data);
    }
    /*
     * Edit company information
     * $id is company id
     */
    public function editCompany($id)
    {
        $company = User::where(['id' => Crypt::decryptString($id)], ['Roles' => 2])->first();
        if (empty($company->toArray())) {
            return redirect()->back()->with('error', 'Company not found');
        } else {
            return view('super_admin_portal.layouts.dashboard.company.company_edit')->with('company', $company);
        }
    }
    public function updateCompany(Request $request)
    {
        $request->validate([
            "date"         => "required",
            "company_name" => "required|string",
            "id"           => "required|string",
        ]);
        // updating company
        $company = User::find(Crypt::decryptString($request->id))->first();
        if (is_null($company)) {
            return redirect()->back()->with('error', 'Company not found');
        } else {
            $company->created_at = \App\Helpers\GeneralFunctions::convertToDateTime($request->date);
            $company->tenants->where('Id', $company->TenantId)->update(['TenantName' => $request->company_name]);
            $company->save();
            return redirect()->route('company.list')->with('success', 'updated successfully');
        }
    }
    public function changeCompanyState(Request $request)
    {
        //TODO Change state
        //TODO inactive with reason
        //TODO send email on inactive
        $rules = [
            "id"    => "required|string",
            "state" => "required|digits_between:0,1",
        ];
        if ($request->state == 0) {
            $rules["reason"] = "required|string";
        }
        $this->validate($request, $rules, [
            "id.required"          => "Could not find company",
            "id.string"            => "Could not find company",
            "state.required"       => "State is missing",
            "state.digits_between" => "state must be number",
            "reason.required"      => "Please Add reason",
            "reason.string"        => "reason must be a string",
        ]);
        $company = User::find($request->id);
        // if company found
        if (!is_null($company)) {
            $company->AccountStatus = (int) $request->state;
            $company->save();
            // sending email to company
            if ($company->AccountStatus == 0) {
                $data = [
                    'subject'         => 'Account Deactivated',
                    'heading_details' => '',
                    'sub_heading'     => 'Your account has been deactivated by admin',
                    'heading'         => 'Betting Form',
                    'title'           => 'Reason',
                    'content'         => $company->reject_reason,
                    'email'           => $company->EmailAddress,
                ];
                GeneralFunctions::sendEmail($data);
            }
            return redirect()->route('company.list')->with('success', 'Company state is changed');
        } else {
            // company not found
            return redirect()->route('company.list')->with('error', 'Company not found');
        }
    }

    /**
     *
     * Tenant Company Details Section
     *
     */
    public function companyDetails($id)
    {
        $data['company'] = User::with('company_name', 'partners_list.staff_members', 'currency')->where(['id' => Crypt::decryptString($id)], ['Roles' => 2])->first();
        if (empty($data['company']->toArray())) {
            return redirect()->back()->with('error', 'Company not found');
        } else {
            $data['internal_staff'] = User::with('staff_members.user_roles', 'partner_settings.currency')->where('Roles', $data['company']->Roles)->where('TenantId', $data['company']->TenantId)->where('IsAdmin', 0)->get();
            $data['internal_staff'] = $data['internal_staff']->toArray();
            return view('super_admin_portal.layouts.company_details.company_profile')->with($data);
        }
    }

    /**
     *
     * Tenant Company Transaction Details
     *
     */
    public function companyTransactionDetails(Request $req)
    {
        // Get User Information
        $req->id        = Crypt::decryptString($req->id);
        $companyDetails = User::where('id', $req->id)->first();
        $record         = TenantAccountDetails::with('user')->with('currency')->where('UserId', $req->id)->get();
        $record         = $record->toArray();
        $responseResult = [];
        // 1) Loop through whole transaction and create result to be shown on the list
        // 2) Base Currency of Tenant Account
        $getTenantBaseCurrency = Currency::where('Tenant_Id', $companyDetails->TenantId)->where('isBaseCurrency', 1)->first();
        $totalBalance          = 0;
        foreach ($record as $key => $value) {
            $responseResult[$key]['id'] = $value['id'];
            if ($value['AccountStatus'] == 1) {
                $source_id                           = explode('-', $value['TransactionID']);
                $responseResult[$key]['source']      = User::where('id', $source_id[1])->first();
                $responseResult[$key]['source']      = $responseResult[$key]['source']->Username;
                $responseResult[$key]['transaction'] = 'Deposit Ammount from Account (' . $responseResult[$key]['source'] . ')';
            } elseif ($value['AccountStatus'] == 2) {
                $source_id                           = explode('-', $value['TransactionID']);
                $responseResult[$key]['source']      = User::where('id', $source_id[1])->first();
                $responseResult[$key]['source']      = $responseResult[$key]['source']->Username;
                $responseResult[$key]['transaction'] = 'Transfer Ammount to Account (' . $responseResult[$key]['source'] . ')';
            } else {
                /**
                TODO:
                - Get Records from Site Account
                 */
                $source_id                           = explode('-', $value['TransactionID']);
                $responseResult[$key]['source']      = SitesAccount::where('id', $source_id[1])->first();
                $responseResult[$key]['source']      = 'Site Account Code (' . $responseResult[$key]['source']->SiteAccountCode . ')';
                $responseResult[$key]['transaction'] = 'Profit Loss From Account Number Code (' . $responseResult[$key]['source'] . ')';
            }
            $responseResult[$key]['no']                             = $key + 1;
            $responseResult[$key]['in_currency']                    = $value['currency']['CurrencyName'];
            $responseResult[$key]['amount_in_different_currencies'] = $value['Amount'];
            $convertedAmountToBaseCurrency                          = $value['Amount'] / $value['Current_Rate'];
            $responseResult[$key]['amount_in_base_currency']        = $value['Amount'] / $value['Current_Rate'] * $getTenantBaseCurrency->CurrentRate;
            $totalBalance                                           = $totalBalance + $responseResult[$key]['amount_in_base_currency'];
            $responseResult[$key]['created_at']                     = $value['created_at'];
            $responseResult[$key]['transaction_id']                 = $value['TransactionID'];
            $responseResult[$key]['remarks']                        = $value['Remarks'];
            $responseResult[$key]['reference_transaction_id']       = $value['reference_transaction_id'];
        }
        $data['responseResult'] = $responseResult;
        $data['totalBalance']   = $totalBalance;
        $data['company']        = User::with('company_name', 'partners_list.staff_members', 'currency')->where(['id' => $req->id], ['Roles' => 2])->first();
        // dd($data);
        return view('super_admin_portal.layouts.company_details.company_profile_transactions', $data);
    }

    /**
     *
     * Block for Companies Reporting
     *
     */
    public function companies_reporting(Request $req)
    {
        // 1) Free Active Account and Paid Active Accounts Detail

        $data['free_active_accounts'] = User::with('company_name')->where('Roles', 2)->where('isAdmin', 1)->where('AccountStatus', 1)->where('account_type', 1)->get()->toArray();

        $data['paid_active_accounts'] = User::with('company_name')->where('Roles', 2)->where('isAdmin', 1)->where('AccountStatus', 1)->where('account_type', 2)->get()->toArray();

        $data['rejected_accounts'] = RejectedTenantAccounts::get()->toArray();

        return view('super_admin_portal.layouts.company_details.company_tracking_process_screen', $data);

    }

}
