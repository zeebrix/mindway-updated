<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseProgress extends Model
{
    use HasFactory;
     protected $fillable = [
        'user_id',
        'course_id',
        'last_lesson_id',
        'progress_percent',
        'completed_at',
    ];
}
