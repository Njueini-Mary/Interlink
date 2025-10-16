<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Task Model
 * 
 * Represents tasks or to-do items associated with projects.
 * Used for tracking work items, deliverables, or milestones during internships or projects.
 * 
 * Purpose:
 * - Break down projects into manageable tasks
 * - Track completion status of work items
 * - Set and monitor due dates
 * - Organize project deliverables
 * 
 * Relationships:
 * - Belongs to a Project
 * 
 * Use Cases:
 * - Interns tracking their daily/weekly tasks
 * - Organizations setting milestones for student projects
 * - Project management during internship period
 * - Performance evaluation based on task completion
 * 
 * @package App\Models
 */
class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<string>
     * 
     * Fields:
     * - project_id: UUID of the associated project
     * - title: Task name or summary (max 255 characters)
     * - description: Detailed task description or requirements
     * - completed: Boolean indicating if the task is completed
     * - due_date: Date when the task should be completed
     */
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'completed',
        'due_date',
    ];

    /**
     * The attributes that should be cast to native types.
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'completed' => 'boolean',
        'due_date' => 'date',
    ];

    /**
     * Get the project that this task belongs to.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Check if the task is overdue.
     * 
     * @return bool True if the task is not completed and past its due date
     */
    public function isOverdue()
    {
        return !$this->completed && $this->due_date && $this->due_date->isPast();
    }

    /**
     * Mark the task as completed.
     * 
     * @return bool True if the update was successful
     */
    public function markAsCompleted()
    {
        return $this->update(['completed' => true]);
    }
}