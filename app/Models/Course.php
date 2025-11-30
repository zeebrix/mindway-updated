<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'course_title',
        'course_description',
        'course_duration',
        'course_thumbnail',
    ];
}
