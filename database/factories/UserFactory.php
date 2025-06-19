<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'slug' => $this->faker->slug,
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'), // or use Hash::make()
            // add other default attributes here...
        ];
    }

    public function student()
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('student');
        });
    }

    public function schoolAdmin()
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('school_admin');
        });
    }
}
