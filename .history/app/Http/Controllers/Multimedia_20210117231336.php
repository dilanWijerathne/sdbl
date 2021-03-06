<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Images;
use App\Models\Signatures;
use App\Models\User;
use App\Models\Declaration;
use App\Models\Applicant;
use Illuminate\Support\Facades\DB;
use Exception;

class Multimedia extends Controller
{


    public function checkDeclaration(Request $request)
    {
        $email = $request->email;
        $rs = Declaration::where("email", $email)->first();
        if (isset($rs['email'])) {
            return $rs;
        } else {
            return 0;
        }
    }

    public function markDeclaration(Request $request)
    {
        $email = $request->email;

        $mark = new Declaration;
        $mark->email = $email;
        $mark->new_login = 1;
        $mark->agreed = 1;
        $mark->save();
    }

    public function delete_my_team_member(Request $request)
    {

        Log::info('user delete user by ');
        Log::info($request);
        $email = $request->email;

        try {
            $us = DB::table('users')->where('email',  $email)->delete();
            return $us;
        } catch (Exception $e) {
            Log::error($e);
        }
    }


    public function update_my_team_member(Request $request)
    {

        Log::info('update user details');
        Log::info($request);
        $name = $request->name;
        $email = $request->email;
        $current_email = $request->cemail;
        $mobile = $request->mobile;
        $role = $request->role;
        $branch = $request->branch;
        try {
            $us = User::where('email', $current_email)
                ->update(['email' => $email, 'name' => $name, 'mobile' => $mobile, 'role' => $role, 'branch' => $branch]);
            return $us;
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public function get_my_team_member(Request $request)
    {
        $user_email = $request->user_email;
        $us =  User::select('name', 'email', 'mobile', 'role', 'branch')->where("email", $user_email)->first();
        return $us;
    }

    public function get_myteam(Request $request)
    {

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

            $models = DB::table('users')
                ->select('name',  'email', 'mobile', 'role')
                ->where('name', 'LIKE', $request->search . '%')
                ->orWhere('email', 'LIKE', '%' . $request->search . '%')
                ->orWhere('mobile', 'LIKE', '%' . $request->search . '%')
                ->orderBy('created_at', 'desc')
                ->limit($request->end)->offset($request->start - 1)
                ->get()
                ->map(function ($item) {
                    return [$item->name,  $item->email, $item->mobile, $item->role];
                })->toArray();


            Log::info($models);
            $ln = DB::table('users')
                ->select('name',  'email', 'mobile', 'role')
                ->where('name', 'LIKE', $request->search . '%')
                ->orWhere('email', 'LIKE', '%' . $request->search . '%')
                ->orWhere('mobile', 'LIKE', '%' . $request->search . '%')
                ->limit($request->end)->offset($request->start - 1)
                ->count();
            // $ln = $app->count();

            $a = array(
                "draw" => $request->draw,
                "recordsTotal" => $ln,
                "recordsFiltered" => $ln,
                "data" => $models,

            );
        } else {

            $models = DB::table('users')
                ->select('name',  'email', 'mobile', 'role')
                ->where('branch', $user)
                ->where('name', 'LIKE', $request->search . '%')
                ->where('email', 'LIKE', '%' . $request->search . '%')
                ->where('mobile', 'LIKE', '%' . $request->search . '%')
                ->orderBy('created_at', 'desc')
                ->limit($request->end)->offset($request->start - 1)
                ->get()
                ->map(function ($item) {
                    return [$item->name,  $item->email, $item->mobile, $item->role];
                })->toArray();


            Log::info($models);
            $ln = DB::table('users')
                ->select('name',  'email', 'mobile', 'role')
                ->where('branch', $user)
                ->where('name', 'LIKE', $request->search . '%')
                ->where('email', 'LIKE', '%' . $request->search . '%')
                ->where('mobile', 'LIKE', '%' . $request->search . '%')
                ->limit($request->end)->offset($request->start - 1)
                ->count();
            // $ln = $app->count();

            $a = array(
                "draw" => $request->draw,
                "recordsTotal" => $ln,
                "recordsFiltered" => $ln,
                "data" => $models,

            );
        }




        echo json_encode($a);
    }


    public function sign(Request $request)
    {

        Log::info("Signature request");
        Log::info($request);

        try {
            $sign = new Signatures;
            $sign->signature =  $request->signature;
            $sign->nic = $request->nic;
            $sign->ref = $request->ref;
            $sign->agent = $request->agent;
            $sign->save();

            $app = Applicant::where("ref", $request->ref)->update(['signed' => 1]);


            echo  "signed";
        } catch (Exception $e) {
            Log::warning("error on signature request");
            Log::error($e);
        }
    }

    public function upload_nic(Request $request)
    {

        Log::info("image upload request");
        Log::info($request);
        try {
            $nic =  $request->nic;
            $agent =  $request->agent;
            $ref =  $request->ref;
            $doctype =  $request->doctype;
            $image = $request->file('file');
            $image_name =  $nic . "-" . $doctype . "." . $image->extension();
            $ext = $image->extension();
            $image->move(public_path('images/lib/' . $nic), $image_name);

            $img =  new Images;
            $img->applicant_ref_number = $ref;
            $img->file_name = $image_name;
            $img->file_type = $doctype;
            $img->nic = $nic;
            $img->agent = $agent;
            $img->file_path = 'images/lib/' . $nic . '/' . $image_name;
            $img->save();
            echo  "saved";
        } catch (Exception $e) {
            Log::error($e);
        }
    }
}
