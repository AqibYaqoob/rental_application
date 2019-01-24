<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class ContractorController extends Controller
{
    /**
     *
     * Show Packages List (Json Record)
     *
     */
    function list(Request $request) {
        $getContractorDetails = User::with('contractor_details')->get()->toArray();
        return response()->json(['status' => true, 'data' => $getContractorDetails]);
    }

    /**
     *
     * Block comment
     *
     */

}
