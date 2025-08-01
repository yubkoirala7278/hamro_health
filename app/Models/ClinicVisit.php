<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicVisit extends Model
{
    use HasFactory;
    protected $table = 'clinic_visits';
    protected $fillable = ['child_id', 'visit_date', 'reason', 'notes'];
}
