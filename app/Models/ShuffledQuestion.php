<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShuffledQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_id',
        'question_order',
    ];

    protected $casts = [
        'question_order' => 'array', // auto-convert JSON to PHP array
    ];
}
