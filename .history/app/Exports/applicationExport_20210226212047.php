<?php

namespace App\Exports;

use App\Models\AllApplications;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;

class applicationExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return AllApplications::all();
    }
}
