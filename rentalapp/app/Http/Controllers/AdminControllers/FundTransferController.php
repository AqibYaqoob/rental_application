<?php

namespace App\Http\Controllers\AdminControllers;

use App\Currency;
use App\Http\Controllers\Controller;
use App\PartnerAccountDetails;
use App\PartnerSetting;
use App\SitesAccount;
use App\TenantAccountDetails;
use App\User;
use Auth;
use GeneralFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Validator;
use View;

class FundTransferController extends Controller
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
    =          Section For Fund transfer Screen Functions         =
    =============================================================*/
    /**
     *
     * Fund Transfer Screen Display Function
     *
     */

    public function fund_transfer_account_screen(Request $req)
    {
        $data['title']            = 'Fund Transfer Account';
        $data['partners_account'] = User::where('TenantId', Auth::user()->TenantId)->where('Roles', 3)->get();
        $data['partners_account'] = $data['partners_account']->toArray();
        return view('company_portal.layouts.transfer_funds.add_funds_for_transactions', $data);
    }

    /**
     *
     * Get Tenant Account Currency and Rates
     *
     */

    public function get_tenant_account_currency(Request $req)
    {
        $data['get_tenant_account_balance'] = 0.0;
        $data['get_base_currency']          = '';
        $getAccountBalance                  = TenantAccountDetails::with('currency')->where('UserId', GeneralFunctions::adminUserId())->get();
        $getAccountBalance                  = $getAccountBalance->toArray();
        $getBaseCurrency                    = Currency::with('currencyList')->where('Tenant_Id', Auth::user()->TenantId)->where('isBaseCurrency', 1)->first();
        $data['get_tenant_account_balance'] = GeneralFunctions::getBalance($getAccountBalance, $getBaseCurrency);
        if ($getBaseCurrency) {
            $data['get_base_currency'] = $getBaseCurrency->currencyList->currency.' ('.$getBaseCurrency->currencyList->code.')';
        }
        // 1) Get Total Account Balance from the Partners Account

        return response()->json(['status' => 'success', 'account_balance' => $data['get_tenant_account_balance'], 'base_currency' => $data['get_base_currency']]);
    }

    /**
     *
     * Deposit Amount Data Function
     *
     */

    public function deposit_amount(Request $req)
    {
        // 1) Validation
        $validationArray = [
            'partner_account' => 'required',
            'transfer_amount' => 'required|numeric',
            'password'        => 'required',
        ];
        if ($req->wh_edit_transaction) {
            $validationArray['transfer_remark'] = 'required';
            $validationArray['settlement_type'] = 'required';
        }
        $rules = [
            'partner_account.required' => 'Partner Account is required',
            'transfer_amount.required' => 'Transfer Amount is required',
            'transfer_amount.numeric'  => 'Transfer Amount is Numeric Only',
            'transfer_remark.required' => 'Remark for Updation required',
            'settlement_type.required' => 'Fund Type is required',
            'password.required'        => 'Please provide account password for transaction',
        ];
        $validator                          = Validator::make($req->all(), $validationArray, $rules);
        $errors                             = GeneralFunctions::error_msg_serialize($validator->errors());
        $data['get_tenant_account_balance'] = 0.0;
        if (count($errors) > 0) {
            return response()->json(['status' => 'error', 'msg_data' => $errors]);
        }
        /**
         *
         * Check if Password for Transaction is accurate to User Password (Then he will able to proceed to transaction process)
         *
         */
        $user = User::find(Auth::user()->id);
        if (!Hash::check($req->password, $user->password)) {
            return response()->json(['status' => 'error', 'msg_data' => ['Password is not correct. Please provide valid password']]);
        }
        $fromCurrencyDetails = Currency::where('Tenant_Id', Auth::user()->TenantId)->where('isBaseCurrency', 1)->with('currencyList')->first();
        if ($req->settlement_type == 2) {
            $req->transfer_amount = (-$req->transfer_amount);

            /**
             *
             * Check if Reversing the Transaction (check if amount is extracting more than available balance)
             *
             */
            if ($req->transfer_amount < 0) {
                // 1) Check Balance First for Partners Account As well if the settlement is in negative transaction than first we have to check that amount is available in the partner account as well.
                $partnerCurrencyDetails     = PartnerSetting::with('currency.currencyList')->where('partner_id', $req->partner_account)->first();
                $partnerAccountBalance      = PartnerAccountDetails::with('currency')->where('UserId', $req->partner_account)->get();
                $partnerAccountBalance      = $partnerAccountBalance->toArray();
                $checkPartnerAccountBalance = GeneralFunctions::getBalance($partnerAccountBalance, $partnerCurrencyDetails->currency);
                $transferAmount             = $req->transfer_amount * $partnerCurrencyDetails->currency->CurrentRate;
                if (abs($transferAmount) > $checkPartnerAccountBalance) {
                    return response()->json(['status' => 'error', 'msg_data' => ["Partner Account Holder don't have enough Funds to transfer. Please check Cash Balance "]]);
                }
            }

        }
        // 2) Check if Available Balance is Enough to transfer such amount
        $getAccountBalance = TenantAccountDetails::where('UserId', GeneralFunctions::adminUserId())->first();
        if ($getAccountBalance) {
            $data['get_tenant_account_balance'] = $getAccountBalance->Amount;
        }
        if ($req->transfer_amount > $data['get_tenant_account_balance']) {
            return response()->json(['status' => 'error', 'msg_data' => ["You don't have enough Funds to transfer. Please check your Cash Balance "]]);
        }
        // 3) Check if Enough Amount is Present in account to Transfer and Deposit
        $details                                               = GeneralFunctions::convertAmount(GeneralFunctions::adminUserId(), $req->partner_account, $req->transfer_amount, $req->transfer_type);
        $data['from_currency_id']                              = $fromCurrencyDetails->Id;
        $data['converted_transfer_amount_in_partners_account'] = (int) $details['converted_funds'];
        $data['from_currency_rate']                            = $fromCurrencyDetails->CurrentRate;
        $data['from_currency']                                 = $fromCurrencyDetails->currencyList->currency;
        $data['to_currency']                                   = $details['partners_currency'];
        $data['current_rate']                                  = $details['currency_rate'];
        $data['partners_detail']                               = User::where('id', $req->partner_account)->first();
        $data['tenant_detail']                                 = User::where('id', Auth::user()->id)->first();
        $data['transfer_amount']                               = $req->transfer_amount;
        $data['to_currency_id']                                = $details['partners_currency_id'];
        $data['transfer_remark']                               = $req->transfer_remark;
        return response()->json(['status' => 'success', 'result' => $data]);
    }

    /**
     *
     * Get Partner Account Details
     *
     */

    public function get_partner_record(Request $req)
    {
        //
        $data['partnerBaseCurrency'] = '';
        $getTargetBaseCurrency       = PartnerSetting::with('currency','currency.currencyList')->where([['settingName', 'base_currency'], ['partner_id', $req->partner_id]])->first();
        //dd($getTargetBaseCurrency);
        if ($getTargetBaseCurrency) {
            $data['partnerBaseCurrency'] = $getTargetBaseCurrency->currency->currencyList->currency.' ('.$getTargetBaseCurrency->currency->currencyList->code.')';
        }
        $getPartnerCashBalance = PartnerAccountDetails::with('currency')->where('UserId', $req->partner_id)->get();
        $getPartnerCashBalance = $getPartnerCashBalance->toArray();
        // 1) Get Total Account Balance from the Partners Account
        $data['partnerBalance'] = GeneralFunctions::getBalance($getPartnerCashBalance, $getTargetBaseCurrency->currency);
        return response()->json(['status' => 'success', 'result' => $data]);
    }

    /**
     *
     * Withdraw Amount Process
     *
     */

    public function withdraw_amount(Request $req)
    {
        // 1) Validation
        $validationArray = [
            'wh_partner_account' => 'required',
            'wh_transfer_amount' => 'required|numeric',
            'password'           => 'required',
        ];
        if ($req->wh_edit_transaction) {
            $validationArray['wh_remark']       = 'required';
            $validationArray['settlement_type'] = 'required';
        }
        $rules = [
            'wh_partner_account.required' => 'Partner Account is required',
            'wh_transfer_amount.required' => 'Transfer Amount is required',
            'wh_transfer_amount.numeric'  => 'Transfer Amount is Numeric Only',
            'wh_remark.required'          => 'Remark for Updation required',
            'settlement_type.required'    => 'Fund Type is required',
            'password.required'           => 'Please provide account password for transaction',
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => 'error', 'msg_data' => $errors]);
        }
        /**
         *
         * Check if Password for Transaction is accurate to User Password (Then he will able to proceed to transaction process)
         *
         */
        $user = User::find(Auth::user()->id);
        if (!Hash::check($req->password, $user->password)) {
            return response()->json(['status' => 'error', 'msg_data' => ['Password is not correct. Please provide valid password']]);
        }
        $data['from_currency'] = PartnerSetting::with('currency.currencyList')->where('partner_id', $req->wh_partner_account)->first();
        if ($req->settlement_type == 2) {
            $req->wh_transfer_amount = (-$req->wh_transfer_amount);

            /**
             *
             * Check if Reversing the Transaction (check if amount is extracting more than available balance)
             *
             */
            if ($req->wh_transfer_amount < 0) {
                // 1) Check Balance First for Tenant Account As well if the settlement is in negative transaction than first we have to check that amount is available in the tenant account as well.
                $fromCurrencyDetails       = Currency::where('Tenant_Id', Auth::user()->TenantId)->where('isBaseCurrency', 1)->first();
                $tenantAccountBalance      = TenantAccountDetails::with('currency')->where('UserId', Auth::user()->id)->get();
                $tenantAccountBalance      = $tenantAccountBalance->toArray();
                $checkTenantAccountBalance = GeneralFunctions::getBalance($tenantAccountBalance, $fromCurrencyDetails);

                $transferAmount = ($req->wh_transfer_amount / $data['from_currency']->currency->CurrentRate) * $fromCurrencyDetails->CurrentRate;
                if (abs($transferAmount) > $checkTenantAccountBalance) {
                    return response()->json(['status' => 'error', 'msg_data' => ["Tenant Account Holder don't have enough Funds to transfer. Please check Cash Balance "]]);
                }
            }

        }
        // 2) Check if Available Balance is Enough to transfer such amount
        $getAccountBalance = PartnerAccountDetails::with('currency')->where('UserId', $req->wh_partner_account)->get();
        $getAccountBalance = $getAccountBalance->toArray();
        // 3) Get Total Account Balance from the Partners Account
        $data['get_partner_account_balance'] = GeneralFunctions::getBalance($getAccountBalance, $data['from_currency']->currency);
        if ($req->wh_transfer_amount > $data['get_partner_account_balance']) {
            return response()->json(['status' => 'error', 'msg_data' => ["Account Holder don't have enough Funds to transfer. Please check Cash Balance "]]);
        }
        // 4) Check if Enough Amount is Present in account to Withdraw
        $details = GeneralFunctions::convertAmount($req->wh_partner_account, GeneralFunctions::adminUserId(), $req->wh_transfer_amount, $req->transfer_type);
        $data['converted_transfer_amount_in_tenant_account'] = ($req->wh_transfer_amount / $data['from_currency']->currency->CurrentRate) * $details['currency_rate'];
        $from_currency_rate                                  = $data['from_currency']->currency->CurrentRate;
        $data['from_currency_id']                            = $data['from_currency']->currency->Id;
        $data['transfer_amount']                             = (int) $req->wh_transfer_amount;
        $data['current_rate']                                = $details['currency_rate'];
        $data['from_currency']                               = $data['from_currency']->currency->currencyList->currency;
        $data['from_currency_rate']                          = $from_currency_rate;
        $data['to_currency']                                 = $details['tenant_currency'];
        $data['partners_detail']                             = User::where('id', $req->wh_partner_account)->first();
        $data['tenant_detail']                               = User::where('id', Auth::user()->id)->first();
        $data['to_currency_id']                              = $details['tenant_currency_id'];
        $data['wh_remark']                                   = $req->wh_remark;
        return response()->json(['status' => 'success', 'result' => $data]);
    }

    /**
     *
     * Partner to Partner Amount Process
     *
     */

    public function p2p_transfer_amount(Request $req)
    {
        // 1) Validation
        $validationArray = [
            'p2p_partner_account'    => 'required',
            'p2p_transfer_amount'    => 'required|numeric',
            'p2p_to_partner_account' => 'required',
            'password'               => 'required',
        ];
        $rules = [
            'p2p_partner_account.required'    => 'Source Partner Account is required',
            'p2p_to_partner_account.required' => 'Target Partner Account is required',
            'p2p_transfer_amount.required'    => 'Transfer Amount is required',
            'p2p_transfer_amount.numeric'     => 'Transfer Amount is Numeric Only',
            'password.required'               => 'Please provide account password for transaction',
        ];
        $validator                                  = Validator::make($req->all(), $validationArray, $rules);
        $errors                                     = GeneralFunctions::error_msg_serialize($validator->errors());
        $data['get_source_partner_account_balance'] = 0.0;
        if (count($errors) > 0) {
            return response()->json(['status' => 'error', 'msg_data' => $errors]);
        }
        /**
         *
         * Check if Password for Transaction is accurate to User Password (Then he will able to proceed to transaction process)
         *
         */
        $user = User::find(Auth::user()->id);
        if (!Hash::check($req->password, $user->password)) {
            return response()->json(['status' => 'error', 'msg_data' => ['Password is not correct. Please provide valid password']]);
        }
        /**
         *
         * Check if Both Partners are not same.
         *
         */
        if ($req->p2p_partner_account == $req->p2p_to_partner_account) {
            return response()->json(['status' => 'error', 'msg_data' => ['Please select different partners for transacrion.']]);
        }

        // 2) Check if Available Balance is Enough to transfer such amount
        $data['from_currency'] = PartnerSetting::with('currency.currencyList')->where('partner_id', $req->p2p_partner_account)->first();
        $getAccountBalance     = PartnerAccountDetails::with('currency')->where('UserId', $req->p2p_partner_account)->get();
        $getAccountBalance     = $getAccountBalance->toArray();
        // 3) Get Total Account Balance from the Partners Account
        $data['get_source_partner_account_balance'] = GeneralFunctions::getBalance($getAccountBalance, $data['from_currency']->currency);
        if ($req->p2p_transfer_amount > $data['get_source_partner_account_balance']) {
            return response()->json(['status' => 'error', 'msg_data' => ["Account Holder don't have enough Funds to transfer. Please check Cash Balance "]]);
        }
        // 3) Check if Enough Amount is Present in account for P2P Process
        $details = GeneralFunctions::convertAmount($req->p2p_partner_account, $req->p2p_to_partner_account, $req->p2p_transfer_amount, $req->transfer_type);

        $data['converted_transfer_amount_in_partners_account'] = ($req->p2p_transfer_amount / $data['from_currency']->currency->CurrentRate) * $details['currency_rate'];
        $from_currency_rate                                    = $data['from_currency']->currency->CurrentRate;
        $data['from_currency_id']                              = $data['from_currency']->currency->Id;
        $data['transfer_amount']                               = (int) $req->p2p_transfer_amount;
        $data['current_rate']                                  = $details['currency_rate'];
        $data['from_currency']                                 = $data['from_currency']->currency->currencyList->currency;
        $data['from_currency_rate']                            = $from_currency_rate;
        $data['to_currency']                                   = $details['partners_currency'];
        $data['source_partners_detail']                        = User::where('id', $req->p2p_partner_account)->first();
        $data['target_partners_detail']                        = User::where('id', $req->p2p_to_partner_account)->first();
        $data['to_currency_id']                                = $details['partners_currency_id'];
        return response()->json(['status' => 'success', 'result' => $data]);
    }

    /**
     *
     * Transfer Funds from One Account to Other.
     *
     */
    public function transfer_funds(Request $req)
    {
        // 1) Check the Transfer type
        // (1 = Tenant Deposit to Partners Account)
        // (2 = Tenant withdraw from Partners Account)
        // (3 = Transfer Amount from one Partner to Other Partner (P2P))
        //TransactionID (yymmddhhmmss-sourceid-123)
        $date = date('Ymdhms');
        if ($req->input('transfer_type') == 2) {
            // 1) Add Transaction to Partners Account Table

            $partner_account_status        = 2;
            $tenant_account_status         = 1;
            $partner_remark                = 'Amount Transfer to other account';
            $tenant_remark                 = 'Added Amount from other Account';
            $partner_amount                = (-$req->source_amount);
            $tenant_amount                 = $req->target_amount;
            $partner_transaction_reference = null;
            $tenant_transaction_reference  = null;
            if ($req->edit_version) {
                $partner_transaction_reference = PartnerAccountDetails::where('CorrelationId', $req->correlation_id)->first();
                $partner_transaction_reference = $partner_transaction_reference->TransactionID;
                $tenant_transaction_reference  = TenantAccountDetails::where('CorrelationId', $req->correlation_id)->first();
                $tenant_transaction_reference  = $tenant_transaction_reference->TransactionID;
                $tenant_remark                 = $req->wh_remark;
                $partner_remark                = $req->wh_remark;
                $partner_amount                = $req->source_amount;
            }
            if ($req->settle_account_type == 2) {
                $partner_account_status = 1;
                $tenant_account_status  = 2;
                $tenant_amount          = (-$req->target_amount);

            }
            $firstTransaction = [
                'TransactionID'            => $date . '-' . $req->target_id . '-' . rand(10, 10000),
                'currencyId'                 => $req->source_currency_id,
                'Amount'                   => (-$req->source_amount),
                'CreatedBy'                => Auth::user()->Username,
                'AccountStatus'            => $partner_account_status, // Transfer amount to other account
                'Remarks'                  => $partner_remark,
                'CorrelationId'            => $date . '-' . $req->source_id . '-' . $req->target_id,
                'UserId'                   => $req->source_id,
                'IPAddress'                => GeneralFunctions::getRealIpAddr(),
                'transfer_type'            => 1,
                'reference_transaction_id' => $partner_transaction_reference,
                'Current_Rate'             => $req->source_currency_rate,
            ];

            // 2) Add Transaction to Tenant Account
            $secondTransaction = [
                'TransactionID'            => $date . '-' . $req->source_id . '-' . rand(10, 10000),
                'currencyId'                 => $req->target_currency_id,
                'Amount'                   => $req->target_amount,
                'CreatedBy'                => Auth::user()->Username,
                'AccountStatus'            => $tenant_account_status, // Withdraw into own account
                'Remarks'                  => $tenant_remark,
                'CorrelationId'            => $date . '-' . $req->source_id . '-' . $req->target_id,
                'UserId'                   => $req->target_id,
                'IPAddress'                => GeneralFunctions::getRealIpAddr(),
                'reference_transaction_id' => $tenant_transaction_reference,
                'Current_Rate'             => $req->target_currency_rate,
            ];
            // 4) Save Record into Partners and Tenant Account Tables
            PartnerAccountDetails::create($firstTransaction);
            TenantAccountDetails::create($secondTransaction);

        } elseif ($req->input('transfer_type') == 1) {

            $tenant_account_status         = 2;
            $partner_account_status        = 1;
            $tenant_remark                 = 'Amount Transfer to other account';
            $partner_remark                = 'Added Amount from other Account';
            $tenant_amount                 = (-$req->source_amount);
            $partner_amount                = $req->target_amount;
            $tenant_transaction_reference  = null;
            $partner_transaction_reference = null;
            if ($req->edit_version) {
                $partner_transaction_reference = PartnerAccountDetails::where('CorrelationId', $req->correlation_id)->first();
                $partner_transaction_reference = $partner_transaction_reference->TransactionID;
                $tenant_transaction_reference  = TenantAccountDetails::where('CorrelationId', $req->correlation_id)->first();
                $tenant_transaction_reference  = $tenant_transaction_reference->TransactionID;
                $tenant_remark                 = $req->wh_remark;
                $partner_remark                = $req->wh_remark;
                $tenant_amount                 = $req->source_amount;
            }
            if ($req->settle_account_type == 2) {
                $tenant_account_status  = 1;
                $partner_account_status = 2;
                $partner_amount         = (-$req->target_amount);

            }
            // 1) Add Transaction to Tenant Account Table
            $firstTransaction = [
                'TransactionID'            => $date . '-' . $req->target_id . '-' . rand(10, 10000),
                'currencyId'                 => $req->source_currency_id,
                'Amount'                   => (-$req->source_amount),
                'CreatedBy'                => Auth::user()->Username,
                'AccountStatus'            => $tenant_account_status, // Transfer amount to other account
                'Remarks'                  => $tenant_remark,
                'CorrelationId'            => $date . '-' . $req->source_id . '-' . $req->target_id,
                'UserId'                   => $req->source_id,
                'IPAddress'                => GeneralFunctions::getRealIpAddr(),
                'reference_transaction_id' => $tenant_transaction_reference,
                'Current_Rate'             => $req->source_currency_rate,
            ];

            // 2) Add Transaction to Partners Account
            $secondTransaction = [
                'TransactionID'            => $date . '-' . $req->source_id . '-' . rand(10, 10000),
                'currencyId'                 => $req->target_currency_id,
                'Amount'                   => $req->target_amount,
                'CreatedBy'                => Auth::user()->Username,
                'AccountStatus'            => $partner_account_status, // Withdraw into own account
                'Remarks'                  => $partner_remark,
                'CorrelationId'            => $date . '-' . $req->source_id . '-' . $req->target_id,
                'UserId'                   => $req->target_id,
                'IPAddress'                => GeneralFunctions::getRealIpAddr(),
                'transfer_type'            => 1,
                'reference_transaction_id' => $partner_transaction_reference,
                'Current_Rate'             => $req->target_currency_rate,
            ];
            TenantAccountDetails::create($firstTransaction);
            PartnerAccountDetails::create($secondTransaction);
        } else {
            // 1) Add Transaction to Partners Account
            $firstTransaction = [
                'TransactionID' => $date . '-' . $req->target_id . '-' . rand(10, 10000),
                'currencyId'      => $req->source_currency_id,
                'Amount'        => (-$req->source_amount),
                'CreatedBy'     => Auth::user()->Username,
                'AccountStatus' => 2, // Transfer amount to other account
                'Remarks'       => 'Amount Transfer to other account',
                'CorrelationId' => $date . '-' . $req->source_id . '-' . $req->target_id,
                'UserId'        => $req->source_id,
                'IPAddress'     => GeneralFunctions::getRealIpAddr(),
                'transfer_type' => 2,
                'Current_Rate'  => $req->source_currency_rate,
            ];

            // 2) Add Transaction to Partners Account
            $secondTransaction = [
                'TransactionID' => $date . '-' . $req->source_id . '-' . rand(10, 10000),
                'currencyId'      => $req->target_currency_id,
                'Amount'        => $req->target_amount,
                'CreatedBy'     => Auth::user()->Username,
                'AccountStatus' => 1, // Withdraw into own account
                'Remarks'       => 'Added Amount from other Account',
                'CorrelationId' => $date . '-' . $req->source_id . '-' . $req->target_id,
                'UserId'        => $req->target_id,
                'IPAddress'     => GeneralFunctions::getRealIpAddr(),
                'transfer_type' => 2,
                'Current_Rate'  => $req->target_currency_rate,
            ];

            PartnerAccountDetails::create($firstTransaction);
            PartnerAccountDetails::create($secondTransaction);
        }

        return redirect()->back()->with('success', 'Successfully Transaction is Completed');
    }

    /**
     *
     * Tenant Transaction List Function.
     *
     */
    public function tenant_transactions_list(Request $req)
    {
        $record         = TenantAccountDetails::with('user')->with('currency', 'currency.currencyList')->where('UserId', Auth::user()->id)->get();
        $record         = $record->toArray();
        $responseResult = [];
        // 1) Loop through whole transaction and create result to be shown on the list
        // 2) Base Currency of Tenant Account
        $getTenantBaseCurrency = Currency::where('Tenant_Id', Auth::user()->TenantId)->where('isBaseCurrency', 1)->first();
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
            $responseResult[$key]['in_currency']                    = $value['currency']['currency_list']['currency'].' ('.$value['currency']['currency_list']['code'].')';
            $responseResult[$key]['amount_in_different_currencies'] = $value['Amount'];
            $convertedAmountToBaseCurrency                          = $value['Amount'] / $value['Current_Rate'];
            $responseResult[$key]['amount_in_base_currency']        = $value['Amount'] / $value['Current_Rate'] * $getTenantBaseCurrency->CurrentRate;
            $totalBalance                                           = $totalBalance + $responseResult[$key]['amount_in_base_currency'];
            $responseResult[$key]['created_at']                     = $value['created_at'];
            $responseResult[$key]['transaction_id']                 = $value['TransactionID'];
            $responseResult[$key]['remarks']                        = $value['Remarks'];
            $responseResult[$key]['reference_transaction_id']       = $value['reference_transaction_id'];
            $responseResult[$key]['account_status']                 = $value['AccountStatus'];
        }
        $data['responseResult'] = $responseResult;
        $data['totalBalance']   = $totalBalance;
        return view('company_portal.layouts.transfer_funds.tenant_transaction_list', $data);
    }

    /**
     *
     * Tenant Fund Transaction Edit Function
     *
     */
    public function tenant_transactions_edit(Request $req)
    {
        // 1) Get Record from Database
        $data['transaction_details'] = TenantAccountDetails::with('currency')->with('user')->where('id', Crypt::decryptString($req->id))->first();
        $data['transaction_details'] = $data['transaction_details']->toArray();
        // 2) Get Base Currency of Tenant
        $getTenantBaseCurrency = Currency::where('Tenant_Id', Auth::user()->TenantId)->where('isBaseCurrency', 1)->first();
        // 3) Get Source Record from Tenant Account Table
        $source_id                                           = explode('-', $data['transaction_details']['TransactionID']);
        $userDetails                                         = User::where('id', $source_id[1])->first();
        $responseResult[0]['source']                         = $userDetails->Username;
        $responseResult[0]['source_id']                      = $userDetails->id;
        $responseResult[0]['remarks']                        = $data['transaction_details']['AccountStatus'] == 1 ? 'Deposit Ammount from Account (' . $responseResult[0]['source'] . ')' : ($data['transaction_details']['AccountStatus'] == 2 ? 'Transfer Ammount to Account (' . $responseResult[0]['source'] . ')' : 'todo');
        $responseResult[0]['in_currency']                    = $data['transaction_details']['currency']['CurrencyName'];
        $responseResult[0]['amount_in_different_currencies'] = $data['transaction_details']['Amount'];
        $convertedAmountToBaseCurrency                       = $data['transaction_details']['Amount'] / $data['transaction_details']['currency']['CurrentRate'];
        $responseResult[0]['amount_in_base_currency']        = $data['transaction_details']['Amount'] / $data['transaction_details']['currency']['CurrentRate'] * $getTenantBaseCurrency->CurrentRate;
        $responseResult[0]['created_at']                     = $data['transaction_details']['created_at'];
        $responseResult[0]['CorrelationId']                  = $data['transaction_details']['CorrelationId'];
        $data['edit_version']                                = true;
        $data['responseResult']                              = $responseResult;
        $data['partners_account']                            = User::where('TenantId', Auth::user()->TenantId)->where('Roles', 3)->get();
        $data['partners_account']                            = $data['partners_account']->toArray();
        return view('company_portal.layouts.transfer_funds.add_funds_for_transactions', $data);
    }

    /*=====  End of Section Fund transfer Screen  ======*/
}
