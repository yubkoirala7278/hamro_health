<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;
use App\Exceptions\PhoneNumberException;
use Illuminate\Support\Facades\Http;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected $fillable = [
        'slug',
        'name',
        'email',
        'password',
        'user_id',
        'phone_number',
        'otp_created_at',
        'otp_code',
        'otp_attempts',
        'phone_number_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'otp_created_at'           => 'datetime',
        'phone_number_verified_at' => 'datetime',
    ];

    // Relationship with school
    public function school()
    {
        return $this->hasOne(School::class, 'user_id');
    }

    // Relationship with student
    public function students()
    {
        return $this->hasMany(Student::class, 'user_id');
    }

    // Relationship with student
    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }


    // Relationship with user
    public function user()
    {
        return $this->belongsTo(User::class);
    }



    /**
     * Sends SMS for phone number verification to user.
     *
     * @return void
     *
     * @throws PhoneNumberException
     * @throws \JsonException
     */
    public function sendPhoneVerificationSms(): void
    {
        $url = config('sms.url');
        $data = [
            'token' => config('sms.token'),
            'from'  => config('sms.from'),
            'to'    => $this->phone_number,
            'text'  => "The OTP generated is {$this->otp_code}. The code will expire in 30 minutes.",
        ];

        $response = Http::post($url, $data);

        if (!$response->successful()) {
            $response = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);

            throw new PhoneNumberException($response['response']);
        }
    }


    // test
    public function createdUsers()
    {
        return $this->hasMany(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function schoolAdminConversations()
    {
        return $this->hasMany(Conversation::class, 'school_admin_id');
    }

    public function studentConversations()
    {
        return $this->hasMany(Conversation::class, 'student_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
}
