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
use App\Models\Branches;
use App\Models\Utils;
use Illuminate\Support\Facades\DB;




class Communication extends Controller
{

    public function comment(Request $request)
    {

        $com = new Comment;
        $com->bdo = $request->input('bdo');
        $com->from = $request->input('from');
        $com->ref = $request->input('ref');
        $com->msg = $request->input('msg');
        $com->status = $request->input('status');
        $com->save();
    }
}
