<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['address', 'phone', 'created_by'];

    /**
     * The admin who created the school.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * All users (admins + students) assigned to this school.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'school_user', 'school_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * School admins only.
     */
    public function schoolAdmins()
    {
        return $this->belongsToMany(User::class, 'school_user', 'school_id', 'user_id')
                    ->whereHas('roles', fn ($query) => $query->where('name', 'school_admin'));
    }

    /**
     * Students only.
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'school_user', 'school_id', 'user_id')
                    ->whereHas('roles', fn ($query) => $query->where('name', 'student'));
    }

    /**
     * Medical reports for this school.
     */
    public function medicalReports()
    {
        return $this->hasMany(MedicalReport::class, 'school_id');
    }

    /**
     * Student profile records (optional if using profiles separately).
     */
    public function studentProfiles()
    {
        return $this->hasMany(StudentProfile::class, 'school_id');
    }

    /**
     * A readable name label using the first school admin.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->schoolAdmins()->first()?->name ?? 'Unknown';
    }
}
