<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Permission;
use App\Role;
use App\Screens;
use App\User;
use Auth;
use GeneralFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Validator;
use View;

class UserRolesController extends Controller
{
    private $paypalObject = '';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->paypalObject = new ExpressCheckout;
    }

    /*==============================================
    =            Section Roles Function            =
    ==============================================*/
    /**
     *
     * Block Show Roles List Function
     *
     */
    public function show_roles_list(Request $req)
    {
        // 1) Get User Roles List from the Role table
        $data['title'] = 'Roles List';
        $data['roles'] = Role::where('owner_id', Auth::user()->id)->get();
        $data['roles'] = $data['roles']->toArray();

        return view('layouts.roles.roles_list', $data);
    }

    /**
     *
     * Block Show Roles Form Function
     *
     */
    public function show_roles_form(Request $request)
    {
        $data['roles_details'] = [];
        $data['title']         = "Add User Role";
        $data['role_info']     = [];
        if ($request && $request->id != '') {
            // Decrypt Url Parameter
            $requestId    = Crypt::decryptString($request->id);
            $getRolesData = Role::with('permissions')->where('id', $requestId)->first();
            if ($getRolesData) {
                $data['role_info']     = $getRolesData->toArray();
                $data['roles_details'] = $this->assemble_permission_array($getRolesData->toArray());
            }
        }

        $data['screens'] = Screens::where('association_id', Auth::user()->Roles)->get();
        $data['screens'] = $data['screens']->toArray();
        return view('layouts.roles.roles_form', $data);
    }

    /**
     *
     * Block Saving Roles with permissions
     *
     */
    public function roles_permission_save(Request $req)
    {
        // Adding Roles in the Database Table

        if ($req && $req->id != '') {
            // Decrypt Url Parameter
            $requestId      = Crypt::decryptString($req->id);
            $updateRoleData = Role::find($requestId)->update(['name' => $req->name, 'description' => $req->description]);
            $rolesInstance  = Role::find($requestId);

            // First Detach All the Permissions.
            $rolesInstance->permissions()->detach();
            // dd($req->input('permissions'));
            if (isset($req->permissions)) {
                foreach ($req->input('permissions') as $key => $value) {
                    $getScreenId = Screens::where('code', $key)->first();
                    foreach ($value as $innerKey => $valueKey) {
                        $getPermission = Permission::where('name', $innerKey)->first();
                        $rolesInstance->permissions()->attach([$getPermission->id => ['screen_id' => $getScreenId->id]]);
                    }
                }
            }
            return back()->with('status', 'Record has been updated successfully');
        }

        $checkRoleExistAlready = Role::where('owner_id', Auth::user()->id)->where('name', $req->input('name'))->get();
        if (count($checkRoleExistAlready) > 0) {
            return back()->with('errors', ['Role Name Already Exist. Please Change it to new Name']);
        }
        $owner               = new Role();
        $owner->name         = $req->input('name');
        $owner->display_name = $req->input('name'); // optional
        $owner->description  = $req->input('description'); // optional
        $owner->owner_id     = Auth::user()->id;
        $owner->save();
        if (isset($req->permissions)) {
            foreach ($req->input('permissions') as $key => $value) {
                $getScreenId = Screens::where('code', $key)->first();
                foreach ($value as $innerKey => $valueKey) {
                    $getPermission = Permission::where('name', $innerKey)->first();
                    $owner->permissions()->attach([$getPermission->id => ['screen_id' => $getScreenId->id]]);
                }
            }
        }

        return back()->with('status', 'Record has been saved successfully');
    }

    /**
     *
     * Block Validation for the Roles Form Using Agax request
     *
     */
    public function roles_permission_validation(Request $request)
    {
        // GeneralFunctions::ajax_debug($request->all());
        $validationArray = [
            'name'        => 'required|max:255',
            'description' => 'required',
        ];
        $validator = Validator::make($request->all(), $validationArray);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => 'error', 'msg_data' => $errors]);
        }
        // GeneralFunctions::ajax_debug();
        return response()->json(['status' => 'success', 'data' => $request->all()]);
    }

    public function assemble_permission_array(array $data)
    {
        $assembleArray = [];
        $permissions   = [1 => 'add', 2 => 'edit', 3 => 'delete', 4 => 'view'];
        foreach ($data['permissions'] as $key => $value) {
            $getScreenCode                                           = Screens::where('id', $value['screen_id'])->first();
            $getPermissionCode                                       = $permissions[$value['pivot']['permission_id']];
            $assembleArray[$getScreenCode->code][$getPermissionCode] = true;
        }

        return $assembleArray;
    }

    public function delete_role(Request $req)
    {
        try
        {
            $deleteRecord = Role::with('permissions')->find($req->input('record_uuid'));
            if (!is_null($deleteRecord)) {
                if ($deleteRecord->permissions->count() > 0) {
                    return redirect()->back()->with('error_msg', 'Role cannot be delete because some of its information is still being used');
                } else {
                    $deleteRecord->delete();
                    return back()->with('status', 'Record has been deleted successfully');
                }
            }
        } catch (\Exception $e) {
            return back()->with('error_msg', 'Record is referenced in other record');
        }
    }
    /*=====  End of Section Roles List  ======*/

}
