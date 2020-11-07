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





    public function checkGender($v){
        $sex="";
        if($v=="Male"){
            $sex="M";
        }else{
            $sex="F";
        }
        return $sex;
    }



    public function create_new_Cif(Request $request)
    {

        //$nic  =  $request->nic;
        $response = Http::withHeaders([
            'Content-Type' => 'bar'
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
            "CURR_STREET" => "Colombo",
            "BUSINESS_PHONE" => "112832599",
            "STATUS" => "1",
            "PRIMARY_OFFICER_COD" => "MOB",
            "CURR_DISTRICT" => "Colombo",
            "CITIZENSHIP_CODE" => "001",
            "CURR_HOUSE_NBR" => "197",
            "HOME_PHONE_NUMBER" => "342231560",
            "TIN_ACTIVITY_DATE" => "2020002",
            "CURR_POST_TOWN" => "Narahenpita",
            "DATE" => "",
            "MARKET_SEQMENT" => "SOT",
            "CURR_COUNTRY" => "Sri Lanka",
            "BRANCH_NUMBER" => "56",
            "ACCOUNT_TYPE" => "S",
            "SOURCE_OF_DATA" => "",
            "SEX" =>  checkGender($request->sex),
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
            "REFERENCE_NUMBER" => rand(10, 999999999),
            "CUSTOMER_CLASSIF" => "1",
            "TIME" => "",
            "NATIONAL_ID_NUMBER" => $request->nic,
            "MOVED_IN_DATE" => "2020002",
            "RACE" => "1",
            "CUSTOMER_OPEN_DATE" => "2020002",
            "TITLE" =>  $request->title,
            "CUST_DOC_ACTIVITY" => "2020002",
            "SOLICITABLE_CODE" => ""

        ]);
    }



    nic,
    dob_day,
    dob_month,
    dob_year,
    sex,
    applicant_status,
    goin_to_open,
    account_type,
    title,
    full_name,
    primary_mobile,
    secondary_mobile,
    email,
    address,
    district,
    name_of_employer,
    position,
    work_address,
    telephone,
    salary,
    other_income,
    purpose_usage,
    source_of_funds,
    anticipated_volumes,
    source_of_wealth,
    pep,
    pep_relationship,
    nominees,
    front_res,
    rear_res,
    security_answer,
    displayName,
    monthly_income,
    pep_code,
    pupose_other_reason,
    source_funds_other_reason,
    wealth_other_reason,
    source_of_other_income,
    existing_customer,
    customer_cif,
    customer_type,
    date_of_birth,
    sex_core_bank,
    national_id_number,
    citizenship_code,
    profession_code,
    postal_code,
    branch_number,
    marital_status,
    socio_economic_group,
    personal_or_non_personal,
    salutation,
    market_seqment,
    employee_code,
    location_code,
    customer_classification,
    first_name,
    status,
    address_status,
    address_status_code,
    residence_proof,



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
