<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class StudentIndexTemplateExport implements FromArray
{
    public function array(): array
    {
        return [
            ['MatricNo1', 'Indexno1'], // Sample row
            ['MatricNo2', 'Indexno2'],
        ];
    }
}
