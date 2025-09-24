<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'surname', 'other_names', 'email', 'dob', 'phone', 'gender',
        'nationality', 'state_of_origin', 'lga', 'religion', 'address',
        'olevel_school', 'olevel_year', 'primary_school', 'primary_year',
        'other_institution', 'other_year', 'school_id', 'matric_number', 'department_id', 'program', 'user_id'
    ];
    public function department()
{
    return $this->belongsTo(Department::class, 'department_id', 'id');
}

}
