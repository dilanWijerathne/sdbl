<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Multimedia extends Controller
{


    public function upload_nic(Request $request){

        $nic =  $request->nic;
        $doctype =  $request->doctype;
        $image = $request->file('file');
        $image_name =  $."-". $doctype.".".$image->extension();
        $image->move(public_path('images'),$image_name);
    }
}
