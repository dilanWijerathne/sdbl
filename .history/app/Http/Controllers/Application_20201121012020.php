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
use App\Models\Cif_Response;
use App\Exceptions\Handler;
use Exception;

class Application extends Controller
{




    public function new_customer(Request $request)
    {

        Log::info('request to new cusotmer');
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

            $existing_customer = $request->input('existing_customer');  // booloan
            $living_place_dif = $request->input('living_place_dif');  // dif
            $customer_cif = $request->input('existing_customer_cif');

            $ref = $request->input('ref');

            //data.append("existing_customer_cif", customer_cif);




            //$string = '265 9959 b9659';
            $s_con = ucfirst($salary);
            $salbar = ucwords(strtolower($s_con));
            $salary_trimmed = preg_replace('/\s+/', '',  $salbar);


            $months_names = [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December",
            ];

            $sex_f = array(
                "Male" => "M",
                "Female" => "F",
            );

            $dob_month_p = array_search($dob_month, $months_names) + 1;


            $nominee = new Nominee;
            $nominee->json = $nominees;
            $nominee->applicant_nic = $nic;
            $nominee->ref_number = $ref;
            $nominee->save();

            $work = new Work_place;
            $work->name = $name_of_employer;
            $work->address = $work_address;
            $work->position = $position;
            $work->telephone = $telephone;
            $work->income_monthly = $salary_trimmed;
            $work->other_income = $other_income;
            $work->applicant_nic = $nic;
            $work->source_other_income = $source_of_other_income;
            $work->ref = $ref;

            $work->save();


            $applicant = new Applicant;

            $applicant->ref = $ref;
            $applicant->title = $title;
            $applicant->surname = $s_name;
            $applicant->initials = "";
            $applicant->display_name = $displayName;
            $applicant->full_name = $full_name;
            $applicant->f_name = $f_name;
            $applicant->nic = $nic;
            $applicant->birth_year = $dob_year;
            $applicant->birth_month = $dob_month_p;
            $applicant->birth_day = $dob_day;
            $applicant->sex = $sex_f[$sex];
            $applicant->applicant_status = $applicant_status;
            $applicant->applicant_going_to_open = $goin_to_open;
            $applicant->applicant_individual_account_type = $account_type;
            $applicant->primary_mobile_number = $primary_mobile;
            $applicant->secondary_mobile_number = $secondary_mobile;
            $applicant->email = $email;
            $applicant->address = $address;
            $applicant->living_place_dif =  $living_place_dif;    // dif
            $applicant->district = $district;
            $applicant->same_nic_address = "";
            $applicant->security_question = $security_answer;
            $applicant->existing_customer = $existing_customer;  // bolean
            $applicant->save();


            $kyc_json = array(
                'pupose' => $purpose_usage,   // convert these json string to array
                'source_funds' => $source_of_funds,
                'anticipated_volume' => $anticipated_volumes,
                'source_wealth' => $source_of_wealth,
                'pep' => $pep,
                'pep_relationsip' => $pep_relationship
            );
            $kyc = new Kyc;
            $kyc->json = json_encode($kyc_json);
            $kyc->nic = $nic;
            $kyc->ref_number = $ref;
            $kyc->save();


            if ($existing_customer === "true") {
                $cif = new Cif_Response;
                $cif->nic = $nic;
                $cif->cif = $customer_cif;
                $cif->save();
            }
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
        $statuses =  ApplicationConfigs::select('id', 'area', 'val', 'description')->where('area', 'account_type')->where('active',  1)->get();




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
        $statuses =  ApplicationConfigs::select('id', 'area', 'val', 'description')->where('area', 'individual_account_type')->where('active',  1)->get();

        echo json_encode($statuses);
    }
}
