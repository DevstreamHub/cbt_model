<?php

namespace App\Imports;

use App\Models\StudentIndex;
use Maatwebsite\Excel\Concerns\ToModel;

class StudentIndexImport implements ToModel
{
    public function model(array $row)
    {
        return new StudentIndex([
            'matric_no'    => $row[0],
            'index_number' => $row[1],
        ]);
    }
}
