<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonMedia extends Model
{
    use HasFactory;
    protected $fillable = [
        'lesson_id',
        'audio_url',
        'video_url',
        'article_text',
        'thumbnail',
        'host_name',
        'host_image',
        'author_name',
    ];

    public function lesson()
    {
        return $this->belongsTo(CourseLesson::class, 'lesson_id');
    }
}
