<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'freelancer_id',
        'client_id',
        'content',
        'rating',
    ];

    /**
     * Relationship with Freelancer.
     */
    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class);
    }

    /**
     * Relationship with Client.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}