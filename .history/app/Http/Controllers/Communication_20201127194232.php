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
use App\Models\Comment;
use App\Models\Msg;
use App\Models\Branches;
use App\Models\Utils;
use Illuminate\Support\Facades\DB;




class Communication extends Controller
{

    public function message(Request $request)
    {


        Log::info('Messaging');
        Log::info($request);

        $msg = new Msg;
        $msg->from_user = $request->input('from_user');
        $msg->to_user = $request->input('to_user');
        $msg->msg = $request->input('msg');
        $msg->nic = $request->input('nic');
        $msg->ref = $request->input('ref');
        $msg->status = 1;
        $msg->save();
    }


    public function message_get_ref(Request $request)
    {
        Log::info('Messages taking out');
        Log::info($request);

        $msg =  Msg::where('ref', $request->input('ref'))->get();
        return $msg;
    }

    public function comment(Request $request)
    {
        Log::info('commenting');
        Log::info($request);
        $com = new Comment;
        $com->bdo = $request->input('bdo');
        $com->from = $request->input('from');
        $com->ref = $request->input('ref');
        $com->msg = $request->input('msg');
        $com->status = 1;
        $com->save();
    }



    public function get_comments_of_application(Request $request)
    {

        $ref = $request->input('ref');

        $com = Comment::where('ref', $ref)->get();
        return $com;
    }
}
