<?php

namespace App\Http\Controllers\Bdo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Applicant;


class ApplicantController extends Controller
{
    public function ApplicantInitialSubmit(Request $request)
    {
    	$state ="";
    	$validation_state=false;
        // Validate the request...
/*
    	    $request->validate([
		        'title' => 'required|max:20',
		        'surname' => 'required',
		        'initials' => 'required',
		        'display_name' => 'required',
		        'full_name' => 'required',
		        'nic' => 'required',
		        'birth_year' => 'required',
		        'birth_month' => 'required',
		        'birth_day' => 'required',
		        'surname' => 'required',
		        'sex' => 'required',
		        'applicant_status' => 'required',
		        'applicant_going_to_open' => 'required',
		        'applicant_individual_account_type' => 'required',
		        'primary_mobile_number' => 'required',
		        'secondary_mobile_number' => 'required',
		        'email' => 'required',
		        'district' => 'required',
		        'same_nic_address' => 'required',
		    ]);


*/


        $applicant = new Applicant;


        if(isset($request->title)){
        	$applicant->title =  $request->title;
        }
        if(isset($request->surname)){
        	$applicant->surname =  $request->surname;	
        }
        if(isset($request->initials)){
        	 $applicant->initials =  $request->initials;
        }
        if(isset($request->display_name)){
        	$applicant->display_name =  $request->display_name;
        }
        if(isset($request->full_name)){
        	$applicant->full_name =  $request->full_name;
        }
        if(isset($request->nic)){
        	 $applicant->nic =  $request->nic;
        }
        if(isset($request->birth_year)){
        	 $applicant->birth_year =  $request->birth_year;
        }
        if(isset($request->birth_month)){
        	 $applicant->birth_month =  $request->birth_month;
        }
        if(isset($request->birth_day)){
        	 $applicant->birth_day =  $request->birth_day;
        }
        if(isset($request->sex)){
        	   $applicant->sex =  $request->sex;
        }
        if(isset($request->applicant_status)){
        	$applicant->applicant_status =  $request->applicant_status;
        }
        if(isset($request->applicant_going_to_open)){
        	$applicant->applicant_going_to_open =  $request->applicant_going_to_open;
        }
         if(isset($request->applicant_individual_account_type)){
        	 $applicant->applicant_individual_account_type =  $request->applicant_individual_account_type;
        }
         if(isset($request->primary_mobile_number)){
        	 $applicant->primary_mobile_number =  $request->primary_mobile_number;	
        }
         if(isset($request->secondary_mobile_number)){
        	$applicant->secondary_mobile_number	 =  $request->secondary_mobile_number;
        }
         if(isset($request->email)){
        	$applicant->email =  $request->email;
        }
         if(isset($request->address)){
        	 $applicant->address =  $request->address;
        }
         if(isset($request->district)){
        	 $applicant->district =  $request->district;
        }
        if(isset($request->same_nic_address)){
        	 $applicant->same_nic_address =  $request->same_nic_address;
        }

       try{ 
	        $applicant->save();
	        $state="1111";
       }catch(Exception $e){
       	$state="0000";
       	Log::info('ApplicantInitialSubmit : '.$e);
       	Log::info('ApplicantInitialSubmit Request: '.json_encode($request));
       }

       return response()->json(compact('state'),200);
    }
}
