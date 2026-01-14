<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseLesson extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'duration_minutes',
        'lesson_type',
        'order_no',
        'audio',
        'video',
        'article_text',
        'host_name',
        'author_name',
    ];
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function media()
    {
        return $this->hasOne(LessonMedia::class, 'lesson_id');
    }
    public function progress()
    {
        return $this->hasMany(LessonProgress::class, 'lesson_id');
    }
   
}
