<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChildMedicalInfo extends Model
{
     use HasFactory;
    protected $table = 'child_medical_infos';
    protected $fillable = [
        'child_id', 'blood_group', 'allergies', 'current_vitamins', 'recent_illness',
        'last_updated_date', 'current_status', 'annual_checkup', 'immunizations'
    ];
}
