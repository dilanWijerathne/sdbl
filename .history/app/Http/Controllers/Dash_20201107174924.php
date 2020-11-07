<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Symfony\Component\VarDumper\Cloner\Data;

use App\Models\Cif_Response;
use App\Models\Account;
use App\Models\Applicant;

class Dash extends Controller
{
    public function dash_view()
    {


        return view('dashboard');
    }


    public function getApplicants()
    {
        $app = Applicant::all();
        $ln = $app->count();
        $a = array(
            "draw" => 1,
            "recordsTotal" => $ln,
            "recordsFiltered" => $ln,
            $app,

        );



        echo json_encode($a);
    }
}
