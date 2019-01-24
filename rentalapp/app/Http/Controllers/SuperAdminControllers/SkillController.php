<?php

namespace App\Http\Controllers\SuperAdminControllers;

use App\Http\Controllers\Controller;
use App\SkillSet;
use DB;
use GeneralFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Validator;
use View;

class SkillController extends Controller
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
        $data['title'] = 'Skill Set Form';
        if ($req && $req->id != '') {
            // Decrypt Url Parameter
            $requestId = Crypt::decryptString($req->id);
            $getRecord = SkillSet::where('id', $requestId)->first();
            if ($getRecord) {
                $data['skills_details'] = $getRecord->toArray();
            }
        }
        return view('super_admin_portal.layouts.skill_set.form', $data);
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
            'skill_name'        => 'required|max:255',
            'skill_description' => 'required',
        ];
        $rules = [
            'skill_name.required'        => 'Skill Set Name is required',
            'skill_name.max'             => 'Skill Set Name should not be more than 255 characters',
            'skill_description.required' => 'Skill Set Description is required',
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
            $record         = ['skill_name' => $req->input('skill_name'), 'skill_description' => $req->input('skill_description')];
            $checkExistance = SkillSet::where('skill_name', $req->input('skill_name'))->get();
            if ($req && $req->id != '') {
                // Decrypt Url Parameter
                $requestId      = Crypt::decryptString($req->id);
                $checkExistance = SkillSet::where('skill_name', $req->input('skill_name'))->where('id', '!=', $requestId)->get();
            }
            if (count($checkExistance->toArray()) > 0) {
                DB::commit();
                return back()->with('error_msg', 'Record Already Exist.');
            }
            if ($req && $req->id != '') {
                $requestId    = Crypt::decryptString($req->id);
                $updateRecord = SkillSet::where('id', $requestId)->update($record);
                DB::commit();
                return back()->with('status', 'Record has been updated successfully');
            }
            SkillSet::create($record);
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
        $data['title']  = 'Skill Set List';
        $data['record'] = SkillSet::get();
        $data['record']->toArray();
        return view('super_admin_portal.layouts.skill_set.list', $data);
    }

    /**
     *
     * Delete Function
     *
     */

    public function delete(Request $req)
    {
        try {
            $deleteRecord = SkillSet::find($req->input('record_uuid'))->delete();
            return back()->with('status', 'Record has been deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error_msg', 'Record is referenced in other record');
        }
    }
}
