<?php

namespace App\Http\Controllers;

use App\PaymentOptions;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    /**
     *
     * Show Payment Options List (Json Record)
     *
     */
    public function payment_list(Request $request)
    {
        $getPaymentDetails = PaymentOptions::get()->toArray();
        return response()->json(['status' => true, 'data' => $getPaymentDetails]);
    }

}
