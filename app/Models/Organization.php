<?php
/**
 * Organization model for Interlink platform.
 *
 * - Stores organization profile, contact, and opportunity listings.
 * - Uses UUIDs for primary key.
 * - Relationships: users (staff/admins), students (interns), activities, notifications, opportunities, applications.
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Organization
 * Represents an organization or company offering internship/attachment opportunities.
 * Stores organization profile and manages opportunity listings.
 */
class Organization extends Model
{
    use HasFactory;

    /**
     * Boot function to auto-generate UUID for id field on create.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    /**
     * Ensure Eloquent treats IDs as UUID strings.
     */
    protected $keyType = 'string';
    public $incrementing = false;
    protected $casts = [
        'id' => 'string',
    ];


    /**
     * Staff and admins (users) belonging to this organization.
     * Use organization_user pivot table.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'organization_user')->withPivot('role')->withTimestamps();
    }

    /**
     * Students (interns) belonging to this organization.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'organization_student')->withTimestamps();
    }

    /**
     * Activities related to this organization.
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'model');
    }

    /**
     * Notifications for this organization.
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'model');
    }

    protected $table = 'organizations';

    /**
     * The attributes that are mass assignable.
     * user_id: Foreign key to User (organization contact)
     * name: Organization name
     * profile: Organization profile/description
     * logo: Path to logo image
     * location: Physical or regional location
     */
    protected $fillable = [
        'name',
        'profile',
        'logo',
        'location',
        'industry',
        'website',
        'contact_email',
        'contact_phone',
        'rating',
        'rating_count',
        'metadata',
    ];

    /**
     * Messages received by the organization.
     */
    public function receivedMessages()
    {
        return $this->morphMany(Message::class, 'recipient');
    }

    /**
     * Relationship: Organization belongs to a user (contact person).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Organization has many opportunities (internships/attachments).
     */
    public function opportunities()
    {
        return $this->hasMany(Opportunity::class, 'organization_id', 'id');
    }

    /**
     * Relationship: Applications through opportunities.
     */
    public function applications()
    {
        return $this->hasManyThrough(Application::class, Opportunity::class);
    }
}
