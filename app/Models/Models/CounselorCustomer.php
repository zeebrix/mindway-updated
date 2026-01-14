<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounselorCustomer extends Model
{
    use HasFactory;
    protected $fillable = [
        'counselor_id',
        'customer_id'
    ];
}
