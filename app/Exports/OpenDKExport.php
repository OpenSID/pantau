<?php

namespace App\Exports;

use App\Models\OpenDK;
use Maatwebsite\Excel\Concerns\FromCollection;

class OpenDKExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return OpenDK::all();
    }
}
