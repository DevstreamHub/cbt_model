<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CbtApiSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',         // ✅ Required if you're manually setting ID in updateOrCreate
        'api_key',
        'base_url',
    ];

    public $timestamps = true;
}
