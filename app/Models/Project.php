<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Project Model
 * 
 * Represents projects or work assignments during internships and attachments.
 * Used to organize tasks, track progress, and manage deliverables for students.
 * 
 * Purpose:
 * - Group related tasks and activities for an internship
 * - Track overall project progress and completion
 * - Store project-specific information and deliverables
 * - Monitor earnings or compensation tied to projects
 * - Portfolio building for students
 * 
 * Features:
 * - Project status tracking (planning, in_progress, completed, etc.)
 * - Thumbnail/cover image support
 * - Earnings tracking for paid projects
 * - Task relationship for breaking down work
 * 
 * Relationships:
 * - Belongs to a Student (through student_id or freelancer_id)
 * - Has many Tasks
 * - May belong to an Organization or Opportunity
 * 
 * Use Cases:
 * - Interns working on specific company projects
 * - Tracking deliverables and milestones
 * - Building student portfolios
 * - Performance evaluation based on project completion
 * - Compensation calculation for project-based work
 * 
 * @package App\Models
 */
class Project extends Model
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
     * - freelancer_id: UUID of the student/intern (legacy field name, consider renaming to student_id)
     * - title: Project name or title (max 255 characters)
     * - description: Detailed project description, goals, and requirements
     * - thumbnail: Path to project cover image or thumbnail
     * - status: Current project status (planning, in_progress, completed, on_hold, cancelled)
     * - earnings: Monetary compensation for the project (nullable)
     * - start_date: Date when the project started
     * - end_date: Date when the project was completed or is expected to complete
     */
    protected $fillable = [
        'freelancer_id',
        'title',
        'description',
        'thumbnail',
        'status',
        'earnings',
        'start_date',
        'end_date',
    ];

    /**
     * The attributes that should be cast to native types.
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'earnings' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the student/freelancer who owns this project.
     * 
     * Note: This uses 'freelancer_id' for backward compatibility.
     * Consider migrating to 'student_id' for consistency.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function freelancer()
    {
        return $this->belongsTo(Student::class, 'freelancer_id');
    }

    /**
     * Alias for freelancer relationship with semantic naming.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->freelancer();
    }

    /**
     * Get all tasks associated with this project.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Calculate project completion percentage based on completed tasks.
     * 
     * @return float Completion percentage (0-100)
     */
    public function getCompletionPercentageAttribute()
    {
        $totalTasks = $this->tasks()->count();
        
        if ($totalTasks === 0) {
            return 0;
        }
        
        $completedTasks = $this->tasks()->where('completed', true)->count();
        
        return round(($completedTasks / $totalTasks) * 100, 2);
    }

    /**
     * Check if the project is overdue.
     * 
     * @return bool True if the project has passed its end date and is not completed
     */
    public function isOverdue()
    {
        return $this->end_date && 
               $this->end_date->isPast() && 
               !in_array($this->status, ['completed', 'cancelled']);
    }
}