<?php

namespace App\Http\Controllers;

use App\Models\ApplicationConfigs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Kyc;
use App\Models\Account;
use App\Models\Fixed;
use App\Models\Work_place;
use App\Models\Nominee;
use App\Models\Images;
use App\Models\Applicant;
use App\Models\Cif_Response;
use App\Exceptions\Handler;
use App\Models\FD_rates;
use App\Models\investment_saving;
use Error;
use Illuminate\Support\Facades\DB;
use Exception;

class Application extends Controller
{



    public function fd_rates(Request $request)
    {
        Log::info('fd rate request');
        Log::info($request);

        $category = "";
        $payable = "";
        $months = "";
        $error = false;

        if (isset($request->category)) {
            $category = $request->category;
        } else {
            $category = "category missing";
            $error = true;
        }
        if (isset($request->payable)) {

            $payable = $request->payable;
        } else {
            $payable = "payable missing";
            $error = true;
        }
        if (isset($request->months)) {
            $months = $request->months;
        } else {
            $months = "months missing";
            $error = true;
        }

        if (!$error) {
            $rates = FD_rates::where("category", $category)->where("payable", $payable)->where("months", $months)->latest()->first();
            $rates =  json_encode($rates);
            Log::info('rates of fd no errors');
            Log::info($rates);
            echo  $rates;
        } else {
            $ar =  array(
                "category" => $category,
                "payable" => $payable,
                "months" => $months,
            );

            Log::info('errors ');

            $jsn =  json_encode($ar);
            Log::info($jsn);
            echo $jsn;
        }
        //  FD_rates::where
    }

    public function default_val($val)
    {

        if ($val === "" | $val === " " | $val === null | $val === "null") {
            $val = 0;
        }

        return $val;
    }



    public function new_investment(Request $request)
    {
        // investment_saving;
        Log::info('request to investment saving');
        Log::info($request);

        /**
         *   investment saving,
         */

        try {
            $nic = $request->input('nic');
            $ref = $request->input('ref');
            $period = $request->input('period');
            $desposit = $request->input('desposit');



            $investment = new investment_saving;
            $investment->ref = $ref;
            $investment->nic = $nic;
            $investment->period = $period;
            $investment->desposit = $desposit;
            $investment->save();
            return 1;
        } catch (Exception $e) {
            Log::error('erro of investment_saving ');
            Log::error($request);
            Log::error($e);
        }
    }


    public function new_fd(Request $request)
    {
        Log::info('request to new FD');
        Log::info($request);

        /**
         *   desposit,
         */

        try {
            $nic = $request->input('nic');
            $ref = $request->input('ref');
            $period = $request->input('period');
            $desposit = $request->input('desposit');
            $interest_payable_at = $request->input('interest_payable_at');
            $interest_disposal_method = $request->input('interest_disposal_method');
            $interest_transfer_bank = $request->input('interest_transfer_bank');

            $interest_transfer_account = $request->input('interest_transfer_account');
            $interest_transfer_branch = $request->input('interest_transfer_branch');
            $interest_transfer_acc_name = $request->input('interest_transfer_acc_name');
            $fd_rate = $request->input('fd_rate');

            if ($interest_transfer_account === NULL | $interest_transfer_account === "") {
                $interest_transfer_account = 0;
            }

            if ($fd_rate == "undefined") {
                $fd_rate = 0;
                Log::info('undefined rate for  new FD - fix');
                Log::info($fd_rate);
            }

            $fixed = new Fixed;
            $fixed->ref = $ref;
            $fixed->nic = $nic;
            $fixed->period = $period;
            $fixed->desposit = $desposit;
            $fixed->interest_payable_at = $interest_payable_at;
            $fixed->interest_disposal_method = $interest_disposal_method;
            $fixed->interest_transfer_bank = $interest_transfer_bank;
            $fixed->interest_transfer_account = $interest_transfer_account;
            $fixed->interest_transfer_branch = $interest_transfer_branch;
            $fixed->interest_transfer_acc_name = $interest_transfer_acc_name;
            $fixed->rate = $fd_rate;
            $fixed->save();
            return 1;
        } catch (Exception $e) {
            Log::error('erro of FD saving ');
            Log::error($request);
            Log::error($e);
        }
    }


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
            $address1 = $request->input('address1');
            $address2 = $request->input('address2');
            $address3 = $request->input('address3');
            $address4 = $request->input('address4');
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

            $bdo = $request->input('bdo');

            $sector = $request->input('sector');

            $pupose_other_reason = $request->input('pupose_other_reason');
            $wealth_other_reason = $request->input('source_funds_other_reason');
            $source_funds_other_reason = $request->input('wealth_other_reason');
            $gps = $request->input('gpsl');
            $appv = $request->input('appv');

