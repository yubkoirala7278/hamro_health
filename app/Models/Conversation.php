<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['school_admin_id', 'student_id', 'last_message_at'];

     protected $casts = [
        'last_message_at' => 'datetime'
    ];

    public function schoolAdmin()
    {
        return $this->belongsTo(User::class, 'school_admin_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
