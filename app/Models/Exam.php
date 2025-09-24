<?php

namespace App\Models;

use App\Models\QuestionBank; // ✅ Add this import
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'type', 'duration', 'is_active', 'question_limit',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    // Exam.php
public function questionBanks()
{
    return $this->hasMany(QuestionBank::class);
}

public function questions()
{
    return $this->hasMany(QuestionBank::class);
}

protected static function booted()
{
    static::deleting(function ($exam) {
        $exam->questions()->delete();
    });
}
public function examProgress()
{
    return $this->hasMany(ExamProgress::class, 'exam_id');
}
// App\Models\Exam.php
public function activeSessions()
{
    return $this->hasMany(ActiveExamSession::class);
}


}
