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





    public function create_new_Cif_inapp()
    {

        $nic = "900103875v";

        $app = Applicant::where("nic", $nic)->orderBy('updated_at', 'desc')->first();

        $kyc = Kyc::where("nic", $nic)->orderBy('updated_at', 'desc')->first();
        $nominee = Nominee::where("applicant_nic", $nic)->orderBy('updated_at', 'desc')->first();
        $work_place = Work_place::where("applicant_nic", $nic)->orderBy('updated_at', 'desc')->first();


        $param = array(
            'second_name' => "",
        );



        echo $app['applicant_status'];

        die();

        $responseB = Http::post('http://10.100.32.72:7801/new_cif_creation/v1/newCifCreation', [
            "FIELD1" => "0",
            "FIELD2" => "0",
            "FIELD3" => "0",
            "FIELD4" => "0",
            "FIELD5" => "0",
            "FIELD6" => "",
            "FIELD7" => "",
            "FIELD8" => "",
            "FIELD9" => "",
            "FIELD10" => "",
            "MARITAL_STATUS" => "",
            "USER_ID" => "",
            "SHORT_NAME" => $param[0], //"Perera ABC",
            "SECOND_NAME" => $param['second_name'],
            "CURR_STREET" => $param['street'],
            "BUSINESS_PHONE" => $param['telephone'],
            "STATUS" => "1",
            "PRIMARY_OFFICER_COD" => "MOB",
            "CURR_DISTRICT" => $param['district'],
            "CITIZENSHIP_CODE" => "001",
            "CURR_HOUSE_NBR" => "197",
            "HOME_PHONE_NUMBER" => $param['secondary_number'],
            "TIN_ACTIVITY_DATE" => $param['today'],
            "CURR_POST_TOWN" => $param['city'],
            "DATE" => "",
            "MARKET_SEQMENT" => "SOT",
            "CURR_COUNTRY" => "Sri Lanka",
            "BRANCH_NUMBER" => "56",
            "ACCOUNT_TYPE" => "S",
            "SOURCE_OF_DATA" => "",
            "SEX" => $param['sex'],
            "CUSTOMER_TYPE" => "001",
            "FIRST_NAME" => $param['initials_of_name'],
            "PREFERED_CUSTOMER" => "",
            "ERROR_CODE" => "1",
            "SEQUENCE_NUMBER" => "1",
            "LOCATION_CODE" => "1",
            "CELLULAR_PHONE_NU" => $param['initials_of_name'],
            "DATE_OF_BIRTH" => $param['dob'],
            "SOCIO_ECONOMIC_GRO" => "001",
            "PERSONAL_NONPERSONAL" => "P",
            "CIF_NUMBER" => "",
            "SURNAME" => $param['surname'], // "Perera",
            "SIC_CODE" => "33",
            "REFERENCE_NUMBER" => $param['ref_number'],
            "CUSTOMER_CLASSIF" => "1",
            "TIME" => "",
            "NATIONAL_ID_NUMBER" => $param['nic'],
            "MOVED_IN_DATE" => $param['today'],
            "RACE" => "",
            "CUSTOMER_OPEN_DATE" => $param['today'],
            "TITLE" => $param['ref_number'] . ".",
            "CUST_DOC_ACTIVITY" => $param['today'],
            "SOLICITABLE_CODE" => ""

        ]);


        /*
        {"JSON":{"Data":{"referenceNumber":"CUS000000000751",
            "cifNumber":" ",
            "status":"1",
            "error1":" ","error2":" ","error3":" ","error4":" ","error5":" ","message":" ","response_status":"OK"}}}
        */



        $var =  $responseB->body();
        $array = json_decode($var, true);
        $id = $array['JSON']['Data']['response_status'];

        $newCif = new Cif_response;
        $newCif->ref_number = $array['JSON']['Data']['referenceNumber'];
        $newCif->cif = $array['JSON']['Data']['cifNumber'];
        $newCif->response_status = $array['JSON']['Data']['response_status'];

        $newCif->save();

        echo $id;
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
