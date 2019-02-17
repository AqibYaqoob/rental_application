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

    /**
     *
     * Update Device Token on Refresh of the Device Token
     *
     */
    public function update_device_token(Request $req)
    {
        $validationArray = [
            'device_token' => 'required',
            'user_id'      => 'required',
        ];
        $rules = [
            'device_token.required' => 299,
            'user_id.required'      => 234,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }

        // Update Device Token for Given User Details
        $updateUserRecord = User::where('id', $req->user_id)->update(['device_token' => $req->device_token]);
        return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => null]);
    }

}
