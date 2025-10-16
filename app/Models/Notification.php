<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Notification Model
 * 
 * Represents in-app notifications for users in the Interlink platform.
 * Provides real-time alerts and updates to keep users informed of important events.
 * 
 * Purpose:
 * - Alert users about application status changes
 * - Notify organizations about new applications
 * - Inform students about new opportunities matching their profile
 * - Send reminders for deadlines and upcoming events
 * - Provide system announcements and updates
 * 
 * Features:
 * - Read/unread tracking
 * - Polymorphic relationships to any model
 * - Categorized by notification type
 * - Timestamped for history tracking
 * 
 * Relationships:
 * - Belongs to a User (notification recipient)
 * - Morphs to any Model (the entity the notification is about)
 * 
 * Notification Types:
 * - application_submitted: When a student submits an application
 * - application_status_changed: When application status is updated
 * - new_opportunity: When a new matching opportunity is posted
 * - message_received: When a user receives a message
 * - deadline_reminder: Reminder for upcoming deadlines
 * - system_announcement: Administrative announcements
 * 
 * @package App\Models
 */
class Notification extends Model
{
    use HasFactory;
    
    /**
     * Use UUIDs as primary key for better scalability.
     */
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<string>
     * 
     * Fields:
     * - user_id: UUID of the user receiving the notification
     * - model_type: Type of the related model (e.g., 'App\\Models\\Application')
     * - model_id: UUID of the related model instance
     * - type: Notification category (e.g., 'application_submitted', 'new_opportunity')
     * - title: Notification headline (max 255 characters)
     * - message: Full notification message content
     * - read_at: Timestamp when notification was read (nullable, null = unread)
     */
    protected $fillable = [
        'user_id',
        'model_type',
        'model_id',
        'type',
        'title',
        'message',
        'read_at',
    ];

    /**
     * The attributes that should be cast to native types.
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Get the user who receives this notification.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that this notification is related to (polymorphic).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model()
    {
        return $this->morphTo();
    }

    /**
     * Get the notifiable entity that the notification belongs to.
     * 
     * Alias for the model() method for semantic clarity.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Check if the notification has been read.
     * 
     * @return bool True if the notification has been read, false otherwise
     */
    public function isRead()
    {
        return !is_null($this->read_at);
    }

    /**
     * Mark the notification as read.
     * 
     * Sets the read_at timestamp to the current time.
     * 
     * @return bool True if the update was successful
     */
    public function markAsRead()
    {
        if ($this->isRead()) {
            return true;
        }
        
        return $this->update(['read_at' => now()]);
    }

    /**
     * Mark the notification as unread.
     * 
     * Clears the read_at timestamp.
     * 
     * @return bool True if the update was successful
     */
    public function markAsUnread()
    {
        return $this->update(['read_at' => null]);
    }

    /**
     * Scope to get only unread notifications.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope to get only read notifications.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }
}
