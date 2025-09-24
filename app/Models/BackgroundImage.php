<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackgroundImage extends Model
{
    protected $fillable = ['name', 'file_path', 'is_active'];

    public static function active()
    {
        return self::where('is_active', true)->first();
    }
}
