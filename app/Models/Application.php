<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Application
 * Represents a student's application to an opportunity (internship/attachment).
 * Stores application details, status, and relationships.
 */
class Application extends Model
{
    use HasFactory;
    /**
     * Use UUIDs as primary key.
     */
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     * opportunity_id: Foreign key to Opportunity
     * student_id: Foreign key to Student
     * cover_letter: Application letter
     * status: submitted/accepted/rejected
     * resume: Application resume
     * interests: Application interests
     * gpa: Application GPA
     */
    protected $fillable = [
        'opportunity_id',
        'student_id',
        'cover_letter',
        'status',
        'resume',
        'interests',
        'gpa',
        'match_message',
        'matched_skills',
        'missing_skills',
        'xai_course',
        'xai_academic_field',
        'xai_gpa',
        'xai_interests',
    ];

    /**
     * Relationship: Application belongs to an opportunity.
     */
    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    /**
     * Relationship: Application belongs to a student.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}