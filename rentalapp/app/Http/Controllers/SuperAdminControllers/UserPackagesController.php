<?php

namespace App\Http\Controllers\SuperAdminControllers;

use App\Http\Controllers\Controller;
use App\Packages;
use DB;
use GeneralFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Validator;
use View;

class UserPackagesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     *
     * Screen Display Function
     *
     */

    public function screen_display(Request $req)
    {
        $data['title'] = 'User Package Form';
        if ($req && $req->id != '') {
            // Decrypt Url Parameter
            $requestId = Crypt::decryptString($req->id);
            $getRecord = Packages::where('id', $requestId)->first();
            if ($getRecord) {
                $data['packages_details'] = $getRecord->toArray();
            }
        }
        return view('super_admin_portal.layouts.user_packages.form', $data);
    }

    /**
     *
     * Validations Function
     *
     */

    public function validation(Request $req)
    {
        // 1) Validation
        $validationArray = [
            'package_name'     => 'required|max:255',
            'description'      => 'required',
            'properties_range' => 'required|numeric',
        ];
        $rules = [
            'package_name.required'     => 'Package Name is required',
            'package_name.max'          => 'Package Name should not be more than 255 characters',
            'description.required'      => 'Description is required',
            'properties_range.required' => 'Properties Range is required',
            'properties_range.numeric'  => 'Properties Range should be numeric value',
            // 'phone_number.digits_between' => 'Phone Number Should be between 7 to 20 digits',
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => 'error', 'msg_data' => $errors]);
        }
        return response()->json(['status' => 'success']);
    }

    /**
     *
     * Save Record Function
     *
     */

    public function save(Request $req)
    {
        DB::beginTransaction();

        try {
            // 1) Check If Supplier Already Exist
            $record         = ['package_name' => $req->input('package_name'), 'description' => $req->input('description'), 'properties_range' => $req->input('properties_range')];
            $checkExistance = Packages::where('package_name', $req->input('package_name'))->get();
            if ($req && $req->id != '') {
                // Decrypt Url Parameter
                $requestId      = Crypt::decryptString($req->id);
                $checkExistance = Packages::where('package_name', $req->input('package_name'))->where('id', '!=', $requestId)->get();
            }
            if (count($checkExistance->toArray()) > 0) {
                DB::commit();
                return back()->with('error_msg', 'Record Already Exist.');
            }
            if ($req && $req->id != '') {
                $requestId    = Crypt::decryptString($req->id);
                $updateRecord = Packages::where('id', $requestId)->update($record);
                DB::commit();
                return back()->with('status', 'Record has been updated successfully');
            }
            Packages::create($record);
            DB::commit();
            return back()->with('status', 'Record has been saved successfully');
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            return back()->with('error_msg', 'Something Went Wrong. The erro message is <br> <p>' . $e->getMessage() . '</p>');
        }

    }

    /**
     *
     * Show List Function
     *
     */

    function list(Request $req) {
        $data['title']  = 'Packages List';
        $data['record'] = Packages::get();
        $data['record']->toArray();
        return view('super_admin_portal.layouts.user_packages.list', $data);
    }

    /**
     *
     * Delete Function
     *
     */

    public function delete(Request $req)
    {
        try {
            $deleteRecord = Packages::find($req->input('record_uuid'))->delete();
            return back()->with('status', 'Record has been deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error_msg', 'Record is referenced in other record');
        }
    }
}
