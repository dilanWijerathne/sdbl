<?php

namespace App\Http\Controllers;

use App\Models\ApplicationConfigs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Kyc;

class Application extends Controller
{





    // account applicants current staus needs to get base on their sex and age
    public function applicant_status(Request $request)
    {
        $age = $request->input('age');
        $sex = $request->input('sex');

        Log::info($request);
        Log::info('applicant age  : ' . $age . "  sex : " . $sex);

        $statuses = null;
        if ($age > 18) {
            if ($sex == "Male") {
                $statuses =  ApplicationConfigs::select('id', 'area', 'val', 'description')->where('area', 'applicant')->where('id', '!=', 250)->where('id', '!=', 251)->get();
            }
            if ($sex == "Female") {
                $statuses =  ApplicationConfigs::select('id', 'area', 'val', 'description')->where('area', 'applicant')->where('id', '!=', 250)->get();
            }
        } elseif ($age < 18) {
            $statuses =  ApplicationConfigs::select('id', 'area', 'val', 'description')->where('id',  250)->get();
        }

        echo json_encode($statuses);
    }


    /**
     * accounts types that applicant going to open
     *
     */

    public function goin_to_open(Request $request)
    {
        $applicant_status = $request->input('status');

        Log::info($request);

        $statuses = null;
        $statuses =  ApplicationConfigs::select('id', 'area', 'val', 'description')->where('area', 'account_type')->get();




        echo json_encode($statuses);
    }



    /**
     *
     * if customer select individula account types in the goin to open to type this has to return accordingly
     *
     */

    public function individual_account_types(Request $request)
    {
        $applicant_status = $request->input('status');

        $age = $request->input('age');
        $sex = $request->input('sex');

        Log::info($request);

        $statuses = null;
        $statuses =  ApplicationConfigs::select('id', 'area', 'val', 'description')->where('area', 'individual_account_type')->get();

        echo json_encode($statuses);
    }
}
