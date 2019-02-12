<?php

namespace App\Http\Controllers;

use App\PaymentOptions;
use GeneralFunctions;
use Illuminate\Http\Request;
use Validator;

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

    /**
     *
     * Testing Push Notification (On Specific Device Number)
     *
     */
    public function push_notification_test(Request $req)
    {
        $validationArray = [
            'device_id' => 'required',
        ];
        $rules = [
            'device_id.required' => 299,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }
        $deviceIds        = [$req->device_id];
        $data             = ['Record Data'];
        $pushNotification = GeneralFunctions::pushNotification($deviceIds, 'Testing Notification', $data, 'Body Head');
        return response()->json(['status' => true, 'data' => $pushNotification]);
    }

}
