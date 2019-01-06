<?php

namespace App\Http\Controllers;

use App\Role;
use App\StaffMember;
use App\Tenants;
use App\User;
use Auth;
use GeneralFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Validator;

class StaffController extends Controller
{
    public function staff_form(Request $request)
    {

        $data           = [];
        $data['title']  = "Add Staff Member";
        $data['roles']  = [];
        $data['record'] = [];
        if ($request && $request->id != '') {
            // Decrypt Url Parameter
            $requestId = Crypt::decryptString($request->id);
            $getRecord = User::with('staff_members')->where('id', $requestId)->first();
            if ($getRecord) {
                $data['record'] = $getRecord->toArray();
            }
        }
        $data['roles'] = Role::where('owner_id', Auth::user()->id)->get();
        $data['roles'] = $data['roles']->toArray();
        return view('layouts.staff.staff_form', $data);
    }

    public function staff_validation(Request $request)
    {
        $validationArray = [
            'name'         => 'required|max:255',
            'phone_number' => 'required',
            'email'        => 'required',
            'role'         => 'required',
        ];
        $validator = Validator::make($request->all(), $validationArray);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => 'error', 'msg_data' => $errors]);
        }
        // GeneralFunctions::ajax_debug();
        return response()->json(['status' => 'success', 'data' => $request->all()]);
    }

    public function staff_insert(Request $request)
    {

        // Adding Temporary Password for the staff
        $uniqueId = substr(uniqid(rand(), true), 2, 2);
        $password = $request->input('email') . $uniqueId;

        if ($request && $request->id != '') {
            // Decrypt Url Parameter
            $requestId    = Crypt::decryptString($request->id);
            $userInstance = User::find($requestId);

            // Attaching Roles and User
            $updateUserData  = User::where('id', $requestId)->update(['EmailAddress' => $request->email, 'Username' => $request->email]);
            $updateStaffData = StaffMember::where('user_id', $requestId)->update(['mobile_number' => $request->phone_number, 'home_number' => $request->home_number, 'staff_name' => $request->name]);

            $userInstance->role()->detach();
            $userInstance->role()->attach([$request->input('role')]);
            return back()->with('status', 'Record has been saved successfully');
        }

        // Adding User Data
        // Check if User Already Exist
        $userCheck = User::where('EmailAddress', $request->email)->where('TenantId', Auth::user()->TenantId)->get();
        if (count($userCheck) > 0) {
            return back()->withErrors(['User Already Exist'])->withInput();
        }
        $userData = [
            'Username'      => $request->input('email'),
            'password'      => Hash::make($password),
            'Roles'         => Auth::user()->Roles,
            'TenantId'      => Auth::user()->TenantId,
            'CreatedBy'     => Auth::user()->Username,
            'AccountStatus' => 1,
            'EmailAddress'  => $request->input('email'),
        ];

        $userDetails = $userSave = User::create($userData);

        // Save record in Staff Detail Screen
        $staffDetail = [
            'staff_name'    => $request->input('name'),
            'mobile_number' => $request->input('phone_number'),
            'home_number'   => $request->input('home_number'),
            'role_id'       => $request->input('role'),
            'user_id'       => $userSave->id,
        ];

        $getCompanyDetails = Tenants::where('id', Auth::user()->TenantId)->first();
        $company           = 'Green Power Administration Team';
        if ($getCompanyDetails) {
            $company = $getCompanyDetails->TenantName;
        }

        StaffMember::create($staffDetail);
        $userInstance = User::find($userDetails->id);

        // Attaching Roles and User
        $userInstance->role()->attach([$request->input('role')]);
        $data = [
            'subject'         => 'New Account',
            'heading_details' => 'Welcome New Staff Member ' . $request->input('name'),
            'sub_heading'     => 'Account has Been Created by the Company Owner',
            'heading'         => 'Green Power',
            'title'           => 'New Member',
            'content'         => 'Congratulationss! for becoming part of <u>' . $company . '</u> u have been a new member. You can login from portal to strat the tasks. <br><b>User Name : ' . $request->input('email') . '</b><br> <b>Password : </b>' . $password,
            'email'           => $request->input('email'),
        ];
        GeneralFunctions::sendEmail($data);
        return back()->with('status', 'Record has been saved successfully');
    }

    public function staff_list()
    {
        $data          = [];
        $data['title'] = "Staff Member List";
        $data['staff'] = [];
        $data['staff'] = User::with('staff_members.user_roles')->where('Roles', Auth::user()->Roles)->where('TenantId', Auth::user()->TenantId)->where('IsAdmin', 0)->get();
        if (isset($data['staff'])) {
            $data['staff'] = $data['staff']->toArray();
        }
        return view('layouts.staff.staff_list', $data);
    }

    public function update_staff_status(Request $req)
    {
        $getRequestStatus = $req->input('account_status');
        $user_uuid        = $req->input('user_id');

        $reason          = null;
        $getUserDetails  = User::where('id', $user_uuid)->first();
        $subject         = 'Your Account has been Activated';
        $heading_details = '';

        if ($getRequestStatus == 2) {
            $reason  = $req->input('reason');
            $subject = 'Your Account has been Deactivated by Company';
        }

        $updateUserStatus = User::find($user_uuid)->update(['status' => $getRequestStatus]);
        $data             = [
            'subject'         => $subject,
            'heading'         => 'Betting Form',
            'sub_heading'     => 'User Account Details',
            'heading_details' => $heading_details,
            'job_title'       => '',
            'content'         => $reason,
            'email'           => $getUserDetails->email,
        ];
        GeneralFunctions::sendEmail($data);
        return back()->with('status', 'Account has been updated with status');
    }

    public function delete_staff(Request $req)
    {
        try {
            $deleteRecord = User::find($req->input('record_uuid'))->delete();
            return back()->with('status', 'Record has been deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error_msg', 'Record is referenced in other record');
        }
    }

}
