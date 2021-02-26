<?php

namespace App\Exports;

use App\Models\Applicant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;

class applicationExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        //    return Applicant::all();


        return DB::table('applicant')
            ->join('cif_response', 'applicant.ref', '=', 'cif_response.ref_number')
            ->join('account', 'applicant.ref', '=', 'account.ref_number')
            ->get();
    }
}
