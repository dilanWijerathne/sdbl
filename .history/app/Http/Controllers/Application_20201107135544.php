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


class Application extends Controller
{

    /**
     *  nic,
    dob_day,
    dob_month,
    dob_year,
    sex,
    applicant_status,
    goin_to_open,
    account_type,
    title,
    full_name,
    primary_mobile,
    secondary_mobile,
    email,
    address,
    district,
    name_of_employer,
    position,
    work_address,
    telephone,
    salary,
    other_income,
    purpose_usage,
    source_of_funds,
    anticipated_volumes,
    source_of_wealth,
    pep,
    pep_relationship,
    nominees,
    front_res,
    rear_res,
    security_answer,
    displayName,
    monthly_income,
    pep_code,
    pupose_other_reason,
    source_funds_other_reason,
    wealth_other_reason,
    source_of_other_income,
    existing_customer,
    customer_cif,
    customer_type,
    date_of_birth,
    sex_core_bank,
    national_id_number,
    citizenship_code,
    profession_code,
    postal_code,
    branch_number,
    marital_status,
    socio_economic_group,
    personal_or_non_personal,
    salutation,
    market_seqment,
    employee_code,
    location_code,
    customer_classification,
    first_name,
    status,
    address_status,
    address_status_code,
    residence_proof,
    f_name,
    s_name,
     *
     */
    public function new_customer(Request $request)
    {
        //        $age = $request->input('age');
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
        $front_res = $request->input('front_res');
        $rear_res = $request->input('rear_res');
        $security_answer = $request->input('security_answer');
        $displayName = $request->input('displayName');
        $monthly_income = $request->input('monthly_income');
        $pep_code = $request->input('pep_code');
        $pupose_other_reason = $request->input('pupose_other_reason');
        $source_funds_other_reason = $request->input('source_funds_other_reason');
        $wealth_other_reason = $request->input('wealth_other_reason');
        $source_of_other_income = $request->input('source_of_other_income');
        $existing_customer = $request->input('existing_customer');
        $customer_cif = $request->input('customer_cif');
        $customer_type = $request->input('customer_type');
        $date_of_birth = $request->input('date_of_birth');
        $sex_core_bank = $request->input('sex_core_bank');
        $national_id_number = $request->input('national_id_number');
        $citizenship_code = $request->input('citizenship_code');
        $profession_code = $request->input('profession_code');
        $postal_code = $request->input('postal_code');
        $branch_number = $request->input('branch_number');
        $marital_status = $request->input('marital_status');
        $socio_economic_group = $request->input('socio_economic_group');
        $personal_or_non_personal = $request->input('personal_or_non_personal');
        $salutation = $request->input('salutation');
        $market_seqment = $request->input('market_seqment');
        $employee_code = $request->input('employee_code');
        $location_code = $request->input('location_code');
        $customer_classification = $request->input('customer_classification');
        $first_name = $request->input('first_name');
        $status = $request->input('status');
        $address_status = $request->input('address_status');
        $address_status_code = $request->input('address_status_code');
        $residence_proof = $request->input('residence_proof');
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
        $applicant->existing_customer = $existing_customer;
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
