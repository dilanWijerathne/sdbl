<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Application_dash extends Controller
{
    public function myOnboardings(Request $request)
    {
        $models = DB::table('applicant')
            ->select('ref', 'title',  'full_name', 'nic', 'primary_mobile_number', 'created_at', 'signed')
            ->where('bdo', $request->bdo)
            ->orderBy('created_at', 'desc')
            ->limit($request->end)->offset($request->start - 1)
            ->get()
            ->map(function ($item) {
                return [$item->ref, $item->title,  $item->full_name, $item->nic, $item->primary_mobile_number, $item->created_at, $item->signed];
            })->toArray();
    }
}
