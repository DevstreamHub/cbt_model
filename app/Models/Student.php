<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'matric_no',
        'surname',
        'other_names',
        'email',
        'department_id',
        'level_id',
        'user_id',
        'passport',
        
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function courseRegistrations()
{
    return $this->hasMany(CourseRegistration::class);
}
public function transactions()
{
    return $this->hasMany(Transaction::class);
}

}
