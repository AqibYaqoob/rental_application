<?php

namespace App\Http\Controllers;

use App\Properties;
use App\PropertiesUtility;
use App\PropertyFiles;
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
            'main_image'  => 'required|mimes:jpeg,jpg,png,gif|max:10000',
            'user_id'     => 'required',
        ];
        $rules = [
            'description.required' => 222,
            'address.required'     => 223,
            'latitude.required'    => 224,
            'latitude.numeric'     => 225,
            'longitude.required'   => 226,
            'longitude.numeric'    => 227,
            'zipcode.required'     => 228,
            'zipcode.numeric'      => 229,
            'city.required'        => 230,
            'main_image.required'  => 231,
            'main_image.mimes'     => 232,
            'main_image.max'       => 233,
            'user_id.required'     => 234,
        ];
        $validator = Validator::make($req->all(), $validationArray, $rules);
        $errors    = GeneralFunctions::error_msg_serialize($validator->errors());
        if (count($errors) > 0) {
            return response()->json(['status' => 'false', 'data' => $errors, 'code' => 400]);
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
        $uploadMainFile = GeneralFunctions::uploadFile($req->file('main_image'));
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

        if (count($req->file('other_images')) > 0) {
            foreach ($req->file('other_images') as $key => $value) {
                $uploadMainFile                      = GeneralFunctions::uploadFile($value);
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
        return response()->json(['status' => true]);

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
            return response()->json(['status' => 'false', 'data' => $errors, 'code' => 400]);
        }
        // Get Result Record
        $record = Properties::with('properties_utility')->with('properties_files')->where('user_id', $req->user_id)->first();
        if ($record) {
            return response()->json(['status' => true, 'data' => $record->toArray()]);
        } else {
            return response()->json(['status' => false, 'code' => 235]);
        }
    }
}
