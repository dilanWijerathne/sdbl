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




class Dash extends Controller
{
    public function dash_view()
    {
        // return view('dashboard');

        echo  "you are not allowed here";
    }



    public function grab_branches()
    {

        return Branches::where('status', 1)->get();
    }



    public function sms($acc, $number)
    {
        $url =  "http://10.100.32.202:7802/sms_sending/v1/SmsSending";
        $mesg = "Thank you for banking with SDB Bank. Your new account number is : " . $acc . ". Call center 0115411411.   Get SDB Bank Mobile app for much better experience : https://rb.gy/cc9xb3 ";

        $response = Http::post($url, [
            'mobalertid' => "0",
            'mobalerttype' => "SINGLE",
            'mobile' => "94" . $number,
            'groupcode' => "",
            'message' =>  $mesg,
            'status' => "QUED",
        ]);

        Log::info('SMS to ' . $number);
        Log::info($response);
    }






    public function reviewed(Request $request)
    {

        Log::info('Review mark');
        Log::info($request);
        if (isset($request->ref) && isset($request->type)) {



            if ($request->type === "ops") {
                $app = Applicant::where("ref", $request->ref)->update(['ops' => 1]);
                $app = Applicant::where("ref", $request->ref)->update(['ops_staff' => $request->bdo]);
            } elseif ($request->type === "mng") {
                $app = Applicant::where("ref", $request->ref)->update(['approved' => 1]);
                $app = Applicant::where("ref", $request->ref)->update(['review_staff' => $request->bdo]);
            } else {
                Log::info('invalid type to review ');
                return  "invalid type";
            }
        } else {
            Log::info('invalid review ');
            return "invalid";
        }
    }



