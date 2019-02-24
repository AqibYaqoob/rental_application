<?php

namespace App\Http\Controllers;

use App\ChatMessages;
use App\ChatMessagesAttachment;
use App\User;
use DB;
use GeneralFunctions;
use Illuminate\Http\Request;
use Validator;

class ChatController extends Controller
{
    /**
     *
     * Get Applicants List on basis of the Properties that are Requested.
     *
     */
    public function get_applicants_list(Request $req)
    {
        $validationArray = [
            'owner_id' => 'required',
        ];
        $rules = [
            'owner_id.required' => 313,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }

        /*----------  Get Applicants List with Status (offline / Online)  ----------*/
        $applicantDetails = DB::table('property_applicants')
            ->leftJoin('users', 'users.id', '=', 'property_applicants.applicant_id')
            ->leftJoin('user_profile', 'user_profile.user_id', '=', 'users.id')
            ->leftJoin('properties', 'properties.user_id', '=', 'users.id')
            ->select('users.id as applicant_id', 'users.is_online', 'users.name as applicant_name', 'users.email', 'users.device_token', 'user_profile.file_path as profile_image_path')
            ->where('users.id', $req->owner_id)
            ->get();
        $applicantDetails = $applicantDetails->toArray();
        if (count($applicantDetails) > 0) {
            return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $applicantDetails]);
        }
        return response()->json(['status' => false, 'errorcode' => [235], 'successcode' => [], 'data' => null]);
    }

    /**
     *
     * Chat Implementation
     *
     */
    public function chat(Request $req)
    {
        $validationArray = [
            'target_user_id' => 'required',
            'user_id'        => 'required',
            'message'        => 'required',
        ];
        $rules = [
            'target_user_id.required' => 241,
            'user_id.required'        => 234,
            'message.required'        => 317,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }
        // Check if the id provded are valid
        $user_id_check        = User::where('id', $req->user_id)->exists();
        $target_user_id_check = User::where('id', $req->target_user_id)->exists();
        if (!$user_id_check) {
            return response()->json(['status' => false, 'errorcode' => [315], 'successcode' => [], 'data' => null]);
        }
        if (!$target_user_id_check) {
            return response()->json(['status' => false, 'errorcode' => [316], 'successcode' => [], 'data' => null]);
        }
        /**
         *
         * Add Total Messages
         *
         */
        // 1) sort the ids
        $chatRoom = [$req->user_id, $req->target_user_id];
        sort($chatRoom);
        $chatRoomObject    = $chatRoom[0] . ':' . $chatRoom[1];
        $chatTotalMessages = DB::select("INSERT INTO chat_total_message (identifier, total_messages) VALUES ('$chatRoomObject',  1) ON DUPLICATE KEY UPDATE total_messages = total_messages + 1;");

        /**
         *
         * Add Mesages in Chat Room
         *
         */
        $attachment_id = null;
        if ($req->attachment) {
            // Add Profile Image which is optional
            $uploadAttachment = GeneralFunctions::uploadFileUsingBase64($req->attachment);
            $attachment       = ChatMessagesAttachment::create([
                'chat_messages_identifiers' => $chatRoomObject,
                'attachment_file_path'      => $uploadAttachment['url'],
                'attachment_file_name'      => $uploadAttachment['file_name'],
            ]);
            $attachment_id = $attachment->id;
        }
        // 1) Add Messages with total numbers
        $chatMessages = ChatMessages::create([
            'identifier'   => $chatRoomObject,
            'message'      => $req->message,
            'from_user_id' => $req->user_id,
            'attachment'   => $attachment_id,
        ]);
        $deviceToken          = User::select('device_token')->where('id', $req->target_user_id)->first();
        $sendPushNotification = GeneralFunctions::pushNotification($deviceToken->device_token, 'New Message recieved. Please check in Application', ['activity_code' => 10000], 'Message');
        if ($sendPushNotification) {
            return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => null]);
        } else {
            return response()->json(['status' => false, 'errorcode' => [], 'successcode' => [318], 'data' => null]);
        }
    }

    /**
     *
     * Get latest Chat Messages
     *
     */
    public function get_chat_messages(Request $req)
    {
        $validationArray = [
            'target_user_id' => 'required',
            'user_id'        => 'required',
        ];
        $rules = [
            'target_user_id.required' => 241,
            'user_id.required'        => 234,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => false, 'errorcode' => $errors, 'successcode' => [], 'data' => null]);
        }
        // 1) sort the ids
        $chatRoom = [$req->user_id, $req->target_user_id];
        sort($chatRoom);
        $chatRoomObject = $chatRoom[0] . ':' . $chatRoom[1];
        $messages       = ChatMessages::with('attachment', 'user_profile')->where('identifier', $chatRoomObject)->get()->toArray();
        return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $messages]);
    }

    /**
     *
     * Get Landloards on Which Applicant Applied
     *
     */
    public function get_owners_list(Request $req)
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
        /*----------  Get Applicants List with Status (offline / Online)  ----------*/
        $ownerDetails = DB::table('property_applicants')
            ->join('properties', 'properties.id', '=', 'property_applicants.property_id')
            ->join('users', 'users.id', '=', 'properties.user_id')
            ->join('user_profile', 'user_profile.user_id', '=', 'users.id')
            ->select('users.id as owner_id', 'users.is_online', 'users.name as owner_name', 'users.email', 'users.device_token', 'user_profile.file_path as profile_image_path')
            ->where('property_applicants.applicant_id', $req->applicant_id)
            ->get();
        $ownerDetails = $ownerDetails->toArray();
        if (count($ownerDetails) > 0) {
            return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $ownerDetails]);
        }
        return response()->json(['status' => false, 'errorcode' => [235], 'successcode' => [], 'data' => null]);
    }
}
