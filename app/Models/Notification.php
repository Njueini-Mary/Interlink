<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Notification
 * Represents a notification for a user or model.
 */
class Notification extends Model
{
    use HasFactory;
    /**
     * Use UUIDs as primary key.
     */
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'model_type',
        'model_id',
        'type',
        'title',
        'message',
        'read_at',
    ];

    protected $dates = [
        'read_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->morphTo();
    }

    /**
     * Get the notifiable entity that the notification belongs to.
     */
    public function notifiable()
    {
        return $this->morphTo();
    }
}
