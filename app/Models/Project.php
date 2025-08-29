<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'freelancer_id',
        'title',
        'description',
        'thumbnail',
        'status',
        'earnings',
    ];

    /**
     * Relationship with Freelancer.
     */
    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class);
    }
}