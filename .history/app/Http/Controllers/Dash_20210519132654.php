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
use App\Models\Fixed;
use App\Models\investment_saving;
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

    public function grab_branches_byid(Request $request)
    {

        $branch_id = $request->id;
        return Branches::where('code',  $branch_id)->first();
    }



    public function dis($val)
    {


        $dis = array(
            "Ampara" => 17,
            "Anuradhapura" => 20,
            "Badulla" => 9,
            "Batticaloa" => 18,
            "Colombo" => 1,
            "Galle" => 6,
            "Gampaha" => 3,
            "Hambantota" => 8,
            "Jaffna" => 25,
            "Kalutara" => 2,
            "Kandy" => 13,
            "Kegalle" => 4,
            "Kilinochchi" => 24,
            "Kurunegala" => 14,
            "Mannar" => 16,
            "Matale" => 12,
            "Matara" => 7,
            "Monaragala" => 10,
            "Mullaitivu" => 23,
            "Nuwara Eliya" => 11,
            "Polonnaruwa" => 21,
            "Puttalam" => 15,
            "Ratnapura" => 5,
            "Trincomalee" => 19,
            "Vavuniya" => 22,


        );


        return  $dis[$val];
    }

    public function sms($acc, $number)
    {
        $url =   env('SMS_SEND');
        $mesg = "Thank you for banking with SDB Bank. Your new account number is : " . $acc . ". Call center 0115411411.   Get SDB Mobile app download for better experience :  https://rb.gy/cc9xb3 ";

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

        Log::info('Review | Reject mark');
        Log::info($request);
        if (isset($request->ref) && isset($request->type)) {



            if ($request->type === "ops") {
                //$app = Applicant::where("ref", $request->ref)->update(['ops' => 1]);
                //$app = Applicant::where("ref", $request->ref)->update(['ops_staff' => $request->bdo]);

                $appk = Applicant::where("ref", $request->ref)->latest()->first();
                Log::info($appk);
                $app = Applicant::find($appk['id']);
                Log::info($app);
                $app->ops = 1;
                $app->ops_staff = $request->bdo;
                $app->timestamps = false;
                $app->save();
            } elseif ($request->type === "mng") {
                $app = Applicant::where("ref", $request->ref)->update(['approved' => 1]);
                $app = Applicant::where("ref", $request->ref)->update(['review_staff' => $request->bdo]);
            } elseif ($request->type === "reject") {
                $app = Applicant::where("ref", $request->ref)->update(['approved' => 3, 'done' => 2]);
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
            //                          http://10.100.32.72:7801/jdate/v1/JDateInformation?cdate=01011930             // uat
            //                          http://10.100.32.202:7802/jdate/v1/JDateInformation?cdate=01011930             // live
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




    public function item_view_by_ref(Request $request)
    {

        $ref  =  $request->ref;

        $app = Applicant::where("ref", $ref)->latest()->first();
        $kyc = Kyc::where("ref_number", $ref)->latest()->first();
        $nominee = Nominee::where("ref_number", $ref)->latest()->first();
        $work_place = Work_place::where("ref", $ref)->latest()->first();

        $account = Account::where('nic', $app['nic'])->get();
        $cif_Response =  Cif_Response::where('nic', $app['nic'])->latest()->first();

        $multimedia =  Images::where('applicant_ref_number', $ref)->get();

        $nic_f = Images::where('applicant_ref_number', $ref)->where('file_type', 'nicf')->latest()->first();
        $nic_r = Images::where('applicant_ref_number', $ref)->where('file_type', 'nicr')->latest()->first();
        $proof = Images::where('applicant_ref_number', $ref)->where('file_type', 'proof')->latest()->first();
        $proofr = Images::where('applicant_ref_number', $ref)->where('file_type', 'proofr')->latest()->first();
        $selfie = Images::where('applicant_ref_number', $ref)->where('file_type', 'selfie')->latest()->first();

        $signatures =  Signatures::where('ref', $ref)->latest()->first();



        $bdo_branch = DB::table('users')
            ->join('branch_codes', 'users.branch', '=', 'branch_codes.code')
            ->select('branch_codes.code', 'branch_codes.name', 'users.email')
            ->where('users.email', $app['bdo'])
            ->first();

        $fd = "null";
        if ($app['applicant_going_to_open'] == "Fixed Deposits") {
            Log::info('fd sected and from db');
            $fd = Fixed::where("ref", $ref)->latest()->first();
            Log::info($fd);
        } else {
            Log::info('NOT fd tected and from db');
        }


        $investment_saving = "null";
        if ($app['applicant_individual_account_type'] == "Investment Saving") {
            Log::info('Investment Saving sected and from db');
            $investment_saving = investment_saving::where("ref", $ref)->latest()->first();
            Log::info($investment_saving);
        } else {
            Log::info('NOT investment saving tected and from db');
        }




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
            "proofr" => $proofr,
            "selfie" => $selfie,
            "multimedia" => $multimedia,
            "bdo" => $bdo_branch,
            "fd" => $fd,
            "investment" => $investment_saving,
            "updated_at" => date('d-m-Y', strtotime($app['updated_at'])),
            "created_at" =>  date('d-m-Y', strtotime($app['created_at'])),
        );


        return $ar;
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
        $proofr = Images::where('nic', $nic)->where('file_type', 'proofr')->latest()->first();
        $selfie = Images::where('nic', $nic)->where('file_type', 'selfie')->latest()->first();

        $signatures =  Signatures::where('nic', $nic)->latest()->first();



        $bdo_branch = DB::table('users')
            ->join('branch_codes', 'users.branch', '=', 'branch_codes.code')
            ->select('branch_codes.code', 'branch_codes.name', 'users.email')
            ->where('users.email', $app['bdo'])
            ->first();

        $fd = "null";
        if ($app['applicant_going_to_open'] == "Fixed Deposits") {
            Log::info('fd sected and from db');
            $fd = Fixed::where("nic", $nic)->latest()->first();
            Log::info($fd);
        } else {
            Log::info('NOT fd tected and from db');
        }

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
            "proofr" => $proofr,
            "selfie" => $selfie,
            "multimedia" => $multimedia,
            "bdo" => $bdo_branch,
            "fd" => $fd,
        );


        return $ar;
    }






    public function select_fd_code($fd_type,  $interest_disposal_method)
    {
        $fd_code = "";
        if ($fd_type === "Normal FD") {
            if ($interest_disposal_method !== "monthly") {
                $fd_code = "161";
            } else {
                $fd_code = "162";
            }
        }

        if ($fd_type === "Upahara FD") {
            if ($interest_disposal_method !== "monthly") {
                $fd_code = "140";
            } else {
                $fd_code = "150";
            }
        }

        if ($fd_type === "Government Special FD") {
            if ($interest_disposal_method !== "monthly") {
                $fd_code = "184";
            } else {
                $fd_code = "185";
            }
        }

        return $fd_code;
    }


    public function fd_payment_frq($period, $interest_disposal_method)
    {
        $frq = "";
        if ($interest_disposal_method === "monthly") {
            $frq = "1";
        } else {
            $frq = $period;
        }

        return $frq;
    }


    public function sdb_account($ac)
    {
        $k = "0";
        if ($ac === NULL | $ac === "" | $ac === " ") {
            $k = "0";
        } else {
            $k = $ac;
        }

        return  $k;
    }


    public function create_investment($para)
    {
        //investment_saving

        $act = array(
            "Investment Saving" => "114",
        );

        $isa = investment_saving::where("ref", $para['app_ref'])->latest()->first();


        Log::info(' investment saving taken from db  for timeaccount');
        Log::info($isa);

        $I_DISPOSTION_CODE = "T";


        Log::info(' investment saving taken from db  for I_DISPOSTION_CODE ');
        Log::info($I_DISPOSTION_CODE);

        $isa_code = "114";



        $REFERENCE_NUMBER =  $this->doRef_fd();
        Log::info(' investment saving taken from db  for fd code ');
        Log::info($REFERENCE_NUMBER);

        ///  $internal_account = $this->sdb_account($fd['interest_transfer_account']);
        //investment_saving

        $aa = array(
            "REFERENCE_NUMBER" => $REFERENCE_NUMBER, //"TIM000000000001",
            "CIF_NUMBER" => $para['cif'], //"0001143959",
            "CUS_RELATIONSHIP" => "SOW",
            "SEQUENCE_FOR_REF" => "1",
            "SEQUENCE_NUMBER" => "0",
            "TIME_AC_NUMBER" => "0",
            "BRANCH_NUMBER" => $para['branch'], //"56",
            "SEQUENCE_NO" => "1",
            "PRODUCT_TYPE" => $isa_code, //"162", // add relevent product type from table
            "OFFICER_CODE" => "TAB", //$para['empId'], // officer epf number  "TAB", //   change when live
            "OPEN_DATE" => $para['today'],   // julina today
            "FACE_AMOUNT" => $isa['desposit'], //"500000",
            "TESSA_TYPE" => "0",
            "RETIRE_PLAN_NUM" => "",
            "P_DISPOSTION_CODE" => "N",
            "I_DISPOSTION_CODE" =>  $I_DISPOSTION_CODE, // "C",
            "SOURCE_OF_FUNDS" => "1",
            "DEPOSIT_AMOUNT" => $isa['desposit'],
            "APPLICATION_DATE" => "0",
            "TITLE_MODIFER" => "",
            "ST_ACCRUAL_DATE" =>  $para['today'], // "0",
            "FUNDS_AVAIL_DATE" =>  $para['today'], // "0",
            "PER_NON_PER_CODE" => "P",
            "INTEREST_RATE" => "0.030000",
            "SEND_RC_NOTICE" => "",
            "NEGOTIABLE_FLAG" => "",
            "EMPLOYEE_CODE" => "E",
            "SECURED_FLAG" => "",
            "LEAD_DAYS_FOR_NOT" => "0",
            "PUBLIC_FUND_FLAG" => "",
            "USER_CODE_1" => "",
            "W_H_TAX_ON_INTERE" => "",
            "W_H_TAX_PERSENTAG" => "0",
            "TRUST_DEP_FLAG" => "",
            "ZBA_FUNDING_CC" => "",
            "LOCAL_CUR_EQ" => "0",
            "EXCHANGE_RATE" => "0",
            "OVERRIDE_CODE" => "",
            "COLLATE_HOLD_AMT" => "0",
            "PASSBOOK_NUMBER" => "0",
            "RENEWAL_OPTION" => "A",
            "RENUWAL_PERIOD" => "M",
            "RENEWAL_FREQUENCY" => $isa['period'], // "24",
            "RENEWAL_SPEC_DAY" => (int)$para['day'], // "06",
            "NEXT_REN_MAT_DATE" => "0",
            "SWAP_RATE" => "0",
            "PAYMENT_PERIOD" => "M",
            "PAYMENT_FRE_CY" =>  $isa['period'],
            "PAYMENT_SPE_DAY" =>  (int)$para['day'], // "06",   change when go live
            "NEXT_INT_PAY_DATE" => "0",
            "CODE_FOR_INT_PA" => "0",
            "PAY_INT_LCY_FLAG" => "",
            "BOOK_NOTE_BUY_RAT" => "",
            "ACCRUAL_METHOD" => "S",
            "ACCRUAL_BASE" => "2",
            "YEAR_BASE" => "2",
            "ACCOUNT_OPEN_DATE" => $para['today'], // "2020280",
            "DATE_OF_BIRTH" => "0",
            "LIFE_EXPEC_YEAR" => "0",
            "LIFE_EXPEC_MONTH" => "0",
            "ALLO_CONT_THIS_YR" => "0",
            "ALLO_CONT_LAST_YR" => "0",
            "BENEFICIARY_DATA" => "",
            "EMPLOYER_DESC" => "",
            "STATEM_CYCLE_CODE" => "0",
            "STATEM_CYCLE_FREQ" => "0",
            "STATEM_SPEC_DAY" => "0",
            "FIRST_NEXT_ST_DA" => "0",
            "NO_OF_RELATIONSHI" => "0",
            "ERROR_CODE" => "",
            "STATUS" => "1",
            "USER_ID" => $para['empId'],
            "DATE" => $para['today'], // "20201101",
            "TIME" =>  time(), //  "20020010",
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
            "ORDER_INDEX_IDER" => "H",
            "ANCHOR_PROFILE_NO" => "45",
            "ORDER_REFERENCE" => "",
            "GENERAL_DESCRIPTION" => "",
            "ORDER_PERIODID" => "",
            "ORDER_FREQUENCY" => "0",
            "ORDER_SPECIFIC_DAY" => "0",
            "ORDER_EXECUTION_DAT" => "0",
            "ORDER_EXPIRY_DATE" => "0",
            "DEBIT_RATE_CODE" => "0",
            "CREDIT_RATE_CODE" => "0",
            "RATE_FIXED_CODE" => "0",
            "CROSS_CURRENCY_CNTR" => "0",
            "ANCHOR_EXCHANGE_RAT" => "0",
            "ANCHOR_APP_NO" => "30",
            "ANCHOR_ACCOUNT_NO" => "0",
            "SEQUENCE_NUMBER2" => "0",
            "GENERAL_DESCRIP_2" => "",
            "GENERAL_DESCRIP_3" => "",
            "INTERNAL_GL_COST_CT" => "0",
            "ORDER_INDEX_IDD" => "H",
            "TARGET_INDEX_IDNO" => "E",
            "TARGET_PROFILE_N0" => "35",
            "INTERNAL_ACCOUNT_N0" => "", //  $fd['interest_transfer_account'], //"1089327",
            "AMOUNT" => "0",
            "DESCRIPTION" => "",
            "INTERNAL_GL_COST_CT2" => "0",
            "TARGET_EXCHANGE_RAT" => "0",
            "TARGET_APP_NO" => "26",
            "TARGET_CURRENCY_COD" => "0",
            "DESCRIPTION_2" => "",
            "DESCRIPTION_3" => "",
            "ACCOUNT_SHORT_NAME" => ""
        );


        Log::info(' investment saving array');
        Log::info(json_encode($aa));

        $url = "";
        if (env('APP_LIVE') === "yes") {
            Log::alert('ACC APP L- ' . env('APP_LIVE') . " point -> " .  env('FD_CREATE'));
            $url =  env('FD_CREATE');
        } elseif (env('APP_LIVE') === "no") {
            Log::alert('ACC APP L- ' . env('APP_LIVE') . " point -> " . env('FD_CREATE_TEST'));
            $url =   env('FD_CREATE_TEST');
        }




        //  $url = "http://10.100.32.72:7801/timeaccountcreation/v1/TimeAccountCreation";   // uat
        $response = Http::post($url, [
            "REFERENCE_NUMBER" => $REFERENCE_NUMBER, //"TIM000000000001",
            "CIF_NUMBER" => $para['cif'], //"0001143959",
            "CUS_RELATIONSHIP" => "SOW",
            "SEQUENCE_FOR_REF" => "1",
            "SEQUENCE_NUMBER" => "0",
            "TIME_AC_NUMBER" => "0",
            "BRANCH_NUMBER" => $para['branch'], //"56",
            "SEQUENCE_NO" => "1",
            "PRODUCT_TYPE" => $isa_code, //"162", // add relevent product type from table
            "OFFICER_CODE" =>  "TAB", //$para['empId'], // officer epf number  "TAB", //   change when live
            "OPEN_DATE" => $para['today'],   // julina today
            "FACE_AMOUNT" => $isa['desposit'], //"500000",
            "TESSA_TYPE" => "0",
            "RETIRE_PLAN_NUM" => "",
            "P_DISPOSTION_CODE" => "N",
            "I_DISPOSTION_CODE" =>  $I_DISPOSTION_CODE, // "C",
            "SOURCE_OF_FUNDS" => "1",
            "DEPOSIT_AMOUNT" => $isa['desposit'],
            "APPLICATION_DATE" => "0",
            "TITLE_MODIFER" => "",
            "ST_ACCRUAL_DATE" => $para['today'], // "0",
            "FUNDS_AVAIL_DATE" =>  $para['today'], // "0",
            "PER_NON_PER_CODE" => "P",
            "INTEREST_RATE" => "0.030000.",
            "SEND_RC_NOTICE" => "",
            "NEGOTIABLE_FLAG" => "",
            "EMPLOYEE_CODE" => "E",
            "SECURED_FLAG" => "",
            "LEAD_DAYS_FOR_NOT" => "0",
            "PUBLIC_FUND_FLAG" => "",
            "USER_CODE_1" => "",
            "W_H_TAX_ON_INTERE" => "",
            "W_H_TAX_PERSENTAG" => "0",
            "TRUST_DEP_FLAG" => "",
            "ZBA_FUNDING_CC" => "",
            "LOCAL_CUR_EQ" => "0",
            "EXCHANGE_RATE" => "0",
            "OVERRIDE_CODE" => "",
            "COLLATE_HOLD_AMT" => "0",
            "PASSBOOK_NUMBER" => "0",
            "RENEWAL_OPTION" => "A",
            "RENUWAL_PERIOD" => "M",
            "RENEWAL_FREQUENCY" => $isa['period'], // "24",
            "RENEWAL_SPEC_DAY" => (int)$para['day'], // "06",   change when go live
            "NEXT_REN_MAT_DATE" => "0",
            "SWAP_RATE" => "0",
            "PAYMENT_PERIOD" => "M",
            "PAYMENT_FRE_CY" => $isa['period'],
            "PAYMENT_SPE_DAY" => (int)$para['day'], // "06",   change when go live
            "NEXT_INT_PAY_DATE" => "0",
            "CODE_FOR_INT_PA" => "0",
            "PAY_INT_LCY_FLAG" => "",
            "BOOK_NOTE_BUY_RAT" => "",
            "ACCRUAL_METHOD" => "S",
            "ACCRUAL_BASE" => "2",
            "YEAR_BASE" => "2",
            "ACCOUNT_OPEN_DATE" => $para['today'], // "2020280",
            "DATE_OF_BIRTH" => "0",
            "LIFE_EXPEC_YEAR" => "0",
            "LIFE_EXPEC_MONTH" => "0",
            "ALLO_CONT_THIS_YR" => "0",
            "ALLO_CONT_LAST_YR" => "0",
            "BENEFICIARY_DATA" => "",
            "EMPLOYER_DESC" => "",
            "STATEM_CYCLE_CODE" => "0",
            "STATEM_CYCLE_FREQ" => "0",
            "STATEM_SPEC_DAY" => "0",
            "FIRST_NEXT_ST_DA" => "0",
            "NO_OF_RELATIONSHI" => "0",
            "ERROR_CODE" => "",
            "STATUS" => "1",
            "USER_ID" => $para['empId'],
            "DATE" => $para['today'], // "20201101",
            "TIME" =>   time(), //"20020010",
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
            "ORDER_INDEX_IDER" => "H",
            "ANCHOR_PROFILE_NO" => "45",
            "ORDER_REFERENCE" => "",
            "GENERAL_DESCRIPTION" => "",
            "ORDER_PERIODID" => "",
            "ORDER_FREQUENCY" => "0",
            "ORDER_SPECIFIC_DAY" => "0",
            "ORDER_EXECUTION_DAT" => "0",
            "ORDER_EXPIRY_DATE" => "0",
            "DEBIT_RATE_CODE" => "0",
            "CREDIT_RATE_CODE" => "0",
            "RATE_FIXED_CODE" => "0",
            "CROSS_CURRENCY_CNTR" => "0",
            "ANCHOR_EXCHANGE_RAT" => "0",
            "ANCHOR_APP_NO" => "30",
            "ANCHOR_ACCOUNT_NO" => "0",
            "SEQUENCE_NUMBER2" => "0",
            "GENERAL_DESCRIP_2" => "",
            "GENERAL_DESCRIP_3" => "",
            "INTERNAL_GL_COST_CT" => "0",
            "ORDER_INDEX_IDD" => "H",
            "TARGET_INDEX_IDNO" => "E",
            "TARGET_PROFILE_N0" => "35",
            "INTERNAL_ACCOUNT_N0" => "", //  $fd['interest_transfer_account'], //"1089327",
            "AMOUNT" => "0",
            "DESCRIPTION" => "",
            "INTERNAL_GL_COST_CT2" => "0",
            "TARGET_EXCHANGE_RAT" => "0",
            "TARGET_APP_NO" => "26",
            "TARGET_CURRENCY_COD" => "0",
            "DESCRIPTION_2" => "",
            "DESCRIPTION_3" => "",
            "ACCOUNT_SHORT_NAME" => ""
        ]);





        Log::info(' investment saving core response ');
        Log::info($response);
        $var =  $response->body();
        $array = json_decode($var, true);
        $id = $array['JSON']['Data']['response_status'];

        $account = new Account;
        $account->app_ref =  $para['app_ref'];
        $account->ref_number = $array['JSON']['Data']['referenceNumber'];
        $account->account_number = $array['JSON']['Data']['timeAccountNo'];
        $account->nic = $para['nic'];

        Log::info(json_encode($array));

        $account->save();



        if (isset($array['JSON']['Data']['status'])) {

            if ((int)$array['JSON']['Data']['status'] === 2) {
                $app = Applicant::where("ref", $para['app_ref'])->update(['done' => 1]);
                $this->sms($array['JSON']['Data']['timeAccountNo'], $para['mobile']);



                $param = array(
                    "cusid" => $para['cif'],
                    "account" => $array['JSON']['Data']['timeAccountNo'],
                    "mobile" => $para['mobile'],
                    "title" => $para['title'],
                    "name" => $para['name'],
                    "branch" => $para['branch'],
                    "email" => $para['email'],
                    "nic" => $para['nic'],


                );

                //Log::info('Registering to SMS FD : ' . $para['mobile']);
                //Log::info('Registering to SMS FD');
                // Log::info(json_encode($param));
                //  Utils::smsreg($param);
            } else {
                Log::error($array['JSON']['Data']['timeAccountNo'] . " | investment saving - wrong response from core api");
            }
        } else {
            Log::error("['JSON']['Data']['timeAccountNo']" . "  investment saving - core banking api response error");
        }





        // end of investment saving
    }

    public function create_fd($para)
    {

        $act = array(
            "Normal FD" => "162",
            "Upahara FD" => "150",
            "Government Special FD" => "185",
        );

        $fd = Fixed::where("ref", $para['app_ref'])->latest()->first();


        Log::info('fd taken from db  for timeaccount');
        Log::info($fd);

        $I_DISPOSTION_CODE = "";
        if ($fd['interest_payable_at'] === "disposeOther") {
            $I_DISPOSTION_CODE = "T";
        } else {
            $I_DISPOSTION_CODE = "C";
        }


        Log::info('fd taken from db  for I_DISPOSTION_CODE ');
        Log::info($I_DISPOSTION_CODE);

        $fd_code = $this->select_fd_code($para['act'],  $fd['interest_disposal_method']);
        Log::info('fd taken from db  for fd code ');
        Log::info($fd_code);


        $REFERENCE_NUMBER =  $this->doRef_fd();
        Log::info('fd taken from db  for fd code ');
        Log::info($REFERENCE_NUMBER);

        ///  $internal_account = $this->sdb_account($fd['interest_transfer_account']);

        $aa = array(
            "REFERENCE_NUMBER" => $REFERENCE_NUMBER, //"TIM000000000001",
            "CIF_NUMBER" => $para['cif'], //"0001143959",
            "CUS_RELATIONSHIP" => "SOW",
            "SEQUENCE_FOR_REF" => "1",
            "SEQUENCE_NUMBER" => "1",
            "TIME_AC_NUMBER" => "0",
            "BRANCH_NUMBER" => $para['branch'], //"56",
            "SEQUENCE_NO" => "0",
            "PRODUCT_TYPE" => $fd_code, //"162", // add relevent product type from table
            "OFFICER_CODE" => "TAB", //$para['empId'], // officer epf number  "TAB", //   change when live
            "OPEN_DATE" => $para['today'],   // julina today
            "FACE_AMOUNT" => $fd['desposit'], //"500000",
            "TESSA_TYPE" => "0",
            "RETIRE_PLAN_NUM" => "",
            "P_DISPOSTION_CODE" => "N",
            "I_DISPOSTION_CODE" =>  $I_DISPOSTION_CODE, // "C",
            "SOURCE_OF_FUNDS" => "1",
            "DEPOSIT_AMOUNT" => $fd['desposit'],
            "APPLICATION_DATE" => "0",
            "TITLE_MODIFER" => "",
            "ST_ACCRUAL_DATE" =>  $para['today'], // "0",
            "FUNDS_AVAIL_DATE" =>  $para['today'], // "0",
            "PER_NON_PER_CODE" => "P",
            "INTEREST_RATE" => "0.098500",  //$fd['rate'], //
            "SEND_RC_NOTICE" => "",
            "NEGOTIABLE_FLAG" => "",
            "EMPLOYEE_CODE" => "E",
            "SECURED_FLAG" => "",
            "LEAD_DAYS_FOR_NOT" => "0",
            "PUBLIC_FUND_FLAG" => "",
            "USER_CODE_1" => "",
            "W_H_TAX_ON_INTERE" => "",
            "W_H_TAX_PERSENTAG" => "0",
            "TRUST_DEP_FLAG" => "",
            "ZBA_FUNDING_CC" => "",
            "LOCAL_CUR_EQ" => "0",
            "EXCHANGE_RATE" => "0",
            "OVERRIDE_CODE" => "",
            "COLLATE_HOLD_AMT" => "0",
            "PASSBOOK_NUMBER" => "0",
            "RENEWAL_OPTION" => "A",
            "RENUWAL_PERIOD" => "M",
            "RENEWAL_FREQUENCY" => $fd['period'], // "24",
            "RENEWAL_SPEC_DAY" => $para['day'], // "06",
            "NEXT_REN_MAT_DATE" => "0",
            "SWAP_RATE" => "0",
            "PAYMENT_PERIOD" => "M",
            "PAYMENT_FRE_CY" =>  $this->fd_payment_frq($fd['period'], $fd['interest_disposal_method']),
            "PAYMENT_SPE_DAY" =>  $para['day'], // "06",   change when go live
            "NEXT_INT_PAY_DATE" => "0",
            "CODE_FOR_INT_PA" => "0",
            "PAY_INT_LCY_FLAG" => "",
            "BOOK_NOTE_BUY_RAT" => "",
            "ACCRUAL_METHOD" => "S",
            "ACCRUAL_BASE" => "2",
            "YEAR_BASE" => "2",
            "ACCOUNT_OPEN_DATE" => $para['today'], // "2020280",
            "DATE_OF_BIRTH" => "0",
            "LIFE_EXPEC_YEAR" => "0",
            "LIFE_EXPEC_MONTH" => "0",
            "ALLO_CONT_THIS_YR" => "0",
            "ALLO_CONT_LAST_YR" => "0",
            "BENEFICIARY_DATA" => "",
            "EMPLOYER_DESC" => "",
            "STATEM_CYCLE_CODE" => "0",
            "STATEM_CYCLE_FREQ" => "0",
            "STATEM_SPEC_DAY" => "0",
            "FIRST_NEXT_ST_DA" => "0",
            "NO_OF_RELATIONSHI" => "0",
            "ERROR_CODE" => "",
            "STATUS" => "1",
            "USER_ID" => $para['empId'],
            "DATE" => $para['today'], // "20201101",
            "TIME" =>  time(), //  "20020010",
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
            "ORDER_INDEX_IDER" => "H",
            "ANCHOR_PROFILE_NO" => "45",
            "ORDER_REFERENCE" => "",
            "GENERAL_DESCRIPTION" => "",
            "ORDER_PERIODID" => "",
            "ORDER_FREQUENCY" => "0",
            "ORDER_SPECIFIC_DAY" => "0",
            "ORDER_EXECUTION_DAT" => "0",
            "ORDER_EXPIRY_DATE" => "0",
            "DEBIT_RATE_CODE" => "0",
            "CREDIT_RATE_CODE" => "0",
            "RATE_FIXED_CODE" => "0",
            "CROSS_CURRENCY_CNTR" => "0",
            "ANCHOR_EXCHANGE_RAT" => "0",
            "ANCHOR_APP_NO" => "30",
            "ANCHOR_ACCOUNT_NO" => "0",
            "SEQUENCE_NUMBER2" => "0",
            "GENERAL_DESCRIP_2" => "",
            "GENERAL_DESCRIP_3" => "",
            "INTERNAL_GL_COST_CT" => "0",
            "ORDER_INDEX_IDD" => "H",
            "TARGET_INDEX_IDNO" => "E",
            "TARGET_PROFILE_N0" => "35",
            "INTERNAL_ACCOUNT_N0" =>  $fd['interest_transfer_account'], //"1089327",
            "AMOUNT" => "0",
            "DESCRIPTION" => "",
            "INTERNAL_GL_COST_CT2" => "0",
            "TARGET_EXCHANGE_RAT" => "0",
            "TARGET_APP_NO" => "26",
            "TARGET_CURRENCY_COD" => "0",
            "DESCRIPTION_2" => "",
            "DESCRIPTION_3" => "",
            "ACCOUNT_SHORT_NAME" => ""
        );


        Log::info('FD array');
        Log::info(json_encode($aa));

        $url = "";
        if (env('APP_LIVE') === "yes") {
            Log::alert('ACC APP L- ' . env('APP_LIVE') . " point -> " .  env('FD_CREATE'));
            $url =  env('FD_CREATE');
        } elseif (env('APP_LIVE') === "no") {
            Log::alert('ACC APP L- ' . env('APP_LIVE') . " point -> " . env('FD_CREATE_TEST'));
            $url =   env('FD_CREATE_TEST');
        }




        //  $url = "http://10.100.32.72:7801/timeaccountcreation/v1/TimeAccountCreation";   // uat
        $response = Http::post($url, [
            "REFERENCE_NUMBER" => $REFERENCE_NUMBER, //"TIM000000000001",
            "CIF_NUMBER" => $para['cif'], //"0001143959",
            "CUS_RELATIONSHIP" => "SOW",
            "SEQUENCE_FOR_REF" => "1",
            "SEQUENCE_NUMBER" => "1",
            "TIME_AC_NUMBER" => "0",
            "BRANCH_NUMBER" => $para['branch'], //"56",
            "SEQUENCE_NO" => "0",
            "PRODUCT_TYPE" => $fd_code, //"162", // add relevent product type from table
            "OFFICER_CODE" =>  "TAB", //$para['empId'], // officer epf number  "TAB", //   change when live
            "OPEN_DATE" => $para['today'],   // julina today
            "FACE_AMOUNT" => $fd['desposit'], //"500000",
            "TESSA_TYPE" => "0",
            "RETIRE_PLAN_NUM" => "",
            "P_DISPOSTION_CODE" => "N",
            "I_DISPOSTION_CODE" =>  $I_DISPOSTION_CODE, // "C",
            "SOURCE_OF_FUNDS" => "1",
            "DEPOSIT_AMOUNT" => $fd['desposit'],
            "APPLICATION_DATE" => "0",
            "TITLE_MODIFER" => "",
            "ST_ACCRUAL_DATE" => $para['today'], // "0",
            "FUNDS_AVAIL_DATE" =>  $para['today'], // "0",
            "PER_NON_PER_CODE" => "P",
            "INTEREST_RATE" => "0.098500",  //$fd['rate'],
            "SEND_RC_NOTICE" => "",
            "NEGOTIABLE_FLAG" => "",
            "EMPLOYEE_CODE" => "E",
            "SECURED_FLAG" => "",
            "LEAD_DAYS_FOR_NOT" => "0",
            "PUBLIC_FUND_FLAG" => "",
            "USER_CODE_1" => "",
            "W_H_TAX_ON_INTERE" => "",
            "W_H_TAX_PERSENTAG" => "0",
            "TRUST_DEP_FLAG" => "",
            "ZBA_FUNDING_CC" => "",
            "LOCAL_CUR_EQ" => "0",
            "EXCHANGE_RATE" => "0",
            "OVERRIDE_CODE" => "",
            "COLLATE_HOLD_AMT" => "0",
            "PASSBOOK_NUMBER" => "0",
            "RENEWAL_OPTION" => "A",
            "RENUWAL_PERIOD" => "M",
            "RENEWAL_FREQUENCY" => $fd['period'], // "24",
            "RENEWAL_SPEC_DAY" => $para['day'], // "06",   change when go live
            "NEXT_REN_MAT_DATE" => "0",
            "SWAP_RATE" => "0",
            "PAYMENT_PERIOD" => "M",
            "PAYMENT_FRE_CY" => $this->fd_payment_frq($fd['period'], $fd['interest_disposal_method']),
            "PAYMENT_SPE_DAY" => $para['day'], // "06",   change when go live
            "NEXT_INT_PAY_DATE" => "0",
            "CODE_FOR_INT_PA" => "0",
            "PAY_INT_LCY_FLAG" => "",
            "BOOK_NOTE_BUY_RAT" => "",
            "ACCRUAL_METHOD" => "S",
            "ACCRUAL_BASE" => "2",
            "YEAR_BASE" => "2",
            "ACCOUNT_OPEN_DATE" => $para['today'], // "2020280",
            "DATE_OF_BIRTH" => "0",
            "LIFE_EXPEC_YEAR" => "0",
            "LIFE_EXPEC_MONTH" => "0",
            "ALLO_CONT_THIS_YR" => "0",
            "ALLO_CONT_LAST_YR" => "0",
            "BENEFICIARY_DATA" => "",
            "EMPLOYER_DESC" => "",
            "STATEM_CYCLE_CODE" => "0",
            "STATEM_CYCLE_FREQ" => "0",
            "STATEM_SPEC_DAY" => "0",
            "FIRST_NEXT_ST_DA" => "0",
            "NO_OF_RELATIONSHI" => "0",
            "ERROR_CODE" => "",
            "STATUS" => "1",
            "USER_ID" => $para['empId'],
            "DATE" => $para['today'], // "20201101",
            "TIME" =>   time(), //"20020010",
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
            "ORDER_INDEX_IDER" => "H",
            "ANCHOR_PROFILE_NO" => "45",
            "ORDER_REFERENCE" => "",
            "GENERAL_DESCRIPTION" => "",
            "ORDER_PERIODID" => "",
            "ORDER_FREQUENCY" => "0",
            "ORDER_SPECIFIC_DAY" => "0",
            "ORDER_EXECUTION_DAT" => "0",
            "ORDER_EXPIRY_DATE" => "0",
            "DEBIT_RATE_CODE" => "0",
            "CREDIT_RATE_CODE" => "0",
            "RATE_FIXED_CODE" => "0",
            "CROSS_CURRENCY_CNTR" => "0",
            "ANCHOR_EXCHANGE_RAT" => "0",
            "ANCHOR_APP_NO" => "30",
            "ANCHOR_ACCOUNT_NO" => "0",
            "SEQUENCE_NUMBER2" => "0",
            "GENERAL_DESCRIP_2" => "",
            "GENERAL_DESCRIP_3" => "",
            "INTERNAL_GL_COST_CT" => "0",
            "ORDER_INDEX_IDD" => "H",
            "TARGET_INDEX_IDNO" => "E",
            "TARGET_PROFILE_N0" => "35",
            "INTERNAL_ACCOUNT_N0" =>  $fd['interest_transfer_account'], //"1089327",
            "AMOUNT" => "0",
            "DESCRIPTION" => "",
            "INTERNAL_GL_COST_CT2" => "0",
            "TARGET_EXCHANGE_RAT" => "0",
            "TARGET_APP_NO" => "26",
            "TARGET_CURRENCY_COD" => "0",
            "DESCRIPTION_2" => "",
            "DESCRIPTION_3" => "",
            "ACCOUNT_SHORT_NAME" => ""
        ]);



        /*

        {
    "JSON": {
        "Data": {
            "referenceNumber": "TIM000000601449",
            "timeAccountNo": "000000000000",
            "status": "2",
            "error1": "       ",
            "error2": "       ",
            "error3": "       ",
            "error4": "       ",
            "error5": "       ",
            "message": "          ",
            "depositonCode": "T",
            "response_status": "OK"
        }
    }
}

        */

        Log::info('FD core response ');
        Log::info($response);
        $var =  $response->body();
        $array = json_decode($var, true);
        $id = $array['JSON']['Data']['response_status'];

        $account = new Account;
        $account->app_ref =  $para['app_ref'];
        $account->ref_number = $array['JSON']['Data']['referenceNumber'];
        $account->account_number = $array['JSON']['Data']['timeAccountNo'];
        $account->nic = $para['nic'];

        Log::info(json_encode($array));

        $account->save();



        if (isset($array['JSON']['Data']['status'])) {

            if ((int)$array['JSON']['Data']['status'] === 2) {
                $app = Applicant::where("ref", $para['app_ref'])->update(['done' => 1]);
                $this->sms($array['JSON']['Data']['timeAccountNo'], $para['mobile']);



                $param = array(
                    "cusid" => $para['cif'],
                    "account" => $array['JSON']['Data']['timeAccountNo'],
                    "mobile" => $para['mobile'],
                    "title" => $para['title'],
                    "name" => $para['name'],
                    "branch" => $para['branch'],
                    "email" => $para['email'],
                    "nic" => $para['nic'],


                );

                //Log::info('Registering to SMS FD : ' . $para['mobile']);
                //Log::info('Registering to SMS FD');
                // Log::info(json_encode($param));
                //  Utils::smsreg($param);
            } else {
                Log::error($array['JSON']['Data']['timeAccountNo'] . " |FD - wrong response from core api");
            }
        } else {
            Log::error("['JSON']['Data']['timeAccountNo']" . " FD - core banking api response error");
        }




        ///////////////// end of fd creation
    }


    public function create_account($para)
    {

        //create_fd
        if ($para['applicant_going_to_open'] === "Fixed Deposits") {
            Log::info('FD creation started ' . json_encode($para));
            $this->create_fd($para);
        }
        if ($para['act'] === "Investment Saving") {
            Log::info('Investment saving creation started ' . json_encode($para));
            $this->create_investment($para);
        } else {

            Log::info('Account creation started ' . json_encode($para));




            $act = array(
                "Top Server" => "122",
                "Normal Savings" => "111",
                "UPay Business Account" => "129",
                "Uththamavi Plus" => "115",
                "Upahara Savings" => "137",
                "Agri Saving" => "136",
                "Investment Saving" => "114",
            );

            $url = "";
            if (env('APP_LIVE') === "yes") {
                Log::alert('ACC APP L- ' . env('APP_LIVE') . " point -> " .  env('ACCOUNT_CREATE'));
                $url =  env('ACCOUNT_CREATE');
            } elseif (env('APP_LIVE') === "no") {
                Log::alert('ACC APP L- ' . env('APP_LIVE') . " point -> " . env('ACCOUNT_CREATE_TEST'));
                $url =   env('ACCOUNT_CREATE_TEST');
            }


            $check2 = array(

                "REFERENCE_NUMBER" => $para['ref'], //"TAP000000001000",

                "CIF_NUMBER" => $para['cif'], // "0001451462",

                "CUS_RELATIONSHIP" => "SOW",

                "SEQUENCE_FOR_REF" => "1",

                "SEQUENCE_NUMBER" => "1",

                "SAVINGS_AC_NUMBER" => "0",

                "BRANCH_NUMBER" => $para['branch'],     //

                "SEQUENCE_NO" => "0",

                "PRODUCT_TYPE" => $act[$para['act']],

                "OFFICER_CODE" => "MOB",

                "OPEN_DATE" => "0",

                "INTEREST_PLAN" => "0",

                "SC_PLAN" => "0",

                "ACCOUNT_TYPE" => "26",

                "NO_OF_RELATIONSHI" => "1",

                "ERROR_CODE" => "",

                "STATUS" => "1",

                "USER_ID" =>  $para['empId'],

                "DATE" => $para['today'],

                "TIME" => time(),

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
            );

            /*
               "USER_ID" => $para['empId'],
            "DATE" => $para['today'], // "20201101",
            "TIME" =>  time(), //  "20020010",

            */

            Log::info('acc array');
            Log::info(json_encode($check2));

            $responseC = Http::post($url, [

                "REFERENCE_NUMBER" => $para['ref'], //"TAP000000001000",

                "CIF_NUMBER" => $para['cif'], // "0001451462",

                "CUS_RELATIONSHIP" => "SOW",

                "SEQUENCE_FOR_REF" => "1",

                "SEQUENCE_NUMBER" => "1",

                "SAVINGS_AC_NUMBER" => "0",

                "BRANCH_NUMBER" => $para['branch'],     //

                "SEQUENCE_NO" => "0",

                "PRODUCT_TYPE" =>  $act[$para['act']],

                "OFFICER_CODE" => "MOB",

                "OPEN_DATE" => "0",

                "INTEREST_PLAN" => "0",

                "SC_PLAN" => "0",

                "ACCOUNT_TYPE" => "26",

                "NO_OF_RELATIONSHI" => "1",

                "ERROR_CODE" => "",

                "STATUS" => "1",

                "USER_ID" =>  $para['empId'],

                "DATE" => $para['today'],

                "TIME" => time(),

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



            $var =  $responseC->body();
            $array = json_decode($var, true);

            Log::info(" account creation response " . $para['ref']);
            Log::info($array);
            $id = $array['JSON']['Data']['response_status'];

            $account = new Account;
            $account->app_ref =  $para['app_ref'];
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
                        "nic" => $para['nic'],


                    );

                    Log::info('Registering to SMS : ' . $para['mobile']);
                    Log::info('Registering to SMS');
                    Log::info(json_encode($param));
                    Utils::smsreg($param);
                } else {
                    Log::error($array['JSON']['Data']['svId'] . " | wrong response from core api");
                }
            } else {
                Log::error("['JSON']['Data']['svId']" . "core banking api response error");
            }
        }
    }
    // end of account creation



    public function generateRandomString($length)
    {
        $characters = '123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }




    // fd ref creation

    public function doRef_fd()
    {
        //   $ref = Ref_nums::orderBy('updated_at', 'desc')->first();
        $ref = Ref_nums::latest()->first();
        //  $app = Applicant::where("nic", $nic)->latest()->first();
        $v =  $ref['ref_number'] + 1;

        $rn = new Ref_nums;
        $rn->ref_number = $v;
        $rn->save();

        $ref = 'TIM0000000' . $v; // removed one 0, orginal TIM00000000
        $wildcard =  $this->generateRandomString(1);
        $ref = substr_replace($ref, $wildcard, 9, 1);
        return $ref;
    }


    public function doRef_cif()
    {
        //   $ref = Ref_nums::orderBy('updated_at', 'desc')->first();
        $ref = Ref_nums::latest()->first();
        //  $app = Applicant::where("nic", $nic)->latest()->first();
        $v =  $ref['ref_number'] + 1;

        $rn = new Ref_nums;
        $rn->ref_number = $v;
        $rn->save();


        $ref = 'TAP0000000' . $v; // removed 0, orginal TAP00000000
        $wildcard =  $this->generateRandomString(1);
        $ref = substr_replace($ref, $wildcard, 9, 1);
        return $ref;
    }




    public function doRef()
    {
        //$ref = Ref_nums::orderBy('updated_at', 'desc')->first();
        $ref = Ref_nums::latest()->first();

        $v =  $ref['ref_number'] + 1;

        $rn = new Ref_nums;
        $rn->ref_number = $v;
        $rn->save();

        $ref = 'CUS0000000' . $v; // removed 0, orginal CUS00000000
        $wildcard =  $this->generateRandomString(1);
        $ref = substr_replace($ref, $wildcard, 9, 1);
        return $ref;
    }


    public function doName($fullname)
    {
        $fullname =  trim($fullname);
        Log::info("Do name input " . $fullname);

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
            $initials .=  $v[0] . ".";
        }

        $str = ltrim($initials, '.');   //// make sure intials separate adter dots
        $str = rtrim($str, ".");
        $mod = explode(" ", $nm);
        Log::info("Do name outcome" . json_encode(array($nm, $second_name, $mod[0], $str)));
        return array($nm, $second_name, $mod[0], $str);
    }

    public function branchArray()
    {
        $b = array();
    }


    public function prepare_mobile_number($d)
    {
        $n =  null;
        if (strlen($d) == 10) {
            $n =  substr($d, 1);
        }
        if (strlen($d) == 11) {
            $n =  substr($d, 2);
        }
        if (strlen($d) == 9) {
            $n =  $d;
        }

        return  $n;
    }

    public function create_new_Cif_inapp(Request $request)
    {

        $ref  =  $request->ref;
        $app = Applicant::where("ref", $ref)->orderBy('updated_at', 'desc')->first();
        $work_place = Work_place::where("ref", $ref)->orderBy('updated_at', 'desc')->first();


        $mydate = getdate(date("U"));
        // change when go live  today
        $d =  13; //$mydate["mday"];
        $m = 05; //$mydate["mon"];
        $y =  $mydate["year"];

        $today = $this->sdb_julian_lib($this->call_sampaths_format($d, $m, $y));
        $nic = $app['nic'];

        if ($app['done'] === 0 | $app['done'] === '0') {



            //////////////////////////
            $bdo_branch = DB::table('users')
                ->join('branch_codes', 'users.branch', '=', 'branch_codes.code')
                ->select('branch_codes.code', 'users.email', 'users.emp')
                ->where('users.email', $app['bdo'])
                ->first();

            Log::info('bdo taken');
            Log::info(json_encode($bdo_branch));
            //  $bdo_branch = json_decode(json_encode($$bdo_branch, true));

            if ($app['existing_customer'] === "true") {

                // if (true) {   // change when go live


                $cif_Response =  Cif_Response::where('nic', $nic)->orderBy('updated_at', 'desc')->first();

                $cif_r =  $this->doRef_cif();

                $ex_cus_mobile =  $app['primary_mobile_number'];
                /*
              prepapre name

               */


                $name = explode(" ", $app['full_name']);
                $num_name = count($name);

                $para = array(
                    "cif" => $cif_Response['cif'],
                    "ref" => $cif_r,
                    "nic" => $nic,
                    "branch" => $bdo_branch->code,
                    "app_ref" => $app['ref'],
                    "mobile" => $this->prepare_mobile_number($ex_cus_mobile), //substr( $ex_cus_mobile , 2),
                    "title" => $app['title'],
                    "name" => $name[$num_name - 1], //  $app['full_name'],
                    "email" => "",
                    "act" => $app['applicant_individual_account_type'],
                    "applicant_going_to_open" => $app['applicant_going_to_open'],
                    'today' => $today, //$this->sdb_julian_lib($this->call_sampaths_format($d, $m, $y)),
                    'day' => $d,
                    'empId' => $bdo_branch->emp,
                );

                //
                Log::info("old customer to new acccount");


                $this->create_account($para);
            } else {


                Log::info("CIF call new cusomter ");
                Log::info(json_encode($app));
                $name = explode(" ", $app['full_name']);
                $num_name = count($name);
                $nm_s = $this->doName($app['full_name']);
                $short_name = str_replace(".", "", $nm_s[3]);
                $s = $nm_s[2] . " " . $short_name;
                $short_name = substr($s, 0, 20);


                $pnumber = $this->prepare_mobile_number($app['primary_mobile_number']); // substr($app['primary_mobile_number'], 1);
                if ($app['secondary_mobile_number'] === null | $app['secondary_mobile_number'] === "null" | $app['secondary_mobile_number'] === NULL | $app['secondary_mobile_number'] === "NULL" | $app['secondary_mobile_number'] === "0" | $app['secondary_mobile_number'] === 0) {
                    $pnumber = $this->prepare_mobile_number($app['primary_mobile_number']); //substr($app['primary_mobile_number'], 1);
                } else {
                    $pnumber = $this->prepare_mobile_number($app['secondary_mobile_number']); //substr($app['secondary_mobile_number'], 1);
                }



                $onumber =  "";
                if ($work_place['telephone'] === null | $work_place['telephone'] === "null" | $work_place['telephone'] === NULL | $work_place['telephone'] === "NULL" | $work_place['telephone'] === "" | $work_place['telephone'] === " " | $work_place['telephone'] === "0" | $work_place['telephone'] === 0) {
                    $onumber =  $this->prepare_mobile_number($app['primary_mobile_number']); //substr($app['primary_mobile_number'], 1);
                } else {
                    $onumber =  $this->prepare_mobile_number($work_place['telephone']); //substr($work_place['telephone'], 1);
                }


                $param = array(
                    'initials_of_name' => $nm_s[3], //$app['display_name'],
                    'district' => substr($app['district'], 0, 20),
                    'house_numer' =>  $app['address1'],
                    'CURR_STREET' => substr($app['address2'], 0, 24), //$app['address2'],
                    'city' => substr($app['address3'], 0, 24),  //  $app['address3'],
                    'city_main' => substr($app['address4'], 0, 20), //  $app['address4'],
                    'secondary_number' =>  $pnumber, //  substr($app['secondary_mobile_number'], 1),
                    'primary_mobile_number' =>  $this->prepare_mobile_number($app['primary_mobile_number']), // substr($app['primary_mobile_number'], 1),
                    'surname' => $name[$num_name - 1],
                    'nic' =>  $app['nic'],
                    'sex' =>  $app['sex'],
                    'dob' =>  $this->sdb_julian_lib($this->call_sampaths_format($app['birth_day'], $app['birth_month'], $app['birth_year'])), //juliantojd($app['birth_month'], $app['birth_day'], $app['birth_year']),
                    'today' => $today, //$this->sdb_julian_lib($this->call_sampaths_format($d, $m, $y)),   //     "2020280", // juliantojd($m, $d, $y),  // for uat only
                    'telephone' => $onumber, //substr($work_place['telephone'], 1),
                    'ref_number' => $this->doRef(),
                    'short_name' => $short_name, // . " " . ,
                    'second_name' =>  $name[$num_name - 1], //, // $nm_s[1],
                    'title' => $app['title'] . ".",
                    'branch' => $this->prepare_number_two_digits((int) $bdo_branch->code),
                );



                Log::info($param);

                $url = "";
                if (env('APP_LIVE') === "yes") {
                    Log::alert('CIF APP L- ' . env('APP_LIVE') . " point -> " .  env('CIF_CREATE'));
                    $url =  env('CIF_CREATE');
                } elseif (env('APP_LIVE') === "no") {
                    Log::alert('CIF APP L- ' . env('APP_LIVE') . " point -> " . env('CIF_CREATE_TEST'));
                    $url =   env('CIF_CREATE_TEST');
                }

                $check = array(
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
                    "SHORT_NAME" => $param['short_name'], //"Perera ABC",
                    "SECOND_NAME" => "", //$param['second_name'],
                    "CURR_STREET" => $param['CURR_STREET'],
                    "BUSINESS_PHONE" =>  $this->default_val($param['telephone']), //$param['telephone'],
                    "STATUS" => 1,
                    "PRIMARY_OFFICER_COD" => "MOB",
                    "CURR_DISTRICT" => $param['city_main'],
                    "CITIZENSHIP_CODE" => "001",
                    "CURR_HOUSE_NBR" => substr($param['house_numer'], 0, 6),
                    "HOME_PHONE_NUMBER" => $this->default_val($param['secondary_number']),
                    "TIN_ACTIVITY_DATE" => $param['today'],  // current date // today  => UAT 2020280 //  october 6 2020
                    "CURR_POST_TOWN" => $param['city'],
                    "DATE" =>  $param['today'], // current date  date("m/d/Y h:i:s a", time()), //
                    "MARKET_SEQMENT" => "SOT",
                    "CURR_COUNTRY" => "Sri Lanka",
                    "BRANCH_NUMBER" => $param['branch'],
                    "ACCOUNT_TYPE" => "S",
                    "SOURCE_OF_DATA" => "",
                    "SEX" => $param['sex'],
                    "CUSTOMER_TYPE" => "001",
                    "FIRST_NAME" => $param['initials_of_name'],
                    "PREFERED_CUSTOMER" => "",
                    "ERROR_CODE" => "",
                    "SEQUENCE_NUMBER" => 1,
                    "LOCATION_CODE" => $this->dis($param['district']),
                    "CELLULAR_PHONE_NU" => $param['primary_mobile_number'],
                    "DATE_OF_BIRTH" => $param['dob'],
                    "SOCIO_ECONOMIC_GRO" => "001",
                    "PERSONAL_NONPERSONAL" => "P",
                    "CIF_NUMBER" => "",
                    "SURNAME" => $param['surname'], // "Perera",
                    "SIC_CODE" => "33",
                    "REFERENCE_NUMBER" =>  $param['ref_number'],
                    "CUSTOMER_CLASSIF" => "1",
                    "TIME" => time(),  /// current time iso time
                    "NATIONAL_ID_NUMBER" => $param['nic'],
                    "MOVED_IN_DATE" =>  $param['today'],   // "2020002"
                    "RACE" => "O",
                    "CUSTOMER_OPEN_DATE" => $param['today'],
                    "TITLE" => $param['title'], //"Mr.",
                    "CUST_DOC_ACTIVITY" => $param['today'],
                    "SOLICITABLE_CODE" => ""
                );

                Log::info('cif array');
                Log::info(json_encode($check));

                $responseB = Http::post($url, [
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
                    "SHORT_NAME" => $param['short_name'], //"Perera ABC",
                    "SECOND_NAME" => "", //$param['second_name'],
                    "CURR_STREET" => $param['CURR_STREET'],
                    "BUSINESS_PHONE" =>  $this->default_val($param['telephone']), //$param['telephone'],
                    "STATUS" => 1,
                    "PRIMARY_OFFICER_COD" => "MOB",
                    "CURR_DISTRICT" => $param['city_main'],
                    "CITIZENSHIP_CODE" => "001",
                    "CURR_HOUSE_NBR" => substr($param['house_numer'], 0, 6),
                    "HOME_PHONE_NUMBER" => $this->default_val($param['secondary_number']),
                    "TIN_ACTIVITY_DATE" => $param['today'],  // current date // today  => UAT 2020280 //  october 6 2020
                    "CURR_POST_TOWN" => $param['city'],
                    "DATE" => $param['today'],  // current date
                    "MARKET_SEQMENT" => "SOT",
                    "CURR_COUNTRY" => "Sri Lanka",
                    "BRANCH_NUMBER" => $param['branch'],
                    "ACCOUNT_TYPE" => "S",
                    "SOURCE_OF_DATA" => "",
                    "SEX" => $param['sex'],
                    "CUSTOMER_TYPE" => "001",
                    "FIRST_NAME" => $param['initials_of_name'],
                    "PREFERED_CUSTOMER" => "",
                    "ERROR_CODE" => "",
                    "SEQUENCE_NUMBER" => 1,
                    "LOCATION_CODE" =>  $this->dis($param['district']),
                    "CELLULAR_PHONE_NU" => $param['primary_mobile_number'],
                    "DATE_OF_BIRTH" => $param['dob'],
                    "SOCIO_ECONOMIC_GRO" => "001",
                    "PERSONAL_NONPERSONAL" => "P",
                    "CIF_NUMBER" => "",
                    "SURNAME" => $param['surname'], // "Perera",
                    "SIC_CODE" => "33",
                    "REFERENCE_NUMBER" =>  $param['ref_number'],
                    "CUSTOMER_CLASSIF" => "1",
                    "TIME" => time(),  /// current time iso time
                    "NATIONAL_ID_NUMBER" => $param['nic'],
                    "MOVED_IN_DATE" =>  $param['today'],   // "2020002"
                    "RACE" => "O",
                    "CUSTOMER_OPEN_DATE" => $param['today'],
                    "TITLE" => $param['title'], //"Mr.",
                    "CUST_DOC_ACTIVITY" => $param['today'],
                    "SOLICITABLE_CODE" => ""

                ]);




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

                            $appUpdated = Applicant::where("ref", $ref)->update(['existing_customer' => "true"]);

                            $cif_r_new =  $this->doRef_cif();

                            /*
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
                                "act" => $app['applicant_individual_account_type'],
                            );
*/

                            ////////////////

                            $para = array(
                                "cif" => $array['JSON']['Data']['cifNumber'], //$cif_Response['cif'],
                                "ref" => $cif_r_new,
                                "nic" => $nic,
                                "branch" => $bdo_branch->code,
                                "app_ref" => $app['ref'],
                                "mobile" => $param['primary_mobile_number'], //$this->prepare_mobile_number($ex_cus_mobile), //substr( $ex_cus_mobile , 2),
                                "title" => $app['title'],
                                "name" =>  $param['surname'], // $app['full_name'],
                                "email" => "",
                                "act" => $app['applicant_individual_account_type'],
                                "applicant_going_to_open" => $app['applicant_going_to_open'],
                                'today' => $today, //$this->sdb_julian_lib($this->call_sampaths_format($d, $m, $y)),
                                'day' => $d,
                                'empId' => $bdo_branch->emp,
                            );


                            //////////////////


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
        Log::info($request);
        Log::info($request->user_email);
        $user_email = $request->user_email;
        $product = $request->product;  // fd  // savings   // all


        $bdo_branch = DB::table('users')
            ->join('branch_codes', 'users.branch', '=', 'branch_codes.code')
            ->select('branch_codes.code', 'users.email')
            ->where('users.email', $user_email)
            ->first();
        //
        $user = $bdo_branch->code;
        Log::info('user code  ' . $user);

        if ($user === 0 | $user === "0") {
            Log::info('user code Central ' . $user);
            //current_branch_search

            if ((int)$request->app_status === 10 && $product === "all") {
                Log::info(' (int)$request->app_status === 10  && $product === all');
                $models = DB::table('applicant')
                    ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at', 'signed')
                    ->where('branch', (int)$request->current_branch_search)
                    ->orderBy('created_at', 'desc')
                    ->limit($request->end)->offset($request->start - 1)
                    ->get()
                    ->map(function ($item) {
                        return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->created_at, $item->signed];
                    })->toArray();


                Log::info($models);
                $ln = DB::table('applicant')
                    ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at', 'signed')
                    ->where('branch', (int)$request->current_branch_search)
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
            if ((int)$request->app_status === 10 && $product === "fd") {
                Log::info(' (int)$request->app_status === 10  && $product === fd');

                $models = DB::table('applicant')
                    ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at', 'signed')
                    ->where('applicant_going_to_open', 'Fixed Deposits')
                    ->where('branch', (int)$request->current_branch_search)
                    ->orderBy('created_at', 'desc')
                    ->limit($request->end)->offset($request->start - 1)
                    ->get()
                    ->map(function ($item) {
                        return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->created_at, $item->signed];
                    })->toArray();


                Log::info($models);
                $ln = DB::table('applicant')
                    ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at', 'signed')
                    ->where('applicant_going_to_open', 'Fixed Deposits')
                    ->where('branch', (int)$request->current_branch_search)
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
            if ((int)$request->app_status === 10 && $product === "savings") {

                Log::info('(int)$request->app_status === 10  && $product === savings');

                $models = DB::table('applicant')
                    ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at', 'signed')
                    ->where('applicant_going_to_open', '!=', 'Fixed Deposits')
                    ->where('branch', (int)$request->current_branch_search)
                    ->orderBy('created_at', 'desc')
                    ->limit($request->end)->offset($request->start - 1)
                    ->get()
                    ->map(function ($item) {
                        return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->created_at, $item->signed];
                    })->toArray();


                Log::info($models);
                $ln = DB::table('applicant')
                    ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at', 'signed')
                    ->where('applicant_going_to_open', '!=', 'Fixed Deposits')
                    ->where('branch', (int)$request->current_branch_search)
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

                Log::info('2nd else');

                if ($product === "savings") {
                    Log::info('savings only type');
                    $models = DB::table('applicant')
                        ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        ->where('applicant_going_to_open', '!=', 'Fixed Deposits')
                        ->where('done', (int)$request->app_status)
                        // ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                        // ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                        ->orderBy('created_at', 'desc')
                        ->limit($request->end)->offset($request->start - 1)
                        ->get()
                        ->map(function ($item) {
                            return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->created_at, $item->signed];
                        })->toArray();


                    Log::info($models);
                    $ln = DB::table('applicant')
                        ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        ->where('applicant_going_to_open', '!=', 'Fixed Deposits')
                        ->where('done', (int)$request->app_status)
                        // ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                        // ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
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
                if ($product === "fd") {
                    Log::info('fd only type');
                    $models = DB::table('applicant')
                        ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        ->where('applicant_going_to_open',  'Fixed Deposits')
                        ->where('done', (int)$request->app_status)
                        // ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                        // ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                        ->orderBy('created_at', 'desc')
                        ->limit($request->end)->offset($request->start - 1)
                        ->get()
                        ->map(function ($item) {
                            return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->created_at, $item->signed];
                        })->toArray();


                    Log::info($models);
                    $ln = DB::table('applicant')
                        ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        ->where('applicant_going_to_open',  'Fixed Deposits')
                        ->where('done', (int)$request->app_status)
                        // ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                        // ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
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
                    Log::info('anything else');
                    $models = DB::table('applicant')
                        ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        ->where('done', (int)$request->app_status)
                        // ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                        // ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                        ->orderBy('created_at', 'desc')
                        ->limit($request->end)->offset($request->start - 1)
                        ->get()
                        ->map(function ($item) {
                            return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->created_at, $item->signed];
                        })->toArray();


                    Log::info($models);
                    $ln = DB::table('applicant')
                        ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        ->where('done', (int)$request->app_status)
                        // ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                        // ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
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
        } else {

            Log::info('user code branch ' . $user);


            $models = DB::table('applicant')
                ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at', 'signed')
                ->where('branch', $user)
                /*  ->orWhere('nic', 'LIKE', $request->search . '%')
                ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                */
                ->orderBy('created_at', 'desc')
                ->limit($request->end)->offset($request->start - 1)
                ->get()
                ->map(function ($item) {
                    return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->created_at, $item->signed];
                })->toArray();


            Log::info($models);
            $ln = DB::table('applicant')
                ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at', 'signed')
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
