<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Images;
use Exception;

class Multimedia extends Controller
{


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
            $image->move(public_path('images/lib'), $image_name);

            $img =  new Images;
            $img->applicant_ref_number = $ref;
            $img->file_name = $image_name;
            $img->file_type = $doctype;
            $img->nic = $nic;
            $img->agent = $agent;
            $img->file_path = 'images/lib/' . $image_name . "." . $ext;
            $img->save();
            echo  "saved";
        } catch (Exception $e) {
            Log::error($e);
        }
    }
}
