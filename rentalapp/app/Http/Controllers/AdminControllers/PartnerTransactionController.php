<?php
namespace App\Http\Controllers\AdminControllers;

use App\Currency;
use App\Http\Controllers\Controller;
use App\PartnerAccountDetails;
use App\PartnerSetting;
use App\SitesAccount;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use View;

class PartnerTransactionController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /*=======================================================
    =            Section for Partner Transaction            =
    =======================================================*/

    /**
     *
     * Tenant Transaction List Function.
     *
     */
    public function partner_transactions_list(Request $req)
    {
        /*$record         = PartnerAccountDetails::with('user')->with('currency.currencyLists')->where('UserId', Auth::user()->id)->get();
        $record         = $record->toArray();
        $responseResult = [];
        // 1) Loop through whole transaction and create result to be shown on the list
        // 2) Base Currency of Tenant Account
        $getPartnerBaseCurrency = PartnerSetting::where('partner_id', Auth::user()->id)->with('currency')->first();
        $totalBalance           = 0;
        // dd($record);
        foreach ($record as $key => $value) {
            dd($value);
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

                $source_id                           = explode('-', $value['TransactionID']);
                $responseResult[$key]['source']      = SitesAccount::where('id', $source_id[1])->first();
                $responseResult[$key]['source']      = 'Site Account Code (' . $responseResult[$key]['source']->SiteAccountCode . ')';
                $responseResult[$key]['transaction'] = 'Profit Loss From Account Number Code (' . $responseResult[$key]['source'] . ')';
            }
            $responseResult[$key]['no']                             = $key + 1;
            $responseResult[$key]['in_currency']                    = $value['currency']['CurrencyName'];
            $responseResult[$key]['amount_in_different_currencies'] = $value['Amount'];
            $convertedAmountToBaseCurrency                          = $value['Amount'] / $value['currency']['CurrentRate'];
            $responseResult[$key]['amount_in_base_currency']        = $value['Amount'] / $value['currency']['CurrentRate'] * $getPartnerBaseCurrency->currency->CurrentRate;
            $totalBalance                                           = $totalBalance + $responseResult[$key]['amount_in_base_currency'];
            $responseResult[$key]['created_at']                     = $value['created_at'];
            $responseResult[$key]['transaction_id']                 = $value['TransactionID'];
            $responseResult[$key]['remarks']                        = $value['Remarks'];
            $responseResult[$key]['reference_transaction_id']       = $value['reference_transaction_id'];
        }
        $data['responseResult'] = $responseResult;
        $data['totalBalance']   = $totalBalance;*/
        return view('partner_portal.layout.transfer_funds.transaction_list');

    }
    public function ajaxPartnerAccountDetail(Request $request)
    {
        $partnerAccountDetails = PartnerAccountDetails::with('currency', 'user', 'currency.currencyList')->where('UserId', Crypt::decryptString($request->p_id))->offset($request->start)->limit($request->length)->get();
        $response = [];
        $count = 1;
        $total = 0;
        $baseTotal = 0;
        if(!empty($partnerAccountDetails->toArray()))
        {
            foreach ($partnerAccountDetails as $detail)
            {
                $partnerBaseCurrencyRate = PartnerSetting::where([['settingName', 'base_currency'], ['partner_id', $detail->user->id]])->first();
                if (!is_null($partnerBaseCurrencyRate))
                {
                    $rate = Currency::find($partnerBaseCurrencyRate->value)->CurrentRate;
                }
                if($detail->AccountStatus == 1)
                    $txt = 'WithDraw';
                elseif($detail->AccountStatus == 2)
                    $txt = 'Deposit';
                elseif($detail->AccountStatus == 3)
                    $txt = 'Profit/Loss';
                elseif($detail->AccountStatus == 4)
                    $txt = 'Monthly Interest';
                if($detail->transfer_type == 1)
                    $type = 'Not P2P';
                elseif($detail->transfer_type == 2)
                    $type = 'P2P';
                $total += $detail->Amount;
                $baseTotal += $detail->Amount/$detail->Current_Rate * $rate;
                $response[] = [$count++, $detail->TransactionID, $detail->currency->currencyList->currency.' ('.$detail->currency->currencyList->code.')', $detail->Current_Rate, $detail->Amount, $detail->Amount/$detail->Current_Rate * $rate, $detail->CreatedBy, $detail->Remarks, $detail->user->Username, $txt, $type, $detail->created_at->format('jS F Y g:ia')];
            }
        }
        return response()->json(["draw" => intval( $request['draw'] ),
            "recordsTotal"    => intval($partnerAccountDetails->count()),
            "recordsFiltered" => intval($partnerAccountDetails->count()),
            "data"            => $response,
            "total"           => $total,
            "baseTotal"       => $baseTotal
        ], 200);
    }

    /*=====  End of Section for Partner Transaction  ======*/

}
