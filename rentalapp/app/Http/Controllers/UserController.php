<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['status' => false, 'code' => 207], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['status' => false, 'code' => 208], 400);
        }
        $status = true;
        return response()->json(compact('token', 'status'), 200);
    }

    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'user_type' => 'required',
        ];

        $messages = [
            'name.required' => 209,
            'name.string' => 210,
            'name.max' => 211,
            'email.required' => 212,
            'email.unique' => 213,
            'email.email' => 214,
            'email.string' => 215,
            'password.required' => 216,
            'password.string' => 217,
            'password.min' => 218,
            'password.confirmed' => 219,
            'user_type.required' => 220,
        ];

        $validator = Validator::make($request->all(), $rules, $messages);    

        if($validator->fails()){
                return response()->json(['status' => false, $validator->errors()], 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'user_type' => $request->get('user_type')
        ]);

        $token = JWTAuth::fromUser($user);
        $status =true; 
        return response()->json(compact('user','token', 'status'),200);
    }

    public function getAuthenticatedUser()
        {
                try {

                        if (! $user = JWTAuth::parseToken()->authenticate()) {
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
}