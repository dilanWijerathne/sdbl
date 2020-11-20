<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Images;
use Exception;

class Multimedia extends Controller
{


    public function upload_nic(Request $request)
    {

        try {
            $nic =  $request->nic;
            $agent =  $request->agent;
            $ref =  $request->ref;
            $doctype =  $request->doctype;
            $image = $request->file('file');
            $image_name =  $nic . "-" . $doctype . "." . $image->extension();
            $image->move(public_path('images'), $image_name);

            $img =  new Images;
            $img->applicant_ref_number = $ref;
            $img->file_name = $image_name;
            $img->file_type = $doctype;
            $img->nic = $nic;
            $img->agent = $agent;
            $img->file_path = 'images/' . $image_name . $image->extension();
            $img->save();
        } catch (Exception $e) {
            echo $e;
        }
    }
}
