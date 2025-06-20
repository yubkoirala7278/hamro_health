<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'phone', 'dob', 'gender', 'address', 'parent_phone', 'emergency_contact', 'grade_level'];

    protected $casts = [
        'dob' => 'date' // Casts dob to a Carbon instance
    ];

    // Relationship with user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
