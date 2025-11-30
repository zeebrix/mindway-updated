<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramPlan extends Model
{
    protected $fillable = [
        'user_id',
        'program_detail_id',
        'type',
        'annual_fee',
        'session_cost',
        'renewal_date',
        'gst_registered',
    ];
}
