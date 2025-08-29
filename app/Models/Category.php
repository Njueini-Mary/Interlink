<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Many-to-Many relationship with Student (students in this category).
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_category')->withTimestamps();
    }

    /**
     * One-to-Many relationship with Opportunity (opportunities in this category).
     */
    public function opportunities()
    {
        return $this->hasMany(Opportunity::class);
    }
}