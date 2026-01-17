<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SleepAudio extends Model
{
    use HasFactory, SoftDeletes; 
    protected $table = 'sleep_audios';
    protected $fillable = [
        'audio',
        'course_id',
        'duration',
        'title',
        'image',
        'color',
        'description',
        'total_play'
    ];

    /**
     * Get the course that this sleep audio belongs to.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
