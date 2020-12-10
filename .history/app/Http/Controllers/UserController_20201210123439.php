<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

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
