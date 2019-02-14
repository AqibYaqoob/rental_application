<?php

namespace App\Http\Controllers;

use App\Cities;
use App\ContractorDetails;
use App\User;
use App\UserPackages;
use GeneralFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function authenticate(Request $request)
    {
        $rules = [
            'email'    => 'required',
            'password' => 'required',
            // 'device_id' => 'required',
        ];

        $messages = [
            'email.required'    => 212,
            'password.required' => 216,
            // 'device_id.required' => 268,
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        $errors = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'data' => null, 'errorcode' => [400], 'successcode' => []]);
        }

        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
        ];
        $record = User::where('email', $request->email)->first();
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['status' => false, 'data' => null, 'errorcode' => [207], 'successcode' => []]);
            }
        } catch (JWTException $e) {
            return response()->json(['status' => false, 'data' => null, 'errorcode' => [208], 'successcode' => []]);
        }
        // check if there is user Authenticated already
        $getUser = User::where('email', $request->email)->where('otp_check', 1)->first();
        if (!$getUser) {
            return response()->json(['status' => false, 'data' => ['user' => $record->toArray(), 'token' => null], 'errorcode' => [240], 'successcode' => []]);
        }
        $data        = ['user' => $record->toArray(), 'token' => $token];
        $status      = true;
        $errorcode   = [];
        $successcode = [200];
        // Add Device Id into User Table and Update Device Id with the user details
        $updateUserRecord = User::where('id', $record->id)->update(['device_id' => $request->device_id]);
        return response()->json(compact('data', 'status', 'errorcode', 'successcode'));
    }

    public function register(Request $request)
    {
        $rules = [
            'user_name' => 'required|string|max:255|unique:users',
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:6|confirmed',
            'user_type' => 'required',
        ];

        $messages = [
            'name.required'                    => 209,
            'name.string'                      => 210,
            'name.max'                         => 211,
            'email.required'                   => 212,
            'email.unique'                     => 213,
            'email.email'                      => 214,
            'email.string'                     => 215,
            'password.required'                => 216,
            'password.string'                  => 217,
            'password.min'                     => 218,
            'password.confirmed'               => 219,
            'user_type.required'               => 220,
            'package.required'                 => 250,
            'user_name.required'               => 209,
            'user_name.string'                 => 210,
            'user_name.max'                    => 211,
            'phone_number.required'            => 251,
            'phone_number.string'              => 252,
            'social_security_number.required'  => 253,
            'social_security_number.numeric'   => 254,
            'skills_set.required'              => 255,
            'reference_email_address.required' => 256,
            'reference_phone_number.required'  => 257,
            'reference_phone_number.numeric'   => 258,
            'user_name.unique'                 => 264,
        ];
        // 1) If Landloard is getting register
        if ($request->input('user_type') == 1) {
            $rules['package'] = 'required';
        }
        // 2) If Contractor getting Register Do following operations
        if ($request->input('user_type') == 3) {
            $rules['phone_number']            = 'required|string';
            $rules['social_security_number']  = 'required|numeric';
            $rules['skills_set']              = 'required';
            $rules['reference_email_address'] = 'required';
            $rules['reference_phone_number']  = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        $errors = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'data' => null, 'errorcode' => $errors, 'successcode' => []]);
        }
        $otpCode = mt_rand(10000, 99999);
        $user    = User::create([
            'name'      => $request->get('name'),
            'email'     => $request->get('email'),
            'password'  => Hash::make($request->get('password')),
            'user_type' => $request->get('user_type'),
            'TenantId'  => 1,
            'Username'  => $request->get('user_name'),
            'Roles'     => 4,
            'otp_code'  => $otpCode,
        ]);
        if ($request->input('user_type') == 1) {
            // Save Package Details
            $addPackageDetails = UserPackages::create(['user_id' => $user->id, 'package_id' => $request->package]);
        }

        if ($request->input('user_type') == 3) {
            $contractorDetails = $this->contractorImplementation($request, $user->id);
        }
        // Send Email Varification Code.
        $data = [
            'subject'         => 'Verification',
            'heading_details' => 'Email Verification (OTP Code)',
            'sub_heading'     => 'Secret Code for Verification',
            'heading'         => 'Code is used for verification of the email address and user account',
            'title'           => 'Please add the code below in the OTP Code section to verify yourself. Also please Note that this code will valid till 4 days. After that this code will be expired. So you need to verify the email again.',
            'content'         => "<h3><b>" . $otpCode . "</b></h3>",
            'email'           => $request->input('email'),
        ];
        $sendEmail = GeneralFunctions::sendEmail($data);
        // $token     = JWTAuth::fromUser($user);
        $data        = ['user' => $user];
        $status      = true;
        $errorcode   = [];
        $successcode = [236];
        return response()->json(compact('data', 'status', 'errorcode', 'successcode'));
    }

    public function getAuthenticatedUser()
    {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('user'));
    }
    /**
     *
     * Forget Password
     *
     */
    public function forget_password(Request $req)
    {
        $rules = [
            'email' => 'required',
        ];

        $messages = [
            'email.required' => 212,
        ];
        $validator = Validator::make($req->all(), $rules, $messages);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'data' => null, 'errorcode' => [400], 'successcode' => []]);
        }
        // check if the Email exist in the System
        $checkEmailExist = User::where('email', $req->email)->first();
        if (!$checkEmailExist) {
            return response()->json(['status' => false, 'data' => null, 'errorcode' => [221], 'successcode' => []]);
        }
        // If email address Exist
        // 5) Send Account Registration Confirmation to Super Admin
        $url  = url('/password/change/?email=' . GeneralFunctions::encryptString($req->input('email')));
        $data = [
            'subject'         => 'Forget Password',
            'heading_details' => 'Forget Password',
            'sub_heading'     => 'You Forget ur Password. Please Check below',
            'heading'         => 'Password Change',
            'title'           => 'You Forget your Password. Please clike on the link below to change password to new one...',
            'content'         => "<a href='" . $url . "' class='btn btn-success'> ClickHere </a> ",
            'email'           => $req->input('email'),
        ];
        $sendEmail = GeneralFunctions::sendEmail($data);
        return response()->json(['status' => true, 'data' => null, 'errorcode' => [], 'successcode' => [200]]);
    }

    /**
     *
     * Page Show for the change Password Form
     *
     */
    public function showForgetPasswordPage(Request $req)
    {
        $data['status'] = false;
        if (!$req->email) {
            return view('custom_layouts.layout.forget_password_form', $data);
        }
        // Get User Record
        $emailAddress = Crypt::decryptString($req->email);
        $userDetails  = User::where('email', $emailAddress)->select('id')->get();
        $userDetails  = $userDetails->toArray();
        if (count($userDetails) == 0) {
            return view('custom_layouts.layout.forget_password_form', $data);
        }
        // Return Form With Unique Id
        $data['status'] = true;
        $data['userId'] = $userDetails[0]['id'];
        return view('custom_layouts.layout.forget_password_form', $data);
    }

    /**
     *
     * New Password Save Function
     *
     */
    public function savePassword(Request $req)
    {
        // 1) Validations
        $validationArray = [
            'id'       => 'required',
            'password' => 'required|min:6|confirmed',
        ];
        $customMessages = [
            'password.required'  => 'Password is required',
            'password.min'       => 'Password should be minimum 6 character long',
            'password.confirmed' => 'Password does not match with confirm password',
        ];
        $validator = Validator::make($req->all(), $validationArray, $customMessages);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }
        // Save Record
        $saveRecord = User::where('id', Crypt::decryptString($req->id))->update(['password' => Hash::make($req->password)]);
        return back()->with('success', 'Your Account is updated with new password.');
    }
    /**
     *
     * Get All Cities Record
     *
     */
    public function getCities(Request $req)
    {
        $record = Cities::get();
        return response()->json(['status' => true, 'data' => $record->toArray(), 'errorcode' => [], 'successcode' => [200]]);
    }

    /**
     *
     * Send OTP Code to Verify and Login
     *
     */
    public function verifyUser(Request $req)
    {
        $rules = [
            'otp_code' => 'required|numeric',
            'user_id'  => 'required|numeric',
        ];

        $messages = [
            'otp_code.required' => 237,
            'otp_code.numeric'  => 238,
            'user_id.required'  => 234,
        ];
        $validator = Validator::make($req->all(), $rules, $messages);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'data' => null, 'errorcode' => [400], 'successcode' => []]);
        }
        // Verify User First
        $getUser = User::where('otp_code', $req->otp_code)->where('id', $req->user_id)->first();
        if ($getUser) {
            // Update OTP Check
            User::where('id', $req->user_id)->update(['otp_check' => 1]);
            $token       = JWTAuth::fromUser($getUser);
            $data        = ['user' => $getUser, 'token' => $token];
            $status      = true;
            $errorcode   = [];
            $successcode = [200];
            return response()->json(compact('data', 'status', 'errorcode', 'successcode'));
        } else {
            return response()->json(['status' => false, 'errorcode' => [239], 'successcode' => [], 'data' => null]);
        }
    }

    /**
     *
     * Resend OTP Code again
     *
     */
    public function resendOtpCode(Request $req)
    {
        $rules = [
            'email' => 'required',
            // 'user_id' => 'required|numeric',
        ];

        $messages = [
            'email.required'   => 212,
            'user_id.required' => 234,
        ];
        $validator = Validator::make($req->all(), $rules, $messages);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'data' => null, 'errorcode' => $errors, 'successcode' => []]);
        }
        $otpCode = mt_rand(10000, 99999);
        // Update New Code and Send Email Address.
        User::where('email', $req->email)->update(['otp_code' => $otpCode, 'otp_check' => 0]);
        $user = User::where('email', $req->email)->get()->toArray();

        // Send Email Varification Code.
        $data = [
            'subject'         => 'Verification',
            'heading_details' => 'Email Verification (OTP Code)',
            'sub_heading'     => 'Secret Code for Verification',
            'heading'         => 'Code is used for verification of the email address and user account',
            'title'           => 'Please add the code below in the OTP Code section to verify yourself. Also please Note that this code will valid till 4 days. After that this code will be expired. So you need to verify the email again.',
            'content'         => "<h3><b>" . $otpCode . "</b></h3>",
            'email'           => $req->input('email'),
        ];
        $sendEmail   = GeneralFunctions::sendEmail($data);
        $data        = null;
        $status      = true;
        $errorcode   = [];
        $successcode = [236];
        return response()->json(compact('data', 'status', 'errorcode', 'successcode'));

    }

    // Implementation of the Contractor Details
    public function contractorImplementation($parameters, $userRecord)
    {
        // 1) Add Contractor Remaining Details
        $contractorDetails = [
            'reference_email'        => $parameters->reference_email_address,
            'reference_phone_number' => $parameters->reference_phone_number,
            'skill_set'              => $parameters->skills_set,
            'social_security_number' => $parameters->social_security_number,
            'driving_licence'        => $parameters->driving_licence,
            'user_id'                => $userRecord,
        ];

        $saveContractorDetailRecord = ContractorDetails::create($contractorDetails);
        return $saveContractorDetailRecord;
    }

    /**
     *
     * Profile Edit
     *
     */
    public function profileEdit(Request $request)
    {
        $rules = [
            'user_id'   => 'required',
            'user_name' => 'required|string|max:255|unique:users,user_name,' . $request->user_id,
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users,email,' . $request->user_id,
            'user_type' => 'required',
        ];

        $messages = [
            'name.required'                    => 209,
            'name.string'                      => 210,
            'name.max'                         => 211,
            'email.required'                   => 212,
            'email.unique'                     => 213,
            'email.email'                      => 214,
            'email.string'                     => 215,
            'user_name.required'               => 209,
            'user_name.string'                 => 210,
            'user_name.max'                    => 211,
            'phone_number.required'            => 251,
            'phone_number.string'              => 252,
            'social_security_number.required'  => 253,
            'social_security_number.numeric'   => 254,
            'skills_set.required'              => 255,
            'reference_email_address.required' => 256,
            'reference_phone_number.required'  => 257,
            'reference_phone_number.numeric'   => 258,
            'user_name.unique'                 => 264,
            'user_id.required'                 => 234,
            'user_type.required'               => 220,
        ];
        // 1) If Contractor getting Register Do following operations
        if ($request->input('user_type') == 3) {
            $rules['phone_number']            = 'required|string';
            $rules['social_security_number']  = 'required|numeric';
            $rules['skills_set']              = 'required';
            $rules['reference_email_address'] = 'required';
            $rules['reference_phone_number']  = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        $errors = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'data' => null, 'errorcode' => $errors, 'successcode' => []]);
        }

        // Update Record
        $userRecord = User::where('id', $request->user_id)->update([
            'name'     => $request->get('name'),
            'email'    => $request->get('email'),
            'Username' => $request->get('user_name'),
        ]);

        if ($request->input('user_type') == 3) {
            $contractorDetails = [
                'reference_email'        => $request->reference_email_address,
                'reference_phone_number' => $request->reference_phone_number,
                'skill_set'              => $request->skills_set,
                'social_security_number' => $request->social_security_number,
                'driving_licence'        => $request->driving_licence,
            ];

            $saveContractorDetailRecord = ContractorDetails::where('user_id', $request->user_id)->update($contractorDetails);
        }

        return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => null]);
    }

    /**
     *
     * User Detail Screen
     *
     */
    public function user_detail_screen(Request $req)
    {
        $userDetail = User::where('id', Crypt::decryptString($req->id))->get()->toArray();
        dd($userDetail);
    }

    /**
     *
     * Change Password
     *
     */
    public function change_password(Request $req)
    {
        $rules = [
            'old_password' => 'required',
            'password'     => 'required|string|min:6|confirmed',
            'user_id'      => 'required',
        ];

        $messages = [
            'password.required'     => 216,
            'password.string'       => 217,
            'password.min'          => 218,
            'password.confirmed'    => 219,
            'user_id.required'      => 234,
            'old_password.required' => 265,
        ];
        $validator = Validator::make($req->all(), $rules, $messages);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'data' => null, 'errorcode' => $errors, 'successcode' => []]);
        }

        // Check if the Old Password is right
        $user = User::find($req->user_id);
        if (Hash::check($req->old_password, $user->password)) {
            // Success
            $savePassword = User::where('id', $req->user_id)->update(['password' => Hash::make($req->get('password'))]);
            return response()->json(['status' => true, 'data' => null, 'errorcode' => [], 'successcode' => [200]]);
        } else {
            return response()->json(['status' => false, 'data' => null, 'errorcode' => [266], 'successcode' => []]);
        }
    }

    public function test_screen(Request $req)
    {
        return view('super_admin_portal.layouts.test_folder.test');
    }

    public function payment_process(Request $req)
    {
        dd($req->all());
    }
}
