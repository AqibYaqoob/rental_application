<?php

namespace App\Http\Controllers;

use App\Packages;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     *
     * Show Packages List (Json Record)
     *
     */
    function list(Request $request) {
        $getPackageDetails = Packages::get()->toArray();
        return response()->json(['status' => true, 'data' => $getPackageDetails]);
    }

}
