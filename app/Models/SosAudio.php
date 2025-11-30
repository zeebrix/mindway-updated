<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SosAudio extends Model
{
    use HasFactory;
    protected $table= 'sos_audios';
    protected $fillable = ['sos_audio', 'course_id', 'audio_title', 'duration', 'total_play'];

    /**
     * Get the course that this SOS audio belongs to.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
