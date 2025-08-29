<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Message
 * Represents a message sent between users (students/organizations).
 */
class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'recipient_type', // 'student' or 'organization'
        'recipient_id',
        'subject',
        'body',
        'read_at',
    ];

    /**
     * Sender relationship (User).
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Polymorphic recipient relationship (Student or Organization).
     */
    public function recipient()
    {
        return $this->morphTo();
    }
}
