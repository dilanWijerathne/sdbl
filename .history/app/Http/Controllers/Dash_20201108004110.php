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


class Dash extends Controller
{
    public function dash_view()
    {
        return view('dashboard');
    }


    public function item_view()
    {


        /**
         *
         *   $nic  =  $request->nic;

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


         */
        return view('item');
    }










    public function create_account($para)
    {
        Log::info('Account creation started ' . json_encode($para));

        $responseC = Http::post('http://10.100.32.72:7801/account_creation/v1/accountCreation', [


            "REFERENCE_NUMBER" => $para['ref'], //"TAP000000001000",

            "CIF_NUMBER" => $para['cif'], // "0001451462",

            "CUS_RELATIONSHIP" => "SOW",

            "SEQUENCE_FOR_REF" => "1",

            "SEQUENCE_NUMBER" => "1",

            "SAVINGS_AC_NUMBER" => "0",

            "BRANCH_NUMBER" => "056",

            "SEQUENCE_NO" => "0",

            "PRODUCT_TYPE" => "111",

            "OFFICER_CODE" => "MOB",

            "OPEN_DATE" => "0",

            "INTEREST_PLAN" => "0",

            "SC_PLAN" => "0",

            "ACCOUNT_TYPE" => "26",

            "NO_OF_RELATIONSHI" => "1",

            "ERROR_CODE" => "",

            "STATUS" => "1",

            "USER_ID" => "",

            "DATE" => "",

            "TIME" => "",

            "FIELD1" => "0",

            "FIELD2" => "0",

            "FIELD3" => "0",

            "FIELD4" => "0",

            "FIELD5" => "0",

            "FIELD6" => "",

            "FIELD7" => "",

            "FIELD8" => "",

            "FIELD9" => "",

            "FIELD10" => ""
        ]);

        /**
         *
         * "{"JSON":{"Data":{"referenceNumber":"TAP000000001000",
         * "referenceNumber":"TAP000000001000",
         * "svId":"000002698240","status":"2","error1":"
         * ","error2":"       ","error3":"       ","error4":"       ","error5":"
         *   ","message":"Existing  ","response_status":"OK"}}}"
         */

        $var =  $responseC->body();
        $array = json_decode($var, true);
        $id = $array['JSON']['Data']['response_status'];

        $account = new Account;
        $account->ref_number = $array['JSON']['Data']['referenceNumber'];
        $account->account_number = $array['JSON']['Data']['svId'];

        Log::info(json_encode($array));

        $account->save();
    }









    public function doRef()
    {
        $ref = Ref_nums::orderBy('updated_at', 'desc')->first();

        $v =  $ref['ref_number'] + 1;

        $rn = new Ref_nums;
        $rn->ref_number = $v;
        $rn->save();

        $ref = 'TAP00000000' . $v;
        return $ref;
    }


    public function doName($fullname)
    {



        $name = explode(" ", $fullname);
        $num_name = count($name);
        $surname = $name[$num_name - 1];
        $second_name = "";

        $nm =  $surname . " ";
        for ($i = 0; $num_name - 1 > $i; $i++) {
            $v = $name[$i];
            $nm .= $v[0];
            $second_name .= $v;
        }


        return array($nm, $second_name);
    }


    public function create_new_Cif_inapp(Request $request)
    {

        // $nic = "900103875v";

        $nic  =  $request->nic;

        $app = Applicant::where("nic", $nic)->orderBy('updated_at', 'desc')->first();

        // $kyc = Kyc::where("nic", $nic)->orderBy('updated_at', 'desc')->first();
        // $nominee = Nominee::where("applicant_nic", $nic)->orderBy('updated_at', 'desc')->first();
        $work_place = Work_place::where("applicant_nic", $nic)->orderBy('updated_at', 'desc')->first();
        //$ref = Ref_nums::orderBy('updated_at', 'desc')->first();

        //$price = DB::table('orders')->max('price');


        Log::info(json_encode($app));
        $name = explode(" ", $app['full_name']);
        $num_name = count($name);
        $street = explode(",", $app['address']);
        $addr_lng = count($street);
        $city = $street[$addr_lng - 1];

        $mydate = getdate(date("U"));
        $d =  $mydate["mon"];
        $m = $mydate["mday"];
        $y = $mydate["year"];

        $nm_s = $this->doName($app['full_name']);

        $param = array(
            'initials_of_name' => $app['display_name'],
            'district' => $app['district'],
            'street' => $street[0],
            'secondary_number' => $app['secondary_mobile_number'],
            'primary_mobile_number' => $app['primary_mobile_number'],
            'city' => $city,
            'surname' => $name[$num_name - 1],
            'nic' =>  $app['nic'],
            'sex' =>  $app['sex'],
            'dob' => "1995101", // juliantojd($app['birth_month'], $app['birth_day'], $app['birth_year']),
            'today' => "90010", //juliantojd($m, $d, $y),
            'telephone' => $work_place['telephone'],
            'ref_number' => $this->doRef(),
            'short_name' => $nm_s[0],
            'second_name' => $nm_s[1],
            'title' => $app['title'],
        );



        //  echo $app['applicant_status'];

        /// die();

        echo json_encode($param);
        //  die();

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
            "MARITAL_STATUS" => "S",
            "USER_ID" => "",
            "SHORT_NAME" => $param['short_name'], //"Perera ABC",
            "SECOND_NAME" => $param['second_name'],
            "CURR_STREET" => $param['street'],
            "BUSINESS_PHONE" => "112832599", //$param['telephone'],
            "STATUS" => "1",
            "PRIMARY_OFFICER_COD" => "MOB",
            "CURR_DISTRICT" => $param['district'],
            "CITIZENSHIP_CODE" => "001",
            "CURR_HOUSE_NBR" => "197",
            "HOME_PHONE_NUMBER" => "112832599", //$param['secondary_number'],
            "TIN_ACTIVITY_DATE" => "2020002", // $param['today'],
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
            "CELLULAR_PHONE_NU" => "112832599", // $param['primary_mobile_number'],
            "DATE_OF_BIRTH" => $param['dob'],
            "SOCIO_ECONOMIC_GRO" => "001",
            "PERSONAL_NONPERSONAL" => "P",
            "CIF_NUMBER" => "",
            "SURNAME" => $param['surname'], // "Perera",
            "SIC_CODE" => "33",
            "REFERENCE_NUMBER" =>  $param['ref_number'],
            "CUSTOMER_CLASSIF" => "1",
            "TIME" => "",
            "NATIONAL_ID_NUMBER" => $param['nic'],
            "MOVED_IN_DATE" => "2020002", $param['today'],
            "RACE" => "",
            "CUSTOMER_OPEN_DATE" => "2020002", //$param['today'],
            "TITLE" => "Mr.", //$param['title'] . ".",
            "CUST_DOC_ACTIVITY" => "2020002", //$param['today'],
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

        Log::info(json_encode($var));



        if ($id == "OK") {

            $para = array(
                "cif" => $array['JSON']['Data']['cifNumber'],
                "ref" => $array['JSON']['Data']['cifNumber'],
            );
            $this->create_account($para);
        }

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