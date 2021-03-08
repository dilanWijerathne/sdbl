<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Applicant;
use Illuminate\Support\Facades\DB;

class FilterController extends Controller
{
    /*
    grid filtering option from data tables comes here
    */

    public function getApplicants(Request $request)
    {


        $app = Applicant::all();

        Log::info('user check to view applicant data');
        Log::info($request);
        Log::info($request->user_email);
        $user_email = $request->user_email;
        $product = $request->product;  // fd  // savings   // all
        // current_branch_search

        $bdo_branch = DB::table('users')
            ->join('branch_codes', 'users.branch', '=', 'branch_codes.code')
            ->select('branch_codes.code', 'users.email')
            ->where('users.email', $user_email)
            ->first();
        //
        $user = $bdo_branch->code;
        Log::info('user code  ' . $user);

        /*
        $current_branch_search = "";
        if (isset($request->current_branch_search)) {
            $current_branch_search = $request->current_branch_search;
        }else{
            $current_branch_search = $user;
        }


*/


        if ($user === 0 | $user === "0") {
            Log::info('user code Central ' . $user);
            //current_branch_search
            if ($request->current_branch_search === "0") {
                // select all branhes from central ops
                if ($request->app_status === '10' && $product === "all") {
                    Log::info(' (int)$request->app_status === 10  && $product === all');
                    $models = DB::table('applicant')
                        ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        ->where([['branch', $request->current_branch_search], ['nic', 'LIKE', $request->search . '%']])
                        ->orderBy('updated_at', 'desc')
                        ->limit($request->end)->offset($request->start - 1)
                        ->get()
                        ->map(function ($item) {
                            return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->updated_at, $item->signed];
                        })->toArray();


                    Log::info($models);
                    $ln = DB::table('applicant')
                        ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        // ->where('branch', (int)$request->current_branch_search)
                        ->where([['branch', $request->current_branch_search], ['nic', 'LIKE', $request->search . '%']])
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
                if ($request->app_status === '10' && $product === "fd") {
                    Log::info(' (int)$request->app_status === 10  && $product === fd');

                    $models = DB::table('applicant')
                        ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        //->where('applicant_going_to_open', 'Fixed Deposits')
                        //->where('branch', (int)$request->current_branch_search)
                        ->where([['branch', $request->current_branch_search], ['applicant_going_to_open',  'Fixed Deposits'], ['nic', 'LIKE', $request->search . '%']])
                        ->orderBy('updated_at', 'desc')
                        ->limit($request->end)->offset($request->start - 1)
                        ->get()
                        ->map(function ($item) {
                            return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->updated_at, $item->signed];
                        })->toArray();


                    Log::info($models);
                    $ln = DB::table('applicant')
                        ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        // ->where('applicant_going_to_open', 'Fixed Deposits')
                        // ->where('branch', (int)$request->current_branch_search)
                        ->where([['branch', $request->current_branch_search], ['applicant_going_to_open',  'Fixed Deposits'], ['nic', 'LIKE', $request->search . '%']])
                        ->limit($request->end)->offset($request->start - 1)
                        ->count();
                    // $ln = $app->count();

                    $a = array(
                        "draw" => $request->draw,
                        "recordsTotal" => $ln,
                        "recordsFiltered" => $ln,
                        "data" => $models,

                    );

                    Log::info(json_encode($a));
                    echo json_encode($a);
                }
                if ($request->app_status === "10" && $product === "savings") {

                    Log::info('(int)$request->app_status === 10  && $product === savings');

                    $models = DB::table('applicant')
                        ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        //->where('applicant_going_to_open', '!=', 'Fixed Deposits')
                        // ->where('branch', (int)$request->current_branch_search)
                        ->where([['branch', $request->current_branch_search], ['applicant_going_to_open', '!=', 'Fixed Deposits'], ['nic', 'LIKE', $request->search . '%']])
                        ->orderBy('updated_at', 'desc')
                        ->limit($request->end)->offset($request->start - 1)
                        ->get()
                        ->map(function ($item) {
                            return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->updated_at, $item->signed];
                        })->toArray();


                    Log::info($models);
                    $ln = DB::table('applicant')
                        ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        // ->where('applicant_going_to_open', '!=', 'Fixed Deposits')
                        ->where([['branch', $request->current_branch_search], ['applicant_going_to_open', '!=', 'Fixed Deposits'], ['nic', 'LIKE', $request->search . '%']])
                        //  ->where('branch', (int)$request->current_branch_search)
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





                if ($request->app_status != "10" && $product === "savings") {
                    Log::info('savings only type');
                    $models = DB::table('applicant')
                        ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        //  ->where('applicant_going_to_open', '!=', 'Fixed Deposits')
                        //    ->where('done', (int)$request->app_status)
                        ->where([['done', (int)$request->app_status], ['branch', $request->current_branch_search], ['applicant_going_to_open', '!=', 'Fixed Deposits'], ['nic', 'LIKE', $request->search . '%']])
                        // ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                        // ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                        ->orderBy('updated_at', 'desc')
                        ->limit($request->end)->offset($request->start - 1)
                        ->get()
                        ->map(function ($item) {
                            return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->updated_at, $item->signed];
                        })->toArray();


                    Log::info($models);
                    $ln = DB::table('applicant')
                        ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        // ->where('applicant_going_to_open', '!=', 'Fixed Deposits')
                        ->where([['done', (int)$request->app_status], ['branch', $request->current_branch_search], ['applicant_going_to_open', '!=', 'Fixed Deposits'], ['nic', 'LIKE', $request->search . '%']])
                        // ->where('done', (int)$request->app_status)
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
                if ($request->app_status != "10" && $product === "fd") {
                    Log::info('fd only type');
                    $models = DB::table('applicant')
                        ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        // ->where('applicant_going_to_open',  'Fixed Deposits')
                        //->where('done', (int)$request->app_status)
                        // ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                        // ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                        ->where([['done', (int)$request->app_status], ['branch', $request->current_branch_search], ['applicant_going_to_open',  'Fixed Deposits'], ['nic', 'LIKE', $request->search . '%']])
                        ->orderBy('updated_at', 'desc')
                        ->limit($request->end)->offset($request->start - 1)
                        ->get()
                        ->map(function ($item) {
                            return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->updated_at, $item->signed];
                        })->toArray();


                    Log::info($models);
                    $ln = DB::table('applicant')
                        ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        //   ->where('applicant_going_to_open',  'Fixed Deposits')
                        //  ->where('done', (int)$request->app_status)
                        // ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                        // ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                        ->where([['done', (int)$request->app_status], ['branch', $request->current_branch_search], ['applicant_going_to_open',  'Fixed Deposits'], ['nic', 'LIKE', $request->search . '%']])
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

                if ($request->app_status != "10" && $product === "all") {
                    Log::info('fd only type');
                    $models = DB::table('applicant')
                        ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        // ->where('applicant_going_to_open',  'Fixed Deposits')
                        //->where('done', (int)$request->app_status)
                        // ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                        // ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                        ->where([['done', (int)$request->app_status], ['branch', $request->current_branch_search], ['nic', 'LIKE', $request->search . '%']])
                        ->orderBy('updated_at', 'desc')
                        ->limit($request->end)->offset($request->start - 1)
                        ->get()
                        ->map(function ($item) {
                            return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->updated_at, $item->signed];
                        })->toArray();


                    Log::info($models);
                    $ln = DB::table('applicant')
                        ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        //   ->where('applicant_going_to_open',  'Fixed Deposits')
                        //  ->where('done', (int)$request->app_status)
                        // ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                        // ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                        ->where([['done', (int)$request->app_status], ['branch', $request->current_branch_search],  ['nic', 'LIKE', $request->search . '%']])
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

                if ($request->app_status === '10' && $product === "all") {
                    Log::info(' (int)$request->app_status === 10  && $product === all');
                    $models = DB::table('applicant')
                        ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        ->where([['branch', $request->current_branch_search], ['nic', 'LIKE', $request->search . '%']])
                        ->orderBy('updated_at', 'desc')
                        ->limit($request->end)->offset($request->start - 1)
                        ->get()
                        ->map(function ($item) {
                            return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->updated_at, $item->signed];
                        })->toArray();


                    Log::info($models);
                    $ln = DB::table('applicant')
                        ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        // ->where('branch', (int)$request->current_branch_search)
                        ->where([['branch', $request->current_branch_search], ['nic', 'LIKE', $request->search . '%']])
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
                if ($request->app_status === '10' && $product === "fd") {
                    Log::info(' (int)$request->app_status === 10  && $product === fd');

                    $models = DB::table('applicant')
                        ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        //->where('applicant_going_to_open', 'Fixed Deposits')
                        //->where('branch', (int)$request->current_branch_search)
                        ->where([['branch', $request->current_branch_search], ['applicant_going_to_open',  'Fixed Deposits'], ['nic', 'LIKE', $request->search . '%']])
                        ->orderBy('updated_at', 'desc')
                        ->limit($request->end)->offset($request->start - 1)
                        ->get()
                        ->map(function ($item) {
                            return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->updated_at, $item->signed];
                        })->toArray();


                    Log::info($models);
                    $ln = DB::table('applicant')
                        ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        // ->where('applicant_going_to_open', 'Fixed Deposits')
                        // ->where('branch', (int)$request->current_branch_search)
                        ->where([['branch', $request->current_branch_search], ['applicant_going_to_open',  'Fixed Deposits'], ['nic', 'LIKE', $request->search . '%']])
                        ->limit($request->end)->offset($request->start - 1)
                        ->count();
                    // $ln = $app->count();

                    $a = array(
                        "draw" => $request->draw,
                        "recordsTotal" => $ln,
                        "recordsFiltered" => $ln,
                        "data" => $models,

                    );

                    Log::info(json_encode($a));
                    echo json_encode($a);
                }
                if ($request->app_status === "10" && $product === "savings") {

                    Log::info('(int)$request->app_status === 10  && $product === savings');

                    $models = DB::table('applicant')
                        ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        //->where('applicant_going_to_open', '!=', 'Fixed Deposits')
                        // ->where('branch', (int)$request->current_branch_search)
                        ->where([['branch', $request->current_branch_search], ['applicant_going_to_open', '!=', 'Fixed Deposits'], ['nic', 'LIKE', $request->search . '%']])
                        ->orderBy('updated_at', 'desc')
                        ->limit($request->end)->offset($request->start - 1)
                        ->get()
                        ->map(function ($item) {
                            return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->updated_at, $item->signed];
                        })->toArray();


                    Log::info($models);
                    $ln = DB::table('applicant')
                        ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        // ->where('applicant_going_to_open', '!=', 'Fixed Deposits')
                        ->where([['branch', $request->current_branch_search], ['applicant_going_to_open', '!=', 'Fixed Deposits'], ['nic', 'LIKE', $request->search . '%']])
                        //  ->where('branch', (int)$request->current_branch_search)
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





                if ($request->app_status != "10" && $product === "savings") {
                    Log::info('savings only type');
                    $models = DB::table('applicant')
                        ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        //  ->where('applicant_going_to_open', '!=', 'Fixed Deposits')
                        //    ->where('done', (int)$request->app_status)
                        ->where([['done', (int)$request->app_status], ['branch', $request->current_branch_search], ['applicant_going_to_open', '!=', 'Fixed Deposits'], ['nic', 'LIKE', $request->search . '%']])
                        // ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                        // ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                        ->orderBy('updated_at', 'desc')
                        ->limit($request->end)->offset($request->start - 1)
                        ->get()
                        ->map(function ($item) {
                            return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->updated_at, $item->signed];
                        })->toArray();


                    Log::info($models);
                    $ln = DB::table('applicant')
                        ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        // ->where('applicant_going_to_open', '!=', 'Fixed Deposits')
                        ->where([['done', (int)$request->app_status], ['branch', $request->current_branch_search], ['applicant_going_to_open', '!=', 'Fixed Deposits'], ['nic', 'LIKE', $request->search . '%']])
                        // ->where('done', (int)$request->app_status)
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
                if ($request->app_status != "10" && $product === "fd") {
                    Log::info('fd only type');
                    $models = DB::table('applicant')
                        ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        // ->where('applicant_going_to_open',  'Fixed Deposits')
                        //->where('done', (int)$request->app_status)
                        // ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                        // ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                        ->where([['done', (int)$request->app_status], ['branch', $request->current_branch_search], ['applicant_going_to_open',  'Fixed Deposits'], ['nic', 'LIKE', $request->search . '%']])
                        ->orderBy('updated_at', 'desc')
                        ->limit($request->end)->offset($request->start - 1)
                        ->get()
                        ->map(function ($item) {
                            return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->updated_at, $item->signed];
                        })->toArray();


                    Log::info($models);
                    $ln = DB::table('applicant')
                        ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        //   ->where('applicant_going_to_open',  'Fixed Deposits')
                        //  ->where('done', (int)$request->app_status)
                        // ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                        // ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                        ->where([['done', (int)$request->app_status], ['branch', $request->current_branch_search], ['applicant_going_to_open',  'Fixed Deposits'], ['nic', 'LIKE', $request->search . '%']])
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

                if ($request->app_status != "10" && $product === "all") {
                    Log::info('fd only type');
                    $models = DB::table('applicant')
                        ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        // ->where('applicant_going_to_open',  'Fixed Deposits')
                        //->where('done', (int)$request->app_status)
                        // ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                        // ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                        ->where([['done', (int)$request->app_status], ['branch', $request->current_branch_search], ['nic', 'LIKE', $request->search . '%']])
                        ->orderBy('updated_at', 'desc')
                        ->limit($request->end)->offset($request->start - 1)
                        ->get()
                        ->map(function ($item) {
                            return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->updated_at, $item->signed];
                        })->toArray();


                    Log::info($models);
                    $ln = DB::table('applicant')
                        ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        //   ->where('applicant_going_to_open',  'Fixed Deposits')
                        //  ->where('done', (int)$request->app_status)
                        // ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                        // ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                        ->where([['done', (int)$request->app_status], ['branch', $request->current_branch_search],  ['nic', 'LIKE', $request->search . '%']])
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

            /*else {
                    Log::info('anything else');
                    $models = DB::table('applicant')
                        ->select('ref', 'title',  'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        ->where('done', (int)$request->app_status)
                        // ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                        // ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                        ->orderBy('updated_at', 'desc')
                        ->limit($request->end)->offset($request->start - 1)
                        ->get()
                        ->map(function ($item) {
                            return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->updated_at, $item->signed];
                        })->toArray();


                    Log::info($models);
                    $ln = DB::table('applicant')
                        ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                        //->where('branch', (int)$request->current_branch_search)
                        ->where('done', (int)$request->app_status)
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
                */
        } else {

            /* non central ops but brancyhes users come here after  */

            Log::info('user code branch ' . $user);

            if ($product === "fd") {

                $models = DB::table('applicant')
                    ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                    //->where('branch', $user)
                    //->where('done', (int)$request->app_status)
                    ->where([['branch', $user], ['done', (int)$request->app_status], ['applicant_going_to_open',  'Fixed Deposits'], ['nic', 'LIKE', $request->search . '%']])
                    /*  ->orWhere('nic', 'LIKE', $request->search . '%')
                ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                */
                    ->orderBy('updated_at', 'desc')
                    ->limit($request->end)->offset($request->start - 1)
                    ->get()
                    ->map(function ($item) {
                        return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->updated_at, $item->signed];
                    })->toArray();


                Log::info($models);
                $ln = DB::table('applicant')
                    ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                    ->where([['branch', $user], ['done', (int)$request->app_status], ['applicant_going_to_open',  'Fixed Deposits'], ['nic', 'LIKE', $request->search . '%']])
                    //  ->where('branch', $user)
                    // ->where('done', (int)$request->app_status)
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
            } else {

                $models = DB::table('applicant')
                    ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                    //->where('branch', $user)
                    //->where('done', (int)$request->app_status)
                    ->where([['branch', $user], ['done', (int)$request->app_status], ['applicant_going_to_open', '!=', 'Fixed Deposits'], ['nic', 'LIKE', $request->search . '%']])
                    /*  ->orWhere('nic', 'LIKE', $request->search . '%')
                ->orWhere('primary_mobile_number', 'LIKE', '%' . $request->search . '%')
                ->orWhere('full_name', 'LIKE', '%' . $request->search . '%')
                */
                    ->orderBy('updated_at', 'desc')
                    ->limit($request->end)->offset($request->start - 1)
                    ->get()
                    ->map(function ($item) {
                        return [$item->ref, $item->title,  $item->full_name, $item->f_name, $item->nic, $item->primary_mobile_number, $item->updated_at, $item->signed];
                    })->toArray();


                Log::info($models);
                $ln = DB::table('applicant')
                    ->select('ref', 'title', 'full_name', 'f_name', 'nic', 'primary_mobile_number', 'updated_at', 'signed')
                    ->where([['branch', $user], ['done', (int)$request->app_status], ['applicant_going_to_open', '!=', 'Fixed Deposits'], ['nic', 'LIKE', $request->search . '%']])

                    ->limit($request->end)->offset($request->start - 1)
                    ->count();


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
}
