<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActiveExamSession extends Model
{
    protected $fillable = ['student_id', 'exam_id', 'session_token'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
