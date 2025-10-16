<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Review Model
 * 
 * Represents reviews and ratings given by students for organizations or opportunities.
 * This model stores feedback and ratings to help future students make informed decisions.
 * 
 * Relationships:
 * - Belongs to a User (reviewer/student)
 * - Belongs to an Organization (if reviewing an organization)
 * - Belongs to an Opportunity (if reviewing an internship/attachment)
 * 
 * Features:
 * - Star ratings (1-5 scale)
 * - Written review text
 * - Timestamp for review submission
 * - Helpful voting system for reviews
 * 
 * @package App\Models
 */
class Review extends Model
{
    use HasFactory;
    /**
     * Use UUIDs as primary key.
     */
    protected $keyType = 'string';
    public $incrementing = false;

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