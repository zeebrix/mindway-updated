<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class CategoryCourse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'title',
        'description',
        'thumbnail',
    ];

    /**
     * Get the category that this course belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
