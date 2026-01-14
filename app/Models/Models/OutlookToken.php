<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutlookToken extends Model
{
    use HasFactory;
    protected $fillable = [
        'counseller_id',
        'access_token',
        'refresh_token',
        'expires_in'
    ];
}
