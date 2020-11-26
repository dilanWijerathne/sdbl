<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Symfony\Component\VarDumper\Cloner\Data;

use App\Models\Cif_Response;
use App\Models\Account;
use App\Models\Applicant;
use App\Models\Kyc;
use App\Models\Nominee;
use App\Models\Work_place;
use App\Models\Ref_nums;
use App\Models\Images;
use App\Models\Signatures;
use App\Models\Branches;
use App\Models\Utils;
use Illuminate\Support\Facades\DB;




class Stat extends Controller
{






    public function some()
    {
        echo  env('APP_URL') . "<BR>";
        echo  env('ACCOUNT_CREATE') . "<BR>";
        echo  env('APP_NAME') . "<BR>";
        echo  env('APP_ENV') . "<BR>";
        echo  env('APP_LIVE') . "<BR>";
        echo  env('APP_LIVE') . "<BR>";
    }




    public function get_progress_completed(Request $request)
    {

        $bdo =  $request->user_email;

        $number_of_applications = Applicant::where('bdo', $bdo)->count();

        $number_of_completedApplications = Applicant::where('bdo', $bdo)->where('done', 1)->count();

        $pct_done  =    ($number_of_completedApplications / $number_of_applications) * 100;



        /////////////////// rejected

        $number_of_rejectedApplications = Applicant::where('bdo', $bdo)->where('done', 3)->count();

        $pct_rejected  =    ($number_of_rejectedApplications / $number_of_applications) * 100;


        $par =  array(
            "reject_pct" => (int)$pct_rejected,
            "done_oct" => (int)$pct_done,
            "done" => $number_of_completedApplications,
            "rejected" => $number_of_rejectedApplications,
        );


        echo  json_encode($par);
    }
}
