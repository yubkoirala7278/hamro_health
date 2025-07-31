<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChildMedicalDocument extends Model
{
    use HasFactory;
    protected $table = 'child_medical_documents';
    protected $fillable = ['child_id', 'file_path', 'file_type', 'file_name'];
}
