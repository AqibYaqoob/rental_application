<?php

namespace App\Http\Controllers\AdminControllers;

use App\Currency;
use App\DailyInterest;
use App\PartnerAccountDetails;
use App\PartnerProfitLoss;
use App\PartnerSetting;
use App\TenantAccountDetails;
use App\TenantProfitLoss;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class AjaxController extends Controller
{
    public function individualProfitLoss(Request $request)
    {
        $data = PartnerProfitLoss::with('sitesAccount', 'user')->where('partnerId', Crypt::decryptString($request->id))->offset($request->start)->limit($request->length)->get();
        $response = [];
        $count = 1;
        if (!empty($data->toArray()))
        {
            foreach ($data as $value)
            {
                $response[] = [$count++, $value->user->Username, $value->sitesAccount->SiteAccountCode, $value->TransactionDate, $value->TotalProfitLoss, $value->created_at->format('jS F Y g:ia')];
            }
            $totalProfitLoss = $data->sum('TotalProfitLoss');
        }
        return response()->json(["draw" => intval( $request['draw'] ),
            "recordsTotal"    => intval($data->count()),
            "recordsFiltered" => intval($data->count()),
            "data"            => $response,
            'totalProfitLoss' => $totalProfitLoss ?? 0.00
        ], 200);
    }
    public function individualAccountDetail(Request $request)
    {
        $user = User::find(Crypt::decryptString($request->id));
        if(!is_null($user))
        {
            $partnerBaseCurrencyRate = PartnerSetting::where([['settingName', 'base_currency'], ['partner_id', $user->id]])->first();
            if (!is_null($partnerBaseCurrencyRate))
            {
                $rate = Currency::find($partnerBaseCurrencyRate->value)->CurrentRate;
            }
            $partnerAccountDetails = PartnerAccountDetails::with('currency.currencyList')->where('UserId', $user->id)->offset($request->start)->limit($request->length)->get();
            $response = [];
            $count = 1;
            $total = 0;
            $baseTotal = 0;
            if(!empty($partnerAccountDetails))
            {
                foreach ($partnerAccountDetails as $detail)
                {
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
                "data"            => $response
            ], 200);
        }
    }
    public function partnerProfitLossReport(Request $request)
    {
        $data = PartnerProfitLoss::with('user', 'sitesAccount')->offset($request->start)->limit($request->length)->get();
        $response = [];
        $count = 1;
        $total = 0;
        if(!empty($data->toArray()))
        {
            $total = $data->sum('TotalProfitLoss');
            foreach ($data as $value)
            {
                $response[] = [$count++, $value->user->Username, $value->sitesAccount->SiteAccountCode, $value->TransactionDate, $value->TotalProfitLoss, $value->created_at->format('jS F Y g:ia')];
            }
        }
        return response()->json(["draw" => intval( $request['draw'] ),
            "recordsTotal"    => intval($data->count()),
            "recordsFiltered" => intval($data->count()),
            "data"            => $response,
            "total"           => $total
        ], 200);
    }
    public function ajaxPartnerAccountDetail(Request $request)
    {
        $partnerAccountDetails = PartnerAccountDetails::with('currency', 'user', 'currency.currencyList')->offset($request->start)->limit($request->length)->get();
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
    public function ajaxTenantProfitloss(Request $request)
    {
        $tenantProfitLoss = TenantProfitLoss::with('user', 'sitesAccount')->where('TenantId', Auth::user()->TenantId)->offset($request->start)->limit($request->length)->get();
        $response = [];
        $count = 1;
        $total = 0;
        if(!empty($tenantProfitLoss->toArray()))
        {
            $total = $tenantProfitLoss->sum('TotalProfitLoss');
            foreach ($tenantProfitLoss as $profitLoss)
            {
                $response[] = [$count++, $profitLoss->user->Username, $profitLoss->sitesAccount->SiteAccountCode, $profitLoss->TransactionDate, $profitLoss->TotalProfitLoss, $profitLoss->created_at->format('jS F Y g:ia')];
            }
        }
        return response()->json(["draw" => intval( $request['draw'] ),
            "recordsTotal"    => intval($tenantProfitLoss->count()),
            "recordsFiltered" => intval($tenantProfitLoss->count()),
            "data"            => $response,
            "total"           => $total
        ], 200);
    }
    public function ajaxTenantAccountDetails(Request $request)
    {
        $details = TenantAccountDetails::with('user', 'currency.currencyList')->offset($request->start)->limit($request->length)->get();
        $response = [];
        $count = 1;
        $total = 0;
        $baseTotal = 0;
        if(!empty($details->toArray()))
        {
            foreach ($details as $detail)
            {
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
                $baseCurrency = Currency::where([['Tenant_Id', $detail->user->TenantId], ['isBaseCurrency', 1]])->first();
                $total += $detail->Amount;
                $baseTotal += $detail->Amount/$detail->Current_Rate * $baseCurrency->CurrentRate;
                $response[] = [$count++, $detail->TransactionID, $detail->currency->currencyList->currency.' ('.$detail->currency->currencyList->code.')', $detail->Current_Rate, $detail->Amount, $detail->Amount/$detail->Current_Rate * $baseCurrency->CurrentRate, $detail->CreatedBy, $detail->Remarks, $detail->user->Username, $txt, $type, $detail->created_at->format('jS F Y g:ia')];
            }
        }
        return response()->json(["draw" => intval( $request['draw'] ),
            "recordsTotal"    => intval($details->count()),
            "recordsFiltered" => intval($details->count()),
            "data"            => $response,
            "total"           => $total,
            "baseTotal"       => $baseTotal
        ], 200);
    }
    public function interestReport(Request $request)
    {
        if($request->id ?? false)
        {
            $interests = DailyInterest::with('user')->where([['seen', 0], ['partner_id', Crypt::decryptString($request->id)]])->whereMonth('created_at', Carbon::now()->month)->offset($request->start)->limit($request->length)->get();
        }
        else
        {
            $interests = DailyInterest::with('user')->where('seen', 0)->whereMonth('created_at', Carbon::now()->month)->offset($request->start)->limit($request->length)->get();
        }
        $data = [];
        $count = 1;
        $totalInterest = 0.00;
        if(!empty($interests->toArray()))
        {
            foreach ($interests as $interest)
            {
                $data[] = [$count++, $interest->user->Username, $interest->created_at->format('jS F Y g:ia'), $interest->amount];
            }
            $totalInterest = $interests->sum('amount');
        }
        return response()->json(["draw" => intval( $request['draw'] ),
            "recordsTotal"    => intval($interests->count()),
            "recordsFiltered" => intval($interests->count()),
            "data"            => $data,
            "total"           => $totalInterest
        ], 200);
    }
}
