<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'surname',
        'other_names',
        'email',
        'dob',
        'phone',
        'gender',
        'nationality',
        'state_of_origin',
        'lga',
        'religion',
        'address',
        'olevel_school',
        'olevel_year',
        'primary_school',
        'primary_year',
        'other_institution',
        'other_year',
        
        'admission_status',
        'department_id',
        'level_id',
        'program',
        'user_id',
        'passport',
        'result_scans',
        'subjects',
    ];

    protected $casts = [
        'result_scans' => 'array',
        'subjects' => 'array',
        'dob' => 'date',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'id');
    }
public function getDecodedSubjectsAttribute()
    {
        // Decode the 'subjects' JSON field to array or return empty array if null
        return $this->subjects ? json_decode($this->subjects, true) : [];
    }
    // In App\Models\Admission.php
public function academicSession()
{
    return $this->belongsTo(AcademicSession::class, 'academic_session_id');
}


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
