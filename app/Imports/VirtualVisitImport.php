<?php

namespace App\Imports;

use App\Schedule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VirtualVisitImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Schedule([
            'user_id' => $row['user_id'],
            'type' => $row['type'],
            'code' => $row['code'],
            'name' => $row['name'],
            'address' => $row['address'],
            'date' => $row['date'],
        ]);
    }
}
