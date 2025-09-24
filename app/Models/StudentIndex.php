<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentIndex extends Model
{
    use HasFactory;

    protected $table = 'student_indexes'; // 👈 FIX HERE

    protected $fillable = [
        'matric_no',
        'index_number',
    ];
}
