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


class Dash extends Controller
{
    public function dash_view()
    {
        return view('dashboard');
    }








    public function getDetailsByNic(Request $request)
    {

        $nic  =  $request->nic;

        $app = Applicant::where("nic", $nic)->get();
        $kyc = Kyc::where("nic", $nic)->get();
        $nominee = Nominee::where("applicant_nic", $nic)->get();
        $work_place = Work_place::where("applicant_nic", $nic)->get();

        $ar = array(
            "Applicant" => $app,
            "KYC" => $kyc,
            "Nominee" => $nominee,
            "Work Place " => $work_place,
        );


        echo json_encode($ar);
    }







    public function getApplicants()
    {


        $app = Applicant::all();



        $k = "";
        $models = $app->map(function ($item) {
            return [$item->title, $item->surname, $item->initials, $item->display_name, $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->created_at];
        })->toArray();



        $ln = $app->count();

        $a = array(
            "draw" => 1,
            "recordsTotal" => $ln,
            "recordsFiltered" => $ln,
            "data" => $models,

        );



        echo json_encode($a);
    }
}
