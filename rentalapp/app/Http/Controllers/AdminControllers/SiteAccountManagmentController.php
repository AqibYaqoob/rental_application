<?php

namespace App\Http\Controllers\AdminControllers;

use App\Category;
use App\Currency;
use App\Http\Controllers\Controller;
use App\PartnerAssignment;
use App\SiteAccountShareholder;
use App\Sites;
use App\SitesAccount;
use App\User;
use Auth;
use GeneralFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;
use Validator;
use View;

class SiteAccountManagmentController extends Controller
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
     * Add Site Screen Display Function
     *
     */

    public function site_account_screen(Request $req)
    {
        $user          = Auth::user();
        $data['title'] = 'Add Site Accounts';
        $data['sites'] = Sites::where('tenant_id', $user->TenantId)->get();
        $category      = Category::where('tenant_id', $user->TenantId)->get();
        if ($category->count() > 0) {
            $data['category'] = $category;
        } else {
            $data['category'] = [];
        }
        $data['currencies'] = Currency::with('currencyList')->where('Tenant_Id', $user->TenantId)->get();
        $data['sites']      = $data['sites']->toArray();
        $data['currencies'] = $data['currencies']->toArray();
        $partners     = User::where([['Roles', 3], ['TenantId', $user->TenantId]])->get();
        $data['partners'] = $partners ?? [];
        if ($req && $req->id != '') {
            // Decrypt Url Parameter
            $requestId = Crypt::decryptString($req->id);
            $getRecord = SitesAccount::where('Id', $requestId)->first();
            if ($getRecord) {
                $data['site_details'] = $getRecord->toArray();
                // fetch partner assignment
                $assignment = PartnerAssignment::where([["SiteAccountId", $getRecord->Id], ['EndDate', null]])->first();
                $data['assignments'] = $assignment;
                // fetch shareholder as wel
                $shareHolder = SiteAccountShareholder::where([["SiteAccountId", $getRecord->Id], ["EndDate", null]])->get();
                $data["shareholders"] = $shareHolder ?? collect([]);
            }
        }
        return view('company_portal.layouts.site_account.site_account_form', $data);
    }

    /**
     *
     * Add Site Validations Function
     *
     */

    public function site_account_validation(Request $req)
    {
        // 1) Validation
        $validationArray = [
            'site_name'               => 'required',
            'currency'                => 'required',
            'site_account_code'       => 'required',
            'total_ternover_percent'  => 'required|integer|min:0',
            'max_single_bet'          => 'required|numeric|min:0',
            'credit'                  => 'required|numeric|min:1',
            'per_bet'                 => 'required|numeric|min:1',
            'site_account_label_code' => 'max:7',
            'remarks_label_color'     => 'max:7',
            'category'                => 'required|integer',
        ];
        $rules = [
            'site_name.required'              => 'Site Name is required',
            'currency.required'               => 'Currency is required',
            'site_account_code.required'      => 'Site Account Code is required',
            'total_ternover_percent.required' => 'Total Turn Over Percentage is required',
            'total_ternover_percent.integer'  => 'Total Turn Over Percentage should be numeric value',
            'total_ternover_percent.min'      => 'Total Turn Over Percentage should be positive number',
            'max_single_bet.required'         => 'Max Single Bet is required',
            'max_single_bet.numeric'          => 'Max Single Bet should be numeric value',
            'max_single_bet.min'              => 'Max Single Bet should be positive number',
            'credit.required'                 => 'Credit is required',
            'credit.numeric'                  => 'Credit value should be numeric',
            'credit.min'                      => 'Credit minimum value should be 1',
            'per_bet.required'                 => 'Per bet value is required',
            'per_bet.numeric'                  => 'Per bet value should be numeric',
            'per_bet.min'                      => 'Per bet  minimum value should be 1',
            'site_account_label_code.max'     => 'Site Account Label Color characters should not be more than 7',
            'remarks_label_color.max'         => 'Remarks Label Color characters should not be more than 7',
            'category.required'               => 'Please select a category',
            'category.integer'                => 'category value must be an integer',
        ];
        if(array_key_exists('assignTo', $req->all()))
        {
            $validationArray["partner"] = "required|integer";
            $rules['partner.required'] = "select a partner to assign account";
            $rules['partner.integer'] = "Assigned partner value is not valid";
        }
        $loopErrors = [];
        if(array_key_exists('shareHolder', $req->all()))
        {
            $shareHolderRuels = [
                "partner" => "required|integer",
                "winPercent" => "required|numeric",
                "losePercent" => "required|numeric",
                "TotalCommissionPercent" => "required|numeric",
                "TotalTurnoverPercentForCommission" => "required|numeric",
            ];
            $rules["partner.required"] = "Please select partner in share holder section";
            $rules["partner.integer"] = "Partner value is not valid in share holder section";
            $rules["winPercent.required"] = "Please add winning percentage in share holder section";
            $rules["winPercent.numeric"] = "Winning percent value is not valid in share holder section";
            $rules["losePercent.required"] = "Please add Lose Percentage in share holder section";
            $rules["losePercent.numeric"] = "Lose percent value is not valid in share holder section";
            $rules["TotalCommissionPercent.required"] = "Please add total commission percentage in share holder section";
            $rules["TotalCommissionPercent.numeric"] = "Total commission percentage value is not valid in share holder section";
            $rules["TotalTurnoverPercentForCommission.required"] = "Please add total turnover percentage in share holder section";
            $rules["TotalTurnoverPercentForCommission.numeric"] = "Total turnover percentage value is not valid in share holder section";
            foreach ($req->lst as $list)
            {
                $validator = Validator::make($list, $shareHolderRuels, $rules);
                $loopErrors["messages"][] = $validator->errors();
            }
        }
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (isset($loopErrors["messages"]))
        {
            foreach ($loopErrors["messages"] as $loopError)
            {
                array_push($errors, GeneralFunctions::error_msg_serialize($loopError));
            }
        }
        if (isset($loopErrors["messages"]))
        {
            // convert nested array into single array
            $errors = array_collapse($errors);
            // remove duplicate value from array
            $errors = array_unique($errors);
            // Check if the Code already Exist
        }
        if ($req && $req->id != '') {
            $checkExistance = SitesAccount::where('SiteAccountCode', $req->site_account_code)->where('id', '!=', Crypt::decryptString($req->id))->first();
        } else {
            $checkExistance = SitesAccount::where('SiteAccountCode', $req->site_account_code)->first();
        }
        if ($checkExistance) {
            array_push($errors, 'Site Account Code Already Mapped. For more details please check with Administration');
        }
        if (count($errors) > 0) {
            return response()->json(['status' => 'error', 'msg_data' => $errors]);
        }
        return response()->json(['status' => 'success']);
    }

    /**
     *
     * Add Site Record Function
     *
     */

    public function add_site_account_record(Request $req)
    {
        try
        {
            $user = Auth::user();
            $now = \Carbon\Carbon::now();
            $errors = [];
            // 1) Convert Total turnover into Decimal number for saving percentage as 2 decimal places in database.
            $total_ternover_percent = $req->total_ternover_percent / 100;
            $record = ['SiteId' => $req->input('site_name'), 'TenantId' => $user->TenantId, 'user_id' => GeneralFunctions::adminUserId(), 'CurrencyId' => $req->input('currency'), 'SiteAccountCode' => $req->input('site_account_code'), 'SiteAccountLabelColor' => $req->input('site_account_label_code'), 'TotalTurnoverPercent' => $total_ternover_percent, 'MaxSingleBet' => $req->input('max_single_bet'), 'credit'=> $req->credit, 'per_bet' => $req->per_bet, 'Remarks' => $req->input('remarks'), 'RemarksLabelColor' => $req->input('remarks_label_color'), 'category_id' => $req->category];
            $checkExistance = SitesAccount::where('SiteId', $req->input('site_name'))->where('TenantId', $user->TenantId)->where('CurrencyId', $req->input('currency'))->where('SiteAccountCode', $req->input('site_account_code'))->where('user_id', $user->id)->get();
            if ($req && $req->id != '')
            {
                // Decrypt Url Parameter
                $requestId = Crypt::decryptString($req->id);
                $checkExistance = SitesAccount::where('SiteId', $req->input('site_name'))->where('TenantId', $user->TenantId)->where('CurrencyId', $req->input('currency'))->where('SiteAccountCode', $req->input('site_account_code'))->where('user_id', Auth::user()->id)->where('Id', '!=', $requestId)->get();
            }
            if (count($checkExistance->toArray()) > 0)
            {
                return back()->with('error_msg', 'Record Already Exist.');
            }
            if ($req && $req->id != '')
            {
                $requestId = Crypt::decryptString($req->id);
                $record['LastUpdateBy'] = $user->Username;
                $record['LastUpdateIPAddress'] = GeneralFunctions::getRealIpAddr();
                $checkExistance = SitesAccount::where('Id', $requestId)->update($record);
                // update partner
                if (array_key_exists("assignTo", $req->all()) && array_key_exists("assignId", $req->all()))
                {
                    $assigned = PartnerAssignment::find(Crypt::decryptString($req->assignId));
                    $assigned->PartnerId = $req->partner;
                    $assigned->AssignedBy = $user->Username;
                    $assigned->updated_at = $now;
                    $assigned->save();
                }
                elseif (array_key_exists("assignId", $req->all()))
                {
                    $assigned = PartnerAssignment::find(Crypt::decryptString($req->assignId));
                    if (!is_null($assigned))
                    {
                        $assigned->delete();
                    }
                }
                elseif(array_key_exists("assignTo", $req->all()))
                {
                    $partnerAssignment = new PartnerAssignment();
                    $partnerAssignment->PartnerId = $req->partner;
                    $partnerAssignment->SiteAccountId = $requestId;
                    $partnerAssignment->StartDate = $now;
                    $partnerAssignment->AssignedBy = $user->Username;
                    $partnerAssignment->Tenant_Id = $user->TenantId;
                    $partnerAssignment->save();
                }
                // update shareholder
                // fetch all existing shareholder from database
                // then update existing
                // and add new shareholders if he added
                // at the end check if user remove any of them
                $shareHolder = SiteAccountShareholder::where([["SiteAccountId", Crypt::decryptString($req->id)], ["EndDate", null]])->get();
                $shareHolderIds = $shareHolder->pluck("Id")->toArray();
                foreach ($req->lst as $item)
                {
                    // only allow if shareholder is exist or user want to remove shareholder using edit site account
                    if (array_key_exists("shareHolder", $req->all()) || array_key_exists("shareId", $item))
                    {
                        if (array_key_exists("shareHolder", $req->all()) && array_key_exists("shareId", $item))
                        {
                            $shared = SiteAccountShareholder::find(Crypt::decryptString($item["shareId"]));
                        }
                        elseif (array_key_exists("shareId", $item))
                        {
                            $shared = SiteAccountShareholder::find(Crypt::decryptString($item["shareId"]));
                            if (!is_null($shared))
                            {
                                $shared->delete();
                                continue;
                            }
                        }
                        elseif (array_key_exists("shareHolder", $req->all()))
                        {
                            $shared = new SiteAccountShareholder();
                            $shared->SiteAccountId = $requestId;
                            $shared->StartDate = $now;
                            $shared->created_at = $now;
                            $shared->CreatedBy = $user->Username;
                            $shared->Tenant_Id = $user->TenantId;
                        }
                        $shared->WinPercent = round($item["winPercent"] / 100, 2);
                        $shared->LosePercent = round($item["losePercent"] / 100, 2);
                        $shared->PartnerId = $item["partner"];
                        $shared->TotalCommissionPercent = round($item["TotalCommissionPercent"] / 100, 2);
                        $shared->TotalTurnoverPercentForCommission = round($item["TotalTurnoverPercentForCommission"] / 100, 2);
                        $shared->save();
                        // removing ids of updated shareholders from array so at the end i can find which shareholder has been removed by user
                        $key = array_search($shared->Id, $shareHolderIds, true);
                        if ($key !== false)
                        {
                            array_pull($shareHolderIds, $key);
                        }
                    }
                }
                // removing shareholder if some one is removed by user
                if (!empty($shareHolderIds))
                    SiteAccountShareholder::whereIn("Id", $shareHolderIds)->update(["EndDate" => \Carbon\Carbon::now()]);
                return redirect('admin/company/site/account/list')->with('status', 'Record has been updated successfully');
            }
            $record['CreatedBy'] = $user->Username;
            $record['CreatedIPAddress'] = GeneralFunctions::getRealIpAddr();
            $newlyAccount = SitesAccount::create($record);
            // check site account assignment to partner
            if (array_key_exists("assignTo", $req->all()) && $req->assignTo == 1) {
                // assign site account to partner
                $alreadyAssigned = PartnerAssignment::where('SiteAccountId', $newlyAccount->Id)->where('PartnerId', $req->partner)->where('EndDate', null)->get()->toArray();
                if (count($alreadyAssigned) == 0) {
                    $partnerAssignment = new PartnerAssignment();
                    $partnerAssignment->PartnerId = $req->partner;
                    $partnerAssignment->SiteAccountId = $newlyAccount->Id;
                    $partnerAssignment->StartDate = $now;
                    $partnerAssignment->AssignedBy = $user->Username;
                    $partnerAssignment->Tenant_Id = $user->TenantId;
                    $partnerAssignment->save();
                }
            }
            // check shareHolder for site account
            if (array_key_exists("shareHolder", $req->all()) && $req->shareHolder == 1) {
                // loop through the shareHolder array
                $bulkInsertion = [];
                foreach ($req->lst as $item) {
                    // Check if Same Site Account is Already given to same Partner
                    $alreadyShared = SiteAccountShareholder::where('SiteAccountId', $newlyAccount->Id)->where('PartnerId', $item["partner"])->where('EndDate', null)->get()->toArray();
                    if (count($alreadyShared) == 0) {
                        $bulkInsertion[] = [
                            "SiteAccountId" => $newlyAccount->Id,
                            "PartnerId" => $item["partner"],
                            "WinPercent" => round($item["winPercent"] / 100, 2),
                            "LosePercent" => round($item["losePercent"] / 100, 2),
                            "TotalCommissionPercent" => round($item["TotalCommissionPercent"] / 100, 2),
                            "TotalTurnoverPercentForCommission" => round($item["TotalTurnoverPercentForCommission"] / 100, 2),
                            "StartDate" => $now->toDateTimeString(),
                            "created_at" => $now->toDateTimeString(),
                            "updated_at" => $now->toDateTimeString(),
                            "CreatedBy" => $user->Username,
                            "Tenant_Id" => $user->TenantId
                        ];
                    }
                }
                // this condition could be optimize
                if (collect(array_count_values(array_pluck($bulkInsertion, "PartnerId")))->max() > 1) {
                    // user is sharing current site account to same partner multiple times
                    $errors[] = "Sharing current site account to same partner multiple times";
                } else {
                    // good to go
                    SiteAccountShareholder::insert($bulkInsertion);
                }
            }
            // check if errors occurs
            if (empty($errors)) {
                return redirect('admin/company/site/account/list')->with('status', 'Record has been saved successfully');
            } else {
                return back()->with('errors_array', $errors);
            }
        }
        catch (\Exception $exception)
        {
            Log::error('SITE ACCOUNT   '.$exception->getMessage(). ':    '.$exception->getLine());
            return redirect()->back()->with('msg_error', 'An error occurred');
        }
    }

    /**
     *
     * Show Site List Function
     *
     */

    public function site_account_list(Request $req)
    {
        $data['title']  = 'Site List';
        $data['record'] = SitesAccount::with('sites', 'tenants', 'currency.currencyList')->where('TenantId', Auth::user()->TenantId)->get();
        $data['record'] = $data['record']->toArray();
        return view('company_portal.layouts.site_account.site_account_list', $data);
    }

    /**
     *
     * Delete Site Account Function
     *
     */

    public function delete_site_account(Request $req)
    {
        try
        {
            $deleteRecord = SitesAccount::with("partnerAssignment", "siteAccountShareholder")->find($req->input('record_uuid'));
            if(!is_null($deleteRecord))
            {
                if($deleteRecord->partnerAssignment->count() > 0 || $deleteRecord->siteAccountShareholder->count() > 0)
                {
                    return redirect()->back()->with("error_msg", "Site account cannot be delete because some of its information is still being used");
                }
                else
                {
                    $deleteRecord->delete();
                    return back()->with('status', 'Record has been deleted successfully.');
                }
            }
        }
        catch (\Exception $e)
        {
            Log::error("DELETE SITE ACCOUNT ".$e->getMessage()." LINE: ".$e->getLine());
            return back()->with('error_msg', 'Record is referenced in other record');
        }
    }
    public function partnerAccountForm()
    {
        $tenantId     = \Illuminate\Support\Facades\Auth::user()->TenantId;
        $siteAccounts = SitesAccount::where('TenantId', $tenantId)->get();
        $partners     = User::where([['Roles', 3], ['TenantId', $tenantId]])->get();
        return view('company_portal.assign_site_account.partner.assign_account')->with(['siteAccounts' => $siteAccounts, 'partners' => $partners]);
    }
    public function assignAccount(Request $request)
    {
        $rules = [
            "siteAccount" => "required|integer",
            "partner"     => "required|integer",
        ];
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }
        $user = \Illuminate\Support\Facades\Auth::user();
        $now  = Carbon::now();
        /**
         *
         * Check if Same Site Account is Already given to same Partner
         *
         */
        $partnerAlreadyHaveThisSite = PartnerAssignment::where('SiteAccountId', $request->siteAccount)->where('PartnerId', $request->partner)->where('EndDate', null)->get()->toArray();
        if (count($partnerAlreadyHaveThisSite) > 0) {
            return back()->with('error_msg', 'Partner Already assigned to this Site Account');
        }
        $partner = PartnerAssignment::where([['SiteAccountId', $request->siteAccount], ['EndDate', null]])->first();
        if (!is_null($partner)) {
            // already assigned
            $partner->EndDate      = $now;
            $partner->LastUpdateBy = $user->Username;
            $partner->save();
        }
        $partnerAssignment = new PartnerAssignment();
        $partnerAssignment->PartnerId  = $request->partner;
        $partnerAssignment->SiteAccountId = $request->siteAccount;
        $partnerAssignment->StartDate     = $now;
        $partnerAssignment->AssignedBy    = $user->id;
        $partnerAssignment->Tenant_Id     = Auth::user()->TenantId;
        $partnerAssignment->save();
        return redirect()->route('partner.account.list')->with('success', 'Site Account has been assigned');
    }
    public function partnerAccountList(Request $request)
    {
        $partnerAccounts = PartnerAssignment::whereHas('user', function ($query) {
            $query->where('Roles', 3);
            $query->where('TenantId', Auth::user()->TenantId);
        })->where("SiteAccountId", Crypt::decryptString($request->id))->get();
        return view('company_portal.assign_site_account.partner.account_list')->with('partnerAccounts', $partnerAccounts);
    }
    public function editPartnerAccount($id)
    {
        $partnerAccount = PartnerAssignment::find(Crypt::decryptString($id));
        if (!is_null($partnerAccount)) {
            $tenantId     = \Illuminate\Support\Facades\Auth::user()->TenantId;
            $siteAccounts = SitesAccount::where('Id', $partnerAccount->SiteAccountId)->get();
            $partners     = User::where([['Roles', 3], ['TenantId', $tenantId], ["AccountStatus", 1]])->get();
            return view('company_portal.assign_site_account.partner.edit_account')->with(['siteAccounts' => $siteAccounts, 'partners' => $partners, 'partnerAccount' => $partnerAccount]);
        } else {
            return redirect()->back()->with('error', 'Could not find partner assigned account');
        }
    }
    public function updatePartner(Request $request)
    {
        $account = PartnerAssignment::find(Crypt::decryptString($request->id));
        if (!is_null($account)) {
            $now = Carbon::now();
            $user = Auth::user();
            // check if assigning to same partner again
            if($account->PartnerId == $request->partner)
            {
                return redirect()->back()->with('error', 'Already assigned to this partner');
            }
            else
            {
                $account->EndDate = $now;
                $account->LastUpdatedBy = $user->Username;
                $account->save();
                // assign to new partner
                $newAssignment = new PartnerAssignment();
                $newAssignment->PartnerId     = $request->partner;
                $newAssignment->SiteAccountId = $request->siteAccount;
                $newAssignment->StartDate = $now;
                $newAssignment->AssignedBy = $user->Username;
                $newAssignment->Tenant_id = $user->TenantId;
                $newAssignment->LastUpdatedBy = null;
                $newAssignment->save();
                return redirect()->route('partner.account.list', ['id' => Crypt::encryptString($newAssignment->SiteAccountId)])->with('success', 'Partner Assignment has been updated');
            }
        }
        else
        {
            return redirect()->back()->with('error', 'could not find any Partner assignment');
        }
    }
    public function partnerAccountDeletion(Request $request)
    {
        $this->validate($request, ["id" => "required|string"], ["id.required" => 'Could not find partner assigned account']);
        $id             = Crypt::decryptString($request->id);
        $partnerAccount = PartnerAssignment::find($id);
        if (!is_null($partnerAccount))
        {
            try
            {
                $partnerAccount->delete();
            }
            catch (Exception $e)
            {
                return redirect()->back()->with('error', 'Can not delete partner account');
            }
            return redirect()->back()->with('success', 'Partner assigned account has been deleted');
        }
        else
        {
            return redirect()->back()->with('error', 'Could not find partner assigned account');
        }
    }
    public function shareHolderAccountForm()
    {
        $tenantId     = \Illuminate\Support\Facades\Auth::user()->TenantId;
        $siteAccounts = SitesAccount::where('TenantId', $tenantId)->get();
        $partners     = User::where([['Roles', 3], ['TenantId', $tenantId]])->get();
        return view('company_portal.assign_site_account.shareholder.assign_account_shareholder')->with(['siteAccounts' => $siteAccounts, 'partners' => $partners]);
    }
    public function shareHolderAssignment(Request $request)
    {
        $rules = [
            "siteAccount"                       => "required|integer",
            "partner"                           => "required|integer",
            "winPercent"                        => "required|numeric",
            "losePercent"                       => "required|numeric",
            "TotalCommissionPercent"            => "required|numeric",
            "TotalTurnoverPercentForCommission" => "required|numeric",
        ];
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }
        /**
         *
         * Check if Same Site Account is Already given to same Partner
         *
         */
        $partnerAlreadyHaveThisSite = SiteAccountShareholder::where('SiteAccountId', $request->siteAccount)->where('PartnerId', $request->partner)->where('EndDate', null)->get()->toArray();
        if (count($partnerAlreadyHaveThisSite) > 0) {
            return redirect()->back()->with('error', 'Partner Already assigned to this Site Account');
        }

        $user                                                  = \Illuminate\Support\Facades\Auth::user();
        $now                                                   = \Carbon\Carbon::now();
        $accountShareholder                                    = new SiteAccountShareholder();
        $accountShareholder->SiteAccountId                     = $request->siteAccount;
        $accountShareholder->PartnerId                         = $request->partner;
        $accountShareholder->WinPercent                        = round($request->winPercent / 100, 2);
        $accountShareholder->LosePercent                       = round($request->losePercent / 100, 2);
        $accountShareholder->TotalCommissionPercent            = round($request->TotalCommissionPercent / 100, 2);
        $accountShareholder->TotalTurnoverPercentForCommission = round($request->TotalTurnoverPercentForCommission / 100, 2);
        $accountShareholder->StartDate                         = $now;
        $accountShareholder->created_at                        = $now;
        $accountShareholder->updated_at                        = $now;
        $accountShareholder->CreatedBy                         = $user->Username;
        $accountShareholder->Tenant_Id                         = Auth::user()->TenantId;
        $accountShareholder->save();

        return redirect()->route('shareHolder.account.list')->with('success', 'Site Account has been assigned');
    }
    public function shareHolderAccountList(Request $request)
    {
        if (Auth::user()->Roles == 3) {
            $shareHolderAccounts = SiteAccountShareholder::where('Tenant_Id', Auth::user()->TenantId)->where('PartnerId', Auth::user()->id)->where("SiteAccountId", Crypt::decryptString($request->id))->get();
        } else {
            $shareHolderAccounts = SiteAccountShareholder::where('Tenant_Id', Auth::user()->TenantId)->where("SiteAccountId", Crypt::decryptString($request->id))->get();
        }

        return view('company_portal.assign_site_account.shareholder.account_list')->with('accounts', $shareHolderAccounts);
    }
    public function editShareHolderAccount($id)
    {
        $shareHolder = SiteAccountShareholder::find(Crypt::decryptString($id));
        if (!is_null($shareHolder)) {
            $tenantId     = \Illuminate\Support\Facades\Auth::user()->TenantId;
            $siteAccounts = SitesAccount::where('TenantId', $tenantId)->get();
            $partners     = User::where([['Roles', 3], ['TenantId', $tenantId]])->get();
            return view('company_portal.assign_site_account.shareholder.edit_shareholder')->with(['siteAccounts' => $siteAccounts, 'partners' => $partners, 'shareholder' => $shareHolder]);
        } else {
            return redirect()->back()->with('error', 'Could not find share Holder account');
        }
    }
    public function updateShareHolderAccount(Request $request)
    {
        $rules = [
            "siteAccount"                       => "required|integer",
            "partner"                           => "required|integer",
            "winPercent"                        => "required|numeric",
            "losePercent"                       => "required|numeric",
            "TotalCommissionPercent"            => "required|numeric",
            "TotalTurnoverPercentForCommission" => "required|numeric",
        ];
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }
        $id      = Crypt::decryptString($request->id);
        $account = SiteAccountShareholder::find($id);
        if (!is_null($account)) {
            //update model
            $account->SiteAccountId                     = $request->siteAccount;
            $account->PartnerId                         = $request->partner;
            $account->WinPercent                        = round($request->winPercent / 100, 2);
            $account->LosePercent                       = round($request->losePercent / 100, 2);
            $account->TotalCommissionPercent            = round($request->TotalCommissionPercent / 100, 2);
            $account->TotalTurnoverPercentForCommission = round($request->TotalTurnoverPercentForCommission / 100, 2);
            $account->CreatedBy                         = \Illuminate\Support\Facades\Auth::user()->Username;
            $account->save();
            return redirect()->route('shareHolder.account.list')->with('success', 'Share Holder site account has been updated');
        } else {
            return redirect()->back()->with('error', 'Could not find share holder account');
        }
    }
    public function shareHolderAccountDeletion(Request $request)
    {
        $account = SiteAccountShareholder::find(Crypt::decryptString($request->id));
        if (!is_null($account))
        {
            try
            {
                $account->delete();
            }
            catch (\Exception $e)
            {
                return redirect()->back()->with('error', 'Can not delete shareholder');
            }
            return redirect()->back()->with('success', 'Shareholder has been deleted');
        }
        else
        {
            return redirect()->back()->with('error', 'Could not find shareholder');
        }
    }

    /**
     *
     * Close account of Site Account from Specific Partner
     *
     */

    public function shareHolderAccountClosed(Request $req)
    {
        $now         = Carbon::now();
        $user        = Auth::user();
        $shareHolder = SiteAccountShareholder::where([['Id', Crypt::decryptString($req->id)], ['EndDate', null]])->first();
        if (!is_null($shareHolder)) {
            // already assigned
            $shareHolder->EndDate      = $now;
            $shareHolder->LastUpdateBy = $user->Username;
            $shareHolder->save();
            return redirect()->back()->with('success', 'Shareholder account has been closed');
        }
        else
        {
            return redirect()->back()->with('error', 'Cannot find shareholder');
        }
    }

    //*************************************SITE MANAGEMENT FROM ADMIN PORTION*******************************************
    public function site_screen(Request $req)
    {
        $data['title'] = 'Add Site';
        if ($req && $req->id != '') {
            // Decrypt Url Parameter
            $requestId = Crypt::decryptString($req->id);
            $getRecord = Sites::where('id', $requestId)->first();
            if ($getRecord) {
                $data['site_details'] = $getRecord->toArray();
            }
        }
        return view('company_portal.layouts.sites.add_site', $data);
    }

    /**
     *
     * Add Site Validations Function
     *
     */

    public function site_validation(Request $req)
    {
        // 1) Validation
        $validationArray = [
            'site_name' => 'required|max:255',
        ];
        $rules = [
            'site_name.required' => 'Site Name is required',
            'site_name.max'      => 'Site Name should not be more than 255 characters',
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => 'error', 'msg_data' => $errors]);
        }
        return response()->json(['status' => 'success']);
    }

    /**
     *
     * Add Site Record Function
     *
     */

    public function add_site_record(Request $req)
    {
        // 1) Check If Domain Already Exist
        $record         = ['SiteName' => $req->input('site_name'), 'Remarks' => $req->input('remarks')];
        $checkExistance = Sites::where('SiteName', $req->input('site_name'))->get();
        if ($req && $req->id != '') {
            // Decrypt Url Parameter
            $requestId      = Crypt::decryptString($req->id);
            $checkExistance = Sites::where('SiteName', $req->input('site_name'))->where('Id', '!=', $requestId)->get();
        }
        if (count($checkExistance->toArray()) > 0) {
            return back()->with('error_msg', 'Record Already Exist.');
        }
        if ($req && $req->id != '') {
            $requestId    = Crypt::decryptString($req->id);
            $updateRecord = Sites::find($requestId)->update($record);
            return back()->with('status', 'Record has been updated successfully');
        }
        $record['tenant_id'] = Auth::user()->TenantId;
        Sites::create($record);
        return back()->with('status', 'Record has been saved successfully');
    }
    /**
     *
     * Show Site List Function
     *
     */

    public function site_list(Request $req)
    {
        $data['title']  = 'Site List';
        $data['record'] = Sites::where('tenant_id', Auth::user()->TenantId)->get();
        $data['record']->toArray();
        return view('company_portal.layouts.sites.site_list', $data);
    }

    /**
     *
     * Delete Sites Function
     *
     */

    public function delete_site(Request $req)
    {
        try
        {
            $deleteRecord = Sites::with("sitesAccount")->find($req->input('record_uuid'));
            if(!is_null($deleteRecord))
            {
                if($deleteRecord->sitesAccount->count() > 0)
                {
                    return redirect()->back()->with("error_msg", "Site cannot be delete because some of its information is still being used");
                }
                else
                {
                    $deleteRecord->delete();
                }
            }
            return back()->with('status', 'Record has been deleted successfully.');
        }
        catch (\Exception $e)
        {
            return back()->with('error_msg', 'Record is referenced in other record');
        }
    }

}
