<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Utils
{


    public static function smsreg($param)
    {

        $response = Http::post('http://10.100.32.72:7801/smsregistration/v1/CreateSmsRegistration', [

            "application_SessionId" =>  uniqid('SDB-'), //"20201021-SDBL-0002",
            "application_Code" => "SDB",
            "application_Password" => "0cc175b9c0f1b6a831c399e269772661",
            "customerid" => $param['cusid'], // "10785",
            "customer_account" => $param['cusid'],
            "customer_mobile" => "94" . $param['cusid'],
            "customer_nic" => $param['nic'],
            "customer_name" => $param['title'] . " " . $param['name'],
            "customer_address" => "Sri Lanka",
            "area_code" => "00010",
            "branch_code" => $param['branch'],
            "isstaff" => "No",
            "customer_email" => $param['email'],
            "customer_categorycode" => "0004",
            "customer_epfno" => "00000",
            "device_type" => "AR",
            "imei" => "",
            "feeprofilecode" => ""

        ]);

        Log::info('sms reg ');
        Log::info($response);

        return  $response;
    }




    public static function currentUser($access_token)
    {


        $response = Http::withToken($access_token)->get("http://10.101.6.198/sdbl/api/user", null);

        $ar = $response->body();
        $array = json_decode($ar, true);

        Log::info('user taken');
        Log::info($array);
        $state = "";


        $state = $array['branch'];




        return   $state;
    }
}
