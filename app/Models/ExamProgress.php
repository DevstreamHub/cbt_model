<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamProgress extends Model
{
    protected $table = 'exam_progress';

    protected $fillable = [
        'student_id',
        'exam_id',
        'question_id',
        'selected_option',
    ];

// Optional: Accessors to auto-convert
public function getSelectedOptionAttribute($value)
{
    return json_decode($value, true);
}

public function setSelectedOptionAttribute($value)
{
    $this->attributes['selected_option'] = json_encode($value);
}

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function question()
    {
        return $this->belongsTo(QuestionBank::class, 'question_id');
    }
}
