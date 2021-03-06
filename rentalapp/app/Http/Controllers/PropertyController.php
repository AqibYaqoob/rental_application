<?php

namespace App\Http\Controllers;

use App\Properties;
use App\PropertiesUtility;
use App\PropertyAddFavourite;
use App\PropertyApplicants;
use App\PropertyFiles;
use App\PropertyRelatedAnswers;
use App\PropertyRelatedQuestions;
use App\PropertyScheduling;
use App\PropertyType;
use App\User;
use DB;
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
            'description'                => 'required',
            'address'                    => 'required',
            'latitude'                   => 'required|numeric',
            'longitude'                  => 'required|numeric',
            'zipcode'                    => 'required|numeric',
            'city'                       => 'required',
            'main_image'                 => 'required|base64',
            'user_id'                    => 'required',
            'property_type'              => 'required',
            'property_related_questions' => 'required',
            'rent'                       => 'required',
        ];
        $rules = [
            'description.required'                => 222,
            'address.required'                    => 223,
            'latitude.required'                   => 224,
            'latitude.numeric'                    => 225,
            'longitude.required'                  => 226,
            'longitude.numeric'                   => 227,
            'zipcode.required'                    => 228,
            'zipcode.numeric'                     => 229,
            'city.required'                       => 230,
            'main_image.required'                 => 231,
            'main_image.base64'                   => 232,
            'property_type.required'              => 258,
            'property_related_questions.required' => 259,
            'user_id.required'                    => 234,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {

            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }

        if ($req->property_related_questions == 1) {
            if (is_array($req->questions)) {
                if (count($req->questions) > 0) {
                    if (count($req->questions) > 4) {
                        return response()->json(['status' => false, 'errorcode' => [262], 'successcode' => [], 'data' => null]);
                    }
                } else {
                    return response()->json(['status' => false, 'errorcode' => [260], 'successcode' => [], 'data' => null]);
                }
            } else {
                return response()->json(['status' => false, 'errorcode' => [261], 'successcode' => [261], 'data' => null]);
            }
        }

        // Check Package Range for Adding the Properties First. If it exceeds than notify user that He/she exceed the limit to add property.
        $getPermissionForAddingNewProperty = GeneralFunctions::checkPackagePropertyRange($req->user_id);
        if (!$getPermissionForAddingNewProperty) {
            return response()->json(['status' => false, 'errorcode' => [300], 'successcode' => [], 'data' => null]);
        }
        // 1) Add Property Details First
        $propertDetail = [
            'description'     => $req->description,
            'address'         => $req->address,
            'latitude'        => $req->latitude,
            'longitutde'      => $req->longitutde,
            'zipcode'         => $req->zipcode,
            'city'            => $req->city,
            'user_id'         => $req->user_id,
            'property_type'   => $req->property_type,
            'application_fee' => $req->application_fee,
            'rent'            => $req->rent,
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
        if (is_array($req->input('other_images'))) {
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
        }
        $savePropertyFileRecord = PropertyFiles::insert($savePropertyImagesRecord);
        if ($req->property_related_questions == 1) {
            // Added Property Related Questions
            $propertyQuestions['property_id'] = $saveProperty->id;
            foreach ($req->questions as $key => $value) {
                $countIndex                                            = (int) $key + 1;
                $propertyQuestions['property_question_' . $countIndex] = $value;
            }
            PropertyRelatedQuestions::create($propertyQuestions);
        }
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
        $record = Properties::with('properties_utility')->with('city_detail')->with('property_type')->with('properties_files')->where('user_id', $req->user_id)->get();
        $record = $record->toArray();
        if (count($record) > 0) {
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
        $record = Properties::with('properties_utility')->with('properties_files')->with('city_detail')->with('property_type')->where('status', 0)->get();
        $record = $record->toArray();
        if (count($record) > 0) {
            return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $record]);
        } else {
            return response()->json(['status' => false, 'errorcode' => [235], 'successcode' => [], 'data' => null]);
        }
    }

    /**
     *
     * Get Specific Property Details
     *
     */
    public function get_specific_property(Request $req)
    {
        $validationArray = [
            'property' => 'required',
        ];
        $rules = [
            'user_id.required'  => 234,
            'property.required' => 241,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }
        $record        = Properties::with('properties_utility')->with('properties_files')->with('city_detail')->with('property_type')->where('id', $req->property)->first();
        $recordDetails = null;
        if ($record) {
            $recordDetails = $record->toArray();
            return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $recordDetails]);
        }
        return response()->json(['status' => false, 'errorcode' => [235], 'successcode' => [], 'data' => null]);
    }

    /**
     *
     * Add Scheduling for the Specific Property for Applying booking
     *
     */
    public function addPropertyScheduling(Request $req)
    {
        try {
            $validationArray = [
                'property'     => 'required',
                'user_id'      => 'required',
                'scheduling'   => 'required',
                'scheduling.*' => 'required|numeric',
            ];
            $rules = [
                'property.required'    => 241,
                'user_id.required'     => 234,
                'scheduling.required'  => 299,
                'scheduling.required'  => 242,
                'scheduling.*.numeric' => 243,
                'answers.required'     => 308,
                'answers.0.required'   => 309,
                'answers.1.required'   => 310,
                'answers.2.required'   => 311,
                'answers.3.required'   => 312,
            ];

            $checkAnyQuestions = PropertyRelatedQuestions::where('property_id', $req->property)->first();
            // Check if Any questions are asked for this property
            $propertyAnswers = [];
            if ($checkAnyQuestions) {
                for ($i = 0; $i < 4; $i++) {
                    $countValue    = $i + 1;
                    $propertyValue = 'property_question_' . $countValue;
                    if ($checkAnyQuestions->{$propertyValue} != null) {
                        $validationArray['answers.' . $i] = 'required';
                    }
                }
                $validationArray['answers'] = 'required';
            }
            $validator = Validator::make($req->all(), $validationArray, $rules);
            $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
            if (count($errors) > 0) {
                return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
            }
            // 1) Create An Array to add bulk Insert Record If Record is Valid
            if (!is_array($req->scheduling)) {
                return response()->json(['status' => false, 'errorcode' => [267], 'successcode' => [], 'data' => null]);
            }
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

            /**
             *
             * Add Answers
             *
             */
            if ($checkAnyQuestions) {
                for ($i = 0; $i < 4; $i++) {
                    $countValue    = $i + 1;
                    $propertyValue = 'property_question_' . $countValue;
                    if ($checkAnyQuestions->{$propertyValue} != null) {
                        $propertyAnswers['answer_' . $countValue] = $req->answers[$i];
                    }
                }
                $propertyAnswers['property_id']  = $req->property;
                $propertyAnswers['applicant_id'] = $req->user_id;
                $saveAnswersRecord               = PropertyRelatedAnswers::create($propertyAnswers);
            }

            $applicantApply = PropertyApplicants::create(['property_id' => $req->property, 'applicant_id' => $req->user_id]);
            $record         = PropertyScheduling::insert($record);
            /*============================================================
            =            Push Notification to specific Device            =
            ============================================================*/
            // Get Device Token of target User Device (Owner of Property)
            $getOwnerUserId       = Properties::select('user_id')->where('id', $req->property)->first();
            $deviceToken          = User::select('device_token')->where('id', $getOwnerUserId->user_id)->first();
            $sendPushNotification = GeneralFunctions::pushNotification($deviceToken->device_token, 'New Booking timming for the property', ['activity_code' => 10001], 'Property Booking');
            if ($sendPushNotification) {
                return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $record]);
            } else {
                return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $record]);
            }
            /*=====  End of Push Notification to specific Device  ======*/
        } catch (Exception $e) {
            return response()->json(['status' => false, 'errorcode' => [$e->getMessage()], 'successcode' => [], 'data' => null]);
        }
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
            'scheduling_id.required'       => 254,
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
        $getRecord = PropertyScheduling::with('applicant')->with('property_detail')->where('id', $req->scheduling_id)->where('status', 1)->first();
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
            return response()->json(['status' => false, 'errorcode' => [257], 'successcode' => [], 'data' => null]);
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
        $propertyDetail = Property::with('properties_schedulings.applicant', function ($query) {
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
            'applicant_id'  => 'required',
        ];
        $rules = [
            'property.required'      => 241,
            'user_id.required'       => 234,
            'scheduling_id.required' => 254,
            'applicant_id.required'  => 247,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }
        try {
            $getUserRecord = PropertyScheduling::with('applicant')->with('property_detail')->where('id', $req->scheduling_id)->first();
            if ($getUserRecord) {
                $deleteProperty = PropertyScheduling::where('property_id', $req->property)->where('applicant_id', $req->applicant_id)->delete();
                /*============================================================
                =            Push Notification to specific Device            =
                ============================================================*/
                // Get Device Token of target User Device (Applicant of Property booked)
                $deviceToken          = User::select('device_token')->where('id', $req->applicant_id)->first();
                $sendPushNotification = GeneralFunctions::pushNotification($deviceToken->device_token, 'Cancel Confirmation for visiting property', ['activity_code' => 10003], 'Cancel Confirmation');
                if ($sendPushNotification) {
                    return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $record]);
                }
                /*=====  End of Push Notification to specific Device  ======*/
                $data = [
                    'subject'         => 'Booking Cancel',
                    'heading_details' => 'Booking for Property (' . $getUserRecord->property_detail->description . ')',
                    'sub_heading'     => '',
                    'heading'         => 'Sorry for Inconvinience',
                    'title'           => 'Unfortunately due some reason your booking has been cancel. We will let you know the new timming soon. Thanks',
                    'content'         => "------------------",
                    'email'           => $getUserRecord->applicant->email,
                ];
                $sendEmail = GeneralFunctions::sendEmail($data);
                return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => null]);
            } else {
                return response()->json(['status' => false, 'errorcode' => [249], 'successcode' => [], 'data' => null]);
            }

        } catch (Exception $e) {
            return response()->json(['status' => false, 'errorcode' => [253], 'successcode' => [], 'data' => null]);
        }
    }

    /**
     *
     * Show Booking details for a specific property
     *
     */
    public function booking_details(Request $req)
    {
        $validationArray = [
            'property' => 'required',
            'user_id'  => 'required',
        ];
        $rules = [
            'property.required' => 241,
            'user_id.required'  => 234,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }

        $getUserRecord = PropertyScheduling::with('applicant')->with('property_detail')->where('property_id', $req->property)->where('status', 1)->first();
        if ($getUserRecord) {
            $getUserRecord = $getUserRecord->toArray();
            return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $getUserRecord]);

        } else {
            return response()->json(['status' => false, 'errorcode' => [235], 'successcode' => [], 'data' => null]);
        }
    }

    /**
     *
     * Add Booking for Specific Property
     *
     */
    public function add_booking_for_specific_property(Request $req)
    {
        $validationArray = [
            'property'      => 'required',
            'user_id'       => 'required',
            'scheduling_id' => 'required',
        ];
        $rules = [
            'property.required'      => 241,
            'user_id.required'       => 234,
            'scheduling_id.required' => 254,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }
        // Check if the booking already exist for the scheduling
        $getRecord = PropertyScheduling::with('applicant')->with('property_detail')->where('id', $req->scheduling_id)->where('status', 1)->first();
        if ($getRecord) {
            return response()->json(['status' => false, 'errorcode' => [235], 'successcode' => [], 'data' => null]);
        }
        $getNewRecord = PropertyScheduling::with('applicant')->with('property_detail')->where('id', $req->scheduling_id)->first();
        if ($getNewRecord)
        // Get Applicant Email Address
        {
            // Add Booking for Showing property
            $updateShowings = PropertyScheduling::where('id', $req->scheduling_id)->update(['status' => 1]);
            /*============================================================
            =            Push Notification to specific Device            =
            ============================================================*/
            // Get Device Token of target User Device (Owner of Property)
            $getApplicantId       = PropertyScheduling::select('applicant_id')->where('id', $req->scheduling_id)->first();
            $deviceToken          = User::select('device_token')->where('id', $getApplicantId->applicant_id)->first();
            $sendPushNotification = GeneralFunctions::pushNotification($deviceToken->device_token, 'Confirmation for visiting property', ['activity_code' => 10002], 'Confirmed Booking');
            if ($sendPushNotification) {
                return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $record]);
            }
            /*=====  End of Push Notification to specific Device  ======*/
            $data = [
                'subject'         => 'Booking for Showing Property',
                'heading_details' => 'Showing Property (' . $getNewRecord->property_detail->description . ')',
                'sub_heading'     => '',
                'heading'         => 'Time for Showing Property',
                'title'           => 'Your timing for showing the property is given below',
                'content'         => "<h3><b>" . GeneralFunctions::convertUnixToReadableFormat($getNewRecord->availibility_date_time) . "</b></h3>",
                'email'           => $getNewRecord->applicant->email,
            ];
            $sendEmail = GeneralFunctions::sendEmail($data);
            return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [256], 'data' => null]);
        } else {
            return response()->json(['status' => false, 'errorcode' => [235], 'successcode' => [], 'data' => null]);
        }

    }

    /**
     *
     * Block : Get Property Type List
     *
     */
    public function get_property_type_list(Request $req)
    {
        $getPropertyTypeDetails = PropertyType::get()->toArray();
        if (count($getPropertyTypeDetails) > 0) {
            return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $getPropertyTypeDetails]);
        } else {
            return response()->json(['status' => false, 'errorcode' => [235], 'successcode' => [], 'data' => null]);
        }
    }

    /**
     *
     * Block comment
     *
     */
    public function property_searching(Request $req)
    {
        $keyword = $req->details;
        $record  = Properties::with('properties_utility')->with('properties_files')->wherehas('city_detail', function ($query) use ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%');
        })->orWhere(function ($query) use ($keyword) {
            $query->where('zipcode', 'like', '%' . $keyword . '%');
        });
        if ($req->property_type) {
            $record = $record->where('property_type', $req->property_type)->get();
        } else {
            $record = $record->get();
        }
        return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $record]);
    }

    /**
     *
     * Add Property to Favourites
     *
     */
    public function add_property_to_favourite(Request $req)
    {
        $validationArray = [
            'property'     => 'required',
            'applicant_id' => 'required',
        ];
        $rules = [
            'property.required'     => 241,
            'applicant_id.required' => 247,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }
        // check if Property is already existed in the Favourites
        $check = PropertyAddFavourite::where('property_id', $req->property)->where('applicant_id', $req->applicant_id)->get()->toArray();
        if (count($check) > 0) {
            return response()->json(['status' => false, 'errorcode' => [263], 'successcode' => [], 'data' => null]);
        }
        $saveRecord = PropertyAddFavourite::create(['property_id' => $req->property, 'applicant_id' => $req->applicant_id]);
        return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => null]);
    }

    /**
     *
     * Show List of Favourite Properties for specific applicant
     *
     */
    public function show_favourite_property(Request $req)
    {
        $validationArray = [
            'applicant_id' => 'required',
        ];
        $rules = [
            'applicant_id.required' => 247,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }
        $getAllFavouriteProperties = PropertyAddFavourite::with('properties_detail.properties_utility', 'properties_detail.properties_files', 'properties_detail.properties_schedulings', 'properties_detail.property_type', 'properties_detail.city_detail')->where('applicant_id', $req->applicant_id)->get()->toArray();
        if (count($getAllFavouriteProperties) > 0) {
            return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $getAllFavouriteProperties]);
        } else {
            return response()->json(['status' => false, 'errorcode' => [235], 'successcode' => [], 'data' => null]);
        }

    }

    /**
     *
     * Remove Favourite Property
     *
     */
    public function remove_favourite_property(Request $req)
    {
        $validationArray = [
            'property'     => 'required',
            'applicant_id' => 'required',
        ];
        $rules = [
            'property.required'     => 241,
            'applicant_id.required' => 247,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }
        // check if Property is already existed in the Favourites
        $check = PropertyAddFavourite::where('property_id', $req->property)->where('applicant_id', $req->applicant_id)->get()->toArray();
        if (count($check) > 0) {
            $delete = PropertyAddFavourite::where('property_id', $req->property)->where('applicant_id', $req->applicant_id)->delete();
            return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => null]);
        }
        return response()->json(['status' => false, 'errorcode' => [235], 'successcode' => [], 'data' => null]);
    }

    /**
     *
     * Apply for Property
     *
     */
    public function apply_for_property(Request $req)
    {
        $validationArray = [
            'property'     => 'required',
            'applicant_id' => 'required',
        ];
        $rules = [
            'property.required'     => 241,
            'applicant_id.required' => 247,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }

    }

    /**
     *
     * Show Non Rented Property of specific Owners
     *
     */
    public function show_rented_property_for_owner(Request $req)
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
        $record = Properties::with('properties_utility')->with('properties_files')->with('city_detail')->with('property_type')->where('status', 0)->where('user_id', $req->user_id)->get();
        $record = $record->toArray();
        if (count($record) > 0) {
            return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $record]);
        } else {
            return response()->json(['status' => false, 'errorcode' => [235], 'successcode' => [], 'data' => null]);
        }
    }

    /**
     *
     * Pending Booking Details for Specific Property
     *
     */
    public function pending_booking_details(Request $req)
    {
        $validationArray = [
            'property' => 'required',
            'user_id'  => 'required',
        ];
        $rules = [
            'property.required' => 241,
            'user_id.required'  => 234,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }
        // Get Properties which are already booked

        $getApplicants = User::whereHas('property_scheduling', function ($query) {
            $query->where('status', 1);
        })->get();
        // dd($getApplicants->toArray());
        // $getApplicants = PropertyScheduling::where('property_id', $req->property)->where('status', 1)->get();
        $getApplicants = $getApplicants->toArray();
        $record        = [];
        if (count($getApplicants) > 0) {
            foreach ($getApplicants as $key => $value) {
                array_push($record, $value['id']);
            }
            // Show Pending Booking Details which are applied from applicant side

            $pendingBookings = User::with('property_scheduling.property_detail')->whereHas('property_scheduling', function ($query) use ($record, $req) {
                $query->where('property_id', $req->property);
                $query->whereNotIn('applicant_id', $record);
            })->get();

            // $pendingBookings = PropertyScheduling::with('applicant')->with('property_detail')->where('property_id', $req->property)->whereNotIn('applicant_id', $record)->get();
        } else {
            $pendingBookings = User::with('property_scheduling.property_detail')->whereHas('property_scheduling.property_detail', function ($query) use ($record, $req) {
                $query->where('property_id', $req->property);
            })->get();
        }
        return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $pendingBookings]);
        // PropertyScheduling::where('property_id', $req->property)->get();
    }

    /**
     *
     * Pending Booking Details for Specific Property
     *
     */
    public function confirmed_booking_details(Request $req)
    {
        $validationArray = [
            'property' => 'required',
            'user_id'  => 'required',
        ];
        $rules = [
            'property.required' => 241,
            'user_id.required'  => 234,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }
        // Get Properties which are already booked
        // $confirmedBookings = User::whereHas('property_scheduling', function ($query) use ($req) {
        //     $query->where('property_id', $req->property);
        //     $query->where('status', 1);
        // })->get();

        // Query Builder
        $confirmedBookings = DB::table('users')
            ->select('users.id as applicant_id', 'users.Username as username', 'users.email', 'users.name', 'users.user_type', 'property_scheduling.availibility_date_time', 'property_scheduling.property_id', 'property_scheduling.id as scheduling_id')
            ->join('property_scheduling', 'users.id', '=', 'property_scheduling.applicant_id')
            ->where('property_scheduling.property_id', $req->property)
            ->where('property_scheduling.status', 1)
            ->get();
        $confirmedBookings = $confirmedBookings->toArray();
        if (count($confirmedBookings) > 0) {
            return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $confirmedBookings]);
        }
        return response()->json(['status' => false, 'errorcode' => [235], 'successcode' => [], 'data' => null]);
    }

    /**
     *
     * Check Available Properties
     *
     */
    public function check_available_property(Request $req)
    {
        $validationArray = [
            'owner_user_id' => 'required',
        ];
        $rules = [
            'owner_user_id.required' => 234,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }

        // Check if Any Property is Existed with owner
        // Get Result Record
        $record = Properties::select('id')->where('user_id', $req->owner_user_id)->first();
        if ($record) {
            return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => null]);
        }
        return response()->json(['status' => false, 'errorcode' => [235], 'successcode' => [], 'data' => null]);
    }

    /**
     *
     * Total Number of Applied Properties
     *
     */
    public function total_applications(Request $req)
    {
        $validationArray = [
            'applicant_id' => 'required',
        ];
        $rules = [
            'applicant_id.required' => 247,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }
        // Get Total Applications
        $totalApplications = PropertyApplicants::where('applicant_id', $req->applicant_id)->count();

        return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $totalApplications]);
    }
}
