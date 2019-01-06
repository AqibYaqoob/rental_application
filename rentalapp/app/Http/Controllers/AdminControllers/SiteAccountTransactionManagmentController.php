<?php

namespace App\Http\Controllers\AdminControllers;

use App\Currency;
use App\Http\Controllers\Controller;
use App\PartnerAccountDetails;
use App\PartnerAssignment;
use App\PartnerProfitLoss;
use App\PartnerSetting;
use App\SiteAccountShareholder;
use App\Sites;
use App\SitesAccount;
use App\SitesAccountTransaction;
use App\TenantAccountDetails;
use App\TenantProfitLoss;
use App\User;
use Auth;
use Carbon\Carbon;
use GeneralFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Validator;
use View;

class SiteAccountTransactionManagmentController extends Controller
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

    public function site_account_transaction_screen(Request $req)
    {
        $data['title']         = 'Add Site Account Transaction (Daily Profit / Loss )';
        $data['sites']         = Sites::where('tenant_id', Auth::user()->TenantId)->get();
        $data['sites']         = $data['sites']->toArray();
        $data['sites_account'] = SitesAccount::where('user_id', GeneralFunctions::adminUserId())->get();
        $data['sites_account'] = $data['sites_account']->toArray();
        if ($req && $req->id != '') {
            // Decrypt Url Parameter
            $requestId = Crypt::decryptString($req->id);
            $getRecord = SitesAccountTransaction::with('site_account')->where('Id', $requestId)->first();
            if ($getRecord) {
                $data['site_account_tran_details'] = $getRecord->toArray();
            }
        }
        $partners = User::where([["TenantId", Auth::user()->TenantId], ["Roles", 3], ["AccountStatus", 1]])->get();
        $data["partners"] = $partners;
        return view('company_portal.layouts.site_account_transactions.site_account_transaction_form', $data);
    }

    /**
     *
     * Add Site Validations Function
     *
     */

    public function site_account_transaction_validation(Request $req)
    {
        $siteValidation = ['site_name' => 'required', 'site_account_code' => 'required'];
        $validationArray = [
            'transaction_date'  => 'required',
            'current_rate'      => 'required|numeric|min:0',
        ];
        $secValidation = $validationArray;
        foreach ($req->lst as $key => $list)
        {
            if(array_key_exists('data_entry_type', $list))
            {
                // mapping data entry
                if ($list['data_entry_type'] == 2)
                {
                    if(!array_key_exists($key, $secValidation))
                    {
                        $secValidation['column_name_total']       = 'required';
                        $secValidation['turn_over_number']        = 'required|min:1';
                        $secValidation['column_name_profit_loss'] = 'required';
                        $secValidation['profit_loss_number']      = 'required|min:1';
                        $secValidation['report_data']             = 'required';
                    }
                    else
                    {
                        continue;
                    }

                }
                else
                {
                    // manual data entry
                    if(!array_key_exists($key, $validationArray))
                    {
                        $validationArray['total_turn_over']   = 'required|numeric';
                        $validationArray['total_profit_loss'] = 'required|numeric';
                    }
                    else
                    {
                        continue;
                    }

                }
            }
            else
            {
                $siteValidation['data_entry_type'] = 'required|numeric';
            }
        }
        $rules = [
            'site_name.required'               => 'Site Name is required',
            'site_account_code.required'       => 'Site Account Code is required',
            'transaction_date.required'        => 'Transaction Date is required',
            'column_name_total.required'       => 'Total Turn Over Column Name Mapping is required',
            'turn_over_number.required'        => 'Total Turn Over Column Number Mapping is required',
            'turn_over_number.min'             => 'Total Turn Over Column Number Mapping should start with 1',
            'column_name_profit_loss.required' => 'Total Profit Loss Column Name Mapping is required',
            'profit_loss_number.required'      => 'Total Profit Loss Column Number Mapping is required',
            'profit_loss_number.min'           => 'Total Profit Loss Column Number Mapping should start with 1',
            'current_rate.required'            => 'Current Rate is required',
            'current_rate.numeric'             => 'Current Rate should be numeric value',
            'current_rate.min'                 => 'Current Rate should be positive number',
            'report_data.required'             => 'Report Data is required',
            'total_turn_over.required'         => 'Total turn Over field is required',
            'total_turn_over.numeric'          => 'Total turn Over should be numeric value',
            'total_profit_loss.required'       => 'Total Profit Loss field is required',
            'total_profit_loss.numeric'        => 'Total Profit Loss should be numeric value',
            'data_entry_type.required'         => 'Please select type of entry'
        ];
        $loopErrors = [];
        $customError = [];
        foreach ($req->lst as $lst)
        {
            if (array_key_exists('data_entry_type', $lst))
            {
                if ($lst['data_entry_type'] == 3)
                {
                    if(is_null($req->tab_partner))
                    {
                        $customError[] = "Please select partner for tabular data entry";
                        continue;
                    }
                    foreach ($req->t_over as $key => $value)
                    {
                        if(!is_null($value))
                        {
                            if (is_null($req->t_profit[$key]))
                            {
                                $customError[] = "If you add turnover of specific day then you must add its corresponding profit loss";
                            }
                        }
                        else
                        {
                            if(!is_null($req->t_profit[$key]))
                            {
                                $customError[] = "If you add profit loss of specific day then you must add its corresponding turnover";
                            }
                        }
                    }
                }
                else if ($lst['data_entry_type'] == 2)
                {
                    $validator = Validator::make($lst, $secValidation, $rules);
                    $loopErrors["messages"][] = $validator->errors();
                }
                else if ($lst['data_entry_type'] == 1)
                {
                    $validator = Validator::make($lst, $validationArray, $rules);
                    $loopErrors["messages"][] = $validator->errors();
                }
            }
        }
        if(is_null($req->tab_partner) && !is_null($req->site_name))
        {
            $validator = Validator::make($req->all(), $siteValidation, $rules);
            $errors = GeneralFunctions::error_msg_serialize($validator->errors());
        }
        else
        {
            $errors = [];
        }
        if (isset($loopErrors["messages"]))
        {
            foreach ($loopErrors["messages"] as $loopError)
            {
                array_push($errors, GeneralFunctions::error_msg_serialize($loopError));
            }
        }
        if(!empty($customError))
        {
            array_push($errors, $customError);
        }
        if (isset($loopErrors["messages"]) || !empty($customError))
        {
            // convert nested array into single array
            $errors = array_flatten($errors);
            // remove duplicate value from array
            $errors = array_unique($errors);
            //dd($errors);
        }
        if (count($errors) > 0) {
            return response()->json(['status' => 'error', 'msg_data' => array_values($errors)]);
        }
        return response()->json(['status' => 'success']);
    }

    /**
     *
     * Add Site Account Transaction Record Function
     *
     */

    public function add_site_account_transaction_record(Request $req)
    {
        $user = Auth::user();
        try
        {
            if ($req && $req->id != '')
            {
                // updating
                $siteAccountTx = SitesAccountTransaction::find(Crypt::decryptString($req->id));
                if(!is_null($siteAccountTx))
                {
                    if($req->lst[0]['data_entry_type'] == 1)
                    {
                        $siteAccountTx->SiteAccountId = $req->site_account_code;
                        $siteAccountTx->TransactionDate = Carbon::parse($req->lst[0]['transaction_date'])->toDateString();
                        $siteAccountTx->CurrentRate = $req->lst[0]['current_rate'];
                        $siteAccountTx->TotalTurnover = $req->lst[0]['total_turn_over'];
                        $siteAccountTx->TotalProfitLoss = $req->lst[0]['total_profit_loss'];
                        $siteAccountTx->updated_at = Carbon::now();
                        $siteAccountTx->LastUpdateBy = $user->Username;
                        $siteAccountTx->save();
                    }
                    elseif($req->lst[0]['data_entry_type'] == 2)
                    {
                        $siteAccountTx->SiteAccountId = $req->site_account_code;
                        $siteAccountTx->TransactionDate = Carbon::parse($req->lst[0]['transaction_date'])->toDateString();
                        $siteAccountTx->CurrentRate = $req->lst[0]['current_rate'];
                        $siteAccountTx->total_turnover_column = $req->lst[0]['column_name_total'];
                        $siteAccountTx->total_turn_over_column_number = $req->lst[0]['turn_over_number'];
                        $siteAccountTx->total_profit_loss_column = $req->lst[0]['column_name_profit_loss'];
                        $siteAccountTx->total_profit_loss_column_number = $req->lst[0]['profit_loss_number'];
                        $siteAccountTx->report_data = $req->lst[0]['report_data'];
                        $siteAccountTx->updated_at = Carbon::now();
                        $siteAccountTx->LastUpdateBy = $user->Username;
                        $siteAccountTx->save();
                    }
                    return redirect('admin/company/site/account/transaction/list')->with('success', 'Transactions has been updated');
                }
                else
                {
                    return redirect()->back()->with('error_msg', 'No Transaction found with this id');
                }
            }
            else
            {
                // insertion
                $bulkInsert = [];
                foreach ($req->lst as $list)
                {
                    if ($list['data_entry_type'] == 3 && !empty($req->t_over))
                    {
                        foreach ($req->t_over as $key => $value)
                        {
                            if (!is_null($value))
                            {
                                $bulkInsert[] = ['SiteAccountId' => $req->site_acc[$key], 'TransactionDate' => Carbon::createFromTimestamp($key), 'CurrentRate' => $req->rate[$req->site_acc[$key]], 'TotalTurnover' => $value,'total_turnover_column' => null, 'total_turn_over_column_number' => null, 'TotalProfitLoss' => $req->t_profit[$key], 'total_profit_loss_column' => null, 'total_profit_loss_column_number' => null, 'report_data' => null, 'TenantId' => $user->TenantId, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()];
                            }
                        }
                    }
                    elseif($list['data_entry_type'] == 2)
                    {
                        $bulkInsert[] = ['SiteAccountId' => $req->input('site_account_code'), 'TransactionDate' => GeneralFunctions::convertToDateTime($list['transaction_date']), 'CurrentRate' => $list['current_rate'], 'TotalTurnover' => 0.00,'total_turnover_column' => $list['column_name_total'], 'total_turn_over_column_number' => $list['turn_over_number'], 'TotalProfitLoss' => 0.00, 'total_profit_loss_column' => $list['column_name_profit_loss'], 'total_profit_loss_column_number' => $list['profit_loss_number'], 'report_data' => $list['report_data'], 'TenantId' => $user->TenantId, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()];
                    }
                    elseif ($list['data_entry_type'] == 1)
                    {
                        $bulkInsert[] = ['SiteAccountId' => $req->input('site_account_code'), 'TransactionDate' => GeneralFunctions::convertToDateTime($list['transaction_date']), 'CurrentRate' => $list['current_rate'], 'TotalTurnover' => $list['total_turn_over'], 'total_turnover_column' => null, 'total_turn_over_column_number' => null, 'TotalProfitLoss' => $list['total_profit_loss'], 'total_profit_loss_column' => null, 'total_profit_loss_column_number' => null, 'report_data' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),  'TenantId' => $user->TenantId];
                    }
                    else
                    {
                        return redirect()->back()->with('error_msg', 'Please Fill up Fields');
                    }
                }
                if(!empty($bulkInsert))
                {
                    DB::table('siteaccounttransactions')->insert($bulkInsert);
                    return redirect('admin/company/site/account/transaction/list')->with('success', 'Transactions has been added');
                }
                else
                    return redirect()->back()->with('error_msg', 'Nothing to Save.');
            }
        }
        catch (\Exception $exception)
        {
            Log::error("ADD SITE ACCOUNT TRANSACTION: ".$exception->getMessage()."  LINE: ".$exception->getLine());
            return redirect()->back()->with('error_msg', 'An error occurred');
        }
    }

    /**
     *
     * Show Site List Function
     *
     */

    public function site_account_transaction_list(Request $req)
    {
        $data['title']  = 'Site Account Transaction List';
        $data['record'] = SitesAccountTransaction::with('site_account')->where('TenantId', Auth::user()->TenantId)->get();
        $data['record'] = $data['record']->toArray();
        return view('company_portal.layouts.site_account_transactions.site_account_transaction_list', $data);
    }

    /**
     *
     * Delete Site Account Function
     *
     */

    public function delete_site_account_transaction(Request $req)
    {
        try
        {
            $deleteRecord = SitesAccountTransaction::where('seen', 0)->find($req->input('record_uuid'))->delete();
            return back()->with('status', 'Record has been deleted successfully.');
        }
        catch (\Exception $e)
        {
            return back()->with('error_msg', 'Record is referenced in other record');
        }
    }

    /**
     *
     * Get Site Account Code Drop Down Values on Basis of Site Name Function
     *
     */

    public function get_account_code(Request $req)
    {
        $selectedOption = null;
        if ($req->input('req_id') != null) {
            $requestId       = Crypt::decryptString($req->req_id);
            $dataSiteAccount = SitesAccountTransaction::with('site_account')->where('Id', $requestId)->first();
            if ($dataSiteAccount) {
                $selectedOption = $dataSiteAccount->site_account->Id;
            }
        }
        // GeneralFunctions::ajax_debug($selectedOption);
        $getAllSiteCodes = SitesAccount::where('SiteId', $req->input('site_name'))->where('user_id', GeneralFunctions::adminUserId())->get();
        $getAllSiteCodes = $getAllSiteCodes->toArray();
        $data            = '';
        if (count($getAllSiteCodes) > 0) {
            $data = $data . '<option value="">Select Option</option>';
            foreach ($getAllSiteCodes as $key => $value) {
                if ($selectedOption == $value['Id']) {
                    $data = $data . '<option value="' . $value['Id'] . '" selected >' . $value['SiteAccountCode'] . '</option>';
                } else {
                    $data = $data . '<option value="' . $value['Id'] . '">' . $value['SiteAccountCode'] . '</option>';
                }
            }
        } else {
            $data = $data . '<option value="">Select Option</option>';
        }
        return response()->json(['status' => 'success', 'result' => $data]);
    }
    public function profitSharing()
    {
        //$rate = Currency::where([['Tenant_Id', Auth::user()->TenantId], ['isBaseCurrency', 1]])->pluck('CurrentRate')->toArray();
        return view('company_portal.layouts.site_account_transactions.share_profit');//->with('rate', $rate[0]);
    }

    /**
     *
     * Get Current Rate of the Site Account
     *
     */

    public function get_current_rate(Request $req)
    {
        $currentRate    = null;
        $getCurrentRate = SitesAccount::with('currency')->where('Id', $req->input('id'))->first();
        if ($getCurrentRate)
        {
            $currentRate = $getCurrentRate->currency->CurrentRate;
        }
        // check current weak transaction entries
        $previousWeekStart = Carbon::now()->previous()->startOfWeek();
        $previousWeekEnd = Carbon::now()->previous()->endOfWeek();
        $siteAccountTransactions = SitesAccountTransaction::whereBetween("TransactionDate", [$previousWeekStart, $previousWeekEnd])->where('SiteAccountId', $req->id)->get();
        $dates = [];
        foreach ($siteAccountTransactions as $accountTransaction)
        {
            $parsedDate = Carbon::parse($accountTransaction->TransactionDate);
            // check transaction date belongs to this week
            if($parsedDate->greaterThanOrEqualTo($previousWeekStart) && $parsedDate->lessThanOrEqualTo($previousWeekEnd))
            {
                $dates[] = strtotime($accountTransaction->TransactionDate) * 1000;
            }
        }
        return response()->json(['status' => 'success', 'result' => $currentRate, 'tx_dates' => $dates, 'start_date' => $previousWeekStart->toDateString(), 'end_date' => $previousWeekEnd->toDateString()]);
    }
    public function getTabularData(Request $request)
    {
        $partnerId = Crypt::decryptString($request->id);
        $previousWeekStart = Carbon::now()->previous()->startOfWeek();
        $previousWeekEnd = Carbon::now()->previous()->endOfWeek();
        $partnerAssignment = PartnerAssignment::with("sitesAccount.currency:Id,CurrentRate")->where("PartnerId", $partnerId)->whereBetween("StartDate", [$previousWeekStart, $previousWeekEnd])->get();
        $dates = [];
        if ($partnerAssignment->count() > 0)
        {
            foreach ($partnerAssignment as $assignment)
            {
                $startDate = Carbon::parse($assignment->StartDate);
                $endDate = Carbon::parse($assignment->EndDate);
                $dates[$assignment->SiteAccountId][] = strtotime($startDate)* 1000;
                $dates[$assignment->SiteAccountId][] = strtotime($endDate)* 1000;
                $dates[$assignment->SiteAccountId]["tx"] = [];
                $dates[$assignment->SiteAccountId]["rate"] = $assignment->sitesAccount->currency->CurrentRate;
                $siteAccountTransactions = SitesAccountTransaction::whereBetween("TransactionDate", [$startDate->toDateString(), $endDate->toDateString()])->where('SiteAccountId', $assignment->SiteAccountId)->get();
                foreach ($siteAccountTransactions as $accountTransaction)
                {
                    $parsedDate = Carbon::parse($accountTransaction->TransactionDate);
                    /*if($accountTransaction->Id == 8)
                    {
                        dd($parsedDate->lessThanOrEqualTo($endDate));
                    }*/
                    // check transaction date belongs to this week
                    if($parsedDate->greaterThanOrEqualTo($startDate) && $parsedDate->lessThanOrEqualTo($endDate))
                    {
                        $dates[$assignment->SiteAccountId]["tx"][] = strtotime($accountTransaction->TransactionDate) * 1000;
                    }
                }
            }
            $site_acc = $partnerAssignment->pluck('sitesAccount.SiteAccountCode', 'sitesAccount.Id')->toArray();
        }
        return response()->json(['status' => 'success', 'site_acc' => $site_acc, 'tx_dates' => $dates, 'start_date' => $previousWeekStart->toDateString(), 'end_date' => $previousWeekEnd->toDateString()]);
    }

    public function shareProfitToPartners(Request $request)
    {
        $validationArray = [
            'start' => 'required',
            'end'   => 'required',
        ];

        $rules = [
            'start.required' => 'Start Date is required',
            'end.required'   => 'End Date is required',
        ];
        $validator = Validator::make($request->all(), $validationArray, $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }
        // received two dates from users
        // find shared account transactions of current logged in tenant
        // calculate commission
        // calculate win/lose
        // add commission + win/lose to find out total profit or loss
        // distribute profit/loss between shareholders

        $user           = \Illuminate\Support\Facades\Auth::user();
        $adminId = \App\Helpers\GeneralFunctions::adminUserId();
        $siteAccounts   = SitesAccount::where('TenantId', $user->TenantId)->with('currency')->get();
        $sharedAccounts = SiteAccountShareholder::whereIn('SiteAccountId', $siteAccounts->pluck('Id')->toArray())->whereNull("EndDate")->get();
        $transactions   = SitesAccountTransaction::whereBetween('TransactionDate', [Carbon::parse($request->start)->toDateTimeString(), Carbon::parse($request->end)->toDateTimeString()])->whereIn('SiteAccountId', $siteAccounts->pluck('Id')->toArray())->where('seen', '<>', 1)->get();
        if (empty($transactions->toArray()))
        {
            return redirect()->back()->with('error', 'No transaction found');
        }
        // loop through site account
        // check if it exist in sharedAccounts collections
        // sum up its transactional amount
        try
        {
            foreach ($siteAccounts as $account)
            {
                $uniqueId                 = Carbon::now()->format('YmdHis') . '-' . $account->Id;
                $txId                     = $uniqueId . '-' . rand();
                $associatedSharedAccounts = $sharedAccounts->whereIn('SiteAccountId', $account->Id)->all();
                $associatedTransactions   = $transactions->whereIn('SiteAccountId', $account->Id)->all();
                if (empty($associatedTransactions))
                {
                    continue;
                }
                $rawTotalTurnover         = collect($associatedTransactions)->sum('TotalTurnover');
                $totalTurnover            = $rawTotalTurnover * $account->TotalTurnoverPercent;
                // calculate win/loss
                $rawProfitLoss = collect($associatedTransactions)->sum('TotalProfitLoss');
                $currency = Currency::where([['Tenant_Id', '=', $user->TenantId], ['isBaseCurrency', '=', 1]])->first();
                // check if current account is not shared b/w partners
                if(empty($associatedSharedAccounts))// && $rawProfitLoss > 0)
                {
                    // then total profit/loss goes to tenant
                    // check current account currency
                    // check tenant base currency
                    // convert profit/loss if necessary
                    // step forward to next account
                    if($currency->Id != $account->CurrencyId)
                    {
                        // it may be next account is dealing within base currency and tenant changes its rate at popup then what rate should i pick for conversion (old database rate or updated Base rate on popup)
                        // check if base currency rate is updated at popup
                        if(array_key_exists($account->Id.'-'.$account->CurrencyId, $request->all()))
                        {
                            $tenantProfitLoss = round(($rawProfitLoss / $request[$account->Id.'-'.$account->CurrencyId]), 2);
                        }
                        else
                        {
                            // if currency does not exist in post request then pick current rate form currency table
                            $tenantProfitLoss = round(($rawProfitLoss / $account->currency->CurrentRate), 2);
                        }
                    }
                    $tenantProfit                  = new TenantProfitLoss();
                    $tenantProfit->TenantId        = $user->TenantId;
                    $tenantProfit->SiteAccountId   = $account->Id;
                    $tenantProfit->TransactionDate = Carbon::now();
                    $tenantProfit->TotalProfitLoss = round($tenantProfitLoss, 2);
                    $tenantProfit->created_at      = Carbon::now();
                    $tenantProfit->save();
                    $tenantTransaction                = new TenantAccountDetails();
                    $tenantTransaction->TransactionID = $txId;
                    $tenantTransaction->Amount        = round($rawProfitLoss,2);
                    $tenantTransaction->CreatedBy     = $user->Username;
                    $tenantTransaction->IPAddress     = $request->ip();
                    $tenantTransaction->Remarks       = 'Profit/Loss';
                    $tenantTransaction->created_at    = Carbon::now();
                    $tenantTransaction->CorrelationId = $uniqueId;
                    $tenantTransaction->UserId        = $adminId;
                    $tenantTransaction->AccountStatus = 3;
                    $tenantTransaction->currencyId    = $account->CurrencyId;
                    $tenantTransaction->transfer_type = 1;
                    $tenantTransaction->Current_Rate  = (array_key_exists($account->Id.'-'.$account->CurrencyId, $request->all()) == true) ? $request[$account->Id.'-'.$account->CurrencyId] : $account->currency->CurrentRate;
                    $tenantTransaction->save();
                    continue;
                }
                // loop through associated shareholder accounts
                // plAmount => profit loss amount
                $totalPartnerProfit = 0;// totalPartnerProfit => count total shared profit
                $totalRawPartnerProfit = 0; // to count total raw profitLoss (without any conversion)
                foreach ($associatedSharedAccounts as $sharedAccount)
                {
                    if ($rawProfitLoss > 0 && $sharedAccount->WinPercent > 0)
                    {
                        $plAmount = $rawProfitLoss * $sharedAccount->WinPercent;
                    }
                    elseif ($rawProfitLoss < 0 && $sharedAccount->LosePercent > 0)
                    {
                        $plAmount = $rawProfitLoss * $sharedAccount->LosePercent;
                    }
                    // calculating commission
                    $commission = $totalTurnover * $sharedAccount->TotalTurnoverPercentForCommission * $sharedAccount->TotalCommissionPercent;
                    // net profit/loss = commission + plAmount which goes to partner
                    $netProfitLoss = $commission + $plAmount;
                    // without any currency conversion
                    $partnerProfit = $netProfitLoss;
                    // check site account and tenant base currency is not same
                    // convert profit into tenant base currency
                    if($currency->Id != $account->CurrencyId)
                    {
                        // if rate comes from popup
                        if (array_key_exists($account->Id . '-' . $account->CurrencyId, $request->all())) {
                            $netProfitLoss = ($netProfitLoss / $request[$account->Id . '-' . $account->CurrencyId]);
                            $rawConvertedProfitLoss = $rawProfitLoss / $request[$account->Id . '-' . $account->CurrencyId];
                        } else {
                            // if not then get rate from currency table
                            $netProfitLoss = ($netProfitLoss / $account->currency->CurrentRate);
                            $rawConvertedProfitLoss = $rawProfitLoss / $account->currency->CurrentRate;
                        }
                    }
                    else
                    {
                        // if tenant and site account have same currency
                        $rawConvertedProfitLoss = $rawProfitLoss;
                    }
                    // if tenant and site account have different currency then netProfitLoss is converted into Tenant Base Currency
                    $totalPartnerProfit += $netProfitLoss;
                    $totalRawPartnerProfit += $partnerProfit;
                    // check partner base currency
                    $partnerBaseCurrency = PartnerSetting::where([['settingName', '=', 'base_currency'], ['partner_id', '=', $sharedAccount->PartnerId]])->with('currency')->first();
                    if($currency->Id != $partnerBaseCurrency->currency->Id)
                    {
                        // now check if partner currency rate is updated in popup
                        if(array_key_exists($account->Id.'-'.$partnerBaseCurrency->currency->CurrencyId, $request->all()))
                        {
                            $netProfitLoss = $netProfitLoss * $request[$account->Id.'-'.$partnerBaseCurrency->currency->CurrentRate];
                        }
                        else
                        {
                            $netProfitLoss = $netProfitLoss * $partnerBaseCurrency->currency->CurrentRate;
                        }

                    }
                    // adding commission to partners_profitloss table
                    $profit                  = new PartnerProfitLoss();
                    $profit->PartnerId       = $sharedAccount->PartnerId;
                    $profit->SiteAccountId   = $account->Id;
                    $profit->TransactionDate = Carbon::now();
                    $profit->TotalProfitLoss = round($netProfitLoss, 2);
                    $profit->created_at      = Carbon::now();
                    $profit->save();
                    // create trnasaction in partner account details
                    $detail                = new PartnerAccountDetails();
                    $detail->TransactionID = $txId;
                    $detail->Amount        = round($partnerProfit, 2);
                    $detail->CreatedBy     = $user->Username;
                    $detail->IPAddress     = $request->ip();
                    $detail->Remarks       = 'No Remarks';
                    $detail->CorrelationId = $uniqueId;
                    $detail->UserId        = $sharedAccount->PartnerId;
                    $detail->created_at    = Carbon::now();
                    $detail->AccountStatus = 3;
                    $detail->currencyId    = $account->CurrencyId;
                    $detail->transfer_type = 1;
                    $detail->Current_Rate  = (array_key_exists($account->Id.'-'.$account->CurrencyId, $request->all()) == true) ? $request[$account->Id.'-'.$account->CurrencyId] : $account->currency->CurrentRate;//(array_key_exists($account->Id.'-'.$account->CurrencyId, $request->all()) == true) ? $request[$account->Id.'-'.$partnerBaseCurrency->currency->CurrencyId] : $partnerBaseCurrency->currency->CurrentRate;
                    $detail->save();
                }
                // calculate remaining profit
                $rawRemainingProfitLoss = $rawProfitLoss - $totalRawPartnerProfit;
                $tenantProfitLoss = $rawConvertedProfitLoss - $totalPartnerProfit;
                // now send remaining amount to tenant account
                $tenantProfit                  = new TenantProfitLoss();
                $tenantProfit->TenantId        = $user->TenantId;
                $tenantProfit->SiteAccountId   = $account->Id;
                $tenantProfit->TransactionDate = Carbon::now();
                $tenantProfit->TotalProfitLoss = round($tenantProfitLoss, 2);
                $tenantProfit->created_at      = Carbon::now();
                $tenantProfit->save();
                // save tenant transaction
                $tenantTransaction                = new TenantAccountDetails();
                $tenantTransaction->TransactionID = $txId;
                $tenantTransaction->Amount        = round($rawRemainingProfitLoss, 2);
                $tenantTransaction->CreatedBy     = $user->Username;
                $tenantTransaction->IPAddress     = $request->ip();
                $tenantTransaction->Remarks       = 'Profit/Loss';
                $tenantTransaction->created_at    = Carbon::now();
                $tenantTransaction->CorrelationId = $uniqueId;
                $tenantTransaction->UserId        = $adminId;
                $tenantTransaction->AccountStatus = 3;
                $tenantTransaction->currencyId    = $account->CurrencyId;
                $tenantTransaction->transfer_type = 1;
                $tenantTransaction->Current_Rate  = (array_key_exists($account->Id.'-'.$account->CurrencyId, $request->all()) == true) ? $request[$account->Id.'-'.$account->CurrencyId] : $account->currency->CurrentRate;
                $tenantTransaction->save();
            }
            // update transactions flag to read
            $txIds = $transactions->pluck('Id')->toArray();
            if(!empty($txIds))
            {
                if(count($txIds) == 1)
                {
                    DB::table('siteaccounttransactions')->where('Id', $transactions->pluck('Id')->toArray())->update(['seen' => 1]);
                }
                elseif (count($txIds) > 1)
                {
                    DB::table('siteaccounttransactions')->whereIn('Id', $transactions->pluck('Id')->toArray())->update(['seen' => 1]);
                }
            }
        }
        catch (\Exception $e) {
            Log::error('SHARE PROFIT LOSS:    ' . $e->getMessage().'    LINE:'.$e->getLine());
            return redirect()->back()->with('error', 'An error occurred in sharing profit');
        }
        return redirect()->back()->with('success', 'Profit/Loss has been shared');
    }
    public function profitLossDetails(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $siteAccounts = SitesAccount::where('TenantId', $user->TenantId)->with(['currency.currencyList', 'sitesAccountTransaction' => function($query) use($request){
            $query->whereBetween('TransactionDate', [Carbon::parse($request->start), Carbon::parse($request->end)])->where("seen", 0);
        }])->get();
        $data = [];
        foreach ($siteAccounts as $account)
        {
            //$totalTransaction = $account->sitesAccountTransaction->where('TransactionDate', '>=',  Carbon::parse($request->start)->toDateString())->where('TransactionDate', '<=', Carbon::parse($request->end)->toDateString())->where('seen', 0);
            //only not seen site account transaction
            if($account->sitesAccountTransaction->count() > 0)
                $data[$account->Id] = [$account->SiteAccountCode, count($account->sitesAccountTransaction->toArray()), 'cur' => [$account->currency->Id, $account->currency->currencyList->currency.' ('.$account->currency->currencyList->code.')', $account->currency->CurrentRate]];
        }
        return response()->json($data, 200);
    }

}