    public function sdb_julian_lib($day)
    {
        //  $day = $request->day;

        $curl = curl_init();

        Log::info('Julian dates from sdb');

        curl_setopt_array($curl, array(
            // http://10.100.32.202:7802/jdate/v1/JDateInformation?cdate
            CURLOPT_URL => "http://10.100.32.202:7802/jdate/v1/JDateInformation?cdate=" . $day,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        Log::info('taken Julian dates from sdb ');
        Log::info($response);
        $vl =  json_decode($response);

        $str = $vl->JSON->Data->Julian_Date; //"19900010";
        $str1 = substr($str, 2);
        return $str;
    }



    public function prepare_number_two_digits($num)
    {
        //$num = 3;
        $num_padded = sprintf("%02d", $num);
        return $num_padded; // returns 04
    }

    public function call_sampaths_format($d, $m, $y)
    {
        $dmy_d = $this->prepare_number_two_digits($d);
        $dmy_m = $this->prepare_number_two_digits($m);
        $dmy_y = $this->prepare_number_two_digits($y);
        $sam_date =  $dmy_d . $dmy_m . $dmy_y;
        Log::info('formated Day');
        Log::info($sam_date);
        return $sam_date;
    }



    public function item_view(Request $request)
    {

        $nic  =  $request->nic;

        $app = Applicant::where("nic", $nic)->latest()->first();
        $kyc = Kyc::where("nic", $nic)->latest()->first();
        $nominee = Nominee::where("applicant_nic", $nic)->latest()->first();
        $work_place = Work_place::where("applicant_nic", $nic)->latest()->first();

        $account = Account::where('nic', $nic)->get();
        $cif_Response =  Cif_Response::where('nic', $nic)->latest()->first();

        $multimedia =  Images::where('nic', $nic)->get();
        $nic_f = Images::where('nic', $nic)->where('file_type', 'nicf')->latest()->first();
        $nic_r = Images::where('nic', $nic)->where('file_type', 'nicr')->latest()->first();
        $proof = Images::where('nic', $nic)->where('file_type', 'proof')->latest()->first();
        $selfie = Images::where('nic', $nic)->where('file_type', 'selfie')->latest()->first();

        $signatures =  Signatures::where('nic', $nic)->latest()->first();



        $bdo_branch = DB::table('users')
            ->join('branch_codes', 'users.branch', '=', 'branch_codes.code')
            ->select('branch_codes.code', 'branch_codes.name', 'users.email')
            ->where('users.email', $app['bdo'])
            ->first();


        $ar = array(
            "Applicant" => $app,
            "KYC" => $kyc,
            "Nominee" => $nominee,
            "WorkPlace" => $work_place,
            "cif" => $cif_Response,
            "acc" => $account,
            "signature" => $signatures,
            "nicf" => $nic_f,
            "nicr" =>  $nic_r,
            "proof" => $proof,
            "selfie" => $selfie,
            "multimedia" => $multimedia,
            "bdo" => $bdo_branch,

        );


        // echo json_encode($ar);




        return $ar; // view('item', compact('ar'));
    }



    // test


    //  old one     http://10.100.32.72:7801/account_creation/v1/accountCreation

    // new onw
    //http://10.100.32.72:7802/account_creationa/v1/accountCreationA

    public function create_account($para)
    {
        Log::info('Account creation started ' . json_encode($para));

        $lv = "http://10.100.32.202:7802/account_creationa/v1/accountCreationA";
        $lv2 = "http://10.100.32.202:7802/account_creationa/v1/accountCreation";

        $t = 'http://10.100.32.72:7801/account_creation/v1/accountCreation';
        $responseC = Http::post($t, [

            "REFERENCE_NUMBER" => $para['ref'], //"TAP000000001000",

            "CIF_NUMBER" => $para['cif'], // "0001451462",

            "CUS_RELATIONSHIP" => "SOW",

            "SEQUENCE_FOR_REF" => "1",

            "SEQUENCE_NUMBER" => "1",

            "SAVINGS_AC_NUMBER" => "0",

            "BRANCH_NUMBER" => $para['branch'],     //

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

            "FIELD6" => "0",

            "FIELD7" => "0",

            "FIELD8" => "0",

            "FIELD9" => "0",

            "FIELD10" => "0"
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
        $account->nic =  $para['nic'];

        Log::info(json_encode($array));

        $account->save();

        //app_ref

        if (isset($array['JSON']['Data']['svId'])) {

            if (strlen($array['JSON']['Data']['svId']) > 2) {
                $app = Applicant::where("ref", $para['app_ref'])->update(['done' => 1]);
                $this->sms($array['JSON']['Data']['svId'], $para['mobile']);



                $param = array(
                    "cusid" => $para['cif'],
                    "account" => $array['JSON']['Data']['svId'],
                    "mobile" => $para['mobile'],
                    "title" => $para['title'],
                    "name" => $para['name'],
                    "branch" => $para['branch'],
                    "email" => $para['email'],

                    Utils::smsreg($param);

                );
            } else {
                Log::error($array['JSON']['Data']['svId'] . " | wrong response from core api");
            }
        } else {
            Log::error("['JSON']['Data']['svId']" . "core banking api response error");
        }
    }







    public function doRef_cif()
    {
        $ref = Ref_nums::orderBy('updated_at', 'desc')->first();

        $v =  $ref['ref_number'] + 1;

        $rn = new Ref_nums;
        $rn->ref_number = $v;
        $rn->save();

        $ref = 'TAP00000000' . $v;
        return $ref;
    }


    public function doRef()
    {
        $ref = Ref_nums::orderBy('updated_at', 'desc')->first();

        $v =  $ref['ref_number'] + 1;

        $rn = new Ref_nums;
        $rn->ref_number = $v;
        $rn->save();

        $ref = 'CUS00000000' . $v;
        return $ref;
    }


    public function doName($fullname)
    {



        $name = explode(" ", $fullname);
        $num_name = count($name);
        $surname = $name[$num_name - 1];
        $second_name = "";

        $initials = "";
        $nm =  $surname . " ";
        for ($i = 0; $num_name - 1 > $i; $i++) {
            $v = $name[$i];
            $nm .= $v[0];
            $second_name .= $v;
            $initials .=  $v[0];
        }

        $str = ltrim($initials, '.');
        $mod = explode(" ", $nm);
        return array($nm, $second_name, $mod[0], $str);
    }

    public function branchArray()
    {
        $b = array();
    }



    public function create_new_Cif_inapp(Request $request)
    {


        $nic  =  $request->nic;

        $app = Applicant::where("nic", $nic)->orderBy('updated_at', 'desc')->first();


        $work_place = Work_place::where("applicant_nic", $nic)->orderBy('updated_at', 'desc')->first();


        if ($app['done'] === 0 | $app['done'] === '0') {



            //////////////////////////
            $bdo_branch = DB::table('users')
                ->join('branch_codes', 'users.branch', '=', 'branch_codes.code')
                ->select('branch_codes.code', 'users.email')
                ->where('users.email', $app['bdo'])
                ->first();

            Log::info('bdo taken');
            Log::info(json_encode($bdo_branch));
            //  $bdo_branch = json_decode(json_encode($$bdo_branch, true));

            if ($app['existing_customer'] === "true") {


                $cif_Response =  Cif_Response::where('nic', $nic)->orderBy('updated_at', 'desc')->first();

                $cif_r =  $this->doRef_cif();

                $ex_cus_mobile =  $app['primary_mobile_number'];

                $para = array(
                    "cif" => $cif_Response['cif'],
                    "ref" => $cif_r,
                    "nic" => $nic,
                    "branch" => $bdo_branch->code,
                    "app_ref" => $app['ref'],
                    "mobile" => substr($ex_cus_mobile, 2),
                    "title" => $app['title'],
                    "name" =>  $app['full_name'],
                    "email" => "",
                );

                Log::info("old customer to new acccount");

                $this->create_account($para);
            } else {


                Log::info("CIF call new cusomter ");
                Log::info(json_encode($app));
                $name = explode(" ", $app['full_name']);
                $num_name = count($name);
                //$street = explode(",", $app['address']);


                $mydate = getdate(date("U"));
                $d =  $mydate["mday"];
                $m = $mydate["mon"];
                $y = $mydate["year"];

                $nm_s = $this->doName($app['full_name']);


                $short_name = str_replace(".", "", $nm_s[3]);
                $s = $nm_s[2] . " " . $short_name;
                $short_name = substr($s, 0, 20);


                $pnumber = substr($app['primary_mobile_number'], 1);
                if ($app['secondary_mobile_number'] === null | $app['secondary_mobile_number'] === "null" | $app['secondary_mobile_number'] === NULL | $app['secondary_mobile_number'] === "NULL" | $app['secondary_mobile_number'] === "0" | $app['secondary_mobile_number'] === 0) {
                    $pnumber = substr($app['primary_mobile_number'], 1);
                } else {
                    $pnumber = substr($app['secondary_mobile_number'], 1);
                }



                $onumber =  "";
                if ($work_place['telephone'] === null | $work_place['telephone'] === "null" | $work_place['telephone'] === NULL | $work_place['telephone'] === "NULL" | $work_place['telephone'] === "" | $work_place['telephone'] === " " | $work_place['telephone'] === "0" | $work_place['telephone'] === 0) {
                    $onumber = substr($app['primary_mobile_number'], 1);
                } else {
                    $onumber = substr($work_place['telephone'], 1);
                }


                $param = array(
                    'initials_of_name' => $nm_s[3], //$app['display_name'],
                    'district' => $app['district'],
                    'house_numer' =>  $app['address1'],
                    'CURR_STREET' => $app['address2'],
                    'city' =>   $app['address3'],
                    'secondary_number' =>  $pnumber, //  substr($app['secondary_mobile_number'], 1),
                    'primary_mobile_number' =>  substr($app['primary_mobile_number'], 1),
                    'surname' => $name[$num_name - 1],
                    'nic' =>  $app['nic'],
                    'sex' =>  $app['sex'],
                    'dob' =>  $this->sdb_julian_lib($this->call_sampaths_format($app['birth_day'], $app['birth_month'], $app['birth_year'])), //juliantojd($app['birth_month'], $app['birth_day'], $app['birth_year']),
                    'today' => $this->sdb_julian_lib($this->call_sampaths_format($d, $m, $y)),   //     "2020280", // juliantojd($m, $d, $y),  // for uat only
                    'telephone' => $onumber, //substr($work_place['telephone'], 1),
                    'ref_number' => $this->doRef(),
                    'short_name' => $short_name, // . " " . ,
                    'second_name' =>  $name[$num_name - 1], //, // $nm_s[1],
                    'title' => $app['title'] . ".",
                    'branch' => $this->prepare_number_two_digits((int) $bdo_branch->code),
                );






                Log::info($param);

                $lv =  "http://10.100.32.202:7802/new_cif_creationa/v1/newCifCreationA";
                $lv2 =  "http://10.100.32.202:7802/new_cif_creationa/v1/newCifCreation";
                $t = 'http://10.100.32.72:7801/new_cif_creation/v1/newCifCreation';


                $responseB = Http::post($t, [
                    "FIELD1" => "0",
                    "FIELD2" => "0",
                    "FIELD3" => "0",
                    "FIELD4" => "0",
                    "FIELD5" => "0",
                    "FIELD6" => "0",
                    "FIELD7" => "0",
                    "FIELD8" => "0",
                    "FIELD9" => "0",
                    "FIELD10" => "0",
                    "MARITAL_STATUS" => "",
                    "USER_ID" => "",
                    "SHORT_NAME" => $param['short_name'], //"Perera ABC",
                    "SECOND_NAME" => $param['second_name'],
                    "CURR_STREET" => $param['CURR_STREET'],
                    "BUSINESS_PHONE" =>  $this->default_val($param['telephone']), //$param['telephone'],
                    "STATUS" => $param['today'],
                    "PRIMARY_OFFICER_COD" => "MOB",
                    "CURR_DISTRICT" => $param['district'],
                    "CITIZENSHIP_CODE" => "001",
                    "CURR_HOUSE_NBR" => $param['house_numer'],
                    "HOME_PHONE_NUMBER" => $this->default_val($param['secondary_number']),
                    "TIN_ACTIVITY_DATE" => $param['today'],  // current date // today  => UAT 2020280 //  october 6 2020
                    "CURR_POST_TOWN" => $param['city'],
                    "DATE" => "",  // current date
                    "MARKET_SEQMENT" => "SOT",
                    "CURR_COUNTRY" => "Sri Lanka",
                    "BRANCH_NUMBER" => $param['branch'],
                    "ACCOUNT_TYPE" => "S",
                    "SOURCE_OF_DATA" => "",
                    "SEX" => $param['sex'],
                    "CUSTOMER_TYPE" => "001",
                    "FIRST_NAME" => $param['initials_of_name'],
                    "PREFERED_CUSTOMER" => "",
                    "ERROR_CODE" => "1",
                    "SEQUENCE_NUMBER" => "1",
                    "LOCATION_CODE" => "1",
                    "CELLULAR_PHONE_NU" => $param['primary_mobile_number'],
                    "DATE_OF_BIRTH" => $param['dob'],
                    "SOCIO_ECONOMIC_GRO" => "001",
                    "PERSONAL_NONPERSONAL" => "P",
                    "CIF_NUMBER" => "",
                    "SURNAME" => $param['surname'], // "Perera",
                    "SIC_CODE" => "33",
                    "REFERENCE_NUMBER" =>  $param['ref_number'],
                    "CUSTOMER_CLASSIF" => "1",
                    "TIME" => "",  /// current time iso time
                    "NATIONAL_ID_NUMBER" => $param['nic'],
                    "MOVED_IN_DATE" =>  $param['today'],   // "2020002"
                    "RACE" => "O",
                    "CUSTOMER_OPEN_DATE" => $param['today'],
                    "TITLE" => $param['title'], //"Mr.",
                    "CUST_DOC_ACTIVITY" => $param['today'],
                    "SOLICITABLE_CODE" => ""

                ]);


                /*
{"JSON":{"Data":{"referenceNumber":"CUS000000000751",
  "cifNumber":" ",
  "status":"1",
  "error1":" ","error2":" ","error3":" ","error4":" ","error5":" ","message":" ","response_status":"OK"}}}
*/


                Log::info("response from cif api");
                Log::info($responseB);

                $var =  $responseB->body();
                Log::info("response from cif api body");
                Log::info($var);
                $array = json_decode($var, true);





                Log::info(json_encode($var));






                if (isset($array['JSON']['Data']['response_status'])) {

                    $newCif = new Cif_response;
                    $newCif->ref_number = $array['JSON']['Data']['referenceNumber'];
                    $newCif->cif = $array['JSON']['Data']['cifNumber'];
                    $newCif->response_status = $array['JSON']['Data']['response_status'];
                    $newCif->nic = $nic;

                    $newCif->save();


                    if (isset($array['JSON']['Data']['cifNumber'])) {





                        if (strlen($array['JSON']['Data']['cifNumber'] > 2)) {


                            $cif_r_new =  $this->doRef_cif();
                            $para = array(
                                "cif" => $array['JSON']['Data']['cifNumber'],
                                "ref" => $cif_r_new,
                                "nic" => $nic,
                                "mobile" => $param['primary_mobile_number'],
                                "branch" => $bdo_branch->code,
                                "app_ref" => $app['ref'],
                                "title" => $app['title'],
                                "name" =>  $param['surname'],
                                "email" => "",
                            );

                            $this->create_account($para);
                        } else {
                            Log::critical($array['JSON']['Data']['cifNumber'] . ' | Core banking ESB seems not functioning properly !  Ref :' . $app['nic']);
                            echo "ESB /Middlewear response error -  check with IT admin !";
                        }
                    } else {
                        Log::critical("Cannot create account process bcz no CIF given from core, Core banking ESB seems not functioning properly !  Ref:" . $app['nic']);
                        echo "ESB /Middlewear response error -  check with IT admin !";
                    }
                } else {
                    Log::critical("Cannot create account process bcz no CIF given from core!   Ref:" . $app['nic']);
                    echo "ESB /Middlewear response error -  check with IT admin !";
                }
            }
        } else {
            echo "Already approved and account created !";
        }

        ////// end
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



    public function default_val($val)
    {

        if ($val === "" | $val === " " | $val === null | $val === "null") {
            $val = 0;
        }

        return $val;
    }





    public function getApplicants(Request $request)
    {


        $app = Applicant::all();


        Log::info('user check to view applicant data');
        Log::info($request->user_email);
        $user_email = $request->user_email;

        $bdo_branch = DB::table('users')
            ->join('branch_codes', 'users.branch', '=', 'branch_codes.code')
            ->select('branch_codes.code', 'users.email')
            ->where('users.email', $user_email)
            ->first();

        $user = $bdo_branch->code;
        Log::info('user code  ' . $user);

        if ($user === 0 | $user === "0") {
            Log::info('user code Central ' . $user);

            $models = DB::table('applicant')
                ->select('title',  'display_name', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at')
                ->where('nic', 'LIKE', $request->search . '%')
                ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                ->orderBy('created_at', 'desc')
                ->limit($request->end)->offset($request->start - 1)
                ->get()
                ->map(function ($item) {
                    return [$item->title,  $item->display_name, $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->created_at];
                })->toArray();


            Log::info($models);
            $ln = DB::table('applicant')
                ->select('title', 'display_name', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at')
                ->where('nic', 'LIKE', $request->search . '%')
                ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                ->limit($request->end)->offset($request->start - 1)
                ->count();
            // $ln = $app->count();

            $a = array(
                "draw" => $request->draw,
                "recordsTotal" => $ln,
                "recordsFiltered" => $ln,
                "data" => $models,

            );



            echo json_encode($a);
        } else {

            Log::info('user code branch ' . $user);


            $models = DB::table('applicant')
                ->select('title',  'display_name', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at')
                ->where('branch', $user)
                /*  ->orWhere('nic', 'LIKE', $request->search . '%')
                ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                */
                ->orderBy('created_at', 'desc')
                ->limit($request->end)->offset($request->start - 1)
                ->get()
                ->map(function ($item) {
                    return [$item->title,  $item->display_name, $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->created_at];
                })->toArray();


            Log::info($models);
            $ln = DB::table('applicant')
                ->select('title', 'display_name', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at')
                ->where('branch', $user)
                /*->orWhere('nic', 'LIKE', $request->search . '%')
                ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                */
                ->limit($request->end)->offset($request->start - 1)
                ->count();
            // $ln = $app->count();

            $a = array(
                "draw" => $request->draw,
                "recordsTotal" => $ln,
                "recordsFiltered" => $ln,
                "data" => $models,

            );



            echo json_encode($a);
        }
    }
}
