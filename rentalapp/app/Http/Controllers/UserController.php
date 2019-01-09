<?php

namespace App\Http\Controllers;

use App\Cities;
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
        ];

        $messages = [
            'email.required'    => 212,
            'password.required' => 216,
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        $errors = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => 'false', 'data' => $errors, 'code' => 400]);
        }

        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
        ];
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['status' => false, 'code' => 207]);
            }
        } catch (JWTException $e) {
            return response()->json(['status' => false, 'code' => 208]);
        }
        $userRecord = User::where('email', $request->email)->first();
        $data       = ['user' => $userRecord->toArray(), 'token' => $token];
        $status     = true;
        return response()->json(compact('data', 'status'));
    }

    public function register(Request $request)
    {
        $rules = [
            'user_name' => 'required|string|max:255',
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:6|confirmed',
            'user_type' => 'required',
            'package'   => 'required',
        ];

        $messages = [
            'name.required'      => 209,
            'name.string'        => 210,
            'name.max'           => 211,
            'email.required'     => 212,
            'email.unique'       => 213,
            'email.email'        => 214,
            'email.string'       => 215,
            'password.required'  => 216,
            'password.string'    => 217,
            'password.min'       => 218,
            'password.confirmed' => 219,
            'user_type.required' => 220,
            'package.required'   => 234,
            'user_name.required' => 209,
            'user_name.string'   => 210,
            'user_name.max'      => 211,
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        $errors = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => 'false', 'data' => $errors, 'code' => 400]);
        }

        $user = User::create([
            'name'      => $request->get('name'),
            'email'     => $request->get('email'),
            'password'  => Hash::make($request->get('password')),
            'user_type' => $request->get('user_type'),
            'TenantId'  => 1,
            'Username'  => $request->get('user_name'),
            'Roles'     => 4,
        ]);

        // Save Package Details
        $addPackageDetails = UserPackages::create(['user_id' => $user->id, 'package_id' => $request->package]);

        $token  = JWTAuth::fromUser($user);
        $data   = ['user' => $user, 'token' => $token];
        $status = true;
        return response()->json(compact('data', 'status'));
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
            return response()->json(['status' => 'false', 'data' => $errors, 'code' => 400]);
        }
        // check if the Email exist in the System
        $checkEmailExist = User::where('email', $req->email)->first();
        if (!$checkEmailExist) {
            return response()->json(['status' => false, 'code' => 221]);
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
        return response()->json(['status' => true]);
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
        return response()->json(['status' => true, 'data' => $record->toArray()]);
    }

}