            Log::info($gps);
            //data.append("existing_customer_cif", customer_cif);



            /// validate nums




            //$string = '265 9959 b9659';
            $salary_trimmed = 0;
            if ($salary === NULL | $salary === "null" | $salary === "NULL" | $salary === null | $salary === "" | $salary === " ") {
                $salary_trimmed = 0;
            } else {
                $s_con = ucfirst($salary);
                $salbar = ucwords(strtolower($s_con));
                $salary_trimmed = preg_replace('/\s+/', '',  $salbar);
            }



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
            $work->telephone = $this->default_val($telephone);
            $work->income_monthly = $salary_trimmed;
            $work->other_income = $other_income;
            $work->applicant_nic = $nic;
            $work->source_other_income = $source_of_other_income;
            $work->ref = $ref;
            $work->sector = $sector;

            $work->save();




            $applicant = new Applicant;

            $applicant->branch = $this->branch_bdo($bdo);
            $applicant->ref = $ref;
            $applicant->title = $title;
            $applicant->surname = $s_name;
            $applicant->initials = "";
            $applicant->display_name = $displayName;
            $applicant->full_name =  trim($full_name);
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
            $applicant->secondary_mobile_number = $this->default_val($secondary_mobile);
            $applicant->email = $email;
            $applicant->address1 = str_replace(',', '', $address1);
            $applicant->address2 = str_replace(',', '', $address2);
            $applicant->address3 = str_replace(',', '', $address3);
            $applicant->address4 = str_replace(',', '', $address4);
            $applicant->living_place_dif =  $living_place_dif;    // dif
            $applicant->district = $district;
            $applicant->same_nic_address = "";
            $applicant->security_question = $security_answer;
            $applicant->existing_customer = $existing_customer;  // bolean
            $applicant->bdo = $bdo;
            $applicant->gps = $gps;
            $applicant->appv = $appv;
            $applicant->save();


            $kyc_json = array(
                'pupose' => $purpose_usage,   // convert these json string to array, has to do with new
                'source_funds' => $source_of_funds,
                'anticipated_volume' => $anticipated_volumes,
                'source_wealth' => $source_of_wealth,
                'pep' => $pep,
                'pep_relationsip' => $pep_relationship
            );
            $kyc = new Kyc;
            $kyc->json = json_encode($kyc_json);
            $kyc->nic = $nic;
            $kyc->pep = $pep;
            $kyc->pep_relationship = $pep_relationship;
            $kyc->pupose_other_reason = $pupose_other_reason;
            $kyc->source_funds_other_reason = $source_funds_other_reason;
            $kyc->wealth_other_reason = $wealth_other_reason;
            $kyc->ref_number = $ref;
            $kyc->save();






            if ($existing_customer === "true") {
                $cif = new Cif_Response;
                $cif->ref_number = $ref;
                $cif->nic = $nic;
                $cif->cif = $customer_cif;
                $cif->save();
            }
        } catch (Exception $e) {
            Log::error($e);
        }

        echo "Application data been recorded !";
    }



    // get the brach of BDO

    public function branch_bdo($email)
    {

        //     $app = Branches::where("nic", $nic)->latest()->first();

        $bdo_branch = DB::table('users')
            ->where('email', $email)
            ->first();


        return  $bdo_branch->branch;
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

        if ($applicant_status === "fixed_deposits") {

            $statuses =  ApplicationConfigs::select('id', 'area', 'val', 'description')
                ->where('area', 'fixed_deposits')
                ->where('age_limit', '<=', $age)
                ->where('active',  1)
                ->get();
        }
        if ($applicant_status === "leasing") {

            $statuses =  ApplicationConfigs::select('id', 'area', 'val', 'description')
                ->where('area', 'Leasing')
                ->where('age_limit', '<=', $age)
                ->where('active',  1)
                ->get();
        }
        if ($applicant_status === "individual_account_type") {

            if ($sex == "Female") {
                //  ->where('applicant_sex', "Female")
                $statuses =  ApplicationConfigs::select('id', 'area', 'val', 'description')
                    ->where('area', 'individual_account_type')
                    ->where('age_limit', '<=', $age)
                    ->where('active',  1)
                    ->get();
            }

            if ($sex == "Male") {
                $statuses =  ApplicationConfigs::select('id', 'area', 'val', 'description')
                    ->where('area', 'individual_account_type')
                    ->where('age_limit', '<=', $age)
                    ->where('applicant_sex', "!=", "Female")
                    ->where('active',  1)
                    ->get();
            }
        }




        // $statuses =  ApplicationConfigs::select('id', 'area', 'val', 'description')->where('area', 'individual_account_type')->where('active',  1)->get();

        echo json_encode($statuses);
    }
}
