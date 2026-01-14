<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class programDetail extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'max_lic',
        'logo',
        'link',
        'code',
        'max_sessions',
        'program_type',
        'trial_expire',
    ];
    public function programDepartments()
    {
        return $this->hasMany(ProgramDepartment::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
