<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Activity Model
 * 
 * Represents system and user activities for comprehensive audit logging and tracking.
 * This model captures all significant actions performed in the system for security,
 * debugging, and analytics purposes.
 * 
 * Purpose:
 * - Audit trail for all user actions
 * - System event logging
 * - User behavior analytics
 * - Security monitoring and compliance
 * 
 * Relationships:
 * - Belongs to a User (the actor who performed the activity)
 * - Morphs to any Model (the target entity affected by the activity)
 * 
 * Use Cases:
 * - Track when users create, update, or delete records
 * - Monitor login/logout activities
 * - Record application submissions
 * - Log opportunity publications
 * - Track profile updates
 * 
 * @package App\Models
 */
class Activity extends Model
{
    use HasFactory;
    
    /**
     * Use UUIDs as primary key for better scalability and security.
     */
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<string>
     * 
     * Fields:
     * - user_id: ID of the user who performed the activity (nullable for system activities)
     * - model_type: Type of the target model (e.g., 'App\\Models\\Opportunity')
     * - model_id: ID of the target model instance
     * - action: The action performed (e.g., 'created', 'updated', 'deleted', 'viewed')
     * - description: Human-readable description of the activity
     * - data: JSON field containing additional context (metadata, old/new values, etc.)
     */
    protected $fillable = [
        'user_id',
        'model_type',
        'model_id',
        'action',
        'description',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Get the user who performed this activity.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that this activity is related to (polymorphic).
     * 
     * This allows activities to be linked to any model in the system
     * (Opportunity, Student, Organization, Application, etc.)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model()
    {
        return $this->morphTo();
    }
}
