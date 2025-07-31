<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChildrenInfo extends Model
{
    use HasFactory;
    protected $fillable=['user_id','school_id','full_name','dob','emergency_contact_number'];
}
