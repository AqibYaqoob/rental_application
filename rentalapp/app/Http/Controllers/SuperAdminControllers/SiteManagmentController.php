<?php

namespace App\Http\Controllers\SuperAdminControllers;

use App\Http\Controllers\Controller;
use App\Sites;
use GeneralFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Validator;
use View;

class SiteManagmentController extends Controller
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

    public function site_screen(Request $req)
    {
        $data['title'] = 'Add Site';
        if ($req && $req->id != '') {
            // Decrypt Url Parameter
            $requestId = Crypt::decryptString($req->id);
            $getRecord = Sites::where('id', $requestId)->first();
            if ($getRecord) {
                $data['site_details'] = $getRecord->toArray();
            }
        }
        return view('super_admin_portal.layouts.sites.add_site', $data);
    }

    /**
     *
     * Add Site Validations Function
     *
     */

    public function site_validation(Request $req)
    {
        // 1) Validation
        $validationArray = [
            'site_name' => 'required|max:255',
        ];
        $rules = [
            'site_name.required' => 'Site Name is required',
            'site_name.max'      => 'Site Name should not be more than 255 characters',
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

    public function add_site_record(Request $req)
    {
        // 1) Check If Domain Already Exist
        $record         = ['SiteName' => $req->input('site_name'), 'Remarks' => $req->input('remarks')];
        $checkExistance = Sites::where('SiteName', $req->input('site_name'))->get();
        if ($req && $req->id != '') {
            // Decrypt Url Parameter
            $requestId      = Crypt::decryptString($req->id);
            $checkExistance = Sites::where('SiteName', $req->input('site_name'))->where('Id', '!=', $requestId)->get();
        }
        if (count($checkExistance->toArray()) > 0) {
            return back()->with('error_msg', 'Record Already Exist.');
        }
        if ($req && $req->id != '') {
            $requestId    = Crypt::decryptString($req->id);
            $updateRecord = Sites::find($requestId)->update($record);
            return back()->with('status', 'Record has been updated successfully');
        }
        Sites::create($record);
        return back()->with('status', 'Record has been saved successfully');
    }

    /**
     *
     * Show Site List Function
     *
     */

    public function site_list(Request $req)
    {
        $data['title']  = 'Site List';
        $data['record'] = Sites::with('tenants')->get();
        $data['record']->toArray();
        return view('super_admin_portal.layouts.sites.site_list', $data);
    }

    /**
     *
     * Delete Sites Function
     *
     */

    public function delete_site(Request $req)
    {
        try {
            $deleteRecord = Sites::find($req->input('record_uuid'))->delete();
            return back()->with('status', 'Record has been deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error_msg', 'Record is referenced in other record');
        }
    }
}
