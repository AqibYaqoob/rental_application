<?php

namespace App\Http\Controllers;

use App\SkillSet;
use Illuminate\Http\Request;

class SkillSetController extends Controller
{
    /**
     *
     * Show Packages List (Json Record)
     *
     */
    function list(Request $request) {
        $getSkillDetails = SkillSet::get()->toArray();
        return response()->json(['status' => true, 'errorcode' => [], 'successcode' => [200], 'data' => $getSkillDetails]);
    }

}
