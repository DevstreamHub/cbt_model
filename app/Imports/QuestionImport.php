<?php

namespace App\Imports;

use App\Models\QuestionBank;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new QuestionBank([
            'exam_id'     => $row['exam_id'],
            'course_id'   => $row['course_id'],
            'question'    => $row['question'],
            'mark'        => $row['mark'],
            'type'        => $row['type'],
            'option_type' => $row['option_type'],
            'options'     => json_encode([
                'a' => $row['option_a'],
                'b' => $row['option_b'],
                'c' => $row['option_c'],
                'd' => $row['option_d'],
            ]),
            'answers'     => explode(',', $row['answers']), // multiple answers supported
            'time'        => $row['time'],
            'is_active'   => $row['is_active'],
        ]);
    }
}
