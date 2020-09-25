<?php

namespace App\Imports;

use App\Schedule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class VirtualVisitImport implements ToModel, WithHeadingRow
{
    private $rows = 0;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        ++$this->rows;

        return new Schedule([
            'user_id' => $row['user_id'],
            'type' => $row['type'],
            'code' => $row['code'],
            'name' => $row['name'],
            'address' => $row['address'],
        ]);
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }
}
