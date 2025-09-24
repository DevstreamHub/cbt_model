<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $table = 'results';
    public $timestamps = false;

    protected $fillable = [
        'course_registration_id',
        'session_id',
        'department_id',
        'semester_id',
        'level_id',
        'ca_score',
        'exam_score',
        'total_score',
];


     public function courseRegistration()
    {
        return $this->belongsTo(CourseRegistration::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }
}