<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'address', 'phone'];

    // Relationship with user
    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
