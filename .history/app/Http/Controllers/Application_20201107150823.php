<?php

namespace App\Http\Controllers;

use App\Models\ApplicationConfigs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Kyc;
use App\Models\Account;
use App\Models\Work_place;
use App\Models\Nominee;
use App\Models\Images;
use App\Models\Applicant;
use App\Exceptions\Handler;
use Exception;

class Application extends Controller
{
    public function new_customer(Request $request)
    {

        Log::log('1', 'request to new cusotmer');
        Log::info($request);

        try {
            $nic = $request->input('nic');
            $dob_day = $request->input('dob_day');
            $dob_month = $request->input('dob_month');
            $dob_year = $request->input('dob_year');
            $sex = $request->input('sex');
            $applicant_status = $request->input('applicant_status');
            $goin_to_open = $request->input('goin_to_open');
            $account_type = $request->input('account_type');
            $title = $request->input('title');
            $full_name = $request->input('full_name');
            $primary_mobile = $request->input('primary_mobile');
            $secondary_mobile = $request->input('secondary_mobile');
            $email = $request->input('email');
            $address = $request->input('address');
            $district = $request->input('district');
            $name_of_employer = $request->input('name_of_employer');
            $position = $request->input('position');
            $work_address = $request->input('work_address');
            $telephone = $request->input('telephone');
            $salary = $request->input('salary');
            $other_income = $request->input('other_income');
            $purpose_usage = $request->input('purpose_usage');
            $source_of_funds = $request->input('source_of_funds');
            $anticipated_volumes = $request->input('anticipated_volumes');
            $source_of_wealth = $request->input('source_of_wealth');
            $pep = $request->input('pep');
            $pep_relationship = $request->input('pep_relationship');
            $nominees = $request->input('nominees');

            $security_answer = $request->input('security_answer');
            $displayName = $request->input('displayName');

            $source_of_other_income = $request->input('source_of_other_income');

            $f_name = $request->input('f_name');
            $s_name = $request->input('s_name');




            $nominee = new Nominee;
            $nominee->json = $nominees;
            $nominee->save();

            $work = new Work_place;
            $work->name = $name_of_employer;
            $work->address = $work_address;
            $work->position = $position;
            $work->telephone = $telephone;
            $work->income_monthly = $salary;
            $work->other_income = $other_income;
            $work->source_other_income = $source_of_other_income;

            $work->save();


            $applicant = new Applicant;

            $applicant->title = $title;
            $applicant->surname = $s_name;
            $applicant->initials = "";
            $applicant->display_name = $displayName;
            $applicant->full_name = $full_name;
            $applicant->f_name = $f_name;
            $applicant->nic = $nic;
            $applicant->birth_year = $dob_year;
            $applicant->birth_month = $dob_month;
            $applicant->birth_day = $dob_day;
            $applicant->sex = $sex;
            $applicant->applicant_status = $applicant_status;
            $applicant->applicant_going_to_open = $goin_to_open;
            $applicant->applicant_individual_account_type = $account_type;
            $applicant->primary_mobile_number = $primary_mobile;
            $applicant->secondary_mobile_number = $secondary_mobile;
            $applicant->email = $email;
            $applicant->address = $address;
            $applicant->living_place_dif = $title;     // recheck
            $applicant->district = $district;
            $applicant->same_nic_address = "";
            $applicant->security_question = $security_answer;
            // $applicant->existing_customer = $existing_customer;
            $applicant->save();


            $kyc_json = array(
                'pupose' => $purpose_usage,
                'source_funds' => $source_of_funds,
                'anticipated_volume' => $anticipated_volumes,
                'source_wealth' => $source_of_wealth,
                'pep' => $pep,
                'pep_relationsip' => $pep_relationship
            );
            $kyc = new Kyc;
            $kyc->json = json_encode($kyc_json);
            $kyc->save();
        } catch (Exception $e) {
            Log::error($e);
        }

        echo "Application data been recorded !";
    }




    // account applicants current staus needs to get base on their sex and age
    public function applicant_status(Request $request)
    {
        $age = $request->input('age');
        $sex = $request->input('sex');

        Log::info($request);
        Log::info('applicant age  : ' . $age . "  sex : " . $sex);

        $statuses = null;
        if ($age > 18) {
            if ($sex == "Male") {
                $statuses =  ApplicationConfigs::select('id', 'area', 'val', 'description')->where('area', 'applicant')->where('id', '!=', 250)->where('id', '!=', 251)->get();
            }
            if ($sex == "Female") {
                $statuses =  ApplicationConfigs::select('id', 'area', 'val', 'description')->where('area', 'applicant')->where('id', '!=', 250)->get();
            }
        } elseif ($age < 18) {
            $statuses =  ApplicationConfigs::select('id', 'area', 'val', 'description')->where('id',  250)->get();
        }

        echo json_encode($statuses);
    }


    /**
     * accounts types that applicant going to open
     *
     */

    public function goin_to_open(Request $request)
    {
        $applicant_status = $request->input('status');

        Log::info($request);

        $statuses = null;
        $statuses =  ApplicationConfigs::select('id', 'area', 'val', 'description')->where('area', 'account_type')->get();




        echo json_encode($statuses);
    }



    /**
     *
     * if customer select individula account types in the goin to open to type this has to return accordingly
     *
     */

    public function individual_account_types(Request $request)
    {
        $applicant_status = $request->input('status');

        $age = $request->input('age');
        $sex = $request->input('sex');

        Log::info($request);

        $statuses = null;
        $statuses =  ApplicationConfigs::select('id', 'area', 'val', 'description')->where('area', 'individual_account_type')->get();

        echo json_encode($statuses);
    }
}
