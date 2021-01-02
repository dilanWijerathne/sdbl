<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'mobile' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'branch' => 'required|string|max:255',
            'emp' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'mobile' => $request->get('mobile'),
            "role" => $request->get('role'),
            "branch" => $request->get('branch'),
            "emp" => $request->get('emp'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);
    }

    public function reset_pass_user(Request $request)
    {
        $email = $request->email;
        $pass =  $request->password;
        $us = User::where('email', $email)
            ->update(['password' => Hash::make($pass)]);
        return  "You have successfully changed your password";
    }

    public function request_reset_password(Request $request)
    {
        $email = $request->email;
        Log::info('Password CR');
        Log::info($request);

        $pass = rand(10000, 99999);

        $usr = User::where("email", $email)->latest()->first();
        $mobile_number =  $usr['mobile'];
        Log::info($usr);

        $us = User::where('email', $email)
            ->update(['password' => Hash::make($pass)]);

        $url =   env('SMS_SEND');
        $mesg = "Your SDB Onboarding app password has been reset successfully! Your new temporary password is : " . $pass . "  Please make sure to change the password.";
        Log::info($mesg);
        $response = Http::post($url, [
            'mobalertid' => "0",
            'mobalerttype' => "SINGLE",
            'mobile' => "94" . substr($mobile_number, 1),
            'groupcode' => "",
            'message' =>  $mesg,
            'status' => "QUED",
        ]);
        Log::info($response);
        return $response;
    }

    public function getAuthenticatedUser()
    {





        try {
            $user = JWTAuth::parseToken()->authenticate();
            //  return json_encode($user);
            return response()->json($user, 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }



    public function open()
    {
        echo "Dilan";
    }
}
