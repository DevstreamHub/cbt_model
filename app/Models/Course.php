<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'course_code', 'credit_unit', 'core_or_elective', 
        'department_id','semester_id', 'level_id',
    ];

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
    public function questions()
{
    return $this->hasMany(QuestionBank::class);
}

// In Course.php
public function exams()
{
    return $this->hasMany(Exam::class);
}

}
