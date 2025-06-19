<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class MedicalReport extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'school_id',
        'uploaded_by',
        'title',
        'description',
        'file_path',
        'checkup_date',
        'report_type',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'checkup_date' => 'date',
    ];

    /**
     * Get the student associated with the medical report.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the school associated with the medical report.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * Get the user who uploaded the medical report (school_admin).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the full URL for the file path.
     *
     * @return string|null
     */
    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    /**
     * Scope to filter medical reports by school admin's school.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\Models\User $schoolAdmin
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForSchoolAdmin($query, User $schoolAdmin)
    {
        return $query->whereHas('school', function ($q) use ($schoolAdmin) {
            $q->whereHas('schoolAdmins', function ($q) use ($schoolAdmin) {
                $q->where('users.id', $schoolAdmin->id);
            });
        });
    }
}