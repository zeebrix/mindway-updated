<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramDepartment extends Model
{
     protected $fillable = [
        'name',
        'status',
        'program_detail_id',
        'user_id',
    ];
}
