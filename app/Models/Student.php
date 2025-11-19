<?php

namespace App\Models;

/**
 * Student model for Interlink platform.
 *
 * - Stores student profile, academic info, and relationships.
 * - Uses UUIDs for primary key.
 * - Relationships: skills, organizations, activities, notifications, applications.
 */

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Student
 * Represents a student profile linked to a user.
 * Stores student-specific data and relationships.
 */
class Student extends Model
{
    use HasFactory;

    /**
     * Many-to-Many relationship with Category (categories this student belongs to).
     */
    public function categories()
    {
        return $this->belongsToMany(\App\Models\Category::class, 'student_category')->withTimestamps();
    }
    use HasFactory;

    /**
     * Boot function to auto-generate UUID for id field on create.
     */
        protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
    use HasFactory;

    /**
     * Many-to-Many relationship with Skill (skills this student has).
     */
    public function skills()
    {
        return $this->belongsToMany(\App\Models\Skill::class, 'student_skill')->withTimestamps();
    }
    /**
     * Use UUIDs as primary key.
     */
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Applications submitted by this student.
     */
    public function applications()
    {
        return $this->hasMany(\App\Models\Application::class, 'student_id', 'id');
    }

    /**
     * Organizations this student (intern) belongs to.
     */
    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_student')->withTimestamps();
    }

    protected $table = 'students';

    /**
     * The attributes that are mass assignable.
     * user_id: Foreign key to User (student)
     * academic_field: Student's academic field/major
     * is_available: Whether student is available for placement
     */
    protected $fillable = [
        'user_id',
        'university',
        'course',
        'year',
        'resume',
        'academic_field',
        'gpa',
        'interests',
        'activities',
        'is_available',
        'rating',
        'rating_count',
        'bio',
        'profile_photo',
        'metadata',
    ];

    /**
     * Activities related to this student.
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'model');
    }

    /**
     * Notifications for this student.
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'model');
    }

    /**
     * Relationship: Student belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
