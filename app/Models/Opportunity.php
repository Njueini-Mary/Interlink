<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class Opportunity
 * Represents an internship, attachment, or volunteering opportunity posted by an organization.
 * Stores details, requirements, and relationships for placement matching.
 */
class Opportunity extends Model
{
    use HasFactory;

    /**
     * Use UUIDs as primary key.
     */
    protected $keyType = 'string';
    public $incrementing = false;
    use HasFactory;

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
     * Applications submitted for this opportunity.
     */
    public function applications()
    {
        return $this->hasMany(\App\Models\Application::class, 'opportunity_id', 'id');
    }

    /**
     * Activities related to this opportunity.
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'model');
    }

    /**
     * Notifications for this opportunity.
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'model');
    }

    /**
     * The attributes that are mass assignable.
     * organization_id: Foreign key to Organization
     * category_id: Academic or professional category
     * title: Opportunity title
     * description: Details about the opportunity
     * location: Where the opportunity is based
     * type: WBL or SBL
     * deadline: Application deadline
     * status: open/closed/filled
     */
    protected $fillable = [
        'organization_id',
        'category_id',
        'title',
        'description',
        'industry',
        'min_gpa',
        'course',
        'academic_field',
        'year',
        'university',
        'organizations',
        'interests',
        'activities',
        'location',
        'type',
        'deadline',
        'status',
        'monthly_stipend',
        'min_hourly_rate',
        'max_hourly_rate',
        'compensation_type',
        'currency',
        'user_id', // Added user_id to track the creator
    ];

    // Ensure deadline is cast to a date instance.
    protected $casts = [
        'deadline' => 'date',
    ];

    /**
     * Relationship: Opportunity belongs to an organization.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Relationship: Opportunity belongs to a category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship: Opportunity requires certain skills (many-to-many).
     */
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'opportunity_skill')->withTimestamps();
    }

    /**
     * Relationship: Opportunity belongs to a user (creator).
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who posted the opportunity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter open opportunities.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function getStatusBadgeAttribute()
    {
        $statuses = [
            'open' => 'bg-blue-100 text-blue-800',
            'in_progress' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            'closed' => 'bg-gray-100 text-gray-800'
        ];
    
        return $statuses[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

}
