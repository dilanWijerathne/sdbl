<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Images;

class Multimedia extends Controller
{


    public function upload_nic(Request $request)
    {

        $nic =  $request->nic;
        $doctype =  $request->doctype;
        $image = $request->file('file');
        $image_name =  $nic . "-" . $doctype . "." . $image->extension();
        $image->move(public_path('images_lib'), $image_name);
    }
}
