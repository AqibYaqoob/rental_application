<?php

namespace App\Http\Controllers\SuperAdminControllers;

use App\Http\Controllers\Controller;
use App\RawCategory;
use DB;
use GeneralFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Validator;
use View;

class RawItemCategoryController extends Controller
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
     * Add Site Screen Display Function
     *
     */

    public function screen_display(Request $req)
    {
        $data['title'] = 'Raw Items Form';
        if ($req && $req->id != '') {
            // Decrypt Url Parameter
            $requestId = Crypt::decryptString($req->id);
            $getRecord = RawCategory::where('id', $requestId)->first();
            if ($getRecord) {
                $data['category_details'] = $getRecord->toArray();
            }
        }
        return view('super_admin_portal.layouts.raw_category.category_form', $data);
    }

    /**
     *
     * Add Site Validations Function
     *
     */

    public function validation(Request $req)
    {
        // 1) Validation
        $validationArray = [
            'category_name' => 'required|max:255',
        ];
        $rules = [
            'site_name.required' => 'Category Name is required',
            'site_name.max'      => 'Category Name should not be more than 255 characters',
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
     * Add Site Record Function
     *
     */

    public function save(Request $req)
    {
        DB::beginTransaction();

        try {
            // 1) Check If Domain Already Exist
            $record         = ['category_name' => $req->input('category_name')];
            $checkExistance = RawCategory::where('category_name', $req->input('category_name'))->get();
            if ($req && $req->id != '') {
                // Decrypt Url Parameter
                $requestId      = Crypt::decryptString($req->id);
                $checkExistance = RawCategory::where('category_name', $req->input('category_name'))->where('id', '!=', $requestId)->get();
            }
            if (count($checkExistance->toArray()) > 0) {
                DB::commit();
                return back()->with('error_msg', 'Record Already Exist.');
            }
            if ($req && $req->id != '') {
                $requestId    = Crypt::decryptString($req->id);
                $updateRecord = RawCategory::where('id', $requestId)->update($record);
                DB::commit();
                return back()->with('status', 'Record has been updated successfully');
            }
            RawCategory::create($record);
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
     * Show Site List Function
     *
     */

    function list(Request $req) {
        $data['title']  = 'Raw Category List';
        $data['record'] = RawCategory::get();
        $data['record']->toArray();
        return view('super_admin_portal.layouts.raw_category.category_list', $data);
    }

    /**
     *
     * Delete Sites Function
     *
     */

    public function delete(Request $req)
    {
        try {
            $deleteRecord = RawCategory::find($req->input('record_uuid'))->delete();
            return back()->with('status', 'Record has been deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error_msg', 'Record is referenced in other record');
        }
    }
}
