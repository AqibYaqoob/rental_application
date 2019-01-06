<?php

namespace App\Http\Controllers\SuperAdminControllers;

use App\Http\Controllers\Controller;
use App\Suppliers;
use DB;
use GeneralFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Validator;
use View;

class SupplierController extends Controller
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
        $data['title'] = 'Supplier Form';
        if ($req && $req->id != '') {
            // Decrypt Url Parameter
            $requestId = Crypt::decryptString($req->id);
            $getRecord = Suppliers::where('id', $requestId)->first();
            if ($getRecord) {
                $data['supplier_details'] = $getRecord->toArray();
            }
        }
        return view('super_admin_portal.layouts.suppliers.form', $data);
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
            'name'         => 'required|max:255',
            'phone_number' => 'nullable|numeric|min:1',
        ];
        $rules = [
            'name.required'        => 'Name is required',
            'name.max'             => 'Name should not be more than 255 characters',
            'phone_number.numeric' => 'Phone Number Should be numeric value',
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
            $record         = ['name' => $req->input('name'), 'phone_number' => $req->input('phone_number') ? (int) $req->input('phone_number') : null, 'address' => $req->input('address')];
            $checkExistance = Suppliers::where('name', $req->input('name'))->get();
            if ($req && $req->id != '') {
                // Decrypt Url Parameter
                $requestId      = Crypt::decryptString($req->id);
                $checkExistance = Suppliers::where('name', $req->input('name'))->where('id', '!=', $requestId)->get();
            }
            if (count($checkExistance->toArray()) > 0) {
                DB::commit();
                return back()->with('error_msg', 'Record Already Exist.');
            }
            if ($req && $req->id != '') {
                $requestId    = Crypt::decryptString($req->id);
                $updateRecord = Suppliers::where('id', $requestId)->update($record);
                DB::commit();
                return back()->with('status', 'Record has been updated successfully');
            }
            Suppliers::create($record);
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
        $data['title']  = 'Suppliers List';
        $data['record'] = Suppliers::get();
        $data['record']->toArray();
        return view('super_admin_portal.layouts.suppliers.list', $data);
    }

    /**
     *
     * Delete Function
     *
     */

    public function delete(Request $req)
    {
        try {
            $deleteRecord = Suppliers::find($req->input('record_uuid'))->delete();
            return back()->with('status', 'Record has been deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error_msg', 'Record is referenced in other record');
        }
    }
}
