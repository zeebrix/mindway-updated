<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseAudio extends Model
{
    protected $fillable = ['audio', 'course_id', 'audio_title', 'duration', 'total_play', 'course_order_by'];
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
