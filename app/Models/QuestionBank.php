<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'course_id',
        'question',
        'mark',
        'type',
        'option_type',
        'options',
        'answers',
        'time',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'answers' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the course that owns the question.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the exam that owns the question.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
