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




    public function kt()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://10.100.32.72:7801/new_cif_creation/v1/newCifCreation",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{  \"FIELD1\":\"0\",\r\n\r\n     \"FIELD2\":\"0\",\r\n\r\n    \"FIELD3\":\"0\",\r\n\r\n    \"FIELD4\":\"0\",\r\n\r\n    \"FIELD5\":\"0\",\r\n\r\n    \"FIELD6\":\"\",\r\n\r\n    \"FIELD7\":\"\",\r\n\r\n    \"FIELD8\":\"\",\r\n\r\n    \"FIELD9\":\"\",\r\n\r\n    \"FIELD10\":\"\",\r\n\r\n    \"MARITAL_STATUS\":\"S\",\r\n\r\n    \"USER_ID\":\"\",\r\n\r\n    \"SHORT_NAME\":\"Perera ABC\",\r\n\r\n    \"SECOND_NAME\":\"Perera\",\r\n\r\n    \"CURR_STREET\":\"Kirula Road\",\r\n\r\n    \"BUSINESS_PHONE\":\"112832599\",\r\n\r\n    \"STATUS\":\"1\",\r\n\r\n    \"PRIMARY_OFFICER_COD\":\"MOB\",\r\n\r\n    \"CURR_DISTRICT\":\"Colombo\",\r\n\r\n    \"CITIZENSHIP_CODE\":\"001\",\r\n\r\n    \"CURR_HOUSE_NBR\":\"197\",\r\n\r\n    \"HOME_PHONE_NUMBER\":\"342231557\",\r\n\r\n    \"TIN_ACTIVITY_DATE\":\"2020002\",\r\n\r\n    \"CURR_POST_TOWN\":\"Narahenpita\",\r\n\r\n    \"DATE\":\"\",\r\n\r\n    \"MARKET_SEQMENT\":\"SOT\",\r\n\r\n    \"CURR_COUNTRY\":\"Sri Lanka\",\r\n\r\n    \"BRANCH_NUMBER\":\"56\",\r\n\r\n    \"ACCOUNT_TYPE\":\"S\",\r\n\r\n    \"SOURCE_OF_DATA\":\"\",\r\n\r\n    \"SEX\":\"M\",\r\n\r\n    \"CUSTOMER_TYPE\":\"001\",\r\n\r\n    \"FIRST_NAME\":\"ABC\",\r\n\r\n    \"PREFERED_CUSTOMER\":\"\",\r\n\r\n    \"ERROR_CODE\":\"1\",\r\n\r\n    \"SEQUENCE_NUMBER\":\"1\",\r\n\r\n    \"LOCATION_CODE\":\"1\",\r\n\r\n    \"CELLULAR_PHONE_NU\":\"773011572\",\r\n\r\n    \"DATE_OF_BIRTH\":\"1995101\",\r\n\r\n    \"SOCIO_ECONOMIC_GRO\":\"001\",\r\n\r\n    \"PERSONAL_NONPERSONAL\":\"P\",\r\n\r\n    \"CIF_NUMBER\":\"\",\r\n\r\n    \"SURNAME\":\"Perera\",\r\n\r\n    \"SIC_CODE\":\"33\",\r\n\r\n    \"REFERENCE_NUMBER\":\"CUS000000000599\",\r\n\r\n    \"CUSTOMER_CLASSIF\":\"1\",\r\n\r\n    \"TIME\":\"\",\r\n\r\n    \"NATIONAL_ID_NUMBER\":\"900103585v\",\r\n\r\n    \"MOVED_IN_DATE\":\"2020002\",\r\n\r\n    \"RACE\":\"1\",\r\n\r\n    \"CUSTOMER_OPEN_DATE\":\"2020002\",\r\n\r\n    \"TITLE\":\"Mr.\",\r\n\r\n    \"CUST_DOC_ACTIVITY\":\"2020002\",\r\n\r\n    \"SOLICITABLE_CODE\":\"\"\r\n\r\n}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }



    public function kk(Request $request)
    {



        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://10.100.32.72:7801/new_cif_creation/v1/newCifCreation",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{  \"FIELD1\":\"0\",\r\n\r\n
      \"FIELD2\":\"0\",\r\n\r\n
        \"FIELD3\":\"0\",\r\n\r\n
          \"FIELD4\":\"0\",\r\n\r\n
           \"FIELD5\":\"0\",\r\n\r\n
            \"FIELD6\":\"\",\r\n\r\n
             \"FIELD7\":\"\",\r\n\r\n
             \"FIELD8\":\"\",\r\n\r\n
             \"FIELD9\":\"\",\r\n\r\n
              \"FIELD10\":\"\",\r\n\r\n
               \"MARITAL_STATUS\":\"S\",\r\n\r\n
                \"USER_ID\":\"\",\r\n\r\n
                \"SHORT_NAME\":\"'.$request->initial_name.'\",\r\n\r\n
                \"SECOND_NAME\":\"'.$request->surname.'\",\r\n\r\n
                \"CURR_STREET\":\"'.$request->street.'\",\r\n\r\n
                \"BUSINESS_PHONE\":\"'.$request->work_phone.'\",\r\n\r\n
                \"STATUS\":\"1\",\r\n\r\n
                \"PRIMARY_OFFICER_COD\":\"MOB\",\r\n\r\n
                \"CURR_DISTRICT\":\"'.$request->district.'\",\r\n\r\n
                \"CITIZENSHIP_CODE\":\"001\",\r\n\r\n
                \"CURR_HOUSE_NBR\":\"'.$request->house_number.'\",\r\n\r\n
                \"HOME_PHONE_NUMBER\":\"'.$request->home_phone.'\",\r\n\r\n
                \"TIN_ACTIVITY_DATE\":\"'.$request->ttn_active_date.'\",\r\n\r\n
                \"CURR_POST_TOWN\":\"'.$request->city.'\",\r\n\r\n
                \"DATE\":\"' .$request->today. '\",\r\n\r\n
                \"MARKET_SEQMENT\":\"SOT\",\r\n\r\n
                \"CURR_COUNTRY\":\"Sri Lanka\",\r\n\r\n
                \"BRANCH_NUMBER\":\"' .$request->branch_code. '\",\r\n\r\n
                \"ACCOUNT_TYPE\":\"S\",\r\n\r\n
                \"SOURCE_OF_DATA\":\"\",\r\n\r\n
                \"SEX\":\"' .$request->sex. '\",\r\n\r\n
                \"CUSTOMER_TYPE\":\"001\",\r\n\r\n
                \"FIRST_NAME\":\"' .$request->f_name. '\",\r\n\r\n
                \"PREFERED_CUSTOMER\":\"\",\r\n\r\n
                \"ERROR_CODE\":\"1\",\r\n\r\n
                \"SEQUENCE_NUMBER\":\"1\",\r\n\r\n
                \"LOCATION_CODE\":\"1\",\r\n\r\n
                \"CELLULAR_PHONE_NU\":\"' .$request->primary_mobile. '\",\r\n\r\n
                \"DATE_OF_BIRTH\":\"'.$request->dob.'\",\r\n\r\n
                \"SOCIO_ECONOMIC_GRO\":\"001\",\r\n\r\n
                \"PERSONAL_NONPERSONAL\":\"P\",\r\n\r\n
                \"CIF_NUMBER\":\"\",\r\n\r\n
                \"SURNAME\":\"'.$request->surname.'\",\r\n\r\n
                \"SIC_CODE\":\"33\",\r\n\r\n
                \"REFERENCE_NUMBER\":\"'.$request->ref.'\",\r\n\r\n
                \"CUSTOMER_CLASSIF\":\"1\",\r\n\r\n
                \"TIME\":\"\",\r\n\r\n
                \"NATIONAL_ID_NUMBER\":\"'.$request->nic.'\",\r\n\r\n
                \"MOVED_IN_DATE\":\"2020002\",\r\n\r\n
                \"RACE\":\"1\",\r\n\r\n
                \"CUSTOMER_OPEN_DATE\":\"'.$request->open_date.'\",\r\n\r\n
                \"TITLE\":\"'.$request->title.'\",\r\n\r\n
                \"CUST_DOC_ACTIVITY\":\"2020002\",\r\n\r\n
                \"SOLICITABLE_CODE\":\"\"\r\n\r\n}\r\n\r\n ",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }


    public function create_new_Cif(Request $request)
    {


         $vl =    array(
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
            "BRANCH_NUMBER" => $request->branch_code,
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
        )
        Log::info($request);

        $responseB = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post('http://10.100.32.72:7801/new_cif_creation/v1/newCifCreation', [

          json_encode(vl)

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
