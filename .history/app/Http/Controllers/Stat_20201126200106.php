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
    public function get_progress(Request $request)
    {

        $bdo =  $request->user_email;

        $number_of_applications = Applicant::where('bdo', $bdo)->count();

        $number_of_completedApplications = Applicant::where('bdo', $bdo)->where('done', 1)->count();

        $pct  =    ($number_of_completedApplications / 100) * $number_of_applications;

        echo  $number_of_applications . "<br>";
        echo  $number_of_completedApplications . "<br>";
        echo  $pct . "<br>";
    }
}
