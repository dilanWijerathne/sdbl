<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Images;

class Multimedia extends Controller
{


    public function upload_nic(Request $request)
    {

        $nic =  $request->nic;
        $ref =  $request->ref;
        $doctype =  $request->doctype;
        $image = $request->file('file');
        $image_name =  $nic . "-" . $doctype . "." . $image->extension();
        $image->move(public_path('images_lib'), $image_name);

        $img =  new Images;
        $img->applicant_ref_number = $ref;
        $img->file_name = $image_name;
        $img->file_type = $doctype;
        $img->nic = $nic;
        $img->agent =;
        $img->file_path =;
        $img->save();
    }
}
