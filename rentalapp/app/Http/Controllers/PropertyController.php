<?php

namespace App\Http\Controllers;

use App\Properties;
use App\PropertiesUtility;
use App\PropertyFiles;
use App\PropertyScheduling;
use GeneralFunctions;
use Illuminate\Http\Request;
use Validator;

class PropertyController extends Controller
{
    /**
     *
     * Add New Property Details
     *
     */
    public function add_property(Request $req)
    {
        $validationArray = [
            'description' => 'required',
            'address'     => 'required',
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
            'zipcode'     => 'required|numeric',
            'city'        => 'required',
            'main_image'  => 'required|base64',
            'user_id'     => 'required',
        ];
        $rules = [
            'description.required'   => 222,
            'address.required'       => 223,
            'latitude.required'      => 224,
            'latitude.numeric'       => 225,
            'longitude.required'     => 226,
            'longitude.numeric'      => 227,
            'zipcode.required'       => 228,
            'zipcode.numeric'        => 229,
            'city.required'          => 230,
            'main_image.required'    => 231,
            'main_image.base64'      => 232,
            'main_image.base64image' => 233,
            'user_id.required'       => 234,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }
        // 1) Add Property Details First
        $propertDetail = [
            'description' => $req->description,
            'address'     => $req->address,
            'latitude'    => $req->latitude,
            'longitutde'  => $req->longitutde,
            'zipcode'     => $req->zipcode,
            'city'        => $req->city,
            'user_id'     => $req->user_id,
        ];
        $saveProperty = Properties::create($propertDetail);
        // 2) Properties Utilities
        $propertyUtilities = [
            'property_id' => $saveProperty->id,
            'electric'    => ($req->electric != null) ? $req->electric : 0,
            'gas'         => ($req->gas != null) ? $req->gas : 0,
            'water'       => ($req->water != null) ? $req->water : 0,
            'trash'       => ($req->trash != null) ? $req->trash : 0,
        ];
        $savePropertyUtilities = PropertiesUtility::create($propertyUtilities);
        // 3) Upload Images of the Property
        $uploadMainFile = GeneralFunctions::uploadFileUsingBase64($req->input('main_image'));
        // Save File in the Table
        $savePropertyImagesRecord            = [];
        $keyValue                            = 0;
        $savePropertyImagesRecord[$keyValue] = [
            'file_name'   => $uploadMainFile['file_name'],
            'file_path'   => $uploadMainFile['url'],
            'property_id' => $saveProperty->id,
            'main_img'    => 1,
            'created_at'  => Date('Y-m-d'),
            'updated_at'  => Date('Y-m-d'),
        ];

        if (count($req->input('other_images')) > 0) {
            foreach ($req->input('other_images') as $key => $value) {
                $uploadMainFile                      = GeneralFunctions::uploadFileUsingBase64($value);
                $keyValue                            = $keyValue + 1;
                $savePropertyImagesRecord[$keyValue] = [
                    'file_name'   => $uploadMainFile['file_name'],
                    'file_path'   => $uploadMainFile['url'],
                    'property_id' => $saveProperty->id,
                    'main_img'    => 0,
                    'created_at'  => Date('Y-m-d'),
                    'updated_at'  => Date('Y-m-d'),
                ];
            }
        }
        $savePropertyFileRecord = PropertyFiles::insert($savePropertyImagesRecord);
        return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => null]);
    }

    // Landloard Property Details
    public function get_landloard_properties(Request $req)
    {
        $validationArray = [
            'user_id' => 'required',
        ];
        $rules = [
            'user_id.required' => 234,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }
        // Get Result Record
        $record = Properties::with('properties_utility')->with('properties_files')->where('user_id', $req->user_id)->get();
        $record = $record->toArray();
        if (count($record > 0)) {
            return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $record]);
        } else {
            return response()->json(['status' => false, 'errorcode' => [235], 'successcode' => [], 'data' => null]);
        }
    }

    /**
     *
     * Get All Properties which are not on Rented right now to show to the Applicant
     *
     */
    public function get_all_non_rented_properties(Request $req)
    {
        $record = Properties::with('properties_utility')->with('properties_files')->where('status', 0)->get();
        $record = $record->toArray();
        if (count($record) > 0) {
            return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $record]);
        } else {
            return response()->json(['status' => false, 'errorcode' => [235], 'successcode' => [], 'data' => null]);
        }
    }

    /**
     *
     * Add Scheduling for the Specific Property for Applying booking
     *
     */
    public function addPropertyScheduling(Request $request)
    {
        $validationArray = [
            'property'      => 'required',
            'user_id'       => 'required',
            'scheduling.*.' => 'required|numeric',
        ];
        $rules = [
            'property.required'     => 241,
            'user_id.required'      => 234,
            'scheduling.*.required' => 242,
            'scheduling.*.numeric'  => 243,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }
        // 1) Create An Array to add bulk Insert Record If Record is Valid
        $count  = 0;
        $record = array();
        foreach ($req->scheduling as $key => $value) {
            $record[$count]['availibility_date_time'] = $value;
            $record[$count]['property_id']            = $req->property;
            $record[$count]['applicant_id']           = $req->user_id;
            $record[$count]['created_at']             = Date('Y-m-d');
            $record[$count]['updated_at']             = Date('Y-m-d');
            $count++;
        }
        PropertyScheduling::insert($record);
        return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $record]);
    }

    /**
     *
     * Re Schedule Timing for the Property to be shown from Owners End
     *
     */
    public function reScheduleProperty(Request $req)
    {
        $validationArray = [
            'property'            => 'required',
            'user_id'             => 'required',
            'new_scheduling_time' => 'required|numeric',
            'scheduling_id'       => 'required',
            'applicant_id'        => 'required',
        ];
        $rules = [
            'property.required'            => 241,
            'user_id.required'             => 234,
            'new_scheduling_time.required' => 244,
            'new_scheduling_time.numeric'  => 246,
            'scheduling_id.required'       => 245,
            'applicant_id.required'        => 247,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }

        // 1) Get Applicant Email Address from User Details
        $applicantEmailAddress = User::where('id', $req->applicant_id)->first();
        if ($applicantEmailAddress) {
            $emailAddress = $applicantEmailAddress->email;
        } else {
            return response()->json(['status' => false, 'errorcode' => [248], 'successcode' => [], 'data' => null]);
        }
        // 2) Check for the time which is booked first. Against which you wnat to reschedule the time
        $getRecord = PropertyScheduling::with('property_detail')->where('id', $req->scheduling_id)->first();
        if ($getRecord) {
            // Update the New Timing and update Applicant through Notification and Email

            $updateRecord = PropertyScheduling::where('id', $req->scheduling_id)->update(['availibility_date_time' => $req->new_scheduling_time]);
            // Get Applicant Email Address
            $data = [
                'subject'         => 'Rescheduling Showing',
                'heading_details' => 'Showing Rescheduling for Property (' . $getRecord->property_detail->description . ')',
                'sub_heading'     => '',
                'heading'         => 'New Time for Showing Property',
                'title'           => 'Unfortunately the previous time was changed by the owner and it is to inform to visit site on the given time as it is booked as a new timming',
                'content'         => "<h3><b>" . GeneralFunctions::convertUnixToReadableFormat($req->new_scheduling_time) . "</b></h3>",
                'email'           => $req->input('email'),
            ];
            $sendEmail = GeneralFunctions::sendEmail($data);
            return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => null]);
        } else {
            return response()->json(['status' => false, 'errorcode' => [246], 'successcode' => [], 'data' => null]);
        }
    }

    /**
     *
     * Show Property Showing timmings that specific applicant applied
     *
     */
    public function showShowingsForSpecificProperty(Request $req)
    {
        $validationArray = [
            'property'     => 'required',
            'user_id'      => 'required',
            'applicant_id' => 'required',
        ];
        $rules = [
            'property.required'     => 241,
            'user_id.required'      => 234,
            'applicant_id.required' => 247,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }

        // Show Property Showing in the Whole record with specific timmings
        $propertyDetail = Property::with('properties_schedulings.applicant' => function ($query) {
            $query->where('status', 1);
        })->where('id', $req->property)->first();
        if ($propertyDetail) {
            $propertyDetail = $propertyDetail->toArray();

        } else {
            return response()->json(['status' => false, 'errorcode' => [249], 'successcode' => [], 'data' => null]);
        }
    }

    /**
     *
     * Delete Scheduling by checking the link and Remove the Showings.
     *
     */
    public function removePropertyShowing(Request $req)
    {
        $validationArray = [
            'property'      => 'required',
            'user_id'       => 'required',
            'scheduling_id' => 'required',
        ];
        $rules = [
            'property.required'     => 241,
            'user_id.required'      => 234,
            'applicant_id.required' => 247,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }

    }

}
