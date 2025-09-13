<?php

/**
 * User model for Interlink platform.
 *
 * - Uses 'role' field for authorization (student, organization, admin).
 * - Relationships: organizations, students, opportunities, etc.
 * - Uses UUIDs for primary key.
 * - Handles authentication and notifications.
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 * Represents a system user: student, admin, or organization contact.
 * Handles authentication and role management.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

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

    /**
     * Use UUIDs as primary key.
     */
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Organizations this user belongs to (as staff/admin).
     * Use organization_user pivot table.
     */
    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_user')->withPivot('role')->withTimestamps();
    }

    /**
     * Get the user's role from the users table.
     */
    public function getRoleAttribute($value)
    {
        return $value;
    }

    /**
     * Activities performed by the user.
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Notifications for the user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Messages sent by the user.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * The attributes that are mass assignable.
     * name: Full name of the user
     * email: Email address
     * password: Hashed password
     * role: User role (admin, organization, student)
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relationship: Each student user has one student record.
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Relationship: Each organization user has one organization profile.
     */
    public function organizationProfile()
    {
        return $this->hasOne(Organization::class);
    }
}
