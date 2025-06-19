<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles, SoftDeletes;

    protected $fillable = [
        'slug',
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The schools this user is assigned to (as school admin or student).
     */
    public function schools()
    {
        return $this->belongsToMany(School::class, 'school_user')
            ->withTimestamps();
    }

    /**
     * Student profile (only for student users).
     */
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class, 'user_id');
    }


    /**
     * Medical reports belonging to this student.
     */
    public function medicalReports()
    {
        return $this->hasMany(MedicalReport::class, 'student_id');
    }

    /**
     * Medical reports uploaded by this user (school admin or student).
     */
    public function uploadedMedicalReports()
    {
        return $this->hasMany(MedicalReport::class, 'uploaded_by');
    }

    /**
     * Schools created by the user (only for admins).
     */
    public function createdSchools()
    {
        return $this->hasMany(School::class, 'created_by');
    }
}
