<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * TimeLog Model
 * 
 * Represents time tracking logs for user activities and actions in the system.
 * This model is used for tracking time spent on various tasks, projects, or activities.
 * 
 * Purpose:
 * - Track time spent by students on internship tasks
 * - Monitor productivity and work hours
 * - Generate time reports for organizations
 * - Support billing and compensation calculations
 * - Provide analytics on time usage patterns
 * 
 * Features:
 * - Links to any model via polymorphic relationships
 * - Records action types (started, paused, stopped, etc.)
 * - Automatic timestamp tracking
 * - User attribution for accountability
 * 
 * Relationships:
 * - Belongs to a User (the person logging time)
 * - Morphs to any Model (the entity being worked on)
 * 
 * Use Cases:
 * - Interns logging daily work hours
 * - Tracking time spent on specific projects
 * - Generating timesheets for payment processing
 * - Monitoring student engagement and productivity
 * - Compliance with work hour regulations
 * 
 * @package App\Models
 */
class TimeLog extends Model
{
    use HasFactory;
    
    /**
     * Use UUIDs as primary key for better scalability and privacy.
     */
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<string>
     * 
     * Fields:
     * - user_id: UUID of the user logging the time
     * - action: Type of action (e.g., 'started', 'paused', 'stopped', 'resumed')
     * - model_type: Type of the related model (e.g., 'App\\Models\\Task', 'App\\Models\\Project')
     * - model_id: UUID of the related model instance
     * - duration: Time duration in minutes (nullable)
     * - notes: Optional notes about the time entry
     * - created_at: Timestamp when the log entry was created
     * - updated_at: Timestamp when the log entry was last updated
     */
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'duration',
        'notes',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'duration' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created this time log.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that this time log is related to (polymorphic).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model()
    {
        return $this->morphTo();
    }

    /**
     * Format duration in human-readable format (hours and minutes).
     * 
     * @return string Formatted duration (e.g., "2h 30m")
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) {
            return '0m';
        }
        
        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;
        
        $formatted = '';
        if ($hours > 0) {
            $formatted .= $hours . 'h ';
        }
        if ($minutes > 0) {
            $formatted .= $minutes . 'm';
        }
        
        return trim($formatted);
    }
}
