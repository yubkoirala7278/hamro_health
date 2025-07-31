<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChildMedicine extends Model
{
     use HasFactory;
    protected $table = 'child_medicines';
    protected $fillable = [
        'child_id', 'medicine_name', 'dosage', 'frequency', 'duration', 'next_dose_due', 'status'
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(ChildrenInfo::class, 'child_id', 'id');
    }
}
