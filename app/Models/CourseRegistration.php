<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseRegistration extends Model
{
    protected $fillable = ['student_id', 'course_id', 'academic_session_id'];

    public function course() {
        return $this->belongsTo(Course::class);
    }


public function student()
{
    return $this->belongsTo(\App\Models\Student::class);
}

    public function session() {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

}
