<?php

namespace App\Http\Controllers\SuperAdminControllers;

use App\Http\Controllers\Controller;
use App\RawCategory;
use App\RawItems;
use DB;
use GeneralFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Validator;
use View;

class RawItemsController extends Controller
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
        $data['title']           = 'Raw Items Form';
        $data['category_record'] = RawCategory::get()->toArray();
        if ($req && $req->id != '') {
            // Decrypt Url Parameter
            $requestId = Crypt::decryptString($req->id);
            $getRecord = RawItems::with('raw_category')->where('id', $requestId)->first();
            if ($getRecord) {
                $data['raw_item_details'] = $getRecord->toArray();
            }
        }
        return view('super_admin_portal.layouts.raw_items.form', $data);
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
            'product_name' => 'required|max:255',
            'category'     => 'required',
        ];
        $rules = [
            'product_name.required' => 'Product Name is required',
            'product_name.max'      => 'Product Name should not be more than 255 characters',
            'category.required'     => 'Category is required',
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
            $record         = ['product_name' => $req->input('product_name'), 'category' => $req->input('category')];
            $checkExistance = RawItems::where('product_name', $req->input('product_name'))->get();
            if ($req && $req->id != '') {
                // Decrypt Url Parameter
                $requestId      = Crypt::decryptString($req->id);
                $checkExistance = RawItems::where('product_name', $req->input('product_name'))->where('id', '!=', $requestId)->get();
            }
            if (count($checkExistance->toArray()) > 0) {
                DB::commit();
                return back()->with('error_msg', 'Record Already Exist.');
            }
            if ($req && $req->id != '') {
                $requestId    = Crypt::decryptString($req->id);
                $updateRecord = RawItems::where('id', $requestId)->update($record);
                DB::commit();
                return back()->with('status', 'Record has been updated successfully');
            }
            RawItems::create($record);
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
        $data['title']  = 'Raw Items List';
        $data['record'] = RawItems::with('raw_category')->get();
        $data['record'] = $data['record']->toArray();
        return view('super_admin_portal.layouts.raw_items.list', $data);
    }

    /**
     *
     * Delete Function
     *
     */

    public function delete(Request $req)
    {
        try {
            $deleteRecord = RawItems::find($req->input('record_uuid'))->delete();
            return back()->with('status', 'Record has been deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error_msg', 'Record is referenced in other record');
        }
    }
}
