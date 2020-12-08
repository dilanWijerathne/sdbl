<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Images;
use App\Models\Signatures;
use Exception;
use Illuminate\Support\Facades\DB;

class Multimedia extends Controller
{


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
                ->where('nic', 'LIKE', $request->search . '%')
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
                ->where('nic', 'LIKE', $request->search . '%')
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
                ->where('nic', 'LIKE', $request->search . '%')
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
                ->where('nic', 'LIKE', $request->search . '%')
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
