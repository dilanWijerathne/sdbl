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
use Illuminate\Support\Facades\DB;




class FDDash extends Controller
{
    public function getFDApplicants(Request $request)
    {


        $app = Applicant::all();


        Log::info('user check to view applicant data');
        Log::info($request);
        Log::info($request->user_email);
        $user_email = $request->user_email;

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

            if ((int)$request->app_status === 10) {
                $models = DB::table('applicant')
                    ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at')
                    ->where('branch', "Fixed Deposits")
                    ->where('branch', (int)$request->current_branch_search)
                    ->orderBy('created_at', 'desc')
                    ->limit($request->end)->offset($request->start - 1)
                    ->get()
                    ->map(function ($item) {
                        return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->created_at];
                    })->toArray();


                Log::info($models);
                $ln = DB::table('applicant')
                    ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at')
                    ->where('branch', "Fixed Deposits")
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
                $models = DB::table('applicant')
                    ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at')
                    //->where('branch', (int)$request->current_branch_search)
                    ->where('done', (int)$request->app_status)
                    ->where('branch', "Fixed Deposits")
                    // ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                    // ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                    ->orderBy('created_at', 'desc')
                    ->limit($request->end)->offset($request->start - 1)
                    ->get()
                    ->map(function ($item) {
                        return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->created_at];
                    })->toArray();


                Log::info($models);
                $ln = DB::table('applicant')
                    ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at')
                    //->where('branch', (int)$request->current_branch_search)
                    ->where('done', (int)$request->app_status)
                    ->where('branch', "Fixed Deposits")
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
        } else {

            Log::info('user code branch ' . $user);


            $models = DB::table('applicant')
                ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at')
                ->where('branch', $user)
                ->where('branch', "Fixed Deposits")
                /*  ->orWhere('nic', 'LIKE', $request->search . '%')
                ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                */
                ->orderBy('created_at', 'desc')
                ->limit($request->end)->offset($request->start - 1)
                ->get()
                ->map(function ($item) {
                    return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->created_at];
                })->toArray();


            Log::info($models);
            $ln = DB::table('applicant')
                ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'created_at')
                ->where('branch', $user)
                ->where('branch', "Fixed Deposits")
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
