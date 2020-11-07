<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;



class DataController extends Controller
{


    public function check_applicant_with_current_banking_data(Request $request)
    {
        //  "http://10.100.32.72:7801/cif/v1/CustomerInformation/?NIC=       782900013V  ",

        if (isset($request->nic)) {
            $nic  =  $request->nic;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "http://10.100.32.72:7801/cif/v1/CustomerInformation/?NIC=" . $nic,
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
            echo $response;
        } else {

            $k = array("code" => "1150", "status" => false, "message" => "NIC  does not exist.", "serverTime" => date("Y-m-d h:i:sa"));

            // $age = array("Peter"=>"35", "Ben"=>"37", "Joe"=>"43");

            echo  json_encode($k);
        }
    }







    public function create_new_Cif(Request $request)
    {

        Log::info($request);

        $responseB = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post('http://10.100.32.72:7801/new_cif_creation/v1/newCifCreation', [

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
            "SHORT_NAME" => $request->initial_name,
            "SECOND_NAME" => $request->surname,
            "CURR_STREET" => $request->street,
            "BUSINESS_PHONE" => $request->work_phone,
            "STATUS" => "1",
            "PRIMARY_OFFICER_COD" => "MOB",
            "CURR_DISTRICT" => $request->district,
            "CITIZENSHIP_CODE" => "001",
            "CURR_HOUSE_NBR" => $request->house_number,
            "HOME_PHONE_NUMBER" => $request->home_phone,
            "TIN_ACTIVITY_DATE" => $request->ttn_active_date,
            "CURR_POST_TOWN" => $request->city,
            "DATE" => $request->today,
            "MARKET_SEQMENT" => "SOT",
            "CURR_COUNTRY" => "Sri Lanka",
            "BRANCH_NUMBER" => "$request->branch_code",
            "ACCOUNT_TYPE" => "S",
            "SOURCE_OF_DATA" => "mobile app onboard",
            "SEX" =>  $request->sex,
            "CUSTOMER_TYPE" => "001",
            "FIRST_NAME" => $request->f_name,
            "PREFERED_CUSTOMER" => "",
            "ERROR_CODE" => "1",
            "SEQUENCE_NUMBER" => "1",
            "LOCATION_CODE" => "1",
            "CELLULAR_PHONE_NU" => $request->primary_mobile,
            "DATE_OF_BIRTH" => $request->dob,
            "SOCIO_ECONOMIC_GRO" => "001",
            "PERSONAL_NONPERSONAL" => "P",
            "CIF_NUMBER" => "",
            "SURNAME" => $request->surname,
            "SIC_CODE" => "33",
            "REFERENCE_NUMBER" => rand(10, 999999999),
            "CUSTOMER_CLASSIF" => "1",
            "TIME" => "",
            "NATIONAL_ID_NUMBER" => $request->nic,
            "MOVED_IN_DATE" => "",
            "RACE" => "1",
            "CUSTOMER_OPEN_DATE" => $request->open_date,
            "TITLE" =>  $request->title,
            "CUST_DOC_ACTIVITY" => "2020002",
            "SOLICITABLE_CODE" => ""

        ]);

        return response()->json(compact('responseB'), 200);
    }





    public function open()
    {
        $data = "This data is open and can be accessed without the client being authenticated";

        return response()->json(compact('data'), 200);
    }

    public function closed()
    {
        $data = "Only authorized users can see this";
        return response()->json(compact('data'), 200);
    }
}
