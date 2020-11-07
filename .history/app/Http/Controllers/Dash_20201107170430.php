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

        echo json_encode($app);
        echo $app[0]['surname'];
    }
}
