<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'thumbnail',
        'duration_minutes',
        'course_type',
        'theme_color',
    ];

    public function lessons()
    {
        return $this->hasMany(CourseLesson::class)->orderBy('order_no');
    }

    public function progress()
    {
        return $this->hasMany(CourseProgress::class);
    }
    public function ssoAudio()
    {
        return $this->hasMany(SosAudio::class);
    }
}
