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
     * @var array
     */
    protected $fillable = [
        'student_id',
        'created_by',
        'report_date',
        'medical_condition',
        'allergies',
        'medications',
        'vaccinations',
        'notes',
        'doctor_name',
        'doctor_contact',
        'specialist',
        'mnc_number',
        'blood_pressure',
        'pulse_rate',
        'temperature',
        'respiratory_rate',
        'oxygen_saturation',
        'report_file',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'report_date' => 'date',
    ];

    /**
     * Get the student that owns the medical report.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the user who created the medical report.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getReportFileUrlAttribute()
    {
        return $this->report_file ? Storage::url($this->report_file) : null;
    }
}
