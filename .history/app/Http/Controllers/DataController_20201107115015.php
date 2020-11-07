<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Symfony\Component\VarDumper\Cloner\Data;

use App\Models\Cif_response;

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



    public function create_account(Request $request)
    {

        $responseB = Http::post('http://10.100.32.72:7801/account_creation/v1/accountCreation', [



            "REFERENCE_NUMBER" => "TAP000000001000",

            "CIF_NUMBER" => "0001451462",

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
    }




    public function create_new_Cif(Request $request)
    {


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
            "SHORT_NAME" => "Perera ABC",
            "SECOND_NAME" => "Perera",
            "CURR_STREET" => "Kirula Road",
            "BUSINESS_PHONE" => "112832599",
            "STATUS" => "1",
            "PRIMARY_OFFICER_COD" => "MOB",
            "CURR_DISTRICT" => "Colombo",
            "CITIZENSHIP_CODE" => "001",
            "CURR_HOUSE_NBR" => "197",
            "HOME_PHONE_NUMBER" => "342231557",
            "TIN_ACTIVITY_DATE" => "2020002",
            "CURR_POST_TOWN" => "Narahenpita",
            "DATE" => "",
            "MARKET_SEQMENT" => "SOT",
            "CURR_COUNTRY" => "Sri Lanka",
            "BRANCH_NUMBER" => "56",
            "ACCOUNT_TYPE" => "S",
            "SOURCE_OF_DATA" => "",
            "SEX" => "M",
            "CUSTOMER_TYPE" => "001",
            "FIRST_NAME" => "ABC",
            "PREFERED_CUSTOMER" => "",
            "ERROR_CODE" => "1",
            "SEQUENCE_NUMBER" => "1",
            "LOCATION_CODE" => "1",
            "CELLULAR_PHONE_NU" => "773011572",
            "DATE_OF_BIRTH" => "1995101",
            "SOCIO_ECONOMIC_GRO" => "001",
            "PERSONAL_NONPERSONAL" => "P",
            "CIF_NUMBER" => "",
            "SURNAME" => "Perera",
            "SIC_CODE" => "33",
            "REFERENCE_NUMBER" => "CUS000000000751",
            "CUSTOMER_CLASSIF" => "1",
            "TIME" => "",
            "NATIONAL_ID_NUMBER" => "930523642V",
            "MOVED_IN_DATE" => "2020002",
            "RACE" => "1",
            "CUSTOMER_OPEN_DATE" => "2020002",
            "TITLE" => "Mr.",
            "CUST_DOC_ACTIVITY" => "2020002",
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
