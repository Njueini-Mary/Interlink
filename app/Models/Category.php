<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class Category extends Model
{

    use HasFactory;

    /**
     * Ensure Eloquent treats IDs as UUID strings.
     */
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

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