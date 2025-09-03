<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Skill extends Model
{
    use HasFactory;

    /**
     * Auto-generate UUID for id if not set.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
    /**
     * Use UUIDs as primary key.
     */
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['name'];

    /**
     * Many-to-Many relationship with Student (students with this skill).
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_skill')->withTimestamps();
    }

    /**
     * Many-to-Many relationship with Opportunity (opportunities requiring this skill).
     */
    public function opportunities()
    {
        return $this->belongsToMany(Opportunity::class, 'opportunity_skill')->withTimestamps();
    }
}
