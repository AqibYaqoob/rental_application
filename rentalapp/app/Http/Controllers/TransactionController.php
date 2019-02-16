<?php

namespace App\Http\Controllers;

use Braintree_ClientToken;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     *
     * Get Client Token (Generate Client Token)
     *
     */
    public function generate_client_token(Request $request)
    {
        $getClientToken = Braintree_ClientToken::generate();

        return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $getClientToken]);
    }
    /**
     *
     * Payment Process Transaction
     *
     */
    public function payment_process(Request $req)
    {
        try {
            // $payload = $request->input('payload', false);
            $nonce = $req->nonce;

            $status = Braintree_Transaction::sale([
                'amount'             => '10.00',
                'paymentMethodNonce' => $nonce,
                'options'            => [
                    'submitForSettlement' => true,
                ],
            ]);

            return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $status]);
        } catch (Exception $e) {
            return response()->json(['status' => true, 'errorcode' => [$e->getMessage()], 'successcode' => [], 'data' => null]);
        }
    }
}
