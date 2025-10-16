<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Message Model
 * 
 * Represents messages sent between users in the Interlink platform.
 * Facilitates communication between students, organizations, and admins.
 * 
 * Purpose:
 * - Enable direct messaging between students and organizations
 * - Allow organizations to reach out to potential candidates
 * - Provide students a way to inquire about opportunities
 * - Support administrative communication
 * 
 * Features:
 * - Polymorphic recipient support (can message students or organizations)
 * - Read/unread tracking via read_at timestamp
 * - Subject and body for structured communication
 * - Timestamped for message history
 * 
 * Relationships:
 * - Belongs to a User (sender)
 * - Morphs to Student or Organization (recipient)
 * 
 * Use Cases:
 * - Students asking questions about opportunities
 * - Organizations inviting students to apply
 * - Follow-up communications after applications
 * - Interview scheduling
 * - General inquiries
 * 
 * @package App\Models
 */
class Message extends Model
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
     * - sender_id: UUID of the user sending the message
     * - recipient_type: Type of recipient entity ('student' or 'organization')
     * - recipient_id: UUID of the recipient (student or organization profile)
     * - subject: Message subject line (max 255 characters)
     * - body: Full message content (text)
     * - read_at: Timestamp when the message was read (nullable, null = unread)
     */
    protected $fillable = [
        'sender_id',
        'recipient_type',
        'recipient_id',
        'subject',
        'body',
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
     * Get the user who sent this message.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the recipient of this message (polymorphic).
     * 
     * The recipient can be either a Student or Organization model,
     * determined by the recipient_type field.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function recipient()
    {
        return $this->morphTo();
    }

    /**
     * Check if the message has been read.
     * 
     * @return bool True if the message has been read, false otherwise
     */
    public function isRead()
    {
        return !is_null($this->read_at);
    }

    /**
     * Mark the message as read.
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
}
