<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CounsellorDetail extends Model
{
    protected $fillable = [
        'user_id',
        'gender',
        'description',
        'intake_link',
        'timezone',
        'avatar',
        'specialization',
        'language',
        'location',
        'communication_method',
        'introduction_video',
        'notice_period',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
